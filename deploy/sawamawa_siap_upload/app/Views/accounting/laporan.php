<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Quick Switcher Tabs -->
<div class="mb-3 d-flex align-items-center justify-content-between">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/jurnal') ?>">
                <i class="fas fa-file-lines mr-1"></i> 1. Jurnal Umum Konsolidasian
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/buku-besar') ?>">
                <i class="fas fa-book-journal-whills mr-1"></i> 2. Jurnal Mutasi per Akun (Buku Pembantu)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" href="<?= base_url('accounting/laporan') ?>">
                <i class="fas fa-chart-pie mr-1"></i> 3. Laporan Keuangan Standar BI & SAK
            </a>
        </li>
    </ul>
</div>

<!-- Bar Filter Periode & Cetak Laporan -->
<div class="card p-3 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <form method="get" action="<?= base_url('accounting/laporan') ?>" class="row align-items-end">
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-day text-teal mr-1"></i> Dari Tanggal:</label>
            <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-check text-teal mr-1"></i> Sampai Tanggal:</label>
            <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold">
                <i class="fas fa-filter mr-1"></i> Terapkan Filter Periode
            </button>
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0 text-md-right">
            <a href="<?= base_url('accounting/cetak-laporan?start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold px-3 shadow-none">
                <i class="fas fa-print mr-1 text-teal"></i> Cetak Format Resmi BI / SAK
            </a>
            <a href="<?= base_url('accounting/saldo-awal') ?>" class="btn btn-outline-teal btn-sm font-weight-bold ml-1 shadow-none">
                <i class="fas fa-coins mr-1"></i> Saldo Awal
            </a>
        </div>
    </form>
</div>

<!-- Header Ringkasan Keuangan Kunci (Executive Financial Indicators) -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card p-3 bg-white shadow-none h-100 mb-0" style="border: 1px solid #b8b8b8; border-left: 5px solid #0d9f4f;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">1. Pendapatan Usaha</span>
                    <h5 class="font-weight-bold text-teal mb-0 mt-1">Rp <?= number_format($totalRevenue, 2, ',', '.') ?></h5>
                </div>
                <div class="p-2 rounded bg-light text-teal border">
                    <i class="fas fa-arrow-trend-up fa-lg"></i>
                </div>
            </div>
            <small class="text-secondary mt-2 d-block" style="font-size: 11.5px;">Klinik, Farmasi, Resto & Lab</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card p-3 bg-white shadow-none h-100 mb-0" style="border: 1px solid #b8b8b8; border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">2. Beban Pokok (HPP)</span>
                    <h5 class="font-weight-bold text-warning mb-0 mt-1">Rp <?= number_format($totalCogs, 2, ',', '.') ?></h5>
                </div>
                <div class="p-2 rounded bg-light text-warning border">
                    <i class="fas fa-boxes-packing fa-lg"></i>
                </div>
            </div>
            <small class="text-secondary mt-2 d-block" style="font-size: 11.5px;">Laba Bruto: <strong>Rp <?= number_format($grossProfit, 2, ',', '.') ?></strong></small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card p-3 bg-white shadow-none h-100 mb-0" style="border: 1px solid #b8b8b8; border-left: 5px solid #dc2626;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">3. Beban Operasional</span>
                    <h5 class="font-weight-bold text-danger mb-0 mt-1">Rp <?= number_format($totalExpense, 2, ',', '.') ?></h5>
                </div>
                <div class="p-2 rounded bg-light text-danger border">
                    <i class="fas fa-arrow-trend-down fa-lg"></i>
                </div>
            </div>
            <small class="text-secondary mt-2 d-block" style="font-size: 11.5px;">Gaji, Jasa Medis & Operasional</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card p-3 bg-white shadow-none h-100 mb-0" style="border: 1px solid #b8b8b8; border-left: 5px solid <?= $netIncome >= 0 ? '#0d9f4f' : '#dc2626' ?>;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-xs font-weight-bold text-uppercase">4. Laba Bersih (Net Profit)</span>
                    <h5 class="font-weight-bold <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?> mb-0 mt-1">
                        Rp <?= number_format($netIncome, 2, ',', '.') ?>
                    </h5>
                </div>
                <div class="p-2 rounded bg-light <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?> border">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
            </div>
            <small class="text-secondary mt-2 d-block" style="font-size: 11.5px;">Margin Bersih: <?= $totalRevenue > 0 ? number_format(($netIncome / $totalRevenue) * 100, 1) : '0' ?>%</small>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- PANDUAN PINTAR & CARA MEMBACA LAPORAN UNTUK PEMILIK / NON-AKUNTAN -->
<!-- ========================================================================= -->
<div class="card mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8; border-left: 4px solid #17a2b8; border-radius: 6px;">
    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#guideCollapse" style="cursor: pointer;">
        <div class="d-flex align-items-center">
            <span class="p-1 px-2 rounded bg-light text-info border mr-2 font-weight-bold" style="font-size: 12px;">
                <i class="fas fa-lightbulb"></i> BANTUAN
            </span>
            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13.5px;">
                Panduan Sederhana: Cara Membaca &amp; Memahami 4 Laporan Keuangan Ini (Bahasa Awam / Pemilik Usaha)
            </h6>
        </div>
        <span class="text-muted text-xs"><i class="fas fa-chevron-down"></i> Klik untuk Buka / Tutup</span>
    </div>
    <div class="collapse show" id="guideCollapse">
        <div class="card-body p-3 pt-2 text-dark" style="font-size: 12.5px; background: #fdfdfd;">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="p-2 rounded border bg-white h-100">
                        <strong class="text-teal d-block mb-1"><i class="fas fa-file-invoice-dollar mr-1"></i> 1. Laba Rugi Komprehensif</strong>
                        <p class="text-muted mb-0" style="font-size: 11.5px;">
                            <strong>Tujuan:</strong> Menjawab apakah klinik sedang <em>Untung</em> atau <em>Rugi</em>.<br>
                            <strong>Rumus Mudah:</strong> (Total Pemasukan Pasien &amp; Resto) - (Biaya Obat &amp; Gaji &amp; Operasional) = <strong>Sisa Uang Laba Bersih</strong>.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-2 rounded border bg-white h-100">
                        <strong class="text-primary d-block mb-1"><i class="fas fa-scale-balanced mr-1"></i> 2. Posisi Keuangan (Neraca)</strong>
                        <p class="text-muted mb-0" style="font-size: 11.5px;">
                            <strong>Tujuan:</strong> Melihat daftar total seluruh kekayaan klinik.<br>
                            <strong>Kiri (Aset):</strong> Uang Kasir + Bank + Nilai Stok Obat + Gedung.<br>
                            <strong>Kanan (Pasiva):</strong> Modal Anda + Hutang ke Supplier Obat. (Nilai Kiri &amp; Kanan <strong>Wajib Sama/Balance</strong>).
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-2 rounded border bg-white h-100">
                        <strong class="text-warning d-block mb-1"><i class="fas fa-money-bill-transfer mr-1"></i> 3. Arus Kas (Cash Flow)</strong>
                        <p class="text-muted mb-0" style="font-size: 11.5px;">
                            <strong>Tujuan:</strong> Melacak aliran uang tunai fisik yang nyata masuk dan keluar dari kasir &amp; rekening bank klinik selama periode ini.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-2 rounded border bg-white h-100">
                        <strong class="text-success d-block mb-1"><i class="fas fa-layer-group mr-1"></i> 4. Analisa Omset Unit Bisnis</strong>
                        <p class="text-muted mb-0" style="font-size: 11.5px;">
                            <strong>Tujuan:</strong> Mengetahui unit usaha mana yang paling banyak menghasilkan uang (Poliklinik vs Apotek Obat vs Restoran Sehat vs Lab Medis).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Keuangan Tabs -->
<div class="card p-0 mb-4 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <div class="card-header p-2 bg-white border-bottom">
        <ul class="nav nav-pills" id="financial-tabs">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" href="#tab-labarugi" data-toggle="pill">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> I. Laporan Laba Rugi Komprehensif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-neraca" data-toggle="pill">
                    <i class="fas fa-scale-balanced mr-1"></i> II. Laporan Posisi Keuangan (Neraca)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-aruskas" data-toggle="pill">
                    <i class="fas fa-money-bill-transfer mr-1"></i> III. Laporan Arus Kas (Metode Langsung)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-unitbisnis" data-toggle="pill">
                    <i class="fas fa-layer-group mr-1"></i> IV. Analisa Omset Unit Bisnis
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content">
            
            <!-- TAB 1: LAPORAN LABA RUGI KOMPREHENSIF -->
            <div class="tab-pane fade show active" id="tab-labarugi">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">LAPORAN LABA RUGI KOMPREHENSIF</h5>
                    <span class="text-teal font-weight-bold" style="font-size:14px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span><br>
                    <small class="text-muted font-weight-bold">Standar Pelaporan: SAK EMKM / Bank Indonesia &bull; Mata Uang: Rupiah (IDR)</small><br>
                    <span class="badge badge-light border mt-1">Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></span>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <table class="table table-bordered table-striped table-sm mb-0 text-dark" style="font-size: 13px;">
                            <!-- BAGIAN A: PENDAPATAN OPERASIONAL -->
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-teal font-weight-bold py-2" style="font-size: 13.5px;">
                                        <i class="fas fa-arrow-trend-up mr-1"></i> A. PENDAPATAN OPERASIONAL UTAMA
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($revenueAccounts)): ?>
                                    <?php foreach ($revenueAccounts as $rev): ?>
                                        <tr>
                                            <td class="pl-4 font-weight-500"><?= esc($rev->code) ?> - <?= esc($rev->name) ?></td>
                                            <td class="text-right font-monospace font-weight-bold" style="width: 240px;">
                                                Rp <?= number_format($rev->balance, 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="pl-4 text-muted">-</td>
                                        <td class="text-right font-monospace">Rp 0,00</td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="bg-light font-weight-bold">
                                    <td class="text-right">TOTAL PENDAPATAN OPERASIONAL (A):</td>
                                    <td class="text-right font-monospace text-teal" style="font-size: 13.5px;">
                                        Rp <?= number_format($totalRevenue, 2, ',', '.') ?>
                                    </td>
                                </tr>

                                <!-- BAGIAN B: BEBAN POKOK PENDAPATAN (HPP) -->
                                <tr class="bg-light">
                                    <th colspan="2" class="text-warning font-weight-bold py-2" style="font-size: 13.5px;">
                                        <i class="fas fa-boxes-packing mr-1"></i> B. BEBAN POKOK PENDAPATAN (HPP)
                                    </th>
                                </tr>
                                <?php if (!empty($cogsAccounts)): ?>
                                    <?php foreach ($cogsAccounts as $cog): ?>
                                        <tr>
                                            <td class="pl-4 font-weight-500"><?= esc($cog->code) ?> - <?= esc($cog->name) ?></td>
                                            <td class="text-right font-monospace font-weight-bold" style="width: 240px;">
                                                Rp <?= number_format($cog->balance, 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="pl-4 text-muted">5-101 - HPP Obat Farmasi & Bahan Resto</td>
                                        <td class="text-right font-monospace">Rp 0,00</td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="bg-light font-weight-bold">
                                    <td class="text-right">TOTAL BEBAN POKOK PENDAPATAN (B):</td>
                                    <td class="text-right font-monospace text-danger" style="font-size: 13.5px;">
                                        Rp <?= number_format($totalCogs, 2, ',', '.') ?>
                                    </td>
                                </tr>

                                <!-- LABA KOTOR -->
                                <tr class="table-info font-weight-bold" style="font-size: 14px; background-color: #e0f2fe;">
                                    <td class="text-right text-dark">LABA KOTOR / BRUTO (A - B):</td>
                                    <td class="text-right font-monospace text-primary">
                                        Rp <?= number_format($grossProfit, 2, ',', '.') ?>
                                    </td>
                                </tr>

                                <!-- BAGIAN C: BEBAN OPERASIONAL & UMUM -->
                                <tr class="bg-light">
                                    <th colspan="2" class="text-danger font-weight-bold py-2" style="font-size: 13.5px;">
                                        <i class="fas fa-arrow-trend-down mr-1"></i> C. BEBAN OPERASIONAL, GAJI & ADMINISTRASI UMUM
                                    </th>
                                </tr>
                                <?php if (!empty($expenseAccounts)): ?>
                                    <?php foreach ($expenseAccounts as $exp): ?>
                                        <tr>
                                            <td class="pl-4 font-weight-500"><?= esc($exp->code) ?> - <?= esc($exp->name) ?></td>
                                            <td class="text-right font-monospace font-weight-bold" style="width: 240px;">
                                                Rp <?= number_format($exp->balance, 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="pl-4 text-muted">Beban Operasional Belum Tercatat</td>
                                        <td class="text-right font-monospace">Rp 0,00</td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="bg-light font-weight-bold">
                                    <td class="text-right">TOTAL BEBAN OPERASIONAL & UMUM (C):</td>
                                    <td class="text-right font-monospace text-danger" style="font-size: 13.5px;">
                                        Rp <?= number_format($totalExpense, 2, ',', '.') ?>
                                    </td>
                                </tr>

                                <!-- LABA BERSIH (NET INCOME) -->
                                <tr class="table-success font-weight-bold" style="font-size: 15px; background-color: <?= $netIncome >= 0 ? '#dcfce7' : '#fee2e2' ?>;">
                                    <td class="text-right <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="fas fa-star mr-1"></i> LABA / (RUGI) BERSIH PERIODE BERJALAN (A - B - C):
                                    </td>
                                    <td class="text-right font-monospace <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($netIncome, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: LAPORAN POSISI KEUANGAN (NERACA STANDAR BI / SAK) -->
            <div class="tab-pane fade" id="tab-neraca">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">LAPORAN POSISI KEUANGAN (NERACA)</h5>
                    <span class="text-teal font-weight-bold" style="font-size:14px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span><br>
                    <small class="text-muted font-weight-bold">Klasifikasi Aset Lancar, Aset Tetap, Liabilitas & Ekuitas SAK EMKM</small><br>
                    <span class="badge badge-light border mt-1">Per Tanggal: <?= date('d F Y', strtotime($endDate)) ?></span>
                </div>

                <div class="row">
                    <!-- SISI KIRI: ASET (AKTIVA) -->
                    <div class="col-lg-6 mb-3">
                        <div class="card p-0 shadow-none mb-0" style="border: 1px solid #b8b8b8;">
                            <div class="card-header bg-light py-2">
                                <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-vault text-teal mr-1"></i> ASET (AKTIVA)</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm mb-0 text-dark" style="font-size: 13px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>1. Aset Lancar (Current Assets)</th>
                                            <th class="text-right" style="width: 170px;">Jumlah (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sumCurrent = 0; ?>
                                        <?php if (!empty($currentAssets)): ?>
                                            <?php foreach ($currentAssets as $ca): ?>
                                                <?php $sumCurrent += (float)$ca->balance; ?>
                                                <tr>
                                                    <td class="pl-3"><?= esc($ca->code) ?> - <?= esc($ca->name) ?></td>
                                                    <td class="text-right font-monospace">Rp <?= number_format($ca->balance, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="text-right">Total Aset Lancar:</td>
                                            <td class="text-right font-monospace text-teal">Rp <?= number_format($sumCurrent, 2, ',', '.') ?></td>
                                        </tr>

                                        <tr class="bg-light">
                                            <th colspan="2">2. Aset Tidak Lancar / Tetap (Fixed Assets)</th>
                                        </tr>
                                        <?php $sumFixed = 0; ?>
                                        <?php if (!empty($fixedAssets)): ?>
                                            <?php foreach ($fixedAssets as $fa): ?>
                                                <?php $sumFixed += (float)$fa->balance; ?>
                                                <tr>
                                                    <td class="pl-3"><?= esc($fa->code) ?> - <?= esc($fa->name) ?></td>
                                                    <td class="text-right font-monospace">Rp <?= number_format($fa->balance, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td class="pl-3 text-muted">Aset Tetap Peralatan Medis</td>
                                                <td class="text-right font-monospace">Rp 0,00</td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="text-right">Total Aset Tetap:</td>
                                            <td class="text-right font-monospace text-teal">Rp <?= number_format($sumFixed, 2, ',', '.') ?></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info font-weight-bold" style="font-size: 14px;">
                                            <td class="text-right">TOTAL ASET (AKTIVA):</td>
                                            <td class="text-right font-monospace text-primary">Rp <?= number_format($totalAsset, 2, ',', '.') ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SISI KANAN: LIABILITAS & EKUITAS (PASIVA) -->
                    <div class="col-lg-6 mb-3">
                        <div class="card p-0 shadow-none mb-0" style="border: 1px solid #b8b8b8;">
                            <div class="card-header bg-light py-2">
                                <h6 class="font-weight-bold text-dark mb-0"><i class="fas fa-scale-balanced text-teal mr-1"></i> LIABILITAS & EKUITAS (PASIVA)</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-sm mb-0 text-dark" style="font-size: 13px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>1. Liabilitas Jangka Pendek (Liabilities)</th>
                                            <th class="text-right" style="width: 170px;">Jumlah (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($liabilityAccounts)): ?>
                                            <?php foreach ($liabilityAccounts as $lia): ?>
                                                <tr>
                                                    <td class="pl-3"><?= esc($lia->code) ?> - <?= esc($lia->name) ?></td>
                                                    <td class="text-right font-monospace">Rp <?= number_format($lia->balance, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="text-right">Total Liabilitas:</td>
                                            <td class="text-right font-monospace text-danger">Rp <?= number_format($totalLiability, 2, ',', '.') ?></td>
                                        </tr>

                                        <tr class="bg-light">
                                            <th colspan="2">2. Ekuitas / Modal (Equity)</th>
                                        </tr>
                                        <?php if (!empty($equityAccounts)): ?>
                                            <?php foreach ($equityAccounts as $eq): ?>
                                                <tr>
                                                    <td class="pl-3"><?= esc($eq->code) ?> - <?= esc($eq->name) ?></td>
                                                    <td class="text-right font-monospace">Rp <?= number_format($eq->balance, 2, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="pl-3 font-weight-500 text-teal">
                                                <i class="fas fa-caret-right mr-1"></i> Laba / (Rugi) Bersih Periode Berjalan
                                            </td>
                                            <td class="text-right font-monospace font-weight-bold <?= $netIncome >= 0 ? 'text-success' : 'text-danger' ?>">
                                                Rp <?= number_format($netIncome, 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                        <tr class="font-weight-bold bg-light">
                                            <td class="text-right">Total Ekuitas Bersih:</td>
                                            <td class="text-right font-monospace text-teal">Rp <?= number_format($totalEquity, 2, ',', '.') ?></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info font-weight-bold" style="font-size: 14px;">
                                            <td class="text-right">TOTAL LIABILITAS & EKUITAS:</td>
                                            <td class="text-right font-monospace text-primary">
                                                Rp <?= number_format($totalLiability + $totalEquity, 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Status Balance Check Box -->
                <?php 
                $balanceDiff = abs($totalAsset - ($totalLiability + $totalEquity));
                $isBalanced  = ($balanceDiff < 0.01);
                ?>
                <div class="alert <?= $isBalanced ? 'alert-success' : 'alert-danger' ?> mt-3 mb-0 d-flex justify-content-between align-items-center shadow-none" style="border: 1px solid <?= $isBalanced ? '#86efac' : '#fca5a5' ?>;">
                    <div>
                        <i class="fas fa-<?= $isBalanced ? 'check-circle' : 'exclamation-triangle' ?> fa-lg mr-2"></i>
                        <strong>Status Keseimbangan Neraca: <?= $isBalanced ? 'SEIMBANG (100% BALANCE)' : 'TERDAPAT SELISIH' ?></strong>
                        <div class="text-xs mt-1">Total Aset (Rp <?= number_format($totalAsset, 2, ',', '.') ?>) = Total Liabilitas & Ekuitas (Rp <?= number_format($totalLiability + $totalEquity, 2, ',', '.') ?>)</div>
                    </div>
                    <div>
                        <span class="badge badge-<?= $isBalanced ? 'success' : 'danger' ?> px-3 py-2 font-weight-bold font-monospace" style="font-size: 13px;">
                            Selisih: Rp <?= number_format($balanceDiff, 2, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- TAB 3: LAPORAN ARUS KAS -->
            <div class="tab-pane fade" id="tab-aruskas">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">LAPORAN ARUS KAS (METODE LANGSUNG)</h5>
                    <span class="text-teal font-weight-bold" style="font-size:14px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span><br>
                    <small class="text-muted font-weight-bold">Penerimaan & Pengeluaran Kas Operasi, Investasi, dan Pendanaan</small><br>
                    <span class="badge badge-light border mt-1">Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></span>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <table class="table table-bordered table-striped table-sm mb-0 text-dark" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Aktivitas Arus Kas (Cash Flow Activities)</th>
                                    <th class="text-right" style="width: 240px;">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-teal"><i class="fas fa-hand-holding-dollar mr-1"></i> A. ARUS KAS DARI AKTIVITAS OPERASI</td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Penerimaan Kas dari Pasien Medis, Apotek & Resto</td>
                                    <td class="text-right font-monospace font-weight-bold text-success">
                                        Rp <?= number_format($cashInTotal, 2, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Pembayaran Beban Operasional, Gaji & Supplier</td>
                                    <td class="text-right font-monospace font-weight-bold text-danger">
                                        (Rp <?= number_format($expensePaidTotal, 2, ',', '.') ?>)
                                    </td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td class="text-right">Arus Kas Bersih dari Aktivitas Operasi:</td>
                                    <td class="text-right font-monospace text-teal">
                                        Rp <?= number_format($cashInTotal - $expensePaidTotal, 2, ',', '.') ?>
                                    </td>
                                </tr>

                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-primary"><i class="fas fa-building mr-1"></i> B. ARUS KAS DARI AKTIVITAS INVESTASI</td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Pengadaan / Pembelian Peralatan Medis & Aset Tetap</td>
                                    <td class="text-right font-monospace text-muted">-</td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td class="text-right">Arus Kas Bersih dari Aktivitas Investasi:</td>
                                    <td class="text-right font-monospace">Rp 0,00</td>
                                </tr>

                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-warning"><i class="fas fa-coins mr-1"></i> C. ARUS KAS DARI AKTIVITAS PENDANAAN</td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Setoran Modal Awal & Tambahan Ekuitas</td>
                                    <td class="text-right font-monospace text-muted">-</td>
                                </tr>
                                <tr class="font-weight-bold bg-light">
                                    <td class="text-right">Arus Kas Bersih dari Aktivitas Pendanaan:</td>
                                    <td class="text-right font-monospace">Rp 0,00</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-success font-weight-bold" style="font-size: 14px;">
                                    <td class="text-right text-dark">KENAIKAN / (PENURUNAN) BERSIH KAS & BANK:</td>
                                    <td class="text-right font-monospace text-success">
                                        Rp <?= number_format($cashInTotal - $expensePaidTotal, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: REKAP OMSET PER UNIT BISNIS -->
            <div class="tab-pane fade" id="tab-unitbisnis">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <h5 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">REKAPITULASI KONTRIBUSI OMSET PER UNIT BISNIS</h5>
                    <span class="text-teal font-weight-bold" style="font-size:14px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span><br>
                    <small class="text-muted font-weight-bold">Performa Lini Usaha Rawat Jalan, Farmasi, Restoran & Laboratorium</small><br>
                    <span class="badge badge-light border mt-1">Periode: <?= date('d F Y', strtotime($startDate)) ?> s/d <?= date('d F Y', strtotime($endDate)) ?></span>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <table class="table table-bordered table-striped mb-0 text-dark" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Lini Unit Usaha / Operasional</th>
                                    <th>Cakupan Layanan</th>
                                    <th class="text-right" style="width: 220px;">Omset Terbayar (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center font-weight-bold">1</td>
                                    <td class="font-weight-bold text-teal"><i class="fas fa-stethoscope mr-1"></i> Poliklinik Rawat Jalan & Medis</td>
                                    <td>Jasa Konsultasi Dokter, Tindakan Medis & Rawat Inap</td>
                                    <td class="text-right font-monospace font-weight-bold">Rp <?= number_format($klinikRevenue, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center font-weight-bold">2</td>
                                    <td class="font-weight-bold text-info"><i class="fas fa-prescription-bottle-alt mr-1"></i> Farmasi & Apotek Terpadu</td>
                                    <td>E-Prescribing Dokter & Penjualan Obat Bebas (OTC)</td>
                                    <td class="text-right font-monospace font-weight-bold">Rp <?= number_format($apotekRevenue, 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-center font-weight-bold">3</td>
                                    <td class="font-weight-bold text-warning"><i class="fas fa-utensils mr-1"></i> Restoran Sehat & Cafe Gizi</td>
                                    <td>Dine-in POS, Takeaway & Paket Diet Medis Pasien</td>
                                    <td class="text-right font-monospace font-weight-bold">Rp <?= number_format($restoSalesTotal, 2, ',', '.') ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-info font-weight-bold" style="font-size: 14px;">
                                    <td colspan="3" class="text-right">TOTAL OMSET TRANSAKSI TERPADU:</td>
                                    <td class="text-right font-monospace text-primary">
                                        Rp <?= number_format($klinikRevenue + $apotekRevenue + $restoSalesTotal, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
