<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTuslaEmbalaseToPrescriptionBilling extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Add tusla and embalase to prescription_details
        $prescDetailsFields = $this->db->getFieldNames('prescription_details');
        if (!in_array('tusla', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details ADD COLUMN tusla DECIMAL(15,2) DEFAULT 0.00 AFTER discount;");
        }
        if (!in_array('embalase', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details ADD COLUMN embalase DECIMAL(15,2) DEFAULT 0.00 AFTER tusla;");
        }

        // 2. Add tusla and embalase to billing_details
        $billingDetailsFields = $this->db->getFieldNames('billing_details');
        if (!in_array('tusla', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details ADD COLUMN tusla DECIMAL(15,2) DEFAULT 0.00 AFTER discount;");
        }
        if (!in_array('embalase', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details ADD COLUMN embalase DECIMAL(15,2) DEFAULT 0.00 AFTER tusla;");
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        $prescDetailsFields = $this->db->getFieldNames('prescription_details');
        if (in_array('embalase', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details DROP COLUMN embalase;");
        }
        if (in_array('tusla', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details DROP COLUMN tusla;");
        }

        $billingDetailsFields = $this->db->getFieldNames('billing_details');
        if (in_array('embalase', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details DROP COLUMN embalase;");
        }
        if (in_array('tusla', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details DROP COLUMN tusla;");
        }
        
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
