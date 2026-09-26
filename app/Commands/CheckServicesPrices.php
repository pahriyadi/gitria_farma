<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckServicesPrices extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:check-services-prices';
    protected $description = 'Check services and service_prices';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $svcs = $db->table('services')
            ->select('services.id, services.code, services.name, services.category, service_prices.price')
            ->join('service_prices', 'service_prices.service_id = services.id', 'left')
            ->get()->getResultArray();

        CLI::write("=== SERVICES & SERVICE_PRICES ===", 'yellow');
        foreach ($svcs as $s) {
            CLI::write("ID: {$s['id']} | Code: {$s['code']} | Name: {$s['name']} | Cat: {$s['category']} | Price: " . number_format($s['price'] ?? 0, 2));
        }

        $tindakan = $db->table('tindakan')->get()->getResultArray();
        CLI::write("\n=== TINDAKAN TABLE ===", 'yellow');
        foreach ($tindakan as $t) {
            CLI::write("ID: {$t['id']} | Code: {$t['code']} | Name: {$t['name']} | Price: " . number_format($t['price'] ?? 0, 2));
        }
    }
}
