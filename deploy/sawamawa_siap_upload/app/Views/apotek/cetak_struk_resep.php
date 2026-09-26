<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Struk e-Resep Pasien') ?> - Sawamawa Medical Center</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 4mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace, Arial, sans-serif;
            font-size: 11.5px;
            color: #000000;
            background: #ffffff;
            line-height: 1.35;
            margin: 0;
            padding: 8px;
            max-width: 320px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .title-badge {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px dashed #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .info-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 6px;
            border-bottom: 1px dashed #000000;
            padding-bottom: 6px;
        }
        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 6px;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 100%;
            font-size: 11px;
            border-top: 1px dashed #000000;
            padding-top: 5px;
            margin-bottom: 8px;
        }
        .summary-table td {
            padding: 1.5px 0;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            padding: 4px 0 !important;
        }
        .footer {
            text-align: center;
            font-size: 9.5px;
            border-top: 1px dashed #000000;
            padding-top: 6px;
            margin-top: 6px;
        }
        .no-print {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 5px 12px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            border: 1px solid #333;
            background: #fff;
            font-size: 11px;
        }
        .btn-print {
            background: #00875a;
            color: #fff;
            border: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <div style="margin-bottom: 5px;"><strong>Struk Penyerahan e-Resep Pasien</strong></div>
    <button onclick="window.print();" class="btn btn-print">Cetak Struk (Print)</button>
    <button onclick="window.close();" class="btn">Tutup</button>
</div>

<div class="header">
    <h3>SAWAMAWA MEDICAL CENTER</h3>
    <p><strong>INSTALASI FARMASI & APOTEK</strong></p>
    <p>Jl. Kebangsaan No. 12, Sumbawa Besar</p>
    <p>Telp: (0371) 23456 | WA: 0812-3456-7890</p>
</div>

<div class="title-badge">
    BUKTI PENYERAHAN e-RESEP
</div>

<table class="info-table">
    <tr>
        <td style="width: 35%;">No. Resep</td>
        <td>: <strong>RSP-<?= str_pad($prescription->id, 4, '0', STR_PAD_LEFT) ?></strong></td>
    </tr>
    <tr>
        <td>No. Rawat / Kunj.</td>
        <td>: <?= esc($prescription->no_visit ?? '-') ?></td>
    </tr>
    <tr>
        <td>No. Kwitansi</td>
        <td>: <?= esc($prescription->billing_no ?? $prescription->receipt_no ?? '-') ?></td>
    </tr>
    <tr>
        <td>Waktu Ambil</td>
        <td>: <?= date('d/m/Y H:i', strtotime($prescription->dispensed_at ?: ($prescription->created_at ?? date('Y-m-d H:i')))) ?></td>
    </tr>
    <tr>
        <td>Pasien</td>
        <td>: <strong><?= esc($prescription->patient_name ?? 'Pasien Umum') ?></strong></td>
    </tr>
    <tr>
        <td>No. RM</td>
        <td>: <?= esc($prescription->no_rm ?? '-') ?></td>
    </tr>
    <tr>
        <td>Dokter Peresep</td>
        <td>: <?= esc($prescription->doctor_name ?? 'Dokter Pemeriksa') ?></td>
    </tr>
    <tr>
        <td>Status Bayar</td>
        <td>: <strong><?= ($prescription->billing_status === 'paid' || !empty($prescription->is_paid)) ? 'LUNAS (' . strtoupper(esc($prescription->payment_method ?? 'TUNAI')) . ')' : 'BELUM LUNAS' ?></strong></td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th>Item Obat & Aturan</th>
            <th class="text-center" style="width: 35px;">Qty</th>
            <th class="text-right" style="width: 70px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($details as $d): 
            $tusla = floatval($d->tusla ?? 0);
            $embalase = floatval($d->embalase ?? 0);
            $disc = floatval($d->discount ?? 0);
            $gross = floatval($d->qty * $d->price);
            $itemSubtotal = max(0, $gross + $tusla + $embalase - $disc);
            $effectiveUnitPrice = $d->qty > 0 ? ($itemSubtotal / $d->qty) : $itemSubtotal;
        ?>
            <tr>
                <td colspan="3" style="padding-top: 4px;">
                    <strong><?= esc($d->medicine_name) ?></strong>
                    <?php if (!empty($d->dosage) && $d->dosage !== '-'): ?>
                        <div style="font-size: 10px; color: #333; font-style: italic;">
                            &bull; Aturan: <?= esc($d->dosage) ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="font-size: 10px; color: #555; padding-left: 6px;">
                    <?= (int)$d->qty ?> x @Rp <?= number_format($effectiveUnitPrice, 0, ',', '.') ?>
                    <?php if ($disc > 0): ?> (Diskon -Rp <?= number_format($disc, 0, ',', '.') ?>)<?php endif; ?>
                </td>
                <td class="text-center" style="font-size: 10px;"><?= (int)$d->qty ?></td>
                <td class="text-right font-weight-bold">Rp <?= number_format($itemSubtotal, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="summary-table">
    <?php if ($summary['total_discount'] > 0): ?>
    <tr>
        <td>Potongan Diskon</td>
        <td class="text-right">- Rp <?= number_format($summary['total_discount'], 0, ',', '.') ?></td>
    </tr>
    <?php endif; ?>
    <tr class="grand-total">
        <td>TOTAL BIAYA RESEP</td>
        <td class="text-right">Rp <?= number_format($summary['grand_total'], 0, ',', '.') ?></td>
    </tr>
</table>

<div class="footer">
    <p style="margin: 3px 0;"><strong>*** Semoga Lekas Sembuh ***</strong></p>
    <p style="margin: 2px 0;">Periksa kembali obat dan aturan pakai sebelum meninggalkan apotek.</p>
    <p style="margin: 2px 0; font-size: 8.5px; color: #666;">Dicetak: <?= date('d/m/Y H:i:s') ?> WITA</p>
</div>

<script>
    window.onload = function() {
        // Otomatis buka dialog print saat halaman dibuka jika diinginkan
        // window.print();
    };
</script>

</body>
</html>
