<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdaptDoctorAndReferenceStructure extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Ensure categories table exists & matches
        $this->db->query("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Ensure polikliniks table exists
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

        // 3. Ensure tindakan table exists
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

        // 4. Adapt doctors table to have category_id, tindakan_id, fee_per_pasien, and allow polyclinic_id to be nullable
        $doctorFields = $this->db->getFieldNames('doctors');
        
        $this->db->query("ALTER TABLE doctors MODIFY COLUMN polyclinic_id INT NULL;");

        if (!in_array('category_id', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors ADD COLUMN category_id INT NULL AFTER name;");
            $this->db->query("ALTER TABLE doctors ADD CONSTRAINT fk_doc_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE;");
        }

        if (!in_array('tindakan_id', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors ADD COLUMN tindakan_id INT NULL AFTER polyclinic_id;");
            $this->db->query("ALTER TABLE doctors ADD CONSTRAINT fk_doc_tindakan FOREIGN KEY (tindakan_id) REFERENCES tindakan(id) ON DELETE SET NULL ON UPDATE CASCADE;");
        }

        if (!in_array('fee_per_pasien', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors ADD COLUMN fee_per_pasien DECIMAL(15,2) DEFAULT 0.00 AFTER tindakan_id;");
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $doctorFields = $this->db->getFieldNames('doctors');
        if (in_array('fee_per_pasien', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors DROP COLUMN fee_per_pasien;");
        }
        if (in_array('tindakan_id', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors DROP FOREIGN KEY fk_doc_tindakan;");
            $this->db->query("ALTER TABLE doctors DROP COLUMN tindakan_id;");
        }
        if (in_array('category_id', $doctorFields)) {
            $this->db->query("ALTER TABLE doctors DROP FOREIGN KEY fk_doc_category;");
            $this->db->query("ALTER TABLE doctors DROP COLUMN category_id;");
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
