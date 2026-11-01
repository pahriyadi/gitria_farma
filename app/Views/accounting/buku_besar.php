<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Buku Besar Paper White / Paper Dark */
:root {
    --acc-bg: #ffffff;
    --acc-text: #1f2937;
    --acc-muted: #6b7280;
    --acc-border: #b8b8b8;
    --acc-card-bg: #ffffff;
    --acc-head-bg: #f8fafc;
    --acc-hover-bg: #f1f5f9;
    --acc-subhead-bg: #f1f5f9;
    --acc-input-bg: #ffffff;
}

body.dark-mode {
    --acc-bg: #14171a;
    --acc-text: #f0f6fc;
    --acc-muted: #9ca3af;
    --acc-border: #374151;
    --acc-card-bg: #1f2328;
    --acc-head-bg: #181b1f;
    --acc-hover-bg: #272c33;
    --acc-subhead-bg: #21262d;
    --acc-input-bg: #181b1f;
}

.acc-card {
    background-color: var(--acc-card-bg) !important;
    border: 1px solid var(--acc-border) !important;
    color: var(--acc-text) !important;
    border-radius: 8px;
}

.table-buku-besar {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--acc-border) !important;
    color: var(--acc-text) !important;
    background-color: var(--acc-card-bg) !important;
}

.table-buku-besar th,
.table-buku-besar td {
    border: 1px solid var(--acc-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-buku-besar thead th {
    background-color: var(--acc-head-bg) !important;
    color: var(--acc-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-buku-besar tbody tr:hover {
    background-color: var(--acc-hover-bg) !important;
}

.running-balance-row td {
    background-color: var(--acc-subhead-bg) !important;
    color: var(--acc-text) !important;
    font-weight: 700;
}

.acc-input {
    background-color: var(--acc-input-bg) !important;
    border: 1px solid var(--acc-border) !important;
    color: var(--acc-text) !important;
}

.acc-input:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25) !important;
}
</style>

<!-- Quick Switcher Tabs -->
<div class="mb-3">
    <div class="accounting-tabs-scroll-wrapper">
        <ul class="nav nav-tabs-modern">
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/jurnal') ?>">
                    <i class="fas fa-file-lines mr-1"></i> 1. Jurnal Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" href="<?= base_url('accounting/buku-besar') ?>">
                    <i class="fas fa-book-journal-whills mr-1"></i> 2. Buku Mutasi Akun
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/laporan') ?>">
                    <i class="fas fa-chart-pie mr-1"></i> 3. Laporan Keuangan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="<?= base_url('accounting/coa') ?>">
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
</div>

<!-- Filter Periode & Pilihan Rekening Akun -->
<div class="card acc-card p-3 mb-3 shadow-sm">
    <form method="get" action="<?= base_url('accounting/buku-besar') ?>" class="row align-items-end">
        <div class="col-md-5 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold mb-1" style="color: var(--acc-text);"><i class="fas fa-folder-tree text-teal mr-1"></i> Pilih Rekening Akun (Jurnal Khusus Akun):</label>
            <select name="account_id" class="form-control form-control-sm font-weight-bold acc-input" data-native="true" onchange="this.form.submit()">
                <?php foreach ($accounts as $acc): ?>
                    <option value="<?= $acc->id ?>" <?= $selectedAccount && $selectedAccount->id == $acc->id ? 'selected' : '' ?>>
                        <?= esc($acc->code) ?> - <?= esc($acc->name) ?> (<?= strtoupper(esc($acc->type)) ?>) &bull; Saldo: Rp <?= number_format($acc->balance, 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold mb-1" style="color: var(--acc-text);"><i class="fas fa-calendar-day text-teal mr-1"></i> Dari Tanggal:</label>
            <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold acc-input" value="<?= esc($startDate) ?>">
        </div>
        <div class="col-md-2 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold mb-1" style="color: var(--acc-text);"><i class="fas fa-calendar-check text-teal mr-1"></i> Sampai Tanggal:</label>
            <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold acc-input" value="<?= esc($endDate) ?>">
        </div>
        <div class="col-md-2 form-group mb-2 mb-md-0">
            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold shadow-sm">
                <i class="fas fa-filter mr-1"></i> Filter Mutasi
            </button>
        </div>
    </form>
</div>

<!-- Card Header Info Akun & Ringkasan Saldo -->
<?php if ($selectedAccount): ?>
<div class="card acc-card p-3 mb-3 shadow-sm" style="border-left: 5px solid #0d9f4f !important;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <span class="badge badge-teal font-monospace px-2 py-1" style="font-size: 13px;"><?= esc($selectedAccount->code) ?></span>
            <h5 class="font-weight-bold d-inline ml-2" style="color: var(--acc-text);"><?= esc($selectedAccount->name) ?></h5>
            <div class="mt-1" style="font-size: 12.5px; color: var(--acc-muted);">
                Tipe Akun: <strong><?= strtoupper(esc($selectedAccount->type)) ?></strong> &bull; 
                Saldo Normal: <strong class="badge badge-light border"><?= strtoupper(esc($selectedAccount->normal_balance)) ?></strong>
            </div>
        </div>
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <div class="d-inline-block text-left mr-4 pr-4" style="border-right: 1px solid var(--acc-border);">
                <span class="text-xs d-block font-weight-bold" style="color: var(--acc-muted);">SALDO AWAL (<?= date('d/m/Y', strtotime($startDate)) ?>)</span>
                <h6 class="font-weight-bold mb-0 font-monospace" style="color: var(--acc-muted);">Rp <?= number_format($openingBalance, 2, ',', '.') ?></h6>
            </div>
            <div class="d-inline-block text-left">
                <span class="text-xs text-teal d-block font-weight-bold">SISA SALDO AKHIR</span>
                <h5 class="font-weight-bold text-teal mb-0 font-monospace">Rp <?= number_format($closingBalance, 2, ',', '.') ?></h5>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabel Mutasi Buku Besar dengan Sisa Saldo Berjalan (Running Balance) -->
<div class="card acc-card p-0 shadow-sm mb-4">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center p-3" style="background-color: var(--acc-head-bg); border-color: var(--acc-border) !important;">
        <h6 class="font-weight-bold mb-0" style="color: var(--acc-text);">
            <i class="fas fa-book-journal-whills text-teal mr-1"></i> Rincian Jurnal &amp; Sisa Saldo Berjalan (Running Balance)
        </h6>
        <span class="badge badge-light border text-xs font-weight-bold">
            Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-buku-besar mb-0 text-sm" id="tableBukuBesar">
                <thead>
                    <tr>
                        <th style="width: 95px;" class="text-center">Tanggal</th>
                        <th style="width: 140px;">No. Jurnal</th>
                        <th style="width: 140px;">Sumber Modul</th>
                        <th>Keterangan / Uraian Transaksi</th>
                        <th style="width: 140px;" class="text-right text-success"><i class="fas fa-arrow-circle-down mr-1"></i>Debet (Masuk)</th>
                        <th style="width: 140px;" class="text-right text-danger"><i class="fas fa-arrow-circle-up mr-1"></i>Kredit (Keluar)</th>
                        <th style="width: 150px;" class="text-right text-teal font-weight-bold">Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris Pertama Tepat Setelah Header: Saldo Awal Akumulasi Dinamis -->
                    <tr class="running-balance-row">
                        <td class="text-center font-monospace text-xs"><?= date('d/m/Y', strtotime($startDate)) ?></td>
                        <td class="text-center font-monospace" style="color: var(--acc-muted);">-</td>
                        <td><span class="badge badge-secondary border text-xs"><i class="fas fa-history mr-1"></i> Saldo Awal</span></td>
                        <td class="font-weight-bold">
                            <i class="fas fa-play text-teal mr-1"></i> Saldo Awal Pembukuan per <?= date('d F Y', strtotime($startDate)) ?> 
                            <small class="font-weight-normal" style="color: var(--acc-muted);">(Akumulasi Saldo Bulan Sebelumnya)</small>
                        </td>
                        <td class="text-right font-monospace" style="color: var(--acc-muted);">-</td>
                        <td class="text-right font-monospace" style="color: var(--acc-muted);">-</td>
                        <td class="text-right font-monospace text-teal font-weight-bold" style="font-size: 13.5px;">
                            Rp <?= number_format($openingBalance, 2, ',', '.') ?>
                        </td>
                    </tr>

                    <?php 
                    $totalD = 0; 
                    $totalC = 0; 
                    ?>
                    <?php if (!empty($mutations)): ?>
                        <?php foreach ($mutations as $m): ?>
                            <?php 
                            $totalD += (float)$m->debit; 
                            $totalC += (float)$m->credit; 
                            ?>
                            <tr>
                                <td class="font-monospace text-xs text-center">
                                    <span class="font-weight-bold" style="color: var(--acc-text);"><?= date('d/m/Y', strtotime($m->entry_date)) ?></span>
                                    <?php if (!empty($m->created_at)): ?>
                                        <small class="d-block text-xxs mt-1" style="color: var(--acc-muted);"><i class="far fa-clock text-teal mr-1"></i><?= date('H:i:s', strtotime($m->created_at)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace font-weight-bold text-teal"><?= esc($m->journal_no) ?></td>
                                <td><span class="badge badge-light border text-xs"><?= esc($m->source_module) ?></span></td>
                                <td><?= esc($m->journal_desc) ?></td>
                                <td class="text-right font-monospace font-weight-bold <?= $m->debit > 0 ? 'text-success' : '' ?>" style="<?= $m->debit <= 0 ? 'color: var(--acc-muted);' : '' ?>">
                                    <?= $m->debit > 0 ? 'Rp ' . number_format($m->debit, 2, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-monospace font-weight-bold <?= $m->credit > 0 ? 'text-danger' : '' ?>" style="<?= $m->credit <= 0 ? 'color: var(--acc-muted);' : '' ?>">
                                    <?= $m->credit > 0 ? 'Rp ' . number_format($m->credit, 2, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-monospace font-weight-bold text-teal">
                                    Rp <?= number_format($m->running_balance, 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color: var(--acc-muted);">
                                <i class="fas fa-inbox fa-2x mb-2 d-block" style="color: var(--acc-muted);"></i>
                                Belum ada mutasi transaksi jurnal pada rekening akun ini untuk rentang periode yang dipilih.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold" style="background-color: var(--acc-head-bg); font-size: 13.5px;">
                        <td colspan="4" class="text-right">TOTAL MUTASI &amp; SALDO AKHIR:</td>
                        <td class="text-right font-monospace text-success">Rp <?= number_format($totalD, 2, ',', '.') ?></td>
                        <td class="text-right font-monospace text-danger">Rp <?= number_format($totalC, 2, ',', '.') ?></td>
                        <td class="text-right font-monospace text-teal font-weight-bold" style="font-size: 14px;">
                            Rp <?= number_format($closingBalance, 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
