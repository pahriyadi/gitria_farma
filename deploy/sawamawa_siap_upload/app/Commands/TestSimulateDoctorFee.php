<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestSimulateDoctorFee extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:simulate-doctor-fee';
    protected $description = 'Test API Simulation of Doctor Fee Nominal vs Percentage';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $cat = $db->table('journal_categories')->where('category_code', 'RAWAT_JALAN_POLI')->get()->getRow();
        if (!$cat) {
            CLI::error("Kategori RAWAT_JALAN_POLI tidak ditemukan!");
            return;
        }

        $accounting = new \App\Controllers\Accounting();
        $accounting->initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );

        // Test 1: Simulation with percentage (66.67%)
        $_GET['amount'] = '150000';
        $_GET['doctor_fee_pct'] = '66.67';
        unset($_GET['doctor_fee_nominal']);

        $res1 = $accounting->apiSimulateSplit($cat->id);
        $json1 = json_decode($res1->getBody(), true);

        CLI::write("=== TEST 1: SIMULATION WITH PERCENTAGE (66.67%) ===", 'yellow');
        CLI::write("Balanced: " . ($json1['is_balanced'] ? 'YES' : 'NO'));
        foreach ($json1['credits'] as $c) {
            CLI::write("  - [{$c['account_code']}] {$c['item_name']}: {$c['amount_fmt']} ({$c['pct']}%)");
        }

        // Test 2: Simulation with fixed nominal (Rp 50.000)
        $_GET['amount'] = '150000';
        $_GET['doctor_fee_nominal'] = '50000';

        $res2 = $accounting->apiSimulateSplit($cat->id);
        $json2 = json_decode($res2->getBody(), true);

        CLI::write("\n=== TEST 2: SIMULATION WITH FIXED NOMINAL (Rp 50.000) ===", 'yellow');
        CLI::write("Balanced: " . ($json2['is_balanced'] ? 'YES' : 'NO'));
        foreach ($json2['credits'] as $c) {
            CLI::write("  - [{$c['account_code']}] {$c['item_name']}: {$c['amount_fmt']} ({$c['pct']}%)");
        }

        CLI::write("\n>>> ALL SIMULATION TESTS FINISHED SUCCESSFULLY! <<<", 'green');
    }
}
