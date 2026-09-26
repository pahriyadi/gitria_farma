<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalCategoryModel extends Model
{
    protected $table            = 'journal_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_code',
        'category_name',
        'module',
        'description',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'category_code' => 'required|min_length[3]|max_length[50]|is_unique[journal_categories.category_code,id,{id}]',
        'category_name' => 'required|min_length[3]|max_length[150]',
        'module'        => 'required|max_length[50]'
    ];
}
