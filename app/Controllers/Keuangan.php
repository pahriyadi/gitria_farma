<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\JournalEngine;

class Keuangan extends BaseController
{
    protected $journalEngine;

    public function __construct()
    {
        $this->journalEngine = new JournalEngine();
    }

    public function kasir()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $billingId     = $this->request->getPost('billing_id');
            $paymentMethod = $this->request->getPost('payment_method') ?: 'tunai';
            $discount      = floatval($this->request->getPost('discount'));
            $paidAmount    = floatval($this->request->getPost('paid_amount'));
            $notes         = trim($this->request->getPost('notes') ?: '');

            $db->transStart();

            try {
                $bill = $db->table('billing_transactions')->where('id', $billingId)->get()->getRow();
                if (!$bill || $bill->status === 'paid') {
                    session()->setFlashdata('error', 'Tagihan tidak ditemukan atau sudah dibayar.');
                    return redirect()->to(base_url('keuangan/kasir'));
                }

                // Calculate gross subtotal and net grand total with discount
                $grossSubtotal = (float)$bill->total_services + (float)$bill->total_medicines + (float)$bill->total_restaurant;
                $grandTotal = max(0, $grossSubtotal - $discount);

                // Validation for cash payment
                if ($paymentMethod === 'tunai' || $paymentMethod === 'cash') {
                    if ($paidAmount < $grandTotal) {
                        session()->setFlashdata('error', 'Uang tunai yang diterima (Rp ' . number_format($paidAmount, 0, ',', '.') . ') kurang dari total tagihan bersih (Rp ' . number_format($grandTotal, 0, ',', '.') . ').');
                        return redirect()->to(base_url('keuangan/kasir'));
                    }
                } else {
                    if ($paidAmount <= 0) {
                        $paidAmount = $grandTotal;
                    }
                }

                $changeAmount = max(0, $paidAmount - $grandTotal);
                $userId = session()->get('user_id') ?: 1;

                // Generate Receipt Number (RCP-YYYYMMDD-XXXX)
                $today = date('Ymd');
                $lastReceipt = $db->table('cash_transactions')
                                   ->where('DATE(created_at)', date('Y-m-d'))
                                   ->orderBy('id', 'DESC')
                                   ->limit(1)
                                   ->get()
                                   ->getRow();
                $nextNum = 1;
                if ($lastReceipt && preg_match('/RCP-\d+-(\d+)/', $lastReceipt->receipt_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
                $receiptNo = 'RCP-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                // Default Cash Register id=1 (Main Register)
                $register = $db->table('cash_registers')->where('id', 1)->get()->getRow();
                if (!$register) {
                    $db->table('cash_registers')->insert([
                        'id'      => 1,
                        'name'    => 'Kasir Utama',
                        'balance' => 0.00,
                        'status'  => 'open'
                    ]);
                    $currBalance = 0.00;
                } else {
                    $currBalance = (float) $register->balance;
                }

                // Insert Cash Transaction
                $db->table('cash_transactions')->insert([
                    'receipt_no'       => $receiptNo,
                    'billing_id'       => $billingId,
                    'cash_register_id' => 1,
                    'amount'           => $grandTotal,
                    'paid_amount'      => $paidAmount,
                    'change_amount'    => $changeAmount,
                    'cashier_id'       => $userId,
                    'payment_method'   => $paymentMethod,
                    'notes'            => $notes,
                    'created_at'       => date('Y-m-d H:i:s')
                ]);

                $cashTxId = $db->insertID();

                // Update Billing Transaction Status to Paid and record payment_method
                $db->table('billing_transactions')
                   ->where('id', $billingId)
                   ->update([
                       'discount'       => $discount,
                       'grand_total'    => $grandTotal,
                       'payment_method' => $paymentMethod,
                       'status'         => 'paid'
                   ]);

                // Update Patient Visit Status: jika ada resep menunggu, arahkan ke 'prescription' (ambil obat); jika tidak, 'completed'
                $hasWaitingPrescription = false;
                if ($bill->visit_id) {
                    $presc = $db->table('prescriptions')
                                ->where('visit_id', $bill->visit_id)
                                ->where('status', 'waiting')
                                ->get()
                                ->getRow();
                    if ($presc) {
                        $hasWaitingPrescription = true;
                    }

                    $nextVisitStatus = $hasWaitingPrescription ? 'prescription' : 'completed';
                    $db->table('patient_visits')
                       ->where('id', $bill->visit_id)
                       ->update([
                           'status'     => $nextVisitStatus,
                           'updated_at' => date('Y-m-d H:i:s')
                       ]);
                }

                // Update Cash Register Balance if cash payment
                if ($paymentMethod === 'tunai' || $paymentMethod === 'cash') {
                    $db->table('cash_registers')
                       ->where('id', 1)
                       ->update(['balance' => $currBalance + $grandTotal]);
                }

                // 1. Post Journals Automatically via Journal Engine dengan Rincian Per-Item
                $visit = $bill->visit_id ? $db->table('patient_visits')->where('id', $bill->visit_id)->get()->getRow() : null;
                $patient = ($visit && $visit->patient_id) ? $db->table('patients')->where('id', $visit->patient_id)->get()->getRow() : null;
                $queue = $bill->visit_id ? $db->table('queue_numbers')->where('visit_id', $bill->visit_id)->get()->getRow() : null;

                $pName = $patient ? $patient->name : 'Pasien Umum';
                $qCode = $queue ? ($queue->queue_no ?: ($visit ? $visit->no_visit : '')) : ($visit ? $visit->no_visit : '');
                $patientTag = $pName . ($qCode ? ' / ' . $qCode : '');

                // Ambil seluruh rincian tagihan per kategori
                $allBillDetails = $db->table('billing_details')->where('billing_id', $billingId)->get()->getResult();

                // 1a. Jurnal Pendapatan Layanan Medis & Tindakan Klinik (Per-Item Tindakan/Jasa)
                if ($bill->total_services > 0) {
                    $docFeeMedisPct = 66.67; // default
                    $docFeeMedisNominal = null;
                    $primaryDoc = null;
                    $doctorRow = null;

                    if ($visit && $visit->doctor_id) {
                        $primaryDoc = $db->table('doctors')->where('id', $visit->doctor_id)->get()->getRow();
                        $doctorRow = $primaryDoc;
                        if ($primaryDoc) {
                            // Cari penugasan spesifik dokter untuk poli kunjungan saat ini
                            if (!empty($visit->polyclinic_id)) {
                                $matchPoli = $db->table('doctors')
                                                ->where('nik_employee', $primaryDoc->nik_employee)
                                                ->where('polyclinic_id', $visit->polyclinic_id)
                                                ->get()
                                                ->getRow();
                                if ($matchPoli && (float)$matchPoli->fee_per_pasien > 0) {
                                    $doctorRow = $matchPoli;
                                }
                            }
                        }
                        if ($doctorRow && isset($doctorRow->fee_per_pasien) && (float)$doctorRow->fee_per_pasien > 0) {
                            if (($doctorRow->fee_type ?? 'percentage') === 'fixed_amount') {
                                $docFeeMedisNominal = (float)$doctorRow->fee_per_pasien;
                            } else {
                                $docFeeMedisPct = (float)$doctorRow->fee_per_pasien;
                            }
                        }
                    }

                    // Format dokter: Dr. NamaDokter
                    $docName = $doctorRow ? $doctorRow->name : ($primaryDoc ? $primaryDoc->name : '');
                    $docTag = '';
                    if (!empty($docName)) {
                        $cleanDoc = (stripos($docName, 'dr') === 0 || stripos($docName, 'Dr') === 0) ? $docName : ('Dr. ' . $docName);
                        $docTag = ' (' . $cleanDoc . ')';
                    }

                    $medisList = [];
                    foreach ($allBillDetails as $bd) {
                        if ($bd->item_type === 'medis') {
                            $medisList[] = $bd->item_name;
                        }
                    }
                    $medisSummary = !empty($medisList) ? implode(', ', $medisList) : 'Konsultasi & Tindakan Medis Dokter';
                    $descMedis = "Pendapatan Layanan Medis - {$bill->billing_no} ({$pName}): {$medisSummary}{$docTag}";

                    $this->journalEngine->postClinicSplitJournal($billingId, $bill->total_services, $descMedis, $paymentMethod, $docFeeMedisPct, 'Kasir Utama', $docFeeMedisNominal);
                }

                // 1b. Jurnal Pendapatan Farmasi & Penjualan Obat (Split Journal Perakun Sesuai Skema Klien)
                // Jika pasien memiliki e-resep yang harus disiapkan dan diambil di Apotek/Farmasi,
                // penjurnalan PENJUALAN_OBAT_RESEP akan otomatis dibukukan saat penyerahan obat selesai di Modul Farmasi.
                // Jika tidak ada antrean resep farmasi (misal obat bebas langsung di kasir), dijurnal langsung di sini.
                if ($bill->total_medicines > 0 && !$hasWaitingPrescription) {
                    $obatList = [];
                    $totalTusla = 0;
                    $totalEmbalase = 0;
                    $calcMedTotal = 0;
                    foreach ($allBillDetails as $bd) {
                        if ($bd->item_type === 'obat') {
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
                    }

                    $actualMedAmount = $calcMedTotal > 0 ? $calcMedTotal : (float)$bill->total_medicines;
                    
                    $obatSummary = !empty($obatList) ? "\n" . implode(", \n", array_unique($obatList)) : 'e-Resep Obat';
                    if (!empty($obatList)) {
                        $obatSummary .= ",\ntotal tusla : Rp " . number_format($totalTusla, 0, ',', '.') . "\ntotal embarse : Rp " . number_format($totalEmbalase, 0, ',', '.');
                    }
                    $descObat = "Pendapatan Farmasi & Resep Obat - {$bill->billing_no} ({$patientTag}): {$obatSummary}";

                    $docFeePct = 5.00;
                    if ($visit && $visit->doctor_id) {
                        $doctorRow = $db->table('doctors')->where('id', $visit->doctor_id)->get()->getRow();
                        if ($doctorRow && isset($doctorRow->prescription_fee_percent)) {
                            $docFeePct = (float)$doctorRow->prescription_fee_percent;
                        }
                    }

                    $this->journalEngine->postPharmacySplitJournal('resep', $billingId, $actualMedAmount, $descObat, $paymentMethod, $docFeePct, 'Apotek (Obat Resep)');
                }

                // 1c. Jurnal Pendapatan Resto & Nutrisi (Per-Item Menu)
                if ($bill->total_restaurant > 0) {
                    $restoList = [];
                    foreach ($allBillDetails as $bd) {
                        if ($bd->item_type === 'resto') {
                            $restoList[] = $bd->item_name . ' (' . (int)$bd->qty . 'x @Rp ' . number_format($bd->price, 0, ',', '.') . ')';
                        }
                    }
                    $restoSummary = !empty($restoList) ? implode(', ', $restoList) : 'Menu Resto & Nutrisi';
                    $descResto = "Pendapatan Resto & Nutrisi - {$bill->billing_no} ({$patientTag}): {$restoSummary}";
                    $this->journalEngine->postJournal('RESTO_SALE', $billingId, $bill->total_restaurant, $descResto, $paymentMethod, 'Kasir Utama');
                }

                // 2. Insert Fee Transactions for Payroll / HRD (No Journal)
                if ($bill->visit_id && $bill->total_services > 0) {
                    $visit = $db->table('patient_visits')->where('id', $bill->visit_id)->get()->getRow();
                    if ($visit && $visit->doctor_id) {
                        $doctor = $db->table('doctors')->where('id', $visit->doctor_id)->get()->getRow();
                        if ($doctor && (float)$doctor->fee_per_pasien > 0) {
                            $docPct = (float)$doctor->fee_per_pasien;
                            $totalDoctorFee = round(($bill->total_services * ($docPct / 100.0)), 2);
                            
                            $db->table('fee_transactions')->insert([
                                'visit_id'    => $bill->visit_id,
                                'doctor_id'   => $visit->doctor_id,
                                'fee_rule_id' => null,
                                'amount'      => $totalDoctorFee,
                                'created_at'  => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    session()->setFlashdata('error', 'Gagal memproses transaksi kasir di database.');
                    return redirect()->to(base_url('keuangan/kasir'));
                }

                \App\Services\AuditService::log(
                    'PAYMENT',
                    'Kasir Utama',
                    "Pelunasan tagihan billing " . ($bill->billing_no ?? "#{$billingId}") . " No. Kwitansi {$receiptNo} sebesar Rp " . number_format($grandTotal, 0, ',', '.') . " ({$paymentMethod})",
                    'billing_transactions',
                    (int) $billingId
                );

                // Push Notifikasi Operasional Pembayaran Lunas ke Seluruh Modul
                try {
                    $notif = new \App\Services\NotificationService();
                    $notif->send([
                        'notification_key' => 'billing_paid_' . $billingId . '_' . time(),
                        'category'         => 'keuangan',
                        'type'             => 'info',
                        'badge'            => 'Lunas',
                        'icon'             => 'fas fa-receipt',
                        'title'            => 'Pembayaran Lunas (Kwitansi #' . $receiptNo . ')',
                        'message'          => "Tagihan Billing #" . ($bill->billing_no ?? $billingId) . " sebesar Rp " . number_format($grandTotal, 0, ',', '.') . " telah lunas via {$paymentMethod}.",
                        'link'             => base_url('keuangan/kasir'),
                        'target_roles'     => 'dokter,perawat,kasir,accounting,super admin,administrator',
                        'sender_name'      => 'Kasir Utama'
                    ]);

                    $patientObj = $visit ? $db->table('patients')->where('id', $visit->patient_id)->get()->getRow() : null;
                    $queueObj = $db->table('queue_numbers')->where('visit_id', $bill->visit_id)->get()->getRow();
                    $qNum = $queueObj ? ($queueObj->queue_no ?: ($visit->no_visit ?? 'A-001')) : ($visit->no_visit ?? 'A-001');
                    $patientName = $patientObj ? $patientObj->name : 'Pasien';

                    if ($hasWaitingPrescription) {
                        $notif->send([
                            'notification_key' => 'prescription_ready_dispense_' . $billingId . '_' . time(),
                            'category'         => 'farmasi',
                            'type'             => 'success',
                            'badge'            => 'Siap Disiapkan',
                            'icon'             => 'fas fa-check-circle',
                            'title'            => 'Resep Siap Disiapkan di Apotek',
                            'message'          => "Tagihan pasien {$patientName} ({$qNum}) telah lunas di Kasir. Obat siap disiapkan & diserahkan.",
                            'link'             => base_url('apotek/resep'),
                            'target_roles'     => 'apoteker,farmasi,super admin,administrator',
                            'sender_name'      => 'Kasir Utama'
                        ]);
                        // Daftarkan event pemanggilan ke antrean server pusat (LiveSync TV)
                        try {
                            $qCallService = new \App\Services\QueueCallService();
                            $qCallService->triggerCall([
                                'service_type'   => 'farmasi',
                                'counter_name'   => 'Loket Farmasi dan Apotek',
                                'queue_number'   => $qNum,
                                'patient_name'   => $patientName,
                                'visit_id'       => $bill->visit_id,
                                'call_action'    => 'call',
                                'call_priority'  => 1,
                            ]);
                        } catch (\Throwable $e) {}

                        // Voice Trigger: Pasien Lunas -> Menuju ke Apotek untuk Pengambilan Obat
                        session()->setFlashdata('voice_trigger', [
                            'action'       => 'to_pharmacy',
                            'queue_number' => $qNum,
                            'patient_name' => $patientName
                        ]);
                    } else {
                        // Daftarkan event pemanggilan selesai ke antrean server pusat
                        try {
                            $qCallService = new \App\Services\QueueCallService();
                            $qCallService->triggerCall([
                                'service_type'   => 'completed',
                                'counter_name'   => 'Selesai',
                                'queue_number'   => $qNum,
                                'patient_name'   => $patientName,
                                'visit_id'       => $bill->visit_id,
                                'call_action'    => 'completed',
                                'call_priority'  => 2,
                            ]);
                        } catch (\Throwable $e) {}

                        // Voice Trigger: Pasien Lunas Selesai (Tanpa Resep)
                        session()->setFlashdata('voice_trigger', [
                            'action'       => 'to_completed',
                            'queue_number' => $qNum,
                            'patient_name' => $patientName
                        ]);
                    }
                } catch (\Throwable $e) {}

                session()->setFlashdata('success', 'Pembayaran berhasil diproses! Kwitansi: ' . $receiptNo . ($changeAmount > 0 ? ' (Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.') . ')' : ''));
                session()->setFlashdata('last_receipt_id', $cashTxId);
                session()->setFlashdata('last_receipt_no', $receiptNo);
                session()->setFlashdata('last_visit_id', $bill->visit_id);
            } catch (\Throwable $e) {
                log_message('error', 'Error kasir payment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
                session()->setFlashdata('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }

            return redirect()->to(base_url('keuangan/kasir'));
        }

        // Fetch unpaid active billings
        $billings = $db->table('billing_transactions bt')
                       ->select('bt.*, 
                                 pv.no_visit, 
                                 pv.payment_method as visit_payment_method,
                                 pv.insurance_id,
                                 ip.name as insurance_name,
                                 ip.code as insurance_code,
                                 p.name as patient_name, 
                                 p.no_rm, 
                                 p.phone as patient_phone,
                                 COALESCE(p.membership_tier, \'regular\') as membership_tier,
                                 pv.status as visit_status')
                       ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                       ->join('patients p', 'p.id = pv.patient_id', 'left')
                       ->join('insurance_providers ip', 'ip.id = pv.insurance_id', 'left')
                       ->whereIn('bt.status', ['draft', 'open', 'partial'])
                       ->where('bt.status !=', 'paid')
                       ->where('bt.status !=', 'cancelled')
                       ->orderBy('bt.id', 'DESC')
                       ->get()
                       ->getResult();

        // Fetch paid / completed billing transactions
        $completedTransactions = $db->table('cash_transactions')
                                    ->select('cash_transactions.*, 
                                              billing_transactions.billing_no,
                                              billing_transactions.total_services,
                                              billing_transactions.total_medicines,
                                              billing_transactions.total_restaurant,
                                              billing_transactions.discount,
                                              billing_transactions.grand_total,
                                              patient_visits.id as visit_id,
                                              patient_visits.no_visit,
                                              patients.name as patient_name,
                                              patients.no_rm,
                                              polyclinics.name as poly_name,
                                              tindakan.name as tindakan_name,
                                              COALESCE(doctors.name, "Dokter Klinik") as doctor_name,
                                              users.username as cashier_name')
                                    ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id')
                                    ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                                    ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                                    ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                                    ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                                    ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                                    ->join('users', 'users.id = cash_transactions.cashier_id', 'left')
                                    ->orderBy('cash_transactions.id', 'DESC')
                                    ->limit(100)
                                    ->get()
                                    ->getResult();

        // Details mapping for JS
        $billingDetails = [];
        $allTxBillIds = array_unique(array_merge(array_column($billings, 'id'), array_column($completedTransactions, 'billing_id')));
        if (!empty($allTxBillIds)) {
            $details = $db->table('billing_details')->whereIn('billing_id', $allTxBillIds)->get()->getResult();
            foreach ($details as $dt) {
                $billingDetails[$dt->billing_id][] = $dt;
            }
        }

        $paymentMethods = $db->table('payment_methods')
                             ->where('is_active', 1)
                             ->orderBy('category', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();

        $data = [
            'title'                 => 'Kasir & Riwayat Pembayaran Pasien',
            'active_menu'           => 'kasir',
            'billings'              => $billings,
            'completedTransactions' => $completedTransactions,
            'billingDetails'        => $billingDetails,
            'paymentMethods'        => $paymentMethods
        ];

        return view('keuangan/kasir', $data);
    }

    /**
     * Membatalkan Pesanan / Tagihan Antrean Kasir Utama
     */
    public function batalTagihan()
    {
        $db = \Config\Database::connect('default');
        $billingId = $this->request->getPost('billing_id');
        $cancelReason = trim($this->request->getPost('cancel_reason') ?: 'Dibatalkan oleh kasir');

        if (empty($billingId)) {
            session()->setFlashdata('error', 'ID Tagihan tidak valid.');
            return redirect()->to(base_url('keuangan/kasir'));
        }

        $db->transStart();

        try {
            $bill = $db->table('billing_transactions')->where('id', $billingId)->get()->getRow();
            if (!$bill) {
                session()->setFlashdata('error', 'Data tagihan tidak ditemukan.');
                return redirect()->to(base_url('keuangan/kasir'));
            }

            // 1. Update status tagihan menjadi 'cancelled'
            $db->table('billing_transactions')
               ->where('id', $billingId)
               ->update([
                   'status'     => 'cancelled',
                   'notes'      => ($bill->notes ? $bill->notes . ' | ' : '') . 'Batal: ' . $cancelReason . ' (' . date('d/m/Y H:i') . ' oleh ' . (session('fullname') ?: session('username')) . ')',
                   'updated_at' => date('Y-m-d H:i:s')
               ]);

            // 2. Batalkan kunjungan pasien (patient_visits) jika ada
            if (!empty($bill->visit_id)) {
                $db->table('patient_visits')
                   ->where('id', $bill->visit_id)
                   ->update([
                       'status'     => 'cancelled',
                       'updated_at' => date('Y-m-d H:i:s')
                   ]);

                // 3. Batalkan nomor antrean (queue_numbers)
                $db->table('queue_numbers')
                   ->where('visit_id', $bill->visit_id)
                   ->update(['status' => 'cancelled']);

                // 4. Batalkan resep apotek jika masih berstatus waiting/draft
                $db->table('prescriptions')
                   ->where('visit_id', $bill->visit_id)
                   ->whereIn('status', ['waiting', 'draft'])
                   ->update(['status' => 'cancelled']);
            }

            // 5. Catat log audit sistem
            $db->table('audit_logs')->insert([
                'user_id'    => session('user_id') ?: 1,
                'action'     => 'CANCEL_BILLING',
                'module'     => 'keuangan',
                'table_name' => 'billing_transactions',
                'record_id'  => $billingId,
                'old_value'  => 'status: ' . $bill->status . ', total: ' . $bill->grand_total,
                'new_value'  => 'status: cancelled | Alasan: ' . $cancelReason,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Gagal memproses pembatalan tagihan.');
                return redirect()->to(base_url('keuangan/kasir'));
            }

            session()->setFlashdata('success', 'Pesanan / Tagihan No. ' . esc($bill->billing_no) . ' berhasil dibatalkan dari antrean kasir.');
            return redirect()->to(base_url('keuangan/kasir'));

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error in batalTagihan: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat membatalkan tagihan: ' . $e->getMessage());
            return redirect()->to(base_url('keuangan/kasir'));
        }
    }

    /**
     * Membatalkan / Void Transaksi Pembayaran Kasir yang Sudah Lunas
     */
    public function voidTransaksi()
    {
        $db = \Config\Database::connect('default');
        $txId = $this->request->getPost('transaction_id');
        $voidReason = trim($this->request->getPost('void_reason') ?: 'Pembatalan/Void pembayaran oleh kasir');

        if (empty($txId)) {
            session()->setFlashdata('error', 'ID Transaksi tidak valid.');
            return redirect()->to(base_url('keuangan/kasir'));
        }

        $db->transStart();

        try {
            $tx = $db->table('cash_transactions')->where('id', $txId)->get()->getRow();
            if (!$tx) {
                session()->setFlashdata('error', 'Transaksi pembayaran tidak ditemukan.');
                return redirect()->to(base_url('keuangan/kasir'));
            }

            // 1. Kurangi saldo kas register jika pembayaran tunai
            if ($tx->payment_method === 'tunai' || $tx->payment_method === 'cash') {
                $reg = $db->table('cash_registers')->where('id', $tx->cash_register_id ?: 1)->get()->getRow();
                if ($reg) {
                    $newBalance = max(0, (float)$reg->balance - (float)$tx->amount);
                    $db->table('cash_registers')->where('id', $reg->id)->update(['balance' => $newBalance]);
                }
            }

            // 2. Batalkan status billing_transactions
            if ($tx->billing_id) {
                $bill = $db->table('billing_transactions')->where('id', $tx->billing_id)->get()->getRow();
                $db->table('billing_transactions')
                   ->where('id', $tx->billing_id)
                   ->update([
                       'status'     => 'cancelled',
                       'notes'      => ($bill ? $bill->notes . ' | ' : '') . 'Void Kwitansi ' . $tx->receipt_no . ': ' . $voidReason,
                       'updated_at' => date('Y-m-d H:i:s')
                   ]);

                if ($bill && $bill->visit_id) {
                    $db->table('patient_visits')
                       ->where('id', $bill->visit_id)
                       ->update(['status' => 'cancelled', 'updated_at' => date('Y-m-d H:i:s')]);

                    $db->table('queue_numbers')
                       ->where('visit_id', $bill->visit_id)
                       ->update(['status' => 'cancelled']);

                    $db->table('prescriptions')
                       ->where('visit_id', $bill->visit_id)
                       ->update(['status' => 'cancelled']);
                }
            }

            // 3. Jurnal Pembalik (Reversal Entry)
            $revDesc = "VOID/Pembatalan Pembayaran Kwitansi " . $tx->receipt_no . " - " . $voidReason;
            $this->journalEngine->postJournal('EXPENSE', $tx->id, $tx->amount, $revDesc, $tx->payment_method, 'Kasir Utama');

            // 4. Hapus record transaksi kas
            $db->table('cash_transactions')->where('id', $txId)->delete();

            // 5. Catat log audit sistem
            $db->table('audit_logs')->insert([
                'user_id'    => session('user_id') ?: 1,
                'action'     => 'VOID_PAYMENT',
                'module'     => 'keuangan',
                'table_name' => 'cash_transactions',
                'record_id'  => $txId,
                'old_value'  => 'receipt_no: ' . $tx->receipt_no . ', amount: ' . $tx->amount,
                'new_value'  => 'status: void/deleted | Alasan: ' . $voidReason,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Gagal memproses pembatalan pembayaran.');
                return redirect()->to(base_url('keuangan/kasir'));
            }

            session()->setFlashdata('success', 'Transaksi Kwitansi ' . esc($tx->receipt_no) . ' berhasil dibatalkan (VOID).');
            return redirect()->to(base_url('keuangan/kasir'));

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Error in voidTransaksi: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat membatalkan transaksi: ' . $e->getMessage());
            return redirect()->to(base_url('keuangan/kasir'));
        }
    }

    /**
     * Cetak Kwitansi Pembayaran Resmi Pasien
     */
    public function cetakKwitansi($id)
    {
        $db = \Config\Database::connect('default');

        $receipt = $db->table('cash_transactions')
                      ->select('cash_transactions.*, 
                                billing_transactions.billing_no,
                                billing_transactions.total_services,
                                billing_transactions.total_medicines,
                                billing_transactions.total_restaurant,
                                billing_transactions.discount,
                                billing_transactions.grand_total,
                                patient_visits.id as visit_id,
                                patient_visits.no_visit,
                                patient_visits.visit_date,
                                patients.name as patient_name,
                                patients.no_rm,
                                patients.nik,
                                patients.phone,
                                patients.address,
                                polyclinics.name as poly_name,
                                tindakan.name as tindakan_name,
                                COALESCE(doctors.name, "Dokter Pemeriksa") as doctor_name,
                                users.username as cashier_name')
                      ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id')
                      ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                      ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                      ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                      ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                      ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                      ->join('users', 'users.id = cash_transactions.cashier_id', 'left')
                      ->where('cash_transactions.id', $id)
                      ->orWhere('cash_transactions.billing_id', $id)
                      ->get()
                      ->getRow();

        if (!$receipt) {
            session()->setFlashdata('error', 'Kwitansi pembayaran tidak ditemukan.');
            return redirect()->to(base_url('keuangan/kasir'));
        }

        $details = $db->table('billing_details')
                      ->where('billing_id', $receipt->billing_id)
                      ->get()
                      ->getResult();

        $data = [
            'title'   => 'Kwitansi Pembayaran ' . $receipt->receipt_no,
            'receipt' => $receipt,
            'details' => $details
        ];

        return view('keuangan/cetak_kwitansi', $data);
    }

    /**
     * AJAX endpoint to fetch single billing transaction with all details
     */
    public function getBillingDetails($id)
    {
        $db = \Config\Database::connect('default');
        $bill = $db->table('billing_transactions bt')
                   ->select('bt.*, pv.no_visit, p.name as patient_name, p.no_rm, pv.status as visit_status')
                   ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                   ->join('patients p', 'p.id = pv.patient_id', 'left')
                   ->where('bt.id', $id)
                   ->get()
                   ->getRow();

        if (!$bill) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tagihan tidak ditemukan']);
        }

        $items = $db->table('billing_details')
                    ->where('billing_id', $id)
                    ->get()
                    ->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'bill'   => $bill,
            'items'  => $items
        ]);
    }

    /**
     * Dashboard Rekap Kasir Harian & Tutup Shift (/keuangan/rekap-harian)
     */
    public function rekapHarian()
    {
        $db = \Config\Database::connect('default');
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        // 1. Transactions on date
        $transactions = $db->table('cash_transactions')
                           ->select('cash_transactions.*, 
                                     billing_transactions.billing_no,
                                     billing_transactions.total_services,
                                     billing_transactions.total_medicines,
                                     billing_transactions.total_restaurant,
                                     billing_transactions.discount,
                                     patients.name as patient_name,
                                     patients.no_rm,
                                     users.username as cashier_name')
                           ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id')
                           ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('users', 'users.id = cash_transactions.cashier_id', 'left')
                           ->where('DATE(cash_transactions.created_at)', $date)
                           ->orderBy('cash_transactions.id', 'ASC')
                           ->get()
                           ->getResult();

        // 2. Metrics & Breakdown
        $totalCollected = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalBank = 0;
        $totalServices = 0;
        $totalMedicines = 0;
        $totalRestaurant = 0;

        foreach ($transactions as $t) {
            $amt = (float)$t->amount;
            $totalCollected += $amt;
            $totalServices += (float)$t->total_services;
            $totalMedicines += (float)$t->total_medicines;
            $totalRestaurant += (float)$t->total_restaurant;

            $pm = strtolower($t->payment_method);
            if (str_contains($pm, 'tunai') || str_contains($pm, 'cash')) {
                $totalCash += $amt;
            } elseif (str_contains($pm, 'qris')) {
                $totalQris += $amt;
            } else {
                $totalBank += $amt;
            }
        }

        // 3. Petty Cash Expenses on date
        $expenses = $db->table('journal_entries')
                       ->select('journal_entries.*, journal_entry_details.debit as amount, accounts.name as account_name, accounts.code as account_code')
                       ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                       ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                       ->where('journal_entries.entry_date', $date)
                       ->where('accounts.type', 'expense')
                       ->where('journal_entry_details.debit >', 0)
                       ->get()
                       ->getResult();

        $totalExpenses = 0;
        foreach ($expenses as $exp) {
            $totalExpenses += (float)$exp->amount;
        }

        // 4. Pharmacy OTC & Direct Prescription Sales on date
        $pharmacySales = $db->table('pharmacy_sales')
                            ->select('pharmacy_sales.*, COALESCE(doctors.name, "Non-Resep / Umum") as doctor_name, users.username as cashier_name')
                            ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                            ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                            ->where('DATE(pharmacy_sales.created_at)', $date)
                            ->orderBy('pharmacy_sales.id', 'DESC')
                            ->get()->getResult();

        $totalPharmacySales    = 0;
        $totalPharmacyTusla    = 0;
        $totalPharmacyEmbalase = 0;
        $totalPharmacyFeeDoc   = 0;

        foreach ($pharmacySales as $ps) {
            $totalPharmacySales    += (float)$ps->grand_total;
            $totalPharmacyTusla    += (float)($ps->tusla_amount ?? 0);
            $totalPharmacyEmbalase += (float)($ps->embalase_amount ?? 0);
            if (!empty($ps->doctor_id)) {
                $totalPharmacyFeeDoc += round((float)$ps->total_amount * 0.05, 2);
            }
        }

        // 5. Main Cash Register Info
        $register = $db->table('cash_registers')->where('id', 1)->get()->getRow();
        $regBalance = $register ? (float)$register->balance : 0.0;

        $data = [
            'title'                 => 'Rekap Penerimaan Kasir & Laporan Shift',
            'active_menu'           => 'keuangan-rekap',
            'date'                  => $date,
            'transactions'          => $transactions,
            'expenses'              => $expenses,
            'totalCollected'        => $totalCollected,
            'totalCash'             => $totalCash,
            'totalQris'             => $totalQris,
            'totalBank'             => $totalBank,
            'totalServices'         => $totalServices,
            'totalMedicines'        => $totalMedicines,
            'totalRestaurant'       => $totalRestaurant,
            'totalExpenses'         => $totalExpenses,
            'pharmacySales'         => $pharmacySales,
            'totalPharmacySales'    => $totalPharmacySales,
            'totalPharmacyTusla'    => $totalPharmacyTusla,
            'totalPharmacyEmbalase' => $totalPharmacyEmbalase,
            'totalPharmacyFeeDoc'   => $totalPharmacyFeeDoc,
            'regBalance'            => $regBalance
        ];

        return view('keuangan/rekap_harian', $data);
    }

    /**
     * Cetak Dokumen Rekap Kasir Harian (Print-Ready)
     */
    public function cetakRekapHarian()
    {
        $db = \Config\Database::connect('default');
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        $transactions = $db->table('cash_transactions')
                           ->select('cash_transactions.*, 
                                     billing_transactions.billing_no,
                                     billing_transactions.total_services,
                                     billing_transactions.total_medicines,
                                     billing_transactions.total_restaurant,
                                     patients.name as patient_name,
                                     patients.no_rm,
                                     users.username as cashier_name')
                           ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id')
                           ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('users', 'users.id = cash_transactions.cashier_id', 'left')
                           ->where('DATE(cash_transactions.created_at)', $date)
                           ->orderBy('cash_transactions.id', 'ASC')
                           ->get()
                           ->getResult();

        $totalCollected = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalBank = 0;

        foreach ($transactions as $t) {
            $amt = (float)$t->amount;
            $totalCollected += $amt;
            $pm = strtolower($t->payment_method);
            if (str_contains($pm, 'tunai') || str_contains($pm, 'cash')) {
                $totalCash += $amt;
            } elseif (str_contains($pm, 'qris')) {
                $totalQris += $amt;
            } else {
                $totalBank += $amt;
            }
        }

        $expenses = $db->table('journal_entries')
                       ->select('journal_entries.*, journal_entry_details.debit as amount, accounts.name as account_name')
                       ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                       ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                       ->where('journal_entries.entry_date', $date)
                       ->where('accounts.type', 'expense')
                       ->where('journal_entry_details.debit >', 0)
                       ->get()
                       ->getResult();

        $totalExpenses = 0;
        foreach ($expenses as $exp) {
            $totalExpenses += (float)$exp->amount;
        }

        $register = $db->table('cash_registers')->where('id', 1)->get()->getRow();

        $data = [
            'title'          => 'Cetak Rekap Kasir ' . date('d-m-Y', strtotime($date)),
            'date'           => $date,
            'transactions'   => $transactions,
            'expenses'       => $expenses,
            'totalCollected' => $totalCollected,
            'totalCash'      => $totalCash,
            'totalQris'      => $totalQris,
            'totalBank'      => $totalBank,
            'totalExpenses'  => $totalExpenses,
            'regBalance'     => $register ? (float)$register->balance : 0.0
        ];

        return view('keuangan/cetak_rekap_harian', $data);
    }

    /**
     * Modul Kas & Bank Operasional (/keuangan/transaksi)
     */
    public function transaksi()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            if ($action === 'add_expense') {
                $db->transStart();

                $amount     = floatval($this->request->getPost('amount'));
                $desc       = trim($this->request->getPost('description') ?: 'Beban Operasional');
                $expenseId  = intval($this->request->getPost('expense_account_id')) ?: 13; // default 6-101
                $sourceAccId = intval($this->request->getPost('source_account_id')) ?: 1;  // default 1-101 (Kas Kasir)

                if ($amount <= 0) {
                    session()->setFlashdata('error', 'Nominal pengeluaran harus lebih besar dari 0.');
                    return redirect()->to(base_url('keuangan/transaksi'));
                }

                // Generate Journal Number
                $today = date('Ymd');
                $lastJournal = $db->table('journal_entries')
                                    ->where('DATE(created_at)', date('Y-m-d'))
                                    ->orderBy('id', 'DESC')
                                    ->limit(1)
                                    ->get()
                                    ->getRow();
                $nextNum = 1;
                if ($lastJournal && preg_match('/JV-\d+-(\d+)/', $lastJournal->journal_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
                $journalNo = 'JV-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                // Insert Journal Header
                $db->table('journal_entries')->insert([
                    'journal_no'    => $journalNo,
                    'entry_date'    => date('Y-m-d'),
                    'source_module' => 'Keuangan Kas Kecil',
                    'reference_id'  => 0,
                    'description'   => "Pengeluaran Beban Operasional: " . $desc,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
                $journalId = $db->insertID();

                // Debit Expense Account
                $db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $expenseId,
                    'debit'      => $amount,
                    'credit'     => 0.00
                ]);
                $expenseAcc = $db->table('accounts')->where('id', $expenseId)->get()->getRow();
                if ($expenseAcc) {
                    $db->table('accounts')->where('id', $expenseId)->update(['balance' => $expenseAcc->balance + $amount]);
                }

                // Credit Asset Account (Kas / Bank)
                $db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $sourceAccId,
                    'debit'      => 0.00,
                    'credit'     => $amount
                ]);
                $cashAcc = $db->table('accounts')->where('id', $sourceAccId)->get()->getRow();
                if ($cashAcc) {
                    $db->table('accounts')->where('id', $sourceAccId)->update(['balance' => $cashAcc->balance - $amount]);
                }

                // If source account is Main Cash (1-101), update Cash Register balance
                if ($sourceAccId === 1) {
                    $register = $db->table('cash_registers')->where('id', 1)->get()->getRow();
                    $regBalance = $register ? (float) $register->balance : 0.00;
                    if ($register) {
                        $db->table('cash_registers')->where('id', 1)->update(['balance' => $regBalance - $amount]);
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    session()->setFlashdata('error', 'Gagal mencatat pengeluaran kas.');
                } else {
                    session()->setFlashdata('success', 'Pengeluaran operasional berhasil dibukukan. No. Jurnal: ' . $journalNo);
                }
                return redirect()->to(base_url('keuangan/transaksi'));
            }
        }

        $registers = $db->table('cash_registers')->get()->getResult();
        
        // Fetch recent cashier receipts
        $transactions = $db->table('cash_transactions')
                           ->select('cash_transactions.*, billing_transactions.billing_no, billing_transactions.visit_id, patients.name as patient_name')
                           ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id')
                           ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->orderBy('cash_transactions.created_at', 'DESC')
                           ->limit(50)
                           ->get()
                           ->getResult();

        // Fetch operational expenses list
        $expenses = $db->table('journal_entries')
                       ->select('journal_entries.*, journal_entry_details.debit as amount, exp_acc.name as expense_name, exp_acc.code as expense_code, src_acc.name as source_name')
                       ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                       ->join('accounts as exp_acc', 'exp_acc.id = journal_entry_details.account_id')
                       ->join('journal_entry_details as j_src', 'j_src.journal_id = journal_entries.id AND j_src.credit > 0', 'left')
                       ->join('accounts as src_acc', 'src_acc.id = j_src.account_id', 'left')
                       ->where('exp_acc.type', 'expense')
                       ->where('journal_entry_details.debit >', 0)
                       ->orderBy('journal_entries.id', 'DESC')
                       ->limit(50)
                       ->get()
                       ->getResult();

        // Fetch internal transfers list
        $transfers = $db->table('internal_cash_transfers')
                        ->select('internal_cash_transfers.*, from_acc.name as from_name, from_acc.code as from_code, to_acc.name as to_name, to_acc.code as to_code, users.username as created_by_name')
                        ->join('accounts as from_acc', 'from_acc.id = internal_cash_transfers.from_account_id')
                        ->join('accounts as to_acc', 'to_acc.id = internal_cash_transfers.to_account_id')
                        ->join('users', 'users.id = internal_cash_transfers.created_by', 'left')
                        ->orderBy('internal_cash_transfers.id', 'DESC')
                        ->limit(30)
                        ->get()
                        ->getResult();

        // Active asset accounts (Kas & Bank)
        $cashBankAccounts = $db->table('accounts')
                               ->where('type', 'asset')
                               ->orderBy('code', 'ASC')
                               ->get()
                               ->getResult();

        // Active expense accounts (Beban)
        $expenseAccounts = $db->table('accounts')
                              ->where('type', 'expense')
                              ->orderBy('code', 'ASC')
                              ->get()
                              ->getResult();

        // Active revenue accounts (Pendapatan Lain)
        $revenueAccounts = $db->table('accounts')
                              ->where('type', 'revenue')
                              ->orderBy('code', 'ASC')
                              ->get()
                              ->getResult();

        $paymentMethods = $db->table('payment_methods')
                             ->where('is_active', 1)
                             ->orderBy('category', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();

        $data = [
            'title'            => 'Kas & Rekening Bank Operasional',
            'active_menu'      => 'keuangan-transaksi',
            'registers'        => $registers,
            'transactions'     => $transactions,
            'expenses'         => $expenses,
            'transfers'        => $transfers,
            'cashBankAccounts' => $cashBankAccounts,
            'expenseAccounts'  => $expenseAccounts,
            'revenueAccounts'  => $revenueAccounts,
            'paymentMethods'   => $paymentMethods
        ];

        return view('keuangan/transaksi', $data);
    }

    /**
     * Transfer Dana Antar Rekening Kas & Bank (Internal Transfer)
     */
    public function transferKas()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $fromAccId = intval($this->request->getPost('from_account_id'));
        $toAccId   = intval($this->request->getPost('to_account_id'));
        $amount    = floatval($this->request->getPost('amount'));
        $desc      = trim($this->request->getPost('description') ?: 'Mutasi Kas Antar Rekening');
        $userId    = session()->get('user_id') ?: 1;

        if ($fromAccId === $toAccId) {
            session()->setFlashdata('error', 'Rekening sumber dan rekening tujuan tidak boleh sama.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        if ($amount <= 0) {
            session()->setFlashdata('error', 'Nominal transfer harus lebih besar dari 0.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        $fromAcc = $db->table('accounts')->where('id', $fromAccId)->get()->getRow();
        $toAcc   = $db->table('accounts')->where('id', $toAccId)->get()->getRow();

        if (!$fromAcc || !$toAcc) {
            session()->setFlashdata('error', 'Rekening akun tidak valid.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        // Generate Transfer Number (TRF-YYYYMMDD-XXXX)
        $today = date('Ymd');
        $lastTrf = $db->table('internal_cash_transfers')
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

        // Record in internal_cash_transfers
        $db->table('internal_cash_transfers')->insert([
            'transfer_no'     => $transferNo,
            'from_account_id' => $fromAccId,
            'to_account_id'   => $toAccId,
            'amount'          => $amount,
            'transfer_date'   => date('Y-m-d'),
            'description'     => $desc,
            'created_by'      => $userId,
            'created_at'      => date('Y-m-d H:i:s')
        ]);

        // Generate Journal Entry
        $lastJournal = $db->table('journal_entries')
                          ->where('DATE(created_at)', date('Y-m-d'))
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->get()
                          ->getRow();
        $nextJvNum = 1;
        if ($lastJournal && preg_match('/JV-\d+-(\d+)/', $lastJournal->journal_no, $matches)) {
            $nextJvNum = intval($matches[1]) + 1;
        }
        $journalNo = 'JV-' . $today . '-' . str_pad($nextJvNum, 4, '0', STR_PAD_LEFT);

        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => 'Mutasi Kas',
            'reference_id'  => 0,
            'description'   => "Mutasi Kas [{$transferNo}]: {$fromAcc->name} ke {$toAcc->name} - {$desc}",
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $db->insertID();

        // Debit Target Account (To Account)
        $db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $toAccId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);
        $db->table('accounts')->where('id', $toAccId)->update(['balance' => $toAcc->balance + $amount]);

        // Credit Source Account (From Account)
        $db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $fromAccId,
            'debit'      => 0.00,
            'credit'     => $amount
        ]);
        $db->table('accounts')->where('id', $fromAccId)->update(['balance' => $fromAcc->balance - $amount]);

        // Update Cash Register if from or to account is Main Cash (id=1)
        if ($fromAccId === 1) {
            $reg = $db->table('cash_registers')->where('id', 1)->get()->getRow();
            if ($reg) $db->table('cash_registers')->where('id', 1)->update(['balance' => (float)$reg->balance - $amount]);
        }
        if ($toAccId === 1) {
            $reg = $db->table('cash_registers')->where('id', 1)->get()->getRow();
            if ($reg) $db->table('cash_registers')->where('id', 1)->update(['balance' => (float)$reg->balance + $amount]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses mutasi transfer kas.');
        } else {
            session()->setFlashdata('success', 'Mutasi transfer kas berhasil diproses! Bukti: ' . $transferNo . ' (Jurnal: ' . $journalNo . ')');
        }

        return redirect()->to(base_url('keuangan/transaksi'));
    }

    /**
     * Catat Pemasukan Kas Non-Pelayanan (Pendapatan Lain-lain)
     */
    public function pemasukanLain()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $targetAccId  = intval($this->request->getPost('target_account_id')) ?: 1; // default Kas Kasir 1-101
        $revenueAccId = intval($this->request->getPost('revenue_account_id')) ?: 4; // default 4-101 or other
        $amount       = floatval($this->request->getPost('amount'));
        $desc         = trim($this->request->getPost('description') ?: 'Pemasukan Kas Non-Pelayanan');

        if ($amount <= 0) {
            session()->setFlashdata('error', 'Nominal pemasukan harus lebih besar dari 0.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        $targetAcc  = $db->table('accounts')->where('id', $targetAccId)->get()->getRow();
        $revenueAcc = $db->table('accounts')->where('id', $revenueAccId)->get()->getRow();

        // Generate Journal Number
        $today = date('Ymd');
        $lastJournal = $db->table('journal_entries')
                          ->where('DATE(created_at)', date('Y-m-d'))
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->get()
                          ->getRow();
        $nextJvNum = 1;
        if ($lastJournal && preg_match('/JV-\d+-(\d+)/', $lastJournal->journal_no, $matches)) {
            $nextJvNum = intval($matches[1]) + 1;
        }
        $journalNo = 'JV-' . $today . '-' . str_pad($nextJvNum, 4, '0', STR_PAD_LEFT);

        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => 'Penerimaan Lain',
            'reference_id'  => 0,
            'description'   => "Pemasukan Kas Lain: " . $desc,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $db->insertID();

        // Debit Kas / Bank Target
        $db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $targetAccId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);
        if ($targetAcc) {
            $db->table('accounts')->where('id', $targetAccId)->update(['balance' => $targetAcc->balance + $amount]);
        }

        // Credit Revenue Account
        $db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $revenueAccId,
            'debit'      => 0.00,
            'credit'     => $amount
        ]);
        if ($revenueAcc) {
            $db->table('accounts')->where('id', $revenueAccId)->update(['balance' => $revenueAcc->balance + $amount]);
        }

        // Update Cash Register if target is Main Cash
        if ($targetAccId === 1) {
            $reg = $db->table('cash_registers')->where('id', 1)->get()->getRow();
            if ($reg) $db->table('cash_registers')->where('id', 1)->update(['balance' => (float)$reg->balance + $amount]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal membukukan pemasukan kas.');
        } else {
            session()->setFlashdata('success', 'Pemasukan kas berhasil dibukukan! No. Jurnal: ' . $journalNo);
        }

        return redirect()->to(base_url('keuangan/transaksi'));
    }

    /**
     * Set / Sesuaikan Saldo Awal Rekening Kas / Bank Secara Cepat
     */
    public function setSaldoAwalKas()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $accountId  = intval($this->request->getPost('account_id'));
        $newBalance = floatval($this->request->getPost('new_balance'));
        $notes      = trim($this->request->getPost('notes') ?: 'Penyesuaian / Input Saldo Awal Kas');
        $userId     = session()->get('user_id') ?: 1;

        if ($accountId <= 0 || $newBalance < 0) {
            session()->setFlashdata('error', 'Rekening kas atau nominal saldo awal tidak valid.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        $account = $db->table('accounts')->where('id', $accountId)->get()->getRow();
        if (!$account) {
            session()->setFlashdata('error', 'Rekening akun tidak ditemukan.');
            return redirect()->to(base_url('keuangan/transaksi'));
        }

        $oldBalance = (float)$account->balance;
        $diff = $newBalance - $oldBalance;

        // Update Account Balance
        $db->table('accounts')->where('id', $accountId)->update(['balance' => $newBalance]);

        // If Kasir Utama (id=1), update cash register balance as well
        if ($accountId === 1) {
            $reg = $db->table('cash_registers')->where('id', 1)->get()->getRow();
            if ($reg) {
                $db->table('cash_registers')->where('id', 1)->update(['balance' => $newBalance]);
            } else {
                $db->table('cash_registers')->insert([
                    'id'      => 1,
                    'name'    => 'Kasir Utama',
                    'balance' => $newBalance,
                    'status'  => 'open'
                ]);
            }
        }

        // Find or create Equity Modal Awal account (3-101)
        $equityAcc = $db->table('accounts')->where('code', '3-101')->orWhere('type', 'equity')->orderBy('code', 'ASC')->get()->getRow();
        if (!$equityAcc) {
            $db->table('accounts')->insert([
                'code'           => '3-101',
                'name'           => 'Modal Awal Disetor',
                'type'           => 'equity',
                'normal_balance' => 'credit',
                'balance'        => 0.00
            ]);
            $equityId = $db->insertID();
        } else {
            $equityId = $equityAcc->id;
        }

        // Generate Journal Entry for Opening Balance Adjustment
        $today = date('Ymd');
        $journalNo = 'JV-SALDOAWAL-' . $today . '-' . str_pad($accountId, 3, '0', STR_PAD_LEFT);

        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => 'Saldo Awal',
            'reference_id'  => $accountId,
            'description'   => "Setting Saldo Awal [{$account->code} - {$account->name}]: Rp " . number_format($newBalance, 0, ',', '.') . " ({$notes})",
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $db->insertID();

        if ($diff > 0) {
            // Debit Kas/Bank, Credit Modal Awal
            $db->table('journal_entry_details')->insert(['journal_id' => $journalId, 'account_id' => $accountId, 'debit' => $diff, 'credit' => 0.00]);
            $db->table('journal_entry_details')->insert(['journal_id' => $journalId, 'account_id' => $equityId, 'debit' => 0.00, 'credit' => $diff]);
            $db->table('accounts')->where('id', $equityId)->update(['balance' => ($equityAcc->balance ?? 0) + $diff]);
        } elseif ($diff < 0) {
            $absDiff = abs($diff);
            // Debit Modal Awal, Credit Kas/Bank
            $db->table('journal_entry_details')->insert(['journal_id' => $journalId, 'account_id' => $equityId, 'debit' => $absDiff, 'credit' => 0.00]);
            $db->table('journal_entry_details')->insert(['journal_id' => $journalId, 'account_id' => $accountId, 'debit' => 0.00, 'credit' => $absDiff]);
            $db->table('accounts')->where('id', $equityId)->update(['balance' => ($equityAcc->balance ?? 0) - $absDiff]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memperbarui saldo awal kas.');
        } else {
            session()->setFlashdata('success', 'Saldo Awal untuk rekening ' . esc($account->name) . ' berhasil diset menjadi Rp ' . number_format($newBalance, 0, ',', '.') . '! Jurnal: ' . $journalNo);
        }

        return redirect()->to(base_url('keuangan/transaksi'));
    }

    // =========================================================================
    // MODUL AGING SCHEDULE (UMUR PIUTANG & HUTANG)
    // =========================================================================
    public function agingReport()
    {
        $db = \Config\Database::connect('default');

        // 1. Piutang Pasien / Asuransi (Billing Transactions Unpaid)
        $piutangList = $db->table('billing_transactions')
                          ->select('billing_transactions.*, 
                                    patients.name as patient_name, 
                                    patients.no_rm, 
                                    billing_transactions.grand_total as total_amount,
                                    COALESCE((SELECT SUM(amount) FROM cash_transactions WHERE billing_id = billing_transactions.id), 0) as paid_amount,
                                    DATEDIFF(NOW(), billing_transactions.created_at) as age_days')
                          ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                          ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                          ->where('billing_transactions.status !=', 'paid')
                          ->where('billing_transactions.status !=', 'cancelled')
                          ->orderBy('billing_transactions.created_at', 'ASC')
                          ->get()->getResult();

        $piutangSummary = ['0_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        foreach ($piutangList as $p) {
            $sisa = $p->total_amount - $p->paid_amount;
            $days = (int)$p->age_days;
            if ($days <= 30) $piutangSummary['0_30'] += $sisa;
            elseif ($days <= 60) $piutangSummary['31_60'] += $sisa;
            elseif ($days <= 90) $piutangSummary['61_90'] += $sisa;
            else $piutangSummary['over_90'] += $sisa;
        }

        // 2. Hutang Supplier Farmasi / PO
        $hutangList = $db->table('purchase_orders')
                         ->select('purchase_orders.*, 
                                   purchase_orders.po_no as po_number,
                                   purchase_orders.total_amount as grand_total,
                                   COALESCE(purchase_orders.order_date, purchase_orders.created_at) as po_date,
                                   suppliers.name as supplier_name, 
                                   suppliers.phone as supplier_phone, 
                                   DATEDIFF(NOW(), COALESCE(purchase_orders.order_date, purchase_orders.created_at)) as age_days')
                         ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id', 'left')
                         ->where('purchase_orders.status !=', 'cancelled')
                         ->orderBy('age_days', 'DESC')
                         ->get()->getResult();

        $hutangSummary = ['0_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
        foreach ($hutangList as $h) {
            $days = (int)$h->age_days;
            $amt = (float)$h->total_amount;
            if ($days <= 30) $hutangSummary['0_30'] += $amt;
            elseif ($days <= 60) $hutangSummary['31_60'] += $amt;
            elseif ($days <= 90) $hutangSummary['61_90'] += $amt;
            else $hutangSummary['over_90'] += $amt;
        }

        $data = [
            'title'          => 'Buku Pembantu & Jadwal Umur Piutang/Hutang (Aging Schedule)',
            'active_menu'    => 'keuangan-aging',
            'piutangList'    => $piutangList,
            'piutangSummary' => $piutangSummary,
            'hutangList'     => $hutangList,
            'hutangSummary'  => $hutangSummary
        ];

        return view('keuangan/aging_report', $data);
    }

    // =========================================================================
    // MODUL SETTLEMENT JASA MEDIS & FEE DOKTER
    // =========================================================================
    public function feeDokter()
    {
        $db = \Config\Database::connect('default');

        // 1. All Doctors with Fee Config
        $allDoctors = $db->table('doctors')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();

        // 2. Calculate Doctor Fees (Poli Medical Fees + Pharmacy Prescription Fees)
        $unpaidDoctors = [];
        foreach ($allDoctors as $doc) {
            // A. Patient Visits in Polyclinic
            $visitRow = $db->table('patient_visits')
                           ->select('COUNT(id) as patient_count')
                           ->where('doctor_id', $doc->id)
                           ->where('status', 'completed')
                           ->get()->getRow();
            $patientCount = (int)($visitRow->patient_count ?? 0);
            $feePerPasien = (float)($doc->fee_per_pasien ?? 0);
            $medicalEarned = $patientCount * $feePerPasien;

            // B. Pharmacy Prescription Sales (5% of Medicine Sales)
            $rxSalesRow = $db->table('pharmacy_sales')
                             ->select('COUNT(id) as rx_count, SUM(total_amount) as total_rx_sales')
                             ->where('doctor_id', $doc->id)
                             ->get()->getRow();
            $rxCount     = (int)($rxSalesRow->rx_count ?? 0);
            $totalRxSales= (float)($rxSalesRow->total_rx_sales ?? 0);
            $pharmacyFee = round($totalRxSales * 0.05, 2);

            // C. Total Already Settled / Paid
            $settleRow = $db->table('doctor_fee_settlements')
                            ->select('SUM(total_amount) as total_paid')
                            ->where('doctor_id', $doc->id)
                            ->where('status', 'paid')
                            ->get()->getRow();
            $totalPaid = (float)($settleRow->total_paid ?? 0);

            // D. Balances
            $grossEarned = $medicalEarned + $pharmacyFee;
            $unpaidBalance = max(0, $grossEarned - $totalPaid);

            // Get poly name
            $poly = $db->table('polyclinics')->where('id', $doc->polyclinic_id)->get()->getRow();
            if (!$poly) {
                $poly = $db->table('polikliniks')->where('id', $doc->polyclinic_id)->get()->getRow();
            }
            $polyName = $poly ? $poly->name : 'Poli Umum';

            $docObj = (object)[
                'doctor_id'      => $doc->id,
                'doctor_name'    => $doc->name,
                'poly_name'      => $polyName,
                'fee_per_pasien' => $feePerPasien,
                'patient_count'  => $patientCount,
                'medical_earned' => $medicalEarned,
                'rx_count'       => $rxCount,
                'total_rx_sales' => $totalRxSales,
                'pharmacy_fee'   => $pharmacyFee,
                'gross_earned'   => $grossEarned,
                'total_paid'     => $totalPaid,
                'unpaid_balance' => $unpaidBalance,
                'total_earned'   => $unpaidBalance > 0 ? $unpaidBalance : $grossEarned
            ];

            $unpaidDoctors[] = $docObj;
        }

        // 3. Paid Settlements History
        $settlements = $db->table('doctor_fee_settlements')
                          ->select('doctor_fee_settlements.*, doctors.name as doctor_name')
                          ->join('doctors', 'doctors.id = doctor_fee_settlements.doctor_id', 'left')
                          ->orderBy('doctor_fee_settlements.id', 'DESC')
                          ->get()->getResult();

        $data = [
            'title'         => 'Rekapitulasi & Settlement Jasa Medis Dokter',
            'active_menu'   => 'keuangan-fee-dokter',
            'allDoctors'    => $allDoctors,
            'unpaidDoctors' => $unpaidDoctors,
            'settlements'   => $settlements
        ];

        return view('keuangan/fee_dokter', $data);
    }

    public function bayarFeeDokter()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $doctorId        = (int)$this->request->getPost('doctor_id');
        $periodStart     = $this->request->getPost('period_start') ?: date('Y-m-01');
        $periodEnd       = $this->request->getPost('period_end') ?: date('Y-m-d');
        $totalActions    = (int)$this->request->getPost('total_actions');
        $totalAmount     = (float)$this->request->getPost('total_amount');
        $medicalPortion  = (float)($this->request->getPost('medical_portion') ?? 0);
        $pharmacyPortion = (float)($this->request->getPost('pharmacy_portion') ?? 0);
        $paymentMethod   = $this->request->getPost('payment_method') ?: 'Transfer Bank';
        $notes           = trim($this->request->getPost('notes') ?: 'Pembayaran Fee Jasa Medis & Resep Dokter');
        $userId          = session()->get('user_id') ?: 1;

        if ($doctorId <= 0 || $totalAmount <= 0) {
            session()->setFlashdata('error', 'Data dokter atau nominal fee tidak valid.');
            return redirect()->to(base_url('keuangan/fee-dokter'));
        }

        $doctor = $db->table('doctors')->where('id', $doctorId)->get()->getRow();
        if (!$doctor) {
            session()->setFlashdata('error', 'Dokter tidak ditemukan.');
            return redirect()->to(base_url('keuangan/fee-dokter'));
        }

        // If portions not explicitly specified, divide logically
        if ($medicalPortion <= 0 && $pharmacyPortion <= 0) {
            $medicalPortion = $totalAmount;
        }

        $today = date('Ymd');
        $settlementNo = 'SETTLE-DOC-' . $today . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Account mapping
        // Account for Medical Fee Expense (6-102 Beban Komisi & Jasa Medis Dokter or 5-103)
        $medicalAcc = $db->table('accounts')->where('code', '6-102')->orWhere('code', '5-103')->get()->getRow();
        $medicalAccId = $medicalAcc ? $medicalAcc->id : 14;

        // Account for Pharmacy Prescription Liability (241 Utang Fee Dokter Resep)
        $pharmacyAcc = $db->table('accounts')->where('code', '241')->get()->getRow();
        $pharmacyAccId = $pharmacyAcc ? $pharmacyAcc->id : 32;

        // Credit: Kas Tunai (1-101) or Bank (1-102)
        $cashAcc = ($paymentMethod === 'Kas Tunai')
            ? $db->table('accounts')->where('code', '1-101')->orWhere('code', '1111')->get()->getRow()
            : $db->table('accounts')->where('code', '1-102')->orWhere('code', '112')->get()->getRow();
        $cashAccId = $cashAcc ? $cashAcc->id : 1;

        // Create Journal Entry
        $journalNo = 'JV-FEE-' . $today . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => 'Jasa Medis & Resep',
            'reference_id'  => $doctorId,
            'description'   => "Pelunasan Fee Dokter [{$doctor->name}] Periode {$periodStart} s/d {$periodEnd}: Total Rp " . number_format($totalAmount, 0, ',', '.') . " [Poli: Rp " . number_format($medicalPortion, 0, ',', '.') . ", Resep 241: Rp " . number_format($pharmacyPortion, 0, ',', '.') . "] ({$notes})",
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $db->insertID();

        // Double-entry Details
        if ($medicalPortion > 0) {
            $db->table('journal_entry_details')->insert([
                'journal_id' => $journalId, 
                'account_id' => $medicalAccId, 
                'debit'      => $medicalPortion, 
                'credit'     => 0.00
            ]);
            $db->query("UPDATE accounts SET balance = balance + {$medicalPortion} WHERE id = {$medicalAccId}");
        }

        if ($pharmacyPortion > 0) {
            $db->table('journal_entry_details')->insert([
                'journal_id' => $journalId, 
                'account_id' => $pharmacyAccId, 
                'debit'      => $pharmacyPortion, 
                'credit'     => 0.00
            ]);
            $db->query("UPDATE accounts SET balance = balance - {$pharmacyPortion} WHERE id = {$pharmacyAccId}");
        }

        // Credit to Cash / Bank
        $db->table('journal_entry_details')->insert([
            'journal_id' => $journalId, 
            'account_id' => $cashAccId, 
            'debit'      => 0.00, 
            'credit'     => $totalAmount
        ]);
        $db->query("UPDATE accounts SET balance = balance - {$totalAmount} WHERE id = {$cashAccId}");

        // Insert Settlement Record
        $db->table('doctor_fee_settlements')->insert([
            'settlement_no'  => $settlementNo,
            'doctor_id'      => $doctorId,
            'period_start'   => $periodStart,
            'period_end'     => $periodEnd,
            'total_actions'  => $totalActions,
            'total_amount'   => $totalAmount,
            'payment_method' => $paymentMethod,
            'status'         => 'paid',
            'paid_at'        => date('Y-m-d H:i:s'),
            'notes'          => $notes . ($pharmacyPortion > 0 ? " (Termasuk Fee Resep Apotek Rp " . number_format($pharmacyPortion, 0, ',', '.') . ")" : ""),
            'journal_id'     => $journalId,
            'created_by'     => $userId,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses pembayaran fee dokter.');
        } else {
            session()->setFlashdata('success', "Pembayaran Fee Dokter {$doctor->name} sebesar Rp " . number_format($totalAmount, 0, ',', '.') . " berhasil diselesaikan! No Settlement: {$settlementNo}, Jurnal: {$journalNo}");
        }

        return redirect()->to(base_url('keuangan/fee-dokter'));
    }

    /**
     * Ekspor Laporan Komprehensif Multi-Sheet Excel (Keuangan + Pasien + Farmasi + Resto)
     */
    public function eksporExcelMultisheet()
    {
        $db = \Config\Database::connect('default');

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        // 1. Data Sheet 1: Transaksi Keuangan & Kasir
        $financeData = $db->table('cash_transactions')
                          ->select('cash_transactions.*, 
                                    billing_transactions.billing_no,
                                    billing_transactions.total_services,
                                    billing_transactions.total_medicines,
                                    billing_transactions.total_restaurant,
                                    billing_transactions.discount,
                                    patients.name as patient_name,
                                    patients.no_rm,
                                    users.username as cashier_name')
                          ->join('billing_transactions', 'billing_transactions.id = cash_transactions.billing_id', 'left')
                          ->join('patient_visits', 'patient_visits.id = billing_transactions.visit_id', 'left')
                          ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                          ->join('users', 'users.id = cash_transactions.cashier_id', 'left')
                          ->where('DATE(cash_transactions.created_at) >=', $startDate)
                          ->where('DATE(cash_transactions.created_at) <=', $endDate)
                          ->orderBy('cash_transactions.id', 'ASC')
                          ->get()
                          ->getResult();

        // 2. Data Sheet 2: Pelayanan Pasien & Rekam Medis
        $patientData = $db->table('patient_visits')
                          ->select('patient_visits.*, 
                                    patients.name as patient_name, 
                                    patients.no_rm, 
                                    patients.gender, 
                                    patients.phone,
                                    COALESCE(polikliniks.name, polyclinics.name) as poly_name,
                                    doctors.name as doctor_name,
                                    medical_records.assessment,
                                    medical_records.icd10_code,
                                    triage_records.blood_pressure,
                                    triage_records.weight,
                                    triage_records.height')
                          ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                          ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                          ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                          ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                          ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                          ->join('triage_records', 'triage_records.visit_id = patient_visits.id', 'left')
                          ->where('patient_visits.visit_date >=', $startDate)
                          ->where('patient_visits.visit_date <=', $endDate)
                          ->orderBy('patient_visits.id', 'ASC')
                          ->get()
                          ->getResult();

        // 3. Data Sheet 3: Farmasi & Penjualan Obat
        $pharmacyPresc = $db->table('prescription_details')
                            ->select('prescription_details.*, 
                                      medicines.name as medicine_name, 
                                      medicines.unit,
                                      medicines.price as med_price,
                                      prescriptions.created_at as presc_date,
                                      patients.name as patient_name,
                                      patients.no_rm,
                                      doctors.name as doctor_name')
                            ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id', 'left')
                            ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                            ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                            ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                            ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                            ->where('DATE(prescriptions.created_at) >=', $startDate)
                            ->where('DATE(prescriptions.created_at) <=', $endDate)
                            ->orderBy('prescriptions.id', 'ASC')
                            ->get()
                            ->getResult();

        // 4. Data Sheet 4: Restoran Gizi & Makanan Pasien
        $restoData = $db->table('restaurant_orders')
                        ->select('restaurant_orders.*, restaurant_tables.table_no')
                        ->join('restaurant_tables', 'restaurant_tables.id = restaurant_orders.table_id', 'left')
                        ->where('DATE(restaurant_orders.created_at) >=', $startDate)
                        ->where('DATE(restaurant_orders.created_at) <=', $endDate)
                        ->orderBy('restaurant_orders.id', 'ASC')
                        ->get()
                        ->getResult();

        // Generate SpreadsheetML (Native Excel Multi-Sheet XML)
        $filename = "Laporan_Komprehensif_Sawamawa_{$startDate}_sd_{$endDate}.xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Sawamawa Medical Center</Author>
  <Created><?= date('Y-m-d\TH:i:s\Z') ?></Created>
  <Company>Sawamawa Medical Center</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="TitleHeader">
   <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#0d9488"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="SubTitle">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Italic="1" ss:Color="#64748b"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="TableHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94a3b8"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94a3b8"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94a3b8"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94a3b8"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#ffffff"/>
   <Interior ss:Color="#0d9488" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="TableCell">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
  </Style>
  <Style ss:ID="TableCurrency">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0"/>
  </Style>
  <Style ss:ID="TableTotal">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#0f766e"/>
   <Interior ss:Color="#ccfbf1" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0"/>
  </Style>
 </Styles>

 <!-- SHEET 1: KEUANGAN & KASIR -->
 <Worksheet ss:Name="Keuangan &amp; Kasir">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="120"/>
   <Column ss:Width="100"/>
   <Column ss:Width="140"/>
   <Column ss:Width="80"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="80"/>
   <Column ss:Width="120"/>
   <Column ss:Width="90"/>
   <Column ss:Width="100"/>
   
   <Row ss:Height="25">
    <Cell ss:MergeAcross="11" ss:StyleID="TitleHeader"><Data ss:Type="String">LAPORAN PENERIMAAN KASIR &amp; KEUANGAN TERPADU</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="11" ss:StyleID="SubTitle"><Data ss:Type="String">Periode: <?= $startDate ?> s/d <?= $endDate ?> | Dicetak: <?= date('d/m/Y H:i:s') ?> WITA</Data></Cell>
   </Row>
   <Row ss:Height="5"></Row>

   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No Kwitansi</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No Tagihan</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No RM</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Jasa Medis (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Farmasi (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Resto Gizi (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Diskon (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Total Bayar (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Metode</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Kasir</Data></Cell>
   </Row>

   <?php 
   $no = 1; 
   $totSvc = 0; $totMed = 0; $totResto = 0; $totDisc = 0; $totGrand = 0;
   foreach ($financeData as $f): 
       $totSvc += (float)$f->total_services;
       $totMed += (float)$f->total_medicines;
       $totResto += (float)$f->total_restaurant;
       $totDisc += (float)$f->discount;
       $totGrand += (float)$f->amount;
   ?>
   <Row>
    <Cell ss:StyleID="TableCell"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($f->receipt_no ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($f->billing_no ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($f->patient_name ?? 'Pasien Umum') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($f->no_rm ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$f->total_services ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$f->total_medicines ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$f->total_restaurant ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$f->discount ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$f->amount ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars(strtoupper($f->payment_method ?? 'TUNAI')) ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($f->cashier_name ?? 'Kasir') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>

   <Row ss:Height="22">
    <Cell ss:MergeAcross="4" ss:StyleID="TableTotal"><Data ss:Type="String">TOTAL KESELURUHAN:</Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totSvc ?></Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totMed ?></Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totResto ?></Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totDisc ?></Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totGrand ?></Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TableTotal"><Data ss:Type="String"></Data></Cell>
   </Row>
  </Table>
 </Worksheet>

 <!-- SHEET 2: PELAYANAN PASIEN & REKAM MEDIS -->
 <Worksheet ss:Name="Pelayanan Pasien">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="110"/>
   <Column ss:Width="80"/>
   <Column ss:Width="140"/>
   <Column ss:Width="70"/>
   <Column ss:Width="50"/>
   <Column ss:Width="110"/>
   <Column ss:Width="130"/>
   <Column ss:Width="90"/>
   <Column ss:Width="180"/>
   <Column ss:Width="80"/>
   <Column ss:Width="80"/>

   <Row ss:Height="25">
    <Cell ss:MergeAcross="11" ss:StyleID="TitleHeader"><Data ss:Type="String">LAPORAN KUNJUNGAN PASIEN &amp; REKAM MEDIS</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="11" ss:StyleID="SubTitle"><Data ss:Type="String">Periode: <?= $startDate ?> s/d <?= $endDate ?></Data></Cell>
   </Row>
   <Row ss:Height="5"></Row>

   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No Kunjungan</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Tanggal</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No RM</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">L/P</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Poliklinik</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Dokter DPJP</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Penjamin</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Diagnosa / Assessment</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">ICD-10</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Tekanan Darah</Data></Cell>
   </Row>

   <?php $pNo = 1; foreach ($patientData as $p): ?>
   <Row>
    <Cell ss:StyleID="TableCell"><Data ss:Type="Number"><?= $pNo++ ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->no_visit ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->visit_date ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->patient_name ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->no_rm ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->gender ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->poly_name ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->doctor_name ?? 'Dokter Jaga') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars(strtoupper($p->payment_method ?? 'UMUM')) ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->assessment ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->icd10_code ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($p->blood_pressure ?? '-') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>

 <!-- SHEET 3: FARMASI & E-RESEP -->
 <Worksheet ss:Name="Farmasi &amp; Resep">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="160"/>
   <Column ss:Width="140"/>
   <Column ss:Width="70"/>
   <Column ss:Width="50"/>
   <Column ss:Width="60"/>
   <Column ss:Width="100"/>
   <Column ss:Width="110"/>
   <Column ss:Width="120"/>
   <Column ss:Width="130"/>

   <Row ss:Height="25">
    <Cell ss:MergeAcross="9" ss:StyleID="TitleHeader"><Data ss:Type="String">LAPORAN PENYIAPAN E-RESEP &amp; OBAT FARMASI</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="9" ss:StyleID="SubTitle"><Data ss:Type="String">Periode: <?= $startDate ?> s/d <?= $endDate ?></Data></Cell>
   </Row>
   <Row ss:Height="5"></Row>

   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Nama Obat</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No RM</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Qty</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Satuan</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Harga (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Subtotal (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Aturan Pakai</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Dokter Penulis</Data></Cell>
   </Row>

   <?php 
   $rxNo = 1; $totRx = 0;
   foreach ($pharmacyPresc as $rx): 
       $subRx = (float)$rx->qty * (float)$rx->price;
       $totRx += $subRx;
   ?>
   <Row>
    <Cell ss:StyleID="TableCell"><Data ss:Type="Number"><?= $rxNo++ ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->medicine_name ?? 'Obat') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->patient_name ?? 'Pasien') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->no_rm ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="Number"><?= (int)$rx->qty ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->unit ?? 'Item') ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$rx->price ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= $subRx ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->dosage ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($rx->doctor_name ?? 'Dokter') ?></Data></Cell>
   </Row>
   <?php endforeach; ?>

   <Row ss:Height="22">
    <Cell ss:MergeAcross="6" ss:StyleID="TableTotal"><Data ss:Type="String">TOTAL PENJUALAN RESEP OBAT:</Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totRx ?></Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TableTotal"><Data ss:Type="String"></Data></Cell>
   </Row>
  </Table>
 </Worksheet>

 <!-- SHEET 4: RESTO GIZI & DIET SEHAT -->
 <Worksheet ss:Name="Resto Gizi">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="110"/>
   <Column ss:Width="90"/>
   <Column ss:Width="140"/>
   <Column ss:Width="80"/>
   <Column ss:Width="150"/>
   <Column ss:Width="110"/>
   <Column ss:Width="90"/>
   <Column ss:Width="90"/>

   <Row ss:Height="25">
    <Cell ss:MergeAcross="8" ss:StyleID="TitleHeader"><Data ss:Type="String">LAPORAN RESTORAN GIZI &amp; NUTRISI SEHAT</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="8" ss:StyleID="SubTitle"><Data ss:Type="String">Periode: <?= $startDate ?> s/d <?= $endDate ?></Data></Cell>
   </Row>
   <Row ss:Height="5"></Row>

   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No Order</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Tanggal</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Nama Pelanggan / Pasien</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Meja / Tipe</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Instruksi Diet Gizi</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Total Bayar (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Metode</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Status</Data></Cell>
   </Row>

   <?php 
   $rNo = 1; $totRestoSum = 0;
   foreach ($restoData as $r): 
       $totRestoSum += (float)$r->grand_total;
   ?>
   <Row>
    <Cell ss:StyleID="TableCell"><Data ss:Type="Number"><?= $rNo++ ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($r->order_no ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($r->customer_name ?? 'Umum') ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($r->table_no ? 'Meja ' . $r->table_no : ucfirst($r->order_type ?? 'Dine-In')) ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars($r->diet_instructions ?? '-') ?></Data></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)$r->grand_total ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars(strtoupper($r->payment_method ?? 'TUNAI')) ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= htmlspecialchars(strtoupper($r->status ?? 'CLOSED')) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>

   <Row ss:Height="22">
    <Cell ss:MergeAcross="5" ss:StyleID="TableTotal"><Data ss:Type="String">TOTAL OMSET RESTORAN GIZI:</Data></Cell>
    <Cell ss:StyleID="TableTotal"><Data ss:Type="Number"><?= $totRestoSum ?></Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="TableTotal"><Data ss:Type="String"></Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
        <?php
        exit();
    }
}
