<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ApprovalEngine;

class Procurement extends BaseController
{
    protected $approvalEngine;

    public function __construct()
    {
        $this->approvalEngine = new ApprovalEngine();
    }

    /**
     * Dashboard Pengadaan, Purchase Requests, Purchase Orders & Penerimaan Barang
     */
    public function po()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            // 1. AJUKAN PURCHASE REQUISITION (PR) DENGAN RINCIAN ITEM
            if ($action === 'create_pr') {
                $db->transStart();

                $supplierId = intval($this->request->getPost('supplier_id'));
                $department = $this->request->getPost('department') ?: 'Apotek Farmasi';
                $desc       = trim($this->request->getPost('description') ?: 'Pengajuan Pengadaan Barang');
                $itemNames  = $this->request->getPost('item_name') ?: [];
                $itemTypes  = $this->request->getPost('item_type') ?: [];
                $itemIds    = $this->request->getPost('item_id') ?: [];
                $qtys       = $this->request->getPost('qty') ?: [];
                $units      = $this->request->getPost('unit') ?: [];
                $prices     = $this->request->getPost('price') ?: [];

                // Generate Request No (PR-YYYYMMDD-XXXX)
                $today = date('Ymd');
                $lastPR = $db->table('purchase_requests')
                             ->where('DATE(created_at)', date('Y-m-d'))
                             ->orderBy('id', 'DESC')
                             ->limit(1)
                             ->get()
                             ->getRow();
                $nextNum = 1;
                if ($lastPR && preg_match('/PR-\d+-(\d+)/', $lastPR->request_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
                $requestNo = 'PR-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                // Calculate Total Amount from Items
                $totalAmount = 0;
                $itemsToInsert = [];
                for ($i = 0; $i < count($itemNames); $i++) {
                    $name = trim($itemNames[$i]);
                    if (empty($name)) continue;

                    $qty   = max(1, intval($qtys[$i] ?? 1));
                    $price = max(0, floatval($prices[$i] ?? 0));
                    $sub   = $qty * $price;
                    $totalAmount += $sub;

                    $itemsToInsert[] = [
                        'item_type'       => $itemTypes[$i] ?? 'obat',
                        'item_id'         => !empty($itemIds[$i]) ? intval($itemIds[$i]) : null,
                        'item_name'       => $name,
                        'qty'             => $qty,
                        'unit'            => $units[$i] ?? 'Pcs',
                        'estimated_price' => $price,
                        'subtotal'        => $sub,
                        'notes'           => null
                    ];
                }

                // If no dynamic items submitted, fallback to manual amount
                if ($totalAmount <= 0) {
                    $totalAmount = floatval($this->request->getPost('amount'));
                }

                // Insert Purchase Request Header
                $db->table('purchase_requests')->insert([
                    'request_no'   => $requestNo,
                    'supplier_id'  => $supplierId,
                    'department'   => $department,
                    'requested_by' => session('user_id') ?: 1,
                    'status'       => 'submitted',
                    'total_amount' => $totalAmount,
                    'description'  => $desc,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
                $prId = $db->insertID();

                // Insert Items
                foreach ($itemsToInsert as $item) {
                    $item['purchase_request_id'] = $prId;
                    $db->table('purchase_request_items')->insert($item);
                }

                // Trigger Approval Workflow
                $this->approvalEngine->submitRequest('PROCUREMENT', $prId, session('user_id') ?: 1);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    session()->setFlashdata('error', 'Gagal mengajukan Purchase Requisition.');
                } else {
                    session()->setFlashdata('success', 'Purchase Requisition berhasil diajukan dengan total Rp ' . number_format($totalAmount, 0, ',', '.') . '. No: ' . $requestNo);
                }
                return redirect()->to(base_url('procurement/po'));
            }

            // 2. PENERIMAAN BARANG (GOODS RECEIPT / GRN) & UPDATE STOK APOTEK + JURNAL
            if ($action === 'receive_goods') {
                $db->transStart();

                $poId            = intval($this->request->getPost('po_id'));
                $deliveryOrderNo = trim($this->request->getPost('delivery_order_no') ?: '');
                $receivedDate    = $this->request->getPost('received_date') ?: date('Y-m-d');
                $notes           = trim($this->request->getPost('notes') ?: '');

                $medicineIds = $this->request->getPost('medicine_id') ?: [];
                $itemNames   = $this->request->getPost('item_name') ?: [];
                $batchNos    = $this->request->getPost('batch_no') ?: [];
                $expDates    = $this->request->getPost('expired_date') ?: [];
                $qtys        = $this->request->getPost('qty') ?: [];
                $units       = $this->request->getPost('unit') ?: [];
                $buyPrices   = $this->request->getPost('buy_price') ?: [];

                // Generate Receipt No (GR-YYYYMMDD-XXXX)
                $today = date('Ymd');
                $lastGR = $db->table('goods_receipts')
                             ->where('DATE(created_at)', date('Y-m-d'))
                             ->orderBy('id', 'DESC')
                             ->limit(1)
                             ->get()
                             ->getRow();
                $nextNum = 1;
                if ($lastGR && preg_match('/GR-\d+-(\d+)/', $lastGR->receipt_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
                $receiptNo = 'GR-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                $totalReceiptValue = 0;
                $grItemsData = [];

                for ($i = 0; $i < count($medicineIds); $i++) {
                    $medId    = intval($medicineIds[$i]);
                    $name     = trim($itemNames[$i] ?? '');
                    $batch    = trim($batchNos[$i] ?? 'BATCH-' . date('Ymd'));
                    $exp      = $expDates[$i] ?? date('Y-m-d', strtotime('+1 year'));
                    $qty      = max(1, intval($qtys[$i] ?? 1));
                    $unit     = $units[$i] ?? 'Pcs';
                    $buyPrice = max(0, floatval($buyPrices[$i] ?? 0));
                    $sub      = $qty * $buyPrice;
                    $totalReceiptValue += $sub;

                    // If medicine name is empty, fetch from medicines table
                    if (empty($name) && $medId > 0) {
                        $mRow = $db->table('medicines')->where('id', $medId)->get()->getRow();
                        if ($mRow) $name = $mRow->name;
                    }

                    $grItemsData[] = [
                        'medicine_id'  => $medId ?: null,
                        'item_name'    => $name ?: 'Obat / Barang Medis',
                        'batch_no'     => $batch,
                        'expired_date' => $exp,
                        'qty_received' => $qty,
                        'unit'         => $unit,
                        'buy_price'    => $buyPrice,
                        'subtotal'     => $sub
                    ];
                }

                // If single item fallback form was used
                if (empty($grItemsData)) {
                    $singleMedId    = intval($this->request->getPost('single_medicine_id'));
                    $singleBatchNo  = trim($this->request->getPost('single_batch_no') ?: 'BATCH-' . date('Ymd'));
                    $singleExpDate  = $this->request->getPost('single_expired_date') ?: date('Y-m-d', strtotime('+1 year'));
                    $singleQty      = max(1, intval($this->request->getPost('single_qty') ?: 1));
                    $singleBuyPrice = floatval($this->request->getPost('single_buy_price') ?: 0);
                    $singleSub      = $singleQty * $singleBuyPrice;
                    $totalReceiptValue = $singleSub;

                    $mRow = $db->table('medicines')->where('id', $singleMedId)->get()->getRow();

                    $grItemsData[] = [
                        'medicine_id'  => $singleMedId ?: null,
                        'item_name'    => $mRow ? $mRow->name : 'Obat Medis',
                        'batch_no'     => $singleBatchNo,
                        'expired_date' => $singleExpDate,
                        'qty_received' => $singleQty,
                        'unit'         => $mRow ? ($mRow->unit ?? 'Pcs') : 'Pcs',
                        'buy_price'    => $singleBuyPrice,
                        'subtotal'     => $singleSub
                    ];
                }

                // Insert Goods Receipt Header
                $db->table('goods_receipts')->insert([
                    'receipt_no'        => $receiptNo,
                    'purchase_order_id' => $poId,
                    'delivery_order_no' => $deliveryOrderNo,
                    'received_date'     => $receivedDate,
                    'receiver_id'       => session('user_id') ?: 1,
                    'total_amount'      => $totalReceiptValue,
                    'notes'             => $notes,
                    'created_at'        => date('Y-m-d H:i:s')
                ]);
                $grId = $db->insertID();

                // Process Items: Create Batches & Stock Movements
                foreach ($grItemsData as $item) {
                    $item['goods_receipt_id'] = $grId;
                    $batchId = null;

                    if ($item['medicine_id']) {
                        // Insert batch
                        $db->table('medicine_batches')->insert([
                            'medicine_id'  => $item['medicine_id'],
                            'batch_no'     => $item['batch_no'],
                            'buy_price'    => $item['buy_price'],
                            'stock'        => $item['qty_received'],
                            'expired_date' => $item['expired_date'],
                            'created_at'   => date('Y-m-d H:i:s')
                        ]);
                        $batchId = $db->insertID();

                        // Add Stock Movement
                        $db->table('stock_movements')->insert([
                            'medicine_id'      => $item['medicine_id'],
                            'batch_id'         => $batchId,
                            'transaction_type' => 'pembelian',
                            'reference_id'     => $grId,
                            'qty_in'           => $item['qty_received'],
                            'qty_out'          => 0,
                            'balance'          => $item['qty_received'],
                            'user_id'          => session('user_id') ?: 1,
                            'created_at'       => date('Y-m-d H:i:s')
                        ]);

                        // Add to Warehouse Stock (Gudang Induk Logistik)
                        $mainWh = $db->table('pharmacy_warehouses')->where('code', 'GD-INDUK')->orWhere('type', 'main')->get()->getRow();
                        $mainWhId = $mainWh ? $mainWh->id : 1;
                        $db->table('warehouse_stock')->insert([
                            'warehouse_id' => $mainWhId,
                            'medicine_id'  => $item['medicine_id'],
                            'batch_id'     => $batchId,
                            'stock'        => $item['qty_received']
                        ]);
                    }

                    $item['batch_id'] = $batchId;
                    $db->table('goods_receipt_items')->insert($item);
                }

                // Update PO Status to received
                $db->table('purchase_orders')->where('id', $poId)->update(['status' => 'received']);

                // Find PO & update linked PR to completed
                $poRow = $db->table('purchase_orders')->where('id', $poId)->get()->getRow();
                if ($poRow && $poRow->purchase_request_id) {
                    $db->table('purchase_requests')->where('id', $poRow->purchase_request_id)->update(['status' => 'completed']);
                }

                // Auto-Journal for Purchase on Credit (AP / Hutang Usaha):
                // Debit: 1-201 (Persediaan Obat / Barang), Credit: 2-101 (Hutang Usaha Supplier)
                if ($totalReceiptValue > 0) {
                    $invAcc = $db->table('accounts')->where('code', '1-201')->get()->getRow();
                    $apAcc  = $db->table('accounts')->where('code', '2-101')->get()->getRow();

                    $invAccId = $invAcc ? $invAcc->id : 4;
                    $apAccId  = $apAcc  ? $apAcc->id  : 6;

                    // Generate Journal Voucher Number
                    $lastJournal = $db->table('journal_entries')
                                      ->where('DATE(created_at)', date('Y-m-d'))
                                      ->orderBy('id', 'DESC')
                                      ->limit(1)
                                      ->get()
                                      ->getRow();
                    $nextJNum = 1;
                    if ($lastJournal && preg_match('/JV-\d+-(\d+)/', $lastJournal->journal_no, $matches)) {
                        $nextJNum = intval($matches[1]) + 1;
                    }
                    $journalNo = 'JV-' . $today . '-' . str_pad($nextJNum, 4, '0', STR_PAD_LEFT);

                    $db->table('journal_entries')->insert([
                        'journal_no'    => $journalNo,
                        'entry_date'    => $receivedDate,
                        'source_module' => 'Pengadaan Barang',
                        'reference_id'  => $grId,
                        'description'   => "Penerimaan Barang " . ($poRow ? $poRow->po_no : 'PO #' . $poId) . " - " . $receiptNo . " (" . ($deliveryOrderNo ? 'SJ: ' . $deliveryOrderNo : '') . ")",
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    $jId = $db->insertID();

                    // Debit Persediaan
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $jId,
                        'account_id' => $invAccId,
                        'debit'      => $totalReceiptValue,
                        'credit'     => 0.00
                    ]);
                    if ($invAcc) {
                        $db->table('accounts')->where('id', $invAccId)->update(['balance' => $invAcc->balance + $totalReceiptValue]);
                    }

                    // Credit Hutang Usaha
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $jId,
                        'account_id' => $apAccId,
                        'debit'      => 0.00,
                        'credit'     => $totalReceiptValue
                    ]);
                    if ($apAcc) {
                        $db->table('accounts')->where('id', $apAccId)->update(['balance' => $apAcc->balance + $totalReceiptValue]);
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    session()->setFlashdata('error', 'Gagal memproses penerimaan barang.');
                } else {
                    session()->setFlashdata('success', 'Barang PO berhasil diterima! Dokumen GRN: ' . $receiptNo . ', stok batch obat bertambah, dan jurnal hutang usaha berhasil dibukukan.');
                }
                return redirect()->to(base_url('procurement/po'));
            }
        }

        // Fetch master data
        $suppliers = $db->table('suppliers')->orderBy('name', 'ASC')->get()->getResult();
        $medicines = $db->table('medicines')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $restoMenus = $db->table('restaurant_menus')->orderBy('name', 'ASC')->get()->getResult();

        // PR list with supplier name and requester name
        $prs = $db->table('purchase_requests')
                  ->select('purchase_requests.*, suppliers.name as supplier_name, users.username as requester_name,
                            (SELECT COUNT(*) FROM purchase_request_items WHERE purchase_request_items.purchase_request_id = purchase_requests.id) as item_count')
                  ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id')
                  ->join('users', 'users.id = purchase_requests.requested_by', 'left')
                  ->orderBy('purchase_requests.created_at', 'DESC')
                  ->get()
                  ->getResult();

        // PO list with supplier name and linked GRN
        $pos = $db->table('purchase_orders')
                  ->select('purchase_orders.*, suppliers.name as supplier_name, purchase_requests.request_no,
                            (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_items.purchase_order_id = purchase_orders.id) as item_count,
                            goods_receipts.id as grn_id, goods_receipts.receipt_no as grn_no')
                  ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                  ->join('purchase_requests', 'purchase_requests.id = purchase_orders.purchase_request_id', 'left')
                  ->join('goods_receipts', 'goods_receipts.purchase_order_id = purchase_orders.id', 'left')
                  ->orderBy('purchase_orders.created_at', 'DESC')
                  ->get()
                  ->getResult();

        // Goods receipts list
        $goodsReceipts = $db->table('goods_receipts')
                            ->select('goods_receipts.*, purchase_orders.po_no, suppliers.name as supplier_name, users.username as receiver_name')
                            ->join('purchase_orders', 'purchase_orders.id = goods_receipts.purchase_order_id')
                            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                            ->join('users', 'users.id = goods_receipts.receiver_id', 'left')
                            ->orderBy('goods_receipts.created_at', 'DESC')
                            ->get()
                            ->getResult();

        $data = [
            'title'         => 'Pengadaan Barang & Purchase Orders (PO)',
            'active_menu'   => 'procurement-po',
            'suppliers'     => $suppliers,
            'medicines'     => $medicines,
            'restoMenus'    => $restoMenus,
            'prs'           => $prs,
            'pos'           => $pos,
            'goodsReceipts' => $goodsReceipts
        ];

        return view('procurement/po', $data);
    }

    /**
     * Kelola Master Supplier / Pedagang Besar Farmasi (PBF) & Vendor
     */
    public function supplier()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'add') {
            $name    = trim($this->request->getPost('name') ?: '');
            $code    = trim($this->request->getPost('code') ?: '');
            $address = trim($this->request->getPost('address') ?: '-');
            $phone   = trim($this->request->getPost('phone') ?: '-');
            $pic     = trim($this->request->getPost('pic_name') ?: '');
            $email   = trim($this->request->getPost('email') ?: '');
            $bank    = trim($this->request->getPost('bank_name') ?: '');
            $accNo   = trim($this->request->getPost('bank_account') ?: '');

            if (empty($code)) {
                $count = $db->table('suppliers')->countAllResults();
                $code = 'SUP-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            }

            $db->table('suppliers')->insert([
                'code'         => $code,
                'name'         => $name,
                'address'      => $address,
                'phone'        => $phone,
                'pic_name'     => $pic,
                'email'        => $email,
                'bank_name'    => $bank,
                'bank_account' => $accNo,
                'status'       => 'active',
                'created_at'   => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'Supplier / PBF baru berhasil ditambahkan: ' . $name);
        } elseif ($action === 'edit') {
            $id      = intval($this->request->getPost('id'));
            $name    = trim($this->request->getPost('name') ?: '');
            $code    = trim($this->request->getPost('code') ?: '');
            $address = trim($this->request->getPost('address') ?: '-');
            $phone   = trim($this->request->getPost('phone') ?: '-');
            $pic     = trim($this->request->getPost('pic_name') ?: '');
            $email   = trim($this->request->getPost('email') ?: '');
            $bank    = trim($this->request->getPost('bank_name') ?: '');
            $accNo   = trim($this->request->getPost('bank_account') ?: '');
            $status  = $this->request->getPost('status') ?: 'active';

            $db->table('suppliers')->where('id', $id)->update([
                'code'         => $code,
                'name'         => $name,
                'address'      => $address,
                'phone'        => $phone,
                'pic_name'     => $pic,
                'email'        => $email,
                'bank_name'    => $bank,
                'bank_account' => $accNo,
                'status'       => $status
            ]);

            session()->setFlashdata('success', 'Data supplier berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = intval($this->request->getPost('id'));
            
            // Check if supplier is referenced in purchase requests or orders
            $hasPR = $db->table('purchase_requests')->where('supplier_id', $id)->countAllResults();
            $hasPO = $db->table('purchase_orders')->where('supplier_id', $id)->countAllResults();

            if ($hasPR > 0 || $hasPO > 0) {
                // Soft deactivate
                $db->table('suppliers')->where('id', $id)->update(['status' => 'inactive']);
                session()->setFlashdata('success', 'Supplier memiliki riwayat transaksi, status diubah menjadi Nonaktif.');
            } else {
                $db->table('suppliers')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Supplier berhasil dihapus dari sistem.');
            }
        }

        return redirect()->to(base_url('procurement/po#tab-suppliers'));
    }

    /**
     * Cetak Dokumen Resmi Purchase Requisition (PR) Lembar Pengajuan
     */
    public function cetakPr($id)
    {
        $db = \Config\Database::connect('default');

        $pr = $db->table('purchase_requests')
                 ->select('purchase_requests.*, suppliers.name as supplier_name, suppliers.code as supplier_code,
                           suppliers.address as supplier_address, suppliers.phone as supplier_phone,
                           suppliers.pic_name, suppliers.email as supplier_email,
                           users.username as requester_name')
                 ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id')
                 ->join('users', 'users.id = purchase_requests.requested_by', 'left')
                 ->where('purchase_requests.id', $id)
                 ->get()
                 ->getRow();

        if (!$pr) {
            session()->setFlashdata('error', 'Purchase Requisition tidak ditemukan.');
            return redirect()->to(base_url('procurement/po'));
        }

        $items = $db->table('purchase_request_items')
                    ->where('purchase_request_id', $pr->id)
                    ->get()
                    ->getResult();

        // Get approval history for this PR
        $approvals = $db->table('approval_requests')
                        ->select('approval_requests.*, approval_steps.step_name, users.username as approver_name')
                        ->join('approval_steps', 'approval_steps.step_level = approval_requests.step_level', 'left')
                        ->join('users', 'users.id = approval_requests.approver_id', 'left')
                        ->where('approval_requests.transaction_type', 'PROCUREMENT')
                        ->where('approval_requests.reference_id', $pr->id)
                        ->orderBy('approval_requests.step_level', 'ASC')
                        ->get()
                        ->getResult();

        $data = [
            'title'     => 'Purchase Requisition ' . $pr->request_no,
            'pr'        => $pr,
            'items'     => $items,
            'approvals' => $approvals
        ];

        return view('procurement/cetak_pr', $data);
    }

    /**
     * Cetak Dokumen Resmi Purchase Order (PO) Siap Cetak
     */
    public function cetakPo($id)
    {
        $db = \Config\Database::connect('default');

        $po = $db->table('purchase_orders')
                 ->select('purchase_orders.*, suppliers.name as supplier_name, suppliers.code as supplier_code,
                           suppliers.address as supplier_address, suppliers.phone as supplier_phone,
                           suppliers.pic_name, suppliers.email as supplier_email,
                           suppliers.bank_name, suppliers.bank_account,
                           purchase_requests.request_no, purchase_requests.department, users.username as creator_name')
                 ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                 ->join('purchase_requests', 'purchase_requests.id = purchase_orders.purchase_request_id', 'left')
                 ->join('users', 'users.id = purchase_requests.requested_by', 'left')
                 ->where('purchase_orders.id', $id)
                 ->get()
                 ->getRow();

        if (!$po) {
            session()->setFlashdata('error', 'Purchase Order tidak ditemukan.');
            return redirect()->to(base_url('procurement/po'));
        }

        $items = $db->table('purchase_order_items')
                    ->where('purchase_order_id', $po->id)
                    ->get()
                    ->getResult();

        $data = [
            'title' => 'Purchase Order ' . $po->po_no,
            'po'    => $po,
            'items' => $items
        ];

        return view('procurement/cetak_po', $data);
    }

    /**
     * Cetak Dokumen Bukti Penerimaan Barang / Goods Receipt Note (GRN)
     */
    public function cetakGrn($id)
    {
        $db = \Config\Database::connect('default');

        $grn = $db->table('goods_receipts')
                  ->select('goods_receipts.*, purchase_orders.po_no, purchase_orders.order_date,
                            suppliers.name as supplier_name, suppliers.code as supplier_code,
                            suppliers.address as supplier_address, suppliers.phone as supplier_phone,
                            users.username as receiver_name')
                  ->join('purchase_orders', 'purchase_orders.id = goods_receipts.purchase_order_id')
                  ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                  ->join('users', 'users.id = goods_receipts.receiver_id', 'left')
                  ->where('goods_receipts.id', $id)
                  ->get()
                  ->getRow();

        if (!$grn) {
            session()->setFlashdata('error', 'Dokumen Goods Receipt tidak ditemukan.');
            return redirect()->to(base_url('procurement/po'));
        }

        $items = $db->table('goods_receipt_items')
                    ->where('goods_receipt_id', $grn->id)
                    ->get()
                    ->getResult();

        $data = [
            'title' => 'Bukti Penerimaan Barang ' . $grn->receipt_no,
            'grn'   => $grn,
            'items' => $items
        ];

        return view('procurement/cetak_grn', $data);
    }

    /**
     * AJAX JSON: Rincian Item Purchase Request
     */
    public function getPrDetailsJson($id)
    {
        $db = \Config\Database::connect('default');

        $pr = $db->table('purchase_requests')
                 ->select('purchase_requests.*, suppliers.name as supplier_name')
                 ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id')
                 ->where('purchase_requests.id', $id)
                 ->get()
                 ->getRow();

        if (!$pr) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PR tidak ditemukan']);
        }

        $items = $db->table('purchase_request_items')->where('purchase_request_id', $id)->get()->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'pr'     => $pr,
            'items'  => $items
        ]);
    }

    /**
     * AJAX JSON: Rincian Item Purchase Order
     */
    public function getPoDetailsJson($id)
    {
        $db = \Config\Database::connect('default');

        $po = $db->table('purchase_orders')
                 ->select('purchase_orders.*, suppliers.name as supplier_name, purchase_requests.request_no')
                 ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                 ->join('purchase_requests', 'purchase_requests.id = purchase_orders.purchase_request_id', 'left')
                 ->where('purchase_orders.id', $id)
                 ->get()
                 ->getRow();

        if (!$po) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'PO tidak ditemukan']);
        }

        $items = $db->table('purchase_order_items')->where('purchase_order_id', $id)->get()->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'po'     => $po,
            'items'  => $items
        ]);
    }

    /**
     * Modul Persetujuan Transaksi (Approval Engine Dashboard)
     */
    public function approval()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $requestId = $this->request->getPost('request_id');
            $action    = $this->request->getPost('action'); // approve or reject
            $notes     = trim($this->request->getPost('notes') ?: '');

            if ($action === 'approve') {
                $res = $this->approvalEngine->approveRequest($requestId, session('user_id') ?: 1, $notes);
            } else {
                $res = $this->approvalEngine->rejectRequest($requestId, session('user_id') ?: 1, $notes);
            }

            if ($res['status'] === 'success') {
                session()->setFlashdata('success', $res['message']);
            } else {
                session()->setFlashdata('error', $res['message']);
            }
            return redirect()->to(base_url('procurement/approval'));
        }

        $roleId  = session('role_id');
        $isSuper = (session('role_name') === 'Super Admin');

        // Pending Approval Requests
        $builder = $db->table('approval_requests')
                      ->select('approval_requests.*, approval_steps.step_name, users.username as approver_name')
                      ->join('approval_steps', 'approval_steps.step_level = approval_requests.step_level', 'left')
                      ->join('users', 'users.id = approval_requests.approver_id', 'left')
                      ->where('approval_requests.status', 'pending');

        if (!$isSuper && $roleId) {
            $builder->where('approval_steps.role_id', $roleId);
        }

        $requests = $builder->orderBy('approval_requests.created_at', 'ASC')->get()->getResult();

        // Map request details (PR and its items)
        $requestDetails = [];
        $requestItems   = [];
        foreach ($requests as $r) {
            if ($r->transaction_type === 'PROCUREMENT') {
                $pr = $db->table('purchase_requests')
                         ->select('purchase_requests.*, suppliers.name as supplier_name, users.username as requester_name')
                         ->join('suppliers', 'suppliers.id = purchase_requests.supplier_id')
                         ->join('users', 'users.id = purchase_requests.requested_by', 'left')
                         ->where('purchase_requests.id', $r->reference_id)
                         ->get()
                         ->getRow();
                $requestDetails[$r->id] = $pr;

                if ($pr) {
                    $items = $db->table('purchase_request_items')->where('purchase_request_id', $pr->id)->get()->getResult();
                    $requestItems[$r->id] = $items;
                }
            }
        }

        // History of approved & rejected requests
        $historyRequests = $db->table('approval_requests')
                              ->select('approval_requests.*, approval_steps.step_name, users.username as approver_name')
                              ->join('approval_steps', 'approval_steps.step_level = approval_requests.step_level', 'left')
                              ->join('users', 'users.id = approval_requests.approver_id', 'left')
                              ->whereIn('approval_requests.status', ['approved', 'rejected'])
                              ->orderBy('approval_requests.updated_at', 'DESC')
                              ->limit(50)
                              ->get()
                              ->getResult();

        $data = [
            'title'           => 'Pusat Persetujuan Transaksi & Pengadaan (Approvals)',
            'active_menu'     => 'procurement-approval',
            'requests'        => $requests,
            'requestDetails'  => $requestDetails,
            'requestItems'    => $requestItems,
            'historyRequests' => $historyRequests
        ];

        return view('procurement/approval', $data);
    }
}
