<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Cetak Surat Medis') ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            color: #000000;
            background: #ffffff;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            font-size: 11px;
        }
        .letter-title {
            text-align: center;
            margin-bottom: 25px;
        }
        .letter-title h4 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .letter-title .letter-no {
            font-size: 13px;
            margin-top: 4px;
        }
        .content-body {
            text-align: justify;
            margin-bottom: 30px;
        }
        .patient-table {
            width: 100%;
            margin: 15px 0 20px 20px;
            border-collapse: collapse;
        }
        .patient-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 75px;
        }
        .no-print {
            background: #f4f6f9;
            padding: 10px 15px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 13px;
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
    <div><strong>Pratinjau Surat Keterangan Medis</strong> (Format Cetak A4/A5 Resmi)</div>
    <div>
        <button onclick="window.print();" class="btn btn-print">Cetak Dokumen Ini</button>
        <button onclick="window.close();" class="btn">Tutup</button>
    </div>
</div>

<!-- Letterhead / Kop Klinik -->
<div class="header">
    <h2>SAWAMAWA MEDICAL CENTER</h2>
    <h3>KLINIK PRATAMA RAWAT JALAN & PELAYANAN MEDIS TERPADU</h3>
    <p>Jl. Kebangsaan No. 12, Sumbawa Besar, Nusa Tenggara Barat (NTB) | Telp: (0371) 23456 | CS: 0812-3456-7890</p>
    <p>Izin Operasional Klinik No: 503/042/DPM-PTSP/2026</p>
</div>

<!-- Judul Surat -->
<div class="letter-title">
    <?php if ($letter->letter_type === 'sakit'): ?>
        <h4>SURAT KETERANGAN SAKIT</h4>
        <div class="letter-no">Nomor: <?= esc($letter->letter_no) ?></div>
    <?php elseif ($letter->letter_type === 'sehat'): ?>
        <h4>SURAT KETERANGAN KESEHATAN</h4>
        <div class="letter-no">Nomor: <?= esc($letter->letter_no) ?></div>
    <?php else: ?>
        <h4>SURAT RUJUKAN PASIEN</h4>
        <div class="letter-no">Nomor: <?= esc($letter->letter_no) ?></div>
    <?php endif; ?>
</div>

<!-- Isi Surat -->
<div class="content-body">
    <?php if ($letter->letter_type === 'sakit'): ?>
        <p>Yang bertanda tangan di bawah ini, Dokter Pemeriksa pada <strong>Sawamawa Medical Center</strong>, menerangkan dengan sebenarnya bahwa:</p>
        
        <table class="patient-table">
            <tr>
                <td style="width: 170px;">Nama Lengkap</td>
                <td style="width: 15px;">:</td>
                <td><strong><?= esc($letter->patient_name) ?></strong></td>
            </tr>
            <tr>
                <td>No. Rekam Medis</td>
                <td>:</td>
                <td><?= esc($letter->no_rm) ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin / Umur</td>
                <td>:</td>
                <td>
                    <?= $letter->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?> / 
                    <?php 
                        $dob = new DateTime($letter->date_of_birth);
                        $now = new DateTime();
                        echo $now->diff($dob)->y . ' Tahun';
                    ?>
                </td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= esc($letter->address) ?></td>
            </tr>
            <tr>
                <td>Diagnosa Medis</td>
                <td>:</td>
                <td><em><?= esc($letter->diagnosis ?: '-') ?></em></td>
            </tr>
        </table>

        <p>
            Berhubung sedang dalam keadaan sakit, yang bersangkutan memerlukan waktu untuk beristirahat dan tidak dapat menjalankan aktivitas / pekerjaannya selama <strong><?= $letter->duration_days ?> (<?= $letter->duration_days ?>) hari</strong>, terhitung mulai tanggal <strong><?= date('d F Y', strtotime($letter->start_date)) ?></strong> sampai dengan tanggal <strong><?= date('d F Y', strtotime($letter->end_date)) ?></strong>.
        </p>
        <p>Demikian surat keterangan ini kami berikan dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    <?php elseif ($letter->letter_type === 'sehat'): ?>
        <p>Yang bertanda tangan di bawah ini, Dokter Pemeriksa pada <strong>Sawamawa Medical Center</strong>, menerangkan bahwa telah melakukan pemeriksaan kesehatan fisik kepada:</p>
        
        <table class="patient-table">
            <tr>
                <td style="width: 170px;">Nama Lengkap</td>
                <td style="width: 15px;">:</td>
                <td><strong><?= esc($letter->patient_name) ?></strong></td>
            </tr>
            <tr>
                <td>NIK KTP / No. RM</td>
                <td>:</td>
                <td><?= esc($letter->nik) ?> / <?= esc($letter->no_rm) ?></td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>
                    <?= date('d F Y', strtotime($letter->date_of_birth)) ?> (<?php 
                        $dob = new DateTime($letter->date_of_birth);
                        $now = new DateTime();
                        echo $now->diff($dob)->y . ' Tahun';
                    ?>)
                </td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td><?= $letter->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
            </tr>
            <tr>
                <td>Alamat Domisili</td>
                <td>:</td>
                <td><?= esc($letter->address) ?></td>
            </tr>
        </table>

        <p>Berdasarkan hasil pemeriksaan klinis saat ini:</p>
        <table class="patient-table" style="margin-top: 5px; margin-bottom: 10px;">
            <tr>
                <td style="width: 170px;">1. Tekanan Darah</td>
                <td style="width: 15px;">:</td>
                <td><?= esc($letter->blood_pressure ?: '120/80 mmHg') ?></td>
            </tr>
            <tr>
                <td>2. Berat / Tinggi Badan</td>
                <td>:</td>
                <td><?= $letter->weight ? $letter->weight . ' kg' : '-' ?> / <?= $letter->height ? $letter->height . ' cm' : '-' ?></td>
            </tr>
            <tr>
                <td>3. Penglihatan Warna</td>
                <td>:</td>
                <td><?= $letter->color_blind === 'normal' ? 'Normal (Tidak Buta Warna)' : ucfirst($letter->color_blind) ?></td>
            </tr>
            <tr>
                <td>4. Kesimpulan Dokter</td>
                <td>:</td>
                <td><strong style="text-decoration: underline;"><?= $letter->health_status === 'sehat' ? 'SEHAT BADAN (Layak Beraktivitas / Bekerja)' : 'TIDAK SEHAT' ?></strong></td>
            </tr>
            <tr>
                <td>5. Keperluan Surat</td>
                <td>:</td>
                <td><?= esc($letter->purpose ?: 'Persyaratan Administrasi') ?></td>
            </tr>
        </table>

        <p>Demikian surat keterangan kesehatan ini dibuat dengan sebenar-benarnya untuk dipergunakan sesuai ketentuan yang berlaku.</p>

    <?php else: ?>
        <p>Kepada Yth.<br>
        <strong>Dokter Spesialis <?= esc($letter->referral_poly ?: 'Penyakit Dalam') ?></strong><br>
        di <?= esc($letter->referral_destination ?: 'Rumah Sakit Rujukan') ?></p>

        <p>Dengan hormat,<br>
        Mohon bantuan pemeriksaan dan penanganan lanjutan terhadap pasien kami:</p>

        <table class="patient-table">
            <tr>
                <td style="width: 170px;">Nama Pasien</td>
                <td style="width: 15px;">:</td>
                <td><strong><?= esc($letter->patient_name) ?></strong> (<?= $letter->gender === 'L' ? 'L' : 'P' ?>)</td>
            </tr>
            <tr>
                <td>No. RM / No. BPJS</td>
                <td>:</td>
                <td><?= esc($letter->no_rm) ?></td>
            </tr>
            <tr>
                <td>Umur / Tgl Lahir</td>
                <td>:</td>
                <td><?= date('d/m/Y', strtotime($letter->date_of_birth)) ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= esc($letter->address) ?></td>
            </tr>
            <tr>
                <td>Diagnosa Kerja Sementara</td>
                <td>:</td>
                <td><strong><?= esc($letter->diagnosis ?: '-') ?></strong></td>
            </tr>
            <tr>
                <td>Alasan Rujukan</td>
                <td>:</td>
                <td><?= esc($letter->referral_reason ?: 'Mohon evaluasi dan tata laksana spesialis lanjutan.') ?></td>
            </tr>
        </table>

        <p>Atas kerjasama dan bantuan teman sejawat, kami ucapkan terima kasih.</p>
    <?php endif; ?>
</div>

<!-- Signature Section -->
<div class="signature-section">
    <div class="signature-box" style="position: relative;">
        <div>Sumbawa Besar, <?= date('d F Y', strtotime($letter->letter_date)) ?></div>
        <div class="font-weight-bold">Dokter Pemeriksa,</div>
        
        <div class="signature-space" style="height: 65px; display: flex; align-items: center; justify-content: center; position: relative;">
            <?php if (!empty($letter->doctor_signature)): ?>
                <img src="<?= $letter->doctor_signature ?>" alt="TTD Dokter" style="max-height: 60px; max-width: 140px; z-index: 2;">
            <?php endif; ?>
            <?php if (clinic_setting('clinic_stamp')): ?>
                <img src="<?= clinic_setting('clinic_stamp') ?>" alt="Stempel Klinik" style="max-height: 60px; position: absolute; left: 10px; opacity: 0.75; z-index: 1;">
            <?php endif; ?>
        </div>

        <div><strong><u><?= esc($letter->doctor_name ?? 'dr. Dokter Pemeriksa') ?></u></strong></div>
        <div style="font-size: 9.5px; color: #444;">SIP: <?= esc($letter->sip_number ?: '503/SIP-D/2026') ?></div>
    </div>
    <div style="clear: both;"></div>
</div>

</body>
</html>
