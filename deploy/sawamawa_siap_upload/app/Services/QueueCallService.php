<?php

namespace App\Services;

use Config\Database;

class QueueCallService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Mendaftarkan event panggilan suara baru ke antrean pusat.
     */
    public function triggerCall(array $data): array
    {
        $now = date('Y-m-d H:i:s');

        $serviceType   = strtolower($data['service_type'] ?? 'poliklinik');
        $serviceId     = !empty($data['service_id']) ? (int)$data['service_id'] : null;
        $counterName   = trim($data['counter_name'] ?? 'Poliklinik');
        $queueNumber   = trim($data['queue_number'] ?? 'A-001');
        $patientName   = trim($data['patient_name'] ?? 'Pasien');
        $visitId       = !empty($data['visit_id']) ? (int)$data['visit_id'] : null;
        $callAction    = strtolower($data['call_action'] ?? 'call');
        $priority      = isset($data['call_priority']) ? (int)$data['call_priority'] : 1;
        $callerUserId  = !empty($data['caller_user_id']) ? (int)$data['caller_user_id'] : (session('user_id') ? (int)session('user_id') : null);
        $callerName    = $data['caller_name'] ?? session('username') ?? 'Petugas';

        // Susun teks narasi panggilan suara terstandar medis Bahasa Indonesia
        $voiceText = $data['voice_text'] ?? $this->buildVoiceText($serviceType, $counterName, $queueNumber, $patientName, $callAction);

        $payload = [
            'service_type'   => $serviceType,
            'service_id'     => $serviceId,
            'counter_name'   => $counterName,
            'queue_number'   => $queueNumber,
            'patient_name'   => $patientName,
            'visit_id'       => $visitId,
            'call_action'    => $callAction,
            'call_priority'  => $priority,
            'voice_text'     => $voiceText,
            'status'         => 'pending',
            'caller_user_id' => $callerUserId,
            'caller_name'    => $callerName,
            'created_at'     => $now,
            'updated_at'     => $now,
        ];

        $this->db->table('queue_call_events')->insert($payload);
        $insertId = $this->db->insertID();

        // Update status di queue_numbers & patient_visits jika visitId diberikan
        if ($visitId) {
            $this->syncVisitQueueStatus($visitId, $queueNumber, $callAction, $serviceType, $now);
        }

        return [
            'status'       => 'success',
            'event_id'     => $insertId,
            'queue_number' => $queueNumber,
            'patient_name' => $patientName,
            'counter_name' => $counterName,
            'voice_text'   => $voiceText,
            'created_at'   => $now,
        ];
    }

    /**
     * Membangun narasi suara Bahasa Indonesia terstandar.
     */
    public function buildVoiceText(string $serviceType, string $counterName, string $queueNumber, string $patientName, string $callAction): string
    {
        $queueText = $this->formatQueueSpoken($queueNumber);

        if ($callAction === 'completed') {
            return "Nomor antrean {$queueText}, atas nama {$patientName}, penyerahan obat telah selesai. Terima kasih banyak atas kunjungan Anda di Sawamawa Medical Center, semoga lekas sembuh.";
        }

        switch ($serviceType) {
            case 'pendaftaran':
                return "Nomor antrean {$queueText}, atas nama {$patientName}, silakan menuju ke {$counterName}. Terima kasih.";

            case 'farmasi':
            case 'apotek':
                return "Nomor antrean {$queueText}, atas nama {$patientName}, transaksi pembayaran telah selesai. Terima kasih. Silakan menuju ke Loket Farmasi dan Apotek untuk pengambilan obat.";

            case 'kasir':
                return "Nomor antrean {$queueText}, atas nama {$patientName}, pemeriksaan dokter telah selesai. Silakan menuju ke {$counterName} untuk administrasi. Terima kasih.";

            case 'tindakan':
            case 'laboratorium':
            case 'lab':
                return "Nomor antrean {$queueText}, atas nama {$patientName}, silakan menuju ke Ruang {$counterName}. Terima kasih.";

            case 'triage':
            case 'ttv':
                return "Nomor antrean {$queueText}, atas nama {$patientName}, silakan menuju ke Ruang Pemeriksaan Tanda Vital Perawat. Terima kasih.";

            case 'poliklinik':
            default:
                return "Nomor antrean {$queueText}, atas nama {$patientName}, silakan masuk ke Ruang {$counterName}. Terima kasih.";
        }
    }

    /**
     * Mengonversi format A-001 menjadi narasi terucap: "A, satu".
     */
    public function formatQueueSpoken(string $queueNo): string
    {
        $parts = explode('-', $queueNo);
        $prefix = strtoupper(trim($parts[0] ?? 'A'));
        $num = isset($parts[1]) ? (int)$parts[1] : 1;
        return "{$prefix}, " . $this->terbilang($num);
    }

    /**
     * Mengambil daftar panggilan pending berurutan prioritas.
     */
    public function getPendingCalls(int $limit = 10, int $lastId = 0): array
    {
        $since = date('Y-m-d H:i:s', strtotime('-15 minutes'));

        $builder = $this->db->table('queue_call_events')
                            ->where('status', 'pending')
                            ->where('created_at >=', $since);

        if ($lastId > 0) {
            $builder->where('id >', $lastId);
        }

        $events = $builder->orderBy('call_priority', 'ASC')
                          ->orderBy('created_at', 'ASC')
                          ->orderBy('id', 'ASC')
                          ->limit($limit)
                          ->get()
                          ->getResultArray();

        return $events;
    }

    /**
     * Tandai event sedang disiarkan.
     */
    public function markAsBroadcasting(int $eventId): bool
    {
        return $this->db->table('queue_call_events')
                        ->where('id', $eventId)
                        ->update([
                            'status'     => 'broadcasting',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
    }

    /**
     * Tandai event telah selesai diputar (ACK dari Display TV / Client).
     */
    public function markAsPlayed(int $eventId): bool
    {
        $now = date('Y-m-d H:i:s');
        return $this->db->table('queue_call_events')
                        ->where('id', $eventId)
                        ->update([
                            'status'     => 'played',
                            'played_at'  => $now,
                            'updated_at' => $now
                        ]);
    }

    /**
     * Tandai event dilewati atau dibatalkan.
     */
    public function markAsSkipped(int $eventId): bool
    {
        return $this->db->table('queue_call_events')
                        ->where('id', $eventId)
                        ->update([
                            'status'     => 'skipped',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
    }

    /**
     * Riwayat panggilan terakhir (untuk audit dan display log).
     */
    public function getRecentCallHistory(int $limit = 20): array
    {
        $today = date('Y-m-d 00:00:00');
        return $this->db->table('queue_call_events')
                        ->where('created_at >=', $today)
                        ->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->getResultArray();
    }

    /**
     * Sinkronisasi status antrean dan kunjungan di database.
     */
    protected function syncVisitQueueStatus(int $visitId, string $queueNumber, string $callAction, string $serviceType, string $now): void
    {
        if ($callAction === 'completed' || $serviceType === 'completed') {
            $visitStatus = 'completed';
            $queueStatus = 'completed';
        } elseif ($serviceType === 'kasir') {
            $visitStatus = 'cashier';
            $queueStatus = 'completed';
        } elseif ($serviceType === 'farmasi' || $serviceType === 'apotek') {
            $visitStatus = 'prescription';
            $queueStatus = 'completed';
        } elseif ($callAction === 'called' || $callAction === 'triage') {
            $visitStatus = 'called';
            $queueStatus = 'called';
        } elseif ($callAction === 'examining') {
            $visitStatus = 'examining';
            $queueStatus = 'examining';
        } elseif ($callAction === 'waiting') {
            $visitStatus = 'waiting';
            $queueStatus = 'waiting';
        } else {
            $visitStatus = $callAction;
            $queueStatus = $callAction;
        }

        $this->db->table('patient_visits')
                 ->where('id', $visitId)
                 ->update([
                     'status'     => $visitStatus,
                     'updated_at' => $now
                 ]);

        $qn = $this->db->table('queue_numbers')->where('visit_id', $visitId)->get()->getRow();
        if ($qn) {
            $this->db->table('queue_numbers')
                     ->where('id', $qn->id)
                     ->update([
                         'status'     => $queueStatus,
                         'updated_at' => $now
                     ]);
        } else {
            $this->db->table('queue_numbers')->insert([
                'queue_no'   => $queueNumber,
                'visit_id'   => $visitId,
                'status'     => $queueStatus,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }

    /**
     * Konversi angka ke terbilang Bahasa Indonesia.
     */
    public function terbilang(int $n): string
    {
        if ($n <= 0) return 'nol';
        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($n < 12) {
            return $satuan[$n];
        } elseif ($n < 20) {
            return $this->terbilang($n - 10) . ' belas';
        } elseif ($n < 100) {
            $r = $n % 10;
            return $this->terbilang((int)($n / 10)) . ' puluh' . ($r ? ' ' . $satuan[$r] : '');
        } elseif ($n < 200) {
            return 'seratus ' . $this->terbilang($n - 100);
        } elseif ($n < 1000) {
            $r = $n % 100;
            return $satuan[(int)($n / 100)] . ' ratus' . ($r ? ' ' . $this->terbilang($r) : '');
        }

        return (string)$n;
    }
}
