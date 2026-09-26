<?php

namespace App\Services;

class AuditService
{
    /**
     * Catat aktivitas pengguna secara terpusat ke tabel audit_logs
     */
    public static function log(string $action, string $module, string $description, ?string $tableName = '', ?int $recordId = 0, $oldVal = null, $newVal = null): bool
    {
        try {
            $db = \Config\Database::connect('default');
            $request = service('request');
            $userId = session()->get('user_id') ?: 1;

            $db->table('audit_logs')->insert([
                'user_id'    => $userId,
                'action'     => strtoupper($action),
                'module'     => $module,
                'table_name' => $tableName ?: $module,
                'record_id'  => $recordId ?: $userId,
                'old_value'  => is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : (string)$oldVal,
                'new_value'  => $description ?: (is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : (string)$newVal),
                'ip_address' => $request->getIPAddress() ?: '127.0.0.1',
                'user_agent' => substr((string)$request->getUserAgent(), 0, 255),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
