<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExactReferenceSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. KATEGORI PELAYANAN
        $this->db->table('categories')->truncate();
        $categories = [
            ['id' => 1, 'name' => 'POLI',      'description' => 'Kategori Pelayanan Poli Klinik Spesialis / Umum'],
            ['id' => 2, 'name' => 'TINDAKAN',  'description' => 'Kategori Pelayanan Tindakan Medis, Estetika & Studio'],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 2. SUB KATEGORI PELAYANAN (Poliklinik)
        $this->db->table('polikliniks')->truncate();
        $polikliniks = [
            ['id' => 1, 'category_id' => 1, 'name' => 'SPESIALIS JANTUNG', 'description' => 'Poli Pelayanan Kesehatan Jantung & Pembuluh Darah', 'status' => 'active'],
            ['id' => 2, 'category_id' => 1, 'name' => 'SPESIALIS GIZI',    'description' => 'Poli Pelayanan Konsultasi & Diet Gizi',             'status' => 'active'],
        ];
        $this->db->table('polikliniks')->insertBatch($polikliniks);

        // Also sync polyclinics table for legacy compatibility
        $this->db->table('polyclinics')->truncate();
        $this->db->table('polyclinics')->insertBatch([
            ['id' => 1, 'name' => 'SPESIALIS JANTUNG', 'description' => 'Poli Spesialis Jantung', 'status' => 'active'],
            ['id' => 2, 'name' => 'SPESIALIS GIZI',    'description' => 'Poli Spesialis Gizi',    'status' => 'active'],
            ['id' => 3, 'name' => 'Tindakan Saja',     'description' => 'Pelayanan Tindakan',     'status' => 'active']
        ]);

        // 3. SUB KATEGORI & SUB KECIL PELAYANAN (Tindakan & Harga)
        $this->db->table('tindakan')->truncate();
        
        // Parent: HAIRSTUDIO (Sub Kategori)
        $this->db->table('tindakan')->insert([
            'id'          => 1,
            'category_id' => 2, // TINDAKAN
            'parent_id'   => null,
            'code'        => 'TND-HAIR',
            'name'        => 'HAIRSTUDIO',
            'description' => 'Sub Kategori Pelayanan Perawatan Rambut',
            'price'       => 0.00,
            'status'      => 'active'
        ]);

        // Sub Kecil: RAMBUT RONTOK (di bawah HAIRSTUDIO, Harga: 50.000)
        $this->db->table('tindakan')->insert([
            'id'          => 2,
            'category_id' => 2, // TINDAKAN
            'parent_id'   => 1, // HAIRSTUDIO
            'code'        => 'TND-HAIR-01',
            'name'        => 'RAMBUT RONTOK',
            'description' => 'Sub Kecil Kategori Pelayanan: Terapi & Perawatan Rambut Rontok',
            'price'       => 50000.00,
            'status'      => 'active'
        ]);

        // Sub Kecil Langsung: FACIAL (Tanpa Sub Kategori / Langsung, Harga: 100.000)
        $this->db->table('tindakan')->insert([
            'id'          => 3,
            'category_id' => 2, // TINDAKAN
            'parent_id'   => null, // Langsung
            'code'        => 'TND-FACIAL',
            'name'        => 'FACIAL',
            'description' => 'Sub Kecil Kategori Pelayanan: Perawatan Wajah Facial',
            'price'       => 100000.00,
            'status'      => 'active'
        ]);

        // 4. DATA DOKTER & FEE PER PASIEN
        $this->db->table('doctors')->truncate();
        $doctors = [
            [
                'id'            => 1,
                'nik_employee'  => 'EMP-DOK-001',
                'name'          => 'PAHRI',
                'category_id'   => 1, // POLI
                'polyclinic_id' => 1, // SPESIALIS JANTUNG
                'tindakan_id'   => null,
                'fee_per_pasien'=> 0.00,
                'sip_number'    => 'SIP/440/001/DINKES',
                'str_number'    => 'STR-PAHRI-2026',
                'str_expiry'    => '2030-12-31',
                'status'        => 'active'
            ],
            [
                'id'            => 2,
                'nik_employee'  => 'EMP-DOK-002',
                'name'          => 'YADI',
                'category_id'   => 2, // TINDAKAN
                'polyclinic_id' => 3, // Tindakan Saja
                'tindakan_id'   => 2, // RAMBUT RONTOK (HAIRSTUDIO)
                'fee_per_pasien'=> 100000.00, // FEE PER PASIEN: 100000
                'sip_number'    => 'SIP/440/002/DINKES',
                'str_number'    => 'STR-YADI-2026',
                'str_expiry'    => '2030-12-31',
                'status'        => 'active'
            ],
            [
                'id'            => 3,
                'nik_employee'  => 'EMP-DOK-003',
                'name'          => 'YADI',
                'category_id'   => 2, // TINDAKAN
                'polyclinic_id' => 3, // Tindakan Saja
                'tindakan_id'   => 3, // FACIAL
                'fee_per_pasien'=> 100000.00, // FEE PER PASIEN: 100000
                'sip_number'    => 'SIP/440/003/DINKES',
                'str_number'    => 'STR-YADI-2026',
                'str_expiry'    => '2030-12-31',
                'status'        => 'active'
            ]
        ];
        $this->db->table('doctors')->insertBatch($doctors);

        // 5. Fee rules synchronisation
        $this->db->table('fee_rules')->truncate();
        $feeRules = [
            ['id' => 1, 'service_id' => 2, 'role_id' => 5, 'percentage' => 0.00, 'flat_fee' => 100000.00, 'status' => 'active'], // Yadi - Rambut Rontok (Fee 100k)
            ['id' => 2, 'service_id' => 3, 'role_id' => 5, 'percentage' => 0.00, 'flat_fee' => 100000.00, 'status' => 'active'], // Yadi - Facial (Fee 100k)
        ];
        $this->db->table('fee_rules')->insertBatch($feeRules);

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
