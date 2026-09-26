<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak E-Tiket Obat') ?> - Sawamawa Medical Center</title>
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
        .etiket-wrapper {
            max-width: 900px;
            margin: 25px auto;
        }
        .etiket-card {
            background: #ffffff;
            border: 2px solid #333333;
            border-radius: 4px;
            padding: 15px 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            position: relative;
        }
        .etiket-header {
            border-bottom: 2px solid #007a5e;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .etiket-title {
            font-size: 15px;
            font-weight: 800;
            color: #007a5e;
            letter-spacing: 0.5px;
        }
        .dosage-box {
            background-color: #f8f9fa;
            border: 1.5px dashed #20c997;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            text-align: center;
        }
        .dosage-text {
            font-size: 18px;
            font-weight: 800;
            color: #007a5e;
            margin-bottom: 2px;
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
            }
            .etiket-wrapper {
                max-width: 100%;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .etiket-card {
                border: 2px solid #000000;
                box-shadow: none;
                page-break-inside: avoid;
                margin-bottom: 15px;
            }
            .etiket-header {
                border-bottom: 2px solid #000000;
            }
            .dosage-box {
                background-color: #ffffff;
                border: 1.5px dashed #000000;
            }
            .dosage-text {
                color: #000000;
            }
        }
    </style>
</head>
<body>

<div class="etiket-wrapper">
    <!-- Action Bar (No Print) -->
    <div class="row no-print mb-3">
        <div class="col-6">
            <a href="<?= base_url('keuangan/kasir') ?>" class="btn btn-outline-secondary font-weight-bold mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Ke Kasir
            </a>
            <a href="<?= base_url('apotek/resep') ?>" class="btn btn-outline-info font-weight-bold">
                <i class="fas fa-pills mr-1"></i> Ke Farmasi
            </a>
        </div>
        <div class="col-6 text-right">
            <button onclick="window.print();" class="btn btn-print">
                <i class="fas fa-print mr-1"></i> Cetak Semua E-Tiket Obat (Label Sticker)
            </button>
        </div>
    </div>

    <!-- Patient Header Card (No Print) -->
    <div class="card p-3 mb-3 bg-white border no-print shadow-sm">
        <div class="row">
            <div class="col-md-6">
                <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-user-injured text-teal mr-1"></i> <?= esc($prescription->patient_name) ?></h5>
                <span class="badge badge-teal">No. RM: <?= esc($prescription->no_rm) ?></span>
                <span class="badge badge-secondary ml-1">Visit: <?= esc($prescription->no_visit) ?></span>
            </div>
            <div class="col-md-6 text-right">
                <div class="text-secondary small">Dokter Peracik/Pemeriksa:</div>
                <div class="font-weight-bold text-dark"><?= esc($prescription->doctor_name ?? 'Dokter Klinik') ?></div>
                <div class="text-muted text-xs">Tgl: <?= date('d/m/Y', strtotime($prescription->created_at)) ?></div>
            </div>
        </div>
    </div>

    <!-- Drug Label Stickers Grid (2 Columns for sticker print) -->
    <div class="row">
        <?php if (!empty($details)): ?>
            <?php foreach ($details as $idx => $d): ?>
                <div class="col-md-6">
                    <div class="etiket-card">
                        <!-- Label Header -->
                        <div class="etiket-header d-flex justify-content-between align-items-center">
                            <div>
                                <div class="etiket-title"><i class="fas fa-clinic-medical"></i> INSTALASI FARMASI</div>
                                <div style="font-size: 11px; font-weight: 600; color: #444;">SAWAMAWA MEDICAL CENTER</div>
                                <div style="font-size: 9px; color: #777;">SIA: 440/12/DINKES/2026 | Telp: (0371) 23456</div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-dark px-2 py-1" style="font-size: 10px;">ETIKET OBAT</span>
                                <div style="font-size: 10px; color: #555; margin-top: 3px;"><?= date('d/m/Y', strtotime($prescription->created_at)) ?></div>
                            </div>
                        </div>

                        <!-- Patient Info on Sticker -->
                        <table class="table-sm table-borderless p-0 m-0 w-100" style="font-size: 12px; line-height: 1.3;">
                            <tr>
                                <td style="width: 80px; font-weight: 600; color: #555;">No. Resep</td>
                                <td>: <strong>RXP-<?= str_pad($prescription->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #555;">Nama Pasien</td>
                                <td>: <strong style="font-size: 13px;"><?= esc($prescription->patient_name) ?></strong> (RM: <?= esc($prescription->no_rm) ?>)</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #555;">Nama Obat</td>
                                <td>: <strong class="text-teal" style="font-size: 13px;"><?= esc($d->medicine_name) ?></strong> (Qty: <strong><?= esc($d->qty) ?> <?= esc($d->unit ?? 'Pcs') ?></strong>)</td>
                            </tr>
                        </table>

                        <!-- Prominent Dosage Banner -->
                        <div class="dosage-box">
                            <div class="text-uppercase text-secondary" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">ATURAN PAKAI / DOSIS:</div>
                            <div class="dosage-text"><?= esc($d->dosage) ?></div>
                            <div style="font-size: 11px; font-weight: 600; color: #555;">Diminum Sesuai Petunjuk Dokter</div>
                        </div>

                        <!-- Footer Warnings -->
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="font-size: 10px; color: #666;">
                            <span><i class="fas fa-info-circle"></i> Simpan di tempat sejuk & kering</span>
                            <span class="font-weight-bold">Semoga Lekas Sembuh</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center p-4 bg-white border">
                <p class="text-muted mb-0">Tidak ada rincian item obat pada resep ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
