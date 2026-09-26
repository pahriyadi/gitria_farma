<?php

namespace App\Models;

use CodeIgniter\Model;

class PolyclinicModel extends Model
{
    protected $table            = 'polyclinics';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'status' => 'in_list[active,inactive]',
    ];
}
