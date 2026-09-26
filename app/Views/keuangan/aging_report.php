<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-none" style="border: 1px solid #b8b8b8 !important; background: #ffffff !important; border-radius: 4px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-hourglass-half text-teal mr-1"></i> BUKU PEMBANTU & JADWAL UMUR PIUTANG / HUTANG (AGING SCHEDULE)
                    </h5>
                    <small class="text-muted">Analisis jatuh tempo piutang pasien/asuransi dan hutang supplier farmasi (0-30 hari, 31-60 hari, 61-90 hari, & >90 hari)</small>
                </div>
                <div>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Cetak Laporan Aging
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Nav Tabs: Piutang Pasien/Asuransi vs Hutang Supplier -->
                <ul class="nav nav-pills mb-4" id="agingTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold px-4" id="piutang-tab" data-toggle="tab" href="#tab-piutang" role="tab">
                            <i class="fas fa-hand-holding-dollar mr-1"></i> 1. Aging Piutang Pasien & Asuransi (AR)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold px-4" id="hutang-tab" data-toggle="tab" href="#tab-hutang" role="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> 2. Aging Hutang Supplier & Vendor (AP)
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="agingTabContent">
                    <!-- ============================================================= -->
                    <!-- TAB 1: AGING PIUTANG PASIEN & ASURANSI -->
                    <!-- ============================================================= -->
                    <div class="tab-pane fade show active" id="tab-piutang" role="tabpanel">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #10b981 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">0 - 30 HARI (LANCAR)</small>
                                    <strong class="text-success h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['0_30'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #3b82f6 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">31 - 60 HARI (PERHATIAN)</small>
                                    <strong class="text-primary h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['31_60'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #f59e0b !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">61 - 90 HARI (KURANG LANCAR)</small>
                                    <strong class="text-warning h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['61_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #ef4444 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">> 90 HARI (MACET / KRITIS)</small>
                                    <strong class="text-danger h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['over_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NO. BILLING / INVOICE</th>
                                        <th>NAMA PASIEN / PENJAMIN</th>
                                        <th>TANGGAL BILLING</th>
                                        <th>UMUR PIUTANG</th>
                                        <th class="text-right">TOTAL TAGIHAN</th>
                                        <th class="text-right">SUDAH DIBAYAR</th>
                                        <th class="text-right text-danger font-weight-bold">SISA PIUTANG</th>
                                        <th class="text-center">KATEGORI AGING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    foreach ($piutangList as $p): 
                                        $sisa = $p->total_amount - $p->paid_amount;
                                        if ($sisa <= 0) continue;
                                        $days = (int)$p->age_days;
                                        $badge = 'success'; $label = '0-30 Hari';
                                        if ($days > 90) { $badge = 'danger'; $label = '>90 Hari'; }
                                        elseif ($days > 60) { $badge = 'warning'; $label = '61-90 Hari'; }
                                        elseif ($days > 30) { $badge = 'primary'; $label = '31-60 Hari'; }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="font-weight-bold text-xs"><code><?= esc($p->billing_no) ?></code></td>
                                            <td>
                                                <strong class="text-dark"><?= esc($p->patient_name ?? 'Pasien Umum') ?></strong>
                                                <small class="text-muted d-block">No RM: <?= esc($p->no_rm ?? '-') ?></small>
                                            </td>
                                            <td class="text-center text-xs"><?= date('d/m/Y', strtotime($p->created_at)) ?></td>
                                            <td class="text-center font-weight-bold"><?= $days ?> Hari</td>
                                            <td class="text-right">Rp <?= number_format($p->total_amount, 0, ',', '.') ?></td>
                                            <td class="text-right text-muted">Rp <?= number_format($p->paid_amount, 0, ',', '.') ?></td>
                                            <td class="text-right text-danger font-weight-bold">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $badge ?> px-2 py-1"><?= $label ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============================================================= -->
                    <!-- TAB 2: AGING HUTANG SUPPLIER & VENDOR -->
                    <!-- ============================================================= -->
                    <div class="tab-pane fade" id="tab-hutang" role="tabpanel">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #10b981 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">0 - 30 HARI (BELUM JATUH TEMPO)</small>
                                    <strong class="text-success h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['0_30'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #3b82f6 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">31 - 60 HARI (JATUH TEMPO DEKAT)</small>
                                    <strong class="text-primary h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['31_60'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #f59e0b !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">61 - 90 HARI (TERTUNGGAK)</small>
                                    <strong class="text-warning h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['61_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 4px solid #ef4444 !important;">
                                    <small class="text-muted d-block text-xs font-weight-bold">> 90 HARI (SANGAT KRITIS)</small>
                                    <strong class="text-danger h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['over_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NO. PURCHASE ORDER (PO)</th>
                                        <th>NAMA SUPPLIER / DISTRIBUTOR</th>
                                        <th>TANGGAL PO</th>
                                        <th>UMUR HUTANG</th>
                                        <th class="text-right">TOTAL INVOICE</th>
                                        <th class="text-right text-danger font-weight-bold">SISA HUTANG</th>
                                        <th class="text-center">KATEGORI AGING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    foreach ($hutangList as $h): 
                                        $days = (int)$h->age_days;
                                        $badge = 'success'; $label = '0-30 Hari';
                                        if ($days > 90) { $badge = 'danger'; $label = '>90 Hari'; }
                                        elseif ($days > 60) { $badge = 'warning'; $label = '61-90 Hari'; }
                                        elseif ($days > 30) { $badge = 'primary'; $label = '31-60 Hari'; }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="font-weight-bold text-xs"><code><?= esc($h->po_no ?? ($h->po_number ?? 'PO-AUTO')) ?></code></td>
                                            <td>
                                                <strong class="text-dark"><?= esc($h->supplier_name ?? 'Vendor Supplier') ?></strong>
                                                <small class="text-muted d-block"><?= esc($h->supplier_phone ?? '-') ?></small>
                                            </td>
                                            <td class="text-center text-xs"><?= date('d/m/Y', strtotime($h->order_date ?? ($h->po_date ?? ($h->created_at ?? date('Y-m-d'))))) ?></td>
                                            <td class="text-center font-weight-bold"><?= $days ?> Hari</td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($h->total_amount ?? ($h->grand_total ?? 0), 0, ',', '.') ?></td>
                                            <td class="text-right text-danger font-weight-bold">Rp <?= number_format($h->total_amount ?? ($h->grand_total ?? 0), 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $badge ?> px-2 py-1"><?= $label ?></span>
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
</div>
<?= $this->endSection() ?>
