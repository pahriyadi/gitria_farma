<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ComprehensiveHelpSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');

        $docs = [
            // =========================================================================
            // 1. WORKFLOWS (Peta Alur & Workflow Terpadu)
            // =========================================================================
            [
                'category'     => 'workflow',
                'title'        => 'Alur Pelayanan Pasien Rawat Jalan & Triase UGD',
                'target_role'  => 'Petugas Pendaftaran & Perawat',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-hospital-user',
                'flow_steps'   => json_encode([
                    ['title' => '1. Pendaftaran Pasien', 'sub' => 'NIK & No. RM Unik', 'icon' => 'fas fa-id-card', 'color' => 'text-primary'],
                    ['title' => '2. Pilih Poliklinik/Dokter', 'sub' => 'Poli Umum/Gigi/Spesialis', 'icon' => 'fas fa-stethoscope', 'color' => 'text-teal'],
                    ['title' => '3. Skala Triase ATS 1-5', 'sub' => 'Skrining Kegawatan', 'icon' => 'fas fa-heart-pulse', 'color' => 'text-danger'],
                    ['title' => '4. No Antrean & Billing Open', 'sub' => 'Cetak Karcis & RME', 'icon' => 'fas fa-ticket', 'color' => 'text-success']
                ]),
                'summary'      => 'Alur lengkap penerimaan pasien rawat jalan mulai dari pengecekan NIK/RM, penentuan poliklinik tujuan, skrining tanda vital dan skala triase kegawatan, hingga penerbitan tiket antrean dan tagihan billing.',
                'content'      => "1. **Pendaftaran Pasien:** Akses menu *Klinik > Pendaftaran Pasien*. Cari berdasarkan NIK/RM atau daftarkan pasien baru.\n2. **Kunjungan Baru:** Klik tombol *+ Kunjungan*, tentukan poliklinik, dokter yang bertugas, dan jenis penjamin (Umum / BPJS / Asuransi Swasta).\n3. **Skrining Triase:** Perawat menginput tanda vital (TD, Nadi, Suhu, RR) dan menetapkan Skala Triase ATS (1-Resusitasi s/d 5-Non Urgensi).\n4. **Antrean Display:** Sistem otomatis menerbitkan Nomor Antrean poliklinik yang langsung tersinkronisasi ke layar TV Ruang Tunggu (`/api/v1/antrean`).",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur RME Dokter, Odontogram 32 Gigi & e-Resep Medis',
                'target_role'  => 'Dokter Umum & Dokter Gigi',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-user-doctor',
                'flow_steps'   => json_encode([
                    ['title' => '1. Panggil Antrean RME', 'sub' => 'Menu SOAP Pasien', 'icon' => 'fas fa-bullhorn', 'color' => 'text-primary'],
                    ['title' => '2. Anamnesa & Fisik', 'sub' => 'Subjective & Objective', 'icon' => 'fas fa-notes-medical', 'color' => 'text-info'],
                    ['title' => '3. Diagnosa & Odontogram', 'sub' => 'ICD-10 & Chart 32 Gigi', 'icon' => 'fas fa-tooth', 'color' => 'text-warning'],
                    ['title' => '4. Buat e-Resep & Selesai', 'sub' => 'Terkirim ke Farmasi', 'icon' => 'fas fa-prescription-bottle', 'color' => 'text-success']
                ]),
                'summary'      => 'Proses pemeriksaan medis elektronik dokter: pencatatan anamnesa SOAP, pengisian odontogram interaktif 32 gigi FDI, penginputan diagnosa ICD-10/ICD-9, dan pembuatan resep elektronik terpadu.',
                'content'      => "1. **Pemeriksaan Pasien:** Buka *Klinik > Rekam Medis (SOAP)* dan klik *Periksa Pasien*.\n2. **Pencatatan SOAP:** Isi keluhan subjektif, hasil pemeriksaan objektif, dan diagnosa assessment ICD-10.\n3. **Odontogram Poli Gigi:** Untuk pemeriksaan gigi, klik tab *Odontogram* dan tentukan kondisi setiap gigi (Caries, Filling, Missing, Crown, Radix, Extract).\n4. **Resep Digital:** Tambahkan item obat dan aturan dosis, lalu klik *Simpan RME*. Resep akan otomatis muncul di modul farmasi secara *real-time*.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Apotek: Peracikan e-Resep, Penjualan Bebas & Kartu Stok FEFO',
                'target_role'  => 'Apoteker & Staf Farmasi',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-pills',
                'flow_steps'   => json_encode([
                    ['title' => '1. Terima e-Resep', 'sub' => 'Antrean Resep Masuk', 'icon' => 'fas fa-inbox', 'color' => 'text-primary'],
                    ['title' => '2. Pilih Batch FEFO', 'sub' => 'First Expired First Out', 'icon' => 'fas fa-boxes-stacked', 'color' => 'text-teal'],
                    ['title' => '3. Dispensing & Serahkan', 'sub' => 'Potong Stok Otomatis', 'icon' => 'fas fa-hand-holding-medical', 'color' => 'text-warning'],
                    ['title' => '4. Buku Kartu Stok Update', 'sub' => 'Audit Running Balance', 'icon' => 'fas fa-book-medical', 'color' => 'text-success']
                ]),
                'summary'      => 'Manajemen instalasi farmasi untuk penyerahan obat resep dan kasir penjualan bebas, pemilihan batch obat terdekat expired (FEFO), serta audit mutasi kartu stok real-time.',
                'content'      => "1. **Tebus Resep:** Buka menu *Apotek > e-Resep Masuk*. Pilih batch obat yang paling mendekati tanggal kadaluarsa (*FEFO Rule*).\n2. **Penjualan Bebas:** Kasir farmasi melayani pembelian obat bebas OTC dan alkes langsung melalui *Apotek > Penjualan Bebas*.\n3. **Buku Kartu Stok Digital:** Akses *Apotek > Buku Kartu Stok* untuk melihat riwayat keluar-masuk barang, referensi nomor transaksi, dan saldo akhir stok berjalan (*Running Balance*).",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Kasir Utama: Pelunasan Billing, Stempel Lunas & QRIS Dinamis',
                'target_role'  => 'Kasir Utama & Staf Billing',
                'badge_color'  => 'warning',
                'icon'         => 'fas fa-cash-register',
                'flow_steps'   => json_encode([
                    ['title' => '1. Buka Billing Pasien', 'sub' => 'Rekap Layanan & Obat', 'icon' => 'fas fa-file-invoice-dollar', 'color' => 'text-primary'],
                    ['title' => '2. Pilih Metode Bayar', 'sub' => 'Tunai/QRIS/Debit/Transfer', 'icon' => 'fas fa-credit-card', 'color' => 'text-teal'],
                    ['title' => '3. Auto-Jurnal Akuntansi', 'sub' => 'Debit Kas vs Pendapatan', 'icon' => 'fas fa-scale-balanced', 'color' => 'text-warning'],
                    ['title' => '4. Cetak Kwitansi Resmi', 'sub' => 'QR Validasi & Stempel', 'icon' => 'fas fa-print', 'color' => 'text-success']
                ]),
                'summary'      => 'Proses rekonsiliasi dan pelunasan tagihan rawat jalan, apotek, dan tindakan medis terintegrasi langsung dengan pembukuan jurnal akuntansi dan cetak bukti pembayaran resmi.',
                'content'      => "1. **Pelunasan Kasir:** Akses *Keuangan > Kasir Utama*, klik tagihan pasien yang berstatus *Open*.\n2. **Metode Pembayaran:** Pilih metode bayar (Tunai, QRIS Dinamis, Kartu Debit, atau Transfer Bank) serta input nominal diskon jika berlaku.\n3. **Integrasi Akuntansi Otomatis:** Saat tombol *Bayar & Lunas* ditekan, `JournalEngine` secara otomatis membukukan jurnal debit kas/bank dan kredit pendapatan klinik.\n4. **Cetak Lembar Kwitansi:** Kwitansi dilengkapi stempel digital resmi, rincian biaya, dan barcode QR validasi keaslian dokumen.",
                'order_num'    => 4,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Akuntansi Terpadu: Saldo Awal, Jurnal Umum & Laporan Keuangan',
                'target_role'  => 'Accounting & Koordinator Keuangan',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-scale-balanced',
                'flow_steps'   => json_encode([
                    ['title' => '1. Setup Saldo Awal', 'sub' => 'Smart Auto-Balancing COA', 'icon' => 'fas fa-sliders', 'color' => 'text-primary'],
                    ['title' => '2. Pencatatan Jurnal', 'sub' => 'Auto & Manual Journal', 'icon' => 'fas fa-book', 'color' => 'text-teal'],
                    ['title' => '3. Buku Besar (Ledger)', 'sub' => 'Audit Per Akun Akuntansi', 'icon' => 'fas fa-list-check', 'color' => 'text-warning'],
                    ['title' => '4. 5 Laporan Keuangan', 'sub' => 'Laba Rugi, Neraca, Cashflow', 'icon' => 'fas fa-file-pdf', 'color' => 'text-success']
                ]),
                'summary'      => 'Siklus akuntansi standar PSAK: setup saldo awal bagan akun (COA), pencatatan jurnal umum berimbang (Debit == Kredit), audit buku besar, serta penyajian laporan keuangan resmi berkop klinik.',
                'content'      => "1. **Setup Saldo Awal:** Akses *Akuntansi > Saldo Awal*. Masukkan saldo historis akun. Sistem memiliki fitur *Smart Auto-Balancing* ke Modal Awal Disetor (3-101).\n2. **Jurnal Umum:** Seluruh transaksi kasir, payroll, fee dokter, dan penyusutan aset otomatis tercatat di *Akuntansi > Jurnal Umum*.\n3. **Laporan Keuangan:** Akses *Akuntansi > Laporan Keuangan* untuk mencetak 5 laporan resmi: Laba Rugi Komprehensif, Neraca Keuangan, Arus Kas, Omset Unit Bisnis, dan Buku Besar.",
                'order_num'    => 5,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Rekapitulasi & Settlement Jasa Medis Dokter',
                'target_role'  => 'Bagian Keuangan & Direksi',
                'badge_color'  => 'purple',
                'icon'         => 'fas fa-hand-holding-dollar',
                'flow_steps'   => json_encode([
                    ['title' => '1. Rekap Fee per Dokter', 'sub' => 'Filter Periode & Dokter', 'icon' => 'fas fa-calculator', 'color' => 'text-primary'],
                    ['title' => '2. Verifikasi Tindakan', 'sub' => 'Cek Pasien & Tindakan', 'icon' => 'fas fa-clipboard-check', 'color' => 'text-teal'],
                    ['title' => '3. Proses Settlement', 'sub' => 'Pencairan Kas/Bank', 'icon' => 'fas fa-money-bill-transfer', 'color' => 'text-warning'],
                    ['title' => '4. Auto-Jurnal Beban', 'sub' => 'Jurnal Jasa Medis (5-103)', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Manajemen komisi dan bagi hasil dokter poliklinik/tindakan berdasarkan tarif layanan medis, rekapitulasi periode kerja, hingga pencairan kas dan pembukuan beban akuntansi.',
                'content'      => "1. **Rekapitulasi:** Buka *Keuangan > Fee Jasa Medis Dokter*. Tentukan filter dokter dan rentang tanggal pelayanan.\n2. **Rincian Fee:** Sistem menghitung otomatis hak fee dokter per pasien berdasarkan tarif layanan master klinik.\n3. **Pencairan Fee:** Klik *Proses Settlement & Bayar Fee*. Sistem membukukan pencairan kas dan mencatat Jurnal Beban Jasa Medis Dokter (5-103 vs 1-101/1-102).\n4. **Bukti Bayar:** Cetak slip rincian pembayaran honor jasa medis dokter sebagai bukti sah.",
                'order_num'    => 6,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Pengadaan Barang (PO), Approval & Penerimaan Gudang (GRN)',
                'target_role'  => 'Bagian Pengadaan, Kepala Klinik & Gudang',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-truck-ramp-box',
                'flow_steps'   => json_encode([
                    ['title' => '1. Buat Pengajuan PO', 'sub' => 'Pilih Vendor & Item Obat', 'icon' => 'fas fa-file-pen', 'color' => 'text-primary'],
                    ['title' => '2. Verifikasi Approval', 'sub' => 'Persetujuan Kepala Klinik', 'icon' => 'fas fa-stamp', 'color' => 'text-teal'],
                    ['title' => '3. Penerimaan Gudang (GRN)', 'sub' => 'Cek Fisik & No. Batch', 'icon' => 'fas fa-box-open', 'color' => 'text-warning'],
                    ['title' => '4. Stok & Hutang Update', 'sub' => 'Buku Hutang & Aging AP', 'icon' => 'fas fa-check-double', 'color' => 'text-success']
                ]),
                'summary'      => 'Siklus pengadaan logistik dan obat farmasi dari permohonan pesanan pembelian (PO), otorisasi bertingkat, penerimaan barang gudang (GRN), hingga mutasi stok dan kartu hutang dagang supplier.',
                'content'      => "1. **Buat PO:** Buka *Pengadaan > Purchase Order (PO)*, pilih supplier dan masukkan item pesanan beserta harga beli.\n2. **Approval:** Kepala Bagian Keuangan / Kepala Klinik menyetujui pengadaan melalui menu *Pengadaan > Approval Pengadaan*.\n3. **Penerimaan Barang:** Bagian gudang memproses penerimaan melalui *Pengadaan > Penerimaan Barang (GRN)*, mencatat nomor batch dan tanggal kadaluarsa.\n4. **Stok & Hutang:** Stok otomatis bertambah di buku kartu stok dan tercatat pada *Aging Schedule Hutang Usaha*.",
                'order_num'    => 7,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur POS Resto Sehat & Kitchen Display System (KDS)',
                'target_role'  => 'Kasir Resto & Koki Dapur',
                'badge_color'  => 'danger',
                'icon'         => 'fas fa-utensils',
                'flow_steps'   => json_encode([
                    ['title' => '1. Input Pesanan Menu', 'sub' => 'POS Touchscreen Resto', 'icon' => 'fas fa-tablet-screen-button', 'color' => 'text-primary'],
                    ['title' => '2. Masuk Layar Dapur (KDS)', 'sub' => 'Status: Sedang Dimasak', 'icon' => 'fas fa-fire-burner', 'color' => 'text-danger'],
                    ['title' => '3. Makanan Siap Saji', 'sub' => 'Update Status KDS Ready', 'icon' => 'fas fa-bell-concierge', 'color' => 'text-warning'],
                    ['title' => '4. Pelunasan Kasir POS', 'sub' => 'Cetak Struk & Auto-Jurnal', 'icon' => 'fas fa-receipt', 'color' => 'text-success']
                ]),
                'summary'      => 'Operasional restoran nutrisi sehat klinik: pemesanan menu makanan/minuman sehat via POS, sinkronisasi pesanan ke monitor dapur koki (KDS), dan pelunasan kasir terhubung akuntansi.',
                'content'      => "1. **Kasir POS Resto:** Akses menu *Resto > Kasir & POS*. Pilih menu makanan sehat dan input nama pemesan / nomor antrean.\n2. **Kitchen Display System (KDS):** Koki dapur melihat pesanan baru di menu *Resto > Layar Dapur (KDS)* secara *real-time*.\n3. **Proses Masak:** Koki mengubah status menjadi *Memasak* lalu *Siap Saji* setelah hidangan selesai.\n4. **Struk Pembayaran:** Kasir menyelesaikan pembayaran dan mencetak struk pemesanan resto sehat.",
                'order_num'    => 8,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Alur Presensi GPS, Pengajuan Cuti & Payroll Gaji Staf HRD',
                'target_role'  => 'HRD & Seluruh Pegawai',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-users-gear',
                'flow_steps'   => json_encode([
                    ['title' => '1. Presensi Masuk/Pulang', 'sub' => 'Geolokasi GPS & Shift', 'icon' => 'fas fa-location-dot', 'color' => 'text-primary'],
                    ['title' => '2. Manajemen Cuti Pegawai', 'sub' => 'Pengajuan & Otorisasi', 'icon' => 'fas fa-calendar-day', 'color' => 'text-teal'],
                    ['title' => '3. Hitung Payroll Bulanan', 'sub' => 'Gaji Pokok & Tunjangan', 'icon' => 'fas fa-calculator', 'color' => 'text-warning'],
                    ['title' => '4. Cetak Slip & Auto-Jurnal', 'sub' => 'Beban Gaji Staf (5-101)', 'icon' => 'fas fa-envelope-open-text', 'color' => 'text-success']
                ]),
                'summary'      => 'Manajemen SDM terintegrasi: presensi kehadiran harian berbasis geolokasi GPS, pengajuan dan persetujuan cuti kerja, hingga otomasi kalkulasi slip gaji payroll dan pembukuan jurnal beban gaji.',
                'content'      => "1. **Presensi Mandiri:** Pegawai melakukan *Check-in* dan *Check-out* kehadiran harian di menu *HRD > Presensi Kehadiran*.\n2. **Pengajuan Cuti:** Pegawai mengajukan izin cuti kerja via *HRD > Pengajuan Cuti*, yang diteruskan ke atasan untuk disetujui.\n3. **Generate Payroll:** Setiap akhir bulan, HRD membuka *HRD > Payroll Gaji* untuk menghitung gaji pokok, tunjangan kehadiran, dan potongan BPJS.\n4. **Pencairan & Slip Gaji:** Klik *Bayar Payroll* untuk membukukan Jurnal Beban Gaji Staf (5-101) dan mencetak slip gaji resmi pegawai.",
                'order_num'    => 9,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Interoperabilitas RESTful API & SATUSEHAT Kemenkes (HL7 FHIR R4)',
                'target_role'  => 'IT & Administrator Sistem',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-network-wired',
                'flow_steps'   => json_encode([
                    ['title' => '1. Header X-API-KEY', 'sub' => 'Autentikasi Token', 'icon' => 'fas fa-key', 'color' => 'text-primary'],
                    ['title' => '2. Request Endpoint API', 'sub' => 'GET /api/v1/...', 'icon' => 'fas fa-link', 'color' => 'text-teal'],
                    ['title' => '3. Standar HL7 FHIR R4', 'sub' => 'Encounter Resource', 'icon' => 'fas fa-code', 'color' => 'text-warning'],
                    ['title' => '4. Respon JSON Standar', 'sub' => 'Status, Code, Data', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Pemanfaatan arsitektur RESTful API modern untuk integrasi Display TV Antrean, Aplikasi Mobile Pasien, dan Bridge Interoperabilitas SATUSEHAT Kemenkes RI berstandar HL7 FHIR R4.',
                'content'      => "1. **Keamanan API:** Seluruh request ke `/api/v1/*` wajib menyertakan header `X-API-KEY` atau `Authorization: Bearer <token>`.\n2. **Display Antrean:** `GET /api/v1/antrean` menyajikan data antrean poliklinik yang sedang dipanggil untuk monitor TV ruang tunggu.\n3. **Katalog Obat:** `GET /api/v1/medicines` menyajikan daftar obat aktif dan stok terkini secara *real-time*.\n4. **SATUSEHAT FHIR:** `GET /api/v1/satusehat/encounter/{visit_id}` menghasilkan payload resmi **HL7 FHIR R4 Encounter Resource** yang siap ditransmisikan ke server Kemenkes RI.",
                'order_num'    => 10,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Penjadwalan Otomatis (Spark Scheduled Cron Tasks) & Disaster Recovery',
                'target_role'  => 'IT & Administrator Sistem',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-clock',
                'flow_steps'   => json_encode([
                    ['title' => '1. Task Scheduler / Cron', 'sub' => 'Jadwalkan Eksekusi', 'icon' => 'fas fa-calendar-check', 'color' => 'text-teal'],
                    ['title' => '2. Eksekusi Spark Cron', 'sub' => 'php spark cron:...', 'icon' => 'fas fa-terminal', 'color' => 'text-primary'],
                    ['title' => '3. Latar Belakang', 'sub' => 'Scan/Depr/Closing/Backup', 'icon' => 'fas fa-cogs', 'color' => 'text-warning'],
                    ['title' => '4. Log Audit Otomatis', 'sub' => 'Tercatat di Audit Trail', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan otomasi tugas latar belakang klinik (pemindaian obat kadaluarsa, penyusutan aset bulanan, tutup buku harian, dan backup database otomatis) menggunakan Spark Scheduled Cron.',
                'content'      => "1. **Scan Obat Kadaluarsa:** `php spark cron:check-expired-medicines` memindai batch obat mendekati expired (< 90 hari) setiap pagi.\n2. **Penyusutan Aset Bulanan:** `php spark cron:monthly-depreciation` menghitung dan menjurnal depresiasi aset tetap setiap akhir bulan.\n3. **Tutup Buku Harian:** `php spark cron:daily-closing` merekonsiliasi total omset harian kasir klinik, apotek, dan resto setiap malam.\n4. **Pencadangan Basis Data:** `php spark cron:database-backup` membuat salinan full SQL dump ke folder `writable/backups/`.\n5. **Uji Otomatis:** Jalankan `php spark system:test-all` kapan pun untuk memverifikasi 100% kesehatan seluruh subsistem.",
                'order_num'    => 11,
                'is_published' => 1,
                'created_by'   => 1
            ],

            // =========================================================================
            // 2. ROLE GUIDES (Panduan Langkah per Peran)
            // =========================================================================
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Kerja: Dokter Spesialis & Dokter Umum',
                'target_role'  => 'Dokter',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-user-md',
                'flow_steps'   => null,
                'summary'      => 'Panduan ringkas tugas dokter poliklinik: pemeriksaan SOAP RME, pengisian odontogram gigi, diagnosa ICD-10, pembuatan e-Resep, dan monitoring fee jasa medis.',
                'content'      => "• **Pemeriksaan Pasien:** Akses *Klinik > Rekam Medis (SOAP)*, klik *Periksa Pasien* pada antrean yang masuk.\n• **Odontogram Gigi:** Pada Poli Gigi, isi visual chart 32 gigi dengan kondisi klinis terkait.\n• **e-Resep Medis:** Ketik nama obat dan dosis aturan minum, lalu simpan agar langsung diterima instalasi farmasi.\n• **Honor & Jasa Medis:** Cek rekapitulasi fee pemeriksaan Anda di menu *Keuangan > Fee Jasa Medis Dokter*.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Kerja: Apoteker & Staf Farmasi',
                'target_role'  => 'Apoteker & Farmasi',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-prescription',
                'flow_steps'   => null,
                'summary'      => 'Panduan penyerahan e-resep dokter berbasis FEFO, kasir penjualan obat bebas OTC, pemantauan buku kartu stok, dan stock opname fisik.',
                'content'      => "• **Tebus Resep:** Buka *Apotek > e-Resep Masuk*, pilih nomor batch obat terdekat expired (*FEFO*) dan klik *Serahkan Obat*.\n• **Penjualan Bebas:** Layani pembelian langsung obat bebas melalui menu *Apotek > Penjualan Bebas*.\n• **Audit Kartu Stok:** Pantau mutasi stok masuk (PO) dan keluar (Resep) pada menu *Apotek > Buku Kartu Stok*.\n• **Stock Opname:** Lakukan penyesuaian fisik stok obat bulanan melalui menu *Apotek > Stock Opname*.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Kerja: Kasir Utama & Petugas Billing',
                'target_role'  => 'Kasir & Billing',
                'badge_color'  => 'warning',
                'icon'         => 'fas fa-cash-register',
                'flow_steps'   => null,
                'summary'      => 'Panduan operasional kasir: pelunasan tagihan rawat jalan, apotek & tindakan medis, pembayaran QRIS/Debit/Tunai, dan cetak kwitansi stempel sah.',
                'content'      => "• **Buka Tagihan:** Buka menu *Keuangan > Kasir Utama*, klik tagihan pasien yang berstatus *Open*.\n• **Input Pembayaran:** Masukkan nominal uang yang diterima dan pilih metode pembayaran (Tunai, QRIS Dinamis, Kartu Debit, atau Transfer Bank).\n• **Cetak Kwitansi:** Cetak bukti pembayaran resmi berkop klinik yang dilengkapi stempel sah dan QR barcode verifikasi dokumen.",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Kerja: Staf Akuntansi & Koordinator Keuangan',
                'target_role'  => 'Accounting & Keuangan',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-calculator',
                'flow_steps'   => null,
                'summary'      => 'Panduan pembukuan jurnal akuntansi, audit buku besar, settlement fee dokter, aging schedule piutang/hutang, dan penerbitan 5 laporan keuangan.',
                'content'      => "• **Audit Jurnal:** Pantau keseimbangan jurnal otomatis transaksi kasir, payroll, dan penyusutan di *Akuntansi > Jurnal Umum*.\n• **Buku Besar:** Telusuri mutasi saldo setiap akun COA melalui *Akuntansi > Buku Besar*.\n• **Settlement Fee:** Proses pencairan bagi hasil dokter melalui *Keuangan > Fee Jasa Medis Dokter*.\n• **Laporan Keuangan:** Cetak Laba Rugi, Neraca, dan Arus Kas resmi melalui menu *Akuntansi > Laporan Keuangan*.",
                'order_num'    => 4,
                'is_published' => 1,
                'created_by'   => 1
            ],

            // =========================================================================
            // 3. FAQ & SOLUSI KENDALA (Frequently Asked Questions)
            // =========================================================================
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana jika terjadi ketidakseimbangan saldo jurnal atau transaksi gagal?',
                'target_role'  => 'Semua Pengguna',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-circle-question',
                'flow_steps'   => null,
                'summary'      => 'Solusi jika jurnal akuntansi tidak seimbang atau terjadi anomali transaksi database.',
                'content'      => "Sistem Sawamawa Medical Center telah dilengkapi dengan **ACID Transaction Engine** dan **Error Tracking APM**.\n1. Jika transaksi terputus di tengah jalan, sistem otomatis melakukan *Rollback* sehingga tidak ada data menggantung.\n2. Untuk memverifikasi keseimbangan seluruh jurnal secara instan, jalankan perintah CLI: `php spark audit:transactions`.\n3. Rincian galat teknis dapat dilihat langsung pada menu *Administrasi Sistem > Error Tracker & APM*.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana cara menghubungkan Display Antrean TV ke sistem klinik?',
                'target_role'  => 'IT & Administrator Sistem',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-tv',
                'flow_steps'   => null,
                'summary'      => 'Langkah menghubungkan layar Smart TV / Monitor ruang tunggu ke API antrean real-time.',
                'content'      => "1. Buka browser pada Smart TV / Mini PC display ruang tunggu.\n2. Arahkan URL ke endpoint antrean atau panggil API RESTful: `GET /api/v1/antrean` dengan menyertakan header `X-API-KEY`.\n3. Data nomor antrean yang sedang dipanggil dan dokter yang bertugas akan ter-update secara *real-time* setiap ada pemanggilan baru di poliklinik.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana cara melakukan pencadangan (backup) dan pemulihan basis data?',
                'target_role'  => 'Super Admin & IT',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-database',
                'flow_steps'   => null,
                'summary'      => 'Prosedur backup manual 1-klik dan penjadwalan otomatis pencadangan database.',
                'content'      => "• **Backup Manual 1-Klik:** Buka menu *Administrasi Sistem > Pengaturan Sistem*, lalu klik tombol *Unduh Backup SQL*.\n• **Backup Otomatis CLI:** Jalankan `php spark cron:database-backup` yang otomatis menyimpan file SQL dump ke folder `writable/backups/`.\n• **Pemulihan (Restore):** File backup SQL dapat diimpor langsung melalui phpMyAdmin atau MySQL CLI.",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1
            ],

            // =========================================================================
            // 4. SECURITY & ARCHITECTURE POLICIES
            // =========================================================================
            [
                'category'     => 'security_policy',
                'title'        => 'Kebijakan Keamanan Data Medis, Hak Akses RBAC & Audit Trail',
                'target_role'  => 'Seluruh Pengguna',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-shield-halved',
                'flow_steps'   => null,
                'summary'      => 'Ketentuan keamanan kerahasiaan rekam medis, enkripsi kata sandi Bcrypt, dan pencatatan audit trail otomatis.',
                'content'      => "1. **Kerahasiaan Data Medis (RME):** Data rekam medis pasien dilindungi oleh matriks hak akses RBAC (*Role-Based Access Control*) dan hanya dapat diakses oleh dokter/perawat pemeriksa.\n2. **Enkripsi Kata Sandi:** Seluruh password akun dienkripsi menggunakan algoritma *Bcrypt Hashing*.\n3. **Universal Audit Trail:** Setiap aktivitas penambahan, pengubahan, penghapusan, pembayaran, dan pencetakan dokumen resmi dicatat secara permanen di menu *Audit Trail Logs* (`system/audit`).\n4. **Proteksi API:** Seluruh endpoint publik dilindungi oleh filter autentikasi token (`ApiKeyFilter`) dan proteksi *Rate Limiting*.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1
            ]
        ];

        foreach ($docs as $doc) {
            $existing = $db->table('system_documentations')->where('title', $doc['title'])->get()->getRow();
            if ($existing) {
                $doc['updated_at'] = date('Y-m-d H:i:s');
                $db->table('system_documentations')->where('id', $existing->id)->update($doc);
            } else {
                $doc['created_at'] = date('Y-m-d H:i:s');
                $db->table('system_documentations')->insert($doc);
            }
        }

        echo "Seluruh Dokumentasi Pusat Bantuan & Peta Alur Sistem berhasil diperbarui.\n";
    }
}
