<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeCashTransactionsSystem extends Migration
{
    public function up()
    {
        $fieldsCash = $this->db->getFieldNames('cash_transactions');
        
        if (!in_array('paid_amount', $fieldsCash)) {
            $this->db->query("ALTER TABLE cash_transactions ADD COLUMN paid_amount DECIMAL(15,2) DEFAULT 0.00 AFTER amount;");
        }
        if (!in_array('change_amount', $fieldsCash)) {
            $this->db->query("ALTER TABLE cash_transactions ADD COLUMN change_amount DECIMAL(15,2) DEFAULT 0.00 AFTER paid_amount;");
        }
        if (!in_array('cashier_id', $fieldsCash)) {
            $this->db->query("ALTER TABLE cash_transactions ADD COLUMN cashier_id INT NULL AFTER change_amount;");
        }
        if (!in_array('notes', $fieldsCash)) {
            $this->db->query("ALTER TABLE cash_transactions ADD COLUMN notes TEXT NULL AFTER cashier_id;");
        }

        // Table for Internal Cash & Bank Transfers (Setor/Tarik Kasir)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS internal_cash_transfers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transfer_no VARCHAR(30) NOT NULL UNIQUE,
                from_account_id INT NOT NULL,
                to_account_id INT NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                transfer_date DATE NOT NULL,
                description TEXT NULL,
                created_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down()
    {
        $this->forge->dropTable('internal_cash_transfers', true);
    }
}
