<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineModel extends Model
{
    protected $table            = 'medicines';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'type',
        'unit',
        'min_stock',
        'price',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'code'  => 'required|max_length[20]|is_unique[medicines.code,id,{id}]',
        'name'  => 'required|min_length[2]|max_length[100]',
        'type'  => 'required|in_list[obat,alkes]',
        'unit'  => 'required|max_length[30]',
        'price' => 'required|numeric',
    ];

    protected $validationMessages = [
        'code' => [
            'required'  => 'Kode obat/alkes wajib diisi.',
            'is_unique' => 'Kode obat ini sudah digunakan.',
        ],
        'name' => [
            'required' => 'Nama obat wajib diisi.',
        ],
    ];
}
