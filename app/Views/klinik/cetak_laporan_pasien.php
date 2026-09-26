<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Laporan Database Pasien - Gitria Farma') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }

        .report-page {
            background: #ffffff;
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .clinic-kop {
            border-bottom: 2.5px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .table-data th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
            text-align: center;
        }

        .table-data td {
            border: 1px solid #e2e8f0;
            padding: 5px 7px;
            vertical-align: middle;
        }

        .table-data tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }

        /* Floating Action Bar */
        .print-floating-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background: rgba(15, 23, 42, 0.95);
            padding: 10px 18px;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .report-page {
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
            .print-floating-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar -->
    <div class="print-floating-bar">
        <span class="text-white font-weight-bold mr-2 text-xs">
            <i class="fas fa-file-pdf text-danger mr-1"></i> Laporan Pasien
        </span>
        <button type="button" class="btn btn-sm btn-success font-weight-bold" onclick="window.location.href='<?= base_url('klinik/export-pasien-excel?' . http_build_query($_GET)) ?>'">
            <i class="fas fa-file-excel mr-1"></i> Unduh Excel
        </button>
        <button type="button" class="btn btn-sm btn-info font-weight-bold" onclick="window.print();">
            <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
        </button>
        <button type="button" class="btn btn-sm btn-outline-light" onclick="window.close();">
            <i class="fas fa-times mr-1"></i> Tutup
        </button>
    </div>

    <div class="report-page">
        <!-- KOP KLINIK -->
        <div class="clinic-kop">
            <div class="row align-items-center">
                <div class="col-auto">
                    <?php if (clinic_logo()): ?>
                        <img src="<?= clinic_logo() ?>" alt="Logo" style="max-height: 55px; max-width: 130px; object-fit: contain;">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-teal text-white rounded font-weight-bold" style="width: 50px; height: 50px; font-size: 20px;">
                            GF
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col text-center">
                    <h4 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;"><?= esc(clinic_setting('clinic_name', 'KLINIK UTAMA GITRIA FARMA')) ?></h4>
                    <p class="text-muted mb-0" style="font-size: 11px;">
                        <?= esc(clinic_setting('clinic_address', 'Jl. Kebangsaan No. 45, Sumbawa Besar, Nusa Tenggara Barat')) ?> &bull; Telp/WA: <?= esc(clinic_setting('clinic_phone', '0812-3456-7890')) ?>
                    </p>
                    <p class="text-muted mb-0" style="font-size: 10px;">
                        Izin Operasional Klinik: <?= esc(clinic_setting('clinic_license', '440/128/DINKES/2023')) ?> &bull; Kode Faskes: <?= esc(clinic_setting('clinic_faskes_code', '52040101')) ?>
                    </p>
                </div>
                <div class="col-auto text-right font-mono" style="font-size: 9.5px;">
                    <div><strong>LAP-PASIEN-01</strong></div>
                    <div class="text-muted">Tgl Cetak: <?= date('d/m/Y H:i') ?> WITA</div>
                </div>
            </div>
        </div>

        <!-- JUDUL & FILTER LAPORAN -->
        <div class="text-center mb-3">
            <h5 class="font-weight-bold text-dark mb-1" style="text-transform: uppercase; letter-spacing: 0.5px;">
                LAPORAN DATABASE DIREKTORI PASIEN TERDAFTAR
            </h5>
            <div class="text-muted text-xs">
                <?php if (!empty($startDate) || !empty($endDate)): ?>
                    Periode Pendaftaran: <strong><?= $startDate ? date('d/m/Y', strtotime($startDate)) : 'Awal' ?></strong> s/d <strong><?= $endDate ? date('d/m/Y', strtotime($endDate)) : 'Sekarang' ?></strong>
                <?php else: ?>
                    Periode: <strong>Semua Waktu (Master Database Pasien)</strong>
                <?php endif; ?>
                <?php if (!empty($filterGender)): ?>
                    &bull; Gender: <strong><?= $filterGender === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                <?php endif; ?>
                <?php if (!empty($filterTier)): ?>
                    &bull; Kategori: <strong><?= ucfirst($filterTier) ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <!-- RINGKASAN METRIK -->
        <div class="row mb-3">
            <div class="col">
                <div class="summary-card">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px;">Total Pasien</small>
                    <strong class="text-dark" style="font-size: 15px;"><?= number_format($totalPatients) ?></strong>
                </div>
            </div>
            <div class="col">
                <div class="summary-card">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px;">Laki-laki</small>
                    <strong class="text-primary" style="font-size: 15px;"><?= number_format($totalMale) ?></strong>
                </div>
            </div>
            <div class="col">
                <div class="summary-card">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px;">Perempuan</small>
                    <strong class="text-danger" style="font-size: 15px;"><?= number_format($totalFemale) ?></strong>
                </div>
            </div>
            <div class="col">
                <div class="summary-card">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px;">Pasien BPJS</small>
                    <strong class="text-success" style="font-size: 15px;"><?= number_format($totalBpjs) ?></strong>
                </div>
            </div>
            <div class="col">
                <div class="summary-card">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px;">Pasien Umum</small>
                    <strong class="text-teal" style="font-size: 15px; color: #0d9488;"><?= number_format($totalUmum) ?></strong>
                </div>
            </div>
        </div>

        <!-- TABEL DATA PASIEN -->
        <table class="table-data mb-4">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 80px;">No RM</th>
                    <th style="width: 110px;">NIK Kependudukan</th>
                    <th>Nama Pasien</th>
                    <th style="width: 40px;">JK</th>
                    <th style="width: 90px;">Tempat, Tgl Lahir</th>
                    <th style="width: 45px;">Usia</th>
                    <th style="width: 75px;">Pekerjaan</th>
                    <th style="width: 40px;">Gol</th>
                    <th style="width: 95px;">WhatsApp / HP</th>
                    <th>Alamat Domisili</th>
                    <th style="width: 95px;">Alergi / RPD</th>
                    <th style="width: 75px;">Tgl Daftar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                    <tr>
                        <td colspan="13" class="text-center py-4 text-muted font-italic">
                            Tidak ada data pasien yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = 1; 
                    foreach ($patients as $p): 
                        $ageStr = '-';
                        if (!empty($p->date_of_birth)) {
                            $dob = new \DateTime($p->date_of_birth);
                            $today = new \DateTime('today');
                            $ageStr = $dob->diff($today)->y . ' th';
                        }
                    ?>
                        <tr>
                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                            <td class="font-mono font-weight-bold text-center text-teal" style="color: #0d9488;"><?= esc($p->no_rm) ?></td>
                            <td class="font-mono text-center"><?= esc($p->nik ?: '-') ?></td>
                            <td class="font-weight-bold text-dark"><?= esc($p->name) ?></td>
                            <td class="text-center font-weight-bold <?= $p->gender === 'L' ? 'text-primary' : 'text-danger' ?>"><?= esc($p->gender) ?></td>
                            <td><?= esc($p->place_of_birth ?: '-') ?>, <?= $p->date_of_birth ? date('d/m/y', strtotime($p->date_of_birth)) : '-' ?></td>
                            <td class="text-center"><?= $ageStr ?></td>
                            <td><?= esc($p->occupation ?: '-') ?></td>
                            <td class="text-center font-weight-bold text-danger"><?= esc($p->blood_type ?: '-') ?></td>
                            <td><?= esc($p->phone ?: '-') ?></td>
                            <td style="font-size: 9.5px;"><?= esc($p->address ?: '-') ?></td>
                            <td style="font-size: 9.5px;">
                                <?php if (!empty($p->allergies)): ?>
                                    <strong class="text-danger">Alergi: <?= esc($p->allergies) ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center font-mono" style="font-size: 9.5px;"><?= $p->created_at ? date('d/m/Y', strtotime($p->created_at)) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- PENGESAHAN TANDA TANGAN -->
        <div class="row pt-3" style="page-break-inside: avoid;">
            <div class="col-6 text-center">
                <div class="text-muted" style="font-size: 11px;">Petugas Pengelola Rekam Medis,</div>
                <div style="height: 60px;"></div>
                <div class="font-weight-bold text-dark">( ............................................................ )</div>
                <small class="text-muted">NIP / ID Petugas</small>
            </div>
            <div class="col-6 text-center">
                <div class="text-muted" style="font-size: 11px;">Sumbawa Besar, <?= date('d F Y') ?><br>Penanggung Jawab / Pimpinan Klinik,</div>
                <div style="height: 60px;"></div>
                <div class="font-weight-bold text-dark"><?= esc(clinic_setting('clinic_head_doctor', 'dr. Gitria, Sp.KK')) ?></div>
                <small class="text-muted">SIP: <?= esc(clinic_setting('clinic_head_sip', '503/449/SIP-D/2022')) ?></small>
            </div>
        </div>
    </div>

</body>
</html>
