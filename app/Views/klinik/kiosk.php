<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Anjungan Pendaftaran Mandiri (Kiosk) - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></title>

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

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        :root {
            --primary: #00875a;
            --primary-dark: #004d33;
            --teal-accent: #20c997;
            --accent-gold: #ffab00;
            --bg-dark: #07172c;
            --card-glass: rgba(13, 38, 71, 0.94);
            --border-glass: rgba(32, 201, 151, 0.35);
            --text-light: #ffffff;
        }

        * {
            box-sizing: border-box;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 80% 20%, #0d3868 0%, #06162a 100%);
            color: var(--text-light);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 1. KIOSK HEADER */
        .kiosk-header {
            background: linear-gradient(90deg, #003624 0%, #006644 50%, #004d33 100%);
            padding: 12px 28px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.5);
            border-bottom: 2.5px solid rgba(32, 201, 151, 0.45);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
            flex-shrink: 0;
        }
        .clinic-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 25px;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.1;
        }
        .clinic-tagline {
            font-size: 13px;
            color: #b3ffdf;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .digital-clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 800;
            color: #fffae6;
            background: rgba(0,0,0,0.4);
            padding: 4px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.25);
            letter-spacing: 1px;
        }

        /* 2. PROGRESS STEP BREADCRUMBS */
        .kiosk-progress-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(4, 17, 34, 0.7);
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .step-pill {
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            margin: 0 12px;
            transition: all 0.3s;
        }
        .step-pill.active {
            color: #20c997;
        }
        .step-pill.active .step-num {
            background: #20c997;
            color: #06152b;
            box-shadow: 0 0 12px rgba(32, 201, 151, 0.6);
        }
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 13px;
        }

        /* 3. MAIN KIOSK BODY */
        .kiosk-main-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* BIG TOUCH ACTION CARDS (HOME SCREEN) */
        .touch-action-card {
            background: var(--card-glass);
            border: 2.5px solid var(--border-glass);
            border-radius: 24px;
            padding: 42px 32px;
            text-align: center;
            box-shadow: 0 16px 40px rgba(0,0,0,0.45);
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(14px);
        }
        .touch-action-card:hover, .touch-action-card:active {
            transform: translateY(-8px) scale(1.02);
            border-color: #20c997;
            box-shadow: 0 22px 50px rgba(32, 201, 151, 0.45);
        }
        .touch-card-icon {
            width: 105px;
            height: 105px;
            border-radius: 50%;
            background: rgba(32, 201, 151, 0.18);
            color: #20c997;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin-bottom: 22px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }
        .card-gold .touch-card-icon {
            background: rgba(255, 171, 0, 0.18);
            color: #ffab00;
        }
        .card-gold:hover, .card-gold:active {
            border-color: #ffab00 !important;
            box-shadow: 0 22px 50px rgba(255, 171, 0, 0.45) !important;
        }

        .touch-card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 10px;
            line-height: 1.2;
        }
        .touch-card-desc {
            font-size: 15.5px;
            color: #cbd5e1;
            line-height: 1.45;
            max-width: 90%;
        }

        /* SECTION / STEP PANELS */
        .kiosk-step-panel {
            width: 100%;
            max-width: 1120px;
            background: var(--card-glass);
            border: 2px solid var(--border-glass);
            border-radius: 26px;
            padding: 34px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.55);
            backdrop-filter: blur(16px);
            display: none;
            animation: panelFadeIn 0.35s ease;
        }
        .kiosk-step-panel.active {
            display: block;
        }
        @keyframes panelFadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        .step-header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 29px;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 6px;
        }
        .step-header-sub {
            font-size: 15.5px;
            color: #94a3b8;
            margin-bottom: 22px;
        }

        /* TOUCH INPUT FIELD */
        .kiosk-input-large {
            font-family: 'JetBrains Mono', monospace;
            font-size: 30px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 2px;
            height: 68px;
            border-radius: 16px;
            border: 2.5px solid #20c997;
            background: rgba(4, 16, 32, 0.95);
            color: #ffffff;
            box-shadow: inset 0 3px 12px rgba(0,0,0,0.6);
        }

        /* TOUCH KEYBOARD (NUMERIC & FULL QWERTY) */
        .keyboard-mode-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }
        .keypad-grid-num {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            max-width: 440px;
            margin: 0 auto;
        }
        .keypad-grid-qwerty {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 820px;
            margin: 0 auto;
        }
        .qwerty-row {
            display: flex;
            justify-content: center;
            gap: 6px;
        }
        .btn-keypad {
            height: 64px;
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            border-radius: 14px;
            background: rgba(14, 40, 75, 0.95);
            color: #ffffff;
            border: 1.5px solid rgba(32, 201, 151, 0.35);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 50px;
        }
        .btn-keypad:hover, .btn-keypad:active {
            background: #20c997;
            color: #06152b;
            transform: scale(1.04);
        }
        .btn-keypad-action {
            background: rgba(220, 38, 38, 0.3);
            color: #fca5a5;
            border-color: rgba(220, 38, 38, 0.5);
            font-size: 20px;
        }
        .btn-keypad-action:hover, .btn-keypad-action:active {
            background: #ef4444;
            color: #ffffff;
        }

        /* CANDIDATE PATIENTS GRID */
        .candidates-list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 14px;
            max-height: 380px;
            overflow-y: auto;
            margin-top: 15px;
            padding-right: 6px;
        }
        .patient-candidate-card {
            background: rgba(14, 40, 75, 0.9);
            border: 2px solid rgba(32, 201, 151, 0.4);
            border-radius: 16px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
            position: relative;
        }
        .patient-candidate-card:hover, .patient-candidate-card:active {
            border-color: #ffab00;
            background: rgba(0, 135, 90, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(32, 201, 151, 0.4);
        }

        /* SELECTION GRID (POLI & DOCTORS) */
        .selection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 14px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 6px;
        }
        .touch-select-item {
            background: rgba(14, 40, 75, 0.88);
            border: 2px solid rgba(32, 201, 151, 0.35);
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 120px;
        }
        .touch-select-item:hover, .touch-select-item.selected {
            background: rgba(0, 135, 90, 0.4);
            border-color: #20c997;
            box-shadow: 0 0 22px rgba(32, 201, 151, 0.45);
            transform: scale(1.02);
        }
        .touch-select-item.selected {
            border-color: #ffab00;
            background: rgba(255, 171, 0, 0.18);
        }
        .item-icon {
            font-size: 32px;
            color: #20c997;
            margin-bottom: 8px;
        }
        .touch-select-item.selected .item-icon {
            color: #ffab00;
        }
        .item-title {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }
        .item-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* THERMAL TICKET PRINT BOX */
        .thermal-ticket-box {
            background: #ffffff;
            color: #000000;
            font-family: 'Courier New', Courier, monospace;
            width: 320px;
            margin: 0 auto;
            padding: 22px 18px;
            border-radius: 8px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.65);
            text-align: center;
            line-height: 1.25;
            border-top: 5px dashed #666;
            border-bottom: 5px dashed #666;
        }
        .ticket-clinic-name {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .ticket-tagline {
            font-size: 10px;
            color: #444;
            margin-bottom: 8px;
        }
        .ticket-queue-no {
            font-size: 54px;
            font-weight: 900;
            line-height: 1;
            margin: 12px 0 6px;
            letter-spacing: 2px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
        }
        .ticket-patient-name {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 2px;
        }
        .ticket-target-poli {
            font-size: 14.5px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        /* 4. KIOSK FOOTER */
        .kiosk-footer {
            background: #002216;
            border-top: 2px solid #20c997;
            padding: 8px 30px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            height: 48px;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* QR Scanner Modal */
        #qr-reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .thermal-ticket-box, .thermal-ticket-box * {
                visibility: visible;
            }
            .thermal-ticket-box {
                position: fixed;
                left: 0;
                top: 0;
                width: 80mm;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
<body>

<!-- 1. KIOSK TOP HEADER BAR -->
<div class="kiosk-header">
    <div class="d-flex align-items-center">
        <?php if (clinic_logo()): ?>
            <img src="<?= clinic_logo() ?>" alt="Logo" class="mr-3" style="max-height: 46px; max-width: 140px; object-fit: contain;">
        <?php else: ?>
            <i class="fas fa-hospital-user fa-2x mr-3 text-warning"></i>
        <?php endif; ?>
        <div>
            <div class="clinic-brand"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></div>
            <div class="clinic-tagline"><i class="fas fa-desktop mr-1 text-teal"></i> Anjungan Pendaftaran Mandiri (APM) Layar Sentuh</div>
        </div>
    </div>
    <div class="d-flex align-items-center">
        <div class="mr-3 text-right d-none d-md-block">
            <div id="live-date" class="font-weight-bold" style="font-size: 13px; color: #ffffff;"></div>
            <div style="font-size: 11px; color: #b3ffdf;">Sumbawa Besar (WITA)</div>
        </div>
        <div class="digital-clock mr-3" id="live-clock">00:00:00</div>
        <button type="button" class="btn btn-sm btn-outline-light font-weight-bold mr-2" onclick="goHomeKiosk();" title="Kembali ke Beranda">
            <i class="fas fa-home"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-light font-weight-bold" onclick="toggleFullScreen();" title="Layar Penuh">
            <i class="fas fa-expand"></i>
        </button>
    </div>
</div>

<!-- 2. STEP PROGRESS TRACKER -->
<div class="kiosk-progress-bar">
    <div class="step-pill active" id="prog-step-1"><span class="step-num">1</span> Kategori Pendaftaran</div>
    <i class="fas fa-chevron-right text-muted mx-2"></i>
    <div class="step-pill" id="prog-step-2"><span class="step-num">2</span> Identifikasi Pasien</div>
    <i class="fas fa-chevron-right text-muted mx-2"></i>
    <div class="step-pill" id="prog-step-3"><span class="step-num">3</span> Pilih Layanan &amp; Dokter</div>
    <i class="fas fa-chevron-right text-muted mx-2"></i>
    <div class="step-pill" id="prog-step-4"><span class="step-num">4</span> Tiket Antrean</div>
</div>

<!-- 3. MAIN KIOSK BODY (TOUCH INTERACTION STEPS) -->
<div class="kiosk-main-body">

    <!-- STEP 1: WELCOME SCREEN (CHOICE: PASIEN LAMA VS PASIEN BARU) -->
    <div class="kiosk-step-panel active" id="step-1-welcome" style="max-width: 980px;">
        <div class="text-center mb-4">
            <h1 style="font-family: 'Outfit', sans-serif; font-weight: 900; font-size: 36px; color: #ffffff; margin-bottom: 8px;">
                Selamat Datang di Anjungan Mandiri
            </h1>
            <p style="font-size: 18px; color: #b3ffdf;">
                Silakan pilih kategori pendaftaran untuk mencetak nomor antrean berobat Anda:
            </p>
        </div>

        <div class="row">
            <!-- Pilihan 1: Pasien Lama (Cepat) -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="touch-action-card" onclick="goToStep('step-2-existing', 2);">
                    <div class="touch-card-icon"><i class="fas fa-id-card"></i></div>
                    <div class="touch-card-title">PASIEN LAMA</div>
                    <div class="touch-card-desc">Sudah pernah berobat sebelumnya / Memiliki NIK KTP, Nomor Rekam Medis (No RM), atau Kartu Pasien.</div>
                    <button class="btn btn-teal btn-lg font-weight-bold px-4 mt-3" style="border-radius: 30px;">
                        <i class="fas fa-arrow-right mr-1"></i> Masuk dengan NIK / RM / QR
                    </button>
                </div>
            </div>

            <!-- Pilihan 2: Pasien Baru -->
            <div class="col-md-6">
                <div class="touch-action-card card-gold" onclick="goToStep('step-2-new-patient', 2);">
                    <div class="touch-card-icon"><i class="fas fa-user-plus"></i></div>
                    <div class="touch-card-title">PASIEN BARU</div>
                    <div class="touch-card-desc">Belum pernah berobat / Pertama kali berkunjung ke Sawamawa Medical Center.</div>
                    <button class="btn btn-warning btn-lg font-weight-bold px-4 mt-3" style="border-radius: 30px;">
                        <i class="fas fa-plus-circle mr-1"></i> Daftar Pasien Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2A: IDENTIFIKASI PASIEN LAMA (VIRTUAL KEYBOARD / QR SCANNER) -->
    <div class="kiosk-step-panel" id="step-2-existing" style="max-width: 860px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-outline-light font-weight-bold px-3 py-2" onclick="goToStep('step-1-welcome', 1);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
            <!-- Tombol Scan QR Kamera -->
            <button type="button" class="btn btn-warning font-weight-bold px-3 py-2 shadow-sm" onclick="startQrScannerModal();">
                <i class="fas fa-qrcode mr-1"></i> Pindai QR Kartu Pasien
            </button>
        </div>

        <div class="text-center">
            <h2 class="step-header-title">Cari Data Pasien Berobat</h2>
            <p class="step-header-sub">Ketik 16 Digit NIK KTP, Nomor RM (contoh: 1 / RM-000001), atau Nama Pasien:</p>

            <div class="input-group mb-2" style="max-width: 600px; margin: 0 auto;">
                <input type="text" id="kiosk-search-input" class="form-control kiosk-input-large" placeholder="NIK / No. RM / Nama..." readonly>
                <div class="input-group-append">
                    <button class="btn btn-danger font-weight-bold px-4" type="button" onclick="pressKey('CLEAR')" style="border-radius: 0 16px 16px 0; font-size: 20px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div id="search-error-msg" class="alert alert-danger font-weight-bold py-2 mt-2 mb-2" style="display: none; max-width: 600px; margin: 0 auto;"></div>

            <!-- Keyboard Mode Switcher -->
            <div class="keyboard-mode-tabs mt-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-teal active font-weight-bold px-4" id="tab-key-num" onclick="switchKeyboardMode('num');">
                        🔢 123 Angka (NIK / No RM)
                    </button>
                    <button type="button" class="btn btn-outline-light font-weight-bold px-4" id="tab-key-qwerty" onclick="switchKeyboardMode('qwerty');">
                        🔤 ABC Huruf (Nama Pasien)
                    </button>
                </div>
            </div>

            <!-- 1. NUMERIC KEYPAD -->
            <div id="keyboard-numeric" class="keypad-grid-num mt-2">
                <button type="button" class="btn-keypad" onclick="pressKey('1')">1</button>
                <button type="button" class="btn-keypad" onclick="pressKey('2')">2</button>
                <button type="button" class="btn-keypad" onclick="pressKey('3')">3</button>
                <button type="button" class="btn-keypad" onclick="pressKey('4')">4</button>
                <button type="button" class="btn-keypad" onclick="pressKey('5')">5</button>
                <button type="button" class="btn-keypad" onclick="pressKey('6')">6</button>
                <button type="button" class="btn-keypad" onclick="pressKey('7')">7</button>
                <button type="button" class="btn-keypad" onclick="pressKey('8')">8</button>
                <button type="button" class="btn-keypad" onclick="pressKey('9')">9</button>
                <button type="button" class="btn-keypad" onclick="pressKey('RM-')">RM-</button>
                <button type="button" class="btn-keypad" onclick="pressKey('0')">0</button>
                <button type="button" class="btn-keypad btn-keypad-action" onclick="pressKey('BACKSPACE')"><i class="fas fa-delete-left"></i></button>
            </div>

            <!-- 2. QWERTY KEYBOARD -->
            <div id="keyboard-qwerty" class="keypad-grid-qwerty mt-2" style="display: none;">
                <div class="qwerty-row">
                    <button type="button" class="btn-keypad" onclick="pressKey('Q')">Q</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('W')">W</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('E')">E</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('R')">R</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('T')">T</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('Y')">Y</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('U')">U</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('I')">I</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('O')">O</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('P')">P</button>
                </div>
                <div class="qwerty-row">
                    <button type="button" class="btn-keypad" onclick="pressKey('A')">A</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('S')">S</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('D')">D</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('F')">F</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('G')">G</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('H')">H</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('J')">J</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('K')">K</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('L')">L</button>
                </div>
                <div class="qwerty-row">
                    <button type="button" class="btn-keypad" onclick="pressKey('Z')">Z</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('X')">X</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('C')">C</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('V')">V</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('B')">B</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('N')">N</button>
                    <button type="button" class="btn-keypad" onclick="pressKey('M')">M</button>
                    <button type="button" class="btn-keypad" onclick="pressKey(' ')">SPASI</button>
                    <button type="button" class="btn-keypad btn-keypad-action" onclick="pressKey('BACKSPACE')"><i class="fas fa-delete-left"></i></button>
                </div>
            </div>

            <!-- Candidate Patients Section -->
            <div id="candidates-container" style="display: none; max-width: 780px; margin: 20px auto 0;">
                <div class="alert alert-info py-2 font-weight-bold mb-2">
                    <i class="fas fa-users mr-1"></i> <span id="candidates-count-text">Ditemukan data pasien yang cocok. Sentuh nama Anda:</span>
                </div>
                <div class="candidates-list-grid" id="candidates-grid">
                    <!-- Populated via AJAX -->
                </div>
            </div>

            <button type="button" class="btn btn-success btn-lg font-weight-bold mt-3 py-3 px-5 shadow" id="btn-submit-search" onclick="searchExistingPatient();" style="border-radius: 20px; font-size: 20px; min-width: 320px;">
                <i class="fas fa-search mr-2"></i> CARI DATA PASIEN
            </button>
        </div>
    </div>

    <!-- STEP 2B: PENDAFTARAN PASIEN BARU (TOUCH FORM) -->
    <div class="kiosk-step-panel" id="step-2-new-patient" style="max-width: 920px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-outline-light font-weight-bold" onclick="goToStep('step-1-welcome', 1);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </button>
            <div class="badge badge-warning text-dark px-3 py-2 font-weight-bold" style="font-size: 13px;">FORM PASIEN BARU</div>
        </div>

        <h2 class="step-header-title">Isi Data Diri Pasien Baru</h2>
        <p class="step-header-sub">Lengkapi formulir singkat di bawah ini:</p>

        <form id="form-kiosk-new-patient">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-white">NIK KTP (16 Digit) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" id="new-nik" class="form-control bg-dark text-white border-secondary font-weight-bold" required maxlength="16" placeholder="Nomor Induk Kependudukan" style="height: 52px; font-size: 17px;">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-white">Nama Lengkap Pasien <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="new-name" class="form-control bg-dark text-white border-secondary font-weight-bold" required placeholder="Nama Lengkap sesuai KTP" style="height: 52px; font-size: 17px;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold text-white">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="gender" id="new-gender" class="form-control bg-dark text-white border-secondary font-weight-bold" style="height: 52px; font-size: 16px;">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold text-white">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" id="new-dob" class="form-control bg-dark text-white border-secondary font-weight-bold" required style="height: 52px; font-size: 16px;">
                </div>
                <div class="col-md-4 form-group">
                    <label class="font-weight-bold text-white">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="new-phone" class="form-control bg-dark text-white border-secondary font-weight-bold" required placeholder="08..." style="height: 52px; font-size: 16px;">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-white">Alamat Tempat Tinggal <span class="text-danger">*</span></label>
                    <input type="text" name="address" id="new-address" class="form-control bg-dark text-white border-secondary font-weight-bold" required placeholder="Kelurahan / Desa / Kecamatan" style="height: 52px; font-size: 16px;">
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-white">Pilihan Golongan Keanggotaan <span class="text-danger">*</span></label>
                    <select name="membership_tier" id="new-tier" class="form-control bg-dark text-white border-secondary font-weight-bold" style="height: 52px; font-size: 16px;">
                        <option value="regular" selected>🟢 Reguler (Standar)</option>
                        <option value="gold">🟡 Gold Member (Prioritas / Lansia / Hamil)</option>
                        <option value="vip">⚫ Platinum / VIP (Eksekutif)</option>
                    </select>
                </div>
            </div>

            <button type="button" class="btn btn-teal btn-lg btn-block font-weight-bold mt-3 py-3" onclick="proceedNewPatientToService();" style="border-radius: 18px; font-size: 19px;">
                Lanjutkan Pilih Poliklinik &amp; Dokter <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>
    </div>

    <!-- STEP 3: PILIH POLIKLINIK, TINDAKAN & DOKTER -->
    <div class="kiosk-step-panel" id="step-3-service" style="max-width: 1060px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-outline-light font-weight-bold" onclick="goToStep('step-1-welcome', 1);">
                <i class="fas fa-arrow-left mr-1"></i> Batal / Beranda
            </button>
            <div class="badge badge-teal px-3 py-2 font-weight-bold" style="font-size: 13px;">LANGKAH 2 DARI 3</div>
        </div>

        <!-- Verified Patient Header Banner -->
        <div class="p-3 mb-3 rounded border border-success d-flex justify-content-between align-items-center" style="background: rgba(0, 135, 90, 0.25);">
            <div>
                <span class="text-xs text-muted text-uppercase font-weight-bold">PASIEN TERVERIFIKASI:</span>
                <div class="h5 font-weight-bold text-white mb-0" id="verified-patient-name">-</div>
                <small class="text-teal font-monospace" id="verified-patient-rm">-</small>
            </div>
            <div id="verified-patient-badge">
                <span class="badge badge-success px-3 py-2 font-weight-bold">REGULER</span>
            </div>
        </div>

        <!-- Tabs: Poliklinik Dokter vs Ruang Tindakan -->
        <ul class="nav nav-pills mb-3" id="pills-tab-service" role="tablist">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold px-4 py-2" id="tab-poly-btn" data-toggle="pill" href="#pane-poly" role="tab" onclick="setVisitType('poli');">
                    <i class="fas fa-stethoscope mr-2"></i> 1. Pemeriksaan Dokter Poliklinik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-4 py-2" id="tab-tindakan-btn" data-toggle="pill" href="#pane-tindakan" role="tab" onclick="setVisitType('tindakan');">
                    <i class="fas fa-syringe mr-2"></i> 2. Tindakan / Pelayanan Khusus
                </a>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContentService">
            <!-- Pane 1: Poliklinik -->
            <div class="tab-pane fade show active" id="pane-poly" role="tabpanel">
                <label class="font-weight-bold text-white mb-2">Sentuh &amp; Pilih Poliklinik yang Dituju:</label>
                <div class="selection-grid mb-3">
                    <?php foreach ($polyclinics as $p): ?>
                        <div class="touch-select-item item-poly" data-poly-id="<?= $p->id ?>" onclick="selectPolyclinic(<?= $p->id ?>, '<?= esc($p->name) ?>');">
                            <i class="fas fa-stethoscope item-icon"></i>
                            <div class="item-title">Poli <?= esc($p->name) ?></div>
                            <div class="item-sub">Pemeriksaan Dokter</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="doctor-selection-box" style="display: none;">
                    <label class="font-weight-bold text-white mb-2"><i class="fas fa-user-md text-warning mr-1"></i> Pilih Dokter Pemeriksa:</label>
                    <div class="selection-grid mb-3" id="doctor-grid-container">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <!-- Pane 2: Tindakan Medis Saja -->
            <div class="tab-pane fade" id="pane-tindakan" role="tabpanel">
                <label class="font-weight-bold text-white mb-2">Sentuh &amp; Pilih Tindakan / Layanan Medis:</label>
                <div class="selection-grid mb-3">
                    <?php foreach ($tindakanServices as $t): ?>
                        <div class="touch-select-item item-tindakan" data-tindakan-id="<?= $t->id ?>" onclick="selectTindakan(<?= $t->id ?>, '<?= esc($t->name) ?>');">
                            <i class="fas fa-heart-pulse item-icon"></i>
                            <div class="item-title"><?= esc($t->name) ?></div>
                            <div class="item-sub"><?= esc($t->parent_name ?: 'Layanan Medis') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Pilihan Metode Pembayaran & Tombol Cetak -->
        <div class="row align-items-center mt-3 pt-3 border-top border-secondary">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="font-weight-bold text-white text-xs text-uppercase mb-1">Metode Penjamin / Pembayaran:</label>
                <select id="kiosk-payment-method" class="form-control bg-dark text-white border-secondary font-weight-bold" style="height: 52px; font-size: 16px;">
                    <option value="Tunai / Umum" selected>💵 Pasien Umum (Tunai / Non-BPJS)</option>
                    <option value="BPJS Kesehatan">🛡️ BPJS Kesehatan (JKN-KIS)</option>
                    <option value="Asuransi Swasta / Perusahaan">🏢 Asuransi Rekanan Swasta / Korporat</option>
                </select>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-warning btn-lg btn-block font-weight-bold py-3 shadow" id="btn-process-kiosk-ticket" onclick="processFinalKioskTicket();" disabled style="border-radius: 18px; font-size: 20px;">
                    <i class="fas fa-print mr-2"></i> CETAK NOMOR ANTREAN
                </button>
            </div>
        </div>
    </div>

    <!-- STEP 4: TIKET ANTREAN BERHASIL & AUTO PRINT -->
    <div class="kiosk-step-panel" id="step-4-ticket" style="max-width: 600px;">
        <div class="text-center mb-3">
            <div style="font-size: 48px; color: #20c997;" class="mb-2">
                <i class="fas fa-circle-check"></i>
            </div>
            <h2 class="step-header-title">Pendaftaran Mandiri Berhasil!</h2>
            <p class="step-header-sub">Silakan ambil struk nomor antrean Anda di bawah ini dan silakan menunggu di ruang tunggu.</p>
        </div>

        <!-- THERMAL TICKET DESIGN -->
        <div class="thermal-ticket-box" id="print-ticket-area">
            <div class="ticket-clinic-name" id="ticket-clinic-name"><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></div>
            <div class="ticket-tagline" id="ticket-clinic-tagline"><?= esc(clinic_setting('clinic_tagline', 'Layanan Medis Terpadu & Paripurna')) ?></div>
            <div style="font-size: 11px; border-bottom: 1px dashed #000; padding-bottom: 4px;" id="ticket-datetime">-</div>

            <div class="ticket-queue-no" id="ticket-queue-num">--</div>

            <div class="ticket-patient-name" id="ticket-patient-name">-</div>
            <div style="font-size: 12px; color: #444; margin-bottom: 6px;" id="ticket-patient-rm">No RM: -</div>

            <div class="ticket-target-poli" id="ticket-target-name">Poliklinik Umum</div>
            <div style="font-size: 11.5px; color: #222; margin-bottom: 10px;" id="ticket-doctor-name">Dokter Jaga</div>

            <div style="background: #f1f5f9; padding: 6px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 10px;">
                Antrean di Depan Anda: <span id="ticket-waiting-count">0</span> Orang
            </div>

            <div style="font-size: 9px; color: #555;">
                Harap perhatikan nomor antrean pada Layar Display TV. Jagalah kebersihan dan ketertiban bersama.
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-outline-light font-weight-bold mr-2" onclick="window.print();">
                <i class="fas fa-print mr-1"></i> Cetak Ulang Struk
            </button>
            <button type="button" class="btn btn-teal font-weight-bold px-4" onclick="goHomeKiosk();">
                <i class="fas fa-home mr-1"></i> Selesai &amp; Kembali ke Awal (<span id="ticket-countdown">8</span>s)
            </button>
        </div>
    </div>

</div>

<!-- 4. MODAL SCAN QR CODE CAMERA -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-white border-teal">
            <div class="modal-header border-secondary py-3">
                <h5 class="modal-title font-weight-bold" id="qrScannerModalLabel">
                    <i class="fas fa-camera text-warning mr-2"></i> Pindai QR Code Kartu Pasien
                </h5>
                <button type="button" class="close text-white" onclick="stopQrScannerModal();" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <p class="text-xs text-muted mb-2">Arahkan QR Code pada kartu identitas pasien atau smartphone Anda ke kamera:</p>
                <div id="qr-reader" style="max-width: 380px; margin: 0 auto; background: #000;"></div>
                <div id="qr-scan-result" class="text-success font-weight-bold mt-2" style="display: none;"></div>
            </div>
            <div class="modal-footer border-secondary py-2">
                <button type="button" class="btn btn-secondary btn-sm" onclick="stopQrScannerModal();">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 5. KIOSK FOOTER -->
<div class="kiosk-footer">
    <div>
        <i class="fas fa-shield-alt text-warning mr-1"></i> Terkoneksi Rekam Medis Digital &amp; Layar TV Display Real-Time
    </div>
    <div>
        Sawamawa Smart Kiosk APM &copy; <?= date('Y') ?>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/voice_caller.js') ?>"></script>
<script>
    const allDoctors = <?= json_encode($doctors) ?>;
    let selectedPatientId = null;
    let selectedPatientTier = 'regular';
    let isNewPatient = false;
    let currentVisitType = 'poli';
    let selectedPolyId = null;
    let selectedDoctorId = null;
    let selectedServiceId = null;
    let countdownTimer = null;
    let html5QrScanner = null;

    // 1. LIVE CLOCK
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

    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => console.log(err));
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // 2. STEP NAVIGATION
    function goToStep(stepId, stepNum) {
        $('.kiosk-step-panel').removeClass('active');
        $('#' + stepId).addClass('active');
        $('#search-error-msg').hide();

        if (stepNum) {
            $('.step-pill').removeClass('active');
            for (let i = 1; i <= stepNum; i++) {
                $('#prog-step-' + i).addClass('active');
            }
        }
    }

    function goHomeKiosk() {
        if (countdownTimer) clearInterval(countdownTimer);
        selectedPatientId = null;
        isNewPatient = false;
        selectedPolyId = null;
        selectedDoctorId = null;
        selectedServiceId = null;
        $('#kiosk-search-input').val('');
        $('#candidates-container').hide();
        $('#candidates-grid').empty();
        $('#form-kiosk-new-patient')[0].reset();
        $('.touch-select-item').removeClass('selected');
        $('#doctor-selection-box').hide();
        $('#btn-process-kiosk-ticket').prop('disabled', true);
        goToStep('step-1-welcome', 1);
    }

    // 3. VIRTUAL KEYBOARD SWITCHER & INPUT
    function switchKeyboardMode(mode) {
        if (mode === 'num') {
            $('#keyboard-numeric').show();
            $('#keyboard-qwerty').hide();
            $('#tab-key-num').addClass('active btn-teal').removeClass('btn-outline-light');
            $('#tab-key-qwerty').removeClass('active btn-teal').addClass('btn-outline-light');
        } else {
            $('#keyboard-numeric').hide();
            $('#keyboard-qwerty').show();
            $('#tab-key-qwerty').addClass('active btn-teal').removeClass('btn-outline-light');
            $('#tab-key-num').removeClass('active btn-teal').addClass('btn-outline-light');
        }
    }

    function pressKey(key) {
        const input = $('#kiosk-search-input');
        let val = input.val();

        if (key === 'CLEAR') {
            val = '';
        } else if (key === 'BACKSPACE') {
            val = val.slice(0, -1);
        } else {
            if (val.length < 30) {
                val += key;
            }
        }
        input.val(val);
        $('#search-error-msg').hide();
        $('#candidates-container').hide();
    }

    // 4. SEARCH EXISTING PATIENT (INTELLIGENT SEARCH & CANDIDATE PICKER)
    function searchExistingPatient() {
        const keyword = $('#kiosk-search-input').val().trim();
        if (!keyword) {
            $('#search-error-msg').text('Silakan ketik NIK, Nomor RM, atau Nama Pasien terlebih dahulu.').show();
            return;
        }

        const btn = $('#btn-submit-search');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> MENCARI DATA...');

        $.ajax({
            url: '<?= base_url('klinik/kiosk-check-patient') ?>',
            type: 'POST',
            data: {
                keyword: keyword,
                '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-search mr-2"></i> CARI DATA PASIEN');

                if (res.status === 'success' && res.patient) {
                    // Exact single patient match
                    selectPatientFromData(res.patient);
                } else if (res.status === 'candidates' && res.candidates && res.candidates.length > 0) {
                    // Multiple candidates found
                    renderCandidates(res.candidates);
                } else {
                    $('#search-error-msg').text(res.message || 'Data pasien tidak ditemukan. Silakan periksa kembali kata kunci atau pilih Pasien Baru.').show();
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-search mr-2"></i> CARI DATA PASIEN');
                $('#search-error-msg').text('Gagal menghubungi server. Silakan coba lagi.').show();
            }
        });
    }

    function renderCandidates(candidates) {
        const grid = $('#candidates-grid');
        grid.empty();

        candidates.forEach(function(p) {
            let tierBadge = '<span class="badge badge-success px-2 py-1">REGULER</span>';
            if (p.membership_tier === 'gold') {
                tierBadge = '<span class="badge badge-warning text-dark px-2 py-1 font-weight-bold"><i class="fas fa-crown mr-1"></i>GOLD</span>';
            } else if (p.membership_tier === 'vip' || p.membership_tier === 'platinum') {
                tierBadge = '<span class="badge badge-dark px-2 py-1 font-weight-bold" style="border: 1px solid #ffc107; color: #ffc107;"><i class="fas fa-gem mr-1"></i>VIP</span>';
            }

            const card = $(`
                <div class="patient-candidate-card" onclick='selectPatientFromData(${JSON.stringify(p)})'>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-white" style="font-size: 16px;">${p.name}</strong>
                        ${tierBadge}
                    </div>
                    <div style="font-size: 12.5px; color: #20c997; font-family: monospace; font-weight: bold;">
                        No RM: ${p.no_rm}
                    </div>
                    <div style="font-size: 11.5px; color: #cbd5e1;">
                        NIK: ${p.masked_nik} | Tgl Lahir: ${p.dob} (${p.age} Thn)
                    </div>
                </div>
            `);
            grid.append(card);
        });

        $('#candidates-count-text').text('Ditemukan ' + candidates.length + ' data pasien yang cocok. Sentuh nama Anda di bawah ini:');
        $('#candidates-container').fadeIn(250);
    }

    function selectPatientFromData(p) {
        selectedPatientId = p.id;
        selectedPatientTier = p.membership_tier || 'regular';
        isNewPatient = false;

        $('#verified-patient-name').text(p.name + ' (' + p.gender + ' / ' + p.age + ' Thn)');
        $('#verified-patient-rm').text('No RM: ' + p.no_rm + ' | NIK: ' + (p.nik || '-'));

        let tierBadgeHtml = '<span class="badge badge-success px-3 py-2 font-weight-bold">REGULER</span>';
        if (selectedPatientTier === 'gold') {
            tierBadgeHtml = '<span class="badge badge-warning text-dark px-3 py-2 font-weight-bold shadow-sm" style="background:#ffc107;"><i class="fas fa-crown mr-1"></i> GOLD MEMBER</span>';
        } else if (selectedPatientTier === 'vip' || selectedPatientTier === 'platinum') {
            tierBadgeHtml = '<span class="badge badge-dark px-3 py-2 font-weight-bold shadow-sm" style="background:#0f172a; border: 1.5px solid #ffc107; color: #ffc107;"><i class="fas fa-gem mr-1"></i> VIP EXECUTIVE</span>';
        }
        $('#verified-patient-badge').html(tierBadgeHtml);

        goToStep('step-3-service', 3);
    }

    // 5. PROCEED NEW PATIENT
    function proceedNewPatientToService() {
        const nik = $('#new-nik').val().trim();
        const name = $('#new-name').val().trim();
        const phone = $('#new-phone').val().trim();
        const dob = $('#new-dob').val();

        if (!nik || !name || !phone || !dob) {
            alert('Harap lengkapi NIK, Nama Lengkap, Tanggal Lahir, dan No. WhatsApp.');
            return;
        }

        isNewPatient = true;
        selectedPatientTier = $('#new-tier').val() || 'regular';

        $('#verified-patient-name').text(name + ' (Pasien Baru)');
        $('#verified-patient-rm').text('NIK: ' + nik + ' (No RM akan digenerate otomatis)');

        let tierBadgeHtml = '<span class="badge badge-success px-3 py-2 font-weight-bold">REGULER</span>';
        if (selectedPatientTier === 'gold') {
            tierBadgeHtml = '<span class="badge badge-warning text-dark px-3 py-2 font-weight-bold shadow-sm" style="background:#ffc107;"><i class="fas fa-crown mr-1"></i> GOLD MEMBER</span>';
        } else if (selectedPatientTier === 'vip' || selectedPatientTier === 'platinum') {
            tierBadgeHtml = '<span class="badge badge-dark px-3 py-2 font-weight-bold shadow-sm" style="background:#0f172a; border: 1.5px solid #ffc107; color: #ffc107;"><i class="fas fa-gem mr-1"></i> VIP EXECUTIVE</span>';
        }
        $('#verified-patient-badge').html(tierBadgeHtml);

        goToStep('step-3-service', 3);
    }

    // 6. SERVICE SELECTION LOGIC
    function setVisitType(type) {
        currentVisitType = type;
        selectedPolyId = null;
        selectedDoctorId = null;
        selectedServiceId = null;
        $('.touch-select-item').removeClass('selected');
        $('#doctor-selection-box').hide();
        $('#btn-process-kiosk-ticket').prop('disabled', true);
    }

    function selectPolyclinic(polyId, polyName) {
        selectedPolyId = polyId;
        $('.item-poly').removeClass('selected');
        $('.item-poly[data-poly-id="' + polyId + '"]').addClass('selected');

        const filteredDocs = allDoctors.filter(d => (!d.poly_id || d.poly_id == polyId));
        const grid = $('#doctor-grid-container');
        grid.empty();

        if (filteredDocs.length > 0) {
            filteredDocs.forEach(function(doc, idx) {
                const autoSel = (idx === 0) ? 'selected' : '';
                if (idx === 0) selectedDoctorId = doc.id;

                grid.append(
                    '<div class="touch-select-item item-doc ' + autoSel + '" data-doc-id="' + doc.id + '" onclick="selectDoctor(' + doc.id + ');">' +
                    '<i class="fas fa-user-md item-icon"></i>' +
                    '<div class="item-title">' + doc.name + '</div>' +
                    '<div class="item-sub">' + (doc.specialization || 'Dokter Spesialis / Umum') + '</div>' +
                    '</div>'
                );
            });
            $('#doctor-selection-box').fadeIn(200);
            $('#btn-process-kiosk-ticket').prop('disabled', false);
        } else {
            selectedDoctorId = allDoctors.length > 0 ? allDoctors[0].id : null;
            $('#doctor-selection-box').hide();
            $('#btn-process-kiosk-ticket').prop('disabled', false);
        }
    }

    function selectDoctor(docId) {
        selectedDoctorId = docId;
        $('.item-doc').removeClass('selected');
        $('.item-doc[data-doc-id="' + docId + '"]').addClass('selected');
        $('#btn-process-kiosk-ticket').prop('disabled', false);
    }

    function selectTindakan(tindakanId, tindakanName) {
        selectedServiceId = tindakanId;
        selectedPolyId = null;
        selectedDoctorId = allDoctors.length > 0 ? allDoctors[0].id : null;

        $('.item-tindakan').removeClass('selected');
        $('.item-tindakan[data-tindakan-id="' + tindakanId + '"]').addClass('selected');
        $('#btn-process-kiosk-ticket').prop('disabled', false);
    }

    // 7. PROCESS FINAL KIOSK REGISTRATION & TICKET PRINT
    function processFinalKioskTicket() {
        const btn = $('#btn-process-kiosk-ticket');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> MEMBUAT NOMOR ANTREAN...');

        const paymentMethod = $('#kiosk-payment-method').val();

        if (isNewPatient) {
            const formData = $('#form-kiosk-new-patient').serialize() + 
                             '&visit_type=' + currentVisitType +
                             '&polyclinic_id=' + (selectedPolyId || '') +
                             '&doctor_id=' + (selectedDoctorId || '') +
                             '&service_id=' + (selectedServiceId || '') +
                             '&payment_method=' + encodeURIComponent(paymentMethod) +
                             '&<?= csrf_token() ?>=' + $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '<?= base_url('klinik/kiosk-register-new-patient') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-print mr-2"></i> CETAK NOMOR ANTREAN');
                    if (res.status === 'success' && res.ticket) {
                        displayKioskTicket(res.ticket);
                    } else {
                        alert(res.message || 'Gagal memproses pendaftaran antrean.');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-print mr-2"></i> CETAK NOMOR ANTREAN');
                    alert('Gagal menghubungi server pendaftaran.');
                }
            });
        } else {
            $.ajax({
                url: '<?= base_url('klinik/kiosk-register-visit') ?>',
                type: 'POST',
                data: {
                    patient_id: selectedPatientId,
                    visit_type: currentVisitType,
                    polyclinic_id: selectedPolyId,
                    doctor_id: selectedDoctorId,
                    service_id: selectedServiceId,
                    payment_method: paymentMethod,
                    '<?= csrf_token() ?>': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-print mr-2"></i> CETAK NOMOR ANTREAN');
                    if (res.status === 'success' && res.ticket) {
                        displayKioskTicket(res.ticket);
                    } else {
                        alert(res.message || 'Gagal memproses pendaftaran antrean.');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-print mr-2"></i> CETAK NOMOR ANTREAN');
                    alert('Gagal menghubungi server pendaftaran.');
                }
            });
        }
    }

    // 8. DISPLAY TICKET & COUNTDOWN
    function displayKioskTicket(t) {
        $('#ticket-datetime').text(t.created_at);
        $('#ticket-queue-num').text(t.queue_no);
        $('#ticket-patient-name').text(t.patient_name.toUpperCase());
        $('#ticket-patient-rm').text('No RM: ' + t.no_rm + ' (' + t.no_visit + ')');
        $('#ticket-target-name').text(t.target_name);
        $('#ticket-doctor-name').text(t.doctor_name);
        $('#ticket-waiting-count').text(t.waiting_count);

        goToStep('step-4-ticket', 4);

        // Play hospital chime & voice guidance
        playHospitalChime(function() {
            if ('speechSynthesis' in window) {
                const text = `Nomor antrean Anda adalah, ${formatQueueVoice(t.queue_no)}. Silakan ambil struk dan menunggu di ruang tunggu.`;
                const utt = new SpeechSynthesisUtterance(text);
                utt.lang = 'id-ID';
                utt.rate = 0.90;
                window.speechSynthesis.speak(utt);
            }
        });

        // Trigger Auto-Print Struk
        setTimeout(function() {
            window.print();
        }, 800);

        // Auto Countdown 8s
        let count = 8;
        $('#ticket-countdown').text(count);
        countdownTimer = setInterval(function() {
            count--;
            $('#ticket-countdown').text(count);
            if (count <= 0) {
                clearInterval(countdownTimer);
                goHomeKiosk();
            }
        }, 1000);
    }

    // 9. LIVE QR CODE SCANNER MODAL
    function startQrScannerModal() {
        $('#qrScannerModal').modal('show');
        $('#qr-scan-result').hide();

        setTimeout(function() {
            if (html5QrScanner === null) {
                html5QrScanner = new Html5Qrcode("qr-reader");
            }
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            html5QrScanner.start({ facingMode: "environment" }, config, onScanSuccess)
                .catch(err => {
                    console.log("Unable to start QR camera:", err);
                    $('#qr-scan-result').text('Kamera tidak terdeteksi atau izin akses ditolak.').show();
                });
        }, 400);
    }

    function onScanSuccess(decodedText) {
        $('#qr-scan-result').text('QR Berhasil Terbaca: ' + decodedText).show();
        stopQrScannerModal();
        $('#kiosk-search-input').val(decodedText);
        searchExistingPatient();
    }

    function stopQrScannerModal() {
        if (html5QrScanner) {
            html5QrScanner.stop().then(() => {
                $('#qrScannerModal').modal('hide');
            }).catch(err => {
                $('#qrScannerModal').modal('hide');
            });
        } else {
            $('#qrScannerModal').modal('hide');
        }
    }
</script>

</body>
</html>
