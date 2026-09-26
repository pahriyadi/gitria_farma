<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .clinic-name {
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
            letter-spacing: 0.5px;
        }
        .clinic-sub {
            font-size: 11px;
            color: #64748b;
        }
        .doc-title {
            text-align: right;
        }
        .doc-badge {
            background-color: #0f172a;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .doc-no {
            font-family: monospace;
            font-size: 15px;
            font-weight: bold;
            color: #0d9488;
            margin-top: 4px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-items th {
            background-color: #f8fafc;
            color: #334155;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .table-items td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 12px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
        }
        .sign-box {
            border-top: 1px solid #94a3b8;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }
        @media print {
            body { background: transparent; padding: 0; }
            .container { box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 800px; margin: 0 auto 15px; display: flex; justify-content: space-between; align-items: center;">
    <a href="<?= base_url('apotek/gudang') ?>" style="color: #0d9488; text-decoration: none; font-weight: bold;">&larr; Kembali ke Gudang Farmasi</a>
    <button onclick="window.print()" style="background-color: #0d9488; color: #ffffff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer;">
        🖨️ Cetak Surat Jalan Mutasi (PDF)
    </button>
</div>

<div class="container">
    <!-- Header -->
    <div class="header">
        <div>
            <div class="clinic-name">SAWAMAWA MEDICAL CENTER</div>
            <div class="clinic-sub">Instalasi Farmasi, Gudang Logistik & Depo Pelayanan Medis</div>
            <div class="clinic-sub">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 22334</div>
        </div>
        <div class="doc-title">
            <span class="doc-badge">SURAT JALAN MUTASI STOK</span>
            <div class="doc-no"><?= esc($transfer->transfer_no) ?></div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Tanggal: <?= date('d F Y', strtotime($transfer->transfer_date)) ?></div>
        </div>
    </div>

    <!-- Metadata Grid -->
    <div class="meta-grid">
        <div>
            <div style="color: #64748b; font-weight: bold; margin-bottom: 3px;">LOKASI PENGIRIM (ASAL):</div>
            <div style="font-size: 13px; font-weight: bold; color: #0f172a;"><?= esc($transfer->source_name) ?> (<?= esc($transfer->source_code) ?>)</div>
            <div>Lokasi: <?= esc($transfer->source_location ?: 'Gedung Logistik Utama') ?></div>
            <div>Penanggung Jawab: <?= esc($transfer->source_pic ?: 'Apoteker Penanggung Jawab') ?></div>
        </div>
        <div>
            <div style="color: #64748b; font-weight: bold; margin-bottom: 3px;">LOKASI PENERIMA (TUJUAN):</div>
            <div style="font-size: 13px; font-weight: bold; color: #0d9488;"><?= esc($transfer->target_name) ?> (<?= esc($transfer->target_code) ?>)</div>
            <div>Lokasi: <?= esc($transfer->target_location ?: 'Depo Pelayanan') ?></div>
            <div>Penanggung Jawab: <?= esc($transfer->target_pic ?: 'Petugas Depo') ?></div>
        </div>
    </div>

    <?php if (!empty($transfer->notes)): ?>
    <div style="background-color: #fefce8; border: 1px solid #fef08a; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: #854d0e; margin-bottom: 15px;">
        <strong>Catatan / Keperluan:</strong> <?= esc($transfer->notes) ?>
    </div>
    <?php endif; ?>

    <!-- Items Table -->
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">NO</th>
                <th style="width: 100px;">KODE</th>
                <th>NAMA OBAT / ALKES</th>
                <th style="width: 110px;">BATCH NO</th>
                <th style="width: 90px; text-align: center;">EXPIRED</th>
                <th style="width: 70px; text-align: center;">JUMLAH</th>
                <th style="width: 70px;">SATUAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; $totQty = 0; foreach ($items as $it): 
                $totQty += (int)$it->qty;
            ?>
            <tr>
                <td style="text-align: center; font-weight: bold; color: #64748b;"><?= $no++ ?></td>
                <td style="font-family: monospace;"><?= esc($it->medicine_code) ?></td>
                <td><strong><?= esc($it->medicine_name) ?></strong></td>
                <td style="font-family: monospace;"><?= esc($it->batch_no ?: '-') ?></td>
                <td style="text-align: center;"><?= $it->expired_date ? date('d/m/Y', strtotime($it->expired_date)) : '-' ?></td>
                <td style="text-align: center; font-weight: bold; color: #0d9488;"><?= number_format($it->qty, 0, ',', '.') ?></td>
                <td><?= esc($it->unit) ?></td>
                <td style="color: #64748b;"><?= esc($it->notes ?: '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL OBAT YANG DIMUTASI:</td>
                <td style="text-align: center; color: #0d9488; font-size: 14px;"><?= number_format($totQty, 0, ',', '.') ?></td>
                <td colspan="2">Item</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <div>
            <div>Petugas Pengirim (Gudang Asal)</div>
            <div class="sign-box"><?= esc($transfer->source_pic ?: 'Petugas Logistik') ?></div>
        </div>
        <div>
            <div>Petugas Penerima (Depo Tujuan)</div>
            <div class="sign-box"><?= esc($transfer->target_pic ?: 'Petugas Depo') ?></div>
        </div>
        <div>
            <div>Mengetahui (Kepala Farmasi)</div>
            <div class="sign-box">Apt. Siti Rahmah, S.Farm</div>
        </div>
    </div>
</div>

</body>
</html>
