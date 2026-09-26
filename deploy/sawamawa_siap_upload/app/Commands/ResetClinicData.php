<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetClinicData extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'clinic:reset';
    protected $description = 'Reset all transactional data in clinic services, billing, pharmacy, and cashier for clean simulation.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");

        $tables = [
            'fee_transactions',
            'cash_transactions',
            'billing_details',
            'billing_transactions',
            'prescription_details',
            'prescriptions',
            'medical_records',
            'triage_records',
            'queue_numbers',
            'patient_visits',
            'journal_entry_details',
            'journal_entries'
        ];

        foreach ($tables as $table) {
            $db->query("TRUNCATE TABLE {$table};");
            CLI::write("Truncated table: {$table}", 'green');
        }

        // Reset cash register balance
        $db->query("UPDATE cash_registers SET balance = 0 WHERE id = 1;");

        // Reset medicine batches stock to 100 for clean testing
        $db->query("UPDATE medicine_batches SET stock = 100;");

        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        CLI::write("===================================================", 'yellow');
        CLI::write("SUCCESS: Data transaksi Pelayanan Klinik telah di-reset!", 'green');
        CLI::write("Master Data (Dokter, Tindakan, Poli, Obat) tetap tersimpan utuh.", 'white');
        CLI::write("===================================================", 'yellow');
    }
}
