<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak Buku Jurnal Umum') ?> - <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></title>
    
    <!-- Google Font & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE / Bootstrap 4 Minimal Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .report-page {
            background: #ffffff;
            width: 297mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 15mm 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            box-sizing: border-box;
            border-radius: 4px;
        }

        .clinic-header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .table-jurnal {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .table-jurnal th {
            background-color: #0f766e !important;
            color: #ffffff !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 8px;
            border: 1px solid #0d9488;
            vertical-align: middle;
        }

        .table-jurnal td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .table-jurnal tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .journal-group-border {
            border-top: 2px solid #cbd5e1 !important;
        }

        .font-mono {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .badge-balance {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 10px;
        }

        .signature-box {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        @page {
            size: A4 landscape;
            margin: 8mm 10mm 10mm 10mm;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .report-page {
                width: 100%;
                min-height: auto;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
            }
            .table-jurnal th {
                background-color: #0f766e !important;
                color: #ffffff !important;
            }
        }
    </style>
</head>
<body>

<!-- Action Bar (Hidden during Print) -->
<div class="container-fluid text-center mb-3 no-print">
    <div class="d-inline-flex align-items-center bg-white p-2 rounded shadow-sm border">
        <button type="button" class="btn btn-success btn-sm font-weight-bold px-3 mr-2" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF (A4)
        </button>
        <a href="<?= base_url('accounting/jurnal/export-excel?' . http_build_query(['source_module' => $filterModule, 'start_date' => $filterStart, 'end_date' => $filterEnd])) ?>" class="btn btn-outline-success btn-sm font-weight-bold px-3 mr-2">
            <i class="fas fa-file-excel mr-1"></i> Unduh File Excel (.xls)
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold px-3" onclick="window.close()">
            <i class="fas fa-times mr-1"></i> Tutup
        </button>
    </div>
</div>

<div class="report-page">
    
    <!-- KOP SURAT KLINIK RESMI -->
    <div class="clinic-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 55px; max-width: 65px; object-fit: contain;" class="mr-3">
            <?php endif; ?>
            <div>
                <h4 class="font-weight-bold mb-0 text-dark" style="font-family:'Outfit', sans-serif; letter-spacing: -0.3px;">
                    <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?>
                </h4>
                <small class="text-secondary d-block font-weight-bold" style="font-size: 10px;">
                    Izin Operasional Fasilitas Pelayanan Kesehatan No: <?= esc(clinic_setting('clinic_license_number', '445/012/DINKES/2024')) ?>
                </small>
                <small class="text-muted d-block" style="font-size: 9.5px;"><?= esc(clinic_setting('clinic_address', 'Sumbawa Besar, Nusa Tenggara Barat')) ?></small>
                <small class="text-muted" style="font-size: 9.5px;">Telp: <?= esc(clinic_setting('clinic_phone', '(0371) 23456')) ?> | Email: <?= esc(clinic_setting('clinic_email', 'info@sawamawamedicalcenter.id')) ?></small>
            </div>
        </div>
        <div class="text-right">
            <span class="badge badge-dark border px-2 py-1 font-weight-bold" style="font-size: 8.5pt;">DOKUMEN AKUNTANSI RESMI</span>
            <small class="d-block text-muted mt-1" style="font-size: 8pt;">Dicetak: <?= date('d/m/Y H:i:s') ?> WITA</small>
            <small class="text-teal font-weight-bold d-block" style="font-size: 8pt;">Standar: SAK EMKM / Bank Indonesia</small>
        </div>
    </div>

    <!-- REPORT TITLE & METADATA -->
    <div class="text-center mb-3">
        <h5 class="font-weight-bold text-dark mb-1" style="font-family:'Outfit', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">
            BUKU JURNAL UMUM KONSOLIDASIAN &amp; PENYESUAIAN
        </h5>
        <div class="text-muted font-weight-500" style="font-size: 10.5px;">
            <span>Periode Pembukuan: <strong><?= !empty($filterStart) ? date('d/m/Y', strtotime($filterStart)) : 'Awal' ?></strong> s/d <strong><?= !empty($filterEnd) ? date('d/m/Y', strtotime($filterEnd)) : 'Sekarang' ?></strong></span>
            <span class="mx-2">&bull;</span>
            <span>Modul Sumber: <strong><?= !empty($filterModule) ? esc($filterModule) : 'Semua Modul Sumber' ?></strong></span>
            <span class="mx-2">&bull;</span>
            <span>Total Transaksi: <strong><?= count($journals) ?> Entri Jurnal</strong></span>
        </div>
    </div>

    <!-- JOURNAL TABLE -->
    <table class="table-jurnal">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th style="width: 85px;" class="text-center">Tanggal &amp; Waktu</th>
                <th style="width: 120px;">No. Jurnal</th>
                <th style="width: 110px;">Modul Sumber</th>
                <th>Keterangan / Uraian Transaksi</th>
                <th style="width: 70px;" class="text-center">Kode</th>
                <th style="width: 170px;">Rekening Akun (COA)</th>
                <th style="width: 100px;" class="text-right">Debet (Rp)</th>
                <th style="width: 100px;" class="text-right">Kredit (Rp)</th>
                <th style="width: 95px;" class="text-right">Sisa Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($journals)): ?>
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <em>Tidak ada data jurnal umum yang ditemukan pada periode dan filter yang dipilih.</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $no = 1;
                $grandDebit = 0;
                $grandCredit = 0;
                foreach ($journals as $j): 
                    $lines = $detailsByJournal[$j->id] ?? [];
                    $lineCount = count($lines);
                    if ($lineCount === 0) continue;
                ?>
                    <?php foreach ($lines as $idx => $l): 
                        $grandDebit += (float)$l->debit;
                        $grandCredit += (float)$l->credit;
                        $isFirst = ($idx === 0);
                        $rowClass = $isFirst ? 'journal-group-border' : '';
                        $isCredit = (float)$l->credit > 0;
                    ?>
                        <tr class="<?= $rowClass ?>">
                            <?php if ($isFirst): ?>
                                <td rowspan="<?= $lineCount ?>" class="text-center font-weight-bold text-muted" style="vertical-align: top;"><?= $no++ ?></td>
                                <td rowspan="<?= $lineCount ?>" class="text-center font-mono text-nowrap" style="vertical-align: top; font-size: 9.5px;">
                                    <span class="font-weight-bold text-dark"><?= date('d/m/Y', strtotime($j->entry_date)) ?></span>
                                    <?php if (!empty($j->created_at)): ?>
                                        <small class="text-muted d-block text-xxs mt-1" style="font-size: 8px;"><i class="far fa-clock mr-1"></i><?= date('H:i:s', strtotime($j->created_at)) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td rowspan="<?= $lineCount ?>" class="font-mono font-weight-bold text-teal" style="vertical-align: top;"><?= esc($j->journal_no) ?></td>
                                <td rowspan="<?= $lineCount ?>" style="vertical-align: top; font-size: 9.5px;"><?= esc($j->source_module) ?></td>
                                <td rowspan="<?= $lineCount ?>" style="vertical-align: top; color: #334155;"><?= nl2br(esc($j->description)) ?></td>
                            <?php endif; ?>
                            
                            <td class="text-center font-mono font-weight-bold <?= $isCredit ? 'text-muted' : 'text-dark' ?>">
                                <?= esc($l->account_code) ?>
                            </td>
                            <td style="<?= $isCredit ? 'padding-left: 18px; color: #475569;' : 'font-weight: 600; color: #0f172a;' ?>">
                                <?= $isCredit ? '&bull; ' : '' ?><?= esc($l->account_name) ?>
                            </td>
                            <td class="text-right font-mono <?= (float)$l->debit > 0 ? 'text-success font-weight-bold' : 'text-muted' ?>">
                                <?= (float)$l->debit > 0 ? number_format($l->debit, 2, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right font-mono <?= (float)$l->credit > 0 ? 'text-danger font-weight-bold' : 'text-muted' ?>">
                                <?= (float)$l->credit > 0 ? number_format($l->credit, 2, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right font-mono text-muted" style="font-size: 9px;">
                                <?= number_format($l->account_balance ?? 0, 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f0fdfa; border-top: 2px solid #0f766e; border-bottom: 2px solid #0f766e;">
                <td colspan="7" class="text-right font-weight-bold text-dark py-2" style="font-size: 11px;">
                    TOTAL MUTASI JURNAL UMUM (DEBET / KREDIT):
                </td>
                <td class="text-right font-mono font-weight-bold text-success py-2" style="font-size: 11px;">
                    Rp <?= number_format($grandDebit ?? 0, 2, ',', '.') ?>
                </td>
                <td class="text-right font-mono font-weight-bold text-danger py-2" style="font-size: 11px;">
                    Rp <?= number_format($grandCredit ?? 0, 2, ',', '.') ?>
                </td>
                <td class="text-center py-2">
                    <?php 
                        $diff = abs(($grandDebit ?? 0) - ($grandCredit ?? 0));
                        if ($diff < 0.01): 
                    ?>
                        <span class="badge-balance"><i class="fas fa-check-circle mr-1"></i>BALANCE</span>
                    <?php else: ?>
                        <span class="badge badge-danger font-weight-bold">SELISIH Rp <?= number_format($diff, 2, ',', '.') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- LEMBAR PENGESAHAN & TANDA TANGAN (STANDAR KEUANGAN RESMI) -->
    <div class="signature-box">
        <div class="row text-center mt-4">
            <div class="col-4">
                <small class="text-muted d-block mb-1">Disiapkan Oleh,</small>
                <div class="font-weight-bold text-dark text-xs">Staf Akuntansi &amp; Pembukuan</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark text-xs" style="text-decoration: underline;">( <?= esc(session()->get('nama_lengkap') ?? 'Staf Akuntansi') ?> )</div>
                <small class="text-muted d-block" style="font-size: 9px;">Tanggal: <?= date('d/m/Y') ?></small>
            </div>
            <div class="col-4">
                <small class="text-muted d-block mb-1">Diperiksa &amp; Diverifikasi Oleh,</small>
                <div class="font-weight-bold text-dark text-xs">Supervisor Keuangan / Akuntan</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark text-xs" style="text-decoration: underline;">( Kepala Keuangan &amp; Akuntansi )</div>
                <small class="text-muted d-block" style="font-size: 9px;">Sawamawa Medical Center</small>
            </div>
            <div class="col-4">
                <small class="text-muted d-block mb-1">Disetujui Oleh,</small>
                <div class="font-weight-bold text-dark text-xs">Direktur Klinik / Pimpinan</div>
                <div style="height: 55px;"></div>
                <div class="font-weight-bold text-dark text-xs" style="text-decoration: underline;">( Direktur Utama )</div>
                <small class="text-muted d-block" style="font-size: 9px;">SIP/NIP: -</small>
            </div>
        </div>
    </div>

</div>

</body>
</html>
