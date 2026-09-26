<?php

namespace App\Services;

class ErrorTrackerService
{
    /**
     * Catat exception/error ke dalam database dengan mekanisme deduplikasi hash
     */
    public static function logException(\Throwable $e, string $level = 'ERROR'): bool
    {
        try {
            $db = \Config\Database::connect('default');

            $message   = $e->getMessage();
            $file      = $e->getFile();
            $line      = $e->getLine();
            $trace     = $e->getTraceAsString();
            $errorHash = md5($message . '|' . $file . '|' . $line);

            $request   = service('request');
            $url       = current_url();
            $method    = $request->getMethod();
            $ipAddress = $request->getIPAddress();
            $userAgent = substr((string)$request->getUserAgent(), 0, 255);
            $userId    = session()->get('user_id') ?: null;

            // Cek apakah error yang sama sudah pernah tercatat
            $existing = $db->table('system_error_logs')
                           ->where('error_hash', $errorHash)
                           ->where('is_resolved', 0)
                           ->get()
                           ->getRow();

            if ($existing) {
                // Increment counter dan perbarui waktu terakhir muncul
                $db->table('system_error_logs')
                   ->where('id', $existing->id)
                   ->update([
                       'count'      => (int)$existing->count + 1,
                       'url'        => $url,
                       'ip_address' => $ipAddress,
                       'user_id'    => $userId,
                       'updated_at' => date('Y-m-d H:i:s'),
                   ]);
            } else {
                // Insert log error baru
                $db->table('system_error_logs')->insert([
                    'error_hash'  => $errorHash,
                    'error_level' => strtoupper($level),
                    'message'     => $message,
                    'file'        => $file,
                    'line'        => $line,
                    'url'         => $url,
                    'method'      => strtoupper($method),
                    'ip_address'  => $ipAddress,
                    'user_id'     => $userId,
                    'user_agent'  => $userAgent,
                    'trace'       => $trace,
                    'count'       => 1,
                    'is_resolved' => 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }

            return true;
        } catch (\Throwable $ignored) {
            // Fail-safe: jangan lempar error lagi saat mencatat error
            return false;
        }
    }

    /**
     * Tandai error sebagai selesai ditangani
     */
    public static function markResolved(int $id, int $userId = null): bool
    {
        try {
            $db = \Config\Database::connect('default');
            return $db->table('system_error_logs')
                      ->where('id', $id)
                      ->update([
                          'is_resolved' => 1,
                          'resolved_at' => date('Y-m-d H:i:s'),
                          'resolved_by' => $userId ?: session()->get('user_id') ?: 1,
                      ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Bersihkan log error yang sudah terselesaikan
     */
    public static function clearResolved(): bool
    {
        try {
            $db = \Config\Database::connect('default');
            return $db->table('system_error_logs')->where('is_resolved', 1)->delete();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Bersihkan seluruh log error
     */
    public static function clearAll(): bool
    {
        try {
            $db = \Config\Database::connect('default');
            return $db->table('system_error_logs')->emptyTable();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
