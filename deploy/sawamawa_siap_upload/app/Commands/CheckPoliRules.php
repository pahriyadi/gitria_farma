<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckPoliRules extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:check-poli-rules';
    protected $description = 'Check rules for RAWAT_JALAN_POLI';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $rules = $db->table('journal_category_rules')
            ->select('journal_category_rules.*, accounts.code as acc_code, accounts.name as acc_name')
            ->join('journal_categories', 'journal_categories.id = journal_category_rules.category_id')
            ->join('accounts', 'accounts.id = journal_category_rules.account_id', 'left')
            ->where('journal_categories.category_code', 'RAWAT_JALAN_POLI')
            ->orderBy('journal_category_rules.sort_order', 'ASC')
            ->get()->getResultArray();

        foreach ($rules as $r) {
            CLI::write(sprintf(
                "ID: %d | Pos: %s | Name: %-20s | Acc: [%s] %-20s | Calc: %-12s | Fixed: %10.2f | Pct: %6.2f | Formula: %s",
                $r['id'],
                $r['position'],
                $r['item_name'],
                $r['acc_code'],
                $r['acc_name'],
                $r['calc_type'],
                $r['fixed_amount_value'],
                $r['percentage_value'],
                $r['formula_code']
            ));
        }
    }
}
