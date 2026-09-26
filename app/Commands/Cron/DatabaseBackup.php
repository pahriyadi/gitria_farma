<?php

namespace App\Commands\Cron;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\DatabaseBackupService;

class DatabaseBackup extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:database-backup';
    protected $description = 'Pencadangan otomatis basis data MySQL hemat ruang (.sql.gz) dan rotasi file arsip.';
    protected $usage       = 'cron:database-backup [options]';
    protected $arguments   = [];
    protected $options     = [
        '--type'  => 'Tipe backup: auto (default cek jadwal), daily, monthly, atau manual',
        '--prune' => 'Hanya jalankan pembersihan / rotasi file backup usang tanpa membuat backup baru'
    ];

    public function run(array $params)
    {
        CLI::write("=================================================================", 'cyan');
        CLI::write("  🏥 SAWAMAWA MEDICAL CENTER - DATABASE BACKUP & ROTATION ENGINE ", 'white', 'blue');
        CLI::write("=================================================================", 'cyan');

        $backupService = new DatabaseBackupService();

        // 1. Opsi jika hanya menjalankan pruning / rotasi
        $isPruneOnly = array_key_exists('prune', $params) || CLI::getOption('prune');
        if ($isPruneOnly) {
            CLI::write("🧹 Menjalankan pembersihan & rotasi file arsip usang...", 'yellow');
            $pruneResult = $backupService->pruneOldBackups();
            CLI::write("   ✅ Selesai: {$pruneResult['deleted_count']} file usang dibersihkan (" . $backupService->formatBytes($pruneResult['freed_bytes']) . " ruang dibebaskan).", 'green');
            return;
        }

        // 2. Tentukan Tipe Backup
        $typeOption = CLI::getOption('type') ?? ($params['type'] ?? 'auto');

        if ($typeOption === 'auto') {
            CLI::write("⏰ Memeriksa jadwal otomatisasi backup (Daily / Monthly)...", 'yellow');
            $results = $backupService->checkAndRunScheduledBackups();

            if (empty($results)) {
                CLI::write("ℹ️  Backup harian dan bulanan untuk periode ini sudah selesai dibuat sebelumnya.", 'light_gray');
                CLI::write("   Gunakan opsi '--type=daily' atau '--type=manual' untuk memaksa pencadangan langsung.", 'light_gray');
            } else {
                foreach ($results as $cat => $res) {
                    if (!empty($res['success'])) {
                        CLI::write("✅ [{$cat}] Backup Berhasil: {$res['filename']}", 'green');
                        CLI::write("   • Ukuran Kompresi : {$res['compressed_human']} (Asli: {$res['uncompressed_human']} | Hemat: {$res['saved_ratio']}%)", 'light_cyan');
                        CLI::write("   • Cakupan Data    : {$res['tables_count']} tabel, {$res['rows_count']} baris records", 'light_cyan');
                        CLI::write("   • Durasi Eksekusi : {$res['duration']} detik", 'light_cyan');
                    } else {
                        CLI::write("❌ [{$cat}] Gagal: " . ($res['message'] ?? 'Error tidak diketahui'), 'red');
                    }
                }
            }
        } else {
            if (!in_array($typeOption, ['daily', 'monthly', 'manual'])) {
                $typeOption = 'manual';
            }

            CLI::write("🚀 Memulai proses pencadangan basis data (Tipe: " . strtoupper($typeOption) . ")...", 'yellow');
            $res = $backupService->runBackup($typeOption);

            if (!empty($res['success'])) {
                CLI::write("✅ Pencadangan Selesai dengan Sukses!", 'green');
                CLI::write("   • Nama Berkas     : {$res['filename']}", 'white');
                CLI::write("   • Lokasi Arsip    : {$res['filepath']}", 'white');
                CLI::write("   • Ukuran Kompresi : {$res['compressed_human']} (Asli: {$res['uncompressed_human']} | Efisiensi Hemat: {$res['saved_ratio']}%)", 'light_cyan');
                CLI::write("   • Total Konten    : {$res['tables_count']} tabel, {$res['rows_count']} baris records", 'light_cyan');
                CLI::write("   • Waktu Eksekusi  : {$res['duration']} detik", 'light_cyan');
                if (!empty($res['pruned_files']) && $res['pruned_files'] > 0) {
                    CLI::write("   • Rotasi Otomatis : {$res['pruned_files']} file usang dibersihkan ({$res['pruned_space_human']} ruang file manager dibebaskan)", 'light_gray');
                }
            } else {
                CLI::write("❌ Gagal melakukan backup: " . ($res['message'] ?? 'Terjadi kesalahan sistem.'), 'red');
            }
        }

        CLI::write("=================================================================", 'cyan');
    }
}

