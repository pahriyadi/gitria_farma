<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixVisitAndQueueStatusColumns extends Migration
{
    public function up()
    {
        // 1. Modify patient_visits.status to VARCHAR(30) to allow flexible workflow statuses ('waiting', 'called', 'triage', 'examining', 'prescription', 'cashier', 'completed', 'cancelled')
        $this->db->query("ALTER TABLE patient_visits MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'waiting';");

        // 2. Modify queue_numbers.status to VARCHAR(30)
        $this->db->query("ALTER TABLE queue_numbers MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'waiting';");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE patient_visits MODIFY COLUMN status ENUM('waiting', 'triage', 'examining', 'prescription', 'cashier', 'completed', 'cancelled') DEFAULT 'waiting';");
        $this->db->query("ALTER TABLE queue_numbers MODIFY COLUMN status ENUM('waiting', 'called', 'completed', 'no-show', 'cancelled') DEFAULT 'waiting';");
    }
}
