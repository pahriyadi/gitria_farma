<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class LiveSync extends BaseController
{
    use ResponseTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');
    }

    /**
     * 1. Live Sync: Antrean Pasien Rawat Jalan & Poli Medis (Zero-Reload)
     */
    public function klinikQueue()
    {
        $today = date('Y-m-d');
        $doctorId = $this->request->getGet('doctor_id') ?? 'all';
        $polyId   = $this->request->getGet('poly_id') ?? 'all';

        $builder = $this->db->table('patient_visits pv')
                            ->select('pv.id, pv.id as visit_id, pv.patient_id, pv.no_visit, pv.visit_type, pv.visit_date, pv.created_at,
                                      COALESCE(qn.updated_at, pv.updated_at, qn.created_at, pv.created_at) as call_time,
                                      COALESCE(qn.id, pv.id) as queue_id,
                                      COALESCE(qn.queue_no, pv.no_visit) as queue_number,
                                      COALESCE(qn.status, pv.status) as status,
                                      pv.status as visit_status,
                                      qn.status as queue_status,
                                      COALESCE(qn.polyclinic_id, pv.polyclinic_id) as polyclinic_id,
                                      COALESCE(polikliniks.name, poly.name) as polyclinic_name,
                                      COALESCE(polikliniks.name, poly.name) as poly_name,
                                      tindakan.name as tindakan_name,
                                      COALESCE(tindakan.name, s.name) as service_name,
                                      parent_tind.name as parent_tindakan_name,
                                      parent_tind.name as parent_service_name,
                                      p.name as patient_name, p.no_rm, p.nik, p.gender,
                                      COALESCE(p.membership_tier, \'regular\') as membership_tier,
                                      pv.doctor_id,
                                      d.name as doctor_name,
                                      (CASE WHEN tr.id IS NOT NULL THEN 1 ELSE 0 END) as has_triage,
                                      tr.blood_pressure, tr.pulse, tr.temperature, tr.respiration, tr.weight, tr.height, tr.complaints, tr.anamnesis, tr.nurse_notes')
                            ->join('patients p', 'p.id = pv.patient_id', 'left')
                            ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                            ->join('polyclinics poly', 'poly.id = pv.polyclinic_id', 'left')
                            ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                            ->join('tindakan', 'tindakan.id = pv.service_id', 'left')
                            ->join('tindakan parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                            ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                            ->join('services s', 's.id = pv.service_id', 'left')
                            ->join('triage_records tr', 'tr.visit_id = pv.id', 'left')
                            ->where('pv.visit_date', $today);

        $tab = $this->request->getGet('tab') ?: 'active';
        if ($tab === 'active') {
            $builder->whereNotIn('pv.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed'])
                    ->whereNotIn('COALESCE(qn.status, pv.status)', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed']);
        } elseif ($tab === 'completed') {
            $builder->groupStart()
                    ->whereIn('pv.status', ['completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed'])
                    ->orWhere('qn.status', 'completed')
                    ->groupEnd();
        }

        if (!empty($polyId) && $polyId !== 'all') {
            $builder->where('pv.polyclinic_id', $polyId);
        }
        if (!empty($doctorId) && $doctorId !== 'all') {
            $builder->where('pv.doctor_id', $doctorId);
        }

        $queues = $builder->orderBy("CASE WHEN COALESCE(qn.status, pv.status) = 'called' OR COALESCE(qn.status, pv.status) = 'triage' THEN 1 WHEN COALESCE(qn.status, pv.status) = 'waiting' THEN 2 ELSE 3 END", '', false)
                          ->orderBy("pv.created_at ASC, pv.id ASC", '', false)
                          ->get()
                          ->getResult();

        // Hitung nomor urut pendaftaran global hari ini (Arrival Order Sequence #1, #2, #3...)
        $allVisitsOrder = $this->db->table('patient_visits')
                                   ->select('id')
                                   ->where('visit_date', $today)
                                   ->orderBy('created_at', 'ASC')
                                   ->orderBy('id', 'ASC')
                                   ->get()
                                   ->getResult();
        $arrivalOrderMap = [];
        $seq = 1;
        foreach ($allVisitsOrder as $avo) {
            $arrivalOrderMap[$avo->id] = $seq++;
        }

        $now = time();
        foreach ($queues as &$q) {
            $vId = $q->visit_id ?? $q->id;
            $q->arrival_seq = $arrivalOrderMap[$vId] ?? 1;
            $regTime = !empty($q->created_at) ? strtotime($q->created_at) : $now;
            $waitMins = max(0, (int) round(($now - $regTime) / 60));
            $q->wait_minutes = $waitMins;

            if ($waitMins < 15) {
                $q->sla_status = 'normal';
                $q->sla_badge  = 'success';
                $q->sla_label  = 'Tepat Waktu (< 15 mnt)';
            } elseif ($waitMins <= 30) {
                $q->sla_status = 'warning';
                $q->sla_badge  = 'warning text-dark';
                $q->sla_label  = 'Perhatian (15-30 mnt)';
            } else {
                $q->sla_status = 'overdue';
                $q->sla_badge  = 'danger';
                $q->sla_label  = 'Prioritas / Terlambat (> 30 mnt)';
            }
        }

        // Hitung statistik hari ini
        $allStatsRows = $this->db->table('patient_visits pv')
                                 ->select('pv.status as pv_status, qn.status as qn_status')
                                 ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                                 ->where('pv.visit_date', $today)
                                 ->get()
                                 ->getResult();

        $stats = [
            'total'        => count($allStatsRows),
            'waiting'      => 0,
            'called'       => 0,
            'triage'       => 0,
            'examining'    => 0,
            'prescription' => 0,
            'cashier'      => 0,
            'completed'    => 0,
            'cancelled'    => 0,
            'active'       => 0
        ];

        foreach ($allStatsRows as $r) {
            $st = strtolower($r->qn_status ?: ($r->pv_status ?: 'waiting'));
            if (isset($stats[$st])) {
                $stats[$st]++;
            }
            if ($st !== 'completed' && $st !== 'cancelled') {
                $stats['active']++;
            }
        }

        return $this->respond([
            'status'    => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'stats'     => $stats,
            'data'      => $queues,
        ]);
    }

    /**
     * 2. Live Sync: Antrean e-Resep Masuk ke Farmasi / Apotek
     */
    public function pharmacyPrescriptions()
    {
        $status = $this->request->getGet('status');

        $builder = $this->db->table('prescriptions pr')
                            ->select('pr.*, 
                                      pv.no_visit,
                                      COALESCE(qn.queue_no, pv.no_visit) as queue_number,
                                      p.name as patient_name, p.no_rm,
                                      COALESCE(d.name, u.username, "Dokter Pemeriksa") as doctor_name,
                                      COALESCE(bt.status, "open") as billing_status,
                                      (CASE WHEN bt.status = "paid" THEN 1 ELSE 0 END) as is_paid,
                                      bt.payment_method,
                                      bt.grand_total as billing_grand_total')
                            ->join('patient_visits pv', 'pv.id = pr.visit_id', 'left')
                            ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                            ->join('patients p', 'p.id = pv.patient_id', 'left')
                            ->join('doctors d', 'd.id = pr.doctor_id', 'left')
                            ->join('users u', 'u.id = pr.doctor_id', 'left')
                            ->join('billing_transactions bt', 'bt.visit_id = pr.visit_id', 'left')
                            ->orderBy('pr.id', 'DESC');

        if (!empty($status)) {
            $builder->where('pr.status', $status);
        } else {
            $builder->where('pr.status', 'waiting');
        }

        $prescriptions = $builder->get()->getResult();

        // Attach prescription details and available batches
        foreach ($prescriptions as $item) {
            $item->items = $this->db->table('prescription_details pd')
                                    ->select('pd.*, m.name as medicine_name, m.unit, m.price')
                                    ->join('medicines m', 'm.id = pd.medicine_id', 'left')
                                    ->where('pd.prescription_id', $item->id)
                                    ->get()
                                    ->getResult();

            foreach ($item->items as &$medItem) {
                $medItem->batches = $this->db->table('medicine_batches')
                                             ->where('medicine_id', $medItem->medicine_id)
                                             ->where('stock >', 0)
                                             ->orderBy('expired_date', 'ASC')
                                             ->get()
                                             ->getResult();
            }
        }

        $counts = [
            'total'      => count($prescriptions),
            'waiting'    => 0,
            'completed'  => 0,
        ];

        foreach ($prescriptions as $p) {
            $st = strtolower($p->status ?? 'waiting');
            if (isset($counts[$st])) {
                $counts[$st]++;
            }
        }

        return $this->respond([
            'status'    => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'counts'    => $counts,
            'data'      => $prescriptions,
        ]);
    }

    /**
     * 3. Live Sync: Antrean Kasir & Billing Siap Bayar
     */
    public function cashierBillings()
    {
        $today = date('Y-m-d');

        // Unpaid / active billings from billing_transactions (status: draft, open, partial)
        $unpaid = $this->db->table('billing_transactions bt')
                           ->select('bt.*, 
                                     pv.no_visit,
                                     COALESCE(qn.queue_no, pv.no_visit) as queue_number,
                                     p.name as patient_name, p.no_rm,
                                     d.name as doctor_name,
                                     COALESCE(polikliniks.name, poly.name) as poly_name')
                           ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                           ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                           ->join('patients p', 'p.id = pv.patient_id', 'left')
                           ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                           ->join('polyclinics poly', 'poly.id = pv.polyclinic_id', 'left')
                           ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                           ->whereIn('bt.status', ['draft', 'open', 'partial'])
                           ->where('bt.status !=', 'paid')
                           ->where('bt.status !=', 'cancelled')
                           ->orderBy('bt.id', 'DESC')
                           ->get()
                           ->getResult();

        foreach ($unpaid as $bill) {
            $bill->total_formatted = number_format($bill->grand_total ?? 0, 0, ',', '.');
            $bill->items = $this->db->table('billing_details bd')
                                    ->where('bd.billing_id', $bill->id)
                                    ->get()
                                    ->getResult();
        }

        // Today's completed transactions
        $completed = $this->db->table('cash_transactions ct')
                              ->select('ct.id, ct.receipt_no, ct.amount, ct.payment_method, ct.created_at,
                                        p.name as patient_name, p.no_rm,
                                        cr.name as register_name')
                              ->join('billing_transactions bt', 'bt.id = ct.billing_id', 'left')
                              ->join('cash_registers cr', 'cr.id = ct.cash_register_id', 'left')
                              ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                              ->join('patients p', 'p.id = pv.patient_id', 'left')
                              ->where('DATE(ct.created_at)', $today)
                              ->orderBy('ct.id', 'DESC')
                              ->limit(20)
                              ->get()
                              ->getResult();

        return $this->respond([
            'status'          => 'success',
            'timestamp'       => date('Y-m-d H:i:s'),
            'unpaid_count'    => count($unpaid),
            'unpaid_data'     => $unpaid,
            'completed_count' => count($completed),
            'completed_data'  => $completed,
        ]);
    }

    /**
     * 4. Live Sync: Pesanan Makanan Resto Gizi / Kitchen Display System (KDS)
     */
    public function restoKitchen()
    {
        $today = date('Y-m-d');

        $orders = $this->db->table('restaurant_orders ro')
                           ->select('ro.*, u.username as cashier_name')
                           ->join('users u', 'u.id = ro.cashier_id', 'left')
                           ->where('DATE(ro.created_at)', $today)
                           ->whereIn('ro.status', ['pending', 'cooking', 'ready'])
                           ->orderBy('ro.id', 'ASC')
                           ->get()
                           ->getResult();

        foreach ($orders as $ord) {
            $ord->items = $this->db->table('restaurant_order_items roi')
                                   ->select('roi.*, mi.name as menu_name, mi.category')
                                   ->join('restaurant_menu_items mi', 'mi.id = roi.menu_item_id', 'left')
                                   ->where('roi.order_id', $ord->id)
                                   ->get()
                                   ->getResult();
            $ord->total_formatted = number_format($ord->total_amount ?? 0, 0, ',', '.');
        }

        return $this->respond([
            'status'    => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'count'     => count($orders),
            'data'      => $orders,
        ]);
    }

    /**
     * 5. Live Sync: Presensi & Absensi Pegawai Hari Ini
     */
    public function hrdPresence()
    {
        $today = date('Y-m-d');

        $attendances = $this->db->table('employee_attendances ea')
                                ->select('ea.*, e.name as employee_name, e.nip, e.position, e.department')
                                ->join('employees e', 'e.id = ea.employee_id', 'left')
                                ->where('ea.date', $today)
                                ->orderBy('ea.check_in_time', 'DESC')
                                ->get()
                                ->getResult();

        $totalEmployees = $this->db->table('employees')->where('status', 'active')->countAllResults();

        return $this->respond([
            'status'          => 'success',
            'timestamp'       => date('Y-m-d H:i:s'),
            'total_employees' => $totalEmployees,
            'present_count'   => count($attendances),
            'data'            => $attendances,
        ]);
    }

    /**
     * 6. Live Sync: Permintaan & Hasil Pemeriksaan Laboratorium Pasien
     */
    public function labQueue()
    {
        $today = date('Y-m-d');

        $labResults = $this->db->table('lab_results lr')
                               ->select('lr.*, 
                                         p.name as patient_name, p.no_rm, p.gender, p.date_of_birth,
                                         d.name as doctor_name')
                               ->join('patients p', 'p.id = lr.patient_id', 'left')
                               ->join('doctors d', 'd.id = lr.doctor_id', 'left')
                               ->where('lr.test_date', $today)
                               ->orderBy('lr.id', 'DESC')
                               ->get()
                               ->getResult();

        $counts = [
            'total'    => count($labResults),
            'normal'   => 0,
            'abnormal' => 0,
        ];

        foreach ($labResults as $lr) {
            if ($lr->status === 'normal') {
                $counts['normal']++;
            } else {
                $counts['abnormal']++;
            }
        }

        return $this->respond([
            'status'    => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'counts'    => $counts,
            'data'      => $labResults,
        ]);
    }

    /**
     * 7. Heartbeat Ping: Untuk mendeteksi koneksi online/offline & kestabilan server
     */
    public function ping()
    {
        return $this->respond([
            'status'      => 'online',
            'timestamp'   => time(),
            'datetime'    => date('Y-m-d H:i:s'),
            'server_time' => date('Y-m-d H:i:s'),
            'csrf_name'   => csrf_token(),
            'csrf_hash'   => csrf_hash(),
            'app'         => 'Sawamawa Medical Center ERP'
        ]);
    }

    /**
     * 8. Fresh CSRF Token Generator: Untuk pengiriman antrean draf offline
     */
    public function csrfToken()
    {
        return $this->respond([
            'status'    => 'success',
            'csrf_name' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }

    /**
     * 9. Live Sync: Antrean Panggilan Suara Terpusat (Voice Queue Dispatcher)
     * Mengambil event panggilan antrean berurutan prioritas untuk diputar di TV Display & Kiosk
     */
    public function voiceQueue()
    {
        $lastId = (int)$this->request->getGet('last_id');
        $limit  = (int)($this->request->getGet('limit') ?: 10);

        $queueService = new \App\Services\QueueCallService();
        $pendingCalls = $queueService->getPendingCalls($limit, $lastId);

        return $this->respond([
            'status'       => 'success',
            'timestamp'    => date('Y-m-d H:i:s'),
            'total_pending'=> count($pendingCalls),
            'data'         => $pendingCalls,
        ]);
    }

    /**
     * 10. Voice Call Trigger API: Mendaftarkan event panggilan baru dari loket/poli/apotek/kasir
     */
    public function voiceCallTrigger()
    {
        $rawInput = [];
        try {
            $contentType = $this->request->getHeaderLine('Content-Type');
            if (stripos($contentType, 'application/json') !== false) {
                $rawInput = (array)$this->request->getJSON(true);
            }
        } catch (\Throwable $e) {
            $rawInput = [];
        }

        if (empty($rawInput)) {
            $rawInput = $this->request->getPost();
        }
        if (empty($rawInput)) {
            $rawInput = $this->request->getVar();
        }
        if (empty($rawInput)) {
            $body = $this->request->getBody();
            if (!empty($body)) {
                $decoded = json_decode($body, true);
                if (is_array($decoded)) {
                    $rawInput = $decoded;
                }
            }
        }

        if (empty($rawInput['queue_number'])) {
            return $this->fail('Nomor antrean (queue_number) wajib diisi.', 400);
        }

        $queueService = new \App\Services\QueueCallService();
        $res = $queueService->triggerCall((array)$rawInput);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Event panggilan suara berhasil didaftarkan ke antrean terpusat.',
            'data'    => $res,
        ]);
    }

    /**
     * 11. Voice Call Acknowledgment: Dikirim oleh Display TV setelah audio selesai diputar
     */
    public function voiceCallAck()
    {
        $rawInput = [];
        try {
            $contentType = $this->request->getHeaderLine('Content-Type');
            if (stripos($contentType, 'application/json') !== false) {
                $rawInput = (array)$this->request->getJSON(true);
            }
        } catch (\Throwable $e) {
            $rawInput = [];
        }

        if (empty($rawInput)) {
            $rawInput = $this->request->getPost();
        }
        if (empty($rawInput)) {
            $rawInput = $this->request->getVar();
        }

        $eventId = (int)($rawInput['event_id'] ?? $this->request->getGet('event_id'));
        $action  = strtolower($rawInput['action'] ?? 'played');

        if (!$eventId) {
            return $this->fail('ID Event (event_id) wajib disertakan.', 400);
        }

        $queueService = new \App\Services\QueueCallService();

        if ($action === 'skipped' || $action === 'cancel') {
            $queueService->markAsSkipped($eventId);
        } else {
            $queueService->markAsPlayed($eventId);
        }

        return $this->respond([
            'status'   => 'success',
            'message'  => 'Status pemanggilan suara berhasil diperbarui.',
            'event_id' => $eventId,
            'action'   => $action,
        ]);
    }

    /**
     * 12. Voice Call History: Riwayat panggilan hari ini untuk log audit
     */
    public function voiceCallHistory()
    {
        $limit = (int)($this->request->getGet('limit') ?: 25);
        $queueService = new \App\Services\QueueCallService();
        $history = $queueService->getRecentCallHistory($limit);

        return $this->respond([
            'status'    => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'total'     => count($history),
            'data'      => $history,
        ]);
    }
}

