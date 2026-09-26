<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-credit-card text-teal mr-2"></i> Master Metode Pembayaran</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item active">Metode Pembayaran</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <div class="card card-teal card-outline shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-teal mb-0">
                    <i class="fas fa-money-check-alt mr-1"></i> Pengaturan Kanal & Rekening Pembayaran
                </h3>
                <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modal-add-payment">
                    <i class="fas fa-plus mr-1"></i> Tambah Metode Pembayaran
                </button>
            </div>
            <div class="card-body">
                <p class="text-secondary text-sm mb-3">
                    <i class="fas fa-info-circle text-info mr-1"></i>
                    Metode pembayaran yang berstatus <strong>Aktif</strong> akan otomatis muncul pada modul <strong>Kasir Pembayaran</strong>, <strong>Penjualan Obat Bebas Apotek</strong>, <strong>Resto POS</strong>, dan <strong>Pendaftaran Pasien</strong>.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable" style="background-color: #ffffff;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 130px;">Kode</th>
                                <th>Nama Metode</th>
                                <th style="width: 120px;" class="text-center">Kategori</th>
                                <th>No. Rekening / Merchant</th>
                                <th>Atas Nama / Pemilik</th>
                                <th>Keterangan</th>
                                <th style="width: 90px;" class="text-center">Status</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($methods as $m): ?>
                                <?php
                                    $catBadge = 'badge-secondary';
                                    if ($m->category === 'cash') $catBadge = 'badge-success';
                                    elseif ($m->category === 'qris') $catBadge = 'badge-info';
                                    elseif ($m->category === 'transfer') $catBadge = 'badge-primary';
                                    elseif ($m->category === 'debit' || $m->category === 'credit') $catBadge = 'badge-warning text-dark';
                                    elseif ($m->category === 'insurance') $catBadge = 'badge-teal';
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code class="font-weight-bold"><?= esc($m->code) ?></code></td>
                                    <td>
                                        <strong class="text-dark"><?= esc($m->name) ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $catBadge ?>"><?= strtoupper(esc($m->category)) ?></span>
                                    </td>
                                    <td><?= esc($m->account_number ?: '-') ?></td>
                                    <td><?= esc($m->account_name ?: '-') ?></td>
                                    <td><small class="text-muted"><?= esc($m->notes ?: '-') ?></small></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('system/master-pembayaran/toggle/' . $m->id) ?>" class="badge <?= $m->is_active ? 'badge-success' : 'badge-danger' ?> py-1 px-2 text-decoration-none" title="Klik untuk ubah status">
                                            <?= $m->is_active ? '<i class="fas fa-check-circle mr-1"></i> Aktif' : '<i class="fas fa-times-circle mr-1"></i> Nonaktif' ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-teal btn-xs btn-edit-payment" 
                                                data-id="<?= $m->id ?>"
                                                data-code="<?= esc($m->code) ?>"
                                                data-name="<?= esc($m->name) ?>"
                                                data-category="<?= esc($m->category) ?>"
                                                data-accountno="<?= esc($m->account_number) ?>"
                                                data-accountname="<?= esc($m->account_name) ?>"
                                                data-notes="<?= esc($m->notes) ?>"
                                                data-active="<?= $m->is_active ?>"
                                                title="Edit Metode">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('system/master-pembayaran/delete/' . $m->id) ?>" class="btn btn-outline-danger btn-xs" onclick="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?');" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

<!-- ========================================================================= -->
<!-- MODAL TAMBAH METODE PEMBAYARAN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-add-payment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('system/master-pembayaran/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-1"></i> Tambah Metode Pembayaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Sistem (Unik / Huruf Kecil & Underscore) <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control font-weight-bold" placeholder="contoh: transfer_bni, qris_dana, dll" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Metode Pembayaran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control font-weight-bold" placeholder="contoh: Transfer Bank BNI, QRIS DANA" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Pembayaran <span class="text-danger">*</span></label>
                        <select name="category" class="form-control font-weight-bold" required>
                            <option value="cash">TUNAI / CASH</option>
                            <option value="qris">QRIS (QR Code)</option>
                            <option value="transfer">TRANSFER BANK</option>
                            <option value="debit">KARTU DEBIT (EDC)</option>
                            <option value="credit">KARTU KREDIT (EDC)</option>
                            <option value="insurance">ASURANSI / BPJS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Rekening / No. Merchant / NMID</label>
                        <input type="text" name="account_number" class="form-control" placeholder="Nomor rekening bank atau ID merchant">
                    </div>
                    <div class="form-group">
                        <label>Atas Nama Rekening / Pemilik Akun</label>
                        <input type="text" name="account_name" class="form-control" placeholder="a.n. Sawamawa Medical Center">
                    </div>
                    <div class="form-group">
                        <label>Catatan / Instruksi Pembayaran</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Keterangan tambahan untuk kasir atau pasien"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="switch-add-active" checked value="1">
                            <label class="custom-control-label font-weight-bold" for="switch-add-active">Status Aktif (Tampil di Kasir & Farmasi)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Metode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT METODE PEMBAYARAN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-edit-payment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('system/master-pembayaran/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Metode Pembayaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Sistem (Unik) <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-code" class="form-control font-weight-bold" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Metode Pembayaran <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-name" class="form-control font-weight-bold" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Pembayaran <span class="text-danger">*</span></label>
                        <select name="category" id="edit-category" class="form-control font-weight-bold" required>
                            <option value="cash">TUNAI / CASH</option>
                            <option value="qris">QRIS (QR Code)</option>
                            <option value="transfer">TRANSFER BANK</option>
                            <option value="debit">KARTU DEBIT (EDC)</option>
                            <option value="credit">KARTU KREDIT (EDC)</option>
                            <option value="insurance">ASURANSI / BPJS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Rekening / No. Merchant / NMID</label>
                        <input type="text" name="account_number" id="edit-accountno" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Atas Nama Rekening / Pemilik Akun</label>
                        <input type="text" name="account_name" id="edit-accountname" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Catatan / Instruksi Pembayaran</label>
                        <textarea name="notes" id="edit-notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="switch-edit-active" value="1">
                            <label class="custom-control-label font-weight-bold" for="switch-edit-active">Status Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold shadow-sm"><i class="fas fa-save mr-1"></i> Perbarui Metode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-edit-payment').click(function() {
        const id = $(this).data('id');
        const code = $(this).data('code');
        const name = $(this).data('name');
        const category = $(this).data('category');
        const accountNo = $(this).data('accountno');
        const accountName = $(this).data('accountname');
        const notes = $(this).data('notes');
        const isActive = $(this).data('active');

        $('#edit-id').val(id);
        $('#edit-code').val(code);
        $('#edit-name').val(name);
        $('#edit-category').val(category);
        $('#edit-accountno').val(accountNo);
        $('#edit-accountname').val(accountName);
        $('#edit-notes').val(notes);
        $('#switch-edit-active').prop('checked', isActive == 1);

        $('#modal-edit-payment').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
