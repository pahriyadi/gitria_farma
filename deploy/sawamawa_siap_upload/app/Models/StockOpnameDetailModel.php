<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOpnameDetailModel extends Model
{
    protected $table            = 'stock_opname_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'opname_id',
        'medicine_id',
        'batch_id',
        'system_stock',
        'physical_stock',
        'difference',
        'reason',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'opname_id'      => 'required|numeric',
        'medicine_id'    => 'required|numeric',
        'system_stock'   => 'required|numeric',
        'physical_stock' => 'required|numeric',
    ];
}
