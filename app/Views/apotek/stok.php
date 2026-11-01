<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>

<style>
    /* ========================================================================= */
    /* STOK OBAT & MASTER FARMASI: PAPER WHITE & PAPER DARK COMPLIANCE            */
    /* ========================================================================= */
    
    /* Card overrides */
    .card {
        box-shadow: none !important;
        border-radius: 4px !important;
    }
    body:not(.dark-mode) .card {
        border: 1px solid #b8b8b8 !important;
        background-color: #ffffff !important;
    }
    body.dark-mode .card,
    .dark-mode .card {
        border: 1px solid #374151 !important;
        background-color: #1f2328 !important;
        color: #f3f4f6 !important;
    }

    /* Info Boxes */
    body:not(.dark-mode) .info-box {
        background-color: #ffffff !important;
        border: 1px solid #b8b8b8 !important;
    }
    body.dark-mode .info-box,
    .dark-mode .info-box {
        background-color: #1f2328 !important;
        border: 1px solid #374151 !important;
        color: #f3f4f6 !important;
    }

    /* Sub-panels */
    body:not(.dark-mode) .bg-light {
        background-color: #f8f9fa !important;
    }
    body.dark-mode .bg-light,
    .dark-mode .bg-light {
        background-color: #181b1f !important;
        color: #f3f4f6 !important;
    }

    body.dark-mode .bg-white,
    .dark-mode .bg-white {
        background-color: #1f2328 !important;
        color: #f3f4f6 !important;
    }

    /* Nav Tabs */
    #custom-tabs-stok {
        border-bottom: 1px solid #b8b8b8;
    }
    body.dark-mode #custom-tabs-stok,
    .dark-mode #custom-tabs-stok {
        border-bottom: 1px solid #374151 !important;
    }
    body:not(.dark-mode) #custom-tabs-stok .nav-link {
        color: #444444;
    }
    body.dark-mode #custom-tabs-stok .nav-link,
    .dark-mode #custom-tabs-stok .nav-link {
        color: #9ca3af;
    }
    #custom-tabs-stok .nav-link.active {
        background-color: #0d9488 !important;
        color: #ffffff !important;
        border-color: #0d9488 !important;
    }

    /* Text adaptive */
    body.dark-mode .text-adaptive-dark,
    .dark-mode .text-adaptive-dark,
    body.dark-mode .text-dark,
    .dark-mode .text-dark {
        color: #f3f4f6 !important;
    }
    body:not(.dark-mode) .text-adaptive-dark {
        color: #333333 !important;
    }
    body.dark-mode .text-secondary,
    .dark-mode .text-secondary {
        color: #9ca3af !important;
    }

    /* Inputs, Selects & Input Groups */
    body.dark-mode .form-control,
    .dark-mode .form-control {
        background-color: #262b32 !important;
        border-color: #374151 !important;
        color: #f3f4f6 !important;
    }
    body.dark-mode .form-control:focus,
    .dark-mode .form-control:focus {
        border-color: #0d9488 !important;
        color: #f3f4f6 !important;
    }
    body.dark-mode .form-control[readonly],
    .dark-mode .form-control[readonly] {
        background-color: #181b1f !important;
        color: #9ca3af !important;
    }

    /* Modals */
    body.dark-mode .modal-content,
    .dark-mode .modal-content {
        background-color: #1f2328 !important;
        border: 1px solid #374151 !important;
        color: #f3f4f6 !important;
    }
    body.dark-mode .modal-header,
    .dark-mode .modal-header,
    body.dark-mode .modal-footer,
    .dark-mode .modal-footer {
        border-color: #374151 !important;
    }

    /* Tables (Strict Excel, No Striped) */
    .table,
    table.dataTable {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    body:not(.dark-mode) .table,
    body:not(.dark-mode) table.dataTable {
        border: 1.5px solid #a0a0a0 !important;
        background-color: #ffffff !important;
    }
    body.dark-mode .table,
    body.dark-mode table.dataTable,
    .dark-mode .table,
    .dark-mode table.dataTable {
        border: 1.5px solid #374151 !important;
        background-color: #1f2328 !important;
        color: #f3f4f6 !important;
    }
    body:not(.dark-mode) .table thead th,
    body:not(.dark-mode) table.dataTable thead th {
        background-color: #ffffff !important;
        color: #333333 !important;
        border: 1px solid #b0b0b0 !important;
    }
    body.dark-mode .table thead th,
    body.dark-mode table.dataTable thead th,
    .dark-mode .table thead th,
    .dark-mode table.dataTable thead th {
        background-color: #181b1f !important;
        color: #f3f4f6 !important;
        border: 1px solid #374151 !important;
    }
    body:not(.dark-mode) .table td,
    body:not(.dark-mode) table.dataTable td {
        border: 1px solid #b8b8b8 !important;
        color: #444444 !important;
        background-color: #ffffff;
    }
    body.dark-mode .table td,
    body.dark-mode table.dataTable td,
    .dark-mode .table td,
    .dark-mode table.dataTable td {
        border: 1px solid #374151 !important;
        color: #f3f4f6 !important;
        background-color: #1f2328 !important;
    }
    body:not(.dark-mode) .table-hover tbody tr:hover td {
        background-color: #f0f7f3 !important;
    }
    body.dark-mode .table-hover tbody tr:hover td,
    .dark-mode .table-hover tbody tr:hover td {
        background-color: #262b32 !important;
    }
    body.dark-mode .badge-light,
    .dark-mode .badge-light {
        background-color: #262b32 !important;
        border-color: #374151 !important;
        color: #d1d5db !important;
    }
    body:not(.dark-mode) .bg-teal-light {
        background-color: #e6fcf5 !important;
        color: #0ca678 !important;
    }
    body.dark-mode .bg-teal-light,
    .dark-mode .bg-teal-light {
        background-color: #133934 !important;
        color: #2dd4bf !important;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <!-- Top Metrics Cards -->
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-pills"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Total Master Obat</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($medicines) ?> <small class="text-muted">Item Terdaftar</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-info elevation-0"><i class="fas fa-dolly-flatbed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Total Batch Penerimaan</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($batches) ?> <small class="text-muted">Batch Fisik</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-warning elevation-0"><i class="fas fa-exclamation-triangle text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Stok Kritis / Di Bawah Min</span>
                        <?php 
                            $criticalCount = 0;
                            foreach ($medicines as $med) {
                                if ($med->total_stock <= $med->min_stock) $criticalCount++;
                            }
                        ?>
                        <span class="info-box-number text-danger h4 mb-0"><?= $criticalCount ?> <small class="text-muted">Item Obat</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-success elevation-0"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Total Unit Persediaan</span>
                        <?php 
                            $totalUnitsAll = array_sum(array_column($medicines, 'total_stock'));
                        ?>
                        <span class="info-box-number text-teal h4 mb-0"><?= number_format($totalUnitsAll, 0, ',', '.') ?> <small class="text-muted">Unit</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Navigation Tabs -->
        <div class="card card-teal card-outline card-outline-tabs shadow">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-stok" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-master-tab" data-toggle="pill" href="#tab-master" role="tab" aria-controls="tab-master" aria-selected="true">
                            <i class="fas fa-pills text-teal mr-1"></i> 1. KATALOG & MASTER DATA OBAT (CRUD)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-batches-tab" data-toggle="pill" href="#tab-batches" role="tab" aria-controls="tab-batches" aria-selected="false">
                            <i class="fas fa-boxes text-info mr-1"></i> 2. DAFTAR BATCH PENERIMAAN & KADALUARSA
                            <span class="badge badge-teal ml-1"><?= count($batches) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-stokContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: MASTER DATA OBAT (FULL CRUD) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-master" role="tabpanel" aria-labelledby="tab-master-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-list text-teal mr-1"></i> Master Data Obat & Ringkasan Pergerakan Stok</h5>
                                <small class="text-muted">Pengaturan katalog obat, harga jual, batas minimum buffer, dan pemantauan akumulasi stok fisik.</small>
                            </div>
                            <div>
                                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#medicineAddModal">
                                    <i class="fas fa-plus mr-1"></i> Tambah Master Obat Baru
                                </button>
                                <button class="btn btn-dark btn-sm font-weight-bold ml-1 shadow-sm" data-toggle="modal" data-target="#batchAddModal">
                                    <i class="fas fa-dolly mr-1"></i> Input Penerimaan Batch
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="table-medicines" class="table table-bordered table-hover datatable-serverside" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">NO</th>
                                        <th style="width: 85px;">KODE</th>
                                        <th>NAMA OBAT & GOLONGAN</th>
                                        <th style="width: 75px;" class="text-center">SATUAN</th>
                                        <th style="width: 110px;" class="text-right">HARGA JUAL</th>
                                        <th style="width: 85px;" class="text-center text-success">TOTAL MASUK</th>
                                        <th style="width: 85px;" class="text-center text-danger">TOTAL KELUAR</th>
                                        <th style="width: 110px;" class="text-center bg-teal-light font-weight-bold">SISA STOK FISIK</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 160px;" class="text-center">AKSI MANAJEMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris tabel diisi secara instan oleh DataTables Server-Side Processing -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: DAFTAR BATCH PENERIMAAN & KADALUARSA -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-batches" role="tabpanel" aria-labelledby="tab-batches-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-boxes text-info mr-1"></i> Rincian Seluruh Batch Fisik Penerimaan & Tanggal Expired</h5>
                                <small class="text-muted">Kelola nomor batch, harga beli distributor (HPP), sisa stok fisik per batch, serta tanggal kadaluarsa.</small>
                            </div>
                            <div>
                                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#batchAddModal">
                                    <i class="fas fa-plus mr-1"></i> Input Batch Penerimaan Baru
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">NO</th>
                                        <th style="width: 90px;">KODE</th>
                                        <th>NAMA OBAT & GOLONGAN</th>
                                        <th style="width: 120px;">NO. BATCH</th>
                                        <th style="width: 110px;" class="text-right">HARGA BELI (HPP)</th>
                                        <th style="width: 90px;" class="text-center font-weight-bold bg-teal-light">STOK BATCH</th>
                                        <th style="width: 110px;" class="text-center">TGL EXPIRED</th>
                                        <th style="width: 120px;" class="text-center">STATUS KADALUARSA</th>
                                        <th style="width: 120px;" class="text-center">AKSI BATCH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $no = 1;
                                        $today = date('Y-m-d');
                                        $warnDate = date('Y-m-d', strtotime('+90 days'));
                                    ?>
                                    <?php foreach ($batches as $b): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                            <td><span class="badge badge-secondary"><?= esc($b->medicine_code) ?></span></td>
                                            <td>
                                                <strong class="text-dark"><?= esc($b->medicine_name) ?></strong><br>
                                                <small class="text-muted">Golongan: <?= strtoupper(esc($b->type ?? 'OBAT')) ?> | Satuan: <?= esc($b->unit ?? 'Pcs') ?></small>
                                            </td>
                                            <td><span class="badge badge-light border font-weight-bold" style="font-size:12px;"><?= esc($b->batch_no) ?></span></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($b->buy_price, 0, ',', '.') ?></td>
                                            <td class="text-center font-weight-bold text-teal" style="font-size: 15px;">
                                                <?= esc($b->stock) ?> <small class="text-muted"><?= esc($b->unit ?? 'Pcs') ?></small>
                                            </td>
                                            <td class="text-center font-weight-bold">
                                                <?= date('d/m/Y', strtotime($b->expired_date)) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($b->expired_date <= $today): ?>
                                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-ban"></i> EXPIRED</span>
                                                <?php elseif ($b->expired_date <= $warnDate): ?>
                                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock"></i> &le; 90 Hari</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> AMAN</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <!-- Edit Batch Button -->
                                                <button class="btn btn-outline-info btn-xs btn-edit-batch font-weight-bold mr-1"
                                                        data-id="<?= $b->id ?>"
                                                        data-medname="<?= esc($b->medicine_name) ?>"
                                                        data-batch="<?= esc($b->batch_no) ?>"
                                                        data-buy="<?= esc($b->buy_price) ?>"
                                                        data-stock="<?= esc($b->stock) ?>"
                                                        data-exp="<?= esc($b->expired_date) ?>"
                                                        data-toggle="modal" data-target="#batchEditModal"
                                                        title="Edit Batch & Koreksi Stok">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <!-- Delete Batch Button -->
                                                <form action="<?= base_url('apotek/stok') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus batch <?= esc($b->batch_no) ?>?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_batch">
                                                    <input type="hidden" name="batch_id" value="<?= $b->id ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-xs font-weight-bold" title="Hapus Batch Ini">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
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
<!-- MODAL: TAMBAH MASTER OBAT -->
<!-- ========================================================================= -->
<div class="modal fade" id="medicineAddModal" tabindex="-1" role="dialog" aria-labelledby="medicineAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="medicineAddModalLabel"><i class="fas fa-plus-circle mr-1"></i> Tambah Master Obat Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('apotek/stok') ?>" method="post">
                <input type="hidden" name="action" value="add_medicine">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Obat <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-weight-bold" placeholder="MED-XXX" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Obat / Sediaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Paracetamol 500mg / Amoxicillin 500mg" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Golongan / Kategori Obat <span class="text-danger">*</span></label>
                            <select name="type" class="form-control font-weight-bold" data-native="true" required>
                                <option value="bebas">Obat Bebas / Bebas Terbatas</option>
                                <option value="keras">Obat Keras (Resep Dokter)</option>
                                <option value="obat_bebas">Obat Bebas (Standar BPOM)</option>
                                <option value="obat_keras">Obat Keras (Standar BPOM)</option>
                                <option value="suplemen">Suplemen & Vitamin</option>
                                <?php if (!empty($medicineCategories)): ?>
                                    <?php foreach ($medicineCategories as $mc): ?>
                                        <option value="<?= esc($mc->drug_class) ?>"><?= esc($mc->name) ?> (<?= strtoupper(str_replace('_', ' ', $mc->drug_class)) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Satuan Eceran Terkecil <span class="text-danger">*</span></label>
                            <select name="unit" class="form-control font-weight-bold" data-native="true" required>
                                <?php if (!empty($units)): ?>
                                    <?php foreach ($units as $u): ?>
                                        <option value="<?= esc($u->name) ?>"><?= esc($u->name) ?> (<?= esc($u->code) ?>)</option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Kapsul">Kapsul</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Tube">Tube</option>
                                    <option value="Ampul">Ampul</option>
                                    <option value="Sachet">Sachet</option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Pilih satuan takaran dispensing terkecil.</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Harga Jual per 1 Butir/Tablet (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control font-weight-bold" placeholder="1000" required min="0">
                            <small class="text-muted">Harga eceran per butir yang dikalikan saat dokter meresepkan.</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Limit Stok Kritis (Buffer Stock Alert) <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" class="form-control" value="10" required min="0">
                            <small class="text-muted">Peringatan otomatis menyala jika sisa stok berada di bawah angka ini.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Obat</label>
                            <select name="status" class="form-control font-weight-bold" data-native="true">
                                <option value="active">ACTIVE (Dapat Diresepkan)</option>
                                <option value="inactive">INACTIVE (Nonaktif)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Optional Initial Stock Section -->
                    <div class="card bg-light p-3 mt-2 border">
                        <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-boxes mr-1"></i> Stok Awal Penerimaan (Opsional)</h6>
                        <div class="row">
                            <div class="col-md-3 form-group mb-0">
                                <label class="text-xs">No. Batch Awal:</label>
                                <input type="text" name="initial_batch_no" class="form-control form-control-sm" placeholder="BCH-001">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="text-xs">Jumlah Stok Awal:</label>
                                <input type="number" name="initial_stock" class="form-control form-control-sm" placeholder="100" min="0">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="text-xs">Harga Beli HPP (Rp):</label>
                                <input type="number" name="initial_buy_price" class="form-control form-control-sm" placeholder="8000" min="0">
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="text-xs">Tanggal Expired:</label>
                                <input type="date" name="initial_expired_date" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Master Obat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT MASTER OBAT -->
<!-- ========================================================================= -->
<div class="modal fade" id="medicineEditModal" tabindex="-1" role="dialog" aria-labelledby="medicineEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title font-weight-bold text-white" id="medicineEditModalLabel"><i class="fas fa-edit mr-1"></i> Edit Data Master Obat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('apotek/stok') ?>" method="post">
                <input type="hidden" name="action" value="edit_medicine">
                <input type="hidden" name="id" id="edit-med-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Obat <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-med-code" class="form-control font-weight-bold" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Obat / Sediaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-med-name" class="form-control font-weight-bold" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Golongan / Kategori Obat <span class="text-danger">*</span></label>
                            <select name="type" id="edit-med-type" class="form-control font-weight-bold" data-native="true" required>
                                <option value="bebas">Obat Bebas / Bebas Terbatas</option>
                                <option value="keras">Obat Keras (Resep Dokter)</option>
                                <option value="obat_bebas">Obat Bebas (Standar BPOM)</option>
                                <option value="obat_keras">Obat Keras (Standar BPOM)</option>
                                <option value="suplemen">Suplemen & Vitamin</option>
                                <?php if (!empty($medicineCategories)): ?>
                                    <?php foreach ($medicineCategories as $mc): ?>
                                        <option value="<?= esc($mc->drug_class) ?>"><?= esc($mc->name) ?> (<?= strtoupper(str_replace('_', ' ', $mc->drug_class)) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Satuan Kemasan <span class="text-danger">*</span></label>
                            <select name="unit" id="edit-med-unit" class="form-control font-weight-bold" data-native="true" required>
                                <?php if (!empty($units)): ?>
                                    <?php foreach ($units as $u): ?>
                                        <option value="<?= esc($u->name) ?>"><?= esc($u->name) ?> (<?= esc($u->code) ?>)</option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Kapsul">Kapsul</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Tube">Tube</option>
                                    <option value="Ampul">Ampul</option>
                                    <option value="Sachet">Sachet</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Harga Jual Pasien (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit-med-price" class="form-control font-weight-bold" required min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Limit Stok Kritis (Buffer Stock Alert) <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" id="edit-med-min" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Obat</label>
                            <select name="status" id="edit-med-status" class="form-control font-weight-bold" data-native="true">
                                <option value="active">ACTIVE (Dapat Diresepkan)</option>
                                <option value="inactive">INACTIVE (Nonaktif)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TAMBAH BATCH PENERIMAAN -->
<!-- ========================================================================= -->
<div class="modal fade" id="batchAddModal" tabindex="-1" role="dialog" aria-labelledby="batchAddModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title font-weight-bold text-white" id="batchAddModalLabel"><i class="fas fa-dolly mr-1"></i> Input Penerimaan Batch Obat Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('apotek/stok') ?>" method="post">
                <input type="hidden" name="action" value="add_batch">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Obat Terkait <span class="text-danger">*</span></label>
                        <select name="medicine_id" id="add-batch-medicine-id" class="form-control font-weight-bold" data-native="true" required>
                            <option value="">-- Cari Kode atau Nama Obat --</option>
                            <?php foreach ($medicines as $m): ?>
                                <option value="<?= $m->id ?>">[<?= esc($m->code) ?>] <?= esc($m->name) ?> (<?= esc($m->unit) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nomor Batch Pabrikan / Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="batch_no" class="form-control font-weight-bold" placeholder="BCH-2026-XXX" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Beli Satuan Distributor (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="buy_price" class="form-control" placeholder="10000" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Stok Masuk Diterima <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control font-weight-bold" placeholder="100" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kadaluarsa (Expired Date) <span class="text-danger">*</span></label>
                        <input type="date" name="expired_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"><i class="fas fa-plus mr-1"></i> Tambah Batch & Mutasi Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT BATCH OBAT -->
<!-- ========================================================================= -->
<div class="modal fade" id="batchEditModal" tabindex="-1" role="dialog" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title font-weight-bold text-white" id="batchEditModalLabel"><i class="fas fa-edit mr-1"></i> Edit Batch & Koreksi Stok</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('apotek/stok') ?>" method="post">
                <input type="hidden" name="action" value="edit_batch">
                <input type="hidden" name="batch_id" id="edit-batch-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Obat:</label>
                        <input type="text" id="edit-batch-medname" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nomor Batch <span class="text-danger">*</span></label>
                        <input type="text" name="batch_no" id="edit-batch-no" class="form-control font-weight-bold" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Beli Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="buy_price" id="edit-batch-buy" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Stok Fisik Batch <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="edit-batch-stock" class="form-control font-weight-bold" required min="0">
                        <small class="text-muted">Jika diubah, selisih otomatis dicatat ke riwayat mutasi sebagai penyesuaian (*adjustment*).</small>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kadaluarsa <span class="text-danger">*</span></label>
                        <input type="date" name="expired_date" id="edit-batch-exp" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables Server-Side Master Obat (Aman dari Reinitialise Warning)
        if ($.fn.DataTable.isDataTable('#table-medicines')) {
            $('#table-medicines').DataTable().destroy();
        }
        $('#table-medicines').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: '<?= base_url('apotek/stok') ?>',
                type: 'GET'
            },
            order: [[2, 'asc']],
            columnDefs: [
                { targets: [0, 5, 6, 9], orderable: false },
                { targets: [0, 3, 5, 6, 7, 8, 9], className: 'text-center' },
                { targets: [4], className: 'text-right' }
            ],
            language: window.dtIndonesianLanguage,
            pageLength: 10
        });

        // Delegated Event Handler: Edit Master Obat
        $(document).on('click', '.btn-edit-medicine', function() {
            $('#edit-med-id').val($(this).data('id'));
            $('#edit-med-code').val($(this).data('code'));
            $('#edit-med-name').val($(this).data('name'));

            var medType = $(this).data('type') || 'bebas';
            $('#edit-med-type').val(medType);
            if (!$('#edit-med-type').val()) {
                if (medType === 'bebas') $('#edit-med-type').val('obat_bebas');
                else if (medType === 'keras') $('#edit-med-type').val('obat_keras');
                else if (medType === 'obat_bebas') $('#edit-med-type').val('bebas');
                else if (medType === 'obat_keras') $('#edit-med-type').val('keras');
            }
            if (!$('#edit-med-type').val()) {
                $('#edit-med-type').prop('selectedIndex', 0);
            }

            var medUnit = $(this).data('unit');
            $('#edit-med-unit').val(medUnit);
            if (!$('#edit-med-unit').val()) {
                $('#edit-med-unit').prop('selectedIndex', 0);
            }

            $('#edit-med-price').val($(this).data('price'));
            $('#edit-med-min').val($(this).data('min'));
            $('#edit-med-status').val($(this).data('status') || 'active');
            $('#medicineEditModal').modal('show');
        });

        // Delegated Event Handler: Tambah Batch Langsung
        $(document).on('click', '.btn-add-batch-direct', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const unit = $(this).data('unit');
            $('#add-batch-medicine-id').val(id).trigger('change');
            $('#batchAddModal').modal('show');
        });

        // Edit Batch Modal Population
        $(document).on('click', '.btn-edit-batch', function() {
            $('#edit-batch-id').val($(this).data('id'));
            $('#edit-batch-medname').val($(this).data('medname'));
            $('#edit-batch-no').val($(this).data('batch'));
            $('#edit-batch-buy').val($(this).data('buy'));
            $('#edit-batch-stock').val($(this).data('stock'));
            $('#edit-batch-exp').val($(this).data('exp'));
        });
    });
</script>
<?= $this->endSection() ?>
