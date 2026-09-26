<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionModel extends Model
{
    protected $table            = 'prescriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'visit_id',
        'doctor_id',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'visit_id'  => 'required|numeric',
        'doctor_id' => 'required|numeric',
        'status'    => 'in_list[waiting,processing,completed,cancelled]',
    ];
}
