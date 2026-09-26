<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-md-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-database text-teal mr-2"></i> Status Database, Backup & Sinkronisasi
                </h1>
                <small class="text-muted">Pencadangan otomatis harian & bulanan ultra-hemat ruang (Gzip Stream), pemantauan kesehatan tabel, dan manajemen rotasi arsip</small>
            </div>
            <div class="col-md-6 text-md-right mt-2 mt-md-0">
                <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modal-instant-backup">
                    <i class="fas fa-file-zipper mr-1"></i> Backup Sekarang
                </button>
                <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modal-backup-settings">
                    <i class="fas fa-clock-rotate-left mr-1"></i> Jadwal & Retensi
                </button>
                <form action="<?= base_url('system/optimize-db') ?>" method="post" class="d-inline mr-1" onsubmit="return confirm('Jalankan optimasi dan defragmentasi seluruh tabel database?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-purple btn-sm font-weight-bold shadow-sm text-white" style="background:#8b5cf6;" title="Optimasi & Defragmentasi Tabel">
                        <i class="fas fa-bolt mr-1"></i> Defrag
                    </button>
                </form>
                <form action="<?= base_url('system/sync-data') ?>" method="post" class="d-inline mr-1" onsubmit="return confirm('Sinkronkan integritas relasi referensi dan parameter sistem?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-dark btn-sm font-weight-bold shadow-sm" title="Sinkronisasi Integritas Data">
                        <i class="fas fa-arrows-rotate mr-1"></i> Sinkronisasi
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modal-reset-transactions" title="Pengosongan Data Transaksi">
                    <i class="fas fa-trash-can mr-1"></i> Reset Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- 4 KPI SUMMARY CARDS -->
        <div class="row mb-4">
            <!-- CARD 1: STATUS AUTO BACKUP -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0d9488 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Otomatisasi Backup</span>
                                <div class="mt-1 mb-1">
                                    <?php if (($backupSettings['backup_auto_daily'] ?? '1') === '1' && ($backupSettings['backup_auto_monthly'] ?? '1') === '1'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-circle-check mr-1"></i> Harian & Bulanan Aktif</span>
                                    <?php elseif (($backupSettings['backup_auto_daily'] ?? '1') === '1'): ?>
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-circle-check mr-1"></i> Harian Aktif</span>
                                    <?php elseif (($backupSettings['backup_auto_monthly'] ?? '1') === '1'): ?>
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-circle-check mr-1"></i> Bulanan Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1"><i class="fas fa-circle-pause mr-1"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-muted" style="line-height: 1.4;">
                                    <div><i class="fas fa-sun text-warning mr-1"></i> Harian: <strong><?= !empty($backupSettings['backup_last_daily']) ? date('d/m/Y H:i', strtotime($backupSettings['backup_last_daily'])) : 'Belum pernah' ?></strong></div>
                                    <div><i class="fas fa-calendar-days text-primary mr-1"></i> Bulanan: <strong><?= !empty($backupSettings['backup_last_monthly']) ? date('d/m/Y H:i', strtotime($backupSettings['backup_last_monthly'])) : 'Belum pernah' ?></strong></div>
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-clock-rotate-left fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: PENYIMPANAN & EFISIENSI -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0284c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-xs font-weight-bold text-info text-uppercase">Arsip & Efisiensi Ruang</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalBackupHuman ?> <small class="text-muted text-xs">(<?= count($backupList) ?> Berkas)</small></h4>
                                <div class="text-xs text-success font-weight-bold mt-1">
                                    <i class="fas fa-file-zipper mr-1"></i> Gzip Stream ~90% Hemat
                                </div>
                                <div class="text-xs text-muted">
                                    <?= $dailyCount ?> Harian | <?= $monthlyCount ?> Bulanan | <?= $manualCount ?> Manual
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-box-archive fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 3: BASIS DATA & BEBAN SERVER -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Ukuran Database Aktif</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= number_format($totalSizeMb, 2) ?> <small class="text-muted">MB</small></h4>
                                <div class="text-xs text-muted mt-1">
                                    <?= number_format($totalRows) ?> Records di <?= $totalTables ?> Tabel
                                </div>
                                <div class="text-xs text-success">
                                    <i class="fas fa-feather mr-1"></i> Ekspor Chunk 500 Row (Ringan)
                                </div>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-hard-drive fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 4: KEBIJAKAN RETENSI -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #8b5cf6 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="text-xs font-weight-bold text-purple text-uppercase">Kebijakan Retensi</span>
                                <h6 class="font-weight-bold text-dark mb-0 mt-1">
                                    <i class="fas fa-shield-halved text-success mr-1"></i> Auto-Rotation Aktif
                                </h6>
                                <div class="text-xs text-muted mt-1" style="line-height: 1.4;">
                                    <div>Simpan: <strong><?= esc($backupSettings['backup_daily_retention_days'] ?? '7') ?> Hari</strong> Harian</div>
                                    <div>Simpan: <strong><?= esc($backupSettings['backup_monthly_retention_months'] ?? '12') ?> Bln</strong> Bulanan</div>
                                </div>
                                <small class="text-muted">File usang dihapus otomatis</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-purple">
                                <i class="fas fa-recycle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CONTAINER -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0 bg-white">
                <ul class="nav nav-tabs" id="custom-tabs-database" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold text-dark py-3 px-4" id="tab-backups-tab" data-toggle="pill" href="#tab-backups" role="tab" aria-controls="tab-backups" aria-selected="true">
                            <i class="fas fa-box-archive text-teal mr-2"></i> Pusat Berkas Cadangan (<?= count($backupList) ?> Arsip)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-dark py-3 px-4" id="tab-tables-tab" data-toggle="pill" href="#tab-tables" role="tab" aria-controls="tab-tables" aria-selected="false">
                            <i class="fas fa-server text-info mr-2"></i> Diagnostik & Status Tabel (<?= $totalTables ?> Tabel)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-dark py-3 px-4" id="tab-automation-tab" data-toggle="pill" href="#tab-automation" role="tab" aria-controls="tab-automation" aria-selected="false">
                            <i class="fas fa-robot text-purple mr-2"></i> Jadwal Cron & Integrasi Server
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-database-content">

                    <!-- TAB 1: PUSAT BERKAS CADANGAN -->
                    <div class="tab-pane fade show active" id="tab-backups" role="tabpanel" aria-labelledby="tab-backups-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">
                                    <i class="fas fa-folder-open text-teal mr-1"></i> Direktori Penyimpanan Terproteksi: <code>writable/backups/</code>
                                </h6>
                                <small class="text-muted">Berkas terkompresi secara streaming menggunakan format <code>.sql.gz</code> tingkat 9. Direktori dilindungi anti-akses publik.</small>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <button type="button" class="btn btn-teal btn-sm font-weight-bold mr-1" data-toggle="modal" data-target="#modal-instant-backup">
                                    <i class="fas fa-plus-circle mr-1"></i> Buat Backup Baru
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#modal-backup-settings">
                                    <i class="fas fa-gear mr-1"></i> Atur Retensi
                                </button>
                            </div>
                        </div>

                        <?php if (empty($backupList)): ?>
                            <div class="text-center py-5 bg-light rounded border">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h6 class="font-weight-bold text-dark">Belum Ada Berkas Backup Tersimpan</h6>
                                <p class="text-muted text-xs mb-3">Klik tombol di bawah untuk membuat snapshot database terkompresi pertama Anda.</p>
                                <button type="button" class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modal-instant-backup">
                                    <i class="fas fa-file-zipper mr-1"></i> Buat Backup Sekarang
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover datatable" style="font-size: 13px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">NO</th>
                                            <th>Nama Berkas Arsip</th>
                                            <th style="width: 130px;" class="text-center">Tipe / Kategori</th>
                                            <th style="width: 140px;" class="text-right">Ukuran Terkompresi</th>
                                            <th style="width: 160px;">Waktu Dibuat</th>
                                            <th style="width: 120px;" class="text-center">Umur Berkas</th>
                                            <th style="width: 180px;" class="text-center">Aksi Manajemen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($backupList as $b): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-zipper text-teal fa-lg mr-2"></i>
                                                        <div>
                                                            <strong class="text-dark" style="font-family: monospace; font-size: 13px;"><?= esc($b['filename']) ?></strong>
                                                            <?php if ($b['is_gzip']): ?>
                                                                <span class="badge badge-success text-xxs ml-1"><i class="fas fa-bolt mr-1"></i>GZIP L9</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary text-xxs ml-1">PLAIN SQL</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($b['type'] === 'daily'): ?>
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-sun mr-1"></i>Harian (Daily)</span>
                                                    <?php elseif ($b['type'] === 'monthly'): ?>
                                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-calendar-days mr-1"></i>Bulanan (Monthly)</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-user-gear mr-1"></i>Manual Snapshot</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold text-teal">
                                                    <?= $b['size_human'] ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted font-weight-bold">
                                                        <i class="far fa-calendar mr-1"></i><?= date('d/m/Y H:i:s', $b['created_time']) ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-light border text-muted"><?= $b['age_human'] ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url('system/backup/download/' . urlencode($b['filename'])) ?>" class="btn btn-outline-teal font-weight-bold" title="Unduh File Arsip">
                                                            <i class="fas fa-download"></i> Unduh
                                                        </a>
                                                        <button type="button" class="btn btn-outline-warning font-weight-bold btn-restore-trigger" data-filename="<?= esc($b['filename']) ?>" title="Pulihkan / Restore Database">
                                                            <i class="fas fa-rotate-left"></i> Restore
                                                        </button>
                                                        <form action="<?= base_url('system/backup/delete/' . urlencode($b['filename'])) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus permanen file backup <?= esc($b['filename']) ?>?');">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-outline-danger font-weight-bold" title="Hapus Berkas">
                                                                <i class="fas fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: DIAGNOSTIK & STATUS TABEL -->
                    <div class="tab-pane fade" id="tab-tables" role="tabpanel" aria-labelledby="tab-tables-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">NO</th>
                                        <th>Nama Tabel</th>
                                        <th>Engine</th>
                                        <th class="text-right">Jumlah Baris</th>
                                        <th class="text-right">Ukuran Data (MB)</th>
                                        <th>Collation</th>
                                        <th class="text-center">Auto Increment</th>
                                        <th>Terakhir Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($tables as $t): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-teal" style="font-family: monospace;"><?= esc($t->name) ?></strong>
                                            </td>
                                            <td><span class="badge badge-light border"><?= esc($t->engine) ?></span></td>
                                            <td class="text-right font-weight-bold"><?= number_format($t->row_count) ?></td>
                                            <td class="text-right font-weight-bold text-dark"><?= number_format($t->size_mb, 3) ?> MB</td>
                                            <td><small class="text-muted"><?= esc($t->collation) ?></small></td>
                                            <td class="text-center"><?= $t->auto_increment ? number_format($t->auto_increment) : '-' ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $t->update_time ? date('d/m/Y H:i', strtotime($t->update_time)) : ($t->create_time ? date('d/m/Y H:i', strtotime($t->create_time)) : '-') ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: JADWAL CRON & INTEGRASI SERVER -->
                    <div class="tab-pane fade" id="tab-automation" role="tabpanel" aria-labelledby="tab-automation-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border shadow-none bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-terminal text-teal mr-1"></i> Opsi 1: CLI Spark Cron (Linux / VPS / cPanel)
                                        </h6>
                                        <p class="text-muted text-xs mb-2">
                                            Jalankan backup otomatis secara background melalui <strong>Linux Crontab</strong> atau <strong>cPanel Cron Jobs</strong> setiap malam pukul 01:00:
                                        </p>
                                        <div class="bg-dark p-2 rounded mb-2 position-relative">
                                            <code class="text-success text-xs d-block" id="cron-cli-cmd">0 1 * * * php <?= esc(ROOTPATH) ?>spark cron:database-backup > /dev/null 2>&1</code>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-dark font-weight-bold" onclick="navigator.clipboard.writeText(document.getElementById('cron-cli-cmd').innerText); alert('Perintah CLI Cron berhasil disalin!');">
                                            <i class="fas fa-copy mr-1"></i> Salin Perintah Crontab
                                        </button>
                                    </div>
                                </div>

                                <div class="card border shadow-none bg-light">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fab fa-windows text-info mr-1"></i> Opsi 2: Windows Task Scheduler (Lokal / Server Windows)
                                        </h6>
                                        <p class="text-muted text-xs mb-2">
                                            Bila menggunakan XAMPP di server Windows lokal, buat Basic Task harian yang mengeksekusi:
                                        </p>
                                        <div class="bg-dark p-2 rounded mb-2">
                                            <code class="text-warning text-xs d-block">php.exe "<?= esc(ROOTPATH) ?>spark" cron:database-backup</code>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border shadow-none bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-globe text-primary mr-1"></i> Opsi 3: Webhook / Web Cron (UptimeRobot / Cron-Job.org)
                                        </h6>
                                        <p class="text-muted text-xs mb-2">
                                            Jika server hosting tidak mengizinkan akses shell CLI, gunakan layanan pemanggil Web Cron gratis dengan URL terlindungi token:
                                        </p>
                                        <div class="bg-dark p-2 rounded mb-2">
                                            <code class="text-info text-xs d-block text-break" id="cron-web-url"><?= base_url('api/backup/cron?token=' . esc($backupSettings['backup_cron_token'] ?? 'sawamawa_cron_backup')) ?></code>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold" onclick="navigator.clipboard.writeText(document.getElementById('cron-web-url').innerText); alert('URL Web Cron berhasil disalin!');">
                                            <i class="fas fa-copy mr-1"></i> Salin URL Webhook
                                        </button>
                                    </div>
                                </div>

                                <div class="card border shadow-none bg-light">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-microchip text-success mr-1"></i> Mekanisme Efisiensi Rendah Beban Server
                                        </h6>
                                        <ul class="text-xs text-muted pl-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>Zero Memory Buffer:</strong> Data di-stream langsung ke kompresor Gzip tanpa ditampung sekaligus di RAM PHP.</li>
                                            <li><strong>Chunking 500 Baris:</strong> Pembagian ekspor data per 500 baris mencegah PHP timeout dan kehabisan memori.</li>
                                            <li><strong>Smart Auto-Rotation:</strong> Sistem secara otomatis memangkas arsip lama sesuai kuota retensi yang Anda tentukan, sehingga ruang disk hosting tidak pernah penuh.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL BUAT BACKUP INSTAN (MANUAL / SCHEDULER TEST)                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-instant-backup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-teal text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fas fa-file-zipper mr-2"></i> Buat Pencadangan Database Sekarang
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/backup/create') ?>" method="post" id="form-create-backup">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted text-xs mb-3">
                        Pilih jenis snapshot pencadangan yang ingin dibuat. Berkas akan otomatis dikompresi dengan format <code>.sql.gz</code> tingkat 9 untuk menghemat hingga 90% ruang penyimpanan.
                    </p>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-dark">Kategori / Tipe Snapshot:</label>
                        <select name="type" class="form-control form-control-sm font-weight-bold">
                            <option value="manual">Snapshot Manual (Disimpan terpisah)</option>
                            <option value="daily">Snapshot Harian (Daily Backup)</option>
                            <option value="monthly">Snapshot Bulanan (Monthly Backup)</option>
                        </select>
                    </div>

                    <div class="alert alert-info border shadow-none text-xs mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Proses ekspor menggunakan teknologi <em>streaming chunking</em> sehingga server tidak akan lag dan tetap dapat melayani transaksi klinik.
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold" id="btn-submit-backup">
                        <i class="fas fa-play mr-1"></i> Mulai Pencadangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL PENGATURAN OTOMATISASI BACKUP & KEBIJAKAN RETENSI                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-backup-settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fas fa-gear mr-2"></i> Pengaturan Otomatisasi Backup & Kebijakan Retensi
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/backup/settings') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-light">
                    <div class="row">
                        <!-- KOLOM 1: JADWAL OTOMATIS -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card h-100 border p-3 bg-white">
                                <h6 class="text-xs font-weight-bold text-teal mb-3 text-uppercase">
                                    <i class="fas fa-calendar-check mr-1"></i> Jadwal Eksekusi Otomatis
                                </h6>
                                
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="backup_auto_daily" name="backup_auto_daily" value="1" <?= ($backupSettings['backup_auto_daily'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-bold text-xs text-dark" for="backup_auto_daily">
                                        Aktifkan Backup Harian Otomatis
                                    </label>
                                    <small class="text-muted d-block mt-1">Dibuat otomatis sekali setiap hari (tengah malam / dini hari).</small>
                                </div>

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="backup_auto_monthly" name="backup_auto_monthly" value="1" <?= ($backupSettings['backup_auto_monthly'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-bold text-xs text-dark" for="backup_auto_monthly">
                                        Aktifkan Backup Bulanan Otomatis
                                    </label>
                                    <small class="text-muted d-block mt-1">Dibuat otomatis setiap awal bulan untuk arsip jangka panjang.</small>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-xs text-dark">Format Kompresi:</label>
                                    <select name="backup_compression" class="form-control form-control-sm">
                                        <option value="gzip" <?= ($backupSettings['backup_compression'] ?? 'gzip') === 'gzip' ? 'selected' : '' ?>>Gzip Stream Level 9 (Sangat Hemat Ruang - Direkomendasikan)</option>
                                        <option value="sql" <?= ($backupSettings['backup_compression'] ?? 'gzip') === 'sql' ? 'selected' : '' ?>>Plain SQL (Tanpa Kompresi)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM 2: KEBIJAKAN RETENSI (HEMAT DISK) -->
                        <div class="col-md-6">
                            <div class="card h-100 border p-3 bg-white">
                                <h6 class="text-xs font-weight-bold text-purple mb-3 text-uppercase">
                                    <i class="fas fa-recycle mr-1"></i> Kebijakan Rotasi & Pembersihan
                                </h6>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-xs text-dark">Simpan Backup Harian Selama:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="1" max="60" name="backup_daily_retention_days" class="form-control font-weight-bold" value="<?= esc($backupSettings['backup_daily_retention_days'] ?? '7') ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">Hari Terakhir</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">File harian yang lebih tua dari batas ini akan dihapus otomatis.</small>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-xs text-dark">Simpan Backup Bulanan Selama:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="1" max="60" name="backup_monthly_retention_months" class="form-control font-weight-bold" value="<?= esc($backupSettings['backup_monthly_retention_months'] ?? '12') ?>">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">Bulan Terakhir</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">File bulanan yang lebih tua dari batas ini akan dihapus otomatis.</small>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-xs text-dark">Secret Token Web Cron / Webhook:</label>
                                    <input type="text" name="backup_cron_token" class="form-control form-control-sm font-weight-bold" value="<?= esc($backupSettings['backup_cron_token'] ?? 'sawamawa_cron_backup') ?>" required>
                                    <small class="text-muted">Token pengaman pemanggilan webhook dari luar.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark btn-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI RESTORE DATABASE                                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-restore-backup" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-2 px-3">
                <h6 class="modal-title font-weight-bold mb-0">
                    <i class="fas fa-triangle-exclamation mr-2"></i> Konfirmasi Restorasi / Pemulihan Database
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/backup/restore') ?>" method="post" id="form-restore-backup">
                <?= csrf_field() ?>
                <input type="hidden" name="filename" id="restore-target-filename" value="">
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-danger border shadow-none mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3 mt-1"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">PERINGATAN TINGKAT TINGGI!</strong>
                                <p class="text-muted text-xs mb-0">
                                    Memulihkan database dari berkas arsip akan <strong>menimpa (overwrite)</strong> seluruh data operasional saat ini dengan data yang ada pada berkas snapshot terpilih.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card border p-3 bg-white mb-3">
                        <div class="text-xs text-muted mb-1">Berkas Arsip Target:</div>
                        <div class="font-weight-bold text-teal" style="font-family: monospace; font-size: 14px;" id="restore-display-filename">-</div>
                    </div>

                    <div class="card border p-3 bg-white mb-0">
                        <label class="text-xs font-weight-bold text-dark mb-1">
                            Ketik kalimat konfirmasi: <code class="text-warning font-weight-bold bg-dark px-1">PULIHKAN DATABASE</code> untuk membuka kunci:
                        </label>
                        <input type="text" class="form-control form-control-sm font-weight-bold" id="input-confirm-restore" name="confirm_text" placeholder="Ketik: PULIHKAN DATABASE" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold btn-sm text-dark" id="btn-submit-restore" disabled>
                        <i class="fas fa-rotate-left mr-1"></i> Pulihkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI PENGOSONGAN DATA TRANSAKSI (CLEAN SLATE GO-LIVE)         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-reset-transactions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fas fa-triangle-exclamation mr-2"></i> Pengosongan Data Transaksi (Clean Slate Go-Live)
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/reset-transactions') ?>" method="post" id="form-reset-transactions">
                <?= csrf_field() ?>
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-warning border shadow-none mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mr-3 mt-1"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">PERHATIAN: Tindakan ini akan mengosongkan seluruh riwayat transaksi!</strong>
                                <p class="text-muted text-xs mb-0">
                                    Fitur ini dirancang khusus untuk membersihkan seluruh data uji coba dan antrean dummy saat masa simulasi, sehingga sistem siap digunakan secara resmi (Go-Live) dari catatan nomor awal.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="card h-100 border p-3 bg-white">
                                <h6 class="text-xs font-weight-bold text-danger mb-2">
                                    <i class="fas fa-trash-can mr-1"></i> DATA TRANSAKSI YANG AKAN DIHAPUS (0 Records):
                                </h6>
                                <ul class="text-xs text-muted pl-3 mb-0" style="line-height: 1.6;">
                                    <li>Data Pasien & Kunjungan Rawat Jalan / Inap</li>
                                    <li>Nomor Antrean & Pemanggilan Suara</li>
                                    <li>Rekam Medis (SOAP, Diagnosa ICD, Odontogram)</li>
                                    <li>e-Resep, Penjualan Farmasi & Mutasi Kartu Stok</li>
                                    <li>Pesanan Restoran & Layanan Gizi</li>
                                    <li>Billing Tagihan Kasir & Buku Kas Transaksi</li>
                                    <li>Jurnal Umum & Pembukuan Akuntansi (Saldo COA direset 0)</li>
                                    <li>Pengadaan (PR, PO, GRN) & Presensi/Payroll HRD</li>
                                    <li>Log Audit, Notifikasi & Riwayat Bridging SATUSEHAT</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border p-3 bg-white">
                                <h6 class="text-xs font-weight-bold text-success mb-2">
                                    <i class="fas fa-shield-check mr-1"></i> DATA PENTING YANG TETAP AMAN (TIDAK DIHAPUS):
                                </h6>
                                <ul class="text-xs text-muted pl-3 mb-0" style="line-height: 1.6;">
                                    <li><strong>Seluruh Akun Pengguna (Users)</strong> & Password</li>
                                    <li><strong>Role & Hak Akses (Permissions)</strong></li>
                                    <li><strong>Pengaturan Sistem & Profil Faskes</strong></li>
                                    <li><strong>Master Dokter & Poliklinik</strong></li>
                                    <li><strong>Katalog Layanan & Tarif Tindakan Medis</strong></li>
                                    <li><strong>Katalog Obat & Alat Kesehatan</strong></li>
                                    <li><strong>Katalog Menu Restoran / Makanan Gizi</strong></li>
                                    <li><strong>Bagan Akun Akuntansi (COA)</strong> & Aturan Jurnal</li>
                                    <li><strong>Master Kamar, Bed, ICD-10, ICD-9 & Supplier</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card border p-3 bg-white mb-2">
                        <label class="text-xs font-weight-bold text-dark mb-1">
                            Ketik kalimat konfirmasi: <code class="text-danger font-weight-bold">KOSONGKAN TRANSAKSI</code> untuk membuka kunci:
                        </label>
                        <input type="text" class="form-control form-control-sm font-weight-bold" id="input-confirm-reset" placeholder="Ketik: KOSONGKAN TRANSAKSI" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold" id="btn-submit-reset" disabled>
                        <i class="fas fa-trash-can mr-1"></i> Ya, Kosongkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Handling Reset Transactions Confirmation
    const inputConfirmReset = document.getElementById('input-confirm-reset');
    const btnSubmitReset = document.getElementById('btn-submit-reset');

    if (inputConfirmReset && btnSubmitReset) {
        inputConfirmReset.addEventListener('input', function() {
            if (this.value.trim().toUpperCase() === 'KOSONGKAN TRANSAKSI') {
                btnSubmitReset.removeAttribute('disabled');
            } else {
                btnSubmitReset.setAttribute('disabled', 'disabled');
            }
        });
    }

    // 2. Handling Restore Database Modal Triggers
    const restoreTriggers = document.querySelectorAll('.btn-restore-trigger');
    const restoreTargetFilename = document.getElementById('restore-target-filename');
    const restoreDisplayFilename = document.getElementById('restore-display-filename');
    const inputConfirmRestore = document.getElementById('input-confirm-restore');
    const btnSubmitRestore = document.getElementById('btn-submit-restore');

    restoreTriggers.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const fname = this.getAttribute('data-filename');
            if (restoreTargetFilename) restoreTargetFilename.value = fname;
            if (restoreDisplayFilename) restoreDisplayFilename.innerText = fname;
            if (inputConfirmRestore) inputConfirmRestore.value = '';
            if (btnSubmitRestore) btnSubmitRestore.setAttribute('disabled', 'disabled');

            $('#modal-restore-backup').modal('show');
        });
    });

    if (inputConfirmRestore && btnSubmitRestore) {
        inputConfirmRestore.addEventListener('input', function() {
            if (this.value.trim().toUpperCase() === 'PULIHKAN DATABASE') {
                btnSubmitRestore.removeAttribute('disabled');
            } else {
                btnSubmitRestore.setAttribute('disabled', 'disabled');
            }
        });
    }

    // 3. Prevent Double Submit on Backup Creation
    const formCreateBackup = document.getElementById('form-create-backup');
    const btnSubmitBackup = document.getElementById('btn-submit-backup');
    if (formCreateBackup && btnSubmitBackup) {
        formCreateBackup.addEventListener('submit', function() {
            btnSubmitBackup.setAttribute('disabled', 'disabled');
            btnSubmitBackup.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sedang Memproses Backup...';
        });
    }
});
</script>
<?= $this->endSection() ?>
