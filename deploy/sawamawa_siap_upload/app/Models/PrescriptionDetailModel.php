<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionDetailModel extends Model
{
    protected $table            = 'prescription_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'prescription_id',
        'medicine_id',
        'qty',
        'dosage',
        'price',
        'discount',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'prescription_id' => 'required|numeric',
        'medicine_id'     => 'required|numeric',
        'qty'             => 'required|numeric|greater_than[0]',
        'dosage'          => 'required|max_length[100]',
        'price'           => 'required|numeric',
    ];
}
