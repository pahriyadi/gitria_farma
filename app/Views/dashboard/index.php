<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- TOP WELCOME & STATUS BANNER -->
<div class="card mb-3 card-kpi-teal">
    <div class="card-body py-2 px-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-hospital-user text-teal mr-2"></i> 
                    Selamat Datang, <?= esc(session('username') ?? 'Admin') ?>!
                    <span class="badge badge-subtle-teal ml-2 font-weight-bold"><?= esc($roleName) ?></span>
                </h5>
                <p class="text-muted mb-0 mt-1" style="font-size: 13px;">
                    Panel Kontrol Terpadu <strong><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></strong> — Pelayanan Medis, Farmasi, Resto Sehat & Keuangan Akuntansi.
                </p>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                <span class="badge badge-light border px-2 py-1 text-dark font-weight-bold" style="font-size: 12px;">
                    <i class="fas fa-calendar-alt text-teal mr-1"></i> <?= date('d F Y') ?>
                </span>
                <span class="badge badge-subtle-teal px-2 py-1 font-weight-bold ml-1" style="font-size: 12px;">
                    <i class="fas fa-clock mr-1"></i> <span id="liveClock"><?= date('H:i:s') ?> WITA</span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. EXECUTIVE FINANCIAL SUITE (Khusus Peran Eksekutif / Keuangan / Super Admin) -->
<!-- ========================================================================= -->
<?php if ($isExecutive): ?>
<div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="font-weight-bold text-dark mb-0">
        <i class="fas fa-chart-pie text-teal mr-1"></i> Ringkasan Kinerja Finansial & Likuiditas
    </h6>
    <div>
        <a href="<?= base_url('keuangan/ekspor-excel-multisheet') ?>" class="btn btn-outline-success btn-xs font-weight-bold mr-1">
            <i class="fas fa-file-excel mr-1"></i> Ekspor Excel Multi-Sheet
        </a>
        <a href="<?= base_url('accounting/laporan') ?>" class="btn btn-outline-teal btn-xs font-weight-bold">
            <i class="fas fa-file-invoice-dollar mr-1"></i> 5 Laporan Keuangan Lengkap
        </a>
    </div>
</div>

<div class="row">
    <!-- Kas & Bank Balance -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-info mb-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Total Kas & Bank</span>
                        <h4 class="font-weight-bold text-dark mt-1 mb-0" style="font-size: 1.25rem;">Rp <?= number_format($cash_bank, 2, ',', '.') ?></h4>
                    </div>
                    <div class="p-2 rounded bg-light text-info border">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                    <span class="text-muted">Kasir + Bank Operasional</span>
                    <a href="<?= base_url('keuangan/transaksi') ?>" class="text-info font-weight-bold">Buku Kas &amp; Bank <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-teal mb-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Total Pendapatan Terpadu</span>
                        <h4 class="font-weight-bold text-teal mt-1 mb-0" style="font-size: 1.25rem;">Rp <?= number_format($total_revenue, 2, ',', '.') ?></h4>
                    </div>
                    <div class="p-2 rounded bg-light text-teal border">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                    <span class="text-muted">Poli + Apotek + Resto + Lab</span>
                    <a href="<?= base_url('accounting/laporan') ?>" class="text-teal font-weight-bold">Laba Rugi <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-danger mb-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Total Beban Operasional</span>
                        <h4 class="font-weight-bold text-danger mt-1 mb-0" style="font-size: 1.25rem;">Rp <?= number_format($total_expenses, 2, ',', '.') ?></h4>
                    </div>
                    <div class="p-2 rounded bg-light text-danger border">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                    <span class="text-muted">HPP Obat + Beban Klinik</span>
                    <a href="<?= base_url('accounting/laporan') ?>" class="text-danger font-weight-bold">Rincian Beban <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-success mb-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Laba Bersih Konsolidasian</span>
                        <h4 class="font-weight-bold text-success mt-1 mb-0" style="font-size: 1.25rem;">Rp <?= number_format($net_profit, 2, ',', '.') ?></h4>
                    </div>
                    <div class="p-2 rounded bg-light text-success border">
                        <i class="fas fa-hand-holding-usd fa-lg"></i>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                    <span class="text-muted">Margin Laba Bersih</span>
                    <a href="<?= base_url('accounting/laporan') ?>" class="text-success font-weight-bold">Analisa Neraca <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Financial Health (Piutang & Hutang) -->
<div class="row mb-2">
    <div class="col-md-6 mb-2">
        <div class="card mb-0">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-invoice text-warning mr-2 fa-lg"></i>
                    <div>
                        <span class="text-muted" style="font-size: 11.5px;">Total Piutang Pasien & BPJS (1-104, 1-105)</span>
                        <h6 class="font-weight-bold text-dark mb-0">Rp <?= number_format($total_receivables, 2, ',', '.') ?></h6>
                    </div>
                </div>
                <a href="<?= base_url('accounting/buku-besar?account_id=5') ?>" class="btn btn-xs btn-outline-warning font-weight-bold">Buku Piutang</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card mb-0">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-receipt text-danger mr-2 fa-lg"></i>
                    <div>
                        <span class="text-muted" style="font-size: 11.5px;">Total Hutang Usaha Supplier (2-101)</span>
                        <h6 class="font-weight-bold text-dark mb-0">Rp <?= number_format($total_payables, 2, ',', '.') ?></h6>
                    </div>
                </div>
                <a href="<?= base_url('accounting/buku-besar?account_id=6') ?>" class="btn btn-xs btn-outline-danger font-weight-bold">Buku Hutang</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ========================================================================= -->
<!-- 2. OPERATIONAL SUMMARY & METRICS (KLINIK, APOTEK, RESTO) -->
<!-- ========================================================================= -->
<div class="d-flex align-items-center justify-content-between mb-2 mt-2">
    <h6 class="font-weight-bold text-dark mb-0">
        <i class="fas fa-clinic-medical text-teal mr-1"></i> Aktivitas Pelayanan Operasional Hari Ini
    </h6>
    <span class="text-muted" style="font-size: 12px;">Pembaruan otomatis data real-time</span>
</div>

<div class="row">
    <!-- Kunjungan Pasien Hari Ini -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-teal mb-0">
            <div class="card-body p-3">
                <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Pasien Hari Ini</span>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 1.5rem;"><?= $today_visits ?> <span style="font-size: 13px; font-weight: normal;" class="text-muted">Kunjungan</span></h3>
                    <span class="badge badge-subtle-success px-2 py-1"><?= $today_visits_completed ?> Selesai</span>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between" style="font-size: 11.5px;">
                    <span class="text-muted"><i class="fas fa-hourglass-start text-warning mr-1"></i> Menunggu: <strong><?= $today_visits_waiting ?></strong></span>
                    <span class="text-muted"><i class="fas fa-stethoscope text-primary mr-1"></i> Diperiksa: <strong><?= $today_visits_examining ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Resep Apotek Hari Ini -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-info mb-0">
            <div class="card-body p-3">
                <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Resep Farmasi Hari Ini</span>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 1.5rem;"><?= $today_prescriptions ?> <span style="font-size: 13px; font-weight: normal;" class="text-muted">Resep</span></h3>
                    <span class="badge badge-subtle-info px-2 py-1"><?= $today_prescriptions_done ?> Dilayani</span>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between" style="font-size: 11.5px;">
                    <span class="text-muted"><i class="fas fa-spinner text-warning mr-1"></i> Antrean: <strong><?= $today_prescriptions_pending ?></strong></span>
                    <a href="<?= base_url('apotek/resep') ?>" class="text-info font-weight-bold">Apotek <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Restoran Sehat Hari Ini -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-warning mb-0">
            <div class="card-body p-3">
                <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Restoran Sehat & Dapur</span>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 1.5rem;"><?= $today_resto_orders ?> <span style="font-size: 13px; font-weight: normal;" class="text-muted">Pesanan</span></h3>
                    <span class="badge badge-subtle-warning px-2 py-1"><?= $active_tables_count ?>/<?= $total_tables_count ?> Meja Terisi</span>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between" style="font-size: 11.5px;">
                    <span class="text-muted"><i class="fas fa-fire text-danger mr-1"></i> KDS Aktif: <strong><?= $today_resto_active_orders ?></strong></span>
                    <a href="<?= base_url('resto/pos') ?>" class="text-warning font-weight-bold">POS Resto <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Pasien & SDM Klinik -->
    <div class="col-xl-3 col-md-6 col-12 mb-2">
        <div class="card h-100 card-kpi-purple mb-0">
            <div class="card-body p-3">
                <span class="text-muted font-weight-bold" style="font-size: 11.5px; text-transform: uppercase;">Master Pasien & Nakes</span>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <h3 class="font-weight-bold text-dark mb-0" style="font-size: 1.5rem;"><?= number_format($total_patients, 0, ',', '.') ?> <span style="font-size: 13px; font-weight: normal;" class="text-muted">Pasien</span></h3>
                    <span class="badge badge-subtle-teal px-2 py-1">+<?= $today_new_patients ?> Baru</span>
                </div>
                <div class="mt-2 pt-2 border-top d-flex justify-content-between" style="font-size: 11.5px;">
                    <span class="text-muted"><i class="fas fa-user-md text-primary mr-1"></i> Dokter: <strong><?= $active_doctors_count ?></strong></span>
                    <span class="text-muted"><i class="fas fa-clinic-medical text-teal mr-1"></i> Poli: <strong><?= $active_polys_count ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. INTERACTIVE ANALYTICS & RISK WARNINGS -->
<!-- ========================================================================= -->
<div class="row">
    <!-- Left Column: 7-Day Trend Chart -->
    <div class="col-lg-8 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-chart-area text-teal mr-1"></i> Tren Kunjungan & Finansial (7 Hari)
                </h6>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary active btn-xs" id="btnShowVisitsChart">Kunjungan</button>
                    <?php if ($isExecutive): ?>
                        <button class="btn btn-outline-secondary btn-xs" id="btnShowFinanceChart">Finansial</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="height: 260px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Operational Risk & Stock Alerts -->
    <div class="col-lg-4 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-shield-alt text-warning mr-1"></i> Peringatan & Manajemen Risiko
                </h6>
            </div>
            <div class="card-body p-3">
                <!-- Alert 1: Stok Kritis Obat -->
                <div class="p-2 mb-2 rounded bg-light border-left-danger border d-flex justify-content-between align-items-center" style="border-left: 3.5px solid #ef4444 !important;">
                    <div>
                        <span class="font-weight-bold text-danger d-block" style="font-size: 12.5px;">
                            <i class="fas fa-boxes-packing mr-1"></i> Stok Obat Kritis (&le; Buffer)
                        </span>
                        <small class="text-muted">Perlu restock / PO segera</small>
                    </div>
                    <div>
                        <span class="badge badge-subtle-danger font-weight-bold" style="font-size: 12px;"><?= $low_stock ?> Item</span>
                    </div>
                </div>

                <!-- Alert 2: Obat Hampir Kedaluwarsa -->
                <div class="p-2 mb-2 rounded bg-light border-left-warning border d-flex justify-content-between align-items-center" style="border-left: 3.5px solid #f59e0b !important;">
                    <div>
                        <span class="font-weight-bold text-warning d-block" style="font-size: 12.5px;">
                            <i class="fas fa-hourglass-half mr-1"></i> Batch Hampir Expired
                        </span>
                        <small class="text-muted">Masa simpan &lt; 90 hari</small>
                    </div>
                    <div>
                        <span class="badge badge-subtle-warning font-weight-bold" style="font-size: 12px;"><?= $expired_count ?> Batch</span>
                    </div>
                </div>

                <!-- Alert 3: Approval Pengadaan / Transaksi -->
                <div class="p-2 mb-2 rounded bg-light border-left-info border d-flex justify-content-between align-items-center" style="border-left: 3.5px solid #0284c7 !important;">
                    <div>
                        <span class="font-weight-bold text-info d-block" style="font-size: 12.5px;">
                            <i class="fas fa-stamp mr-1"></i> Menunggu Approval (PO)
                        </span>
                        <small class="text-muted">Pengajuan pengadaan baru</small>
                    </div>
                    <div>
                        <span class="badge badge-subtle-info font-weight-bold" style="font-size: 12px;"><?= $pending_po_approval ?> PO</span>
                    </div>
                </div>

                <!-- Alert 4: Presensi & Cuti Staf -->
                <div class="p-2 rounded bg-light border-left-primary border d-flex justify-content-between align-items-center" style="border-left: 3.5px solid #8b5cf6 !important;">
                    <div>
                        <span class="font-weight-bold text-primary d-block" style="font-size: 12.5px;">
                            <i class="fas fa-users-cog mr-1"></i> Kehadiran SDM Hari Ini
                        </span>
                        <small class="text-muted">Cuti tertunda: <?= $pending_leaves ?></small>
                    </div>
                    <div>
                        <span class="badge badge-subtle-teal font-weight-bold" style="font-size: 12px;"><?= $today_attendance ?> Hadir</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. QUICK ACTION COMMAND CENTER -->
<!-- ========================================================================= -->
<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="card-title font-weight-bold text-dark mb-0">
            <i class="fas fa-bolt text-warning mr-1"></i> Pusat Pintasan Aksi Cepat (Quick Action Command Center)
        </h6>
    </div>
    <div class="card-body p-3">
        <div class="row text-center">
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('klinik/pendaftaran') ?>" class="btn btn-outline-teal btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-user-plus fa-lg d-block mb-1"></i> Pendaftaran
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('klinik/display') ?>" target="_blank" class="btn btn-outline-info btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-tv fa-lg d-block mb-1"></i> Display Antrean
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('klinik/soap') ?>" class="btn btn-outline-primary btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-stethoscope fa-lg d-block mb-1"></i> RME Dokter
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('apotek/resep') ?>" class="btn btn-outline-success btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-pills fa-lg d-block mb-1"></i> Resep Apotek
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('resto/pos') ?>" class="btn btn-outline-warning btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-utensils fa-lg d-block mb-1"></i> Kasir Resto
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-2">
                <a href="<?= base_url('keuangan/kasir') ?>" class="btn btn-outline-danger btn-block font-weight-bold py-2" style="font-size: 12.5px;">
                    <i class="fas fa-cash-register fa-lg d-block mb-1"></i> Kasir Medis
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 5. LIVE RECENT ACTIVITY TABLES -->
<!-- ========================================================================= -->
<div class="row">
    <!-- Kunjungan Pasien Terkini -->
    <div class="col-lg-6 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-user-injured text-teal mr-1"></i> Kunjungan Pasien Terkini
                </h6>
                <a href="<?= base_url('klinik/pendaftaran') ?>" class="btn btn-xs btn-outline-secondary font-weight-bold">Lihat Semua</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0 text-dark" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th>No Visit</th>
                            <th>Pasien</th>
                            <th>Poliklinik</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_visits)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada kunjungan tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_visits as $visit): ?>
                                <tr>
                                    <td><strong><?= esc($visit->no_visit) ?></strong></td>
                                    <td>
                                        <div class="font-weight-bold"><?= esc($visit->patient_name ?? 'Pasien Umum') ?></div>
                                        <small class="text-muted">No RM: <?= esc($visit->no_rm ?? '-') ?></small>
                                    </td>
                                    <td><?= esc($visit->polyclinic_name ?? '-') ?></td>
                                    <td>
                                        <?php 
                                        $badgeColor = [
                                            'waiting' => 'subtle-warning',
                                            'triage' => 'subtle-info',
                                            'examining' => 'subtle-teal',
                                            'prescription' => 'subtle-warning',
                                            'cashier' => 'subtle-teal',
                                            'completed' => 'subtle-success',
                                            'cancelled' => 'subtle-danger'
                                        ][$visit->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $badgeColor ?>"><?= esc(strtoupper($visit->status)) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Resep Apotek Masuk Terkini -->
    <div class="col-lg-6 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-prescription-bottle-alt text-info mr-1"></i> Resep Apotek Masuk Terkini
                </h6>
                <a href="<?= base_url('apotek/resep') ?>" class="btn btn-xs btn-outline-secondary font-weight-bold">Lihat Semua</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0 text-dark" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th>No Visit</th>
                            <th>Nama Pasien</th>
                            <th>Dokter Penulis</th>
                            <th>Status Dispensing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_prescriptions)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada resep masuk hari ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_prescriptions as $rx): ?>
                                <tr>
                                    <td><strong><?= esc($rx->no_visit ?? ('RX-' . $rx->id)) ?></strong></td>
                                    <td>
                                        <div class="font-weight-bold"><?= esc($rx->patient_name ?? 'Pasien') ?></div>
                                        <small class="text-muted"><?= esc($rx->no_rm ?? '-') ?></small>
                                    </td>
                                    <td><?= esc($rx->doctor_name ?? 'Dokter Jaga') ?></td>
                                    <td>
                                        <?php 
                                        $rxBadge = [
                                            'waiting' => 'subtle-warning',
                                            'processing' => 'subtle-info',
                                            'completed' => 'subtle-success',
                                            'cancelled' => 'subtle-danger'
                                        ][$rx->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $rxBadge ?>"><?= esc(strtoupper($rx->status)) ?></span>
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

<!-- Row 2: Resto Orders & Audit Trail Logs -->
<div class="row">
    <!-- Pesanan Resto Terkini -->
    <div class="col-lg-6 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-utensils text-warning mr-1"></i> Pesanan Restoran & KDS Terkini
                </h6>
                <a href="<?= base_url('resto/laporan') ?>" class="btn btn-xs btn-outline-secondary font-weight-bold">Lihat Semua</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0 text-dark" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th>No Order</th>
                            <th>Meja</th>
                            <th>Status Dapur</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada order resto tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $ord): ?>
                                <tr>
                                    <td><strong><?= esc($ord->order_no) ?></strong></td>
                                    <td><span class="badge badge-light border">Meja <?= esc($ord->table_no ?? 'Takeaway') ?></span></td>
                                    <td>
                                        <?php 
                                        $ordBadge = [
                                            'open' => 'secondary',
                                            'cooking' => 'subtle-info',
                                            'ready' => 'subtle-warning',
                                            'closed' => 'subtle-success',
                                            'cancelled' => 'subtle-danger'
                                        ][$ord->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $ordBadge ?>"><?= esc(strtoupper($ord->status)) ?></span>
                                    </td>
                                    <td><?= date('d M H:i', strtotime($ord->created_at)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Trail Aktivitas Keamanan -->
    <div class="col-lg-6 mb-3">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-shield-alt text-teal mr-1"></i> Log Aktivitas Sistem (Audit Trail)
                </h6>
                <a href="<?= base_url('system/audit') ?>" class="btn btn-xs btn-outline-secondary font-weight-bold">Buka Log Penuh</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0 text-dark" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Modul</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_audit_logs)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada catatan aktivitas keamanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_audit_logs as $log): ?>
                                <tr>
                                    <td><small class="text-muted"><?= date('H:i:s', strtotime($log->created_at)) ?></small></td>
                                    <td><strong><?= esc($log->username ?? 'System') ?></strong></td>
                                    <td><span class="badge badge-light border"><?= esc($log->module) ?></span></td>
                                    <td>
                                        <span class="badge badge-<?= in_array($log->action, ['LOGIN', 'CREATE']) ? 'subtle-success' : (in_array($log->action, ['DELETE', 'LOGIN_FAILED']) ? 'subtle-danger' : 'subtle-info') ?>">
                                            <?= esc($log->action) ?>
                                        </span>
                                        <span class="text-muted ml-1" style="font-size: 11.5px;"><?= esc(substr($log->new_value ?? '', 0, 35)) ?>...</span>
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

<!-- ========================================================================= -->
<!-- 3. LIVE OPERATIONAL CONTROL CENTER & SMART MONITORING                     -->
<!-- ========================================================================= -->
<div class="d-flex align-items-center justify-content-between mb-2 mt-2">
    <h6 class="font-weight-bold text-dark mb-0">
        <i class="fas fa-tower-broadcast text-teal mr-1"></i> Live Operational Control Center
    </h6>
    <span class="badge badge-subtle-teal font-weight-bold px-2 py-1"><i class="fas fa-bolt mr-1"></i> Real-Time Sync</span>
</div>

<div class="row">
    <!-- 1. Obat Stok Kritis & Warning Minimum -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card h-100 mb-0 shadow-none border" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #ef4444 !important;">
            <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                <strong class="text-xs text-danger font-weight-bold text-uppercase">
                    <i class="fas fa-triangle-exclamation mr-1"></i> Stok Obat Kritis (< Min)
                </strong>
                <a href="<?= base_url('procurement/po') ?>" class="btn btn-xs btn-outline-danger font-weight-bold">Ajukan PO <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-striped mb-0 text-xs">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th class="text-center">Sisa</th>
                            <th class="text-center">Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($critical_drugs)): ?>
                            <tr><td colspan="3" class="text-center text-success py-3"><i class="fas fa-check-circle mr-1"></i> Stok seluruh obat aman</td></tr>
                        <?php else: ?>
                            <?php foreach ($critical_drugs as $cd): ?>
                                <tr>
                                    <td><strong><?= esc($cd->name) ?></strong></td>
                                    <td class="text-center font-weight-bold text-danger"><?= $cd->stock ?> <?= esc($cd->unit) ?></td>
                                    <td class="text-center text-muted"><?= $cd->min_stock ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Live Ruangan & Tempat Tidur Observasi -->
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card h-100 mb-0 shadow-none border" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #0d9488 !important;">
            <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                <strong class="text-xs text-teal font-weight-bold text-uppercase">
                    <i class="fas fa-door-open mr-1"></i> Okupansi Ruangan & Pelayanan
                </strong>
                <a href="<?= base_url('system/master-klinik') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">Master Ruang</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-striped mb-0 text-xs">
                    <thead>
                        <tr>
                            <th>Ruangan</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($live_rooms)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data ruangan</td></tr>
                        <?php else: ?>
                            <?php foreach ($live_rooms as $lr): 
                                $isOccupied = $lr->active_patients > 0;
                            ?>
                                <tr>
                                    <td><strong><?= esc($lr->name) ?></strong></td>
                                    <td class="text-center text-muted"><?= esc(ucfirst($lr->room_type ?? 'Poli')) ?></td>
                                    <td class="text-center">
                                        <?php if ($isOccupied): ?>
                                            <span class="badge badge-warning text-dark font-weight-bold"><?= $lr->active_patients ?> Pasien Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-success font-weight-bold">Tersedia / Kosong</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Piutang Klaim Asuransi / BPJS Menunggu -->
    <div class="col-lg-4 col-md-12 mb-3">
        <div class="card h-100 mb-0 shadow-none border" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #0284c7 !important;">
            <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                <strong class="text-xs text-info font-weight-bold text-uppercase">
                    <i class="fas fa-handshake mr-1"></i> Piutang Klaim Asuransi / BPJS
                </strong>
                <a href="<?= base_url('keuangan/kasir') ?>" class="btn btn-xs btn-outline-info font-weight-bold">Kasir Klaim</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-striped mb-0 text-xs">
                    <thead>
                        <tr>
                            <th>Pasien & Penjamin</th>
                            <th class="text-right">Nominal Klaim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unclaimed_insurance)): ?>
                            <tr><td colspan="2" class="text-center text-success py-3"><i class="fas fa-check-circle mr-1"></i> Seluruh klaim asuransi terproses</td></tr>
                        <?php else: ?>
                            <?php foreach ($unclaimed_insurance as $ui): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($ui->patient_name ?? 'Pasien') ?></strong>
                                        <small class="badge badge-info ml-1"><?= esc($ui->insurance_name ?? 'Asuransi') ?></small>
                                        <small class="text-muted d-block"><?= esc($ui->billing_no) ?></small>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark align-middle">Rp <?= number_format($ui->grand_total, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
$(document).ready(function() {
    // Live Clock updater
    setInterval(function() {
        var now = new Date();
        var timeStr = String(now.getHours()).padStart(2, '0') + ':' +
                      String(now.getMinutes()).padStart(2, '0') + ':' +
                      String(now.getSeconds()).padStart(2, '0') + ' WITA';
        $('#liveClock').text(timeStr);
    }, 1000);

    // Chart Data Setup
    var chartLabels  = <?= $chart_days ?>;
    var visitsData   = <?= $chart_visits ?>;
    var revenueData  = <?= $chart_revenue ?>;
    var expensesData = <?= $chart_expenses ?>;

    var ctx = document.getElementById('trendChart').getContext('2d');
    var currentChart;

    function renderVisitsChart() {
        if (currentChart) currentChart.destroy();
        currentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Kunjungan Pasien (Orang)',
                    data: visitsData,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#0d9488',
                    pointRadius: 3.5,
                    fill: true,
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true, position: 'top' },
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, stepSize: 1 }
                    }]
                }
            }
        });
    }

    function renderFinanceChart() {
        if (currentChart) currentChart.destroy();
        currentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Pendapatan (IDR)',
                        data: revenueData,
                        backgroundColor: 'rgba(13, 148, 136, 0.8)',
                        borderColor: '#0d9488',
                        borderWidth: 1
                    },
                    {
                        label: 'Beban Pengeluaran (IDR)',
                        data: expensesData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: '#ef4444',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true, position: 'top' },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }]
                }
            }
        });
    }

    renderVisitsChart();

    $('#btnShowVisitsChart').click(function() {
        $(this).addClass('active');
        $('#btnShowFinanceChart').removeClass('active');
        renderVisitsChart();
    });

    $('#btnShowFinanceChart').click(function() {
        $(this).addClass('active');
        $('#btnShowVisitsChart').removeClass('active');
        renderFinanceChart();
    });
});
</script>
<?= $this->endSection() ?>
