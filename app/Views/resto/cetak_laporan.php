<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?> - Sawamawa Medical Center</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 22%;
            text-align: center;
        }
        .summary-card h4 {
            margin: 5px 0 0 0;
            color: #0d9488;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th, td {
            padding: 7px 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer-sign {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .sign-box {
            text-align: center;
            width: 200px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0d9488; color: #fff; border: none; cursor: pointer; font-weight: bold; border-radius: 4px;">
            🖨️ Cetak Dokumen Ini
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #666; color: #fff; border: none; cursor: pointer; font-weight: bold; border-radius: 4px; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h2>SAWAMAWA MEDICAL CENTER</h2>
        <p>REKAP LAPORAN PENJUALAN RESTO & NUTRISI SEHAT</p>
        <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <div class="summary-box">
        <div class="summary-card">
            <small>Total Omset Penjualan</small>
            <h4>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <small>Total Transaksi</small>
            <h4><?= number_format($totalOrders, 0, ',', '.') ?> Pesanan</h4>
        </div>
        <div class="summary-card">
            <small>Rata-rata Transaksi</small>
            <h4>Rp <?= number_format($avgBasket, 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <small>Total Porsi & Item</small>
            <h4><?= number_format($totalItemsSold, 0, ',', '.') ?> Item</h4>
        </div>
    </div>

    <h3>Rincian Transaksi Penjualan</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>No. Pesanan</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Tipe</th>
                <th>Metode</th>
                <th class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ordersList)): ?>
                <?php $no = 1; foreach ($ordersList as $o): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><strong><?= esc($o->order_no) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
                        <td><?= esc($o->customer_name) ?></td>
                        <td style="text-transform: capitalize;"><?= esc(str_replace('_', ' ', $o->order_type)) ?></td>
                        <td style="text-transform: uppercase;"><?= esc($o->payment_method ?: 'tunai') ?></td>
                        <td class="text-right font-weight-bold">Rp <?= number_format($o->grand_total, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="6" class="text-right">TOTAL KESELURUHAN</th>
                    <th class="text-right">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></th>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Sumbawa, <?= date('d M Y') ?><br>Kepala Unit Resto & Gizi</p>
            <br><br><br>
            <p><strong>(............................................)</strong></p>
        </div>
    </div>

</body>
</html>
