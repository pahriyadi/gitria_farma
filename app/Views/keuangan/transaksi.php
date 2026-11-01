<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Kas & Rekening Bank Paper White / Paper Dark */
:root {
    --tx-bg: #ffffff;
    --tx-text: #1f2937;
    --tx-muted: #6b7280;
    --tx-border: #b8b8b8;
    --tx-card-bg: #ffffff;
    --tx-head-bg: #f8fafc;
    --tx-hover-bg: #f1f5f9;
    --tx-input-bg: #ffffff;
}

body.dark-mode {
    --tx-bg: #14171a;
    --tx-text: #f0f6fc;
    --tx-muted: #9ca3af;
    --tx-border: #374151;
    --tx-card-bg: #1f2328;
    --tx-head-bg: #181b1f;
    --tx-hover-bg: #272c33;
    --tx-input-bg: #181b1f;
}

.tx-card {
    background-color: var(--tx-card-bg) !important;
    border: 1px solid var(--tx-border) !important;
    color: var(--tx-text) !important;
    border-radius: 8px;
}

.table-transaksi {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--tx-border) !important;
    color: var(--tx-text) !important;
    background-color: var(--tx-card-bg) !important;
}

.table-transaksi th,
.table-transaksi td {
    border: 1px solid var(--tx-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-transaksi thead th {
    background-color: var(--tx-head-bg) !important;
    color: var(--tx-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-transaksi tbody tr:hover {
    background-color: var(--tx-hover-bg) !important;
}

.tx-input {
    background-color: var(--tx-input-bg) !important;
    border: 1px solid var(--tx-border) !important;
    color: var(--tx-text) !important;
}

.tx-input:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25) !important;
}

.nav-tabs .nav-link {
    color: var(--tx-muted) !important;
    border-color: transparent !important;
}

.nav-tabs .nav-link.active {
    color: #0d9488 !important;
    background-color: var(--tx-card-bg) !important;
    border-color: var(--tx-border) var(--tx-border) var(--tx-card-bg) !important;
    font-weight: 700;
}

body.dark-mode .nav-tabs .nav-link.active {
    color: #2dd4bf !important;
}

body.dark-mode .modal-content {
    background-color: var(--tx-card-bg) !important;
    color: var(--tx-text) !important;
    border: 1px solid var(--tx-border) !important;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: var(--tx-border) !important;
}
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--tx-text);">
            <i class="fas fa-wallet text-teal mr-2"></i> Kas &amp; Rekening Bank Operasional
        </h4>
        <small style="color: var(--tx-muted);">Kelola saldo laci kasir, pengeluaran kas kecil (petty cash), mutasi antar rekening, dan pendapatan non-pelayanan</small>
    </div>
    <div class="col-lg-7 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm" id="btnOpenSaldoAwal" data-toggle="modal" data-target="#modalSaldoAwalKas">
                <i class="fas fa-coins mr-1"></i> Atur Saldo Awal
            </button>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" id="btnOpenExpense" data-toggle="modal" data-target="#modalExpense">
                <i class="fas fa-arrow-up-from-bracket mr-1"></i> Catat Beban Kas Kecil
            </button>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" id="btnOpenTransfer" data-toggle="modal" data-target="#modalTransfer">
                <i class="fas fa-right-left mr-1"></i> Transfer Kas / Bank
            </button>
            <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" id="btnOpenRevenue" data-toggle="modal" data-target="#modalRevenue">
                <i class="fas fa-plus-circle mr-1"></i> Pemasukan Lain
            </button>
            <a href="<?= base_url('keuangan/rekap-harian') ?>" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-calendar-check mr-1"></i> Rekap Harian
            </a>
        </div>
    </div>
</div>

<!-- CASH & BANK ACCOUNT BALANCES -->
<div class="row mb-3">
    <?php foreach ($cashBankAccounts as $acc): ?>
        <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
            <div class="card tx-card h-100 shadow-sm" style="border-left: 4px solid <?= str_contains(strtolower($acc->name), 'kasir') ? '#0d9488' : (str_contains(strtolower($acc->name), 'bca') ? '#0284c7' : '#8b5cf6') ?> !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-xs font-weight-bold text-uppercase" style="color: var(--tx-muted);">[<?= esc($acc->code) ?>] <?= esc($acc->name) ?></span>
                            <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--tx-text);">Rp <?= number_format($acc->balance, 2, ',', '.') ?></h4>
                            <small class="text-success"><i class="fas fa-circle-check mr-1"></i>Aktif</small>
                        </div>
                        <div class="p-3 rounded-circle text-teal" style="background-color: var(--tx-head-bg);">
                            <i class="fas <?= str_contains(strtolower($acc->name), 'bank') ? 'fa-building-columns' : (str_contains(strtolower($acc->name), 'qris') ? 'fa-qrcode' : 'fa-vault') ?> fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- MAIN TABS CARD -->
<div class="card tx-card mb-4 shadow-sm">
    <div class="card-header p-0 pt-2 border-bottom" style="background-color: var(--tx-head-bg); border-color: var(--tx-border) !important;">
        <ul class="nav nav-tabs px-3" id="transaksi-tabs" role="tablist" style="border-bottom-color: var(--tx-border) !important;">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold py-2.5 px-3" id="tab-expenses-link" data-toggle="pill" href="#tab-expenses" role="tab">
                    <i class="fas fa-arrow-trend-down text-danger mr-1"></i> 1. PENGELUARAN BEBAN OPERASIONAL (PETTY CASH)
                    <span class="badge badge-danger ml-2"><?= count($expenses) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-2.5 px-3" id="tab-receipts-link" data-toggle="pill" href="#tab-receipts" role="tab">
                    <i class="fas fa-receipt text-success mr-1"></i> 2. PENERIMAAN KWITANSI KASIR
                    <span class="badge badge-success ml-2"><?= count($transactions) ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold py-2.5 px-3" id="tab-transfers-link" data-toggle="pill" href="#tab-transfers" role="tab">
                    <i class="fas fa-right-left text-primary mr-1"></i> 3. MUTASI TRANSFER ANTAR KAS &amp; BANK
                    <span class="badge badge-primary ml-2"><?= count($transfers) ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-3">
        <div class="tab-content" id="transaksi-tabsContent">

            <!-- TAB 1: PENGELUARAN BEBAN OPERASIONAL -->
            <div class="tab-pane fade show active" id="tab-expenses" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-bold mb-0" style="color: var(--tx-text);">Riwayat Pengeluaran Kas Kecil &amp; Beban Operasional</h6>
                        <small style="color: var(--tx-muted);">Pencatatan beban operasional otomatis memotong saldo akun kas/bank dan membukukan jurnal akuntansi</small>
                    </div>
                    <button class="btn btn-danger btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalExpense">
                        <i class="fas fa-plus mr-1"></i> Input Pengeluaran Baru
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-transaksi mb-0 text-sm" id="tableExpenses">
                        <thead>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4" style="color: var(--tx-muted);">Belum ada riwayat beban pengeluaran operasional.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: PENERIMAAN KWITANSI KASIR -->
            <div class="tab-pane fade" id="tab-receipts" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-bold mb-0" style="color: var(--tx-text);">Riwayat Kwitansi Masuk Kasir</h6>
                        <small style="color: var(--tx-muted);">Seluruh transaksi pembayaran yang telah diselesaikan oleh kasir klinik</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table-transaksi mb-0 text-sm" id="tableReceipts">
                        <thead>
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
                            <?php if (!empty($transactions)): ?>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4" style="color: var(--tx-muted);">Belum ada kwitansi transaksi pembayaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: MUTASI TRANSFER KAS & BANK -->
            <div class="tab-pane fade" id="tab-transfers" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-bold mb-0" style="color: var(--tx-text);">Riwayat Mutasi &amp; Transfer Internal Kas/Bank</h6>
                        <small style="color: var(--tx-muted);">Daftar pemindahan dana internal antar akun kasir dan rekening bank operasional</small>
                    </div>
                    <button class="btn btn-primary btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalTransfer">
                        <i class="fas fa-right-left mr-1"></i> Buat Mutasi Transfer
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table-transaksi mb-0 text-sm" id="tableTransfers">
                        <thead>
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
                                        <td class="text-right font-weight-bold" style="color: var(--tx-text);">
                                            Rp <?= number_format($trf->amount, 2, ',', '.') ?>
                                        </td>
                                        <td><?= esc($trf->description) ?></td>
                                        <td><?= esc($trf->created_by_name ?: 'Administrator') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4" style="color: var(--tx-muted);">Belum ada riwayat mutasi transfer antar rekening.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: CATAT PENGELUARAN BEBAN OPERASIONAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalExpense" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
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
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Kategori Akun Beban: <span class="text-danger">*</span></label>
                        <select name="expense_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($expenseAccounts as $ea): ?>
                                <option value="<?= $ea->id ?>" <?= $ea->code === '6-101' ? 'selected' : '' ?>>
                                    [<?= esc($ea->code) ?>] <?= esc($ea->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Sumber Dana (Akun Kas/Bank): <span class="text-danger">*</span></label>
                        <select name="source_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Nominal Pengeluaran (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-danger form-control-lg tx-input" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Deskripsi Keperluan Pengeluaran: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control form-control-sm tx-input" rows="3" placeholder="Contoh: Pembelian kertas resep, pembayaran token listrik poli, konsumsi..." required></textarea>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-right-left mr-1"></i> Mutasi Transfer Antar Kas &amp; Bank
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/transfer-kas') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Rekening Asal (Sumber Dana): <span class="text-danger">*</span></label>
                        <select name="from_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Rekening Tujuan: <span class="text-danger">*</span></label>
                        <select name="to_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>" <?= str_contains(strtolower($cba->name), 'bca') ? 'selected' : '' ?>>
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Nominal Transfer (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-primary form-control-lg tx-input" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Keterangan Transfer:</label>
                        <textarea name="description" class="form-control form-control-sm tx-input" rows="2" placeholder="Contoh: Setoran hasil kasir harian ke rekening Bank BCA"></textarea>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
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
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Rekening Penerima (Kas/Bank): <span class="text-danger">*</span></label>
                        <select name="target_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Kategori Pendapatan: <span class="text-danger">*</span></label>
                        <select name="revenue_account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($revenueAccounts as $ra): ?>
                                <option value="<?= $ra->id ?>">
                                    [<?= esc($ra->code) ?>] <?= esc($ra->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Nominal Pemasukan (Rp): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control font-weight-bold text-success form-control-lg tx-input" placeholder="0" required min="1000">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Deskripsi Pemasukan:</label>
                        <textarea name="description" class="form-control form-control-sm tx-input" rows="2" placeholder="Contoh: Pendapatan sewa kantin/lahan, bunga bank giro, koreksi saldo"></textarea>
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

<!-- ========================================================================= -->
<!-- MODAL SET SALDO AWAL KAS / BANK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalSaldoAwalKas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-teal text-white py-2">
                <h6 class="modal-title font-weight-bold">
                    <i class="fas fa-coins mr-1"></i> Atur / Sesuaikan Saldo Awal Rekening Kas &amp; Bank
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('keuangan/set-saldo-awal-kas') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="alert text-xs mb-3" style="background-color: var(--tx-head-bg); border: 1px solid var(--tx-border); color: var(--tx-text);">
                        <i class="fas fa-info-circle mr-1 text-teal"></i> <strong>Saldo Awal Pertama Kali:</strong>
                        Masukkan jumlah uang tunai di laci kasir atau saldo awal di rekening bank saat sistem pertama kali mulai dioperasikan.
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Pilih Rekening Kas / Bank: <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control form-control-sm font-weight-bold tx-input" data-native="true" required>
                            <?php foreach ($cashBankAccounts as $cba): ?>
                                <option value="<?= $cba->id ?>">
                                    [<?= esc($cba->code) ?>] <?= esc($cba->name) ?> (Saldo Saat Ini: Rp <?= number_format($cba->balance, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Nominal Saldo Awal Baru (Rp): <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="new_balance" class="form-control font-weight-bold text-teal form-control-lg tx-input" placeholder="0" required min="0">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold" style="color: var(--tx-text);">Keterangan / Catatan:</label>
                        <input type="text" name="notes" class="form-control form-control-sm tx-input" placeholder="Contoh: Modal Kas Awal Kasir / Saldo Awal Rekening Giro" value="Modal Awal Kas Operasional">
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
