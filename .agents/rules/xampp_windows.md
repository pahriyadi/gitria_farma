# Aturan Eksekusi Lingkungan XAMPP di Windows

## Latar Belakang
Pada sistem Windows yang menggunakan XAMPP, biner operasional (PHP, MySQL, Composer) sering kali tidak didaftarkan di variabel lingkungan PATH sistem secara global. Aturan ini memastikan agen dapat mengeksekusi perintah pengembangan tanpa terhambat oleh error program tidak ditemukan.

## Aturan Perilaku Agen
1. **Deteksi PATH Absolut**:
   - Jika direktori aktif berada di dalam `C:\xampp\htdocs\`, asumsikan sistem menggunakan XAMPP.
   - Gunakan path absolut `C:\xampp\php\php.exe` untuk semua perintah eksekusi PHP (misal: `C:\xampp\php\php.exe spark migrate`).
   - Gunakan path absolut `C:\xampp\mysql\bin\mysql.exe` untuk semua interaksi konsol MySQL (misal: `C:\xampp\mysql\bin\mysql.exe -u root -e "..."`).

2. **Eksekusi Composer Lokal**:
   - Jika perintah global `composer` tidak tersedia, jangan meminta pengguna menginstalnya secara manual.
   - Unduh biner `composer.phar` ke direktori root proyek menggunakan perintah:
     `Invoke-WebRequest -Uri "https://getcomposer.org/composer.phar" -OutFile "composer.phar"`
   - Jalankan seluruh perintah composer menggunakan PHP lokal:
     `C:\xampp\php\php.exe composer.phar <perintah>` (misal: `C:\xampp\php\php.exe composer.phar update`).
