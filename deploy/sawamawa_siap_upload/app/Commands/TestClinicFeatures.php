<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestClinicFeatures extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:clinic-features';
    protected $description = 'Test E2E fitur Kasir Klinik & Jurnal Bagi Hasil Konsultasi';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $journalEngine = new \App\Services\JournalEngine();

        // Alter percentage_value column to DECIMAL(8,4) so it supports 19.0250 and 10.9875
        $db->query("ALTER TABLE journal_category_rules MODIFY COLUMN percentage_value DECIMAL(8,4) DEFAULT 0.0000;");

        // Seed / Update RAWAT_JALAN_POLI rules
        $catPoli = $db->table('journal_categories')->where('category_code', 'RAWAT_JALAN_POLI')->get()->getRow();
        if ($catPoli) {
            $db->table('journal_category_rules')->where('category_id', $catPoli->id)->delete();
            $now = date('Y-m-d H:i:s');
            $newRules = [
                ['category_id' => $catPoli->id, 'item_name' => 'KAS TUNAI KLINIK', 'account_id' => 175, 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.0000, 'fixed_amount_value' => 0.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'DOKTER', 'account_id' => 115, 'position' => 'credit', 'calc_type' => 'dynamic_fee', 'percentage_value' => 66.6667, 'fixed_amount_value' => 0.00, 'formula_code' => 'DOCTOR_FEE_PCT', 'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'UTANG FEE KARYAWAN', 'account_id' => 82, 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 6.6667, 'fixed_amount_value' => 10000.00, 'formula_code' => 'EMPLOYEE_FEE', 'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'FASILITAS KLINIK', 'account_id' => 174, 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 17.9307, 'fixed_amount_value' => 0.00, 'formula_code' => 'REMAINDER_TIER', 'sort_order' => 4, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'B M H P', 'account_id' => 106, 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 5.0733, 'fixed_amount_value' => 0.00, 'formula_code' => 'REMAINDER_TIER', 'sort_order' => 5, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'ADMINISTRASI', 'account_id' => 112, 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 2.9300, 'fixed_amount_value' => 0.00, 'formula_code' => 'REMAINDER_TIER', 'sort_order' => 6, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['category_id' => $catPoli->id, 'item_name' => 'KONSELING FARMASI', 'account_id' => 113, 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 0.7326, 'fixed_amount_value' => 0.00, 'formula_code' => 'DYNAMIC_OBAT_REMAINDER', 'sort_order' => 7, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ];
            $db->table('journal_category_rules')->insertBatch($newRules);
            CLI::write("✓ Berhasil memperbarui aturan RAWAT_JALAN_POLI ke formula klien (100% Balanced)!", 'green');
        }

        CLI::write("=== TEST 1: KONSULTASI KLINIK Rp 150.000 (Fee Dokter 66.67%) ===", 'yellow');
        
        $billingId = 99991;
        $totalServices = 150000;
        $descMedis = "Pendapatan Layanan Medis Klinik - BIL-TEST-01 (Test Pasien)";
        $paymentMethod = 'tunai';
        $docFeeMedisPct = 66.666667; // approx 100.000 out of 150.000

        $res1 = $journalEngine->postClinicSplitJournal(
            $billingId,
            $totalServices,
            $descMedis,
            $paymentMethod,
            $docFeeMedisPct,
            'Kasir Utama'
        );

        CLI::write("Hasil: " . json_encode($res1));

        if ($res1['status'] === 'success') {
            $journalNo = $res1['journal_no'];
            $je = $db->table('journal_entries')->where('journal_no', $journalNo)->get()->getRow();
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.name as acc_name, accounts.code as acc_code')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $je->id)
                          ->get()->getResult();

            $totalDeb = 0;
            $totalCre = 0;
            foreach ($details as $d) {
                $deb = (float)$d->debit;
                $cre = (float)$d->credit;
                $totalDeb += $deb;
                $totalCre += $cre;

                $debStr = $deb > 0 ? 'Rp ' . str_pad(number_format($deb, 0, ',', '.'), 9, ' ', STR_PAD_LEFT) : 'Rp         0';
                $creStr = $cre > 0 ? 'Rp ' . str_pad(number_format($cre, 0, ',', '.'), 9, ' ', STR_PAD_LEFT) : 'Rp         0';
                $codeName = '[' . str_pad($d->acc_code, 5, ' ', STR_PAD_RIGHT) . '] ' . str_pad($d->acc_name, 36, ' ', STR_PAD_RIGHT);
                CLI::write("  {$codeName} | Debit: {$debStr} | Kredit: {$creStr}");
            }

            $bal = abs($totalDeb - $totalCre) < 0.01 ? '[BALANCE: OK]' : '[TIDAK BALANCE!]';
            CLI::write("  TOTAL -> Debit: Rp " . number_format($totalDeb, 0, ',', '.') . " | Kredit: Rp " . number_format($totalCre, 0, ',', '.') . " {$bal}");
            
            // cleanup
            $db->table('journal_entry_details')->where('journal_id', $je->id)->delete();
            $db->table('journal_entries')->where('id', $je->id)->delete();
        }

        CLI::write("\nCleanup data test selesai.", 'green');
    }
}
