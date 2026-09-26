<?php

namespace App\Models;

use CodeIgniter\Model;

class GoodsReceiptModel extends Model
{
    protected $table            = 'goods_receipts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'receipt_no',
        'po_id',
        'supplier_id',
        'received_date',
        'received_by',
        'invoice_no',
        'notes',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'receipt_no'    => 'required|max_length[50]|is_unique[goods_receipts.receipt_no,id,{id}]',
        'received_date' => 'required|valid_date',
        'received_by'   => 'required|numeric',
    ];
}
