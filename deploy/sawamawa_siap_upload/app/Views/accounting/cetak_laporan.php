<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Laporan Keuangan Resmi Standar Bank Indonesia & SAK') ?></title>
    
    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=fallback" rel="stylesheet">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            background: #f1f5f9;
            padding: 25px 15px;
            font-size: 11.5pt;
            line-height: 1.4;
        }

        .report-page {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 35px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .clinic-header {
            border-bottom: 3px double #0d9f4f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .table-report {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-report th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 700;
            font-size: 10.5pt;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }

        .table-report td {
            font-size: 10pt;
            padding: 5px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .section-heading {
            font-size: 11.5pt;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1.5px solid #0d9f4f;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }

        .audit-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 9.5pt;
            font-weight: 800;
            border-radius: 4px;
        }

        .signature-section {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .report-page {
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

<!-- Tombol Aksi Cetak Navigasi -->
<div class="text-center mb-3 no-print">
    <button type="button" class="btn btn-success font-weight-bold px-4 py-2 mr-2 shadow-sm" onclick="window.print()">
        <i class="fas fa-print mr-1"></i> Cetak / Simpan Dokumen PDF (A4)
    </button>
    <button type="button" class="btn btn-outline-secondary font-weight-bold px-3 py-2" onclick="window.close()">
        <i class="fas fa-times mr-1"></i> Tutup
    </button>
</div>

<div class="report-page">
    
    <!-- KOP SURAT RESMI STANDAR KLINIK & PERBANKAN -->
    <div class="clinic-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 60px; max-width: 70px; object-fit: contain;" class="mr-3">
            <?php endif; ?>
            <div>
                <h3 class="font-weight-bold mb-0 text-dark" style="font-family:'Outfit', sans-serif; letter-spacing: -0.5px;">
                    <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?>
                </h3>
                <small class="text-secondary d-block font-weight-bold">
                    Izin Operasional Pelayanan Kesehatan No: <?= esc(clinic_setting('clinic_license_number', '445/012/DINKES/2024')) ?>
                </small>
                <small class="text-muted d-block"><?= esc(clinic_setting('clinic_address', 'Sumbawa Besar, Nusa Tenggara Barat')) ?></small>
                <small class="text-muted">Telp: <?= esc(clinic_setting('clinic_phone', '(0371) 23456')) ?> | Email: <?= esc(clinic_setting('clinic_email', 'info@sawamawamedicalcenter.id')) ?></small>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark border px-2 py-1 font-weight-bold" style="font-size: 9pt;">DOKUMEN KEUANGAN RESMI</span>
            <small class="d-block text-muted mt-1" style="font-size: 9pt;">Dicetak: <?= date('d/m/Y H:i:s') ?></small>
            <small class="text-teal font-weight-bold" style="font-size: 8.5pt;">Standar: SAK EMKM / Bank Indonesia</small>
        </div>
    </div>

    <!-- JUDUL UTAMA & PERIODE LAPORAN -->
    <div class="text-center mb-3">
        <h4 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px; text-transform: uppercase;">
            LAPORAN KEUANGAN & POSISI FISIK KONSOLIDASIAN
        </h4>
        <div class="text-secondary font-weight-500" style="font-size: 11pt;">
            Periode: <strong><?= date('d F Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d F Y', strtotime($endDate)) ?></strong>
        </div>
        <small class="text-muted font-italic">Satuan Angka: Rupiah Indonesia (IDR) &bull; Metode Pembukuan: Akrual SAK EMKM</small>
    </div>

    <!-- ========================================================================= -->
    <!-- I. LAPORAN LABA RUGI KOMPREHENSIF -->
    <!-- ========================================================================= -->
    <div class="section-heading d-flex justify-content-between align-items-center">
        <span>I. LAPORAN LABA RUGI KOMPREHENSIF (STATEMENT OF PROFIT OR LOSS)</span>
    </div>

    <table class="table-report mb-3">
        <thead>
            <tr>
                <th>Uraian Akun & Kategori Rekening</th>
                <th class="text-right" style="width: 220px;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENDAPATAN -->
            <tr style="background: #f8fafc;">
                <td colspan="2"><strong>A. PENDAPATAN OPERASIONAL UTAMA:</strong></td>
            </tr>
            <?php foreach ($revenueAccounts as $rev): ?>
                <tr>
                    <td class="pl-4"><?= esc($rev->code) ?> - <?= esc($rev->name) ?></td>
                    <td class="text-right font-mono">Rp <?= number_format($rev->balance, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="font-weight-bold bg-light">
                <td class="text-right">TOTAL PENDAPATAN OPERASIONAL (A):</td>
                <td class="text-right font-mono text-dark">Rp <?= number_format($totalRevenue, 2, ',', '.') ?></td>
            </tr>

            <!-- HPP -->
            <tr style="background: #f8fafc;">
                <td colspan="2"><strong>B. BEBAN POKOK PENDAPATAN (HPP OBAT & BAHAN BAKU):</strong></td>
            </tr>
            <?php if (!empty($cogsAccounts)): ?>
                <?php foreach ($cogsAccounts as $cog): ?>
                    <tr>
                        <td class="pl-4"><?= esc($cog->code) ?> - <?= esc($cog->name) ?></td>
                        <td class="text-right font-mono">Rp <?= number_format($cog->balance, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td class="pl-4 text-muted">5-101 - HPP Obat Farmasi & Bahan Baku Resto</td>
                    <td class="text-right font-mono">Rp 0,00</td>
                </tr>
            <?php endif; ?>
            <tr class="font-weight-bold bg-light">
                <td class="text-right">TOTAL BEBAN POKOK PENDAPATAN (B):</td>
                <td class="text-right font-mono text-dark">Rp <?= number_format($totalCogs, 2, ',', '.') ?></td>
            </tr>

            <!-- LABA KOTOR -->
            <tr class="font-weight-bold" style="background-color: #f1f5f9; font-size: 11pt;">
                <td class="text-right">LABA KOTOR / BRUTO (A - B):</td>
                <td class="text-right font-mono">Rp <?= number_format($grossProfit, 2, ',', '.') ?></td>
            </tr>

            <!-- BEBAN OPERASIONAL -->
            <tr style="background: #f8fafc;">
                <td colspan="2"><strong>C. BEBAN OPERASIONAL, GAJI, JASA MEDIS & ADMINISTRASI UMUM:</strong></td>
            </tr>
            <?php foreach ($expenseAccounts as $exp): ?>
                <tr>
                    <td class="pl-4"><?= esc($exp->code) ?> - <?= esc($exp->name) ?></td>
                    <td class="text-right font-mono">Rp <?= number_format($exp->balance, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="font-weight-bold bg-light">
                <td class="text-right">TOTAL BEBAN OPERASIONAL & UMUM (C):</td>
                <td class="text-right font-mono text-dark">Rp <?= number_format($totalExpense, 2, ',', '.') ?></td>
            </tr>

            <!-- LABA BERSIH -->
            <tr class="font-weight-bold" style="background-color: #f8fafc; border-top: 2px solid #0f172a; border-bottom: 2px solid #0f172a; font-size: 11.5pt;">
                <td class="text-right">
                    <strong>LABA / (RUGI) BERSIH TAHUN BERJALAN (A - B - C):</strong>
                </td>
                <td class="text-right font-mono">
                    Rp <?= number_format($netIncome, 2, ',', '.') ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ========================================================================= -->
    <!-- II. LAPORAN POSISI KEUANGAN (NERACA AUDIT STANDAR SAK / BI) -->
    <!-- ========================================================================= -->
    <div class="section-heading d-flex justify-content-between align-items-center">
        <span>II. LAPORAN POSISI KEUANGAN / NERACA (STATEMENT OF FINANCIAL POSITION)</span>
        <?php 
        $balanceDiff = abs($totalAsset - ($totalLiability + $totalEquity));
        $isBalanced  = ($balanceDiff < 0.01);
        ?>
        <span class="audit-badge <?= $isBalanced ? 'badge-success' : 'badge-danger' ?>">
            STATUS AUDIT: <?= $isBalanced ? '100% BALANCE (SEIMBANG)' : 'SELISIH Rp ' . number_format($balanceDiff, 2, ',', '.') ?>
        </span>
    </div>

    <div class="row">
        <!-- AKTIVA / ASET -->
        <div class="col-6 pr-2">
            <table class="table-report mb-2">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center bg-light">ASET (AKTIVA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #f8fafc;"><td colspan="2"><strong>1. Aset Lancar (Current Assets):</strong></td></tr>
                    <?php $sumCurrent = 0; ?>
                    <?php if (!empty($currentAssets)): ?>
                        <?php foreach ($currentAssets as $ca): ?>
                            <?php $sumCurrent += (float)$ca->balance; ?>
                            <tr>
                                <td class="pl-3"><?= esc($ca->code) ?> - <?= esc($ca->name) ?></td>
                                <td class="text-right font-mono">Rp <?= number_format($ca->balance, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="font-weight-bold bg-light">
                        <td class="text-right">Total Aset Lancar:</td>
                        <td class="text-right font-mono">Rp <?= number_format($sumCurrent, 2, ',', '.') ?></td>
                    </tr>

                    <tr style="background: #f8fafc;"><td colspan="2"><strong>2. Aset Tetap (Fixed Assets):</strong></td></tr>
                    <?php $sumFixed = 0; ?>
                    <?php if (!empty($fixedAssets)): ?>
                        <?php foreach ($fixedAssets as $fa): ?>
                            <?php $sumFixed += (float)$fa->balance; ?>
                            <tr>
                                <td class="pl-3"><?= esc($fa->code) ?> - <?= esc($fa->name) ?></td>
                                <td class="text-right font-mono">Rp <?= number_format($fa->balance, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td class="pl-3 text-muted">Aset Alkes & Inventaris</td><td class="text-right font-mono">Rp 0,00</td></tr>
                    <?php endif; ?>
                    <tr class="font-weight-bold bg-light">
                        <td class="text-right">Total Aset Tetap:</td>
                        <td class="text-right font-mono">Rp <?= number_format($sumFixed, 2, ',', '.') ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold" style="background-color: #f1f5f9; border-top: 2px solid #0f172a; font-size: 11pt;">
                        <td class="text-right">TOTAL ASET:</td>
                        <td class="text-right font-mono">Rp <?= number_format($totalAsset, 2, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- PASIVA / LIABILITAS & EKUITAS -->
        <div class="col-6 pl-2">
            <table class="table-report mb-2">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center bg-light">LIABILITAS &amp; EKUITAS (PASIVA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #f8fafc;"><td colspan="2"><strong>1. Liabilitas Jangka Pendek:</strong></td></tr>
                    <?php if (!empty($liabilityAccounts)): ?>
                        <?php foreach ($liabilityAccounts as $lia): ?>
                            <tr>
                                <td class="pl-3"><?= esc($lia->code) ?> - <?= esc($lia->name) ?></td>
                                <td class="text-right font-mono">Rp <?= number_format($lia->balance, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="font-weight-bold bg-light">
                        <td class="text-right">Total Liabilitas:</td>
                        <td class="text-right font-mono">Rp <?= number_format($totalLiability, 2, ',', '.') ?></td>
                    </tr>

                    <tr style="background: #f8fafc;"><td colspan="2"><strong>2. Ekuitas / Modal Bersih:</strong></td></tr>
                    <?php if (!empty($equityAccounts)): ?>
                        <?php foreach ($equityAccounts as $eq): ?>
                            <tr>
                                <td class="pl-3"><?= esc($eq->code) ?> - <?= esc($eq->name) ?></td>
                                <td class="text-right font-mono">Rp <?= number_format($eq->balance, 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr>
                        <td class="pl-3">Laba / (Rugi) Bersih Berjalan</td>
                        <td class="text-right font-mono font-weight-bold">Rp <?= number_format($netIncome, 2, ',', '.') ?></td>
                    </tr>
                    <tr class="font-weight-bold bg-light">
                        <td class="text-right">Total Ekuitas:</td>
                        <td class="text-right font-mono">Rp <?= number_format($totalEquity, 2, ',', '.') ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold" style="background-color: #f1f5f9; border-top: 2px solid #0f172a; font-size: 11pt;">
                        <td class="text-right">TOTAL PASIVA:</td>
                        <td class="text-right font-mono">Rp <?= number_format($totalLiability + $totalEquity, 2, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- III. CATATAN ATAS LAPORAN KEUANGAN (NOTES & POLICIES) -->
    <!-- ========================================================================= -->
    <div class="section-heading">
        III. CATATAN RINGKAS KEBIJAKAN AKUNTANSI (NOTES TO FINANCIAL STATEMENTS)
    </div>
    <div class="p-2 mb-3 bg-light rounded" style="font-size: 9.5pt; border: 1px solid #cbd5e1;">
        <ol class="mb-0 pl-3">
            <li><strong>Dasar Penyusunan:</strong> Laporan keuangan disusun berdasarkan Standar Akuntansi Keuangan Entitas Mikro, Kecil, dan Menengah (SAK EMKM) dan ketentuan transparansi pelaporan Bank Indonesia.</li>
            <li><strong>Pengakuan Pendapatan &amp; Beban:</strong> Menggunakan asas akrual (<em>Accrual Basis</em>), di mana pendapatan diakui saat pelayanan medis/obat diberikan dan beban diakui saat timbul kewajiban.</li>
            <li><strong>Persediaan Obat &amp; Bahan Baku:</strong> Dinilai berdasarkan metode <em>First-In, First-Out</em> (FIFO) dengan pencatatan mutasi otomatis secara periodik.</li>
        </ol>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR PENGESAHAN & TANDA TANGAN RESMI 3 PIHAK -->
    <!-- ========================================================================= -->
    <div class="signature-section">
        <table style="width: 100%; border: none; text-align: center; font-size: 10pt;">
            <tr style="border: none;">
                <td style="width: 33.3%; border: none; vertical-align: top;">
                    <strong>Disiapkan Oleh,</strong><br>
                    Bagian Keuangan &amp; Akuntansi<br><br><br><br><br>
                    <u><strong>( ............................................ )</strong></u><br>
                    Staf Akuntan / Finance
                </td>
                <td style="width: 33.3%; border: none; vertical-align: top;">
                    <strong>Diperiksa Oleh,</strong><br>
                    Internal Auditor / Manajer<br><br><br><br><br>
                    <u><strong>( ............................................ )</strong></u><br>
                    Manajer Operasional
                </td>
                <td style="width: 33.3%; border: none; vertical-align: top;">
                    <strong>Disetujui &amp; Disahkan Oleh,</strong><br>
                    Direktur Utama Klinik<br><br><br><br><br>
                    <u><strong><?= esc(clinic_setting('clinic_director', 'dr. Andi Wijaya, Sp.PD')) ?></strong></u><br>
                    Direktur Sawamawa Medical Center
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
