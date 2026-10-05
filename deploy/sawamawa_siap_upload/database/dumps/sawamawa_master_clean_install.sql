-- ========================================================
-- SAWAMAWA MEDICAL CENTER & RESTO GIZI - MASTER DATABASE
-- Clean Install Dump with Distributor Module
-- Generated: 2026-09-28 14:19:42
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- --------------------------------------------------------
-- Table structure for `accounts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `accounts`;
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
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `accounts`
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('1', '1', 'Kas', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('2', '11', 'Kas Apotek', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('3', '111', 'Kas Tunai Apotek', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('4', '1111', 'Kas kasir 1', 'asset', NULL, 'debit', '161500.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('5', '1112', 'Kas kasir 2', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('6', '112', 'Kas Bank Apotek', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('7', '1121', 'Kas Digital', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('8', '12', 'Piutang', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('9', '121', 'Piutang Asuransi BPJS', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('10', '122', 'Piutang Antar Unit', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('11', '123', 'Cadangan Kerugian Piutang', 'asset', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('12', '13', 'Uang Muka & Biaya dibayar di Muka', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('13', '131', 'Uang Muka Pembelian Barang', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('14', '132', 'Uang Muka Pembelian Aset', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('15', '133', 'Uang Muka Lainnya', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('16', '134', 'Biaya Dibayar di Muka', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('17', '1341', 'Sewa Dibayar di Muka', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('18', '1342', 'Asuransi Dibayar di Muka', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('19', '1343', 'Langganan Dibayar di Muka', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('20', '1344', 'Biaya Dibayar di Muka Lainnya', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('21', '14', 'Persediaan', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('22', '141', 'Persediaan Obat', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('23', '1411', 'Gitria Derma', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('24', '1412', 'Obat Resep', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('25', '1413', 'Obat Bebas Terbatas', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('26', '1414', 'Obat Bebas', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('27', '1415', 'Obat Generic', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('28', '1416', 'Obat Bermerek', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('29', '1417', 'Obat Narkotika', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('30', '1418', 'Obat Psikotropika', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('31', '1419', 'Obat Prekursor', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('32', '14110', 'Obat Program/Khusus', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('33', '14111', 'Obat Keras', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('34', '142', 'Persediaan Alat Kesehatan', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('35', '1421', 'Alkes Habis Pakai', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('36', '1422', 'Alkes Diagnostik', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('37', '1423', 'Alkes Non Medis', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('38', '1424', 'APD', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('39', '1425', 'Perlengkapan Medis', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('40', '143', 'Persediaan Kosmetic & Personal Care', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('41', '1431', 'Kosmetik Wajah', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('42', '1432', 'Kosmetik Tubuh', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('43', '1433', 'Perawatan Rambut', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('44', '1434', 'Perawatan Mulut', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('45', '1435', 'Personal Care Lainnya', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('46', '144', 'Persediaan Drug Store', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('47', '1441', 'Susu', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('48', '1442', 'Makanan & Minuman', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('49', '1443', 'Vitamin & Minuman Kesehatan', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('50', '1444', 'Perlengkapan Bayi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('51', '1445', 'Perlengkapan Rumah Tangga', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('52', '1446', 'ATK & Aksesori', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('53', '1447', 'Lain-lain', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('54', '145', 'Persediaan Herbal & Suplemen', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('55', '1451', 'Herbal', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('56', '1452', 'Suplemen', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('57', '1453', 'Vitamin', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('58', '1454', 'Produk Nutrisi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('59', '146', 'Persediaan Konsinyasi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('60', '1461', 'Obat Konsinyasi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('61', '1462', 'Alkes Konsinyasi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('62', '1463', 'Kosmetik Konsinyasi', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('63', '147', 'Persediaan Dalam Perjalanan', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('64', '148', 'Persediaan Barang Rusak', 'asset', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('65', '149', 'Cadangan Penurunan Nilai Persediaan', 'asset', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('66', '2', 'Liabilitas', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('67', '21', 'Utang Supplier & Vendor', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('68', '211', 'Utang Dagang', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('69', '212', 'Utang Vendor Jasa', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('70', '22', 'Utang Operasional', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('71', '221', 'Utang Utilitas', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('72', '222', 'Utang Pajak', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('73', '223', 'Utang BPJS', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('74', '224', 'Utang Sewa', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('75', '225', 'Utang Lainnya', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('76', '23', 'Utang SDM', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('77', '231', 'Utang Gaji', 'liability', NULL, 'credit', '1265.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('78', '232', 'Utang THR', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('79', '233', 'Utang Bonus', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('80', '24', 'Utang Tenaga Medis', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('81', '241', 'Utang Fee Dokter', 'liability', NULL, 'credit', '575.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('82', '242', 'Utang Fee Karyawan', 'liability', NULL, 'credit', '10000.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('83', '25', 'Hutang Bank', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('84', '26', 'Hutang Internal', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('85', '27', 'Pendapatan Diterima Dimuka', 'liability', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('86', '3', 'Modal/Prive/Ekuitas', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('87', '31', 'Modal Saham', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('88', '311', 'Modal Disetor', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('89', '312', 'Tambahan Modal Disetor', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('90', '32', 'Saldo Laba', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('91', '321', 'Laba Tahun Berjalan', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('92', '322', 'Laba Ditahan', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('93', '33', 'Cadangan', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('94', '331', 'Cadangan Umum', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('95', '332', 'Cadangan Investasi', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('96', '34', 'Dividen', 'equity', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('97', '341', 'Dividen Belum Dibayar', 'equity', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('98', '342', 'Dividen Dibayarkan', 'equity', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('99', '35', 'Prive', 'equity', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('100', '4', 'Pendapatan', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('101', '41', 'Penjualan', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('102', '411', 'Obat Resep', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('103', '412', 'Obat Bebas', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('104', '413', 'Obat', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('105', '414', 'OTC', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('106', '415', 'BMHP', 'revenue', NULL, 'credit', '7610.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('107', '416', 'Alkes', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('108', '417', 'Kosmetik', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('109', '418', 'Suplemen', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('110', '419', 'Konsul Online', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('111', '42', 'Pendapatan Jasa Dokter', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('112', '421', 'Administrasi', 'revenue', NULL, 'credit', '4395.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('113', '422', 'Konseling Farmasi', 'revenue', NULL, 'credit', '1099.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('114', '423', 'Penunjang', 'revenue', NULL, 'credit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('115', '424', 'Dokter', 'revenue', NULL, 'credit', '100000.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('116', '43', 'Pengurang Pendapatan', 'revenue', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('117', '431', 'Diskon', 'revenue', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('118', '432', 'Retur', 'revenue', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('119', '434', 'Promo', 'revenue', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('120', '5', 'Beban', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('121', '51', 'Harga Pokok Penjualan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('122', '511', 'HPP Obat', 'expense', NULL, 'debit', '-5060.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('123', '512', 'HPP Alat Kesehatan', 'expense', NULL, 'debit', '-1035.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('124', '513', 'HPP BMHP', 'expense', NULL, 'debit', '-805.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('125', '514', 'HPP Kosmetik', 'expense', NULL, 'debit', '-2760.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('126', '515', 'HPP Suplemen', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('127', '52', 'Beban Pajak', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('128', '521', 'PPh', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('129', '522', 'Pajak Daerah', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('130', '523', 'Pajak Lainnya', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('131', '53', 'Beban Personel', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('132', '531', 'Gaji Personel', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('133', '532', 'THR', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('134', '533', 'Bonus/Insentif', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('135', '534', 'Fee Dokter', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('136', '535', 'Fee Karyawan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('137', '536', 'BPJS Kesehatan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('138', '537', 'BPJS Ketenagakerjaan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('139', '538', 'Tunjangan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('140', '54', 'Beban Operasional', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('141', '541', 'Listrik', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('142', '542', 'Air', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('143', '543', 'Internet & Telepon', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('144', '544', 'ATK', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('145', '545', 'Kebersihan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('146', '546', 'Keamanan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('147', '547', 'Perawatan & Servis Peralatan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('148', '548', 'Transportasi', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('149', '549', 'Konsumsi Operasional', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('150', '55', 'Beban Administrasi & Umum', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('151', '551', 'Administrasi Bank', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('152', '552', 'Materai', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('153', '553', 'Perizinan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('154', '554', 'Iuran Organiasi', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('155', '555', 'Langganan Software/SIM Apotek', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('156', '556', 'Penyusutan Aset Tetap', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('157', '557', 'Amortisasi', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('158', '559', 'Bunga Pinjaman', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('159', '56', 'Beban Pemasaran', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('160', '561', 'Promosi', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('161', '562', 'Iklan', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('162', '563', 'Diskon Promosi', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('163', '564', 'Sponsorship', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('164', '565', 'Media Sosial/Content Creator', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('165', '57', 'Beban Lainnya', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('166', '571', 'Beban Obat Expired', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('167', '572', 'Beban Obat Rusak', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('168', '573', 'Selisih Stok Opname', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('169', '574', 'Beban Pemusnahan Obat', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('170', '575', 'Piutang Tak Tertagih', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('171', '576', 'Denda & Penalti', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('172', '577', 'Beban Lain-lain', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('173', '578', 'Beban fasilitas bersama', 'expense', NULL, 'debit', '0.00', '2026-09-05 01:14:51');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('174', '425', 'Fasilitas Klinik', 'revenue', '111', 'credit', '26896.00', '2026-09-05 15:05:05');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('175', '111.2', 'Kas Tunai Klinik', 'asset', NULL, 'debit', '0.00', '2026-09-05 18:34:54');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('177', '1-104', 'Kas Distributor', 'asset', NULL, 'debit', '0.00', '2026-09-28 19:20:12');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('178', '1-114', 'Bank Distributor', 'asset', NULL, 'debit', '0.00', '2026-09-28 19:20:12');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('179', '1-203', 'Piutang Usaha Distributor', 'asset', NULL, 'debit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('180', '1-302', 'Persediaan Barang Distributor', 'asset', NULL, 'debit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('181', '2-103', 'Utang Usaha Distributor', 'liability', NULL, 'credit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('182', '4-104', 'Pendapatan Penjualan Distributor', 'revenue', NULL, 'credit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('183', '4-204', 'Diskon Penjualan Distributor', 'revenue', NULL, 'debit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('184', '4-304', 'Retur Penjualan Distributor', 'revenue', NULL, 'debit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('185', '5-104', 'Harga Pokok Penjualan (HPP) Distributor', 'expense', NULL, 'debit', '0.00', '2026-09-28 19:20:13');
INSERT INTO `accounts` (`id`, `code`, `name`, `type`, `parent_id`, `normal_balance`, `balance`, `created_at`) VALUES ('186', '6-104', 'Beban Operasional Distributor', 'expense', NULL, 'debit', '0.00', '2026-09-28 19:20:13');

-- --------------------------------------------------------
-- Table structure for `approval_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `approval_requests`;
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
  KEY `approver_id` (`approver_id`),
  CONSTRAINT `approval_requests_ibfk_1` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `approval_steps`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `approval_steps`;
CREATE TABLE `approval_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `step_name` varchar(50) NOT NULL,
  `step_level` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `approval_steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `approval_steps_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `approval_workflows`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `approval_workflows`;
CREATE TABLE `approval_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `articles`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `articles`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `asset_depreciations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `asset_depreciations`;
CREATE TABLE `asset_depreciations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `depreciation_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `book_value_after` decimal(15,2) DEFAULT 0.00,
  `journal_entry_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_depreciations_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `asset_maintenances`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `asset_maintenances`;
CREATE TABLE `asset_maintenances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `cost` decimal(15,2) DEFAULT 0.00,
  `technician_vendor` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('scheduled','in_progress','completed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_maintenances_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `asset_mutations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `asset_mutations`;
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
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_mutations_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `inventory_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `audit_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
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
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `beds`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `beds`;
CREATE TABLE `beds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `bed_number` varchar(30) NOT NULL,
  `tariff_per_day` decimal(15,2) DEFAULT 0.00,
  `status` enum('available','occupied','maintenance','reserved') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `beds_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `billing_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `billing_details`;
CREATE TABLE `billing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billing_id` int(11) NOT NULL,
  `item_type` enum('medis','obat','resto') NOT NULL,
  `is_racikan` tinyint(1) NOT NULL DEFAULT 0,
  `racikan_name` varchar(150) DEFAULT NULL,
  `parent_racikan_id` int(11) DEFAULT NULL,
  `item_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `tusla` decimal(15,2) DEFAULT 0.00,
  `embalase` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `billing_id` (`billing_id`),
  CONSTRAINT `billing_details_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `billing_transactions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `billing_transactions`;
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
  UNIQUE KEY `billing_no` (`billing_no`),
  UNIQUE KEY `visit_id` (`visit_id`),
  KEY `fk_bt_payment_method` (`payment_method_id`),
  CONSTRAINT `billing_transactions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bt_payment_method` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `cash_registers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cash_registers`;
CREATE TABLE `cash_registers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `cash_transactions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `cash_transactions`;
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
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `billing_id` (`billing_id`),
  KEY `cash_register_id` (`cash_register_id`),
  CONSTRAINT `cash_transactions_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing_transactions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cash_transactions_ibfk_2` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `consent_templates`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `consent_templates`;
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
  PRIMARY KEY (`id`),
  KEY `fk_ct_tindakan` (`tindakan_id`),
  CONSTRAINT `fk_ct_tindakan` FOREIGN KEY (`tindakan_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `departments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_customers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_customers`;
CREATE TABLE `distributor_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `npwp` varchar(50) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT 0.00,
  `current_receivable` decimal(15,2) DEFAULT 0.00,
  `payment_terms_days` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dist_cust_code` (`code`),
  KEY `idx_dist_cust_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `distributor_customers`
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('1', 'CUST-001', 'Apotek Sehat Sentosa', 'PT Sehat Sentosa Jaya', '081234567890', 'kontak@sehatsentosa.com', 'Jl. Sudirman No. 45, Palu', NULL, '25000000.00', '0.00', '30', 'active', '2026-09-28 19:20:13', '2026-09-28 19:20:13');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('2', 'CUST-002', 'Klinik Medika Pratama', 'Klinik Pratama Medika', '085298765432', 'admin@medikapratama.id', 'Jl. Diponegoro No. 12, Palu', NULL, '15000000.00', '0.00', '14', 'active', '2026-09-28 19:20:13', '2026-09-28 19:20:13');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('3', 'CUST-003', 'Toko Obat Sumber Waras', 'UD Sumber Waras', '081344556677', 'sumberwaras@gmail.com', 'Jl. Gajah Mada No. 88, Palu', NULL, '10000000.00', '0.00', '0', 'active', '2026-09-28 19:20:13', '2026-09-28 19:20:13');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('4', 'CUST-TEST-9456', 'PT Mitra Apotek Prima 399', 'PT Mitra Sehat', '081216936809', 'mitra@test.com', 'Jl. Ahmad Yani No. 99 (Updated)', NULL, '50000000.00', '0.00', '30', 'active', '2026-09-28 20:14:37', '2026-09-28 20:14:37');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('5', 'CUST-TEST-4609', 'PT Mitra Apotek Prima 827', 'PT Mitra Sehat', '081226287943', 'mitra@test.com', 'Jl. Ahmad Yani No. 99 (Updated)', NULL, '50000000.00', '0.00', '30', 'active', '2026-09-28 20:17:11', '2026-09-28 20:17:11');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('6', 'CUST-TEST-7003', 'PT Mitra Apotek Prima 704', 'PT Mitra Sehat', '081243984255', 'mitra@test.com', 'Jl. Ahmad Yani No. 99 (Updated)', NULL, '50000000.00', '180000.00', '30', 'active', '2026-09-28 20:18:19', '2026-09-28 20:18:19');
INSERT INTO `distributor_customers` (`id`, `code`, `name`, `company_name`, `phone`, `email`, `address`, `npwp`, `credit_limit`, `current_receivable`, `payment_terms_days`, `status`, `created_at`, `updated_at`) VALUES ('7', 'CUST-TEST-1072', 'PT Mitra Apotek Prima 152', 'PT Mitra Sehat', '081243992917', 'mitra@test.com', 'Jl. Ahmad Yani No. 99 (Updated)', NULL, '50000000.00', '120000.00', '30', 'active', '2026-09-28 20:19:06', '2026-09-28 20:19:06');

-- --------------------------------------------------------
-- Table structure for `distributor_payments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_payments`;
CREATE TABLE `distributor_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(50) NOT NULL,
  `receivable_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(30) DEFAULT 'cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dist_pay_no` (`payment_no`),
  KEY `idx_dist_pay_rec` (`receivable_id`),
  KEY `idx_dist_pay_cust` (`customer_id`),
  CONSTRAINT `fk_dist_pay_rec_1` FOREIGN KEY (`receivable_id`) REFERENCES `distributor_receivables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_receivables`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_receivables`;
CREATE TABLE `distributor_receivables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `total_receivable` decimal(15,2) NOT NULL,
  `total_paid` decimal(15,2) DEFAULT 0.00,
  `remaining_balance` decimal(15,2) NOT NULL,
  `status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `last_payment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dist_rec_sale` (`sale_id`),
  KEY `idx_dist_rec_cust` (`customer_id`),
  KEY `idx_dist_rec_due` (`due_date`),
  KEY `idx_dist_rec_status` (`status`),
  CONSTRAINT `fk_dist_rec_cust_2` FOREIGN KEY (`customer_id`) REFERENCES `distributor_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_rec_sale_1` FOREIGN KEY (`sale_id`) REFERENCES `distributor_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_return_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_return_details`;
CREATE TABLE `distributor_return_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `sale_detail_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `distributor_stock_id` int(11) DEFAULT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dist_retdet_ret` (`return_id`),
  KEY `idx_dist_retdet_med` (`medicine_id`),
  CONSTRAINT `fk_dist_retdet_med_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_retdet_ret_1` FOREIGN KEY (`return_id`) REFERENCES `distributor_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_returns`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_returns`;
CREATE TABLE `distributor_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_no` varchar(50) NOT NULL,
  `return_date` date NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_return_amount` decimal(15,2) DEFAULT 0.00,
  `refund_method` enum('deduct_receivable','cash_refund','bank_refund') DEFAULT 'deduct_receivable',
  `reason` text DEFAULT NULL,
  `status` enum('completed','cancelled') DEFAULT 'completed',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dist_ret_no` (`return_no`),
  KEY `idx_dist_ret_sale` (`sale_id`),
  KEY `idx_dist_ret_cust` (`customer_id`),
  CONSTRAINT `fk_dist_ret_cust_2` FOREIGN KEY (`customer_id`) REFERENCES `distributor_customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_ret_sale_1` FOREIGN KEY (`sale_id`) REFERENCES `distributor_sales` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_sale_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_sale_details`;
CREATE TABLE `distributor_sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `distributor_stock_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `unit` varchar(30) NOT NULL DEFAULT 'PCS',
  `buy_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `cogs_total` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dist_saledet_sale` (`sale_id`),
  KEY `idx_dist_saledet_med` (`medicine_id`),
  CONSTRAINT `fk_dist_saledet_med_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_saledet_sale_1` FOREIGN KEY (`sale_id`) REFERENCES `distributor_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_sales`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_sales`;
CREATE TABLE `distributor_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) NOT NULL,
  `sale_date` date NOT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_type` enum('cash','credit','transfer') NOT NULL DEFAULT 'cash',
  `payment_terms_days` int(11) DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `tax_percent` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `remaining_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `delivery_status` enum('pending','delivered') DEFAULT 'delivered',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dist_inv_no` (`invoice_no`),
  KEY `idx_dist_sale_cust` (`customer_id`),
  KEY `idx_dist_sale_date` (`sale_date`),
  CONSTRAINT `fk_dist_sales_cust_1` FOREIGN KEY (`customer_id`) REFERENCES `distributor_customers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_stock_movements`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_stock_movements`;
CREATE TABLE `distributor_stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `distributor_stock_id` int(11) DEFAULT NULL,
  `movement_type` enum('in','out','adjustment','transfer_in','transfer_out','return_in') NOT NULL,
  `qty` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `reference_no` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dist_mov_med` (`medicine_id`),
  KEY `idx_dist_mov_stock` (`distributor_stock_id`),
  KEY `idx_dist_mov_ref` (`reference_no`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_stock_transfer_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_stock_transfer_items`;
CREATE TABLE `distributor_stock_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `buy_price` decimal(15,2) DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dist_trfitem_trf` (`transfer_id`),
  KEY `idx_dist_trfitem_med` (`medicine_id`),
  CONSTRAINT `fk_dist_trfitem_med_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_trfitem_trf_1` FOREIGN KEY (`transfer_id`) REFERENCES `distributor_stock_transfers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_stock_transfers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_stock_transfers`;
CREATE TABLE `distributor_stock_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(50) NOT NULL,
  `transfer_date` date NOT NULL,
  `source_type` enum('distributor','pharmacy','resto','clinic') NOT NULL,
  `target_type` enum('distributor','pharmacy','resto','clinic') NOT NULL,
  `status` enum('draft','approved','completed','cancelled') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dist_trf_no` (`transfer_no`),
  KEY `idx_dist_trf_date` (`transfer_date`),
  KEY `idx_dist_trf_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `distributor_stocks`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `distributor_stocks`;
CREATE TABLE `distributor_stocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `buy_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dist_stock_med` (`medicine_id`),
  KEY `idx_dist_stock_batch` (`batch_no`),
  CONSTRAINT `fk_dist_stock_med_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `doctor_fee_settlements`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `doctor_fee_settlements`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `settlement_no` (`settlement_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `doctor_schedules`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `doctor_schedules`;
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
  KEY `fk_ds_doctor` (`doctor_id`),
  KEY `fk_ds_room` (`room_id`),
  CONSTRAINT `fk_ds_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ds_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `doctors`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nik_employee` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `polyclinic_id` int(11) DEFAULT NULL,
  `tindakan_id` int(11) DEFAULT NULL,
  `fee_type` enum('percentage','fixed_amount') NOT NULL DEFAULT 'percentage',
  `fee_per_pasien` decimal(15,2) DEFAULT 0.00,
  `prescription_fee_percent` decimal(5,2) NOT NULL DEFAULT 5.00,
  `sip_number` varchar(50) NOT NULL,
  `str_number` varchar(50) NOT NULL,
  `str_expiry` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `digital_signature` longtext DEFAULT NULL,
  `stamp_image` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik_employee` (`nik_employee`),
  KEY `polyclinic_id` (`polyclinic_id`),
  KEY `fk_doc_category` (`category_id`),
  KEY `fk_doc_tindakan` (`tindakan_id`),
  CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_doc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_doc_tindakan` FOREIGN KEY (`tindakan_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `employee_attendances`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employee_attendances`;
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
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `employee_attendances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `employee_attendances_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `employee_leaves`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employee_leaves`;
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
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `employee_leaves_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `employees`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `employees`;
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
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `fee_rules`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `fee_rules`;
CREATE TABLE `fee_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `flat_fee` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `fee_rules_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fee_rules_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `fee_transactions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `fee_transactions`;
CREATE TABLE `fee_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `fee_rule_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `visit_id` (`visit_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `fee_rule_id` (`fee_rule_id`),
  CONSTRAINT `fee_transactions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fee_transactions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fee_transactions_ibfk_3` FOREIGN KEY (`fee_rule_id`) REFERENCES `fee_rules` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `goods_receipt_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `goods_receipt_items`;
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
  PRIMARY KEY (`id`),
  KEY `goods_receipt_id` (`goods_receipt_id`),
  CONSTRAINT `goods_receipt_items_ibfk_1` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `goods_receipts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `goods_receipts`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `purchase_order_id` (`purchase_order_id`),
  CONSTRAINT `goods_receipts_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `icd10_diagnoses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `icd10_diagnoses`;
CREATE TABLE `icd10_diagnoses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `icd10_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `visit_id` (`visit_id`),
  CONSTRAINT `icd10_diagnoses_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `icd9_procedures`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `icd9_procedures`;
CREATE TABLE `icd9_procedures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `icd9_code` varchar(10) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `visit_id` (`visit_id`),
  CONSTRAINT `icd9_procedures_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `insurance_providers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `insurance_providers`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `internal_cash_transfers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `internal_cash_transfers`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_no` (`transfer_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `inventory_assets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `inventory_assets`;
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
  UNIQUE KEY `code` (`code`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `inventory_assets_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `job_positions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `job_positions`;
CREATE TABLE `job_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `base_salary_min` decimal(15,2) DEFAULT 0.00,
  `base_salary_max` decimal(15,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `job_positions_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `journal_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `journal_categories`;
CREATE TABLE `journal_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `module` varchar(50) NOT NULL DEFAULT 'apotek',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_code` (`category_code`),
  KEY `module` (`module`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `journal_category_rules`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `journal_category_rules`;
CREATE TABLE `journal_category_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `position` enum('debit','credit') NOT NULL DEFAULT 'credit',
  `calc_type` enum('percentage','fixed_amount','dynamic_fee','formula') NOT NULL DEFAULT 'percentage',
  `percentage_value` decimal(8,4) DEFAULT 0.0000,
  `fixed_amount_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `formula_code` varchar(50) DEFAULT NULL,
  `sort_order` int(5) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `account_id` (`account_id`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `journal_entries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `journal_entries`;
CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_no` varchar(30) NOT NULL,
  `entry_date` date NOT NULL,
  `source_module` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_no` (`journal_no`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `journal_entry_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `journal_entry_details`;
CREATE TABLE `journal_entry_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `journal_id` (`journal_id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `journal_entry_details_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `journal_entry_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `kitchen_orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kitchen_orders`;
CREATE TABLE `kitchen_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_detail_id` int(11) NOT NULL,
  `status` enum('new','cooking','ready') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_detail_id` (`order_detail_id`),
  CONSTRAINT `kitchen_orders_ibfk_1` FOREIGN KEY (`order_detail_id`) REFERENCES `restaurant_order_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `lab_results`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lab_results`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_no` (`lab_no`),
  KEY `idx_lab_visit` (`visit_id`),
  KEY `idx_lab_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `lab_tests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lab_tests`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `master_icd10`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `master_icd10`;
CREATE TABLE `master_icd10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `master_icd9`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `master_icd9`;
CREATE TABLE `master_icd9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name_id` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `medical_informed_consents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medical_informed_consents`;
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
  KEY `idx_consent_patient` (`patient_id`),
  KEY `idx_consent_visit` (`visit_id`),
  KEY `fk_mic_doctor` (`doctor_id`),
  CONSTRAINT `fk_mic_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `medical_letters`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medical_letters`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter_no` (`letter_no`),
  KEY `visit_id` (`visit_id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_ml_doctor` (`doctor_id`),
  CONSTRAINT `fk_ml_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ml_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `medical_photos`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medical_photos`;
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
  KEY `idx_photo_patient` (`patient_id`),
  KEY `idx_photo_visit` (`visit_id`),
  CONSTRAINT `fk_mp_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `medical_records`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medical_records`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_id` (`visit_id`),
  CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `medicine_batches`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicine_batches`;
CREATE TABLE `medicine_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `batch_no` varchar(50) NOT NULL,
  `buy_price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `expired_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `medicine_batches_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `medicine_categories`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicine_categories`;
CREATE TABLE `medicine_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `drug_class` enum('obat_bebas','obat_bebas_terbatas','obat_keras','psikotropika','narkotika','prekursor','alkes_bmhp','herbal','suplemen') NOT NULL DEFAULT 'obat_keras',
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `medicines`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicines`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_med_category` (`category_id`),
  KEY `fk_med_unit` (`unit_id`),
  CONSTRAINT `fk_med_category` FOREIGN KEY (`category_id`) REFERENCES `medicine_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_med_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `notification_rules`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notification_rules`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `rule_code` (`rule_code`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `nurses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `nurses`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `nik_employee` (`nik_employee`),
  KEY `polyclinic_id` (`polyclinic_id`),
  CONSTRAINT `nurses_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `odontograms`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `odontograms`;
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
  PRIMARY KEY (`id`),
  KEY `patient_id_tooth_number` (`patient_id`,`tooth_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `patient_visits`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patient_visits`;
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
  `satusehat_encounter_id` varchar(100) DEFAULT NULL,
  `satusehat_sync_time` datetime DEFAULT NULL,
  `visit_type` enum('poli','tindakan') DEFAULT 'poli',
  `service_id` int(11) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_visit` (`no_visit`),
  KEY `patient_id` (`patient_id`),
  KEY `polyclinic_id` (`polyclinic_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `fk_pv_service` (`service_id`),
  KEY `fk_pv_room` (`room_id`),
  KEY `fk_pv_insurance` (`insurance_id`),
  CONSTRAINT `fk_pv_insurance` FOREIGN KEY (`insurance_id`) REFERENCES `insurance_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pv_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pv_service` FOREIGN KEY (`service_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL,
  CONSTRAINT `patient_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `patient_visits_ibfk_2` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `patient_visits_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `patients`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_rm` varchar(20) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `satusehat_ihs_id` varchar(100) DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_rm` (`no_rm`),
  UNIQUE KEY `nik` (`nik`),
  KEY `fk_pat_insurance` (`insurance_provider_id`),
  CONSTRAINT `fk_pat_insurance` FOREIGN KEY (`insurance_provider_id`) REFERENCES `insurance_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `payment_methods`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment_methods`;
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
  UNIQUE KEY `code` (`code`),
  KEY `idx_active` (`is_active`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `payroll_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payroll_items`;
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
  PRIMARY KEY (`id`),
  KEY `payroll_id` (`payroll_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `payroll_items_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payroll_items_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `payrolls`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payrolls`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_code` (`payroll_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `permissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `permissions`
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('1', 'system.settings', 'Mengatur profil perusahaan & pengaturan sistem', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('2', 'users.manage', 'Manajemen data user & role permissions', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('3', 'audit.view', 'Melihat log audit trail system', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('4', 'clinic.register', 'Melakukan pendaftaran pasien online/offline', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('5', 'clinic.soap', 'Mengisi rekam medis SOAP & e-resep dokter', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('6', 'clinic.billing', 'Melakukan transaksi pembayaran di kasir klinik', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('7', 'pharmacy.dispense', 'Memproses e-resep & penjualan obat bebas apotek', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('8', 'pharmacy.stock', 'Mengelola stok persediaan obat farmasi', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('9', 'procurement.apply', 'Mengajukan PO pengadaan obat ke keuangan', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('10', 'procurement.verify', 'Verifikasi anggaran pengadaan PO oleh keuangan', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('11', 'procurement.approve', 'Persetujuan akhir pengadaan PO oleh Direksi', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('12', 'resto.order', 'Mencatat order & open bill meja restoran POS', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('13', 'resto.kitchen', 'Mengelola display antrean pesanan makanan dapur KDS', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('14', 'finance.manage', 'Mengelola penerimaan/pengeluaran kas & bank operasional', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('15', 'accounting.ledger', 'Melihat jurnal umum, COA, & laporan keuangan', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('16', 'inventory.manage', 'Mengelola inventaris aset umum non-medis', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('17', 'hrd.payroll', 'Mengelola presensi, nakes SIP & penggajian payroll', '2026-08-20 16:29:18');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('18', 'distributor.view', 'Melihat modul dan dashboard Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('19', 'distributor.sales', 'Melakukan transaksi penjualan kasir & faktur grosir Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('20', 'distributor.customers', 'Mengelola database customer & limit kredit Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('21', 'distributor.stock', 'Mengelola persediaan stok dan kartu stok Gudang Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('22', 'distributor.transfers', 'Melakukan transfer stok antar unit bisnis (Distributor, Apotek, Resto, Klinik)', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('23', 'distributor.receivables', 'Monitoring piutang jatuh tempo & pelunasan pembayaran piutang Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('24', 'distributor.returns', 'Mengelola retur penjualan Distributor', '2026-09-28 19:20:13');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('25', 'distributor.reports', 'Melihat laporan penjualan, piutang, stok, HPP dan Laba Rugi Distributor', '2026-09-28 19:20:13');

-- --------------------------------------------------------
-- Table structure for `pharmacy_pending_prescriptions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pharmacy_pending_prescriptions`;
CREATE TABLE `pharmacy_pending_prescriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pending_no` varchar(50) NOT NULL,
  `customer_name` varchar(150) NOT NULL DEFAULT 'Pelanggan Umum',
  `customer_phone` varchar(50) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `source_type` enum('otc','resep_dokter','kasir_klinik') DEFAULT 'otc',
  `payload_json` longtext NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tusla_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `embalase_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','resumed','cancelled') DEFAULT 'pending',
  `cashier_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pending_no` (`pending_no`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `pharmacy_sale_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pharmacy_sale_details`;
CREATE TABLE `pharmacy_sale_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `is_racikan` tinyint(1) NOT NULL DEFAULT 0,
  `racikan_name` varchar(150) DEFAULT NULL,
  `racikan_group` varchar(50) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tusla` decimal(15,2) NOT NULL DEFAULT 0.00,
  `embalase` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dosage_instruction` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sale_ref` (`sale_id`),
  KEY `idx_med_ref` (`medicine_id`),
  CONSTRAINT `fk_psd_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`),
  CONSTRAINT `fk_psd_sale` FOREIGN KEY (`sale_id`) REFERENCES `pharmacy_sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `pharmacy_sales`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pharmacy_sales`;
CREATE TABLE `pharmacy_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_no` varchar(50) NOT NULL,
  `prescription_type` enum('bebas','resep','racikan','online') NOT NULL DEFAULT 'bebas',
  `customer_name` varchar(150) NOT NULL DEFAULT 'Pelanggan Umum',
  `customer_phone` varchar(50) DEFAULT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tusla_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `embalase_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'tunai',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cashier_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_fee_nominal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sale_no` (`sale_no`),
  KEY `idx_sale_date` (`sale_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `pharmacy_warehouses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pharmacy_warehouses`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `polikliniks`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `polikliniks`;
CREATE TABLE `polikliniks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `polikliniks_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `polyclinics`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `polyclinics`;
CREATE TABLE `polyclinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `prescription_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `prescription_details`;
CREATE TABLE `prescription_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `is_racikan` tinyint(1) NOT NULL DEFAULT 0,
  `racikan_name` varchar(150) DEFAULT NULL,
  `racikan_group` varchar(50) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `tusla` decimal(15,2) DEFAULT 0.00,
  `embalase` decimal(15,2) DEFAULT 0.00,
  `status` enum('served','bought_outside','cancelled') DEFAULT 'served',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `prescription_id` (`prescription_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `prescription_details_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prescription_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `prescriptions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `tusla_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `embalase_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('waiting','processing','completed','cancelled') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dispensed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_id` (`visit_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `purchase_order_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_order_items`;
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
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `purchase_orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_orders`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_no` (`po_no`),
  KEY `purchase_request_id` (`purchase_request_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `purchase_request_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_request_items`;
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
  PRIMARY KEY (`id`),
  KEY `purchase_request_id` (`purchase_request_id`),
  CONSTRAINT `purchase_request_items_ibfk_1` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `purchase_requests`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `purchase_requests`;
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
  UNIQUE KEY `request_no` (`request_no`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `purchase_requests_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `queue_call_events`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `queue_call_events`;
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
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `status_call_priority_created_at` (`status`,`call_priority`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `queue_numbers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `queue_numbers`;
CREATE TABLE `queue_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `polyclinic_id` int(11) NOT NULL,
  `queue_no` varchar(10) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `polyclinic_id` (`polyclinic_id`),
  KEY `visit_id` (`visit_id`),
  CONSTRAINT `queue_numbers_ibfk_1` FOREIGN KEY (`polyclinic_id`) REFERENCES `polyclinics` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `queue_numbers_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `restaurant_menus`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `restaurant_menus`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `restaurant_order_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `restaurant_order_details`;
CREATE TABLE `restaurant_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('new','cooking','ready','served') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `restaurant_order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `restaurant_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `restaurant_order_details_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `restaurant_menus` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `restaurant_orders`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `restaurant_orders`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `table_id` (`table_id`),
  KEY `visit_id` (`visit_id`),
  CONSTRAINT `restaurant_orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `restaurant_orders_ibfk_2` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `restaurant_tables`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `restaurant_tables`;
CREATE TABLE `restaurant_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_no` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('empty','active') DEFAULT 'empty',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_no` (`table_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `role_permissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `role_permissions`
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('5', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('6', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('6', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('7', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('7', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('7', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('8', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('8', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('8', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('9', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('9', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('9', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('10', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('10', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('10', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('10', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('11', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('11', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('11', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('12', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('12', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('13', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('13', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('13', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('14', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('15', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('16', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('17', '25');

-- --------------------------------------------------------
-- Table structure for `roles`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `roles`
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('1', 'Super Admin', 'Akses penuh ke seluruh modul sistem', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('2', 'IT', 'Administrator sistem, database, dan log audit', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('3', 'Direksi', 'Pimpinan Utama holding company PT. ARM ERA CORPORAT', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('4', 'Kepala Klinik', 'Penanggung jawab operasional klinik utama', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('5', 'Dokter', 'Tenaga medis dokter spesialis / umum', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('6', 'Perawat', 'Tenaga medis nakes pembantu poli / salon', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('7', 'Kasir', 'Kasir utama pembayaran klinik & billing', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('8', 'Apoteker', 'Tenaga farmasi penyiapan e-resep apotek', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('9', 'Gudang', 'Petugas logistik stok obat & pengadaan barang', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('10', 'Koordinator Keuangan', 'Verifikasi anggaran pengadaan & pengeluaran kas', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('11', 'Accounting', 'Pengelola COA, pembukuan jurnal & laporan keuangan', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('12', 'Umum', 'Umum & Inventaris, pengelola aset non-medis', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('13', 'HRD', 'Manajemen kepegawaian, kehadiran & payroll', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('14', 'Resto/Kasir', 'Kasir penjualan restoran POS', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('15', 'Resto/Dapur', 'Koki & staf dapur KDS restoran', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('16', 'Manager', 'Manager operasional lintas divisi', '2026-08-21 00:29:18', '2026-08-21 00:29:18');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('17', 'Distributor', 'Akses operasional dan manajemen unit Distributor / Grosir', '2026-09-28 19:20:13', '2026-09-28 19:20:13');

-- --------------------------------------------------------
-- Table structure for `rooms`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `rooms`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `satusehat_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `satusehat_logs`;
CREATE TABLE `satusehat_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `resource_type` varchar(50) NOT NULL,
  `resource_id` varchar(100) DEFAULT NULL,
  `status` enum('success','failed','pending') DEFAULT 'pending',
  `http_status` int(11) DEFAULT NULL,
  `request_payload` longtext DEFAULT NULL,
  `response_payload` longtext DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `synced_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ss_visit` (`visit_id`),
  KEY `idx_ss_patient` (`patient_id`),
  KEY `idx_ss_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `service_prices`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `service_prices`;
CREATE TABLE `service_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `service_prices_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `services`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_services_parent` (`parent_id`),
  CONSTRAINT `fk_services_parent` FOREIGN KEY (`parent_id`) REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `stock_movements`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_movements`;
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
  KEY `medicine_id` (`medicine_id`),
  KEY `batch_id` (`batch_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `stock_opname_details`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_opname_details`;
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
  PRIMARY KEY (`id`),
  KEY `opname_id` (`opname_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `batch_id` (`batch_id`),
  CONSTRAINT `stock_opname_details_ibfk_1` FOREIGN KEY (`opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stock_opname_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `stock_opname_details_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `medicine_batches` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `stock_opnames`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_opnames`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `opname_no` (`opname_no`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_opnames_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `stock_transfer_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_transfer_items`;
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
  KEY `stock_transfer_id` (`stock_transfer_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `stock_transfers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stock_transfers`;
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
  UNIQUE KEY `transfer_no` (`transfer_no`),
  KEY `source_warehouse_id` (`source_warehouse_id`),
  KEY `target_warehouse_id` (`target_warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `suppliers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `suppliers`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_documentations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_documentations`;
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
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `order_num` (`order_num`),
  KEY `is_published` (`is_published`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_error_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_error_logs`;
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
  PRIMARY KEY (`id`),
  KEY `error_hash` (`error_hash`),
  KEY `is_resolved` (`is_resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_notification_reads`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_notification_reads`;
CREATE TABLE `system_notification_reads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notif_user` (`notification_id`,`user_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_notif` (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_notifications`;
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
  KEY `idx_category` (`category`),
  KEY `idx_key` (`notification_key`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_group` varchar(50) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for `system_settings`
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('1', 'klinik', 'clinic_name', 'Sawamawa Medical Center', 'Nama Resmi Klinik Utama', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('2', 'klinik', 'clinic_tagline', 'Pusat Layanan Medis Terpadu & Terpercaya', 'Slogan / Tagline Resmi', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('3', 'klinik', 'clinic_address', 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB', 'Alamat Kantor Klinik Utama', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('4', 'klinik', 'clinic_phone', '(0371) 23456 / 081122334455', 'Nomor Telepon Hotline Klinik', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('5', 'klinik', 'clinic_email', 'info@sawamawamedicalcenter.id', 'Email Resmi Pelayanan', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('6', 'klinik', 'clinic_website', 'www.sawamawamedicalcenter.id', 'Website Resmi Klinik', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('7', 'klinik', 'clinic_license_number', '445/012/DINKES/2024', 'Nomor Izin Operasional Klinik', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('8', 'klinik', 'clinic_license', '445/012/DINKES/2024', 'Alias Nomor Izin Operasional Klinik', '2026-09-06 11:33:14', '2026-09-06 11:33:14');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('9', 'klinik', 'clinic_faskes_code', '52040101', 'Kode Fasilitas Kesehatan (Kemenkes / BPJS / SatuSehat)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('10', 'klinik', 'clinic_director', 'dr. Andi Wijaya, Sp.PD', 'Nama Direktur / Penanggung Jawab Medis', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('11', 'klinik', 'clinic_head_doctor', 'dr. Andi Wijaya, Sp.PD', 'Dokter Penanggung Jawab Medis', '2026-09-06 11:33:14', '2026-09-06 11:33:14');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('12', 'klinik', 'clinic_head_sip', '503/449/SIP-D/2022', 'SIP Dokter Penanggung Jawab Medis', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('13', 'klinik', 'clinic_bpjs_active', 'true', 'Status Integrasi BPJS Kesehatan (true/false)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('14', 'branding', 'clinic_logo', '', 'File Logo Resmi Klinik', '2026-09-06 11:33:14', '2026-09-06 11:33:14');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('15', 'branding', 'clinic_favicon', '', 'File Favicon Website', '2026-09-06 11:33:14', '2026-09-06 11:33:14');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('16', 'branding', 'clinic_stamp', '', 'File Stempel Digital Resmi Klinik', '2026-09-06 11:33:14', '2026-09-06 11:33:14');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('17', 'branding', 'clinic_watermark', 'SAWAMAWA', 'Teks Watermark Lembar Dokumen', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('18', 'branding', 'clinic_footer_note', 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.', 'Catatan Kaki Dokumen Cetak', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('19', 'api', 'wa_gateway_provider', 'fonnte', 'Provider WhatsApp Gateway (fonnte/wablas/custom)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('20', 'api', 'wa_api_token', '', 'API Token WhatsApp Gateway', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('21', 'api', 'wa_sender_number', '081122334455', 'Nomor Pengirim WhatsApp Resmi', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('22', 'apotek', 'pharmacy_min_stock_alert', '10', 'Batas Minimum Stok untuk Notifikasi Stok Kritis', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('23', 'apotek', 'pharmacy_tax_percent', '10', 'Persentase Pajak Penjualan Obat Apotek (%)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('24', 'apotek', 'pharmacy_profit_margin_percent', '20', 'Persentase Margin Keuntungan Standar Obat (%)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('25', 'resto', 'resto_tax_percent', '10', 'Persentase Pajak Penjualan Restoran POS (%)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('26', 'resto', 'resto_service_charge_percent', '5', 'Persentase Biaya Pelayanan Restoran (%)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('27', 'resto', 'resto_auto_billing_inpatient', 'true', 'Otomatisasikan Pembebanan Resto ke Billing Pasien Rawat Inap (true/false)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('28', 'online_reg', 'online_registration_active', 'false', 'Status Pendaftaran Online (true/false)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('29', 'online_reg', 'online_registration_open_time', '06:00', 'Jam Buka Pendaftaran Online (WITA)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('30', 'online_reg', 'online_registration_close_time', '21:00', 'Jam Tutup Pendaftaran Online (WITA)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('31', 'online_reg', 'online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.', 'Pesan Saat Pendaftaran Online Ditutup', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('32', 'display', 'tv_media_type', 'slideshow', 'Tipe Media Display TV (slideshow/local_video/youtube)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('33', 'display', 'tv_video_url', '', 'URL Berkas Video Lokal TV', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('34', 'display', 'tv_youtube_id', 'dQw4w9WgXcQ', 'ID Video YouTube Layar TV', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('35', 'voice', 'voice_gender', 'female', 'Karakter Gender Suara Panggilan (female/male/auto)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('36', 'voice', 'voice_chime_type', 'hospital_2tone', 'Melodi Notifikasi Chime Antrean', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('37', 'voice', 'voice_rate', '0.85', 'Kecepatan Artikulasi Suara', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('38', 'voice', 'voice_pitch', '1.0', 'Tinggi Nada Suara (Pitch)', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('39', 'voice', 'voice_volume', '1.0', 'Volume Output Suara Panggilan', '2026-09-06 11:33:14', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('40', 'satusehat', 'satusehat_active', '1', 'Status Aktif Integrasi SATUSEHAT (true/false)', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('41', 'satusehat', 'satusehat_mode', 'sandbox', 'Mode Lingkungan SATUSEHAT (sandbox/staging/production)', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('42', 'satusehat', 'satusehat_org_id', '10000004', 'Organization ID Faskes SATUSEHAT Kemenkes', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('43', 'satusehat', 'satusehat_client_id', 'MOCK-CLIENT-ID-SAWAMAWA', 'Client ID DTO Kemenkes', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('44', 'satusehat', 'satusehat_client_secret', 'MOCK-CLIENT-SECRET-SAWAMAWA', 'Client Secret DTO Kemenkes', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('45', 'satusehat', 'satusehat_auth_url', 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1', 'URL OAuth2 Auth Token SATUSEHAT', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('46', 'satusehat', 'satusehat_fhir_url', 'https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1', 'Base URL HL7 FHIR R4 SATUSEHAT', '2026-09-26 00:43:50', '2026-09-26 23:12:42');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('47', 'backup', 'backup_last_daily', '2026-09-26 15:50:52', 'Timestamp Eksekusi Backup', '2026-09-26 15:50:52', '2026-09-26 15:50:52');
INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES ('48', 'backup', 'backup_last_monthly', '2026-09-26 15:50:52', 'Timestamp Eksekusi Backup', '2026-09-26 15:50:52', '2026-09-26 15:50:52');

-- --------------------------------------------------------
-- Table structure for `system_slow_queries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_slow_queries`;
CREATE TABLE `system_slow_queries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `query_sql` text NOT NULL,
  `execution_time_ms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `caller_location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `system_updates`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `system_updates`;
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
  PRIMARY KEY (`id`),
  KEY `idx_version` (`version`),
  KEY `idx_published_date` (`is_published`,`release_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `tindakan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tindakan`;
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
  UNIQUE KEY `code` (`code`),
  KEY `category_id` (`category_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `tindakan_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `tindakan_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `tindakan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `transaction_account_mappings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaction_account_mappings`;
CREATE TABLE `transaction_account_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(50) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_type` (`transaction_type`),
  KEY `debit_account_id` (`debit_account_id`),
  KEY `credit_account_id` (`credit_account_id`),
  CONSTRAINT `transaction_account_mappings_ibfk_1` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `transaction_account_mappings_ibfk_2` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `triage_records`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `triage_records`;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_id` (`visit_id`),
  KEY `fk_tr_nurse` (`nurse_id`),
  CONSTRAINT `fk_tr_nurse` FOREIGN KEY (`nurse_id`) REFERENCES `nurses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `triage_records_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `units`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category` enum('farmasi','logistik','aset','umum') DEFAULT 'farmasi',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `user_permissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `idx_idx_user_id` (`user_id`),
  KEY `idx_idx_permission_id` (`permission_id`),
  CONSTRAINT `fk_user_permissions_permission_id_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_permissions_user_id_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
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
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `warehouse_stock`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `warehouse_stock`;
CREATE TABLE `warehouse_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_batch_unique` (`warehouse_id`,`medicine_id`,`batch_id`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `whatsapp_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `whatsapp_logs`;
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

-- --------------------------------------------------------
-- Table structure for `work_shifts`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `work_shifts`;
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
  UNIQUE KEY `shift_code` (`shift_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
