<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePharmacyDirectSalesTable extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Tabel pharmacy_sales (Penjualan Obat Bebas / Non-Resep Walk-in)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS pharmacy_sales (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sale_no VARCHAR(50) NOT NULL UNIQUE,
                customer_name VARCHAR(150) NOT NULL DEFAULT 'Pelanggan Umum',
                customer_phone VARCHAR(50) NULL,
                sale_date DATE NOT NULL,
                total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                grand_total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                payment_method ENUM('tunai', 'debit', 'transfer', 'qris') NOT NULL DEFAULT 'tunai',
                paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                change_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                cashier_id INT NULL,
                notes TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_sale_date (sale_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Tabel pharmacy_sale_details (Detail Item Obat Bebas)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS pharmacy_sale_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sale_id INT NOT NULL,
                medicine_id INT NOT NULL,
                batch_id INT NULL,
                qty INT NOT NULL DEFAULT 1,
                price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                discount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                dosage_instruction VARCHAR(255) NULL,
                created_at DATETIME NULL,
                INDEX idx_sale_ref (sale_id),
                INDEX idx_med_ref (medicine_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS pharmacy_sale_details;");
        $this->db->query("DROP TABLE IF EXISTS pharmacy_sales;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
