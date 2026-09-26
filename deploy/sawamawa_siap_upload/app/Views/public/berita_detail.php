<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Artikel Kesehatan - Sawamawa Medical Center') ?></title>
    
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
            --text-main: #334155;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
            --radius-lg: 12px;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            background-color: var(--bg-light);
            line-height: 1.8;
        }

        .navbar-main {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .article-content {
            font-size: 16px;
            color: #334155;
        }
        .article-content h1, .article-content h2, .article-content h3, .article-content h4, .article-content h5 {
            color: var(--dark-slate);
            font-weight: 700;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        .article-content p {
            margin-bottom: 16px;
        }
        .article-content ul, .article-content ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .recent-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 14px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: block;
        }
        .recent-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
            text-decoration: none;
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
            <a href="<?= base_url('berita') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Semua Artikel
            </a>
            <a href="<?= base_url('daftar-online') ?>" class="btn btn-teal btn-sm font-weight-bold text-white" style="background:#0d9488;">
                <i class="fas fa-ticket-alt mr-1"></i> Daftar Antrean Online
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row">
        <!-- Main Article Container -->
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 border rounded shadow-sm" style="border-radius: 14px !important;">
                
                <!-- Category & Meta -->
                <div class="mb-3">
                    <span class="badge badge-info px-3 py-1 font-weight-bold" style="font-size: 12px;"><?= esc($article->category) ?></span>
                </div>

                <h1 class="font-weight-bold text-dark mb-3" style="font-size: 28px; line-height: 1.3;">
                    <?= esc($article->title) ?>
                </h1>

                <div class="d-flex align-items-center text-xs text-muted mb-4 pb-3 border-bottom flex-wrap" style="gap: 15px;">
                    <span><i class="fas fa-user-doctor text-teal mr-1"></i> <strong class="text-dark"><?= esc($article->author) ?></strong></span>
                    <span><i class="fas fa-calendar-alt mr-1"></i> <?= date('d F Y', strtotime($article->created_at)) ?></span>
                    <span><i class="fas fa-eye mr-1"></i> <?= number_format((int)$article->views, 0, ',', '.') ?> pembaca</span>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($article->image_url) && file_exists(FCPATH . $article->image_url)): ?>
                    <div class="mb-4">
                        <img src="<?= base_url($article->image_url) ?>" alt="<?= esc($article->title) ?>" class="img-fluid rounded" style="width: 100%; max-height: 420px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <!-- Lead Summary -->
                <?php if (!empty($article->summary)): ?>
                    <div class="p-3 mb-4 rounded bg-light border-left" style="border-left: 4px solid #0d9488 !important; font-style: italic; color: #475569;">
                        <?= esc($article->summary) ?>
                    </div>
                <?php endif; ?>

                <!-- Full HTML Content -->
                <div class="article-content">
                    <?= $article->content ?>
                </div>

                <!-- Share Box -->
                <div class="mt-5 pt-4 border-top d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-sm-0">
                        <strong class="text-dark text-xs">Bagikan artikel ini ke kerabat &amp; keluarga:</strong>
                    </div>
                    <div>
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode('*' . $article->title . "*\n\nBaca artikel kesehatan dari Sawamawa Medical Center di: " . current_url()) ?>" target="_blank" class="btn btn-success btn-sm font-weight-bold">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-primary btn-sm font-weight-bold ml-1">
                            <i class="fab fa-facebook mr-1"></i> Facebook
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            
            <!-- Quick Booking Card -->
            <div class="card p-4 border-0 shadow-sm mb-4 text-center" style="border-radius: 12px; background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color: white;">
                <h5 class="font-weight-bold mb-2">Butuh Konsultasi Dokter?</h5>
                <p class="text-xs text-white-50 mb-3">Daftarkan diri Anda secara online untuk mendapatkan nomor antrean dokter tanpa menunggu lama di klinik.</p>
                <a href="<?= base_url('daftar-online') ?>" class="btn btn-light btn-sm font-weight-bold text-teal py-2">
                    <i class="fas fa-ticket-alt mr-1"></i> Reservasi Antrean Online
                </a>
            </div>

            <!-- Recent Articles -->
            <div class="card p-3 border shadow-sm" style="border-radius: 12px;">
                <h6 class="font-weight-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="fas fa-newspaper text-teal mr-1"></i> Artikel Medis Lainnya
                </h6>
                <?php if (!empty($recentArticles)): foreach ($recentArticles as $rec): ?>
                    <a href="<?= base_url('berita/' . $rec->slug) ?>" class="recent-card">
                        <span class="badge badge-light border text-teal text-xs mb-1"><?= esc($rec->category) ?></span>
                        <div class="font-weight-bold text-dark text-xs mb-1" style="line-height: 1.35;"><?= esc($rec->title) ?></div>
                        <small class="text-muted" style="font-size: 11px;"><i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($rec->created_at)) ?></small>
                    </a>
                <?php endforeach; else: ?>
                    <small class="text-muted">Tidak ada artikel lain.</small>
                <?php endif; ?>
            </div>

        </div>
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
