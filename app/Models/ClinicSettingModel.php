<?php

namespace App\Models;

use CodeIgniter\Model;

class ClinicSettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_group',
        'key_name',
        'setting_value',
        'description',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'setting_group' => 'required|max_length[50]',
        'key_name'      => 'required|max_length[100]',
    ];
}
