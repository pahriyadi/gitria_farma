<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Rekap Harian Paper White / Paper Dark */
:root {
    --rekap-bg: #ffffff;
    --rekap-text: #1f2937;
    --rekap-muted: #6b7280;
    --rekap-border: #b8b8b8;
    --rekap-card-bg: #ffffff;
    --rekap-head-bg: #f8fafc;
    --rekap-hover-bg: #f1f5f9;
    --rekap-input-bg: #ffffff;
    --rekap-box-bg: #f8fafc;
}

body.dark-mode {
    --rekap-bg: #14171a;
    --rekap-text: #f0f6fc;
    --rekap-muted: #9ca3af;
    --rekap-border: #374151;
    --rekap-card-bg: #1f2328;
    --rekap-head-bg: #181b1f;
    --rekap-hover-bg: #272c33;
    --rekap-input-bg: #181b1f;
    --rekap-box-bg: #181b1f;
}

.rekap-card {
    background-color: var(--rekap-card-bg) !important;
    border: 1px solid var(--rekap-border) !important;
    color: var(--rekap-text) !important;
    border-radius: 8px;
}

.rekap-box {
    background-color: var(--rekap-box-bg) !important;
    border: 1px solid var(--rekap-border) !important;
    border-radius: 8px;
    padding: 12px 14px;
}

.table-rekap {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--rekap-border) !important;
    color: var(--rekap-text) !important;
    background-color: var(--rekap-card-bg) !important;
}

.table-rekap th,
.table-rekap td {
    border: 1px solid var(--rekap-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-rekap thead th {
    background-color: var(--rekap-head-bg) !important;
    color: var(--rekap-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-rekap tbody tr:hover {
    background-color: var(--rekap-hover-bg) !important;
}

.rekap-input {
    background-color: var(--rekap-input-bg) !important;
    border: 1px solid var(--rekap-border) !important;
    color: var(--rekap-text) !important;
}

.rekap-input:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25) !important;
}
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--rekap-text);">
            <i class="fas fa-calendar-check text-teal mr-2"></i> Rekap Kasir &amp; Laporan Tutup Shift
        </h4>
        <small style="color: var(--rekap-muted);">Rekapitulasi penerimaan kasir, rincian metode pembayaran, dan rekonsiliasi kas fisik harian</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <a href="<?= base_url('keuangan/kasir') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-cash-register mr-1"></i> Buka Kasir Pembayaran
            </a>
            <a href="<?= base_url('keuangan/cetak-rekap-harian?date=' . $date) ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak Rekap Shift Kasir
            </a>
        </div>
    </div>
</div>

<!-- FILTER DATE BAR -->
<div class="card rekap-card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="<?= base_url('keuangan/rekap-harian') ?>" class="form-row align-items-end">
            <div class="col-md-4">
                <label class="font-weight-bold mb-1" style="font-size: 13px; color: var(--rekap-text);">Pilih Tanggal Laporan Kasir:</label>
                <input type="date" name="date" class="form-control form-control-sm font-weight-bold rekap-input" value="<?= esc($date) ?>" required>
            </div>
            <div class="col-md-8 text-right mt-3 mt-md-0">
                <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-3 shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Tampilkan Rekap
                </button>
                <a href="<?= base_url('keuangan/rekap-harian') ?>" class="btn btn-outline-secondary btn-sm px-3 ml-1 shadow-sm">
                    <i class="fas fa-rotate-left mr-1"></i> Hari Ini
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 4 KPI SUMMARY CARDS -->
<div class="row mb-3">
    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
        <div class="card rekap-card h-100 shadow-sm" style="border-left: 4px solid #0d9488 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-teal text-uppercase">Total Penerimaan Kasir</span>
                        <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rekap-text);">Rp <?= number_format($totalCollected, 0, ',', '.') ?></h4>
                        <small style="color: var(--rekap-muted);"><?= count($transactions) ?> Kwitansi Selesai</small>
                    </div>
                    <div class="p-3 rounded-circle text-teal" style="background-color: var(--rekap-head-bg);">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
        <div class="card rekap-card h-100 shadow-sm" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-success text-uppercase">Pembayaran Tunai (Cash)</span>
                        <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rekap-text);">Rp <?= number_format($totalCash, 0, ',', '.') ?></h4>
                        <small style="color: var(--rekap-muted);">Fisik uang masuk laci kasir</small>
                    </div>
                    <div class="p-3 rounded-circle text-success" style="background-color: var(--rekap-head-bg);">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
        <div class="card rekap-card h-100 shadow-sm" style="border-left: 4px solid #0284c7 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-info text-uppercase">Non-Tunai (QRIS &amp; Bank)</span>
                        <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rekap-text);">Rp <?= number_format($totalQris + $totalBank, 0, ',', '.') ?></h4>
                        <small style="color: var(--rekap-muted);">QRIS: <?= number_format($totalQris, 0, ',', '.') ?> | Bank: <?= number_format($totalBank, 0, ',', '.') ?></small>
                    </div>
                    <div class="p-3 rounded-circle text-info" style="background-color: var(--rekap-head-bg);">
                        <i class="fas fa-qrcode fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card rekap-card h-100 shadow-sm" style="border-left: 4px solid #ef4444 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-danger text-uppercase">Pengeluaran Kas Kecil</span>
                        <h4 class="font-weight-bold mb-0 mt-1" style="color: var(--rekap-text);">Rp <?= number_format($totalExpenses, 0, ',', '.') ?></h4>
                        <small style="color: var(--rekap-muted);"><?= count($expenses) ?> Transaksi Beban</small>
                    </div>
                    <div class="p-3 rounded-circle text-danger" style="background-color: var(--rekap-head-bg);">
                        <i class="fas fa-arrow-trend-down fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REKONSILIASI KAS FISIK & RINCIAN UNIT BISNIS -->
<div class="row mb-3">
    <!-- Cash Reconciliation Summary -->
    <div class="col-md-6 mb-3">
        <div class="card rekap-card h-100 shadow-sm">
            <div class="card-header border-bottom py-3" style="background-color: var(--rekap-head-bg); border-color: var(--rekap-border) !important;">
                <h6 class="font-weight-bold mb-0" style="color: var(--rekap-text);">
                    <i class="fas fa-calculator text-teal mr-2"></i> Rekonsiliasi Saldo Kas Fisik Kasir (Cash Count)
                </h6>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless mb-0" style="font-size: 14px; color: var(--rekap-text);">
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--rekap-border);">
                            <td class="py-2" style="color: var(--rekap-muted);">Penerimaan Uang Tunai Hari Ini</td>
                            <td class="text-right font-weight-bold text-success py-2">+ Rp <?= number_format($totalCash, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--rekap-border);">
                            <td class="py-2" style="color: var(--rekap-muted);">Pengeluaran Kas Kecil Operasional</td>
                            <td class="text-right font-weight-bold text-danger py-2">- Rp <?= number_format($totalExpenses, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--rekap-border); background-color: var(--rekap-head-bg);">
                            <td class="font-weight-bold py-2">Arus Kas Bersih Hari Ini (Net Cash Flow)</td>
                            <td class="text-right font-weight-bold text-teal py-2">Rp <?= number_format($totalCash - $totalExpenses, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold py-2">Saldo Akhir Buku Kasir Utama (Laci Kas)</td>
                            <td class="text-right font-weight-bold h5 mb-0 py-2">Rp <?= number_format($regBalance, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Live Physical Cash Count & Auto-Reconcile Tool -->
                <div class="mt-3 pt-3" style="border-top: 1px solid var(--rekap-border);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-xs font-weight-bold text-teal text-uppercase"><i class="fas fa-coins mr-1"></i> Hitung Lembaran Fisik Uang Kasir (Cash Counter):</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary shadow-sm" id="btn-reset-cash-calc"><i class="fas fa-rotate-left mr-1"></i> Reset</button>
                    </div>
                    <div class="row text-xs">
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 100.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="100000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 50.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="50000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 20.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="20000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 10.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="10000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 5.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="5000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 2.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="2000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Rp 1.000 (Lbr):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-denom font-weight-bold rekap-input" data-val="1000" placeholder="0">
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <label class="font-weight-bold mb-0" style="color: var(--rekap-text);">Koin / Logam (Rp):</label>
                            <input type="number" min="0" class="form-control form-control-sm text-center input-cash-coin font-weight-bold rekap-input" placeholder="0">
                        </div>
                    </div>
                    <!-- Result Comparison Box -->
                    <div class="p-2 mt-2 rounded border d-flex justify-content-between align-items-center" style="background-color: var(--rekap-head-bg); border-color: var(--rekap-border) !important;">
                        <div>
                            <small class="d-block" style="font-size: 11px; color: var(--rekap-muted);">Total Fisik Terhitung:</small>
                            <strong class="h6 mb-0" id="lbl-physical-total" style="color: var(--rekap-text);">Rp 0</strong>
                        </div>
                        <div class="text-right">
                            <small class="d-block" style="font-size: 11px; color: var(--rekap-muted);">Hasil Rekonsiliasi Kas:</small>
                            <span id="badge-cash-reconcile" class="badge badge-secondary px-2 py-1 font-weight-bold">Masukkan Hitungan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown by Business Unit -->
    <div class="col-md-6 mb-3">
        <div class="card rekap-card h-100 shadow-sm">
            <div class="card-header border-bottom py-3" style="background-color: var(--rekap-head-bg); border-color: var(--rekap-border) !important;">
                <h6 class="font-weight-bold mb-0" style="color: var(--rekap-text);">
                    <i class="fas fa-chart-pie text-teal mr-2"></i> Kontribusi Pendapatan Unit Layanan
                </h6>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless mb-0" style="font-size: 14px; color: var(--rekap-text);">
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--rekap-border);">
                            <td class="py-2">
                                <i class="fas fa-stethoscope text-primary mr-2"></i> <strong>Jasa Medis &amp; Poliklinik</strong>
                            </td>
                            <td class="text-right font-weight-bold py-2">Rp <?= number_format($totalServices, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--rekap-border);">
                            <td class="py-2">
                                <i class="fas fa-pills text-info mr-2"></i> <strong>Apotek &amp; Farmasi Resep</strong>
                            </td>
                            <td class="text-right font-weight-bold py-2">Rp <?= number_format($totalMedicines, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--rekap-border);">
                            <td class="py-2">
                                <i class="fas fa-utensils text-success mr-2"></i> <strong>Resto Gizi &amp; Skincare Sehat</strong>
                            </td>
                            <td class="text-right font-weight-bold py-2">Rp <?= number_format($totalRestaurant, 0, ',', '.') ?></td>
                        </tr>
                        <tr style="background-color: var(--rekap-head-bg);">
                            <td class="font-weight-bold py-2">TOTAL PENDAPATAN KOTOR</td>
                            <td class="text-right font-weight-bold text-teal h5 mb-0 py-2">Rp <?= number_format($totalServices + $totalMedicines + $totalRestaurant, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DETAILED TRANSACTIONS TABLE -->
<div class="card rekap-card shadow-sm mb-4">
    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center" style="background-color: var(--rekap-head-bg); border-color: var(--rekap-border) !important;">
        <h6 class="font-weight-bold mb-0" style="color: var(--rekap-text);">
            <i class="fas fa-list text-teal mr-2"></i> Rincian Seluruh Kwitansi Pembayaran Kasir (Tanggal: <?= date('d/m/Y', strtotime($date)) ?>)
        </h6>
        <span class="badge badge-teal font-weight-bold"><?= count($transactions) ?> Transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-rekap mb-0 text-sm" id="tableTransactions">
                <thead>
                    <tr>
                        <th style="width: 140px;" class="text-center">No. Kwitansi</th>
                        <th style="width: 80px;" class="text-center">Waktu</th>
                        <th>Nama Pasien &amp; No. RM</th>
                        <th class="text-right">Jasa Medis</th>
                        <th class="text-right">Farmasi</th>
                        <th class="text-right">Resto Gizi</th>
                        <th class="text-right text-success">Total Bayar</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Kasir</th>
                        <th style="width: 90px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td class="text-center font-weight-bold text-teal">
                                    <?= esc($t->receipt_no) ?>
                                    <small class="text-muted d-block"><?= esc($t->billing_no) ?></small>
                                </td>
                                <td class="text-center"><?= date('H:i', strtotime($t->created_at)) ?> WITA</td>
                                <td>
                                    <strong><?= esc($t->patient_name ?? 'Pasien Umum') ?></strong>
                                    <small class="badge badge-secondary ml-1"><?= esc($t->no_rm ?? '-') ?></small>
                                </td>
                                <td class="text-right">Rp <?= number_format($t->total_services, 0, ',', '.') ?></td>
                                <td class="text-right">Rp <?= number_format($t->total_medicines, 0, ',', '.') ?></td>
                                <td class="text-right">Rp <?= number_format($t->total_restaurant, 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold" style="font-size: 13.5px;">
                                    Rp <?= number_format($t->amount, 0, ',', '.') ?>
                                </td>
                                <td class="text-center text-uppercase font-weight-bold" style="font-size: 11px;">
                                    <span class="badge badge-light border"><?= esc($t->payment_method) ?></span>
                                </td>
                                <td class="text-center"><?= esc($t->cashier_name ?: 'Kasir Utama') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('keuangan/cetak-kwitansi/' . $t->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold shadow-sm" title="Cetak Kwitansi">
                                        <i class="fas fa-print"></i> Kwitansi
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4" style="color: var(--rekap-muted);">Belum ada transaksi pembayaran pada tanggal ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PHARMACY SALES TABLE (OTC & DIRECT PRESCRIPTION) -->
<div class="card rekap-card shadow-sm mb-4">
    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center" style="background-color: var(--rekap-head-bg); border-color: var(--rekap-border) !important;">
        <div>
            <h6 class="font-weight-bold mb-0" style="color: var(--rekap-text);">
                <i class="fas fa-prescription-bottle-alt text-teal mr-2"></i> Rincian Penjualan Apotek Farmasi &amp; Resep (Tanggal: <?= date('d/m/Y', strtotime($date)) ?>)
            </h6>
            <small style="color: var(--rekap-muted);">Total Omzet: <strong>Rp <?= number_format($totalPharmacySales ?? 0, 0, ',', '.') ?></strong> | Tusla: <strong>Rp <?= number_format($totalPharmacyTusla ?? 0, 0, ',', '.') ?></strong> | Embalase: <strong>Rp <?= number_format($totalPharmacyEmbalase ?? 0, 0, ',', '.') ?></strong> | Fee Dokter: <strong>Rp <?= number_format($totalPharmacyFeeDoc ?? 0, 0, ',', '.') ?></strong></small>
        </div>
        <span class="badge badge-teal font-weight-bold"><?= count($pharmacySales ?? []) ?> Penjualan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-rekap mb-0 text-sm" id="tablePharmacySales">
                <thead>
                    <tr>
                        <th style="width: 140px;" class="text-center">No. Transaksi</th>
                        <th style="width: 80px;" class="text-center">Waktu</th>
                        <th>Pembeli / Pasien</th>
                        <th>Dokter / Resep</th>
                        <th class="text-right">Tusla Racik</th>
                        <th class="text-right">Embalase</th>
                        <th class="text-right text-success">Grand Total</th>
                        <th class="text-right text-danger">Fee Dokter</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Kasir</th>
                        <th style="width: 80px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pharmacySales)): ?>
                        <?php foreach ($pharmacySales as $ps): ?>
                            <?php $psFee = !empty($ps->doctor_id) ? round((float)$ps->total_amount * 0.05, 2) : 0; ?>
                            <tr>
                                <td class="text-center font-weight-bold text-teal"><?= esc($ps->sale_no) ?></td>
                                <td class="text-center"><?= date('H:i', strtotime($ps->created_at)) ?> WITA</td>
                                <td>
                                    <strong><?= esc($ps->customer_name ?: 'Pelanggan Umum') ?></strong>
                                    <small class="text-muted d-block"><?= esc($ps->customer_phone ?: '-') ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($ps->doctor_id)): ?>
                                        <span class="badge badge-teal px-2 py-1"><i class="fas fa-user-md mr-1"></i> <?= esc($ps->doctor_name) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted">Bebas (OTC)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-info font-weight-bold">
                                    <?= (float)($ps->tusla_amount ?? 0) > 0 ? 'Rp ' . number_format($ps->tusla_amount, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right text-teal font-weight-bold">
                                    <?= (float)($ps->embalase_amount ?? 0) > 0 ? 'Rp ' . number_format($ps->embalase_amount, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-weight-bold text-success" style="font-size: 13.5px;">
                                    Rp <?= number_format($ps->grand_total, 0, ',', '.') ?>
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    <?= $psFee > 0 ? 'Rp ' . number_format($psFee, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border text-uppercase"><?= esc($ps->payment_method ?: 'Tunai') ?></span>
                                </td>
                                <td class="text-center"><?= esc($ps->cashier_name ?: 'Kasir') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('apotek/cetak-nota/' . $ps->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold shadow-sm" title="Cetak Struk/Nota">
                                        <i class="fas fa-print"></i> Nota
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-4" style="color: var(--rekap-muted);">Belum ada transaksi penjualan apotek langsung pada tanggal ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const expectedCash = <?= floatval($regBalance ?? 0) ?>;

    function calculateCashReconcile() {
        let total = 0;
        let hasInput = false;

        $('.input-cash-denom').each(function() {
            const count = parseInt($(this).val()) || 0;
            const val = parseFloat($(this).data('val')) || 0;
            if (count > 0) hasInput = true;
            total += (count * val);
        });

        const coin = parseFloat($('.input-cash-coin').val()) || 0;
        if (coin > 0) hasInput = true;
        total += coin;

        $('#lbl-physical-total').text('Rp ' + total.toLocaleString('id-ID'));

        if (!hasInput) {
            $('#badge-cash-reconcile').attr('class', 'badge badge-secondary px-2 py-1 font-weight-bold').html('Masukkan Hitungan');
            return;
        }

        const diff = total - expectedCash;
        if (Math.abs(diff) === 0) {
            $('#badge-cash-reconcile').attr('class', 'badge badge-success px-2 py-1 font-weight-bold').html('<i class="fas fa-check-circle mr-1"></i> PAS / SEIMBANG (0)');
        } else if (diff > 0) {
            $('#badge-cash-reconcile').attr('class', 'badge badge-primary px-2 py-1 font-weight-bold').html('<i class="fas fa-arrow-trend-up mr-1"></i> LEBIH (+ Rp ' + diff.toLocaleString('id-ID') + ')');
        } else {
            $('#badge-cash-reconcile').attr('class', 'badge badge-danger px-2 py-1 font-weight-bold').html('<i class="fas fa-arrow-trend-down mr-1"></i> KURANG (- Rp ' + Math.abs(diff).toLocaleString('id-ID') + ')');
        }
    }

    $(document).on('input change keyup', '.input-cash-denom, .input-cash-coin', function() {
        calculateCashReconcile();
    });

    $('#btn-reset-cash-calc').click(function() {
        $('.input-cash-denom, .input-cash-coin').val('');
        calculateCashReconcile();
    });
});
</script>
<?= $this->endSection() ?>
