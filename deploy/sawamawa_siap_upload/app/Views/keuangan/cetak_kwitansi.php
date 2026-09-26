<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kwitansi Pembayaran') ?> - Sawamawa Medical Center</title>
    <!-- Google Font: Source Sans Pro & Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            color: #212529;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13.5px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            border: 1px solid #b8b8b8;
            padding: 35px 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .clinic-header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .clinic-title {
            font-size: 22px;
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
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .table-items th {
            background-color: #f8f9fa;
            border-top: 1px solid #b8b8b8;
            border-bottom: 2px solid #b8b8b8;
            font-weight: 700;
            color: #333333;
            font-size: 13px;
        }
        .table-items td {
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        .total-box {
            background-color: #f8f9fa;
            border: 1px solid #b8b8b8;
            padding: 14px 18px;
            border-radius: 4px;
        }
        .terbilang-box {
            background-color: #f0fdfa;
            border-left: 4px solid #0d9488;
            padding: 8px 12px;
            font-size: 12.5px;
            font-style: italic;
            color: #0f766e;
        }
        .btn-print {
            background-color: #0d9488;
            color: #ffffff;
            font-weight: 700;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background-color: #0f766e;
            color: #ffffff;
        }
        @media print {
            body {
                background: #ffffff;
                color: #000000;
                font-size: 12px;
            }
            .receipt-container {
                max-width: 100%;
                margin: 0;
                padding: 10px 20px;
                border: none;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
            .clinic-header {
                border-bottom: 2px solid #000000;
            }
            .total-box {
                background-color: #ffffff;
                border: 1px solid #000000;
            }
            .terbilang-box {
                background-color: #ffffff;
                border-left: 3px solid #000000;
                color: #000000;
            }
        }
    </style>
</head>
<body>

<?php
function terbilangAngka($angka) {
    $angka = abs($angka);
    $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $terbilang = "";
    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilangAngka($angka - 10) . " Belas";
    } else if ($angka < 100) {
        $terbilang = terbilangAngka($angka / 10) . " Puluh" . terbilangAngka($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " Seratus" . terbilangAngka($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilangAngka($angka / 100) . " Ratus" . terbilangAngka($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " Seribu" . terbilangAngka($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilangAngka($angka / 1000) . " Ribu" . terbilangAngka($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilangAngka($angka / 1000000) . " Juta" . terbilangAngka($angka % 1000000);
    } else if ($angka < 1000000000000) {
        $terbilang = terbilangAngka($angka / 1000000000) . " Miliar" . terbilangAngka(fmod($angka, 1000000000));
    }
    return $terbilang;
}
?>

<div class="container">
    <!-- Action Bar (No Print) -->
    <div class="row no-print mt-3 mb-2" style="max-width: 800px; margin: 0 auto;">
        <div class="col-6">
            <a href="<?= base_url('keuangan/kasir') ?>" class="btn btn-outline-secondary font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kasir
            </a>
        </div>
        <div class="col-6 text-right">
            <?php if (!empty($receipt->visit_id)): ?>
                <a href="<?= base_url('apotek/cetak-etiket/' . $receipt->visit_id) ?>" target="_blank" class="btn btn-outline-info font-weight-bold mr-2">
                    <i class="fas fa-prescription mr-1"></i> Cetak E-Tiket Obat
                </a>
            <?php endif; ?>
            <button onclick="window.print();" class="btn btn-print">
                <i class="fas fa-print mr-1"></i> Cetak Kwitansi
            </button>
        </div>
    </div>

    <!-- Official Receipt Sheet -->
    <div class="receipt-container">
        <!-- Clinic Header -->
        <div class="clinic-header d-flex justify-content-between align-items-center">
            <div>
                <div class="clinic-title"><i class="fas fa-hospital-alt mr-1"></i> SAWAMAWA MEDICAL CENTER</div>
                <div class="text-secondary small font-weight-bold">Layanan Kesehatan Primer, Spesialis, Apotek Farmasi & Resto Gizi</div>
                <div class="text-muted text-xs">Izin Operasional: 440/102/SIP-K/DINKES/2026 | NPWP: 98.765.432.1-912.000</div>
                <div class="text-muted text-xs">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 23456 / WA: 0812-3456-7890</div>
            </div>
            <div class="text-right">
                <span class="receipt-badge text-uppercase"><i class="fas fa-receipt mr-1"></i> KWITANSI PEMBAYARAN</span>
                <div class="font-weight-bold text-dark mt-2" style="font-size: 16px;"><?= esc($receipt->receipt_no) ?></div>
                <div class="text-muted small">Tgl: <?= date('d/m/Y H:i', strtotime($receipt->created_at)) ?> WITA</div>
            </div>
        </div>

        <!-- Patient & Visit Meta -->
        <div class="row mb-3 pb-2 border-bottom">
            <div class="col-6">
                <table class="table-sm table-borderless p-0 m-0 text-sm">
                    <tr>
                        <td class="text-secondary pl-0" style="width: 130px;">Nama Pasien</td>
                        <td class="font-weight-bold text-dark">: <?= esc($receipt->patient_name ?? 'Pasien Umum') ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">No. Rekam Medis (RM)</td>
                        <td class="font-weight-bold text-teal">: <?= esc($receipt->no_rm ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">No. Kunjungan / Visit</td>
                        <td class="text-dark">: <?= esc($receipt->no_visit ?? '-') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table-sm table-borderless p-0 m-0 text-sm">
                    <tr>
                        <td class="text-secondary pl-0" style="width: 130px;">No. Tagihan (Billing)</td>
                        <td class="font-weight-bold text-dark">: <?= esc($receipt->billing_no) ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">Layanan / Poli</td>
                        <td class="text-dark">: <?= esc($receipt->poly_name ?: ($receipt->tindakan_name ?: 'Pelayanan Klinik')) ?></td>
                    </tr>
                    <tr>
                        <td class="text-secondary pl-0">Dokter Pemeriksa</td>
                        <td class="text-dark">: <?= esc($receipt->doctor_name ?? 'Dokter Klinik') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Itemized List Table -->
        <table class="table table-sm table-items mb-3">
            <thead>
                <tr>
                    <th style="width: 40px;" class="text-center">NO</th>
                    <th>RINCIAN ITEM / LAYANAN / OBAT</th>
                    <th style="width: 90px;" class="text-center">JENIS</th>
                    <th style="width: 60px;" class="text-center">QTY</th>
                    <th style="width: 120px;" class="text-right">HARGA (RP)</th>
                    <th style="width: 130px;" class="text-right">SUBTOTAL (RP)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($details)): ?>
                    <?php $no = 1; foreach ($details as $item): ?>
                        <tr>
                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($item->item_name) ?></strong>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-<?= $item->item_type === 'medis' ? 'teal' : ($item->item_type === 'obat' ? 'info' : 'warning text-dark') ?>" style="font-size: 10px;">
                                    <?= strtoupper(esc($item->item_type)) ?>
                                </span>
                            </td>
                            <td class="text-center font-weight-bold"><?= esc($item->qty) ?></td>
                            <td class="text-right"><?= number_format($item->price, 0, ',', '.') ?></td>
                            <td class="text-right font-weight-bold"><?= number_format($item->subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center">1</td>
                        <td>Pelayanan Medis & Konsultasi Pasien</td>
                        <td class="text-center"><span class="badge badge-teal">MEDIS</span></td>
                        <td class="text-center">1</td>
                        <td class="text-right"><?= number_format($receipt->amount, 0, ',', '.') ?></td>
                        <td class="text-right font-weight-bold"><?= number_format($receipt->amount, 0, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Terbilang Banner -->
        <div class="terbilang-box mb-3">
            <strong>Terbilang:</strong> <?= trim(terbilangAngka($receipt->amount)) ?> Rupiah
        </div>

        <!-- Totals & Payment Summary -->
        <div class="row">
            <div class="col-7">
                <div class="p-2 border bg-light text-xs mb-2">
                    <span class="font-weight-bold text-secondary text-uppercase d-block mb-1">Status Pembayaran:</span>
                    <span class="badge badge-success font-weight-bold px-2 py-1" style="font-size: 13px;">
                        <i class="fas fa-check-circle mr-1"></i> LUNAS (PAID)
                    </span>
                    <span class="ml-2 text-muted">Metode: <strong><?= strtoupper(esc($receipt->payment_method)) ?></strong></span>
                    <?php if (!empty($receipt->notes)): ?>
                        <div class="mt-1 text-muted">Catatan: <?= esc($receipt->notes) ?></div>
                    <?php endif; ?>
                </div>
                <div class="text-muted text-xs font-italic">
                    * Kwitansi ini merupakan bukti pembayaran yang sah yang diterbitkan secara elektronik oleh Sistem Informasi Sawamawa Medical Center.
                </div>
            </div>
            <div class="col-5">
                <div class="total-box">
                    <div class="d-flex justify-content-between mb-1 text-secondary text-sm">
                        <span>Total Jasa Medis:</span>
                        <span class="font-weight-bold">Rp <?= number_format($receipt->total_services ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-secondary text-sm">
                        <span>Total Farmasi / Obat:</span>
                        <span class="font-weight-bold">Rp <?= number_format($receipt->total_medicines ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <?php if (($receipt->total_restaurant ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between mb-1 text-secondary text-sm">
                            <span>Total POS Resto:</span>
                            <span class="font-weight-bold">Rp <?= number_format($receipt->total_restaurant, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (($receipt->discount ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between mb-1 text-danger text-sm">
                            <span>Diskon / Potongan:</span>
                            <span class="font-weight-bold">- Rp <?= number_format($receipt->discount, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between text-dark mb-1">
                        <span class="font-weight-bold">TOTAL TAGIHAN:</span>
                        <span class="font-weight-bold h5 text-teal mb-0">Rp <?= number_format($receipt->amount, 0, ',', '.') ?></span>
                    </div>
                    <?php if (($receipt->paid_amount ?? 0) > 0): ?>
                        <div class="d-flex justify-content-between text-muted text-sm mb-1">
                            <span>Uang Diterima:</span>
                            <span class="font-weight-bold text-success">Rp <?= number_format($receipt->paid_amount, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted text-sm">
                            <span>Kembalian:</span>
                            <span class="font-weight-bold text-dark">Rp <?= number_format($receipt->change_amount ?? 0, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Signatures & QR Verification -->
        <div class="row mt-4 pt-3 align-items-center text-center">
            <div class="col-4">
                <div class="text-muted small">Pasien / Keluarga,</div>
                <div style="height: 45px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( <?= esc($receipt->patient_name ?? 'Pasien') ?> )
                </div>
            </div>
            <div class="col-4">
                <div class="d-inline-block p-1 border bg-white rounded shadow-sm text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=75x75&data=<?= urlencode(base_url('keuangan/cetak-kwitansi/' . $receipt->id)) ?>" alt="QRIS / Verifikasi Digital" style="width: 70px; height: 70px;">
                    <small class="d-block text-muted font-weight-bold" style="font-size: 9px; margin-top: 2px;">QRIS / VALIDASI SAH</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-muted small">Sumbawa Besar, <?= date('d F Y') ?><br>Petugas Kasir,</div>
                <div style="height: 35px;"></div>
                <div class="font-weight-bold text-dark border-top d-inline-block pt-1 px-3">
                    ( <?= esc($receipt->cashier_name ?: (session('username') ?: 'Kasir Utama')) ?> )
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
