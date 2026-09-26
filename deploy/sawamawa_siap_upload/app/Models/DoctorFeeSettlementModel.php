<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorFeeSettlementModel extends Model
{
    protected $table            = 'doctor_fee_settlements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'settlement_no',
        'doctor_id',
        'period_start',
        'period_end',
        'total_actions',
        'total_amount',
        'payment_method',
        'status',
        'paid_at',
        'notes',
        'journal_id',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'settlement_no' => 'required|max_length[50]|is_unique[doctor_fee_settlements.settlement_no,id,{id}]',
        'doctor_id'     => 'required|numeric',
        'total_amount'  => 'required|numeric|greater_than[0]',
        'status'        => 'in_list[draft,paid,cancelled]',
    ];
}
