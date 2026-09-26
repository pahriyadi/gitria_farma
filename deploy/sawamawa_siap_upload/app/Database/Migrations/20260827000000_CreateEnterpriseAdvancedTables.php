<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnterpriseAdvancedTables extends Migration
{
    public function up()
    {
        // 1. Tabel Odontogram (Poli Gigi)
        if (!$this->db->tableExists('odontograms')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'visit_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'patient_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tooth_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10', // e.g. '11', '12', '51', etc.
                ],
                'condition_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '20', // e.g. 'caries', 'filling', 'missing', 'crown', 'bridge', 'normal'
                    'default'    => 'normal',
                ],
                'condition_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '100',
                    'null'       => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
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
            $this->forge->addKey(['patient_id', 'tooth_number']);
            $this->forge->createTable('odontograms');
        }

        // 2. Tabel Settlement / Pembayaran Fee Dokter
        if (!$this->db->tableExists('doctor_fee_settlements')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'settlement_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'unique'     => true,
                ],
                'doctor_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'period_start' => [
                    'type' => 'DATE',
                ],
                'period_end' => [
                    'type' => 'DATE',
                ],
                'total_actions' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'total_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0.00,
                ],
                'payment_method' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50',
                    'default'    => 'Transfer Bank',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draft', 'paid', 'cancelled'],
                    'default'    => 'paid',
                ],
                'paid_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'journal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
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
            $this->forge->createTable('doctor_fee_settlements');
        }

        // 3. Tabel Log WhatsApp Gateway
        if (!$this->db->tableExists('whatsapp_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'recipient_phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '30',
                ],
                'recipient_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '150',
                    'null'       => true,
                ],
                'message_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '50', // e.g. 'antrean_online', 'obat_siap', 'pengingat_kontrol', 'slip_gaji'
                ],
                'message_content' => [
                    'type' => 'TEXT',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'sent', 'failed'],
                    'default'    => 'sent',
                ],
                'sent_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('whatsapp_logs');
        }
    }

    public function down()
    {
        $this->forge->dropTable('odontograms', true);
        $this->forge->dropTable('doctor_fee_settlements', true);
        $this->forge->dropTable('whatsapp_logs', true);
    }
}
