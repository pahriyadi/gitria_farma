<?php

namespace App\Commands\Cron;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DailyClosing extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:daily-closing';
    protected $description = 'Menjalankan penutupan transaksi harian (Daily Closing) dan rekonsiliasi kasir klinik';

    public function run(array $params)
    {
        CLI::write("⏰ Menjalankan Cron: Penutupan Buku Kasir & Rekonsiliasi Harian...", 'yellow');
        $db = \Config\Database::connect('default');
        $today = date('Y-m-d');

        // Total Pendapatan Kasir Hari Ini
        $clinicIncome = (float) $db->table('billing_transactions')
                                   ->where('DATE(created_at)', $today)
                                   ->where('status', 'paid')
                                   ->selectSum('grand_total')
                                   ->get()->getRow()->grand_total ?? 0;

        $pharmacyIncome = (float) $db->table('pharmacy_sales')
                                     ->where('sale_date', $today)
                                     ->selectSum('grand_total')
                                     ->get()->getRow()->grand_total ?? 0;

        $restoIncome = (float) $db->table('restaurant_orders')
                                  ->where('DATE(created_at)', $today)
                                  ->where('payment_status', 'paid')
                                  ->selectSum('total_amount')
                                  ->get()->getRow()->total_amount ?? 0;

        $totalIncome = $clinicIncome + $pharmacyIncome + $restoIncome;

        CLI::write("   - Pendapatan Kasir Rawat Jalan : Rp " . number_format($clinicIncome, 0, ',', '.'), 'white');
        CLI::write("   - Pendapatan Kasir Farmasi/Obat: Rp " . number_format($pharmacyIncome, 0, ',', '.'), 'white');
        CLI::write("   - Pendapatan Resto Sehat       : Rp " . number_format($restoIncome, 0, ',', '.'), 'white');
        CLI::write("   -----------------------------------------------------", 'white');
        CLI::write("   📊 TOTAL OMSET HARIAN ({$today}): Rp " . number_format($totalIncome, 0, ',', '.'), 'green');

        \App\Services\AuditService::log(
            'DAILY_CLOSING',
            'Keuangan & Akuntansi',
            "Tutup Buku Harian Tanggal {$today} - Total Penerimaan: Rp " . number_format($totalIncome, 0, ',', '.') . " (Klinik: Rp {$clinicIncome}, Apotek: Rp {$pharmacyIncome}, Resto: Rp {$restoIncome})"
        );

        CLI::write("   ✅ Daily Closing berhasil direkam ke riwayat keuangan & audit trail.\n", 'green');
    }
}
