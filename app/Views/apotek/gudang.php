<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-warehouse text-teal mr-2"></i> Gudang Farmasi & Multi-Depo Pelayanan
                </h1>
                <small class="text-muted">Pemisahan stok fisik antara Gudang Induk Logistik dengan Depo Apotek, Depo UGD, dan Depo Rawat Inap serta mutasi transfer antar-lokasi</small>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#modalTransferStok">
                    <i class="fas fa-dolly-flatbed mr-1"></i> + Buat Mutasi Transfer Stok
                </button>
                <a href="<?= base_url('apotek/stok') ?>" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm mr-1">
                    <i class="fas fa-boxes-stacked mr-1"></i> Master Stok Global
                </a>
                <a href="<?= base_url('procurement/po') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-truck-moving mr-1"></i> Penerimaan PBF (GRN)
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
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Lokasi Gudang & Depo</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalWarehouses ?> <small class="text-muted">Unit</small></h4>
                                <small class="text-muted">Gudang induk & depo aktif</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-warehouse fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-info text-uppercase">Stok Gudang Induk</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= number_format($totalIndukStock, 0, ',', '.') ?> <small class="text-muted">Item</small></h4>
                                <small class="text-muted">Persediaan pusat logistik</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-boxes-stacked fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-success text-uppercase">Stok Depo Pelayanan</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= number_format($totalDepoStock, 0, ',', '.') ?> <small class="text-muted">Item</small></h4>
                                <small class="text-muted">Siap dispensing di poli & UGD</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-prescription-bottle-medical fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #8b5cf6 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-purple text-uppercase">Riwayat Mutasi Transfer</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalTransferCount ?> <small class="text-muted">Surat Jalan</small></h4>
                                <small class="text-muted">Perpindahan antar lokasi</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-purple">
                                <i class="fas fa-truck-ramp-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS: 1. DISTRIBUSI STOK PER GUDANG / DEPO, 2. SURAT MUTASI TRANSFER, 3. MASTER LOKASI GUDANG -->
        <div class="card shadow-sm bg-white" style="border: 1px solid #b8b8b8;">
            <div class="card-header bg-white p-2 border-bottom">
                <ul class="nav nav-pills" id="warehouseTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold text-xs py-2 px-3" id="tab-stock-link" data-toggle="pill" href="#tab-stock" role="tab">
                            <i class="fas fa-cubes text-teal mr-1"></i> Distribusi Stok per Lokasi Gudang / Depo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-transfer-link" data-toggle="pill" href="#tab-transfer" role="tab">
                            <i class="fas fa-dolly text-info mr-1"></i> Riwayat Mutasi Transfer Antar-Gudang (<?= count($transfers) ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-master-wh-link" data-toggle="pill" href="#tab-master-wh" role="tab">
                            <i class="fas fa-building text-warning mr-1"></i> Master Lokasi Gudang & Depo (<?= count($warehouses) ?>)
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3">
                <div class="tab-content" id="warehouseTabContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: DISTRIBUSI STOK PER LOKASI GUDANG & DEPO -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-stock" role="tabpanel">
                        <!-- Location Filter Pills -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 bg-light p-2 rounded border">
                            <div class="d-flex flex-wrap align-items-center">
                                <span class="font-weight-bold text-xs text-dark mr-3"><i class="fas fa-filter text-teal mr-1"></i> Filter Lokasi:</span>
                                <a href="<?= base_url('apotek/gudang') ?>" class="btn btn-xs <?= empty($selectedWhId) ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold mr-1 mb-1">
                                    Semua Lokasi (<?= count($warehouseStocks) ?> Data)
                                </a>
                                <?php foreach ($warehouses as $wh): ?>
                                    <a href="<?= base_url('apotek/gudang?warehouse_id=' . $wh->id) ?>" class="btn btn-xs <?= $selectedWhId == $wh->id ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold mr-1 mb-1">
                                        <?= esc($wh->name) ?> (<?= esc($wh->code) ?>)
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div>
                                <button type="button" class="btn btn-teal btn-xs font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTransferStok">
                                    <i class="fas fa-plus mr-1"></i> Transfer Stok Obat
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered table-sm mb-0" id="table-warehouse-stock" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th style="width: 140px;">LOKASI GUDANG / DEPO</th>
                                        <th>NAMA OBAT / ALKES</th>
                                        <th style="width: 100px;">KODE OBAT</th>
                                        <th style="width: 110px;">BATCH NO</th>
                                        <th style="width: 110px;" class="text-center">EXPIRED DATE</th>
                                        <th style="width: 90px;" class="text-center">STOK RIIL</th>
                                        <th style="width: 80px;">SATUAN</th>
                                        <th style="width: 120px;" class="text-right">HARGA JUAL</th>
                                        <th style="width: 100px;" class="text-center">AKSI CEPAT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($warehouseStocks)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">Belum ada data stok pada lokasi gudang yang dipilih.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1; foreach ($warehouseStocks as $stk): 
                                            $isExpired = strtotime($stk->expired_date) <= time();
                                            $isNearExp = strtotime($stk->expired_date) <= strtotime('+60 days') && !$isExpired;
                                        ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <span class="badge <?= $stk->warehouse_type === 'main' ? 'badge-primary' : 'badge-teal text-white' ?> px-2 py-1">
                                                    <?= esc($stk->warehouse_name) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-dark"><?= esc($stk->medicine_name) ?></strong>
                                            </td>
                                            <td><span class="badge badge-light border text-monospace"><?= esc($stk->medicine_code) ?></span></td>
                                            <td><span class="badge badge-secondary text-monospace"><?= esc($stk->batch_no) ?></span></td>
                                            <td class="text-center font-weight-bold <?= $isExpired ? 'text-danger' : ($isNearExp ? 'text-warning' : 'text-dark') ?>">
                                                <?= date('d/m/Y', strtotime($stk->expired_date)) ?>
                                                <?php if ($isExpired): ?>
                                                    <span class="badge badge-danger text-xs d-block">EXPIRED</span>
                                                <?php elseif ($isNearExp): ?>
                                                    <span class="badge badge-warning text-xs d-block">< 60 Hari</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center font-weight-bold h6 mb-0 text-teal">
                                                <?= number_format($stk->stock, 0, ',', '.') ?>
                                            </td>
                                            <td><?= esc($stk->unit) ?></td>
                                            <td class="text-right font-weight-bold text-dark">
                                                Rp <?= number_format($stk->price, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold btn-quick-transfer" 
                                                        data-wh-id="<?= $stk->warehouse_id ?>" 
                                                        data-med-id="<?= $stk->medicine_id ?>" 
                                                        data-med-name="<?= esc($stk->medicine_name) ?>"
                                                        data-batch-id="<?= $stk->batch_id ?>"
                                                        data-batch-no="<?= esc($stk->batch_no) ?>"
                                                        data-unit="<?= esc($stk->unit) ?>"
                                                        data-stock="<?= $stk->stock ?>"
                                                        title="Transfer Item Ini">
                                                    <i class="fas fa-share mr-1"></i> Transfer
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: RIWAYAT SURAT MUTASI TRANSFER ANTAR-GUDANG -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-transfer" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered table-sm mb-0" id="table-transfers" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th style="width: 140px;">NO. SURAT MUTASI</th>
                                        <th style="width: 100px;">TANGGAL</th>
                                        <th>GUDANG ASAL (DARI)</th>
                                        <th>GUDANG TUJUAN (KE)</th>
                                        <th style="width: 90px;" class="text-center">TOTAL ITEM</th>
                                        <th style="width: 90px;" class="text-center">TOTAL QTY</th>
                                        <th style="width: 110px;">PETUGAS</th>
                                        <th style="width: 90px;" class="text-center">STATUS</th>
                                        <th style="width: 110px;" class="text-center">DOKUMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transfers)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">Belum ada transaksi mutasi transfer stok antar-gudang.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $tNo = 1; foreach ($transfers as $trf): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $tNo++ ?></td>
                                            <td>
                                                <strong class="text-teal font-monospace"><?= esc($trf->transfer_no) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($trf->transfer_date)) ?></td>
                                            <td>
                                                <span class="badge badge-secondary px-2 py-1"><?= esc($trf->source_name) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-teal px-2 py-1 text-white"><?= esc($trf->target_name) ?></span>
                                            </td>
                                            <td class="text-center font-weight-bold"><?= (int)$trf->item_count ?> Item</td>
                                            <td class="text-center font-weight-bold text-dark"><?= number_format($trf->total_qty ?? 0, 0, ',', '.') ?></td>
                                            <td><?= esc($trf->requester_name ?: 'Apoteker') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-success px-2 py-1 text-uppercase"><?= esc($trf->status) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('apotek/cetak-surat-mutasi/' . $trf->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold shadow-sm">
                                                    <i class="fas fa-print mr-1"></i> Surat Jalan
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: MASTER LOKASI GUDANG & DEPO -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-master-wh" role="tabpanel">
                        <div class="row">
                            <?php foreach ($warehouses as $w): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm h-100 bg-white border">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge <?= $w->type === 'main' ? 'badge-primary' : 'badge-teal text-white' ?> text-uppercase mb-1">
                                                    <?= $w->type === 'main' ? 'Gudang Induk Logistik' : 'Depo Pelayanan Medis' ?>
                                                </span>
                                                <h5 class="font-weight-bold text-dark mb-0"><?= esc($w->name) ?></h5>
                                                <small class="text-monospace text-muted">Kode: <?= esc($w->code) ?></small>
                                            </div>
                                            <span class="badge badge-success px-2 py-1">AKTIF</span>
                                        </div>
                                        <div class="text-xs text-muted mb-2">
                                            <div><i class="fas fa-location-dot text-teal mr-1"></i> <strong>Lokasi:</strong> <?= esc($w->location ?: 'Klinik Sawamawa') ?></div>
                                            <div><i class="fas fa-user-shield text-info mr-1"></i> <strong>PIC / Apoteker:</strong> <?= esc($w->pic_name ?: 'Apoteker Penanggung Jawab') ?></div>
                                            <div><i class="fas fa-phone text-success mr-1"></i> <strong>Kontak / WA:</strong> <?= esc($w->phone ?: '-') ?></div>
                                        </div>
                                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                            <a href="<?= base_url('apotek/gudang?warehouse_id=' . $w->id) ?>" class="btn btn-outline-teal btn-xs font-weight-bold">
                                                <i class="fas fa-boxes-stacked mr-1"></i> Lihat Stok di Lokasi Ini
                                            </a>
                                            <button type="button" class="btn btn-teal btn-xs font-weight-bold btn-transfer-from" data-wh-id="<?= $w->id ?>">
                                                <i class="fas fa-dolly mr-1"></i> Buat Transfer dari Lokasi Ini
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: FORM MUTASI TRANSFER STOK ANTAR-GUDANG -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalTransferStok" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-teal text-white py-2">
                <h6 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-dolly-flatbed mr-1"></i> Form Mutasi Transfer Stok Obat Antar-Gudang / Depo
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('apotek/transfer-stok') ?>" method="post" id="formTransferStok">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    
                    <div class="row">
                        <!-- Source Warehouse -->
                        <div class="col-md-5 form-group mb-2">
                            <label class="text-xs font-weight-bold">Gudang / Depo Asal (DARI): <span class="text-danger">*</span></label>
                            <select name="source_warehouse_id" id="trf-source-wh" class="form-control form-control-sm font-weight-bold border-teal" required>
                                <?php foreach ($warehouses as $wh): ?>
                                    <option value="<?= $wh->id ?>"><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 text-center d-flex align-items-center justify-content-center pt-2">
                            <div class="p-2 bg-light rounded-circle text-teal border">
                                <i class="fas fa-arrow-right fa-lg"></i>
                            </div>
                        </div>

                        <!-- Target Warehouse -->
                        <div class="col-md-5 form-group mb-2">
                            <label class="text-xs font-weight-bold">Gudang / Depo Tujuan (KE): <span class="text-danger">*</span></label>
                            <select name="target_warehouse_id" id="trf-target-wh" class="form-control form-control-sm font-weight-bold border-teal" required>
                                <?php foreach ($warehouses as $wh): ?>
                                    <option value="<?= $wh->id ?>" <?= $wh->code === 'DEPO-RJ' ? 'selected' : '' ?>><?= esc($wh->name) ?> (<?= esc($wh->code) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Mutasi Transfer: <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date" class="form-control form-control-sm font-weight-bold" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-8 form-group mb-2">
                            <label class="text-xs font-weight-bold">Catatan / Keperluan Transfer:</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Pemenuhan stok harian Depo Rawat Jalan dari Gudang Induk">
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                        <label class="text-xs font-weight-bold text-dark mb-0">
                            <i class="fas fa-boxes-stacked text-teal mr-1"></i> Rincian Obat yang Ditransfer:
                        </label>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold" id="btn-add-transfer-item">
                            <i class="fas fa-plus mr-1"></i> Tambah Item Obat
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" id="table-transfer-items" style="font-size: 12.5px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>NAMA OBAT / ALKES</th>
                                    <th style="width: 180px;">BATCH NO & EXP</th>
                                    <th style="width: 90px;" class="text-center">QTY</th>
                                    <th style="width: 80px;">SATUAN</th>
                                    <th style="width: 140px;">KETERANGAN</th>
                                    <th style="width: 40px;" class="text-center"><i class="fas fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="medicine_id[]" class="form-control form-control-sm select-med" required>
                                            <option value="">-- Pilih Obat --</option>
                                            <?php foreach ($medicines as $m): ?>
                                                <option value="<?= $m->id ?>" data-unit="<?= esc($m->unit) ?>"><?= esc($m->name) ?> (<?= esc($m->code) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="batch_id[]" class="form-control form-control-sm select-batch">
                                            <option value="">-- Batch Otomatis --</option>
                                            <?php foreach ($batches as $b): ?>
                                                <option value="<?= $b->id ?>" data-med-id="<?= $b->medicine_id ?>"><?= esc($b->batch_no) ?> (ED: <?= date('d/m/y', strtotime($b->expired_date)) ?> | S: <?= $b->stock ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control form-control-sm text-center font-weight-bold" value="10" min="1" required>
                                    </td>
                                    <td>
                                        <input type="text" name="unit[]" class="form-control form-control-sm input-unit" value="Pcs">
                                    </td>
                                    <td>
                                        <input type="text" name="item_notes[]" class="form-control form-control-sm" placeholder="Catatan item...">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-trf-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Proses & Terbitkan Surat Mutasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add row to transfer items table
    $('#btn-add-transfer-item').click(function() {
        const firstRow = $('#table-transfer-items tbody tr:first').clone();
        firstRow.find('input').val('');
        firstRow.find('input[name="qty[]"]').val('10');
        firstRow.find('input[name="unit[]"]').val('Pcs');
        firstRow.find('select').val('');
        $('#table-transfer-items tbody').append(firstRow);
    });

    // Remove row
    $(document).on('click', '.btn-remove-trf-row', function() {
        if ($('#table-transfer-items tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    // Auto-fill unit when medicine is selected
    $(document).on('change', '.select-med', function() {
        const medId = $(this).val();
        const unit = $(this).find(':selected').data('unit') || 'Pcs';
        const tr = $(this).closest('tr');
        tr.find('.input-unit').val(unit);

        // Filter batches dropdown
        const batchSelect = tr.find('.select-batch');
        batchSelect.find('option').each(function() {
            const bMedId = $(this).data('med-id');
            if (!bMedId || bMedId == medId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Quick transfer button on stock row
    $('.btn-quick-transfer').click(function() {
        const whId = $(this).data('wh-id');
        const medId = $(this).data('med-id');
        const batchId = $(this).data('batch-id');
        const unit = $(this).data('unit');

        $('#trf-source-wh').val(whId);
        
        // Select target warehouse different from source
        $('#trf-target-wh option').each(function() {
            if ($(this).val() != whId) {
                $('#trf-target-wh').val($(this).val());
                return false;
            }
        });

        const firstRow = $('#table-transfer-items tbody tr:first');
        firstRow.find('.select-med').val(medId).trigger('change');
        firstRow.find('.select-batch').val(batchId);
        firstRow.find('.input-unit').val(unit);

        $('#modalTransferStok').modal('show');
    });

    // Transfer from warehouse card
    $('.btn-transfer-from').click(function() {
        const whId = $(this).data('wh-id');
        $('#trf-source-wh').val(whId);
        $('#trf-target-wh option').each(function() {
            if ($(this).val() != whId) {
                $('#trf-target-wh').val($(this).val());
                return false;
            }
        });
        $('#modalTransferStok').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
