<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-wallet text-teal mr-2"></i> Kas & Rekening Bank Operasional
                </h1>
                <small class="text-muted">Kelola saldo laci kasir, pengeluaran kas kecil (petty cash), mutasi antar rekening, dan pendapatan non-pelayanan</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-none mr-1" data-toggle="modal" data-target="#modalSaldoAwalKas">
                    <i class="fas fa-coins mr-1"></i> Atur Saldo Awal
                </button>
                <button class="btn btn-danger btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalExpense">
                    <i class="fas fa-arrow-up-from-bracket mr-1"></i> Catat Beban Kas Kecil
                </button>
                <button class="btn btn-primary btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalTransfer">
                    <i class="fas fa-right-left mr-1"></i> Transfer Kas / Bank
                </button>
                <button class="btn btn-success btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalRevenue">
                    <i class="fas fa-plus-circle mr-1"></i> Pemasukan Lain
                </button>
                <a href="<?= base_url('keuangan/rekap-harian') ?>" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-calendar-check mr-1"></i> Rekap Harian
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- CASH & BANK ACCOUNT BALANCES -->
        <div class="row mb-4">
            <?php foreach ($cashBankAccounts as $acc): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid <?= str_contains(strtolower($acc->name), 'kasir') ? '#0d9488' : (str_contains(strtolower($acc->name), 'bca') ? '#0284c7' : '#8b5cf6') ?> !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-xs font-weight-bold text-secondary text-uppercase">[<?= esc($acc->code) ?>] <?= esc($acc->name) ?></span>
                                    <h4 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($acc->balance, 2, ',', '.') ?></h4>
                                    <small class="text-muted">Status: Aktif</small>
                                </div>
                                <div class="bg-light p-3 rounded-circle text-teal">
                                    <i class="fas <?= str_contains(strtolower($acc->name), 'bank') ? 'fa-building-columns' : (str_contains(strtolower($acc->name), 'qris') ? 'fa-qrcode' : 'fa-vault') ?> fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="transaksi-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-expenses-link" data-toggle="pill" href="#tab-expenses" role="tab">
                            <i class="fas fa-arrow-trend-down text-danger mr-1"></i> 1. PENGELUARAN BEBAN OPERASIONAL (PETTY CASH)
                            <span class="badge badge-danger ml-2"><?= count($expenses) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-receipts-link" data-toggle="pill" href="#tab-receipts" role="tab">
                            <i class="fas fa-receipt text-success mr-1"></i> 2. PENERIMAAN KWITANSI KASIR
                            <span class="badge badge-success ml-2"><?= count($transactions) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-transfers-link" data-toggle="pill" href="#tab-transfers" role="tab">
                            <i class="fas fa-right-left text-primary mr-1"></i> 3. MUTASI TRANSFER ANTAR KAS & BANK
                            <span class="badge badge-primary ml-2"><?= count($transfers) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="transaksi-tabsContent">

                    <!-- TAB 1: PENGELUARAN BEBAN OPERASIONAL -->
                    <div class="tab-pane fade show active" id="tab-expenses" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Riwayat Pengeluaran Kas Kecil & Beban Operasional</h6>
                                <small class="text-muted">Pencatatan beban operasional otomatis memotong saldo akun kas/bank dan membukukan jurnal akuntansi</small>
                            </div>
                            <button class="btn btn-danger btn-sm font-weight-bold" data-toggle="modal" data-target="#modalExpense">
                                <i class="fas fa-plus mr-1"></i> Input Pengeluaran Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;" class="text-center">No. Jurnal</th>
                                        <th style="width: 100px;">Tanggal</th>
                                        <th>Kategori Beban Akun</th>
                                        <th>Keterangan / Keperluan</th>
                                        <th>Sumber Kas/Bank</th>
                                        <th style="width: 140px;" class="text-right text-danger">Nominal Beban</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($expenses)): ?>
                                        <?php foreach ($expenses as $exp): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-teal"><?= esc($exp->journal_no) ?></td>
                                                <td><?= date('d/m/Y', strtotime($exp->entry_date)) ?></td>
                                                <td>
                                                    <span class="badge badge-light border font-weight-bold text-dark">
                                                        [<?= esc($exp->expense_code) ?>] <?= esc($exp->expense_name) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($exp->description) ?></td>
                                                <td>
                                                    <span class="badge badge-secondary font-weight-bold">
                                                        <?= esc($exp->source_name ?: 'Kas Kasir Utama') ?>
                                                    </span>
                                                </td>
                                                <td class="text-right font-weight-bold text-danger">
                                                    - Rp <?= number_format($exp->amount, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: PENERIMAAN KWITANSI KASIR -->
                    <div class="tab-pane fade" id="tab-receipts" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Riwayat Kwitansi Masuk Kasir</h6>
                                <small class="text-muted">Seluruh transaksi pembayaran yang telah diselesaikan oleh kasir klinik</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. Kwitansi</th>
                                        <th>No. Billing</th>
                                        <th>Nama Pasien</th>
                                        <th class="text-right">Nominal Masuk</th>
                                        <th class="text-center">Metode Bayar</th>
                                        <th>Waktu Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $t): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($t->receipt_no) ?></td>
                                            <td><strong><?= esc($t->billing_no) ?></strong></td>
                                            <td><?= esc($t->patient_name ?? 'Pasien Umum') ?></td>
                                            <td class="text-right font-weight-bold text-success">
                                                Rp <?= number_format($t->amount, 2, ',', '.') ?>
                                            </td>
                                            <td class="text-center text-uppercase font-weight-bold" style="font-size: 11px;">
                                                <span class="badge badge-light border"><?= esc($t->payment_method) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($t->created_at)) ?> WITA</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: MUTASI TRANSFER KAS & BANK -->
                    <div class="tab-pane fade" id="tab-transfers" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Riwayat Mutasi & Transfer Internal Kas/Bank</h6>
                                <small class="text-muted">Daftar pemindahan dana internal antar akun kasir dan rekening bank operasional</small>
                            </div>
                            <button class="btn btn-primary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalTransfer">
                                <i class="fas fa-right-left mr-1"></i> Buat Mutasi Transfer
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. Mutasi</th>
                                        <th style="width: 100px;">Tanggal</th>
                                        <th>Rekening Asal (Sumber)</th>
                                        <th style="width: 30px;" class="text-center"></th>
                                        <th>Rekening Tujuan</th>
                                        <th class="text-right">Nominal Transfer</th>
                                        <th>Keterangan</th>
                                        <th>Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transfers)): ?>
                                        <?php foreach ($transfers as $trf): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-teal"><?= esc($trf->transfer_no) ?></td>
                                                <td><?= date('d/m/Y', strtotime($trf->transfer_date)) ?></td>
                                                <td>
                                                    <span class="badge badge-secondary font-weight-bold">[<?= esc($trf->from_code) ?>] <?= esc($trf->from_name) ?></span>
                                                </td>
                                                <td class="text-center text-teal"><i class="fas fa-arrow-right"></i></td>
                                                <td>
                                                    <span class="badge badge-info font-weight-bold">[<?= esc($trf->to_code) ?>] <?= esc($trf->to_name) ?></span>
                                                </td>
                                                <td class="text-right font-weight-bold text-dark">
                                                    Rp <?= number_format($trf->amount, 2, ',', '.') ?>
                                                </td>
                                                <td><?= esc($trf->description) ?></td>
                                                <td><?= esc($trf->created_by_name ?: 'Administrator') ?></td>
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

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CATAT PENGELUARAN BEBAN OPERASIONAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalExpense" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-arrow-up-from-bracket mr-1"></i> Catat Beban Operasional / Kas Kecil
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/transaksi') ?>" method="post">
                <input type="hidden" name="action" value="add_expense">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Kategori Akun Beban: <span class="text-danger">*</span></label>
                        <select name="expense_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($expenseAccounts as $ea): ?>
                                <option value="<?= $ea->id ?>" <?= $ea->code === '6-101' ? 'selected' : '' ?>>
                                    [<?= esc($ea->code) ?>] <?= esc($ea->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Sumber Dana (Akun Kas/Bank): <span class="text-danger">*</span></label>
                        <select name="source_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nominal Pengeluaran (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-danger form-control-lg" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Deskripsi Keperluan Pengeluaran: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Contoh: Pembelian kertas resep, pembayaran token listrik poli, konsumsi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Bukukan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: MUTASI TRANSFER KAS & BANK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalTransfer" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-right-left mr-1"></i> Mutasi Transfer Antar Kas & Bank
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/transfer-kas') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Rekening Asal (Sumber Dana): <span class="text-danger">*</span></label>
                        <select name="from_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Rekening Tujuan: <span class="text-danger">*</span></label>
                        <select name="to_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>" <?= str_contains(strtolower($cba->name), 'bca') ? 'selected' : '' ?>>
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nominal Transfer (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-primary form-control-lg" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Keterangan Transfer:</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Contoh: Setoran hasil kasir harian ke rekening Bank BCA"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Proses Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CATAT PEMASUKAN KAS LAIN-LAIN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRevenue" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Catat Pemasukan Kas Non-Pelayanan
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/pemasukan-lain') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Rekening Penerima (Kas/Bank): <span class="text-danger">*</span></label>
                        <select name="target_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Kategori Pendapatan: <span class="text-danger">*</span></label>
                        <select name="revenue_account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($revenueAccounts as $ra): ?>
                                <option value="<?= $ra->id ?>">
                                    [<?= esc($ra->code) ?>] <?= esc($ra->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nominal Pemasukan (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-success form-control-lg" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Deskripsi Pemasukan:</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Contoh: Pendapatan sewa kantin/lahan, bunga bank giro, koreksi saldo"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Pemasukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SET SALDO AWAL KAS / BANK -->
<div class="modal fade" id="modalSaldoAwalKas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal text-white p-3">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-coins mr-1"></i> Atur / Sesuaikan Saldo Awal Rekening Kas & Bank
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/set-saldo-awal-kas') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="alert alert-info alert-excel text-xs mb-3">
                        <i class="fas fa-info-circle mr-1"></i> <strong>Saldo Awal Pertama Kali:</strong>
                        Masukkan jumlah uang tunai di laci kasir atau saldo awal di rekening bank saat sistem pertama kali mulai dioperasikan.
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Pilih Rekening Kas / Bank: <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control form-control-sm font-weight-bold" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo Saat Ini: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Nominal Saldo Awal Baru (Rp): <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="new_balance" class="form-control font-weight-bold text-teal form-control-lg" placeholder="0" required min="0">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Keterangan / Catatan:</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Modal Kas Awal Kasir / Saldo Awal Rekening Giro" value="Modal Awal Kas Operasional">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Terapkan Saldo Awal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
