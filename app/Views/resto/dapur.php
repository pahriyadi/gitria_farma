<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-tv text-teal mr-2"></i> Kitchen Display System (KDS) & Dapur Gizi
                </h4>
            </div>
            <div class="col-sm-6 text-right">
                <span class="badge badge-light border p-2 mr-2">
                    <i class="fas fa-clock text-teal mr-1"></i> Real-Time KDS
                </span>
                <a href="<?= base_url('resto/pos') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                    <i class="fas fa-cash-register mr-1"></i> Kembali ke POS Kasir
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Active Orders Grid -->
        <div class="row">
            <?php if (empty($kitchenOrders)): ?>
                <div class="col-12 text-center py-5">
                    <div class="card shadow-none border p-5 bg-white">
                        <i class="fas fa-champagne-glasses fa-4x text-muted mb-3"></i>
                        <h5 class="text-secondary font-weight-bold">Semua Pesanan Dapur & Diet Telah Selesai Disajikan</h5>
                        <p class="text-muted mb-0">Tidak ada antrean pesanan makanan atau diet gizi aktif saat ini.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php 
                // Group orders by order_id
                $groupedOrders = [];
                foreach ($kitchenOrders as $ko) {
                    $groupedOrders[$ko->order_id][] = $ko;
                }
                ?>

                <?php foreach ($groupedOrders as $orderId => $kItems): ?>
                    <?php 
                    $first = $kItems[0];
                    $orderTime = strtotime($first->order_time);
                    $minsAgo = round((time() - $orderTime) / 60);
                    $isDiet = ($first->order_type === 'diet_pasien');
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 <?= $isDiet ? 'card-outline card-teal' : 'card-outline card-secondary' ?> shadow-none">
                            <!-- Card Header -->
                            <div class="card-header bg-white py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="font-weight-bold mb-0 <?= $isDiet ? 'text-teal' : 'text-dark' ?>">
                                        <i class="fas fa-receipt mr-1"></i> <?= esc($first->order_no) ?>
                                    </h5>
                                    <span class="badge badge-<?= $minsAgo > 20 ? 'danger' : ($minsAgo > 10 ? 'warning text-dark' : 'light border') ?> font-weight-bold">
                                        <i class="far fa-clock mr-1"></i> <?= $minsAgo ?> mnt lalu
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-dark font-weight-bold">
                                        <i class="fas fa-user mr-1 text-muted"></i> <?= esc($first->customer_name ?: 'Pelanggan') ?>
                                    </small>
                                    <span class="badge badge-<?= $isDiet ? 'teal text-white' : 'secondary' ?> text-uppercase" style="font-size: 10px;">
                                        <?= $isDiet ? 'DIET PASIEN POLI' : 'WALK-IN' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-3">
                                <!-- Prominent Diet / Allergy Banner -->
                                <?php if (!empty($first->diet_instructions)): ?>
                                    <div class="alert alert-warning border-warning p-2 mb-3 small font-weight-bold">
                                        <i class="fas fa-triangle-exclamation text-danger mr-1"></i> 
                                        <strong>INSTRUKSI GIZI KHUSUS:</strong>
                                        <div class="text-danger mt-1 pl-3"><?= esc($first->diet_instructions) ?></div>
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
                                                    <strong class="text-dark" style="font-size: 14px;">
                                                        <?= esc($item->menu_name) ?>
                                                    </strong>
                                                    <small class="text-muted d-block text-capitalize">
                                                        [<?= esc(str_replace('_', ' ', $item->menu_category)) ?>]
                                                    </small>
                                                </div>

                                                <!-- Item Action Status -->
                                                <div>
                                                    <?php if ($item->status === 'new'): ?>
                                                        <form action="<?= base_url('resto/dapur') ?>" method="post" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="kitchen_order_id" value="<?= $item->id ?>">
                                                            <input type="hidden" name="status" value="cooking">
                                                            <button type="submit" class="btn btn-warning btn-xs font-weight-bold shadow-sm">
                                                                <i class="fas fa-fire mr-1"></i> Mulai Masak
                                                            </button>
                                                        </form>
                                                    <?php elseif ($item->status === 'cooking'): ?>
                                                        <form action="<?= base_url('resto/dapur') ?>" method="post" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="kitchen_order_id" value="<?= $item->id ?>">
                                                            <input type="hidden" name="status" value="ready">
                                                            <button type="submit" class="btn btn-success btn-xs font-weight-bold shadow-sm">
                                                                <i class="fas fa-check-double mr-1"></i> Siap Saji
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge badge-success px-2 py-1">
                                                            <i class="fas fa-check"></i> Siap Diantar
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Card Footer: Quick Print / Summary -->
                            <div class="card-footer bg-light p-2 text-right">
                                <a href="<?= base_url('resto/cetak-nota/' . $orderId) ?>" target="_blank" class="btn btn-outline-secondary btn-xs font-weight-bold">
                                    <i class="fas fa-print mr-1"></i> Cetak Tiket Pesanan
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    // Auto reload KDS every 30 seconds to fetch incoming kitchen/diet orders
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
<?= $this->endSection() ?>
