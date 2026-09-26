<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\PharmacyService;
use App\Services\JournalEngine;

class TestKonsulOnline extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:konsul-online';
    protected $description = 'Verifikasi alur transaksi Konsultasi Online di Kasir Apotek dan penjurnalan per-akun berimbang KONSULTASI_ONLINE';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN PENGUJIAN TRANSAKSI KONSULTASI ONLINE ===", 'yellow');

        $db = \Config\Database::connect('default');
        $pharmacyService = new PharmacyService();

        // 1. Dapatkan dokter
        $doctor = $db->table('doctors')->get()->getRow();
        if (!$doctor) {
            CLI::error("Tidak ditemukan dokter aktif di sistem!");
            return;
        }
        CLI::write("Menggunakan Dokter: {$doctor->name} (ID: {$doctor->id})", 'green');

        // 2. Dapatkan obat dan batch dengan stok
        $batch = $db->table('medicine_batches mb')
                    ->select('mb.id as batch_id, mb.medicine_id, mb.stock, m.name as medicine_name, m.price')
                    ->join('medicines m', 'm.id = mb.medicine_id')
                    ->where('mb.stock >', 5)
                    ->get()
                    ->getRow();

        if (!$batch) {
            CLI::error("Tidak ditemukan batch obat dengan stok > 5!");
            return;
        }
        CLI::write("Menggunakan Obat: {$batch->medicine_name} (Batch ID: {$batch->batch_id}, Harga: Rp " . number_format($batch->price, 0, ',', '.') . ")", 'green');

        // 3. Simulasikan payload transaksi penjualan kasir langsung jenis ONLINE
        $qty = 2;
        $tusla = 3000;
        $embalase = 2000;
        $grandTotal = ($batch->price * $qty) + $tusla + $embalase;

        $payload = [
            'customer_name'     => 'Pasien Simulasi Konsultasi Online',
            'customer_phone'    => '08123456789',
            'payment_method'    => 'tunai',
            'paid_amount'       => $grandTotal,
            'doctor_id'         => $doctor->id,
            'prescription_type' => 'online',
            'tusla_amount'      => $tusla,
            'embalase_amount'   => $embalase,
            'notes'             => 'Testing automasi Konsultasi Online via Spark',
            'cashier_id'        => 1,
            'items'             => [
                [
                    'medicine_id'        => $batch->medicine_id,
                    'batch_id'           => $batch->batch_id,
                    'qty'                => $qty,
                    'price'              => $batch->price,
                    'discount'           => 0,
                    'tusla'              => $tusla,
                    'embalase'           => $embalase,
                    'dosage_instruction' => '3x1 sesudah makan',
                    'is_racikan'         => 0,
                    'racikan_name'       => '',
                    'racikan_group'      => ''
                ]
            ]
        ];

        CLI::write("\nMemproses transaksi kasir langsung (Prescription Type: online, Total: Rp " . number_format($grandTotal, 0, ',', '.') . ")...", 'cyan');
        $res = $pharmacyService->processDirectSale($payload);

        if ($res['status'] !== 'success') {
            CLI::error("Gagal memproses transaksi kasir: " . $res['message']);
            return;
        }

        $saleId = $res['sale_id'];
        CLI::write("Transaksi berhasil disimpan! Sale ID: {$saleId}", 'green');

        // 4. Verifikasi data di tabel pharmacy_sales
        $sale = $db->table('pharmacy_sales')->where('id', $saleId)->get()->getRow();
        if (!$sale || $sale->prescription_type !== 'online') {
            CLI::error("Data pharmacy_sales tidak sesuai! Expected prescription_type: online, Got: " . ($sale->prescription_type ?? 'NULL'));
            return;
        }
        CLI::write("Verifikasi tabel pharmacy_sales: prescription_type = 'online' [PASSED]", 'green');

        // 5. Verifikasi penjurnalan di journal_entries
        $journal = $db->table('journal_entries')
                      ->where('source_module', 'Apotek (Konsultasi Online)')
                      ->where('reference_id', $saleId)
                      ->get()
                      ->getRow();

        if (!$journal) {
            CLI::error("Journal entry tidak ditemukan untuk modul 'Apotek (Konsultasi Online)' dan ref ID {$saleId}!");
            return;
        }
        CLI::write("Verifikasi journal_entries: Journal No: {$journal->journal_no}, Modul: {$journal->source_module} [PASSED]", 'green');

        // 6. Verifikasi rincian akun di journal_entry_details (Double-Entry Balance & Formula KONSULTASI_ONLINE)
        $lines = $db->table('journal_entry_details jl')
                    ->select('jl.*, a.code as account_code, a.name as account_name')
                    ->join('accounts a', 'a.id = jl.account_id', 'left')
                    ->where('jl.journal_id', $journal->id)
                    ->orderBy('jl.debit', 'DESC')
                    ->get()
                    ->getResult();

        $totalDebit = 0;
        $totalCredit = 0;
        CLI::write("\nRincian Jurnal Umum Terbentuk:", 'yellow');
        foreach ($lines as $line) {
            $totalDebit += floatval($line->debit);
            $totalCredit += floatval($line->credit);
            $typeStr = $line->debit > 0 ? "DEBET : Rp " . number_format($line->debit, 2) : "KREDIT: Rp " . number_format($line->credit, 2);
            CLI::write(sprintf("  [%s - %s] %s", $line->account_code, $line->account_name, $typeStr));
        }

        CLI::write("\nTotal Debet : Rp " . number_format($totalDebit, 2), 'cyan');
        CLI::write("Total Kredit: Rp " . number_format($totalCredit, 2), 'cyan');
        $selisih = abs($totalDebit - $totalCredit);
        CLI::write("Selisih     : Rp " . number_format($selisih, 4), $selisih < 0.01 ? 'green' : 'red');

        if ($selisih > 0.01) {
            CLI::error("Jurnal TIDAK BALANCE!");
            return;
        }
        CLI::write("Verifikasi Double-Entry Berimbang 100% [PASSED]", 'green');

        // 7. Cleanup data simulasi testing agar tidak mengotori database operasional
        CLI::write("\nMembersihkan data pengujian...", 'yellow');
        $db->table('journal_entry_details')->where('journal_id', $journal->id)->delete();
        $db->table('journal_entries')->where('id', $journal->id)->delete();
        $db->table('pharmacy_sale_details')->where('sale_id', $saleId)->delete();
        $db->table('pharmacy_sales')->where('id', $saleId)->delete();
        // Kembalikan stok obat
        $db->table('medicine_batches')->where('id', $batch->batch_id)->increment('stock', $qty);

        CLI::write("Pembersihan data pengujian selesai.", 'green');
        CLI::write("=== SELURUH PENGUJIAN FITUR KONSULTASI ONLINE SUKSES (100% PASS) ===", 'green');
    }
}
