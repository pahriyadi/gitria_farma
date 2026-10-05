<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-rotate-left text-danger mr-2"></i> Retur Penjualan Grosir
                </h4>
                <small class="text-muted">Pengembalian barang rusak / salah kirim dari pelanggan, pemulihan stok gudang, dan penyesuaian piutang / pengembalian dana</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" onclick="openAddReturnModal()">
                        <i class="fas fa-plus-circle mr-1"></i> Buat Retur Baru
                    </button>
                    <a href="<?= base_url('distributor/riwayat') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-receipt mr-1"></i> Riwayat Penjualan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- RETURNS TABLE CARD -->
        <div class="card card-outline card-danger shadow-none border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>No. Dokumen Retur</th>
                                <th>Tanggal</th>
                                <th>No. Faktur Penjualan</th>
                                <th>Pelanggan</th>
                                <th class="text-right">Total Nilai Retur</th>
                                <th>Metode Pengembalian</th>
                                <th>Alasan Retur</th>
                                <th>Petugas</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returns)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-rotate-left fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                        Belum ada riwayat retur penjualan.
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
                                        <td><small class="text-muted"><?= esc($r->creator_name ?: 'Petugas') ?></small></td>
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

    </div>
</div>

<!-- MODAL BUAT RETUR PENJUALAN -->
<div class="modal fade" id="modalAddReturn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
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
                            <select class="form-control form-control-sm select2" id="ret_sale_id" required onchange="loadSaleItemsForReturn()">
                                <option value="">-- Pilih Faktur Penjualan --</option>
                                <?php foreach ($recentSales as $rs): ?>
                                    <option value="<?= $rs->id ?>">
                                        <?= esc($rs->invoice_no) ?> - <?= esc($rs->customer_name) ?> (<?= date('d/m/Y', strtotime($rs->sale_date)) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">METODE KOMPENSASI RETUR <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm font-weight-bold" id="ret_refund_method" required>
                                <option value="deduct_receivable" selected>Potong Saldo Piutang Customer</option>
                                <option value="cash_refund">Pengembalian Kas Tunai (Akun 1-104)</option>
                                <option value="bank_refund">Pengembalian Transfer Bank (Akun 1-114)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">TANGGAL RETUR</label>
                            <input type="date" class="form-control form-control-sm" id="ret_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">ALASAN RETUR</label>
                            <input type="text" class="form-control form-control-sm" id="ret_reason" placeholder="Barang rusak / salah kirim / kemasan bocor..." required>
                        </div>
                    </div>

                    <hr class="my-2">
                    <h6 class="font-weight-bold text-xs text-muted text-uppercase mb-2">PILIH ITEM DAN JUMLAH YANG DIRETUR</h6>

                    <div id="returnItemsContainer">
                        <div class="text-center py-4 text-muted">Pilih faktur penjualan terlebih dahulu untuk menampilkan daftar barang.</div>
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light">
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
    document.getElementById('returnItemsContainer').innerHTML = '<div class="text-center py-4 text-muted">Pilih faktur penjualan terlebih dahulu untuk menampilkan daftar barang.</div>';
    $('#modalAddReturn').modal('show');
}

function loadSaleItemsForReturn() {
    const saleId = document.getElementById('ret_sale_id').value;
    const cont = document.getElementById('returnItemsContainer');

    if (!saleId) {
        cont.innerHTML = '<div class="text-center py-4 text-muted">Pilih faktur penjualan terlebih dahulu.</div>';
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
                    <table class="table table-bordered table-sm text-sm mb-0">
                        <thead class="bg-light">
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
                            <input type="number" class="form-control form-control-sm text-center font-weight-bold" id="ret_item_qty_${idx}" value="0" min="0" max="${it.qty}">
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
