<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockOpnamesTable extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. stock_opnames header
        $this->db->query("
            CREATE TABLE IF NOT EXISTS stock_opnames (
                id INT AUTO_INCREMENT PRIMARY KEY,
                opname_no VARCHAR(30) NOT NULL UNIQUE,
                opname_date DATE NOT NULL,
                user_id INT NOT NULL,
                status ENUM('draft', 'adjusted') DEFAULT 'adjusted',
                total_items INT DEFAULT 0,
                total_discrepancy INT DEFAULT 0,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. stock_opname_details
        $this->db->query("
            CREATE TABLE IF NOT EXISTS stock_opname_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                opname_id INT NOT NULL,
                medicine_id INT NOT NULL,
                batch_id INT NOT NULL,
                system_stock INT NOT NULL,
                physical_stock INT NOT NULL,
                difference INT NOT NULL,
                reason VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (opname_id) REFERENCES stock_opnames(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (batch_id) REFERENCES medicine_batches(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS stock_opname_details;");
        $this->db->query("DROP TABLE IF EXISTS stock_opnames;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
