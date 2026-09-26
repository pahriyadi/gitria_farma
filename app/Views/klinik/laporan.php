<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-chart-pie text-teal mr-2"></i> Laporan & Rekapitulasi Pelayanan Klinik</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Klinik</li>
                    <li class="breadcrumb-item active">Laporan Morbiditas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- Filter Card -->
        <div class="card bg-light border mb-4">
            <div class="card-body p-3">
                <form action="<?= base_url('klinik/laporan') ?>" method="get" class="form-inline d-flex flex-wrap justify-content-between align-items-center">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <label class="mr-2 font-weight-bold text-dark"><i class="fas fa-calendar-alt text-teal mr-1"></i> Periode Pelayanan:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="<?= esc($startDate) ?>">
                        <span class="mr-2 font-weight-bold text-muted">s/d</span>
                        <input type="date" name="end_date" class="form-control form-control-sm mr-2" value="<?= esc($endDate) ?>">
                        <button type="submit" class="btn btn-teal btn-sm font-weight-bold">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                    <div class="btn-group">
                        <a href="<?= base_url('klinik/cetak-laporan?type=morbiditas&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-success btn-sm font-weight-bold mr-1">
                            <i class="fas fa-print mr-1"></i> 10 Morbiditas
                        </a>
                        <a href="<?= base_url('klinik/cetak-laporan?type=kunjungan&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold mr-1">
                            <i class="fas fa-file-alt mr-1"></i> Rekap Kunjungan
                        </a>
                        <a href="<?= base_url('klinik/cetak-laporan?type=waktu_pelayanan&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                            <i class="fas fa-stopwatch mr-1"></i> Cetak Laporan Waktu Layanan (SLA)
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="row">
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="info-box border elevation-0 bg-white">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Total Kunjungan Pasien</span>
                        <span class="info-box-number text-dark h4 font-weight-bold mb-0"><?= number_format($totalVisits) ?></span>
                        <small class="text-muted">Periode terpilih</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="info-box border elevation-0 bg-white">
                    <span class="info-box-icon bg-info elevation-0"><i class="fas fa-file-medical"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Surat Medis Diterbitkan</span>
                        <span class="info-box-number text-dark h4 font-weight-bold mb-0"><?= number_format($totalLetters) ?></span>
                        <small class="text-muted">SKS, SKD & Rujukan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="info-box border elevation-0 bg-white">
                    <span class="info-box-icon bg-success elevation-0"><i class="fas fa-vial"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-muted">Tes Laboratorium</span>
                        <span class="info-box-number text-dark h4 font-weight-bold mb-0"><?= number_format($totalLabTests) ?></span>
                        <small class="text-muted">Pemeriksaan penunjang</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Container -->
        <div class="card card-teal card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="klinik-report-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-morbiditas-tab" data-toggle="pill" href="#tab-morbiditas" role="tab">
                            <i class="fas fa-viruses mr-1"></i> 1. 10 Besar Morbiditas (ICD-10)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-demografi-tab" data-toggle="pill" href="#tab-demografi" role="tab">
                            <i class="fas fa-chart-bar mr-1"></i> 2. Demografi & Kunjungan Poliklinik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-teal" id="tab-sla-tab" data-toggle="pill" href="#tab-sla" role="tab">
                            <i class="fas fa-stopwatch mr-1"></i> 3. Waktu Tunggu & Durasi Layanan Pasien (SLA)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="klinik-report-tabContent">

                    <!-- TAB 1: 10 BESAR MORBIDITAS (ICD-10) -->
                    <div class="tab-pane fade show active" id="tab-morbiditas" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-trophy text-warning mr-1"></i> Rekapitulasi 10 Besar Penyakit Terbanyak (Laporan Morbiditas Dinkes)
                            </h5>
                            <span class="badge badge-light border text-secondary font-weight-bold">
                                Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?>
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">Ranking</th>
                                        <th style="width: 120px;" class="text-center">Kode ICD-10</th>
                                        <th>Nama Diagnosa Penyakit (Deskripsi Bahasa Indonesia & Inggris)</th>
                                        <th style="width: 130px;" class="text-center">Jumlah Kasus</th>
                                        <th style="width: 130px;" class="text-center">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($topIcd)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Belum ada data diagnosa ICD-10 pada periode terpilih.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $totalCases = array_sum(array_column($topIcd, 'total_cases'));
                                        $rank = 1;
                                        foreach ($topIcd as $icd): 
                                            $pct = $totalCases > 0 ? ($icd->total_cases / $totalCases) * 100 : 0;
                                        ?>
                                            <tr>
                                                <td class="text-center font-weight-bold">
                                                    <?php if ($rank === 1): ?>
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-crown"></i> 1</span>
                                                    <?php elseif ($rank === 2): ?>
                                                        <span class="badge badge-secondary px-2 py-1">2</span>
                                                    <?php elseif ($rank === 3): ?>
                                                        <span class="badge badge-danger px-2 py-1">3</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light border"><?= $rank ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-teal"><?= esc($icd->icd10_code) ?></td>
                                                <td>
                                                    <strong><?= esc($icd->name_id ?? $icd->name_en ?? 'Diagnosa Medis Terdaftar') ?></strong>
                                                    <?php if (!empty($icd->name_en) && !empty($icd->name_id)): ?>
                                                        <br><small class="text-muted font-italic"><?= esc($icd->name_en) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-dark h6 mb-0"><?= number_format($icd->total_cases) ?> Kasus</td>
                                                <td class="text-center">
                                                    <div class="progress progress-xs mb-1">
                                                        <div class="progress-bar bg-teal" style="width: <?= $pct ?>%"></div>
                                                    </div>
                                                    <small class="font-weight-bold text-secondary"><?= number_format($pct, 1) ?>%</small>
                                                </td>
                                            </tr>
                                        <?php $rank++; endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: DEMOGRAFI & KUNJUNGAN POLIKLINIK -->
                    <div class="tab-pane fade" id="tab-demografi" role="tabpanel">
                        <div class="row">
                            <!-- Kunjungan Berdasarkan Poli -->
                            <div class="col-md-6 mb-4">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-clinic-medical text-teal mr-1"></i> Kunjungan per Poliklinik / Ruang Tindakan</h6>
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Poliklinik / Layanan</th>
                                            <th style="width: 140px;" class="text-center">Total Kunjungan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($visitsByPoli)): ?>
                                            <tr><td colspan="2" class="text-center py-3 text-muted">Tidak ada data.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($visitsByPoli as $vp): ?>
                                                <tr>
                                                    <td><strong><?= esc($vp->poly_name) ?></strong></td>
                                                    <td class="text-center font-weight-bold text-dark"><?= number_format($vp->total_visits) ?> Pasien</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Distribusi Gender & Cara Bayar -->
                            <div class="col-md-6 mb-4">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-wallet text-teal mr-1"></i> Distribusi Pasien & Cara Bayar</h6>
                                <table class="table table-bordered table-striped mb-4">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Metode Pembayaran</th>
                                            <th style="width: 140px;" class="text-center">Jumlah Pasien</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paymentStats as $ps): ?>
                                            <tr>
                                                <td><strong><?= strtoupper(esc($ps->payment_method)) ?></strong></td>
                                                <td class="text-center font-weight-bold text-dark"><?= number_format($ps->total) ?> Pasien</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-venus-mars text-teal mr-1"></i> Jenis Kelamin Pasien</h6>
                                <div class="row">
                                    <?php foreach ($genderStats as $gs): ?>
                                        <div class="col-6">
                                            <div class="card p-3 border text-center">
                                                <div class="text-secondary small font-weight-bold text-uppercase">
                                                    <?= $gs->gender === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' ?>
                                                </div>
                                                <div class="h4 font-weight-bold text-teal mb-0"><?= number_format($gs->total) ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: ANALISIS WAKTU TUNGGU & DURASI LAYANAN PASIEN (SLA) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-sla" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-stopwatch text-teal mr-1"></i> Evaluasi Durasi & Waktu Tunggu Pelayanan Pasien (SLA / Lead Time)
                                </h5>
                                <small class="text-muted">Mengukur durasi riil setiap tahapan: Kedatangan $\rightarrow$ TTV Perawat $\rightarrow$ Pemeriksaan Dokter $\rightarrow$ Kasir Utama $\rightarrow$ Apotek Farmasi</small>
                            </div>
                            <span class="badge badge-teal px-3 py-2 font-weight-bold shadow-sm" style="font-size: 13px;">
                                <i class="fas fa-check-circle mr-1"></i> <?= number_format($avgMetrics['count_served'] ?? 0) ?> Pasien Selesai Dievaluasi
                            </span>
                        </div>

                        <!-- 5 KPI Cards: Rata-Rata Waktu Tiap Pos Layanan -->
                        <div class="row mb-3">
                            <div class="col-lg col-md-4 col-sm-6 mb-2">
                                <div class="card p-3 border shadow-none bg-light text-center h-100" style="border-left: 4px solid #0ea5e9 !important;">
                                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1"><i class="fas fa-user-nurse text-info mr-1"></i> 1. Tunggu TTV Perawat</div>
                                    <div class="h3 font-weight-bold text-dark mb-0"><?= $avgMetrics['avg_ttv'] ?? 0 ?> <span class="text-sm font-weight-normal text-muted">mnt</span></div>
                                    <small class="text-muted">Target: $\le 10$ mnt</small>
                                </div>
                            </div>
                            <div class="col-lg col-md-4 col-sm-6 mb-2">
                                <div class="card p-3 border shadow-none bg-light text-center h-100" style="border-left: 4px solid #10b981 !important;">
                                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1"><i class="fas fa-stethoscope text-success mr-1"></i> 2. Konsultasi Dokter</div>
                                    <div class="h3 font-weight-bold text-dark mb-0"><?= $avgMetrics['avg_doc'] ?? 0 ?> <span class="text-sm font-weight-normal text-muted">mnt</span></div>
                                    <small class="text-muted">Target: $\le 15$ mnt</small>
                                </div>
                            </div>
                            <div class="col-lg col-md-4 col-sm-6 mb-2">
                                <div class="card p-3 border shadow-none bg-light text-center h-100" style="border-left: 4px solid #f59e0b !important;">
                                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1"><i class="fas fa-cash-register text-warning mr-1"></i> 3. Pembayaran Kasir</div>
                                    <div class="h3 font-weight-bold text-dark mb-0"><?= $avgMetrics['avg_cashier'] ?? 0 ?> <span class="text-sm font-weight-normal text-muted">mnt</span></div>
                                    <small class="text-muted">Target: $\le 7$ mnt</small>
                                </div>
                            </div>
                            <div class="col-lg col-md-4 col-sm-6 mb-2">
                                <div class="card p-3 border shadow-none bg-light text-center h-100" style="border-left: 4px solid #0d9488 !important;">
                                    <div class="text-xs font-weight-bold text-muted text-uppercase mb-1"><i class="fas fa-pills text-teal mr-1"></i> 4. Serah Obat Apotek</div>
                                    <div class="h3 font-weight-bold text-dark mb-0"><?= $avgMetrics['avg_pharmacy'] ?? 0 ?> <span class="text-sm font-weight-normal text-muted">mnt</span></div>
                                    <small class="text-muted">Target: $\le 15$ mnt</small>
                                </div>
                            </div>
                            <div class="col-lg col-md-4 col-sm-6 mb-2">
                                <div class="card p-3 border shadow-none bg-white text-center h-100 shadow-sm" style="border-left: 4px solid #6366f1 !important; background:#f8fafc;">
                                    <div class="text-xs font-weight-bold text-indigo text-uppercase mb-1"><i class="fas fa-flag-checkered text-primary mr-1"></i> TOTAL WAKTU LAYANAN</div>
                                    <div class="h3 font-weight-bold text-teal mb-0"><?= $avgMetrics['avg_overall'] ?? 0 ?> <span class="text-sm font-weight-normal text-muted">mnt</span></div>
                                    <small class="text-muted font-weight-bold">End-to-End Layanan</small>
                                </div>
                            </div>
                        </div>

                        <!-- Table: Rincian Riwayat & Durasi Waktu Pasien -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-sm">
                                <thead class="bg-light">
                                    <tr class="text-center align-middle">
                                        <th rowspan="2" style="width: 45px;">No</th>
                                        <th rowspan="2" style="width: 100px;">No. Antrean</th>
                                        <th rowspan="2">Pasien & Layanan</th>
                                        <th colspan="5" class="bg-secondary text-white py-1">Catatan Jam / Timestamp Transaksi</th>
                                        <th colspan="4" class="bg-dark text-white py-1">Durasi per Pos Layanan (Menit)</th>
                                        <th rowspan="2" style="width: 110px;" class="bg-teal text-white">Total Waktu</th>
                                        <th rowspan="2" style="width: 130px;">Status Evaluasi</th>
                                    </tr>
                                    <tr class="text-center" style="font-size: 11px;">
                                        <th title="Waktu Pendaftaran / Datang">Daftar ($T_0$)</th>
                                        <th title="Waktu Mulai TTV Perawat">TTV ($T_1$)</th>
                                        <th title="Waktu Selesai SOAP Dokter">Dokter ($T_2$)</th>
                                        <th title="Waktu Lunas di Kasir">Kasir ($T_3$)</th>
                                        <th title="Waktu Selesai Obat Apotek">Apotek ($T_4$)</th>
                                        <th title="Waktu Tunggu TTV">Tunggu</th>
                                        <th title="Durasi Periksa Dokter">Dokter</th>
                                        <th title="Durasi di Kasir">Kasir</th>
                                        <th title="Durasi di Apotek">Apotek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($slaVisits)): ?>
                                        <tr>
                                            <td colspan="14" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Tidak ada data antrean pasien pada periode terpilih.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($slaVisits as $sv): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td class="text-center font-weight-bold">
                                                    <span class="badge badge-teal px-2 py-1" style="font-size: 12px;"><?= esc($sv->queue_no) ?></span>
                                                    <br><small class="text-muted"><?= esc($sv->no_visit) ?></small>
                                                </td>
                                                <td>
                                                    <strong class="text-dark"><?= esc($sv->patient_name) ?></strong>
                                                    <small class="text-muted">(RM: <?= esc($sv->no_rm) ?>)</small>
                                                    <br>
                                                    <small class="text-secondary"><i class="fas fa-stethoscope text-teal mr-1"></i><?= esc($sv->service_name) ?> &bull; <?= esc($sv->doctor_name) ?></small>
                                                </td>
                                                <!-- Timestamps -->
                                                <td class="text-center font-weight-bold text-dark"><?= $sv->time_t0 ?></td>
                                                <td class="text-center font-weight-bold <?= $sv->time_t1 !== '-' ? 'text-info' : 'text-muted' ?>"><?= $sv->time_t1 ?></td>
                                                <td class="text-center font-weight-bold <?= $sv->time_t2 !== '-' ? 'text-success' : 'text-muted' ?>"><?= $sv->time_t2 ?></td>
                                                <td class="text-center font-weight-bold <?= $sv->time_t3 !== '-' ? 'text-warning' : 'text-muted' ?>"><?= $sv->time_t3 ?></td>
                                                <td class="text-center font-weight-bold <?= $sv->time_t4 !== '-' ? 'text-teal' : 'text-muted' ?>"><?= $sv->time_t4 ?></td>
                                                <!-- Durations -->
                                                <td class="text-center font-weight-bold <?= ($sv->dur_ttv !== null && $sv->dur_ttv > 15) ? 'text-danger' : '' ?>">
                                                    <?= $sv->dur_ttv !== null ? ($sv->dur_ttv . ' mnt') : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?= $sv->dur_doc !== null ? ($sv->dur_doc . ' mnt') : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?= $sv->dur_cashier !== null ? ($sv->dur_cashier . ' mnt') : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?= $sv->dur_pharmacy !== null ? ($sv->dur_pharmacy . ' mnt') : '-' ?>
                                                </td>
                                                <!-- Total Duration -->
                                                <td class="text-center">
                                                    <?php if ($sv->total_minutes !== null): ?>
                                                        <span class="h6 font-weight-bold text-dark mb-0 d-block"><?= $sv->total_minutes ?> mnt</span>
                                                    <?php else: ?>
                                                        <span class="text-muted font-italic">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- Efficiency Badge -->
                                                <td class="text-center">
                                                    <span class="badge <?= $sv->efficiency_badge ?> px-2 py-1 font-weight-bold">
                                                        <?= $sv->efficiency_label ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
