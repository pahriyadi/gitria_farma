<?php

namespace App\Services;

class JournalEngine
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function postJournal($transactionType, $referenceId, $amount, $description, $paymentMethod = null, $sourceModule = 'Keuangan')
    {
        $amount = (float) $amount;
        if ($amount <= 0) {
            return [
                'status'  => 'success',
                'message' => 'Nilai transaksi 0, tidak perlu penjurnalan.'
            ];
        }

        // 1. Fetch account mapping
        $mapping = $this->db->table('transaction_account_mappings')
                            ->where('transaction_type', $transactionType)
                            ->get()
                            ->getRow();

        if (!$mapping) {
            $fallbacks = [
                'CLINIC_PAYMENT' => (object)['debit_account_id' => 1, 'credit_account_id' => 9],
                'PHARMACY_SALE'  => (object)['debit_account_id' => 1, 'credit_account_id' => 10],
                'RESTO_SALE'     => (object)['debit_account_id' => 1, 'credit_account_id' => 11],
                'FEE_EXPENSE'    => (object)['debit_account_id' => 14, 'credit_account_id' => 7]
            ];
            $mapping = $fallbacks[$transactionType] ?? null;
        }

        if (!$mapping) {
            return [
                'status'  => 'error',
                'message' => 'Mapping akun untuk tipe transaksi ' . $transactionType . ' belum diatur.'
            ];
        }

        $debitAccountId = $mapping->debit_account_id;
        $creditAccountId = $mapping->credit_account_id;

        // Dynamic Debit Account selection based on payment method
        if (!empty($paymentMethod) && in_array($transactionType, ['CLINIC_PAYMENT', 'PHARMACY_SALE', 'RESTO_SALE'])) {
            $pMethod = strtolower($paymentMethod);
            if ($pMethod === 'qris') {
                $qrisAcc = $this->db->table('accounts')->where('code', '1-103')->get()->getRow();
                if ($qrisAcc) $debitAccountId = $qrisAcc->id;
            } elseif (in_array($pMethod, ['transfer', 'debit', 'credit', 'edc'])) {
                $bankAcc = $this->db->table('accounts')->where('code', '1-102')->get()->getRow();
                if ($bankAcc) $debitAccountId = $bankAcc->id;
            } else {
                $cashAcc = $this->db->table('accounts')->where('code', '1-101')->get()->getRow();
                if ($cashAcc) $debitAccountId = $cashAcc->id;
            }
        }

        // 2. Generate Journal Number (JV-YYYYMMDD-XXXX)
        $today = date('Ymd');
        $maxRow = $this->db->query("SELECT MAX(CAST(SUBSTRING_INDEX(journal_no, '-', -1) AS UNSIGNED)) as max_seq FROM journal_entries WHERE journal_no LIKE 'JV-{$today}-%'")->getRow();
        $nextNum = ($maxRow && $maxRow->max_seq) ? ((int)$maxRow->max_seq + 1) : 1;
        $journalNo = 'JV-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // 3. Create Journal Entry Header
        $this->db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => $sourceModule,
            'reference_id'  => $referenceId,
            'description'   => $description,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $this->db->insertID();

        if (!$journalId || $journalId <= 0) {
            // Fallback retrieval of inserted ID
            $insertedEntry = $this->db->table('journal_entries')->where('journal_no', $journalNo)->get()->getRow();
            $journalId = $insertedEntry ? (int)$insertedEntry->id : null;
        }

        if (!$journalId || $journalId <= 0) {
            return [
                'status'  => 'error',
                'message' => 'Gagal mendapatkan ID entri jurnal keuangan.'
            ];
        }

        // 4. Insert Journal Entry Detail - DEBIT
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $debitAccountId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);

        // 5. Insert Journal Entry Detail - CREDIT
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $creditAccountId,
            'debit'      => 0.00,
            'credit'     => $amount
        ]);

        // 6. Update Accounts balance
        $debitAcc = $this->db->table('accounts')->where('id', $debitAccountId)->get()->getRow();
        if ($debitAcc) {
            $newBal = ($debitAcc->normal_balance === 'debit') ? ($debitAcc->balance + $amount) : ($debitAcc->balance - $amount);
            $this->db->table('accounts')->where('id', $debitAccountId)->update(['balance' => $newBal]);
        }

        $creditAcc = $this->db->table('accounts')->where('id', $creditAccountId)->get()->getRow();
        if ($creditAcc) {
            $newBal = ($creditAcc->normal_balance === 'credit') ? ($creditAcc->balance + $amount) : ($creditAcc->balance - $amount);
            $this->db->table('accounts')->where('id', $creditAccountId)->update(['balance' => $newBal]);
        }

        return [
            'status'     => 'success',
            'journal_no' => $journalNo,
            'message'    => 'Jurnal transaksi berhasil dibukukan.'
        ];
    }

    /**
     * Sinkronisasi transaksi terbayar yang belum terjurnal (Resto POS, Apotek OTC, Kasir Klinik)
     */
    public function syncUnpostedTransactions()
    {
        // 1. Sync Paid Resto Orders
        $paidRestoOrders = $this->db->table('restaurant_orders')
                                    ->where('payment_status', 'paid')
                                    ->get()
                                    ->getResult();

        foreach ($paidRestoOrders as $ro) {
            $exists = $this->db->table('journal_entries')
                               ->where('source_module', 'Resto POS')
                               ->where('reference_id', $ro->id)
                               ->countAllResults();
            if ($exists === 0 && floatval($ro->grand_total) > 0) {
                $details = $this->db->table('restaurant_order_details rod')
                                    ->select('rod.qty, rod.price, rm.name')
                                    ->join('restaurant_menus rm', 'rm.id = rod.menu_id', 'left')
                                    ->where('rod.order_id', $ro->id)
                                    ->get()
                                    ->getResult();
                $itemStrs = [];
                foreach ($details as $d) {
                    $itemStrs[] = ($d->name ?: 'Item') . ' (' . (int)$d->qty . 'x @Rp ' . number_format($d->price, 0, ',', '.') . ')';
                }
                $itemSummary = !empty($itemStrs) ? implode(', ', $itemStrs) : 'Menu Resto';

                $this->postJournal(
                    'RESTO_SALE',
                    $ro->id,
                    $ro->grand_total,
                    'Pendapatan POS Resto & Nutrisi - ' . $ro->order_no . ' (' . $ro->customer_name . '): ' . $itemSummary,
                    $ro->payment_method,
                    'Resto POS'
                );
            }
        }

        // 2. Sync OTC Pharmacy Sales (Direct Sales)
        $otcSales = $this->db->table('pharmacy_sales')
                             ->where('grand_total >', 0)
                             ->get()
                             ->getResult();

        foreach ($otcSales as $os) {
            $exists = $this->db->table('journal_entries')
                               ->where('source_module', 'Farmasi Apotek')
                               ->where('reference_id', $os->id)
                               ->countAllResults();
            if ($exists === 0 && floatval($os->grand_total) > 0) {
                $details = $this->db->table('pharmacy_sale_details psd')
                                    ->select('psd.qty, psd.price, m.name')
                                    ->join('medicines m', 'm.id = psd.medicine_id', 'left')
                                    ->where('psd.sale_id', $os->id)
                                    ->get()
                                    ->getResult();
                $itemStrs = [];
                foreach ($details as $d) {
                    $itemStrs[] = ($d->name ?: 'Obat') . ' (' . (int)$d->qty . 'x @Rp ' . number_format($d->price, 0, ',', '.') . ')';
                }
                $itemSummary = !empty($itemStrs) ? implode(', ', $itemStrs) : 'Obat Bebas';

                $this->postJournal(
                    'PHARMACY_SALE',
                    $os->id,
                    $os->grand_total,
                    'Pendapatan Penjualan Obat Bebas Apotek - ' . $os->sale_no . ' (' . $os->customer_name . '): ' . $itemSummary,
                    $os->payment_method,
                    'Farmasi Apotek'
                );
            }
        }
    }
}
