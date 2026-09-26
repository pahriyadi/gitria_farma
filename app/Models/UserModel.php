<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'role_id',
        'status',
        'fullname',
        'avatar',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]',
        'role_id'  => 'required|numeric',
        'status'   => 'in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'username' => [
            'required'  => 'Username wajib diisi.',
            'is_unique' => 'Username ini sudah digunakan.',
        ],
        'email' => [
            'required'    => 'Alamat email wajib diisi.',
            'valid_email' => 'Format email tidak valid.',
            'is_unique'   => 'Email ini sudah terdaftar.',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            // Only hash if not already hashed
            if (!preg_match('/^\$2y\$/', $data['data']['password'])) {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
            }
        } else {
            unset($data['data']['password']);
        }

        return $data;
    }
}
