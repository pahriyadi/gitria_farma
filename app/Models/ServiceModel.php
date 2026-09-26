<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table            = 'services';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'category',
        'parent_id',
        'polyclinic_id',
        'price',
        'doctor_fee',
        'clinic_fee',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[100]',
        'price' => 'permit_empty|numeric',
    ];
}
