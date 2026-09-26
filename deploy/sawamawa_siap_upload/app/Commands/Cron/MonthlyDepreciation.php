<?php

namespace App\Commands\Cron;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MonthlyDepreciation extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:monthly-depreciation';
    protected $description = 'Menghitung dan membukukan jurnal penyusutan aset tetap bulanan secara otomatis';

    public function run(array $params)
    {
        CLI::write("⏰ Menjalankan Cron: Perhitungan Beban Penyusutan Aset Tetap Bulanan...", 'yellow');
        $db = \Config\Database::connect('default');

        $assets = $db->table('inventory_assets')
                     ->where('status', 'active')
                     ->where('purchase_price >', 0)
                     ->where('useful_life_months >', 0)
                     ->get()
                     ->getResult();

        if (empty($assets)) {
            CLI::write("   ℹ️ Tidak ada aset tetap aktif yang memenuhi kriteria penyusutan.", 'white');
            return;
        }

        $totalDepreciation = 0;
        $db->transStart();
        try {
            foreach ($assets as $asset) {
                // Metode Garis Lurus (Straight-line method)
                $depreciableBase = (float) $asset->purchase_price - (float) ($asset->salvage_value ?? 0);
                $monthlyDepr = $depreciableBase / (int) $asset->useful_life_months;

                // Cek batas akumulasi
                $maxDepr = $depreciableBase;
                $currentAccum = (float) ($asset->accumulated_depreciation ?? 0);
                if ($currentAccum + $monthlyDepr > $maxDepr) {
                    $monthlyDepr = $maxDepr - $currentAccum;
                }

                if ($monthlyDepr > 0) {
                    $newAccum = $currentAccum + $monthlyDepr;
                    $newBookValue = (float) $asset->purchase_price - $newAccum;

                    $db->table('inventory_assets')
                       ->where('id', $asset->id)
                       ->update([
                           'accumulated_depreciation' => $newAccum,
                           'current_book_value'       => $newBookValue,
                       ]);

                    $totalDepreciation += $monthlyDepr;
                    CLI::write("   - Aset [{$asset->code}] {$asset->name}: Penyusutan Rp " . number_format($monthlyDepr, 0, ',', '.') . " (Nilai Buku: Rp " . number_format($newBookValue, 0, ',', '.') . ")", 'white');
                }
            }

            // Post Auto-Jurnal Beban Penyusutan Aset jika > 0
            if ($totalDepreciation > 0) {
                $je = new \App\Services\JournalEngine();
                $je->postJournal(
                    'ASSET_DEPRECIATION',
                    date('Ym'),
                    $totalDepreciation,
                    "Penyusutan Aset Tetap Otomatis Periode " . date('F Y'),
                    'non_cash',
                    'Inventaris'
                );
                CLI::write("   ✅ Jurnal Beban Penyusutan berhasil dibukukan total: Rp " . number_format($totalDepreciation, 0, ',', '.'), 'green');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            CLI::error("   ❌ Gagal menghitung penyusutan: " . $e->getMessage());
        }
    }
}
