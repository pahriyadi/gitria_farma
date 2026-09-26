<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .error-card { text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 500px; width: 100%; }
        .error-icon { font-size: 72px; color: #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon"><i class="fas fa-ban"></i></div>
        <h1 class="display-4 font-weight-bold text-danger">403</h1>
        <h3>Akses Ditolak</h3>
        <p class="text-secondary mt-3"><?= esc($message ?? 'Anda tidak memiliki hak akses untuk membuka halaman ini.') ?></p>
        <div class="mt-4">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary"><i class="fas fa-home"></i> Kembali ke Dashboard</a>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger ml-2"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </div>
    </div>
</body>
</html>
