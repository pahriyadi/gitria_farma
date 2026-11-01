<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark */
    :root {
        --ks-canvas-bg: #f4f6f9;
        --ks-card-bg: #ffffff;
        --ks-border: #b8b8b8;
        --ks-text: #212529;
        --ks-text-muted: #6c757d;
        --ks-th-bg: #f8fafc;
        --ks-hover-bg: #f1f5f9;
        --ks-balance-bg: #ecfdf5;
        --ks-balance-text: #047857;
        --ks-filter-bg: #f8fafc;
    }

    body.dark-mode {
        --ks-canvas-bg: #14171a;
        --ks-card-bg: #1f2328;
        --ks-border: #374151;
        --ks-text: #f0f6fc;
        --ks-text-muted: #9ca3af;
        --ks-th-bg: #181b1f;
        --ks-hover-bg: #262c36;
        --ks-balance-bg: #064e3b;
        --ks-balance-text: #6ee7b7;
        --ks-filter-bg: #181b1f;
    }

    /* Container Card Styling */
    .ks-card {
        background-color: var(--ks-card-bg) !important;
        border: 1px solid var(--ks-border) !important;
        border-radius: 4px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .ks-header {
        background-color: var(--ks-card-bg) !important;
        border-bottom: 1px solid var(--ks-border) !important;
    }

    .ks-filter-box {
        background-color: var(--ks-filter-bg) !important;
        border: 1px solid var(--ks-border) !important;
        border-radius: 4px;
    }

    .ks-text-adaptive {
        color: var(--ks-text) !important;
    }

    .ks-text-muted {
        color: var(--ks-text-muted) !important;
    }

    /* Strict Excel-Standard Table - No Zebra Stripes (desain_tabel.md) */
    .table-ks {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1px solid var(--ks-border) !important;
        color: var(--ks-text) !important;
        margin-bottom: 0 !important;
    }

    .table-ks thead th {
        background-color: var(--ks-th-bg) !important;
        color: var(--ks-text) !important;
        border: 1px solid var(--ks-border) !important;
        padding: 9px 10px !important;
        font-size: 11.5px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700 !important;
        vertical-align: middle !important;
    }

    .table-ks tbody td {
        border: 1px solid var(--ks-border) !important;
        padding: 7px 10px !important;
        font-size: 12.5px !important;
        vertical-align: middle !important;
        background-color: var(--ks-card-bg) !important;
        color: var(--ks-text) !important;
    }

    .table-ks tbody tr:hover td {
        background-color: var(--ks-hover-bg) !important;
    }

    .table-ks td.col-balance {
        background-color: var(--ks-balance-bg) !important;
        color: var(--ks-balance-text) !important;
        font-weight: 700;
    }

    /* Metric Summary KPI Cards */
    .kpi-stat-box {
        background: var(--ks-card-bg) !important;
        border: 1px solid var(--ks-border) !important;
        border-radius: 4px;
        padding: 12px 14px;
        transition: transform 0.15s ease;
    }

    .kpi-stat-box:hover {
        transform: translateY(-2px);
    }

    /* Form Controls in Dark/Light */
    body.dark-mode .form-control,
    body.dark-mode .custom-select {
        background-color: #181b1f !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }

    body.dark-mode .form-control:focus,
    body.dark-mode .custom-select:focus {
        border-color: #20c997 !important;
        box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25) !important;
    }

    /* Print Stylesheet */
    @media print {
        .main-sidebar, .main-header, .btn, .ks-filter-box, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate, .breadcrumb, footer {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .ks-card, .table-ks {
            border-color: #000 !important;
        }
        .table-ks thead th, .table-ks tbody td {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
        .print-header {
            display: block !important;
        }
    }

    .print-header {
        display: none;
    }
</style>

<!-- Print Letterhead (Visible only on print) -->
<div class="print-header mb-4 text-center">
    <h4 class="font-weight-bold mb-0">SAWAMAWA MEDICAL CENTER</h4>
    <p class="mb-0 text-sm">INSTALASI FARMASI &amp; LOGISTIK OBAT</p>
    <p class="text-xs text-muted">Jl. Raya Sawamawa No. 123, Telp: (021) 7788-9900 | SIPA: 503/Apt/2026</p>
    <hr style="border-top: 2px solid #000; margin-top: 5px; margin-bottom: 15px;">
    <h5 class="font-weight-bold text-uppercase">LEMBAR BUKU KARTU STOK OBAT</h5>
</div>

<div class="row">
    <div class="col-12">
        <div class="card ks-card shadow-none">
            <!-- Header Card -->
            <div class="card-header ks-header p-3 d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold ks-text-adaptive mb-0">
                        <i class="fas fa-book-medical text-teal mr-2"></i>BUKU KARTU STOK OBAT &amp; ALKES (STOCK CARD LEDGER)
                    </h5>
                    <small class="ks-text-muted">Pencatatan real-time mutasi keluar-masuk, batch expired date, referensi dokumen, dan saldo berjalan</small>
                </div>
                <div class="mt-2 mt-md-0 d-flex align-items-center">
                    <a href="<?= base_url('apotek/stok') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Stok Master
                    </a>
                    <button type="button" class="btn btn-outline-info btn-sm font-weight-bold mr-2" id="btn-export-excel">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Cetak Kartu Stok
                    </button>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <!-- Filter Section -->
                <form action="<?= base_url('apotek/kartu-stok') ?>" method="get" class="mb-4 p-3 ks-filter-box" id="form-filter-kartu">
                    <div class="row align-items-end">
                        <div class="col-md-5 form-group mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold ks-text-adaptive">
                                <i class="fas fa-pills text-teal mr-1"></i> Pilih Obat / Alkes <span class="text-danger">*</span>
                            </label>
                            <select name="medicine_id" id="select-medicine" data-native="true" class="form-control form-control-sm font-weight-bold" onchange="this.form.submit();" required>
                                <option value="">-- Pilih Obat / Alkes Terdaftar --</option>
                                <?php foreach ($medicines as $m): ?>
                                    <option value="<?= $m->id ?>" <?= ($selectedMedicine && $selectedMedicine->id == $m->id) ? 'selected' : '' ?>>
                                        [<?= esc($m->code) ?>] <?= esc($m->name) ?> (Fisik: <?= (int)($m->stock ?? 0) ?> <?= esc($m->unit) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold ks-text-adaptive">
                                <i class="far fa-calendar-alt text-info mr-1"></i> Periode Mulai
                            </label>
                            <input type="date" name="start_date" id="filter-start-date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="col-md-3 form-group mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold ks-text-adaptive">
                                <i class="far fa-calendar-check text-info mr-1"></i> Periode Sampai
                            </label>
                            <input type="date" name="end_date" id="filter-end-date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="col-md-1 form-group mb-0">
                            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold" title="Terapkan Filter">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($selectedMedicine): ?>
                    <!-- Ringkasan Profil Obat & 4 KPI Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="kpi-stat-box">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-xs font-weight-bold text-uppercase ks-text-muted">Master Obat Terpilih</span>
                                    <span class="badge badge-teal px-2 py-1"><?= esc($selectedMedicine->category_name ?? 'Obat Bebas') ?></span>
                                </div>
                                <h6 class="font-weight-bold ks-text-adaptive mb-1">
                                    <code><?= esc($selectedMedicine->code) ?></code> - <?= esc($selectedMedicine->name) ?>
                                </h6>
                                <small class="ks-text-muted">
                                    Bentuk/Satuan: <strong><?= esc($selectedMedicine->unit) ?></strong> | Min Stok: <?= (int)$selectedMedicine->min_stock ?>
                                </small>
                            </div>
                        </div>

                        <div class="col-md-2 col-6 mb-2 mb-md-0">
                            <div class="kpi-stat-box text-center">
                                <span class="text-xs font-weight-bold text-uppercase ks-text-muted d-block mb-1">Saldo Awal</span>
                                <h5 class="font-weight-bold ks-text-adaptive mb-0"><?= number_format($saldoAwal, 0, ',', '.') ?></h5>
                                <small class="ks-text-muted"><?= esc($selectedMedicine->unit) ?></small>
                            </div>
                        </div>

                        <div class="col-md-3 col-6 mb-2 mb-md-0">
                            <div class="kpi-stat-box text-center">
                                <span class="text-xs font-weight-bold text-uppercase ks-text-muted d-block mb-1">Total Masuk / Keluar</span>
                                <h5 class="font-weight-bold mb-0">
                                    <span class="text-success">+<?= number_format($totalIn, 0, ',', '.') ?></span> / 
                                    <span class="text-danger">-<?= number_format($totalOut, 0, ',', '.') ?></span>
                                </h5>
                                <small class="ks-text-muted"><?= esc($selectedMedicine->unit) ?> periode ini</small>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2 mb-md-0">
                            <div class="kpi-stat-box text-center" style="border-left: 4px solid #20c997 !important;">
                                <span class="text-xs font-weight-bold text-uppercase text-teal d-block mb-1">Sisa Stok Fisik Saat Ini</span>
                                <h4 class="font-weight-bold text-teal mb-0">
                                    <?= number_format((int)($selectedMedicine->stock ?? 0), 0, ',', '.') ?> <span style="font-size: 15px;"><?= esc($selectedMedicine->unit) ?></span>
                                </h4>
                                <small class="ks-text-muted">Sinkronisasi FEFO Multi-Batch</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Buku Kartu Stok (Excel Strict Border Style) -->
                    <div class="table-responsive">
                        <table id="table-kartu-stok" class="table table-ks">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 45px;">No</th>
                                    <th style="width: 135px;">Tanggal &amp; Waktu</th>
                                    <th style="width: 145px;">No. Referensi</th>
                                    <th style="width: 140px;">Tipe Mutasi</th>
                                    <th style="width: 130px;">Batch &amp; ED</th>
                                    <th style="width: 90px;" class="text-right text-success">Masuk (+)</th>
                                    <th style="width: 90px;" class="text-right text-danger">Keluar (-)</th>
                                    <th style="width: 110px;" class="text-right text-teal">Sisa Saldo</th>
                                    <th style="width: 100px;">Operator</th>
                                    <th>Keterangan / Rincian Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ledger)): ?>
                                    <?php 
                                    $no = 1; 
                                    foreach ($ledger as $row): 
                                    ?>
                                        <tr>
                                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                            <td class="text-center font-monospace" style="font-size: 12px;">
                                                <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                            </td>
                                            <td class="font-weight-bold text-center">
                                                <code><?= esc($row['ref_no']) ?></code>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $row['type_badge'] ?> px-2 py-1 font-weight-bold" style="font-size: 11px;">
                                                    <?= esc($row['type_label']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center font-monospace" style="font-size: 11.5px;">
                                                <i class="fas fa-barcode text-muted mr-1"></i><?= esc($row['batch_no'] ?: '-') ?>
                                                <?php if (!empty($row['expired_date'])): ?>
                                                    <br><small class="ks-text-muted">ED: <?= date('d/m/Y', strtotime($row['expired_date'])) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right text-success font-weight-bold">
                                                <?= $row['qty_in'] > 0 ? '+' . number_format($row['qty_in'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right text-danger font-weight-bold">
                                                <?= $row['qty_out'] > 0 ? '-' . number_format($row['qty_out'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right col-balance font-monospace">
                                                <?= number_format($row['balance'], 0, ',', '.') ?> <?= esc($selectedMedicine->unit) ?>
                                            </td>
                                            <td class="text-center ks-text-muted" style="font-size: 11.5px;">
                                                <i class="fas fa-user-circle mr-1"></i><?= esc($row['user_name'] ?? 'Petugas') ?>
                                            </td>
                                            <td style="font-size: 12px;">
                                                <?= esc($row['notes'] ?? '-') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <div class="p-5 text-center ks-filter-box">
                        <i class="fas fa-book-open fa-3x mb-3 text-teal"></i>
                        <h5 class="font-weight-bold ks-text-adaptive">Silakan Pilih Obat / Alkes Terlebih Dahulu</h5>
                        <p class="ks-text-muted mb-0">Pilih salah satu obat dari daftar dropdown di atas untuk membuka riwayat buku kartu stok dan audit balance.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables with zero zebra striping
    if (document.getElementById('table-kartu-stok')) {
        const dt = $('#table-kartu-stok').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            order: [[0, 'asc']], // Order by No
            language: {
                search: "_INPUT_",
                searchPlaceholder: "🔍 Cari histori mutasi, batch, nomor...",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ transaksi",
                infoEmpty: "Tidak ada transaksi tercatat",
                emptyTable: "Belum ada transaksi mutasi stok pada periode ini",
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹"
                }
            },
            dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-2"lf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-2"ip>'
        });

        // Quick Export to CSV / Excel
        const btnExport = document.getElementById('btn-export-excel');
        if (btnExport) {
            btnExport.addEventListener('click', function() {
                let csv = [];
                const rows = document.querySelectorAll('#table-kartu-stok tr');
                for (let i = 0; i < rows.length; i++) {
                    let row = [], cols = rows[i].querySelectorAll('td, th');
                    for (let j = 0; j < cols.length; j++) {
                        let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
                        row.push('"' + text + '"');
                    }
                    csv.push(row.join(','));
                }
                const csvFile = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const downloadLink = document.createElement('a');
                downloadLink.download = 'Kartu_Stok_<?= $selectedMedicine ? esc($selectedMedicine->code) : 'Export' ?>_' + new Date().toISOString().slice(0, 10) + '.csv';
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = 'none';
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            });
        }
    }
});
</script>
<?= $this->endSection() ?>
