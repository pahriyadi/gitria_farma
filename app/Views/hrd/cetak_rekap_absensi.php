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
            font-size: 12px;
            color: var(--text-main);
            margin: 0;
            padding: 20px 0 40px;
        }

        .action-toolbar {
            max-width: 960px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
        }

        .paper-document {
            max-width: 960px;
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
            font-size: 11px;
        }

        .table-doc th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 8px 8px;
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
        }

        .table-doc td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-doc tfoot td {
            border-top: 2px solid #cbd5e1;
            padding: 9px 8px;
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
            .paper-document { border: none; box-shadow: none; padding: 10px 15px; max-width: 100%; }
            .table-doc th { background: #f1f5f9 !important; color: #000 !important; border-top: 1px solid #000 !important; border-bottom: 2px solid #000 !important; }
            .table-doc td { border-bottom: 1px solid #ccc !important; }
            @page { size: A4 landscape; margin: 8mm; }
        }
    </style>
</head>
<body>

<div class="action-toolbar">
    <a href="<?= base_url('hrd/absensi#tab-recap') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Absensi
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
                <div class="text-secondary small font-weight-bold">Laporan Rekapitulasi Presensi & Disiplin Kerja Pegawai</div>
                <div class="text-muted text-xs"><?= esc(clinic_setting('clinic_address')) ?> | Telp: <?= esc(clinic_setting('clinic_phone')) ?></div>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark text-uppercase px-2 py-1">REKAPITULASI PRESENSI</span>
            <div class="font-weight-bold text-teal mt-1">Periode: <?= date('F', mktime(0, 0, 0, $month, 10)) ?> <?= esc($year) ?></div>
            <div class="text-muted small">Dicetak: <?= date('d/m/Y H:i') ?> WITA</div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row bg-light p-2 rounded mb-3 border text-xs">
        <div class="col-6">
            <strong>Unit Kerja:</strong> Seluruh Divisi (Klinik, Farmasi, Resto, Manajemen)<br>
            <strong>Total Karyawan Terdata:</strong> <?= count($recap) ?> Orang
        </div>
        <div class="col-6 text-right">
            <strong>Tujuan Dokumen:</strong> Evaluasi Disiplin & Rekonsiliasi Payroll Bulanan<br>
            <strong>Status Audit:</strong> Terverifikasi HRD
        </div>
    </div>

    <!-- Table -->
    <table class="table-doc">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">NO</th>
                <th style="width: 90px;">NIP</th>
                <th>NAMA PEGAWAI / DOKTER</th>
                <th>DEPARTEMEN</th>
                <th class="text-center">HADIR (H)</th>
                <th class="text-center">TERLAMBAT (T)</th>
                <th class="text-center">SAKIT (S)</th>
                <th class="text-center">IZIN (I)</th>
                <th class="text-center">CUTI (C)</th>
                <th class="text-center">ALPHA (A)</th>
                <th class="text-right">TOTAL TELAT</th>
                <th class="text-right">TOTAL LEMBUR</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $totH = 0; $totT = 0; $totS = 0; $totI = 0; $totC = 0; $totA = 0; $totLate = 0; $totOt = 0;
            foreach ($recap as $r): 
                $totH += $r->total_present;
                $totT += $r->total_late;
                $totS += $r->total_sick;
                $totI += $r->total_permit;
                $totC += $r->total_leave;
                $totA += $r->total_alpha;
                $totLate += $r->total_late_minutes;
                $totOt += $r->total_overtime_minutes;
            ?>
                <tr>
                    <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                    <td class="font-weight-bold text-teal"><?= esc($r->nip) ?></td>
                    <td>
                        <strong><?= esc($r->employee_name) ?></strong>
                        <small class="d-block text-muted"><?= esc($r->position ?: 'Staff') ?></small>
                    </td>
                    <td><?= esc($r->department) ?></td>
                    <td class="text-center font-weight-bold text-success"><?= $r->total_present ?></td>
                    <td class="text-center font-weight-bold text-warning"><?= $r->total_late ?></td>
                    <td class="text-center font-weight-bold text-info"><?= $r->total_sick ?></td>
                    <td class="text-center font-weight-bold text-primary"><?= $r->total_permit ?></td>
                    <td class="text-center font-weight-bold text-secondary"><?= $r->total_leave ?></td>
                    <td class="text-center font-weight-bold text-danger"><?= $r->total_alpha ?></td>
                    <td class="text-right"><?= number_format($r->total_late_minutes) ?> mnt</td>
                    <td class="text-right text-success font-weight-bold"><?= number_format($r->total_overtime_minutes) ?> mnt</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="bg-light font-weight-bold">
                <td colspan="4" class="text-right">TOTAL KESELURUHAN KLINIK:</td>
                <td class="text-center text-success"><?= $totH ?></td>
                <td class="text-center text-warning"><?= $totT ?></td>
                <td class="text-center text-info"><?= $totS ?></td>
                <td class="text-center text-primary"><?= $totI ?></td>
                <td class="text-center text-secondary"><?= $totC ?></td>
                <td class="text-center text-danger"><?= $totA ?></td>
                <td class="text-right"><?= number_format($totLate) ?> mnt</td>
                <td class="text-right text-success"><?= number_format($totOt) ?> mnt</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <div class="sign-section">
        <div class="sign-card">
            <div class="small text-muted mb-4">Dibuat Oleh (Koordinator Presensi / HRD),</div>
            <div class="sign-name">Staff HRD & Absensi</div>
            <div class="small text-muted">Sawamawa Medical Center</div>
        </div>

        <div class="sign-card">
            <div class="small text-muted mb-4">Disetujui Oleh (Direktur Operasional),</div>
            <div class="sign-name">Direktur Utama</div>
            <div class="small text-muted">Sawamawa Medical Center</div>
        </div>
    </div>
</div>

</body>
</html>
