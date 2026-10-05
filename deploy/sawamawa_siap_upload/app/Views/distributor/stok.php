<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-boxes-stacked text-teal mr-2"></i> Stok Gudang Distributor &amp; Kartu Stok
                </h4>
                <small class="text-muted">Stok mandiri terisolasi khusus Unit Bisnis Distributor (terpisah dari Stok Apotek &amp; Resto)</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" onclick="openAddStockModal()">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah / Inbound Stok
                    </button>
                    <a href="<?= base_url('distributor/transfer') ?>" class="btn btn-outline-primary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-arrow-right-arrow-left mr-1"></i> Transfer Antar-Unit
                    </a>
                    <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-outline-indigo btn-sm font-weight-bold shadow-sm" style="color: #4f46e5; border-color: #4f46e5;">
                        <i class="fas fa-cart-plus mr-1"></i> Penjualan Grosir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <!-- STOCKS TABLE CARD -->
        <div class="card card-outline card-teal shadow-none border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Kode &amp; Nama Obat</th>
                                <th>No. Batch</th>
                                <th>Kedaluwarsa (Exp)</th>
                                <th class="text-right">Harga Beli (HPP)</th>
                                <th class="text-right">Harga Jual Grosir</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-right">Total Nilai Stok</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stocks)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-boxes-stacked fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                        Stok gudang distributor masih kosong. Silakan tambah stok obat.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stocks as $s): 
                                    $isLow = $s->stock <= $s->min_stock;
                                    $totalVal = $s->stock * (float)$s->buy_price;
                                ?>
                                    <tr>
                                        <td>
                                            <strong class="text-dark d-block"><?= esc($s->med_name) ?></strong>
                                            <small class="text-muted"><?= esc($s->med_code) ?> | Satuan: <?= esc($s->default_unit ?: 'PCS') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border"><?= esc($s->batch_no ?: '-') ?></span>
                                        </td>
                                        <td>
                                            <?= $s->expired_date ? date('d/m/Y', strtotime($s->expired_date)) : '-' ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-muted">
                                            Rp <?= number_format($s->buy_price, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-indigo">
                                            Rp <?= number_format($s->selling_price, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center font-weight-bold <?= $isLow ? 'text-danger' : 'text-success' ?>" style="font-size: 15px;">
                                            <?= $s->stock ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            Rp <?= number_format($totalVal, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($isLow): ?>
                                                <span class="badge badge-danger font-weight-bold"><i class="fas fa-triangle-exclamation mr-1"></i>Stok Menipis</span>
                                            <?php else: ?>
                                                <span class="badge badge-success font-weight-bold">Aman</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-outline-info" onclick="openKartuStok(<?= $s->id ?>)" title="Buku Kartu Stok Mutasi">
                                                <i class="fas fa-book mr-1"></i> Kartu Stok
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick='editStock(<?= json_encode($s) ?>)' title="Update Stok / Harga">
                                                <i class="fas fa-edit"></i>
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

<!-- MODAL ADD / EDIT STOCK -->
<div class="modal fade" id="modalStockForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form action="<?= base_url('distributor/simpan-stok') ?>" method="post">
                <input type="hidden" name="id" id="stock_id" value="">
                
                <div class="modal-header bg-teal text-white py-2">
                    <h6 class="modal-title font-weight-bold" id="modalStockTitle"><i class="fas fa-boxes-stacked mr-1"></i> Tambah Stok Gudang Distributor</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">PILIH OBAT / BARANG <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm select2" name="medicine_id" id="stock_med_id" required>
                                <option value="">-- Pilih Obat --</option>
                                <?php foreach ($medicines as $m): ?>
                                    <option value="<?= $m->id ?>" data-price="<?= (float)$m->price ?>">
                                        <?= esc($m->code) ?> - <?= esc($m->name) ?> (<?= esc($m->unit ?: 'PCS') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">NO. BATCH DISTRIBUTOR</label>
                            <input type="text" class="form-control form-control-sm" name="batch_no" id="stock_batch" placeholder="DIST-YYYYMMDD-XXX">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">TANGGAL KEDALUWARSA (EXP)</label>
                            <input type="date" class="form-control form-control-sm" name="expired_date" id="stock_exp" value="<?= date('Y-m-d', strtotime('+1 year')) ?>">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">MINIMAL STOK ALERT</label>
                            <input type="number" class="form-control form-control-sm" name="min_stock" id="stock_min" value="10" min="1">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">HARGA BELI POKOK (HPP)</label>
                            <input type="number" class="form-control form-control-sm text-right" name="buy_price" id="stock_buy_price" value="0" min="0" step="100" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">HARGA JUAL GROSIR (B2B)</label>
                            <input type="number" class="form-control form-control-sm text-right text-indigo font-weight-bold" name="selling_price" id="stock_sell_price" value="0" min="0" step="100" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1" id="lbl_qty_title">JUMLAH STOK MASUK <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm text-center font-weight-bold" name="stock" id="stock_qty" value="10" min="1" required>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">CATATAN / SUMBER PENERIMAAN</label>
                            <input type="text" class="form-control form-control-sm" name="notes" id="stock_notes" placeholder="Penerimaan langsung pabrik / penyesuaian stok...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-teal font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Stok Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL KARTU STOK MUTASI -->
<div class="modal fade" id="modalKartuStok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-teal text-white py-2">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-book-medical mr-1"></i> Buku Kartu Stok Gudang Distributor</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="kartuStokModalBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-teal"></i> Memuat mutasi kartu stok...</div>
            </div>
        </div>
    </div>
</div>

<script>
function openAddStockModal() {
    document.getElementById('stock_id').value = '';
    document.getElementById('stock_med_id').value = '';
    document.getElementById('stock_batch').value = 'DIST-' + new Date().toISOString().slice(0,10).replace(/-/g,'');
    document.getElementById('stock_buy_price').value = '0';
    document.getElementById('stock_sell_price').value = '0';
    document.getElementById('stock_qty').value = '100';
    document.getElementById('stock_min').value = '10';
    document.getElementById('lbl_qty_title').innerText = 'JUMLAH STOK MASUK';

    document.getElementById('modalStockTitle').innerHTML = '<i class="fas fa-boxes-stacked mr-1"></i> Tambah / Inbound Stok Gudang Distributor';
    $('#modalStockForm').modal('show');
}

function editStock(s) {
    document.getElementById('stock_id').value = s.id;
    document.getElementById('stock_med_id').value = s.medicine_id;
    document.getElementById('stock_batch').value = s.batch_no || '';
    document.getElementById('stock_exp').value = s.expired_date || '';
    document.getElementById('stock_buy_price').value = s.buy_price;
    document.getElementById('stock_sell_price').value = s.selling_price;
    document.getElementById('stock_qty').value = '0';
    document.getElementById('stock_min').value = s.min_stock;
    document.getElementById('lbl_qty_title').innerText = 'TAMBAH QTY STOK (+)';

    document.getElementById('modalStockTitle').innerHTML = '<i class="fas fa-edit mr-1"></i> Update Stok &amp; Harga: ' + s.med_name;
    $('#modalStockForm').modal('show');
}

function openKartuStok(stockId) {
    $('#modalKartuStok').modal('show');
    const modalBody = document.getElementById('kartuStokModalBody');
    modalBody.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-teal"></i> Memuat mutasi kartu stok...</div>';

    fetch('<?= base_url('distributor/kartu-stok/') ?>/' + stockId)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const st = res.stock;
            const movs = res.movements;

            let movsHtml = '';
            if (movs.length === 0) {
                movsHtml = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat mutasi stok pada item ini.</td></tr>';
            } else {
                movs.forEach(m => {
                    let typeBadge = '<span class="badge badge-success">Masuk (In)</span>';
                    if (m.movement_type === 'out') typeBadge = '<span class="badge badge-danger">Keluar (Jual)</span>';
                    else if (m.movement_type === 'transfer_out') typeBadge = '<span class="badge badge-warning">Transfer Keluar</span>';
                    else if (m.movement_type === 'transfer_in') typeBadge = '<span class="badge badge-info">Transfer Masuk</span>';
                    else if (m.movement_type === 'return_in') typeBadge = '<span class="badge badge-primary">Retur Masuk</span>';

                    movsHtml += `
                        <tr>
                            <td>${m.created_at}</td>
                            <td>${typeBadge}</td>
                            <td class="font-weight-bold text-dark">${m.reference_no || '-'}</td>
                            <td class="text-center font-weight-bold ${m.movement_type.includes('in') ? 'text-success' : 'text-danger'}">
                                ${m.movement_type.includes('in') ? '+' : '-'}${m.qty}
                            </td>
                            <td class="text-center text-muted">${m.stock_before}</td>
                            <td class="text-center font-weight-bold text-teal">${m.stock_after}</td>
                            <td><small class="text-muted">${m.notes || '-'}</small></td>
                        </tr>
                    `;
                });
            }

            modalBody.innerHTML = `
                <div class="row mb-3 align-items-center">
                    <div class="col-8">
                        <h5 class="font-weight-bold text-teal mb-0">${st.med_name}</h5>
                        <small class="text-muted">Kode: ${st.med_code} | Batch: ${st.batch_no || '-'} | Exp: ${st.expired_date || '-'}</small>
                    </div>
                    <div class="col-4 text-right">
                        <span class="text-muted text-xs font-weight-bold d-block">SISA SALDO STOK:</span>
                        <h3 class="font-weight-bold text-indigo mb-0">${st.stock} Unit</h3>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Waktu Mutasi</th>
                                <th>Jenis Transaksi</th>
                                <th>No. Referensi / Dokumen</th>
                                <th class="text-center">Qty Mutasi</th>
                                <th class="text-center">Saldo Awal</th>
                                <th class="text-center">Saldo Akhir</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>${movsHtml}</tbody>
                    </table>
                </div>
            `;
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat kartu stok.</div>';
        }
    })
    .catch(err => {
        modalBody.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan: ' + err + '</div>';
    });
}
</script>
<?= $this->endSection() ?>
