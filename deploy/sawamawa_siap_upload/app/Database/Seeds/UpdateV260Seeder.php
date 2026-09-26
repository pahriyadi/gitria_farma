<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV260Seeder extends Seeder
{
    public function run()
    {
        $existing = $this->db->table('system_updates')->where('version', 'v2.6.0')->get()->getRow();
        
        $data = [
            'version'      => 'v2.6.0',
            'title'        => 'Pusat Bantuan Dinamis, Standar Akuntansi Bank Indonesia & Role-Based Dashboard Suite',
            'category'     => 'FINANSIAL & ENTERPRISE',
            'badge_color'  => 'teal',
            'release_date' => '2026-08-26',
            'summary'      => 'Rilis komprehensif: Rekam Medis Elektronik (RME) Super Lengkap multi-tab (SOAP, TTV & IMT Tracker, e-Resep kumulatif, Surat Medis, Hasil Lab & Cetak PDF A4), Aksesibilitas Rekam Medis mandiri, Pusat Bantuan CRUD mandiri, Laporan Keuangan resmi standar BI & SAK EMKM, Buku Besar Running Balance, Dashboard Eksekutif berbasis peran, manajemen Saldo Awal, dan fitur Edit/Hapus Jurnal Penyesuaian.',
            'details'      => "• Rekam Medis Elektronik (RME) Super Lengkap: Akses instan rekam medis pasien dengan klik No RM atau nama pasien di menu Pendaftaran. Dilengkapi multi-tab komprehensif: (1) Timeline Kunjungan & SOAP DPJP lengkap dengan ICD-10 & ICD-9, (2) Tabel Tren Tanda Vital (TTV) & kalkulasi otomatis IMT/BMI, (3) Rekapitulasi Riwayat Terapi Obat Farmasi Kumulatif, (4) Daftar Surat Medis (SKS/SKD/Rujukan) & Hasil Laboratorium, serta (5) Fitur Cetak Ringkasan RME (Medical Summary) format resmi A4 ber-KOP klinik.\n• Aksesibilitas Rekam Medis & SOAP: Menu Rekam Medis & SOAP kini tersedia langsung di navigasi sidebar bagi seluruh peran dan departemen terkait (Dokter, Perawat, Rekam Medis, Kepala Klinik, Direksi, Super Admin, IT) serta terintegrasi penuh dalam sistem Role-Based Permission (clinic.soap).\n• Fitur Koreksi & Edit Jurnal: Penambahan tombol Edit (Pensil) & Hapus (Tong Sampah) pada Buku Jurnal Umum untuk entri Manual/Penyesuaian dengan pemulihan saldo otomatis, modal edit dinamis, serta perlindungan kunci (Lock) untuk jurnal operasional otomatis.\n• Fitur Saldo Awal Akuntansi: Pembaruan logika penyimpanan saldo awal dengan auto-clean jurnal lama saat input ulang, dukungan koreksi angka 0/kosong, tombol 'Bersihkan Input ke 0', dan tombol 'Hapus & Reset Semua Saldo ke 0'.\n• Pusat Bantuan & Dokumentasi Mandiri: Fitur Dokumentasi & Alur Sistem berbasis database (system_documentations) dengan editor CRUD, generator diagram langkah visual, panduan per peran staf, FAQ, dan ekspor cetak PDF manual A4.\n• Jurnal Umum & Buku Besar: Penambahan kolom Sisa Saldo Berjalan (Running Balance) baris per baris dan posisi baris pertama Saldo Awal Dinamis lintas bulan.\n• Laporan Keuangan Standar BI & SAK: Format formal Laba Rugi Komprehensif, Neraca Posisi Keuangan (Aktiva vs Pasiva Balance Check), Arus Kas Metode Langsung, Rekap Omset Lini Usaha, dan lembar pengesahan 3 pihak.\n• Dashboard Eksekutif & Operasional: Perlindungan data finansial khusus pimpinan/keuangan dan panel metrik operasional harian untuk seluruh peran staf.\n• Resolusi Routing & Bebas 404: Penyesuaian seluruh tautan pintasan dashboard (Kas & Bank, Buku Piutang, Buku Hutang, RME Dokter, dan Laporan Resto) serta penambahan rute alias aman.\n• Pembersihan Transaksi Produksi: Reset 42 tabel transaksi dengan menjaga 100% keutuhan seluruh master referensi obat, produk, tarif, ICD, dan RBAC.\n• Paket Dokumen Proposal & Word: Penerbitan proposal penawaran resmi Rp 20 Juta beserta ekspor dokumen .docx dan .doc.",
            'is_major'     => 1,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->table('system_updates')->where('version', 'v2.6.0')->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('system_updates')->insert($data);
        }

        echo "Log rilis versi v2.6.0 berhasil diperbarui ke tabel system_updates.\n";
    }
}
