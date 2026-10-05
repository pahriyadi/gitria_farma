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
2. Salin dan jalankan **1 baris perintah sakti ini**:

```bash
cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id && git pull origin main
```

3. ✨ **Selesai!** Seluruh file di website Anda akan langsung terupdate dengan versi terbaru dari GitHub.

---

## 🔄 ALUR KERJA HARIAN (DAILY WORKFLOW CHEAT-SHEET)

Setiap hari saat Anda mengembangkan website, alurnya sesederhana:

```text
[1. Edit / Koding di IDE]
       ↓
[2. Terminal Laptop: Commit & Push]
   git add . ; git commit -m "pesan update" ; git push origin main
       ↓
[3. Terminal Hosting: Pull Update]
   cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id && git pull origin main
       ↓
[4. Selesai! Refresh website di browser]
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
