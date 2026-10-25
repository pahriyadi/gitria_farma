/**
 * Data bawaan Peta Alur Sistem 3D.
 * Modul = ruangan di kantor 3D. Skenario = urutan langkah permintaan/pengembalian data.
 * Isi diambil dari alur bisnis di dokumentasi sistem (nama tabel & format nomor dokumen asli).
 * Semua data ini bisa diubah pengguna lewat panel "Atur Alur" dan disimpan di localStorage.
 */

export const KIND_OPTIONS = [
    { value: 'desk', label: 'Meja kerja' },
    { value: 'bed', label: 'Meja + bed periksa' },
    { value: 'lab', label: 'Meja laboratorium' },
    { value: 'shelf', label: 'Rak obat / gudang' },
    { value: 'kitchen', label: 'Dapur' },
    { value: 'server', label: 'Rak server' },
    { value: 'tv', label: 'Layar display & ruang tunggu' },
    { value: 'kiosk', label: 'Kiosk / pintu masuk' }
];

export const STAFF_OPTIONS = [
    { value: 'admin', label: 'Staf administrasi' },
    { value: 'medis', label: 'Tenaga medis (jas putih)' },
    { value: 'chef', label: 'Juru masak' },
    { value: 'it', label: 'Petugas IT' },
    { value: 'eksekutif', label: 'Manajemen' },
    { value: 'pasien', label: 'Pasien / tamu' }
];

export const DEFAULT_MODULES = [
    { id: 'pasien', name: 'Pasien & Kiosk APM', sub: 'Portal publik', kind: 'kiosk', staff: 'pasien', builtin: true },
    { id: 'pendaftaran', name: 'Pendaftaran', sub: 'Admisi & antrean', kind: 'desk', staff: 'admin', builtin: true },
    { id: 'triage', name: 'Triase Perawat', sub: 'Tanda vital', kind: 'bed', staff: 'medis', builtin: true },
    { id: 'dokter', name: 'Poli Dokter', sub: 'RME SOAP & e-Resep', kind: 'bed', staff: 'medis', builtin: true },
    { id: 'lab', name: 'Laboratorium', sub: 'Patologi klinis', kind: 'lab', staff: 'medis', builtin: true },

    { id: 'livesync', name: 'LiveSync & Display', sub: 'TV antrean & suara', kind: 'tv', staff: 'it', builtin: true },
    { id: 'kasir', name: 'Kasir Terpadu', sub: 'Unified billing', kind: 'desk', staff: 'admin', builtin: true },
    { id: 'database', name: 'Server Database', sub: 'MySQL 8, 111 tabel', kind: 'server', staff: 'it', builtin: true },
    { id: 'apotek', name: 'Apotek & Farmasi', sub: 'Dispensing FEFO', kind: 'shelf', staff: 'medis', builtin: true },
    { id: 'resto', name: 'Dapur Gizi (KDS)', sub: 'Menu diet pasien', kind: 'kitchen', staff: 'chef', builtin: true },

    { id: 'direktur', name: 'Ruang Direktur', sub: 'Laporan & approval', kind: 'desk', staff: 'eksekutif', builtin: true },
    { id: 'journal', name: 'Akuntansi', sub: 'JournalEngine', kind: 'desk', staff: 'admin', builtin: true },
    { id: 'gudang', name: 'Gudang Induk', sub: 'GRN & mutasi stok', kind: 'shelf', staff: 'admin', builtin: true },
    { id: 'distributor', name: 'Distributor B2B', sub: 'Faktur grosir & piutang', kind: 'desk', staff: 'admin', builtin: true },
    { id: 'mitra', name: 'Pelanggan Grosir', sub: 'Apotek & klinik mitra', kind: 'kiosk', staff: 'pasien', builtin: true },

    { id: 'hrd', name: 'HRD & Payroll', sub: 'Presensi & gaji', kind: 'desk', staff: 'admin', builtin: true },
    { id: 'pengadaan', name: 'Pengadaan', sub: 'PR, PO & approval', kind: 'desk', staff: 'admin', builtin: true }
];

const req = (from, to, label, payload) => ({ from, to, type: 'request', label, payload });
const res = (from, to, label, payload) => ({ from, to, type: 'response', label, payload });
const proc = (at, label, payload) => ({ from: at, to: at, type: 'process', label, payload });

export const DEFAULT_SCENARIOS = [
    {
        id: 'klinik',
        name: 'Pelayanan Klinik Rawat Jalan',
        desc: 'Dari pasien mendaftar sampai kuitansi dan jurnal otomatis.',
        steps: [
            req('pasien', 'pendaftaran', 'Pasien mendaftar (online / offline)', 'Nama, NIK, poli tujuan, penjamin (Umum / BPJS)'),
            req('pendaftaran', 'database', 'Cek & simpan data pasien', 'patients → generate No. RM-XXXXXX'),
            res('database', 'pendaftaran', 'No. RM & nomor antrean', 'queue_numbers → A-001 per poli'),
            req('pendaftaran', 'livesync', 'Kirim panggilan antrean', 'queue_call_events → Display TV + suara TTS'),
            req('pendaftaran', 'triage', 'Pasien diarahkan ke triase', 'patient_visits (status: triase)'),
            req('triage', 'database', 'Simpan tanda vital', 'triage_records: tensi, nadi, suhu, RR, SpO2, BB, TB'),
            res('database', 'dokter', 'Vital sign & riwayat tampil di RME', 'medical_records riwayat + triage_records'),
            proc('dokter', 'Pemeriksaan SOAP & diagnosa', 'ICD-10 diagnosa, ICD-9-CM prosedur'),
            req('dokter', 'database', 'Simpan rekam medis', 'medical_records (S, O, A, P)'),
            req('dokter', 'apotek', 'Kirim e-Resep', 'prescriptions + prescription_details'),
            req('dokter', 'lab', 'Permintaan pemeriksaan lab', 'lab_results (status: diminta)'),
            req('dokter', 'resto', 'Rujukan menu diet', 'kitchen_orders (menu diet pasien)'),
            req('lab', 'database', 'Input hasil lab', 'lab_results + rentang normal lab_tests'),
            proc('apotek', 'Dispensing obat FEFO multi-batch', 'Ambil batch dengan expired paling dekat'),
            req('apotek', 'database', 'Kurangi stok batch', 'medicine_batches, stock_movements'),
            res('database', 'kasir', 'Tagihan terkalkulasi otomatis', 'billing_transactions: tindakan + obat + lab + resto'),
            proc('kasir', 'Terima pembayaran', 'Tunai, QRIS, transfer bank, EDC, BPJS'),
            req('kasir', 'journal', 'Posting jurnal otomatis', 'JournalEngine: Kas (D) / Pendapatan (K)'),
            req('journal', 'database', 'Simpan jurnal seimbang', 'journal_entries + journal_entry_details'),
            res('kasir', 'pasien', 'Kuitansi & obat diserahkan', 'Cetak kuitansi / struk thermal')
        ]
    },
    {
        id: 'distributor',
        name: 'Distributor & Grosir B2B',
        desc: 'Faktur grosir dengan cek plafon kredit, surat jalan, dan pelunasan piutang.',
        steps: [
            req('mitra', 'distributor', 'Pesanan partai besar', 'Daftar obat & jumlah dari apotek / klinik mitra'),
            req('distributor', 'database', 'Cek plafon & sisa limit kredit', 'DistributorService: distributor_customers vs distributor_receivables'),
            res('database', 'distributor', 'Sisa plafon & termin TOP', 'Limit tersedia, TOP 7 / 14 / 30 / 60 hari'),
            proc('distributor', 'Buat faktur grosir', 'INV-DIST-YYYYMMDD-XXXX, diskon grosir, PPN'),
            req('distributor', 'database', 'Simpan faktur & kurangi stok', 'distributor_sales, distributor_sale_details, distributor_stocks'),
            req('distributor', 'journal', 'Posting jurnal penjualan kredit', 'Piutang 1-103 (D) / Pendapatan Grosir 4-104 (K), HPP 5-104 (D)'),
            req('journal', 'database', 'Simpan jurnal', 'journal_entries + journal_entry_details'),
            res('distributor', 'mitra', 'Faktur & Surat Jalan (DO) dikirim', 'Cetak faktur A4 + delivery order'),
            req('mitra', 'distributor', 'Pembayaran piutang', 'PAY-DIST-YYYYMMDD-XXXX'),
            req('distributor', 'database', 'Update aging piutang', 'distributor_payments, distributor_receivables'),
            req('distributor', 'journal', 'Jurnal pelunasan', 'Kas / Bank (D) / Piutang 1-103 (K)'),
            res('database', 'direktur', 'Laporan laba rugi grosir', 'Akun 4-104, 4-304, 5-104')
        ]
    },
    {
        id: 'pengadaan',
        name: 'Pengadaan PR → PO → GRN',
        desc: 'Stok kritis memicu permintaan pembelian, approval, penerimaan barang, dan mutasi ke apotek.',
        steps: [
            req('apotek', 'pengadaan', 'Stok kritis: ajukan PR', 'purchase_requests + purchase_request_items'),
            req('pengadaan', 'database', 'Simpan PR & mulai approval', 'approval_requests, approval_steps'),
            req('pengadaan', 'direktur', 'Minta persetujuan', 'ApprovalEngine: verifikator → approver'),
            res('direktur', 'pengadaan', 'PR disetujui', 'approval_steps (status: approved)'),
            proc('pengadaan', 'Terbitkan PO ke PBF', 'purchase_orders ke suppliers'),
            req('pengadaan', 'database', 'Simpan PO', 'purchase_orders + purchase_order_items'),
            proc('gudang', 'Barang datang, cek fisik & batch', 'Cocokkan qty, batch, expired date'),
            req('gudang', 'database', 'Simpan Goods Receipt', 'goods_receipts, medicine_batches (otomatis), warehouse_stock'),
            req('gudang', 'journal', 'Jurnal persediaan', 'Persediaan (D) / Hutang Usaha (K)'),
            req('gudang', 'apotek', 'Mutasi stok ke Depo Apotek', 'stock_transfers + stock_transfer_items'),
            req('apotek', 'database', 'Update kartu stok', 'stock_movements (running balance)')
        ]
    },
    {
        id: 'livesync',
        name: 'LiveSync Real-Time (tanpa reload)',
        desc: 'Layar-layar operasional menarik data JSON secara berkala dari server.',
        steps: [
            req('livesync', 'database', 'Polling data antrean', 'LiveSync: JSON antrean terbaru'),
            res('database', 'livesync', 'Antrean terbaru dikirim', 'queue_numbers + queue_call_events'),
            proc('livesync', 'Panggil nomor dengan suara', 'Web Speech API, anti-tabrakan suara'),
            req('apotek', 'database', 'Polling resep masuk', 'prescriptions (status: baru)'),
            res('database', 'apotek', 'Resep baru muncul tanpa reload', 'Notifikasi di layar farmasi'),
            req('resto', 'database', 'Polling tiket dapur', 'kitchen_orders'),
            res('database', 'resto', 'Status tiket diperbarui', 'Queued → Cooking → Ready'),
            req('kasir', 'database', 'Polling tagihan siap bayar', 'billing_transactions (status: menunggu)'),
            res('database', 'kasir', 'Tagihan baru tampil', 'Daftar kasir ter-update')
        ]
    },
    {
        id: 'payroll',
        name: 'HRD: Presensi ke Payroll',
        desc: 'Rekap kehadiran bulanan menjadi slip gaji dan jurnal beban gaji.',
        steps: [
            req('hrd', 'database', 'Ambil presensi bulanan', 'employee_attendances, employee_leaves, work_shifts'),
            res('database', 'hrd', 'Rekap kehadiran pegawai', 'Hadir, cuti, terlambat per pegawai'),
            proc('hrd', 'Hitung payroll', 'Gaji pokok, tunjangan, potongan'),
            req('hrd', 'database', 'Simpan payroll', 'payrolls + payroll_items'),
            req('hrd', 'journal', 'Jurnal beban gaji', 'Beban Gaji (D) / Kas (K)'),
            req('journal', 'database', 'Simpan jurnal', 'journal_entries + journal_entry_details'),
            res('database', 'direktur', 'Laporan beban SDM', 'Rekap payroll bulanan')
        ]
    }
];
