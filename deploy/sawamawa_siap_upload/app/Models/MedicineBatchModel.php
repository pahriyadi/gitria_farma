<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineBatchModel extends Model
{
    protected $table            = 'medicine_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'medicine_id',
        'batch_no',
        'expired_date',
        'stock',
        'buy_price',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'medicine_id'  => 'required|numeric',
        'batch_no'     => 'required|max_length[50]',
        'expired_date' => 'required|valid_date',
        'stock'        => 'required|numeric',
    ];
}
