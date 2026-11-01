<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Distributor Paper White / Paper Dark */
:root {
    --dist-bg: #ffffff;
    --dist-text: #1f2937;
    --dist-muted: #6b7280;
    --dist-border: #b8b8b8;
    --dist-card-bg: #ffffff;
    --dist-head-bg: #f8fafc;
    --dist-hover-bg: #f1f5f9;
    --dist-input-bg: #ffffff;
    --dist-box-bg: #f8fafc;
}

body.dark-mode {
    --dist-bg: #14171a;
    --dist-text: #f0f6fc;
    --dist-muted: #9ca3af;
    --dist-border: #374151;
    --dist-card-bg: #1f2328;
    --dist-head-bg: #181b1f;
    --dist-hover-bg: #272c33;
    --dist-input-bg: #181b1f;
    --dist-box-bg: #181b1f;
}

.dist-card {
    background-color: var(--dist-card-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    border-radius: 8px;
}

.dist-stat-box {
    background-color: var(--dist-box-bg) !important;
    border: 1px solid var(--dist-border) !important;
    border-radius: 8px;
    padding: 12px 14px;
}

.table-dist {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    background-color: var(--dist-card-bg) !important;
}

.table-dist th,
.table-dist td {
    border: 1px solid var(--dist-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-dist thead th {
    background-color: var(--dist-head-bg) !important;
    color: var(--dist-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-dist tbody tr:hover {
    background-color: var(--dist-hover-bg) !important;
}

.dist-input {
    background-color: var(--dist-input-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
}

.dist-input:focus {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25) !important;
}

.nav-tabs .nav-link {
    color: var(--dist-muted) !important;
    border-color: transparent !important;
}

.nav-tabs .nav-link.active {
    color: #4f46e5 !important;
    background-color: var(--dist-card-bg) !important;
    border-color: var(--dist-border) var(--dist-border) var(--dist-card-bg) !important;
    font-weight: 700;
}

body.dark-mode .nav-tabs .nav-link.active {
    color: #818cf8 !important;
}
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--dist-text);">
            <i class="fas fa-chart-line text-indigo mr-2"></i> Laporan Keuangan &amp; Operasional Distributor
        </h4>
        <small style="color: var(--dist-muted);">Analisis penjualan grosir, umur piutang, perputaran stok gudang, dan Laporan Laba Rugi (P&amp;L) mandiri</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                <i class="fas fa-cart-plus mr-1"></i> Kasir Grosir
            </a>
            <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm text-dark">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Piutang
            </a>
            <a href="<?= base_url('accounting/jurnal?scope=all') ?>" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm" title="Lihat Jurnal Umum Konsolidasian">
                <i class="fas fa-layer-group mr-1 text-warning"></i> Jurnal Konsolidasi
            </a>
        </div>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card dist-card mb-3">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('distributor/laporan') ?>">
            <input type="hidden" name="tab" value="<?= esc($tab) ?>">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">DARI TANGGAL</label>
                    <input type="date" class="form-control form-control-sm dist-input font-weight-bold" name="start_date" value="<?= esc($startDate) ?>">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">SAMPAI TANGGAL</label>
                    <input type="date" class="form-control form-control-sm dist-input font-weight-bold" name="end_date" value="<?= esc($endDate) ?>">
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-indigo btn-sm btn-block font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-filter mr-1"></i> Terapkan Periode
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- REPORT TABS -->
<div class="card dist-card mb-4">
    <div class="card-header p-0 pt-2 border-bottom" style="background-color: var(--dist-head-bg); border-color: var(--dist-border) !important;">
        <ul class="nav nav-tabs px-3" id="distributorReportTabs" role="tablist" style="border-bottom-color: var(--dist-border) !important;">
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'penjualan' ? 'active' : '' ?>" href="<?= base_url("distributor/laporan?tab=penjualan&start_date={$startDate}&end_date={$endDate}") ?>">
                    <i class="fas fa-receipt mr-1"></i> 1. Penjualan &amp; Rekap Grosir
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'piutang' ? 'active' : '' ?>" href="<?= base_url("distributor/laporan?tab=piutang&start_date={$startDate}&end_date={$endDate}") ?>">
                    <i class="fas fa-hourglass-half mr-1"></i> 2. Piutang &amp; Aging Schedule
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'stok' ? 'active' : '' ?>" href="<?= base_url("distributor/laporan?tab=stok&start_date={$startDate}&end_date={$endDate}") ?>">
                    <i class="fas fa-boxes-stacked mr-1"></i> 3. Persediaan &amp; Nilai Stok
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $tab === 'labarugi' ? 'active' : '' ?>" href="<?= base_url("distributor/laporan?tab=labarugi&start_date={$startDate}&end_date={$endDate}") ?>">
                    <i class="fas fa-chart-pie mr-1"></i> 4. Laba Rugi (P&amp;L) Distributor
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-3">
        
        <!-- TAB 1: PENJUALAN -->
        <?php if ($tab === 'penjualan'): ?>
            <div class="row mb-3">
                <div class="col-md-3 col-6 mb-2">
                    <div class="dist-stat-box">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-muted);">TOTAL PENJUALAN KOTOR:</span>
                        <h5 class="font-weight-bold text-indigo mb-0">Rp <?= number_format($salesSummary->total_gross ?? 0, 0, ',', '.') ?></h5>
                        <small style="color: var(--dist-muted);"><?= $salesSummary->total_tx ?? 0 ?> Transaksi</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="dist-stat-box">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-muted);">TOTAL DISKON:</span>
                        <h5 class="font-weight-bold text-danger mb-0">Rp <?= number_format($salesSummary->total_discount ?? 0, 0, ',', '.') ?></h5>
                        <small style="color: var(--dist-muted);">Diskon Khusus Grosir</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="dist-stat-box">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-muted);">DIBAYAR LUNAS (CASH/TF):</span>
                        <h5 class="font-weight-bold text-success mb-0">Rp <?= number_format($salesSummary->total_paid ?? 0, 0, ',', '.') ?></h5>
                        <small style="color: var(--dist-muted);">Kas Tunai &amp; Bank Masuk</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="dist-stat-box">
                        <span class="text-xs font-weight-bold d-block" style="color: var(--dist-muted);">SISA PIUTANG TEMPO:</span>
                        <h5 class="font-weight-bold text-warning mb-0">Rp <?= number_format($salesSummary->total_unpaid ?? 0, 0, ',', '.') ?></h5>
                        <small style="color: var(--dist-muted);">Menunggu Jatuh Tempo</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <h6 class="font-weight-bold mb-2" style="color: var(--dist-text);"><i class="fas fa-users text-indigo mr-1"></i> Rekap Penjualan per Pelanggan</h6>
                    <div class="table-responsive">
                        <table class="table-dist text-sm" id="tableSalesByCustomer">
                            <thead>
                                <tr>
                                    <th>Pelanggan</th>
                                    <th class="text-center" style="width: 80px;">Faktur</th>
                                    <th class="text-right">Total Penjualan</th>
                                    <th class="text-right">Sisa Piutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($salesByCustomer)): ?>
                                    <tr><td colspan="4" class="text-center py-3" style="color: var(--dist-muted);">Tidak ada data penjualan pada periode ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($salesByCustomer as $sc): ?>
                                        <tr>
                                            <td><strong><?= esc($sc->customer_name) ?></strong></td>
                                            <td class="text-center font-weight-bold"><?= $sc->total_tx ?></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($sc->total_amount, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-danger">Rp <?= number_format($sc->total_remaining, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <h6 class="font-weight-bold mb-2" style="color: var(--dist-text);"><i class="fas fa-pills text-teal mr-1"></i> Rekap Penjualan per Barang / Obat</h6>
                    <div class="table-responsive">
                        <table class="table-dist text-sm" id="tableSalesByMedicine">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th class="text-center" style="width: 90px;">Qty Terjual</th>
                                    <th class="text-right">Total Nilai Penjualan</th>
                                    <th class="text-right">Total HPP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($salesByMedicine)): ?>
                                    <tr><td colspan="4" class="text-center py-3" style="color: var(--dist-muted);">Tidak ada data penjualan barang pada periode ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($salesByMedicine as $sm): ?>
                                        <tr>
                                            <td><strong><?= esc($sm->med_name) ?></strong></td>
                                            <td class="text-center font-weight-bold text-indigo"><?= $sm->total_qty ?></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($sm->total_sales_val, 0, ',', '.') ?></td>
                                            <td class="text-right" style="color: var(--dist-muted);">Rp <?= number_format($sm->total_cogs_val, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <!-- TAB 2: PIUTANG & AGING -->
        <?php elseif ($tab === 'piutang'): ?>
            <h6 class="font-weight-bold mb-2" style="color: var(--dist-text);"><i class="fas fa-hourglass-half text-warning mr-1"></i> Daftar Piutang &amp; Umur Tagihan (Aging Schedule)</h6>
            <div class="table-responsive">
                <table class="table-dist text-sm" id="tableReportReceivables">
                    <thead>
                        <tr>
                            <th>No. Faktur</th>
                            <th>Pelanggan</th>
                            <th>Tgl Faktur</th>
                            <th>Jatuh Tempo</th>
                            <th>Umur Piutang</th>
                            <th class="text-right">Total Faktur</th>
                            <th class="text-right">Sudah Dibayar</th>
                            <th class="text-right">Sisa Tagihan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($receivablesList)): ?>
                            <tr><td colspan="9" class="text-center py-4" style="color: var(--dist-muted);">Semua piutang distributor telah lunas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($receivablesList as $rl): 
                                $today = strtotime(date('Y-m-d'));
                                $invDate = strtotime($rl->invoice_date);
                                $dueDate = strtotime($rl->due_date);
                                $ageDays = max(0, round(($today - $invDate) / (60 * 60 * 24)));
                                $isOver = ($dueDate < $today);
                            ?>
                                <tr>
                                    <td class="font-weight-bold text-indigo"><?= esc($rl->invoice_no) ?></td>
                                    <td><strong><?= esc($rl->customer_name) ?></strong></td>
                                    <td><?= date('d/m/Y', $invDate) ?></td>
                                    <td><span class="<?= $isOver ? 'text-danger font-weight-bold' : '' ?>"><?= date('d/m/Y', $dueDate) ?></span></td>
                                    <td><span class="badge <?= $ageDays > 60 ? 'badge-danger' : ($ageDays > 30 ? 'badge-warning' : 'badge-light border') ?>"><?= $ageDays ?> Hari</span></td>
                                    <td class="text-right">Rp <?= number_format($rl->total_receivable, 0, ',', '.') ?></td>
                                    <td class="text-right text-success">Rp <?= number_format($rl->total_paid, 0, ',', '.') ?></td>
                                    <td class="text-right font-weight-bold text-danger">Rp <?= number_format($rl->remaining_balance, 0, ',', '.') ?></td>
                                    <td class="text-center"><span class="badge badge-warning text-dark font-weight-bold"><?= $rl->status ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- TAB 3: STOK & NILAI PERSEDIAAN -->
        <?php elseif ($tab === 'stok'): ?>
            <h6 class="font-weight-bold mb-2" style="color: var(--dist-text);"><i class="fas fa-boxes-stacked text-teal mr-1"></i> Nilai Persediaan Stok Gudang Distributor</h6>
            <div class="table-responsive">
                <table class="table-dist text-sm" id="tableReportStock">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Batch</th>
                            <th>Exp Date</th>
                            <th class="text-right">Harga Beli (HPP)</th>
                            <th class="text-right">Harga Jual</th>
                            <th class="text-center">Sisa Stok</th>
                            <th class="text-right">Total Nilai Aset Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandStockVal = 0;
                        if (empty($stocksList)): 
                        ?>
                            <tr><td colspan="8" class="text-center py-4" style="color: var(--dist-muted);">Stok gudang distributor kosong.</td></tr>
                        <?php else: ?>
                            <?php foreach ($stocksList as $sl): 
                                $stkVal = $sl->stock * (float)$sl->buy_price;
                                $grandStockVal += $stkVal;
                            ?>
                                <tr>
                                    <td><?= esc($sl->med_code) ?></td>
                                    <td><strong><?= esc($sl->med_name) ?></strong></td>
                                    <td><?= esc($sl->batch_no ?: '-') ?></td>
                                    <td><?= $sl->expired_date ? date('d/m/Y', strtotime($sl->expired_date)) : '-' ?></td>
                                    <td class="text-right">Rp <?= number_format($sl->buy_price, 0, ',', '.') ?></td>
                                    <td class="text-right text-indigo">Rp <?= number_format($sl->selling_price, 0, ',', '.') ?></td>
                                    <td class="text-center font-weight-bold text-success"><?= $sl->stock ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($stkVal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="font-weight-bold" style="background-color: var(--dist-head-bg); font-size: 14px;">
                                <td colspan="7" class="text-right">TOTAL NILAI ASET PERSEDIAAN GUDANG DISTRIBUTOR:</td>
                                <td class="text-right text-success">Rp <?= number_format($grandStockVal, 0, ',', '.') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <!-- TAB 4: LABA RUGI (PROFIT & LOSS) -->
        <?php elseif ($tab === 'labarugi'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card dist-card shadow-sm mb-0">
                        <div class="card-header py-3 text-center" style="background-color: var(--dist-head-bg); border-color: var(--dist-border) !important;">
                            <h5 class="font-weight-bold m-0" style="color: var(--dist-text);">LAPORAN LABA RUGI (PROFIT &amp; LOSS)</h5>
                            <h6 class="font-weight-bold text-indigo mt-1 mb-0">UNIT BISNIS DISTRIBUTOR &amp; GROSIR</h6>
                            <small style="color: var(--dist-muted);">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></small>
                        </div>
                        <div class="card-body p-4">
                            <table class="table table-borderless table-sm mb-0" style="font-size: 14px; color: var(--dist-text);">
                                <!-- PENDAPATAN -->
                                <tr class="font-weight-bold text-indigo" style="border-bottom: 2px solid var(--dist-border);">
                                    <td colspan="2">1. PENDAPATAN OPERASIONAL DISTRIBUTOR</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Penjualan Kotor Grosir B2B (Akun 4-104)</td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($totalSalesVal, 0, ',', '.') ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="pl-4 text-danger">Retur Penjualan Grosir (Akun 4-304)</td>
                                    <td class="text-right text-danger font-weight-bold">(Rp <?= number_format($totalReturnsVal, 0, ',', '.') ?>)</td>
                                    <td></td>
                                </tr>
                                <tr class="font-weight-bold" style="background-color: var(--dist-head-bg);">
                                    <td class="pl-4">TOTAL PENDAPATAN BERSIH (NET REVENUE)</td>
                                    <td></td>
                                    <td class="text-right text-indigo font-weight-bold">Rp <?= number_format($netRevenue, 0, ',', '.') ?></td>
                                </tr>

                                <!-- HPP -->
                                <tr class="font-weight-bold text-danger mt-3" style="border-bottom: 2px solid var(--dist-border);">
                                    <td colspan="2" class="pt-3">2. HARGA POKOK PENJUALAN (HPP)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Beban Pokok Penjualan Obat Grosir (Akun 5-104)</td>
                                    <td class="text-right font-weight-bold text-danger">Rp <?= number_format($totalCogsVal, 0, ',', '.') ?></td>
                                    <td></td>
                                </tr>
                                <tr class="font-weight-bold" style="background-color: var(--dist-head-bg);">
                                    <td class="pl-4">TOTAL HARGA POKOK PENJUALAN (HPP)</td>
                                    <td></td>
                                    <td class="text-right text-danger font-weight-bold">(Rp <?= number_format($totalCogsVal, 0, ',', '.') ?>)</td>
                                </tr>

                                <!-- LABA KOTOR -->
                                <tr class="text-white font-weight-bold mt-3" style="background-color: #4f46e5; font-size: 15px;">
                                    <td class="py-2.5 pl-3">LABA KOTOR DISTRIBUTOR (GROSS PROFIT)</td>
                                    <td class="text-center py-2.5">Margin: <?= number_format($grossMarginPct, 1) ?>%</td>
                                    <td class="text-right py-2.5 pr-3">Rp <?= number_format($grossProfit, 0, ',', '.') ?></td>
                                </tr>
                            </table>

                            <div class="alert text-xs mt-3 mb-0" style="background-color: var(--dist-head-bg); border: 1px solid var(--dist-border); color: var(--dist-muted);">
                                <i class="fas fa-info-circle mr-1 text-indigo"></i>
                                Laporan ini terintegrasi penuh secara real-time dengan Jurnal Umum Holding dan Laporan Keuangan Neraca &amp; Konsolidasi Perusahaan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>
