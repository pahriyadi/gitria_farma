<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV270Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');

        // 1. Insert or update system_updates for v2.7.0
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.7.0')->get()->getRow();
        $updateData = [
            'version'      => 'v2.7.0',
            'title'        => 'Enterprise Advanced Modules & Clinical Excellence',
            'category'     => 'ENTERPRISE SUITE',
            'badge_color'  => 'teal',
            'release_date' => '2026-08-27',
            'summary'      => 'Penambahan 6 modul enterprise: Odontogram FDI 32 Gigi & Triase IGD di Poli Gigi/RME, Buku Kartu Stok Digital (Stock Card Ledger), Aging Schedule Piutang/Hutang, Settlement Jasa Medis & Auto-Jurnal Fee Dokter, Matriks KPI & Evaluasi Kinerja SDM, serta WhatsApp Gateway Dispatcher & 1-Click Database Backup.',
            'details'      => "• Odontogram Interaktif FDI 32 Gigi: Visual chart 32 gigi dengan palet diagnosis medis real-time di RME Poli Gigi (Normal, Caries, Filling, Missing, Crown, Radix, Extract).\n• Triase Cepat IGD/UGD (Skala ATS 1-5): Identifikasi tingkat kegawatan klinis pasien secara instan dengan indikator visual dan integrasi rekam medis.\n• Buku Kartu Stok Digital (Stock Card Ledger): Audit histori mutasi barang masuk (PO), keluar (e-Resep Medis & Penjualan Bebas), dan penyesuaian opname dengan saldo berjalan (Running Balance).\n• Buku Pembantu & Aging Schedule: Analisis umur piutang pasien/asuransi dan hutang vendor supplier (0-30, 31-60, 61-90, >90 hari).\n• Rekapitulasi & Settlement Jasa Medis Dokter: Manajemen bagi hasil dokter poliklinik terotomatisasi langsung ke Jurnal Akuntansi Beban Jasa Medis vs Kas/Bank.\n• Stempel Verifikasi & Generator QRIS Dinamis: Validasi sah pencetakan kwitansi kasir dilengkapi QR barcode dinamis.\n• POS Resto Sehat Cepat: Operasional pemesanan berbasis Nomor Antrean/Pesanan tanpa kewajiban meja.\n• Matriks KPI & Produktivitas SDM: Metrik kinerja dokter (pasien, resep, fee) dan rekapitulasi presensi disiplin kerja pegawai.\n• Disaster Recovery & WhatsApp Gateway: 1-Click download full database backup SQL serta WhatsApp Gateway Dispatcher & Log audit notifikasi pasien.",
            'is_major'     => 1,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existingUpdate) {
            $db->table('system_updates')->where('id', $existingUpdate->id)->update($updateData);
        } else {
            $updateData['created_at'] = date('Y-m-d H:i:s');
            $db->table('system_updates')->insert($updateData);
        }

        // 2. Insert Help Documentation items for the new modules
        $helps = [
            [
                'category'     => 'workflow',
                'title'        => 'Odontogram FDI 32 Gigi & Triase IGD (Poli Gigi & Medis)',
                'target_role'  => 'Dokter Gigi & Perawat',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-tooth',
                'flow_steps'   => json_encode([
                    ['title' => '1. Buka Tab Odontogram', 'sub' => 'Di Rekam Medis SOAP', 'icon' => 'fas fa-tooth', 'color' => 'text-teal'],
                    ['title' => '2. Pilih Nomor Gigi', 'sub' => 'FDI Gigi 11 s/d 48', 'icon' => 'fas fa-hand-pointer', 'color' => 'text-primary'],
                    ['title' => '3. Pilih Kondisi Gigi', 'sub' => 'Caries/Filling/Missing', 'icon' => 'fas fa-palette', 'color' => 'text-warning'],
                    ['title' => '4. Auto-Save ke RME', 'sub' => 'Tersimpan Permanen', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan pencatatan kondisi 32 gigi pasien berbasis format internasional FDI World Dental Federation dan skala triase kegawatan IGD.',
                'content'      => "1. **Buka RME Pasien:** Akses menu *Klinik > Rekam Medis (SOAP)*, lalu pilih pasien yang ditangani.\n2. **Tab Odontogram:** Klik tab *4. Odontogram Poli Gigi*. Chart 32 gigi akan ditampilkan secara visual.\n3. **Diagnosa Gigi:** Klik gigi yang bermasalah, lalu pilih jenis kondisi klinis (Normal, Caries, Filling, Missing, Crown, Radix, Extract).\n4. **Triase Pasien:** Skala Triase ATS 1-5 (Resusitasi, Emergensi, Urgensi, Semi-Urgensi, Non-Urgensi) dapat diset langsung pada formulir SOAP perawat/dokter.",
                'order_num'    => 16,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Buku Kartu Stok Digital & Monitoring FEFO Obat',
                'target_role'  => 'Apoteker & Farmasi',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-book-medical',
                'flow_steps'   => json_encode([
                    ['title' => '1. Pilih Obat / Alkes', 'sub' => 'Dropdown Master Obat', 'icon' => 'fas fa-pills', 'color' => 'text-teal'],
                    ['title' => '2. Filter Periode', 'sub' => 'Rentang Tanggal', 'icon' => 'fas fa-calendar', 'color' => 'text-info'],
                    ['title' => '3. Audit Mutasi', 'sub' => 'PO, Resep, Penjualan', 'icon' => 'fas fa-list-check', 'color' => 'text-warning'],
                    ['title' => '4. Running Balance', 'sub' => 'Saldo Fisik Akurat', 'icon' => 'fas fa-calculator', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan pelacakan mutasi obat masuk, keluar, dan saldo akhir berjalan (Running Balance) pada buku kartu stok farmasi.',
                'content'      => "1. **Akses Menu:** Masuk ke *Apotek > Buku Kartu Stok*.\n2. **Pilih Obat:** Tentukan obat atau alkes yang ingin diaudit dari daftar dropdown.\n3. **Lihat Histori:** Sistem akan merekapitulasi seluruh mutasi dari Penerimaan PO (+), e-Resep Pasien (-), Penjualan Bebas (-), dan Penyesuaian Stok Opname (±).\n4. **Cetak Dokumen:** Klik tombol *Cetak Kartu Stok* untuk keperluan arsip audit farmasi resmi.",
                'order_num'    => 17,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Aging Schedule Piutang/Hutang & Settlement Fee Dokter',
                'target_role'  => 'Keuangan & Akuntansi',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-hourglass-half',
                'flow_steps'   => json_encode([
                    ['title' => '1. Cek Umur Piutang', 'sub' => '0-30, 31-60, >90 Hari', 'icon' => 'fas fa-clock', 'color' => 'text-primary'],
                    ['title' => '2. Rekap Fee Dokter', 'sub' => 'Klinik & Tindakan', 'icon' => 'fas fa-stethoscope', 'color' => 'text-teal'],
                    ['title' => '3. Form Settlement', 'sub' => 'Nominal & Kas/Bank', 'icon' => 'fas fa-credit-card', 'color' => 'text-warning'],
                    ['title' => '4. Auto Jurnal Umum', 'sub' => 'Beban vs Kas/Bank', 'icon' => 'fas fa-book', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan pengawasan umur piutang/hutang jatuh tempo dan pencairan bagi hasil jasa medis dokter poliklinik.',
                'content'      => "1. **Aging Schedule:** Buka *Keuangan > Aging Piutang & Hutang* untuk memantau status tagihan pasien/asuransi dan hutang PO distributor farmasi.\n2. **Settlement Fee Dokter:** Buka *Keuangan > Jasa Medis Dokter*, klik tombol *Bayarkan Fee* pada dokter yang bersangkutan.\n3. **Auto-Jurnal:** Sistem otomatis membuat entri Jurnal Akuntansi berimbang (Debit Beban Jasa Medis vs Kredit Rekening Kas/Bank) tanpa perlu input manual di buku jurnal.",
                'order_num'    => 18,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'system',
                'title'        => 'Error Tracker (APM) & System Scaling Monitor',
                'target_role'  => 'IT & Administrator',
                'badge_color'  => 'danger',
                'icon'         => 'fas fa-bug',
                'flow_steps'   => json_encode([
                    ['title' => '1. Pelacakan Error', 'sub' => 'Deduplikasi Otomatis', 'icon' => 'fas fa-bug', 'color' => 'text-danger'],
                    ['title' => '2. Inspeksi Trace', 'sub' => 'Modal Stack Trace', 'icon' => 'fas fa-code', 'color' => 'text-primary'],
                    ['title' => '3. Resolusi Isu', 'sub' => 'Tandai Selesai', 'icon' => 'fas fa-check-circle', 'color' => 'text-success'],
                    ['title' => '4. 1-Click Scaling', 'sub' => 'Cache & DB Defrag', 'icon' => 'fas fa-gauge-high', 'color' => 'text-teal']
                ]),
                'summary'      => 'Panduan pemantauan kesehatan sistem real-time, pelacakan exception crash, dan manajemen skalabilitas database & cache.',
                'content'      => "1. **Error Tracker & APM:** Buka *Administrasi Sistem > Error Tracker & APM* untuk memeriksa log eksepsi runtime, frekuensi kejadian, lokasi baris kode, dan jejak call stack.\n2. **Resolusi Isu:** Klik tombol *Trace* untuk melihat rincian stack trace dan klik *Selesai* setelah bug kode diperbaiki.\n3. **Scaling & Performa:** Buka *Administrasi Sistem > Scaling & Performa* untuk memeriksa penggunaan RAM server, OPcache, serta menjalankan *Bersihkan Cache* dan *Optimasi & Defrag Database* secara 1-klik.",
                'order_num'    => 19,
                'is_published' => 1,
                'created_by'   => 1
            ]
        ];

        foreach ($helps as $h) {
            $existingHelp = $db->table('system_documentations')->where('title', $h['title'])->get()->getRow();
            if ($existingHelp) {
                $db->table('system_documentations')->where('id', $existingHelp->id)->update(array_merge($h, ['updated_at' => date('Y-m-d H:i:s')]));
            } else {
                $db->table('system_documentations')->insert(array_merge($h, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]));
            }
        }

        echo "Log rilis v2.7.0 dan panduan bantuan berhasil dipublikasikan.\n";
    }
}
