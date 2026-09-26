<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $roleName = session('role_name') ?? 'Super Admin';
        $userId   = session('user_id') ?? 1;
        $isExecutive = in_array($roleName, ['Super Admin', 'Direktur', 'Manajemen', 'Keuangan', 'IT']);
        
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));

        // =========================================================================
        // 1. FINANCIAL METRICS (COA & Real-Time Balances)
        // =========================================================================
        $totalRevenue = 0;
        $totalExpenses = 0;
        $cashBankBalance = 0;
        $totalReceivables = 0;
        $totalPayables = 0;

        if ($db->tableExists('accounts')) {
            $coaAccounts = $db->table('accounts')->get()->getResult();
            foreach ($coaAccounts as $acc) {
                if ($acc->type === 'revenue') {
                    $totalRevenue += $acc->balance;
                } elseif ($acc->type === 'expense') {
                    $totalExpenses += $acc->balance;
                } elseif ($acc->type === 'asset') {
                    if (in_array($acc->code, ['1-101', '1-102', '1-103'])) {
                        $cashBankBalance += $acc->balance;
                    } elseif (in_array($acc->code, ['1-104', '1-105'])) {
                        $totalReceivables += $acc->balance;
                    }
                } elseif ($acc->type === 'liability' && in_array($acc->code, ['2-101', '2-102'])) {
                    $totalPayables += $acc->balance;
                }
            }
        }
        $netProfit = $totalRevenue - $totalExpenses;

        // =========================================================================
        // 2. OPERATIONAL COUNTS & KPIS
        // =========================================================================
        // Total Patient Visits (Total & Today)
        $totalPatients = $db->table('patients')->countAllResults();
        $todayNewPatients = $db->table('patients')->where('DATE(created_at)', $today)->countAllResults();
        
        $todayVisitsCount = $db->table('patient_visits')->where('visit_date', $today)->countAllResults();
        $todayVisitsWaiting = $db->table('patient_visits')->where('visit_date', $today)->whereIn('status', ['waiting', 'triage'])->countAllResults();
        $todayVisitsExamining = $db->table('patient_visits')->where('visit_date', $today)->where('status', 'examining')->countAllResults();
        $todayVisitsCompleted = $db->table('patient_visits')->where('visit_date', $today)->where('status', 'completed')->countAllResults();
        $totalVisitsAllTime = $db->table('patient_visits')->countAllResults();

        // Poliklinik & Doctors Count
        $activePolysCount = $db->table('polikliniks')->where('status', 'active')->countAllResults();
        $activeDoctorsCount = $db->table('doctors')->where('status', 'active')->countAllResults();

        // Prescriptions Metrics (Today)
        $todayPrescriptionsCount = $db->table('prescriptions')->where('DATE(created_at)', $today)->countAllResults();
        $todayPrescriptionsPending = $db->table('prescriptions')->where('DATE(created_at)', $today)->whereIn('status', ['waiting', 'processing'])->countAllResults();
        $todayPrescriptionsCompleted = $db->table('prescriptions')->where('DATE(created_at)', $today)->where('status', 'completed')->countAllResults();

        // Pharmacy Low Stock & Expired Batches Alert
        $lowStockCount = 0;
        if ($db->tableExists('medicines') && $db->tableExists('medicine_batches')) {
            $lowStockRows = $db->table('medicines')
                               ->select('medicines.id, medicines.min_stock, COALESCE(SUM(medicine_batches.stock), 0) as total_stock')
                               ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                               ->groupBy('medicines.id, medicines.min_stock')
                               ->having('total_stock <= medicines.min_stock')
                               ->get()
                               ->getResult();
            $lowStockCount = count($lowStockRows);
        }

        $warningExpDate = date('Y-m-d', strtotime('+90 days'));
        $expiredCount = $db->table('medicine_batches')
                           ->where('expired_date <=', $warningExpDate)
                           ->where('stock >', 0)
                           ->countAllResults();

        // Resto Metrics (Today)
        $todayRestoOrdersCount = $db->table('restaurant_orders')->where('DATE(created_at)', $today)->countAllResults();
        $todayRestoActiveOrders = $db->table('restaurant_orders')->where('DATE(created_at)', $today)->whereIn('status', ['open', 'cooking', 'ready'])->countAllResults();
        $activeTablesCount = $db->table('restaurant_tables')->where('status', 'active')->countAllResults();
        $totalTablesCount = $db->table('restaurant_tables')->countAllResults();

        // HRD & Attendance (Today)
        $todayAttendanceCount = 0;
        $pendingLeaveCount = 0;
        if ($db->tableExists('employee_attendances')) {
            $todayAttendanceCount = $db->table('employee_attendances')->where('date', $today)->countAllResults();
        }
        if ($db->tableExists('employee_leaves')) {
            $pendingLeaveCount = $db->table('employee_leaves')->where('status', 'pending')->countAllResults();
        }

        // Procurement Pending Approvals
        $pendingPoApproval = 0;
        if ($db->tableExists('approval_requests')) {
            $pendingPoApproval = $db->table('approval_requests')->where('status', 'pending')->countAllResults();
        }

        // =========================================================================
        // 3. 7-DAY TREND ANALYTICS FOR CHARTS
        // =========================================================================
        $chartDays = [];
        $chartVisits = [];
        $chartRevenue = [];
        $chartExpenses = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $chartDays[] = date('d M', strtotime($d));
            
            // Visits on this day
            $vCount = $db->table('patient_visits')->where('visit_date', $d)->countAllResults();
            $chartVisits[] = $vCount;

            // Approximate Journal/Billing Revenue on this day
            $revDay = 0;
            $expDay = 0;
            if ($db->tableExists('journal_entries') && $db->tableExists('journal_entry_details')) {
                $jRows = $db->table('journal_entries')
                            ->select('journal_entry_details.debit, journal_entry_details.credit, accounts.type')
                            ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                            ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                            ->where('journal_entries.entry_date', $d)
                            ->get()
                            ->getResult();
                foreach ($jRows as $jr) {
                    if ($jr->type === 'revenue') {
                        $revDay += ($jr->credit - $jr->debit);
                    } elseif ($jr->type === 'expense') {
                        $expDay += ($jr->debit - $jr->credit);
                    }
                }
            }
            $chartRevenue[] = max(0, $revDay);
            $chartExpenses[] = max(0, $expDay);
        }

        // Revenue Breakdown by Unit (Estimasi COA / Transaksi)
        $poliRevenue = 0;
        $farmasiRevenue = 0;
        $restoRevenue = 0;
        $labRevenue = 0;

        if ($db->tableExists('accounts')) {
            $accList = $db->table('accounts')->where('type', 'revenue')->get()->getResult();
            foreach ($accList as $a) {
                if (stripos($a->name, 'Klinik') !== false || stripos($a->name, 'Poli') !== false || stripos($a->name, 'Medis') !== false) {
                    $poliRevenue += $a->balance;
                } elseif (stripos($a->name, 'Apotek') !== false || stripos($a->name, 'Farmasi') !== false || stripos($a->name, 'Obat') !== false) {
                    $farmasiRevenue += $a->balance;
                } elseif (stripos($a->name, 'Resto') !== false || stripos($a->name, 'Makanan') !== false || stripos($a->name, 'Gizi') !== false) {
                    $restoRevenue += $a->balance;
                } elseif (stripos($a->name, 'Lab') !== false) {
                    $labRevenue += $a->balance;
                } else {
                    $poliRevenue += $a->balance;
                }
            }
        }

        // =========================================================================
        // 4. RECENT LIVE ACTIVITY FEEDS
        // =========================================================================
        // Recent Patient Visits
        $recentVisits = $db->table('patient_visits')
                           ->select('patient_visits.*, patients.name as patient_name, patients.no_rm, polyclinics.name as polyclinic_name, doctors.name as doctor_name')
                           ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                           ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                           ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                           ->orderBy('patient_visits.id', 'DESC')
                           ->limit(6)
                           ->get()
                           ->getResult();

        // Recent Prescriptions
        $recentPrescriptions = $db->table('prescriptions')
                                  ->select('prescriptions.*, patient_visits.no_visit, patients.name as patient_name, patients.no_rm, doctors.name as doctor_name')
                                  ->join('patient_visits', 'patient_visits.id = prescriptions.visit_id', 'left')
                                  ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                                  ->join('doctors', 'doctors.id = prescriptions.doctor_id', 'left')
                                  ->orderBy('prescriptions.id', 'DESC')
                                  ->limit(5)
                                  ->get()
                                  ->getResult();

        // Recent Resto Orders
        $recentOrders = $db->table('restaurant_orders')
                           ->select('restaurant_orders.*, restaurant_tables.table_no')
                           ->join('restaurant_tables', 'restaurant_tables.id = restaurant_orders.table_id', 'left')
                           ->orderBy('restaurant_orders.id', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResult();

        // Recent Audit Logs
        $recentAuditLogs = $db->table('audit_logs')
                              ->select('audit_logs.*, users.username')
                              ->join('users', 'users.id = audit_logs.user_id', 'left')
                              ->orderBy('audit_logs.id', 'DESC')
                              ->limit(5)
                              ->get()
                              ->getResult();

        // 3 Live Control Center Datasets
        // 1. Critical Drugs Level (< Safety Stock)
        $criticalDrugsList = $db->table('medicines')
                                ->select('medicines.id, medicines.code, medicines.name, medicines.unit, medicines.min_stock, COALESCE(SUM(medicine_batches.stock), 0) as stock')
                                ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                                ->where('medicines.status', 'active')
                                ->groupBy('medicines.id, medicines.code, medicines.name, medicines.unit, medicines.min_stock')
                                ->having('stock <= medicines.min_stock')
                                ->orderBy('stock', 'ASC')
                                ->limit(5)
                                ->get()
                                ->getResult();

        // 2. Live Rooms & Bed Occupancy
        $liveRoomsList = $db->table('rooms')
                            ->select('rooms.*, COUNT(patient_visits.id) as active_patients')
                            ->join('patient_visits', 'patient_visits.room_id = rooms.id AND patient_visits.visit_date = CURDATE() AND patient_visits.status IN ("waiting", "examining", "triage")', 'left')
                            ->where('rooms.status', 'active')
                            ->groupBy('rooms.id')
                            ->orderBy('rooms.name', 'ASC')
                            ->limit(6)
                            ->get()
                            ->getResult();

        // 3. Unclaimed Insurance / BPJS Pending Claims
        $unclaimedInsuranceList = $db->table('billing_transactions bt')
                                     ->select('bt.*, ip.name as insurance_name, p.name as patient_name, pv.no_visit')
                                     ->join('patient_visits pv', 'pv.id = bt.visit_id', 'left')
                                     ->join('patients p', 'p.id = pv.patient_id', 'left')
                                     ->join('insurance_providers ip', 'ip.id = pv.insurance_id', 'left')
                                     ->where('pv.payment_method', 'asuransi')
                                     ->where('bt.status !=', 'paid')
                                     ->orderBy('bt.id', 'DESC')
                                     ->limit(5)
                                     ->get()
                                     ->getResult();

        $data = [
            'title'                      => 'Dashboard Sawamawa Medical Center',
            'active_menu'                => 'dashboard',
            'isExecutive'                => $isExecutive,
            'roleName'                   => $roleName,
            // Finansial
            'total_revenue'              => $totalRevenue,
            'total_expenses'             => $totalExpenses,
            'net_profit'                 => $netProfit,
            'cash_bank'                  => $cashBankBalance,
            'total_receivables'          => $totalReceivables,
            'total_payables'             => $totalPayables,
            // Pasien & Medis
            'total_patients'             => $totalPatients,
            'today_new_patients'         => $todayNewPatients,
            'today_visits'               => $todayVisitsCount,
            'today_visits_waiting'       => $todayVisitsWaiting,
            'today_visits_examining'     => $todayVisitsExamining,
            'today_visits_completed'     => $todayVisitsCompleted,
            'total_visits_all'           => $totalVisitsAllTime,
            'active_polys_count'         => $activePolysCount,
            'active_doctors_count'       => $activeDoctorsCount,
            // Farmasi
            'today_prescriptions'        => $todayPrescriptionsCount,
            'today_prescriptions_pending'=> $todayPrescriptionsPending,
            'today_prescriptions_done'   => $todayPrescriptionsCompleted,
            'low_stock'                  => $lowStockCount,
            'expired_count'              => $expiredCount,
            // Resto
            'today_resto_orders'         => $todayRestoOrdersCount,
            'today_resto_active_orders'  => $todayRestoActiveOrders,
            'active_tables_count'        => $activeTablesCount,
            'total_tables_count'         => $totalTablesCount,
            // HRD & Approval
            'today_attendance'           => $todayAttendanceCount,
            'pending_leaves'             => $pendingLeaveCount,
            'pending_po_approval'        => $pendingPoApproval,
            // Charts Data
            'chart_days'                 => json_encode($chartDays),
            'chart_visits'               => json_encode($chartVisits),
            'chart_revenue'              => json_encode($chartRevenue),
            'chart_expenses'             => json_encode($chartExpenses),
            'unit_poli_revenue'          => $poliRevenue,
            'unit_farmasi_revenue'       => $farmasiRevenue,
            'unit_resto_revenue'         => $restoRevenue,
            'unit_lab_revenue'           => $labRevenue,
            // Lists & Control Center
            'recent_visits'              => $recentVisits,
            'recent_prescriptions'       => $recentPrescriptions,
            'recent_orders'              => $recentOrders,
            'recent_audit_logs'          => $recentAuditLogs,
            'critical_drugs'             => $criticalDrugsList,
            'live_rooms'                 => $liveRoomsList,
            'unclaimed_insurance'        => $unclaimedInsuranceList
        ];

        return view('dashboard/index', $data);
    }
}
