# 🚀 PANDUAN LENGKAP DEPLOYMENT & GO-LIVE HOSTING (cPanel)
**Sistem Informasi ERP Sawamawa Medical Center & Resto Gizi**
*Domain Target:* `http://appsdemo-albiandra-project.my.id/` (atau `https://appsdemo-albiandra-project.my.id/`)

---

## 📋 DAFTAR CHECKLIST SEBELUM & SAAT GO-LIVE

Berikut adalah 6 langkah praktis untuk meng-online-kan aplikasi ke hosting cPanel:

---

### LANGKAH 1: Persiapan File & Kompresi ZIP di Lokal

1. Pastikan seluruh perubahan kode sudah tersimpan.
2. Di folder project `c:\xampp\htdocs\sawamawamedicalcenter.id`, pilih seluruh file dan folder, lalu kompresi menjadi **`sawamawa_source.zip`**.
   * *Folder yang wajib masuk:* `app`, `public`, `system`, `writable`, `vendor`, `.env`, `.htaccess`, `spark`.

---

### LANGKAH 2: Upload & Ekstrak di File Manager cPanel

Ada **2 Metode Struktur Folder** di cPanel:

#### 🌟 METODE A (Paling Mudah & Praktis — Menggunakan Root `.htaccess`):
1. Buka **cPanel > File Manager > `public_html/`**.
2. Upload file `sawamawa_source.zip` ke dalam `public_html/`.
3. Klik kanan pada file zip lalu pilih **Extract**.
4. Root `.htaccess` yang telah disediakan akan **secara otomatis mengarahkan pengunjung ke folder `/public`** dan **memblokir akses luar ke file `.env` / folder `app`**.

#### 🔒 METODE B (Paling Aman Standar Enterprise — Memisahkan Core & Public):
1. Di root cPanel (`/home/username/`), buat folder baru bernama `sawamawa_core`.
2. Ekstrak seluruh folder `app`, `system`, `vendor`, `writable`, `.env` ke dalam `/home/username/sawamawa_core/`.
3. Pindahkan **isi folder `public/`** (yaitu `index.php`, `.htaccess`, `assets/`, `dist/`, `plugins/`) ke dalam `public_html/`.
4. Buka file `public_html/index.php` menggunakan Code Editor cPanel, lalu ubah baris:
   ```php
   require FCPATH . '../app/Config/Paths.php';
   // Ubah menjadi:
   require FCPATH . '../sawamawa_core/app/Config/Paths.php';
   ```

---

### LANGKAH 3: Pembuatan & Import Database MySQL di cPanel

1. Buka **cPanel > MySQL® Databases**:
   - Buat Database baru (contoh: `u12345_sawamawa`).
   - Buat User Database baru (contoh: `u12345_user`) & Password yang kuat.
   - Tambahkan User ke Database (*Add User To Database*) dan centang **ALL PRIVILEGES**.
2. Buka **cPanel > phpMyAdmin**:
   - Pilih database yang baru dibuat (`u12345_sawamawa`).
   - Klik menu **Import**, lalu pilih file SQL dump terbaru yang sudah digenerate dari:
     `writable/backups/backup_sawamawa_20260826_190017.sql` (atau export dari phpMyAdmin lokal).
   - Klik tombol **Go / Kirim** hingga proses import 44 tabel selesai (100% sukses).

---

### LANGKAH 4: Konfigurasi File `.env` di Hosting

Buka file **`.env`** di hosting (pastikan fitur *Show Hidden Files / Dotfiles* aktif di File Manager cPanel), lalu sesuaikan konfigurasi berikut:

```ini
#--------------------------------------------------------------------
# SAWAMAWA MEDICAL CENTER - PRODUCTION ENVIRONMENT
#--------------------------------------------------------------------

# 1. Ubah Environment ke Production (Menyembunyikan Debug Toolbar & Mengamankan Error Stack)
CI_ENVIRONMENT = production

# 2. Sesuaikan Base URL dengan Domain Anda (Gunakan https jika SSL aktif)
app.baseURL = 'http://appsdemo-albiandra-project.my.id/'
# Jika sudah pasang SSL (HTTPS), gunakan:
# app.baseURL = 'https://appsdemo-albiandra-project.my.id/'

app.indexPage = ''

# 3. Kredensial Database cPanel Anda
database.default.hostname = localhost
database.default.database = u12345_sawamawa
database.default.username = u12345_user
database.default.password = PasswordDatabaseHostingAnda!
database.default.DBDriver = MySQLi
database.default.port     = 3306
database.default.charset  = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
```

---

### LANGKAH 5: Pengaturan Versi PHP & Ekstensi di cPanel

1. Buka **cPanel > Select PHP Version** atau **MultiPHP Manager**:
   - Pilih versi **PHP 8.1** atau **PHP 8.2** (Rekomendasi).
2. Di menu **PHP Extensions**, pastikan ekstensi berikut dicentang/aktif:
   - ✅ `intl` (Wajib untuk format tanggal & currency CodeIgniter 4)
   - ✅ `mbstring` (Wajib untuk manipulasi string multibyte)
   - ✅ `curl` (Wajib untuk integrasi API WhatsApp Gateway & SATUSEHAT)
   - ✅ `gd` (Wajib untuk pemrosesan gambar, avatar & QR barcode)
   - ✅ `mysqlnd` / `pdo_mysql` (Koneksi database)
   - ✅ `json`, `xml`, `zip`

---

### LANGKAH 6: Pengaturan Hak Akses (Permissions) Folder `writable/`

Pastikan folder `writable/` dan seluruh sub-foldernya dapat ditulisi oleh sistem:
- Folder **`writable/`** $\rightarrow$ Permission **`0755`** (atau `0775`)
- Subfolder `writable/cache/`, `writable/logs/`, `writable/session/`, `writable/uploads/`, `writable/backups/` $\rightarrow$ Permission **`0755`**.

---

### ⏰ OPSIONAL: Pengaturan Cron Jobs Otomatis di cPanel

Agar fitur otomatisasi berjalan di latar belakang (pemindaian obat kadaluarsa, penyusutan aset, dan backup database harian), buka **cPanel > Cron Jobs**, lalu tambahkan jadwal berikut:

| Nama Tugas Cron | Jadwal Waktu | Perintah Command di cPanel |
|:---|:---|:---|
| **Scan Obat Expired (FEFO)** | Setiap Hari jam 06:00 | `/usr/local/bin/php /home/username/public_html/spark cron:check-expired-medicines >/dev/null 2>&1` |
| **Tutup Buku Kasir Harian** | Setiap Hari jam 23:30 | `/usr/local/bin/php /home/username/public_html/spark cron:daily-closing >/dev/null 2>&1` |
| **Backup Database Otomatis**| Setiap Minggu jam 01:00| `/usr/local/bin/php /home/username/public_html/spark cron:database-backup >/dev/null 2>&1` |

*(Ganti `/home/username/public_html/` sesuai path direktori akun cPanel Anda).*

---

### 🔑 Akun Login Default di Live Hosting:
* **URL Login:** `http://appsdemo-albiandra-project.my.id/login`
* **Username Super Admin:** `admin`
* **Password Default:** `admin123` (atau sesuai password yang Anda gunakan di lokal).

---
*Selamat! Sistem Sawamawa Medical Center & Resto Gizi Anda siap melayani pasien secara online dan terintegrasi.* 🏥✨
