<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">

        <!-- Top Metrics Cards -->
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-prescription-bottle-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Obat Diserahkan (Periode)</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= number_format($totalDispensedQty, 0, ',', '.') ?> <small class="text-muted">Unit</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-success elevation-0"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Nilai Penjualan Farmasi</span>
                        <span class="info-box-number text-teal h5 font-weight-bold mb-0">Rp <?= number_format($totalDispensedAmount, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-danger elevation-0"><i class="fas fa-hourglass-end"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Risiko Expired (&le; 90 Hari)</span>
                        <span class="info-box-number text-danger h4 mb-0"><?= $criticalCount + $warningCount ?> <small class="text-muted">Batch</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-info elevation-0"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold" style="font-size: 11px;">Aktivitas Mutasi Stok</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($movements) ?> <small class="text-muted">Transaksi</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Period & Actions Card -->
        <div class="card card-outline card-teal shadow-sm mb-3">
            <div class="card-body py-3">
                <form action="<?= base_url('apotek/laporan') ?>" method="get" class="row align-items-end">
                    <div class="col-md-3 form-group mb-md-0">
                        <label class="font-weight-bold text-xs text-secondary text-uppercase mb-1">Periode Dari Tanggal:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-md-3 form-group mb-md-0">
                        <label class="font-weight-bold text-xs text-secondary text-uppercase mb-1">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-md-3 form-group mb-md-0">
                        <label class="font-weight-bold text-xs text-secondary text-uppercase mb-1">Filter Item Obat (Opsional):</label>
                        <select name="medicine_id" class="form-control form-control-sm">
                            <option value="">-- Semua Item Obat --</option>
                            <?php foreach ($medicinesList as $med): ?>
                                <option value="<?= $med->id ?>" <?= $medFilter == $med->id ? 'selected' : '' ?>>
                                    [<?= esc($med->code) ?>] <?= esc($med->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-md-0 d-flex">
                        <button type="submit" class="btn btn-teal btn-sm font-weight-bold flex-grow-1 mr-1">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="<?= base_url('apotek/laporan') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Report Content Tabs -->
        <div class="card card-teal card-outline card-outline-tabs shadow">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-laporan" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-3" id="tab-dispensed-tab" data-toggle="pill" href="#tab-dispensed" role="tab" aria-controls="tab-dispensed" aria-selected="true">
                            <i class="fas fa-file-invoice text-teal mr-1"></i> 1. PEMAKAIAN & PENJUALAN RESEP
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-movements-tab" data-toggle="pill" href="#tab-movements" role="tab" aria-controls="tab-movements" aria-selected="false">
                            <i class="fas fa-dolly-flatbed text-info mr-1"></i> 2. KARTU STOK & MUTASI OBAT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-expired-tab" data-toggle="pill" href="#tab-expired" role="tab" aria-controls="tab-expired" aria-selected="false">
                            <i class="fas fa-calendar-times text-warning mr-1"></i> 3. MONITORING KADALUARSA (FEFO)
                            <?php if ($criticalCount > 0): ?>
                                <span class="badge badge-danger ml-1"><?= $criticalCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-3" id="tab-top-tab" data-toggle="pill" href="#tab-top" role="tab" aria-controls="tab-top" aria-selected="false">
                            <i class="fas fa-trophy text-teal mr-1"></i> 4. TOP 10 OBAT FAST-MOVING
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-laporanContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: PEMAKAIAN & PENJUALAN OBAT RESEP -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-dispensed" role="tabpanel" aria-labelledby="tab-dispensed-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-receipt text-teal mr-1"></i> Laporan Rincian Pemakaian & Penjualan Obat Pasien</h5>
                                <small class="text-muted">Periode: <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong></small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=pemakaian&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm">
                                    <i class="fas fa-print mr-1"></i> Cetak Laporan Pemakaian
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th style="width: 100px;" class="text-center">TANGGAL</th>
                                        <th>IDENTITAS PASIEN & DOKTER</th>
                                        <th style="width: 90px;">KODE</th>
                                        <th>NAMA OBAT & SATUAN</th>
                                        <th style="width: 80px;" class="text-center">QTY</th>
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
                                                <td class="text-center">
                                                    <strong><?= date('d/m/Y', strtotime($d->dispensed_date)) ?></strong><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($d->dispensed_date)) ?></small>
                                                </td>
                                                <td>
                                                    <strong class="text-dark"><?= esc($d->patient_name) ?></strong> <small class="badge badge-secondary"><?= esc($d->no_rm) ?></small><br>
                                                    <small class="text-muted"><i class="fas fa-user-md"></i> <?= esc($d->doctor_name) ?></small>
                                                </td>
                                                <td><span class="badge badge-light border"><?= esc($d->medicine_code) ?></span></td>
                                                <td>
                                                    <strong><?= esc($d->medicine_name) ?></strong><br>
                                                    <small class="text-muted">Satuan: <?= esc($d->unit) ?></small>
                                                </td>
                                                <td class="text-center font-weight-bold text-teal" style="font-size: 14px;">
                                                    <?= esc($d->qty) ?>
                                                </td>
                                                <td class="text-sm">
                                                    <span class="badge badge-light border text-dark"><?= esc($d->dosage) ?></span>
                                                </td>
                                                <td class="text-right">Rp <?= number_format($d->price, 0, ',', '.') ?></td>
                                                <td class="text-right font-weight-bold text-dark">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold" style="font-size: 14px;">
                                        <td colspan="5" class="text-right">TOTAL PENGELUARAN & PENJUALAN:</td>
                                        <td class="text-center text-teal"><?= number_format($totalDispensedQty, 0, ',', '.') ?></td>
                                        <td colspan="2"></td>
                                        <td class="text-right text-success">Rp <?= number_format($totalDispensedAmount, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: KARTU STOK & MUTASI OBAT -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-movements" role="tabpanel" aria-labelledby="tab-movements-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-exchange-alt text-info mr-1"></i> Log Mutasi & Kartu Stok Obat Farmasi</h5>
                                <small class="text-muted">Rekam jejak setiap penambahan, pengurangan resep, stock opname, dan retur obat.</small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=mutasi&start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-info btn-sm font-weight-bold shadow-sm">
                                    <i class="fas fa-print mr-1"></i> Cetak Kartu Stok
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 110px;" class="text-center">WAKTU</th>
                                        <th style="width: 90px;">KODE</th>
                                        <th>NAMA OBAT</th>
                                        <th style="width: 110px;">NO. BATCH</th>
                                        <th style="width: 110px;" class="text-center">TIPE TRANSAKSI</th>
                                        <th style="width: 80px;" class="text-center text-success">MASUK (+)</th>
                                        <th style="width: 80px;" class="text-center text-danger">KELUAR (-)</th>
                                        <th style="width: 90px;" class="text-center bg-light font-weight-bold">SALDO AKHIR</th>
                                        <th style="width: 100px;">PETUGAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($movements)): ?>
                                        <?php foreach ($movements as $m): ?>
                                            <tr>
                                                <td class="text-center text-sm">
                                                    <strong><?= date('d/m/Y', strtotime($m->created_at)) ?></strong><br>
                                                    <small class="text-muted"><?= date('H:i:s', strtotime($m->created_at)) ?></small>
                                                </td>
                                                <td><span class="badge badge-secondary"><?= esc($m->medicine_code) ?></span></td>
                                                <td>
                                                    <strong><?= esc($m->medicine_name) ?></strong><br>
                                                    <small class="text-muted">Satuan: <?= esc($m->unit) ?></small>
                                                </td>
                                                <td>
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
                                                <td class="text-center font-weight-bold text-success">
                                                    <?= $m->qty_in > 0 ? '+' . $m->qty_in : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-danger">
                                                    <?= $m->qty_out > 0 ? '-' . $m->qty_out : '-' ?>
                                                </td>
                                                <td class="text-center font-weight-bold text-dark bg-light" style="font-size: 14px;">
                                                    <?= esc($m->balance) ?>
                                                </td>
                                                <td class="text-sm">
                                                    <i class="fas fa-user-circle text-muted mr-1"></i> <?= esc($m->user_name ?? 'Sistem') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: MONITORING KADALUARSA (FEFO) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-expired" role="tabpanel" aria-labelledby="tab-expired-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-hourglass-half text-warning mr-1"></i> Monitoring Kadaluarsa Obat & Analisis FEFO (First-Expired, First-Out)</h5>
                                <small class="text-muted">Deteksi dini obat yang mendekati masa kadaluarsa untuk tindakan retur distributor atau promosi resep dokter.</small>
                            </div>
                            <div>
                                <a href="<?= base_url('apotek/cetak-laporan?type=expired') ?>" target="_blank" class="btn btn-outline-warning btn-sm font-weight-bold shadow-sm">
                                    <i class="fas fa-print mr-1"></i> Cetak Monitoring Expired
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th style="width: 90px;">KODE</th>
                                        <th>NAMA OBAT & GOLONGAN</th>
                                        <th style="width: 120px;">NO. BATCH</th>
                                        <th style="width: 110px;" class="text-center">TGL EXPIRED</th>
                                        <th style="width: 120px;" class="text-center">SISA HARI</th>
                                        <th style="width: 90px;" class="text-center">STOK FISIK</th>
                                        <th style="width: 130px;" class="text-center">STATUS RISIKO</th>
                                        <th>REKOMENDASI AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($expiryBatches)): ?>
                                        <?php $no = 1; foreach ($expiryBatches as $eb): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td><span class="badge badge-secondary"><?= esc($eb->medicine_code) ?></span></td>
                                                <td>
                                                    <strong><?= esc($eb->medicine_name) ?></strong><br>
                                                    <small class="text-muted">Golongan: <span class="badge badge-light border"><?= strtoupper(esc($eb->type)) ?></span> | Satuan: <?= esc($eb->unit) ?></small>
                                                </td>
                                                <td><strong><?= esc($eb->batch_no) ?></strong></td>
                                                <td class="text-center font-weight-bold">
                                                    <?= date('d/m/Y', strtotime($eb->expired_date)) ?>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?php if ($eb->days_left <= 0): ?>
                                                        <span class="text-danger font-weight-bold">Expired (Lewat <?= abs($eb->days_left) ?> Hari)</span>
                                                    <?php else: ?>
                                                        <span class="<?= $eb->days_left <= 30 ? 'text-danger' : ($eb->days_left <= 90 ? 'text-warning font-weight-bold' : 'text-success') ?>">
                                                            <?= $eb->days_left ?> Hari Lagi
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center font-weight-bold" style="font-size: 14px;">
                                                    <?= esc($eb->stock) ?> <small class="text-muted"><?= esc($eb->unit) ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($eb->risk_status === 'expired'): ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-ban"></i> EXPIRED</span>
                                                    <?php elseif ($eb->risk_status === 'critical'): ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> KRITIS (&le; 30 Hari)</span>
                                                    <?php elseif ($eb->risk_status === 'warning'): ?>
                                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock"></i> WASPADA (&le; 90 Hari)</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-shield-alt"></i> AMAN</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-sm">
                                                    <?php if ($eb->risk_status === 'expired'): ?>
                                                        <strong class="text-danger"><i class="fas fa-trash-alt"></i> Karantina & Musnahkan / Retur Segera</strong>
                                                    <?php elseif ($eb->risk_status === 'critical'): ?>
                                                        <strong class="text-danger"><i class="fas fa-undo"></i> Ajukan Retur Supplier / FEFO Prioritas</strong>
                                                    <?php elseif ($eb->risk_status === 'warning'): ?>
                                                        <span class="text-warning font-weight-bold"><i class="fas fa-arrow-up"></i> FEFO: Habiskan Batch Ini Dahulu</span>
                                                    <?php else: ?>
                                                        <span class="text-muted"><i class="fas fa-check"></i> Distribusi Normal Sesuai Resep</span>
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
                    <!-- TAB 4: TOP 10 FAST-MOVING MEDICINES -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-top" role="tabpanel" aria-labelledby="tab-top-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-chart-pie text-teal mr-1"></i> Analisis Ranking 10 Besar Obat Fast-Moving (Paling Sering Diresepkan)</h5>
                                <small class="text-muted">Data agregasi pemakaian kumulatif untuk acuan perencanaan pengadaan obat (Procurement / PO).</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">RANK</th>
                                                <th style="width: 100px;">KODE</th>
                                                <th>NAMA OBAT</th>
                                                <th style="width: 120px;" class="text-center">GOLONGAN</th>
                                                <th style="width: 100px;" class="text-center">SATUAN</th>
                                                <th style="width: 140px;" class="text-center">FREKUENSI RESEP</th>
                                                <th style="width: 150px;" class="text-center bg-teal-light font-weight-bold">TOTAL QTY KELUAR</th>
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
                                                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="fas fa-crown"></i> #1</span>
                                                            <?php elseif ($rank === 2): ?>
                                                                <span class="badge badge-secondary font-weight-bold px-2 py-1">#2</span>
                                                            <?php elseif ($rank === 3): ?>
                                                                <span class="badge badge-brown font-weight-bold px-2 py-1" style="background:#cd7f32; color:#fff;">#3</span>
                                                            <?php else: ?>
                                                                <span class="text-muted font-weight-bold">#<?= $rank ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><span class="badge badge-secondary"><?= esc($top->code) ?></span></td>
                                                        <td><strong class="text-dark" style="font-size: 14px;"><?= esc($top->name) ?></strong></td>
                                                        <td class="text-center"><span class="badge badge-light border"><?= strtoupper(esc($top->type)) ?></span></td>
                                                        <td class="text-center font-weight-bold"><?= esc($top->unit) ?></td>
                                                        <td class="text-center font-weight-bold"><?= esc($top->freq_count) ?> Kali Resep</td>
                                                        <td class="text-center font-weight-bold text-teal" style="font-size: 16px;">
                                                            <?= number_format($top->total_qty, 0, ',', '.') ?> <small class="text-muted"><?= esc($top->unit) ?></small>
                                                        </td>
                                                        <td style="min-width: 180px; vertical-align: middle;">
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-teal" role="progressbar" style="width: <?= $pct ?>%" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <small class="text-muted"><?= $pct ?>% dari item terbanyak</small>
                                                        </td>
                                                    </tr>
                                                    <?php $rank++; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted p-4">Belum ada riwayat peresepan obat yang selesai diserahkan.</td>
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
<?= $this->endSection() ?>
