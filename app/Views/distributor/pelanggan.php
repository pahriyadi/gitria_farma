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
            <i class="fas fa-users-rectangle text-indigo mr-2"></i> Database Pelanggan / Customer B2B
        </h4>
        <p class="small mb-0" style="color: var(--dist-text-muted);">
            Kelola master pelanggan grosir, limit kredit, termin pembayaran (TOP), dan riwayat piutang
        </p>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <button type="button" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" id="btnOpenAddCustomer" onclick="openAddCustomerModal()" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Pelanggan Baru
            </button>
            <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-outline-success btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-cash-register mr-1"></i> Transaksi Kasir
            </a>
            <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Cek Piutang
            </a>
        </div>
    </div>
</div>

<!-- CUSTOMERS TABLE CARD -->
<div class="dist-card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="tableCustomers">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Pelanggan &amp; Perusahaan</th>
                        <th>Kontak &amp; Alamat</th>
                        <th>NPWP</th>
                        <th class="text-right">Limit Kredit</th>
                        <th class="text-right">Piutang Berjalan</th>
                        <th class="text-center">Termin (TOP)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5" style="color: var(--dist-text-muted);">
                                <i class="fas fa-users-slash fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                Belum ada data pelanggan B2B. Silakan tambah pelanggan baru.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td class="font-weight-bold text-indigo"><?= esc($c->code) ?></td>
                                <td>
                                    <strong class="d-block" style="color: var(--dist-text);"><?= esc($c->name) ?></strong>
                                    <?php if (!empty($c->company_name)): ?>
                                        <small class="d-block" style="color: var(--dist-text-muted);"><i class="fas fa-building mr-1"></i><?= esc($c->company_name) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="color: var(--dist-text);"><i class="fas fa-phone-alt text-muted mr-1"></i><?= esc($c->phone) ?></div>
                                    <?php if (!empty($c->address)): ?>
                                        <small class="d-block text-truncate" style="max-width: 200px; color: var(--dist-text-muted);"><i class="fas fa-map-marker-alt text-muted mr-1"></i><?= esc($c->address) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($c->npwp ?: '-') ?></td>
                                <td class="text-right font-weight-bold" style="color: var(--dist-text);">
                                    Rp <?= number_format($c->credit_limit, 0, ',', '.') ?>
                                </td>
                                <td class="text-right font-weight-bold <?= $c->current_receivable > 0 ? 'text-danger' : 'text-success' ?>">
                                    Rp <?= number_format($c->current_receivable, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border"><?= $c->payment_terms_days > 0 ? ($c->payment_terms_days . ' Hari') : 'Cash / COD' ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($c->status === 'active'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-customer" onclick='editCustomer(<?= json_encode($c) ?>)' title="Edit Pelanggan">
                                        <i class="fas fa-edit mr-1"></i> Edit
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

<!-- MODAL ADD / EDIT CUSTOMER -->
<div class="modal fade" id="modalCustomerForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <form action="<?= base_url('distributor/simpan-pelanggan') ?>" method="post" id="formCustomer">
                <input type="hidden" name="id" id="cust_id" value="">
                
                <div class="modal-header bg-indigo text-white py-2.5" style="background-color: #4f46e5;">
                    <h6 class="modal-title font-weight-bold" id="modalCustomerTitle"><i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan Baru</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">KODE PELANGGAN</label>
                            <input type="text" class="form-control form-control-sm dist-input" name="code" id="cust_code" placeholder="Otomatis jika kosong">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">NAMA PELANGGAN / APOTEK MITRA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm dist-input" name="name" id="cust_name" required placeholder="Contoh: Apotek Sehat Sentosa">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">NAMA PERUSAHAAN / BADAN HUKUM</label>
                            <input type="text" class="form-control form-control-sm dist-input" name="company_name" id="cust_company" placeholder="Contoh: PT Sehat Sentosa Jaya">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">NO. TELEPON / WA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm dist-input" name="phone" id="cust_phone" required placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">EMAIL</label>
                            <input type="email" class="form-control form-control-sm dist-input" name="email" id="cust_email" placeholder="kontak@perusahaan.com">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">NPWP PERUSAHAAN</label>
                            <input type="text" class="form-control form-control-sm dist-input" name="npwp" id="cust_npwp" placeholder="00.000.000.0-000.000">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">ALAMAT LENGKAP &amp; PENGIRIMAN</label>
                            <textarea class="form-control form-control-sm dist-input" name="address" id="cust_address" rows="2" placeholder="Alamat lengkap tujuan pengiriman barang..."></textarea>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">LIMIT KREDIT (RP)</label>
                            <input type="number" class="form-control form-control-sm dist-input" name="credit_limit" id="cust_limit" value="0" min="0" step="1000">
                            <small class="text-muted">Isi 0 jika tidak ada plafon kredit.</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">TERMIN PEMBAYARAN / TOP (HARI)</label>
                            <input type="number" class="form-control form-control-sm dist-input" name="payment_terms_days" id="cust_terms" value="30" min="0">
                            <small class="text-muted">Isi 0 untuk Tunai / COD.</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">STATUS</label>
                            <select class="form-control form-control-sm dist-input" name="status" id="cust_status" data-native="true">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-indigo font-weight-bold" id="btnSubmitCustomer" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-save mr-1"></i> Simpan Data Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddCustomerModal() {
    document.getElementById('cust_id').value = '';
    document.getElementById('cust_code').value = '';
    document.getElementById('cust_name').value = '';
    document.getElementById('cust_company').value = '';
    document.getElementById('cust_phone').value = '';
    document.getElementById('cust_email').value = '';
    document.getElementById('cust_npwp').value = '';
    document.getElementById('cust_address').value = '';
    document.getElementById('cust_limit').value = '0';
    document.getElementById('cust_terms').value = '30';
    document.getElementById('cust_status').value = 'active';

    document.getElementById('modalCustomerTitle').innerHTML = '<i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan Baru';
    $('#modalCustomerForm').modal('show');
}

function editCustomer(c) {
    document.getElementById('cust_id').value = c.id;
    document.getElementById('cust_code').value = c.code;
    document.getElementById('cust_name').value = c.name;
    document.getElementById('cust_company').value = c.company_name || '';
    document.getElementById('cust_phone').value = c.phone;
    document.getElementById('cust_email').value = c.email || '';
    document.getElementById('cust_npwp').value = c.npwp || '';
    document.getElementById('cust_address').value = c.address || '';
    document.getElementById('cust_limit').value = c.credit_limit;
    document.getElementById('cust_terms').value = c.payment_terms_days;
    document.getElementById('cust_status').value = c.status;

    document.getElementById('modalCustomerTitle').innerHTML = '<i class="fas fa-edit mr-1"></i> Edit Data Pelanggan';
    $('#modalCustomerForm').modal('show');
}
</script>
<?= $this->endSection() ?>
