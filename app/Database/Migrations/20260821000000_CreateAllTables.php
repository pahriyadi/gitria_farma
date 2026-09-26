<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllTables extends Migration
{
    public function up()
    {
        // Nonaktifkan foreign key checks untuk menghindari kegagalan ordering saat pembuatan batch
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. roles
        $this->db->query("
            CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. permissions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. role_permissions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS role_permissions (
                role_id INT NOT NULL,
                permission_id INT NOT NULL,
                PRIMARY KEY (role_id, permission_id),
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 4. users
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role_id INT NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 5. audit_logs
        $this->db->query("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                module VARCHAR(50) NOT NULL,
                table_name VARCHAR(50) NOT NULL,
                record_id INT NOT NULL,
                old_value TEXT NULL,
                new_value TEXT NULL,
                ip_address VARCHAR(45) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 6. patients
        $this->db->query("
            CREATE TABLE IF NOT EXISTS patients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_rm VARCHAR(20) NOT NULL UNIQUE,
                nik VARCHAR(16) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                gender ENUM('L', 'P') NOT NULL,
                place_of_birth VARCHAR(50) NOT NULL,
                date_of_birth DATE NOT NULL,
                phone VARCHAR(15) NOT NULL,
                address TEXT NOT NULL,
                emergency_contact_name VARCHAR(100) NULL,
                emergency_contact_phone VARCHAR(15) NULL,
                bpjs_number VARCHAR(20) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 7. polyclinics
        $this->db->query("
            CREATE TABLE IF NOT EXISTS polyclinics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 8. doctors
        $this->db->query("
            CREATE TABLE IF NOT EXISTS doctors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nik_employee VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                polyclinic_id INT NOT NULL,
                sip_number VARCHAR(50) NOT NULL,
                str_number VARCHAR(50) NOT NULL,
                str_expiry DATE NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (polyclinic_id) REFERENCES polyclinics(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 9. nurses
        $this->db->query("
            CREATE TABLE IF NOT EXISTS nurses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nik_employee VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                polyclinic_id INT NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (polyclinic_id) REFERENCES polyclinics(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 10. patient_visits
        $this->db->query("
            CREATE TABLE IF NOT EXISTS patient_visits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                no_visit VARCHAR(30) NOT NULL UNIQUE,
                patient_id INT NOT NULL,
                polyclinic_id INT NOT NULL,
                doctor_id INT NOT NULL,
                payment_method ENUM('umum', 'bpjs', 'asuransi') NOT NULL,
                status ENUM('waiting', 'triage', 'examining', 'prescription', 'cashier', 'completed', 'cancelled') DEFAULT 'waiting',
                visit_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (polyclinic_id) REFERENCES polyclinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 11. queue_numbers
        $this->db->query("
            CREATE TABLE IF NOT EXISTS queue_numbers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                polyclinic_id INT NOT NULL,
                queue_no VARCHAR(10) NOT NULL,
                visit_id INT NULL,
                status ENUM('waiting', 'called', 'completed', 'no-show', 'cancelled') DEFAULT 'waiting',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (polyclinic_id) REFERENCES polyclinics(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 12. triage_records
        $this->db->query("
            CREATE TABLE IF NOT EXISTS triage_records (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL UNIQUE,
                weight DECIMAL(5,2) NULL,
                height DECIMAL(5,2) NULL,
                blood_pressure VARCHAR(10) NULL,
                temperature DECIMAL(4,2) NULL,
                pulse INT NULL,
                respiration INT NULL,
                complaints TEXT NULL,
                anamnesis TEXT NULL,
                nurse_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 13. medical_records
        $this->db->query("
            CREATE TABLE IF NOT EXISTS medical_records (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL UNIQUE,
                subjective TEXT NULL,
                objective TEXT NULL,
                assessment TEXT NULL,
                plan TEXT NULL,
                doctor_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 14. icd10_diagnoses
        $this->db->query("
            CREATE TABLE IF NOT EXISTS icd10_diagnoses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL,
                icd10_code VARCHAR(10) NOT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 15. icd9_procedures
        $this->db->query("
            CREATE TABLE IF NOT EXISTS icd9_procedures (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL,
                icd9_code VARCHAR(10) NOT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 16a. categories (Kategori Utama: Poliklinik, Tindakan, dll)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 16b. polikliniks (Tabel terpisah khusus Data Poliklinik)
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

        // 16c. tindakan (Tabel terpisah khusus Tindakan/Sub-Layanan dengan parent_id untuk sub-tindakan)
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

        // 16. services (Tabel lama dipertahankan untuk kompatibilitas)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                category VARCHAR(50) NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 17. service_prices
        $this->db->query("
            CREATE TABLE IF NOT EXISTS service_prices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                service_id INT NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 18. lab_tests
        $this->db->query("
            CREATE TABLE IF NOT EXISTS lab_tests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                reference_range VARCHAR(100) NULL,
                price DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 19. lab_results
        $this->db->query("
            CREATE TABLE IF NOT EXISTS lab_results (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL,
                lab_test_id INT NOT NULL,
                result_value VARCHAR(100) NOT NULL,
                status ENUM('normal', 'abnormal') NOT NULL,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 20. medicines
        $this->db->query("
            CREATE TABLE IF NOT EXISTS medicines (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                type ENUM('bebas', 'keras') NOT NULL,
                unit VARCHAR(20) NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                min_stock INT DEFAULT 10,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 21. medicine_batches
        $this->db->query("
            CREATE TABLE IF NOT EXISTS medicine_batches (
                id INT AUTO_INCREMENT PRIMARY KEY,
                medicine_id INT NOT NULL,
                batch_no VARCHAR(50) NOT NULL,
                buy_price DECIMAL(15,2) NOT NULL,
                stock INT NOT NULL DEFAULT 0,
                expired_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 22. stock_movements
        $this->db->query("
            CREATE TABLE IF NOT EXISTS stock_movements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                medicine_id INT NOT NULL,
                batch_id INT NOT NULL,
                transaction_type ENUM('pembelian', 'penjualan', 'resep', 'retur', 'adjustment', 'opname', 'expired', 'rusak') NOT NULL,
                reference_id INT NULL,
                qty_in INT DEFAULT 0,
                qty_out INT DEFAULT 0,
                balance INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (batch_id) REFERENCES medicine_batches(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 23. prescriptions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS prescriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL UNIQUE,
                doctor_id INT NOT NULL,
                status ENUM('waiting', 'processing', 'completed', 'cancelled') DEFAULT 'waiting',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 24. prescription_details
        $this->db->query("
            CREATE TABLE IF NOT EXISTS prescription_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                prescription_id INT NOT NULL,
                medicine_id INT NOT NULL,
                qty INT NOT NULL,
                dosage VARCHAR(100) NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 25. suppliers
        $this->db->query("
            CREATE TABLE IF NOT EXISTS suppliers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                address TEXT NOT NULL,
                phone VARCHAR(15) NOT NULL,
                bank_name VARCHAR(50) NULL,
                bank_account VARCHAR(30) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 26. restaurant_tables
        $this->db->query("
            CREATE TABLE IF NOT EXISTS restaurant_tables (
                id INT AUTO_INCREMENT PRIMARY KEY,
                table_no VARCHAR(10) NOT NULL UNIQUE,
                capacity INT NOT NULL,
                status ENUM('empty', 'active') DEFAULT 'empty',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 27. restaurant_menus
        $this->db->query("
            CREATE TABLE IF NOT EXISTS restaurant_menus (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                category ENUM('makanan', 'minuman', 'paket') NOT NULL,
                classification ENUM('resep', 'umum') NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 28. restaurant_orders
        $this->db->query("
            CREATE TABLE IF NOT EXISTS restaurant_orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_no VARCHAR(30) NOT NULL UNIQUE,
                table_id INT NOT NULL,
                visit_id INT NULL,
                status ENUM('open', 'cooking', 'ready', 'closed', 'cancelled') DEFAULT 'open',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 29. restaurant_order_details
        $this->db->query("
            CREATE TABLE IF NOT EXISTS restaurant_order_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                menu_id INT NOT NULL,
                qty INT NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                status ENUM('new', 'cooking', 'ready', 'served') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES restaurant_orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (menu_id) REFERENCES restaurant_menus(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 30. kitchen_orders
        $this->db->query("
            CREATE TABLE IF NOT EXISTS kitchen_orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_detail_id INT NOT NULL UNIQUE,
                status ENUM('new', 'cooking', 'ready') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_detail_id) REFERENCES restaurant_order_details(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 31. billing_transactions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS billing_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                billing_no VARCHAR(30) NOT NULL UNIQUE,
                visit_id INT NOT NULL UNIQUE,
                total_services DECIMAL(15,2) DEFAULT 0.00,
                total_medicines DECIMAL(15,2) DEFAULT 0.00,
                total_restaurant DECIMAL(15,2) DEFAULT 0.00,
                discount DECIMAL(15,2) DEFAULT 0.00,
                grand_total DECIMAL(15,2) DEFAULT 0.00,
                status ENUM('draft', 'open', 'partial', 'paid', 'cancelled') DEFAULT 'draft',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 32. billing_details
        $this->db->query("
            CREATE TABLE IF NOT EXISTS billing_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                billing_id INT NOT NULL,
                item_type ENUM('medis', 'obat', 'resto') NOT NULL,
                item_name VARCHAR(150) NOT NULL,
                qty INT NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                subtotal DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (billing_id) REFERENCES billing_transactions(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 33. cash_registers
        $this->db->query("
            CREATE TABLE IF NOT EXISTS cash_registers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                balance DECIMAL(15,2) DEFAULT 0.00,
                status ENUM('open', 'closed') DEFAULT 'open',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 34. cash_transactions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS cash_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                receipt_no VARCHAR(30) NOT NULL UNIQUE,
                billing_id INT NOT NULL,
                cash_register_id INT NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                payment_method ENUM('cash', 'transfer', 'qris', 'debit', 'credit') NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (billing_id) REFERENCES billing_transactions(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (cash_register_id) REFERENCES cash_registers(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 35. purchase_requests
        $this->db->query("
            CREATE TABLE IF NOT EXISTS purchase_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                request_no VARCHAR(30) NOT NULL UNIQUE,
                supplier_id INT NOT NULL,
                status ENUM('draft', 'submitted', 'verified', 'approved', 'rejected', 'completed') DEFAULT 'draft',
                total_amount DECIMAL(15,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 36. purchase_orders
        $this->db->query("
            CREATE TABLE IF NOT EXISTS purchase_orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                po_no VARCHAR(30) NOT NULL UNIQUE,
                purchase_request_id INT NOT NULL,
                supplier_id INT NOT NULL,
                total_amount DECIMAL(15,2) NOT NULL,
                status ENUM('ordered', 'received', 'cancelled') DEFAULT 'ordered',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 37. goods_receipts
        $this->db->query("
            CREATE TABLE IF NOT EXISTS goods_receipts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                receipt_no VARCHAR(30) NOT NULL UNIQUE,
                purchase_order_id INT NOT NULL,
                received_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 38. approval_workflows
        $this->db->query("
            CREATE TABLE IF NOT EXISTS approval_workflows (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_type VARCHAR(50) NOT NULL UNIQUE,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 39. approval_steps
        $this->db->query("
            CREATE TABLE IF NOT EXISTS approval_steps (
                id INT AUTO_INCREMENT PRIMARY KEY,
                workflow_id INT NOT NULL,
                step_name VARCHAR(50) NOT NULL,
                step_level INT NOT NULL,
                role_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (workflow_id) REFERENCES approval_workflows(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 40. approval_requests
        $this->db->query("
            CREATE TABLE IF NOT EXISTS approval_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_type VARCHAR(50) NOT NULL,
                reference_id INT NOT NULL,
                step_level INT NOT NULL,
                approver_id INT NULL,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (approver_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 41. accounts
        $this->db->query("
            CREATE TABLE IF NOT EXISTS accounts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
                parent_id INT NULL,
                normal_balance ENUM('debit', 'credit') NOT NULL,
                balance DECIMAL(15,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 42. transaction_account_mappings
        $this->db->query("
            CREATE TABLE IF NOT EXISTS transaction_account_mappings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                transaction_type VARCHAR(50) NOT NULL UNIQUE,
                debit_account_id INT NOT NULL,
                credit_account_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (debit_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (credit_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 43. journal_entries
        $this->db->query("
            CREATE TABLE IF NOT EXISTS journal_entries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                journal_no VARCHAR(30) NOT NULL UNIQUE,
                entry_date DATE NOT NULL,
                source_module VARCHAR(50) NOT NULL,
                reference_id INT NULL,
                description TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 44. journal_entry_details
        $this->db->query("
            CREATE TABLE IF NOT EXISTS journal_entry_details (
                id INT AUTO_INCREMENT PRIMARY KEY,
                journal_id INT NOT NULL,
                account_id INT NOT NULL,
                debit DECIMAL(15,2) DEFAULT 0.00,
                credit DECIMAL(15,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (journal_id) REFERENCES journal_entries(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 45. inventory_assets
        $this->db->query("
            CREATE TABLE IF NOT EXISTS inventory_assets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                category VARCHAR(50) NOT NULL,
                location VARCHAR(100) NOT NULL,
                purchase_date DATE NOT NULL,
                price DECIMAL(15,2) NOT NULL,
                supplier_id INT NULL,
                condition_status ENUM('good', 'damaged', 'maintenance') DEFAULT 'good',
                pj_employee VARCHAR(100) NULL,
                serial_number VARCHAR(50) NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 46. asset_mutations
        $this->db->query("
            CREATE TABLE IF NOT EXISTS asset_mutations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                asset_id INT NOT NULL,
                old_location VARCHAR(100) NOT NULL,
                new_location VARCHAR(100) NOT NULL,
                old_pj VARCHAR(100) NOT NULL,
                new_pj VARCHAR(100) NOT NULL,
                mutation_date DATE NOT NULL,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (asset_id) REFERENCES inventory_assets(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 47. asset_depreciations
        $this->db->query("
            CREATE TABLE IF NOT EXISTS asset_depreciations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                asset_id INT NOT NULL,
                depreciation_date DATE NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (asset_id) REFERENCES inventory_assets(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 48. fee_rules
        $this->db->query("
            CREATE TABLE IF NOT EXISTS fee_rules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                service_id INT NOT NULL,
                role_id INT NULL,
                percentage DECIMAL(5,2) DEFAULT 0.00,
                flat_fee DECIMAL(15,2) DEFAULT 0.00,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 49. fee_transactions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS fee_transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_id INT NOT NULL,
                doctor_id INT NOT NULL,
                fee_rule_id INT NOT NULL,
                amount DECIMAL(15,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (visit_id) REFERENCES patient_visits(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (fee_rule_id) REFERENCES fee_rules(id) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 50. employees
        $this->db->query("
            CREATE TABLE IF NOT EXISTS employees (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nip VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                department VARCHAR(50) NOT NULL,
                salary DECIMAL(15,2) NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Re-aktifkan foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // Drop tables in reverse dependency order
        $tables = [
            'employees', 'fee_transactions', 'fee_rules', 'asset_depreciations', 'asset_mutations',
            'inventory_assets', 'journal_entry_details', 'journal_entries', 'transaction_account_mappings',
            'accounts', 'approval_requests', 'approval_steps', 'approval_workflows', 'goods_receipts',
            'purchase_orders', 'purchase_requests', 'cash_transactions', 'cash_registers', 'billing_details',
            'billing_transactions', 'kitchen_orders', 'restaurant_order_details', 'restaurant_orders',
            'restaurant_menus', 'restaurant_tables', 'suppliers', 'prescription_details', 'prescriptions',
            'stock_movements', 'medicine_batches', 'medicines', 'lab_results', 'lab_tests', 'service_prices',
            'services', 'tindakan', 'polikliniks', 'categories',
            'icd9_procedures', 'icd10_diagnoses', 'medical_records', 'triage_records',
            'queue_numbers', 'patient_visits', 'nurses', 'doctors', 'polyclinics', 'patients', 'audit_logs',
            'users', 'role_permissions', 'permissions', 'roles'
        ];

        foreach ($tables as $table) {
            $this->db->query("DROP TABLE IF EXISTS {$table};");
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
