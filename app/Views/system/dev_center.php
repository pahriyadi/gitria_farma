<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<?php
    $counts = $anomalies['counts'] ?? [];
    $totalAnomalies = (int)($counts['unresolved_errors'] ?? 0)
                    + (int)($counts['suspicious_ips'] ?? 0)
                    + (int)($counts['midnight_activities'] ?? 0)
                    + (int)($counts['negative_stock'] ?? 0);
?>

<style>
    .dev-terminal-box {
        background: #0d1117;
        color: #c9d1d9;
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.55;
        border-radius: 10px;
        border: 1px solid #30363d;
        max-height: 440px;
        overflow-y: auto;
        padding: 14px;
    }
    .dev-log-line {
        padding: 3px 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        word-break: break-word;
    }
    .dev-log-critical { color: #ff7b72; background: rgba(248, 81, 73, 0.12); font-weight: 700; }
    .dev-log-error    { color: #ffa198; }
    .dev-log-warning  { color: #d29922; }
    .dev-log-info     { color: #79c0ff; }
    .dev-kpi-card {
        border-radius: 12px;
        transition: transform 0.15s ease;
    }
    .dev-kpi-card:hover {
        transform: translateY(-2px);
    }
    .table-clean-grid {
        border-collapse: collapse !important;
    }
    .table-clean-grid th,
    .table-clean-grid td {
        border: 1px solid #b8b8b8 !important;
    }
    body.dark-mode .table-clean-grid th,
    body.dark-mode .table-clean-grid td {
        border-color: #374151 !important;
    }
</style>

<!-- TOP DEVELOPER COMMAND HEADER -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; overflow: hidden;">
    <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 65%, #0d9488 100%); color: #ffffff;">
        <div class="mb-2 mb-md-0">
            <div class="d-flex align-items-center mb-1">
                <span class="badge badge-danger mr-2 px-2 py-1 font-weight-bold" style="font-size: 10.5px;">
                    <i class="fas fa-terminal mr-1"></i> DEVELOPER COMMAND CENTER
                </span>
                <span class="badge badge-light text-dark font-monospace" style="font-size: 10.5px;">
                    Git: <?= esc($telemetry['git_branch']) ?> @ <?= esc($telemetry['git_hash']) ?>
                </span>
                <span class="badge badge-info ml-2 font-monospace" style="font-size: 10.5px;">
                    <?= esc($integrations['latest_version']) ?>
                </span>
            </div>
            <h4 class="font-weight-bold mb-0 text-white" style="font-size: 19px;">
                Ruang Khusus Pengembang &amp; Pengawasan Sistem
            </h4>
            <small class="text-white-50">
                Pemantauan Server Real-Time, Radar Anomali Keamanan, Aktivitas Karyawan, Error Tracker, &amp; Diagnostik Database
            </small>
        </div>

        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
            <div class="custom-control custom-switch mr-2">
                <input type="checkbox" class="custom-control-input" id="switch-dev-autorefresh">
                <label class="custom-control-label text-white text-xs font-weight-bold" for="switch-dev-autorefresh" style="cursor:pointer;">
                    Auto-Refresh 30d
                </label>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light font-weight-bold" id="btn-dev-refresh-telemetry">
                <i class="fas fa-sync-alt mr-1"></i> Refresh Telemetry
            </button>
            <button type="button" class="btn btn-sm btn-warning text-dark font-weight-bold" id="btn-dev-clear-cache">
                <i class="fas fa-broom mr-1"></i> Bersihkan Cache
            </button>
        </div>
    </div>
</div>

<!-- 4 KPI SUMMARY CARDS -->
<div class="row mb-3">
    <div class="col-xl-3 col-sm-6 mb-2">
        <div class="card border shadow-xs dev-kpi-card h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs text-muted font-weight-bold text-uppercase">Error Belum Selesai</div>
                    <div class="h4 font-weight-bold mb-0 mt-1 <?= ($counts['unresolved_errors'] ?? 0) > 0 ? 'text-danger' : 'text-success' ?>" id="kpi-unresolved-err">
                        <?= (int)($counts['unresolved_errors'] ?? 0) ?>
                    </div>
                    <small class="text-muted text-xs">Tabel <code>system_error_logs</code></small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center <?= ($counts['unresolved_errors'] ?? 0) > 0 ? 'bg-danger' : 'bg-success' ?> text-white" style="width: 44px; height: 44px;">
                    <i class="fas fa-bug fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-2">
        <div class="card border shadow-xs dev-kpi-card h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs text-muted font-weight-bold text-uppercase">Login Gagal (24 Jam)</div>
                    <div class="h4 font-weight-bold mb-0 mt-1 <?= ($counts['failed_logins_24h'] ?? 0) >= 5 ? 'text-warning' : 'text-dark' ?>" id="kpi-failed-login">
                        <?= (int)($counts['failed_logins_24h'] ?? 0) ?>
                    </div>
                    <small class="text-muted text-xs"><?= (int)($counts['suspicious_ips'] ?? 0) ?> IP terdeteksi berulang</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark" style="width: 44px; height: 44px;">
                    <i class="fas fa-user-shield fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-2">
        <div class="card border shadow-xs dev-kpi-card h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs text-muted font-weight-bold text-uppercase">Aksi Berisiko / Tengah Malam</div>
                    <div class="h4 font-weight-bold mb-0 mt-1 text-primary" id="kpi-risk-act">
                        <?= (int)($counts['high_risk_actions'] ?? 0) ?> / <?= (int)($counts['midnight_activities'] ?? 0) ?>
                    </div>
                    <small class="text-muted text-xs">7 Hari (Delete/Void vs 00-05)</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 44px; height: 44px;">
                    <i class="fas fa-radar fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-2">
        <div class="card border shadow-xs dev-kpi-card h-100">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs text-muted font-weight-bold text-uppercase">Memori PHP &amp; Disk</div>
                    <div class="h5 font-weight-bold mb-0 mt-1 text-teal" id="kpi-mem-disk">
                        <?= esc($telemetry['memory_used_mb']) ?> MB &bull; <?= esc($telemetry['disk_used_pct']) ?>%
                    </div>
                    <small class="text-muted text-xs">Sisa Disk: <span id="kpi-disk-free"><?= esc($telemetry['disk_free_gb']) ?></span> GB</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-teal text-white" style="width: 44px; height: 44px;">
                    <i class="fas fa-server fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN 6-TAB DEVELOPER WORKSPACE -->
<div class="card card-outline card-teal border-0 shadow-sm">
    <div class="card-header p-0 border-bottom bg-light">
        <ul class="nav nav-tabs nav-justified" id="devCenterTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-server" role="tab">
                    <i class="fas fa-microchip text-teal mr-1"></i> 1. Server &amp; Env
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-anomalies" role="tab">
                    <i class="fas fa-triangle-exclamation text-danger mr-1"></i> 2. Radar Anomali
                    <?php if ($totalAnomalies > 0): ?>
                        <span class="badge badge-danger ml-1"><?= $totalAnomalies ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-staff" role="tab">
                    <i class="fas fa-users-viewfinder text-primary mr-1"></i> 3. Karyawan &amp; Aktivitas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-errors" role="tab">
                    <i class="fas fa-bug text-warning mr-1"></i> 4. Error &amp; Log CI4
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-database" role="tab">
                    <i class="fas fa-database text-info mr-1"></i> 5. Database &amp; Slow Query
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-3 text-truncate" data-toggle="pill" href="#tab-integrations" role="tab">
                    <i class="fas fa-plug-circle-check text-success mr-1"></i> 6. Integrasi &amp; API
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-3 p-md-4">
        <div class="tab-content">

            <!-- ================================================================= -->
            <!-- TAB 1: SERVER & ENVIRONMENT TELEMETRY                             -->
            <!-- ================================================================= -->
            <div class="tab-pane fade show active" id="tab-server" role="tabpanel">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card border shadow-none h-100">
                            <div class="card-header bg-light py-2 px-3">
                                <h6 class="font-weight-bold mb-0 text-sm"><i class="fas fa-server text-teal mr-1"></i> Spesifikasi Runtime &amp; Server</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-clean-grid mb-0" style="font-size: 13px;">
                                    <tbody>
                                        <tr><td class="font-weight-bold" style="width: 42%;">PHP Version</td><td><span class="badge badge-primary"><?= esc($telemetry['php_version']) ?></span> (OPcache: <?= $telemetry['opcache_enabled'] ? '<span class="text-success font-weight-bold">Aktif</span>' : '<span class="text-muted">Nonaktif</span>' ?>)</td></tr>
                                        <tr><td class="font-weight-bold">CodeIgniter Version</td><td><code>v<?= esc($telemetry['ci_version']) ?></code> (Env: <span class="badge badge-dark"><?= esc($telemetry['environment']) ?></span>)</td></tr>
                                        <tr><td class="font-weight-bold">MySQL / MariaDB</td><td><code><?= esc($telemetry['mysql_version']) ?></code></td></tr>
                                        <tr><td class="font-weight-bold">Web Server</td><td><?= esc($telemetry['server_software']) ?></td></tr>
                                        <tr><td class="font-weight-bold">Sistem Operasi</td><td><?= esc($telemetry['os_info']) ?></td></tr>
                                        <tr><td class="font-weight-bold">Zona Waktu &amp; Jam Server</td><td><?= esc($telemetry['timezone']) ?> &bull; <strong class="font-monospace"><?= esc($telemetry['server_time']) ?></strong></td></tr>
                                        <tr><td class="font-weight-bold">Base URL Aktif</td><td><code><?= esc($telemetry['base_url']) ?></code></td></tr>
                                        <tr><td class="font-weight-bold">Batas PHP (INI)</td><td>Memory: <code><?= esc($telemetry['memory_limit']) ?></code> &bull; Upload: <code><?= esc($telemetry['upload_max']) ?></code> &bull; Max Exec: <code><?= esc($telemetry['max_exec_time']) ?></code></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2 px-3">
                                <h6 class="font-weight-bold mb-0 text-sm"><i class="fas fa-hard-drive text-info mr-1"></i> Kapasitas Penyimpanan &amp; Memori</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between text-xs font-weight-bold mb-1">
                                        <span>Penggunaan Disk Server</span>
                                        <span><?= esc($telemetry['disk_used_pct']) ?>% Terpakai (Sisa <?= esc($telemetry['disk_free_gb']) ?> GB / <?= esc($telemetry['disk_total_gb']) ?> GB)</span>
                                    </div>
                                    <div class="progress" style="height: 10px; border-radius: 6px;">
                                        <div class="progress-bar <?= $telemetry['disk_used_pct'] > 85 ? 'bg-danger' : 'bg-teal' ?>" style="width: <?= esc($telemetry['disk_used_pct']) ?>%"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-xs text-muted">
                                    <span>Memori Request Saat Ini: <strong><?= esc($telemetry['memory_used_mb']) ?> MB</strong></span>
                                    <span>Peak Memori: <strong><?= esc($telemetry['memory_peak_mb']) ?> MB</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="card border shadow-none">
                            <div class="card-header bg-light py-2 px-3">
                                <h6 class="font-weight-bold mb-0 text-sm"><i class="fas fa-puzzle-piece text-warning mr-1"></i> Ekstensi PHP &amp; Izin Folder (Writable)</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2 text-xs font-weight-bold text-muted">Ekstensi Wajib:</div>
                                <div class="d-flex flex-wrap mb-3" style="gap: 6px;">
                                    <?php foreach ($telemetry['extensions'] as $ext => $loaded): ?>
                                        <span class="badge <?= $loaded ? 'badge-success' : 'badge-danger' ?> px-2 py-1">
                                            <i class="fas <?= $loaded ? 'fa-check' : 'fa-times' ?> mr-1"></i> <?= esc($ext) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mb-2 text-xs font-weight-bold text-muted">Izin Tulis Direktori:</div>
                                <div class="d-flex flex-wrap" style="gap: 6px;">
                                    <?php foreach ($telemetry['dir_checks'] as $dir => $writable): ?>
                                        <span class="badge <?= $writable ? 'badge-info' : 'badge-danger' ?> px-2 py-1">
                                            <i class="fas <?= $writable ? 'fa-folder-open' : 'fa-lock' ?> mr-1"></i> <?= esc($dir) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 2: RADAR ANOMALI & HAL TIDAK WAJAR                            -->
            <!-- ================================================================= -->
            <div class="tab-pane fade" id="tab-anomalies" role="tabpanel">
                <!-- Operational Anomalies Alert Row -->
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <div class="alert <?= ($counts['negative_stock'] ?? 0) > 0 ? 'alert-danger' : 'alert-light border' ?> mb-0 p-3">
                            <div class="font-weight-bold text-sm"><i class="fas fa-pills mr-1"></i> Anomali Stok Obat Minus</div>
                            <div class="h4 font-weight-bold my-1"><?= (int)($counts['negative_stock'] ?? 0) ?> Batch</div>
                            <small>Batch obat dengan nilai stok &lt; 0 di tabel <code>medicine_batches</code>.</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="alert <?= ($counts['stale_billings'] ?? 0) > 0 ? 'alert-warning' : 'alert-light border' ?> mb-0 p-3">
                            <div class="font-weight-bold text-sm"><i class="fas fa-file-invoice-dollar mr-1"></i> Tagihan Menggantung (&gt; 3 Hari)</div>
                            <div class="h4 font-weight-bold my-1"><?= (int)($counts['stale_billings'] ?? 0) ?> Transaksi</div>
                            <small>Billing berstatus <code>open</code> yang belum diselesaikan kasir lebih dari 3 hari.</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="alert <?= ($counts['users_without_email'] ?? 0) > 0 ? 'alert-info' : 'alert-light border' ?> mb-0 p-3">
                            <div class="font-weight-bold text-sm"><i class="fab fa-google mr-1"></i> Akun Aktif Tanpa Email Google</div>
                            <div class="h4 font-weight-bold my-1"><?= (int)($counts['users_without_email'] ?? 0) ?> Akun</div>
                            <small>Akun staf aktif yang belum memiliki email untuk Login Google SSO.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Suspicious IPs & Failed Logins -->
                    <div class="col-lg-6 mb-4">
                        <h6 class="font-weight-bold text-danger mb-2"><i class="fas fa-user-lock mr-1"></i> Percobaan Login Gagal Berulang (24 Jam)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>IP Address</th>
                                        <th>Keterangan Percobaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anomalies['failed_logins_list'] as $fl): ?>
                                        <tr>
                                            <td class="text-nowrap"><?= esc($fl->created_at) ?></td>
                                            <td><code class="text-danger font-weight-bold"><?= esc($fl->ip_address) ?></code></td>
                                            <td><?= esc($fl->new_value) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Midnight Activities -->
                    <div class="col-lg-6 mb-4">
                        <h6 class="font-weight-bold text-warning mb-2"><i class="fas fa-moon mr-1"></i> Aktivitas Tengah Malam (00:00 - 05:00) - 7 Hari</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>Aksi / Modul</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($anomalies['midnight_activities'] as $ma): ?>
                                        <tr>
                                            <td class="text-nowrap"><?= esc($ma->created_at) ?></td>
                                            <td><strong><?= esc($ma->username ?: 'Guest') ?></strong> <small class="text-muted">(<?= esc($ma->role_name ?: '-') ?>)</small></td>
                                            <td><span class="badge badge-dark"><?= esc($ma->action) ?></span> <?= esc($ma->module) ?></td>
                                            <td><code><?= esc($ma->ip_address) ?></code></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- High Risk Actions (DELETE, VOID, RESET) -->
                <h6 class="font-weight-bold text-dark mt-2 mb-2"><i class="fas fa-radiation text-danger mr-1"></i> Aktivitas Berisiko Tinggi (DELETE / VOID / RESET / RESTORE) - 7 Hari</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Modul &amp; Tabel</th>
                                <th>Detail Perubahan</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anomalies['high_risk_actions'] as $hra): ?>
                                <tr>
                                    <td class="text-nowrap"><?= esc($hra->created_at) ?></td>
                                    <td><strong><?= esc($hra->username ?: 'System') ?></strong> <small class="text-muted">(<?= esc($hra->role_name ?: '-') ?>)</small></td>
                                    <td><span class="badge badge-danger"><?= esc($hra->action) ?></span></td>
                                    <td><?= esc($hra->module) ?> <small class="text-muted">(<?= esc($hra->table_name) ?> #<?= esc($hra->record_id) ?>)</small></td>
                                    <td><?= esc($hra->new_value) ?></td>
                                    <td><code><?= esc($hra->ip_address) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 3: PENGAWASAN KARYAWAN & AKTIVITAS LIVE                       -->
            <!-- ================================================================= -->
            <div class="tab-pane fade" id="tab-staff" role="tabpanel">
                <h6 class="font-weight-bold text-primary mb-2"><i class="fas fa-id-card-clip mr-1"></i> Status Kehadiran Karyawan Hari Ini (<?= date('d M Y') ?>)</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen &amp; Jabatan</th>
                                <th>Kontak &amp; Email</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status Presensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><code><?= esc($emp->nip) ?></code></td>
                                    <td class="font-weight-bold"><?= esc($emp->name) ?></td>
                                    <td><span class="badge badge-light border"><?= esc($emp->department) ?></span> <small class="text-muted"><?= esc($emp->position) ?></small></td>
                                    <td><?= esc($emp->phone ?: '-') ?> <br><small class="text-muted"><?= esc($emp->email ?: '-') ?></small></td>
                                    <td class="text-nowrap font-monospace"><?= esc($emp->check_in_time ?: '-') ?></td>
                                    <td class="text-nowrap font-monospace"><?= esc($emp->check_out_time ?: '-') ?></td>
                                    <td>
                                        <?php if ($emp->att_status === 'present'): ?>
                                            <span class="badge badge-success">HADIR</span>
                                        <?php elseif ($emp->att_status === 'late'): ?>
                                            <span class="badge badge-warning text-dark">TERLAMBAT (<?= (int)$emp->late_minutes ?>m)</span>
                                        <?php elseif ($emp->att_status): ?>
                                            <span class="badge badge-info text-uppercase"><?= esc($emp->att_status) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">BELUM CHECK-IN</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-stream mr-1"></i> Jejak Aktivitas Pengguna Terbaru (150 Log Terakhir)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Aksi</th>
                                <th>Modul</th>
                                <th>Keterangan</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $al): ?>
                                <?php
                                    $actBadge = 'badge-secondary';
                                    if (in_array($al->action, ['LOGIN', 'LOGIN_GOOGLE'], true)) $actBadge = 'badge-success';
                                    elseif ($al->action === 'LOGIN_FAILED') $actBadge = 'badge-danger';
                                    elseif ($al->action === 'CREATE') $actBadge = 'badge-primary';
                                    elseif ($al->action === 'UPDATE') $actBadge = 'badge-info';
                                    elseif (in_array($al->action, ['DELETE', 'VOID'], true)) $actBadge = 'badge-danger';
                                ?>
                                <tr>
                                    <td class="text-nowrap"><?= esc($al->created_at) ?></td>
                                    <td class="font-weight-bold"><?= esc($al->username ?: 'Guest') ?></td>
                                    <td><small class="text-muted"><?= esc($al->role_name ?: '-') ?></small></td>
                                    <td><span class="badge <?= $actBadge ?>"><?= esc($al->action) ?></span></td>
                                    <td><?= esc($al->module) ?></td>
                                    <td><?= esc($al->new_value ?: '-') ?></td>
                                    <td><code><?= esc($al->ip_address) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 4: ERROR TRACKER & LOG SERVER CI4                             -->
            <!-- ================================================================= -->
            <div class="tab-pane fade" id="tab-errors" role="tabpanel">
                <h6 class="font-weight-bold text-danger mb-2"><i class="fas fa-bug mr-1"></i> Error Tracker Terdeduplikasi (<code>system_error_logs</code>)</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>Terakhir Terjadi</th>
                                <th>Level</th>
                                <th>Pesan &amp; Lokasi File</th>
                                <th>Frekuensi</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($errorLogs as $err): ?>
                                <tr>
                                    <td class="text-nowrap"><?= esc($err->updated_at ?: $err->created_at) ?></td>
                                    <td><span class="badge badge-danger"><?= esc(strtoupper($err->error_level ?? 'ERROR')) ?></span></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($err->message) ?></div>
                                        <small class="text-muted font-monospace"><?= esc($err->file) ?>:<?= esc($err->line) ?> (<?= esc($err->url) ?>)</small>
                                    </td>
                                    <td><span class="badge badge-dark"><?= (int)($err->count ?? 1) ?>x</span></td>
                                    <td>
                                        <?php if (!empty($err->is_resolved)): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark">Belum Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-xs btn-outline-info btn-view-trace" data-trace="<?= esc($err->trace ?? 'Tidak ada stack trace.') ?>" data-msg="<?= esc($err->message) ?>">
                                            <i class="fas fa-code"></i> Trace
                                        </button>
                                        <?php if (empty($err->is_resolved)): ?>
                                            <button type="button" class="btn btn-xs btn-success btn-resolve-err" data-id="<?= (int)$err->id ?>">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- CI4 File Log Tailer -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-dark mb-1">
                        <i class="fas fa-file-lines text-teal mr-1"></i> File Log Harian Server (<code>writable/logs/</code>)
                    </h6>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <select id="select-ci4-logfile" class="form-control form-control-sm" data-native="true" style="width: 240px;">
                            <?php if (empty($logFiles)): ?>
                                <option value="">Belum ada file log harian</option>
                            <?php else: foreach ($logFiles as $lf): ?>
                                <option value="<?= esc($lf['name']) ?>" <?= $lf['name'] === $selectedLogFile ? 'selected' : '' ?>>
                                    <?= esc($lf['name']) ?> (<?= esc($lf['size_kb']) ?> KB)
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                        <?php if (!empty($selectedLogFile)): ?>
                            <form action="<?= base_url('system/dev-center/clear-log') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus file log harian ini?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="file" id="clear-log-filename" value="<?= esc($selectedLogFile) ?>">
                                <button type="submit" class="btn btn-xs btn-outline-danger font-weight-bold">
                                    <i class="fas fa-trash mr-1"></i> Hapus Log
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-2 d-flex" style="gap: 8px;" id="ci4-log-stats">
                    <span class="badge badge-danger">CRITICAL: <?= (int)($logPreview['stats']['critical'] ?? 0) ?></span>
                    <span class="badge badge-warning text-dark">ERROR: <?= (int)($logPreview['stats']['error'] ?? 0) ?></span>
                    <span class="badge badge-info">WARNING: <?= (int)($logPreview['stats']['warning'] ?? 0) ?></span>
                </div>

                <div class="dev-terminal-box" id="ci4-log-viewer">
                    <?php if (empty($logPreview['lines'])): ?>
                        <div class="text-muted">Tidak ada entri log di file ini. Sistem berjalan bersih!</div>
                    <?php else: foreach ($logPreview['lines'] as $line): ?>
                        <?php
                            $cls = '';
                            if (stripos($line, 'CRITICAL') === 0) $cls = 'dev-log-critical';
                            elseif (stripos($line, 'ERROR') === 0) $cls = 'dev-log-error';
                            elseif (stripos($line, 'WARNING') === 0) $cls = 'dev-log-warning';
                            elseif (stripos($line, 'INFO') === 0) $cls = 'dev-log-info';
                        ?>
                        <div class="dev-log-line <?= $cls ?>"><?= esc($line) ?></div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 5: DATABASE & SLOW QUERIES                                    -->
            <!-- ================================================================= -->
            <div class="tab-pane fade" id="tab-database" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 bg-light border rounded">
                    <div>
                        <strong class="text-dark">Database Aktif: <code><?= esc($dbStats['db_name']) ?></code></strong>
                        <div class="text-xs text-muted mt-1">
                            Total Tabel: <strong><?= (int)$dbStats['total_tables'] ?></strong> &bull;
                            Estimasi Total Baris: <strong><?= number_format($dbStats['total_rows']) ?></strong> &bull;
                            Total Ukuran: <strong><?= esc($dbStats['total_size_mb']) ?> MB</strong>
                        </div>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <a href="<?= base_url('system/database') ?>" class="btn btn-sm btn-teal font-weight-bold text-white">
                            <i class="fas fa-database mr-1"></i> Kelola Backup &amp; Restore
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-table text-info mr-1"></i> Top 20 Tabel Terbesar</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Tabel</th>
                                        <th class="text-right">Jumlah Baris</th>
                                        <th class="text-right">Ukuran (MB)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbStats['top_tables'] as $tbl): ?>
                                        <tr>
                                            <td><code><?= esc($tbl->table_name) ?></code></td>
                                            <td class="text-right"><?= number_format((int)$tbl->table_rows) ?></td>
                                            <td class="text-right font-weight-bold"><?= esc($tbl->size_mb) ?> MB</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <h6 class="font-weight-bold text-warning mb-2"><i class="fas fa-gauge-high mr-1"></i> Slow Query Monitor (<code>system_slow_queries</code>)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-clean-grid datatable" style="font-size: 12.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Durasi</th>
                                        <th>Query &amp; Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($slowQueries as $sq): ?>
                                        <tr>
                                            <td class="text-nowrap"><?= esc($sq->created_at) ?></td>
                                            <td><span class="badge badge-warning text-dark"><?= esc($sq->execution_time_ms) ?> ms</span></td>
                                            <td>
                                                <code class="d-block text-truncate" style="max-width: 280px;" title="<?= esc($sq->query_sql) ?>"><?= esc($sq->query_sql) ?></code>
                                                <small class="text-muted"><?= esc($sq->caller_location) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- TAB 6: INTEGRASI & API STATUS                                     -->
            <!-- ================================================================= -->
            <div class="tab-pane fade" id="tab-integrations" role="tabpanel">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border shadow-none h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold mb-0"><i class="fab fa-google text-primary mr-1"></i> Login Google SSO</h6>
                                    <span class="badge <?= $integrations['google_enabled'] ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= $integrations['google_enabled'] ? 'AKTIF' : 'NONAKTIF' ?>
                                    </span>
                                </div>
                                <div class="text-xs text-muted mb-2">
                                    Mode: <strong><?= esc($integrations['google_mode']) ?></strong><br>
                                    Client ID: <?= $integrations['google_client_ok'] ? '<span class="text-success font-weight-bold">Terpasang</span>' : '<span class="text-danger font-weight-bold">Belum Diisi</span>' ?>
                                </div>
                                <a href="<?= base_url('system/settings') ?>" class="btn btn-xs btn-outline-primary font-weight-bold">Atur di Pengaturan</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border shadow-none h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold mb-0"><i class="fab fa-whatsapp text-success mr-1"></i> WhatsApp Gateway</h6>
                                    <span class="badge <?= $integrations['wa_token_ok'] ? 'badge-success' : 'badge-warning text-dark' ?>">
                                        <?= $integrations['wa_token_ok'] ? 'TOKEN OK' : 'SIMULASI' ?>
                                    </span>
                                </div>
                                <div class="text-xs text-muted mb-2">
                                    Provider: <strong><?= esc(strtoupper($integrations['wa_provider'])) ?></strong><br>
                                    Pengirim: <code><?= esc($integrations['wa_sender']) ?></code> &bull; Total Log: <strong><?= (int)$integrations['wa_total_logs'] ?></strong>
                                </div>
                                <a href="<?= base_url('system/whatsapp') ?>" class="btn btn-xs btn-outline-success font-weight-bold">Buka Log WhatsApp</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card border shadow-none h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold mb-0"><i class="fas fa-heart-pulse text-danger mr-1"></i> SATUSEHAT Kemenkes</h6>
                                    <span class="badge <?= $integrations['ss_active'] ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= esc(strtoupper($integrations['ss_mode'])) ?>
                                    </span>
                                </div>
                                <div class="text-xs text-muted mb-2">
                                    Status: <strong><?= $integrations['ss_active'] ? 'Aktif' : 'Nonaktif' ?></strong><br>
                                    Org ID: <code><?= esc($integrations['ss_org_id']) ?></code>
                                </div>
                                <a href="<?= base_url('system/settings') ?>" class="btn btn-xs btn-outline-danger font-weight-bold">Konfigurasi FHIR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DETAIL STACK TRACE ERROR -->
<div class="modal fade" id="modalDevTrace" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold mb-0"><i class="fas fa-code text-warning mr-1"></i> Detail Stack Trace Error</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="font-weight-bold text-danger mb-2" id="dev-trace-msg"></div>
                <pre class="dev-terminal-box mb-0" id="dev-trace-body" style="white-space: pre-wrap;"></pre>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var devAutoTimer = null;

    function refreshTelemetry() {
        var $btn = $('#btn-dev-refresh-telemetry');
        $btn.prop('disabled', true).find('i').addClass('fa-spin');
        $.getJSON('<?= base_url('system/dev-center/telemetry') ?>', function(res) {
            if (res && res.status === 'success') {
                $('#kpi-unresolved-err').text(res.unresolved_err);
                $('#kpi-failed-login').text(res.failed_login_24h);
                $('#kpi-risk-act').text(res.risk_actions + ' / ' + res.midnight_act);
                $('#kpi-mem-disk').html(res.memory_used_mb + ' MB &bull; ' + res.disk_used_pct + '%');
                $('#kpi-disk-free').text(res.disk_free_gb);
            }
        }).always(function() {
            $btn.prop('disabled', false).find('i').removeClass('fa-spin');
        });
    }

    function initDevCenterPage() {
        $(document).off('.devCenter');

        $(document).on('click.devCenter', '#btn-dev-refresh-telemetry', function() {
            refreshTelemetry();
        });

        $(document).on('change.devCenter', '#switch-dev-autorefresh', function() {
            if (devAutoTimer) {
                clearInterval(devAutoTimer);
                devAutoTimer = null;
            }
            if ($(this).is(':checked')) {
                devAutoTimer = setInterval(function() {
                    if (!$('#switch-dev-autorefresh').length || !$('#switch-dev-autorefresh').is(':checked')) {
                        clearInterval(devAutoTimer);
                        devAutoTimer = null;
                        return;
                    }
                    refreshTelemetry();
                }, 30000);
            }
        });

        $(document).on('click.devCenter', '#btn-dev-clear-cache', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Membersihkan...');
            $.post('<?= base_url('system/dev-center/clear-cache') ?>', {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function(res) {
                alert((res && res.message) ? res.message : 'Cache berhasil dibersihkan.');
                refreshTelemetry();
            }).fail(function() {
                alert('Gagal membersihkan cache.');
            }).always(function() {
                $btn.prop('disabled', false).html('<i class="fas fa-broom mr-1"></i> Bersihkan Cache');
            });
        });

        $(document).on('click.devCenter', '.btn-view-trace', function() {
            $('#dev-trace-msg').text($(this).attr('data-msg') || '');
            $('#dev-trace-body').text($(this).attr('data-trace') || '');
            $('#modalDevTrace').modal('show');
        });

        $(document).on('click.devCenter', '.btn-resolve-err', function() {
            var $btn = $(this);
            var id = $btn.attr('data-id');
            $.post('<?= base_url('system/dev-center/resolve-error/') ?>' + id, {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }, function(res) {
                if (res && res.status === 'success') {
                    var $tr = $btn.closest('tr');
                    $tr.find('td:eq(4)').html('<span class="badge badge-success">Selesai</span>');
                    $btn.remove();
                    refreshTelemetry();
                }
            });
        });

        $(document).on('change.devCenter', '#select-ci4-logfile', function() {
            var file = $(this).val();
            if (!file) return;
            $('#clear-log-filename').val(file);
            $('#ci4-log-viewer').html('<div class="text-info"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat ' + file + '...</div>');
            $.getJSON('<?= base_url('system/dev-center/read-log') ?>', { file: file }, function(res) {
                if (res && res.status === 'success') {
                    $('#ci4-log-stats').html(
                        '<span class="badge badge-danger">CRITICAL: ' + res.stats.critical + '</span> ' +
                        '<span class="badge badge-warning text-dark">ERROR: ' + res.stats.error + '</span> ' +
                        '<span class="badge badge-info">WARNING: ' + res.stats.warning + '</span>'
                    );
                    if (!res.lines.length) {
                        $('#ci4-log-viewer').html('<div class="text-muted">Tidak ada entri log di file ini.</div>');
                        return;
                    }
                    var html = res.lines.map(function(l) {
                        var cls = '';
                        if (/^CRITICAL/i.test(l)) cls = 'dev-log-critical';
                        else if (/^ERROR/i.test(l)) cls = 'dev-log-error';
                        else if (/^WARNING/i.test(l)) cls = 'dev-log-warning';
                        else if (/^INFO/i.test(l)) cls = 'dev-log-info';
                        return '<div class="dev-log-line ' + cls + '">' + $('<div>').text(l).html() + '</div>';
                    }).join('');
                    $('#ci4-log-viewer').html(html);
                }
            });
        });

        $('a[data-toggle="pill"]').off('shown.bs.tab.devCenter').on('shown.bs.tab.devCenter', function() {
            if ($.fn.dataTable) {
                try {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                } catch (e) {}
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDevCenterPage);
    } else {
        initDevCenterPage();
    }
})();
</script>
<?= $this->endSection() ?>
