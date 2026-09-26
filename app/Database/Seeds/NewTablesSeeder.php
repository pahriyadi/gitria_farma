<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NewTablesSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Categories
        $categories = [
            ['id' => 1, 'name' => 'Poliklinik',      'description' => 'Kategori layanan poli klinik spesialis'],
            ['id' => 2, 'name' => 'Tindakan Medis',  'description' => 'Kategori tindakan medis umum & spesialis'],
            ['id' => 3, 'name' => 'Estetika',        'description' => 'Kategori layanan estetika & kecantikan'],
            ['id' => 4, 'name' => 'Hairstudio',      'description' => 'Kategori layanan perawatan rambut'],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 2. Polikliniks
        $polikliniks = [
            ['id' => 1, 'category_id' => 1, 'name' => 'Spesialis Jantung', 'description' => 'Poli spesialis pelayanan kesehatan jantung & pembuluh darah', 'status' => 'active'],
            ['id' => 2, 'category_id' => 1, 'name' => 'Spesialis Gizi',    'description' => 'Poli konsultasi gizi terintegrasi menu diet Restoran',        'status' => 'active'],
            ['id' => 3, 'category_id' => 1, 'name' => 'Poli Gigi',         'description' => 'Poli pelayanan kesehatan gigi & mulut',                        'status' => 'active'],
            ['id' => 4, 'category_id' => 1, 'name' => 'Poli Umum',         'description' => 'Poli pelayanan kesehatan umum tingkat dasar',                  'status' => 'active'],
        ];
        $this->db->table('polikliniks')->insertBatch($polikliniks);

        // 3. Tindakan Induk (parent_id = null)
        $tindakanInduk = [
            ['id' => 1,  'category_id' => 2, 'parent_id' => null, 'code' => 'TND-001', 'name' => 'Konsultasi Dokter Spesialis', 'description' => 'Konsultasi dengan dokter spesialis', 'price' => 0,      'status' => 'active'],
            ['id' => 2,  'category_id' => 2, 'parent_id' => null, 'code' => 'TND-002', 'name' => 'Tindakan EKG',                'description' => 'Rekam jantung elektrokardiografi',  'price' => 100000, 'status' => 'active'],
            ['id' => 3,  'category_id' => 4, 'parent_id' => null, 'code' => 'TND-003', 'name' => 'Hairstudio',                 'description' => 'Layanan perawatan rambut lengkap',   'price' => 0,      'status' => 'active'],
            ['id' => 4,  'category_id' => 3, 'parent_id' => null, 'code' => 'TND-004', 'name' => 'Estetika & Kecantikan',      'description' => 'Layanan estetika & perawatan kulit', 'price' => 0,      'status' => 'active'],
        ];
        $this->db->table('tindakan')->insertBatch($tindakanInduk);

        // 4. Sub-tindakan (parent_id diisi)
        $tindakanSub = [
            // Sub Konsultasi Spesialis
            ['id' => 10, 'category_id' => 2, 'parent_id' => 1, 'code' => 'TND-001A', 'name' => 'Konsultasi Spesialis Jantung', 'description' => 'Konsultasi dr. spesialis jantung', 'price' => 150000, 'status' => 'active'],
            ['id' => 11, 'category_id' => 2, 'parent_id' => 1, 'code' => 'TND-001B', 'name' => 'Konsultasi Spesialis Gizi',    'description' => 'Konsultasi dr. spesialis gizi',    'price' => 150000, 'status' => 'active'],
            ['id' => 12, 'category_id' => 2, 'parent_id' => 1, 'code' => 'TND-001C', 'name' => 'Konsultasi Poli Umum',         'description' => 'Konsultasi dokter umum',            'price' => 75000,  'status' => 'active'],
            // Sub Hairstudio
            ['id' => 20, 'category_id' => 4, 'parent_id' => 3, 'code' => 'TND-003A', 'name' => 'Perawatan Rambut Rontok',      'description' => 'Terapi rambut rontok & penguatan',  'price' => 250000, 'status' => 'active'],
            ['id' => 21, 'category_id' => 4, 'parent_id' => 3, 'code' => 'TND-003B', 'name' => 'Creambath & Hair Mask',        'description' => 'Perawatan rambut creambath',        'price' => 150000, 'status' => 'active'],
            ['id' => 22, 'category_id' => 4, 'parent_id' => 3, 'code' => 'TND-003C', 'name' => 'Hair Color & Highlight',       'description' => 'Pewarnaan & highlight rambut',      'price' => 350000, 'status' => 'active'],
            // Sub Estetika & Kecantikan
            ['id' => 30, 'category_id' => 3, 'parent_id' => 4, 'code' => 'TND-004A', 'name' => 'Facial Treatment',             'description' => 'Perawatan wajah facial lengkap',   'price' => 200000, 'status' => 'active'],
            ['id' => 31, 'category_id' => 3, 'parent_id' => 4, 'code' => 'TND-004B', 'name' => 'Laser Peeling',                'description' => 'Peeling laser kulit wajah',         'price' => 450000, 'status' => 'active'],
            ['id' => 32, 'category_id' => 3, 'parent_id' => 4, 'code' => 'TND-004C', 'name' => 'Microdermabrasi',              'description' => 'Exfoliasi kulit microdermabrasi',   'price' => 300000, 'status' => 'active'],
        ];
        $this->db->table('tindakan')->insertBatch($tindakanSub);

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        echo "NewTablesSeeder: categories, polikliniks, dan tindakan berhasil di-seed!\n";
    }
}
