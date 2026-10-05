<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-users-rectangle text-indigo mr-2"></i> Database Pelanggan / Customer B2B
                </h4>
                <small class="text-muted">Kelola master pelanggan grosir, limit kredit, termin pembayaran (TOP), dan riwayat piutang</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <button type="button" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" onclick="openAddCustomerModal()" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
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

        <!-- CUSTOMERS TABLE CARD -->
        <div class="card card-outline card-indigo shadow-none border">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
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
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                        Belum ada data pelanggan B2B. Silakan tambah pelanggan baru.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $c): ?>
                                    <tr>
                                        <td class="font-weight-bold text-indigo"><?= esc($c->code) ?></td>
                                        <td>
                                            <strong class="text-dark d-block"><?= esc($c->name) ?></strong>
                                            <?php if (!empty($c->company_name)): ?>
                                                <small class="text-muted d-block"><i class="fas fa-building mr-1"></i><?= esc($c->company_name) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><i class="fas fa-phone-alt text-muted mr-1"></i><?= esc($c->phone) ?></div>
                                            <?php if (!empty($c->address)): ?>
                                                <small class="text-muted d-block text-truncate" style="max-width: 200px;"><i class="fas fa-map-marker-alt text-muted mr-1"></i><?= esc($c->address) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($c->npwp ?: '-') ?></td>
                                        <td class="text-right font-weight-bold text-dark">
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
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick='editCustomer(<?= json_encode($c) ?>)' title="Edit Pelanggan">
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

<!-- MODAL ADD / EDIT CUSTOMER -->
<div class="modal fade" id="modalCustomerForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form action="<?= base_url('distributor/simpan-pelanggan') ?>" method="post">
                <input type="hidden" name="id" id="cust_id" value="">
                
                <div class="modal-header bg-indigo text-white py-2" style="background-color: #4f46e5;">
                    <h6 class="modal-title font-weight-bold" id="modalCustomerTitle"><i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan Baru</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">KODE PELANGGAN</label>
                            <input type="text" class="form-control form-control-sm" name="code" id="cust_code" placeholder="Otomatis jika kosong">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">NAMA PELANGGAN / APOTEK MITRA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="name" id="cust_name" required placeholder="Contoh: Apotek Sehat Sentosa">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">NAMA PERUSAHAAN / BADAN HUKUM</label>
                            <input type="text" class="form-control form-control-sm" name="company_name" id="cust_company" placeholder="Contoh: PT Sehat Sentosa Jaya">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">NO. TELEPON / WA <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="phone" id="cust_phone" required placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">EMAIL</label>
                            <input type="email" class="form-control form-control-sm" name="email" id="cust_email" placeholder="kontak@perusahaan.com">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">NPWP PERUSAHAAN</label>
                            <input type="text" class="form-control form-control-sm" name="npwp" id="cust_npwp" placeholder="00.000.000.0-000.000">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">ALAMAT LENGKAP &amp; PENGIRIMAN</label>
                            <textarea class="form-control form-control-sm" name="address" id="cust_address" rows="2" placeholder="Alamat lengkap tujuan pengiriman barang..."></textarea>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">LIMIT KREDIT (RP)</label>
                            <input type="number" class="form-control form-control-sm" name="credit_limit" id="cust_limit" value="0" min="0" step="1000">
                            <small class="text-muted">Isi 0 jika tidak ada plafon kredit.</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">TERMIN PEMBAYARAN / TOP (HARI)</label>
                            <input type="number" class="form-control form-control-sm" name="payment_terms_days" id="cust_terms" value="30" min="0">
                            <small class="text-muted">Isi 0 untuk Tunai / COD.</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">STATUS</label>
                            <select class="form-control form-control-sm" name="status" id="cust_status">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-indigo font-weight-bold" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
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
