<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?> | Portal Autentikasi macOS</title>

    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
        <link rel="shortcut icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --apple-font: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Plus Jakarta Sans", "Inter", "Helvetica Neue", sans-serif;
            --apple-blue: #007aff;
            --apple-blue-dark: #0062cc;
            --apple-green: #34c759;
            --apple-orange: #ff9500;
            --apple-red: #ff3b30;
            --apple-text-dark: #1d1d1f;
            --apple-text-muted: #86868b;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            font-family: var(--apple-font);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #000;
            color: var(--apple-text-dark);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            zoom: 90%;
        }

        /* macOS Big Sur Dynamic Artistic Wallpaper Background */
        .macos-wallpaper-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 15% 15%, rgba(0, 122, 255, 0.45) 0%, transparent 40%),
                radial-gradient(circle at 85% 20%, rgba(255, 45, 85, 0.35) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(52, 199, 89, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 85% 85%, rgba(255, 149, 0, 0.35) 0%, transparent 45%),
                linear-gradient(135deg, #1e3c72 0%, #2a5298 35%, #7928ca 70%, #ff0080 100%);
            background-size: cover;
            background-position: center;
            z-index: -2;
        }

        .macos-blur-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            backdrop-filter: blur(50px) saturate(160%);
            -webkit-backdrop-filter: blur(50px) saturate(160%);
            background: rgba(0, 0, 0, 0.15);
            z-index: -1;
        }

        /* 1. macOS Top Menubar Header */
        .macos-top-menubar {
            width: 100%;
            height: 38px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 500;
            z-index: 100;
            text-shadow: 0 1px 2px rgba(0,0,0,0.35);
        }

        .macos-menubar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none !important;
        }

        .macos-menubar-brand i {
            font-size: 15px;
        }

        .macos-menubar-status {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 12px;
        }

        /* 2. Login Central Layout */
        .login-main-container {
            width: 100%;
            max-width: 960px;
            margin: auto;
            padding: 30px 16px;
            z-index: 10;
        }

        /* macOS Window Container Card */
        .macos-window-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(35px) saturate(190%);
            -webkit-backdrop-filter: blur(35px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            transition: all 0.3s ease;
        }

        /* macOS Window Titlebar Header with Traffic Lights */
        .macos-window-header {
            background: rgba(255, 255, 255, 0.55);
            border-bottom: 1px solid rgba(0, 0, 0, 0.07);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .macos-traffic-lights {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .macos-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            border: 0.5px solid rgba(0, 0, 0, 0.15);
        }
        .macos-dot-red { background: #ff5f56; }
        .macos-dot-yellow { background: #ffbd2e; }
        .macos-dot-green { background: #27c93f; }

        .macos-window-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #1d1d1f;
            letter-spacing: -0.01em;
        }

        /* Left Side: Clinic Showcase Panel */
        .macos-showcase-panel {
            padding: 44px 36px;
            background: linear-gradient(180deg, rgba(240, 246, 255, 0.5) 0%, rgba(255, 255, 255, 0.3) 100%);
            border-right: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .clinic-avatar-box {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(180deg, #007aff 0%, #0056b3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 28px;
            box-shadow: 0 8px 20px rgba(0, 122, 255, 0.35), inset 0 1px 0 rgba(255,255,255,0.4);
            margin-bottom: 20px;
        }

        .clinic-avatar-img {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            object-fit: contain;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .showcase-heading {
            font-size: 26px;
            font-weight: 800;
            color: #1d1d1f;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .showcase-text-blue {
            color: #007aff;
        }

        .showcase-subtext {
            color: #64748b;
            font-size: 13.5px;
            line-height: 1.55;
            margin-bottom: 24px;
        }

        .macos-feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .macos-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .macos-feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .icon-blue { background: linear-gradient(180deg, #007aff, #0056b3); }
        .icon-green { background: linear-gradient(180deg, #34c759, #248a3d); }
        .icon-purple { background: linear-gradient(180deg, #af52de, #5856d6); }

        .macos-feature-text {
            font-size: 12.5px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .macos-feature-desc {
            font-size: 11px;
            color: #86868b;
            font-weight: 400;
        }

        /* Right Side: Login Form Panel */
        .macos-form-panel {
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .macos-form-title {
            font-size: 22px;
            font-weight: 700;
            color: #1d1d1f;
            letter-spacing: -0.01em;
            margin-bottom: 6px;
        }

        .macos-form-subtitle {
            font-size: 13px;
            color: #86868b;
            margin-bottom: 24px;
        }

        .macos-input-wrapper {
            position: relative;
            margin-bottom: 18px;
        }

        .macos-input-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }

        .macos-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .macos-input-icon {
            position: absolute;
            left: 14px;
            color: #007aff;
            font-size: 13px;
            z-index: 5;
        }

        .macos-form-control {
            width: 100%;
            height: 42px;
            background: rgba(255, 255, 255, 0.92) !important;
            border: 1px solid rgba(0, 0, 0, 0.14) !important;
            border-radius: 10px !important;
            padding: 8px 14px 8px 38px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #1d1d1f !important;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
            transition: all 0.15s ease;
        }

        .macos-form-control:focus {
            background: #ffffff !important;
            border-color: #007aff !important;
            box-shadow: 0 0 0 3.5px rgba(0, 122, 255, 0.25) !important;
            outline: none !important;
        }

        .macos-btn-toggle-pwd {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
            z-index: 5;
            transition: color 0.15s ease;
        }

        .macos-btn-toggle-pwd:hover {
            color: #007aff;
        }

        /* macOS Action Submit Button */
        .macos-btn-submit {
            width: 100%;
            height: 44px;
            background: linear-gradient(180deg, #007aff 0%, #0062cc 100%) !important;
            border: 1px solid #0056b3 !important;
            border-radius: 10px !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0, 122, 255, 0.35) !important;
            transition: all 0.15s ease;
            cursor: pointer;
            margin-top: 10px;
        }

        .macos-btn-submit:hover {
            background: linear-gradient(180deg, #1a88ff 0%, #007aff 100%) !important;
            box-shadow: 0 4px 14px rgba(0, 122, 255, 0.45) !important;
            transform: translateY(-1px);
        }

        .macos-btn-submit:active {
            transform: translateY(0);
        }

        /* Footer Info inside Card */
        .macos-trust-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(246, 248, 252, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 24px;
        }

        /* 3. Bottom Credits / System Status */
        .macos-bottom-dock {
            text-align: center;
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
            padding: 16px 20px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            z-index: 10;
        }

        .macos-bottom-dock a {
            color: #ffffff !important;
            font-weight: 600;
            text-decoration: underline;
        }

        @media (max-width: 991px) {
            .macos-showcase-panel {
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.06);
                padding: 32px 24px;
            }
            .macos-form-panel {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>

<!-- Ambient Wallpaper Background -->
<div class="macos-wallpaper-bg"></div>
<div class="macos-blur-overlay"></div>

<!-- 1. Top Menubar -->
<div class="macos-top-menubar">
    <div class="macos-menubar-brand">
        <i class="fab fa-apple"></i>
        <span><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></span>
    </div>
    <div class="macos-menubar-status">
        <a href="<?= base_url() ?>" class="text-white text-decoration-none mr-2">
            <i class="fas fa-house mr-1"></i> Beranda Publik
        </a>
        <span><i class="fas fa-wifi mr-1"></i> Online</span>
        <span id="macos-clock"><?= date('D, d M H:i') ?></span>
        <i class="fas fa-sliders"></i>
    </div>
</div>

<!-- 2. Main Login Window -->
<div class="login-main-container">
    <div class="macos-window-card">
        
        <!-- macOS Window Titlebar -->
        <div class="macos-window-header">
            <div class="macos-traffic-lights">
                <span class="macos-dot macos-dot-red" title="Tutup"></span>
                <span class="macos-dot macos-dot-yellow" title="Minimalkan"></span>
                <span class="macos-dot macos-dot-green" title="Perbesar"></span>
            </div>
            <span class="macos-window-title">
                <i class="fas fa-shield-halved text-primary mr-1"></i> Portal Autentikasi Nakes
            </span>
            <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 10px; background: rgba(52,199,89,0.15); color: #248a3d; border: 1px solid rgba(52,199,89,0.3); border-radius: 20px;">
                <i class="fas fa-lock mr-1"></i> 256-Bit SSL
            </span>
        </div>

        <div class="row no-gutters">
            
            <!-- LEFT PANEL: Clinic Info & Feature Showcase -->
            <div class="col-lg-6">
                <div class="macos-showcase-panel">
                    <div>
                        <!-- Clinic Logo / Avatar -->
                        <?php if (clinic_logo()): ?>
                            <img src="<?= clinic_logo() ?>" alt="Logo" class="clinic-avatar-img">
                        <?php else: ?>
                            <div class="clinic-avatar-box">
                                <i class="fas fa-hospital-user"></i>
                            </div>
                        <?php endif; ?>

                        <h2 class="showcase-heading">
                            Sistem Informasi <br>
                            <span class="showcase-text-blue">Pelayanan Medis Terpadu</span>
                        </h2>

                        <p class="showcase-subtext">
                            Pusat kendali klinis operasional: Rekam Medis Elektronik (RME), Instalasi Farmasi e-Resep, Laboratorium, dan Billing Keuangan.
                        </p>

                        <!-- Key Features -->
                        <div class="macos-feature-list">
                            <div class="macos-feature-item">
                                <div class="macos-feature-icon icon-blue">
                                    <i class="fas fa-laptop-medical"></i>
                                </div>
                                <div>
                                    <div class="macos-feature-text">Rekam Medis Elektronik (RME)</div>
                                    <div class="macos-feature-desc">SOAP digital terintegrasi standar SATUSEHAT Kemenkes RI.</div>
                                </div>
                            </div>

                            <div class="macos-feature-item">
                                <div class="macos-feature-icon icon-green">
                                    <i class="fas fa-prescription-bottle-medical"></i>
                                </div>
                                <div>
                                    <div class="macos-feature-text">Farmasi &amp; e-Resep Cepat</div>
                                    <div class="macos-feature-desc">Sinkronisasi stok obat dan peracikan resep tanpa jeda.</div>
                                </div>
                            </div>

                            <div class="macos-feature-item">
                                <div class="macos-feature-icon icon-purple">
                                    <i class="fas fa-shield-check"></i>
                                </div>
                                <div>
                                    <div class="macos-feature-text">Keamanan Akses &amp; Audit Trail</div>
                                    <div class="macos-feature-desc">Enkripsi data klinis berstandar privasi informasi ISO 27001.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ISO Certification footer in left panel -->
                    <div class="pt-4 mt-3 border-top" style="border-color: rgba(0,0,0,0.06) !important;">
                        <small class="text-muted d-block mb-2 font-weight-bold" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px;">
                            Standar Kepatuhan Sistem:
                        </small>
                        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                            <span class="badge badge-light border text-muted font-weight-bold" style="font-size: 10px; border-radius: 6px; padding: 4px 8px;">ISO 27001</span>
                            <span class="badge badge-light border text-muted font-weight-bold" style="font-size: 10px; border-radius: 6px; padding: 4px 8px;">ISO 27701</span>
                            <span class="badge badge-light border text-muted font-weight-bold" style="font-size: 10px; border-radius: 6px; padding: 4px 8px;">SATUSEHAT</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Login Form -->
            <div class="col-lg-6">
                <div class="macos-form-panel">
                    
                    <h3 class="macos-form-title">Selamat Datang</h3>
                    <p class="macos-form-subtitle">Masukkan username dan kata sandi akun nakes Anda.</p>

                    <!-- Error Flashdata -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-0 p-3 mb-3 d-flex align-items-start" style="background: rgba(255,59,48,0.12); color: #d70015; border-radius: 10px; font-size: 12.5px;">
                            <i class="fas fa-circle-exclamation mr-2 mt-1" style="font-size: 15px;"></i>
                            <div style="flex: 1;"><?= session()->getFlashdata('error') ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Success Flashdata -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success border-0 p-3 mb-3 d-flex align-items-start" style="background: rgba(52,199,89,0.14); color: #248a3d; border-radius: 10px; font-size: 12.5px;">
                            <i class="fas fa-circle-check mr-2 mt-1" style="font-size: 15px;"></i>
                            <div style="flex: 1;"><?= session()->getFlashdata('success') ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('login') ?>" method="post" id="form-auth-login">
                        <?= csrf_field() ?>

                        <!-- Username Input -->
                        <div class="macos-input-wrapper">
                            <label class="macos-input-label" for="username_input">
                                <span>Username / Email</span>
                                <small class="text-muted font-weight-normal">Kredensial Petugas</small>
                            </label>
                            <div class="macos-input-group">
                                <i class="fas fa-user macos-input-icon"></i>
                                <input type="text" name="username" id="username_input" class="macos-form-control" placeholder="Masukkan username / email" autocomplete="username" required autofocus>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="macos-input-wrapper mb-4">
                            <label class="macos-input-label" for="login-password">
                                <span>Kata Sandi (Password)</span>
                                <small class="text-muted font-weight-normal">Sensitif Huruf</small>
                            </label>
                            <div class="macos-input-group">
                                <i class="fas fa-key macos-input-icon"></i>
                                <input type="password" name="password" id="login-password" class="macos-form-control" placeholder="Masukkan kata sandi akun" autocomplete="current-password" required style="padding-right: 40px;">
                                <button type="button" class="macos-btn-toggle-pwd" id="btn-toggle-login-pwd" title="Lihat Password" tabindex="-1">
                                    <i class="fas fa-eye" id="icon-login-pwd"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="macos-btn-submit" id="btn-submit-login">
                            <i class="fas fa-right-to-bracket"></i> Masuk ke Sistem
                        </button>
                    </form>

                    <!-- Trust Bar -->
                    <div class="macos-trust-pill">
                        <i class="fas fa-shield-halved text-primary fa-lg"></i>
                        <div>
                            <strong class="d-block text-dark font-weight-bold" style="font-size: 11.5px;">
                                Autentikasi Terproteksi &amp; Terenkripsi
                            </strong>
                            <small class="text-muted d-block" style="font-size: 10.5px;">
                                Proteksi Brute-Force, CSRF Token, dan Audit Log Terverifikasi.
                            </small>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- 3. Bottom Dock Credits -->
<div class="macos-bottom-dock">
    &copy; <?= date('Y') ?> <strong><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></strong> &bull; Sistem Informasi Manajemen Klinik &bull; <a href="<?= base_url() ?>">Kembali ke Beranda Publik</a>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Toggle Show/Hide Password
        $('#btn-toggle-login-pwd').click(function(e) {
            e.preventDefault();
            const input = $('#login-password');
            const icon = $('#icon-login-pwd');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
                $(this).attr('title', 'Sembunyikan Password');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye');
                $(this).attr('title', 'Lihat Password');
            }
        });

        // Submit button loading animation
        $('#form-auth-login').on('submit', function() {
            const btn = $('#btn-submit-login');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengautentikasi...');
        });

        // Dynamic Clock in Top Menubar
        setInterval(function() {
            const now = new Date();
            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            $('#macos-clock').text(`${dayName}, ${date} ${monthName} ${hours}:${minutes}`);
        }, 1000);
    });
</script>

</body>
</html>
