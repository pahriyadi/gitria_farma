<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InspectCurrentTransaction extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:inspect-current-tx';
    protected $description = 'Inspect latest transaction, billing, prescription, and journals';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write("=== 1. PATIENT VISITS ===", 'yellow');
        $visits = $db->table('patient_visits')->get()->getResultArray();
        foreach ($visits as $v) {
            CLI::write("ID: {$v['id']} | PatientID: {$v['patient_id']} | Status: {$v['status']}");
        }

        CLI::write("\n=== 2. PRESCRIPTIONS & DETAILS ===", 'yellow');
        $prescs = $db->table('prescriptions')->get()->getResultArray();
        foreach ($prescs as $p) {
            CLI::write("Presc ID: {$p['id']} | Visit ID: {$p['visit_id']} | Status: {$p['status']}");
            $pDetails = $db->table('prescription_details')->where('prescription_id', $p['id'])->get()->getResultArray();
            foreach ($pDetails as $pd) {
                $sub = $pd['subtotal'] ?? (($pd['qty'] * ($pd['price'] ?? 0)) + ($pd['tusla'] ?? 0) + ($pd['embalase'] ?? 0));
                $tusla = $pd['tusla'] ?? 0;
                $emb = $pd['embalase'] ?? 0;
                $prc = $pd['price'] ?? 0;
                CLI::write("  - MedID: {$pd['medicine_id']} | Qty: {$pd['qty']} | Price: {$prc} | Tusla: {$tusla} | Embalase: {$emb} | Subtotal: {$sub}");
            }
        }

        CLI::write("\n=== 3. BILLING TRANSACTIONS & DETAILS ===", 'yellow');
        $bills = $db->table('billing_transactions')->get()->getResultArray();
        foreach ($bills as $b) {
            CLI::write("Bill ID: {$b['id']} | No: {$b['billing_no']} | Services: {$b['total_services']} | Medicines: {$b['total_medicines']} | Grand: {$b['grand_total']} | Status: {$b['status']}");
            $bDetails = $db->table('billing_details')->where('billing_id', $b['id'])->get()->getResultArray();
            foreach ($bDetails as $bd) {
                $tusla = $bd['tusla'] ?? 0;
                $emb = $bd['embalase'] ?? 0;
                CLI::write("  - Type: {$bd['item_type']} | Name: {$bd['item_name']} | Qty: {$bd['qty']} | Price: {$bd['price']} | Tusla: {$tusla} | Embalase: {$emb} | Subtotal: {$bd['subtotal']}");
            }
        }

        CLI::write("\n=== 4. JOURNAL ENTRIES & DETAILS ===", 'yellow');
        $journals = $db->table('journal_entries')->get()->getResultArray();
        foreach ($journals as $j) {
            CLI::write("Journal ID: {$j['id']} | No: {$j['journal_no']} | Date: {$j['entry_date']} | Created: " . ($j['created_at'] ?? 'null') . " | Mod: {$j['source_module']} | Ref: {$j['reference_id']}");
            CLI::write("Desc: {$j['description']}");
            $jDetails = $db->table('journal_entry_details')
                           ->select('journal_entry_details.*, accounts.code, accounts.name as acc_name')
                           ->join('accounts', 'accounts.id = journal_entry_details.account_id', 'left')
                           ->where('journal_id', $j['id'])
                           ->get()
                           ->getResultArray();
            $totDeb = 0;
            $totCrd = 0;
            foreach ($jDetails as $jd) {
                $totDeb += $jd['debit'];
                $totCrd += $jd['credit'];
                CLI::write(sprintf("  [%-6s] %-25s | D: %10.2f | K: %10.2f", $jd['code'], $jd['acc_name'], $jd['debit'], $jd['credit']));
            }
            CLI::write("  TOTAL D: " . number_format($totDeb, 2) . " | K: " . number_format($totCrd, 2));
            CLI::write("---------------------------------------------------------------");
        }

        CLI::write("\n=== 5. CASH TRANSACTIONS ===", 'yellow');
        $cashTxs = $db->table('cash_transactions')->get()->getResultArray();
        foreach ($cashTxs as $ct) {
            CLI::write("CashTx ID: {$ct['id']} | Receipt: {$ct['receipt_no']} | Amount: {$ct['amount']} | Paid: {$ct['paid_amount']} | Change: {$ct['change_amount']}");
        }
    }
}
