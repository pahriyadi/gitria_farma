<?php

namespace App\Models;

use CodeIgniter\Model;

class OdontogramModel extends Model
{
    protected $table            = 'odontograms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'patient_id',
        'visit_id',
        'tooth_no',
        'condition',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'patient_id' => 'required|numeric',
        'tooth_no'   => 'required|numeric',
        'condition'  => 'required|max_length[50]',
    ];
}
