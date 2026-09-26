<?php

namespace App\Models;

use CodeIgniter\Model;

class RestaurantOrderModel extends Model
{
    protected $table            = 'restaurant_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_no',
        'customer_name',
        'table_number',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'visit_id',
        'cashier_id',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'order_no'     => 'required|max_length[30]|is_unique[restaurant_orders.order_no,id,{id}]',
        'total_amount' => 'required|numeric',
        'status'       => 'in_list[pending,cooking,ready,served,completed,cancelled]',
    ];
}
