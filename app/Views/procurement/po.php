<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-boxes-packing text-teal mr-2"></i> Pengadaan Barang & Purchase Orders (PO)
                </h1>
                <small class="text-muted">Kelola pengajuan kebutuhan (PR), penerbitan PO ke supplier/PBF, penerimaan barang fisik (GRN), dan master vendor</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalCreatePr">
                    <i class="fas fa-file-circle-plus mr-1"></i> Ajukan PR Baru
                </button>
                <button class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalAddSupplier">
                    <i class="fas fa-truck-field mr-1"></i> Tambah Supplier PBF
                </button>
                <a href="<?= base_url('procurement/approval') ?>" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-clipboard-check mr-1"></i> Persetujuan (Approvals)
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
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Purchase Requisitions</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($prs) ?> <small class="text-muted">Pengajuan</small></h4>
                                <small class="text-muted">Kebutuhan unit klinik & apotek</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-file-invoice fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-info text-uppercase">Purchase Orders (PO)</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($pos) ?> <small class="text-muted">Order Aktif</small></h4>
                                <small class="text-muted">Diterbitkan ke vendor / PBF</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-truck-moving fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-success text-uppercase">Penerimaan Barang (GRN)</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($goodsReceipts) ?> <small class="text-muted">Surat Terima</small></h4>
                                <small class="text-muted">Stok & batch obat masuk</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-dolly fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-purple text-uppercase">Supplier & PBF Mitra</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($suppliers) ?> <small class="text-muted">Vendor</small></h4>
                                <small class="text-muted">Pedagang Besar Farmasi</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-purple">
                                <i class="fas fa-building-flag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="procurement-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-pr-link" data-toggle="pill" href="#tab-pr" role="tab">
                            <i class="fas fa-file-invoice text-teal mr-1"></i> 1. PURCHASE REQUISITIONS (PR)
                            <span class="badge badge-teal ml-2"><?= count($prs) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-po-link" data-toggle="pill" href="#tab-po" role="tab">
                            <i class="fas fa-truck-moving text-info mr-1"></i> 2. PURCHASE ORDERS (PO)
                            <span class="badge badge-info ml-2"><?= count($pos) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-grn-link" data-toggle="pill" href="#tab-grn" role="tab">
                            <i class="fas fa-dolly text-success mr-1"></i> 3. PENERIMAAN BARANG (GOODS RECEIPT / GRN)
                            <span class="badge badge-success ml-2"><?= count($goodsReceipts) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-suppliers-link" data-toggle="pill" href="#tab-suppliers" role="tab">
                            <i class="fas fa-truck-field text-purple mr-1"></i> 4. MASTER SUPPLIER / PBF VENDOR
                            <span class="badge badge-secondary ml-2"><?= count($suppliers) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="procurement-tabsContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: PURCHASE REQUISITIONS (PR) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-pr" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Pengajuan Kebutuhan Barang (Purchase Requisitions)</h6>
                                <small class="text-muted">Pengajuan pengadaan barang dari unit operasional sebelum disetujui menjadi Purchase Order (PO)</small>
                            </div>
                            <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalCreatePr">
                                <i class="fas fa-plus mr-1"></i> Ajukan PR Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. PR</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Unit / Departemen</th>
                                        <th>Supplier Tujuan</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-right">Estimasi Anggaran</th>
                                        <th class="text-center">Status</th>
                                        <th>Pemohon</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prs as $pr): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($pr->request_no) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($pr->created_at)) ?></td>
                                            <td><span class="badge badge-light border font-weight-bold text-dark"><?= esc($pr->department ?: 'Apotek Farmasi') ?></span></td>
                                            <td><strong><?= esc($pr->supplier_name) ?></strong></td>
                                            <td class="text-center font-weight-bold"><?= $pr->item_count > 0 ? $pr->item_count . ' Item' : 'Lump Sum' ?></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($pr->total_amount, 2, ',', '.') ?></td>
                                            <td class="text-center">
                                                <?php 
                                                $badgeColor = [
                                                    'draft'     => 'secondary',
                                                    'submitted' => 'warning text-dark',
                                                    'verified'  => 'info',
                                                    'approved'  => 'primary',
                                                    'completed' => 'success',
                                                    'rejected'  => 'danger'
                                                ][$pr->status] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $badgeColor ?> text-uppercase px-2 py-1"><?= esc($pr->status) ?></span>
                                            </td>
                                            <td><?= esc($pr->requester_name ?: 'Petugas Unit') ?></td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <button class="btn btn-outline-teal btn-xs font-weight-bold btn-view-pr mr-1" data-id="<?= $pr->id ?>" title="Lihat Dokumen PR">
                                                    <i class="fas fa-file-invoice"></i> Dokumen
                                                </button>
                                                <a href="<?= base_url('procurement/cetak-pr/' . $pr->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold" title="Cetak / Download PDF">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: PURCHASE ORDERS (PO) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-po" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Purchase Orders (PO) Resmi</h6>
                                <small class="text-muted">Surat pesanan resmi yang telah disetujui direksi dan diterbitkan ke supplier/PBF</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. PO</th>
                                        <th>Tanggal Order</th>
                                        <th>Ref. No. PR</th>
                                        <th>Supplier / PBF</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-right">Total Nilai PO</th>
                                        <th class="text-center">Status PO</th>
                                        <th style="width: 190px;" class="text-center">Aksi Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pos as $po): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($po->po_no) ?></td>
                                            <td><?= !empty($po->order_date) ? date('d/m/Y', strtotime($po->order_date)) : date('d/m/Y', strtotime($po->created_at)) ?></td>
                                            <td><small class="badge badge-secondary"><?= esc($po->request_no ?: 'PR Non-Workflow') ?></small></td>
                                            <td><strong><?= esc($po->supplier_name) ?></strong></td>
                                            <td class="text-center font-weight-bold"><?= $po->item_count > 0 ? $po->item_count . ' Item' : '1 Paket' ?></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($po->total_amount, 2, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $po->status === 'received' ? 'success' : ($po->status === 'ordered' ? 'primary' : 'secondary') ?> text-uppercase px-2 py-1">
                                                    <?= strtoupper(esc($po->status)) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('procurement/cetak-po/' . $po->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold mr-1" title="Cetak Purchase Order">
                                                    <i class="fas fa-print"></i> Cetak PO
                                                </a>
                                                <?php if ($po->status === 'ordered'): ?>
                                                    <button class="btn btn-success btn-xs font-weight-bold btn-receive-po" 
                                                            data-id="<?= $po->id ?>" 
                                                            data-no="<?= $po->po_no ?>"
                                                            data-supplier="<?= esc($po->supplier_name) ?>"
                                                            title="Proses Penerimaan Barang">
                                                        <i class="fas fa-dolly"></i> Terima
                                                    </button>
                                                <?php else: ?>
                                                    <?php if (!empty($po->grn_id)): ?>
                                                        <a href="<?= base_url('procurement/cetak-grn/' . $po->grn_id) ?>" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold" title="Lihat GRN">
                                                            <i class="fas fa-check-double"></i> GRN
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Selesai</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: PENERIMAAN BARANG (GOODS RECEIPT / GRN) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-grn" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Penerimaan Barang Fisik (Goods Receipt Notes)</h6>
                                <small class="text-muted">Bukti tanda terima barang dari supplier yang menambah stok apotek dan menjurnal hutang usaha</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. GRN</th>
                                        <th>No. PO</th>
                                        <th>No. Surat Jalan / Faktur</th>
                                        <th>Tanggal Terima</th>
                                        <th>Supplier / PBF</th>
                                        <th class="text-right">Total Nilai Barang</th>
                                        <th>Penerima</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($goodsReceipts as $gr): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-success"><?= esc($gr->receipt_no) ?></td>
                                            <td class="font-weight-bold text-teal"><?= esc($gr->po_no) ?></td>
                                            <td><strong><?= esc($gr->delivery_order_no ?: '-') ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($gr->received_date)) ?></td>
                                            <td><?= esc($gr->supplier_name) ?></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($gr->total_amount, 2, ',', '.') ?></td>
                                            <td><?= esc($gr->receiver_name ?: 'Petugas Gudang') ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('procurement/cetak-grn/' . $gr->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold" title="Cetak Bukti GRN">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 4: MASTER SUPPLIER & PBF VENDOR -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-suppliers" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Master Pedagang Besar Farmasi (PBF) & Vendor Pengadaan</h6>
                                <small class="text-muted">Direktori supplier obat, alat kesehatan, bahan baku resto, dan perlengkapan klinik</small>
                            </div>
                            <button class="btn btn-dark btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddSupplier">
                                <i class="fas fa-plus mr-1"></i> Tambah Supplier Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 100px;" class="text-center">Kode</th>
                                        <th>Nama Perusahaan / PBF</th>
                                        <th>Alamat Operasional</th>
                                        <th>Telepon / WA</th>
                                        <th>Nama PIC</th>
                                        <th>Info Rekening Bank</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suppliers as $s): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($s->code) ?></td>
                                            <td><strong><?= esc($s->name) ?></strong></td>
                                            <td><?= esc($s->address) ?></td>
                                            <td><?= esc($s->phone) ?></td>
                                            <td><?= esc($s->pic_name ?: '-') ?></td>
                                            <td>
                                                <?php if (!empty($s->bank_name)): ?>
                                                    <small class="badge badge-light border font-weight-bold">
                                                        <?= esc($s->bank_name) ?>: <?= esc($s->bank_account) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= ($s->status ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= strtoupper(esc($s->status ?? 'active')) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-outline-info btn-xs btn-edit-supplier" 
                                                        data-id="<?= $s->id ?>" 
                                                        data-code="<?= esc($s->code) ?>" 
                                                        data-name="<?= esc($s->name) ?>" 
                                                        data-address="<?= esc($s->address) ?>" 
                                                        data-phone="<?= esc($s->phone) ?>" 
                                                        data-pic="<?= esc($s->pic_name ?? '') ?>" 
                                                        data-email="<?= esc($s->email ?? '') ?>" 
                                                        data-bank="<?= esc($s->bank_name ?? '') ?>" 
                                                        data-acc="<?= esc($s->bank_account ?? '') ?>"
                                                        data-status="<?= esc($s->status ?? 'active') ?>"
                                                        title="Edit Supplier">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="<?= base_url('procurement/supplier') ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus / menonaktifkan supplier ini?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $s->id ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
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
<!-- MODAL: AJUKAN PURCHASE REQUISITION (PR) MULTI-ITEM -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalCreatePr" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-file-circle-plus mr-1 text-teal"></i> Ajukan Purchase Requisition (PR) Kebutuhan Barang
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('procurement/po') ?>" method="post" id="form-create-pr">
                <input type="hidden" name="action" value="create_pr">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Supplier Tujuan (PBF / Vendor): <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="pr-supplier-select" class="form-control form-control-sm font-weight-bold" required>
                                <option value="">-- Pilih Rekanan Supplier / PBF --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s->id ?>" 
                                            data-name="<?= esc($s->name) ?>"
                                            data-phone="<?= esc($s->phone) ?>"
                                            data-pic="<?= esc($s->pic_name ?? '-') ?>"
                                            data-bank="<?= esc($s->bank_name ?? '-') ?>"
                                            data-acc="<?= esc($s->bank_account ?? '-') ?>">
                                        <?= esc($s->name) ?> (<?= esc($s->code) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="pr-supplier-card" class="mt-1 p-2 bg-light rounded border text-xs" style="display:none; border: 1px dashed #0d9488 !important;">
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-user-tie text-teal mr-1"></i> PIC: <strong id="pr-sup-pic">-</strong></span>
                                    <span><i class="fab fa-whatsapp text-success mr-1"></i> <strong id="pr-sup-phone">-</strong></span>
                                </div>
                                <div class="mt-1 text-muted">
                                    <i class="fas fa-credit-card mr-1"></i> Bank: <span id="pr-sup-bank">-</span> (<span id="pr-sup-acc">-</span>)
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Unit / Departemen Pemohon: <span class="text-danger">*</span></label>
                            <select name="department" class="form-control form-control-sm font-weight-bold" required>
                                <option value="Apotek Farmasi">Apotek & Farmasi Resep</option>
                                <option value="Poliklinik & Medis">Poliklinik & Pelayanan Medis</option>
                                <option value="Resto Gizi & Sehat">Resto Gizi & Skincare</option>
                                <option value="Umum & Sarana">Umum & Sarana Operasional</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold">Keperluan / Keterangan Pengajuan:</label>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Contoh: Pengadaan rutin restock obat farmasi minggu ke-4">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="text-xs font-weight-bold text-teal mb-0"><i class="fas fa-list mr-1"></i> Rincian Item Barang yang Diajukan:</label>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold" id="btn-add-pr-row">
                            <i class="fas fa-plus mr-1"></i> Tambah Baris Item
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm mb-0" id="table-pr-items" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pilih Item Obat / Nama Barang <span class="text-danger">*</span></th>
                                    <th style="width: 90px;" class="text-center">Qty <span class="text-danger">*</span></th>
                                    <th style="width: 90px;">Satuan</th>
                                    <th style="width: 140px;" class="text-right">Est. Harga Satuan (Rp)</th>
                                    <th style="width: 150px;" class="text-right">Subtotal (Rp)</th>
                                    <th style="width: 40px;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" name="item_name[]" class="form-control form-control-sm item-name-input" list="medicine-options" placeholder="Ketik / pilih nama obat..." required>
                                        <input type="hidden" name="item_type[]" value="obat">
                                        <input type="hidden" name="item_id[]" value="">
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control form-control-sm text-center pr-qty" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="text" name="unit[]" class="form-control form-control-sm pr-unit" value="Pcs">
                                    </td>
                                    <td>
                                        <input type="number" name="price[]" class="form-control form-control-sm text-right pr-price" value="0" min="0" required>
                                    </td>
                                    <td class="text-right font-weight-bold pr-subtotal text-dark pt-2">
                                        Rp 0
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <datalist id="medicine-options">
                        <?php if (!empty($medicines)): ?>
                            <?php foreach ($medicines as $m): ?>
                                <option value="<?= esc($m->name) ?>" data-id="<?= $m->id ?>" data-price="<?= $m->price ?>" data-unit="<?= esc($m->unit ?? 'Pcs') ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>

                    <!-- Summary Box -->
                    <div class="card bg-light p-3 border mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-secondary text-sm font-weight-bold">ESTIMASI TOTAL ANGGARAN PR:</span>
                                <small class="text-muted d-block">Pengajuan akan diverifikasi oleh Supervisor Keuangan & Direksi</small>
                            </div>
                            <div>
                                <h4 class="font-weight-bold text-teal mb-0" id="lbl-pr-total">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan PR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: PENERIMAAN BARANG PO (GOODS RECEIPT / GRN) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalReceivePo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-dolly mr-1 text-success"></i> Goods Receipt Note (Penerimaan Fisik Barang PO)
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('procurement/po') ?>" method="post" id="form-receive-goods">
                <input type="hidden" name="action" value="receive_goods">
                <input type="hidden" name="po_id" id="rcv-po-id">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">No. PO Terpilih:</label>
                            <input type="text" id="rcv-po-no" class="form-control form-control-sm font-weight-bold text-teal" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Supplier / PBF:</label>
                            <input type="text" id="rcv-supplier-name" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">No. Surat Jalan / Faktur Vendor: <span class="text-danger">*</span></label>
                            <input type="text" name="delivery_order_no" class="form-control form-control-sm font-weight-bold" placeholder="SJ-998812" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Penerimaan Fisik: <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Lokasi Gudang Penyimpanan: <span class="text-danger">*</span></label>
                            <input type="text" name="warehouse_location" class="form-control form-control-sm font-weight-bold" value="Gudang Utama Farmasi" required>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Petugas Penerima: <span class="text-danger">*</span></label>
                            <input type="text" name="received_by" class="form-control form-control-sm font-weight-bold" value="<?= esc(session()->get('user_name') ?? 'Petugas Farmasi') ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold">Catatan Kondisi Fisik Barang:</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Kemasan tersegel rapi, suhu boks pendingin 4°C, barang lengkap">
                    </div>

                    <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 12px;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Penerimaan barang ini akan secara <strong>otomatis menambah stok fisik obat</strong> di sistem apotek, mencatat riwayat mutasi stok masuk, dan menjurnal <strong>Persediaan Obat (Debit)</strong> pada <strong>Hutang Usaha PBF (Kredit)</strong>.
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold"><i class="fas fa-check-double mr-1"></i> Konfirmasi Terima Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MASTER SUPPLIER (TAMBAH / EDIT) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddSupplier" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleSupplierModal">
                    <i class="fas fa-truck-field mr-1 text-teal"></i> Tambah Supplier / PBF Baru
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('procurement/supplier') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" id="sup-action" value="add">
                <input type="hidden" name="id" id="sup-id" value="">
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nama Perusahaan / PBF: <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="sup-name" class="form-control form-control-sm font-weight-bold" placeholder="PT. Kimia Farma Trading & Distribution" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kode Supplier:</label>
                            <input type="text" name="code" id="sup-code" class="form-control form-control-sm" placeholder="Auto / SUP-001">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">No. Telepon / WA: <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="sup-phone" class="form-control form-control-sm" placeholder="0812-3456-7890" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Contact Person (PIC):</label>
                            <input type="text" name="pic_name" id="sup-pic" class="form-control form-control-sm" placeholder="Bpk. Rahmat">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Email Perusahaan:</label>
                            <input type="email" name="email" id="sup-email" class="form-control form-control-sm" placeholder="sales@supplier.co.id">
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Alamat Kantor / Gudang:</label>
                        <textarea name="address" id="sup-address" class="form-control form-control-sm" rows="2" placeholder="Jl. Industri Farmasi No. 10"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Bank Pembayaran:</label>
                            <input type="text" name="bank_name" id="sup-bank" class="form-control form-control-sm" placeholder="Bank BCA / Mandiri">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nomor Rekening:</label>
                            <input type="text" name="bank_account" id="sup-acc" class="form-control form-control-sm" placeholder="882-0192-334">
                        </div>
                    </div>
                    <div class="form-group mb-0" id="group-sup-status" style="display:none;">
                        <label class="text-xs font-weight-bold">Status Supplier:</label>
                        <select name="status" id="sup-status" class="form-control form-control-sm">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Data Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: VIEW RINCIAN PURCHASE REQUISITION (PR) - LEMBAR DOKUMEN RESMI -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalViewPr" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-file-lines mr-1 text-teal"></i> Pratinjau Dokumen Pengajuan (PR)
                </h6>
                <div>
                    <a href="#" id="btn-print-vpr" target="_blank" class="btn btn-teal btn-xs font-weight-bold mr-2 text-white">
                        <i class="fas fa-print mr-1"></i> Cetak / Download PDF
                    </a>
                    <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Paper Document Sheet Inside Modal -->
                <div class="bg-white p-4 rounded shadow-sm border" style="border-color: #cbd5e1 !important; font-family: 'Inter', sans-serif;">
                    <!-- Letterhead -->
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom" style="border-color: #0d9488 !important; border-bottom-width: 2px !important;">
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded mr-3 text-teal border" style="font-size: 24px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-teal mb-0">SAWAMAWA MEDICAL CENTER</h5>
                                <small class="text-secondary font-weight-bold d-block">Logistik Farmasi, Pelayanan Medis & Pengadaan</small>
                                <small class="text-muted" style="font-size: 11px;">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-dark px-2 py-1 text-uppercase" style="letter-spacing: 0.5px;">PURCHASE REQUISITION</span>
                            <div class="font-weight-bold text-teal mt-1" style="font-size: 15px; font-family: monospace;" id="vpr-no"></div>
                            <small class="text-muted d-block" id="vpr-date"></small>
                        </div>
                    </div>

                    <!-- Metadata Box -->
                    <div class="row bg-light p-3 rounded mb-3 border" style="font-size: 12px;">
                        <div class="col-md-6">
                            <div class="mb-1"><strong>Unit Pemohon:</strong> <span id="vpr-dept" class="font-weight-bold"></span></div>
                            <div class="mb-1"><strong>Supplier Tujuan:</strong> <span id="vpr-supplier" class="text-teal font-weight-bold"></span></div>
                            <div><strong>Keperluan:</strong> <span id="vpr-desc" class="text-muted"></span></div>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="mb-1"><strong>Status PR:</strong> <span id="vpr-status" class="badge"></span></div>
                            <div><strong>Sistem Verifikasi:</strong> <span class="badge badge-light border">Multi-Tier Approvals</span></div>
                        </div>
                    </div>

                    <!-- Table Items -->
                    <h6 class="font-weight-bold text-dark mb-2" style="font-size: 12.5px;"><i class="fas fa-boxes-stacked text-teal mr-1"></i> Rincian Kebutuhan Barang / Obat:</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm mb-0" id="table-vpr-items" style="font-size: 12.5px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 35px;" class="text-center">NO</th>
                                    <th>NAMA ITEM / OBAT</th>
                                    <th style="width: 70px;" class="text-center">QTY</th>
                                    <th style="width: 70px;">SATUAN</th>
                                    <th style="width: 140px;" class="text-right">EST. HARGA SATUAN</th>
                                    <th style="width: 150px;" class="text-right">EST. SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="5" class="text-right text-dark">ESTIMASI TOTAL ANGGARAN:</td>
                                    <td class="text-right text-teal h6 mb-0 font-weight-bold" id="vpr-total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Verification Footer Seal -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted" style="font-size: 11px;">
                        <div>
                            <i class="fas fa-shield-alt text-teal mr-1"></i> Terverifikasi digital melalui SIM-Klinik Sawamawa Medical Center
                        </div>
                        <div>
                            Dokumen Resmi Pengadaan
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Klik Cetak / Download PDF untuk membuka dokumen siap print A4.</small>
                <div>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <a href="#" id="btn-print-vpr-bottom" target="_blank" class="btn btn-teal btn-sm font-weight-bold text-white">
                        <i class="fas fa-print mr-1"></i> Cetak / Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Calculate PR item subtotals & grand total
        function updatePrTotals() {
            let total = 0;
            $('#table-pr-items tbody tr').each(function() {
                const qty = parseFloat($(this).find('.pr-qty').val()) || 0;
                const price = parseFloat($(this).find('.pr-price').val()) || 0;
                const sub = qty * price;
                $(this).find('.pr-subtotal').text('Rp ' + sub.toLocaleString('id-ID'));
                total += sub;
            });
            $('#lbl-pr-total').text('Rp ' + total.toLocaleString('id-ID'));
        }

        $(document).on('input change', '.pr-qty, .pr-price', function() {
            updatePrTotals();
        });

        // Auto-Fill Supplier Information Card
        $('#pr-supplier-select').change(function() {
            const opt = $(this).find(':selected');
            const supId = opt.val();
            if (supId) {
                $('#pr-sup-pic').text(opt.data('pic') || '-');
                $('#pr-sup-phone').text(opt.data('phone') || '-');
                $('#pr-sup-bank').text(opt.data('bank') || '-');
                $('#pr-sup-acc').text(opt.data('acc') || '-');
                $('#pr-supplier-card').slideDown(150);
            } else {
                $('#pr-supplier-card').slideUp(150);
            }
        });

        // Autocomplete price & unit on medicine selection
        $(document).on('input', '.item-name-input', function() {
            const val = $(this).val();
            const option = $('#medicine-options option').filter(function() {
                return $(this).val() === val;
            });
            if (option.length) {
                const price = option.data('price') || 0;
                const unit = option.data('unit') || 'Pcs';
                const id = option.data('id') || '';
                const tr = $(this).closest('tr');
                tr.find('.pr-price').val(price);
                tr.find('.pr-unit').val(unit);
                tr.find('input[name="item_id[]"]').val(id);
                updatePrTotals();
            }
        });

        // Add dynamic row in PR modal
        $('#btn-add-pr-row').click(function() {
            const newRow = `
                <tr>
                    <td>
                        <input type="text" name="item_name[]" class="form-control form-control-sm item-name-input" list="medicine-options" placeholder="Ketik / pilih nama obat..." required>
                        <input type="hidden" name="item_type[]" value="obat">
                        <input type="hidden" name="item_id[]" value="">
                    </td>
                    <td>
                        <input type="number" name="qty[]" class="form-control form-control-sm text-center pr-qty" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="text" name="unit[]" class="form-control form-control-sm pr-unit" value="Pcs">
                    </td>
                    <td>
                        <input type="number" name="price[]" class="form-control form-control-sm text-right pr-price" value="0" min="0" required>
                    </td>
                    <td class="text-right font-weight-bold pr-subtotal text-dark pt-2">
                        Rp 0
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#table-pr-items tbody').append(newRow);
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#table-pr-items tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updatePrTotals();
            }
        });

        // View PR Details Modal (Paper Document Sheet)
        $('.btn-view-pr').click(function() {
            const id = $(this).data('id');
            $.getJSON('<?= base_url('procurement/pr-details-json/') ?>' + id, function(res) {
                if (res.status === 'success') {
                    $('#vpr-no').text(res.pr.request_no);
                    $('#vpr-supplier').text(res.pr.supplier_name);
                    $('#vpr-dept').text(res.pr.department || 'Apotek Farmasi');
                    $('#vpr-desc').text(res.pr.description || 'Pengadaan kebutuhan unit');
                    $('#vpr-status').text(res.pr.status.toUpperCase()).attr('class', 'badge badge-' + (res.pr.status === 'approved' || res.pr.status === 'completed' ? 'success' : (res.pr.status === 'rejected' ? 'danger' : 'warning')));
                    $('#vpr-date').text('Tanggal: ' + res.pr.created_at);
                    $('#vpr-total').text('Rp ' + parseFloat(res.pr.total_amount).toLocaleString('id-ID'));

                    const printUrl = '<?= base_url('procurement/cetak-pr/') ?>' + res.pr.id;
                    $('#btn-print-vpr, #btn-print-vpr-bottom').attr('href', printUrl);

                    let html = '';
                    if (res.items && res.items.length > 0) {
                        let no = 1;
                        res.items.forEach(function(it) {
                            html += `
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">${no++}</td>
                                    <td><strong>${it.item_name}</strong></td>
                                    <td class="text-center font-weight-bold">${it.qty}</td>
                                    <td>${it.unit}</td>
                                    <td class="text-right">Rp ${parseFloat(it.estimated_price).toLocaleString('id-ID')}</td>
                                    <td class="text-right font-weight-bold text-dark">Rp ${parseFloat(it.subtotal).toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });
                    } else {
                        html = `<tr><td colspan="6" class="text-center text-muted py-3">Pengadaan bersifat paket anggaran (Rp ${parseFloat(res.pr.total_amount).toLocaleString('id-ID')})</td></tr>`;
                    }
                    $('#table-vpr-items tbody').html(html);
                    $('#modalViewPr').modal('show');
                }
            });
        });

        // Receive PO Goods Modal handler
        $('.btn-receive-po').click(function() {
            const poId = $(this).data('id');
            const poNo = $(this).data('no');
            const supName = $(this).data('supplier');

            $('#rcv-po-id').val(poId);
            $('#rcv-po-no').val(poNo);
            $('#rcv-supplier-name').val(supName);

            // Fetch PO items to populate receive table
            $.getJSON('<?= base_url('procurement/po-details-json/') ?>' + poId, function(res) {
                let html = '';
                if (res.status === 'success' && res.items && res.items.length > 0) {
                    res.items.forEach(function(it) {
                        html += `
                            <tr>
                                <td>
                                    <strong>${it.item_name}</strong>
                                    <input type="hidden" name="medicine_id[]" value="${it.item_id || ''}">
                                    <input type="hidden" name="item_name[]" value="${it.item_name}">
                                    <input type="hidden" name="unit[]" value="${it.unit}">
                                </td>
                                <td>
                                    <input type="text" name="batch_no[]" class="form-control form-control-sm font-weight-bold" placeholder="BATCH-..." required>
                                </td>
                                <td>
                                    <input type="date" name="expired_date[]" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                                </td>
                                <td>
                                    <input type="number" name="qty[]" class="form-control form-control-sm text-center font-weight-bold" value="${it.qty}" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="buy_price[]" class="form-control form-control-sm text-right" value="${it.price}" min="0" required>
                                </td>
                                <td class="text-right font-weight-bold pt-2">
                                    Rp ${parseFloat(it.subtotal).toLocaleString('id-ID')}
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td>
                                <select name="single_medicine_id" class="form-control form-control-sm font-weight-bold" required>
                                    <?php foreach ($medicines as $m): ?>
                                        <option value="<?= $m->id ?>"><?= esc($m->name) ?> (<?= esc($m->code) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="single_batch_no" class="form-control form-control-sm font-weight-bold" placeholder="BATCH-..." required>
                            </td>
                            <td>
                                <input type="date" name="single_expired_date" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                            </td>
                            <td>
                                <input type="number" name="single_qty" class="form-control form-control-sm text-center font-weight-bold" value="1" min="1" required>
                            </td>
                            <td>
                                <input type="number" name="single_buy_price" class="form-control form-control-sm text-right" value="0" min="0" required>
                            </td>
                            <td class="text-right font-weight-bold pt-2">
                                Otomatis
                            </td>
                        </tr>
                    `;
                }
                $('#table-receive-items tbody').html(html);
                $('#modalReceivePo').modal('show');
            });
        });

        // Edit Supplier modal prefill
        $('.btn-edit-supplier').click(function() {
            $('#titleSupplierModal').html('<i class="fas fa-edit mr-1"></i> Edit Data Supplier / PBF');
            $('#sup-action').val('edit');
            $('#sup-id').val($(this).data('id'));
            $('#sup-code').val($(this).data('code'));
            $('#sup-name').val($(this).data('name'));
            $('#sup-address').val($(this).data('address'));
            $('#sup-phone').val($(this).data('phone'));
            $('#sup-pic').val($(this).data('pic'));
            $('#sup-email').val($(this).data('email'));
            $('#sup-bank').val($(this).data('bank'));
            $('#sup-acc').val($(this).data('acc'));
            $('#sup-status').val($(this).data('status'));
            $('#group-sup-status').show();
            $('#modalAddSupplier').modal('show');
        });

        // Reset Supplier modal on opening for Add
        $('[data-target="#modalAddSupplier"]').click(function() {
            if (!$('#sup-id').val()) {
                $('#titleSupplierModal').html('<i class="fas fa-truck-field mr-1"></i> Tambah Supplier / PBF Baru');
                $('#sup-action').val('add');
                $('#sup-id').val('');
                $('#group-sup-status').hide();
            }
        });
    });
</script>
<?= $this->endSection() ?>
