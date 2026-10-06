# 🚀 STANDAR PENGEMBANGAN, DEPLOYMENT & ANTI-FRAUD SAWA MAWA ERP

Aturan ini menjadi pedoman permanen untuk seluruh proses coding, testing, dan deployment di repositori ini.

---

## 1. 🛑 ATURAN PENGELOLAAN GIT (KOMITMEN MUTLAK)
- **DILARANG MELAKUKAN `git commit` ATAU `git push` OTOMATIS.**
- AI harus menyiapkan seluruh perubahan, memverifikasi linter, lalu memberikan blok perintah `git add`, `git commit -m "..."`, dan `git push origin main` agar **dieksekusi secara mandiri oleh USER**.

---

## 2. 🔄 POLA ATOMIC UPDATE (KODE & DATABASE SYNC)
Setiap kali ada penambahan atau modifikasi tabel/kolom:
1. **Seeder / Migration Aman (*Idempotent*)**: Gunakan `IF NOT EXISTS` atau pengecekan sebelum membuat tabel/kolom agar aman dijalankan berulang kali.
2. **Auto-hook ke Web UI**: Masukkan pemanggilan seeder baru ke dalam metode `System::syncData()` di `app/Controllers/System.php`.
3. **Berikan 1 Baris Perintah Terminal Hosting**:
   ```bash
   cd /home/n1579664/public_html/appsdemo-albiandra-project.my.id && git pull origin main && php spark db:seed <NamaSeeder>
   ```

---

## 3. 🛡️ PRINSIP ANTI-FRAUD & REVERSAL ENGINE
- **Hard Delete Dilarang untuk Transaksi Operasional**: Transaksi yang dibatalkan tidak boleh dihapus dari database.
- **Reversing Journal Double-Entry**: Selalu buat jurnal pembalik seimbang 100% (`JV-VOID-...`) yang menukar posisi Debit dan Kredit transaksi asal.
- **Restorasi Stok Batch (FIFO/FEFO)**: Kembalikan kuantitas obat ke Nomor Batch & Expired Date asal dengan tipe mutasi `void_in`.
- **Otorisasi Supervisor**: Pembatalan wajib meminta verifikasi PIN 6 Digit Supervisor dengan sistem *Lockout* 15 menit jika 5x salah input.
- **Berita Acara Resmi**: Sediakan pencetakan Berita Acara Pembatalan Transaksi resmi (PDF).

---

## 4. 🧪 PEMERIKSAAN SINTAKSIS & INTEGRITAS SEBELUM DISERAHKAN
- Selalu uji sintaksis seluruh file yang diubah menggunakan PHP CLI XAMPP:
  ```bash
  C:\xampp\php\php.exe -l <filepath>
  ```
- Jalankan test runner `php spark test:void-integrity` untuk memastikan database, rute, dan engine bebas dari error.
