<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingTransactionModel extends Model
{
    protected $table            = 'billing_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'visit_id',
        'total_services',
        'total_medicines',
        'total_restaurant',
        'discount_amount',
        'grand_total',
        'status',
        'payment_method',
        'paid_amount',
        'change_amount',
        'paid_at',
        'cashier_id',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'visit_id'    => 'required|numeric',
        'grand_total' => 'required|numeric',
        'status'      => 'in_list[open,paid,cancelled,partial]',
    ];
}
