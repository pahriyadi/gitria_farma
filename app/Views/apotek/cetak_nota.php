<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Nota Penjualan Apotek') ?></title>
    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace, Arial;
            font-size: 12px;
            color: #000000;
            background: #ffffff;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
            max-width: 320px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000000;
            padding-bottom: 8px;
            margin-bottom: 8px;
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
        .info-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000000;
            padding-bottom: 6px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 8px;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }
        .items-table td {
            padding: 4px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            border-top: 1px dashed #000000;
            padding-top: 6px;
            font-size: 11px;
            margin-bottom: 12px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-row.grand {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            border-top: 1px dashed #000000;
            padding-top: 8px;
        }
        .no-print {
            background: #f4f6f9;
            padding: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .btn {
            padding: 5px 12px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            border: 1px solid #333;
            background: #fff;
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
    <div><strong>Pratinjau Nota Penjualan Obat Bebas</strong></div>
    <div style="margin-top: 5px;">
        <button onclick="window.print();" class="btn btn-print">Cetak Nota (Print)</button>
        <button onclick="window.close();" class="btn">Tutup</button>
    </div>
</div>

<div class="header">
    <h3>SAWAMAWA PHARMACY</h3>
    <p><strong>INSTALASI FARMASI & APOTEK</strong></p>
    <p>Jl. Kebangsaan No. 12, Sumbawa Besar</p>
    <p>Telp: (0371) 23456 | WA: 0812-3456-7890</p>
</div>

<table class="info-table">
    <tr>
        <td>No. Nota</td>
        <td>: <strong><?= esc($sale->sale_no) ?></strong></td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>: <?= date('d/m/Y H:i', strtotime($sale->created_at ?? $sale->sale_date)) ?></td>
    </tr>
    <tr>
        <td>Pelanggan</td>
        <td>: <?= esc($sale->customer_name) ?></td>
    </tr>
    <tr>
        <td>Kasir/Apoteker</td>
        <td>: <?= esc($sale->cashier_name ?? 'Farmasi') ?></td>
    </tr>
    <?php if (!empty($sale->doctor_name)): ?>
    <tr>
        <td><?= ($sale->prescription_type === 'online') ? 'Dokter Online' : 'Dokter Peresep' ?></td>
        <td>: <?= esc($sale->doctor_name) ?><?= ($sale->prescription_type === 'online') ? ' (Konsultasi Online)' : '' ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td>Metode Bayar</td>
        <td>: <?= strtoupper(esc($sale->payment_method)) ?></td>
    </tr>
</table>

<?php
// Group items: separate regular items and group racikan items
$displayItems = [];
$racikanGroups = [];

foreach ($items as $item) {
    if (!empty($item->is_racikan)) {
        $grpKey = !empty($item->racikan_group) ? $item->racikan_group : ($item->racikan_name ?: 'Racikan ' . $item->id);
        if (!isset($racikanGroups[$grpKey])) {
            $racikanGroups[$grpKey] = [
                'name'               => $item->racikan_name ?: 'Obat Racikan Khusus',
                'dosage_instruction' => $item->dosage_instruction ?: 'Sesuai petunjuk dokter/apoteker',
                'total_subtotal'     => 0,
                'count_items'        => 0
            ];
        }
        $racikanGroups[$grpKey]['total_subtotal'] += floatval($item->subtotal);
        $racikanGroups[$grpKey]['count_items']++;
        if (!empty($item->dosage_instruction) && empty($racikanGroups[$grpKey]['dosage_instruction'])) {
            $racikanGroups[$grpKey]['dosage_instruction'] = $item->dosage_instruction;
        }
    } else {
        $displayItems[] = $item;
    }
}
?>

<table class="items-table">
    <thead>
        <tr>
            <th>Item Obat</th>
            <th class="text-center" style="width: 35px;">Qty</th>
            <th class="text-right" style="width: 75px;">Harga</th>
            <th class="text-right" style="width: 75px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <!-- 1. Regular Non-Racikan Items -->
        <?php foreach ($displayItems as $item): ?>
            <tr>
                <td colspan="4" style="font-weight: bold; padding-top: 4px;">
                    <?= esc($item->medicine_name) ?>
                    <?php if (!empty($item->batch_no)): ?>
                        <small style="font-weight: normal; color: #555;">(B: <?= esc($item->batch_no) ?>)</small>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td style="color: #444; font-size: 10px; padding-left: 5px;">
                    <?= !empty($item->dosage_instruction) ? esc($item->dosage_instruction) : 'Obat Bebas' ?>
                </td>
                <td class="text-center"><?= $item->qty ?> <?= esc($item->unit) ?></td>
                <td class="text-right">
                    <?php 
                    $unitPrice = ($item->price * $item->qty + ($item->tusla ?? 0) + ($item->embalase ?? 0)) / max(1, $item->qty);
                    echo number_format($unitPrice, 0, ',', '.');
                    ?>
                </td>
                <td class="text-right font-weight-bold"><?= number_format($item->subtotal, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- 2. Racikan Packages (Masked - Komposisi bahan mentah disembunyikan untuk pasien) -->
        <?php foreach ($racikanGroups as $rg): ?>
            <tr>
                <td colspan="4" style="font-weight: bold; padding-top: 4px; color: #004d40;">
                    <span style="border: 1px solid #004d40; padding: 1px 3px; font-size: 9px; border-radius: 2px;">RACIKAN</span>
                    <?= esc($rg['name']) ?>
                </td>
            </tr>
            <tr>
                <td style="color: #444; font-size: 10px; padding-left: 5px;">
                    <?= esc($rg['dosage_instruction']) ?>
                </td>
                <td class="text-center">1 Paket</td>
                <td class="text-right"><?= number_format($rg['total_subtotal'], 0, ',', '.') ?></td>
                <td class="text-right font-weight-bold"><?= number_format($rg['total_subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="total-section">
    <div class="total-row">
        <span>Subtotal Obat:</span>
        <span>Rp <?= number_format($sale->total_amount + ($sale->tusla_amount ?? 0) + ($sale->embalase_amount ?? 0), 0, ',', '.') ?></span>
    </div>
    <?php if ($sale->discount_amount > 0): ?>
        <div class="total-row">
            <span>Diskon:</span>
            <span>- Rp <?= number_format($sale->discount_amount, 0, ',', '.') ?></span>
        </div>
    <?php endif; ?>
    <div class="total-row grand">
        <span>TOTAL:</span>
        <span>Rp <?= number_format($sale->grand_total, 0, ',', '.') ?></span>
    </div>
    <div class="total-row" style="margin-top: 4px;">
        <span>Bayar (<?= strtoupper($sale->payment_method) ?>):</span>
        <span>Rp <?= number_format($sale->paid_amount ?: $sale->grand_total, 0, ',', '.') ?></span>
    </div>
    <div class="total-row">
        <span>Kembalian:</span>
        <span>Rp <?= number_format($sale->change_amount, 0, ',', '.') ?></span>
    </div>
</div>

<div class="footer">
    <p><strong>TERIMA KASIH ATAS KUNJUNGAN ANDA</strong></p>
    <p>Semoga Lekas Sehat & Bugar Kembali</p>
    <p><em>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian sebelumnya.</em></p>
</div>

</body>
</html>
