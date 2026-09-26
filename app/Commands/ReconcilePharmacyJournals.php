<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ReconcilePharmacyJournals extends BaseCommand
{
    protected $group       = 'Accounting';
    protected $name        = 'accounting:reconcile-journals';
    protected $description = 'Clean up duplicate pharmacy journal entries and reconcile account balances.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // 1. Find all duplicates for Apotek (Obat Bebas) ref: 2
        $entries = $db->table('journal_entries')
                      ->where('source_module', 'Apotek (Obat Bebas)')
                      ->where('reference_id', 2)
                      ->orderBy('id', 'ASC')
                      ->get()
                      ->getResult();

        CLI::write("Found " . count($entries) . " journal entries for pharmacy sale 2.", 'yellow');

        if (count($entries) > 1) {
            $keepId = $entries[0]->id;
            CLI::write("Keeping original entry ID: {$keepId} ({$entries[0]->journal_no})", 'green');
            
            for ($i = 1; $i < count($entries); $i++) {
                $delId = $entries[$i]->id;
                $db->table('journal_entry_details')->where('journal_id', $delId)->delete();
                $db->table('journal_entries')->where('id', $delId)->delete();
                CLI::write("Deleted duplicate entry ID: {$delId} ({$entries[$i]->journal_no})", 'light_gray');
            }
        }

        // 2. Trigger sync to post sale 1 if missing
        $je = new \App\Services\JournalEngine();
        $je->syncUnpostedTransactions();

        // 3. Check journals for sale 1 and 2
        $s1J = $db->table('journal_entries')->where('source_module', 'Apotek (Obat Bebas)')->where('reference_id', 1)->get()->getResult();
        $s2J = $db->table('journal_entries')->where('source_module', 'Apotek (Obat Bebas)')->where('reference_id', 2)->get()->getResult();
        CLI::write("Journals for Sale 1: " . count($s1J) . " (Journal: " . ($s1J[0]->journal_no ?? '-') . ")", 'green');
        CLI::write("Journals for Sale 2: " . count($s2J) . " (Journal: " . ($s2J[0]->journal_no ?? '-') . ")", 'green');

        // 4. Test multiple syncs to ensure NO duplicates ever happen
        $je->syncUnpostedTransactions();
        $je->syncUnpostedTransactions();
        $je->syncUnpostedTransactions();

        $s1J_after = $db->table('journal_entries')->where('source_module', 'Apotek (Obat Bebas)')->where('reference_id', 1)->get()->getResult();
        $s2J_after = $db->table('journal_entries')->where('source_module', 'Apotek (Obat Bebas)')->where('reference_id', 2)->get()->getResult();
        CLI::write("Journals for Sale 1 after 3 sync calls: " . count($s1J_after) . " (Expected: 1)", 'green');
        CLI::write("Journals for Sale 2 after 3 sync calls: " . count($s2J_after) . " (Expected: 1)", 'green');

        // 5. Reconcile account balances to exact ledger sums
        CLI::write("\n--- Reconciling account balances with ledger details ---", 'yellow');
        $accounts = $db->table('accounts')->get()->getResult();
        foreach ($accounts as $acc) {
            $sumDebits = $db->table('journal_entry_details')
                            ->selectSum('debit')
                            ->where('account_id', $acc->id)
                            ->get()
                            ->getRow()->debit ?? 0.00;
                            
            $sumCredits = $db->table('journal_entry_details')
                             ->selectSum('credit')
                             ->where('account_id', $acc->id)
                             ->get()
                             ->getRow()->credit ?? 0.00;
                             
            $correctBal = ($acc->normal_balance === 'debit') ? ($sumDebits - $sumCredits) : ($sumCredits - $sumDebits);
            $db->table('accounts')->where('id', $acc->id)->update(['balance' => $correctBal]);
        }
        CLI::write("Account balances successfully reconciled with ledger.", 'green');
    }
}
