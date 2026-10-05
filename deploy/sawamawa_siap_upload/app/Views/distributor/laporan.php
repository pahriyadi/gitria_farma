<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-chart-line text-indigo mr-2"></i> Laporan Keuangan &amp; Operasional Distributor
                </h4>
                <small class="text-muted">Analisis penjualan grosir, umur piutang, perputaran stok gudang, dan Laporan Laba Rugi (P&amp;L) mandiri</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-cart-plus mr-1"></i> Kasir Grosir
                    </a>
                    <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Piutang
                    </a>
                    <a href="<?= base_url('accounting/jurnal') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-book mr-1"></i> Jurnal Umum Holding
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- FILTER CARD -->
        <div class="card card-outline card-indigo shadow-none border mb-3">
            <div class="card-body p-3">
                <form method="get" action="<?= base_url('distributor/laporan') ?>">
                    <input type="hidden" name="tab" value="<?= esc($tab) ?>">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">DARI TANGGAL</label>
                            <input type="date" class="form-control form-control-sm" name="start_date" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="text-xs font-weight-bold text-muted mb-1">SAMPAI TANGGAL</label>
                            <input type="date" class="form-control form-control-sm" name="end_date" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-indigo btn-sm btn-block font-weight-bold" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                                <i class="fas fa-filter mr-1"></i> Terapkan Periode
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- REPORT TABS -->
        <div class="card card-outline card-indigo card-tabs shadow-none border">
            <div class="card-header p-0 pt-1 border-bottom-0 bg-white">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'penjualan' ? 'active font-weight-bold text-indigo' : 'text-muted' ?>" href="<?= base_url("distributor/laporan?tab=penjualan&start_date={$startDate}&end_date={$endDate}") ?>">
                            <i class="fas fa-receipt mr-1"></i> 1. Penjualan &amp; Rekap Grosir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'piutang' ? 'active font-weight-bold text-indigo' : 'text-muted' ?>" href="<?= base_url("distributor/laporan?tab=piutang&start_date={$startDate}&end_date={$endDate}") ?>">
                            <i class="fas fa-hourglass-half mr-1"></i> 2. Piutang &amp; Aging Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'stok' ? 'active font-weight-bold text-indigo' : 'text-muted' ?>" href="<?= base_url("distributor/laporan?tab=stok&start_date={$startDate}&end_date={$endDate}") ?>">
                            <i class="fas fa-boxes-stacked mr-1"></i> 3. Persediaan &amp; Nilai Stok
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'labarugi' ? 'active font-weight-bold text-indigo' : 'text-muted' ?>" href="<?= base_url("distributor/laporan?tab=labarugi&start_date={$startDate}&end_date={$endDate}") ?>">
                            <i class="fas fa-chart-pie mr-1"></i> 4. Laba Rugi (P&amp;L) Distributor
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3">
                
                <!-- TAB 1: PENJUALAN -->
                <?php if ($tab === 'penjualan'): ?>
                    <div class="row mb-3">
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-muted text-xs font-weight-bold d-block">TOTAL PENJUALAN KOTOR:</span>
                                <h5 class="font-weight-bold text-indigo mb-0">Rp <?= number_format($salesSummary->total_gross ?? 0, 0, ',', '.') ?></h5>
                                <small class="text-muted"><?= $salesSummary->total_tx ?? 0 ?> Transaksi</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-muted text-xs font-weight-bold d-block">TOTAL DISKON:</span>
                                <h5 class="font-weight-bold text-danger mb-0">Rp <?= number_format($salesSummary->total_discount ?? 0, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-muted text-xs font-weight-bold d-block">DIBAYAR LUNAS (CASH/TF):</span>
                                <h5 class="font-weight-bold text-success mb-0">Rp <?= number_format($salesSummary->total_paid ?? 0, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded border">
                                <span class="text-muted text-xs font-weight-bold d-block">SISA PIUTANG TEMPO:</span>
                                <h5 class="font-weight-bold text-warning mb-0">Rp <?= number_format($salesSummary->total_unpaid ?? 0, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-users text-indigo mr-1"></i> Rekap Penjualan per Pelanggan</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm text-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Pelanggan</th>
                                            <th class="text-center">Faktur</th>
                                            <th class="text-right">Total Penjualan</th>
                                            <th class="text-right">Sisa Piutang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($salesByCustomer)): ?>
                                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>
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

                        <div class="col-lg-6">
                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-pills text-teal mr-1"></i> Rekap Penjualan per Barang / Obat</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm text-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nama Obat</th>
                                            <th class="text-center">Qty Terjual</th>
                                            <th class="text-right">Total Nilai Penjualan</th>
                                            <th class="text-right">Total HPP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($salesByMedicine)): ?>
                                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($salesByMedicine as $sm): ?>
                                                <tr>
                                                    <td><strong><?= esc($sm->med_name) ?></strong></td>
                                                    <td class="text-center font-weight-bold text-indigo"><?= $sm->total_qty ?></td>
                                                    <td class="text-right font-weight-bold">Rp <?= number_format($sm->total_sales_val, 0, ',', '.') ?></td>
                                                    <td class="text-right text-muted">Rp <?= number_format($sm->total_cogs_val, 0, ',', '.') ?></td>
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
                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-hourglass-half text-warning mr-1"></i> Daftar Piutang &amp; Umur Tagihan (Aging Schedule)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm text-sm">
                            <thead class="bg-light">
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
                                    <tr><td colspan="9" class="text-center text-muted py-4">Semua piutang distributor telah lunas.</td></tr>
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
                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-boxes-stacked text-teal mr-1"></i> Nilai Persediaan Stok Gudang Distributor</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-sm">
                            <thead class="bg-light">
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
                                    <tr><td colspan="8" class="text-center text-muted py-4">Stok kosong.</td></tr>
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
                                    <tr class="bg-light font-weight-bold" style="font-size: 15px;">
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
                            <div class="card shadow-none border">
                                <div class="card-header bg-white py-3 text-center">
                                    <h5 class="font-weight-bold text-dark m-0">LAPORAN LABA RUGI (PROFIT &amp; LOSS)</h5>
                                    <h6 class="font-weight-bold text-indigo mt-1 mb-0">UNIT BISNIS DISTRIBUTOR &amp; GROSIR</h6>
                                    <small class="text-muted">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></small>
                                </div>
                                <div class="card-body p-4">
                                    <table class="table table-borderless table-sm" style="font-size: 14px;">
                                        <!-- PENDAPATAN -->
                                        <tr class="border-bottom font-weight-bold text-indigo">
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
                                        <tr class="font-weight-bold bg-light">
                                            <td class="pl-4">TOTAL PENDAPATAN BERSIH (NET REVENUE)</td>
                                            <td></td>
                                            <td class="text-right text-indigo font-weight-bold">Rp <?= number_format($netRevenue, 0, ',', '.') ?></td>
                                        </tr>

                                        <!-- HPP -->
                                        <tr class="border-bottom font-weight-bold text-danger mt-3">
                                            <td colspan="2" class="pt-3">2. HARGA POKOK PENJUALAN (HPP)</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="pl-4">Beban Pokok Penjualan Obat Grosir (Akun 5-104)</td>
                                            <td class="text-right font-weight-bold text-danger">Rp <?= number_format($totalCogsVal, 0, ',', '.') ?></td>
                                            <td></td>
                                        </tr>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="pl-4">TOTAL HARGA POKOK PENJUALAN (HPP)</td>
                                            <td></td>
                                            <td class="text-right text-danger font-weight-bold">(Rp <?= number_format($totalCogsVal, 0, ',', '.') ?>)</td>
                                        </tr>

                                        <!-- LABA KOTOR -->
                                        <tr class="bg-indigo text-white font-weight-bold mt-3" style="background-color: #4f46e5; font-size: 16px;">
                                            <td class="py-2.5 pl-3">LABA KOTOR DISTRIBUTOR (GROSS PROFIT)</td>
                                            <td class="text-center py-2.5">Margin: <?= number_format($grossMarginPct, 1) ?>%</td>
                                            <td class="text-right py-2.5 pr-3">Rp <?= number_format($grossProfit, 0, ',', '.') ?></td>
                                        </tr>
                                    </table>

                                    <div class="alert alert-info text-xs mt-3 mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Laporan ini terintegrasi penuh secara real-time dengan Jurnal Umum Holding dan Laporan Keuangan Neraca &amp; Konsolidasi Perusahaan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
