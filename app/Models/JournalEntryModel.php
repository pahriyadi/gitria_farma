<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntryModel extends Model
{
    protected $table            = 'journal_entries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'journal_no',
        'entry_date',
        'source_module',
        'reference_id',
        'description',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'journal_no'  => 'required|max_length[30]|is_unique[journal_entries.journal_no,id,{id}]',
        'entry_date'  => 'required|valid_date',
        'description' => 'required|max_length[255]',
    ];
}
