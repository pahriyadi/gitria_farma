<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemDocumentationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'workflow', // 'workflow', 'role_guide', 'faq', 'security_policy'
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'target_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'Semua Peran',
            ],
            'badge_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'teal',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'fas fa-circle-question',
            ],
            'flow_steps' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'order_num' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_published' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('category');
        $this->forge->addKey('order_num');
        $this->forge->addKey('is_published');
        $this->forge->createTable('system_documentations', true);
    }

    public function down()
    {
        $this->forge->dropTable('system_documentations', true);
    }
}
