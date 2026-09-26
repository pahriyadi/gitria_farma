<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradePharmacyFeaturesAndCoa extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');

        // 1. Table pharmacy_pending_prescriptions (Pending Resep / Hold Cart)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pending_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Pelanggan Umum',
            ],
            'customer_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'visit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'source_type' => [
                'type'       => 'ENUM',
                'constraint' => ['otc', 'resep_dokter', 'kasir_klinik'],
                'default'    => 'otc',
            ],
            'payload_json' => [
                'type' => 'LONGTEXT',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'tusla_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'embalase_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'resumed', 'cancelled'],
                'default'    => 'pending',
            ],
            'cashier_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pending_no');
        $this->forge->addKey('status');
        $this->forge->createTable('pharmacy_pending_prescriptions', true);

        // 2. Add columns to pharmacy_sales if not exist
        $colsSales = $db->getFieldNames('pharmacy_sales');
        if (!in_array('tusla_amount', $colsSales)) {
            $db->query("ALTER TABLE pharmacy_sales ADD COLUMN tusla_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER discount_amount;");
        }
        if (!in_array('embalase_amount', $colsSales)) {
            $db->query("ALTER TABLE pharmacy_sales ADD COLUMN embalase_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER tusla_amount;");
        }
        if (!in_array('doctor_id', $colsSales)) {
            $db->query("ALTER TABLE pharmacy_sales ADD COLUMN doctor_id INT(11) NULL AFTER cashier_id;");
        }
        if (!in_array('prescription_type', $colsSales)) {
            $db->query("ALTER TABLE pharmacy_sales ADD COLUMN prescription_type ENUM('bebas','resep','racikan') NOT NULL DEFAULT 'bebas' AFTER sale_no;");
        }

        // 3. Add columns to pharmacy_sale_details if not exist
        $colsSaleDetails = $db->getFieldNames('pharmacy_sale_details');
        if (!in_array('is_racikan', $colsSaleDetails)) {
            $db->query("ALTER TABLE pharmacy_sale_details ADD COLUMN is_racikan TINYINT(1) NOT NULL DEFAULT 0 AFTER medicine_id;");
        }
        if (!in_array('racikan_name', $colsSaleDetails)) {
            $db->query("ALTER TABLE pharmacy_sale_details ADD COLUMN racikan_name VARCHAR(150) NULL AFTER is_racikan;");
        }
        if (!in_array('racikan_group', $colsSaleDetails)) {
            $db->query("ALTER TABLE pharmacy_sale_details ADD COLUMN racikan_group VARCHAR(50) NULL AFTER racikan_name;");
        }
        if (!in_array('tusla', $colsSaleDetails)) {
            $db->query("ALTER TABLE pharmacy_sale_details ADD COLUMN tusla DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER discount;");
        }
        if (!in_array('embalase', $colsSaleDetails)) {
            $db->query("ALTER TABLE pharmacy_sale_details ADD COLUMN embalase DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER tusla;");
        }

        // 4. Add columns to prescriptions & prescription_details
        $colsPresc = $db->getFieldNames('prescriptions');
        if (!in_array('tusla_amount', $colsPresc)) {
            $db->query("ALTER TABLE prescriptions ADD COLUMN tusla_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER doctor_id;");
        }
        if (!in_array('embalase_amount', $colsPresc)) {
            $db->query("ALTER TABLE prescriptions ADD COLUMN embalase_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER tusla_amount;");
        }

        $colsPrescDetails = $db->getFieldNames('prescription_details');
        if (!in_array('is_racikan', $colsPrescDetails)) {
            $db->query("ALTER TABLE prescription_details ADD COLUMN is_racikan TINYINT(1) NOT NULL DEFAULT 0 AFTER medicine_id;");
        }
        if (!in_array('racikan_name', $colsPrescDetails)) {
            $db->query("ALTER TABLE prescription_details ADD COLUMN racikan_name VARCHAR(150) NULL AFTER is_racikan;");
        }
        if (!in_array('racikan_group', $colsPrescDetails)) {
            $db->query("ALTER TABLE prescription_details ADD COLUMN racikan_group VARCHAR(50) NULL AFTER racikan_name;");
        }

        // 5. Add columns to billing_details
        $colsBillingDetails = $db->getFieldNames('billing_details');
        if (!in_array('is_racikan', $colsBillingDetails)) {
            $db->query("ALTER TABLE billing_details ADD COLUMN is_racikan TINYINT(1) NOT NULL DEFAULT 0 AFTER item_type;");
        }
        if (!in_array('racikan_name', $colsBillingDetails)) {
            $db->query("ALTER TABLE billing_details ADD COLUMN racikan_name VARCHAR(150) NULL AFTER is_racikan;");
        }
        if (!in_array('parent_racikan_id', $colsBillingDetails)) {
            $db->query("ALTER TABLE billing_details ADD COLUMN parent_racikan_id INT(11) NULL AFTER racikan_name;");
        }

        // 6. Add prescription_fee_percent to doctors
        $colsDoctors = $db->getFieldNames('doctors');
        if (!in_array('prescription_fee_percent', $colsDoctors)) {
            $db->query("ALTER TABLE doctors ADD COLUMN prescription_fee_percent DECIMAL(5,2) NOT NULL DEFAULT 5.00 AFTER fee_per_pasien;");
        }

        // 7. Seed / Update COA Apotek in accounts table
        $newAccounts = [
            // Kas & Bank Apotek
            ['code' => '111', 'name' => 'Kas Tunai Apotek', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1111', 'name' => 'Kas Kasir 1', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1112', 'name' => 'Kas Kasir 2', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '112', 'name' => 'Kas Bank Apotek', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1121', 'name' => 'Kas Digital (QRIS/Transfer)', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            // Piutang
            ['code' => '121', 'name' => 'Piutang Asuransi BPJS', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '122', 'name' => 'Piutang Karyawan', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '123', 'name' => 'Cadangan Kerugian Piutang', 'type' => 'asset', 'normal_balance' => 'credit', 'balance' => 0.00],
            // Persediaan
            ['code' => '1411', 'name' => 'Persediaan Obat Bebas', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1412', 'name' => 'Persediaan Obat Bebas Terbatas', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1413', 'name' => 'Persediaan Obat Keras', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1414', 'name' => 'Persediaan Obat Generik', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1415', 'name' => 'Persediaan Obat Paten', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1421', 'name' => 'Persediaan Alkes & BMHP', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '1431', 'name' => 'Kemasan & Embalase (Kapsul/Botol/Klip)', 'type' => 'asset', 'normal_balance' => 'debit', 'balance' => 0.00],
            // Utang
            ['code' => '211', 'name' => 'Utang Supplier PBF / Vendor Obat', 'type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '231', 'name' => 'Utang Pajak', 'type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '241', 'name' => 'Utang Fee Dokter Resep', 'type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '242', 'name' => 'Utang Jasa Medis Dokter', 'type' => 'liability', 'normal_balance' => 'credit', 'balance' => 0.00],
            // Pendapatan
            ['code' => '411', 'name' => 'Pendapatan Penjualan Obat Resep', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '412', 'name' => 'Pendapatan Penjualan Obat Bebas', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '413', 'name' => 'Pendapatan Penjualan Alkes', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '421', 'name' => 'Pendapatan Konsul Online', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '422', 'name' => 'Pendapatan Tusla (Jasa Racik)', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '423', 'name' => 'Pendapatan Embalase (Kemasan)', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            ['code' => '424', 'name' => 'Pendapatan Jasa Dokter', 'type' => 'revenue', 'normal_balance' => 'credit', 'balance' => 0.00],
            // Beban Pokok & Bagi Hasil / Alokasi
            ['code' => '511', 'name' => 'Obat (Untuk Pembelian Obat Lagi)', 'type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '512', 'name' => 'Penunjang', 'type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '513', 'name' => 'Obat Resep', 'type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0.00],
            ['code' => '514', 'name' => 'ADM (Administrasi)', 'type' => 'expense', 'normal_balance' => 'debit', 'balance' => 0.00],
        ];

        foreach ($newAccounts as $acc) {
            $exists = $db->table('accounts')->where('code', $acc['code'])->get()->getRow();
            if (!$exists) {
                $db->table('accounts')->insert($acc);
            } else {
                $db->table('accounts')->where('code', $acc['code'])->update([
                    'name'           => $acc['name'],
                    'type'           => $acc['type'],
                    'normal_balance' => $acc['normal_balance'],
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('pharmacy_pending_prescriptions', true);
    }
}
