<?php
// Helper Terbilang Bahasa Indonesia
if (!function_exists('terbilangResep')) {
    function terbilangResep($angka) {
        $angka = abs((float)$angka);
        $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = terbilangResep($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            $terbilang = terbilangResep($angka / 10) . " Puluh" . terbilangResep($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " Seratus" . terbilangResep($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = terbilangResep($angka / 100) . " Ratus" . terbilangResep($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " Seribu" . terbilangResep($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = terbilangResep($angka / 1000) . " Ribu" . terbilangResep($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = terbilangResep($angka / 1000000) . " Juta" . terbilangResep($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = terbilangResep($angka / 1000000000) . " Miliar" . terbilangResep(fmod($angka, 1000000000));
        }
        return $terbilang;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kwitansi Pembayaran Resep Obat') ?> - Sawamawa Medical Center</title>
    <!-- Google Font: Source Sans Pro & Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #212529;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }
        .receipt-container {
            max-width: 820px;
            margin: 25px auto;
            background: #ffffff;
            border: 1px solid #b8b8b8;
            padding: 30px 35px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 4px;
        }
        .clinic-header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .clinic-title {
            font-size: 20px;
            font-weight: 800;
            color: #0d9488;
            letter-spacing: 0.5px;
        }
        .receipt-badge {
            display: inline-block;
            background-color: #0d9488;
            color: #ffffff;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            letter-spacing: 0.5px;
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
            font-size: 12.5px;
        }
        .terbilang-box {
            background-color: #f0fdfa;
            border: 1px dashed #0d9488;
            border-radius: 4px;
            padding: 10px 14px;
            font-style: italic;
            font-size: 13px;
            color: #115e59;
        }
        .stamp-box {
            height: 75px;
        }
        .no-print-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            margin-bottom: 20px;
        }
        @media print {
            body {
                background: #ffffff;
            }
            .receipt-container {
                max-width: 100%;
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

<!-- Floating Print Action Bar -->
<div class="no-print-bar d-flex justify-content-between align-items-center">
    <div>
        <h6 class="font-weight-bold text-teal mb-0"><i class="fas fa-file-invoice mr-1"></i> Kwitansi Resmi e-Resep Farmasi</h6>
        <small class="text-muted">Siap dicetak pada format kertas A4 atau Folio</small>
    </div>
    <div>
        <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-2" onclick="window.print();">
            <i class="fas fa-print mr-1"></i> Cetak Kwitansi
        </button>
        <button type="button" class="btn btn-secondary btn-sm font-weight-bold" onclick="window.close();">
            <i class="fas fa-times mr-1"></i> Tutup
        </button>
    </div>
</div>

<div class="receipt-container">
    <!-- Clinic Header & Kop Surat -->
    <div class="clinic-header d-flex justify-content-between align-items-start">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-clinic-medical text-teal" style="font-size: 42px;"></i>
            </div>
            <div>
                <div class="clinic-title">SAWAMAWA MEDICAL CENTER</div>
                <div class="font-weight-bold text-dark text-sm">INSTALASI FARMASI & APOTEK RESMI</div>
                <div class="text-muted text-xs">Jl. Kebangsaan No. 12, Sumbawa Besar | Telp: (0371) 23456 | WA: 0812-3456-7890</div>
                <div class="text-muted text-xs">Izin Operasional Apotek: No. 503/044/DPM-PTSP/FARM/2024</div>
            </div>
        </div>
        <div class="text-right">
            <span class="receipt-badge text-uppercase"><i class="fas fa-prescription-bottle-medical mr-1"></i> Kwitansi Resep</span>
            <div class="font-weight-bold text-dark mt-2" style="font-size: 15px;">
                No: <strong>RSP-<?= str_pad($prescription->id, 4, '0', STR_PAD_LEFT) ?></strong>
            </div>
            <div class="text-muted text-xs">
                Billing: <strong><?= esc($prescription->billing_no ?? '-') ?></strong>
            </div>
        </div>
    </div>

    <!-- Metadata Kunjungan & Pasien -->
    <div class="row mb-3 bg-light p-3 rounded" style="border: 1px solid #e2e8f0;">
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0" style="font-size: 12.5px;">
                <tr>
                    <td class="text-muted p-0" style="width: 110px;">Nama Pasien</td>
                    <td class="p-0">: <strong class="text-dark"><?= esc($prescription->patient_name ?? 'Pasien Umum') ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted p-0">No. Rekam Medis</td>
                    <td class="p-0">: <span class="badge badge-secondary"><?= esc($prescription->no_rm ?? '-') ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted p-0">No. Kunjungan</td>
                    <td class="p-0">: <?= esc($prescription->no_visit ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Poliklinik</td>
                    <td class="p-0">: <?= esc($prescription->poly_name ?? 'Rawat Jalan / Poliklinik') ?></td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0" style="font-size: 12.5px;">
                <tr>
                    <td class="text-muted p-0" style="width: 120px;">Dokter Peresep</td>
                    <td class="p-0">: <strong class="text-dark"><?= esc($prescription->doctor_name ?? 'Dokter Pemeriksa') ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Waktu Penyerahan</td>
                    <td class="p-0">: <?= date('d F Y - H:i', strtotime($prescription->dispensed_at ?: ($prescription->created_at ?? date('Y-m-d H:i')))) ?> WITA</td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Metode Bayar</td>
                    <td class="p-0">: <span class="badge badge-teal"><?= strtoupper(esc($prescription->payment_method ?? 'Tunai')) ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted p-0">Status Tagihan</td>
                    <td class="p-0">: 
                        <?php if ($prescription->billing_status === 'paid' || !empty($prescription->is_paid)): ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                        <?php else: ?>
                            <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> BELUM LUNAS</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Table of Prescribed Medicines -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-items table-sm mb-0">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Nama Obat &amp; Aturan Pakai</th>
                    <th style="width: 80px;" class="text-center">Qty</th>
                    <th style="width: 140px;" class="text-right">Harga Satuan</th>
                    <th style="width: 150px;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($details as $d): 
                    $tusla = floatval($d->tusla ?? 0);
                    $embalase = floatval($d->embalase ?? 0);
                    $disc = floatval($d->discount ?? 0);
                    $gross = floatval($d->qty * $d->price);
                    $subtotal = max(0, $gross + $tusla + $embalase - $disc);
                    $effectiveUnitPrice = $d->qty > 0 ? ($subtotal / $d->qty) : $subtotal;
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong class="text-dark"><?= esc($d->medicine_name) ?></strong>
                            <?php if (!empty($d->dosage) && $d->dosage !== '-'): ?>
                                <div class="text-xs text-muted font-italic">
                                    <i class="fas fa-hand-holding-medical mr-1 text-teal"></i> Aturan: <?= esc($d->dosage) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-weight-bold"><?= (int)$d->qty ?> <?= esc($d->unit ?? 'Pcs') ?></td>
                        <td class="text-right">Rp <?= number_format($effectiveUnitPrice, 0, ',', '.') ?></td>
                        <td class="text-right font-weight-bold text-teal">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Calculation -->
    <div class="row justify-content-end mb-3">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                <tr>
                    <td class="text-muted">Subtotal Tagihan Obat:</td>
                    <td class="text-right font-weight-bold">Rp <?= number_format($summary['grand_total'] + $summary['total_discount'], 0, ',', '.') ?></td>
                </tr>
                <?php if ($summary['total_discount'] > 0): ?>
                <tr>
                    <td class="text-muted">Potongan Diskon Farmasi:</td>
                    <td class="text-right text-danger font-weight-bold">- Rp <?= number_format($summary['total_discount'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <tr style="border-top: 2px solid #0d9488; border-bottom: 2px solid #0d9488;">
                    <td class="font-weight-bold text-dark py-2" style="font-size: 14px;">TOTAL TAGIHAN RESEP:</td>
                    <td class="text-right font-weight-bold text-teal py-2" style="font-size: 16px;">
                        Rp <?= number_format($summary['grand_total'], 0, ',', '.') ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Terbilang Banner -->
    <div class="terbilang-box mb-4">
        <strong>Terbilang:</strong> <?= trim(terbilangResep($summary['grand_total'])) ?> Rupiah
    </div>

    <!-- Signatures & Verification Stamp -->
    <div class="row mt-4 pt-2">
        <div class="col-6 text-center">
            <div class="text-muted text-xs mb-1">Penerima Obat / Pasien / Keluarga</div>
            <div class="stamp-box"></div>
            <div class="font-weight-bold text-dark border-top pt-1 d-inline-block" style="min-width: 170px;">
                ( <?= esc($prescription->patient_name ?? 'Pasien') ?> )
            </div>
        </div>
        <div class="col-6 text-center">
            <div class="text-muted text-xs mb-1">Sumbawa Besar, <?= date('d F Y') ?></div>
            <div class="text-xs font-weight-bold text-dark mb-1">Petugas / Apoteker Farmasi</div>
            <div class="stamp-box d-flex align-items-center justify-content-center">
                <span class="badge badge-light border text-teal font-weight-bold px-3 py-1" style="font-size: 11px; opacity: 0.85;">
                    <i class="fas fa-stamp mr-1"></i> VERIFIED &amp; DISPENSED
                </span>
            </div>
            <div class="font-weight-bold text-dark border-top pt-1 d-inline-block" style="min-width: 170px;">
                ( <?= esc($prescription->pharmacist_name ?? session('username') ?? 'Apoteker Penanggung Jawab') ?> )
            </div>
        </div>
    </div>

    <div class="text-center text-muted mt-4 pt-2 border-top" style="font-size: 10px;">
        Dokumen kwitansi ini merupakan bukti pembayaran dan penyerahan resep obat yang sah yang diterbitkan oleh SIM-Klinik Sawamawa Medical Center.
    </div>
</div>

</body>
</html>
