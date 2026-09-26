-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 05:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sawamawa_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `normal_balance` enum('debit','credit') NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES
(1, '1-101', 'Kas Kasir Utama', 'asset', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(2, '1-102', 'Bank BCA Operasional', 'asset', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(3, '1-103', 'Bank Mandiri QRIS', 'asset', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(4, '1-201', 'Persediaan Obat-obatan', 'asset', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(5, '1-301', 'Piutang Klaim BPJS', 'asset', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(6, '2-101', 'Hutang Dagang Supplier PBF', 'liability', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(7, '2-102', 'Hutang Komisi Dokter', 'liability', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(8, '3-101', 'Modal Disetor Saham', 'equity', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(9, '4-101', 'Pendapatan Pelayanan Klinik', 'revenue', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(10, '4-102', 'Pendapatan Apotek Farmasi', 'revenue', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(11, '4-103', 'Pendapatan POS Restoran', 'revenue', NULL, 'credit', 0.00, '2026-08-20 16:29:18'),
(12, '5-101', 'Beban HPP Obat Farmasi', 'expense', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(13, '6-101', 'Beban Gaji Staf Karyawan', 'expense', NULL, 'debit', 0.00, '2026-08-20 16:29:18'),
(14, '6-102', 'Beban Komisi & Jasa Medis Dokter', 'expense', NULL, 'debit', 0.00, '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `id` int(11) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `step_level` int(11) NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_steps`
--

CREATE TABLE `approval_steps` (
  `id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `step_name` varchar(50) NOT NULL,
  `step_level` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_steps`
--

INSERT INTO `approval_steps` (`id`, `workflow_id`, `step_name`, `step_level`, `role_id`, `created_at`) VALUES
(1, 1, 'Persetujuan Akhir Direksi', 1, 3, '2026-08-20 09:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflows`
--

CREATE TABLE `approval_workflows` (
  `id` int(11) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_workflows`
--

INSERT INTO `approval_workflows` (`id`, `transaction_type`, `description`, `created_at`) VALUES
(1, 'PROCUREMENT', 'Alur Verifikasi Pembelian Logistik', '2026-08-20 09:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `asset_depreciations`
--

CREATE TABLE `asset_depreciations` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `depreciation_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `book_value_after` decimal(15,2) DEFAULT 0.00,
  `journal_entry_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_maintenances`
--

CREATE TABLE `asset_maintenances` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `technician_vendor` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('scheduled','in_progress','completed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_mutations`
--

CREATE TABLE `asset_mutations` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `old_location` varchar(100) NOT NULL,
  `new_location` varchar(100) NOT NULL,
  `old_pj` varchar(100) NOT NULL,
  `new_pj` varchar(100) NOT NULL,
  `mutation_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT 1,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `table_name` varchar(50) DEFAULT '',
  `record_id` int(11) DEFAULT 0,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `table_name`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'LOGIN_FAILED', 'Auth', 'users', 1, NULL, 'Percobaan login gagal untuk username: superadmin', '::1', NULL, '2026-08-26 03:34:52'),
(2, 1, 'LOGIN_FAILED', 'Auth', 'users', 1, NULL, 'Percobaan login gagal untuk username: superadmin', '::1', NULL, '2026-08-26 03:34:59'),
(3, 1, 'LOGIN', 'Auth', 'users', 1, NULL, 'User superadmin login berhasil', '::1', NULL, '2026-08-26 03:35:04'),
(4, 1, 'CREATE', 'Pendaftaran Pasien', 'patients', 101, NULL, 'Mendaftarkan pasien baru [Ny. Siti Rahmawati] (No RM: RM-00101) ke Poli Spesialis Gizi Klinis', '192.168.1.15', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 08:02:16'),
(5, 2, 'UPDATE', 'Rekam Medis (SOAP)', 'patient_visits', 45, 'Diagnosis: Observasi Awal', 'Menyimpan SOAP klinis, Diagnosa Utama [E11.9 - Diabetes Mellitus Tipe 2], dan e-Resep Medis', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 08:27:16'),
(6, 2, 'UPDATE', 'Poli Gigi (Odontogram)', 'odontograms', 12, NULL, 'Memperbarui chart odontogram: Gigi 16 (Caries Media) & Gigi 21 (Composite Filling)', '192.168.1.22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 08:42:16'),
(7, 3, 'DISPENSE', 'Apotek (e-Resep)', 'prescriptions', 30, 'Status: waiting', 'Menyerahkan dan meracik tebus resep e-Resep RX-00030 (Metformin 500mg, Glimepiride 2mg)', '192.168.1.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 08:57:16'),
(8, 4, 'PAYMENT', 'Kasir Utama', 'billing_transactions', 88, 'Status: open', 'Pelunasan tagihan billing kasir pasien No. BIL-20260827-088: Rp 350.000 (Metode: QRIS Dinamis)', '192.168.1.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:12:16'),
(9, 1, 'CREATE', 'Pengadaan PO', 'purchase_orders', 14, NULL, 'Pengajuan Purchase Order PO-20260827-014 ke PT Kimia Farma Trading & Distribution (Total: Rp 8.500.000)', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:22:16'),
(10, 5, 'APPROVE', 'Pengadaan (Approval)', 'approval_requests', 14, 'Status: pending', 'Menyetujui dokumen pengadaan barang PO-20260827-014 oleh Kepala Bagian Logistik & Direksi', '192.168.1.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:32:16'),
(11, 1, 'UPDATE', 'User & Hak Akses', 'users', 6, 'Role: Staf Farmasi', 'Mengubah hak akses pengguna [apoteker_siti] menjadi Peran Apoteker & PIC Gudang', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:42:16'),
(12, 1, 'DELETE', 'Master Layanan', 'services', 99, 'Layanan Konsultasi Lama (Nonaktif)', 'Menghapus paket layanan kadaluarsa dari daftar master klinik', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:52:16'),
(13, 1, 'EXPORT', 'Backup Database', 'audit_logs', 1, NULL, 'Mengunduh salinan backup penuh database (Full SQL Dump) untuk arsip berkala', '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', '2026-08-26 09:57:16'),
(14, 1, 'OPTIMIZE', 'System Scaling', 'System Scaling', 1, NULL, 'Menjalankan optimasi & defragmentasi pada 81 tabel database', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 10:03:37'),
(15, 1, 'OPTIMIZE', 'System Scaling', 'System Scaling', 1, NULL, 'Menjalankan optimasi & defragmentasi pada 84 tabel database', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 11:02:12'),
(16, 1, 'LOGIN_FAILED', 'Auth', 'users', 1, NULL, 'Percobaan login gagal untuk username: superadmin', '::1', NULL, '2026-08-26 17:40:35'),
(17, 1, 'LOGIN', 'Auth', 'users', 1, NULL, 'User superadmin login berhasil', '::1', NULL, '2026-08-26 17:40:44');

-- --------------------------------------------------------

--
-- Table structure for table `billing_details`
--

CREATE TABLE `billing_details` (
  `id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `item_type` enum('medis','obat','resto') NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billing_transactions`
--

CREATE TABLE `billing_transactions` (
  `id` int(11) NOT NULL,
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
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_registers`
--

INSERT INTO `cash_registers` (`id`, `name`, `balance`, `status`, `created_at`) VALUES
(1, 'Kasir Utama', 0.00, 'open', '2026-08-21 03:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `cash_transactions`
--

CREATE TABLE `cash_transactions` (
  `id` int(11) NOT NULL,
  `receipt_no` varchar(30) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `cash_register_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `change_amount` decimal(15,2) DEFAULT 0.00,
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'tunai',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'POLI', 'Kategori Pelayanan Poli Klinik Spesialis / Umum', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(2, 'TINDAKAN', 'Kategori Pelayanan Tindakan Medis, Estetika & Studio', '2026-08-21 01:34:42', '2026-08-21 01:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `nik_employee`, `name`, `category_id`, `polyclinic_id`, `tindakan_id`, `fee_per_pasien`, `sip_number`, `str_number`, `str_expiry`, `status`, `created_at`) VALUES
(1, 'EMP-DOK-001', 'PAHRI', 1, 1, NULL, 0.00, 'SIP/440/001/DINKES', 'STR-PAHRI-2026', '2030-12-31', 'active', '2026-08-21 01:34:42'),
(2, 'EMP-DOK-002', 'YADI', 2, 3, 2, 100000.00, 'SIP/440/002/DINKES', 'STR-YADI-2026', '2030-12-31', 'active', '2026-08-21 01:34:42'),
(3, 'EMP-DOK-003', 'YADI', 2, 3, 3, 100000.00, 'SIP/440/003/DINKES', 'STR-YADI-2026', '2030-12-31', 'active', '2026-08-21 01:34:42'),
(4, 'DOC-GIZI-001', 'dr. Felicia Nugraha, Sp.GK', 1, 4, NULL, 0.00, '446/SIP-DS/DINKES/2024/091', 'STR-GIZI-882910', '2029-12-31', 'active', '2026-08-21 15:36:47'),
(5, 'DOC-DERM-001', 'dr. Amanda Clarissa, Sp.DVE', 1, 5, NULL, 0.00, '446/SIP-DS/DINKES/2024/092', 'STR-DERM-882920', '2029-12-31', 'active', '2026-08-21 15:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_fee_settlements`
--

CREATE TABLE `doctor_fee_settlements` (
  `id` int(11) UNSIGNED NOT NULL,
  `settlement_no` varchar(50) NOT NULL,
  `doctor_id` int(11) UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `total_actions` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Transfer Bank',
  `status` enum('draft','paid','cancelled') NOT NULL DEFAULT 'paid',
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `journal_id` int(11) UNSIGNED DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `nip`, `nik_ktp`, `name`, `department`, `position`, `phone`, `email`, `address`, `join_date`, `salary`, `allowance_position`, `allowance_transport`, `deduction_bpjs`, `bank_name`, `bank_account`, `employment_type`, `status`, `created_at`) VALUES
(1, 'NIP-2025-001', NULL, 'dr. Andi Wijaya, Sp.PD', 'Klinik', NULL, NULL, NULL, NULL, NULL, 15000000.00, 0.00, 0.00, 0.00, NULL, NULL, 'tetap', 'active', '2026-08-20 16:29:18'),
(2, 'NIP-2025-002', NULL, 'Ns. Rina Kartika, S.Kep', 'Klinik', NULL, NULL, NULL, NULL, NULL, 4500000.00, 0.00, 0.00, 0.00, NULL, NULL, 'tetap', 'active', '2026-08-20 16:29:18'),
(3, 'NIP-2025-003', NULL, 'Siti Aminah', 'Keuangan', NULL, NULL, NULL, NULL, NULL, 3800000.00, 0.00, 0.00, 0.00, NULL, NULL, 'tetap', 'active', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendances`
--

CREATE TABLE `employee_attendances` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_rules`
--

CREATE TABLE `fee_rules` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `flat_fee` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_rules`
--

INSERT INTO `fee_rules` (`id`, `service_id`, `role_id`, `percentage`, `flat_fee`, `status`, `created_at`) VALUES
(1, 2, 5, 0.00, 100000.00, 'active', '2026-08-21 01:34:42'),
(2, 3, 5, 0.00, 100000.00, 'active', '2026-08-21 01:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `fee_transactions`
--

CREATE TABLE `fee_transactions` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `fee_rule_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipts`
--

CREATE TABLE `goods_receipts` (
  `id` int(11) NOT NULL,
  `receipt_no` varchar(30) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `delivery_order_no` varchar(50) DEFAULT NULL,
  `received_date` date NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_items`
--

CREATE TABLE `goods_receipt_items` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `icd9_procedures`
--

CREATE TABLE `icd9_procedures` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `icd9_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `icd10_diagnoses`
--

CREATE TABLE `icd10_diagnoses` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `icd10_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `internal_cash_transfers`
--

CREATE TABLE `internal_cash_transfers` (
  `id` int(11) NOT NULL,
  `transfer_no` varchar(30) NOT NULL,
  `from_account_id` int(11) NOT NULL,
  `to_account_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transfer_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_assets`
--

CREATE TABLE `inventory_assets` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `journal_no` varchar(30) NOT NULL,
  `entry_date` date NOT NULL,
  `source_module` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_details`
--

CREATE TABLE `journal_entry_details` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kitchen_orders`
--

CREATE TABLE `kitchen_orders` (
  `id` int(11) NOT NULL,
  `order_detail_id` int(11) NOT NULL,
  `status` enum('new','cooking','ready') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `reference_range` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `code`, `name`, `reference_range`, `price`, `created_at`) VALUES
(1, 'LAB-CBC', 'Darah Lengkap (Complete Blood Count)', 'HB: 13-16 g/dL, Leukosit: 4000-10000', 120000.00, '2026-08-20 16:29:18'),
(2, 'LAB-GLU', 'Gula Darah Puasa (GDP)', '70 - 110 mg/dL', 45000.00, '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `master_icd9`
--

CREATE TABLE `master_icd9` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_icd9`
--

INSERT INTO `master_icd9` (`id`, `code`, `name_id`, `name_en`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, '89.52', 'Elektrokardiogram (EKG 12-Lead)', 'Electrocardiogram', 'Pemeriksaan Kardiovaskular', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(2, '88.72', 'Ekokardiografi Jantung (USG Jantung)', 'Diagnostic ultrasound of heart', 'Pemeriksaan Kardiovaskular', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(3, '88.76', 'USG Abdomen / Perut', 'Diagnostic ultrasound of abdomen and retroperitoneum', 'Pemeriksaan Radiologi / USG', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(4, '93.57', 'Perawatan & Ganti Verban Luka (Wound Dressing)', 'Application of other wound dressing', 'Tindakan Keperawatan / Bedah Minor', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(5, '86.59', 'Penjahitan Luka Terbuka (Heacting / Suture)', 'Closure of skin and subcutaneous tissue of other sites', 'Tindakan Bedah Minor', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(6, '86.04', 'Insisi & Drainase Abses Kulit', 'Other incision with drainage of skin and subcutaneous tissue', 'Tindakan Bedah Minor', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(7, '86.22', 'Debridement Luka / Pengangkatan Jaringan Mati', 'Excisional debridement of wound, infection, or burn', 'Tindakan Bedah Minor', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(8, '96.59', 'Irigasi / Cuci Telinga (Spooling Telinga)', 'Other irrigation of wound', 'Tindakan THT', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(9, '93.94', 'Terapi Nebulisasi / Inhalasi Uap', 'Respiratory medication administered by nebulizer', 'Tindakan Pulmonologi / Respirasi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(10, '99.21', 'Injeksi Antibiotik / Obat Intramuskular (IM)', 'Injection of antibiotic', 'Prosedur Injeksi & Terapi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(11, '99.18', 'Pemasangan Infus Cairan Intravena (IV Line)', 'Injection or infusion of electrolytes or other therapeutic fluid', 'Prosedur Injeksi & Terapi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(12, '90.59', 'Pemeriksaan Laboratorium Darah Rutin', 'Microscopic examination of blood', 'Pemeriksaan Laboratorium', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(13, '86.89', 'Facial Treatment & Perawatan Kulit Wajah', 'Other facial plastic and aesthetic procedures', 'Layanan Estetika Medis', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(14, '86.99', 'Terapi Perawatan Kulit Kepala / Rambut Rontok (Hairstudio)', 'Other operations on skin and subcutaneous tissue', 'Layanan Estetika Medis', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(15, '89.07', 'Konsultasi Medis & Edukasi Pasien Spesialis', 'Consultation, described as comprehensive', 'Konsultasi Medis', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `master_icd10`
--

CREATE TABLE `master_icd10` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_icd10`
--

INSERT INTO `master_icd10` (`id`, `code`, `name_id`, `name_en`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, 'I10', 'Hipertensi Esensial (Primer)', 'Essential (primary) hypertension', 'Penyakit Sistem Sirkulasi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(2, 'I20.9', 'Angina Pectoris, Tidak Spesifik', 'Angina pectoris, unspecified', 'Penyakit Sistem Sirkulasi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(3, 'I50.9', 'Gagal Jantung, Tidak Spesifik', 'Heart failure, unspecified', 'Penyakit Sistem Sirkulasi', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(4, 'E11.9', 'Diabetes Melitus Tipe 2 Tanpa Komplikasi', 'Type 2 diabetes mellitus without complications', 'Penyakit Endokrin & Metabolik', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(5, 'E78.5', 'Hiperlipidemia, Tidak Spesifik (Kolesterol Tinggi)', 'Hyperlipidemia, unspecified', 'Penyakit Endokrin & Metabolik', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(6, 'E79.0', 'Hiperurisemia Tanpa Tanda Arthritis (Asam Urat)', 'Hyperuricaemia without signs of inflammatory arthritis', 'Penyakit Endokrin & Metabolik', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(7, 'J00', 'Nasofaringitis Akut (Common Cold / Batuk Pilek)', 'Acute nasopharyngitis [common cold]', 'Penyakit Sistem Pernapasan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(8, 'J02.9', 'Faringitis Akut (Radang Tenggorokan)', 'Acute pharyngitis, unspecified', 'Penyakit Sistem Pernapasan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(9, 'J06.9', 'Infeksi Saluran Pernapasan Akut (ISPA)', 'Acute upper respiratory infection, unspecified', 'Penyakit Sistem Pernapasan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(10, 'J45.9', 'Asma Bronkial, Tidak Spesifik', 'Asthma, unspecified', 'Penyakit Sistem Pernapasan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(11, 'K29.7', 'Gastritis, Tidak Spesifik (Maag)', 'Gastritis, unspecified', 'Penyakit Sistem Pencernaan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(12, 'K30', 'Dispepsia (Gangguan Pencernaan / Asam Lambung)', 'Dyspepsia', 'Penyakit Sistem Pencernaan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(13, 'A09', 'Gastroenteritis & Kolitis Akut (Diare)', 'Infectious gastroenteritis and colitis, unspecified', 'Penyakit Infeksi & Parasit', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(14, 'A01.0', 'Demam Tifoid (Tipes)', 'Typhoid fever', 'Penyakit Infeksi & Parasit', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(15, 'A90', 'Demam Dengue (DBD Klasik)', 'Dengue fever [classical dengue]', 'Penyakit Infeksi & Parasit', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(16, 'R50.9', 'Demam (Febris), Tidak Spesifik', 'Fever, unspecified', 'Gejala & Tanda Umum', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(17, 'R51', 'Sakit Kepala (Cephalgia)', 'Headache', 'Gejala & Tanda Umum', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(18, 'R42', 'Pusing & Vertigo', 'Dizziness and giddiness', 'Gejala & Tanda Umum', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(19, 'M79.1', 'Mialgia (Nyeri Otot)', 'Myalgia', 'Penyakit Otot & Rangka', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(20, 'L20.9', 'Dermatitis Atopik (Eksim Kulit)', 'Atopic dermatitis, unspecified', 'Penyakit Kulit', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(21, 'L70.0', 'Acne Vulgaris (Jerawat)', 'Acne vulgaris', 'Penyakit Kulit & Estetika', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(22, 'L65.9', 'Alopesia / Rambut Rontok, Tidak Spesifik', 'Non-scarring hair loss, unspecified', 'Penyakit Kulit & Estetika', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(23, 'H10.9', 'Konjungtivitis, Tidak Spesifik (Sakit Mata)', 'Conjunctivitis, unspecified', 'Penyakit Mata', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38'),
(24, 'Z00.0', 'Pemeriksaan Medis Umum (Medical Check-up)', 'General medical examination', 'Faktor Kontak Layanan Kesehatan', 'active', '2026-08-21 09:22:38', '2026-08-21 09:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `medical_letters`
--

CREATE TABLE `medical_letters` (
  `id` int(11) UNSIGNED NOT NULL,
  `letter_no` varchar(50) NOT NULL,
  `letter_type` enum('sakit','sehat','rujukan') NOT NULL DEFAULT 'sakit',
  `visit_id` int(11) UNSIGNED NOT NULL,
  `patient_id` int(11) UNSIGNED NOT NULL,
  `doctor_id` int(11) UNSIGNED DEFAULT NULL,
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
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `icd10_code` varchar(20) DEFAULT NULL,
  `icd9_code` varchar(20) DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('bebas','keras') NOT NULL,
  `unit` varchar(20) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `min_stock` int(11) DEFAULT 10,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `code`, `name`, `type`, `unit`, `price`, `min_stock`, `status`, `created_at`) VALUES
(1, 'MED-PCT500', 'Paracetamol 500mg Tablet', 'bebas', 'tablet', 1000.00, 100, 'active', '2026-08-21 15:13:43'),
(2, 'MED-IBU400', 'Ibuprofen 400mg Kaplet', 'bebas', 'kaplet', 1500.00, 50, 'active', '2026-08-21 15:13:44'),
(3, 'MED-ANTASIDA', 'Antasida Doen Tablet Kunyah', 'bebas', 'tablet', 800.00, 80, 'active', '2026-08-21 15:13:44'),
(4, 'MED-PROMAG', 'Promag Tablet Kunyah (Dus/Blister)', 'bebas', 'tablet', 1200.00, 60, 'active', '2026-08-21 15:13:44'),
(5, 'MED-OBH100', 'OBH Tropica Plus Anak Sirup 100ml', 'bebas', 'botol', 22000.00, 15, 'active', '2026-08-21 15:13:44'),
(6, 'MED-SANMOL-SYR', 'Sanmol Paracetamol Sirup 60ml', 'bebas', 'botol', 25000.00, 20, 'active', '2026-08-21 15:13:44'),
(7, 'MED-VITC500', 'Vitamin C 500mg (IPI / Enervon-C)', 'bebas', 'tablet', 1500.00, 100, 'active', '2026-08-21 15:13:44'),
(8, 'MED-BETADINE60', 'Betadine Antiseptic Solution 60ml', 'bebas', 'botol', 35000.00, 10, 'active', '2026-08-21 15:13:44'),
(9, 'MED-MINYAK-KAYU', 'Minyak Kayu Putih Cap Lang 60ml', 'bebas', 'botol', 28000.00, 15, 'active', '2026-08-21 15:13:44'),
(10, 'MED-ORALIT', 'Oralit Garam Rehidrasi Sachet', 'bebas', 'sachet', 2000.00, 50, 'active', '2026-08-21 15:13:44'),
(11, 'MED-DIAPET', 'Diapet Kapsul Herbal Antidiare', 'bebas', 'kapsul', 1500.00, 40, 'active', '2026-08-21 15:13:44'),
(12, 'MED-INSTO', 'Insto Regular Tetes Mata 7.5ml', 'bebas', 'botol', 18500.00, 15, 'active', '2026-08-21 15:13:44'),
(13, 'MED-HYDROCORT', 'Hydrocortisone Cream 2.5% 5gr', 'bebas', 'tube', 12000.00, 15, 'active', '2026-08-21 15:13:44'),
(14, 'MED-AMX500', 'Amoxicillin 500mg Kaplet', 'keras', 'kaplet', 1500.00, 100, 'active', '2026-08-21 15:13:44'),
(15, 'MED-CFX100', 'Cefixime 100mg Kapsul', 'keras', 'kapsul', 4500.00, 50, 'active', '2026-08-21 15:13:44'),
(16, 'MED-CIP500', 'Ciprofloxacin 500mg Tablet', 'keras', 'tablet', 2000.00, 40, 'active', '2026-08-21 15:13:44'),
(17, 'MED-AML5', 'Amlodipine 5mg Tablet', 'keras', 'tablet', 1200.00, 80, 'active', '2026-08-21 15:13:44'),
(18, 'MED-AML10', 'Amlodipine 10mg Tablet', 'keras', 'tablet', 1800.00, 80, 'active', '2026-08-21 15:13:44'),
(19, 'MED-CAP25', 'Captopril 25mg Tablet', 'keras', 'tablet', 800.00, 60, 'active', '2026-08-21 15:13:44'),
(20, 'MED-MET500', 'Metformin HCl 500mg Tablet', 'keras', 'tablet', 1000.00, 100, 'active', '2026-08-21 15:13:44'),
(21, 'MED-GLI2', 'Glimepiride 2mg Tablet', 'keras', 'tablet', 2200.00, 40, 'active', '2026-08-21 15:13:44'),
(22, 'MED-OMP20', 'Omeprazole 20mg Kapsul', 'keras', 'kapsul', 2500.00, 60, 'active', '2026-08-21 15:13:44'),
(23, 'MED-LAN30', 'Lansoprazole 30mg Kapsul', 'keras', 'kapsul', 3500.00, 50, 'active', '2026-08-21 15:13:44'),
(24, 'MED-CTZ10', 'Cetirizine 10mg Tablet', 'keras', 'tablet', 1200.00, 60, 'active', '2026-08-21 15:13:44'),
(25, 'MED-DEX05', 'Dexamethasone 0.5mg Tablet', 'keras', 'tablet', 600.00, 100, 'active', '2026-08-21 15:13:44'),
(26, 'MED-SAL2', 'Salbutamol 2mg Tablet', 'keras', 'tablet', 800.00, 50, 'active', '2026-08-21 15:13:44'),
(27, 'MED-ASAM-MEF500', 'Asam Mefenamat 500mg Kaplet', 'keras', 'kaplet', 1500.00, 80, 'active', '2026-08-21 15:13:44'),
(28, 'MED-ALLOP100', 'Allopurinol 100mg Tablet (Asam Urat)', 'keras', 'tablet', 1200.00, 60, 'active', '2026-08-21 15:13:44'),
(29, 'MED-SIMV10', 'Simvastatin 10mg Tablet (Kolesterol)', 'keras', 'tablet', 1800.00, 60, 'active', '2026-08-21 15:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_batches`
--

CREATE TABLE `medicine_batches` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `buy_price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `expired_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicine_batches`
--

INSERT INTO `medicine_batches` (`id`, `medicine_id`, `batch_no`, `buy_price`, `stock`, `expired_date`, `created_at`) VALUES
(1, 1, 'BCH-PCT26A', 450.00, 0, '2027-12-31', '2026-08-21 15:13:43'),
(2, 1, 'BCH-PCT26B', 480.00, 0, '2028-06-30', '2026-08-21 15:13:44'),
(3, 2, 'BCH-IBU26', 750.00, 0, '2027-10-15', '2026-08-21 15:13:44'),
(4, 3, 'BCH-ATD26', 350.00, 0, '2027-08-20', '2026-08-21 15:13:44'),
(5, 4, 'BCH-PMG26', 650.00, 0, '2028-01-10', '2026-08-21 15:13:44'),
(6, 5, 'BCH-OBH26', 16000.00, 0, '2027-11-20', '2026-08-21 15:13:44'),
(7, 6, 'BCH-SNM26', 18500.00, 0, '2028-03-15', '2026-08-21 15:13:44'),
(8, 7, 'BCH-VTC26', 800.00, 0, '2028-05-10', '2026-08-21 15:13:44'),
(9, 8, 'BCH-BTD26', 26000.00, 0, '2028-09-30', '2026-08-21 15:13:44'),
(10, 9, 'BCH-MKP26', 21000.00, 0, '2029-01-01', '2026-08-21 15:13:44'),
(11, 10, 'BCH-ORL26', 900.00, 0, '2028-04-12', '2026-08-21 15:13:44'),
(12, 11, 'BCH-DPT26', 800.00, 0, '2027-12-01', '2026-08-21 15:13:44'),
(13, 12, 'BCH-INS26', 13500.00, 0, '2027-09-15', '2026-08-21 15:13:44'),
(14, 13, 'BCH-HYD26', 7500.00, 0, '2027-11-01', '2026-08-21 15:13:44'),
(15, 14, 'BCH-AMX26A', 700.00, 0, '2027-12-31', '2026-08-21 15:13:44'),
(16, 14, 'BCH-AMX26B', 750.00, 0, '2028-06-30', '2026-08-21 15:13:44'),
(17, 15, 'BCH-CFX26', 2800.00, 0, '2028-02-18', '2026-08-21 15:13:44'),
(18, 16, 'BCH-CIP26', 1100.00, 0, '2027-10-30', '2026-08-21 15:13:44'),
(19, 17, 'BCH-AML5-26', 500.00, 0, '2028-04-25', '2026-08-21 15:13:44'),
(20, 18, 'BCH-AML10-26', 800.00, 0, '2028-05-15', '2026-08-21 15:13:44'),
(21, 19, 'BCH-CAP26', 350.00, 0, '2027-11-10', '2026-08-21 15:13:44'),
(22, 20, 'BCH-MET26', 450.00, 0, '2028-07-20', '2026-08-21 15:13:44'),
(23, 21, 'BCH-GLI26', 1200.00, 0, '2027-09-05', '2026-08-21 15:13:44'),
(24, 22, 'BCH-OMP26', 1100.00, 0, '2028-03-30', '2026-08-21 15:13:44'),
(25, 23, 'BCH-LAN26', 1800.00, 0, '2028-06-15', '2026-08-21 15:13:44'),
(26, 24, 'BCH-CTZ26', 500.00, 0, '2028-08-10', '2026-08-21 15:13:44'),
(27, 25, 'BCH-DEX26', 250.00, 0, '2027-10-25', '2026-08-21 15:13:44'),
(28, 26, 'BCH-SAL26', 350.00, 0, '2027-12-15', '2026-08-21 15:13:44'),
(29, 27, 'BCH-AMF26', 700.00, 0, '2028-01-20', '2026-08-21 15:13:44'),
(30, 28, 'BCH-ALP26', 550.00, 0, '2028-05-15', '2026-08-21 15:13:44'),
(31, 29, 'BCH-SMV26', 850.00, 0, '2028-04-10', '2026-08-21 15:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20260821000000', 'App\\Database\\Migrations\\CreateAllTables', 'default', 'App', 1787243342, 1),
(2, '20260821010300', 'App\\Database\\Migrations\\CreateCategoriesPolikliniksTindakan', 'default', 'App', 1787249962, 2),
(3, '20260821010400', 'App\\Database\\Migrations\\AdaptDoctorAndReferenceStructure', 'default', 'App', 1787276069, 3),
(4, '20260821010500', 'App\\Database\\Migrations\\AddDiscountToPrescriptionAndBillingDetails', 'default', 'App', 1787278676, 4),
(5, '20260821010600', 'App\\Database\\Migrations\\MakeFeeRuleIdNullableInFeeTransactions', 'default', 'App', 1787283153, 5),
(6, '20260821010700', 'App\\Database\\Migrations\\CreateMasterIcdTables', 'default', 'App', 1787304061, 6),
(7, '20260821010800', 'App\\Database\\Migrations\\CreateStockOpnamesTable', 'default', 'App', 1787305199, 7),
(8, '20260821010900', 'App\\Database\\Migrations\\CreateClinicalLettersAndLabTables', 'default', 'App', 1787321507, 8),
(9, '20260821011000', 'App\\Database\\Migrations\\AlterLabResultsAndMedicalLetters', 'default', 'App', 1787321847, 9),
(10, '20260821011100', 'App\\Database\\Migrations\\AddIcdColumnsToMedicalRecords', 'default', 'App', 1787322740, 10),
(11, '20260821011200', 'App\\Database\\Migrations\\CreatePharmacyDirectSalesTable', 'default', 'App', 1787323527, 11),
(12, '20260821011300', 'App\\Database\\Migrations\\CreatePaymentMethodsTable', 'default', 'App', 1787323870, 12),
(13, '20260821011400', 'App\\Database\\Migrations\\UpgradeRestoHealthyPosSystem', 'default', 'App', 1787326567, 13),
(14, '20260822010000', 'App\\Database\\Migrations\\UpgradeCashTransactionsSystem', 'default', 'App', 1787330611, 14),
(15, '20260822020000', 'App\\Database\\Migrations\\UpgradeProcurementSystem', 'default', 'App', 1787367190, 15),
(16, '20260822030000', 'App\\Database\\Migrations\\UpgradeAssetsAndHrdSystem', 'default', 'App', 1787368209, 16),
(17, '20260822040000', 'App\\Database\\Migrations\\CreateAttendanceSystem', 'default', 'App', 1787368502, 17),
(18, '20260822000000', 'App\\Database\\Migrations\\CreateSystemUpdatesTable', 'default', 'App', 1787380636, 18),
(19, '20260826000000', 'App\\Database\\Migrations\\FixAuditLogsColumns', 'default', 'App', 1787704725, 19),
(20, '20260826010000', 'App\\Database\\Migrations\\CreateSystemDocumentationsTable', 'default', 'App', 1787714910, 20),
(21, '20260827000000', 'App\\Database\\Migrations\\CreateEnterpriseAdvancedTables', 'default', 'App', 1787764753, 21),
(22, '20260828000000', 'App\\Database\\Migrations\\CreateSystemErrorAndPerformanceTables', 'default', 'App', 1787766530, 22);

-- --------------------------------------------------------

--
-- Table structure for table `notification_rules`
--

CREATE TABLE `notification_rules` (
  `id` int(11) NOT NULL,
  `rule_code` varchar(50) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `target_roles` varchar(255) NOT NULL,
  `severity` varchar(20) NOT NULL DEFAULT 'warning',
  `icon` varchar(50) DEFAULT 'fas fa-bell',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_rules`
--

INSERT INTO `notification_rules` (`id`, `rule_code`, `rule_name`, `category`, `description`, `target_roles`, `severity`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ACCT_UNBALANCED_JOURNAL', 'Peringatan Keseimbangan Jurnal Akuntansi', 'accounting', 'Mendeteksi dan memperingatkan secara real-time jika terdapat jurnal umum yang tidak balance (Debit != Kredit).', 'accounting,keuangan,super admin,administrator', 'danger', 'fas fa-scale-unbalanced', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(2, 'ACCT_AGING_RECEIVABLES', 'Peringatan Piutang Jatuh Tempo (>30 Hari)', 'accounting', 'Memberi notifikasi jika terdapat piutang pasien atau asuransi yang melewati batas waktu 30 hari.', 'accounting,keuangan,super admin', 'warning', 'fas fa-file-invoice-dollar', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(3, 'SEC_FAILED_LOGIN_ALERT', 'Peringatan Percobaan Login Gagal Beruntun', 'security', 'Mendeteksi potensi ancaman keamanan atau brute-force attack pada otentikasi login sistem.', 'super admin,administrator,it / technical support', 'danger', 'fas fa-shield-halved', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(4, 'SEC_APM_ERROR_ALERT', 'Peringatan Galat Aplikasi di APM Engine', 'security', 'Memunculkan notifikasi instan kepada tim IT ketika terjadi unhandled error di server.', 'super admin,administrator,it / technical support', 'danger', 'fas fa-bug', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(5, 'PHARM_LOW_STOCK', 'Peringatan Stok Obat Menipis / Kritis', 'pharmacy', 'Memberi peringatan saat total stok obat mencapai atau berada di bawah batas minimum stok.', 'apoteker,asisten apoteker,bagian pengadaan,super admin', 'warning', 'fas fa-triangle-exclamation', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(6, 'PHARM_FEFO_EXPIRED', 'Peringatan Batch Obat Mendekati Kadaluarsa', 'pharmacy', 'Peringatan FEFO untuk obat aktif yang akan expired dalam kurun waktu 60 hari ke depan.', 'apoteker,asisten apoteker,super admin', 'warning', 'fas fa-calendar-xmark', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(7, 'CLINIC_QUEUE_WAITING', 'Peringatan Antrean Pasien Menunggu Pemeriksaan', 'clinical', 'Notifikasi real-time saat pasien baru terdaftar menunggu panggilan dokter/perawat di poliklinik.', 'dokter spesialis,dokter umum,dokter gigi,perawat,super admin', 'teal', 'fas fa-user-clock', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(8, 'CASHIER_UNPAID_BILLING', 'Peringatan Tagihan Pasien Siap Bayar di Kasir', 'cashier', 'Notifikasi ketika pasien selesai dilayani di poli/farmasi dan siap melakukan pembayaran kasir.', 'kasir,staf keuangan,super admin', 'warning', 'fas fa-cash-register', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(9, 'HRD_LEAVE_PENDING', 'Peringatan Pengajuan Cuti Menunggu Otorisasi', 'hr', 'Notifikasi permohonan cuti kerja karyawan baru yang membutuhkan persetujuan HRD / Manajemen.', 'hrd / sdm,kepala hrd,super admin', 'info', 'fas fa-id-badge', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09'),
(10, 'PROC_APPROVAL_PENDING', 'Peringatan Otorisasi Pengadaan Barang / PO', 'procurement', 'Notifikasi berkas Purchase Order yang memerlukan persetujuan otorisasi Direksi / Manajemen.', 'direksi,kepala klinik,super admin', 'danger', 'fas fa-signature', 1, '2026-08-27 02:58:09', '2026-08-27 02:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `nurses`
--

CREATE TABLE `nurses` (
  `id` int(11) NOT NULL,
  `nik_employee` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nurses`
--

INSERT INTO `nurses` (`id`, `nik_employee`, `name`, `polyclinic_id`, `status`, `created_at`) VALUES
(1, 'EMP-NRS-001', 'Ns. Rina Kartika, S.Kep', 1, 'active', '2026-08-20 16:29:18'),
(2, 'EMP-NRS-002', 'Ns. Ahmad Fauzi, S.Kep', 2, 'active', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `odontograms`
--

CREATE TABLE `odontograms` (
  `id` int(11) UNSIGNED NOT NULL,
  `visit_id` int(11) UNSIGNED DEFAULT NULL,
  `patient_id` int(11) UNSIGNED NOT NULL,
  `tooth_number` varchar(10) NOT NULL,
  `condition_code` varchar(20) NOT NULL DEFAULT 'normal',
  `condition_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `place_of_birth` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(15) DEFAULT NULL,
  `bpjs_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` int(11) NOT NULL,
  `no_visit` varchar(30) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'umum',
  `status` enum('waiting','triage','examining','prescription','cashier','completed','cancelled') DEFAULT 'waiting',
  `visit_type` enum('poli','tindakan') DEFAULT 'poli',
  `service_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` enum('cash','qris','transfer','debit','credit','insurance') NOT NULL DEFAULT 'cash',
  `account_number` varchar(100) DEFAULT NULL,
  `account_name` varchar(150) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `code`, `name`, `category`, `account_number`, `account_name`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'tunai', 'Tunai / Cash', 'cash', NULL, 'Kasir Utama', 'Pembayaran tunai langsung di kasir', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(2, 'qris', 'QRIS Dinamis / Statis', 'qris', 'NMID: ID1020030040050', 'Sawamawa Medical Center', 'Scan QRIS BCA/Mandiri/GoPay/OVO/ShopeePay/DANA', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(3, 'transfer_bca', 'Transfer Bank BCA', 'transfer', '8920192831', 'PT Sawamawa Medical Center', 'Rekening Operasional BCA', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(4, 'transfer_bri', 'Transfer Bank BRI', 'transfer', '012901002345501', 'Sawamawa Medical Center', 'Rekening Penerimaan BRI', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(5, 'transfer_mandiri', 'Transfer Bank Mandiri', 'transfer', '1610009876543', 'Sawamawa Medical Center', 'Rekening Bank Mandiri', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(6, 'debit', 'Kartu Debit (EDC)', 'debit', 'EDC BCA / Mandiri / BRI', 'Mesin EDC Kasir', 'Gesek kartu debit semua bank via EDC', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(7, 'credit', 'Kartu Kredit (Visa / Mastercard)', 'credit', 'EDC Kartu Kredit', 'Mesin EDC Kasir', 'Pembayaran Kartu Kredit Visa/Mastercard/JCB', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(8, 'bpjs', 'BPJS Kesehatan', 'insurance', 'PKS BPJS Kesehatan', 'Klaim BPJS', 'Jaminan Pelayanan Peserta BPJS Kesehatan', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58'),
(9, 'asuransi', 'Asuransi Swasta / Rekanan', 'insurance', 'Klaim Asuransi Swasta', 'Rekanan Asuransi', 'Prudential, Allianz, Inhealth, Mandiri AXA, dll', 1, '2026-08-21 14:50:58', '2026-08-21 14:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'system.settings', 'Mengatur profil perusahaan & pengaturan sistem', '2026-08-20 16:29:18'),
(2, 'users.manage', 'Manajemen data user & role permissions', '2026-08-20 16:29:18'),
(3, 'audit.view', 'Melihat log audit trail system', '2026-08-20 16:29:18'),
(4, 'clinic.register', 'Melakukan pendaftaran pasien online/offline', '2026-08-20 16:29:18'),
(5, 'clinic.soap', 'Mengisi rekam medis SOAP & e-resep dokter', '2026-08-20 16:29:18'),
(6, 'clinic.billing', 'Melakukan transaksi pembayaran di kasir klinik', '2026-08-20 16:29:18'),
(7, 'pharmacy.dispense', 'Memproses e-resep & penjualan obat bebas apotek', '2026-08-20 16:29:18'),
(8, 'pharmacy.stock', 'Mengelola stok persediaan obat farmasi', '2026-08-20 16:29:18'),
(9, 'procurement.apply', 'Mengajukan PO pengadaan obat ke keuangan', '2026-08-20 16:29:18'),
(10, 'procurement.verify', 'Verifikasi anggaran pengadaan PO oleh keuangan', '2026-08-20 16:29:18'),
(11, 'procurement.approve', 'Persetujuan akhir pengadaan PO oleh Direksi', '2026-08-20 16:29:18'),
(12, 'resto.order', 'Mencatat order & open bill meja restoran POS', '2026-08-20 16:29:18'),
(13, 'resto.kitchen', 'Mengelola display antrean pesanan makanan dapur KDS', '2026-08-20 16:29:18'),
(14, 'finance.manage', 'Mengelola penerimaan/pengeluaran kas & bank operasional', '2026-08-20 16:29:18'),
(15, 'accounting.ledger', 'Melihat jurnal umum, COA, & laporan keuangan', '2026-08-20 16:29:18'),
(16, 'inventory.manage', 'Mengelola inventaris aset umum non-medis', '2026-08-20 16:29:18'),
(17, 'hrd.payroll', 'Mengelola presensi, nakes SIP & penggajian payroll', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_sales`
--

CREATE TABLE `pharmacy_sales` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_sale_details`
--

CREATE TABLE `pharmacy_sale_details` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dosage_instruction` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `polikliniks`
--

CREATE TABLE `polikliniks` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `polikliniks`
--

INSERT INTO `polikliniks` (`id`, `category_id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'SPESIALIS JANTUNG', 'Poli Pelayanan Kesehatan Jantung & Pembuluh Darah', 'active', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(2, 1, 'SPESIALIS GIZI', 'Poli Pelayanan Konsultasi & Diet Gizi', 'active', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(3, 1, 'Poli Spesialis Gizi Klinis', 'Pelayanan asesmen status gizi, terapi nutrisi medik, program diet DM, Hipertensi, dan obesitas.', 'active', '2026-08-21 15:36:30', '2026-08-21 15:36:30'),
(4, 1, 'Poli Estetika & Dermal Care', 'Pelayanan perawatan kesehatan kulit medis, dermal care, dan terapi estetika.', 'active', '2026-08-21 15:36:30', '2026-08-21 15:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `polyclinics`
--

CREATE TABLE `polyclinics` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `polyclinics`
--

INSERT INTO `polyclinics` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'SPESIALIS JANTUNG', 'Poli Spesialis Jantung', 'active', '2026-08-21 01:34:42'),
(2, 'SPESIALIS GIZI', 'Poli Spesialis Gizi', 'active', '2026-08-21 01:34:42'),
(3, 'Tindakan Saja', 'Pelayanan Tindakan', 'active', '2026-08-21 01:34:42'),
(4, 'Poli Spesialis Gizi Klinis', 'Pelayanan asesmen status gizi, terapi nutrisi medik, program diet DM, Hipertensi, dan obesitas.', 'active', '2026-08-21 15:36:30'),
(5, 'Poli Estetika & Dermal Care', 'Pelayanan perawatan kesehatan kulit medis, dermal care, dan terapi estetika.', 'active', '2026-08-21 15:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `status` enum('waiting','processing','completed','cancelled') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_details`
--

CREATE TABLE `prescription_details` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `status` enum('served','bought_outside','cancelled') DEFAULT 'served',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `po_no` varchar(30) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `order_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_terms` varchar(50) DEFAULT 'Net 30 Hari',
  `notes` text DEFAULT NULL,
  `status` enum('ordered','received','cancelled') DEFAULT 'ordered',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `item_type` enum('obat','alkes','bahan_resto','inventaris') DEFAULT 'obat',
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(30) NOT NULL DEFAULT 'Pcs',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `request_no` varchar(30) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `department` varchar(50) DEFAULT 'Apotek Farmasi',
  `requested_by` int(11) DEFAULT NULL,
  `status` enum('draft','submitted','verified','approved','rejected','completed') DEFAULT 'draft',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `item_type` enum('obat','alkes','bahan_resto','inventaris') DEFAULT 'obat',
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `unit` varchar(30) NOT NULL DEFAULT 'Pcs',
  `estimated_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue_numbers`
--

CREATE TABLE `queue_numbers` (
  `id` int(11) NOT NULL,
  `polyclinic_id` int(11) NOT NULL,
  `queue_no` varchar(10) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `status` enum('waiting','called','completed','no-show','cancelled') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menus`
--

CREATE TABLE `restaurant_menus` (
  `id` int(11) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `classification` enum('resep','umum') NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) DEFAULT 100,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_menus`
--

INSERT INTO `restaurant_menus` (`id`, `code`, `name`, `description`, `category`, `classification`, `price`, `stock`, `is_active`, `created_at`) VALUES
(1, NULL, 'Nasi Diet Jantung Gizi Rendah Garam', NULL, 'makanan', 'resep', 45000.00, 100, 1, '2026-08-20 16:29:18'),
(2, NULL, 'Salad Buah Tinggi Protein & Serat', NULL, 'makanan', 'resep', 35000.00, 100, 1, '2026-08-20 16:29:18'),
(3, NULL, 'Nasi Goreng Spesial Era', NULL, 'makanan', 'umum', 30000.00, 100, 1, '2026-08-20 16:29:18'),
(4, NULL, 'Jus Wortel Murni', NULL, 'minuman', 'umum', 15000.00, 100, 1, '2026-08-20 16:29:18'),
(5, 'MKN-01', 'Nasi Merah Dada Ayam Panggang Herbal', 'Tinggi protein, rendah lemak jenuh dengan tumis brokoli wortel', 'makanan', 'umum', 38000.00, 100, 1, '2026-08-21 15:36:07'),
(6, 'MKN-02', 'Salmon Panggang Lemon & Mashed Potato Sehat', 'Kaya asam lemak Omega-3 dan antioksidan alami', 'makanan', 'umum', 55000.00, 100, 1, '2026-08-21 15:36:07'),
(7, 'MKN-03', 'Sup Ayam Jamur Tofu Bening Organik', 'Kuah kaldu alami tanpa MSG, ramah pencernaan dan lambung', 'makanan', 'umum', 28000.00, 100, 1, '2026-08-21 15:36:07'),
(8, 'MKN-04', 'Salad Sayur Segar Dressing Extra Virgin Olive Oil', 'Kombinasi selada romaine, tomat ceri, jagung, dan alpukat segar', 'makanan', 'umum', 25000.00, 100, 1, '2026-08-21 15:36:07'),
(9, 'MKN-05', 'Bubur Oat Gizi Ayam Suwir & Telur Rebus', 'Kaya serat beta-glukan untuk menjaga kestabilan gula darah dan kolesterol', 'makanan', 'umum', 22000.00, 100, 1, '2026-08-21 15:36:07'),
(10, 'MNM-01', 'Cold-Pressed Detox Green Juice (Bayam, Apel, Timun)', 'Membantu proses detoksifikasi tubuh dan meningkatkan kesegaran seluler', 'minuman', 'umum', 24000.00, 100, 1, '2026-08-21 15:36:07'),
(11, 'MNM-02', 'Fresh Beetroot & Apple Energy Booster Juice', 'Meningkatkan sirkulasi darah dan stamina alami tubuh', 'minuman', 'umum', 25000.00, 100, 1, '2026-08-21 15:36:07'),
(12, 'MNM-03', 'Wedang Jahe Merah & Madu Hutan Murni', 'Menghangatkan tubuh, meredakan inflamasi dan meningkatkan imun', 'minuman', 'umum', 18000.00, 100, 1, '2026-08-21 15:36:07'),
(13, 'MNM-04', 'Infused Water Lemon Mint & Chia Seeds 500ml', 'Hidrasi optimal kaya serat larut dan vitamin C alami', 'minuman', 'umum', 15000.00, 100, 1, '2026-08-21 15:36:07'),
(14, 'GIZI-DM', 'Paket Diet Diabetes Melitus 1500 kkal (3x Makan)', 'Formula gizi terkontrol indeks glikemik rendah terukur standar spesialis gizi', 'diet_gizi', 'resep', 85000.00, 100, 1, '2026-08-21 15:36:07'),
(15, 'GIZI-HT', 'Paket Diet Rendah Garam & Hipertensi (DASH Diet)', 'Natrium < 1200mg/hari dengan asupan kalium dan magnesium optimal', 'diet_gizi', 'resep', 80000.00, 100, 1, '2026-08-21 15:36:07'),
(16, 'GIZI-PURIN', 'Paket Diet Rendah Purin & Asam Urat', 'Bebas jeroan, daging merah dan ekstrak ragi untuk stabilisasi kadar asam urat', 'diet_gizi', 'resep', 75000.00, 100, 1, '2026-08-21 15:36:07'),
(17, 'GIZI-POSTOP', 'Paket Pemulihan Tinggi Protein Pasca Tindakan / Bedah', 'Diperkaya albumin ikan gabus dan mikronutrien untuk regenerasi jaringan', 'diet_gizi', 'resep', 95000.00, 100, 1, '2026-08-21 15:36:07'),
(18, 'GIZI-LAMBUNG', 'Paket Diet Lambung Halus / Gastritis & GERD Friendly', 'Tekstur lembut non-asam non-pedas untuk proteksi mukosa lambung', 'diet_gizi', 'resep', 70000.00, 100, 1, '2026-08-21 15:36:07'),
(19, 'SKIN-01', 'Dermatological Broad-Spectrum Sunscreen SPF50+ PA++++', 'Formula non-komedogenik medis untuk perlindungan UV harian dan pasca laser', 'skincare', 'umum', 125000.00, 100, 1, '2026-08-21 15:36:07'),
(20, 'SKIN-02', 'Gentle Skin Barrier Foam Cleanser 100ml', 'pH seimbang 5.5 dengan ekstrak oat dan ceramide, aman untuk kulit sensitif', 'skincare', 'umum', 85000.00, 100, 1, '2026-08-21 15:36:07'),
(21, 'SKIN-03', 'Hydrating Ceramide & Hyaluronic Gel Moisturizer 50gr', 'Mengunci kelembapan mendalam dan memperkuat skin barrier', 'skincare', 'umum', 110000.00, 100, 1, '2026-08-21 15:36:07'),
(22, 'SKIN-04', 'Post-Procedure Calming & Soothing Centella Gel', 'Meredakan kemerahan dan iritasi setelah tindakan facial atau peeling medis', 'skincare', 'umum', 95000.00, 100, 1, '2026-08-21 15:36:07'),
(23, 'SKIN-05', 'Medical Brightening Serum Alpha Arbutin & Niacinamide 10%', 'Membantu mencerahkan flek hitam dan meratakan warna kulit', 'skincare', 'umum', 140000.00, 100, 1, '2026-08-21 15:36:07'),
(24, 'SUP-01', 'Pure Whey Protein Isolate Medical Grade Sachet (Box 10s)', '25g protein murni per sachet tanpa pemanis buatan untuk terapi nutrisi', 'suplemen', 'umum', 175000.00, 100, 1, '2026-08-21 15:36:07'),
(25, 'SUP-02', 'High Potency Multivitamin & Zinc Immunity Booster (30 Caps)', 'Kombinasi lengkap vitamin A, B-Complex, C, D3, E, dan Zinc elemental', 'suplemen', 'umum', 90000.00, 100, 1, '2026-08-21 15:36:07'),
(26, 'SUP-03', 'Deep Sea Fish Oil Omega-3 1000mg (60 Softgels)', 'EPA 360mg / DHA 240mg untuk kesehatan kardiovaskular dan daya ingat', 'suplemen', 'umum', 135000.00, 100, 1, '2026-08-21 15:36:07'),
(27, 'SUP-04', 'Marine Collagen Peptide + Glutathione Drink (10 Sachets)', 'Mendukung elastisitas kulit dan kesehatan sendi', 'suplemen', 'umum', 160000.00, 100, 1, '2026-08-21 15:36:07'),
(28, 'SUP-05', 'Probiotics Multi-Strain Digestive Health (30 Kapsul)', '10 Miliar CFU probiotik hidup untuk keseimbangan flora usus', 'suplemen', 'umum', 120000.00, 100, 1, '2026-08-21 15:36:07');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_orders`
--

CREATE TABLE `restaurant_orders` (
  `id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_order_details`
--

CREATE TABLE `restaurant_order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('new','cooking','ready','served') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` int(11) NOT NULL,
  `table_no` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('empty','active') DEFAULT 'empty',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `table_no`, `capacity`, `status`, `created_at`) VALUES
(1, 'Meja 01', 4, 'empty', '2026-08-20 16:29:18'),
(2, 'Meja 02', 2, 'empty', '2026-08-20 16:29:18'),
(3, 'Meja 03', 6, 'empty', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Akses penuh ke seluruh modul sistem', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(2, 'IT', 'Administrator sistem, database, dan log audit', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(3, 'Direksi', 'Pimpinan Utama holding company PT. ARM ERA CORPORAT', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(4, 'Kepala Klinik', 'Penanggung jawab operasional klinik utama', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(5, 'Dokter', 'Tenaga medis dokter spesialis / umum', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(6, 'Perawat', 'Tenaga medis nakes pembantu poli / salon', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(7, 'Kasir', 'Kasir utama pembayaran klinik & billing', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(8, 'Apoteker', 'Tenaga farmasi penyiapan e-resep apotek', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(9, 'Gudang', 'Petugas logistik stok obat & pengadaan barang', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(10, 'Koordinator Keuangan', 'Verifikasi anggaran pengadaan & pengeluaran kas', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(11, 'Accounting', 'Pengelola COA, pembukuan jurnal & laporan keuangan', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(12, 'Umum', 'Umum & Inventaris, pengelola aset non-medis', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(13, 'HRD', 'Manajemen kepegawaian, kehadiran & payroll', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(14, 'Resto/Kasir', 'Kasir penjualan restoran POS', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(15, 'Resto/Dapur', 'Koki & staf dapur KDS restoran', '2026-08-20 16:29:18', '2026-08-20 16:29:18'),
(16, 'Manager', 'Manager operasional lintas divisi', '2026-08-20 16:29:18', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(2, 1),
(2, 2),
(2, 3),
(3, 3),
(3, 11),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 17),
(5, 5),
(6, 4),
(6, 5),
(7, 6),
(7, 7),
(7, 12),
(8, 7),
(8, 8),
(8, 9),
(9, 8),
(9, 9),
(9, 16),
(10, 6),
(10, 10),
(10, 14),
(10, 15),
(11, 14),
(11, 15),
(11, 16),
(12, 9),
(12, 16),
(13, 2),
(13, 3),
(13, 17),
(14, 12),
(15, 13),
(16, 3),
(16, 4),
(16, 6),
(16, 8),
(16, 10),
(16, 12),
(16, 14),
(16, 17);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `code`, `name`, `category`, `parent_id`, `status`, `created_at`) VALUES
(1, 'SRV-001', 'Konsultasi Spesialis Jantung', 'klinik', NULL, 'active', '2026-08-20 16:29:18'),
(2, 'SRV-002', 'Konsultasi Spesialis Gizi', 'klinik', NULL, 'active', '2026-08-20 16:29:18'),
(3, 'SRV-003', 'Pemeriksaan EKG Jantung', 'klinik', NULL, 'active', '2026-08-20 16:29:18'),
(4, 'SRV-004', 'Hairstudio', 'tindakan', 6, 'active', '2026-08-20 16:29:18'),
(5, 'SRV-005', 'Tindakan Estetik (Facial Treatment)', 'tindakan', 7, 'active', '2026-08-20 16:29:18'),
(8, 'TDK-GIZI-IND', 'Pelayanan Terapi Gizi Klinis', 'tindakan', NULL, 'active', '2026-08-21 15:36:47'),
(9, 'TDK-GIZI-01', 'Konsultasi & Asesmen Komposisi Tubuh (BIA Scanner)', 'tindakan', 8, 'active', '2026-08-21 15:36:47'),
(10, 'TDK-GIZI-02', 'Perencanaan Program Diet Medis Terpersonalisasi (Meal Plan)', 'tindakan', 8, 'active', '2026-08-21 15:36:47'),
(11, 'TDK-GIZI-03', 'Konsultasi Diet Terapi Penyakit Kronis (DM / Hipertensi / Ginjal)', 'tindakan', 8, 'active', '2026-08-21 15:36:47'),
(12, 'TDK-GIZI-04', 'Edukasi Gizi & Pantauan Kepatuhan Nutrisi Pasien', 'tindakan', 8, 'active', '2026-08-21 15:36:47'),
(13, 'TDK-DERM-IND', 'Pelayanan Estetika Medis & Dermal Care', 'tindakan', NULL, 'active', '2026-08-21 15:36:47'),
(14, 'TDK-DERM-01', 'Medical Facial Deep Cleansing & Ozone Detox', 'tindakan', 13, 'active', '2026-08-21 15:36:47'),
(15, 'TDK-DERM-02', 'Medical Chemical Peeling Brightening Anti-Aging', 'tindakan', 13, 'active', '2026-08-21 15:36:47'),
(16, 'TDK-DERM-03', 'Post-Procedure Soothing Care & LED Light Therapy', 'tindakan', 13, 'active', '2026-08-21 15:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `service_prices`
--

CREATE TABLE `service_prices` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_prices`
--

INSERT INTO `service_prices` (`id`, `service_id`, `price`, `created_at`) VALUES
(1, 1, 150000.00, '2026-08-20 16:29:18'),
(2, 2, 150000.00, '2026-08-20 16:29:18'),
(3, 3, 100000.00, '2026-08-20 16:29:18'),
(4, 4, 250000.00, '2026-08-20 16:29:18'),
(5, 5, 200000.00, '2026-08-20 16:29:18'),
(6, 9, 150000.00, '2026-08-21 15:36:47'),
(7, 10, 200000.00, '2026-08-21 15:36:47'),
(8, 11, 175000.00, '2026-08-21 15:36:47'),
(9, 12, 100000.00, '2026-08-21 15:36:47'),
(10, 14, 250000.00, '2026-08-21 15:36:47'),
(11, 15, 350000.00, '2026-08-21 15:36:47'),
(12, 16, 200000.00, '2026-08-21 15:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `transaction_type` enum('pembelian','penjualan','resep','retur','adjustment','opname','expired','rusak') NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `qty_in` int(11) DEFAULT 0,
  `qty_out` int(11) DEFAULT 0,
  `balance` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opnames`
--

CREATE TABLE `stock_opnames` (
  `id` int(11) NOT NULL,
  `opname_no` varchar(30) NOT NULL,
  `opname_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('draft','adjusted') DEFAULT 'adjusted',
  `total_items` int(11) DEFAULT 0,
  `total_discrepancy` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_details`
--

CREATE TABLE `stock_opname_details` (
  `id` int(11) NOT NULL,
  `opname_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `system_stock` int(11) NOT NULL,
  `physical_stock` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `pic_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `code`, `name`, `address`, `phone`, `pic_name`, `email`, `bank_name`, `bank_account`, `status`, `created_at`) VALUES
(1, 'SUPP-001', 'PT Kalbe Farma Tbk', 'Jl. Industri Raya No. 45, Jakarta', '081199887766', NULL, NULL, 'BCA', '123-456-7890', 'active', '2026-08-20 16:29:18'),
(2, 'SUPP-002', 'PT Kimia Farma Trading & Distribution', 'Jl. Veteran No. 12, Mataram', '085611223344', NULL, NULL, 'Mandiri', '161-00-1122-3344', 'active', '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `system_documentations`
--

CREATE TABLE `system_documentations` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_documentations`
--

INSERT INTO `system_documentations` (`id`, `category`, `title`, `target_role`, `badge_color`, `icon`, `flow_steps`, `summary`, `content`, `order_num`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'workflow', 'Pelayanan Pasien Rawat Jalan & Rekam Medis (SOAP)', 'Poliklinik & Medis', 'teal', 'fas fa-hospital-user', '[{\"title\":\"1. Pendaftaran\",\"sub\":\"Online \\/ Loket FO\",\"icon\":\"fas fa-mobile-screen-button\",\"color\":\"text-teal\"},{\"title\":\"2. Triase Perawat\",\"sub\":\"TTV & Keluhan\",\"icon\":\"fas fa-heart-pulse\",\"color\":\"text-danger\"},{\"title\":\"3. Periksa Dokter\",\"sub\":\"SOAP, ICD & E-Resep\",\"icon\":\"fas fa-user-doctor\",\"color\":\"text-primary\"},{\"title\":\"4. Farmasi & Kasir\",\"sub\":\"Obat & Billing\",\"icon\":\"fas fa-receipt\",\"color\":\"text-success\"}]', 'Alur terpadu pelayanan rawat jalan mulai dari pendaftaran (mandiri online / loket), antrean display suara, triase TTV perawat, pemeriksaan dokter SOAP & ICD-10, telaah resep farmasi, hingga pelunasan di kasir.', '1. **Pendaftaran Pasien:** Pasien mendaftar secara online melalui portal web atau didaftarkan oleh resepsionis di menu *Klinik > Pendaftaran Pasien*. Sistem otomatis menerbitkan No Kunjungan (`VS-XXXX`) dan No Antrean (`A-XXX`).\n2. **Triase Perawat:** Perawat memanggil antrean pasien melalui tombol suara otomatis di menu *Klinik > Antrean Poliklinik* dan menginput asesmen tanda vital (TTV).\n3. **Pemeriksaan Dokter (RME SOAP):** Dokter membuka menu *Klinik > Rekam Medis (SOAP)*, memilih diagnosa ICD-10, tindakan medis, serta meresepkan obat secara elektronik.\n4. **Farmasi & Kasir:** Resep otomatis muncul di antrean apotek untuk diracik, dan kasir langsung menarik seluruh rincian biaya untuk diterbitkan kwitansi pembayarannya.', 1, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(2, 'workflow', 'Restoran Sehat, POS Meja & Kitchen Display System (KDS)', 'Resto & Dapur Gizi', 'warning', 'fas fa-utensils', '[{\"title\":\"1. Input POS Resto\",\"sub\":\"Pilih Meja \\/ Diet\",\"icon\":\"fas fa-utensils\",\"color\":\"text-warning\"},{\"title\":\"2. KDS Dapur Memasak\",\"sub\":\"Layar Koki & Bel\",\"icon\":\"fas fa-fire-burner\",\"color\":\"text-danger\"},{\"title\":\"3. Pembayaran \\/ Billed\",\"sub\":\"Tunai \\/ Kasir Medis\",\"icon\":\"fas fa-cash-register\",\"color\":\"text-success\"}]', 'Alur pemesanan makanan/minuman sehat atau diet gizi pasien rawat jalan/inap terintegrasi dengan layar dapur koki dan kasir.', '1. **Pemesanan POS:** Pelayan memilih nomor meja atau pasien diet medis di menu *Resto > POS Resto*.\n2. **KDS Dapur:** Layar dapur di menu *Resto > Dapur Gizi (KDS)* berbunyi dan menampilkan pesanan. Koki mengklik *Mulai Masak* lalu *Selesai Masak*.\n3. **Pembayaran:** Kasir menerima pembayaran tunai/QRIS atau memilih *Billed to Clinic* agar tagihan digabungkan ke kasir utama klinik.', 2, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(3, 'workflow', 'Pengadaan Obat Farmasi & Multi-Batch Tracking', 'Farmasi & Pengadaan', 'info', 'fas fa-truck-ramp-box', '[{\"title\":\"1. Permintaan (PR)\",\"sub\":\"Stok Menipis\",\"icon\":\"fas fa-clipboard-list\",\"color\":\"text-info\"},{\"title\":\"2. Approval PO\",\"sub\":\"Disetujui Direktur\",\"icon\":\"fas fa-signature\",\"color\":\"text-warning\"},{\"title\":\"3. Penerimaan (GR)\",\"sub\":\"Batch & Exp Date\",\"icon\":\"fas fa-truck-ramp-box\",\"color\":\"text-teal\"},{\"title\":\"4. Stok & Jurnal\",\"sub\":\"Auto Hutang PBF\",\"icon\":\"fas fa-boxes-stacked\",\"color\":\"text-success\"}]', 'Alur pengadaan obat mulai dari Purchase Request (PR), verifikasi bertingkat approval Direktur, penerimaan barang (GR) dengan nomor batch & expired date, hingga pencatatan hutang dagang supplier.', '1. **Purchase Request (PR):** Asisten apoteker mengajukan PR saat stok obat mencapai batas minimum.\n2. **Approval & PO:** Direktur/Manajemen menyetujui approval di menu *Pengadaan > Approval Request*, lalu PO diterbitkan ke Supplier PBF.\n3. **Penerimaan Barang (GR):** Saat obat tiba, gudang menginput Goods Receipt lengkap dengan Nomor Batch dan Tanggal Kedaluwarsa.\n4. **Update Stok & Jurnal:** Stok bertambah seketika dan Journal Engine mencatat jurnal hutang dagang.', 3, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(4, 'workflow', 'Akuntansi Otomatis & Cara Membaca Laporan Keuangan (Untuk Pemilik / Non-Akuntan)', 'Pimpinan / Akuntansi', 'teal', 'fas fa-scale-balanced', '[{\"title\":\"1. Saldo Awal\",\"sub\":\"Uang Kas & Modal\",\"icon\":\"fas fa-coins\",\"color\":\"text-warning\"},{\"title\":\"2. Auto Journal\",\"sub\":\"Otomatis dari Kasir\",\"icon\":\"fas fa-receipt\",\"color\":\"text-teal\"},{\"title\":\"3. Buku Besar\",\"sub\":\"Mutasi per Rekening\",\"icon\":\"fas fa-book-journal-whills\",\"color\":\"text-primary\"},{\"title\":\"4. 4 Laporan BI\",\"sub\":\"Untung\\/Rugi & Neraca\",\"icon\":\"fas fa-chart-pie\",\"color\":\"text-success\"}]', 'Panduan praktis bahasa sederhana bagi pemilik atau staf non-akuntan untuk mengoperasikan pembukuan, mengecek laba/rugi, dan membaca neraca kekayaan klinik secara mudah.', '### 💡 Mengapa Anda Tidak Perlu Pusing dengan Akuntansi?\nSistem Sawamawa telah dilengkapi **Otomatisasi Jurnal (Journal Engine)**. Anda **TIDAK PERLU** membuat jurnal debet/kredit secara manual! Setiap kali kasir menerima uang, apotek menjual obat, resto menerima pesanan, atau dokter melayani pasien, sistem **otomatis mencatat pembukuan akuntansi di latar belakang**.\n\n### 📖 Cara Mudah Membaca 4 Laporan Keuangan:\n1. **Laba Rugi Komprehensif (Menu: Akuntansi > Laporan Keuangan > Tab I):**\n   * Menjawab: *Berapa keuntungan bersih klinik bulan ini?*\n   * Rumus: `Total Pemasukan Pasien & Resto` - `Biaya Obat & Beban Gaji/Listrik` = **Laba Bersih**.\n2. **Posisi Keuangan / Neraca (Tab II):**\n   * Menjawab: *Berapa total kekayaan aset klinik saat ini?*\n   * Kolom Kiri (**Aset**): Uang Kas + Tabungan Bank + Nilai Stok Obat + Peralatan Medis/Gedung.\n   * Kolom Kanan (**Pasiva**): Modal Pemilik + Sisa Hutang ke Supplier Obat. Nilai kiri dan kanan **selalu sama (100% Balance)**.\n3. **Arus Kas (Tab III):**\n   * Menjawab: *Uang tunai fisik di kasir & bank bertambah atau berkurang?*\n4. **Analisa Omset Unit Bisnis (Tab IV):**\n   * Menjawab: *Unit usaha mana yang paling banyak menghasilkan uang?* (Poli Medis, Apotek, Resto, atau Lab).\n\n### ⚙️ Cara Memulai Saldo Awal (Menu: Akuntansi > Saldo Awal Sistem):\n1. Masukkan saldo uang riil di laci kasir dan tabungan bank klinik saat ini.\n2. Masukkan taksiran total nilai stok obat yang ada di rak apotek.\n3. Masukkan modal awal pribadi yang Anda gunakan mendirikan klinik.\n4. Klik **Simpan & Terapkan Saldo Awal Sistem** -> Selesai! Neraca otomatis seimbang.', 4, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(5, 'role_guide', 'Panduan Operasional Resepsionis / Front Office', 'Pendaftaran / FO', 'teal', 'fas fa-hospital-user', NULL, 'Tata cara mendaftarkan pasien baru/lama, memproses pendaftaran online, dan mencetak kartu berobat ber-barcode.', '1. Buka menu **Klinik > Pendaftaran Pasien**.\n2. Klik tombol **+ Pendaftaran Baru**.\n3. Untuk **Pasien Baru**: Isi biodata lengkap (NIK, Nama, Tanggal Lahir, Alamat, No HP) -> Pilih Poli & Dokter -> Klik *Simpan & Terbitkan Antrean*.\n4. Untuk **Pasien Lama**: Masukkan No RM / NIK pada kolom cari -> Pilih Dokter -> Klik *Daftarkan Kunjungan*.\n5. Pasien online otomatis muncul di daftar kunjungan hari ini tanpa perlu registrasi ulang.', 1, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(6, 'role_guide', 'Panduan Operasional Perawat (Triase & TTV)', 'Perawat', 'danger', 'fas fa-user-nurse', NULL, 'Panduan pemanggilan antrean suara dan penginputan tanda-tanda vital pasien sebelum masuk ruang dokter.', '1. Buka menu **Klinik > Antrean Poliklinik**.\n2. Klik tombol **Panggil Suara** untuk memanggil pasien ke ruang periksa.\n3. Klik tombol **Triase / Asesmen** pada baris pasien.\n4. Masukkan Tekanan Darah (Sistol/Diastol), Nadi, Suhu, Pernapasan, BB, dan TB -> Klik **Simpan Asesmen**.', 2, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(7, 'role_guide', 'Panduan Dokter Spesialis & Umum (RME SOAP)', 'Dokter', 'primary', 'fas fa-user-doctor', NULL, 'Pengisian rekam medis SOAP, pencarian kode ICD-10/9, tindakan medis, e-prescribing, dan pembuatan surat rujukan.', '1. Buka menu **Klinik > Rekam Medis (SOAP)**.\n2. Pilih pasien dari antrean periksa.\n3. Isi kolom **Subjective, Objective, Assessment, dan Plan**.\n4. Pilih kode diagnosa penyakit pada kolom pencarian ICD-10.\n5. Pada bagian E-Resep, pilih obat dan tentukan dosis serta aturan pakai -> Klik **Simpan Rekam Medis & Kirim Resep**.', 3, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(8, 'role_guide', 'Panduan Apoteker & Gudang Farmasi', 'Apoteker', 'info', 'fas fa-prescription-bottle-alt', NULL, 'Telaah resep masuk, penyiapan obat FEFO, pencetakan etiket, penjualan bebas OTC, dan stok opname obat.', '1. Buka menu **Apotek > Resep Dokter** untuk memproses e-resep dokter.\n2. Klik **Siapkan Obat** -> Sistem otomatis mengalokasikan batch obat yang paling mendekati tanggal kedaluwarsa (FEFO).\n3. Klik **Selesai Racik & Cetak Etiket** untuk mencetak label pemakaian obat.\n4. Untuk penjualan obat bebas, gunakan menu **Apotek > Penjualan Bebas**.', 4, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(9, 'role_guide', 'Panduan Kasir Utama & Billing', 'Kasir', 'success', 'fas fa-cash-register', NULL, 'Penerimaan tagihan pelayanan medis & obat, pembayaran multi-metode, pencetakan kwitansi, dan tutup shift kasir.', '1. Buka menu **Keuangan > Kasir Utama**.\n2. Klik tagihan pasien yang berstatus *Menunggu Pembayaran*.\n3. Pilih metode pembayaran (Tunai, QRIS Bank Mandiri/BCA, Transfer Bank, atau BPJS).\n4. Masukkan nominal uang yang diterima -> Klik **Bayar & Cetak Kwitansi**.\n5. Pada akhir jam kerja, buka menu **Keuangan > Rekap Kasir & Shift** untuk melakukan tutup shift.', 5, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(10, 'faq', 'Bagaimana jika pasien mendaftar online tetapi tidak membawa tiket cetak?', 'Pendaftaran / FO', 'teal', 'fas fa-circle-question', NULL, 'Solusi verifikasi kedatangan pasien pendaftaran online tanpa tiket fisik.', 'Petugas pendaftaran cukup mencari **Nama Pasien** atau **NIK KTP** pada tabel pendaftaran hari ini. Data pasien dan nomor antreannya sudah otomatis aktif di sistem.', 1, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(11, 'faq', 'Bagaimana jika koneksi internet terputus saat dokter sedang mengisi SOAP?', 'Dokter', 'primary', 'fas fa-circle-question', NULL, 'Fitur draft autosave pada formulir SOAP dokter.', 'Sistem menyimpan draft SOAP secara berkala di browser. Ketika koneksi pulih dan halaman dimuat ulang, teks pemeriksaan yang belum tersimpan akan dipulihkan otomatis.', 2, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(12, 'faq', 'Bagaimana cara mengubah stok obat jika terdapat selisih fisik di gudang?', 'Apoteker', 'info', 'fas fa-circle-question', NULL, 'Prosedur penyesuaian stok opname farmasi.', 'Buka menu **Apotek > Stock Opname** -> Klik *+ Input Opname Baru* -> Masukkan jumlah fisik riil di rak -> Sistem otomatis menyesuaikan kartu stok dan membukukan selisih ke akun beban penyesuaian persediaan.', 3, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(13, 'faq', 'Bagaimana jika salah ketik saldo awal akun atau ingin mengosongkan kembali ke Rp 0?', 'Keuangan / Akuntansi', 'warning', 'fas fa-coins', NULL, 'Koreksi dan reset saldo awal kas, bank, persediaan, dan modal pembukuan sistem.', 'Buka menu **Akuntansi > Saldo Awal Sistem**.\n1. Untuk mengoreksi nominal: Klik pada kolom akun yang salah ketik, masukkan nominal baru (atau angka `0`), lalu klik **Simpan & Terapkan Saldo Awal Sistem**.\n2. Untuk membersihkan seluruh form: Klik tombol **Bersihkan Input ke 0**.\n3. Untuk menghapus total seluruh pembukuan saldo awal: Klik tombol merah **Hapus & Reset Semua Saldo ke 0**.', 4, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(14, 'faq', 'Bagaimana cara mengedit atau menghapus jurnal umum jika terjadi salah input?', 'Keuangan / Akuntansi', 'teal', 'fas fa-file-lines', NULL, 'Mekanisme pengeditan jurnal manual dan koreksi transaksi otomatis.', '1. **Jurnal Manual & Penyesuaian:** Buka menu **Akuntansi > Jurnal Umum**. Pada baris jurnal yang bersangkutan, klik ikon biru **Edit (Pensil)** untuk mengubah nominal/akun, atau klik ikon merah **Hapus (Tong Sampah)** untuk membatalkan jurnal tersebut. Sistem akan otomatis memulihkan saldo akun ke posisi semula.\n2. **Jurnal Otomatis (Kasir/Apotek/Resto):** Bertanda kunci (🔒) agar data rekam medis dan fisik obat tetap akurat. Koreksi dilakukan di modul sumbernya (misal pembatalan billing kasir) atau dengan menginput **Jurnal Penyesuaian / Pembalik (Reversing Entry)** di menu Jurnal Umum.', 5, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(15, 'faq', 'Bagaimana cara melihat riwayat rekam medis pasien langsung dari menu pendaftaran?', 'Pendaftaran / Medis', 'success', 'fas fa-notes-medical', NULL, 'Melihat seluruh riwayat kunjungan, TTV, SOAP, dan resep pasien dengan sekali klik.', 'Buka menu **Pendaftaran Pasien**. Pada tabel daftar pasien, klik nomor rekam medis (misalnya `RM-000001`) atau nama pasien. Jendela modal interaktif akan langsung menampilkan bio pasien, riwayat tanda vital, catatan SOAP dokter, kode diagnosa ICD-10, serta terapi e-resep obat dari seluruh kunjungan terdahulu.', 6, 1, 1, '2026-08-26 17:01:13', '2026-08-26 17:01:13'),
(16, 'workflow', 'Odontogram FDI 32 Gigi & Triase IGD (Poli Gigi & Medis)', 'Dokter Gigi & Perawat', 'teal', 'fas fa-tooth', '[{\"title\":\"1. Buka Tab Odontogram\",\"sub\":\"Di Rekam Medis SOAP\",\"icon\":\"fas fa-tooth\",\"color\":\"text-teal\"},{\"title\":\"2. Pilih Nomor Gigi\",\"sub\":\"FDI Gigi 11 s\\/d 48\",\"icon\":\"fas fa-hand-pointer\",\"color\":\"text-primary\"},{\"title\":\"3. Pilih Kondisi Gigi\",\"sub\":\"Caries\\/Filling\\/Missing\",\"icon\":\"fas fa-palette\",\"color\":\"text-warning\"},{\"title\":\"4. Auto-Save ke RME\",\"sub\":\"Tersimpan Permanen\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Panduan pencatatan kondisi 32 gigi pasien berbasis format internasional FDI World Dental Federation dan skala triase kegawatan IGD.', '1. **Buka RME Pasien:** Akses menu *Klinik > Rekam Medis (SOAP)*, lalu pilih pasien yang ditangani.\n2. **Tab Odontogram:** Klik tab *4. Odontogram Poli Gigi*. Chart 32 gigi akan ditampilkan secara visual.\n3. **Diagnosa Gigi:** Klik gigi yang bermasalah, lalu pilih jenis kondisi klinis (Normal, Caries, Filling, Missing, Crown, Radix, Extract).\n4. **Triase Pasien:** Skala Triase ATS 1-5 (Resusitasi, Emergensi, Urgensi, Semi-Urgensi, Non-Urgensi) dapat diset langsung pada formulir SOAP perawat/dokter.', 16, 1, 1, '2026-08-26 17:26:21', '2026-08-26 17:52:45'),
(17, 'workflow', 'Buku Kartu Stok Digital & Monitoring FEFO Obat', 'Apoteker & Farmasi', 'success', 'fas fa-book-medical', '[{\"title\":\"1. Pilih Obat \\/ Alkes\",\"sub\":\"Dropdown Master Obat\",\"icon\":\"fas fa-pills\",\"color\":\"text-teal\"},{\"title\":\"2. Filter Periode\",\"sub\":\"Rentang Tanggal\",\"icon\":\"fas fa-calendar\",\"color\":\"text-info\"},{\"title\":\"3. Audit Mutasi\",\"sub\":\"PO, Resep, Penjualan\",\"icon\":\"fas fa-list-check\",\"color\":\"text-warning\"},{\"title\":\"4. Running Balance\",\"sub\":\"Saldo Fisik Akurat\",\"icon\":\"fas fa-calculator\",\"color\":\"text-success\"}]', 'Panduan pelacakan mutasi obat masuk, keluar, dan saldo akhir berjalan (Running Balance) pada buku kartu stok farmasi.', '1. **Akses Menu:** Masuk ke *Apotek > Buku Kartu Stok*.\n2. **Pilih Obat:** Tentukan obat atau alkes yang ingin diaudit dari daftar dropdown.\n3. **Lihat Histori:** Sistem akan merekapitulasi seluruh mutasi dari Penerimaan PO (+), e-Resep Pasien (-), Penjualan Bebas (-), dan Penyesuaian Stok Opname (±).\n4. **Cetak Dokumen:** Klik tombol *Cetak Kartu Stok* untuk keperluan arsip audit farmasi resmi.', 17, 1, 1, '2026-08-26 17:26:21', '2026-08-26 17:52:45'),
(18, 'workflow', 'Aging Schedule Piutang/Hutang & Settlement Fee Dokter', 'Keuangan & Akuntansi', 'primary', 'fas fa-hourglass-half', '[{\"title\":\"1. Cek Umur Piutang\",\"sub\":\"0-30, 31-60, >90 Hari\",\"icon\":\"fas fa-clock\",\"color\":\"text-primary\"},{\"title\":\"2. Rekap Fee Dokter\",\"sub\":\"Klinik & Tindakan\",\"icon\":\"fas fa-stethoscope\",\"color\":\"text-teal\"},{\"title\":\"3. Form Settlement\",\"sub\":\"Nominal & Kas\\/Bank\",\"icon\":\"fas fa-credit-card\",\"color\":\"text-warning\"},{\"title\":\"4. Auto Jurnal Umum\",\"sub\":\"Beban vs Kas\\/Bank\",\"icon\":\"fas fa-book\",\"color\":\"text-success\"}]', 'Panduan pengawasan umur piutang/hutang jatuh tempo dan pencairan bagi hasil jasa medis dokter poliklinik.', '1. **Aging Schedule:** Buka *Keuangan > Aging Piutang & Hutang* untuk memantau status tagihan pasien/asuransi dan hutang PO distributor farmasi.\n2. **Settlement Fee Dokter:** Buka *Keuangan > Jasa Medis Dokter*, klik tombol *Bayarkan Fee* pada dokter yang bersangkutan.\n3. **Auto-Jurnal:** Sistem otomatis membuat entri Jurnal Akuntansi berimbang (Debit Beban Jasa Medis vs Kredit Rekening Kas/Bank) tanpa perlu input manual di buku jurnal.', 18, 1, 1, '2026-08-26 17:26:21', '2026-08-26 17:52:45'),
(19, 'system', 'Error Tracker (APM) & System Scaling Monitor', 'IT & Administrator', 'danger', 'fas fa-bug', '[{\"title\":\"1. Pelacakan Error\",\"sub\":\"Deduplikasi Otomatis\",\"icon\":\"fas fa-bug\",\"color\":\"text-danger\"},{\"title\":\"2. Inspeksi Trace\",\"sub\":\"Modal Stack Trace\",\"icon\":\"fas fa-code\",\"color\":\"text-primary\"},{\"title\":\"3. Resolusi Isu\",\"sub\":\"Tandai Selesai\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"},{\"title\":\"4. 1-Click Scaling\",\"sub\":\"Cache & DB Defrag\",\"icon\":\"fas fa-gauge-high\",\"color\":\"text-teal\"}]', 'Panduan pemantauan kesehatan sistem real-time, pelacakan exception crash, dan manajemen skalabilitas database & cache.', '1. **Error Tracker & APM:** Buka *Administrasi Sistem > Error Tracker & APM* untuk memeriksa log eksepsi runtime, frekuensi kejadian, lokasi baris kode, dan jejak call stack.\n2. **Resolusi Isu:** Klik tombol *Trace* untuk melihat rincian stack trace dan klik *Selesai* setelah bug kode diperbaiki.\n3. **Scaling & Performa:** Buka *Administrasi Sistem > Scaling & Performa* untuk memeriksa penggunaan RAM server, OPcache, serta menjalankan *Bersihkan Cache* dan *Optimasi & Defrag Database* secara 1-klik.', 19, 1, 1, '2026-08-26 17:52:45', '2026-08-26 17:52:45'),
(20, 'security', 'Interoperabilitas RESTful API & Bridge SATUSEHAT Kemenkes', 'IT & Administrator Sistem', 'primary', 'fas fa-network-wired', '[{\"title\":\"1. Header X-API-KEY\",\"sub\":\"Autentikasi Token\",\"icon\":\"fas fa-key\",\"color\":\"text-primary\"},{\"title\":\"2. Panggil Endpoint\",\"sub\":\"GET \\/api\\/v1\\/...\",\"icon\":\"fas fa-link\",\"color\":\"text-teal\"},{\"title\":\"3. Format HL7 FHIR\",\"sub\":\"Standar Kemenkes RI\",\"icon\":\"fas fa-code\",\"color\":\"text-warning\"},{\"title\":\"4. Respon JSON Standar\",\"sub\":\"Status, Code, Data\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Panduan pemanfaatan RESTful API terstandar untuk Display TV Antrean, Integrasi Mobile Pasien, dan SatuSehat Kemenkes.', '1. **Autentikasi API:** Seluruh request ke endpoint `/api/v1/*` harus menyertakan header `X-API-KEY` atau `Authorization: Bearer <token>`.\n2. **Endpoint Display Antrean:** `GET /api/v1/antrean` menyajikan data antrean poliklinik yang sedang dipanggil dan jumlah antrean menunggu.\n3. **Endpoint Katalog Farmasi:** `GET /api/v1/medicines` menyajikan daftar obat aktif beserta total stok terkini.\n4. **Bridge SATUSEHAT:** `GET /api/v1/satusehat/encounter/{visit_id}` menghasilkan payload JSON terstandar HL7 FHIR R4 Encounter Resource yang siap dikirim ke server Kemenkes RI.', 20, 1, 1, '2026-08-26 18:34:45', NULL),
(21, 'technical', 'Penjadwalan Otomatis (Spark Scheduled Cron Tasks)', 'IT & Administrator Sistem', 'teal', 'fas fa-clock', '[{\"title\":\"1. Task Scheduler \\/ Cron\",\"sub\":\"Atur Eksekusi Berkala\",\"icon\":\"fas fa-calendar-check\",\"color\":\"text-teal\"},{\"title\":\"2. spark cron:...\",\"sub\":\"Eksekusi Perintah CLI\",\"icon\":\"fas fa-terminal\",\"color\":\"text-primary\"},{\"title\":\"3. Proses Latar Belakang\",\"sub\":\"Scan\\/Depr\\/Closing\\/Backup\",\"icon\":\"fas fa-cogs\",\"color\":\"text-warning\"},{\"title\":\"4. Log Audit Otomatis\",\"sub\":\"Tercatat di Audit Trail\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Panduan pengaturan otomasi tugas latar belakang klinik menggunakan perintah Spark CLI Cron.', '1. **Pemeriksaan Obat Kadaluarsa:** Jalankan `php spark cron:check-expired-medicines` setiap pagi untuk memindai batch obat mendekati kadaluarsa (< 90 hari).\n2. **Penyusutan Aset Bulanan:** Jalankan `php spark cron:monthly-depreciation` setiap akhir bulan untuk menghitung dan membukukan jurnal penyusutan aset tetap.\n3. **Penutupan Kasir Harian:** Jalankan `php spark cron:daily-closing` setiap malam untuk rekonsiliasi total omset kasir (Klinik, Apotek, Resto).\n4. **Pencadangan Basis Data:** Jalankan `php spark cron:database-backup` secara berkala untuk mengekspor full SQL dump ke folder `writable/backups/`.', 21, 1, 1, '2026-08-26 18:34:45', NULL),
(22, 'workflow', 'Alur Pelayanan Pasien Rawat Jalan & Triase UGD', 'Petugas Pendaftaran & Perawat', 'teal', 'fas fa-hospital-user', '[{\"title\":\"1. Pendaftaran Pasien\",\"sub\":\"NIK & No. RM Unik\",\"icon\":\"fas fa-id-card\",\"color\":\"text-primary\"},{\"title\":\"2. Pilih Poliklinik\\/Dokter\",\"sub\":\"Poli Umum\\/Gigi\\/Spesialis\",\"icon\":\"fas fa-stethoscope\",\"color\":\"text-teal\"},{\"title\":\"3. Skala Triase ATS 1-5\",\"sub\":\"Skrining Kegawatan\",\"icon\":\"fas fa-heart-pulse\",\"color\":\"text-danger\"},{\"title\":\"4. No Antrean & Billing Open\",\"sub\":\"Cetak Karcis & RME\",\"icon\":\"fas fa-ticket\",\"color\":\"text-success\"}]', 'Alur lengkap penerimaan pasien rawat jalan mulai dari pengecekan NIK/RM, penentuan poliklinik tujuan, skrining tanda vital dan skala triase kegawatan, hingga penerbitan tiket antrean dan tagihan billing.', '1. **Pendaftaran Pasien:** Akses menu *Klinik > Pendaftaran Pasien*. Cari berdasarkan NIK/RM atau daftarkan pasien baru.\n2. **Kunjungan Baru:** Klik tombol *+ Kunjungan*, tentukan poliklinik, dokter yang bertugas, dan jenis penjamin (Umum / BPJS / Asuransi Swasta).\n3. **Skrining Triase:** Perawat menginput tanda vital (TD, Nadi, Suhu, RR) dan menetapkan Skala Triase ATS (1-Resusitasi s/d 5-Non Urgensi).\n4. **Antrean Display:** Sistem otomatis menerbitkan Nomor Antrean poliklinik yang langsung tersinkronisasi ke layar TV Ruang Tunggu (`/api/v1/antrean`).', 1, 1, 1, '2026-08-26 18:38:06', NULL),
(23, 'workflow', 'Alur RME Dokter, Odontogram 32 Gigi & e-Resep Medis', 'Dokter Umum & Dokter Gigi', 'primary', 'fas fa-user-doctor', '[{\"title\":\"1. Panggil Antrean RME\",\"sub\":\"Menu SOAP Pasien\",\"icon\":\"fas fa-bullhorn\",\"color\":\"text-primary\"},{\"title\":\"2. Anamnesa & Fisik\",\"sub\":\"Subjective & Objective\",\"icon\":\"fas fa-notes-medical\",\"color\":\"text-info\"},{\"title\":\"3. Diagnosa & Odontogram\",\"sub\":\"ICD-10 & Chart 32 Gigi\",\"icon\":\"fas fa-tooth\",\"color\":\"text-warning\"},{\"title\":\"4. Buat e-Resep & Selesai\",\"sub\":\"Terkirim ke Farmasi\",\"icon\":\"fas fa-prescription-bottle\",\"color\":\"text-success\"}]', 'Proses pemeriksaan medis elektronik dokter: pencatatan anamnesa SOAP, pengisian odontogram interaktif 32 gigi FDI, penginputan diagnosa ICD-10/ICD-9, dan pembuatan resep elektronik terpadu.', '1. **Pemeriksaan Pasien:** Buka *Klinik > Rekam Medis (SOAP)* dan klik *Periksa Pasien*.\n2. **Pencatatan SOAP:** Isi keluhan subjektif, hasil pemeriksaan objektif, dan diagnosa assessment ICD-10.\n3. **Odontogram Poli Gigi:** Untuk pemeriksaan gigi, klik tab *Odontogram* dan tentukan kondisi setiap gigi (Caries, Filling, Missing, Crown, Radix, Extract).\n4. **Resep Digital:** Tambahkan item obat dan aturan dosis, lalu klik *Simpan RME*. Resep akan otomatis muncul di modul farmasi secara *real-time*.', 2, 1, 1, '2026-08-26 18:38:06', NULL),
(24, 'workflow', 'Alur Apotek: Peracikan e-Resep, Penjualan Bebas & Kartu Stok FEFO', 'Apoteker & Staf Farmasi', 'success', 'fas fa-pills', '[{\"title\":\"1. Terima e-Resep\",\"sub\":\"Antrean Resep Masuk\",\"icon\":\"fas fa-inbox\",\"color\":\"text-primary\"},{\"title\":\"2. Pilih Batch FEFO\",\"sub\":\"First Expired First Out\",\"icon\":\"fas fa-boxes-stacked\",\"color\":\"text-teal\"},{\"title\":\"3. Dispensing & Serahkan\",\"sub\":\"Potong Stok Otomatis\",\"icon\":\"fas fa-hand-holding-medical\",\"color\":\"text-warning\"},{\"title\":\"4. Buku Kartu Stok Update\",\"sub\":\"Audit Running Balance\",\"icon\":\"fas fa-book-medical\",\"color\":\"text-success\"}]', 'Manajemen instalasi farmasi untuk penyerahan obat resep dan kasir penjualan bebas, pemilihan batch obat terdekat expired (FEFO), serta audit mutasi kartu stok real-time.', '1. **Tebus Resep:** Buka menu *Apotek > e-Resep Masuk*. Pilih batch obat yang paling mendekati tanggal kadaluarsa (*FEFO Rule*).\n2. **Penjualan Bebas:** Kasir farmasi melayani pembelian obat bebas OTC dan alkes langsung melalui *Apotek > Penjualan Bebas*.\n3. **Buku Kartu Stok Digital:** Akses *Apotek > Buku Kartu Stok* untuk melihat riwayat keluar-masuk barang, referensi nomor transaksi, dan saldo akhir stok berjalan (*Running Balance*).', 3, 1, 1, '2026-08-26 18:38:06', NULL),
(25, 'workflow', 'Alur Kasir Utama: Pelunasan Billing, Stempel Lunas & QRIS Dinamis', 'Kasir Utama & Staf Billing', 'warning', 'fas fa-cash-register', '[{\"title\":\"1. Buka Billing Pasien\",\"sub\":\"Rekap Layanan & Obat\",\"icon\":\"fas fa-file-invoice-dollar\",\"color\":\"text-primary\"},{\"title\":\"2. Pilih Metode Bayar\",\"sub\":\"Tunai\\/QRIS\\/Debit\\/Transfer\",\"icon\":\"fas fa-credit-card\",\"color\":\"text-teal\"},{\"title\":\"3. Auto-Jurnal Akuntansi\",\"sub\":\"Debit Kas vs Pendapatan\",\"icon\":\"fas fa-scale-balanced\",\"color\":\"text-warning\"},{\"title\":\"4. Cetak Kwitansi Resmi\",\"sub\":\"QR Validasi & Stempel\",\"icon\":\"fas fa-print\",\"color\":\"text-success\"}]', 'Proses rekonsiliasi dan pelunasan tagihan rawat jalan, apotek, dan tindakan medis terintegrasi langsung dengan pembukuan jurnal akuntansi dan cetak bukti pembayaran resmi.', '1. **Pelunasan Kasir:** Akses *Keuangan > Kasir Utama*, klik tagihan pasien yang berstatus *Open*.\n2. **Metode Pembayaran:** Pilih metode bayar (Tunai, QRIS Dinamis, Kartu Debit, atau Transfer Bank) serta input nominal diskon jika berlaku.\n3. **Integrasi Akuntansi Otomatis:** Saat tombol *Bayar & Lunas* ditekan, `JournalEngine` secara otomatis membukukan jurnal debit kas/bank dan kredit pendapatan klinik.\n4. **Cetak Lembar Kwitansi:** Kwitansi dilengkapi stempel digital resmi, rincian biaya, dan barcode QR validasi keaslian dokumen.', 4, 1, 1, '2026-08-26 18:38:06', NULL),
(26, 'workflow', 'Alur Akuntansi Terpadu: Saldo Awal, Jurnal Umum & Laporan Keuangan', 'Accounting & Koordinator Keuangan', 'info', 'fas fa-scale-balanced', '[{\"title\":\"1. Setup Saldo Awal\",\"sub\":\"Smart Auto-Balancing COA\",\"icon\":\"fas fa-sliders\",\"color\":\"text-primary\"},{\"title\":\"2. Pencatatan Jurnal\",\"sub\":\"Auto & Manual Journal\",\"icon\":\"fas fa-book\",\"color\":\"text-teal\"},{\"title\":\"3. Buku Besar (Ledger)\",\"sub\":\"Audit Per Akun Akuntansi\",\"icon\":\"fas fa-list-check\",\"color\":\"text-warning\"},{\"title\":\"4. 5 Laporan Keuangan\",\"sub\":\"Laba Rugi, Neraca, Cashflow\",\"icon\":\"fas fa-file-pdf\",\"color\":\"text-success\"}]', 'Siklus akuntansi standar PSAK: setup saldo awal bagan akun (COA), pencatatan jurnal umum berimbang (Debit == Kredit), audit buku besar, serta penyajian laporan keuangan resmi berkop klinik.', '1. **Setup Saldo Awal:** Akses *Akuntansi > Saldo Awal*. Masukkan saldo historis akun. Sistem memiliki fitur *Smart Auto-Balancing* ke Modal Awal Disetor (3-101).\n2. **Jurnal Umum:** Seluruh transaksi kasir, payroll, fee dokter, dan penyusutan aset otomatis tercatat di *Akuntansi > Jurnal Umum*.\n3. **Laporan Keuangan:** Akses *Akuntansi > Laporan Keuangan* untuk mencetak 5 laporan resmi: Laba Rugi Komprehensif, Neraca Keuangan, Arus Kas, Omset Unit Bisnis, dan Buku Besar.', 5, 1, 1, '2026-08-26 18:38:06', NULL),
(27, 'workflow', 'Alur Rekapitulasi & Settlement Jasa Medis Dokter', 'Bagian Keuangan & Direksi', 'purple', 'fas fa-hand-holding-dollar', '[{\"title\":\"1. Rekap Fee per Dokter\",\"sub\":\"Filter Periode & Dokter\",\"icon\":\"fas fa-calculator\",\"color\":\"text-primary\"},{\"title\":\"2. Verifikasi Tindakan\",\"sub\":\"Cek Pasien & Tindakan\",\"icon\":\"fas fa-clipboard-check\",\"color\":\"text-teal\"},{\"title\":\"3. Proses Settlement\",\"sub\":\"Pencairan Kas\\/Bank\",\"icon\":\"fas fa-money-bill-transfer\",\"color\":\"text-warning\"},{\"title\":\"4. Auto-Jurnal Beban\",\"sub\":\"Jurnal Jasa Medis (5-103)\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Manajemen komisi dan bagi hasil dokter poliklinik/tindakan berdasarkan tarif layanan medis, rekapitulasi periode kerja, hingga pencairan kas dan pembukuan beban akuntansi.', '1. **Rekapitulasi:** Buka *Keuangan > Fee Jasa Medis Dokter*. Tentukan filter dokter dan rentang tanggal pelayanan.\n2. **Rincian Fee:** Sistem menghitung otomatis hak fee dokter per pasien berdasarkan tarif layanan master klinik.\n3. **Pencairan Fee:** Klik *Proses Settlement & Bayar Fee*. Sistem membukukan pencairan kas dan mencatat Jurnal Beban Jasa Medis Dokter (5-103 vs 1-101/1-102).\n4. **Bukti Bayar:** Cetak slip rincian pembayaran honor jasa medis dokter sebagai bukti sah.', 6, 1, 1, '2026-08-26 18:38:06', NULL),
(28, 'workflow', 'Alur Pengadaan Barang (PO), Approval & Penerimaan Gudang (GRN)', 'Bagian Pengadaan, Kepala Klinik & Gudang', 'teal', 'fas fa-truck-ramp-box', '[{\"title\":\"1. Buat Pengajuan PO\",\"sub\":\"Pilih Vendor & Item Obat\",\"icon\":\"fas fa-file-pen\",\"color\":\"text-primary\"},{\"title\":\"2. Verifikasi Approval\",\"sub\":\"Persetujuan Kepala Klinik\",\"icon\":\"fas fa-stamp\",\"color\":\"text-teal\"},{\"title\":\"3. Penerimaan Gudang (GRN)\",\"sub\":\"Cek Fisik & No. Batch\",\"icon\":\"fas fa-box-open\",\"color\":\"text-warning\"},{\"title\":\"4. Stok & Hutang Update\",\"sub\":\"Buku Hutang & Aging AP\",\"icon\":\"fas fa-check-double\",\"color\":\"text-success\"}]', 'Siklus pengadaan logistik dan obat farmasi dari permohonan pesanan pembelian (PO), otorisasi bertingkat, penerimaan barang gudang (GRN), hingga mutasi stok dan kartu hutang dagang supplier.', '1. **Buat PO:** Buka *Pengadaan > Purchase Order (PO)*, pilih supplier dan masukkan item pesanan beserta harga beli.\n2. **Approval:** Kepala Bagian Keuangan / Kepala Klinik menyetujui pengadaan melalui menu *Pengadaan > Approval Pengadaan*.\n3. **Penerimaan Barang:** Bagian gudang memproses penerimaan melalui *Pengadaan > Penerimaan Barang (GRN)*, mencatat nomor batch dan tanggal kadaluarsa.\n4. **Stok & Hutang:** Stok otomatis bertambah di buku kartu stok dan tercatat pada *Aging Schedule Hutang Usaha*.', 7, 1, 1, '2026-08-26 18:38:06', NULL),
(29, 'workflow', 'Alur POS Resto Sehat & Kitchen Display System (KDS)', 'Kasir Resto & Koki Dapur', 'danger', 'fas fa-utensils', '[{\"title\":\"1. Input Pesanan Menu\",\"sub\":\"POS Touchscreen Resto\",\"icon\":\"fas fa-tablet-screen-button\",\"color\":\"text-primary\"},{\"title\":\"2. Masuk Layar Dapur (KDS)\",\"sub\":\"Status: Sedang Dimasak\",\"icon\":\"fas fa-fire-burner\",\"color\":\"text-danger\"},{\"title\":\"3. Makanan Siap Saji\",\"sub\":\"Update Status KDS Ready\",\"icon\":\"fas fa-bell-concierge\",\"color\":\"text-warning\"},{\"title\":\"4. Pelunasan Kasir POS\",\"sub\":\"Cetak Struk & Auto-Jurnal\",\"icon\":\"fas fa-receipt\",\"color\":\"text-success\"}]', 'Operasional restoran nutrisi sehat klinik: pemesanan menu makanan/minuman sehat via POS, sinkronisasi pesanan ke monitor dapur koki (KDS), dan pelunasan kasir terhubung akuntansi.', '1. **Kasir POS Resto:** Akses menu *Resto > Kasir & POS*. Pilih menu makanan sehat dan input nama pemesan / nomor antrean.\n2. **Kitchen Display System (KDS):** Koki dapur melihat pesanan baru di menu *Resto > Layar Dapur (KDS)* secara *real-time*.\n3. **Proses Masak:** Koki mengubah status menjadi *Memasak* lalu *Siap Saji* setelah hidangan selesai.\n4. **Struk Pembayaran:** Kasir menyelesaikan pembayaran dan mencetak struk pemesanan resto sehat.', 8, 1, 1, '2026-08-26 18:38:06', NULL),
(30, 'workflow', 'Alur Presensi GPS, Pengajuan Cuti & Payroll Gaji Staf HRD', 'HRD & Seluruh Pegawai', 'teal', 'fas fa-users-gear', '[{\"title\":\"1. Presensi Masuk\\/Pulang\",\"sub\":\"Geolokasi GPS & Shift\",\"icon\":\"fas fa-location-dot\",\"color\":\"text-primary\"},{\"title\":\"2. Manajemen Cuti Pegawai\",\"sub\":\"Pengajuan & Otorisasi\",\"icon\":\"fas fa-calendar-day\",\"color\":\"text-teal\"},{\"title\":\"3. Hitung Payroll Bulanan\",\"sub\":\"Gaji Pokok & Tunjangan\",\"icon\":\"fas fa-calculator\",\"color\":\"text-warning\"},{\"title\":\"4. Cetak Slip & Auto-Jurnal\",\"sub\":\"Beban Gaji Staf (5-101)\",\"icon\":\"fas fa-envelope-open-text\",\"color\":\"text-success\"}]', 'Manajemen SDM terintegrasi: presensi kehadiran harian berbasis geolokasi GPS, pengajuan dan persetujuan cuti kerja, hingga otomasi kalkulasi slip gaji payroll dan pembukuan jurnal beban gaji.', '1. **Presensi Mandiri:** Pegawai melakukan *Check-in* dan *Check-out* kehadiran harian di menu *HRD > Presensi Kehadiran*.\n2. **Pengajuan Cuti:** Pegawai mengajukan izin cuti kerja via *HRD > Pengajuan Cuti*, yang diteruskan ke atasan untuk disetujui.\n3. **Generate Payroll:** Setiap akhir bulan, HRD membuka *HRD > Payroll Gaji* untuk menghitung gaji pokok, tunjangan kehadiran, dan potongan BPJS.\n4. **Pencairan & Slip Gaji:** Klik *Bayar Payroll* untuk membukukan Jurnal Beban Gaji Staf (5-101) dan mencetak slip gaji resmi pegawai.', 9, 1, 1, '2026-08-26 18:38:06', NULL),
(31, 'workflow', 'Interoperabilitas RESTful API & SATUSEHAT Kemenkes (HL7 FHIR R4)', 'IT & Administrator Sistem', 'primary', 'fas fa-network-wired', '[{\"title\":\"1. Header X-API-KEY\",\"sub\":\"Autentikasi Token\",\"icon\":\"fas fa-key\",\"color\":\"text-primary\"},{\"title\":\"2. Request Endpoint API\",\"sub\":\"GET \\/api\\/v1\\/...\",\"icon\":\"fas fa-link\",\"color\":\"text-teal\"},{\"title\":\"3. Standar HL7 FHIR R4\",\"sub\":\"Encounter Resource\",\"icon\":\"fas fa-code\",\"color\":\"text-warning\"},{\"title\":\"4. Respon JSON Standar\",\"sub\":\"Status, Code, Data\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Pemanfaatan arsitektur RESTful API modern untuk integrasi Display TV Antrean, Aplikasi Mobile Pasien, dan Bridge Interoperabilitas SATUSEHAT Kemenkes RI berstandar HL7 FHIR R4.', '1. **Keamanan API:** Seluruh request ke `/api/v1/*` wajib menyertakan header `X-API-KEY` atau `Authorization: Bearer <token>`.\n2. **Display Antrean:** `GET /api/v1/antrean` menyajikan data antrean poliklinik yang sedang dipanggil untuk monitor TV ruang tunggu.\n3. **Katalog Obat:** `GET /api/v1/medicines` menyajikan daftar obat aktif dan stok terkini secara *real-time*.\n4. **SATUSEHAT FHIR:** `GET /api/v1/satusehat/encounter/{visit_id}` menghasilkan payload resmi **HL7 FHIR R4 Encounter Resource** yang siap ditransmisikan ke server Kemenkes RI.', 10, 1, 1, '2026-08-26 18:38:06', NULL),
(32, 'workflow', 'Penjadwalan Otomatis (Spark Scheduled Cron Tasks) & Disaster Recovery', 'IT & Administrator Sistem', 'success', 'fas fa-clock', '[{\"title\":\"1. Task Scheduler \\/ Cron\",\"sub\":\"Jadwalkan Eksekusi\",\"icon\":\"fas fa-calendar-check\",\"color\":\"text-teal\"},{\"title\":\"2. Eksekusi Spark Cron\",\"sub\":\"php spark cron:...\",\"icon\":\"fas fa-terminal\",\"color\":\"text-primary\"},{\"title\":\"3. Latar Belakang\",\"sub\":\"Scan\\/Depr\\/Closing\\/Backup\",\"icon\":\"fas fa-cogs\",\"color\":\"text-warning\"},{\"title\":\"4. Log Audit Otomatis\",\"sub\":\"Tercatat di Audit Trail\",\"icon\":\"fas fa-check-circle\",\"color\":\"text-success\"}]', 'Panduan otomasi tugas latar belakang klinik (pemindaian obat kadaluarsa, penyusutan aset bulanan, tutup buku harian, dan backup database otomatis) menggunakan Spark Scheduled Cron.', '1. **Scan Obat Kadaluarsa:** `php spark cron:check-expired-medicines` memindai batch obat mendekati expired (< 90 hari) setiap pagi.\n2. **Penyusutan Aset Bulanan:** `php spark cron:monthly-depreciation` menghitung dan menjurnal depresiasi aset tetap setiap akhir bulan.\n3. **Tutup Buku Harian:** `php spark cron:daily-closing` merekonsiliasi total omset harian kasir klinik, apotek, dan resto setiap malam.\n4. **Pencadangan Basis Data:** `php spark cron:database-backup` membuat salinan full SQL dump ke folder `writable/backups/`.\n5. **Uji Otomatis:** Jalankan `php spark system:test-all` kapan pun untuk memverifikasi 100% kesehatan seluruh subsistem.', 11, 1, 1, '2026-08-26 18:38:06', NULL),
(33, 'role_guide', 'Panduan Kerja: Dokter Spesialis & Dokter Umum', 'Dokter', 'primary', 'fas fa-user-md', NULL, 'Panduan ringkas tugas dokter poliklinik: pemeriksaan SOAP RME, pengisian odontogram gigi, diagnosa ICD-10, pembuatan e-Resep, dan monitoring fee jasa medis.', '• **Pemeriksaan Pasien:** Akses *Klinik > Rekam Medis (SOAP)*, klik *Periksa Pasien* pada antrean yang masuk.\n• **Odontogram Gigi:** Pada Poli Gigi, isi visual chart 32 gigi dengan kondisi klinis terkait.\n• **e-Resep Medis:** Ketik nama obat dan dosis aturan minum, lalu simpan agar langsung diterima instalasi farmasi.\n• **Honor & Jasa Medis:** Cek rekapitulasi fee pemeriksaan Anda di menu *Keuangan > Fee Jasa Medis Dokter*.', 1, 1, 1, '2026-08-26 18:38:06', NULL),
(34, 'role_guide', 'Panduan Kerja: Apoteker & Staf Farmasi', 'Apoteker & Farmasi', 'success', 'fas fa-prescription', NULL, 'Panduan penyerahan e-resep dokter berbasis FEFO, kasir penjualan obat bebas OTC, pemantauan buku kartu stok, dan stock opname fisik.', '• **Tebus Resep:** Buka *Apotek > e-Resep Masuk*, pilih nomor batch obat terdekat expired (*FEFO*) dan klik *Serahkan Obat*.\n• **Penjualan Bebas:** Layani pembelian langsung obat bebas melalui menu *Apotek > Penjualan Bebas*.\n• **Audit Kartu Stok:** Pantau mutasi stok masuk (PO) dan keluar (Resep) pada menu *Apotek > Buku Kartu Stok*.\n• **Stock Opname:** Lakukan penyesuaian fisik stok obat bulanan melalui menu *Apotek > Stock Opname*.', 2, 1, 1, '2026-08-26 18:38:06', NULL),
(35, 'role_guide', 'Panduan Kerja: Kasir Utama & Petugas Billing', 'Kasir & Billing', 'warning', 'fas fa-cash-register', NULL, 'Panduan operasional kasir: pelunasan tagihan rawat jalan, apotek & tindakan medis, pembayaran QRIS/Debit/Tunai, dan cetak kwitansi stempel sah.', '• **Buka Tagihan:** Buka menu *Keuangan > Kasir Utama*, klik tagihan pasien yang berstatus *Open*.\n• **Input Pembayaran:** Masukkan nominal uang yang diterima dan pilih metode pembayaran (Tunai, QRIS Dinamis, Kartu Debit, atau Transfer Bank).\n• **Cetak Kwitansi:** Cetak bukti pembayaran resmi berkop klinik yang dilengkapi stempel sah dan QR barcode verifikasi dokumen.', 3, 1, 1, '2026-08-26 18:38:06', NULL),
(36, 'role_guide', 'Panduan Kerja: Staf Akuntansi & Koordinator Keuangan', 'Accounting & Keuangan', 'info', 'fas fa-calculator', NULL, 'Panduan pembukuan jurnal akuntansi, audit buku besar, settlement fee dokter, aging schedule piutang/hutang, dan penerbitan 5 laporan keuangan.', '• **Audit Jurnal:** Pantau keseimbangan jurnal otomatis transaksi kasir, payroll, dan penyusutan di *Akuntansi > Jurnal Umum*.\n• **Buku Besar:** Telusuri mutasi saldo setiap akun COA melalui *Akuntansi > Buku Besar*.\n• **Settlement Fee:** Proses pencairan bagi hasil dokter melalui *Keuangan > Fee Jasa Medis Dokter*.\n• **Laporan Keuangan:** Cetak Laba Rugi, Neraca, dan Arus Kas resmi melalui menu *Akuntansi > Laporan Keuangan*.', 4, 1, 1, '2026-08-26 18:38:06', NULL),
(37, 'faq', 'Bagaimana jika terjadi ketidakseimbangan saldo jurnal atau transaksi gagal?', 'Semua Pengguna', 'info', 'fas fa-circle-question', NULL, 'Solusi jika jurnal akuntansi tidak seimbang atau terjadi anomali transaksi database.', 'Sistem Sawamawa Medical Center telah dilengkapi dengan **ACID Transaction Engine** dan **Error Tracking APM**.\n1. Jika transaksi terputus di tengah jalan, sistem otomatis melakukan *Rollback* sehingga tidak ada data menggantung.\n2. Untuk memverifikasi keseimbangan seluruh jurnal secara instan, jalankan perintah CLI: `php spark audit:transactions`.\n3. Rincian galat teknis dapat dilihat langsung pada menu *Administrasi Sistem > Error Tracker & APM*.', 1, 1, 1, '2026-08-26 18:38:06', NULL),
(38, 'faq', 'Bagaimana cara menghubungkan Display Antrean TV ke sistem klinik?', 'IT & Administrator Sistem', 'teal', 'fas fa-tv', NULL, 'Langkah menghubungkan layar Smart TV / Monitor ruang tunggu ke API antrean real-time.', '1. Buka browser pada Smart TV / Mini PC display ruang tunggu.\n2. Arahkan URL ke endpoint antrean atau panggil API RESTful: `GET /api/v1/antrean` dengan menyertakan header `X-API-KEY`.\n3. Data nomor antrean yang sedang dipanggil dan dokter yang bertugas akan ter-update secara *real-time* setiap ada pemanggilan baru di poliklinik.', 2, 1, 1, '2026-08-26 18:38:06', NULL),
(39, 'faq', 'Bagaimana cara melakukan pencadangan (backup) dan pemulihan basis data?', 'Super Admin & IT', 'success', 'fas fa-database', NULL, 'Prosedur backup manual 1-klik dan penjadwalan otomatis pencadangan database.', '• **Backup Manual 1-Klik:** Buka menu *Administrasi Sistem > Pengaturan Sistem*, lalu klik tombol *Unduh Backup SQL*.\n• **Backup Otomatis CLI:** Jalankan `php spark cron:database-backup` yang otomatis menyimpan file SQL dump ke folder `writable/backups/`.\n• **Pemulihan (Restore):** File backup SQL dapat diimpor langsung melalui phpMyAdmin atau MySQL CLI.', 3, 1, 1, '2026-08-26 18:38:06', NULL),
(40, 'security_policy', 'Kebijakan Keamanan Data Medis, Hak Akses RBAC & Audit Trail', 'Seluruh Pengguna', 'teal', 'fas fa-shield-halved', NULL, 'Ketentuan keamanan kerahasiaan rekam medis, enkripsi kata sandi Bcrypt, dan pencatatan audit trail otomatis.', '1. **Kerahasiaan Data Medis (RME):** Data rekam medis pasien dilindungi oleh matriks hak akses RBAC (*Role-Based Access Control*) dan hanya dapat diakses oleh dokter/perawat pemeriksa.\n2. **Enkripsi Kata Sandi:** Seluruh password akun dienkripsi menggunakan algoritma *Bcrypt Hashing*.\n3. **Universal Audit Trail:** Setiap aktivitas penambahan, pengubahan, penghapusan, pembayaran, dan pencetakan dokumen resmi dicatat secara permanen di menu *Audit Trail Logs* (`system/audit`).\n4. **Proteksi API:** Seluruh endpoint publik dilindungi oleh filter autentikasi token (`ApiKeyFilter`) dan proteksi *Rate Limiting*.', 1, 1, 1, '2026-08-26 18:38:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_error_logs`
--

CREATE TABLE `system_error_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `error_hash` varchar(64) NOT NULL,
  `error_level` enum('CRITICAL','ERROR','WARNING','NOTICE') NOT NULL DEFAULT 'ERROR',
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `line` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `trace` longtext DEFAULT NULL,
  `count` int(11) NOT NULL DEFAULT 1,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_notifications`
--

INSERT INTO `system_notifications` (`id`, `notification_key`, `category`, `type`, `badge`, `icon`, `title`, `message`, `link`, `target_roles`, `target_user_id`, `sender_name`, `is_broadcast`, `created_at`) VALUES
(1, 'pharm_low_stock', 'pharmacy', 'danger', 'Stok Kritis', 'fas fa-triangle-exclamation', 'Peringatan Stok Obat Menipis', 'Ada 29 jenis obat mencapai batas minimum stok / perlu segera diajukan PR pengadaan.', 'http://localhost/sawamawamedicalcenter.id/public/apotek/stok', 'apoteker,bagian pengadaan,super admin', NULL, 'System Sentinel Engine', 1, '2026-08-26 19:03:52'),
(2, 'pharm_low_stock', 'pharmacy', 'danger', 'Stok Kritis', 'fas fa-triangle-exclamation', 'Peringatan Stok Obat Menipis', 'Ada 29 jenis obat mencapai batas minimum stok / perlu segera diajukan PR pengadaan.', 'http://localhost/sawamawamedicalcenter.id/public/apotek/stok', 'apoteker,bagian pengadaan,super admin', NULL, 'System Sentinel Engine', 1, '2026-08-27 02:50:36');

-- --------------------------------------------------------

--
-- Table structure for table `system_notification_reads`
--

CREATE TABLE `system_notification_reads` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_group` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'klinik', 'clinic_name', 'GITRIA FARMA', 'Nama Resmi Klinik Utama', '2026-08-20 17:11:36', '2026-08-21 13:53:01'),
(2, 'klinik', 'clinic_address', 'Jl. Raya Sawamawa No. 100, Sumbawa', 'Alamat Kantor Klinik Utama', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(3, 'klinik', 'clinic_phone', '081122334455', 'Nomor Telepon Hotline Klinik', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(4, 'klinik', 'clinic_bpjs_active', 'true', 'Status Integrasi BPJS Kesehatan (true/false)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(5, 'apotek', 'pharmacy_min_stock_alert', '10', 'Batas Minimum Stok untuk Notifikasi Stok Kritis', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(6, 'apotek', 'pharmacy_tax_percent', '10', 'Persentase Pajak Penjualan Obat Apotek (%)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(7, 'apotek', 'pharmacy_profit_margin_percent', '20', 'Persentase Margin Keuntungan Standar Obat (%)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(8, 'resto', 'resto_tax_percent', '10', 'Persentase Pajak Penjualan Restoran POS (%)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(9, 'resto', 'resto_service_charge_percent', '5', 'Persentase Biaya Pelayanan Restoran (%)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(10, 'resto', 'resto_auto_billing_inpatient', 'true', 'Otomatisasikan Pembebanan Resto ke Billing Pasien Rawat Inap (true/false)', '2026-08-20 17:11:36', '2026-08-20 17:11:36'),
(11, 'klinik', 'clinic_tagline', 'Pusat Layanan Medis Terpadu & Terpercaya', 'Slogan / Tagline Resmi', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(12, 'klinik', 'clinic_email', 'info@samawamedicalcenter.id', 'Email Resmi Pelayanan', '2026-08-22 03:34:46', '2026-08-26 12:22:16'),
(13, 'klinik', 'clinic_website', 'www.samawamedicalcenter.id', 'Website Resmi Klinik', '2026-08-22 03:34:46', '2026-08-26 12:22:17'),
(14, 'klinik', 'clinic_license_number', '445/012/DINKES/2024', 'Nomor Izin Operasional Klinik', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(15, 'klinik', 'clinic_director', 'dr. Andi Wijaya, Sp.PD', 'Nama Direktur / Penanggung Jawab Medis', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(16, 'klinik', 'clinic_watermark', 'GITRIA FARMA', 'Teks Watermark Lembar Dokumen', '2026-08-22 03:34:46', '2026-08-22 03:36:26'),
(17, 'klinik', 'clinic_footer_note', 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.', 'Catatan Kaki Dokumen Cetak', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(18, 'klinik', 'clinic_logo', '', 'File Logo Klinik', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(19, 'klinik', 'clinic_favicon', '', 'File Favicon Website', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(20, 'api', 'wa_gateway_provider', 'fonnte', 'Provider WhatsApp Gateway (fonnte/wablas)', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(21, 'api', 'wa_api_token', '', 'API Token WhatsApp Gateway', '2026-08-22 03:34:46', '2026-08-22 03:34:46'),
(22, 'api', 'wa_sender_number', '081122334455', 'Nomor Pengirim WhatsApp Resmi', '2026-08-22 03:34:46', '2026-08-22 03:34:46');

-- --------------------------------------------------------

--
-- Table structure for table `system_slow_queries`
--

CREATE TABLE `system_slow_queries` (
  `id` int(11) UNSIGNED NOT NULL,
  `query_sql` text NOT NULL,
  `execution_time_ms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `caller_location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_updates`
--

CREATE TABLE `system_updates` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_updates`
--

INSERT INTO `system_updates` (`id`, `version`, `title`, `category`, `badge_color`, `release_date`, `summary`, `details`, `is_major`, `is_published`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'v2.5.0', 'Implementasi DataTables Server-Side Processing & Kecepatan Tinggi', 'PERFORMA & DATA', 'teal', '2026-08-22', 'Seluruh tabel berdata besar kini memuat ribuan baris data secara instan (< 15 ms) tanpa membebani memori browser menggunakan arsitektur Server-Side Processing.', '• Pendaftaran Pasien: Pencarian instan berdasarkan No RM, NIK, Nama, Telp, dan Alamat.\n• Stok Apotek: Agregasi stok fisik dan peringatan stok kritis otomatis.\n• Master ICD-10 & ICD-9-CM: Katalog kode diagnosa dan prosedur tindakan medis dengan paginasi instan.\n• Audit Trail: Pelacakan log aktivitas pengguna, aksi, modul, dan IP address terfilter.\n• Buku Jurnal Umum: Paginasi entri jurnal double-entry otomatis beserta rincian mutasi debet/kredit.', 1, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(2, 'v2.4.0', 'Setup Saldo Awal Sistem & Master Laporan Keuangan Standar Audit', 'FINANSIAL & AKUNTANSI', 'success', '2026-08-21', 'Penyempurnaan modul pembukuan akuntansi dengan wizard saldo awal cerdas dan 5 laporan keuangan komprehensif berstandar audit resmi.', '• Setup Saldo Awal (Smart Auto-Balancing): Wizard input kas, bank, persediaan, aset, hutang, dan modal dengan penyeimbangan otomatis ke Modal Awal Disetor (3-101).\n• Laporan Laba Rugi Komprehensif: Pendapatan operasional, HPP, beban nakes & operasional, hingga Net Profit.\n• Laporan Neraca: Sisi Aktiva vs Pasiva dengan indikator status Balanced Badge.\n• Laporan Arus Kas & Rekap Omset Unit Bisnis: Lacak arus kas dan proporsi omset poli/farmasi/resto/lab.\n• Buku Besar Kronologis (General Ledger): Rincian transaksi per rekening COA.\n• Lembar Cetak Laporan Keuangan Resmi: Format resmi berkop surat klinik dan tanda tangan direktur.', 1, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(3, 'v2.3.0', 'Master Tarif Tindakan Bertingkat & Poliklinik Baru', 'KLINIK & MEDIS', 'info', '2026-08-20', 'Pengelompokan layanan medis secara hierarkis (Parent-Child) dan penambahan master poli serta spesialis.', '• Kategori & Layanan Bertingkat (Parent-Child): Tindakan medis dikelompokkan ke dalam kategori induk.\n• Dropdown Optgroup Terstruktur: Formulir pendaftaran menampilkan grup tindakan rapi.\n• Manajemen Poliklinik & Dokter: Penambahan poli spesialis baru dan jadwal praktek.\n• Pengaturan Jasa Medis & Komisi Nakes: Persentase bagi hasil terhitung otomatis saat transaksi dibayar.', 0, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(4, 'v2.2.0', 'Landing Page Publik & Portal Reservasi Pasien Online', 'PUBLIC PORTAL', 'primary', '2026-08-19', 'Website profil modern Sawamawa Medical Center dan formulir pendaftaran pasien online aman.', '• Landing Page Premium: Profil dokter, poliklinik, layanan apotek & resto, dan peta lokasi.\n• Registrasi Pasien Baru Online: Penerbitan No RM otomatis terlindungi CSRF dan verifikasi NIK.\n• Booking Kunjungan Pasien Lama: Reservasi poli cepat dengan verifikasi ganda No RM / NIK dan Tanggal Lahir.', 0, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(5, 'v2.1.0', 'Restoran Sehat Touchscreen POS, KDS Dapur & Apotek Multi-Batch', 'FARMASI & RESTO', 'warning', '2026-08-18', 'Modul kasir layar sentuh restoran sehat, Kitchen Display System (KDS), dan penelusuran batch farmasi.', '• Resto & Cafe Sehat: Layar kasir touchscreen, manajemen meja, dan integrasi rujukan diet dokter gizi.\n• Farmasi Multi-Batch: Pelacakan batch penerimaan obat, tanggal kedaluwarsa (FIFO/FEFO), dan penjualan OTC.\n• Penunjang Medis: Modul Laboratorium Patologi dan Dental Odontogram Gigi interaktif.', 0, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(6, 'v2.0.0', 'Fondasi ERP MVC-S, RBAC 10 Peran & Journal Engine', 'CORE ENGINE', 'secondary', '2026-08-15', 'Arsitektur dasar sistem ERP terintegrasi, manajemen hak akses multi-peran, dan pembukuan otomatis.', '• RBAC 10 Peran Pengguna: Hak akses terisolasi untuk seluruh departemen.\n• Journal Engine Otomatis: Transaksi lunas otomatis membukukan jurnal debet/kredit ke COA.\n• Audit Trail Logging: Pencatatan riwayat perubahan data, waktu, pengguna, dan IP address.', 1, 1, 1, '2026-08-22 06:37:16', '2026-08-22 06:37:16'),
(7, 'v2.6.0', 'Pusat Bantuan Dinamis, Standar Akuntansi Bank Indonesia & Role-Based Dashboard Suite', 'FINANSIAL & ENTERPRISE', 'teal', '2026-08-26', 'Rilis komprehensif: Rekam Medis Elektronik (RME) Super Lengkap multi-tab (SOAP, TTV & IMT Tracker, e-Resep kumulatif, Surat Medis, Hasil Lab & Cetak PDF A4), Aksesibilitas Rekam Medis mandiri, Pusat Bantuan CRUD mandiri, Laporan Keuangan resmi standar BI & SAK EMKM, Buku Besar Running Balance, Dashboard Eksekutif berbasis peran, manajemen Saldo Awal, dan fitur Edit/Hapus Jurnal Penyesuaian.', '• Rekam Medis Elektronik (RME) Super Lengkap: Akses instan rekam medis pasien dengan klik No RM atau nama pasien di menu Pendaftaran. Dilengkapi multi-tab komprehensif: (1) Timeline Kunjungan & SOAP DPJP lengkap dengan ICD-10 & ICD-9, (2) Tabel Tren Tanda Vital (TTV) & kalkulasi otomatis IMT/BMI, (3) Rekapitulasi Riwayat Terapi Obat Farmasi Kumulatif, (4) Daftar Surat Medis (SKS/SKD/Rujukan) & Hasil Laboratorium, serta (5) Fitur Cetak Ringkasan RME (Medical Summary) format resmi A4 ber-KOP klinik.\n• Aksesibilitas Rekam Medis & SOAP: Menu Rekam Medis & SOAP kini tersedia langsung di navigasi sidebar bagi seluruh peran dan departemen terkait (Dokter, Perawat, Rekam Medis, Kepala Klinik, Direksi, Super Admin, IT) serta terintegrasi penuh dalam sistem Role-Based Permission (clinic.soap).\n• Fitur Koreksi & Edit Jurnal: Penambahan tombol Edit (Pensil) & Hapus (Tong Sampah) pada Buku Jurnal Umum untuk entri Manual/Penyesuaian dengan pemulihan saldo otomatis, modal edit dinamis, serta perlindungan kunci (Lock) untuk jurnal operasional otomatis.\n• Fitur Saldo Awal Akuntansi: Pembaruan logika penyimpanan saldo awal dengan auto-clean jurnal lama saat input ulang, dukungan koreksi angka 0/kosong, tombol \'Bersihkan Input ke 0\', dan tombol \'Hapus & Reset Semua Saldo ke 0\'.\n• Pusat Bantuan & Dokumentasi Mandiri: Fitur Dokumentasi & Alur Sistem berbasis database (system_documentations) dengan editor CRUD, generator diagram langkah visual, panduan per peran staf, FAQ, dan ekspor cetak PDF manual A4.\n• Jurnal Umum & Buku Besar: Penambahan kolom Sisa Saldo Berjalan (Running Balance) baris per baris dan posisi baris pertama Saldo Awal Dinamis lintas bulan.\n• Laporan Keuangan Standar BI & SAK: Format formal Laba Rugi Komprehensif, Neraca Posisi Keuangan (Aktiva vs Pasiva Balance Check), Arus Kas Metode Langsung, Rekap Omset Lini Usaha, dan lembar pengesahan 3 pihak.\n• Dashboard Eksekutif & Operasional: Perlindungan data finansial khusus pimpinan/keuangan dan panel metrik operasional harian untuk seluruh peran staf.\n• Resolusi Routing & Bebas 404: Penyesuaian seluruh tautan pintasan dashboard (Kas & Bank, Buku Piutang, Buku Hutang, RME Dokter, dan Laporan Resto) serta penambahan rute alias aman.\n• Pembersihan Transaksi Produksi: Reset 42 tabel transaksi dengan menjaga 100% keutuhan seluruh master referensi obat, produk, tarif, ICD, dan RBAC.\n• Paket Dokumen Proposal & Word: Penerbitan proposal penawaran resmi Rp 20 Juta beserta ekspor dokumen .docx dan .doc.', 1, 1, 1, '2026-08-25 18:51:23', '2026-08-26 09:01:12'),
(8, 'v2.7.0', 'Enterprise Advanced Modules & Clinical Excellence', 'ENTERPRISE SUITE', 'teal', '2026-08-27', 'Penambahan 6 modul enterprise: Odontogram FDI 32 Gigi & Triase IGD di Poli Gigi/RME, Buku Kartu Stok Digital (Stock Card Ledger), Aging Schedule Piutang/Hutang, Settlement Jasa Medis & Auto-Jurnal Fee Dokter, Matriks KPI & Evaluasi Kinerja SDM, serta WhatsApp Gateway Dispatcher & 1-Click Database Backup.', '• Odontogram Interaktif FDI 32 Gigi: Visual chart 32 gigi dengan palet diagnosis medis real-time di RME Poli Gigi (Normal, Caries, Filling, Missing, Crown, Radix, Extract).\n• Triase Cepat IGD/UGD (Skala ATS 1-5): Identifikasi tingkat kegawatan klinis pasien secara instan dengan indikator visual dan integrasi rekam medis.\n• Buku Kartu Stok Digital (Stock Card Ledger): Audit histori mutasi barang masuk (PO), keluar (e-Resep Medis & Penjualan Bebas), dan penyesuaian opname dengan saldo berjalan (Running Balance).\n• Buku Pembantu & Aging Schedule: Analisis umur piutang pasien/asuransi dan hutang vendor supplier (0-30, 31-60, 61-90, >90 hari).\n• Rekapitulasi & Settlement Jasa Medis Dokter: Manajemen bagi hasil dokter poliklinik terotomatisasi langsung ke Jurnal Akuntansi Beban Jasa Medis vs Kas/Bank.\n• Stempel Verifikasi & Generator QRIS Dinamis: Validasi sah pencetakan kwitansi kasir dilengkapi QR barcode dinamis.\n• POS Resto Sehat Cepat: Operasional pemesanan berbasis Nomor Antrean/Pesanan tanpa kewajiban meja.\n• Matriks KPI & Produktivitas SDM: Metrik kinerja dokter (pasien, resep, fee) dan rekapitulasi presensi disiplin kerja pegawai.\n• Disaster Recovery & WhatsApp Gateway: 1-Click download full database backup SQL serta WhatsApp Gateway Dispatcher & Log audit notifikasi pasien.', 1, 1, 1, '2026-08-26 09:26:06', '2026-08-26 09:52:45'),
(9, 'v2.8.0', 'Enterprise Unified Architecture: API-Ready, Event-Driven Hooks, CI4 Native Models & Automated Cron Jobs', 'ENTERPRISE ARCHITECTURE', 'success', '2026-08-27', 'Pembaruan arsitektur enterprise CodeIgniter 4: RESTful API-Ready Subsystem dengan Autentikasi Token, Decoupled Event-Driven Hooks, 33 CI4 Native Models dengan Built-in Validation Rules, Spark Scheduled Cron Tasks, Pelacakan Universal Audit Trail, dan Unified Testing Automation Suite.', '• RESTful API-Ready Subsystem (/api/v1): Proteksi ApiKeyFilter (X-API-KEY / Bearer Token) & ResponseTrait untuk endpoint Display TV Antrean Real-time, Pencarian & Registrasi Mandiri Pasien, Katalog & Stok Obat, serta Bridge HL7 FHIR R4 Encounter SATUSEHAT Kemenkes RI.\n• Decoupled Event-Driven Architecture: Integrasi CodeIgniter\\Events\\Events untuk pemicu otomatis lintas modul (patient.registered -> Kartu Digital, billing.paid -> Auto-Jurnal & Komisi Dokter, pharmacy.dispensed -> Audit e-Resep, pharmacy.stock_low -> Alert Pengadaan Kritis, system.error_logged -> APM Tracker).\n• 33 CodeIgniter 4 Native Models: Standardisasi layer data resmi dengan $allowedFields (Mass-Assignment Protection), $validationRules & $validationMessages terpusat, $useTimestamps otomatis, dan Entity Callbacks (Auto Bcrypt Password Hashing).\n• Universal Audit Trail Recording: Pelacakan otomatis seluruh aktivitas pengguna (CREATE, UPDATE, DELETE, PAYMENT, APPROVE, DISPENSE, EXPORT, LOGIN, LOGOUT) dengan filter interaktif dan badge visual multi-warna.\n• Spark Scheduled Cron Jobs: Perintah otomatis siap pakai untuk Windows Task Scheduler / Linux Cron (cron:check-expired-medicines, cron:monthly-depreciation, cron:daily-closing, cron:database-backup).\n• Automated Testing Suite (spark system:test-all): Suite pengujian otomatis terpadu yang memverifikasi 100% kesehatan 33 Models, Event Listeners, API Endpoints, Scheduled Cron, dan Transaksi Database ACID.', 1, 1, 1, '2026-08-26 10:34:45', '2026-08-26 10:34:45');

-- --------------------------------------------------------

--
-- Table structure for table `tindakan`
--

CREATE TABLE `tindakan` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tindakan`
--

INSERT INTO `tindakan` (`id`, `category_id`, `parent_id`, `code`, `name`, `description`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'TND-HAIR', 'HAIRSTUDIO', 'Sub Kategori Pelayanan Perawatan Rambut', 0.00, 'active', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(2, 2, 1, 'TND-HAIR-01', 'RAMBUT RONTOK', 'Sub Kecil Kategori Pelayanan: Terapi & Perawatan Rambut Rontok', 50000.00, 'active', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(3, 2, NULL, 'TND-FACIAL', 'FACIAL', 'Sub Kecil Kategori Pelayanan: Perawatan Wajah Facial', 100000.00, 'active', '2026-08-21 01:34:42', '2026-08-21 01:34:42'),
(4, 2, NULL, 'TDK-GIZI-IND', 'Pelayanan Terapi Gizi Klinis', 'Paket layanan dan tindakan medis gizi klinis terpadu', NULL, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(5, 2, 4, 'TDK-GIZI-01', 'Konsultasi & Asesmen Komposisi Tubuh (BIA Scanner)', 'Konsultasi & Asesmen Komposisi Tubuh (BIA Scanner)', 150000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(6, 2, 4, 'TDK-GIZI-02', 'Perencanaan Program Diet Medis Terpersonalisasi (Meal Plan)', 'Perencanaan Program Diet Medis Terpersonalisasi (Meal Plan)', 200000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(7, 2, 4, 'TDK-GIZI-03', 'Konsultasi Diet Terapi Penyakit Kronis (DM / Hipertensi / Ginjal)', 'Konsultasi Diet Terapi Penyakit Kronis (DM / Hipertensi / Ginjal)', 175000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(8, 2, 4, 'TDK-GIZI-04', 'Edukasi Gizi & Pantauan Kepatuhan Nutrisi Pasien', 'Edukasi Gizi & Pantauan Kepatuhan Nutrisi Pasien', 100000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(9, 2, NULL, 'TDK-DERM-IND', 'Pelayanan Estetika Medis & Dermal Care', 'Tindakan perawatan kulit dan regenerasi dermal medis', NULL, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(10, 2, 9, 'TDK-DERM-01', 'Medical Facial Deep Cleansing & Ozone Detox', 'Medical Facial Deep Cleansing & Ozone Detox', 250000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(11, 2, 9, 'TDK-DERM-02', 'Medical Chemical Peeling Brightening Anti-Aging', 'Medical Chemical Peeling Brightening Anti-Aging', 350000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47'),
(12, 2, 9, 'TDK-DERM-03', 'Post-Procedure Soothing Care & LED Light Therapy', 'Post-Procedure Soothing Care & LED Light Therapy', 200000.00, 'active', '2026-08-21 15:36:47', '2026-08-21 15:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_account_mappings`
--

CREATE TABLE `transaction_account_mappings` (
  `id` int(11) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_account_mappings`
--

INSERT INTO `transaction_account_mappings` (`id`, `transaction_type`, `debit_account_id`, `credit_account_id`, `created_at`) VALUES
(1, 'CLINIC_PAYMENT', 3, 9, '2026-08-20 16:29:18'),
(2, 'PHARMACY_SALE', 1, 10, '2026-08-20 16:29:18'),
(3, 'RESTO_SALE', 2, 11, '2026-08-20 16:29:18'),
(4, 'FEE_EXPENSE', 14, 7, '2026-08-20 16:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `triage_records`
--

CREATE TABLE `triage_records` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_pressure` varchar(10) DEFAULT NULL,
  `temperature` decimal(4,2) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL,
  `respiration` int(11) DEFAULT NULL,
  `complaints` text DEFAULT NULL,
  `anamnesis` text DEFAULT NULL,
  `nurse_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `email`, `phone`, `avatar`, `bio`, `password`, `plain_password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', NULL, 'admin@arm.co.id', NULL, 'uploads/avatars/avatar_user_1_1787377811.jpg', NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 1, 'active', '2026-08-20 16:29:18', '2026-08-22 05:50:11'),
(2, 'direktur', NULL, 'direksi@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 3, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(3, 'dr_andi', NULL, 'andi.sp@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 5, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(4, 'ns_rina', NULL, 'rina.pw@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 6, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(5, 'kasir_siti', NULL, 'siti.ks@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 7, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(6, 'keuangan_budi', NULL, 'budi.keu@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 10, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(7, 'accounting_edi', NULL, 'edi.acc@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 11, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(8, 'resto_santi', NULL, 'santi.resto@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 14, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(9, 'chef_joko', NULL, 'joko.chef@arm.co.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 15, 'active', '2026-08-20 16:29:18', '2026-08-22 04:19:46'),
(10, 'apoteker_dewi', NULL, 'dewi.apt@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 8, 'active', '2026-08-22 03:58:16', '2026-08-22 04:19:46'),
(11, 'gudang_agus', NULL, 'agus.gdg@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 9, 'active', '2026-08-22 03:58:16', '2026-08-22 04:19:46'),
(12, 'umum_bambang', NULL, 'bambang.um@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 12, 'active', '2026-08-22 03:58:17', '2026-08-22 04:19:46'),
(13, 'hrd_maya', NULL, 'maya.hrd@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 13, 'active', '2026-08-22 03:58:17', '2026-08-22 04:19:46'),
(14, 'dr_kepala', NULL, 'kepala@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 4, 'active', '2026-08-22 03:58:17', '2026-08-22 04:19:46'),
(15, 'manager_doni', NULL, 'doni.mgr@sawamawa.id', NULL, NULL, NULL, '$2y$10$w5FKigWvVTYCBfYN9a8lyOPtZtKGTUkS5TF8hy7NH49VpHXnQm3zu', 'admin123', 16, 'active', '2026-08-22 03:58:17', '2026-08-22 04:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`user_id`, `permission_id`, `created_at`) VALUES
(1, 1, '2026-08-22 12:26:49'),
(1, 2, '2026-08-22 12:26:49'),
(1, 3, '2026-08-22 12:26:49'),
(1, 4, '2026-08-22 12:26:49'),
(1, 5, '2026-08-22 12:26:49'),
(1, 6, '2026-08-22 12:26:49'),
(1, 7, '2026-08-22 12:26:49'),
(1, 8, '2026-08-22 12:26:49'),
(1, 9, '2026-08-22 12:26:49'),
(1, 10, '2026-08-22 12:26:49'),
(1, 11, '2026-08-22 12:26:49'),
(1, 12, '2026-08-22 12:26:49'),
(1, 13, '2026-08-22 12:26:49'),
(1, 14, '2026-08-22 12:26:49'),
(1, 15, '2026-08-22 12:26:49'),
(1, 16, '2026-08-22 12:26:49'),
(1, 17, '2026-08-22 12:26:49'),
(2, 3, '2026-08-22 12:26:49'),
(2, 11, '2026-08-22 12:26:49'),
(2, 14, '2026-08-22 12:26:49'),
(2, 15, '2026-08-22 12:26:49'),
(2, 16, '2026-08-22 12:26:49'),
(2, 17, '2026-08-22 12:26:49'),
(3, 5, '2026-08-22 12:26:49'),
(4, 4, '2026-08-22 12:26:49'),
(4, 5, '2026-08-22 12:26:49'),
(5, 6, '2026-08-22 12:26:49'),
(5, 7, '2026-08-22 12:26:49'),
(5, 12, '2026-08-22 12:26:49'),
(6, 6, '2026-08-22 12:26:49'),
(6, 10, '2026-08-22 12:26:49'),
(6, 14, '2026-08-22 12:26:49'),
(6, 15, '2026-08-22 12:26:49'),
(7, 14, '2026-08-22 12:26:49'),
(7, 15, '2026-08-22 12:26:49'),
(7, 16, '2026-08-22 12:26:49'),
(8, 12, '2026-08-22 12:26:49'),
(9, 13, '2026-08-22 12:26:49'),
(10, 7, '2026-08-22 12:26:49'),
(10, 8, '2026-08-22 12:26:49'),
(10, 9, '2026-08-22 12:26:49'),
(11, 8, '2026-08-22 12:26:49'),
(11, 9, '2026-08-22 12:26:49'),
(11, 16, '2026-08-22 12:26:49'),
(12, 9, '2026-08-22 12:26:49'),
(12, 16, '2026-08-22 12:26:49'),
(13, 2, '2026-08-22 12:26:49'),
(13, 3, '2026-08-22 12:26:49'),
(13, 17, '2026-08-22 12:26:49'),
(14, 3, '2026-08-22 12:26:49'),
(14, 4, '2026-08-22 12:26:49'),
(14, 5, '2026-08-22 12:26:49'),
(14, 6, '2026-08-22 12:26:49'),
(14, 7, '2026-08-22 12:26:49'),
(14, 8, '2026-08-22 12:26:49'),
(14, 9, '2026-08-22 12:26:49'),
(14, 10, '2026-08-22 12:26:49'),
(14, 17, '2026-08-22 12:26:49'),
(15, 3, '2026-08-22 12:26:49'),
(15, 4, '2026-08-22 12:26:49'),
(15, 6, '2026-08-22 12:26:49'),
(15, 8, '2026-08-22 12:26:49'),
(15, 10, '2026-08-22 12:26:49'),
(15, 12, '2026-08-22 12:26:49'),
(15, 14, '2026-08-22 12:26:49'),
(15, 17, '2026-08-22 12:26:49');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_logs`
--

CREATE TABLE `whatsapp_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `recipient_phone` varchar(30) NOT NULL,
  `recipient_name` varchar(150) DEFAULT NULL,
  `message_type` varchar(50) NOT NULL,
  `message_content` text NOT NULL,
  `status` enum('pending','sent','failed') NOT NULL DEFAULT 'sent',
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_shifts`
--

CREATE TABLE `work_shifts` (
  `id` int(11) NOT NULL,
  `shift_code` varchar(20) NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `late_tolerance_minutes` int(11) DEFAULT 15,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_shifts`
--

INSERT INTO `work_shifts` (`id`, `shift_code`, `shift_name`, `start_time`, `end_time`, `late_tolerance_minutes`, `status`, `created_at`) VALUES
(1, 'SHIFT-PAGI', 'Shift Pagi (Pelayanan/Poli)', '07:00:00', '14:00:00', 15, 'active', '2026-08-22 03:15:02'),
(2, 'SHIFT-SIANG', 'Shift Siang (Pelayanan/Apotek)', '14:00:00', '21:00:00', 15, 'active', '2026-08-22 03:15:02'),
(3, 'SHIFT-MALAM', 'Shift Malam (Jaga/UGD/Rawat)', '21:00:00', '07:00:00', 15, 'active', '2026-08-22 03:15:02'),
(4, 'SHIFT-REGULER', 'Shift Normal (Manajemen & Kantor)', '08:00:00', '16:00:00', 15, 'active', '2026-08-22 03:15:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indexes for table `approval_steps`
--
ALTER TABLE `approval_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_id` (`workflow_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `approval_workflows`
--
ALTER TABLE `approval_workflows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_type` (`transaction_type`);

--
-- Indexes for table `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `asset_mutations`
--
ALTER TABLE `asset_mutations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `billing_details`
--
ALTER TABLE `billing_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_id` (`billing_id`);

--
-- Indexes for table `billing_transactions`
--
ALTER TABLE `billing_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `billing_no` (`billing_no`),
  ADD UNIQUE KEY `visit_id` (`visit_id`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_no` (`receipt_no`),
  ADD KEY `billing_id` (`billing_id`),
  ADD KEY `cash_register_id` (`cash_register_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik_employee` (`nik_employee`),
  ADD KEY `polyclinic_id` (`polyclinic_id`),
  ADD KEY `fk_doc_category` (`category_id`),
  ADD KEY `fk_doc_tindakan` (`tindakan_id`);

--
-- Indexes for table `doctor_fee_settlements`
--
ALTER TABLE `doctor_fee_settlements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settlement_no` (`settlement_no`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indexes for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `fee_rules`
--
ALTER TABLE `fee_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `fee_transactions`
--
ALTER TABLE `fee_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `fee_rule_id` (`fee_rule_id`);

--
-- Indexes for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_no` (`receipt_no`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_receipt_id` (`goods_receipt_id`);

--
-- Indexes for table `icd9_procedures`
--
ALTER TABLE `icd9_procedures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Indexes for table `icd10_diagnoses`
--
ALTER TABLE `icd10_diagnoses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Indexes for table `internal_cash_transfers`
--
ALTER TABLE `internal_cash_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_no` (`transfer_no`);

--
-- Indexes for table `inventory_assets`
--
ALTER TABLE `inventory_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_no` (`journal_no`);

--
-- Indexes for table `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_detail_id` (`order_detail_id`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lab_no` (`lab_no`),
  ADD KEY `idx_lab_visit` (`visit_id`),
  ADD KEY `idx_lab_patient` (`patient_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `master_icd9`
--
ALTER TABLE `master_icd9`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `master_icd10`
--
ALTER TABLE `master_icd10`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `medical_letters`
--
ALTER TABLE `medical_letters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `letter_no` (`letter_no`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_id` (`visit_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_rules`
--
ALTER TABLE `notification_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rule_code` (`rule_code`);

--
-- Indexes for table `nurses`
--
ALTER TABLE `nurses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik_employee` (`nik_employee`),
  ADD KEY `polyclinic_id` (`polyclinic_id`);

--
-- Indexes for table `odontograms`
--
ALTER TABLE `odontograms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id_tooth_number` (`patient_id`,`tooth_number`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_rm` (`no_rm`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_visit` (`no_visit`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `polyclinic_id` (`polyclinic_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payroll_code` (`payroll_code`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_id` (`payroll_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `pharmacy_sales`
--
ALTER TABLE `pharmacy_sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_no` (`sale_no`),
  ADD KEY `idx_sale_date` (`sale_date`);

--
-- Indexes for table `pharmacy_sale_details`
--
ALTER TABLE `pharmacy_sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_ref` (`sale_id`),
  ADD KEY `idx_med_ref` (`medicine_id`);

--
-- Indexes for table `polikliniks`
--
ALTER TABLE `polikliniks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `polyclinics`
--
ALTER TABLE `polyclinics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_id` (`visit_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `prescription_details`
--
ALTER TABLE `prescription_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_no` (`po_no`),
  ADD KEY `purchase_request_id` (`purchase_request_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_no` (`request_no`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_id` (`purchase_request_id`);

--
-- Indexes for table `queue_numbers`
--
ALTER TABLE `queue_numbers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `polyclinic_id` (`polyclinic_id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Indexes for table `restaurant_menus`
--
ALTER TABLE `restaurant_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Indexes for table `restaurant_order_details`
--
ALTER TABLE `restaurant_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_no` (`table_no`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_services_parent` (`parent_id`);

--
-- Indexes for table `service_prices`
--
ALTER TABLE `service_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opname_no` (`opname_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opname_id` (`opname_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `system_documentations`
--
ALTER TABLE `system_documentations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `order_num` (`order_num`),
  ADD KEY `is_published` (`is_published`);

--
-- Indexes for table `system_error_logs`
--
ALTER TABLE `system_error_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `error_hash` (`error_hash`),
  ADD KEY `is_resolved` (`is_resolved`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_key` (`notification_key`),
  ADD KEY `idx_target_user` (`target_user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `system_notification_reads`
--
ALTER TABLE `system_notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_notif_user` (`notification_id`,`user_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_notif` (`notification_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_slow_queries`
--
ALTER TABLE `system_slow_queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_updates`
--
ALTER TABLE `system_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_version` (`version`),
  ADD KEY `idx_published_date` (`is_published`,`release_date`);

--
-- Indexes for table `tindakan`
--
ALTER TABLE `tindakan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `transaction_account_mappings`
--
ALTER TABLE `transaction_account_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_type` (`transaction_type`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `credit_account_id` (`credit_account_id`);

--
-- Indexes for table `triage_records`
--
ALTER TABLE `triage_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_id` (`visit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`user_id`,`permission_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_permission_id` (`permission_id`);

--
-- Indexes for table `whatsapp_logs`
--
ALTER TABLE `whatsapp_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `work_shifts`
--
ALTER TABLE `work_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shift_code` (`shift_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_steps`
--
ALTER TABLE `approval_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `approval_workflows`
--
ALTER TABLE `approval_workflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_mutations`
--
ALTER TABLE `asset_mutations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `billing_details`
--
ALTER TABLE `billing_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_transactions`
--
ALTER TABLE `billing_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctor_fee_settlements`
--
ALTER TABLE `doctor_fee_settlements`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_rules`
--
ALTER TABLE `fee_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fee_transactions`
--
ALTER TABLE `fee_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `icd9_procedures`
--
ALTER TABLE `icd9_procedures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `icd10_diagnoses`
--
ALTER TABLE `icd10_diagnoses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internal_cash_transfers`
--
ALTER TABLE `internal_cash_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_assets`
--
ALTER TABLE `inventory_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `master_icd9`
--
ALTER TABLE `master_icd9`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `master_icd10`
--
ALTER TABLE `master_icd10`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `medical_letters`
--
ALTER TABLE `medical_letters`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notification_rules`
--
ALTER TABLE `notification_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nurses`
--
ALTER TABLE `nurses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `odontograms`
--
ALTER TABLE `odontograms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pharmacy_sales`
--
ALTER TABLE `pharmacy_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_sale_details`
--
ALTER TABLE `pharmacy_sale_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `polikliniks`
--
ALTER TABLE `polikliniks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `polyclinics`
--
ALTER TABLE `polyclinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_details`
--
ALTER TABLE `prescription_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue_numbers`
--
ALTER TABLE `queue_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restaurant_menus`
--
ALTER TABLE `restaurant_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_order_details`
--
ALTER TABLE `restaurant_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `service_prices`
--
ALTER TABLE `service_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_documentations`
--
ALTER TABLE `system_documentations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `system_error_logs`
--
ALTER TABLE `system_error_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_notification_reads`
--
ALTER TABLE `system_notification_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `system_slow_queries`
--
ALTER TABLE `system_slow_queries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_updates`
--
ALTER TABLE `system_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tindakan`
--
ALTER TABLE `tindakan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaction_account_mappings`
--
ALTER TABLE `transaction_account_mappings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `triage_records`
--
ALTER TABLE `triage_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `whatsapp_logs`
--
ALTER TABLE `whatsapp_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `work_shifts`
--
ALTER TABLE `work_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_ibfk_1` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `approval_steps`
--
ALTER TABLE `approval_steps`
  ADD CONSTRAINT `approval_steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `approval_steps_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_depreciations`
--
ALTER TABLE `asset_depreciations`
  ADD CONSTRAINT `asset_depreciations_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `asset_maintenances`
--
ALTER TABLE `asset_maintenances`
  ADD CONSTRAINT `asset_maintenances_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `asset_mutations`
--
ALTER TABLE `asset_mutations`
  ADD CONSTRAINT `asset_mutations_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `billing_details`
--
ALTER TABLE `billing_details`
  ADD CONSTRAINT `billing_details_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `billing_transactions`
--
ALTER TABLE `billing_transactions`
  ADD CONSTRAINT `billing_transactions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD CONSTRAINT `cash_transactions_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cash_transactions_ibfk_2` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_tindakan` FOREIGN KEY (`tindakan_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD CONSTRAINT `employee_attendances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_attendances_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD CONSTRAINT `employee_leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fee_rules`
--
ALTER TABLE `fee_rules`
  ADD CONSTRAINT `fee_rules_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fee_rules_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fee_transactions`
--
ALTER TABLE `fee_transactions`
  ADD CONSTRAINT `fee_transactions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fee_transactions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fee_transactions_ibfk_3` FOREIGN KEY (`fee_rule_id`) REFERENCES `fee_rules` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD CONSTRAINT `goods_receipts_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  ADD CONSTRAINT `goods_receipt_items_ibfk_1` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `icd9_procedures`
--
ALTER TABLE `icd9_procedures`
  ADD CONSTRAINT `icd9_procedures_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `icd10_diagnoses`
--
ALTER TABLE `icd10_diagnoses`
  ADD CONSTRAINT `icd10_diagnoses_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_assets`
--
ALTER TABLE `inventory_assets`
  ADD CONSTRAINT `inventory_assets_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `journal_entry_details`
--
ALTER TABLE `journal_entry_details`
  ADD CONSTRAINT `journal_entry_details_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_entry_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  ADD CONSTRAINT `kitchen_orders_ibfk_1` FOREIGN KEY (`order_detail_id`) REFERENCES `restaurant_order_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medicine_batches`
--
ALTER TABLE `medicine_batches`
  ADD CONSTRAINT `medicine_batches_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `nurses`
--
ALTER TABLE `nurses`
  ADD CONSTRAINT `nurses_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `patient_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_visits_ibfk_2` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_visits_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD CONSTRAINT `payroll_items_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_items_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `polikliniks`
--
ALTER TABLE `polikliniks`
  ADD CONSTRAINT `polikliniks_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `prescription_details`
--
ALTER TABLE `prescription_details`
  ADD CONSTRAINT `prescription_details_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescription_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `purchase_requests_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `queue_numbers`
--
ALTER TABLE `queue_numbers`
  ADD CONSTRAINT `queue_numbers_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `queue_numbers_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_orders`
--
ALTER TABLE `restaurant_orders`
  ADD CONSTRAINT `restaurant_orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `restaurant_orders_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_order_details`
--
ALTER TABLE `restaurant_order_details`
  ADD CONSTRAINT `restaurant_order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `restaurant_order_details_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `restaurant_menus` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_parent` FOREIGN KEY (`parent_id`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `service_prices`
--
ALTER TABLE `service_prices`
  ADD CONSTRAINT `service_prices_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD CONSTRAINT `stock_opnames_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_opname_details`
--
ALTER TABLE `stock_opname_details`
  ADD CONSTRAINT `stock_opname_details_ibfk_1` FOREIGN KEY (`opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_opname_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_opname_details_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tindakan`
--
ALTER TABLE `tindakan`
  ADD CONSTRAINT `tindakan_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tindakan_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaction_account_mappings`
--
ALTER TABLE `transaction_account_mappings`
  ADD CONSTRAINT `transaction_account_mappings_ibfk_1` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_account_mappings_ibfk_2` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `triage_records`
--
ALTER TABLE `triage_records`
  ADD CONSTRAINT `triage_records_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
