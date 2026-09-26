<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClinicalLettersAndLabTables extends Migration
{
    public function up()
    {
        // 1. Table medical_letters
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'letter_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true
            ],
            'letter_type' => [
                'type'       => 'ENUM',
                'constraint' => ['sakit', 'sehat', 'rujukan'],
                'default'    => 'sakit'
            ],
            'visit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true
            ],
            'letter_date' => [
                'type' => 'DATE'
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'duration_days' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
                'default'    => 1
            ],
            'diagnosis' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'purpose' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true
            ],
            'health_status' => [
                'type'       => 'ENUM',
                'constraint' => ['sehat', 'tidak_sehat'],
                'default'    => 'sehat',
                'null'       => true
            ],
            'blood_pressure' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true
            ],
            'height' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true
            ],
            'color_blind' => [
                'type'       => 'ENUM',
                'constraint' => ['normal', 'partial', 'total'],
                'default'    => 'normal',
                'null'       => true
            ],
            'referral_destination' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true
            ],
            'referral_poly' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'referral_reason' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('visit_id');
        $this->forge->addKey('patient_id');
        $this->forge->createTable('medical_letters', true);

        // 2. Table lab_results
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'lab_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true
            ],
            'visit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'patient_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true
            ],
            'doctor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true
            ],
            'officer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Analis Laboratorium'
            ],
            'test_date' => [
                'type' => 'DATE'
            ],
            'test_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 150
            ],
            'result_value' => [
                'type'       => 'VARCHAR',
                'constraint' => 100
            ],
            'normal_range' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['normal', 'high', 'low', 'abnormal'],
                'default'    => 'normal'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('visit_id');
        $this->forge->addKey('patient_id');
        $this->forge->createTable('lab_results', true);
    }

    public function down()
    {
        $this->forge->dropTable('medical_letters', true);
        $this->forge->dropTable('lab_results', true);
    }
}
