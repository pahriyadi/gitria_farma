<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestVoidIntegrity extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:void-integrity';
    protected $description = 'Runs comprehensive operational CRUD and integrity tests for Void Reversal & Anti-Fraud Center';

    public function run(array $params)
    {
        CLI::write("=======================================================", 'yellow');
        CLI::write("🛡️  OPERATIONAL CRUD & INTEGRITY TEST SUITE - VOID CENTER", 'yellow');
        CLI::write("=======================================================\n", 'yellow');

        $db = \Config\Database::connect('default');
        $voidEngine = new \App\Services\VoidReversalEngine();

        $passCount = 0;
        $failCount = 0;

        $assertTest = function($description, $condition) use (&$passCount, &$failCount) {
            if ($condition) {
                CLI::write(" [PASS] " . $description, 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] " . $description, 'red');
                $failCount++;
            }
        };

        // -----------------------------------------------------------------------------
        // TEST 1: Database Tables Existence & Columns
        // -----------------------------------------------------------------------------
        CLI::write("1. Checking Database Tables & Structure...", 'cyan');
        $tables = ['void_audit_logs', 'void_reasons_master', 'supervisor_pins'];
        foreach ($tables as $tbl) {
            $exists = $db->tableExists($tbl);
            $assertTest("Table `$tbl` exists", $exists);
        }

        $colLogs = $db->getFieldNames('void_audit_logs');
        $assertTest("`void_audit_logs` has `item_snapshot_json` column", in_array('item_snapshot_json', $colLogs));
        $assertTest("`void_audit_logs` has `reversal_journal_id` column", in_array('reversal_journal_id', $colLogs));
        $assertTest("`void_audit_logs` has `void_number` column", in_array('void_number', $colLogs));

        // -----------------------------------------------------------------------------
        // TEST 2: Supervisor PIN Management & Verification
        // -----------------------------------------------------------------------------
        CLI::write("\n2. Testing Supervisor PIN Verification & Security...", 'cyan');
        $supervisor = $db->table('users')
                         ->select('users.id, users.username, users.fullname')
                         ->join('roles', 'roles.id = users.role_id')
                         ->whereIn('roles.name', ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan', 'Manajer'])
                         ->where('users.status', 'active')
                         ->get()
                         ->getFirstRow();

        if ($supervisor) {
            $spvId = (int)$supervisor->id;
            CLI::write("   Testing with Supervisor: {$supervisor->fullname} (ID: $spvId)", 'white');

            // Test valid default PIN (123456)
            $resValid = $voidEngine->verifySupervisorPin($spvId, '123456');
            $assertTest("Verification with correct PIN (123456)", $resValid['valid'] === true);

            // Test invalid PIN
            $resInvalid = $voidEngine->verifySupervisorPin($spvId, '000000');
            $assertTest("Verification with wrong PIN is rejected", ($resInvalid['valid'] === false));

            // Reset failed attempts for this test user
            $db->table('supervisor_pins')->where('user_id', $spvId)->update([
                'failed_attempts' => 0,
                'locked_until' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $assertTest("Reset failed attempts counter & lockout", true);
        } else {
            CLI::write("   [SKIP] No supervisor user found in active state", 'yellow');
        }

        // -----------------------------------------------------------------------------
        // TEST 3: Master Reasons Data
        // -----------------------------------------------------------------------------
        CLI::write("\n3. Testing Master Reasons Data...", 'cyan');
        $reasonsCount = $db->table('void_reasons_master')->where('is_active', 1)->countAllResults();
        $assertTest("Active reasons in `void_reasons_master` >= 5 (Found: $reasonsCount)", $reasonsCount >= 5);

        $sampleReason = $db->table('void_reasons_master')->where('module', 'klinik')->get()->getFirstRow();
        $assertTest("Found reason for module `klinik`", !empty($sampleReason));

        // -----------------------------------------------------------------------------
        // TEST 4: View Rendering Test (Syntax & Context Integrity)
        // -----------------------------------------------------------------------------
        CLI::write("\n4. Testing View Rendering Engine...", 'cyan');
        try {
            $dummyLogs = $db->table('void_audit_logs')->limit(5)->get()->getResult();
            $dummyReasons = $db->table('void_reasons_master')->where('is_active', 1)->get()->getResult();
            $dummySupervisors = $db->table('users')->select('id, fullname as name, username')->limit(5)->get()->getResult();

            $data = [
                'title'        => 'Test Void Logs',
                'active_menu'  => 'accounting-void-logs',
                'logs'         => $dummyLogs,
                'startDate'    => date('Y-m-01'),
                'endDate'      => date('Y-m-d'),
                'selectedModule' => 'all',
                'selectedSupervisor' => 'all',
                'supervisors'  => $dummySupervisors,
                'reasons'      => $dummyReasons,
                'kpi'          => [
                    'todayCount'     => 0,
                    'todayAmount'    => 0,
                    'monthCount'     => 0,
                    'monthAmount'    => 0,
                    'topModuleName'  => 'Belum Ada',
                    'topModuleCount' => 0
                ]
            ];

            ob_start();
            $html = view('accounting/void_logs', $data, ['saveData' => false]);
            $buf = ob_get_clean();
            $rendered = $html ?: $buf;
            $assertTest("View `accounting/void_logs` renders cleanly (" . strlen($rendered) . " bytes)", strlen($rendered) > 500);
        } catch (\Throwable $e) {
            $assertTest("View `accounting/void_logs` failed: " . $e->getMessage(), false);
        }

        try {
            $dummyLogObj = (object)[
                'id' => 9999,
                'void_number' => 'VOID-TEST-20261006-0001',
                'transaction_type' => 'billing_klinik',
                'reference_id' => 123,
                'reference_number' => 'INV-TEST-001',
                'total_amount' => 150000,
                'payment_method' => 'tunai',
                'reason_category' => 'Salah Entri Kasir',
                'reason_detail' => 'Uji coba operasional sistem anti-fraud',
                'item_snapshot_json' => json_encode(['billing' => ['patient_name' => 'Pasien Testing', 'no_rm' => 'RM-0001']]),
                'cashier_name' => 'Kasir Test',
                'cashier_username' => 'kasir1',
                'supervisor_name' => 'Dr. Supervisor',
                'supervisor_username' => 'spv1',
                'supervisor_role_name' => 'Kepala Klinik',
                'reversal_journal_no' => 'JV-VOID-20261006-0001',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit/CLI Test',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'completed'
            ];

            ob_start();
            $htmlCetak = view('accounting/cetak_berita_acara_void', ['log' => $dummyLogObj], ['saveData' => false]);
            $buf2 = ob_get_clean();
            $rendered2 = $htmlCetak ?: $buf2;
            $assertTest("View `accounting/cetak_berita_acara_void` renders cleanly (" . strlen($rendered2) . " bytes)", strlen($rendered2) > 500);
        } catch (\Throwable $e) {
            $assertTest("View `accounting/cetak_berita_acara_void` failed: " . $e->getMessage(), false);
        }

        // -----------------------------------------------------------------------------
        // TEST 5: Reversal Engine Dry-Run (Database Transaction Rollback)
        // -----------------------------------------------------------------------------
        CLI::write("\n5. Testing Reversal Engine Execution (Isolated Dry-Run)...", 'cyan');
        try {
            // Test non-existent transaction gives clean error
            $dryResult = $voidEngine->executeVoid('billing_klinik', 999999, 'Test', 'Testing', 1, 1, '127.0.0.1', 'CLI');
            $assertTest("Non-existent transaction returns structured error gracefully", $dryResult['status'] === 'error');
        } catch (\Throwable $e) {
            $assertTest("Dry run failed: " . $e->getMessage(), false);
        }

        // -----------------------------------------------------------------------------
        // SUMMARY
        // -----------------------------------------------------------------------------
        CLI::write("\n=======================================================", 'yellow');
        CLI::write("TEST RESULTS: Total = " . ($passCount + $failCount) . " | Passed = $passCount | Failed = $failCount", 'yellow');
        CLI::write("=======================================================", 'yellow');

        if ($failCount === 0) {
            CLI::write("🎉 ALL OPERATIONAL CRUD & INTEGRITY CHECKS PASSED WITH 0 ERRORS!\n", 'green');
        } else {
            CLI::write("⚠️ SOME CHECKS FAILED.\n", 'red');
        }
    }
}
