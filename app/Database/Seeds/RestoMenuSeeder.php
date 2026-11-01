<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RestoMenuSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');
        $count = $db->table('restaurant_menus')->countAllResults();
        if ($count === 0) {
            $sampleMenus = [
                ['code' => 'MKN-01', 'name' => 'Nasi Diet Jantung Gizi Rendah Garam', 'description' => 'Nutrisi rendah natrium dengan sayuran kukus segar', 'category' => 'makanan', 'classification' => 'resep', 'price' => 45000, 'stock' => 50, 'is_active' => 1],
                ['code' => 'MKN-02', 'name' => 'Salad Buah Tinggi Protein & Serat', 'description' => 'Campuran apel, pir, almond dengan greek yoghurt dressing', 'category' => 'makanan', 'classification' => 'umum', 'price' => 35000, 'stock' => 40, 'is_active' => 1],
                ['code' => 'MKN-03', 'name' => 'Nasi Merah Ayam Panggang Herbal', 'description' => 'Dada ayam tanpa lemak dengan rempah kunyit dan nasi merah pulen', 'category' => 'makanan', 'classification' => 'umum', 'price' => 38000, 'stock' => 35, 'is_active' => 1],
                ['code' => 'MNM-01', 'name' => 'Jus Wortel & Apel Detox Murni', 'description' => 'Cold-pressed juice tanpa gula tambahan untuk cleansing hati', 'category' => 'minuman', 'classification' => 'umum', 'price' => 18000, 'stock' => 60, 'is_active' => 1],
                ['code' => 'MNM-02', 'name' => 'Teh Hijau Herbal Antioksidan', 'description' => 'Seduhan daun teh hijau organik kaya EGCG', 'category' => 'minuman', 'classification' => 'umum', 'price' => 12000, 'stock' => 80, 'is_active' => 1],
                ['code' => 'GZ-01', 'name' => 'Paket Diet DM & Hipertensi 1500 kkal', 'description' => 'Meal plan lengkap indeks glikemik rendah dan minim sodium', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 65000, 'stock' => 25, 'is_active' => 1],
                ['code' => 'GZ-02', 'name' => 'Paket Terapi Gizi Pasca Bedah', 'description' => 'Kaya albumin dan zinc untuk akselerasi penyembuhan luka', 'category' => 'diet_gizi', 'classification' => 'resep', 'price' => 70000, 'stock' => 20, 'is_active' => 1],
                ['code' => 'SKN-01', 'name' => 'Dermal Sunscreen Medis SPF 50+', 'description' => 'Tabir surya hipoalergenik untuk kulit sensitif klinis', 'category' => 'skincare', 'classification' => 'umum', 'price' => 85000, 'stock' => 30, 'is_active' => 1],
                ['code' => 'SKN-02', 'name' => 'Gentle Hydrating Cleanser 100ml', 'description' => 'Pembersih wajah pH balanced tanpa SLS dan pewangi', 'category' => 'skincare', 'classification' => 'umum', 'price' => 75000, 'stock' => 25, 'is_active' => 1],
                ['code' => 'SPL-01', 'name' => 'Vitamin C 500mg & Zinc Tablet', 'description' => 'Suplemen imunitas harian non-acidic ramah lambung', 'category' => 'suplemen', 'classification' => 'umum', 'price' => 45000, 'stock' => 50, 'is_active' => 1],
                ['code' => 'PKT-01', 'name' => 'Paket Sehat Bugar & Nutrisi Wellness', 'description' => 'Kombinasi Meal Plan Sehat + Jus Detox + Vitamin C Imun', 'category' => 'paket', 'classification' => 'umum', 'price' => 99000, 'stock' => 15, 'is_active' => 1]
            ];
            $db->table('restaurant_menus')->insertBatch($sampleMenus);
        }
    }
}

