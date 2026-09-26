<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Layar Display TV Antrean & Informasi - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></title>

    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
        <link rel="shortcut icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary: #00875a;
            --primary-dark: #004d33;
            --teal-accent: #20c997;
            --accent-gold: #ffab00;
            --bg-dark: #06152b;
            --card-dark: #0b2240;
            --card-glass: rgba(14, 39, 73, 0.88);
            --border-glass: rgba(32, 201, 151, 0.25);
            --text-light: #ffffff;
        }

        * {
            box-sizing: border-box;
            user-select: none;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #0e315b 0%, #06152b 100%);
            color: var(--text-light);
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 1. TOP HEADER BAR */
        .tv-header {
            background: linear-gradient(90deg, #003624 0%, #006644 50%, #004d33 100%);
            padding: 8px 24px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.5);
            border-bottom: 2px solid rgba(32, 201, 151, 0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 68px;
            flex-shrink: 0;
            z-index: 100;
        }
        .clinic-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.1;
        }
        .clinic-tagline {
            font-size: 12px;
            color: #b3ffdf;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .digital-clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 800;
            color: #fffae6;
            background: rgba(0,0,0,0.35);
            padding: 2px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            letter-spacing: 1px;
        }

        /* 2. MAIN TV CONTAINER (SPLIT SCREEN: ~63% IKLAN & PROMO / ~37% ANTREAN) */
        .tv-main-container {
            flex: 1;
            display: flex;
            padding: 12px 18px;
            gap: 16px;
            overflow: hidden;
        }

        /* LEFT: BIG ADVERTISING & HEALTH INFO SCREEN (~63%) */
        .media-ad-column {
            flex: 1.7;
            display: flex;
            flex-direction: column;
            gap: 10px;
            height: 100%;
        }

        .ad-video-wrapper {
            flex: 1;
            background: #000000;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid rgba(32, 201, 151, 0.35);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ad-video-wrapper iframe, 
        .ad-video-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        /* Slideshow Banner Promo */
        .ad-slide {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.8s ease-in-out;
        }
        .ad-slide.active {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .slide-overlay-content {
            background: linear-gradient(0deg, rgba(6, 21, 43, 0.95) 0%, rgba(6, 21, 43, 0.6) 60%, transparent 100%);
            padding: 24px 30px;
        }
        .slide-badge {
            background: #20c997;
            color: #06152b;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .slide-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .slide-desc {
            font-size: 14px;
            color: #cbd5e1;
            line-height: 1.4;
            max-width: 92%;
        }

        /* Facility & Doctor Mini Highlight Bar */
        .ad-highlight-bar {
            height: 68px;
            background: var(--card-glass);
            border-radius: 12px;
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
            flex-shrink: 0;
        }
        .highlight-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .highlight-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(32, 201, 151, 0.15);
            color: #20c997;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .highlight-title {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }
        .highlight-sub {
            font-size: 11px;
            color: #94a3b8;
        }

        /* RIGHT: QUEUE NUMBERS & CALLING CARDS (~37%) */
        .queue-display-column {
            flex: 1.05;
            display: flex;
            flex-direction: column;
            gap: 10px;
            height: 100%;
        }

        /* BIG HERO CALLING CARD */
        .hero-call-card {
            background: linear-gradient(135deg, rgba(0, 77, 51, 0.95) 0%, rgba(14, 39, 73, 0.95) 100%);
            border: 3px solid #20c997;
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 0 25px rgba(32, 201, 151, 0.3);
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }
        .hero-call-card.is-calling {
            border-color: #ffab00;
            animation: pulseHero 1.8s infinite alternate;
        }
        @keyframes pulseHero {
            0% { box-shadow: 0 0 15px rgba(32, 201, 151, 0.4); transform: scale(1); }
            100% { box-shadow: 0 0 35px rgba(255, 171, 0, 0.7); transform: scale(1.01); }
        }

        .hero-header-label {
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #ffab00;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .hero-queue-number {
            font-family: 'Outfit', sans-serif;
            font-size: 60px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
            letter-spacing: 2px;
            margin: 4px 0 2px;
            text-shadow: 0 3px 12px rgba(0,0,0,0.6);
        }
        .hero-patient-name {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .hero-destination-box {
            margin-top: 6px;
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            padding: 5px 10px;
            border: 1px solid rgba(255,255,255,0.15);
            font-size: 15px;
            font-weight: 700;
            color: #b3ffdf;
        }

        /* GRID OF ALL POLIKLINIK / TINDAKAN CARDS */
        .poly-grid-scroll {
            flex: 1;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            padding-right: 2px;
        }
        .poly-grid-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .poly-grid-scroll::-webkit-scrollbar-thumb {
            background: rgba(32, 201, 151, 0.4);
            border-radius: 10px;
        }

        .mini-poly-card {
            background: var(--card-glass);
            border-radius: 12px;
            border: 1.5px solid var(--border-glass);
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            height: 124px;
        }
        .mini-poly-card.active-poli {
            border-color: #20c997;
            box-shadow: 0 0 15px rgba(32, 201, 151, 0.25);
        }
        .mini-poly-card.calling-poli {
            border-color: #ffab00;
            box-shadow: 0 0 20px rgba(255, 171, 0, 0.5);
            animation: miniBlink 1.5s infinite alternate;
        }
        @keyframes miniBlink {
            0% { transform: scale(1); }
            100% { transform: scale(1.02); }
        }

        .mini-card-head {
            background: rgba(0, 54, 36, 0.9);
            color: #ffffff;
            padding: 5px 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mini-card-body {
            padding: 4px 8px;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .mini-queue-num {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 900;
            color: #20c997;
            line-height: 1;
            letter-spacing: 1px;
        }
        .mini-poly-card.calling-poli .mini-queue-num {
            color: #ffab00;
        }
        .mini-patient-name {
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }
        .mini-card-foot {
            background: rgba(0,0,0,0.25);
            padding: 3px 6px;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        /* 3. RUNNING TEXT FOOTER */
        .tv-footer {
            background: #002014;
            border-top: 2px solid #20c997;
            padding: 4px 0;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            height: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        /* FLOATING ACTION BUTTONS */
        .floating-controls {
            position: fixed;
            bottom: 44px;
            left: 20px;
            z-index: 999;
            display: flex;
            gap: 8px;
        }
        .btn-float-action {
            background: rgba(14, 39, 73, 0.92);
            color: #ffffff;
            border: 1px solid rgba(32, 201, 151, 0.4);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(8px);
            transition: all 0.2s;
        }
        .btn-float-action:hover {
            background: #20c997;
            color: #06152b;
            transform: scale(1.04);
        }

        /* Fullscreen & Audio Activation Modal */
        #fullscreen-prompt-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(6, 21, 43, 0.92);
            backdrop-filter: blur(12px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .prompt-card {
            background: linear-gradient(135deg, #0b2240 0%, #004d33 100%);
            border: 2px solid #20c997;
            border-radius: 24px;
            padding: 36px 48px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            max-width: 580px;
            animation: bounceIn 0.5s ease;
        }
        @keyframes bounceIn {
            0% { transform: scale(0.85); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Media Settings Modal */
        .modal-content-dark {
            background: #0b2240;
            color: #ffffff;
            border: 2px solid #20c997;
            border-radius: 16px;
        }
        .nav-pills-custom .nav-link {
            color: #cbd5e1;
            border-radius: 10px;
            font-weight: 700;
            padding: 8px 16px;
        }
        .nav-pills-custom .nav-link.active {
            background: #20c997;
            color: #06152b;
        }
    </style>
</head>
<body>

<!-- AUTO-FULLSCREEN & AUDIO ACTIVATION MODAL -->
<div id="fullscreen-prompt-modal" onclick="activateTvMode();">
    <div class="prompt-card">
        <div style="font-size: 50px; color: #ffab00; margin-bottom: 10px;">
            <i class="fas fa-tv"></i>
        </div>
        <h2 style="font-family: 'Outfit', sans-serif; font-weight: 900; color: #ffffff; margin-bottom: 8px;">
            Aktifkan Mode Layar TV Besar
        </h2>
        <p style="font-size: 15px; color: #cbd5e1; margin-bottom: 22px;">
            Klik tombol di bawah untuk membuka <strong>Layar Penuh (Fullscreen TV)</strong> dan mengaktifkan <strong>Audio Suara Panggilan Antrean Otomatis</strong>.
        </p>
        <button type="button" class="btn btn-warning btn-lg font-weight-bold px-5 py-3 shadow" style="border-radius: 30px; font-size: 17px;">
            <i class="fas fa-expand-arrows-alt mr-2"></i> BUKA FULLSCREEN SEKARANG
        </button>
    </div>
</div>

<!-- 1. TOP HEADER BAR -->
<div class="tv-header">
    <div class="d-flex align-items-center">
        <?php if (clinic_logo()): ?>
            <img src="<?= clinic_logo() ?>" alt="Logo" class="mr-3" style="max-height: 42px; max-width: 130px; object-fit: contain;">
        <?php else: ?>
            <i class="fas fa-hospital-user fa-2x mr-3 text-warning"></i>
        <?php endif; ?>
        <div>
            <div class="clinic-brand"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
            <div class="clinic-tagline"><?= esc(clinic_setting('clinic_tagline', 'Layanan Kesehatan Paripurna & Resto Gizi Sehat Terpadu')) ?></div>
        </div>
    </div>
    <div class="d-flex align-items-center">
        <div class="mr-3 text-right d-none d-md-block">
            <div id="live-date" class="font-weight-bold" style="font-size: 13px; color: #ffffff;"></div>
            <div style="font-size: 11px; color: #b3ffdf;">Sumbawa Besar (WITA)</div>
        </div>
        <div class="digital-clock mr-3" id="live-clock">00:00:00</div>
        <button type="button" class="btn btn-sm btn-outline-light font-weight-bold mr-2" data-toggle="modal" data-target="#modalMediaSettings" title="Pengaturan Media Iklan TV">
            <i class="fas fa-sliders text-warning"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-light font-weight-bold" onclick="toggleFullScreen();" title="Toggle Fullscreen">
            <i class="fas fa-expand" id="fs-icon"></i>
        </button>
    </div>
</div>

<!-- 2. MAIN SPLIT SCREEN (63% IKLAN & PROMO / 37% ANTREAN) -->
<div class="tv-main-container">

    <!-- LEFT: ADVERTISING & HOSPITAL INFORMATION (63%) -->
    <div class="media-ad-column">
        
        <!-- Video / Promo / YouTube Container -->
        <div class="ad-video-wrapper" id="ad-media-container">
            
            <!-- A. MODE 1: SLIDESHOW GAMBAR EDUKASI & FASILITAS (Default) -->
            <div id="media-view-slideshow" style="width: 100%; height: 100%; position: relative;">
                <div class="ad-slide active" style="background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.65)), url('https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&w=1200&q=80');">
                    <div class="slide-overlay-content">
                        <span class="slide-badge"><i class="fas fa-star mr-1"></i> Layanan Unggulan</span>
                        <div class="slide-title">Instalasi Gawat Darurat (UGD 24 Jam) & Poliklinik Spesialis</div>
                        <div class="slide-desc">Dilengkapi peralatan medis modern, dokter spesialis berpengalaman, dan sistem antrean terintegrasi untuk kenyamanan pasien dan keluarga.</div>
                    </div>
                </div>
                <div class="ad-slide" style="background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.65)), url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1200&q=80');">
                    <div class="slide-overlay-content">
                        <span class="slide-badge"><i class="fas fa-flask mr-1"></i> Diagnostik Presisi</span>
                        <div class="slide-title">Laboratorium Medis Lengkap & Instalasi Farmasi Cepat</div>
                        <div class="slide-desc">Pemeriksaan darah, urine, kimia klinik dengan hasil akurat dan pengambilan resep obat terverifikasi apoteker profesional tanpa antre lama.</div>
                    </div>
                </div>
                <div class="ad-slide" style="background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.65)), url('https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1200&q=80');">
                    <div class="slide-overlay-content">
                        <span class="slide-badge"><i class="fas fa-utensils mr-1"></i> Nutrisi Sehat</span>
                        <div class="slide-title">Sawamawa Resto Gizi Sehat & Nutrisi Medis</div>
                        <div class="slide-desc">Menyajikan makanan sehat bergizi seimbang yang dikurasi khusus oleh ahli gizi klinis untuk mendukung pemulihan stamina pasien.</div>
                    </div>
                </div>
                <div class="ad-slide" style="background-image: linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.65)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1200&q=80');">
                    <div class="slide-overlay-content">
                        <span class="slide-badge"><i class="fas fa-mobile-screen mr-1"></i> Digital Healthcare</span>
                        <div class="slide-title">Pendaftaran Online Mandiri & Rekam Medis Terkoneksi SATUSEHAT</div>
                        <div class="slide-desc">Daftar berobat dari rumah melalui portal website kami, pantau nomor antrean langsung dari smartphone tanpa perlu menunggu lama.</div>
                    </div>
                </div>
            </div>

            <!-- B. MODE 2: VIDEO MP4 LOKAL (Ringan & 0% Buffering) -->
            <div id="media-view-local-video" style="width: 100%; height: 100%; display: none;">
                <video id="tv-local-player" autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover;">
                    <source src="<?= esc($tvVideoUrl ?: base_url('uploads/videos/display_ad.mp4')) ?>" type="video/mp4">
                    Browser Smart TV Anda tidak mendukung pemutar video HTML5.
                </video>
            </div>

            <!-- C. MODE 3: YOUTUBE EMBED VIDEO / LIVE STREAMING -->
            <div id="media-view-youtube" style="width: 100%; height: 100%; display: none;">
                <iframe id="tv-youtube-player" src="https://www.youtube-nocookie.com/embed/<?= esc($tvYoutubeId ?: 'dQw4w9WgXcQ') ?>?autoplay=1&mute=1&loop=1&playlist=<?= esc($tvYoutubeId ?: 'dQw4w9WgXcQ') ?>&controls=0&showinfo=0&rel=0&modestbranding=1" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>

        </div>

        <!-- Highlight Mini Info Bar -->
        <div class="ad-highlight-bar">
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fas fa-truck-medical"></i></div>
                <div>
                    <div class="highlight-title">Ambulans Siaga 24 Jam</div>
                    <div class="highlight-sub">Layanan Penjemputan Darurat</div>
                </div>
            </div>
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fas fa-user-doctor"></i></div>
                <div>
                    <div class="highlight-title">Dokter Spesialis & Umum</div>
                    <div class="highlight-sub">Pelayanan Ramah & Profesional</div>
                </div>
            </div>
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fas fa-pills"></i></div>
                <div>
                    <div class="highlight-title">Apotek & Resep Resmi</div>
                    <div class="highlight-sub">Obat Paten & Standar BPOM</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: QUEUE NUMBERS & ACTIVE CALLING PANEL (37%) -->
    <div class="queue-display-column">

        <!-- 1. BIG HERO CALLING CARD -->
        <div class="hero-call-card" id="hero-call-card">
            <div class="hero-header-label">
                <i class="fas fa-bullhorn text-warning"></i>
                <span id="hero-label-text">PANGGILAN ANTREAN SAAT INI</span>
            </div>
            <div class="hero-queue-number" id="hero-queue-num">--</div>
            <div class="hero-patient-name" id="hero-patient-name">Menunggu Panggilan...</div>
            <div class="hero-destination-box" id="hero-destination">
                <i class="fas fa-door-open mr-1 text-teal"></i> Silakan perhatikan panggilan petugas
            </div>
        </div>

        <!-- 2. GRID OF ALL POLIKLINIK & RUANG TINDAKAN -->
        <div class="poly-grid-scroll" id="poly-grid-container">
            <?php foreach ($polyclinics as $p): ?>
                <?php $current = $activeQueues[$p->id] ?? null; ?>
                <div class="mini-poly-card <?= ($current && ($current->status === 'called' || $current->status === 'triage')) ? 'calling-poli' : ($current ? 'active-poli' : '') ?>" id="mini-poly-<?= $p->id ?>" data-poly-id="<?= $p->id ?>">
                    <div class="mini-card-head">
                        <span><i class="fas fa-stethoscope mr-1 text-warning"></i> POLI <?= esc($p->name) ?></span>
                        <span class="badge badge-light text-dark font-weight-bold" id="mini-status-<?= $p->id ?>" style="font-size: 10px;">
                            <?= $current ? (($current->status === 'called' || $current->status === 'triage') ? 'DIPANGGIL' : strtoupper($current->status)) : 'STANDBY' ?>
                        </span>
                    </div>
                    <div class="mini-card-body">
                        <div class="mini-queue-num" id="mini-qno-<?= $p->id ?>"><?= $current ? esc($current->queue_no) : '--' ?></div>
                        <div class="mini-patient-name" id="mini-name-<?= $p->id ?>"><?= ($current && !empty($current->patient_name)) ? esc($current->patient_name) : 'Menunggu Panggilan' ?></div>
                    </div>
                    <div class="mini-card-foot">
                        <span id="mini-doc-<?= $p->id ?>"><i class="fas fa-user-md mr-1 text-success"></i> <?= ($current && !empty($current->doctor_name)) ? esc($current->doctor_name) : 'Dokter Jaga' ?></span>
                        <i class="fas fa-volume-up text-muted"></i>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>

<!-- 3. RUNNING TEXT FOOTER -->
<div class="tv-footer">
    <div class="container-fluid">
        <marquee scrollamount="6">
            <i class="fas fa-bullhorn text-warning mr-2"></i> 
            Selamat datang di <strong><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></strong>. Harap perhatikan nomor antrean Anda pada layar. Bagi pasien yang nomor antreannya dipanggil, silakan langsung menuju ke ruang poliklinik atau ruang tindakan terkait. Pendaftaran online dan jadwal dokter dapat diakses melalui website resmi kami. Jagalah ketertiban dan kebersihan bersama demi kenyamanan pelayanan.
        </marquee>
    </div>
</div>

<!-- FLOATING CONTROLS (PILIH MEDIA, TEST SUARA & FULLSCREEN) -->
<div class="floating-controls">
    <button type="button" class="btn-float-action" data-toggle="modal" data-target="#modalMediaSettings">
        <i class="fas fa-photo-film text-warning"></i> Pilih Opsi Iklan TV
    </button>
    <button type="button" class="btn-float-action" onclick="testDisplayVoice();">
        <i class="fas fa-volume-up text-info"></i> Tes Suara
    </button>
    <button type="button" class="btn-float-action" onclick="toggleFullScreen();">
        <i class="fas fa-expand text-success"></i> Fullscreen TV
    </button>
</div>

<!-- MODAL: PENGATURAN PILIHAN MEDIA IKLAN TV -->
<div class="modal fade" id="modalMediaSettings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-content-dark shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-tv text-warning mr-2"></i> Pengaturan Pilihan Media Layar TV</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/save-tv-media') ?>" method="post" enctype="multipart/form-data" id="form-tv-media">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="text-muted text-sm mb-3">Pilih format media yang ingin ditampilkan pada sisi kiri layar TV ruang tunggu:</p>
                    
                    <ul class="nav nav-pills nav-pills-custom mb-3" id="mediaPillTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?= ($tvMediaType === 'slideshow' || empty($tvMediaType)) ? 'active' : '' ?>" id="pill-slideshow-tab" data-toggle="pill" href="#tab-slideshow" role="tab">
                                <i class="fas fa-images mr-1"></i> 1. Slide Banner Foto (Default)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($tvMediaType === 'local_video') ? 'active' : '' ?>" id="pill-local-video-tab" data-toggle="pill" href="#tab-local-video" role="tab">
                                <i class="fas fa-file-video mr-1"></i> 2. Video MP4 Lokal (Paling Ringan)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($tvMediaType === 'youtube') ? 'active' : '' ?>" id="pill-youtube-tab" data-toggle="pill" href="#tab-youtube" role="tab">
                                <i class="fab fa-youtube mr-1 text-danger"></i> 3. Link YouTube / Streaming
                            </a>
                        </li>
                    </ul>

                    <input type="hidden" name="media_type" id="input_media_type" value="<?= esc($tvMediaType ?: 'slideshow') ?>">

                    <div class="tab-content" id="mediaPillTabContent">
                        <!-- Tab 1: Slideshow -->
                        <div class="tab-pane fade <?= ($tvMediaType === 'slideshow' || empty($tvMediaType)) ? 'show active' : '' ?>" id="tab-slideshow" role="tabpanel">
                            <div class="alert alert-dark border-secondary">
                                <h6 class="font-weight-bold text-teal"><i class="fas fa-check-circle mr-1"></i> Mode Slide Banner Dinamis</h6>
                                <p class="mb-0 text-sm">Menampilkan slide promosi fasilitas UGD 24 Jam, Poli Spesialis, Laboratorium, Apotek, dan Resto Gizi secara berotasi setiap 8 detik. Ringan, bebas buffering, dan tampil elegan.</p>
                            </div>
                        </div>

                        <!-- Tab 2: Local Video -->
                        <div class="tab-pane fade <?= ($tvMediaType === 'local_video') ? 'show active' : '' ?>" id="tab-local-video" role="tabpanel">
                            <div class="alert alert-dark border-secondary">
                                <h6 class="font-weight-bold text-warning"><i class="fas fa-bolt mr-1"></i> Mode Video MP4 Lokal (Sangat Direkomendasikan)</h6>
                                <p class="text-sm mb-3">Memutar video promosi berulang (*looping*) dari server lokal tanpa butuh koneksi internet, 100% bebas dari iklan pihak luar, dan didukung akselerasi GPU Smart TV.</p>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-white">Upload File Video Baru (.mp4 / .webm max 100MB):</label>
                                    <input type="file" name="video_file" class="form-control-file text-white" accept="video/mp4,video/webm">
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-white">Atau Masukkan URL File Video MP4:</label>
                                    <input type="text" name="video_url" class="form-control bg-dark text-white border-secondary" placeholder="http://.../video.mp4" value="<?= esc($tvVideoUrl) ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: YouTube -->
                        <div class="tab-pane fade <?= ($tvMediaType === 'youtube') ? 'show active' : '' ?>" id="tab-youtube" role="tabpanel">
                            <div class="alert alert-dark border-secondary">
                                <h6 class="font-weight-bold text-danger"><i class="fab fa-youtube mr-1"></i> Mode YouTube Embed / Live Streaming</h6>
                                <p class="text-sm mb-3">Memutar video langsung dari channel YouTube RS. Pastikan TV memiliki koneksi internet yang stabil.</p>
                                
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-white">Link Video atau ID YouTube:</label>
                                    <input type="text" name="youtube_id" class="form-control bg-dark text-white border-secondary" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ atau dQw4w9WgXcQ" value="<?= esc($tvYoutubeId) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Simpan Pilihan Media</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/voice_caller.js') ?>"></script>
<script>
    // State Tracking
    let audioUnlocked = false;
    let lastDataHash = '';
    let seenCalledQueueIds = new Set();
    const API_URL = '<?= rtrim(base_url(), '/') ?>/api/sync/klinik-queue';
    let currentMediaType = '<?= esc($tvMediaType ?: 'slideshow') ?>';

    // 1. SET ACTIVE MEDIA VIEW
    function applyMediaView(mode) {
        currentMediaType = mode;
        $('#media-view-slideshow').hide();
        $('#media-view-local-video').hide();
        $('#media-view-youtube').hide();

        if (mode === 'local_video') {
            $('#media-view-local-video').show();
            const player = document.getElementById('tv-local-player');
            if (player) {
                player.play().catch(e => console.log('Autoplay video info:', e));
            }
        } else if (mode === 'youtube') {
            $('#media-view-youtube').show();
        } else {
            $('#media-view-slideshow').show();
        }
    }

    // Apply initial media mode
    applyMediaView(currentMediaType);

    // Sync tab selection with hidden input
    $('#pill-slideshow-tab').on('click', function() { $('#input_media_type').val('slideshow'); });
    $('#pill-local-video-tab').on('click', function() { $('#input_media_type').val('local_video'); });
    $('#pill-youtube-tab').on('click', function() { $('#input_media_type').val('youtube'); });

    // 2. FULLSCREEN & AUDIO ACTIVATION
    function activateTvMode() {
        audioUnlocked = true;
        
        // Request Fullscreen
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => console.log('FS blocked', err));
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }

        // Resume Audio Context
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                const ctx = new AudioCtx();
                ctx.resume();
            }
        } catch (e) {}

        // Play chime & hide modal
        playHospitalChime(function() {
            $('#fullscreen-prompt-modal').fadeOut(350);
        });

        // Trigger local video play if in video mode
        if (currentMediaType === 'local_video') {
            const player = document.getElementById('tv-local-player');
            if (player) player.play().catch(e => {});
        }
    }

    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => console.log(err));
            $('#fs-icon').removeClass('fa-expand').addClass('fa-compress');
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                $('#fs-icon').removeClass('fa-compress').addClass('fa-expand');
            }
        }
    }

    // Keyboard Shortcuts: F = Fullscreen, T = Test Voice, M = Media Modal
    $(document).keydown(function(e) {
        if (e.key === 'F11' || e.key === 'f' || e.key === 'F') {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                toggleFullScreen();
            }
        } else if (e.key === 'm' || e.key === 'M') {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                $('#modalMediaSettings').modal('toggle');
            }
        } else if (e.key === 't' || e.key === 'T') {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                testDisplayVoice();
            }
        }
    });

    // 3. LIVE CLOCK (WITA)
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        $('#live-clock').text(`${hrs}:${mins}:${secs}`);

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        $('#live-date').text(now.toLocaleDateString('id-ID', options));
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 4. SLIDESHOW ROTATION (EVERY 8 SECONDS)
    let currentSlide = 0;
    const slides = $('.ad-slide');
    function nextSlide() {
        if (currentMediaType !== 'slideshow') return;
        slides.eq(currentSlide).removeClass('active').fadeOut(600);
        currentSlide = (currentSlide + 1) % slides.length;
        slides.eq(currentSlide).addClass('active').fadeIn(600);
    }
    setInterval(nextSlide, 8000);

    // 5. TEST VOICE CALL
    // 5. TEST VOICE CALL
    function testDisplayVoice() {
        activateTvMode();
        window.VCM.callPatient('A-001', 'Budi Santoso', 'Poliklinik Umum');
    }

    // 6. REACTIVE VOICE EVENT HOOKS (SINKRONISASI VISUAL HERO BOX & SUARA PANGGILAN ANTI-TABRAKAN)
    window.addEventListener('vcm:call_started', function(e) {
        const detail = e.detail || {};
        const qNo    = detail.queueNo || '--';
        const pName  = detail.patientName || 'Pasien';
        let dest     = detail.targetName || 'Ruang Pelayanan';
        let label    = 'SEDANG DIPANGGIL';
        let iconCls  = 'fa-volume-high';

        const sType = (detail.serviceType || '').toLowerCase();
        if (sType === 'kasir') {
            label = 'MENUJU KASIR';
            dest = 'Kasir Pembayaran';
        } else if (sType === 'farmasi' || sType === 'apotek') {
            label = 'AMBIL OBAT DI APOTEK';
            dest = 'Loket Farmasi dan Apotek';
        } else if (sType === 'completed') {
            label = 'PELAYANAN SELESAI';
            dest = 'Selesai Dilayani';
            iconCls = 'fa-check-circle';
        }

        $('#hero-call-card').addClass('is-calling');
        $('#hero-label-text').html('<i class="fas ' + iconCls + ' text-warning mr-1 animate__animated animate__heartBeat animate__infinite"></i> ' + label);
        $('#hero-queue-num').text(qNo);
        $('#hero-patient-name').text(pName);
        $('#hero-destination').html('<i class="fas fa-door-open mr-1 text-warning"></i> Silakan menuju ke <strong>' + dest + '</strong>');
    });

    window.addEventListener('vcm:call_ended', function() {
        setTimeout(function() {
            if (!window.VCM.isPlaying()) {
                $('#hero-label-text').html('<i class="fas fa-check-circle text-teal mr-1"></i> PANGGILAN SELESAI');
                $('#hero-call-card').removeClass('is-calling');
            }
        }, 1200);
    });

    // 7. ZERO-RELOAD REAL-TIME QUEUE & POLYCLINIC SYNC
    function syncDisplayTV() {
        $.ajax({
            url: API_URL,
            type: 'GET',
            dataType: 'json',
            cache: false,
            timeout: 5000,
            success: function(res) {
                if (!res || res.status !== 'success' || !res.data) return;

                const queues = res.data;
                const currentHash = JSON.stringify(queues.map(q => ({ id: q.id, s: q.status, q: q.queue_number, t: q.call_time })));

                if (currentHash === lastDataHash) return;
                lastDataHash = currentHash;

                // Jika tidak sedang ada audio aktif, perbarui hero card dengan antrean standby/menunggu
                if (!window.VCM.isPlaying()) {
                    const calledQueues = queues.filter(q => {
                        const s = (q.status || '').toLowerCase();
                        const vSt = (q.visit_status || '').toLowerCase();
                        if (vSt === 'cancelled' || vSt === 'completed' || vSt === 'cashier' || vSt === 'prescription' || vSt === 'pharmacy' || vSt === 'done') return false;
                        if (s === 'cancelled' || s === 'completed' || s === 'cashier' || s === 'prescription' || s === 'pharmacy' || s === 'done') return false;
                        return s === 'called' || s === 'triage';
                    });

                    if (calledQueues.length > 0) {
                        const topCall = calledQueues[0];
                        const targetName = (topCall.visit_type === 'tindakan')
                            ? 'Ruang Tindakan ' + (topCall.tindakan_name || topCall.service_name || 'Medis')
                            : 'Poliklinik ' + (topCall.polyclinic_name || topCall.poly_name || 'Umum');

                        $('#hero-call-card').addClass('is-calling');
                        $('#hero-label-text').html('<i class="fas fa-volume-high text-warning mr-1"></i> ANTRIAN TERPANGGIL');
                        $('#hero-queue-num').text(topCall.queue_number || '--');
                        $('#hero-patient-name').text(topCall.patient_name || 'Pasien');
                        $('#hero-destination').html('<i class="fas fa-door-open mr-1 text-warning"></i> Silakan menuju ke <strong>' + targetName + '</strong>');
                    } else {
                        const activeWaiting = queues.filter(q => {
                            const s = (q.status || '').toLowerCase();
                            const vSt = (q.visit_status || '').toLowerCase();
                            if (vSt === 'cashier' || vSt === 'prescription' || vSt === 'completed' || vSt === 'cancelled' || vSt === 'pharmacy' || vSt === 'done') return false;
                            if (s === 'completed' || s === 'cancelled' || s === 'cashier' || s === 'prescription' || s === 'pharmacy' || s === 'done' || s === 'no-show') return false;
                            return s === 'waiting' || s === 'examining';
                        });

                        if (activeWaiting.length > 0) {
                            const topWaiting = activeWaiting[0];
                            const targetName = (topWaiting.visit_type === 'tindakan')
                                ? 'Ruang Tindakan ' + (topWaiting.tindakan_name || topWaiting.service_name || 'Medis')
                                : 'Poliklinik ' + (topWaiting.polyclinic_name || topWaiting.poly_name || 'Umum');

                            $('#hero-call-card').removeClass('is-calling');
                            $('#hero-label-text').html('<i class="fas fa-user-clock text-teal mr-1"></i> ANTREAN BERIKUTNYA');
                            $('#hero-queue-num').text(topWaiting.queue_number || '--');
                            $('#hero-patient-name').text(topWaiting.patient_name || 'Pasien');
                            $('#hero-destination').html('<i class="fas fa-door-open mr-1 text-teal"></i> Menuju <strong>' + targetName + '</strong>');
                        } else {
                            $('#hero-call-card').removeClass('is-calling');
                            $('#hero-label-text').html('<i class="fas fa-check-circle text-success mr-1"></i> SISTEM ANTREAN STANDBY');
                            $('#hero-queue-num').text('--');
                            $('#hero-patient-name').text('Menunggu Panggilan Pasien...');
                            $('#hero-destination').html('<i class="fas fa-door-open mr-1 text-teal"></i> Silakan perhatikan panggilan petugas');
                        }
                    }
                }

                // 2. Map queues to polyclinic cards
                const polyMap = {};
                queues.forEach(q => {
                    const status = (q.status || '').toLowerCase();
                    const vStatus = (q.visit_status || '').toLowerCase();
                    if (vStatus === 'cancelled' || vStatus === 'completed' || vStatus === 'prescription' || vStatus === 'cashier' || vStatus === 'pharmacy' || vStatus === 'done') {
                        return;
                    }
                    if (status === 'cancelled' || status === 'completed' || status === 'prescription' || status === 'cashier' || status === 'pharmacy' || status === 'done' || status === 'no-show') {
                        return;
                    }
                    const polyId = q.polyclinic_id;
                    const isCallingState = (status === 'called' || status === 'triage');
                    if (polyId && !polyMap[polyId]) {
                        polyMap[polyId] = q;
                    } else if (polyId && polyMap[polyId] && polyMap[polyId].status !== 'called' && isCallingState) {
                        polyMap[polyId] = q;
                    }
                });

                // 3. Update Mini Cards
                $('.mini-poly-card').each(function() {
                    const polyId = $(this).data('poly-id');
                    const q = polyMap[polyId];
                    const $card = $('#mini-poly-' + polyId);

                    if (q) {
                        const isCalling = (q.status === 'called' || q.status === 'triage');
                        $card.removeClass('calling-poli active-poli').addClass(isCalling ? 'calling-poli' : 'active-poli');
                        $('#mini-qno-' + polyId).text(q.queue_number || '--');
                        $('#mini-name-' + polyId).text(q.patient_name || 'Pasien');
                        $('#mini-doc-' + polyId).html('<i class="fas fa-user-md mr-1 text-success"></i> ' + (q.doctor_name || 'Dokter Jaga'));

                        const stColor = isCalling ? 'warning text-dark' : (q.status === 'completed' ? 'success' : 'light');
                        const stLabel = isCalling ? 'DIPANGGIL' : q.status.toUpperCase();
                        $('#mini-status-' + polyId).attr('class', 'badge badge-' + stColor + ' font-weight-bold').text(stLabel);
                    } else {
                        $card.removeClass('calling-poli active-poli');
                        $('#mini-qno-' + polyId).text('--');
                        $('#mini-name-' + polyId).text('Menunggu');
                        $('#mini-status-' + polyId).attr('class', 'badge badge-secondary font-weight-bold').text('STANDBY');
                    }
                });
            },
            error: function(err) {
                console.warn('Sync TV display error', err);
            }
        });
    }

    // Inisialisasi Server Voice Queue Polling (Anti-Collision Audio Dispatcher)
    window.VCM.startServerPolling(2000);

    // Polling Visual Data setiap 2.5 detik
    setInterval(syncDisplayTV, 2500);
    syncDisplayTV();
</script>

</body>
</html>
