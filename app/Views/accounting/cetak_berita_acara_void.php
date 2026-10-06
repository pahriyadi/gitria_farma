<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pembatalan Transaksi - <?= esc($log->void_number) ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0 0 4px;
            font-size: 16pt;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .header-title p {
            margin: 0;
            font-size: 9pt;
            color: #64748b;
        }
        .doc-title {
            text-align: center;
            margin: 16px 0;
            text-transform: uppercase;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 13pt;
            color: #dc2626;
            text-decoration: underline;
            letter-spacing: 0.8px;
        }
        .doc-title span {
            font-size: 10pt;
            font-weight: bold;
            color: #334155;
            font-family: monospace;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: top;
        }
        .info-table td.label {
            width: 28%;
            color: #475569;
            font-weight: bold;
        }
        .info-table td.val {
            width: 72%;
            color: #0f172a;
        }
        .box-reason {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .box-reason strong {
            color: #dc2626;
            display: block;
            margin-bottom: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
            font-size: 10pt;
        }
        .signature-table td {
            width: 33.33%;
            padding: 10px;
            vertical-align: top;
        }
        .sig-space {
            height: 65px;
        }
        .watermark {
            position: fixed;
            top: 35%;
            left: 20%;
            font-size: 90pt;
            color: rgba(220, 38, 38, 0.08);
            transform: rotate(-35deg);
            font-weight: 900;
            letter-spacing: 12px;
            pointer-events: none;
            z-index: -1;
        }
        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0d9488;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="watermark">VOID</div>

    <button class="btn-print" onclick="window.print();">🖨️ Cetak Berita Acara</button>

    <!-- Header Instansi -->
    <table class="header-table">
        <tr>
            <td style="width: 70px; vertical-align: middle;">
                <?php if (clinic_logo()): ?>
                    <img src="<?= clinic_logo() ?>" style="max-width: 60px; max-height: 60px;">
                <?php endif; ?>
            </td>
            <td class="header-title">
                <h2><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></h2>
                <p><?= esc(clinic_setting('clinic_address', 'Jl. Raya Sawamawa No. 123, Indonesia')) ?> | Telp: <?= esc(clinic_setting('clinic_phone', '(021) 12345678')) ?></p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h3>BERITA ACARA PEMBATALAN TRANSAKSI (VOID)</h3>
        <span>Nomor: <?= esc($log->void_number) ?></span>
    </div>

    <p style="font-size: 10pt; margin-bottom: 12px;">
        Pada hari ini, <strong><?= date('d F Y', strtotime($log->created_at)) ?></strong> pukul <strong><?= date('H:i:s', strtotime($log->created_at)) ?> WIB</strong>, telah dilakukan pembatalan resmi (*Void Transaksi*) pada sistem ERP dengan rincian data sebagai berikut:
    </p>

    <!-- Rincian Transaksi Asal -->
    <table class="info-table" border="1" style="border-collapse: collapse; border-color: #e2e8f0;">
        <tr>
            <td class="label">Tipe Modul Transaksi</td>
            <td class="val"><strong><?= strtoupper(str_replace('_', ' ', $log->transaction_type)) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Referensi Asal</td>
            <td class="val"><strong><?= esc($log->reference_number) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Total Nominal Dibatalkan</td>
            <td class="val"><strong style="color: #dc2626; font-size: 11pt;">Rp <?= number_format($log->total_amount, 0, ',', '.') ?></strong></td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran Asal</td>
            <td class="val"><?= strtoupper(esc($log->payment_method)) ?></td>
        </tr>
        <tr>
            <td class="label">Jurnal Pembalik Otomatis</td>
            <td class="val"><code><?= esc($log->reversal_journal_no ?: 'Tidak memerlukan reversing journal') ?></code></td>
        </tr>
        <tr>
            <td class="label">Kasir / Pemohon Void</td>
            <td class="val"><?= esc($log->cashier_name ?: $log->cashier_username) ?></td>
        </tr>
        <tr>
            <td class="label">Supervisor yang Mengesahkan</td>
            <td class="val"><strong><?= esc($log->supervisor_name ?: $log->supervisor_username) ?></strong> (<?= esc($log->supervisor_role_name ?? 'Supervisor') ?>)</td>
        </tr>
    </table>

    <!-- Alasan Pembatalan -->
    <div class="box-reason">
        <strong>Kategori &amp; Alasan Pembatalan:</strong>
        <p style="margin: 0; font-style: italic;">"<?= esc($log->reason_category) ?> - <?= esc($log->reason_detail) ?>"</p>
    </div>

    <p style="font-size: 9.5pt; color: #64748b; margin-top: 10px;">
        <em>*Catatan Sistem: Pembatalan ini telah diverifikasi dengan otorisasi PIN Supervisor. Jurnal akuntansi pembalik telah dibukukan dan mutasi stok barang/obat telah dipulihkan secara otomatis.</em>
    </p>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Kasir / Pemohon,
                <div class="sig-space"></div>
                <strong>( <?= esc($log->cashier_name ?: $log->cashier_username) ?> )</strong>
            </td>
            <td>
                Supervisor / Pengesah,
                <div class="sig-space"></div>
                <strong>( <?= esc($log->supervisor_name ?: $log->supervisor_username) ?> )</strong>
            </td>
            <td>
                Pimpinan / Keuangan,
                <div class="sig-space"></div>
                <strong>( ________________________ )</strong>
            </td>
        </tr>
    </table>
</body>
</html>
