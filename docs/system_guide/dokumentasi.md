# DOKUMENTASI LENGKAP PENGEMBANGAN & SISTEM INFORMASI ERP TERPADU
**SAWAMAWA MEDICAL CENTER & RESTO GIZI**
*Sistem Manajemen Klinik Pratama/Utama, Apotek Terpadu, POS Restoran Sehat, Akuntansi, Keuangan, dan Portal Publik*

---

## 📑 DAFTAR ISI
1. [Gambaran Umum & Arsitektur Sistem](#1-gambaran-umum--arsitektur-sistem)
2. [Standar Desain Antarmuka (Paper White Design System)](#2-standar-desain-antarmuka-paper-white-design-system)
3. [Matriks Peran Pengguna & Hak Akses (RBAC)](#3-matriks-peran-pengguna--hak-akses-rbac)
4. [Struktur Database & Hubungan Entitas (ERD)](#4-struktur-database--hubungan-entitas-erd)
5. [Alur Bisnis Terintegrasi (End-to-End Workflow)](#5-alur-bisnis-terintegrasi-end-to-end-workflow)
6. [Fitur-Fitur Utama Setiap Modul](#6-fitur-fitur-utama-setiap-modul)
   - 6.1 [Portal Publik & Landing Page Pasien](#61-portal-publik--landing-page-pasien)
   - 6.2 [Pelayanan Medis & Rekam Medis Elektronik (RME)](#62-pelayanan-medis--rekam-medis-elektronik-rme)
   - 6.3 [Master Data Referensi & Tarif Bertingkat](#63-master-data-referensi--tarif-bertingkat)
   - 6.4 [Farmasi & Apotek Terpadu (Multi-Batch Tracking)](#64-farmasi--apotek-terpadu-multi-batch-tracking)
   - 6.5 [Unit Bisnis Distributor & Farmasi Grosir (B2B)](#65-unit-bisnis-distributor--farmasi-grosir-b2b)
   - 6.6 [Restoran Sehat & Kitchen Display System (KDS)](#66-restoran-sehat--kitchen-display-system-kds)
   - 6.7 [Kasir Terpadu & Multi-Metode Pembayaran](#67-kasir-terpadu--multi-metode-pembayaran)
   - 6.8 [Keuangan, Kas & Bank](#68-keuangan-kas--bank)
   - 6.9 [Akuntansi, Setup Saldo Awal & Laporan Keuangan](#69-akuntansi-setup-saldo-awal--laporan-keuangan)
   - 6.10 [Pengadaan (Procurement) & Approval Workflow](#610-pengadaan-procurement--approval-workflow)
   - 6.11 [Inventaris Aset & HRD Kepegawaian](#611-inventaris-aset--hrd-kepegawaian)
   - 6.12 [Administrasi Sistem, Audit Trail & Database Management](#612-administrasi-sistem-audit-trail--database-management)
7. [Implementasi DataTables Server-Side Processing](#7-implementasi-datatables-server-side-processing)
8. [Arsitektur Keamanan & Proteksi Data Medis](#8-arsitektur-keamanan--proteksi-data-medis)
9. [Log Rilis Pembaruan Sistem (Changelog & Milestone)](#9-log-rilis-pembaruan-sistem-changelog--milestone)

---

## 1. Gambaran Umum & Arsitektur Sistem

Sistem ERP Sawamawa Medical Center dibangun dengan pendekatan **MVC-S (Model-View-Controller-Service)** di atas platform **CodeIgniter 4 (PHP 8.2+)** dan basis data relasional **MySQL (InnoDB Engine)**. 

### Komponen Arsitektur Utama:
```
┌─────────────────────────────────────────────────────────────────────────┐
│                     USER INTERFACE & PUBLIC PORTAL                      │
│   (Paper White UI, Responsive CSS, DataTables Server-Side, Bootstrap 4) │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ HTTP Request / AJAX JSON
                                     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         CONTROLLER & FILTER LAYER                       │
│    - AuthFilter (Session & RBAC Guard)                                  │
│    - BaseController (Helpers, Security, Shared Context)                 │
│    - Specialized Controllers (Klinik, Apotek, Resto, Keuangan, Acct)    │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ Validated Data & Method Calls
                                     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         SERVICE & ENGINE LAYER                          │
│    - JournalEngine (Auto Double-Entry Accounting Generator)             │
│    - ApprovalEngine (Multi-Level Workflow PO & Kas Keluar)              │
│    - InventoryEngine (FIFO/FEFO Multi-Batch Stock Manager)              │
│    - DataTables Helper (High-Speed SQL Server-Side Handler)             │
│    - AuditTrail (Real-time Event Logging & Security Tracking)           │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ Prepared Statements (ActiveRecord)
                                     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                     RELATIONAL DATABASE LAYER (MySQL)                   │
│   (InnoDB, Foreign Key Constraints, Transactions ACID, UTF8MB4)         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Standar Desain Antarmuka (Paper White Design System)

Sistem secara ketat mengimplementasikan filosofi **Paper White Design System**:
1. **Latar Belakang & Kartu**: Menggunakan latar putih murni (`#ffffff`) dengan pembatas garis tipis tegas (`border: 1px solid #b8b8b8`) tanpa bayangan berat (*no heavy drop shadows*).
2. **Warna Aksen Identitas**: Hijau Sawamawa / Teal Organik (`#0d9f4f` / `#20c997`) yang mencerminkan kesehatan, kebersihan, dan profesionalitas medis.
3. **Tipografi**: Menggunakan font modern *Inter / Roboto* dengan kontras tinggi (`#212529` untuk teks utama dan `#6c757d` untuk teks sekunder).
4. **Tabel Data**: Garis pemisah tegas, header abu-abu lembut (`bg-light`), dan animasi *hover* baris halus.
5. **Responsif & Ringan**: Tidak membebani browser pengguna dengan library pihak ketiga berlebih.

---

## 3. Matriks Peran Pengguna & Hak Akses (RBAC)

Sistem mengamankan seluruh endpoint melalui **Role-Based Access Control (RBAC)** dengan 10 peran utama:

| No | Peran (Role) | Akses Utama Modul | Deskripsi Tanggung Jawab |
|:---:|:---|:---|:---|
| 1 | **Super Admin** | *Seluruh Modul (Full Access)* | Manajemen sistem, hak akses, audit trail, backup, dan seluruh konfigurasi. |
| 2 | **Pendaftaran** | Pendaftaran Pasien, Antrean Poli, Cetak Kartu | Pendaftaran pasien offline/online, booking poli, verifikasi data BPJS/NIK. |
| 3 | **Perawat** | Triage Vital Sign, Antrean Poli | Pemeriksaan tanda vital awal (tekanan darah, suhu, SPO2, keluhan utama). |
| 4 | **Dokter** | RME SOAP, ICD Diagnosa/Prosedur, E-Prescription | Pemeriksaan klinis pasien, diagnosa, tindakan, rujukan lab, resep obat, & diet resto. |
| 5 | **Farmasi / Apoteker** | Dispensing Resep, Stok Obat, Batch Expired, POS OTC | Penyiapan obat racikan/non-racikan, kartu stok obat, dan penjualan bebas. |
| 6 | **Kasir / Billing** | Kasir Klinik Terpadu, Rekap Kasir, Pembayaran Multi | Penerimaan pembayaran tagihan pasien, cetak kuitansi struk, rekap shift. |
| 7 | **Kasir Resto / Waiter** | POS Resto Touchscreen, Manajemen Meja, Open Bill | Pemesanan menu makanan/minuman sehat, pembagian meja, open bill. |
| 8 | **Chef / Dapur** | Kitchen Display System (KDS) | Pemantauan tiket pesanan dapur secara realtime dan update status masak. |
| 9 | **Keuangan & Akuntan** | Kas & Bank, Saldo Awal, Jurnal, Buku Besar, Laporan Keuangan | Manajemen arus kas, posting jurnal, setup saldo awal, dan 5 laporan keuangan. |
| 10 | **Direktur / Manajemen** | Laporan Eksekutif, Cetak Laporan, Approval Procurement | Pemantauan grafik omset, pengesahan laporan keuangan resmi, approval anggaran. |

---

## 4. Struktur Database & Hubungan Entitas (ERD)

Database terdiri dari 35+ tabel relasional dengan integritas kunci asing (*foreign key constraints*):

1. **Autentikasi & Otorisasi**: `users`, `roles`, `permissions`, `role_permissions`, `audit_logs`.
2. **Klinik & Rekam Medis**: `patients`, `polikliniks`, `doctors`, `nurses`, `tindakan`, `patient_visits`, `medical_records`, `odontogram`, `lab_requests`, `lab_results`, `doctor_fee_rules`, `inpatient_rooms`, `inpatient_beds`, `inpatient_admissions`.
3. **Farmasi & Apotek**: `medicines`, `medicine_batches`, `prescriptions`, `prescription_items`, `medicine_movements`, `otc_sales`, `otc_sale_items`.
4. **Restoran & Cafe**: `resto_categories`, `resto_menus`, `resto_tables`, `resto_orders`, `resto_order_items`.
5. **Kasir & Keuangan**: `payment_methods`, `billings`, `billing_items`, `cash_registers`, `cash_movements`.
6. **Akuntansi & Jurnal**: `accounts` (COA), `journal_entries`, `journal_entry_details`.
7. **Pengadaan, Inventaris & HRD**: `suppliers`, `purchase_orders`, `purchase_order_items`, `assets`, `asset_depreciations`, `employees`, `attendances`, `payrolls`.

---

## 5. Alur Bisnis Terintegrasi (End-to-End Workflow)

```
[ PASIEN ]
    │
    ├─► Daftar Online (Landing Page) / Loket Front Office
    │   └─► Diterbitkan No. Rekam Medis (No RM) & Tiket Antrean
    │
    ▼
[ TRIAGE PERAWAT ]
    │
    └─► Input Tanda Vital (Tensi, Nadi, Suhu, RR, SPO2, Keluhan)
    │
    ▼
[ DOKTER POLIKLINIK (RME SOAP) ]
    │
    ├─► Anamnesis (S) & Pemeriksaan Fisik (O)
    ├─► Penetapan Diagnosa ICD-10 & Prosedur ICD-9-CM (A)
    ├─► Tindakan Medis & Odontogram Gigi (P)
    ├─► E-Prescribing (Resep Obat Digital ke Apotek)
    ├─► Rujukan Diet Pasien (Resep Gizi Digital ke Resto)
    └─► Permintaan Laboratorium Patologi (Status Pending)
    │
    ├───► [ APOTEK ] : Dispensing Resep (FIFO/FEFO Multi-Batch)
    ├───► [ RESTO ]  : KDS Dapur Resto Menyiapkan Menu Diet
    └───► [ LAB ]    : Input Hasil Uji Laboratorium
    │
    ▼
[ KASIR UTAMA (BILLING TERPADU) ]
    │
    ├─► Tagihan Otomatis Terkalkulasi (Tindakan + Obat + Menu Resto + Lab)
    ├─► Pembayaran Multipayment (Tunai, QRIS, Bank Transfer, EDC, BPJS/Asuransi, Piutang)
    ├─► Cetak Kuitansi Resmi / Struk Thermal Kasir
    │
    ▼ (Auto-Trigger Journal Engine)
[ AKUNTANSI & KEUANGAN ]
    │
    ├─► Otomatis Membukukan Jurnal Umum Double-Entry Seimbang
    ├─► Mutasi Saldo Kas/Bank Kasir Bertambah Realtime
    ├─► Akumulasi Perhitungan Komisi Jasa Medis Dokter & Nakes
    └─► Tersinkronisasi Otomatis ke 5 Laporan Keuangan (Laba Rugi, Neraca, Arus Kas)
```

---

## 6. Fitur-Fitur Utama Setiap Modul

### 6.1. Portal Publik & Landing Page Pasien
* **Profil Klinik Modern**: Menampilkan dokter spesialis, jadwal poliklinik, layanan unggulan apotek & resto sehat, testimoni pasien, dan lokasi Google Maps.
* **Pendaftaran Pasien Baru Online**: Formulir registrasi terlindungi CSRF dan verifikasi NIK. Pasien baru otomatis mendapatkan Nomor Rekam Medis (No RM).
* **Booking Kunjungan Pasien Lama**: Reservasi berobat cepat dengan validasi ganda No RM / NIK dan Tanggal Lahir untuk perlindungan privasi.

### 6.2. Pelayanan Medis & Rekam Medis Elektronik (RME)
* **Pendaftaran & Antrean Multi-Poli**: Penomoran antrean otomatis per poli dengan layar TV Display antrean bersuara / visual modern.
* **RME Berstandar SOAP**: Pencatatan Subjektif, Objektif, Asesmen, dan Plan yang komprehensif.
* **Integrasi ICD-10 & ICD-9-CM**: Pencarian kode diagnosa penyakit dan tindakan medis internasional secara server-side.
* **Penunjang Medis**: Modul Laboratorium Patologi Klinis dan Dental Odontogram Gigi interaktif.
* **Manajemen Rawat Inap**: Pengaturan kamar, bed ketersediaan pasien, dan rekam medis rawat inap.

### 6.3. Master Data Referensi & Tarif Bertingkat
* **Layanan Tindakan Hierarkis (Parent-Child)**: Pengelompokan tarif ke dalam kategori induk (Administrasi, Rawat Jalan, Tindakan Khusus, Gigi, Lab, dll) yang otomatis membentuk *Optgroup Dropdown* rapi di formulir pendaftaran.
* **Aturan Komisi Jasa Medis**: Pembagian persentase fee dokter, klinik, dan perawat terhitung otomatis saat transaksi dibayar.
* **Master Metode Pembayaran**: Manajemen channel pembayaran (Tunai, QRIS, BCA, Mandiri, BRI, EDC, BPJS, Asuransi).

### 6.4. Farmasi & Apotek Terpadu (Multi-Batch Tracking)
* **Katalog Obat Server-Side**: Memuat ratusan hingga ribuan master obat secara instan.
* **Tracking Multi-Batch & Expired Date**: Penelusuran batch penerimaan obat, stok fisik, dan tanggal kedaluwarsa dengan sistem FIFO/FEFO.
* **Peringatan Stok Kritis**: Notifikasi badge otomatis jika stok berada di bawah batas minimum (*reorder point*).
* **Dispensing Resep & Penjualan Bebas (OTC)**: Pemrosesan resep elektronik dokter dan penjualan obat bebas tanpa resep.

### 6.5. Unit Bisnis Distributor & Farmasi Grosir (B2B)
* **Dashboard Analitik Distributor**: KPI omset harian/bulanan, piutang aktif, peringatan piutang jatuh tempo (*overdue*), dan valuasi stok gudang B2B.
* **Kasir & Faktur Grosir B2B (`INV-DIST-YYYYMMDD-XXXX`)**:
  * Transaksi penjualan partai besar/grosir untuk apotek mitra, klinik rekanan, RS, dan toko obat.
  * Pengecekan limit kredit dan sisa plafon pelanggan B2B secara real-time.
  * Dukungan metode pembayaran tunai/transfer atau kredit berjangka (Termin TOP 7, 14, 30, 60 hari).
  * Cetak Faktur Penjualan Grosir resmi dan Surat Jalan / Delivery Order (DO) ber-Kop Surat.
* **Master Pelanggan Grosir**: Pencatatan legalitas izin apotek/SIA/SIPA/NPWP, limit kredit, dan kontak penanggung jawab.
* **Stok & Batch Distributor**: Manajemen katalog obat distributor, penetapan harga grosir vs harga modal, dan kartu stok mutasi.
* **Transfer Stok Antar Gudang (`TRF-DIST-YYYYMMDD-XXXX`)**: Mutasi transfer keluar/masuk dari Gudang Induk Logistik ke Unit Distributor.
* **Manajemen Piutang Usaha & Pembayaran (`PAY-DIST-YYYYMMDD-XXXX`)**: Klasifikasi *Aging Schedule* (0-30, 31-60, >60 hari), pembayaran bertahap/lunas, dan cetak bukti kuitansi bayar.
* **Retur Penjualan Grosir (`RET-DIST-YYYYMMDD-XXXX`)**: Penanganan barang retur, pemulihan stok obat, penyesuaian piutang, dan auto-reversing jurnal HPP.
* **Laporan Laba Rugi Mandiri Unit Distributor**: Laporan P&L khusus unit bisnis B2B (Penjualan 4-104, Retur 4-304, HPP 5-104).

### 6.6. Restoran Sehat & Kitchen Display System (KDS)
* **Touchscreen POS Kasir**: Desain kasir layar sentuh dengan filter kategori menu, open table bill, dan split payment.
* **Kitchen Display System (KDS)**: Tampilan layar monitor dapur untuk koki/chef dengan status pesanan realtime (*Menunggu -> Dimasak -> Siap Saji*).
* **Resep Diet Pasien**: Dokter spesialis gizi dapat mengirimkan rujukan paket makanan sehat langsung ke sistem kasir/dapur resto.

### 6.7. Kasir Terpadu & Multi-Metode Pembayaran
* **Kalkulasi Tagihan Terpusat**: Menggabungkan seluruh tagihan medis, tindakan, resep obat apotek, dan pesanan resto dalam satu invoice.
* **Multi-Payment**: Mendukung kombinasi pembayaran (misal: sebagian BPJS/Asuransi, sisanya Tunai/QRIS).
* **Cetak Struk & Kuitansi**: Format cetak struk thermal 58mm/80mm dan kuitansi resmi ukuran A4/A5.

### 6.8. Keuangan, Kas & Bank
* **Manajemen Kas & Bank**: Pencatatan kas kecil kasir (*cash float*), rekening bank operasional, kas masuk, kas keluar, dan transfer antar-rekening.
* **Rekap Shift Kasir**: Rekonsiliasi fisik kas kasir saat pergantian shift atau tutup buku harian.
* **Monitoring Piutang & Utang**: Pelacakan klaim piutang BPJS/Asuransi serta sisa hutang ke supplier pengadaan obat.

### 6.9. Akuntansi, Setup Saldo Awal & Laporan Keuangan
* **Setup Saldo Awal (Smart Auto-Balancing)**:
  * Wizard penetapan saldo awal kas kasir, bank, persediaan apotek & resto, aset medis, hutang usaha, dan ekuitas.
  * **Auto-Balancing Cerdas**: Selisih aktiva dan pasiva otomatis diseimbangkan ke akun **Modal Awal Disetor (3-101)** sehingga neraca selalu 100% *Balance*.
  * Otomatis membukukan Jurnal Saldo Awal (`JV-SALDOAWAL-YYYYMMDD`) dan menyinkronkan saldo kasir `cash_registers`.
* **5 Laporan Keuangan Komprehensif**:
  1. *Laporan Laba Rugi Komprehensif (Income Statement)*: Pendapatan klinik, apotek retail, distributor grosir, resto, HPP bahan baku, beban operasional, payroll nakes, hingga Laba/Rugi Bersih berjalan.
  2. *Laporan Neraca Posisi Keuangan (Balance Sheet)*: Sisi Aktiva vs Pasiva dengan indikator status *Balanced Badge*.
  3. *Laporan Arus Kas (Cash Flow Statement)*: Arus kas masuk/keluar dari aktivitas operasional.
  4. *Rekapitulasi Kontribusi Omset Unit Bisnis*: Grafik dan ringkasan persentase omset Poli vs Apotek vs Distributor vs Resto vs Lab.
  5. *Buku Besar Kronologis (General Ledger)*: Melacak seluruh mutasi debit/kredit per rekening COA.
* **Format Cetak Laporan Keuangan Resmi**: Format berstandar audit dengan Kop Surat Resmi Klinik, Periode Laporan, dan Kolom Pengesahan Tanda Tangan Direktur & Kepala Keuangan.

### 6.10. Pengadaan (Procurement) & Approval Workflow
* **Purchase Order (PO)**: Pengajuan pengadaan obat ke supplier farmasi.
* **Multi-Level Approval**: Alur persetujuan bertingkat (*Diajukan -> Diverifikasi Kepala Bagian -> Disetujui Direktur*).

### 6.11. Inventaris Aset & HRD Kepegawaian
* **Inventaris & Aset**: Pencatatan aset medis dan non-medis beserta perhitungan depresiasi penyusutan bulanan otomatis ke jurnal akuntansi.
* **Pegawai & Payroll**: Data nakes/staf, jadwal shift, presensi kehadiran, dan slip gaji terintegrasi bagi hasil jasa medis dokter.

### 6.12. Administrasi Sistem, Audit Trail & Database Management
* **Audit Trail Logs**: Mencatat seluruh aktivitas user, modul, jenis aksi (LOGIN, CREATE, UPDATE, DELETE), IP address, dan user agent.
* **Backup & Database Management**: Pembuatan SQL dump database sekali klik, pembersihan data sampah, dan optimalisasi tabel MySQL.
* **Menu "Apa yang Baru?" (What's New)**: Halaman timeline pembaruan fitur untuk memudahkan pemantauan versi sistem.

---

## 7. Implementasi DataTables Server-Side Processing

Mengikuti standar arsitektur performa tinggi, sistem menggunakan helper terpusat `datatable_server_side()` ([app/Helpers/setting_helper.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Helpers/setting_helper.php)) pada seluruh tabel berdata besar:

* **Prinsip Kerja**:
  1. Browser hanya meminta subset data (misal 10-15 baris per halaman) menggunakan AJAX `draw`, `start`, `length`, `search`, dan `order`.
  2. Database memproses kueri `LIMIT` dan `OFFSET` dengan *Prepared Statements* aman.
  3. Mengembalikan response JSON standar `{draw, recordsTotal, recordsFiltered, data}` dalam hitungan milidetik.
* **Tabel yang Menggunakan Server-Side**:
  * Pendaftaran Pasien (`#table-patients`)
  * Katalog Master Obat (`#table-medicines`)
  * Master Diagnosa ICD-10 (`#table-icd10`) & Prosedur ICD-9-CM (`#table-icd9`)
  * Audit Trail Log Aktivitas Keamanan (`#table-audit`)
  * Buku Jurnal Umum Akuntansi (`#table-jurnal`)

---

## 8. Arsitektur Keamanan & Proteksi Data Medis

1. **Enkripsi & Hashing Kata Sandi**: Menggunakan algoritma *BCrypt / Argon2* bawaan PHP 8.2+.
2. **Proteksi Serangan Siber**:
   * **CSRF Protection**: Token acak unik pada setiap formulir POST/AJAX.
   * **XSS Defense**: Sanitasi keluaran otomatis menggunakan `esc($var)`.
   * **SQL Injection Prevention**: Seluruh kueri menggunakan Parameter Binding QueryBuilder CodeIgniter 4.
3. **Session & Middleware Isolation**: Setiap akses URL diverifikasi oleh filter otentikasi dan pengecekan hak akses RBAC.

---

## 9. Log Rilis Pembaruan Sistem (Changelog & Milestone)

### 📌 Versi 2.6.0 — Pusat Bantuan Dinamis, Standar Akuntansi Bank Indonesia & Role-Based Dashboard (26 Agustus 2026)
* **Rekam Medis Elektronik (RME) Super Lengkap & Multi-Tab (Berstandar SATUSEHAT & Akreditasi)**:
  * Pada tabel Pendaftaran Pasien, kolom **Nomor Rekam Medis** (misal: `RM-000001`) dan **Nama Pasien** kini berstatus interaktif dan membuka **Modal RME Suite Terpadu** super lengkap dengan 4 tab navigasi:
    1. **Tab 1 — Riwayat Kunjungan & SOAP Terintegrasi**: Kartu rekam medis per kunjungan mencakup no. visit, tanggal, poli/tindakan, dokter DPJP, TTV awal keperawatan, keluhan utama, catatan SOAP (Subjective, Objective, Assessment + ICD-10 & ICD-9-CM, Plan), catatan khusus dokter, tabel e-resep farmasi, serta status lunas kasir/billing.
    2. **Tab 2 — Tren Tanda Vital (TTV & IMT Tracker)**: Tabel komparasi fluktuasi tanda vital lintas waktu (Tekanan Darah, Nadi, Suhu, Pernapasan/RR, BB, TB) disertai kalkulasi otomatis **Indeks Massa Tubuh (IMT/BMI)** dan kategori status gizi (*Kurus, Normal, Overweight, Obesitas*).
    3. **Tab 3 — Riwayat Terapi Obat Farmasi (Kumulatif)**: Rekapitulasi seluruh obat-obatan yang pernah diresepkan kepada pasien, total kuantiti pemakaian, frekuensi pemberian, serta aturan pakai/dosis terakhir.
    4. **Tab 4 — Surat Medis & Hasil Laboratorium**: Riwayat seluruh Surat Keterangan Sakit (SKS), Surat Sehat (SKD), Surat Rujukan (SRP), dan hasil tes laboratorium beserta tombol cetak instan.
  * **Fitur Cetak Ringkasan RME (Medical Summary A4)**: Tombol *Cetak Ringkasan RME (PDF)* ber-KOP resmi klinik dengan tata letak rapi, legalitas tanda tangan DPJP, dan siap digunakan untuk kebutuhan klaim, rujukan, atau evaluasi medis.
* **Aksesibilitas Mandiri Menu Rekam Medis & SOAP**:
  * Menu **Rekam Medis & SOAP** kini tampil secara mandiri di navigasi sidebar (*Pelayanan Klinik*) sehingga dokter, perawat, petugas rekam medis, dan pimpinan dapat membukanya kapan saja tanpa harus melewati antrean hari ini.
  * Terintegrasi penuh dengan hak akses peran (*Role-Based Permission: `clinic.soap`*) yang dapat dicentang/diatur secara fleksibel pada menu Pengaturan Pengguna.
  * Penambahan rute alias resmi `klinik/rekam-medis` yang langsung terhubung ke workspace RME.
* **Koreksi, Edit & Hapus Jurnal Penyesuaian (Journal Correction Suite)**:
  * Penambahan tombol interaktif **Edit (Pensil)** dan **Hapus (Tong Sampah)** pada baris jurnal Manual / Penyesuaian di menu Buku Jurnal Umum.
  * Fitur *Auto-Balance Reversal*: Saat jurnal diedit atau dihapus, saldo akun COA yang bersangkutan otomatis dipulihkan/disesuaikan secara presisi.
  * Modal edit interaktif untuk memperbarui tanggal, uraian, akun debet/kredit, dan nominal transaksi.
  * Perlindungan status kunci (🔒 *Otomatis Sistem*) untuk menjaga integritas transaksi kasir, apotek, dan billing pasien.
* **Manajemen & Reset Saldo Awal Sistem**:
  * Penyempurnaan mekanisme pembukuan saldo awal dengan *auto-clean* jurnal lama saat dilakukan koreksi atau perubahan nominal, sehingga tidak terjadi akumulasi/penumpukan jurnal residu.
  * Dukungan penuh pengosongan/penghapusan nominal (mengganti angka menjadi `0` atau mengosongkan kolom input).
  * Penambahan tombol **"Bersihkan Input ke 0"** (reset form instan) dan tombol **"Hapus & Reset Semua Saldo ke 0"** (membersihkan seluruh saldo akun COA & jurnal pembukuan awal di database).
* **Pusat Bantuan & Dokumentasi Mandiri (Database-Driven)**:
  * Modul Dokumentasi & Alur Sistem dinamis berbasis basis data (`system_documentations`) dengan editor CRUD mandiri bagi pimpinan/staf untuk menambah dan mengedit alur sistem secara langsung dari web UI.
  * Fitur *Visual Step-by-Step Flowchart Generator* otomatis berikon, pengelompokan panduan per peran staf, FAQ interaktif, kotak pencarian langsung, dan ekspor cetak PDF manual A4.
* **Buku Jurnal Umum & Buku Besar Berstandar BI**:
  * Penambahan kolom **Sisa Saldo Akun (Running Balance)** pada DataTables Buku Jurnal Umum.
  * Modul **Jurnal Mutasi per Akun (Buku Pembantu)** dengan posisi baris pertama **Saldo Awal Dinamis** yang secara otomatis mengakumulasi saldo dari bulan-bulan sebelumnya.
  * Perhitungan saldo berjalan naik/turun baris per baris (*Debit-Normal* untuk Aset/Beban, *Kredit-Normal* untuk Hutang/Modal/Pendapatan).
  * Panel filter multi-dimensi (Modul Sumber Transaksi & Rentang Tanggal).
* **Laporan Keuangan Standar Bank Indonesia, OJK & SAK EMKM**:
  * Penyajian formal **Laporan Laba Rugi Komprehensif**, **Laporan Posisi Keuangan / Neraca**, **Laporan Arus Kas (Metode Langsung)**, dan **Rekapitulasi Omset Lini Usaha**.
  * Lembar pengesahan 3 pihak standar audit perbankan (*Staf Akuntansi, Internal Auditor, Direktur Utama Klinik*) dan badge verifikasi *100% BALANCE*.
* **Dashboard Eksekutif & Operasional Berbasis Peran (RBAC Guard)**:
  * Pembatasan akses data finansial sensitif (hanya dapat dilihat oleh *Super Admin, Direktur, Manajemen, Keuangan, & IT*).
  * Dashboard operasional khusus staf medis, farmasi, kasir, resto, dan HRD dengan metrik harian, grafik tren kunjungan 7 hari, dan *Quick Action Command Center*.
* **Resolusi Routing & Bebas 404**:
  * Penyesuaian seluruh tautan pintasan dashboard (Kas & Bank, Buku Piutang, Buku Hutang, RME Dokter, dan Laporan Resto) serta pendaftaran rute alias aman di `app/Config/Routes.php`.
* **Keamanan & Skema Database**:
  * Perbaikan nullability dan default value pada kolom `record_id` & `table_name` di tabel `audit_logs` melalui migrasi `FixAuditLogsColumns`.
* **Pembersihan Data Siap Produksi (*Clean Production State*)**:
  * Pengosongan 42 tabel transaksi (kunjungan, antrean, resep, billing, order resto, jurnal, log testing) dengan menjaga 100% keutuhan seluruh master referensi obat, produk resto, tarif tindakan medis, ICD-10/9, lab, dokter, poli, nakes, akun COA, dan RBAC.
* **Penerbitan Proposal Penawaran & Ekspor Microsoft Word**:
  * Dokumen resmi penawaran sistem Rp 20.000.000,- ([`proposal.md`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/proposal.md)) beserta berkas Microsoft Word OpenXML ([`proposal_penawaran_sawamawa.docx`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/proposal_penawaran_sawamawa.docx)) dan Word HTML ([`proposal_penawaran_sawamawa.doc`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/proposal_penawaran_sawamawa.doc)).

### 📌 Versi 2.8.0 — Arsitektur Terpadu: API-Ready, Event-Driven, CI4 Native Models & Cron (27 Agustus 2026)
* **RESTful API-Ready Subsystem (`/api/v1`)**: Proteksi `ApiKeyFilter` (`X-API-KEY` / Bearer Token) & `ResponseTrait` untuk endpoint Display TV Antrean Real-time, Registrasi Mandiri Pasien, Katalog & Stok Obat, serta Bridge HL7 FHIR R4 Encounter SATUSEHAT Kemenkes RI.
* **Decoupled Event-Driven Hooks (`Config\Events`)**: Pemicu otomatis lintas modul (`patient.registered` -> Kartu Digital, `billing.paid` -> Auto-Jurnal & Komisi Dokter, `pharmacy.dispensed` -> Audit e-Resep, `pharmacy.stock_low` -> Alert Pengadaan, `system.error_logged` -> APM Tracker).
* **33 CodeIgniter 4 Native Models**: Standardisasi layer data resmi dengan `$allowedFields` (Mass-Assignment Protection), `$validationRules` & `$validationMessages` terpusat, `$useTimestamps` otomatis, dan Entity Callbacks (Auto Bcrypt Password Hashing).
* **Universal Audit Trail Recording**: Pelacakan otomatis seluruh aksi pengguna (CREATE, UPDATE, DELETE, PAYMENT, APPROVE, DISPENSE, EXPORT, LOGIN, LOGOUT) dengan filter interaktif dan badge visual multi-warna.
* **Spark Scheduled Cron Jobs**: Perintah otomatis siap pakai untuk Windows Task Scheduler / Linux Cron (`cron:check-expired-medicines`, `cron:monthly-depreciation`, `cron:daily-closing`, `cron:database-backup`).
* **Automated Unified Testing Suite (`spark system:test-all`)**: Suite pengujian otomatis terpadu yang memverifikasi 100% kesehatan 33 Models, Event Listeners, API Endpoints, Scheduled Cron, dan Transaksi Database ACID.

### 📌 Versi 2.7.0 — Enterprise Advanced Modules & Clinical Excellence (27 Agustus 2026)
* **Odontogram Interaktif FDI 32 Gigi**: Visual chart 32 gigi dengan palet diagnosis medis real-time di RME Poli Gigi (Normal, Caries, Filling, Missing, Crown, Radix, Extract).
* **Triase Cepat IGD/UGD (Skala ATS 1-5)**: Identifikasi tingkat kegawatan klinis pasien secara instan dengan indikator visual dan integrasi rekam medis.
* **Buku Kartu Stok Digital (Stock Card Ledger)**: Audit histori mutasi barang masuk (PO), keluar (e-Resep Medis & Penjualan Bebas), dan penyesuaian opname dengan saldo berjalan (Running Balance).
* **Buku Pembantu & Aging Schedule**: Analisis umur piutang pasien/asuransi dan hutang vendor supplier (0-30, 31-60, 61-90, >90 hari).
* **Rekapitulasi & Settlement Jasa Medis Dokter**: Manajemen bagi hasil dokter poliklinik terotomatisasi langsung ke Jurnal Akuntansi Beban Jasa Medis vs Kas/Bank.
* **Error Tracking & APM Engine + High-Concurrency Scaling**: Deteksi galat otomatis terdeduplikasi (Hash 64-bit), Profiling kapasitas 44+ tabel database, dan 1-Click Optimize & Cache Clearing.

### 📌 Versi 2.5.0 — Performa & DataTables Server-Side (22 Agustus 2026)
* Implementasi DataTables Server-Side Processing pada Pendaftaran Pasien, Apotek, ICD-10/ICD-9, Audit Trail, dan Jurnal Umum.
* Helper `datatable_server_side()` terintegrasi dengan closure row formatter.
* Penambahan menu interaktif **"Apa yang Baru?" (What's New)** berbasis basis data `system_updates` dengan fitur CRUD lengkap dan deteksi versi otomatis (`clinic_latest_version()`).

### 📌 Versi 2.4.0 — Finishing Akuntansi & Saldo Awal (21 Agustus 2026)
* Setup Saldo Awal Sistem dengan *Smart Auto-Balancing* ke Modal Awal Disetor (3-101).
* Penyempurnaan 5 Laporan Keuangan Terpadu (Laba Rugi, Neraca, Arus Kas, Omset Unit Bisnis, Buku Besar).
* Format Lembar Cetak Laporan Keuangan Resmi Berkop Surat Klinik.

### 📌 Versi 2.3.0 — Master Klinik & Tindakan Bertingkat (20 Agustus 2026)
* Struktur tarif dan tindakan medis bertingkat (*Parent-Child Categories*).
* Pengelompokan tindakan berbasis *Optgroup Dropdown* pada formulir pendaftaran.
* Aturan bagi hasil jasa medis dokter dan perawat terotomatisasi.

### 📌 Versi 2.2.0 — Public Portal & Registrasi Pasien Online (19 Agustus 2026)
* Landing Page profil klinik dan reservasi pasien online berkeamanan verifikasi ganda NIK/No RM.

### 📌 Versi 2.1.0 — Resto Sehat POS, Apotek Multi-Batch & Penunjang (18 Agustus 2026)
* Touchscreen POS Resto, Kitchen Display System (KDS), dan integrasi rujukan diet gizi dokter.
* Multi-Batch tracking obat, expired date monitoring, resep elektronik, dan modul Laboratorium & Odontogram Gigi.

### 📌 Versi 2.0.0 — Fondasi ERP Terpadu & Keamanan Sistem (15 Agustus 2026)
* Arsitektur MVC-S, RBAC 10 peran, Journal Engine, Approval Engine, dan Audit Trail Logging.

---
*Dokumen ini dirancang sebagai acuan teknis resmi arsitektur dan operasional sistem Sawamawa Medical Center & Resto Gizi.*
