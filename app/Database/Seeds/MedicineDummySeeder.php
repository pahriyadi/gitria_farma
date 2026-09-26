<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MedicineDummySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // 1. Delete old data safely
        $db->table('medicine_batches')->emptyTable();
        $db->table('medicines')->emptyTable();
        $db->table('medicine_categories')->emptyTable();
        $db->table('units')->emptyTable();

        // 2. Insert Units
        $units = [
            ['id' => 1, 'code' => 'TAB', 'name' => 'Tablet', 'category' => 'farmasi'],
            ['id' => 2, 'code' => 'BTL', 'name' => 'Botol', 'category' => 'farmasi'],
            ['id' => 3, 'code' => 'STRIP', 'name' => 'Strip', 'category' => 'farmasi'],
            ['id' => 4, 'code' => 'TUBE', 'name' => 'Tube', 'category' => 'farmasi']
        ];
        $db->table('units')->insertBatch($units);

        // 3. Insert Categories
        $categories = [
            ['id' => 1, 'code' => 'OBB', 'name' => 'Obat Bebas', 'drug_class' => 'obat_bebas'],
            ['id' => 2, 'code' => 'OBK', 'name' => 'Obat Keras', 'drug_class' => 'obat_keras'],
            ['id' => 3, 'code' => 'SUP', 'name' => 'Suplemen', 'drug_class' => 'suplemen']
        ];
        $db->table('medicine_categories')->insertBatch($categories);

        // 4. Insert Medicines
        $medicines = [
            [
                'id' => 1, 'code' => 'MED-001', 'name' => 'Paracetamol 500mg', 
                'category_id' => 1, 'unit_id' => 1, 'unit' => 'Tablet',
                'type' => 'bebas', 'price' => 5000, 'min_stock' => 50
            ],
            [
                'id' => 2, 'code' => 'MED-002', 'name' => 'Amoxicillin 500mg', 
                'category_id' => 2, 'unit_id' => 3, 'unit' => 'Strip',
                'type' => 'keras', 'price' => 15000, 'min_stock' => 20
            ],
            [
                'id' => 3, 'code' => 'MED-003', 'name' => 'Vitamin C 1000mg', 
                'category_id' => 3, 'unit_id' => 2, 'unit' => 'Botol',
                'type' => 'bebas', 'price' => 35000, 'min_stock' => 10
            ],
            [
                'id' => 4, 'code' => 'MED-004', 'name' => 'Salep Kulit Kalpanax', 
                'category_id' => 1, 'unit_id' => 4, 'unit' => 'Tube',
                'type' => 'bebas', 'price' => 12500, 'min_stock' => 5
            ]
        ];
        $db->table('medicines')->insertBatch($medicines);

        // 5. Insert Batches
        $batches = [
            [
                'medicine_id' => 1, 'batch_no' => 'BCH-2026-09-01-A',
                'buy_price' => 4000, 'stock' => 200, 'expired_date' => '2028-09-01'
            ],
            [
                'medicine_id' => 2, 'batch_no' => 'BCH-2026-09-01-B',
                'buy_price' => 12000, 'stock' => 100, 'expired_date' => '2027-12-01'
            ],
            [
                'medicine_id' => 3, 'batch_no' => 'BCH-2026-09-01-C',
                'buy_price' => 28000, 'stock' => 50, 'expired_date' => '2028-05-01'
            ],
            [
                'medicine_id' => 4, 'batch_no' => 'BCH-2026-09-01-D',
                'buy_price' => 10000, 'stock' => 30, 'expired_date' => '2027-06-01'
            ]
        ];
        $db->table('medicine_batches')->insertBatch($batches);

        echo "Medicine Dummy Data Seeded Successfully!\n";
    }
}
