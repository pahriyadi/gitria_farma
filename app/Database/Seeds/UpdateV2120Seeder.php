<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateV2120Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('default');
        $releaseDate = '2026-11-17';
        $existingUpdate = $db->table('system_updates')->where('version', 'v2.12.0')->get()->getRow();

        $updateData = [
            'version'      => 'v2.12.0',
            'title'        => 'Integrasi Login Akun Google (OAuth 2.0) & Mode Autentikasi Adaptif',
            'category'     => 'KEAMANAN & AUTENTIKASI',
            'badge_color'  => 'primary',
            'release_date' => $releaseDate,
            'summary'      => 'Menghadirkan fitur Single Sign-On (SSO) menggunakan Akun Google (Google Identity Services) dengan verifikasi JWT server-side, dukungan mode login fleksibel, serta kontrol penuh melalui Pengaturan Sistem.',
            'details'      => "• Login Akun Google (OAuth 2.0 GIS): Staf dan administrator kini dapat masuk ke sistem menggunakan akun Gmail atau Google Workspace yang emailnya terdaftar di Manajemen Pengguna.\n• Verifikasi JWT Server-Side: Token kredensial Google diverifikasi langsung di sisi server ke endpoint resmi Google (oauth2.googleapis.com/tokeninfo) dengan validasi aud, iss, exp, dan email_verified.\n• Tiga Mode Autentikasi Fleksibel: Admin dapat memilih mode 'Keduanya (Manual & Google)', 'Wajib Google (Staf) + Manual (Admin)', atau 'Hanya Akun Google' (dilengkapi tautan Login Darurat Admin).\n• Tab Pengaturan Baru (9. Login Google): Pengaturan aktivasi, mode login, dan pengisian Google OAuth 2.0 Client ID tersedia langsung di menu Pengaturan Sistem beserta panduan Google Cloud Console.\n• Halaman Login Adaptif Gelap/Terang: Tersedia tombol toggle Dark/Light Mode di halaman login, dan tombol Sign in with Google otomatis menyesuaikan tema tampilan.\n• Validasi Keunikan Email & Audit Trail: Pengecekan duplikasi email secara case-insensitive pada Manajemen Pengguna dan Profil Saya, serta pencatatan riwayat login Google (LOGIN_GOOGLE) pada Audit Log.",
            'is_major'     => 1,
            'is_published' => 1,
            'created_by'   => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($existingUpdate) {
            $db->table('system_updates')->where('id', $existingUpdate->id)->update($updateData);
            echo "Log rilis versi v2.12.0 berhasil diperbarui.\n";
            return;
        }

        $updateData['created_at'] = date('Y-m-d H:i:s');
        $db->table('system_updates')->insert($updateData);
        echo "Log rilis versi v2.12.0 berhasil ditambahkan ke tabel system_updates.\n";
    }
}
