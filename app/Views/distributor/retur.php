<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Distributor Paper White / Paper Dark */
:root {
    --dist-bg: #ffffff;
    --dist-text: #1f2937;
    --dist-muted: #6b7280;
    --dist-border: #b8b8b8;
    --dist-card-bg: #ffffff;
    --dist-head-bg: #f8fafc;
    --dist-hover-bg: #f1f5f9;
    --dist-input-bg: #ffffff;
}

body.dark-mode {
    --dist-bg: #14171a;
    --dist-text: #f0f6fc;
    --dist-muted: #9ca3af;
    --dist-border: #374151;
    --dist-card-bg: #1f2328;
    --dist-head-bg: #181b1f;
    --dist-hover-bg: #272c33;
    --dist-input-bg: #181b1f;
}

.dist-card {
    background-color: var(--dist-card-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    border-radius: 8px;
}

.table-dist {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    background-color: var(--dist-card-bg) !important;
}

.table-dist th,
.table-dist td {
    border: 1px solid var(--dist-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-dist thead th {
    background-color: var(--dist-head-bg) !important;
    color: var(--dist-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-dist tbody tr:hover {
    background-color: var(--dist-hover-bg) !important;
}

.dist-input {
    background-color: var(--dist-input-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
}

.dist-input:focus {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25) !important;
}

body.dark-mode .modal-content {
    background-color: var(--dist-card-bg) !important;
    color: var(--dist-text) !important;
    border: 1px solid var(--dist-border) !important;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: var(--dist-border) !important;
    background-color: var(--dist-head-bg) !important;
}
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--dist-text);">
            <i class="fas fa-rotate-left text-danger mr-2"></i> Retur Penjualan Grosir
        </h4>
        <small style="color: var(--dist-muted);">Pengembalian barang rusak / salah kirim dari pelanggan, pemulihan stok gudang, dan penyesuaian piutang / pengembalian dana</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" id="btnOpenAddReturn" onclick="openAddReturnModal()">
                <i class="fas fa-plus-circle mr-1"></i> Buat Retur Baru
            </button>
            <a href="<?= base_url('distributor/riwayat') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-receipt mr-1"></i> Riwayat Penjualan
            </a>
            <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm text-dark">
                <i class="fas fa-clock mr-1"></i> Monitoring Piutang
            </a>
        </div>
    </div>
</div>

<!-- RETURNS TABLE CARD -->
<div class="card dist-card shadow-sm border-0 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="tableReturns">
                <thead>
                    <tr>
                        <th style="width: 170px;">No. Dokumen Retur</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 170px;">No. Faktur Penjualan</th>
                        <th>Pelanggan</th>
                        <th class="text-right" style="width: 150px;">Total Nilai Retur</th>
                        <th style="width: 180px;">Metode Pengembalian</th>
                        <th>Alasan Retur</th>
                        <th style="width: 130px;">Petugas</th>
                        <th class="text-center" style="width: 130px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($returns)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5" style="color: var(--dist-muted);">
                                <i class="fas fa-rotate-left fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                Belum ada riwayat retur penjualan grosir.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($returns as $r): ?>
                            <tr>
                                <td class="font-weight-bold text-danger"><?= esc($r->return_no) ?></td>
                                <td><?= date('d/m/Y', strtotime($r->return_date)) ?></td>
                                <td class="font-weight-bold text-indigo"><?= esc($r->invoice_no) ?></td>
                                <td><strong><?= esc($r->customer_name) ?></strong></td>
                                <td class="text-right font-weight-bold text-danger">
                                    Rp <?= number_format($r->total_return_amount, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?php if ($r->refund_method === 'deduct_receivable'): ?>
                                        <span class="badge badge-info font-weight-bold">Potong Piutang</span>
                                    <?php elseif ($r->refund_method === 'cash_refund'): ?>
                                        <span class="badge badge-warning text-dark font-weight-bold">Pengembalian Kas Tunai</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary font-weight-bold">Pengembalian Transfer Bank</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($r->reason ?: '-') ?></td>
                                <td><small style="color: var(--dist-muted);"><?= esc($r->creator_name ?: 'Petugas') ?></small></td>
                                <td class="text-center">
                                    <span class="badge badge-success">Selesai &amp; Dijurnal</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL BUAT RETUR PENJUALAN -->
<div class="modal fade" id="modalAddReturn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="formCreateReturn">
                
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-rotate-left mr-1"></i> Form Retur Penjualan Grosir</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">PILIH FAKTUR PENJUALAN YG DIRETUR <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm dist-input font-weight-bold" id="ret_sale_id" data-native="true" required onchange="loadSaleItemsForReturn()">
                                <option value="">-- Pilih Faktur Penjualan --</option>
                                <?php if (!empty($recentSales)): ?>
                                    <?php foreach ($recentSales as $rs): ?>
                                        <option value="<?= $rs->id ?>">
                                            <?= esc($rs->invoice_no) ?> - <?= esc($rs->customer_name) ?> (<?= date('d/m/Y', strtotime($rs->sale_date)) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">METODE KOMPENSASI RETUR <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm dist-input font-weight-bold" id="ret_refund_method" data-native="true" required>
                                <option value="deduct_receivable" selected>Potong Saldo Piutang Customer</option>
                                <option value="cash_refund">Pengembalian Kas Tunai (Akun 1-104)</option>
                                <option value="bank_refund">Pengembalian Transfer Bank (Akun 1-114)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">TANGGAL RETUR</label>
                            <input type="date" class="form-control form-control-sm dist-input" id="ret_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">ALASAN RETUR</label>
                            <input type="text" class="form-control form-control-sm dist-input" id="ret_reason" placeholder="Barang rusak / salah kirim / kemasan bocor..." required>
                        </div>
                    </div>

                    <hr class="my-2" style="border-color: var(--dist-border);">
                    <h6 class="font-weight-bold text-xs text-uppercase mb-2" style="color: var(--dist-text);">PILIH ITEM DAN JUMLAH YANG DIRETUR</h6>

                    <div id="returnItemsContainer">
                        <div class="text-center py-4" style="color: var(--dist-muted);">Pilih faktur penjualan terlebih dahulu untuk menampilkan daftar barang.</div>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger font-weight-bold" id="btnSubmitReturn">
                        <i class="fas fa-check mr-1"></i> Proses Retur &amp; Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let returnSaleItems = [];

function openAddReturnModal() {
    document.getElementById('ret_sale_id').value = '';
    document.getElementById('returnItemsContainer').innerHTML = '<div class="text-center py-4" style="color: var(--dist-muted);">Pilih faktur penjualan terlebih dahulu untuk menampilkan daftar barang.</div>';
    $('#modalAddReturn').modal('show');
}

function loadSaleItemsForReturn() {
    const saleId = document.getElementById('ret_sale_id').value;
    const cont = document.getElementById('returnItemsContainer');

    if (!saleId) {
        cont.innerHTML = '<div class="text-center py-4" style="color: var(--dist-muted);">Pilih faktur penjualan terlebih dahulu.</div>';
        return;
    }

    cont.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-danger"></i> Memuat item faktur...</div>';

    fetch('<?= base_url('distributor/detail-penjualan/') ?>/' + saleId)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            returnSaleItems = res.items;

            let html = `
                <div class="table-responsive">
                    <table class="table-dist text-sm mb-0">
                        <thead>
                            <tr>
                                <th>Nama Obat / Barang</th>
                                <th>Batch</th>
                                <th class="text-center">Qty Terjual</th>
                                <th class="text-right">Harga Jual</th>
                                <th class="text-center" style="width: 120px;">Qty Retur</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            returnSaleItems.forEach((it, idx) => {
                html += `
                    <tr>
                        <td>
                            <strong>${it.med_name}</strong>
                            <input type="hidden" id="ret_item_sale_detail_id_${idx}" value="${it.id}">
                        </td>
                        <td><span class="badge badge-light border">${it.batch_no || '-'}</span></td>
                        <td class="text-center font-weight-bold">${it.qty}</td>
                        <td class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(it.selling_price)}</td>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm text-center font-weight-bold dist-input" id="ret_item_qty_${idx}" value="0" min="0" max="${it.qty}">
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            cont.innerHTML = html;
        } else {
            cont.innerHTML = '<div class="alert alert-danger">Gagal memuat item faktur.</div>';
        }
    })
    .catch(err => {
        cont.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan: ' + err + '</div>';
    });
}

document.getElementById('formCreateReturn').addEventListener('submit', function(e) {
    e.preventDefault();

    const saleId = document.getElementById('ret_sale_id').value;
    if (!saleId) {
        alert('Silakan pilih Faktur Penjualan.');
        return;
    }

    const itemsToReturn = [];
    returnSaleItems.forEach((it, idx) => {
        const qty = parseInt(document.getElementById(`ret_item_qty_${idx}`).value || 0);
        if (qty > 0) {
            itemsToReturn.push({
                sale_detail_id: it.id,
                qty: qty
            });
        }
    });

    if (itemsToReturn.length === 0) {
        alert('Silakan masukkan jumlah (Qty) retur minimal pada salah satu item.');
        return;
    }

    const payload = {
        sale_id: saleId,
        return_date: document.getElementById('ret_date').value,
        refund_method: document.getElementById('ret_refund_method').value,
        reason: document.getElementById('ret_reason').value,
        items: itemsToReturn
    };

    const btn = document.getElementById('btnSubmitReturn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';

    fetch('<?= base_url('distributor/simpan-retur') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            alert(res.message);
            window.location.reload();
        } else {
            alert('Error: ' + res.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Proses Retur &amp; Jurnal';
        }
    })
    .catch(err => {
        alert('Gagal memproses retur: ' + err);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Proses Retur &amp; Jurnal';
    });
});
</script>
<?= $this->endSection() ?>
