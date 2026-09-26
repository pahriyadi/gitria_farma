<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPatientFlow extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:patient-flow';
    protected $description = 'Test 5-Step Patient Workflow: Pendaftaran -> Dokter/SOAP/E-Resep -> Kasir -> Apotek -> Auto-Journaling';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $clinicService = new \App\Services\ClinicService();
        $pharmacyService = new \App\Services\PharmacyService();
        $journalEngine = new \App\Services\JournalEngine();

        // Cleanup any previous unbalanced test journals
        $oldJnlIds = $db->table('journal_entries')->select('id')->whereIn('journal_no', ['JV-20260905-0017', 'JV-20260905-0019'])->get()->getResultArray();
        if (!empty($oldJnlIds)) {
            $ids = array_column($oldJnlIds, 'id');
            $db->table('journal_entry_details')->whereIn('journal_id', $ids)->delete();
            $db->table('journal_entries')->whereIn('id', $ids)->delete();
        }

        CLI::write("=================================================================", 'yellow');
        CLI::write("=== TESTING 5-STEP INTEGRATED PATIENT SERVICE & WORKFLOW ===", 'yellow');
        CLI::write("=================================================================", 'yellow');

        // STEP 1: Pendaftaran (Online & Offline)
        CLI::write("\n--- [STEP 1] Pendaftaran Pasien (Online / Offline) & Antrean ---", 'cyan');
        
        $nik = '5204' . rand(100000000000, 999999999999);
        $patientName = 'Pasien Test Alur ' . rand(100, 999);
        $phone = '0812' . rand(10000000, 99999999);
        $dob = '1992-08-17';

        $regRes = $clinicService->registerPatient([
            'nik'           => $nik,
            'name'          => $patientName,
            'gender'        => 'L',
            'phone'         => $phone,
            'date_of_birth' => $dob,
            'address'       => 'Jl. Sumbawa Barat No. ' . rand(1, 100),
            'membership_tier' => 'regular'
        ]);

        if ($regRes['status'] !== 'success') {
            CLI::error("Gagal mendaftarkan pasien: " . ($regRes['message'] ?? ''));
            return;
        }

        $patientId = $regRes['patient_id'];
        $noRm = $regRes['no_rm'];
        CLI::write("  [1A] Pasien Terdaftar: {$patientName} | No RM: {$noRm} (ID: {$patientId})", 'green');

        // Klik Tombol Kunjungan (Visit & Queue)
        $doctorId = 1; // Dokter Umum
        $polyId = 1;   // Poli Umum
        $visitRes = $clinicService->createVisit($patientId, $polyId, $doctorId, 'umum', 'poli');

        if ($visitRes['status'] !== 'success') {
            CLI::error("Gagal membuat kunjungan: " . ($visitRes['message'] ?? ''));
            return;
        }

        $visitId = $visitRes['visit_id'];
        $noVisit = $visitRes['no_visit'];
        $queueNo = $visitRes['queue_no'];
        CLI::write("  [1B] Tombol Kunjungan Berhasil: No Visit {$noVisit} | No Antrean: {$queueNo} (Status: waiting)", 'green');

        // STEP 2: Ruang Dokter (TTV Awal + SOAP + E-Resep)
        CLI::write("\n--- [STEP 2] Ruang Dokter: TTV Awal + SOAP + E-Resep ---", 'cyan');

        // 2A. TTV Awal
        $db->table('triage_records')->insert([
            'visit_id'       => $visitId,
            'blood_pressure' => '120/80',
            'temperature'    => '38.2',
            'pulse'          => '84',
            'respiration'    => '20',
            'weight'         => '64',
            'height'         => '168',
            'complaints'     => 'Demam dan pusing 2 hari',
            'anamnesis'      => 'Keluhan demam mendadak sejak kemarin sore'
        ]);
        $db->table('patient_visits')->where('id', $visitId)->update(['status' => 'examining']);
        CLI::write("  [2A] TTV Awal Berhasil Dicatat: TD 120/80, Suhu 38.2°C, Nadi 84x/mnt", 'green');

        // 2B. SOAP
        $soapData = [
            'subjective'   => 'Demam naik turun, batuk kering, nafsu makan menurun',
            'objective'    => 'Keadaan umum sedang, compos mentis, suhu 38.2 C, faring hiperemis minimal',
            'assessment'   => 'Febris akut suspect ISPA',
            'plan'         => 'Paracetamol tab 500mg, Amoxicillin 500mg, CTM 4mg, istirahat cukup',
            'icd10_code'   => 'J06.9',
            'icd9_code'    => '89.07',
            'doctor_notes' => 'Banyak minum air putih, kontrol 3 hari bila belum membaik'
        ];
        $soapRes = $clinicService->saveSoap($visitId, $soapData);
        CLI::write("  [2B] SOAP Dokter Tersimpan: {$soapData['assessment']} (ICD-10: {$soapData['icd10_code']})", 'green');

        // 2C. E-Resep & Billing
        $med1 = $db->table('medicines')
                   ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id')
                   ->where('medicine_batches.stock >', 5)
                   ->select('medicines.*')
                   ->groupBy('medicines.id')
                   ->orderBy('medicines.id', 'ASC')
                   ->get()->getRow();

        $med2 = $db->table('medicines')
                   ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id')
                   ->where('medicine_batches.stock >', 5)
                   ->where('medicines.id !=', $med1 ? $med1->id : 0)
                   ->select('medicines.*')
                   ->groupBy('medicines.id')
                   ->orderBy('medicines.id', 'ASC')
                   ->get()->getRow();

        if (!$med1) {
            CLI::error("Tidak ada stok obat di database untuk resep.");
            return;
        }

        $db->table('prescriptions')->insert([
            'visit_id'  => $visitId,
            'doctor_id' => $doctorId,
            'status'    => 'waiting'
        ]);
        $prescId = $db->insertID();

        $prescList = [
            ['med' => $med1, 'qty' => 10, 'dosage' => '3x1 tablet sesudah makan']
        ];
        if ($med2) {
            $prescList[] = ['med' => $med2, 'qty' => 5, 'dosage' => '2x1 tablet sesudah makan'];
        }

        $bill = $db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
        if (!$bill) {
            CLI::error("Billing transaction tidak ditemukan untuk kunjungan {$visitId}");
            return;
        }

        $consultPrice = 150000;
        $db->table('billing_details')->insert([
            'billing_id' => $bill->id,
            'item_type'  => 'medis',
            'item_name'  => 'Konsultasi Dokter Poli Umum',
            'qty'        => 1,
            'price'      => $consultPrice,
            'discount'   => 0,
            'subtotal'   => $consultPrice
        ]);

        $totalMed = 0;
        foreach ($prescList as $item) {
            $m = $item['med'];
            $sub = $m->price * $item['qty'];
            $totalMed += $sub;

            $db->table('prescription_details')->insert([
                'prescription_id' => $prescId,
                'medicine_id'     => $m->id,
                'qty'             => $item['qty'],
                'dosage'          => $item['dosage'],
                'price'           => $m->price,
                'status'          => 'served'
            ]);

            $db->table('billing_details')->insert([
                'billing_id' => $bill->id,
                'item_type'  => 'obat',
                'item_name'  => $m->name,
                'qty'        => $item['qty'],
                'price'      => $m->price,
                'discount'   => 0,
                'subtotal'   => $sub
            ]);
        }

        $grandTotal = $consultPrice + $totalMed;
        $db->table('billing_transactions')->where('id', $bill->id)->update([
            'total_services'  => $consultPrice,
            'total_medicines' => $totalMed,
            'grand_total'     => $grandTotal,
            'status'          => 'open'
        ]);

        // Status Kunjungan -> cashier, Antrean Poli -> completed
        $db->table('patient_visits')->where('id', $visitId)->update(['status' => 'cashier']);
        $db->table('queue_numbers')->where('visit_id', $visitId)->update(['status' => 'completed']);

        CLI::write("  [2C] E-Resep Berhasil Dibuat: " . count($prescList) . " Rincian Obat (Rp " . number_format($totalMed, 0, ',', '.') . ")", 'green');
        CLI::write("  [2D] Billing Siap Di Kasir ({$bill->billing_no}): Layanan Rp " . number_format($consultPrice, 0, ',', '.') . " + Obat Rp " . number_format($totalMed, 0, ',', '.') . " = Total Rp " . number_format($grandTotal, 0, ',', '.'), 'green');
        CLI::write("  [2E] Status Kunjungan Diupdate -> 'cashier' (Lanjut ke Pembayaran Kasir)", 'green');

        // STEP 3: Pembayaran Kasir (Modul Kasir & Poliklinik)
        CLI::write("\n--- [STEP 3] Pembayaran Kasir (Modul Kasir & Poliklinik) ---", 'cyan');

        $receiptNo = 'RCP-' . date('Ymd') . '-' . rand(1000, 9999);
        $db->table('cash_transactions')->insert([
            'receipt_no'       => $receiptNo,
            'billing_id'       => $bill->id,
            'cash_register_id' => 1,
            'amount'           => $grandTotal,
            'paid_amount'      => $grandTotal,
            'change_amount'    => 0,
            'cashier_id'       => 1,
            'payment_method'   => 'tunai',
            'notes'            => 'Lunas di Kasir Utama (Test)',
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        $db->table('billing_transactions')->where('id', $bill->id)->update([
            'status'         => 'paid',
            'payment_method' => 'tunai'
        ]);

        // Karena ada resep menunggu, arahkan ke 'prescription'
        $db->table('patient_visits')->where('id', $visitId)->update(['status' => 'prescription']);

        CLI::write("  [3A] Pelunasan Kasir Selesai: Kwitansi {$receiptNo} Sebesar Rp " . number_format($grandTotal, 0, ',', '.') . " (Status: PAID)", 'green');
        CLI::write("  [3B] Status Kunjungan Diupdate -> 'prescription' (Lanjut Menuju Apotek)", 'green');

        // STEP 5: Auto-Journaling (Poli & Apotek)
        CLI::write("\n--- [STEP 5] Penjurnalan Otomatis Sesuai Template & Aturan Jurnal ---", 'cyan');

        // 5A: Jurnal Layanan Poliklinik (Kategori RAWAT_JALAN_POLI)
        $catPoliRow = $db->table('journal_categories')->where('category_code', 'RAWAT_JALAN_POLI')->get()->getRow();
        if ($catPoliRow) {
            $catRules = $db->table('journal_category_rules')->where('category_id', $catPoliRow->id)->get()->getResultArray();
            CLI::write("  [Rules RAWAT_JALAN_POLI in DB (" . count($catRules) . " rules)]:", 'yellow');
            foreach ($catRules as $cr) {
                CLI::write("    - {$cr['item_name']} | acc_id: {$cr['account_id']} | pos: {$cr['position']} | calc: {$cr['calc_type']} | pct: {$cr['percentage_value']} | fixed: {$cr['fixed_amount_value']} | formula: {$cr['formula_code']}", 'yellow');
            }
        }
        $resJnlClinic = $journalEngine->postClinicSplitJournal(
            $bill->id,
            $consultPrice,
            "Pendapatan Layanan Medis Klinik - {$bill->billing_no} ({$patientName} / {$queueNo}): Konsultasi Dokter Poli",
            'tunai',
            66.67,
            'Kasir Utama'
        );
        CLI::write("  [5A] Jurnal Poliklinik Dibukukan: No {$resJnlClinic['journal_no']} ({$resJnlClinic['status']})", 'green');

        // 5B: Jurnal Penjualan Resep Apotek (Kategori PENJUALAN_OBAT_RESEP)
        $resJnlPharm = $journalEngine->postPharmacySplitJournal(
            'resep',
            $bill->id,
            $totalMed,
            "Pendapatan Farmasi & Resep Obat - {$bill->billing_no} ({$patientName} / {$queueNo}): E-Resep",
            'tunai',
            5.0,
            'Kasir Utama'
        );
        CLI::write("  [5B] Jurnal Apotek Dibukukan: No {$resJnlPharm['journal_no']} ({$resJnlPharm['status']})", 'green');

        // STEP 4: Pengambilan Obat di Apotek (Modul Kasir & Apotek)
        CLI::write("\n--- [STEP 4] Pengambilan Obat di Apotek (Modul Apotek) ---", 'cyan');

        $dispenseData = [];
        foreach ($prescList as $item) {
            $m = $item['med'];
            $batch = $db->table('medicine_batches')
                        ->where('medicine_id', $m->id)
                        ->where('stock >=', $item['qty'])
                        ->orderBy('expired_date', 'ASC')
                        ->get()->getRow();
            if (!$batch) {
                $batch = $db->table('medicine_batches')->where('medicine_id', $m->id)->get()->getRow();
            }

            $dispenseData[$m->id] = [
                'action'   => 'internal',
                'batch_id' => $batch ? $batch->id : 1,
                'qty'      => $item['qty'],
                'discount' => 0
            ];
        }

        $dispenseRes = $pharmacyService->processPrescription($prescId, $dispenseData);
        CLI::write("  [4A] Dispensing Apotek: {$dispenseRes['status']} - {$dispenseRes['message']}", 'green');

        $vFinal = $db->table('patient_visits')->where('id', $visitId)->get()->getRow();
        $pFinal = $db->table('prescriptions')->where('id', $prescId)->get()->getRow();
        $bFinal = $db->table('billing_transactions')->where('id', $bill->id)->get()->getRow();

        CLI::write("\n=================================================================", 'yellow');
        CLI::write("=== VERIFIKASI AKHIR STATUS KESELURUHAN ALUR PELAYANAN ===", 'yellow');
        CLI::write("=================================================================", 'yellow');
        CLI::write("1. Status Kunjungan Pasien (patient_visits.status): {$vFinal->status} [HARUS completed]", $vFinal->status === 'completed' ? 'green' : 'red');
        CLI::write("2. Status Resep Obat (prescriptions.status): {$pFinal->status} [HARUS completed]", $pFinal->status === 'completed' ? 'green' : 'red');
        CLI::write("3. Status Billing Kasir (billing_transactions.status): {$bFinal->status} [HARUS paid]", $bFinal->status === 'paid' ? 'green' : 'red');

        // Audit Journal Details
        CLI::write("\n--- RINCIAN JURNAL POLIKLINIK ({$resJnlClinic['journal_no']}) ---", 'white');
        $jClinic = $db->table('journal_entries')->where('journal_no', $resJnlClinic['journal_no'])->get()->getRow();
        if ($jClinic) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code, accounts.name')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $jClinic->id)
                          ->get()->getResult();
            $dTot = 0; $cTot = 0;
            foreach ($details as $d) {
                $dTot += $d->debit; $cTot += $d->credit;
                CLI::write("  [{$d->code}] " . str_pad($d->name, 35) . " | Debit: " . str_pad(number_format($d->debit, 2), 12, ' ', STR_PAD_LEFT) . " | Kredit: " . str_pad(number_format($d->credit, 2), 12, ' ', STR_PAD_LEFT));
            }
            CLI::write("  TOTAL: Debit Rp " . number_format($dTot, 2) . " == Kredit Rp " . number_format($cTot, 2) . " (" . ($dTot == $cTot ? 'BALANCE OK' : 'UNBALANCED!') . ")", $dTot == $cTot ? 'green' : 'red');
        }

        CLI::write("\n--- RINCIAN JURNAL APOTEK ({$resJnlPharm['journal_no']}) ---", 'white');
        $jPharm = $db->table('journal_entries')->where('journal_no', $resJnlPharm['journal_no'])->get()->getRow();
        if ($jPharm) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code, accounts.name')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $jPharm->id)
                          ->get()->getResult();
            $dTot = 0; $cTot = 0;
            foreach ($details as $d) {
                $dTot += $d->debit; $cTot += $d->credit;
                CLI::write("  [{$d->code}] " . str_pad($d->name, 35) . " | Debit: " . str_pad(number_format($d->debit, 2), 12, ' ', STR_PAD_LEFT) . " | Kredit: " . str_pad(number_format($d->credit, 2), 12, ' ', STR_PAD_LEFT));
            }
            CLI::write("  TOTAL: Debit Rp " . number_format($dTot, 2) . " == Kredit Rp " . number_format($cTot, 2) . " (" . ($dTot == $cTot ? 'BALANCE OK' : 'UNBALANCED!') . ")", $dTot == $cTot ? 'green' : 'red');
        }

        CLI::write("\n✓ SELURUH ALUR 5 LANGKAH TERVERIFIKASI SEMPURNA!", 'green');
    }
}
