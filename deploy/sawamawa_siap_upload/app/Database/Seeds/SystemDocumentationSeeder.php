<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemDocumentationSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('system_documentations')->truncate();

        $docs = [
            // WORKFLOWS
            [
                'category'     => 'workflow',
                'title'        => 'Pelayanan Pasien Rawat Jalan & Rekam Medis (SOAP)',
                'target_role'  => 'Poliklinik & Medis',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-hospital-user',
                'flow_steps'   => json_encode([
                    ['title' => '1. Pendaftaran', 'sub' => 'Online / Loket FO', 'icon' => 'fas fa-mobile-screen-button', 'color' => 'text-teal'],
                    ['title' => '2. Triase Perawat', 'sub' => 'TTV & Keluhan', 'icon' => 'fas fa-heart-pulse', 'color' => 'text-danger'],
                    ['title' => '3. Periksa Dokter', 'sub' => 'SOAP, ICD & E-Resep', 'icon' => 'fas fa-user-doctor', 'color' => 'text-primary'],
                    ['title' => '4. Farmasi & Kasir', 'sub' => 'Obat & Billing', 'icon' => 'fas fa-receipt', 'color' => 'text-success']
                ]),
                'summary'      => 'Alur terpadu pelayanan rawat jalan mulai dari pendaftaran (mandiri online / loket), antrean display suara, triase TTV perawat, pemeriksaan dokter SOAP & ICD-10, telaah resep farmasi, hingga pelunasan di kasir.',
                'content'      => "1. **Pendaftaran Pasien:** Pasien mendaftar secara online melalui portal web atau didaftarkan oleh resepsionis di menu *Klinik > Pendaftaran Pasien*. Sistem otomatis menerbitkan No Kunjungan (`VS-XXXX`) dan No Antrean (`A-XXX`).\n2. **Triase Perawat:** Perawat memanggil antrean pasien melalui tombol suara otomatis di menu *Klinik > Antrean Poliklinik* dan menginput asesmen tanda vital (TTV).\n3. **Pemeriksaan Dokter (RME SOAP):** Dokter membuka menu *Klinik > Rekam Medis (SOAP)*, memilih diagnosa ICD-10, tindakan medis, serta meresepkan obat secara elektronik.\n4. **Farmasi & Kasir:** Resep otomatis muncul di antrean apotek untuk diracik, dan kasir langsung menarik seluruh rincian biaya untuk diterbitkan kwitansi pembayarannya.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Restoran Sehat, POS Meja & Kitchen Display System (KDS)',
                'target_role'  => 'Resto & Dapur Gizi',
                'badge_color'  => 'warning',
                'icon'         => 'fas fa-utensils',
                'flow_steps'   => json_encode([
                    ['title' => '1. Input POS Resto', 'sub' => 'Pilih Meja / Diet', 'icon' => 'fas fa-utensils', 'color' => 'text-warning'],
                    ['title' => '2. KDS Dapur Memasak', 'sub' => 'Layar Koki & Bel', 'icon' => 'fas fa-fire-burner', 'color' => 'text-danger'],
                    ['title' => '3. Pembayaran / Billed', 'sub' => 'Tunai / Kasir Medis', 'icon' => 'fas fa-cash-register', 'color' => 'text-success']
                ]),
                'summary'      => 'Alur pemesanan makanan/minuman sehat atau diet gizi pasien rawat jalan/inap terintegrasi dengan layar dapur koki dan kasir.',
                'content'      => "1. **Pemesanan POS:** Pelayan memilih nomor meja atau pasien diet medis di menu *Resto > POS Resto*.\n2. **KDS Dapur:** Layar dapur di menu *Resto > Dapur Gizi (KDS)* berbunyi dan menampilkan pesanan. Koki mengklik *Mulai Masak* lalu *Selesai Masak*.\n3. **Pembayaran:** Kasir menerima pembayaran tunai/QRIS atau memilih *Billed to Clinic* agar tagihan digabungkan ke kasir utama klinik.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Pengadaan Obat Farmasi & Multi-Batch Tracking',
                'target_role'  => 'Farmasi & Pengadaan',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-truck-ramp-box',
                'flow_steps'   => json_encode([
                    ['title' => '1. Permintaan (PR)', 'sub' => 'Stok Menipis', 'icon' => 'fas fa-clipboard-list', 'color' => 'text-info'],
                    ['title' => '2. Approval PO', 'sub' => 'Disetujui Direktur', 'icon' => 'fas fa-signature', 'color' => 'text-warning'],
                    ['title' => '3. Penerimaan (GR)', 'sub' => 'Batch & Exp Date', 'icon' => 'fas fa-truck-ramp-box', 'color' => 'text-teal'],
                    ['title' => '4. Stok & Jurnal', 'sub' => 'Auto Hutang PBF', 'icon' => 'fas fa-boxes-stacked', 'color' => 'text-success']
                ]),
                'summary'      => 'Alur pengadaan obat mulai dari Purchase Request (PR), verifikasi bertingkat approval Direktur, penerimaan barang (GR) dengan nomor batch & expired date, hingga pencatatan hutang dagang supplier.',
                'content'      => "1. **Purchase Request (PR):** Asisten apoteker mengajukan PR saat stok obat mencapai batas minimum.\n2. **Approval & PO:** Direktur/Manajemen menyetujui approval di menu *Pengadaan > Approval Request*, lalu PO diterbitkan ke Supplier PBF.\n3. **Penerimaan Barang (GR):** Saat obat tiba, gudang menginput Goods Receipt lengkap dengan Nomor Batch dan Tanggal Kedaluwarsa.\n4. **Update Stok & Jurnal:** Stok bertambah seketika dan Journal Engine mencatat jurnal hutang dagang.",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'workflow',
                'title'        => 'Akuntansi Otomatis & Cara Membaca Laporan Keuangan (Untuk Pemilik / Non-Akuntan)',
                'target_role'  => 'Pimpinan / Akuntansi',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-scale-balanced',
                'flow_steps'   => json_encode([
                    ['title' => '1. Saldo Awal', 'sub' => 'Uang Kas & Modal', 'icon' => 'fas fa-coins', 'color' => 'text-warning'],
                    ['title' => '2. Auto Journal', 'sub' => 'Otomatis dari Kasir', 'icon' => 'fas fa-receipt', 'color' => 'text-teal'],
                    ['title' => '3. Buku Besar', 'sub' => 'Mutasi per Rekening', 'icon' => 'fas fa-book-journal-whills', 'color' => 'text-primary'],
                    ['title' => '4. 4 Laporan BI', 'sub' => 'Untung/Rugi & Neraca', 'icon' => 'fas fa-chart-pie', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan praktis bahasa sederhana bagi pemilik atau staf non-akuntan untuk mengoperasikan pembukuan, mengecek laba/rugi, dan membaca neraca kekayaan klinik secara mudah.',
                'content'      => "### 💡 Mengapa Anda Tidak Perlu Pusing dengan Akuntansi?\nSistem Sawamawa telah dilengkapi **Otomatisasi Jurnal (Journal Engine)**. Anda **TIDAK PERLU** membuat jurnal debet/kredit secara manual! Setiap kali kasir menerima uang, apotek menjual obat, resto menerima pesanan, atau dokter melayani pasien, sistem **otomatis mencatat pembukuan akuntansi di latar belakang**.\n\n### 📖 Cara Mudah Membaca 4 Laporan Keuangan:\n1. **Laba Rugi Komprehensif (Menu: Akuntansi > Laporan Keuangan > Tab I):**\n   * Menjawab: *Berapa keuntungan bersih klinik bulan ini?*\n   * Rumus: `Total Pemasukan Pasien & Resto` - `Biaya Obat & Beban Gaji/Listrik` = **Laba Bersih**.\n2. **Posisi Keuangan / Neraca (Tab II):**\n   * Menjawab: *Berapa total kekayaan aset klinik saat ini?*\n   * Kolom Kiri (**Aset**): Uang Kas + Tabungan Bank + Nilai Stok Obat + Peralatan Medis/Gedung.\n   * Kolom Kanan (**Pasiva**): Modal Pemilik + Sisa Hutang ke Supplier Obat. Nilai kiri dan kanan **selalu sama (100% Balance)**.\n3. **Arus Kas (Tab III):**\n   * Menjawab: *Uang tunai fisik di kasir & bank bertambah atau berkurang?*\n4. **Analisa Omset Unit Bisnis (Tab IV):**\n   * Menjawab: *Unit usaha mana yang paling banyak menghasilkan uang?* (Poli Medis, Apotek, Resto, atau Lab).\n\n### ⚙️ Cara Memulai Saldo Awal (Menu: Akuntansi > Saldo Awal Sistem):\n1. Masukkan saldo uang riil di laci kasir dan tabungan bank klinik saat ini.\n2. Masukkan taksiran total nilai stok obat yang ada di rak apotek.\n3. Masukkan modal awal pribadi yang Anda gunakan mendirikan klinik.\n4. Klik **Simpan & Terapkan Saldo Awal Sistem** -> Selesai! Neraca otomatis seimbang.",
                'order_num'    => 4,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],

            // ROLE GUIDES
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Operasional Resepsionis / Front Office',
                'target_role'  => 'Pendaftaran / FO',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-hospital-user',
                'summary'      => 'Tata cara mendaftarkan pasien baru/lama, memproses pendaftaran online, dan mencetak kartu berobat ber-barcode.',
                'content'      => "1. Buka menu **Klinik > Pendaftaran Pasien**.\n2. Klik tombol **+ Pendaftaran Baru**.\n3. Untuk **Pasien Baru**: Isi biodata lengkap (NIK, Nama, Tanggal Lahir, Alamat, No HP) -> Pilih Poli & Dokter -> Klik *Simpan & Terbitkan Antrean*.\n4. Untuk **Pasien Lama**: Masukkan No RM / NIK pada kolom cari -> Pilih Dokter -> Klik *Daftarkan Kunjungan*.\n5. Pasien online otomatis muncul di daftar kunjungan hari ini tanpa perlu registrasi ulang.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Operasional Perawat (Triase & TTV)',
                'target_role'  => 'Perawat',
                'badge_color'  => 'danger',
                'icon'         => 'fas fa-user-nurse',
                'summary'      => 'Panduan pemanggilan antrean suara dan penginputan tanda-tanda vital pasien sebelum masuk ruang dokter.',
                'content'      => "1. Buka menu **Klinik > Antrean Poliklinik**.\n2. Klik tombol **Panggil Suara** untuk memanggil pasien ke ruang periksa.\n3. Klik tombol **Triase / Asesmen** pada baris pasien.\n4. Masukkan Tekanan Darah (Sistol/Diastol), Nadi, Suhu, Pernapasan, BB, dan TB -> Klik **Simpan Asesmen**.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Dokter Spesialis & Umum (RME SOAP)',
                'target_role'  => 'Dokter',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-user-doctor',
                'summary'      => 'Pengisian rekam medis SOAP, pencarian kode ICD-10/9, tindakan medis, e-prescribing, dan pembuatan surat rujukan.',
                'content'      => "1. Buka menu **Klinik > Rekam Medis (SOAP)**.\n2. Pilih pasien dari antrean periksa.\n3. Isi kolom **Subjective, Objective, Assessment, dan Plan**.\n4. Pilih kode diagnosa penyakit pada kolom pencarian ICD-10.\n5. Pada bagian E-Resep, pilih obat dan tentukan dosis serta aturan pakai -> Klik **Simpan Rekam Medis & Kirim Resep**.",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Apoteker & Gudang Farmasi',
                'target_role'  => 'Apoteker',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-prescription-bottle-alt',
                'summary'      => 'Telaah resep masuk, penyiapan obat FEFO, pencetakan etiket, penjualan bebas OTC, dan stok opname obat.',
                'content'      => "1. Buka menu **Apotek > Resep Dokter** untuk memproses e-resep dokter.\n2. Klik **Siapkan Obat** -> Sistem otomatis mengalokasikan batch obat yang paling mendekati tanggal kedaluwarsa (FEFO).\n3. Klik **Selesai Racik & Cetak Etiket** untuk mencetak label pemakaian obat.\n4. Untuk penjualan obat bebas, gunakan menu **Apotek > Penjualan Bebas**.",
                'order_num'    => 4,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'role_guide',
                'title'        => 'Panduan Kasir Utama & Billing',
                'target_role'  => 'Kasir',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-cash-register',
                'summary'      => 'Penerimaan tagihan pelayanan medis & obat, pembayaran multi-metode, pencetakan kwitansi, dan tutup shift kasir.',
                'content'      => "1. Buka menu **Keuangan > Kasir Utama**.\n2. Klik tagihan pasien yang berstatus *Menunggu Pembayaran*.\n3. Pilih metode pembayaran (Tunai, QRIS Bank Mandiri/BCA, Transfer Bank, atau BPJS).\n4. Masukkan nominal uang yang diterima -> Klik **Bayar & Cetak Kwitansi**.\n5. Pada akhir jam kerja, buka menu **Keuangan > Rekap Kasir & Shift** untuk melakukan tutup shift.",
                'order_num'    => 5,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],

            // FAQS
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana jika pasien mendaftar online tetapi tidak membawa tiket cetak?',
                'target_role'  => 'Pendaftaran / FO',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-circle-question',
                'summary'      => 'Solusi verifikasi kedatangan pasien pendaftaran online tanpa tiket fisik.',
                'content'      => "Petugas pendaftaran cukup mencari **Nama Pasien** atau **NIK KTP** pada tabel pendaftaran hari ini. Data pasien dan nomor antreannya sudah otomatis aktif di sistem.",
                'order_num'    => 1,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana jika koneksi internet terputus saat dokter sedang mengisi SOAP?',
                'target_role'  => 'Dokter',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-circle-question',
                'summary'      => 'Fitur draft autosave pada formulir SOAP dokter.',
                'content'      => "Sistem menyimpan draft SOAP secara berkala di browser. Ketika koneksi pulih dan halaman dimuat ulang, teks pemeriksaan yang belum tersimpan akan dipulihkan otomatis.",
                'order_num'    => 2,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana cara mengubah stok obat jika terdapat selisih fisik di gudang?',
                'target_role'  => 'Apoteker',
                'badge_color'  => 'info',
                'icon'         => 'fas fa-circle-question',
                'summary'      => 'Prosedur penyesuaian stok opname farmasi.',
                'content'      => "Buka menu **Apotek > Stock Opname** -> Klik *+ Input Opname Baru* -> Masukkan jumlah fisik riil di rak -> Sistem otomatis menyesuaikan kartu stok dan membukukan selisih ke akun beban penyesuaian persediaan.",
                'order_num'    => 3,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana jika salah ketik saldo awal akun atau ingin mengosongkan kembali ke Rp 0?',
                'target_role'  => 'Keuangan / Akuntansi',
                'badge_color'  => 'warning',
                'icon'         => 'fas fa-coins',
                'summary'      => 'Koreksi dan reset saldo awal kas, bank, persediaan, dan modal pembukuan sistem.',
                'content'      => "Buka menu **Akuntansi > Saldo Awal Sistem**.\n1. Untuk mengoreksi nominal: Klik pada kolom akun yang salah ketik, masukkan nominal baru (atau angka `0`), lalu klik **Simpan & Terapkan Saldo Awal Sistem**.\n2. Untuk membersihkan seluruh form: Klik tombol **Bersihkan Input ke 0**.\n3. Untuk menghapus total seluruh pembukuan saldo awal: Klik tombol merah **Hapus & Reset Semua Saldo ke 0**.",
                'order_num'    => 4,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana cara mengedit atau menghapus jurnal umum jika terjadi salah input?',
                'target_role'  => 'Keuangan / Akuntansi',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-file-lines',
                'summary'      => 'Mekanisme pengeditan jurnal manual dan koreksi transaksi otomatis.',
                'content'      => "1. **Jurnal Manual & Penyesuaian:** Buka menu **Akuntansi > Jurnal Umum**. Pada baris jurnal yang bersangkutan, klik ikon biru **Edit (Pensil)** untuk mengubah nominal/akun, atau klik ikon merah **Hapus (Tong Sampah)** untuk membatalkan jurnal tersebut. Sistem akan otomatis memulihkan saldo akun ke posisi semula.\n2. **Jurnal Otomatis (Kasir/Apotek/Resto):** Bertanda kunci (🔒) agar data rekam medis dan fisik obat tetap akurat. Koreksi dilakukan di modul sumbernya (misal pembatalan billing kasir) atau dengan menginput **Jurnal Penyesuaian / Pembalik (Reversing Entry)** di menu Jurnal Umum.",
                'order_num'    => 5,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ],
            [
                'category'     => 'faq',
                'title'        => 'Bagaimana cara melihat riwayat rekam medis pasien langsung dari menu pendaftaran?',
                'target_role'  => 'Pendaftaran / Medis',
                'badge_color'  => 'success',
                'icon'         => 'fas fa-notes-medical',
                'summary'      => 'Melihat seluruh riwayat kunjungan, TTV, SOAP, dan resep pasien dengan sekali klik.',
                'content'      => "Buka menu **Pendaftaran Pasien**. Pada tabel daftar pasien, klik nomor rekam medis (misalnya `RM-000001`) atau nama pasien. Jendela modal interaktif akan langsung menampilkan bio pasien, riwayat tanda vital, catatan SOAP dokter, kode diagnosa ICD-10, serta terapi e-resep obat dari seluruh kunjungan terdahulu.",
                'order_num'    => 6,
                'is_published' => 1,
                'created_by'   => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($docs as $d) {
            $this->db->table('system_documentations')->insert($d);
        }

        echo "Seeder system_documentations berhasil dijalankan (" . count($docs) . " data panduan tersimpan).\n";
    }
}
