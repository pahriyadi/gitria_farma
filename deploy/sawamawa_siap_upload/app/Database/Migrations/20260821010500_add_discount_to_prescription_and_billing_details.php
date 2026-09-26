<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountToPrescriptionAndBillingDetails extends Migration
{
    public function up()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        // 1. Add discount column to billing_details
        $billingDetailsFields = $this->db->getFieldNames('billing_details');
        if (!in_array('discount', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details ADD COLUMN discount DECIMAL(15,2) DEFAULT 0.00 AFTER price;");
        }

        // 2. Add discount and status column to prescription_details
        $prescDetailsFields = $this->db->getFieldNames('prescription_details');
        if (!in_array('discount', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details ADD COLUMN discount DECIMAL(15,2) DEFAULT 0.00 AFTER price;");
        }
        if (!in_array('status', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details ADD COLUMN status ENUM('served', 'bought_outside', 'cancelled') DEFAULT 'served' AFTER discount;");
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }

    public function down()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $billingDetailsFields = $this->db->getFieldNames('billing_details');
        if (in_array('discount', $billingDetailsFields)) {
            $this->db->query("ALTER TABLE billing_details DROP COLUMN discount;");
        }
        $prescDetailsFields = $this->db->getFieldNames('prescription_details');
        if (in_array('status', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details DROP COLUMN status;");
        }
        if (in_array('discount', $prescDetailsFields)) {
            $this->db->query("ALTER TABLE prescription_details DROP COLUMN discount;");
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
