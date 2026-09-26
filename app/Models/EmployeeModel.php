<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nip',
        'name',
        'position',
        'department',
        'shift_id',
        'join_date',
        'basic_salary',
        'allowance_fixed',
        'allowance_transport',
        'allowance_meal',
        'bank_name',
        'bank_account',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'nip'          => 'required|max_length[30]|is_unique[employees.nip,id,{id}]',
        'name'         => 'required|max_length[100]',
        'position'     => 'required|max_length[50]',
        'basic_salary' => 'required|numeric',
        'status'       => 'in_list[active,inactive,terminated]',
    ];
}
