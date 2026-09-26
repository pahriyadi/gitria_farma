<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PharmacyService;

class Apotek extends BaseController
{
    protected $pharmacyService;

    public function __construct()
    {
        $this->pharmacyService = new PharmacyService();
    }

    public function resep()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $prescriptionId = $this->request->getPost('prescription_id');
            $dispenses = $this->request->getPost('dispense'); // Array: [med_id => ['batch_id' => X, 'qty' => Y]]

            $prescription = $db->table('prescriptions')
                               ->select('prescriptions.*, patient_visits.no_visit, patients.name as patient_name, queue_numbers.queue_no')
                               ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                               ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                               ->join('queue_numbers', 'queue_numbers.visit_id = patient_visits.id', 'left')
                               ->where('prescriptions.id', $prescriptionId)
                               ->get()
                               ->getRow();

            $res = $this->pharmacyService->processPrescription($prescriptionId, $dispenses);
            if ($res['status'] === 'success') {
                $qNumber = $prescription ? ($prescription->queue_no ?: ($prescription->no_visit ?? 'A-001')) : 'A-001';
                $patientName = $prescription ? ($prescription->patient_name ?: 'Pasien') : 'Pasien';

                // Daftarkan event pemanggilan selesai dan terima kasih ke antrean server pusat (LiveSync TV)
                try {
                    $qCallService = new \App\Services\QueueCallService();
                    $qCallService->triggerCall([
                        'service_type'   => 'completed',
                        'counter_name'   => 'Selesai',
                        'queue_number'   => $qNumber,
                        'patient_name'   => $patientName,
                        'visit_id'       => $prescription ? $prescription->visit_id : null,
                        'call_action'    => 'completed',
                        'call_priority'  => 2,
                    ]);
                } catch (\Throwable $e) {}

                session()->setFlashdata('voice_trigger', [
                    'action'       => 'to_completed',
                    'queue_number' => $qNumber,
                    'patient_name' => $patientName
                ]);
                session()->setFlashdata('success', $res['message'] . ' Penyerahan obat untuk pasien ' . esc($patientName) . ' (' . esc($qNumber) . ') telah selesai.');
            } else {
                session()->setFlashdata('error', $res['message']);
            }
            return redirect()->to(base_url('apotek/resep'));
        }

        // Fetch pending prescriptions with cashier payment status
        $prescriptions = $db->table('prescriptions')
                            ->select('prescriptions.*, 
                                      patient_visits.no_visit, 
                                      patients.name as patient_name, 
                                      patients.no_rm, 
                                      COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name,
                                      COALESCE(bt.status, "open") as billing_status,
                                      (CASE WHEN bt.status = "paid" THEN 1 ELSE 0 END) as is_paid,
                                      bt.payment_method,
                                      bt.grand_total as billing_grand_total')
                            ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                            ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                            ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                            ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                            ->join('billing_transactions bt', 'bt.visit_id = prescriptions.visit_id', 'left')
                            ->where('prescriptions.status', 'waiting')
                            ->orderBy('prescriptions.created_at', 'ASC')
                            ->get()
                            ->getResult();

        // Get details of all waiting prescriptions
        $prescriptionDetails = [];
        foreach ($prescriptions as $p) {
            $details = $db->table('prescription_details')
                          ->select('prescription_details.*, medicines.name as medicine_name')
                          ->join('medicines', 'medicines.id = prescription_details.medicine_id')
                          ->where('prescription_id', $p->id)
                          ->get()
                          ->getResult();

            // Fetch available batches for each medicine in prescription
            foreach ($details as &$d) {
                $d->batches = $db->table('medicine_batches')
                                 ->where('medicine_id', $d->medicine_id)
                                 ->where('stock >', 0)
                                 ->orderBy('expired_date', 'ASC') // FEFO: oldest expiry first
                                 ->get()
                                 ->getResult();
            }

            $prescriptionDetails[$p->id] = $details;
        }

        // Fetch completed prescriptions history with billing & items summary
        $completedPrescriptions = $db->table('prescriptions')
                                     ->select('prescriptions.*, 
                                               patient_visits.no_visit, 
                                               patient_visits.visit_date,
                                               patients.name as patient_name, 
                                               patients.no_rm, 
                                               patients.gender,
                                               patients.date_of_birth,
                                               COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name,
                                               COALESCE(bt.status, "open") as billing_status,
                                               COALESCE(bt.billing_no, "-") as billing_no,
                                               (CASE WHEN bt.status = "paid" THEN 1 ELSE 0 END) as is_paid,
                                               bt.payment_method,
                                               bt.id as billing_id,
                                               bt.grand_total as billing_grand_total,
                                               bt.total_medicines as billing_total_medicines,
                                               ct.receipt_no')
                                     ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                                     ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                                     ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                                     ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                                     ->join('billing_transactions bt', 'bt.visit_id = prescriptions.visit_id', 'left')
                                     ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                                     ->where('prescriptions.status', 'completed')
                                     ->orderBy('prescriptions.dispensed_at', 'DESC')
                                     ->limit(150)
                                     ->get()
                                     ->getResult();

        foreach ($completedPrescriptions as &$cp) {
            $cDetails = $db->table('prescription_details')
                           ->select('prescription_details.*, medicines.name as medicine_name')
                           ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                           ->where('prescription_id', $cp->id)
                           ->get()
                           ->getResult();

            $cp->details = $cDetails;
            $cp->item_count = count($cDetails);

            $summaryParts = [];
            $totalGross = 0; $totalTusla = 0; $totalEmbalase = 0; $totalDisc = 0;
            foreach ($cDetails as $cd) {
                $summaryParts[] = $cd->medicine_name . ' (' . (int)$cd->qty . 'x)';
                $totalGross += ($cd->qty * $cd->price);
                $totalTusla += (float)($cd->tusla ?? 0);
                $totalEmbalase += (float)($cd->embalase ?? 0);
                $totalDisc += (float)($cd->discount ?? 0);
            }
            $cp->items_summary = implode(', ', $summaryParts);
            $cp->calculated_total = max(0, $totalGross + $totalTusla + $totalEmbalase - $totalDisc);
            $cp->total_tusla = $totalTusla;
            $cp->total_embalase = $totalEmbalase;
        }

        $data = [
            'title'                  => 'Tebus e-Resep Pasien',
            'active_menu'            => 'apotek-resep',
            'prescriptions'          => $prescriptions,
            'details'                => $prescriptionDetails,
            'completedPrescriptions' => $completedPrescriptions
        ];

        return view('apotek/resep', $data);
    }

    public function stok()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            // 1. Tambah Master Obat Baru
            if ($action === 'add_medicine') {
                $code = trim($this->request->getPost('code'));
                $name = trim($this->request->getPost('name'));

                // Check duplicate code
                $existing = $db->table('medicines')->where('code', $code)->get()->getRow();
                if ($existing) {
                    session()->setFlashdata('error', 'Kode obat "' . $code . '" sudah terdaftar.');
                    return redirect()->to(base_url('apotek/stok'));
                }

                $medicineData = [
                    'code'      => $code,
                    'name'      => $name,
                    'type'      => $this->request->getPost('type') ?: 'bebas',
                    'unit'      => $this->request->getPost('unit') ?: 'Tablet',
                    'price'     => (float) $this->request->getPost('price'),
                    'min_stock' => (int) ($this->request->getPost('min_stock') ?: 10),
                    'status'    => $this->request->getPost('status') ?: 'active'
                ];

                $db->table('medicines')->insert($medicineData);
                $medId = $db->insertID();

                // Optional Initial Batch & Stock
                $initBatchNo = trim($this->request->getPost('initial_batch_no') ?? '');
                $initStock   = (int) ($this->request->getPost('initial_stock') ?? 0);
                $initBuy     = (float) ($this->request->getPost('initial_buy_price') ?? 0);
                $initExp     = $this->request->getPost('initial_expired_date') ?? '';

                if ($initStock > 0 && !empty($initBatchNo)) {
                    $db->table('medicine_batches')->insert([
                        'medicine_id'  => $medId,
                        'batch_no'     => $initBatchNo,
                        'buy_price'    => $initBuy,
                        'stock'        => $initStock,
                        'expired_date' => $initExp ?: date('Y-m-d', strtotime('+1 year'))
                    ]);
                    $batchId = $db->insertID();

                    $db->table('stock_movements')->insert([
                        'medicine_id'      => $medId,
                        'batch_id'         => $batchId,
                        'transaction_type' => 'pembelian',
                        'qty_in'           => $initStock,
                        'qty_out'          => 0,
                        'balance'          => $initStock,
                        'user_id'          => session('user_id') ?: 1
                    ]);
                }

                session()->setFlashdata('success', 'Master obat "' . $name . '" berhasil didaftarkan.');
                return redirect()->to(base_url('apotek/stok'));
            }

            // 2. Edit Master Obat
            if ($action === 'edit_medicine') {
                $id   = (int) $this->request->getPost('id');
                $code = trim($this->request->getPost('code'));
                $name = trim($this->request->getPost('name'));

                $db->table('medicines')->where('id', $id)->update([
                    'code'      => $code,
                    'name'      => $name,
                    'type'      => $this->request->getPost('type'),
                    'unit'      => $this->request->getPost('unit'),
                    'price'     => (float) $this->request->getPost('price'),
                    'min_stock' => (int) $this->request->getPost('min_stock'),
                    'status'    => $this->request->getPost('status')
                ]);

                session()->setFlashdata('success', 'Data master obat "' . $name . '" berhasil diperbarui.');
                return redirect()->to(base_url('apotek/stok'));
            }

            // 3. Hapus Master Obat
            if ($action === 'delete_medicine') {
                $id = (int) $this->request->getPost('id');

                // Check relations in prescription details
                $hasPrescription = $db->table('prescription_details')->where('medicine_id', $id)->countAllResults();
                if ($hasPrescription > 0) {
                    // Soft delete / inactivate instead
                    $db->table('medicines')->where('id', $id)->update(['status' => 'inactive']);
                    session()->setFlashdata('warning', 'Obat telah memiliki riwayat transaksi resep. Status otomatis diubah menjadi NONAKTIF.');
                } else {
                    $db->table('stock_movements')->where('medicine_id', $id)->delete();
                    $db->table('medicine_batches')->where('medicine_id', $id)->delete();
                    $db->table('medicines')->where('id', $id)->delete();
                    session()->setFlashdata('success', 'Master obat berhasil dihapus dari sistem.');
                }

                return redirect()->to(base_url('apotek/stok'));
            }

            // 4. Tambah Batch Obat Baru (Penerimaan Stok Masuk)
            if ($action === 'add_batch') {
                $medId   = (int) $this->request->getPost('medicine_id');
                $batchNo = trim($this->request->getPost('batch_no'));
                $buy     = (float) $this->request->getPost('buy_price');
                $stock   = (int) $this->request->getPost('stock');
                $exp     = $this->request->getPost('expired_date');

                $batchData = [
                    'medicine_id'  => $medId,
                    'batch_no'     => $batchNo,
                    'buy_price'    => $buy,
                    'stock'        => $stock,
                    'expired_date' => $exp
                ];

                $db->table('medicine_batches')->insert($batchData);
                $batchId = $db->insertID();

                // Calculate cumulative stock for this medicine
                $currentTotal = (int) $db->table('medicine_batches')->where('medicine_id', $medId)->selectSum('stock')->get()->getRow()->stock;

                // Add Stock Movement
                $db->table('stock_movements')->insert([
                    'medicine_id'      => $medId,
                    'batch_id'         => $batchId,
                    'transaction_type' => 'pembelian',
                    'qty_in'           => $stock,
                    'qty_out'          => 0,
                    'balance'          => $currentTotal,
                    'user_id'          => session('user_id') ?: 1
                ]);

                session()->setFlashdata('success', 'Batch obat ' . $batchNo . ' (' . $stock . ' unit) berhasil ditambahkan.');
                return redirect()->to(base_url('apotek/stok'));
            }

            // 5. Edit Batch Obat
            if ($action === 'edit_batch') {
                $batchId = (int) $this->request->getPost('batch_id');
                $batchNo = trim($this->request->getPost('batch_no'));
                $buy     = (float) $this->request->getPost('buy_price');
                $newStock = (int) $this->request->getPost('stock');
                $exp     = $this->request->getPost('expired_date');

                $currentBatch = $db->table('medicine_batches')->where('id', $batchId)->get()->getRow();
                if ($currentBatch) {
                    $diff = $newStock - (int)$currentBatch->stock;

                    $db->table('medicine_batches')->where('id', $batchId)->update([
                        'batch_no'     => $batchNo,
                        'buy_price'    => $buy,
                        'stock'        => $newStock,
                        'expired_date' => $exp
                    ]);

                    // If stock was adjusted, record movement
                    if ($diff !== 0) {
                        $currentTotal = (int) $db->table('medicine_batches')->where('medicine_id', $currentBatch->medicine_id)->selectSum('stock')->get()->getRow()->stock;

                        $db->table('stock_movements')->insert([
                            'medicine_id'      => $currentBatch->medicine_id,
                            'batch_id'         => $batchId,
                            'transaction_type' => 'adjustment',
                            'qty_in'           => $diff > 0 ? $diff : 0,
                            'qty_out'          => $diff < 0 ? abs($diff) : 0,
                            'balance'          => $currentTotal,
                            'user_id'          => session('user_id') ?: 1
                        ]);
                    }

                    session()->setFlashdata('success', 'Batch obat ' . $batchNo . ' berhasil diperbarui.');
                }

                return redirect()->to(base_url('apotek/stok'));
            }

            // 6. Hapus Batch Obat
            if ($action === 'delete_batch') {
                $batchId = (int) $this->request->getPost('batch_id');
                $batch = $db->table('medicine_batches')->where('id', $batchId)->get()->getRow();
                if ($batch) {
                    $db->table('stock_movements')->where('batch_id', $batchId)->delete();
                    $db->table('medicine_batches')->where('id', $batchId)->delete();
                    session()->setFlashdata('success', 'Batch obat ' . $batch->batch_no . ' berhasil dihapus.');
                }
                return redirect()->to(base_url('apotek/stok'));
            }
        }

        // AJAX Handler untuk DataTables Server-Side Master Obat
        if ($this->request->getGet('draw')) {
            return datatable_server_side('medicines', [
                0 => 'medicines.id',
                1 => 'medicines.code',
                2 => 'medicines.name',
                3 => 'medicines.unit',
                4 => 'medicines.price',
                5 => 'medicines.id',
                6 => 'medicines.id',
                7 => 'total_stock',
                8 => 'medicines.status',
                9 => 'medicines.id'
            ], [
                'select' => 'medicines.*, COALESCE(SUM(medicine_batches.stock), 0) as total_stock, COUNT(medicine_batches.id) as batch_count',
                'joins'  => [
                    ['table' => 'medicine_batches', 'cond' => 'medicine_batches.medicine_id = medicines.id', 'type' => 'left']
                ],
                'search_columns' => ['medicines.code', 'medicines.name', 'medicines.type', 'medicines.unit'],
                'default_order'  => ['medicines.name', 'ASC'],
                'row_formatter'  => function($m, $no) {
                    $typeBadge = '<span class="badge badge-light border">' . strtoupper(esc($m->type)) . '</span>';
                    $statusBadge = $m->status === 'active' ? '<span class="badge badge-success">AKTIF</span>' : '<span class="badge badge-secondary">NONAKTIF</span>';
                    $stockBadge = $m->total_stock <= $m->min_stock 
                        ? '<span class="badge badge-danger font-weight-bold px-2 py-1" style="font-size: 13px;">' . number_format($m->total_stock, 0, ',', '.') . '</span><br><small class="text-danger font-weight-bold"><i class="fas fa-triangle-exclamation"></i> Kritis</small>'
                        : '<span class="badge badge-teal font-weight-bold px-2 py-1" style="font-size: 13px;">' . number_format($m->total_stock, 0, ',', '.') . '</span>';
                    
                    $actions = '
                        <button class="btn btn-outline-teal btn-xs font-weight-bold btn-add-batch-direct shadow-none mr-1" data-id="' . $m->id . '" data-name="' . esc($m->name) . '" data-unit="' . esc($m->unit) . '" title="Input Batch Penerimaan Baru">
                            <i class="fas fa-plus"></i> Batch
                        </button>
                        <button class="btn btn-outline-info btn-xs font-weight-bold btn-edit-medicine shadow-none mr-1" 
                                data-id="' . $m->id . '" 
                                data-code="' . esc($m->code) . '" 
                                data-name="' . esc($m->name) . '" 
                                data-type="' . esc($m->type) . '" 
                                data-unit="' . esc($m->unit) . '" 
                                data-price="' . (float)$m->price . '" 
                                data-min="' . (int)$m->min_stock . '" 
                                data-status="' . esc($m->status) . '" 
                                title="Edit Master Obat">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . base_url('apotek/stok') . '" method="post" class="d-inline-block" onsubmit="return confirm(\'Hapus master obat ' . esc($m->name) . '?\');">
                            <input type="hidden" name="action" value="delete_medicine">
                            <input type="hidden" name="id" value="' . $m->id . '">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-outline-danger btn-xs font-weight-bold shadow-none" title="Hapus Master Obat"><i class="fas fa-trash"></i></button>
                        </form>
                    ';

                    return [
                        '<span class="font-weight-bold text-muted">' . $no . '</span>',
                        '<span class="badge badge-secondary font-monospace">' . esc($m->code) . '</span>',
                        '<div><strong class="text-dark" style="font-size: 13.5px;">' . esc($m->name) . '</strong><br><small class="text-muted">Golongan: ' . $typeBadge . ' | Limit Min: <span class="text-danger font-weight-bold">' . esc($m->min_stock) . '</span> | ' . $m->batch_count . ' Batch</small></div>',
                        '<span class="badge badge-light border text-uppercase">' . esc($m->unit) . '</span>',
                        '<div class="text-right font-weight-bold font-monospace">Rp ' . number_format($m->price, 0, ',', '.') . '</div>',
                        '<div class="text-center font-weight-bold text-success font-monospace">' . number_format($m->total_stock, 0, ',', '.') . '</div>',
                        '<div class="text-center font-weight-bold text-danger font-monospace">-</div>',
                        '<div class="text-center">' . $stockBadge . '</div>',
                        '<div class="text-center">' . $statusBadge . '</div>',
                        '<div class="text-center text-nowrap">' . $actions . '</div>'
                    ];
                }
            ]);
        }

        // Fetch medicines with cumulative in, out, and current stock analytics
        $medicines = $db->table('medicines')
                        ->select('medicines.*, 
                                  COALESCE(SUM(medicine_batches.stock), 0) as total_stock,
                                  COUNT(medicine_batches.id) as batch_count')
                        ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                        ->groupBy('medicines.id')
                        ->orderBy('medicines.name', 'ASC')
                        ->get()
                        ->getResult();

        // Calculate total in and total out per medicine from stock_movements
        $movementsSummary = $db->table('stock_movements')
                               ->select('medicine_id, 
                                         COALESCE(SUM(qty_in), 0) as total_in, 
                                         COALESCE(SUM(qty_out), 0) as total_out')
                               ->groupBy('medicine_id')
                               ->get()
                               ->getResult();
        $movementMap = [];
        foreach ($movementsSummary as $mv) {
            $movementMap[$mv->medicine_id] = $mv;
        }

        foreach ($medicines as &$m) {
            $m->total_in  = isset($movementMap[$m->id]) ? (int)$movementMap[$m->id]->total_in : (int)$m->total_stock;
            $m->total_out = isset($movementMap[$m->id]) ? (int)$movementMap[$m->id]->total_out : 0;
        }

        // Fetch detailed batches
        $batches = $db->table('medicine_batches')
                      ->select('medicine_batches.*, medicines.name as medicine_name, medicines.code as medicine_code, medicines.unit, medicines.type')
                      ->join('medicines', 'medicines.id = medicine_batches.medicine_id')
                      ->orderBy('medicine_batches.expired_date', 'ASC')
                      ->get()
                      ->getResult();

        $medicineCategories = $db->table('medicine_categories')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $units = $db->table('units')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();

        $data = [
            'title'              => 'Manajemen Persediaan & Master Obat',
            'active_menu'        => 'apotek-stok',
            'medicines'          => $medicines,
            'batches'            => $batches,
            'medicineCategories' => $medicineCategories,
            'units'              => $units
        ];

        return view('apotek/stok', $data);
    }

    public function cetakEtiket($id)
    {
        $db = \Config\Database::connect('default');

        // Query prescription either by prescription ID or visit ID
        $prescription = $db->table('prescriptions')
                           ->select('prescriptions.*, 
                                     patient_visits.no_visit, 
                                     patient_visits.visit_date,
                                     patients.name as patient_name, 
                                     patients.no_rm, 
                                     patients.date_of_birth,
                                     patients.gender,
                                     COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name')
                           ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                           ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                           ->where('prescriptions.id', $id)
                           ->orWhere('prescriptions.visit_id', $id)
                           ->get()
                           ->getRow();

        if (!$prescription) {
            session()->setFlashdata('error', 'Data resep atau etiket obat tidak ditemukan.');
            return redirect()->to(base_url('apotek/resep'));
        }

        $details = $db->table('prescription_details')
                      ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                      ->join('medicines', 'medicines.id = prescription_details.medicine_id')
                      ->where('prescription_id', $prescription->id)
                      ->get()
                      ->getResult();

        $data = [
            'title'        => 'Cetak E-Tiket Obat Pasien - ' . $prescription->patient_name,
            'prescription' => $prescription,
            'details'      => $details
        ];

        return view('apotek/cetak_etiket', $data);
    }

    /**
     * AJAX Endpoint: Ambil rincian lengkap e-resep (Item obat, dosis, tusla, embalase, dan status bayar)
     */
    public function getPrescriptionDetailJson($id)
    {
        $db = \Config\Database::connect('default');
        $prescription = $db->table('prescriptions')
                           ->select('prescriptions.*, 
                                     patient_visits.no_visit, 
                                     patient_visits.visit_date,
                                     patients.name as patient_name, 
                                     patients.no_rm, 
                                     patients.gender,
                                     patients.date_of_birth,
                                     patients.phone,
                                     patients.address,
                                     COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name,
                                     COALESCE(bt.status, "open") as billing_status,
                                     COALESCE(bt.billing_no, "-") as billing_no,
                                     (CASE WHEN bt.status = "paid" THEN 1 ELSE 0 END) as is_paid,
                                     bt.payment_method,
                                     bt.id as billing_id,
                                     bt.grand_total as billing_grand_total,
                                     bt.total_medicines as billing_total_medicines,
                                     ct.receipt_no')
                           ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                           ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                           ->join('billing_transactions bt', 'bt.visit_id = prescriptions.visit_id', 'left')
                           ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                           ->where('prescriptions.id', $id)
                           ->get()
                           ->getRow();

        if (!$prescription) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Resep tidak ditemukan.']);
        }

        $details = $db->table('prescription_details')
                      ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                      ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                      ->where('prescription_id', $prescription->id)
                      ->get()
                      ->getResult();

        $totalGross = 0; $totalTusla = 0; $totalEmbalase = 0; $totalDiscount = 0;
        foreach ($details as &$d) {
            $d->batches = $db->table('medicine_batches')
                             ->where('medicine_id', $d->medicine_id)
                             ->where('stock >', 0)
                             ->orderBy('expired_date', 'ASC')
                             ->get()
                             ->getResult();

            $d->subtotal = max(0, ($d->qty * $d->price) + ($d->tusla ?? 0) + ($d->embalase ?? 0) - ($d->discount ?? 0));
            $totalGross += ($d->qty * $d->price);
            $totalTusla += (float)($d->tusla ?? 0);
            $totalEmbalase += (float)($d->embalase ?? 0);
            $totalDiscount += (float)($d->discount ?? 0);
        }

        $grandTotal = max(0, $totalGross + $totalTusla + $totalEmbalase - $totalDiscount);

        return $this->response->setJSON([
            'status'         => 'success',
            'prescription'   => $prescription,
            'details'        => $details,
            'summary'        => [
                'total_gross'    => $totalGross,
                'total_tusla'    => $totalTusla,
                'total_embalase' => $totalEmbalase,
                'total_discount' => $totalDiscount,
                'grand_total'    => $grandTotal,
            ]
        ]);
    }

    /**
     * Cetak Struk / Nota Termal e-Resep Pasien (Format 80mm / 58mm POS Printer)
     */
    public function cetakStrukResep($id)
    {
        $db = \Config\Database::connect('default');

        $prescription = $db->table('prescriptions')
                           ->select('prescriptions.*, 
                                     patient_visits.no_visit, 
                                     patient_visits.visit_date,
                                     patients.name as patient_name, 
                                     patients.no_rm, 
                                     patients.date_of_birth,
                                     patients.gender,
                                     COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name,
                                     COALESCE(bt.billing_no, "-") as billing_no,
                                     COALESCE(bt.status, "open") as billing_status,
                                     COALESCE(bt.payment_method, "tunai") as payment_method,
                                     bt.grand_total as billing_grand_total,
                                     bt.total_medicines as billing_total_medicines,
                                     ct.receipt_no')
                           ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                           ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                           ->join('billing_transactions bt', 'bt.visit_id = prescriptions.visit_id', 'left')
                           ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                           ->where('prescriptions.id', $id)
                           ->orWhere('prescriptions.visit_id', $id)
                           ->get()
                           ->getRow();

        if (!$prescription) {
            session()->setFlashdata('error', 'Data resep tidak ditemukan.');
            return redirect()->to(base_url('apotek/resep'));
        }

        $details = $db->table('prescription_details')
                      ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                      ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                      ->where('prescription_id', $prescription->id)
                      ->get()
                      ->getResult();

        $totalGross = 0; $totalTusla = 0; $totalEmbalase = 0; $totalDisc = 0;
        foreach ($details as &$d) {
            $d->subtotal = max(0, ($d->qty * $d->price) + ($d->tusla ?? 0) + ($d->embalase ?? 0) - ($d->discount ?? 0));
            $totalGross += ($d->qty * $d->price);
            $totalTusla += (float)($d->tusla ?? 0);
            $totalEmbalase += (float)($d->embalase ?? 0);
            $totalDisc += (float)($d->discount ?? 0);
        }

        $grandTotal = max(0, $totalGross + $totalTusla + $totalEmbalase - $totalDisc);

        $data = [
            'title'        => 'Struk e-Resep ' . ($prescription->patient_name ?? ''),
            'prescription' => $prescription,
            'details'      => $details,
            'summary'      => [
                'total_gross'    => $totalGross,
                'total_tusla'    => $totalTusla,
                'total_embalase' => $totalEmbalase,
                'total_discount' => $totalDisc,
                'grand_total'    => $grandTotal
            ]
        ];

        return view('apotek/cetak_struk_resep', $data);
    }

    /**
     * Cetak Kwitansi Resmi Pembayaran Resep Obat Pasien (Format Dokumen Resmi Siap Cetak A4 / Letter)
     */
    public function cetakKwitansiResep($id)
    {
        $db = \Config\Database::connect('default');

        $prescription = $db->table('prescriptions')
                           ->select('prescriptions.*, 
                                     patient_visits.no_visit, 
                                     patient_visits.visit_date,
                                     patients.name as patient_name, 
                                     patients.no_rm, 
                                     patients.nik,
                                     patients.phone,
                                     patients.address,
                                     patients.date_of_birth,
                                     patients.gender,
                                     polyclinics.name as poly_name,
                                     COALESCE(doctors.name, users.username, "Dokter Pemeriksa") as doctor_name,
                                     bt.id as billing_id,
                                     COALESCE(bt.billing_no, "-") as billing_no,
                                     COALESCE(bt.status, "open") as billing_status,
                                     COALESCE(bt.payment_method, "tunai") as payment_method,
                                     bt.grand_total as billing_grand_total,
                                     bt.total_medicines as billing_total_medicines,
                                     ct.receipt_no,
                                     ct.amount as cash_amount,
                                     ct.paid_amount,
                                     ct.change_amount,
                                     cashier.username as cashier_name')
                           ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                           ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                           ->join('users', 'users.id = prescriptions.doctor_id', 'left')
                           ->join('billing_transactions bt', 'bt.visit_id = prescriptions.visit_id', 'left')
                           ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                           ->join('users cashier', 'cashier.id = ct.cashier_id', 'left')
                           ->where('prescriptions.id', $id)
                           ->orWhere('prescriptions.visit_id', $id)
                           ->get()
                           ->getRow();

        if (!$prescription) {
            session()->setFlashdata('error', 'Data resep tidak ditemukan.');
            return redirect()->to(base_url('apotek/resep'));
        }

        $details = $db->table('prescription_details')
                      ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                      ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                      ->where('prescription_id', $prescription->id)
                      ->get()
                      ->getResult();

        $totalGross = 0; $totalTusla = 0; $totalEmbalase = 0; $totalDisc = 0;
        foreach ($details as &$d) {
            $d->subtotal = max(0, ($d->qty * $d->price) + ($d->tusla ?? 0) + ($d->embalase ?? 0) - ($d->discount ?? 0));
            $totalGross += ($d->qty * $d->price);
            $totalTusla += (float)($d->tusla ?? 0);
            $totalEmbalase += (float)($d->embalase ?? 0);
            $totalDisc += (float)($d->discount ?? 0);
        }

        $grandTotal = max(0, $totalGross + $totalTusla + $totalEmbalase - $totalDisc);

        $data = [
            'title'        => 'Kwitansi Resep ' . ($prescription->patient_name ?? ''),
            'prescription' => $prescription,
            'details'      => $details,
            'summary'      => [
                'total_gross'    => $totalGross,
                'total_tusla'    => $totalTusla,
                'total_embalase' => $totalEmbalase,
                'total_discount' => $totalDisc,
                'grand_total'    => $grandTotal
            ]
        ];

        return view('apotek/cetak_kwitansi_resep', $data);
    }

    public function opname()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $notes = $this->request->getPost('notes');
            $items = $this->request->getPost('items'); // Array: [batch_id => ['medicine_id' => X, 'system_stock' => Y, 'physical_stock' => Z, 'reason' => '...']]

            if (empty($items) || !is_array($items)) {
                session()->setFlashdata('error', 'Tidak ada data stok fisik obat yang dihitung.');
                return redirect()->to(base_url('apotek/opname'));
            }

            $db->transStart();

            // 1. Generate Opname Number (SO-YYYYMMDD-XXXX)
            $today = date('Ymd');
            $lastOpname = $db->table('stock_opnames')
                             ->where('DATE(created_at)', date('Y-m-d'))
                             ->orderBy('id', 'DESC')
                             ->limit(1)
                             ->get()
                             ->getRow();
            $nextNum = 1;
            if ($lastOpname && preg_match('/SO-\d+-(\d+)/', $lastOpname->opname_no, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            }
            $opnameNo = 'SO-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // 2. Insert Opname Header
            $db->table('stock_opnames')->insert([
                'opname_no'          => $opnameNo,
                'opname_date'        => date('Y-m-d'),
                'user_id'            => session('user_id') ?: 1,
                'status'             => 'adjusted',
                'total_items'        => 0,
                'total_discrepancy'  => 0,
                'notes'              => $notes
            ]);
            $opnameId = $db->insertID();

            $totalItemsCount = 0;
            $totalDiscrepancySum = 0;

            // 3. Process each counted item
            foreach ($items as $batchId => $item) {
                // Only process items where physical_stock is explicitly provided (not empty string)
                if (!isset($item['physical_stock']) || $item['physical_stock'] === '') {
                    continue;
                }

                $medicineId    = (int) $item['medicine_id'];
                $systemStock   = (int) $item['system_stock'];
                $physicalStock = (int) $item['physical_stock'];
                $difference    = $physicalStock - $systemStock;
                $reason        = trim($item['reason'] ?? '');

                $totalItemsCount++;
                $totalDiscrepancySum += abs($difference);

                // Insert into stock_opname_details
                $db->table('stock_opname_details')->insert([
                    'opname_id'      => $opnameId,
                    'medicine_id'    => $medicineId,
                    'batch_id'       => $batchId,
                    'system_stock'   => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference'     => $difference,
                    'reason'         => $reason
                ]);

                // Update physical stock in medicine_batches
                $db->table('medicine_batches')
                   ->where('id', $batchId)
                   ->update(['stock' => $physicalStock]);

                // Log movement if there was a discrepancy
                if ($difference !== 0) {
                    $db->table('stock_movements')->insert([
                        'medicine_id'      => $medicineId,
                        'batch_id'         => $batchId,
                        'transaction_type' => 'opname',
                        'reference_id'     => $opnameId,
                        'qty_in'           => $difference > 0 ? $difference : 0,
                        'qty_out'          => $difference < 0 ? abs($difference) : 0,
                        'balance'          => $physicalStock,
                        'user_id'          => session('user_id') ?: 1
                    ]);
                }
            }

            // Update Opname Totals
            $db->table('stock_opnames')
               ->where('id', $opnameId)
               ->update([
                   'total_items'       => $totalItemsCount,
                   'total_discrepancy' => $totalDiscrepancySum
               ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Gagal memproses penyesuaian Stock Opname.');
            } else {
                session()->setFlashdata('success', 'Stock Opname ' . $opnameNo . ' berhasil disimpan! ' . $totalItemsCount . ' item obat telah diselaraskan dengan stok fisik.');
                session()->setFlashdata('last_opname_id', $opnameId);
            }

            return redirect()->to(base_url('apotek/opname'));
        }

        // Fetch current active medicines & batches for count sheet
        $batches = $db->table('medicine_batches')
                      ->select('medicine_batches.*, medicines.code as medicine_code, medicines.name as medicine_name, medicines.unit, medicines.type')
                      ->join('medicines', 'medicines.id = medicine_batches.medicine_id')
                      ->where('medicines.status', 'active')
                      ->orderBy('medicines.name', 'ASC')
                      ->orderBy('medicine_batches.expired_date', 'ASC')
                      ->get()
                      ->getResult();

        // Fetch historical opnames
        $opnameHistory = $db->table('stock_opnames')
                            ->select('stock_opnames.*, users.username as staff_name')
                            ->join('users', 'users.id = stock_opnames.user_id', 'left')
                            ->orderBy('stock_opnames.id', 'DESC')
                            ->get()
                            ->getResult();

        // Fetch opname details for modal breakdown
        $opnameDetails = [];
        if (!empty($opnameHistory)) {
            $allOpnameIds = array_column($opnameHistory, 'id');
            $details = $db->table('stock_opname_details')
                          ->select('stock_opname_details.*, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no, medicine_batches.expired_date')
                          ->join('medicines', 'medicines.id = stock_opname_details.medicine_id')
                          ->join('medicine_batches', 'medicine_batches.id = stock_opname_details.batch_id')
                          ->whereIn('opname_id', $allOpnameIds)
                          ->get()
                          ->getResult();
            foreach ($details as $d) {
                $opnameDetails[$d->opname_id][] = $d;
            }
        }

        $data = [
            'title'         => 'Stock Opname Obat & Penyesuaian Fisik',
            'active_menu'   => 'apotek-opname',
            'batches'       => $batches,
            'opnameHistory' => $opnameHistory,
            'opnameDetails' => $opnameDetails
        ];

        return view('apotek/opname', $data);
    }

    public function cetakOpname($id)
    {
        $db = \Config\Database::connect('default');

        $opname = $db->table('stock_opnames')
                     ->select('stock_opnames.*, users.username as staff_name')
                     ->join('users', 'users.id = stock_opnames.user_id', 'left')
                     ->where('stock_opnames.id', $id)
                     ->get()
                     ->getRow();

        if (!$opname) {
            session()->setFlashdata('error', 'Dokumen Stock Opname tidak ditemukan.');
            return redirect()->to(base_url('apotek/opname'));
        }

        $details = $db->table('stock_opname_details')
                      ->select('stock_opname_details.*, medicines.code as medicine_code, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no, medicine_batches.expired_date')
                      ->join('medicines', 'medicines.id = stock_opname_details.medicine_id')
                      ->join('medicine_batches', 'medicine_batches.id = stock_opname_details.batch_id')
                      ->where('opname_id', $opname->id)
                      ->get()
                      ->getResult();

        $data = [
            'title'   => 'Berita Acara Stock Opname ' . $opname->opname_no,
            'opname'  => $opname,
            'details' => $details
        ];

        return view('apotek/cetak_opname', $data);
    }

    public function laporan()
    {
        $db = \Config\Database::connect('default');

        $startDate   = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate     = $this->request->getGet('end_date') ?: date('Y-m-d');
        $medFilter   = $this->request->getGet('medicine_id');
        $doctorFilter= $this->request->getGet('doctor_id');
        $saleType    = $this->request->getGet('sale_type'); // 'otc', 'resep', or empty=all

        // 1. Tab 1: Mutasi & Kartu Stok Obat
        $movementsBuilder = $db->table('stock_movements')
                               ->select('stock_movements.*, medicines.code as medicine_code, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no, medicine_batches.expired_date, users.username as user_name')
                               ->join('medicines', 'medicines.id = stock_movements.medicine_id')
                               ->join('medicine_batches', 'medicine_batches.id = stock_movements.batch_id', 'left')
                               ->join('users', 'users.id = stock_movements.user_id', 'left')
                               ->where('DATE(stock_movements.created_at) >=', $startDate)
                               ->where('DATE(stock_movements.created_at) <=', $endDate);

        if (!empty($medFilter)) {
            $movementsBuilder->where('stock_movements.medicine_id', $medFilter);
        }
        $movements = $movementsBuilder->orderBy('stock_movements.id', 'DESC')->get()->getResult();

        // 2. Tab 2: Laporan Pemakaian & Penjualan Obat (Resep Terlayani dari Klinik)
        $dispensedBuilder = $db->table('prescription_details')
                        ->select('prescription_details.*, 
                                  prescriptions.created_at as dispensed_date,
                                  medicines.code as medicine_code,
                                  medicines.name as medicine_name,
                                  medicines.unit,
                                  patient_visits.no_visit,
                                  patients.name as patient_name,
                                  patients.no_rm,
                                  COALESCE(doctors.name, "Dokter Pemeriksa") as doctor_name')
                        ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id')
                        ->join('medicines', 'medicines.id = prescription_details.medicine_id')
                        ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                        ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                        ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                        ->where('DATE(prescriptions.created_at) >=', $startDate)
                        ->where('DATE(prescriptions.created_at) <=', $endDate)
                        ->where('prescription_details.status', 'served');

        if (!empty($doctorFilter)) {
            $dispensedBuilder->where('prescriptions.doctor_id', $doctorFilter);
        }
        $dispensed = $dispensedBuilder->orderBy('prescriptions.id', 'DESC')->get()->getResult();

        $totalDispensedQty = 0;
        $totalDispensedAmount = 0;
        foreach ($dispensed as $dsp) {
            $totalDispensedQty += (int) $dsp->qty;
            $totalDispensedAmount += ((float)$dsp->price * (int)$dsp->qty);
        }

        // 3. Tab 3: Monitoring Kadaluarsa & FEFO
        $expiryBatches = $db->table('medicine_batches')
                            ->select('medicine_batches.*, medicines.code, medicines.code as medicine_code, medicines.name, medicines.name as medicine_name, medicines.unit, medicines.type, medicines.price')
                            ->join('medicines', 'medicines.id = medicine_batches.medicine_id')
                            ->where('medicine_batches.stock >', 0)
                            ->orderBy('medicine_batches.expired_date', 'ASC')
                            ->get()->getResult();

        $criticalCount = 0;
        $warningCount = 0;
        $now = time();
        foreach ($expiryBatches as &$eb) {
            $expTimestamp = strtotime($eb->expired_date);
            $daysLeft = ceil(($expTimestamp - $now) / 86400);
            $eb->days_left = $daysLeft;
            if ($daysLeft <= 0)        { $eb->risk_status = 'expired';  $criticalCount++; }
            elseif ($daysLeft <= 30)   { $eb->risk_status = 'critical'; $criticalCount++; }
            elseif ($daysLeft <= 90)   { $eb->risk_status = 'warning';  $warningCount++;  }
            else                       { $eb->risk_status = 'safe'; }
        }

        // 4. Tab 4: Top 10 Fast-Moving Medicines
        $topMedicines = $db->table('prescription_details')
                           ->select('medicines.id, medicines.code, medicines.name, medicines.unit, medicines.type, SUM(prescription_details.qty) as total_qty, COUNT(prescription_details.id) as freq_count')
                           ->join('medicines', 'medicines.id = prescription_details.medicine_id')
                           ->where('prescription_details.status', 'served')
                           ->groupBy('medicines.id')
                           ->orderBy('total_qty', 'DESC')
                           ->limit(10)->get()->getResult();

        // 5. Tab 5: Penjualan Apotek OTC (pharmacy_sales) — dengan breakdown Tusla, Embalase, Fee Dokter
        $otcSalesBuilder = $db->table('pharmacy_sales')
                              ->select('pharmacy_sales.*, 
                                        COALESCE(doctors.name, "") as doctor_name,
                                        users.username as cashier_name')
                              ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                              ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                              ->where('DATE(pharmacy_sales.created_at) >=', $startDate)
                              ->where('DATE(pharmacy_sales.created_at) <=', $endDate);

        if (!empty($saleType) && $saleType === 'otc') {
            $otcSalesBuilder->where('pharmacy_sales.doctor_id IS NULL');
        } elseif (!empty($saleType) && $saleType === 'resep') {
            $otcSalesBuilder->where('pharmacy_sales.doctor_id IS NOT NULL');
        }
        if (!empty($doctorFilter)) {
            $otcSalesBuilder->where('pharmacy_sales.doctor_id', $doctorFilter);
        }

        $otcSales = $otcSalesBuilder->orderBy('pharmacy_sales.id', 'DESC')->get()->getResult();

        $otcSummary = [
            'total_sales'    => 0,
            'total_obat'     => 0,
            'total_tusla'    => 0,
            'total_embalase' => 0,
            'total_discount' => 0,
            'total_grand'    => 0,
            'total_fee_doc'  => 0,
            'count'          => count($otcSales),
        ];
        foreach ($otcSales as $os) {
            $otcSummary['total_sales']    += (float)$os->total_amount;
            $otcSummary['total_tusla']    += (float)($os->tusla_amount ?? 0);
            $otcSummary['total_embalase'] += (float)($os->embalase_amount ?? 0);
            $otcSummary['total_discount'] += (float)($os->discount_amount ?? 0);
            $otcSummary['total_grand']    += (float)$os->grand_total;
            // Estimate fee dokter 5% if has doctor
            if (!empty($os->doctor_id)) {
                $otcSummary['total_fee_doc'] += round((float)$os->total_amount * 0.05, 2);
            }
        }
        $otcSummary['total_obat'] = $otcSummary['total_grand']
                                  - $otcSummary['total_tusla']
                                  - $otcSummary['total_embalase']
                                  + $otcSummary['total_discount'];

        // Doctors list for filter dropdown
        $doctorsList   = $db->table('doctors')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $medicinesList = $db->table('medicines')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();

        $data = [
            'title'                => 'Laporan & Analitik Farmasi Apotek',
            'active_menu'          => 'apotek-laporan',
            'startDate'            => $startDate,
            'endDate'              => $endDate,
            'medFilter'            => $medFilter,
            'doctorFilter'         => $doctorFilter,
            'saleType'             => $saleType,
            'movements'            => $movements,
            'dispensed'            => $dispensed,
            'totalDispensedQty'    => $totalDispensedQty,
            'totalDispensedAmount' => $totalDispensedAmount,
            'expiryBatches'        => $expiryBatches,
            'criticalCount'        => $criticalCount,
            'warningCount'         => $warningCount,
            'topMedicines'         => $topMedicines,
            'medicinesList'        => $medicinesList,
            'doctorsList'          => $doctorsList,
            'otcSales'             => $otcSales,
            'otcSummary'           => $otcSummary,
        ];

        return view('apotek/laporan', $data);
    }

    public function cetakLaporan()
    {
        $db = \Config\Database::connect('default');

        $type      = $this->request->getGet('type') ?: 'pemakaian';
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        if ($type === 'mutasi') {
            $reportTitle = 'Laporan Mutasi Kartu Stok Obat';
            $dataRows = $db->table('stock_movements')
                           ->select('stock_movements.*, medicines.code, medicines.code as medicine_code, medicines.name, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no, users.username as user_name')
                           ->join('medicines', 'medicines.id = stock_movements.medicine_id')
                           ->join('medicine_batches', 'medicine_batches.id = stock_movements.batch_id', 'left')
                           ->join('users', 'users.id = stock_movements.user_id', 'left')
                           ->where('DATE(stock_movements.created_at) >=', $startDate)
                           ->where('DATE(stock_movements.created_at) <=', $endDate)
                           ->orderBy('stock_movements.id', 'DESC')
                           ->get()
                           ->getResult();
        } elseif ($type === 'expired') {
            $reportTitle = 'Laporan Monitoring Kadaluarsa & Persediaan Obat';
            $dataRows = $db->table('medicine_batches')
                           ->select('medicine_batches.*, medicines.code, medicines.code as medicine_code, medicines.name, medicines.name as medicine_name, medicines.unit, medicines.type, medicines.price')
                           ->join('medicines', 'medicines.id = medicine_batches.medicine_id')
                           ->where('medicine_batches.stock >', 0)
                           ->orderBy('medicine_batches.expired_date', 'ASC')
                           ->get()
                           ->getResult();
        } else {
            $reportTitle = 'Laporan Pemakaian & Penjualan Obat Farmasi';
            $dataRows = $db->table('prescription_details')
                           ->select('prescription_details.*, 
                                     prescriptions.created_at as dispensed_date,
                                     medicines.code,
                                     medicines.code as medicine_code,
                                     medicines.name,
                                     medicines.name as medicine_name,
                                     medicines.unit,
                                     patient_visits.no_visit,
                                     patients.name as patient_name,
                                     patients.no_rm,
                                     COALESCE(doctors.name, "Dokter Pemeriksa") as doctor_name')
                           ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id')
                           ->join('medicines', 'medicines.id = prescription_details.medicine_id')
                           ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                           ->where('DATE(prescriptions.created_at) >=', $startDate)
                           ->where('DATE(prescriptions.created_at) <=', $endDate)
                           ->where('prescription_details.status', 'served')
                           ->orderBy('prescriptions.id', 'DESC')
                           ->get()
                           ->getResult();
        }

        // 5. OTC Pharmacy Sales Report
        if ($type === 'penjualan_otc') {
            $doctorFilter = $this->request->getGet('doctor_id');
            $saleType     = $this->request->getGet('sale_type');

            $reportTitle = 'Laporan Penjualan Apotek (OTC & Resep Langsung)';
            $salesBuilder = $db->table('pharmacy_sales')
                               ->select('pharmacy_sales.*, COALESCE(doctors.name, "Non-Resep / Umum") as doctor_name, users.username as cashier_name')
                               ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                               ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                               ->where('DATE(pharmacy_sales.created_at) >=', $startDate)
                               ->where('DATE(pharmacy_sales.created_at) <=', $endDate);

            if (!empty($saleType) && $saleType === 'otc') {
                $salesBuilder->where('pharmacy_sales.doctor_id IS NULL');
            } elseif (!empty($saleType) && $saleType === 'resep') {
                $salesBuilder->where('pharmacy_sales.doctor_id IS NOT NULL');
            }
            if (!empty($doctorFilter)) {
                $salesBuilder->where('pharmacy_sales.doctor_id', $doctorFilter);
            }

            $dataRows = $salesBuilder->orderBy('pharmacy_sales.id', 'DESC')->get()->getResult();
        }

        $data = [
            'title'       => $reportTitle,
            'reportType'  => $type,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'dataRows'    => $dataRows
        ];

        return view('apotek/cetak_laporan', $data);
    }

    public function exportPenjualanCsv()
    {
        $db = \Config\Database::connect('default');

        $startDate    = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate      = $this->request->getGet('end_date') ?: date('Y-m-d');
        $doctorFilter = $this->request->getGet('doctor_id');
        $saleType     = $this->request->getGet('sale_type');

        $salesBuilder = $db->table('pharmacy_sales')
                           ->select('pharmacy_sales.*, COALESCE(doctors.name, "Non-Resep / Bebas") as doctor_name, users.username as cashier_name')
                           ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                           ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                           ->where('DATE(pharmacy_sales.created_at) >=', $startDate)
                           ->where('DATE(pharmacy_sales.created_at) <=', $endDate);

        if (!empty($saleType) && $saleType === 'otc') {
            $salesBuilder->where('pharmacy_sales.doctor_id IS NULL');
        } elseif (!empty($saleType) && $saleType === 'resep') {
            $salesBuilder->where('pharmacy_sales.doctor_id IS NOT NULL');
        }
        if (!empty($doctorFilter)) {
            $salesBuilder->where('pharmacy_sales.doctor_id', $doctorFilter);
        }

        $sales = $salesBuilder->orderBy('pharmacy_sales.id', 'DESC')->get()->getResult();

        $filename = 'Laporan_Penjualan_Apotek_' . $startDate . '_sd_' . $endDate . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header Row
        fputcsv($output, [
            'No',
            'No. Transaksi',
            'Tanggal & Waktu',
            'Nama Pembeli / Pasien',
            'No. WhatsApp',
            'Dokter Perujuk',
            'Kasir / Petugas',
            'Metode Pembayaran',
            'Total Obat (Rp)',
            'Tusla Jasa Racik (Rp)',
            'Embalase Kemasan (Rp)',
            'Diskon (Rp)',
            'Grand Total (Rp)',
            'Estimasi Fee Dokter 5% (Rp)',
            'Status Pembayaran'
        ]);

        $no = 1;
        foreach ($sales as $s) {
            $totalObat = (float)$s->grand_total - (float)($s->tusla_amount ?? 0) - (float)($s->embalase_amount ?? 0) + (float)($s->discount_amount ?? 0);
            $feeDokter = !empty($s->doctor_id) ? round((float)$s->total_amount * 0.05, 2) : 0;

            fputcsv($output, [
                $no++,
                $s->sale_no,
                date('d/m/Y H:i', strtotime($s->created_at)),
                $s->customer_name ?: 'Pelanggan Umum',
                $s->customer_phone ?: '-',
                $s->doctor_name,
                $s->cashier_name ?: 'Kasir',
                $s->payment_method ?: 'Tunai',
                $totalObat,
                (float)($s->tusla_amount ?? 0),
                (float)($s->embalase_amount ?? 0),
                (float)($s->discount_amount ?? 0),
                (float)$s->grand_total,
                $feeDokter,
                $s->payment_status ?: 'paid'
            ]);
        }

        fclose($output);
        exit;
    }

    // =========================================================================
    // MODUL PENJUALAN OBAT BEBAS / NON-RESEP (WALK-IN OTC SALES)
    // =========================================================================
    public function penjualan()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $customerName     = $this->request->getPost('customer_name');
            $customerPhone    = $this->request->getPost('customer_phone');
            $paymentMethod    = $this->request->getPost('payment_method');
            $paidAmount       = floatval($this->request->getPost('paid_amount'));
            $notes            = $this->request->getPost('notes');
            $rawDoctorId      = $this->request->getPost('doctor_id');
            $doctorId         = null;
            $doctorMode       = null;
            if (!empty($rawDoctorId)) {
                if (strpos($rawDoctorId, ':') !== false) {
                    [$docIdPart, $docModePart] = explode(':', $rawDoctorId, 2);
                    $doctorId = intval($docIdPart) ?: null;
                    $doctorMode = $docModePart;
                } else {
                    $doctorId = intval($rawDoctorId) ?: null;
                }
            }

            $prescriptionType = $this->request->getPost('prescription_type') ?: 'bebas';
            if ($doctorMode === 'online') {
                $prescriptionType = 'online';
            }
            $doctorFeeNominal = floatval($this->request->getPost('doctor_fee_nominal') ?: 0);
            $tuslaAmount      = floatval($this->request->getPost('tusla_amount') ?: 0);
            $embalaseAmount   = floatval($this->request->getPost('embalase_amount') ?: 0);
            $rawItems         = $this->request->getPost('items'); // Array of items

            $payload = [
                'customer_name'      => $customerName,
                'customer_phone'     => $customerPhone,
                'payment_method'     => $paymentMethod,
                'paid_amount'        => $paidAmount,
                'doctor_id'          => $doctorId,
                'doctor_fee_nominal' => $doctorFeeNominal,
                'prescription_type'  => $prescriptionType,
                'tusla_amount'       => $tuslaAmount,
                'embalase_amount'    => $embalaseAmount,
                'notes'              => $notes,
                'cashier_id'         => session('user_id') ?: 1,
                'items'              => $rawItems
            ];

            $res = $this->pharmacyService->processDirectSale($payload);
            if ($res['status'] === 'success') {
                session()->setFlashdata('success', $res['message']);
                session()->setFlashdata('last_sale_id', $res['sale_id']);
            } else {
                session()->setFlashdata('error', $res['message']);
            }

            return redirect()->to(base_url('apotek/penjualan'));
        }

        // Active medicines with positive total stock
        $medicines = $db->table('medicines')
                        ->select('medicines.*, COALESCE(SUM(medicine_batches.stock), 0) as total_stock')
                        ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                        ->where('medicines.status', 'active')
                        ->groupBy('medicines.id')
                        ->orderBy('medicines.name', 'ASC')
                        ->get()
                        ->getResult();

        // Get available batches mapped by medicine_id
        $batches = $db->table('medicine_batches')
                      ->where('stock >', 0)
                      ->orderBy('expired_date', 'ASC')
                      ->get()
                      ->getResult();

        $batchesMap = [];
        foreach ($batches as $b) {
            $batchesMap[$b->medicine_id][] = $b;
        }

        // Recent sales history
        $recentSales = $db->table('pharmacy_sales')
                          ->select('pharmacy_sales.*, users.username as cashier_name, doctors.name as doctor_name')
                          ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                          ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                          ->orderBy('pharmacy_sales.created_at', 'DESC')
                          ->orderBy('pharmacy_sales.id', 'DESC')
                          ->limit(50)
                          ->get()
                          ->getResult();

        $paymentMethods = $db->table('payment_methods')
                             ->where('is_active', 1)
                             ->orderBy('category', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();

        $patients = $db->table('patients')
                       ->select('id, name, no_rm, phone, COALESCE(membership_tier, "regular") as membership_tier')
                       ->orderBy('name', 'ASC')
                       ->get()
                       ->getResult();

        $doctors = $db->table('doctors')
                      ->where('status', 'active')
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResult();

        $activePending = $db->table('pharmacy_pending_prescriptions')
                            ->where('status', 'pending')
                            ->orderBy('id', 'DESC')
                            ->get()
                            ->getResult();

        $data = [
            'title'          => 'Penjualan Obat Bebas & Kasir Apotek',
            'active_menu'    => 'apotek-penjualan',
            'medicines'      => $medicines,
            'batchesMap'     => $batchesMap,
            'recentSales'    => $recentSales,
            'paymentMethods' => $paymentMethods,
            'patients'       => $patients,
            'doctors'        => $doctors,
            'activePending'  => $activePending
        ];

        return view('apotek/penjualan_langsung', $data);
    }

    /**
     * AJAX: Ambil Detail Rincian Item Penjualan Kasir Apotek
     */
    public function ajaxSaleDetail($saleId)
    {
        $db = \Config\Database::connect('default');
        $sale = $db->table('pharmacy_sales')
                   ->select('pharmacy_sales.*, users.username as cashier_name, doctors.name as doctor_name')
                   ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                   ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                   ->where('pharmacy_sales.id', $saleId)
                   ->get()
                   ->getRow();

        if (!$sale) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data penjualan tidak ditemukan.']);
        }

        $items = $db->table('pharmacy_sale_details')
                    ->select('pharmacy_sale_details.*, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no')
                    ->join('medicines', 'medicines.id = pharmacy_sale_details.medicine_id', 'left')
                    ->join('medicine_batches', 'medicine_batches.id = pharmacy_sale_details.batch_id', 'left')
                    ->where('pharmacy_sale_details.sale_id', $saleId)
                    ->get()
                    ->getResult();

        // Cari jurnal umum terkait
        $journal = $db->table('journal_entries')
                      ->where('reference_id', $saleId)
                      ->like('source_module', 'Apotek')
                      ->get()
                      ->getRow();

        return $this->response->setJSON([
            'status'  => 'success',
            'sale'    => $sale,
            'items'   => $items,
            'journal' => $journal
        ]);
    }

    /**
     * AJAX: Simpan Draf Resep / Hold Cart Sementara (Pending Resep)
     */
    public function ajaxPendingSave()
    {
        $db = \Config\Database::connect('default');
        $customerName   = trim($this->request->getPost('customer_name') ?: 'Pelanggan Umum');
        $customerPhone  = trim($this->request->getPost('customer_phone') ?: '');
        $patientId      = $this->request->getPost('patient_id') ?: null;
        $doctorId       = $this->request->getPost('doctor_id') ?: null;
        $sourceType     = $this->request->getPost('source_type') ?: 'otc';
        $notes          = trim($this->request->getPost('notes') ?: '');
        $tuslaAmount    = floatval($this->request->getPost('tusla_amount') ?: 0);
        $embalaseAmount = floatval($this->request->getPost('embalase_amount') ?: 0);
        $totalAmount    = floatval($this->request->getPost('total_amount') ?: 0);
        $itemsJson      = $this->request->getPost('items_json');

        $pendingNo = 'PND-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $db->table('pharmacy_pending_prescriptions')->insert([
            'pending_no'      => $pendingNo,
            'customer_name'   => $customerName,
            'customer_phone'  => $customerPhone,
            'patient_id'      => $patientId,
            'doctor_id'       => $doctorId,
            'source_type'     => $sourceType,
            'payload_json'    => is_string($itemsJson) ? $itemsJson : json_encode($itemsJson),
            'total_amount'    => $totalAmount,
            'tusla_amount'    => $tuslaAmount,
            'embalase_amount' => $embalaseAmount,
            'status'          => 'pending',
            'cashier_id'      => session('user_id') ?: 1,
            'notes'           => $notes,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'pending_id' => $db->insertID(),
            'pending_no' => $pendingNo,
            'message'    => "Resep/transaksi berhasil ditahan sementara (No. Pending: {$pendingNo}). Antrean kasir dapat dilanjutkan."
        ]);
    }

    /**
     * AJAX: Ambil Daftar Resep Tertunda (Pending)
     */
    public function ajaxPendingList()
    {
        $db = \Config\Database::connect('default');
        $rows = $db->table('pharmacy_pending_prescriptions')
                   ->where('status', 'pending')
                   ->orderBy('id', 'DESC')
                   ->get()
                   ->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $rows
        ]);
    }

    /**
     * AJAX: Buka Kembali Resep Tertunda (Resume Pending)
     */
    public function ajaxPendingResume($id)
    {
        $db = \Config\Database::connect('default');
        $row = $db->table('pharmacy_pending_prescriptions')->where('id', $id)->get()->getRow();
        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Draf resep pending tidak ditemukan.']);
        }

        $items = json_decode($row->payload_json, true) ?: [];

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $row,
            'items'  => $items
        ]);
    }

    /**
     * AJAX: Batalkan / Hapus Resep Tertunda
     */
    public function ajaxPendingDelete($id)
    {
        $db = \Config\Database::connect('default');
        $db->table('pharmacy_pending_prescriptions')->where('id', $id)->update([
            'status'     => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Resep pending berhasil dibatalkan.'
        ]);
    }

    /**
     * AJAX: Ambil Resep Sebelumnya (Repeat Prescription / History Copy)
     */
    public function ajaxPastPrescriptions()
    {
        $db = \Config\Database::connect('default');
        $query = trim($this->request->getGet('q') ?? '');
        $patientId = intval($this->request->getGet('patient_id') ?? 0);

        $builder = $db->table('prescriptions p')
                      ->select('p.id as presc_id, p.created_at, p.tusla_amount, p.embalase_amount,
                                pv.no_visit, pt.id as patient_id, pt.name as patient_name, pt.no_rm,
                                d.id as doctor_id, COALESCE(d.name, "Dokter Pemeriksa") as doctor_name')
                      ->join('patient_visits pv', 'pv.id = p.visit_id', 'left')
                      ->join('patients pt', 'pt.id = pv.patient_id', 'left')
                      ->join('doctors d', 'd.id = p.doctor_id', 'left');

        if ($patientId > 0) {
            $builder->where('pt.id', $patientId);
        } elseif (!empty($query)) {
            $builder->groupStart()
                    ->like('pt.name', $query)
                    ->orLike('pt.no_rm', $query)
                    ->orLike('pv.no_visit', $query)
                    ->groupEnd();
        }
        $prescriptions = $builder->orderBy('p.id', 'DESC')->limit(25)->get()->getResult();

        foreach ($prescriptions as &$p) {
            $items = $db->table('prescription_details pd')
                        ->select('pd.*, m.name as medicine_name, m.code as medicine_code, m.price, m.unit')
                        ->join('medicines m', 'm.id = pd.medicine_id', 'left')
                        ->where('pd.prescription_id', $p->presc_id)
                        ->get()
                        ->getResult();
            $p->items = $items;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $prescriptions
        ]);
    }

    public function cetakNota($saleId)
    {
        $db = \Config\Database::connect('default');

        $sale = $db->table('pharmacy_sales')
                   ->select('pharmacy_sales.*, users.username as cashier_name, doctors.name as doctor_name')
                   ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                   ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                   ->where('pharmacy_sales.id', $saleId)
                   ->get()
                   ->getRow();

        if (!$sale) {
            return redirect()->to(base_url('apotek/penjualan'))->with('error', 'Nota penjualan obat tidak ditemukan.');
        }

        $items = $db->table('pharmacy_sale_details')
                    ->select('pharmacy_sale_details.*, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no')
                    ->join('medicines', 'medicines.id = pharmacy_sale_details.medicine_id', 'left')
                    ->join('medicine_batches', 'medicine_batches.id = pharmacy_sale_details.batch_id', 'left')
                    ->where('pharmacy_sale_details.sale_id', $saleId)
                    ->get()
                    ->getResult();

        $data = [
            'title' => 'Nota Penjualan Obat - ' . $sale->sale_no,
            'sale'  => $sale,
            'items' => $items
        ];

        return view('apotek/cetak_nota', $data);
    }

    // =========================================================================
    // MODUL BUKU KARTU STOK OBAT & ALKES DIGITAL (STOCK CARD LEDGER)
    // =========================================================================
    public function kartuStok()
    {
        $db = \Config\Database::connect('default');
        
        $medicines = $db->table('medicines')
                        ->select('medicines.*, COALESCE(SUM(medicine_batches.stock), 0) as stock')
                        ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                        ->where('medicines.status', 'active')
                        ->groupBy('medicines.id')
                        ->orderBy('medicines.name', 'ASC')
                        ->get()->getResult();
        $selectedMedId = $this->request->getGet('medicine_id');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $selectedMedicine = null;
        $ledger = [];
        $totalIn = 0;
        $totalOut = 0;

        if ($selectedMedId) {
            $selectedMedicine = $db->table('medicines')
                                   ->select('medicines.*, COALESCE(SUM(medicine_batches.stock), 0) as stock')
                                   ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                                   ->where('medicines.id', $selectedMedId)
                                   ->groupBy('medicines.id')
                                   ->get()->getRow();

            // 1. Goods Receipts (Masuk dari Supplier PO)
            $grItems = $db->table('goods_receipt_items')
                          ->select('goods_receipt_items.qty_received as qty, 
                                    goods_receipts.receipt_no as ref_no, 
                                    goods_receipts.received_date as trans_date, 
                                    goods_receipt_items.batch_no,
                                    CONCAT("Penerimaan PO: ", COALESCE(suppliers.name, "Supplier")) as notes')
                          ->join('goods_receipts', 'goods_receipts.id = goods_receipt_items.goods_receipt_id', 'left')
                          ->join('purchase_orders', 'purchase_orders.id = goods_receipts.purchase_order_id', 'left')
                          ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                          ->where('goods_receipt_items.medicine_id', $selectedMedId)
                          ->get()->getResultArray();

            // 2. Prescriptions (Keluar melalui e-Resep Pasien)
            $rxItems = $db->table('prescription_details')
                          ->select('prescription_details.qty, 
                                    CONCAT("RX-", LPAD(prescriptions.id, 5, "0")) as ref_no, 
                                    prescriptions.created_at as trans_date, 
                                    COALESCE(medicine_batches.batch_no, "-") as batch_no,
                                    CONCAT("e-Resep Pasien: ", COALESCE(patients.name, "-"), " (No RM: ", COALESCE(patients.no_rm, "-"), ")") as notes')
                          ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id', 'left')
                          ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                          ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                          ->join('medicine_batches', 'medicine_batches.medicine_id = prescription_details.medicine_id', 'left')
                          ->where('prescription_details.medicine_id', $selectedMedId)
                          ->get()->getResultArray();

            // 3. Direct Sales (Keluar melalui Kasir Penjualan Obat Bebas)
            $posItems = $db->table('pharmacy_sale_details')
                           ->select('pharmacy_sale_details.qty, 
                                     pharmacy_sales.sale_no as ref_no, 
                                     pharmacy_sales.sale_date as trans_date, 
                                     medicine_batches.batch_no,
                                     CONCAT("Penjualan Obat Bebas: ", COALESCE(pharmacy_sales.customer_name, "Umum")) as notes')
                           ->join('pharmacy_sales', 'pharmacy_sales.id = pharmacy_sale_details.sale_id', 'left')
                           ->join('medicine_batches', 'medicine_batches.id = pharmacy_sale_details.batch_id', 'left')
                           ->where('pharmacy_sale_details.medicine_id', $selectedMedId)
                           ->get()->getResultArray();

            // 4. Stock Opname Adjustments
            $opnameItems = $db->table('stock_opname_details')
                              ->select('stock_opname_details.physical_stock, stock_opname_details.system_stock, stock_opname_details.difference,
                                        stock_opnames.opname_no as ref_no,
                                        stock_opnames.opname_date as trans_date,
                                        medicine_batches.batch_no,
                                        CONCAT("Penyesuaian Opname (Fisik: ", stock_opname_details.physical_stock, ") - ", COALESCE(stock_opname_details.reason, "-")) as notes')
                              ->join('stock_opnames', 'stock_opnames.id = stock_opname_details.opname_id', 'left')
                              ->join('medicine_batches', 'medicine_batches.id = stock_opname_details.batch_id', 'left')
                              ->where('stock_opname_details.medicine_id', $selectedMedId)
                              ->get()->getResultArray();

            $allMovements = [];
            foreach ($grItems as $g) {
                $allMovements[] = [
                    'created_at' => $g['trans_date'] ?: date('Y-m-d H:i:s'),
                    'ref_no'     => $g['ref_no'] ?: 'GRN-AUTO',
                    'type_label' => 'Penerimaan PO',
                    'type_badge' => 'success',
                    'batch_no'   => $g['batch_no'],
                    'qty_in'     => (int)$g['qty'],
                    'qty_out'    => 0,
                    'notes'      => $g['notes']
                ];
            }
            foreach ($rxItems as $r) {
                $allMovements[] = [
                    'created_at' => $r['trans_date'] ?: date('Y-m-d H:i:s'),
                    'ref_no'     => $r['ref_no'] ?: 'RX-AUTO',
                    'type_label' => 'e-Resep Medis',
                    'type_badge' => 'info',
                    'batch_no'   => $r['batch_no'],
                    'qty_in'     => 0,
                    'qty_out'    => (int)$r['qty'],
                    'notes'      => $r['notes']
                ];
            }
            foreach ($posItems as $p) {
                $allMovements[] = [
                    'created_at' => $p['trans_date'] ?: date('Y-m-d H:i:s'),
                    'ref_no'     => $p['ref_no'] ?: 'SLS-AUTO',
                    'type_label' => 'Penjualan Bebas',
                    'type_badge' => 'warning',
                    'batch_no'   => $p['batch_no'],
                    'qty_in'     => 0,
                    'qty_out'    => (int)$p['qty'],
                    'notes'      => $p['notes']
                ];
            }
            foreach ($opnameItems as $o) {
                $diff = (int)$o['difference'];
                $allMovements[] = [
                    'created_at' => $o['trans_date'] ?: date('Y-m-d H:i:s'),
                    'ref_no'     => $o['ref_no'] ?: 'OPN-AUTO',
                    'type_label' => 'Stok Opname',
                    'type_badge' => 'secondary',
                    'batch_no'   => $o['batch_no'],
                    'qty_in'     => $diff > 0 ? $diff : 0,
                    'qty_out'    => $diff < 0 ? abs($diff) : 0,
                    'notes'      => $o['notes']
                ];
            }

            // Sort chronologically
            usort($allMovements, function($a, $b) {
                return strtotime($a['created_at']) <=> strtotime($b['created_at']);
            });

            // Calculate running balance
            $currentRunningBal = 0;
            foreach ($allMovements as &$m) {
                $currentRunningBal += ($m['qty_in'] - $m['qty_out']);
                $m['balance'] = $currentRunningBal;
                $totalIn += $m['qty_in'];
                $totalOut += $m['qty_out'];
            }

            $ledger = $allMovements;
        }

        $data = [
            'title'            => 'Buku Kartu Stok Obat Digital (Stock Card Ledger)',
            'active_menu'      => 'apotek-kartu-stok',
            'medicines'        => $medicines,
            'selectedMedicine' => $selectedMedicine,
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'ledger'           => $ledger,
            'totalIn'          => $totalIn,
            'totalOut'         => $totalOut
        ];

        return view('apotek/kartu_stok', $data);
    }

    /**
     * Dashboard Gudang Farmasi & Multi-Depo (/apotek/gudang)
     */
    public function gudang()
    {
        $db = \Config\Database::connect('default');

        // 1. List all warehouses
        $warehouses = $db->table('pharmacy_warehouses')->orderBy('id', 'ASC')->get()->getResult();

        // 2. Selected warehouse filter (default: all or Gudang Induk)
        $selectedWhId = intval($this->request->getGet('warehouse_id'));

        // 3. Stock per warehouse query
        $stockBuilder = $db->table('warehouse_stock')
                           ->select('warehouse_stock.*, 
                                     pharmacy_warehouses.name as warehouse_name,
                                     pharmacy_warehouses.code as warehouse_code,
                                     pharmacy_warehouses.type as warehouse_type,
                                     medicines.code as medicine_code,
                                     medicines.name as medicine_name,
                                     medicines.unit,
                                     medicines.type as medicine_type,
                                     medicines.price,
                                     medicine_batches.batch_no,
                                     medicine_batches.expired_date')
                           ->join('pharmacy_warehouses', 'pharmacy_warehouses.id = warehouse_stock.warehouse_id')
                           ->join('medicines', 'medicines.id = warehouse_stock.medicine_id')
                           ->join('medicine_batches', 'medicine_batches.id = warehouse_stock.batch_id')
                           ->where('warehouse_stock.stock >', 0);

        if ($selectedWhId > 0) {
            $stockBuilder->where('warehouse_stock.warehouse_id', $selectedWhId);
        }

        $warehouseStocks = $stockBuilder->orderBy('medicines.name', 'ASC')->get()->getResult();

        // 4. Stock Transfer History
        $transfers = $db->table('stock_transfers')
                        ->select('stock_transfers.*, 
                                  src.name as source_name, src.code as source_code,
                                  tgt.name as target_name, tgt.code as target_code,
                                  users.username as requester_name,
                                  (SELECT COUNT(*) FROM stock_transfer_items WHERE stock_transfer_items.stock_transfer_id = stock_transfers.id) as item_count,
                                  (SELECT SUM(qty) FROM stock_transfer_items WHERE stock_transfer_items.stock_transfer_id = stock_transfers.id) as total_qty')
                        ->join('pharmacy_warehouses src', 'src.id = stock_transfers.source_warehouse_id')
                        ->join('pharmacy_warehouses tgt', 'tgt.id = stock_transfers.target_warehouse_id')
                        ->join('users', 'users.id = stock_transfers.requested_by', 'left')
                        ->orderBy('stock_transfers.id', 'DESC')
                        ->get()
                        ->getResult();

        // 5. Active medicines with available stock for transfer modal
        $medicines = $db->table('medicines')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $batches = $db->table('medicine_batches')->where('stock >', 0)->orderBy('expired_date', 'ASC')->get()->getResult();

        // 6. Summary metrics
        $totalWarehouses = count($warehouses);
        $totalIndukStock = 0;
        $totalDepoStock  = 0;
        $totalTransferCount = count($transfers);

        $allWs = $db->table('warehouse_stock')
                    ->select('warehouse_stock.stock, pharmacy_warehouses.type')
                    ->join('pharmacy_warehouses', 'pharmacy_warehouses.id = warehouse_stock.warehouse_id')
                    ->get()->getResult();
        foreach ($allWs as $w) {
            if ($w->type === 'main') {
                $totalIndukStock += (int)$w->stock;
            } else {
                $totalDepoStock += (int)$w->stock;
            }
        }

        $data = [
            'title'              => 'Gudang Farmasi & Multi-Depo Pelayanan',
            'active_menu'        => 'apotek-gudang',
            'warehouses'         => $warehouses,
            'selectedWhId'       => $selectedWhId,
            'warehouseStocks'    => $warehouseStocks,
            'transfers'          => $transfers,
            'medicines'          => $medicines,
            'batches'            => $batches,
            'totalWarehouses'    => $totalWarehouses,
            'totalIndukStock'    => $totalIndukStock,
            'totalDepoStock'     => $totalDepoStock,
            'totalTransferCount' => $totalTransferCount
        ];

        return view('apotek/gudang', $data);
    }

    /**
     * Proses Mutasi Transfer Stok Antar Gudang / Depo
     */
    public function transferStok()
    {
        $db = \Config\Database::connect('default');

        $sourceWhId = intval($this->request->getPost('source_warehouse_id'));
        $targetWhId = intval($this->request->getPost('target_warehouse_id'));
        $transferDate = $this->request->getPost('transfer_date') ?: date('Y-m-d');
        $notes = trim($this->request->getPost('notes') ?: '');

        $medIds   = $this->request->getPost('medicine_id') ?: [];
        $batchIds = $this->request->getPost('batch_id') ?: [];
        $qtys     = $this->request->getPost('qty') ?: [];
        $units    = $this->request->getPost('unit') ?: [];
        $itemNotes= $this->request->getPost('item_notes') ?: [];

        if ($sourceWhId === $targetWhId) {
            session()->setFlashdata('error', 'Gudang asal dan gudang tujuan transfer tidak boleh sama.');
            return redirect()->to(base_url('apotek/gudang'));
        }

        if (empty($medIds) || count($medIds) === 0) {
            session()->setFlashdata('error', 'Pilih minimal satu item obat untuk ditransfer.');
            return redirect()->to(base_url('apotek/gudang'));
        }

        $db->transStart();

        // Generate Transfer No (TRF-YYYYMMDD-XXXX)
        $today = date('Ymd');
        $lastTrf = $db->table('stock_transfers')
                      ->where('DATE(created_at)', date('Y-m-d'))
                      ->orderBy('id', 'DESC')
                      ->limit(1)
                      ->get()
                      ->getRow();
        $nextNum = 1;
        if ($lastTrf && preg_match('/TRF-\d+-(\d+)/', $lastTrf->transfer_no, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $transferNo = 'TRF-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $userId = session('user_id') ?: 1;

        // Insert Header
        $db->table('stock_transfers')->insert([
            'transfer_no'         => $transferNo,
            'source_warehouse_id' => $sourceWhId,
            'target_warehouse_id' => $targetWhId,
            'transfer_date'       => $transferDate,
            'requested_by'        => $userId,
            'approved_by'         => $userId,
            'status'              => 'completed',
            'notes'               => $notes,
            'created_at'          => date('Y-m-d H:i:s')
        ]);
        $trfId = $db->insertID();

        // Process Items
        for ($i = 0; $i < count($medIds); $i++) {
            $medId   = intval($medIds[$i]);
            $batchId = intval($batchIds[$i] ?? 0);
            $qty     = max(1, intval($qtys[$i] ?? 1));
            $unit    = $units[$i] ?? 'Pcs';
            $iNote   = $itemNotes[$i] ?? null;

            if ($medId <= 0) continue;

            // If batchId is not provided, pick first available batch
            if ($batchId <= 0) {
                $firstB = $db->table('medicine_batches')->where('medicine_id', $medId)->where('stock >', 0)->orderBy('expired_date', 'ASC')->limit(1)->get()->getRow();
                $batchId = $firstB ? $firstB->id : 1;
            }

            // Insert item record
            $db->table('stock_transfer_items')->insert([
                'stock_transfer_id' => $trfId,
                'medicine_id'       => $medId,
                'batch_id'          => $batchId,
                'qty'               => $qty,
                'unit'              => $unit,
                'notes'             => $iNote,
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            // Deduct stock from Source Warehouse
            $srcRow = $db->table('warehouse_stock')
                         ->where('warehouse_id', $sourceWhId)
                         ->where('medicine_id', $medId)
                         ->where('batch_id', $batchId)
                         ->get()->getRow();
            if ($srcRow) {
                $newSrcStock = max(0, $srcRow->stock - $qty);
                $db->table('warehouse_stock')
                   ->where('id', $srcRow->id)
                   ->update(['stock' => $newSrcStock]);
            } else {
                $db->table('warehouse_stock')->insert([
                    'warehouse_id' => $sourceWhId,
                    'medicine_id'  => $medId,
                    'batch_id'     => $batchId,
                    'stock'        => 0
                ]);
            }

            // Increase stock in Target Warehouse
            $tgtRow = $db->table('warehouse_stock')
                         ->where('warehouse_id', $targetWhId)
                         ->where('medicine_id', $medId)
                         ->where('batch_id', $batchId)
                         ->get()->getRow();
            if ($tgtRow) {
                $db->table('warehouse_stock')
                   ->where('id', $tgtRow->id)
                   ->update(['stock' => $tgtRow->stock + $qty]);
            } else {
                $db->table('warehouse_stock')->insert([
                    'warehouse_id' => $targetWhId,
                    'medicine_id'  => $medId,
                    'batch_id'     => $batchId,
                    'stock'        => $qty
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses mutasi transfer stok antar gudang.');
        } else {
            session()->setFlashdata('success', "Mutasi transfer stok berhasil dibukukan! No Surat Jalan Mutasi: {$transferNo}");
        }

        return redirect()->to(base_url('apotek/gudang'));
    }

    /**
     * Cetak Surat Jalan Bukti Mutasi Transfer Antar Gudang / Depo
     */
    public function cetakSuratMutasi($id)
    {
        $db = \Config\Database::connect('default');

        $transfer = $db->table('stock_transfers')
                       ->select('stock_transfers.*, 
                                 src.name as source_name, src.code as source_code, src.location as source_location, src.pic_name as source_pic,
                                 tgt.name as target_name, tgt.code as target_code, tgt.location as target_location, tgt.pic_name as target_pic,
                                 users.username as requester_name')
                       ->join('pharmacy_warehouses src', 'src.id = stock_transfers.source_warehouse_id')
                       ->join('pharmacy_warehouses tgt', 'tgt.id = stock_transfers.target_warehouse_id')
                       ->join('users', 'users.id = stock_transfers.requested_by', 'left')
                       ->where('stock_transfers.id', $id)
                       ->get()
                       ->getRow();

        if (!$transfer) {
            session()->setFlashdata('error', 'Surat mutasi tidak ditemukan.');
            return redirect()->to(base_url('apotek/gudang'));
        }

        $items = $db->table('stock_transfer_items')
                    ->select('stock_transfer_items.*, 
                              medicines.code as medicine_code, 
                              medicines.name as medicine_name,
                              medicine_batches.batch_no,
                              medicine_batches.expired_date')
                    ->join('medicines', 'medicines.id = stock_transfer_items.medicine_id')
                    ->join('medicine_batches', 'medicine_batches.id = stock_transfer_items.batch_id', 'left')
                    ->where('stock_transfer_items.stock_transfer_id', $id)
                    ->get()
                    ->getResult();

        $data = [
            'title'    => 'Surat Mutasi Transfer ' . $transfer->transfer_no,
            'transfer' => $transfer,
            'items'    => $items
        ];

        return view('apotek/cetak_surat_mutasi', $data);
    }

    /**
     * AJAX endpoint to fetch stock of a specific warehouse
     */
    public function getWarehouseStockJson($warehouseId)
    {
        $db = \Config\Database::connect('default');
        $stocks = $db->table('warehouse_stock')
                     ->select('warehouse_stock.*, 
                               medicines.name as medicine_name, 
                               medicines.code as medicine_code,
                               medicines.unit,
                               medicine_batches.batch_no,
                               medicine_batches.expired_date')
                     ->join('medicines', 'medicines.id = warehouse_stock.medicine_id')
                     ->join('medicine_batches', 'medicine_batches.id = warehouse_stock.batch_id')
                     ->where('warehouse_stock.warehouse_id', $warehouseId)
                     ->where('warehouse_stock.stock >', 0)
                     ->orderBy('medicines.name', 'ASC')
                     ->get()
                     ->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $stocks
        ]);
    }
}

