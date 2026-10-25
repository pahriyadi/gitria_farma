<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV2110Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');
        $releaseDate = '2026-10-26';
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.11.0')->get()->getRow();
        
        $updateData = [
            'version'      => 'v2.11.0',
            'title'        => 'Visualisasi Peta Alur Sistem 3D Interaktif & AI Kurir Kantor Bergerak',
            'category'     => 'FITUR BARU & INTERAKTIF',
            'badge_color'  => 'teal',
            'release_date' => $releaseDate,
            'summary'      => 'Menghadirkan halaman Peta Alur Sistem 3D interaktif yang menggambarkan kantor virtual modern, menampilkan pergerakan data riil antar modul secara visual, kurir staf otonom, dan panel editor skenario fleksibel.',
            'details'      => "• Peta Alur Kantor 3D Interaktif: Visualisasi miniatur kantor isometrik low-poly yang merepresentasikan 17 modul terintegrasi (Klinik, Farmasi, Resto, Laboratorium, Gudang, Kasir, hingga Akuntansi Double-Entry).\n• AI Karakter Staf Otonom: Karakter di tiap ruangan memiliki rutinitas mandiri seperti bekerja di meja, inspeksi ruangan, berjalan-jalan santai, dan bergegas kembali saat ada tugas masuk.\n• Kurir Pembawa Berkas Riil: Setiap transmisi data (Request / Response) kini diantarkan langsung oleh karakter kurir yang berjalan keluar ruangan melintasi lorong menuju modul tujuan.\n• Papan Nama Ruangan Ultra-Minimalis: Label ruangan dibuat ramping dan proporsional (micro game pin) dengan efek kaca transparan sehingga area kerja 3D terlihat luas dan lega.\n• Sidebar Keterangan Collapsible: Panel keterangan alur di sebelah kiri dapat diciutkan dengan tombol panah atau tombol pintasan keyboard (K) untuk kenyamanan tampilan penuh.\n• Editor Workflow Mandiri: Kemudahan mengubah, menambah langkah baru, mengatur kecepatan animasi, atau mengekspor dan mengimpor skenario alur kerja dalam format JSON.\n• Akses Cepat Pusat Bantuan: Terhubung langsung melalui rute /alur-3d dan tombol khusus di Pusat Bantuan tanpa hambatan layout SPA.",
            'is_major'     => 1,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existingUpdate) {
            $db->table('system_updates')->where('id', $existingUpdate->id)->update($updateData);
            echo "Log rilis versi v2.11.0 berhasil diperbarui.\n";
            return;
        }

        $updateData['created_at'] = date('Y-m-d H:i:s');
        $db->table('system_updates')->insert($updateData);
        echo "Log rilis versi v2.11.0 berhasil ditambahkan ke tabel system_updates.\n";
    }
}
