<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientVisitModel extends Model
{
    protected $table            = 'patient_visits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_visit',
        'patient_id',
        'polyclinic_id',
        'doctor_id',
        'payment_method',
        'status',
        'visit_type',
        'service_id',
        'triage_scale',
        'visit_date',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'no_visit'       => 'required|max_length[30]|is_unique[patient_visits.no_visit,id,{id}]',
        'patient_id'     => 'required|numeric',
        'polyclinic_id'  => 'required|numeric',
        'payment_method' => 'required|in_list[umum,bpjs,asuransi]',
        'status'         => 'in_list[waiting,triage,examining,prescription,cashier,completed,cancelled]',
        'visit_date'     => 'required|valid_date',
    ];

    protected $validationMessages = [
        'no_visit' => [
            'required'  => 'Nomor kunjungan wajib diisi.',
            'is_unique' => 'Nomor kunjungan ini sudah tercatat.',
        ],
        'patient_id' => [
            'required' => 'Identitas pasien wajib ditentukan.',
        ],
        'polyclinic_id' => [
            'required' => 'Poliklinik tujuan wajib dipilih.',
        ],
    ];
}
