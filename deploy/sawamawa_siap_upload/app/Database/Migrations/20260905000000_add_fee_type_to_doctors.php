<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFeeTypeToDoctors extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');
        
        // Cek apakah kolom fee_type sudah ada
        $fields = $db->getFieldNames('doctors');
        if (!in_array('fee_type', $fields)) {
            $this->forge->addColumn('doctors', [
                'fee_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['percentage', 'fixed_amount'],
                    'default'    => 'percentage',
                    'null'       => false,
                    'after'      => 'tindakan_id'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect('default');
        $fields = $db->getFieldNames('doctors');
        if (in_array('fee_type', $fields)) {
            $this->forge->dropColumn('doctors', 'fee_type');
        }
    }
}
