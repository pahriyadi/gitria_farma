<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-teal shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-book-bookmark text-teal mr-1"></i> Bagan Akun (Chart of Accounts)</h3>
                <div class="ml-auto">
                    <button class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#coaModal">
                        <i class="fas fa-plus"></i> Registrasi Akun Baru
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered datatable">
                    <thead>
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Rekening Akun</th>
                            <th>Tipe Kategori</th>
                            <th>Saldo Normal</th>
                            <th>Saldo Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $acc): ?>
                            <tr>
                                <td><span class="badge badge-teal py-1 px-2 font-weight-bold"><?= esc($acc->code) ?></span></td>
                                <td><strong><?= esc($acc->name) ?></strong></td>
                                <td><span class="badge badge-secondary"><?= strtoupper(esc($acc->type)) ?></span></td>
                                <td><?= ucfirst(esc($acc->normal_balance)) ?></td>
                                <td class="font-weight-bold">
                                    Rp <?= number_format($acc->balance, 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
            <form action="<?= base_url('accounting/coa') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Akun <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: 1-105" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Rekening Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Akun (misal: Kas Kecil)" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Kategori Akun <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="asset">Asset (Aktiva)</option>
                            <option value="liability">Liability (Kewajiban)</option>
                            <option value="equity">Equity (Modal)</option>
                            <option value="revenue">Revenue (Pendapatan)</option>
                            <option value="expense">Expense (Beban Pengeluaran)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Saldo Normal <span class="text-danger">*</span></label>
                        <select name="normal_balance" class="form-control" required>
                            <option value="debit">Debet (Asset/Expense)</option>
                            <option value="credit">Kredit (Liability/Equity/Revenue)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Saldo Awal (Rp)</label>
                        <input type="number" name="balance" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save"></i> Daftarkan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
