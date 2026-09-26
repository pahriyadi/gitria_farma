<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table            = 'accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'type',
        'parent_id',
        'normal_balance',
        'balance',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'code'           => 'required|max_length[20]|is_unique[accounts.code,id,{id}]',
        'name'           => 'required|max_length[100]',
        'type'           => 'required|in_list[asset,liability,equity,revenue,expense]',
        'normal_balance' => 'required|in_list[debit,credit]',
    ];

    protected $validationMessages = [
        'code' => [
            'required'  => 'Kode akun (COA) wajib diisi.',
            'is_unique' => 'Kode akun ini sudah terdaftar.',
        ],
        'name' => [
            'required' => 'Nama akun wajib diisi.',
        ],
    ];
}
