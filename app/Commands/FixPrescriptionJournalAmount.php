<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JournalEngine;

class FixPrescriptionJournalAmount extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'journal:fix-prescription-amount';
    protected $description = 'Recalculate JV-20260906-0002 to accurate 108.000 with tusla and embalase';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $je = new JournalEngine();

        $jnl = $db->table('journal_entries')->where('journal_no', 'JV-20260906-0002')->get()->getRow();
        if (!$jnl) {
            CLI::error("Journal JV-20260906-0002 tidak ditemukan.");
            return;
        }

        // Delete old details of JV-20260906-0002
        $db->table('journal_entry_details')->where('journal_id', $jnl->id)->delete();
        $db->table('journal_entries')->where('id', $jnl->id)->delete();

        // Check billing 1
        $bill = $db->table('billing_transactions')->where('id', $jnl->reference_id)->get()->getRow();
        $allBillDetails = $db->table('billing_details')
                             ->where('billing_id', $bill->id)
                             ->where('item_type', 'obat')
                             ->get()->getResult();

        $calcMedTotal = 0;
        foreach ($allBillDetails as $bd) {
            $calcMedTotal += (float)$bd->subtotal;
        }

        CLI::write("Calculated accurate medicine total from billing_details: Rp " . number_format($calcMedTotal, 0, ',', '.'), 'yellow');

        // Post fresh journal
        $res = $je->postPharmacySplitJournal(
            'resep',
            $bill->id,
            $calcMedTotal,
            $jnl->description,
            !empty($bill->payment_method) ? $bill->payment_method : 'tunai',
            5.0,
            'Apotek (Obat Resep)'
        );

        // Rename the newly generated journal_no back to JV-20260906-0002 for continuity
        $newJnl = $db->table('journal_entries')->where('journal_no', $res['journal_no'])->get()->getRow();
        if ($newJnl) {
            $db->table('journal_entries')->where('id', $newJnl->id)->update(['journal_no' => 'JV-20260906-0002']);
        }

        CLI::write("Jurnal JV-20260906-0002 berhasil diperbarui ke Rp " . number_format($calcMedTotal, 0, ',', '.'), 'green');

        // Update cash_transactions to match the new 258.000 billing grand total
        $db->table('cash_transactions')->where('billing_id', $bill->id)->update([
            'amount'      => (float)$bill->grand_total,
            'paid_amount' => (float)$bill->grand_total
        ]);
        CLI::write("Cash transaction for billing #{$bill->id} updated to Rp " . number_format($bill->grand_total, 0, ',', '.'), 'green');

        // Verify details
        $details = $db->table('journal_entry_details jed')
                      ->select('jed.*, a.code, a.name')
                      ->join('accounts a', 'a.id = jed.account_id')
                      ->where('jed.journal_id', $newJnl->id)
                      ->get()->getResult();

        $totD = 0; $totK = 0;
        CLI::write("\n=== RINCIAN JURNAL TERBARU JV-20260906-0002 ===", 'yellow');
        foreach ($details as $d) {
            CLI::write(sprintf("  [%-6s] %-25s | D: %10.2f | K: %10.2f", $d->code, $d->name, $d->debit, $d->credit));
            $totD += $d->debit;
            $totK += $d->credit;
        }
        CLI::write("TOTAL DEBET : Rp " . number_format($totD, 2));
        CLI::write("TOTAL KREDIT: Rp " . number_format($totK, 2));
    }
}
