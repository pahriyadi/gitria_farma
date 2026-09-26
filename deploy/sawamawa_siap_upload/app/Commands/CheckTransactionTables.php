<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTransactionTables extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:check-tx-tables';
    protected $description = 'Check transaction tables count';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $allTables = $db->listTables();
        $related = array_filter($allTables, function($t) {
            return preg_match('/(visit|queue|record|presc|bill|cash|journal|pharmacy|sale|apotek)/i', $t);
        });

        CLI::write("=== RELATED TABLES COUNT ===", 'yellow');
        foreach ($related as $t) {
            $count = $db->table($t)->countAllResults();
            CLI::write(sprintf("%-30s: %d rows", $t, $count));
        }

        $jnl = $db->table('journal_entries')->get()->getResultArray();
        CLI::write("\n=== JOURNAL ENTRIES ===", 'yellow');
        foreach ($jnl as $j) {
            CLI::write("ID: {$j['id']} | No: {$j['journal_no']} | Date: {$j['entry_date']} | Module: {$j['source_module']} | Desc: {$j['description']}");
        }
    }
}
