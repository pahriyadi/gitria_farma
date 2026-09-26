<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JournalEngine;

class TestPharmacyFeatures extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:pharmacy-features';
    protected $description = 'Uji coba Split Journal Apotek, Tusla, Embalase, dan Racikan';

    public function run(array $params)
    {
        CLI::write("=== TEST 1: Penjualan Obat Resep Rp 85.000 (Fee Dokter 5%) ===", 'yellow');
        $je = new JournalEngine();
        $res1 = $je->postPharmacySplitJournal('resep', 99991, 85000, 'Test Resep Rp 85.000', 'tunai', 5.0, 'Testing');
        CLI::write("Hasil: " . json_encode($res1), 'cyan');

        $db = \Config\Database::connect('default');
        $details1 = $db->table('journal_entry_details jed')
                       ->select('jed.*, a.code, a.name')
                       ->join('accounts a', 'a.id = jed.account_id')
                       ->join('journal_entries je', 'je.id = jed.journal_id')
                       ->where('je.journal_no', $res1['journal_no'])
                       ->get()
                       ->getResult();

        $sumD1 = 0; $sumC1 = 0;
        foreach ($details1 as $d) {
            CLI::write(sprintf("  [%-6s] %-36s | Debit: Rp %9s | Kredit: Rp %9s", $d->code, $d->name, number_format($d->debit, 0, ',', '.'), number_format($d->credit, 0, ',', '.')), 'light_gray');
            $sumD1 += $d->debit;
            $sumC1 += $d->credit;
        }
        CLI::write(sprintf("  TOTAL -> Debit: Rp %s | Kredit: Rp %s [BALANCE: %s]", number_format($sumD1, 0, ',', '.'), number_format($sumC1, 0, ',', '.'), ($sumD1 == $sumC1 ? 'OK' : 'FAIL')), 'green');

        CLI::write("\n=== TEST 2: Penjualan Obat Bebas Rp 100.000 ===", 'yellow');
        $res2 = $je->postPharmacySplitJournal('bebas', 99992, 100000, 'Test Obat Bebas Rp 100.000', 'tunai', 0, 'Testing');
        CLI::write("Hasil: " . json_encode($res2), 'cyan');

        $details2 = $db->table('journal_entry_details jed')
                       ->select('jed.*, a.code, a.name')
                       ->join('accounts a', 'a.id = jed.account_id')
                       ->join('journal_entries je', 'je.id = jed.journal_id')
                       ->where('je.journal_no', $res2['journal_no'])
                       ->get()
                       ->getResult();

        $sumD2 = 0; $sumC2 = 0;
        foreach ($details2 as $d) {
            CLI::write(sprintf("  [%-6s] %-36s | Debit: Rp %9s | Kredit: Rp %9s", $d->code, $d->name, number_format($d->debit, 0, ',', '.'), number_format($d->credit, 0, ',', '.')), 'light_gray');
            $sumD2 += $d->debit;
            $sumC2 += $d->credit;
        }
        CLI::write(sprintf("  TOTAL -> Debit: Rp %s | Kredit: Rp %s [BALANCE: %s]", number_format($sumD2, 0, ',', '.'), number_format($sumC2, 0, ',', '.'), ($sumD2 == $sumC2 ? 'OK' : 'FAIL')), 'green');

        // Cleanup
        $db->table('journal_entry_details')->whereIn('journal_id', function($builder) {
            return $builder->select('id')->from('journal_entries')->whereIn('reference_id', [99991, 99992]);
        })->delete();
        $db->table('journal_entries')->whereIn('reference_id', [99991, 99992])->delete();
        CLI::write("\nCleanup data test selesai.", 'dark_gray');
    }
}
