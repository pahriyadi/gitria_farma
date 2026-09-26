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
            --primary-dark: #0f766e;
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
            -webkit-font-smoothing: antialiased;
        }

        .action-toolbar {
            max-width: 820px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .paper-document {
            max-width: 820px;
            margin: 0 auto;
            background: var(--paper-bg);
            padding: 40px 48px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            position: relative;
            box-sizing: border-box;
        }

        /* Watermark */
        .paper-document::before {
            content: "SAWAMAWA";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(13, 148, 136, 0.03);
            pointer-events: none;
            letter-spacing: 12px;
            z-index: 0;
        }

        .doc-header {
            position: relative;
            z-index: 1;
            border-bottom: 2.5px solid var(--primary-teal);
            padding-bottom: 16px;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .clinic-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .clinic-logo-icon {
            width: 48px;
            height: 48px;
            background: #ccfbf1;
            color: var(--primary-teal);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 1px solid #99f6e4;
        }

        .clinic-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-teal);
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .clinic-subtitle {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
        }

        .clinic-address {
            font-size: 10.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .doc-badge-panel {
            text-align: right;
        }

        .doc-type-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .doc-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-teal);
            margin-top: 5px;
        }

        .meta-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 22px;
        }

        .meta-item {
            display: flex;
            font-size: 12px;
            margin-bottom: 4px;
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

        .table-doc {
            position: relative;
            z-index: 1;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12.5px;
        }

        .table-doc th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 9px 12px;
            border-top: 1px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
        }

        .table-doc td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table-doc tfoot td {
            border-top: 2px solid #cbd5e1;
            border-bottom: none;
            padding: 10px 12px;
        }

        .sign-section {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-top: 35px;
            text-align: center;
        }

        .sign-card {
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 12px 8px;
            background: #fafafa;
        }

        .sign-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .sign-space {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sign-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            margin: 0 10px;
        }

        .sign-status {
            font-size: 10px;
            font-weight: 600;
            color: var(--primary-teal);
        }

        .doc-footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10.5px;
            color: var(--text-muted);
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000;
            }
            .action-toolbar {
                display: none !important;
            }
            .paper-document {
                border: none;
                box-shadow: none;
                padding: 15px 25px;
                max-width: 100%;
            }
            .paper-document::before {
                display: none;
            }
            .meta-grid {
                background: #fff !important;
                border: 1px solid #000 !important;
            }
            .table-doc th {
                background: #f1f5f9 !important;
                color: #000 !important;
                border-top: 1px solid #000 !important;
                border-bottom: 2px solid #000 !important;
            }
            .table-doc td {
                border-bottom: 1px solid #ccc !important;
            }
            .sign-card {
                background: #fff !important;
                border: 1px solid #999 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

<div class="action-toolbar">
    <div>
        <a href="<?= base_url('procurement/po') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar PR
        </a>
    </div>
    <div class="d-flex align-items-center">
        <button onclick="window.print();" class="btn btn-dark btn-sm font-weight-bold shadow-sm mr-2">
            <i class="fas fa-file-pdf mr-1"></i> Download PDF
        </button>
        <button onclick="window.print();" class="btn btn-teal btn-sm font-weight-bold shadow-sm" style="background: #0d9488; color: #fff;">
            <i class="fas fa-print mr-1"></i> Cetak Lembar PR
        </button>
    </div>
</div>

<div class="paper-document">
    <!-- Header -->
    <div class="doc-header">
        <div class="clinic-brand" style="display: flex; align-items: center; gap: 12px;">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 52px; max-width: 150px; object-fit: contain;">
            <?php else: ?>
                <div class="clinic-logo-icon">
                    <i class="fas fa-hospital"></i>
                </div>
            <?php endif; ?>
            <div>
                <div class="clinic-title"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
                <div class="clinic-subtitle"><?= esc(clinic_setting('clinic_tagline', 'Sistem Informasi Pengadaan Farmasi & Logistik Medis')) ?></div>
                <div class="clinic-address"><?= esc(clinic_setting('clinic_address')) ?> | Telp: <?= esc(clinic_setting('clinic_phone')) ?> | Izin: <?= esc(clinic_setting('clinic_license_number', '503/012/K-CLINIC/2024')) ?></div>
            </div>
        </div>
        <div class="doc-badge-panel">
            <span class="doc-type-badge"><i class="fas fa-file-lines mr-1"></i> PURCHASE REQUISITION</span>
            <div class="doc-number"><?= esc($pr->request_no) ?></div>
            <div class="text-muted small mt-1">Tgl: <?= date('d F Y', strtotime($pr->created_at)) ?></div>
        </div>
    </div>

    <!-- Metadata Panel -->
    <div class="meta-grid">
        <div>
            <div class="meta-item">
                <span class="meta-label">Unit Pemohon</span>
                <span class="meta-value">: <?= esc($pr->department ?: 'Apotek Farmasi') ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Petugas Pemohon</span>
                <span class="meta-value">: <?= esc($pr->requester_name ?: 'Petugas Unit') ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status Dokumen</span>
                <span class="meta-value">: 
                    <span class="badge badge-<?= $pr->status === 'approved' || $pr->status === 'completed' ? 'success' : ($pr->status === 'rejected' ? 'danger' : 'warning') ?> text-uppercase">
                        <?= esc($pr->status) ?>
                    </span>
                </span>
            </div>
        </div>
        <div>
            <div class="meta-item">
                <span class="meta-label">Supplier / Rekanan</span>
                <span class="meta-value">: <?= esc($pr->supplier_name) ?> (<?= esc($pr->supplier_code) ?>)</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Kontak Supplier</span>
                <span class="meta-value">: <?= esc($pr->supplier_phone) ?> <?= !empty($pr->pic_name) ? '(' . esc($pr->pic_name) . ')' : '' ?></span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Keperluan</span>
                <span class="meta-value">: <?= esc($pr->description ?: 'Restock rutin kebutuhan medis & farmasi') ?></span>
            </div>
        </div>
    </div>

    <!-- Itemized List Table -->
    <table class="table-doc">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">NO</th>
                <th>NAMA ITEM OBAT / BARANG / LAYANAN</th>
                <th style="width: 70px;" class="text-center">QTY</th>
                <th style="width: 80px;">SATUAN</th>
                <th style="width: 140px;" class="text-right">EST. HARGA SATUAN</th>
                <th style="width: 150px;" class="text-right">EST. SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php $no = 1; foreach ($items as $it): ?>
                    <tr>
                        <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                        <td>
                            <strong style="color: #0f172a;"><?= esc($it->item_name) ?></strong>
                            <small class="d-block text-muted text-uppercase" style="font-size: 10px;"><?= esc($it->item_type ?? 'obat') ?></small>
                        </td>
                        <td class="text-center font-weight-bold"><?= esc($it->qty) ?></td>
                        <td><?= esc($it->unit) ?></td>
                        <td class="text-right font-weight-medium">Rp <?= number_format($it->estimated_price, 2, ',', '.') ?></td>
                        <td class="text-right font-weight-bold" style="color: #0f172a;">Rp <?= number_format($it->subtotal, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td class="text-center">1</td>
                    <td><strong>Pengadaan Paket Anggaran Unit</strong></td>
                    <td class="text-center">1</td>
                    <td>Paket</td>
                    <td class="text-right">Rp <?= number_format($pr->total_amount, 2, ',', '.') ?></td>
                    <td class="text-right font-weight-bold">Rp <?= number_format($pr->total_amount, 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-weight-bold" style="font-size: 13px;">ESTIMASI TOTAL ANGGARAN PR:</td>
                <td class="text-right font-weight-bold" style="font-size: 15px; color: var(--primary-teal);">
                    Rp <?= number_format($pr->total_amount, 2, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Approval Stepper / Signatures -->
    <div class="sign-section">
        <div class="sign-card">
            <div class="sign-title">Diajukan Oleh</div>
            <div class="sign-space">
                <i class="fas fa-signature text-muted opacity-50" style="font-size: 24px;"></i>
            </div>
            <div class="sign-name"><?= esc($pr->requester_name ?: 'Petugas Pengadaan') ?></div>
            <div class="sign-status text-muted">Pemohon Unit</div>
        </div>

        <div class="sign-card">
            <div class="sign-title">Verifikasi Keuangan</div>
            <div class="sign-space">
                <?php 
                $step1 = array_filter($approvals ?? [], fn($a) => $a->step_level == 1);
                $s1 = reset($step1);
                ?>
                <?php if ($s1 && $s1->status === 'approved'): ?>
                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> VERIFIED</span>
                <?php elseif ($s1 && $s1->status === 'rejected'): ?>
                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> REJECTED</span>
                <?php else: ?>
                    <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> MENUNGGU</span>
                <?php endif; ?>
            </div>
            <div class="sign-name"><?= $s1 && $s1->approver_name ? esc($s1->approver_name) : 'Supervisor Keuangan' ?></div>
            <div class="sign-status"><?= $s1 && $s1->status === 'approved' ? date('d/m/Y H:i', strtotime($s1->updated_at ?? $s1->created_at)) : 'Level 1' ?></div>
        </div>

        <div class="sign-card">
            <div class="sign-title">Persetujuan Direksi</div>
            <div class="sign-space">
                <?php 
                $step2 = array_filter($approvals ?? [], fn($a) => $a->step_level == 2);
                $s2 = reset($step2);
                ?>
                <?php if ($s2 && $s2->status === 'approved'): ?>
                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> APPROVED</span>
                <?php elseif ($s2 && $s2->status === 'rejected'): ?>
                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> REJECTED</span>
                <?php else: ?>
                    <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-lock mr-1"></i> TAHAP 2</span>
                <?php endif; ?>
            </div>
            <div class="sign-name"><?= $s2 && $s2->approver_name ? esc($s2->approver_name) : 'Direktur Operasional' ?></div>
            <div class="sign-status"><?= $s2 && $s2->status === 'approved' ? date('d/m/Y H:i', strtotime($s2->updated_at ?? $s2->created_at)) : 'Level 2 Final' ?></div>
        </div>
    </div>

    <!-- Footer Security Seal -->
    <div class="doc-footer">
        <div>
            <i class="fas fa-shield-alt mr-1 text-teal"></i> Dokumen resmi ini digenerate secara elektronik melalui SIM-Klinik Sawamawa Medical Center.
        </div>
        <div>
            ID: <?= md5($pr->request_no . $pr->created_at) ?> | <?= date('d/m/Y H:i:s') ?> WITA
        </div>
    </div>
</div>

</body>
</html>
