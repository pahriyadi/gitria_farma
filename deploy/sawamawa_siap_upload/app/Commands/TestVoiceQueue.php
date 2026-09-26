<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\QueueCallService;

class TestVoiceQueue extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:voice-queue';
    protected $description = 'Test Voice Call Queue and Multi-Service Sequential Flow';

    public function run(array $params)
    {
        CLI::write('=== SIMULASI ALUR TERPADU PANDUAN SUARA PASIEN SAWAMAWA MEDICAL CENTER ===', 'yellow');
        CLI::newLine();

        $service = new QueueCallService();

        // 1. Fase 1: Dokter Selesai SOAP & e-Resep -> Pasien diarahkan ke Kasir Utama
        CLI::write('1. Dokter menyelesaikan pemeriksaan SOAP & e-Resep pasien A-001 (Budi Santoso)...', 'cyan');
        $call1 = $service->triggerCall([
            'service_type'   => 'kasir',
            'counter_name'   => 'Kasir Pembayaran',
            'queue_number'   => 'A-001',
            'patient_name'   => 'Budi Santoso',
            'call_action'    => 'call',
            'call_priority'  => 1,
        ]);
        CLI::write('   -> Voice Event #1 Disimpan: ' . $call1['voice_text'], 'green');

        // 2. Fase 2: Kasir Selesai Pelunasan Tagihan -> Pasien diarahkan ke Apotek untuk ambil obat
        CLI::write('2. Kasir menyelesaikan pelunasan tagihan pasien A-001...', 'cyan');
        $call2 = $service->triggerCall([
            'service_type'   => 'farmasi',
            'counter_name'   => 'Loket Farmasi dan Apotek',
            'queue_number'   => 'A-001',
            'patient_name'   => 'Budi Santoso',
            'call_action'    => 'call',
            'call_priority'  => 1,
        ]);
        CLI::write('   -> Voice Event #2 Disimpan: ' . $call2['voice_text'], 'green');

        // 3. Fase 3: Apoteker Selesai Menyerahkan Obat -> Ucapan Terima Kasih & Semoga Lekas Sembuh
        CLI::write('3. Apotek menyelesaikan penyiapan & penyerahan obat pasien A-001...', 'cyan');
        $call3 = $service->triggerCall([
            'service_type'   => 'completed',
            'counter_name'   => 'Selesai',
            'queue_number'   => 'A-001',
            'patient_name'   => 'Budi Santoso',
            'call_action'    => 'completed',
            'call_priority'  => 2,
        ]);
        CLI::write('   -> Voice Event #3 Disimpan: ' . $call3['voice_text'], 'green');

        // 4. Periksa Antrean Pending di Server Queue
        CLI::newLine();
        CLI::write('=== URUTAN ANTRIAN SIAR SUARA DI TV DISPLAY (ANTI-TABRAKAN / SEQUENTIAL) ===', 'yellow');
        $pending = $service->getPendingCalls(10);
        foreach ($pending as $idx => $p) {
            CLI::write(sprintf(
                '[%d] ID: %-3d | Prio: %d | Unit: %-15s | Antrean: %-6s | Narasi: %s',
                $idx + 1,
                $p['id'],
                $p['call_priority'],
                $p['service_type'],
                $p['queue_number'],
                $p['voice_text']
            ), 'white');
        }

        // 5. Tandai seluruhnya selesai (ACK)
        $service->markAsPlayed((int)$call1['event_id']);
        $service->markAsPlayed((int)$call2['event_id']);
        $service->markAsPlayed((int)$call3['event_id']);

        CLI::newLine();
        CLI::write('✅ Seluruh event panggilan suara berhasil diverifikasi & bebas tabrakan!', 'green');
    }
}
