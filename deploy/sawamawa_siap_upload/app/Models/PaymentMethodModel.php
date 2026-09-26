<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table            = 'payment_methods';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'category',
        'account_id',
        'admin_fee',
        'instructions',
        'is_active',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'code' => 'required|max_length[30]|is_unique[payment_methods.code,id,{id}]',
        'name' => 'required|max_length[100]',
    ];
}
