---
trigger: always_on
---

# 📋 DOKUMENTASI SISTEM ERP GITRIA FARMA - SAWAMAWA MEDICAL CENTER

## 📌 Ringkasan Eksekutif

**Sawamawa Medical Center ERP** adalah sistem informasi manajemen klinik terintegrasi penuh (*end-to-end*) yang mendigitalisasi seluruh proses operasional fasilitas kesehatan tingkat pertama (FKTP), unit apotek retail, hingga unit bisnis mandiri **Distributor & PBF Farmasi Grosir (B2B)**. Sistem ini menggabungkan 12 modul utama yang saling terhubung otomatis untuk mendukung operasi klinik, apotek, distributor/grosir, restoran kesehatan, akuntansi, dan manajemen SDM.

### Keunggulan Utama:
- ✅ **Klinik Digital Penuh** (Pendaftaran → Triase → SOAP → e-Resep → Lab → Billing → Penyerahan Obat)
- ✅ **Farmasi Multi-Gudang & Multi-Batch** (Gudang Induk + Depo Apotek + Depo Rawat Inap + Mutasi Transfer Otomatis)
- ✅ **Distributor & Farmasi Grosir B2B** (Faktur B2B, Plafon Kredit, Termin TOP, Surat Jalan, Retur, Laba Rugi Grosir Mandiri)
- ✅ **Akuntansi Double-Entry** (Chart of Accounts → Jurnal Otomatis `JournalEngine` → Laporan Keuangan 5 Pilar)
- ✅ **Real-Time LiveSync** (Zero-Reload JSON Polling untuk Antrean, Resep, Kasir, Dapur, Lab)
- ✅ **Voice Calling System** (Text-to-Speech Anti-Collision dengan Web Speech API)
- ✅ **Multi-Display** (Display TV Antrean Klinik + Kiosk Mandiri APM + Kitchen Display System)
- ✅ **RBAC Granular** (16 Role + 75+ Permission + Audit Trail Komprehensif)
- ✅ **SATUSEHAT Ready** (Endpoint FHIR R4 untuk Integrasi Kemenkes RI)

---

## 🏗️ TEKNOLOGI & STACK

### Backend
| Komponen | Teknologi | Versi |
|---|---|---|
| Framework | CodeIgniter 4 | 4.7+ |
| Bahasa Pemrograman | PHP | 8.1 / 8.2 |
| Database | MySQL | 8.0 (InnoDB Engine) |
| Web Server | Apache | 2.4.x (XAMPP) |
| Package Manager | Composer | 2.x |

### Frontend
| Komponen | Teknologi | Versi |
|---|---|---|
| Admin Template | AdminLTE | 3.2.0 |
| CSS Framework | Bootstrap | 4.6.x |
| JavaScript | jQuery | 3.6.4 |
| Tabel Interaktif | DataTables | 1.10.21 |
| Dropdown | Select2 | 4.0.13 |
| Grafik | Chart.js | Latest |
| Ikon | Font Awesome | 6.4.0 |
| Text-to-Speech | Web Speech API | HTML5 Native |

---

## 📊 STRUKTUR DATABASE (111 Tabel Terintegrasi)

### 🏥 Modul Klinik & Rekam Medis (16 tabel)
`patients`, `patient_visits`, `medical_records`, `triage_records`, `queue_numbers`, `queue_call_events`, `lab_results`, `lab_tests`, `medical_letters`, `medical_informed_consents`, `medical_photos`, `odontograms`, `polyclinics`, `doctors`, `doctor_schedules`, `nurses`

### 💊 Modul Farmasi & Apotek (15 tabel)
`medicines`, `medicine_batches`, `medicine_categories`, `prescriptions`, `prescription_details`, `pharmacy_sales`, `pharmacy_sale_details`, `stock_movements`, `stock_opnames`, `stock_opname_details`, `pharmacy_warehouses`, `warehouse_stock`, `stock_transfers`, `stock_transfer_items`, `units`

### 🚚 Modul Distributor & Farmasi Grosir B2B (10 tabel)
`distributor_customers`, `distributor_stocks`, `distributor_stock_movements`, `distributor_sales`, `distributor_sale_details`, `distributor_receivables`, `distributor_payments`, `distributor_stock_transfers`, `distributor_returns`, `distributor_return_details`

### 💰 Modul Keuangan & Kasir (10 tabel)
`billing_transactions`, `billing_details`, `cash_registers`, `cash_transactions`, `internal_cash_transfers`, `payment_methods`, `fee_rules`, `fee_transactions`, `doctor_fee_settlements`, `transaction_account_mappings`

### 📊 Modul Akuntansi Double-Entry (3 tabel)
`accounts` (Chart of Accounts), `journal_entries`, `journal_entry_details`

### 🏗️ Modul Pengadaan & Approval (9 tabel)
`suppliers`, `purchase_requests`, `purchase_request_items`, `purchase_orders`, `purchase_order_items`, `goods_receipts`, `goods_receipt_items`, `approval_workflows`, `approval_requests`, `approval_steps`

### 🍽️ Modul Restoran Gizi (5 tabel)
`restaurant_menus`, `restaurant_orders`, `restaurant_order_details`, `restaurant_tables`, `kitchen_orders`

### 👥 Modul HRD & Kepegawaian (7 tabel)
`employees`, `employee_attendances`, `employee_leaves`, `payrolls`, `payroll_items`, `work_shifts`, `departments`, `job_positions`

### 📦 Modul Inventaris Aset (4 tabel)
`inventory_assets`, `asset_depreciations`, `asset_maintenances`, `asset_mutations`

### ⚙️ Modul Sistem & Administrasi (18 tabel)
`users`, `roles`, `permissions`, `role_permissions`, `user_permissions`, `audit_logs`, `system_settings`, `system_notifications`, `system_notification_reads`, `notification_rules`, `system_updates`, `system_documentations`, `system_error_logs`, `system_slow_queries`, `whatsapp_logs`, `articles`, `migrations`

### 🏥 Master Data Referensi (14 tabel)
`categories`, `consent_templates`, `icd10_diagnoses`, `icd9_procedures`, `master_icd10`, `master_icd9`, `insurance_providers`, `services`, `service_prices`, `tindakan`, `rooms`, `beds`

---

## 🎭 RBAC: Peran Pengguna Utama

| # | Peran | Akses Utama | Deskripsi |
|:---:|:---|:---|:---|
| 1 | **Super Admin** | *Seluruh Modul* | Manajemen sistem, hak akses, audit trail, backup, konfigurasi |
| 2 | **Pendaftaran** | Pasien, Antrean, Cetak | Pendaftaran offline/online, booking poli, verifikasi BPJS |
| 3 | **Perawat** | Triage, Vital Sign | Pemeriksaan tanda vital awal (tekanan darah, suhu, SPO2) |
| 4 | **Dokter** | SOAP, ICD, e-Resep | Pemeriksaan klinis, diagnosa, tindakan, rujukan lab, resep |
| 5 | **Farmasi/Apoteker** | Resep, Stok, Batch | Dispensing resep, kartu stok, penjualan OTC |
| 6 | **Kasir/Billing** | Kasir Klinik, Rekap | Penerimaan pembayaran, cetak kuitansi, rekap shift |
| 7 | **Distributor / Grosir** | B2B POS, Piutang, DO | Faktur partai besar, monitoring plafon kredit, surat jalan, retur |
| 8 | **Kasir Resto/Waiter** | POS Resto, Meja | Pemesanan menu, pembagian meja, open bill |
| 9 | **Chef/Dapur** | Kitchen Display System | Pemantauan tiket pesanan dapur realtime |
| 10 | **Keuangan/Akuntan** | Kas, Jurnal, Laporan | Manajemen arus kas, posting jurnal, 5 laporan keuangan |
| 11 | **Logistik/Gudang** | PO, GRN, Transfer Stok | Penerimaan barang, mutasi antar gudang, kartu stok |
| 12 | **Direktur/Manajemen** | Laporan Eksekutif | Pemantauan grafik omset, pengesahan laporan, approval |

---

## 🔄 ALUR BISNIS TERINTEGRASI

### 1. Alur Pelayanan Klinik & Retail
```
PASIEN MENDAFTAR (Online/Offline)
    ↓
PEMERIKSAAN TRIAGE (Perawat)
    ├─ Input Tanda Vital (Tensi, Nadi, Suhu, RR, SPO2)
    ↓
PEMERIKSAAN DOKTER (RME SOAP)
    ├─ Diagnosa ICD-10 & Prosedur ICD-9-CM
    ├─ e-Prescription ke Apotek
    ├─ Rujukan Diet ke Resto
    └─ Permintaan Lab Patologi
    ↓
PARALELISASI OPERASIONAL
    ├─ APOTEK: Dispensing Resep (FIFO/FEFO Multi-Batch)
    ├─ RESTO: KDS Dapur Menyiapkan Menu Diet
    └─ LAB: Input Hasil Uji Laboratorium
    ↓
KASIR TERPADU (Unified Billing)
    ├─ Tagihan Auto-Terkalkulasi (Tindakan + Obat + Resto + Lab)
    ├─ Pembayaran Multi-Method (Tunai, QRIS, Bank, EDC, BPJS)
    └─ Cetak Kuitansi Resmi
    ↓
AKUNTANSI OTOMATIS (JournalEngine)
```

### 2. Alur Unit Distributor & Grosir B2B
```
PELANGGAN GROSIR (Apotek/Klinik/Toko Obat)
    ↓
PENGECEKAN PLAFON & SISA LIMIT KREDIT (DistributorService)
    ↓
PEMBUATAN FAKTUR PENJUALAN GROSIR (`INV-DIST-YYYYMMDD-XXXX`)
    ├─ Multi-Item Obat Grosir + Batch Expired Date
    ├─ Diskon Khusus Partai Besar / Grosir
    ├─ Pilihan Pembayaran: Tunai/Transfer atau Kredit (TOP 7/14/30/60 Hari)
    ↓
LOGISTIK & PENGIRIMAN
    ├─ Cetak Faktur Penjualan Grosir (A4/Continuous)
    ├─ Cetak Surat Jalan / Delivery Order (DO) untuk Kurir/Ekspedisi
    └─ Pengurangan Stok Otomatis di `distributor_stocks` & Kartu Stok
    ↓
POSTING JURNAL OTOMATIS (`JournalEngine`)
    ├─ Tunai: Kas/Bank (D) vs Pendapatan Grosir Akun 4-104 (K)
    ├─ Kredit: Piutang Usaha Grosir Akun 1-103 (D) vs Pendapatan Grosir (K)
    └─ HPP: Beban Pokok Penjualan Akun 5-104 (D) vs Persediaan Grosir (K)
    ↓
MANAJEMEN PIUTANG & RETUR
    ├─ Monitoring Aging Schedule (0-30, 31-60, >60 hari)
    ├─ Pembayaran/Pelunasan Piutang (`PAY-DIST-YYYYMMDD-XXXX`)
    └─ Retur Barang Grosir (`RET-DIST-YYYYMMDD-XXXX`) + Pembalikan HPP & Jurnal
```

---

## 📁 STRUKTUR DIREKTORI PROYEK

```
sawamawamedicalcenter.id/
├── app/
│   ├── Commands/              # CLI commands (Spark)
│   ├── Config/
│   │   ├── Routes.php         # 350 baris rute
│   │   ├── Filters.php        # HTTP filter bindings
│   │   └── Events.php         # Event-driven hooks
│   ├── Controllers/           # 19 Controllers + 5 API
│   │   ├── Accounting.php     # Akuntansi & Laporan
│   │   ├── Apotek.php         # Farmasi & Stok Retail
│   │   ├── Distributor.php    # Distributor & Grosir B2B
│   │   ├── Auth.php           # Login & Session
│   │   ├── Dashboard.php      # Executive Dashboard
│   │   ├── Klinik.php         # Pendaftaran, SOAP
│   │   ├── Keuangan.php       # Kasir & Keuangan
│   │   ├── Resto.php          # POS & Kitchen
│   │   ├── HRD.php            # Payroll & Absensi
│   │   ├── Procurement.php    # PO & Approval
│   │   ├── LiveSync.php       # Real-Time JSON
│   │   └── Api/              # REST API Controllers
│   ├── Filters/               # 3 HTTP Filters
│   ├── Helpers/
│   │   └── setting_helper.php # DataTable Server-Side
│   ├── Models/                # 33 Eloquent Models
│   ├── Services/              # 11 Service Engines
│   │   ├── JournalEngine.php
│   │   ├── DistributorService.php
│   │   ├── ApprovalEngine.php
│   │   ├── PharmacyService.php
│   │   ├── ClinicService.php
│   │   ├── FinanceService.php
│   │   ├── NotificationService.php
│   │   └── ...
│   ├── Views/                 # Blade Templates
│   │   ├── accounting/        # 6 views
│   │   ├── apotek/           # 12 views
│   │   ├── distributor/      # 11 views (Dashboard, POS, Stok, Piutang, Retur, DO)
│   │   ├── klinik/           # 16 views
│   │   ├── keuangan/         # 7 views
│   │   ├── resto/            # 6 views
│   │   ├── hrd/              # 6 views
│   │   └── ...
│   └── Libraries/
├── database/
│   └── dumps/                 # SQL Backups
├── docs/
│   ├── DOKUMENTASI_SISTEM_LENGKAP.md
│   └── system_guide/
├── public/
└── README.md
```

---

## 🚀 12 MODUL SISTEM UTAMA

### 1️⃣ **Portal Publik & Pendaftaran Pasien**
- Landing page profil klinik modern
- Pendaftaran pasien baru online (tanpa login)
- Booking kunjungan pasien lama
- Kiosk mandiri (APM) fullscreen responsif
- Auto-generate No. Rekam Medis (`RM-XXXXXX`)
- Auto-generate Nomor Antrean per poli (`A-001`, `B-001`)
- Integrasi data BPJS/Asuransi & Membership Tier

### 2️⃣ **Pelayanan Klinik & Rekam Medis Elektronik (RME)**
- Antrean multi-poli dengan Display TV bersuara
- RME Berstandar SOAP (Subjective, Objective, Assessment, Plan)
- Input vital signs terintegrasi (Tensi, Suhu, Nadi, RR, SPO2, BB, TB)
- e-Prescription langsung ke Apotek
- Laboratorium Patologi Klinis dengan referensi normal range
- Odontogram gigi digital interaktif (32 gigi FDI)
- Surat Medis & Rujukan (SKS, SKD, SRP)
- Manajemen Rawat Inap dengan bed management
- Riwayat kunjungan pasien timeline & cetak ringkasan RME (PDF)

### 3️⃣ **Farmasi & Apotek Terpadu (Retail)**
- Katalog obat 1000+ item dengan pencarian server-side
- **Multi-Batch & Expired Date Tracking** (FIFO/FEFO)
- Dispensing e-resep otomatis
- Penjualan OTC (Over-The-Counter) non-resep
- Stock Opname fisik vs sistem
- Multi-Gudang (Gudang Induk + Depo Apotek + Depo Rawat Inap)
- Mutasi transfer antar gudang otomatis
- Kartu Stok (Stock Card Ledger) dengan running balance
- Peringatan stok kritis (reorder point) & cetak label etiket

### 4️⃣ **Distributor & Farmasi Grosir B2B (Unit Mandiri)**
- **Dashboard Distributor**: Metrik omset harian/bulanan, total piutang, overdue alert, valuasi stok B2B, dan grafik tren penjualan.
- **Kasir & Faktur Grosir (`Distributor::penjualan`)**:
  - Penjualan partai besar untuk Apotek mitra, Klinik, Toko Obat, RS, dan Reseller.
  - Auto-generate No. Faktur: `INV-DIST-YYYYMMDD-XXXX`.
  - Integrasi validasi limit kredit pelanggan dan sisa plafon.
  - Perhitungan diskon grosir per item, PPN, dan opsi pembayaran (Tunai/Kredit TOP 7-60 hari).
  - Cetak Faktur Penjualan Grosir resmi (A4/Continuous Paper) ber-Kop Surat.
  - Cetak Surat Jalan / Delivery Order (DO) untuk ekspedisi.
- **Master Pelanggan Grosir (`Distributor::pelanggan`)**: Pengaturan data legalitas (SIA/SIPA/NPWP), limit kredit, termin pembayaran (TOP), dan riwayat piutang.
- **Stok & Batch Grosir (`Distributor::stok`)**: Pengelolaan harga modal vs harga grosir, buffer stok, dan kartu stok mutasi distributor.
- **Transfer Stok Antar Gudang (`Distributor::transfer`)**: Mutasi barang dari Gudang Induk Logistik ke Unit Distributor (`TRF-DIST-YYYYMMDD-XXXX`).
- **Manajemen Piutang Usaha (`Distributor::piutang`)**:
  - Monitoring piutang jatuh tempo & klasifikasi Aging Schedule (0-30, 31-60, >60 hari).
  - Pembayaran piutang bertahap/lunas (`PAY-DIST-YYYYMMDD-XXXX`) & cetak bukti bayar.
- **Retur Penjualan Grosir (`Distributor::retur`)**: Pengembalian obat rusak/kadaluarsa (`RET-DIST-YYYYMMDD-XXXX`), auto-reversing HPP, dan penyesuaian piutang.
- **Laporan Laba Rugi Mandiri Distributor (`Distributor::laporan`)**: Analisis P&L khusus unit grosir (Penjualan Akun 4-104, Retur 4-304, HPP 5-104).

### 5️⃣ **Restoran Sehat & Kitchen Display System (KDS)**
- Touchscreen POS Kasir & manajemen meja/open bill
- Menu categories dengan filter dinamis
- Kitchen Display System (KDS) dapur realtime (Queued → Cooking → Ready)
- Resep diet pasien terintegrasi langsung dari rekam medis dokter
- Split payment support & integrasi tagihan ke kasir utama

### 6️⃣ **Kasir Terpadu & Multi-Metode Pembayaran**
- Unified billing: Tindakan + Obat + Resto + Lab dalam 1 invoice terpadu
- Multi-payment: Tunai, QRIS, Bank Transfer, EDC, BPJS, Asuransi, Piutang
- Cetak struk thermal 58mm/80mm & kuitansi resmi A4/A5
- Split/Combo payment (contoh: Sebagian BPJS + Sebagian Tunai)

### 7️⃣ **Keuangan & Manajemen Kas**
- Kas register per shift kasir (buka & tutup kas)
- Transaksi kas masuk/keluar harian & transfer kas internal
- Rekap shift kasir saat pergantian tugas
- Monitoring piutang pasien & hutang pengadaan supplier
- Perhitungan komisi & jasa medis dokter/nakes

### 8️⃣ **Akuntansi & Laporan Keuangan Double-Entry**
- **Chart of Accounts (COA)** terstandarisasi
- **Journal Engine Otomatis**: Setiap transaksi kasir, apotek retail, resto, dan distributor auto-jurnal seimbang 100%
- **Setup Saldo Awal** dengan Smart Auto-Balancing (ke akun Modal)
- **5 Laporan Keuangan Komprehensif**:
  1. Laporan Laba Rugi (Income Statement)
  2. Laporan Neraca (Balance Sheet)
  3. Laporan Arus Kas (Cash Flow Statement)
  4. Rekapitulasi Omset Lini Usaha (Poli, Apotek, Distributor, Resto, Lab)
  5. Buku Besar (General Ledger) & Jurnal Umum

### 9️⃣ **Pengadaan & Approval Workflow**
- Purchase Request (PR) → Purchase Order (PO) → Goods Receipt Note (GRN)
- Multi-level Approval Workflow (Verifikator → Approver)
- Auto-create batch obat dan update stok Gudang Induk saat barang diterima
- Manajemen master pemasok (PBF)

### 🔟 **Inventaris & Manajemen Aset**
- Master data aset tetap, barcode tracking, dan lokasi fisik
- Perhitungan penyusutan otomatis (Straight-line Depreciation)
- Riwayat mutasi lokasi dan log perawatan/servis berkala

### 1️⃣1️⃣ **HRD & Manajemen Kepegawaian**
- Master data pegawai, jabatan, dan departemen
- Presensi check-in/check-out harian per shift kerja
- Pengajuan dan persetujuan cuti karyawan
- Penggajian otomatis (Payroll bulanan) + slip gaji PDF & auto-jurnal beban gaji

### 1️⃣2️⃣ **Sistem Administrasi, Audit & Keamanan**
- RBAC Granular (16 Role + 75+ Permission + User Permission Override)
- Audit Trail komprehensif seluruh aktivitas HTTP
- WhatsApp Gateway & simulasi notifikasi otomatis
- Error Tracker terpusat (deduplicated hash) & Slow Query Monitor
- Backup database dump SQL sekali klik
- Pusat Bantuan interaktif & dokumentasi alur kerja sistem