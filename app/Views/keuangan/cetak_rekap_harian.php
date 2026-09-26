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
            border: 1px solid #999;
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
        .footer-signs {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .sign-box {
            text-align: center;
            width: 220px;
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
        <p>BERITA ACARA REKAP PENERIMAAN KASIR & LAPORAN TUTUP SHIFT HARIAN</p>
        <p>Tanggal Transaksi: <strong><?= date('d F Y', strtotime($date)) ?></strong> | Waktu Cetak: <?= date('d/m/Y H:i:s') ?> WITA</p>
    </div>

    <div class="summary-box">
        <div class="summary-card">
            <small>Total Penerimaan</small>
            <h4>Rp <?= number_format($totalCollected, 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <small>Tunai / Cash Laci</small>
            <h4 style="color: #10b981;">Rp <?= number_format($totalCash, 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <small>Non-Tunai (QRIS/Bank)</small>
            <h4 style="color: #0284c7;">Rp <?= number_format($totalQris + $totalBank, 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <small>Pengeluaran Kas Kecil</small>
            <h4 style="color: #ef4444;">Rp <?= number_format($totalExpenses, 0, ',', '.') ?></h4>
        </div>
    </div>

    <h3>Daftar Kwitansi Pembayaran Pasien</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>No. Kwitansi</th>
                <th>Waktu</th>
                <th>Nama Pasien</th>
                <th>No. RM</th>
                <th>Metode</th>
                <th>Kasir</th>
                <th class="text-right">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php $no = 1; foreach ($transactions as $t): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><strong><?= esc($t->receipt_no) ?></strong></td>
                        <td><?= date('H:i', strtotime($t->created_at)) ?> WITA</td>
                        <td><?= esc($t->patient_name ?? 'Pasien Umum') ?></td>
                        <td><?= esc($t->no_rm ?? '-') ?></td>
                        <td style="text-transform: uppercase; font-size: 11px;"><?= esc($t->payment_method) ?></td>
                        <td><?= esc($t->cashier_name ?: 'Kasir Utama') ?></td>
                        <td class="text-right font-weight-bold">Rp <?= number_format($t->amount, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="7" class="text-right">TOTAL PENERIMAAN KASIR</th>
                    <th class="text-right">Rp <?= number_format($totalCollected, 0, ',', '.') ?></th>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data transaksi pembayaran pada tanggal ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-signs">
        <div class="sign-box">
            <p>Petugas Kasir,</p>
            <br><br><br>
            <p><strong>(............................................)</strong></p>
        </div>
        <div class="sign-box">
            <p>Supervisor Keuangan,</p>
            <br><br><br>
            <p><strong>(............................................)</strong></p>
        </div>
    </div>

</body>
</html>
