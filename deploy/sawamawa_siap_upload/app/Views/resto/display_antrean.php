<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Sawamawa Medical Center</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --teal-accent: #0d9488;
            --emerald-glow: #10b981;
            --amber-glow: #f59e0b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            margin: 0;
        }

        .header-bar {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid rgba(255, 255, 255, 0.08);
            padding: 14px 28px;
        }

        .brand-logo {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .brand-logo span {
            color: var(--teal-accent);
        }

        .clock-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 28px;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: 1px;
        }

        .panel-container {
            flex: 1;
            padding: 24px;
        }

        .queue-card {
            background: var(--card-dark);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .queue-header-cooking {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: white;
            padding: 16px 24px;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .queue-header-ready {
            background: linear-gradient(135deg, #0d9488, #059669);
            color: white;
            padding: 16px 24px;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .queue-body {
            flex: 1;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            align-content: start;
            min-height: 480px;
        }

        .order-token {
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .token-cooking {
            background: rgba(245, 158, 11, 0.12);
            border: 1.5px solid rgba(245, 158, 11, 0.4);
            animation: pulse-border 2s infinite;
        }

        .token-ready {
            background: rgba(16, 185, 129, 0.18);
            border: 2px solid #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
            animation: bounce-ready 1s ease;
        }

        .order-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
        }

        .token-cooking .order-number {
            color: #fcd34d;
        }

        .token-ready .order-number {
            color: #6ee7b7;
        }

        .customer-title {
            font-size: 14px;
            font-weight: 600;
            color: #94a3b8;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .token-ready .customer-title {
            color: #e2e8f0;
        }

        .empty-placeholder {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 380px;
            color: #64748b;
        }

        .empty-placeholder i {
            font-size: 56px;
            margin-bottom: 16px;
            opacity: 0.4;
        }

        .ticker-bar {
            background: #020617;
            padding: 10px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .ticker-label {
            background: var(--teal-accent);
            color: white;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 6px;
            margin-right: 16px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pulsing-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px #10b981;
            animation: blink 1.2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @keyframes pulse-border {
            0% { border-color: rgba(245, 158, 11, 0.3); }
            50% { border-color: rgba(245, 158, 11, 0.9); }
            100% { border-color: rgba(245, 158, 11, 0.3); }
        }

        @keyframes bounce-ready {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Header Bar -->
    <header class="header-bar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="brand-logo mr-4">
                <i class="fas fa-utensils mr-2 text-teal"></i>SAWAMAWA <span>RESTO & NUTRITION</span>
            </div>
            <span class="badge badge-dark border border-secondary px-3 py-2 text-light font-weight-bold" style="font-size: 12px;">
                <span class="pulsing-dot"></span> ANTRIAN PESANAN REALTIME
            </span>
        </div>

        <div class="d-flex align-items-center">
            <div class="text-right mr-4">
                <div class="clock-display" id="clock">00:00:00</div>
                <small class="text-muted font-weight-bold" id="dateText"><?= esc($server_date) ?></small>
            </div>
            <button class="btn btn-outline-light btn-sm font-weight-bold px-3 py-2" id="btnFullscreen" onclick="toggleFullscreen()">
                <i class="fas fa-expand mr-1"></i> Fullscreen TV
            </button>
        </div>
    </header>

    <!-- Main Container -->
    <main class="panel-container">
        <div class="row h-100">
            
            <!-- Left Column: Sedang Disiapkan / Dimasak -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="queue-card">
                    <div class="queue-header-cooking">
                        <div>
                            <i class="fas fa-fire-burner mr-2"></i> SEDANG DISIAPKAN / DIMASAK
                        </div>
                        <span class="badge badge-light px-3 py-1 text-dark font-weight-bold" id="badgeCookingCount">
                            <?= count($preparing) ?> Pesanan
                        </span>
                    </div>
                    <div class="queue-body" id="cookingList">
                        <?php if (!empty($preparing)): ?>
                            <?php foreach ($preparing as $p): ?>
                                <div class="order-token token-cooking">
                                    <div class="order-number"><?= esc($p->order_no) ?></div>
                                    <div class="customer-title">
                                        <i class="fas fa-user mr-1 text-muted"></i> <?= esc($p->customer_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-placeholder">
                                <i class="fas fa-mug-hot"></i>
                                <h5>Semua Pesanan Sedang Berjalan Selesai</h5>
                                <p class="text-muted">Menunggu pesanan baru dari kasir POS...</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Siap Diambil / Selesai -->
            <div class="col-md-6">
                <div class="queue-card">
                    <div class="queue-header-ready">
                        <div>
                            <i class="fas fa-bell-concierge mr-2"></i> SILAKAN DIAMBIL (READY)
                        </div>
                        <span class="badge badge-light px-3 py-1 text-dark font-weight-bold" id="badgeReadyCount">
                            <?= count($ready) ?> Pesanan
                        </span>
                    </div>
                    <div class="queue-body" id="readyList">
                        <?php if (!empty($ready)): ?>
                            <?php foreach ($ready as $r): ?>
                                <div class="order-token token-ready">
                                    <div class="order-number"><?= esc($r->order_no) ?></div>
                                    <div class="customer-title">
                                        <i class="fas fa-circle-check mr-1 text-success"></i> <?= esc($r->customer_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-placeholder">
                                <i class="fas fa-clipboard-check"></i>
                                <h5>Belum Ada Pesanan Siap Ambil</h5>
                                <p class="text-muted">Pesanan yang telah selesai akan muncul di sini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Ticker -->
    <footer class="ticker-bar">
        <div class="ticker-label"><i class="fas fa-heart-pulse mr-1"></i> Edukasi Gizi</div>
        <marquee behavior="scroll" direction="left" scrollamount="6" class="text-light">
            Selamat Datang di <strong>Sawamawa Medical Center Resto & Healthy Nutrition Store</strong>. 
            Semua menu makanan dan minuman disiapkan higienis oleh nutrisionis klinis dengan bahan segar pilihan. 
            Untuk diet khusus diabetes, hipertensi, atau asam urat, silakan konsultasikan dengan dokter spesialis gizi kami.
        </marquee>
    </footer>

    <!-- Audio Chime for Ready Orders -->
    <audio id="audioNotification" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Digital Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    alert(`Gagal mengaktifkan fullscreen: ${err.message}`);
                });
                document.getElementById('btnFullscreen').innerHTML = '<i class="fas fa-compress mr-1"></i> Keluar Fullscreen';
            } else {
                document.exitFullscreen();
                document.getElementById('btnFullscreen').innerHTML = '<i class="fas fa-expand mr-1"></i> Fullscreen TV';
            }
        }

        // Realtime Polling
        let lastReadyIds = [];

        function fetchQueueData() {
            $.ajax({
                url: '<?= base_url('resto/antrean-json') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    // Update Preparing / Cooking List
                    const prepList = $('#cookingList');
                    prepList.empty();
                    $('#badgeCookingCount').text(res.preparing.length + ' Pesanan');

                    if (res.preparing && res.preparing.length > 0) {
                        res.preparing.forEach(function(item) {
                            prepList.append(`
                                <div class="order-token token-cooking">
                                    <div class="order-number">${item.order_no}</div>
                                    <div class="customer-title">
                                        <i class="fas fa-user mr-1 text-muted"></i> ${item.customer_name}
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        prepList.append(`
                            <div class="empty-placeholder">
                                <i class="fas fa-mug-hot"></i>
                                <h5>Semua Pesanan Sedang Berjalan Selesai</h5>
                                <p class="text-muted">Menunggu pesanan baru dari kasir POS...</p>
                            </div>
                        `);
                    }

                    // Update Ready List
                    const readyList = $('#readyList');
                    readyList.empty();
                    $('#badgeReadyCount').text(res.ready.length + ' Pesanan');

                    let currentReadyIds = [];
                    if (res.ready && res.ready.length > 0) {
                        res.ready.forEach(function(item) {
                            currentReadyIds.push(item.id);
                            readyList.append(`
                                <div class="order-token token-ready">
                                    <div class="order-number">${item.order_no}</div>
                                    <div class="customer-title">
                                        <i class="fas fa-circle-check mr-1 text-success"></i> ${item.customer_name}
                                    </div>
                                </div>
                            `);
                        });

                        // Check for new ready item to trigger sound chime
                        const hasNewReady = currentReadyIds.some(id => !lastReadyIds.includes(id));
                        if (hasNewReady && lastReadyIds.length > 0) {
                            try {
                                const sound = document.getElementById('audioNotification');
                                if (sound) sound.play().catch(e => console.log('Audio autoplay blocked'));
                            } catch(e) {}
                        }
                    } else {
                        readyList.append(`
                            <div class="empty-placeholder">
                                <i class="fas fa-clipboard-check"></i>
                                <h5>Belum Ada Pesanan Siap Ambil</h5>
                                <p class="text-muted">Pesanan yang telah selesai akan muncul di sini</p>
                            </div>
                        `);
                    }

                    lastReadyIds = currentReadyIds;
                },
                error: function(err) {
                    console.error('Failed to poll antrean data', err);
                }
            });
        }

        // Poll every 4 seconds
        setInterval(fetchQueueData, 4000);
    </script>
</body>
</html>
