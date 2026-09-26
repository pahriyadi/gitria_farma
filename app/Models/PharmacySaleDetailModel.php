<?php

namespace App\Models;

use CodeIgniter\Model;

class PharmacySaleDetailModel extends Model
{
    protected $table            = 'pharmacy_sale_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_id',
        'medicine_id',
        'batch_id',
        'qty',
        'price',
        'discount',
        'subtotal',
        'dosage_instruction',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'sale_id'     => 'required|numeric',
        'medicine_id' => 'required|numeric',
        'qty'         => 'required|numeric|greater_than[0]',
        'subtotal'    => 'required|numeric',
    ];
}
