<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-none border" style="border: 1px solid #b8b8b8 !important; border-radius: 6px;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #b8b8b8;">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-bug text-danger mr-2"></i> Error Tracking & APM System (Real-Time Diagnostics)
                    </h5>
                    <small class="text-muted">Pelacakan galat aplikasi otomatis, deduplikasi eksepsi runtime, dan inspeksi jejak kode (Stack Trace).</small>
                </div>
                <div>
                    <a href="<?= base_url('system/clear-error-logs?type=resolved') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-none mr-1" onclick="return confirm('Bersihkan semua log error yang sudah diselesaikan?');">
                        <i class="fas fa-broom mr-1"></i> Bersihkan Log Selesai
                    </a>
                    <a href="<?= base_url('system/clear-error-logs?type=all') ?>" class="btn btn-outline-danger btn-sm font-weight-bold shadow-none" onclick="return confirm('PERINGATAN: Seluruh riwayat log error akan dihapus permanen. Lanjutkan?');">
                        <i class="fas fa-trash mr-1"></i> Reset Seluruh Log
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- 4 KPI Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #ef4444 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">ERROR BELUM SELESAI (OPEN)</small>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <strong class="text-danger h4 font-weight-bold mb-0"><?= number_format($kpi['unresolvedCount']) ?> Isu</strong>
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #b91c1c !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">CRITICAL FATAL CRASHES</small>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <strong class="text-dark h4 font-weight-bold mb-0"><?= number_format($kpi['criticalCount']) ?> Crash</strong>
                                <span class="badge badge-dark px-2 py-1"><i class="fas fa-skull-crossbones mr-1"></i> Critical</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #f59e0b !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">TOTAL FREKUENSI KEJADIAN</small>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <strong class="text-warning h4 font-weight-bold mb-0"><?= number_format($kpi['totalOccurrences']) ?>x Muncul</strong>
                                <small class="text-muted font-weight-bold">Terdeduplikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #0d9f4f !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">TOTAL JEJAK KODE TERDAFTAR</small>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <strong class="text-teal h4 font-weight-bold mb-0"><?= number_format($kpi['totalLogs']) ?> Entri</strong>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> APM Synced</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Toolbar -->
                <form action="<?= base_url('system/error-logs') ?>" method="get" class="mb-3 p-3 bg-light border rounded" style="border: 1px solid #b8b8b8 !important;">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label class="text-xs font-weight-bold text-dark mr-2 mb-0">Status Error:</label>
                            <select name="status" class="form-control form-control-sm d-inline-block w-auto" onchange="this.form.submit()">
                                <option value="unresolved" <?= ($statusFilter === 'unresolved') ? 'selected' : '' ?>>🔴 Belum Selesai (Open)</option>
                                <option value="resolved" <?= ($statusFilter === 'resolved') ? 'selected' : '' ?>>🟢 Sudah Selesai (Resolved)</option>
                                <option value="all" <?= ($statusFilter === 'all') ? 'selected' : '' ?>>Semua Status</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="text-xs font-weight-bold text-dark mr-2 mb-0">Tingkat Severity:</label>
                            <select name="level" class="form-control form-control-sm d-inline-block w-auto" onchange="this.form.submit()">
                                <option value="">Semua Tingkat</option>
                                <option value="CRITICAL" <?= ($levelFilter === 'CRITICAL') ? 'selected' : '' ?>>CRITICAL (Fatal)</option>
                                <option value="ERROR" <?= ($levelFilter === 'ERROR') ? 'selected' : '' ?>>ERROR</option>
                                <option value="WARNING" <?= ($levelFilter === 'WARNING') ? 'selected' : '' ?>>WARNING</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <a href="<?= base_url('system/error-logs') ?>" class="btn btn-secondary btn-sm shadow-none">
                                <i class="fas fa-sync-alt mr-1"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Tabel Daftar Log Error -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th style="width: 100px;">SEVERITY</th>
                                <th>PESAN GALAT &amp; LOKASI SOURCE CODE</th>
                                <th style="width: 180px;">URL &amp; METHOD</th>
                                <th style="width: 80px;" class="text-center">FREKUENSI</th>
                                <th style="width: 130px;">TERAKHIR MUNCUL</th>
                                <th style="width: 140px;" class="text-center">AKSI &amp; RESOLUSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($errorLogs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-shield-heart fa-2x text-success mb-2 d-block"></i>
                                        <strong>Luar Biasa! Tidak ada log galat/error yang tercatat.</strong><br>
                                        <small>Sistem beroperasi 100% normal dan stabil.</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($errorLogs as $err): 
                                    $badgeClass = 'badge-danger';
                                    if ($err->error_level === 'CRITICAL') $badgeClass = 'badge-dark';
                                    elseif ($err->error_level === 'WARNING') $badgeClass = 'badge-warning';
                                    elseif ($err->error_level === 'NOTICE') $badgeClass = 'badge-info';
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> px-2 py-1"><?= esc($err->error_level) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block text-sm" style="word-break: break-all;">
                                            <?= esc($err->message) ?>
                                        </strong>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-file-code text-teal mr-1"></i> <code><?= esc($err->file) ?></code> : <strong>Baris <?= (int)$err->line ?></strong>
                                        </small>
                                    </td>
                                    <td class="text-xs">
                                        <span class="badge badge-secondary mr-1"><?= esc($err->method) ?></span>
                                        <span class="text-dark font-weight-bold" style="word-break: break-all;"><?= esc($err->url) ?></span>
                                        <small class="text-muted d-block mt-1">IP: <?= esc($err->ip_address ?? '127.0.0.1') ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-primary px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                            <?= number_format($err->count) ?>x
                                        </span>
                                    </td>
                                    <td class="text-xs text-muted">
                                        <?= date('d/m/Y H:i', strtotime($err->updated_at)) ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-outline-info btn-xs font-weight-bold mr-1 btn-trace" 
                                                data-toggle="modal" 
                                                data-target="#modalTrace"
                                                data-title="<?= esc($err->message) ?>"
                                                data-file="<?= esc($err->file) ?>"
                                                data-line="<?= (int)$err->line ?>"
                                                data-trace="<?= esc($err->trace) ?>">
                                            <i class="fas fa-code mr-1"></i> Trace
                                        </button>
                                        <?php if ($err->is_resolved == 0): ?>
                                            <a href="<?= base_url('system/resolve-error/' . $err->id) ?>" class="btn btn-success btn-xs font-weight-bold" onclick="return confirm('Tandai isu galat ini sebagai sudah selesai?');">
                                                <i class="fas fa-check mr-1"></i> Selesai
                                            </a>
                                        <?php else: ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Resolved</span>
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
    </div>
</div>

<!-- Modal Stack Trace Detail -->
<div class="modal fade" id="modalTrace" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border: 1px solid #b8b8b8;">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-bug text-danger mr-2"></i> Inspeksi Stack Trace &amp; Source Origin
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="p-3 bg-white border rounded mb-3" style="border: 1px solid #b8b8b8 !important;">
                    <small class="text-muted d-block text-xs font-weight-bold">PESAN KESALAHAN:</small>
                    <strong class="text-danger d-block mt-1" id="traceModalTitle" style="font-size: 15px;">-</strong>
                    <div class="mt-2 text-xs text-muted">
                        <i class="fas fa-map-marker-alt text-teal mr-1"></i> Lokasi: <code id="traceModalFile">-</code> pada Baris <strong id="traceModalLine">-</strong>
                    </div>
                </div>

                <small class="text-dark d-block font-weight-bold mb-1"><i class="fas fa-align-left text-teal mr-1"></i> Exception Call Stack:</small>
                <pre class="p-3 bg-dark text-light rounded font-weight-bold" style="font-size: 12px; max-height: 450px; overflow-y: auto; line-height: 1.6;" id="traceModalContent"></pre>
            </div>
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold shadow-none" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-trace').on('click', function() {
        var title = $(this).data('title');
        var file  = $(this).data('file');
        var line  = $(this).data('line');
        var trace = $(this).data('trace');

        $('#traceModalTitle').text(title);
        $('#traceModalFile').text(file);
        $('#traceModalLine').text(line);
        $('#traceModalContent').text(trace || 'Tidak ada stack trace mendalam.');
    });
});
</script>
<?= $this->endSection() ?>
