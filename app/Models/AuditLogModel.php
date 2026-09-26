<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'table_name',
        'record_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'action' => 'required|max_length[50]',
        'module' => 'required|max_length[100]',
    ];
}
