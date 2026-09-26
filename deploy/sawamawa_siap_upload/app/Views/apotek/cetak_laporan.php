<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Laporan Farmasi') ?> - Sawamawa Medical Center</title>
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #111111;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }
        .report-container {
            max-width: 960px;
            margin: 30px auto;
            background: #ffffff;
            border: 1px solid #b8b8b8;
            padding: 35px 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .clinic-header {
            border-bottom: 2px solid #20c997;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .clinic-title {
            font-size: 22px;
            font-weight: 800;
            color: #007a5e;
            letter-spacing: 0.5px;
        }
        .report-badge {
            display: inline-block;
            background-color: #20c997;
            color: #ffffff;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 12px;
        }
        .table-items th {
            background-color: #f8f9fa;
            border-top: 1px solid #b8b8b8;
            border-bottom: 2px solid #b8b8b8;
            font-weight: 700;
            color: #333333;
            font-size: 12px;
        }
        .table-items td {
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .btn-print {
            background-color: #20c997;
            color: #ffffff;
            font-weight: 700;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
        }
        .btn-print:hover {
            background-color: #1aa179;
            color: #ffffff;
        }
        @media print {
            body {
                background: #ffffff;
                color: #000000;
                font-size: 11px;
            }
            .report-container {
                max-width: 100%;
                margin: 0;
                padding: 10px 15px;
                border: none;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            .clinic-header {
                border-bottom: 2px solid #000000;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Action Bar (No Print) -->
    <div class="row no-print mt-3 mb-2" style="max-width: 960px; margin: 0 auto;">
        <div class="col-6">
            <a href="<?= base_url('apotek/laporan') ?>" class="btn btn-outline-secondary font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard Laporan
            </a>
        </div>
        <div class="col-6 text-right">
            <button onclick="window.print();" class="btn btn-print">
                <i class="fas fa-print mr-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Official Report Sheet -->
    <div class="report-container">
        <!-- Clinic Header -->
        <div class="clinic-header d-flex justify-content-between align-items-center">
            <div>
                <div class="clinic-title"><i class="fas fa-clinic-medical mr-1"></i> SAWAMAWA MEDICAL CENTER</div>
                <div class="text-secondary small font-weight-bold">INSTALASI FARMASI & PELAYANAN OBAT</div>
                <div class="text-muted text-xs">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 23456</div>
            </div>
            <div class="text-right">
                <span class="report-badge text-uppercase"><i class="fas fa-chart-bar mr-1"></i> LAPORAN RESMI FARMASI</span>
                <div class="font-weight-bold text-dark mt-2" style="font-size: 15px;"><?= esc($title) ?></div>
                <?php if ($reportType !== 'expired'): ?>
                    <div class="text-muted small">Periode: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></div>
                <?php else: ?>
                    <div class="text-muted small">Posisi Data: <?= date('d/m/Y H:i') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Report Table Section -->
        <?php if ($reportType === 'pemakaian'): ?>
            <!-- 1. Pemakaian & Penjualan Obat -->
            <table class="table table-sm table-items mb-3">
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">NO</th>
                        <th style="width: 85px;" class="text-center">TGL</th>
                        <th>PASIEN & NO. RM</th>
                        <th>DOKTER</th>
                        <th>NAMA OBAT</th>
                        <th style="width: 60px;" class="text-center">QTY</th>
                        <th style="width: 90px;" class="text-right">HARGA</th>
                        <th style="width: 105px;" class="text-right">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $totalQty = 0;
                        $totalAmount = 0;
                    ?>
                    <?php if (!empty($dataRows)): ?>
                        <?php $no = 1; foreach ($dataRows as $r): ?>
                            <?php 
                                $sub = (float)$r->price * (int)$r->qty;
                                $totalQty += (int)$r->qty;
                                $totalAmount += $sub;
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($r->dispensed_date)) ?></td>
                                <td><strong><?= esc($r->patient_name) ?></strong> <small class="text-muted">(<?= esc($r->no_rm) ?>)</small></td>
                                <td class="text-sm"><?= esc($r->doctor_name) ?></td>
                                <td><strong><?= esc($r->medicine_name) ?></strong></td>
                                <td class="text-center font-weight-bold"><?= esc($r->qty) ?></td>
                                <td class="text-right">Rp <?= number_format($r->price, 0, ',', '.') ?></td>
                                <td class="text-right font-weight-bold">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted p-3">Tidak ada data transaksi pemakaian obat pada periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light font-weight-bold" style="font-size: 13px;">
                        <td colspan="5" class="text-right">TOTAL KESELURUHAN:</td>
                        <td class="text-center text-teal"><?= number_format($totalQty, 0, ',', '.') ?></td>
                        <td></td>
                        <td class="text-right text-success">Rp <?= number_format($totalAmount, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php elseif ($reportType === 'mutasi'): ?>
            <!-- 2. Kartu Stok & Mutasi Obat -->
            <table class="table table-sm table-items mb-3">
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">NO</th>
                        <th style="width: 110px;" class="text-center">WAKTU</th>
                        <th>NAMA OBAT & SATUAN</th>
                        <th style="width: 90px;">BATCH</th>
                        <th style="width: 90px;" class="text-center">TIPE</th>
                        <th style="width: 70px;" class="text-center">MASUK</th>
                        <th style="width: 70px;" class="text-center">KELUAR</th>
                        <th style="width: 80px;" class="text-center">SALDO</th>
                        <th style="width: 90px;">PETUGAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataRows)): ?>
                        <?php $no = 1; foreach ($dataRows as $r): ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                <td class="text-center text-sm"><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></td>
                                <td><strong><?= esc($r->medicine_name) ?></strong> <small class="text-muted">(<?= esc($r->unit) ?>)</small></td>
                                <td><?= esc($r->batch_no ?: '-') ?></td>
                                <td class="text-center"><span class="badge badge-light border text-uppercase"><?= esc($r->transaction_type) ?></span></td>
                                <td class="text-center text-success font-weight-bold"><?= $r->qty_in > 0 ? '+' . $r->qty_in : '-' ?></td>
                                <td class="text-center text-danger font-weight-bold"><?= $r->qty_out > 0 ? '-' . $r->qty_out : '-' ?></td>
                                <td class="text-center font-weight-bold"><?= esc($r->balance) ?></td>
                                <td class="text-sm"><?= esc($r->user_name ?? 'Sistem') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted p-3">Tidak ada data mutasi stok pada periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($reportType === 'expired'): ?>
            <!-- 3. Monitoring Kadaluarsa & FEFO -->
            <table class="table table-sm table-items mb-3">
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">NO</th>
                        <th style="width: 80px;">KODE</th>
                        <th>NAMA OBAT & GOLONGAN</th>
                        <th style="width: 100px;">NO. BATCH</th>
                        <th style="width: 100px;" class="text-center">TGL EXPIRED</th>
                        <th style="width: 90px;" class="text-center">SISA HARI</th>
                        <th style="width: 80px;" class="text-center">STOK</th>
                        <th style="width: 100px;" class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataRows)): ?>
                        <?php 
                            $no = 1; 
                            $now = time();
                        ?>
                        <?php foreach ($dataRows as $r): ?>
                            <?php 
                                $daysLeft = ceil((strtotime($r->expired_date) - $now) / 86400);
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                <td><?= esc($r->medicine_code ?? $r->code ?? '-') ?></td>
                                <td><strong><?= esc($r->medicine_name ?? $r->name ?? '-') ?></strong> <small class="text-muted">(<?= strtoupper(esc($r->type ?? '')) ?>)</small></td>
                                <td><?= esc($r->batch_no ?? '-') ?></td>
                                <td class="text-center font-weight-bold"><?= date('d/m/Y', strtotime($r->expired_date)) ?></td>
                                <td class="text-center font-weight-bold">
                                    <?php if ($daysLeft <= 0): ?>
                                        <span class="text-danger">Lewat <?= abs($daysLeft) ?> Hari</span>
                                    <?php else: ?>
                                        <span class="<?= $daysLeft <= 30 ? 'text-danger' : ($daysLeft <= 90 ? 'text-warning' : 'text-success') ?>">
                                            <?= $daysLeft ?> Hari
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center font-weight-bold"><?= esc($r->stock) ?> <?= esc($r->unit) ?></td>
                                <td class="text-center font-weight-bold text-xs">
                                    <?php if ($daysLeft <= 0): ?>
                                        <span class="badge badge-danger">EXPIRED</span>
                                    <?php elseif ($daysLeft <= 30): ?>
                                        <span class="badge badge-danger">KRITIS (&le; 30 Hari)</span>
                                    <?php elseif ($daysLeft <= 90): ?>
                                        <span class="badge badge-warning">WASPADA (&le; 90 Hari)</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">AMAN</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted p-3">Tidak ada data persediaan batch obat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Signatures Block -->
        <div class="row mt-5 pt-3 text-center">
            <div class="col-4">
                <div class="text-muted small">Petugas Pelaksana Farmasi,</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( <?= session('username') ? ucfirst(session('username')) : 'Staf Farmasi' ?> )
                </div>
            </div>
            <div class="col-4">
                <div class="text-muted small">Kepala Instalasi Farmasi,</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( apt. Penanggung Jawab, S.Farm )
                </div>
            </div>
            <div class="col-4">
                <div class="text-muted small">Mengetahui,<br>Direktur / Pimpinan Klinik,</div>
                <div style="height: 40px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( Manajemen Sawamawa )
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
