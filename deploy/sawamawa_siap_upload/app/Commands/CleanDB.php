<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanDB extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:clean';
    protected $description = 'Truncate all tables except users and auth.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();

        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            if (
                $table === 'migrations' ||
                strpos($table, 'user') !== false ||
                strpos($table, 'auth_') !== false ||
                strpos($table, 'role') !== false ||
                $table === 'journal_categories' ||
                $table === 'journal_category_rules'
            ) {
                CLI::write("Skipping {$table}...", 'yellow');
                continue;
            }

            CLI::write("Truncating {$table}...", 'green');
            $db->query("TRUNCATE TABLE {$table}");
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 1');
        CLI::write("Done.", 'green');
    }
}
