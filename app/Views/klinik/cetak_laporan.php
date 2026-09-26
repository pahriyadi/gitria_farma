<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Laporan Pelayanan Klinik') ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000000;
            background: #ffffff;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 11px;
        }
        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title h4 {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .report-table th, .report-table td {
            border: 1px solid #333333;
            padding: 6px 8px;
        }
        .report-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .sig-box {
            width: 250px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
        }
        .no-print {
            background: #f4f6f9;
            padding: 10px 15px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 6px 14px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            border: 1px solid #333;
            background: #fff;
        }
        .btn-print {
            background: #00875a;
            color: #fff;
            border: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <div><strong>Pratinjau Laporan Pelayanan Klinik</strong> (Sawamawa Medical Center)</div>
    <div>
        <button onclick="window.print();" class="btn btn-print">Cetak Laporan Resmi</button>
        <button onclick="window.close();" class="btn">Tutup</button>
    </div>
</div>

<!-- Header / Kop -->
<div class="header">
    <h2>SAWAMAWA MEDICAL CENTER</h2>
    <h3>KLINIK PRATAMA RAWAT JALAN & PELAYANAN MEDIS TERPADU</h3>
    <p>Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 23456 | CS: 0812-3456-7890</p>
</div>

<div class="report-title">
    <?php if ($type === 'morbiditas'): ?>
        <h4>LAPORAN 10 BESAR PENYAKIT TERBANYAK (MORBIDITAS ICD-10)</h4>
    <?php elseif ($type === 'waktu_pelayanan'): ?>
        <h4>LAPORAN EVALUASI WAKTU TUNGGU & DURASI PELAYANAN PASIEN (SLA)</h4>
    <?php else: ?>
        <h4>LAPORAN REKAPITULASI KUNJUNGAN PASIEN POLIKLINIK</h4>
    <?php endif; ?>
    <div style="font-size: 12px; margin-top: 3px;">
        Periode: <strong><?= date('d F Y', strtotime($startDate)) ?></strong> s/d <strong><?= date('d F Y', strtotime($endDate)) ?></strong>
    </div>
</div>

<?php if ($type === 'morbiditas'): ?>
    <!-- 10 Morbidity Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 50px;">Ranking</th>
                <th style="width: 120px;">Kode ICD-10</th>
                <th>Nama Diagnosa Penyakit (Bahasa Indonesia & Inggris)</th>
                <th style="width: 140px;">Jumlah Kasus</th>
                <th style="width: 120px;">Persentase (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($topIcd)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data diagnosa penyakit pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php 
                    $rank = 1;
                    $totalCasesAll = array_sum(array_map(function($i) { return $i->total_cases; }, $topIcd));
                ?>
                <?php foreach ($topIcd as $icd): ?>
                    <?php $pct = $totalCasesAll > 0 ? round(($icd->total_cases / $totalCasesAll) * 100, 1) : 0; ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;">#<?= $rank++ ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= esc($icd->icd10_code) ?></td>
                        <td>
                            <strong><?= esc($icd->name_id ?: $icd->name_en ?: 'Diagnosa Medis') ?></strong>
                            <?php if (!empty($icd->name_en) && $icd->name_en !== $icd->name_id): ?>
                                <br><small style="color: #666;"><em><?= esc($icd->name_en) ?></em></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center; font-weight: bold;"><?= number_format($icd->total_cases) ?> Kasus</td>
                        <td style="text-align: center;"><?= $pct ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($type === 'waktu_pelayanan'): ?>
    <!-- SLA Summary KPI Box for Print -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; text-align: center; gap: 8px;">
        <div style="flex: 1; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background: #f9fafb;">
            <div style="font-size: 10px; font-weight: bold; color: #555;">RATA2 TUNGGU TTV</div>
            <div style="font-size: 16px; font-weight: bold; color: #0284c7;"><?= $avgMetrics['avg_ttv'] ?? 0 ?> mnt</div>
        </div>
        <div style="flex: 1; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background: #f9fafb;">
            <div style="font-size: 10px; font-weight: bold; color: #555;">RATA2 PERIKSA DOKTER</div>
            <div style="font-size: 16px; font-weight: bold; color: #10b981;"><?= $avgMetrics['avg_doc'] ?? 0 ?> mnt</div>
        </div>
        <div style="flex: 1; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background: #f9fafb;">
            <div style="font-size: 10px; font-weight: bold; color: #555;">RATA2 KASIR UTAMA</div>
            <div style="font-size: 16px; font-weight: bold; color: #f59e0b;"><?= $avgMetrics['avg_cashier'] ?? 0 ?> mnt</div>
        </div>
        <div style="flex: 1; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background: #f9fafb;">
            <div style="font-size: 10px; font-weight: bold; color: #555;">RATA2 SERAH OBAT</div>
            <div style="font-size: 16px; font-weight: bold; color: #0d9488;"><?= $avgMetrics['avg_pharmacy'] ?? 0 ?> mnt</div>
        </div>
        <div style="flex: 1; border: 1.5px solid #0d9488; padding: 8px; border-radius: 4px; background: #f0fdfa;">
            <div style="font-size: 10px; font-weight: bold; color: #0f766e;">TOTAL RATA2 LAYANAN</div>
            <div style="font-size: 16px; font-weight: bold; color: #0f766e;"><?= $avgMetrics['avg_overall'] ?? 0 ?> mnt</div>
        </div>
    </div>

    <!-- SLA Details Table -->
    <table class="report-table" style="font-size: 11px;">
        <thead>
            <tr style="text-align: center;">
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="width: 70px;">No. Antrean</th>
                <th rowspan="2">Pasien & Dokter</th>
                <th colspan="5" style="background: #e5e7eb;">Catatan Jam / Timestamp</th>
                <th colspan="4" style="background: #d1d5db;">Durasi per Pos (Menit)</th>
                <th rowspan="2" style="width: 70px; background: #ccfbf1;">Total Waktu</th>
            </tr>
            <tr style="text-align: center; font-size: 10px;">
                <th>Daftar</th>
                <th>TTV</th>
                <th>Dokter</th>
                <th>Kasir</th>
                <th>Apotek</th>
                <th>Tunggu</th>
                <th>Dokter</th>
                <th>Kasir</th>
                <th>Apotek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($slaVisits)): ?>
                <tr><td colspan="13" style="text-align:center; padding: 20px;">Tidak ada data pelayanan pasien pada periode ini.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($slaVisits as $sv): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= esc($sv->queue_no) ?></td>
                        <td>
                            <strong><?= esc($sv->patient_name) ?></strong> (RM: <?= esc($sv->no_rm) ?>)
                            <br><small style="color: #666;"><?= esc($sv->service_name) ?> &bull; <?= esc($sv->doctor_name) ?></small>
                        </td>
                        <td style="text-align: center; font-weight: bold;"><?= $sv->time_t0 ?></td>
                        <td style="text-align: center;"><?= $sv->time_t1 ?></td>
                        <td style="text-align: center;"><?= $sv->time_t2 ?></td>
                        <td style="text-align: center;"><?= $sv->time_t3 ?></td>
                        <td style="text-align: center;"><?= $sv->time_t4 ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= $sv->dur_ttv !== null ? ($sv->dur_ttv . ' mnt') : '-' ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= $sv->dur_doc !== null ? ($sv->dur_doc . ' mnt') : '-' ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= $sv->dur_cashier !== null ? ($sv->dur_cashier . ' mnt') : '-' ?></td>
                        <td style="text-align: center; font-weight: bold;"><?= $sv->dur_pharmacy !== null ? ($sv->dur_pharmacy . ' mnt') : '-' ?></td>
                        <td style="text-align: center; font-weight: bold; background: #f0fdfa;">
                            <?= $sv->total_minutes !== null ? ($sv->total_minutes . ' mnt') : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php else: ?>
    <!-- Visits Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 110px;">No. Kunjungan</th>
                <th style="width: 90px;">No. RM</th>
                <th>Nama Pasien</th>
                <th style="width: 60px;">JK</th>
                <th style="width: 90px;">Tgl Kunjungan</th>
                <th>Poliklinik / Layanan</th>
                <th>Dokter Pemeriksa</th>
                <th style="width: 90px;">Diagnosa ICD</th>
                <th style="width: 80px;">Cara Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($visits as $v): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td style="text-align: center; font-weight: bold;"><?= esc($v->no_visit) ?></td>
                    <td style="text-align: center;"><?= esc($v->no_rm) ?></td>
                    <td><strong><?= esc($v->patient_name) ?></strong></td>
                    <td style="text-align: center;"><?= $v->gender ?></td>
                    <td style="text-align: center;"><?= date('d/m/Y', strtotime($v->visit_date)) ?></td>
                    <td><?= esc($v->polyclinic_name) ?></td>
                    <td><?= esc($v->doctor_name ?? 'Dokter Jaga') ?></td>
                    <td style="text-align: center; font-weight: bold;"><?= esc($v->icd10_code ?: '-') ?></td>
                    <td style="text-align: center;"><?= strtoupper(esc($v->payment_method)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Signatures -->
<div class="signature-section">
    <div class="sig-box">
        <div>Mengetahui,</div>
        <div>Kepala Pelayanan Medis</div>
        <div class="sig-space"></div>
        <div><strong><u>dr. Dokter Penanggung Jawab</u></strong></div>
        <div style="font-size: 11px;">SIP: 503/SIP-D/2026</div>
    </div>
    <div class="sig-box">
        <div>Sumbawa Besar, <?= date('d F Y') ?></div>
        <div>Direktur Sawamawa Medical Center</div>
        <div class="sig-space"></div>
        <div><strong><u>dr. Direktur Utama</u></strong></div>
        <div style="font-size: 11px;">Pimpinan Fasilitas Pelayanan Kesehatan</div>
    </div>
</div>

</body>
</html>
