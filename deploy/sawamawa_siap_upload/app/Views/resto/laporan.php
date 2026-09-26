<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-chart-pie text-teal mr-2"></i> Laporan & Analitik Penjualan Resto
                </h1>
                <small class="text-muted">Analisis performa omset, menu terlaris, dan rekap transaksi gizi & skincare</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= base_url('resto/display-antrean') ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm mr-2">
                    <i class="fas fa-tv mr-1 text-teal"></i> Buka Display TV
                </a>
                <a href="<?= base_url('resto/pos') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-cash-register mr-1"></i> Buka Kasir POS
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- FILTER & ACTION BAR -->
        <div class="card card-outline card-teal mb-4 shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-body p-3 bg-white">
                <form method="GET" action="<?= base_url('resto/laporan') ?>" class="form-row align-items-end">
                    <div class="col-md-3">
                        <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>" required>
                    </div>
                    <div class="col-md-6 text-right mt-3 mt-md-0">
                        <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-3">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="<?= base_url('resto/laporan') ?>" class="btn btn-outline-secondary btn-sm px-3 ml-1">
                            <i class="fas fa-sync mr-1"></i> Reset
                        </a>
                        <a href="<?= base_url('resto/cetak-laporan?start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold px-3 ml-2">
                            <i class="fas fa-print mr-1"></i> Cetak Rekap Laporan
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4 KPI SUMMARY CARDS -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-left-teal h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Total Omset Penjualan</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
                                <small class="text-muted">Periode terpilih</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-left-info h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-info text-uppercase">Total Pesanan Selesai</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= number_format($totalOrders, 0, ',', '.') ?> Transaksi</h4>
                                <small class="text-muted">Paid & Billed to clinic</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-receipt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-left-success h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Rata-rata Transaksi (Basket)</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($avgBasket, 0, ',', '.') ?></h4>
                                <small class="text-muted">Per transaksi pelanggan</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-basket-shopping fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm border-left-warning h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-warning text-uppercase">Total Porsi & Item Terjual</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= number_format($totalItemsSold, 0, ',', '.') ?> Item</h4>
                                <small class="text-muted">Makanan, jus & skincare</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-warning">
                                <i class="fas fa-boxes-stacked fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS & BEST SELLERS -->
        <div class="row mb-4">
            
            <!-- Daily Sales Trend Chart -->
            <div class="col-md-8 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-chart-line text-teal mr-2"></i> Tren Omset Penjualan Harian
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <canvas id="salesTrendChart" style="min-height: 260px; max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown Pie Chart -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-chart-pie text-teal mr-2"></i> Kontribusi Omset Kategori
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <canvas id="categoryChart" style="min-height: 260px; max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- TOP SELLING PRODUCTS LEADERBOARD -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-trophy text-warning mr-2"></i> Produk & Menu Paling Laris (Top Sellers)
                        </h6>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Kode & Nama Item</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Total Terjual</th>
                                    <th class="text-right">Total Pendapatan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topItems)): ?>
                                    <?php $rank = 1; foreach ($topItems as $item): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold">
                                                <?php if ($rank === 1): ?>
                                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-crown"></i> 1</span>
                                                <?php elseif ($rank === 2): ?>
                                                    <span class="badge badge-secondary px-2 py-1">2</span>
                                                <?php elseif ($rank === 3): ?>
                                                    <span class="badge badge-bronze px-2 py-1 bg-amber" style="background:#d97706; color:#fff;">3</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border"><?= $rank ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                <?= esc($item->name) ?>
                                                <small class="text-muted d-block"><?= esc($item->code) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    if ($item->category === 'makanan') echo 'success';
                                                    elseif ($item->category === 'minuman') echo 'info';
                                                    elseif ($item->category === 'diet_gizi') echo 'warning text-dark';
                                                    elseif ($item->category === 'skincare') echo 'pink bg-purple text-white';
                                                    else echo 'secondary';
                                                ?> text-capitalize font-weight-bold">
                                                    <?= esc(str_replace('_', ' ', $item->category)) ?>
                                                </span>
                                            </td>
                                            <td class="text-center font-weight-bold text-teal">
                                                <?= number_format($item->total_qty, 0, ',', '.') ?> Item
                                            </td>
                                            <td class="text-right font-weight-bold text-dark">
                                                Rp <?= number_format($item->total_omset, 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php $rank++; endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data penjualan pada periode ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILED TRANSACTION TABLE -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm bg-white" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-list text-teal mr-2"></i> Rincian Seluruh Transaksi Penjualan Resto
                        </h6>
                        <span class="badge badge-teal font-weight-bold"><?= count($ordersList) ?> Transaksi</span>
                    </div>
                    <div class="card-body p-3">
                        <table class="table table-bordered table-hover datatable" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal & Jam</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tipe Pesanan</th>
                                    <th>Metode Bayar</th>
                                    <th>Kasir</th>
                                    <th class="text-right">Total Tagihan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ordersList as $ord): ?>
                                    <tr>
                                        <td class="font-weight-bold text-teal"><?= esc($ord->order_no) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($ord->created_at)) ?></td>
                                        <td>
                                            <strong><?= esc($ord->customer_name) ?></strong>
                                            <?php if ($ord->diet_instructions): ?>
                                                <small class="text-muted d-block"><?= esc(mb_strimwidth($ord->diet_instructions, 0, 30, '...')) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $ord->order_type === 'diet_pasien' ? 'warning text-dark' : 'light border' ?> text-capitalize font-weight-bold">
                                                <?= esc(str_replace('_', ' ', $ord->order_type)) ?>
                                            </span>
                                        </td>
                                        <td class="text-uppercase font-weight-bold" style="font-size: 11px;">
                                            <?= esc($ord->payment_method ?: 'tunai') ?>
                                        </td>
                                        <td><?= esc($ord->cashier_name ?: 'Kasir Utama') ?></td>
                                        <td class="text-right font-weight-bold text-dark">
                                            Rp <?= number_format($ord->grand_total, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('resto/cetak-nota/' . $ord->id) ?>" target="_blank" class="btn btn-xs btn-outline-dark font-weight-bold" title="Cetak Ulang Struk">
                                                <i class="fas fa-print"></i> Struk
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
</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Daily Sales Trend Data
        const trendData = <?= json_encode($dailyTrends) ?>;
        const trendLabels = trendData.map(d => d.sale_date);
        const trendRevenues = trendData.map(d => parseFloat(d.daily_revenue));

        const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels.length > 0 ? trendLabels : ['Hari Ini'],
                datasets: [{
                    label: 'Omset Penjualan (Rp)',
                    data: trendRevenues.length > 0 ? trendRevenues : [0],
                    backgroundColor: 'rgba(13, 148, 136, 0.15)',
                    borderColor: '#0d9488',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#0d9488',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Category Breakdown Data
        const catData = <?= json_encode($categoryBreakdown) ?>;
        const catLabels = catData.map(c => c.category.toUpperCase().replace('_', ' '));
        const catAmounts = catData.map(c => parseFloat(c.total_amount));
        const catColors = ['#10b981', '#06b6d4', '#f59e0b', '#8b5cf6', '#64748b'];

        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length > 0 ? catLabels : ['Belum Ada Data'],
                datasets: [{
                    data: catAmounts.length > 0 ? catAmounts : [1],
                    backgroundColor: catColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
