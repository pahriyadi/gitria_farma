<?php

namespace App\Models;

use CodeIgniter\Model;

class StockOpnameModel extends Model
{
    protected $table            = 'stock_opnames';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'opname_no',
        'opname_date',
        'user_id',
        'status',
        'total_items',
        'total_discrepancy',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'opname_no'   => 'required|max_length[30]|is_unique[stock_opnames.opname_no,id,{id}]',
        'opname_date' => 'required|valid_date',
        'user_id'     => 'required|numeric',
    ];
}
