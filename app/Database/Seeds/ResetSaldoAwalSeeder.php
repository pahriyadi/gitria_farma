<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResetSaldoAwalSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');
        
        $oldOpeningJournals = $db->table('journal_entries')->where('source_module', 'Saldo Awal')->get()->getResult();
        if (!empty($oldOpeningJournals)) {
            $oldJIds = array_column($oldOpeningJournals, 'id');
            $db->table('journal_entry_details')->whereIn('journal_id', $oldJIds)->delete();
            $db->table('journal_entries')->where('source_module', 'Saldo Awal')->delete();
        }

        $db->table('accounts')->update(['balance' => 0.00]);
        if ($db->tableExists('cash_registers')) {
            $db->table('cash_registers')->update(['balance' => 0.00]);
        }

        echo "Seluruh Saldo Awal dan jurnal pembukuan awal lama telah berhasil dibersihkan kembali ke Rp 0,00.\n";
    }
}
