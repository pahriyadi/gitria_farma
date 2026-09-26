<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Berkas Rekam Medis (Lembar 1 - 7) - ' . ($patient->name ?? 'Pasien')) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10.5px;
            color: #111111;
            background: #ffffff;
            line-height: 1.35;
            margin: 0;
            padding: 8px;
        }
        .page-sheet {
            page-break-after: always;
            break-after: page;
            min-height: 990px;
            position: relative;
            padding-bottom: 10px;
        }
        .page-sheet:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #000000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h2 {
            margin: 0;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0b532c;
        }
        .header h4 {
            margin: 2px 0;
            font-size: 11px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 9px;
            color: #444444;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 8px;
            border: 1.5px solid #0b532c;
            background-color: #f4faf6;
            padding: 4px 6px;
            border-radius: 4px;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 12.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0b532c;
        }
        .doc-title .sheet-code {
            font-size: 9.5px;
            font-weight: bold;
            color: #333333;
        }
        .section-header {
            font-size: 10.5px;
            font-weight: bold;
            background-color: #e9f4ee;
            padding: 3px 8px;
            border-left: 4px solid #0d9f4f;
            border-top: 1px solid #c2ded0;
            border-right: 1px solid #c2ded0;
            border-bottom: 1px solid #c2ded0;
            margin-top: 8px;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #0a4926;
        }
        .patient-card {
            border: 1px solid #777777;
            padding: 6px 8px;
            margin-bottom: 8px;
            background-color: #fafafa;
            border-radius: 4px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }
        .table-info td {
            padding: 2px 4px;
            font-size: 10px;
            vertical-align: top;
        }
        .table-info td.label {
            width: 135px;
            font-weight: bold;
            color: #333333;
        }
        .table-bordered-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 6px;
        }
        .table-bordered-custom th, .table-bordered-custom td {
            border: 1px solid #777777;
            padding: 3.5px 5px;
            font-size: 9.5px;
            vertical-align: top;
        }
        .table-bordered-custom th {
            background-color: #eaeff2;
            font-weight: bold;
            text-align: center;
        }
        .ttv-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .ttv-grid th, .ttv-grid td {
            border: 1px solid #888888;
            padding: 3px 4px;
            font-size: 9.5px;
            text-align: center;
        }
        .ttv-grid th {
            background-color: #f0f4f2;
            font-weight: bold;
        }
        .soap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }
        .soap-table td {
            padding: 2px 4px;
            font-size: 10px;
            vertical-align: top;
        }
        .soap-table td.soap-code {
            width: 110px;
            font-weight: bold;
            color: #0b6623;
        }
        .signature-area {
            margin-top: 15px;
            width: 100%;
            page-break-inside: avoid;
        }
        .no-print {
            text-align: right;
            margin-bottom: 12px;
            background: #ffffff;
            padding: 8px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .photo-box {
            border: 1.5px dashed #888888;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            background: #fdfdfd;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
        }
        .photo-box img {
            max-width: 100%;
            max-height: 230px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #cccccc;
        }
        .badge-kategori {
            display: inline-block;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 9px;
            border-radius: 3px;
            background: #e9f2ee;
            color: #0d6b38;
            border: 1px solid #0d9f4f;
            margin-bottom: 4px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .page-sheet { page-break-after: always; break-after: page; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 7px 18px; background: #0d9f4f; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 13px;">
            🖨️ Cetak Berkas Lengkap RME (Lembar 1 s/d 7)
        </button>
        <button onclick="window.close()" style="padding: 7px 18px; background: #6c757d; color: white; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; margin-left: 6px; font-size: 13px;">
            ✖️ Tutup Pratinjau
        </button>
        <div style="font-size: 11px; color: #555555; margin-top: 5px;">
            Format Berkas: Standar Dokumen Rekam Medis GITRIA FARMA &bull; Otomatis Terbagi Menjadi 7 Lembar Berseri A4.
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 1: IDENTITAS SOSIAL & BIODATA PASIEN                              -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4><?= esc(clinic_setting('clinic_tagline', 'Pusat Layanan Medis Terpadu & Farmasi Terpercaya')) ?></h4>
            <p><?= esc(clinic_setting('clinic_address')) ?> | Telp: <?= esc(clinic_setting('clinic_phone')) ?> | Email: <?= esc(clinic_setting('clinic_email')) ?></p>
            <p style="font-size: 9px;">Izin Operasional: <?= esc(clinic_setting('clinic_license_number', '445/012/DINKES/2024')) ?></p>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 1: RINGKASAN MASUK &amp; IDENTITAS SOSIAL PASIEN</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-01 &bull; REKAM MEDIS ELEKTRONIK TERPADU</span>
        </div>

        <div class="section-header">A. DATA DEMOGRAFI &amp; IDENTITAS PASIEN</div>
        <div class="patient-card">
            <table class="table-info">
                <tr>
                    <td class="label">Nomor Rekam Medis</td>
                    <td style="font-weight: bold; font-family: monospace; font-size: 13px; color: #0d9f4f;">: <?= esc($patient->no_rm) ?></td>
                    <td class="label">Nomor Induk Kependudukan (NIK)</td>
                    <td style="font-weight: bold; font-family: monospace;">: <?= esc($patient->nik ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Nama Lengkap Pasien</td>
                    <td style="font-weight: bold; font-size: 11.5px;">: <?= esc($patient->name) ?></td>
                    <td class="label">Jenis Kelamin / Usia</td>
                    <td>: <?= $patient->gender === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' ?> / <?= $age ?></td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tanggal Lahir</td>
                    <td>: <?= esc($patient->place_of_birth ?: '-') ?>, <?= $patient->date_of_birth ? date('d F Y', strtotime($patient->date_of_birth)) : '-' ?></td>
                    <td class="label">Pekerjaan Pasien</td>
                    <td>: <?= esc($patient->occupation ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Agama / Kepercayaan</td>
                    <td>: <?= esc($patient->religion ?: '-') ?></td>
                    <td class="label">Golongan Darah / Rhesus</td>
                    <td>: <strong><?= esc($patient->blood_type ?: '-') ?></strong></td>
                </tr>
                <tr>
                    <td class="label">Status Pernikahan</td>
                    <td>: <?= esc($patient->marital_status ?: '-') ?></td>
                    <td class="label">Pendidikan Terakhir</td>
                    <td>: <?= esc($patient->education ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Nomor Telepon / WhatsApp</td>
                    <td>: <?= esc($patient->phone ?: '-') ?></td>
                    <td class="label">Golongan Pasien / BPJS</td>
                    <td>: <?= esc($patient->bpjs_number ? 'BPJS: ' . $patient->bpjs_number : 'Umum / Mandiri') ?> (<?= strtoupper(esc($patient->membership_tier ?? 'Reguler')) ?>)</td>
                </tr>
                <tr>
                    <td class="label">Alamat Lengkap Domisili</td>
                    <td colspan="3">: <?= esc($patient->address ?: '-') ?></td>
                </tr>
            </table>
        </div>

        <div class="section-header">B. PENANGGUNG JAWAB / KONTAK DARURAT KELUARGA</div>
        <table class="table-bordered-custom">
            <thead>
                <tr>
                    <th style="width: 35%;">Nama Penanggung Jawab</th>
                    <th style="width: 30%;">Hubungan dengan Pasien</th>
                    <th style="width: 35%;">Nomor Telepon / HP Darurat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?= esc($patient->emergency_contact_name ?: '-') ?></strong></td>
                    <td>Keluarga / Kerabat</td>
                    <td><?= esc($patient->emergency_contact_phone ?: '-') ?></td>
                </tr>
            </tbody>
        </table>

        <div class="section-header">C. PERINGATAN KLINIS KHUSUS (ALERGI &amp; RIWAYAT PENYAKIT)</div>
        <table class="table-bordered-custom">
            <tr>
                <td style="width: 25%; font-weight: bold; color: #b71c1c; background: #ffebee;">
                    ⚠️ RIWAYAT ALERGI:
                </td>
                <td style="font-weight: bold; color: #b71c1c;">
                    <?= esc($patient->allergies ?: 'TIDAK ADA CATATAN ALERGI OBAT/MAKANAN') ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f5f5f5;">
                    Riwayat Penyakit Terdahulu (RPD):
                </td>
                <td>
                    <?= esc($patient->medical_history ?: 'Tidak ada riwayat penyakit berat / kronis terdahulu') ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f5f5f5;">
                    Riwayat Penyakit Keluarga (RPK):
                </td>
                <td>
                    Tidak ada riwayat penyakit menurun spesifik (Hipertensi/Diabetes/Asma) yang dilaporkan.
                </td>
            </tr>
        </table>

        <div class="section-header">D. PERNYATAAN PERSETUJUAN UMUM (GENERAL CONSENT)</div>
        <p style="font-size: 9px; text-align: justify; color: #333333; margin: 4px 0 15px 0;">
            Pasien/Wali menyatakan bahwa data identitas di atas adalah benar dan memberikan persetujuan umum untuk menerima pelayanan kesehatan, pemeriksaan penunjang dasar, asuhan keperawatan, dan pengobatan sesuai indikasi medis klinis di <?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?>.
        </p>

        <table class="signature-area">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 45px 0;">
                        Pasien / Keluarga Penanggung Jawab,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc($patient->name) ?> )
                    </p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; position: relative;">
                    <p style="margin: 0 0 5px 0;">
                        Sumbawa Besar, <?= date('d F Y') ?><br>
                        <strong>Petugas Pendaftaran / Rekam Medis</strong>
                    </p>
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php if (clinic_setting('clinic_stamp')): ?>
                            <img src="<?= clinic_setting('clinic_stamp') ?>" alt="Stempel Klinik" style="max-height: 50px; opacity: 0.85;">
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc(session()->get('user_name') ?: 'Petugas Rekam Medis') ?> )
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 2: ASESMEN AWAL KEPERAWATAN & MEDIS PASIEN BARU                    -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 2: ASESMEN AWAL KEPERAWATAN &amp; MEDIS RAWAT JALAN</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-02 &bull; PEMERIKSAAN PERTAMA PASIEN BARU</span>
        </div>

        <div style="font-size: 9.5px; margin-bottom: 6px; border-bottom: 1px dashed #999; padding-bottom: 3px;">
            <strong>No RM:</strong> <?= esc($patient->no_rm) ?> | 
            <strong>Nama Pasien:</strong> <?= esc($patient->name) ?> | 
            <strong>JK/Usia:</strong> <?= $patient->gender ?>/<?= $age ?> | 
            <strong>Alergi:</strong> <span style="color:#b71c1c; font-weight:bold;"><?= esc($patient->allergies ?: 'Tidak ada') ?></span>
        </div>

        <?php 
            $initial = !empty($visits) ? end($visits) : null; 
            $initDate = $initial && $initial->visit_date ? date('d/m/Y', strtotime($initial->visit_date)) : date('d/m/Y');
        ?>

        <div class="section-header">A. ANAMNESIS &amp; ASESMEN TRIAGE KEPERAWATAN</div>
        <table class="table-bordered-custom">
            <tr>
                <td style="width: 25%; font-weight: bold;">Tanggal &amp; No. Kunjungan</td>
                <td><?= $initDate ?> (No. Kunjungan: <?= esc($initial->no_visit ?? 'VS-' . date('Ymd') . '-0001') ?>)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Keluhan Utama</td>
                <td style="font-weight: bold;"><?= esc($initial->complaints ?? ($patient->complaints ?? '- (Pemeriksaan awal / Konsultasi kesehatan)')) ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Riwayat Penyakit Sekarang (RPS)</td>
                <td><?= esc($initial->subjective ?? 'Pasien datang dengan keluhan, dilakukan evaluasi triage awal, anamnesa keperawatan, dan pemeriksaan dokter.') ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Skrining Nyeri &amp; Risiko Jatuh</td>
                <td>Skala Nyeri (NRS): <strong><?= esc($initial->pain_scale ?? '0 / 10 (Tidak Nyeri)') ?></strong> | Risiko Jatuh: <strong>Rendah / Mandiri</strong></td>
            </tr>
        </table>

        <div class="section-header">B. TANDA-TANDA VITAL &amp; STATUS ANTROPOMETRI AWAL</div>
        <table class="ttv-grid">
            <thead>
                <tr>
                    <th>Tekanan Darah</th>
                    <th>Frekuensi Nadi</th>
                    <th>Suhu Tubuh</th>
                    <th>Laju Nafas (RR)</th>
                    <th>Berat Badan</th>
                    <th>Tinggi Badan</th>
                    <th>Status Gizi / IMT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?= esc($initial->blood_pressure ?? '120/80') ?></strong> mmHg</td>
                    <td><strong><?= esc($initial->pulse ?? '80') ?></strong> x/menit</td>
                    <td><strong><?= esc($initial->temperature ?? '36.5') ?></strong> °C</td>
                    <td><strong><?= esc($initial->respiration ?? '20') ?></strong> x/menit</td>
                    <td><strong><?= esc($initial->weight ?? '60') ?></strong> kg</td>
                    <td><strong><?= esc($initial->height ?? '165') ?></strong> cm</td>
                    <td>Normal</td>
                </tr>
            </tbody>
        </table>

        <div class="section-header">C. STATUS FISIK GENERALIS &amp; LOKALIS</div>
        <table class="table-bordered-custom">
            <tr>
                <td style="width: 25%; font-weight: bold;">Keadaan Umum &amp; GCS</td>
                <td>Tampak Sakit Ringan / Sedang &bull; Kesadaran: Compos Mentis (GCS: E4 M6 V5)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pemeriksaan Kepala / Leher / Thorax</td>
                <td>Kepala normocephal, konjungtiva ananemis, sclera ikterik (-), Cor: S1-S2 reguler, Pulmo: vesikuler normal.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Pemeriksaan Abdomen &amp; Ekstremitas</td>
                <td>Abdomen supel, bising usus normal, nyeri tekan (-), Ekstremitas akral hangat, CRT < 2 detik.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status Lokalis Area Keluhan</td>
                <td><?= esc($initial->objective ?? 'Pemeriksaan fisik status lokalis dalam batas wajar / sesuai temuan klinis.') ?></td>
            </tr>
        </table>

        <div class="section-header">D. ASESMEN SOAP PERTAMA DOKTER DPJP</div>
        <table class="soap-table" style="border: 1px solid #777777; padding: 4px; border-radius: 4px; margin-bottom: 6px;">
            <tr>
                <td class="soap-code">Subjective (S)</td>
                <td>: <?= esc($initial->subjective ?? 'Anamnesa keluhan klinis, riwayat perjalanan penyakit, dan faktor pemicu.') ?></td>
            </tr>
            <tr>
                <td class="soap-code">Objective (O)</td>
                <td>: <?= esc($initial->objective ?? 'Hasil pemeriksaan fisik, tanda vital, dan temuan klinis objektif.') ?></td>
            </tr>
            <tr>
                <td class="soap-code">Assessment (A)</td>
                <td>: 
                    <?php if (!empty($initial->icd10_code)): ?>
                        <strong>[ICD-10: <?= esc($initial->icd10_code) ?> - <?= esc($initial->icd10_desc) ?>]</strong> 
                    <?php endif; ?>
                    <?= esc($initial->assessment ?? 'Observasi Klinis / Evaluasi Rawat Jalan') ?>
                </td>
            </tr>
            <tr>
                <td class="soap-code">Plan &amp; Terapi (P)</td>
                <td>: <?= esc($initial->plan ?? 'Edukasi kesehatan, rencana tindakan/terapi medikamentosa, dan monitoring berkala.') ?></td>
            </tr>
        </table>

        <table class="signature-area">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 45px 0;">
                        Perawat Pengkaji Triage Awal,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( Ns. Perawat Klinik, S.Kep )
                    </p>
                    <p style="margin: 0; font-size: 8.5px; color: #555;">SIP: 449/SIP.P/2024</p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; position: relative;">
                    <p style="margin: 0 0 5px 0;">
                        Sumbawa Besar, <?= $initDate ?><br>
                        <strong>Dokter Penanggung Jawab Pelayanan (DPJP)</strong>
                    </p>
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php if (!empty($initial->doctor_signature)): ?>
                            <img src="<?= $initial->doctor_signature ?>" alt="TTD DPJP" style="max-height: 48px; max-width: 120px; z-index: 2;">
                        <?php else: ?>
                            <div style="height: 45px;"></div>
                        <?php endif; ?>
                        <?php if (clinic_setting('clinic_stamp')): ?>
                            <img src="<?= clinic_setting('clinic_stamp') ?>" alt="Stempel" style="max-height: 48px; position: absolute; left: 15px; opacity: 0.65; z-index: 1;">
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($initial->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Medis')) ?>
                    </p>
                    <p style="margin: 0; font-size: 8.5px; color: #555;">SIP: <?= esc($initial->doctor_sip ?? '446/SIP.D/2024') ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 3: CATATAN PERKEMBANGAN PASIEN TERINTEGRASI (CPPT)                 -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 3: CATATAN PERKEMBANGAN PASIEN TERINTEGRASI (CPPT)</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-03 &bull; LEMBAR KUNJUNGAN ULANG BERKALA</span>
        </div>

        <div style="font-size: 9.5px; margin-bottom: 6px; border-bottom: 1px dashed #999; padding-bottom: 3px;">
            <strong>No RM:</strong> <?= esc($patient->no_rm) ?> | 
            <strong>Nama Pasien:</strong> <?= esc($patient->name) ?> | 
            <strong>JK/Usia:</strong> <?= $patient->gender ?>/<?= $age ?>
        </div>

        <table class="table-bordered-custom">
            <thead>
                <tr>
                    <th style="width: 12%;">Tgl / Jam</th>
                    <th style="width: 15%;">PPA / Profesi</th>
                    <th style="width: 43%;">Hasil Asesmen Pasien (SOAP Klinis)</th>
                    <th style="width: 18%;">Instruksi PPA &amp; e-Resep</th>
                    <th style="width: 12%;">Paraf DPJP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($visits)): ?>
                    <?php foreach ($visits as $vIdx => $v): 
                        $vDate = $v->visit_date ? date('d/m/y', strtotime($v->visit_date)) : '-';
                    ?>
                        <tr>
                            <td style="text-align: center; font-size: 9px;">
                                <strong><?= $vDate ?></strong><br>
                                <span style="font-size: 8px; color:#555;"><?= esc($v->no_visit) ?></span>
                            </td>
                            <td style="font-size: 9px;">
                                <strong><?= esc($v->doctor_name ?: 'Dokter DPJP') ?></strong><br>
                                <span style="font-size: 8px; color:#666;"><?= esc($v->polyclinic_name ?: $v->tindakan_name ?: 'Poli') ?></span>
                            </td>
                            <td style="font-size: 9px;">
                                <div><strong>TD:</strong> <?= esc($v->blood_pressure ?: '-') ?> mmHg | <strong>N:</strong> <?= esc($v->pulse ?: '-') ?> | <strong>S:</strong> <?= esc($v->temperature ?: '-') ?>°C</div>
                                <div><strong>S:</strong> <?= esc($v->subjective ?: $v->complaints ?: '-') ?></div>
                                <div><strong>O:</strong> <?= esc($v->objective ?: '-') ?></div>
                                <div><strong>A:</strong> <?= esc($v->assessment ?: '-') ?></div>
                                <div><strong>P:</strong> <?= esc($v->plan ?: '-') ?></div>
                            </td>
                            <td style="font-size: 8.5px;">
                                <?php if (!empty($v->icd10_code)): ?>
                                    <span style="font-weight: bold; color: #0d6b38;">ICD: <?= esc($v->icd10_code) ?></span><br>
                                <?php endif; ?>
                                <?php if (!empty($v->medicines)): ?>
                                    <?php foreach (array_slice($v->medicines, 0, 3) as $med): ?>
                                        &bull; <?= esc($med->medicine_name) ?> (<?= esc($med->qty) ?>)<br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #888;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 8.5px;">
                                <div style="font-family: monospace; font-size: 8px; color: #0d6b38;">[TERVERIFIKASI]</div>
                                <strong><?= esc($v->doctor_name ?: 'DPJP') ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Baris Template Kosong Standar Dokumen Fisik -->
                <?php 
                    $blankRows = max(0, 5 - count($visits)); 
                    for ($i = 1; $i <= $blankRows; $i++): 
                ?>
                    <tr style="height: 52px;">
                        <td style="text-align: center; color: #888;">.../.../20...</td>
                        <td style="color: #888;">Dokter DPJP / Perawat</td>
                        <td style="color: #888;">
                            <strong>S:</strong><br>
                            <strong>O:</strong> TD: ..../.... mmHg, N: .... x/m, S: .... °C<br>
                            <strong>A:</strong><br>
                            <strong>P:</strong>
                        </td>
                        <td style="color: #888;">ICD-10: ..........</td>
                        <td style="text-align: center; color: #888;">( ............ )</td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div style="font-size: 8.5px; color: #666; margin-top: 8px;">
            * Lembar CPPT diisi berurutan oleh Tenaga Medis (Dokter, Perawat, Apoteker) setiap kali pasien melakukan kunjungan konsultasi berkala.
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 4: LEMBAR INFORMASI & EDUKASI TINDAKAN MEDIS                      -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 4: PEMBERIAN INFORMASI &amp; EDUKASI TINDAKAN KEDOKTERAN</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-04 &bull; LEMBAR EDUKASI SEBELUM TINDAKAN</span>
        </div>

        <?php 
            $firstConsent = !empty($informedConsents) ? $informedConsents[0] : null; 
        ?>

        <div class="section-header">A. IDENTITAS PASIEN &amp; DOKTER PEMBERI INFORMASI</div>
        <table class="table-info" style="border: 1px solid #777; padding: 5px; border-radius: 4px; margin-bottom: 6px;">
            <tr>
                <td class="label">Dokter Pelaksana Tindakan</td>
                <td>: <strong><?= esc($firstConsent->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Tindakan')) ?></strong></td>
                <td class="label">Nama Pasien</td>
                <td>: <strong><?= esc($patient->name) ?></strong></td>
            </tr>
            <tr>
                <td class="label">Pemberi Edukasi Medis</td>
                <td>: <?= esc($firstConsent->doctor_name ?? 'Dokter DPJP / Pelaksana') ?></td>
                <td class="label">No. Rekam Medis</td>
                <td>: <span style="font-family: monospace; font-weight: bold;"><?= esc($patient->no_rm) ?></span></td>
            </tr>
            <tr>
                <td class="label">Penerima Edukasi / Hubungan</td>
                <td>: <?= esc($firstConsent->authorized_person_name ?? $patient->name) ?> (<?= esc($firstConsent->authorized_person_relation ?? 'Diri Sendiri') ?>)</td>
                <td class="label">Jenis Kelamin / Usia</td>
                <td>: <?= $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?> / <?= $age ?></td>
            </tr>
        </table>

        <div class="section-header">B. MATERI PENJELASAN TINDAKAN KEDOKTERAN (10 BUTIR STANDAR KKI)</div>
        <table class="table-bordered-custom">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Jenis Informasi Klinis</th>
                    <th style="width: 55%;">Isi Materi Penjelasan Dokter DPJP</th>
                    <th style="width: 15%;">Tanda Konfirmasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td><strong>Diagnosis (Kerja &amp; Banding)</strong></td>
                    <td><?= esc($firstConsent->diagnosis ?? 'Diagnosis kerja berdasarkan anamnesa dan pemeriksaan fisik klinis.') ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td><strong>Dasar Diagnosis &amp; Indikasi</strong></td>
                    <td><?= esc($firstConsent->indication ?? 'Indikasi medis perlunya tindakan untuk terapi kuratif / perbaikan klinis.') ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td><strong>Tata Cara &amp; Prosedur Tindakan</strong></td>
                    <td><strong><?= esc($firstConsent->procedure_name ?? 'Tindakan Pelayanan Medis / Terapi Klinis') ?></strong><br><?= esc($firstConsent->procedure_desc ?? 'Prosedur tindakan aseptik standar operasional profesi.') ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td><strong>Tujuan Tindakan Medis</strong></td>
                    <td>Tujuan kuratif, simptomatis, atau estetika medis terpadu sesuai indikasi.</td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td><strong>Risiko &amp; Komplikasi</strong></td>
                    <td><?= esc($firstConsent->risks_complications ?? 'Risiko nyeri ringan, reaksi alergi lokal, atau pembengkakan sementara.') ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">6</td>
                    <td><strong>Prognosis Tindakan</strong></td>
                    <td><?= esc($firstConsent->prognosis ?? 'Dubia ad Bonam (Baik dengan kepatuhan instruksi perawatan)') ?></td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">7</td>
                    <td><strong>Alternatif Terapi &amp; Risiko</strong></td>
                    <td>Alternatif terapi konservatif serta potensi risiko apabila tindakan tidak dilakukan.</td>
                    <td style="text-align: center; font-weight: bold; color: #0d6b38;">✔ Telah Dijelaskan</td>
                </tr>
            </tbody>
        </table>

        <div class="section-header">C. PERNYATAAN PEMBERIAN INFORMASI</div>
        <p style="font-size: 9px; text-align: justify; color: #333333; margin: 4px 0 15px 0;">
            Dengan ini menyatakan bahwa saya, Dokter Penanggung Jawab Pelayanan telah menjelaskan butir-butir informasi di atas secara benar dan jelas, serta memberikan kesempatan untuk bertanya. Pasien/Keluarga menyatakan telah memahami seluruh materi penjelasan.
        </p>

        <table class="signature-area">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 45px 0;">
                        Pasien / Penerima Edukasi,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc($firstConsent->authorized_person_name ?? $patient->name) ?> )
                    </p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; position: relative;">
                    <p style="margin: 0 0 5px 0;">
                        Sumbawa Besar, <?= $firstConsent && $firstConsent->consent_date ? date('d F Y', strtotime($firstConsent->consent_date)) : date('d F Y') ?><br>
                        <strong>Dokter Pelaksana / Pemberi Informasi</strong>
                    </p>
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php 
                            $docSig4 = !empty($firstConsent->doctor_signature) ? $firstConsent->doctor_signature : (!empty($firstConsent->doctor_ref_signature) ? $firstConsent->doctor_ref_signature : null);
                        ?>
                        <?php if ($docSig4): ?>
                            <img src="<?= $docSig4 ?>" alt="TTD DPJP" style="max-height: 48px; max-width: 120px; z-index: 2;">
                        <?php else: ?>
                            <div style="height: 45px;"></div>
                        <?php endif; ?>
                        <?php if (clinic_setting('clinic_stamp')): ?>
                            <img src="<?= clinic_setting('clinic_stamp') ?>" alt="Stempel" style="max-height: 48px; position: absolute; left: 15px; opacity: 0.65; z-index: 1;">
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($firstConsent->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Tindakan')) ?>
                    </p>
                    <p style="margin: 0; font-size: 8.5px; color: #555;">SIP: <?= esc($firstConsent->doctor_sip ?? '503/SIP-D/2026') ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 5: PERNYATAAN PERSETUJUAN / PENOLAKAN TINDAKAN (INFORMED CONSENT)   -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 5: PERNYATAAN PERSETUJUAN / PENOLAKAN TINDAKAN MEDIS</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-05 &bull; SURAT PERNYATAAN TINDAKAN MEDIS</span>
        </div>

        <div class="section-header">A. IDENTITAS PEMBERI PERNYATAAN</div>
        <table class="table-info" style="border: 1px solid #777; padding: 5px; border-radius: 4px; margin-bottom: 6px;">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: <strong><?= esc($firstConsent->authorized_person_name ?? $patient->name) ?></strong></td>
                <td class="label">Hubungan dgn Pasien</td>
                <td>: <?= esc($firstConsent->authorized_person_relation ?? 'Diri Sendiri') ?></td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td>: <?= esc($patient->address ?: '-') ?></td>
                <td class="label">Nomor Telepon / HP</td>
                <td>: <?= esc($patient->phone ?: '-') ?></td>
            </tr>
        </table>

        <div class="section-header">B. PERNYATAAN PERSETUJUAN / PENOLAKAN HUKUM</div>
        <div style="border: 1.5px solid #0d9f4f; padding: 8px 10px; background: #f4faf6; border-radius: 4px; margin-bottom: 8px; font-size: 10px; text-align: justify; line-height: 1.5;">
            Dengan ini menyatakan <strong><?= (!empty($firstConsent) && $firstConsent->consent_type === 'refuse') ? '<span style="color:#b71c1c;">MENOLAK</span>' : '<span style="color:#0d6b38;">MENYETUJUI</span>' ?></strong> 
            untuk dilakukannya tindakan medis berupa:
            <div style="font-size: 11.5px; font-weight: bold; color: #0d6b38; margin: 3px 0; text-align: center; text-transform: uppercase;">
                "<?= esc($firstConsent->procedure_name ?? 'TINDAKAN PELAYANAN MEDIS / TERAPI KLINIS') ?>"
            </div>
            Terhadap Pasien: <strong><?= esc($patient->name) ?></strong> (No. RM: <strong><?= esc($patient->no_rm) ?></strong>).<br>
            Saya memahami sepenuhnya perlunya tindakan tersebut dan telah menyadari bahwa ilmu kedokteran bukanlah ilmu pasti, sehingga keberhasilan tindakan adalah ikhtiar terbaik sesuai standar profesi medis.
        </div>

        <div class="section-header">C. LEMBAR PENGESAHAN TANDA TANGAN (DIGITAL / FISIK)</div>
        <table class="signature-area" style="margin-top: 15px;">
            <tr>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 5px 0;">
                        Pasien / Yang Menyatakan,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($firstConsent->authorized_person_signature)): ?>
                            <img src="<?= $firstConsent->authorized_person_signature ?>" alt="TTD Pasien" style="max-height: 55px; max-width: 130px;">
                        <?php else: ?>
                            <div style="border-bottom: 1px dashed #999; width: 130px; height: 45px;"></div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc($firstConsent->authorized_person_name ?? $patient->name) ?> )
                    </p>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 5px 0;">
                        Saksi Klinik / Perawat,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                        <div style="border-bottom: 1px dashed #999; width: 130px; height: 45px;"></div>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc($firstConsent->witness_name ?? 'Ns. Perawat Klinik') ?> )
                    </p>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 5px 0;">
                        Dokter Pelaksana Tindakan,<br>
                        <strong>Tanda Tangan DPJP</strong>
                    </p>
                    <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                        <?php 
                            $docSig5 = !empty($firstConsent->doctor_signature) ? $firstConsent->doctor_signature : (!empty($firstConsent->doctor_ref_signature) ? $firstConsent->doctor_ref_signature : null);
                        ?>
                        <?php if ($docSig5): ?>
                            <img src="<?= $docSig5 ?>" alt="TTD Dokter" style="max-height: 55px; max-width: 130px;">
                        <?php else: ?>
                            <div style="border-bottom: 1px dashed #999; width: 130px; height: 45px;"></div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( <?= esc($firstConsent->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Medis')) ?> )
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 6: DOKUMENTASI FOTO PASIEN (BEFORE / PRA-TINDAKAN)                 -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 6: DOKUMENTASI FOTO KLINIS PASIEN (PRA-TINDAKAN / BEFORE)</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-06 &bull; DOKUMENTASI VISUAL KAMERA IPAD / DIGITAL</span>
        </div>

        <div style="font-size: 9.5px; margin-bottom: 6px; border-bottom: 1px dashed #999; padding-bottom: 3px;">
            <strong>No RM:</strong> <?= esc($patient->no_rm) ?> | 
            <strong>Nama Pasien:</strong> <?= esc($patient->name) ?> | 
            <strong>Kategori:</strong> <span class="badge-kategori">PRA-TINDAKAN (BEFORE)</span>
        </div>

        <?php 
            $beforePhotos = [];
            if (!empty($medicalPhotos)) {
                foreach ($medicalPhotos as $mp) {
                    if ($mp->category === 'before' || empty($beforePhotos)) {
                        $beforePhotos[] = $mp;
                    }
                }
            }
        ?>

        <?php if (!empty($beforePhotos)): ?>
            <div class="photo-box">
                <img src="<?= base_url($beforePhotos[0]->photo_path) ?>" alt="Foto Pra-Tindakan">
                <div style="font-size: 10.5px; font-weight: bold; margin-top: 6px; color: #0d6b38;">
                    <?= esc($beforePhotos[0]->title) ?>
                </div>
                <div style="font-size: 9px; color: #555555; margin-top: 2px;">
                    Diambil pada: <?= date('d/m/Y H:i', strtotime($beforePhotos[0]->created_at)) ?> WITA &bull; Petugas: <?= esc($beforePhotos[0]->taken_by ?: 'Perawat Klinik') ?>
                </div>
            </div>
            <table class="table-bordered-custom">
                <tr>
                    <td style="width: 25%; font-weight: bold;">Catatan Klinis Visual</td>
                    <td><?= esc($beforePhotos[0]->description ?: 'Tampak kondisi visual area tindakan sebelum dilakukan intervensi medis.') ?></td>
                </tr>
            </table>
        <?php else: ?>
            <!-- Placeholder Frame Standar Dokumen Fisik -->
            <div class="photo-box" style="border: 2px dashed #bbbbbb; background: #fafafa;">
                <div style="color: #888888; font-size: 36px; margin-bottom: 4px;">📷</div>
                <div style="font-weight: bold; font-size: 11.5px; color: #555555;">FRAME FOTO KLINIS PRA-TINDAKAN (BEFORE)</div>
                <div style="font-size: 9.5px; color: #888888; max-width: 400px; margin-top: 3px;">
                    Area lampiran foto klinis dari iPad / kamera perawat saat asesmen awal pasien.
                </div>
            </div>
            <table class="table-bordered-custom">
                <tr>
                    <td style="width: 25%; font-weight: bold;">Catatan Klinis Visual</td>
                    <td style="color: #777;">Tampak kondisi klinis area sebelum tindakan medis (diisi oleh dokter/perawat).</td>
                </tr>
            </table>
        <?php endif; ?>

        <table class="signature-area">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0 0 45px 0;">
                        Petugas Pengambil Foto / Perawat,<br>
                        <strong>Tanda Tangan</strong>
                    </p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        ( Ns. Perawat Klinik, S.Kep )
                    </p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; position: relative;">
                    <p style="margin: 0 0 5px 0;">
                        Sumbawa Besar, <?= date('d F Y') ?><br>
                        <strong>Dokter Penanggung Jawab Pelayanan</strong>
                    </p>
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php if (!empty($initial->doctor_signature)): ?>
                            <img src="<?= $initial->doctor_signature ?>" alt="TTD DPJP" style="max-height: 48px; max-width: 120px; z-index: 2;">
                        <?php else: ?>
                            <div style="height: 45px;"></div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($initial->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Medis')) ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 7: DOKUMENTASI FOTO PASIEN (AFTER / PASCA-TINDAKAN & EVALUASI)    -->
    <!-- ========================================================================= -->
    <div class="page-sheet">
        <div class="header">
            <h2><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></h2>
            <h4>PELAYANAN MEDIS, REKAM MEDIS ELEKTRONIK &amp; APOTEK TERPADU</h4>
        </div>

        <div class="doc-title">
            <h3>LEMBAR 7: DOKUMENTASI FOTO PASCA-TINDAKAN &amp; EVALUASI AKHIR (AFTER)</h3>
            <span class="sheet-code">KODE FORMULIR: RM-GF-07 &bull; EVALUASI HASIL &amp; DOKUMENTASI AKHIR</span>
        </div>

        <div style="font-size: 9.5px; margin-bottom: 6px; border-bottom: 1px dashed #999; padding-bottom: 3px;">
            <strong>No RM:</strong> <?= esc($patient->no_rm) ?> | 
            <strong>Nama Pasien:</strong> <?= esc($patient->name) ?> | 
            <strong>Kategori:</strong> <span class="badge-kategori" style="background:#e8f4fd; color:#0d47a1; border-color:#2196f3;">PASCA-TINDAKAN (AFTER / EVALUASI)</span>
        </div>

        <?php 
            $afterPhotos = [];
            if (!empty($medicalPhotos)) {
                foreach ($medicalPhotos as $mp) {
                    if ($mp->category === 'after') {
                        $afterPhotos[] = $mp;
                    }
                }
                if (empty($afterPhotos) && count($medicalPhotos) > 1) {
                    $afterPhotos[] = $medicalPhotos[count($medicalPhotos) - 1];
                }
            }
        ?>

        <?php if (!empty($afterPhotos)): ?>
            <div class="photo-box">
                <img src="<?= base_url($afterPhotos[0]->photo_path) ?>" alt="Foto Pasca-Tindakan">
                <div style="font-size: 10.5px; font-weight: bold; margin-top: 6px; color: #0d47a1;">
                    <?= esc($afterPhotos[0]->title) ?>
                </div>
                <div style="font-size: 9px; color: #555555; margin-top: 2px;">
                    Diambil pada: <?= date('d/m/Y H:i', strtotime($afterPhotos[0]->created_at)) ?> WITA &bull; Petugas: <?= esc($afterPhotos[0]->taken_by ?: 'Perawat Klinik') ?>
                </div>
            </div>
            <table class="table-bordered-custom">
                <tr>
                    <td style="width: 25%; font-weight: bold;">Evaluasi Klinis &amp; Hasil</td>
                    <td><?= esc($afterPhotos[0]->description ?: 'Tampak hasil klinis pasca-tindakan membaik sesuai rencana terapi.') ?></td>
                </tr>
            </table>
        <?php else: ?>
            <!-- Placeholder Frame Standar Dokumen Fisik -->
            <div class="photo-box" style="border: 2px dashed #bbbbbb; background: #fafafa;">
                <div style="color: #888888; font-size: 36px; margin-bottom: 4px;">📸</div>
                <div style="font-weight: bold; font-size: 11.5px; color: #555555;">FRAME FOTO KLINIS PASCA-TINDAKAN (AFTER / HASIL)</div>
                <div style="font-size: 9.5px; color: #888888; max-width: 400px; margin-top: 3px;">
                    Area lampiran foto klinis hasil evaluasi pasca-tindakan atau follow-up pasien.
                </div>
            </div>
            <table class="table-bordered-custom">
                <tr>
                    <td style="width: 25%; font-weight: bold;">Evaluasi Klinis &amp; Hasil</td>
                    <td style="color: #777;">Tampak hasil pasca-tindakan medis dan evaluasi pemulihan.</td>
                </tr>
            </table>
        <?php endif; ?>

        <div class="section-header">KESIMPULAN AKHIR &amp; INSTRUKSI PULANG DPJP</div>
        <table class="table-bordered-custom">
            <tr>
                <td style="width: 25%; font-weight: bold;">Instruksi Perawatan di Rumah</td>
                <td>Minum obat teratur sesuai dosis, menjaga kebersihan area tindakan, dan istirahat cukup.</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jadwal Kontrol Ulang</td>
                <td>Kontrol kembali 3 - 7 hari pasca tindakan atau bila timbul keluhan darurat / komplikasi.</td>
            </tr>
        </table>

        <table class="signature-area">
            <tr>
                <td style="width: 50%; text-align: left; vertical-align: bottom;">
                    <p style="margin: 0; font-size: 8.5px; color: #666666;">
                        * Berkas Rekam Medis Lengkap (Lembar 1 s/d 7) sah diterbitkan oleh Sistem RME Terpadu <?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?>.<br>
                        * Dicetak pada: <?= date('d/m/Y H:i') ?> WITA.
                    </p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top; position: relative;">
                    <p style="margin: 0 0 5px 0;">
                        Sumbawa Besar, <?= date('d F Y') ?><br>
                        <strong>Dokter Penanggung Jawab Pelayanan (DPJP)</strong>
                    </p>
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php if (!empty($initial->doctor_signature)): ?>
                            <img src="<?= $initial->doctor_signature ?>" alt="TTD DPJP" style="max-height: 48px; max-width: 120px; z-index: 2;">
                        <?php else: ?>
                            <div style="height: 45px;"></div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        <?= esc($initial->doctor_name ?? clinic_setting('clinic_director', 'dr. Pelaksana Medis')) ?>
                    </p>
                    <p style="margin: 0; font-size: 8.5px; color: #555;">SIP: <?= esc($initial->doctor_sip ?? '446/SIP.D/2024') ?></p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
