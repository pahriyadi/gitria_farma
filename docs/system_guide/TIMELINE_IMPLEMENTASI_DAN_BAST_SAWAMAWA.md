# MASTER RENCANA KERJA 2 BULAN: PENYEMPURNAAN SISTEM, KONSULTASI, PENYESUAIAN KEBUTUHAN RIIL, TESTING, PELATIHAN, GO-LIVE & BAST
**SAWAMAWA MEDICAL CENTER & RESTO GIZI**  
*Sistem Informasi Manajemen Fasilitas Kesehatan Terpadu (Klinik, Farmasi, Resto, Kasir & Akuntansi)*  
**Dokumen Referensi:** `TL-REFINE-BAST/2026`  
**Total Durasi Pelaksanaan:** 2 Bulan (8 Minggu / 60 Hari Kalender)  
**Status Fondasi Sistem Awal:** Siap Pakai (*101 Tabel DB, 23 Controllers, UI Paper White, RBAC 16 Peran*)

---

## 📌 Ringkasan Parameter Rencana Kerja 2 Bulan

| Parameter | Keterangan |
|---|---|
| **Fokus Proyek** | **Penyempurnaan Modul, Konsultasi/FGD Kebutuhan Riil, Testing, Training & BAST** |
| **Lokasi Implementasi** | Sawamawa Medical Center & Resto Gizi, Sumbawa Besar, NTB |
| **Lingkup Penyempurnaan** | **Klinik (RME SOAP & Odontogram), Farmasi Multi-Gudang, Resto Gizi & Akuntansi Terpadu** |
| **Bulan 1 (M1 - M4)** | Konsultasi Langsung, FGD Kebutuhan Lapangan & Penyesuaian Fitur Riil |
| **Bulan 2 (M5 - M8)** | Simulasi Alur Penuh (Dry-Run), Testing UAT, Pelatihan Nakes/Staf, Go-Live & BAST |
| **Target UAT Resmi** | Minggu Ke-6 (Hari Ke-36 s/d 42) |
| **Target Pelatihan User** | Minggu Ke-7 (Hari Ke-43 s/d 47) |
| **Target Go-Live Resmi** | Minggu Ke-8 (Hari Ke-50) |
| **Masa Pendampingan (Hypercare)** | Minggu Ke-8 (Hari Ke-51 s/d 59) — Standby On-Site di Klinik |
| **Target BAST & Serah Terima** | Minggu Ke-8 (Hari Ke-60) |
| **Garansi Pemeliharaan** | 6 (Enam) Bulan Bebas Biaya Pasca Go-Live & BAST |

---

## 1. 📊 Matriks Gantt Chart Rencana Kerja (Minggu 1 – Minggu 8)

| No | Komponen & Modul Pekerjaan | PIC Tim | M1 | M2 | M3 | M4 | M5 | M6 | M7 | M8 | Deliverable / Output Utama |
|:--:|:---|:---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---|
| **I** | **BULAN 1 — KONSULTASI & PENYESUAIAN KEBUTUHAN RIIL (4 BIDANG)** | | | | | | | | | | |
| 1.1 | **Klinik & RME:** Konsultasi dokter/perawat, finetuning SOAP, Odontogram 32 gigi, triase ATS, format cetak rujukan riil | Nakes & Dev | 🟩 | | | | | | | | RME SOAP & Poli Gigi Sesuai Kebiasaan Dokter |
| 1.2 | **Farmasi Multi-Gudang:** FGD Apoteker, alur Gudang Induk vs Depo, mutasi stok, etiket obat, racikan, FEFO/FIFO riil | Apoteker & Dev | | 🟩 | | | | | | | Alur Mutasi Obat & Kartu Stok Presisi Riil |
| 1.3 | **Resto Gizi & Kasir:** Penyelarasan menu diet pasien dari SOAP, layar KDS dapur, multi-payment QRIS/EDC, struk kwitansi | Koki, Kasir, Dev | | | 🟩 | | | | | Alur Dapur KDS & Billing Kasir Terpadu |
| 1.4 | **Akuntansi & Finansial:** Penyesuaian COA riil klinik, aturan bagi hasil komisi dokter, 5 laporan keuangan BI/OJK, saldo awal | Akuntan & Dev | | | | 🟩 | | | | COA Riil, Jurnal Otomatis & Format Laporan Valid |
| **II** | **BULAN 2 — SIMULASI, TESTING, PELATIHAN, GO-LIVE & BAST** | | | | | | | | | | |
| 2.1 | **Simulasi Transaksi Riil Tertutup (Dry-Run):** Uji coba 50 skenario kunjungan pasien (Pendaftaran $\rightarrow$ RME $\rightarrow$ Resep $\rightarrow$ Kasir $\rightarrow$ Jurnal) | QA & Tim Inti | | | | | 🟦 | | | | Alur Closed-Loop 100% Mulus & Tanpa Selisih |
| 2.2 | **User Acceptance Testing (UAT) Resmi:** Verifikasi fungsional bersama seluruh kepala unit kerja & perbaikan cepat temuan UAT | Kepala Unit & Dev | | | | | | 🟦 | | | Form Checklist UAT Seluruh Modul Berstatus PASSED |
| 2.3 | **Pelatihan Intensif Pengguna (Hands-On):** 7 sesi pelatihan dokter, perawat, apotek, kasir, dapur resto, akuntan, direksi | Trainer & Staf | | | | | | | 🟧 | | Seluruh Staf Mahir Mengoperasikan Sistem |
| 2.4 | **Migrasi Master & Saldo Awal Riil:** Pembersihan data simulasi (clean state), input stok fisik riil obat & saldo kas/bank | DBA & Akuntan | | | | | | | 🟧 | | Database Siap Produksi (*Clean State Balanced*) |
| 2.5 | **CUT-OFF & GO-LIVE RESMI OPERASIONAL:** Sistem resmi 100% melayani seluruh pasien riil klinik | Seluruh Tim | | | | | | | | 🟥 | **Sistem Aktif Melayani Pasien Riil (Hari 50)** |
| 2.6 | **Pendampingan Lapangan (Hypercare On-Site):** Tim teknis standby mendampingi langsung staf di klinik | Support Specialist | | | | | | | | 🟧 | Pendampingan Lapangan (Hari 51 s/d 59) |
| 2.7 | **EVALUASI AKHIR & PENANDATANGANAN BAST:** Penyerahan dokumen legal BAST resmi bersama Direktur Utama | Direksi & Dev | | | | | | | | 🟩 | **BAST Resmi Ditandatangani & Sah (Hari 60)** |

> **Keterangan Warna Simbol:**  
> 🟩 *Konsultasi, Penyesuaian Kebutuhan Riil & BAST* &nbsp;|&nbsp; 🟦 *Simulasi Alur & Testing UAT* &nbsp;|&nbsp; 🟧 *Pelatihan Staf, Setup Riil & Hypercare* &nbsp;|&nbsp; 🟥 *Go-Live Resmi Operasional*

---

## 2. 🔍 Rincian Agenda Bulan 1: Konsultasi & Penyesuaian Kebutuhan Riil

Mengingat fondasi sistem telah lengkap (101 tabel database, routing, template UI Paper White, RBAC), 4 minggu pertama dialokasikan untuk mendengarkan masukan pengguna dan mencocokkan sistem dengan SOP riil klinik:

### Minggu 1: Bidang Pelayanan Medis & Rekam Medis Elektronik (RME)
* **Konsultasi & Diskusi Terarah (FGD):**
  * Diskusi bersama Dokter DPJP (Umum, Gigi, Spesialis) dan Koordinator Perawat mengenai alur pelayanan pasien dari triase s/d ruang periksa.
* **Penambahan & Penyesuaian Kebutuhan Riil:**
  * Penyesuaian default isian formulir RME SOAP agar sesuai dengan gaya pemeriksaan dokter.
  * Penyesuaian tata letak dan palet diagnosa pada **Odontogram 32 Gigi** untuk Poli Gigi.
  * Penyesuaian parameter Triase UGD (Skala ATS 1-5) dan format cetak surat medis (Surat Sakit, Surat Sehat, Surat Rujukan) berkop resmi klinik.

### Minggu 2: Bidang Farmasi, Apotek & Multi-Gudang
* **Konsultasi & Diskusi Terarah (FGD):**
  * Diskusi bersama Apoteker Pengelola Apotek (APA) dan tenaga teknis farmasi mengenai alur barang masuk, penyimpanan, dan pengeluaran obat.
* **Penambahan & Penyesuaian Kebutuhan Riil:**
  * Penyesuaian alur mutasi stok fisik dari Gudang Induk Logistik ke Depo Rawat Jalan / Depo IGD beserta cetak Surat Jalan Mutasi.
  * Penyesuaian format cetak etiket aturan pakai obat (sirup, tablet, salep, tetes).
  * Pengaturan aturan racikan obat (kapsul/pulveres) dan penetapan batas minimum stok (peringatan stok kritis & FEFO).

### Minggu 3: Bidang Resto Gizi Sehat & Kasir Billing Terpadu
* **Konsultasi & Diskusi Terarah (FGD):**
  * Diskusi bersama Kepala Dapur Resto Sehat mengenai tampilan Kitchen Display System (KDS) dan alur penyajian makanan sehat.
  * Diskusi bersama Petugas Kasir mengenai integrasi seluruh transaksi pelayanan.
* **Penambahan & Penyesuaian Kebutuhan Riil:**
  * Integrasi menu diet klinis dari rekam medis dokter langsung tampil di layar juru masak/dapur.
  * Penyesuaian metode pembayaran riil di kasir: QRIS dinamis/statis, mesin EDC bank rekanan, tunai, dan piutang penjamin/asuransi.
  * Fitur pemecahan nota (*split bill*) dan format cetak struk kwitansi pembayaran resmi.

### Minggu 4: Bidang Keuangan, Akuntansi & Jasa Medis Dokter
* **Konsultasi & Diskusi Terarah (FGD):**
  * Diskusi bersama Kepala Bagian Keuangan dan Akuntan Klinik mengenai bagan akun standar (COA) dan pelaporan keuangan.
* **Penambahan & Penyesuaian Kebutuhan Riil:**
  * Sinkronisasi Chart of Accounts (COA) dengan rekening bank dan kas riil milik Sawamawa Medical Center.
  * Penyesuaian rumus perhitungan bagi hasil jasa medis dokter per tindakan/pelayanan.
  * Penyelarasan format cetak Laporan Laba Rugi, Neraca, Arus Kas, dan Buku Besar berstandar audit Bank Indonesia/OJK.
  * Uji coba setup saldo awal dengan fitur *Smart Auto-Balancing* ke Modal Disetor.

---

## 3. 🧪 Rincian Agenda Bulan 2: Simulasi, Testing UAT, Pelatihan, Go-Live & BAST

### Minggu 5: Simulasi Transaksi Riil Tertutup (Dry-Run Simulation)
* Melakukan uji coba 50 skenario transaksi end-to-end tanpa melibatkan pasien umum:
  $$\text{Pendaftaran Pasien} \longrightarrow \text{Triase Perawat} \longrightarrow \text{SOAP Dokter \& Odontogram} \longrightarrow \text{e-Resep \& Order Resto} \longrightarrow \text{Dispensing Apotek} \longrightarrow \text{Kasir Billing} \longrightarrow \text{Auto-Jurnal Akuntansi} \longrightarrow \text{Laporan Keuangan}$$
* Memvalidasi tidak ada selisih saldo, mutasi stok akurat, dan cetak dokumen berjalan sempurna.

### Minggu 6: User Acceptance Testing (UAT) Resmi & Sign-Off
* Pelaksanaan UAT resmi bersama seluruh penanggung jawab unit:
  1. *Front Office:* Pendaftaran & antrean voice caller.
  2. *Keperawatan:* Triase ATS & TTV.
  3. *Dokter:* Form SOAP, ICD-10/9, Odontogram 32 gigi.
  4. *Farmasi:* Dispensing resep, mutasi multi-gudang, kartu stok.
  5. *Resto:* POS touchscreen & Kitchen Display.
  6. *Kasir:* Pembayaran multi-metode & cetak kwitansi.
  7. *Akuntansi:* Jurnal otomatis, saldo awal & laporan keuangan balance.
  8. *Manajemen:* Approval anggaran & monitoring dashboard eksekutif.
* Perbaikan cepat (*fast-track refinement*) jika ada temuan minor saat UAT.

### Minggu 7: Pelatihan Pengguna (Hands-On) & Migrasi Saldo Awal Riil
* **Hari Ke-43 s/d 47 (Pelatihan Intensif):** Seluruh staf/nakes dilatih langsung menggunakan perangkat masing-masing hingga mandiri dan lancar.
* **Hari Ke-48 s/d 49 (Setup Data Riil):**
  * Pembersihan database simulasi pengujian (*Truncate Data Transaksi Uji*).
  * Penginputan stok fisik riil obat apotek hasil stock opname bersama.
  * Penginputan saldo riil kas kasir & bank, master tarif, data nakes, dan penguncian saldo awal (*100% Balanced*).

### Minggu 8: Go-Live Resmi, Pendampingan Lapangan (Hypercare) & BAST
* **Hari Ke-50 (HARI-H GO-LIVE RESMI):**
  * Seluruh operasional klinik dan resto 100% beralih menggunakan sistem ERP terpadu untuk melayani seluruh pasien riil.
* **Hari Ke-51 s/d 59 (Masa Hypercare On-Site):**
  * Tim teknis *standby langsung di klinik* mengawal staf operasional di jam pelayanan, memastikan zero downtime dan kenyamanan seluruh pengguna.
* **Hari Ke-60 (PENANDATANGANAN RESMI BAST):**
  * Penyerahan dokumentasi sistem lengkap, berita acara hasil UAT, dan penandatanganan Berita Acara Serah Terima (BAST) pekerjaan bersama Direktur Utama Sawamawa Medical Center.
