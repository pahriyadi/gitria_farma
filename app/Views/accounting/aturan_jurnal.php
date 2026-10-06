<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- ========================================================================= -->
<!-- EXECUTIVE HEADER & QUICK STATS                                            -->
<!-- ========================================================================= -->
<?php
$totalCat = count($categories);
$activeCat = 0;
$balancedCat = 0;
$totalRulesCount = 0;
foreach ($categories as $c) {
    if (!empty($c->is_active)) $activeCat++;
    if (!empty($c->is_balanced)) $balancedCat++;
    $totalRulesCount += ($c->total_rules ?? 0);
}
?>
<div class="content-header p-0 mb-3">
    <div class="container-fluid p-0">
        <div class="row align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.45rem;">
                    <i class="fas fa-sliders text-teal mr-2"></i> Template &amp; Aturan Jurnal Multi-Akun
                </h1>
                <small class="text-muted">Konfigurasi pembagian bagi hasil (Dynamic COA Split) &amp; integrasi penjurnalan otomatis multi-modul</small>
            </div>
            <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
                <span class="badge badge-pill badge-light border text-teal px-3 py-2 font-weight-bold shadow-xs mr-1">
                    <i class="fas fa-folder-tree mr-1"></i> <?= $totalCat ?> Kategori
                </span>
                <span class="badge badge-pill badge-light border text-success px-3 py-2 font-weight-bold shadow-xs mr-1">
                    <i class="fas fa-scale-balanced mr-1"></i> <?= $balancedCat ?> / <?= $totalCat ?> Balanced
                </span>
                <span class="badge badge-pill badge-light border text-primary px-3 py-2 font-weight-bold shadow-xs">
                    <i class="fas fa-list-check mr-1"></i> <?= $totalRulesCount ?> Sub-Pos Akun
                </span>
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
            <a class="nav-link active font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/aturan-jurnal') ?>">
                <i class="fas fa-sliders text-teal mr-1"></i> 5. Template &amp; Aturan Jurnal
                <span class="badge badge-pill badge-teal ml-1"><?= $totalCat ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold py-2 text-truncate" href="<?= base_url('accounting/saldo-awal') ?>">
                <i class="fas fa-scale-balanced text-secondary mr-1"></i> 6. Saldo Awal
            </a>
        </li>
    </ul>
</div>

<!-- ========================================================================= -->
<!-- PANDUAN RINGKAS DYNAMIC COA SPLIT                                         -->
<!-- ========================================================================= -->
<div class="alert alert-light border shadow-sm d-flex align-items-start mb-3" style="border-left: 4px solid #20c997 !important; border-radius: 6px;">
    <div class="mr-3 text-teal mt-1" style="font-size: 20px;">
        <i class="fas fa-circle-info"></i>
    </div>
    <div class="text-sm flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="font-weight-bold text-dark mb-1">Panduan Formula Dynamic COA Split &amp; Penjurnalan Otomatis</h6>
            <button type="button" class="close text-muted" data-dismiss="alert" aria-label="Close" style="font-size: 16px;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <p class="text-muted mb-0" style="line-height: 1.5;">
            Halaman ini mengatur formula pembagian transaksi (Bagi Hasil / Revenue Split) multi-akun secara dinamis.
            Setiap transaksi di <strong>Farmasi Apotek</strong> (Obat Bebas, Resep Dokter, Konsul Online), <strong>Kasir Pelayanan Medis / Poli</strong>, dan <strong>Resto Gizi Sehat</strong> akan langsung dijurnal otomatis berpasangan (*Double-Entry*) berdasarkan komposisi pos akun yang ditentukan.
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
                <div class="rounded-circle p-3 mr-3 bg-teal-soft">
                    <i class="fas fa-folder-tree fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Total Kategori Template</span>
                    <h4 class="font-weight-bold mb-0 text-dark"><?= $totalCat ?> <span class="text-xs font-weight-normal text-muted">(<?= $activeCat ?> Aktif)</span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-success-soft">
                    <i class="fas fa-scale-balanced fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Status Keseimbangan</span>
                    <h4 class="font-weight-bold mb-0 text-success"><?= $balancedCat ?> / <?= $totalCat ?> <span class="text-xs font-weight-bold text-success">Balanced</span></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-primary-soft">
                    <i class="fas fa-list-check fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Total Sub-Pos Akun</span>
                    <h4 class="font-weight-bold mb-0 text-primary"><?= $totalRulesCount ?> Sub-Pos</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-2">
        <div class="card shadow-xs border-0 bg-white h-100 kpi-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="rounded-circle p-3 mr-3 bg-warning-soft">
                    <i class="fas fa-arrows-split-up-and-left fa-lg"></i>
                </div>
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">Modul Terintegrasi</span>
                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 15px;">Apotek, Kasir, Resto</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MAIN CARD & TABLE DAFTAR KATEGORI ATURAN JURNAL                           -->
<!-- ========================================================================= -->
<div class="card card-outline card-teal shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-2 mb-md-0">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-table-list text-teal mr-1"></i> Daftar Kategori Template &amp; Formula Jurnal Per-Akun
                </h5>
                <small class="text-muted d-block mt-0.5">Konfigurasi pos alokasi Debet &amp; Kredit untuk penjurnalan otomatis transaksi kasir faskes</small>
            </div>
            <div class="d-flex flex-wrap" style="gap: 6px;">
                <button class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalDefaultTemplate" title="Muat template aturan jurnal standar faskes & klinik">
                    <i class="fas fa-wand-magic-sparkles mr-1"></i> Muat Template Standar (Default)
                </button>
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddCategory">
                    <i class="fas fa-plus mr-1"></i> + Tambah Kategori Template
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="table-aturan-jurnal" class="table table-hover table-striped table-bordered datatable w-100" style="font-size: 12px;">
                <thead class="bg-light text-dark">
                    <tr>
                        <th style="width: 45px;" class="text-center">NO</th>
                        <th style="min-width: 180px;">KODE &amp; NAMA TEMPLATE KATEGORI</th>
                        <th style="width: 110px;" class="text-center">MODUL SISTEM</th>
                        <th style="min-width: 250px;">STRUKTUR SUB-POS ALOKASI AKUN</th>
                        <th style="width: 140px;" class="text-center">DEBET VS KREDIT</th>
                        <th style="width: 85px;" class="text-center">STATUS</th>
                        <th style="width: 130px;" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                <h6 class="font-weight-bold text-dark mb-1">Belum Ada Kategori Template Aturan Jurnal</h6>
                                <p class="text-muted text-sm mb-3">Klik tombol di bawah untuk memuat konfigurasi template jurnal standar bawaan sistem (*100% Balanced*), atau buat template baru secara manual.</p>
                                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#modalDefaultTemplate">
                                    <i class="fas fa-wand-magic-sparkles mr-1"></i> Muat Template Standar (Default)
                                </button>
                                <button class="btn btn-outline-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalAddCategory">
                                    <i class="fas fa-plus mr-1"></i> Tambah Kategori Manual
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($categories as $cat): ?>
                            <tr>
                                <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <div>
                                        <a href="<?= base_url('accounting/aturan-jurnal/detail/' . $cat->id) ?>" class="font-weight-bold text-teal text-decoration-none" title="Klik untuk mengelola sub-aturan akun">
                                            <?= esc($cat->category_name) ?>
                                        </a>
                                        <div class="mt-0.5">
                                            <span class="badge badge-light border text-muted font-monospace font-weight-bold">
                                                <code><?= esc($cat->category_code) ?></code>
                                            </span>
                                        </div>
                                        <?php if (!empty($cat->description)): ?>
                                            <small class="text-muted d-block mt-1" style="max-width: 320px; line-height: 1.3;">
                                                <?= esc($cat->description) ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $modBadge = 'badge-secondary';
                                    $modIcon = 'fa-tag';
                                    if ($cat->module === 'apotek') { $modBadge = 'badge-teal'; $modIcon = 'fa-prescription-bottle-medical'; }
                                    elseif ($cat->module === 'keuangan') { $modBadge = 'badge-success'; $modIcon = 'fa-cash-register'; }
                                    elseif ($cat->module === 'resto') { $modBadge = 'badge-warning text-dark'; $modIcon = 'fa-utensils'; }
                                    elseif ($cat->module === 'klinik') { $modBadge = 'badge-primary'; $modIcon = 'fa-hospital-user'; }
                                    ?>
                                    <span class="badge <?= $modBadge ?> py-1 px-2 font-weight-bold text-uppercase">
                                        <i class="fas <?= $modIcon ?> mr-1"></i> <?= esc($cat->module) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap" style="gap: 4px; max-width: 400px;">
                                        <?php if (empty($cat->rules)): ?>
                                            <span class="text-xs text-muted font-italic">Belum ada sub-aturan pos akun.</span>
                                        <?php else: ?>
                                            <?php foreach ($cat->rules as $r): ?>
                                                <?php
                                                $pillColor = $r->position === 'debit' ? 'badge-primary' : 'badge-info';
                                                $pctVal = (float)$r->percentage_value;
                                                $formattedPct = (floor($pctVal) == $pctVal) ? (int)$pctVal . '%' : number_format($pctVal, 2, '.', '') . '%';
                                                if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                                                    $valStr = 'Fee Dokter';
                                                    $pillColor = 'badge-warning text-dark';
                                                } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                                                    $valStr = 'Rp ' . number_format($r->fixed_amount_value, 0, ',', '.');
                                                } elseif ($pctVal > 0) {
                                                    $valStr = $formattedPct;
                                                } else {
                                                    $valStr = 'Formula';
                                                }
                                                ?>
                                                <span class="badge <?= $pillColor ?> font-weight-normal py-1 px-2" style="font-size: 11px;">
                                                    <strong><?= strtoupper($r->position[0]) ?>:</strong> <?= esc($r->item_name) ?> <span class="badge badge-light text-dark font-weight-bold"><?= $valStr ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="font-size: 11.5px; line-height: 1.4;">
                                        <?php if (!empty($cat->is_fixed_amount)): ?>
                                            <span class="text-primary font-weight-bold">Dr: Rp <?= number_format($cat->debit_fixed_sum, 0, ',', '.') ?></span><br>
                                            <span class="text-info font-weight-bold">Cr: Rp <?= number_format($cat->credit_fixed_sum, 0, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="text-primary font-weight-bold">Dr: <?= number_format($cat->debit_sum, 1) ?>%</span><br>
                                            <span class="text-info font-weight-bold">Cr: <?= number_format($cat->credit_sum, 1) ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($cat->is_balanced)): ?>
                                        <span class="badge badge-success px-2 py-1 mt-1 font-weight-bold">
                                            <i class="fas fa-check-circle mr-1"></i> 100% Balance
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark px-2 py-1 mt-1 font-weight-bold">
                                            <i class="fas fa-triangle-exclamation mr-1"></i> Belum Balance
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?= base_url('accounting/aturan-jurnal/toggle-category/' . $cat->id) ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <?php if ($cat->is_active): ?>
                                            <button type="submit" class="btn btn-xs btn-outline-success font-weight-bold shadow-xs" title="Klik untuk menonaktifkan">
                                                <i class="fas fa-check-circle mr-1"></i> Aktif
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-xs btn-outline-secondary font-weight-bold shadow-xs" title="Klik untuk mengaktifkan">
                                                <i class="fas fa-times-circle mr-1"></i> Non-Aktif
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('accounting/aturan-jurnal/detail/' . $cat->id) ?>" class="btn btn-teal btn-xs font-weight-bold shadow-xs" title="Kelola Pos-Pos Akun &amp; Formula Jurnal">
                                            <i class="fas fa-sliders mr-1"></i> Atur
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary btn-xs shadow-xs" onclick="editCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Kategori">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-xs shadow-xs" onclick="deleteCategory(<?= $cat->id ?>, '<?= esc(addslashes($cat->category_name)) ?>')" title="Hapus Kategori">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
<!-- MODAL TAMBAH / EDIT KATEGORI TEMPLATE                                     -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-teal text-white py-3">
                <h5 class="modal-title font-weight-bold" id="catModalTitle">
                    <i class="fas fa-folder-plus mr-1"></i> Tambah Kategori Template Jurnal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/aturan-jurnal/save-category') ?>" method="post" id="formCategory">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="cat_id" value="">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="category_code" id="cat_code" class="form-control text-uppercase font-monospace font-weight-bold" placeholder="Contoh: PENJUALAN_OBAT_BEBAS" required>
                        <small class="text-muted">Kode unik referensi sistem (huruf kapital tanpa spasi, gunakan underscore `_`)</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Nama Kategori Template <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" id="cat_name" class="form-control font-weight-bold" placeholder="Contoh: PENJUALAN OBAT RESEP" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Modul Terkait <span class="text-danger">*</span></label>
                        <select name="module" id="cat_module" class="form-control select2" required style="width: 100%;">
                            <option value="apotek">Farmasi &amp; Apotek (Obat Resep, Bebas, Online)</option>
                            <option value="keuangan">Keuangan &amp; Kasir Utama</option>
                            <option value="resto">Resto Sehat &amp; Dapur Gizi</option>
                            <option value="klinik">Pelayanan Medis &amp; Tindakan Poli</option>
                            <option value="umum">Umum / Transaksi Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Keterangan / Catatan Alur</label>
                        <textarea name="description" id="cat_description" class="form-control" rows="2" placeholder="Deskripsi alur pembagian pos akun transaksi ini..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="cat_is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="cat_is_active">Aktifkan Template Jurnal Ini</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-save-cat" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL LOAD DEFAULT / STANDARD JOURNAL TEMPLATES                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalDefaultTemplate" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-teal text-white py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-wand-magic-sparkles mr-1"></i> Muat Template Aturan Jurnal Standar Faskes
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/aturan-jurnal/reset-default') ?>" method="post" id="formDefaultTpl">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-xs mb-3" style="border-left: 4px solid #17a2b8 !important; font-size: 12.5px;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Fitur ini akan menginisialisasi atau menyinkronkan <strong>6 Template Aturan Jurnal Standar</strong> (*100% Double-Entry Balanced*) yang telah disesuaikan dengan alur operasional klinik, apotek, resto gizi, dan grosir distributor.
                    </div>

                    <h6 class="font-weight-bold text-dark mb-2 text-sm">Daftar Template Standar yang Akan Dibuat:</h6>
                    <div class="row mb-3" style="font-size: 11.5px;">
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-teal font-weight-bold text-uppercase mb-1">Apotek</span>
                                <strong class="d-block text-dark">1. Penjualan Obat Bebas (OTC)</strong>
                                <small class="text-muted">Kas (Dr 100%) vs Obat (49%), Pajak (11%), Penunjang (9%), Resep (7%), ADM (24%).</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-teal font-weight-bold text-uppercase mb-1">Apotek</span>
                                <strong class="d-block text-dark">2. Penjualan Obat Resep Dokter</strong>
                                <small class="text-muted">Kas (Dr 100%) vs Fee Dokter (5%), Obat (44%), Pajak (11%), Penunjang (9%), Resep (7%), ADM (24%).</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-primary font-weight-bold text-uppercase mb-1">Klinik / Apotek</span>
                                <strong class="d-block text-dark">3. Konsultasi Online &amp; Telemedis</strong>
                                <small class="text-muted">Kas (Dr 100%) vs Jasa Dokter Tetap (Rp 20rb), Utang Fee Dr, &amp; Alokasi 5 Pos Obat.</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-primary font-weight-bold text-uppercase mb-1">Klinik</span>
                                <strong class="d-block text-dark">4. Rawat Jalan Poli &amp; Tindakan Medis</strong>
                                <small class="text-muted">Kas Utama (Dr 100%) vs Jasa Medis Dokter (66.67%) &amp; Sarana Fasilitas Klinik (33.33%).</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-warning text-dark font-weight-bold text-uppercase mb-1">Resto Gizi</span>
                                <strong class="d-block text-dark">5. Penjualan Resto Gizi Sehat</strong>
                                <small class="text-muted">Kas Resto (Dr 100%) vs Pendapatan Resto Gizi Sehat (Cr 100%).</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light h-100">
                                <span class="badge badge-success font-weight-bold text-uppercase mb-1">Distributor</span>
                                <strong class="d-block text-dark">6. Penjualan Grosir B2B</strong>
                                <small class="text-muted">Kas/Bank (Dr 100%) vs Pendapatan Penjualan Grosir B2B (Cr 100%).</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-dark mb-2 text-sm">Pilih Metode Pemuatan Template:</h6>
                    <div class="card border p-3 mb-0 bg-light">
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="mode_merge" name="mode" value="merge" class="custom-control-input" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="mode_merge">
                                <i class="fas fa-code-merge text-teal mr-1"></i> Sinkronkan Template Standar (Direkomendasikan)
                            </label>
                            <small class="d-block text-muted pl-4">Menambahkan kategori standar yang belum ada tanpa menghapus atau mengubah kategori kustom buatan Anda.</small>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="mode_overwrite" name="mode" value="overwrite" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold text-danger" for="mode_overwrite">
                                <i class="fas fa-rotate text-danger mr-1"></i> Reset Total &amp; Muat Ulang Standar Pabrikan
                            </label>
                            <small class="d-block text-muted pl-4">Menghapus seluruh konfigurasi kategori jurnal dan sub-pos saat ini, lalu memuat ulang 6 template standar murni.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-load-default" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-play mr-1"></i> Proses Muat Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus Kategori -->
<form id="formDeleteCat" action="" method="post" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
function editCategory(cat) {
    $('#catModalTitle').html('<i class="fas fa-edit mr-1"></i> Edit Kategori Template Jurnal');
    $('#cat_id').val(cat.id);
    $('#cat_code').val(cat.category_code);
    $('#cat_name').val(cat.category_name);
    $('#cat_module').val(cat.module).trigger('change');
    $('#cat_description').val(cat.description || '');
    $('#cat_is_active').prop('checked', parseInt(cat.is_active) === 1);
    $('#modalAddCategory').modal('show');
}

$('#modalAddCategory').on('hidden.bs.modal', function () {
    $('#catModalTitle').html('<i class="fas fa-folder-plus mr-1"></i> Tambah Kategori Template Jurnal');
    $('#formCategory')[0].reset();
    $('#cat_id').val('');
    $('#cat_is_active').prop('checked', true);
});

// Anti double-submit proteksi
$('#formCategory').on('submit', function() {
    const $btn = $('#btn-save-cat');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
});

$('#formDefaultTpl').on('submit', function() {
    const $btn = $('#btn-load-default');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Template...');
});

function deleteCategory(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Kategori Jurnal?',
            text: 'Anda yakin ingin menghapus kategori "' + name + '" beserta seluruh sub-pos aturannya?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDeleteCat');
                form.action = '<?= base_url('accounting/aturan-jurnal/delete-category') ?>/' + id;
                form.submit();
            }
        });
    } else {
        if (confirm('Anda yakin ingin menghapus kategori "' + name + '" beserta seluruh sub-pos aturannya?')) {
            const form = document.getElementById('formDeleteCat');
            form.action = '<?= base_url('accounting/aturan-jurnal/delete-category') ?>/' + id;
            form.submit();
        }
    }
}
</script>

<style>
/* ========================================================================== */
/* ACCOUNTING SUITE MODERN SCROLLABLE TABS & DESIGN TOKENS                    */
/* ========================================================================== */
.accounting-tabs-scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}
.accounting-tabs-scroll-wrapper::-webkit-scrollbar {
    height: 4px;
}
.accounting-tabs-scroll-wrapper::-webkit-scrollbar-thumb {
    background-color: #20c997;
    border-radius: 4px;
}
.nav-tabs-modern {
    border-bottom: 2px solid #e9ecef;
    min-width: 100%;
}
.nav-tabs-modern .nav-link {
    color: #495057;
    border: none;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
}
.nav-tabs-modern .nav-link:hover {
    color: #00796b;
    border-bottom: 3px solid #b2dfdb;
    background-color: #f8f9fa;
}
.nav-tabs-modern .nav-link.active {
    color: #00796b !important;
    border-bottom: 3px solid #20c997 !important;
    background-color: #ffffff !important;
}
.kpi-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}
.bg-teal-soft {
    background: rgba(32, 201, 151, 0.15);
    color: #20c997;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-success-soft {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-primary-soft {
    background: rgba(0, 123, 255, 0.15);
    color: #007bff;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-warning-soft {
    background: rgba(255, 193, 7, 0.15);
    color: #e0a800;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.table td, .table th {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>
