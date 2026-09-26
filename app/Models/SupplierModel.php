<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'name'   => 'required|max_length[100]',
        'phone'  => 'required|max_length[30]',
        'status' => 'in_list[active,inactive]',
    ];
}
