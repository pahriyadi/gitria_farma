<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class System extends BaseController
{
    /**
     * Manajemen User & Hak Akses (RBAC)
     */
    public function users()
    {
        $db = \Config\Database::connect('default');

        $users = $db->table('users')
                    ->select('users.*, roles.name as role_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->orderBy('users.created_at', 'DESC')
                    ->get()
                    ->getResult();

        $roles = $db->table('roles')->orderBy('id', 'ASC')->get()->getResult();

        // 1. Get All Permissions
        $allPermissions = $db->table('permissions')->orderBy('id', 'ASC')->get()->getResult();

        // 2. Map default permissions per role: role_id => [perm_name, ...]
        $rolePermsRows = $db->table('role_permissions')
                            ->select('role_permissions.role_id, permissions.name as perm_name')
                            ->join('permissions', 'permissions.id = role_permissions.permission_id')
                            ->get()
                            ->getResult();
        $rolePermissionsMap = [];
        foreach ($rolePermsRows as $rp) {
            $rolePermissionsMap[$rp->role_id][] = $rp->perm_name;
        }

        // Attach user count & permissions to each role object
        foreach ($roles as $r) {
            $r->user_count = $db->table('users')->where('role_id', $r->id)->countAllResults();
            $r->permissions = $rolePermissionsMap[$r->id] ?? [];
        }

        // 3. Map custom permissions per user: user_id => [perm_name, ...]
        $userPermissionsMap = [];
        try {
            $userPermsRows = $db->table('user_permissions')
                                ->select('user_permissions.user_id, permissions.name as perm_name')
                                ->join('permissions', 'permissions.id = user_permissions.permission_id')
                                ->get()
                                ->getResult();
            foreach ($userPermsRows as $up) {
                $userPermissionsMap[$up->user_id][] = $up->perm_name;
            }
        } catch (\Throwable $e) {
            log_message('error', 'user_permissions query error in users list: ' . $e->getMessage());
        }

        // Attach permissions to each user object
        foreach ($users as $u) {
            $u->custom_permissions = $userPermissionsMap[$u->id] ?? ($rolePermissionsMap[$u->role_id] ?? []);
        }

        // 4 KPI User Stats
        $totalUsers    = count($users);
        $activeUsers   = 0;
        $adminCount    = 0;
        $medicalStaff  = 0;

        foreach ($users as $u) {
            if ($u->status === 'active') $activeUsers++;
            if (in_array(strtolower($u->role_name), ['super admin', 'administrator', 'manajer', 'direktur'])) $adminCount++;
            if (in_array(strtolower($u->role_name), ['dokter', 'perawat', 'apoteker', 'analis lab'])) $medicalStaff++;
        }

        $data = [
            'title'              => 'User & Hak Akses Role Management',
            'active_menu'        => 'system-users',
            'users'              => $users,
            'roles'              => $roles,
            'allPermissions'     => $allPermissions,
            'rolePermissionsMap' => $rolePermissionsMap,
            'totalUsers'         => $totalUsers,
            'activeUsers'        => $activeUsers,
            'adminCount'         => $adminCount,
            'medicalStaff'       => $medicalStaff
        ];

        return view('system/users', $data);
    }

    /**
     * Simpan / Edit Akun Pengguna & Hak Akses Custom
     */
    public function saveUser()
    {
        $db = \Config\Database::connect('default');

        $id            = intval($this->request->getPost('user_id'));
        $username      = trim($this->request->getPost('username'));
        $email         = trim($this->request->getPost('email'));
        $roleId        = intval($this->request->getPost('role_id'));
        $password      = $this->request->getPost('password');
        $status        = $this->request->getPost('status') ?: 'active';
        $selectedPerms = $this->request->getPost('permissions') ?: []; // Array of checked permission names

        // Check if username/email already taken by other user
        $checkUser = $db->table('users')
                        ->groupStart()
                            ->where('username', $username)
                            ->orWhere('email', $email)
                        ->groupEnd();
        if ($id > 0) {
            $checkUser->where('id !=', $id);
        }
        $existing = $checkUser->get()->getRow();

        if ($existing) {
            session()->setFlashdata('error', 'Username atau Email sudah terdaftar pada pengguna lain.');
            return redirect()->to(base_url('system/users'));
        }

        $targetUserId = 0;

        if ($id > 0) {
            // Update User
            $updateData = [
                'username' => $username,
                'email'    => $email,
                'role_id'  => $roleId,
                'status'   => $status
            ];
            if (!empty($password)) {
                $updateData['password']       = password_hash($password, PASSWORD_BCRYPT);
                $updateData['plain_password'] = $password;
            }
            $db->table('users')->where('id', $id)->update($updateData);
            $targetUserId = $id;

            // Audit Log
            $this->logAudit('UPDATE', 'User Management', 'Memperbarui data pengguna & hak akses: ' . $username);
            session()->setFlashdata('success', 'Akun & hak akses pengguna ' . $username . ' berhasil diperbarui.');
        } else {
            // Create User
            if (empty($password)) {
                $password = 'Sawamawa@2026';
            }
            $insertData = [
                'username'       => $username,
                'email'          => $email,
                'password'       => password_hash($password, PASSWORD_BCRYPT),
                'plain_password' => $password,
                'role_id'        => $roleId,
                'status'         => $status,
                'created_at'     => date('Y-m-d H:i:s')
            ];
            $db->table('users')->insert($insertData);
            $targetUserId = $db->insertID();

            // Audit Log
            $this->logAudit('CREATE', 'User Management', 'Membuat pengguna baru: ' . $username);
            session()->setFlashdata('success', 'Akun pengguna ' . $username . ' berhasil didaftarkan.');
        }

        // Save Custom User Permissions if provided
        if ($targetUserId > 0) {
            $db->table('user_permissions')->where('user_id', $targetUserId)->delete();
            if (!empty($selectedPerms) && is_array($selectedPerms)) {
                $permRows = $db->table('permissions')->whereIn('name', $selectedPerms)->get()->getResult();
                foreach ($permRows as $p) {
                    $db->table('user_permissions')->insert([
                        'user_id'       => $targetUserId,
                        'permission_id' => $p->id
                    ]);
                }
            }
        }

        return redirect()->to(base_url('system/users'));
    }

    /**
     * Toggle Status Pengguna (Aktif / Nonaktif)
     */
    public function toggleUser($id)
    {
        $db = \Config\Database::connect('default');
        $user = $db->table('users')->where('id', $id)->get()->getRow();

        if ($user) {
            if ($user->id == session('user_id')) {
                session()->setFlashdata('error', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif.');
                return redirect()->to(base_url('system/users'));
            }

            $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
            $db->table('users')->where('id', $id)->update(['status' => $newStatus]);
            $this->logAudit('UPDATE', 'User Management', 'Mengubah status akun ' . $user->username . ' menjadi: ' . $newStatus);
            session()->setFlashdata('success', 'Status akun ' . $user->username . ' diubah menjadi ' . strtoupper($newStatus) . '.');
        }

        return redirect()->to(base_url('system/users'));
    }

    /**
     * Simpan / Edit Role Jabatan & Otorisasi Permission Default
     */
    public function saveRole()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $id          = intval($this->request->getPost('role_id'));
        $name        = trim($this->request->getPost('name'));
        $description = trim($this->request->getPost('description'));
        $permissions = $this->request->getPost('permissions') ?: []; // array of perm names

        if (empty($name)) {
            session()->setFlashdata('error', 'Nama Role / Jabatan wajib diisi.');
            return redirect()->to(base_url('system/users#tab-roles'));
        }

        // Check unique role name
        $existing = $db->table('roles')->where('name', $name);
        if ($id > 0) {
            $existing->where('id !=', $id);
        }
        if ($existing->countAllResults() > 0) {
            session()->setFlashdata('error', 'Nama Role tersebut sudah terdaftar.');
            return redirect()->to(base_url('system/users#tab-roles'));
        }

        if ($id > 0) {
            // Update role
            $db->table('roles')->where('id', $id)->update([
                'name'        => $name,
                'description' => $description,
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
            $roleId = $id;
            $this->logAudit('UPDATE', 'Role Management', 'Memperbarui role jabatan: ' . $name);
        } else {
            // Insert role
            $db->table('roles')->insert([
                'name'        => $name,
                'description' => $description,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
            $roleId = $db->insertID();
            $this->logAudit('CREATE', 'Role Management', 'Membuat role jabatan baru: ' . $name);
        }

        // Update default role permissions in role_permissions table
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        if (!empty($permissions) && is_array($permissions)) {
            $allPerms = $db->table('permissions')->get()->getResult();
            $permMap = [];
            foreach ($allPerms as $p) {
                $permMap[$p->name] = $p->id;
            }

            foreach ($permissions as $pName) {
                if (isset($permMap[$pName])) {
                    $db->table('role_permissions')->insert([
                        'role_id'       => $roleId,
                        'permission_id' => $permMap[$pName]
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menyimpan role jabatan.');
        } else {
            session()->setFlashdata('success', "Role jabatan '{$name}' dan hak akses defaultnya berhasil disimpan!");
        }

        return redirect()->to(base_url('system/users#tab-roles'));
    }

    /**
     * Hapus Role Custom
     */
    public function deleteRole($id)
    {
        $db = \Config\Database::connect('default');
        $id = intval($id);

        if (in_array($id, [1, 2])) {
            session()->setFlashdata('error', 'Role sistem Super Admin dan IT dilindungi dan tidak dapat dihapus.');
            return redirect()->to(base_url('system/users#tab-roles'));
        }

        $usersCount = $db->table('users')->where('role_id', $id)->countAllResults();
        if ($usersCount > 0) {
            session()->setFlashdata('error', "Role tidak dapat dihapus karena masih digunakan oleh {$usersCount} pengguna aktif.");
            return redirect()->to(base_url('system/users#tab-roles'));
        }

        $role = $db->table('roles')->where('id', $id)->get()->getRow();
        $roleName = $role ? $role->name : 'ID ' . $id;

        $db->table('role_permissions')->where('role_id', $id)->delete();
        $db->table('roles')->where('id', $id)->delete();

        $this->logAudit('DELETE', 'Role Management', 'Menghapus role jabatan: ' . $roleName);
        session()->setFlashdata('success', "Role jabatan '{$roleName}' berhasil dihapus.");
        return redirect()->to(base_url('system/users#tab-roles'));
    }
    public function database()
    {
        $db = \Config\Database::connect('default');
        $databaseName = $db->getDatabase();

        // Fetch all tables with table status
        $tablesQuery = $db->query("
            SELECT 
                TABLE_NAME as name,
                ENGINE as engine,
                TABLE_ROWS as row_count,
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
                TABLE_COLLATION as collation,
                AUTO_INCREMENT as auto_increment,
                UPDATE_TIME as update_time,
                CREATE_TIME as create_time
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME ASC
        ", [$databaseName]);

        $tables = $tablesQuery->getResult();

        $totalTables = count($tables);
        $totalRows   = 0;
        $totalSizeMb = 0;

        foreach ($tables as $t) {
            $totalRows   += intval($t->row_count);
            $totalSizeMb += floatval($t->size_mb);
        }

        // Integrity Check (Check for any orphan records in vital relationships)
        $orphanIssues = [];

        // Check fee_transactions without valid doctor
        $orphanDocFees = $db->query("SELECT COUNT(*) as cnt FROM fee_transactions WHERE doctor_id NOT IN (SELECT id FROM doctors)")->getRow();
        if ($orphanDocFees && $orphanDocFees->cnt > 0) {
            $orphanIssues[] = $orphanDocFees->cnt . " transaksi komisi dokter tidak memiliki relasi dokter yang valid.";
        }

        // Check medicine batches with 0 stock
        $zeroStockBatches = $db->query("SELECT COUNT(*) as cnt FROM medicine_batches WHERE stock <= 0")->getRow();
        $zeroStockCount = $zeroStockBatches ? $zeroStockBatches->cnt : 0;

        // Backup Service & Automation Integration
        $backupService = new \App\Services\DatabaseBackupService();
        $backupSettings = $backupService->getSettings();
        $backupList = $backupService->getBackupList();

        $totalBackupBytes = 0;
        $dailyCount = 0;
        $monthlyCount = 0;
        $manualCount = 0;
        foreach ($backupList as $b) {
            $totalBackupBytes += $b['size_bytes'];
            if ($b['type'] === 'daily') $dailyCount++;
            elseif ($b['type'] === 'monthly') $monthlyCount++;
            else $manualCount++;
        }

        $data = [
            'title'              => 'Status Database, Backup & Sinkronisasi Data',
            'active_menu'        => 'system-database',
            'databaseName'       => $databaseName,
            'tables'             => $tables,
            'totalTables'        => $totalTables,
            'totalRows'          => $totalRows,
            'totalSizeMb'        => $totalSizeMb,
            'orphanIssues'       => $orphanIssues,
            'zeroStockCount'     => $zeroStockCount,
            'backupSettings'     => $backupSettings,
            'backupList'         => $backupList,
            'totalBackupBytes'   => $totalBackupBytes,
            'totalBackupHuman'   => $backupService->formatBytes($totalBackupBytes),
            'dailyCount'         => $dailyCount,
            'monthlyCount'       => $monthlyCount,
            'manualCount'        => $manualCount,
            'backupDir'          => $backupService->getBackupDir()
        ];

        return view('system/database', $data);
    }

    /**
     * Buat Pencadangan Database Baru (Manual / Instant / Scheduled Type)
     */
    public function createBackup()
    {
        $type = $this->request->getPost('type') ?: 'manual';
        if (!in_array($type, ['daily', 'monthly', 'manual'])) {
            $type = 'manual';
        }

        $backupService = new \App\Services\DatabaseBackupService();
        $res = $backupService->runBackup($type);

        if (!empty($res['success'])) {
            $msg = "Backup basis data ({$res['filename']}) berhasil dibuat! Ukuran: {$res['compressed_human']} (hemat {$res['saved_ratio']}% ruang dengan Gzip).";
            if (!empty($res['pruned_files']) && $res['pruned_files'] > 0) {
                $msg .= " Rotasi otomatis membersihkan {$res['pruned_files']} file arsip usang ({$res['pruned_space_human']}).";
            }
            session()->setFlashdata('success', $msg);
        } else {
            session()->setFlashdata('error', 'Gagal membuat backup: ' . ($res['message'] ?? 'Terjadi kesalahan sistem.'));
        }

        return redirect()->to(base_url('system/database'));
    }

    /**
     * Unduh File Backup Secara Aman
     */
    public function downloadBackup(string $filename)
    {
        $backupService = new \App\Services\DatabaseBackupService();
        $safeName = basename($filename);
        $filepath = $backupService->getBackupDir() . $safeName;

        if (!file_exists($filepath)) {
            session()->setFlashdata('error', 'File backup tidak ditemukan atau sudah dibersihkan oleh sistem rotasi.');
            return redirect()->to(base_url('system/database'));
        }

        $this->logAudit('EXPORT', 'Database Administration', 'Mengunduh file backup: ' . $safeName);

        $mimeType = str_ends_with($safeName, '.gz') ? 'application/gzip' : 'application/sql';

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $safeName . '"')
            ->setHeader('Content-Length', (string)filesize($filepath))
            ->setBody(file_get_contents($filepath));
    }

    /**
     * Hapus File Backup Tertentu
     */
    public function deleteBackup(string $filename)
    {
        $backupService = new \App\Services\DatabaseBackupService();
        $safeName = basename($filename);
        
        if ($backupService->deleteBackup($safeName)) {
            session()->setFlashdata('success', "File backup '{$safeName}' berhasil dihapus dari server.");
        } else {
            session()->setFlashdata('error', "Gagal menghapus file backup '{$safeName}'.");
        }

        return redirect()->to(base_url('system/database'));
    }

    /**
     * Pulihkan (Restore) Database dari File Arsip Terpilih
     */
    public function restoreBackup()
    {
        $filename = trim((string)$this->request->getPost('filename'));
        $confirmText = trim((string)$this->request->getPost('confirm_text'));

        if (strtoupper($confirmText) !== 'PULIHKAN DATABASE') {
            session()->setFlashdata('error', 'Konfirmasi restorasi tidak sesuai. Ketik "PULIHKAN DATABASE" untuk melanjutkan.');
            return redirect()->to(base_url('system/database'));
        }

        if (empty($filename)) {
            session()->setFlashdata('error', 'Nama file backup tidak valid.');
            return redirect()->to(base_url('system/database'));
        }

        $backupService = new \App\Services\DatabaseBackupService();
        $result = $backupService->restoreBackup($filename);

        if ($result['success']) {
            $msg = "Restorasi basis data dari file '{$result['filename']}' BERHASIL diselesaikan! Total {$result['query_count']} pernyataan SQL dieksekusi.";
            if (!empty($result['error_count']) && $result['error_count'] > 0) {
                $msg .= " (Catatan: {$result['error_count']} query dilewati/peringatan non-kritis).";
            }
            session()->setFlashdata('success', $msg);
        } else {
            session()->setFlashdata('error', 'Gagal memulihkan database: ' . ($result['message'] ?? 'Terjadi kesalahan saat mengeksekusi SQL.'));
        }

        return redirect()->to(base_url('system/database'));
    }

    /**
     * Simpan Pengaturan Otomatisasi Backup & Kebijakan Retensi
     */
    public function saveBackupSettings()
    {
        $data = [
            'backup_auto_daily'               => $this->request->getPost('backup_auto_daily') ? '1' : '0',
            'backup_auto_monthly'             => $this->request->getPost('backup_auto_monthly') ? '1' : '0',
            'backup_daily_retention_days'     => max(1, (int)$this->request->getPost('backup_daily_retention_days')),
            'backup_monthly_retention_months' => max(1, (int)$this->request->getPost('backup_monthly_retention_months')),
            'backup_compression'              => $this->request->getPost('backup_compression') ?: 'gzip',
            'backup_cron_token'               => trim((string)$this->request->getPost('backup_cron_token')) ?: 'sawamawa_cron_backup'
        ];

        $backupService = new \App\Services\DatabaseBackupService();
        $backupService->saveSettings($data);

        // Jalankan auto-pruning langsung bila kuota retensi diperkecil
        $pruned = $backupService->pruneOldBackups();

        $this->logAudit('UPDATE', 'Database Administration', 'Memperbarui konfigurasi otomatisasi backup & kebijakan retensi.');

        $msg = 'Konfigurasi otomatisasi backup dan retensi berhasil disimpan.';
        if (!empty($pruned['deleted_count']) && $pruned['deleted_count'] > 0) {
            $msg .= " Menyesuaikan retensi: {$pruned['deleted_count']} file usang dibersihkan (" . $backupService->formatBytes($pruned['freed_bytes']) . " ruang dibebaskan).";
        }

        session()->setFlashdata('success', $msg);
        return redirect()->to(base_url('system/database'));
    }

    /**
     * Endpoint Web Cron / Webhook Pencadangan Otomatis Terjadwal
     */
    public function cronBackup()
    {
        $backupService = new \App\Services\DatabaseBackupService();
        $settings = $backupService->getSettings();

        $token = $this->request->getGet('token') ?? $this->request->getHeaderLine('X-Cron-Token');
        $expectedToken = $settings['backup_cron_token'] ?? '';

        if (empty($expectedToken) || $token !== $expectedToken) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Akses ditolak: Token cron tidak valid atau tidak disediakan.'
            ]);
        }

        $type = $this->request->getGet('type') ?? 'auto';

        if ($type === 'auto') {
            $results = $backupService->checkAndRunScheduledBackups();
            return $this->response->setJSON([
                'status'    => 'success',
                'mode'      => 'auto_scheduled',
                'timestamp' => date('Y-m-d H:i:s'),
                'executed'  => $results
            ]);
        } else {
            $res = $backupService->runBackup($type);
            return $this->response->setJSON([
                'status'    => $res['success'] ? 'success' : 'error',
                'mode'      => $type,
                'timestamp' => date('Y-m-d H:i:s'),
                'result'    => $res
            ]);
        }
    }

    /**
     * Download Backup SQL Dump File (1-Click Database Dump Legacy)
     */
    public function backupDump()
    {
        $backupService = new \App\Services\DatabaseBackupService();
        $res = $backupService->runBackup('manual');

        if (!empty($res['success']) && file_exists($res['filepath'])) {
            return $this->downloadBackup($res['filename']);
        }

        session()->setFlashdata('error', 'Gagal membuat dump backup.');
        return redirect()->to(base_url('system/database'));
    }

    /**
     * Optimasi & Analisis Tabel Database
     */
    public function optimizeDb()
    {
        $db = \Config\Database::connect('default');
        $tables = $db->listTables();

        foreach ($tables as $table) {
            $db->query("OPTIMIZE TABLE `" . $table . "`");
            $db->query("ANALYZE TABLE `" . $table . "`");
        }

        $this->logAudit('OPTIMIZE', 'Database Administration', 'Melakukan optimasi dan defragmentasi seluruh tabel database.');
        session()->setFlashdata('success', 'Seluruh (' . count($tables) . ') tabel database berhasil dioptimasi dan didefragmentasi.');
        return redirect()->to(base_url('system/database'));
    }

    /**
     * Sinkronisasi Data & Perbaikan Relasi Foreign Keys
     */
    public function syncData()
    {
        $db = \Config\Database::connect('default');

        $db->transStart();

        // 1. Ensure essential default system settings exist
        $defaults = [
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_name', 'setting_value' => 'Sawamawa Medical Center', 'description' => 'Nama Resmi Klinik Utama'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_address', 'setting_value' => 'Jl. Raya Sawamawa No. 100, Sumbawa Besar', 'description' => 'Alamat Kantor Klinik Utama'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_phone', 'setting_value' => '081122334455', 'description' => 'Nomor Telepon Hotline Klinik'],
            ['setting_group' => 'api', 'setting_key' => 'wa_gateway_provider', 'setting_value' => 'fonnte', 'description' => 'Provider WhatsApp Gateway (fonnte/wablas)'],
            ['setting_group' => 'api', 'setting_key' => 'wa_api_token', 'setting_value' => '', 'description' => 'API Token WhatsApp Gateway'],
            ['setting_group' => 'api', 'setting_key' => 'wa_sender_number', 'setting_value' => '081122334455', 'description' => 'Nomor Pengirim WhatsApp Resmi']
        ];

        foreach ($defaults as $def) {
            $check = $db->table('system_settings')->where('setting_key', $def['setting_key'])->countAllResults();
            if ($check === 0) {
                $db->table('system_settings')->insert($def);
            }
        }

        // 2. Auto-run Void & Anti-Fraud Seeder
        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('App\Database\Seeds\VoidSystemSeeder');
            $seeder->call('App\Database\Seeds\DistributorCoaSeeder');
        } catch (\Throwable $e) {
            log_message('error', 'Auto-run Seeders in syncData: ' . $e->getMessage());
        }

        $db->transComplete();

        $this->logAudit('SYNC', 'Database Administration', 'Menjalankan sinkronisasi integritas referensi, skema anti-fraud, dan parameter sistem.');
        session()->setFlashdata('success', 'Proses sinkronisasi data, pembaruan skema anti-fraud void, dan verifikasi parameter sistem berhasil diselesaikan.');
        return redirect()->to(base_url('system/database'));
    }

    /**
     * Kosongkan Data Transaksi Operasional (Clean Slate untuk Go-Live / Reset Uji Coba)
     * Hanya menghapus data transaksi operasional, menjaga akun pengguna dan seluruh master referensi penting.
     */
    public function resetTransactions()
    {
        $db = \Config\Database::connect('default');

        // 1. Ambil seluruh tabel di database
        $allTables = $db->listTables();

        // 2. Daftar tabel referensi & pengguna yang WAJIB DIPERTAHANKAN (TIDAK BOLEH DIHAPUS)
        $preservedTables = [
            // Pengguna, Role & Hak Akses
            'users',
            'roles',
            'permissions',
            'role_permissions',
            'user_permissions',

            // Parameter Sistem & Dokumentasi
            'system_settings',
            'system_modules',
            'articles',
            'whats_new',
            'help_topics',
            'system_documentations',

            // Master Pelayanan Medis
            'doctors',
            'polikliniks',
            'polyclinics',
            'tindakan',
            'master_icd10',
            'master_icd9',
            'rooms',
            'beds',
            'insurance_providers',
            'consent_templates',
            'lab_test_types',
            'lab_tests',

            // Master Farmasi & Logistik
            'medicine_categories',
            'medicines',
            'units',
            'suppliers',

            // Master Restoran & Gizi
            'restaurant_categories',
            'resto_categories',
            'restaurant_menus',
            'menus',

            // Master Akuntansi & Keuangan
            'accounts',
            'journal_split_categories',
            'journal_split_rules',
            'master_pembayaran',

            // Master SDM & Organisasi
            'departments',
            'job_positions',
            'employees',
            'approval_workflows',
            'approval_steps',

            // Master Aset
            'assets'
        ];

        // 3. Tentukan tabel-tabel transaksi yang akan dikosongkan
        $tablesToTruncate = [];
        foreach ($allTables as $t) {
            if (!in_array($t, $preservedTables, true)) {
                $tablesToTruncate[] = $t;
            }
        }

        // 4. Lakukan pencadangan snapshot otomatis sebelum pengosongan
        try {
            $backupDir = WRITEPATH . 'backups/';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            $snapshotFile = $backupDir . 'pre_reset_backup_' . date('Ymd_His') . '.sql';
            
            $dump = "-- Sawamawa Automatic Backup Prior to Transaction Reset\n";
            $dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $dump .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
            foreach ($tablesToTruncate as $tb) {
                $rows = $db->table($tb)->get()->getResultArray();
                if (!empty($rows)) {
                    $dump .= "-- Backup data for table `{$tb}`\n";
                    foreach ($rows as $row) {
                        $fields = array_map(function($val) use ($db) {
                            if ($val === null) return 'NULL';
                            return $db->escape($val);
                        }, array_values($row));
                        $dump .= "INSERT INTO `{$tb}` VALUES (" . implode(", ", $fields) . ");\n";
                    }
                    $dump .= "\n";
                }
            }
            $dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";
            file_put_contents($snapshotFile, $dump);
        } catch (\Throwable $ex) {
            log_message('error', 'Gagal membuat auto backup pre-reset: ' . $ex->getMessage());
        }

        // 5. Eksekusi pengosongan tabel transaksi
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $truncatedCount = 0;
        $truncatedList = [];

        foreach ($tablesToTruncate as $tbl) {
            try {
                $db->query("TRUNCATE TABLE `{$tbl}`");
                $truncatedCount++;
                $truncatedList[] = $tbl;
            } catch (\Throwable $e) {
                // Fallback jika TRUNCATE gagal karena foreign key constraint, gunakan DELETE
                try {
                    $db->query("DELETE FROM `{$tbl}`");
                    $db->query("ALTER TABLE `{$tbl}` AUTO_INCREMENT = 1");
                    $truncatedCount++;
                    $truncatedList[] = $tbl;
                } catch (\Throwable $e2) {
                    log_message('error', "Gagal mengosongkan tabel {$tbl}: " . $e2->getMessage());
                }
            }
        }

        // 6. Reset saldo akun pada tabel accounts menjadi 0.00
        if (in_array('accounts', $allTables)) {
            $db->query("UPDATE `accounts` SET `balance` = 0.00;");
        }

        // 7. Reset status / stok pada master obat jika ada kolom stock
        if (in_array('medicines', $allTables)) {
            $fields = $db->getFieldNames('medicines');
            if (in_array('stock', $fields)) {
                $db->query("UPDATE `medicines` SET `stock` = 0;");
            }
        }

        // 8. Hidupkan kembali foreign key checks
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        // 9. Catat di audit log
        $this->logAudit('RESET', 'Database Administration', 'Mengosongkan ' . $truncatedCount . ' tabel data transaksi operasional. Master data dan user tetap aman.');

        $msg = "Pengosongan database transaksi berhasil diselesaikan! Sebanyak " . $truncatedCount . " tabel transaksi telah dibersihkan menjadi 0 records. Seluruh akun pengguna dan data referensi penting klinik tetap terjaga aman.";

        if ($this->request->isAJAX() || $this->request->getGet('format') === 'json') {
            return $this->response->setJSON([
                'status'           => 'success',
                'message'          => $msg,
                'truncated_count'  => $truncatedCount,
                'truncated_tables' => $truncatedList,
                'preserved_tables' => $preservedTables
            ]);
        }

        session()->setFlashdata('success', $msg);
        return redirect()->to(base_url('system/database'));
    }

    /**
     * Log Audit Trail
     */
    public function audit()
    {
        $db = \Config\Database::connect('default');

        $filterAction = $this->request->getGet('action') ?: '';
        $filterModule = $this->request->getGet('module') ?: '';
        $filterDate   = $this->request->getGet('date') ?: '';

        // AJAX Handler untuk DataTables Server-Side Processing Log Audit
        if ($this->request->getGet('draw')) {
            $where = [];
            if (!empty($filterAction)) $where['audit_logs.action'] = $filterAction;
            if (!empty($filterModule)) $where['audit_logs.module'] = $filterModule;
            if (!empty($filterDate)) $where['DATE(audit_logs.created_at)'] = $filterDate;

            return datatable_server_side('audit_logs', [
                0 => 'audit_logs.id',
                1 => 'audit_logs.created_at',
                2 => 'users.username',
                3 => 'audit_logs.module',
                4 => 'audit_logs.action',
                5 => 'audit_logs.new_value',
                6 => 'audit_logs.ip_address'
            ], [
                'select' => 'audit_logs.*, users.username',
                'joins'  => [
                    ['table' => 'users', 'cond' => 'users.id = audit_logs.user_id', 'type' => 'left']
                ],
                'where'  => $where,
                'search_columns' => ['users.username', 'audit_logs.module', 'audit_logs.action', 'audit_logs.new_value', 'audit_logs.ip_address'],
                'default_order'  => ['audit_logs.created_at', 'DESC'],
                'row_formatter'  => function($log, $no) {
                    $badgeColor = match($log->action) {
                        'LOGIN' => 'success',
                        'LOGOUT' => 'secondary',
                        'CREATE', 'REGISTER', 'INSERT' => 'success',
                        'UPDATE', 'EDIT', 'SYNC' => 'info',
                        'DELETE', 'TRUNCATE' => 'danger',
                        'APPROVE', 'VERIFY' => 'teal',
                        'PAYMENT', 'SETTLEMENT' => 'warning',
                        'DISPENSE' => 'primary',
                        'EXPORT', 'BACKUP', 'VIEW' => 'dark',
                        default => 'primary'
                    };
                    return [
                        '<span class="text-muted font-weight-bold">' . $no . '</span>',
                        '<span class="font-monospace text-xs text-nowrap">' . date('d/m/Y H:i:s', strtotime($log->created_at)) . '</span>',
                        '<strong>' . esc($log->username ?: 'Sistem (Auto)') . '</strong>',
                        '<span class="badge badge-light border text-xs">' . esc($log->module) . '</span>',
                        '<span class="badge badge-' . $badgeColor . ' font-weight-bold text-xs">' . esc($log->action) . '</span>',
                        '<span class="text-xs text-dark">' . esc($log->new_value) . '</span>',
                        '<span class="font-monospace text-xs text-muted">' . esc($log->ip_address) . '</span>'
                    ];
                }
            ]);
        }

        $modules = $db->table('audit_logs')->distinct()->select('module')->orderBy('module', 'ASC')->get()->getResult();

        $data = [
            'title'        => 'Sistem Audit Trail & Log Aktivitas Keamanan',
            'active_menu'  => 'system-audit',
            'modules'      => $modules,
            'filterAction' => $filterAction,
            'filterModule' => $filterModule,
            'filterDate'   => $filterDate
        ];

        return view('system/audit', $data);
    }

    /**
     * API Notifikasi Realtime Interkoneksi Lintas Seluruh Modul & Peran
     */
    public function notifApi()
    {
        $db = \Config\Database::connect('default');
        $session = session();
        $userId = (int) ($session->get('user_id') ?: 1);
        $permissions = (array) $session->get('permissions');
        $roleName = strtolower((string) $session->get('role_name'));
        $isSuperAdmin = in_array($roleName, ['super admin', 'administrator', 'it / technical support']);

        $notifService = new \App\Services\NotificationService();
        $notifications = [];

        // -------------------------------------------------------------
        // 0. ACCOUNTING ALERTS: KESEIMBANGAN JURNAL & PIUTANG JATUH TEMPO
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('accounting.view', $permissions) || strpos($roleName, 'accounting') !== false || strpos($roleName, 'keuangan') !== false) {
            $acctAlerts = $notifService->checkAccountingAlerts();
            foreach ($acctAlerts as $alert) {
                $notifications[] = $alert;
            }
        }

        // -------------------------------------------------------------
        // 1. SECURITY & APM ALERTS
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('system.settings', $permissions) || strpos($roleName, 'it') !== false) {
            $secAlerts = $notifService->checkSecurityAlerts();
            foreach ($secAlerts as $alert) {
                $notifications[] = $alert;
            }
        }

        // -------------------------------------------------------------
        // 2. FARMASI & APOTEK (e-Resep Masuk dari Poli & Stok Kritis)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('pharmacy.dispense', $permissions) || strpos($roleName, 'apotek') !== false || strpos($roleName, 'farmasi') !== false || strpos($roleName, 'gudang') !== false) {
            if ($notifService->isRuleActive('PHARM_LOW_STOCK')) {
                $pendingPrescriptions = $db->table('prescriptions')
                                           ->whereIn('status', ['pending', 'waiting'])
                                           ->countAllResults();
                if ($pendingPrescriptions > 0) {
                    $maxRx = $db->table('prescriptions')->whereIn('status', ['pending', 'waiting'])->selectMax('id')->get()->getRow();
                    $rxId = $maxRx ? $maxRx->id : 0;
                    $notifications[] = [
                        'id'           => 'resep_pending_' . $rxId . '_' . $pendingPrescriptions,
                        'key'          => 'resep_pending_alert',
                        'category'     => 'pharmacy',
                        'badge'        => 'e-Resep Masuk',
                        'type'         => 'success',
                        'icon'         => 'fas fa-pills',
                        'title'        => 'e-Resep Dokter Siap Disiapkan',
                        'message'      => 'Ada ' . $pendingPrescriptions . ' lembar e-resep baru dari dokter yang siap diracik/diserahkan ke pasien.',
                        'link'         => base_url('apotek/resep'),
                        'target_roles' => 'apoteker,asisten apoteker,farmasi,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }

                // Stok Obat Kritis
                $lowStockCount = $db->table('medicines')
                                    ->select('medicines.id, medicines.min_stock')
                                    ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                                    ->groupBy(['medicines.id', 'medicines.min_stock'])
                                    ->having('COALESCE(SUM(medicine_batches.stock), 0) <= medicines.min_stock')
                                    ->countAllResults();
                if ($lowStockCount > 0) {
                    $notifications[] = [
                        'id'           => 'low_stock_' . $lowStockCount,
                        'key'          => 'pharm_low_stock',
                        'category'     => 'pharmacy',
                        'badge'        => 'Stok Kritis',
                        'type'         => 'danger',
                        'icon'         => 'fas fa-triangle-exclamation',
                        'title'        => 'Peringatan Stok Obat Menipis',
                        'message'      => 'Ada ' . $lowStockCount . ' jenis obat mencapai batas minimum stok / perlu segera diajukan PR pengadaan.',
                        'link'         => base_url('apotek/stok'),
                        'target_roles' => 'apoteker,bagian pengadaan,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }

            // Obat Mendekati Expired (< 60 hari)
            if ($notifService->isRuleActive('PHARM_FEFO_EXPIRED')) {
                $expDateLimit = date('Y-m-d', strtotime('+60 days'));
                $expCount = $db->table('medicine_batches')
                               ->where('stock >', 0)
                               ->where('expired_date <=', $expDateLimit)
                               ->countAllResults();
                if ($expCount > 0) {
                    $notifications[] = [
                        'id'           => 'exp_batches_' . $expCount,
                        'key'          => 'pharm_exp_batches',
                        'category'     => 'pharmacy',
                        'badge'        => 'FEFO Alert',
                        'type'         => 'warning',
                        'icon'         => 'fas fa-calendar-xmark',
                        'title'        => 'Obat Mendekati Expired (< 60 Hari)',
                        'message'      => 'Ada ' . $expCount . ' batch obat aktif yang akan expired dalam 60 hari. Prioritaskan pengeluaran FEFO.',
                        'link'         => base_url('apotek/stok'),
                        'target_roles' => 'apoteker,asisten apoteker,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }
        }

        // -------------------------------------------------------------
        // 3. PELAYANAN POLI DOKTER & PERAWAT (Antrean Pasien Menunggu)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('clinic.soap', $permissions) || in_array('clinic.register', $permissions) || strpos($roleName, 'dokter') !== false || strpos($roleName, 'perawat') !== false || strpos($roleName, 'kepala') !== false) {
            if ($notifService->isRuleActive('CLINIC_QUEUE_WAITING')) {
                $today = date('Y-m-d');
                $waitingPatients = $db->table('patient_visits')
                                      ->where('visit_date', $today)
                                      ->where('status', 'waiting')
                                      ->countAllResults();
                if ($waitingPatients > 0) {
                    $maxV = $db->table('patient_visits')->where('visit_date', $today)->where('status', 'waiting')->selectMax('id')->get()->getRow();
                    $vId = $maxV ? $maxV->id : 0;
                    $notifications[] = [
                        'id'           => 'queue_waiting_' . $vId . '_' . $waitingPatients,
                        'key'          => 'clinic_queue_waiting',
                        'category'     => 'clinical',
                        'badge'        => 'Antrean Pasien',
                        'type'         => 'teal',
                        'icon'         => 'fas fa-user-clock',
                        'title'        => 'Pasien Menunggu Pemeriksaan Poli',
                        'message'      => 'Terdapat ' . $waitingPatients . ' pasien baru terdaftar dalam antrean menunggu pemeriksaan dokter / perawat.',
                        'link'         => base_url('klinik/soap'),
                        'target_roles' => 'dokter spesialis,dokter umum,dokter gigi,perawat,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }
        }

        // -------------------------------------------------------------
        // 4. KASIR & BILLING KLINIK (Tagihan Menunggu Pembayaran)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('clinic.billing', $permissions) || in_array('finance.cashier', $permissions) || strpos($roleName, 'kasir') !== false || strpos($roleName, 'keuangan') !== false || strpos($roleName, 'admin') !== false || strpos($roleName, 'direksi') !== false) {
            if ($notifService->isRuleActive('CASHIER_UNPAID_BILLING')) {
                $unpaidBills = $db->table('billing_transactions')
                                  ->whereIn('status', ['draft', 'open', 'unpaid', 'partial'])
                                  ->where('status !=', 'paid')
                                  ->where('status !=', 'cancelled')
                                  ->countAllResults();
                if ($unpaidBills > 0) {
                    $maxB = $db->table('billing_transactions')->whereIn('status', ['draft', 'open', 'unpaid', 'partial'])->where('status !=', 'paid')->where('status !=', 'cancelled')->selectMax('id')->get()->getRow();
                    $bId = $maxB ? $maxB->id : 0;
                    $notifications[] = [
                        'id'           => 'unpaid_bills_' . $bId . '_' . $unpaidBills,
                        'key'          => 'cashier_unpaid_bills',
                        'category'     => 'cashier',
                        'badge'        => 'Siap Bayar',
                        'type'         => 'warning',
                        'icon'         => 'fas fa-cash-register',
                        'title'        => 'Tagihan Pasien Menunggu Pembayaran',
                        'message'      => 'Ada ' . $unpaidBills . ' tagihan pasien dari poli/layanan medis yang siap diproses pembayarannya di Kasir Utama.',
                        'link'         => base_url('keuangan/kasir'),
                        'target_roles' => 'kasir,staf keuangan,accounting,super admin,administrator',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }
        }

        // -------------------------------------------------------------
        // 5. KEUANGAN & ANGGARAN (Verifikasi Pengajuan PR)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('procurement.verify', $permissions) || in_array('finance.manage', $permissions) || strpos($roleName, 'keuangan') !== false || strpos($roleName, 'accounting') !== false) {
            $pendingPr = $db->table('purchase_requests')
                            ->whereIn('status', ['submitted', 'pending_verify'])
                            ->countAllResults();
            if ($pendingPr > 0) {
                $maxPr = $db->table('purchase_requests')->whereIn('status', ['submitted', 'pending_verify'])->selectMax('id')->get()->getRow();
                $prId = $maxPr ? $maxPr->id : 0;
                $notifications[] = [
                    'id'           => 'pr_verify_' . $prId . '_' . $pendingPr,
                    'key'          => 'proc_pr_verify',
                    'category'     => 'procurement',
                    'badge'        => 'Verifikasi PR',
                    'type'         => 'info',
                    'icon'         => 'fas fa-file-invoice-dollar',
                    'title'        => 'Pengajuan PR Menunggu Verifikasi Anggaran',
                    'message'      => 'Ada ' . $pendingPr . ' pengajuan Purchase Request dari unit kerja menunggu verifikasi anggaran keuangan.',
                    'link'         => base_url('procurement/po'),
                    'target_roles' => 'keuangan,accounting,super admin',
                    'time'         => date('H:i') . ' WIB'
                ];
            }
        }

        // -------------------------------------------------------------
        // 6. DIREKSI & MANAJEMEN (Approval Otorisasi PO & PR)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('procurement.approve', $permissions) || strpos($roleName, 'direksi') !== false || strpos($roleName, 'direktur') !== false || strpos($roleName, 'manager') !== false) {
            if ($notifService->isRuleActive('PROC_APPROVAL_PENDING')) {
                $pendingApprovals = $db->table('approval_requests')
                                       ->where('status', 'pending')
                                       ->countAllResults();
                if ($pendingApprovals > 0) {
                    $maxAppr = $db->table('approval_requests')->where('status', 'pending')->selectMax('id')->get()->getRow();
                    $apprId = $maxAppr ? $maxAppr->id : 0;
                    $notifications[] = [
                        'id'           => 'po_approval_' . $apprId . '_' . $pendingApprovals,
                        'key'          => 'proc_po_approval',
                        'category'     => 'procurement',
                        'badge'        => 'Approval PO',
                        'type'         => 'danger',
                        'icon'         => 'fas fa-signature',
                        'title'        => 'Dokumen Pengadaan Menunggu Persetujuan Direksi',
                        'message'      => 'Ada ' . $pendingApprovals . ' dokumen pengadaan barang/obat menunggu persetujuan otorisasi pencairan dana.',
                        'link'         => base_url('procurement/approval'),
                        'target_roles' => 'direksi,kepala klinik,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }
        }

        // -------------------------------------------------------------
        // 7. DAPUR GIZI RESTO (Pesanan Masuk KDS)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('resto.kitchen', $permissions) || strpos($roleName, 'dapur') !== false || strpos($roleName, 'chef') !== false) {
            $pendingCooking = $db->table('restaurant_orders')
                                 ->whereIn('status', ['pending', 'cooking'])
                                 ->countAllResults();
            if ($pendingCooking > 0) {
                $maxOrd = $db->table('restaurant_orders')->whereIn('status', ['pending', 'cooking'])->selectMax('id')->get()->getRow();
                $ordId = $maxOrd ? $maxOrd->id : 0;
                $notifications[] = [
                    'id'           => 'kds_cooking_' . $ordId . '_' . $pendingCooking,
                    'key'          => 'resto_kds_order',
                    'category'     => 'resto',
                    'badge'        => 'Order Baru',
                    'type'         => 'success',
                    'icon'         => 'fas fa-utensils',
                    'title'        => 'Pesanan Makanan Baru di Dapur KDS',
                    'message'      => 'Ada ' . $pendingCooking . ' pesanan menu sehat dari kasir resto yang perlu dimasak dan disajikan.',
                    'link'         => base_url('resto/dapur'),
                    'target_roles' => 'koki dapur resto,kasir resto,super admin',
                    'time'         => date('H:i') . ' WIB'
                ];
            }
        }

        // -------------------------------------------------------------
        // 8. HRD & KEPEGAWAIAN (Pengajuan Cuti Menunggu Approval)
        // -------------------------------------------------------------
        if ($isSuperAdmin || in_array('hrd.payroll', $permissions) || strpos($roleName, 'hrd') !== false) {
            if ($notifService->isRuleActive('HRD_LEAVE_PENDING')) {
                $pendingLeaves = $db->table('employee_leaves')
                                    ->where('status', 'pending')
                                    ->countAllResults();
                if ($pendingLeaves > 0) {
                    $maxL = $db->table('employee_leaves')->where('status', 'pending')->selectMax('id')->get()->getRow();
                    $lId = $maxL ? $maxL->id : 0;
                    $notifications[] = [
                        'id'           => 'leave_pending_' . $lId . '_' . $pendingLeaves,
                        'key'          => 'hrd_leave_pending',
                        'category'     => 'hr',
                        'badge'        => 'Pengajuan Cuti',
                        'type'         => 'info',
                        'icon'         => 'fas fa-id-badge',
                        'title'        => 'Pengajuan Cuti Pegawai Menunggu Persetujuan',
                        'message'      => 'Ada ' . $pendingLeaves . ' permohonan cuti kerja karyawan yang menunggu verifikasi HRD / Manajemen.',
                        'link'         => base_url('hrd/cuti'),
                        'target_roles' => 'hrd / sdm,kepala hrd,super admin',
                        'time'         => date('H:i') . ' WIB'
                    ];
                }
            }
        }

        // Simpan seluruh notifikasi aktif ke tabel system_notifications (Persistent History)
        foreach ($notifications as $n) {
            $notifService->send([
                'notification_key' => $n['key'] ?? $n['id'],
                'category'         => $n['category'] ?? 'general',
                'type'             => $n['type'] ?? 'info',
                'badge'            => $n['badge'] ?? null,
                'icon'             => $n['icon'] ?? 'fas fa-bell',
                'title'            => $n['title'] ?? 'Pemberitahuan Sistem',
                'message'          => $n['message'] ?? '',
                'link'             => $n['link'] ?? base_url('dashboard'),
                'target_roles'     => $n['target_roles'] ?? null,
                'sender_name'      => 'System Sentinel Engine'
            ]);
        }

        $unreadCount = $notifService->countUnreadForUser($userId, $roleName, $permissions);

        return $this->response->setJSON([
            'status'      => 'success',
            'count'       => count($notifications),
            'unreadCount' => $unreadCount,
            'list'        => $notifications,
            'serverTime'  => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Halaman Riwayat Notifikasi & Manajemen Aturan Notifikasi (Notification Rules)
     */
    public function notifications()
    {
        $session = session();
        $userId = (int) ($session->get('user_id') ?: 1);
        $roleName = (string) $session->get('role_name');
        $permissions = (array) $session->get('permissions');

        $notifService = new \App\Services\NotificationService();
        $db = \Config\Database::connect('default');

        $category = $this->request->getGet('category');
        $onlyUnread = $this->request->getGet('unread') === '1';

        $notifications = $notifService->getNotificationsForUser($userId, $roleName, $permissions, 100, $category, $onlyUnread);
        $rules = $db->table('notification_rules')->orderBy('category', 'ASC')->get()->getResult();

        $unreadCount = $notifService->countUnreadForUser($userId, $roleName, $permissions);
        $totalCount = $db->table('system_notifications')->countAllResults();

        $data = [
            'title'         => 'Riwayat Notifikasi & Aturan Sistem',
            'active_menu'   => 'system',
            'notifications' => $notifications,
            'rules'         => $rules,
            'unreadCount'   => $unreadCount,
            'totalCount'    => $totalCount,
            'category'      => $category,
            'onlyUnread'    => $onlyUnread
        ];

        return view('system/notifications', $data);
    }

    /**
     * Tandai Satu Notifikasi Telah Dibaca
     */
    public function markNotificationRead($id)
    {
        $session = session();
        $userId = (int) ($session->get('user_id') ?: 1);

        $notifService = new \App\Services\NotificationService();
        $notifService->markAsRead((int) $id, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Notifikasi ditandai telah dibaca.']);
        }

        return redirect()->back();
    }

    /**
     * Tandai Seluruh Notifikasi Telah Dibaca
     */
    public function markAllNotificationsRead()
    {
        $session = session();
        $userId = (int) ($session->get('user_id') ?: 1);
        $roleName = (string) $session->get('role_name');

        $notifService = new \App\Services\NotificationService();
        $notifService->markAllAsRead($userId, $roleName);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Seluruh notifikasi berhasil ditandai telah dibaca.']);
        }

        session()->setFlashdata('success', 'Seluruh notifikasi Anda telah ditandai sebagai dibaca.');
        return redirect()->to(base_url('system/notifications'));
    }

    /**
     * Toggle Aktif / Nonaktif Aturan Notifikasi (Rules Engine)
     */
    public function toggleNotificationRule($id)
    {
        $db = \Config\Database::connect('default');
        $rule = $db->table('notification_rules')->where('id', $id)->get()->getRow();

        if ($rule) {
            $newStatus = $rule->is_active ? 0 : 1;
            $db->table('notification_rules')->where('id', $id)->update(['is_active' => $newStatus]);
            
            \App\Services\AuditService::log(
                'UPDATE',
                'Notification Rules',
                "Mengubah status aturan notifikasi '{$rule->rule_name}' menjadi " . ($newStatus ? 'AKTIF' : 'NONAKTIF'),
                'notification_rules',
                (int) $id
            );

            session()->setFlashdata('success', "Status aturan notifikasi '{$rule->rule_name}' berhasil diperbarui.");
        }

        return redirect()->to(base_url('system/notifications') . '#tab-rules');
    }

    /**
     * Audit Jejak Siapa yang Telah Membaca Notifikasi Tertentu (Read Receipts AJAX)
     */
    public function notificationReceipts($id)
    {
        $notifService = new \App\Services\NotificationService();
        $receipts = $notifService->getNotificationReadReceipts((int) $id);

        return $this->response->setJSON([
            'status'   => 'success',
            'receipts' => $receipts
        ]);
    }

    /**
     * Pengaturan Parameter, Identitas Klinik, Stempel & Integrasi API (Satu Data Referensi Terpadu)
     */
    public function settings()
    {
        $db = \Config\Database::connect('default');

        // 1. Auto Create settings table if not exists
        $db->query("CREATE TABLE IF NOT EXISTS system_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_group VARCHAR(50) NOT NULL,
            setting_key VARCHAR(50) NOT NULL UNIQUE,
            setting_value TEXT NULL,
            description VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Ensure all default settings exist (Sawamawa Medical Center Single Source of Truth)
        $defaults = [
            // Identitas & Profil Klinik
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_name', 'setting_value' => 'Sawamawa Medical Center', 'description' => 'Nama Resmi Klinik Utama'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_tagline', 'setting_value' => 'Pusat Layanan Medis Terpadu & Terpercaya', 'description' => 'Slogan / Tagline Resmi'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_address', 'setting_value' => 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB', 'description' => 'Alamat Kantor Klinik Utama'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_phone', 'setting_value' => '(0371) 23456 / 081122334455', 'description' => 'Nomor Telepon Hotline Klinik'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_email', 'setting_value' => 'info@sawamawamedicalcenter.id', 'description' => 'Email Resmi Pelayanan'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_website', 'setting_value' => 'www.sawamawamedicalcenter.id', 'description' => 'Website Resmi Klinik'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_license_number', 'setting_value' => '445/012/DINKES/2024', 'description' => 'Nomor Izin Operasional Klinik'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_license', 'setting_value' => '445/012/DINKES/2024', 'description' => 'Alias Nomor Izin Operasional Klinik'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_faskes_code', 'setting_value' => '52040101', 'description' => 'Kode Fasilitas Kesehatan (Kemenkes / BPJS / SatuSehat)'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_director', 'setting_value' => 'dr. Andi Wijaya, Sp.PD', 'description' => 'Nama Direktur / Penanggung Jawab Medis'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_head_doctor', 'setting_value' => 'dr. Andi Wijaya, Sp.PD', 'description' => 'Dokter Penanggung Jawab Medis'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_head_sip', 'setting_value' => '503/449/SIP-D/2022', 'description' => 'SIP Dokter Penanggung Jawab Medis'],
            ['setting_group' => 'klinik', 'setting_key' => 'clinic_bpjs_active', 'setting_value' => 'true', 'description' => 'Status Integrasi BPJS Kesehatan (true/false)'],

            // Branding & Dokumen Legal
            ['setting_group' => 'branding', 'setting_key' => 'clinic_logo', 'setting_value' => '', 'description' => 'File Logo Resmi Klinik'],
            ['setting_group' => 'branding', 'setting_key' => 'clinic_favicon', 'setting_value' => '', 'description' => 'File Favicon Website'],
            ['setting_group' => 'branding', 'setting_key' => 'clinic_stamp', 'setting_value' => '', 'description' => 'File Stempel Digital Resmi Klinik'],
            ['setting_group' => 'branding', 'setting_key' => 'clinic_watermark', 'setting_value' => 'SAWAMAWA', 'description' => 'Teks Watermark Lembar Dokumen'],
            ['setting_group' => 'branding', 'setting_key' => 'clinic_footer_note', 'setting_value' => 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.', 'description' => 'Catatan Kaki Dokumen Cetak'],

            // API & Integrasi
            ['setting_group' => 'api', 'setting_key' => 'wa_gateway_provider', 'setting_value' => 'fonnte', 'description' => 'Provider WhatsApp Gateway (fonnte/wablas/custom)'],
            ['setting_group' => 'api', 'setting_key' => 'wa_api_token', 'setting_value' => '', 'description' => 'API Token WhatsApp Gateway'],
            ['setting_group' => 'api', 'setting_key' => 'wa_sender_number', 'setting_value' => '081122334455', 'description' => 'Nomor Pengirim WhatsApp Resmi'],

            // Apotek & Farmasi
            ['setting_group' => 'apotek', 'setting_key' => 'pharmacy_min_stock_alert', 'setting_value' => '10', 'description' => 'Batas Minimum Stok untuk Notifikasi Stok Kritis'],
            ['setting_group' => 'apotek', 'setting_key' => 'pharmacy_tax_percent', 'setting_value' => '10', 'description' => 'Persentase Pajak Penjualan Obat Apotek (%)'],
            ['setting_group' => 'apotek', 'setting_key' => 'pharmacy_profit_margin_percent', 'setting_value' => '20', 'description' => 'Persentase Margin Keuntungan Standar Obat (%)'],

            // Resto POS & Gizi
            ['setting_group' => 'resto', 'setting_key' => 'resto_tax_percent', 'setting_value' => '10', 'description' => 'Persentase Pajak Penjualan Restoran POS (%)'],
            ['setting_group' => 'resto', 'setting_key' => 'resto_service_charge_percent', 'setting_value' => '5', 'description' => 'Persentase Biaya Pelayanan Restoran (%)'],
            ['setting_group' => 'resto', 'setting_key' => 'resto_auto_billing_inpatient', 'setting_value' => 'true', 'description' => 'Otomatisasikan Pembebanan Resto ke Billing Pasien Rawat Inap (true/false)'],

            // Pendaftaran Mandiri Online
            ['setting_group' => 'online_reg', 'setting_key' => 'online_registration_active', 'setting_value' => 'true', 'description' => 'Status Pendaftaran Online (true/false)'],
            ['setting_group' => 'online_reg', 'setting_key' => 'online_registration_open_time', 'setting_value' => '06:00', 'description' => 'Jam Buka Pendaftaran Online (WITA)'],
            ['setting_group' => 'online_reg', 'setting_key' => 'online_registration_close_time', 'setting_value' => '21:00', 'description' => 'Jam Tutup Pendaftaran Online (WITA)'],
            ['setting_group' => 'online_reg', 'setting_key' => 'online_registration_closed_message', 'setting_value' => 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.', 'description' => 'Pesan Saat Pendaftaran Online Ditutup'],

            // Display TV Antrean & Pemanggil Suara
            ['setting_group' => 'display', 'setting_key' => 'tv_media_type', 'setting_value' => 'slideshow', 'description' => 'Tipe Media Display TV (slideshow/local_video/youtube)'],
            ['setting_group' => 'display', 'setting_key' => 'tv_video_url', 'setting_value' => '', 'description' => 'URL Berkas Video Lokal TV'],
            ['setting_group' => 'display', 'setting_key' => 'tv_youtube_id', 'setting_value' => 'dQw4w9WgXcQ', 'description' => 'ID Video YouTube Layar TV'],

            ['setting_group' => 'voice', 'setting_key' => 'voice_gender', 'setting_value' => 'female', 'description' => 'Karakter Gender Suara Panggilan (female/male/auto)'],
            ['setting_group' => 'voice', 'setting_key' => 'voice_chime_type', 'setting_value' => 'hospital_2tone', 'description' => 'Melodi Notifikasi Chime Antrean'],
            ['setting_group' => 'voice', 'setting_key' => 'voice_rate', 'setting_value' => '0.85', 'description' => 'Kecepatan Artikulasi Suara'],
            ['setting_group' => 'voice', 'setting_key' => 'voice_pitch', 'setting_value' => '1.0', 'description' => 'Tinggi Nada Suara (Pitch)'],
            ['setting_group' => 'voice', 'setting_key' => 'voice_volume', 'setting_value' => '1.0', 'description' => 'Volume Output Suara Panggilan'],

            // SATUSEHAT Kemenkes RI (HL7 FHIR R4)
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_active', 'setting_value' => 'true', 'description' => 'Status Aktif Integrasi SATUSEHAT (true/false)'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_mode', 'setting_value' => 'sandbox', 'description' => 'Mode Lingkungan SATUSEHAT (sandbox/staging/production)'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_org_id', 'setting_value' => '10000004', 'description' => 'Organization ID Faskes SATUSEHAT Kemenkes'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_client_id', 'setting_value' => 'MOCK-CLIENT-ID-SAWAMAWA', 'description' => 'Client ID DTO Kemenkes'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_client_secret', 'setting_value' => 'MOCK-CLIENT-SECRET-SAWAMAWA', 'description' => 'Client Secret DTO Kemenkes'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_auth_url', 'setting_value' => 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1', 'description' => 'URL OAuth2 Auth Token SATUSEHAT'],
            ['setting_group' => 'satusehat', 'setting_key' => 'satusehat_fhir_url', 'setting_value' => 'https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1', 'description' => 'Base URL HL7 FHIR R4 SATUSEHAT']
        ];

        foreach ($defaults as $def) {
            $check = $db->table('system_settings')->where('setting_key', $def['setting_key'])->countAllResults();
            if ($check === 0) {
                $db->table('system_settings')->insert($def);
            }
        }

        // 3. Handle Update Request
        if (strtolower($this->request->getMethod()) === 'post') {
            $settingsInput = $this->request->getPost('settings');
            
            // Map group key helper
            $groupMap = [
                'clinic_name' => 'klinik', 'clinic_tagline' => 'klinik', 'clinic_address' => 'klinik',
                'clinic_phone' => 'klinik', 'clinic_email' => 'klinik', 'clinic_website' => 'klinik',
                'clinic_license_number' => 'klinik', 'clinic_license' => 'klinik', 'clinic_faskes_code' => 'klinik',
                'clinic_director' => 'klinik', 'clinic_head_doctor' => 'klinik', 'clinic_head_sip' => 'klinik',
                'clinic_bpjs_active' => 'klinik',
                'clinic_logo' => 'branding', 'clinic_favicon' => 'branding', 'clinic_stamp' => 'branding',
                'clinic_watermark' => 'branding', 'clinic_footer_note' => 'branding',
                'pharmacy_min_stock_alert' => 'apotek', 'pharmacy_tax_percent' => 'apotek', 'pharmacy_profit_margin_percent' => 'apotek',
                'resto_tax_percent' => 'resto', 'resto_service_charge_percent' => 'resto', 'resto_auto_billing_inpatient' => 'resto',
                'wa_gateway_provider' => 'api', 'wa_api_token' => 'api', 'wa_sender_number' => 'api',
                'online_registration_active' => 'online_reg', 'online_registration_open_time' => 'online_reg',
                'online_registration_close_time' => 'online_reg', 'online_registration_closed_message' => 'online_reg',
                'tv_media_type' => 'display', 'tv_video_url' => 'display', 'tv_youtube_id' => 'display',
                'voice_gender' => 'voice', 'voice_chime_type' => 'voice', 'voice_rate' => 'voice',
                'voice_pitch' => 'voice', 'voice_volume' => 'voice',
                'satusehat_active' => 'satusehat', 'satusehat_mode' => 'satusehat', 'satusehat_org_id' => 'satusehat',
                'satusehat_client_id' => 'satusehat', 'satusehat_client_secret' => 'satusehat',
                'satusehat_auth_url' => 'satusehat', 'satusehat_fhir_url' => 'satusehat'
            ];

            if (is_array($settingsInput)) {
                foreach ($settingsInput as $key => $val) {
                    $exists = $db->table('system_settings')->where('setting_key', $key)->countAllResults();
                    if ($exists > 0) {
                        $db->table('system_settings')
                           ->where('setting_key', $key)
                           ->update([
                               'setting_value' => $val,
                               'updated_at'    => date('Y-m-d H:i:s')
                           ]);
                    } else {
                        $targetGroup = $groupMap[$key] ?? 'klinik';
                        $db->table('system_settings')->insert([
                            'setting_group' => $targetGroup,
                            'setting_key'   => $key,
                            'setting_value' => $val,
                            'description'   => $key,
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                // Automatic synchronization of essential aliases
                if (isset($settingsInput['clinic_license_number'])) {
                    $db->table('system_settings')
                       ->where('setting_key', 'clinic_license')
                       ->update(['setting_value' => $settingsInput['clinic_license_number']]);
                }
                if (isset($settingsInput['clinic_director'])) {
                    $db->table('system_settings')
                       ->where('setting_key', 'clinic_head_doctor')
                       ->update(['setting_value' => $settingsInput['clinic_director']]);
                }
            }

            $uploadDir = FCPATH . 'uploads/settings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Handle Reset / Removal Flags
            if ($this->request->getPost('remove_logo') === '1') {
                $current = clinic_setting('clinic_logo', '');
                if (!empty($current) && file_exists(FCPATH . $current)) {
                    @unlink(FCPATH . $current);
                }
                $db->table('system_settings')->where('setting_key', 'clinic_logo')->update(['setting_value' => '']);
            }
            if ($this->request->getPost('remove_favicon') === '1') {
                $current = clinic_setting('clinic_favicon', '');
                if (!empty($current) && file_exists(FCPATH . $current)) {
                    @unlink(FCPATH . $current);
                }
                $db->table('system_settings')->where('setting_key', 'clinic_favicon')->update(['setting_value' => '']);
            }
            if ($this->request->getPost('remove_stamp') === '1') {
                $current = clinic_setting('clinic_stamp', '');
                if (!empty($current) && file_exists(FCPATH . $current)) {
                    @unlink(FCPATH . $current);
                }
                $db->table('system_settings')->where('setting_key', 'clinic_stamp')->update(['setting_value' => '']);
            }

            // Handle Upload Logo
            $logoFile = $this->request->getFile('clinic_logo_file');
            if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
                $newName = 'logo_' . time() . '.' . $logoFile->getExtension();
                $logoFile->move($uploadDir, $newName);
                $relPath = 'uploads/settings/' . $newName;

                $db->table('system_settings')->where('setting_key', 'clinic_logo')->update(['setting_value' => $relPath]);
            }

            // Handle Upload Favicon
            $favFile = $this->request->getFile('clinic_favicon_file');
            if ($favFile && $favFile->isValid() && !$favFile->hasMoved()) {
                $newFavName = 'favicon_' . time() . '.' . $favFile->getExtension();
                $favFile->move($uploadDir, $newFavName);
                $relFavPath = 'uploads/settings/' . $newFavName;

                $db->table('system_settings')->where('setting_key', 'clinic_favicon')->update(['setting_value' => $relFavPath]);
            }

            // Handle Upload Stempel Klinik
            $stampFile = $this->request->getFile('clinic_stamp_file');
            if ($stampFile && $stampFile->isValid() && !$stampFile->hasMoved()) {
                $newStampName = 'stamp_' . time() . '.' . $stampFile->getExtension();
                $stampFile->move($uploadDir, $newStampName);
                $relStampPath = 'uploads/settings/' . $newStampName;

                $db->table('system_settings')->where('setting_key', 'clinic_stamp')->update(['setting_value' => $relStampPath]);
            }

            if (function_exists('clear_setting_cache')) {
                clear_setting_cache();
            }

            $this->logAudit('UPDATE', 'System Settings', 'Memperbarui identitas klinik, logo/favicon/stempel, dan parameter sistem.');
            session()->setFlashdata('success', 'Seluruh konfigurasi identitas klinik, logo/favicon/stempel, dan parameter sistem berhasil diperbarui.');
            return redirect()->to(base_url('system/settings'));
        }

        // 4. Fetch settings
        $settings = $db->table('system_settings')->get()->getResult();

        $groupedSettings = [];
        foreach ($settings as $s) {
            $groupedSettings[$s->setting_group][] = $s;
        }

        // System Diagnostics Data
        $uploadDir = FCPATH . 'uploads/settings/';
        $isUploadWritable = is_dir($uploadDir) ? is_writable($uploadDir) : is_writable(FCPATH . 'uploads/');

        $diagnostics = [
            'php_version'        => PHP_VERSION,
            'ci_version'         => \CodeIgniter\CodeIgniter::CI_VERSION,
            'db_version'         => $db->getVersion(),
            'server_time'        => date('d F Y, H:i:s') . ' WITA',
            'upload_dir_writable'=> $isUploadWritable,
            'total_settings'     => count($settings),
            'environment'        => ENVIRONMENT
        ];

        $data = [
            'title'       => 'Pengaturan Parameter & Identitas Klinik (Satu Data Referensi)',
            'active_menu' => 'system-settings',
            'settings'    => $groupedSettings,
            'diagnostics' => $diagnostics
        ];

        return view('system/settings', $data);
    }

    /**
     * Uji Coba Koneksi WhatsApp Gateway (AJAX)
     */
    public function testWaGateway()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Hanya menerima request AJAX.'
            ]);
        }

        $provider = $this->request->getPost('provider') ?: clinic_setting('wa_gateway_provider', 'fonnte');
        $token    = trim((string)$this->request->getPost('token')) ?: clinic_setting('wa_api_token', '');
        $sender   = trim((string)$this->request->getPost('sender')) ?: clinic_setting('wa_sender_number', '081122334455');

        if (empty($token)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'API Token WhatsApp Gateway masih kosong. Silakan masukkan token terlebih dahulu pada form di atas.'
            ]);
        }

        // Diagnostic Ping
        if ($provider === 'fonnte') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/device');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($err) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menghubungi server Fonnte (Timeout/Network): ' . $err
                ]);
            }

            $json = json_decode($response, true);
            if ($httpCode === 200 && !empty($json['status'])) {
                $deviceName = $json['name'] ?? ($json['device'] ?? 'Device Terhubung');
                $deviceStatus = $json['device_status'] ?? 'Connect';
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => "Koneksi WhatsApp Gateway Fonnte BERHASIL!\nStatus Device: {$deviceStatus} | Akun: {$deviceName}",
                    'raw'     => $json
                ]);
            } else {
                $reason = $json['reason'] ?? ($json['message'] ?? 'Token Fonnte tidak valid atau device terputus.');
                return $this->response->setJSON([
                    'status'  => 'warning',
                    'message' => "Respon Gateway: {$reason}",
                    'raw'     => $json
                ]);
            }
        } elseif ($provider === 'wablas') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://wablas.com/api/device/info?token=' . urlencode($token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menghubungi Wablas: ' . $err
                ]);
            }

            $json = json_decode($response, true);
            return $this->response->setJSON([
                'status'  => !empty($json['status']) ? 'success' : 'warning',
                'message' => 'Respon Wablas: ' . ($json['message'] ?? 'Periksa respon gateway'),
                'raw'     => $json
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Simulasi Custom Webhook WhatsApp aktif. Sistem siap mengirim webhook payload ke endpoint yang telah ditentukan.'
            ]);
        }
    }

    /**
     * Uji Coba Koneksi & Auth OAuth2 SATUSEHAT Kemenkes RI (AJAX)
     */
    public function testSatuSehat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Hanya menerima request AJAX.'
            ]);
        }

        try {
            $satuSehat = new \App\Services\SatuSehatService();
            $result = $satuSehat->testConnection();

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan internal saat menguji koneksi SATUSEHAT: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================================================
     * CMS MANAJEMEN ARTIKEL & BERITA KESEHATAN
     * =========================================================================
     */
    public function articles()
    {
        $db = \Config\Database::connect('default');

        // 1. Auto-create articles table
        $db->query("CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            category VARCHAR(100) NOT NULL DEFAULT 'Edukasi Kesehatan',
            author VARCHAR(150) NULL DEFAULT 'Tim Medis Sawamawa',
            summary TEXT NOT NULL,
            content LONGTEXT NOT NULL,
            image_url VARCHAR(255) NULL,
            status ENUM('published', 'draft') NOT NULL DEFAULT 'published',
            views INT NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Seed starter articles if empty
        $count = $db->table('articles')->countAllResults();
        if ($count === 0) {
            $starterArticles = [
                [
                    'title'       => 'Tips Menjaga Daya Tahan Tubuh & Pola Hidup Sehat di Musim Pancaroba',
                    'slug'        => 'tips-menjaga-daya-tahan-tubuh-musim-pancaroba',
                    'category'    => 'Edukasi Kesehatan',
                    'author'      => 'dr. Andi Wijaya, Sp.PD',
                    'summary'     => 'Perubahan cuaca ekstrem rentan menurunkan imunitas. Kenali 5 langkah sederhana menjaga tubuh tetap bugar dan terhindar dari infeksi virus.',
                    'content'     => '<p>Pergantian musim atau pancaroba sering kali diiringi dengan peningkatan kasus infeksi saluran pernapasan atas (ISPA), flu, hingga gangguan pencernaan. Menjaga imunitas tubuh merupakan pertahanan terdepan agar tetap aktif dan produktif.</p><h5>1. Konsumsi Makanan Bergizi Seimbang</h5><p>Perbanyak asupan vitamin C, D, dan zinc yang bisa didapatkan dari buah jeruk, sayuran hijau, telur, dan ikan. Makanan kaya antioksidan membantu menangkal radikal bebas dan memperkuat sel imun.</p><h5>2. Penuhi Kebutuhan Cairan (Minimal 2 Liter/Hari)</h5><p>Dehidrasi dapat menurunkan efektivitas membran mukosa di saluran pernapasan dalam menyaring debu dan bakteri. Pastikan minum air putih cukup sepanjang hari.</p><h5>3. Istirahat Cukup (7-8 Jam Tiap Malam)</h5><p>Saat tidur, tubuh memproduksi sitokin yang berperan penting dalam melawan infeksi dan peradangan. Kurang tidur kronis secara langsung menurunkan respon imun tubuh.</p><h5>4. Olahraga Ringan Rutin</h5><p>Aktivitas fisik seperti jalan cepat 30 menit sehari dapat memperlancar sirkulasi darah dan pergerakan sel-sel darah putih dalam mendeteksi kuman penyakit.</p><p>Jika Anda merasakan gejala demam lebih dari 3 hari, batuk memberat, atau lemas berkepanjangan, segera konsultasikan ke <strong>Poliklinik Penyakit Dalam Sawamawa Medical Center</strong> untuk pemeriksaan medis menyeluruh.</p>',
                    'image_url'   => null,
                    'status'      => 'published',
                    'views'       => 142,
                    'created_at'  => date('Y-m-d H:i:s', strtotime('-5 days'))
                ],
                [
                    'title'       => 'Pentingnya Deteksi Dini Penyakit Tidak Menular: Diabetes & Hipertensi',
                    'slug'        => 'pentingnya-deteksi-dini-diabetes-dan-hipertensi',
                    'category'    => 'Tips Medis',
                    'author'      => 'dr. Siti Rahmawati, Sp.A',
                    'summary'     => 'Hipertensi dan diabetes sering disebut "silent killer" karena kerap tidak menimbulkan gejala pada fase awal. Medical check-up rutin adalah kunci pencegahan.',
                    'content'     => '<p>Penyakit Tidak Menular (PTM) seperti tekanan darah tinggi (hipertensi) dan diabetes melitus terus menjadi penyebab utama komplikasi kardiovaskular di Indonesia. Sebagian besar pasien baru menyadari kondisinya saat telah terjadi komplikasi pada jantung, ginjal, atau mata.</p><h5>Siapa yang Wajib Melakukan Skrining?</h5><ul><li>Individu berusia 30 tahun ke atas.</li><li>Memiliki riwayat keluarga dengan diabetes atau hipertensi.</li><li>Memiliki indeks massa tubuh (BMI) di atas batas normal atau obesitas.</li><li>Gaya hidup kurang gerak (sedentary lifestyle) dan perokok aktif.</li></ul><h5>Pemeriksaan Berkala di Sawamawa Medical Center</h5><p>Laboratorium kami menyediakan paket pemeriksaan gula darah puasa, HbA1c, profil lipid (kolesterol), serta pemantauan tensi digital terintegrasi rekam medis elektronik.</p>',
                    'image_url'   => null,
                    'status'      => 'published',
                    'views'       => 98,
                    'created_at'  => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'title'       => 'Transformasi Digital: Layanan E-Resep & Antrean Realtime di Sawamawa Medical Center',
                    'slug'        => 'transformasi-digital-layanan-e-resep-antrean-realtime',
                    'category'    => 'Berita Klinik',
                    'author'      => 'Humas & IT Sawamawa',
                    'summary'     => 'Sawamawa Medical Center mengimplementasikan sistem rekam medis elektronik terintegrasi SATUSEHAT Kemenkes RI, e-resep otomatis farmasi, dan reservasi online.',
                    'content'     => '<p>Sebagai wujud komitmen memberikan pelayanan medis yang modern, cepat, dan transparan, <strong>Sawamawa Medical Center</strong> telah mengadopsi SIM-Klinik digital terpadu.</p><h5>Fitur Unggulan Sistem Baru:</h5><ol><li><strong>Pendaftaran Online Mandiri:</strong> Pasien baru dan lama dapat memesan nomor antrean dokter tanpa harus antre fisik sejak subuh.</li><li><strong>E-Resep Digital Tanpa Kertas:</strong> Resep obat dari ruang periksa dokter langsung terkirim secara instan ke Instalasi Farmasi, memangkas waktu tunggu penyiapan obat hingga 60%.</li><li><strong>Integrasi SATUSEHAT Kemenkes RI:</strong> Riwayat diagnosis dan resume medis pasien terstandar secara nasional dan aman terlindungi.</li></ol>',
                    'image_url'   => null,
                    'status'      => 'published',
                    'views'       => 215,
                    'created_at'  => date('Y-m-d H:i:s', strtotime('-1 days'))
                ],
                [
                    'title'       => 'Pentingnya Nutrisi Terukur untuk Pasien Rawat Jalan dan Keluarga',
                    'slug'        => 'pentingnya-nutrisi-terukur-rawat-jalan',
                    'category'    => 'Layanan & Fasilitas',
                    'author'      => 'Instalasi Gizi & Resto Sawamawa',
                    'summary'     => 'Makanan bergizi bukan hanya untuk pemulihan pasien, melainkan investasi kesehatan jangka panjang. Resto Sehat Sawamawa siap menyajikan menu diet higienis.',
                    'content'     => '<p>Gizi yang tepat memegang peranan krusial dalam mempercepat proses penyembuhan jaringan tubuh dan menjaga kestabilan metabolisme. Di <strong>Resto Gizi Sehat Sawamawa</strong>, setiap menu dikurasi oleh ahli gizi klinis dengan takaran kalori, natrium, dan gula yang terukur.</p><p>Keluarga pasien dan masyarakat umum dapat menikmati aneka hidangan sehat, jus segar tanpa pemanis buatan, serta sup nutrisi yang higienis dan lezat.</p>',
                    'image_url'   => null,
                    'status'      => 'published',
                    'views'       => 76,
                    'created_at'  => date('Y-m-d H:i:s')
                ]
            ];
            $db->table('articles')->insertBatch($starterArticles);
        }

        // 3. Handle POST Actions (Create, Update, Delete, Toggle)
        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action') ?: 'create';

            if ($action === 'create' || $action === 'update') {
                $title    = trim(strip_tags((string)$this->request->getPost('title')));
                $category = trim(strip_tags((string)$this->request->getPost('category'))) ?: 'Edukasi Kesehatan';
                $author   = trim(strip_tags((string)$this->request->getPost('author'))) ?: 'Tim Medis Sawamawa';
                $summary  = trim(strip_tags((string)$this->request->getPost('summary')));
                $content  = trim((string)$this->request->getPost('content'));
                $status   = $this->request->getPost('status') === 'draft' ? 'draft' : 'published';

                if (empty($title) || empty($content)) {
                    session()->setFlashdata('error', 'Judul dan konten artikel wajib diisi.');
                    return redirect()->to(base_url('system/articles'));
                }

                // Base Slug Generator
                $slug = url_title($title, '-', true);

                $articleData = [
                    'title'      => $title,
                    'category'   => $category,
                    'author'     => $author,
                    'summary'    => $summary ?: mb_substr(strip_tags($content), 0, 180) . '...',
                    'content'    => $content,
                    'status'     => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Handle Image Upload
                $imgFile = $this->request->getFile('article_image');
                if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
                    $uploadDir = FCPATH . 'uploads/articles/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $newName = 'art_' . time() . '_' . rand(100, 999) . '.' . $imgFile->getExtension();
                    $imgFile->move($uploadDir, $newName);
                    $articleData['image_url'] = 'uploads/articles/' . $newName;
                }

                if ($action === 'create') {
                    // Check duplicate slug
                    $existingSlug = $db->table('articles')->where('slug', $slug)->countAllResults();
                    if ($existingSlug > 0) {
                        $slug .= '-' . time();
                    }
                    $articleData['slug']       = $slug;
                    $articleData['views']      = 0;
                    $articleData['created_at'] = date('Y-m-d H:i:s');

                    $db->table('articles')->insert($articleData);
                    $this->logAudit('CREATE', 'Articles', "Menambahkan artikel/berita baru: {$title}");
                    session()->setFlashdata('success', 'Artikel / berita kesehatan baru berhasil diterbitkan.');
                } else {
                    $articleId = (int)$this->request->getPost('article_id');
                    $db->table('articles')->where('id', $articleId)->update($articleData);
                    $this->logAudit('UPDATE', 'Articles', "Memperbarui artikel/berita ID: {$articleId}");
                    session()->setFlashdata('success', 'Artikel / berita berhasil diperbarui.');
                }

                return redirect()->to(base_url('system/articles'));
            }

            if ($action === 'delete') {
                $articleId = (int)$this->request->getPost('article_id');
                $db->table('articles')->where('id', $articleId)->delete();
                $this->logAudit('DELETE', 'Articles', "Menghapus artikel ID: {$articleId}");
                session()->setFlashdata('success', 'Artikel berhasil dihapus.');
                return redirect()->to(base_url('system/articles'));
            }

            if ($action === 'toggle_status') {
                $articleId  = (int)$this->request->getPost('article_id');
                $currentArt = $db->table('articles')->where('id', $articleId)->get()->getRow();
                if ($currentArt) {
                    $newStatus = $currentArt->status === 'published' ? 'draft' : 'published';
                    $db->table('articles')->where('id', $articleId)->update(['status' => $newStatus]);
                    session()->setFlashdata('success', "Status artikel diubah menjadi {$newStatus}.");
                }
                return redirect()->to(base_url('system/articles'));
            }
        }

        // 4. Fetch all articles
        $articles = $db->table('articles')->orderBy('id', 'DESC')->get()->getResult();

        $data = [
            'title'       => 'Manajemen Publikasi Artikel & Berita Medis',
            'active_menu' => 'system-articles',
            'articles'    => $articles
        ];

        return view('system/articles', $data);
    }

    /**
     * =========================================================================
     * HALAMAN PROFIL PENGGUNA (PROFILE SAYA)
     * =========================================================================
     */
    public function profile()
    {
        $db = \Config\Database::connect('default');
        $userId = session('user_id');

        // 1. Auto-migration check: ensure profile columns exist in users table
        $fields = $db->getFieldNames('users');
        if (!in_array('fullname', $fields)) {
            $db->query("ALTER TABLE users ADD COLUMN fullname VARCHAR(150) NULL AFTER username;");
        }
        if (!in_array('phone', $fields)) {
            $db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(30) NULL AFTER email;");
        }
        if (!in_array('avatar', $fields)) {
            $db->query("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER phone;");
        }
        if (!in_array('bio', $fields)) {
            $db->query("ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER avatar;");
        }

        // 2. Handle POST Update Profile Info
        if (strtolower($this->request->getMethod()) === 'post') {
            $fullname = trim(strip_tags((string)$this->request->getPost('fullname')));
            $email    = trim(strip_tags((string)$this->request->getPost('email')));
            $phone    = trim(strip_tags((string)$this->request->getPost('phone')));
            $bio      = trim(strip_tags((string)$this->request->getPost('bio')));

            // Check email uniqueness if changed
            $existing = $db->table('users')
                           ->where('email', $email)
                           ->where('id !=', $userId)
                           ->get()
                           ->getRow();
            if ($existing) {
                session()->setFlashdata('error', 'Alamat email sudah digunakan oleh akun pengguna lain.');
                return redirect()->to(base_url('profile'));
            }

            $updateData = [
                'fullname' => $fullname,
                'email'    => $email,
                'phone'    => $phone,
                'bio'      => $bio
            ];

            // Handle Avatar Upload if submitted in this form
            $avatarFile = $this->request->getFile('avatar');
            if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
                $ext = strtolower($avatarFile->getExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    session()->setFlashdata('error', 'Format foto profil harus berupa JPG, JPEG, PNG, atau WEBP.');
                    return redirect()->to(base_url('profile'));
                }

                $uploadDir = FCPATH . 'uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newAvatarName = 'avatar_user_' . $userId . '_' . time() . '.' . $ext;
                $avatarFile->move($uploadDir, $newAvatarName);
                $updateData['avatar'] = 'uploads/avatars/' . $newAvatarName;

                // Update session
                session()->set('user_avatar', $updateData['avatar']);
            }

            $db->table('users')->where('id', $userId)->update($updateData);

            // Update session email & fullname
            session()->set('email', $email);
            if (!empty($fullname)) {
                session()->set('fullname', $fullname);
            }

            $this->logAudit('UPDATE', 'User Profile', 'Memperbarui profil diri: ' . session('username'));
            session()->setFlashdata('success', 'Informasi profil dan identitas diri Anda berhasil diperbarui!');
            return redirect()->to(base_url('profile'));
        }

        // 3. Fetch current user data
        $user = $db->table('users')
                   ->select('users.*, roles.name as role_name, roles.description as role_desc')
                   ->join('roles', 'roles.id = users.role_id')
                   ->where('users.id', $userId)
                   ->get()
                   ->getRow();

        // 4. Fetch assigned permissions
        $userPerms = [];
        try {
            $userPerms = $db->table('user_permissions')
                            ->select('permissions.name, permissions.description')
                            ->join('permissions', 'permissions.id = user_permissions.permission_id')
                            ->where('user_permissions.user_id', $userId)
                            ->get()
                            ->getResult();
        } catch (\Throwable $e) {
            log_message('error', 'user_permissions query error in user detail: ' . $e->getMessage());
        }

        if (empty($userPerms)) {
            try {
                $userPerms = $db->table('role_permissions')
                                ->select('permissions.name, permissions.description')
                                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                                ->where('role_permissions.role_id', $user->role_id)
                                ->get()
                                ->getResult();
            } catch (\Throwable $e) {
                log_message('error', 'role_permissions query error in user detail: ' . $e->getMessage());
            }
        }

        // 5. Fetch recent user audit logs
        $recentLogs = $db->table('audit_logs')
                         ->where('user_id', $userId)
                         ->orderBy('id', 'DESC')
                         ->limit(10)
                         ->get()
                         ->getResult();

        $data = [
            'title'       => 'Profil Pengguna - ' . ($user->fullname ?: $user->username),
            'active_menu' => 'profile',
            'user'        => $user,
            'permissions' => $userPerms,
            'recent_logs' => $recentLogs
        ];

        return view('system/profile', $data);
    }

    /**
     * Update Password Pengguna
     */
    public function updatePassword()
    {
        $db = \Config\Database::connect('default');
        $userId = session('user_id');

        $oldPassword     = (string)$this->request->getPost('old_password');
        $newPassword     = (string)$this->request->getPost('new_password');
        $confirmPassword = (string)$this->request->getPost('confirm_password');

        if (empty($oldPassword) || empty($newPassword)) {
            session()->setFlashdata('error', 'Password lama dan Password baru wajib diisi.');
            return redirect()->to(base_url('profile') . '#tab-keamanan');
        }

        if (strlen($newPassword) < 6) {
            session()->setFlashdata('error', 'Password baru minimal harus 6 karakter.');
            return redirect()->to(base_url('profile') . '#tab-keamanan');
        }

        if ($newPassword !== $confirmPassword) {
            session()->setFlashdata('error', 'Konfirmasi password baru tidak cocok.');
            return redirect()->to(base_url('profile') . '#tab-keamanan');
        }

        $user = $db->table('users')->where('id', $userId)->get()->getRow();
        if (!$user || !password_verify($oldPassword, $user->password)) {
            session()->setFlashdata('error', 'Password lama yang Anda masukkan tidak sesuai.');
            return redirect()->to(base_url('profile') . '#tab-keamanan');
        }

        $db->table('users')->where('id', $userId)->update([
            'password'       => password_hash($newPassword, PASSWORD_BCRYPT),
            'plain_password' => $newPassword
        ]);

        $this->logAudit('SECURITY', 'User Profile', 'Mengubah kata sandi akun: ' . session('username'));
        session()->setFlashdata('success', 'Kata sandi / Password akun Anda berhasil diperbarui dengan aman!');
        return redirect()->to(base_url('profile') . '#tab-keamanan');
    }

    /**
     * Upload / Ganti Foto Profil Cepat
     */
    public function uploadAvatar()
    {
        $db = \Config\Database::connect('default');
        $userId = session('user_id');

        $fields = $db->getFieldNames('users');
        if (!in_array('avatar', $fields)) {
            $db->query("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER email;");
        }

        $avatarFile = $this->request->getFile('avatar_quick');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $ext = strtolower($avatarFile->getExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                session()->setFlashdata('error', 'Format foto harus berupa JPG, PNG, atau WEBP.');
                return redirect()->to(base_url('profile'));
            }

            $uploadDir = FCPATH . 'uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newAvatarName = 'avatar_user_' . $userId . '_' . time() . '.' . $ext;
            $avatarFile->move($uploadDir, $newAvatarName);
            $relPath = 'uploads/avatars/' . $newAvatarName;

            $db->table('users')->where('id', $userId)->update(['avatar' => $relPath]);
            session()->set('user_avatar', $relPath);

            $this->logAudit('UPDATE', 'User Profile', 'Memperbarui foto profil: ' . session('username'));
            session()->setFlashdata('success', 'Foto profil Anda berhasil diunggah dan diperbarui!');
        } else {
            session()->setFlashdata('error', 'Gagal mengunggah foto. Pastikan file gambar valid.');
        }

        return redirect()->to(base_url('profile'));
    }

    /**
     * Menu Apa Yang Baru / Riwayat Pembaruan & Fitur Baru Sistem (Database-Driven)
     */
    public function whatsNew()
    {
        $db = \Config\Database::connect('default');
        $isSuper = (session('role_name') === 'Super Admin' || session('role_name') === 'IT');

        $builder = $db->table('system_updates');
        if (!$isSuper) {
            $builder->where('is_published', 1);
        }
        $updates = $builder->orderBy('release_date', 'DESC')
                           ->orderBy('id', 'DESC')
                           ->get()
                           ->getResult();

        $latestRow = $db->table('system_updates')
                        ->where('is_published', 1)
                        ->orderBy('release_date', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->get(1)
                        ->getRow();
        $currentVersion = $latestRow ? $latestRow->version : 'v2.5.0';

        $data = [
            'title'          => 'Apa yang Baru? - Log Pembaruan & Rilis Fitur',
            'active_menu'    => 'system-whats-new',
            'updates'        => $updates,
            'currentVersion' => $currentVersion,
            'isSuper'        => $isSuper
        ];

        return view('system/whats_new', $data);
    }

    /**
     * Endpoint API JSON untuk Memeriksa Versi Sistem & Rilis Terkini
     */
    public function systemVersion()
    {
        $info = clinic_latest_update_info();

        return $this->response->setJSON([
            'status'        => 'success',
            'version'       => $info ? $info->version : 'v2.9.0',
            'title'         => $info ? $info->title : 'Sawamawa Medical Center Enterprise',
            'category'      => $info ? $info->category : 'PERFORMA & DATA',
            'badge_color'   => $info ? $info->badge_color : 'teal',
            'release_date'  => $info ? $info->release_date : '2026-09-26',
            'summary'       => $info ? $info->summary : '',
            'details'       => $info ? $info->details : '',
            'is_major'      => $info ? (int)$info->is_major : 1,
            'timestamp'     => time()
        ]);
    }

    /**
     * Simpan / Perbarui Data Pembaruan Sistem (Admin)
     */
    public function saveWhatsNew()
    {
        $roleName = session('role_name');
        if ($roleName !== 'Super Admin' && $roleName !== 'IT') {
            session()->setFlashdata('error', 'Akses ditolak. Anda tidak memiliki izin mengelola log pembaruan.');
            return redirect()->to(base_url('system/whats-new'));
        }

        $db = \Config\Database::connect('default');
        $id = $this->request->getPost('id');

        $version     = trim($this->request->getPost('version'));
        $title       = trim($this->request->getPost('title'));
        $category    = trim($this->request->getPost('category')) ?: 'FITUR BARU';
        $badgeColor  = trim($this->request->getPost('badge_color')) ?: 'teal';
        $releaseDate = $this->request->getPost('release_date') ?: date('Y-m-d');
        $summary     = trim($this->request->getPost('summary'));
        $details     = trim($this->request->getPost('details'));
        $isMajor     = $this->request->getPost('is_major') ? 1 : 0;
        $isPublished = $this->request->getPost('is_published') !== null ? (int)$this->request->getPost('is_published') : 1;

        if (empty($version) || empty($title)) {
            session()->setFlashdata('error', 'Versi dan Judul Pembaruan wajib diisi.');
            return redirect()->to(base_url('system/whats-new'));
        }

        $payload = [
            'version'      => $version,
            'title'        => $title,
            'category'     => $category,
            'badge_color'  => $badgeColor,
            'release_date' => $releaseDate,
            'summary'      => $summary,
            'details'      => $details,
            'is_major'     => $isMajor,
            'is_published' => $isPublished,
            'created_by'   => session('user_id') ?: 1
        ];

        if (!empty($id)) {
            $db->table('system_updates')->where('id', $id)->update($payload);
            $this->logAudit('UPDATE', 'System Updates', 'Memperbarui rilis fitur ' . $version . ' - ' . $title);
            session()->setFlashdata('success', 'Pembaruan rilis ' . esc($version) . ' berhasil diperbarui!');
        } else {
            $db->table('system_updates')->insert($payload);
            $this->logAudit('CREATE', 'System Updates', 'Menambahkan rilis fitur baru ' . $version . ' - ' . $title);
            session()->setFlashdata('success', 'Pembaruan rilis fitur baru ' . esc($version) . ' berhasil ditambahkan!');
        }

        return redirect()->to(base_url('system/whats-new'));
    }

    /**
     * Hapus Data Pembaruan Sistem (Admin)
     */
    public function deleteWhatsNew()
    {
        $roleName = session('role_name');
        if ($roleName !== 'Super Admin' && $roleName !== 'IT') {
            session()->setFlashdata('error', 'Akses ditolak.');
            return redirect()->to(base_url('system/whats-new'));
        }

        $db = \Config\Database::connect('default');
        $id = $this->request->getPost('id');

        if (!empty($id)) {
            $row = $db->table('system_updates')->where('id', $id)->get()->getRow();
            if ($row) {
                $db->table('system_updates')->where('id', $id)->delete();
                $this->logAudit('DELETE', 'System Updates', 'Menghapus log rilis fitur: ' . $row->version . ' - ' . $row->title);
                session()->setFlashdata('success', 'Catatan pembaruan ' . esc($row->version) . ' berhasil dihapus.');
            }
        }

        return redirect()->to(base_url('system/whats-new'));
    }

    /**
     * Toggle Status Publish Pembaruan Sistem
     */
    public function toggleWhatsNew($id)
    {
        $roleName = session('role_name');
        if ($roleName !== 'Super Admin' && $roleName !== 'IT') {
            session()->setFlashdata('error', 'Akses ditolak.');
            return redirect()->to(base_url('system/whats-new'));
        }

        $db = \Config\Database::connect('default');
        $row = $db->table('system_updates')->where('id', $id)->get()->getRow();
        if ($row) {
            $newStatus = $row->is_published ? 0 : 1;
            $db->table('system_updates')->where('id', $id)->update(['is_published' => $newStatus]);
            session()->setFlashdata('success', 'Status rilis ' . esc($row->version) . ' berhasil diubah.');
        }

        return redirect()->to(base_url('system/whats-new'));
    }

    // =========================================================================
    // MODUL WHATSAPP GATEWAY & REAL-TIME NOTIFICATIONS
    // =========================================================================
    public function whatsapp()
    {
        $db = \Config\Database::connect('default');

        $logs = $db->table('whatsapp_logs')
                   ->orderBy('id', 'DESC')
                   ->limit(100)
                   ->get()->getResult();

        $totalSent = $db->table('whatsapp_logs')->where('status', 'sent')->countAllResults();
        $totalReminder = $db->table('whatsapp_logs')->where('message_type', 'pengingat_kontrol')->countAllResults();

        $data = [
            'title'         => 'Otomasi WhatsApp Gateway & Notifikasi Real-time',
            'active_menu'   => 'system-whatsapp',
            'logs'          => $logs,
            'totalSent'     => $totalSent,
            'totalReminder' => $totalReminder
        ];

        return view('system/whatsapp', $data);
    }

    public function sendWhatsappSimulation()
    {
        $db = \Config\Database::connect('default');

        $phone   = trim($this->request->getPost('recipient_phone'));
        $name    = trim($this->request->getPost('recipient_name'));
        $type    = $this->request->getPost('message_type') ?: 'info_umum';
        $content = trim($this->request->getPost('message_content'));

        if (empty($phone) || empty($content)) {
            session()->setFlashdata('error', 'Nomor telepon dan isi pesan wajib diisi.');
            return redirect()->to(base_url('system/whatsapp'));
        }

        // Clean phone number format
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        // Insert to Log
        $db->table('whatsapp_logs')->insert([
            'recipient_phone'   => $cleanPhone,
            'recipient_name'    => $name,
            'message_type'      => $type,
            'message_content'   => $content,
            'status'            => 'sent',
            'sent_at'           => date('Y-m-d H:i:s'),
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        $this->logAudit('CREATE', 'WhatsApp Gateway', 'Mengirim pesan WA ke: ' . $cleanPhone . ' (' . $name . ')');
        session()->setFlashdata('success', 'Pesan WhatsApp berhasil dikirim ke ' . $name . ' (' . $cleanPhone . ')!');

        return redirect()->to(base_url('system/whatsapp'));
    }

    // =========================================================================
    // MODUL ERROR TRACKING & APM (APPLICATION PERFORMANCE MONITORING)
    // =========================================================================
    public function errorLogs()
    {
        $db = \Config\Database::connect('default');
        $request = service('request');

        $statusFilter = ($request instanceof \CodeIgniter\HTTP\IncomingRequest) ? ($request->getGet('status') ?? 'unresolved') : ($_GET['status'] ?? 'unresolved');
        $levelFilter  = ($request instanceof \CodeIgniter\HTTP\IncomingRequest) ? $request->getGet('level') : ($_GET['level'] ?? null);

        $builder = $db->table('system_error_logs')
                      ->select('system_error_logs.*, users.username, users.username as user_name')
                      ->join('users', 'users.id = system_error_logs.user_id', 'left')
                      ->orderBy('system_error_logs.updated_at', 'DESC');

        if ($statusFilter === 'unresolved') {
            $builder->where('system_error_logs.is_resolved', 0);
        } elseif ($statusFilter === 'resolved') {
            $builder->where('system_error_logs.is_resolved', 1);
        }

        if (!empty($levelFilter)) {
            $builder->where('system_error_logs.error_level', strtoupper($levelFilter));
        }

        $errorLogs = $builder->get()->getResult();

        // Calculate KPI
        $allStats = $db->table('system_error_logs')
                       ->select('
                           COUNT(*) as total_logs,
                           SUM(count) as total_occurrences,
                           SUM(CASE WHEN is_resolved = 0 THEN 1 ELSE 0 END) as unresolved_count,
                           SUM(CASE WHEN error_level = "CRITICAL" AND is_resolved = 0 THEN 1 ELSE 0 END) as critical_count
                       ')
                       ->get()
                       ->getRow();

        $data = [
            'title'        => 'Error Tracking & APM System',
            'active_menu'  => 'system-error-logs',
            'statusFilter' => $statusFilter,
            'levelFilter'  => $levelFilter,
            'errorLogs'    => $errorLogs,
            'kpi'          => [
                'totalLogs'        => (int)($allStats->total_logs ?? 0),
                'totalOccurrences' => (int)($allStats->total_occurrences ?? 0),
                'unresolvedCount'  => (int)($allStats->unresolved_count ?? 0),
                'criticalCount'    => (int)($allStats->critical_count ?? 0),
            ]
        ];

        return view('system/error_tracker', $data);
    }

    public function resolveError($id)
    {
        \App\Services\ErrorTrackerService::markResolved((int)$id);
        $this->logAudit('UPDATE', 'Error Tracker', 'Menandai error #' . $id . ' sebagai selesai');
        session()->setFlashdata('success', 'Error berhasil ditandai sebagai terselesaikan.');
        return redirect()->to(base_url('system/error-logs'));
    }

    public function clearErrorLogs()
    {
        $type = $this->request->getGet('type') ?: 'resolved';
        if ($type === 'all') {
            \App\Services\ErrorTrackerService::clearAll();
            $this->logAudit('DELETE', 'Error Tracker', 'Membersihkan seluruh log error');
            session()->setFlashdata('success', 'Seluruh riwayat log error berhasil dibersihkan.');
        } else {
            \App\Services\ErrorTrackerService::clearResolved();
            $this->logAudit('DELETE', 'Error Tracker', 'Membersihkan log error yang terselesaikan');
            session()->setFlashdata('success', 'Log error yang sudah diselesaikan berhasil dibersihkan.');
        }
        return redirect()->to(base_url('system/error-logs'));
    }

    // =========================================================================
    // MODUL SYSTEM SCALING & PERFORMANCE ENGINE
    // =========================================================================
    public function performance()
    {
        $db = \Config\Database::connect('default');

        // 1. Server & PHP Health Metrics
        $memoryUsage     = memory_get_usage(true);
        $memoryPeakUsage = memory_get_peak_usage(true);
        $memoryLimit     = ini_get('memory_limit');
        $maxExecution    = ini_get('max_execution_time') . 's';
        $opcacheEnabled  = function_exists('opcache_get_status') && (bool)opcache_get_status(false);

        // 2. Database Tables Size Analysis
        $dbName = $db->getDatabase();
        $tableStats = $db->query("
            SELECT 
                TABLE_NAME as table_name,
                TABLE_ROWS as table_rows,
                DATA_LENGTH as data_length,
                INDEX_LENGTH as index_length,
                DATA_FREE as data_free,
                (DATA_LENGTH + INDEX_LENGTH) as total_size
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            ORDER BY total_size DESC
        ", [$dbName])->getResult();

        $totalDbBytes = 0;
        $totalDbRows  = 0;
        $totalOverhead = 0;
        foreach ($tableStats as $t) {
            $totalDbBytes  += (int)$t->total_size;
            $totalDbRows   += (int)$t->table_rows;
            $totalOverhead += (int)$t->data_free;
        }

        // 3. Cache Storage Stats
        $cachePath = WRITEPATH . 'cache';
        $cacheFiles = is_dir($cachePath) ? scandir($cachePath) : [];
        $cacheCount = count(array_diff($cacheFiles ?: [], ['.', '..', '.gitkeep', 'index.html']));

        // 4. Session Count
        $sessionCount = $db->table('users')->where('status', 'active')->countAllResults();

        $data = [
            'title'          => 'System Scaling & Performance Monitor',
            'active_menu'    => 'system-performance',
            'serverMetrics'  => [
                'phpVersion'      => PHP_VERSION,
                'memoryUsageMb'   => round($memoryUsage / 1024 / 1024, 2),
                'memoryPeakMb'    => round($memoryPeakUsage / 1024 / 1024, 2),
                'memoryLimit'     => $memoryLimit,
                'maxExecution'    => $maxExecution,
                'opcacheEnabled'  => $opcacheEnabled,
                'totalDbMb'       => round($totalDbBytes / 1024 / 1024, 2),
                'totalDbRows'     => $totalDbRows,
                'totalOverheadMb' => round($totalOverhead / 1024 / 1024, 2),
                'cacheCount'      => $cacheCount,
                'sessionCount'    => $sessionCount,
            ],
            'tableStats'     => $tableStats,
        ];

        return view('system/performance', $data);
    }

    public function clearCache()
    {
        // 1. Bersihkan File Cache Sistem CI4
        $cachePath = WRITEPATH . 'cache';
        $deletedFiles = 0;
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && !in_array(basename($file), ['.gitkeep', 'index.html'])) {
                    @unlink($file);
                    $deletedFiles++;
                }
            }
        }

        // 2. Bersihkan Driver Cache Framework CI4 (File / Redis / Memcached)
        try {
            \Config\Services::cache()->clean();
        } catch (\Throwable $e) {}

        // 3. Reset OPcache PHP Bytecode
        $opcacheCleared = false;
        if (function_exists('opcache_reset')) {
            $opcacheCleared = @opcache_reset();
        }

        $this->logAudit('OPTIMIZE', 'System Scaling', 'Membersihkan cache sistem CI4, memori OPcache, dan memicu pembaruan klien');

        if ($this->request->isAJAX() || $this->request->getGet('format') === 'json' || strpos($this->request->getHeaderLine('Accept'), 'application/json') !== false) {
            return $this->response
                ->setHeader('Clear-Site-Data', '"cache"')
                ->setJSON([
                    'status'          => 'success',
                    'message'         => 'Cache sistem server, framework, dan OPcache berhasil disegarkan 100%!',
                    'deleted_files'   => $deletedFiles,
                    'opcache_cleared' => $opcacheCleared,
                    'timestamp'       => time()
                ]);
        }

        session()->setFlashdata('success', 'Cache sistem dan memori OPcache berhasil disegarkan 100%!');
        return redirect()->to(base_url('system/performance'));
    }

    public function optimizeTables()
    {
        $db = \Config\Database::connect('default');
        $dbName = $db->getDatabase();
        $tables = $db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$dbName])->getResult();

        $optimizedCount = 0;
        foreach ($tables as $t) {
            $db->query("OPTIMIZE TABLE `{$t->TABLE_NAME}`");
            $optimizedCount++;
        }

        $this->logAudit('OPTIMIZE', 'System Scaling', "Menjalankan optimasi & defragmentasi pada {$optimizedCount} tabel database");
        session()->setFlashdata('success', "Berhasil melakukan defragmentasi dan optimasi pada {$optimizedCount} tabel database!");
        return redirect()->to(base_url('system/performance'));
    }

    /**
     * Helper: Catat Log Audit Trail
     */
    private function logAudit($action, $module, $description, $tableName = '', $recordId = 0)
    {
        try {
            $db = \Config\Database::connect('default');
            $db->table('audit_logs')->insert([
                'user_id'    => session('user_id') ?: 1,
                'action'     => $action,
                'module'     => $module,
                'table_name' => $tableName ?: $module,
                'record_id'  => $recordId ?: (session('user_id') ?: 1),
                'new_value'  => $description,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => substr((string)$this->request->getUserAgent(), 0, 255),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Ignore log audit errors
        }
    }

    // =========================================================================
    // CSRF TOKEN REFRESH ENDPOINT (untuk form dinamis LiveSync)
    // =========================================================================

    /**
     * Mengembalikan CSRF token terbaru sebagai JSON.
     * Dipanggil oleh refreshCsrfMeta() di layout.php setelah LiveSync me-render
     * form baru ke dalam DOM agar token tetap sinkron dan submit tidak ditolak.
     *
     * GET system/csrf-token
     */
    public function csrfToken()
    {
        if (!session()->has('user_id')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'token_name' => csrf_token(),
            'token_hash' => csrf_hash(),
        ]);
    }
}


