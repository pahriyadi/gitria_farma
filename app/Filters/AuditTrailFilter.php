<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\AuditService;

class AuditTrailFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing before
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return;
        }

        $uri = trim($request->getUri()->getPath(), '/');
        $method = strtoupper($request->getMethod());

        // Skip internal/polling/AJAX calls
        if (str_contains($uri, 'notif-api') || 
            str_contains($uri, 'json') || 
            (str_contains($uri, 'system/audit') && $request->getGet('draw'))) {
            return;
        }

        // Only log successful responses (status < 400 or redirects)
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            return;
        }

        $action = '';
        $module = '';
        $description = '';
        $tableName = '';

        // 1. Process POST Requests (Create / Update / Delete / Approve / Payment)
        if ($method === 'POST') {
            if (str_contains($uri, 'klinik/pendaftaran')) {
                $action = 'CREATE';
                $module = 'Pendaftaran Pasien';
                $tableName = 'patients';
                $description = 'Mendaftarkan pasien / antrean kunjungan poliklinik baru: ' . ($request->getPost('name') ?: 'Pasien');
            } elseif (str_contains($uri, 'klinik/soap')) {
                $action = 'UPDATE';
                $module = 'Rekam Medis (SOAP)';
                $tableName = 'patient_visits';
                $description = 'Menyimpan rekam medis elektronik (RME/SOAP), diagnosa ICD-10 & resep medis';
            } elseif (str_contains($uri, 'klinik/save-odontogram')) {
                $action = 'UPDATE';
                $module = 'Poli Gigi (Odontogram)';
                $tableName = 'odontograms';
                $description = 'Memperbarui chart odontogram 32 gigi FDI pasien';
            } elseif (str_contains($uri, 'klinik/surat')) {
                $action = 'CREATE';
                $module = 'Surat Medis & Rujukan';
                $tableName = 'medical_letters';
                $description = 'Membuat surat keterangan medis / rujukan pasien: ' . ($request->getPost('letter_type') ?: 'Surat Medis');
            } elseif (str_contains($uri, 'klinik/lab')) {
                $action = 'CREATE';
                $module = 'Hasil Laboratorium';
                $tableName = 'lab_results';
                $description = 'Menginput hasil tes parameter laboratorium klinis';
            } elseif (str_contains($uri, 'apotek/penjualan')) {
                $action = 'CREATE';
                $module = 'Apotek (Penjualan Bebas)';
                $tableName = 'pharmacy_sales';
                $description = 'Transaksi penjualan obat bebas kasir farmasi (Total: Rp ' . number_format((float)$request->getPost('grand_total'), 0, ',', '.') . ')';
            } elseif (str_contains($uri, 'apotek/resep')) {
                $action = 'DISPENSE';
                $module = 'Apotek (e-Resep)';
                $tableName = 'prescriptions';
                $description = 'Menyerahkan dan meracik obat resep dokter';
            } elseif (str_contains($uri, 'apotek/stok')) {
                $action = 'UPDATE';
                $module = 'Master Obat';
                $tableName = 'medicines';
                $description = 'Pembaruan data obat / penambahan batch baru: ' . ($request->getPost('name') ?: 'Obat');
            } elseif (str_contains($uri, 'apotek/opname')) {
                $action = 'UPDATE';
                $module = 'Stock Opname Obat';
                $tableName = 'stock_opnames';
                $description = 'Penyesuaian fisik hasil stock opname farmasi';
            } elseif (str_contains($uri, 'keuangan/kasir')) {
                $action = 'PAYMENT';
                $module = 'Kasir Utama';
                $tableName = 'billing_transactions';
                $description = 'Pelunasan pembayaran tagihan billing pasien (Metode: ' . strtoupper($request->getPost('payment_method') ?: 'TUNAI') . ')';
            } elseif (str_contains($uri, 'keuangan/transaksi')) {
                $action = 'CREATE';
                $module = 'Kas & Bank';
                $tableName = 'cash_transactions';
                $description = 'Pencatatan mutasi transaksi kas & bank: ' . ($request->getPost('description') ?: 'Transaksi Kas');
            } elseif (str_contains($uri, 'keuangan/bayar-fee-dokter')) {
                $action = 'PAYMENT';
                $module = 'Jasa Medis Dokter';
                $tableName = 'doctor_fee_settlements';
                $description = 'Pencairan dan pembayaran settlement jasa medis dokter';
            } elseif (str_contains($uri, 'accounting/saldo-awal') || str_contains($uri, 'accounting/save-saldo-awal')) {
                $action = 'UPDATE';
                $module = 'Akuntansi (Saldo Awal)';
                $tableName = 'accounts';
                $description = 'Setup dan penyesuaian saldo awal bagan akun (COA)';
            } elseif (str_contains($uri, 'accounting/jurnal')) {
                $action = 'CREATE';
                $module = 'Akuntansi (Jurnal Umum)';
                $tableName = 'journal_entries';
                $description = 'Pencatatan entri jurnal umum akuntansi manual: ' . ($request->getPost('description') ?: 'Jurnal');
            } elseif (str_contains($uri, 'procurement/po')) {
                $action = 'CREATE';
                $module = 'Pengadaan PO';
                $tableName = 'purchase_orders';
                $description = 'Pengajuan pesanan pembelian (Purchase Order) farmasi & logistik';
            } elseif (str_contains($uri, 'procurement/approval')) {
                $action = 'APPROVE';
                $module = 'Pengadaan (Approval)';
                $tableName = 'approval_requests';
                $description = 'Verifikasi dan persetujuan pengadaan barang';
            } elseif (str_contains($uri, 'inventaris/aset')) {
                $action = 'CREATE';
                $module = 'Aset & Inventaris';
                $tableName = 'assets';
                $description = 'Pencatatan aset barang / inventaris baru: ' . ($request->getPost('name') ?: 'Aset');
            } elseif (str_contains($uri, 'hrd/pegawai')) {
                $action = 'CREATE';
                $module = 'HRD & Kepegawaian';
                $tableName = 'employees';
                $description = 'Pengelolaan data profil / registrasi pegawai: ' . ($request->getPost('name') ?: 'Pegawai');
            } elseif (str_contains($uri, 'hrd/generate-payroll') || str_contains($uri, 'hrd/bayar-payroll')) {
                $action = 'PAYMENT';
                $module = 'Payroll Gaji Karyawan';
                $tableName = 'payrolls';
                $description = 'Pemrosesan dan pencairan payroll gaji bulanan staf';
            } elseif (str_contains($uri, 'hrd/check-in') || str_contains($uri, 'hrd/check-out')) {
                $action = 'CREATE';
                $module = 'Presensi & Absensi';
                $tableName = 'employee_attendances';
                $description = 'Pencatatan check-in / check-out presensi harian karyawan';
            } elseif (str_contains($uri, 'system/save-user')) {
                $action = 'UPDATE';
                $module = 'User & Hak Akses';
                $tableName = 'users';
                $description = 'Pembaruan akun pengguna dan konfigurasi peran/hak akses';
            } elseif (str_contains($uri, 'system/settings')) {
                $action = 'UPDATE';
                $module = 'Pengaturan Parameter';
                $tableName = 'clinic_settings';
                $description = 'Pembaruan parameter profil klinik & konfigurasi sistem';
            } elseif (str_contains($uri, 'system/master-klinik')) {
                $action = 'UPDATE';
                $module = 'Master Klinik';
                $tableName = 'services';
                $description = 'Pembaruan data master layanan / dokter / poliklinik';
            } elseif (str_contains($uri, 'system/master-icd')) {
                $action = 'UPDATE';
                $module = 'Master ICD';
                $tableName = 'icd10';
                $description = 'Pembaruan basis data master diagnosa ICD-10 / ICD-9';
            } elseif (str_contains($uri, 'system/master-pembayaran')) {
                $action = 'UPDATE';
                $module = 'Master Pembayaran';
                $tableName = 'payment_methods';
                $description = 'Pembaruan master metode pembayaran kasir';
            } elseif (str_contains($uri, 'resto/pos')) {
                $action = 'CREATE';
                $module = 'Resto & Nutrisi POS';
                $tableName = 'restaurant_orders';
                $description = 'Transaksi pemesanan menu resto sehat & skincare';
            }
        }

        // 2. Process Sensitive GET Requests (Cetak / Export / Delete / Toggle)
        elseif ($method === 'GET') {
            if (str_contains($uri, 'cetak-') || str_contains($uri, 'print') || str_contains($uri, 'export')) {
                $action = 'EXPORT';
                $module = 'Cetak Dokumen';
                $description = 'Mencetak dokumen resmi / laporan sistem: ' . $uri;
            } elseif (str_contains($uri, 'backup-dump')) {
                $action = 'EXPORT';
                $module = 'Backup Database';
                $description = 'Mengunduh salinan backup basis data (SQL Dump)';
            } elseif (str_contains($uri, 'delete-') || str_contains($uri, 'delete/')) {
                $action = 'DELETE';
                $module = 'Penghapusan Data';
                $description = 'Menghapus data rekaman sistem pada rute: ' . $uri;
            } elseif (str_contains($uri, 'toggle-')) {
                $action = 'UPDATE';
                $module = 'Status Rekaman';
                $description = 'Mengubah status aktif / non-aktif rekaman pada rute: ' . $uri;
            }
        }

        if (!empty($action) && !empty($module)) {
            AuditService::log($action, $module, $description, $tableName);
        }
    }
}
