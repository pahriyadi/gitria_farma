<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Sawamawa Medical Center</title>
    <!-- Fonts & Bootstrap 4 -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary-teal: #0d9488;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --paper-bg: #ffffff;
        }

        body {
            background: #f1f5f9;
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            color: var(--text-main);
            margin: 0;
            padding: 20px 0 40px;
        }

        .action-toolbar {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
        }

        .paper-document {
            max-width: 900px;
            margin: 0 auto;
            background: var(--paper-bg);
            padding: 35px 40px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .doc-header {
            border-bottom: 2.5px solid var(--primary-teal);
            padding-bottom: 14px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .clinic-title {
            font-size: 19px;
            font-weight: 800;
            color: var(--primary-teal);
            letter-spacing: -0.5px;
        }

        .table-doc {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11.5px;
        }

        .table-doc th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
        }

        .table-doc td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-doc tfoot td {
            border-top: 2px solid #cbd5e1;
            padding: 9px 10px;
        }

        .sign-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
            text-align: center;
        }

        .sign-card {
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 12px 10px;
            background: #fafafa;
        }

        .sign-name {
            font-size: 12px;
            font-weight: 700;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            margin: 0 30px;
        }

        @media print {
            body { background: #ffffff; padding: 0; color: #000; }
            .action-toolbar { display: none !important; }
            .paper-document { border: none; box-shadow: none; padding: 10px 20px; max-width: 100%; }
            .table-doc th { background: #f1f5f9 !important; color: #000 !important; border-top: 1px solid #000 !important; border-bottom: 2px solid #000 !important; }
            .table-doc td { border-bottom: 1px solid #ccc !important; }
            @page { size: A4 portrait; margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="action-toolbar">
    <a href="<?= base_url('inventaris/aset') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Inventaris
    </a>
    <div class="d-flex align-items-center">
        <button onclick="window.print();" class="btn btn-dark btn-sm font-weight-bold mr-2">
            <i class="fas fa-file-pdf mr-1"></i> Download PDF
        </button>
        <button onclick="window.print();" class="btn btn-teal btn-sm font-weight-bold text-white" style="background:#0d9488;">
            <i class="fas fa-print mr-1"></i> Cetak Dokumen Rekap
        </button>
    </div>
</div>

<div class="paper-document">
    <!-- Header -->
    <div class="doc-header">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 50px; max-width: 140px; object-fit: contain;">
            <?php else: ?>
                <div class="clinic-logo-icon text-teal" style="font-size: 32px;">
                    <i class="fas fa-hospital"></i>
                </div>
            <?php endif; ?>
            <div>
                <div class="clinic-title text-teal font-weight-bold" style="font-size: 18px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
                <div class="text-secondary small font-weight-bold">Laporan Rekapitulasi Inventaris Aset Aktif Klinik</div>
                <div class="text-muted text-xs"><?= esc(clinic_setting('clinic_address')) ?> | Telp: <?= esc(clinic_setting('clinic_phone')) ?></div>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark text-uppercase px-2 py-1">LAPORAN ASET INVENTARIS</span>
            <div class="text-muted small mt-1">Dicetak: <?= date('d F Y H:i') ?> WITA</div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row bg-light p-2 rounded mb-3 border text-xs">
        <div class="col-6">
            <strong>Total Unit Aset:</strong> <?= count($assets) ?> Barang<br>
            <strong>Status Audit:</strong> Inventaris Aktif Terverifikasi
        </div>
        <div class="col-6 text-right">
            <strong>Total Nilai Perolehan:</strong> Rp <?= number_format($totalAcquisition, 0, ',', '.') ?><br>
            <strong>Total Nilai Buku Saat Ini:</strong> <span class="font-weight-bold text-teal">Rp <?= number_format($totalBookValue, 0, ',', '.') ?></span>
        </div>
    </div>

    <!-- Table -->
    <table class="table-doc">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">NO</th>
                <th style="width: 100px;">KODE ASET</th>
                <th>NAMA BARANG / MERK</th>
                <th>KATEGORI</th>
                <th>LOKASI RUANGAN</th>
                <th>PJ ASET</th>
                <th class="text-right">HARGA BELI</th>
                <th class="text-right">NILAI BUKU</th>
                <th class="text-center">KONDISI</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($assets as $a): ?>
                <tr>
                    <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                    <td class="font-weight-bold text-teal"><?= esc($a->code) ?></td>
                    <td>
                        <strong><?= esc($a->name) ?></strong>
                        <?php if (!empty($a->brand)): ?>
                            <small class="d-block text-muted"><?= esc($a->brand) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($a->category) ?></td>
                    <td><?= esc($a->location) ?></td>
                    <td><?= esc($a->pj_employee) ?></td>
                    <td class="text-right">Rp <?= number_format($a->price, 0, ',', '.') ?></td>
                    <td class="text-right font-weight-bold text-dark">Rp <?= number_format($a->current_value !== null ? $a->current_value : $a->price, 0, ',', '.') ?></td>
                    <td class="text-center font-weight-bold text-uppercase"><?= esc($a->condition_status) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="bg-light font-weight-bold">
                <td colspan="6" class="text-right">TOTAL NILAI KESELURUHAN ASET:</td>
                <td class="text-right">Rp <?= number_format($totalAcquisition, 0, ',', '.') ?></td>
                <td class="text-right text-teal">Rp <?= number_format($totalBookValue, 0, ',', '.') ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <div class="sign-section">
        <div class="sign-card">
            <div class="small text-muted mb-4">Penanggung Jawab Inventaris,</div>
            <div class="sign-name">Staff Logistik & Sarpras</div>
            <div class="small text-muted">Sawamawa Medical Center</div>
        </div>

        <div class="sign-card">
            <div class="small text-muted mb-4">Mengetahui (Pimpinan / Direksi),</div>
            <div class="sign-name">Direktur Operasional</div>
            <div class="small text-muted">Sawamawa Medical Center</div>
        </div>
    </div>
</div>

</body>
</html>
