<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark */
    :root {
        --kds-canvas-bg: #f4f6f9;
        --kds-card-bg: #ffffff;
        --kds-card-header: #ffffff;
        --kds-border: #b8b8b8;
        --kds-border-item: #e5e7eb;
        --kds-text: #212529;
        --kds-text-muted: #6c757d;
        --kds-box-bg: #f8fafc;
        --kds-hover-bg: #f0f7f3;
    }

    body.dark-mode {
        --kds-canvas-bg: #14171a;
        --kds-card-bg: #1f2328;
        --kds-card-header: #1f2328;
        --kds-border: #374151;
        --kds-border-item: #374151;
        --kds-text: #f0f6fc;
        --kds-text-muted: #9ca3af;
        --kds-box-bg: #181b1f;
        --kds-hover-bg: #262c36;
    }

    /* Container Card Styling */
    .kds-card {
        background-color: var(--kds-card-bg) !important;
        border: 1px solid var(--kds-border) !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .kds-card .card-header {
        background-color: var(--kds-card-header) !important;
        border-bottom: 1px solid var(--kds-border) !important;
        color: var(--kds-text) !important;
    }

    .kds-card .card-body {
        background-color: var(--kds-card-bg) !important;
        color: var(--kds-text) !important;
    }

    .kds-card .card-footer {
        background-color: var(--kds-box-bg) !important;
        border-top: 1px solid var(--kds-border) !important;
    }

    /* KPI Summary Stat Box */
    .kds-stat-box {
        background-color: var(--kds-card-bg);
        border: 1px solid var(--kds-border);
        border-radius: 4px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* List Group Item in Dark Mode */
    .kds-card .list-group-item {
        background-color: transparent !important;
        border-color: var(--kds-border-item) !important;
        color: var(--kds-text) !important;
    }

    /* Filter Tabs */
    #kdsFilterTabs .nav-link {
        color: var(--kds-text-muted);
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 5px 12px;
        font-size: 13px;
        font-weight: 600;
        margin-right: 4px;
        transition: all 0.2s ease;
    }

    #kdsFilterTabs .nav-link:hover {
        background-color: var(--kds-hover-bg);
        color: var(--kds-text);
        border-color: var(--kds-border);
    }

    #kdsFilterTabs .nav-link.active {
        background-color: #0d9488 !important;
        color: #ffffff !important;
        border-color: #0d9488 !important;
    }

    body.dark-mode #kdsFilterTabs .nav-link {
        color: #9ca3af;
    }

    body.dark-mode #kdsFilterTabs .nav-link.active {
        background-color: #0d9488 !important;
        color: #ffffff !important;
    }
</style>

<!-- Top Action Header -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--kds-text);">
            <i class="fas fa-fire-burner text-warning mr-2"></i> Kitchen Display System (KDS) & Dapur Gizi
        </h4>
        <small class="text-muted">Monitoring antrean tiket pesanan dapur, terapi diet gizi pasien, & status saji real-time</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <span class="badge badge-light border p-2 font-weight-bold" id="kds-sync-indicator" style="font-size: 12px;">
                <i class="fas fa-satellite-dish text-teal mr-1"></i> Live KDS <span id="reload-timer" class="text-muted">(30s)</span>
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-none" id="btn-toggle-auto-reload" title="Jeda / Lanjutkan Auto Reload">
                <i class="fas fa-pause mr-1" id="icon-auto-reload"></i> <span id="lbl-auto-reload">Jeda</span>
            </button>
            <a href="<?= base_url('resto/display-antrean') ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold shadow-none" title="Layar Antrean TV">
                <i class="fas fa-tv mr-1 text-teal"></i> Display Antrean TV
            </a>
            <a href="<?= base_url('resto/pos') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-none text-white">
                <i class="fas fa-cash-register mr-1"></i> Kembali ke POS Kasir
            </a>
        </div>
    </div>
</div>

<?php 
// Calculate KDS Queue Workload Metrics
$totalTickets = 0;
$countNew = 0;
$countCooking = 0;
$countReady = 0;

$groupedOrders = [];
if (!empty($kitchenOrders)) {
    foreach ($kitchenOrders as $ko) {
        $groupedOrders[$ko->order_id][] = $ko;
        if ($ko->status === 'new') $countNew++;
        elseif ($ko->status === 'cooking') $countCooking++;
        elseif ($ko->status === 'ready') $countReady++;
    }
    $totalTickets = count($groupedOrders);
}
?>

<!-- KPI Summary Workload Cards -->
<div class="row mb-3">
    <div class="col-6 col-md-3 mb-2 mb-md-0">
        <div class="kds-stat-box">
            <div>
                <small class="text-muted font-weight-bold d-block text-uppercase" style="font-size: 11px;">Total Tiket Aktif</small>
                <h4 class="font-weight-bold mb-0" style="color: var(--kds-text);"><?= $totalTickets ?></h4>
            </div>
            <span class="text-secondary opacity-50" style="font-size: 26px;"><i class="fas fa-receipt"></i></span>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-2 mb-md-0">
        <div class="kds-stat-box" style="border-left: 4px solid #ef4444 !important;">
            <div>
                <small class="text-danger font-weight-bold d-block text-uppercase" style="font-size: 11px;">Menunggu (New)</small>
                <h4 class="font-weight-bold mb-0 text-danger"><?= $countNew ?></h4>
            </div>
            <span class="text-danger opacity-75" style="font-size: 26px;"><i class="fas fa-clock"></i></span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kds-stat-box" style="border-left: 4px solid #f59e0b !important;">
            <div>
                <small class="text-warning font-weight-bold d-block text-uppercase" style="font-size: 11px;">Sedang Dimasak</small>
                <h4 class="font-weight-bold mb-0 text-warning"><?= $countCooking ?></h4>
            </div>
            <span class="text-warning opacity-75" style="font-size: 26px;"><i class="fas fa-fire"></i></span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kds-stat-box" style="border-left: 4px solid #10b981 !important;">
            <div>
                <small class="text-success font-weight-bold d-block text-uppercase" style="font-size: 11px;">Siap Saji (Ready)</small>
                <h4 class="font-weight-bold mb-0 text-success"><?= $countReady ?></h4>
            </div>
            <span class="text-success opacity-75" style="font-size: 26px;"><i class="fas fa-bell-concierge"></i></span>
        </div>
    </div>
</div>

<!-- Filter Tabs by Status -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 8px;">
    <ul class="nav nav-pills" id="kdsFilterTabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-status="all">
                <i class="fas fa-th-large mr-1"></i> Semua Tiket (<?= $totalTickets ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="#" data-status="new">
                <i class="fas fa-clock mr-1"></i> Baru Masuk (<?= $countNew ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-warning" href="#" data-status="cooking">
                <i class="fas fa-fire mr-1"></i> Sedang Dimasak (<?= $countCooking ?>)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-success" href="#" data-status="ready">
                <i class="fas fa-check-double mr-1"></i> Siap Saji (<?= $countReady ?>)
            </a>
        </li>
    </ul>

    <div class="input-group" style="max-width: 280px;">
        <div class="input-group-prepend">
            <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
        </div>
        <input type="text" id="kds-search-input" class="form-control form-control-sm border-left-0" placeholder="Cari No. Order / Pasien...">
    </div>
</div>

<!-- Active Orders Grid -->
<div class="row" id="kds-tickets-grid">
    <?php if (empty($groupedOrders)): ?>
        <div class="col-12 text-center py-5">
            <div class="card kds-card p-5">
                <i class="fas fa-utensils fa-4x text-muted mb-3 opacity-50"></i>
                <h5 class="font-weight-bold" style="color: var(--kds-text);">Semua Pesanan Dapur & Terapi Diet Telah Selesai Disajikan</h5>
                <p class="text-muted mb-0">Tidak ada antrean pesanan makanan atau terapi gizi aktif saat ini.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($groupedOrders as $orderId => $kItems): ?>
            <?php 
            $first = $kItems[0];
            $orderTime = strtotime($first->order_time);
            $minsAgo = round((time() - $orderTime) / 60);
            $isDiet = ($first->order_type === 'diet_pasien');

            // Determine aggregate ticket status
            $hasNew = false;
            $hasCooking = false;
            $allReady = true;
            foreach ($kItems as $ki) {
                if ($ki->status === 'new') $hasNew = true;
                if ($ki->status === 'cooking') $hasCooking = true;
                if ($ki->status !== 'ready') $allReady = false;
            }
            $ticketStatus = $hasNew ? 'new' : ($hasCooking ? 'cooking' : ($allReady ? 'ready' : 'other'));
            ?>
            <div class="col-md-6 col-lg-4 mb-4 kds-ticket-wrapper" 
                 data-ticket-id="<?= $orderId ?>"
                 data-status="<?= $ticketStatus ?>"
                 data-order-no="<?= strtolower(esc($first->order_no)) ?>"
                 data-customer="<?= strtolower(esc($first->customer_name ?: '')) ?>">
                
                <div class="card h-100 kds-card" style="border-top: 4px solid <?= $isDiet ? '#0d9488' : '#6b7280' ?> !important;">
                    <!-- Card Header -->
                    <div class="card-header py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="font-weight-bold mb-0" style="color: <?= $isDiet ? '#0d9488' : 'var(--kds-text)' ?>;">
                                <i class="fas fa-receipt mr-1"></i> <?= esc($first->order_no) ?>
                            </h5>
                            <span class="badge badge-<?= $minsAgo > 20 ? 'danger' : ($minsAgo > 10 ? 'warning text-dark' : 'light border') ?> font-weight-bold">
                                <i class="far fa-clock mr-1"></i> <?= $minsAgo ?> mnt lalu
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="font-weight-bold" style="color: var(--kds-text);">
                                <i class="fas fa-user mr-1 text-muted"></i> <?= esc($first->customer_name ?: 'Pelanggan Walk-In') ?>
                                <?php if ($first->polyclinic_name): ?>
                                    <span class="text-muted font-weight-normal">(<?= esc($first->polyclinic_name) ?>)</span>
                                <?php endif; ?>
                            </small>
                            <span class="badge badge-<?= $isDiet ? 'teal text-white' : 'secondary' ?> text-uppercase" style="font-size: 10px;">
                                <?= $isDiet ? 'DIET PASIEN POLI' : 'WALK-IN' ?>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- Prominent Diet / Allergy Banner -->
                        <?php if (!empty($first->diet_instructions)): ?>
                            <div class="alert alert-warning border-warning p-2 mb-3 small font-weight-bold shadow-none" style="border-left: 4px solid #dc2626 !important;">
                                <div class="d-flex align-items-center text-danger mb-1">
                                    <i class="fas fa-triangle-exclamation mr-1"></i> 
                                    <strong>INSTRUKSI GIZI & PANTANGAN MEDIS:</strong>
                                </div>
                                <div class="text-danger pl-3" style="line-height: 1.3;"><?= esc($first->diet_instructions) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Items List -->
                        <ul class="list-group list-group-flush mb-0">
                            <?php foreach ($kItems as $item): ?>
                                <li class="list-group-item px-0 py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge badge-dark font-weight-bold mr-1" style="font-size: 13px;">
                                                <?= $item->qty ?>x
                                            </span>
                                            <strong style="font-size: 14px; color: var(--kds-text);">
                                                <?= esc($item->menu_name) ?>
                                            </strong>
                                            <small class="text-muted d-block text-capitalize">
                                                [<?= esc(str_replace('_', ' ', $item->menu_category)) ?>]
                                            </small>
                                        </div>

                                        <!-- Item Action Status -->
                                        <div>
                                            <?php if ($item->status === 'new'): ?>
                                                <form action="<?= base_url('resto/dapur') ?>" method="post" class="d-inline form-kds-action">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="kitchen_order_id" value="<?= $item->id ?>">
                                                    <input type="hidden" name="status" value="cooking">
                                                    <button type="submit" class="btn btn-warning btn-xs font-weight-bold shadow-none btn-kds-cook" title="Mulai Memasak Item Ini">
                                                        <i class="fas fa-fire mr-1"></i> Mulai Masak
                                                    </button>
                                                </form>
                                            <?php elseif ($item->status === 'cooking'): ?>
                                                <form action="<?= base_url('resto/dapur') ?>" method="post" class="d-inline form-kds-action">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="kitchen_order_id" value="<?= $item->id ?>">
                                                    <input type="hidden" name="status" value="ready">
                                                    <button type="submit" class="btn btn-success btn-xs font-weight-bold shadow-none btn-kds-ready" title="Tandai Sudah Matang / Siap Saji">
                                                        <i class="fas fa-check-double mr-1"></i> Siap Saji
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <div class="d-inline-flex align-items-center">
                                                    <span class="badge badge-success px-2 py-1 mr-1">
                                                        <i class="fas fa-check"></i> Siap
                                                    </span>
                                                    <form action="<?= base_url('resto/dapur') ?>" method="post" class="d-inline form-kds-action">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="kitchen_order_id" value="<?= $item->id ?>">
                                                        <input type="hidden" name="status" value="served">
                                                        <button type="submit" class="btn btn-outline-secondary btn-xs shadow-none btn-kds-served" title="Pesanan Telah Disajikan ke Meja/Pasien">
                                                            <i class="fas fa-check-circle text-success"></i> Saji
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Card Footer: Quick Print / Summary -->
                    <div class="card-footer p-2 text-right">
                        <a href="<?= base_url('resto/cetak-nota/' . $orderId) ?>" target="_blank" class="btn btn-outline-secondary btn-xs font-weight-bold shadow-none">
                            <i class="fas fa-print mr-1"></i> Cetak Tiket Dapur
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    let autoReloadActive = true;
    let secondsLeft = 30;

    // 1. Countdown timer
    const countdownInterval = setInterval(function() {
        if (autoReloadActive) {
            secondsLeft--;
            $('#reload-timer').text('(' + secondsLeft + 's)');
            if (secondsLeft <= 0) {
                location.reload();
            }
        }
    }, 1000);

    // 2. Pause / Resume Toggle
    $('#btn-toggle-auto-reload').on('click', function() {
        autoReloadActive = !autoReloadActive;
        if (autoReloadActive) {
            secondsLeft = 30;
            $('#icon-auto-reload').removeClass('fa-play text-success').addClass('fa-pause');
            $('#lbl-auto-reload').text('Jeda');
            $('#reload-timer').text('(30s)');
        } else {
            $('#icon-auto-reload').removeClass('fa-pause').addClass('fa-play text-success');
            $('#lbl-auto-reload').text('Lanjut');
            $('#reload-timer').text('(Dijeda)');
        }
    });

    // 3. Client-side Filter by Status Tabs
    $('#kdsFilterTabs .nav-link').on('click', function(e) {
        e.preventDefault();
        $('#kdsFilterTabs .nav-link').removeClass('active');
        $(this).addClass('active');

        const targetStatus = $(this).data('status');
        filterKdsTickets();
    });

    // 4. Client-side Search by Order No / Customer
    $('#kds-search-input').on('keyup input', function() {
        filterKdsTickets();
    });

    function filterKdsTickets() {
        const activeStatus = $('#kdsFilterTabs .nav-link.active').data('status') || 'all';
        const query = $('#kds-search-input').val().toLowerCase().trim();

        $('.kds-ticket-wrapper').each(function() {
            const ticketStatus = $(this).data('status');
            const orderNo = $(this).data('order-no') || '';
            const customer = $(this).data('customer') || '';

            const matchStatus = (activeStatus === 'all' || ticketStatus === activeStatus);
            const matchQuery = (query === '' || orderNo.includes(query) || customer.includes(query));

            if (matchStatus && matchQuery) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
