<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterIcdSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Standar ICD-10 (Diagnosa Penyakit Terbanyak di Klinik)
        $icd10Data = [
            ['code' => 'I10',     'name_id' => 'Hipertensi Esensial (Primer)', 'name_en' => 'Essential (primary) hypertension', 'category' => 'Penyakit Sistem Sirkulasi'],
            ['code' => 'I20.9',   'name_id' => 'Angina Pectoris, Tidak Spesifik', 'name_en' => 'Angina pectoris, unspecified', 'category' => 'Penyakit Sistem Sirkulasi'],
            ['code' => 'I50.9',   'name_id' => 'Gagal Jantung, Tidak Spesifik', 'name_en' => 'Heart failure, unspecified', 'category' => 'Penyakit Sistem Sirkulasi'],
            ['code' => 'E11.9',   'name_id' => 'Diabetes Melitus Tipe 2 Tanpa Komplikasi', 'name_en' => 'Type 2 diabetes mellitus without complications', 'category' => 'Penyakit Endokrin & Metabolik'],
            ['code' => 'E78.5',   'name_id' => 'Hiperlipidemia, Tidak Spesifik (Kolesterol Tinggi)', 'name_en' => 'Hyperlipidemia, unspecified', 'category' => 'Penyakit Endokrin & Metabolik'],
            ['code' => 'E79.0',   'name_id' => 'Hiperurisemia Tanpa Tanda Arthritis (Asam Urat)', 'name_en' => 'Hyperuricaemia without signs of inflammatory arthritis', 'category' => 'Penyakit Endokrin & Metabolik'],
            ['code' => 'J00',     'name_id' => 'Nasofaringitis Akut (Common Cold / Batuk Pilek)', 'name_en' => 'Acute nasopharyngitis [common cold]', 'category' => 'Penyakit Sistem Pernapasan'],
            ['code' => 'J02.9',   'name_id' => 'Faringitis Akut (Radang Tenggorokan)', 'name_en' => 'Acute pharyngitis, unspecified', 'category' => 'Penyakit Sistem Pernapasan'],
            ['code' => 'J06.9',   'name_id' => 'Infeksi Saluran Pernapasan Akut (ISPA)', 'name_en' => 'Acute upper respiratory infection, unspecified', 'category' => 'Penyakit Sistem Pernapasan'],
            ['code' => 'J45.9',   'name_id' => 'Asma Bronkial, Tidak Spesifik', 'name_en' => 'Asthma, unspecified', 'category' => 'Penyakit Sistem Pernapasan'],
            ['code' => 'K29.7',   'name_id' => 'Gastritis, Tidak Spesifik (Maag)', 'name_en' => 'Gastritis, unspecified', 'category' => 'Penyakit Sistem Pencernaan'],
            ['code' => 'K30',     'name_id' => 'Dispepsia (Gangguan Pencernaan / Asam Lambung)', 'name_en' => 'Dyspepsia', 'category' => 'Penyakit Sistem Pencernaan'],
            ['code' => 'A09',     'name_id' => 'Gastroenteritis & Kolitis Akut (Diare)', 'name_en' => 'Infectious gastroenteritis and colitis, unspecified', 'category' => 'Penyakit Infeksi & Parasit'],
            ['code' => 'A01.0',   'name_id' => 'Demam Tifoid (Tipes)', 'name_en' => 'Typhoid fever', 'category' => 'Penyakit Infeksi & Parasit'],
            ['code' => 'A90',     'name_id' => 'Demam Dengue (DBD Klasik)', 'name_en' => 'Dengue fever [classical dengue]', 'category' => 'Penyakit Infeksi & Parasit'],
            ['code' => 'R50.9',   'name_id' => 'Demam (Febris), Tidak Spesifik', 'name_en' => 'Fever, unspecified', 'category' => 'Gejala & Tanda Umum'],
            ['code' => 'R51',     'name_id' => 'Sakit Kepala (Cephalgia)', 'name_en' => 'Headache', 'category' => 'Gejala & Tanda Umum'],
            ['code' => 'R42',     'name_id' => 'Pusing & Vertigo', 'name_en' => 'Dizziness and giddiness', 'category' => 'Gejala & Tanda Umum'],
            ['code' => 'M79.1',   'name_id' => 'Mialgia (Nyeri Otot)', 'name_en' => 'Myalgia', 'category' => 'Penyakit Otot & Rangka'],
            ['code' => 'L20.9',   'name_id' => 'Dermatitis Atopik (Eksim Kulit)', 'name_en' => 'Atopic dermatitis, unspecified', 'category' => 'Penyakit Kulit'],
            ['code' => 'L70.0',   'name_id' => 'Acne Vulgaris (Jerawat)', 'name_en' => 'Acne vulgaris', 'category' => 'Penyakit Kulit & Estetika'],
            ['code' => 'L65.9',   'name_id' => 'Alopesia / Rambut Rontok, Tidak Spesifik', 'name_en' => 'Non-scarring hair loss, unspecified', 'category' => 'Penyakit Kulit & Estetika'],
            ['code' => 'H10.9',   'name_id' => 'Konjungtivitis, Tidak Spesifik (Sakit Mata)', 'name_en' => 'Conjunctivitis, unspecified', 'category' => 'Penyakit Mata'],
            ['code' => 'Z00.0',   'name_id' => 'Pemeriksaan Medis Umum (Medical Check-up)', 'name_en' => 'General medical examination', 'category' => 'Faktor Kontak Layanan Kesehatan']
        ];

        foreach ($icd10Data as $item) {
            $exists = $this->db->table('master_icd10')->where('code', $item['code'])->get()->getRow();
            if (!$exists) {
                $item['status'] = 'active';
                $this->db->table('master_icd10')->insert($item);
            }
        }

        // 2. Standar ICD-9-CM (Prosedur & Tindakan Medis)
        $icd9Data = [
            ['code' => '89.52',   'name_id' => 'Elektrokardiogram (EKG 12-Lead)', 'name_en' => 'Electrocardiogram', 'category' => 'Pemeriksaan Kardiovaskular'],
            ['code' => '88.72',   'name_id' => 'Ekokardiografi Jantung (USG Jantung)', 'name_en' => 'Diagnostic ultrasound of heart', 'category' => 'Pemeriksaan Kardiovaskular'],
            ['code' => '88.76',   'name_id' => 'USG Abdomen / Perut', 'name_en' => 'Diagnostic ultrasound of abdomen and retroperitoneum', 'category' => 'Pemeriksaan Radiologi / USG'],
            ['code' => '93.57',   'name_id' => 'Perawatan & Ganti Verban Luka (Wound Dressing)', 'name_en' => 'Application of other wound dressing', 'category' => 'Tindakan Keperawatan / Bedah Minor'],
            ['code' => '86.59',   'name_id' => 'Penjahitan Luka Terbuka (Heacting / Suture)', 'name_en' => 'Closure of skin and subcutaneous tissue of other sites', 'category' => 'Tindakan Bedah Minor'],
            ['code' => '86.04',   'name_id' => 'Insisi & Drainase Abses Kulit', 'name_en' => 'Other incision with drainage of skin and subcutaneous tissue', 'category' => 'Tindakan Bedah Minor'],
            ['code' => '86.22',   'name_id' => 'Debridement Luka / Pengangkatan Jaringan Mati', 'name_en' => 'Excisional debridement of wound, infection, or burn', 'category' => 'Tindakan Bedah Minor'],
            ['code' => '96.59',   'name_id' => 'Irigasi / Cuci Telinga (Spooling Telinga)', 'name_en' => 'Other irrigation of wound', 'category' => 'Tindakan THT'],
            ['code' => '93.94',   'name_id' => 'Terapi Nebulisasi / Inhalasi Uap', 'name_en' => 'Respiratory medication administered by nebulizer', 'category' => 'Tindakan Pulmonologi / Respirasi'],
            ['code' => '99.21',   'name_id' => 'Injeksi Antibiotik / Obat Intramuskular (IM)', 'name_en' => 'Injection of antibiotic', 'category' => 'Prosedur Injeksi & Terapi'],
            ['code' => '99.18',   'name_id' => 'Pemasangan Infus Cairan Intravena (IV Line)', 'name_en' => 'Injection or infusion of electrolytes or other therapeutic fluid', 'category' => 'Prosedur Injeksi & Terapi'],
            ['code' => '90.59',   'name_id' => 'Pemeriksaan Laboratorium Darah Rutin', 'name_en' => 'Microscopic examination of blood', 'category' => 'Pemeriksaan Laboratorium'],
            ['code' => '86.89',   'name_id' => 'Facial Treatment & Perawatan Kulit Wajah', 'name_en' => 'Other facial plastic and aesthetic procedures', 'category' => 'Layanan Estetika Medis'],
            ['code' => '86.99',   'name_id' => 'Terapi Perawatan Kulit Kepala / Rambut Rontok (Hairstudio)', 'name_en' => 'Other operations on skin and subcutaneous tissue', 'category' => 'Layanan Estetika Medis'],
            ['code' => '89.07',   'name_id' => 'Konsultasi Medis & Edukasi Pasien Spesialis', 'name_en' => 'Consultation, described as comprehensive', 'category' => 'Konsultasi Medis']
        ];

        foreach ($icd9Data as $item) {
            $exists = $this->db->table('master_icd9')->where('code', $item['code'])->get()->getRow();
            if (!$exists) {
                $item['status'] = 'active';
                $this->db->table('master_icd9')->insert($item);
            }
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
