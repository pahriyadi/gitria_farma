<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Sawamawa Medical Center</title>
    <!-- Fonts & Bootstrap 4 -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f1f5f9;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 30px 15px;
            color: #1e293b;
        }

        .action-toolbar {
            max-width: 450px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
        }

        /* Standard Asset Label Tag Sticker (8cm x 5cm) */
        .asset-tag {
            width: 420px;
            margin: 0 auto 20px;
            background: #ffffff;
            border: 2px solid #0d9488;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            position: relative;
            box-sizing: border-box;
        }

        .tag-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1.5px solid #0d9488;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .tag-title {
            font-size: 13px;
            font-weight: 800;
            color: #0d9488;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .tag-subtitle {
            font-size: 9.5px;
            font-weight: 600;
            color: #64748b;
        }

        .tag-body {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .qr-box {
            width: 84px;
            height: 84px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-align: center;
        }

        .tag-info {
            flex-grow: 1;
            font-size: 11.5px;
        }

        .tag-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 800;
            color: #0d9488;
            margin-bottom: 4px;
        }

        .tag-item-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 12.5px;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .tag-meta-line {
            color: #475569;
            font-size: 10.5px;
            line-height: 1.3;
        }

        .tag-footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .action-toolbar { display: none !important; }
            .asset-tag { border: 1.5px solid #000; box-shadow: none; margin: 0 0 15px 0; page-break-inside: avoid; }
            .tag-header { border-bottom: 1.5px solid #000; }
            .tag-title { color: #000; }
            .tag-code { color: #000; }
            .qr-box { border: 1px solid #000; }
        }
    </style>
</head>
<body>

<div class="action-toolbar">
    <a href="<?= base_url('inventaris/aset') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
    <button onclick="window.print();" class="btn btn-teal btn-sm font-weight-bold shadow-sm" style="background:#0d9488; color:#fff;">
        <i class="fas fa-print mr-1"></i> Cetak Label Stiker
    </button>
</div>

<!-- Primary Asset Label -->
<div class="asset-tag">
    <div class="tag-header">
        <div>
            <div class="tag-title"><i class="fas fa-hospital mr-1"></i> <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
            <div class="tag-subtitle">LABEL INVENTARIS ASET KLINIK RESMI</div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark" style="font-size: 9px;"><?= strtoupper(esc($asset->category)) ?></span>
        </div>
    </div>

    <div class="tag-body">
        <div class="qr-box">
            <i class="fas fa-qrcode fa-3x text-dark mb-1"></i>
            <span style="font-size: 8px; font-weight: 700; color: #0d9488;">VERIFIED</span>
        </div>

        <div class="tag-info">
            <div class="tag-code"><?= esc($asset->code) ?></div>
            <div class="tag-item-name"><?= esc($asset->name) ?></div>
            <div class="tag-meta-line"><strong>Merk / SN:</strong> <?= esc($asset->brand ?: '-') ?> <?= !empty($asset->serial_number) ? '(' . esc($asset->serial_number) . ')' : '' ?></div>
            <div class="tag-meta-line"><strong>Lokasi:</strong> <?= esc($asset->location) ?></div>
            <div class="tag-meta-line"><strong>PJ Aset:</strong> <?= esc($asset->pj_employee) ?></div>
            <div class="tag-meta-line"><strong>Tgl Perolehan:</strong> <?= date('d/m/Y', strtotime($asset->purchase_date)) ?></div>
        </div>
    </div>

    <div class="tag-footer">
        <span>DILARANG MELEPAS / MERUSAK LABEL INI</span>
        <span>SIM-KLINIK SAWAMAWA</span>
    </div>
</div>

</body>
</html>
