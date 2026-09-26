<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV280Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Insert or update system_updates for v2.8.0
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.8.0')->get()->getRow();
        $updateData = [
            'version'      => 'v2.8.0',
            'title'        => 'Enterprise Unified Architecture: API-Ready, Event-Driven Hooks, CI4 Native Models & Automated Cron Jobs',
            'category'     => 'ENTERPRISE ARCHITECTURE',
            'badge_color'  => 'success',
            'release_date' => '2026-08-27',
            'summary'      => 'Pembaruan arsitektur enterprise CodeIgniter 4: RESTful API-Ready Subsystem dengan Autentikasi Token, Decoupled Event-Driven Hooks, 33 CI4 Native Models dengan Built-in Validation Rules, Spark Scheduled Cron Tasks, Pelacakan Universal Audit Trail, dan Unified Testing Automation Suite.',
            'details'      => "• RESTful API-Ready Subsystem (/api/v1): Proteksi ApiKeyFilter (X-API-KEY / Bearer Token) & ResponseTrait untuk endpoint Display TV Antrean Real-time, Pencarian & Registrasi Mandiri Pasien, Katalog & Stok Obat, serta Bridge HL7 FHIR R4 Encounter SATUSEHAT Kemenkes RI.\n• Decoupled Event-Driven Architecture: Integrasi CodeIgniter\\Events\\Events untuk pemicu otomatis lintas modul (patient.registered -> Kartu Digital, billing.paid -> Auto-Jurnal & Komisi Dokter, pharmacy.dispensed -> Audit e-Resep, pharmacy.stock_low -> Alert Pengadaan Kritis, system.error_logged -> APM Tracker).\n• 33 CodeIgniter 4 Native Models: Standardisasi layer data resmi dengan \$allowedFields (Mass-Assignment Protection), \$validationRules & \$validationMessages terpusat, \$useTimestamps otomatis, dan Entity Callbacks (Auto Bcrypt Password Hashing).\n• Universal Audit Trail Recording: Pelacakan otomatis seluruh aktivitas pengguna (CREATE, UPDATE, DELETE, PAYMENT, APPROVE, DISPENSE, EXPORT, LOGIN, LOGOUT) dengan filter interaktif dan badge visual multi-warna.\n• Spark Scheduled Cron Jobs: Perintah otomatis siap pakai untuk Windows Task Scheduler / Linux Cron (cron:check-expired-medicines, cron:monthly-depreciation, cron:daily-closing, cron:database-backup).\n• Automated Testing Suite (spark system:test-all): Suite pengujian otomatis terpadu yang memverifikasi 100% kesehatan 33 Models, Event Listeners, API Endpoints, Scheduled Cron, dan Transaksi Database ACID.",
            'is_major'     => 1,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existingUpdate) {
            $db->table('system_updates')->where('id', $existingUpdate->id)->update($updateData);
        } else {
            $updateData['created_at'] = date('Y-m-d H:i:s');
            $db->table('system_updates')->insert($updateData);
        }

        // 2. Insert Help Documentation items for v2.8.0
        $helps = [
            [
                'category'     => 'security',
                'title'        => 'Interoperabilitas RESTful API & Bridge SATUSEHAT Kemenkes',
                'target_role'  => 'IT & Administrator Sistem',
                'badge_color'  => 'primary',
                'icon'         => 'fas fa-network-wired',
                'flow_steps'   => json_encode([
                    ['title' => '1. Header X-API-KEY', 'sub' => 'Autentikasi Token', 'icon' => 'fas fa-key', 'color' => 'text-primary'],
                    ['title' => '2. Panggil Endpoint', 'sub' => 'GET /api/v1/...', 'icon' => 'fas fa-link', 'color' => 'text-teal'],
                    ['title' => '3. Format HL7 FHIR', 'sub' => 'Standar Kemenkes RI', 'icon' => 'fas fa-code', 'color' => 'text-warning'],
                    ['title' => '4. Respon JSON Standar', 'sub' => 'Status, Code, Data', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan pemanfaatan RESTful API terstandar untuk Display TV Antrean, Integrasi Mobile Pasien, dan SatuSehat Kemenkes.',
                'content'      => "1. **Autentikasi API:** Seluruh request ke endpoint `/api/v1/*` harus menyertakan header `X-API-KEY` atau `Authorization: Bearer <token>`.\n2. **Endpoint Display Antrean:** `GET /api/v1/antrean` menyajikan data antrean poliklinik yang sedang dipanggil dan jumlah antrean menunggu.\n3. **Endpoint Katalog Farmasi:** `GET /api/v1/medicines` menyajikan daftar obat aktif beserta total stok terkini.\n4. **Bridge SATUSEHAT:** `GET /api/v1/satusehat/encounter/{visit_id}` menghasilkan payload JSON terstandar HL7 FHIR R4 Encounter Resource yang siap dikirim ke server Kemenkes RI.",
                'order_num'    => 20,
                'is_published' => 1,
                'created_by'   => 1
            ],
            [
                'category'     => 'technical',
                'title'        => 'Penjadwalan Otomatis (Spark Scheduled Cron Tasks)',
                'target_role'  => 'IT & Administrator Sistem',
                'badge_color'  => 'teal',
                'icon'         => 'fas fa-clock',
                'flow_steps'   => json_encode([
                    ['title' => '1. Task Scheduler / Cron', 'sub' => 'Atur Eksekusi Berkala', 'icon' => 'fas fa-calendar-check', 'color' => 'text-teal'],
                    ['title' => '2. spark cron:...', 'sub' => 'Eksekusi Perintah CLI', 'icon' => 'fas fa-terminal', 'color' => 'text-primary'],
                    ['title' => '3. Proses Latar Belakang', 'sub' => 'Scan/Depr/Closing/Backup', 'icon' => 'fas fa-cogs', 'color' => 'text-warning'],
                    ['title' => '4. Log Audit Otomatis', 'sub' => 'Tercatat di Audit Trail', 'icon' => 'fas fa-check-circle', 'color' => 'text-success']
                ]),
                'summary'      => 'Panduan pengaturan otomasi tugas latar belakang klinik menggunakan perintah Spark CLI Cron.',
                'content'      => "1. **Pemeriksaan Obat Kadaluarsa:** Jalankan `php spark cron:check-expired-medicines` setiap pagi untuk memindai batch obat mendekati kadaluarsa (< 90 hari).\n2. **Penyusutan Aset Bulanan:** Jalankan `php spark cron:monthly-depreciation` setiap akhir bulan untuk menghitung dan membukukan jurnal penyusutan aset tetap.\n3. **Penutupan Kasir Harian:** Jalankan `php spark cron:daily-closing` setiap malam untuk rekonsiliasi total omset kasir (Klinik, Apotek, Resto).\n4. **Pencadangan Basis Data:** Jalankan `php spark cron:database-backup` secara berkala untuk mengekspor full SQL dump ke folder `writable/backups/`.",
                'order_num'    => 21,
                'is_published' => 1,
                'created_by'   => 1
            ]
        ];

        foreach ($helps as $help) {
            $existingHelp = $db->table('system_documentations')->where('title', $help['title'])->get()->getRow();
            if ($existingHelp) {
                $help['updated_at'] = date('Y-m-d H:i:s');
                $db->table('system_documentations')->where('id', $existingHelp->id)->update($help);
            } else {
                $help['created_at'] = date('Y-m-d H:i:s');
                $db->table('system_documentations')->insert($help);
            }
        }

        echo "Log rilis v2.8.0 dan panduan arsitektur terpadu berhasil dipublikasikan.\n";
    }
}
