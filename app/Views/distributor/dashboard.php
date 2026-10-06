<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-truck-fast text-indigo mr-2"></i> Dashboard Distributor &amp; Grosir
                </h4>
                <small class="text-muted">Unit Bisnis Distribusi B2B, Penjualan Partai Besar, Manajemen Piutang &amp; Stok Mandiri</small>
            </div>
            <div class="col-lg-6 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-indigo btn-sm font-weight-bold shadow-sm" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-cart-plus mr-1"></i> Kasir &amp; Faktur Baru
                    </a>
                    <a href="<?= base_url('distributor/kas') ?>" class="btn btn-outline-success btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-wallet mr-1"></i> Kas &amp; Bank B2B
                    </a>
                    <a href="<?= base_url('distributor/jurnal') ?>" class="btn btn-outline-indigo btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-file-lines mr-1"></i> Jurnal B2B
                    </a>
                    <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Piutang
                    </a>
                    <a href="<?= base_url('distributor/laporan') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-chart-line mr-1"></i> Laporan Laba Rugi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- STATISTIC CARDS ROW -->
        <div class="row">
            <!-- 1. Penjualan Hari Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-xs border" style="border-radius: 12px; border-left: 4px solid #4f46e5 !important;">
                    <div class="inner p-3">
                        <span class="text-muted font-weight-bold text-xs text-uppercase">Omset Hari Ini</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-indigo" style="color: #4f46e5;">Rp <?= number_format($stats['sales_today'] ?? 0, 0, ',', '.') ?></h4>
                        <small class="text-muted d-block mt-1"><i class="fas fa-calendar-day mr-1"></i> <?= date('d M Y') ?></small>
                    </div>
                    <div class="icon" style="top: 15px; right: 15px; color: rgba(79, 70, 229, 0.15);">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Penjualan Bulan Ini -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-xs border" style="border-radius: 12px; border-left: 4px solid #0d9488 !important;">
                    <div class="inner p-3">
                        <span class="text-muted font-weight-bold text-xs text-uppercase">Omset Bulan Ini</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-teal">Rp <?= number_format($stats['sales_month'] ?? 0, 0, ',', '.') ?></h4>
                        <small class="text-muted d-block mt-1"><i class="fas fa-receipt mr-1"></i> <?= $stats['sales_count_month'] ?? 0 ?> Transaksi Faktur</small>
                    </div>
                    <div class="icon" style="top: 15px; right: 15px; color: rgba(13, 148, 136, 0.15);">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Total Piutang Belum Lunas -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-xs border" style="border-radius: 12px; border-left: 4px solid #f59e0b !important;">
                    <div class="inner p-3">
                        <span class="text-muted font-weight-bold text-xs text-uppercase">Total Piutang Berjalan</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-warning">Rp <?= number_format($stats['total_receivable'] ?? 0, 0, ',', '.') ?></h4>
                        <small class="text-danger font-weight-bold d-block mt-1">
                            <i class="fas fa-triangle-exclamation mr-1"></i> Rp <?= number_format($stats['overdue_receivable'] ?? 0, 0, ',', '.') ?> Jatuh Tempo (<?= $stats['overdue_count'] ?? 0 ?> Faktur)
                        </small>
                    </div>
                    <div class="icon" style="top: 15px; right: 15px; color: rgba(245, 158, 11, 0.15);">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>

            <!-- 4. Nilai Stok Gudang Distributor -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white shadow-xs border" style="border-radius: 12px; border-left: 4px solid #10b981 !important;">
                    <div class="inner p-3">
                        <span class="text-muted font-weight-bold text-xs text-uppercase">Nilai Stok Gudang (HPP)</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-success">Rp <?= number_format($stats['stock_cogs_value'] ?? 0, 0, ',', '.') ?></h4>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-boxes-stacked mr-1"></i> <?= $stats['total_stock_units'] ?? 0 ?> Unit / <?= $stats['total_stock_items'] ?? 0 ?> Item (<?= $stats['customer_count'] ?? 0 ?> Customer)
                        </small>
                    </div>
                    <div class="icon" style="top: 15px; right: 15px; color: rgba(16, 185, 129, 0.15);">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN SECTION -->
        <div class="row">
            <!-- LEFT: RECENT SALES TABLE -->
            <div class="col-lg-8">
                <div class="card card-outline card-indigo shadow-none border">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold m-0 text-dark">
                            <i class="fas fa-receipt mr-1 text-indigo"></i> Transaksi Penjualan Terbaru
                        </h5>
                        <a href="<?= base_url('distributor/riwayat') ?>" class="btn btn-outline-indigo btn-xs font-weight-bold">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No. Faktur</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Metode</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($stats['recent_sales'])): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi penjualan distributor.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($stats['recent_sales'] as $s): ?>
                                            <tr>
                                                <td class="font-weight-bold text-indigo"><?= esc($s->invoice_no) ?></td>
                                                <td><?= date('d/m/Y', strtotime($s->sale_date)) ?></td>
                                                <td>
                                                    <strong><?= esc($s->customer_name) ?></strong>
                                                    <?php if (!empty($s->company_name)): ?>
                                                        <small class="text-muted d-block"><?= esc($s->company_name) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($s->payment_type === 'credit'): ?>
                                                        <span class="badge badge-warning text-dark font-weight-bold">Kredit (<?= $s->payment_terms_days ?> Hari)</span>
                                                    <?php elseif ($s->payment_type === 'transfer'): ?>
                                                        <span class="badge badge-info font-weight-bold">Transfer Bank</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success font-weight-bold">Tunai</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <?php if ($s->payment_status === 'paid'): ?>
                                                        <span class="badge badge-success">Lunas</span>
                                                    <?php elseif ($s->payment_status === 'partial'): ?>
                                                        <span class="badge badge-info">Sebagian</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Belum Lunas</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('distributor/cetak-faktur/' . $s->id) ?>" target="_blank" class="btn btn-xs btn-outline-primary" title="Cetak Faktur">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                    <a href="<?= base_url('distributor/cetak-surat-jalan/' . $s->id) ?>" target="_blank" class="btn btn-xs btn-outline-secondary" title="Surat Jalan">
                                                        <i class="fas fa-truck"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: RECEIVABLES MONITORING & QUICK ACTIONS -->
            <div class="col-lg-4">
                <!-- Overdue Receivables Alert Card -->
                <div class="card card-outline card-warning shadow-none border mb-3">
                    <div class="card-header bg-white py-2">
                        <h6 class="card-title font-weight-bold m-0 text-dark">
                            <i class="fas fa-hourglass-half text-warning mr-1"></i> Piutang Jatuh Tempo
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush text-sm">
                            <?php if (empty($stats['due_receivables'])): ?>
                                <li class="list-group-item text-center text-muted py-3">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada piutang tertunggak.
                                </li>
                            <?php else: ?>
                                <?php foreach ($stats['due_receivables'] as $dr): 
                                    $isOver = strtotime($dr->due_date) < strtotime(date('Y-m-d'));
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <div>
                                            <strong class="d-block text-dark"><?= esc($dr->customer_name) ?></strong>
                                            <small class="text-muted">Faktur: <?= esc($dr->invoice_no) ?></small>
                                            <small class="d-block <?= $isOver ? 'text-danger font-weight-bold' : 'text-muted' ?>">
                                                Tempo: <?= date('d/m/Y', strtotime($dr->due_date)) ?> <?= $isOver ? '(Lewat Tempo)' : '' ?>
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-weight-bold text-danger d-block">Rp <?= number_format($dr->remaining_balance, 0, ',', '.') ?></span>
                                            <a href="<?= base_url('distributor/piutang') ?>" class="btn btn-xs btn-outline-warning mt-1">Bayar</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-light p-2 text-center">
                        <a href="<?= base_url('distributor/piutang') ?>" class="text-xs font-weight-bold text-warning">
                            Kelola Semua Piutang <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Menu Panel -->
                <div class="card card-outline card-secondary shadow-none border">
                    <div class="card-header bg-white py-2">
                        <h6 class="card-title font-weight-bold m-0 text-dark">
                            <i class="fas fa-bolt text-warning mr-1"></i> Akses Cepat Distributor
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row no-gutters">
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/penjualan') ?>" class="btn btn-outline-indigo btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-cash-register d-block text-indigo mb-1" style="font-size: 16px;"></i>
                                    <strong>Kasir Grosir</strong>
                                </a>
                            </div>
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/pelanggan') ?>" class="btn btn-outline-info btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-users d-block text-info mb-1" style="font-size: 16px;"></i>
                                    <strong>Data Pelanggan</strong>
                                </a>
                            </div>
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/stok') ?>" class="btn btn-outline-teal btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-boxes-stacked d-block text-teal mb-1" style="font-size: 16px;"></i>
                                    <strong>Stok &amp; FEFO</strong>
                                </a>
                            </div>
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/transfer') ?>" class="btn btn-outline-primary btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-arrow-right-arrow-left d-block text-primary mb-1" style="font-size: 16px;"></i>
                                    <strong>Transfer Unit</strong>
                                </a>
                            </div>
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/retur') ?>" class="btn btn-outline-danger btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-rotate-left d-block text-danger mb-1" style="font-size: 16px;"></i>
                                    <strong>Retur Penjualan</strong>
                                </a>
                            </div>
                            <div class="col-6 p-1">
                                <a href="<?= base_url('distributor/laporan') ?>" class="btn btn-outline-success btn-block py-2 text-left" style="font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-chart-pie d-block text-success mb-1" style="font-size: 16px;"></i>
                                    <strong>Laba Rugi</strong>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
