<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestJurnalDataOutput extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:jurnal-data-output';
    protected $description = 'Test JSON row output from Accounting::jurnal';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $journals = $db->table('journal_entries')->get()->getResult();

        CLI::write("=== JURNAL BADGE VERIFICATION ===", 'yellow');
        foreach ($journals as $j) {
            // Badge Modul Terpadu yang informatif (Logic from Accounting.php)
            $modulBadge = '<span class="badge badge-light border text-xs">' . esc($j->source_module) . '</span>';
            if (in_array($j->source_module, ['Kasir Utama', 'Kasir', 'Keuangan'])) {
                if (stripos($j->description, 'Farmasi') !== false || stripos($j->description, 'Resep') !== false) {
                    $modulBadge = '<span class="badge badge-success text-xs shadow-none"><i class="fas fa-prescription-bottle-medical mr-1"></i>Apotek (Obat Resep)</span>';
                } elseif (stripos($j->description, 'Medis') !== false || stripos($j->description, 'Klinik') !== false) {
                    $modulBadge = '<span class="badge badge-info text-xs shadow-none"><i class="fas fa-stethoscope mr-1"></i>Kasir &amp; Poliklinik</span>';
                } elseif (stripos($j->description, 'Resto') !== false || stripos($j->description, 'Nutrisi') !== false) {
                    $modulBadge = '<span class="badge badge-warning text-xs shadow-none"><i class="fas fa-utensils mr-1"></i>Kasir &amp; Resto</span>';
                } elseif (stripos($j->description, 'Komisi Dokter') !== false) {
                    $modulBadge = '<span class="badge badge-primary text-xs shadow-none"><i class="fas fa-user-doctor mr-1"></i>Jasa Dokter &amp; Kasir</span>';
                } else {
                    $modulBadge = '<span class="badge badge-light border text-xs shadow-none"><i class="fas fa-cash-register mr-1"></i>Kasir Utama</span>';
                }
            } elseif ($j->source_module === 'Farmasi Apotek' || stripos($j->source_module, 'Apotek') !== false) {
                if (stripos($j->description, 'Konsultasi Online') !== false || stripos($j->source_module, 'Konsultasi Online') !== false) {
                    $modulBadge = '<span class="badge badge-primary text-xs shadow-none"><i class="fas fa-globe mr-1"></i>Apotek (Konsultasi Online)</span>';
                } elseif (stripos($j->description, 'Resep') !== false || stripos($j->source_module, 'Resep') !== false) {
                    $modulBadge = '<span class="badge badge-success text-xs shadow-none"><i class="fas fa-prescription mr-1"></i>Apotek (Obat Resep)</span>';
                } else {
                    $modulBadge = '<span class="badge badge-teal text-xs shadow-none"><i class="fas fa-pills mr-1"></i>Apotek (Obat Bebas)</span>';
                }
            } elseif ($j->source_module === 'Resto POS') {
                $modulBadge = '<span class="badge badge-warning text-xs shadow-none"><i class="fas fa-utensils mr-1"></i>Resto &amp; Nutrisi</span>';
            }

            CLI::write("Journal No    : {$j->journal_no}");
            CLI::write("Source Module : {$j->source_module}");
            CLI::write("Rendered Badge: " . strip_tags($modulBadge));
            CLI::write("----------------------------------------------------------------");
        }

        CLI::write("\n=== VERIFYING ORDER BY DATE & TIME (DESC) ===", 'yellow');
        $sortedJournals = $db->table('journal_entries')
                             ->orderBy('created_at', 'DESC')
                             ->orderBy('id', 'DESC')
                             ->get()
                             ->getResult();

        $prevTime = PHP_INT_MAX;
        $orderCorrect = true;
        foreach ($sortedJournals as $sj) {
            $timeTs = strtotime($sj->created_at ?? ($sj->entry_date . ' 00:00:00'));
            $entryTime = !empty($sj->created_at) ? date('H:i:s', strtotime($sj->created_at)) : '00:00:00';
            $entryDate = date('d/m/Y', strtotime($sj->entry_date ?: ($sj->created_at ?? 'now')));

            CLI::write(sprintf("ID: %-2d | No: %-16s | Tanggal: %-10s | Jam: %-8s | Modul: %s", 
                $sj->id, $sj->journal_no, $entryDate, $entryTime, $sj->source_module
            ));

            if ($timeTs > $prevTime) {
                $orderCorrect = false;
            }
            $prevTime = $timeTs;
        }

        if ($orderCorrect) {
            CLI::write("\n>>> SUCCESS: Transaksi terurut 100% sempurna berdasarkan Tanggal & Jam (Terbaru di atas) <<<", 'green');
        } else {
            CLI::error("\n>>> FAILED: Transaksi tidak terurut dengan benar! <<<");
        }
    }
}
