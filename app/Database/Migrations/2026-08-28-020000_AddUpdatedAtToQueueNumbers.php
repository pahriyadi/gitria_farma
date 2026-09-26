<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToQueueNumbers extends Migration
{
    public function up()
    {
        $fields = [
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ];

        if (!$this->db->fieldExists('updated_at', 'queue_numbers')) {
            $this->forge->addColumn('queue_numbers', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('updated_at', 'queue_numbers')) {
            $this->forge->dropColumn('queue_numbers', 'updated_at');
        }
    }
}
