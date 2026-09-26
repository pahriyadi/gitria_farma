<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-boxes-packing text-teal mr-2"></i> Inventaris & Manajemen Aset Klinik
                </h1>
                <small class="text-muted">Pencatatan aset medis & non-medis, mutasi ruangan, jadwal pemeliharaan, depresiasi nilai buku, dan cetak label stiker</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalAddAsset">
                    <i class="fas fa-plus mr-1"></i> Catat Aset Baru
                </button>
                <a href="<?= base_url('inventaris/cetak-laporan') ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak Rekap Aset
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
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0d9488 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Total Nilai Perolehan</span>
                                <h5 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($totalAcquisition, 0, ',', '.') ?></h5>
                                <small class="text-muted"><?= count($assets) ?> Unit Aset Aktif</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-calculator fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-info text-uppercase">Nilai Buku Saat Ini</span>
                                <h5 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($totalBookValue, 0, ',', '.') ?></h5>
                                <small class="text-muted">Setelah depresiasi berkala</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Kondisi Baik (Prima)</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $goodCondition ?> <small class="text-muted">Unit</small></h4>
                                <small class="text-muted">Siap operasi pelayanan</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-warning text-uppercase">Servis & Perbaikan</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $maintenanceCount ?> <small class="text-muted">Unit</small></h4>
                                <small class="text-muted">Perlu tindakan teknisi</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-warning">
                                <i class="fas fa-screwdriver-wrench fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="asset-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-assets-link" data-toggle="pill" href="#tab-assets" role="tab">
                            <i class="fas fa-boxes-stacked text-teal mr-1"></i> 1. MASTER DATA ASET
                            <span class="badge badge-teal ml-2"><?= count($assets) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-mutasi-link" data-toggle="pill" href="#tab-mutasi" role="tab">
                            <i class="fas fa-right-left text-info mr-1"></i> 2. MUTASI & RELOKASI
                            <span class="badge badge-info ml-2"><?= count($mutations) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-servis-link" data-toggle="pill" href="#tab-servis" role="tab">
                            <i class="fas fa-wrench text-warning mr-1"></i> 3. PEMELIHARAAN & SERVIS
                            <span class="badge badge-warning ml-2"><?= count($maintenances) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-depresiasi-link" data-toggle="pill" href="#tab-depresiasi" role="tab">
                            <i class="fas fa-arrow-trend-down text-purple mr-1"></i> 4. PENYUSUTAN (DEPRESIASI)
                            <span class="badge badge-secondary ml-2"><?= count($depreciations) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="asset-tabsContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: MASTER DATA ASET -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-assets" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Inventaris Aset Medis, Elektronik & Operasional</h6>
                                <small class="text-muted">Daftar aktiva tetap klinik lengkap dengan lokasi penempatan, penanggung jawab, dan nilai buku</small>
                            </div>
                            <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddAsset">
                                <i class="fas fa-plus mr-1"></i> Tambah Aset
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;" class="text-center">Kode Aset</th>
                                        <th>Nama Barang / Merk</th>
                                        <th>Kategori</th>
                                        <th>Lokasi Penempatan</th>
                                        <th>Penanggung Jawab</th>
                                        <th class="text-right">Harga Perolehan</th>
                                        <th class="text-right">Nilai Buku</th>
                                        <th class="text-center">Kondisi</th>
                                        <th style="width: 170px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assets as $ast): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($ast->code) ?></td>
                                            <td>
                                                <strong class="text-dark"><?= esc($ast->name) ?></strong>
                                                <?php if (!empty($ast->brand)): ?>
                                                    <small class="d-block text-muted"><?= esc($ast->brand) ?> <?= !empty($ast->serial_number) ? '(SN: ' . esc($ast->serial_number) . ')' : '' ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-light border font-weight-bold"><?= esc($ast->category) ?></span></td>
                                            <td><i class="fas fa-location-dot text-danger mr-1"></i> <?= esc($ast->location) ?></td>
                                            <td><?= esc($ast->pj_employee) ?></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($ast->price, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-info">Rp <?= number_format($ast->current_value !== null ? $ast->current_value : $ast->price, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <?php 
                                                $condBadge = [
                                                    'good'        => 'success',
                                                    'damaged'     => 'danger',
                                                    'maintenance' => 'warning text-dark'
                                                ][$ast->condition_status] ?? 'secondary';
                                                $condText = [
                                                    'good'        => 'BAIK',
                                                    'damaged'     => 'RUSAK',
                                                    'maintenance' => 'SERVIS'
                                                ][$ast->condition_status] ?? strtoupper(esc($ast->condition_status));
                                                ?>
                                                <span class="badge badge-<?= $condBadge ?> px-2 py-1"><?= $condText ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('inventaris/cetak-label/' . $ast->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold mr-1" title="Cetak Stiker Label QR">
                                                    <i class="fas fa-qrcode"></i> Label
                                                </a>
                                                <button class="btn btn-outline-info btn-xs btn-edit-asset mr-1"
                                                        data-id="<?= $ast->id ?>"
                                                        data-code="<?= esc($ast->code) ?>"
                                                        data-name="<?= esc($ast->name) ?>"
                                                        data-brand="<?= esc($ast->brand ?? '') ?>"
                                                        data-category="<?= esc($ast->category) ?>"
                                                        data-location="<?= esc($ast->location) ?>"
                                                        data-purchase_date="<?= esc($ast->purchase_date) ?>"
                                                        data-price="<?= esc($ast->price) ?>"
                                                        data-useful_life="<?= esc($ast->useful_life_years ?? 4) ?>"
                                                        data-salvage="<?= esc($ast->salvage_value ?? 0) ?>"
                                                        data-pj="<?= esc($ast->pj_employee) ?>"
                                                        data-sn="<?= esc($ast->serial_number ?? '') ?>"
                                                        data-condition="<?= esc($ast->condition_status) ?>"
                                                        data-supplier="<?= esc($ast->supplier_id ?? '') ?>"
                                                        title="Edit Aset">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-teal btn-xs btn-open-mutasi mr-1"
                                                        data-id="<?= $ast->id ?>"
                                                        data-name="<?= esc($ast->name) ?> (<?= esc($ast->code) ?>)"
                                                        data-location="<?= esc($ast->location) ?>"
                                                        data-pj="<?= esc($ast->pj_employee) ?>"
                                                        title="Mutasi Lokasi">
                                                    <i class="fas fa-right-left"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: MUTASI & RELOKASI -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-mutasi" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Riwayat Mutasi & Perpindahan Lokasi Aset</h6>
                                <small class="text-muted">Catatan perpindahan ruangan, serah terima penanggung jawab aset antar departemen</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;" class="text-center">Kode Aset</th>
                                        <th>Nama Aset</th>
                                        <th>Lokasi Lama &rarr; Lokasi Baru</th>
                                        <th>PJ Lama &rarr; PJ Baru</th>
                                        <th>Tanggal Mutasi</th>
                                        <th>Catatan Alasan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mutations as $mut): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($mut->asset_code) ?></td>
                                            <td><strong><?= esc($mut->asset_name) ?></strong></td>
                                            <td>
                                                <span class="text-muted"><?= esc($mut->old_location) ?></span>
                                                <i class="fas fa-arrow-right text-teal mx-1"></i>
                                                <strong class="text-dark"><?= esc($mut->new_location) ?></strong>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?= esc($mut->old_pj) ?></span>
                                                <i class="fas fa-arrow-right text-teal mx-1"></i>
                                                <strong class="text-dark"><?= esc($mut->new_pj) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($mut->mutation_date)) ?></td>
                                            <td><?= esc($mut->notes ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: PEMELIHARAAN & SERVIS -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-servis" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Catatan Pemeliharaan, Servis & Kalibrasi Aset</h6>
                                <small class="text-muted">Pemeliharaan preventif dan perbaikan mesin/peralatan medis klinik</small>
                            </div>
                            <button class="btn btn-warning btn-sm font-weight-bold shadow-sm text-dark" data-toggle="modal" data-target="#modalAddService">
                                <i class="fas fa-wrench mr-1"></i> Catat Servis Aset
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;" class="text-center">Kode Aset</th>
                                        <th>Nama Aset & Lokasi</th>
                                        <th>Tanggal Servis</th>
                                        <th>Teknisi / Vendor</th>
                                        <th>Uraian Tindakan Perbaikan</th>
                                        <th class="text-right">Biaya Servis (Rp)</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maintenances as $mnt): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($mnt->asset_code) ?></td>
                                            <td><strong><?= esc($mnt->asset_name) ?></strong> <small class="text-muted">(<?= esc($mnt->location) ?>)</small></td>
                                            <td><?= date('d/m/Y', strtotime($mnt->service_date)) ?></td>
                                            <td><strong><?= esc($mnt->technician_vendor) ?></strong></td>
                                            <td><?= esc($mnt->description) ?></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($mnt->cost, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-success text-uppercase px-2 py-1">SELESAI</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 4: PENYUSUTAN (DEPRESIASI) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-depresiasi" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Perhitungan Penyusutan Aset (Garis Lurus / Straight-Line)</h6>
                                <small class="text-muted">Penyusutan nilai aktiva tetap berdasarkan masa manfaat aset</small>
                            </div>
                            <button class="btn btn-purple btn-sm font-weight-bold shadow-sm text-white" style="background:#8b5cf6;" data-toggle="modal" data-target="#modalAddDepreciation">
                                <i class="fas fa-calculator mr-1"></i> Hitung Depresiasi Aset
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;" class="text-center">Kode Aset</th>
                                        <th>Nama Aset</th>
                                        <th>Tanggal Depresiasi</th>
                                        <th class="text-right">Harga Perolehan</th>
                                        <th class="text-right">Nilai Penyusutan</th>
                                        <th class="text-right">Sisa Nilai Buku</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($depreciations as $dep): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($dep->asset_code) ?></td>
                                            <td><strong><?= esc($dep->asset_name) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($dep->depreciation_date)) ?></td>
                                            <td class="text-right">Rp <?= number_format($dep->original_price, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-danger">- Rp <?= number_format($dep->amount, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-success">Rp <?= number_format($dep->book_value_after, 0, ',', '.') ?></td>
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
<!-- MODAL: TAMBAH / EDIT ASET -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddAsset" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleAssetModal">
                    <i class="fas fa-boxes-packing mr-1 text-teal"></i> Catat Aset Inventaris Baru
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('inventaris/aset') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" id="asset-action" value="add_asset">
                <input type="hidden" name="id" id="asset-id" value="">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kode Aset:</label>
                            <input type="text" name="code" id="ast-code" class="form-control form-control-sm font-weight-bold" placeholder="Auto (AST-ELEK-0001)">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kategori Aset: <span class="text-danger">*</span></label>
                            <select name="category" id="ast-category" class="form-control form-control-sm font-weight-bold" required>
                                <option value="Medis">Peralatan Medis & Laboratorium</option>
                                <option value="Elektronik">Peralatan Elektronik & IT</option>
                                <option value="Mebel">Mebel & Furnitur Ruangan</option>
                                <option value="Kendaraan">Kendaraan & Ambulans</option>
                                <option value="Umum">Sarana & Prasarana Umum</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Barang Aset: <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="ast-name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Mesin USG 4D / Monitor Pasien / Laptop Kasir" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Merk / Brand:</label>
                            <input type="text" name="brand" id="ast-brand" class="form-control form-control-sm" placeholder="Contoh: Mindray / GE / Samsung">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Lokasi Penempatan: <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="ast-location" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Ruang Poli Umum / Lab" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Penanggung Jawab (PJ) Aset: <span class="text-danger">*</span></label>
                            <input type="text" name="pj_employee" id="ast-pj" class="form-control form-control-sm font-weight-bold" placeholder="Nama Dokter / Staff PJ" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Harga Perolehan (Rp): <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="ast-price" class="form-control form-control-sm font-weight-bold text-teal" placeholder="15000000" required min="1">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Pembelian: <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" id="ast-purchase-date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Masa Manfaat (Tahun):</label>
                            <input type="number" name="useful_life_years" id="ast-useful-life" class="form-control form-control-sm" value="4" min="1" max="30">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nomor Seri / Serial Number:</label>
                            <input type="text" name="serial_number" id="ast-sn" class="form-control form-control-sm" placeholder="SN-99882211">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kondisi Fisik Saat Ini:</label>
                            <select name="condition_status" id="ast-condition" class="form-control form-control-sm">
                                <option value="good">Baik (Siap Pakai)</option>
                                <option value="maintenance">Dalam Perbaikan / Servis</option>
                                <option value="damaged">Rusak / Tidak Berfungsi</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Supplier / Vendor Pembelian:</label>
                            <select name="supplier_id" id="ast-supplier" class="form-control form-control-sm">
                                <option value="">-- Tanpa Vendor Terdaftar --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s->id ?>"><?= esc($s->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Data Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MUTASI LOKASI ASET -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalMutasiAsset" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-right-left mr-1 text-info"></i> Form Mutasi & Relokasi Aset
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('inventaris/mutasi') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="asset_id" id="mut-asset-id">
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Aset Terpilih:</label>
                        <input type="text" id="mut-asset-name" class="form-control form-control-sm font-weight-bold text-teal" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Lokasi Saat Ini (Lama):</label>
                            <input type="text" id="mut-old-location" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">PJ Saat Ini (Lama):</label>
                            <input type="text" id="mut-old-pj" class="form-control form-control-sm bg-light" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Lokasi Penempatan Baru: <span class="text-danger">*</span></label>
                            <input type="text" name="new_location" class="form-control form-control-sm font-weight-bold" placeholder="Ruang Poli Gigi" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">PJ Baru (Penerima): <span class="text-danger">*</span></label>
                            <input type="text" name="new_pj" class="form-control form-control-sm font-weight-bold" placeholder="Nama Staff / Dokter Penerima" required>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Tanggal Efektif Mutasi: <span class="text-danger">*</span></label>
                        <input type="date" name="mutation_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Catatan / Alasan Mutasi:</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Pemindahan unit untuk poli gigi">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Proses Mutasi Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CATAT SERVIS / PEMELIHARAAN ASET -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddService" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-wrench mr-1 text-warning"></i> Catat Pemeliharaan / Servis Aset
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('inventaris/servis') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Pilih Aset yang Diservis: <span class="text-danger">*</span></label>
                        <select name="asset_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($assets as $a): ?>
                                <option value="<?= $a->id ?>"><?= esc($a->name) ?> (<?= esc($a->code) ?>) - <?= esc($a->location) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Servis: <span class="text-danger">*</span></label>
                            <input type="date" name="service_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Biaya Servis / Sparepart (Rp):</label>
                            <input type="number" name="cost" class="form-control form-control-sm font-weight-bold" placeholder="350000" value="0">
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nama Teknisi / Vendor Servis:</label>
                        <input type="text" name="technician_vendor" class="form-control form-control-sm" placeholder="Contoh: PT. Medika Service / Teknisi Internal">
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Uraian Tindakan Perbaikan: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Contoh: Pembersihan filter, penggantian kabel power, kalibrasi sensor" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs font-weight-bold">Jadwal Servis Berikutnya:</label>
                            <input type="date" name="next_maintenance_date" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+6 months')) ?>">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs font-weight-bold">Status Kondisi Setelah Servis:</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="completed">Selesai (Siap Operasional)</option>
                                <option value="scheduled">Terjadwal Servis Rutin</option>
                                <option value="in_progress">Sedang Dikerjakan Teknisi</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold text-dark"><i class="fas fa-save mr-1"></i> Simpan Catatan Servis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: HITUNG DEPRESIASI NILAI BUKU ASET -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddDepreciation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-calculator mr-1 text-purple"></i> Perhitungan Depresiasi Nilai Buku Aset
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('inventaris/hitung-depresiasi') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Pilih Aset untuk Depresiasi: <span class="text-danger">*</span></label>
                        <select name="asset_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($assets as $a): ?>
                                <option value="<?= $a->id ?>">
                                    <?= esc($a->name) ?> (<?= esc($a->code) ?>) - Beli: Rp <?= number_format($a->price, 0, ',', '.') ?> (Masa: <?= $a->useful_life_years ?? 4 ?> Thn)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Periode Penyusutan: <span class="text-danger">*</span></label>
                        <select name="depreciation_period" class="form-control form-control-sm font-weight-bold" required>
                            <option value="tahunan">Penyusutan Tahunan (1 Tahun Penuh)</option>
                            <option value="bulanan">Penyusutan Bulanan (1/12 Tahun)</option>
                        </select>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 12px;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Penyusutan menggunakan metode <strong>Garis Lurus (*Straight-Line*)</strong>: Nilai perolehan dikurangi nilai residu dibagi masa manfaat. Nilai buku aset akan otomatis berkurang.
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-purple btn-sm font-weight-bold text-white" style="background:#8b5cf6;"><i class="fas fa-check mr-1"></i> Proses Penyusutan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit Asset
        $('.btn-edit-asset').click(function() {
            $('#titleAssetModal').html('<i class="fas fa-edit mr-1"></i> Edit Data Aset Inventaris');
            $('#asset-action').val('edit_asset');
            $('#asset-id').val($(this).data('id'));
            $('#ast-code').val($(this).data('code'));
            $('#ast-name').val($(this).data('name'));
            $('#ast-brand').val($(this).data('brand'));
            $('#ast-category').val($(this).data('category'));
            $('#ast-location').val($(this).data('location'));
            $('#ast-purchase-date').val($(this).data('purchase_date'));
            $('#ast-price').val($(this).data('price'));
            $('#ast-useful-life').val($(this).data('useful_life'));
            $('#ast-salvage').val($(this).data('salvage'));
            $('#ast-pj').val($(this).data('pj'));
            $('#ast-sn').val($(this).data('sn'));
            $('#ast-condition').val($(this).data('condition'));
            $('#ast-supplier').val($(this).data('supplier'));
            $('#modalAddAsset').modal('show');
        });

        // Reset Add Modal
        $('[data-target="#modalAddAsset"]').click(function() {
            if (!$('#asset-id').val()) {
                $('#titleAssetModal').html('<i class="fas fa-boxes-packing mr-1"></i> Catat Aset Inventaris Baru');
                $('#asset-action').val('add_asset');
                $('#asset-id').val('');
            }
        });

        // Open Mutasi Modal
        $('.btn-open-mutasi').click(function() {
            $('#mut-asset-id').val($(this).data('id'));
            $('#mut-asset-name').val($(this).data('name'));
            $('#mut-old-location').val($(this).data('location'));
            $('#mut-old-pj').val($(this).data('pj'));
            $('#modalMutasiAsset').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
