<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Panduan Operasional & Dokumentasi Alur Sistem ERP') ?></title>
    
    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=fallback" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #0f172a;
            background: #f8fafc;
            padding: 20px;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .guide-page {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 35px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .guide-header {
            border-bottom: 2.5px solid #0d9f4f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11.5pt;
            font-weight: 800;
            color: #0d9f4f;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 22px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .workflow-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .guide-page {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

<div class="text-center mb-3 no-print">
    <button type="button" class="btn btn-success font-weight-bold px-4 py-2 mr-2" onclick="window.print()">
        <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF Manual (A4)
    </button>
    <button type="button" class="btn btn-outline-secondary font-weight-bold px-3 py-2" onclick="window.close()">
        <i class="fas fa-times mr-1"></i> Tutup
    </button>
</div>

<div class="guide-page">
    <div class="guide-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 50px; max-width: 60px; object-fit: contain;" class="mr-3">
            <?php endif; ?>
            <div>
                <h4 class="font-weight-bold mb-0 text-dark" style="font-family:'Outfit', sans-serif;">
                    <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER & RESTO GIZI')) ?>
                </h4>
                <small class="text-secondary d-block font-weight-bold">Buku Pedoman Operasional &amp; Alur Sistem Terpadu</small>
                <small class="text-muted"><?= esc(clinic_setting('clinic_address', 'Sumbawa Besar, NTB')) ?> | Telp: <?= esc(clinic_setting('clinic_phone', '(0371) 23456')) ?></small>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark border px-2 py-1 font-weight-bold" style="font-size: 8.5pt;">DOKUMEN SOP RESMI</span>
            <small class="d-block text-muted mt-1" style="font-size: 8pt;">Versi Sistem: <?= esc(clinic_latest_version()) ?></small>
        </div>
    </div>

    <div class="text-center mb-3">
        <h5 class="font-weight-bold text-dark mb-0 text-uppercase">PANDUAN OPERASIONAL & ALUR KERJA SISTEM</h5>
        <small class="text-muted">Dokumen resmi tata cara operasional rawat jalan, farmasi, restoran, kasir, dan akuntansi</small>
    </div>

    <!-- BAGIAN 1: PETA ALUR BISNIS -->
    <div class="section-title">I. Peta Alur Bisnis Terintegrasi (Workflows)</div>
    <?php if (!empty($workflows)): ?>
        <?php foreach ($workflows as $idx => $wf): ?>
            <div class="workflow-box">
                <strong class="text-dark"><i class="fas fa-play text-teal mr-1"></i> <?= $idx + 1 ?>. <?= esc($wf->title) ?></strong> 
                <span class="badge badge-light border text-xs ml-1"><?= esc($wf->target_role) ?></span>
                <?php if (!empty($wf->summary)): ?>
                    <p class="mb-1 text-muted text-xs mt-1"><em><?= esc($wf->summary) ?></em></p>
                <?php endif; ?>
                <div class="text-dark text-xs mt-1">
                    <?= nl2br(esc($wf->content)) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- BAGIAN 2: PANDUAN LANGKAH PER PERAN -->
    <div class="section-title">II. Panduan Langkah per Peran Staf</div>
    <?php if (!empty($roleGuides)): ?>
        <?php foreach ($roleGuides as $idx => $rg): ?>
            <div class="workflow-box">
                <strong class="text-dark"><i class="fas fa-user-check text-teal mr-1"></i> <?= $idx + 1 ?>. <?= esc($rg->title) ?></strong>
                <span class="badge badge-light border text-xs ml-1"><?= esc($rg->target_role) ?></span>
                <div class="text-dark text-xs mt-2">
                    <?= nl2br(esc($rg->content)) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- BAGIAN 3: FAQ -->
    <?php if (!empty($faqs)): ?>
        <div class="section-title">III. Tanya Jawab Kendala Operasional (FAQ)</div>
        <?php foreach ($faqs as $idx => $fq): ?>
            <div class="workflow-box">
                <strong class="text-dark">Q<?= $idx + 1 ?>: <?= esc($fq->title) ?></strong>
                <div class="text-muted text-xs mt-1">
                    <strong>Jawaban:</strong> <?= nl2br(esc($fq->content)) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="mt-4 p-2 bg-light rounded text-center text-xs text-muted border">
        Dokumen ini diterbitkan secara dinamis dari basis data sistem ERP Sawamawa Medical Center.
    </div>
</div>

</body>
</html>
