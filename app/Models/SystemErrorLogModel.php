<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemErrorLogModel extends Model
{
    protected $table            = 'system_error_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'error_hash',
        'error_level',
        'message',
        'file',
        'line',
        'url',
        'method',
        'ip_address',
        'user_id',
        'user_agent',
        'trace',
        'count',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'error_hash'  => 'required|max_length[64]',
        'error_level' => 'required|max_length[20]',
        'message'     => 'required',
    ];
}
