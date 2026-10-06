<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-receipt text-indigo mr-2"></i> Riwayat Penjualan Distributor
                </h4>
                <small class="text-muted">Arsip faktur penjualan, status pembayaran, cetak faktur &amp; surat jalan</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-cart-plus mr-1"></i> Buat Faktur Baru
                    </a>
                    <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Tagihan Piutang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- FILTER CARD -->
        <div class="card card-outline card-secondary shadow-none border mb-3">
            <div class="card-body p-3">
                <form method="get" action="<?= base_url('distributor/riwayat') ?>">
                    <div class="row">
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">DARI TANGGAL</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">SAMPAI TANGGAL</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">PELANGGAN</label>
                            <select class="form-control form-control-sm" name="customer_id">
                                <option value="">-- Semua Pelanggan --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c->id ?>" <?= $customerId == $c->id ? 'selected' : '' ?>>
                                        <?= esc($c->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">METODE BAYAR</label>
                            <select class="form-control form-control-sm" name="payment_type">
                                <option value="">-- Semua --</option>
                                <option value="cash" <?= $paymentType === 'cash' ? 'selected' : '' ?>>Tunai</option>
                                <option value="transfer" <?= $paymentType === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                                <option value="credit" <?= $paymentType === 'credit' ? 'selected' : '' ?>>Kredit / Tempo</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">STATUS</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="">-- Semua Status --</option>
                                <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Lunas</option>
                                <option value="partial" <?= $status === 'partial' ? 'selected' : '' ?>>Sebagian</option>
                                <option value="unpaid" <?= $status === 'unpaid' ? 'selected' : '' ?>>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-indigo btn-sm btn-block font-weight-bold" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="card card-outline card-indigo shadow-none border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>No. Faktur</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Metode Bayar</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-right">Total Faktur</th>
                                <th class="text-right">Sisa Tagihan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                        Tidak ada data penjualan pada periode yang dipilih.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $s): ?>
                                    <tr>
                                        <td class="font-weight-bold text-indigo"><?= esc($s->invoice_no) ?></td>
                                        <td><?= date('d/m/Y', strtotime($s->sale_date)) ?></td>
                                        <td>
                                            <strong class="text-dark d-block"><?= esc($s->customer_name) ?></strong>
                                            <small class="text-muted"><i class="fas fa-phone-alt mr-1"></i><?= esc($s->customer_phone) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($s->payment_type === 'credit'): ?>
                                                <span class="badge badge-warning text-dark font-weight-bold">Tempo (<?= $s->payment_terms_days ?> Hari)</span>
                                            <?php elseif ($s->payment_type === 'transfer'): ?>
                                                <span class="badge badge-info font-weight-bold">Transfer Bank</span>
                                            <?php else: ?>
                                                <span class="badge badge-success font-weight-bold">Tunai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $s->due_date ? date('d/m/Y', strtotime($s->due_date)) : '-' ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            Rp <?= number_format($s->total_amount, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right font-weight-bold <?= $s->remaining_amount > 0 ? 'text-danger' : 'text-success' ?>">
                                            Rp <?= number_format($s->remaining_amount, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($s->payment_status === 'paid'): ?>
                                                <span class="badge badge-success">Lunas</span>
                                            <?php elseif ($s->payment_status === 'partial'): ?>
                                                <span class="badge badge-info">Sebagian</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Belum Lunas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-xs" role="group">
                                                <button type="button" class="btn btn-xs btn-outline-info" onclick="viewDetail(<?= $s->id ?>)" title="Rincian Item">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="<?= base_url('distributor/cetak-faktur/' . $s->id) ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="Cetak Faktur">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="<?= base_url('distributor/cetak-surat-jalan/' . $s->id) ?>" target="_blank" class="btn btn-xs btn-outline-secondary" title="Cetak Surat Jalan">
                                                    <i class="fas fa-truck"></i>
                                                </a>
                                                <?php if (empty($s->is_voided)): ?>
                                                    <button type="button" class="btn btn-xs btn-outline-danger" 
                                                            onclick="window.openVoidModal('distributor_sale', <?= $s->id ?>, '<?= esc($s->invoice_no) ?>', <?= (float)$s->total_amount ?>, '<?= esc($s->customer_name) ?>', function(){ location.reload(); })" 
                                                            title="Void Faktur Penjualan">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge badge-danger text-xs font-weight-bold"><i class="fas fa-ban"></i> VOID</span>
                                                <?php endif; ?>
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

    </div>
</div>

<!-- MODAL DETAIL PENJUALAN -->
<div class="modal fade" id="modalDetailSale" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-indigo text-white py-2" style="background-color: #4f46e5;">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-file-invoice mr-1"></i> Rincian Faktur Penjualan Grosir</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="detailModalBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-indigo"></i> Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(saleId) {
    $('#modalDetailSale').modal('show');
    const modalBody = document.getElementById('detailModalBody');
    modalBody.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-indigo"></i> Memuat data...</div>';

    fetch('<?= base_url('distributor/detail-penjualan/') ?>/' + saleId)
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const s = res.sale;
            const items = res.items;

            let itemsHtml = '';
            items.forEach((it, idx) => {
                itemsHtml += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>
                            <strong>${it.med_name}</strong>
                            <small class="text-muted d-block">${it.med_code} | Sat: ${it.unit}</small>
                        </td>
                        <td><span class="badge badge-light border">${it.batch_no || '-'}</span></td>
                        <td class="text-center font-weight-bold">${it.qty}</td>
                        <td class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(it.selling_price)}</td>
                        <td class="text-right font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(it.subtotal)}</td>
                    </tr>
                `;
            });

            modalBody.innerHTML = `
                <div class="row mb-3">
                    <div class="col-6">
                        <span class="text-muted text-xs font-weight-bold d-block">PELANGGAN:</span>
                        <h6 class="font-weight-bold text-dark mb-0">${s.customer_name}</h6>
                        <small class="text-muted d-block">${s.customer_company || ''}</small>
                        <small class="text-muted d-block">${s.customer_address || ''} | Telp: ${s.customer_phone || '-'}</small>
                    </div>
                    <div class="col-6 text-right">
                        <span class="text-muted text-xs font-weight-bold d-block">NO. FAKTUR:</span>
                        <h5 class="font-weight-bold text-indigo mb-0">${s.invoice_no}</h5>
                        <small class="text-muted d-block">Tanggal: ${s.sale_date}</small>
                        <small class="text-muted d-block">Kasir: ${s.cashier_name || 'Admin'}</small>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Obat / Barang</th>
                                <th>Batch</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-7">
                        <div class="p-2 bg-light rounded border text-xs">
                            <strong>Catatan:</strong> ${s.notes || '-'}
                        </div>
                    </div>
                    <div class="col-5">
                        <table class="table table-sm text-sm mb-0">
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-right font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(s.subtotal)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diskon:</td>
                                <td class="text-right text-danger">Rp ${new Intl.NumberFormat('id-ID').format(s.discount_amount)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">PPN (${s.tax_percent}%):</td>
                                <td class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(s.tax_amount)}</td>
                            </tr>
                            <tr class="bg-light">
                                <td class="font-weight-bold">Total Tagihan:</td>
                                <td class="text-right font-weight-bold text-indigo">Rp ${new Intl.NumberFormat('id-ID').format(s.total_amount)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sudah Dibayar:</td>
                                <td class="text-right text-success font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(s.paid_amount)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sisa Tagihan:</td>
                                <td class="text-right text-danger font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(s.remaining_amount)}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-right mt-3">
                    <a href="<?= base_url('distributor/cetak-faktur/') ?>/${s.id}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-print mr-1"></i> Cetak Faktur
                    </a>
                    <a href="<?= base_url('distributor/cetak-surat-jalan/') ?>/${s.id}" target="_blank" class="btn btn-sm btn-secondary">
                        <i class="fas fa-truck mr-1"></i> Cetak Surat Jalan
                    </a>
                </div>
            `;
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat rincian faktur.</div>';
        }
    })
    .catch(err => {
        modalBody.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan: ' + err + '</div>';
    });
}
</script>
<?= $this->endSection() ?>
