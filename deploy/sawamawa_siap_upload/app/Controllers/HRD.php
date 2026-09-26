<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HRD extends BaseController
{
    /**
     * Dashboard Master Pegawai, Komisi Jasa Medis & Payroll Penggajian
     */
    public function pegawai()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            // 1. TAMBAH PEGAWAI BARU
            if ($action === 'add_employee') {
                $nip = trim($this->request->getPost('nip') ?: '');
                if (empty($nip)) {
                    $year = date('Y');
                    $count = $db->table('employees')->countAllResults();
                    $nip = 'EMP-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                }

                $empData = [
                    'nip'                 => $nip,
                    'nik_ktp'             => trim($this->request->getPost('nik_ktp') ?: ''),
                    'name'                => trim($this->request->getPost('name')),
                    'department'          => $this->request->getPost('department') ?: 'Klinik',
                    'position'            => trim($this->request->getPost('position') ?: 'Staff'),
                    'salary'              => max(0, floatval($this->request->getPost('salary'))),
                    'allowance_position'  => max(0, floatval($this->request->getPost('allowance_position') ?: 0)),
                    'allowance_transport' => max(0, floatval($this->request->getPost('allowance_transport') ?: 0)),
                    'deduction_bpjs'      => max(0, floatval($this->request->getPost('deduction_bpjs') ?: 0)),
                    'phone'               => trim($this->request->getPost('phone') ?: ''),
                    'email'               => trim($this->request->getPost('email') ?: ''),
                    'address'             => trim($this->request->getPost('address') ?: ''),
                    'join_date'           => $this->request->getPost('join_date') ?: date('Y-m-d'),
                    'bank_name'           => trim($this->request->getPost('bank_name') ?: ''),
                    'bank_account'        => trim($this->request->getPost('bank_account') ?: ''),
                    'employment_type'     => $this->request->getPost('employment_type') ?: 'tetap',
                    'status'              => 'active',
                    'created_at'          => date('Y-m-d H:i:s')
                ];

                $db->table('employees')->insert($empData);
                session()->setFlashdata('success', 'Pegawai baru berhasil didaftarkan: ' . $empData['name'] . ' (' . $nip . ')');
                return redirect()->to(base_url('hrd/pegawai'));
            }

            // 2. EDIT PEGAWAI
            if ($action === 'edit_employee') {
                $id = intval($this->request->getPost('id'));

                $db->table('employees')->where('id', $id)->update([
                    'nip'                 => trim($this->request->getPost('nip')),
                    'nik_ktp'             => trim($this->request->getPost('nik_ktp') ?: ''),
                    'name'                => trim($this->request->getPost('name')),
                    'department'          => $this->request->getPost('department'),
                    'position'            => trim($this->request->getPost('position')),
                    'salary'              => max(0, floatval($this->request->getPost('salary'))),
                    'allowance_position'  => max(0, floatval($this->request->getPost('allowance_position') ?: 0)),
                    'allowance_transport' => max(0, floatval($this->request->getPost('allowance_transport') ?: 0)),
                    'deduction_bpjs'      => max(0, floatval($this->request->getPost('deduction_bpjs') ?: 0)),
                    'phone'               => trim($this->request->getPost('phone') ?: ''),
                    'email'               => trim($this->request->getPost('email') ?: ''),
                    'address'             => trim($this->request->getPost('address') ?: ''),
                    'join_date'           => $this->request->getPost('join_date'),
                    'bank_name'           => trim($this->request->getPost('bank_name') ?: ''),
                    'bank_account'        => trim($this->request->getPost('bank_account') ?: ''),
                    'employment_type'     => $this->request->getPost('employment_type') ?: 'tetap',
                    'status'              => $this->request->getPost('status') ?: 'active'
                ]);

                session()->setFlashdata('success', 'Data pegawai berhasil diperbarui.');
                return redirect()->to(base_url('hrd/pegawai'));
            }

            // 3. NONAKTIFKAN PEGAWAI
            if ($action === 'delete_employee') {
                $id = intval($this->request->getPost('id'));
                $db->table('employees')->where('id', $id)->update(['status' => 'inactive']);
                session()->setFlashdata('success', 'Status pegawai telah dinonaktifkan.');
                return redirect()->to(base_url('hrd/pegawai'));
            }
        }

        // Fetch Employees
        $employees = $db->table('employees')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();

        // Calculate Doctor Accrued Fees & details
        $doctors = $db->table('doctors')->get()->getResult();
        $doctorAccruals = [];
        $totalDoctorFees = 0;
        foreach ($doctors as $doc) {
            $totalFees = $db->table('fee_transactions')
                            ->where('doctor_id', $doc->id)
                            ->selectSum('amount')
                            ->get()
                            ->getRow();
            $fees = $totalFees->amount ?? 0.00;
            $doctorAccruals[$doc->name] = [
                'doctor_id' => $doc->id,
                'fees'      => $fees
            ];
            $totalDoctorFees += $fees;
        }

        // Fetch Payroll History
        $payrolls = $db->table('payrolls')->orderBy('period_year', 'DESC')->orderBy('period_month', 'DESC')->get()->getResult();

        // KPI Calculations
        $totalMonthlyBaseSalary = 0;
        foreach ($employees as $e) {
            $totalMonthlyBaseSalary += ($e->salary + ($e->allowance_position ?? 0) + ($e->allowance_transport ?? 0));
        }

        $data = [
            'title'                  => 'HRD & Payroll Penggajian Pegawai',
            'active_menu'            => 'hrd-payroll',
            'employees'              => $employees,
            'doctorAccruals'         => $doctorAccruals,
            'payrolls'               => $payrolls,
            'totalEmployees'         => count($employees),
            'totalMonthlyBaseSalary' => $totalMonthlyBaseSalary,
            'totalDoctorFees'        => $totalDoctorFees
        ];

        return view('hrd/pegawai', $data);
    }

    /**
     * Generate Payroll Bulanan Terintegrasi untuk Seluruh Pegawai & Dokter
     */
    public function generatePayroll()
    {
        $db = \Config\Database::connect('default');

        $month       = intval($this->request->getPost('period_month') ?: date('n'));
        $year        = intval($this->request->getPost('period_year') ?: date('Y'));
        $paymentDate = $this->request->getPost('payment_date') ?: date('Y-m-d');
        $notes       = trim($this->request->getPost('notes') ?: 'Penggajian Periode Bulan ' . $month . '/' . $year);

        // Check if payroll for this period already exists
        $existing = $db->table('payrolls')->where('period_month', $month)->where('period_year', $year)->get()->getRow();
        if ($existing) {
            session()->setFlashdata('error', 'Payroll untuk periode Bulan ' . $month . '/' . $year . ' sudah pernah digenerate (No: ' . $existing->payroll_code . ').');
            return redirect()->to(base_url('hrd/pegawai#tab-payroll'));
        }

        $db->transStart();

        $payrollCode = 'PAY-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);

        // Insert Payroll Header
        $db->table('payrolls')->insert([
            'payroll_code'    => $payrollCode,
            'period_month'    => $month,
            'period_year'     => $year,
            'payment_date'    => $paymentDate,
            'payment_method'  => 'transfer',
            'status'          => 'approved',
            'notes'           => $notes,
            'created_at'      => date('Y-m-d H:i:s')
        ]);
        $payrollId = $db->insertID();

        // Process each active employee
        $employees = $db->table('employees')->where('status', 'active')->get()->getResult();
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        foreach ($employees as $emp) {
            $baseSalary  = floatval($emp->salary);
            $allowPos    = floatval($emp->allowance_position ?? 0);
            $allowTrans  = floatval($emp->allowance_transport ?? 0);
            $deductBpjs  = floatval($emp->deduction_bpjs ?? 0);
            $doctorFee   = 0;
            $docId       = null;

            // Check if employee name matches any doctor in doctors table
            $matchedDoc = $db->table('doctors')->like('name', $emp->name)->get()->getRow();
            if ($matchedDoc) {
                $docId = $matchedDoc->id;
                $feeSum = $db->table('fee_transactions')
                             ->where('doctor_id', $docId)
                             ->selectSum('amount')
                             ->get()
                             ->getRow();
                $doctorFee = floatval($feeSum->amount ?? 0);
            }

            // Auto-Sync Attendance: Hitung Lembur dan Potongan Absensi Bulan Ini
            $attStats = $db->table('employee_attendances')
                           ->select('SUM(overtime_minutes) as total_ot, SUM(late_minutes) as total_late, SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as total_alpha')
                           ->where('employee_id', $emp->id)
                           ->where('MONTH(date)', $month)
                           ->where('YEAR(date)', $year)
                           ->get()
                           ->getRow();

            $totalOtMins   = intval($attStats->total_ot ?? 0);
            $totalLateMins = intval($attStats->total_late ?? 0);
            $totalAlpha    = intval($attStats->total_alpha ?? 0);

            // Rate Lembur: Rp 20.000 / jam
            $overtimeBonus = round(($totalOtMins / 60) * 20000);
            
            // Potongan Keterlambatan (> 30 mnt: Rp 1.000 / mnt) + Potongan Alpha (Gaji Pokok / 25 hari kerja)
            $lateDeduction = ($totalLateMins > 30) ? round(($totalLateMins - 30) * 1000) : 0;
            $dailyRate = ($baseSalary > 0) ? ($baseSalary / 25) : 0;
            $alphaDeduction = round($totalAlpha * $dailyRate);
            $deductOther = $lateDeduction + $alphaDeduction;

            $gross = $baseSalary + $allowPos + $allowTrans + $doctorFee + $overtimeBonus;
            $deductions = $deductBpjs + $deductOther;
            $net = max(0, $gross - $deductions);

            $totalGross      += $gross;
            $totalDeductions += $deductions;
            $totalNet        += $net;

            $db->table('payroll_items')->insert([
                'payroll_id'          => $payrollId,
                'employee_id'         => $emp->id,
                'doctor_id'           => $docId,
                'basic_salary'        => $baseSalary,
                'allowance_position'  => $allowPos,
                'allowance_transport' => $allowTrans,
                'overtime_bonus'      => $overtimeBonus,
                'doctor_medical_fee'  => $doctorFee,
                'deduction_bpjs'      => $deductBpjs,
                'deduction_tax'       => 0.00,
                'deduction_other'     => $deductOther,
                'net_salary'          => $net,
                'status'              => 'paid',
                'created_at'          => date('Y-m-d H:i:s')
            ]);
        }

        // Update Payroll Header Totals
        $db->table('payrolls')->where('id', $payrollId)->update([
            'total_gross'      => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net_salary' => $totalNet,
            'total_employees'  => count($employees)
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal membuat rekapitulasi payroll penggajian.');
        } else {
            session()->setFlashdata('success', 'Payroll Periode ' . $month . '/' . $year . ' berhasil digenerate untuk ' . count($employees) . ' pegawai. Total Penggajian: Rp ' . number_format($totalNet, 0, ',', '.'));
        }

        return redirect()->to(base_url('hrd/pegawai#tab-payroll'));
    }

    /**
     * Pembayaran Payroll & Otomasi Jurnal Akuntansi Beban Gaji
     */
    public function bayarPayroll()
    {
        $db = \Config\Database::connect('default');

        $payrollId = intval($this->request->getPost('payroll_id'));
        $payroll = $db->table('payrolls')->where('id', $payrollId)->get()->getRow();

        if (!$payroll) {
            session()->setFlashdata('error', 'Payroll tidak ditemukan.');
            return redirect()->to(base_url('hrd/pegawai#tab-payroll'));
        }

        $db->transStart();

        $db->table('payrolls')->where('id', $payrollId)->update(['status' => 'paid']);
        $db->table('payroll_items')->where('payroll_id', $payrollId)->update(['status' => 'paid']);

        // Auto-Journal for Payroll:
        // Debit: 6-101 (Beban Gaji Karyawan) & 6-102 (Beban Jasa Medis Dokter)
        // Credit: 1-102 (Bank BCA Operasional) atau 1-101 (Kas)
        $salaryAcc  = $db->table('accounts')->where('code', '6-101')->get()->getRow();
        $bankAcc    = $db->table('accounts')->where('code', '1-102')->get()->getRow();

        $salaryAccId = $salaryAcc ? $salaryAcc->id : 13;
        $bankAccId   = $bankAcc ? $bankAcc->id : 2;

        $today = date('Ymd');
        $lastJournal = $db->table('journal_entries')
                          ->where('DATE(created_at)', date('Y-m-d'))
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->get()
                          ->getRow();
        $nextJNum = 1;
        if ($lastJournal && preg_match('/JV-\d+-(\d+)/', $lastJournal->journal_no, $matches)) {
            $nextJNum = intval($matches[1]) + 1;
        }
        $journalNo = 'JV-' . $today . '-' . str_pad($nextJNum, 4, '0', STR_PAD_LEFT);

        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => $payroll->payment_date,
            'source_module' => 'HRD Payroll',
            'reference_id'  => $payrollId,
            'description'   => "Pembayaran Gaji Pegawai & Dokter - " . $payroll->payroll_code . " (Periode " . $payroll->period_month . "/" . $payroll->period_year . ")",
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $jId = $db->insertID();

        // Debit Beban Gaji
        $db->table('journal_entry_details')->insert([
            'journal_id' => $jId,
            'account_id' => $salaryAccId,
            'debit'      => $payroll->total_net_salary,
            'credit'     => 0.00
        ]);
        if ($salaryAcc) {
            $db->table('accounts')->where('id', $salaryAccId)->update(['balance' => $salaryAcc->balance + $payroll->total_net_salary]);
        }

        // Credit Bank BCA
        $db->table('journal_entry_details')->insert([
            'journal_id' => $jId,
            'account_id' => $bankAccId,
            'debit'      => 0.00,
            'credit'     => $payroll->total_net_salary
        ]);
        if ($bankAcc) {
            $db->table('accounts')->where('id', $bankAccId)->update(['balance' => $bankAcc->balance - $payroll->total_net_salary]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses pembayaran payroll.');
        } else {
            session()->setFlashdata('success', 'Payroll ' . $payroll->payroll_code . ' berhasil dibayarkan dan jurnal beban gaji sebesar Rp ' . number_format($payroll->total_net_salary, 0, ',', '.') . ' berhasil dibukukan.');
        }

        return redirect()->to(base_url('hrd/pegawai#tab-payroll'));
    }

    /**
     * Cetak Slip Gaji Individu Pegawai / Dokter (Paper Document Sheet)
     */
    public function cetakSlipGaji($itemId)
    {
        $db = \Config\Database::connect('default');

        $item = $db->table('payroll_items')
                   ->select('payroll_items.*, employees.name as employee_name, employees.nip, employees.position, employees.department,
                             employees.bank_name, employees.bank_account, employees.employment_type,
                             payrolls.payroll_code, payrolls.period_month, payrolls.period_year, payrolls.payment_date')
                   ->join('employees', 'employees.id = payroll_items.employee_id')
                   ->join('payrolls', 'payrolls.id = payroll_items.payroll_id')
                   ->where('payroll_items.id', $itemId)
                   ->get()
                   ->getRow();

        if (!$item) {
            session()->setFlashdata('error', 'Data slip gaji tidak ditemukan.');
            return redirect()->to(base_url('hrd/pegawai'));
        }

        $data = [
            'title' => 'Slip Gaji ' . $item->employee_name . ' - Periode ' . $item->period_month . '/' . $item->period_year,
            'slip'  => $item
        ];

        return view('hrd/cetak_slip_gaji', $data);
    }

    /**
     * Cetak Rekapitulasi Laporan Payroll Bulanan
     */
    public function cetakRekapPayroll($payrollId)
    {
        $db = \Config\Database::connect('default');

        $payroll = $db->table('payrolls')->where('id', $payrollId)->get()->getRow();
        if (!$payroll) {
            session()->setFlashdata('error', 'Payroll tidak ditemukan.');
            return redirect()->to(base_url('hrd/pegawai'));
        }

        $items = $db->table('payroll_items')
                    ->select('payroll_items.*, employees.name as employee_name, employees.nip, employees.position, employees.department, employees.bank_name, employees.bank_account')
                    ->join('employees', 'employees.id = payroll_items.employee_id')
                    ->where('payroll_items.payroll_id', $payrollId)
                    ->orderBy('employees.name', 'ASC')
                    ->get()
                    ->getResult();

        $data = [
            'title'   => 'Rekapitulasi Penggajian Payroll ' . $payroll->payroll_code,
            'payroll' => $payroll,
            'items'   => $items
        ];

        return view('hrd/cetak_rekap_payroll', $data);
    }

    /**
     * AJAX JSON: Data Pegawai untuk Modal Edit
     */
    public function getPegawaiJson($id)
    {
        $db = \Config\Database::connect('default');
        $emp = $db->table('employees')->where('id', $id)->get()->getRow();
        if (!$emp) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pegawai tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $emp]);
    }

    /**
     * Dashboard Absensi, Presensi Harian, Shift Kerja & Pengajuan Cuti
     */
    public function absensi()
    {
        $db = \Config\Database::connect('default');

        $filterDate = $this->request->getGet('date') ?: date('Y-m-d');
        $filterDept = $this->request->getGet('department') ?: '';
        $filterMonth = intval($this->request->getGet('month') ?: date('n'));
        $filterYear = intval($this->request->getGet('year') ?: date('Y'));

        // 1. Fetch Today's Attendances
        $attBuilder = $db->table('employee_attendances')
                         ->select('employee_attendances.*, employees.name as employee_name, employees.nip, employees.department, employees.position,
                                   work_shifts.shift_name, work_shifts.start_time, work_shifts.end_time')
                         ->join('employees', 'employees.id = employee_attendances.employee_id')
                         ->join('work_shifts', 'work_shifts.id = employee_attendances.shift_id', 'left')
                         ->where('employee_attendances.date', $filterDate);

        if (!empty($filterDept)) {
            $attBuilder->where('employees.department', $filterDept);
        }

        $attendances = $attBuilder->orderBy('employee_attendances.check_in_time', 'ASC')->get()->getResult();

        // 2. Fetch Active Employees & Shifts
        $employees = $db->table('employees')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $shifts    = $db->table('work_shifts')->where('status', 'active')->orderBy('start_time', 'ASC')->get()->getResult();

        // 3. Fetch Leaves & Permits
        $leaves = $db->table('employee_leaves')
                     ->select('employee_leaves.*, employees.name as employee_name, employees.nip, employees.department')
                     ->join('employees', 'employees.id = employee_leaves.employee_id')
                     ->orderBy('employee_leaves.created_at', 'DESC')
                     ->get()
                     ->getResult();

        // 4. Monthly Attendance Recapitulation
        $monthlyRecap = $db->table('employee_attendances')
                           ->select('employees.id as employee_id, employees.name as employee_name, employees.nip, employees.department,
                                     SUM(CASE WHEN employee_attendances.status = "present" THEN 1 ELSE 0 END) as total_present,
                                     SUM(CASE WHEN employee_attendances.status = "late" THEN 1 ELSE 0 END) as total_late,
                                     SUM(CASE WHEN employee_attendances.status = "sick" THEN 1 ELSE 0 END) as total_sick,
                                     SUM(CASE WHEN employee_attendances.status = "permit" THEN 1 ELSE 0 END) as total_permit,
                                     SUM(CASE WHEN employee_attendances.status = "leave" THEN 1 ELSE 0 END) as total_leave,
                                     SUM(CASE WHEN employee_attendances.status = "alpha" THEN 1 ELSE 0 END) as total_alpha,
                                     SUM(employee_attendances.late_minutes) as total_late_minutes')
                           ->join('employees', 'employees.id = employee_attendances.employee_id')
                           ->where('MONTH(employee_attendances.date)', $filterMonth)
                           ->where('YEAR(employee_attendances.date)', $filterYear)
                           ->groupBy('employees.id')
                           ->orderBy('employees.name', 'ASC')
                           ->get()
                           ->getResult();

        // KPI Calculations for Filter Date
        $totalPresentToday = 0;
        $totalLateToday    = 0;
        $totalLeaveToday   = 0;

        foreach ($attendances as $a) {
            if ($a->status === 'present') $totalPresentToday++;
            if ($a->status === 'late') {
                $totalPresentToday++;
                $totalLateToday++;
            }
            if (in_array($a->status, ['sick', 'permit', 'leave'])) $totalLeaveToday++;
        }

        $totalActiveEmployees = count($employees);
        $totalNotYetCheckedIn = max(0, $totalActiveEmployees - count($attendances));

        $data = [
            'title'                 => 'Absensi & Presensi Karyawan Klinik',
            'active_menu'           => 'hrd-absensi',
            'filterDate'            => $filterDate,
            'filterDept'            => $filterDept,
            'filterMonth'           => $filterMonth,
            'filterYear'            => $filterYear,
            'attendances'           => $attendances,
            'employees'             => $employees,
            'shifts'                => $shifts,
            'leaves'                => $leaves,
            'monthlyRecap'          => $monthlyRecap,
            'totalPresentToday'     => $totalPresentToday,
            'totalLateToday'        => $totalLateToday,
            'totalLeaveToday'       => $totalLeaveToday,
            'totalNotYetCheckedIn'  => $totalNotYetCheckedIn,
            'totalActiveEmployees'  => $totalActiveEmployees
        ];

        return view('hrd/absensi', $data);
    }

    /**
     * Check-In Presensi Pegawai (Kiosk / Terminal Absensi)
     */
    public function checkIn()
    {
        $db = \Config\Database::connect('default');

        $employeeId = intval($this->request->getPost('employee_id'));
        $shiftId    = intval($this->request->getPost('shift_id'));
        $date       = $this->request->getPost('date') ?: date('Y-m-d');
        $time       = $this->request->getPost('check_in_time') ?: date('H:i:s');
        $notes      = trim($this->request->getPost('notes') ?: '');

        $emp = $db->table('employees')->where('id', $employeeId)->get()->getRow();
        if (!$emp) {
            session()->setFlashdata('error', 'Pegawai tidak ditemukan.');
            return redirect()->to(base_url('hrd/absensi'));
        }

        // Check if already checked in today
        $existing = $db->table('employee_attendances')
                       ->where('employee_id', $employeeId)
                       ->where('date', $date)
                       ->get()
                       ->getRow();

        if ($existing && !empty($existing->check_in_time)) {
            session()->setFlashdata('error', 'Pegawai ' . $emp->name . ' sudah melakukan Check-In hari ini pukul ' . $existing->check_in_time . '.');
            return redirect()->to(base_url('hrd/absensi'));
        }

        // Determine Shift & Calculate Lateness
        $shift = $db->table('work_shifts')->where('id', $shiftId)->get()->getRow();
        $status = 'present';
        $lateMinutes = 0;

        if ($shift) {
            $shiftStart = strtotime($date . ' ' . $shift->start_time);
            $checkInTimestamp = strtotime($date . ' ' . $time);
            $toleranceSeconds = ($shift->late_tolerance_minutes ?: 15) * 60;

            if ($checkInTimestamp > ($shiftStart + $toleranceSeconds)) {
                $status = 'late';
                $lateMinutes = ceil(($checkInTimestamp - $shiftStart) / 60);
            }
        }

        $attData = [
            'employee_id'    => $employeeId,
            'shift_id'       => $shiftId ?: null,
            'date'           => $date,
            'check_in_time'  => $time,
            'status'         => $status,
            'late_minutes'   => $lateMinutes,
            'notes'          => $notes,
            'ip_address'     => $this->request->getIPAddress(),
            'device_info'    => $this->request->getUserAgent()->getBrowser() . ' ' . $this->request->getUserAgent()->getPlatform(),
            'created_at'     => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $db->table('employee_attendances')->where('id', $existing->id)->update($attData);
        } else {
            $db->table('employee_attendances')->insert($attData);
        }

        $lateMsg = ($status === 'late') ? ' (Terlambat ' . $lateMinutes . ' menit)' : ' (Tepat Waktu)';
        session()->setFlashdata('success', 'Check-In Berhasil: ' . $emp->name . ' pukul ' . $time . $lateMsg . '.');
        return redirect()->to(base_url('hrd/absensi'));
    }

    /**
     * Check-Out Presensi Pegawai
     */
    public function checkOut()
    {
        $db = \Config\Database::connect('default');

        $employeeId = intval($this->request->getPost('employee_id'));
        $date       = $this->request->getPost('date') ?: date('Y-m-d');
        $time       = $this->request->getPost('check_out_time') ?: date('H:i:s');

        $emp = $db->table('employees')->where('id', $employeeId)->get()->getRow();
        if (!$emp) {
            session()->setFlashdata('error', 'Pegawai tidak ditemukan.');
            return redirect()->to(base_url('hrd/absensi'));
        }

        $existing = $db->table('employee_attendances')
                       ->where('employee_id', $employeeId)
                       ->where('date', $date)
                       ->get()
                       ->getRow();

        if (!$existing) {
            session()->setFlashdata('error', 'Pegawai belum melakukan Check-In untuk tanggal ' . $date . '.');
            return redirect()->to(base_url('hrd/absensi'));
        }

        // Calculate Overtime if any
        $overtimeMinutes = 0;
        if ($existing->shift_id) {
            $shift = $db->table('work_shifts')->where('id', $existing->shift_id)->get()->getRow();
            if ($shift) {
                $shiftEnd = strtotime($date . ' ' . $shift->end_time);
                $checkOutTimestamp = strtotime($date . ' ' . $time);
                if ($checkOutTimestamp > $shiftEnd) {
                    $overtimeMinutes = floor(($checkOutTimestamp - $shiftEnd) / 60);
                }
            }
        }

        $db->table('employee_attendances')->where('id', $existing->id)->update([
            'check_out_time'   => $time,
            'overtime_minutes' => $overtimeMinutes
        ]);

        $overtimeMsg = ($overtimeMinutes > 0) ? ' (Lembur ' . $overtimeMinutes . ' menit)' : '';
        session()->setFlashdata('success', 'Check-Out Berhasil: ' . $emp->name . ' pukul ' . $time . $overtimeMsg . '.');
        return redirect()->to(base_url('hrd/absensi'));
    }

    /**
     * Ajukan Cuti / Izin / Sakit Pegawai
     */
    public function ajukanCuti()
    {
        $db = \Config\Database::connect('default');

        $employeeId = intval($this->request->getPost('employee_id'));
        $leaveType  = $this->request->getPost('leave_type') ?: 'izin';
        $startDate  = $this->request->getPost('start_date');
        $endDate    = $this->request->getPost('end_date');
        $reason     = trim($this->request->getPost('reason') ?: '');

        $start = strtotime($startDate);
        $end   = strtotime($endDate);
        $totalDays = max(1, round(($end - $start) / (60 * 60 * 24)) + 1);

        $db->table('employee_leaves')->insert([
            'employee_id' => $employeeId,
            'leave_type'  => $leaveType,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'total_days'  => $totalDays,
            'reason'      => $reason,
            'status'      => 'pending',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Pengajuan cuti/izin (' . $totalDays . ' hari) berhasil disimpan dan menunggu verifikasi.');
        return redirect()->to(base_url('hrd/absensi#tab-cuti'));
    }

    /**
     * Verifikasi & Approval Cuti / Izin Pegawai
     */
    public function approvalCuti()
    {
        $db = \Config\Database::connect('default');

        $leaveId = intval($this->request->getPost('leave_id'));
        $action  = $this->request->getPost('action'); // approve / reject
        $notes   = trim($this->request->getPost('approval_notes') ?: '');

        $leave = $db->table('employee_leaves')->where('id', $leaveId)->get()->getRow();
        if (!$leave) {
            session()->setFlashdata('error', 'Pengajuan cuti tidak ditemukan.');
            return redirect()->to(base_url('hrd/absensi#tab-cuti'));
        }

        $db->transStart();

        $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
        $db->table('employee_leaves')->where('id', $leaveId)->update([
            'status'         => $newStatus,
            'approved_by'    => session('user_id') ?: 1,
            'approval_notes' => $notes
        ]);

        // If approved, populate employee_attendances table with leave records for each day in range
        if ($newStatus === 'approved') {
            $current = strtotime($leave->start_date);
            $end     = strtotime($leave->end_date);

            $attStatusMap = [
                'cuti_tahunan'    => 'leave',
                'sakit'           => 'sick',
                'izin'            => 'permit',
                'cuti_melahirkan' => 'leave',
                'dinas_luar'      => 'present'
            ];
            $attStatus = $attStatusMap[$leave->leave_type] ?? 'leave';

            while ($current <= $end) {
                $curDate = date('Y-m-d', $current);
                $existingAtt = $db->table('employee_attendances')
                                  ->where('employee_id', $leave->employee_id)
                                  ->where('date', $curDate)
                                  ->get()
                                  ->getRow();

                if (!$existingAtt) {
                    $db->table('employee_attendances')->insert([
                        'employee_id' => $leave->employee_id,
                        'date'        => $curDate,
                        'status'      => $attStatus,
                        'notes'       => 'Pengajuan Cuti: ' . $leave->reason,
                        'created_at'  => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $db->table('employee_attendances')->where('id', $existingAtt->id)->update([
                        'status' => $attStatus,
                        'notes'  => 'Pengajuan Cuti: ' . $leave->reason
                    ]);
                }
                $current = strtotime('+1 day', $current);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses approval cuti.');
        } else {
            session()->setFlashdata('success', 'Status pengajuan cuti berhasil diubah menjadi: ' . strtoupper($newStatus));
        }

        return redirect()->to(base_url('hrd/absensi#tab-cuti'));
    }

    /**
     * Master Shift Kerja Klinik (Tambah / Edit)
     */
    public function manageShift()
    {
        $db = \Config\Database::connect('default');

        $shiftId   = intval($this->request->getPost('shift_id'));
        $shiftCode = trim($this->request->getPost('shift_code'));
        $shiftName = trim($this->request->getPost('shift_name'));
        $startTime = $this->request->getPost('start_time');
        $endTime   = $this->request->getPost('end_time');
        $tolerance = intval($this->request->getPost('late_tolerance_minutes') ?: 15);

        if ($shiftId > 0) {
            $db->table('work_shifts')->where('id', $shiftId)->update([
                'shift_code'             => $shiftCode,
                'shift_name'             => $shiftName,
                'start_time'             => $startTime,
                'end_time'               => $endTime,
                'late_tolerance_minutes' => $tolerance
            ]);
            session()->setFlashdata('success', 'Shift kerja berhasil diperbarui.');
        } else {
            $db->table('work_shifts')->insert([
                'shift_code'             => $shiftCode,
                'shift_name'             => $shiftName,
                'start_time'             => $startTime,
                'end_time'               => $endTime,
                'late_tolerance_minutes' => $tolerance,
                'status'                 => 'active',
                'created_at'             => date('Y-m-d H:i:s')
            ]);
            session()->setFlashdata('success', 'Shift kerja baru berhasil ditambahkan.');
        }

        return redirect()->to(base_url('hrd/absensi#tab-shift'));
    }

    /**
     * Cetak Laporan Rekapitulasi Presensi Bulanan Pegawai (Paper Document Sheet)
     */
    public function cetakRekapAbsensi()
    {
        $db = \Config\Database::connect('default');

        $month = intval($this->request->getGet('month') ?: date('n'));
        $year  = intval($this->request->getGet('year') ?: date('Y'));

        $recap = $db->table('employee_attendances')
                    ->select('employees.id as employee_id, employees.name as employee_name, employees.nip, employees.department, employees.position,
                              SUM(CASE WHEN employee_attendances.status = "present" THEN 1 ELSE 0 END) as total_present,
                              SUM(CASE WHEN employee_attendances.status = "late" THEN 1 ELSE 0 END) as total_late,
                              SUM(CASE WHEN employee_attendances.status = "sick" THEN 1 ELSE 0 END) as total_sick,
                              SUM(CASE WHEN employee_attendances.status = "permit" THEN 1 ELSE 0 END) as total_permit,
                              SUM(CASE WHEN employee_attendances.status = "leave" THEN 1 ELSE 0 END) as total_leave,
                              SUM(CASE WHEN employee_attendances.status = "alpha" THEN 1 ELSE 0 END) as total_alpha,
                              SUM(employee_attendances.late_minutes) as total_late_minutes,
                              SUM(employee_attendances.overtime_minutes) as total_overtime_minutes')
                    ->join('employees', 'employees.id = employee_attendances.employee_id')
                    ->where('MONTH(employee_attendances.date)', $month)
                    ->where('YEAR(employee_attendances.date)', $year)
                    ->groupBy('employees.id')
                    ->orderBy('employees.department', 'ASC')
                    ->orderBy('employees.name', 'ASC')
                    ->get()
                    ->getResult();

        $data = [
            'title' => 'Laporan Rekapitulasi Presensi Pegawai Periode ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $year,
            'month' => $month,
            'year'  => $year,
            'recap' => $recap
        ];

        return view('hrd/cetak_rekap_absensi', $data);
    }

    // =========================================================================
    // MODUL EVALUASI KPI & PRODUKTIVITAS KARYAWAN
    // =========================================================================
    public function kpi()
    {
        $db = \Config\Database::connect('default');
        $selectedMonth = $this->request->getGet('month') ?: date('Y-m');

        // 1. Doctor KPI: Patients examined, prescriptions issued, estimated fee
        $doctorKpi = $db->table('doctors')
                        ->select('doctors.id, doctors.name, 
                                  COALESCE(polikliniks.name, polyclinics.name, "Poli Umum") as poly_name, 
                                  COALESCE(doctors.fee_per_pasien, 0) as fee_per_pasien,
                                  COUNT(DISTINCT patient_visits.id) as total_patients,
                                  COUNT(DISTINCT prescriptions.id) as total_prescriptions,
                                  (COUNT(DISTINCT patient_visits.id) * COALESCE(doctors.fee_per_pasien, 0)) as total_fee')
                        ->join('polikliniks', 'polikliniks.id = doctors.polyclinic_id', 'left')
                        ->join('polyclinics', 'polyclinics.id = doctors.polyclinic_id', 'left')
                        ->join('patient_visits', 'patient_visits.doctor_id = doctors.id AND DATE_FORMAT(patient_visits.visit_date, "%Y-%m") = "' . $selectedMonth . '"', 'left')
                        ->join('prescriptions', 'prescriptions.doctor_id = doctors.id AND DATE_FORMAT(prescriptions.created_at, "%Y-%m") = "' . $selectedMonth . '"', 'left')
                        ->where('doctors.status', 'active')
                        ->groupBy('doctors.id')
                        ->orderBy('total_patients', 'DESC')
                        ->get()->getResult();

        // 2. Employee Attendance & KPI
        $employeeKpi = $db->table('employees')
                          ->select('employees.id, employees.name, employees.nip as nik, employees.position, employees.department,
                                    SUM(CASE WHEN employee_attendances.status = "present" THEN 1 ELSE 0 END) as hadir_count,
                                    SUM(CASE WHEN employee_attendances.status = "late" THEN 1 ELSE 0 END) as late_count,
                                    SUM(CASE WHEN employee_attendances.status IN ("leave", "permit", "sick") THEN 1 ELSE 0 END) as leave_count,
                                    COALESCE(SUM(TIMESTAMPDIFF(MINUTE, employee_attendances.check_in_time, employee_attendances.check_out_time) / 60), 0) as total_work_hours')
                          ->join('employee_attendances', 'employee_attendances.employee_id = employees.id AND DATE_FORMAT(employee_attendances.date, "%Y-%m") = "' . $selectedMonth . '"', 'left')
                          ->where('employees.status', 'active')
                          ->groupBy('employees.id')
                          ->orderBy('employees.name', 'ASC')
                          ->get()->getResult();

        $data = [
            'title'         => 'Matriks KPI & Evaluasi Kinerja Karyawan',
            'active_menu'   => 'hrd-kpi',
            'selectedMonth' => $selectedMonth,
            'doctorKpi'     => $doctorKpi,
            'employeeKpi'   => $employeeKpi
        ];

        return view('hrd/kpi', $data);
    }
}

