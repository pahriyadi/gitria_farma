<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMembershipTierToPatients extends Migration
{
    public function up()
    {
        $fields = [
            'membership_tier' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'regular',
                'after'      => 'bpjs_number'
            ],
        ];
        if (!$this->db->fieldExists('membership_tier', 'patients')) {
            $this->forge->addColumn('patients', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('membership_tier', 'patients')) {
            $this->forge->dropColumn('patients', 'membership_tier');
        }
    }
}
