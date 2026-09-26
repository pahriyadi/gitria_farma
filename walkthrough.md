# Laporan Penyelesaian: Fleksibilitas Aturan Jurnal Terpadu (Nominal Rp & Persentase %)

Sesuai instruksi dan kesepakatan terbaru:
1. **Tidak lagi memakai "Pembagi Fee Karyawan (Proporsional Rp)"** yang kaku/membingungkan.
2. **Semua sub-pos akun bebas diatur**: bisa dengan **Nominal Tetap (Rp)** atau **Persentase (%)**.
3. **Pemusatan Penuh di 1 Halaman**: Seluruh aturan jurnal multi-akun untuk semua modul (Klinik Rawat Jalan, Apotek Resep, Obat Bebas, Resto) kini diatur melalui menu resmi [Template & Aturan Jurnal](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/accounting/aturan_jurnal.php) (`accounting/aturan-jurnal`).
4. **Membersihkan Menu Redundan**: Tab terpisah "Skema Bagi Hasil" telah dihapus dari navigasi akuntansi sehingga tata kelola sistem menjadi rapi dan terpusat.

---

## Ringkasan Perubahan yang Dilakukan

### 1. Master Aturan Jurnal Klinik (`RAWAT_JALAN_POLI`)
Sub-pos akun untuk kategori **PELAYANAN RAWAT JALAN & TINDAKAN MEDIS** telah dikonfigurasi lengkap di menu [Detail Aturan Jurnal](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/accounting/aturan_jurnal_detail.php):
* **[DEBET] KAS KASIR KLINIK** (1111 / 111): Mengikuti metode pembayaran pasien (Kas / Bank / QRIS).
* **[KREDIT] Utang Fee Dokter** (241): Tipe `dynamic_fee` (mengambil persentase fee dari master dokter pemeriksa, default 66,67%).
* **[KREDIT] Utang Jasa Karyawan** (242): Tipe fleksibel — bisa diisi **Nominal Tetap (Rp)** (misal Rp 10.000) atau **Persentase (%)** (misal 6,67%).
* **[KREDIT] Fasilitas Klinik** (425): Tipe `percentage` (17,93%).
* **[KREDIT] BMHP** (415): Tipe `percentage` (5,07%).
* **[KREDIT] Administrasi Klinik** (421): Tipe `percentage` (2,93%).
* **[KREDIT] Konseling Farmasi / Penyeimbang** (422): Tipe `percentage` (0,73%) dengan penyeimbang otomatis selisih pembulatan rupiah.

---

### 2. Mesin Jurnal Dinamis ([JournalEngine.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Services/JournalEngine.php))
* Fungsi `postClinicSplitJournal()` dan `postPharmacySplitJournal()` kini secara dinamis membaca tabel `journal_categories` dan `journal_category_rules`.
* Mendukung penuh:
  * `fixed_amount`: Langsung memotong/mengalokasikan nominal rupiah yang diinputkan.
  * `percentage`: Menghitung persentase dari total transaksi.
  * `dynamic_fee`: Membaca fee per-dokter yang bertugas menangani pasien.
  * *Auto-balancing*: Menyerap selisih pembulatan sen/rupiah ke pos penampung yang ditentukan agar jurnal **selalu 100% balance**.

---

### 4. Perbaikan Penjualan Resep di Kasir Apotek (`/apotek/penjualan`) Masuk ke Jurnal Umum

* **Penyebab Masalah**: 
  Saat transaksi penjualan obat resep disimpan dari `/apotek/penjualan` (`pharmacy_sales` ID 1), sistem memanggil `postPharmacySplitJournal` dengan `source_module = 'Apotek (Obat Resep)'` dan `reference_id = 1`. Modul jurnal mendeteksi entri lama dengan nama modul dan ID yang sama dari penyerahan resep rawat jalan klinik (`billing_transactions` ID 1), sehingga salah mengira bahwa transaksi tersebut telah pernah dijurnal dan membatalkan pembuatan entri baru.
* **Solusi yang Diterapkan**:
  1. Memisahkan modul sumber untuk transaksi langsung di kasir apotek menjadi:
     - `Kasir Apotek (Obat Resep)`
     - `Kasir Apotek (Obat Bebas)`
     - `Kasir Apotek (Konsultasi Online)`
     Sedangkan resep dari kunjungan rawat jalan dokter tetap menggunakan `Apotek (Obat Resep)`.
  2. Menambahkan nama dokter `(Dr. [Nama Dokter])` otomatis pada uraian jurnal kasir apotek jika dokter peresep dipilih.
  3. Mengupdate transaksi penjualan resep kasir apotek yang tertunda (`SL-20260906-0001` - Rp 43.000) ke Jurnal Umum:
     - Nomor Jurnal: `JV-20260907-0001`
     - Modul Sumber: `Kasir Apotek (Obat Resep)`
     - Status: 100% Seimbang (Debet Rp 43.000 | Kredit Rp 43.000).

### 5. Peningkatan & Perbaikan Tab Riwayat Penjualan Apotek (`/apotek/penjualan`)

* **Perbaikan Tampilan & Layout DataTables**:
  - Memperbaiki masalah tabel yang menyusut/terdistorsi saat tab Riwayat dibuka dengan menambahkan event `shown.bs.tab` untuk auto-recalc kolom DataTables.
  - Menambahkan counter transaksi di tab header: `<span class="badge badge-teal ml-1"><?= count($recentSales) ?></span>`.
* **Ringkasan Widget Riwayat**:
  - Menambahkan 3 widget ringkasan di atas tabel: Total Transaksi Kasir, Total Omset Riwayat (Rp), dan Komposisi Transaksi (Resep/Online vs Obat Bebas) beserta tombol pintas ke Laporan Apotek.
* **Tabel Riwayat Transaksi**:
  - Kolom dilengkapi: No, No. Nota & Jam Transaksi, Pelanggan (WhatsApp), Dokter Peresep, Jenis Transaksi, Metode Bayar & Nama Kasir, Grand Total (+Tusla/Embalase), serta Tombol Aksi.
* **Modal Rincian Transaksi Interaktif (`#modal-detail-sale`)**:
  - Dilengkapi tombol **Detail** pada setiap baris untuk melihat pop-up modal berisi rincian lengkap bahan baku/obat, batch FEFO, tusla, embalase, diskon, uang diterima, kembalian, serta integrasi status ke Jurnal Umum (`JV-...`).
  - Tombol **Cetak Ulang Nota** langsung dari dalam modal.

---

## Hasil Pengujian & Verifikasi

### 1. Uji Transaksi Layanan Klinik (`spark test:clinic-features`)
* **Uji Kasus 1 (Nominal Tetap Rp 10.000)**:
  ```
  Total Transaksi: Rp 150.000
  [DEBET ] Kas kasir 1                          : Rp 150.000
  [KREDIT] Utang Fee Dokter (66.67%)            : Rp 100.000
  [KREDIT] Utang Jasa Karyawan (Nominal Rp)     : Rp  10.000
  [KREDIT] Fasilitas Klinik (17.93%)            : Rp  26.895
  [KREDIT] BMHP (5.07%)                         : Rp   7.605
  [KREDIT] Administrasi (2.93%)                 : Rp   4.395
  [KREDIT] Konseling Farmasi / Penyeimbang      : Rp   1.105
  TOTAL: Debit Rp 150.000 | Kredit Rp 150.000 -> 100% BALANCE!
  ```

* **Uji Kasus 2 (Persentase 6,67%)**:
  ```
  Total Transaksi: Rp 150.000
  [DEBET ] Kas kasir 1                          : Rp 150.000
  [KREDIT] Utang Fee Dokter (66.67%)            : Rp 100.000
  [KREDIT] Utang Jasa Karyawan (6.67%)          : Rp  10.005
  [KREDIT] Fasilitas Klinik (17.93%)            : Rp  26.895
  [KREDIT] BMHP (5.07%)                         : Rp   7.605
  [KREDIT] Administrasi (2.93%)                 : Rp   4.395
  [KREDIT] Konseling Farmasi / Penyeimbang      : Rp   1.100
  TOTAL: Debit Rp 150.000 | Kredit Rp 150.000 -> 100% BALANCE!
  ```

### 2. Uji Transaksi Farmasi Apotek (`spark test:pharmacy-features`)
* **Penjualan Resep Dokter Rp 85.000**: Debit Rp 85.000 == Kredit Rp 85.000 (**BALANCE OK**).
* **Penjualan Obat Bebas Rp 100.000**: Debit Rp 100.000 == Kredit Rp 100.000 (**BALANCE OK**).

### 3. Uji Audit Transaksi & ACID Integrity (`spark audit:transactions`)
* Seluruh 16 entri jurnal di buku besar terbukti **100% seimbang (Total Debit == Total Kredit)** tanpa ada selisih.
* Seluruh relasi kunci asing, mutasi stok obat, dan audit trail aktif dan berjalan sempurna.

---

## Cara Menggunakannya Sekarang

1. Masuk ke menu **Accounting > 5. Template & Aturan Jurnal** (`accounting/aturan-jurnal`).
2. Pilih kategori yang ingin diubah (misal: **PELAYANAN RAWAT JALAN & TINDAKAN MEDIS** atau **PENJUALAN OBAT RESEP**), lalu klik tombol hijau **Kelola Sub-Aturan Pos**.
3. Di dalam layar sub-aturan:
   * Klik ikon **Edit (Pensil)** pada pos akun yang ingin diatur (misal: *Utang Jasa Karyawan*).
   * Pilih **Tipe Kalkulasi**:
     * Jika ingin nominal rupiah: Pilih **Nominal Tetap (Rp)** lalu masukkan nominalnya (misal: `10000`).
     * Jika ingin persentase: Pilih **Persentase (%)** lalu masukkan nilainya (misal: `6.67`).
   * Klik **Simpan Sub-Pos**.
4. Di sebelah kanan layar terdapat panel **Simulator Kalkulasi Bagi Hasil** yang bisa langsung dicoba secara *real-time* untuk memastikan perhitungannya sudah sesuai sebelum transaksi kasir dijalankan.
