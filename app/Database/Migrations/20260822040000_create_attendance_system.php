<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceSystem extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. work_shifts (Master Shift Kerja)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS work_shifts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                shift_code VARCHAR(20) NOT NULL UNIQUE,
                shift_name VARCHAR(100) NOT NULL,
                start_time TIME NOT NULL,
                end_time TIME NOT NULL,
                late_tolerance_minutes INT DEFAULT 15,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. employee_attendances (Data Presensi / Absensi Harian)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS employee_attendances (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                shift_id INT NULL,
                date DATE NOT NULL,
                check_in_time TIME NULL,
                check_out_time TIME NULL,
                status ENUM('present', 'late', 'sick', 'permit', 'leave', 'alpha') DEFAULT 'present',
                late_minutes INT DEFAULT 0,
                overtime_minutes INT DEFAULT 0,
                notes TEXT NULL,
                ip_address VARCHAR(45) NULL,
                device_info VARCHAR(150) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (shift_id) REFERENCES work_shifts(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. employee_leaves (Pengajuan Cuti / Izin / Sakit)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS employee_leaves (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                leave_type ENUM('cuti_tahunan', 'sakit', 'izin', 'cuti_melahirkan', 'dinas_luar') DEFAULT 'izin',
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                total_days INT DEFAULT 1,
                reason TEXT NOT NULL,
                attachment_doc VARCHAR(255) NULL,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                approved_by INT NULL,
                approval_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed default shifts if table is empty
        $count = $this->db->table('work_shifts')->countAllResults();
        if ($count == 0) {
            $defaultShifts = [
                ['shift_code' => 'SHIFT-PAGI', 'shift_name' => 'Shift Pagi (Pelayanan/Poli)', 'start_time' => '07:00:00', 'end_time' => '14:00:00', 'late_tolerance_minutes' => 15, 'status' => 'active'],
                ['shift_code' => 'SHIFT-SIANG', 'shift_name' => 'Shift Siang (Pelayanan/Apotek)', 'start_time' => '14:00:00', 'end_time' => '21:00:00', 'late_tolerance_minutes' => 15, 'status' => 'active'],
                ['shift_code' => 'SHIFT-MALAM', 'shift_name' => 'Shift Malam (Jaga/UGD/Rawat)', 'start_time' => '21:00:00', 'end_time' => '07:00:00', 'late_tolerance_minutes' => 15, 'status' => 'active'],
                ['shift_code' => 'SHIFT-REGULER', 'shift_name' => 'Shift Normal (Manajemen & Kantor)', 'start_time' => '08:00:00', 'end_time' => '16:00:00', 'late_tolerance_minutes' => 15, 'status' => 'active']
            ];
            $this->db->table('work_shifts')->insertBatch($defaultShifts);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS employee_leaves;");
        $this->db->query("DROP TABLE IF EXISTS employee_attendances;");
        $this->db->query("DROP TABLE IF EXISTS work_shifts;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
