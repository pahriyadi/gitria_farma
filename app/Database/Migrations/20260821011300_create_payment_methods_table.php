<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Tabel master payment_methods
        $this->db->query("
            CREATE TABLE IF NOT EXISTS payment_methods (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(50) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                category ENUM('cash', 'qris', 'transfer', 'debit', 'credit', 'insurance') NOT NULL DEFAULT 'cash',
                account_number VARCHAR(100) NULL,
                account_name VARCHAR(150) NULL,
                notes VARCHAR(255) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_active (is_active),
                INDEX idx_category (category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Insert Default Master Payment Methods
        $now = date('Y-m-d H:i:s');
        $defaults = [
            [
                'code'           => 'tunai',
                'name'           => 'Tunai / Cash',
                'category'       => 'cash',
                'account_number' => null,
                'account_name'   => 'Kasir Utama',
                'notes'          => 'Pembayaran tunai langsung di kasir',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'qris',
                'name'           => 'QRIS Dinamis / Statis',
                'category'       => 'qris',
                'account_number' => 'NMID: ID1020030040050',
                'account_name'   => 'Sawamawa Medical Center',
                'notes'          => 'Scan QRIS BCA/Mandiri/GoPay/OVO/ShopeePay/DANA',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'transfer_bca',
                'name'           => 'Transfer Bank BCA',
                'category'       => 'transfer',
                'account_number' => '8920192831',
                'account_name'   => 'PT Sawamawa Medical Center',
                'notes'          => 'Rekening Operasional BCA',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'transfer_bri',
                'name'           => 'Transfer Bank BRI',
                'category'       => 'transfer',
                'account_number' => '012901002345501',
                'account_name'   => 'Sawamawa Medical Center',
                'notes'          => 'Rekening Penerimaan BRI',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'transfer_mandiri',
                'name'           => 'Transfer Bank Mandiri',
                'category'       => 'transfer',
                'account_number' => '1610009876543',
                'account_name'   => 'Sawamawa Medical Center',
                'notes'          => 'Rekening Bank Mandiri',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'debit',
                'name'           => 'Kartu Debit (EDC)',
                'category'       => 'debit',
                'account_number' => 'EDC BCA / Mandiri / BRI',
                'account_name'   => 'Mesin EDC Kasir',
                'notes'          => 'Gesek kartu debit semua bank via EDC',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'credit',
                'name'           => 'Kartu Kredit (Visa / Mastercard)',
                'category'       => 'credit',
                'account_number' => 'EDC Kartu Kredit',
                'account_name'   => 'Mesin EDC Kasir',
                'notes'          => 'Pembayaran Kartu Kredit Visa/Mastercard/JCB',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'bpjs',
                'name'           => 'BPJS Kesehatan',
                'category'       => 'insurance',
                'account_number' => 'PKS BPJS Kesehatan',
                'account_name'   => 'Klaim BPJS',
                'notes'          => 'Jaminan Pelayanan Peserta BPJS Kesehatan',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ],
            [
                'code'           => 'asuransi',
                'name'           => 'Asuransi Swasta / Rekanan',
                'category'       => 'insurance',
                'account_number' => 'Klaim Asuransi Swasta',
                'account_name'   => 'Rekanan Asuransi',
                'notes'          => 'Prudential, Allianz, Inhealth, Mandiri AXA, dll',
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now
            ]
        ];

        foreach ($defaults as $d) {
            $exists = $this->db->table('payment_methods')->where('code', $d['code'])->get()->getRow();
            if (!$exists) {
                $this->db->table('payment_methods')->insert($d);
            }
        }

        // Alter payment_method columns in cash_transactions & billing_transactions & patient_visits to VARCHAR(50)
        $this->db->query("ALTER TABLE cash_transactions MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'tunai';");
        
        // Ensure payment_method exists in billing_transactions
        $col = $this->db->query("SHOW COLUMNS FROM billing_transactions LIKE 'payment_method'")->getRow();
        if (!$col) {
            $this->db->query("ALTER TABLE billing_transactions ADD COLUMN payment_method VARCHAR(50) NULL;");
        } else {
            $this->db->query("ALTER TABLE billing_transactions MODIFY COLUMN payment_method VARCHAR(50) NULL;");
        }

        $this->db->query("ALTER TABLE patient_visits MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'umum';");
        $this->db->query("ALTER TABLE pharmacy_sales MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'tunai';");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS payment_methods;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
