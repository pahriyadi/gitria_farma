<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemErrorAndPerformanceTables extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Tabel Log Error & Exception Tracking (APM)
        if (!$this->db->tableExists('system_error_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'error_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '64',
                    'null'       => false,
                ],
                'error_level' => [
                    'type'       => 'ENUM',
                    'constraint' => ['CRITICAL', 'ERROR', 'WARNING', 'NOTICE'],
                    'default'    => 'ERROR',
                ],
                'message' => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'file' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'line' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'method' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'default'    => 'GET',
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '45',
                    'null'       => true,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'user_agent' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'trace' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'is_resolved' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'resolved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'resolved_by' => [
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
            $this->forge->addKey('error_hash');
            $this->forge->addKey('is_resolved');
            $this->forge->createTable('system_error_logs');
        }

        // 2. Tabel Slow Queries Profiler
        if (!$this->db->tableExists('system_slow_queries')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'query_sql' => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'execution_time_ms' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
                'caller_location' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '255',
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('system_slow_queries');
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->forge->dropTable('system_slow_queries', true);
        $this->forge->dropTable('system_error_logs', true);
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
