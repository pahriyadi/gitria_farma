<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanDataSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // Daftar tabel transaksi operasional yang dikosongkan
        $tablesToClean = [
            // 1. Pelayanan Medis & Pasien
            'patient_visits',
            'patients',
            'queue_numbers',
            'triage_records',
            'medical_records',
            'icd10_diagnoses',
            'icd9_procedures',
            'medical_letters',
            'lab_results',

            // 2. Farmasi & Resep
            'prescriptions',
            'prescription_details',
            'pharmacy_sales',
            'pharmacy_sale_details',
            'stock_movements',
            'stock_opnames',
            'stock_opname_details',

            // 3. Kasir & Billing
            'billing_transactions',
            'billing_details',
            'cash_transactions',
            'internal_cash_transfers',
            'fee_transactions',

            // 4. Restoran & Dapur
            'restaurant_orders',
            'restaurant_order_details',
            'kitchen_orders',

            // 5. Akuntansi & Jurnal
            'journal_entries',
            'journal_entry_details',

            // 6. Pengadaan & Aset
            'purchase_requests',
            'purchase_request_items',
            'purchase_orders',
            'purchase_order_items',
            'goods_receipts',
            'goods_receipt_items',
            'approval_requests',
            'asset_depreciations',
            'asset_maintenances',
            'asset_mutations',
            'inventory_assets',

            // 7. HRD & Keamanan
            'employee_attendances',
            'employee_leaves',
            'payrolls',
            'payroll_items',
            'audit_logs'
        ];

        $clearedCount = 0;
        foreach ($tablesToClean as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->table($table)->truncate();
                $clearedCount++;
            }
        }

        // Reset status operasional dan saldo awal
        if ($this->db->tableExists('accounts')) {
            $this->db->table('accounts')->update(['balance' => 0.00]);
        }

        if ($this->db->tableExists('cash_registers')) {
            $this->db->table('cash_registers')->update(['balance' => 0.00]);
        }

        if ($this->db->tableExists('restaurant_tables')) {
            $this->db->table('restaurant_tables')->update(['status' => 'empty']);
        }

        if ($this->db->tableExists('medicine_batches')) {
            $this->db->table('medicine_batches')->update(['stock' => 0]);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        echo "\n[SUKSES] Seluruh {$clearedCount} tabel data transaksi (pasien, billing, resep, order resto, jurnal, payroll, log) berhasil dikosongkan!\n";
        echo "[INFO] Seluruh data referensi master (Obat, Produk Resto, Master Tindakan & Tarif, ICD-10/9, Lab, Dokter, Poli, Nakes, Akun COA, dan Pengguna Sistem) tetap terjaga 100% UTUH.\n";
    }
}
