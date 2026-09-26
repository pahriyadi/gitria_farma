<?php

namespace App\Models;

use CodeIgniter\Model;

class CashTransactionModel extends Model
{
    protected $table            = 'cash_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaction_no',
        'type',
        'category',
        'amount',
        'payment_method',
        'reference_type',
        'billing_id',
        'reference_id',
        'description',
        'user_id',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'transaction_no' => 'required|max_length[30]|is_unique[cash_transactions.transaction_no,id,{id}]',
        'type'           => 'required|in_list[in,out]',
        'amount'         => 'required|numeric|greater_than[0]',
        'description'    => 'required|max_length[255]',
    ];
}
