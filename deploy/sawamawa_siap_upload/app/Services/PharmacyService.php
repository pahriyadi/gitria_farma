<?php

namespace App\Services;

use App\Services\JournalEngine;

class PharmacyService
{
    protected $db;
    protected $journalEngine;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
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
        foreach ($dispenseDetails as $medId => $details) {
            $med = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
            $medName = $med ? $med->name : "ID {$medId}";
            $action = $details['action'] ?? 'internal';
            $qty = intval($details['qty'] ?? 1);
            $itemDiscount = floatval($details['discount'] ?? 0);

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
                $newStock = $batch->stock - $qty;
                $this->db->table('medicine_batches')->where('id', $batchId)->update(['stock' => $newStock]);

                // Deduct stock in Depo Apotek (DEPO-RJ)
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

                // Add Stock Movement log
                $this->db->table('stock_movements')->insert([
                    'medicine_id'      => $medId,
                    'batch_id'         => $batchId,
                    'transaction_type' => 'resep',
                    'reference_id'     => $prescriptionId,
                    'qty_in'           => 0,
                    'qty_out'          => $qty,
                    'balance'          => $newStock,
                    'user_id'          => session('user_id') ?: 1
                ]);

                // Update prescription details status and discount
                $this->db->table('prescription_details')
                         ->where('prescription_id', $prescriptionId)
                         ->where('medicine_id', $medId)
                         ->update([
                             'discount' => $itemDiscount,
                             'status'   => 'served'
                         ]);

                // Update or Insert in billing_details with per-item discount
                if ($bill && $med) {
                    $price = $med->price;
                    $subtotal = max(0, ($qty * $price) - $itemDiscount);

                    $existingBillItem = $this->db->table('billing_details')
                                                 ->where('billing_id', $bill->id)
                                                 ->where('item_type', 'obat')
                                                 ->where('item_name', $med->name)
                                                 ->get()
                                                 ->getRow();

                    if ($existingBillItem) {
                        $this->db->table('billing_details')
                                 ->where('id', $existingBillItem->id)
                                 ->update([
                                     'qty'      => $qty,
                                     'price'    => $price,
                                     'discount' => $itemDiscount,
                                     'subtotal' => $subtotal
                                 ]);
                    } else {
                        $this->db->table('billing_details')->insert([
                            'billing_id' => $bill->id,
                            'item_type'  => 'obat',
                            'item_name'  => $med->name,
                            'qty'        => $qty,
                            'price'      => $price,
                            'discount'   => $itemDiscount,
                            'subtotal'   => $subtotal
                        ]);
                    }
                }
            } else {
                // Action is 'beli_luar' (resep luar) OR 'cancel' (dibatalkan)
                $statusVal = $action === 'beli_luar' ? 'bought_outside' : 'cancelled';

                $this->db->table('prescription_details')
                         ->where('prescription_id', $prescriptionId)
                         ->where('medicine_id', $medId)
                         ->update([
                             'discount' => 0.00,
                             'status'   => $statusVal
                         ]);

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
                    $totalServices += $d->subtotal;
                } elseif ($d->item_type === 'obat') {
                    $totalMedicines += ($d->qty * $d->price); // Gross medicines
                    $totalDiscounts += ($d->discount ?? 0);   // Discounts from pharmacy
                } elseif ($d->item_type === 'resto') {
                    $totalRestaurant += $d->subtotal;
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
        }

        // 3. Selesaikan Status e-Resep & Catat Timestamp Penyerahan Obat (SLA)
        $this->db->table('prescriptions')->where('id', $prescriptionId)->update([
            'status'       => 'completed',
            'dispensed_at' => date('Y-m-d H:i:s')
        ]);

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
            return [
                'status'  => 'error',
                'message' => 'Gagal memproses penyiapan resep obat.'
            ];
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
        $customerName  = trim($payload['customer_name'] ?? 'Pelanggan Umum');
        $customerPhone = trim($payload['customer_phone'] ?? '');
        $paymentMethod = $payload['payment_method'] ?? 'tunai';
        $paidAmount    = floatval($payload['paid_amount'] ?? 0);
        $notes         = trim($payload['notes'] ?? '');
        $cashierId     = intval($payload['cashier_id'] ?? (session('user_id') ?: 1));
        $items         = $payload['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            return [
                'status'  => 'error',
                'message' => 'Pilih minimal satu item obat untuk transaksi penjualan langsung.'
            ];
        }

        $this->db->transStart();

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

            if ($medId <= 0 || $qty <= 0) continue;

            $med = $this->db->table('medicines')->where('id', $medId)->get()->getRow();
            if (!$med) continue;

            $unitPrice = floatval($med->price);
            $itemSubtotal = max(0, ($unitPrice * $qty) - $discount);

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
                'subtotal'           => $itemSubtotal,
                'dosage_instruction' => $dosage,
                'medicine_name'      => $med->name,
                'unit'               => $med->unit
            ];
        }

        if (empty($processedItems)) {
            $this->db->transRollback();
            return [
                'status'  => 'error',
                'message' => 'Tidak ada item obat valid yang dapat diproses.'
            ];
        }

        $grandTotal = max(0, $totalGross - $totalDiscount);
        $changeAmount = max(0, $paidAmount - $grandTotal);

        // 2. Insert Sale Record
        $saleData = [
            'sale_no'         => $saleNo,
            'customer_name'   => $customerName ?: 'Pelanggan Umum',
            'customer_phone'  => $customerPhone,
            'sale_date'       => date('Y-m-d'),
            'total_amount'    => $totalGross,
            'discount_amount' => $totalDiscount,
            'grand_total'     => $grandTotal,
            'payment_method'  => $paymentMethod,
            'paid_amount'     => $paidAmount,
            'change_amount'   => $changeAmount,
            'cashier_id'      => $cashierId,
            'notes'           => $notes,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ];
        $this->db->table('pharmacy_sales')->insert($saleData);
        $saleId = $this->db->insertID();

        // 3. Insert Details & Deduct Batch Stock
        foreach ($processedItems as $pi) {
            $this->db->table('pharmacy_sale_details')->insert([
                'sale_id'            => $saleId,
                'medicine_id'        => $pi['medicine_id'],
                'batch_id'           => $pi['batch_id'],
                'qty'                => $pi['qty'],
                'price'              => $pi['price'],
                'discount'           => $pi['discount'],
                'subtotal'           => $pi['subtotal'],
                'dosage_instruction' => $pi['dosage_instruction'],
                'created_at'         => date('Y-m-d H:i:s')
            ]);

            // Deduct Stock
            $batch = $this->db->table('medicine_batches')->where('id', $pi['batch_id'])->get()->getRow();
            $newStock = $batch ? max(0, $batch->stock - $pi['qty']) : 0;
            $this->db->table('medicine_batches')->where('id', $pi['batch_id'])->update(['stock' => $newStock]);

            // Deduct Stock in Depo Apotek Rawat Jalan (DEPO-RJ)
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

            // Record Movement
            $this->db->table('stock_movements')->insert([
                'medicine_id'      => $pi['medicine_id'],
                'batch_id'         => $pi['batch_id'],
                'transaction_type' => 'penjualan',
                'reference_id'     => $saleId,
                'qty_in'           => 0,
                'qty_out'          => $pi['qty'],
                'balance'          => $newStock,
                'user_id'          => $cashierId
            ]);
        }

        // 4. Update Cash Register Balance
        $activeRegister = $this->db->table('cash_registers')->where('status', 'open')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
        if ($activeRegister) {
            $this->db->table('cash_registers')->where('id', $activeRegister->id)->set('balance', 'balance + ' . floatval($grandTotal), false)->update();
        }

        // Rincian item obat bebas untuk keterangan jurnal akuntansi
        $itemNames = [];
        foreach ($items as $it) {
            $m = $this->db->table('medicines')->where('id', $it['medicine_id'])->get()->getRow();
            if ($m) {
                $itemNames[] = $m->name . ' (' . (int)$it['qty'] . 'x @Rp ' . number_format($m->price, 0, ',', '.') . ')';
            }
        }
        $itemsSummary = !empty($itemNames) ? implode(', ', $itemNames) : 'Obat Bebas';

        // Auto-Posting Double-Entry Accounting Journal
        $this->journalEngine->postJournal(
            'PHARMACY_SALE',
            $saleId,
            $grandTotal,
            'Pendapatan Penjualan Obat Bebas Apotek - ' . $saleNo . ' (' . $customerName . '): ' . $itemsSummary,
            $paymentMethod,
            'Farmasi Apotek'
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return [
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan transaksi penjualan obat bebas.'
            ];
        }

        return [
            'status'     => 'success',
            'sale_id'    => $saleId,
            'sale_no'    => $saleNo,
            'grand_total'=> $grandTotal,
            'message'    => "Penjualan obat bebas No. {$saleNo} berhasil diselesaikan. Stok obat telah diperbarui."
        ];
    }
}
