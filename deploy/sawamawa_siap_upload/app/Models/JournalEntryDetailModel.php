<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntryDetailModel extends Model
{
    protected $table            = 'journal_entry_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'journal_id',
        'account_id',
        'debit',
        'credit',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'journal_id' => 'required|numeric',
        'account_id' => 'required|numeric',
        'debit'      => 'required|numeric',
        'credit'     => 'required|numeric',
    ];
}
