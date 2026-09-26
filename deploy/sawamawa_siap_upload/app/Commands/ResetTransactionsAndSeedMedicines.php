<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ResetTransactionsAndSeedMedicines extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'app:reset-transactions';
    protected $description = 'Hapus semua data transaksi & jurnal, reset saldo akun, dan isi master obat referensi baru';

    public function run(array $params)
    {
        $db = \Config\Database::connect('default');

        CLI::write("================================================================", 'yellow');
        CLI::write("  MEMBERSIHKAN TRANSAKSI & MENGISI OBAT REFERENSI BARU", 'yellow');
        CLI::write("================================================================", 'yellow');

        $db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Daftar tabel transaksi yang dibersihkan
        $transactionTables = [
            'journal_entry_details',
            'journal_entries',
            'billing_details',
            'billing_transactions',
            'cash_transactions',
            'cash_registers',
            'fee_transactions',
            'doctor_fee_settlements',
            'internal_cash_transfers',
            'pharmacy_sale_details',
            'pharmacy_sales',
            'pharmacy_pending_prescriptions',
            'prescription_details',
            'prescriptions',
            'stock_movements',
            'stock_opname_details',
            'stock_opnames',
            'stock_transfer_items',
            'stock_transfers',
            'restaurant_order_details',
            'restaurant_orders',
            'kitchen_orders',
            'queue_numbers',
            'queue_call_events',
            'medicine_batches',
            'medicines'
        ];

        foreach ($transactionTables as $table) {
            try {
                $db->query("TRUNCATE TABLE `{$table}`;");
                CLI::write("  [OK] Berhasil mengosongkan tabel: {$table}", 'green');
            } catch (\Throwable $e) {
                CLI::write("  [WARN] Gagal truncate {$table} ({$e->getMessage()}), mencoba DELETE...", 'yellow');
                $db->query("DELETE FROM `{$table}`;");
            }
        }

        // 2. Reset Saldo di COA (Accounts)
        $db->query("UPDATE `accounts` SET balance = 0.00;");
        CLI::write("  [OK] Berhasil mereset saldo semua akun COA ke 0.00", 'green');

        // 3. Pastikan Satuan (Units) tambahan tersedia jika belum ada
        $units = [
            ['code' => 'TAB', 'name' => 'Tablet', 'category' => 'farmasi', 'status' => 'active'],
            ['code' => 'BTL', 'name' => 'Botol', 'category' => 'farmasi', 'status' => 'active'],
            ['code' => 'STRIP', 'name' => 'Strip', 'category' => 'farmasi', 'status' => 'active'],
            ['code' => 'TUBE', 'name' => 'Tube', 'category' => 'farmasi', 'status' => 'active'],
            ['code' => 'KAP', 'name' => 'Kapsul', 'category' => 'farmasi', 'status' => 'active'],
            ['code' => 'SCH', 'name' => 'Sachet', 'category' => 'farmasi', 'status' => 'active']
        ];
        foreach ($units as $u) {
            $existU = $db->table('units')->where('code', $u['code'])->get()->getRow();
            if (!$existU) {
                $db->table('units')->insert(array_merge($u, ['created_at' => date('Y-m-d H:i:s')]));
            }
        }

        // Helper cari unit ID
        $getUnitId = function($code) use ($db) {
            $r = $db->table('units')->where('code', $code)->get()->getRow();
            return $r ? (int)$r->id : 1;
        };

        // 4. Input Daftar Master Obat Referensi Baru
        $newMedicines = [
            [
                'code'        => 'MED-001',
                'name'        => 'Paracetamol 500 mg Tablet',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('TAB'),
                'type'        => 'bebas',
                'unit'        => 'Tablet',
                'price'       => 1500.00,
                'min_stock'   => 50,
                'status'      => 'active',
                'buy_price'   => 500.00,
                'stock'       => 500,
                'batch_no'    => 'BCH-PCT-202609',
                'expired'     => '2028-12-31'
            ],
            [
                'code'        => 'MED-002',
                'name'        => 'Amoxicillin 500 mg Kaplet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 14000.00,
                'min_stock'   => 20,
                'status'      => 'active',
                'buy_price'   => 8000.00,
                'stock'       => 120,
                'batch_no'    => 'BCH-AMX-202609',
                'expired'     => '2028-06-30'
            ],
            [
                'code'        => 'MED-003',
                'name'        => 'Cetirizine 10 mg Tablet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 12000.00,
                'min_stock'   => 20,
                'status'      => 'active',
                'buy_price'   => 6000.00,
                'stock'       => 100,
                'batch_no'    => 'BCH-CTZ-202609',
                'expired'     => '2028-08-31'
            ],
            [
                'code'        => 'MED-004',
                'name'        => 'Ibuprofen 400 mg Tablet',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'bebas',
                'unit'        => 'Strip',
                'price'       => 9500.00,
                'min_stock'   => 25,
                'status'      => 'active',
                'buy_price'   => 5000.00,
                'stock'       => 150,
                'batch_no'    => 'BCH-IBU-202609',
                'expired'     => '2028-10-31'
            ],
            [
                'code'        => 'MED-005',
                'name'        => 'Omeprazole 20 mg Kapsul',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 18500.00,
                'min_stock'   => 20,
                'status'      => 'active',
                'buy_price'   => 9000.00,
                'stock'       => 80,
                'batch_no'    => 'BCH-OMP-202609',
                'expired'     => '2028-05-31'
            ],
            [
                'code'        => 'MED-006',
                'name'        => 'Antasida Doen Tablet Kunyah',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'bebas',
                'unit'        => 'Strip',
                'price'       => 7000.00,
                'min_stock'   => 30,
                'status'      => 'active',
                'buy_price'   => 3500.00,
                'stock'       => 200,
                'batch_no'    => 'BCH-ATD-202609',
                'expired'     => '2028-11-30'
            ],
            [
                'code'        => 'MED-007',
                'name'        => 'Vitamin C 500 mg Botol 30 Tablet',
                'category_id' => 3, // Suplemen
                'unit_id'     => $getUnitId('BTL'),
                'type'        => 'bebas',
                'unit'        => 'Botol',
                'price'       => 45000.00,
                'min_stock'   => 15,
                'status'      => 'active',
                'buy_price'   => 25000.00,
                'stock'       => 60,
                'batch_no'    => 'BCH-VTC-202609',
                'expired'     => '2029-01-31'
            ],
            [
                'code'        => 'MED-008',
                'name'        => 'Vitamin D3 1000 IU Botol',
                'category_id' => 3, // Suplemen
                'unit_id'     => $getUnitId('BTL'),
                'type'        => 'bebas',
                'unit'        => 'Botol',
                'price'       => 65000.00,
                'min_stock'   => 10,
                'status'      => 'active',
                'buy_price'   => 38000.00,
                'stock'       => 50,
                'batch_no'    => 'BCH-VTD-202609',
                'expired'     => '2029-03-31'
            ],
            [
                'code'        => 'MED-009',
                'name'        => 'Ambroxol 30 mg Tablet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 8500.00,
                'min_stock'   => 20,
                'status'      => 'active',
                'buy_price'   => 4000.00,
                'stock'       => 110,
                'batch_no'    => 'BCH-AMB-202609',
                'expired'     => '2028-07-31'
            ],
            [
                'code'        => 'MED-010',
                'name'        => 'Dexamethasone 0.5 mg Tablet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 6500.00,
                'min_stock'   => 20,
                'status'      => 'active',
                'buy_price'   => 3000.00,
                'stock'       => 90,
                'batch_no'    => 'BCH-DEX-202609',
                'expired'     => '2028-09-30'
            ],
            [
                'code'        => 'MED-011',
                'name'        => 'Amlodipine 10 mg Tablet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 11500.00,
                'min_stock'   => 25,
                'status'      => 'active',
                'buy_price'   => 5500.00,
                'stock'       => 130,
                'batch_no'    => 'BCH-AML-202609',
                'expired'     => '2028-04-30'
            ],
            [
                'code'        => 'MED-012',
                'name'        => 'Metformin 500 mg Tablet',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('STRIP'),
                'type'        => 'keras',
                'unit'        => 'Strip',
                'price'       => 9000.00,
                'min_stock'   => 25,
                'status'      => 'active',
                'buy_price'   => 4500.00,
                'stock'       => 120,
                'batch_no'    => 'BCH-MET-202609',
                'expired'     => '2028-08-31'
            ],
            [
                'code'        => 'MED-013',
                'name'        => 'Salep Hidrokortison 2.5% Tube 5g',
                'category_id' => 2, // Obat Keras
                'unit_id'     => $getUnitId('TUBE'),
                'type'        => 'keras',
                'unit'        => 'Tube',
                'price'       => 16500.00,
                'min_stock'   => 15,
                'status'      => 'active',
                'buy_price'   => 8500.00,
                'stock'       => 70,
                'batch_no'    => 'BCH-HDC-202609',
                'expired'     => '2027-12-31'
            ],
            [
                'code'        => 'MED-014',
                'name'        => 'Betadine Antiseptic Solution 60 ml',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('BTL'),
                'type'        => 'bebas',
                'unit'        => 'Botol',
                'price'       => 32500.00,
                'min_stock'   => 15,
                'status'      => 'active',
                'buy_price'   => 21000.00,
                'stock'       => 65,
                'batch_no'    => 'BCH-BTD-202609',
                'expired'     => '2029-05-31'
            ],
            [
                'code'        => 'MED-015',
                'name'        => 'Oralit 200 ml Sachet',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('SCH'),
                'type'        => 'bebas',
                'unit'        => 'Sachet',
                'price'       => 2500.00,
                'min_stock'   => 50,
                'status'      => 'active',
                'buy_price'   => 1000.00,
                'stock'       => 250,
                'batch_no'    => 'BCH-ORL-202609',
                'expired'     => '2029-02-28'
            ],
            [
                'code'        => 'MED-016',
                'name'        => 'OBH Sirup Batuk Hitam 100 ml',
                'category_id' => 1, // Obat Bebas
                'unit_id'     => $getUnitId('BTL'),
                'type'        => 'bebas',
                'unit'        => 'Botol',
                'price'       => 22500.00,
                'min_stock'   => 15,
                'status'      => 'active',
                'buy_price'   => 14000.00,
                'stock'       => 75,
                'batch_no'    => 'BCH-OBH-202609',
                'expired'     => '2028-11-30'
            ],
        ];

        $now = date('Y-m-d H:i:s');
        $insertedCount = 0;

        foreach ($newMedicines as $m) {
            $buyPrice = $m['buy_price'];
            $stock = $m['stock'];
            $batchNo = $m['batch_no'];
            $expired = $m['expired'];

            unset($m['buy_price'], $m['stock'], $m['batch_no'], $m['expired']);
            $m['created_at'] = $now;

            $db->table('medicines')->insert($m);
            $medId = $db->insertID();

            // Insert Batch
            $db->table('medicine_batches')->insert([
                'medicine_id'  => $medId,
                'batch_no'     => $batchNo,
                'buy_price'    => $buyPrice,
                'stock'        => $stock,
                'expired_date' => $expired,
                'created_at'   => $now
            ]);

            $insertedCount++;
        }

        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        CLI::write("  [OK] Berhasil memasukkan {$insertedCount} master obat referensi baru beserta batch dan stok!", 'green');
        CLI::write("================================================================", 'green');
        CLI::write("  PEMBERSIHAN TRANSAKSI & SEEDING OBAT BERHASIL SELESAI!", 'green');
        CLI::write("================================================================", 'green');
    }
}
