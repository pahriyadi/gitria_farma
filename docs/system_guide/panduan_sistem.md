# 📖 PANDUAN PENGGUNAAN & ALUR OPERASIONAL SISTEM ERP
# SAWAMAWA MEDICAL CENTER

*Panduan Lengkap Langkah demi Langkah Pelayanan Medis, Farmasi, Restoran, dan Kasir Keuangan.*

---

## 📑 DAFTAR ISI
1. [Struktur Akun & Login Pengguna](#1-struktur-akun--login-pengguna)
2. [Alur 1: Pendaftaran Pasien Baru & Antrean](#2-alur-1-pendaftaran-pasien-baru--antrean)
3. [Alur 2: Pemeriksaan Triage Perawat & SOAP Dokter](#3-alur-2-pemeriksaan-triage-perawat--soap-dokter)
4. [Alur 3: Pelayanan Farmasi & Apotek](#4-alur-3-pelayanan-farmasi--apotek)
5. [Alur 4: Stock Opname & Laporan Farmasi](#5-alur-4-stock-opname--laporan-farmasi)
6. [Alur 5: Pembayaran Kasir & Cetak Kwitansi](#6-alur-5-pembayaran-kasir--cetak-kwitansi)
7. [Alur 6: Manajemen Master Sistem](#7-alur-6-manajemen-master-sistem)

---

## 1. Struktur Akun & Login Pengguna

Sistem ERP menggunakan autentikasi berbasis peran (*Role-Based Access Control / RBAC*):

| Role / Jabatan | Akses Utama | Keterangan |
| :--- | :--- | :--- |
| **Super Admin** | Seluruh Modul Sistem | Pengaturan Master, User, Audit Log, dan Seluruh Fitur |
| **Dokter** | SOAP Medis & e-Resep | Pemeriksaan RME, Diagnosa ICD, dan Peresepan Obat |
| **Perawat** | Pendaftaran & Triage | Registrasi Pasien & Input Tanda-Tanda Vital |
| **Apoteker** | Farmasi, Resep & Stok | Tebus Resep, Cetak E-Tiket, Master Obat & Stock Opname |
| **Kasir** | Kasir & Keuangan | Pelunasan Tagihan Terpadu & Cetak Kwitansi |

---

## 2. Alur 1: Pendaftaran Pasien (Online Publik & Manual Admin)

### A. Pendaftaran Online Mandiri oleh Pasien (`/daftar-online`)
1. Pasien membuka halaman utama web (`/`) atau langsung ke form **Pendaftaran Online** (`/daftar-online`).
2. Pasien memasukkan NIK, Nama Lengkap, Nomor WhatsApp, Tanggal Lahir, Jenis Kelamin, dan Alamat.
3. Pasien memilih **Pemeriksaan Dokter (Poliklinik)** atau **Tindakan Medis Langsung (Lab/Khitan/dll)**, serta tanggal kunjungan yang diinginkan.
4. Klik **"Daftar Sekarang & Dapatkan Nomor Antrean"**.
5. **Karcis Antrean Digital & Konfirmasi WhatsApp**:
   - Pasien langsung mendapatkan **Nomor Antrean Kunjungan** dan **No. RM**.
   - Pasien dapat mengklik tombol **"Simpan Tiket ke WhatsApp Saya"** untuk mengirim rincian jadwal dan karcis ke nomor WhatsApp-nya.
   - Pasien juga dapat mencetak e-karcis atau langsung chat ke Customer Care WhatsApp Klinik.

### B. Pendaftaran Manual oleh Admin Resepsionis (`/klinik/pendaftaran`)
1. Buka menu **LAYANAN KLINIK** $\rightarrow$ **Pendaftaran Pasien** (`/klinik/pendaftaran`).
2. Masukkan data identitas pasien (NIK, Nama, No. HP, Alamat, Tanggal Lahir, Jenis Kelamin).
3. **Pilih Jenis Kunjungan**:
   - **Pemeriksaan Dokter (Poliklinik)**: Pilih Poliklinik $\rightarrow$ Pilih Dokter Pemeriksa.
   - **Tindakan Medis Langsung**: Pilih Kategori & Nama Tindakan.
4. Klik **"Daftarkan Pasien & Terbitkan Antrean"**.
5. Pasien otomatis masuk ke antrean live (`/klinik/antrean`).

---

## 3. Alur 2: Pemeriksaan Triage Perawat & SOAP Dokter

1. Buka menu **LAYANAN KLINIK** $\rightarrow$ **Pemeriksaan & SOAP** (`/klinik/soap`).
2. Klik nama pasien pada daftar antrean di sebelah kiri.
3. **Pemeriksaan Triage (Perawat)**:
   - Isi Berat Badan, Tinggi Badan, Tekanan Darah (Tensi), Suhu Tubuh, Nadi, dan Keluhan Utama.
   - Klik **"Simpan Catatan Triage Perawat"**.
4. **Pemeriksaan Medis SOAP (Dokter)**:
   - **Subjective (S)**: Keluhan lanjutan & anamnesis.
   - **Objective (O)**: Hasil pemeriksaan fisik langsung.
   - **Assessment (A)**: Diagnosa kerja dokter.
   - **Plan (P)**: Rencana terapi & tindak lanjut.
5. **Diagnosa ICD-10 & Prosedur ICD-9-CM**:
   - Ketik atau pilih kode ICD pada kotak input datalist (deskripsi penyakit otomatis terisi).
6. **Resep Obat Elektronik (e-Resep)**:
   - Pilih nama obat (sistem menampilkan harga satuan eceran per butir/tablet).
   - Masukkan jumlah butir/tablet yang dibutuhkan dan aturan pakai (misal: *3 x 1 tablet sesudah makan*).
   - Estimasi biaya obat otomatis terhitung secara langsung (*live calculation*).
7. Klik **"Selesai Pemeriksaan Dokter & Kirim e-Resep ke Apotek"**.

---

## 4. Alur 3: Pelayanan Farmasi & Apotek

1. Buka menu **FARMASI & APOTEK** $\rightarrow$ **Tebus e-Resep** (`/apotek/resep`).
2. Klik resep pasien yang menunggu penyiapan.
3. Tentukan opsi penyerahan per item obat:
   - 🟢 **Diserahkan dari Apotek Internal** (otomatis mengurangi stok batch sesuai prinsip FEFO).
   - 🟡 **Beli di Luar** (jika stok internal kosong atau merupakan obat rujukan luar).
   - 🔴 **Dibatalkan** (jika pasien tidak menghendaki pengambilan obat tertentu).
4. **Cetak Label E-Tiket Obat**:
   - Klik tombol **"Cetak E-Tiket Obat"** untuk mencetak stiker label aturan pakai obat siap tempel pada kemasan obat pasien.
5. Klik **"Selesaikan Penyiapan & Teruskan ke Kasir"**.

### B. Penjualan Obat Bebas Langsung / Non-Resep (`/apotek/penjualan`)
1. Buka menu **FARMASI & APOTEK** $\rightarrow$ **Penjualan Obat Bebas**.
2. Masukkan nama pembeli / pelanggan (opsional, default: "Pelanggan Umum") dan nomor WhatsApp.
3. Pilih metode pembayaran (Tunai, QRIS, Debit, Transfer).
4. Klik **"+ Tambah Item Obat"**:
   - Pilih nama obat dari daftar (menampilkan live sisa stok).
   - Sistem otomatis memilih batch dengan masa kadaluarsa terdekat (*FEFO*).
   - Masukkan jumlah (qty) dan diskon (jika ada).
   - Masukkan aturan pakai singkat (opsional).
5. Masukkan jumlah uang bayar yang diterima $\rightarrow$ Kembalian otomatis terkalkulasi.
6. Klik **"Selesaikan Transaksi & Terbitkan Nota"**.
7. Klik tombol **"Cetak Struk / Nota Penjualan"** untuk mencetak struk kasir resmi bagi pembeli.

---

## 5. Alur 4: Stock Opname & Laporan Farmasi

### A. Manajemen Master & Batch Obat (`/apotek/stok`)
- **Tambah Obat Baru**: Masukkan Kode, Nama, Golongan (Bebas/Keras), Satuan Eceran (Tablet/Kapsul/Botol), dan Harga Jual per 1 Butir.
- **Input Penerimaan Batch**: Masukkan nomor batch pabrikan, tanggal kadaluarsa, harga beli (HPP), dan jumlah stok masuk.
- **Edit & Hapus**: Tersedia tombol Edit dan Hapus pada setiap baris obat dan batch obat.

### B. Stock Opname Fisik Obat (`/apotek/opname`)
1. Buka formulir lembar hitung fisik obat.
2. Masukkan jumlah stok fisik riil di rak/gudang obat.
3. Selisih otomatis terkalkulasi (Cocok / Lebih / Kurang).
4. Klik **"Simpan & Terapkan Penyesuaian Stok Fisik"**.
5. Buka Tab 2 untuk melihat riwayat dan **Cetak Berita Acara Stock Opname**.

### C. Laporan & Analitik Farmasi (`/apotek/laporan`)
- **Laporan Pemakaian & Penjualan Obat**: Rincian obat keluar per resep beserta nilai nominal omset farmasi.
- **Buku Mutasi & Kartu Stok**: Audit trail lengkap pergerakan stok masuk, resep keluar, opname, dan retur.
- **Monitoring Kadaluarsa (FEFO)**: Deteksi dini obat yang mendekati masa expired ($\le 30$ hari / $\le 90$ hari).
- **Top 10 Obat Fast-Moving**: Analisis ranking obat paling sering diresepkan untuk acuan pengadaan bulanan (PO).
- **Cetak Laporan**: Seluruh laporan siap dicetak/diekspor ke format cetak resmi.

---

## 6. Alur 5: Pembayaran Kasir & Cetak Kwitansi

1. Buka menu **KEUANGAN & KASIR** $\rightarrow$ **Kasir Pembayaran** (`/keuangan/kasir`).
2. Klik tagihan pasien pada antrean kasir.
3. Sistem menggabungkan seluruh tagihan secara otomatis:
   - Jasa Medis / Tindakan Dokter
   - Obat / Farmasi
   - Pesanan Restoran POS (jika ada)
4. Masukkan diskon (jika ada), pilih metode pembayaran (Tunai, Debit, Transfer, QRIS), dan masukkan jumlah uang bayar.
5. Klik **"Selesaikan Transaksi & Terbitkan Kwitansi"**.
6. **Cetak Kwitansi Pembayaran**:
   - Klik tombol **"Cetak Kwitansi Pembayaran"** pada banner sukses atau Tab **Riwayat Pembayaran (Lunas)**.
   - Lembar kwitansi resmi siap dicetak/PDF dengan tanda tangan kasir.

---

## 7. Alur 6: Manajemen Master Sistem

- **Master Poliklinik, Dokter & Tindakan (`/system/master-klinik`)**:
  - Mengelola data poli, dokter spesialis, tarif tindakan medis (induk & sub-tindakan), dan skema bagi hasil komisi dokter.
- **Master Kode ICD (`/system/master-icd`)**:
  - Mengelola katalog kode diagnosa penyakit ICD-10 dan prosedur medis ICD-9-CM.
- **Master Metode Pembayaran (`/system/master-pembayaran`)**:
  - Mengelola opsi kanal pembayaran klinik & farmasi (Tunai, QRIS, Bank BCA/BRI/Mandiri, Debit EDC, BPJS, Asuransi).
  - Mengatur nomor rekening/NMID, atas nama akun, dan toggle status aktif/nonaktif.
- **Pengguna & Hak Akses (`/system/users`)**:
  - Mengelola akun staf dan hak akses modul per departemen.

---

## 8. Alur 7: Surat Keterangan Medis & Hasil Laboratorium

### A. Penerbitan Surat Keterangan Sakit / Sehat / Rujukan (`/klinik/surat`)
1. Buka menu **PELAYANAN KLINIK** $\rightarrow$ **Surat Medis & Rujukan**.
2. Klik tombol **"Terbitkan Surat Baru"**.
3. Pilih kunjungan pasien terkait $\rightarrow$ Pilih jenis surat:
   - **Surat Sakit (SKS)**: Masukkan lama istirahat (hari), tanggal mulai/selesai, dan diagnosa.
   - **Surat Sehat (SKD)**: Masukkan tekanan darah, BB/TB, hasil tes buta warna, dan status kesehatan.
   - **Surat Rujukan (SRP)**: Masukkan nama RS tujuan, poli rujukan, dan alasan rujukan.
4. Klik **"Terbitkan & Simpan Surat"**.
5. Klik tombol **"Cetak"** untuk mencetak surat resmi ber-kop klinik.

### B. Pemeriksaan & Hasil Laboratorium (`/klinik/lab`)
1. Buka menu **PELAYANAN KLINIK** $\rightarrow$ **Hasil Laboratorium**.
2. Klik **"Input Hasil Tes Lab Baru"** $\rightarrow$ Pilih kunjungan pasien dan jenis tes (GDS, Asam Urat, Kolesterol, Hb, Urin Rutin, dll).
3. Masukkan angka hasil pengukuran. Sistem otomatis mengisi nilai rujukan normal.
4. Pilih status evaluasi (*Normal / High / Low*).
5. Klik **"Simpan Hasil Laboratorium"** $\rightarrow$ Klik **"Cetak"** untuk lembar hasil resmi.

---

## 9. Alur 8: Display Antrean TV & Laporan Morbiditas Dinkes

### A. Layar Display Antrean Ruang Tunggu (`/klinik/display`)
- Buka tautan `/klinik/display` pada layar TV monitor ruang tunggu klinik.
- Layar otomatis menampilkan nomor antrean yang sedang dipanggil per poliklinik, jam digital WITA, dan teks berjalan (*running text*).
- Layar otomatis me-refresh status antrean secara langsung.

### B. Laporan 10 Besar Morbiditas & Kunjungan (`/klinik/laporan`)
- Buka menu **PELAYANAN KLINIK** $\rightarrow$ **Laporan Morbiditas**.
- Atur rentang tanggal periode laporan.
- Sistem menyajikan **Ranking 10 Besar Penyakit Terbanyak (Top 10 ICD-10)** dan statistik demografi kunjungan pasien.
- Klik **"Cetak Laporan 10 Morbiditas (Dinkes)"** untuk mencetak format laporan resmi bagi Dinas Kesehatan.
