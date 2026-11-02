<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDynamicMasterTablesAndUnifyPolyclinics extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');

        // 1. article_categories table
        $db->query("
        CREATE TABLE IF NOT EXISTS `article_categories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `slug` varchar(120) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_art_cat_name` (`name`),
            UNIQUE KEY `idx_art_cat_slug` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");

        // 2. leave_types table
        $db->query("
        CREATE TABLE IF NOT EXISTS `leave_types` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `code` varchar(50) NOT NULL,
            `name` varchar(150) NOT NULL,
            `days_allowed` int(11) NOT NULL DEFAULT 0,
            `is_paid` tinyint(1) NOT NULL DEFAULT 1,
            `description` varchar(255) DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_leave_code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");

        // 3. asset_categories table
        $db->query("
        CREATE TABLE IF NOT EXISTS `asset_categories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `code` varchar(50) NOT NULL,
            `name` varchar(150) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_asset_code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");
    }

    public function down()
    {
        $db = \Config\Database::connect('default');
        $db->query("DROP TABLE IF EXISTS `article_categories`");
        $db->query("DROP TABLE IF EXISTS `leave_types`");
        $db->query("DROP TABLE IF EXISTS `asset_categories`");
    }
}
