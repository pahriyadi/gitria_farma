<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-utensils text-teal mr-2"></i> POS Resto Sehat & Terapi Gizi
                </h4>
                <small class="text-muted">Layanan makanan sehat, diet medis spesialis gizi, skincare & suplemen</small>
            </div>
            <div class="col-lg-7 col-md-12 text-lg-right">
                <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
                    <a href="<?= base_url('resto/display-antrean') ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm" title="Layar Antrean TV">
                        <i class="fas fa-tv mr-1 text-teal"></i> Display Antrean TV
                    </a>
                    <a href="<?= base_url('resto/laporan') ?>" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm" title="Laporan & Analitik Penjualan">
                        <i class="fas fa-chart-pie mr-1"></i> Laporan & Analitik
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalManageMenusList">
                        <i class="fas fa-boxes-stacked mr-1 text-teal"></i> Master Menu & Stok
                    </button>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddMenu">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Menu
                    </button>
                    <a href="<?= base_url('resto/dapur') ?>" class="btn btn-dark btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-fire-burner mr-1 text-warning"></i> Kitchen (KDS)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            
            <!-- LEFT COLUMN: PRODUCT SELECTION & CATALOG -->
            <div class="col-lg-7">
                <div class="card card-outline card-teal shadow-none">
                    <div class="card-header bg-white p-2">
                        <!-- Category Filter Tabs -->
                        <ul class="nav nav-pills" id="categoryTabs">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" href="#tab-all" data-toggle="tab" data-cat="all">
                                    <i class="fas fa-th-large mr-1"></i> Semua
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" href="#tab-makanan" data-toggle="tab" data-cat="makanan">
                                    <i class="fas fa-bowl-food mr-1"></i> Makanan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" href="#tab-minuman" data-toggle="tab" data-cat="minuman">
                                    <i class="fas fa-glass-water mr-1"></i> Jus & Detox
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold text-teal" href="#tab-diet" data-toggle="tab" data-cat="diet_gizi">
                                    <i class="fas fa-heart-pulse mr-1"></i> Diet Spesialis Gizi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" href="#tab-skincare" data-toggle="tab" data-cat="skincare">
                                    <i class="fas fa-pump-medical mr-1"></i> Skincare Dermal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" href="#tab-suplemen" data-toggle="tab" data-cat="suplemen">
                                    <i class="fas fa-capsules mr-1"></i> Suplemen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold text-danger" href="#tab-bundling" data-toggle="tab" data-cat="paket">
                                    <i class="fas fa-tags mr-1"></i> Paket Bundling
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-3">
                        <!-- Search Product Filter -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="search-product-input" class="form-control border-left-0" placeholder="Ketik nama menu, makanan sehat, skincare, suplemen, atau kode produk...">
                        </div>

                        <!-- Product Grid Items -->
                        <div class="row" id="product-grid" style="max-height: 520px; overflow-y: auto;">
                            <?php foreach ($menus as $m): ?>
                                <?php 
                                    $stk = intval($m->stock); 
                                    $isOutOfStock = ($stk <= 0);
                                    $isLowStock = ($stk > 0 && $stk <= 5);
                                ?>
                                <div class="col-md-4 col-sm-6 mb-3 product-card-wrapper" data-category="<?= esc($m->category) ?>" data-name="<?= strtolower(esc($m->name)) ?>" data-code="<?= strtolower(esc($m->code ?: '')) ?>">
                                    <div class="card h-100 border p-2 product-card-clickable <?= $isOutOfStock ? 'opacity-75 bg-light' : '' ?>" 
                                         style="cursor: pointer; transition: all 0.2s;"
                                         data-id="<?= $m->id ?>"
                                         data-code="<?= esc($m->code ?: '-') ?>"
                                         data-name="<?= esc($m->name) ?>"
                                         data-price="<?= floatval($m->price) ?>"
                                         data-category="<?= esc($m->category) ?>"
                                         data-stock="<?= $stk ?>">
                                        
                                        <!-- Top Info Bar -->
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="badge badge-<?php 
                                                if ($m->category === 'makanan') echo 'success';
                                                elseif ($m->category === 'minuman') echo 'info';
                                                elseif ($m->category === 'diet_gizi') echo 'warning text-dark';
                                                elseif ($m->category === 'skincare') echo 'pink bg-purple text-white';
                                                elseif ($m->category === 'paket') echo 'danger';
                                                else echo 'secondary';
                                            ?> font-weight-bold text-uppercase" style="font-size: 9px;">
                                                <?= esc(str_replace('_', ' ', $m->category)) ?>
                                            </span>
                                            <div>
                                                <small class="text-muted font-weight-bold mr-1"><?= esc($m->code ?: '-') ?></small>
                                                <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-1 btn-edit-menu"
                                                        data-id="<?= $m->id ?>"
                                                        data-code="<?= esc($m->code ?: '') ?>"
                                                        data-name="<?= esc($m->name) ?>"
                                                        data-category="<?= esc($m->category) ?>"
                                                        data-classification="<?= esc($m->classification) ?>"
                                                        data-price="<?= $m->price ?>"
                                                        data-stock="<?= $m->stock ?>"
                                                        data-active="<?= $m->is_active ?>"
                                                        data-description="<?= esc($m->description ?: '') ?>"
                                                        title="Edit Master Produk">
                                                    <i class="fas fa-pencil-alt" style="font-size: 10px;"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Name & Description -->
                                        <div class="font-weight-bold text-dark mb-1" style="font-size: 13px; line-height: 1.3; min-height: 34px;">
                                            <?= esc($m->name) ?>
                                        </div>
                                        <div class="text-muted small mb-1" style="font-size: 11px; min-height: 26px; line-height: 1.2;">
                                            <?= esc(mb_strimwidth($m->description ?: 'Nutrisi higienis standar gizi klinis', 0, 50, '...')) ?>
                                        </div>

                                        <!-- Stock Status & Quick Restock Bar -->
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge badge-<?= $isOutOfStock ? 'danger' : ($isLowStock ? 'warning text-dark' : 'success') ?> font-weight-bold" style="font-size: 9px;">
                                                <?= $isOutOfStock ? '<i class="fas fa-ban mr-1"></i> Habis (0)' : ($isLowStock ? '<i class="fas fa-triangle-exclamation mr-1"></i> Sisa: ' . $stk : 'Stok: ' . $stk) ?>
                                            </span>
                                            <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-1 btn-quick-restock"
                                                    data-id="<?= $m->id ?>"
                                                    data-name="<?= esc($m->name) ?>"
                                                    data-stock="<?= $stk ?>"
                                                    title="Tambah Stok (+)">
                                                <i class="fas fa-plus text-success" style="font-size: 9px;"></i> Stok
                                            </button>
                                        </div>

                                        <!-- Price & Add Button -->
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-1 border-top">
                                            <span class="font-weight-bold text-teal" style="font-size: 13px;">
                                                Rp <?= number_format($m->price, 0, ',', '.') ?>
                                            </span>
                                            <button type="button" class="btn btn-xs <?= $isOutOfStock ? 'btn-secondary disabled' : 'btn-outline-teal' ?> font-weight-bold px-2">
                                                <i class="fas fa-plus"></i> <?= $isOutOfStock ? 'Habis' : 'Pilih' ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: ORDER CART & DIRECT SETTLEMENT -->
            <div class="col-lg-5">
                <div class="card card-outline card-teal shadow-none sticky-card">
                    <div class="card-header bg-white py-2">
                        <h6 class="card-title font-weight-bold text-teal mb-0">
                            <i class="fas fa-cart-shopping mr-1"></i> Form Pemesanan & Kasir Resto
                        </h6>
                    </div>
                    
                    <form action="<?= base_url('resto/pos') ?>" method="post" id="form-resto-order">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="create_order">
                        
                        <div class="card-body p-3">
                            <!-- Dual Mode Selector -->
                            <div class="form-group mb-2">
                                <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Tipe Pemesanan:</label>
                                <div class="btn-group btn-group-toggle btn-block" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary btn-sm active font-weight-bold" id="btn-mode-umum">
                                        <input type="radio" name="order_type" value="umum" checked>
                                        <i class="fas fa-user mr-1"></i> Penjualan Bebas / Walk-In
                                    </label>
                                    <label class="btn btn-outline-teal btn-sm font-weight-bold" id="btn-mode-diet">
                                        <input type="radio" name="order_type" value="diet_pasien">
                                        <i class="fas fa-heart-pulse mr-1"></i> Diet Pasien (Poli Gizi)
                                    </label>
                                </div>
                            </div>

                            <!-- Patient Visit Linker (Visible when Diet Pasien selected) -->
                            <div id="patient-visit-group" class="p-2 mb-2 bg-light border rounded" style="display: none;">
                                <label class="text-xs font-weight-bold text-teal mb-1">
                                    <i class="fas fa-hospital-user mr-1"></i> Pilih Kunjungan Pasien Rawat Jalan:
                                </label>
                                <select name="visit_id" id="select-patient-visit" class="form-control select-searchable font-weight-bold" data-search-placeholder="🔍 Cari nama / antrean pasien...">
                                    <option value="">-- Cari Pasien / No. Rekam Medis / Poli --</option>
                                    <?php foreach ($activeVisits as $av): ?>
                                        <option value="<?= $av->id ?>" 
                                                data-name="<?= esc($av->patient_name) ?>" 
                                                data-phone="<?= esc($av->patient_phone ?: '') ?>"
                                                data-poly="<?= esc($av->polyclinic_name) ?>"
                                                data-doctor="<?= esc($av->doctor_name) ?>"
                                                data-allergies="<?= esc($av->patient_allergies ?? '') ?>">
                                            [<?= esc($av->no_rm) ?>] <?= esc($av->patient_name) ?> (<?= esc($av->polyclinic_name) ?> - <?= esc($av->doctor_name) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="form-group mt-2 mb-2">
                                    <label class="text-xs text-danger font-weight-bold mb-0">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Instruksi Diet Khusus / Alergi Pasien:
                                    </label>
                                    <input type="text" name="diet_instructions" id="input-diet-instructions" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Diet DM 1500 kkal, Rendah Garam, Alergi Kacang & Seafood">
                                </div>

                                <!-- Dynamic Patient Clinical Nutrition Recommendation Banner -->
                                <div id="patient-nutrition-recommendation-box" class="card shadow-none border bg-white mt-2 mb-1 p-2" style="display: none; border-left: 4px solid #0d9488 !important;">
                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-1 mb-1">
                                        <span class="text-xs font-weight-bold text-teal">
                                            <i class="fas fa-robot mr-1"></i> Rekomendasi Terapi Gizi & Alergi
                                        </span>
                                        <span class="badge badge-light border text-dark text-xs" id="patient-poly-badge">-</span>
                                    </div>
                                    <div class="text-xs mb-1">
                                        <strong>Diagnosa Medis:</strong> <span class="text-secondary" id="patient-diagnosis-text">-</span>
                                    </div>
                                    <div class="mb-1" id="patient-tags-container">
                                        <!-- Dynamic Badges -->
                                    </div>
                                    <div id="patient-restrictions-alert" class="alert alert-warning py-1 px-2 mb-0 text-xs font-weight-bold" style="display: none;">
                                        <i class="fas fa-hand mr-1"></i> Pantangan: <span id="patient-restrictions-text"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Walk-in Customer Fields -->
                            <div class="row" id="walkin-fields-group">
                                <div class="col-md-7 form-group mb-2">
                                    <label class="text-xs font-weight-bold mb-0">Nama Pembeli / Pemesan:</label>
                                    <input type="text" name="customer_name" id="input-customer-name" class="form-control form-control-sm font-weight-bold" placeholder="Nama Pelanggan Walk-In">
                                </div>
                                <div class="col-md-5 form-group mb-2">
                                    <label class="text-xs font-weight-bold mb-0">No. WhatsApp / HP:</label>
                                    <input type="text" name="customer_phone" id="input-customer-phone" class="form-control form-control-sm" placeholder="08xxxxxxxx">
                                </div>
                            </div>

                            <!-- Cart Table -->
                            <label class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Rincian Item Pesanan:</label>
                            <div class="table-responsive border rounded mb-2" style="max-height: 220px; overflow-y: auto;">
                                <table class="table table-sm table-striped mb-0" id="cart-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Menu / Produk</th>
                                            <th style="width: 70px;" class="text-center">Qty</th>
                                            <th style="width: 95px;" class="text-right">Subtotal</th>
                                            <th style="width: 35px;" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-body">
                                        <!-- Dynamic Cart Rows -->
                                        <tr id="empty-cart-placeholder">
                                            <td colspan="4" class="text-center text-muted py-3 small">
                                                <i class="fas fa-basket-shopping fa-2x mb-1 d-block text-secondary opacity-50"></i>
                                                Belum ada menu dipilih. Klik produk di sebelah kiri.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Calculations -->
                            <div class="bg-light p-2 rounded border mb-2">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>Subtotal Item:</span>
                                    <strong id="lbl-subtotal-cart">Rp 0</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span>Diskon / Potongan (Rp):</span>
                                    <input type="number" step="1000" name="discount_amount" id="input-discount" class="form-control form-control-sm text-right font-weight-bold" style="width: 120px; height: 26px;" value="0" min="0">
                                </div>
                                <div class="d-flex justify-content-between font-weight-bold text-teal pt-1 border-top" style="font-size: 16px;">
                                    <span>Total Akhir:</span>
                                    <span id="lbl-grandtotal-cart">Rp 0</span>
                                </div>
                            </div>

                            <!-- Settlement Method & Action -->
                            <div class="form-group mb-2">
                                <label class="text-xs font-weight-bold mb-1">Aksi Pembayaran:</label>
                                <select name="payment_action" id="select-payment-action" class="form-control form-control-sm font-weight-bold">
                                    <option value="pay_now">💵 Bayar Langsung di Kasir Resto (Lunas)</option>
                                    <option value="bill_to_clinic" id="opt-bill-clinic" disabled>🏥 Gabungkan ke Kasir Pasien Klinik (Bill-to-Clinic)</option>
                                    <option value="pay_later">🕒 Bayar Nanti (Open Order)</option>
                                </select>
                            </div>

                            <!-- Payment Channel & Tender (for Pay Now) -->
                            <div id="pay-now-details">
                                <div class="row">
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="text-xs font-weight-bold mb-0">Kanal Bayar:</label>
                                        <select name="payment_method" class="form-control form-control-sm font-weight-bold">
                                            <?php foreach ($paymentMethods as $pm): ?>
                                                <option value="<?= esc($pm->code) ?>">
                                                    <?= esc($pm->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="text-xs font-weight-bold mb-0">Uang Diterima (Rp):</label>
                                        <input type="number" step="1000" name="paid_amount" id="input-paid" class="form-control form-control-sm font-weight-bold text-right" placeholder="0">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted font-weight-bold mb-2">
                                    <span>Kembalian:</span>
                                    <span id="lbl-change" class="text-dark">Rp 0</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-teal btn-block font-weight-bold shadow-sm" id="btn-submit-order" disabled>
                                <i class="fas fa-paper-plane mr-1"></i> Proses Pesanan & Kirim ke Dapur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- BOTTOM SECTION: TODAY'S ORDERS LIST -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card card-outline card-secondary shadow-none">
                    <div class="card-header bg-white">
                        <h6 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-list-check mr-1 text-teal"></i> Monitoring Pesanan Resto & Terapi Gizi Hari Ini
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;">No. Pesanan</th>
                                        <th style="width: 80px;">Tipe</th>
                                        <th>Pemesan / Pasien</th>
                                        <th>Menu & Produk</th>
                                        <th class="text-right" style="width: 110px;">Total</th>
                                        <th class="text-center" style="width: 100px;">Status Masak</th>
                                        <th class="text-center" style="width: 110px;">Pembayaran</th>
                                        <th class="text-center" style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orders)): ?>
                                        <?php foreach ($orders as $ord): ?>
                                            <tr>
                                                <td class="font-weight-bold text-teal">
                                                    <?= esc($ord->order_no) ?>
                                                    <small class="text-muted d-block"><?= date('H:i', strtotime($ord->created_at)) ?> WIB</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $ord->order_type === 'diet_pasien' ? 'teal text-white' : 'secondary' ?>">
                                                        <?= $ord->order_type === 'diet_pasien' ? 'Diet Pasien' : 'Walk-In' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?= esc($ord->customer_name ?: '-') ?></strong>
                                                    <?php if ($ord->visit_id && $ord->polyclinic_name): ?>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-hospital-user text-teal mr-1"></i> [<?= esc($ord->no_rm) ?>] <?= esc($ord->polyclinic_name) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if ($ord->diet_instructions): ?>
                                                        <small class="text-danger font-weight-bold d-block">
                                                            <i class="fas fa-exclamation-circle"></i> <?= esc($ord->diet_instructions) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($details[$ord->id])): ?>
                                                        <ul class="list-unstyled mb-0 small">
                                                            <?php foreach ($details[$ord->id] as $d): ?>
                                                                <li>
                                                                    <span class="font-weight-bold"><?= $d->qty ?>x</span> <?= esc($d->menu_name) ?>
                                                                    <span class="badge badge-xs badge-<?= $d->status === 'ready' || $d->status === 'served' ? 'success' : ($d->status === 'cooking' ? 'warning' : 'secondary') ?>">
                                                                        <?= esc($d->status) ?>
                                                                    </span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold text-dark">
                                                    Rp <?= number_format($ord->grand_total, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                    $statusBadge = 'secondary';
                                                    if ($ord->status === 'cooking') $statusBadge = 'warning';
                                                    elseif ($ord->status === 'ready') $statusBadge = 'success';
                                                    elseif ($ord->status === 'closed') $statusBadge = 'dark';
                                                    ?>
                                                    <span class="badge badge-<?= $statusBadge ?> px-2 py-1 text-uppercase">
                                                        <?= esc($ord->status) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($ord->payment_status === 'paid'): ?>
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> LUNAS</span>
                                                        <small class="text-muted d-block"><?= strtoupper(esc($ord->payment_method ?: 'cash')) ?></small>
                                                    <?php elseif ($ord->payment_status === 'billed_to_clinic'): ?>
                                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-file-invoice"></i> BILL KLINIK</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-clock"></i> BELUM BAYAR</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url('resto/cetak-nota/' . $ord->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs" title="Cetak Struk">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        <?php if ($ord->payment_status !== 'paid' && $ord->payment_status !== 'billed_to_clinic'): ?>
                                                            <button type="button" class="btn btn-outline-success btn-xs btn-settle-modal" 
                                                                    data-id="<?= $ord->id ?>" 
                                                                    data-no="<?= esc($ord->order_no) ?>" 
                                                                    data-total="<?= $ord->grand_total ?>" 
                                                                    title="Bayar Sekarang">
                                                                <i class="fas fa-dollar-sign"></i> Bayar
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if ($ord->status !== 'closed' && $ord->status !== 'cancelled'): ?>
                                                            <form action="<?= base_url('resto/pos') ?>" method="post" class="d-inline" onsubmit="return confirm('Selesaikan / Tutup pesanan ini?');">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="order_id" value="<?= $ord->id ?>">
                                                                <input type="hidden" name="status" value="closed">
                                                                <button type="submit" class="btn btn-outline-secondary btn-xs" title="Tutup Selesai">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: SETTLE PAYMENT -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalSettlePayment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h6 class="modal-title text-white font-weight-bold" id="modalSettleTitle">Pelunasan Tagihan Resto</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('resto/pos') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="settle_payment">
                <input type="hidden" name="order_id" id="settle-order-id">
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="text-xs">Total Tagihan:</label>
                        <h5 class="font-weight-bold text-teal" id="settle-total-text">Rp 0</h5>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs">Metode Pembayaran:</label>
                        <select name="payment_method" class="form-control form-control-sm font-weight-bold">
                            <?php foreach ($paymentMethods as $pm): ?>
                                <option value="<?= esc($pm->code) ?>">
                                    <?= esc($pm->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs">Uang Diterima (Rp):</label>
                        <input type="number" name="paid_amount" id="settle-paid-input" class="form-control form-control-sm font-weight-bold text-right" required>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold">Simpan Pelunasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: ADD MASTER MENU / PRODUK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddMenu" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h6 class="modal-title text-white font-weight-bold">Tambah Menu / Produk Sehat Baru</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('resto/pos') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_menu">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kode Produk:</label>
                            <input type="text" name="code" class="form-control form-control-sm font-weight-bold" placeholder="MKN-XX / SKIN-XX">
                        </div>
                        <div class="col-md-8 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Menu / Produk: <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Nasi Merah Ayam Panggang" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kategori: <span class="text-danger">*</span></label>
                            <select name="category" class="form-control form-control-sm font-weight-bold" required>
                                <option value="makanan">Makanan Sehat</option>
                                <option value="minuman">Minuman & Detox Juice</option>
                                <option value="diet_gizi">Paket Diet Spesialis Gizi</option>
                                <option value="skincare">Skincare & Dermal Care</option>
                                <option value="suplemen">Suplemen & Nutrasetikal</option>
                                <option value="paket">Paket Bundling Medis & Wellness</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Klasifikasi:</label>
                            <select name="classification" class="form-control form-control-sm">
                                <option value="umum">Umum (Bebas)</option>
                                <option value="resep">Rujukan Diet Dokter</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Harga Jual (Rp): <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control form-control-sm font-weight-bold" placeholder="35000" required min="0">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Stok Awal:</label>
                            <input type="number" name="stock" class="form-control form-control-sm" value="100" min="0">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Deskripsi / Kandungan Gizi Singkat:</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Komposisi gizi, manfaat kesehatan atau petunjuk penggunaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: KELOLA MASTER MENU & PRODUK (DAFTAR LENGKAP & EDIT / HAPUS) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalManageMenusList" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h6 class="modal-title text-white font-weight-bold">
                    <i class="fas fa-boxes-stacked mr-1"></i> Manajemen Master Menu Resto & Produk Nutrisi Sehat
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted small mb-0">
                        Kelola katalog makanan, minuman herbal, paket diet klinis, skincare medis, dan suplemen. Anda dapat mengedit harga, stok, atau menonaktifkan/menghapus produk.
                    </p>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" data-dismiss="modal" data-toggle="modal" data-target="#modalAddMenu">
                        <i class="fas fa-plus mr-1"></i> Tambah Baru
                    </button>
                </div>

                <div class="table-responsive border rounded">
                    <table class="table table-sm table-hover table-striped mb-0 datatable" style="font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 80px;">Kode</th>
                                <th>Nama Menu / Produk</th>
                                <th style="width: 130px;">Kategori</th>
                                <th style="width: 100px;">Klasifikasi</th>
                                <th class="text-right" style="width: 100px;">Harga (Rp)</th>
                                <th class="text-center" style="width: 70px;">Stok</th>
                                <th class="text-center" style="width: 90px;">Status</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allMenus)): ?>
                                <?php foreach ($allMenus as $menuItem): ?>
                                    <tr>
                                        <td class="font-weight-bold text-teal"><?= esc($menuItem->code ?: '-') ?></td>
                                        <td>
                                            <strong><?= esc($menuItem->name) ?></strong>
                                            <?php if ($menuItem->description): ?>
                                                <small class="text-muted d-block"><?= esc(mb_strimwidth($menuItem->description, 0, 60, '...')) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                if ($menuItem->category === 'makanan') echo 'success';
                                                elseif ($menuItem->category === 'minuman') echo 'info';
                                                elseif ($menuItem->category === 'diet_gizi') echo 'warning text-dark';
                                                elseif ($menuItem->category === 'skincare') echo 'pink bg-purple text-white';
                                                else echo 'secondary';
                                            ?> text-uppercase" style="font-size: 10px;">
                                                <?= esc(str_replace('_', ' ', $menuItem->category)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $menuItem->classification === 'resep' ? 'primary' : 'light border' ?> text-capitalize">
                                                <?= esc($menuItem->classification) ?>
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold text-dark">
                                            <?= number_format($menuItem->price, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $menuItem->stock > 10 ? 'light border' : ($menuItem->stock > 0 ? 'warning text-dark' : 'danger') ?>">
                                                <?= $menuItem->stock ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $menuItem->is_active ? 'success' : 'secondary' ?>">
                                                <?= $menuItem->is_active ? 'AKTIF' : 'NONAKTIF' ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-outline-info btn-xs btn-edit-menu font-weight-bold mr-1"
                                                        data-id="<?= $menuItem->id ?>"
                                                        data-code="<?= esc($menuItem->code ?: '') ?>"
                                                        data-name="<?= esc($menuItem->name) ?>"
                                                        data-category="<?= esc($menuItem->category) ?>"
                                                        data-classification="<?= esc($menuItem->classification) ?>"
                                                        data-price="<?= $menuItem->price ?>"
                                                        data-stock="<?= $menuItem->stock ?>"
                                                        data-active="<?= $menuItem->is_active ?>"
                                                        data-description="<?= esc($menuItem->description ?: '') ?>"
                                                        title="Edit Data">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <!-- Delete / Inactivate Button -->
                                                <form action="<?= base_url('resto/pos') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / menonaktifkan produk <?= esc($menuItem->name) ?>?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_menu">
                                                    <input type="hidden" name="menu_id" value="<?= $menuItem->id ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus / Nonaktifkan">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT MASTER MENU / PRODUK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalEditMenu" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h6 class="modal-title text-white font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> Edit Data Menu / Produk Sehat
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('resto/pos') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save_menu">
                <input type="hidden" name="menu_id" id="edit-menu-id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kode Produk:</label>
                            <input type="text" name="code" id="edit-menu-code" class="form-control form-control-sm font-weight-bold" placeholder="MKN-XX">
                        </div>
                        <div class="col-md-8 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Menu / Produk: <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-menu-name" class="form-control form-control-sm font-weight-bold" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Kategori: <span class="text-danger">*</span></label>
                            <select name="category" id="edit-menu-category" class="form-control form-control-sm font-weight-bold" required>
                                <option value="makanan">Makanan Sehat</option>
                                <option value="minuman">Minuman & Detox Juice</option>
                                <option value="diet_gizi">Paket Diet Spesialis Gizi</option>
                                <option value="skincare">Skincare & Dermal Care</option>
                                <option value="suplemen">Suplemen & Nutrasetikal</option>
                                <option value="paket">Paket Bundling Medis & Wellness</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Klasifikasi:</label>
                            <select name="classification" id="edit-menu-classification" class="form-control form-control-sm">
                                <option value="umum">Umum (Bebas)</option>
                                <option value="resep">Rujukan Diet Dokter</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Harga Jual (Rp): <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit-menu-price" class="form-control form-control-sm font-weight-bold" required min="0">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Stok Produk:</label>
                            <input type="number" name="stock" id="edit-menu-stock" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Status Aktif:</label>
                            <select name="is_active" id="edit-menu-active" class="form-control form-control-sm font-weight-bold">
                                <option value="1">Aktif (Dijual)</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Deskripsi / Kandungan Gizi:</label>
                        <textarea name="description" id="edit-menu-description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: QUICK RESTOCK (PENAMBAHAN STOK CEPAT) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalQuickRestock" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success py-2">
                <h6 class="modal-title text-white font-weight-bold">
                    <i class="fas fa-boxes-packing mr-1"></i> Tambah Stok Cepat
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('resto/restock') ?>" method="post" id="form-quick-restock">
                <?= csrf_field() ?>
                <input type="hidden" name="menu_id" id="restock-menu-id">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <span class="text-xs text-muted">Nama Produk:</span>
                        <h6 class="font-weight-bold text-dark mb-0" id="restock-menu-name">-</h6>
                        <span class="badge badge-light border mt-1" id="restock-current-stock">Stok Saat Ini: 0</span>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Jumlah Tambah Stok (+): <span class="text-danger">*</span></label>
                        <input type="number" name="added_qty" id="restock-added-qty" class="form-control form-control-lg text-center font-weight-bold text-success" value="10" min="1" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Catatan / No. Batch (opsional):</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Produksi Dapur Pagi / Suplai Baru">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-plus mr-1"></i> Tambah Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: POS INTERACTIVITY -->
<!-- ========================================================================= -->
<script>
$(document).ready(function() {
    let cart = {}; // key: menu_id -> {id, name, price, qty, notes}

    // 1. Filter tabs
    $('#categoryTabs a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
        const cat = $(this).data('cat');
        filterProducts(cat, $('#search-product-input').val().toLowerCase().trim());
    });

    // 2. Search product input
    $('#search-product-input').on('keyup input', function() {
        const query = $(this).val().toLowerCase().trim();
        const activeCat = $('#categoryTabs .nav-link.active').data('cat') || 'all';
        filterProducts(activeCat, query);
    });

    function filterProducts(category, query) {
        $('.product-card-wrapper').each(function() {
            const itemCat = $(this).data('category');
            const itemName = $(this).data('name');
            const itemCode = $(this).data('code');

            const matchCat = (category === 'all' || itemCat === category);
            const matchQuery = (query === '' || itemName.includes(query) || itemCode.includes(query));

            if (matchCat && matchQuery) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // 3. Click Product Card -> Add to Cart
    $(document).on('click', '.product-card-clickable', function(e) {
        if ($(e.target).closest('.btn-edit-menu').length > 0) {
            return; // Ignore add to cart if clicking edit button
        }

        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));

        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = {
                id: id,
                name: name,
                price: price,
                qty: 1,
                notes: ''
            };
        }
        renderCart();
    });

    // 4. Render Cart
    function renderCart() {
        const tbody = $('#cart-body');
        tbody.empty();

        const keys = Object.keys(cart);
        if (keys.length === 0) {
            tbody.html(`
                <tr id="empty-cart-placeholder">
                    <td colspan="4" class="text-center text-muted py-3 small">
                        <i class="fas fa-basket-shopping fa-2x mb-1 d-block text-secondary opacity-50"></i>
                        Belum ada menu dipilih. Klik produk di sebelah kiri.
                    </td>
                </tr>
            `);
            $('#btn-submit-order').prop('disabled', true);
            updateTotals();
            return;
        }

        $('#btn-submit-order').prop('disabled', false);

        keys.forEach(function(key, idx) {
            const item = cart[key];
            const subtotal = item.price * item.qty;
            const tr = `
                <tr data-id="${item.id}">
                    <td class="py-1">
                        <input type="hidden" name="items[${idx}][menu_id]" value="${item.id}">
                        <strong class="d-block" style="font-size: 12px;">${item.name}</strong>
                        <small class="text-muted">@ Rp ${item.price.toLocaleString('id-ID')}</small>
                        <input type="text" name="items[${idx}][notes]" class="form-control form-control-xs item-notes mt-1" style="font-size: 10px; height: 22px;" placeholder="Catatan (opsional)" value="${item.notes || ''}">
                    </td>
                    <td class="py-1 text-center align-middle">
                        <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm text-center font-weight-bold input-cart-qty p-1" style="height: 28px;" value="${item.qty}" min="1" data-id="${item.id}">
                    </td>
                    <td class="py-1 text-right align-middle font-weight-bold text-dark" style="font-size: 13px;">
                        Rp ${subtotal.toLocaleString('id-ID')}
                    </td>
                    <td class="py-1 text-center align-middle">
                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-cart p-0" style="width: 22px; height: 22px;" data-id="${item.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(tr);
        });

        updateTotals();
    }

    // 5. Quantity & Notes changes
    $(document).on('change input', '.input-cart-qty', function() {
        const id = $(this).data('id');
        const qty = parseInt($(this).val()) || 1;
        if (cart[id]) {
            cart[id].qty = Math.max(1, qty);
            renderCart();
        }
    });

    $(document).on('change input', '.item-notes', function() {
        const id = $(this).closest('tr').data('id');
        if (cart[id]) {
            cart[id].notes = $(this).val();
        }
    });

    // 6. Remove Cart Item
    $(document).on('click', '.btn-remove-cart', function() {
        const id = $(this).data('id');
        delete cart[id];
        renderCart();
    });

    // 7. Update Totals & Change calculation
    function updateTotals() {
        let gross = 0;
        Object.keys(cart).forEach(function(k) {
            gross += (cart[k].price * cart[k].qty);
        });

        const discount = parseFloat($('#input-discount').val()) || 0;
        const grandTotal = Math.max(0, gross - discount);

        $('#lbl-subtotal-cart').text('Rp ' + gross.toLocaleString('id-ID'));
        $('#lbl-grandtotal-cart').text('Rp ' + grandTotal.toLocaleString('id-ID'));

        // If paid amount is less or empty, sync with grandTotal
        const paidVal = parseFloat($('#input-paid').val());
        if (isNaN(paidVal) || paidVal === 0 || paidVal < grandTotal) {
            $('#input-paid').val(grandTotal > 0 ? grandTotal : '');
        }

        calculateChange();
    }

    $('#input-discount').on('input change', function() {
        updateTotals();
    });

    $('#input-paid').on('input change', function() {
        calculateChange();
    });

    function calculateChange() {
        let gross = 0;
        Object.keys(cart).forEach(function(k) {
            gross += (cart[k].price * cart[k].qty);
        });
        const discount = parseFloat($('#input-discount').val()) || 0;
        const grandTotal = Math.max(0, gross - discount);
        const paid = parseFloat($('#input-paid').val()) || 0;

        const change = Math.max(0, paid - grandTotal);
        $('#lbl-change').text('Rp ' + change.toLocaleString('id-ID'));
    }

    // 8. Order Mode Switcher (Umum vs Diet Pasien)
    $('input[name="order_type"]').change(function() {
        const mode = $(this).val();
        if (mode === 'diet_pasien') {
            $('#patient-visit-group').slideDown(150);
            $('#opt-bill-clinic').prop('disabled', false);
            $('#select-payment-action').val('bill_to_clinic').trigger('change');
        } else {
            $('#patient-visit-group').slideUp(150);
            $('#opt-bill-clinic').prop('disabled', true);
            if ($('#select-payment-action').val() === 'bill_to_clinic') {
                $('#select-payment-action').val('pay_now').trigger('change');
            }
        }
    });

    // 9. On Patient Visit Select -> Auto Fill & Fetch Clinical Diet Recommendations
    $('#select-patient-visit').change(function() {
        const selected = $(this).find('option:selected');
        const visitId = selected.val();

        if (visitId) {
            $('#input-customer-name').val(selected.data('name') || '');
            $('#input-customer-phone').val(selected.data('phone') || '');
            
            const allergies = selected.data('allergies') || '';
            if (allergies && allergies.trim() !== '' && allergies !== '-') {
                if (!$('#input-diet-instructions').val()) {
                    $('#input-diet-instructions').val('Riwayat Alergi Pasien: ' + allergies);
                }
            }

            // Auto suggest diet notes if from Poli Gizi
            const poly = selected.data('poly') || '';
            if (poly.toLowerCase().includes('gizi')) {
                const currentVal = $('#input-diet-instructions').val();
                const giziNote = 'Rujukan Terapi Gizi Spesialis: ' + (selected.data('doctor') || '');
                $('#input-diet-instructions').val(currentVal ? currentVal + ' | ' + giziNote : giziNote);
            }

            // AJAX fetch patient diagnosis & smart nutrition recommendation
            $.ajax({
                url: '<?= base_url('resto/patient-diet-info') ?>/' + visitId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#patient-poly-badge').text(res.poly_name);
                        $('#patient-diagnosis-text').text(res.diagnosis);
                        
                        const tagsBox = $('#patient-tags-container');
                        tagsBox.empty();
                        if (res.recommendations && res.recommendations.length > 0) {
                            res.recommendations.forEach(function(rec) {
                                tagsBox.append(`
                                    <span class="badge badge-${rec.badge} mr-1 mb-1 font-weight-bold" style="font-size: 10px;" title="${rec.desc}">
                                        <i class="fas fa-check-circle mr-1"></i> ${rec.tag}
                                    </span>
                                `);
                            });
                        }

                        if (res.restrictions && res.restrictions.length > 0) {
                            $('#patient-restrictions-text').text(res.restrictions.join(', '));
                            $('#patient-restrictions-alert').show();
                        } else {
                            $('#patient-restrictions-alert').hide();
                        }

                        $('#patient-nutrition-recommendation-box').slideDown(150);
                    }
                }
            });
        } else {
            $('#patient-nutrition-recommendation-box').slideUp(150);
        }
    });

    // 10. Quick Restock Button Trigger
    $(document).on('click', '.btn-quick-restock', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const id = btn.data('id');
        const name = btn.data('name');
        const stock = btn.data('stock');

        $('#restock-menu-id').val(id);
        $('#restock-menu-name').text(name);
        $('#restock-current-stock').text('Stok Saat Ini: ' + stock);
        $('#restock-added-qty').val(10);

        $('#modalQuickRestock').modal('show');
    });

    // 11. Product Click with Out of Stock Check
    $(document).on('click', '.product-card-clickable', function(e) {
        if ($(e.target).closest('.btn-edit-menu').length || $(e.target).closest('.btn-quick-restock').length) {
            return;
        }

        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const stock = parseInt($(this).data('stock'));

        if (stock <= 0) {
            alert('Perhatian: Stok ' + name + ' habis (0). Silakan lakukan tambah stok terlebih dahulu melalui tombol [+ Stok].');
            return;
        }

        if (cart[id]) {
            if (cart[id].qty >= stock) {
                alert('Peringatan: Jumlah pesanan sudah mencapai batas stok yang tersedia (' + stock + ').');
                return;
            }
            cart[id].qty += 1;
        } else {
            cart[id] = {
                id: id,
                name: name,
                price: price,
                qty: 1,
                notes: ''
            };
        }
        renderCart();
    });

    // 12. Payment Action Selector Toggle
    $('#select-payment-action').change(function() {
        const action = $(this).val();
        if (action === 'pay_now') {
            $('#pay-now-details').slideDown(150);
        } else {
            $('#pay-now-details').slideUp(150);
        }
    });

    // 13. Modal Settle Payment Trigger
    $('.btn-settle-modal').click(function() {
        const id = $(this).data('id');
        const no = $(this).data('no');
        const total = parseFloat($(this).data('total'));

        $('#settle-order-id').val(id);
        $('#modalSettleTitle').text('Pelunasan ' + no);
        $('#settle-total-text').text('Rp ' + total.toLocaleString('id-ID'));
        $('#settle-paid-input').val(total);

        $('#modalSettlePayment').modal('show');
    });

    // 14. Modal Edit Menu / Produk Trigger
    $(document).on('click', '.btn-edit-menu', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this).closest('.btn-edit-menu');
        const id = btn.data('id');
        const code = btn.data('code') || '';
        const name = btn.data('name') || '';
        const category = btn.data('category') || 'makanan';
        const classification = btn.data('classification') || 'umum';
        const price = btn.data('price') || 0;
        const stock = btn.data('stock') || 100;
        const active = btn.data('active') !== undefined ? btn.data('active') : 1;
        const desc = btn.data('description') || '';

        $('#edit-menu-id').val(id);
        $('#edit-menu-code').val(code);
        $('#edit-menu-name').val(name);
        $('#edit-menu-category').val(category);
        $('#edit-menu-classification').val(classification);
        $('#edit-menu-price').val(price);
        $('#edit-menu-stock').val(stock);
        $('#edit-menu-active').val(active);
        $('#edit-menu-description').val(desc);

        if ($('#modalManageMenusList').hasClass('show') || $('#modalManageMenusList').is(':visible')) {
            $('#modalManageMenusList').modal('hide');
            setTimeout(function() {
                $('#modalEditMenu').modal('show');
            }, 350);
        } else {
            $('#modalEditMenu').modal('show');
        }
    });

    // Adjust datatable columns when manage modal opens
    $('#modalManageMenusList').on('shown.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#modalManageMenusList table')) {
            $('#modalManageMenusList table').DataTable().columns.adjust();
        }
    });
});
</script>

<style>
.product-card-clickable:hover {
    border-color: #0d9f4f !important;
    background-color: #f0f7f3 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(13, 159, 79, 0.12) !important;
}
.badge-xs {
    font-size: 9px;
    padding: 0.2em 0.4em;
}
.bg-purple {
    background-color: #6f42c1 !important;
}
</style>
<?= $this->endSection() ?>
