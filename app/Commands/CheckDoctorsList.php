<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckDoctorsList extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:check-doctors-list';
    protected $description = 'Check doctors table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $docs = $db->table('doctors')->get()->getResultArray();

        CLI::write("=== DOCTORS TABLE ===", 'yellow');
        foreach ($docs as $d) {
            CLI::write(sprintf(
                "ID: %d | Code: %s | Name: %-25s | FeeType: %-12s | Fee: %10.2f | RxPct: %5.2f | PoliID: %s",
                $d['id'],
                $d['doctor_code'] ?? ($d['nik_employee'] ?? '-'),
                $d['doctor_name'] ?? ($d['name'] ?? '-'),
                $d['fee_type'] ?? 'percentage',
                $d['fee_per_pasien'] ?? 0,
                $d['fee_resep_pct'] ?? 0,
                $d['polyclinic_id'] ?? '-'
            ));
        }
    }
}
