<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table            = 'doctors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'nik_doctor',
        'name',
        'polyclinic_id',
        'tindakan_id',
        'fee_per_pasien',
        'sip_number',
        'str_number',
        'str_expiry',
        'phone',
        'status',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'name'           => 'required|min_length[2]|max_length[100]',
        'fee_per_pasien' => 'permit_empty|numeric',
        'status'         => 'in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama dokter spesialis/umum wajib diisi.',
        ],
    ];
}
