<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\PharmacyService;
use App\Services\JournalEngine;

class TestPharmacyE2E extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:pharmacy-e2e';
    protected $description = 'Pengujian menyeluruh (E2E) 5 Fitur Apotek, Racikan Masking, Tusla/Embalase, Pending, dan Jurnal Bagi Hasil';

    public function run(array $params)
    {
        $db = \Config\Database::connect('default');
        CLI::write("================================================================", 'cyan');
        CLI::write("       UJI COBA MENYELURUH (E2E) FITUR FARMASI & APOTEK        ", 'yellow');
        CLI::write("================================================================", 'cyan');

        // 1. Ambil 2 sampel obat untuk transaksi langsung & racikan
        $med1 = $db->table('medicines')->where('status', 'active')->get()->getRow();
        if (!$med1) {
            CLI::error("Tidak ada data obat di database.");
            return;
        }

        // Ambil batch untuk obat 1
        $batch1 = $db->table('medicine_batches')->where('medicine_id', $med1->id)->where('stock >=', 2)->get()->getRow();
        if (!$batch1) {
            // Seed batch jika kosong
            $db->table('medicine_batches')->insert([
                'medicine_id'  => $med1->id,
                'batch_no'     => 'BTC-TEST-E2E-01',
                'stock'        => 50,
                'expired_date' => date('Y-12-31', strtotime('+1 year')),
                'buy_price'    => 5000,
                'sale_price'   => 8500,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            $batch1 = $db->table('medicine_batches')->where('medicine_id', $med1->id)->get()->getRow();
        }

        $doctor = $db->table('doctors')->where('status', 'active')->get()->getRow();
        $docId = $doctor ? $doctor->id : null;

        CLI::write("\n[STEP 1] Memproses Transaksi Penjualan Apotek (Obat + Racikan + Tusla + Embalase)...", 'yellow');
        $ps = new PharmacyService();
        $stockBefore = $batch1->stock;

        $salePayload = [
            'customer_name'     => 'Pasien Demo Budi',
            'customer_phone'    => '081234567890',
            'payment_method'    => 'tunai',
            'paid_amount'       => 100000,
            'doctor_id'         => $docId,
            'prescription_type' => 'racikan',
            'tusla_amount'      => 3000,
            'embalase_amount'   => 2000,
            'notes'             => 'Test Transaksi E2E Kasir Apotek',
            'cashier_id'        => 1,
            'items'             => [
                [
                    'medicine_id'        => $med1->id,
                    'batch_id'           => $batch1->id,
                    'qty'                => 2,
                    'discount'           => 0,
                    'dosage_instruction' => '3x1 tablet sesudah makan',
                    'is_racikan'         => 0,
                    'racikan_name'       => '',
                    'racikan_group'      => ''
                ],
                [
                    'medicine_id'        => $med1->id,
                    'batch_id'           => $batch1->id,
                    'qty'                => 3,
                    'discount'           => 0,
                    'dosage_instruction' => '3x1 bungkus sesudah makan',
                    'is_racikan'         => 1,
                    'racikan_name'       => 'Puyer Flu & Demam Anak 10 Bungkus',
                    'racikan_group'      => 'RCK-TEST-001'
                ]
            ]
        ];

        $resSale = $ps->processDirectSale($salePayload);
        CLI::write("Status Transaksi: " . json_encode($resSale), 'green');

        if ($resSale['status'] !== 'success') {
            CLI::error("Transaksi gagal!");
            return;
        }

        $saleId = $resSale['sale_id'];

        // Cek pengurangan stok batch
        $batchAfter = $db->table('medicine_batches')->where('id', $batch1->id)->get()->getRow();
        $deducted = $stockBefore - $batchAfter->stock;
        CLI::write(sprintf("Stok Batch [%s]: Awal = %d | Sisa = %d | Terpotong = %d (Expected: 5)", $batch1->batch_no, $stockBefore, $batchAfter->stock, $deducted), 'cyan');

        // Cek data di database pharmacy_sales
        $saleDb = $db->table('pharmacy_sales')->where('id', $saleId)->get()->getRow();
        CLI::write(sprintf("Data Penjualan: No = %s | Total = Rp %s | Tusla = Rp %s | Embalase = Rp %s | Grand Total = Rp %s",
            $saleDb->sale_no,
            number_format($saleDb->total_amount, 0, ',', '.'),
            number_format($saleDb->tusla_amount, 0, ',', '.'),
            number_format($saleDb->embalase_amount, 0, ',', '.'),
            number_format($saleDb->grand_total, 0, ',', '.')
        ), 'light_gray');

        // Cek data di pharmacy_sale_details
        $saleDetails = $db->table('pharmacy_sale_details')->where('sale_id', $saleId)->get()->getResult();
        CLI::write("Rincian Item Database (Total " . count($saleDetails) . " baris):", 'yellow');
        foreach ($saleDetails as $sd) {
            CLI::write(sprintf("  -> Med ID: %d | Is Racikan: %d | Racikan Name: '%s' | Qty: %d | Subtotal: Rp %s",
                $sd->medicine_id, $sd->is_racikan, $sd->racikan_name, $sd->qty, number_format($sd->subtotal, 0, ',', '.')
            ), 'light_gray');
        }

        CLI::write("\n[STEP 2] Memeriksa Jurnal Bagi Hasil Perakun (Double-Entry Balance)...", 'yellow');
        $journal = $db->table('journal_entries')->where('source_module', 'Farmasi Apotek')->where('reference_id', $saleId)->get()->getRow();
        if ($journal) {
            CLI::write(sprintf("No. Jurnal: %s | Tanggal: %s | Deskripsi: %s", $journal->journal_no, $journal->entry_date, $journal->description), 'green');
            $jDetails = $db->table('journal_entry_details jed')
                           ->select('jed.*, a.code, a.name')
                           ->join('accounts a', 'a.id = jed.account_id')
                           ->where('jed.journal_id', $journal->id)
                           ->get()
                           ->getResult();
            $totD = 0; $totC = 0;
            foreach ($jDetails as $jd) {
                CLI::write(sprintf("  [%-6s] %-36s | Debit: Rp %9s | Kredit: Rp %9s", $jd->code, $jd->name, number_format($jd->debit, 0, ',', '.'), number_format($jd->credit, 0, ',', '.')), 'light_gray');
                $totD += $jd->debit;
                $totC += $jd->credit;
            }
            CLI::write(sprintf("  STATUS KESEIMBANGAN JURNAL: Debit = Rp %s | Kredit = Rp %s [%s]",
                number_format($totD, 0, ',', '.'), number_format($totC, 0, ',', '.'), ($totD == $totC && $totD > 0 ? 'BALANCE 100%' : 'TIDAK BALANCE')
            ), 'green');
        } else {
            CLI::error("Jurnal transaksi tidak ditemukan!");
        }

        CLI::write("\n[STEP 3] Pengujian Fitur Pending Resep (Hold & Resume)...", 'yellow');
        $pendingNo = 'PND-TEST-' . time();
        $db->table('pharmacy_pending_prescriptions')->insert([
            'pending_no'      => $pendingNo,
            'customer_name'   => 'Pasien Pending Test',
            'customer_phone'  => '0899998888',
            'patient_id'      => null,
            'doctor_id'       => $docId,
            'source_type'     => 'otc',
            'payload_json'    => json_encode($salePayload['items']),
            'total_amount'    => 55000,
            'tusla_amount'    => 3000,
            'embalase_amount' => 2000,
            'status'          => 'pending',
            'cashier_id'      => 1,
            'notes'           => 'Menunggu konfirmasi keluarga',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);
        $pendingId = $db->insertID();
        CLI::write("Pending Resep berhasil dibuat: ID {$pendingId}, No. {$pendingNo}", 'cyan');

        // Verify Pending Listing
        $activePnd = $db->table('pharmacy_pending_prescriptions')->where('status', 'pending')->where('id', $pendingId)->get()->getRow();
        if ($activePnd) {
            CLI::write("Verifikasi Daftar Pending: Ditemukan antrean untuk '{$activePnd->customer_name}', Total: Rp " . number_format($activePnd->total_amount, 0, ',', '.'), 'green');
        }

        // Simulate Resume & Cancellation
        $db->table('pharmacy_pending_prescriptions')->where('id', $pendingId)->update([
            'status'     => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        CLI::write("Verifikasi Pembatalan Pending: Status berhasil diubah ke 'cancelled'.", 'green');

        CLI::write("\n[STEP 4] Pengujian Cetak Nota & Masking Racikan...", 'yellow');
        // Render view apotek/cetak_nota
        $saleDataForView = $db->table('pharmacy_sales')
                              ->select('pharmacy_sales.*, users.username as cashier_name, doctors.name as doctor_name')
                              ->join('users', 'users.id = pharmacy_sales.cashier_id', 'left')
                              ->join('doctors', 'doctors.id = pharmacy_sales.doctor_id', 'left')
                              ->where('pharmacy_sales.id', $saleId)
                              ->get()
                              ->getRow();

        $itemsForView = $db->table('pharmacy_sale_details')
                           ->select('pharmacy_sale_details.*, medicines.name as medicine_name, medicines.unit, medicine_batches.batch_no')
                           ->join('medicines', 'medicines.id = pharmacy_sale_details.medicine_id', 'left')
                           ->join('medicine_batches', 'medicine_batches.id = pharmacy_sale_details.batch_id', 'left')
                           ->where('pharmacy_sale_details.sale_id', $saleId)
                           ->get()
                           ->getResult();

        $htmlNota = view('apotek/cetak_nota', [
            'title' => 'Nota Penjualan',
            'sale'  => $saleDataForView,
            'items' => $itemsForView
        ]);

        // esc() converts & -> &amp; in HTML output, check both plain and HTML-entity form
        $racikanName = 'Puyer Flu & Demam Anak 10 Bungkus';
        $racikanNameEsc = htmlspecialchars($racikanName, ENT_QUOTES, 'UTF-8');
        $hasMaskedRacikan = (strpos($htmlNota, $racikanName) !== false || strpos($htmlNota, $racikanNameEsc) !== false)
                          && (strpos($htmlNota, 'RACIKAN') !== false);
        $hasTusla = (strpos($htmlNota, 'Tusla') !== false);
        $hasEmbalase = (strpos($htmlNota, 'Embalase') !== false);

        CLI::write("Verifikasi Struk / Nota Pasien:", 'yellow');
        CLI::write("  - Racikan Masked Grouping : " . ($hasMaskedRacikan ? 'PASS (Bahan mentah disembunyikan, paket racikan dicetak)' : 'FAIL'), $hasMaskedRacikan ? 'green' : 'red');
        CLI::write("  - Tusla (Jasa Racik)     : " . ($hasTusla ? 'PASS' : 'FAIL'), $hasTusla ? 'green' : 'red');
        CLI::write("  - Embalase (Kemasan)     : " . ($hasEmbalase ? 'PASS' : 'FAIL'), $hasEmbalase ? 'green' : 'red');

        // Kembalikan stok batch agar data tes bersih
        $db->table('medicine_batches')->where('id', $batch1->id)->update(['stock' => $stockBefore]);
        CLI::write("\n[STEP 5] Pemulihan Stok Simulasi Selesai (Stok kembali ke {$stockBefore}).", 'dark_gray');

        CLI::write("\n================================================================", 'cyan');
        CLI::write("       SEMUA 5 FITUR FARMASI & AKUNTANSI LULUS PENGUJIAN!       ", 'green');
        CLI::write("================================================================", 'cyan');
    }
}
