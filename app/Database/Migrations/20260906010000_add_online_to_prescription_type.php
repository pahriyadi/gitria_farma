<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOnlineToPrescriptionType extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');
        $db->query("ALTER TABLE pharmacy_sales MODIFY COLUMN prescription_type ENUM('bebas', 'resep', 'racikan', 'online') NOT NULL DEFAULT 'bebas'");
    }

    public function down()
    {
        $db = \Config\Database::connect('default');
        $db->query("ALTER TABLE pharmacy_sales MODIFY COLUMN prescription_type ENUM('bebas', 'resep', 'racikan') NOT NULL DEFAULT 'bebas'");
    }
}
