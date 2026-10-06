<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- ========================================================================= -->
<!-- EXECUTIVE HEADER & QUICK STATS                                            -->
<!-- ========================================================================= -->
<div class="content-header p-0 mb-3">
    <div class="container-fluid p-0">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.45rem;">
                    <i class="fas fa-shield-halved text-danger mr-2"></i> Pusat Log Pembatalan / Void (Anti-Fraud Center)
                </h1>
                <small class="text-muted">Audit trail, otorisasi supervisor, dan jurnal pembalik otomatis untuk seluruh pembatalan transaksi</small>
            </div>
            <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                <button class="btn btn-info btn-sm font-weight-bold shadow-xs mr-1" data-toggle="modal" data-target="#modalReadmeVoid">
                    <i class="fas fa-book-open mr-1"></i> Panduan Operasional (README)
                </button>
                <button class="btn btn-outline-secondary btn-sm font-weight-bold shadow-xs mr-1" data-toggle="modal" data-target="#modalManagePin">
                    <i class="fas fa-key mr-1"></i> Atur PIN Supervisor
                </button>
                <button class="btn btn-danger btn-sm font-weight-bold shadow-sm" onclick="window.location.reload();">
                    <i class="fas fa-arrows-rotate mr-1"></i> Refresh Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ACCOUNTING SUITE SUB-NAVIGATION BAR (RESPONSIVE SCROLLABLE)              -->
<!-- ========================================================================= -->
<div class="accounting-tabs-scroll-wrapper mb-3">
    <ul class="nav nav-tabs nav-tabs-modern flex-nowrap" id="accountingNavTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/jurnal') ?>">
                <i class="fas fa-file-lines text-teal mr-1"></i> 1. Jurnal Umum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/buku-besar') ?>">
                <i class="fas fa-book-journal-whills text-info mr-1"></i> 2. Buku Mutasi Akun
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/laporan') ?>">
                <i class="fas fa-chart-pie text-success mr-1"></i> 3. Laporan Keuangan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/coa') ?>">
                <i class="fas fa-book-bookmark text-primary mr-1"></i> 4. Bagan Akun (COA)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/aturan-jurnal') ?>">
                <i class="fas fa-sliders text-warning mr-1"></i> 5. Aturan Jurnal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/saldo-awal') ?>">
                <i class="fas fa-scale-balanced text-secondary mr-1"></i> 6. Saldo Awal
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active font-weight-bold py-2 text-truncate text-danger" href="<?= base_url('accounting/void-logs') ?>">
                <i class="fas fa-shield-virus text-danger mr-1"></i> 7. Log Void (Anti-Fraud)
                <span class="badge badge-pill badge-danger ml-1"><?= count($logs ?? []) ?></span>
            </a>
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- PANDUAN RINGKAS ANTI-FRAUD VOID CENTER                                    -->
<!-- ========================================================================= -->
<div class="alert alert-light border shadow-sm d-flex align-items-start mb-3" style="border-left: 4px solid #dc3545 !important; border-radius: 6px;">
    <div class="mr-3 text-danger mt-1" style="font-size: 20px;">
        <i class="fas fa-shield-halved"></i>
    </div>
    <div class="text-sm flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-circle-info text-danger mr-1"></i> Panduan Singkat Sistem Pembatalan Transaksi (Void Anti-Fraud)
            </h6>
            <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold" data-toggle="modal" data-target="#modalReadmeVoid">
                <i class="fas fa-book-open mr-1"></i> Baca README Lengkap
            </button>
        </div>
        <p class="text-muted mb-0" style="line-height: 1.5;">
            Sistem Anti-Fraud ini mengamankan seluruh transaksi kasir klinik, apotek retail, distributor B2B, kas operasional, dan resto gizi. Setiap pembatalan wajib melalui <strong>Otorisasi PIN Supervisor (6 Digit)</strong>, otomatis menerbitkan <strong>Jurnal Pembalik Simetris (Reversing Entry)</strong>, mengembalikan <strong>Batch Mutasi Stok FIFO/FEFO</strong>, dan menerbitkan <strong>Berita Acara Pembatalan Resmi (PDF)</strong>.
        </p>
    </div>
</div>

<!-- ========================================================================= -->
<!-- KPI SUMMARY CARDS                                                         -->
<!-- ========================================================================= -->
<div class="row mb-3">
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-danger-soft">
                    <i class="fas fa-ban fa-lg text-danger"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Void Hari Ini</span>
                    <h4 class="font-weight-bold mb-0 text-dark">Rp <?= number_format($kpi['todayAmount'], 0, ',', '.') ?></h4>
                    <small class="text-muted"><?= $kpi['todayCount'] ?> Transaksi dibatalkan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-warning-soft">
                    <i class="fas fa-calendar-xmark fa-lg text-warning"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Void Bulan Ini</span>
                    <h4 class="font-weight-bold mb-0 text-dark">Rp <?= number_format($kpi['monthAmount'], 0, ',', '.') ?></h4>
                    <small class="text-muted"><?= $kpi['monthCount'] ?> Transaksi dibatalkan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-primary-soft">
                    <i class="fas fa-layer-group fa-lg text-primary"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Modul Terbanyak Void</span>
                    <h5 class="font-weight-bold mb-0 text-dark"><?= esc($kpi['topModuleName']) ?></h5>
                    <small class="text-muted"><?= $kpi['topModuleCount'] ?> Kali Pembatalan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-success-soft">
                    <i class="fas fa-scale-balanced fa-lg text-success"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Integritas Jurnal</span>
                    <h5 class="font-weight-bold mb-0 text-success">100% Reversing</h5>
                    <small class="text-muted">Double-entry balanced</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- FILTER BAR                                                                -->
<!-- ========================================================================= -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('accounting/void-logs') ?>" class="row align-items-end" style="gap: 4px 0;">
            <div class="col-md-3 col-sm-6">
                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Tanggal Sampai</label>
                <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Modul Transaksi</label>
                <select name="module" class="form-control form-control-sm font-weight-bold">
                    <option value="">Semua Modul</option>
                    <option value="billing_klinik" <?= $selectedModule === 'billing_klinik' ? 'selected' : '' ?>>Kasir Klinik</option>
                    <option value="pharmacy_sale" <?= $selectedModule === 'pharmacy_sale' ? 'selected' : '' ?>>Apotek Retail</option>
                    <option value="distributor_sale" <?= $selectedModule === 'distributor_sale' ? 'selected' : '' ?>>Distributor B2B</option>
                    <option value="receivable_payment" <?= $selectedModule === 'receivable_payment' ? 'selected' : '' ?>>Bayar Piutang</option>
                    <option value="cash_expense" <?= $selectedModule === 'cash_expense' ? 'selected' : '' ?>>Kas Operasional</option>
                    <option value="resto_sale" <?= $selectedModule === 'resto_sale' ? 'selected' : '' ?>>Resto Sehat</option>
                    <option value="doctor_fee" <?= $selectedModule === 'doctor_fee' ? 'selected' : '' ?>>Fee Dokter</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Supervisor Pengesah</label>
                <select name="supervisor_id" class="form-control form-control-sm font-weight-bold">
                    <option value="">Semua Supervisor</option>
                    <?php foreach ($supervisors as $spv): ?>
                        <option value="<?= $spv->id ?>" <?= $selectedSupervisor == $spv->id ? 'selected' : '' ?>>
                            <?= esc($spv->name ?: $spv->username) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 text-md-right mt-2 mt-md-0">
                <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold shadow-xs">
                    <i class="fas fa-filter mr-1"></i> Filter Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MAIN TABLE LOG AUDIT VOID                                                 -->
<!-- ========================================================================= -->
<div class="card card-outline card-danger shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold text-dark mb-0">
                <i class="fas fa-list-check text-danger mr-1"></i> Riwayat Transaksi Dibatalkan &amp; Reversing Entry
            </h5>
            <span class="badge badge-light border text-muted font-weight-bold">
                Total: <?= count($logs) ?> Catatan Void
            </span>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="table-void-logs" class="table table-hover table-striped table-bordered datatable w-100" style="font-size: 12px;">
                <thead class="bg-light text-dark">
                    <tr>
                        <th style="width: 45px;" class="text-center">NO</th>
                        <th style="width: 140px;">NO. VOID &amp; WAKTU</th>
                        <th style="width: 120px;" class="text-center">MODUL</th>
                        <th style="min-width: 160px;">NO. REFERENSI ASAL</th>
                        <th style="width: 130px;" class="text-right">NOMINAL (RP)</th>
                        <th style="min-width: 160px;">KASIR &amp; SUPERVISOR</th>
                        <th style="min-width: 200px;">KATEGORI &amp; ALASAN VOID</th>
                        <th style="width: 130px;" class="text-center">JURNAL PEMBALIK</th>
                        <th style="width: 100px;" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-shield-check fa-3x mb-2 text-success"></i>
                                <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Transaksi yang Dibatalkan</h6>
                                <small class="text-secondary">Seluruh operasional kasir dan pembukuan berjalan lancar tanpa pembatalan pada periode ini.</small>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($logs as $l): ?>
                            <tr>
                                <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-danger font-monospace"><?= esc($l->void_number) ?></strong>
                                    <small class="d-block text-muted">
                                        <i class="fas fa-clock mr-1"></i> <?= date('d/m/Y H:i', strtotime($l->created_at)) ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $badgeColor = 'badge-secondary';
                                    $modLabel = ucfirst(str_replace('_', ' ', $l->transaction_type));
                                    if ($l->transaction_type === 'billing_klinik') { $badgeColor = 'badge-primary'; $modLabel = 'Klinik'; }
                                    elseif ($l->transaction_type === 'pharmacy_sale') { $badgeColor = 'badge-teal'; $modLabel = 'Apotek'; }
                                    elseif ($l->transaction_type === 'distributor_sale') { $badgeColor = 'badge-indigo'; $modLabel = 'Grosir B2B'; }
                                    elseif ($l->transaction_type === 'receivable_payment') { $badgeColor = 'badge-info'; $modLabel = 'Bayar Piutang'; }
                                    elseif ($l->transaction_type === 'cash_expense') { $badgeColor = 'badge-warning text-dark'; $modLabel = 'Kas Operasional'; }
                                    elseif ($l->transaction_type === 'resto_sale') { $badgeColor = 'badge-orange text-white'; $modLabel = 'Resto Gizi'; }
                                    ?>
                                    <span class="badge <?= $badgeColor ?> px-2 py-1 font-weight-bold text-uppercase">
                                        <?= esc($modLabel) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= esc($l->reference_number) ?></strong>
                                    <small class="d-block text-muted">Metode: <span class="badge badge-light border text-uppercase font-weight-bold"><?= esc($l->payment_method) ?></span></small>
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    Rp <?= number_format($l->total_amount, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <div>
                                        <small class="text-muted font-weight-bold">Kasir:</small>
                                        <span class="text-dark font-weight-bold"><?= esc($l->cashier_name ?: $l->cashier_username) ?></span>
                                    </div>
                                    <div class="mt-0.5">
                                        <small class="text-muted font-weight-bold">Supervisor:</small>
                                        <span class="text-teal font-weight-bold"><i class="fas fa-key mr-1"></i> <?= esc($l->supervisor_name ?: $l->supervisor_username) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold mb-1">
                                        <?= esc($l->reason_category) ?>
                                    </span>
                                    <p class="text-secondary text-xs mb-0" style="line-height: 1.3;">
                                        <?= esc($l->reason_detail) ?>
                                    </p>
                                </td>
                                <td class="text-center">
                                    <?php if ($l->reversal_journal_no): ?>
                                        <span class="badge badge-success px-2 py-1 font-monospace font-weight-bold" title="Jurnal Pembalik Otomatis">
                                            <i class="fas fa-check-circle mr-1"></i> <?= esc($l->reversal_journal_no) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted">Tanpa Jurnal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info btn-xs shadow-xs" onclick="viewVoidDetail(<?= $l->id ?>)" title="Lihat Snapshot Rincian">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                        <a href="<?= base_url('accounting/void-logs/cetak/' . $l->id) ?>" target="_blank" class="btn btn-outline-secondary btn-xs shadow-xs" title="Cetak Berita Acara Void (PDF)">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL SNAPSHOT ITEM TRANSAKSI                                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalVoidDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title font-weight-bold" id="voidDetailTitle">
                    <i class="fas fa-receipt mr-1"></i> Rincian Snapshot Pembatalan Transaksi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="voidDetailBody">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Memuat snapshot data transaksi...</p>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <a href="#" id="btnCetakBeritaAcara" target="_blank" class="btn btn-danger btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak Berita Acara Void (PDF)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL SETUP PIN SUPERVISOR                                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalManagePin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-key mr-1"></i> Atur PIN Supervisor Void
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/void-logs/save-pin') ?>" method="post" id="formSavePin">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 text-xs shadow-xs mb-3" style="border-left: 3px solid #17a2b8 !important;">
                        <i class="fas fa-info-circle mr-1"></i> PIN 6 digit ini digunakan untuk mengotorisasi setiap pembatalan transaksi kasir (Anti-Fraud Gate).
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Pilih Akun Supervisor / Pimpinan <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control select2" required>
                            <?php foreach ($supervisors as $spv): ?>
                                <option value="<?= $spv->id ?>">
                                    <?= esc($spv->name ?: $spv->username) ?> (<?= esc($spv->role_name) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">PIN Baru (Minimal 6 Digit Angka) <span class="text-danger">*</span></label>
                        <input type="password" name="new_pin" class="form-control font-weight-bold text-center" maxlength="12" placeholder="••••••" required autocomplete="new-password">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark text-sm">Konfirmasi PIN Baru <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_pin" class="form-control font-weight-bold text-center" maxlength="12" placeholder="••••••" required autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan PIN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL PANDUAN OPERASIONAL & ARSITEKTUR VOID CENTER (README)               -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalReadmeVoid" tabindex="-1" role="dialog" aria-labelledby="modalReadmeVoidLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-gradient-teal text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="modalReadmeVoidLabel">
                        <i class="fas fa-book-open mr-2"></i> Panduan Operasional &amp; Standar Prosedur Void Transaksi (README)
                    </h5>
                    <small class="text-white-50">Pedoman Lengkap Audit Trail, Jurnal Pembalik, dan Otorisasi Anti-Fraud</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Nav Tabs Modal -->
                <div class="bg-light border-bottom px-4 pt-3">
                    <ul class="nav nav-tabs border-0" id="readmeTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold text-dark" id="tab-prinsip-tab" data-toggle="tab" href="#tab-prinsip" role="tab">
                                <i class="fas fa-shield-halved text-danger mr-1"></i> 1. Prinsip Anti-Fraud
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-dark" id="tab-modul-tab" data-toggle="tab" href="#tab-modul" role="tab">
                                <i class="fas fa-layer-group text-primary mr-1"></i> 2. Cakupan 8 Modul
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-dark" id="tab-langkah-tab" data-toggle="tab" href="#tab-langkah" role="tab">
                                <i class="fas fa-list-ol text-success mr-1"></i> 3. Langkah Pembatalan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-dark" id="tab-pin-tab" data-toggle="tab" href="#tab-pin" role="tab">
                                <i class="fas fa-key text-warning mr-1"></i> 4. PIN &amp; Keamanan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold text-dark" id="tab-faq-tab" data-toggle="tab" href="#tab-faq" role="tab">
                                <i class="fas fa-circle-question text-info mr-1"></i> 5. FAQ &amp; Solusi
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-4" id="readmeTabsContent" style="font-size: 13.5px; line-height: 1.6;">
                    <!-- TAB 1: PRINSIP ANTI-FRAUD -->
                    <div class="tab-pane fade show active" id="tab-prinsip" role="tabpanel">
                        <div class="alert alert-warning border-0 p-3 mb-3" style="background-color: #fff3cd; color: #856404; border-radius: 8px;">
                            <i class="fas fa-triangle-exclamation mr-1 font-weight-bold"></i>
                            <strong>Aturan Baku Pembukuan Akuntansi &amp; Medis:</strong> Seluruh transaksi yang telah tersimpan <u>DILARANG KERAS DIHAPUS SECARA FISIK (Hard Delete)</u>. Setiap pembatalan wajib melalui Reversing Entry agar integritas neraca, arus kas, dan audit trail tetap seimbang 100%.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100 shadow-xs">
                                    <div class="card-body p-3">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-scale-balanced text-teal mr-1"></i> Reversing Entry Otomatis
                                        </h6>
                                        <p class="text-muted mb-0">
                                            Sistem <code>VoidReversalEngine</code> secara otomatis membuat entri jurnal pembalik berkode <code>JV-VOID-YYYYMMDD-XXXX</code> yang menukar posisi Debit dan Kredit transaksi asal secara simetris. Nilai laporan laba rugi, buku besar, dan neraca langsung tersinkronisasi tanpa distorsi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border h-100 shadow-xs">
                                    <div class="card-body p-3">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-boxes-stacked text-primary mr-1"></i> Restorasi Batch Stok (FIFO/FEFO)
                                        </h6>
                                        <p class="text-muted mb-0">
                                            Pada modul Farmasi Retail dan Distributor Grosir B2B, stok obat yang dibatalkan akan otomatis dikembalikan ke <strong>Nomor Batch &amp; Expired Date Asal</strong> disertai pencatatan kartu stok mutasi masuk bertipe <code>void_in</code>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border bg-light shadow-xs mt-2">
                            <div class="card-body p-3">
                                <h6 class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-camera-retro text-danger mr-1"></i> Snapshot Data Permanen (JSON Immutaible Payload)
                                </h6>
                                <p class="text-muted mb-0">
                                    Sebelum dibatalkan, seluruh rincian transaksi (nama pasien, dokter, rincian obat, kuantitas, harga, diskon, nama kasir, supervisor, IP address, user agent) disimpan secara permanen dalam bentuk JSON snapshot pada tabel <code>void_audit_logs</code> untuk keperluan audit internal/eksternal.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: CAKUPAN 8 MODUL -->
                    <div class="tab-pane fade" id="tab-modul" role="tabpanel">
                        <h6 class="font-weight-bold text-dark mb-3">Tabel Matriks Efek Pembatalan per Modul:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped" style="font-size: 12px;">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th>Modul Transaksi</th>
                                        <th>Tindakan Status</th>
                                        <th>Efek Akuntansi (Jurnal)</th>
                                        <th>Efek Logistik &amp; Piutang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>1. Kasir Billing Pasien / Klinik</strong></td>
                                        <td>Status pembayaran diubah ke <code>void</code> / <code>cancelled</code></td>
                                        <td>Debit Pendapatan &amp; Jasa Medis vs Kredit Kas/Bank Kasir</td>
                                        <td>Status tagihan billing dibuka kembali</td>
                                    </tr>
                                    <tr>
                                        <td><strong>2. Penjualan Apotek (Retail)</strong></td>
                                        <td>Status <code>is_voided = 1</code></td>
                                        <td>Debit Pendapatan Obat &amp; Tusla vs Kredit Kas/Bank Kasir</td>
                                        <td>Stok batch obat dikembalikan (+Qty ke batch asal)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>3. Faktur Penjualan Distributor B2B</strong></td>
                                        <td>Status <code>is_voided = 1</code>, status bayar <code>cancelled</code></td>
                                        <td>Debit Pendapatan Grosir vs Kredit Piutang Usaha Grosir</td>
                                        <td>Stok batch distributor kembali &amp; sisa limit kredit pelanggan dipulihkan</td>
                                    </tr>
                                    <tr>
                                        <td><strong>4. Pembayaran Piutang Grosir</strong></td>
                                        <td>Status <code>is_voided = 1</code></td>
                                        <td>Debit Piutang Usaha vs Kredit Kas/Bank Penerimaan</td>
                                        <td>Sisa tagihan faktur &amp; kartu piutang bertambah kembali</td>
                                    </tr>
                                    <tr>
                                        <td><strong>5. Kas Masuk Non-Operasional</strong></td>
                                        <td>Status <code>cancelled</code></td>
                                        <td>Debit Pendapatan Lain vs Kredit Kas/Bank</td>
                                        <td>Saldo kas/bank dikurangi sebesar nominal asal</td>
                                    </tr>
                                    <tr>
                                        <td><strong>6. Kas Keluar / Beban Operasional</strong></td>
                                        <td>Status <code>cancelled</code></td>
                                        <td>Debit Kas/Bank Sumber vs Kredit Akun Beban</td>
                                        <td>Saldo kas/bank dipulihkan sebesar nominal beban</td>
                                    </tr>
                                    <tr>
                                        <td><strong>7. Pesanan Restoran Gizi Sehat</strong></td>
                                        <td>Status order <code>cancelled</code></td>
                                        <td>Debit Pendapatan Makanan vs Kredit Kas/Bank Resto</td>
                                        <td>Status meja/pesanan dapur dibatalkan</td>
                                    </tr>
                                    <tr>
                                        <td><strong>8. Billing Laboratorium Klinik</strong></td>
                                        <td>Status invoice <code>cancelled</code></td>
                                        <td>Debit Pendapatan Lab vs Kredit Kas/Bank Lab</td>
                                        <td>Invoice uji lab dibatalkan</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: LANGKAH PEMBATALAN -->
                    <div class="tab-pane fade" id="tab-langkah" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 38px; height: 38px; font-size: 16px; font-weight: bold;">1</div>
                                    <h6 class="font-weight-bold text-dark">Klik Tombol Void</h6>
                                    <p class="text-muted text-xs mb-0">Petugas kasir/apoteker menemukan transaksi yang salah pada riwayat dan menekan tombol <span class="badge badge-outline-danger"><i class="fas fa-ban"></i> Void</span>.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-2" style="width: 38px; height: 38px; font-size: 16px; font-weight: bold;">2</div>
                                    <h6 class="font-weight-bold text-dark">Otorisasi Supervisor &amp; Alasan</h6>
                                    <p class="text-muted text-xs mb-0">Pilih kategori alasan resmi, ketik kronologi pembatalan, lalu minta Supervisor memasukkan <strong>PIN 6 Digit</strong> pada popup otorisasi.</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light text-center h-100">
                                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-2" style="width: 38px; height: 38px; font-size: 16px; font-weight: bold;">3</div>
                                    <h6 class="font-weight-bold text-dark">Cetak Berita Acara (PDF)</h6>
                                    <p class="text-muted text-xs mb-0">Sistem mengeksekusi reversing journal dan otomatis menyediakan tautan cetak dokumen resmi Berita Acara untuk ditandatangani.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: PIN & KEAMANAN -->
                    <div class="tab-pane fade" id="tab-pin" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border p-3 shadow-xs h-100">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-id-badge text-teal mr-1"></i> Siapa Saja yang Memiliki Hak PIN?</h6>
                                    <p class="text-muted text-xs mb-2">PIN otorisasi hanya diberikan kepada akun pengguna dengan wewenang pengawasan (Supervisor Level), antara lain:</p>
                                    <ul class="text-xs text-muted pl-3 mb-0">
                                        <li>Super Admin &amp; IT Administrator</li>
                                        <li>Direksi / Kepala Klinik</li>
                                        <li>Koordinator Keuangan &amp; Kasir Utama</li>
                                        <li>Manajer Operasional / Apoteker Pengelola</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border p-3 shadow-xs h-100">
                                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-lock text-danger mr-1"></i> Proteksi Anti-Brute Force Lockout</h6>
                                    <p class="text-muted text-xs mb-2">Untuk mencegah percobaan tebak PIN oleh pihak yang tidak berwenang:</p>
                                    <ul class="text-xs text-muted pl-3 mb-0">
                                        <li>PIN tersimpan dalam enkripsi <strong>Bcrypt Hash</strong> standar perbankan.</li>
                                        <li>Jika terjadi <strong>5 kali salah input berturut-turut</strong>, akun supervisor otomatis terkunci (*Locked Out*) selama 15 menit.</li>
                                        <li>PIN default sistem adalah <code>123456</code> dan wajib diganti secara berkala.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: FAQ & SOLUSI -->
                    <div class="tab-pane fade" id="tab-faq" role="tabpanel">
                        <div class="accordion" id="accordionFaqVoid">
                            <div class="card border mb-2 shadow-xs">
                                <div class="card-header bg-white p-2" id="faqHeading1">
                                    <button class="btn btn-link btn-block text-left text-dark font-weight-bold text-xs p-0 text-decoration-none" type="button" data-toggle="collapse" data-target="#faqCol1">
                                        <i class="fas fa-question-circle text-teal mr-1"></i> Apakah transaksi yang sudah di-void bisa dibatalkan kembali (di-unvoid)?
                                    </button>
                                </div>
                                <div id="faqCol1" class="collapse show" data-parent="#accordionFaqVoid">
                                    <div class="card-body p-3 text-xs text-muted border-top">
                                        <strong>Tidak bisa.</strong> Pembatalan transaksi bersifat permanen untuk menjaga auditabilitas akuntansi. Jika terjadi kesalahan void, kasir harus membuat entri transaksi baru.
                                    </div>
                                </div>
                            </div>

                            <div class="card border mb-2 shadow-xs">
                                <div class="card-header bg-white p-2" id="faqHeading2">
                                    <button class="btn btn-link btn-block text-left text-dark font-weight-bold text-xs p-0 text-decoration-none" type="button" data-toggle="collapse" data-target="#faqCol2">
                                        <i class="fas fa-question-circle text-teal mr-1"></i> Bagaimana jika Supervisor lupa PIN otorisasi?
                                    </button>
                                </div>
                                <div id="faqCol2" class="collapse" data-parent="#accordionFaqVoid">
                                    <div class="card-body p-3 text-xs text-muted border-top">
                                        Supervisor dapat mereset PIN baru kapan saja melalui tombol <strong>"🔑 Atur PIN Supervisor"</strong> di bagian atas halaman Log Void ini.
                                    </div>
                                </div>
                            </div>

                            <div class="card border mb-0 shadow-xs">
                                <div class="card-header bg-white p-2" id="faqHeading3">
                                    <button class="btn btn-link btn-block text-left text-dark font-weight-bold text-xs p-0 text-decoration-none" type="button" data-toggle="collapse" data-target="#faqCol3">
                                        <i class="fas fa-question-circle text-teal mr-1"></i> Di mana bukti fisik Berita Acara disimpan?
                                    </button>
                                </div>
                                <div id="faqCol3" class="collapse" data-parent="#accordionFaqVoid">
                                    <div class="card-body p-3 text-xs text-muted border-top">
                                        Berita Acara dapat dicetak ulang kapan saja dari tabel riwayat pada tombol <span class="badge badge-danger"><i class="fas fa-print"></i> Cetak</span>. Dokumen PDF yang telah dicetak dan ditandatangani diarsipkan bersama rekap kasir harian.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-shield-halved text-teal mr-1"></i> ERP Gitria Farma - Sawamawa Medical Center Anti-Fraud System</small>
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup Panduan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewVoidDetail(id) {
    $('#voidDetailTitle').html('<i class="fas fa-spinner fa-spin mr-1"></i> Memuat Snapshot...');
    $('#btnCetakBeritaAcara').attr('href', '<?= base_url('accounting/void-logs/cetak') ?>/' + id);
    $('#modalVoidDetail').modal('show');

    $.ajax({
        url: '<?= base_url('accounting/void-logs/detail') ?>/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                const log = res.data;
                $('#voidDetailTitle').html('<i class="fas fa-receipt mr-1"></i> Snapshot: ' + log.void_number);
                
                let html = `
                    <div class="row mb-3 pb-2 border-bottom text-sm">
                        <div class="col-md-6">
                            <span class="text-muted d-block text-xs">NO. REFERENSI ASAL:</span>
                            <strong class="text-dark font-monospace" style="font-size: 15px;">${log.reference_number}</strong>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span class="text-muted d-block text-xs">TOTAL DIBATALKAN:</span>
                            <strong class="text-danger" style="font-size: 16px;">Rp ${new Intl.NumberFormat('id-ID').format(log.total_amount)}</strong>
                        </div>
                    </div>
                    <div class="row mb-3 text-xs">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Modul:</strong> <span class="badge badge-light border text-uppercase">${log.transaction_type}</span></p>
                            <p class="mb-1"><strong>Waktu Void:</strong> ${log.created_at}</p>
                            <p class="mb-1"><strong>Metode Bayar:</strong> ${log.payment_method.toUpperCase()}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Kasir Pengaju:</strong> ${log.cashier_name || log.cashier_username}</p>
                            <p class="mb-1"><strong>Supervisor Pengesah:</strong> ${log.supervisor_name || log.supervisor_username} (${log.supervisor_role_name || 'Supervisor'})</p>
                            <p class="mb-1"><strong>Jurnal Pembalik:</strong> <code>${log.reversal_journal_no || 'None'}</code></p>
                        </div>
                    </div>
                    <div class="alert alert-light border p-2 mb-3 text-xs">
                        <strong class="d-block text-danger mb-0.5"><i class="fas fa-circle-exclamation mr-1"></i> Alasan Pembatalan (${log.reason_category}):</strong>
                        <span class="text-secondary">${log.reason_detail}</span>
                    </div>
                `;

                if (log.item_snapshot_json) {
                    html += `
                        <h6 class="font-weight-bold text-dark text-xs text-uppercase mb-2">Item Snapshot Transaksi:</h6>
                        <pre class="bg-light p-2 border rounded" style="font-size: 11px; max-height: 200px; overflow: auto;">${JSON.stringify(log.snapshot_decoded, null, 2)}</pre>
                    `;
                }

                $('#voidDetailBody').html(html);
            }
        },
        error: function() {
            $('#voidDetailBody').html('<div class="alert alert-danger text-center"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat snapshot detail log void.</div>');
        }
    });
}
</script>

<style>
.bg-danger-soft { background-color: rgba(220, 53, 69, 0.12); color: #dc3545; }
.bg-warning-soft { background-color: rgba(255, 193, 7, 0.15); color: #856404; }
.bg-teal-soft { background-color: rgba(32, 201, 151, 0.12); color: #20c997; }
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.12); color: #0d6efd; }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.12); color: #198754; }
</style>
<?= $this->endSection() ?>
