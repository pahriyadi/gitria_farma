<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Hasil Pemeriksaan Laboratorium') ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
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
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .meta-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .meta-col {
            width: 48%;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .lab-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .lab-table th, .lab-table td {
            border: 1px solid #333333;
            padding: 8px 10px;
        }
        .lab-table th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .sig-box {
            width: 220px;
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
    <div><strong>Pratinjau Lembar Hasil Laboratorium</strong> (Sawamawa Medical Center)</div>
    <div>
        <button onclick="window.print();" class="btn btn-print">Cetak Hasil Lab</button>
        <button onclick="window.close();" class="btn">Tutup</button>
    </div>
</div>

<!-- Header / Kop -->
<div class="header">
    <h2>SAWAMAWA MEDICAL CENTER</h2>
    <h3>INSTALASI LABORATORIUM KLINIK & DIAGNOSTIK</h3>
    <p>Jl. Kebangsaan No. 12, Sumbawa Besar, NTB | Telp: (0371) 23456 | CS: 0812-3456-7890</p>
</div>

<div class="doc-title">
    <h4>LEMBAR HASIL PEMERIKSAAN LABORATORIUM</h4>
    <div style="font-size: 12px; margin-top: 3px;">No. Lab: <strong><?= esc($lab->lab_no) ?></strong></div>
</div>

<!-- Metadata Pasien -->
<div class="meta-grid">
    <div class="meta-col">
        <table class="meta-table">
            <tr>
                <td style="width: 120px;">No. Rekam Medis</td>
                <td style="width: 10px;">:</td>
                <td><strong><?= esc($lab->no_rm) ?></strong></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><strong><?= esc($lab->patient_name) ?></strong> (<?= $lab->gender === 'L' ? 'L' : 'P' ?>)</td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>
                    <?= date('d/m/Y', strtotime($lab->date_of_birth)) ?> (<?php 
                        $dob = new DateTime($lab->date_of_birth);
                        $now = new DateTime();
                        echo $now->diff($dob)->y . ' Thn';
                    ?>)
                </td>
            </tr>
        </table>
    </div>
    <div class="meta-col">
        <table class="meta-table">
            <tr>
                <td style="width: 120px;">Tgl. Pemeriksaan</td>
                <td style="width: 10px;">:</td>
                <td><?= date('d F Y', strtotime($lab->test_date)) ?></td>
            </tr>
            <tr>
                <td>Dokter Pengirim</td>
                <td>:</td>
                <td><?= esc($lab->doctor_name ?? 'Dokter Pemeriksa') ?></td>
            </tr>
            <tr>
                <td>Petugas / Analis</td>
                <td>:</td>
                <td><?= esc($lab->officer_name) ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Lab Result Table -->
<table class="lab-table">
    <thead>
        <tr>
            <th style="width: 40px; text-align: center;">No</th>
            <th>Pemeriksaan / Parameter</th>
            <th style="width: 130px; text-align: center;">Hasil</th>
            <th style="width: 90px; text-align: center;">Satuan</th>
            <th style="width: 150px; text-align: center;">Nilai Rujukan</th>
            <th style="width: 100px; text-align: center;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center; font-weight: bold;">1</td>
            <td><strong><?= esc($lab->test_type) ?></strong></td>
            <td style="text-align: center; font-weight: bold; font-size: 14px;">
                <?= esc($lab->result_value) ?>
            </td>
            <td style="text-align: center;"><?= esc($lab->unit ?: '-') ?></td>
            <td style="text-align: center; font-size: 12px;"><?= esc($lab->normal_range ?: '-') ?></td>
            <td style="text-align: center; font-weight: bold;">
                <?php if ($lab->status === 'normal'): ?>
                    <span style="color: #00875a;">NORMAL</span>
                <?php elseif ($lab->status === 'high'): ?>
                    <span style="color: #de350b;">HIGH (*)</span>
                <?php elseif ($lab->status === 'low'): ?>
                    <span style="color: #ff9900;">LOW (*)</span>
                <?php else: ?>
                    <span style="color: #de350b;">ABNORMAL</span>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>

<?php if (!empty($lab->notes)): ?>
    <div style="border: 1px dashed #666; padding: 8px 12px; font-size: 12px; margin-bottom: 20px;">
        <strong>Catatan Laboratorium:</strong> <?= esc($lab->notes) ?>
    </div>
<?php endif; ?>

<!-- Signature -->
<div class="signature-section">
    <div class="sig-box">
        <div>Mengetahui,</div>
        <div>Dokter Penanggung Jawab</div>
        <div class="sig-space"></div>
        <div><strong><u><?= esc($lab->doctor_name ?? 'dr. Dokter Pemeriksa') ?></u></strong></div>
        <div style="font-size: 11px;">SIP: <?= esc($lab->sip_number ?: '503/SIP-D/2026') ?></div>
    </div>
    <div class="sig-box">
        <div>Sumbawa Besar, <?= date('d F Y', strtotime($lab->test_date)) ?></div>
        <div>Analis Laboratorium,</div>
        <div class="sig-space"></div>
        <div><strong><u><?= esc($lab->officer_name) ?></u></strong></div>
        <div style="font-size: 11px;">Petugas Laboratorium</div>
    </div>
</div>

</body>
</html>
