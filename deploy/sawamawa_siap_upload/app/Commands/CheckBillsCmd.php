<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckBillsCmd extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'check:bills';
    protected $description = 'Check billing transactions query';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $billings = $db->table('billing_transactions bt')
                       ->select('bt.*, pv.no_visit, p.name as patient_name, p.no_rm, pv.status as visit_status')
                       ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                       ->join('patients p', 'p.id = pv.patient_id', 'left')
                       ->whereIn('bt.status', ['open', 'partial', 'draft'])
                       ->where('bt.status !=', 'paid')
                       ->where('bt.status !=', 'cancelled')
                       ->orderBy("CASE WHEN pv.status = 'cashier' THEN 1 WHEN bt.status = 'open' THEN 2 ELSE 3 END", '', false)
                       ->orderBy('bt.id', 'DESC')
                       ->get()
                       ->getResult();

        CLI::write("Total Unpaid Billings: " . count($billings), 'green');
        foreach ($billings as $b) {
            CLI::write("ID: {$b->id} | No: {$b->billing_no} | Pasien: {$b->patient_name} | RM: {$b->no_rm} | BillStatus: {$b->status} | VisitStatus: {$b->visit_status} | Grand: Rp " . number_format($b->grand_total, 0, ',', '.'));
        }
    }
}
