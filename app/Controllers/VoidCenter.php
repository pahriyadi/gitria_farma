<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\VoidReversalEngine;

class VoidCenter extends BaseController
{
    protected $voidEngine;

    public function __construct()
    {
        $this->voidEngine = new VoidReversalEngine();
    }

    /**
     * Dashboard & Monitoring Pusat Log Pembatalan (Anti-Fraud Center)
     * URL: GET accounting/void-logs
     */
    public function index()
    {
        $db = \Config\Database::connect('default');

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');
        $module    = $this->request->getGet('module') ?: '';
        $supervisorId = $this->request->getGet('supervisor_id') ?: '';

        // Query Logs with joins
        $builder = $db->table('void_audit_logs')
                      ->select('void_audit_logs.*, 
                               cashier.username as cashier_username, cashier.fullname as cashier_name,
                               supervisor.username as supervisor_username, supervisor.fullname as supervisor_name,
                               supervisor_roles.name as supervisor_role_name,
                               journal_entries.journal_no as reversal_journal_no')
                      ->join('users as cashier', 'cashier.id = void_audit_logs.cashier_user_id', 'left')
                      ->join('users as supervisor', 'supervisor.id = void_audit_logs.supervisor_user_id', 'left')
                      ->join('roles as supervisor_roles', 'supervisor_roles.id = supervisor.role_id', 'left')
                      ->join('journal_entries', 'journal_entries.id = void_audit_logs.reversal_journal_id', 'left')
                      ->where('DATE(void_audit_logs.created_at) >=', $startDate)
                      ->where('DATE(void_audit_logs.created_at) <=', $endDate)
                      ->orderBy('void_audit_logs.id', 'DESC');

        if (!empty($module)) {
            $builder->where('void_audit_logs.transaction_type', $module);
        }
        if (!empty($supervisorId)) {
            $builder->where('void_audit_logs.supervisor_user_id', $supervisorId);
        }

        $logs = $builder->get()->getResult();

        // 1. KPI Today Stats
        $today = date('Y-m-d');
        $todayStats = $db->table('void_audit_logs')
                         ->select('COUNT(*) as total_count, COALESCE(SUM(total_amount), 0) as total_amount')
                         ->where('DATE(created_at)', $today)
                         ->get()
                         ->getRow();

        // 2. KPI Month Stats
        $monthStart = date('Y-m-01');
        $monthStats = $db->table('void_audit_logs')
                         ->select('COUNT(*) as total_count, COALESCE(SUM(total_amount), 0) as total_amount')
                         ->where('DATE(created_at) >=', $monthStart)
                         ->get()
                         ->getRow();

        // 3. Modul Paling Sering Di-void Bulan Ini
        $topModule = $db->table('void_audit_logs')
                        ->select('transaction_type, COUNT(*) as count, SUM(total_amount) as total_amount')
                        ->where('DATE(created_at) >=', $monthStart)
                        ->groupBy('transaction_type')
                        ->orderBy('count', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();

        // 4. Daftar Supervisor yang Berwenang
        $supervisors = $db->table('users')
                          ->select('users.id, users.fullname as name, users.username, roles.name as role_name')
                          ->join('roles', 'roles.id = users.role_id')
                          ->whereIn('roles.name', ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan', 'Manajer'])
                          ->where('users.status', 'active')
                          ->orderBy('users.fullname', 'ASC')
                          ->get()
                          ->getResult();

        // 5. Master Alasan Void
        $reasons = $db->table('void_reasons_master')
                      ->where('is_active', 1)
                      ->orderBy('category_name', 'ASC')
                      ->get()
                      ->getResult();

        $data = [
            'title'        => 'Pusat Log Pembatalan / Void Transaksi (Anti-Fraud Center)',
            'active_menu'  => 'accounting-void-logs',
            'logs'         => $logs,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'selectedModule' => $module,
            'selectedSupervisor' => $supervisorId,
            'supervisors'  => $supervisors,
            'reasons'      => $reasons,
            'kpi'          => [
                'todayCount'     => (int)($todayStats->total_count ?? 0),
                'todayAmount'    => (float)($todayStats->total_amount ?? 0),
                'monthCount'     => (int)($monthStats->total_count ?? 0),
                'monthAmount'    => (float)($monthStats->total_amount ?? 0),
                'topModuleName'  => $topModule ? ucfirst(str_replace('_', ' ', $topModule->transaction_type)) : 'Belum Ada',
                'topModuleCount' => (int)($topModule->count ?? 0)
            ]
        ];

        return view('accounting/void_logs', $data);
    }

    /**
     * API Detail Log Void (JSON Snapshot)
     */
    public function detailJson($id)
    {
        $db = \Config\Database::connect('default');

        $log = $db->table('void_audit_logs')
                  ->select('void_audit_logs.*, 
                           cashier.fullname as cashier_name, cashier.username as cashier_username,
                           supervisor.fullname as supervisor_name, supervisor.username as supervisor_username,
                           supervisor_roles.name as supervisor_role_name,
                           journal_entries.journal_no as reversal_journal_no, journal_entries.description as reversal_journal_desc')
                  ->join('users as cashier', 'cashier.id = void_audit_logs.cashier_user_id', 'left')
                  ->join('users as supervisor', 'supervisor.id = void_audit_logs.supervisor_user_id', 'left')
                  ->join('roles as supervisor_roles', 'supervisor_roles.id = supervisor.role_id', 'left')
                  ->join('journal_entries', 'journal_entries.id = void_audit_logs.reversal_journal_id', 'left')
                  ->where('void_audit_logs.id', $id)
                  ->get()
                  ->getRow();

        if (!$log) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Log void tidak ditemukan.']);
        }

        $log->snapshot_decoded = json_decode($log->item_snapshot_json ?? '{}', true);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $log
        ]);
    }

    /**
     * API AJAX Verifikasi PIN Supervisor
     */
    public function apiVerifyPin()
    {
        $supervisorUserId = $this->request->getPost('supervisor_user_id');
        $pin = $this->request->getPost('pin');

        $result = $this->voidEngine->verifySupervisorPin($supervisorUserId, $pin);

        if (!$result['valid']) {
            return $this->response->setStatusCode(400)->setJSON($result);
        }

        return $this->response->setJSON($result);
    }

    /**
     * API AJAX Eksekusi Void Transaksi
     */
    public function apiExecuteVoid()
    {
        $trxType          = trim((string)$this->request->getPost('transaction_type'));
        $referenceId      = (int)$this->request->getPost('reference_id');
        $reasonCategory   = trim((string)$this->request->getPost('reason_category'));
        $reasonDetail     = trim((string)$this->request->getPost('reason_detail'));
        $supervisorUserId = (int)$this->request->getPost('supervisor_user_id');
        $supervisorPin    = trim((string)$this->request->getPost('supervisor_pin'));

        if (empty($trxType) || $referenceId <= 0 || empty($reasonCategory) || empty($reasonDetail) || $supervisorUserId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Semua kolom formulir (Tipe Transaksi, ID, Kategori Alasan, Rincian Alasan, dan Supervisor) wajib diisi lengkap.'
            ]);
        }

        // 1. Verifikasi PIN terlebih dahulu
        $pinVerify = $this->voidEngine->verifySupervisorPin($supervisorUserId, $supervisorPin);
        if (!$pinVerify['valid']) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $pinVerify['message']
            ]);
        }

        // 2. Eksekusi Pembatalan Terpadu
        $cashierUserId = session('user_id') ?: 1;
        $ipAddress = $this->request->getIPAddress();
        $userAgent = substr((string)$this->request->getUserAgent(), 0, 255);

        $result = $this->voidEngine->executeVoid(
            $trxType,
            $referenceId,
            $reasonCategory,
            $reasonDetail,
            $supervisorUserId,
            $cashierUserId,
            $ipAddress,
            $userAgent
        );

        if ($result['status'] !== 'success') {
            return $this->response->setStatusCode(422)->setJSON($result);
        }

        return $this->response->setJSON($result);
    }

    /**
     * API Get Master Alasan Void
     */
    public function apiGetReasons()
    {
        $db = \Config\Database::connect('default');
        $module = $this->request->getGet('module') ?: 'all';

        $builder = $db->table('void_reasons_master')->where('is_active', 1);
        if ($module !== 'all') {
            $builder->groupStart()
                    ->where('module', 'all')
                    ->orWhere('module', $module)
                    ->groupEnd();
        }

        $reasons = $builder->orderBy('category_name', 'ASC')->get()->getResult();

        $supervisors = $db->table('users')
                          ->select('users.id, users.fullname as name, users.username, roles.name as role_name')
                          ->join('roles', 'roles.id = users.role_id')
                          ->whereIn('roles.name', ['Super Admin', 'IT', 'Direksi', 'Kepala Klinik', 'Koordinator Keuangan', 'Manajer'])
                          ->where('users.status', 'active')
                          ->orderBy('users.fullname', 'ASC')
                          ->get()
                          ->getResult();

        return $this->response->setJSON([
            'status'      => 'success',
            'reasons'     => $reasons,
            'supervisors' => $supervisors
        ]);
    }

    /**
     * Simpan / Ubah PIN Supervisor
     */
    public function saveSupervisorPin()
    {
        $db = \Config\Database::connect('default');

        $userId = (int)$this->request->getPost('user_id');
        $newPin = trim((string)$this->request->getPost('new_pin'));
        $confirmPin = trim((string)$this->request->getPost('confirm_pin'));

        if ($userId <= 0 || strlen($newPin) < 6) {
            session()->setFlashdata('error', 'PIN harus berupa minimal 6 karakter/angka.');
            return redirect()->back();
        }

        if ($newPin !== $confirmPin) {
            session()->setFlashdata('error', 'Konfirmasi PIN baru tidak sesuai.');
            return redirect()->back();
        }

        $pinHash = password_hash($newPin, PASSWORD_BCRYPT);
        $exist = $db->table('supervisor_pins')->where('user_id', $userId)->get()->getRow();

        if ($exist) {
            $db->table('supervisor_pins')->where('id', $exist->id)->update([
                'pin_hash'        => $pinHash,
                'failed_attempts' => 0,
                'is_locked'       => 0,
                'locked_until'    => null,
                'updated_at'      => date('Y-m-d H:i:s')
            ]);
        } else {
            $db->table('supervisor_pins')->insert([
                'user_id'         => $userId,
                'pin_hash'        => $pinHash,
                'failed_attempts' => 0,
                'is_locked'       => 0
            ]);
        }

        session()->setFlashdata('success', 'PIN Otorisasi Supervisor berhasil diperbarui dengan aman!');
        return redirect()->back();
    }

    /**
     * Cetak Berita Acara Pembatalan Transaksi Resmi (PDF Print View)
     */
    public function cetakBeritaAcara($id)
    {
        $db = \Config\Database::connect('default');

        $log = $db->table('void_audit_logs')
                  ->select('void_audit_logs.*, 
                           cashier.fullname as cashier_name, cashier.username as cashier_username,
                           supervisor.fullname as supervisor_name, supervisor.username as supervisor_username,
                           supervisor_roles.name as supervisor_role_name,
                           journal_entries.journal_no as reversal_journal_no')
                  ->join('users as cashier', 'cashier.id = void_audit_logs.cashier_user_id', 'left')
                  ->join('users as supervisor', 'supervisor.id = void_audit_logs.supervisor_user_id', 'left')
                  ->join('roles as supervisor_roles', 'supervisor_roles.id = supervisor.role_id', 'left')
                  ->join('journal_entries', 'journal_entries.id = void_audit_logs.reversal_journal_id', 'left')
                  ->where('void_audit_logs.id', $id)
                  ->get()
                  ->getRow();

        if (!$log) {
            session()->setFlashdata('error', 'Data Berita Acara Void tidak ditemukan.');
            return redirect()->to(base_url('accounting/void-logs'));
        }

        $log->snapshot_decoded = json_decode($log->item_snapshot_json ?? '{}', true);

        return view('accounting/cetak_berita_acara_void', ['log' => $log]);
    }
}
