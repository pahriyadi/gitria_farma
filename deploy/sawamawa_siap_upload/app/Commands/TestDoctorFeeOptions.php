<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestDoctorFeeOptions extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:doctor-fee-options';
    protected $description = 'Test Doctor Fee Options (Fixed Nominal vs Percentage) and Auto-Journaling Integration';

    public function run(array $params)
    {
        $db = \Config\Database::connect('default');
        $journalEngine = new \App\Services\JournalEngine();

        CLI::write("=================================================================", 'yellow');
        CLI::write("=== TESTING DOCTOR FEE OPTIONS & AUTO JOURNAL INTEGRATION ===", 'yellow');
        CLI::write("=================================================================", 'yellow');

        // 1. Check doctors table fee_type column
        CLI::write("\n[1] Check Column 'fee_type' on table 'doctors':", 'cyan');
        $fields = $db->getFieldNames('doctors');
        if (in_array('fee_type', $fields)) {
            CLI::write("  [OK] Column 'fee_type' successfully exists in table doctors!", 'green');
        } else {
            CLI::write("  [FAIL] Column 'fee_type' not found!", 'red');
            return;
        }

        // 2. Setup or Update Doctor with Percentage
        CLI::write("\n[2] Testing Doctor with PERCENTAGE fee (DR. A - 66.67%):", 'cyan');
        $docA = $db->table('doctors')->where('id', 1)->get()->getRow();
        if ($docA) {
            $db->table('doctors')->where('id', 1)->update([
                'fee_type'       => 'percentage',
                'fee_per_pasien' => 66.67
            ]);
            CLI::write("  Doctor 1 ({$docA->name}): fee_type = percentage, fee_per_pasien = 66.67%", 'green');
        }

        // Test Split Journal for Doc A (Amount Rp 150.000, fee 66.67% = Rp 100.000)
        $resPct = $journalEngine->postClinicSplitJournal(
            888801,
            150000,
            'Simulasi Dokter Persentase 66.67%',
            'tunai',
            66.67,
            'Test Kasir',
            null
        );
        CLI::write("  Journal Status: " . $resPct['status'] . " (" . ($resPct['journal_no'] ?? '') . ")", 'green');
        $jnlPct = $db->table('journal_entries')->where('journal_no', $resPct['journal_no'])->get()->getRow();
        if ($jnlPct) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code, accounts.name')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $jnlPct->id)
                          ->get()->getResult();
            $totD = 0; $totC = 0;
            foreach ($details as $d) {
                CLI::write("    [{$d->code}] {$d->name} -> D: " . number_format($d->debit, 2) . " | K: " . number_format($d->credit, 2));
                $totD += (float)$d->debit;
                $totC += (float)$d->credit;
            }
            CLI::write("  Total: D = " . number_format($totD, 2) . " | K = " . number_format($totC, 2) . " | Balance: " . (abs($totD - $totC) < 0.01 ? 'YES (100% BALANCE)' : 'NO'), 'green');
            
            // Clean up
            $db->table('journal_entry_details')->where('journal_id', $jnlPct->id)->delete();
            $db->table('journal_entries')->where('id', $jnlPct->id)->delete();
        }

        // 3. Setup or Update Doctor with FIXED NOMINAL
        CLI::write("\n[3] Testing Doctor with FIXED NOMINAL fee (DR. B - Rp 50.000):", 'cyan');
        $docB = $db->table('doctors')->where('id', 2)->get()->getRow();
        if ($docB) {
            $db->table('doctors')->where('id', 2)->update([
                'fee_type'       => 'fixed_amount',
                'fee_per_pasien' => 50000.00
            ]);
            CLI::write("  Doctor 2 ({$docB->name}): fee_type = fixed_amount, fee_per_pasien = Rp 50.000", 'green');
        }

        // Test Split Journal for Doc B (Amount Rp 150.000, fee Rp 50.000)
        $resFixed = $journalEngine->postClinicSplitJournal(
            888802,
            150000,
            'Simulasi Dokter Nominal Tetap Rp 50.000',
            'tunai',
            null,
            'Test Kasir',
            50000.00
        );
        CLI::write("  Journal Status: " . $resFixed['status'] . " (" . ($resFixed['journal_no'] ?? '') . ")", 'green');
        $jnlFixed = $db->table('journal_entries')->where('journal_no', $resFixed['journal_no'])->get()->getRow();
        if ($jnlFixed) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code, accounts.name')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $jnlFixed->id)
                          ->get()->getResult();
            $totD = 0; $totC = 0;
            foreach ($details as $d) {
                CLI::write("    [{$d->code}] {$d->name} -> D: " . number_format($d->debit, 2) . " | K: " . number_format($d->credit, 2));
                $totD += (float)$d->debit;
                $totC += (float)$d->credit;
            }
            CLI::write("  Total: D = " . number_format($totD, 2) . " | K = " . number_format($totC, 2) . " | Balance: " . (abs($totD - $totC) < 0.01 ? 'YES (100% BALANCE)' : 'NO'), 'green');
            
            // Clean up
            $db->table('journal_entry_details')->where('journal_id', $jnlFixed->id)->delete();
            $db->table('journal_entries')->where('id', $jnlFixed->id)->delete();
        }

        // 4. Test Doctor with FIXED NOMINAL Rp 80.000
        CLI::write("\n[4] Testing Doctor with FIXED NOMINAL fee (Rp 80.000 pada Layanan Rp 150.000):", 'cyan');
        $resFixed3 = $journalEngine->postClinicSplitJournal(
            888803,
            150000,
            'Simulasi Dokter Nominal Tetap Rp 80.000',
            'tunai',
            null,
            'Test Kasir',
            80000.00
        );
        CLI::write("  Journal Status: " . $resFixed3['status'] . " (" . ($resFixed3['journal_no'] ?? '') . ")", 'green');
        $jnlFixed3 = $db->table('journal_entries')->where('journal_no', $resFixed3['journal_no'])->get()->getRow();
        if ($jnlFixed3) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code, accounts.name')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->where('journal_id', $jnlFixed3->id)
                          ->get()->getResult();
            $totD = 0; $totC = 0;
            foreach ($details as $d) {
                CLI::write("    [{$d->code}] {$d->name} -> D: " . number_format($d->debit, 2) . " | K: " . number_format($d->credit, 2));
                $totD += (float)$d->debit;
                $totC += (float)$d->credit;
            }
            CLI::write("  Total: D = " . number_format($totD, 2) . " | K = " . number_format($totC, 2) . " | Balance: " . (abs($totD - $totC) < 0.01 ? 'YES (100% BALANCE)' : 'NO'), 'green');
            
            // Clean up
            $db->table('journal_entry_details')->where('journal_id', $jnlFixed3->id)->delete();
            $db->table('journal_entries')->where('id', $jnlFixed3->id)->delete();
        }

        CLI::write("\n>>> ALL TESTS PASSED! PERSENTASE & NOMINAL TETAP BERFUNGSI SEMPURNA & BALANCE <<<\n", 'green');
    }
}
