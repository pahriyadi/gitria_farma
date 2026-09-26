<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table            = 'inventory_assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'category',
        'location',
        'purchase_date',
        'purchase_price',
        'useful_life_months',
        'salvage_value',
        'accumulated_depreciation',
        'current_book_value',
        'condition',
        'status',
        'notes',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'code'           => 'required|max_length[30]|is_unique[assets.code,id,{id}]',
        'name'           => 'required|max_length[100]',
        'purchase_price' => 'required|numeric',
        'condition'      => 'in_list[good,fair,damaged,maintenance]',
    ];
}
