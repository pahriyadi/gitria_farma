<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pendaftaran Pasien Online - Sawamawa Medical Center') ?></title>
    
    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
        <link rel="shortcut icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --brand-primary: #0d9488;
            --brand-dark: #0f766e;
            --brand-light: #14b8a6;
            --brand-subtle: #f0fdf4;
            --dark-slate: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --radius-md: 10px;
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
            position: relative;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--text-main);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        /* Glass Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--dark-slate);
            letter-spacing: -0.4px;
        }

        /* Hero Header Banner */
        .reg-header-banner {
            background: linear-gradient(135deg, #042f2e 0%, #0f766e 100%);
            color: #ffffff;
            padding: 38px 0 52px;
            position: relative;
            overflow: hidden;
        }
        .reg-header-banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 24px;
            background: #f1f5f9;
            border-radius: 24px 24px 0 0;
        }

        /* Global Responsive Button Fix */
        .btn {
            white-space: normal !important;
            word-break: normal;
            max-width: 100%;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 11.5px;
            max-width: 100%;
            margin-bottom: 8px;
            line-height: 1.4;
            text-align: center;
            box-sizing: border-box;
            width: 100%;
        }
        .status-pill-open {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .status-pill-closed {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .status-pill .status-title {
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .status-pill .status-time {
            font-size: 11px;
            font-weight: 600;
            opacity: 0.9;
        }

        /* Main Registration Card */
        .reg-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            margin-top: -28px;
            margin-bottom: 50px;
            position: relative;
            z-index: 10;
        }

        /* Section Step Headers */
        .step-heading {
            font-size: 15px;
            font-weight: 800;
            color: var(--dark-slate);
            display: flex;
            align-items: center;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
            letter-spacing: -0.2px;
        }
        .step-num-badge {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #ccfbf1;
            color: var(--brand-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            margin-right: 10px;
        }

        /* Patient Category Interactive Cards */
        .patient-type-box {
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .patient-type-box:hover {
            border-color: var(--brand-light);
            background: #f0fdf4;
            transform: translateY(-2px);
        }
        .patient-type-box.active {
            border-color: var(--brand-primary);
            background: #f0fdf4;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.12);
        }

        /* Form Inputs Modern */
        .form-group label {
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control-custom {
            height: 46px;
            background-color: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--dark-slate);
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
            outline: none;
        }

        /* Visit Type Toggle Buttons */
        .btn-toggle-service {
            font-weight: 700;
            font-size: 13.5px;
            padding: 12px 18px;
            border-radius: 8px !important;
            border: 1.5px solid #cbd5e1;
            color: #475569;
            background: #f8fafc;
            transition: all 0.2s ease;
        }
        .btn-toggle-service:hover {
            background: #f0fdf4;
            border-color: var(--brand-primary);
            color: var(--brand-dark);
        }
        .btn-toggle-service.active {
            background: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.25);
        }

        /* Submit Button */
        .btn-brand-cta {
            height: 52px;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            border: none;
            border-radius: var(--radius-md);
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-brand-cta:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.45);
            color: #ffffff;
        }

        /* Footer */
        .footer-dark {
            background: var(--dark-slate);
            color: #94a3b8;
            padding: 40px 0 20px;
            font-size: 12.5px;
        }

        @media (max-width: 767px) {
            .reg-card { padding: 24px 18px; }
            .reg-header-banner { padding: 24px 0 40px; }
        }
    </style>
</head>
<body>

<!-- Navbar Header -->
<header class="navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= base_url() ?>" class="d-flex align-items-center text-decoration-none">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="height: 36px; max-width: 44px; object-fit: contain; margin-right: 10px;">
            <?php else: ?>
                <span class="text-teal mr-2" style="font-size: 24px; color: var(--brand-primary);"><i class="fas fa-hospital-user"></i></span>
            <?php endif; ?>
            <div>
                <span class="navbar-brand-title d-block"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span>
                <small class="text-muted d-none d-sm-block" style="font-size: 10.5px;">Pusat Pelayanan Medis Terpadu &amp; Rekam Medis Elektronik</small>
            </div>
        </a>
        <div class="d-flex align-items-center" style="gap: 8px;">
            <button type="button" class="btn btn-outline-success btn-sm font-weight-bold" data-toggle="modal" data-target="#modalDaftarWa">
                <i class="fab fa-whatsapp mr-1"></i> <span class="d-none d-md-inline">Daftar via</span> WhatsApp
            </button>
            <a href="<?= base_url('login') ?>" class="btn btn-outline-dark btn-sm font-weight-bold">
                <i class="fas fa-user-doctor mr-1"></i> Portal Nakes
            </a>
            <a href="<?= base_url() ?>" class="btn btn-light border btn-sm font-weight-bold d-none d-sm-inline-flex">
                <i class="fas fa-home mr-1"></i> Beranda
            </a>
        </div>
    </div>
</header>

<!-- Hero Header Banner -->
<div class="reg-header-banner">
    <div class="container text-center">
        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill mb-2" style="background: rgba(20, 184, 166, 0.2); border: 1px solid rgba(45, 212, 191, 0.4); color: #5eead4; font-size: 11.5px; font-weight: 700;">
            <i class="fas fa-shield-alt mr-1.5"></i> Terintegrasi SATUSEHAT Kemenkes RI &bull; Enkripsi 256-Bit SSL
        </div>
        <h2 class="font-weight-bold mb-1" style="letter-spacing: -0.5px; font-size: 28px;">Pendaftaran Antrean Berobat Online</h2>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 13.5px;">
            Reservasi nomor antrean mandiri dari rumah dengan mudah. Dapatkan <strong>Kartu Pasien Digital</strong> dan <strong>Estimasi Jam Pelayanan</strong> secara instan.
        </p>
    </div>
</div>

<!-- Main Container -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <!-- Banner Pendaftaran Tutup (Jika Di Luar Jam Operasional) -->
            <?php if (empty($isOnlineOpen)): ?>
                <div class="alert alert-warning border shadow-sm p-3 p-md-4 text-center mb-3" style="border-radius: 12px; background: #fffbeb; border-color: #fde68a !important;">
                    <i class="fas fa-clock text-warning fa-3x mb-2"></i>
                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: clamp(16px, 3.5vw, 18px);">Pendaftaran Online Mandiri Sedang Ditutup</h5>
                    <p class="text-secondary text-sm mb-3 mx-auto" style="max-width: 620px; font-size: 13px; line-height: 1.5;">
                        <?= esc($closedMessage ?? 'Pendaftaran online saat ini sedang ditutup di luar jam operasional.') ?>
                    </p>
                    <div class="d-flex justify-content-center flex-wrap" style="gap: 10px;">
                        <button type="button" class="btn btn-success font-weight-bold px-3 py-2 text-wrap" data-toggle="modal" data-target="#modalDaftarWa" style="max-width: 100%; line-height: 1.35;">
                            <i class="fab fa-whatsapp mr-1"></i> Buka Pendaftaran via WhatsApp
                        </button>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary font-weight-bold px-3 py-2 text-wrap" style="max-width: 100%; line-height: 1.35;">
                            <i class="fas fa-home mr-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Flash Error Notification -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert" style="border-left: 5px solid #dc2626; border-radius: 8px; font-size: 13.5px;">
                    <i class="fas fa-circle-exclamation mr-1.5 text-danger"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- REGISTRATION FORM CARD -->
            <div class="reg-card">
                
                <!-- Status Badge -->
                <div class="text-center mb-4 pb-3 border-bottom">
                    <?php if (!empty($isOnlineOpen)): ?>
                        <div class="status-pill status-pill-open">
                            <div class="status-title">
                                <i class="fas fa-circle mr-1 text-success" style="font-size: 8px;"></i> Pendaftaran Dibuka
                            </div>
                            <div class="status-time">
                                (<?= esc($openTime) ?> - <?= esc($closeTime) ?> WITA)
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="status-pill status-pill-closed">
                            <div class="status-title">
                                <i class="fas fa-ban mr-1 text-danger"></i> Pendaftaran Online &amp; WA Tutup
                            </div>
                            <div class="status-time">
                                (Buka <?= esc($openTime) ?> - <?= esc($closeTime) ?> WITA)
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Edukasi Alur Pendaftaran Pasien (Fast & Simple) -->
                <div class="card border-0 mb-4 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px solid #a7f3d0 !important;">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-start">
                            <div class="mr-3 text-teal d-none d-sm-block" style="font-size: 26px; color: #0d9488;">
                                <i class="fas fa-circle-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">
                                    <i class="fas fa-shield-halved mr-1 text-teal d-sm-none"></i> Alur Cepat Pendaftaran Pasien (Hanya 1 Menit):
                                </h6>
                                <p class="text-muted text-xs mb-2">Reservasi nomor antrean tanpa antre panjang dan tanpa ribet.</p>
                                <div class="row text-xs text-secondary mt-1" style="line-height: 1.45;">
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="p-2 rounded bg-white border h-100 shadow-2xs">
                                            <strong class="text-dark d-block mb-1"><span class="badge badge-teal px-1.5 py-0.5 mr-1" style="background:#0d9488; color:#fff;">1</span> Isi Data Pokok:</strong>
                                            Cukup isi NIK, Nama, &amp; No. WhatsApp dari ponsel Anda.
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2 mb-md-0">
                                        <div class="p-2 rounded bg-white border h-100 shadow-2xs">
                                            <strong class="text-dark d-block mb-1"><span class="badge badge-teal px-1.5 py-0.5 mr-1" style="background:#0d9488; color:#fff;">2</span> Dapatkan Tiket WA:</strong>
                                            Nomor antrean &amp; kartu pasien otomatis terkirim ke WhatsApp Anda.
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2 rounded bg-white border h-100 shadow-2xs">
                                            <strong class="text-dark d-block mb-1"><span class="badge badge-teal px-1.5 py-0.5 mr-1" style="background:#0d9488; color:#fff;">3</span> Datang ke Klinik:</strong>
                                            Tunjukkan tiket WhatsApp ke petugas pendaftaran untuk validasi &amp; konsultasi dokter.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('daftar-online') ?>" method="post" id="form-pendaftaran-online">
                    <?= csrf_field() ?>

                    <!-- STEP 1: PILIH KATEGORI PASIEN -->
                    <div class="step-heading">
                        <span class="step-num-badge">1</span>
                        <span>KATEGORI PASIEN</span>
                    </div>

                    <div class="row mb-4">
                        <!-- Opsi Pasien Baru -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="patient-type-box active" id="card-type-baru" onclick="selectPatientCategory('baru')">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="radio-baru" name="patient_type" value="baru" class="custom-control-input" checked>
                                    <label class="custom-control-label font-weight-bold text-dark" for="radio-baru" style="font-size: 14px;">
                                        <i class="fas fa-user-plus text-teal mr-1.5" style="color: var(--brand-primary);"></i> Pasien Baru
                                    </label>
                                </div>
                                <small class="text-secondary d-block mt-2" style="font-size: 12px; line-height: 1.45;">
                                    Saya <strong>belum pernah berobat</strong> atau belum memiliki Nomor Rekam Medis (No. RM) di Sawamawa Medical Center.
                                </small>
                            </div>
                        </div>

                        <!-- Opsi Pasien Lama -->
                        <div class="col-md-6">
                            <div class="patient-type-box" id="card-type-lama" onclick="selectPatientCategory('lama')">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="radio-lama" name="patient_type" value="lama" class="custom-control-input">
                                    <label class="custom-control-label font-weight-bold text-dark" for="radio-lama" style="font-size: 14px;">
                                        <i class="fas fa-id-card text-teal mr-1.5" style="color: var(--brand-primary);"></i> Pasien Lama (Sudah Terdaftar)
                                    </label>
                                </div>
                                <small class="text-secondary d-block mt-2" style="font-size: 12px; line-height: 1.45;">
                                    Saya <strong>sudah memiliki No. RM atau NIK</strong> terdaftar di klinik (Verifikasi instan tanpa isi ulang biodata).
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: DATA PASIEN BARU -->
                    <div id="section-pasien-baru">
                        <div class="step-heading">
                            <span class="step-num-badge">2</span>
                            <span>DATA IDENTITAS PASIEN BARU</span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nomor Induk Kependudukan (NIK KTP / KK) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" id="input-nik-baru" class="form-control form-control-custom font-weight-bold" placeholder="16 Digit NIK KTP / Kartu Keluarga" value="<?= old('nik') ?>" maxlength="16" minlength="16" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                                <small class="text-muted" style="font-size: 11px;">Digunakan untuk pembuatan No. Rekam Medis (RME) resmi.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="input-name-baru" class="form-control form-control-custom font-weight-bold" placeholder="Nama lengkap sesuai identitas KTP/KK" value="<?= old('name') ?>" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nomor WhatsApp / HP Aktif <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="input-phone-baru" class="form-control form-control-custom font-weight-bold" placeholder="Contoh: 081234567890" value="<?= old('phone') ?>" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                                <small class="text-muted" style="font-size: 11px;">Tiket nomor antrean &amp; kartu pasien digital akan dikirimkan ke nomor ini.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-control form-control-custom" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                                    <option value="L" <?= old('gender') === 'L' ? 'selected' : '' ?>>Laki-laki (L)</option>
                                    <option value="P" <?= old('gender') === 'P' ? 'selected' : '' ?>>Perempuan (P)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="place_of_birth" class="form-control form-control-custom" placeholder="Contoh: Sumbawa" value="<?= old('place_of_birth', 'Sumbawa') ?>" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" id="input-dob-baru" class="form-control form-control-custom" value="<?= old('date_of_birth', '2000-01-01') ?>" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label>Alamat Lengkap Domisili <span class="text-danger">*</span></label>
                                <input type="text" name="address" id="input-address-baru" class="form-control form-control-custom" placeholder="Nama Jalan / Kelurahan / Desa / RT RW..." value="<?= old('address') ?>" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Nomor BPJS (Opsional):</label>
                                <input type="text" name="bpjs_number" class="form-control form-control-custom" placeholder="Nomor Kartu BPJS (Jika Ada)" value="<?= old('bpjs_number') ?>" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: VERIFIKASI PASIEN LAMA -->
                    <div id="section-pasien-lama" style="display: none;">
                        <div class="step-heading">
                            <span class="step-num-badge">2</span>
                            <span>VERIFIKASI IDENTITAS PASIEN LAMA</span>
                        </div>

                        <div class="alert alert-light border p-3 text-xs mb-3 text-secondary" style="border-radius: 8px; background:#f0fdf4; border-color:#bbf7d0 !important;">
                            <i class="fas fa-lock text-teal mr-1"></i> <strong>Keamanan Data Rekam Medis:</strong> Masukkan Nomor RM / NIK dan Tanggal Lahir Anda untuk verifikasi identitas secara otomatis.
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-5 form-group">
                                <label>Nomor Rekam Medis (No. RM) / NIK: <span class="text-danger">*</span></label>
                                <input type="text" name="old_identifier" id="input-old-identifier" class="form-control form-control-custom font-weight-bold font-monospace" placeholder="Contoh: RM-000001 atau NIK 16 digit">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Tanggal Lahir: <span class="text-danger">*</span></label>
                                <input type="date" name="old_dob" id="input-old-dob" class="form-control form-control-custom font-weight-bold">
                            </div>
                            <div class="col-md-3 form-group">
                                <button type="button" class="btn btn-teal btn-block font-weight-bold" style="height: 46px; background:#0d9488; color:#ffffff;" onclick="verifyOldPatient()">
                                    <i class="fas fa-search mr-1"></i> Verifikasi Data
                                </button>
                            </div>
                        </div>

                        <!-- Card Hasil Verifikasi Pasien Lama -->
                        <div id="box-old-patient-verified" class="card p-3 mb-3" style="display: none; background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 8px;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="d-inline-flex align-items-center justify-content-center mr-3" style="background:#10b981; color:#ffffff; width:40px; height:40px; border-radius:50%; font-size:18px;">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <h6 class="font-weight-bold text-dark mb-0 mr-2" id="verified-name">Nama Pasien</h6>
                                            <span class="badge badge-dark font-weight-bold font-monospace" id="verified-rm">RM-XXXXXX</span>
                                        </div>
                                        <small class="text-secondary" id="verified-address">Alamat domisili terdaftar</small>
                                    </div>
                                </div>
                                <span class="badge badge-success px-2.5 py-1.5 font-weight-bold" style="font-size: 11px;">
                                    <i class="fas fa-shield-check mr-1"></i> Terverifikasi
                                </span>
                            </div>

                            <div class="row mt-2 pt-2 border-top">
                                <div class="col-md-6 form-group mb-0">
                                    <label class="text-xs font-weight-bold text-dark">Perbarui No. WhatsApp (Jika Berubah):</label>
                                    <input type="text" name="old_phone" id="verified-phone" class="form-control form-control-sm font-weight-bold" placeholder="Nomor WhatsApp Aktif">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: PILIHAN LAYANAN, DOKTER & JADWAL -->
                    <div class="step-heading mt-4">
                        <span class="step-num-badge">3</span>
                        <span>PILIHAN LAYANAN, DOKTER &amp; JADWAL KUNJUNGAN</span>
                    </div>

                    <div class="form-group mb-3">
                        <label class="d-block">Pilih Jenis Pelayanan Medis: <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-toggle-service active w-50" id="lbl-type-poli">
                                <input type="radio" name="visit_type" id="type-poli" value="poli" checked> 
                                <i class="fas fa-stethoscope mr-1"></i> 1. Poliklinik Dokter
                            </label>
                            <label class="btn btn-toggle-service w-50" id="lbl-type-tindakan">
                                <input type="radio" name="visit_type" id="type-tindakan" value="tindakan"> 
                                <i class="fas fa-syringe mr-1"></i> 2. Tindakan Medis Langsung
                            </label>
                        </div>
                    </div>

                    <!-- Pane Poliklinik & Dokter -->
                    <div id="pane-poli">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pilih Poliklinik Tujuan: <span class="text-danger">*</span></label>
                                <select name="polyclinic_id" id="select-poly" class="form-control form-control-custom font-weight-bold" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                                    <?php foreach ($polyclinics as $p): ?>
                                        <option value="<?= $p->id ?>" <?= old('polyclinic_id') == $p->id ? 'selected' : '' ?>>
                                            Poliklinik <?= esc($p->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Pilih Dokter Spesialis / Umum: <span class="text-danger">*</span></label>
                                <select name="doctor_id" id="select-doctor" class="form-control form-control-custom font-weight-bold" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?= $doc->id ?>" data-poly="<?= $doc->polyclinic_id ?>" <?= old('doctor_id') == $doc->id ? 'selected' : '' ?>>
                                            <?= esc($doc->name) ?> (<?= esc($doc->polyclinic_name ?? 'Umum') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pane Tindakan Langsung -->
                    <div id="pane-tindakan" style="display: none;">
                        <div class="form-group">
                            <label>Pilih Tindakan / Paket Medis: <span class="text-danger">*</span></label>
                            <select name="service_id" id="select-service" class="form-control form-control-custom font-weight-bold" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                                <option value="">-- Pilih Tindakan Medis --</option>
                                <?php foreach ($services as $srv): ?>
                                    <option value="<?= $srv->id ?>" <?= old('service_id') == $srv->id ? 'selected' : '' ?>>
                                        [<?= esc($srv->category_name ?? 'Medis') ?>] <?= esc($srv->name) ?> - Rp <?= number_format($srv->price, 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tanggal Rencana Kunjungan: <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control form-control-custom font-weight-bold" value="<?= old('visit_date', date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>" <?= empty($isOnlineOpen) ? 'disabled' : 'required' ?>>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Keluhan / Gejala Utama (Opsional):</label>
                            <input type="text" name="complaints" class="form-control form-control-custom" placeholder="Contoh: Demam 2 hari, batuk, kontrol tensi..." value="<?= old('complaints') ?>" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                        </div>
                    </div>

                    <div class="text-center mt-4 pt-2">
                        <?php if (!empty($isOnlineOpen)): ?>
                            <button type="submit" class="btn btn-brand-cta">
                                <i class="fas fa-ticket-alt mr-2"></i> Daftar Sekarang &amp; Dapatkan Nomor Antrean
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-block font-weight-bold py-3 text-wrap" disabled style="white-space: normal; line-height: 1.4;">
                                <i class="fas fa-ban mr-1"></i> Pendaftaran Online Sedang Ditutup
                            </button>
                        <?php endif; ?>
                        
                        <small class="text-muted d-block mt-2.5" style="font-size: 11.5px;">
                            <i class="fas fa-shield-halved text-success mr-1"></i> Data Anda dienkripsi 256-bit dan diproses sesuai standar privasi rekam medis nasional.
                        </small>
                    </div>
                </form>

                <!-- WhatsApp Booking Quick Option Box -->
                <div class="card p-3 mt-4" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="mb-2 mb-md-0">
                            <strong class="text-dark d-block" style="font-size: 13.5px;">
                                <i class="fab fa-whatsapp text-success mr-1"></i> Ingin Mendaftar Langsung via WhatsApp?
                            </strong>
                            <?php if (!empty($isOnlineOpen)): ?>
                                <small class="text-secondary">Pilih pendaftaran Pasien Baru atau Pasien Lama untuk diteruskan langsung ke WhatsApp Resepsionis.</small>
                            <?php else: ?>
                                <small class="text-danger font-weight-bold">Layanan pendaftaran WhatsApp saat ini juga sedang ditutup di luar jam operasional.</small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!empty($isOnlineOpen)): ?>
                                <button type="button" class="btn btn-success btn-sm font-weight-bold px-3 py-2" data-toggle="modal" data-target="#modalDaftarWa">
                                    <i class="fab fa-whatsapp mr-1"></i> Format Daftar WhatsApp
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-sm font-weight-bold px-3 py-2" data-toggle="modal" data-target="#modalDaftarWa">
                                    <i class="fas fa-clock mr-1"></i> Status: Tutup
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Footer with ISO & SATUSEHAT Logos -->
<footer class="footer-dark text-center">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center flex-wrap mb-3" style="gap: 10px;">
            <img src="<?= base_url('assets/images/iso-27001.svg') ?>" alt="ISO/IEC 27001" style="height: 30px; width: auto;">
            <img src="<?= base_url('assets/images/iso-27701.svg') ?>" alt="ISO/IEC 27701" style="height: 30px; width: auto;">
            <img src="<?= base_url('assets/images/iso-9001.svg') ?>" alt="ISO 9001:2015" style="height: 30px; width: auto;">
            <img src="<?= base_url('assets/images/satusehat-logo.svg') ?>" alt="SATUSEHAT" style="height: 30px; width: auto;">
            <img src="<?= base_url('assets/images/ssl-secure.svg') ?>" alt="256-Bit SSL" style="height: 30px; width: auto;">
        </div>
        <div class="text-xs mb-1" style="color: #94a3b8;">
            &copy; <?= date('Y') ?> <strong><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></strong>. Seluruh data rekam medis dilindungi kerahasiaannya.
        </div>
    </div>
</footer>

<!-- Modal Pendaftaran Berobat via WhatsApp -->
<div class="modal fade" id="modalDaftarWa" tabindex="-1" role="dialog" aria-labelledby="modalDaftarWaTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%);">
                <div>
                    <h5 class="modal-title font-weight-bold d-flex align-items-center mb-0" id="modalDaftarWaTitle">
                        <i class="fab fa-whatsapp fa-lg mr-2"></i> Pendaftaran Berobat via WhatsApp
                    </h5>
                    <small class="text-white-50">Layanan reservasi langsung ke WhatsApp Admin Sawamawa Medical Center</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <?php if (empty($isOnlineOpen)): ?>
                    <div class="alert alert-warning border shadow-none p-3 text-center mb-3" style="border-radius: 8px; background: #fffbeb; border-color: #fde68a !important;">
                        <i class="fas fa-clock text-warning fa-lg mr-1"></i> <strong class="text-dark">Layanan Pendaftaran Berobat via WhatsApp Sedang Ditutup</strong>
                        <div class="text-xs text-secondary mt-1"><?= esc($closedMessage) ?></div>
                        <div class="mt-2 text-xs font-weight-bold text-dark">
                            <i class="fas fa-hospital mr-1 text-teal"></i> Untuk kondisi darurat medis, silakan langsung menuju IGD Sawamawa Medical Center (Siaga 24 Jam).
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Pilihan Tipe Pasien: Baru vs Lama -->
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="card p-3 text-center border patient-type-select-wa" id="wa-type-baru" style="cursor: pointer; border-radius: 8px; border: 2px solid #0d9488 !important; background: #f0fdf4;">
                            <i class="fas fa-user-plus text-teal fa-2x mb-1" style="color: #0d9488;"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Pasien Baru</h6>
                            <small class="text-muted" style="font-size: 11px;">Belum punya No. Rekam Medis</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border patient-type-select-wa" id="wa-type-lama" style="cursor: pointer; border-radius: 8px; border: 2px solid #cbd5e1; background: #ffffff;">
                            <i class="fas fa-id-card text-secondary fa-2x mb-1"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Pasien Lama</h6>
                            <small class="text-muted" style="font-size: 11px;">Sudah punya No. RM / Pernah Berobat</small>
                        </div>
                    </div>
                </div>

                <!-- Form Fields Container -->
                <div class="card p-3 border rounded bg-white shadow-none" style="border: 1px solid #e2e8f0 !important;">
                    
                    <!-- 1. Fields Khusus Pasien Baru -->
                    <div id="wa-fields-baru">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" id="wa_new_name" class="form-control form-control-sm" placeholder="Contoh: Budi Santoso" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">NIK KTP (16 Digit) <span class="text-danger">*</span></label>
                                <input type="text" id="wa_new_nik" maxlength="16" class="form-control form-control-sm" placeholder="5204xxxxxxxxxxxx" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" id="wa_new_dob" class="form-control form-control-sm" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Jenis Kelamin</label>
                                <select id="wa_new_gender" class="form-control form-control-sm" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                                    <option value="Laki-laki">Laki-laki (L)</option>
                                    <option value="Perempuan">Perempuan (P)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">No. WhatsApp Pasien</label>
                                <input type="text" id="wa_new_phone" class="form-control form-control-sm" placeholder="0812xxxxxxxx" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="text-xs font-weight-bold text-dark">Alamat Domisili / Tempat Tinggal</label>
                            <input type="text" id="wa_new_address" class="form-control form-control-sm" placeholder="Jl. Kartini No. 5, Sumbawa Besar" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                        </div>
                    </div>

                    <!-- 2. Fields Khusus Pasien Lama -->
                    <div id="wa-fields-lama" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">Nomor Rekam Medis (No. RM) / NIK <span class="text-danger">*</span></label>
                                <input type="text" id="wa_old_rm" class="form-control form-control-sm" placeholder="RM-000123 atau NIK 16 digit" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                                <input type="text" id="wa_old_name" class="form-control form-control-sm" placeholder="Nama sesuai rekam medis" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">Tanggal Lahir Pasien <span class="text-danger">*</span></label>
                                <input type="date" id="wa_old_dob" class="form-control form-control-sm" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold text-dark">No. WhatsApp Pasien</label>
                                <input type="text" id="wa_old_phone" class="form-control form-control-sm" placeholder="0812xxxxxxxx" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pelayanan & Jadwal Kunjungan (Umum untuk Keduanya) -->
                    <hr class="my-2">
                    <div class="row mt-2">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Poliklinik / Tindakan yang Dituju <span class="text-danger">*</span></label>
                            <select id="wa_poly" class="form-control form-control-sm font-weight-bold" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                                <?php if (!empty($polyclinics)): foreach ($polyclinics as $p): ?>
                                    <option value="Poliklinik <?= esc($p->name) ?>">Poliklinik <?= esc($p->name) ?></option>
                                <?php endforeach; endif; ?>
                                <?php if (!empty($services)): foreach ($services as $srv): ?>
                                    <option value="Tindakan: <?= esc($srv->name) ?>">Tindakan: <?= esc($srv->name) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Rencana Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="date" id="wa_visit_date" class="form-control form-control-sm font-weight-bold" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" <?= empty($isOnlineOpen) ? 'disabled' : '' ?>>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-dark">Keluhan Utama / Gejala / Alasan Kunjungan</label>
                        <textarea id="wa_complaints" class="form-control form-control-sm" rows="2" placeholder="Tuliskan keluhan atau riwayat kontrol Anda..." <?= empty($isOnlineOpen) ? 'disabled' : '' ?>></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light btn-sm text-secondary font-weight-bold" data-dismiss="modal">
                    Tutup
                </button>
                <?php if (!empty($isOnlineOpen)): ?>
                    <button type="button" class="btn btn-success btn-md font-weight-bold px-4 shadow-sm" id="btn-submit-wa-booking">
                        <i class="fab fa-whatsapp mr-1"></i> Kirim Format Pendaftaran ke WhatsApp
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-md font-weight-bold px-4 shadow-sm" disabled>
                        <i class="fas fa-ban mr-1"></i> Pendaftaran Sedang Tutup
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Switch between Pasien Baru vs Pasien Lama
    function selectPatientCategory(type) {
        if (type === 'lama') {
            $('#radio-lama').prop('checked', true);
            $('#card-type-lama').addClass('active');
            $('#card-type-baru').removeClass('active');
            $('#section-pasien-baru').slideUp(200);
            $('#section-pasien-lama').slideDown(200);
            
            // Set required states
            $('#input-nik-baru, #input-name-baru, #input-phone-baru, #input-address-baru, #input-dob-baru').prop('required', false);
        } else {
            $('#radio-baru').prop('checked', true);
            $('#card-type-baru').addClass('active');
            $('#card-type-lama').removeClass('active');
            $('#section-pasien-lama').slideUp(200);
            $('#section-pasien-baru').slideDown(200);
            
            // Restore required states
            $('#input-nik-baru, #input-name-baru, #input-phone-baru, #input-address-baru, #input-dob-baru').prop('required', true);
        }
    }

    // 2. Switch Visit Type: Poli vs Tindakan
    $('input[name="visit_type"]').change(function() {
        if ($(this).val() === 'tindakan') {
            $('#pane-poli').slideUp(150);
            $('#pane-tindakan').slideDown(150);
            $('#lbl-type-tindakan').addClass('active');
            $('#lbl-type-poli').removeClass('active');
        } else {
            $('#pane-tindakan').slideUp(150);
            $('#pane-poli').slideDown(150);
            $('#lbl-type-poli').addClass('active');
            $('#lbl-type-tindakan').removeClass('active');
        }
    });

    // 3. Verify Existing Patient (AJAX)
    function verifyOldPatient() {
        const identifier = $('#input-old-identifier').val().trim();
        const dob = $('#input-old-dob').val();

        if (!identifier || !dob) {
            alert('Mohon masukkan Nomor Rekam Medis / NIK dan Tanggal Lahir Anda terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '<?= base_url('api/check-patient-public') ?>',
            type: 'POST',
            data: {
                identifier: identifier,
                dob: dob,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#verified-name').text(res.name);
                    $('#verified-rm').text(res.no_rm);
                    $('#verified-address').text(res.address || 'Alamat terdaftar');
                    $('#verified-phone').val(res.phone || '');
                    $('#box-old-patient-verified').slideDown(200);
                } else {
                    $('#box-old-patient-verified').slideUp(150);
                    alert(res.message || 'Data pasien tidak ditemukan.');
                }
            },
            error: function() {
                alert('Terjadi kendala saat memeriksa data. Silakan coba kembali.');
            }
        });
    }

    // 4. WhatsApp Registration Modal Handler
    const isOnlineOpen = <?= !empty($isOnlineOpen) ? 'true' : 'false' ?>;
    let currentWaType = 'baru';
    $('#wa-type-baru').click(function() {
        currentWaType = 'baru';
        $('.patient-type-select-wa').css({'border': '2px solid #cbd5e1', 'background': '#ffffff'});
        $(this).css({'border': '2px solid #0d9488 !important', 'background': '#f0fdf4'});
        $('#wa-fields-baru').slideDown(150);
        $('#wa-fields-lama').slideUp(150);
    });

    $('#wa-type-lama').click(function() {
        currentWaType = 'lama';
        $('.patient-type-select-wa').css({'border': '2px solid #cbd5e1', 'background': '#ffffff'});
        $(this).css({'border': '2px solid #0d9488 !important', 'background': '#f0fdf4'});
        $('#wa-fields-lama').slideDown(150);
        $('#wa-fields-baru').slideUp(150);
    });

    $('#btn-submit-wa-booking').click(function() {
        if (!isOnlineOpen) {
            alert('Mohon maaf, layanan pendaftaran online dan WhatsApp saat ini sedang ditutup.');
            return;
        }

        const adminPhone = '<?= clinic_setting('clinic_phone', '6281234567890') ?>'.replace(/[^0-9]/g, '');
        const targetPhone = adminPhone.startsWith('0') ? ('62' + adminPhone.substring(1)) : adminPhone;

        const poly = $('#wa_poly').val() || '-';
        const visitDate = $('#wa_visit_date').val() || '-';
        const complaints = $('#wa_complaints').val().trim() || '-';

        let msg = '';

        if (currentWaType === 'baru') {
            const name = $('#wa_new_name').val().trim();
            const nik = $('#wa_new_nik').val().trim();
            const dob = $('#wa_new_dob').val();
            const gender = $('#wa_new_gender').val();
            const phone = $('#wa_new_phone').val().trim();
            const address = $('#wa_new_address').val().trim();

            if (!name || !nik || !dob) {
                alert('Mohon lengkapi Nama Lengkap, NIK (16 digit), dan Tanggal Lahir Pasien.');
                return;
            }

            msg = `*PENDAFTARAN BEROBAT VIA WHATSAPP*\n` +
                  `*<?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?>*\n` +
                  `---------------------------------------\n` +
                  `• *Status Pasien:* PASIEN BARU\n` +
                  `• *Nama Lengkap:* ${name}\n` +
                  `• *NIK KTP:* ${nik}\n` +
                  `• *Tanggal Lahir:* ${dob}\n` +
                  `• *Jenis Kelamin:* ${gender}\n` +
                  `• *No. WhatsApp Pasien:* ${phone || '-'}\n` +
                  `• *Alamat Domisili:* ${address || '-'}\n` +
                  `• *Layanan / Poliklinik:* ${poly}\n` +
                  `• *Rencana Tanggal Kunjungan:* ${visitDate}\n` +
                  `• *Keluhan Utama:* ${complaints}\n` +
                  `---------------------------------------\n` +
                  `Mohon konfirmasi jadwal & nomor antrean kami. Terima kasih.`;
        } else {
            const rm = $('#wa_old_rm').val().trim();
            const name = $('#wa_old_name').val().trim();
            const dob = $('#wa_old_dob').val();
            const phone = $('#wa_old_phone').val().trim();

            if (!rm || !name || !dob) {
                alert('Mohon lengkapi No. RM / NIK, Nama Lengkap, dan Tanggal Lahir Pasien.');
                return;
            }

            msg = `*PENDAFTARAN BEROBAT VIA WHATSAPP*\n` +
                  `*<?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?>*\n` +
                  `---------------------------------------\n` +
                  `• *Status Pasien:* PASIEN LAMA\n` +
                  `• *No. Rekam Medis (RM) / NIK:* ${rm}\n` +
                  `• *Nama Lengkap:* ${name}\n` +
                  `• *Tanggal Lahir:* ${dob}\n` +
                  `• *No. WhatsApp Pasien:* ${phone || '-'}\n` +
                  `• *Layanan / Poliklinik:* ${poly}\n` +
                  `• *Rencana Tanggal Kunjungan:* ${visitDate}\n` +
                  `• *Keluhan / Catatan Kontrol:* ${complaints}\n` +
                  `---------------------------------------\n` +
                  `Mohon konfirmasi jadwal & nomor antrean kami. Terima kasih.`;
        }

        const waUrl = `https://api.whatsapp.com/send?phone=${targetPhone}&text=${encodeURIComponent(msg)}`;
        window.open(waUrl, '_blank');
        $('#modalDaftarWa').modal('hide');
    });
</script>

</body>
</html>
