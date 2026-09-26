<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixJournalBadge extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'journal:fix-badge';
    protected $description = 'Fix journal source_module to Apotek (Obat Resep) for prescription entries';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Update JV-20260906-0002 and any existing prescription journals
        $updated = $db->table('journal_entries')
                      ->where('journal_no', 'JV-20260906-0002')
                      ->orLike('description', 'Resep')
                      ->update(['source_module' => 'Apotek (Obat Resep)']);

        CLI::write("Journal source_module updated for prescription journals: {$updated} rows affected.", 'green');

        // Check the journal entries now
        $rows = $db->table('journal_entries')->get()->getResultArray();
        CLI::write("\n=== CURRENT JOURNAL ENTRIES ===", 'yellow');
        foreach ($rows as $r) {
            CLI::write("ID: {$r['id']} | No: {$r['journal_no']} | Module: {$r['source_module']} | Desc: " . substr($r['description'], 0, 60) . '...');
        }
    }
}
