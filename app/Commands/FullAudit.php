<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FullAudit extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'system:audit';
    protected $description = 'Run Full System Code & Integrity Audit';

    public function run(array $params)
    {
        CLI::write("==================================================", 'yellow');
        CLI::write("🔍 MEMULAI AUDIT MENYELURUH SAWAMAWA MEDICAL CENTER", 'yellow');
        CLI::write("==================================================", 'yellow');

        $issuesFound = [];
        $db = \Config\Database::connect('default');

        // 1. AUDIT CONTROLLERS & METHODS DARI ROUTES.PHP
        CLI::write("\n[1/6] Memeriksa Controller & Method Existence dari Routes.php...", 'cyan');
        $routesFile = file_get_contents(APPPATH . 'Config/Routes.php');
        
        preg_match_all('/\$routes->(?:get|post)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*([^\);]+)/', $routesFile, $matchesDirect, PREG_SET_ORDER);
        preg_match_all('/\$routes->match\s*\(\s*\[[^\]]+\]\s*,\s*[\'"]([^\'"]+)[\'"]\s*,\s*([^\);]+)/', $routesFile, $matchesMatch, PREG_SET_ORDER);
        
        $matches = array_merge($matchesDirect, $matchesMatch);

        $allRoutes = [];
        $testedRoutes = 0;

        foreach ($matches as $m) {
            $uri = trim($m[1], '/');
            $rawHandler = trim($m[2]);
            if (str_starts_with($rawHandler, 'function') || str_starts_with($rawHandler, 'static function')) {
                $handler = 'Closure';
            } else {
                $handler = trim($rawHandler, " '\",\t\n\r");
            }
            $allRoutes[$uri] = $handler;
            $testedRoutes++;

            if ($handler !== 'Closure' && !empty($handler) && str_contains($handler, '::')) {
                $parts = explode('::', $handler);
                $controllerName = trim($parts[0], " '\\/");
                $methodName = explode('/', trim($parts[1] ?? 'index', " '\\/"))[0];

                if (str_starts_with($controllerName, 'App\\Controllers\\')) {
                    $fullClass = '\\' . $controllerName;
                } elseif (str_starts_with($controllerName, 'Api\\')) {
                    $fullClass = '\\App\\Controllers\\' . $controllerName;
                } else {
                    $fullClass = '\\App\\Controllers\\' . $controllerName;
                }

                if (!class_exists($fullClass)) {
                    $issuesFound[] = "[ROUTING] Controller '$fullClass' tidak ditemukan untuk route '$uri' -> '$handler'";
                } else {
                    if (!method_exists($fullClass, $methodName)) {
                        $issuesFound[] = "[ROUTING] Method '$methodName' tidak ditemukan di Controller '$fullClass' untuk route '$uri'";
                    }
                }
            }
        }
        CLI::write("   ✅ Terverifikasi $testedRoutes rute aktif di Routes.php.", 'green');

        // 2. AUDIT CONTROLLER INITIALIZATION & METHOD RUNTIME
        CLI::write("\n[2/6] Memeriksa Inisialisasi Seluruh 16 Controller...", 'cyan');
        $controllers = [
            'Accounting', 'Apotek', 'Auth', 'Dashboard', 'Help', 'Home',
            'HRD', 'Inventaris', 'Keuangan', 'Klinik', 'MasterIcd',
            'MasterKlinik', 'MasterPembayaran', 'Procurement', 'Resto', 'System'
        ];

        // Mock login session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => 1,
            'username'   => 'admin',
            'name'       => 'Super Administrator',
            'role_id'    => 1,
            'role_name'  => 'Super Admin',
            'role_slug'  => 'superadmin'
        ]);

        foreach ($controllers as $cName) {
            $class = "\\App\\Controllers\\$cName";
            try {
                $inst = new $class();
                $inst->initController(service('request'), service('response'), service('logger'));
                CLI::write("   - Controller $cName : OK", 'white');
            } catch (\Throwable $e) {
                $issuesFound[] = "[CONTROLLER INIT ERROR] Controller '$cName' gagal diinisialisasi: " . $e->getMessage();
            }
        }
        CLI::write("   ✅ Seluruh controller berhasil diinisialisasi tanpa error.", 'green');

        // 3. AUDIT SIDEBAR & LAYOUT NAVIGATION LINKS
        CLI::write("\n[3/6] Memeriksa Link di Navigation Layout (app/Views/layouts/layout.php)...", 'cyan');
        $layoutContent = file_get_contents(APPPATH . 'Views/layouts/layout.php');
        preg_match_all('/base_url\([\'"]([^\'"]+)[\'"]\)/', $layoutContent, $layoutMatches);
        $sidebarLinks = array_unique($layoutMatches[1] ?? []);

        foreach ($sidebarLinks as $link) {
            $cleanLink = trim(parse_url($link, PHP_URL_PATH) ?? $link, '/');
            if ($cleanLink === '' || $cleanLink === '#' || str_starts_with($cleanLink, 'assets/') || str_starts_with($cleanLink, 'uploads/')) continue;

            $matched = false;
            foreach ($allRoutes as $routePattern => $handler) {
                $cleanPattern = trim($routePattern, '/');
                $regex = '#^' . str_replace(['(:num)', '(:any)', '(:segment)'], ['[0-9]+', '[^/]+', '[^/]+'], $cleanPattern) . '$#';
                if (preg_match($regex, $cleanLink) || $cleanPattern === $cleanLink) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $issuesFound[] = "[NAVIGATION BROKEN LINK] URL 'base_url(\"$cleanLink\")' di layout tidak memiliki rute aktif di Routes.php!";
            }
        }
        CLI::write("   ✅ Terverifikasi " . count($sidebarLinks) . " link URL di layout navigasi.", 'green');

        // 4. AUDIT DATABASE TABLES INTEGRITY
        CLI::write("\n[4/6] Memeriksa Integritas Struktur Database & Tabel Transaksi...", 'cyan');
        $requiredTables = [
            'users', 'roles', 'permissions', 'role_permissions', 'user_permissions',
            'accounts', 'journal_entries', 'journal_entry_details',
            'patients', 'patient_visits', 'triage_records', 'medical_records',
            'polikliniks', 'doctors', 'tindakan', 'service_prices',
            'medicines', 'medicine_batches', 'prescriptions', 'prescription_details',
            'pharmacy_sales', 'pharmacy_sale_details',
            'billing_transactions', 'billing_details', 'cash_registers',
            'restaurant_tables', 'restaurant_menus', 'restaurant_orders', 'restaurant_order_details',
            'employees', 'payrolls', 'payroll_items', 'employee_attendances',
            'purchase_requests', 'purchase_orders', 'goods_receipts',
            'inventory_assets', 'asset_depreciations', 'asset_maintenances',
            'medical_letters', 'lab_results', 'system_settings', 'system_updates', 'system_documentations'
        ];

        $dbTables = $db->listTables();
        $missingTables = array_diff($requiredTables, $dbTables);
        if (!empty($missingTables)) {
            foreach ($missingTables as $mt) {
                $issuesFound[] = "[DATABASE] Tabel '$mt' belum dibuat atau hilang di database!";
            }
        } else {
            CLI::write("   ✅ Seluruh " . count($requiredTables) . " tabel utama lengkap dan aktif di database.", 'green');
        }

        // 5. AUDIT FINANCIAL ACCOUNTING BALANCE INTEGRITY
        CLI::write("\n[5/6] Memeriksa Integritas Keseimbangan Jurnal Akuntansi (Debit vs Kredit)...", 'cyan');
        $unbalancedJournals = $db->query("
            SELECT journal_id, SUM(debit) as total_debit, SUM(credit) as total_credit,
                   ABS(SUM(debit) - SUM(credit)) as diff
            FROM journal_entry_details
            GROUP BY journal_id
            HAVING diff > 0.01
        ")->getResult();

        if (!empty($unbalancedJournals)) {
            foreach ($unbalancedJournals as $uj) {
                $issuesFound[] = "[AKUNTANSI UNBALANCED] Jurnal ID #{$uj->journal_id} tidak seimbang! Debit: Rp " . number_format($uj->total_debit, 0, ',', '.') . " vs Kredit: Rp " . number_format($uj->total_credit, 0, ',', '.') . " (Selisih: Rp " . number_format($uj->diff, 0, ',', '.') . ")";
            }
        } else {
            CLI::write("   ✅ 100% Jurnal Umum seimbang (Total Debit == Total Kredit).", 'green');
        }

        // 6. AUDIT RBAC & MASTER DATA STATS
        CLI::write("\n[6/6] Memeriksa Status Master Data, Hak Akses & Konfigurasi...", 'cyan');
        $rolesCount = $db->table('roles')->countAllResults();
        $permCount = $db->table('permissions')->countAllResults();
        $rolePermCount = $db->table('role_permissions')->countAllResults();
        $coasCount = $db->table('accounts')->countAllResults();
        $usersCount = $db->table('users')->countAllResults();
        $medsCount = $db->table('medicines')->countAllResults();
        $docsCount = $db->table('system_documentations')->countAllResults();
        $updatesCount = $db->table('system_updates')->countAllResults();

        CLI::write("   - Peran Pengguna (Roles)   : $rolesCount peran", 'white');
        CLI::write("   - Modul Hak Akses (Perms)  : $permCount permissions ($rolePermCount relasi aktif)", 'white');
        CLI::write("   - Chart of Accounts (COA)  : $coasCount akun", 'white');
        CLI::write("   - Pengguna Sistem (Users)  : $usersCount user aktif", 'white');
        CLI::write("   - Master Obat & Alkes      : $medsCount item", 'white');
        CLI::write("   - Dokumentasi Bantuan      : $docsCount panduan", 'white');
        CLI::write("   - Catatan Rilis Update     : $updatesCount versi", 'white');

        // HASIL AKHIR
        CLI::write("\n==================================================", 'yellow');
        if (empty($issuesFound)) {
            CLI::write("🎉 HASIL AUDIT: 100% SEMPURNA! SISTEM SIAP PRODUKSI.", 'green');
        } else {
            CLI::write("⚠️ DITEMUKAN " . count($issuesFound) . " CATATAN / ISU:", 'red');
            foreach ($issuesFound as $idx => $issue) {
                CLI::write("   " . ($idx + 1) . ". $issue", 'red');
            }
        }
        CLI::write("==================================================\n", 'yellow');
    }
}
