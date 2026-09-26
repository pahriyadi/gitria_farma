<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemUpdatesTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS system_updates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                version VARCHAR(20) NOT NULL,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(50) NOT NULL DEFAULT 'FITUR BARU',
                badge_color VARCHAR(30) NOT NULL DEFAULT 'teal',
                release_date DATE NOT NULL,
                summary TEXT NULL,
                details TEXT NULL,
                is_major TINYINT(1) NOT NULL DEFAULT 0,
                is_published TINYINT(1) NOT NULL DEFAULT 1,
                created_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_version (version),
                INDEX idx_published_date (is_published, release_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed initial release notes if empty
        $count = $this->db->table('system_updates')->countAllResults();
        if ($count === 0) {
            $this->db->table('system_updates')->insertBatch([
                [
                    'version'      => 'v2.5.0',
                    'title'        => 'Implementasi DataTables Server-Side Processing & Kecepatan Tinggi',
                    'category'     => 'PERFORMA & DATA',
                    'badge_color'  => 'teal',
                    'release_date' => '2026-08-22',
                    'summary'      => 'Seluruh tabel berdata besar kini memuat ribuan baris data secara instan (< 15 ms) tanpa membebani memori browser menggunakan arsitektur Server-Side Processing.',
                    'details'      => "• Pendaftaran Pasien: Pencarian instan berdasarkan No RM, NIK, Nama, Telp, dan Alamat.\n• Stok Apotek: Agregasi stok fisik dan peringatan stok kritis otomatis.\n• Master ICD-10 & ICD-9-CM: Katalog kode diagnosa dan prosedur tindakan medis dengan paginasi instan.\n• Audit Trail: Pelacakan log aktivitas pengguna, aksi, modul, dan IP address terfilter.\n• Buku Jurnal Umum: Paginasi entri jurnal double-entry otomatis beserta rincian mutasi debet/kredit.",
                    'is_major'     => 1,
                    'is_published' => 1,
                    'created_by'   => 1
                ],
                [
                    'version'      => 'v2.4.0',
                    'title'        => 'Setup Saldo Awal Sistem & Master Laporan Keuangan Standar Audit',
                    'category'     => 'FINANSIAL & AKUNTANSI',
                    'badge_color'  => 'success',
                    'release_date' => '2026-08-21',
                    'summary'      => 'Penyempurnaan modul pembukuan akuntansi dengan wizard saldo awal cerdas dan 5 laporan keuangan komprehensif berstandar audit resmi.',
                    'details'      => "• Setup Saldo Awal (Smart Auto-Balancing): Wizard input kas, bank, persediaan, aset, hutang, dan modal dengan penyeimbangan otomatis ke Modal Awal Disetor (3-101).\n• Laporan Laba Rugi Komprehensif: Pendapatan operasional, HPP, beban nakes & operasional, hingga Net Profit.\n• Laporan Neraca: Sisi Aktiva vs Pasiva dengan indikator status Balanced Badge.\n• Laporan Arus Kas & Rekap Omset Unit Bisnis: Lacak arus kas dan proporsi omset poli/farmasi/resto/lab.\n• Buku Besar Kronologis (General Ledger): Rincian transaksi per rekening COA.\n• Lembar Cetak Laporan Keuangan Resmi: Format resmi berkop surat klinik dan tanda tangan direktur.",
                    'is_major'     => 1,
                    'is_published' => 1,
                    'created_by'   => 1
                ],
                [
                    'version'      => 'v2.3.0',
                    'title'        => 'Master Tarif Tindakan Bertingkat & Poliklinik Baru',
                    'category'     => 'KLINIK & MEDIS',
                    'badge_color'  => 'info',
                    'release_date' => '2026-08-20',
                    'summary'      => 'Pengelompokan layanan medis secara hierarkis (Parent-Child) dan penambahan master poli serta spesialis.',
                    'details'      => "• Kategori & Layanan Bertingkat (Parent-Child): Tindakan medis dikelompokkan ke dalam kategori induk.\n• Dropdown Optgroup Terstruktur: Formulir pendaftaran menampilkan grup tindakan rapi.\n• Manajemen Poliklinik & Dokter: Penambahan poli spesialis baru dan jadwal praktek.\n• Pengaturan Jasa Medis & Komisi Nakes: Persentase bagi hasil terhitung otomatis saat transaksi dibayar.",
                    'is_major'     => 0,
                    'is_published' => 1,
                    'created_by'   => 1
                ],
                [
                    'version'      => 'v2.2.0',
                    'title'        => 'Landing Page Publik & Portal Reservasi Pasien Online',
                    'category'     => 'PUBLIC PORTAL',
                    'badge_color'  => 'primary',
                    'release_date' => '2026-08-19',
                    'summary'      => 'Website profil modern Sawamawa Medical Center dan formulir pendaftaran pasien online aman.',
                    'details'      => "• Landing Page Premium: Profil dokter, poliklinik, layanan apotek & resto, dan peta lokasi.\n• Registrasi Pasien Baru Online: Penerbitan No RM otomatis terlindungi CSRF dan verifikasi NIK.\n• Booking Kunjungan Pasien Lama: Reservasi poli cepat dengan verifikasi ganda No RM / NIK dan Tanggal Lahir.",
                    'is_major'     => 0,
                    'is_published' => 1,
                    'created_by'   => 1
                ],
                [
                    'version'      => 'v2.1.0',
                    'title'        => 'Restoran Sehat Touchscreen POS, KDS Dapur & Apotek Multi-Batch',
                    'category'     => 'FARMASI & RESTO',
                    'badge_color'  => 'warning',
                    'release_date' => '2026-08-18',
                    'summary'      => 'Modul kasir layar sentuh restoran sehat, Kitchen Display System (KDS), dan penelusuran batch farmasi.',
                    'details'      => "• Resto & Cafe Sehat: Layar kasir touchscreen, manajemen meja, dan integrasi rujukan diet dokter gizi.\n• Farmasi Multi-Batch: Pelacakan batch penerimaan obat, tanggal kedaluwarsa (FIFO/FEFO), dan penjualan OTC.\n• Penunjang Medis: Modul Laboratorium Patologi dan Dental Odontogram Gigi interaktif.",
                    'is_major'     => 0,
                    'is_published' => 1,
                    'created_by'   => 1
                ],
                [
                    'version'      => 'v2.0.0',
                    'title'        => 'Fondasi ERP MVC-S, RBAC 10 Peran & Journal Engine',
                    'category'     => 'CORE ENGINE',
                    'badge_color'  => 'secondary',
                    'release_date' => '2026-08-15',
                    'summary'      => 'Arsitektur dasar sistem ERP terintegrasi, manajemen hak akses multi-peran, dan pembukuan otomatis.',
                    'details'      => "• RBAC 10 Peran Pengguna: Hak akses terisolasi untuk seluruh departemen.\n• Journal Engine Otomatis: Transaksi lunas otomatis membukukan jurnal debet/kredit ke COA.\n• Audit Trail Logging: Pencatatan riwayat perubahan data, waktu, pengguna, dan IP address.",
                    'is_major'     => 1,
                    'is_published' => 1,
                    'created_by'   => 1
                ]
            ]);
        }
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS system_updates;");
    }
}
