-- ====================================================================
-- SAWAMAWA MEDICAL CENTER & GITRIA FARMA ERP
-- MASTER DATABASE CLEAN INSTALL / RESET DUMP (108 TABEL LENGKAP)
-- SCRIPT PENGHAPUS DATA SELURUHNYA & PEMASANGAN TABEL LENGKAP TERBARU
-- Compatible with MySQL 5.7+, 8.0+ & MariaDB 10.3+
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------------------
-- 1. DROP ALL EXISTING TABLES
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `user_permissions`;
DROP TABLE IF EXISTS `accounts`;
DROP TABLE IF EXISTS `transaction_account_mappings`;
DROP TABLE IF EXISTS `journal_categories`;
DROP TABLE IF EXISTS `journal_category_rules`;
DROP TABLE IF EXISTS `journal_entries`;
DROP TABLE IF EXISTS `journal_entry_details`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `job_positions`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `polikliniks`;
DROP TABLE IF EXISTS `polyclinics`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `service_prices`;
DROP TABLE IF EXISTS `tindakan`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `beds`;
DROP TABLE IF EXISTS `units`;
DROP TABLE IF EXISTS `medicine_categories`;
DROP TABLE IF EXISTS `payment_methods`;
DROP TABLE IF EXISTS `insurance_providers`;
DROP TABLE IF EXISTS `consent_templates`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `doctor_schedules`;
DROP TABLE IF EXISTS `nurses`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `work_shifts`;
DROP TABLE IF EXISTS `employee_attendances`;
DROP TABLE IF EXISTS `employee_leaves`;
DROP TABLE IF EXISTS `payrolls`;
DROP TABLE IF EXISTS `payroll_items`;
DROP TABLE IF EXISTS `patients`;
DROP TABLE IF EXISTS `patient_visits`;
DROP TABLE IF EXISTS `queue_numbers`;
DROP TABLE IF EXISTS `queue_call_events`;
DROP TABLE IF EXISTS `vital_signs`;
DROP TABLE IF EXISTS `triage_records`;
DROP TABLE IF EXISTS `medical_records`;
DROP TABLE IF EXISTS `master_icd10`;
DROP TABLE IF EXISTS `master_icd9`;
DROP TABLE IF EXISTS `icd10_diagnoses`;
DROP TABLE IF EXISTS `icd9_procedures`;
DROP TABLE IF EXISTS `odontograms`;
DROP TABLE IF EXISTS `lab_tests`;
DROP TABLE IF EXISTS `lab_results`;
DROP TABLE IF EXISTS `medical_letters`;
DROP TABLE IF EXISTS `medical_photos`;
DROP TABLE IF EXISTS `medical_informed_consents`;
DROP TABLE IF EXISTS `pharmacy_warehouses`;
DROP TABLE IF EXISTS `medicines`;
DROP TABLE IF EXISTS `medicine_batches`;
DROP TABLE IF EXISTS `warehouse_stock`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `prescription_items`;
DROP TABLE IF EXISTS `prescription_details`;
DROP TABLE IF EXISTS `pharmacy_pending_prescriptions`;
DROP TABLE IF EXISTS `pharmacy_sales`;
DROP TABLE IF EXISTS `pharmacy_sale_details`;
DROP TABLE IF EXISTS `stock_opnames`;
DROP TABLE IF EXISTS `stock_opname_details`;
DROP TABLE IF EXISTS `stock_transfers`;
DROP TABLE IF EXISTS `stock_transfer_items`;
DROP TABLE IF EXISTS `cash_registers`;
DROP TABLE IF EXISTS `internal_cash_transfers`;
DROP TABLE IF EXISTS `billing_transactions`;
DROP TABLE IF EXISTS `billing_details`;
DROP TABLE IF EXISTS `cash_transactions`;
DROP TABLE IF EXISTS `fee_rules`;
DROP TABLE IF EXISTS `fee_transactions`;
DROP TABLE IF EXISTS `doctor_fee_settlements`;
DROP TABLE IF EXISTS `restaurant_categories`;
DROP TABLE IF EXISTS `restaurant_tables`;
DROP TABLE IF EXISTS `restaurant_menus`;
DROP TABLE IF EXISTS `restaurant_orders`;
DROP TABLE IF EXISTS `restaurant_order_details`;
DROP TABLE IF EXISTS `kitchen_orders`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `purchase_requests`;
DROP TABLE IF EXISTS `purchase_request_items`;
DROP TABLE IF EXISTS `purchase_orders`;
DROP TABLE IF EXISTS `purchase_order_items`;
DROP TABLE IF EXISTS `goods_receipts`;
DROP TABLE IF EXISTS `goods_receipt_items`;
DROP TABLE IF EXISTS `inventory_assets`;
DROP TABLE IF EXISTS `asset_depreciations`;
DROP TABLE IF EXISTS `asset_maintenances`;
DROP TABLE IF EXISTS `asset_mutations`;
DROP TABLE IF EXISTS `approval_workflows`;
DROP TABLE IF EXISTS `approval_steps`;
DROP TABLE IF EXISTS `approval_requests`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `notification_rules`;
DROP TABLE IF EXISTS `system_notifications`;
DROP TABLE IF EXISTS `system_notification_reads`;
DROP TABLE IF EXISTS `system_settings`;
DROP TABLE IF EXISTS `system_updates`;
DROP TABLE IF EXISTS `system_documentations`;
DROP TABLE IF EXISTS `system_error_logs`;
DROP TABLE IF EXISTS `system_performance_metrics`;
DROP TABLE IF EXISTS `system_slow_queries`;
DROP TABLE IF EXISTS `whatsapp_logs`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `migrations`;

-- --------------------------------------------------------------------
-- 2. STRUCTURE: 108 ENTERPRISE TABLES DEFINITION (ORDERED)
-- --------------------------------------------------------------------
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `idx_permission_id` (`permission_id`),
  CONSTRAINT `fk_role_permissions_role_id_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_role_permissions_permission_id_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `digital_signature` longtext DEFAULT NULL,
  `sip_str_number` varchar(100) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_role_id` (`role_id`),
  CONSTRAINT `fk_users_role_id_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `idx_idx_user_id` (`user_id`),
  KEY `idx_idx_permission_id` (`permission_id`),
  CONSTRAINT `fk_user_permissions_user_id_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_permissions_permission_id_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `normal_balance` enum('debit','credit') NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_parent_id` (`parent_id`),
  CONSTRAINT `fk_accounts_parent_id_1` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transaction_account_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transaction_type` (`transaction_type`),
  KEY `idx_debit_account_id` (`debit_account_id`),
  KEY `idx_credit_account_id` (`credit_account_id`),
  CONSTRAINT `fk_transaction_account_mappings_debit_account_id_1` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_transaction_account_mappings_credit_account_id_2` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL UNIQUE,
  `category_name` varchar(150) NOT NULL,
  `module` varchar(50) DEFAULT 'apotek',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_module` (`module`),
  KEY `idx_idx_idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_category_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `position` enum('debit','credit') DEFAULT 'credit',
  `calc_type` enum('percentage','fixed_amount','dynamic_fee','formula') DEFAULT 'percentage',
  `percentage_value` decimal(6,2) DEFAULT 0.00,
  `fixed_amount_value` decimal(15,2) DEFAULT 0.00,
  `formula_code` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_category_id` (`category_id`),
  KEY `idx_idx_idx_account_id` (`account_id`),
  KEY `idx_idx_idx_is_active` (`is_active`),
  CONSTRAINT `fk_journal_category_rules_category_id_1` FOREIGN KEY (`category_id`) REFERENCES `journal_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_no` varchar(30) NOT NULL,
  `entry_date` date NOT NULL,
  `source_module` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_journal_no` (`journal_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entry_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_journal_id` (`journal_id`),
  KEY `idx_account_id` (`account_id`),
  CONSTRAINT `fk_journal_entry_details_journal_id_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_journal_entry_details_account_id_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `base_salary_min` decimal(15,2) DEFAULT 0.00,
  `base_salary_max` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_department_id` (`department_id`),
  CONSTRAINT `fk_job_positions_department_id_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` enum('medicine','service','general') NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `polikliniks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`),
  KEY `idx_category_id` (`category_id`),
  CONSTRAINT `fk_polikliniks_category_id_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `polyclinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) DEFAULT 'tindakan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_fk_services_parent` (`parent_id`),
  CONSTRAINT `fk_services_parent_id_1` FOREIGN KEY (`parent_id`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_service_id` (`service_id`),
  CONSTRAINT `fk_service_prices_service_id_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tindakan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_parent_id` (`parent_id`),
  CONSTRAINT `fk_tindakan_category_id_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_tindakan_parent_id_2` FOREIGN KEY (`parent_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('poli','tindakan','observasi','rawat_inap','laboratorium','apotek','kasir','gudang') NOT NULL DEFAULT 'poli',
  `floor` varchar(20) DEFAULT 'Lantai 1',
  `capacity` int(11) DEFAULT 1,
  `tariff_per_day` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `beds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `bed_number` varchar(30) NOT NULL,
  `tariff_per_day` decimal(15,2) DEFAULT 0.00,
  `status` enum('available','occupied','maintenance','reserved') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`),
  CONSTRAINT `fk_beds_room_id_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category` enum('farmasi','logistik','aset','umum') DEFAULT 'farmasi',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medicine_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `drug_class` enum('obat_bebas','obat_bebas_terbatas','obat_keras','psikotropika','narkotika','prekursor','alkes_bmhp','herbal','suplemen') NOT NULL DEFAULT 'obat_keras',
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('cash','qris','transfer','debit','credit','insurance') NOT NULL DEFAULT 'cash',
  `account_number` varchar(100) DEFAULT NULL,
  `account_name` varchar(150) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_idx_active` (`is_active`),
  KEY `idx_idx_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `insurance_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('bpjs','asuransi_swasta','corporate','pemerintah','mandiri') NOT NULL DEFAULT 'asuransi_swasta',
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `claim_address` text DEFAULT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `discount_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` enum('bpjs','swasta','perusahaan') NOT NULL DEFAULT 'swasta',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `consent_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tindakan_id` int(11) DEFAULT NULL,
  `template_title` varchar(150) NOT NULL,
  `diagnosis_indication` varchar(255) NOT NULL,
  `procedure_action` varchar(255) NOT NULL,
  `goal_benefits` text NOT NULL,
  `risks_complications` text NOT NULL,
  `prognosis` varchar(255) NOT NULL,
  `alternative_therapies` text NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fk_ct_tindakan` (`tindakan_id`),
  CONSTRAINT `fk_consent_templates_tindakan_id_1` FOREIGN KEY (`tindakan_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nik_employee` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `polyclinic_id` int(11) DEFAULT NULL,
  `tindakan_id` int(11) DEFAULT NULL,
  `fee_per_pasien` decimal(15,2) DEFAULT 0.00,
  `sip_number` varchar(50) NOT NULL,
  `str_number` varchar(50) NOT NULL,
  `str_expiry` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `digital_signature` longtext DEFAULT NULL,
  `stamp_image` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `specialization` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `fee_type` enum('percentage','fixed_amount') DEFAULT 'percentage',
  `prescription_fee_percent` decimal(5,2) DEFAULT 5.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nik_employee` (`nik_employee`),
  KEY `idx_polyclinic_id` (`polyclinic_id`),
  KEY `idx_fk_doc_category` (`category_id`),
  KEY `idx_fk_doc_tindakan` (`tindakan_id`),
  CONSTRAINT `fk_doctors_polyclinic_id_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_doctors_category_id_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_doctors_tindakan_id_3` FOREIGN KEY (`tindakan_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY `idx_idx_idx_user_id` (`user_id`),
  CONSTRAINT `fk_doctors_user_id_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `start_time` time NOT NULL DEFAULT '08:00:00',
  `end_time` time NOT NULL DEFAULT '14:00:00',
  `max_quota` int(11) NOT NULL DEFAULT 30,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fk_ds_doctor` (`doctor_id`),
  KEY `idx_fk_ds_room` (`room_id`),
  CONSTRAINT `fk_doctor_schedules_doctor_id_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doctor_schedules_room_id_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `nurses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik_employee` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `digital_signature` longtext DEFAULT NULL,
  `sip_number` varchar(50) DEFAULT NULL,
  `str_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nik_employee` (`nik_employee`),
  KEY `idx_polyclinic_id` (`polyclinic_id`),
  CONSTRAINT `fk_nurses_polyclinic_id_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  KEY `idx_idx_idx_user_id` (`user_id`),
  CONSTRAINT `fk_nurses_user_id_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) NOT NULL,
  `nik_ktp` varchar(25) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `salary` decimal(15,2) NOT NULL,
  `allowance_position` decimal(15,2) DEFAULT 0.00,
  `allowance_transport` decimal(15,2) DEFAULT 0.00,
  `deduction_bpjs` decimal(15,2) DEFAULT 0.00,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(30) DEFAULT NULL,
  `employment_type` enum('tetap','kontrak','mitra','magang') DEFAULT 'tetap',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_nip` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `work_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_code` varchar(20) NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `late_tolerance_minutes` int(11) DEFAULT 15,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shift_code` (`shift_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `employee_attendances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` enum('present','late','sick','permit','leave','alpha') DEFAULT 'present',
  `late_minutes` int(11) DEFAULT 0,
  `overtime_minutes` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_shift_id` (`shift_id`),
  CONSTRAINT `fk_employee_attendances_employee_id_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_employee_attendances_shift_id_2` FOREIGN KEY (`shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `employee_leaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('cuti_tahunan','sakit','izin','cuti_melahirkan','dinas_luar') DEFAULT 'izin',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) DEFAULT 1,
  `reason` text NOT NULL,
  `attachment_doc` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee_id` (`employee_id`),
  CONSTRAINT `fk_employee_leaves_employee_id_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_code` varchar(30) NOT NULL,
  `period_month` int(11) NOT NULL,
  `period_year` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('transfer','cash') DEFAULT 'transfer',
  `bank_source_id` int(11) DEFAULT NULL,
  `total_gross` decimal(15,2) DEFAULT 0.00,
  `total_deductions` decimal(15,2) DEFAULT 0.00,
  `total_net_salary` decimal(15,2) DEFAULT 0.00,
  `total_employees` int(11) DEFAULT 0,
  `status` enum('draft','approved','paid') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payroll_no` varchar(30) NOT NULL UNIQUE,
  `employee_id` int(11) NOT NULL,
  `basic_salary` decimal(15,2) NOT NULL,
  `allowances` decimal(15,2) DEFAULT 0.00,
  `deductions` decimal(15,2) DEFAULT 0.00,
  `net_salary` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payroll_code` (`payroll_code`),
  KEY `idx_idx_idx_employee_id` (`employee_id`),
  CONSTRAINT `fk_payrolls_employee_id_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `basic_salary` decimal(15,2) DEFAULT 0.00,
  `allowance_position` decimal(15,2) DEFAULT 0.00,
  `allowance_transport` decimal(15,2) DEFAULT 0.00,
  `overtime_bonus` decimal(15,2) DEFAULT 0.00,
  `doctor_medical_fee` decimal(15,2) DEFAULT 0.00,
  `deduction_bpjs` decimal(15,2) DEFAULT 0.00,
  `deduction_tax` decimal(15,2) DEFAULT 0.00,
  `deduction_other` decimal(15,2) DEFAULT 0.00,
  `net_salary` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','paid') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('allowance','deduction') NOT NULL,
  `description` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payroll_id` (`payroll_id`),
  KEY `idx_employee_id` (`employee_id`),
  CONSTRAINT `fk_payroll_items_payroll_id_1` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payroll_items_employee_id_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `place_of_birth` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(15) DEFAULT NULL,
  `bpjs_number` varchar(20) DEFAULT NULL,
  `insurance_provider_id` int(11) DEFAULT NULL,
  `membership_tier` varchar(30) DEFAULT 'regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `satusehat_ihs_id` varchar(100) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `insurance_type` enum('umum','bpjs','asuransi') DEFAULT 'umum',
  `insurance_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_no_rm` (`no_rm`),
  UNIQUE KEY `uq_nik` (`nik`),
  KEY `idx_fk_pat_insurance` (`insurance_provider_id`),
  CONSTRAINT `fk_patients_insurance_provider_id_1` FOREIGN KEY (`insurance_provider_id`) REFERENCES `insurance_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patient_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_visit` varchar(30) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `bed_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'umum',
  `insurance_id` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'waiting',
  `visit_type` enum('poli','tindakan') DEFAULT 'poli',
  `service_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `satusehat_encounter_id` varchar(100) DEFAULT NULL,
  `satusehat_sync_time` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_no_visit` (`no_visit`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_polyclinic_id` (`polyclinic_id`),
  KEY `idx_doctor_id` (`doctor_id`),
  KEY `idx_fk_pv_service` (`service_id`),
  KEY `idx_fk_pv_room` (`room_id`),
  KEY `idx_fk_pv_insurance` (`insurance_id`),
  CONSTRAINT `fk_patient_visits_insurance_id_1` FOREIGN KEY (`insurance_id`) REFERENCES `insurance_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_patient_visits_room_id_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_patient_visits_service_id_3` FOREIGN KEY (`service_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_patient_visits_patient_id_4` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_patient_visits_polyclinic_id_5` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_patient_visits_doctor_id_6` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  KEY `idx_idx_idx_bed_id` (`bed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `queue_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `polyclinic_id` int(11) NOT NULL,
  `queue_no` varchar(10) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `service_type` enum('pendaftaran','poli_umum','poli_gigi','poli_kia','lab','apotek','kasir') NOT NULL,
  `counter_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_polyclinic_id` (`polyclinic_id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_queue_numbers_polyclinic_id_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_queue_numbers_visit_id_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `queue_call_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_type` varchar(50) NOT NULL DEFAULT 'poliklinik',
  `service_id` int(11) unsigned DEFAULT NULL,
  `counter_name` varchar(150) NOT NULL DEFAULT 'Poliklinik Umum',
  `queue_number` varchar(50) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `visit_id` int(11) unsigned DEFAULT NULL,
  `call_action` varchar(30) NOT NULL DEFAULT 'call',
  `call_priority` tinyint(2) NOT NULL DEFAULT 1,
  `voice_text` text DEFAULT NULL,
  `status` enum('pending','broadcasting','played','skipped','cancelled') NOT NULL DEFAULT 'pending',
  `caller_user_id` int(11) unsigned DEFAULT NULL,
  `caller_name` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `played_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_played` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_status_call_priority_created_at` (`status`,`call_priority`,`created_at`),
  KEY `idx_idx_idx_service_type` (`service_type`),
  KEY `idx_idx_idx_is_played` (`is_played`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vital_signs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `systolic` int(11) DEFAULT NULL,
  `diastolic` int(11) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,1) DEFAULT NULL,
  `oxygen_saturation` int(11) DEFAULT NULL,
  `gcs` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_vital_signs_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `triage_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `nurse_id` int(11) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_pressure` varchar(10) DEFAULT NULL,
  `temperature` decimal(4,2) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL,
  `respiration` int(11) DEFAULT NULL,
  `complaints` text DEFAULT NULL,
  `anamnesis` text DEFAULT NULL,
  `nurse_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `triage_level` enum('merah','kuning','hijau','hitam') DEFAULT 'hijau',
  `chief_complaint` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_visit_id` (`visit_id`),
  KEY `idx_fk_tr_nurse` (`nurse_id`),
  CONSTRAINT `fk_triage_records_nurse_id_1` FOREIGN KEY (`nurse_id`) REFERENCES `nurses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_triage_records_visit_id_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_idx_idx_visit_id` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `icd10_code` varchar(20) DEFAULT NULL,
  `icd9_code` varchar(20) DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `physical_exam` text DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `icd10_name` varchar(255) DEFAULT NULL,
  `icd9_name` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_visit_id` (`visit_id`),
  CONSTRAINT `fk_medical_records_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_idx_idx_visit_id` (`visit_id`),
  KEY `idx_idx_idx_doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `master_icd10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description_en` text NOT NULL,
  `description_id` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `master_icd9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description_en` text NOT NULL,
  `description_id` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `icd10_diagnoses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `icd10_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `code` varchar(20) DEFAULT NULL,
  `type` enum('primary','secondary') DEFAULT 'primary',
  PRIMARY KEY (`id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_icd10_diagnoses_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `icd9_procedures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `icd9_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_icd9_procedures_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `odontograms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) unsigned DEFAULT NULL,
  `patient_id` int(11) unsigned NOT NULL,
  `tooth_number` varchar(10) NOT NULL,
  `condition_code` varchar(20) NOT NULL DEFAULT 'normal',
  `condition_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `condition_type` enum('normal','caries','filling','missing','crown','bridge','implant','other') DEFAULT 'normal',
  PRIMARY KEY (`id`),
  KEY `idx_patient_id_tooth_number` (`patient_id`,`tooth_number`),
  KEY `idx_idx_idx_patient_id` (`patient_id`),
  KEY `idx_idx_idx_tooth_number` (`tooth_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Hematologi',
  `specimen` varchar(50) NOT NULL DEFAULT 'Darah Vena',
  `reference_range` varchar(100) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `normal_range` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lab_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_no` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `officer_name` varchar(100) DEFAULT 'Analis Laboratorium',
  `test_date` date NOT NULL,
  `test_type` varchar(150) NOT NULL,
  `result_value` varchar(100) NOT NULL,
  `normal_range` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `status` enum('normal','high','low','abnormal') DEFAULT 'normal',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `lab_test_id` int(11) DEFAULT NULL,
  `technician_id` int(11) DEFAULT NULL,
  `is_abnormal` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lab_no` (`lab_no`),
  KEY `idx_idx_lab_visit` (`visit_id`),
  KEY `idx_idx_lab_patient` (`patient_id`),
  KEY `idx_idx_idx_doctor_id` (`doctor_id`),
  KEY `idx_idx_idx_lab_test_id` (`lab_test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medical_letters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `letter_no` varchar(50) NOT NULL,
  `letter_type` enum('sakit','sehat','rujukan') NOT NULL DEFAULT 'sakit',
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `letter_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration_days` int(5) DEFAULT 1,
  `diagnosis` text DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `health_status` enum('sehat','tidak_sehat') DEFAULT 'sehat',
  `blood_pressure` varchar(50) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `color_blind` enum('normal','partial','total') DEFAULT 'normal',
  `referral_destination` varchar(255) DEFAULT NULL,
  `referral_poly` varchar(100) DEFAULT NULL,
  `referral_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_letter_no` (`letter_no`),
  KEY `idx_visit_id` (`visit_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_fk_ml_doctor` (`doctor_id`),
  CONSTRAINT `fk_medical_letters_doctor_id_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_medical_letters_patient_id_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medical_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `category` enum('before','process','after','other') DEFAULT 'before',
  `title` varchar(255) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `taken_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_idx_photo_patient` (`patient_id`),
  KEY `idx_idx_photo_visit` (`visit_id`),
  CONSTRAINT `fk_medical_photos_patient_id_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medical_informed_consents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `procedure_name` varchar(255) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `indication` text DEFAULT NULL,
  `procedure_desc` text DEFAULT NULL,
  `risks_complications` text DEFAULT NULL,
  `prognosis` varchar(255) DEFAULT NULL,
  `consent_type` enum('agree','refuse') DEFAULT 'agree',
  `authorized_person_name` varchar(255) NOT NULL,
  `authorized_person_relation` varchar(100) DEFAULT 'Diri Sendiri',
  `authorized_person_signature` mediumtext DEFAULT NULL,
  `doctor_signature` mediumtext DEFAULT NULL,
  `witness_name` varchar(255) DEFAULT NULL,
  `consent_date` datetime NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_idx_consent_patient` (`patient_id`),
  KEY `idx_idx_consent_visit` (`visit_id`),
  KEY `idx_fk_mic_doctor` (`doctor_id`),
  CONSTRAINT `fk_medical_informed_consents_doctor_id_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pharmacy_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('main','depo','unit') NOT NULL DEFAULT 'depo',
  `pic_name` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `type` enum('bebas','keras') NOT NULL,
  `unit` varchar(20) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `min_stock` int(11) DEFAULT 10,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `generic_name` varchar(100) DEFAULT NULL,
  `form` enum('tablet','capsule','syrup','injection','ointment','drops','other') DEFAULT 'tablet',
  `buy_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `side_effects` text DEFAULT NULL,
  `contraindications` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_fk_med_category` (`category_id`),
  KEY `idx_fk_med_unit` (`unit_id`),
  CONSTRAINT `fk_medicines_category_id_1` FOREIGN KEY (`category_id`) REFERENCES `medicine_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_medicines_unit_id_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medicine_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `buy_price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `expired_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `warehouse_id` int(11) DEFAULT 1,
  `sale_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_medicine_batches_medicine_id_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouse_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_warehouse_batch_unique` (`warehouse_id`,`medicine_id`,`batch_id`),
  KEY `idx_warehouse_id` (`warehouse_id`),
  KEY `idx_medicine_id` (`medicine_id`),
  KEY `idx_batch_id` (`batch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `transaction_type` enum('pembelian','penjualan','resep','retur','adjustment','opname','expired','rusak') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `qty_in` int(11) DEFAULT 0,
  `qty_out` int(11) DEFAULT 0,
  `balance` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_medicine_id` (`medicine_id`),
  KEY `idx_batch_id` (`batch_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_stock_movements_medicine_id_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_movements_batch_id_2` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_movements_user_id_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `status` enum('waiting','processing','completed','cancelled') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dispensed_at` datetime DEFAULT NULL,
  `prescription_no` varchar(30) NOT NULL UNIQUE,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_visit_id` (`visit_id`),
  KEY `idx_doctor_id` (`doctor_id`),
  CONSTRAINT `fk_prescriptions_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prescriptions_doctor_id_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  KEY `idx_idx_idx_visit_id` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dosage` varchar(50) NOT NULL,
  `instructions` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `tusla` decimal(15,2) DEFAULT 0.00,
  `embalase` decimal(15,2) DEFAULT 0.00,
  `is_racikan` tinyint(1) DEFAULT 0,
  `racikan_name` varchar(100) DEFAULT NULL,
  `aturan_pakai` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_prescription_id` (`prescription_id`),
  KEY `idx_idx_idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_prescription_items_prescription_id_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prescription_items_medicine_id_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `prescription_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `status` enum('served','bought_outside','cancelled') DEFAULT 'served',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_prescription_id` (`prescription_id`),
  KEY `idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_prescription_details_prescription_id_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prescription_details_medicine_id_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pharmacy_pending_prescriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pending_no` varchar(50) NOT NULL,
  `customer_name` varchar(150) DEFAULT 'Pelanggan Umum',
  `customer_phone` varchar(50) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `source_type` enum('otc','resep_dokter','kasir_klinik') DEFAULT 'otc',
  `payload_json` longtext NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `tusla_amount` decimal(15,2) DEFAULT 0.00,
  `embalase_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','resumed','cancelled') DEFAULT 'pending',
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_pending_no` (`pending_no`),
  KEY `idx_idx_idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pharmacy_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_no` varchar(50) NOT NULL,
  `customer_name` varchar(150) NOT NULL DEFAULT 'Pelanggan Umum',
  `customer_phone` varchar(50) DEFAULT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'tunai',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_fee_nominal` decimal(15,2) DEFAULT 0.00,
  `prescription_type` enum('bebas','resep','racikan','online','konsul_online') DEFAULT 'bebas',
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sale_no` (`sale_no`),
  KEY `idx_idx_sale_date` (`sale_date`),
  KEY `idx_idx_idx_doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pharmacy_sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dosage_instruction` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `tusla` decimal(15,2) DEFAULT 0.00,
  `embalase` decimal(15,2) DEFAULT 0.00,
  `is_racikan` tinyint(1) DEFAULT 0,
  `racikan_name` varchar(100) DEFAULT NULL,
  `aturan_pakai` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_sale_ref` (`sale_id`),
  KEY `idx_idx_med_ref` (`medicine_id`),
  CONSTRAINT `fk_pharmacy_sale_details_medicine_id_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ,
  CONSTRAINT `fk_pharmacy_sale_details_sale_id_2` FOREIGN KEY (`sale_id`) REFERENCES `pharmacy_sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_opnames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opname_no` varchar(30) NOT NULL,
  `opname_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('draft','adjusted') DEFAULT 'adjusted',
  `total_items` int(11) DEFAULT 0,
  `total_discrepancy` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `performed_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_opname_no` (`opname_no`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_stock_opnames_user_id_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  KEY `idx_idx_idx_performed_by` (`performed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_opname_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opname_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `system_stock` int(11) NOT NULL,
  `physical_stock` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_opname_id` (`opname_id`),
  KEY `idx_medicine_id` (`medicine_id`),
  KEY `idx_batch_id` (`batch_id`),
  CONSTRAINT `fk_stock_opname_details_opname_id_1` FOREIGN KEY (`opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_opname_details_medicine_id_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_opname_details_batch_id_3` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(50) NOT NULL,
  `source_warehouse_id` int(11) NOT NULL,
  `target_warehouse_id` int(11) NOT NULL,
  `transfer_date` date NOT NULL,
  `requested_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('draft','approved','completed','cancelled') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transfer_no` (`transfer_no`),
  KEY `idx_source_warehouse_id` (`source_warehouse_id`),
  KEY `idx_target_warehouse_id` (`target_warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `stock_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(50) DEFAULT 'Pcs',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_stock_transfer_id` (`stock_transfer_id`),
  KEY `idx_medicine_id` (`medicine_id`),
  KEY `idx_batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cash_registers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `internal_cash_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(30) NOT NULL,
  `from_account_id` int(11) NOT NULL,
  `to_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transfer_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transfer_no` (`transfer_no`),
  KEY `idx_idx_idx_from_account_id` (`from_account_id`),
  KEY `idx_idx_idx_to_account_id` (`to_account_id`),
  CONSTRAINT `fk_internal_cash_transfers_from_account_id_1` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`) ,
  CONSTRAINT `fk_internal_cash_transfers_to_account_id_2` FOREIGN KEY (`to_account_id`) REFERENCES `accounts` (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `billing_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_no` varchar(30) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `total_services` decimal(15,2) DEFAULT 0.00,
  `total_medicines` decimal(15,2) DEFAULT 0.00,
  `total_restaurant` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','open','partial','paid','cancelled') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_billing_no` (`billing_no`),
  UNIQUE KEY `uq_visit_id` (`visit_id`),
  KEY `idx_fk_bt_payment_method` (`payment_method_id`),
  CONSTRAINT `fk_billing_transactions_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_billing_transactions_payment_method_id_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  KEY `idx_idx_idx_visit_id` (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `billing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `item_type` enum('medis','obat','resto') NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_id` int(11) DEFAULT NULL,
  `tusla` decimal(15,2) DEFAULT 0.00,
  `embalase` decimal(15,2) DEFAULT 0.00,
  `is_racikan` tinyint(1) DEFAULT 0,
  `racikan_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_billing_id` (`billing_id`),
  CONSTRAINT `fk_billing_details_billing_id_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cash_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(30) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `cash_register_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `change_amount` decimal(15,2) DEFAULT 0.00,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'tunai',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_receipt_no` (`receipt_no`),
  KEY `idx_billing_id` (`billing_id`),
  KEY `idx_cash_register_id` (`cash_register_id`),
  CONSTRAINT `fk_cash_transactions_billing_id_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cash_transactions_cash_register_id_2` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fee_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `flat_fee` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_role_id` (`role_id`),
  CONSTRAINT `fk_fee_rules_service_id_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fee_rules_role_id_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fee_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `fee_rule_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_visit_id` (`visit_id`),
  KEY `idx_doctor_id` (`doctor_id`),
  KEY `idx_fee_rule_id` (`fee_rule_id`),
  CONSTRAINT `fk_fee_transactions_visit_id_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fee_transactions_doctor_id_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_fee_transactions_fee_rule_id_3` FOREIGN KEY (`fee_rule_id`) REFERENCES `fee_rules` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_fee_settlements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `settlement_no` varchar(50) NOT NULL,
  `doctor_id` int(11) unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `total_actions` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Transfer Bank',
  `status` enum('draft','paid','cancelled') NOT NULL DEFAULT 'paid',
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `journal_id` int(11) unsigned DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `total_fee` decimal(15,2) DEFAULT 0.00,
  `total_visits` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settlement_no` (`settlement_no`),
  KEY `idx_idx_idx_doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurant_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL UNIQUE,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-utensils',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurant_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_no` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('empty','active') DEFAULT 'empty',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_table_no` (`table_no`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurant_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `classification` enum('resep','umum') NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) DEFAULT 100,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `calories` int(11) DEFAULT NULL,
  `protein` decimal(5,1) DEFAULT NULL,
  `carbs` decimal(5,1) DEFAULT NULL,
  `fat` decimal(5,1) DEFAULT NULL,
  `is_healthy` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`),
  KEY `idx_idx_idx_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurant_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(30) NOT NULL,
  `order_type` enum('umum','diet_pasien') DEFAULT 'umum',
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `diet_instructions` text DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `status` enum('open','cooking','ready','closed','cancelled') DEFAULT 'open',
  `payment_status` enum('unpaid','paid','billed_to_clinic') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `change_amount` decimal(15,2) DEFAULT 0.00,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `table_number` varchar(20) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `order_status` enum('pending','cooking','ready','served','cancelled') NOT NULL DEFAULT 'pending',
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_order_no` (`order_no`),
  KEY `idx_table_id` (`table_id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_restaurant_orders_table_id_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_restaurant_orders_visit_id_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY `idx_idx_idx_patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurant_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('new','cooking','ready','served') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(15,2) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_menu_id` (`menu_id`),
  CONSTRAINT `fk_restaurant_order_details_order_id_1` FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_restaurant_order_details_menu_id_2` FOREIGN KEY (`menu_id`) REFERENCES `restaurant_menus` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kitchen_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_detail_id` int(11) NOT NULL,
  `status` enum('new','cooking','ready') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_order_detail_id` (`order_detail_id`),
  CONSTRAINT `fk_kitchen_orders_order_detail_id_1` FOREIGN KEY (`order_detail_id`) REFERENCES `restaurant_order_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `contact_person` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_no` varchar(30) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `department` varchar(50) DEFAULT 'Apotek Farmasi',
  `requested_by` int(11) DEFAULT NULL,
  `status` enum('draft','submitted','verified','approved','rejected','completed') DEFAULT 'draft',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_request_no` (`request_no`),
  KEY `idx_supplier_id` (`supplier_id`),
  CONSTRAINT `fk_purchase_requests_supplier_id_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_request_id` int(11) NOT NULL,
  `item_type` enum('obat','alkes','bahan_resto','inventaris') DEFAULT 'obat',
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(30) NOT NULL DEFAULT 'Pcs',
  `estimated_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_request_id` (`purchase_request_id`),
  CONSTRAINT `fk_purchase_request_items_purchase_request_id_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_idx_idx_request_id` (`request_id`),
  KEY `idx_idx_idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_purchase_request_items_request_id_2` FOREIGN KEY (`request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_request_items_medicine_id_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_no` varchar(30) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_terms` varchar(50) DEFAULT 'Net 30 Hari',
  `notes` text DEFAULT NULL,
  `status` enum('ordered','received','cancelled') DEFAULT 'ordered',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_po_no` (`po_no`),
  KEY `idx_purchase_request_id` (`purchase_request_id`),
  KEY `idx_supplier_id` (`supplier_id`),
  CONSTRAINT `fk_purchase_orders_purchase_request_id_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_orders_supplier_id_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(11) NOT NULL,
  `item_type` enum('obat','alkes','bahan_resto','inventaris') DEFAULT 'obat',
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(30) NOT NULL DEFAULT 'Pcs',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `po_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase_order_id` (`purchase_order_id`),
  CONSTRAINT `fk_purchase_order_items_purchase_order_id_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_idx_idx_po_id` (`po_id`),
  KEY `idx_idx_idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_purchase_order_items_po_id_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_purchase_order_items_medicine_id_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `goods_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(30) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `delivery_order_no` varchar(50) DEFAULT NULL,
  `received_date` date NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `po_id` int(11) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_receipt_no` (`receipt_no`),
  KEY `idx_purchase_order_id` (`purchase_order_id`),
  CONSTRAINT `fk_goods_receipts_purchase_order_id_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `goods_receipt_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `expired_date` date NOT NULL,
  `qty_received` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(30) DEFAULT 'Pcs',
  `buy_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goods_receipt_id` (`goods_receipt_id`),
  CONSTRAINT `fk_goods_receipt_items_goods_receipt_id_1` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `idx_idx_idx_receipt_id` (`receipt_id`),
  KEY `idx_idx_idx_medicine_id` (`medicine_id`),
  CONSTRAINT `fk_goods_receipt_items_receipt_id_2` FOREIGN KEY (`receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_goods_receipt_items_medicine_id_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `location` varchar(100) NOT NULL,
  `purchase_date` date NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `useful_life_years` int(11) DEFAULT 4,
  `salvage_value` decimal(15,2) DEFAULT 0.00,
  `current_value` decimal(15,2) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `condition_status` enum('good','damaged','maintenance') DEFAULT 'good',
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `pj_employee` varchar(100) DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`),
  KEY `idx_supplier_id` (`supplier_id`),
  CONSTRAINT `fk_inventory_assets_supplier_id_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asset_depreciations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `depreciation_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `book_value_after` decimal(15,2) DEFAULT 0.00,
  `journal_entry_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` int(11) DEFAULT NULL,
  `depreciation_amount` decimal(15,2) DEFAULT 0.00,
  `accumulated_depreciation` decimal(15,2) DEFAULT 0.00,
  `book_value` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_asset_id` (`asset_id`),
  CONSTRAINT `fk_asset_depreciations_asset_id_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asset_maintenances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `technician_vendor` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('scheduled','in_progress','completed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `maintenance_date` date NOT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_asset_id` (`asset_id`),
  CONSTRAINT `fk_asset_maintenances_asset_id_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `asset_mutations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `old_location` varchar(100) NOT NULL,
  `new_location` varchar(100) NOT NULL,
  `old_pj` varchar(100) NOT NULL,
  `new_pj` varchar(100) NOT NULL,
  `mutation_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `from_location` varchar(100) DEFAULT NULL,
  `to_location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_asset_id` (`asset_id`),
  CONSTRAINT `fk_asset_mutations_asset_id_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transaction_type` (`transaction_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `step_name` varchar(50) NOT NULL,
  `step_level` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_workflow_id` (`workflow_id`),
  KEY `idx_role_id` (`role_id`),
  CONSTRAINT `fk_approval_steps_workflow_id_1` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_approval_steps_role_id_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `step_level` int(11) NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_approver_id` (`approver_id`),
  CONSTRAINT `fk_approval_requests_approver_id_1` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 1,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `table_name` varchar(50) DEFAULT '',
  `record_id` int(11) DEFAULT 0,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_audit_logs_user_id_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  KEY `idx_idx_idx_module` (`module`),
  KEY `idx_idx_idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_code` varchar(50) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `target_roles` varchar(255) NOT NULL,
  `severity` varchar(20) NOT NULL DEFAULT 'warning',
  `icon` varchar(50) DEFAULT 'fas fa-bell',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rule_code` (`rule_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_key` varchar(100) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `type` varchar(20) NOT NULL DEFAULT 'info',
  `badge` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-bell',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `target_roles` varchar(255) DEFAULT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT 'System Engine',
  `is_broadcast` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_idx_category` (`category`),
  KEY `idx_idx_key` (`notification_key`),
  KEY `idx_idx_target_user` (`target_user_id`),
  KEY `idx_idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_notification_reads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_uk_notif_user` (`notification_id`,`user_id`),
  KEY `idx_idx_user` (`user_id`),
  KEY `idx_idx_notif` (`notification_id`),
  CONSTRAINT `fk_system_notification_reads_notification_id_1` FOREIGN KEY (`notification_id`) REFERENCES `system_notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_group` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_setting_key` (`setting_key`),
  KEY `idx_idx_idx_setting_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'FITUR BARU',
  `badge_color` varchar(30) NOT NULL DEFAULT 'teal',
  `release_date` date NOT NULL,
  `summary` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `is_major` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `release_name` varchar(100) DEFAULT NULL,
  `type` enum('major','minor','patch','hotfix') DEFAULT 'minor',
  `changelog` longtext DEFAULT NULL,
  `is_installed` tinyint(1) DEFAULT 1,
  `installed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_idx_version` (`version`),
  KEY `idx_idx_published_date` (`is_published`,`release_date`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_documentations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL DEFAULT 'workflow',
  `title` varchar(255) NOT NULL,
  `target_role` varchar(100) DEFAULT 'Semua Peran',
  `badge_color` varchar(50) NOT NULL DEFAULT 'teal',
  `icon` varchar(100) NOT NULL DEFAULT 'fas fa-circle-question',
  `flow_steps` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `order_num` int(11) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `role_target` varchar(100) DEFAULT 'all',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_order_num` (`order_num`),
  KEY `idx_is_published` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_error_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `error_hash` varchar(64) NOT NULL,
  `error_level` enum('CRITICAL','ERROR','WARNING','NOTICE') NOT NULL DEFAULT 'ERROR',
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `trace` longtext DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT 1,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `level` enum('CRITICAL','ERROR','WARNING','NOTICE') NOT NULL DEFAULT 'ERROR',
  `error_code` varchar(50) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_error_hash` (`error_hash`),
  KEY `idx_is_resolved` (`is_resolved`),
  KEY `idx_idx_idx_level` (`level`),
  KEY `idx_idx_idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_performance_metrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL,
  `response_time_ms` decimal(10,2) NOT NULL,
  `memory_usage_mb` decimal(8,2) NOT NULL,
  `peak_memory_mb` decimal(8,2) NOT NULL,
  `query_count` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_idx_idx_route` (`route`),
  KEY `idx_idx_idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_slow_queries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `query_sql` text NOT NULL,
  `execution_time_ms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `caller_location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `whatsapp_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient_phone` varchar(30) NOT NULL,
  `recipient_name` varchar(150) DEFAULT NULL,
  `message_type` varchar(50) NOT NULL,
  `message_content` text NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'sent',
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Edukasi Kesehatan',
  `author` varchar(150) DEFAULT 'Tim Medis Sawamawa',
  `summary` text NOT NULL,
  `content` longtext NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `author_name` varchar(100) DEFAULT 'Tim Medis Sawamawa',
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`),
  KEY `idx_idx_idx_is_published` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. MASTER SEED DATA
-- --------------------------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Akses penuh ke seluruh modul sistem klinik, apotek, resto, dan keuangan'),
(2, 'IT', 'Administrator sistem, database, dan log audit'),
(3, 'Direksi', 'Pimpinan Utama holding company PT. ARM ERA CORPORAT'),
(4, 'Kepala Klinik', 'Penanggung jawab operasional klinik utama'),
(5, 'Dokter', 'Tenaga medis dokter spesialis / umum'),
(6, 'Perawat', 'Tenaga medis nakes pembantu poli / salon'),
(7, 'Kasir', 'Kasir utama pembayaran klinik & billing'),
(8, 'Apoteker', 'Tenaga farmasi penyiapan e-resep apotek'),
(9, 'Gudang', 'Petugas logistik stok obat & pengadaan barang'),
(10, 'Koordinator Keuangan', 'Verifikasi anggaran pengadaan & pengeluaran kas'),
(11, 'Accounting', 'Pengelola COA, pembukuan jurnal & laporan keuangan'),
(12, 'Umum', 'Umum & Inventaris, pengelola aset non-medis'),
(13, 'HRD', 'Manajemen kepegawaian, kehadiran & payroll'),
(14, 'Resto/Kasir', 'Kasir penjualan restoran POS'),
(15, 'Resto/Dapur', 'Koki & staf dapur KDS restoran'),
(16, 'Manager', 'Manager operasional lintas divisi');

-- Permissions
INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'system.settings', 'Mengatur profil perusahaan & pengaturan sistem'),
(2, 'users.manage', 'Manajemen data user & role permissions'),
(3, 'audit.view', 'Melihat log audit trail system'),
(4, 'clinic.register', 'Melakukan pendaftaran pasien online/offline'),
(5, 'clinic.soap', 'Mengisi rekam medis SOAP & e-resep dokter'),
(6, 'clinic.billing', 'Melakukan transaksi pembayaran di kasir klinik'),
(7, 'pharmacy.dispense', 'Memproses e-resep & penjualan obat bebas apotek'),
(8, 'pharmacy.stock', 'Mengelola stok persediaan obat farmasi'),
(9, 'procurement.apply', 'Mengajukan PO pengadaan obat ke keuangan'),
(10, 'procurement.verify', 'Verifikasi anggaran pengadaan PO oleh keuangan'),
(11, 'procurement.approve', 'Persetujuan akhir pengadaan PO oleh Direksi'),
(12, 'resto.order', 'Mencatat order & open bill meja restoran POS'),
(13, 'resto.kitchen', 'Mengelola display antrean pesanan makanan dapur KDS'),
(14, 'finance.manage', 'Mengelola penerimaan/pengeluaran kas & bank operasional'),
(15, 'accounting.ledger', 'Melihat jurnal umum, COA, & laporan keuangan'),
(16, 'inventory.manage', 'Mengelola inventaris aset umum non-medis'),
(17, 'hrd.payroll', 'Mengelola presensi, nakes SIP & penggajian payroll');

-- Role Permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10), (1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16), (1, 17),
(2, 1), (2, 2), (2, 3),
(3, 3), (3, 11), (3, 14), (3, 15), (3, 16), (3, 17),
(4, 3), (4, 4), (4, 5), (4, 6), (4, 7), (4, 8), (4, 9), (4, 10), (4, 17),
(5, 4), (5, 5), (5, 7), (5, 8),
(6, 4), (6, 5),
(7, 4), (7, 6), (7, 7), (7, 12), (7, 14),
(8, 7), (8, 8), (8, 9),
(9, 8), (9, 9), (9, 16),
(10, 6), (10, 10), (10, 14), (10, 15),
(11, 14), (11, 15), (11, 16),
(12, 9), (12, 16),
(13, 2), (13, 3), (13, 17),
(14, 12), (14, 14),
(15, 13),
(16, 3), (16, 4), (16, 6), (16, 8), (16, 10), (16, 12), (16, 14), (16, 17);

-- Users (All default password: 'admin123')
INSERT INTO `users` (`id`, `username`, `fullname`, `email`, `phone`, `avatar`, `bio`, `password`, `plain_password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'Dr. H. Ahmad Sawamawa, M.Kes', 'admin@sawamawamedicalcenter.id', '081234567890', 'uploads/avatars/avatar_user_1_1787377811.jpg', 'Super Administrator Utama Sistem', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 1, 'active', NOW(), NOW()),
(2, 'direktur', 'dr. Muhammad Ridwan, Sp.JP, FIHA', 'direktur@sawamawamedicalcenter.id', '081234567891', NULL, 'Direktur Medis Utama', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 3, 'active', NOW(), NOW()),
(3, 'dr_andi', 'dr. Andi Pratama, Sp.PD', 'dr.andi@sawamawamedicalcenter.id', '081234567892', NULL, 'Dokter Spesialis Penyakit Dalam', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 5, 'active', NOW(), NOW()),
(4, 'ns_rina', 'Ns. Rina Kartika, S.Kep', 'rina.nurse@sawamawamedicalcenter.id', '081234567893', NULL, 'Perawat Poliklinik', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 6, 'active', NOW(), NOW()),
(5, 'kasir_siti', 'Siti Nurhaliza, A.Md.Keb', 'kasir@sawamawamedicalcenter.id', '081234567894', NULL, 'Petugas Kasir & Pembayaran', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 7, 'active', NOW(), NOW()),
(6, 'keuangan_budi', 'Budi Santoso, S.E.', 'keuangan@sawamawamedicalcenter.id', '081234567895', NULL, 'Koordinator Keuangan & Verifikasi PO', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 10, 'active', NOW(), NOW()),
(7, 'accounting_edi', 'Edi Pramono, S.Ak.', 'accounting@sawamawamedicalcenter.id', '081234567896', NULL, 'Akuntan & Pembukuan Jurnal', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 11, 'active', NOW(), NOW()),
(8, 'resto_santi', 'Santi Rahayu', 'resto@sawamawamedicalcenter.id', '081234567897', NULL, 'Kasir Restoran Sehat', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 14, 'active', NOW(), NOW()),
(9, 'chef_joko', 'Chef Joko Susilo', 'dapur@sawamawamedicalcenter.id', '081234567898', NULL, 'Kepala Dapur Gizi Sehat', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 15, 'active', NOW(), NOW()),
(10, 'apoteker_dewi', 'apt. Dewi Lestari, S.Farm', 'apotek@sawamawamedicalcenter.id', '081234567899', NULL, 'Kepala Instalasi Farmasi', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 8, 'active', NOW(), NOW()),
(11, 'gudang_agus', 'Agus Salim, A.Md.', 'gudang@sawamawamedicalcenter.id', '081234567800', NULL, 'Petugas Logistik & Gudang', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 9, 'active', NOW(), NOW()),
(12, 'umum_bambang', 'Bambang Irawan', 'umum@sawamawamedicalcenter.id', '081234567801', NULL, 'Staff Umum & Inventaris Aset', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 12, 'active', NOW(), NOW()),
(13, 'hrd_maya', 'Maya Wulandari, S.Psi.', 'hrd@sawamawamedicalcenter.id', '081234567802', NULL, 'Manajer HRD & Kepegawaian', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 13, 'active', NOW(), NOW()),
(14, 'dr_kepala', 'dr. Hj. Siti Fatimah, M.Biomed', 'kepalaklinik@sawamawamedicalcenter.id', '081234567803', NULL, 'Kepala Pelayanan Medis Klinik', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 4, 'active', NOW(), NOW()),
(15, 'manager_doni', 'Doni Setiawan, M.M.', 'manager@sawamawamedicalcenter.id', '081234567804', NULL, 'Manager Operasional Klinik & Apotek', '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 16, 'active', NOW(), NOW());

-- Cash Registers
INSERT INTO `cash_registers` (`id`, `name`, `balance`, `status`) VALUES
(1, 'Kasir Utama', 0.00, 'open'),
(2, 'Kasir Apotek Farmasi', 0.00, 'open'),
(3, 'Kasir Resto Sehat', 0.00, 'open');

-- Payment Methods
INSERT INTO `payment_methods` (`id`, `code`, `name`, `category`, `account_number`, `account_name`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'tunai', 'Uang Tunai (Cash)', 'cash', 'KAS-01', 'Kas Kasir Utama', 'Pembayaran tunai di loket', 1, NOW(), NOW()),
(2, 'transfer_bca', 'Transfer Bank BCA', 'bank_transfer', '8735019283', 'Klinik Sawamawa', 'Rekening Operasional BCA', 1, NOW(), NOW()),
(3, 'transfer_mandiri', 'Transfer Bank Mandiri', 'bank_transfer', '1420019283741', 'Klinik Sawamawa', 'Rekening Operasional Mandiri', 1, NOW(), NOW()),
(4, 'qris', 'QRIS Dinamis / Statis (Gopay/OVO/Dana/ShopeePay)', 'qris', 'NMID: 9360012837', 'Klinik Sawamawa QRIS', 'Pembayaran digital instant', 1, NOW(), NOW()),
(5, 'debit_bca', 'Kartu Debit BCA (EDC)', 'edc', 'EDC BCA Loket 1', 'Mesin EDC Loket', 'EDC gesek kartu debit', 1, NOW(), NOW()),
(6, 'credit_card', 'Kartu Kredit (Visa / Mastercard / JCB)', 'edc', 'EDC Kartu Kredit', 'Mesin EDC', 'Kartu kredit fee 0%', 1, NOW(), NOW()),
(7, 'bpjs', 'BPJS Kesehatan / KIS', 'insurance', 'Klaim BPJS', 'BPJS Kesehatan', 'PBI & Non-PBI', 1, NOW(), NOW()),
(8, 'asuransi', 'Asuransi Swasta / Rekanan', 'insurance', 'Klaim Asuransi Swasta', 'Rekanan Asuransi', 'Prudential, Allianz, Inhealth, dll', 1, NOW(), NOW());

-- Chart of Accounts (COA)
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`) VALUES
(1, '1-101', 'Kas Kasir Utama', 'asset', NULL, 'debit', 0.00),
(2, '1-102', 'Bank BCA Operasional', 'asset', NULL, 'debit', 0.00),
(3, '1-103', 'Bank Mandiri QRIS', 'asset', NULL, 'debit', 0.00),
(4, '1-201', 'Persediaan Obat-obatan', 'asset', NULL, 'debit', 0.00),
(5, '1-301', 'Piutang Klaim Pasien / BPJS', 'asset', NULL, 'debit', 0.00),
(6, '2-101', 'Hutang Dagang Supplier PBF', 'liability', NULL, 'credit', 0.00),
(7, '2-102', 'Hutang Komisi Dokter', 'liability', NULL, 'credit', 0.00),
(8, '3-101', 'Modal Disetor Saham', 'equity', NULL, 'credit', 0.00),
(9, '4-101', 'Pendapatan Pelayanan Klinik', 'revenue', NULL, 'credit', 0.00),
(10, '4-102', 'Pendapatan Apotek Farmasi', 'revenue', NULL, 'credit', 0.00),
(11, '4-103', 'Pendapatan POS Restoran', 'revenue', NULL, 'credit', 0.00),
(12, '5-101', 'Beban HPP Obat Farmasi', 'expense', NULL, 'debit', 0.00),
(13, '6-101', 'Beban Gaji Staf Karyawan', 'expense', NULL, 'debit', 0.00),
(14, '6-102', 'Beban Komisi & Jasa Medis Dokter', 'expense', NULL, 'debit', 0.00),
(15, '1111', 'Kas Tunai Apotek & Klinik', 'asset', NULL, 'debit', 0.00),
(16, '1121', 'Kas Digital / QRIS', 'asset', NULL, 'debit', 0.00),
(17, '112', 'Bank Transfer Operasional', 'asset', NULL, 'debit', 0.00),
(18, '511', 'Obat (Untuk Pembelian Obat Lagi)', 'expense', NULL, 'debit', 0.00),
(19, '231', 'Utang Pajak Transaksi', 'liability', NULL, 'credit', 0.00),
(20, '512', 'Penunjang Medis & Lab', 'expense', NULL, 'debit', 0.00),
(21, '513', 'Obat Resep Dokter', 'expense', NULL, 'debit', 0.00),
(22, '514', 'Biaya Administrasi & Operasional', 'expense', NULL, 'debit', 0.00),
(23, '241', 'Utang Fee / Jasa Dokter', 'liability', NULL, 'credit', 0.00),
(24, '242', 'Utang Fee Karyawan', 'liability', NULL, 'credit', 0.00),
(25, '424', 'Pendapatan Jasa Dokter', 'revenue', NULL, 'credit', 0.00),
(26, '425', 'Pendapatan Fasilitas Klinik', 'revenue', NULL, 'credit', 0.00),
(27, '415', 'Pendapatan BMHP & Bahan Medis', 'revenue', NULL, 'credit', 0.00),
(28, '421', 'Pendapatan Administrasi Pasien', 'revenue', NULL, 'credit', 0.00),
(29, '422', 'Pendapatan Konseling Farmasi', 'revenue', NULL, 'credit', 0.00),
(30, '411', 'Pendapatan Tindakan Medis', 'revenue', NULL, 'credit', 0.00),
(31, '412', 'Pendapatan Penjualan Resep Apotek', 'revenue', NULL, 'credit', 0.00);

-- Transaction Account Mappings
INSERT INTO `transaction_account_mappings` (`id`, `transaction_type`, `debit_account_id`, `credit_account_id`) VALUES
(1, 'CLINIC_PAYMENT', 15, 30),
(2, 'PHARMACY_SALE', 15, 31),
(3, 'RESTO_SALE', 15, 11),
(4, 'FEE_EXPENSE', 14, 23);

-- Journal Categories (Bagi Hasil Sesuai Skema Klien)
INSERT INTO `journal_categories` (`id`, `category_code`, `category_name`, `module`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PENJUALAN_OBAT_RESEP', 'Penjualan Obat Resep Dokter', 'apotek', 'Skema bagi hasil: Kas -> Obat (Dynamic), Utang Pajak 11%, Penunjang 9%, Obat Resep 7%, ADM 24%, Fee Dokter (Dynamic)', 1, NOW(), NOW()),
(2, 'PENJUALAN_OBAT_BEBAS', 'Penjualan Obat Bebas / OTC', 'apotek', 'Skema bagi hasil: Kas -> Obat 49%, Utang Pajak 11%, Penunjang 9%, Obat Resep 7%, ADM 24%', 1, NOW(), NOW()),
(3, 'KONSULTASI_ONLINE', 'Konsultasi Online & Telemedicine', 'apotek', 'Skema: Kas -> Jasa Dokter (Rp 20.000 Fixed), Fee Dokter (Fixed/Manual), Sisa dibagi: Obat 49%, Pajak 11%, Penunjang 9%, Obat Resep 7%, ADM 24%', 1, NOW(), NOW()),
(4, 'RAWAT_JALAN_POLI', 'Pelayanan Rawat Jalan Poli & Tindakan', 'klinik', 'Skema: Kas -> Fee Dokter 66.67% (2/3), Fee Karyawan Rp 10.000, Sisa dibagi: Fasilitas 67.24%, BMHP 19.025%, Administrasi 10.9875%, Konseling 2.7475%', 1, NOW(), NOW());

-- Journal Category Rules
INSERT INTO `journal_category_rules` (`category_id`, `item_name`, `account_id`, `position`, `calc_type`, `percentage_value`, `fixed_amount_value`, `formula_code`, `sort_order`, `notes`, `is_active`) VALUES
(1, 'Kas Tunai Apotek', 15, 'debit', 'percentage', 100.00, 0.00, 'DEBIT_CASH', 1, 'Kas masuk penerimaan tunai/qris', 1),
(1, 'Obat (Untuk Pembelian Obat Lagi)', 18, 'credit', 'formula', 44.00, 0.00, 'DYNAMIC_OBAT_REMAINDER', 2, 'Porsi pembelian obat (49% dikurangi fee dokter)', 1),
(1, 'Utang Pajak', 19, 'credit', 'percentage', 11.00, 0.00, 'FIXED_PCT', 3, 'Alokasi pajak 11%', 1),
(1, 'Penunjang', 20, 'credit', 'percentage', 9.00, 0.00, 'FIXED_PCT', 4, 'Alokasi penunjang medis 9%', 1),
(1, 'Obat Resep', 21, 'credit', 'percentage', 7.00, 0.00, 'FIXED_PCT', 5, 'Alokasi obat resep 7%', 1),
(1, 'ADM & Operasional', 22, 'credit', 'percentage', 24.00, 0.00, 'FIXED_PCT', 6, 'Alokasi administrasi 24%', 1),
(1, 'Utang Fee Dokter', 23, 'credit', 'dynamic_fee', 5.00, 0.00, 'DOCTOR_FEE_PCT', 7, 'Fee resep dokter (default 5% atau dinamis per dokter)', 1),
(2, 'Kas Tunai Apotek', 15, 'debit', 'percentage', 100.00, 0.00, 'DEBIT_CASH', 1, 'Penerimaan kas penjualan bebas', 1),
(2, 'Obat (Untuk Pembelian Obat Lagi)', 18, 'credit', 'percentage', 49.00, 0.00, 'FIXED_PCT', 2, 'Alokasi pengadaan obat 49%', 1),
(2, 'Utang Pajak', 19, 'credit', 'percentage', 11.00, 0.00, 'FIXED_PCT', 3, 'Alokasi pajak 11%', 1),
(2, 'Penunjang', 20, 'credit', 'percentage', 9.00, 0.00, 'FIXED_PCT', 4, 'Alokasi penunjang 9%', 1),
(2, 'Obat Resep', 21, 'credit', 'percentage', 7.00, 0.00, 'FIXED_PCT', 5, 'Alokasi obat resep 7%', 1),
(2, 'ADM & Operasional', 22, 'credit', 'percentage', 24.00, 0.00, 'FIXED_PCT', 6, 'Alokasi ADM 24%', 1),
(3, 'Kas Tunai / Bank Apotek', 15, 'debit', 'percentage', 100.00, 0.00, 'DEBIT_CASH', 1, 'Kas masuk konsultasi online', 1),
(3, 'Pendapatan Jasa Dokter (Tetap)', 25, 'credit', 'fixed_amount', 0.00, 20000.00, 'FIXED_NOMINAL', 2, 'Jasa dokter tetap Rp 20.000', 1),
(3, 'Utang Fee Dokter (Manual/Tarif)', 23, 'credit', 'fixed_amount', 0.00, 0.00, 'DOCTOR_FEE_NOMINAL', 3, 'Nominal fee dokter tambahan jika diisi', 1),
(3, 'Obat (Sisa Basis)', 18, 'credit', 'percentage', 49.00, 0.00, 'PCT_REMAINDER', 4, '49% dari sisa bersih setelah jasa & fee dokter', 1),
(3, 'Utang Pajak (Sisa Basis)', 19, 'credit', 'percentage', 11.00, 0.00, 'PCT_REMAINDER', 5, '11% dari sisa bersih', 1),
(3, 'Penunjang (Sisa Basis)', 20, 'credit', 'percentage', 9.00, 0.00, 'PCT_REMAINDER', 6, '9% dari sisa bersih', 1),
(3, 'Obat Resep (Sisa Basis)', 21, 'credit', 'percentage', 7.00, 0.00, 'PCT_REMAINDER', 7, '7% dari sisa bersih', 1),
(3, 'ADM (Sisa Basis)', 22, 'credit', 'percentage', 24.00, 0.00, 'PCT_REMAINDER', 8, '24% dari sisa bersih', 1),
(4, 'Kas Kasir Klinik', 15, 'debit', 'percentage', 100.00, 0.00, 'DEBIT_CASH', 1, 'Penerimaan kas rawat jalan', 1),
(4, 'Dokter / Jasa Medis', 25, 'credit', 'dynamic_fee', 66.67, 0.00, 'DOCTOR_FEE_PCT', 2, 'Fee dokter (default 66.67% atau tarif tetap dokter)', 1),
(4, 'Utang Fee Karyawan', 24, 'credit', 'fixed_amount', 0.00, 10000.00, 'EMPLOYEE_FEE', 3, 'Alokasi fee staf karyawan klinik Rp 10.000', 1),
(4, 'Fasilitas Klinik', 26, 'credit', 'percentage', 67.24, 0.00, 'REMAINDER_TIER', 4, '67.24% dari sisa bersih dana klinik', 1),
(4, 'BMHP (Bahan Medis Habis Pakai)', 27, 'credit', 'percentage', 19.025, 0.00, 'REMAINDER_TIER', 5, '19.025% dari sisa bersih dana klinik', 1),
(4, 'Administrasi', 28, 'credit', 'percentage', 10.9875, 0.00, 'REMAINDER_TIER', 6, '10.9875% dari sisa bersih dana klinik', 1),
(4, 'Konseling Farmasi', 29, 'credit', 'percentage', 2.7475, 0.00, 'DYNAMIC_OBAT_REMAINDER', 7, '2.7475% dari sisa bersih dana klinik (penyeimbang)', 1);

-- Pharmacy Warehouses
INSERT INTO `pharmacy_warehouses` (`id`, `code`, `name`, `type`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'GUDANG-UTAMA', 'Gudang Pusat Farmasi', 'gudang_utama', 'Lantai 1 Sayap Barat', 1, NOW(), NOW()),
(2, 'DEPO-RJ', 'Depo Farmasi Rawat Jalan', 'depo', 'Loket Farmasi Utama', 1, NOW(), NOW()),
(3, 'DEPO-RI', 'Depo Farmasi Rawat Inap', 'depo', 'Gedung Rawat Inap Lt. 2', 1, NOW(), NOW());

-- Polikliniks
INSERT INTO `polikliniks` (`id`, `name`, `code`, `description`, `status`) VALUES
(1, 'Poli Umum', 'POLI-UMUM', 'Pemeriksaan umum dan konsultasi dokter umum', 'active'),
(2, 'Poli Gigi & Mulut', 'POLI-GIGI', 'Pemeriksaan gigi, scaling, penambalan, dan pencabutan', 'active'),
(3, 'Poli KIA & Kebidanan', 'POLI-KIA', 'Kesehatan ibu & anak, pemeriksaan kehamilan (ANC), KB, imunisasi', 'active'),
(4, 'Poli Penyakit Dalam (Sp.PD)', 'POLI-DALAM', 'Konsultasi dan penanganan spesialis penyakit dalam', 'active');

INSERT INTO `polyclinics` (`id`, `name`, `code`, `description`, `status`) VALUES
(1, 'Poli Umum', 'POLI-UMUM', 'Pemeriksaan umum dan konsultasi dokter umum', 'active'),
(2, 'Poli Gigi & Mulut', 'POLI-GIGI', 'Pemeriksaan gigi, scaling, penambalan, dan pencabutan', 'active'),
(3, 'Poli KIA & Kebidanan', 'POLI-KIA', 'Kesehatan ibu & anak, pemeriksaan kehamilan (ANC), KB, imunisasi', 'active'),
(4, 'Poli Penyakit Dalam (Sp.PD)', 'POLI-DALAM', 'Konsultasi dan penanganan spesialis penyakit dalam', 'active');

-- Services / Layanan Medis
INSERT INTO `services` (`id`, `code`, `name`, `category`, `parent_id`, `price`, `unit`, `status`) VALUES
(1, 'SRV-001', 'Konsultasi & Pemeriksaan Dokter Umum', 'Konsultasi', NULL, 50000.00, 'tindakan', 'active'),
(2, 'SRV-002', 'Konsultasi Dokter Spesialis', 'Konsultasi', NULL, 150000.00, 'tindakan', 'active'),
(3, 'SRV-003', 'Pemeriksaan & Tindakan Gigi', 'Gigi', NULL, 100000.00, 'tindakan', 'active'),
(4, 'SRV-004', 'Pemeriksaan USG Kandungan (2D/4D)', 'Kebidanan', NULL, 200000.00, 'tindakan', 'active'),
(5, 'SRV-005', 'Injeksi / Suntik Obat & Vitamin', 'Tindakan', NULL, 35000.00, 'tindakan', 'active'),
(6, 'SRV-006', 'Perawatan Luka & Hecting (Jahit Luka)', 'Tindakan', NULL, 75000.00, 'tindakan', 'active'),
(7, 'SRV-007', 'Nebulizer Inhalasi', 'Tindakan', NULL, 60000.00, 'tindakan', 'active'),
(8, 'SRV-008', 'Cek Gula Darah Sewaktu (GDS)', 'Laboratorium', NULL, 20000.00, 'tindakan', 'active'),
(9, 'SRV-009', 'Cek Asam Urat & Kolesterol Lengkap', 'Laboratorium', NULL, 50000.00, 'tindakan', 'active');

-- Doctors
INSERT INTO `doctors` (`id`, `nik_employee`, `user_id`, `name`, `specialization`, `sip_number`, `phone`, `fee_type`, `fee_per_pasien`, `prescription_fee_percent`, `polyclinic_id`, `status`) VALUES
(1, 'DOC-001', 3, 'dr. Andi Pratama, Sp.PD', 'Spesialis Penyakit Dalam', 'SIP.446/001/DS/2026', '081234567892', 'percentage', 66.67, 5.00, 4, 'active'),
(2, 'DOC-002', NULL, 'Dr. Siti Aminah', 'Dokter Umum', 'SIP.446/002/DU/2026', '081234567895', 'percentage', 66.67, 5.00, 1, 'active'),
(3, 'DOC-003', NULL, 'Drg. Hendra Wijaya', 'Dokter Gigi', 'SIP.446/003/DG/2026', '081234567896', 'percentage', 66.67, 5.00, 2, 'active'),
(4, 'DOC-004', NULL, 'Dr. Ratna Dewi, Sp.OG', 'Spesialis Kebidanan & Kandungan', 'SIP.446/004/OB/2026', '081234567897', 'percentage', 66.67, 5.00, 3, 'active');

-- Nurses
INSERT INTO `nurses` (`id`, `nik_employee`, `user_id`, `name`, `str_number`, `phone`, `polyclinic_id`, `status`) VALUES
(1, 'EMP-NRS-001', 4, 'Ns. Rina Kartika, S.Kep', 'STR-440-123-2026', '081234567893', 1, 'active'),
(2, 'EMP-NRS-002', NULL, 'Ns. Ahmad Fauzi, S.Kep', 'STR-440-124-2026', '081234567805', 2, 'active');

-- Categories
INSERT INTO `categories` (`id`, `name`, `type`) VALUES
(1, 'Analgesik & Antipiretik', 'medicine'),
(2, 'Antibiotik & Antimikroba', 'medicine'),
(3, 'Vitamin & Suplemen', 'medicine'),
(4, 'Antihistamin & Alergi', 'medicine'),
(5, 'Obat Saluran Cerna (Gastro)', 'medicine'),
(6, 'Obat Kardiovaskular & Hipertensi', 'medicine');

-- Sample Medicines & Batches
INSERT INTO `medicines` (`id`, `code`, `name`, `generic_name`, `category_id`, `form`, `unit`, `buy_price`, `sale_price`, `price`, `stock`, `min_stock`, `status`) VALUES
(1, 'MED-001', 'Paracetamol 500mg', 'Paracetamol', 1, 'tablet', 'strip', 4000.00, 7500.00, 7500.00, 150, 20, 'active'),
(2, 'MED-002', 'Amoxicillin 500mg', 'Amoxicillin Trihydrate', 2, 'capsule', 'strip', 8000.00, 15000.00, 15000.00, 80, 15, 'active'),
(3, 'MED-003', 'Vitamin C 500mg (IPI)', 'Ascorbic Acid', 3, 'tablet', 'botol', 6000.00, 12000.00, 12000.00, 100, 10, 'active'),
(4, 'MED-004', 'Cetirizine 10mg', 'Cetirizine HCl', 4, 'tablet', 'strip', 5000.00, 10000.00, 10000.00, 60, 10, 'active'),
(5, 'MED-005', 'Omeprazole 20mg', 'Omeprazole', 5, 'capsule', 'strip', 10000.00, 18000.00, 18000.00, 75, 10, 'active'),
(6, 'MED-006', 'Amlodipine 5mg', 'Amlodipine Besylate', 6, 'tablet', 'strip', 6000.00, 11000.00, 11000.00, 90, 15, 'active');

INSERT INTO `medicine_batches` (`id`, `medicine_id`, `batch_no`, `warehouse_id`, `stock`, `expired_date`, `buy_price`, `sale_price`) VALUES
(1, 1, 'BTC-PCT-202601', 1, 150, '2028-12-31', 4000.00, 7500.00),
(2, 2, 'BTC-AMX-202601', 1, 80, '2027-10-31', 8000.00, 15000.00),
(3, 3, 'BTC-VTC-202601', 1, 100, '2028-06-30', 6000.00, 12000.00),
(4, 4, 'BTC-CTZ-202601', 1, 60, '2028-08-31', 5000.00, 10000.00),
(5, 5, 'BTC-OMP-202601', 1, 75, '2027-12-31', 10000.00, 18000.00),
(6, 6, 'BTC-AML-202601', 1, 90, '2028-05-31', 6000.00, 11000.00);

-- Departments
INSERT INTO `departments` (`id`, `code`, `name`, `description`, `status`) VALUES
(1, 'MED', 'Pelayanan Medis & Keperawatan', 'Divisi dokter spesialis, dokter umum, dan perawat', 'active'),
(2, 'FARM', 'Farmasi & Pengadaan Obat', 'Divisi apoteker, asisten apoteker, dan gudang farmasi', 'active'),
(3, 'KEU', 'Keuangan, Akuntansi & Kasir', 'Divisi kasir, bendahara, dan akuntansi pembukuan', 'active'),
(4, 'RESTO', 'Restoran Sehat & Gizi', 'Divisi kuliner gizi dan layanan nutrisi klinik', 'active'),
(5, 'UMUM', 'Umum, IT & Logistik', 'Divisi maintenance sistem, logistik umum, dan kebersihan', 'active');

-- Job Positions
INSERT INTO `job_positions` (`id`, `department_id`, `code`, `title`, `base_salary_min`, `base_salary_max`, `status`) VALUES
(1, 1, 'POS-DOK-SP', 'Dokter Spesialis', 5000000.00, 15000000.00, 'active'),
(2, 1, 'POS-DOK-UM', 'Dokter Umum', 4000000.00, 8000000.00, 'active'),
(3, 1, 'POS-PERAWAT', 'Perawat Poliklinik', 2500000.00, 4500000.00, 'active'),
(4, 2, 'POS-APOTEKER', 'Apoteker Penanggung Jawab', 3500000.00, 6000000.00, 'active'),
(5, 3, 'POS-KASIR', 'Petugas Kasir Klinik', 2200000.00, 3500000.00, 'active'),
(6, 3, 'POS-AKUNTAN', 'Staf Akuntansi & Pajak', 3000000.00, 5000000.00, 'active');

-- Medicine Categories & Units
INSERT INTO `medicine_categories` (`id`, `code`, `name`, `drug_class`, `description`, `status`) VALUES
(1, 'KAT-BEBAS', 'OBAT BEBAS', 'obat_bebas', 'Obat yang dapat dibeli bebas tanpa resep dokter', 'active'),
(2, 'KAT-TERBATAS', 'OBAT BEBAS TERBATAS', 'obat_bebas_terbatas', 'Obat dengan tanda lingkaran biru dengan peringatan khusus', 'active'),
(3, 'KAT-KERAS', 'OBAT KERAS', 'obat_keras', 'Obat bertanda K merah yang wajib dengan resep dokter', 'active'),
(4, 'KAT-ANTIBIOTIK', 'ANTIBIOTIKA & ANTIINFEKSI', 'obat_keras', 'Golongan antibiotik dan kemoterapeutika', 'active'),
(5, 'KAT-ALKES', 'ALAT KESEHATAN & BMHP', 'alkes', 'Bahan medis habis pakai, spuit, kassa, infus', 'active');

INSERT INTO `units` (`id`, `code`, `name`, `category`, `status`) VALUES
(1, 'TAB', 'Tablet', 'farmasi', 'active'),
(2, 'KAP', 'Kapsul', 'farmasi', 'active'),
(3, 'BTL', 'Botol / Fls', 'farmasi', 'active'),
(4, 'STR', 'Strip', 'farmasi', 'active'),
(5, 'BOX', 'Box / Kotak', 'farmasi', 'active'),
(6, 'AMP', 'Ampul', 'farmasi', 'active'),
(7, 'VIAL', 'Vial', 'farmasi', 'active'),
(8, 'TUBE', 'Tube', 'farmasi', 'active'),
(9, 'PCS', 'Pcs / Buah', 'umum', 'active'),
(10, 'SACH', 'Sachet', 'farmasi', 'active');

-- Rooms & Beds
INSERT INTO `rooms` (`id`, `code`, `name`, `type`, `floor`, `capacity`, `tariff_per_day`, `status`) VALUES
(1, 'RM-POLI-01', 'Ruang Poliklinik Umum 1', 'poli', 'Lantai 1', 1, 0.00, 'active'),
(2, 'RM-POLI-02', 'Ruang Poliklinik Spesialis', 'poli', 'Lantai 1', 1, 0.00, 'active'),
(3, 'RM-POLI-GIGI', 'Ruang Poliklinik Gigi & Mulut', 'poli', 'Lantai 1', 1, 0.00, 'active'),
(4, 'RM-TDK-01', 'Ruang Tindakan Medis & IGD', 'igd', 'Lantai 1', 2, 50000.00, 'active'),
(5, 'RM-LAB-01', 'Ruang Laboratorium Diagnostik', 'lab', 'Lantai 1', 1, 0.00, 'active'),
(6, 'RM-FAR-01', 'Ruang Pelayanan Farmasi / Apotek', 'lainnya', 'Lantai 1', 1, 0.00, 'active'),
(7, 'RM-VIP-01', 'Ruang Rawat Inap VIP Mawar 1', 'rawat_inap', 'Lantai 2', 1, 350000.00, 'active'),
(8, 'RM-KLS1-01', 'Ruang Rawat Inap Kelas 1 Melati', 'rawat_inap', 'Lantai 2', 2, 200000.00, 'active');

INSERT INTO `beds` (`id`, `room_id`, `bed_number`, `tariff_per_day`, `status`) VALUES
(1, 4, 'BED-IGD-01', 50000.00, 'available'),
(2, 4, 'BED-IGD-02', 50000.00, 'available'),
(3, 7, 'BED-VIP-01', 350000.00, 'available'),
(4, 8, 'BED-KLS1-01', 200000.00, 'available'),
(5, 8, 'BED-KLS1-02', 200000.00, 'available');

-- Doctor Schedules
INSERT INTO `doctor_schedules` (`id`, `doctor_id`, `room_id`, `day_of_week`, `start_time`, `end_time`, `max_quota`, `status`) VALUES
(1, 1, 2, 'Senin', '08:00:00', '16:00:00', 30, 'active'),
(2, 1, 2, 'Selasa', '08:00:00', '16:00:00', 30, 'active'),
(3, 1, 2, 'Rabu', '08:00:00', '16:00:00', 30, 'active'),
(4, 1, 2, 'Kamis', '08:00:00', '16:00:00', 30, 'active'),
(5, 1, 2, 'Jumat', '08:00:00', '16:00:00', 30, 'active'),
(6, 1, 2, 'Sabtu', '08:00:00', '14:00:00', 20, 'active'),
(7, 2, 1, 'Senin', '08:00:00', '16:00:00', 40, 'active'),
(8, 2, 1, 'Selasa', '08:00:00', '16:00:00', 40, 'active'),
(9, 3, 3, 'Senin', '09:00:00', '15:00:00', 20, 'active'),
(10, 4, 2, 'Rabu', '13:00:00', '17:00:00', 25, 'active');

-- Consent Templates
INSERT INTO `consent_templates` (`id`, `tindakan_id`, `template_title`, `diagnosis_indication`, `procedure_action`, `goal_benefits`, `risks_complications`, `prognosis`, `alternative_therapies`, `status`) VALUES
(1, NULL, 'Persetujuan Tindakan Medis Umum', 'Indikasi medis pemeriksaan dan tindakan rawat jalan', 'Tindakan medis diagnostik dan kuratif sesuai standar operasional prosedur', 'Mempercepat penyembuhan dan meredakan gejala penyakit pasien', 'Reaksi alergi obat, nyeri ringan, atau memar pada area tindakan', 'Baik (Dubia ad Bonam) dengan kepatuhan terapi medis', 'Terapi konservatif medikamentosa oral', 'active'),
(2, NULL, 'Persetujuan Tindakan Pencabutan Gigi', 'Gangguan karies gigi lanjut atau impaksi gigi', 'Prosedur ekstraksi gigi dengan anestesi lokal', 'Menghilangkan sumber infeksi dan nyeri pada rongga mulut', 'Perdarahan pasca ekstraksi, pembengkakan lokal, infeksi sekunder', 'Baik dengan perawatan luka soket yang adekuat', 'Perawatan saluran akar atau penambalan', 'active');

-- Insurance Providers
INSERT INTO `insurance_providers` (`id`, `code`, `name`, `type`, `category`, `phone`, `email`, `claim_address`, `pic_name`, `discount_rate`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BPJS-KES', 'BPJS Kesehatan / KIS', 'bpjs', 'bpjs', '165', 'bpjs@sawamawamedicalcenter.id', 'Kantor Cabang BPJS Kesehatan', 'Humas BPJS', 0.00, 1, 'active', NOW(), NOW()),
(2, 'ASR-PRU', 'Prudential Life Assurance', 'asuransi_swasta', 'swasta', '1500085', 'klaim.prudential@email.com', 'Sudirman Central Business District', 'Ibu Ratna (PIC Klaim)', 0.00, 1, 'active', NOW(), NOW()),
(3, 'ASR-ALLIANZ', 'Allianz Care Indonesia', 'asuransi_swasta', 'swasta', '1500136', 'klaim.allianz@email.com', 'World Trade Center Jakarta', 'Bpk. Hendra (PIC Klaim)', 0.00, 1, 'active', NOW(), NOW());

-- Lab Tests
INSERT INTO `lab_tests` (`id`, `code`, `name`, `category`, `specimen`, `reference_range`, `normal_range`, `unit`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LAB-GDS', 'Gula Darah Sewaktu (GDS)', 'Kimia Darah', 'Darah Kapiler', '70 - 140', '70 - 140 mg/dL', 'mg/dL', 20000.00, 'active', NOW(), NOW()),
(2, 'LAB-AU', 'Asam Urat (Uric Acid)', 'Kimia Darah', 'Darah Vena / Kapiler', 'P: 2.4 - 5.7, L: 3.4 - 7.0', '3.4 - 7.0 mg/dL', 'mg/dL', 25000.00, 'active', NOW(), NOW()),
(3, 'LAB-CHOL', 'Kolesterol Total', 'Kimia Darah', 'Darah Vena / Kapiler', '< 200', '< 200 mg/dL', 'mg/dL', 30000.00, 'active', NOW(), NOW()),
(4, 'LAB-DL', 'Darah Lengkap / Rutin (CBC 3-Diff)', 'Hematologi', 'Darah EDTA', 'Hb: 12-16, Leukosit: 4000-10000', 'Normal Hematologi', 'diff', 75000.00, 'active', NOW(), NOW()),
(5, 'LAB-URINE', 'Urine Lengkap (Urinalisis)', 'Urinalisis', 'Urine Pagi Segar', 'Negatif Protein, Reduksi', 'Normal Urinalisis', 'parameter', 45000.00, 'active', NOW(), NOW());

-- Restaurant Categories & Menus
INSERT INTO `restaurant_categories` (`id`, `code`, `name`, `description`, `icon`, `is_active`, `created_at`) VALUES
(1, 'MAKANAN-SEHAT', 'Menu Makanan & Diet Gizi Sehat', 'Menu makanan seimbang rendah garam dan minyak', 'fas fa-bowl-rice', 1, NOW()),
(2, 'JUS-HERBAL', 'Jus Buah Segar & Minuman Herbal', 'Jus tanpa gula tambahan dan racikan rempah herbal', 'fas fa-glass-water', 1, NOW()),
(3, 'SNACK-SEHAT', 'Snack & Camilan Bergizi', 'Kudapan sehat tinggi serat', 'fas fa-cookie', 1, NOW());

INSERT INTO `restaurant_menus` (`id`, `category_id`, `code`, `name`, `description`, `price`, `cost_price`, `calories`, `protein`, `carbs`, `fat`, `is_healthy`, `is_available`, `created_at`) VALUES
(1, 1, 'MENU-001', 'Nasi Merah Dada Ayam Panggang Sayur', 'Nasi merah pulen, dada ayam bakar rempah, brokoli wortel rebus', 35000.00, 20000.00, 420, 38.5, 45.0, 6.2, 1, 1, NOW()),
(2, 1, 'MENU-002', 'Sup Ikan Gurame Bening Daun Kemangi', 'Sup ikan segar bening tanpa santan kaya omega-3', 40000.00, 22000.00, 310, 32.0, 15.0, 4.5, 1, 1, NOW()),
(3, 2, 'MENU-003', 'Jus Mix Antioksidan (Naga + Bit + Apel)', 'Jus murni fresh press tanpa gula tambahan', 20000.00, 10000.00, 140, 2.0, 32.0, 0.5, 1, 1, NOW()),
(4, 2, 'MENU-004', 'Wedang Jahe Merah Sereh Madu Murni', 'Minuman hangat penghangat tubuh dan imun booster', 15000.00, 7000.00, 95, 0.5, 22.0, 0.2, 1, 1, NOW());

-- System Settings
INSERT INTO `system_settings` (`setting_group`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
('general', 'clinic_name', 'Sawamawa Medical Center & Gitria Farma', 'Nama Resmi Klinik & Apotek', NOW()),
('general', 'clinic_address', 'Jl. Trans Utama No. 88, Pusat Pelayanan Terpadu', 'Alamat Lengkap', NOW()),
('general', 'clinic_phone', '(021) 8876-5432 / WhatsApp 0812-3456-7890', 'Kontak Operasional', NOW()),
('general', 'clinic_email', 'info@sawamawamedicalcenter.id', 'Email Resmi', NOW()),
('satusehat', 'satusehat_org_id', '100028174', 'Organization ID Kemenkes SatuSehat', NOW()),
('satusehat', 'satusehat_client_id', 'DEV_CLIENT_ID_SAWAMAWA', 'Client ID SatuSehat API', NOW()),
('satusehat', 'satusehat_client_secret', 'DEV_SECRET_SAWAMAWA', 'Client Secret SatuSehat', NOW());

-- Notification Rules
INSERT INTO `notification_rules` (`id`, `rule_code`, `name`, `category`, `description`, `is_active`) VALUES
(1, 'BILLING_UNPAID_ALERT', 'Tagihan Pasien Belum Lunas di Kasir', 'keuangan', 'Peringatan antrean pasien menunggu pembayaran kasir', 1),
(2, 'PRESCRIPTION_READY_ALERT', 'Resep Obat Siap Diserahkan di Apotek', 'farmasi', 'Notifikasi pasien siap dipanggil ke loket obat', 1),
(3, 'STOCK_MINIMUM_ALERT', 'Peringatan Stok Obat Menipis / Kritis', 'farmasi', 'Deteksi stok obat di bawah batas aman minimum', 1),
(4, 'ACCT_UNBALANCED_JOURNAL', 'Deteksi Jurnal Akuntansi Tidak Seimbang', 'akuntansi', 'Peringatan kritis entri debit vs kredit berselisih', 1),
(5, 'SEC_APM_ERROR_ALERT', 'Insiden Galat Sistem Tertangkap APM Engine', 'security', 'Peringatan galat runtime otomatis', 1);

-- All Migrations Record
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
('20260821000000', 'App\\Database\\Migrations\\CreateAllTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010300', 'App\\Database\\Migrations\\CreateCategoriesPolikliniksTindakan', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010400', 'App\\Database\\Migrations\\AdaptDoctorAndReferenceStructure', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010500', 'App\\Database\\Migrations\\AddDiscountToPrescriptionAndBillingDetails', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010600', 'App\\Database\\Migrations\\MakeFeeRuleIdNullableInFeeTransactions', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010700', 'App\\Database\\Migrations\\CreateMasterIcdTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010800', 'App\\Database\\Migrations\\CreateStockOpnamesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821010900', 'App\\Database\\Migrations\\CreateClinicalLettersAndLabTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821011000', 'App\\Database\\Migrations\\AlterLabResultsAndMedicalLetters', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821011100', 'App\\Database\\Migrations\\AddIcdColumnsToMedicalRecords', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821011200', 'App\\Database\\Migrations\\CreatePharmacyDirectSalesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821011300', 'App\\Database\\Migrations\\CreatePaymentMethodsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260821011400', 'App\\Database\\Migrations\\UpgradeRestoHealthyPosSystem', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260822000000', 'App\\Database\\Migrations\\CreateSystemUpdatesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260822010000', 'App\\Database\\Migrations\\UpgradeCashTransactionsSystem', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260822020000', 'App\\Database\\Migrations\\UpgradeProcurementSystem', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260822030000', 'App\\Database\\Migrations\\UpgradeAssetsAndHrdSystem', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260822040000', 'App\\Database\\Migrations\\CreateAttendanceSystem', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260826000000', 'App\\Database\\Migrations\\FixAuditLogsColumns', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260826010000', 'App\\Database\\Migrations\\CreateSystemDocumentationsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260827000000', 'App\\Database\\Migrations\\CreateEnterpriseAdvancedTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260828000000', 'App\\Database\\Migrations\\CreateSystemErrorAndPerformanceTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260829000000', 'App\\Database\\Migrations\\CreateQueueCallEventsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260904000000', 'App\\Database\\Migrations\\UpgradePharmacyFeaturesAndCoa', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260904010000', 'App\\Database\\Migrations\\CreateDynamicJournalTemplatesTables', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260905000000', 'App\\Database\\Migrations\\AddFeeTypeToDoctors', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260905000000', 'App\\Database\\Migrations\\AddTuslaEmbalaseToPrescriptionBilling', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260906010000', 'App\\Database\\Migrations\\AddOnlineToPrescriptionType', 'default', 'App', UNIX_TIMESTAMP(), 1),
('20260906020000', 'App\\Database\\Migrations\\UpdateKonsultasiOnlineRulesAndPharmacySales', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-08-28-011200', 'App\\Database\\Migrations\\AddMembershipTierToPatients', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-08-28-020000', 'App\\Database\\Migrations\\AddUpdatedAtToQueueNumbers', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2026-08-28-021000', 'App\\Database\\Migrations\\FixVisitAndQueueStatusColumns', 'default', 'App', UNIX_TIMESTAMP(), 1);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ====================================================================
-- END OF MASTER DATABASE DUMP
-- ====================================================================
