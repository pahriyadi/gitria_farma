<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-calendar-check text-teal mr-2"></i> Absensi & Presensi Karyawan Klinik
                </h1>
                <small class="text-muted">Terminal presensi harian, pemantauan keterlambatan/lembur, manajemen shift klinik, dan pengajuan cuti/izin</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalCheckIn">
                    <i class="fas fa-fingerprint mr-1"></i> Check-In / Presensi
                </button>
                <button class="btn btn-warning btn-sm font-weight-bold shadow-sm text-dark mr-1" data-toggle="modal" data-target="#modalLeave">
                    <i class="fas fa-file-signature mr-1"></i> Ajukan Cuti / Izin
                </button>
                <a href="<?= base_url('hrd/cetak-rekap-absensi?month=' . $filterMonth . '&year=' . $filterYear) ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak Rekap Presensi
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- 4 KPI SUMMARY CARDS -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Hadir Hari Ini</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalPresentToday ?> <small class="text-muted">/ <?= $totalActiveEmployees ?> Staf</small></h4>
                                <small class="text-muted"><?= date('d F Y', strtotime($filterDate)) ?></small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #f59e0b !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-warning text-uppercase">Terlambat Masuk</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalLateToday ?> <small class="text-muted">Pegawai</small></h4>
                                <small class="text-muted">Melewati toleransi shift</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-warning">
                                <i class="fas fa-user-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0284c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-info text-uppercase">Cuti / Izin / Sakit</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalLeaveToday ?> <small class="text-muted">Pegawai</small></h4>
                                <small class="text-muted">Izin terverifikasi</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-hospital-user fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #ef4444 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-danger text-uppercase">Belum Check-In</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalNotYetCheckedIn ?> <small class="text-muted">Pegawai</small></h4>
                                <small class="text-muted">Menunggu kehadiran</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-danger">
                                <i class="fas fa-user-xmark fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="attendance-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-daily-link" data-toggle="pill" href="#tab-daily" role="tab">
                            <i class="fas fa-clock text-teal mr-1"></i> 1. PRESENSI HARIAN
                            <span class="badge badge-teal ml-2"><?= count($attendances) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-recap-link" data-toggle="pill" href="#tab-recap" role="tab">
                            <i class="fas fa-chart-column text-info mr-1"></i> 2. REKAPITULASI BULANAN
                            <span class="badge badge-info ml-2"><?= count($monthlyRecap) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-cuti-link" data-toggle="pill" href="#tab-cuti" role="tab">
                            <i class="fas fa-file-signature text-warning mr-1"></i> 3. PENGAJUAN & APPROVAL CUTI
                            <span class="badge badge-warning ml-2"><?= count($leaves) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-shift-link" data-toggle="pill" href="#tab-shift" role="tab">
                            <i class="fas fa-business-time text-purple mr-1"></i> 4. MASTER SHIFT KERJA
                            <span class="badge badge-secondary ml-2"><?= count($shifts) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="attendance-tabsContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: PRESENSI HARIAN -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-daily" role="tabpanel">
                        <!-- Date Filter Toolbar -->
                        <form method="get" action="<?= base_url('hrd/absensi') ?>" class="mb-3 bg-light p-2 rounded border">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-white"><i class="fas fa-calendar mr-1"></i> Tanggal:</span>
                                        </div>
                                        <input type="date" name="date" class="form-control" value="<?= esc($filterDate) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-white">Departemen:</span>
                                        </div>
                                        <select name="department" class="form-control">
                                            <option value="">-- Semua Departemen --</option>
                                            <option value="Klinik" <?= $filterDept === 'Klinik' ? 'selected' : '' ?>>Klinik Medis</option>
                                            <option value="Apotek" <?= $filterDept === 'Apotek' ? 'selected' : '' ?>>Apotek Farmasi</option>
                                            <option value="Restoran" <?= $filterDept === 'Restoran' ? 'selected' : '' ?>>POS Restoran</option>
                                            <option value="Keuangan" <?= $filterDept === 'Keuangan' ? 'selected' : '' ?>>Keuangan & Kasir</option>
                                            <option value="Sarpras" <?= $filterDept === 'Sarpras' ? 'selected' : '' ?>>Umum & IT</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold">
                                        <i class="fas fa-filter mr-1"></i> Filter Presensi
                                    </button>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modalCheckIn">
                                        <i class="fas fa-fingerprint mr-1"></i> Check-In Karyawan
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 120px;" class="text-center">NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Departemen & Jabatan</th>
                                        <th>Shift Kerja</th>
                                        <th class="text-center">Jam Masuk</th>
                                        <th class="text-center">Jam Keluar</th>
                                        <th class="text-center">Status</th>
                                        <th>Keterlambatan / Lembur</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendances as $att): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($att->nip) ?></td>
                                            <td><strong class="text-dark"><?= esc($att->employee_name) ?></strong></td>
                                            <td>
                                                <span class="badge badge-light border font-weight-bold"><?= esc($att->department) ?></span>
                                                <small class="d-block text-muted"><?= esc($att->position ?: 'Staff') ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-secondary"><?= esc($att->shift_name ?: 'Non-Shift') ?></strong>
                                                <?php if (!empty($att->start_time)): ?>
                                                    <small class="d-block text-muted">(<?= substr($att->start_time, 0, 5) ?> - <?= substr($att->end_time, 0, 5) ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center font-weight-bold <?= $att->status === 'late' ? 'text-warning' : 'text-success' ?>">
                                                <?= $att->check_in_time ? date('H:i:s', strtotime($att->check_in_time)) : '-' ?>
                                            </td>
                                            <td class="text-center font-weight-bold text-dark">
                                                <?= $att->check_out_time ? date('H:i:s', strtotime($att->check_out_time)) : '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                $statusMap = [
                                                    'present' => ['badge' => 'success', 'label' => 'HADIR'],
                                                    'late'    => ['badge' => 'warning text-dark', 'label' => 'TERLAMBAT'],
                                                    'sick'    => ['badge' => 'info', 'label' => 'SAKIT'],
                                                    'permit'  => ['badge' => 'primary', 'label' => 'IZIN'],
                                                    'leave'   => ['badge' => 'secondary', 'label' => 'CUTI'],
                                                    'alpha'   => ['badge' => 'danger', 'label' => 'ALPHA']
                                                ];
                                                $st = $statusMap[$att->status] ?? ['badge' => 'light', 'label' => strtoupper($att->status)];
                                                ?>
                                                <span class="badge badge-<?= $st['badge'] ?> px-2 py-1"><?= $st['label'] ?></span>
                                            </td>
                                            <td>
                                                <?php if ($att->late_minutes > 0): ?>
                                                    <small class="text-danger font-weight-bold d-block"><i class="fas fa-exclamation-triangle mr-1"></i> Terlambat <?= $att->late_minutes ?> mnt</small>
                                                <?php endif; ?>
                                                <?php if ($att->overtime_minutes > 0): ?>
                                                    <small class="text-success font-weight-bold d-block"><i class="fas fa-plus-circle mr-1"></i> Lembur <?= $att->overtime_minutes ?> mnt</small>
                                                <?php endif; ?>
                                                <?php if ($att->late_minutes == 0 && $att->overtime_minutes == 0): ?>
                                                    <small class="text-muted"><?= esc($att->notes ?: 'Tepat Waktu') ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (empty($att->check_out_time)): ?>
                                                    <button class="btn btn-outline-danger btn-xs font-weight-bold btn-checkout-trigger"
                                                            data-id="<?= $att->employee_id ?>"
                                                            data-name="<?= esc($att->employee_name) ?>"
                                                            title="Check-Out Pulang">
                                                        <i class="fas fa-right-from-bracket"></i> Out
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge badge-success text-xs"><i class="fas fa-check"></i> Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: REKAPITULASI BULANAN -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-recap" role="tabpanel">
                        <form method="get" action="<?= base_url('hrd/absensi#tab-recap') ?>" class="mb-3 bg-light p-2 rounded border">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-white">Bulan:</span>
                                        </div>
                                        <select name="month" class="form-control font-weight-bold">
                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?= $m ?>" <?= $filterMonth == $m ? 'selected' : '' ?>>
                                                    <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold bg-white">Tahun:</span>
                                        </div>
                                        <input type="number" name="year" class="form-control font-weight-bold" value="<?= esc($filterYear) ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info btn-sm btn-block font-weight-bold">
                                        <i class="fas fa-search mr-1"></i> Tampilkan Rekap
                                    </button>
                                </div>
                                <div class="col-md-5 text-right">
                                    <a href="<?= base_url('hrd/cetak-rekap-absensi?month=' . $filterMonth . '&year=' . $filterYear) ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                                        <i class="fas fa-print mr-1"></i> Cetak Rekapitulasi Presensi
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 120px;" class="text-center">NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Departemen</th>
                                        <th class="text-center text-success">Hadir</th>
                                        <th class="text-center text-warning">Terlambat</th>
                                        <th class="text-center text-info">Sakit</th>
                                        <th class="text-center text-primary">Izin</th>
                                        <th class="text-center text-secondary">Cuti</th>
                                        <th class="text-center text-danger">Alpha</th>
                                        <th class="text-right">Total Telat (Mnt)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monthlyRecap as $rec): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($rec->nip) ?></td>
                                            <td><strong><?= esc($rec->employee_name) ?></strong></td>
                                            <td><span class="badge badge-light border"><?= esc($rec->department) ?></span></td>
                                            <td class="text-center font-weight-bold text-success"><?= $rec->total_present ?></td>
                                            <td class="text-center font-weight-bold text-warning"><?= $rec->total_late ?></td>
                                            <td class="text-center font-weight-bold text-info"><?= $rec->total_sick ?></td>
                                            <td class="text-center font-weight-bold text-primary"><?= $rec->total_permit ?></td>
                                            <td class="text-center font-weight-bold text-secondary"><?= $rec->total_leave ?></td>
                                            <td class="text-center font-weight-bold text-danger"><?= $rec->total_alpha ?></td>
                                            <td class="text-right font-weight-bold text-dark"><?= number_format($rec->total_late_minutes) ?> mnt</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: PENGAJUAN & APPROVAL CUTI -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-cuti" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Pengajuan Cuti, Izin & Surat Dokter Karyawan</h6>
                                <small class="text-muted">Verifikasi permohonan ketidakhadiran kerja yang akan otomatis tercatat ke log presensi</small>
                            </div>
                            <button class="btn btn-warning btn-sm font-weight-bold shadow-sm text-dark" data-toggle="modal" data-target="#modalLeave">
                                <i class="fas fa-plus mr-1"></i> Form Pengajuan Cuti Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Pegawai</th>
                                        <th>Jenis Cuti / Izin</th>
                                        <th>Tanggal Mulai & Selesai</th>
                                        <th class="text-center">Durasi</th>
                                        <th>Alasan / Keterangan</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 140px;" class="text-center">Aksi Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaves as $lv): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-dark"><?= esc($lv->employee_name) ?></strong>
                                                <small class="d-block text-muted"><?= esc($lv->nip) ?> - <?= esc($lv->department) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info text-uppercase font-weight-bold">
                                                    <?= str_replace('_', ' ', esc($lv->leave_type)) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= date('d/m/Y', strtotime($lv->start_date)) ?></strong> s/d <strong><?= date('d/m/Y', strtotime($lv->end_date)) ?></strong>
                                            </td>
                                            <td class="text-center font-weight-bold"><?= $lv->total_days ?> Hari</td>
                                            <td><?= esc($lv->reason) ?></td>
                                            <td class="text-center">
                                                <?php 
                                                $lvBadge = [
                                                    'pending'  => 'warning text-dark',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ][$lv->status] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $lvBadge ?> text-uppercase px-2 py-1">
                                                    <?= strtoupper(esc($lv->status)) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($lv->status === 'pending'): ?>
                                                    <form action="<?= base_url('hrd/approval-cuti') ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="leave_id" value="<?= $lv->id ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-success btn-xs font-weight-bold mr-1" title="Setujui Cuti">
                                                            <i class="fas fa-check"></i> Setuju
                                                        </button>
                                                    </form>
                                                    <form action="<?= base_url('hrd/approval-cuti') ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="leave_id" value="<?= $lv->id ?>">
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn btn-danger btn-xs font-weight-bold" title="Tolak Cuti">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <small class="text-muted"><?= esc($lv->approval_notes ?: 'Telah diverifikasi') ?></small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 4: MASTER SHIFT KERJA -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-shift" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Master Jadwal & Shift Kerja Klinik</h6>
                                <small class="text-muted">Pengaturan jam masuk, jam pulang, dan toleransi keterlambatan pelayanan poli & apotek</small>
                            </div>
                            <button class="btn btn-purple btn-sm font-weight-bold shadow-sm text-white" style="background:#8b5cf6;" data-toggle="modal" data-target="#modalShift">
                                <i class="fas fa-plus mr-1"></i> Tambah Shift Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">Kode Shift</th>
                                        <th>Nama Shift</th>
                                        <th class="text-center">Jam Mulai Masuk</th>
                                        <th class="text-center">Jam Selesai Pulang</th>
                                        <th class="text-center">Toleransi Keterlambatan</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($shifts as $sh): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($sh->shift_code) ?></td>
                                            <td><strong><?= esc($sh->shift_name) ?></strong></td>
                                            <td class="text-center font-weight-bold text-success"><?= substr($sh->start_time, 0, 5) ?> WITA</td>
                                            <td class="text-center font-weight-bold text-danger"><?= substr($sh->end_time, 0, 5) ?> WITA</td>
                                            <td class="text-center font-weight-bold"><?= $sh->late_tolerance_minutes ?> Menit</td>
                                            <td class="text-center">
                                                <span class="badge badge-success text-uppercase px-2 py-1"><?= strtoupper(esc($sh->status)) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-outline-info btn-xs btn-edit-shift"
                                                        data-id="<?= $sh->id ?>"
                                                        data-code="<?= esc($sh->shift_code) ?>"
                                                        data-name="<?= esc($sh->shift_name) ?>"
                                                        data-start="<?= esc($sh->start_time) ?>"
                                                        data-end="<?= esc($sh->end_time) ?>"
                                                        data-tolerance="<?= esc($sh->late_tolerance_minutes) ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CHECK-IN PRESENSI -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalCheckIn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-fingerprint mr-1 text-teal"></i> Terminal Presensi Masuk (Check-In)
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/check-in') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Pilih Nama Pegawai / Dokter: <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control form-control-sm font-weight-bold" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($employees as $e): ?>
                                <option value="<?= $e->id ?>"><?= esc($e->name) ?> (<?= esc($e->nip) ?>) - <?= esc($e->department) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Shift Kerja: <span class="text-danger">*</span></label>
                        <select name="shift_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($shifts as $sh): ?>
                                <option value="<?= $sh->id ?>"><?= esc($sh->shift_name) ?> (<?= substr($sh->start_time, 0, 5) ?> - <?= substr($sh->end_time, 0, 5) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal: <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Jam Check-In: <span class="text-danger">*</span></label>
                            <input type="time" name="check_in_time" class="form-control form-control-sm font-weight-bold text-teal" value="<?= date('H:i') ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Catatan / Keterangan:</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Check-in shift pagi">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-check mr-1"></i> Simpan Check-In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CHECK-OUT PRESENSI -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalCheckOut" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-right-from-bracket mr-1 text-danger"></i> Presensi Pulang (Check-Out)
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/check-out') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="employee_id" id="cout-emp-id">
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nama Pegawai:</label>
                        <input type="text" id="cout-emp-name" class="form-control form-control-sm font-weight-bold bg-light" readonly>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Jam Check-Out Pulang: <span class="text-danger">*</span></label>
                        <input type="time" name="check_out_time" class="form-control form-control-sm font-weight-bold text-danger" value="<?= date('H:i') ?>" required>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold"><i class="fas fa-sign-out-alt mr-1"></i> Simpan Check-Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: AJUKAN CUTI / IZIN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalLeave" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-file-signature mr-1 text-warning"></i> Form Pengajuan Cuti / Izin Pegawai
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/ajukan-cuti') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Pilih Pegawai: <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control form-control-sm font-weight-bold" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($employees as $e): ?>
                                <option value="<?= $e->id ?>"><?= esc($e->name) ?> (<?= esc($e->nip) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Jenis Ketidakhadiran: <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-control form-control-sm font-weight-bold" required>
                            <option value="cuti_tahunan">Cuti Tahunan Pegawai</option>
                            <option value="sakit">Sakit (Dengan Surat Keterangan Dokter)</option>
                            <option value="izin">Izin Kepentingan Pribadi / Keluarga</option>
                            <option value="cuti_melahirkan">Cuti Melahirkan</option>
                            <option value="dinas_luar">Tugas Luar / Dinas Pendidikan</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Mulai: <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Selesai: <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Alasan / Keterangan: <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="Uraikan alasan pengajuan cuti atau keperluan izin..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold text-dark"><i class="fas fa-paper-plane mr-1"></i> Ajukan Permohonan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MASTER SHIFT KERJA -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalShift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleShiftModal">
                    <i class="fas fa-business-time mr-1 text-purple"></i> Master Shift Kerja Baru
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/manage-shift') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="shift_id" id="shift-id" value="">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kode Shift: <span class="text-danger">*</span></label>
                            <input type="text" name="shift_code" id="shift-code" class="form-control form-control-sm font-weight-bold" placeholder="SHIFT-PAGI" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Shift: <span class="text-danger">*</span></label>
                            <input type="text" name="shift_name" id="shift-name" class="form-control form-control-sm font-weight-bold" placeholder="Shift Pagi Pelayanan" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Jam Masuk: <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="shift-start" class="form-control form-control-sm font-weight-bold text-teal" value="07:00" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Jam Pulang: <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="shift-end" class="form-control form-control-sm font-weight-bold text-danger" value="14:00" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Toleransi (Mnt):</label>
                            <input type="number" name="late_tolerance_minutes" id="shift-tolerance" class="form-control form-control-sm" value="15" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-purple btn-sm font-weight-bold text-white" style="background:#8b5cf6;"><i class="fas fa-save mr-1"></i> Simpan Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Checkout trigger modal
        $('.btn-checkout-trigger').click(function() {
            $('#cout-emp-id').val($(this).data('id'));
            $('#cout-emp-name').val($(this).data('name'));
            $('#modalCheckOut').modal('show');
        });

        // Edit Shift
        $('.btn-edit-shift').click(function() {
            $('#titleShiftModal').html('<i class="fas fa-edit mr-1"></i> Edit Shift Kerja');
            $('#shift-id').val($(this).data('id'));
            $('#shift-code').val($(this).data('code'));
            $('#shift-name').val($(this).data('name'));
            $('#shift-start').val($(this).data('start'));
            $('#shift-end').val($(this).data('end'));
            $('#shift-tolerance').val($(this).data('tolerance'));
            $('#modalShift').modal('show');
        });

        // Reset Shift Modal
        $('[data-target="#modalShift"]').click(function() {
            if (!$('#shift-id').val()) {
                $('#titleShiftModal').html('<i class="fas fa-business-time mr-1"></i> Master Shift Kerja Baru');
                $('#shift-id').val('');
            }
        });
    });
</script>
<?= $this->endSection() ?>
