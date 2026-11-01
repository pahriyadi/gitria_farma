<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Aging Report Paper White / Paper Dark */
:root {
    --aging-bg: #ffffff;
    --aging-text: #1f2937;
    --aging-muted: #6b7280;
    --aging-border: #b8b8b8;
    --aging-card-bg: #ffffff;
    --aging-head-bg: #f8fafc;
    --aging-hover-bg: #f1f5f9;
    --aging-box-bg: #f8fafc;
}

body.dark-mode {
    --aging-bg: #14171a;
    --aging-text: #f0f6fc;
    --aging-muted: #9ca3af;
    --aging-border: #374151;
    --aging-card-bg: #1f2328;
    --aging-head-bg: #181b1f;
    --aging-hover-bg: #272c33;
    --aging-box-bg: #181b1f;
}

.aging-card {
    background-color: var(--aging-card-bg) !important;
    border: 1px solid var(--aging-border) !important;
    color: var(--aging-text) !important;
    border-radius: 8px;
}

.aging-box {
    background-color: var(--aging-box-bg) !important;
    border: 1px solid var(--aging-border) !important;
    border-radius: 8px;
    padding: 12px 14px;
}

.table-aging {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--aging-border) !important;
    color: var(--aging-text) !important;
    background-color: var(--aging-card-bg) !important;
}

.table-aging th,
.table-aging td {
    border: 1px solid var(--aging-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-aging thead th {
    background-color: var(--aging-head-bg) !important;
    color: var(--aging-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-aging tbody tr:hover {
    background-color: var(--aging-hover-bg) !important;
}

.nav-pills .nav-link {
    color: var(--aging-muted) !important;
    background-color: transparent !important;
    border: 1px solid var(--aging-border) !important;
    border-radius: 6px;
    margin-right: 8px;
}

.nav-pills .nav-link.active {
    color: #ffffff !important;
    background-color: #0d9488 !important;
    border-color: #0d9488 !important;
}
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-lg-7 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--aging-text);">
            <i class="fas fa-hourglass-half text-teal mr-2"></i> Jadwal Umur Piutang &amp; Hutang (Aging Schedule)
        </h4>
        <small style="color: var(--aging-muted);">Analisis jatuh tempo piutang pasien/asuransi dan hutang supplier farmasi (0-30 hari, 31-60 hari, 61-90 hari, &amp; &gt;90 hari)</small>
    </div>
    <div class="col-lg-5 col-md-12 text-lg-right">
        <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" onclick="window.print();">
            <i class="fas fa-print mr-1"></i> Cetak Laporan Aging
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card aging-card shadow-sm mb-4">
            <div class="card-body p-4">
                
                <!-- Nav Tabs: Piutang Pasien/Asuransi vs Hutang Supplier -->
                <ul class="nav nav-pills mb-4" id="agingTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold px-4 shadow-sm" id="piutang-tab" data-toggle="tab" href="#tab-piutang" role="tab">
                            <i class="fas fa-hand-holding-dollar mr-1"></i> 1. Aging Piutang Pasien &amp; Asuransi (AR)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold px-4 shadow-sm" id="hutang-tab" data-toggle="tab" href="#tab-hutang" role="tab">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> 2. Aging Hutang Supplier &amp; Vendor (AP)
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
                                <div class="aging-box text-center" style="border-top: 4px solid #10b981 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">0 - 30 HARI (LANCAR)</small>
                                    <strong class="text-success h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['0_30'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #3b82f6 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">31 - 60 HARI (PERHATIAN)</small>
                                    <strong class="text-primary h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['31_60'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #f59e0b !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">61 - 90 HARI (KURANG LANCAR)</small>
                                    <strong class="text-warning h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['61_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #ef4444 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">&gt; 90 HARI (MACET / KRITIS)</small>
                                    <strong class="text-danger h6 font-weight-bold mb-0">Rp <?= number_format($piutangSummary['over_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table-aging mb-0 text-sm" id="tableAgingPiutang">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">NO</th>
                                        <th>NO. BILLING / INVOICE</th>
                                        <th>NAMA PASIEN / PENJAMIN</th>
                                        <th class="text-center">TANGGAL BILLING</th>
                                        <th class="text-center">UMUR PIUTANG</th>
                                        <th class="text-right">TOTAL TAGIHAN</th>
                                        <th class="text-right">SUDAH DIBAYAR</th>
                                        <th class="text-right font-weight-bold text-danger">SISA PIUTANG</th>
                                        <th class="text-center">KATEGORI AGING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $hasPiutang = false;
                                    foreach ($piutangList as $p): 
                                        $sisa = $p->total_amount - $p->paid_amount;
                                        if ($sisa <= 0) continue;
                                        $hasPiutang = true;
                                        $days = (int)$p->age_days;
                                        $badge = 'success'; $label = '0-30 Hari';
                                        if ($days > 90) { $badge = 'danger'; $label = '>90 Hari'; }
                                        elseif ($days > 60) { $badge = 'warning'; $label = '61-90 Hari'; }
                                        elseif ($days > 30) { $badge = 'primary'; $label = '31-60 Hari'; }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="font-weight-bold text-xs"><code style="color: #0d9488;"><?= esc($p->billing_no) ?></code></td>
                                            <td>
                                                <strong style="color: var(--aging-text);"><?= esc($p->patient_name ?? 'Pasien Umum') ?></strong>
                                                <small style="color: var(--aging-muted);" class="d-block">No RM: <?= esc($p->no_rm ?? '-') ?></small>
                                            </td>
                                            <td class="text-center text-xs"><?= date('d/m/Y', strtotime($p->created_at)) ?></td>
                                            <td class="text-center font-weight-bold"><?= $days ?> Hari</td>
                                            <td class="text-right">Rp <?= number_format($p->total_amount, 0, ',', '.') ?></td>
                                            <td class="text-right" style="color: var(--aging-muted);">Rp <?= number_format($p->paid_amount, 0, ',', '.') ?></td>
                                            <td class="text-right text-danger font-weight-bold">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $badge ?> px-2 py-1 font-weight-bold"><?= $label ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (!$hasPiutang): ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4" style="color: var(--aging-muted);">
                                                <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada piutang pasien tertunggak.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                                <div class="aging-box text-center" style="border-top: 4px solid #10b981 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">0 - 30 HARI (BELUM JATUH TEMPO)</small>
                                    <strong class="text-success h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['0_30'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #3b82f6 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">31 - 60 HARI (JATUH TEMPO DEKAT)</small>
                                    <strong class="text-primary h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['31_60'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #f59e0b !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">61 - 90 HARI (TERTUNGGAK)</small>
                                    <strong class="text-warning h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['61_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="aging-box text-center" style="border-top: 4px solid #ef4444 !important;">
                                    <small class="d-block text-xs font-weight-bold" style="color: var(--aging-muted);">&gt; 90 HARI (SANGAT KRITIS)</small>
                                    <strong class="text-danger h6 font-weight-bold mb-0">Rp <?= number_format($hutangSummary['over_90'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table-aging mb-0 text-sm" id="tableAgingHutang">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">NO</th>
                                        <th>NO. PURCHASE ORDER (PO)</th>
                                        <th>NAMA SUPPLIER / DISTRIBUTOR</th>
                                        <th class="text-center">TANGGAL PO</th>
                                        <th class="text-center">UMUR HUTANG</th>
                                        <th class="text-right">TOTAL INVOICE</th>
                                        <th class="text-right text-danger font-weight-bold">SISA HUTANG</th>
                                        <th class="text-center">KATEGORI AGING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    if (empty($hutangList)):
                                    ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4" style="color: var(--aging-muted);">
                                                <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada hutang supplier tertunggak.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($hutangList as $h): 
                                            $days = (int)$h->age_days;
                                            $badge = 'success'; $label = '0-30 Hari';
                                            if ($days > 90) { $badge = 'danger'; $label = '>90 Hari'; }
                                            elseif ($days > 60) { $badge = 'warning'; $label = '61-90 Hari'; }
                                            elseif ($days > 30) { $badge = 'primary'; $label = '31-60 Hari'; }
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td class="font-weight-bold text-xs"><code style="color: #0d9488;"><?= esc($h->po_no ?? ($h->po_number ?? 'PO-AUTO')) ?></code></td>
                                                <td>
                                                    <strong style="color: var(--aging-text);"><?= esc($h->supplier_name ?? 'Vendor Supplier') ?></strong>
                                                    <small style="color: var(--aging-muted);" class="d-block"><?= esc($h->supplier_phone ?? '-') ?></small>
                                                </td>
                                                <td class="text-center text-xs"><?= date('d/m/Y', strtotime($h->order_date ?? ($h->po_date ?? ($h->created_at ?? date('Y-m-d'))))) ?></td>
                                                <td class="text-center font-weight-bold"><?= $days ?> Hari</td>
                                                <td class="text-right font-weight-bold">Rp <?= number_format($h->total_amount ?? ($h->grand_total ?? 0), 0, ',', '.') ?></td>
                                                <td class="text-right text-danger font-weight-bold">Rp <?= number_format($h->total_amount ?? ($h->grand_total ?? 0), 0, ',', '.') ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $badge ?> px-2 py-1 font-weight-bold"><?= $label ?></span>
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
        </div>
    </div>
</div>
<?= $this->endSection() ?>
