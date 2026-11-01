<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark */
    :root {
        --rl-canvas-bg: #f4f6f9;
        --rl-card-bg: #ffffff;
        --rl-card-header: #ffffff;
        --rl-border: #b8b8b8;
        --rl-border-td: #c8c8c8;
        --rl-border-th: #b0b0b0;
        --rl-text: #212529;
        --rl-text-muted: #6c757d;
        --rl-th-bg: #f8fafc;
        --rl-hover-bg: #f0f7f3;
        --rl-box-bg: #f8fafc;
        --rl-input-bg: #ffffff;
    }

    body.dark-mode {
        --rl-canvas-bg: #14171a;
        --rl-card-bg: #1f2328;
        --rl-card-header: #1f2328;
        --rl-border: #374151;
        --rl-border-td: #374151;
        --rl-border-th: #374151;
        --rl-text: #f0f6fc;
        --rl-text-muted: #9ca3af;
        --rl-th-bg: #181b1f;
        --rl-hover-bg: #262c36;
        --rl-box-bg: #181b1f;
        --rl-input-bg: #181b1f;
    }

    /* Container Card Styling */
    .rl-card {
        background-color: var(--rl-card-bg) !important;
        border: 1px solid var(--rl-border) !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .rl-card .card-header {
        background-color: var(--rl-card-header) !important;
        border-bottom: 1px solid var(--rl-border) !important;
        color: var(--rl-text) !important;
    }

    .rl-card .card-body {
        background-color: var(--rl-card-bg) !important;
        color: var(--rl-text) !important;
    }

    /* KPI Summary Stat Box */
    .rl-stat-box {
        background-color: var(--rl-card-bg);
        border: 1px solid var(--rl-border);
        border-radius: 4px;
        padding: 16px;
        height: 100%;
        transition: border-color 0.2s ease;
    }

    .rl-stat-box.border-left-teal {
        border-left: 4px solid #0d9488 !important;
    }
    .rl-stat-box.border-left-info {
        border-left: 4px solid #0284c7 !important;
    }
    .rl-stat-box.border-left-success {
        border-left: 4px solid #16a34a !important;
    }
    .rl-stat-box.border-left-warning {
        border-left: 4px solid #d97706 !important;
    }

    .rl-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--rl-box-bg);
        border: 1px solid var(--rl-border);
        flex-shrink: 0;
    }

    /* Strict Table Styling (desain_tabel.md) */
    .table-resto {
        border-collapse: collapse !important;
        width: 100%;
        margin-bottom: 0;
        color: var(--rl-text) !important;
    }

    .table-resto th,
    .table-resto td {
        border: 1px solid var(--rl-border) !important;
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table-resto thead th {
        background-color: var(--rl-th-bg) !important;
        color: var(--rl-text) !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid var(--rl-border) !important;
    }

    .table-resto tbody tr {
        background-color: var(--rl-card-bg) !important;
        transition: background-color 0.15s ease;
    }

    .table-resto tbody tr:hover {
        background-color: var(--rl-hover-bg) !important;
    }

    /* Form Controls & Date Inputs */
    .rl-input {
        background-color: var(--rl-input-bg) !important;
        border: 1px solid var(--rl-border) !important;
        color: var(--rl-text) !important;
        border-radius: 4px;
    }

    .rl-input:focus {
        border-color: #0d9488 !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2) !important;
    }

    body.dark-mode input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    /* DataTables in Dark Mode */
    body.dark-mode .dataTables_wrapper .dataTables_length,
    body.dark-mode .dataTables_wrapper .dataTables_filter,
    body.dark-mode .dataTables_wrapper .dataTables_info,
    body.dark-mode .dataTables_wrapper .dataTables_processing,
    body.dark-mode .dataTables_wrapper .dataTables_paginate {
        color: var(--rl-text-muted) !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_filter input,
    body.dark-mode .dataTables_wrapper .dataTables_length select {
        background-color: var(--rl-input-bg) !important;
        border: 1px solid var(--rl-border) !important;
        color: var(--rl-text) !important;
        border-radius: 4px;
        padding: 4px 8px;
    }

    body.dark-mode .page-item.disabled .page-link {
        background-color: var(--rl-box-bg) !important;
        border-color: var(--rl-border) !important;
        color: var(--rl-text-muted) !important;
    }

    body.dark-mode .page-item .page-link {
        background-color: var(--rl-card-bg) !important;
        border-color: var(--rl-border) !important;
        color: var(--rl-text) !important;
    }

    body.dark-mode .page-item.active .page-link {
        background-color: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-sm-7">
        <h4 class="font-weight-bold mb-1" style="color: var(--rl-text);">
            <i class="fas fa-chart-pie text-teal mr-2"></i> Laporan & Analitik Penjualan Resto
        </h4>
        <p class="small mb-0" style="color: var(--rl-text-muted);">
            Analisis komprehensif performa omset, menu terlaris, dan rekap transaksi nutrisi gizi klinik
        </p>
    </div>
    <div class="col-sm-5 text-right mt-2 mt-sm-0">
        <a href="<?= base_url('resto/display-antrean') ?>" target="_blank" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
            <i class="fas fa-tv mr-1 text-teal"></i> Buka Display TV
        </a>
        <a href="<?= base_url('resto/pos') ?>" class="btn btn-teal btn-sm font-weight-bold">
            <i class="fas fa-cash-register mr-1"></i> Buka Kasir POS
        </a>
    </div>
</div>

<!-- FILTER & ACTION BAR -->
<div class="rl-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= base_url('resto/laporan') ?>" class="form-row align-items-end">
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="font-weight-bold mb-1" style="font-size: 13px; color: var(--rl-text);">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm rl-input" value="<?= esc($startDate) ?>" required>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="font-weight-bold mb-1" style="font-size: 13px; color: var(--rl-text);">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control form-control-sm rl-input" value="<?= esc($endDate) ?>" required>
            </div>
            <div class="col-md-6 text-right mt-2 mt-md-0">
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
        <div class="rl-stat-box border-left-teal">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-weight-bold text-teal text-uppercase">Total Omset Penjualan</span>
                    <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rl-text);">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
                    <small style="color: var(--rl-text-muted);">Periode terpilih</small>
                </div>
                <div class="rl-icon-circle text-teal">
                    <i class="fas fa-wallet fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="rl-stat-box border-left-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-weight-bold text-info text-uppercase">Total Pesanan Selesai</span>
                    <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rl-text);"><?= number_format($totalOrders, 0, ',', '.') ?> Transaksi</h4>
                    <small style="color: var(--rl-text-muted);">Paid & Billed to clinic</small>
                </div>
                <div class="rl-icon-circle text-info">
                    <i class="fas fa-receipt fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="rl-stat-box border-left-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-weight-bold text-success text-uppercase">Rata-rata Transaksi</span>
                    <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rl-text);">Rp <?= number_format($avgBasket, 0, ',', '.') ?></h4>
                    <small style="color: var(--rl-text-muted);">Per transaksi pelanggan</small>
                </div>
                <div class="rl-icon-circle text-success">
                    <i class="fas fa-basket-shopping fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="rl-stat-box border-left-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs font-weight-bold text-warning text-uppercase">Porsi & Item Terjual</span>
                    <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rl-text);"><?= number_format($totalItemsSold, 0, ',', '.') ?> Item</h4>
                    <small style="color: var(--rl-text-muted);">Makanan, jus & skincare</small>
                </div>
                <div class="rl-icon-circle text-warning">
                    <i class="fas fa-boxes-stacked fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS & BEST SELLERS -->
<div class="row mb-4">
    <!-- Daily Sales Trend Chart -->
    <div class="col-md-8 mb-3">
        <div class="rl-card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0" style="color: var(--rl-text);">
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
        <div class="rl-card h-100">
            <div class="card-header py-3">
                <h6 class="font-weight-bold mb-0" style="color: var(--rl-text);">
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
        <div class="rl-card">
            <div class="card-header py-3">
                <h6 class="font-weight-bold mb-0" style="color: var(--rl-text);">
                    <i class="fas fa-trophy text-warning mr-2"></i> Produk & Menu Paling Laris (Top Sellers)
                </h6>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table-resto mb-0" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="text-center">#</th>
                            <th>Kode & Nama Item</th>
                            <th>Kategori</th>
                            <th class="text-center">Total Terjual</th>
                            <th class="text-right">Total Pendapatan</th>
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
                                            <span class="badge px-2 py-1" style="background:#d97706; color:#ffffff;">3</span>
                                        <?php else: ?>
                                            <span class="badge border" style="background:var(--rl-box-bg); color:var(--rl-text);"><?= $rank ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-bold" style="color: var(--rl-text);">
                                        <?= esc($item->name) ?>
                                        <small class="d-block" style="color: var(--rl-text-muted); font-size: 11px;"><?= esc($item->code) ?></small>
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
                                    <td class="text-right font-weight-bold" style="color: var(--rl-text);">
                                        Rp <?= number_format($item->total_omset, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php $rank++; endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color: var(--rl-text-muted);">Belum ada data penjualan pada periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DETAILED TRANSACTION TABLE -->
<div class="row mb-4">
    <div class="col-12">
        <div class="rl-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0" style="color: var(--rl-text);">
                    <i class="fas fa-list text-teal mr-2"></i> Rincian Seluruh Transaksi Penjualan Resto
                </h6>
                <span class="badge badge-teal font-weight-bold px-2 py-1"><?= count($ordersList) ?> Transaksi</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table-resto datatable" id="tableOrdersReport" style="font-size: 13px;">
                        <thead>
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
                                        <strong style="color: var(--rl-text);"><?= esc($ord->customer_name) ?></strong>
                                        <?php if ($ord->diet_instructions): ?>
                                            <small class="d-block" style="color: var(--rl-text-muted);"><?= esc(mb_strimwidth($ord->diet_instructions, 0, 30, '...')) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $ord->order_type === 'diet_pasien' ? 'warning text-dark' : 'secondary' ?> text-capitalize font-weight-bold">
                                            <?= esc(str_replace('_', ' ', $ord->order_type)) ?>
                                        </span>
                                    </td>
                                    <td class="text-uppercase font-weight-bold" style="font-size: 11px; color: var(--rl-text);">
                                        <?= esc($ord->payment_method ?: 'tunai') ?>
                                    </td>
                                    <td style="color: var(--rl-text);"><?= esc($ord->cashier_name ?: 'Kasir Utama') ?></td>
                                    <td class="text-right font-weight-bold" style="color: var(--rl-text);">
                                        Rp <?= number_format($ord->grand_total, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('resto/cetak-nota/' . $ord->id) ?>" target="_blank" class="btn btn-xs btn-outline-secondary font-weight-bold" title="Cetak Ulang Struk">
                                            <i class="fas fa-print mr-1"></i> Struk
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

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.body.classList.contains('dark-mode');
        const gridColor = isDark ? '#374151' : '#e5e7eb';
        const textColor = isDark ? '#9ca3af' : '#64748b';

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
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
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
                    borderColor: isDark ? '#1f2328' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            boxWidth: 12, 
                            font: { size: 11 },
                            color: isDark ? '#f0f6fc' : '#212529'
                        }
                    }
                }
            }
        });

        // Initialize DataTable with indonesian language
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tableOrdersReport').DataTable({
                language: {
                    search: "Cari Transaksi:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada transaksi yang cocok",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                },
                order: [[1, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                responsive: true
            });
        }
    });
</script>
<?= $this->endSection() ?>
