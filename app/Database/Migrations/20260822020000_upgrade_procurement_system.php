<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeProcurementSystem extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Add extra columns to suppliers if not exists
        if ($this->db->tableExists('suppliers')) {
            $fields = $this->db->getFieldNames('suppliers');
            if (!in_array('pic_name', $fields)) {
                $this->db->query("ALTER TABLE suppliers ADD COLUMN pic_name VARCHAR(100) NULL AFTER phone;");
            }
            if (!in_array('email', $fields)) {
                $this->db->query("ALTER TABLE suppliers ADD COLUMN email VARCHAR(100) NULL AFTER pic_name;");
            }
            if (!in_array('status', $fields)) {
                $this->db->query("ALTER TABLE suppliers ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER bank_account;");
            }
        }

        // 2. Add extra columns to purchase_requests if not exists
        if ($this->db->tableExists('purchase_requests')) {
            $fields = $this->db->getFieldNames('purchase_requests');
            if (!in_array('department', $fields)) {
                $this->db->query("ALTER TABLE purchase_requests ADD COLUMN department VARCHAR(50) DEFAULT 'Apotek Farmasi' AFTER supplier_id;");
            }
            if (!in_array('requested_by', $fields)) {
                $this->db->query("ALTER TABLE purchase_requests ADD COLUMN requested_by INT NULL AFTER department;");
            }
            if (!in_array('description', $fields)) {
                $this->db->query("ALTER TABLE purchase_requests ADD COLUMN description TEXT NULL AFTER total_amount;");
            }
        }

        // 3. Add extra columns to purchase_orders if not exists
        if ($this->db->tableExists('purchase_orders')) {
            $fields = $this->db->getFieldNames('purchase_orders');
            if (!in_array('order_date', $fields)) {
                $this->db->query("ALTER TABLE purchase_orders ADD COLUMN order_date DATE NULL AFTER supplier_id;");
            }
            if (!in_array('payment_terms', $fields)) {
                $this->db->query("ALTER TABLE purchase_orders ADD COLUMN payment_terms VARCHAR(50) DEFAULT 'Net 30 Hari' AFTER total_amount;");
            }
            if (!in_array('notes', $fields)) {
                $this->db->query("ALTER TABLE purchase_orders ADD COLUMN notes TEXT NULL AFTER payment_terms;");
            }
        }

        // 4. Add extra columns to goods_receipts if not exists
        if ($this->db->tableExists('goods_receipts')) {
            $fields = $this->db->getFieldNames('goods_receipts');
            if (!in_array('delivery_order_no', $fields)) {
                $this->db->query("ALTER TABLE goods_receipts ADD COLUMN delivery_order_no VARCHAR(50) NULL AFTER purchase_order_id;");
            }
            if (!in_array('receiver_id', $fields)) {
                $this->db->query("ALTER TABLE goods_receipts ADD COLUMN receiver_id INT NULL AFTER received_date;");
            }
            if (!in_array('total_amount', $fields)) {
                $this->db->query("ALTER TABLE goods_receipts ADD COLUMN total_amount DECIMAL(15,2) DEFAULT 0.00 AFTER receiver_id;");
            }
            if (!in_array('notes', $fields)) {
                $this->db->query("ALTER TABLE goods_receipts ADD COLUMN notes TEXT NULL AFTER total_amount;");
            }
        }

        // 5. Create purchase_request_items
        $this->db->query("
            CREATE TABLE IF NOT EXISTS purchase_request_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_request_id INT NOT NULL,
                item_type ENUM('obat', 'alkes', 'bahan_resto', 'inventaris') DEFAULT 'obat',
                item_id INT NULL,
                item_name VARCHAR(150) NOT NULL,
                qty INT NOT NULL DEFAULT 1,
                unit VARCHAR(30) NOT NULL DEFAULT 'Pcs',
                estimated_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 6. Create purchase_order_items
        $this->db->query("
            CREATE TABLE IF NOT EXISTS purchase_order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id INT NOT NULL,
                item_type ENUM('obat', 'alkes', 'bahan_resto', 'inventaris') DEFAULT 'obat',
                item_id INT NULL,
                item_name VARCHAR(150) NOT NULL,
                qty INT NOT NULL DEFAULT 1,
                unit VARCHAR(30) NOT NULL DEFAULT 'Pcs',
                price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 7. Create goods_receipt_items
        $this->db->query("
            CREATE TABLE IF NOT EXISTS goods_receipt_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                goods_receipt_id INT NOT NULL,
                medicine_id INT NULL,
                batch_id INT NULL,
                item_name VARCHAR(150) NOT NULL,
                batch_no VARCHAR(50) NOT NULL,
                expired_date DATE NOT NULL,
                qty_received INT NOT NULL DEFAULT 1,
                unit VARCHAR(30) DEFAULT 'Pcs',
                buy_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (goods_receipt_id) REFERENCES goods_receipts(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS goods_receipt_items;");
        $this->db->query("DROP TABLE IF EXISTS purchase_order_items;");
        $this->db->query("DROP TABLE IF EXISTS purchase_request_items;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
