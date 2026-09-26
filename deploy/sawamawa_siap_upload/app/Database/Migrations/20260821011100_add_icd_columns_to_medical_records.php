<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIcdColumnsToMedicalRecords extends Migration
{
    public function up()
    {
        // Add icd10_code and icd9_code columns to medical_records if they don't exist
        $fields = [
            'icd10_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'plan'
            ],
            'icd9_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'icd10_code'
            ]
        ];

        // Check if columns already exist
        if (!$this->db->fieldExists('icd10_code', 'medical_records')) {
            $this->forge->addColumn('medical_records', [
                'icd10_code' => $fields['icd10_code']
            ]);
        }

        if (!$this->db->fieldExists('icd9_code', 'medical_records')) {
            $this->forge->addColumn('medical_records', [
                'icd9_code' => $fields['icd9_code']
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('icd10_code', 'medical_records')) {
            $this->forge->dropColumn('medical_records', 'icd10_code');
        }
        if ($this->db->fieldExists('icd9_code', 'medical_records')) {
            $this->forge->dropColumn('medical_records', 'icd9_code');
        }
    }
}
