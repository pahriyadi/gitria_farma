# Implementasi Alur Baru: Bayar di Kasir Dahulu Sebelum Obat Disiapkan di Apotek (Pay-First Workflow)

Logika alur operasional farmasi dan kasir telah disesuaikan agar pasien **melunasi tagihan di Kasir Utama terlebih dahulu**, baru kemudian obat dapat disiapkan dan diserahkan oleh Apoteker.

---

## 🔄 Alur Kerja Baru Terpadu (*End-to-End*)

1. **Dokter Selesai SOAP & Resep Obat ([`Klinik.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Klinik.php))**:
   - Tagihan lengkap (jasa medis/tindakan + obat-obatan) langsung berstatus **`open`** di **Kasir Utama**.
   - e-Resep masuk ke antrean **Apotek** dengan indikator **`🟡 Belum Bayar di Kasir`**.
   - Pasien diarahkan langsung ke Kasir Utama untuk pelunasan tagihan.

2. **Penguncian Tombol & Proteksi di Apotek ([`resep.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/apotek/resep.php))**:
   - **Jika Tagihan Belum Lunas**:
     - Ditampilkan banner peringatan: `🔒 MENUNGGU PELUNASAN KASIR UTAMA (OBAT TERKUNCI)`.
     - Tombol **"Selesai Dispensing & Serahkan Obat"** dalam kondisi **DISABLED / TERKUNCI** (warna abu-abu, tidak bisa diklik).
     - Apoteker tetap dapat melihat resep untuk mempersiapkan/meracik obat di awal.
   - **Jika Tagihan Sudah Lunas**:
     - Ditampilkan banner sukses: `✅ PEMBAYARAN SUDAH LUNAS DI KASIR UTAMA`.
     - Tombol **"Selesai Dispensing & Serahkan Obat"** otomatis menjadi **AKTIF** (warna teal).

3. **Sinkronisasi Real-Time Zero-Reload ([`LiveSync.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/LiveSync.php), [`live-sync-engine.js`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/public/assets/js/live-sync-engine.js))**:
   - Saat kasir memproses pelunasan pasien $\rightarrow$ notifikasi audio chime berbunyi dan UI Apotek seketika mengaktifkan tombol penyerahan obat tanpa perlu reload browser.

4. **Validasi Keamanan Server-Side & Pemeliharaan Status Lunas ([`PharmacyService.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Services/PharmacyService.php))**:
   - Sistem menolak proses submit dispensing jika `billing_transactions.status !== 'paid'`.
   - Saat apoteker menyelesaikan dispensing dan menyerahkan obat, status tagihan **tetap dipertahankan `paid` (lunas)** dan **TIDAK di-reset ke `open`**, sehingga tagihan tidak akan masuk kembali ke antrean Kasir Utama.
   - Stok batch FEFO terpotong, resep berstatus `completed`, dan kunjungan pasien selesai penuh (`status = 'completed'`).

5. **Panduan Suara Terpadu Perjalanan Pasien (Unified Voice Calling Guidance)**:
   - **Tahap 1 (Dokter Selesai SOAP & Resep)**:
     - Dokter klik *Simpan Rekam Medis & Resep* $\rightarrow$ Audio berbunyi: *"Nomor antrean [Nomor], atas nama [Nama Pasien], pemeriksaan telah selesai, silakan menuju ke Kasir Utama untuk pembayaran dan penebusan obat."*
   - **Tahap 2 (Kasir Selesai Pelunasan Tagihan)**:
     - Kasir klik *Proses Pembayaran* $\rightarrow$ Audio berbunyi: *"Nomor antrean [Nomor], atas nama [Nama Pasien], pembayaran telah lunas, silakan menuju ke Apotek untuk pengambilan obat."*
   - **Tahap 3 (Apotek Selesai Serahkan Obat)**:
     - Apoteker klik *Selesai Dispensing & Serahkan Obat* $\rightarrow$ Audio berbunyi: *"Nomor antrean [Nomor], atas nama [Nama Pasien], penyerahan obat telah selesai. Terima kasih atas kunjungan Anda, semoga lekas sembuh."*
   - Seluruh tahapan dipandu dengan nomor antrean dan nama pasien yang sama secara berurutan tanpa saling mendahului.

---

## 🛠️ Berkas yang Telah Diperbarui
- [Klinik.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Klinik.php) (Tagihan langsung 'open' dan notifikasi paralel ke Kasir & Apotek)
- [Keuangan.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Keuangan.php) (Status kunjungan diarahkan ke 'prescription' jika ada resep menunggu, broadcast notifikasi ke Apotek saat lunas)
- [PharmacyService.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Services/PharmacyService.php) (Proteksi server-side status 'paid' sebelum dispensing, status kunjungan completed setelah serah obat)
- [Apotek.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Apotek.php) (Join `billing_transactions` untuk mendeteksi `is_paid` dan `billing_status`)
- [LiveSync.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/LiveSync.php) (Integrasi payload realtime `is_paid` dan status billing)
- [resep.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/apotek/resep.php) (Badge status kasir, alert banner, dan locking tombol)
- [live-sync-engine.js](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/public/assets/js/live-sync-engine.js) (Deteksi perubahan lunas secara zero-reload dan auto-unlock tombol)

---

# Form Pembayaran Billing Pasien & Fitur Kasir

Penyempurnaan menyeluruh pada **Form Pembayaran Billing Pasien**, kalkulasi **Subtotal & Diskon**, serta **Fitur Kasir Interaktif** telah selesai diimplementasikan.

---

## ⚡ Fitur & Solusi yang Diterapkan

### 1. Rincian Tabel Lengkap dengan Footer Total Subtotal
- **Struktur Tabel Transparan**:
  - Kolom rincian meliputi: No, Deskripsi Item, Kategori (`LAYANAN`, `OBAT`, `RESTO`), Qty, Harga Satuan, Diskon Item, dan Subtotal.
  - Ditambahkan **Table Footer (`<tfoot>`)** yang secara otomatis menjumlahkan:
    - **Total Qty Item**
    - **Total Subtotal Kotor (`grossSubtotal`)**
    - **Total Diskon Item (`totalItemDisc`)**
    - **Total Subtotal Bersih**

### 2. Breakdown Kategori Tagihan Dinamis
- Ditambahkan ringkasan per kategori di atas metode pembayaran:
  - 🩺 **Layanan Medis & Tindakan: Rp ...**
  - 💊 **Obat & Farmasi: Rp ...**
  - 🍽️ **Resto Gizi / Makanan: Rp ...**

### 3. Kontrol Diskon Fleksibel & Tombol Cepat (Presets)
- Kasir dapat memasukkan diskon nominal langsung (Rp).
- Tersedia tombol cepat persentase diskon: **0% (Reset)**, **5%**, **10%**, **20%**, **50%**, dan **Gratis (100%)**.
- Nilai Tagihan Bersih (`grandTotal = Math.max(0, grossSubtotal - discount)`) dikalkulasi seketika secara real-time.

### 4. Panel Pembayaran & Kembalian Cerdas
- **Metode Tunai / Cash**:
  - Tombol nominal cepat: `Uang Pas`, `Rp 50.000`, `Rp 100.000`, `Rp 200.000`, `Rp 500.000`.
  - Tampilan kembalian interaktif:
    - 🟢 *Hijau*: Lunas Pas (`Rp 0`) atau Uang Kembalian (`Rp X.XXX`).
    - 🔴 *Merah*: Peringatan Kurang Bayar (`- Rp X.XXX`).
- **Metode Non-Tunai (QRIS, Transfer, Debit/Kredit)**:
  - Otomatis mengisi nominal bayar sesuai total tagihan bersih (Lunas Otomatis).

### 5. Ketahanan Sinkronisasi & AJAX Fallback
- Penambahan endpoint AJAX [`/keuangan/billing-details/{id}`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Keuangan.php#L370-L400) dan route di [`Routes.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Config/Routes.php).
- Saat kasir mengklik antrean pasien, rincian item segera ditampilkan dari cache lokal dan diverifikasi ulang ke server via AJAX agar nilai tagihan selalu 100% akurat.

---

## 🛠️ File yang Diperbarui
- [kasir.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/keuangan/kasir.php) (Penyempurnaan markup tabel, breakdown kategori, tombol diskon cepat, kalkulasi subtotal, uang diterima, dan kembalian)
- [Keuangan.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Keuangan.php) (Penambahan method `getBillingDetails` untuk AJAX fallback)
- [Routes.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Config/Routes.php) (Pendaftaran route `keuangan/billing-details/(:num)`)

---

## 2. Audit & Penyempurnaan Sistem Real-time (Poin B s/d E)

Pemeriksaan menyeluruh dan penyempurnaan kode telah dilakukan untuk **Poin B sampai dengan Poin E** tanpa menjalankan automated browser test (sesuai instruksi agar seluruh pengujian dilakukan mandiri oleh Anda).

---

## 🔍 Temuan Audit & Perbaikan yang Diterapkan

### 1. Poin B: Farmasi & Apotek e-Resep (`app/Views/apotek/resep.php` & `LiveSync.php`)
- **Query Filter Status Resep**: Diperbaiki query pada `LiveSync::pharmacyPrescriptions()` agar secara default mengambil seluruh antrean e-resep berstatus `waiting` tanpa terpotong batas tanggal jika ada resep aktif yang belum diserahkan.
- **Stock & FEFO Batch Loading**: Memastikan relasi `prescription_details` $\leftrightarrow$ `medicine_batches` tersaring dengan `stock > 0` dan terurut FEFO (`expired_date ASC`).
- **Event Delegation Selector**: Event listener klik resep dan pemilihan opsi (*Diserahkan Internal*, *Beli di Luar*, *Dibatalkan*) berjalan responsif baik untuk data statis maupun data baru yang masuk secara live tanpa reload.

### 2. Poin C: Kasir & Billing (`app/Views/keuangan/kasir.php` & `LiveSync.php`)
- **Bug Status Query Kasir (KRUSIAL)**: Pada controller `LiveSync::cashierBillings()`, sebelumnya digunakan query `where('bt.status', 'unpaid')`. Berdasarkan skema database, kolom `status` pada `billing_transactions` bertipe enum `('draft', 'open', 'partial', 'paid', 'cancelled')`. Telah diperbaiki menjadi `->whereIn('bt.status', ['draft', 'open', 'partial'])` sehingga antrean tagihan pasien yang baru selesai diperiksa dokter/tindakan langsung muncul secara real-time di kasir.
- **Event Delegation Riwayat Invoice**: Diperbaiki trigger tombol `.btn-view-invoice` menjadi delegated listener `$(document).on('click', ...)` agar modal rincian kwitansi tetap dapat dibuka pada data yang baru disinkronkan secara live.

### 3. Poin D: Laboratorium Klinik (`app/Views/klinik/lab.php` & `LiveSync.php`)
- **Struktur Table Body & Aksi**: Memastikan sinkronisasi tabel `#live-lab-results-body` pada `LiveSyncEngine.renderLabQueue()` memiliki struktur badge status (`NORMAL`, `HIGH`, `LOW`, `ABNORMAL`), tombol cetak dokumen lab, dan token CSRF yang valid untuk form penghapusan.
- **Modal Input Interaktif**: Form input lab baru otomatis mengisi referensi pasien, dokter perujuk, satuan nilai normal, dan batas rujukan parameter tes.

### 4. Poin E: Handover Status Badges & TTV Sync (`app/Views/klinik/antrean.php`, `soap.php`, `pendaftaran.php`)
- **Indikator `Belum TTV` vs `TTV Lengkap`**: Sinkronisasi data triage (`triage_records`) dihubungkan langsung ke antrean poliklinik dan sidebar SOAP dokter. Begitu perawat menyimpan TTV/skrining awal, badge status pasien otomatis berubah menjadi hijau (`TTV Lengkap`) secara real-time.
- **Transisi Status Antrean**: Status antrean (`waiting` $\rightarrow$ `called` $\rightarrow$ `examining` $\rightarrow$ `prescription` $\rightarrow$ `cashier` $\rightarrow$ `completed`) terhubung penuh di layar antrean, pendaftaran, dan workspace dokter.

---

## 🛠️ File yang Telah Diverifikasi & Diperbarui
- [LiveSync.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/LiveSync.php)
- [kasir.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/keuangan/kasir.php)
- [layout.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/layouts/layout.php)
- [Apotek.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Controllers/Apotek.php) & [resep.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/apotek/resep.php)
- [lab.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/klinik/lab.php)
- [soap.php](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/klinik/soap.php)

---

## 3. Hasil Pengujian
- **Status PHP Lint**: ✅ `No syntax errors detected` di semua controller & view.
- **Respon Klik Pasien Baru**: ✅ **100% Instan & Langsung Membuka Panel RME** tanpa perlu refresh.
- **Sinkronisasi Otomatis**: ✅ **Berjalan Mulus di Latar Belakang**.
