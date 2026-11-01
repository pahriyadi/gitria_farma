<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark (desain_tabel.md & dokumentasi_dark_mode.md) */
    :root {
        --dist-canvas-bg: #f4f6f9;
        --dist-card-bg: #ffffff;
        --dist-card-header: #ffffff;
        --dist-border: #b8b8b8;
        --dist-border-td: #c8c8c8;
        --dist-border-th: #b0b0b0;
        --dist-text: #212529;
        --dist-text-muted: #6c757d;
        --dist-th-bg: #f8fafc;
        --dist-hover-bg: #f0f4f8;
        --dist-box-bg: #f8fafc;
        --dist-input-bg: #ffffff;
    }

    body.dark-mode {
        --dist-canvas-bg: #14171a;
        --dist-card-bg: #1f2328;
        --dist-card-header: #1f2328;
        --dist-border: #374151;
        --dist-border-td: #374151;
        --dist-border-th: #374151;
        --dist-text: #f0f6fc;
        --dist-text-muted: #9ca3af;
        --dist-th-bg: #181b1f;
        --dist-hover-bg: #262c36;
        --dist-box-bg: #181b1f;
        --dist-input-bg: #181b1f;
    }

    /* Container Card Styling */
    .dist-card {
        background-color: var(--dist-card-bg) !important;
        border: 1px solid var(--dist-border) !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .dist-card .card-header {
        background-color: var(--dist-card-header) !important;
        border-bottom: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    .dist-card .card-body {
        background-color: var(--dist-card-bg) !important;
        color: var(--dist-text) !important;
    }

    /* Form Controls */
    .dist-input {
        background-color: var(--dist-input-bg) !important;
        border: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
        border-radius: 4px;
    }

    .dist-input:focus {
        border-color: #4f46e5 !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
    }

    body.dark-mode input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    /* Strict Table Styling (desain_tabel.md) */
    .table-dist {
        border-collapse: collapse !important;
        width: 100%;
        margin-bottom: 0;
        color: var(--dist-text) !important;
    }

    .table-dist th,
    .table-dist td {
        border: 1px solid var(--dist-border) !important;
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table-dist thead th {
        background-color: var(--dist-th-bg) !important;
        color: var(--dist-text) !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid var(--dist-border) !important;
    }

    .table-dist tbody tr {
        background-color: var(--dist-card-bg) !important;
        transition: background-color 0.15s ease;
    }

    .table-dist tbody tr:hover {
        background-color: var(--dist-hover-bg) !important;
    }

    /* Modal in Dark Mode */
    body.dark-mode .modal-content {
        background-color: var(--dist-card-bg) !important;
        border: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    body.dark-mode .modal-header {
        border-bottom: 1px solid var(--dist-border) !important;
    }

    body.dark-mode .modal-footer {
        background-color: var(--dist-box-bg) !important;
        border-top: 1px solid var(--dist-border) !important;
    }
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="font-weight-bold mb-1" style="color: var(--dist-text);">
            <i class="fas fa-arrow-right-arrow-left text-primary mr-2"></i> Transfer Stok Antar-Unit Bisnis
        </h4>
        <p class="small mb-0" style="color: var(--dist-text-muted);">
            Perpindahan stok barang antar unit (Distributor &harr; Apotek / Klinik / Resto) dengan penjurnalan otomatis
        </p>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" id="btnOpenTransferModal" onclick="openTransferModal()">
                <i class="fas fa-plus-circle mr-1"></i> Buat Transfer Stok Baru
            </button>
            <a href="<?= base_url('distributor/stok') ?>" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-boxes-stacked mr-1"></i> Stok Gudang
            </a>
        </div>
    </div>
</div>

<!-- TRANSFERS TABLE CARD -->
<div class="dist-card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="tableTransfers">
                <thead>
                    <tr>
                        <th>No. Dokumen Transfer</th>
                        <th>Tanggal</th>
                        <th>Unit Asal (Source)</th>
                        <th>Unit Tujuan (Target)</th>
                        <th>Keterangan / Catatan</th>
                        <th>Petugas</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transfers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5" style="color: var(--dist-text-muted);">
                                <i class="fas fa-arrow-right-arrow-left fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                Belum ada riwayat transfer stok antar unit.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transfers as $t): ?>
                            <tr>
                                <td class="font-weight-bold text-primary"><?= esc($t->transfer_no) ?></td>
                                <td><?= date('d/m/Y', strtotime($t->transfer_date)) ?></td>
                                <td>
                                    <span class="badge badge-secondary font-weight-bold text-uppercase"><?= esc($t->source_type) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-success font-weight-bold text-uppercase"><?= esc($t->target_type) ?></span>
                                </td>
                                <td><?= esc($t->notes ?: '-') ?></td>
                                <td><small style="color: var(--dist-text-muted);"><?= esc($t->creator_name ?: 'Petugas') ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-success">Selesai (Completed)</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL BUAT TRANSFER STOK -->
<div class="modal fade" id="modalTransferStock" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <form action="<?= base_url('distributor/simpan-transfer') ?>" method="post" id="formTransfer">
                
                <div class="modal-header bg-primary text-white py-2.5">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-arrow-right-arrow-left mr-1"></i> Form Transfer Stok Antar Unit</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">UNIT ASAL (DARI) <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm font-weight-bold dist-input" name="source_type" id="source_type" data-native="true" required>
                                <option value="distributor" selected>Gudang Distributor</option>
                                <option value="pharmacy">Farmasi &amp; Apotek</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">UNIT TUJUAN (KE) <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm font-weight-bold dist-input" name="target_type" id="target_type" data-native="true" required>
                                <option value="pharmacy" selected>Farmasi &amp; Apotek</option>
                                <option value="distributor">Gudang Distributor</option>
                                <option value="clinic">Pelayanan Medis Klinik</option>
                                <option value="resto">Resto Gizi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">TANGGAL TRANSFER</label>
                            <input type="date" class="form-control form-control-sm dist-input" name="transfer_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">CATATAN TRANSFER</label>
                            <input type="text" class="form-control form-control-sm dist-input" name="notes" placeholder="Tujuan mutasi / permintaan cabang...">
                        </div>
                    </div>

                    <hr class="my-2" style="border-color: var(--dist-border);">
                    <h6 class="font-weight-bold text-xs text-uppercase mb-2" style="color: var(--dist-text-muted);">PILIH ITEM OBAT YG DITRANSFER</h6>

                    <div class="row align-items-end mb-3">
                        <div class="col-md-6">
                            <label class="text-xs mb-1" style="color: var(--dist-text-muted);">Pilih Obat</label>
                            <select class="form-control form-control-sm dist-input" id="trf_med_select" data-native="true">
                                <option value="">-- Pilih Obat --</option>
                                <?php foreach ($distStocks as $ds): ?>
                                    <option value="<?= $ds->medicine_id ?>" 
                                        data-name="<?= esc($ds->med_name) ?>" 
                                        data-batch="<?= esc($ds->batch_no) ?>" 
                                        data-exp="<?= esc($ds->expired_date) ?>" 
                                        data-buy="<?= (float)$ds->buy_price ?>" 
                                        data-stock="<?= (int)$ds->stock ?>">
                                        <?= esc($ds->med_name) ?> (Stok: <?= $ds->stock ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="text-xs mb-1" style="color: var(--dist-text-muted);">Jumlah (Qty)</label>
                            <input type="number" class="form-control form-control-sm text-center dist-input" id="trf_qty_input" value="1" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-block font-weight-bold" id="btnAddTransferItem" onclick="addTransferItem()">
                                <i class="fas fa-plus mr-1"></i> Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-dist text-sm" id="trfItemsTable">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Batch</th>
                                    <th class="text-center">Qty Transfer</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="trfItemsTableBody">
                                <tr id="emptyTrfRow">
                                    <td colspan="4" class="text-center py-3" style="color: var(--dist-text-muted);">Belum ada item obat ditambahkan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold" id="btnSubmitTransfer">
                        <i class="fas fa-paper-plane mr-1"></i> Proses Transfer Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let transferItemsList = [];

function openTransferModal() {
    transferItemsList = [];
    renderTransferTable();
    $('#modalTransferStock').modal('show');
}

function addTransferItem() {
    const sel = document.getElementById('trf_med_select');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) {
        alert('Silakan pilih obat terlebih dahulu.');
        return;
    }

    const qty = parseInt(document.getElementById('trf_qty_input').value || 1);
    const maxStk = parseInt(opt.getAttribute('data-stock') || 0);

    if (qty <= 0) {
        alert('Jumlah transfer harus lebih besar dari 0.');
        return;
    }

    if (qty > maxStk) {
        alert(`Jumlah transfer melebihi stok yang tersedia (${maxStk}).`);
        return;
    }

    const medId = parseInt(opt.value);
    const existing = transferItemsList.find(x => x.medicine_id === medId);
    if (existing) {
        existing.qty += qty;
    } else {
        transferItemsList.push({
            medicine_id: medId,
            med_name: opt.getAttribute('data-name'),
            batch_no: opt.getAttribute('data-batch') || '',
            expired_date: opt.getAttribute('data-exp') || '',
            buy_price: parseFloat(opt.getAttribute('data-buy') || 0),
            qty: qty
        });
    }

    renderTransferTable();
}

function removeTransferItem(idx) {
    transferItemsList.splice(idx, 1);
    renderTransferTable();
}

function renderTransferTable() {
    const tbody = document.getElementById('trfItemsTableBody');
    if (transferItemsList.length === 0) {
        tbody.innerHTML = '<tr id="emptyTrfRow"><td colspan="4" class="text-center py-3" style="color: var(--dist-text-muted);">Belum ada item obat ditambahkan.</td></tr>';
        return;
    }

    let html = '';
    transferItemsList.forEach((it, idx) => {
        html += `
            <tr>
                <td>
                    <strong style="color: var(--dist-text);">${it.med_name}</strong>
                    <input type="hidden" name="items[${idx}][medicine_id]" value="${it.medicine_id}">
                    <input type="hidden" name="items[${idx}][batch_no]" value="${it.batch_no}">
                    <input type="hidden" name="items[${idx}][expired_date]" value="${it.expired_date}">
                    <input type="hidden" name="items[${idx}][buy_price]" value="${it.buy_price}">
                </td>
                <td><span class="badge badge-light border">${it.batch_no || '-'}</span></td>
                <td class="text-center font-weight-bold">
                    <input type="number" class="form-control form-control-sm text-center font-weight-bold dist-input d-inline-block" style="width: 80px;" name="items[${idx}][qty]" value="${it.qty}" min="1">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-transfer-item" onclick="removeTransferItem(${idx})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

document.getElementById('formTransfer').addEventListener('submit', function(e) {
    if (transferItemsList.length === 0) {
        e.preventDefault();
        alert('Silakan tambahkan minimal 1 item obat yang akan ditransfer.');
    }
});
</script>
<?= $this->endSection() ?>
