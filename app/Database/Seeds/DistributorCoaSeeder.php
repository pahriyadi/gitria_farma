<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DistributorCoaSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            ['code' => '113', 'name' => 'Kas Distributor / Grosir', 'type' => 'Asset', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '1131', 'name' => 'Kas Tunai Distributor', 'type' => 'Asset', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '1132', 'name' => 'Bank Penerimaan Distributor', 'type' => 'Asset', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '124', 'name' => 'Piutang Usaha Grosir (B2B)', 'type' => 'Asset', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '1410', 'name' => 'Persediaan Obat Grosir (Distributor)', 'type' => 'Asset', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '4110', 'name' => 'Pendapatan Penjualan Grosir (Distributor)', 'type' => 'Revenue', 'normal_balance' => 'credit', 'balance' => 0],
            ['code' => '4310', 'name' => 'Potongan Penjualan / Diskon Grosir', 'type' => 'Revenue', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '4320', 'name' => 'Retur Penjualan Grosir', 'type' => 'Revenue', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '5110', 'name' => 'HPP Penjualan Grosir (Distributor)', 'type' => 'Expense', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '566', 'name' => 'Beban Ekspedisi & Logistik Grosir', 'type' => 'Expense', 'normal_balance' => 'debit', 'balance' => 0],
            ['code' => '5331', 'name' => 'Insentif & Komisi Sales Grosir', 'type' => 'Expense', 'normal_balance' => 'debit', 'balance' => 0],
        ];

        foreach ($accounts as $acc) {
            $exists = $this->db->table('accounts')->where('code', $acc['code'])->get()->getRow();
            if (!$exists) {
                $this->db->table('accounts')->insert($acc);
            } else {
                $this->db->table('accounts')->where('code', $acc['code'])->update([
                    'name'           => $acc['name'],
                    'type'           => $acc['type'],
                    'normal_balance' => $acc['normal_balance']
                ]);
            }
        }
    }
}
