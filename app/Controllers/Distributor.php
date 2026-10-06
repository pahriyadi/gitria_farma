<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\DistributorService;
use Config\Database;

class Distributor extends BaseController
{
    protected $distributorService;
    protected $db;

    public function __construct()
    {
        $this->distributorService = new DistributorService();
        $this->db = Database::connect('default');
    }

    /**
     * Helper to get current user ID
     */
    protected function getUserId()
    {
        return (int) (session()->get('user_id') ?? 1);
    }

    /**
     * 1. Dashboard Unit Distributor
     */
    public function index()
    {
        $stats = $this->distributorService->getDashboardStats();
        
        $data = [
            'title' => 'Dashboard Distributor & Grosir',
            'stats' => $stats
        ];

        return view('distributor/dashboard', $data);
    }

    /**
     * 2. Kasir & Buat Faktur Penjualan Grosir B2B
     */
    public function penjualan()
    {
        // Get active customers
        $customers = $this->db->table('distributor_customers')
                              ->where('status', 'active')
                              ->orderBy('name', 'ASC')
                              ->get()->getResult();

        // Get medicines in distributor stock with available stock
        $stocks = $this->db->table('distributor_stocks')
                           ->select('distributor_stocks.*, medicines.code as med_code, medicines.name as med_name, medicines.type as med_type, medicines.unit as default_unit')
                           ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                           ->where('distributor_stocks.stock >', 0)
                           ->orderBy('medicines.name', 'ASC')
                           ->get()->getResult();

        $invoiceNo = $this->distributorService->generateInvoiceNo();

        $data = [
            'title'     => 'Kasir & Faktur Grosir B2B',
            'customers' => $customers,
            'stocks'    => $stocks,
            'invoiceNo' => $invoiceNo
        ];

        return view('distributor/penjualan', $data);
    }

    /**
     * AJAX/POST: Simpan Transaksi Penjualan Grosir
     */
    public function simpanPenjualan()
    {
        if (!$this->request->isAJAX() && strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        try {
            $jsonData = $this->request->getJSON(true);
            $postData = $jsonData ?: $this->request->getPost();

            $res = $this->distributorService->processSale($postData, $this->getUserId());
            return $this->response->setJSON($res);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 3. Riwayat Transaksi Penjualan Distributor
     */
    public function riwayat()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $customerId = (int) $this->request->getGet('customer_id');
        $paymentType = $this->request->getGet('payment_type');
        $status = $this->request->getGet('status');

        $builder = $this->db->table('distributor_sales')
                            ->select('distributor_sales.*, distributor_customers.name as customer_name, distributor_customers.phone as customer_phone, COALESCE(users.fullname, users.username) as cashier_name')
                            ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                            ->join('users', 'users.id = distributor_sales.created_by', 'left')
                            ->where('distributor_sales.sale_date >=', $startDate)
                            ->where('distributor_sales.sale_date <=', $endDate);

        if ($customerId > 0) {
            $builder->where('distributor_sales.customer_id', $customerId);
        }
        if (!empty($paymentType)) {
            $builder->where('distributor_sales.payment_type', $paymentType);
        }
        if (!empty($status)) {
            $builder->where('distributor_sales.payment_status', $status);
        }

        $sales = $builder->orderBy('distributor_sales.id', 'DESC')->get()->getResult();

        $customers = $this->db->table('distributor_customers')->orderBy('name', 'ASC')->get()->getResult();

        $data = [
            'title'       => 'Riwayat Penjualan Distributor',
            'sales'       => $sales,
            'customers'   => $customers,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'customerId'  => $customerId,
            'paymentType' => $paymentType,
            'status'      => $status
        ];

        return view('distributor/riwayat', $data);
    }

    /**
     * AJAX: Detail Penjualan
     */
    public function detailPenjualan($id)
    {
        $sale = $this->db->table('distributor_sales')
                         ->select('distributor_sales.*, distributor_customers.name as customer_name, distributor_customers.company_name, distributor_customers.address as customer_address, distributor_customers.phone as customer_phone, COALESCE(users.fullname, users.username) as cashier_name')
                         ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                         ->join('users', 'users.id = distributor_sales.created_by', 'left')
                         ->where('distributor_sales.id', (int) $id)
                         ->get()->getRow();

        if (!$sale) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Penjualan tidak ditemukan']);
        }

        $items = $this->db->table('distributor_sale_details')
                          ->select('distributor_sale_details.*, medicines.name as med_name, medicines.code as med_code')
                          ->join('medicines', 'medicines.id = distributor_sale_details.medicine_id')
                          ->where('distributor_sale_details.sale_id', (int) $id)
                          ->get()->getResult();

        $receivable = $this->db->table('distributor_receivables')
                               ->where('sale_id', (int) $id)
                               ->get()->getRow();

        return $this->response->setJSON([
            'status'     => 'success',
            'sale'       => $sale,
            'items'      => $items,
            'receivable' => $receivable
        ]);
    }

    /**
     * Cetak Faktur Penjualan B2B
     */
    public function cetakFaktur($id)
    {
        $sale = $this->db->table('distributor_sales')
                         ->select('distributor_sales.*, distributor_customers.name as customer_name, distributor_customers.company_name, distributor_customers.address as customer_address, distributor_customers.phone as customer_phone, distributor_customers.npwp as customer_npwp, COALESCE(users.fullname, users.username) as cashier_name')
                         ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                         ->join('users', 'users.id = distributor_sales.created_by', 'left')
                         ->where('distributor_sales.id', (int) $id)
                         ->get()->getRow();

        if (!$sale) {
            return redirect()->to(base_url('distributor/riwayat'))->with('error', 'Faktur tidak ditemukan.');
        }

        $items = $this->db->table('distributor_sale_details')
                          ->select('distributor_sale_details.*, medicines.name as med_name, medicines.code as med_code')
                          ->join('medicines', 'medicines.id = distributor_sale_details.medicine_id')
                          ->where('distributor_sale_details.sale_id', (int) $id)
                          ->get()->getResult();

        $data = [
            'sale'  => $sale,
            'items' => $items
        ];

        return view('distributor/cetak_faktur', $data);
    }

    /**
     * Cetak Surat Jalan Pengiriman
     */
    public function cetakSuratJalan($id)
    {
        $sale = $this->db->table('distributor_sales')
                         ->select('distributor_sales.*, distributor_customers.name as customer_name, distributor_customers.company_name, distributor_customers.address as customer_address, distributor_customers.phone as customer_phone, COALESCE(users.fullname, users.username) as cashier_name')
                         ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                         ->join('users', 'users.id = distributor_sales.created_by', 'left')
                         ->where('distributor_sales.id', (int) $id)
                         ->get()->getRow();

        if (!$sale) {
            return redirect()->to(base_url('distributor/riwayat'))->with('error', 'Data tidak ditemukan.');
        }

        $items = $this->db->table('distributor_sale_details')
                          ->select('distributor_sale_details.*, medicines.name as med_name, medicines.code as med_code')
                          ->join('medicines', 'medicines.id = distributor_sale_details.medicine_id')
                          ->where('distributor_sale_details.sale_id', (int) $id)
                          ->get()->getResult();

        $data = [
            'sale'  => $sale,
            'items' => $items
        ];

        return view('distributor/cetak_surat_jalan', $data);
    }

    /**
     * 4. Manajemen Customer / Pelanggan B2B
     */
    public function pelanggan()
    {
        $customers = $this->db->table('distributor_customers')
                              ->orderBy('id', 'DESC')
                              ->get()->getResult();

        $data = [
            'title'     => 'Database Pelanggan / Customer B2B',
            'customers' => $customers
        ];

        return view('distributor/pelanggan', $data);
    }

    /**
     * Simpan / Update Pelanggan
     */
    public function simpanPelanggan()
    {
        $id = (int) $this->request->getPost('id');
        $code = trim($this->request->getPost('code') ?: ('CUST-' . date('ymd') . rand(10,99)));
        $name = trim($this->request->getPost('name'));
        $companyName = trim($this->request->getPost('company_name') ?: '');
        $phone = trim($this->request->getPost('phone'));
        $email = trim($this->request->getPost('email') ?: '');
        $address = trim($this->request->getPost('address') ?: '');
        $npwp = trim($this->request->getPost('npwp') ?: '');
        $creditLimit = (float) $this->request->getPost('credit_limit');
        $paymentTerms = (int) $this->request->getPost('payment_terms_days');
        $status = $this->request->getPost('status') ?: 'active';

        if (empty($name) || empty($phone)) {
            return redirect()->back()->with('error', 'Nama dan No. Telepon customer wajib diisi.');
        }

        $payload = [
            'code'               => $code,
            'name'               => $name,
            'company_name'       => $companyName,
            'phone'              => $phone,
            'email'              => $email,
            'address'            => $address,
            'npwp'               => $npwp,
            'credit_limit'       => $creditLimit,
            'payment_terms_days' => $paymentTerms,
            'status'             => $status
        ];

        if ($id > 0) {
            $this->db->table('distributor_customers')->where('id', $id)->update($payload);
            session()->setFlashdata('success', "Data customer '{$name}' berhasil diperbarui.");
        } else {
            $this->db->table('distributor_customers')->insert($payload);
            session()->setFlashdata('success', "Customer baru '{$name}' berhasil ditambahkan.");
        }

        return redirect()->to(base_url('distributor/pelanggan'));
    }

    /**
     * 5. Manajemen Stok Gudang Distributor & Kartu Stok
     */
    public function stok()
    {
        $stocks = $this->db->table('distributor_stocks')
                           ->select('distributor_stocks.*, medicines.code as med_code, medicines.name as med_name, medicines.type as med_type, medicines.unit as default_unit')
                           ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                           ->orderBy('medicines.name', 'ASC')
                           ->get()->getResult();

        $medicines = $this->db->table('medicines')
                              ->where('status', 'active')
                              ->orderBy('name', 'ASC')
                              ->get()->getResult();

        $data = [
            'title'     => 'Stok Gudang Distributor & Kartu Stok',
            'stocks'    => $stocks,
            'medicines' => $medicines
        ];

        return view('distributor/stok', $data);
    }

    /**
     * Simpan / Tambah Stok Distributor
     */
    public function simpanStok()
    {
        $id = (int) $this->request->getPost('id');
        $medId = (int) $this->request->getPost('medicine_id');
        $batchNo = trim($this->request->getPost('batch_no') ?: ('DIST-' . date('Ymd')));
        $expDate = $this->request->getPost('expired_date') ?: date('Y-m-d', strtotime('+1 year'));
        $buyPrice = (float) $this->request->getPost('buy_price');
        $sellPrice = (float) $this->request->getPost('selling_price');
        $qty = (int) $this->request->getPost('stock');
        $minStock = (int) ($this->request->getPost('min_stock') ?: 10);
        $notes = trim($this->request->getPost('notes') ?: 'Penambahan Stok Manual Gudang Distributor');

        if ($medId <= 0 || $qty <= 0) {
            return redirect()->back()->with('error', 'Pilih obat dan masukkan jumlah stok yang valid.');
        }

        $medRow = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
        $medName = $medRow ? $medRow->name : 'Obat';

        if ($id > 0) {
            $cur = $this->db->table('distributor_stocks')->where('id', $id)->get()->getRow();
            $stkBefore = $cur ? $cur->stock : 0;
            $newStock = $stkBefore + $qty;

            $this->db->table('distributor_stocks')->where('id', $id)->update([
                'batch_no'      => $batchNo,
                'expired_date'  => $expDate,
                'buy_price'     => $buyPrice,
                'selling_price' => $sellPrice,
                'stock'         => $newStock,
                'min_stock'     => $minStock
            ]);
            $stockId = $id;
        } else {
            $stkBefore = 0;
            $newStock = $qty;

            $this->db->table('distributor_stocks')->insert([
                'medicine_id'   => $medId,
                'batch_no'      => $batchNo,
                'expired_date'  => $expDate,
                'buy_price'     => $buyPrice,
                'selling_price' => $sellPrice,
                'stock'         => $qty,
                'min_stock'     => $minStock
            ]);
            $stockId = $this->db->insertID();
        }

        // Log stock movement
        $this->db->table('distributor_stock_movements')->insert([
            'medicine_id'          => $medId,
            'distributor_stock_id' => $stockId,
            'movement_type'        => 'in',
            'qty'                  => $qty,
            'stock_before'         => $stkBefore,
            'stock_after'          => $newStock,
            'unit_price'           => $buyPrice,
            'reference_no'         => 'INBOUND-MANUAL',
            'notes'                => $notes,
            'created_by'           => $this->getUserId(),
            'created_at'           => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', "Stok obat '{$medName}' berhasil ditambahkan ke gudang distributor.");
        return redirect()->to(base_url('distributor/stok'));
    }

    /**
     * AJAX: Kartu Stok Mutasi Item
     */
    public function kartuStok($stockId)
    {
        $stock = $this->db->table('distributor_stocks')
                          ->select('distributor_stocks.*, medicines.name as med_name, medicines.code as med_code')
                          ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                          ->where('distributor_stocks.id', (int) $stockId)
                          ->get()->getRow();

        if (!$stock) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Stok tidak ditemukan']);
        }

        $movements = $this->db->table('distributor_stock_movements')
                              ->select('distributor_stock_movements.*, COALESCE(users.fullname, users.username) as user_name')
                              ->join('users', 'users.id = distributor_stock_movements.created_by', 'left')
                              ->where('distributor_stock_movements.distributor_stock_id', (int) $stockId)
                              ->orderBy('distributor_stock_movements.id', 'DESC')
                              ->get()->getResult();

        return $this->response->setJSON([
            'status'    => 'success',
            'stock'     => $stock,
            'movements' => $movements
        ]);
    }

    /**
     * 6. Transfer Stok Antar-Unit Bisnis
     */
    public function transfer()
    {
        $transfers = $this->db->table('distributor_stock_transfers')
                              ->select('distributor_stock_transfers.*, COALESCE(u1.fullname, u1.username) as creator_name')
                              ->join('users u1', 'u1.id = distributor_stock_transfers.created_by', 'left')
                              ->orderBy('distributor_stock_transfers.id', 'DESC')
                              ->get()->getResult();

        $medicines = $this->db->table('medicines')
                              ->where('status', 'active')
                              ->orderBy('name', 'ASC')
                              ->get()->getResult();

        $distStocks = $this->db->table('distributor_stocks')
                               ->select('distributor_stocks.*, medicines.name as med_name, medicines.code as med_code')
                               ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                               ->where('distributor_stocks.stock >', 0)
                               ->get()->getResult();

        $data = [
            'title'      => 'Transfer Stok Antar-Unit Bisnis',
            'transfers'  => $transfers,
            'medicines'  => $medicines,
            'distStocks' => $distStocks
        ];

        return view('distributor/transfer', $data);
    }

    /**
     * Simpan Transfer Stok Antar Unit
     */
    public function simpanTransfer()
    {
        try {
            $jsonData = $this->request->getJSON(true);
            $postData = $jsonData ?: $this->request->getPost();

            $res = $this->distributorService->processStockTransfer($postData, $this->getUserId());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON($res);
            }
            session()->setFlashdata('success', $res['message']);
            return redirect()->to(base_url('distributor/transfer'));
        } catch (\Throwable $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * 7. Monitoring Piutang & Pelunasan
     */
    public function piutang()
    {
        $status = $this->request->getGet('status');
        $customerId = (int) $this->request->getGet('customer_id');
        $aging = $this->request->getGet('aging'); // 0-30, 31-60, 61-90, >90, overdue

        $builder = $this->db->table('distributor_receivables')
                            ->select('distributor_receivables.*, distributor_customers.name as customer_name, distributor_customers.company_name, distributor_customers.phone as customer_phone, distributor_sales.payment_type')
                            ->join('distributor_customers', 'distributor_customers.id = distributor_receivables.customer_id')
                            ->join('distributor_sales', 'distributor_sales.id = distributor_receivables.sale_id');

        if (!empty($status)) {
            $builder->where('distributor_receivables.status', $status);
        }
        if ($customerId > 0) {
            $builder->where('distributor_receivables.customer_id', $customerId);
        }

        $receivables = $builder->orderBy('distributor_receivables.due_date', 'ASC')->get()->getResult();

        $today = date('Y-m-d');
        // Filter by aging in memory if selected
        if (!empty($aging)) {
            $receivables = array_filter($receivables, function($r) use ($aging, $today) {
                $days = (strtotime($today) - strtotime($r->invoice_date)) / (60 * 60 * 24);
                $isOverdue = strtotime($r->due_date) < strtotime($today) && $r->status !== 'paid';

                if ($aging === 'overdue') return $isOverdue;
                if ($aging === '0-30') return $days <= 30;
                if ($aging === '31-60') return $days > 30 && $days <= 60;
                if ($aging === '61-90') return $days > 60 && $days <= 90;
                if ($aging === '>90') return $days > 90;
                return true;
            });
        }

        $customers = $this->db->table('distributor_customers')->orderBy('name', 'ASC')->get()->getResult();

        $data = [
            'title'       => 'Monitoring Piutang & Jatuh Tempo',
            'receivables' => $receivables,
            'customers'   => $customers,
            'status'      => $status,
            'customerId'  => $customerId,
            'aging'       => $aging
        ];

        return view('distributor/piutang', $data);
    }

    /**
     * AJAX: Bayar / Lunasi Piutang
     */
    public function bayarPiutang()
    {
        try {
            $jsonData = $this->request->getJSON(true);
            $postData = $jsonData ?: $this->request->getPost();

            $res = $this->distributorService->processPayment($postData, $this->getUserId());
            return $this->response->setJSON($res);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Riwayat Pembayaran Suatu Piutang
     */
    public function riwayatPembayaran($receivableId)
    {
        $payments = $this->db->table('distributor_payments')
                             ->select('distributor_payments.*, COALESCE(users.fullname, users.username) as cashier_name')
                             ->join('users', 'users.id = distributor_payments.created_by', 'left')
                             ->where('distributor_payments.receivable_id', (int) $receivableId)
                             ->orderBy('distributor_payments.id', 'DESC')
                             ->get()->getResult();

        return $this->response->setJSON([
            'status'   => 'success',
            'payments' => $payments
        ]);
    }

    /**
     * 8. Retur Penjualan Grosir
     */
    public function retur()
    {
        $returns = $this->db->table('distributor_returns')
                            ->select('distributor_returns.*, distributor_customers.name as customer_name, distributor_sales.invoice_no, COALESCE(users.fullname, users.username) as creator_name')
                            ->join('distributor_customers', 'distributor_customers.id = distributor_returns.customer_id')
                            ->join('distributor_sales', 'distributor_sales.id = distributor_returns.sale_id')
                            ->join('users', 'users.id = distributor_returns.created_by', 'left')
                            ->orderBy('distributor_returns.id', 'DESC')
                            ->get()->getResult();

        $recentSales = $this->db->table('distributor_sales')
                                ->select('distributor_sales.*, distributor_customers.name as customer_name')
                                ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                                ->orderBy('distributor_sales.id', 'DESC')
                                ->limit(50)
                                ->get()->getResult();

        $data = [
            'title'       => 'Retur Penjualan Distributor',
            'returns'     => $returns,
            'recentSales' => $recentSales
        ];

        return view('distributor/retur', $data);
    }

    /**
     * AJAX: Simpan Retur Penjualan
     */
    public function simpanRetur()
    {
        try {
            $jsonData = $this->request->getJSON(true);
            $postData = $jsonData ?: $this->request->getPost();

            $res = $this->distributorService->processReturn($postData, $this->getUserId());
            return $this->response->setJSON($res);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 9. Laporan Keuangan & Operasional Distributor
     */
    public function laporan()
    {
        $tab = $this->request->getGet('tab') ?: 'penjualan';
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        // Tab 1: Penjualan
        $salesSummary = $this->db->table('distributor_sales')
                                 ->select('SUM(subtotal) as total_subtotal, SUM(discount_amount) as total_discount, SUM(tax_amount) as total_tax, SUM(total_amount) as total_gross, SUM(paid_amount) as total_paid, SUM(remaining_amount) as total_unpaid, COUNT(*) as total_tx')
                                 ->where('sale_date >=', $startDate)
                                 ->where('sale_date <=', $endDate)
                                 ->get()->getRow();

        $salesByCustomer = $this->db->table('distributor_sales')
                                    ->select('distributor_customers.name as customer_name, COUNT(distributor_sales.id) as total_tx, SUM(distributor_sales.total_amount) as total_amount, SUM(distributor_sales.paid_amount) as total_paid, SUM(distributor_sales.remaining_amount) as total_remaining')
                                    ->join('distributor_customers', 'distributor_customers.id = distributor_sales.customer_id')
                                    ->where('distributor_sales.sale_date >=', $startDate)
                                    ->where('distributor_sales.sale_date <=', $endDate)
                                    ->groupBy('distributor_sales.customer_id')
                                    ->orderBy('total_amount', 'DESC')
                                    ->get()->getResult();

        $salesByMedicine = $this->db->table('distributor_sale_details')
                                    ->select('medicines.name as med_name, medicines.code as med_code, SUM(distributor_sale_details.qty) as total_qty, SUM(distributor_sale_details.subtotal) as total_sales_val, SUM(distributor_sale_details.cogs_total) as total_cogs_val')
                                    ->join('medicines', 'medicines.id = distributor_sale_details.medicine_id')
                                    ->join('distributor_sales', 'distributor_sales.id = distributor_sale_details.sale_id')
                                    ->where('distributor_sales.sale_date >=', $startDate)
                                    ->where('distributor_sales.sale_date <=', $endDate)
                                    ->groupBy('distributor_sale_details.medicine_id')
                                    ->orderBy('total_sales_val', 'DESC')
                                    ->get()->getResult();

        // Tab 2: Piutang & Aging Schedule
        $receivablesList = $this->db->table('distributor_receivables')
                                    ->select('distributor_receivables.*, distributor_customers.name as customer_name')
                                    ->join('distributor_customers', 'distributor_customers.id = distributor_receivables.customer_id')
                                    ->where('distributor_receivables.status !=', 'paid')
                                    ->orderBy('distributor_receivables.due_date', 'ASC')
                                    ->get()->getResult();

        // Tab 3: Stok & Mutasi
        $stocksList = $this->db->table('distributor_stocks')
                               ->select('distributor_stocks.*, medicines.name as med_name, medicines.code as med_code, medicines.unit as default_unit')
                               ->join('medicines', 'medicines.id = distributor_stocks.medicine_id')
                               ->orderBy('distributor_stocks.stock', 'DESC')
                               ->get()->getResult();

        // Tab 4: Laba Rugi (P&L) Distributor
        $totalSalesVal = (float) ($salesSummary->total_gross ?? 0);
        $totalCogsVal = $this->db->table('distributor_sale_details')
                                 ->selectSum('cogs_total')
                                 ->join('distributor_sales', 'distributor_sales.id = distributor_sale_details.sale_id')
                                 ->where('distributor_sales.sale_date >=', $startDate)
                                 ->where('distributor_sales.sale_date <=', $endDate)
                                 ->get()->getRow()->cogs_total ?? 0;

        $totalReturnsVal = $this->db->table('distributor_returns')
                                    ->selectSum('total_return_amount')
                                    ->where('return_date >=', $startDate)
                                    ->where('return_date <=', $endDate)
                                    ->get()->getRow()->total_return_amount ?? 0;

        $netRevenue = $totalSalesVal - (float)$totalReturnsVal;
        $grossProfit = $netRevenue - (float)$totalCogsVal;
        $grossMarginPct = $netRevenue > 0 ? (($grossProfit / $netRevenue) * 100) : 0;

        $data = [
            'title'           => 'Laporan Keuangan & Penjualan Distributor',
            'tab'             => $tab,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'salesSummary'    => $salesSummary,
            'salesByCustomer' => $salesByCustomer,
            'salesByMedicine' => $salesByMedicine,
            'receivablesList' => $receivablesList,
            'stocksList'      => $stocksList,
            'totalSalesVal'   => $totalSalesVal,
            'totalReturnsVal' => (float)$totalReturnsVal,
            'netRevenue'      => $netRevenue,
            'totalCogsVal'    => (float)$totalCogsVal,
            'grossProfit'     => $grossProfit,
            'grossMarginPct'  => $grossMarginPct
        ];

        return view('distributor/laporan', $data);
    }

    /**
     * 10. Buku Kas & Arus Kas Mandiri Distributor
     */
    public function kas()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $accountType = $this->request->getGet('account_type') ?: 'all';

        // Get Distributor Accounts
        $accKasTunai = $this->db->table('accounts')->where('code', '1131')->get()->getRow();
        $accKasBank = $this->db->table('accounts')->where('code', '1132')->get()->getRow();

        $saldoKasTunai = (float) ($accKasTunai->balance ?? 0);
        $saldoKasBank = (float) ($accKasBank->balance ?? 0);
        $totalSaldo = $saldoKasTunai + $saldoKasBank;

        // Query journal entries involving distributor cash accounts
        $distributorAccountIds = [];
        if ($accKasTunai) $distributorAccountIds[] = (int) $accKasTunai->id;
        if ($accKasBank) $distributorAccountIds[] = (int) $accKasBank->id;

        $builder = $this->db->table('journal_entry_details')
                            ->select('journal_entry_details.*, journal_entries.journal_no, journal_entries.entry_date, journal_entries.source_module, journal_entries.description, accounts.code as acc_code, accounts.name as acc_name')
                            ->join('journal_entries', 'journal_entries.id = journal_entry_details.journal_id')
                            ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                            ->where('journal_entries.entry_date >=', $startDate)
                            ->where('journal_entries.entry_date <=', $endDate);

        if (!empty($distributorAccountIds)) {
            if ($accountType === 'cash' && $accKasTunai) {
                $builder->where('journal_entry_details.account_id', $accKasTunai->id);
            } elseif ($accountType === 'bank' && $accKasBank) {
                $builder->where('journal_entry_details.account_id', $accKasBank->id);
            } else {
                $builder->whereIn('journal_entry_details.account_id', $distributorAccountIds);
            }
        }

        $mutasiList = $builder->orderBy('journal_entries.entry_date', 'DESC')
                              ->orderBy('journal_entries.id', 'DESC')
                              ->get()->getResult();

        // Calculate in / out totals
        $totalMasuk = 0;
        $totalKeluar = 0;
        foreach ($mutasiList as $m) {
            $totalMasuk += (float) $m->debit;
            $totalKeluar += (float) $m->credit;
        }

        // Available expense accounts for Kas Keluar
        $expenseAccounts = $this->db->table('accounts')
                                    ->where('type', 'Expense')
                                    ->orderBy('code', 'ASC')
                                    ->get()->getResult();

        // Available revenue/other accounts for Kas Masuk
        $revenueAccounts = $this->db->table('accounts')
                                    ->whereIn('type', ['Revenue', 'Equity'])
                                    ->orderBy('code', 'ASC')
                                    ->get()->getResult();

        $data = [
            'title'           => 'Buku Kas Mandiri Distributor',
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'accountType'     => $accountType,
            'saldoKasTunai'   => $saldoKasTunai,
            'saldoKasBank'    => $saldoKasBank,
            'totalSaldo'      => $totalSaldo,
            'totalMasuk'      => $totalMasuk,
            'totalKeluar'     => $totalKeluar,
            'mutasiList'      => $mutasiList,
            'expenseAccounts' => $expenseAccounts,
            'revenueAccounts' => $revenueAccounts
        ];

        return view('distributor/kas', $data);
    }

    /**
     * AJAX/POST: Simpan Transaksi Kas Masuk / Kas Keluar Distributor
     */
    public function simpanKas()
    {
        if (!$this->request->isAJAX() && strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        try {
            $type = $this->request->getPost('type'); // in / out
            $accountTarget = $this->request->getPost('account_target'); // 1131 (tunai) / 1132 (bank)
            $opposingAccountId = (int) $this->request->getPost('opposing_account_id');
            $amount = (float) $this->request->getPost('amount');
            $date = $this->request->getPost('transaction_date') ?: date('Y-m-d');
            $description = trim($this->request->getPost('description') ?? '');
            $userId = $this->getUserId();

            if ($amount <= 0) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nominal transaksi harus lebih besar dari 0']);
            }
            if (!$opposingAccountId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Silakan pilih akun lawan transaksi']);
            }

            $this->db->transStart();

            $distKasAcc = $this->db->table('accounts')->where('code', $accountTarget)->get()->getRow();
            if (!$distKasAcc) {
                $distKasAcc = $this->db->table('accounts')->where('code', '113')->get()->getRow();
            }
            $oppAcc = $this->db->table('accounts')->where('id', $opposingAccountId)->get()->getRow();

            if (!$distKasAcc || !$oppAcc) {
                throw new \Exception('Akun COA tidak valid');
            }

            $journalEngine = new \App\Services\JournalEngine();
            $journalNo = $journalEngine->generateJournalNo();
            $sourceModule = 'Kas ' . ($type === 'in' ? 'Masuk' : 'Keluar') . ' Distributor';

            $this->db->table('journal_entries')->insert([
                'journal_no'    => $journalNo,
                'entry_date'    => $date,
                'source_module' => $sourceModule,
                'reference_id'  => 0,
                'description'   => "Kas " . ($type === 'in' ? 'Masuk' : 'Keluar') . " Distributor: " . $description,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $journalId = $this->db->insertID();

            if ($type === 'in') {
                // Debit: Kas Distributor, Kredit: Akun Lawan
                $this->db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $distKasAcc->id,
                    'debit'      => $amount,
                    'credit'     => 0.00
                ]);
                $this->db->table('accounts')->where('id', $distKasAcc->id)->update([
                    'balance' => (float)$distKasAcc->balance + $amount
                ]);

                $this->db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $oppAcc->id,
                    'debit'      => 0.00,
                    'credit'     => $amount
                ]);
                $this->db->table('accounts')->where('id', $oppAcc->id)->update([
                    'balance' => (float)$oppAcc->balance + ($oppAcc->normal_balance === 'credit' ? $amount : -$amount)
                ]);
            } else {
                // Kas Keluar: Debit Akun Lawan (Beban), Kredit Kas Distributor
                $this->db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $oppAcc->id,
                    'debit'      => $amount,
                    'credit'     => 0.00
                ]);
                $this->db->table('accounts')->where('id', $oppAcc->id)->update([
                    'balance' => (float)$oppAcc->balance + ($oppAcc->normal_balance === 'debit' ? $amount : -$amount)
                ]);

                $this->db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $distKasAcc->id,
                    'debit'      => 0.00,
                    'credit'     => $amount
                ]);
                $this->db->table('accounts')->where('id', $distKasAcc->id)->update([
                    'balance' => (float)$distKasAcc->balance - $amount
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal memproses transaksi kas.');
            }

            return $this->response->setJSON([
                'status'     => 'success',
                'journal_no' => $journalNo,
                'message'    => 'Transaksi kas distributor berhasil disimpan dan dijurnal otomatis.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 11. Jurnal Transaksi Khusus Unit Distributor
     */
    public function jurnal()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $search = trim($this->request->getGet('search') ?? '');

        $distributorModules = [
            'Distributor & Grosir',
            'Pelunasan Piutang Distributor',
            'Retur Penjualan Distributor',
            'HPP Distributor',
            'Kas Masuk Distributor',
            'Kas Keluar Distributor'
        ];

        $builder = $this->db->table('journal_entries')
                            ->whereIn('source_module', $distributorModules)
                            ->where('entry_date >=', $startDate)
                            ->where('entry_date <=', $endDate);

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('journal_no', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
        }

        $journals = $builder->orderBy('entry_date', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->get()->getResult();

        // Get details for all listed journals
        $journalIds = array_map(function($j) { return $j->id; }, $journals);
        $detailsByJournal = [];

        if (!empty($journalIds)) {
            $details = $this->db->table('journal_entry_details')
                                ->select('journal_entry_details.*, accounts.code as acc_code, accounts.name as acc_name')
                                ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                                ->whereIn('journal_entry_details.journal_id', $journalIds)
                                ->orderBy('journal_entry_details.id', 'ASC')
                                ->get()->getResult();

            foreach ($details as $d) {
                $detailsByJournal[$d->journal_id][] = $d;
            }
        }

        $data = [
            'title'            => 'Jurnal Umum Transaksi Distributor',
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'search'           => $search,
            'journals'         => $journals,
            'detailsByJournal' => $detailsByJournal
        ];

        return view('distributor/jurnal', $data);
    }
}
