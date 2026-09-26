<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterLabResultsAndMedicalLetters extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Recreate / Alter lab_results to match modern clinical requirements
        $this->db->query("DROP TABLE IF EXISTS lab_results;");

        $this->db->query("
            CREATE TABLE lab_results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                lab_no VARCHAR(50) NOT NULL UNIQUE,
                visit_id INT NOT NULL,
                patient_id INT NOT NULL,
                doctor_id INT NULL,
                officer_name VARCHAR(100) DEFAULT 'Analis Laboratorium',
                test_date DATE NOT NULL,
                test_type VARCHAR(150) NOT NULL,
                result_value VARCHAR(100) NOT NULL,
                normal_range VARCHAR(100) NULL,
                unit VARCHAR(50) NULL,
                status ENUM('normal', 'high', 'low', 'abnormal') DEFAULT 'normal',
                notes TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                INDEX idx_lab_visit (visit_id),
                INDEX idx_lab_patient (patient_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        // No-op
    }
}
