<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark */
    :root {
        --lp-canvas-bg: #f4f6f9;
        --lp-card-bg: #ffffff;
        --lp-border: #b8b8b8;
        --lp-text: #212529;
        --lp-text-muted: #6c757d;
        --lp-th-bg: #f8fafc;
        --lp-hover-bg: #f1f5f9;
        --lp-box-bg: #f8fafc;
    }

    body.dark-mode {
        --lp-canvas-bg: #14171a;
        --lp-card-bg: #1f2328;
        --lp-border: #374151;
        --lp-text: #f0f6fc;
        --lp-text-muted: #9ca3af;
        --lp-th-bg: #181b1f;
        --lp-hover-bg: #262c36;
        --lp-box-bg: #181b1f;
    }

    /* Container Card Styling */
    .lp-card {
        background-color: var(--lp-card-bg) !important;
        border: 1px solid var(--lp-border) !important;
        border-radius: 4px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .lp-header {
        background-color: var(--lp-card-bg) !important;
        border-bottom: 1px solid var(--lp-border) !important;
    }

    .lp-box {
        background-color: var(--lp-box-bg) !important;
        border: 1px solid var(--lp-border) !important;
        border-radius: 4px;
    }

    .lp-text-adaptive {
        color: var(--lp-text) !important;
    }

    .lp-text-muted {
        color: var(--lp-text-muted) !important;
    }

    /* Strict Excel-Standard Table - No Zebra Stripes (desain_tabel.md) */
    .table-lp {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1px solid var(--lp-border) !important;
        color: var(--lp-text) !important;
        margin-bottom: 0 !important;
    }

    .table-lp thead th {
        background-color: var(--lp-th-bg) !important;
        color: var(--lp-text) !important;
        border: 1px solid var(--lp-border) !important;
        padding: 9px 10px !important;
        font-size: 11.5px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700 !important;
        vertical-align: middle !important;
    }

    .table-lp tbody td {
        border: 1px solid var(--lp-border) !important;
        padding: 7px 10px !important;
        font-size: 12.5px !important;
        vertical-align: middle !important;
        background-color: var(--lp-card-bg) !important;
        color: var(--lp-text) !important;
    }

    .table-lp tbody tr:hover td {
        background-color: var(--lp-hover-bg) !important;
    }

    .table-lp tfoot td {
        background-color: var(--lp-th-bg) !important;
        color: var(--lp-text) !important;
        border: 1px solid var(--lp-border) !important;
        font-weight: 700;
        padding: 8px 10px !important;
    }

    /* KPI Info Boxes */
    .lp-info-box {
        background: var(--lp-card-bg) !important;
        border: 1px solid var(--lp-border) !important;
        border-radius: 4px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        transition: transform 0.15s ease;
    }

    .lp-info-box:hover {
        transform: translateY(-2px);
    }

    .lp-info-icon {
        width: 48px;
        height: 48px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    /* Nav Tabs Styling */
    .nav-tabs {
        border-bottom: 1px solid var(--lp-border) !important;
    }

    .nav-tabs .nav-link {
        color: var(--lp-text-muted) !important;
        border: 1px solid transparent !important;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .nav-tabs .nav-link.active {
        color: #20c997 !important;
        background-color: var(--lp-card-bg) !important;
        border-color: var(--lp-border) var(--lp-border) transparent !important;
    }

    /* Form Controls in Dark/Light */
    body.dark-mode .form-control,
    body.dark-mode select {
        background-color: #181b1f !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }

    body.dark-mode .form-control:focus,
    body.dark-mode select:focus {
        border-color: #20c997 !important;
        box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25) !important;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <!-- Top Metrics Cards -->
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="lp-info-box">
                    <div class="lp-info-icon bg-teal text-white">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase lp-text-muted d-block">Obat Diserahkan</span>
                        <h4 class="font-weight-bold lp-text-adaptive mb-0"><?= number_format($totalDispensedQty, 0, ',', '.') ?> <small class="lp-text-muted" style="font-size: 13px;">Unit</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="lp-info-box">
                    <div class="lp-info-icon bg-success text-white">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase lp-text-muted d-block">Nilai Penjualan Resep</span>
                        <h5 class="font-weight-bold text-teal mb-0">Rp <?= number_format($totalDispensedAmount, 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="lp-info-box">
                    <div class="lp-info-icon bg-danger text-white">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase lp-text-muted d-block">Risiko Expired (&le; 90 Hari)</span>
                        <h4 class="font-weight-bold text-danger mb-0"><?= $criticalCount + $warningCount ?> <small class="lp-text-muted" style="font-size: 13px;">Batch</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mb-2 mb-md-0">
                <div class="lp-info-box">
                    <div class="lp-info-icon bg-info text-white">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase lp-text-muted d-block">Aktivitas Mutasi Stok</span>
                        <h4 class="font-weight-bold lp-text-adaptive mb-0"><?= count($movements) ?> <small class="lp-text-muted" style="font-size: 13px;">Transaksi</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Period & Actions Card -->
        <div class="card lp-card shadow-none mb-3">
            <div class="card-body p-3">
                <form action="<?= base_url('apotek/laporan') ?>" method="get" class="row align-items-end" id="form-filter-laporan">
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-xs lp-text-adaptive text-uppercase mb-1">Dari Tanggal:</label>
                        <input type="date" name="start_date" id="filter-start-date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-xs lp-text-adaptive text-uppercase mb-1">Sampai Tanggal:</label>
                        <input type="date" name="end_date" id="filter-end-date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-xs lp-text-adaptive text-uppercase mb-1">Jenis Transaksi:</label>
                        <select name="sale_type" data-native="true" class="form-control form-control-sm">
                            <option value="">-- Semua Transaksi --</option>
                            <option value="resep" <?= $saleType === 'resep' ? 'selected' : '' ?>>Resep Dokter / Poli</option>
                            <option value="otc" <?= $saleType === 'otc' ? 'selected' : '' ?>>Obat Bebas Non-Resep (OTC)</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-xs lp-text-adaptive text-uppercase mb-1">Dokter Perujuk:</label>
                        <select name="doctor_id" data-native="true" class="form-control form-control-sm">
                            <option value="">-- Semua Dokter --</option>
                            <?php foreach ($doctorsList as $doc): ?>
                                <option value="<?= $doc->id ?>" <?= $doctorFilter == $doc->id ? 'selected' : '' ?>>
                                    <?= esc($doc->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-2 mb-md-0">
                        <label class="font-weight-bold text-xs lp-text-adaptive text-uppercase mb-1">Filter Obat (Opsional):</label>
                        <select name="medicine_id" data-native="true" class="form-control form-control-sm">
                            <option value="">-- Semua Item Obat --</option>
                            <?php foreach ($medicinesList as $med): ?>
                                <option value="<?= $med->id ?>" <?= $medFilter == $med->id ? 'selected' : '' ?>>
                                    [<?= esc($med->code) ?>] <?= esc($med->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0 d-flex">
                        <button type="submit" class="btn btn-teal btn-sm font-weight-bold flex-grow-1 mr-1 shadow-none">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('apotek/laporan') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Report Content Tabs -->
        <div class="card lp-card shadow-none">
            <div class="card-header p-0 border-bottom-0 bg-transparent">
                <ul class="nav nav-tabs" id="custom-tabs-laporan" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-3" id="tab-dispensed-tab" data-toggle="pill" href="#tab-dispensed" role="tab" aria-controls="tab-dispensed" aria-selected="true">
                            <i class="fas fa-file-invoice text-teal mr-1"></i> 1. PEMAKAIAN &amp; PENJUALAN RESEP
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-sales-tab" data-toggle="pill" href="#tab-sales" role="tab" aria-controls="tab-sales" aria-selected="false">
                            <i class="fas fa-cash-register text-success mr-1"></i> 2. PENJUALAN APOTEK (OTC &amp; RESEP)
                            <span class="badge badge-success ml-1"><?= count($otcSales) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-movements-tab" data-toggle="pill" href="#tab-movements" role="tab" aria-controls="tab-movements" aria-selected="false">
                            <i class="fas fa-dolly-flatbed text-info mr-1"></i> 3. KARTU STOK &amp; MUTASI OBAT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-expired-tab" data-toggle="pill" href="#tab-expired" role="tab" aria-controls="tab-expired" aria-selected="false">
                            <i class="fas fa-calendar-times text-warning mr-1"></i> 4. MONITORING KADALUARSA (FEFO)
                            <?php if ($criticalCount > 0): ?>
                                <span class="badge badge-danger ml-1"><?= $criticalCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-top-tab" data-toggle="pill" href="#tab-top" role="tab" aria-controls="tab-top" aria-selected="false">
                            <i class="fas fa-trophy text-teal mr-1"></i> 5. TOP 10 FAST-MOVING
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="tab-content" id="custom-tabs-laporanContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: PEMAKAIAN & PENJUALAN OBAT RESEP -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-dispensed" role="tabpanel" aria-labelledby="tab-dispensed-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="font-weight-bold lp-text-adaptive mb-0">
                                    <i class="fas fa-receipt text-teal mr-1"></i> Laporan Rincian Pemakaian &amp; Penjualan Obat Pasien
                                </h5>
                                <small class="lp-text-muted">Periode: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong></small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=pemakaian&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold btn-print-report">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan Pemakaian
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-lp datatable" id="table-dispensed">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 45px;">NO</th>
                                        <th style="width: 105px;">TANGGAL</th>
                                        <th>IDENTITAS PASIEN &amp; DOKTER</th>
                                        <th style="width: 95px;">KODE</th>
                                        <th>NAMA OBAT &amp; SATUAN</th>
                                        <th style="width: 80px;">QTY</th>
                                        <th>ATURAN PAKAI</th>
                                        <th style="width: 110px;" class="text-right">HARGA SATUAN</th>
                                        <th style="width: 120px;" class="text-right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dispensed)): ?>
                                        <?php $no = 1; foreach ($dispensed as $d): ?>
                                            <?php $sub = (float)$d->price * (int)$d->qty; ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td class="text-center font-monospace" style="font-size: 12px;">
                                                    <strong><?= date('d/m/Y', strtotime($d->dispensed_date)) ?></strong><br>
                                                    <small class="lp-text-muted"><?= date('H:i', strtotime($d->dispensed_date)) ?></small>
                                                </td>
                                                <td>
                                                    <strong class="lp-text-adaptive"><?= esc($d->patient_name) ?></strong> <span class="badge badge-secondary ml-1"><?= esc($d->no_rm) ?></span><br>
                                                    <small class="lp-text-muted"><i class="fas fa-user-md mr-1"></i><?= esc($d->doctor_name) ?></small>
                                                </td>
                                                <td class="text-center"><span class="badge badge-light border"><?= esc($d->medicine_code) ?></span></td>
                                                <td>
                                                    <strong class="lp-text-adaptive"><?= esc($d->medicine_name) ?></strong><br>
                                                    <small class="lp-text-muted">Satuan: <?= esc($d->unit) ?></small>
                                                </td>
                                                <td class="text-center font-weight-bold text-teal" style="font-size: 14px;">
                                                    <?= esc($d->qty) ?>
                                                </td>
                                                <td class="text-sm">
                                                    <span class="badge badge-light border text-dark"><?= esc($d->dosage) ?></span>
                                                </td>
                                                <td class="text-right font-monospace">Rp <?= number_format($d->price, 0, ',', '.') ?></td>
                                                <td class="text-right font-weight-bold lp-text-adaptive font-monospace">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold" style="font-size: 13.5px;">
                                        <td colspan="5" class="text-right">TOTAL PENGELUARAN &amp; PENJUALAN:</td>
                                        <td class="text-center text-teal font-monospace"><?= number_format($totalDispensedQty, 0, ',', '.') ?></td>
                                        <td colspan="2"></td>
                                        <td class="text-right text-success font-monospace">Rp <?= number_format($totalDispensedAmount, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: PENJUALAN APOTEK (OTC & RESEP LANGSUNG) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-sales" role="tabpanel" aria-labelledby="tab-sales-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="font-weight-bold lp-text-adaptive mb-0">
                                    <i class="fas fa-cash-register text-success mr-1"></i> Rekap Penjualan Apotek (OTC &amp; Resep Langsung)
                                </h5>
                                <small class="lp-text-muted">Rincian pendapatan farmasi mencakup obat bebas, racikan, jasa tusla, kemasan embalase, dan fee dokter.</small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/export-penjualan-csv?start_date=' . $startDate . '&end_date=' . $endDate . '&doctor_id=' . $doctorFilter . '&sale_type=' . $saleType) ?>" class="btn btn-outline-success btn-sm font-weight-bold mr-1" id="btn-export-sales-csv">
                                    <i class="fas fa-file-csv mr-1"></i> Ekspor CSV
                                </a>
                                <a href="<?= base_url('apotek/cetak-laporan?type=penjualan_otc&start_date=' . $startDate . '&end_date=' . $endDate . '&doctor_id=' . $doctorFilter . '&sale_type=' . $saleType) ?>" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold btn-print-report">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan Penjualan
                                </a>
                            </div>
                        </div>

                        <!-- KPI Summary Cards for Sales -->
                        <div class="row mb-3">
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0">
                                    <small class="lp-text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Total Transaksi</small>
                                    <h5 class="font-weight-bold lp-text-adaptive mb-0"><?= number_format($otcSummary['count'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">Struk Penjualan</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0">
                                    <small class="lp-text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Net Obat &amp; BMHP</small>
                                    <h5 class="font-weight-bold lp-text-adaptive mb-0">Rp <?= number_format($otcSummary['total_obat'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">Nilai Pokok Item</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0">
                                    <small class="lp-text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Tusla (Jasa Racik)</small>
                                    <h5 class="font-weight-bold text-info mb-0">Rp <?= number_format($otcSummary['total_tusla'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">Jasa Farmasi</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0">
                                    <small class="lp-text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Embalase (Kemasan)</small>
                                    <h5 class="font-weight-bold text-teal mb-0">Rp <?= number_format($otcSummary['total_embalase'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">Klip &amp; Pot</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0">
                                    <small class="lp-text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Fee Dokter (Akun 241)</small>
                                    <h5 class="font-weight-bold text-danger mb-0">Rp <?= number_format($otcSummary['total_fee_doc'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">5% Resep Dokter</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-2">
                                <div class="lp-box p-2 text-center h-100 mb-0" style="border-left: 3px solid #20c997 !important;">
                                    <small class="text-uppercase font-weight-bold text-teal" style="font-size: 10px;">Grand Total Omzet</small>
                                    <h5 class="font-weight-bold text-teal mb-0">Rp <?= number_format($otcSummary['total_grand'], 0, ',', '.') ?></h5>
                                    <small class="lp-text-muted" style="font-size: 10px;">Penerimaan Kasir</small>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-lp datatable" id="table-sales">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 40px;">NO</th>
                                        <th style="width: 135px;">NO. TRANSAKSI</th>
                                        <th style="width: 95px;">WAKTU</th>
                                        <th>PEMBELI / PASIEN</th>
                                        <th>DOKTER / RESEP</th>
                                        <th class="text-right" style="width: 95px;">OBAT</th>
                                        <th class="text-right" style="width: 80px;">TUSLA</th>
                                        <th class="text-right" style="width: 80px;">EMBALASE</th>
                                        <th class="text-right" style="width: 110px;">GRAND TOTAL</th>
                                        <th class="text-right" style="width: 95px;">FEE DOKTER</th>
                                        <th style="width: 100px;">KASIR &amp; METODE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($otcSales)): ?>
                                        <?php $no = 1; foreach ($otcSales as $os): ?>
                                            <?php 
                                                $osObat = (float)$os->grand_total - (float)($os->tusla_amount ?? 0) - (float)($os->embalase_amount ?? 0) + (float)($os->discount_amount ?? 0);
                                                $osFeeDoc = !empty($os->doctor_id) ? round((float)$os->total_amount * 0.05, 2) : 0;
                                            ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td class="text-center font-monospace"><code><?= esc($os->sale_no) ?></code></td>
                                                <td class="text-center font-monospace" style="font-size: 12px;">
                                                    <strong><?= date('d/m/Y', strtotime($os->created_at)) ?></strong><br>
                                                    <small class="lp-text-muted"><?= date('H:i', strtotime($os->created_at)) ?></small>
                                                </td>
                                                <td>
                                                    <strong class="lp-text-adaptive"><?= esc($os->customer_name ?: 'Umum') ?></strong>
                                                    <?php if (!empty($os->customer_phone)): ?>
                                                        <br><small class="lp-text-muted"><?= esc($os->customer_phone) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($os->doctor_name)): ?>
                                                        <strong class="text-teal"><i class="fas fa-user-md mr-1"></i><?= esc($os->doctor_name) ?></strong>
                                                    <?php else: ?>
                                                        <span class="badge badge-light border">Penjualan Bebas (OTC)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-monospace">Rp <?= number_format($osObat, 0, ',', '.') ?></td>
                                                <td class="text-right font-monospace text-info">Rp <?= number_format($os->tusla_amount ?? 0, 0, ',', '.') ?></td>
                                                <td class="text-right font-monospace text-teal">Rp <?= number_format($os->embalase_amount ?? 0, 0, ',', '.') ?></td>
                                                <td class="text-right font-monospace font-weight-bold text-success" style="font-size: 13px;">
                                                    Rp <?= number_format($os->grand_total, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-right font-monospace text-danger">
                                                    <?= $osFeeDoc > 0 ? 'Rp ' . number_format($osFeeDoc, 0, ',', '.') : '-' ?>
                                                </td>
                                                <td class="text-center" style="font-size: 11.5px;">
                                                    <span class="badge badge-light border text-uppercase mb-1"><?= esc($os->payment_method ?: 'Tunai') ?></span><br>
                                                    <small class="lp-text-muted"><i class="fas fa-user mr-1"></i><?= esc($os->cashier_name ?? 'Kasir') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold" style="font-size: 13.5px;">
                                        <td colspan="5" class="text-right">TOTAL KESELURUHAN PENJUALAN APOTEK:</td>
                                        <td class="text-right font-monospace">Rp <?= number_format($otcSummary['total_obat'], 0, ',', '.') ?></td>
                                        <td class="text-right text-info font-monospace">Rp <?= number_format($otcSummary['total_tusla'], 0, ',', '.') ?></td>
                                        <td class="text-right text-teal font-monospace">Rp <?= number_format($otcSummary['total_embalase'], 0, ',', '.') ?></td>
                                        <td class="text-right text-success font-monospace" style="font-size: 14px;">Rp <?= number_format($otcSummary['total_grand'], 0, ',', '.') ?></td>
                                        <td class="text-right text-danger font-monospace">Rp <?= number_format($otcSummary['total_fee_doc'], 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: KARTU STOK & MUTASI OBAT -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-movements" role="tabpanel" aria-labelledby="tab-movements-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="font-weight-bold lp-text-adaptive mb-0">
                                    <i class="fas fa-exchange-alt text-info mr-1"></i> Log Mutasi &amp; Kartu Stok Obat Farmasi
                                </h5>
                                <small class="lp-text-muted">Rekam jejak setiap penambahan stok, pengeluaran resep, stok opname, dan retur obat.</small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=mutasi&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-info btn-sm font-weight-bold btn-print-report">
                                    <i class="fas fa-print mr-1"></i> Cetak Kartu Stok
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-lp datatable" id="table-movements">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 110px;">WAKTU</th>
                                        <th style="width: 95px;">KODE</th>
                                        <th>NAMA OBAT</th>
                                        <th style="width: 120px;">NO. BATCH</th>
                                        <th style="width: 120px;">TIPE MUTASI</th>
                                        <th style="width: 85px;" class="text-success">MASUK (+)</th>
                                        <th style="width: 85px;" class="text-danger">KELUAR (-)</th>
                                        <th style="width: 95px;" class="text-teal">SALDO AKHIR</th>
                                        <th style="width: 100px;">PETUGAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($movements)): ?>
                                        <?php foreach ($movements as $m): ?>
                                            <tr>
                                                <td class="text-center font-monospace" style="font-size: 12px;">
                                                    <strong><?= date('d/m/Y', strtotime($m->created_at)) ?></strong><br>
                                                    <small class="lp-text-muted"><?= date('H:i:s', strtotime($m->created_at)) ?></small>
                                                </td>
                                                <td class="text-center"><span class="badge badge-secondary"><?= esc($m->medicine_code) ?></span></td>
                                                <td>
                                                    <strong class="lp-text-adaptive"><?= esc($m->medicine_name) ?></strong><br>
                                                    <small class="lp-text-muted">Satuan: <?= esc($m->unit) ?></small>
                                                </td>
                                                <td class="text-center font-monospace">
                                                    <span class="badge badge-light border"><?= esc($m->batch_no ?: '-') ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $badgeClass = 'secondary';
                                                        if ($m->transaction_type === 'resep') $badgeClass = 'primary';
                                                        elseif ($m->transaction_type === 'pembelian') $badgeClass = 'success';
                                                        elseif ($m->transaction_type === 'opname') $badgeClass = 'warning';
                                                        elseif ($m->transaction_type === 'retur') $badgeClass = 'danger';
                                                    ?>
                                                    <span class="badge badge-<?= $badgeClass ?> text-uppercase"><?= esc($m->transaction_type) ?></span>
                                                </td>
                                                <td class="text-center font-weight-bold text-success font-monospace">
                                                    <?= $m->qty_in > 0 ? '+' . $m->qty_in : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-danger font-monospace">
                                                    <?= $m->qty_out > 0 ? '-' . $m->qty_out : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-teal font-monospace" style="font-size: 13.5px;">
                                                    <?= esc($m->balance) ?>
                                                </td>
                                                <td class="text-center text-sm lp-text-muted">
                                                    <i class="fas fa-user-circle mr-1"></i><?= esc($m->user_name ?? 'Sistem') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 4: MONITORING KADALUARSA (FEFO) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-expired" role="tabpanel" aria-labelledby="tab-expired-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="font-weight-bold lp-text-adaptive mb-0">
                                    <i class="fas fa-hourglass-half text-warning mr-1"></i> Monitoring Kadaluarsa Obat &amp; Analisis FEFO (First-Expired, First-Out)
                                </h5>
                                <small class="lp-text-muted">Deteksi dini obat yang mendekati masa kadaluarsa untuk tindakan retur distributor atau prioritas penyerahan resep.</small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=expired') ?>" target="_blank" class="btn btn-outline-warning btn-sm font-weight-bold btn-print-report">
                                    <i class="fas fa-print mr-1"></i> Cetak Monitoring Expired
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-lp datatable" id="table-expired">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 45px;">NO</th>
                                        <th style="width: 95px;">KODE</th>
                                        <th>NAMA OBAT &amp; GOLONGAN</th>
                                        <th style="width: 120px;">NO. BATCH</th>
                                        <th style="width: 110px;">TGL EXPIRED</th>
                                        <th style="width: 125px;">SISA HARI</th>
                                        <th style="width: 95px;">STOK FISIK</th>
                                        <th style="width: 135px;">STATUS RISIKO</th>
                                        <th>REKOMENDASI AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($expiryBatches)): ?>
                                        <?php $no = 1; foreach ($expiryBatches as $eb): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td class="text-center"><span class="badge badge-secondary"><?= esc($eb->medicine_code) ?></span></td>
                                                <td>
                                                    <strong class="lp-text-adaptive"><?= esc($eb->medicine_name) ?></strong><br>
                                                    <small class="lp-text-muted">Golongan: <span class="badge badge-light border"><?= strtoupper(esc($eb->type)) ?></span> | Satuan: <?= esc($eb->unit) ?></small>
                                                </td>
                                                <td class="text-center font-monospace"><strong><?= esc($eb->batch_no) ?></strong></td>
                                                <td class="text-center font-weight-bold font-monospace">
                                                    <?= date('d/m/Y', strtotime($eb->expired_date)) ?>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?php if ($eb->days_left <= 0): ?>
                                                        <span class="text-danger font-weight-bold">Expired (Lewat <?= abs($eb->days_left) ?> Hari)</span>
                                                    <?php else: ?>
                                                        <span class="<?= $eb->days_left <= 30 ? 'text-danger font-weight-bold' : ($eb->days_left <= 90 ? 'text-warning font-weight-bold' : 'text-success font-weight-bold') ?>">
                                                            <?= $eb->days_left ?> Hari Lagi
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center font-weight-bold font-monospace text-teal" style="font-size: 13.5px;">
                                                    <?= esc($eb->stock) ?> <small class="lp-text-muted"><?= esc($eb->unit) ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($eb->risk_status === 'expired'): ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i>EXPIRED</span>
                                                    <?php elseif ($eb->risk_status === 'critical'): ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i>KRITIS (&le; 30 Hari)</span>
                                                    <?php elseif ($eb->risk_status === 'warning'): ?>
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i>WASPADA (&le; 90 Hari)</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-shield-alt mr-1"></i>AMAN</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-sm">
                                                    <?php if ($eb->risk_status === 'expired'): ?>
                                                        <strong class="text-danger"><i class="fas fa-trash-alt mr-1"></i>Karantina &amp; Retur Segera</strong>
                                                    <?php elseif ($eb->risk_status === 'critical'): ?>
                                                        <strong class="text-danger"><i class="fas fa-undo mr-1"></i>Ajukan Retur Supplier / FEFO Prioritas</strong>
                                                    <?php elseif ($eb->risk_status === 'warning'): ?>
                                                        <span class="text-warning font-weight-bold"><i class="fas fa-arrow-up mr-1"></i>FEFO: Habiskan Batch Ini Dahulu</span>
                                                    <?php else: ?>
                                                        <span class="lp-text-muted"><i class="fas fa-check mr-1"></i>Distribusi Normal Sesuai Resep</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 5: TOP 10 FAST-MOVING MEDICINES -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-top" role="tabpanel" aria-labelledby="tab-top-tab">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div class="mb-2 mb-md-0">
                                <h5 class="font-weight-bold lp-text-adaptive mb-0">
                                    <i class="fas fa-chart-pie text-teal mr-1"></i> Analisis Ranking 10 Besar Obat Fast-Moving (Paling Sering Diresepkan)
                                </h5>
                                <small class="lp-text-muted">Data agregasi pemakaian kumulatif untuk acuan perencanaan pengadaan obat (Procurement / PO).</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-lp" id="table-top-medicines">
                                        <thead>
                                            <tr class="text-center">
                                                <th style="width: 55px;">RANK</th>
                                                <th style="width: 100px;">KODE</th>
                                                <th>NAMA OBAT</th>
                                                <th style="width: 120px;">GOLONGAN</th>
                                                <th style="width: 100px;">SATUAN</th>
                                                <th style="width: 140px;">FREKUENSI RESEP</th>
                                                <th style="width: 150px;" class="text-teal">TOTAL QTY KELUAR</th>
                                                <th>INDIKATOR DISTRIBUSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($topMedicines)): ?>
                                                <?php 
                                                    $rank = 1; 
                                                    $maxQty = max(array_column($topMedicines, 'total_qty')) ?: 1;
                                                ?>
                                                <?php foreach ($topMedicines as $top): ?>
                                                    <?php $pct = round(($top->total_qty / $maxQty) * 100); ?>
                                                    <tr>
                                                        <td class="text-center font-weight-bold">
                                                            <?php if ($rank === 1): ?>
                                                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="fas fa-crown mr-1"></i>#1</span>
                                                            <?php elseif ($rank === 2): ?>
                                                                <span class="badge badge-secondary font-weight-bold px-2 py-1">#2</span>
                                                            <?php elseif ($rank === 3): ?>
                                                                <span class="badge font-weight-bold px-2 py-1" style="background:#cd7f32; color:#fff;">#3</span>
                                                            <?php else: ?>
                                                                <span class="lp-text-muted font-weight-bold">#<?= $rank ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center"><span class="badge badge-secondary"><?= esc($top->code) ?></span></td>
                                                        <td><strong class="lp-text-adaptive" style="font-size: 13.5px;"><?= esc($top->name) ?></strong></td>
                                                        <td class="text-center"><span class="badge badge-light border"><?= strtoupper(esc($top->type)) ?></span></td>
                                                        <td class="text-center font-weight-bold"><?= esc($top->unit) ?></td>
                                                        <td class="text-center font-weight-bold font-monospace"><?= esc($top->freq_count) ?> Resep</td>
                                                        <td class="text-center font-weight-bold text-teal font-monospace" style="font-size: 15px;">
                                                            <?= number_format($top->total_qty, 0, ',', '.') ?> <small class="lp-text-muted"><?= esc($top->unit) ?></small>
                                                        </td>
                                                        <td style="min-width: 180px; vertical-align: middle;">
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-teal" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="lp-text-muted"><?= $pct ?>% dari item terlaris</small>
                                                        </td>
                                                    </tr>
                                                    <?php $rank++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center lp-text-muted p-4">Belum ada riwayat peresepan obat yang selesai diserahkan.</td>
                                                </tr>
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

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for report tables
    $('.table-lp.datatable').each(function() {
        $(this).DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "🔍 Cari data laporan...",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data laporan",
                emptyTable: "Belum ada rekaman data pada periode yang dipilih",
                paginate: { first: "«", last: "»", next: "›", previous: "‹" }
            }
        });
    });

    // Auto adjust columns when switching tabs
    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
</script>
<?= $this->endSection() ?>
