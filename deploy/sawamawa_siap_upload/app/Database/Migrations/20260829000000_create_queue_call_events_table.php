<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQueueCallEventsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'service_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'poliklinik', // pendaftaran, poliklinik, tindakan, laboratorium, farmasi, kasir, custom
            ],
            'service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'counter_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Poliklinik Umum',
            ],
            'queue_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'patient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'visit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'call_action' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'call', // call, recall, transfer, completed, custom
            ],
            'call_priority' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 1, // 0 = Urgent/VIP, 1 = Normal, 2 = Low
            ],
            'voice_text' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'broadcasting', 'played', 'skipped', 'cancelled'],
                'default'    => 'pending',
            ],
            'caller_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'caller_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'played_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['status', 'call_priority', 'created_at']);
        $this->forge->createTable('queue_call_events', true);
    }

    public function down()
    {
        $this->forge->dropTable('queue_call_events', true);
    }
}
