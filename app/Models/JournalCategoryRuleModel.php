<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalCategoryRuleModel extends Model
{
    protected $table            = 'journal_category_rules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'item_name',
        'account_id',
        'position',
        'calc_type',
        'percentage_value',
        'fixed_amount_value',
        'formula_code',
        'sort_order',
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
        'category_id'      => 'required|is_natural_no_zero',
        'item_name'        => 'required|min_length[2]|max_length[150]',
        'position'         => 'required|in_list[debit,credit]',
        'calc_type'        => 'required|in_list[percentage,fixed_amount,dynamic_fee,formula]'
    ];
}
