<?php

namespace App\Services;

class VoidReversalEngine
{
    protected $db;
    protected $journalEngine;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');
        $this->journalEngine = new \App\Services\JournalEngine();
    }

    /**
     * Verifikasi PIN Supervisor dengan proteksi brute-force lockout
     */
    public function verifySupervisorPin($supervisorUserId, $pin)
    {
        $supervisorUserId = (int)$supervisorUserId;
        $pin = trim((string)$pin);

        if ($supervisorUserId <= 0 || empty($pin)) {
            return ['valid' => false, 'message' => 'Supervisor dan PIN otorisasi wajib diisi.'];
        }

        // Cek user
        $user = $this->db->table('users')
                         ->select('users.*, roles.name as role_name')
                         ->join('roles', 'roles.id = users.role_id')
                         ->where('users.id', $supervisorUserId)
                         ->where('users.status', 'active')
                         ->get()
                         ->getRow();

        if (!$user) {
            return ['valid' => false, 'message' => 'Akun supervisor tidak ditemukan atau tidak aktif.'];
        }

        // Cek izin (Role Admin/IT/Direksi/Kepala Klinik/Koordinator Keuangan atau custom permission)
        $allowedRoles = ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan', 'Manajer', 'Accounting'];
        $hasRole = in_array($user->role_name, $allowedRoles);

        if (!$hasRole) {
            $hasPerm = $this->db->table('role_permissions')
                                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                                ->where('role_permissions.role_id', $user->role_id)
                                ->where('permissions.name', 'void.authorize')
                                ->countAllResults();
            if ($hasPerm === 0) {
                return ['valid' => false, 'message' => "Pengguna {$user->username} ({$user->role_name}) tidak memiliki wewenang mengotorisasi pembatalan transaksi."];
            }
        }

        // Ambil data PIN
        $pinRow = $this->db->table('supervisor_pins')->where('user_id', $supervisorUserId)->get()->getRow();

        if (!$pinRow) {
            // Auto-create default PIN (123456) jika belum diatur
            $defaultHash = password_hash('123456', PASSWORD_BCRYPT);
            $this->db->table('supervisor_pins')->insert([
                'user_id'         => $supervisorUserId,
                'pin_hash'        => $defaultHash,
                'failed_attempts' => 0,
                'is_locked'       => 0
            ]);
            $pinRow = $this->db->table('supervisor_pins')->where('user_id', $supervisorUserId)->get()->getRow();
        }

        // Cek lockout
        if ($pinRow->is_locked) {
            if ($pinRow->locked_until && strtotime($pinRow->locked_until) > time()) {
                $remainMin = ceil((strtotime($pinRow->locked_until) - time()) / 60);
                return ['valid' => false, 'message' => "Akun supervisor ini terkunci karena salah PIN berulang kali. Silakan coba lagi dalam {$remainMin} menit."];
            } else {
                // Unlock expired
                $this->db->table('supervisor_pins')->where('id', $pinRow->id)->update([
                    'is_locked'       => 0,
                    'failed_attempts' => 0,
                    'locked_until'    => null
                ]);
            }
        }

        // Validasi PIN (Support password_verify or fallback default)
        $isValid = password_verify($pin, $pinRow->pin_hash) || ($pin === '123456' && password_verify('123456', $pinRow->pin_hash));

        if (!$isValid) {
            $failed = (int)$pinRow->failed_attempts + 1;
            $update = ['failed_attempts' => $failed];

            if ($failed >= 5) {
                $update['is_locked'] = 1;
                $update['locked_until'] = date('Y-m-d H:i:s', time() + 900); // 15 menit
                $this->db->table('supervisor_pins')->where('id', $pinRow->id)->update($update);
                return ['valid' => false, 'message' => 'PIN salah 5 kali! Akun otorisasi dikunci selama 15 menit demi keamanan.'];
            }

            $this->db->table('supervisor_pins')->where('id', $pinRow->id)->update($update);
            $sisa = 5 - $failed;
            return ['valid' => false, 'message' => "PIN Supervisor salah! Sisa percobaan: {$sisa} kali."];
        }

        // Reset failed counter
        $this->db->table('supervisor_pins')->where('id', $pinRow->id)->update([
            'failed_attempts' => 0,
            'is_locked'       => 0,
            'locked_until'    => null
        ]);

        return [
            'valid'           => true,
            'supervisor_id'   => $user->id,
            'supervisor_name' => $user->name ?? $user->username,
            'role_name'       => $user->role_name
        ];
    }

    /**
     * Generate Nomor Void Unik: VOID-YYYYMMDD-XXXX
     */
    public function generateVoidNo()
    {
        $today = date('Ymd');
        $nextNum = 1;

        try {
            $q = $this->db->table('void_audit_logs')
                          ->like('void_number', 'VOID-' . $today . '-', 'after')
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->get();
            $lastEntry = ($q && is_object($q)) ? $q->getRow() : null;
            if ($lastEntry && !empty($lastEntry->void_number)) {
                if (preg_match('/VOID-\d+-(\d+)/', $lastEntry->void_number, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
            }
        } catch (\Throwable $e) {
            $nextNum = (int) date('His');
        }

        return 'VOID-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Eksekusi Pembatalan / Void Transaksi Terpadu (Atomic Reversal Engine)
     */
    public function executeVoid($transactionType, $referenceId, $reasonCategory, $reasonDetail, $supervisorUserId, $cashierUserId = null, $ipAddress = null, $userAgent = null)
    {
        $cashierUserId = $cashierUserId ?: (session('user_id') ?: 1);
        $ipAddress = $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $userAgent ?: substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        $this->db->transBegin();

        try {
            $voidNumber = $this->generateVoidNo();
            $reversalJournalId = null;
            $trxSummary = null;

            switch ($transactionType) {
                // 1. BILLING KASIR KLINIK
                case 'billing_klinik':
                case 'billing_transactions':
                    $trxSummary = $this->reverseBillingKlinik($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 2. KASIR FARMASI & APOTEK (RETAIL)
                case 'pharmacy_sale':
                case 'pharmacy_sales':
                    $trxSummary = $this->reversePharmacySale($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 3. FAKTUR GROSIR DISTRIBUTOR B2B
                case 'distributor_sale':
                case 'distributor_sales':
                    $trxSummary = $this->reverseDistributorSale($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 4. PEMBAYARAN PIUTANG DISTRIBUTOR
                case 'receivable_payment':
                case 'distributor_payments':
                    $trxSummary = $this->reverseDistributorPayment($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 5. KAS OPERASIONAL (PENGELUARAN / PEMASUKAN LAIN)
                case 'cash_expense':
                case 'cash_transactions':
                    $trxSummary = $this->reverseCashTransaction($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 6. TRANSFER KAS INTERNAL
                case 'cash_transfer':
                case 'internal_cash_transfers':
                    $trxSummary = $this->reverseCashTransfer($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 7. RESTO GIZI SEHAT
                case 'resto_sale':
                case 'restaurant_orders':
                    $trxSummary = $this->reverseRestoSale($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                // 8. SETTLEMENT FEE DOKTER
                case 'doctor_fee':
                case 'doctor_fee_settlements':
                    $trxSummary = $this->reverseDoctorFeeSettlement($referenceId, $voidNumber, $reasonDetail);
                    $reversalJournalId = $trxSummary['journal_id'] ?? null;
                    break;

                default:
                    throw new \Exception("Tipe transaksi '{$transactionType}' tidak didukung untuk pembatalan void.");
            }

            // Catat ke void_audit_logs
            $logPayload = [
                'void_number'         => $voidNumber,
                'transaction_type'    => $trxSummary['normalized_type'] ?? $transactionType,
                'reference_id'        => (int)$referenceId,
                'reference_number'    => $trxSummary['reference_number'] ?? ('REF-' . $referenceId),
                'total_amount'        => (float)($trxSummary['total_amount'] ?? 0),
                'payment_method'      => $trxSummary['payment_method'] ?? 'tunai',
                'cash_register_id'    => $trxSummary['cash_register_id'] ?? null,
                'reversal_journal_id' => $reversalJournalId,
                'reason_category'     => $reasonCategory,
                'reason_detail'       => $reasonDetail,
                'cashier_user_id'     => (int)$cashierUserId,
                'supervisor_user_id'  => (int)$supervisorUserId,
                'ip_address'          => $ipAddress,
                'user_agent'          => $userAgent,
                'item_snapshot_json'  => json_encode($trxSummary['snapshot'] ?? []),
                'created_at'          => date('Y-m-d H:i:s')
            ];

            $this->db->table('void_audit_logs')->insert($logPayload);
            $voidId = $this->db->insertID();

            // Update record transaksi asal dengan void_log_id
            $this->updateTransactionVoidFlags($transactionType, $referenceId, $voidId);

            $this->db->transCommit();

            // Catat ke audit trail umum sistem
            $this->logAuditTrail('VOID', ucfirst(str_replace('_', ' ', $transactionType)), "Membatalkan {$logPayload['reference_number']} senilai Rp " . number_format($logPayload['total_amount'], 0, ',', '.') . " (No. Void: {$voidNumber})", $voidId);

            return [
                'status'           => 'success',
                'void_id'          => $voidId,
                'void_number'      => $voidNumber,
                'reference_number' => $logPayload['reference_number'],
                'total_amount'     => $logPayload['total_amount'],
                'message'          => "Transaksi {$logPayload['reference_number']} berhasil dibatalkan (No. Void: {$voidNumber}). Jurnal pembalik dan stok telah dipulihkan."
            ];

        } catch (\Throwable $e) {
            $this->db->transRollback();
            return [
                'status'  => 'error',
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 1. Reversing Billing Kasir Klinik
     */
    protected function reverseBillingKlinik($id, $voidNumber, $reason)
    {
        $billing = $this->db->table('billing_transactions')->where('id', $id)->get()->getRow();
        if (!$billing) {
            throw new \Exception("Data tagihan billing #{$id} tidak ditemukan.");
        }
        if (!empty($billing->is_voided) || ($billing->payment_status ?? '') === 'void') {
            throw new \Exception("Tagihan billing {$billing->invoice_no} sudah pernah dibatalkan sebelumnya.");
        }

        // Ambil rincian billing
        $details = $this->db->table('billing_details')->where('billing_id', $id)->get()->getResult();

        // 1. Reversing Jurnal Akuntansi yang terkait dengan billing ini
        $journalId = $this->createReversingJournal(
            'Kasir Klinik (Billing Pasien)',
            $id,
            $billing->invoice_no,
            $voidNumber,
            $reason
        );

        // 2. Kembalikan status kunjungan pasien jika ada
        if (!empty($billing->visit_id)) {
            $this->db->table('patient_visits')->where('id', $billing->visit_id)->update([
                'payment_status' => 'unpaid',
                'status'         => 'in_progress', // Kembalikan ke antrean kasir/pelayanan
                'updated_at'     => date('Y-m-d H:i:s')
            ]);
        }

        // 3. Batalkan fee transaksi dokter yang terkait dengan billing ini
        try {
            $this->db->table('fee_transactions')
                     ->where('reference_type', 'billing')
                     ->where('reference_id', $id)
                     ->update([
                         'status'     => 'cancelled',
                         'notes'      => "[VOID {$voidNumber}] Dibatalkan kasir: {$reason}",
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);
        } catch (\Throwable $e) {}

        // 4. Update status billing
        $this->db->table('billing_transactions')->where('id', $id)->update([
            'payment_status' => 'void',
            'is_voided'      => 1,
            'voided_at'      => date('Y-m-d H:i:s'),
            'notes'          => trim(($billing->notes ?? '') . " [VOID {$voidNumber}] {$reason}")
        ]);

        return [
            'normalized_type'  => 'billing_klinik',
            'reference_number' => $billing->invoice_no ?? ('BILL-' . $id),
            'total_amount'     => (float)($billing->total_amount ?? $billing->grand_total ?? 0),
            'payment_method'   => $billing->payment_method ?? 'tunai',
            'cash_register_id' => $billing->cash_register_id ?? null,
            'journal_id'       => $journalId,
            'snapshot'         => [
                'billing' => $billing,
                'details' => $details
            ]
        ];
    }

    /**
     * 2. Reversing Penjualan Apotek (Retail)
     */
    protected function reversePharmacySale($id, $voidNumber, $reason)
    {
        $sale = $this->db->table('pharmacy_sales')->where('id', $id)->get()->getRow();
        if (!$sale) {
            throw new \Exception("Data penjualan apotek #{$id} tidak ditemukan.");
        }
        if (!empty($sale->is_voided) || ($sale->status ?? '') === 'void' || ($sale->status ?? '') === 'cancelled') {
            throw new \Exception("Penjualan apotek {$sale->invoice_no} sudah pernah dibatalkan.");
        }

        $items = $this->db->table('pharmacy_sale_details')->where('sale_id', $id)->get()->getResult();

        // 1. Pulihkan Stok Obat & Batch FEFO
        foreach ($items as $item) {
            $medicineId = $item->medicine_id;
            $qty = (float)$item->quantity;
            $batchId = $item->batch_id ?? null;

            // Tambah stok master obat
            $this->db->query("UPDATE `medicines` SET `stock` = `stock` + ? WHERE `id` = ?", [$qty, $medicineId]);

            // Tambah stok batch jika ada
            if ($batchId) {
                $this->db->query("UPDATE `medicine_batches` SET `current_stock` = `current_stock` + ? WHERE `id` = ?", [$qty, $batchId]);
            }

            // Catat mutasi pembalik di kartu stok
            try {
                $currentStock = (float)($this->db->table('medicines')->where('id', $medicineId)->get()->getRow()->stock ?? 0);
                $this->db->table('stock_movements')->insert([
                    'medicine_id'     => $medicineId,
                    'batch_id'        => $batchId,
                    'movement_type'   => 'IN',
                    'quantity'        => $qty,
                    'balance_after'   => $currentStock,
                    'reference_type'  => 'void_sale',
                    'reference_id'    => $id,
                    'notes'           => "[VOID {$voidNumber}] Pengembalian stok nota {$sale->invoice_no}: {$reason}",
                    'created_by'      => session('user_id') ?: 1,
                    'created_at'      => date('Y-m-d H:i:s')
                ]);
            } catch (\Throwable $e) {}
        }

        // 2. Reversing Jurnal Akuntansi
        $journalId = $this->createReversingJournal(
            'Kasir Apotek (Obat Bebas)',
            $id,
            $sale->invoice_no,
            $voidNumber,
            $reason
        );

        if (!$journalId) {
            $journalId = $this->createReversingJournal(
                'Kasir Apotek (Obat Resep)',
                $id,
                $sale->invoice_no,
                $voidNumber,
                $reason
            );
        }

        // 3. Update status penjualan
        $this->db->table('pharmacy_sales')->where('id', $id)->update([
            'status'     => 'cancelled',
            'is_voided'  => 1,
            'voided_at'  => date('Y-m-d H:i:s')
        ]);

        return [
            'normalized_type'  => 'pharmacy_sale',
            'reference_number' => $sale->invoice_no ?? ('INV-APT-' . $id),
            'total_amount'     => (float)($sale->grand_total ?? $sale->total_amount ?? 0),
            'payment_method'   => $sale->payment_method ?? 'tunai',
            'cash_register_id' => $sale->cash_register_id ?? null,
            'journal_id'       => $journalId,
            'snapshot'         => [
                'sale'  => $sale,
                'items' => $items
            ]
        ];
    }

    /**
     * 3. Reversing Faktur Distributor & Grosir B2B
     */
    protected function reverseDistributorSale($id, $voidNumber, $reason)
    {
        $sale = $this->db->table('distributor_sales')->where('id', $id)->get()->getRow();
        if (!$sale) {
            throw new \Exception("Faktur distributor #{$id} tidak ditemukan.");
        }
        if (!empty($sale->is_voided) || ($sale->status ?? '') === 'void' || ($sale->status ?? '') === 'cancelled') {
            throw new \Exception("Faktur distributor {$sale->invoice_number} sudah pernah dibatalkan.");
        }

        $items = $this->db->table('distributor_sale_details')->where('sale_id', $id)->get()->getResult();

        // 1. Pulihkan Stok Grosir B2B
        foreach ($items as $item) {
            $medicineId = $item->medicine_id;
            $qty = (float)$item->quantity;
            $batchNo = $item->batch_number ?? null;

            // Kembalikan ke distributor_stocks jika ada
            try {
                if ($this->db->tableExists('distributor_stocks')) {
                    $bQ = $this->db->table('distributor_stocks')->where('medicine_id', $medicineId);
                    if ($batchNo) $bQ->where('batch_number', $batchNo);
                    $distStock = $bQ->get()->getRow();

                    if ($distStock) {
                        $this->db->table('distributor_stocks')->where('id', $distStock->id)->update([
                            'current_stock' => (float)$distStock->current_stock + $qty
                        ]);
                    }
                }
            } catch (\Throwable $e) {}

            // Pulihkan juga medicines stock
            $this->db->query("UPDATE `medicines` SET `stock` = `stock` + ? WHERE `id` = ?", [$qty, $medicineId]);
        }

        // 2. Pulihkan Piutang & Plafon Kredit Pelanggan B2B jika transaksi Kredit
        if (!empty($sale->customer_id)) {
            $cust = $this->db->table('distributor_customers')->where('id', $sale->customer_id)->get()->getRow();
            if ($cust && ($sale->payment_type ?? '') === 'credit') {
                $revAmount = (float)$sale->grand_total;
                $newReceivable = max(0, (float)$cust->current_receivable - $revAmount);
                $this->db->table('distributor_customers')->where('id', $cust->id)->update([
                    'current_receivable' => $newReceivable
                ]);
            }
        }

        // 3. Batalkan data piutang distributor_receivables jika ada
        try {
            if ($this->db->tableExists('distributor_receivables')) {
                $this->db->table('distributor_receivables')->where('sale_id', $id)->update([
                    'status' => 'cancelled',
                    'notes'  => "[VOID {$voidNumber}] {$reason}"
                ]);
            }
        } catch (\Throwable $e) {}

        // 4. Reversing Jurnal Akuntansi
        $journalId = $this->createReversingJournal(
            'Penjualan Grosir Distributor',
            $id,
            $sale->invoice_number,
            $voidNumber,
            $reason
        );

        // 5. Update Status Faktur
        $this->db->table('distributor_sales')->where('id', $id)->update([
            'status'    => 'cancelled',
            'is_voided' => 1,
            'voided_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'normalized_type'  => 'distributor_sale',
            'reference_number' => $sale->invoice_number ?? ('INV-DIST-' . $id),
            'total_amount'     => (float)($sale->grand_total ?? 0),
            'payment_method'   => $sale->payment_type ?? 'tunai',
            'cash_register_id' => null,
            'journal_id'       => $journalId,
            'snapshot'         => [
                'sale'  => $sale,
                'items' => $items
            ]
        ];
    }

    /**
     * 4. Reversing Pembayaran Piutang Distributor
     */
    protected function reverseDistributorPayment($id, $voidNumber, $reason)
    {
        $pay = $this->db->table('distributor_payments')->where('id', $id)->get()->getRow();
        if (!$pay) {
            throw new \Exception("Data pembayaran piutang #{$id} tidak ditemukan.");
        }
        if (!empty($pay->is_voided)) {
            throw new \Exception("Pembayaran piutang {$pay->payment_number} sudah pernah dibatalkan.");
        }

        // 1. Tambahkan kembali sisa piutang di distributor_receivables / customer
        if (!empty($pay->receivable_id)) {
            $this->db->query("UPDATE `distributor_receivables` SET `paid_amount` = GREATEST(0, `paid_amount` - ?), `remaining_amount` = `remaining_amount` + ?, `status` = 'partial' WHERE `id` = ?", [$pay->amount_paid, $pay->amount_paid, $pay->receivable_id]);
        }
        if (!empty($pay->customer_id)) {
            $this->db->query("UPDATE `distributor_customers` SET `current_receivable` = `current_receivable` + ? WHERE `id` = ?", [$pay->amount_paid, $pay->customer_id]);
        }

        // 2. Reversing Jurnal Akuntansi
        $journalId = $this->createReversingJournal(
            'Pelunasan Piutang Distributor',
            $id,
            $pay->payment_number,
            $voidNumber,
            $reason
        );

        // 3. Update status pembayaran
        $this->db->table('distributor_payments')->where('id', $id)->update([
            'is_voided' => 1,
            'voided_at' => date('Y-m-d H:i:s'),
            'notes'     => trim(($pay->notes ?? '') . " [VOID {$voidNumber}] {$reason}")
        ]);

        return [
            'normalized_type'  => 'receivable_payment',
            'reference_number' => $pay->payment_number ?? ('PAY-DIST-' . $id),
            'total_amount'     => (float)($pay->amount_paid ?? 0),
            'payment_method'   => $pay->payment_method ?? 'transfer',
            'cash_register_id' => null,
            'journal_id'       => $journalId,
            'snapshot'         => $pay
        ];
    }

    /**
     * 5. Reversing Transaksi Kas Masuk / Kas Keluar
     */
    protected function reverseCashTransaction($id, $voidNumber, $reason)
    {
        $trx = $this->db->table('cash_transactions')->where('id', $id)->get()->getRow();
        if (!$trx) {
            throw new \Exception("Transaksi kas #{$id} tidak ditemukan.");
        }
        if (!empty($trx->is_voided)) {
            throw new \Exception("Transaksi kas #{$id} sudah pernah dibatalkan.");
        }

        $journalId = $this->createReversingJournal(
            'Transaksi Kas Operasional',
            $id,
            $trx->voucher_no ?? ('CSH-' . $id),
            $voidNumber,
            $reason
        );

        $this->db->table('cash_transactions')->where('id', $id)->update([
            'is_voided'   => 1,
            'voided_at'   => date('Y-m-d H:i:s'),
            'description' => trim(($trx->description ?? '') . " [VOID {$voidNumber}] {$reason}")
        ]);

        return [
            'normalized_type'  => 'cash_expense',
            'reference_number' => $trx->voucher_no ?? ('CSH-' . $id),
            'total_amount'     => (float)($trx->amount ?? 0),
            'payment_method'   => 'tunai',
            'cash_register_id' => $trx->cash_register_id ?? null,
            'journal_id'       => $journalId,
            'snapshot'         => $trx
        ];
    }

    /**
     * 6. Reversing Transfer Kas Internal
     */
    protected function reverseCashTransfer($id, $voidNumber, $reason)
    {
        $trx = $this->db->table('internal_cash_transfers')->where('id', $id)->get()->getRow();
        if (!$trx) {
            throw new \Exception("Transfer kas #{$id} tidak ditemukan.");
        }
        if (!empty($trx->is_voided)) {
            throw new \Exception("Transfer kas #{$id} sudah pernah dibatalkan.");
        }

        $journalId = $this->createReversingJournal(
            'Transfer Kas Internal',
            $id,
            $trx->transfer_no ?? ('TRF-' . $id),
            $voidNumber,
            $reason
        );

        $this->db->table('internal_cash_transfers')->where('id', $id)->update([
            'status'    => 'cancelled',
            'is_voided' => 1,
            'voided_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'normalized_type'  => 'cash_transfer',
            'reference_number' => $trx->transfer_no ?? ('TRF-' . $id),
            'total_amount'     => (float)($trx->amount ?? 0),
            'payment_method'   => 'transfer',
            'cash_register_id' => null,
            'journal_id'       => $journalId,
            'snapshot'         => $trx
        ];
    }

    /**
     * 7. Reversing Penjualan Resto
     */
    protected function reverseRestoSale($id, $voidNumber, $reason)
    {
        $order = $this->db->table('restaurant_orders')->where('id', $id)->get()->getRow();
        if (!$order) {
            throw new \Exception("Order resto #{$id} tidak ditemukan.");
        }
        if (!empty($order->is_voided) || ($order->status ?? '') === 'cancelled') {
            throw new \Exception("Order resto #{$id} sudah pernah dibatalkan.");
        }

        $items = $this->db->table('restaurant_order_details')->where('order_id', $id)->get()->getResult();

        $journalId = $this->createReversingJournal(
            'Kasir Resto (Resto Sehat)',
            $id,
            $order->order_number ?? ('RST-' . $id),
            $voidNumber,
            $reason
        );

        // Batalkan tiket dapur
        try {
            $this->db->table('kitchen_orders')->where('order_id', $id)->update(['status' => 'cancelled']);
        } catch (\Throwable $e) {}

        $this->db->table('restaurant_orders')->where('id', $id)->update([
            'status'    => 'cancelled',
            'is_voided' => 1,
            'voided_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'normalized_type'  => 'resto_sale',
            'reference_number' => $order->order_number ?? ('RST-' . $id),
            'total_amount'     => (float)($order->total_amount ?? 0),
            'payment_method'   => $order->payment_method ?? 'tunai',
            'cash_register_id' => null,
            'journal_id'       => $journalId,
            'snapshot'         => [
                'order' => $order,
                'items' => $items
            ]
        ];
    }

    /**
     * 8. Reversing Settlement Fee Dokter
     */
    protected function reverseDoctorFeeSettlement($id, $voidNumber, $reason)
    {
        $stl = $this->db->table('doctor_fee_settlements')->where('id', $id)->get()->getRow();
        if (!$stl) {
            throw new \Exception("Data pencairan fee dokter #{$id} tidak ditemukan.");
        }
        if (!empty($stl->is_voided) || ($stl->status ?? '') === 'cancelled') {
            throw new \Exception("Pencairan fee dokter #{$id} sudah pernah dibatalkan.");
        }

        // Kembalikan status fee_transactions ke 'approved' / 'unsettled'
        try {
            $this->db->table('fee_transactions')
                     ->where('settlement_id', $id)
                     ->update([
                         'status'        => 'approved',
                         'settlement_id' => null
                     ]);
        } catch (\Throwable $e) {}

        $journalId = $this->createReversingJournal(
            'Settlement Fee Dokter',
            $id,
            $stl->settlement_no ?? ('STL-' . $id),
            $voidNumber,
            $reason
        );

        $this->db->table('doctor_fee_settlements')->where('id', $id)->update([
            'status'    => 'cancelled',
            'is_voided' => 1,
            'voided_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'normalized_type'  => 'doctor_fee',
            'reference_number' => $stl->settlement_no ?? ('STL-' . $id),
            'total_amount'     => (float)($stl->total_fee_amount ?? $stl->net_amount ?? 0),
            'payment_method'   => 'transfer',
            'cash_register_id' => null,
            'journal_id'       => $journalId,
            'snapshot'         => $stl
        ];
    }

    /**
     * Helper: Buat Jurnal Pembalik Otomatis Berpasangan (Reversing Journal Entry)
     */
    protected function createReversingJournal($sourceModule, $referenceId, $referenceNo, $voidNumber, $reason)
    {
        // Cari entri jurnal asal
        $origJournals = $this->db->table('journal_entries')
                                 ->where('reference_id', $referenceId)
                                 ->like('source_module', $sourceModule)
                                 ->notLike('journal_no', 'JV-VOID-', 'after')
                                 ->get()
                                 ->getResult();

        if (empty($origJournals)) {
            // Coba pencarian fleksibel by reference_id saja
            $origJournals = $this->db->table('journal_entries')
                                     ->where('reference_id', $referenceId)
                                     ->notLike('journal_no', 'JV-VOID-', 'after')
                                     ->get()
                                     ->getResult();
        }

        if (empty($origJournals)) {
            return null;
        }

        $reversalJournalId = null;

        foreach ($origJournals as $origJournal) {
            $origDetails = $this->db->table('journal_entry_details')
                                    ->where('journal_id', $origJournal->id)
                                    ->get()
                                    ->getResult();

            if (empty($origDetails)) continue;

            $revJournalNo = 'JV-' . str_replace('VOID-', 'VOID-', $voidNumber);

            // Buat header jurnal pembalik
            $this->db->table('journal_entries')->insert([
                'journal_no'    => $revJournalNo,
                'entry_date'    => date('Y-m-d'),
                'source_module' => '[PEMBALIKAN VOID] ' . $origJournal->source_module,
                'reference_id'  => $referenceId,
                'description'   => "[PEMBATALAN/VOID {$voidNumber}] Pembalikan Jurnal #{$origJournal->journal_no} untuk {$referenceNo}: {$reason}",
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $newJournalId = $this->db->insertID();
            $reversalJournalId = $newJournalId;

            // Invert seluruh posisi Debet menjadi Kredit, dan Kredit menjadi Debet
            foreach ($origDetails as $d) {
                $newDebit = (float)$d->credit;
                $newCredit = (float)$d->debit;

                $this->db->table('journal_entry_details')->insert([
                    'journal_id' => $newJournalId,
                    'account_id' => $d->account_id,
                    'debit'      => $newDebit,
                    'credit'     => $newCredit
                ]);

                // Update saldo CoA
                $this->applyBalanceMovement($d->account_id, $newDebit, $newCredit);
            }
        }

        return $reversalJournalId;
    }

    /**
     * Update saldo akun pada Chart of Accounts
     */
    protected function applyBalanceMovement($accountId, $debit, $credit)
    {
        if (!$accountId) return;
        $acc = $this->db->table('accounts')->where('id', $accountId)->get()->getRow();
        if (!$acc) return;

        $net = 0;
        if (isset($acc->normal_balance) && $acc->normal_balance === 'debit') {
            $net = $debit - $credit;
        } else {
            $net = $credit - $debit;
        }
        $newBal = (float)($acc->balance ?? 0) + $net;
        $this->db->table('accounts')->where('id', $accountId)->update(['balance' => $newBal]);
    }

    /**
     * Helper update flag void di tabel asal
     */
    protected function updateTransactionVoidFlags($trxType, $referenceId, $voidLogId)
    {
        $tableMap = [
            'billing_klinik'       => 'billing_transactions',
            'billing_transactions' => 'billing_transactions',
            'pharmacy_sale'        => 'pharmacy_sales',
            'pharmacy_sales'       => 'pharmacy_sales',
            'distributor_sale'     => 'distributor_sales',
            'distributor_sales'    => 'distributor_sales',
            'receivable_payment'   => 'distributor_payments',
            'distributor_payments' => 'distributor_payments',
            'cash_expense'         => 'cash_transactions',
            'cash_transactions'    => 'cash_transactions',
            'cash_transfer'        => 'internal_cash_transfers',
            'resto_sale'           => 'restaurant_orders',
            'doctor_fee'           => 'doctor_fee_settlements'
        ];

        $targetTable = $tableMap[$trxType] ?? null;
        if ($targetTable && $this->db->tableExists($targetTable)) {
            try {
                $fields = $this->db->getFieldNames($targetTable);
                $update = [];
                if (in_array('is_voided', $fields)) $update['is_voided'] = 1;
                if (in_array('void_log_id', $fields)) $update['void_log_id'] = $voidLogId;
                if (in_array('voided_at', $fields)) $update['voided_at'] = date('Y-m-d H:i:s');
                if (!empty($update)) {
                    $this->db->table($targetTable)->where('id', $referenceId)->update($update);
                }
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Catat ke audit trail log
     */
    protected function logAuditTrail($action, $module, $description, $recordId = 0)
    {
        try {
            $this->db->table('audit_logs')->insert([
                'user_id'    => session('user_id') ?: 1,
                'action'     => $action,
                'module'     => $module,
                'table_name' => 'void_audit_logs',
                'record_id'  => $recordId,
                'new_value'  => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {}
    }
}
