<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Sawamawa Medical Center</title>
    <!-- Fonts & Bootstrap 4 -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap">
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
            font-size: 13px;
            color: var(--text-main);
            margin: 0;
            padding: 20px 0 40px;
        }

        .action-toolbar {
            max-width: 800px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
        }

        .paper-document {
            max-width: 800px;
            margin: 0 auto;
            background: var(--paper-bg);
            padding: 38px 44px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .paper-document::before {
            content: "CONFIDENTIAL";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 65px;
            font-weight: 900;
            color: rgba(13, 148, 136, 0.03);
            pointer-events: none;
            letter-spacing: 10px;
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

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: 12px;
        }

        .meta-item {
            display: flex;
            margin-bottom: 3px;
        }
        .meta-item:last-child { margin-bottom: 0; }

        .meta-label {
            width: 130px;
            color: var(--text-muted);
            font-weight: 500;
            flex-shrink: 0;
        }

        .meta-value {
            color: var(--text-main);
            font-weight: 600;
        }

        .salary-breakdown {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .breakdown-box {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 14px;
            background: #ffffff;
        }

        .breakdown-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid var(--border-color);
            margin-bottom: 10px;
        }

        .salary-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 4px 0;
            border-bottom: 1px dashed #f1f5f9;
        }

        .salary-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            font-weight: 700;
            padding-top: 8px;
            margin-top: 4px;
            border-top: 1.5px solid var(--border-color);
        }

        .thp-banner {
            background: #f0fdfa;
            border: 1.5px solid #99f6e4;
            border-radius: 6px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .sign-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 25px;
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
            .paper-document::before { display: none; }
            .meta-grid { background: #fff !important; border: 1px solid #000 !important; }
            .breakdown-box { border: 1px solid #000 !important; }
            .thp-banner { background: #f8fafc !important; border: 1px solid #000 !important; }
            .sign-card { background: #fff !important; border: 1px solid #999 !important; }
            @page { size: A4 portrait; margin: 12mm; }
        }
    </style>
</head>
<body>

<div class="action-toolbar">
    <a href="<?= base_url('hrd/pegawai#tab-payroll') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Payroll
    </a>
    <div class="d-flex align-items-center">
        <button onclick="window.print();" class="btn btn-dark btn-sm font-weight-bold mr-2">
            <i class="fas fa-file-pdf mr-1"></i> Download PDF
        </button>
        <button onclick="window.print();" class="btn btn-teal btn-sm font-weight-bold text-white" style="background:#0d9488;">
            <i class="fas fa-print mr-1"></i> Cetak Slip Gaji
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
                <div class="text-secondary small font-weight-bold">Slip Gaji & Bukti Penerimaan Penghasilan Pegawai (Confidential)</div>
                <div class="text-muted text-xs"><?= esc(clinic_setting('clinic_address')) ?> | Telp: <?= esc(clinic_setting('clinic_phone')) ?></div>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark text-uppercase px-2 py-1">SLIP GAJI RESMI</span>
            <div class="font-weight-bold text-teal mt-1" style="font-family: monospace; font-size: 15px;"><?= esc($slip->payroll_code) ?></div>
            <div class="text-muted small">Periode: <strong><?= date('F', mktime(0, 0, 0, $slip->period_month, 10)) ?> <?= esc($slip->period_year) ?></strong></div>
        </div>
    </div>

    <!-- Metadata Grid -->
    <div class="meta-grid">
        <div>
            <div class="meta-item">
                <span class="meta-label">Nama Pegawai</span>
                <span class="meta-value">: <?= esc($slip->employee_name) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">NIP</span>
                <span class="meta-value">: <?= esc($slip->nip) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Jabatan / Posisi</span>
                <span class="meta-value">: <?= esc($slip->position ?: 'Staff') ?></span>
            </div>
        </div>
        <div>
            <div class="meta-item">
                <span class="meta-label">Departemen</span>
                <span class="meta-value">: <?= esc($slip->department) ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status Kerja</span>
                <span class="meta-value">: <span class="badge badge-light border text-uppercase"><?= esc($slip->employment_type ?? 'tetap') ?></span></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Rekening Payroll</span>
                <span class="meta-value">: <?= esc($slip->bank_name ?: 'Bank') ?> - <?= esc($slip->bank_account ?: '-') ?></span>
            </div>
        </div>
    </div>

    <!-- Salary Breakdown (Penerimaan vs Potongan) -->
    <div class="salary-breakdown">
        <!-- Penerimaan -->
        <div class="breakdown-box">
            <div class="breakdown-title text-success"><i class="fas fa-plus-circle mr-1"></i> A. PENGHASILAN (PENERIMAAN)</div>
            
            <div class="salary-row">
                <span>Gaji Pokok</span>
                <strong>Rp <?= number_format($slip->basic_salary, 0, ',', '.') ?></strong>
            </div>

            <?php if ($slip->allowance_position > 0): ?>
                <div class="salary-row">
                    <span>Tunjangan Jabatan</span>
                    <strong>Rp <?= number_format($slip->allowance_position, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($slip->allowance_transport > 0): ?>
                <div class="salary-row">
                    <span>Tunjangan Makan & Transport</span>
                    <strong>Rp <?= number_format($slip->allowance_transport, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($slip->doctor_medical_fee > 0): ?>
                <div class="salary-row">
                    <span>Jasa Medis & Komisi Tindakan</span>
                    <strong class="text-teal">Rp <?= number_format($slip->doctor_medical_fee, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($slip->overtime_bonus > 0): ?>
                <div class="salary-row">
                    <span>Lembur & Insentif</span>
                    <strong>Rp <?= number_format($slip->overtime_bonus, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php 
            $totalPenerimaan = $slip->basic_salary + $slip->allowance_position + $slip->allowance_transport + $slip->doctor_medical_fee + $slip->overtime_bonus;
            ?>
            <div class="salary-total-row text-success">
                <span>TOTAL PENGHASILAN KOTOR:</span>
                <span>Rp <?= number_format($totalPenerimaan, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Potongan -->
        <div class="breakdown-box">
            <div class="breakdown-title text-danger"><i class="fas fa-minus-circle mr-1"></i> B. POTONGAN</div>

            <div class="salary-row">
                <span>Iuran BPJS Kesehatan / TK</span>
                <strong>Rp <?= number_format($slip->deduction_bpjs, 0, ',', '.') ?></strong>
            </div>

            <?php if ($slip->deduction_tax > 0): ?>
                <div class="salary-row">
                    <span>Pajak Penghasilan (PPh 21)</span>
                    <strong>Rp <?= number_format($slip->deduction_tax, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($slip->deduction_other > 0): ?>
                <div class="salary-row">
                    <span>Kasbon / Potongan Lain</span>
                    <strong>Rp <?= number_format($slip->deduction_other, 0, ',', '.') ?></strong>
                </div>
            <?php endif; ?>

            <?php 
            $totalPotongan = $slip->deduction_bpjs + $slip->deduction_tax + $slip->deduction_other;
            ?>
            <div class="salary-total-row text-danger">
                <span>TOTAL POTONGAN:</span>
                <span>Rp <?= number_format($totalPotongan, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Take Home Pay Banner -->
    <div class="thp-banner">
        <div>
            <span class="text-xs font-weight-bold text-secondary text-uppercase d-block">TOTAL GAJI BERSIH DITERIMA (TAKE HOME PAY):</span>
            <span class="font-weight-bold text-dark" style="font-size: 11px;">
                Status Pembayaran: <span class="badge badge-success text-uppercase">LUNAS / <?= strtoupper(esc($slip->status)) ?></span>
            </span>
        </div>
        <div class="text-right">
            <h4 class="font-weight-bold text-teal mb-0">Rp <?= number_format($slip->net_salary, 0, ',', '.') ?></h4>
        </div>
    </div>

    <!-- Signatures -->
    <div class="sign-section">
        <div class="sign-card">
            <div class="small text-muted mb-4">Penerima (Pegawai Bersangkutan),</div>
            <div class="sign-name"><?= esc($slip->employee_name) ?></div>
            <div class="small text-muted">NIP: <?= esc($slip->nip) ?></div>
        </div>

        <div class="sign-card">
            <div class="small text-muted mb-4">Mengetahui (HRD & Keuangan),</div>
            <div class="sign-name">Manager HRD & Payroll</div>
            <div class="small text-muted">Sawamawa Medical Center</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-muted text-xs mt-4 pt-2 border-top">
        Dokumen slip gaji ini bersifat rahasia (*confidential*) dan diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.
    </div>
</div>

</body>
</html>
