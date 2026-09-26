<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_no',
        'supplier_id',
        'user_id',
        'status',
        'total_amount',
        'order_date',
        'expected_date',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'po_no'        => 'required|max_length[30]|is_unique[purchase_orders.po_no,id,{id}]',
        'supplier_id'  => 'required|numeric',
        'total_amount' => 'required|numeric',
        'status'       => 'in_list[draft,submitted,approved,rejected,ordered,received,completed,cancelled]',
    ];
}
