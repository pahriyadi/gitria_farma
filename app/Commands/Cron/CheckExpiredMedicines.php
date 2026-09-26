<?php

namespace App\Commands\Cron;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckExpiredMedicines extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:check-expired-medicines';
    protected $description = 'Memindai batch obat mendekati kadaluarsa (< 30/60/90 hari) dan mencatat notifikasi peringatan FEFO';

    public function run(array $params)
    {
        CLI::write("⏰ Menjalankan Cron: Pemindaian Obat Mendekati Kadaluarsa...", 'yellow');
        $db = \Config\Database::connect('default');

        $today = date('Y-m-d');
        $ninetyDays = date('Y-m-d', strtotime('+90 days'));

        $expiring = $db->table('medicine_batches')
                       ->select('medicine_batches.*, medicines.name as medicine_name, medicines.code as medicine_code')
                       ->join('medicines', 'medicines.id = medicine_batches.medicine_id', 'left')
                       ->where('medicine_batches.stock >', 0)
                       ->where('medicine_batches.expired_date <=', $ninetyDays)
                       ->orderBy('medicine_batches.expired_date', 'ASC')
                       ->get()
                       ->getResult();

        if (empty($expiring)) {
            CLI::write("   ✅ Seluruh stok obat aman (tidak ada batch kadaluarsa dalam 90 hari ke depan).", 'green');
            return;
        }

        CLI::write("   ⚠️ Ditemukan " . count($expiring) . " batch obat perlu perhatian khusus (FEFO):", 'yellow');
        foreach ($expiring as $item) {
            $daysLeft = (int) ((strtotime($item->expired_date) - strtotime($today)) / 86400);
            $statusStr = $daysLeft <= 0 ? "KADALUARSA" : "Sisa {$daysLeft} hari";
            $color = $daysLeft <= 30 ? 'red' : 'yellow';

            CLI::write("      - [{$item->medicine_code}] {$item->medicine_name} (Batch: {$item->batch_no}, Stok: {$item->stock}) -> {$statusStr} (Exp: {$item->expired_date})", $color);

            // Trigger log jika kritis (< 30 hari)
            if ($daysLeft <= 30) {
                \App\Services\AuditService::log(
                    'EXPIRY_ALERT',
                    'Apotek & Gudang Farmasi',
                    "Peringatan Kadaluarsa: [{$item->medicine_name}] Batch {$item->batch_no} tersisa {$item->stock} unit ({$statusStr})",
                    'medicine_batches',
                    (int) $item->id
                );
            }
        }
        CLI::write("   ✅ Pemindaian selesai & audit log diperbarui.\n", 'green');
    }
}
