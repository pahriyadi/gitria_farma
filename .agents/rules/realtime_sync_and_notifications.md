# Arsitektur Zero-Reload Live-Sync & Real-Time Notification Multi-Browser

Pedoman ini mengatur standar implementasi sistem sinkronisasi realtime, polling antar-browser, alur operasional terpadu (Klinik $\rightarrow$ Kasir $\rightarrow$ Apotek $\rightarrow$ Display TV), panduan suara pasien, dan sistem notifikasi terpusat pada aplikasi CodeIgniter 4 dan JavaScript murni.

---

## 1. Prinsip Zero-Reload (Dilarang Menggunakan `window.location.reload()`)
- **Larangan**: Jangan pernah menyematkan `window.location.reload()` di dalam fungsi penangkap notifikasi atau *background polling* rutin. Reload paksa akan mereset form input aktif pengguna, memicu kedipan layar, dan merusak *user experience*.
- **Implementasi yang Benar**:
  - Gunakan pembaruan DOM murni via JavaScript (`LiveSyncEngine`) dengan membandingkan hash data (`lastPayloads`).
  - Bila data berubah, perbarui hanya elemen target (`#live-antrean-body`, `#queue-list`, `#presc-list`, `#bill-list`) atau gunakan API `DataTable.ajax.reload(null, false)`.
  - Simpan state elemen yang sedang aktif (misalnya pasien yang sedang dipilih dokter di SOAP atau apoteker di e-Resep) agar form kerja tidak terhapus saat ada sinkronisasi baris baru.

---

## 2. Standar Alur Operasional Terpadu: *Pay-First Workflow* (Bayar di Kasir Dahulu Sebelum Penyerahan Obat)
1. **Fase Dokter (SOAP & e-Resep)**:
   - Input resep obat pada saat dokter menyelesaikan SOAP otomatis memperbarui tagihan kasir menjadi berstatus **`open`** (menggabungkan rincian tindakan medis/jasa poli + obat-obatan).
   - e-Resep otomatis masuk ke daftar antrean Apotek berstatus `waiting` dengan indikator badge **`🟡 Belum Bayar di Kasir`**.
   - Pasien diarahkan terlebih dahulu ke **Kasir Utama** untuk pelunasan tagihan.
2. **Fase Apotek (Penguncian Tombol & Proteksi Dispensing)**:
   - **Jika Tagihan Belum Lunas**:
     - Ditampilkan banner peringatan: `🔒 MENUNGGU PELUNASAN KASIR UTAMA (OBAT TERKUNCI)`.
     - Tombol **"Selesai Dispensing & Serahkan Obat"** dalam kondisi **DISABLED / TERKUNCI** (warna abu-abu, kursor terkunci).
     - Apoteker tetap dapat melihat rincian obat untuk mulai mempersiapkan atau meracik obat di awal.
   - **Jika Tagihan Sudah Lunas**:
     - Ditampilkan banner konfirmasi: `✅ PEMBAYARAN SUDAH LUNAS DI KASIR UTAMA (Metode: Tunai/QRIS/Transfer)`.
     - Tombol **"Selesai Dispensing & Serahkan Obat"** otomatis **AKTIF** (warna teal).
3. **Fase Kasir (Pelunasan & Notifikasi Siap Diserahkan)**:
   - Pelunasan tagihan mengubah `billing_transactions.status = 'paid'`, mencatat transaksi kas dan kwitansi.
   - Jika terdapat e-resep yang menunggu, status kunjungan diset ke `'prescription'`.
   - Kasir otomatis mengirimkan broadcast notifikasi operasional ke role `apoteker,farmasi`: *"Resep Siap Disiapkan di Apotek (Tagihan Pasien Telah Lunas di Kasir)"*.
4. **Fase Penyerahan Obat & Penyelesaian Penuh (Full Completion)**:
   - Saat apoteker menekan "Selesai Dispensing & Serahkan Obat", `PharmacyService::processPrescription` memotong stok batch FEFO dan menyelesaikan status resep (`status = 'completed'`).
   - **ATURAN WAJIB**: Status tagihan di `billing_transactions` **HARUS TETAP DIPERTAHANKAN `paid` (LUNAS)** dan **DILARANG DI-RESET KEMBALI KE `open`**, agar tagihan tidak pernah masuk lagi ke meja Kasir Utama.
   - Status kunjungan pasien diselesaikan penuh (`patient_visits.status = 'completed'`).

---

## 3. Katalog & Spesifikasi Endpoint Real-Time LiveSync API (`api/sync/*`)
Semua endpoint berkecepatan tinggi ini didaftarkan di `app/Config/Routes.php` dalam grup `api/sync` dan dipantau oleh `live-sync-engine.js`:
- `GET api/sync/klinik-queue`: Sinkronisasi antrean pendaftaran, poli tindakan, rekam medis SOAP, dan TV display.
- `GET api/sync/pharmacy-prescriptions`: Sinkronisasi antrean resep farmasi, deteksi pelunasan kasir (`is_paid`, `billing_status`), dan batch stok FEFO.
- `GET api/sync/cashier-billings`: Sinkronisasi tagihan aktif siap bayar di Kasir Utama.
- `GET api/sync/resto-kitchen`: Sinkronisasi pesanan dapur gizi Resto KDS.
- `GET api/sync/hrd-presence`: Sinkronisasi presensi & absensi pegawai.
- `GET api/sync/lab-queue`: Sinkronisasi antrean pemeriksaan sampel & input hasil lab.

---

## 4. Standar *Cache Busting Versioning* Berkas JavaScript Dinamis
Untuk mencegah cache browser menahan versi JavaScript lama saat aplikasi diperbarui:
- Seluruh skrip dinamis di `app/Views/layouts/layout.php` **WAJIB** dipanggil dengan query string berbasis timestamp modifikasi file:
  ```php
  <!-- Voice Caller & Audio Synthesizer -->
  <script src="<?= base_url('assets/js/voice_caller.js?v=' . (file_exists(FCPATH . 'assets/js/voice_caller.js') ? filemtime(FCPATH . 'assets/js/voice_caller.js') : time())) ?>"></script>
  <!-- Universal Zero-Reload Real-Time LiveSync Engine -->
  <script>const BASE_URL = '<?= base_url() ?>';</script>
  <script src="<?= base_url('assets/js/live-sync-engine.js?v=' . (file_exists(FCPATH . 'assets/js/live-sync-engine.js') ? filemtime(FCPATH . 'assets/js/live-sync-engine.js') : time())) ?>"></script>
  ```

---

## 5. Pengecekan Nilai Boolean / Status Multitipe pada UI JavaScript
Hindari validasi kaku bertipe tunggal seperti `attr('data-is-paid') === '1'`. Selalu gunakan pemeriksaan multi-kondisi yang mencakup atribut DOM, jQuery data cache, dan string status:
```javascript
const isPaid = (
    attrPaid === '1' || attrPaid === 1 || attrPaid === 'true' || attrPaid === true ||
    attrStatus === 'paid' || dataPaid === 1 || dataPaid === '1' ||
    dataPaid === true || dataPaid === 'true' || dataStatus === 'paid'
);
```

---

## 6. Keselarasan Zona Waktu Server, PHP, dan MySQL
- **Standar**: Pastikan timezone aplikasi di CodeIgniter disetel sama persis dengan zona waktu operasional lokal klinik dan MySQL server (misal: `Asia/Makassar` untuk WITA Sumbawa).
- **Konfigurasi Wajib**:
  - Di `app/Config/App.php`: `public string $appTimezone = 'Asia/Makassar';`
  - Di `.env`: `app.appTimezone = 'Asia/Makassar'`
- **Pentingnya**: Kueri seperti `WHERE DATE(created_at) = date('Y-m-d')` akan mengembalikan 0 baris jika terjadi *timezone mismatch* antara PHP (UTC) dan database lokal (WITA).

---

## 7. Penanganan Form & AJAX Token CSRF Dinamis
- **Standar**: Setiap form atau permintaan AJAX yang dieksekusi secara dinamis via JavaScript ke dalam DOM harus memiliki token CSRF yang valid.
- **Pemasangan di Layout**:
  - Pasang tag meta di `<head>`:
    ```html
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    ```
  - Pada request AJAX:
    ```javascript
    const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
    const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    const postData = { action: 'delete_queue', queue_id: id, is_ajax: '1' };
    postData[csrfName] = csrfHash;
    ```
  - Sediakan fallback berupa link aksi langsung yang aman (seperti `klinik/antrean/delete/ID`) dengan konfirmasi dialog.

---

## 8. Pencegahan Penimpaan Kolom ID & Format Raw SQL `orderBy`
- **Aliasing Kolom JOIN**: Saat melakukan `JOIN` tabel (misal: `queue_numbers` dengan `patients`, `doctors`, `polikliniks`), jangan gunakan wildcard `SELECT queue_numbers.*` yang rawan tertimpa oleh `patients.id`.
- **Aturan CI4 Query Builder `orderBy` pada Fungsi Kompleks**:
  - Jika `orderBy` mengandung fungsi berkoma seperti `COALESCE(...)` atau `CASE WHEN`, **WAJIB** sertakan parameter `$escape = false`:
    ```php
    ->orderBy("COALESCE(qn.updated_at, pv.updated_at, pv.created_at) DESC", '', false)
    ```
  - Jangan gunakan `->orderBy("COALESCE(a, b)", "DESC")` karena CI4 Query Builder akan salah memecah string koma dan menyisipkan keyword `DESC` ke dalam parameter fungsi (`COALESCE(a DESC, b) DESC`).

---

## 9. Penghapusan Bertingkat Transaksi yang Bersih (Cascading Deletion)
- **Standar**: Saat membatalkan antrean atau kunjungan pasien, seluruh transaksi turunan harus dibersihkan secara sistematis dalam satu siklus:
  1. Ambil metadata pasien (Nama, No. Kunjungan, No. Antrean) terlebih dahulu untuk kebutuhan notifikasi.
  2. Nonaktifkan foreign key checks sementara: `$db->query("SET FOREIGN_KEY_CHECKS = 0;");`
  3. Hapus data secara bertingkat pada skema tabel yang valid:
     - `cash_transactions` & `fee_transactions`
     - `billing_details` & `billing_transactions`
     - `lab_results`
     - `medical_letters`
     - `medical_records` & `triage_records`
     - `prescription_details` & `prescriptions`
     - `queue_numbers` & `patient_visits`
  4. Aktifkan kembali foreign key checks: `$db->query("SET FOREIGN_KEY_CHECKS = 1;");`
  5. Kirimkan siaran notifikasi pembatalan (*broadcast notification*) bertipe `danger` agar seluruh browser menerima alert pembaruan.

---

## 10. Arsitektur Broadcasting Notifikasi & Audio Chime
- **Broadcast Siaran**: Untuk peristiwa bersama (pendaftaran pasien baru, antrean dibatalkan), set `'target_roles' => null` pada `NotificationService::send()`.
- **Visual & Audio**:
  - Render kartu toast mengambang di pojok kanan atas dengan auto-dismiss 6 detik.
  - Putar audio alert lembut menggunakan Web Audio API synthesis oscillator atau file audio lonceng klinik.
  - Catat ID notifikasi yang sudah diputar di `sessionStorage['seen_notif_ids']` untuk mencegah bunyi berulang saat polling berikutnya.

---

## 11. Standar Sinkronisasi Layar Display TV (`/klinik/display`)
- **Hero Calling Card**:
  - **Sinkronisasi Multi-Layanan**:
    - Panggilan Dokter/Poli: Menampilkan visual berkedip emas (`is-calling`), badge `SEDANG DIPANGGIL`, nomor antrean besar, nama pasien, dan tujuan ruang poliklinik.
    - Panggilan ke Kasir: Menampilkan badge `MENUJU KASIR` dengan instruksi *"Silakan menuju ke Kasir Pembayaran"*.
    - Panggilan ke Apotek: Menampilkan badge `AMBIL OBAT DI APOTEK` dengan instruksi *"Silakan menuju ke Loket Farmasi dan Apotek"*.
    - Panggilan Selesai: Menampilkan badge `PELAYANAN SELESAI` dengan instruksi *"Penyerahan obat telah selesai. Semoga lekas sembuh"*.
  - Saat semua antrean selesai/batal, kartu hero dan kartu grid poli seketika kembali ke status `SISTEM ANTREAN STANDBY`.
- **Polyclinic Cards**:
  - Filter keluar antrean berstatus `cancelled`, `completed`, `prescription`, `cashier`, `pharmacy`, `done`, `no-show` dari kartu poliklinik agar tampilan TV selalu bersih dan akurat.
- **Audio Broadcaster Ruang Tunggu**:
  - TV Display menjalankan `window.VCM.startServerPolling(2000)` untuk siar pengeras suara (*loudspeaker*) berurutan secara otomatis (FIFO) dengan jeda hening *anti-collision* 850ms.

---

## 12. Standar Panduan Suara Terpadu Perjalanan Pasien (*Unified Voice Guidance System*)
Sistem panduan suara menggunakan **nomor antrean yang sama** sepanjang alur pelayanan klinik, kasir, hingga penyerahan obat di apotek secara berurutan dan teratur:

### A. Rangkaian Suara Panggilan & Kalimat Standar
| Tahap | Fungsi Pemanggil di `voice_caller.js` | Kalimat Suara Bahasa Indonesia |
|---|---|---|
| **1. Dokter Selesai SOAP** | `window.VCM.callToCashier(queueNo, patientName)` | *"Nomor antrean [A-001], atas nama [Nama Pasien], pemeriksaan dokter telah selesai. Silakan menuju ke Kasir Pembayaran untuk administrasi. Terima kasih."* |
| **2. Kasir Selesai Pelunasan** | `window.VCM.callToPharmacy(queueNo, patientName)` | *"Nomor antrean [A-001], atas nama [Nama Pasien], transaksi pembayaran telah selesai. Terima kasih. Silakan menuju ke Loket Farmasi dan Apotek untuk pengambilan obat."* |
| **3. Apotek Selesai Serah Obat** | `window.VCM.callCompleted(queueNo, patientName)` | *"Nomor antrean [A-001], atas nama [Nama Pasien], penyerahan obat telah selesai. Terima kasih banyak atas kunjungan Anda di Sawamawa Medical Center, semoga lekas sembuh."* |

### B. Pola Pemicu Suara Flashdata Controller & View
Untuk memicu suara otomatis secara aman pasca submit data tanpa reload paksa:
1. **Di Controller** (`Klinik.php`, `Keuangan.php`, `Apotek.php`):
   ```php
   session()->setFlashdata('voice_trigger', [
       'action'       => 'to_cashier', // 'to_pharmacy' | 'to_completed'
       'queue_number' => $queueNumber,
       'patient_name' => $patientName
   ]);
   ```
2. **Di View** (`soap.php`, `kasir.php`, `resep.php`):
   ```javascript
   <?php if (session()->getFlashdata('voice_trigger')): 
       $vTrigger = session()->getFlashdata('voice_trigger');
       if (($vTrigger['action'] ?? '') === 'to_cashier'):
   ?>
       setTimeout(function() {
           var qNum = '<?= esc($vTrigger['queue_number'] ?? 'A-001') ?>';
           var pName = '<?= esc($vTrigger['patient_name'] ?? 'Pasien') ?>';
           if (window.VCM && typeof window.VCM.callToCashier === 'function') {
               window.VCM.callToCashier(qNum, pName);
           } else if (typeof callPatientToCashier === 'function') {
               callPatientToCashier(qNum, pName);
           }
       }, 600);
   <?php endif; endif; ?>
   ```

### C. Proteksi Sinkronisasi Status Kunjungan di `QueueCallService`
- Saat memicu event suara (`triggerCall`), method `syncVisitQueueStatus` **wajib memperhatikan parameter `$serviceType`**:
  - Arah Kasir (`service_type = 'kasir'`): `patient_visits.status = 'cashier'` & `queue_numbers.status = 'completed'`.
  - Arah Farmasi (`service_type = 'farmasi'`): `patient_visits.status = 'prescription'` & `queue_numbers.status = 'completed'`.
  - Selesai Obat / Tuntas (`call_action = 'completed'`): `patient_visits.status = 'completed'` & `queue_numbers.status = 'completed'`.
- **Dilarang menimpa** status kunjungan yang sudah di kasir/apotek/selesai kembali menjadi `examining` atau `called`.

### D. Standar Engine Audio (*Hospital-Grade Voice Engine*)
- **Hospital Chime (Dua Nada)**: Menggunakan Web Audio API oscillator nada E5 (659.25 Hz) $\rightarrow$ C5 (523.25 Hz) dengan jeda 850ms sebelum ucapan dimulai, dilengkapi *instant fallback* jika `AudioContext` ditangguhkan browser.
- **Speech Synthesis (id-ID)**: Menggunakan suara `id-ID` prioritas (Google Bahasa Indonesia / Microsoft Gadis/Andika) dengan tempo `rate = 0.85` yang tenang, sopan, dan jelas.
- **Pencegahan Bug Garbage Collector**: Instance `SpeechSynthesisUtterance` wajib di-assign ke variabel global `window._activeSpeechUtterance` dan `window._vcmActiveUtt` agar browser Chromium/Edge tidak memotong suara di tengah jalan.
- **Pelafalan Angka Alami**: Kode antrean (seperti `A-012` atau `B-105`) selalu dilafalkan menggunakan konversi terbilang alami (*"A, dua belas"*, *"B, seratus lima"*).

---

## 13. Standar Ketahanan Offline & Auto-Sync Transaksi Draf Lokal (*Offline Resilience & FIFO Auto-Sync*)
Ketika terjadi pemadaman listrik, server down, atau koneksi internet terputus, seluruh aktivitas input form tetap dapat berjalan normal tanpa kehilangan data melalui `public/assets/js/offline-sync-engine.js`:

1. **Deteksi Status Jaringan Real-Time**:
   - `offline-sync-engine.js` mendeteksi event browser `online`/`offline` serta melakukan heartbeat ping ke `GET api/sync/ping` setiap 6 detik.
   - Status ditampilkan di Navbar: 🟢 `Online`, 🔴 `Offline (Draf Aktif)`, 🟡 `Menyinkronkan...`.
   - Banner darurat sticky muncul di atas layar untuk memberi tahu pengguna bahwa mode draf sedang aktif.
2. **Pencegatan Submit & Penyimpanan Draf Lokal**:
   - Form submission (POST) yang dilakukan saat koneksi offline dicegat (`intercept`) secara otomatis.
   - Data payload disimpan ke dalam penyimpanan klien **IndexedDB (`SawamawaOfflineDB`)** dengan fallback ke `localStorage`.
   - Audio chime offline dibunyikan dan notifikasi dialog tampil: *"Data Disimpan di Draf Lokal"*.
   - Pengguna dapat terus menginput transaksi berikutnya tanpa hambatan.
3. **Auto-Sync Otomatis Saat Jaringan Pulih (*Zero-Click Reconnection*)**:
   - Begitu koneksi online kembali, engine secara otomatis meminta token CSRF baru dari `GET api/sync/csrf-token`.
   - Seluruh antrean draf dikirimkan ke server secara berurutan (*FIFO Sequential Replay*).
   - Setelah sukses, draf dibersihkan dari memori browser, notifikasi ringkasan sukses ditampilkan, audio ceria 3-nada dimainkan, dan tampilan data disegarkan.
4. **Pusat Kelola Draf Offline**:
   - Pengguna dapat membuka modal **Pusat Draf Offline** dari Navbar untuk melihat daftar transaksi tertunda, melakukan sinkronisasi manual, atau membersihkan data draf.

