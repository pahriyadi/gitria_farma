<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Quick Switcher Tabs -->
<div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="accounting-tabs-scroll-wrapper">
        <ul class="nav nav-tabs-modern">
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/jurnal') ?>">
                    <i class="fas fa-file-lines mr-1"></i> 1. Jurnal Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/buku-besar') ?>">
                    <i class="fas fa-book-journal-whills mr-1"></i> 2. Buku Mutasi Akun
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/laporan') ?>">
                    <i class="fas fa-chart-pie mr-1"></i> 3. Laporan Keuangan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" href="<?= base_url('accounting/coa') ?>">
                    <i class="fas fa-book-bookmark mr-1"></i> 4. Bagan Akun (COA)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/aturan-jurnal') ?>">
                    <i class="fas fa-sliders mr-1"></i> 5. Template &amp; Aturan Jurnal
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/saldo-awal') ?>">
                    <i class="fas fa-scale-balanced mr-1"></i> 6. Saldo Awal
                </a>
            </li>
        </ul>
    </div>
    <div class="mt-2 mt-md-0">
        <button class="btn btn-teal btn-sm font-weight-bold shadow-xs" data-toggle="modal" data-target="#coaModal">
            <i class="fas fa-plus mr-1"></i> Registrasi Akun Baru
        </button>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm font-weight-bold" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-teal shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-book-bookmark text-teal mr-1"></i> Bagan Akun (Chart of Accounts)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable w-100" style="font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th width="120">Kode Akun</th>
                                <th>Nama Rekening Akun</th>
                                <th>Tipe Kategori</th>
                                <th>Saldo Normal</th>
                                <th>Saldo Saat Ini</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $acc): ?>
                                <tr>
                                    <td><span class="badge badge-teal py-1 px-2 font-weight-bold font-monospace" style="font-size: 13px;"><?= esc($acc->code) ?></span></td>
                                    <td><strong><?= esc($acc->name) ?></strong></td>
                                    <td><span class="badge badge-secondary"><?= strtoupper(esc($acc->type)) ?></span></td>
                                    <td><?= ucfirst(esc($acc->normal_balance)) ?></td>
                                    <td class="font-weight-bold">
                                        Rp <?= number_format($acc->balance, 2, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#editCoaModal<?= $acc->id ?>" title="Edit COA">
                                            <i class="fas fa-edit"></i>
                                        </button>
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

<!-- Modal Add COA -->
<div class="modal fade" id="coaModal" tabindex="-1" role="dialog" aria-labelledby="coaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="coaModalLabel"><i class="fas fa-plus mr-1 text-teal"></i> Registrasi Rekening COA Baru</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/coa') ?>" method="post" id="formNewCoa">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nomor / Kode Akun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="code" id="new_coa_code" class="form-control font-weight-bold" placeholder="Contoh: 1113, 243, dsb" required autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white" id="new_coa_code_status"><i class="fas fa-barcode text-muted"></i></span>
                            </div>
                        </div>
                        <div id="new_coa_code_feedback" class="mt-1 font-weight-bold"></div>
                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i> Sistem akan langsung memverifikasi agar tidak terjadi nomor akun kembar.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nama Rekening Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Akun (misal: Kas Kecil, Utang Lainnya)" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Tipe Kategori Akun <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="asset">Asset (Aktiva)</option>
                            <option value="liability">Liability (Kewajiban)</option>
                            <option value="equity">Equity (Modal)</option>
                            <option value="revenue">Revenue (Pendapatan)</option>
                            <option value="expense">Expense (Beban Pengeluaran)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Saldo Normal <span class="text-danger">*</span></label>
                        <select name="normal_balance" class="form-control" required>
                            <option value="debit">Debit (Asset/Expense)</option>
                            <option value="credit">Kredit (Liability/Equity/Revenue)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitNewCoa" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan COA Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($accounts as $acc): ?>
<!-- Edit Modal for each account -->
<div class="modal fade" id="editCoaModal<?= $acc->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-edit mr-1 text-teal"></i> Edit Rekening COA</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/coa/update/' . $acc->id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nomor / Kode Akun <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="code" class="form-control font-weight-bold edit_coa_input" id="edit_coa_code_<?= $acc->id ?>" data-account-id="<?= $acc->id ?>" data-original-code="<?= esc($acc->code) ?>" value="<?= esc($acc->code) ?>" required autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white" id="edit_coa_code_status_<?= $acc->id ?>"><i class="fas fa-check text-success"></i></span>
                            </div>
                        </div>
                        <div id="edit_coa_code_feedback_<?= $acc->id ?>" class="mt-1 font-weight-bold"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nama Rekening Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control font-weight-bold" value="<?= esc($acc->name) ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Tipe Kategori Akun <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="asset" <?= strtolower($acc->type) == 'asset' ? 'selected' : '' ?>>Asset (Aktiva)</option>
                            <option value="liability" <?= strtolower($acc->type) == 'liability' ? 'selected' : '' ?>>Liability (Kewajiban)</option>
                            <option value="equity" <?= strtolower($acc->type) == 'equity' ? 'selected' : '' ?>>Equity (Modal)</option>
                            <option value="revenue" <?= strtolower($acc->type) == 'revenue' ? 'selected' : '' ?>>Revenue (Pendapatan)</option>
                            <option value="expense" <?= strtolower($acc->type) == 'expense' ? 'selected' : '' ?>>Expense (Beban Pengeluaran)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Saldo Normal <span class="text-danger">*</span></label>
                        <select name="normal_balance" class="form-control" required>
                            <option value="debit" <?= trim(strtolower($acc->normal_balance)) == 'debit' ? 'selected' : '' ?>>Debit (Asset/Expense)</option>
                            <option value="credit" <?= trim(strtolower($acc->normal_balance)) == 'credit' ? 'selected' : '' ?>>Kredit (Liability/Equity/Revenue)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitEditCoa_<?= $acc->id ?>" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
$(document).ready(function () {
    // Database daftar akun yang ada untuk verifikasi instan client-side
    const existingAccounts = <?= json_encode(array_map(fn($a) => [
        'id'   => (int)$a->id,
        'code' => trim((string)$a->code),
        'name' => trim((string)$a->name)
    ], $accounts)) ?>;

    // 1. Verifikasi Nomor Akun Baru Secara Real-Time
    $('#new_coa_code').on('input keyup change paste', function () {
        const input = $(this);
        const code = input.val().trim();
        const feedback = $('#new_coa_code_feedback');
        const status = $('#new_coa_code_status');
        const btnSubmit = $('#btnSubmitNewCoa');

        if (code === '') {
            input.removeClass('is-invalid is-valid');
            status.html('<i class="fas fa-barcode text-muted"></i>');
            feedback.html('');
            btnSubmit.prop('disabled', false);
            return;
        }

        // Cari apakah kode sudah terdaftar
        const duplicate = existingAccounts.find(a => a.code.toLowerCase() === code.toLowerCase());

        if (duplicate) {
            input.removeClass('is-valid').addClass('is-invalid');
            status.html('<i class="fas fa-times-circle text-danger"></i>');
            feedback.html('<span class="text-danger text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> Nomor akun <strong>[' + duplicate.code + ']</strong> sudah digunakan oleh <em>"' + duplicate.name + '"</em>! Silakan gunakan nomor lain.</span>');
            btnSubmit.prop('disabled', true);
        } else {
            input.removeClass('is-invalid').addClass('is-valid');
            status.html('<i class="fas fa-check-circle text-success"></i>');
            feedback.html('<span class="text-success text-sm"><i class="fas fa-check-circle mr-1"></i> Nomor akun <strong>[' + code + ']</strong> tersedia dan dapat digunakan.</span>');
            btnSubmit.prop('disabled', false);
        }
    });

    // Reset saat modal ditutup
    $('#coaModal').on('hidden.bs.modal', function () {
        $('#new_coa_code').val('').removeClass('is-invalid is-valid');
        $('#new_coa_code_status').html('<i class="fas fa-barcode text-muted"></i>');
        $('#new_coa_code_feedback').html('');
        $('#btnSubmitNewCoa').prop('disabled', false);
    });

    // 2. Verifikasi Nomor Akun Saat Edit
    $('.edit_coa_input').on('input keyup change paste', function () {
        const input = $(this);
        const code = input.val().trim();
        const accountId = parseInt(input.data('account-id'));
        const originalCode = input.data('original-code').toString().trim();
        const feedback = $('#edit_coa_code_feedback_' + accountId);
        const status = $('#edit_coa_code_status_' + accountId);
        const btnSubmit = $('#btnSubmitEditCoa_' + accountId);

        if (code === '' || code.toLowerCase() === originalCode.toLowerCase()) {
            input.removeClass('is-invalid is-valid');
            status.html('<i class="fas fa-check text-success"></i>');
            feedback.html('');
            btnSubmit.prop('disabled', false);
            return;
        }

        // Cari apakah kode sudah terdaftar di akun LAIN
        const duplicate = existingAccounts.find(a => a.id !== accountId && a.code.toLowerCase() === code.toLowerCase());

        if (duplicate) {
            input.removeClass('is-valid').addClass('is-invalid');
            status.html('<i class="fas fa-times-circle text-danger"></i>');
            feedback.html('<span class="text-danger text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> Nomor akun <strong>[' + duplicate.code + ']</strong> sudah terdaftar atas nama <em>"' + duplicate.name + '"</em>!</span>');
            btnSubmit.prop('disabled', true);
        } else {
            input.removeClass('is-invalid').addClass('is-valid');
            status.html('<i class="fas fa-check-circle text-success"></i>');
            feedback.html('<span class="text-success text-sm"><i class="fas fa-check-circle mr-1"></i> Nomor akun <strong>[' + code + ']</strong> tersedia.</span>');
        }
    });

    // Anti double submit
    $('form#formNewCoa, form[action*="accounting/coa/update"]').on('submit', function() {
        var btn = $(this).find('button[type="submit"]');
        if (btn.length && !btn.prop('disabled')) {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
        }
    });

    // Adjust DataTable on tab or resize
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if ($.fn.DataTable.isDataTable('.datatable')) {
                $('.datatable').DataTable().columns.adjust();
            }
        }, 200);
    });
});
</script>

<?= $this->endSection() ?>
