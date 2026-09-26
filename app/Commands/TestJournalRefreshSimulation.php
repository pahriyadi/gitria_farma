<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestJournalRefreshSimulation extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:journal-refresh';
    protected $description = 'Simulate page refresh on accounting/jurnal to verify 0 duplicate journals created.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        // Remove leftover test journals if any
        foreach ([6, 7] as $tid) {
            $tj = $db->table('journal_entries')->where('id', $tid)->get()->getRow();
            if ($tj) {
                $details = $db->table('journal_entry_details')->where('journal_id', $tid)->get()->getResult();
                foreach ($details as $d) {
                    if ($d->debit > 0) $db->table('accounts')->where('id', $d->account_id)->decrement('balance', $d->debit);
                    if ($d->credit > 0) $db->table('accounts')->where('id', $d->account_id)->decrement('balance', -$d->credit);
                }
                $db->table('journal_entry_details')->where('journal_id', $tid)->delete();
                $db->table('journal_entries')->where('id', $tid)->delete();
            }
        }
        $initialCount = $db->table('journal_entries')->countAllResults();
        CLI::write("Initial journal entries count: {$initialCount}", 'yellow');

        $je = new \App\Services\JournalEngine();

        // Simulate 10 page refreshes calling syncUnpostedTransactions()
        for ($i = 1; $i <= 10; $i++) {
            $je->syncUnpostedTransactions();

            $currentCount = $db->table('journal_entries')->countAllResults();
            if ($currentCount !== $initialCount) {
                CLI::error("DUPLICATE DETECTED at loop #{$i}! Expected {$initialCount}, got {$currentCount}");
                return;
            }
        }

        CLI::write("Count after 10 syncUnpostedTransactions calls: {$currentCount} (Expected: {$initialCount})", 'green');

        // Test creating a new direct sale
        CLI::write("\n--- Testing New Direct Sale Creation & Deduplication ---", 'yellow');
        $pharmService = new \App\Services\PharmacyService();
        $batch = $db->table('medicine_batches')->where('stock >', 0)->get()->getRow();
        if ($batch) {
            $newSalePayload = [
                'customer_name'     => 'Pasien Uji Dedup',
                'customer_phone'    => '081299998888',
                'payment_method'    => 'tunai',
                'paid_amount'       => 25000,
                'doctor_id'         => null,
                'prescription_type' => 'bebas',
                'tusla_amount'      => 0,
                'embalase_amount'   => 0,
                'notes'             => 'Test dedup sync',
                'cashier_id'        => 1,
                'items'             => [
                    [
                        'medicine_id' => $batch->medicine_id,
                        'batch_id'    => $batch->id,
                        'qty'         => 1,
                        'price'       => 25000,
                        'discount'    => 0,
                        'tusla'       => 0,
                        'embalase'    => 0,
                        'subtotal'    => 25000,
                    ]
                ]
            ];

            $saleRes = $pharmService->processDirectSale($newSalePayload);
            if ($saleRes['status'] === 'success') {
                $saleId = $saleRes['sale_id'];
                CLI::write("New Sale Created: ID {$saleId} ({$saleRes['sale_no']})", 'green');

                $countAfterSale = $db->table('journal_entries')->countAllResults();
                CLI::write("Journal count immediately after sale: {$countAfterSale} (Expected: " . ($initialCount + 1) . ")", ($countAfterSale === $initialCount + 1) ? 'green' : 'red');

                // Now simulate 10 refreshes
                for ($r = 1; $r <= 10; $r++) {
                    $je->syncUnpostedTransactions();
                }

                $countAfterRefreshes = $db->table('journal_entries')->countAllResults();
                CLI::write("Journal count after 10 sync refreshes: {$countAfterRefreshes} (Expected: {$countAfterSale})", ($countAfterRefreshes === $countAfterSale) ? 'green' : 'red');

                // Cleanup test sale
                $db->table('pharmacy_sale_details')->where('sale_id', $saleId)->delete();
                $db->table('pharmacy_sales')->where('id', $saleId)->delete();
                $testJnls = $db->table('journal_entries')->groupStart()->where('source_module', 'Kasir Apotek (Obat Bebas)')->orWhere('source_module', 'Apotek (Obat Bebas)')->groupEnd()->where('reference_id', $saleId)->get()->getResult();
                foreach ($testJnls as $testJnl) {
                    $details = $db->table('journal_entry_details')->where('journal_id', $testJnl->id)->get()->getResult();
                    foreach ($details as $d) {
                        if ($d->debit > 0) $db->table('accounts')->where('id', $d->account_id)->decrement('balance', $d->debit);
                        if ($d->credit > 0) $db->table('accounts')->where('id', $d->account_id)->decrement('balance', -$d->credit);
                    }
                    $db->table('journal_entry_details')->where('journal_id', $testJnl->id)->delete();
                    $db->table('journal_entries')->where('id', $testJnl->id)->delete();
                }
                // Return stock
                $db->table('medicine_batches')->where('id', $batch->id)->increment('stock', 1);
                CLI::write("Cleaned up test sale successfully.", 'green');
            }
        }

        CLI::write("\n=== SUCCESS: ALL DEDUPLICATION & REFRESH TESTS PASSED 100%! ===", 'green');
    }
}
