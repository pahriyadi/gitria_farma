<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table            = 'payrolls';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'payroll_no',
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'net_salary',
        'payment_status',
        'payment_date',
        'payment_method',
        'journal_id',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'payroll_no'     => 'required|max_length[30]|is_unique[payrolls.payroll_no,id,{id}]',
        'employee_id'    => 'required|numeric',
        'month'          => 'required|numeric',
        'year'           => 'required|numeric',
        'net_salary'     => 'required|numeric',
        'payment_status' => 'in_list[pending,paid,cancelled]',
    ];
}
