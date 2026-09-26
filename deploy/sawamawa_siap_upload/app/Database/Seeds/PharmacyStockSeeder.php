<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PharmacyStockSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        $meds = [
            // =========================================================================
            // 1. OBAT BEBAS / OTC (Bisa dibeli langsung tanpa resep)
            // =========================================================================
            [
                'code'      => 'MED-PCT500',
                'name'      => 'Paracetamol 500mg Tablet',
                'type'      => 'bebas',
                'unit'      => 'tablet',
                'price'     => 1000,
                'min_stock' => 100,
                'batches'   => [
                    ['batch_no' => 'BCH-PCT26A', 'buy_price' => 450, 'stock' => 500, 'expired_date' => '2027-12-31'],
                    ['batch_no' => 'BCH-PCT26B', 'buy_price' => 480, 'stock' => 350, 'expired_date' => '2028-06-30']
                ]
            ],
            [
                'code'      => 'MED-IBU400',
                'name'      => 'Ibuprofen 400mg Kaplet',
                'type'      => 'bebas',
                'unit'      => 'kaplet',
                'price'     => 1500,
                'min_stock' => 50,
                'batches'   => [
                    ['batch_no' => 'BCH-IBU26', 'buy_price' => 750, 'stock' => 300, 'expired_date' => '2027-10-15']
                ]
            ],
            [
                'code'      => 'MED-ANTASIDA',
                'name'      => 'Antasida Doen Tablet Kunyah',
                'type'      => 'bebas',
                'unit'      => 'tablet',
                'price'     => 800,
                'min_stock' => 80,
                'batches'   => [
                    ['batch_no' => 'BCH-ATD26', 'buy_price' => 350, 'stock' => 400, 'expired_date' => '2027-08-20']
                ]
            ],
            [
                'code'      => 'MED-PROMAG',
                'name'      => 'Promag Tablet Kunyah (Dus/Blister)',
                'type'      => 'bebas',
                'unit'      => 'tablet',
                'price'     => 1200,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-PMG26', 'buy_price' => 650, 'stock' => 250, 'expired_date' => '2028-01-10']
                ]
            ],
            [
                'code'      => 'MED-OBH100',
                'name'      => 'OBH Tropica Plus Anak Sirup 100ml',
                'type'      => 'bebas',
                'unit'      => 'botol',
                'price'     => 22000,
                'min_stock' => 15,
                'batches'   => [
                    ['batch_no' => 'BCH-OBH26', 'buy_price' => 16000, 'stock' => 45, 'expired_date' => '2027-11-20']
                ]
            ],
            [
                'code'      => 'MED-SANMOL-SYR',
                'name'      => 'Sanmol Paracetamol Sirup 60ml',
                'type'      => 'bebas',
                'unit'      => 'botol',
                'price'     => 25000,
                'min_stock' => 20,
                'batches'   => [
                    ['batch_no' => 'BCH-SNM26', 'buy_price' => 18500, 'stock' => 50, 'expired_date' => '2028-03-15']
                ]
            ],
            [
                'code'      => 'MED-VITC500',
                'name'      => 'Vitamin C 500mg (IPI / Enervon-C)',
                'type'      => 'bebas',
                'unit'      => 'tablet',
                'price'     => 1500,
                'min_stock' => 100,
                'batches'   => [
                    ['batch_no' => 'BCH-VTC26', 'buy_price' => 800, 'stock' => 600, 'expired_date' => '2028-05-10']
                ]
            ],
            [
                'code'      => 'MED-BETADINE60',
                'name'      => 'Betadine Antiseptic Solution 60ml',
                'type'      => 'bebas',
                'unit'      => 'botol',
                'price'     => 35000,
                'min_stock' => 10,
                'batches'   => [
                    ['batch_no' => 'BCH-BTD26', 'buy_price' => 26000, 'stock' => 30, 'expired_date' => '2028-09-30']
                ]
            ],
            [
                'code'      => 'MED-MINYAK-KAYU',
                'name'      => 'Minyak Kayu Putih Cap Lang 60ml',
                'type'      => 'bebas',
                'unit'      => 'botol',
                'price'     => 28000,
                'min_stock' => 15,
                'batches'   => [
                    ['batch_no' => 'BCH-MKP26', 'buy_price' => 21000, 'stock' => 40, 'expired_date' => '2029-01-01']
                ]
            ],
            [
                'code'      => 'MED-ORALIT',
                'name'      => 'Oralit Garam Rehidrasi Sachet',
                'type'      => 'bebas',
                'unit'      => 'sachet',
                'price'     => 2000,
                'min_stock' => 50,
                'batches'   => [
                    ['batch_no' => 'BCH-ORL26', 'buy_price' => 900, 'stock' => 200, 'expired_date' => '2028-04-12']
                ]
            ],
            [
                'code'      => 'MED-DIAPET',
                'name'      => 'Diapet Kapsul Herbal Antidiare',
                'type'      => 'bebas',
                'unit'      => 'kapsul',
                'price'     => 1500,
                'min_stock' => 40,
                'batches'   => [
                    ['batch_no' => 'BCH-DPT26', 'buy_price' => 800, 'stock' => 150, 'expired_date' => '2027-12-01']
                ]
            ],
            [
                'code'      => 'MED-INSTO',
                'name'      => 'Insto Regular Tetes Mata 7.5ml',
                'type'      => 'bebas',
                'unit'      => 'botol',
                'price'     => 18500,
                'min_stock' => 15,
                'batches'   => [
                    ['batch_no' => 'BCH-INS26', 'buy_price' => 13500, 'stock' => 35, 'expired_date' => '2027-09-15']
                ]
            ],
            [
                'code'      => 'MED-HYDROCORT',
                'name'      => 'Hydrocortisone Cream 2.5% 5gr',
                'type'      => 'bebas',
                'unit'      => 'tube',
                'price'     => 12000,
                'min_stock' => 15,
                'batches'   => [
                    ['batch_no' => 'BCH-HYD26', 'buy_price' => 7500, 'stock' => 45, 'expired_date' => '2027-11-01']
                ]
            ],

            // =========================================================================
            // 2. OBAT KERAS & RESEP DOKTER (Ethical Drugs)
            // =========================================================================
            [
                'code'      => 'MED-AMX500',
                'name'      => 'Amoxicillin 500mg Kaplet',
                'type'      => 'keras',
                'unit'      => 'kaplet',
                'price'     => 1500,
                'min_stock' => 100,
                'batches'   => [
                    ['batch_no' => 'BCH-AMX26A', 'buy_price' => 700, 'stock' => 450, 'expired_date' => '2027-12-31'],
                    ['batch_no' => 'BCH-AMX26B', 'buy_price' => 750, 'stock' => 300, 'expired_date' => '2028-06-30']
                ]
            ],
            [
                'code'      => 'MED-CFX100',
                'name'      => 'Cefixime 100mg Kapsul',
                'type'      => 'keras',
                'unit'      => 'kapsul',
                'price'     => 4500,
                'min_stock' => 50,
                'batches'   => [
                    ['batch_no' => 'BCH-CFX26', 'buy_price' => 2800, 'stock' => 200, 'expired_date' => '2028-02-18']
                ]
            ],
            [
                'code'      => 'MED-CIP500',
                'name'      => 'Ciprofloxacin 500mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 2000,
                'min_stock' => 40,
                'batches'   => [
                    ['batch_no' => 'BCH-CIP26', 'buy_price' => 1100, 'stock' => 250, 'expired_date' => '2027-10-30']
                ]
            ],
            [
                'code'      => 'MED-AML5',
                'name'      => 'Amlodipine 5mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1200,
                'min_stock' => 80,
                'batches'   => [
                    ['batch_no' => 'BCH-AML5-26', 'buy_price' => 500, 'stock' => 500, 'expired_date' => '2028-04-25']
                ]
            ],
            [
                'code'      => 'MED-AML10',
                'name'      => 'Amlodipine 10mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1800,
                'min_stock' => 80,
                'batches'   => [
                    ['batch_no' => 'BCH-AML10-26', 'buy_price' => 800, 'stock' => 400, 'expired_date' => '2028-05-15']
                ]
            ],
            [
                'code'      => 'MED-CAP25',
                'name'      => 'Captopril 25mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 800,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-CAP26', 'buy_price' => 350, 'stock' => 350, 'expired_date' => '2027-11-10']
                ]
            ],
            [
                'code'      => 'MED-MET500',
                'name'      => 'Metformin HCl 500mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1000,
                'min_stock' => 100,
                'batches'   => [
                    ['batch_no' => 'BCH-MET26', 'buy_price' => 450, 'stock' => 600, 'expired_date' => '2028-07-20']
                ]
            ],
            [
                'code'      => 'MED-GLI2',
                'name'      => 'Glimepiride 2mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 2200,
                'min_stock' => 40,
                'batches'   => [
                    ['batch_no' => 'BCH-GLI26', 'buy_price' => 1200, 'stock' => 200, 'expired_date' => '2027-09-05']
                ]
            ],
            [
                'code'      => 'MED-OMP20',
                'name'      => 'Omeprazole 20mg Kapsul',
                'type'      => 'keras',
                'unit'      => 'kapsul',
                'price'     => 2500,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-OMP26', 'buy_price' => 1100, 'stock' => 350, 'expired_date' => '2028-03-30']
                ]
            ],
            [
                'code'      => 'MED-LAN30',
                'name'      => 'Lansoprazole 30mg Kapsul',
                'type'      => 'keras',
                'unit'      => 'kapsul',
                'price'     => 3500,
                'min_stock' => 50,
                'batches'   => [
                    ['batch_no' => 'BCH-LAN26', 'buy_price' => 1800, 'stock' => 250, 'expired_date' => '2028-06-15']
                ]
            ],
            [
                'code'      => 'MED-CTZ10',
                'name'      => 'Cetirizine 10mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1200,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-CTZ26', 'buy_price' => 500, 'stock' => 400, 'expired_date' => '2028-08-10']
                ]
            ],
            [
                'code'      => 'MED-DEX05',
                'name'      => 'Dexamethasone 0.5mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 600,
                'min_stock' => 100,
                'batches'   => [
                    ['batch_no' => 'BCH-DEX26', 'buy_price' => 250, 'stock' => 500, 'expired_date' => '2027-10-25']
                ]
            ],
            [
                'code'      => 'MED-SAL2',
                'name'      => 'Salbutamol 2mg Tablet',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 800,
                'min_stock' => 50,
                'batches'   => [
                    ['batch_no' => 'BCH-SAL26', 'buy_price' => 350, 'stock' => 300, 'expired_date' => '2027-12-15']
                ]
            ],
            [
                'code'      => 'MED-ASAM-MEF500',
                'name'      => 'Asam Mefenamat 500mg Kaplet',
                'type'      => 'keras',
                'unit'      => 'kaplet',
                'price'     => 1500,
                'min_stock' => 80,
                'batches'   => [
                    ['batch_no' => 'BCH-AMF26', 'buy_price' => 700, 'stock' => 450, 'expired_date' => '2028-01-20']
                ]
            ],
            [
                'code'      => 'MED-ALLOP100',
                'name'      => 'Allopurinol 100mg Tablet (Asam Urat)',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1200,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-ALP26', 'buy_price' => 550, 'stock' => 350, 'expired_date' => '2028-05-15']
                ]
            ],
            [
                'code'      => 'MED-SIMV10',
                'name'      => 'Simvastatin 10mg Tablet (Kolesterol)',
                'type'      => 'keras',
                'unit'      => 'tablet',
                'price'     => 1800,
                'min_stock' => 60,
                'batches'   => [
                    ['batch_no' => 'BCH-SMV26', 'buy_price' => 850, 'stock' => 300, 'expired_date' => '2028-04-10']
                ]
            ]
        ];

        foreach ($meds as $m) {
            $batches = $m['batches'];
            unset($m['batches']);

            $existingMed = $this->db->table('medicines')->where('code', $m['code'])->get()->getRow();
            if ($existingMed) {
                $medId = $existingMed->id;
                $this->db->table('medicines')->where('id', $medId)->update($m);
            } else {
                $this->db->table('medicines')->insert($m);
                $medId = $this->db->insertID();
            }

            foreach ($batches as $b) {
                $b['medicine_id'] = $medId;
                $existingBatch = $this->db->table('medicine_batches')
                                          ->where('medicine_id', $medId)
                                          ->where('batch_no', $b['batch_no'])
                                          ->get()
                                          ->getRow();
                if ($existingBatch) {
                    $this->db->table('medicine_batches')->where('id', $existingBatch->id)->update($b);
                } else {
                    $this->db->table('medicine_batches')->insert($b);
                }
            }
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
