<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeRestoHealthyPosSystem extends Migration
{
    public function up()
    {
        // 1. Modify restaurant_orders to support order number without table and add clinical & payment fields
        $fieldsOrder = $this->db->getFieldNames('restaurant_orders');

        // Allow NULL table_id
        $this->db->query("ALTER TABLE restaurant_orders MODIFY table_id INT NULL;");

        // Add additional columns
        if (!in_array('order_type', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN order_type ENUM('umum', 'diet_pasien') DEFAULT 'umum' AFTER order_no;");
        }
        if (!in_array('customer_name', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN customer_name VARCHAR(100) NULL AFTER order_type;");
        }
        if (!in_array('customer_phone', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN customer_phone VARCHAR(20) NULL AFTER customer_name;");
        }
        if (!in_array('diet_instructions', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN diet_instructions TEXT NULL AFTER customer_phone;");
        }
        if (!in_array('payment_status', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN payment_status ENUM('unpaid', 'paid', 'billed_to_clinic') DEFAULT 'unpaid' AFTER status;");
        }
        if (!in_array('payment_method', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN payment_method VARCHAR(50) NULL AFTER payment_status;");
        }
        if (!in_array('total_amount', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN total_amount DECIMAL(15,2) DEFAULT 0.00 AFTER payment_method;");
        }
        if (!in_array('discount_amount', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN discount_amount DECIMAL(15,2) DEFAULT 0.00 AFTER total_amount;");
        }
        if (!in_array('grand_total', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN grand_total DECIMAL(15,2) DEFAULT 0.00 AFTER discount_amount;");
        }
        if (!in_array('paid_amount', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN paid_amount DECIMAL(15,2) DEFAULT 0.00 AFTER grand_total;");
        }
        if (!in_array('change_amount', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN change_amount DECIMAL(15,2) DEFAULT 0.00 AFTER paid_amount;");
        }
        if (!in_array('cashier_id', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN cashier_id INT NULL AFTER change_amount;");
        }
        if (!in_array('notes', $fieldsOrder)) {
            $this->db->query("ALTER TABLE restaurant_orders ADD COLUMN notes TEXT NULL AFTER cashier_id;");
        }

        // 2. Modify restaurant_menus to support skincare, supplements, and clinical diet packages
        $fieldsMenu = $this->db->getFieldNames('restaurant_menus');
        $this->db->query("ALTER TABLE restaurant_menus MODIFY category VARCHAR(50) NOT NULL;");

        if (!in_array('code', $fieldsMenu)) {
            $this->db->query("ALTER TABLE restaurant_menus ADD COLUMN code VARCHAR(30) NULL AFTER id;");
        }
        if (!in_array('description', $fieldsMenu)) {
            $this->db->query("ALTER TABLE restaurant_menus ADD COLUMN description TEXT NULL AFTER name;");
        }
        if (!in_array('stock', $fieldsMenu)) {
            $this->db->query("ALTER TABLE restaurant_menus ADD COLUMN stock INT DEFAULT 100 AFTER price;");
        }
        if (!in_array('is_active', $fieldsMenu)) {
            $this->db->query("ALTER TABLE restaurant_menus ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER stock;");
        }

        // 3. Seed rich default healthy menus, skincare, and diet nutrition items
        $defaultItems = [
            // Makanan Sehat
            ['code' => 'MKN-01', 'name' => 'Nasi Merah Dada Ayam Panggang Herbal', 'category' => 'makanan', 'classification' => 'umum', 'price' => 38000, 'description' => 'Tinggi protein, rendah lemak jenuh dengan tumis brokoli wortel'],
            ['code' => 'MKN-02', 'name' => 'Salmon Panggang Lemon & Mashed Potato Sehat', 'category' => 'makanan', 'classification' => 'umum', 'price' => 55000, 'description' => 'Kaya asam lemak Omega-3 dan antioksidan alami'],
            ['code' => 'MKN-03', 'name' => 'Sup Ayam Jamur Tofu Bening Organik', 'category' => 'makanan', 'classification' => 'umum', 'price' => 28000, 'description' => 'Kuah kaldu alami tanpa MSG, ramah pencernaan dan lambung'],
            ['code' => 'MKN-04', 'name' => 'Salad Sayur Segar Dressing Extra Virgin Olive Oil', 'category' => 'makanan', 'classification' => 'umum', 'price' => 25000, 'description' => 'Kombinasi selada romaine, tomat ceri, jagung, dan alpukat segar'],
            ['code' => 'MKN-05', 'name' => 'Bubur Oat Gizi Ayam Suwir & Telur Rebus', 'category' => 'makanan', 'classification' => 'umum', 'price' => 22000, 'description' => 'Kaya serat beta-glukan untuk menjaga kestabilan gula darah dan kolesterol'],

            // Minuman Sehat & Detox
            ['code' => 'MNM-01', 'name' => 'Cold-Pressed Detox Green Juice (Bayam, Apel, Timun)', 'category' => 'minuman', 'classification' => 'umum', 'price' => 24000, 'description' => 'Membantu proses detoksifikasi tubuh dan meningkatkan kesegaran seluler'],
            ['code' => 'MNM-02', 'name' => 'Fresh Beetroot & Apple Energy Booster Juice', 'category' => 'minuman', 'classification' => 'umum', 'price' => 25000, 'description' => 'Meningkatkan sirkulasi darah dan stamina alami tubuh'],
            ['code' => 'MNM-03', 'name' => 'Wedang Jahe Merah & Madu Hutan Murni', 'category' => 'minuman', 'classification' => 'umum', 'price' => 18000, 'description' => 'Menghangatkan tubuh, meredakan inflamasi dan meningkatkan imun'],
            ['code' => 'MNM-04', 'name' => 'Infused Water Lemon Mint & Chia Seeds 500ml', 'category' => 'minuman', 'classification' => 'umum', 'price' => 15000, 'description' => 'Hidrasi optimal kaya serat larut dan vitamin C alami'],

            // Paket Diet Spesialis Gizi Klinis (Prescription / Diet Referral)
            ['code' => 'GIZI-DM', 'name' => 'Paket Diet Diabetes Melitus 1500 kkal (3x Makan)', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 85000, 'description' => 'Formula gizi terkontrol indeks glikemik rendah terukur standar spesialis gizi'],
            ['code' => 'GIZI-HT', 'name' => 'Paket Diet Rendah Garam & Hipertensi (DASH Diet)', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 80000, 'description' => 'Natrium < 1200mg/hari dengan asupan kalium dan magnesium optimal'],
            ['code' => 'GIZI-PURIN', 'name' => 'Paket Diet Rendah Purin & Asam Urat', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 75000, 'description' => 'Bebas jeroan, daging merah dan ekstrak ragi untuk stabilisasi kadar asam urat'],
            ['code' => 'GIZI-POSTOP', 'name' => 'Paket Pemulihan Tinggi Protein Pasca Tindakan / Bedah', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 95000, 'description' => 'Diperkaya albumin ikan gabus dan mikronutrien untuk regenerasi jaringan'],
            ['code' => 'GIZI-LAMBUNG', 'name' => 'Paket Diet Lambung Halus / Gastritis & GERD Friendly', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 70000, 'description' => 'Tekstur lembut non-asam non-pedas untuk proteksi mukosa lambung'],

            // Skincare & Dermal Care Medis
            ['code' => 'SKIN-01', 'name' => 'Dermatological Broad-Spectrum Sunscreen SPF50+ PA++++', 'category' => 'skincare', 'classification' => 'umum', 'price' => 125000, 'description' => 'Formula non-komedogenik medis untuk perlindungan UV harian dan pasca laser'],
            ['code' => 'SKIN-02', 'name' => 'Gentle Skin Barrier Foam Cleanser 100ml', 'category' => 'skincare', 'classification' => 'umum', 'price' => 85000, 'description' => 'pH seimbang 5.5 dengan ekstrak oat dan ceramide, aman untuk kulit sensitif'],
            ['code' => 'SKIN-03', 'name' => 'Hydrating Ceramide & Hyaluronic Gel Moisturizer 50gr', 'category' => 'skincare', 'classification' => 'umum', 'price' => 110000, 'description' => 'Mengunci kelembapan mendalam dan memperkuat skin barrier'],
            ['code' => 'SKIN-04', 'name' => 'Post-Procedure Calming & Soothing Centella Gel', 'category' => 'skincare', 'classification' => 'umum', 'price' => 95000, 'description' => 'Meredakan kemerahan dan iritasi setelah tindakan facial atau peeling medis'],
            ['code' => 'SKIN-05', 'name' => 'Medical Brightening Serum Alpha Arbutin & Niacinamide 10%', 'category' => 'skincare', 'classification' => 'umum', 'price' => 140000, 'description' => 'Membantu mencerahkan flek hitam dan meratakan warna kulit'],

            // Suplemen & Nutrasetikal
            ['code' => 'SUP-01', 'name' => 'Pure Whey Protein Isolate Medical Grade Sachet (Box 10s)', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 175000, 'description' => '25g protein murni per sachet tanpa pemanis buatan untuk terapi nutrisi'],
            ['code' => 'SUP-02', 'name' => 'High Potency Multivitamin & Zinc Immunity Booster (30 Caps)', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 90000, 'description' => 'Kombinasi lengkap vitamin A, B-Complex, C, D3, E, dan Zinc elemental'],
            ['code' => 'SUP-03', 'name' => 'Deep Sea Fish Oil Omega-3 1000mg (60 Softgels)', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 135000, 'description' => 'EPA 360mg / DHA 240mg untuk kesehatan kardiovaskular dan daya ingat'],
            ['code' => 'SUP-04', 'name' => 'Marine Collagen Peptide + Glutathione Drink (10 Sachets)', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 160000, 'description' => 'Mendukung elastisitas kulit dan kesehatan sendi'],
            ['code' => 'SUP-05', 'name' => 'Probiotics Multi-Strain Digestive Health (30 Kapsul)', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 120000, 'description' => '10 Miliar CFU probiotik hidup untuk keseimbangan flora usus']
        ];

        foreach ($defaultItems as $item) {
            $existing = $this->db->table('restaurant_menus')->where('name', $item['name'])->get()->getRow();
            if ($existing) {
                $this->db->table('restaurant_menus')->where('id', $existing->id)->update($item);
            } else {
                $this->db->table('restaurant_menus')->insert($item);
            }
        }
    }

    public function down()
    {
        // No down needed
    }
}
