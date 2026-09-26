<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Quick Switcher Tabs -->
<div class="mb-3 d-flex align-items-center justify-content-between">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/jurnal') ?>">
                <i class="fas fa-file-lines mr-1"></i> 1. Jurnal Umum Konsolidasian
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" href="<?= base_url('accounting/buku-besar') ?>">
                <i class="fas fa-book-journal-whills mr-1"></i> 2. Jurnal Mutasi per Akun (Buku Pembantu & Saldo Berjalan)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/laporan') ?>">
                <i class="fas fa-chart-pie mr-1"></i> 3. Laporan Keuangan
            </a>
        </li>
    </ul>
</div>

<!-- Filter Periode & Pilihan Rekening Akun -->
<div class="card p-3 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <form method="get" action="<?= base_url('accounting/buku-besar') ?>" class="row align-items-end">
        <div class="col-md-5 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-folder-tree text-teal mr-1"></i> Pilih Rekening Akun (Jurnal Khusus Akun):</label>
            <select name="account_id" class="form-control form-control-sm font-weight-bold" onchange="this.form.submit()">
                <?php foreach ($accounts as $acc): ?>
                    <option value="<?= $acc->id ?>" <?= $selectedAccount && $selectedAccount->id == $acc->id ? 'selected' : '' ?>>
                        <?= esc($acc->code) ?> - <?= esc($acc->name) ?> (<?= strtoupper(esc($acc->type)) ?>) &bull; Saldo: Rp <?= number_format($acc->balance, 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-day text-teal mr-1"></i> Dari Tanggal:</label>
            <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
        </div>
        <div class="col-md-2 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-check text-teal mr-1"></i> Sampai Tanggal:</label>
            <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
        </div>
        <div class="col-md-2 form-group mb-2 mb-md-0">
            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold">
                <i class="fas fa-filter mr-1"></i> Filter Mutasi
            </button>
        </div>
    </form>
</div>

<!-- Card Header Info Akun & Ringkasan Saldo -->
<?php if ($selectedAccount): ?>
<div class="card p-3 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8; border-left: 5px solid #0d9f4f;">
    <div class="row align-items-center">
        <div class="col-md-6">
            <span class="badge badge-teal font-monospace px-2 py-1" style="font-size: 13px;"><?= esc($selectedAccount->code) ?></span>
            <h5 class="font-weight-bold text-dark d-inline ml-2"><?= esc($selectedAccount->name) ?></h5>
            <div class="text-secondary mt-1" style="font-size: 12.5px;">
                Tipe Akun: <strong><?= strtoupper(esc($selectedAccount->type)) ?></strong> &bull; 
                Saldo Normal: <strong class="badge badge-light border"><?= strtoupper(esc($selectedAccount->normal_balance)) ?></strong>
            </div>
        </div>
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <div class="d-inline-block text-left mr-4 border-right pr-4">
                <span class="text-xs text-muted d-block font-weight-bold">SALDO AWAL (<?= date('d/m/Y', strtotime($startDate)) ?>)</span>
                <h6 class="font-weight-bold text-secondary mb-0 font-monospace">Rp <?= number_format($openingBalance, 2, ',', '.') ?></h6>
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
<div class="card p-0 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
        <h6 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-book-journal-whills text-teal mr-1"></i> Rincian Jurnal & Sisa Saldo Berjalan (Running Balance)
        </h6>
        <span class="badge badge-light border text-xs font-weight-bold">
            Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0 text-dark" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 95px;" class="text-center">Tanggal</th>
                        <th style="width: 140px;">No. Jurnal</th>
                        <th style="width: 130px;">Sumber Modul</th>
                        <th>Keterangan / Uraian Transaksi</th>
                        <th style="width: 135px;" class="text-right text-success"><i class="fas fa-arrow-circle-down mr-1"></i>Debet (Masuk)</th>
                        <th style="width: 135px;" class="text-right text-danger"><i class="fas fa-arrow-circle-up mr-1"></i>Kredit (Keluar)</th>
                        <th style="width: 150px;" class="text-right bg-light text-teal font-weight-bold">Sisa Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris Pertama Tepat Setelah Header: Saldo Awal Akumulasi Dinamis -->
                    <tr class="table-light font-weight-bold" style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                        <td class="text-center font-monospace text-xs"><?= date('d/m/Y', strtotime($startDate)) ?></td>
                        <td class="text-center font-monospace text-muted">-</td>
                        <td><span class="badge badge-secondary border text-xs"><i class="fas fa-history mr-1"></i> Saldo Awal</span></td>
                        <td class="font-weight-bold text-dark">
                            <i class="fas fa-play text-teal mr-1"></i> Saldo Awal Pembukuan per <?= date('d F Y', strtotime($startDate)) ?> 
                            <small class="text-muted font-weight-normal">(Akumulasi Saldo Bulan Sebelumnya)</small>
                        </td>
                        <td class="text-right font-monospace text-muted">-</td>
                        <td class="text-right font-monospace text-muted">-</td>
                        <td class="text-right font-monospace text-teal font-weight-bold bg-light" style="font-size: 13.5px;">
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
                                <td class="font-monospace text-xs text-center"><?= date('d/m/Y', strtotime($m->entry_date)) ?></td>
                                <td class="font-monospace font-weight-bold text-teal"><?= esc($m->journal_no) ?></td>
                                <td><span class="badge badge-light border text-xs"><?= esc($m->source_module) ?></span></td>
                                <td><?= esc($m->journal_desc) ?></td>
                                <td class="text-right font-monospace font-weight-bold <?= $m->debit > 0 ? 'text-success' : 'text-muted' ?>">
                                    <?= $m->debit > 0 ? 'Rp ' . number_format($m->debit, 2, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-monospace font-weight-bold <?= $m->credit > 0 ? 'text-danger' : 'text-muted' ?>">
                                    <?= $m->credit > 0 ? 'Rp ' . number_format($m->credit, 2, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-monospace font-weight-bold text-teal bg-light">
                                    Rp <?= number_format($m->running_balance, 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block text-muted"></i>
                                Belum ada mutasi transaksi jurnal pada rekening akun ini untuk rentang periode yang dipilih.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light" style="font-size: 13.5px;">
                        <td colspan="4" class="text-right">TOTAL MUTASI & SALDO AKHIR:</td>
                        <td class="text-right font-monospace text-success">Rp <?= number_format($totalD, 2, ',', '.') ?></td>
                        <td class="text-right font-monospace text-danger">Rp <?= number_format($totalC, 2, ',', '.') ?></td>
                        <td class="text-right font-monospace text-teal font-weight-bold bg-light" style="font-size: 14px;">
                            Rp <?= number_format($closingBalance, 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
