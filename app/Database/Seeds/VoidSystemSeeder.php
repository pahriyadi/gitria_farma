<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VoidSystemSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');

        // 1. Create table void_audit_logs if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS `void_audit_logs` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              `void_number` VARCHAR(64) NOT NULL UNIQUE,
              `transaction_type` ENUM('billing_klinik', 'pharmacy_sale', 'distributor_sale', 'receivable_payment', 'cash_expense', 'cash_transfer', 'resto_sale', 'doctor_fee', 'goods_receipt') NOT NULL,
              `reference_id` BIGINT UNSIGNED NOT NULL,
              `reference_number` VARCHAR(64) NOT NULL,
              `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
              `payment_method` VARCHAR(32) NOT NULL DEFAULT 'tunai',
              `cash_register_id` INT UNSIGNED NULL,
              `reversal_journal_id` BIGINT UNSIGNED NULL,
              `reason_category` VARCHAR(100) NOT NULL,
              `reason_detail` TEXT NOT NULL,
              `cashier_user_id` INT UNSIGNED NOT NULL,
              `supervisor_user_id` INT UNSIGNED NOT NULL,
              `ip_address` VARCHAR(45) NOT NULL,
              `user_agent` VARCHAR(255) NULL,
              `item_snapshot_json` LONGTEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `idx_void_trx` (`transaction_type`, `reference_id`),
              INDEX `idx_void_created` (`created_at`),
              INDEX `idx_void_supervisor` (`supervisor_user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Create table void_reasons_master if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS `void_reasons_master` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `category_name` VARCHAR(100) NOT NULL,
              `module` ENUM('all', 'klinik', 'apotek', 'distributor', 'resto', 'keuangan') NOT NULL DEFAULT 'all',
              `is_active` TINYINT(1) NOT NULL DEFAULT 1,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Seed default reasons if empty
        $reasonsCount = $db->table('void_reasons_master')->countAllResults();
        if ($reasonsCount === 0) {
            $defaultReasons = [
                ['category_name' => 'Salah Input Jumlah / Kuantitas Barang', 'module' => 'all'],
                ['category_name' => 'Salah Pilih Nama Obat / Tindakan Medis', 'module' => 'all'],
                ['category_name' => 'Pasien Batal Berobat / Pindah Faskes', 'module' => 'klinik'],
                ['category_name' => 'Dokter Merevisi Resep / Dosis Obat', 'module' => 'apotek'],
                ['category_name' => 'Salah Memilih Metode Pembayaran (Tunai / QRIS / Transfer)', 'module' => 'keuangan'],
                ['category_name' => 'Pelanggan Grosir Mengubah Surat Pesanan', 'module' => 'distributor'],
                ['category_name' => 'Transaksi Duplikat / Double Input Kasir', 'module' => 'all'],
                ['category_name' => 'Uang Pasien / Kartu Tidak Mencukupi saat Pembayaran', 'module' => 'keuangan'],
                ['category_name' => 'Penyesuaian Administrasi Manajemen', 'module' => 'all']
            ];
            $db->table('void_reasons_master')->insertBatch($defaultReasons);
        }

        // 3. Create table supervisor_pins if not exists
        $db->query("
            CREATE TABLE IF NOT EXISTS `supervisor_pins` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `user_id` INT UNSIGNED NOT NULL UNIQUE,
              `pin_hash` VARCHAR(255) NOT NULL,
              `failed_attempts` TINYINT NOT NULL DEFAULT 0,
              `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
              `locked_until` DATETIME NULL,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Seed default PIN (123456) for admin / supervisor users if not exists
        $adminUsers = $db->table('users')
                         ->select('users.id')
                         ->join('roles', 'roles.id = users.role_id')
                         ->whereIn('roles.name', ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan', 'Manajer'])
                         ->get()
                         ->getResult();

        foreach ($adminUsers as $u) {
            $hasPin = $db->table('supervisor_pins')->where('user_id', $u->id)->countAllResults();
            if ($hasPin === 0) {
                $db->table('supervisor_pins')->insert([
                    'user_id'         => $u->id,
                    'pin_hash'        => password_hash('123456', PASSWORD_BCRYPT),
                    'failed_attempts' => 0,
                    'is_locked'       => 0
                ]);
            }
        }

        // 4. Safely add is_voided, void_log_id, voided_at columns to transaction tables
        $tablesToCheck = [
            'billing_transactions',
            'pharmacy_sales',
            'distributor_sales',
            'cash_transactions',
            'restaurant_orders',
            'doctor_fee_settlements'
        ];

        foreach ($tablesToCheck as $tbl) {
            try {
                if ($db->tableExists($tbl)) {
                    $fields = $db->getFieldNames($tbl);
                    if (!in_array('is_voided', $fields)) {
                        $db->query("ALTER TABLE `{$tbl}` ADD COLUMN `is_voided` TINYINT(1) NOT NULL DEFAULT 0;");
                    }
                    if (!in_array('void_log_id', $fields)) {
                        $db->query("ALTER TABLE `{$tbl}` ADD COLUMN `void_log_id` BIGINT UNSIGNED NULL;");
                    }
                    if (!in_array('voided_at', $fields)) {
                        $db->query("ALTER TABLE `{$tbl}` ADD COLUMN `voided_at` DATETIME NULL;");
                    }
                }
            } catch (\Throwable $e) {
                // Ignore alter errors if column already exists
            }
        }

        // 5. Add void permissions
        $perms = [
            ['name' => 'void.view', 'description' => 'Melihat data pusat log void pembatalan'],
            ['name' => 'void.authorize', 'description' => 'Mengotorisasi pembatalan transaksi dengan PIN supervisor'],
            ['name' => 'void.manage_pin', 'description' => 'Mengatur PIN supervisor untuk void transaksi']
        ];

        foreach ($perms as $p) {
            $exist = $db->table('permissions')->where('name', $p['name'])->get()->getRow();
            if (!$exist) {
                $db->table('permissions')->insert($p);
                $permId = $db->insertID();

                // Link to Super Admin, IT, Direksi, Kepala Klinik
                $targetRoles = $db->table('roles')->whereIn('name', ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan'])->get()->getResult();
                foreach ($targetRoles as $r) {
                    $db->table('role_permissions')->insert([
                        'role_id'       => $r->id,
                        'permission_id' => $permId
                    ]);
                }
            }
        }
    }
}
