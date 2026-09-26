<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sawamawa Medical Center - Klinik Pratama & Pelayanan Kesehatan Terpadu') ?></title>
    
    <!-- Meta SEO & Open Graph -->
    <meta name="description" content="Sawamawa Medical Center - Pusat Pelayanan Medis Terpadu, Poliklinik Dokter Spesialis, Farmasi E-Resep, Laboratorium, dan Resto Sehat di Sumbawa Besar.">
    <meta name="keywords" content="klinik sumbawa, dokter spesialis sumbawa, sawamawa medical center, daftar online klinik, rekam medis elektronik, satusehat">
    <meta name="author" content="Sawamawa Medical Center">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #14b8a6;
            --primary-subtle: #f0fdf4;
            --accent-mint: #ccfbf1;
            --dark-slate: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.03);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.02);
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 20px;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-main);
            background-color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--dark-slate);
            letter-spacing: -0.02em;
        }

        /* Top Announcement / Emergency Hotline Bar */
        .top-info-bar {
            background-color: #042f2e;
            color: #ccfbf1;
            font-size: 12.5px;
            padding: 7px 0;
            font-weight: 500;
            border-bottom: 1px solid rgba(20, 184, 166, 0.2);
        }
        .top-info-bar a {
            color: #5eead4;
            text-decoration: none;
        }
        .top-info-bar a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Navbar Header (Glassmorphism) */
        .navbar-main {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1040;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 19px;
            color: var(--dark-slate);
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }
        .navbar-brand img {
            height: 38px;
            max-width: 44px;
            object-fit: contain;
            margin-right: 10px;
        }
        .nav-link {
            font-weight: 600;
            font-size: 13.5px;
            color: #475569 !important;
            padding: 8px 14px !important;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-dark) !important;
            background-color: var(--primary-subtle);
        }

        /* Button Styling */
        .btn-brand-primary {
            background-color: var(--primary);
            color: #ffffff !important;
            border: 1px solid var(--primary);
            font-weight: 700;
            font-size: 13.5px;
            padding: 10px 22px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2);
            transition: all 0.2s ease;
        }
        .btn-brand-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(13, 148, 136, 0.3);
        }
        .btn-brand-outline {
            background-color: transparent;
            color: var(--primary-dark) !important;
            border: 1.5px solid var(--primary);
            font-weight: 700;
            font-size: 13.5px;
            padding: 9px 20px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-brand-outline:hover {
            background-color: var(--primary-subtle);
            border-color: var(--primary-dark);
        }

        /* Hero Section */
        .hero-section {
            padding: 70px 0 60px;
            background: radial-gradient(circle at 85% 15%, rgba(204, 251, 241, 0.45) 0%, rgba(255, 255, 255, 1) 70%);
            border-bottom: 1px solid var(--card-border);
            position: relative;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            background: #e6f4ea;
            color: #065f46;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 18px;
            border: 1px solid #a7f3d0;
        }
        .hero-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.18;
            color: var(--dark-slate);
            margin-bottom: 18px;
            letter-spacing: -0.03em;
        }
        .hero-title .text-gradient {
            background: linear-gradient(135deg, #0f766e 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.65;
            margin-bottom: 28px;
            max-width: 580px;
            font-weight: 400;
        }

        /* Quick Booking Card */
        .quick-booking-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-xl);
            padding: 28px;
            box-shadow: var(--shadow-xl);
            position: relative;
            z-index: 10;
        }

        /* Section Headings */
        .section-tag {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            background: var(--primary-subtle);
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        .section-title {
            font-size: 30px;
            font-weight: 800;
            color: var(--dark-slate);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }
        .section-desc {
            font-size: 14.5px;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto;
        }

        /* Service Cards */
        .service-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 24px;
            height: 100%;
            transition: all 0.25s ease;
        }
        .service-card:hover {
            border-color: var(--primary-light);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .service-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }

        /* Doctor Cards */
        .doctor-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
        }
        .doctor-card:hover {
            border-color: var(--primary-light);
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }
        .doctor-avatar-box {
            height: 160px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ccfbf1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Article / News Card */
        .article-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .article-card:hover {
            border-color: var(--primary-light);
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        .article-thumb {
            height: 180px;
            width: 100%;
            object-fit: cover;
            background: #f1f5f9;
        }
        .article-thumb-fallback {
            height: 180px;
            background: linear-gradient(135deg, #0d9488 0%, #0369a1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 42px;
        }
        .article-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .article-cat-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            background: #e0f2fe;
            color: #0369a1;
            display: inline-block;
            margin-bottom: 10px;
        }
        .article-title {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            color: var(--dark-slate);
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .article-summary {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }
        .article-meta {
            font-size: 11.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Step Guide Item */
        .step-item-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 22px;
            position: relative;
            height: 100%;
        }
        .step-number-badge {
            width: 38px;
            height: 38px;
            background: var(--primary);
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 14px;
        }

        /* Footer */
        .footer-main {
            background-color: var(--dark-slate);
            color: #94a3b8;
            padding: 60px 0 25px;
            font-size: 13.5px;
        }
        .footer-main h5 {
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .footer-main a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-main a:hover {
            color: #ffffff;
        }

        /* Floating WhatsApp Button */
        .floating-wa-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background-color: #25d366;
            color: #ffffff !important;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
            z-index: 1030;
            transition: transform 0.2s ease;
        }
        .floating-wa-btn:hover {
            transform: scale(1.08);
            color: #ffffff;
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 32px; }
            .hero-section { padding: 40px 0 40px; }
        }
    </style>
</head>
<body>

<!-- Top Announcement & Hotline -->
<div class="top-info-bar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="badge badge-success mr-2 font-weight-bold" style="font-size: 10.5px; background: #059669;">IGD &amp; FARMASI 24 JAM</span>
            <span><i class="fas fa-location-dot mr-1"></i> <?= esc(clinic_setting('clinic_address', 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB')) ?></span>
        </div>
        <div class="d-none d-md-flex align-items-center" style="gap: 15px;">
            <span><i class="fab fa-whatsapp mr-1"></i> Hotline: <a href="https://api.whatsapp.com/send?phone=<?= clinic_setting('clinic_phone', '6281234567890') ?>" target="_blank"><?= esc(clinic_setting('clinic_phone', '(0371) 23456')) ?></a></span>
            <span><i class="fas fa-shield-alt mr-1"></i> Terintegrasi SATUSEHAT Kemenkes RI</span>
        </div>
    </div>
</div>

<!-- Glass Navbar Header -->
<nav class="navbar navbar-expand-lg navbar-light navbar-main">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo">
            <?php else: ?>
                <span class="text-teal mr-2" style="font-size: 24px;"><i class="fas fa-hospital-user"></i></span>
            <?php endif; ?>
            <span><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ml-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#layanan">Layanan Medis</a></li>
                <li class="nav-item"><a class="nav-link" href="#dokter">Dokter</a></li>
                <li class="nav-item"><a class="nav-link" href="#artikel">Artikel &amp; Berita</a></li>
                <li class="nav-item"><a class="nav-link" href="#cara-daftar">Alur Daftar</a></li>
                <li class="nav-item"><a class="nav-link" href="#tarif">Tarif</a></li>
                <li class="nav-item ml-lg-2 mt-2 mt-lg-0">
                    <a href="<?= base_url('login') ?>" class="btn btn-brand-outline btn-sm font-weight-bold">
                        <i class="fas fa-user-doctor mr-1"></i> Portal Nakes
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- 1. Hero Section -->
<section class="hero-section" id="beranda">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fas fa-shield-halved mr-1.5 text-teal"></i> Pusat Layanan Medis Terpadu &amp; Terpercaya Sumbawa
                </div>
                <h1 class="hero-title">
                    Kesehatan Anda Prioritas Kami, <br>
                    <span class="text-gradient">Pelayanan Ramah &amp; Modern</span>.
                </h1>
                <p class="hero-subtitle">
                    Klinik Pratama rawat jalan dengan dokter spesialis &amp; umum, instalasi farmasi e-resep otomatis, laboratorium diagnostik akurat, dan resto nutrisi sehat terpadu.
                </p>
                <div class="d-flex flex-wrap align-items-center mb-4" style="gap: 10px;">
                    <?php if (!empty($isOnlineOpen)): ?>
                        <a href="<?= base_url('daftar-online') ?>" class="btn btn-brand-primary btn-lg font-weight-bold">
                            <i class="fas fa-ticket-alt mr-1"></i> Daftar Antrean Online
                        </a>
                        <button type="button" class="btn btn-brand-outline btn-lg font-weight-bold" data-toggle="modal" data-target="#modalDaftarWa">
                            <i class="fab fa-whatsapp mr-1 text-success"></i> Daftar via WhatsApp
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-brand-outline btn-lg font-weight-bold" data-toggle="modal" data-target="#modalDaftarWa">
                            <i class="fas fa-clock mr-1 text-warning"></i> Status Pendaftaran: Tutup
                        </button>
                        <a href="#dokter" class="btn btn-brand-primary btn-lg font-weight-bold">
                            <i class="fas fa-user-md mr-1"></i> Cek Jadwal Dokter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="row pt-3 border-top" style="border-top-color: var(--card-border) !important;">
                    <div class="col-4">
                        <div class="h5 font-weight-bold mb-0 text-dark">24 Jam</div>
                        <small class="text-muted" style="font-size: 11.5px;">Siaga Medis &amp; IGD</small>
                    </div>
                    <div class="col-4">
                        <div class="h5 font-weight-bold mb-0 text-dark"><?= count($doctors) ?>+ Dokter</div>
                        <small class="text-muted" style="font-size: 11.5px;">Spesialis &amp; Umum</small>
                    </div>
                    <div class="col-4">
                        <div class="h5 font-weight-bold mb-0 text-teal">SATUSEHAT</div>
                        <small class="text-muted" style="font-size: 11.5px;">Standar Kemenkes RI</small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Reservation Card -->
            <div class="col-lg-5 text-center mt-5 mt-lg-0">
                <div class="quick-booking-card">
                    <div class="text-center mb-3">
                        <?php if (!empty($isOnlineOpen)): ?>
                            <span class="badge badge-success px-3 py-1 font-weight-bold text-uppercase" style="font-size: 11px;">
                                <i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Pendaftaran Dibuka (<?= esc($openTime) ?> - <?= esc($closeTime) ?> WITA)
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger text-white px-3 py-1 font-weight-bold text-uppercase" style="font-size: 11px;">
                                <i class="fas fa-ban mr-1"></i> Pendaftaran Online &amp; WA Tutup (Buka <?= esc($openTime) ?> - <?= esc($closeTime) ?> WITA)
                            </span>
                        <?php endif; ?>
                        
                        <h4 class="mt-2 mb-1" style="font-size: 19px;">Reservasi Antrean Cepat</h4>
                        <small class="text-muted">Pilih poliklinik dan tanggal untuk mendapatkan kartu pasien digital</small>
                    </div>

                    <?php if (!empty($isOnlineOpen)): ?>
                        <form action="<?= base_url('daftar-online') ?>" method="get">
                            <div class="form-group text-left">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Pilih Poliklinik Tujuan:</label>
                                <select name="polyclinic_id" class="form-control form-control-lg font-weight-bold" style="font-size: 13.5px; border-radius: 8px;">
                                    <?php foreach ($polyclinics as $p): ?>
                                        <option value="<?= $p->id ?>">Poliklinik <?= esc($p->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group text-left">
                                <label class="font-weight-bold text-xs text-uppercase text-muted">Rencana Tanggal Kunjungan:</label>
                                <input type="date" name="visit_date" class="form-control form-control-lg font-weight-bold" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" style="font-size: 13.5px; border-radius: 8px;">
                            </div>
                            <button type="submit" class="btn btn-brand-primary btn-block font-weight-bold py-2.5 mt-3" style="font-size: 14.5px;">
                                <i class="fas fa-arrow-right mr-1"></i> Lanjutkan Pendaftaran
                            </button>
                        </form>
                        <div class="mt-3 text-center">
                            <small class="text-muted">Pasien lama cukup masukkan <strong>No. RM / NIK</strong> untuk verifikasi instan.</small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border text-left p-3 my-3 text-xs text-muted" style="border-radius: 8px;">
                            <i class="fas fa-info-circle text-teal mr-1"></i> <?= esc($closedMessage) ?>
                        </div>
                        <button type="button" class="btn btn-secondary btn-block font-weight-bold py-2 mt-2" data-toggle="modal" data-target="#modalDaftarWa">
                            <i class="fas fa-clock mr-1"></i> Pendaftaran Online &amp; WA Sedang Tutup
                        </button>
                        <div class="mt-3 text-center">
                            <small class="text-muted">Untuk kondisi darurat, silakan langsung menuju <strong>IGD Sawamawa Medical Center (24 Jam)</strong>.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Layanan Medis Terpadu -->
<section class="py-5" id="layanan" style="background: #ffffff; border-bottom: 1px solid var(--card-border);">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="section-tag">Fasilitas &amp; Layanan</span>
            <h2 class="section-title">Layanan Medis Terintegrasi Modern</h2>
            <p class="section-desc">Pusat kesehatan komprehensif didukung fasilitas mutakhir dan tenaga medis berdedikasi tinggi.</p>
        </div>

        <div class="row">
            <!-- 1. Poliklinik -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#ccfbf1; color:#0f766e;">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Poliklinik Spesialis &amp; Umum</h5>
                    <p class="text-muted text-xs mb-0">Pelayanan rawat jalan dokter spesialis penyakit dalam, anak, gigi, dan umum dengan rekam medis digital terintegrasi.</p>
                </div>
            </div>
            <!-- 2. Farmasi E-Resep -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#e0e7ff; color:#4338ca;">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Instalasi Farmasi E-Resep</h5>
                    <p class="text-muted text-xs mb-0">Resep obat digital langsung terhubung dari ruang dokter ke instalasi farmasi. Cepat, tepat dosis, dan bebas antre lama.</p>
                </div>
            </div>
            <!-- 3. Laboratorium -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#fee2e2; color:#b91c1c;">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Laboratorium Diagnostik</h5>
                    <p class="text-muted text-xs mb-0">Pemeriksaan darah lengkap, tes urin, profil lipid, gula darah, dan uji diagnostik akurat standar Kemenkes RI.</p>
                </div>
            </div>
            <!-- 4. Rawat Inap & Observasi -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#fef3c7; color:#b45309;">
                        <i class="fas fa-bed-pulse"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Rawat Inap &amp; Observasi</h5>
                    <p class="text-muted text-xs mb-0">Ruang observasi dan rawat inap berstandar higienis dengan monitoring tanda vital perawat 24 jam.</p>
                </div>
            </div>
            <!-- 5. Resto Gizi Sehat -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#dcfce7; color:#15803d;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Resto Gizi &amp; Diet Medis</h5>
                    <p class="text-muted text-xs mb-0">Penyediaan menu bergizi terukur oleh ahli gizi klinis untuk mendukung pemulihan pasien dan santapan keluarga.</p>
                </div>
            </div>
            <!-- 6. IGD 24 Jam & Homecare -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="service-card">
                    <div class="service-icon-box" style="background:#f3e8ff; color:#7e22ce;">
                        <i class="fas fa-truck-medical"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">IGD 24 Jam &amp; Homecare</h5>
                    <p class="text-muted text-xs mb-0">Penanganan kegawatdaruratan medis darurat 24 jam setiap hari serta layanan perawat homecare ke rumah pasien.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Jadwal Dokter Spesialis & Umum -->
<section class="py-5" id="dokter" style="background: var(--bg-light); border-bottom: 1px solid var(--card-border);">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="section-tag">Tim Medis Profesional</span>
            <h2 class="section-title">Jadwal Praktek Dokter</h2>
            <p class="section-desc">Dokter spesialis dan dokter umum bersertifikat resmi Surat Izin Praktik (SIP) siap memberikan pelayanan medis prima.</p>
        </div>

        <div class="row">
            <?php if (!empty($doctors)): foreach ($doctors as $doc): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-avatar-box">
                            <div class="text-center">
                                <span class="text-teal" style="font-size: 52px;"><i class="fas fa-user-doctor"></i></span>
                            </div>
                        </div>
                        <div class="p-3 text-center">
                            <span class="badge badge-subtle-teal font-weight-bold text-xs mb-1" style="background:#ccfbf1; color:#0f766e;">
                                <?= esc($doc->polyclinic_name ?? 'Poliklinik') ?>
                            </span>
                            <h6 class="font-weight-bold text-dark mb-1" style="font-size: 14.5px;"><?= esc($doc->name ?? 'Dokter') ?></h6>
                            <small class="text-muted d-block text-xs mb-2">SIP: <?= esc($doc->sip_number ?? '445/SIP/DINKES/2024') ?></small>
                            <div class="pt-2 border-top text-xs text-secondary">
                                <i class="fas fa-clock text-teal mr-1"></i> <?= esc($doc->schedule_days ?? 'Senin - Sabtu') ?> <br>
                                <span class="font-weight-bold text-dark"><?= esc($doc->schedule_time ?? '08:00 - 14:00 WITA') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center text-muted py-4">Data dokter sedang dimutakhirkan.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 4. 📰 SECTION ARTIKEL & EDUKASI KESEHATAN (CMS Terintegrasi) -->
<section class="py-5" id="artikel" style="background: #ffffff; border-bottom: 1px solid var(--card-border);">
    <div class="container py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="section-tag">Wawasan &amp; Tips Medis</span>
                <h2 class="section-title mb-1">Artikel &amp; Edukasi Kesehatan</h2>
                <p class="section-desc text-left m-0">Informasi kesehatan terpercaya, panduan pencegahan penyakit, dan kabar terbaru dari Sawamawa Medical Center.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= base_url('berita') ?>" class="btn btn-brand-outline btn-sm font-weight-bold">
                    <i class="fas fa-list mr-1"></i> Semua Artikel
                </a>
            </div>
        </div>

        <div class="row">
            <?php if (!empty($articles)): foreach ($articles as $art): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="article-card">
                        <?php if (!empty($art->image_url) && file_exists(FCPATH . $art->image_url)): ?>
                            <img src="<?= base_url($art->image_url) ?>" alt="<?= esc($art->title) ?>" class="article-thumb">
                        <?php else: ?>
                            <div class="article-thumb-fallback">
                                <i class="fas fa-heart-pulse"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="article-body">
                            <div>
                                <span class="article-cat-badge"><?= esc($art->category) ?></span>
                            </div>
                            <h5 class="article-title" title="<?= esc($art->title) ?>">
                                <?= esc($art->title) ?>
                            </h5>
                            <p class="article-summary">
                                <?= esc($art->summary) ?>
                            </p>
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-teal btn-sm btn-block font-weight-bold btn-read-article" data-slug="<?= esc($art->slug) ?>">
                                    <i class="fas fa-book-open mr-1"></i> Baca Selengkapnya
                                </button>
                            </div>
                            <div class="article-meta">
                                <span><i class="fas fa-user-doctor mr-1"></i> <?= esc($art->author) ?></span>
                                <span><i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($art->created_at)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12 text-center text-muted py-4">Belum ada artikel yang diterbitkan.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 5. Alur Pelayanan Pasien (4 Langkah Mudah) -->
<section class="py-5" id="cara-daftar" style="background: var(--bg-light); border-bottom: 1px solid var(--card-border);">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="section-tag">Alur Pelayanan Pasien</span>
            <h2 class="section-title">Panduan 4 Langkah Pendaftaran Berobat</h2>
            <p class="section-desc">Kemudahan pendaftaran mandiri dari rumah untuk pasien baru maupun pasien lama yang telah memiliki Nomor Rekam Medis (No. RM).</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="step-item-card">
                    <div class="step-number-badge">1</div>
                    <h6 class="font-weight-bold text-dark mb-1">Daftar Mandiri</h6>
                    <p class="text-muted text-xs mb-0">Daftar melalui formulir online di website ini atau lewat Kiosk APM Mandiri saat tiba di klinik.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="step-item-card">
                    <div class="step-number-badge">2</div>
                    <h6 class="font-weight-bold text-dark mb-1">Nomor Antrean &amp; QR</h6>
                    <p class="text-muted text-xs mb-0">Dapatkan tiket nomor antrean digital lengkap dengan estimasi jam pelayanan dokter.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="step-item-card">
                    <div class="step-number-badge">3</div>
                    <h6 class="font-weight-bold text-dark mb-1">Pemeriksaan Medis</h6>
                    <p class="text-muted text-xs mb-0">Skrining tanda vital oleh perawat, dilanjutkan konsultasi &amp; tindakan medis oleh dokter.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3">
                <div class="step-item-card">
                    <div class="step-number-badge">4</div>
                    <h6 class="font-weight-bold text-dark mb-1">E-Resep &amp; Kasir</h6>
                    <p class="text-muted text-xs mb-0">Resep otomatis tersalurkan ke Instalasi Farmasi. Pembayaran mudah via Tunai, QRIS, atau Kartu Debit.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. Transparansi Tarif Layanan -->
<section class="py-5" id="tarif" style="background: #ffffff; border-bottom: 1px solid var(--card-border);">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="section-tag">Transparansi Biaya</span>
            <h2 class="section-title">Katalog Tarif Layanan &amp; Tindakan</h2>
            <p class="section-desc">Biaya pelayanan terstandar, transparan, dan terjangkau untuk seluruh masyarakat.</p>
        </div>

        <div class="card border shadow-sm" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-xs font-weight-bold text-uppercase">Nama Tindakan / Layanan</th>
                            <th class="py-3 px-4 text-xs font-weight-bold text-uppercase">Kategori</th>
                            <th class="py-3 px-4 text-xs font-weight-bold text-uppercase text-right">Tarif Pasien Umum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)): foreach ($services as $s): ?>
                            <tr>
                                <td class="py-3 px-4 font-weight-bold text-dark"><?= esc($s->name) ?></td>
                                <td class="py-3 px-4"><span class="badge badge-light border text-xs"><?= esc($s->category_name ?? 'Pelayanan Medis') ?></span></td>
                                <td class="py-3 px-4 font-weight-bold text-teal text-right">Rp <?= number_format($s->price, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Katalog tarif dapat dilihat langsung di meja kasir.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Floating WhatsApp Widget -->
<a href="https://api.whatsapp.com/send?phone=<?= clinic_setting('clinic_phone', '6281234567890') ?>&text=Halo%20Admin%20Sawamawa%20Medical%20Center,%20saya%20ingin%20berkonsultasi%20mengenai%20layanan%20medis." target="_blank" class="floating-wa-btn" title="Chat WhatsApp Admin">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- 7. Footer Elegan Deep Slate -->
<footer class="footer-main">
    <div class="container">
        <div class="row pb-4 border-bottom" style="border-color: #334155 !important;">
            <div class="col-md-5 mb-4 mb-md-0">
                <div class="h6 text-white font-weight-bold mb-2"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
                <p class="text-xs" style="color: #94a3b8; max-width: 380px;">Klinik Pratama Rawat Jalan, Pelayanan Farmasi e-Resep, Laboratorium Diagnostik, dan Resto Gizi Terpadu Sumbawa Besar, NTB.</p>
                <div class="mt-3">
                    <span class="badge badge-dark border py-2 px-3 text-teal font-weight-bold" style="background:#1e293b; border-color:#334155 !important; color:#2dd4bf !important;">
                        <i class="fas fa-shield-alt mr-1"></i> Terintegrasi SATUSEHAT Kemenkes RI
                    </span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <h5>Tautan Cepat</h5>
                <ul class="list-unstyled text-xs">
                    <li class="mb-2"><a href="<?= base_url('daftar-online') ?>">Daftar Antrean Online</a></li>
                    <li class="mb-2"><a href="<?= base_url('berita') ?>">Pusat Edukasi &amp; Berita</a></li>
                    <li class="mb-2"><a href="#cara-daftar">Panduan Pendaftaran</a></li>
                    <li class="mb-2"><a href="#dokter">Jadwal Praktek Dokter</a></li>
                    <li class="mb-2"><a href="#tarif">Katalog Tarif Layanan</a></li>
                    <li class="mb-2"><a href="<?= base_url('login') ?>">Portal Login Nakes</a></li>
                </ul>
            </div>
            <div class="col-md-4 col-6">
                <h5>Kontak Resmi</h5>
                <p class="text-xs mb-1 text-white font-weight-bold"><i class="fab fa-whatsapp mr-1 text-teal"></i> <?= esc(clinic_setting('clinic_phone', '0812-3456-7890')) ?></p>
                <p class="text-xs mb-1 text-white"><i class="fas fa-envelope mr-1 text-teal"></i> <?= esc(clinic_setting('clinic_email', 'info@sawamawamedicalcenter.id')) ?></p>
                <p class="text-xs mt-2" style="color: #94a3b8;"><?= esc(clinic_setting('clinic_address', 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB')) ?></p>
            </div>
        </div>

        <!-- ISO & Standard Keamanan Data Pasien Ribbon (Dengan Logo Gambar Resmi) -->
        <div class="row pt-4 pb-3 mt-4 border-top align-items-center" style="border-color: #334155 !important;">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <strong class="text-white d-block text-xs mb-1 font-weight-bold">
                    <i class="fas fa-shield-halved text-teal mr-1"></i> Standar Kualitas &amp; Keamanan Data:
                </strong>
                <p class="text-xs mb-0" style="color: #94a3b8; font-size: 11px;">
                    Seluruh data rekam medis elektronik (RME), identitas pasien, dan resep farmasi dienkripsi dengan standar internasional ISO &amp; Kemenkes RI.
                </p>
            </div>
            <div class="col-lg-8">
                <div class="d-flex flex-wrap justify-content-lg-end align-items-center" style="gap: 10px;">
                    <img src="<?= base_url('assets/images/iso-27001.svg') ?>" alt="ISO/IEC 27001 ISMS Certified" title="ISO/IEC 27001 - Sistem Manajemen Keamanan Informasi" style="height: 38px; width: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="<?= base_url('assets/images/iso-27701.svg') ?>" alt="ISO/IEC 27701 PIMS Certified" title="ISO/IEC 27701 - Perlindungan Privasi Data Pribadi Pasien" style="height: 38px; width: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="<?= base_url('assets/images/iso-9001.svg') ?>" alt="ISO 9001:2015 Quality Management" title="ISO 9001:2015 - Sistem Manajemen Mutu Layanan" style="height: 38px; width: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="<?= base_url('assets/images/satusehat-logo.svg') ?>" alt="SATUSEHAT Kemenkes RI" title="SATUSEHAT Kemenkes RI - Interoperabilitas RME Nasional" style="height: 38px; width: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="<?= base_url('assets/images/ssl-secure.svg') ?>" alt="256-Bit SSL TLS 1.3 Encryption" title="256-Bit SSL TLS 1.3 End-to-End Encryption" style="height: 38px; width: auto; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
            </div>
        </div>

        <div class="pt-3 text-center text-xs" style="color: #64748b;">
            &copy; 2026 PT. ARM ERA CORPORAT &mdash; <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?>. All rights reserved.
        </div>
    </div>
</footer>

<!-- Modal Pendaftaran Berobat via WhatsApp -->
<div class="modal fade" id="modalDaftarWa" tabindex="-1" role="dialog" aria-labelledby="modalDaftarWaTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
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
                            <i class="fas fa-user-plus text-teal fa-2x mb-1"></i>
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

<!-- Modal Quick Article Reader -->
<div class="modal fade" id="modalArticleReader" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white p-3" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
                <div class="d-flex align-items-center">
                    <span class="badge badge-light text-teal font-weight-bold text-xs mr-2" id="reader_category">Kesehatan</span>
                    <small class="text-white-50" id="reader_date">Tanggal</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-white" style="font-size: 15px; line-height: 1.75; color: #334155;">
                <h3 class="font-weight-bold text-dark mb-2" id="reader_title" style="font-size: 22px; line-height: 1.35;">Judul Artikel</h3>
                <div class="d-flex align-items-center text-xs text-muted mb-4 pb-2 border-bottom">
                    <span class="mr-3"><i class="fas fa-user-doctor text-teal mr-1"></i> <strong id="reader_author" class="text-dark">Penulis</strong></span>
                    <span><i class="fas fa-eye mr-1"></i> <span id="reader_views">0</span> pembaca</span>
                </div>
                <div id="reader_image_box" class="mb-4 text-center" style="display: none;">
                    <img id="reader_image" src="" alt="Cover" class="img-fluid rounded" style="max-height: 320px; width: 100%; object-fit: cover;">
                </div>
                <div id="reader_content">
                    <!-- Konten artikel akan dimasukkan di sini via JavaScript -->
                </div>
            </div>
            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-teal btn-sm font-weight-bold" id="btn-share-article-wa">
                    <i class="fab fa-whatsapp mr-1"></i> Bagikan Artikel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 4 & jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        const isOnlineOpen = <?= !empty($isOnlineOpen) ? 'true' : 'false' ?>;
        let currentWaType = 'baru'; // 'baru' atau 'lama'
        let currentArticleTitle = '';
        let currentArticleSlug = '';

        // Switch Pasien Baru vs Lama
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

        // Submit WhatsApp Booking Generator
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

        // Quick Article Reader Trigger
        $('.btn-read-article').click(function() {
            const slug = $(this).data('slug');
            if (!slug) return;

            $.ajax({
                url: '<?= base_url('berita/') ?>' + slug,
                type: 'GET',
                dataType: 'json',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                success: function(res) {
                    if (res.status === 'success' && res.article) {
                        const art = res.article;
                        currentArticleTitle = art.title;
                        currentArticleSlug = slug;

                        $('#reader_title').text(art.title);
                        $('#reader_category').text(art.category);
                        $('#reader_author').text(art.author);
                        $('#reader_date').text(art.created_at);
                        $('#reader_views').text(art.views);
                        $('#reader_content').html(art.content);

                        if (art.image_url) {
                            $('#reader_image').attr('src', art.image_url);
                            $('#reader_image_box').show();
                        } else {
                            $('#reader_image_box').hide();
                        }

                        $('#modalArticleReader').modal('show');
                    } else {
                        window.location.href = '<?= base_url('berita/') ?>' + slug;
                    }
                },
                error: function() {
                    window.location.href = '<?= base_url('berita/') ?>' + slug;
                }
            });
        });

        // Share Article to WhatsApp
        $('#btn-share-article-wa').click(function() {
            const shareUrl = window.location.origin + '<?= base_url('berita/') ?>' + currentArticleSlug;
            const text = `*${currentArticleTitle}*\nBaca selengkapnya artikel kesehatan dari Sawamawa Medical Center di:\n${shareUrl}`;
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
        });
    });
</script>

</body>
</html>
