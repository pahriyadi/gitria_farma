<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DevCenter extends BaseController
{
    /**
     * Pastikan hanya Super Admin atau IT (Pengembang Sistem) yang dapat mengakses
     */
    private function ensureDeveloperAccess(): bool
    {
        $roleName = (string) session('role_name');
        return in_array($roleName, ['Super Admin', 'IT'], true);
    }

    /**
     * Dashboard Utama Ruang Khusus Pengembang (Developer Command Center)
     */
    public function index()
    {
        if (!$this->ensureDeveloperAccess()) {
            session()->setFlashdata('error', 'Akses Ditolak: Ruang Khusus Pengembang hanya diperuntukkan bagi Super Admin / IT.');
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect('default');

        // 1. Server & Environment Telemetry
        $telemetry = $this->collectServerTelemetry($db);

        // 2. Anomaly & Security Radar (Hal Tidak Wajar)
        $anomalies = $this->detectAnomalies($db);

        // 3. Pengawasan Karyawan & Aktivitas Pengguna
        $today = date('Y-m-d');
        $employees = [];
        try {
            $employees = $db->table('employees e')
                            ->select('e.id, e.nip, e.name, e.department, e.position, e.email, e.phone, e.status,
                                      ea.check_in_time, ea.check_out_time, ea.status as att_status, ea.late_minutes')
                            ->join('employee_attendances ea', "ea.employee_id = e.id AND ea.date = '{$today}'", 'left')
                            ->where('e.status', 'active')
                            ->orderBy('e.department', 'ASC')
                            ->orderBy('e.name', 'ASC')
                            ->get()
                            ->getResult();
        } catch (\Throwable $e) {
            log_message('error', 'DevCenter employees query: ' . $e->getMessage());
        }

        $users = [];
        try {
            $users = $db->table('users u')
                        ->select('u.id, u.username, u.email, u.status, u.created_at, r.name as role_name')
                        ->join('roles r', 'r.id = u.role_id', 'left')
                        ->orderBy('u.id', 'ASC')
                        ->get()
                        ->getResult();
        } catch (\Throwable $e) {}

        $auditLogs = [];
        try {
            $auditLogs = $db->table('audit_logs al')
                            ->select('al.*, u.username, r.name as role_name')
                            ->join('users u', 'u.id = al.user_id', 'left')
                            ->join('roles r', 'r.id = u.role_id', 'left')
                            ->orderBy('al.id', 'DESC')
                            ->limit(150)
                            ->get()
                            ->getResult();
        } catch (\Throwable $e) {}

        // 4. Error Tracker & CI4 Log Files
        $errorLogs = [];
        try {
            if ($db->tableExists('system_error_logs')) {
                $errorLogs = $db->table('system_error_logs sel')
                                ->select('sel.*, u.username')
                                ->join('users u', 'u.id = sel.user_id', 'left')
                                ->orderBy('sel.is_resolved', 'ASC')
                                ->orderBy('sel.updated_at', 'DESC')
                                ->limit(100)
                                ->get()
                                ->getResult();
            }
        } catch (\Throwable $e) {}

        $logFiles = $this->listCi4LogFiles();
        $selectedLogFile = !empty($logFiles) ? $logFiles[0]['name'] : null;
        $logPreview = $selectedLogFile ? $this->parseCi4LogFile($selectedLogFile) : ['lines' => [], 'stats' => ['critical' => 0, 'error' => 0, 'warning' => 0]];

        // 5. Database Info & Slow Queries
        $dbStats = $this->collectDatabaseStats($db);
        $slowQueries = [];
        try {
            if ($db->tableExists('system_slow_queries')) {
                $slowQueries = $db->table('system_slow_queries')
                                  ->orderBy('id', 'DESC')
                                  ->limit(50)
                                  ->get()
                                  ->getResult();
            }
        } catch (\Throwable $e) {}

        // 6. Integrasi & API Status
        $integrations = $this->collectIntegrationStatus($db);

        return view('system/dev_center', [
            'title'           => 'Ruang Pengembang (Developer Command Center)',
            'active_menu'     => 'system-dev-center',
            'telemetry'       => $telemetry,
            'anomalies'       => $anomalies,
            'employees'       => $employees,
            'users'           => $users,
            'auditLogs'       => $auditLogs,
            'errorLogs'       => $errorLogs,
            'logFiles'        => $logFiles,
            'selectedLogFile' => $selectedLogFile,
            'logPreview'      => $logPreview,
            'dbStats'         => $dbStats,
            'slowQueries'     => $slowQueries,
            'integrations'    => $integrations,
        ]);
    }

    /**
     * Endpoint JSON Live Telemetry (Refresh Tanpa Reload)
     */
    public function telemetryJson()
    {
        if (!$this->ensureDeveloperAccess()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $db = \Config\Database::connect('default');
        $telemetry = $this->collectServerTelemetry($db);
        $anomalies = $this->detectAnomalies($db);

        return $this->response->setJSON([
            'status'          => 'success',
            'timestamp'       => date('H:i:s'),
            'memory_used_mb'  => $telemetry['memory_used_mb'],
            'memory_peak_mb'  => $telemetry['memory_peak_mb'],
            'disk_free_gb'    => $telemetry['disk_free_gb'],
            'disk_used_pct'   => $telemetry['disk_used_pct'],
            'unresolved_err'  => $anomalies['counts']['unresolved_errors'],
            'failed_login_24h'=> $anomalies['counts']['failed_logins_24h'],
            'midnight_act'    => $anomalies['counts']['midnight_activities'],
            'risk_actions'    => $anomalies['counts']['high_risk_actions'],
        ]);
    }

    /**
     * AJAX Endpoint untuk membaca file log harian CI4
     */
    public function readLogFile()
    {
        if (!$this->ensureDeveloperAccess()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $filename = basename((string) $this->request->getGet('file'));
        if (!preg_match('/^log-\d{4}-\d{2}-\d{2}\.log$/', $filename)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama file log tidak valid.']);
        }

        $parsed = $this->parseCi4LogFile($filename);
        return $this->response->setJSON([
            'status' => 'success',
            'file'   => $filename,
            'lines'  => $parsed['lines'],
            'stats'  => $parsed['stats'],
        ]);
    }

    /**
     * Hapus file log harian tertentu
     */
    public function clearLogFile()
    {
        if (!$this->ensureDeveloperAccess()) {
            return redirect()->to(base_url('dashboard'));
        }

        $filename = basename((string) $this->request->getPost('file'));
        if (preg_match('/^log-\d{4}-\d{2}-\d{2}\.log$/', $filename)) {
            $path = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path)) {
                @unlink($path);
                session()->setFlashdata('success', "File log {$filename} berhasil dihapus.");
            }
        }

        return redirect()->to(base_url('system/dev-center#tab-errors'));
    }

    /**
     * Tandai Error pada system_error_logs sebagai Resolved
     */
    public function resolveError($id)
    {
        if (!$this->ensureDeveloperAccess()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        $db = \Config\Database::connect('default');
        $db->table('system_error_logs')->where('id', (int) $id)->update([
            'is_resolved' => 1,
            'resolved_at' => date('Y-m-d H:i:s'),
            'resolved_by' => session('user_id') ?: 1
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Error berhasil ditandai selesai.']);
        }

        session()->setFlashdata('success', 'Error log berhasil ditandai selesai.');
        return redirect()->to(base_url('system/dev-center#tab-errors'));
    }

    /**
     * Bersihkan Cache Aplikasi & Pengaturan secara instan
     */
    public function clearCacheQuick()
    {
        if (!$this->ensureDeveloperAccess()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Forbidden']);
        }

        try {
            cache()->clean();
        } catch (\Throwable $e) {}

        if (function_exists('clear_setting_cache')) {
            clear_setting_cache();
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Cache aplikasi, pengaturan, dan OPcache berhasil dibersihkan.']);
        }

        session()->setFlashdata('success', 'Seluruh cache aplikasi berhasil dibersihkan.');
        return redirect()->to(base_url('system/dev-center'));
    }

    // =========================================================================
    // PRIVATE HELPERS: TELEMETRY, ANOMALY RADAR, LOG PARSER, DB STATS
    // =========================================================================

    private function collectServerTelemetry($db): array
    {
        $memUsedBytes = memory_get_usage(true);
        $memPeakBytes = memory_get_peak_usage(true);

        $diskFree  = @disk_free_space(ROOTPATH) ?: 0;
        $diskTotal = @disk_total_space(ROOTPATH) ?: 1;
        $diskUsed  = max(0, $diskTotal - $diskFree);
        $diskPct   = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $mysqlVersion = '-';
        try {
            $verRow = $db->query('SELECT VERSION() as v')->getRow();
            $mysqlVersion = $verRow ? $verRow->v : '-';
        } catch (\Throwable $e) {}

        // Git Commit Info (baca file .git tanpa exec agar aman di shared hosting)
        $gitBranch = '-';
        $gitHash   = '-';
        try {
            $headFile = ROOTPATH . '.git' . DIRECTORY_SEPARATOR . 'HEAD';
            if (is_file($headFile)) {
                $head = trim((string) file_get_contents($headFile));
                if (strpos($head, 'ref: ') === 0) {
                    $refPath = substr($head, 5);
                    $gitBranch = basename($refPath);
                    $refFile = ROOTPATH . '.git' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $refPath);
                    if (is_file($refFile)) {
                        $gitHash = substr(trim((string) file_get_contents($refFile)), 0, 7);
                    }
                } else {
                    $gitHash = substr($head, 0, 7);
                }
            }
        } catch (\Throwable $e) {}

        $requiredExts = ['curl', 'mysqli', 'intl', 'mbstring', 'gd', 'zip', 'openssl', 'json'];
        $extensions = [];
        foreach ($requiredExts as $ext) {
            $extensions[$ext] = extension_loaded($ext);
        }

        $dirChecks = [
            'writable/logs'    => is_writable(WRITEPATH . 'logs'),
            'writable/cache'   => is_writable(WRITEPATH . 'cache'),
            'writable/session' => is_writable(WRITEPATH . 'session'),
            'public/uploads'   => is_dir(FCPATH . 'uploads') ? is_writable(FCPATH . 'uploads') : is_writable(FCPATH),
        ];

        return [
            'php_version'       => PHP_VERSION,
            'ci_version'        => \CodeIgniter\CodeIgniter::CI_VERSION,
            'environment'       => defined('ENVIRONMENT') ? ENVIRONMENT : 'production',
            'os_info'           => php_uname('s') . ' ' . php_uname('r'),
            'server_software'   => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI / Unknown',
            'mysql_version'     => $mysqlVersion,
            'timezone'          => date_default_timezone_get(),
            'server_time'       => date('Y-m-d H:i:s'),
            'base_url'          => base_url(),
            'memory_used_mb'    => round($memUsedBytes / 1048576, 2),
            'memory_peak_mb'    => round($memPeakBytes / 1048576, 2),
            'memory_limit'      => ini_get('memory_limit'),
            'upload_max'        => ini_get('upload_max_filesize'),
            'post_max'          => ini_get('post_max_size'),
            'max_exec_time'     => ini_get('max_execution_time') . 's',
            'opcache_enabled'   => function_exists('opcache_get_status') && @opcache_get_status(false) !== false,
            'disk_free_gb'      => round($diskFree / 1073741824, 2),
            'disk_total_gb'     => round($diskTotal / 1073741824, 2),
            'disk_used_pct'     => $diskPct,
            'git_branch'        => $gitBranch,
            'git_hash'          => $gitHash,
            'extensions'        => $extensions,
            'dir_checks'        => $dirChecks,
        ];
    }

    private function detectAnomalies($db): array
    {
        $since24h = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $since7d  = date('Y-m-d H:i:s', strtotime('-7 days'));

        $failedLoginsByIp = [];
        $failedLoginsList = [];
        $midnightActivities = [];
        $highRiskActions = [];
        $unresolvedErrorsCount = 0;
        $negativeStockCount = 0;
        $staleBillingsCount = 0;
        $usersWithoutEmailCount = 0;

        try {
            $failedLoginsByIp = $db->query("
                SELECT ip_address, COUNT(*) as attempts, MAX(created_at) as last_attempt
                FROM audit_logs
                WHERE action = 'LOGIN_FAILED' AND created_at >= ?
                GROUP BY ip_address
                HAVING attempts >= 2
                ORDER BY attempts DESC
                LIMIT 20
            ", [$since24h])->getResult();

            $failedLoginsList = $db->table('audit_logs al')
                                   ->select('al.*, u.username')
                                   ->join('users u', 'u.id = al.user_id', 'left')
                                   ->where('al.action', 'LOGIN_FAILED')
                                   ->where('al.created_at >=', $since24h)
                                   ->orderBy('al.id', 'DESC')
                                   ->limit(20)
                                   ->get()
                                   ->getResult();

            // Aktivitas tidak wajar antara jam 00:00 - 05:00
            $midnightActivities = $db->query("
                SELECT al.*, u.username, r.name as role_name
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE al.created_at >= ?
                  AND HOUR(al.created_at) BETWEEN 0 AND 4
                ORDER BY al.id DESC
                LIMIT 30
            ", [$since7d])->getResult();

            // Aksi berisiko tinggi (DELETE, VOID, RESET)
            $highRiskActions = $db->table('audit_logs al')
                                  ->select('al.*, u.username, r.name as role_name')
                                  ->join('users u', 'u.id = al.user_id', 'left')
                                  ->join('roles r', 'r.id = u.role_id', 'left')
                                  ->whereIn('al.action', ['DELETE', 'VOID', 'RESET', 'RESTORE'])
                                  ->where('al.created_at >=', $since7d)
                                  ->orderBy('al.id', 'DESC')
                                  ->limit(30)
                                  ->get()
                                  ->getResult();
        } catch (\Throwable $e) {}

        try {
            if ($db->tableExists('system_error_logs')) {
                $unresolvedErrorsCount = (int) $db->table('system_error_logs')->where('is_resolved', 0)->countAllResults();
            }
        } catch (\Throwable $e) {}

        try {
            if ($db->tableExists('medicine_batches')) {
                $negativeStockCount = (int) $db->table('medicine_batches')->where('stock <', 0)->countAllResults();
            }
        } catch (\Throwable $e) {}

        try {
            if ($db->tableExists('billing_transactions')) {
                $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));
                $staleBillingsCount = (int) $db->table('billing_transactions')
                                               ->where('status', 'open')
                                               ->where('created_at <', $threeDaysAgo)
                                               ->countAllResults();
            }
        } catch (\Throwable $e) {}

        try {
            $usersWithoutEmailCount = (int) $db->table('users')
                                                 ->where('status', 'active')
                                                 ->groupStart()
                                                     ->where('email', '')
                                                     ->orWhere('email IS NULL')
                                                 ->groupEnd()
                                                 ->countAllResults();
        } catch (\Throwable $e) {}

        return [
            'failed_logins_by_ip' => $failedLoginsByIp,
            'failed_logins_list'  => $failedLoginsList,
            'midnight_activities' => $midnightActivities,
            'high_risk_actions'   => $highRiskActions,
            'counts'              => [
                'unresolved_errors'    => $unresolvedErrorsCount,
                'failed_logins_24h'    => count($failedLoginsList),
                'suspicious_ips'       => count($failedLoginsByIp),
                'midnight_activities'  => count($midnightActivities),
                'high_risk_actions'    => count($highRiskActions),
                'negative_stock'       => $negativeStockCount,
                'stale_billings'       => $staleBillingsCount,
                'users_without_email'  => $usersWithoutEmailCount,
            ]
        ];
    }

    private function listCi4LogFiles(): array
    {
        $logDir = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR;
        if (!is_dir($logDir)) {
            return [];
        }

        $files = glob($logDir . 'log-*.log') ?: [];
        rsort($files);

        $list = [];
        foreach (array_slice($files, 0, 15) as $f) {
            $list[] = [
                'name'     => basename($f),
                'size_kb'  => round(filesize($f) / 1024, 1),
                'modified' => date('Y-m-d H:i:s', filemtime($f))
            ];
        }

        return $list;
    }

    private function parseCi4LogFile(string $filename): array
    {
        $path = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . basename($filename);
        if (!is_file($path)) {
            return ['lines' => [], 'stats' => ['critical' => 0, 'error' => 0, 'warning' => 0]];
        }

        $allLines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $stats = ['critical' => 0, 'error' => 0, 'warning' => 0];

        foreach ($allLines as $l) {
            if (stripos($l, 'CRITICAL') === 0) $stats['critical']++;
            elseif (stripos($l, 'ERROR') === 0) $stats['error']++;
            elseif (stripos($l, 'WARNING') === 0) $stats['warning']++;
        }

        // Ambil 200 baris terakhir (terbaru di atas)
        $tail = array_slice($allLines, -200);
        $tail = array_reverse($tail);

        return ['lines' => $tail, 'stats' => $stats];
    }

    private function collectDatabaseStats($db): array
    {
        $dbName = $db->getDatabase();
        $tables = [];
        $totalSizeMb = 0;
        $totalRows = 0;

        try {
            $rows = $db->query("
                SELECT TABLE_NAME as table_name,
                       TABLE_ROWS as table_rows,
                       ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 3) as size_mb
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = ?
                ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            ", [$dbName])->getResult();

            foreach ($rows as $r) {
                $totalSizeMb += (float) $r->size_mb;
                $totalRows += (int) $r->table_rows;
            }
            $tables = array_slice($rows, 0, 20);
        } catch (\Throwable $e) {}

        return [
            'db_name'       => $dbName,
            'total_tables'  => count($rows ?? []),
            'total_rows'    => $totalRows,
            'total_size_mb' => round($totalSizeMb, 2),
            'top_tables'    => $tables,
        ];
    }

    private function collectIntegrationStatus($db): array
    {
        $waTotal = 0;
        try {
            if ($db->tableExists('whatsapp_logs')) {
                $waTotal = (int) $db->table('whatsapp_logs')->countAllResults();
            }
        } catch (\Throwable $e) {}

        return [
            'google_enabled'   => in_array(clinic_setting('google_login_enabled', '0'), ['1', 'true', 'on', 1], true),
            'google_mode'      => clinic_setting('google_login_mode', 'both'),
            'google_client_ok' => trim((string) clinic_setting('google_client_id', '')) !== '',
            'wa_provider'      => clinic_setting('wa_gateway_provider', 'fonnte'),
            'wa_token_ok'      => trim((string) clinic_setting('wa_api_token', '')) !== '',
            'wa_sender'        => clinic_setting('wa_sender_number', '-'),
            'wa_total_logs'    => $waTotal,
            'ss_active'        => in_array(clinic_setting('satusehat_active', '1'), ['1', 'true', 'on', 1], true),
            'ss_mode'          => clinic_setting('satusehat_mode', 'sandbox'),
            'ss_org_id'        => clinic_setting('satusehat_org_id', '-'),
            'latest_version'   => function_exists('clinic_latest_version') ? clinic_latest_version() : 'v2.12.0',
        ];
    }
}
