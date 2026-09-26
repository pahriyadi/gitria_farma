<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks untuk seeding
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Seed Categories (Kategori Utama)
        $categories = [
            ['id' => 1, 'name' => 'Poliklinik',      'description' => 'Kategori layanan poli klinik spesialis'],
            ['id' => 2, 'name' => 'Tindakan Medis',  'description' => 'Kategori tindakan medis umum & spesialis'],
            ['id' => 3, 'name' => 'Estetika',        'description' => 'Kategori layanan estetika & kecantikan'],
            ['id' => 4, 'name' => 'Hairstudio',      'description' => 'Kategori layanan perawatan rambut'],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 2. Seed Polikliniks (Tabel Khusus Data Poliklinik)
        $polikliniks = [
            ['id' => 1, 'category_id' => 1, 'name' => 'Spesialis Jantung', 'description' => 'Poli spesialis pelayanan kesehatan jantung & pembuluh darah', 'status' => 'active'],
            ['id' => 2, 'category_id' => 1, 'name' => 'Spesialis Gizi',    'description' => 'Poli konsultasi gizi terintegrasi menu diet Restoran',        'status' => 'active'],
            ['id' => 3, 'category_id' => 1, 'name' => 'Poli Gigi',         'description' => 'Poli pelayanan kesehatan gigi & mulut',                        'status' => 'active'],
            ['id' => 4, 'category_id' => 1, 'name' => 'Poli Umum',         'description' => 'Poli pelayanan kesehatan umum tingkat dasar',                  'status' => 'active'],
        ];
        $this->db->table('polikliniks')->insertBatch($polikliniks);

        // 3. Seed Tindakan (Tindakan Induk / Parent)
        $tindakanInduk = [
            ['id' => 1,  'category_id' => 2, 'parent_id' => null, 'code' => 'TND-001', 'name' => 'Konsultasi Dokter Spesialis', 'description' => 'Konsultasi dengan dokter spesialis', 'price' => 0,      'status' => 'active'],
            ['id' => 2,  'category_id' => 2, 'parent_id' => null, 'code' => 'TND-002', 'name' => 'Tindakan EKG',                'description' => 'Rekam jantung elektrokardiografi',  'price' => 100000, 'status' => 'active'],
            ['id' => 3,  'category_id' => 4, 'parent_id' => null, 'code' => 'TND-003', 'name' => 'Hairstudio',                 'description' => 'Layanan perawatan rambut lengkap',   'price' => 0,      'status' => 'active'],
            ['id' => 4,  'category_id' => 3, 'parent_id' => null, 'code' => 'TND-004', 'name' => 'Estetika & Kecantikan',      'description' => 'Layanan estetika & perawatan kulit', 'price' => 0,      'status' => 'active'],
        ];
        $this->db->table('tindakan')->insertBatch($tindakanInduk);

        // 3b. Seed Tindakan Sub-layanan (Child / Sub-tindakan dengan parent_id)
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









        // 4. Seed Users (passwords hashed)
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
        $users = [
            ['id' => 1, 'username' => 'superadmin', 'email' => 'admin@arm.co.id', 'password' => $passwordHash, 'role_id' => 1, 'status' => 'active'],
            ['id' => 2, 'username' => 'direktur', 'email' => 'direksi@arm.co.id', 'password' => $passwordHash, 'role_id' => 3, 'status' => 'active'],
            ['id' => 3, 'username' => 'dr_andi', 'email' => 'andi.sp@arm.co.id', 'password' => $passwordHash, 'role_id' => 5, 'status' => 'active'],
            ['id' => 4, 'username' => 'ns_rina', 'email' => 'rina.pw@arm.co.id', 'password' => $passwordHash, 'role_id' => 6, 'status' => 'active'],
            ['id' => 5, 'username' => 'kasir_siti', 'email' => 'siti.ks@arm.co.id', 'password' => $passwordHash, 'role_id' => 7, 'status' => 'active'],
            ['id' => 6, 'username' => 'keuangan_budi', 'email' => 'budi.keu@arm.co.id', 'password' => $passwordHash, 'role_id' => 10, 'status' => 'active'],
            ['id' => 7, 'username' => 'accounting_edi', 'email' => 'edi.acc@arm.co.id', 'password' => $passwordHash, 'role_id' => 11, 'status' => 'active'],
            ['id' => 8, 'username' => 'resto_santi', 'email' => 'santi.resto@arm.co.id', 'password' => $passwordHash, 'role_id' => 14, 'status' => 'active'],
            ['id' => 9, 'username' => 'chef_joko', 'email' => 'joko.chef@arm.co.id', 'password' => $passwordHash, 'role_id' => 15, 'status' => 'active']
        ];
        $this->db->table('users')->insertBatch($users);

        // 5. Seed Polyclinics (Poli)
        $polyclinics = [
            ['id' => 1, 'name' => 'Spesialis Jantung', 'description' => 'Poli spesialis pelayanan kesehatan jantung & pembuluh darah', 'status' => 'active'],
            ['id' => 2, 'name' => 'Spesialis Gizi', 'description' => 'Poli konsultasi gizi terintegrasi menu diet Restoran', 'status' => 'active'],
            ['id' => 3, 'name' => 'Poli Gigi', 'description' => 'Poli pelayanan kesehatan gigi & mulut', 'status' => 'active'],
            ['id' => 4, 'name' => 'Poli Umum', 'description' => 'Poli pelayanan kesehatan umum tingkat dasar', 'status' => 'active']
        ];
        $this->db->table('polyclinics')->insertBatch($polyclinics);

        // 6. Seed Doctors
        $doctors = [
            ['id' => 1, 'nik_employee' => 'EMP-DOK-001', 'name' => 'dr. Andi Wijaya, Sp.PD', 'polyclinic_id' => 1, 'sip_number' => 'SIP/440/123/DINKES', 'str_number' => 'STR-12345678', 'str_expiry' => '2029-12-31', 'status' => 'active'],
            ['id' => 2, 'nik_employee' => 'EMP-DOK-002', 'name' => 'dr. Siti Rahma, Sp.GK', 'polyclinic_id' => 2, 'sip_number' => 'SIP/440/124/DINKES', 'str_number' => 'STR-87654321', 'str_expiry' => '2030-05-20', 'status' => 'active'],
            ['id' => 3, 'nik_employee' => 'EMP-DOK-003', 'name' => 'drg. Maya Putri', 'polyclinic_id' => 3, 'sip_number' => 'SIP/440/125/DINKES', 'str_number' => 'STR-22446688', 'str_expiry' => '2028-09-15', 'status' => 'active']
        ];
        $this->db->table('doctors')->insertBatch($doctors);

        // 7. Seed Nurses
        $nurses = [
            ['id' => 1, 'nik_employee' => 'EMP-NRS-001', 'name' => 'Ns. Rina Kartika, S.Kep', 'polyclinic_id' => 1, 'status' => 'active'],
            ['id' => 2, 'nik_employee' => 'EMP-NRS-002', 'name' => 'Ns. Ahmad Fauzi, S.Kep', 'polyclinic_id' => 2, 'status' => 'active']
        ];
        $this->db->table('nurses')->insertBatch($nurses);

        // 8. Seed Services
        $services = [
            ['id' => 1, 'code' => 'SRV-001', 'name' => 'Konsultasi Spesialis Jantung', 'category' => 'klinik', 'status' => 'active'],
            ['id' => 2, 'code' => 'SRV-002', 'name' => 'Konsultasi Spesialis Gizi', 'category' => 'klinik', 'status' => 'active'],
            ['id' => 3, 'code' => 'SRV-003', 'name' => 'Pemeriksaan EKG Jantung', 'category' => 'klinik', 'status' => 'active'],
            ['id' => 4, 'code' => 'SRV-004', 'name' => 'Tindakan Hairstudio (Rambut Rontok)', 'category' => 'tindakan', 'status' => 'active'],
            ['id' => 5, 'code' => 'SRV-005', 'name' => 'Tindakan Estetik (Facial Treatment)', 'category' => 'tindakan', 'status' => 'active']
        ];
        $this->db->table('services')->insertBatch($services);

        // 9. Seed Service Prices
        $servicePrices = [
            ['service_id' => 1, 'price' => 150000.00],
            ['service_id' => 2, 'price' => 150000.00],
            ['service_id' => 3, 'price' => 100000.00],
            ['service_id' => 4, 'price' => 250000.00],
            ['service_id' => 5, 'price' => 200000.00]
        ];
        $this->db->table('service_prices')->insertBatch($servicePrices);

        // 10. Seed Lab Tests
        $labTests = [
            ['id' => 1, 'code' => 'LAB-CBC', 'name' => 'Darah Lengkap (Complete Blood Count)', 'reference_range' => 'HB: 13-16 g/dL, Leukosit: 4000-10000', 'price' => 120000.00],
            ['id' => 2, 'code' => 'LAB-GLU', 'name' => 'Gula Darah Puasa (GDP)', 'reference_range' => '70 - 110 mg/dL', 'price' => 45000.00]
        ];
        $this->db->table('lab_tests')->insertBatch($labTests);

        // 11. Seed Medicines
        $medicines = [
            ['id' => 1, 'code' => 'MED-001', 'name' => 'Paracetamol 500mg Tablet (Box)', 'type' => 'bebas', 'unit' => 'Box', 'price' => 25000.00, 'min_stock' => 10, 'status' => 'active'],
            ['id' => 2, 'code' => 'MED-002', 'name' => 'Amoxicillin 500mg (Strip)', 'type' => 'keras', 'unit' => 'Strip', 'price' => 20000.00, 'min_stock' => 15, 'status' => 'active'],
            ['id' => 3, 'code' => 'MED-003', 'name' => 'Cefadroxil 500mg Capsule (Box)', 'type' => 'keras', 'unit' => 'Box', 'price' => 55000.00, 'min_stock' => 5, 'status' => 'active'],
            ['id' => 4, 'code' => 'MED-004', 'name' => 'Vitamin C 1000mg Tablet (Botol)', 'type' => 'bebas', 'unit' => 'Botol', 'price' => 65000.00, 'min_stock' => 8, 'status' => 'active']
        ];
        $this->db->table('medicines')->insertBatch($medicines);

        // 12. Seed Medicine Batches
        $medicineBatches = [
            ['id' => 1, 'medicine_id' => 1, 'batch_no' => 'BCH-2026-001', 'buy_price' => 15000.00, 'stock' => 50, 'expired_date' => '2027-11-20'],
            ['id' => 2, 'medicine_id' => 1, 'batch_no' => 'BCH-2026-002', 'buy_price' => 15000.00, 'stock' => 20, 'expired_date' => '2026-10-15'], // Expired warning
            ['id' => 3, 'medicine_id' => 2, 'batch_no' => 'BCH-2026-003', 'buy_price' => 12000.00, 'stock' => 40, 'expired_date' => '2027-05-15']
        ];
        $this->db->table('medicine_batches')->insertBatch($medicineBatches);

        // 13. Seed Suppliers
        $suppliers = [
            ['id' => 1, 'code' => 'SUPP-001', 'name' => 'PT Kalbe Farma Tbk', 'address' => 'Jl. Industri Raya No. 45, Jakarta', 'phone' => '081199887766', 'bank_name' => 'BCA', 'bank_account' => '123-456-7890'],
            ['id' => 2, 'code' => 'SUPP-002', 'name' => 'PT Kimia Farma Trading & Distribution', 'address' => 'Jl. Veteran No. 12, Mataram', 'phone' => '085611223344', 'bank_name' => 'Mandiri', 'bank_account' => '161-00-1122-3344']
        ];
        $this->db->table('suppliers')->insertBatch($suppliers);

        // 14. Seed Restaurant Tables
        $restaurantTables = [
            ['id' => 1, 'table_no' => 'Meja 01', 'capacity' => 4, 'status' => 'empty'],
            ['id' => 2, 'table_no' => 'Meja 02', 'capacity' => 2, 'status' => 'empty'],
            ['id' => 3, 'table_no' => 'Meja 03', 'capacity' => 6, 'status' => 'empty']
        ];
        $this->db->table('restaurant_tables')->insertBatch($restaurantTables);

        // 15. Seed Restaurant Menus
        $restaurantMenus = [
            ['id' => 1, 'name' => 'Nasi Diet Jantung Gizi Rendah Garam', 'category' => 'makanan', 'classification' => 'resep', 'price' => 45000.00],
            ['id' => 2, 'name' => 'Salad Buah Tinggi Protein & Serat', 'category' => 'makanan', 'classification' => 'resep', 'price' => 35000.00],
            ['id' => 3, 'name' => 'Nasi Goreng Spesial Era', 'category' => 'makanan', 'classification' => 'umum', 'price' => 30000.00],
            ['id' => 4, 'name' => 'Jus Wortel Murni', 'category' => 'minuman', 'classification' => 'umum', 'price' => 15000.00]
        ];
        $this->db->table('restaurant_menus')->insertBatch($restaurantMenus);

        // 16. Seed Accounts (Chart of Accounts / COA)
        $accounts = [
            ['id' => 1, 'code' => '1-101', 'name' => 'Kas Kasir Utama', 'type' => 'asset', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 5000000.00],
            ['id' => 2, 'code' => '1-102', 'name' => 'Bank BCA Operasional', 'type' => 'asset', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 45000000.00],
            ['id' => 3, 'code' => '1-103', 'name' => 'Bank Mandiri QRIS', 'type' => 'asset', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 12500000.00],
            ['id' => 4, 'code' => '1-201', 'name' => 'Persediaan Obat-obatan', 'type' => 'asset', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 15000000.00],
            ['id' => 5, 'code' => '1-301', 'name' => 'Piutang Klaim BPJS', 'type' => 'asset', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 35000000.00],
            ['id' => 6, 'code' => '2-101', 'name' => 'Hutang Dagang Supplier PBF', 'type' => 'liability', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 6500000.00],
            ['id' => 7, 'code' => '2-102', 'name' => 'Hutang Komisi Dokter', 'type' => 'liability', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 8, 'code' => '3-101', 'name' => 'Modal Disetor Saham', 'type' => 'equity', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 100000000.00],
            ['id' => 9, 'code' => '4-101', 'name' => 'Pendapatan Pelayanan Klinik', 'type' => 'revenue', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 10, 'code' => '4-102', 'name' => 'Pendapatan Apotek Farmasi', 'type' => 'revenue', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 11, 'code' => '4-103', 'name' => 'Pendapatan POS Restoran', 'type' => 'revenue', 'parent_id' => null, 'normal_balance' => 'credit', 'balance' => 0.00],
            ['id' => 12, 'code' => '5-101', 'name' => 'Beban HPP Obat Farmasi', 'type' => 'expense', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 0.00],
            ['id' => 13, 'code' => '6-101', 'name' => 'Beban Gaji Staf Karyawan', 'type' => 'expense', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 0.00],
            ['id' => 14, 'code' => '6-102', 'name' => 'Beban Komisi & Jasa Medis Dokter', 'type' => 'expense', 'parent_id' => null, 'normal_balance' => 'debit', 'balance' => 0.00]
        ];
        $this->db->table('accounts')->insertBatch($accounts);

        // 17. Seed Transaction Account Mappings
        $mappings = [
            ['transaction_type' => 'CLINIC_PAYMENT', 'debit_account_id' => 3, 'credit_account_id' => 9], // QRIS to Revenue Clinic
            ['transaction_type' => 'PHARMACY_SALE', 'debit_account_id' => 1, 'credit_account_id' => 10], // Cash to Revenue Pharmacy
            ['transaction_type' => 'RESTO_SALE', 'debit_account_id' => 2, 'credit_account_id' => 11], // EDC Bank to Revenue Resto
            ['transaction_type' => 'FEE_EXPENSE', 'debit_account_id' => 14, 'credit_account_id' => 7] // Fee Expense to Payable Commission
        ];
        $this->db->table('transaction_account_mappings')->insertBatch($mappings);

        // 18. Seed Fee Rules (e.g. EKG Tindakan gets 60% fee to doctor role)
        $feeRules = [
            ['service_id' => 1, 'role_id' => 5, 'percentage' => 60.00, 'flat_fee' => 0.00, 'status' => 'active'], // 60% of Consultation
            ['service_id' => 3, 'role_id' => 5, 'percentage' => 50.00, 'flat_fee' => 0.00, 'status' => 'active'], // 50% of EKG
            ['service_id' => 4, 'role_id' => 5, 'percentage' => 40.00, 'flat_fee' => 0.00, 'status' => 'active']  // 40% of Hairstudio
        ];
        $this->db->table('fee_rules')->insertBatch($feeRules);

        // 19. Seed Employees
        $employees = [
            ['id' => 1, 'nip' => 'NIP-2025-001', 'name' => 'dr. Andi Wijaya, Sp.PD', 'department' => 'Klinik', 'salary' => 15000000.00, 'status' => 'active'],
            ['id' => 2, 'nip' => 'NIP-2025-002', 'name' => 'Ns. Rina Kartika, S.Kep', 'department' => 'Klinik', 'salary' => 4500000.00, 'status' => 'active'],
            ['id' => 3, 'nip' => 'NIP-2025-003', 'name' => 'Siti Aminah', 'department' => 'Keuangan', 'salary' => 3800000.00, 'status' => 'active']
        ];
        $this->db->table('employees')->insertBatch($employees);

        // Re-aktifkan foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
