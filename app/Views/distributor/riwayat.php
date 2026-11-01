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
        border-top: 1px solid var(--dist-border) !important;
    }

    .dist-box-info {
        background-color: var(--dist-box-bg);
        border: 1px solid var(--dist-border);
        border-radius: 4px;
    }
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="font-weight-bold mb-1" style="color: var(--dist-text);">
            <i class="fas fa-receipt text-indigo mr-2"></i> Riwayat Penjualan Distributor
        </h4>
        <p class="small mb-0" style="color: var(--dist-text-muted);">
            Arsip faktur penjualan, status pembayaran, cetak faktur &amp; surat jalan
        </p>
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

<!-- FILTER CARD -->
<div class="dist-card mb-3">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('distributor/riwayat') ?>">
            <div class="row">
                <div class="col-md-2 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">DARI TANGGAL</label>
                    <input type="date" class="form-control form-control-sm dist-input" name="start_date" value="<?= esc($startDate) ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">SAMPAI TANGGAL</label>
                    <input type="date" class="form-control form-control-sm dist-input" name="end_date" value="<?= esc($endDate) ?>">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">PELANGGAN</label>
                    <select class="form-control form-control-sm dist-input" name="customer_id" data-native="true">
                        <option value="">-- Semua Pelanggan --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c->id ?>" <?= $customerId == $c->id ? 'selected' : '' ?>>
                                <?= esc($c->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">METODE BAYAR</label>
                    <select class="form-control form-control-sm dist-input" name="payment_type" data-native="true">
                        <option value="">-- Semua --</option>
                        <option value="cash" <?= $paymentType === 'cash' ? 'selected' : '' ?>>Tunai</option>
                        <option value="transfer" <?= $paymentType === 'transfer' ? 'selected' : '' ?>>Transfer</option>
                        <option value="credit" <?= $paymentType === 'credit' ? 'selected' : '' ?>>Kredit / Tempo</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">STATUS</label>
                    <select class="form-control form-control-sm dist-input" name="status" data-native="true">
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
<div class="dist-card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="tableSalesHistory">
                <thead>
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
                            <td colspan="9" class="text-center py-5" style="color: var(--dist-text-muted);">
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
                                    <strong class="d-block" style="color: var(--dist-text);"><?= esc($s->customer_name) ?></strong>
                                    <small style="color: var(--dist-text-muted);"><i class="fas fa-phone-alt mr-1"></i><?= esc($s->customer_phone) ?></small>
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
                                <td class="text-right font-weight-bold" style="color: var(--dist-text);">
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
                                        <button type="button" class="btn btn-xs btn-outline-info btn-view-detail" onclick="viewDetail(<?= $s->id ?>)" title="Rincian Item">
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

<!-- MODAL DETAIL PENJUALAN -->
<div class="modal fade" id="modalDetailSale" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header bg-indigo text-white py-2.5" style="background-color: #4f46e5;">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-file-invoice mr-1"></i> Rincian Faktur Penjualan Grosir</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="detailModalBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-indigo"></i> Memuat data...</div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
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
                        <td class="text-center">${idx + 1}</td>
                        <td>
                            <strong style="color: var(--dist-text);">${it.med_name}</strong>
                            <small class="d-block" style="color: var(--dist-text-muted);">${it.med_code} | Sat: ${it.unit}</small>
                        </td>
                        <td><span class="badge badge-light border">${it.batch_no || '-'}</span></td>
                        <td class="text-center font-weight-bold text-indigo">${it.qty}</td>
                        <td class="text-right">Rp ${new Intl.NumberFormat('id-ID').format(it.selling_price)}</td>
                        <td class="text-right font-weight-bold" style="color: var(--dist-text);">Rp ${new Intl.NumberFormat('id-ID').format(it.subtotal)}</td>
                    </tr>
                `;
            });

            modalBody.innerHTML = `
                <div class="row mb-3">
                    <div class="col-6">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-text-muted);">PELANGGAN:</span>
                        <h6 class="font-weight-bold mb-0" style="color: var(--dist-text);">${s.customer_name}</h6>
                        <small class="d-block" style="color: var(--dist-text-muted);">${s.customer_company || ''}</small>
                        <small class="d-block" style="color: var(--dist-text-muted);">${s.customer_address || ''} | Telp: ${s.customer_phone || '-'}</small>
                    </div>
                    <div class="col-6 text-right">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-text-muted);">NO. FAKTUR:</span>
                        <h5 class="font-weight-bold text-indigo mb-0">${s.invoice_no}</h5>
                        <small class="d-block" style="color: var(--dist-text-muted);">Tanggal: ${s.sale_date}</small>
                        <small class="d-block" style="color: var(--dist-text-muted);">Kasir: ${s.cashier_name || 'Admin'}</small>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table-dist text-sm">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
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
                        <div class="dist-box-info p-2 text-xs">
                            <strong style="color: var(--dist-text);">Catatan:</strong> 
                            <span style="color: var(--dist-text-muted);">${s.notes || '-'}</span>
                        </div>
                    </div>
                    <div class="col-5">
                        <table class="table-dist text-sm mb-0">
                            <tr>
                                <td style="color: var(--dist-text-muted);">Subtotal:</td>
                                <td class="text-right font-weight-bold" style="color: var(--dist-text);">Rp ${new Intl.NumberFormat('id-ID').format(s.subtotal)}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--dist-text-muted);">Diskon:</td>
                                <td class="text-right text-danger">Rp ${new Intl.NumberFormat('id-ID').format(s.discount_amount)}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--dist-text-muted);">PPN (${s.tax_percent}%):</td>
                                <td class="text-right" style="color: var(--dist-text);">Rp ${new Intl.NumberFormat('id-ID').format(s.tax_amount)}</td>
                            </tr>
                            <tr style="background-color: var(--dist-th-bg);">
                                <td class="font-weight-bold" style="color: var(--dist-text);">Total Tagihan:</td>
                                <td class="text-right font-weight-bold text-indigo">Rp ${new Intl.NumberFormat('id-ID').format(s.total_amount)}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--dist-text-muted);">Sudah Dibayar:</td>
                                <td class="text-right text-success font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(s.paid_amount)}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--dist-text-muted);">Sisa Tagihan:</td>
                                <td class="text-right text-danger font-weight-bold">Rp ${new Intl.NumberFormat('id-ID').format(s.remaining_amount)}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-right mt-3">
                    <a href="<?= base_url('distributor/cetak-faktur/') ?>/${s.id}" target="_blank" class="btn btn-sm btn-indigo" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-print mr-1"></i> Cetak Faktur
                    </a>
                    <a href="<?= base_url('distributor/cetak-surat-jalan/') ?>/${s.id}" target="_blank" class="btn btn-sm btn-outline-secondary">
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
