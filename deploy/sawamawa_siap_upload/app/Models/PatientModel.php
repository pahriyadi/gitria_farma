<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table            = 'patients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_rm',
        'nik',
        'name',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bpjs_number',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'no_rm'  => 'required|max_length[30]|is_unique[patients.no_rm,id,{id}]',
        'nik'    => 'required|min_length[16]|max_length[20]|is_unique[patients.nik,id,{id}]',
        'name'   => 'required|min_length[2]|max_length[100]',
        'gender' => 'required|in_list[L,P]',
    ];

    protected $validationMessages = [
        'no_rm' => [
            'required'  => 'Nomor Rekam Medis (No. RM) wajib diisi.',
            'is_unique' => 'Nomor RM ini sudah digunakan.',
        ],
        'nik' => [
            'required'   => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'min_length' => 'NIK harus berjumlah 16 digit.',
            'is_unique'  => 'NIK ini sudah terdaftar atas nama pasien lain.',
        ],
        'name' => [
            'required' => 'Nama lengkap pasien wajib diisi.',
        ],
    ];

    /**
     * Generate Next Nomor Rekam Medis (RM-XXXXXX)
     */
    public function generateNextNoRm(): string
    {
        $lastRM = $this->orderBy('id', 'DESC')->first();
        $nextNum = 1;
        if ($lastRM && preg_match('/RM-(\d+)/', $lastRM->no_rm, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        return 'RM-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
    }
}
