<?php

namespace App\Services;

use Config\Database;
use App\Services\JournalEngine;

class DistributorService
{
    protected $db;
    protected $journalEngine;

    public function __construct()
    {
        $this->db = Database::connect('default');
        $this->journalEngine = new JournalEngine();
    }

    /**
     * Generate unique invoice number: INV-DIST-YYYYMMDD-XXXX
     */
    public function generateInvoiceNo()
    {
        $today = date('Ymd');
        $prefix = 'INV-DIST-' . $today . '-';
        $q = $this->db->table('distributor_sales')
                      ->like('invoice_no', $prefix, 'after')
                      ->orderBy('id', 'DESC')
                      ->limit(1)
                      ->get();
        $row = ($q && is_object($q)) ? $q->getRow() : null;
        $seq = 1;
        if ($row && !empty($row->invoice_no)) {
            $parts = explode('-', $row->invoice_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique payment number: PAY-DIST-YYYYMMDD-XXXX
     */
    public function generatePaymentNo()
    {
        $today = date('Ymd');
        $prefix = 'PAY-DIST-' . $today . '-';
        $q = $this->db->table('distributor_payments')
                      ->like('payment_no', $prefix, 'after')
                      ->orderBy('id', 'DESC')
                      ->limit(1)
                      ->get();
        $row = ($q && is_object($q)) ? $q->getRow() : null;
        $seq = 1;
        if ($row && !empty($row->payment_no)) {
            $parts = explode('-', $row->payment_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique return number: RET-DIST-YYYYMMDD-XXXX
     */
    public function generateReturnNo()
    {
        $today = date('Ymd');
        $prefix = 'RET-DIST-' . $today . '-';
        $q = $this->db->table('distributor_returns')
                      ->like('return_no', $prefix, 'after')
                      ->orderBy('id', 'DESC')
                      ->limit(1)
                      ->get();
        $row = ($q && is_object($q)) ? $q->getRow() : null;
        $seq = 1;
        if ($row && !empty($row->return_no)) {
            $parts = explode('-', $row->return_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique transfer number: TRF-DIST-YYYYMMDD-XXXX
     */
    public function generateTransferNo()
    {
        $today = date('Ymd');
        $prefix = 'TRF-DIST-' . $today . '-';
        $q = $this->db->table('distributor_stock_transfers')
                      ->like('transfer_no', $prefix, 'after')
                      ->orderBy('id', 'DESC')
                      ->limit(1)
                      ->get();
        $row = ($q && is_object($q)) ? $q->getRow() : null;
        $seq = 1;
        if ($row && !empty($row->transfer_no)) {
            $parts = explode('-', $row->transfer_no);
            $seq = intval(end($parts)) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get Dashboard Stats for Distributor Module
     */
    public function getDashboardStats()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // 1. Total Sales Today & This Month
        $salesToday = $this->db->table('distributor_sales')
                               ->selectSum('total_amount')
                               ->where('sale_date', $today)
                               ->get()->getRow()->total_amount ?? 0;

        $salesMonth = $this->db->table('distributor_sales')
                               ->selectSum('total_amount')
                               ->like('sale_date', $thisMonth, 'after')
                               ->get()->getRow()->total_amount ?? 0;

        $salesCountMonth = $this->db->table('distributor_sales')
                                    ->where('sale_date >=', $thisMonth . '-01')
                                    ->countAllResults();

        // 2. Receivables (Piutang) Stats
        $totalReceivable = $this->db->table('distributor_receivables')
                                    ->selectSum('remaining_balance')
                                    ->where('status !=', 'paid')
                                    ->get()->getRow()->remaining_balance ?? 0;

        $overdueReceivable = $this->db->table('distributor_receivables')
                                      ->selectSum('remaining_balance')
                                      ->where('status !=', 'paid')
                                      ->where('due_date <', $today)
                                      ->get()->getRow()->remaining_balance ?? 0;

        $overdueCount = $this->db->table('distributor_receivables')
                                 ->where('status !=', 'paid')
                                 ->where('due_date <', $today)
                                 ->countAllResults();

        // 3. Total Stock Value & Items in Distributor Warehouse
        $stockValRes = $this->db->table('distributor_stocks')
                                ->select('SUM(stock * buy_price) as total_cogs_val, SUM(stock * selling_price) as total_sell_val, SUM(stock) as total_units, COUNT(*) as total_items')
                                ->where('stock >', 0)
                                ->get()->getRow();

        // 4. Low stock count
        $lowStockCount = $this->db->query("SELECT COUNT(*) as c FROM distributor_stocks WHERE stock <= min_stock")->getRow()->c ?? 0;

        // 5. Total Active Customers
        $customerCount = $this->db->table('distributor_customers')->where('status', 'active')->countAllResults();

        // 6. Recent Sales
        $recentSales = $this->db->table('distributor_sales')
                                ->select('distributor_sales.*, distributor_customers.name as customer_name, distributor_customers.company_name')
                                ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                                ->orderBy('distributor_sales.id', 'DESC')
                                ->limit(5)
                                ->get()->getResult();

        // 7. Recent Overdue / Approaching Due Receivables
        $dueReceivables = $this->db->table('distributor_receivables')
                                   ->select('distributor_receivables.*, distributor_customers.name as customer_name, distributor_customers.phone')
                                   ->join('distributor_customers', 'distributor_customers.id = distributor_receivables.customer_id')
                                   ->where('distributor_receivables.status !=', 'paid')
                                   ->orderBy('distributor_receivables.due_date', 'ASC')
                                   ->limit(5)
                                   ->get()->getResult();

        return [
            'sales_today'         => (float) $salesToday,
            'sales_month'         => (float) $salesMonth,
            'sales_count_month'   => (int) $salesCountMonth,
            'total_receivable'    => (float) $totalReceivable,
            'overdue_receivable'  => (float) $overdueReceivable,
            'overdue_count'       => (int) $overdueCount,
            'stock_cogs_value'    => (float) ($stockValRes->total_cogs_val ?? 0),
            'stock_sell_value'    => (float) ($stockValRes->total_sell_val ?? 0),
            'total_stock_units'   => (int) ($stockValRes->total_units ?? 0),
            'total_stock_items'   => (int) ($stockValRes->total_items ?? 0),
            'low_stock_count'     => (int) $lowStockCount,
            'customer_count'      => (int) $customerCount,
            'recent_sales'        => $recentSales,
            'due_receivables'     => $dueReceivables
        ];
    }

    /**
     * Process B2B Sale / Cashier Transaction
     */
    public function processSale(array $data, int $userId = 1)
    {
        $this->db->transStart();

        $customerId = (int) ($data['customer_id'] ?? 0);
        $customer = $this->db->table('distributor_customers')->where('id', $customerId)->get()->getRow();
        if (!$customer) {
            throw new \Exception('Customer / Pelanggan tidak ditemukan.');
        }

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new \Exception('Daftar barang penjualan tidak boleh kosong.');
        }

        $paymentType = strtolower($data['payment_type'] ?? 'cash'); // cash, credit, transfer
        $paymentTerms = (int) ($data['payment_terms_days'] ?? ($customer->payment_terms_days ?? 0));
        $saleDate = !empty($data['sale_date']) ? $data['sale_date'] : date('Y-m-d');
        
        $dueDate = null;
        if ($paymentType === 'credit') {
            $days = $paymentTerms > 0 ? $paymentTerms : 30;
            $dueDate = date('Y-m-d', strtotime($saleDate . " +$days days"));
        }

        $invoiceNo = !empty($data['invoice_no']) ? $data['invoice_no'] : $this->generateInvoiceNo();

        // Calculate totals and check stocks
        $subtotal = 0;
        $totalCogs = 0;
        $saleDetails = [];
        $itemDescList = [];

        foreach ($items as $item) {
            $stockId = (int) ($item['distributor_stock_id'] ?? 0);
            $qty = (int) ($item['qty'] ?? 0);
            $sellingPrice = (float) ($item['selling_price'] ?? 0);
            $discItem = (float) ($item['discount_amount'] ?? 0);

            if ($qty <= 0) continue;

            $stockRow = $this->db->table('distributor_stocks')
                                 ->select('distributor_stocks.*, medicines.name as med_name, medicines.code as med_code')
                                 ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                                 ->where('distributor_stocks.id', $stockId)
                                 ->get()->getRow();

            if (!$stockRow) {
                throw new \Exception("Stok barang ID {$stockId} tidak ditemukan di gudang distributor.");
            }

            if ($stockRow->stock < $qty) {
                throw new \Exception("Stok tidak mencukupi untuk obat '{$stockRow->med_name}'. Tersedia: {$stockRow->stock}, diminta: {$qty}.");
            }

            $lineSubtotal = ($sellingPrice * $qty) - $discItem;
            $lineCogs = (float) $stockRow->buy_price * $qty;

            $subtotal += $lineSubtotal;
            $totalCogs += $lineCogs;

            $saleDetails[] = [
                'distributor_stock_id' => $stockRow->id,
                'medicine_id'          => $stockRow->medicine_id,
                'batch_no'             => $stockRow->batch_no,
                'expired_date'         => $stockRow->expired_date,
                'qty'                  => $qty,
                'unit'                 => $item['unit'] ?? 'PCS',
                'buy_price'            => $stockRow->buy_price,
                'selling_price'        => $sellingPrice,
                'discount_amount'      => $discItem,
                'subtotal'             => $lineSubtotal,
                'cogs_total'           => $lineCogs,
                'med_name'             => $stockRow->med_name,
                'stock_before'         => $stockRow->stock
            ];

            $itemDescList[] = "{$stockRow->med_name} ({$qty}x)";
        }

        if (empty($saleDetails)) {
            throw new \Exception('Tidak ada item valid untuk diproses.');
        }

        // Global discount & tax
        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $taxAmount = (float) ($data['tax_amount'] ?? 0);
        $totalAmount = max(0, $subtotal - $discountAmount + $taxAmount);

        // Check customer credit limit if credit sale
        if ($paymentType === 'credit') {
            $creditLimit = (float) $customer->credit_limit;
            $currentRec = (float) $customer->current_receivable;
            if ($creditLimit > 0 && ($currentRec + $totalAmount) > $creditLimit) {
                $maxAllowed = max(0, $creditLimit - $currentRec);
                throw new \Exception("Transaksi melebihi limit kredit customer! Limit: Rp " . number_format($creditLimit, 0, ',', '.') . ", Piutang Berjalan: Rp " . number_format($currentRec, 0, ',', '.') . ", Sisa Kuota Kredit: Rp " . number_format($maxAllowed, 0, ',', '.'));
            }
        }

        $paidAmount = ($paymentType === 'credit') ? 0.00 : $totalAmount;
        $remainingAmount = ($paymentType === 'credit') ? $totalAmount : 0.00;
        $paymentStatus = ($paymentType === 'credit') ? 'unpaid' : 'paid';

        // 1. Insert into distributor_sales
        $this->db->table('distributor_sales')->insert([
            'invoice_no'          => $invoiceNo,
            'sale_date'           => $saleDate,
            'customer_id'         => $customerId,
            'payment_type'        => $paymentType,
            'payment_terms_days'  => $paymentTerms,
            'due_date'            => $dueDate,
            'subtotal'            => $subtotal,
            'discount_percent'    => (float) ($data['discount_percent'] ?? 0),
            'discount_amount'     => $discountAmount,
            'tax_percent'         => (float) ($data['tax_percent'] ?? 0),
            'tax_amount'          => $taxAmount,
            'total_amount'        => $totalAmount,
            'paid_amount'         => $paidAmount,
            'remaining_amount'    => $remainingAmount,
            'payment_status'      => $paymentStatus,
            'delivery_status'     => 'delivered',
            'notes'               => $data['notes'] ?? '',
            'created_by'          => $userId,
            'created_at'          => date('Y-m-d H:i:s')
        ]);
        $saleId = $this->db->insertID();

        // 2. Insert details & deduct stock
        foreach ($saleDetails as $det) {
            $this->db->table('distributor_sale_details')->insert([
                'sale_id'              => $saleId,
                'distributor_stock_id' => $det['distributor_stock_id'],
                'medicine_id'          => $det['medicine_id'],
                'batch_no'             => $det['batch_no'],
                'expired_date'         => $det['expired_date'],
                'qty'                  => $det['qty'],
                'unit'                 => $det['unit'],
                'buy_price'            => $det['buy_price'],
                'selling_price'        => $det['selling_price'],
                'discount_amount'      => $det['discount_amount'],
                'subtotal'             => $det['subtotal'],
                'cogs_total'           => $det['cogs_total'],
                'created_at'           => date('Y-m-d H:i:s')
            ]);

            // Deduct stock in distributor_stocks
            $newStock = $det['stock_before'] - $det['qty'];
            $this->db->table('distributor_stocks')
                     ->where('id', $det['distributor_stock_id'])
                     ->update(['stock' => $newStock]);

            // Log stock movement
            $this->db->table('distributor_stock_movements')->insert([
                'medicine_id'          => $det['medicine_id'],
                'distributor_stock_id' => $det['distributor_stock_id'],
                'movement_type'        => 'out',
                'qty'                  => $det['qty'],
                'stock_before'         => $det['stock_before'],
                'stock_after'          => $newStock,
                'unit_price'           => $det['selling_price'],
                'reference_no'         => $invoiceNo,
                'notes'                => "Penjualan Grosir Faktur {$invoiceNo} ({$customer->name})",
                'created_by'           => $userId,
                'created_at'           => date('Y-m-d H:i:s')
            ]);
        }

        // 3. If credit, insert into distributor_receivables & update customer balance
        if ($paymentType === 'credit') {
            $this->db->table('distributor_receivables')->insert([
                'sale_id'           => $saleId,
                'customer_id'       => $customerId,
                'invoice_no'        => $invoiceNo,
                'invoice_date'      => $saleDate,
                'due_date'          => $dueDate,
                'total_receivable'  => $totalAmount,
                'total_paid'        => 0.00,
                'remaining_balance' => $totalAmount,
                'status'            => 'unpaid',
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            $newCustRec = (float) $customer->current_receivable + $totalAmount;
            $this->db->table('distributor_customers')
                     ->where('id', $customerId)
                     ->update(['current_receivable' => $newCustRec]);
        }

        // 4. Automated Journal Entry via JournalEngine
        $itemSummary = implode(', ', $itemDescList);
        $this->journalEngine->postDistributorSaleJournal(
            $saleId,
            $invoiceNo,
            $totalAmount,
            $paymentType,
            $paymentType === 'transfer' ? 'transfer' : 'tunai',
            $totalCogs,
            $discountAmount,
            $taxAmount,
            $customer->name,
            $itemSummary
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses transaksi penjualan grosir.');
        }

        return [
            'status'     => 'success',
            'sale_id'    => $saleId,
            'invoice_no' => $invoiceNo,
            'message'    => "Penjualan Grosir {$invoiceNo} berhasil disimpan dan dijurnal otomatis."
        ];
    }

    /**
     * Process Receivable Payment (Pelunasan Piutang)
     */
    public function processPayment(array $data, int $userId = 1)
    {
        $this->db->transStart();

        $receivableId = (int) ($data['receivable_id'] ?? 0);
        $rec = $this->db->table('distributor_receivables')->where('id', $receivableId)->get()->getRow();
        if (!$rec) {
            throw new \Exception('Data piutang tidak ditemukan.');
        }

        if ($rec->status === 'paid' || $rec->remaining_balance <= 0) {
            throw new \Exception('Piutang ini sudah lunas.');
        }

        $amount = (float) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw new \Exception('Jumlah pembayaran harus lebih besar dari 0.');
        }

        if ($amount > (float) $rec->remaining_balance) {
            throw new \Exception('Jumlah pembayaran melebihi sisa piutang (Sisa: Rp ' . number_format($rec->remaining_balance, 0, ',', '.') . ').');
        }

        $paymentDate = !empty($data['payment_date']) ? $data['payment_date'] : date('Y-m-d');
        $paymentMethod = $data['payment_method'] ?? 'cash';
        $paymentNo = $this->generatePaymentNo();

        // 1. Insert distributor_payments
        $this->db->table('distributor_payments')->insert([
            'payment_no'       => $paymentNo,
            'receivable_id'    => $receivableId,
            'sale_id'          => $rec->sale_id,
            'customer_id'      => $rec->customer_id,
            'payment_date'     => $paymentDate,
            'amount'           => $amount,
            'payment_method'   => $paymentMethod,
            'reference_number' => $data['reference_number'] ?? '',
            'notes'            => $data['notes'] ?? '',
            'created_by'       => $userId,
            'created_at'       => date('Y-m-d H:i:s')
        ]);
        $paymentId = $this->db->insertID();

        // 2. Update distributor_receivables
        $newPaid = (float) $rec->total_paid + $amount;
        $newRem = (float) $rec->total_receivable - $newPaid;
        $newStatus = ($newRem <= 0.01) ? 'paid' : 'partial';

        $this->db->table('distributor_receivables')
                 ->where('id', $receivableId)
                 ->update([
                     'total_paid'        => $newPaid,
                     'remaining_balance' => max(0, $newRem),
                     'status'            => $newStatus,
                     'last_payment_date' => $paymentDate
                 ]);

        // 3. Update distributor_sales
        $sale = $this->db->table('distributor_sales')->where('id', $rec->sale_id)->get()->getRow();
        if ($sale) {
            $newSalePaid = (float) $sale->paid_amount + $amount;
            $newSaleRem = (float) $sale->total_amount - $newSalePaid;
            $saleStatus = ($newSaleRem <= 0.01) ? 'paid' : 'partial';

            $this->db->table('distributor_sales')
                     ->where('id', $sale->id)
                     ->update([
                         'paid_amount'      => $newSalePaid,
                         'remaining_amount' => max(0, $newSaleRem),
                         'payment_status'   => $saleStatus
                     ]);
        }

        // 4. Update customer balance
        $cust = $this->db->table('distributor_customers')->where('id', $rec->customer_id)->get()->getRow();
        if ($cust) {
            $newCustRec = max(0, (float) $cust->current_receivable - $amount);
            $this->db->table('distributor_customers')
                     ->where('id', $cust->id)
                     ->update(['current_receivable' => $newCustRec]);
        }

        // 5. Automated Journal Entry
        $this->journalEngine->postDistributorPaymentJournal(
            $paymentId,
            $paymentNo,
            $amount,
            $paymentMethod,
            $rec->invoice_no,
            $cust->name ?? 'Customer'
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses pelunasan piutang.');
        }

        return [
            'status'     => 'success',
            'payment_no' => $paymentNo,
            'message'    => "Pembayaran piutang {$paymentNo} sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil diproses dan dijurnal."
        ];
    }

    /**
     * Process Sales Return (Retur Penjualan Grosir)
     */
    public function processReturn(array $data, int $userId = 1)
    {
        $this->db->transStart();

        $saleId = (int) ($data['sale_id'] ?? 0);
        $sale = $this->db->table('distributor_sales')->where('id', $saleId)->get()->getRow();
        if (!$sale) {
            throw new \Exception('Transaksi penjualan tidak ditemukan.');
        }

        $items = $data['items'] ?? [];
        if (empty($items) || !is_array($items)) {
            throw new \Exception('Daftar barang retur tidak boleh kosong.');
        }

        $returnNo = $this->generateReturnNo();
        $returnDate = !empty($data['return_date']) ? $data['return_date'] : date('Y-m-d');
        $refundMethod = $data['refund_method'] ?? 'deduct_receivable';

        $totalReturnAmount = 0;
        $totalCogsReversal = 0;
        $returnDetails = [];

        foreach ($items as $it) {
            $saleDetailId = (int) ($it['sale_detail_id'] ?? 0);
            $qtyReturn = (int) ($it['qty'] ?? 0);
            if ($qtyReturn <= 0) continue;

            $saleDet = $this->db->table('distributor_sale_details')->where('id', $saleDetailId)->get()->getRow();
            if (!$saleDet || $saleDet->sale_id != $saleId) {
                throw new \Exception('Detail penjualan retur tidak valid.');
            }

            if ($qtyReturn > $saleDet->qty) {
                throw new \Exception("Jumlah retur ({$qtyReturn}) melebihi jumlah yang terjual ({$saleDet->qty}).");
            }

            $subtotal = $qtyReturn * (float) $saleDet->selling_price;
            $cogsReversal = $qtyReturn * (float) $saleDet->buy_price;

            $totalReturnAmount += $subtotal;
            $totalCogsReversal += $cogsReversal;

            $returnDetails[] = [
                'sale_detail_id'       => $saleDet->id,
                'medicine_id'          => $saleDet->medicine_id,
                'distributor_stock_id' => $saleDet->distributor_stock_id,
                'batch_no'             => $saleDet->batch_no,
                'qty'                  => $qtyReturn,
                'price'                => $saleDet->selling_price,
                'subtotal'             => $subtotal,
                'buy_price'            => $saleDet->buy_price,
                'cogs_reversal'        => $cogsReversal
            ];
        }

        if (empty($returnDetails)) {
            throw new \Exception('Tidak ada item valid untuk diretur.');
        }

        // 1. Insert distributor_returns
        $this->db->table('distributor_returns')->insert([
            'return_no'            => $returnNo,
            'return_date'          => $returnDate,
            'sale_id'              => $saleId,
            'customer_id'          => $sale->customer_id,
            'total_return_amount'  => $totalReturnAmount,
            'refund_method'        => $refundMethod,
            'reason'               => $data['reason'] ?? '',
            'status'               => 'completed',
            'created_by'           => $userId,
            'created_at'           => date('Y-m-d H:i:s')
        ]);
        $returnId = $this->db->insertID();

        // 2. Insert details & restore distributor stock
        foreach ($returnDetails as $rd) {
            $this->db->table('distributor_return_details')->insert([
                'return_id'            => $returnId,
                'sale_detail_id'       => $rd['sale_detail_id'],
                'medicine_id'          => $rd['medicine_id'],
                'distributor_stock_id' => $rd['distributor_stock_id'],
                'batch_no'             => $rd['batch_no'],
                'qty'                  => $rd['qty'],
                'price'                => $rd['price'],
                'subtotal'             => $rd['subtotal']
            ]);

            // Restore stock in distributor_stocks
            $curStk = $this->db->table('distributor_stocks')->where('id', $rd['distributor_stock_id'])->get()->getRow();
            $stockBefore = $curStk ? $curStk->stock : 0;
            $stockAfter = $stockBefore + $rd['qty'];

            if ($curStk) {
                $this->db->table('distributor_stocks')
                         ->where('id', $rd['distributor_stock_id'])
                         ->update(['stock' => $stockAfter]);
            }

            // Log movement
            $this->db->table('distributor_stock_movements')->insert([
                'medicine_id'          => $rd['medicine_id'],
                'distributor_stock_id' => $rd['distributor_stock_id'],
                'movement_type'        => 'return_in',
                'qty'                  => $rd['qty'],
                'stock_before'         => $stockBefore,
                'stock_after'          => $stockAfter,
                'unit_price'           => $rd['price'],
                'reference_no'         => $returnNo,
                'notes'                => "Retur Penjualan Grosir Faktur {$sale->invoice_no} ({$returnNo})",
                'created_by'           => $userId,
                'created_at'           => date('Y-m-d H:i:s')
            ]);
        }

        // 3. Handle financial adjustment
        $cust = $this->db->table('distributor_customers')->where('id', $sale->customer_id)->get()->getRow();
        if ($refundMethod === 'deduct_receivable') {
            $rec = $this->db->table('distributor_receivables')->where('sale_id', $saleId)->get()->getRow();
            if ($rec && $rec->remaining_balance > 0) {
                $deduct = min($totalReturnAmount, (float) $rec->remaining_balance);
                $newRem = max(0, (float) $rec->remaining_balance - $deduct);
                $recStatus = ($newRem <= 0.01) ? 'paid' : 'partial';

                $this->db->table('distributor_receivables')
                         ->where('id', $rec->id)
                         ->update([
                             'remaining_balance' => $newRem,
                             'status'            => $recStatus
                         ]);

                if ($cust) {
                    $newCustRec = max(0, (float) $cust->current_receivable - $deduct);
                    $this->db->table('distributor_customers')
                             ->where('id', $cust->id)
                             ->update(['current_receivable' => $newCustRec]);
                }
            }
        }

        // 4. Automated Journal Entry
        $this->journalEngine->postDistributorReturnJournal(
            $returnId,
            $returnNo,
            $totalReturnAmount,
            $totalCogsReversal,
            $refundMethod,
            $sale->invoice_no,
            $cust->name ?? 'Customer'
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses retur penjualan.');
        }

        return [
            'status'     => 'success',
            'return_no'  => $returnNo,
            'message'    => "Retur Penjualan {$returnNo} berhasil diproses dan dicatat ke jurnal."
        ];
    }

    /**
     * Inter-unit Stock Transfer (Distributor <-> Pharmacy/Resto/Clinic)
     */
    public function processStockTransfer(array $data, int $userId = 1)
    {
        $this->db->transStart();

        $sourceType = $data['source_type'] ?? 'distributor';
        $targetType = $data['target_type'] ?? 'pharmacy';
        $items = $data['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            throw new \Exception('Daftar item transfer tidak boleh kosong.');
        }

        if ($sourceType === $targetType) {
            throw new \Exception('Unit asal dan unit tujuan tidak boleh sama.');
        }

        $transferNo = $this->generateTransferNo();
        $transferDate = !empty($data['transfer_date']) ? $data['transfer_date'] : date('Y-m-d');

        // 1. Insert distributor_stock_transfers
        $this->db->table('distributor_stock_transfers')->insert([
            'transfer_no'   => $transferNo,
            'transfer_date' => $transferDate,
            'source_type'   => $sourceType,
            'target_type'   => $targetType,
            'status'        => 'completed',
            'notes'         => $data['notes'] ?? '',
            'created_by'    => $userId,
            'approved_by'   => $userId,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $transferId = $this->db->insertID();

        $totalTransferCogs = 0;

        foreach ($items as $it) {
            $medId = (int) ($it['medicine_id'] ?? 0);
            $qty = (int) ($it['qty'] ?? 0);
            $batchNo = $it['batch_no'] ?? ('BATCH-' . date('Ymd'));
            $expDate = $it['expired_date'] ?? date('Y-m-d', strtotime('+1 year'));
            $buyPrice = (float) ($it['buy_price'] ?? 0);

            if ($qty <= 0) continue;

            $totalTransferCogs += ($buyPrice * $qty);

            // Insert item record
            $this->db->table('distributor_stock_transfer_items')->insert([
                'transfer_id'  => $transferId,
                'medicine_id'  => $medId,
                'batch_no'     => $batchNo,
                'expired_date' => $expDate,
                'qty'          => $qty,
                'buy_price'    => $buyPrice,
                'notes'        => $it['notes'] ?? ''
            ]);

            // Handle Source Stock Out
            if ($sourceType === 'distributor') {
                $distStk = $this->db->table('distributor_stocks')
                                    ->where('medicine_id', $medId)
                                    ->orderBy('stock', 'DESC')
                                    ->get()->getRow();
                if (!$distStk || $distStk->stock < $qty) {
                    throw new \Exception("Stok Distributor tidak mencukupi untuk transfer item ID {$medId}.");
                }
                $stkBefore = $distStk->stock;
                $stkAfter = $stkBefore - $qty;
                $this->db->table('distributor_stocks')->where('id', $distStk->id)->update(['stock' => $stkAfter]);

                $this->db->table('distributor_stock_movements')->insert([
                    'medicine_id'          => $medId,
                    'distributor_stock_id' => $distStk->id,
                    'movement_type'        => 'transfer_out',
                    'qty'                  => $qty,
                    'stock_before'         => $stkBefore,
                    'stock_after'          => $stkAfter,
                    'unit_price'           => $buyPrice,
                    'reference_no'         => $transferNo,
                    'notes'                => "Transfer Keluar ke {$targetType} (#{$transferNo})",
                    'created_by'           => $userId,
                    'created_at'           => date('Y-m-d H:i:s')
                ]);
            }

            // Handle Target Stock In
            if ($targetType === 'distributor') {
                $distStk = $this->db->table('distributor_stocks')
                                    ->where('medicine_id', $medId)
                                    ->get()->getRow();
                if ($distStk) {
                    $stkBefore = $distStk->stock;
                    $stkAfter = $stkBefore + $qty;
                    $this->db->table('distributor_stocks')->where('id', $distStk->id)->update(['stock' => $stkAfter]);
                    $stockId = $distStk->id;
                } else {
                    $stkBefore = 0;
                    $stkAfter = $qty;
                    $medRow = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
                    $sellPrice = $medRow ? (float) $medRow->price : ($buyPrice * 1.25);
                    $this->db->table('distributor_stocks')->insert([
                        'medicine_id'   => $medId,
                        'batch_no'      => $batchNo,
                        'expired_date'  => $expDate,
                        'buy_price'     => $buyPrice,
                        'selling_price' => $sellPrice,
                        'stock'         => $qty,
                        'min_stock'     => 10
                    ]);
                    $stockId = $this->db->insertID();
                }

                $this->db->table('distributor_stock_movements')->insert([
                    'medicine_id'          => $medId,
                    'distributor_stock_id' => $stockId,
                    'movement_type'        => 'transfer_in',
                    'qty'                  => $qty,
                    'stock_before'         => $stkBefore,
                    'stock_after'          => $stkAfter,
                    'unit_price'           => $buyPrice,
                    'reference_no'         => $transferNo,
                    'notes'                => "Transfer Masuk dari {$sourceType} (#{$transferNo})",
                    'created_by'           => $userId,
                    'created_at'           => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Automated Journal
        $this->journalEngine->postDistributorStockTransferJournal(
            $transferId,
            $transferNo,
            $totalTransferCogs,
            $sourceType,
            $targetType
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \Exception('Gagal memproses transfer stok antar unit.');
        }

        return [
            'status'      => 'success',
            'transfer_no' => $transferNo,
            'message'     => "Transfer stok {$transferNo} ({$sourceType} -> {$targetType}) berhasil diproses dan dijurnal."
        ];
    }
}
