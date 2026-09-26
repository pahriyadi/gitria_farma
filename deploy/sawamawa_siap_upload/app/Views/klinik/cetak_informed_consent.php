<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Informed Consent - Persetujuan Tindakan Medis') ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            color: #111111;
            background: #ffffff;
            line-height: 1.45;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header h4 {
            margin: 2px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #444444;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .doc-title .doc-subtitle {
            font-size: 10.5px;
            font-weight: bold;
            color: #555555;
            margin-top: 2px;
        }
        .consent-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .badge-agree {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .badge-refuse {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .table-info, .table-edu {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-info td {
            padding: 2.5px 4px;
            font-size: 11px;
            vertical-align: top;
        }
        .table-info td.label {
            width: 140px;
            font-weight: bold;
            color: #333333;
        }
        .table-edu th, .table-edu td {
            border: 1px solid #888888;
            padding: 4px 6px;
            font-size: 10.5px;
            vertical-align: top;
        }
        .table-edu th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            background-color: #e9ecef;
            padding: 4px 6px;
            border: 1px solid #cccccc;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .statement-box {
            border: 1px solid #aaaaaa;
            padding: 8px 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            background-color: #fcfcfc;
            border-radius: 4px;
            font-size: 10.5px;
            line-height: 1.4;
        }
        .signatures {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signatures td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 5px;
            font-size: 10.5px;
        }
        .sig-box {
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 5px auto;
        }
        .sig-img {
            max-height: 60px;
            max-width: 130px;
            object-fit: contain;
        }
        .sig-line {
            font-weight: bold;
            text-decoration: underline;
        }
        .no-print {
            text-align: right;
            margin-bottom: 12px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 7px 16px; background-color: #0d8a72; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px;">
            🖨️ Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 7px 14px; background-color: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; margin-left: 5px;">
            Tutup
        </button>
    </div>

    <!-- KOP RESMI -->
    <div class="header">
        <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
        <h4><?= esc(clinic_setting('clinic_tagline', 'Pusat Layanan Medis Terpadu & Farmasi Terpercaya')) ?></h4>
        <p><?= esc(clinic_setting('clinic_address')) ?> | Telp/WA: <?= esc(clinic_setting('clinic_phone')) ?> | Email: <?= esc(clinic_setting('clinic_email')) ?></p>
        <p style="font-size: 9px; margin-top: 2px;">Izin Operasional: <?= esc(clinic_setting('clinic_license_number', '445/012/DINKES/2024')) ?></p>
    </div>

    <div class="doc-title">
        <h3>SURAT <?= $consent->consent_type === 'refuse' ? 'PENOLAKAN' : 'PERSETUJUAN' ?> TINDAKAN KEDOKTERAN</h3>
        <div class="doc-subtitle">LEMBAR INFORMED CONSENT & EDUKASI PASIEN</div>
        <div class="consent-badge <?= $consent->consent_type === 'refuse' ? 'badge-refuse' : 'badge-agree' ?>">
            Status: <?= $consent->consent_type === 'refuse' ? 'PENOLAKAN TINDAKAN MEDIS' : 'PERSETUJUAN TINDAKAN MEDIS' ?>
        </div>
    </div>

    <!-- 1. PEMBERIAN INFORMASI TINDAKAN -->
    <div class="section-title">I. PEMBERIAN INFORMASI & EDUKASI TINDAKAN MEDIS</div>
    <table class="table-edu">
        <thead>
            <tr>
                <th style="width: 25px; text-align:center;">No</th>
                <th style="width: 170px;">Jenis Informasi</th>
                <th>Isi Edukasi & Penjelasan Dokter</th>
                <th style="width: 65px; text-align:center;">Paraf Pasien</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center;">1</td>
                <td><strong>Diagnosis (WD & DD)</strong></td>
                <td><?= esc($consent->diagnosis ?: '-') ?></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
            <tr>
                <td style="text-align:center;">2</td>
                <td><strong>Tindakan Kedokteran</strong></td>
                <td><strong style="color: #0b6623;"><?= esc($consent->procedure_name) ?></strong></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
            <tr>
                <td style="text-align:center;">3</td>
                <td><strong>Indikasi Tindakan</strong></td>
                <td><?= nl2br(esc($consent->indication ?: '-')) ?></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
            <tr>
                <td style="text-align:center;">4</td>
                <td><strong>Tata Cara / Prosedur</strong></td>
                <td><?= nl2br(esc($consent->procedure_desc ?: '-')) ?></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
            <tr>
                <td style="text-align:center;">5</td>
                <td><strong>Risiko & Komplikasi</strong></td>
                <td><?= nl2br(esc($consent->risks_complications ?: '-')) ?></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
            <tr>
                <td style="text-align:center;">6</td>
                <td><strong>Prognosis</strong></td>
                <td><?= esc($consent->prognosis ?: 'Dubia ad Bonam (Baik)') ?></td>
                <td style="text-align:center;">✔ Terinfo</td>
            </tr>
        </tbody>
    </table>

    <!-- 2. IDENTITAS PENANGGUNG JAWAB & PASIEN -->
    <div class="section-title">II. PERNYATAAN <?= $consent->consent_type === 'refuse' ? 'PENOLAKAN' : 'PERSETUJUAN' ?> TINDAKAN MEDIS</div>
    <div class="statement-box">
        Yang bertanda tangan di bawah ini:<br>
        <table class="table-info" style="margin: 5px 0;">
            <tr>
                <td class="label">Nama Penanggung Jawab</td>
                <td>: <strong><?= esc($consent->authorized_person_name) ?></strong></td>
                <td class="label">Hubungan dgn Pasien</td>
                <td>: <?= esc($consent->authorized_person_relation ?: 'Diri Sendiri') ?></td>
            </tr>
            <tr>
                <td class="label">Nama Pasien</td>
                <td>: <strong><?= esc($consent->patient_name) ?></strong></td>
                <td class="label">Nomor Rekam Medis</td>
                <td>: <strong><?= esc($consent->no_rm) ?></strong></td>
            </tr>
            <tr>
                <td class="label">Tanggal Lahir / Usia</td>
                <td>: <?= date('d/m/Y', strtotime($consent->date_of_birth)) ?> (<?= $age ?>)</td>
                <td class="label">Jenis Kelamin</td>
                <td>: <?= $consent->gender === 'L' ? 'Laki-Laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td class="label">Alamat / No. HP</td>
                <td colspan="3">: <?= esc($consent->address) ?> (<?= esc($consent->phone) ?>)</td>
            </tr>
        </table>

        <?php if ($consent->consent_type === 'refuse'): ?>
            Menyatakan dengan sesungguhnya bahwa saya telah menerima penjelasan lengkap dan <strong>MENOLAK</strong> untuk dilakukannya tindakan kedokteran berupa <u><?= esc($consent->procedure_name) ?></u> terhadap pasien di atas. Saya memahami segala konsekuensi medis atas keputusan penolakan ini tanpa menuntut pihak klinik.
        <?php else: ?>
            Menyatakan dengan sesungguhnya bahwa saya telah menerima penjelasan secara jelas dan rinci dari dokter mengenai tujuan, prosedur, risiko, dan kemungkinan komplikasi, serta dengan sadar memberikan <strong>PERSETUJUAN</strong> untuk dilakukannya tindakan kedokteran berupa <u><?= esc($consent->procedure_name) ?></u> terhadap pasien di atas.
        <?php endif; ?>
    </div>

    <!-- TANDA TANGAN -->
    <table class="signatures">
        <tr>
            <td>
                Sumbawa Besar, <?= date('d/m/Y H:i', strtotime($consent->consent_date)) ?><br>
                Yang Menyatakan (Pasien/Wali),
                <div class="sig-box">
                    <?php if (!empty($consent->authorized_person_signature)): ?>
                        <img src="<?= $consent->authorized_person_signature ?>" class="sig-img" alt="TTD Pasien/Wali">
                    <?php else: ?>
                        <div style="color: #999; font-style: italic; font-size: 9.5px;">(Tanda Tangan Digital)</div>
                    <?php endif; ?>
                </div>
                <div class="sig-line">( <?= esc($consent->authorized_person_name) ?> )</div>
            </td>
            <td>
                <br>
                Saksi Petugas Medis,
                <div class="sig-box">
                    <div style="color: #999; font-style: italic; font-size: 9.5px;">(Saksi Terdaftar)</div>
                </div>
                <div class="sig-line">( <?= esc($consent->witness_name ?: 'Perawat Klinik') ?> )</div>
            </td>
            <td>
                <br>
                Dokter Penanggung Jawab Pelayanan,
                <div class="sig-box">
                    <?php if (!empty($consent->doctor_signature)): ?>
                        <img src="<?= $consent->doctor_signature ?>" class="sig-img" alt="TTD Dokter">
                    <?php else: ?>
                        <div style="color: #999; font-style: italic; font-size: 9.5px;">(Tanda Tangan Dokter)</div>
                    <?php endif; ?>
                </div>
                <div class="sig-line">( <?= esc($consent->doctor_name ?: clinic_setting('clinic_director', 'dr. Pelaksana')) ?> )</div>
                <div style="font-size: 9px; color: #555;">SIP: <?= esc($consent->sip_number ?: '446/SIP.D/2024') ?></div>
            </td>
        </tr>
    </table>

</body>
</html>
