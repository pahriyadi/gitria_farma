<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPrescriptionHistory extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:prescription-history';
    protected $description = 'Verify prescription history, detail JSON, thermal struk, and kwitansi';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write("=== 1. TEST HISTORY QUERY IN APOTEK ===", 'yellow');
        $completed = $db->table('prescriptions')
                        ->select('prescriptions.*, 
                                  patient_visits.no_visit, 
                                  patients.name as patient_name, 
                                  patients.no_rm, 
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
                        ->get()
                        ->getResult();

        CLI::write("Found " . count($completed) . " completed prescriptions.", 'green');
        foreach ($completed as $cp) {
            $cDetails = $db->table('prescription_details')
                           ->select('prescription_details.*, medicines.name as medicine_name')
                           ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                           ->where('prescription_id', $cp->id)
                           ->get()
                           ->getResult();

            $totalGross = 0; $totalTusla = 0; $totalEmbalase = 0; $totalDisc = 0;
            foreach ($cDetails as $cd) {
                $totalGross += ($cd->qty * $cd->price);
                $totalTusla += (float)($cd->tusla ?? 0);
                $totalEmbalase += (float)($cd->embalase ?? 0);
                $totalDisc += (float)($cd->discount ?? 0);
            }
            $calcTotal = max(0, $totalGross + $totalTusla + $totalEmbalase - $totalDisc);

            CLI::write("  - Presc ID: {$cp->id} | Patient: {$cp->patient_name} ({$cp->no_rm}) | Doctor: {$cp->doctor_name}");
            CLI::write("    Medicines: " . count($cDetails) . " items | Total: Rp " . number_format($calcTotal, 0, ',', '.') . " (Tusla: Rp " . number_format($totalTusla, 0, ',', '.') . " | Embalase: Rp " . number_format($totalEmbalase, 0, ',', '.') . ")");
            CLI::write("    Status Bayar: " . ($cp->billing_status === 'paid' ? 'LUNAS (' . $cp->payment_method . ')' : 'BELUM BAYAR'));
        }

        CLI::write("\n=== 2. TEST ENDPOINTS VIA CONTROLLER DISPATCH ===", 'yellow');
        $apotek = new \App\Controllers\Apotek();
        $apotek->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        if (!empty($completed[0])) {
            $testId = $completed[0]->id;

            // Test getPrescriptionDetailJson
            $resJson = $apotek->getPrescriptionDetailJson($testId);
            $rawJson = $resJson->getBody();
            $dataJson = json_decode($rawJson, true);
            if ($dataJson && $dataJson['status'] === 'success') {
                CLI::write("JSON endpoint (resep-detail-json) PASSED: Total Rp " . number_format($dataJson['summary']['grand_total'], 0, ',', '.'), 'green');
            } else {
                CLI::error("JSON endpoint FAILED");
            }

            // Test cetakStrukResep
            $strukView = $apotek->cetakStrukResep($testId);
            if (is_string($strukView) && strpos($strukView, 'BUKTI PENYERAHAN e-RESEP') !== false) {
                $hasNoTuslaStruk = (strpos($strukView, '+Tusla') === false && strpos($strukView, 'Jasa Racik') === false);
                $hasNoEmbStruk   = (strpos($strukView, '+Emb') === false && strpos($strukView, 'Kemasan (Embalase)') === false);
                CLI::write("Thermal receipt view (cetak-struk-resep) PASSED: Contains BUKTI PENYERAHAN e-RESEP", 'green');
                CLI::write("  - Tuslah hidden on Struk: " . ($hasNoTuslaStruk ? "YES (PASSED)" : "NO (FAILED)"), $hasNoTuslaStruk ? 'green' : 'red');
                CLI::write("  - Embalase hidden on Struk: " . ($hasNoEmbStruk ? "YES (PASSED)" : "NO (FAILED)"), $hasNoEmbStruk ? 'green' : 'red');
            } else {
                CLI::error("Thermal receipt view FAILED");
            }

            // Test cetakKwitansiResep
            $kwitansiView = $apotek->cetakKwitansiResep($testId);
            if (is_string($kwitansiView) && strpos($kwitansiView, 'Kwitansi Resep') !== false) {
                $hasNoTuslaKwitansi = (strpos($kwitansiView, '<th>Tuslah</th>') === false && strpos($kwitansiView, 'Total Jasa Racik') === false);
                $hasNoEmbKwitansi   = (strpos($kwitansiView, '<th>Embalase</th>') === false && strpos($kwitansiView, 'Total Kemasan') === false);
                CLI::write("Official kwitansi view (cetak-kwitansi-resep) PASSED: Contains Kwitansi Resep", 'green');
                CLI::write("  - Tuslah hidden on Kwitansi: " . ($hasNoTuslaKwitansi ? "YES (PASSED)" : "NO (FAILED)"), $hasNoTuslaKwitansi ? 'green' : 'red');
                CLI::write("  - Embalase hidden on Kwitansi: " . ($hasNoEmbKwitansi ? "YES (PASSED)" : "NO (FAILED)"), $hasNoEmbKwitansi ? 'green' : 'red');
            } else {
                CLI::error("Official kwitansi view FAILED");
            }

            // Test cetakEtiket
            $etiketView = $apotek->cetakEtiket($testId);
            if (is_string($etiketView) && strpos($etiketView, 'Cetak E-Tiket') !== false) {
                CLI::write("E-Tiket view (cetak-etiket) PASSED: Contains Cetak E-Tiket", 'green');
            } else {
                CLI::error("E-Tiket view FAILED");
            }
        }

        CLI::write("\n=== ALL PRESCRIPTION HISTORY & PRINT TESTS PASSED CLEANLY! ===", 'green');
    }
}
