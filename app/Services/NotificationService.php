<?php

namespace App\Services;

class NotificationService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');
        $this->ensureTablesExist();
    }

    /**
     * Pastikan tabel-tabel notifikasi dan rules otomatis tercipta
     */
    public function ensureTablesExist()
    {
        // 1. Table system_notifications
        $this->db->query("CREATE TABLE IF NOT EXISTS system_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            notification_key VARCHAR(100) NULL,
            category VARCHAR(50) NOT NULL DEFAULT 'general',
            type VARCHAR(20) NOT NULL DEFAULT 'info',
            badge VARCHAR(50) NULL,
            icon VARCHAR(50) DEFAULT 'fas fa-bell',
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(255) NULL,
            target_roles VARCHAR(255) NULL,
            target_user_id INT NULL,
            sender_name VARCHAR(100) DEFAULT 'System Engine',
            is_broadcast TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_key (notification_key),
            INDEX idx_target_user (target_user_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Table system_notification_reads (Read Receipts Tracking)
        $this->db->query("CREATE TABLE IF NOT EXISTS system_notification_reads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            notification_id INT NOT NULL,
            user_id INT NOT NULL,
            read_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_notif_user (notification_id, user_id),
            INDEX idx_user (user_id),
            INDEX idx_notif (notification_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 3. Table notification_rules (Configurable Rules Engine)
        $this->db->query("CREATE TABLE IF NOT EXISTS notification_rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            rule_code VARCHAR(50) NOT NULL UNIQUE,
            rule_name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            description VARCHAR(255) NULL,
            target_roles VARCHAR(255) NOT NULL,
            severity VARCHAR(20) NOT NULL DEFAULT 'warning',
            icon VARCHAR(50) DEFAULT 'fas fa-bell',
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Seed default notification rules if empty
        if ($this->db->table('notification_rules')->countAllResults() == 0) {
            $defaultRules = [
                [
                    'rule_code'    => 'ACCT_UNBALANCED_JOURNAL',
                    'rule_name'    => 'Peringatan Keseimbangan Jurnal Akuntansi',
                    'category'     => 'accounting',
                    'description'  => 'Mendeteksi dan memperingatkan secara real-time jika terdapat jurnal umum yang tidak balance (Debit != Kredit).',
                    'target_roles' => 'accounting,keuangan,super admin,administrator',
                    'severity'     => 'danger',
                    'icon'         => 'fas fa-scale-unbalanced',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'ACCT_AGING_RECEIVABLES',
                    'rule_name'    => 'Peringatan Piutang Jatuh Tempo (>30 Hari)',
                    'category'     => 'accounting',
                    'description'  => 'Memberi notifikasi jika terdapat piutang pasien atau asuransi yang melewati batas waktu 30 hari.',
                    'target_roles' => 'accounting,keuangan,super admin',
                    'severity'     => 'warning',
                    'icon'         => 'fas fa-file-invoice-dollar',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'SEC_FAILED_LOGIN_ALERT',
                    'rule_name'    => 'Peringatan Percobaan Login Gagal Beruntun',
                    'category'     => 'security',
                    'description'  => 'Mendeteksi potensi ancaman keamanan atau brute-force attack pada otentikasi login sistem.',
                    'target_roles' => 'super admin,administrator,it / technical support',
                    'severity'     => 'danger',
                    'icon'         => 'fas fa-shield-halved',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'SEC_APM_ERROR_ALERT',
                    'rule_name'    => 'Peringatan Galat Aplikasi di APM Engine',
                    'category'     => 'security',
                    'description'  => 'Memunculkan notifikasi instan kepada tim IT ketika terjadi unhandled error di server.',
                    'target_roles' => 'super admin,administrator,it / technical support',
                    'severity'     => 'danger',
                    'icon'         => 'fas fa-bug',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'PHARM_LOW_STOCK',
                    'rule_name'    => 'Peringatan Stok Obat Menipis / Kritis',
                    'category'     => 'pharmacy',
                    'description'  => 'Memberi peringatan saat total stok obat mencapai atau berada di bawah batas minimum stok.',
                    'target_roles' => 'apoteker,asisten apoteker,bagian pengadaan,super admin',
                    'severity'     => 'warning',
                    'icon'         => 'fas fa-triangle-exclamation',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'PHARM_FEFO_EXPIRED',
                    'rule_name'    => 'Peringatan Batch Obat Mendekati Kadaluarsa',
                    'category'     => 'pharmacy',
                    'description'  => 'Peringatan FEFO untuk obat aktif yang akan expired dalam kurun waktu 60 hari ke depan.',
                    'target_roles' => 'apoteker,asisten apoteker,super admin',
                    'severity'     => 'warning',
                    'icon'         => 'fas fa-calendar-xmark',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'CLINIC_QUEUE_WAITING',
                    'rule_name'    => 'Peringatan Antrean Pasien Menunggu Pemeriksaan',
                    'category'     => 'clinical',
                    'description'  => 'Notifikasi real-time saat pasien baru terdaftar menunggu panggilan dokter/perawat di poliklinik.',
                    'target_roles' => 'dokter spesialis,dokter umum,dokter gigi,perawat,super admin',
                    'severity'     => 'teal',
                    'icon'         => 'fas fa-user-clock',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'CASHIER_UNPAID_BILLING',
                    'rule_name'    => 'Peringatan Tagihan Pasien Siap Bayar di Kasir',
                    'category'     => 'cashier',
                    'description'  => 'Notifikasi ketika pasien selesai dilayani di poli/farmasi dan siap melakukan pembayaran kasir.',
                    'target_roles' => 'kasir,staf keuangan,super admin',
                    'severity'     => 'warning',
                    'icon'         => 'fas fa-cash-register',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'HRD_LEAVE_PENDING',
                    'rule_name'    => 'Peringatan Pengajuan Cuti Menunggu Otorisasi',
                    'category'     => 'hr',
                    'description'  => 'Notifikasi permohonan cuti kerja karyawan baru yang membutuhkan persetujuan HRD / Manajemen.',
                    'target_roles' => 'hrd / sdm,kepala hrd,super admin',
                    'severity'     => 'info',
                    'icon'         => 'fas fa-id-badge',
                    'is_active'    => 1
                ],
                [
                    'rule_code'    => 'PROC_APPROVAL_PENDING',
                    'rule_name'    => 'Peringatan Otorisasi Pengadaan Barang / PO',
                    'category'     => 'procurement',
                    'description'  => 'Notifikasi berkas Purchase Order yang memerlukan persetujuan otorisasi Direksi / Manajemen.',
                    'target_roles' => 'direksi,kepala klinik,super admin',
                    'severity'     => 'danger',
                    'icon'         => 'fas fa-signature',
                    'is_active'    => 1
                ]
            ];
            $this->db->table('notification_rules')->insertBatch($defaultRules);
        }
    }

    /**
     * Catat / Kirim Notifikasi Baru Terpusat dengan Deduplikasi Kunci
     */
    public function send($data)
    {
        $key = $data['notification_key'] ?? null;
        
        // Jika ada key, periksa apakah notifikasi serupa sudah ada dalam 2 jam terakhir
        if ($key) {
            $exQ = $this->db->table('system_notifications')
                                 ->where('notification_key', $key)
                                 ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-2 hours')))
                                 ->get();
            $existing = ($exQ && is_object($exQ)) ? $exQ->getRow() : null;
            if ($existing) {
                // Update timestamp dan message
                $this->db->table('system_notifications')->where('id', $existing->id)->update([
                    'message'    => $data['message'] ?? $existing->message,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                return $existing->id;
            }
        }

        $insertData = [
            'notification_key' => $key,
            'category'         => $data['category'] ?? 'general',
            'type'             => $data['type'] ?? 'info',
            'badge'            => $data['badge'] ?? null,
            'icon'             => $data['icon'] ?? 'fas fa-bell',
            'title'            => $data['title'] ?? 'Pemberitahuan Sistem',
            'message'          => $data['message'] ?? '',
            'link'             => $data['link'] ?? base_url('dashboard'),
            'target_roles'     => $data['target_roles'] ?? null,
            'target_user_id'   => $data['target_user_id'] ?? null,
            'sender_name'      => $data['sender_name'] ?? 'System Engine',
            'is_broadcast'     => isset($data['is_broadcast']) ? (int)$data['is_broadcast'] : 1,
            'created_at'       => date('Y-m-d H:i:s')
        ];

        $this->db->table('system_notifications')->insert($insertData);
        return $this->db->insertID();
    }

    /**
     * Memeriksa kondisi aturan aktif (Rules Engine)
     */
    public function isRuleActive($ruleCode)
    {
        $rQ = $this->db->table('notification_rules')->where('rule_code', $ruleCode)->get();
        $rule = ($rQ && is_object($rQ)) ? $rQ->getRow() : null;
        return $rule ? (bool)$rule->is_active : true;
    }

    /**
     * Pengecekan Sistem Akuntansi (Realtime Unbalanced Journal Detection & Aging Alert)
     */
    public function checkAccountingAlerts()
    {
        $alerts = [];

        // 1. DETEKSI JURNAL TIDAK BALANCE (CRITICAL ALERT)
        if ($this->isRuleActive('ACCT_UNBALANCED_JOURNAL')) {
            $unQ = $this->db->query("
                SELECT je.id, je.journal_no, je.entry_date, je.description, 
                       SUM(jed.debit) as total_debit, 
                       SUM(jed.credit) as total_credit,
                       ABS(SUM(jed.debit) - SUM(jed.credit)) as diff
                FROM journal_entries je
                JOIN journal_entry_details jed ON jed.journal_id = je.id
                GROUP BY je.id, je.journal_no, je.entry_date, je.description
                HAVING diff > 0.01
                LIMIT 5
            ");
            $unbalanced = ($unQ && is_object($unQ)) ? $unQ->getResult() : [];

            if (!empty($unbalanced)) {
                $count = count($unbalanced);
                $first = $unbalanced[0];
                $diffFormatted = number_format($first->diff, 0, ',', '.');
                $alerts[] = [
                    'id'           => 'acct_unbalanced_' . $first->id . '_' . $count,
                    'key'          => 'acct_unbalanced_journals',
                    'category'     => 'accounting',
                    'badge'        => 'JURNAL TIDAK BALANCE',
                    'type'         => 'danger',
                    'icon'         => 'fas fa-scale-unbalanced',
                    'title'        => 'PERINGATAN KRITIS: Jurnal Akuntansi Tidak Seimbang!',
                    'message'      => "Ditemukan {$count} entri jurnal tidak seimbang. Contoh Jurnal {$first->journal_no} selisih Rp {$diffFormatted} (Debit: Rp " . number_format($first->total_debit, 0, ',', '.') . " vs Kredit: Rp " . number_format($first->total_credit, 0, ',', '.') . "). Segera lakukan rekonsiliasi!",
                    'link'         => base_url('akuntansi/jurnal'),
                    'target_roles' => 'accounting,keuangan,super admin,administrator',
                    'time'         => date('H:i') . ' WIB'
                ];
            }
        }

        // 2. DETEKSI PIUTANG PASIEN/ASURANSI MENUNGGU PEMBAYARAN
        if ($this->isRuleActive('ACCT_AGING_RECEIVABLES')) {
            $overdueCount = $this->db->table('billing_transactions')
                                     ->whereIn('status', ['open', 'unpaid'])
                                     ->where('created_at <', date('Y-m-d H:i:s', strtotime('-14 days')))
                                     ->countAllResults();
            if ($overdueCount > 0) {
                $alerts[] = [
                    'id'           => 'acct_aging_overdue_' . $overdueCount,
                    'key'          => 'acct_aging_overdue',
                    'category'     => 'accounting',
                    'badge'        => 'Piutang Menumpuk',
                    'type'         => 'warning',
                    'icon'         => 'fas fa-file-invoice-dollar',
                    'title'        => 'Piutang Pasien/Asuransi Belum Dilunasi (>14 Hari)',
                    'message'      => "Terdapat {$overdueCount} tagihan pasien/klaim belum lunas yang berumur lebih dari 14 hari.",
                    'link'         => base_url('keuangan/aging'),
                    'target_roles' => 'accounting,keuangan,super admin',
                    'time'         => date('H:i') . ' WIB'
                ];
            }
        }

        return $alerts;
    }

    /**
     * Pengecekan Security & APM Alerts
     */
    public function checkSecurityAlerts()
    {
        $alerts = [];

        // 1. APM Error Tracking Alert
        if ($this->isRuleActive('SEC_APM_ERROR_ALERT')) {
            $unresolvedCount = $this->db->table('system_error_logs')
                                        ->where('is_resolved', 0)
                                        ->countAllResults();
            if ($unresolvedCount > 0) {
                $maxErr = $this->db->table('system_error_logs')->where('is_resolved', 0)->selectMax('id')->get()->getRow();
                $errId = $maxErr ? $maxErr->id : 0;
                $alerts[] = [
                    'id'           => 'sec_apm_errors_' . $errId . '_' . $unresolvedCount,
                    'key'          => 'sec_apm_unresolved',
                    'category'     => 'security',
                    'badge'        => 'Galat APM',
                    'type'         => 'danger',
                    'icon'         => 'fas fa-bug',
                    'title'        => 'Insiden Galat Sistem Tertangkap APM Engine',
                    'message'      => "Ada {$unresolvedCount} insiden galat runtime aktif yang belum diselesaikan pada APM Error Tracker.",
                    'link'         => base_url('system/error-logs'),
                    'target_roles' => 'super admin,administrator,it / technical support',
                    'time'         => date('H:i') . ' WIB'
                ];
            }
        }

        return $alerts;
    }

    /**
     * Ambil Notifikasi untuk Pengguna Tertentu dengan Status Dibaca (Read Receipt)
     */
    public function getNotificationsForUser($userId, $roleName, $permissions = [], $limit = 50, $category = null, $onlyUnread = false)
    {
        $roleNameClean = strtolower(trim($roleName));
        $isSuperAdmin = in_array($roleNameClean, ['super admin', 'administrator', 'it / technical support']);

        $builder = $this->db->table('system_notifications sn')
                            ->select('sn.*, (CASE WHEN snr.id IS NOT NULL THEN 1 ELSE 0 END) as is_read, snr.read_at, COALESCE(u.fullname, u.username) as reader_name')
                            ->join('system_notification_reads snr', 'snr.notification_id = sn.id AND snr.user_id = ' . (int)$userId, 'left')
                            ->join('users u', 'u.id = snr.user_id', 'left');

        // Filter role authorization
        if (!$isSuperAdmin) {
            $builder->groupStart()
                    ->where('sn.target_user_id', $userId)
                    ->orWhere('sn.target_roles IS NULL')
                    ->orWhere('sn.target_roles', '')
                    ->orLike('sn.target_roles', $roleNameClean)
                    ->groupEnd();
        }

        if ($category) {
            $builder->where('sn.category', $category);
        }

        if ($onlyUnread) {
            $builder->where('snr.id IS NULL');
        }

        $builder->orderBy('sn.id', 'DESC')->limit($limit);
        return $builder->get()->getResult();
    }

    /**
     * Hitung Jumlah Notifikasi Belum Dibaca
     */
    public function countUnreadForUser($userId, $roleName, $permissions = [])
    {
        $roleNameClean = strtolower(trim($roleName));
        $isSuperAdmin = in_array($roleNameClean, ['super admin', 'administrator', 'it / technical support']);

        $builder = $this->db->table('system_notifications sn')
                            ->join('system_notification_reads snr', 'snr.notification_id = sn.id AND snr.user_id = ' . (int)$userId, 'left')
                            ->where('snr.id IS NULL');

        if (!$isSuperAdmin) {
            $builder->groupStart()
                    ->where('sn.target_user_id', $userId)
                    ->orWhere('sn.target_roles IS NULL')
                    ->orWhere('sn.target_roles', '')
                    ->orLike('sn.target_roles', $roleNameClean)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Tandai Notifikasi Telah Dibaca oleh Pengguna
     */
    public function markAsRead($notificationId, $userId)
    {
        $exQ = $this->db->table('system_notification_reads')
                             ->where('notification_id', $notificationId)
                             ->where('user_id', $userId)
                             ->get();
        $existing = ($exQ && is_object($exQ)) ? $exQ->getRow() : null;
        if (!$existing) {
            $this->db->table('system_notification_reads')->insert([
                'notification_id' => $notificationId,
                'user_id'         => $userId,
                'read_at'         => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }

    /**
     * Tandai Seluruh Notifikasi Telah Dibaca oleh Pengguna
     */
    public function markAllAsRead($userId, $roleName)
    {
        $notifications = $this->getNotificationsForUser($userId, $roleName, [], 200, null, true);
        foreach ($notifications as $n) {
            $this->markAsRead($n->id, $userId);
        }
        return true;
    }

    /**
     * Ambil Audit Jejak Siapa Saja yang Telah Membaca Notifikasi Ini
     */
    public function getNotificationReadReceipts($notificationId)
    {
        return $this->db->table('system_notification_reads snr')
                        ->select('snr.read_at, COALESCE(u.fullname, u.username) as user_name, u.username, r.name as role_name')
                        ->join('users u', 'u.id = snr.user_id')
                        ->join('roles r', 'r.id = u.role_id', 'left')
                        ->where('snr.notification_id', $notificationId)
                        ->orderBy('snr.read_at', 'DESC')
                        ->get()
                        ->getResult();
    }
}
