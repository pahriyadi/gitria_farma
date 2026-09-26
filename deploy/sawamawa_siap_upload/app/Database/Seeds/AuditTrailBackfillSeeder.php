<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuditTrailBackfillSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');

        $fields = $db->getFieldNames('audit_logs');
        if (!in_array('user_agent', $fields)) {
            $db->query("ALTER TABLE audit_logs ADD COLUMN user_agent VARCHAR(255) NULL AFTER ip_address;");
        }

        $sampleLogs = [
            [
                'user_id'    => 1,
                'action'     => 'CREATE',
                'module'     => 'Pendaftaran Pasien',
                'table_name' => 'patients',
                'record_id'  => 101,
                'old_value'  => null,
                'new_value'  => 'Mendaftarkan pasien baru [Ny. Siti Rahmawati] (No RM: RM-00101) ke Poli Spesialis Gizi Klinis',
                'ip_address' => '192.168.1.15',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'user_id'    => 2,
                'action'     => 'UPDATE',
                'module'     => 'Rekam Medis (SOAP)',
                'table_name' => 'patient_visits',
                'record_id'  => 45,
                'old_value'  => 'Diagnosis: Observasi Awal',
                'new_value'  => 'Menyimpan SOAP klinis, Diagnosa Utama [E11.9 - Diabetes Mellitus Tipe 2], dan e-Resep Medis',
                'ip_address' => '192.168.1.22',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-95 minutes'))
            ],
            [
                'user_id'    => 2,
                'action'     => 'UPDATE',
                'module'     => 'Poli Gigi (Odontogram)',
                'table_name' => 'odontograms',
                'record_id'  => 12,
                'old_value'  => null,
                'new_value'  => 'Memperbarui chart odontogram: Gigi 16 (Caries Media) & Gigi 21 (Composite Filling)',
                'ip_address' => '192.168.1.22',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-80 minutes'))
            ],
            [
                'user_id'    => 3,
                'action'     => 'DISPENSE',
                'module'     => 'Apotek (e-Resep)',
                'table_name' => 'prescriptions',
                'record_id'  => 30,
                'old_value'  => 'Status: waiting',
                'new_value'  => 'Menyerahkan dan meracik tebus resep e-Resep RX-00030 (Metformin 500mg, Glimepiride 2mg)',
                'ip_address' => '192.168.1.30',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-65 minutes'))
            ],
            [
                'user_id'    => 4,
                'action'     => 'PAYMENT',
                'module'     => 'Kasir Utama',
                'table_name' => 'billing_transactions',
                'record_id'  => 88,
                'old_value'  => 'Status: open',
                'new_value'  => 'Pelunasan tagihan billing kasir pasien No. BIL-20260827-088: Rp 350.000 (Metode: QRIS Dinamis)',
                'ip_address' => '192.168.1.40',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-50 minutes'))
            ],
            [
                'user_id'    => 1,
                'action'     => 'CREATE',
                'module'     => 'Pengadaan PO',
                'table_name' => 'purchase_orders',
                'record_id'  => 14,
                'old_value'  => null,
                'new_value'  => 'Pengajuan Purchase Order PO-20260827-014 ke PT Kimia Farma Trading & Distribution (Total: Rp 8.500.000)',
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-40 minutes'))
            ],
            [
                'user_id'    => 5,
                'action'     => 'APPROVE',
                'module'     => 'Pengadaan (Approval)',
                'table_name' => 'approval_requests',
                'record_id'  => 14,
                'old_value'  => 'Status: pending',
                'new_value'  => 'Menyetujui dokumen pengadaan barang PO-20260827-014 oleh Kepala Bagian Logistik & Direksi',
                'ip_address' => '192.168.1.5',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ],
            [
                'user_id'    => 1,
                'action'     => 'UPDATE',
                'module'     => 'User & Hak Akses',
                'table_name' => 'users',
                'record_id'  => 6,
                'old_value'  => 'Role: Staf Farmasi',
                'new_value'  => 'Mengubah hak akses pengguna [apoteker_siti] menjadi Peran Apoteker & PIC Gudang',
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-20 minutes'))
            ],
            [
                'user_id'    => 1,
                'action'     => 'DELETE',
                'module'     => 'Master Layanan',
                'table_name' => 'services',
                'record_id'  => 99,
                'old_value'  => 'Layanan Konsultasi Lama (Nonaktif)',
                'new_value'  => 'Menghapus paket layanan kadaluarsa dari daftar master klinik',
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 minutes'))
            ],
            [
                'user_id'    => 1,
                'action'     => 'EXPORT',
                'module'     => 'Backup Database',
                'table_name' => 'audit_logs',
                'record_id'  => 1,
                'old_value'  => null,
                'new_value'  => 'Mengunduh salinan backup penuh database (Full SQL Dump) untuk arsip berkala',
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ]
        ];

        foreach ($sampleLogs as $log) {
            $db->table('audit_logs')->insert($log);
        }

        echo "Log Audit Trail komprehensif berhasil diisi.\n";
    }
}
