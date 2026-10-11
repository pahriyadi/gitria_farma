<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV2100Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');
        $releaseDate = '2026-10-11';
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.10.0')->get()->getRow();
        $updateData = [
            'version'      => 'v2.10.0',
            'title'        => 'Penyegaran Antarmuka, Navigasi Global & Aksi yang Lebih Jelas',
            'category'     => 'PENYEMPURNAAN ANTARMUKA',
            'badge_color'  => 'primary',
            'release_date' => $releaseDate,
            'summary'      => 'Penyegaran tampilan menyeluruh menghadirkan halaman yang lebih rapi, navigasi konsisten, serta menu aksi yang lebih mudah dipahami di seluruh modul.',
            'details'      => "• Tampilan lebih lembut dan konsisten: Latar halaman putih pudar, sidebar putih, kartu dengan garis dan bayangan tipis, serta kontras ikon sidebar yang lebih jelas saat diarahkan.\n• Area kerja halaman terpadu: Konten setiap halaman dibungkus dalam panel yang konsisten untuk memperjelas batas area kerja dan merapikan jarak antarelemen.\n• Navigasi breadcrumb satu baris: Judul halaman, breadcrumb, dan aksi halaman ditata sejajar; tombol aksi tetap berada di sisi kanan dan tata letak menyesuaikan layar kecil.\n• Menu aksi lebih informatif: Aksi tabel, toolbar, header, dan tindakan sistem diringkas dalam dropdown berisi ikon beserta keterangan agar fungsi setiap pilihan mudah dikenali.\n• Tooltip yang mengganggu dihilangkan tanpa menghapus label aksesibilitas pada kontrol.\n• Navigasi Portal Distributor B2B disederhanakan melalui sidebar, dengan menu operasional B2B lengkap saat portal dibuka.",
            'is_major'     => 0,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existingUpdate) {
            $db->table('system_updates')->where('id', $existingUpdate->id)->update($updateData);
            return;
        }

        $updateData['created_at'] = date('Y-m-d H:i:s');
        $db->table('system_updates')->insert($updateData);
    }
}
