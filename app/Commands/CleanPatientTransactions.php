<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanPatientTransactions extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'clinic:clean-patient-data';
    protected $description = 'Clean all old patient visits, queues, billings, prescriptions, and journals for fresh testing';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write("=================================================================", 'yellow');
        CLI::write("=== MEMBERSIHKAN DATA TRANSAKSI KUNJUNGAN PASIEN LAMA ===", 'yellow');
        CLI::write("=================================================================", 'yellow');

        $tablesToClean = [
            'queue_call_events',
            'queue_numbers',
            'triage_records',
            'medical_records',
            'prescription_details',
            'prescriptions',
            'billing_details',
            'billing_transactions',
            'cash_transactions',
            'pharmacy_sale_details',
            'pharmacy_sales',
            'journal_entry_details',
            'journal_entries',
            'patient_visits'
        ];

        $db->query('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tablesToClean as $table) {
            if ($db->tableExists($table)) {
                $countBefore = $db->table($table)->countAllResults();
                $db->table($table)->truncate();
                CLI::write("  [OK] Tabel '{$table}' dibersihkan (sebelumnya {$countBefore} baris).", 'green');
            }
        }

        // Reset account balances to 0.00 for clean trial balance
        $db->table('accounts')->update(['balance' => 0.00]);
        CLI::write("  [OK] Saldo seluruh rekening COA (accounts.balance) di-reset ke 0.00.", 'green');

        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        CLI::write("\n>>> SELURUH DATA TRANSAKSI LAMA BERHASIL DIBERSIHKAN! <<<", 'green');
        CLI::write("Master Data (Pasien, Dokter, Obat/Stok, Template Aturan Jurnal, Akun COA) tetap aman.", 'cyan');
        CLI::write("Sistem siap untuk pengujian alur pendaftaran dan transaksi pasien baru.", 'cyan');
    }
}
