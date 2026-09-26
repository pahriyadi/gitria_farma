<?php

namespace App\Services;

use Config\Database;

/**
 * DatabaseBackupService
 * 
 * Layanan manajemen pencadangan basis data terintegrasi:
 * - Kompresi Gzip Stream tingkat tinggi (.sql.gz) menghemat ~90% ruang penyimpanan.
 * - Ekspor streaming per-tabel & per-batch rendah memori (server tetap ringan & stabil).
 * - Kebijakan retensi & rotasi otomatis (harian 7 hari, bulanan 12 bulan) agar file manager bersih.
 * - Dukungan eksekusi CLI Cron, Web Cron, dan GUI Panel Admin.
 */
class DatabaseBackupService
{
    protected $db;
    protected $backupDir;

    public function __construct()
    {
        $this->db = Database::connect('default');
        $this->backupDir = WRITEPATH . 'backups/';

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        // Amankan direktori backup dari akses langsung web publik
        $htaccess = $this->backupDir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Deny from all\n");
        }
        $indexHtml = $this->backupDir . 'index.html';
        if (!file_exists($indexHtml)) {
            file_put_contents($indexHtml, "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>");
        }
    }

    /**
     * Ambil direktori penyimpanan backup
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Ambil pengaturan auto-backup dari system_settings
     */
    public function getSettings(): array
    {
        $defaults = [
            'backup_auto_daily'               => '1',
            'backup_auto_monthly'             => '1',
            'backup_daily_retention_days'     => '7',
            'backup_monthly_retention_months' => '12',
            'backup_compression'              => 'gzip',
            'backup_last_daily'               => '',
            'backup_last_monthly'             => '',
            'backup_cron_token'               => 'sawamawa_cron_backup_' . substr(md5(config('App')->appTimezone ?? 'sawamawa'), 0, 16)
        ];

        $settings = $defaults;
        if ($this->db->tableExists('system_settings')) {
            $rows = $this->db->table('system_settings')
                ->where('setting_group', 'backup')
                ->orWhereIn('setting_key', array_keys($defaults))
                ->get()
                ->getResultArray();

            foreach ($rows as $r) {
                $settings[$r['setting_key']] = $r['setting_value'];
            }
        }

        return $settings;
    }

    /**
     * Simpan / Perbarui Pengaturan Backup
     */
    public function saveSettings(array $data): bool
    {
        if (!$this->db->tableExists('system_settings')) {
            return false;
        }

        $allowedKeys = [
            'backup_auto_daily',
            'backup_auto_monthly',
            'backup_daily_retention_days',
            'backup_monthly_retention_months',
            'backup_compression',
            'backup_cron_token'
        ];

        foreach ($allowedKeys as $key) {
            if (isset($data[$key])) {
                $val = trim((string)$data[$key]);
                $exists = $this->db->table('system_settings')->where('setting_key', $key)->countAllResults();
                if ($exists > 0) {
                    $this->db->table('system_settings')->where('setting_key', $key)->update(['setting_value' => $val]);
                } else {
                    $this->db->table('system_settings')->insert([
                        'setting_group' => 'backup',
                        'setting_key'   => $key,
                        'setting_value' => $val,
                        'description'   => 'Konfigurasi Otomatisasi Backup Database'
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * Jalankan Ekspor / Pembuatan Backup Database
     * 
     * @param string $type 'daily', 'monthly', atau 'manual'
     * @param string|null $customFilename
     * @return array Status eksekusi dan metrik backup
     */
    public function runBackup(string $type = 'manual', ?string $customFilename = null): array
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(600);

        $startTime = microtime(true);
        $settings = $this->getSettings();
        $useGzip = ($settings['backup_compression'] ?? 'gzip') === 'gzip' && function_exists('gzopen');

        $ext = $useGzip ? '.sql.gz' : '.sql';
        $now = date('Y-m-d_His');
        $dateDay = date('Y-m-d');
        $dateMonth = date('Y-m');

        if ($customFilename) {
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $customFilename);
            if (!str_ends_with($filename, $ext)) {
                $filename .= $ext;
            }
        } else {
            if ($type === 'daily') {
                $filename = "backup_daily_{$dateDay}{$ext}";
            } elseif ($type === 'monthly') {
                $filename = "backup_monthly_{$dateMonth}{$ext}";
            } else {
                $filename = "backup_manual_{$now}{$ext}";
            }
        }

        $filepath = $this->backupDir . $filename;

        // Buka file stream (Gzip level 9 untuk kompresi maksimal & hemat storage)
        $handle = $useGzip ? @gzopen($filepath, 'wb9') : @fopen($filepath, 'wb');
        if (!$handle) {
            return [
                'success' => false,
                'message' => 'Gagal membuat file backup pada: ' . $filepath
            ];
        }

        $writeStream = function(string $content) use ($handle, $useGzip) {
            if ($useGzip) {
                gzwrite($handle, $content);
            } else {
                fwrite($handle, $content);
            }
        };

        // Header SQL Dump
        $dbName = $this->db->getDatabase();
        $writeStream("-- ====================================================================\n");
        $writeStream("-- SAWAMAWA MEDICAL CENTER - AUTOMATED DATABASE BACKUP\n");
        $writeStream("-- Type       : " . strtoupper($type) . " SNAPSHOT\n");
        $writeStream("-- Database   : {$dbName}\n");
        $writeStream("-- Generated  : " . date('Y-m-d H:i:s') . "\n");
        $writeStream("-- Compressed : " . ($useGzip ? 'YES (GZIP Level 9)' : 'NO') . "\n");
        $writeStream("-- ====================================================================\n\n");
        $writeStream("/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
        $writeStream("/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
        $writeStream("/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
        $writeStream("SET NAMES utf8mb4;\n");
        $writeStream("SET FOREIGN_KEY_CHECKS = 0;\n");
        $writeStream("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n");
        $writeStream("SET time_zone = '+00:00';\n\n");

        $tables = $this->db->listTables();
        $totalTables = count($tables);
        $totalRows = 0;
        $uncompressedBytes = 0;

        // Iterasi tabel secara hemat memori (streaming batch)
        foreach ($tables as $table) {
            // Struktur Tabel
            $createTableQuery = $this->db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
            $createTableSql = $createTableQuery['Create Table'] ?? '';

            $tableHeader = "-- --------------------------------------------------------\n";
            $tableHeader .= "-- Table structure for table `{$table}`\n";
            $tableHeader .= "-- --------------------------------------------------------\n";
            $tableHeader .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $tableHeader .= $createTableSql . ";\n\n";

            $uncompressedBytes += strlen($tableHeader);
            $writeStream($tableHeader);

            // Data Tabel (Chunking 500 rows per batch agar server tidak kehabisan RAM)
            $countQuery = $this->db->query("SELECT COUNT(*) as cnt FROM `{$table}`")->getRow();
            $rowCount = $countQuery ? (int)$countQuery->cnt : 0;
            $totalRows += $rowCount;

            if ($rowCount > 0) {
                $writeStream("-- Dumping data for table `{$table}` ({$rowCount} rows)\n");
                $limit = 500;
                $offset = 0;

                while ($offset < $rowCount) {
                    $rows = $this->db->table($table)->limit($limit, $offset)->get()->getResultArray();
                    if (empty($rows)) {
                        break;
                    }

                    $cols = array_keys($rows[0]);
                    $colList = '`' . implode('`, `', $cols) . '`';
                    
                    $rowValues = [];
                    foreach ($rows as $r) {
                        $escapedValues = array_map(function($val) {
                            if ($val === null) return 'NULL';
                            return $this->db->escape($val);
                        }, array_values($r));

                        $rowValues[] = "(" . implode(', ', $escapedValues) . ")";
                    }

                    $insertSql = "INSERT INTO `{$table}` ({$colList}) VALUES \n" . implode(",\n", $rowValues) . ";\n";
                    $uncompressedBytes += strlen($insertSql);
                    $writeStream($insertSql);

                    $offset += $limit;
                    unset($rows, $rowValues);
                }
                $writeStream("\n");
            }
        }

        $writeStream("SET FOREIGN_KEY_CHECKS = 1;\n");
        $writeStream("/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n");
        $writeStream("/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n");
        $writeStream("/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n");
        $writeStream("-- EOD: Completed at " . date('Y-m-d H:i:s') . "\n");

        if ($useGzip) {
            gzclose($handle);
        } else {
            fclose($handle);
        }

        $duration = round(microtime(true) - $startTime, 2);
        $compressedSize = file_exists($filepath) ? filesize($filepath) : 0;
        $savedRatio = $uncompressedBytes > 0 ? round((1 - ($compressedSize / $uncompressedBytes)) * 100, 1) : 0;

        // Catat timestamp eksekusi terakhir di system_settings
        $timestampNow = date('Y-m-d H:i:s');
        if ($type === 'daily') {
            $this->updateSettingKey('backup_last_daily', $timestampNow);
        } elseif ($type === 'monthly') {
            $this->updateSettingKey('backup_last_monthly', $timestampNow);
        }

        // Jalankan rotasi & pembersihan file usang otomatis (Auto-Pruning)
        $pruned = $this->pruneOldBackups();

        // Audit Log
        if (class_exists('\App\Services\AuditService')) {
            \App\Services\AuditService::log(
                'BACKUP',
                'Database Administration',
                "Pencadangan database [{$type}] selesai: {$filename} (" . $this->formatBytes($compressedSize) . ", hemat {$savedRatio}%, {$totalTables} tabel, {$totalRows} baris, {$duration}s)"
            );
        }

        return [
            'success'            => true,
            'type'               => $type,
            'filename'           => $filename,
            'filepath'           => $filepath,
            'compressed_size'    => $compressedSize,
            'compressed_human'   => $this->formatBytes($compressedSize),
            'uncompressed_bytes' => $uncompressedBytes,
            'uncompressed_human' => $this->formatBytes($uncompressedBytes),
            'saved_ratio'        => $savedRatio,
            'tables_count'       => $totalTables,
            'rows_count'         => $totalRows,
            'duration'           => $duration,
            'pruned_files'       => $pruned['deleted_count'] ?? 0,
            'pruned_space_human' => $this->formatBytes($pruned['freed_bytes'] ?? 0),
            'created_at'         => $timestampNow
        ];
    }

    /**
     * Pembersihan File Backup Usang Otomatis (Smart Auto-Pruning & Rotation)
     * Menjaga file manager tetap hemat dan tidak pernah memenuhi server.
     */
    public function pruneOldBackups(): array
    {
        $settings = $this->getSettings();
        $dailyRetention = max(1, (int)($settings['backup_daily_retention_days'] ?? 7));
        $monthlyRetention = max(1, (int)($settings['backup_monthly_retention_months'] ?? 12));
        $manualRetention = 10; // Simpan 10 backup manual terakhir

        $files = glob($this->backupDir . 'backup_*.*');
        if (empty($files)) {
            return ['deleted_count' => 0, 'freed_bytes' => 0];
        }

        $dailyFiles = [];
        $monthlyFiles = [];
        $manualFiles = [];

        foreach ($files as $f) {
            $base = basename($f);
            $mtime = filemtime($f);
            if (str_starts_with($base, 'backup_daily_')) {
                $dailyFiles[$f] = $mtime;
            } elseif (str_starts_with($base, 'backup_monthly_')) {
                $monthlyFiles[$f] = $mtime;
            } elseif (str_starts_with($base, 'backup_manual_') || str_starts_with($base, 'backup_sawamawa_')) {
                $manualFiles[$f] = $mtime;
            }
        }

        // Urutkan dari yang terbaru ke terlama
        arsort($dailyFiles);
        arsort($monthlyFiles);
        arsort($manualFiles);

        $toDelete = [];

        // Hapus file harian yang melebihi kuota retensi
        if (count($dailyFiles) > $dailyRetention) {
            $excessDaily = array_slice(array_keys($dailyFiles), $dailyRetention);
            $toDelete = array_merge($toDelete, $excessDaily);
        }

        // Hapus file bulanan yang melebihi kuota retensi
        if (count($monthlyFiles) > $monthlyRetention) {
            $excessMonthly = array_slice(array_keys($monthlyFiles), $monthlyRetention);
            $toDelete = array_merge($toDelete, $excessMonthly);
        }

        // Hapus file manual yang melebihi batas kuota
        if (count($manualFiles) > $manualRetention) {
            $excessManual = array_slice(array_keys($manualFiles), $manualRetention);
            $toDelete = array_merge($toDelete, $excessManual);
        }

        $deletedCount = 0;
        $freedBytes = 0;

        foreach ($toDelete as $filePath) {
            if (file_exists($filePath)) {
                $freedBytes += filesize($filePath);
                if (@unlink($filePath)) {
                    $deletedCount++;
                }
            }
        }

        return [
            'deleted_count' => $deletedCount,
            'freed_bytes'   => $freedBytes
        ];
    }

    /**
     * Dapatkan Seluruh Daftar File Backup yang Tersedia
     */
    public function getBackupList(): array
    {
        $files = glob($this->backupDir . 'backup_*.*');
        if (empty($files)) {
            return [];
        }

        $list = [];
        foreach ($files as $filePath) {
            $filename = basename($filePath);
            $size = filesize($filePath);
            $mtime = filemtime($filePath);

            $type = 'manual';
            if (str_starts_with($filename, 'backup_daily_')) {
                $type = 'daily';
            } elseif (str_starts_with($filename, 'backup_monthly_')) {
                $type = 'monthly';
            }

            $isGzip = str_ends_with($filename, '.gz');

            $list[] = [
                'filename'     => $filename,
                'filepath'     => $filePath,
                'type'         => $type,
                'is_gzip'      => $isGzip,
                'size_bytes'   => $size,
                'size_human'   => $this->formatBytes($size),
                'created_time' => $mtime,
                'created_at'   => date('Y-m-d H:i:s', $mtime),
                'age_human'    => $this->timeAgo($mtime)
            ];
        }

        // Urutkan dari file terbaru ke terlama
        usort($list, function($a, $b) {
            return $b['created_time'] <=> $a['created_time'];
        });

        return $list;
    }

    /**
     * Cek dan Jalankan Backup Otomatis Terjadwal (Web Cron / Lazy Trigger)
     * Digunakan ketika cron server belum aktif, sehingga sistem tetap aman & terjadwal.
     */
    public function checkAndRunScheduledBackups(): array
    {
        $settings = $this->getSettings();
        $results = [];

        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // 1. Cek Backup Harian
        if (($settings['backup_auto_daily'] ?? '1') === '1') {
            $lastDaily = $settings['backup_last_daily'] ?? '';
            $lastDailyDate = $lastDaily ? date('Y-m-d', strtotime($lastDaily)) : '';

            if ($lastDailyDate !== $today) {
                $results['daily'] = $this->runBackup('daily');
            }
        }

        // 2. Cek Backup Bulanan
        if (($settings['backup_auto_monthly'] ?? '1') === '1') {
            $lastMonthly = $settings['backup_last_monthly'] ?? '';
            $lastMonthlyMonth = $lastMonthly ? date('Y-m', strtotime($lastMonthly)) : '';

            if ($lastMonthlyMonth !== $thisMonth) {
                $results['monthly'] = $this->runBackup('monthly');
            }
        }

        return $results;
    }

    /**
     * Restore Basis Data dari File Backup (.sql atau .sql.gz)
     */
    public function restoreBackup(string $filename): array
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(900);

        $safeName = basename($filename);
        $filepath = $this->backupDir . $safeName;

        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'message' => 'File backup tidak ditemukan: ' . $safeName
            ];
        }

        $isGzip = str_ends_with($safeName, '.gz');
        $handle = $isGzip ? @gzopen($filepath, 'rb') : @fopen($filepath, 'rb');

        if (!$handle) {
            return [
                'success' => false,
                'message' => 'Gagal membuka file backup untuk restorasi.'
            ];
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        $queryBuffer = '';
        $queryCount = 0;
        $errorCount = 0;
        $errors = [];

        while (!($isGzip ? gzeof($handle) : feof($handle))) {
            $line = $isGzip ? gzgets($handle, 65536) : fgets($handle, 65536);
            if ($line === false) break;

            $trimmed = trim($line);
            if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*')) {
                continue;
            }

            $queryBuffer .= $line;

            if (str_ends_with($trimmed, ';')) {
                try {
                    $this->db->query($queryBuffer);
                    $queryCount++;
                } catch (\Throwable $e) {
                    $errorCount++;
                    if (count($errors) < 10) {
                        $errors[] = substr($e->getMessage(), 0, 150);
                    }
                }
                $queryBuffer = '';
            }
        }

        if ($isGzip) {
            gzclose($handle);
        } else {
            fclose($handle);
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        if (class_exists('\App\Services\AuditService')) {
            \App\Services\AuditService::log(
                'RESTORE',
                'Database Administration',
                "Restorasi basis data dari file [{$safeName}] selesai ({$queryCount} query dieksekusi, {$errorCount} error)."
            );
        }

        return [
            'success'     => $errorCount === 0 || $queryCount > 0,
            'filename'    => $safeName,
            'query_count' => $queryCount,
            'error_count' => $errorCount,
            'errors'      => $errors
        ];
    }

    /**
     * Hapus File Backup
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . $safeName;

        if (file_exists($filepath)) {
            $ok = @unlink($filepath);
            if ($ok && class_exists('\App\Services\AuditService')) {
                \App\Services\AuditService::log('DELETE_BACKUP', 'Database Administration', "Menghapus file backup: {$safeName}");
            }
            return $ok;
        }

        return false;
    }

    /**
     * Helper update setting_value di system_settings
     */
    protected function updateSettingKey(string $key, string $value): void
    {
        if (!$this->db->tableExists('system_settings')) return;

        $exists = $this->db->table('system_settings')->where('setting_key', $key)->countAllResults();
        if ($exists > 0) {
            $this->db->table('system_settings')->where('setting_key', $key)->update(['setting_value' => $value]);
        } else {
            $this->db->table('system_settings')->insert([
                'setting_group' => 'backup',
                'setting_key'   => $key,
                'setting_value' => $value,
                'description'   => 'Timestamp Eksekusi Backup'
            ]);
        }
    }

    /**
     * Format byte ukuran file menjadi KB, MB, GB yang manusiawi
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Waktu lampau ramah pengguna (Time ago)
     */
    protected function timeAgo(int $timestamp): string
    {
        $diff = time() - $timestamp;
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        if ($diff < 2592000) return floor($diff / 86400) . ' hari lalu';
        return date('d M Y', $timestamp);
    }
}
