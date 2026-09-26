<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClinicalNutritionSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $now = date('Y-m-d H:i:s');

        // 1. Poliklinik Gizi Klinis & Estetika
        $polys = [
            ['name' => 'Poli Spesialis Gizi Klinis', 'description' => 'Pelayanan asesmen status gizi, terapi nutrisi medik, program diet DM, Hipertensi, dan obesitas.'],
            ['name' => 'Poli Estetika & Dermal Care', 'description' => 'Pelayanan perawatan kesehatan kulit medis, dermal care, dan terapi estetika.']
        ];

        foreach ($polys as $p) {
            // polyclinics table
            $exOld = $this->db->table('polyclinics')->where('name', $p['name'])->get()->getRow();
            if (!$exOld) {
                $this->db->table('polyclinics')->insert(['name' => $p['name'], 'description' => $p['description']]);
                $oldPolyId = $this->db->insertID();
            } else {
                $oldPolyId = $exOld->id;
            }

            // polikliniks table (new)
            $exNew = $this->db->table('polikliniks')->where('name', $p['name'])->get()->getRow();
            if (!$exNew) {
                $this->db->table('polikliniks')->insert(['category_id' => 1, 'name' => $p['name'], 'description' => $p['description'], 'status' => 'active']);
            }
        }

        $polyGizi = $this->db->table('polyclinics')->where('name', 'Poli Spesialis Gizi Klinis')->get()->getRow();
        $polySkin = $this->db->table('polyclinics')->where('name', 'Poli Estetika & Dermal Care')->get()->getRow();

        // 2. Dokter Spesialis Gizi & Estetika
        $doctors = [
            [
                'nik_employee'   => 'DOC-GIZI-001',
                'name'           => 'dr. Felicia Nugraha, Sp.GK',
                'polyclinic_id'  => $polyGizi ? $polyGizi->id : 1,
                'category_id'    => 1,
                'sip_number'     => '446/SIP-DS/DINKES/2024/091',
                'str_number'     => 'STR-GIZI-882910',
                'str_expiry'     => '2029-12-31',
                'status'         => 'active'
            ],
            [
                'nik_employee'   => 'DOC-DERM-001',
                'name'           => 'dr. Amanda Clarissa, Sp.DVE',
                'polyclinic_id'  => $polySkin ? $polySkin->id : 1,
                'category_id'    => 1,
                'sip_number'     => '446/SIP-DS/DINKES/2024/092',
                'str_number'     => 'STR-DERM-882920',
                'str_expiry'     => '2029-12-31',
                'status'         => 'active'
            ]
        ];

        foreach ($doctors as $d) {
            $exDoc = $this->db->table('doctors')->where('name', $d['name'])->get()->getRow();
            if (!$exDoc) {
                $this->db->table('doctors')->insert($d);
            }
        }

        // 3. Tindakan Terapi Gizi & Estetika
        $tindakanParents = [
            [
                'code' => 'TDK-GIZI-IND',
                'name' => 'Pelayanan Terapi Gizi Klinis',
                'desc' => 'Paket layanan dan tindakan medis gizi klinis terpadu',
                'subs' => [
                    ['code' => 'TDK-GIZI-01', 'name' => 'Konsultasi & Asesmen Komposisi Tubuh (BIA Scanner)', 'price' => 150000],
                    ['code' => 'TDK-GIZI-02', 'name' => 'Perencanaan Program Diet Medis Terpersonalisasi (Meal Plan)', 'price' => 200000],
                    ['code' => 'TDK-GIZI-03', 'name' => 'Konsultasi Diet Terapi Penyakit Kronis (DM / Hipertensi / Ginjal)', 'price' => 175000],
                    ['code' => 'TDK-GIZI-04', 'name' => 'Edukasi Gizi & Pantauan Kepatuhan Nutrisi Pasien', 'price' => 100000]
                ]
            ],
            [
                'code' => 'TDK-DERM-IND',
                'name' => 'Pelayanan Estetika Medis & Dermal Care',
                'desc' => 'Tindakan perawatan kulit dan regenerasi dermal medis',
                'subs' => [
                    ['code' => 'TDK-DERM-01', 'name' => 'Medical Facial Deep Cleansing & Ozone Detox', 'price' => 250000],
                    ['code' => 'TDK-DERM-02', 'name' => 'Medical Chemical Peeling Brightening Anti-Aging', 'price' => 350000],
                    ['code' => 'TDK-DERM-03', 'name' => 'Post-Procedure Soothing Care & LED Light Therapy', 'price' => 200000]
                ]
            ]
        ];

        foreach ($tindakanParents as $tp) {
            // Parent in tindakan
            $exP = $this->db->table('tindakan')->where('code', $tp['code'])->get()->getRow();
            if ($exP) {
                $parentId = $exP->id;
            } else {
                $this->db->table('tindakan')->insert([
                    'category_id' => 2,
                    'parent_id'   => null,
                    'code'        => $tp['code'],
                    'name'        => $tp['name'],
                    'description' => $tp['desc'],
                    'price'       => null,
                    'status'      => 'active'
                ]);
                $parentId = $this->db->insertID();
            }

            // Parent in services
            $exSrvP = $this->db->table('services')->where('code', $tp['code'])->get()->getRow();
            if ($exSrvP) {
                $srvParentId = $exSrvP->id;
            } else {
                $this->db->table('services')->insert([
                    'code'        => $tp['code'],
                    'name'        => $tp['name'],
                    'category'    => 'tindakan',
                    'parent_id'   => null,
                    'status'      => 'active'
                ]);
                $srvParentId = $this->db->insertID();
            }

            // Sub items
            foreach ($tp['subs'] as $sub) {
                // in tindakan
                $exSub = $this->db->table('tindakan')->where('code', $sub['code'])->get()->getRow();
                if (!$exSub) {
                    $this->db->table('tindakan')->insert([
                        'category_id' => 2,
                        'parent_id'   => $parentId,
                        'code'        => $sub['code'],
                        'name'        => $sub['name'],
                        'description' => $sub['name'],
                        'price'       => $sub['price'],
                        'status'      => 'active'
                    ]);
                }

                // in services & service_prices
                $exSrvSub = $this->db->table('services')->where('code', $sub['code'])->get()->getRow();
                if (!$exSrvSub) {
                    $this->db->table('services')->insert([
                        'code'        => $sub['code'],
                        'name'        => $sub['name'],
                        'category'    => 'tindakan',
                        'parent_id'   => $srvParentId,
                        'status'      => 'active'
                    ]);
                    $subSrvId = $this->db->insertID();
                    $this->db->table('service_prices')->insert([
                        'service_id' => $subSrvId,
                        'price'      => $sub['price']
                    ]);
                }
            }
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
