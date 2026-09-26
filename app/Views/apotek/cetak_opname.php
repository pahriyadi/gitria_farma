<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Berita Acara Stock Opname') ?> - Sawamawa Medical Center</title>
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
            max-width: 900px;
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
    <div class="row no-print mt-3 mb-2" style="max-width: 900px; margin: 0 auto;">
        <div class="col-6">
            <a href="<?= base_url('apotek/opname') ?>" class="btn btn-outline-secondary font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Stock Opname
            </a>
        </div>
        <div class="col-6 text-right">
            <button onclick="window.print();" class="btn btn-print">
                <i class="fas fa-print mr-1"></i> Cetak Berita Acara
            </button>
        </div>
    </div>

    <!-- Official Report Sheet -->
    <div class="report-container">
        <!-- Clinic Header -->
        <div class="clinic-header d-flex justify-content-between align-items-center">
            <div>
                <div class="clinic-title"><i class="fas fa-clinic-medical mr-1"></i> SAWAMAWA MEDICAL CENTER</div>
                <div class="text-secondary small font-weight-bold">INSTALASI FARMASI & APOTEK PELAYANAN</div>
                <div class="text-muted text-xs">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 23456</div>
            </div>
            <div class="text-right">
                <span class="report-badge text-uppercase"><i class="fas fa-clipboard-check mr-1"></i> BERITA ACARA OPNAME</span>
                <div class="font-weight-bold text-dark mt-2" style="font-size: 16px;"><?= esc($opname->opname_no) ?></div>
                <div class="text-muted small">Tgl: <?= date('d/m/Y', strtotime($opname->opname_date)) ?></div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="row mb-3 pb-2 border-bottom">
            <div class="col-6">
                <table class="table-sm table-borderless p-0 m-0 text-sm">
                    <tr>
                        <td class="text-secondary pl-0" style="width: 140px;">No. Dokumen Opname</td>
                        <td class="font-weight-bold text-teal">: <?= esc($opname->opname_no) ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">Tanggal Pelaksanaan</td>
                        <td class="font-weight-bold text-dark">: <?= date('d F Y', strtotime($opname->opname_date)) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table-sm table-borderless p-0 m-0 text-sm">
                    <tr>
                        <td class="text-secondary pl-0" style="width: 140px;">Petugas Pelaksana</td>
                        <td class="font-weight-bold text-dark">: <?= esc($opname->staff_name ?? 'Apoteker') ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">Status Penyesuaian</td>
                        <td class="font-weight-bold text-success">: TERADJUST (Stok Fisik Diperbarui)</td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if (!empty($opname->notes)): ?>
            <div class="p-2 mb-3 bg-light border text-sm">
                <strong>Catatan / Keterangan:</strong> <?= esc($opname->notes) ?>
            </div>
        <?php endif; ?>

        <!-- Itemized Discrepancy Table -->
        <table class="table table-sm table-items mb-3">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">NO</th>
                    <th style="width: 90px;">KODE</th>
                    <th>NAMA OBAT & SEDIAAN</th>
                    <th style="width: 110px;">BATCH & EXP</th>
                    <th style="width: 80px;" class="text-center">SISTEM</th>
                    <th style="width: 80px;" class="text-center">FISIK</th>
                    <th style="width: 80px;" class="text-center">SELISIH</th>
                    <th>KETERANGAN / ALASAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($details)): ?>
                    <?php $no = 1; foreach ($details as $d): ?>
                        <?php 
                            $diff = (int) $d->difference;
                        ?>
                        <tr>
                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                            <td><span class="badge badge-secondary"><?= esc($d->medicine_code) ?></span></td>
                            <td>
                                <strong><?= esc($d->medicine_name) ?></strong>
                            </td>
                            <td class="text-sm">
                                <?= esc($d->batch_no) ?><br>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($d->expired_date)) ?></small>
                            </td>
                            <td class="text-center font-weight-bold"><?= esc($d->system_stock) ?> <small><?= esc($d->unit) ?></small></td>
                            <td class="text-center font-weight-bold text-teal"><?= esc($d->physical_stock) ?> <small><?= esc($d->unit) ?></small></td>
                            <td class="text-center font-weight-bold">
                                <?php if ($diff === 0): ?>
                                    <span class="text-success">0</span>
                                <?php elseif ($diff > 0): ?>
                                    <span class="text-info">+<?= $diff ?></span>
                                <?php else: ?>
                                    <span class="text-danger"><?= $diff ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-secondary"><?= esc($d->reason ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted p-3">Tidak ada rincian item dalam dokumen opname ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Summary Metrics Box -->
        <div class="row bg-light p-3 border rounded mb-4">
            <div class="col-4 text-center border-right">
                <span class="text-secondary text-xs text-uppercase font-weight-bold">Total Item Dihitung</span>
                <div class="h5 font-weight-bold text-dark mb-0"><?= esc($opname->total_items) ?> Batch</div>
            </div>
            <div class="col-4 text-center border-right">
                <span class="text-secondary text-xs text-uppercase font-weight-bold">Total Selisih Unit</span>
                <div class="h5 font-weight-bold text-warning mb-0"><?= esc($opname->total_discrepancy) ?> Unit</div>
            </div>
            <div class="col-4 text-center">
                <span class="text-secondary text-xs text-uppercase font-weight-bold">Hasil Penyesuaian</span>
                <div class="h5 font-weight-bold text-success mb-0">100% Diselaraskan</div>
            </div>
        </div>

        <!-- Official Signatures Block -->
        <div class="row mt-4 pt-2 text-center">
            <div class="col-4">
                <div class="text-muted small">Petugas Pelaksana Opname,</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( <?= esc($opname->staff_name ?? 'Apoteker Pelaksana') ?> )
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
