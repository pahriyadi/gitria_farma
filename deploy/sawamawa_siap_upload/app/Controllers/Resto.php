<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\RestoService;

class Resto extends BaseController
{
    protected $restoService;

    public function __construct()
    {
        $this->restoService = new RestoService();
    }

    public function pos()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            // 1. Create New Order (Penjualan Bebas Makanan/Skincare / Diet Pasien)
            if ($action === 'create_order') {
                $payload = [
                    'order_type'        => $this->request->getPost('order_type'),
                    'customer_name'     => $this->request->getPost('customer_name'),
                    'customer_phone'    => $this->request->getPost('customer_phone'),
                    'diet_instructions' => $this->request->getPost('diet_instructions'),
                    'visit_id'          => $this->request->getPost('visit_id'),
                    'items'             => $this->request->getPost('items') ?: [],
                    'discount_amount'   => $this->request->getPost('discount_amount') ?: 0,
                    'payment_action'    => $this->request->getPost('payment_action') ?: 'pay_now',
                    'payment_method'    => $this->request->getPost('payment_method') ?: 'cash',
                    'paid_amount'       => $this->request->getPost('paid_amount') ?: 0,
                    'notes'             => $this->request->getPost('notes')
                ];

                $res = $this->restoService->createOrder($payload);
                if ($res['status'] === 'success') {
                    $printUrl = base_url('resto/cetak-nota/' . $res['order_id']);
                    session()->setFlashdata('success', $res['message'] . ' <a href="' . $printUrl . '" target="_blank" class="btn btn-xs btn-outline-light ml-2 font-weight-bold"><i class="fas fa-print"></i> Cetak Struk / Nota</a>');
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                return redirect()->to(base_url('resto/pos'));
            }

            // 2. Direct Settle Payment for Open Orders
            if ($action === 'settle_payment') {
                $orderId       = $this->request->getPost('order_id');
                $paymentMethod = $this->request->getPost('payment_method');
                $paidAmount    = floatval($this->request->getPost('paid_amount'));

                $res = $this->restoService->settleOrderPayment($orderId, $paymentMethod, $paidAmount);
                if ($res['status'] === 'success') {
                    $printUrl = base_url('resto/cetak-nota/' . $orderId);
                    session()->setFlashdata('success', $res['message'] . ' <a href="' . $printUrl . '" target="_blank" class="btn btn-xs btn-outline-light ml-2 font-weight-bold"><i class="fas fa-print"></i> Cetak Struk</a>');
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                return redirect()->to(base_url('resto/pos'));
            }

            // 3. Update Order Status
            if ($action === 'update_status') {
                $orderId   = $this->request->getPost('order_id');
                $newStatus = $this->request->getPost('status');

                $res = $this->restoService->updateOrderStatus($orderId, $newStatus);
                if ($res['status'] === 'success') {
                    session()->setFlashdata('success', $res['message']);
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                return redirect()->to(base_url('resto/pos'));
            }

            // 4. Manage Menu / Product Item (Add or Update)
            if ($action === 'save_menu') {
                $menuId = $this->request->getPost('menu_id');
                $isActive = $this->request->getPost('is_active') !== null ? intval($this->request->getPost('is_active')) : 1;
                $dataMenu = [
                    'code'           => trim($this->request->getPost('code')),
                    'name'           => trim($this->request->getPost('name')),
                    'category'       => $this->request->getPost('category'),
                    'classification' => $this->request->getPost('classification') ?: 'umum',
                    'price'          => floatval($this->request->getPost('price')),
                    'stock'          => intval($this->request->getPost('stock') ?: 100),
                    'description'    => trim($this->request->getPost('description')),
                    'is_active'      => $isActive
                ];

                if (!empty($menuId)) {
                    $db->table('restaurant_menus')->where('id', $menuId)->update($dataMenu);
                    session()->setFlashdata('success', 'Data menu/produk "' . esc($dataMenu['name']) . '" berhasil diperbarui.');
                } else {
                    $db->table('restaurant_menus')->insert($dataMenu);
                    session()->setFlashdata('success', 'Menu/produk baru "' . esc($dataMenu['name']) . '" berhasil ditambahkan.');
                }
                return redirect()->to(base_url('resto/pos'));
            }

            // 5. Delete or Inactivate Menu / Product Item
            if ($action === 'delete_menu') {
                $menuId = $this->request->getPost('menu_id');
                if (!empty($menuId)) {
                    $targetMenu = $db->table('restaurant_menus')->where('id', $menuId)->get()->getRow();
                    $menuName = $targetMenu ? $targetMenu->name : 'Item';

                    $hasOrders = $db->table('restaurant_order_details')->where('menu_id', $menuId)->countAllResults();
                    if ($hasOrders > 0) {
                        $db->table('restaurant_menus')->where('id', $menuId)->update(['is_active' => 0]);
                        session()->setFlashdata('success', 'Menu/produk "' . esc($menuName) . '" telah dinonaktifkan dari daftar kasir (memiliki riwayat transaksi).');
                    } else {
                        $db->table('restaurant_menus')->where('id', $menuId)->delete();
                        session()->setFlashdata('success', 'Menu/produk "' . esc($menuName) . '" berhasil dihapus permanen.');
                    }
                }
                return redirect()->to(base_url('resto/pos'));
            }
        }

        // Fetch All Master Menus and Products for Management Modal
        $allMenus = $db->table('restaurant_menus')
                       ->orderBy('id', 'DESC')
                       ->get()
                       ->getResult();

        // Fetch Active Menus for POS Catalog
        $menus = $db->table('restaurant_menus')
                    ->where('is_active', 1)
                    ->orderBy('category', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->getResult();

        // Categorized Menus
        $categorizedMenus = [
            'makanan'   => [],
            'minuman'   => [],
            'diet_gizi' => [],
            'skincare'  => [],
            'suplemen'  => []
        ];
        foreach ($menus as $m) {
            $cat = $m->category ?: 'makanan';
            if (isset($categorizedMenus[$cat])) {
                $categorizedMenus[$cat][] = $m;
            } else {
                $categorizedMenus['makanan'][] = $m;
            }
        }

        // Fetch Today's Orders (Open, Cooking, Ready, Closed)
        $orders = $db->table('restaurant_orders')
                     ->select('restaurant_orders.*, patients.name as patient_name, patients.no_rm, polyclinics.name as polyclinic_name, users.username as cashier_name')
                     ->join('patient_visits', 'patient_visits.id = restaurant_orders.visit_id', 'left')
                     ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                     ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                     ->join('users', 'users.id = restaurant_orders.cashier_id', 'left')
                     ->where('DATE(restaurant_orders.created_at)', date('Y-m-d'))
                     ->orderBy('restaurant_orders.id', 'DESC')
                     ->get()
                     ->getResult();

        // Fetch Order Details
        $orderDetails = [];
        foreach ($orders as $o) {
            $details = $db->table('restaurant_order_details')
                          ->select('restaurant_order_details.*, restaurant_menus.name as menu_name, restaurant_menus.category as menu_category')
                          ->join('restaurant_menus', 'restaurant_menus.id = restaurant_order_details.menu_id')
                          ->where('order_id', $o->id)
                          ->get()
                          ->getResult();
            $orderDetails[$o->id] = $details;
        }

        // Active Patient Visits for Nutrition/Diet referrals
        $activeVisits = $db->table('patient_visits')
                           ->select('patient_visits.*, patients.name as patient_name, patients.no_rm, patients.phone as patient_phone, patients.allergies as patient_allergies, polyclinics.name as polyclinic_name, doctors.name as doctor_name')
                           ->join('patients', 'patients.id = patient_visits.patient_id')
                           ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id')
                           ->join('doctors', 'doctors.id = patient_visits.doctor_id')
                           ->whereIn('patient_visits.status', ['waiting', 'triage', 'examining', 'pharmacy', 'billing', 'completed'])
                           ->where('DATE(patient_visits.visit_date)', date('Y-m-d'))
                           ->orderBy('patient_visits.id', 'DESC')
                           ->get()
                           ->getResult();

        // Dynamic Payment Methods
        $paymentMethods = $db->table('payment_methods')->where('is_active', 1)->orderBy('id', 'ASC')->get()->getResult();

        $data = [
            'title'            => 'POS Resto Sehat & Skincare Care',
            'active_menu'      => 'resto-pos',
            'menus'            => $menus,
            'allMenus'         => $allMenus,
            'categorizedMenus' => $categorizedMenus,
            'orders'           => $orders,
            'details'          => $orderDetails,
            'activeVisits'     => $activeVisits,
            'paymentMethods'   => $paymentMethods
        ];

        return view('resto/pos', $data);
    }

    public function dapur()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $kitchenOrderId = $this->request->getPost('kitchen_order_id');
            $newStatus      = $this->request->getPost('status'); // 'cooking', 'ready', 'served'

            $kOrder = $db->table('kitchen_orders')->where('id', $kitchenOrderId)->get()->getRow();
            if ($kOrder) {
                $db->table('kitchen_orders')->where('id', $kitchenOrderId)->update(['status' => $newStatus]);
                
                // Sync to restaurant_order_details
                $db->table('restaurant_order_details')
                   ->where('id', $kOrder->order_detail_id)
                   ->update(['status' => $newStatus]);

                // Check parent order overall status
                $detail = $db->table('restaurant_order_details')->where('id', $kOrder->order_detail_id)->get()->getRow();
                if ($detail) {
                    $orderId = $detail->order_id;
                    $allDetails = $db->table('restaurant_order_details')->where('order_id', $orderId)->get()->getResult();
                    
                    $allReady = true;
                    $anyCooking = false;
                    foreach ($allDetails as $d) {
                        if ($d->status === 'cooking') $anyCooking = true;
                        if ($d->status !== 'ready' && $d->status !== 'served') $allReady = false;
                    }

                    if ($allReady) {
                        $db->table('restaurant_orders')->where('id', $orderId)->update(['status' => 'ready']);
                    } elseif ($anyCooking) {
                        $db->table('restaurant_orders')->where('id', $orderId)->update(['status' => 'cooking']);
                    }
                }
            }

            session()->setFlashdata('success', 'Status pesanan dapur berhasil diperbarui menjadi ' . strtoupper($newStatus) . '.');
            return redirect()->to(base_url('resto/dapur'));
        }

        // Fetch active kitchen orders (today only)
        $kitchenOrders = $db->table('kitchen_orders')
                            ->select('kitchen_orders.*, restaurant_order_details.qty, restaurant_menus.name as menu_name, restaurant_menus.category as menu_category, restaurant_orders.id as order_id, restaurant_orders.order_no, restaurant_orders.order_type, restaurant_orders.customer_name, restaurant_orders.diet_instructions, restaurant_orders.created_at as order_time, polyclinics.name as polyclinic_name')
                            ->join('restaurant_order_details', 'restaurant_order_details.id = kitchen_orders.order_detail_id')
                            ->join('restaurant_menus', 'restaurant_menus.id = restaurant_order_details.menu_id')
                            ->join('restaurant_orders', 'restaurant_orders.id = restaurant_order_details.order_id')
                            ->join('patient_visits', 'patient_visits.id = restaurant_orders.visit_id', 'left')
                            ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                            ->whereIn('kitchen_orders.status', ['new', 'cooking', 'ready'])
                            ->where('DATE(kitchen_orders.created_at)', date('Y-m-d'))
                            ->orderBy('kitchen_orders.id', 'ASC')
                            ->get()
                            ->getResult();

        $data = [
            'title'         => 'Kitchen Display System (KDS) & Dapur Gizi',
            'active_menu'   => 'resto-dapur',
            'kitchenOrders' => $kitchenOrders
        ];

        return view('resto/dapur', $data);
    }

    public function cetakNota($orderId)
    {
        $db = \Config\Database::connect('default');

        $order = $db->table('restaurant_orders')
                    ->select('restaurant_orders.*, patients.name as patient_name, patients.no_rm, polyclinics.name as polyclinic_name, users.username as cashier_name')
                    ->join('patient_visits', 'patient_visits.id = restaurant_orders.visit_id', 'left')
                    ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                    ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                    ->join('users', 'users.id = restaurant_orders.cashier_id', 'left')
                    ->where('restaurant_orders.id', $orderId)
                    ->get()
                    ->getRow();

        if (!$order) {
            return redirect()->to(base_url('resto/pos'))->with('error', 'Pesanan tidak ditemukan.');
        }

        $details = $db->table('restaurant_order_details')
                      ->select('restaurant_order_details.*, restaurant_menus.name as menu_name, restaurant_menus.code as menu_code, restaurant_menus.category as menu_category')
                      ->join('restaurant_menus', 'restaurant_menus.id = restaurant_order_details.menu_id')
                      ->where('order_id', $orderId)
                      ->get()
                      ->getResult();

        $data = [
            'order'   => $order,
            'details' => $details
        ];

        return view('resto/cetak_nota', $data);
    }

    /**
     * Layar TV Display Antrean Pesanan Publik (/resto/display-antrean)
     */
    public function displayAntrean()
    {
        $antreanData = $this->restoService->getDisplayAntrean();

        $data = [
            'title'       => 'Layar Antrean Pesanan Resto & Healthy Store',
            'preparing'   => $antreanData['preparing'],
            'ready'       => $antreanData['ready'],
            'server_time' => $antreanData['server_time'],
            'server_date' => $antreanData['server_date']
        ];

        return view('resto/display_antrean', $data);
    }

    /**
     * Endpoint JSON untuk polling realtime layar TV antrean
     */
    public function getAntreanJson()
    {
        $antreanData = $this->restoService->getDisplayAntrean();
        return $this->response->setJSON($antreanData);
    }

    /**
     * Dashboard Laporan & Analitik Penjualan Resto (/resto/laporan)
     */
    public function laporan()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $analytics = $this->restoService->getRestoAnalytics($startDate, $endDate);

        $data = array_merge($analytics, [
            'title'       => 'Laporan & Analisis Penjualan Resto',
            'active_menu' => 'resto-laporan'
        ]);

        return view('resto/laporan', $data);
    }

    /**
     * Cetak Laporan Penjualan Resto (Print-Ready)
     */
    public function cetakLaporan()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $analytics = $this->restoService->getRestoAnalytics($startDate, $endDate);

        $data = array_merge($analytics, [
            'title' => 'Cetak Rekap Laporan Penjualan Resto & Nutrisi Sehat'
        ]);

        return view('resto/cetak_laporan', $data);
    }

    /**
     * Quick Restock / Penambahan Stok Menu
     */
    public function restock()
    {
        $menuId   = $this->request->getPost('menu_id');
        $addedQty = $this->request->getPost('added_qty');
        $notes    = $this->request->getPost('notes') ?: '';

        $res = $this->restoService->restockMenu($menuId, $addedQty, $notes);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($res);
        }

        if ($res['status'] === 'success') {
            session()->setFlashdata('success', $res['message']);
        } else {
            session()->setFlashdata('error', $res['message']);
        }

        return redirect()->to(base_url('resto/pos'));
    }

    /**
     * Rekomendasi Gizi & Diagnosa Pasien Kunjungan
     */
    public function getPatientDietInfo($visitId)
    {
        $info = $this->restoService->getPatientDietInfo($visitId);
        return $this->response->setJSON($info);
    }
}
