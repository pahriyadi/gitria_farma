<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesPolikliniksTindakan extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // Tabel categories (Kategori Utama)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tabel polikliniks (Terpisah dari polyclinics lama)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS polikliniks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                name VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Tabel tindakan (Tindakan & Sub-tindakan dengan parent_id)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS tindakan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NULL,
                parent_id INT NULL,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(150) NOT NULL,
                description VARCHAR(255) NULL,
                price DECIMAL(15,2) DEFAULT 0.00,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
                FOREIGN KEY (parent_id) REFERENCES tindakan(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS tindakan;");
        $this->db->query("DROP TABLE IF EXISTS polikliniks;");
        $this->db->query("DROP TABLE IF EXISTS categories;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
