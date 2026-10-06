<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token & Base URL Meta for Dynamic AJAX/LiveSync Forms -->
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="base-url" content="<?= rtrim(base_url(), '/') ?>">
    <meta name="clinic-name" content="<?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?>">
    <meta name="voice-gender" content="<?= esc(clinic_setting('voice_gender', 'female')) ?>">
    <meta name="voice-chime" content="<?= esc(clinic_setting('voice_chime_type', 'hospital_2tone')) ?>">
    <meta name="voice-rate" content="<?= esc(clinic_setting('voice_rate', '0.85')) ?>">
    <meta name="voice-pitch" content="<?= esc(clinic_setting('voice_pitch', '1.0')) ?>">
    <meta name="voice-volume" content="<?= esc(clinic_setting('voice_volume', '1.0')) ?>">
    <title><?= esc($title ?? clinic_setting('clinic_name', 'GITRIA FARMA')) ?> - <?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></title>

    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
        <link rel="shortcut icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables & Responsive Extension -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive-bs4/2.4.1/responsive.bootstrap4.min.css">
    <!-- Select2 Searchable Dropdown -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <!-- Theme style (AdminLTE) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- Paper White Design System (desain_tabel.md) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=' . (file_exists(FCPATH . 'assets/css/custom.css') ? filemtime(FCPATH . 'assets/css/custom.css') : time())) ?>">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Inisialisasi Tema Instan Default: macOS 11 Big Sur (theme-macos)
        (function() {
            var savedTheme = localStorage.getItem('sawamawa_admin_theme') || 'theme-macos';
            document.documentElement.className = savedTheme;
        })();
    </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed theme-macos">
<script>
    (function() {
        var savedTheme = localStorage.getItem('sawamawa_admin_theme') || 'theme-macos';
        var sidebarState = localStorage.getItem('sawamawa_sidebar_state');
        var isSmallScreen = window.innerWidth < 992;
        var baseClasses = 'hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed';
        
        // Default adalah DIBUKA (Expanded). Hanya collapse jika layar kecil (< 992px) ATAU user sengaja memilih collapsed
        if (isSmallScreen || sidebarState === 'collapsed') {
            baseClasses += ' sidebar-collapse';
        }
        document.body.className = baseClasses + ' ' + savedTheme;
    })();
</script>
<!-- Top Smooth SPA Progress Bar (Zero-Reload Page Transitions) -->
<div id="spa-progressbar" style="position: fixed; top: 0; left: 0; width: 0%; height: 3px; background: linear-gradient(90deg, #0d9488, #10b981, #14b8a6); z-index: 99999; transition: width 0.2s ease-out; opacity: 0; pointer-events: none;"></div>

<!-- Page Preloader (Transparan Glassmorphism & Logo Resmi Perusahaan) -->
<div id="page-preloader" class="page-preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.70); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 99999; display: flex; align-items: center; justify-content: center; transition: opacity 0.25s ease, visibility 0.25s ease;">
    <div class="preloader-spinner-wrapper" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.95); padding: 26px 36px; border-radius: 22px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12), 0 3px 10px rgba(13, 148, 136, 0.08); border: 1px solid rgba(255, 255, 255, 0.9); min-width: 240px;">
        <div class="preloader-logo-container" style="position: relative; width: 76px; height: 76px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; background: #ffffff; border-radius: 50%; padding: 6px; box-shadow: 0 4px 14px rgba(0,0,0,0.06);">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" class="preloader-logo-img" style="max-width: 52px; max-height: 52px; width: auto; height: auto; object-fit: contain; display: block;">
            <?php else: ?>
                <span class="d-inline-flex align-items-center justify-content-center preloader-logo-img text-teal" style="font-size:32px;">
                    <i class="fas fa-hospital-user"></i>
                </span>
            <?php endif; ?>
            <div class="preloader-spinner" style="position: absolute; top: -3px; left: -3px; width: calc(100% + 6px); height: calc(100% + 6px); border: 3.5px solid rgba(13, 148, 136, 0.16); border-top-color: #0d9488; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
        </div>

        <div class="preloader-title" style="font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: 0.4px; margin-bottom: 2px;"><?= esc(clinic_setting('clinic_name', 'GITRIA FARMA')) ?></div>
        <div class="preloader-text" style="font-size: 11.5px; color: #64748b; font-weight: 500;">Memuat data sistem medis...</div>
    </div>
</div>
<div class="wrapper">
<?php 
$roleName = session('role_name');
$perms = session('permissions') ?? [];
$isSuper = ($roleName === 'Super Admin' || $roleName === 'IT');

// Helper function to check permissions
$hasPerm = function($perm) use ($isSuper, $perms) {
    return $isSuper || in_array($perm, $perms);
};

// Permission flags per functional category
$canAccessClinic = $isSuper || $hasPerm('clinic.register') || $hasPerm('clinic.soap') || $hasPerm('clinic.billing') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Dokter', 'Perawat', 'Rekam Medis']);
$canAccessPharmacy = $isSuper || $hasPerm('pharmacy.dispense') || $hasPerm('pharmacy.stock') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Apoteker', 'Gudang']);
$canAccessResto = $isSuper || $hasPerm('resto.order') || $hasPerm('resto.kitchen') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Resto/Kasir', 'Resto/Dapur']);
$canAccessDistributor = $isSuper || $hasPerm('distributor.view') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Distributor', 'Manager']);
$canAccessFinance = $isSuper || $hasPerm('clinic.billing') || $hasPerm('finance.manage') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Kasir', 'Koordinator Keuangan', 'Accounting']);
$canAccessAccounting = $isSuper || $hasPerm('accounting.ledger') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Accounting', 'Koordinator Keuangan', 'Manager']);
$canAccessProcurement = $isSuper || $hasPerm('procurement.apply') || $hasPerm('procurement.verify') || $hasPerm('procurement.approve') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'Gudang', 'Apoteker', 'Koordinator Keuangan', 'Manager']);
$canAccessHRD = $isSuper || $hasPerm('inventory.manage') || $hasPerm('hrd.payroll') || in_array($roleName, ['Direksi', 'Kepala Klinik', 'HRD', 'Manager']);
$canAccessMaster = $isSuper || $hasPerm('system.settings') || in_array($roleName, ['Direksi', 'Kepala Klinik']);
$canAccessAdmin = $isSuper || in_array($roleName, ['Direksi']);
?>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-light modern-macos-header">
        <!-- Left navbar links -->
        <ul class="navbar-nav align-items-center flex-nowrap" style="gap: 10px;">
            <li class="nav-item">
                <a class="nav-icon-btn" data-widget="pushmenu" href="#" role="button" title="Buka / Tutup Navigasi Sidebar"><i class="fas fa-bars-staggered"></i></a>
            </li>

            <?php 
            $currUri = uri_string();
            $isDashboard = in_array($currUri, ['', 'dashboard']) || ($active_menu ?? '') === 'dashboard';
            $isApotekActive = str_starts_with($currUri, 'apotek') || str_starts_with($active_menu ?? '', 'apotek');
            $isRestoActive  = str_starts_with($currUri, 'resto') || str_starts_with($active_menu ?? '', 'resto');
            $isDistributorActive = str_starts_with($currUri, 'distributor') || str_starts_with($active_menu ?? '', 'distributor');
            $isKeuanganActive = str_starts_with($currUri, 'keuangan') || in_array($active_menu ?? '', ['kasir', 'keuangan-rekap', 'keuangan-transaksi', 'keuangan-aging', 'keuangan-fee-dokter']);
            $isAccountingActive = str_starts_with($currUri, 'accounting') || str_starts_with($active_menu ?? '', 'accounting');
            $isProcurementActive = str_starts_with($currUri, 'procurement') || str_starts_with($active_menu ?? '', 'procurement');
            $isHrdActive = str_starts_with($currUri, 'hrd') || str_starts_with($currUri, 'inventaris') || in_array($active_menu ?? '', ['inventaris-aset', 'hrd-payroll', 'hrd-absensi', 'hrd-kpi', 'hrd-pegawai']);
            $isMasterActive = in_array($currUri, ['system/master-klinik', 'system/master-icd', 'system/master-pembayaran']) || in_array($active_menu ?? '', ['system-master-klinik', 'system-master-icd', 'master-pembayaran']);
            $isSystemActive = str_starts_with($currUri, 'system') && !$isMasterActive;
            $isKlinikActive = (str_starts_with($currUri, 'klinik') || in_array($active_menu ?? '', ['pendaftaran', 'antrean', 'soap', 'rekam-medis', 'rme', 'surat', 'lab', 'laporan_klinik', 'klinik-hub'])) && !$isDashboard;
            ?>

            <!-- Modern Spotlight Global Search Trigger (Desktop & Mobile) -->
            <li class="nav-item d-none d-md-block">
                <button type="button" class="btn-modern-spotlight d-flex align-items-center justify-content-between" id="btn-trigger-spotlight" title="Pencarian Cepat Pasien &amp; Modul (Tekan Ctrl + K)">
                    <div class="d-flex align-items-center text-truncate mr-2">
                        <div class="spotlight-search-sparkle mr-2">
                            <i class="fas fa-magnifying-glass"></i>
                        </div>
                        <span class="spotlight-placeholder text-truncate">Cari pasien (No. RM, NIK, Nama) atau modul...</span>
                    </div>
                    <kbd class="spotlight-badge-kbd">Ctrl K</kbd>
                </button>
            </li>
            <li class="nav-item d-md-none">
                <button type="button" class="nav-icon-btn" id="btn-trigger-spotlight-mobile" title="Cari Pasien (Ctrl+K)">
                    <i class="fas fa-magnifying-glass text-primary"></i>
                </button>
            </li>

            <!-- Live Status Antrean Poli (Pill Widget with Pulse Indicator) -->
            <?php 
            $activeQueueCount = clinic_active_queue_count(); 
            if ($canAccessClinic):
            ?>
            <li class="nav-item d-none d-sm-block">
                <a href="<?= base_url('klinik/pendaftaran') ?>" class="navbar-queue-pill <?= $activeQueueCount > 0 ? 'has-queue' : '' ?>" title="Lihat Antrean Pasien Hari Ini">
                    <span class="pulse-dot <?= $activeQueueCount > 0 ? 'pulse-active' : '' ?>"></span>
                    <i class="fas fa-hospital-user mr-1.5" style="font-size: 11.5px;"></i>
                    <span>Antrean: <strong><?= $activeQueueCount ?></strong> Pasien</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Right navbar links (Icon-Only, Spacious & Clean) -->
        <ul class="navbar-nav ml-auto align-items-center flex-nowrap" style="gap: 10px;">
            <!-- Indikator Jaringan & Pusat Draf Offline -->
            <li class="nav-item">
                <a href="javascript:void(0)" id="navbar-network-status" class="nav-icon-btn position-relative" onclick="if(window.offlineSyncEngine) window.offlineSyncEngine.openQueueModal(); else $('#modal-offline-queue').modal('show');" style="cursor: pointer;" title="Status Jaringan &amp; Antrean Draf Offline">
                    <i class="fas fa-wifi text-success" id="icon-network-status"></i>
                    <span class="online-indicator-dot"></span>
                    <span id="offline-draft-count" class="badge badge-warning text-dark nav-badge-counter d-none font-weight-bold">0</span>
                </a>
            </li>

            <!-- Badge Versi Sistem & Trigger Apa Yang Baru -->
            <li class="nav-item d-none d-sm-block">
                <a href="javascript:void(0)" onclick="$('#modal-whats-new-popup').modal('show')" class="nav-icon-btn d-flex align-items-center px-2.5 text-decoration-none" style="width: auto; border-radius: 20px; gap: 5px; background: rgba(13, 148, 136, 0.08); border: 1px solid rgba(13, 148, 136, 0.25);" title="Versi Sistem: <?= clinic_latest_version() ?> (Klik untuk melihat Apa Yang Baru)">
                    <i class="fas fa-sparkles text-warning" style="font-size: 11px;"></i>
                    <span class="text-teal font-weight-bold" style="font-size: 11px;"><?= clinic_latest_version() ?></span>
                </a>
            </li>

            <!-- Tombol Paksa Perbarui Sistem & Bersihkan Cache Komputer -->
            <li class="nav-item">
                <button type="button" class="nav-icon-btn btn-force-update" onclick="window.forceUpdateSystem()" title="Paksa Perbarui Sistem &amp; Bersihkan Cache Komputer">
                    <i class="fas fa-arrows-rotate text-teal"></i>
                </button>
            </li>

            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-icon-btn position-relative" data-dropdown-active="false" data-toggle="dropdown" href="#" role="button" title="Notifikasi Sistem">
                    <i class="far fa-bell text-secondary" id="icon-nav-bell"></i>
                    <span class="badge badge-danger nav-badge-counter font-weight-bold" id="notif-count" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0 py-2" id="notif-dropdown" style="border-radius: 14px; min-width: 320px; margin-top: 8px;">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between bg-light" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <span class="font-weight-bold text-dark text-xs"><i class="fas fa-bell mr-1 text-teal"></i> <span id="notif-header-text">0 Notifikasi Baru</span></span>
                        <a href="<?= base_url('system/notifications/mark-all-read') ?>" class="text-xs text-muted" title="Tandai Semua Dibaca"><i class="fas fa-check-double"></i></a>
                    </div>
                    <div id="notif-dropdown-items" style="max-height: 300px; overflow-y: auto;">
                        <a href="javascript:void(0)" class="dropdown-item text-center text-muted py-3">
                            <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada notifikasi tertunda
                        </a>
                    </div>
                    <div class="p-2 bg-light border-top text-center" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                        <a href="<?= base_url('system/notifications') ?>" class="text-xs font-weight-bold text-teal"><i class="fas fa-list-check mr-1"></i> Riwayat Notifikasi &amp; Rules</a>
                    </div>
                </div>
            </li>

            <!-- Tombol Pusat Menu Lengkap & Kontrol (Launchpad Hub) -->
            <li class="nav-item">
                <button type="button" class="nav-icon-btn btn-control-hub" data-toggle="modal" data-target="#modal-control-center" title="Pusat Kontrol &amp; Menu Lengkap (Aksi Cepat, Display TV, Tema &amp; Pemeliharaan)">
                    <i class="fas fa-table-cells-large text-primary"></i>
                </button>
            </li>
            
            <!-- User Profile & Account Dropdown -->
            <li class="nav-item dropdown ml-1">
                <a class="nav-profile-btn dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" title="Akun Pengguna">
                    <div class="user-avatar-wrap">
                        <?php if (user_avatar()): ?>
                            <img src="<?= user_avatar() ?>" alt="Avatar" class="user-avatar-img rounded-circle">
                        <?php else: ?>
                            <div class="user-avatar-initials">
                                <?= strtoupper(substr(session('fullname') ?: session('username') ?: 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-none d-xl-flex flex-column text-left ml-2" style="line-height: 1.15;">
                        <span class="user-pill-fullname text-truncate" style="max-width: 120px; font-weight: 600; font-size: 12px; color: #1e293b;"><?= esc(session('fullname') ?: session('username')) ?></span>
                        <span class="user-pill-role text-truncate" style="max-width: 120px; font-size: 10px; color: #64748b;"><?= esc(session('role_name') ?? 'Petugas') ?></span>
                    </div>
                    <i class="fas fa-chevron-down user-pill-caret ml-1.5 d-none d-sm-inline-block" style="font-size: 10px; color: #64748b;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 py-2" style="border-radius: 14px; min-width: 240px; margin-top: 8px;">
                    <div class="px-3 py-2 border-bottom mb-1 bg-light" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <strong class="d-block text-dark text-sm"><?= esc(session('fullname') ?: session('username')) ?></strong>
                        <span class="badge badge-primary font-weight-bold text-xs mt-1"><?= esc(session('role_name') ?? 'Petugas') ?></span>
                    </div>
                    <a href="<?= base_url('profile') ?>" class="dropdown-item py-2 d-flex align-items-center">
                        <i class="fas fa-user-gear mr-2.5 text-primary"></i> Profil &amp; Pengaturan Akun
                    </a>
                    <a href="javascript:void(0)" onclick="$('#modal-whats-new-popup').modal('show')" class="dropdown-item py-2 d-flex align-items-center text-dark">
                        <i class="fas fa-sparkles mr-2.5 text-warning"></i> Apa yang Baru? <span class="badge badge-teal ml-auto font-weight-bold" style="font-size: 10px;"><?= clinic_latest_version() ?></span>
                    </a>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-control-center" class="dropdown-item py-2 d-flex align-items-center text-dark">
                        <i class="fas fa-table-cells-large mr-2.5 text-primary"></i> Pusat Kontrol &amp; Menu
                    </a>
                    <a href="javascript:void(0)" onclick="window.forceUpdateSystem()" class="dropdown-item py-2 d-flex align-items-center text-teal font-weight-bold">
                        <i class="fas fa-arrows-rotate mr-2.5 text-teal"></i> Perbarui Sistem &amp; Hapus Cache
                    </a>
                    <div class="dropdown-divider my-1"></div>
                    <a href="<?= base_url('logout') ?>" class="dropdown-item py-2 text-danger font-weight-bold d-flex align-items-center" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                        <i class="fas fa-arrow-right-from-bracket mr-2.5"></i> Keluar
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container (Putih di Atas Kertas) -->
    <aside class="main-sidebar sidebar-light-success border-right elevation-0">
        <!-- Brand Logo -->
        <a href="<?= base_url('dashboard') ?>" class="brand-link d-flex align-items-center">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" class="brand-image img-circle elevation-1 mr-2" style="max-height:33px; max-width:33px; object-fit:contain;">
            <?php else: ?>
                <span class="brand-image img-circle elevation-1 d-inline-flex align-items-center justify-content-center bg-teal text-white mr-2" style="width:33px; height:33px;">
                    <i class="fas fa-hospital-alt"></i>
                </span>
            <?php endif; ?>
            <span class="brand-text font-weight-bold text-dark" style="font-size: 14px; letter-spacing: -0.3px;">
                <?= esc(clinic_setting('clinic_name', 'SAWAMAWA')) ?>
            </span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $isDashboard ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-chart-line text-teal"></i>
                            <p class="font-weight-bold">Dashboard Utama</p>
                        </a>
                    </li>

                    <!-- Section 1: Operasional & Pelayanan -->
                    <li class="nav-header text-uppercase" style="font-size: 11px; letter-spacing: 0.6px; font-weight: 800; color: #334155; padding: 12px 14px 4px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: 6px;">OPERASIONAL &amp; PELAYANAN</li>

                    <?php if ($canAccessClinic): ?>
                    <li class="nav-item has-treeview <?= $isKlinikActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isKlinikActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-hospital-user text-primary"></i>
                            <p class="font-weight-bold">
                                Pelayanan Medis
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('klinik/pendaftaran') ?>" class="nav-link <?= in_array($active_menu ?? '', ['pendaftaran', 'antrean']) || $currUri === 'klinik/pendaftaran' ? 'active' : '' ?>">
                                    <i class="fas fa-user-plus nav-icon text-primary"></i>
                                    <p>Pendaftaran &amp; Antrean</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('klinik/soap') ?>" class="nav-link <?= in_array($active_menu ?? '', ['soap', 'rme', 'rekam-medis']) || $currUri === 'klinik/soap' ? 'active' : '' ?>">
                                    <i class="fas fa-notes-medical nav-icon text-teal"></i>
                                    <p>RME SOAP Pasien</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('klinik/lab') ?>" class="nav-link <?= ($active_menu ?? '') === 'lab' || $currUri === 'klinik/lab' ? 'active' : '' ?>">
                                    <i class="fas fa-flask nav-icon text-warning"></i>
                                    <p>Laboratorium</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('klinik/surat') ?>" class="nav-link <?= ($active_menu ?? '') === 'surat' || $currUri === 'klinik/surat' ? 'active' : '' ?>">
                                    <i class="fas fa-file-medical nav-icon text-secondary"></i>
                                    <p>Surat &amp; Rujukan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('klinik/laporan') ?>" class="nav-link <?= ($active_menu ?? '') === 'laporan_klinik' || $currUri === 'klinik/laporan' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-pie nav-icon text-success"></i>
                                    <p>Laporan Pelayanan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessPharmacy): ?>
                    <li class="nav-item has-treeview <?= $isApotekActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isApotekActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-prescription-bottle-medical text-teal"></i>
                            <p class="font-weight-bold">
                                Farmasi &amp; Apotek
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/resep') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-resep', 'resep']) || $currUri === 'apotek/resep' ? 'active' : '' ?>">
                                    <i class="fas fa-file-prescription nav-icon text-primary"></i>
                                    <p>Pelayanan E-Resep</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/penjualan') ?>" class="nav-link <?= in_array($active_menu ?? '', ['penjualan_langsung', 'apotek-penjualan']) || $currUri === 'apotek/penjualan' ? 'active' : '' ?>">
                                    <i class="fas fa-cart-shopping nav-icon text-warning"></i>
                                    <p>Kasir Apotek (OTC)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/stok') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-stok', 'stok']) || $currUri === 'apotek/stok' ? 'active' : '' ?>">
                                    <i class="fas fa-pills nav-icon text-teal"></i>
                                    <p>Stok Obat &amp; FEFO</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/gudang') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-gudang', 'gudang']) || $currUri === 'apotek/gudang' ? 'active' : '' ?>">
                                    <i class="fas fa-warehouse nav-icon text-secondary"></i>
                                    <p>Gudang &amp; Mutasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/kartu-stok') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-kartu-stok', 'kartu-stok']) || $currUri === 'apotek/kartu-stok' ? 'active' : '' ?>">
                                    <i class="fas fa-book-medical nav-icon text-info"></i>
                                    <p>Buku Kartu Stok</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/opname') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-opname', 'opname']) || $currUri === 'apotek/opname' ? 'active' : '' ?>">
                                    <i class="fas fa-clipboard-check nav-icon text-warning"></i>
                                    <p>Stok Opname</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('apotek/laporan') ?>" class="nav-link <?= in_array($active_menu ?? '', ['apotek-laporan', 'laporan']) || $currUri === 'apotek/laporan' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-line nav-icon text-success"></i>
                                    <p>Laporan Farmasi</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessResto): ?>
                    <li class="nav-item has-treeview <?= $isRestoActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isRestoActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-utensils text-warning"></i>
                            <p class="font-weight-bold">
                                Resto &amp; Dapur Gizi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('resto/pos') ?>" class="nav-link <?= ($active_menu ?? '') === 'resto-pos' || $currUri === 'resto/pos' ? 'active' : '' ?>">
                                    <i class="fas fa-cash-register nav-icon text-warning"></i>
                                    <p>Kasir Resto (POS)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('resto/dapur') ?>" class="nav-link <?= ($active_menu ?? '') === 'resto-dapur' || $currUri === 'resto/dapur' ? 'active' : '' ?>">
                                    <i class="fas fa-fire-burner nav-icon text-danger"></i>
                                    <p>Dapur Gizi (KDS)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('resto/laporan') ?>" class="nav-link <?= ($active_menu ?? '') === 'resto-laporan' || $currUri === 'resto/laporan' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-pie nav-icon text-success"></i>
                                    <p>Laporan Resto</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessDistributor): ?>
                    <li class="nav-item has-treeview <?= $isDistributorActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isDistributorActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-truck-fast text-indigo"></i>
                            <p class="font-weight-bold">
                                Distributor &amp; Grosir
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/dashboard') ?>" class="nav-link <?= $currUri === 'distributor/dashboard' || $currUri === 'distributor' ? 'active' : '' ?>">
                                    <i class="fas fa-gauge-high nav-icon text-indigo"></i>
                                    <p>Dashboard Grosir</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/penjualan') ?>" class="nav-link <?= $currUri === 'distributor/penjualan' ? 'active' : '' ?>">
                                    <i class="fas fa-cash-register nav-icon text-success"></i>
                                    <p>Kasir &amp; Faktur B2B</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/riwayat') ?>" class="nav-link <?= $currUri === 'distributor/riwayat' ? 'active' : '' ?>">
                                    <i class="fas fa-receipt nav-icon text-primary"></i>
                                    <p>Riwayat Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/pelanggan') ?>" class="nav-link <?= $currUri === 'distributor/pelanggan' ? 'active' : '' ?>">
                                    <i class="fas fa-users-rectangle nav-icon text-info"></i>
                                    <p>Pelanggan &amp; Limit</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/stok') ?>" class="nav-link <?= $currUri === 'distributor/stok' ? 'active' : '' ?>">
                                    <i class="fas fa-boxes-stacked nav-icon text-teal"></i>
                                    <p>Stok &amp; Kartu Stok</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/transfer') ?>" class="nav-link <?= $currUri === 'distributor/transfer' ? 'active' : '' ?>">
                                    <i class="fas fa-arrow-right-arrow-left nav-icon text-secondary"></i>
                                    <p>Transfer Antar Unit</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/piutang') ?>" class="nav-link <?= $currUri === 'distributor/piutang' ? 'active' : '' ?>">
                                    <i class="fas fa-file-invoice-dollar nav-icon text-warning"></i>
                                    <p>Monitoring Piutang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/retur') ?>" class="nav-link <?= $currUri === 'distributor/retur' ? 'active' : '' ?>">
                                    <i class="fas fa-rotate-left nav-icon text-danger"></i>
                                    <p>Retur Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('distributor/laporan') ?>" class="nav-link <?= $currUri === 'distributor/laporan' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-pie nav-icon text-success"></i>
                                    <p>Laporan Distributor</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Section 2: Keuangan & Akuntansi -->
                    <li class="nav-header text-uppercase" style="font-size: 11px; letter-spacing: 0.6px; font-weight: 800; color: #334155; padding: 12px 14px 4px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: 6px;">KEUANGAN &amp; AKUNTANSI</li>


                    <?php if ($canAccessFinance): ?>
                    <li class="nav-item has-treeview <?= $isKeuanganActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isKeuanganActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-cash-register text-success"></i>
                            <p class="font-weight-bold">
                                Keuangan &amp; Kasir
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('keuangan/kasir') ?>" class="nav-link <?= in_array($active_menu ?? '', ['kasir']) || $currUri === 'keuangan/kasir' ? 'active' : '' ?>">
                                    <i class="fas fa-receipt nav-icon text-success"></i>
                                    <p>Kasir Pembayaran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('keuangan/rekap-harian') ?>" class="nav-link <?= in_array($active_menu ?? '', ['keuangan-rekap']) || $currUri === 'keuangan/rekap-harian' ? 'active' : '' ?>">
                                    <i class="fas fa-calendar-check nav-icon text-info"></i>
                                    <p>Rekap Kasir &amp; Shift</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('keuangan/transaksi') ?>" class="nav-link <?= in_array($active_menu ?? '', ['keuangan-transaksi']) || $currUri === 'keuangan/transaksi' ? 'active' : '' ?>">
                                    <i class="fas fa-wallet nav-icon text-teal"></i>
                                    <p>Kas &amp; Rekening Bank</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('keuangan/fee-dokter') ?>" class="nav-link <?= in_array($active_menu ?? '', ['keuangan-fee-dokter']) || $currUri === 'keuangan/fee-dokter' ? 'active' : '' ?>">
                                    <i class="fas fa-user-doctor nav-icon text-primary"></i>
                                    <p>Rekap Fee Dokter</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('keuangan/aging-schedule') ?>" class="nav-link <?= in_array($active_menu ?? '', ['keuangan-aging']) || $currUri === 'keuangan/aging-schedule' ? 'active' : '' ?>">
                                    <i class="fas fa-hourglass-half nav-icon text-warning"></i>
                                    <p>Aging Piutang Pasien</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessAccounting): ?>
                    <li class="nav-item has-treeview <?= $isAccountingActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isAccountingActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar text-teal"></i>
                            <p class="font-weight-bold">
                                Akuntansi &amp; Jurnal
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/jurnal') ?>" class="nav-link <?= in_array($active_menu ?? '', ['accounting-jurnal']) || $currUri === 'accounting/jurnal' ? 'active' : '' ?>">
                                    <i class="fas fa-file-lines nav-icon text-teal"></i>
                                    <p>1. Jurnal Umum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/buku-besar') ?>" class="nav-link <?= in_array($active_menu ?? '', ['accounting-buku-besar']) || $currUri === 'accounting/buku-besar' ? 'active' : '' ?>">
                                    <i class="fas fa-book-journal-whills nav-icon text-info"></i>
                                    <p>2. Buku Mutasi Akun</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/laporan') ?>" class="nav-link <?= in_array($active_menu ?? '', ['accounting-laporan']) || $currUri === 'accounting/laporan' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-pie nav-icon text-success"></i>
                                    <p>3. Laporan Keuangan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/coa') ?>" class="nav-link <?= in_array($active_menu ?? '', ['accounting-coa']) || $currUri === 'accounting/coa' ? 'active' : '' ?>">
                                    <i class="fas fa-book-bookmark nav-icon text-secondary"></i>
                                    <p>4. Bagan Akun (COA)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/aturan-jurnal') ?>" class="nav-link <?= in_array($active_menu ?? '', ['aturan-jurnal', 'aturan_jurnal']) || str_starts_with($currUri, 'accounting/aturan-jurnal') ? 'active' : '' ?>">
                                    <i class="fas fa-sliders nav-icon text-warning"></i>
                                    <p>5. Aturan Jurnal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('accounting/saldo-awal') ?>" class="nav-link <?= in_array($active_menu ?? '', ['accounting-saldo-awal']) || $currUri === 'accounting/saldo-awal' ? 'active' : '' ?>">
                                    <i class="fas fa-scale-balanced nav-icon text-primary"></i>
                                    <p>6. Saldo Awal</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Section 3: Manajemen & Logistik -->
                    <?php if ($canAccessProcurement || $canAccessHRD): ?>
                    <li class="nav-header text-uppercase" style="font-size: 11px; letter-spacing: 0.6px; font-weight: 800; color: #334155; padding: 12px 14px 4px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: 6px;">LOGISTIK &amp; KEPEGAWAIAN</li>

                    <?php if ($canAccessProcurement): ?>
                    <li class="nav-item has-treeview <?= $isProcurementActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isProcurementActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-truck-ramp-box text-teal"></i>
                            <p class="font-weight-bold">
                                Pengadaan &amp; Logistik
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('procurement/po') ?>" class="nav-link <?= ($active_menu ?? '') === 'procurement-po' || $currUri === 'procurement/po' ? 'active' : '' ?>">
                                    <i class="fas fa-file-invoice nav-icon text-teal"></i>
                                    <p>Surat Pesanan (PO)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('procurement/approval') ?>" class="nav-link <?= ($active_menu ?? '') === 'procurement-approval' || $currUri === 'procurement/approval' ? 'active' : '' ?>">
                                    <i class="fas fa-clipboard-check nav-icon text-success"></i>
                                    <p>Persetujuan Request</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($canAccessHRD): ?>
                    <li class="nav-item has-treeview <?= $isHrdActive ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= $isHrdActive ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-id-card-clip text-primary"></i>
                            <p class="font-weight-bold">
                                Aset &amp; HRD Pegawai
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= base_url('hrd/pegawai') ?>" class="nav-link <?= in_array($active_menu ?? '', ['hrd-pegawai', 'pegawai']) || $currUri === 'hrd/pegawai' ? 'active' : '' ?>">
                                    <i class="fas fa-user-group nav-icon text-primary"></i>
                                    <p>Data Pegawai</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('hrd/absensi') ?>" class="nav-link <?= in_array($active_menu ?? '', ['hrd-absensi', 'absensi']) || $currUri === 'hrd/absensi' ? 'active' : '' ?>">
                                    <i class="fas fa-calendar-check nav-icon text-success"></i>
                                    <p>Presensi / Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('hrd/payroll') ?>" class="nav-link <?= in_array($active_menu ?? '', ['hrd-payroll']) || $currUri === 'hrd/payroll' ? 'active' : '' ?>">
                                    <i class="fas fa-money-bill-wave nav-icon text-teal"></i>
                                    <p>Penggajian (Payroll)</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('hrd/kpi') ?>" class="nav-link <?= in_array($active_menu ?? '', ['hrd-kpi', 'kpi']) || $currUri === 'hrd/kpi' ? 'active' : '' ?>">
                                    <i class="fas fa-chart-line nav-icon text-warning"></i>
                                    <p>Matriks Penilaian KPI</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('inventaris/aset') ?>" class="nav-link <?= in_array($active_menu ?? '', ['inventaris-aset', 'aset']) || $currUri === 'inventaris/aset' ? 'active' : '' ?>">
                                    <i class="fas fa-boxes-packing nav-icon text-info"></i>
                                    <p>Inventaris Aset Klinik</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Section 4: Master Data, Sistem & Bantuan -->
                    <li class="nav-header text-uppercase" style="font-size: 11px; letter-spacing: 0.6px; font-weight: 800; color: #334155; padding: 12px 14px 4px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: 6px;">SISTEM &amp; PENGATURAN</li>

                    <?php if ($canAccessMaster || $canAccessAdmin): ?>
                    <li class="nav-item has-treeview <?= ($isMasterActive || $isSystemActive) ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= ($isMasterActive || $isSystemActive) ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-gear text-secondary"></i>
                            <p class="font-weight-bold">
                                Sistem &amp; Pengaturan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if ($canAccessMaster): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('system/master-klinik') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-master-klinik' || $currUri === 'system/master-klinik' ? 'active' : '' ?>">
                                    <i class="fas fa-hospital-user nav-icon text-teal"></i>
                                    <p>Master Referensi Klinik</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/master-icd') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-master-icd' || $currUri === 'system/master-icd' ? 'active' : '' ?>">
                                    <i class="fas fa-book-medical nav-icon text-primary"></i>
                                    <p>Katalog ICD-10</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/master-pembayaran') ?>" class="nav-link <?= ($active_menu ?? '') === 'master-pembayaran' || $currUri === 'system/master-pembayaran' ? 'active' : '' ?>">
                                    <i class="fas fa-credit-card nav-icon text-success"></i>
                                    <p>Metode Pembayaran</p>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($canAccessAdmin): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('system/settings') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-settings' || $currUri === 'system/settings' ? 'active' : '' ?>">
                                    <i class="fas fa-sliders nav-icon text-dark"></i>
                                    <p>Pengaturan Dasar Klinik</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/users') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-users' || $currUri === 'system/users' ? 'active' : '' ?>">
                                    <i class="fas fa-users-gear nav-icon text-primary"></i>
                                    <p>Pengguna &amp; RBAC</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/notifications') ?>" class="nav-link <?= ($active_menu ?? '') === 'system' && $currUri === 'system/notifications' ? 'active' : '' ?>">
                                    <i class="fas fa-bell nav-icon text-warning"></i>
                                    <p>Notifikasi &amp; Rules</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/whatsapp') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-whatsapp' || $currUri === 'system/whatsapp' ? 'active' : '' ?>">
                                    <i class="fab fa-whatsapp nav-icon text-success"></i>
                                    <p>WhatsApp Gateway</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/database') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-database' || $currUri === 'system/database' ? 'active' : '' ?>">
                                    <i class="fas fa-database nav-icon text-info"></i>
                                    <p>Database &amp; Backup</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/audit') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-audit' || $currUri === 'system/audit' ? 'active' : '' ?>">
                                    <i class="fas fa-shield-halved nav-icon text-secondary"></i>
                                    <p>Audit Trail Log</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url('system/whats-new') ?>" class="nav-link <?= ($active_menu ?? '') === 'system-whats-new' || $currUri === 'system/whats-new' ? 'active' : '' ?>">
                                    <i class="fas fa-sparkles nav-icon text-warning"></i>
                                    <p>Apa yang Baru? <span class="badge badge-warning text-dark right font-weight-bold"><?= clinic_latest_version() ?></span></p>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="<?= base_url('profile') ?>" class="nav-link <?= isset($active_menu) && $active_menu === 'profile' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-gear text-teal"></i>
                            <p class="font-weight-bold">Profil &amp; Akun Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('bantuan') ?>" class="nav-link <?= isset($active_menu) && $active_menu === 'bantuan' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-circle-question text-info"></i>
                            <p class="font-weight-bold">Pusat Bantuan &amp; Alur</p>
                        </a>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper pt-3">
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Flash Messages Modern (Paper White & Auto-Dismiss) -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-flash-card alert-dismissible fade show mb-3" role="alert" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #0d9f4f; border-radius:4px; padding:12px 16px; box-shadow:none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center mr-3" style="background:#e6f4ea; color:#076e34; width:32px; height:32px; border-radius:50%; font-size:15px; flex-shrink:0;">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div>
                                    <strong class="text-dark d-block" style="font-size:13.5px; line-height:1.2;">Berhasil</strong>
                                    <span class="text-secondary" style="font-size:13px;"><?= session()->getFlashdata('success') ?></span>
                                </div>
                            </div>
                            <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close" style="font-size:20px; line-height:1; padding:0 8px; opacity:0.6;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-flash-card alert-dismissible fade show mb-3" role="alert" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #dc2626; border-radius:4px; padding:12px 16px; box-shadow:none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center mr-3" style="background:#fee2e2; color:#dc2626; width:32px; height:32px; border-radius:50%; font-size:15px; flex-shrink:0;">
                                    <i class="fas fa-triangle-exclamation"></i>
                                </span>
                                <div>
                                    <strong class="text-dark d-block" style="font-size:13.5px; line-height:1.2;">Pemberitahuan / Kendala</strong>
                                    <span class="text-secondary" style="font-size:13px;"><?= session()->getFlashdata('error') ?></span>
                                </div>
                            </div>
                            <button type="button" class="close text-secondary" data-dismiss="alert" aria-label="Close" style="font-size:20px; line-height:1; padding:0 8px; opacity:0.6;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Render Dynamic Content View -->
                <?= $this->renderSection('content') ?>
                
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer d-flex justify-content-between align-items-center">
        <div>
            <strong>&copy; 2026 <a href="#" class="text-dark font-weight-bold">PT. ARM ERA CORPORAT</a>.</strong> All rights reserved.
        </div>
        <div class="d-none d-sm-flex align-items-center" style="gap: 8px;">
            <span class="text-muted text-xs">Sistem Manajemen SIM-Klinik Terpadu</span>
            <a href="javascript:void(0)" onclick="$('#modal-whats-new-popup').modal('show')" class="badge badge-teal px-2 py-1 font-weight-bold" style="font-size: 11px;" title="Lihat Catatan Rilis & Fitur Baru">
                <i class="fas fa-sparkles text-warning mr-1"></i> Versi <?= clinic_latest_version() ?>
            </a>
        </div>
    </footer>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP APA YANG BARU & TRACKING VERSI SISTEM                         -->
    <!-- ========================================================================= -->
    <?php $latestUpdateInfo = clinic_latest_update_info(); ?>
    <div class="modal fade" id="modal-whats-new-popup" tabindex="-1" role="dialog" aria-labelledby="modalWhatsNewPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-2 rounded-circle text-teal mr-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                            <i class="fas fa-sparkles text-warning fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0 text-white" id="modalWhatsNewPopupLabel">
                                Apa yang Baru di Sawamawa Medical Center?
                            </h5>
                            <small class="text-white-50">Log Pembaruan Rilis Versi Terkini: <strong><?= esc($latestUpdateInfo ? $latestUpdateInfo->version : 'v2.9.0') ?></strong> (<?= esc($latestUpdateInfo ? date('d F Y', strtotime($latestUpdateInfo->release_date)) : date('d F Y')) ?>)</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    <?php if ($latestUpdateInfo): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <span class="badge badge-<?= esc($latestUpdateInfo->badge_color ?? 'teal') ?> font-weight-bold px-2.5 py-1 text-uppercase" style="font-size: 11px;">
                                    <?= esc($latestUpdateInfo->category ?? 'FITUR BARU') ?>
                                </span>
                                <?php if ($latestUpdateInfo->is_major): ?>
                                    <span class="badge badge-danger ml-1 font-weight-bold px-2 py-1" style="font-size: 11px;">MAJOR RELEASE</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted text-xs font-weight-bold">
                                <i class="far fa-calendar-check mr-1"></i> Rilis: <?= date('d M Y', strtotime($latestUpdateInfo->release_date)) ?>
                            </span>
                        </div>

                        <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">
                            <?= esc($latestUpdateInfo->title) ?>
                        </h5>

                        <?php if (!empty($latestUpdateInfo->summary)): ?>
                            <div class="alert alert-light border shadow-none text-dark p-3 mb-3" style="font-size: 13.5px; line-height: 1.6; border-left: 4px solid #0d9488 !important;">
                                <?= nl2br(esc($latestUpdateInfo->summary)) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($latestUpdateInfo->details)): ?>
                            <h6 class="font-weight-bold text-dark mb-2 text-xs text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="fas fa-list-check text-teal mr-1"></i> Rincian Peningkatan &amp; Fitur Baru:
                            </h6>
                            <div class="bg-light p-3 rounded border mb-0">
                                <?php 
                                $lines = explode("\n", $latestUpdateInfo->details);
                                foreach ($lines as $l):
                                    $l = trim($l);
                                    if (empty($l)) continue;
                                ?>
                                    <div class="d-flex align-items-start mb-2 text-dark" style="font-size: 13px; line-height: 1.5;">
                                        <i class="fas fa-check-circle text-teal mr-2 mt-1" style="font-size: 13px; flex-shrink: 0;"></i>
                                        <div>
                                            <?php if (str_starts_with($l, '•') || str_starts_with($l, '-')): ?>
                                                <?= esc(ltrim($l, '•- ')) ?>
                                            <?php else: ?>
                                                <?= esc($l) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-sparkles fa-3x text-warning mb-2"></i>
                            <h6 class="font-weight-bold text-dark">Versi Sistem Stabil Terpasang</h6>
                            <p class="text-xs mb-0">Sistem Sawamawa Medical Center berjalan dengan versi rilis terkini.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer py-2.5 px-4 bg-light d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('system/whats-new') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                        <i class="fas fa-clock-rotate-left mr-1"></i> Lihat Semua Riwayat Rilis
                    </a>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold px-4" data-dismiss="modal">
                        <i class="fas fa-check mr-1"></i> Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Pusat Draf & Antrean Offline (Offline Resilience Hub) -->
    <div class="modal fade" id="modal-offline-queue" tabindex="-1" role="dialog" aria-labelledby="modalOfflineQueueLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-teal text-white">
                    <h5 class="modal-title font-weight-bold" id="modalOfflineQueueLabel">
                        <i class="fas fa-cloud-arrow-up mr-2"></i> Pusat Draf &amp; Antrean Offline (<span id="offline-modal-count">0</span>)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-xs font-weight-bold text-muted text-uppercase d-block">Status Sinkronisasi Sistem</span>
                            <small class="text-secondary">Data yang tersimpan di bawah ini akan otomatis dikirim ke database saat listrik/internet pulih.</small>
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold" id="btn-purge-local-cache" title="Bersihkan cache tampilan dan data antrean lokal yang usang">
                                <i class="fas fa-broom mr-1"></i> Bersihkan Cache
                            </button>
                            <button type="button" class="btn btn-sm btn-teal font-weight-bold shadow-sm" onclick="window.forceUpdateSystem()" title="Paksa unduh pembaruan kode dan bersihkan cache seluruh perangkat">
                                <i class="fas fa-arrows-rotate mr-1"></i> Perbarui Sistem
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-teal font-weight-bold" id="btn-manual-sync-offline">
                                <i class="fas fa-sync mr-1"></i> Sinkronkan Sekarang
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" id="btn-clear-all-offline">
                                <i class="fas fa-trash mr-1"></i> Kosongkan
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th style="width: 140px;">Waktu Input</th>
                                    <th>Deskripsi Transaksi / Draf</th>
                                    <th style="width: 110px;" class="text-center">Status</th>
                                    <th style="width: 60px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="offline-queue-tbody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Membaca draf lokal...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <small class="text-muted mr-auto">
                        <i class="fas fa-shield-halved text-success mr-1"></i> Data aman tersimpan di IndexedDB browser lokal.
                    </small>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Pusat Kontrol & Menu Lengkap (Executive Control Center & Launchpad) -->
    <div class="modal fade" id="modal-control-center" tabindex="-1" role="dialog" aria-labelledby="modalControlCenterLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-gradient-teal text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalControlCenterLabel">
                            <i class="fas fa-table-cells-large mr-2"></i> Pusat Kontrol &amp; Menu Lengkap
                        </h5>
                        <small class="text-white-50">Akses cepat transaksi, layar antrean, alat audio, personalisasi tema, dan bantuan sistem.</small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" style="background: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                    
                    <!-- Quick Filter Box -->
                    <div class="mb-4">
                        <div class="input-group shadow-xs" style="border-radius: 10px; overflow: hidden; border: 1px solid #cbd5e1;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="control-center-filter-input" class="form-control border-0 pl-0" placeholder="Ketik untuk mencari menu atau alat bantu..." style="font-size: 13px; box-shadow: none;" autocomplete="off">
                        </div>
                    </div>

                    <!-- 1. Aksi Cepat & Transaksi Pelayanan -->
                    <div class="control-center-section mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-xs font-weight-bold text-uppercase text-muted"><i class="fas fa-bolt text-warning mr-1"></i> Aksi Cepat Pelayanan &amp; Transaksi</span>
                        </div>
                        <div class="row">
                            <?php if ($canAccessClinic): ?>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('klinik/pendaftaran') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(0,122,255,0.12); flex-shrink: 0;"><i class="fas fa-user-plus text-primary"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Pasien Baru</strong>
                                        <small class="text-muted text-xs">Pendaftaran &amp; antrean</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('klinik/soap') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(13,148,136,0.12); flex-shrink: 0;"><i class="fas fa-notes-medical text-teal"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">RME SOAP Pasien</strong>
                                        <small class="text-muted text-xs">Rekam medis &amp; diagnosa</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessFinance): ?>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('keuangan/kasir') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(52,199,89,0.14); flex-shrink: 0;"><i class="fas fa-cash-register text-success"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Kasir Pembayaran</strong>
                                        <small class="text-muted text-xs">Billing &amp; kwitansi</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessPharmacy): ?>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('apotek/resep') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(255,149,0,0.14); flex-shrink: 0;"><i class="fas fa-file-prescription text-warning"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">E-Resep Obat</strong>
                                        <small class="text-muted text-xs">Dispensing resep dokter</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('apotek/penjualan') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(175,82,222,0.12); flex-shrink: 0;"><i class="fas fa-cart-shopping" style="color: #af52de;"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Kasir Apotek OTC</strong>
                                        <small class="text-muted text-xs">Penjualan obat bebas</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessAccounting): ?>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('accounting/jurnal') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(0,122,255,0.1); flex-shrink: 0;"><i class="fas fa-book text-primary"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Jurnal Manual</strong>
                                        <small class="text-muted text-xs">Entri jurnal memorial</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessProcurement): ?>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('procurement/po') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(100,116,139,0.12); flex-shrink: 0;"><i class="fas fa-truck-ramp-box text-secondary"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Pesanan (PO)</strong>
                                        <small class="text-muted text-xs">Pengadaan obat &amp; BHP</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. Layar Publik & Anjungan Mandiri (Display Eksternal) -->
                    <?php if ($canAccessClinic || $canAccessResto): ?>
                    <div class="control-center-section mb-4">
                        <span class="text-xs font-weight-bold text-uppercase text-muted d-block mb-2"><i class="fas fa-desktop text-info mr-1"></i> Layar Antrean Publik &amp; Kiosk</span>
                        <div class="row">
                            <?php if ($canAccessClinic): ?>
                            <div class="col-md-6 mb-2 control-card-item">
                                <a href="<?= base_url('klinik/display') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(14,165,233,0.12); flex-shrink: 0;"><i class="fas fa-tv text-info"></i></span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-dark" style="font-size: 13px;">Layar Display TV Poli</strong>
                                            <i class="fas fa-arrow-up-right-from-square text-muted text-xs"></i>
                                        </div>
                                        <small class="text-muted text-xs">Tampilan nomor antrean TV ruang tunggu</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 mb-2 control-card-item">
                                <a href="<?= base_url('klinik/kiosk') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(13,148,136,0.12); flex-shrink: 0;"><i class="fas fa-desktop text-teal"></i></span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-dark" style="font-size: 13px;">Kiosk APM Mandiri</strong>
                                            <i class="fas fa-arrow-up-right-from-square text-muted text-xs"></i>
                                        </div>
                                        <small class="text-muted text-xs">Anjungan check-in mandiri pasien</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>
                            <?php if ($canAccessResto): ?>
                            <div class="col-md-6 mb-2 control-card-item">
                                <a href="<?= base_url('resto/display-antrean') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(245,158,11,0.12); flex-shrink: 0;"><i class="fas fa-utensils text-warning"></i></span>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-dark" style="font-size: 13px;">Display TV Resto &amp; Gizi</strong>
                                            <i class="fas fa-arrow-up-right-from-square text-muted text-xs"></i>
                                        </div>
                                        <small class="text-muted text-xs">Antrean pesanan dapur &amp; resto</small>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- 3. Audio & Voice Caller -->
                    <div class="control-center-section mb-4">
                        <span class="text-xs font-weight-bold text-uppercase text-muted d-block mb-2"><i class="fas fa-volume-high text-teal mr-1"></i> Pengaturan Audio &amp; Suara Panggilan</span>
                        <div class="row">
                            <div class="col-md-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" onclick="$('#modal-control-center').modal('hide'); if(window.VCM) window.VCM.openPanel();" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(234,179,8,0.14); flex-shrink: 0;"><i class="fas fa-bullhorn text-warning"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Panel Voice Caller Antrean</strong>
                                        <small class="text-muted text-xs">Panggil nomor antrean suara faskes</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" id="btn-toggle-sound" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(13,148,136,0.12); flex-shrink: 0;"><i class="fas fa-volume-high text-teal" id="icon-sound-toggle"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Saklar Suara Notifikasi</strong>
                                        <small class="text-muted text-xs">Klik untuk mengaktifkan / mode hening</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Pilihan Tema Tampilan Panel -->
                    <div class="control-center-section mb-4">
                        <span class="text-xs font-weight-bold text-uppercase text-muted d-block mb-2"><i class="fas fa-palette text-purple mr-1"></i> Personalisasi Tema Tampilan</span>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center justify-content-between p-2.5 bg-white rounded border text-decoration-none" data-theme="theme-macos">
                                    <div class="d-flex align-items-center">
                                        <i class="fab fa-apple mr-2 text-dark" style="font-size: 16px;"></i>
                                        <div>
                                            <strong class="text-dark text-xs d-block">macOS 11 Big Sur</strong>
                                            <small class="text-muted text-xxs">Frosted Glass (Default)</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-success theme-check d-none"></i>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center justify-content-between p-2.5 bg-white rounded border text-decoration-none" data-theme="theme-excel-paper">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-table-cells mr-2 text-success" style="font-size: 16px;"></i>
                                        <div>
                                            <strong class="text-dark text-xs d-block">Putih di Atas Kertas</strong>
                                            <small class="text-muted text-xxs">Classic Excel Grid</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-success theme-check d-none"></i>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center justify-content-between p-2.5 bg-white rounded border text-decoration-none" data-theme="theme-paper-white">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-lines mr-2 text-primary" style="font-size: 16px;"></i>
                                        <div>
                                            <strong class="text-dark text-xs d-block">Paper White</strong>
                                            <small class="text-muted text-xxs">Soft Precision Slate</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-success theme-check d-none"></i>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center justify-content-between p-2.5 bg-white rounded border text-decoration-none" data-theme="theme-modern-emerald">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-gem mr-2 text-teal" style="font-size: 16px;"></i>
                                        <div>
                                            <strong class="text-dark text-xs d-block">Modern Emerald</strong>
                                            <small class="text-muted text-xxs">Elegan Toska Modern</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-success theme-check d-none"></i>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center justify-content-between p-2.5 bg-white rounded border text-decoration-none" data-theme="theme-windows-xp">
                                    <div class="d-flex align-items-center">
                                        <i class="fab fa-windows mr-2 text-info" style="font-size: 16px;"></i>
                                        <div>
                                            <strong class="text-dark text-xs d-block">Windows XP Classic</strong>
                                            <small class="text-muted text-xxs">Retro Luna Blue 3D</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-check text-success theme-check d-none"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Bantuan, Pembaruan & Monitor Sistem -->
                    <div class="control-center-section">
                        <span class="text-xs font-weight-bold text-uppercase text-muted d-block mb-2"><i class="fas fa-circle-question text-secondary mr-1"></i> Bantuan &amp; Pemeliharaan Sistem</span>
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('bantuan') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(13,148,136,0.12); flex-shrink: 0;"><i class="fas fa-book-open-reader text-teal"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Panduan &amp; Alur</strong>
                                        <small class="text-muted text-xs">Dokumentasi operasional</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="<?= base_url('system/whats-new') ?>" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(234,179,8,0.14); flex-shrink: 0;"><i class="fas fa-sparkles text-warning"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Apa yang Baru?</strong>
                                        <small class="text-muted text-xs">Versi <?= esc(clinic_latest_version()) ?></small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-2 control-card-item">
                                <a href="javascript:void(0)" onclick="$('#modal-control-center').modal('hide'); window.forceUpdateSystem();" class="control-menu-card d-flex align-items-center p-3 bg-white rounded shadow-xs text-decoration-none h-100">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-3" style="width: 36px; height: 36px; background: rgba(13,148,136,0.12); flex-shrink: 0;"><i class="fas fa-arrows-rotate text-teal"></i></span>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 13px;">Segarkan Cache</strong>
                                        <small class="text-muted text-xs">Paksa ambil kode terbaru</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
                    <span class="text-xs text-muted">
                        <i class="fas fa-shield-halved text-teal mr-1"></i> <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?> &bull; v<?= esc(clinic_latest_version()) ?>
                    </span>
                    <button type="button" class="btn btn-sm btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ./wrapper -->

<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<!-- DataTables & Responsive Plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive/2.4.1/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-responsive-bs4/2.4.1/responsive.bootstrap4.min.js"></script>
<!-- Select2 Searchable Dropdown JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/id.min.js"></script>
<!-- Voice Caller & Audio Synthesizer -->
<script src="<?= base_url('assets/js/voice_caller.js?v=' . (file_exists(FCPATH . 'assets/js/voice_caller.js') ? filemtime(FCPATH . 'assets/js/voice_caller.js') : time())) ?>"></script>
<!-- Universal Zero-Reload Real-Time LiveSync Engine -->
<script>const BASE_URL = '<?= base_url() ?>';</script>
<script src="<?= base_url('assets/js/live-sync-engine.js?v=' . (file_exists(FCPATH . 'assets/js/live-sync-engine.js') ? filemtime(FCPATH . 'assets/js/live-sync-engine.js') : time())) ?>"></script>
<!-- Universal Offline Resilience & Auto-Sync Engine -->
<script src="<?= base_url('assets/js/offline-sync-engine.js?v=' . (file_exists(FCPATH . 'assets/js/offline-sync-engine.js') ? filemtime(FCPATH . 'assets/js/offline-sync-engine.js') : time())) ?>"></script>

<!-- Global Initializations & Notifications -->
<script>
    $(document).ready(function() {
        // =========================================================================
        // FIX MENYELURUH: MODAL + DROPDOWN / SELECT2 (ANTI CANCEL & ANTI AUTO-CLOSE)
        // =========================================================================
        if ($.fn.modal && $.fn.modal.Constructor) {
            // 1. Matikan enforceFocus Bootstrap agar Select2 / input search tidak mencuri fokus & menutup modal
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            
            // 2. Set default static backdrop (tidak menutup saat klik di luar)
            if ($.fn.modal.Constructor.Default) {
                $.fn.modal.Constructor.Default.backdrop = 'static';
                $.fn.modal.Constructor.Default.keyboard = true;
            }
        }

        // Terapkan data-backdrop="static" pada seluruh modal
        $('.modal').attr('data-backdrop', 'static');

        // Cegah event bubbling dari dropdown / select / Select2 ke backdrop modal
        $(document).on('click mousedown touchstart', '.select2, .select2-container, .select2-dropdown, .select2-results, .dropdown-menu, select, .custom-select, option', function(e) {
            e.stopPropagation();
        });

        // Simpan status buka/tutup sidebar jika pengguna menekan tombol hamburger
        $(document).on('collapsed.lte.pushmenu', function() {
            localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
        });
        $(document).on('shown.lte.pushmenu', function() {
            localStorage.setItem('sawamawa_sidebar_state', 'expanded');
        });

        // =========================================================================
        // macOS UNIVERSAL SEARCHABLE DROPDOWN COMPONENT (AKTIF DI SEMUA HALAMAN)
        // =========================================================================
        window.initSearchableSelects = function(context) {
            const scope = context ? $(context) : $(document);
            
            // Target seluruh select form-control, custom-select, select2, dan select-searchable (Kecuali DataTables pagination)
            scope.find('select.select-searchable, select.select2, select[data-search="true"], select.form-control, select.custom-select').not('select[name$="_length"], .dataTables_length select, select[data-native="true"], select[multiple]').each(function() {
                const $select = $(this);
                if ($select.data('macos-select-initialized')) return;
                $select.data('macos-select-initialized', true);

                // Sembunyikan native select dari pandangan (tetap ada di DOM untuk form submit)
                $select.hide();

                const placeholder = $select.data('search-placeholder') || $select.attr('placeholder') || '🔍 Ketik untuk mencari...';
                const initialSelectedOption = $select.find('option:selected');
                let currentLabel = initialSelectedOption.text() || $select.find('option:first').text() || '-- Pilih Opsi --';
                const totalOptionsCount = $select.find('option').length;
                const showSearchBox = totalOptionsCount > 3 || $select.hasClass('select2') || $select.hasClass('select-searchable');
                
                // Bangun wrapper, trigger button & dropdown menu
                const $wrapper = $('<div class="macos-select-wrapper"></div>');
                const $trigger = $(`
                    <div class="macos-select-trigger ${$select.prop('disabled') ? 'disabled' : ''}" tabindex="0">
                        <span class="macos-select-label">${currentLabel}</span>
                        <i class="fas fa-chevron-down macos-select-arrow"></i>
                    </div>
                `);

                const $menu = $(`
                    <div class="macos-select-menu">
                        ${showSearchBox ? `
                        <div class="macos-select-search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="macos-select-search-field" placeholder="${placeholder}">
                        </div>` : ''}
                        <div class="macos-select-options-list"></div>
                    </div>
                `);

                const $optionsList = $menu.find('.macos-select-options-list');
                const $searchInput = $menu.find('.macos-select-search-field');

                // Render opsi dari native <select>
                function renderOptions(filterQuery) {
                    $optionsList.empty();
                    const query = (filterQuery || '').toLowerCase().trim();
                    const currentValue = $select.val();
                    let matchCount = 0;

                    $select.children().each(function() {
                        if ($(this).is('optgroup')) {
                            const groupLabel = $(this).attr('label');
                            let groupMatches = [];

                            $(this).find('option').each(function() {
                                const val = $(this).val();
                                const text = $(this).text();
                                if (!query || text.toLowerCase().includes(query) || val === '') {
                                    groupMatches.push({ val: val, text: text, isSelected: (val == currentValue && val !== '') });
                                }
                            });

                            if (groupMatches.length > 0) {
                                $optionsList.append(`<div class="macos-select-optgroup-header">${groupLabel}</div>`);
                                groupMatches.forEach(function(item) {
                                    const $opt = $(`
                                        <div class="macos-select-option ${item.isSelected ? 'is-selected' : ''}" data-value="${item.val}">
                                            <span>${item.text}</span>
                                            ${item.isSelected ? '<i class="fas fa-check text-primary" style="font-size: 11px;"></i>' : ''}
                                        </div>
                                    `);
                                    $optionsList.append($opt);
                                    if (item.val !== '') matchCount++;
                                });
                            }
                        } else if ($(this).is('option')) {
                            const val = $(this).val();
                            const text = $(this).text();
                            if (!query || text.toLowerCase().includes(query) || val === '') {
                                const isSelected = (val == currentValue && val !== '');
                                const $opt = $(`
                                    <div class="macos-select-option ${isSelected ? 'is-selected' : ''}" data-value="${val}">
                                        <span>${text}</span>
                                        ${isSelected ? '<i class="fas fa-check text-primary" style="font-size: 11px;"></i>' : ''}
                                    </div>
                                `);
                                $optionsList.append($opt);
                                if (val !== '') matchCount++;
                            }
                        }
                    });

                    if (matchCount === 0 && query) {
                        $optionsList.append(`<div class="macos-select-no-results">⚠️ Tidak ada hasil untuk "${query}"</div>`);
                    }
                }

                renderOptions('');

                // Toggle menu saat trigger diklik
                $trigger.on('click', function(e) {
                    e.stopPropagation();
                    if ($select.prop('disabled')) return;

                    const isOpen = $menu.hasClass('is-open');
                    
                    // Tutup dropdown lain yang terbuka & reset elevasi parent
                    $('.macos-select-menu').removeClass('is-open drop-up');
                    $('.macos-select-trigger').removeClass('is-open');
                    $('.macos-select-wrapper').removeClass('is-open');
                    $('.macos-select-parent-elevated').removeClass('macos-select-parent-elevated');

                    if (!isOpen) {
                        // Smart Drop-up & Horizontal Alignment Detection
                        const triggerEl = $trigger[0];
                        if (triggerEl) {
                            const rect = triggerEl.getBoundingClientRect();
                            const spaceBelow = window.innerHeight - rect.bottom;
                            const spaceAbove = rect.top;
                            
                            // Vertical detection
                            if (spaceBelow < 240 && spaceAbove > 200) {
                                $menu.addClass('drop-up');
                            } else {
                                $menu.removeClass('drop-up');
                            }

                            // Horizontal boundary detection (agar tidak terpotong tepi kanan layar/tabel)
                            const expectedWidth = Math.max(rect.width, 280);
                            if (rect.left + expectedWidth > window.innerWidth - 20) {
                                $menu.css({ 'left': 'auto', 'right': '0' });
                            } else {
                                $menu.css({ 'left': '0', 'right': 'auto' });
                            }
                        }

                        $wrapper.addClass('is-open');
                        $menu.addClass('is-open');
                        $trigger.addClass('is-open');
                        
                        // Elevate ALL parent containers (prescription-table-wrapper, emr-section-card, table-responsive, table, tr, td, card, modal, form-group, row, col)
                        $wrapper.parents('.prescription-table-wrapper, .emr-section-card, .table-responsive, table, tbody, tr, td, .card, .modal-content, .modal-body, .form-group, .row, .col, [class*="col-"]').addClass('macos-select-parent-elevated');

                        // Always refresh options from underlying select
                        renderOptions($searchInput.length > 0 ? $searchInput.val() : '');

                        if ($searchInput.length > 0) {
                            $searchInput.val('');
                            renderOptions('');
                            setTimeout(() => $searchInput.focus(), 50);
                        }
                    }
                });

                // Live search filter saat mengetik di dalam dropdown
                if ($searchInput.length > 0) {
                    $searchInput.on('input keyup', function(e) {
                        e.stopPropagation();
                        renderOptions($(this).val());
                    });
                }

                // Cegah klik di dalam menu dari menutup modal / trigger
                $menu.on('click mousedown touchstart', function(e) {
                    e.stopPropagation();
                });

                // Pilih opsi saat diklik
                $optionsList.on('click', '.macos-select-option', function(e) {
                    e.stopPropagation();
                    const val = $(this).data('value');
                    const text = $(this).find('span').text();

                    $select.val(val).trigger('change');
                    $trigger.find('.macos-select-label').text(text);
                    $menu.removeClass('is-open drop-up');
                    $trigger.removeClass('is-open');
                    $wrapper.removeClass('is-open');
                    $('.macos-select-parent-elevated').removeClass('macos-select-parent-elevated');
                });

                // Listen to external change or update events on native <select>
                $select.on('updateOptions updateLabel change', function() {
                    const selectedOpt = $select.find('option:selected');
                    const label = (selectedOpt.length && selectedOpt.val()) ? selectedOpt.text() : ($select.find('option:first').text() || '-- Pilih --');
                    $trigger.find('.macos-select-label').text(label);
                    renderOptions('');
                });

                // Tutup saat klik di luar dropdown
                $(document).on('click', function() {
                    $menu.removeClass('is-open drop-up');
                    $trigger.removeClass('is-open');
                    $wrapper.removeClass('is-open');
                    $('.macos-select-parent-elevated').removeClass('macos-select-parent-elevated');
                });

                // Tutup saat menekan tombol Escape
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape' && $menu.hasClass('is-open')) {
                        $menu.removeClass('is-open drop-up');
                        $trigger.removeClass('is-open');
                        $wrapper.removeClass('is-open');
                        $('.macos-select-parent-elevated').removeClass('macos-select-parent-elevated');
                    }
                });

                // Sinkronisasi label & opsi saat select diperbarui secara dinamis
                $select.on('change options:updated', function() {
                    const selText = $select.find('option:selected').text();
                    if (selText) {
                        $trigger.find('.macos-select-label').text(selText);
                    }
                    if ($searchInput.length > 0) {
                        renderOptions($searchInput.val());
                    } else {
                        renderOptions('');
                    }
                });

                $wrapper.append($trigger).append($menu);
                $select.after($wrapper);
            });
        };

        // Inisialisasi awal pada saat document ready
        window.initSearchableSelects();

        // Inisialisasi ulang otomatis setiap kali modal dibuka, tab berganti, atau konten dimuat
        $(document).on('shown.bs.modal', '.modal', function() {
            window.initSearchableSelects(this);
        });

        $(document).on('shown.bs.tab', function() {
            window.initSearchableSelects();
        });

        // Re-check secara berkala untuk elemen AJAX dinamis baru
        $(document).ajaxComplete(function() {
            window.initSearchableSelects();
        });

        // =========================================================================
        // DATATABLES STANDAR INDONESIA & SERVER-SIDE PROCESSING HELPER
        // =========================================================================
        if ($.fn.dataTable && $.fn.dataTable.ext) {
            $.fn.dataTable.ext.errMode = 'none';
        }
        window.dtIndonesianLanguage = {
            processing: '<div class="text-center py-2"><i class="fas fa-spinner fa-spin fa-2x text-teal"></i><div class="text-xs text-muted mt-1 font-weight-bold">Memproses data...</div></div>',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            infoFiltered: "(disaring dari _MAX_ total entri)",
            zeroRecords: "Tidak ada data yang cocok ditemukan",
            emptyTable: "Tidak ada data tersedia di tabel ini",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        };

        // Init regular client-side datatables (only if not marked for server-side)
        if ($('.datatable').length > 0) {
            $('.datatable').each(function() {
                if (!$(this).hasClass('datatable-serverside') && !$.fn.DataTable.isDataTable(this)) {
                    var $tbody = $(this).find('tbody');
                    if ($tbody.find('tr').length === 1 && $tbody.find('td[colspan]').length > 0) {
                        $tbody.empty();
                    }
                    try {
                        $(this).DataTable({
                            "language": window.dtIndonesianLanguage,
                            "pageLength": 10,
                            "autoWidth": false
                        });
                    } catch (err) {
                        console.warn('DataTable auto-init notice for table:', this, err);
                    }
                }
            });
        }

        // =========================================================================
        // REALTIME NOTIFIKASI INTERKONEKSI & SUARA PERINGATAN KERAS
        // =========================================================================
        let audioCtx = null;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (AudioContextClass) {
                    audioCtx = new AudioContextClass();
                }
            }
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Unlock browser audio context on any user activity in the browser
        $(document).on('click keydown scroll mousemove touchstart', function() {
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        });

        // Synthesize a loud, distinct, long resonant hospital alert chime (~2.0 seconds)
        window.playLoudHospitalAlertSound = function() {
            try {
                const ctx = getAudioContext();
                if (!ctx) return;

                if (ctx.state === 'suspended') {
                    ctx.resume().then(() => playSoundNow(ctx)).catch(() => playSoundNow(ctx));
                } else {
                    playSoundNow(ctx);
                }
            } catch (e) {
                console.warn('Audio alert error:', e);
            }
        };

        function playSoundNow(ctx) {
            try {
                const now = ctx.currentTime;
                const masterGain = ctx.createGain();
                masterGain.gain.setValueAtTime(1.0, now); // Loud volume
                masterGain.connect(ctx.destination);

                // Triple harmonic chime sequence: G5 (784Hz) -> C6 (1046Hz) -> E6 (1318Hz long decay)
                const tones = [
                    { freq: 784.00,  start: 0.00, duration: 0.40 },
                    { freq: 1046.50, start: 0.35, duration: 0.45 },
                    { freq: 1318.51, start: 0.75, duration: 1.35 }
                ];

                tones.forEach(function(t) {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(t.freq, now + t.start);

                    // Add rich overtone for clinical authority & clarity
                    const harmonic = ctx.createOscillator();
                    const harmGain = ctx.createGain();
                    harmonic.type = 'triangle';
                    harmonic.frequency.setValueAtTime(t.freq * 2, now + t.start);

                    // Main Envelope
                    gain.gain.setValueAtTime(0.0001, now + t.start);
                    gain.gain.exponentialRampToValueAtTime(0.95, now + t.start + 0.04);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + t.start + t.duration);

                    // Harmonic Envelope
                    harmGain.gain.setValueAtTime(0.0001, now + t.start);
                    harmGain.gain.exponentialRampToValueAtTime(0.45, now + t.start + 0.04);
                    harmGain.gain.exponentialRampToValueAtTime(0.0001, now + t.start + t.duration);

                    osc.connect(gain);
                    gain.connect(masterGain);

                    harmonic.connect(harmGain);
                    harmGain.connect(masterGain);

                    osc.start(now + t.start);
                    osc.stop(now + t.start + t.duration);

                    harmonic.start(now + t.start);
                    harmonic.stop(now + t.start + t.duration);
                });
            } catch (e) {
                console.warn('Audio play error:', e);
            }
        }

        // =========================================================================
        // PENGATURAN MODE PENGINGAT SUARA (AKTIF / HENING)
        // =========================================================================
        function isSoundEnabled() {
            return localStorage.getItem('notif_sound_enabled') !== 'false';
        }

        function updateSoundToggleUI() {
            const enabled = isSoundEnabled();
            const btn = $('#btn-toggle-sound');
            const icon = $('#icon-sound-toggle');
            if (enabled) {
                icon.removeClass('fa-volume-xmark text-secondary').addClass('fa-volume-high text-teal');
                btn.attr('title', 'Pengingat Suara: AKTIF (Klik untuk Mode Hening / Pengingat Visual Saja)');
            } else {
                icon.removeClass('fa-volume-high text-teal').addClass('fa-volume-xmark text-secondary');
                btn.attr('title', 'Pengingat Suara: HENING (Klik untuk Mengaktifkan Suara Lonjor)');
            }
        }

        updateSoundToggleUI();

        $('#btn-toggle-sound').click(function() {
            const current = isSoundEnabled();
            localStorage.setItem('notif_sound_enabled', current ? 'false' : 'true');
            updateSoundToggleUI();
            if (!current) {
                // Test sound once when user explicitly activates sound
                playLoudHospitalAlertSound();
            }
        });

        // Store dismissed notification IDs in session storage
        function getDismissedNotifs() {
            try {
                return JSON.parse(sessionStorage.getItem('dismissed_notif_ids') || '[]');
            } catch (e) {
                return [];
            }
        }
        function saveDismissedNotif(id) {
            const list = getDismissedNotifs();
            if (!list.includes(id)) {
                list.push(id);
                sessionStorage.setItem('dismissed_notif_ids', JSON.stringify(list));
            }
        }

        function getSeenNotifs() {
            try {
                return JSON.parse(sessionStorage.getItem('seen_notif_ids') || '[]');
            } catch (e) {
                return [];
            }
        }
        function saveSeenNotifs(ids) {
            sessionStorage.setItem('seen_notif_ids', JSON.stringify(ids));
        }

        let isInitialPoll = true;
        let lastNotifIds = getSeenNotifs();

        // Poll for notifications every 5 seconds
        function pollNotifications() {
            $.ajax({
                url: '<?= base_url("system/notif-api") ?>',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.status === 'success') {
                        const count = data.count || 0;
                        const list = data.list || [];
                        const dismissed = getDismissedNotifs();
                        const currentIds = list.map(i => i.id);

                        // 1. Update Navbar Bell Badge
                        if (count > 0) {
                            $('#notif-count').text(count).show();
                            $('#icon-nav-bell').addClass('text-teal');
                            $('#notif-header-text').text(count + ' Notifikasi Interkoneksi');
                        } else {
                            $('#notif-count').hide();
                            $('#icon-nav-bell').removeClass('text-teal');
                            $('#notif-header-text').text('0 Notifikasi Baru');
                        }

                        // 2. Populate Dropdown Menu
                        let notifHtml = '';
                        if (list.length > 0) {
                            list.forEach(function(item) {
                                const borderClass = item.type === 'danger' ? 'text-danger' : (item.type === 'warning' ? 'text-warning' : 'text-teal');
                                notifHtml += `
                                    <div class="dropdown-divider m-0"></div>
                                    <a href="${item.link}" class="dropdown-item py-2 px-3">
                                        <div class="d-flex align-items-start">
                                            <i class="${item.icon || 'fas fa-bell'} ${borderClass} mr-2 mt-1"></i>
                                            <div style="white-space: normal;">
                                                <strong class="d-block text-xs text-dark">${item.title}</strong>
                                                <p class="text-xs text-muted mb-0">${item.message}</p>
                                                <small class="text-secondary font-weight-bold" style="font-size: 10px;">${item.time || ''} &bull; ${item.badge || ''}</small>
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                        } else {
                            notifHtml = `
                                <a href="javascript:void(0)" class="dropdown-item text-center text-muted py-3">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Tidak ada notifikasi tertunda
                                </a>
                            `;
                        }
                        $('#notif-dropdown-items').html(notifHtml);

                        // 3. Trigger Alert & Render Floating Toast ONLY for Genuinely New Incoming items
                        if (!isInitialPoll) {
                            const newArrivals = list.filter(item => !lastNotifIds.includes(item.id) && !dismissed.includes(item.id));

                            if (newArrivals.length > 0) {
                                if (isSoundEnabled()) {
                                    playLoudHospitalAlertSound();
                                }

                                // Render FLOATING TOAST CARDS (Hanya muncul saat ada event baru & auto-close 6 detik)
                                const container = $('#floating-notif-container');
                                newArrivals.forEach(function(item) {
                                    const cardId = 'float-card-' + item.id.replace(/[^a-zA-Z0-9]/g, '_');
                                    if ($('#' + cardId).length === 0) {
                                        const accentColor = item.type === 'danger' ? '#dc2626' : (item.type === 'warning' ? '#d97706' : '#0d9f4f');
                                        const badgeBg = item.type === 'danger' ? 'badge-danger' : (item.type === 'warning' ? 'badge-warning' : 'badge-success');

                                        const cardHtml = `
                                            <div id="${cardId}" class="floating-notif-card" style="pointer-events: auto; background: #ffffff; border: 1px solid #b8b8b8; border-left: 5px solid ${accentColor}; border-radius: 4px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); padding: 14px; position: relative; animation: slideInRight 0.3s ease-out;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <div class="d-flex align-items-center">
                                                        <i class="${item.icon || 'fas fa-bell'} mr-2" style="color: ${accentColor}; font-size: 15px;"></i>
                                                        <span class="badge ${badgeBg} font-weight-bold" style="font-size: 10px; text-transform: uppercase;">${item.badge || item.category || 'Peringatan'}</span>
                                                    </div>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-dismiss-float" data-id="${item.id}" data-card="#${cardId}" title="Tutup Notifikasi" style="border: none; font-size: 16px; line-height: 1; padding: 0 4px;">&times;</button>
                                                </div>
                                                <div class="font-weight-bold text-dark text-xs mb-1" style="font-size: 13px;">${item.title}</div>
                                                <div class="text-secondary mb-2" style="font-size: 12px; line-height: 1.4;">${item.message}</div>
                                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                    <small class="text-muted" style="font-size: 10px;"><i class="fas fa-clock mr-1"></i> ${item.time || 'Real-time'}</small>
                                                    <div>
                                                        <a href="${item.link}" class="btn btn-xs btn-teal font-weight-bold mr-1" style="border-radius: 3px;">
                                                            <i class="fas fa-arrow-up-right-from-square mr-1"></i> Buka Modul
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-dismiss-float" data-id="${item.id}" data-card="#${cardId}" style="border-radius: 3px;">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        const $card = $(cardHtml);
                                        container.append($card);

                                        // Auto-close toast automatically after 6 seconds with smooth fade-out
                                        let autoCloseTimer = setTimeout(function() {
                                            $card.fadeOut(400, function() {
                                                $(this).remove();
                                            });
                                        }, 6000);

                                        // Pause timer on mouse hover, resume on mouse leave
                                        $card.hover(
                                            function() { clearTimeout(autoCloseTimer); },
                                            function() {
                                                autoCloseTimer = setTimeout(function() {
                                                    $card.fadeOut(400, function() {
                                                        $(this).remove();
                                                    });
                                                }, 4000);
                                            }
                                        );
                                    }
                                });

                                // =========================================================================
                                // SINKRONISASI DATA OTOMATIS TANPA PERLU REFRESH MANUAL (SEMUA DEVISI)
                                // =========================================================================
                                const currentPath = window.location.pathname.toLowerCase();
                                let shouldAutoRefreshPage = false;

                                newArrivals.forEach(function(item) {
                                    const itemUrl = (item.link || '').toLowerCase();
                                    const itemCat = (item.category || '').toLowerCase();

                                    // 1. Pelayanan Klinik, Poli & Pendaftaran
                                    if (itemCat === 'klinik' || itemUrl.includes('klinik/')) {
                                        if (currentPath.includes('klinik/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                    // 2. Farmasi & Apotek
                                    else if (itemCat === 'farmasi' || itemCat === 'pharmacy' || itemUrl.includes('apotek/')) {
                                        if (currentPath.includes('apotek/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                    // 3. Kasir, Billing & Keuangan
                                    else if (itemCat === 'keuangan' || itemCat === 'cashier' || itemUrl.includes('keuangan/')) {
                                        if (currentPath.includes('keuangan/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                    // 4. Logistik & Pengadaan (PR/PO)
                                    else if (itemCat === 'procurement' || itemUrl.includes('procurement/')) {
                                        if (currentPath.includes('procurement/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                    // 5. Resto & Dapur Gizi KDS
                                    else if (itemCat === 'resto' || itemUrl.includes('resto/')) {
                                        if (currentPath.includes('resto/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                    // 6. HRD & Kepegawaian
                                    else if (itemCat === 'hr' || itemCat === 'hrd' || itemUrl.includes('hrd/')) {
                                        if (currentPath.includes('hrd/') || currentPath.includes('dashboard')) {
                                            shouldAutoRefreshPage = true;
                                        }
                                    }
                                });

                                // Segarkan data LiveSync & DataTables secara instan TANPA reload halaman (Zero-Reload Murni)
                                if (shouldAutoRefreshPage) {
                                    if (window.LiveSyncEngine) {
                                        window.LiveSyncEngine.pollNow();
                                    }

                                    // Jika terdapat DataTable server-side, reload ajax datatable seketika tanpa refresh halaman
                                    if ($('.datatable-serverside').length > 0 && $.fn.DataTable) {
                                        $('.datatable-serverside').each(function() {
                                            if ($.fn.DataTable.isDataTable(this)) {
                                                try {
                                                    $(this).DataTable().ajax.reload(null, false);
                                                } catch (e) {
                                                    console.warn('DataTable auto-reload notice:', e);
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        }

                        isInitialPoll = false;
                        lastNotifIds = currentIds;
                        saveSeenNotifs(currentIds);
                    }
                }
            });
        }

        // Auto-dismiss in-page flash alert messages after 6 seconds with smooth fade
        if ($('.alert-flash-card').length > 0) {
            setTimeout(function() {
                $('.alert-flash-card').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 6000);
        }

        // Dismiss single floating notification card
        $(document).on('click', '.btn-dismiss-float', function() {
            const card = $($(this).data('card'));
            const notifId = $(this).data('id');
            saveDismissedNotif(notifId);
            card.fadeOut(200, function() {
                $(this).remove();
            });
        });

        // Dismiss all floating notifications
        $('#btn-clear-floating-notifs').click(function() {
            $('.floating-notif-card').each(function() {
                const btn = $(this).find('.btn-dismiss-float');
                const notifId = btn.data('id');
                if (notifId) saveDismissedNotif(notifId);
            });
            $('#floating-notif-container').empty();
        });

        // Run polling immediately and repeat every 5 seconds (Real-time live multi-browser)
        pollNotifications();
        setInterval(pollNotifications, 5000);

        // Sidebar Scroll Position Persistence & Active Menu Auto-Focus
        const sidebar = $('.sidebar');
        const mainSidebar = $('.main-sidebar');
        if (sidebar.length > 0) {
            const savedScrollPos = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedScrollPos !== null) {
                sidebar.scrollTop(parseInt(savedScrollPos));
                mainSidebar.scrollTop(parseInt(savedScrollPos));
                setTimeout(function() {
                    sidebar.scrollTop(parseInt(savedScrollPos));
                    mainSidebar.scrollTop(parseInt(savedScrollPos));
                }, 50);
            } else {
                const activeMenu = $('.nav-sidebar .nav-link.active');
                if (activeMenu.length > 0) {
                    activeMenu[0].scrollIntoView({ behavior: 'auto', block: 'center' });
                }
            }

            sidebar.on('scroll', function() {
                sessionStorage.setItem('sidebar_scroll_pos', $(this).scrollTop());
            });
            mainSidebar.on('scroll', function() {
                sessionStorage.setItem('sidebar_scroll_pos', $(this).scrollTop());
            });

            $('.nav-sidebar .nav-link').on('click', function() {
                const pos = sidebar.scrollTop() || mainSidebar.scrollTop() || 0;
                sessionStorage.setItem('sidebar_scroll_pos', pos);
            });
        }
    });

    // =========================================================================
    // PAGE PRELOADER CONTROLLER (loading_page.md)
    // =========================================================================
    window.hidePreloader = function() {
        const preloader = document.getElementById('page-preloader');
        if (preloader) {
            preloader.classList.add('fade-out');
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 200);
        }
    };

    // 1. Sembunyikan preloader saat halaman selesai dimuat penuh
    window.addEventListener('load', window.hidePreloader);
    window.addEventListener('pageshow', window.hidePreloader);
    window.addEventListener('focus', window.hidePreloader);

    // Fallback safety timeout (maksimal 2.5 detik)
    setTimeout(window.hidePreloader, 2500);

    // 2. Tampilkan preloader saat form disubmit (mencegah double submit)
    document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        if (form.target === '_blank' || form.classList.contains('no-preloader')) {
            return;
        }
        const preloader = document.getElementById('page-preloader');
        if (preloader) {
            preloader.style.display = 'flex';
            preloader.offsetHeight; // memicu reflow
            preloader.classList.remove('fade-out');
        }
    });

    // 3. Tampilkan preloader saat navigasi internal berpindah halaman
    document.addEventListener('click', function(e) {
        if (e.defaultPrevented) return;

        const link = e.target.closest('a');
        if (!link) return;
        
        const href = link.getAttribute('href');
        const target = link.getAttribute('target');
        
        if (!href || 
            href.startsWith('#') || 
            href.startsWith('javascript:') || 
            target === '_blank' || 
            link.hasAttribute('download') || 
            link.classList.contains('no-preloader') ||
            link.classList.contains('dropdown-toggle') ||
            link.getAttribute('data-toggle') ||
            link.getAttribute('data-widget')
        ) {
            return;
        }

        // Jika link memiliki dialog konfirmasi (onclick confirm), jangan aktifkan preloader otomatis
        if (link.hasAttribute('onclick') && link.getAttribute('onclick').toLowerCase().includes('confirm')) {
            return;
        }
        
        const preloader = document.getElementById('page-preloader');
        if (preloader) {
            preloader.style.display = 'flex';
            preloader.offsetHeight; // memicu reflow
            preloader.classList.remove('fade-out');
        }
    });
</script>

<!-- FLOATING PERSISTENT NOTIFICATION CONTAINER (Melayang & Tetap Terbuka) -->
<div id="floating-notif-container" style="position: fixed; top: 65px; right: 20px; z-index: 1060; width: 380px; max-width: calc(100vw - 40px); pointer-events: none; display: flex; flex-direction: column; gap: 10px;"></div>

<!-- MODAL PANEL PUSAT PANGGILAN SUARA & ANTREAN MULTI-LAYANAN -->
<div class="modal fade" id="vcm-panel-modal" tabindex="-1" role="dialog" aria-labelledby="vcmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-teal text-white py-3">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="vcmModalTitle">
                    <i class="fas fa-bullhorn mr-2"></i> Voice Caller &amp; Manajemen Antrean Multi-Layanan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Kolom Kiri: Form Test Panggilan Cepat -->
                    <div class="col-md-6 border-right">
                        <h6 class="font-weight-bold text-teal mb-3"><i class="fas fa-paper-plane mr-1"></i> Siarkan Panggilan Baru</h6>
                        <div class="form-group">
                            <label class="font-weight-bold text-xs text-secondary mb-1">UNIT / LOKET PELAYANAN</label>
                            <select id="vcm-test-service-type" class="form-control form-control-sm">
                                <option value="poliklinik" data-counter="Poliklinik Umum">Poliklinik Umum</option>
                                <option value="poliklinik" data-counter="Poli Gigi dan Mulut">Poli Gigi &amp; Mulut</option>
                                <option value="poliklinik" data-counter="Poli Spesialis Anak">Poli Spesialis Anak</option>
                                <option value="triage" data-counter="Ruang Tanda Vital Perawat">Ruang TTV Perawat</option>
                                <option value="pendaftaran" data-counter="Loket Pendaftaran 1">Loket Pendaftaran 1</option>
                                <option value="pendaftaran" data-counter="Loket Pendaftaran 2">Loket Pendaftaran 2</option>
                                <option value="farmasi" data-counter="Loket Farmasi dan Apotek">Loket Farmasi &amp; Apotek</option>
                                <option value="kasir" data-counter="Kasir Pembayaran">Kasir Pembayaran</option>
                                <option value="laboratorium" data-counter="Laboratorium">Laboratorium</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-xs text-secondary mb-1">NOMOR ANTREAN</label>
                                    <input type="text" id="vcm-test-queue-no" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: A-001" value="A-001">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-xs text-secondary mb-1">PRIORITAS</label>
                                    <select id="vcm-test-priority" class="form-control form-control-sm">
                                        <option value="1">Normal (1)</option>
                                        <option value="0">Urgent / VIP (0)</option>
                                        <option value="2">Low (2)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-xs text-secondary mb-1">NAMA PASIEN</label>
                            <input type="text" id="vcm-test-patient-name" class="form-control form-control-sm" placeholder="Nama Pasien" value="Budi Santoso">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-teal btn-sm font-weight-bold flex-grow-1 mr-2" id="vcm-btn-test-trigger">
                                <i class="fas fa-play mr-1"></i> Siarkan Panggilan
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" onclick="window.VCM.stop();">
                                <i class="fas fa-stop mr-1"></i> Stop
                            </button>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Pengaturan Suara & Antrean Aktif -->
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-teal mb-3"><i class="fas fa-sliders mr-1"></i> Pengaturan Mesin Suara</h6>
                        <div class="form-group mb-2">
                            <div class="d-flex justify-content-between">
                                <label class="font-weight-bold text-xs text-secondary mb-0">KECEPATAN BICARA (RATE)</label>
                                <small id="vcm-lbl-rate" class="font-weight-bold text-teal">0.88x</small>
                            </div>
                            <input type="range" class="custom-range" id="vcm-input-rate" min="0.6" max="1.4" step="0.05" value="0.88">
                        </div>
                        <div class="form-group mb-2">
                            <div class="d-flex justify-content-between">
                                <label class="font-weight-bold text-xs text-secondary mb-0">NADA SUARA (PITCH)</label>
                                <small id="vcm-lbl-pitch" class="font-weight-bold text-teal">1.0</small>
                            </div>
                            <input type="range" class="custom-range" id="vcm-input-pitch" min="0.7" max="1.3" step="0.05" value="1.0">
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between">
                                <label class="font-weight-bold text-xs text-secondary mb-0">VOLUME SUARA</label>
                                <small id="vcm-lbl-volume" class="font-weight-bold text-teal">100%</small>
                            </div>
                            <input type="range" class="custom-range" id="vcm-input-volume" min="0.1" max="1.0" step="0.05" value="1.0">
                        </div>
                        <div class="alert alert-info py-2 px-3 text-xs mb-0" style="border-radius: 8px;">
                            <i class="fas fa-shield-halved mr-1"></i> <strong>Anti-Collision Mutex:</strong> Panggilan dari berbagai unit akan diurutkan otomatis secara aman tanpa pernah tumpang tindih.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                <a href="<?= base_url('klinik/display') ?>" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold">
                    <i class="fas fa-tv mr-1"></i> Buka Layar Display TV Ruang Tunggu
                </a>
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi Kontrol Panel Voice Caller
    $(document).ready(function() {
        if (typeof window.VCM !== 'undefined') {
            const s = window.VCM.getSettings();
            $('#vcm-input-rate').val(s.rate || 0.88);
            $('#vcm-lbl-rate').text((s.rate || 0.88) + 'x');
            $('#vcm-input-pitch').val(s.pitch || 1.0);
            $('#vcm-lbl-pitch').text(s.pitch || 1.0);
            $('#vcm-input-volume').val(s.volume || 1.0);
            $('#vcm-lbl-volume').text(Math.round((s.volume || 1.0) * 100) + '%');

            $('#vcm-input-rate').on('input', function() {
                const val = parseFloat($(this).val());
                $('#vcm-lbl-rate').text(val.toFixed(2) + 'x');
                const cur = window.VCM.getSettings();
                cur.rate = val;
                window.VCM.setSettings(cur);
            });
            $('#vcm-input-pitch').on('input', function() {
                const val = parseFloat($(this).val());
                $('#vcm-lbl-pitch').text(val.toFixed(2));
                const cur = window.VCM.getSettings();
                cur.pitch = val;
                window.VCM.setSettings(cur);
            });
            $('#vcm-input-volume').on('input', function() {
                const val = parseFloat($(this).val());
                $('#vcm-lbl-volume').text(Math.round(val * 100) + '%');
                const cur = window.VCM.getSettings();
                cur.volume = val;
                window.VCM.setSettings(cur);
            });

            $('#vcm-btn-test-trigger').on('click', function() {
                const sType = $('#vcm-test-service-type').val();
                const cName = $('#vcm-test-service-type option:selected').data('counter') || 'Poliklinik Umum';
                const qNo   = $('#vcm-test-queue-no').val().trim() || 'A-001';
                const pName = $('#vcm-test-patient-name').val().trim() || 'Budi Santoso';
                const prio  = parseInt($('#vcm-test-priority').val() || 1, 10);

                window.VCM.triggerServerCall({
                    service_type: sType,
                    counter_name: cName,
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: prio
                }, function(err, res) {
                    if (!err && typeof toastr !== 'undefined') {
                        toastr.success('Panggilan ' + qNo + ' (' + pName + ') disiarkan ke antrean terpusat.', '📢 Voice Call');
                    }
                });
            });
        }
    });

    // =========================================================================
    // MODUL PEMILIH TEMA PANEL ADMIN (Excel Paper / macOS / Paper White / Emerald / XP)
    // =========================================================================
    function applyAdminTheme(themeName) {
        // Hapus kelas tema sebelumnya
        document.body.classList.remove('theme-excel-paper', 'theme-macos', 'theme-paper-white', 'theme-modern-emerald', 'theme-windows-xp');
        document.documentElement.classList.remove('theme-excel-paper', 'theme-macos', 'theme-paper-white', 'theme-modern-emerald', 'theme-windows-xp');
        
        // Tambahkan tema terpilih
        document.body.classList.add(themeName);
        document.documentElement.classList.add(themeName);

        // Simpan preferensi di LocalStorage
        localStorage.setItem('sawamawa_admin_theme', themeName);

        // Update indikator checkmark di dropdown
        $('.theme-select-opt').each(function() {
            var t = $(this).data('theme');
            if (t === themeName) {
                $(this).find('.theme-check').removeClass('d-none');
                $(this).addClass('bg-light font-weight-bold');
            } else {
                $(this).find('.theme-check').addClass('d-none');
                $(this).removeClass('bg-light font-weight-bold');
            }
        });
    }

    $(document).ready(function() {
        var currentTheme = localStorage.getItem('sawamawa_admin_theme') || 'theme-macos';
        applyAdminTheme(currentTheme);

        $(document).on('click', '.theme-select-opt', function(e) {
            e.preventDefault();
            var chosenTheme = $(this).data('theme');
            applyAdminTheme(chosenTheme);

            if (typeof toastr !== 'undefined') {
                var themeTitle = {
                    'theme-excel-paper': 'Putih di Atas Kertas (Classic Excel Grid)',
                    'theme-macos': 'macOS 11 Big Sur (Apple Frosted Glass)',
                    'theme-paper-white': 'Paper White (Soft Precision Slate)',
                    'theme-modern-emerald': 'Modern Emerald (Toska Elegan)',
                    'theme-windows-xp': 'Windows XP (Retro Luna Blue)'
                }[chosenTheme] || 'Tema Kustom';
                toastr.info('Tema tampilan dialihkan ke ' + themeTitle, '🎨 Tema Berubah');
            }
        });
    });

    // =========================================================================
    // UNIVERSAL ZERO-RELOAD SPA PAGE NAVIGATOR ENGINE (CLEAN & SEAMLESS)
    // =========================================================================
    (function() {
        function updateActiveNavLinks(targetUrl) {
            try {
                const urlObj = new URL(targetUrl, window.location.origin);
                const path = urlObj.pathname.toLowerCase();

                let activeSidebarPrefix = '';
                if (path.includes('/apotek/')) activeSidebarPrefix = 'apotek';
                else if (path.includes('/resto/')) activeSidebarPrefix = 'resto';
                else if (path.includes('/keuangan/')) activeSidebarPrefix = 'keuangan';
                else if (path.includes('/accounting/')) activeSidebarPrefix = 'accounting';
                else if (path.includes('/procurement/')) activeSidebarPrefix = 'procurement';
                else if (path.includes('/hrd/') || path.includes('/inventaris/')) activeSidebarPrefix = path.includes('/inventaris/') ? 'inventaris' : 'hrd';
                else if (path.includes('/system/master-')) activeSidebarPrefix = 'system/master';
                else if (path.includes('/system/')) activeSidebarPrefix = 'system';
                else if (path.includes('/klinik/')) activeSidebarPrefix = 'klinik';
                else if (path.includes('/dashboard')) activeSidebarPrefix = 'dashboard';

                // Update Sidebar Link yang Aktif (Hierarkis & Treeview aware)
                $('.nav-sidebar .nav-link').removeClass('active');
                
                let matchedLink = null;
                let bestMatchLen = 0;

                $('.nav-sidebar a.nav-link').each(function() {
                    const href = $(this).attr('href');
                    if (!href || href === '#' || href.startsWith('javascript:')) return;
                    try {
                        const linkPath = (new URL(href, window.location.origin)).pathname.toLowerCase();
                        if (path === linkPath) {
                            matchedLink = $(this);
                            bestMatchLen = 99999;
                            return false;
                        } else if (linkPath !== '/' && path.startsWith(linkPath) && linkPath.length > bestMatchLen) {
                            matchedLink = $(this);
                            bestMatchLen = linkPath.length;
                        }
                    } catch(e) {}
                });

                if (matchedLink && matchedLink.length) {
                    matchedLink.addClass('active');
                    const $parentTreeview = matchedLink.closest('.has-treeview');
                    if ($parentTreeview.length) {
                        $parentTreeview.addClass('menu-open');
                        $parentTreeview.children('.nav-link').addClass('active');
                        $parentTreeview.children('.nav-treeview').show();
                    }
                } else if (activeSidebarPrefix) {
                    $('.nav-sidebar a.nav-link').each(function() {
                        const href = ($(this).attr('href') || '').toLowerCase();
                        if (href && href !== '#' && href.includes(activeSidebarPrefix)) {
                            $(this).addClass('active');
                            const $parentTreeview = $(this).closest('.has-treeview');
                            if ($parentTreeview.length) {
                                $parentTreeview.addClass('menu-open');
                                $parentTreeview.children('.nav-link').addClass('active');
                                $parentTreeview.children('.nav-treeview').show();
                            }
                            return false;
                        }
                    });
                }
            } catch(e) {}
        }

        // Initialize active nav states on initial page load
        $(document).ready(function() {
            updateActiveNavLinks(window.location.href);

            // Simpan status buka/tutup sidebar saat user mengklik toggle pushmenu
            $(document).on('collapsed.lte.pushmenu', function() {
                localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
            });
            $(document).on('shown.lte.pushmenu', function() {
                localStorage.setItem('sawamawa_sidebar_state', 'expanded');
            });
        });

        // =========================================================================
        // GLOBAL FORCE UPDATE & SYSTEM CACHE PURGE CONTROLLER
        // =========================================================================
        window.forceUpdateSystem = function(silentOrInteractive, callback) {
            // Mode handling: jika dipanggil dari tombol klik (tanpa argumen) default interactive = true
            const isSilent = (silentOrInteractive === true);

            // Jika offlineSyncEngine tersedia dan dipanggil dalam mode interaktif, delegasikan ke wizard lengkap
            if (!isSilent && window.offlineSyncEngine && typeof window.offlineSyncEngine.forceUpdateSystem === 'function') {
                return window.offlineSyncEngine.forceUpdateSystem(true);
            }

            try {
                // 1. Bersihkan Service Worker Registrations
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.getRegistrations().then(function(regs) {
                        for (let r of regs) {
                            try { r.unregister(); } catch(e) {}
                        }
                    }).catch(function() {});
                }

                // 2. Bersihkan Service Worker Cache Storage jika ada
                if ('caches' in window) {
                    caches.keys().then(function(names) {
                        for (let name of names) {
                            try { caches.delete(name); } catch(e) {}
                        }
                    }).catch(function() {});
                }

                // 3. Bersihkan SessionStorage & LocalStorage non-esensial (pertahankan tema, sidebar state & audio)
                const preserveKeys = [
                    'sawamawa_admin_theme',
                    'sawamawa_sidebar_state',
                    'sidebar_scroll_pos',
                    'vcm_settings',
                    'offline_draft_prescriptions'
                ];
                
                // Bersihkan sessionStorage non-kritis
                for (let i = sessionStorage.length - 1; i >= 0; i--) {
                    const k = sessionStorage.key(i);
                    if (k && !preserveKeys.includes(k)) {
                        sessionStorage.removeItem(k);
                    }
                }

                // 4. Bersihkan DataTables cached state
                if ($.fn.DataTable) {
                    $('.dataTable, table[id]').each(function() {
                        if ($.fn.DataTable.isDataTable(this)) {
                            try {
                                $(this).DataTable().state.clear();
                            } catch(e) {}
                        }
                    });
                }

                // 5. Trigger pembersihan cache server & OPcache PHP di background
                const cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
                try {
                    fetch(cleanBase + '/system/clear-cache?format=json', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).catch(function() {});
                } catch(e) {}

                // 6. Jika bukan mode senyap (silent), tampilkan feedback visual & reload halaman penuh
                if (!isSilent) {
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Membersihkan cache aplikasi dan memuat versi terbaru...', '🔄 Perbarui Sistem', { timeOut: 1200 });
                    }
                    setTimeout(function() {
                        const cleanUrl = new URL(window.location.href);
                        cleanUrl.searchParams.set('_v', Date.now());
                        window.location.href = cleanUrl.toString();
                    }, 400);
                } else if (typeof callback === 'function') {
                    callback();
                }
            } catch(e) {
                if (!isSilent) {
                    window.location.reload(true);
                }
            }
        };

        window.navigateSpa = function(url, pushHistory, forceBypassCache) {
            if (pushHistory === undefined) pushHistory = true;
            if (forceBypassCache === undefined) forceBypassCache = true;
            if (!url || url === '#' || url.startsWith('javascript:')) return;
            if (url === window.location.href && !forceBypassCache) return;

            const $prog = $('#spa-progressbar');
            $prog.css({ width: '25%', opacity: 1 });

            const $wrapper = $('.content-wrapper');
            $wrapper.css({ opacity: 0.5, transition: 'opacity 0.12s ease' });

            // Clean up open modal backdrops or active select dropdowns & tooltips
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.select2-container--open').remove();
            $('.macos-select-wrapper.is-open').removeClass('is-open');
            if ($.fn.tooltip) {
                $('[data-toggle="tooltip"], [title]').tooltip('dispose');
            }

            // Destroy existing DataTables cleanly to prevent "Cannot reinitialise DataTable"
            if ($.fn.DataTable) {
                $('.dataTable, table[id]').each(function() {
                    if ($.fn.DataTable.isDataTable(this)) {
                        try {
                            $(this).DataTable().destroy(false);
                        } catch(e) {}
                    }
                });
            }

            // Bersihkan cache temporary sebelum render halaman baru
            if (forceBypassCache && typeof window.forceUpdateSystem === 'function') {
                window.forceUpdateSystem(true);
            }

            $prog.css({ width: '60%' });

            // Bentuk fetchUrl dengan timestamp parameter agar browser dan proxy tidak melayani cache basi
            let fetchUrl = url;
            try {
                const u = new URL(url, window.location.origin);
                u.searchParams.set('_ts', Date.now());
                fetchUrl = u.toString();
            } catch(e) {}

            $.ajax({
                url: fetchUrl,
                type: 'GET',
                cache: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache'
                },
                success: function(html) {
                    $prog.css({ width: '90%' });

                    if (pushHistory) {
                        window.history.pushState({ spaUrl: url }, '', url);
                    }

                    // Extract new title & content
                    const $temp = $('<div>').html(html);
                    const newTitle = $temp.find('title').text();
                    if (newTitle) document.title = newTitle;

                    let $newContent = $temp.find('.content-wrapper');
                    if (!$newContent.length) {
                        $newContent = $temp.find('.content, #main-content');
                    }

                    if ($newContent.length) {
                        $wrapper.html($newContent.html());
                    } else {
                        window.location.href = url;
                        return;
                    }

                    // Update active nav status
                    updateActiveNavLinks(url);

                    // Run page-specific scripts
                    $newContent.find('script').each(function() {
                        const src = $(this).attr('src');
                        if (src) {
                            if (!$('script[src="' + src + '"]').length) {
                                $.getScript(src);
                            }
                        } else {
                            const code = $(this).text();
                            if (code && !code.includes('spa-progressbar') && !code.includes('navigateSpa')) {
                                try {
                                    window.eval(code);
                                } catch(err) {
                                    console.warn('SPA script eval error:', err);
                                }
                            }
                        }
                    });

                    // Re-initialize plugins
                    setTimeout(function() {
                        if (window.initSearchableSelects) {
                            window.initSearchableSelects();
                        }
                        if ($.fn.tooltip) {
                            $('[title], [data-toggle="tooltip"]').tooltip();
                        }
                        if ($.fn.dataTable) {
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
                        }
                        $wrapper.css({ opacity: 1 });
                        $prog.css({ width: '100%' });
                        setTimeout(() => $prog.css({ opacity: 0, width: '0%' }), 200);
                    }, 80);
                },
                error: function() {
                    window.location.href = url;
                }
            });
        };

        // Attach click interceptor for internal SPA navigation
        $(document).on('click', 'a.btn-clinical-nav, .nav-sidebar a.nav-link', function(e) {
            const href = $(this).attr('href');
            const target = $(this).attr('target');

            if (!href || href === '#' || href.startsWith('javascript:') || target === '_blank') {
                return;
            }

            // Auto-collapse sidebar on mobile/tablet screens when a navigation link is clicked
            if ($(window).width() < 992 && $(this).closest('.nav-sidebar').length) {
                $('body').addClass('sidebar-collapse').removeClass('sidebar-open');
            }

            // Only intercept internal application links (not logout, not downloads)
            if (href.includes(window.location.origin) || href.startsWith('/') || href.startsWith('./') || !href.includes('://')) {
                if (!href.includes('logout') && !href.includes('export') && !href.includes('cetak') && !href.includes('pdf')) {
                    e.preventDefault();
                    window.navigateSpa(href, true, true);
                }
            }
        });

        // Close sidebar on mobile/tablet when clicking the content area
        $(document).on('click', '.content-wrapper', function() {
            if ($(window).width() < 992 && $('body').hasClass('sidebar-open')) {
                $('body').addClass('sidebar-collapse').removeClass('sidebar-open');
            }
        });

        // Handle browser Back / Forward history
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.spaUrl) {
                window.navigateSpa(e.state.spaUrl, false);
            } else {
                window.navigateSpa(window.location.href, false);
            }
        });
    })();
</script>

<style>
    /* Universal High-Efficiency Sidebar Typography & Display */
    .main-sidebar {
        box-shadow: 1px 0 0 0 rgba(0, 0, 0, 0.06);
    }
    .sidebar .nav-sidebar .nav-item {
        margin-bottom: 2px;
    }
    .sidebar .nav-sidebar .nav-link {
        border-radius: 7px;
        font-size: 13.5px;
        padding: 7px 12px;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
    }
    .main-sidebar .nav-sidebar .nav-link p {
        margin: 0 !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        color: #1e293b;
    }

    body.theme-macos .sidebar .nav-link p {
        color: #1d1d1f !important;
    }

    /* When active */
    .sidebar .nav-sidebar .nav-link.active p,
    body.theme-macos .sidebar .nav-link.active p {
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    /* In Collapsed State (closed and not hovered), cleanly hide text & headers */
    body.sidebar-collapse:not(.sidebar-open) .main-sidebar:not(:hover) .nav-sidebar .nav-link p,
    body.sidebar-collapse:not(.sidebar-open) .main-sidebar:not(:hover) .nav-header {
        display: none !important;
    }

    /* When expanded or hovered in collapsed mode, show text & headers with 100% clarity */
    body.sidebar-collapse.sidebar-mini .main-sidebar:hover .nav-sidebar .nav-link p,
    body.sidebar-collapse.sidebar-mini .main-sidebar.sidebar-focused .nav-sidebar .nav-link p,
    body:not(.sidebar-collapse) .main-sidebar .nav-sidebar .nav-link p,
    body.sidebar-open .main-sidebar .nav-sidebar .nav-link p {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    body.sidebar-collapse.sidebar-mini .main-sidebar:hover .nav-header,
    body.sidebar-collapse.sidebar-mini .main-sidebar.sidebar-focused .nav-header,
    body:not(.sidebar-collapse) .main-sidebar .nav-header,
    body.sidebar-open .main-sidebar .nav-header {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* =========================================================================
       MODERN TOP HEADER NAVBAR & SPACIOUS ICON CONTROLS
       ========================================================================= */
    .modern-macos-header {
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 12px rgba(0, 0, 0, 0.02) !important;
        padding: 0.5rem 1.25rem !important;
        height: 60px !important;
    }

    .nav-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(241, 245, 249, 0.85);
        border: 1px solid rgba(203, 213, 225, 0.5);
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        outline: none !important;
        position: relative;
    }

    .nav-icon-btn:hover {
        background: #ffffff;
        color: #007aff;
        border-color: #007aff;
        box-shadow: 0 3px 10px rgba(0, 122, 255, 0.15);
        transform: translateY(-1.5px);
    }

    .nav-icon-btn.btn-force-update:hover {
        color: #0d9488;
        border-color: #0d9488;
        box-shadow: 0 3px 10px rgba(13, 148, 136, 0.18);
    }
    .nav-icon-btn.btn-force-update:hover i {
        transform: rotate(180deg);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-icon-btn.btn-control-hub {
        background: rgba(0, 122, 255, 0.08);
        border-color: rgba(0, 122, 255, 0.25);
    }
    .nav-icon-btn.btn-control-hub:hover {
        background: #007aff;
        color: #ffffff;
        border-color: #007aff;
        box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
    }
    .nav-icon-btn.btn-control-hub:hover i {
        color: #ffffff !important;
    }

    .nav-badge-counter {
        position: absolute;
        top: -4px;
        right: -4px;
        font-size: 9.5px;
        padding: 2px 5px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        border: 2px solid #ffffff;
    }

    .online-indicator-dot {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        border: 1.5px solid #ffffff;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
    }

    .nav-profile-btn {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px 3px 4px;
        border-radius: 20px;
        background: rgba(241, 245, 249, 0.85);
        border: 1px solid rgba(203, 213, 225, 0.5);
        color: #1e293b;
        text-decoration: none !important;
        transition: all 0.18s ease;
        height: 38px;
    }
    .nav-profile-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        color: #007aff;
        transform: translateY(-1px);
    }
    .user-avatar-wrap {
        position: relative;
        width: 30px;
        height: 30px;
    }
    .user-avatar-img, .user-avatar-initials {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }
    .user-avatar-initials {
        background: linear-gradient(135deg, #0d9488, #007aff);
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* =========================================================================
       MODERNIZED SPOTLIGHT FAST SEARCH TRIGGER & MODAL
       ========================================================================= */
    .btn-modern-spotlight {
        background: rgba(241, 245, 249, 0.85);
        border: 1px solid rgba(203, 213, 225, 0.6);
        border-radius: 24px;
        padding: 4px 14px;
        width: 310px;
        height: 38px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #64748b;
        outline: none !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .btn-modern-spotlight:hover {
        background: #ffffff;
        border-color: #007aff;
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.12), 0 3px 8px rgba(0, 0, 0, 0.04);
        color: #1e293b;
        transform: translateY(-1px);
    }
    .spotlight-search-sparkle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(0, 122, 255, 0.1);
        color: #007aff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .spotlight-placeholder {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
    }
    .spotlight-badge-kbd {
        background: #ffffff;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        font-family: inherit;
        line-height: 1;
        letter-spacing: 0.3px;
    }

    /* Modal Backdrop & Acrylic Glass Box */
    .macos-spotlight-modal .modal-dialog {
        margin: 12vh auto 0 auto !important;
        max-width: 680px;
        width: 94%;
    }
    .macos-spotlight-modal .modal-content {
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(35px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(35px) saturate(190%) !important;
        border: 1px solid rgba(255, 255, 255, 0.9) !important;
        border-radius: 20px !important;
        box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden;
    }
    .spotlight-search-header {
        padding: 0 22px;
        height: 64px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
    }
    .spotlight-search-input {
        border: none !important;
        outline: none !important;
        background: transparent !important;
        font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", Roboto, sans-serif !important;
        font-size: 17px !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        width: 100%;
        padding: 0;
        box-shadow: none !important;
    }
    .spotlight-search-input::placeholder {
        color: #94a3b8 !important;
        font-weight: 400 !important;
        font-size: 15.5px !important;
    }
    .spotlight-esc-tag {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        outline: none !important;
        transition: all 0.15s ease;
    }
    .spotlight-esc-tag:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .spotlight-results-scroll {
        max-height: 440px;
        overflow-y: auto;
        padding: 10px 12px;
        background: #f8fafc;
    }
    .spotlight-section-header {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        padding: 10px 14px 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .spotlight-result-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 12px;
        margin-bottom: 4px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        color: #1e293b;
        cursor: pointer;
    }
    .spotlight-result-item:hover,
    .spotlight-result-item.is-selected {
        background: #007aff !important;
        border-color: #007aff !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(0, 122, 255, 0.35);
        transform: translateY(-1px);
    }
    .spotlight-result-item:hover .text-dark,
    .spotlight-result-item.is-selected .text-dark {
        color: #ffffff !important;
    }
    .spotlight-result-item:hover .text-muted,
    .spotlight-result-item.is-selected .text-muted {
        color: rgba(255, 255, 255, 0.82) !important;
    }
    .spotlight-result-item:hover .badge-light,
    .spotlight-result-item.is-selected .badge-light {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
    }
    .spotlight-result-item:hover .badge-primary,
    .spotlight-result-item.is-selected .badge-primary {
        background: #ffffff !important;
        color: #007aff !important;
    }
    .spotlight-result-item:hover .spotlight-quick-kbd,
    .spotlight-result-item.is-selected .spotlight-quick-kbd {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.4) !important;
    }
    .spotlight-quick-kbd {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        font-size: 11px;
        padding: 2px 7px;
        border-radius: 5px;
        font-weight: 600;
    }
    .spotlight-app-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        margin-right: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .spotlight-app-icon.icon-patient { background: linear-gradient(135deg, #007aff, #0056b3); color: #fff; }
    .spotlight-app-icon.icon-menu { background: linear-gradient(135deg, #0d9488, #14b8a6); color: #fff; }
    .spotlight-app-icon.icon-green { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
    .spotlight-app-icon.icon-amber { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .spotlight-app-icon.icon-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }
    .spotlight-result-item:hover .spotlight-app-icon,
    .spotlight-result-item.is-selected .spotlight-app-icon {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .spotlight-footer {
        padding: 10px 20px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .spotlight-footer-keys {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 11.5px;
        color: #64748b;
    }
    .spotlight-footer-keys kbd {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .btn-action-pill {
        border-radius: 14px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        padding: 3px 9px !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
    }

    /* =========================================================================
       PUSAT KONTROL & MENU LENGKAP MODAL STYLING (LAUNCHPAD HUB)
       ========================================================================= */
    .control-menu-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.18s ease-in-out;
        text-decoration: none !important;
        cursor: pointer;
        height: 100%;
    }
    .control-menu-card:hover {
        border-color: #007aff;
        background: #ffffff;
        box-shadow: 0 6px 16px rgba(0, 122, 255, 0.12);
        transform: translateY(-1.5px);
    }
    .control-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .bg-primary-soft { background: rgba(0, 122, 255, 0.1); color: #007aff; }
    .bg-teal-soft { background: rgba(13, 148, 136, 0.1); color: #0d9488; }
    .bg-success-soft { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-amber-soft { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-orange-soft { background: rgba(249, 115, 22, 0.1); color: #f97316; }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-purple-soft { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .bg-indigo-soft { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    
    .control-theme-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.15s ease;
        text-decoration: none !important;
        color: #1e293b;
    }
    .control-theme-card:hover {
        border-color: #0d9488;
        background: #f8fafc;
        transform: translateY(-1px);
    }
    .theme-color-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }
</style>

<!-- Modal Pusat Kontrol & Menu Lengkap (Launchpad Hub) -->
<div class="modal fade" id="modal-control-center" tabindex="-1" role="dialog" aria-labelledby="modalControlCenterLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #f8fafc;">
            <!-- Modal Header -->
            <div class="modal-header bg-white px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mr-3" style="width: 40px; height: 40px; font-size: 18px; box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);">
                        <i class="fas fa-table-cells-large"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalControlCenterLabel" style="font-size: 17px; letter-spacing: -0.3px;">Pusat Kontrol &amp; Menu Lengkap</h5>
                        <p class="text-xs text-muted mb-0">Navigasi terpusat, aksi cepat pelayanan, layar publik antrean, tema, dan pemeliharaan sistem</p>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <div class="input-group input-group-sm control-center-search-box" style="width: 240px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0" style="border-radius: 20px 0 0 20px;"><i class="fas fa-magnifying-glass text-muted" style="font-size: 11px;"></i></span>
                        </div>
                        <input type="text" id="control-center-filter-input" class="form-control bg-light border-left-0" placeholder="Filter menu cepat..." style="border-radius: 0 20px 20px 0; font-size: 12px;">
                    </div>
                    <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close" style="opacity: 0.7; outline: none; font-size: 24px; padding: 0 8px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" id="control-center-content-body">
                <div class="row">
                    <!-- Column 1: Aksi Cepat Pelayanan & Transaksi -->
                    <div class="col-lg-7 col-md-12 mb-4 control-group-section" data-section="pelayanan">
                        <div class="d-flex align-items-center justify-content-between mb-2.5 pb-1 border-bottom">
                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                <i class="fas fa-bolt text-warning mr-1.5"></i> Aksi Cepat Pelayanan &amp; Transaksi
                            </h6>
                            <span class="badge badge-light border text-muted text-xs">Paling Sering Digunakan</span>
                        </div>
                        <div class="row" style="gap: 10px 0;">
                            <?php if ($canAccessClinic): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="pendaftaran registrasi pasien baru rawat jalan berkas rm antrean">
                                <a href="<?= base_url('klinik/pendaftaran') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-primary-soft text-primary mr-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Registrasi Pasien Baru</div>
                                        <div class="text-xs text-muted text-truncate">Pendaftaran &amp; nomor antrean</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="soap rme rekam medis elektronik dokter perawat diagnosa icd anamnesa">
                                <a href="<?= base_url('klinik/soap') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-teal-soft text-teal mr-3">
                                        <i class="fas fa-notes-medical"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">RME SOAP Pasien</div>
                                        <div class="text-xs text-muted text-truncate">Pemeriksaan dokter &amp; rekam medis</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessFinance): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="kasir bayar billing pembayaran kwitansi invoice pos piutang">
                                <a href="<?= base_url('keuangan/kasir') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-success-soft text-success mr-3">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Kasir &amp; Billing Pasien</div>
                                        <div class="text-xs text-muted text-truncate">Pelunasan tindakan, obat &amp; cetak nota</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessPharmacy): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="resep apotek farmasi obat racikan dispensing telaah e-resep">
                                <a href="<?= base_url('apotek/resep') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-amber-soft text-warning mr-3">
                                        <i class="fas fa-file-prescription"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Pelayanan E-Resep</div>
                                        <div class="text-xs text-muted text-truncate">Dispensing resep dokter klinik</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="kasir apotek otc penjualan langsung bebas obat bebas suplemen">
                                <a href="<?= base_url('apotek/penjualan') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-orange-soft text-orange mr-3">
                                        <i class="fas fa-cart-shopping"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Kasir Apotek (OTC)</div>
                                        <div class="text-xs text-muted text-truncate">Penjualan obat bebas &amp; alkes</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessResto): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="resto kantin makanan minuman pos kasir resto pesanan menu">
                                <a href="<?= base_url('resto/pos') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-danger-soft text-danger mr-3">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Kasir Resto &amp; Gizi</div>
                                        <div class="text-xs text-muted text-truncate">Pemesanan F&amp;B pasien &amp; pengunjung</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessAccounting): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="akuntansi jurnal memorial buku besar coa neraca laba rugi">
                                <a href="<?= base_url('accounting/jurnal') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-purple-soft text-purple mr-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Entri Jurnal Akuntansi</div>
                                        <div class="text-xs text-muted text-truncate">Jurnal penyesuaian &amp; buku besar</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if ($canAccessProcurement): ?>
                            <div class="col-sm-6 mb-2 control-item-col" data-keywords="procurement pengadaan purchase order po surat pesanan supplier vendor">
                                <a href="<?= base_url('procurement/purchase-orders') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-indigo-soft text-indigo mr-3">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm text-truncate">Surat Pesanan (PO)</div>
                                        <div class="text-xs text-muted text-truncate">Pengadaan obat &amp; logistik medis</div>
                                    </div>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Column 2: Layar Antrean Publik, Display TV, Kiosk, & Audio -->
                    <div class="col-lg-5 col-md-12 mb-4 control-group-section" data-section="layar-publik">
                        <!-- Layar Publik & Display TV -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-1 border-bottom">
                                <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                    <i class="fas fa-tv text-teal mr-1.5"></i> Layar Antrean Publik &amp; Kiosk
                                </h6>
                                <span class="badge badge-light border text-muted text-xs">Full Screen Display</span>
                            </div>
                            <div class="row" style="gap: 10px 0;">
                                <div class="col-12 mb-2 control-item-col" data-keywords="display antrean tv ruang tunggu monitor televisi poli klinik caller">
                                    <a href="<?= base_url('display') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                        <div class="control-card-icon bg-teal-soft text-teal mr-3">
                                            <i class="fas fa-tv"></i>
                                        </div>
                                        <div class="text-truncate" style="flex: 1;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="font-weight-bold text-sm">Display TV Antrean Poli</span>
                                                <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 10px;"></i>
                                            </div>
                                            <div class="text-xs text-muted text-truncate">Layar monitor ruang tunggu utama</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-12 mb-2 control-item-col" data-keywords="kiosk apm anjungan pendaftaran mandiri pasien touchscreen cetak nomor antrean">
                                    <a href="<?= base_url('kiosk') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                        <div class="control-card-icon bg-primary-soft text-primary mr-3">
                                            <i class="fas fa-tablet-screen-button"></i>
                                        </div>
                                        <div class="text-truncate" style="flex: 1;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="font-weight-bold text-sm">Kiosk APM Mandiri Pasien</span>
                                                <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 10px;"></i>
                                            </div>
                                            <div class="text-xs text-muted text-truncate">Anjungan mandiri cetak nomor antrean</div>
                                        </div>
                                    </a>
                                </div>
                                <?php if ($canAccessResto): ?>
                                <div class="col-12 mb-2 control-item-col" data-keywords="display resto tv dapur kds pesanan resto kantin gizi">
                                    <a href="<?= base_url('resto/display') ?>" target="_blank" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                        <div class="control-card-icon bg-warning-soft text-warning mr-3">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <div class="text-truncate" style="flex: 1;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="font-weight-bold text-sm">Display Antrean Resto (TV)</span>
                                                <i class="fas fa-arrow-up-right-from-square text-muted" style="font-size: 10px;"></i>
                                            </div>
                                            <div class="text-xs text-muted text-truncate">Monitor status order resto siap ambil</div>
                                        </div>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Kontrol Suara & Audio Alert -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-1 border-bottom">
                                <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                    <i class="fas fa-volume-high text-primary mr-1.5"></i> Suara &amp; Pengingat
                                </h6>
                                <span class="badge badge-light border text-muted text-xs">Audio Browser</span>
                            </div>
                            <div class="p-3 bg-white border rounded-lg d-flex align-items-center justify-content-between control-item-col" data-keywords="suara sound audio mute senyap hening speaker pengingat bell notifikasi">
                                <div class="d-flex align-items-center">
                                    <div class="control-card-icon bg-light text-secondary mr-3">
                                        <i class="fas fa-volume-high" id="icon-sound-toggle"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-sm text-dark">Saklar Suara Notifikasi</div>
                                        <div class="text-xs text-muted">Bunyi lonceng antrean &amp; notifikasi sistem</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-teal font-weight-bold" id="btn-toggle-sound" style="border-radius: 20px; font-size: 12px; padding: 4px 14px;">
                                    Beralih Mode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Row 2: Personalisasi Tema & Bantuan Sistem -->
                <div class="row">
                    <!-- Personalisasi Tema Tampilan -->
                    <div class="col-lg-7 col-md-12 mb-3 control-group-section" data-section="tema">
                        <div class="d-flex align-items-center justify-content-between mb-2.5 pb-1 border-bottom">
                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                <i class="fas fa-palette text-info mr-1.5"></i> Personalisasi Tema Panel
                            </h6>
                            <span class="badge badge-light border text-muted text-xs">5 Mode Tampilan</span>
                        </div>
                        <div class="row" style="gap: 10px 0;">
                            <div class="col-6 col-md-4 mb-2 control-item-col" data-keywords="tema macos big sur apple glass modern">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center p-2" data-theme="theme-macos">
                                    <div class="theme-color-dot" style="background: linear-gradient(135deg, #007aff, #5856d6);"></div>
                                    <div class="text-truncate ml-2" style="flex: 1;">
                                        <div class="font-weight-bold text-xs text-truncate">macOS 11 Big Sur</div>
                                        <span class="badge badge-primary badge-pill text-xs theme-check d-none" style="font-size: 9px; padding: 2px 6px;">Aktif</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 mb-2 control-item-col" data-keywords="tema putih diatas kertas excel grid data">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center p-2" data-theme="theme-excel-paper">
                                    <div class="theme-color-dot" style="background: linear-gradient(135deg, #107c41, #21a366);"></div>
                                    <div class="text-truncate ml-2" style="flex: 1;">
                                        <div class="font-weight-bold text-xs text-truncate">Putih di Atas Kertas</div>
                                        <span class="badge badge-success badge-pill text-xs theme-check d-none" style="font-size: 9px; padding: 2px 6px;">Aktif</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 mb-2 control-item-col" data-keywords="tema paper white slate clean soft">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center p-2" data-theme="theme-paper-white">
                                    <div class="theme-color-dot" style="background: linear-gradient(135deg, #64748b, #94a3b8);"></div>
                                    <div class="text-truncate ml-2" style="flex: 1;">
                                        <div class="font-weight-bold text-xs text-truncate">Paper White</div>
                                        <span class="badge badge-secondary badge-pill text-xs theme-check d-none" style="font-size: 9px; padding: 2px 6px;">Aktif</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 mb-2 control-item-col" data-keywords="tema modern emerald toska hijau elegan">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center p-2" data-theme="theme-modern-emerald">
                                    <div class="theme-color-dot" style="background: linear-gradient(135deg, #0d9488, #14b8a6);"></div>
                                    <div class="text-truncate ml-2" style="flex: 1;">
                                        <div class="font-weight-bold text-xs text-truncate">Modern Emerald</div>
                                        <span class="badge badge-info badge-pill text-xs theme-check d-none" style="font-size: 9px; padding: 2px 6px;">Aktif</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-4 mb-2 control-item-col" data-keywords="tema windows xp retro luna blue classic">
                                <a href="javascript:void(0)" class="control-theme-card theme-select-opt d-flex align-items-center p-2" data-theme="theme-windows-xp">
                                    <div class="theme-color-dot" style="background: linear-gradient(135deg, #0055ea, #3a93f7);"></div>
                                    <div class="text-truncate ml-2" style="flex: 1;">
                                        <div class="font-weight-bold text-xs text-truncate">Windows XP (Retro)</div>
                                        <span class="badge badge-primary badge-pill text-xs theme-check d-none" style="font-size: 9px; padding: 2px 6px;">Aktif</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bantuan & Pemeliharaan Sistem -->
                    <div class="col-lg-5 col-md-12 mb-3 control-group-section" data-section="pemeliharaan">
                        <div class="d-flex align-items-center justify-content-between mb-2.5 pb-1 border-bottom">
                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                <i class="fas fa-wrench text-secondary mr-1.5"></i> Bantuan &amp; Pemeliharaan
                            </h6>
                            <span class="badge badge-light border text-muted text-xs">Diagnostik</span>
                        </div>
                        <div class="d-flex flex-column" style="gap: 8px;">
                            <div class="control-item-col" data-keywords="perbarui sistem refresh cache opcache update sync sinkronisasi versi terbaru">
                                <a href="javascript:void(0)" onclick="window.forceUpdateSystem();" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none" style="border-color: rgba(13, 148, 136, 0.4); background: rgba(13, 148, 136, 0.04);">
                                    <div class="control-card-icon bg-teal text-white mr-3">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </div>
                                    <div class="text-truncate" style="flex: 1;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="font-weight-bold text-sm text-teal">Paksa Perbarui Sistem</span>
                                            <span class="badge badge-teal text-white text-xs" style="font-size: 10px;">Bersihkan Cache</span>
                                        </div>
                                        <div class="text-xs text-muted text-truncate">Muat ulang seluruh kode &amp; aset terbaru</div>
                                    </div>
                                </a>
                            </div>
                            <div class="control-item-col" data-keywords="bantuan panduan petunjuk alur user guide dokumentasi notifikasi">
                                <a href="<?= base_url('system/notifications') ?>" class="control-menu-card d-flex align-items-center p-2.5 text-dark text-decoration-none">
                                    <div class="control-card-icon bg-light text-secondary mr-3">
                                        <i class="fas fa-circle-question"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="font-weight-bold text-sm">Pusat Notifikasi &amp; Panduan</div>
                                        <div class="text-xs text-muted text-truncate">Riwayat pemberitahuan &amp; tips alur kerja</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State jika filter tidak menemukan item -->
                <div id="control-center-empty" class="text-center py-4 d-none">
                    <i class="fas fa-circle-question text-muted mb-2" style="font-size: 32px; opacity: 0.4;"></i>
                    <p class="font-weight-bold text-dark mb-1">Menu Tidak Ditemukan</p>
                    <p class="text-xs text-muted mb-0">Tidak ada menu atau pengaturan yang cocok dengan kata kunci pencarian.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white px-4 py-2.5 border-top d-flex align-items-center justify-content-between">
                <div class="text-xs text-muted">
                    <i class="fas fa-keyboard mr-1"></i> Tekan <kbd class="px-1.5 py-0.5 bg-light border rounded">Ctrl + K</kbd> untuk Spotlight Pasien &amp; Modul
                </div>
                <button type="button" class="btn btn-sm btn-secondary font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modern Spotlight Search Modal (Universal Patient & Feature Lookup) -->
<div class="modal fade macos-spotlight-modal" id="modal-spotlight" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0">
            <div class="spotlight-search-header">
                <div class="spotlight-search-sparkle mr-1" style="width: 28px; height: 28px; font-size: 13px;">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <input type="text" id="spotlight-search-input" class="spotlight-search-input" placeholder="Cari pasien (No. RM, NIK, Nama) atau menu..." autocomplete="off">
                <span class="spinner-border spinner-border-sm text-primary ml-2 d-none" id="spotlight-spinner" role="status"></span>
                <button type="button" class="spotlight-esc-tag ml-2" data-dismiss="modal" title="Tutup Spotlight (Esc)">ESC</button>
            </div>
            <div id="spotlight-results-container" class="spotlight-results-scroll">
                <!-- Initial State: Suggested Quick Commands -->
                <div id="spotlight-initial-state">
                    <div class="spotlight-section-header">
                        <span><i class="fas fa-bolt mr-1" style="color: #007aff;"></i> Saran Pintasan Utama</span>
                        <span style="font-weight: 500; font-size: 10px; opacity: 0.8;">Pilih atau Ketik untuk Cari</span>
                    </div>

                    <a href="<?= base_url('klinik/pendaftaran') ?>" class="spotlight-result-item is-selected">
                        <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                            <div class="spotlight-app-icon icon-patient"><i class="fas fa-user-plus"></i></div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="font-weight-bold text-dark" style="font-size: 13.5px;">Registrasi Pasien Baru</div>
                                <div class="text-xs text-muted">Pendaftaran pasien baru, cari berkas RM &amp; antrean faskes</div>
                            </div>
                        </div>
                        <kbd class="spotlight-quick-kbd">↵</kbd>
                    </a>

                    <a href="<?= base_url('klinik/soap') ?>" class="spotlight-result-item">
                        <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                            <div class="spotlight-app-icon icon-menu"><i class="fas fa-notes-medical"></i></div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="font-weight-bold text-dark" style="font-size: 13.5px;">Pemeriksaan RME SOAP</div>
                                <div class="text-xs text-muted">Rekam medis elektronik terintegrasi, diagnosis &amp; tindakan dokter</div>
                            </div>
                        </div>
                        <kbd class="spotlight-quick-kbd">↵</kbd>
                    </a>

                    <a href="<?= base_url('keuangan/kasir') ?>" class="spotlight-result-item">
                        <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                            <div class="spotlight-app-icon icon-green"><i class="fas fa-cash-register"></i></div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="font-weight-bold text-dark" style="font-size: 13.5px;">Kasir &amp; Billing Pasien</div>
                                <div class="text-xs text-muted">Pelunasan pembayaran tindakan medis, obat &amp; kwitansi</div>
                            </div>
                        </div>
                        <kbd class="spotlight-quick-kbd">↵</kbd>
                    </a>

                    <a href="<?= base_url('apotek/resep') ?>" class="spotlight-result-item">
                        <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                            <div class="spotlight-app-icon icon-amber"><i class="fas fa-file-prescription"></i></div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="font-weight-bold text-dark" style="font-size: 13.5px;">Pelayanan E-Resep Obat</div>
                                <div class="text-xs text-muted">Antrean resep farmasi, racikan &amp; dispensing obat pasien</div>
                            </div>
                        </div>
                        <kbd class="spotlight-quick-kbd">↵</kbd>
                    </a>

                    <a href="<?= base_url('accounting/jurnal') ?>" class="spotlight-result-item">
                        <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                            <div class="spotlight-app-icon icon-purple"><i class="fas fa-book"></i></div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="font-weight-bold text-dark" style="font-size: 13.5px;">Jurnal Akuntansi &amp; Buku Besar</div>
                                <div class="text-xs text-muted">Entri jurnal memorial penyesuaian &amp; laporan keuangan klinik</div>
                            </div>
                        </div>
                        <kbd class="spotlight-quick-kbd">↵</kbd>
                    </a>
                </div>
                <div id="spotlight-dynamic-results" class="d-none"></div>
            </div>
            <div class="spotlight-footer">
                <div class="spotlight-footer-keys">
                    <span><kbd>↵</kbd> Buka</span>
                    <span><kbd>↓</kbd><kbd>↑</kbd> Navigasi</span>
                    <span><kbd>esc</kbd> Tutup</span>
                </div>
                <span class="font-weight-bold" style="color: #007aff; font-size: 11.5px;"><i class="fas fa-bolt mr-1"></i> Sawamawa Spotlight</span>
            </div>
        </div>
    </div>
</div>

<script>
// =========================================================================
// UNIVERSAL HEADER CONTROLLER & MACOS SPOTLIGHT SEARCH ENGINE
// =========================================================================
(function() {
    'use strict';

    // ---------------------------------------------------------------------
    // 1. NAVBAR DROPDOWNS & ACTION CONTROLLERS (+ Aksi Cepat, Profil, etc.)
    // ---------------------------------------------------------------------
    // Memastikan toggle dropdown header bekerja 100% mulus & tidak terbentur Popper.js
    $(document).on('click', '.main-header [data-toggle="dropdown"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $dropdown = $(this).closest('.dropdown');
        var isShown = $dropdown.hasClass('show');
        
        // Tutup semua dropdown header lain terlebih dahulu
        $('.main-header .dropdown.show').not($dropdown).removeClass('show').find('.dropdown-menu').removeClass('show');
        $('.main-header [data-toggle="dropdown"]').not(this).attr('aria-expanded', 'false');
        
        if (isShown) {
            $dropdown.removeClass('show').find('.dropdown-menu').removeClass('show');
            $(this).attr('aria-expanded', 'false');
        } else {
            $dropdown.addClass('show').find('.dropdown-menu').addClass('show');
            $(this).attr('aria-expanded', 'true');
        }
    });

    // Tutup dropdown jika klik di luar area navbar
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-header .dropdown').length) {
            $('.main-header .dropdown.show').removeClass('show').find('.dropdown-menu').removeClass('show');
            $('.main-header [data-toggle="dropdown"]').attr('aria-expanded', 'false');
        }
    });

    // ---------------------------------------------------------------------
    // 2. AUDIO ALERT & MUTE TOGGLE CONTROLLER (#btn-toggle-sound)
    // ---------------------------------------------------------------------
    function syncSoundToggleUI(isMuted) {
        var $icon = $('#icon-sound-toggle');
        var $btn = $('#btn-toggle-sound');
        if (isMuted) {
            $icon.removeClass('fa-volume-high text-teal').addClass('fa-volume-xmark text-secondary');
            $btn.attr('title', 'Pengingat Suara: NONAKTIF (Klik untuk Mengaktifkan)');
            window.soundMuted = true;
        } else {
            $icon.removeClass('fa-volume-xmark text-secondary').addClass('fa-volume-high text-teal');
            $btn.attr('title', 'Pengingat Suara: AKTIF (Klik untuk Mode Hening)');
            window.soundMuted = false;
        }
    }

    // Inisialisasi status audio saat halaman dibuka
    var initialMute = localStorage.getItem('sawamawa_sound_muted') === '1';
    syncSoundToggleUI(initialMute);

    $(document).on('click', '#btn-toggle-sound', function(e) {
        e.preventDefault();
        var currentMute = localStorage.getItem('sawamawa_sound_muted') === '1';
        var newMute = !currentMute;
        localStorage.setItem('sawamawa_sound_muted', newMute ? '1' : '0');
        syncSoundToggleUI(newMute);

        if (typeof toastr !== 'undefined') {
            if (newMute) {
                toastr.warning('Pengingat suara dinonaktifkan (Mode Hening).', '🔇 Suara Senyap');
            } else {
                toastr.success('Pengingat suara & antrean kembali aktif.', '🔊 Suara Aktif');
            }
        }
    });

    // ---------------------------------------------------------------------
    // 3. MACOS SPOTLIGHT UNIVERSAL SEARCH ENGINE
    // ---------------------------------------------------------------------
    var spotlightDebounce = null;
    var $spotlightModal = $('#modal-spotlight');
    var $spotlightInput = $('#spotlight-search-input');
    var $spotlightSpinner = $('#spotlight-spinner');
    var $spotlightInitial = $('#spotlight-initial-state');
    var $spotlightResults = $('#spotlight-dynamic-results');

    function openSpotlight() {
        $spotlightModal.modal('show');
        $spotlightInitial.find('.spotlight-result-item').removeClass('is-selected').first().addClass('is-selected');
        setTimeout(function() {
            $spotlightInput.focus().select();
        }, 150);
    }

    function closeSpotlight() {
        $spotlightModal.modal('hide');
        $spotlightInput.val('');
        $spotlightResults.addClass('d-none').html('');
        $spotlightInitial.removeClass('d-none');
        $spotlightInitial.find('.spotlight-result-item').removeClass('is-selected').first().addClass('is-selected');
    }

    // Trigger dari tombol Desktop & Mobile
    $(document).on('click', '#btn-trigger-spotlight, #btn-trigger-spotlight-mobile', function(e) {
        e.preventDefault();
        openSpotlight();
    });

    // Pintasan Keyboard Global: Ctrl + K atau Cmd + K
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            if ($spotlightModal.hasClass('show')) {
                closeSpotlight();
            } else {
                openSpotlight();
            }
        }
    });

    // Navigasi Keyboard pada Modal Spotlight (Arrow Down / Up / Enter / Escape)
    $(document).on('keydown', '#spotlight-search-input', function(e) {
        var $activeContainer = $spotlightResults.hasClass('d-none') ? $spotlightInitial : $spotlightResults;
        var $items = $activeContainer.find('.spotlight-result-item');
        if (!$items.length) return;

        var $active = $items.filter('.is-selected');
        var currentIndex = $items.index($active);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            var nextIndex = (currentIndex + 1) >= $items.length ? 0 : (currentIndex + 1);
            $items.removeClass('is-selected');
            var $nextItem = $items.eq(nextIndex).addClass('is-selected');
            $nextItem[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            var prevIndex = (currentIndex - 1) < 0 ? ($items.length - 1) : (currentIndex - 1);
            $items.removeClass('is-selected');
            var $prevItem = $items.eq(prevIndex).addClass('is-selected');
            $prevItem[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            var $target = $active.length ? $active : $items.first();
            if ($target.length) {
                var $targetBtn = $target.find('a.btn-action-pill').first();
                if ($targetBtn.length) {
                    window.location.href = $targetBtn.attr('href');
                } else if ($target.is('a')) {
                    window.location.href = $target.attr('href');
                } else if ($target.find('a').length) {
                    window.location.href = $target.find('a').first().attr('href');
                }
                closeSpotlight();
            }
        } else if (e.key === 'Escape') {
            closeSpotlight();
        }
    });

    // Realtime Input Search dengan Debounce & AJAX GET (Anti CSRF-Expire)
    $(document).on('input', '#spotlight-search-input', function() {
        var query = $(this).val().trim();
        clearTimeout(spotlightDebounce);

        if (query.length < 2) {
            $spotlightInitial.removeClass('d-none');
            $spotlightResults.addClass('d-none').html('');
            $spotlightSpinner.addClass('d-none');
            return;
        }

        $spotlightSpinner.removeClass('d-none');

        spotlightDebounce = setTimeout(function() {
            $.ajax({
                url: '<?= base_url("api/spotlight-search") ?>',
                type: 'GET',
                data: { keyword: query },
                dataType: 'json',
                success: function(res) {
                    $spotlightSpinner.addClass('d-none');
                    if (res && res.status === 'success') {
                        renderSpotlightResults(res, query);
                    } else {
                        renderEmptySpotlight(query);
                    }
                },
                error: function() {
                    $spotlightSpinner.addClass('d-none');
                    renderErrorSpotlight();
                }
            });
        }, 180);
    });

    function renderEmptySpotlight(query) {
        $spotlightResults.removeClass('d-none').html(`
            <div class="p-4 text-center text-muted">
                <i class="fas fa-circle-question text-muted mb-2" style="font-size: 28px; opacity: 0.4;"></i>
                <p class="mb-1 font-weight-bold" style="font-size: 13.5px; color: #334155;">Tidak Ada Hasil</p>
                <p class="text-xs mb-0">Tidak ditemukan pasien atau menu dengan kata kunci "<strong>${$('<div>').text(query).html()}</strong>".</p>
            </div>
        `);
        $spotlightInitial.addClass('d-none');
    }

    function renderErrorSpotlight() {
        $spotlightResults.removeClass('d-none').html(`
            <div class="p-3 text-center text-danger">
                <i class="fas fa-triangle-exclamation mb-1" style="font-size: 22px;"></i>
                <p class="mb-0 text-xs font-weight-bold">Gagal memuat hasil pencarian. Silakan periksa koneksi jaringan Anda.</p>
            </div>
        `);
        $spotlightInitial.addClass('d-none');
    }

    function renderSpotlightResults(data, query) {
        var patients = data.patients || [];
        var menus = data.menus || [];

        if (patients.length === 0 && menus.length === 0) {
            renderEmptySpotlight(query);
            return;
        }

        var html = '';

        // Render Data Pasien Terkait
        if (patients.length > 0) {
            html += '<div class="spotlight-section-header">';
            html += '  <span><i class="fas fa-hospital-user mr-1" style="color: #007aff;"></i> Pasien Terdaftar (' + patients.length + ')</span>';
            html += '  <span style="font-weight: 500; font-size: 10px; opacity: 0.8;">Tekan Enter untuk Rekam Medis</span>';
            html += '</div>';

            patients.forEach(function(p, idx) {
                var isFirst = idx === 0 ? ' is-selected' : '';
                html += '<div class="spotlight-result-item' + isFirst + '" data-patient-url="' + p.soap_url + '" tabindex="0">';
                html += '  <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">';
                html += '    <div class="spotlight-app-icon icon-patient"><i class="fas fa-user-injured"></i></div>';
                html += '    <div style="min-width: 0; flex: 1;" class="mr-2">';
                html += '      <div class="d-flex align-items-center mb-0.5 flex-wrap">';
                html += '        <span class="badge badge-primary font-weight-bold mr-2" style="font-size: 10.5px; border-radius: 12px; padding: 2px 7px;">' + p.no_rm + '</span>';
                html += '        <strong class="text-dark text-truncate mr-2" style="font-size: 14px;">' + p.name + '</strong>';
                html += '        <span class="badge badge-light border text-muted" style="font-size: 10px; border-radius: 10px;">' + (p.gender === 'L' ? 'Laki-laki' : 'Perempuan') + ', ' + p.age + ' thn</span>';
                html += '      </div>';
                html += '      <div class="text-xs text-muted text-truncate">';
                html += '        <span>NIK: ' + (p.nik || '-') + '</span> &bull; ';
                html += '        <span>Telp: ' + (p.phone || '-') + '</span> &bull; ';
                html += '        <span>' + (p.address || '-') + '</span>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <div class="d-flex align-items-center" style="gap: 5px; flex-shrink: 0;">';
                html += '    <a href="' + p.soap_url + '" class="btn btn-action-pill btn-outline-teal" title="Buka Rekam Medis Elektronik SOAP"><i class="fas fa-notes-medical mr-1"></i> SOAP</a>';
                html += '    <a href="' + p.resep_url + '" class="btn btn-action-pill btn-outline-warning" title="Buat Resep Farmasi"><i class="fas fa-file-prescription mr-1"></i> Resep</a>';
                html += '    <a href="' + p.kasir_url + '" class="btn btn-action-pill btn-outline-success" title="Pembayaran Billing Kasir"><i class="fas fa-cash-register mr-1"></i> Kasir</a>';
                html += '  </div>';
                html += '</div>';
            });
        }

        // Render Menu & Navigasi Cepat
        if (menus.length > 0) {
            html += '<div class="spotlight-section-header' + (patients.length > 0 ? ' mt-2' : '') + '">';
            html += '  <span><i class="fas fa-compass mr-1" style="color: #30b0c7;"></i> Menu & Tindakan Langsung (' + menus.length + ')</span>';
            html += '  <span style="font-weight: 500; font-size: 10px; opacity: 0.8;">Navigasi Cepat</span>';
            html += '</div>';

            menus.forEach(function(m, idx) {
                var isSelected = (patients.length === 0 && idx === 0) ? ' is-selected' : '';
                html += '<a href="' + m.url + '" class="spotlight-result-item' + isSelected + '">';
                html += '  <div class="d-flex align-items-center">';
                html += '    <div class="spotlight-app-icon icon-menu"><i class="fas ' + m.icon + '"></i></div>';
                html += '    <div>';
                html += '      <div class="font-weight-bold text-dark" style="font-size: 13.5px;">' + m.title + '</div>';
                html += '      <div class="text-xs text-muted">' + m.module + '</div>';
                html += '    </div>';
                html += '  </div>';
                html += '  <i class="fas fa-chevron-right text-muted" style="font-size: 11px; opacity: 0.7;"></i>';
                html += '</a>';
            });
        }

        $spotlightResults.removeClass('d-none').html(html);
        $spotlightInitial.addClass('d-none');
    }

    // Hover state sinkronisasi dengan item terpilih keyboard
    $(document).on('mouseenter', '.spotlight-result-item', function() {
        $('.spotlight-result-item').removeClass('is-selected');
        $(this).addClass('is-selected');
    });

    // Navigasi saat baris pasien diklik langsung
    $(document).on('click', '.spotlight-result-item[data-patient-url]', function(e) {
        if ($(e.target).closest('a').length) return; // biarkan link aksi spesifik (SOAP/Resep/Kasir) bekerja normal
        window.location.href = $(this).data('patient-url');
        closeSpotlight();
    });

    // Tutup modal secara otomatis ketika pengguna mengklik link di dalam spotlight
    $(document).on('click', '.spotlight-result-item a, a.spotlight-result-item', function() {
        closeSpotlight();
    });

    // ---------------------------------------------------------------------
    // 4. MODAL PUSAT KONTROL & MENU LENGKAP FILTER SEARCH ENGINE
    // ---------------------------------------------------------------------
    $(document).on('input', '#control-center-filter-input', function() {
        var query = $(this).val().toLowerCase().trim();
        var visibleCount = 0;

        $('.control-item-col').each(function() {
            var text = $(this).text().toLowerCase();
            var keywords = ($(this).data('keywords') || '').toLowerCase();
            
            if (!query || text.indexOf(query) !== -1 || keywords.indexOf(query) !== -1) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        // Tampilkan/sembunyikan section berdasarkan ketersediaan item
        $('.control-group-section').each(function() {
            var hasVisible = $(this).find('.control-item-col:not(.d-none)').length > 0;
            if (hasVisible) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });

        if (visibleCount === 0 && query.length > 0) {
            $('#control-center-empty').removeClass('d-none');
        } else {
            $('#control-center-empty').addClass('d-none');
        }
    });

    // Auto focus filter input saat modal control center dibuka
    $('#modal-control-center').on('shown.bs.modal', function() {
        $('#control-center-filter-input').focus();
    });

    // Reset filter saat modal control center ditutup
    $('#modal-control-center').on('hidden.bs.modal', function() {
        $('#control-center-filter-input').val('');
        $('.control-item-col, .control-group-section').removeClass('d-none');
        $('#control-center-empty').addClass('d-none');
    });

    // Tutup modal control center jika item menu biasa diklik
    $(document).on('click', '#modal-control-center a.control-menu-card:not([target="_blank"])', function() {
        $('#modal-control-center').modal('hide');
    });

    // ---------------------------------------------------------------------
    // 5. AUTO-POPUP APA YANG BARU (ONCE PER NEW RELEASE)
    // ---------------------------------------------------------------------
    (function() {
        var currentVersion = '<?= clinic_latest_version() ?>';
        if (!currentVersion) return;
        var lastSeenVersion = localStorage.getItem('sawamawa_last_seen_version');
        
        // Jangan auto-popup jika pengguna berada di halaman /system/whats-new itu sendiri
        var currentPath = window.location.pathname;
        if (currentPath.indexOf('/system/whats-new') === -1) {
            if (lastSeenVersion !== currentVersion) {
                setTimeout(function() {
                    $('#modal-whats-new-popup').modal('show');
                }, 900);
            }
        }
        
        $('#modal-whats-new-popup').on('hidden.bs.modal', function() {
            localStorage.setItem('sawamawa_last_seen_version', currentVersion);
        });
    })();
})();
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
