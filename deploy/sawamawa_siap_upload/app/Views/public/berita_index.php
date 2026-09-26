<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pusat Edukasi Medis & Berita Kesehatan - Sawamawa Medical Center') ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --dark-slate: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --radius-lg: 12px;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            background-color: var(--bg-light);
            line-height: 1.6;
        }

        .navbar-main {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .article-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .article-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08);
        }
        .article-thumb {
            height: 180px;
            width: 100%;
            object-fit: cover;
        }
        .article-thumb-fallback {
            height: 180px;
            background: linear-gradient(135deg, #0d9488 0%, #0369a1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 40px;
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
            margin-bottom: 8px;
        }
        .article-title {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            color: var(--dark-slate);
            margin-bottom: 8px;
            text-decoration: none;
        }
        .article-title:hover {
            color: var(--primary);
            text-decoration: none;
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

        .category-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            background: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--card-border);
            transition: all 0.2s ease;
            display: inline-block;
            margin-right: 6px;
            margin-bottom: 8px;
        }
        .category-pill:hover, .category-pill.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-main">
    <div class="container">
        <a class="navbar-brand font-weight-bold d-flex align-items-center" href="<?= base_url() ?>" style="font-size: 18px;">
            <?php if (clinic_logo()): ?>
                <img src="<?= clinic_logo() ?>" alt="Logo" style="height: 36px; margin-right: 8px;">
            <?php else: ?>
                <span class="text-teal mr-2" style="font-size: 22px;"><i class="fas fa-hospital-user"></i></span>
            <?php endif; ?>
            <span><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></span>
        </a>
        <div class="ml-auto">
            <a href="<?= base_url() ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
            <a href="<?= base_url('daftar-online') ?>" class="btn btn-teal btn-sm font-weight-bold text-white" style="background:#0d9488;">
                <i class="fas fa-ticket-alt mr-1"></i> Daftar Antrean Online
            </a>
        </div>
    </div>
</nav>

<!-- Header Banner -->
<div class="py-5 bg-white border-bottom">
    <div class="container text-center">
        <span class="badge badge-subtle-teal font-weight-bold px-3 py-1 mb-2" style="background:#ccfbf1; color:#0f766e; font-size:12px;">
            EDUKASI &amp; INFORMASI MEDIS
        </span>
        <h1 class="font-weight-bold text-dark" style="font-size: 32px;">Pusat Artikel &amp; Berita Kesehatan</h1>
        <p class="text-muted text-sm mx-auto" style="max-width: 600px;">
            Ragam tips hidup sehat, informasi penyakit, dan pengumuman resmi dari tenaga medis profesional Sawamawa Medical Center.
        </p>

        <!-- Search Form -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form action="<?= base_url('berita') ?>" method="get" class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                    <?php if (!empty($currentCat)): ?>
                        <input type="hidden" name="kategori" value="<?= esc($currentCat) ?>">
                    <?php endif; ?>
                    <input type="text" name="q" class="form-control border-0 py-4" placeholder="Cari topik artikel (contoh: diabetes, vitamin, vaksin)..." value="<?= esc($searchQuery ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-teal px-4 text-white font-weight-bold" style="background:#0d9488;" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container py-5">
    <!-- Category Filter Pills -->
    <div class="mb-4 text-center">
        <a href="<?= base_url('berita') ?>" class="category-pill <?= empty($currentCat) ? 'active' : '' ?>">Semua Kategori</a>
        <a href="<?= base_url('berita?kategori=Edukasi+Kesehatan') ?>" class="category-pill <?= $currentCat === 'Edukasi Kesehatan' ? 'active' : '' ?>">Edukasi Kesehatan</a>
        <a href="<?= base_url('berita?kategori=Tips+Medis') ?>" class="category-pill <?= $currentCat === 'Tips Medis' ? 'active' : '' ?>">Tips Medis</a>
        <a href="<?= base_url('berita?kategori=Berita+Klinik') ?>" class="category-pill <?= $currentCat === 'Berita Klinik' ? 'active' : '' ?>">Berita Klinik</a>
        <a href="<?= base_url('berita?kategori=Layanan+%26+Fasilitas') ?>" class="category-pill <?= $currentCat === 'Layanan & Fasilitas' ? 'active' : '' ?>">Layanan &amp; Fasilitas</a>
    </div>

    <!-- Article Grid -->
    <div class="row">
        <?php if (!empty($articles)): foreach ($articles as $art): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="article-card">
                    <a href="<?= base_url('berita/' . $art->slug) ?>">
                        <?php if (!empty($art->image_url) && file_exists(FCPATH . $art->image_url)): ?>
                            <img src="<?= base_url($art->image_url) ?>" alt="<?= esc($art->title) ?>" class="article-thumb">
                        <?php else: ?>
                            <div class="article-thumb-fallback">
                                <i class="fas fa-heart-pulse"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="article-body">
                        <div>
                            <span class="article-cat-badge"><?= esc($art->category) ?></span>
                        </div>
                        <a href="<?= base_url('berita/' . $art->slug) ?>" class="article-title" title="<?= esc($art->title) ?>">
                            <?= esc($art->title) ?>
                        </a>
                        <p class="article-summary">
                            <?= esc($art->summary) ?>
                        </p>
                        <div class="article-meta">
                            <span><i class="fas fa-user-doctor mr-1"></i> <?= esc($art->author) ?></span>
                            <span><i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($art->created_at)) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-file-circle-question fa-3x mb-3 text-muted"></i>
                <h5>Tidak ada artikel yang ditemukan.</h5>
                <p class="text-xs">Coba gunakan kata kunci pencarian lain atau pilih kategori Semua.</p>
                <a href="<?= base_url('berita') ?>" class="btn btn-outline-teal btn-sm font-weight-bold mt-2">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-muted py-4 text-center text-xs" style="background:#0f172a !important;">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center flex-wrap mb-3" style="gap: 10px;">
            <img src="<?= base_url('assets/images/iso-27001.svg') ?>" alt="ISO/IEC 27001" title="ISO/IEC 27001 - Keamanan Informasi" style="height: 32px; width: auto;">
            <img src="<?= base_url('assets/images/iso-27701.svg') ?>" alt="ISO/IEC 27701" title="ISO/IEC 27701 - Privasi Data Pasien" style="height: 32px; width: auto;">
            <img src="<?= base_url('assets/images/iso-9001.svg') ?>" alt="ISO 9001:2015" title="ISO 9001:2015 - Mutu Pelayanan" style="height: 32px; width: auto;">
            <img src="<?= base_url('assets/images/satusehat-logo.svg') ?>" alt="SATUSEHAT Kemenkes RI" title="SATUSEHAT Kemenkes RI" style="height: 32px; width: auto;">
            <img src="<?= base_url('assets/images/ssl-secure.svg') ?>" alt="256-Bit SSL" title="256-Bit SSL Encryption" style="height: 32px; width: auto;">
        </div>
        <div class="text-muted text-xs mb-1" style="font-size: 11px;">
            Sistem Informasi Medis Terenkripsi 256-Bit SSL &bull; Standar Keamanan Informasi &amp; Privasi Data Pasien Internasional
        </div>
        <div style="color: #64748b;">
            &copy; <?= date('Y') ?> <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?>. Seluruh hak cipta dilindungi undang-undang.
        </div>
    </div>
</footer>

</body>
</html>
