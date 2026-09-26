<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeFeeRuleIdNullableInFeeTransactions extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Make fee_rule_id nullable in fee_transactions
        $this->db->query("ALTER TABLE fee_transactions MODIFY fee_rule_id INT NULL;");

        // 2. Ensure default fee_rules exists
        $feeRuleCount = $this->db->table('fee_rules')->countAllResults();
        if ($feeRuleCount === 0) {
            $this->db->table('fee_rules')->insert([
                'id'         => 1,
                'service_id' => 1,
                'role_id'    => null,
                'percentage' => 0.00,
                'flat_fee'   => 0.00,
                'status'     => 'active'
            ]);
        }

        // 3. Ensure transaction_account_mappings exist
        $mappings = [
            ['transaction_type' => 'CLINIC_PAYMENT', 'debit_account_id' => 1, 'credit_account_id' => 9],
            ['transaction_type' => 'PHARMACY_SALE',  'debit_account_id' => 1, 'credit_account_id' => 10],
            ['transaction_type' => 'RESTO_SALE',     'debit_account_id' => 1, 'credit_account_id' => 11],
            ['transaction_type' => 'FEE_EXPENSE',    'debit_account_id' => 14, 'credit_account_id' => 7]
        ];
        foreach ($mappings as $m) {
            $exists = $this->db->table('transaction_account_mappings')->where('transaction_type', $m['transaction_type'])->get()->getRow();
            if (!$exists) {
                $this->db->table('transaction_account_mappings')->insert($m);
            }
        }

        // 4. Ensure accounts exist
        $accounts = [
            ['id' => 1, 'code' => '1-101', 'name' => 'Kas Kasir Utama', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['id' => 7, 'code' => '2-102', 'name' => 'Hutang Komisi Dokter', 'type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 9, 'code' => '4-101', 'name' => 'Pendapatan Pelayanan Klinik', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 10, 'code' => '4-102', 'name' => 'Pendapatan Apotek Farmasi', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 11, 'code' => '4-103', 'name' => 'Pendapatan POS Restoran', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 14, 'code' => '6-102', 'name' => 'Beban Komisi & Jasa Medis Dokter', 'type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0.00]
        ];
        foreach ($accounts as $acc) {
            $exists = $this->db->table('accounts')->where('id', $acc['id'])->get()->getRow();
            if (!$exists) {
                $this->db->table('accounts')->insert($acc);
            }
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        // No down needed
    }
}
