<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV290Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');

        // 1. Insert or update system_updates for v2.9.0
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.9.0')->get()->getRow();
        $updateData = [
            'version'      => 'v2.9.0',
            'title'        => 'Automated Database Backup Engine, Force Update Pipeline, Spotlight Search & Universal Version Tracking',
            'category'     => 'PERFORMA & DATA',
            'badge_color'  => 'teal',
            'release_date' => '2026-09-26',
            'summary'      => 'Pembaruan rilis v2.9.0 menghadirkan arsitektur pencadangan otomatis (Daily & Monthly) berbasis streaming Gzip Level 9 ultra hemat ruang, Pipa Paksa Pembaruan Sistem lintas tab browser, Spotlight Quick Search interaktif, dan pelacakan versi sistem terpadu.',
            'details'      => "• Automated Database Backup Engine (Gzip Stream Level 9): Pencadangan otomatis harian (Daily) dan bulanan (Monthly) dengan kompresi streaming langsung (.sql.gz) yang menghemat ruang penyimpanan server hingga ~90% (25 KB vs 154 KB).\n• Low-Memory Batch Chunking: Ekspor data 500 baris per iterasi mencegah PHP memory exhaustion dan CPU spike sehingga operasional klinik tetap lancar saat backup berlangsung.\n• Smart Auto-Pruning & Retention: Pembersihan otomatis arsip usang (7 hari harian, 12 bulan bulanan) menjaga kapasitas hosting/file manager tetap bersih dan tidak pernah penuh.\n• Multi-Trigger Execution: Dukungan eksekusi fleksibel via Linux Crontab, Windows Task Scheduler, Web Cron/Webhook aman terlindungi token, serta GUI Panel Admin.\n• Universal Force Update Pipeline (Client & Server): Mekanisme 1-klik pembersihan menyeluruh (OPcache PHP server, CI4 framework cache, W3C Clear-Site-Data header, Service Worker unregister, dan selective local storage purge).\n• Multi-Tab Synchronization Broadcast: Penggunaan BroadcastChannel dan StorageEvent listener yang otomatis menyinkronkan seluruh tab browser terbuka di perangkat yang sama saat pembaruan dipicu.\n• Spotlight Quick Search (Ctrl+K): Pencarian cepat universal yang mempermudah navigasi antar modul medis, antrean, kasir, farmasi, dan pengaturan sistem.\n• Interactive 'What's New' & Version Tracker: Modal notifikasi otomatis saat rilis versi baru serta pelacakan versi terpusat di seluruh sudut antarmuka sistem.",
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

        // 2. Insert Documentation / Guide
        $doc = [
            'category'     => 'system',
            'title'        => 'Pencadangan Otomatis Database & Pembaruan Sistem Terpadu',
            'target_role'  => 'IT & Administrator Sistem',
            'badge_color'  => 'teal',
            'icon'         => 'fas fa-database',
            'flow_steps'   => json_encode([
                ['title' => '1. Jadwal Otomatis', 'sub' => 'Harian & Bulanan', 'icon' => 'fas fa-calendar-check', 'color' => 'text-teal'],
                ['title' => '2. Gzip Stream L9', 'sub' => 'Hemat Ruang 90%', 'icon' => 'fas fa-file-zipper', 'color' => 'text-success'],
                ['title' => '3. Auto-Pruning', 'sub' => 'Rotasi Arsip Usang', 'icon' => 'fas fa-recycle', 'color' => 'text-primary'],
                ['title' => '4. Paksa Perbarui', 'sub' => 'Zero Stale Cache', 'icon' => 'fas fa-arrows-rotate', 'color' => 'text-warning']
            ]),
            'summary'      => 'Panduan pemanfaatan fitur pencadangan otomatis hemat ruang dan pipa paksa pembaruan sistem anti stale cache.',
            'content'      => "1. **Pusat Database & Backup:** Akses menu *Administrasi Sistem > Status Database & Backup*. Anda dapat mengatur jadwal pencadangan harian & bulanan serta kebijakan retensi penyimpanan.\n2. **Kompresi Gzip Tingkat Tinggi:** Setiap file backup dikompresi langsung ke `.sql.gz` sehingga sangat ringan dan ramah kapasitas hosting.\n3. **Paksa Perbarui Sistem:** Gunakan tombol *Paksa Perbarui Sistem* di topbar atau menu profil untuk menyegarkan seluruh cache komputer staf ke kode terbaru tanpa perlu hard reload manual.",
            'order_num'    => 22,
            'is_published' => 1,
            'created_by'   => 1
        ];

        $existingDoc = $db->table('system_documentations')->where('title', $doc['title'])->get()->getRow();
        if ($existingDoc) {
            $db->table('system_documentations')->where('id', $existingDoc->id)->update(array_merge($doc, ['updated_at' => date('Y-m-d H:i:s')]));
        } else {
            $db->table('system_documentations')->insert(array_merge($doc, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]));
        }

        echo "Log rilis v2.9.0 dan dokumentasi berhasil dipublikasikan!\n";
    }
}
