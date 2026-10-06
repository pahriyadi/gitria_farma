# 📖 PANDUAN PRAKTIS GIT COMMIT, PUSH & DEPLOY KE HOSTINGER

Dokumentasi ini dibuat sebagai panduan langkah demi langkah bagi Anda untuk melakukan **Commit, Push ke GitHub, dan Deploy ke Server Hosting (Hostinger/cPanel)** secara mandiri dan mudah.

---

## 🚀 BAGIAN 1: CARA COMMIT & PUSH DARI TERMINAL LAPTOP/IDE

Setiap kali Anda atau AI selesai membuat perubahan, menambah fitur, atau memperbaiki bug pada kode aplikasi, ikuti alur kerja berikut:

### ⚡ Alur 3 Perintah Standar (Hafalkan 3 Baris Ini):

```powershell
# 1. Masukkan semua perubahan file
git add .

# 2. Simpan snapshot perubahan dengan pesan catatan
git commit -m "tulis pesan perubahan Anda di sini"

# 3. Kirim ke repositori GitHub
git push origin main
```

---

### 💡 Cara Cepat (Jalankan Sekaligus 1 Baris di PowerShell):

Cukup buka Terminal di IDE (tekan `Ctrl + ~`), salin baris ini, ganti pesannya, lalu tekan **Enter**:

```powershell
git add . ; git commit -m "update modul dan perbaikan tampilan" ; git push origin main
```

---

### 🔍 Perintah Tambahan yang Sangat Membantu:

| Perintah | Fungsi / Kapan Digunakan |
|---|---|
| `git status` | Melihat file apa saja yang baru dibuat, diedit, atau dihapus |
| `git log -n 5 --oneline` | Melihat 5 riwayat commit terakhir secara ringkas |
| `git diff` | Melihat baris kode mana yang berubah sebelum di-commit |

---

## 🌐 BAGIAN 2: CARA DEPLOY / UPDATE KE SERVER HOSTING

Setelah Anda berhasil melakukan `git push` ke GitHub, sekarang saatnya memperbarui file di server hosting.

### 🌟 Cara Utama: Menggunakan Terminal cPanel (Super Cepat - 3 Detik)

1. Buka cPanel hosting Anda → buka menu **Terminal**.
2. Salin dan jalankan **perintah komplit ini** (sekaligus meng-update file kodingan & memperbarui tabel database server):

```bash
cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id && git pull origin main && php spark db:seed VoidSystemSeeder
```

3. ✨ **Selesai!** Seluruh file kode dan struktur tabel database di hosting Anda langsung terupdate otomatis.

---

### 🗄️ BAGAIMANA CARA DATABASE SERVER IKUT BERUBAH?

Ada **3 Cara Praktis** untuk memastikan database di server hosting selalu up-to-date:

#### ✅ Cara 1: Sekali Jalan di Terminal cPanel (Paling Direkomendasikan)
Cukup jalankan perintah seeder/migration saat Anda melakukan `git pull`:
```bash
cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id
git pull origin main
php spark db:seed VoidSystemSeeder
```

#### ✅ Cara 2: Melalui Tombol Web UI (Tanpa Buka Terminal)
1. Login ke website hosting Anda sebagai Super Admin.
2. Buka menu **Administrasi Database** (atau dari Pusat Kontrol / Launchpad).
3. Klik tombol **"🔄 Sinkronisasi Data"** atau **"Paksa Pembaruan Sistem"**.
4. Sistem backend akan otomatis menjalankan *Seeder* dan memastikan seluruh tabel & kolom baru terbuat.

#### ✅ Cara 3: Menggunakan Spark Migrate
Jika ada file migration baru:
```bash
php spark migrate
```

---

## 🔄 ALUR KERJA HARIAN (DAILY WORKFLOW CHEAT-SHEET)

Setiap kali Anda selesai mengembangkan fitur baru:

```text
[1. Edit / Koding di IDE Laptop]
       ↓
[2. Terminal Laptop: Commit & Push]
   git add . ; git commit -m "update fitur" ; git push origin main
       ↓
[3. Terminal Hosting: Pull Update + Update Database]
   cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id && git pull origin main && php spark db:seed VoidSystemSeeder
       ↓
[4. Selesai! Website & Database langsung sinkron sempurna]
```

---

## 🛠️ BAGIAN 3: TROUBLESHOOTING & SOLUSI MASALAH UMUM

### 1. Pesan: *"error: Your local changes to the following files would be overwritten by merge"*
**Penyebab**: Ada file yang diedit langsung di cPanel File Manager yang bentrok dengan GitHub.
**Solusi**: Jalankan perintah pemaksaan sinkronisasi ini di Terminal cPanel:
```bash
cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id
git reset --hard origin/main
git pull origin main
```

### 2. Website Menampilkan *"Whoops! We seem to have hit a snag"*
**Penyebab**: Terjadi error database atau permission folder.
**Solusi**:
1. Buka `.env` di hosting, ubah `CI_ENVIRONMENT = development` untuk melihat letak error.
2. Periksa apakah ada tabel database baru yang belum diimpor ke phpMyAdmin.
3. Kembalikan `CI_ENVIRONMENT = production` setelah selesai.

---

## 🔒 CATATAN KEAMANAN PENTING
- File `.env` di hosting berisi password database asli dan **tidak boleh ditimpa** dengan template kosong.
- File `.gitignore` sudah dikonfigurasi untuk mencegah file password ter-upload ke GitHub publik.
