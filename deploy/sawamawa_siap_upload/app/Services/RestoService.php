<?php

namespace App\Services;

use App\Services\JournalEngine;

class RestoService
{
    protected $db;
    protected $journalEngine;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->journalEngine = new JournalEngine();
    }

    /**
     * Create Resto & Healthy Store Order (Penjualan Bebas Makanan/Skincare atau Diet Pasien Poli Gizi)
     */
    public function createOrder(array $payload)
    {
        $this->db->transStart();

        $today = date('Ymd');
        $todayDate = date('Y-m-d');
        $session = session();
        $userId = $session->get('user_id') ?: 1;

        // 1. Generate Order Number (ORD-YYYYMMDD-XXXX)
        $lastOrder = $this->db->table('restaurant_orders')
                              ->where('DATE(created_at)', $todayDate)
                              ->orderBy('id', 'DESC')
                              ->limit(1)
                              ->get()
                              ->getRow();
        $nextNum = 1;
        if ($lastOrder && preg_match('/ORD-\d+-(\d+)/', $lastOrder->order_no, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $orderNo = 'ORD-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $orderType = $payload['order_type'] ?? 'umum';
        $visitId   = !empty($payload['visit_id']) ? intval($payload['visit_id']) : null;
        $items     = $payload['items'] ?? [];

        if (empty($items)) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Pesanan harus memiliki minimal 1 item produk atau menu.'];
        }

        // If diet pasien, auto-fill customer name from patient record if not specified
        $customerName = trim($payload['customer_name'] ?? '');
        $customerPhone = trim($payload['customer_phone'] ?? '');
        $dietInstructions = trim($payload['diet_instructions'] ?? '');

        if ($visitId && empty($customerName)) {
            $patientVisit = $this->db->table('patient_visits')
                                     ->select('patients.name, patients.phone')
                                     ->join('patients', 'patients.id = patient_visits.patient_id')
                                     ->where('patient_visits.id', $visitId)
                                     ->get()
                                     ->getRow();
            if ($patientVisit) {
                $customerName = $patientVisit->name;
                $customerPhone = $patientVisit->phone ?: '';
            }
        }

        if (empty($customerName)) {
            $customerName = 'Pelanggan Walk-In #' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        }

        // Calculate totals
        $totalGross = 0;
        $orderDetailRows = [];

        foreach ($items as $it) {
            $menuId = intval($it['menu_id'] ?? 0);
            $qty    = max(1, intval($it['qty'] ?? 1));
            $itemNotes = trim($it['notes'] ?? '');

            $menu = $this->db->table('restaurant_menus')->where('id', $menuId)->get()->getRow();
            if (!$menu) continue;

            $price = floatval($menu->price);
            $subtotal = $price * $qty;
            $totalGross += $subtotal;

            $orderDetailRows[] = [
                'menu'       => $menu,
                'qty'        => $qty,
                'price'      => $price,
                'subtotal'   => $subtotal,
                'notes'      => $itemNotes
            ];
        }

        if (empty($orderDetailRows)) {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Tidak ada item produk valid yang dipilih.'];
        }

        $discountAmount = max(0, floatval($payload['discount_amount'] ?? 0));
        $grandTotal     = max(0, $totalGross - $discountAmount);

        $paymentAction  = $payload['payment_action'] ?? 'pay_now'; // 'pay_now', 'bill_to_clinic', 'pay_later'
        $paymentMethod  = $payload['payment_method'] ?? 'cash';
        $paidAmount     = floatval($payload['paid_amount'] ?? 0);
        $changeAmount   = 0;
        $paymentStatus  = 'unpaid';

        if ($paymentAction === 'pay_now') {
            if ($paidAmount < $grandTotal) {
                $paidAmount = $grandTotal; // Default exact amount
            }
            $changeAmount = max(0, $paidAmount - $grandTotal);
            $paymentStatus = 'paid';
        } elseif ($paymentAction === 'bill_to_clinic') {
            $paymentStatus = 'billed_to_clinic';
            $paidAmount = 0;
            $changeAmount = 0;
        }

        // 2. Insert Header Order
        $orderData = [
            'order_no'          => $orderNo,
            'order_type'        => $orderType,
            'customer_name'     => $customerName,
            'customer_phone'    => $customerPhone,
            'diet_instructions' => $dietInstructions,
            'table_id'          => null,
            'visit_id'          => $visitId,
            'status'            => 'open',
            'payment_status'    => $paymentStatus,
            'payment_method'    => $paymentMethod,
            'total_amount'      => $totalGross,
            'discount_amount'   => $discountAmount,
            'grand_total'       => $grandTotal,
            'paid_amount'       => $paidAmount,
            'change_amount'     => $changeAmount,
            'cashier_id'        => $userId,
            'notes'             => trim($payload['notes'] ?? ''),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->db->table('restaurant_orders')->insert($orderData);
        $orderId = $this->db->insertID();

        // 3. Insert Details & Kitchen Trigger
        $kitchenItemsCount = 0;
        foreach ($orderDetailRows as $row) {
            $menu = $row['menu'];
            $detailData = [
                'order_id'   => $orderId,
                'menu_id'    => $menu->id,
                'qty'        => $row['qty'],
                'price'      => $row['price'],
                'status'     => 'new',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->table('restaurant_order_details')->insert($detailData);
            $detailId = $this->db->insertID();

            // Check if item needs kitchen preparation (makanan, minuman, diet_gizi)
            $needsKitchen = in_array($menu->category, ['makanan', 'minuman', 'diet_gizi', 'paket']);
            if ($needsKitchen) {
                $this->db->table('kitchen_orders')->insert([
                    'order_detail_id' => $detailId,
                    'status'          => 'new',
                    'created_at'      => date('Y-m-d H:i:s')
                ]);
                $kitchenItemsCount++;
            }

            // Deduct stock for all menus/products that track stock
            if (isset($menu->stock)) {
                $newStock = max(0, intval($menu->stock) - intval($row['qty']));
                $this->db->table('restaurant_menus')->where('id', $menu->id)->update(['stock' => $newStock]);
            }
        }

        // If all items are instant take-home (skincare/suplemen only), mark order as ready
        if ($kitchenItemsCount === 0) {
            $this->db->table('restaurant_orders')->where('id', $orderId)->update(['status' => 'ready']);
        }

        // 4. Handle Direct Settlement in Cash Register & Auto-Posting Accounting Journal
        if ($paymentStatus === 'paid') {
            // Update Active Cash Register Balance
            $activeRegister = $this->db->table('cash_registers')->where('status', 'open')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
            if ($activeRegister) {
                $this->db->table('cash_registers')->where('id', $activeRegister->id)->set('balance', 'balance + ' . floatval($grandTotal), false)->update();
            }

            // Rincian menu resto untuk keterangan jurnal akuntansi
            $itemNames = [];
            foreach ($items as $it) {
                $menu = $this->db->table('restaurant_menus')->where('id', $it['menu_id'])->get()->getRow();
                if ($menu) {
                    $itemNames[] = $menu->name . ' (' . (int)$it['qty'] . 'x @Rp ' . number_format($menu->price, 0, ',', '.') . ')';
                }
            }
            $itemsSummary = !empty($itemNames) ? implode(', ', $itemNames) : 'Menu Resto';

            // Auto-Posting Double-Entry Accounting Journal (Debit Kas/Bank, Kredit Pendapatan POS Resto)
            $this->journalEngine->postJournal(
                'RESTO_SALE',
                $orderId,
                $grandTotal,
                'Pendapatan POS Resto & Nutrisi - ' . $orderNo . ' (' . $customerName . '): ' . $itemsSummary,
                $paymentMethod,
                'Resto POS'
            );
        }

        // 5. Handle Bill-to-Clinic for Patient Diet Orders
        if ($paymentStatus === 'billed_to_clinic' && $visitId) {
            $bill = $this->db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
            if ($bill) {
                // Add items into billing_details
                foreach ($orderDetailRows as $row) {
                    $this->db->table('billing_details')->insert([
                        'billing_id' => $bill->id,
                        'item_type'  => 'resto',
                        'item_name'  => $row['menu']->name . ' (Gizi Resto)',
                        'qty'        => $row['qty'],
                        'price'      => $row['price'],
                        'subtotal'   => $row['subtotal']
                    ]);
                }

                // Update totals
                $newTotalResto = $bill->total_restaurant + $grandTotal;
                $newGrandTotal = $bill->grand_total + $grandTotal;
                $this->db->table('billing_transactions')
                         ->where('id', $bill->id)
                         ->update([
                             'total_restaurant' => $newTotalResto,
                             'grand_total'      => $newGrandTotal
                         ]);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Gagal memproses pesanan restoran & produk sehat.'];
        }

        return [
            'status'         => 'success',
            'order_id'       => $orderId,
            'order_no'       => $orderNo,
            'customer_name'  => $customerName,
            'payment_status' => $paymentStatus,
            'grand_total'    => $grandTotal,
            'message'        => 'Pesanan ' . $orderNo . ' berhasil diproses.'
        ];
    }

    /**
     * Selesaikan / Bayar Pesanan Langsung di Resto
     */
    public function settleOrderPayment($orderId, $paymentMethod, $paidAmount)
    {
        $this->db->transStart();

        $order = $this->db->table('restaurant_orders')->where('id', $orderId)->get()->getRow();
        if (!$order) {
            return ['status' => 'error', 'message' => 'Data pesanan tidak ditemukan.'];
        }

        $grandTotal = floatval($order->grand_total);
        if ($paidAmount < $grandTotal) {
            $paidAmount = $grandTotal;
        }
        $changeAmount = max(0, $paidAmount - $grandTotal);
        $userId = session()->get('user_id') ?: 1;

        $this->db->table('restaurant_orders')
                 ->where('id', $orderId)
                 ->update([
                     'payment_status' => 'paid',
                     'payment_method' => $paymentMethod,
                     'paid_amount'    => $paidAmount,
                     'change_amount'  => $changeAmount,
                     'cashier_id'     => $userId
                 ]);

        // Update Cash Register Balance
        $activeRegister = $this->db->table('cash_registers')->where('status', 'open')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
        if ($activeRegister) {
            $this->db->table('cash_registers')->where('id', $activeRegister->id)->set('balance', 'balance + ' . floatval($grandTotal), false)->update();
        }

        // Rincian menu resto untuk keterangan jurnal akuntansi
        $orderDetails = $this->db->table('restaurant_order_details rod')
                                 ->select('rod.qty, rod.price, rm.name')
                                 ->join('restaurant_menus rm', 'rm.id = rod.menu_id', 'left')
                                 ->where('rod.order_id', $orderId)
                                 ->get()
                                 ->getResult();
        $itemNames = [];
        foreach ($orderDetails as $od) {
            $itemNames[] = ($od->name ?: 'Item') . ' (' . (int)$od->qty . 'x @Rp ' . number_format($od->price, 0, ',', '.') . ')';
        }
        $itemsSummary = !empty($itemNames) ? implode(', ', $itemNames) : 'Menu Resto';

        // Auto-Posting Double-Entry Accounting Journal
        $this->journalEngine->postJournal(
            'RESTO_SALE',
            $orderId,
            $grandTotal,
            'Pelunasan Tagihan Resto & Nutrisi - ' . $order->order_no . ' (' . $order->customer_name . '): ' . $itemsSummary,
            $paymentMethod,
            'Resto POS'
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Gagal menyelesaikan pembayaran pesanan.'];
        }

        return ['status' => 'success', 'message' => 'Pembayaran pesanan ' . $order->order_no . ' berhasil diselesaikan.'];
    }

    /**
     * Update Status Pesanan Keseluruhan
     */
    public function updateOrderStatus($orderId, $status)
    {
        $validStatuses = ['open', 'cooking', 'ready', 'closed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return ['status' => 'error', 'message' => 'Status tidak valid.'];
        }

        $this->db->table('restaurant_orders')->where('id', $orderId)->update(['status' => $status]);
        return ['status' => 'success', 'message' => 'Status pesanan berhasil diubah menjadi ' . strtoupper($status) . '.'];
    }

    /**
     * Quick Restock / Penambahan Stok Menu & Produk
     */
    public function restockMenu($menuId, $addedQty, $notes = '')
    {
        $menu = $this->db->table('restaurant_menus')->where('id', $menuId)->get()->getRow();
        if (!$menu) {
            return ['status' => 'error', 'message' => 'Produk/Menu tidak ditemukan.'];
        }

        $addedQty = intval($addedQty);
        if ($addedQty <= 0) {
            return ['status' => 'error', 'message' => 'Jumlah penambahan stok harus lebih dari 0.'];
        }

        $newStock = intval($menu->stock) + $addedQty;
        $this->db->table('restaurant_menus')->where('id', $menuId)->update([
            'stock' => $newStock,
            'is_active' => 1
        ]);

        return [
            'status'    => 'success',
            'message'   => 'Stok ' . $menu->name . ' berhasil ditambah sebanyak ' . $addedQty . ' (Total Stok: ' . $newStock . ').',
            'new_stock' => $newStock
        ];
    }

    /**
     * Data Layar Display Antrean TV Monitor Resto
     */
    public function getDisplayAntrean()
    {
        $today = date('Y-m-d');

        // 1. Sedang Disiapkan / Dimasak (status: open, cooking)
        $preparingOrders = $this->db->table('restaurant_orders')
                                   ->select('id, order_no, customer_name, order_type, status, created_at')
                                   ->whereIn('status', ['open', 'cooking'])
                                   ->where('DATE(created_at)', $today)
                                   ->orderBy('id', 'ASC')
                                   ->limit(12)
                                   ->get()
                                   ->getResult();

        // 2. Siap Diambil / Selesai (status: ready)
        $readyOrders = $this->db->table('restaurant_orders')
                                ->select('id, order_no, customer_name, order_type, status, created_at')
                                ->where('status', 'ready')
                                ->where('DATE(created_at)', $today)
                                ->orderBy('id', 'DESC')
                                ->limit(12)
                                ->get()
                                ->getResult();

        return [
            'preparing' => $preparingOrders,
            'ready'     => $readyOrders,
            'server_time' => date('H:i:s'),
            'server_date' => date('d M Y')
        ];
    }

    /**
     * Data Rekomendasi Gizi & Diagnosa Pasien untuk POS
     */
    public function getPatientDietInfo($visitId)
    {
        $visit = $this->db->table('patient_visits')
                          ->select('patient_visits.*, patients.name as patient_name, patients.nik, patients.phone, poliklinik.name as poly_name')
                          ->join('patients', 'patients.id = patient_visits.patient_id')
                          ->join('poliklinik', 'poliklinik.id = patient_visits.poly_id', 'left')
                          ->where('patient_visits.id', $visitId)
                          ->get()
                          ->getRow();

        if (!$visit) {
            return ['status' => 'error', 'message' => 'Data kunjungan tidak ditemukan.'];
        }

        // Get SOAP / Assessment notes if any
        $soap = $this->db->table('medical_records')
                         ->where('visit_id', $visitId)
                         ->get()
                         ->getRow();

        $diagnosis = $soap ? ($soap->assessment ?: $soap->diagnosis ?: '') : '';
        $allergies = $soap ? ($soap->allergies ?: '') : '';

        // Analyze recommended and restricted nutrition tags
        $recommendations = [];
        $restrictions = [];
        $diagLower = strtolower($diagnosis . ' ' . $allergies . ' ' . ($visit->poly_name ?? ''));

        if (str_contains($diagLower, 'diabet') || str_contains($diagLower, 'dm') || str_contains($diagLower, 'gula')) {
            $recommendations[] = ['tag' => 'Rendah Gula (Sugar-Free)', 'badge' => 'success', 'desc' => 'Dianjurkan makanan/jus kaya serat tanpa gula tambahan'];
            $restrictions[] = 'Hindari minuman manis, sirup & dessert bergula tinggi';
        }
        if (str_contains($diagLower, 'hipertensi') || str_contains($diagLower, 'tensi') || str_contains($diagLower, 'jantung')) {
            $recommendations[] = ['tag' => 'DASH Diet (Rendah Garam)', 'badge' => 'info', 'desc' => 'Dianjurkan menu kaya kalium, salad & kukusan herbal'];
            $restrictions[] = 'Batasi natrium/garam tinggi, msg & gorengan';
        }
        if (str_contains($diagLower, 'asam urat') || str_contains($diagLower, 'gout') || str_contains($diagLower, 'purin')) {
            $recommendations[] = ['tag' => 'Rendah Purin', 'badge' => 'primary', 'desc' => 'Dianjurkan jus lemon detox, sup sayur bening'];
            $restrictions[] = 'Hindari jeroan, ekstrak daging pekat, dan emping';
        }
        if (str_contains($diagLower, 'gerd') || str_contains($diagLower, 'maag') || str_contains($diagLower, 'gastritis') || str_contains($diagLower, 'lambung')) {
            $recommendations[] = ['tag' => 'Ramah Lambung (Non-Acidic)', 'badge' => 'warning text-dark', 'desc' => 'Dianjurkan bubur gizi, sup hangat & pisang'];
            $restrictions[] = 'Hindari makanan pedas, kopi, dan jus buah asam pekat';
        }
        if (str_contains($diagLower, 'kulit') || str_contains($diagLower, 'dermal') || str_contains($diagLower, 'acne') || str_contains($diagLower, 'estetika') || str_contains($diagLower, 'gizi')) {
            $recommendations[] = ['tag' => 'Antioksidan & Skin Repair', 'badge' => 'purple bg-purple text-white', 'desc' => 'Dianjurkan jus berry, kolagen booster & skincare dermal'];
        }

        if (empty($recommendations)) {
            $recommendations[] = ['tag' => 'Menu Sehat Seimbang', 'badge' => 'teal', 'desc' => 'Cocok untuk seluruh menu bergizi Sawamawa Resto'];
        }

        return [
            'status'          => 'success',
            'patient_name'    => $visit->patient_name,
            'poly_name'       => $visit->poly_name ?: 'Poli Umum',
            'diagnosis'       => $diagnosis ?: 'Pemeriksaan Rutin / Skrining Gizi',
            'allergies'       => $allergies ?: 'Tidak ada alergi tercatat',
            'recommendations' => $recommendations,
            'restrictions'    => $restrictions
        ];
    }

    /**
     * Data Laporan & Analitik Resto
     */
    public function getRestoAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: date('Y-m-01');
        $endDate   = $endDate ?: date('Y-m-d');

        // Base query for orders
        $ordersQuery = $this->db->table('restaurant_orders')
                                ->where('DATE(created_at) >=', $startDate)
                                ->where('DATE(created_at) <=', $endDate)
                                ->whereIn('payment_status', ['paid', 'billed_to_clinic']);

        $totalRevenue = (float) ($ordersQuery->selectSum('grand_total')->get()->getRow()->grand_total ?? 0);
        
        $totalOrders = $this->db->table('restaurant_orders')
                                ->where('DATE(created_at) >=', $startDate)
                                ->where('DATE(created_at) <=', $endDate)
                                ->whereIn('payment_status', ['paid', 'billed_to_clinic'])
                                ->countAllResults();

        $avgBasket = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        $totalItemsSold = (int) ($this->db->table('restaurant_order_details')
                                          ->join('restaurant_orders', 'restaurant_orders.id = restaurant_order_details.order_id')
                                          ->where('DATE(restaurant_orders.created_at) >=', $startDate)
                                          ->where('DATE(restaurant_orders.created_at) <=', $endDate)
                                          ->whereIn('restaurant_orders.payment_status', ['paid', 'billed_to_clinic'])
                                          ->selectSum('restaurant_order_details.qty')
                                          ->get()->getRow()->qty ?? 0);

        // Sales Trend by Date
        $dailyTrends = $this->db->table('restaurant_orders')
                                ->select("DATE(created_at) as sale_date, COUNT(id) as total_orders, SUM(grand_total) as daily_revenue")
                                ->where('DATE(created_at) >=', $startDate)
                                ->where('DATE(created_at) <=', $endDate)
                                ->whereIn('payment_status', ['paid', 'billed_to_clinic'])
                                ->groupBy("DATE(created_at)")
                                ->orderBy("sale_date", "ASC")
                                ->get()
                                ->getResult();

        // Top 5 Best Selling Items
        $topItems = $this->db->table('restaurant_order_details')
                             ->select('restaurant_menus.name, restaurant_menus.category, restaurant_menus.code, SUM(restaurant_order_details.qty) as total_qty, SUM(restaurant_order_details.qty * restaurant_order_details.price) as total_omset')
                             ->join('restaurant_menus', 'restaurant_menus.id = restaurant_order_details.menu_id')
                             ->join('restaurant_orders', 'restaurant_orders.id = restaurant_order_details.order_id')
                             ->where('DATE(restaurant_orders.created_at) >=', $startDate)
                             ->where('DATE(restaurant_orders.created_at) <=', $endDate)
                             ->whereIn('restaurant_orders.payment_status', ['paid', 'billed_to_clinic'])
                             ->groupBy('restaurant_order_details.menu_id')
                             ->orderBy('total_qty', 'DESC')
                             ->limit(6)
                             ->get()
                             ->getResult();

        // Category Breakdown
        $categoryBreakdown = $this->db->table('restaurant_order_details')
                                      ->select('restaurant_menus.category, SUM(restaurant_order_details.qty) as total_qty, SUM(restaurant_order_details.qty * restaurant_order_details.price) as total_amount')
                                      ->join('restaurant_menus', 'restaurant_menus.id = restaurant_order_details.menu_id')
                                      ->join('restaurant_orders', 'restaurant_orders.id = restaurant_order_details.order_id')
                                      ->where('DATE(restaurant_orders.created_at) >=', $startDate)
                                      ->where('DATE(restaurant_orders.created_at) <=', $endDate)
                                      ->whereIn('restaurant_orders.payment_status', ['paid', 'billed_to_clinic'])
                                      ->groupBy('restaurant_menus.category')
                                      ->get()
                                      ->getResult();

        // Detailed Orders List
        $ordersList = $this->db->table('restaurant_orders')
                               ->select('restaurant_orders.*, users.username as cashier_name')
                               ->join('users', 'users.id = restaurant_orders.cashier_id', 'left')
                               ->where('DATE(restaurant_orders.created_at) >=', $startDate)
                               ->where('DATE(restaurant_orders.created_at) <=', $endDate)
                               ->whereIn('restaurant_orders.payment_status', ['paid', 'billed_to_clinic'])
                               ->orderBy('restaurant_orders.id', 'DESC')
                               ->get()
                               ->getResult();

        return [
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'totalRevenue'      => $totalRevenue,
            'totalOrders'       => $totalOrders,
            'avgBasket'         => $avgBasket,
            'totalItemsSold'    => $totalItemsSold,
            'dailyTrends'       => $dailyTrends,
            'topItems'          => $topItems,
            'categoryBreakdown' => $categoryBreakdown,
            'ordersList'        => $ordersList
        ];
    }
}
