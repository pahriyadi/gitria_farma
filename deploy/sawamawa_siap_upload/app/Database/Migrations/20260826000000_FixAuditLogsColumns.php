<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixAuditLogsColumns extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE audit_logs MODIFY COLUMN record_id INT NULL DEFAULT 0;");
        $this->db->query("ALTER TABLE audit_logs MODIFY COLUMN table_name VARCHAR(50) NULL DEFAULT '';");
        $this->db->query("ALTER TABLE audit_logs MODIFY COLUMN user_id INT NULL DEFAULT 1;");
        
        $fields = $this->db->getFieldNames('audit_logs');
        if (!in_array('user_agent', $fields)) {
            $this->db->query("ALTER TABLE audit_logs ADD COLUMN user_agent VARCHAR(255) NULL AFTER ip_address;");
        }
    }

    public function down()
    {
        // No-op
    }
}
