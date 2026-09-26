<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterIcdTables extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Master ICD-10 (Katalog Diagnosa Penyakit)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS master_icd10 (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name_id VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NULL,
                category VARCHAR(150) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Master ICD-9-CM (Katalog Prosedur & Tindakan Medis)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS master_icd9 (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name_id VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NULL,
                category VARCHAR(150) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS master_icd10;");
        $this->db->query("DROP TABLE IF EXISTS master_icd9;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
