<?php

namespace App\Models;

use CodeIgniter\Model;

class PharmacySaleModel extends Model
{
    protected $table            = 'pharmacy_sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_no',
        'customer_name',
        'customer_phone',
        'sale_date',
        'total_amount',
        'discount_amount',
        'grand_total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'cashier_id',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'sale_no'        => 'required|max_length[50]|is_unique[pharmacy_sales.sale_no,id,{id}]',
        'customer_name'  => 'required|max_length[150]',
        'sale_date'      => 'required|valid_date',
        'grand_total'    => 'required|numeric',
        'payment_method' => 'required|in_list[tunai,debit,transfer,qris]',
    ];
}
