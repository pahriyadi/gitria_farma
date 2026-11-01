<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark */
    :root {
        --op-canvas-bg: #f4f6f9;
        --op-card-bg: #ffffff;
        --op-border: #b8b8b8;
        --op-text: #212529;
        --op-text-muted: #6c757d;
        --op-th-bg: #f8fafc;
        --op-hover-bg: #f1f5f9;
        --op-box-bg: #f8fafc;
    }

    body.dark-mode {
        --op-canvas-bg: #14171a;
        --op-card-bg: #1f2328;
        --op-border: #374151;
        --op-text: #f0f6fc;
        --op-text-muted: #9ca3af;
        --op-th-bg: #181b1f;
        --op-hover-bg: #262c36;
        --op-box-bg: #181b1f;
    }

    /* Card & Container Styling */
    .op-card {
        background-color: var(--op-card-bg) !important;
        border: 1px solid var(--op-border) !important;
        border-radius: 4px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .op-header {
        background-color: var(--op-card-bg) !important;
        border-bottom: 1px solid var(--op-border) !important;
    }

    .op-box {
        background-color: var(--op-box-bg) !important;
        border: 1px solid var(--op-border) !important;
        border-radius: 4px;
    }

    .op-text-adaptive {
        color: var(--op-text) !important;
    }

    .op-text-muted {
        color: var(--op-text-muted) !important;
    }

    /* Strict Excel-Standard Tables - Zero Zebra Stripes (desain_tabel.md) */
    .table-op {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1px solid var(--op-border) !important;
        color: var(--op-text) !important;
        margin-bottom: 0 !important;
    }

    .table-op thead th {
        background-color: var(--op-th-bg) !important;
        color: var(--op-text) !important;
        border: 1px solid var(--op-border) !important;
        padding: 9px 10px !important;
        font-size: 11.5px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700 !important;
        vertical-align: middle !important;
    }

    .table-op tbody td {
        border: 1px solid var(--op-border) !important;
        padding: 7px 10px !important;
        font-size: 12.5px !important;
        vertical-align: middle !important;
        background-color: var(--op-card-bg) !important;
        color: var(--op-text) !important;
    }

    .table-op tbody tr:hover td {
        background-color: var(--op-hover-bg) !important;
    }

    /* KPI Info Boxes in Dark/Light */
    .op-info-box {
        background: var(--op-card-bg) !important;
        border: 1px solid var(--op-border) !important;
        border-radius: 4px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        transition: transform 0.15s ease;
    }

    .op-info-box:hover {
        transform: translateY(-2px);
    }

    .op-info-icon {
        width: 52px;
        height: 52px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-right: 14px;
        flex-shrink: 0;
    }

    /* Nav Tabs Styling */
    .nav-tabs {
        border-bottom: 1px solid var(--op-border) !important;
    }

    .nav-tabs .nav-link {
        color: var(--op-text-muted) !important;
        border: 1px solid transparent !important;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .nav-tabs .nav-link.active {
        color: #20c997 !important;
        background-color: var(--op-card-bg) !important;
        border-color: var(--op-border) var(--op-border) transparent !important;
    }

    /* Form Controls in Dark/Light */
    body.dark-mode .form-control,
    body.dark-mode textarea {
        background-color: #181b1f !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }

    body.dark-mode .form-control:focus,
    body.dark-mode textarea:focus {
        border-color: #20c997 !important;
        box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25) !important;
    }

    /* Modal Dark Mode */
    body.dark-mode .modal-content {
        background-color: #1f2328 !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }

    body.dark-mode .modal-header {
        border-bottom-color: #374151 !important;
    }

    body.dark-mode .modal-footer {
        border-top-color: #374151 !important;
        background-color: #181b1f !important;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <!-- Top Metrics KPI Cards -->
        <div class="row mb-3">
            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="op-info-box">
                    <div class="op-info-icon bg-teal text-white">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase op-text-muted d-block">Total Batch Obat Terdaftar</span>
                        <h4 class="font-weight-bold op-text-adaptive mb-0"><?= count($batches) ?> <small class="op-text-muted" style="font-size: 13px;">Batch Aktif</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="op-info-box">
                    <div class="op-info-icon bg-info text-white">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase op-text-muted d-block">Total Riwayat Opname</span>
                        <h4 class="font-weight-bold op-text-adaptive mb-0"><?= count($opnameHistory) ?> <small class="op-text-muted" style="font-size: 13px;">Berita Acara</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="op-info-box">
                    <div class="op-info-icon bg-warning text-white">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase op-text-muted d-block">Opname Terakhir</span>
                        <h5 class="font-weight-bold op-text-adaptive mb-0">
                            <?= !empty($opnameHistory) ? date('d/m/Y', strtotime($opnameHistory[0]->opname_date)) : 'Belum Pernah' ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Navigation Tabs -->
        <div class="card op-card shadow-none">
            <div class="card-header p-0 border-bottom-0 bg-transparent">
                <ul class="nav nav-tabs" id="custom-tabs-opname" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-input-tab" data-toggle="pill" href="#tab-input" role="tab" aria-controls="tab-input" aria-selected="true">
                            <i class="fas fa-calculator text-teal mr-1"></i> 1. FORMULIR INPUT STOCK OPNAME OBAT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-history-tab" data-toggle="pill" href="#tab-history" role="tab" aria-controls="tab-history" aria-selected="false">
                            <i class="fas fa-history text-info mr-1"></i> 2. RIWAYAT &amp; BERITA ACARA OPNAME
                            <?php if (!empty($opnameHistory)): ?>
                                <span class="badge badge-teal ml-2"><?= count($opnameHistory) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="tab-content" id="custom-tabs-opnameContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: FORMULIR INPUT STOCK OPNAME -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-input" role="tabpanel" aria-labelledby="tab-input-tab">
                        <form action="<?= base_url('apotek/opname') ?>" method="post" id="form-stock-opname">
                            <?= csrf_field() ?>

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 op-box p-3">
                                <div class="mb-2 mb-md-0">
                                    <h5 class="font-weight-bold op-text-adaptive mb-1">
                                        <i class="fas fa-edit text-teal mr-1"></i> LEMBAR HITUNG STOK FISIK OBAT
                                    </h5>
                                    <small class="op-text-muted">Masukkan jumlah fisik riil di rak/gudang obat. Sistem akan menghitung selisih dan melakukan audit penyesuaian otomatis.</small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2" id="btn-prefill-system">
                                        <i class="fas fa-magic mr-1"></i> Isi Semua Sesuai Stok Sistem
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" id="btn-clear-physical">
                                        <i class="fas fa-eraser mr-1"></i> Kosongkan Input Fisik
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-op" id="opname-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 45px;">NO</th>
                                            <th style="width: 100px;">KODE</th>
                                            <th>NAMA OBAT &amp; KATEGORI</th>
                                            <th style="width: 140px;">NO. BATCH &amp; EXP</th>
                                            <th style="width: 110px;">STOK SISTEM</th>
                                            <th style="width: 135px;" class="text-teal">STOK FISIK (RIIL) <span class="text-danger">*</span></th>
                                            <th style="width: 130px;">SELISIH</th>
                                            <th>ALASAN / KETERANGAN SELISIH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($batches)): ?>
                                            <?php $no = 1; foreach ($batches as $b): ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                    <td class="text-center"><span class="badge badge-secondary"><?= esc($b->medicine_code) ?></span></td>
                                                    <td>
                                                        <strong class="op-text-adaptive"><?= esc($b->medicine_name) ?></strong><br>
                                                        <small class="op-text-muted"><i class="fas fa-tag mr-1"></i>Golongan: <span class="badge badge-light border"><?= esc(strtoupper($b->type ?? 'OBAT')) ?></span> | Satuan: <strong><?= esc($b->unit ?? 'Pcs') ?></strong></small>
                                                        <input type="hidden" name="items[<?= $b->id ?>][medicine_id]" value="<?= $b->medicine_id ?>">
                                                        <input type="hidden" name="items[<?= $b->id ?>][system_stock]" value="<?= $b->stock ?>" id="sys-<?= $b->id ?>">
                                                    </td>
                                                    <td class="text-center font-monospace" style="font-size: 12px;">
                                                        <strong><?= esc($b->batch_no) ?></strong><br>
                                                        <small class="op-text-muted">Exp: <?= date('d/m/Y', strtotime($b->expired_date)) ?></small>
                                                    </td>
                                                    <td class="text-center font-weight-bold op-text-adaptive" style="font-size: 14px;">
                                                        <?= esc($b->stock) ?> <small class="op-text-muted"><?= esc($b->unit ?? 'Pcs') ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="number" 
                                                               name="items[<?= $b->id ?>][physical_stock]" 
                                                               id="phys-<?= $b->id ?>" 
                                                               data-batchid="<?= $b->id ?>"
                                                               class="form-control form-control-sm text-center font-weight-bold input-physical" 
                                                               placeholder="Isi Fisik" 
                                                               min="0">
                                                    </td>
                                                    <td class="text-center font-weight-bold" id="diff-cell-<?= $b->id ?>">
                                                        <span class="badge badge-light border text-muted">-</span>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               name="items[<?= $b->id ?>][reason]" 
                                                               id="reason-<?= $b->id ?>"
                                                               class="form-control form-control-sm" 
                                                               placeholder="Keterangan selisih (opsional)">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Submission -->
                            <div class="op-box p-3 mt-3">
                                <div class="row align-items-center">
                                    <div class="col-md-7 form-group mb-2 mb-md-0">
                                        <label class="text-xs font-weight-bold op-text-adaptive mb-1">Catatan / Keterangan Berita Acara Stock Opname:</label>
                                        <textarea name="notes" id="opname-notes" class="form-control form-control-sm" rows="2" placeholder="Contoh: Stock Opname Rutin Bulanan Farmasi. Pemeriksaan fisik rak display dan gudang utama."></textarea>
                                    </div>
                                    <div class="col-md-5 text-md-right">
                                        <button type="submit" id="btn-submit-opname" class="btn btn-teal btn-lg font-weight-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menyimpan dan menerapkan penyesuaian stok fisik obat ini? Data stok sistem akan otomatis diselaraskan dengan hasil hitungan.');">
                                            <i class="fas fa-save mr-1"></i> Simpan &amp; Terapkan Penyesuaian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: RIWAYAT & BERITA ACARA OPNAME -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel" aria-labelledby="tab-history-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold op-text-adaptive mb-0"><i class="fas fa-history text-info mr-1"></i> RIWAYAT DOKUMEN STOCK OPNAME OBAT</h5>
                                <small class="op-text-muted">Arsip berita acara stock opname dan log selisih penyesuaian fisik obat farmasi.</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-op" id="table-history-opname">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 140px;">NO. OPNAME</th>
                                        <th style="width: 110px;">TANGGAL</th>
                                        <th>PETUGAS OPNAME</th>
                                        <th style="width: 110px;">ITEM DIHITUNG</th>
                                        <th style="width: 130px;">TOTAL SELISIH</th>
                                        <th>CATATAN / KETERANGAN</th>
                                        <th style="width: 100px;">STATUS</th>
                                        <th style="width: 190px;">AKSI DOKUMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($opnameHistory)): ?>
                                        <?php foreach ($opnameHistory as $op): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold">
                                                    <span class="badge badge-teal px-2 py-1 font-monospace" style="font-size: 11.5px;">
                                                        <?= esc($op->opname_no) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= date('d/m/Y', strtotime($op->opname_date)) ?></strong><br>
                                                    <small class="op-text-muted"><?= date('H:i', strtotime($op->created_at)) ?></small>
                                                </td>
                                                <td>
                                                    <strong class="op-text-adaptive"><?= esc($op->staff_name ?? 'Apoteker') ?></strong><br>
                                                    <small class="op-text-muted">Instalasi Farmasi</small>
                                                </td>
                                                <td class="text-center font-weight-bold op-text-adaptive">
                                                    <?= esc($op->total_items) ?> Item
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($op->total_discrepancy > 0): ?>
                                                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1">
                                                            <?= esc($op->total_discrepancy) ?> Unit Selisih
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success px-2 py-1">Cocok (0)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-sm">
                                                    <?= esc($op->notes ?: 'Stock Opname Rutin Farmasi') ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success px-2 py-1">TERADJUST</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Print Berita Acara -->
                                                    <a href="<?= base_url('apotek/cetak-opname/' . $op->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold mr-1 btn-cetak-opname">
                                                        <i class="fas fa-print mr-1"></i> Berita Acara
                                                    </a>
                                                    <!-- View Details Modal Trigger -->
                                                    <button class="btn btn-outline-secondary btn-xs btn-view-opname-detail font-weight-bold"
                                                            data-id="<?= $op->id ?>"
                                                            data-no="<?= esc($op->opname_no) ?>"
                                                            data-date="<?= date('d/m/Y', strtotime($op->opname_date)) ?>"
                                                            data-staff="<?= esc($op->staff_name ?? 'Apoteker') ?>"
                                                            data-toggle="modal" data-target="#modalOpnameDetail">
                                                        <i class="fas fa-eye mr-1"></i> Rincian
                                                    </button>
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

<!-- Modal Rincian Detail Opname -->
<div class="modal fade" id="modalOpnameDetail" tabindex="-1" role="dialog" aria-labelledby="modalOpnameDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="modalOpnameDetailLabel">
                    <i class="fas fa-clipboard-list mr-1"></i> Rincian Selisih Stock Opname
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row op-box p-3 mb-3">
                    <div class="col-6">
                        <small class="op-text-muted d-block">NOMOR OPNAME:</small>
                        <span id="mdl-opname-no" class="text-teal font-weight-bold h6 mb-1 d-block font-monospace"></span>
                        <small class="op-text-muted">Tanggal: <span id="mdl-opname-date" class="font-weight-bold op-text-adaptive"></span></small>
                    </div>
                    <div class="col-6 text-right">
                        <small class="op-text-muted d-block">PETUGAS / APOTEKER:</small>
                        <span id="mdl-opname-staff" class="font-weight-bold op-text-adaptive h6 mb-0 d-block"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-op" id="mdl-opname-table">
                        <thead>
                            <tr class="text-center">
                                <th>Nama Obat</th>
                                <th style="width: 120px;">Batch &amp; Exp</th>
                                <th style="width: 90px;">Sistem</th>
                                <th style="width: 90px;" class="text-teal">Fisik</th>
                                <th style="width: 100px;">Selisih</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const opnameDetailsMap = <?= json_encode($opnameDetails) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables for History Table
        if (document.getElementById('table-history-opname')) {
            $('#table-history-opname').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [[1, 'desc']], // Sort by date
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "🔍 Cari dokumen opname, petugas, catatan...",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ dokumen",
                    infoEmpty: "Tidak ada riwayat opname",
                    emptyTable: "Belum ada dokumen riwayat stock opname",
                    paginate: { first: "«", last: "»", next: "›", previous: "‹" }
                }
            });
        }

        // Adjust DataTables columns on tab switch
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Calculate discrepancy live on physical stock input
        $('.input-physical').on('input change', function() {
            const batchId = $(this).data('batchid');
            const sysVal = parseInt($('#sys-' + batchId).val()) || 0;
            const physVal = $(this).val();

            const cell = $('#diff-cell-' + batchId);

            if (physVal === '') {
                cell.html('<span class="badge badge-light border text-muted">-</span>');
                return;
            }

            const pNum = parseInt(physVal) || 0;
            const diff = pNum - sysVal;

            if (diff === 0) {
                cell.html('<span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Cocok (0)</span>');
            } else if (diff > 0) {
                cell.html(`<span class="badge badge-info px-2 py-1"><i class="fas fa-plus"></i> +${diff} (Lebih)</span>`);
            } else {
                cell.html(`<span class="badge badge-danger px-2 py-1"><i class="fas fa-minus"></i> ${diff} (Kurang)</span>`);
            }
        });

        // Pre-fill physical stock identical to system stock
        $('#btn-prefill-system').click(function() {
            $('.input-physical').each(function() {
                const batchId = $(this).data('batchid');
                const sysVal = $('#sys-' + batchId).val();
                $(this).val(sysVal).trigger('input');
            });
        });

        // Clear physical inputs
        $('#btn-clear-physical').click(function() {
            if (confirm('Kosongkan semua isian stok fisik?')) {
                $('.input-physical').val('').trigger('input');
            }
        });

        // Opname Detail Modal Trigger
        $(document).on('click', '.btn-view-opname-detail', function() {
            const id = $(this).data('id');
            const no = $(this).data('no');
            const date = $(this).data('date');
            const staff = $(this).data('staff');

            $('#mdl-opname-no').text(no);
            $('#mdl-opname-date').text(date);
            $('#mdl-opname-staff').text(staff);

            const details = opnameDetailsMap[id] || [];
            let html = '';

            if (details.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted p-3">Tidak ada rincian item.</td></tr>';
            } else {
                details.forEach(function(d) {
                    const diff = parseInt(d.difference) || 0;
                    let diffBadge = '';
                    if (diff === 0) {
                        diffBadge = '<span class="badge badge-success px-2 py-1">0 (Cocok)</span>';
                    } else if (diff > 0) {
                        diffBadge = `<span class="badge badge-info px-2 py-1">+${diff}</span>`;
                    } else {
                        diffBadge = `<span class="badge badge-danger px-2 py-1">${diff}</span>`;
                    }

                    html += `
                        <tr>
                            <td><strong class="op-text-adaptive">${d.medicine_name}</strong></td>
                            <td class="text-center font-monospace" style="font-size: 11.5px;">${d.batch_no}<br><small class="op-text-muted">${d.expired_date}</small></td>
                            <td class="text-center font-weight-bold">${d.system_stock} ${d.unit}</td>
                            <td class="text-center font-weight-bold text-teal">${d.physical_stock} ${d.unit}</td>
                            <td class="text-center font-weight-bold">${diffBadge}</td>
                            <td class="text-sm op-text-adaptive">${d.reason || '-'}</td>
                        </tr>
                    `;
                });
            }

            $('#mdl-opname-table tbody').html(html);
        });
    });
</script>
<?= $this->endSection() ?>
