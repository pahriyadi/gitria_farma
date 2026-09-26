<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeAssetsAndHrdSystem extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Upgrade inventory_assets table
        if ($this->db->tableExists('inventory_assets')) {
            $fields = $this->db->getFieldNames('inventory_assets');
            if (!in_array('useful_life_years', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN useful_life_years INT DEFAULT 4 AFTER price;");
            }
            if (!in_array('salvage_value', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN salvage_value DECIMAL(15,2) DEFAULT 0.00 AFTER useful_life_years;");
            }
            if (!in_array('current_value', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN current_value DECIMAL(15,2) NULL AFTER salvage_value;");
            }
            if (!in_array('brand', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN brand VARCHAR(100) NULL AFTER name;");
            }
            if (!in_array('last_maintenance_date', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN last_maintenance_date DATE NULL AFTER condition_status;");
            }
            if (!in_array('next_maintenance_date', $fields)) {
                $this->db->query("ALTER TABLE inventory_assets ADD COLUMN next_maintenance_date DATE NULL AFTER last_maintenance_date;");
            }
        }

        // 2. Upgrade asset_mutations
        if ($this->db->tableExists('asset_mutations')) {
            $fields = $this->db->getFieldNames('asset_mutations');
            if (!in_array('approved_by', $fields)) {
                $this->db->query("ALTER TABLE asset_mutations ADD COLUMN approved_by INT NULL AFTER notes;");
            }
        }

        // 3. Upgrade asset_depreciations
        if ($this->db->tableExists('asset_depreciations')) {
            $fields = $this->db->getFieldNames('asset_depreciations');
            if (!in_array('book_value_after', $fields)) {
                $this->db->query("ALTER TABLE asset_depreciations ADD COLUMN book_value_after DECIMAL(15,2) DEFAULT 0.00 AFTER amount;");
            }
            if (!in_array('journal_entry_id', $fields)) {
                $this->db->query("ALTER TABLE asset_depreciations ADD COLUMN journal_entry_id INT NULL AFTER book_value_after;");
            }
        }

        // 4. Create asset_maintenances (Pemeliharaan & Servis Aset)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS asset_maintenances (
                id INT AUTO_INCREMENT PRIMARY KEY,
                asset_id INT NOT NULL,
                service_date DATE NOT NULL,
                cost DECIMAL(15,2) DEFAULT 0.00,
                technician_vendor VARCHAR(100) NULL,
                description TEXT NOT NULL,
                status ENUM('scheduled', 'in_progress', 'completed') DEFAULT 'completed',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (asset_id) REFERENCES inventory_assets(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 5. Upgrade employees table
        if ($this->db->tableExists('employees')) {
            $fields = $this->db->getFieldNames('employees');
            if (!in_array('nik_ktp', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN nik_ktp VARCHAR(25) NULL AFTER nip;");
            }
            if (!in_array('position', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN position VARCHAR(100) NULL AFTER department;");
            }
            if (!in_array('phone', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN phone VARCHAR(20) NULL AFTER position;");
            }
            if (!in_array('email', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN email VARCHAR(100) NULL AFTER phone;");
            }
            if (!in_array('address', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN address TEXT NULL AFTER email;");
            }
            if (!in_array('join_date', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN join_date DATE NULL AFTER address;");
            }
            if (!in_array('bank_name', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN bank_name VARCHAR(50) NULL AFTER salary;");
            }
            if (!in_array('bank_account', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN bank_account VARCHAR(30) NULL AFTER bank_name;");
            }
            if (!in_array('employment_type', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN employment_type ENUM('tetap', 'kontrak', 'mitra', 'magang') DEFAULT 'tetap' AFTER bank_account;");
            }
            if (!in_array('allowance_position', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN allowance_position DECIMAL(15,2) DEFAULT 0.00 AFTER salary;");
            }
            if (!in_array('allowance_transport', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN allowance_transport DECIMAL(15,2) DEFAULT 0.00 AFTER allowance_position;");
            }
            if (!in_array('deduction_bpjs', $fields)) {
                $this->db->query("ALTER TABLE employees ADD COLUMN deduction_bpjs DECIMAL(15,2) DEFAULT 0.00 AFTER allowance_transport;");
            }
        }

        // 6. Create payrolls (Rekap Penggajian Bulanan)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS payrolls (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payroll_code VARCHAR(30) NOT NULL UNIQUE,
                period_month INT NOT NULL,
                period_year INT NOT NULL,
                payment_date DATE NOT NULL,
                payment_method ENUM('transfer', 'cash') DEFAULT 'transfer',
                bank_source_id INT NULL,
                total_gross DECIMAL(15,2) DEFAULT 0.00,
                total_deductions DECIMAL(15,2) DEFAULT 0.00,
                total_net_salary DECIMAL(15,2) DEFAULT 0.00,
                total_employees INT DEFAULT 0,
                status ENUM('draft', 'approved', 'paid') DEFAULT 'draft',
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 7. Create payroll_items (Rincian Slip Gaji Individu)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS payroll_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payroll_id INT NOT NULL,
                employee_id INT NOT NULL,
                doctor_id INT NULL,
                basic_salary DECIMAL(15,2) DEFAULT 0.00,
                allowance_position DECIMAL(15,2) DEFAULT 0.00,
                allowance_transport DECIMAL(15,2) DEFAULT 0.00,
                overtime_bonus DECIMAL(15,2) DEFAULT 0.00,
                doctor_medical_fee DECIMAL(15,2) DEFAULT 0.00,
                deduction_bpjs DECIMAL(15,2) DEFAULT 0.00,
                deduction_tax DECIMAL(15,2) DEFAULT 0.00,
                deduction_other DECIMAL(15,2) DEFAULT 0.00,
                net_salary DECIMAL(15,2) DEFAULT 0.00,
                status ENUM('pending', 'paid') DEFAULT 'pending',
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (payroll_id) REFERENCES payrolls(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS payroll_items;");
        $this->db->query("DROP TABLE IF EXISTS payrolls;");
        $this->db->query("DROP TABLE IF EXISTS asset_maintenances;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
