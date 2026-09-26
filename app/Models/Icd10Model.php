<?php

namespace App\Models;

use CodeIgniter\Model;

class Icd10Model extends Model
{
    protected $table            = 'master_icd10';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name_en',
        'name_id',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'code'    => 'required|max_length[10]|is_unique[icd10.code,id,{id}]',
        'name_id' => 'required|max_length[255]',
    ];
}
