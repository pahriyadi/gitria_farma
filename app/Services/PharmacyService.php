<?php

namespace App\Services;

use App\Services\JournalEngine;

class PharmacyService
{
    protected $db;
    protected $journalEngine;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');
        $this->journalEngine = new JournalEngine();
    }

    public function processPrescription($prescriptionId, $dispenseDetails)
    {
        // $dispenseDetails is an array: [medicine_id => ['action' => 'internal'|'beli_luar'|'cancel', 'batch_id' => X, 'qty' => Y, 'discount' => Z]]
        if (empty($dispenseDetails) || !is_array($dispenseDetails)) {
            return [
                'status'  => 'error',
                'message' => 'Rincian obat yang akan diproses tidak ditemukan.'
            ];
        }

        try {
            $this->db->transStart();

            $prescription = $this->db->table('prescriptions')->where('id', $prescriptionId)->get()->getRow();
            if (!$prescription) {
                $this->db->transRollback();
                return [
                    'status'  => 'error',
                    'message' => 'Data resep tidak ditemukan di sistem.'
                ];
            }

            $bill = $this->db->table('billing_transactions')->where('visit_id', $prescription->visit_id)->get()->getRow();
            if ($bill && $bill->status !== 'paid') {
                $this->db->transRollback();
                return [
                    'status'  => 'error',
                    'message' => 'Obat belum dapat diserahkan/diselesaikan karena pasien belum melakukan pelunasan tagihan di Kasir Utama.'
                ];
            }

            // Find a valid fallback user_id for stock movement logging
            $movementUserId = (int)(session('user_id') ?: 0);
            if ($movementUserId <= 0) {
                $firstUser = $this->db->table('users')->select('id')->orderBy('id', 'ASC')->limit(1)->get()->getRow();
                $movementUserId = $firstUser ? (int)$firstUser->id : 1;
            }

            $hasWarehouseStock = $this->db->tableExists('warehouse_stock');
            $hasWarehouses = $this->db->tableExists('pharmacy_warehouses');

            foreach ($dispenseDetails as $medId => $details) {
                $med = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
                $medName = $med ? $med->name : "ID {$medId}";
                $action = $details['action'] ?? 'internal';
                $qty = intval($details['qty'] ?? 1);
                $itemDiscount = floatval($details['discount'] ?? 0);
                $itemTusla = floatval($details['tusla'] ?? 0);
                $itemEmbalase = floatval($details['embalase'] ?? 0);

                if ($action === 'internal') {
                    // Dispensed from internal pharmacy stock
                    if (!isset($details['batch_id']) || empty($details['batch_id'])) {
                        $this->db->transRollback();
                        return [
                            'status'  => 'error',
                            'message' => "Silakan pilih Batch Obat untuk '{$medName}' terlebih dahulu, atau pilih opsi 'Beli di Luar' jika stok habis."
                        ];
                    }

                    $batchId = $details['batch_id'];
                    $batch = $this->db->table('medicine_batches')->where('id', $batchId)->get()->getRow();
                    if (!$batch || $batch->stock < $qty) {
                        $this->db->transRollback();
                        $avail = $batch ? $batch->stock : 0;
                        return [
                            'status'  => 'error',
                            'message' => "Stok Batch {$batchId} untuk obat '{$medName}' tidak mencukupi (Tersedia: {$avail}, Diminta: {$qty}). Silakan pilih batch lain atau alihkan ke opsi 'Beli di Luar'."
                        ];
                    }

                    // Deduct stock in global batch
                    $newStock = max(0, $batch->stock - $qty);
                    $this->db->table('medicine_batches')->where('id', $batchId)->update(['stock' => $newStock]);

                    // Deduct stock in Depo Apotek (DEPO-RJ) if warehouse_stock table exists
                    if ($hasWarehouseStock && $hasWarehouses) {
                        try {
                            $depoWh = $this->db->table('pharmacy_warehouses')->where('code', 'DEPO-RJ')->orWhere('type', 'depo')->get()->getRow();
                            $depoWhId = $depoWh ? $depoWh->id : 2;
                            $wsRow = $this->db->table('warehouse_stock')
                                              ->where('warehouse_id', $depoWhId)
                                              ->where('medicine_id', $medId)
                                              ->where('batch_id', $batchId)
                                              ->get()->getRow();
                            if ($wsRow) {
                                $this->db->table('warehouse_stock')->where('id', $wsRow->id)->update(['stock' => max(0, $wsRow->stock - $qty)]);
                            }
                        } catch (\Throwable $e) {
                            // Non-blocking for warehouse stock
                        }
                    }

                    // Add Stock Movement log
                    try {
                        $this->db->table('stock_movements')->insert([
                            'medicine_id'      => $medId,
                            'batch_id'         => $batchId,
                            'transaction_type' => 'resep',
                            'reference_id'     => $prescriptionId,
                            'qty_in'           => 0,
                            'qty_out'          => $qty,
                            'balance'          => $newStock,
                            'user_id'          => $movementUserId
                        ]);
                    } catch (\Throwable $e) {
                        // Non-blocking movement log
                    }

                    // Update prescription details status and discount
                    $pDetailUpdate = [];
                    if ($this->db->fieldExists('status', 'prescription_details')) $pDetailUpdate['status'] = 'served';
                    if ($this->db->fieldExists('discount', 'prescription_details')) $pDetailUpdate['discount'] = $itemDiscount;
                    if ($this->db->fieldExists('tusla', 'prescription_details')) $pDetailUpdate['tusla'] = $itemTusla;
                    if ($this->db->fieldExists('embalase', 'prescription_details')) $pDetailUpdate['embalase'] = $itemEmbalase;

                    if (!empty($pDetailUpdate)) {
                        $this->db->table('prescription_details')
                                 ->where('prescription_id', $prescriptionId)
                                 ->where('medicine_id', $medId)
                                 ->update($pDetailUpdate);
                    }

                    // Update or Insert in billing_details with per-item discount
                    if ($bill && $med) {
                        $price = $med->price;
                        $subtotal = max(0, ($qty * $price) + $itemTusla + $itemEmbalase - $itemDiscount);

                        $existingBillItem = $this->db->table('billing_details')
                                                     ->where('billing_id', $bill->id)
                                                     ->where('item_type', 'obat')
                                                     ->where('item_name', $med->name)
                                                     ->get()
                                                     ->getRow();

                        $bData = [
                            'qty'      => $qty,
                            'price'    => $price,
                            'subtotal' => $subtotal
                        ];
                        if ($this->db->fieldExists('discount', 'billing_details')) $bData['discount'] = $itemDiscount;
                        if ($this->db->fieldExists('tusla', 'billing_details')) $bData['tusla'] = $itemTusla;
                        if ($this->db->fieldExists('embalase', 'billing_details')) $bData['embalase'] = $itemEmbalase;

                        if ($existingBillItem) {
                            $this->db->table('billing_details')
                                     ->where('id', $existingBillItem->id)
                                     ->update($bData);
                        } else {
                            $bData['billing_id'] = $bill->id;
                            $bData['item_type']  = 'obat';
                            $bData['item_name']  = $med->name;
                            $this->db->table('billing_details')->insert($bData);
                        }
                    }
                } else {
                    // Action is 'beli_luar' (resep luar) OR 'cancel' (dibatalkan)
                    $statusVal = $action === 'beli_luar' ? 'bought_outside' : 'cancelled';

                    $pCancelUpdate = [];
                    if ($this->db->fieldExists('status', 'prescription_details')) $pCancelUpdate['status'] = $statusVal;
                    if ($this->db->fieldExists('discount', 'prescription_details')) $pCancelUpdate['discount'] = 0.00;
                    if ($this->db->fieldExists('tusla', 'prescription_details')) $pCancelUpdate['tusla'] = 0.00;
                    if ($this->db->fieldExists('embalase', 'prescription_details')) $pCancelUpdate['embalase'] = 0.00;

                    if (!empty($pCancelUpdate)) {
                        $this->db->table('prescription_details')
                                 ->where('prescription_id', $prescriptionId)
                                 ->where('medicine_id', $medId)
                                 ->update($pCancelUpdate);
                    }

                    // Delete from billing_details
                    if ($bill && $med) {
                        $this->db->table('billing_details')
                                 ->where('billing_id', $bill->id)
                                 ->where('item_type', 'obat')
                                 ->where('item_name', $med->name)
                                 ->delete();
                    }
                }
            }

            // 2. Recalculate Billing totals accurately with per-item discounts
            if ($bill) {
                $allDetails = $this->db->table('billing_details')
                                       ->where('billing_id', $bill->id)
                                       ->get()
                                       ->getResult();

                $totalServices = 0;
                $totalMedicines = 0;
                $totalRestaurant = 0;
                $totalDiscounts = 0;

                foreach ($allDetails as $d) {
                    if ($d->item_type === 'medis') {
                        $totalServices += (float)($d->subtotal ?? 0);
                    } elseif ($d->item_type === 'obat') {
                        $tuslaVal = isset($d->tusla) ? (float)$d->tusla : 0;
                        $embalaseVal = isset($d->embalase) ? (float)$d->embalase : 0;
                        $totalMedicines += (((float)$d->qty * (float)$d->price) + $tuslaVal + $embalaseVal);
                        $totalDiscounts += (float)($d->discount ?? 0);
                    } elseif ($d->item_type === 'resto') {
                        $totalRestaurant += (float)($d->subtotal ?? 0);
                    }
                }

                $grandTotal = max(0, ($totalServices + $totalMedicines + $totalRestaurant) - $totalDiscounts);

                $targetBillingStatus = ($bill->status === 'paid') ? 'paid' : 'open';
                $this->db->table('billing_transactions')
                         ->where('id', $bill->id)
                         ->update([
                             'total_services'   => $totalServices,
                             'total_medicines'  => $totalMedicines,
                             'total_restaurant' => $totalRestaurant,
                             'discount'         => $totalDiscounts,
                             'grand_total'      => $grandTotal,
                             'status'           => $targetBillingStatus,
                             'updated_at'       => date('Y-m-d H:i:s')
                         ]);

                // Update in-memory $bill object properties
                $bill->total_medicines = $totalMedicines;
                $bill->grand_total = $grandTotal;
                $bill->total_services = $totalServices;
            }

            // 3. Selesaikan Status e-Resep & Catat Timestamp Penyerahan Obat (SLA)
            $prescFinalUpdate = ['status' => 'completed'];
            if ($this->db->fieldExists('dispensed_at', 'prescriptions')) {
                $prescFinalUpdate['dispensed_at'] = date('Y-m-d H:i:s');
            }
            $this->db->table('prescriptions')->where('id', $prescriptionId)->update($prescFinalUpdate);

            // 4. Update Status Kunjungan Pasien (Lunas + Obat Diserahkan = Selesai Penuh)
            $visitStatus = ($bill && $bill->status === 'paid') ? 'completed' : 'cashier';
            $this->db->table('patient_visits')
                     ->where('id', $prescription->visit_id)
                     ->update([
                         'status'     => $visitStatus,
                         'updated_at' => date('Y-m-d H:i:s')
                     ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $dbErr = $this->db->error();
                $errDetail = !empty($dbErr['message']) ? (': ' . $dbErr['message']) : '';
                return [
                    'status'  => 'error',
                    'message' => 'Gagal memproses penyiapan resep obat' . $errDetail
                ];
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in processPrescription: ' . $e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'Gagal memproses penyiapan resep obat: ' . $e->getMessage()
            ];
        }

        // 5. Penjurnalan Otomatis Selesai Ambil Obat di Farmasi (Template PENJUALAN_OBAT_RESEP)
        if ($bill && (float)$bill->total_medicines > 0) {
            try {
                $visit = $this->db->table('patient_visits')->where('id', $prescription->visit_id)->get()->getRow();
                $patient = ($visit && $visit->patient_id) ? $this->db->table('patients')->where('id', $visit->patient_id)->get()->getRow() : null;
                $queue = $this->db->table('queue_numbers')->where('visit_id', $prescription->visit_id)->get()->getRow();

                $pName = $patient ? $patient->name : 'Pasien Umum';
                $qCode = $queue ? ($queue->queue_no ?: ($visit ? $visit->no_visit : '')) : ($visit ? $visit->no_visit : '');
                $patientTag = $pName . ($qCode ? ' / ' . $qCode : '');

                $allBillDetails = $this->db->table('billing_details')
                                           ->where('billing_id', $bill->id)
                                           ->where('item_type', 'obat')
                                           ->get()->getResult();

                $obatList = [];
                $totalTusla = 0;
                $totalEmbalase = 0;
                $calcMedTotal = 0;
                foreach ($allBillDetails as $bd) {
                    $itemLabel = (!empty($bd->is_racikan) && !empty($bd->racikan_name)) ? ('[Racikan] ' . $bd->racikan_name) : $bd->item_name;
                    $tuslaVal = isset($bd->tusla) ? (float)$bd->tusla : 0;
                    $embalaseVal = isset($bd->embalase) ? (float)$bd->embalase : 0;
                    $totalTusla += $tuslaVal;
                    $totalEmbalase += $embalaseVal;
                    $calcMedTotal += (float)$bd->subtotal;
                    
                    $priceStr = number_format($bd->price * $bd->qty, 0, ',', '.');
                    $tuslaStr = number_format($tuslaVal, 0, ',', '.');
                    $embalaseStr = number_format($embalaseVal, 0, ',', '.');
                    $obatList[] = $itemLabel . ' (' . (int)$bd->qty . 'x) (Rp ' . $priceStr . ') | tusla (Rp ' . $tuslaStr . ') | embarse (Rp ' . $embalaseStr . ')';
                }

                $pharmAmount = $calcMedTotal > 0 ? $calcMedTotal : (float)$bill->total_medicines;

                $obatSummary = !empty($obatList) ? "\n" . implode(", \n", array_unique($obatList)) : 'e-Resep Obat';
                if (!empty($obatList)) {
                    $obatSummary .= ",\ntotal tusla : Rp " . number_format($totalTusla, 0, ',', '.') . "\ntotal embarse : Rp " . number_format($totalEmbalase, 0, ',', '.');
                }
                $descObat = "Pendapatan Farmasi & Resep Obat - {$bill->billing_no} ({$patientTag}): {$obatSummary}";

                $docFeePct = 5.00;
                if ($visit && $visit->doctor_id) {
                    $doctorRow = $this->db->table('doctors')->where('id', $visit->doctor_id)->get()->getRow();
                    if ($doctorRow && isset($doctorRow->prescription_fee_percent)) {
                        $docFeePct = (float)$doctorRow->prescription_fee_percent;
                    }
                }

                $payMethod = !empty($bill->payment_method) ? $bill->payment_method : 'tunai';
                $this->journalEngine->postPharmacySplitJournal('resep', $bill->id, $pharmAmount, $descObat, $payMethod, $docFeePct, 'Apotek (Obat Resep)');
            } catch (\Throwable $e) {
                log_message('error', 'Gagal membukukan jurnal farmasi resep: ' . $e->getMessage());
            }
        }

        // Trigger Event Hook Penyerahan e-Resep
        \CodeIgniter\Events\Events::trigger('pharmacy.dispensed', $prescriptionId, 'Dokter', 'Pasien');

        // Push Notifikasi Operasional Penyerahan Obat Selesai
        try {
            $notif = new \App\Services\NotificationService();
            $notif->send([
                'notification_key' => 'prescription_dispensed_' . $prescriptionId . '_' . time(),
                'category'         => 'farmasi',
                'type'             => 'success',
                'badge'            => 'Obat Diserahkan',
                'icon'             => 'fas fa-pills',
                'title'            => 'Obat Pasien Telah Diserahkan',
                'message'          => "Penyiapan dan penyerahan e-resep obat telah selesai diproses oleh Apoteker.",
                'link'             => base_url('apotek/resep'),
                'target_roles'     => 'apoteker,farmasi,super admin,administrator',
                'sender_name'      => 'Apotek & Farmasi'
            ]);
        } catch (\Throwable $e) {}

        return [
            'status'  => 'success',
            'message' => 'e-Resep berhasil diselesaikan dan obat telah diserahkan kepada pasien.'
        ];
    }

    /**
     * Memproses transaksi Penjualan Obat Bebas Langsung (OTC / Walk-In)
     */
    public function processDirectSale(array $payload)
    {
        $customerName     = trim($payload['customer_name'] ?? 'Pelanggan Umum');
        $customerPhone    = trim($payload['customer_phone'] ?? '');
        $paymentMethod    = $payload['payment_method'] ?? 'tunai';
        $paidAmount       = floatval($payload['paid_amount'] ?? 0);
        $notes            = trim($payload['notes'] ?? '');
        $cashierId        = intval($payload['cashier_id'] ?? (session('user_id') ?: 1));
        $doctorId         = !empty($payload['doctor_id']) ? intval($payload['doctor_id']) : null;
        $doctorFeeNominal = floatval($payload['doctor_fee_nominal'] ?? 0);
        $prescriptionType = $payload['prescription_type'] ?? 'bebas';
        $tuslaAmount      = floatval($payload['tusla_amount'] ?? 0);
        $embalaseAmount   = floatval($payload['embalase_amount'] ?? 0);
        $items            = $payload['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            return [
                'status'  => 'error',
                'message' => 'Pilih minimal satu item obat untuk transaksi penjualan langsung.'
            ];
        }

        try {
            $this->db->transStart();

            // Find valid fallback user_id
            $movementUserId = (int)(session('user_id') ?: $cashierId);
            if ($movementUserId <= 0) {
                $firstUser = $this->db->table('users')->select('id')->orderBy('id', 'ASC')->limit(1)->get()->getRow();
                $movementUserId = $firstUser ? (int)$firstUser->id : 1;
            }

            $today = date('Ymd');
            $prefix = 'SL-' . $today . '-';
            $lastSale = $this->db->table('pharmacy_sales')
                                 ->where("sale_no LIKE '{$prefix}%'")
                                 ->orderBy('id', 'DESC')
                                 ->limit(1)
                                 ->get()
                                 ->getRow();
            $nextSeq = 1;
            if ($lastSale && preg_match('/-(\d+)$/', $lastSale->sale_no, $m)) {
                $nextSeq = intval($m[1]) + 1;
            }
            $saleNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // 1. Calculate items total and validate batches
            $totalGross = 0;
            $totalDiscount = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $medId = intval($item['medicine_id'] ?? 0);
                $batchId = intval($item['batch_id'] ?? 0);
                $qty = intval($item['qty'] ?? 1);
                $discount = floatval($item['discount'] ?? 0);
                $dosage = trim($item['dosage_instruction'] ?? '');
                $isRacikan = !empty($item['is_racikan']) ? 1 : 0;
                $racikanName = trim($item['racikan_name'] ?? '');
                $racikanGroup = trim($item['racikan_group'] ?? '');
                $itemTusla = floatval($item['tusla'] ?? 0);
                $itemEmbalase = floatval($item['embalase'] ?? 0);

                if ($medId <= 0 || $qty <= 0) continue;

                $med = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
                if (!$med) continue;

                $unitPrice = floatval($med->price);
                $itemSubtotal = max(0, ($unitPrice * $qty) - $discount + $itemTusla + $itemEmbalase);

                // Validate Batch Stock
                $batch = null;
                if ($batchId > 0) {
                    $batch = $this->db->table('medicine_batches')->where('id', $batchId)->where('medicine_id', $medId)->get()->getRow();
                } else {
                    // Auto-pick FEFO batch if not explicitly specified
                    $batch = $this->db->table('medicine_batches')
                                      ->where('medicine_id', $medId)
                                      ->where('stock >=', $qty)
                                      ->orderBy('expired_date', 'ASC')
                                      ->get()
                                      ->getRow();
                }

                if (!$batch || $batch->stock < $qty) {
                    $this->db->transRollback();
                    $avail = $batch ? $batch->stock : 0;
                    return [
                        'status'  => 'error',
                        'message' => "Stok obat '{$med->name}' (Batch: " . ($batch ? $batch->batch_no : 'Kosong') . ") tidak mencukupi. Tersedia: {$avail} {$med->unit}, diminta: {$qty} {$med->unit}."
                    ];
                }

                $totalGross += ($unitPrice * $qty);
                $totalDiscount += $discount;

                $processedItems[] = [
                    'medicine_id'        => $medId,
                    'batch_id'           => $batch->id,
                    'batch_no'           => $batch->batch_no,
                    'qty'                => $qty,
                    'price'              => $unitPrice,
                    'discount'           => $discount,
                    'tusla'              => $itemTusla,
                    'embalase'           => $itemEmbalase,
                    'subtotal'           => $itemSubtotal,
                    'dosage_instruction' => $dosage,
                    'medicine_name'      => $med->name,
                    'unit'               => $med->unit,
                    'is_racikan'         => $isRacikan,
                    'racikan_name'       => $racikanName,
                    'racikan_group'      => $racikanGroup
                ];
            }

            if (empty($processedItems)) {
                $this->db->transRollback();
                return [
                    'status'  => 'error',
                    'message' => 'Tidak ada item obat valid yang dapat diproses.'
                ];
            }

            $grandTotal = max(0, ($totalGross - $totalDiscount) + $tuslaAmount + $embalaseAmount);
            $changeAmount = max(0, $paidAmount - $grandTotal);

            // 2. Insert Sale Record dynamically based on available columns
            $saleData = [
                'sale_no'           => $saleNo,
                'customer_name'     => $customerName ?: 'Pelanggan Umum',
                'sale_date'         => date('Y-m-d'),
                'total_amount'      => $totalGross,
                'discount_amount'   => $totalDiscount,
                'grand_total'       => $grandTotal,
                'payment_method'    => $paymentMethod,
                'paid_amount'       => $paidAmount,
                'change_amount'     => $changeAmount,
                'cashier_id'        => $movementUserId,
                'notes'             => $notes,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ];

            if ($this->db->fieldExists('prescription_type', 'pharmacy_sales')) $saleData['prescription_type'] = $prescriptionType;
            if ($this->db->fieldExists('customer_phone', 'pharmacy_sales')) $saleData['customer_phone'] = $customerPhone;
            if ($this->db->fieldExists('tusla_amount', 'pharmacy_sales')) $saleData['tusla_amount'] = $tuslaAmount;
            if ($this->db->fieldExists('embalase_amount', 'pharmacy_sales')) $saleData['embalase_amount'] = $embalaseAmount;
            if ($this->db->fieldExists('doctor_id', 'pharmacy_sales')) $saleData['doctor_id'] = $doctorId;
            if ($this->db->fieldExists('doctor_fee_nominal', 'pharmacy_sales')) $saleData['doctor_fee_nominal'] = $doctorFeeNominal;

            $this->db->table('pharmacy_sales')->insert($saleData);
            $saleId = $this->db->insertID();

            $hasWarehouseStock = $this->db->tableExists('warehouse_stock');
            $hasWarehouses = $this->db->tableExists('pharmacy_warehouses');

            // 3. Insert Details & Deduct Batch Stock
            foreach ($processedItems as $pi) {
                $detailData = [
                    'sale_id'            => $saleId,
                    'medicine_id'        => $pi['medicine_id'],
                    'batch_id'           => $pi['batch_id'],
                    'qty'                => $pi['qty'],
                    'price'              => $pi['price'],
                    'discount'           => $pi['discount'],
                    'subtotal'           => $pi['subtotal'],
                    'created_at'         => date('Y-m-d H:i:s')
                ];

                if ($this->db->fieldExists('is_racikan', 'pharmacy_sale_details')) $detailData['is_racikan'] = $pi['is_racikan'];
                if ($this->db->fieldExists('racikan_name', 'pharmacy_sale_details')) $detailData['racikan_name'] = $pi['racikan_name'];
                if ($this->db->fieldExists('racikan_group', 'pharmacy_sale_details')) $detailData['racikan_group'] = $pi['racikan_group'];
                if ($this->db->fieldExists('tusla', 'pharmacy_sale_details')) $detailData['tusla'] = $pi['tusla'];
                if ($this->db->fieldExists('embalase', 'pharmacy_sale_details')) $detailData['embalase'] = $pi['embalase'];
                if ($this->db->fieldExists('dosage_instruction', 'pharmacy_sale_details')) $detailData['dosage_instruction'] = $pi['dosage_instruction'];

                $this->db->table('pharmacy_sale_details')->insert($detailData);

                // Deduct Stock in medicine_batches
                $batch = $this->db->table('medicine_batches')->where('id', $pi['batch_id'])->get()->getRow();
                $newStock = $batch ? max(0, $batch->stock - $pi['qty']) : 0;
                $this->db->table('medicine_batches')->where('id', $pi['batch_id'])->update(['stock' => $newStock]);

                // Deduct Stock in Depo Apotek Rawat Jalan (DEPO-RJ)
                if ($hasWarehouseStock && $hasWarehouses) {
                    try {
                        $depoWh = $this->db->table('pharmacy_warehouses')->where('code', 'DEPO-RJ')->orWhere('type', 'depo')->get()->getRow();
                        $depoWhId = $depoWh ? $depoWh->id : 2;
                        $wsRow = $this->db->table('warehouse_stock')
                                          ->where('warehouse_id', $depoWhId)
                                          ->where('medicine_id', $pi['medicine_id'])
                                          ->where('batch_id', $pi['batch_id'])
                                          ->get()->getRow();
                        if ($wsRow) {
                            $this->db->table('warehouse_stock')->where('id', $wsRow->id)->update(['stock' => max(0, $wsRow->stock - $pi['qty'])]);
                        }
                    } catch (\Throwable $e) {}
                }

                // Record Movement
                try {
                    $this->db->table('stock_movements')->insert([
                        'medicine_id'      => $pi['medicine_id'],
                        'batch_id'         => $pi['batch_id'],
                        'transaction_type' => 'penjualan',
                        'reference_id'     => $saleId,
                        'qty_in'           => 0,
                        'qty_out'          => $pi['qty'],
                        'balance'          => $newStock,
                        'user_id'          => $movementUserId
                    ]);
                } catch (\Throwable $e) {}
            }

            // 4. Update Cash Register Balance
            if ($this->db->tableExists('cash_registers')) {
                try {
                    $activeRegister = $this->db->table('cash_registers')->where('status', 'open')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
                    if ($activeRegister) {
                        $this->db->table('cash_registers')->where('id', $activeRegister->id)->set('balance', 'balance + ' . floatval($grandTotal), false)->update();
                    }
                } catch (\Throwable $e) {}
            }

            // Rincian item untuk summary jurnal
            $itemNames = [];
            foreach ($processedItems as $it) {
                $label = $it['is_racikan'] && !empty($it['racikan_name']) ? ('[Racikan] ' . $it['racikan_name']) : $it['medicine_name'];
                $priceStr = number_format($it['price'] * $it['qty'], 0, ',', '.');
                $tuslaStr = number_format($it['tusla'], 0, ',', '.');
                $embalaseStr = number_format($it['embalase'], 0, ',', '.');
                
                $itemNames[] = $label . ' (' . (int)$it['qty'] . 'x) (Rp ' . $priceStr . ') | tusla (Rp ' . $tuslaStr . ') | embarse (Rp ' . $embalaseStr . ')';
            }
            $itemsSummary = !empty($itemNames) ? "\n" . implode(", \n", $itemNames) : 'Penjualan Farmasi';
            $itemsSummary .= ",\ntotal tusla : Rp " . number_format($tuslaAmount, 0, ',', '.') . "\ntotal embarse : Rp " . number_format($embalaseAmount, 0, ',', '.');

            // Auto-Posting Split Journal (Jurnal Umum Perakun Berimbang)
            $docFeePct = 5.00;
            $docName = '';
            if ($doctorId) {
                $docRow = $this->db->table('doctors')->where('id', $doctorId)->get()->getRow();
                if ($docRow) {
                    $docName = $docRow->name;
                    if (isset($docRow->prescription_fee_percent)) {
                        $docFeePct = (float)$docRow->prescription_fee_percent;
                    }
                }
            }

            $docTag = '';
            if (!empty($docName)) {
                $cleanDoc = (stripos($docName, 'dr') === 0 || stripos($docName, 'Dr') === 0) ? $docName : ('Dr. ' . $docName);
                $docTag = ' (' . $cleanDoc . ')';
            }

            if ($prescriptionType === 'online' || $prescriptionType === 'konsul_online') {
                $saleJournalType = 'online';
                $sourceModule    = 'Kasir Apotek (Konsultasi Online)';
                $descPrefix      = 'Pendapatan Konsultasi Online Apotek';
            } elseif ($prescriptionType === 'resep' || $prescriptionType === 'racikan' || $doctorId) {
                $saleJournalType = 'resep';
                $sourceModule    = 'Kasir Apotek (Obat Resep)';
                $descPrefix      = 'Pendapatan Resep Obat Apotek';
            } else {
                $saleJournalType = 'bebas';
                $sourceModule    = 'Kasir Apotek (Obat Bebas)';
                $descPrefix      = 'Pendapatan Penjualan Obat Bebas';
            }

            $journalDesc = "{$descPrefix} - {$saleNo} ({$customerName}): {$itemsSummary}{$docTag}";

            try {
                $this->journalEngine->postPharmacySplitJournal(
                    $saleJournalType,
                    $saleId,
                    $grandTotal,
                    $journalDesc,
                    $paymentMethod,
                    $docFeePct,
                    $sourceModule,
                    $doctorFeeNominal
                );
            } catch (\Throwable $e) {
                log_message('error', 'Gagal membukukan jurnal penjualan farmasi: ' . $e->getMessage());
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $dbErr = $this->db->error();
                $errDetail = !empty($dbErr['message']) ? (': ' . $dbErr['message']) : '';
                return [
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan saat menyimpan transaksi penjualan obat bebas' . $errDetail
                ];
            }

            return [
                'status'     => 'success',
                'sale_id'    => $saleId,
                'sale_no'    => $saleNo,
                'grand_total'=> $grandTotal,
                'message'    => "Penjualan obat bebas No. {$saleNo} berhasil diselesaikan. Stok obat telah diperbarui."
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in processDirectSale: ' . $e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'Gagal memproses penjualan obat bebas: ' . $e->getMessage()
            ];
        }
    }
}
