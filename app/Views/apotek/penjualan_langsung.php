<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-cash-register text-teal mr-2"></i> Kasir Apotek & Penjualan Obat</h1>
                <small class="text-muted">Layanan kasir obat bebas (OTC), tebus resep dokter, obat racikan, tusla & embalase</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Farmasi & Apotek</li>
                    <li class="breadcrumb-item active">Kasir Penjualan Obat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <?php if (session()->getFlashdata('last_sale_id')): ?>
            <div id="sale-success-banner" class="card mb-3 p-3" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #0d9f4f; border-radius:4px; box-shadow:none; animation: slideInDown 0.3s ease-out;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-start mb-2 mb-md-0">
                        <span class="d-inline-flex align-items-center justify-content-center mr-3 mt-1" style="background:#e6f4ea; color:#076e34; width:38px; height:38px; border-radius:50%; font-size:18px; flex-shrink:0;">
                            <i class="fas fa-check"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="font-weight-bold text-dark mb-0 mr-2" style="font-size:15px;">Transaksi Penjualan Obat Berhasil!</h6>
                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size:11px;">
                                    <i class="fas fa-check-circle mr-1"></i>Stok Dipotong & Jurnal Terbit
                                </span>
                            </div>
                            <p class="mb-0 text-secondary text-xs">
                                Penjualan obat telah selesai diproses dan jurnal bagi hasil telah dibukukan secara otomatis.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <a href="<?= base_url('apotek/cetak-nota/' . session()->getFlashdata('last_sale_id')) ?>" target="_blank" class="btn btn-teal btn-sm font-weight-bold mr-2">
                            <i class="fas fa-print mr-1"></i> Cetak Struk / Nota Pasien
                        </a>
                        <button type="button" class="btn btn-xs btn-outline-secondary ml-1" onclick="$('#sale-success-banner').fadeOut(300, function(){ $(this).remove(); });" title="Tutup Notifikasi" style="border:none; font-size:18px; line-height:1; padding:0 6px;">&times;</button>
                    </div>
                </div>
            </div>
            <script>
                let saleTimer = setTimeout(function() {
                    $('#sale-success-banner').fadeOut(500, function() { $(this).remove(); });
                }, 10000);
            </script>
        <?php endif; ?>

        <!-- Quick Top Action Buttons -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap: 8px;">
            <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                <!-- Button Ambil Resep Sebelumnya -->
                <button type="button" class="btn btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#modal-past-prescriptions">
                    <i class="fas fa-history mr-1"></i> Ambil Resep Sebelumnya
                </button>
                <!-- Button Pending Resep List -->
                <button type="button" class="btn btn-warning font-weight-bold shadow-sm text-dark" data-toggle="modal" data-target="#modal-pending-list" id="btn-open-pending-list">
                    <i class="fas fa-pause-circle mr-1"></i> Resep Tertunda (Pending)
                    <span class="badge badge-dark ml-1" id="badge-pending-count"><?= count($activePending ?? []) ?></span>
                </button>
            </div>
            <div>
                <a href="<?= base_url('apotek/resep') ?>" class="btn btn-outline-teal font-weight-bold shadow-sm">
                    <i class="fas fa-receipt mr-1"></i> Antrean e-Resep Pasien Rawat Jalan
                </a>
            </div>
        </div>

        <div class="card card-teal card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="otc-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-kasir-tab" data-toggle="pill" href="#tab-kasir" role="tab">
                            <i class="fas fa-cart-plus mr-1"></i> Kasir Penjualan Apotek & Racikan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-riwayat-tab" data-toggle="pill" href="#tab-riwayat" role="tab">
                            <i class="fas fa-history mr-1"></i> Riwayat Penjualan Apotek
                            <span class="badge badge-teal ml-1" id="badge-riwayat-count"><?= count($recentSales ?? []) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="otc-tabContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: KASIR PENJUALAN OBAT & RACIKAN -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-kasir" role="tabpanel">
                        <form action="<?= base_url('apotek/penjualan') ?>" method="post" id="form-otc-sale">
                            <?= csrf_field() ?>
                            <input type="hidden" name="patient_id" id="input-patient-id" value="">
                            
                            <!-- Header Info Pelanggan & Pembayaran -->
                            <div class="card bg-light border mb-3">
                                <div class="card-body p-3">
                                    <!-- Auto-Fill Member Pasien Terdaftar -->
                                    <div class="row mb-2">
                                        <div class="col-md-7">
                                            <label class="text-xs font-weight-bold text-teal mb-1"><i class="fas fa-id-card mr-1"></i> Hubungkan ke Data Pasien Klinik (Opsional):</label>
                                            <select id="select-customer-patient" class="form-control form-control-sm select-searchable font-weight-bold" data-search-placeholder="🔍 Ketik nama / No. RM pasien...">
                                                <option value="">-- Pelanggan Umum (Walk-in Non-Member) --</option>
                                                <?php if (!empty($patients)): ?>
                                                    <?php foreach ($patients as $pt): ?>
                                                        <option value="<?= $pt->id ?>" 
                                                                data-name="<?= esc($pt->name) ?>" 
                                                                data-phone="<?= esc($pt->phone ?? '') ?>" 
                                                                data-tier="<?= esc($pt->membership_tier ?? 'regular') ?>">
                                                            <?= esc($pt->name) ?> (RM: <?= esc($pt->no_rm) ?><?= !empty($pt->phone) ? ' | WA: ' . esc($pt->phone) : '' ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="text-xs font-weight-bold text-teal mb-1"><i class="fas fa-user-md mr-1"></i> Dokter Peresep / Rujukan / Konsultasi Online:</label>
                                            <select name="doctor_id" id="input-doctor-id" class="form-control form-control-sm font-weight-bold text-dark">
                                                <option value="" data-type="bebas">-- Non-Resep / Tanpa Dokter (Obat Bebas) --</option>
                                                <?php if (!empty($doctors)): ?>
                                                    <optgroup label="🌐 KONSULTASI ONLINE (Formula: KONSULTASI_ONLINE)">
                                                        <?php foreach ($doctors as $d): ?>
                                                            <option value="<?= $d->id ?>:online" data-doc-id="<?= $d->id ?>" data-type="online" data-fee-pct="15.0">
                                                                🌐 <?= esc($d->name) ?> (Konsultasi Online)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                    <optgroup label="📋 DOKTER PERESEP / RUJUKAN (Formula: RESEP DOKTER)">
                                                        <?php foreach ($doctors as $d): ?>
                                                            <option value="<?= $d->id ?>:resep" data-doc-id="<?= $d->id ?>" data-type="resep" data-fee-pct="<?= $d->prescription_fee_percent ?? 5.00 ?>">
                                                                📋 <?= esc($d->name) ?> (Tebus Resep - Fee: <?= number_format($d->prescription_fee_percent ?? 5.00, 1) ?>%)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endif; ?>
                                            </select>
                                            <div id="badge-doc-status" class="mt-1" style="display:none;"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-user text-teal mr-1"></i> Nama Pembeli / Pasien:</label>
                                            <input type="text" name="customer_name" id="input-customer-name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Budi Santoso" value="Pelanggan Umum" required>
                                        </div>
                                        <div class="col-md-2 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fab fa-whatsapp text-teal mr-1"></i> No. HP / WA:</label>
                                            <input type="text" name="customer_phone" id="input-customer-phone" class="form-control form-control-sm" placeholder="08xxxxxxxx">
                                        </div>
                                        <div class="col-md-3 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-tags text-teal mr-1"></i> Jenis Transaksi:</label>
                                            <select name="prescription_type" id="input-presc-type" class="form-control form-control-sm font-weight-bold text-teal">
                                                <option value="bebas">💊 Penjualan Obat Bebas (OTC)</option>
                                                <option value="online">🌐 Konsultasi Online (Telemedisin Dokter)</option>
                                                <option value="resep">📋 Tebus Resep Dokter (Non-Racik)</option>
                                                <option value="racikan">🥣 Resep Obat Racikan Khusus</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-wallet text-teal mr-1"></i> Pembayaran:</label>
                                            <select name="payment_method" id="select-payment-method" class="form-control form-control-sm font-weight-bold text-dark" required>
                                                <?php if (!empty($paymentMethods)): ?>
                                                    <?php foreach ($paymentMethods as $pm): ?>
                                                        <option value="<?= esc($pm->code) ?>">
                                                            <?= strtoupper(esc($pm->name)) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="tunai">TUNAI / CASH</option>
                                                    <option value="qris">QRIS DINAMIS</option>
                                                    <option value="transfer_bca">TRANSFER BCA</option>
                                                    <option value="debit">KARTU DEBIT</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-calendar text-teal mr-1"></i> Tanggal:</label>
                                            <input type="text" class="form-control form-control-sm bg-white font-weight-bold text-center" value="<?= date('d/m/Y') ?>" readonly>
                                    </div>

                                    <!-- Panel Khusus Konsultasi Online: Input Utang Fee Dokter Manual -->
                                    <div class="row" id="row-konsul-online-fee" style="display: none;">
                                        <div class="col-12">
                                            <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center justify-content-between flex-wrap shadow-xs" style="border-left: 4px solid #17a2b8; border-radius: 6px; background-color: #f0f9ff;">
                                                <div class="d-flex align-items-center mb-1 mb-md-0">
                                                    <i class="fas fa-globe fa-lg mr-2 text-info"></i>
                                                    <div>
                                                        <strong class="text-dark">Ketentuan Formula Jurnal Konsultasi Online:</strong>
                                                        <span class="text-muted text-xs d-block">Jasa Dokter Rp 20.000 (Tetap) + Utang Fee Dokter (Diisi Sendiri) sebagai pengurang Kas. Sisa kas dialokasikan ke Obat (49%), Pajak (11%), Penunjang (9%), Resep (7%), ADM (24%).</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mt-1 mt-md-0">
                                                    <label class="font-weight-bold text-dark text-xs mb-0 mr-2"><i class="fas fa-money-bill-wave text-success mr-1"></i> Utang Fee Dokter (Rp):</label>
                                                    <div class="input-group input-group-sm" style="width: 180px;">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-white font-weight-bold text-teal">Rp</span>
                                                        </div>
                                                        <input type="number" name="doctor_fee_nominal" id="input-doctor-fee-nominal" class="form-control font-weight-bold text-teal" placeholder="0" value="0" min="0" step="any">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Item Obat & Racikan -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap: 8px;">
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-pills text-teal mr-1"></i> Rincian Item Obat & Racikan</h5>
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" id="btn-add-item">
                                        <i class="fas fa-plus mr-1"></i> Tambah Item Obat
                                    </button>
                                    <button type="button" class="btn btn-dark btn-sm font-weight-bold" data-toggle="modal" data-target="#modal-add-racikan">
                                        <i class="fas fa-mortar-pestle mr-1 text-warning"></i> + Racik Obat (Racikan)
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped" id="table-otc-items">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width: 250px;">Nama Obat & Aturan Pakai</th>
                                            <th style="width: 170px;">Pilihan Batch (FEFO)</th>
                                            <th style="width: 120px;" class="text-right">Harga Satuan</th>
                                            <th style="width: 100px;" class="text-center">Jumlah (Qty)</th>
                                            <th style="width: 110px;" class="text-right">Diskon (Rp)</th>
                                            <th style="width: 110px;" class="text-right">Tuslah (Rp)</th>
                                            <th style="width: 110px;" class="text-right">Embalase (Rp)</th>
                                            <th style="width: 130px;" class="text-right">Subtotal</th>
                                            <th style="width: 45px;" class="text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="otc-items-body">
                                        <!-- Row 0 pre-rendered -->
                                        <tr id="otc-row-0" class="otc-item-row" data-index="0">
                                            <td>
                                                <input type="hidden" name="items[0][is_racikan]" class="input-is-racikan" value="0">
                                                <input type="hidden" name="items[0][racikan_name]" class="input-racikan-name" value="">
                                                <input type="hidden" name="items[0][racikan_group]" class="input-racikan-group" value="">
                                                <select name="items[0][medicine_id]" class="form-control form-control-sm select-searchable select-med font-weight-bold" data-index="0" data-search-placeholder="🔍 Cari nama obat..." required>
                                                    <option value="" data-price="0" data-unit="">-- Pilih / Ketik Nama Obat --</option>
                                                    <?php foreach ($medicines as $m): ?>
                                                        <?php 
                                                        $stock = intval($m->total_stock);
                                                        $stockBadge = $stock > 0 ? " (Stok: {$stock} {$m->unit})" : " (STOK HABIS)";
                                                        $disabled = $stock <= 0 ? "disabled" : "";
                                                        ?>
                                                        <option value="<?= $m->id ?>" data-price="<?= $m->price ?>" data-unit="<?= esc($m->unit) ?>" <?= $disabled ?>>
                                                            <?= esc($m->name) ?><?= $stockBadge ?> - Rp <?= number_format($m->price, 0, ',', '.') ?> / <?= esc($m->unit) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="text" name="items[0][dosage_instruction]" class="form-control form-control-sm mt-1" placeholder="Aturan pakai singkat (contoh: 3x1 tablet setelah makan)">
                                            </td>
                                            <td>
                                                <select name="items[0][batch_id]" class="form-control form-control-sm select-batch" data-index="0" id="select-batch-0" required>
                                                    <option value="">-- Pilih Obat Dulu --</option>
                                                </select>
                                            </td>
                                            <td class="text-right align-middle">
                                                <strong id="lbl-unit-price-0" class="text-dark">Rp 0</strong>
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][qty]" class="form-control form-control-sm text-center input-qty font-weight-bold" data-index="0" id="input-qty-0" value="1" min="1" required>
                                                <small class="text-muted d-block text-center mt-1" id="lbl-unit-label-0">pcs</small>
                                            </td>
                                            <td>
                                                <input type="number" step="100" name="items[0][discount]" class="form-control form-control-sm text-right input-disc" data-index="0" id="input-disc-0" value="0" min="0">
                                            </td>
                                            <td>
                                                <input type="number" step="100" name="items[0][tusla]" class="form-control form-control-sm text-right input-tusla" data-index="0" id="input-item-tusla-0" value="0" min="0">
                                            </td>
                                            <td>
                                                <input type="number" step="100" name="items[0][embalase]" class="form-control form-control-sm text-right input-embalase" data-index="0" id="input-item-embalase-0" value="0" min="0">
                                            </td>
                                            <td class="text-right align-middle font-weight-bold text-teal" style="font-size: 14px;">
                                                <span id="lbl-subtotal-0">Rp 0</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row" data-index="0" title="Hapus Baris">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Ringkasan Pembayaran, Tusla, Embalase & Aksi Pending -->
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold text-dark"><i class="fas fa-comment-alt text-teal mr-1"></i> Catatan Transaksi / Instruksi Farmasi:</label>
                                    <textarea name="notes" id="input-notes" class="form-control mb-3" rows="4" placeholder="Catatan opsional kasir..."></textarea>
                                    
                                    <!-- Hold / Pending Button Alert -->
                                    <div class="card bg-light border p-3">
                                        <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-pause-circle text-warning mr-1"></i> Tahan Transaksi (Pending Resep):</h6>
                                        <p class="text-xs text-muted mb-2">
                                            Jika pasien menunggu keluarga, konfirmasi dosis, atau mengambil uang, Anda bisa menahan resep ini sementara agar antrean kasir dapat melayani pasien berikutnya.
                                        </p>
                                        <button type="button" class="btn btn-outline-warning font-weight-bold text-dark shadow-sm" id="btn-pending-save">
                                            <i class="fas fa-save mr-1"></i> Tahan & Simpan ke Daftar Resep Pending
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light border p-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold text-secondary">Subtotal Obat Kotor:</span>
                                            <span class="font-weight-bold text-dark h6 mb-0" id="lbl-total-gross">Rp 0</span>
                                        </div>

                                        <!-- Fitur TUSLA (Jasa Racik / Resep) -->
                                        <div class="border rounded p-2 mb-2 bg-white">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="font-weight-bold text-teal mb-0 text-xs">
                                                    <i class="fas fa-hand-holding-medical mr-1"></i> 1. Total TUSLA (Otomatis):
                                                </label>
                                            </div>
                                            <input type="number" step="500" name="tusla_amount" id="input-tusla" class="form-control form-control-sm text-right font-weight-bold bg-light" value="0" min="0" readonly>
                                        </div>

                                        <!-- Fitur EMBALASE (Bahan Kemasan Obat) -->
                                        <div class="border rounded p-2 mb-2 bg-white">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="font-weight-bold text-info mb-0 text-xs">
                                                    <i class="fas fa-box-open mr-1"></i> 2. Total EMBALASE (Otomatis):
                                                </label>
                                            </div>
                                            <input type="number" step="500" name="embalase_amount" id="input-embalase" class="form-control form-control-sm text-right font-weight-bold bg-light" value="0" min="0" readonly>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold text-secondary">Total Potongan Diskon:</span>
                                            <span class="font-weight-bold text-danger h6 mb-0" id="lbl-total-discount">- Rp 0</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="h5 font-weight-bold text-dark mb-0">TOTAL TAGIHAN:</span>
                                            <span class="h3 font-weight-bold text-teal mb-0" id="lbl-grand-total">Rp 0</span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="font-weight-bold text-dark mb-0">Uang Diterima / Bayar (Rp):</label>
                                                <small class="text-muted font-weight-bold" id="lbl-payment-mode-hint"><i class="fas fa-money-bill-wave text-success mr-1"></i>Tunai</small>
                                            </div>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold text-teal bg-white">Rp</span>
                                                </div>
                                                <input type="number" step="500" name="paid_amount" id="input-paid-amount" class="form-control form-control-lg font-weight-bold text-right text-dark" placeholder="0" required autocomplete="off">
                                            </div>
                                            <!-- Quick Cash Presets -->
                                            <div class="d-flex flex-wrap mt-1" style="gap: 4px;" id="quick-cash-container">
                                                <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold btn-quick-money-otc" id="btn-otc-exact">Uang Pas</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="10000">10k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="20000">20k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="50000">50k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="100000">100k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="200000">200k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="500000">500k</button>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <span class="h6 font-weight-bold text-secondary mb-0">Uang Kembalian:</span>
                                            <span class="h5 font-weight-bold mb-0" id="lbl-change-amount">Rp 0</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-teal btn-block btn-lg font-weight-bold shadow mt-3" id="btn-submit-otc">
                                        <i class="fas fa-check-circle mr-1"></i> Selesaikan Transaksi & Terbitkan Nota
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: RIWAYAT PENJUALAN APOTEK -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                        <?php
                            $totalRiwayatNominal = 0;
                            $totalRiwayatResep   = 0;
                            $totalRiwayatBebas   = 0;
                            if (!empty($recentSales)) {
                                foreach ($recentSales as $rs) {
                                    $totalRiwayatNominal += (float) $rs->grand_total;
                                    if (($rs->prescription_type ?? 'bebas') === 'bebas') {
                                        $totalRiwayatBebas++;
                                    } else {
                                        $totalRiwayatResep++;
                                    }
                                }
                            }
                        ?>

                        <!-- Ringkasan Statistik Penjualan Terakhir -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <div class="p-2 border rounded bg-white shadow-xs d-flex align-items-center">
                                    <div class="rounded-circle bg-teal p-3 text-white mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-receipt fa-lg"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted text-xs font-weight-bold text-uppercase">Total Transaksi Kasir</small>
                                        <h6 class="font-weight-bold text-dark mb-0"><?= count($recentSales ?? []) ?> Transaksi</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <div class="p-2 border rounded bg-white shadow-xs d-flex align-items-center">
                                    <div class="rounded-circle bg-success p-3 text-white mr-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-money-bill-wave fa-lg"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted text-xs font-weight-bold text-uppercase">Total Omset Riwayat</small>
                                        <h6 class="font-weight-bold text-success mb-0">Rp <?= number_format($totalRiwayatNominal, 0, ',', '.') ?></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded bg-white shadow-xs d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted text-xs font-weight-bold text-uppercase">Komposisi Transaksi</small>
                                        <div class="mt-1">
                                            <span class="badge badge-info mr-1"><i class="fas fa-prescription mr-1"></i><?= $totalRiwayatResep ?> Resep/Online</span>
                                            <span class="badge badge-secondary"><i class="fas fa-capsules mr-1"></i><?= $totalRiwayatBebas ?> Obat Bebas</span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('apotek/laporan') ?>" class="btn btn-outline-teal btn-xs font-weight-bold shadow-sm" title="Buka Rekap Laporan Penjualan Lengkap">
                                        <i class="fas fa-chart-line mr-1"></i> Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable text-dark" id="table-riwayat-penjualan" style="font-size: 13px; width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">No</th>
                                        <th style="width: 140px;" class="text-center">No. Nota & Waktu</th>
                                        <th>Nama Pelanggan / Pasien</th>
                                        <th>Dokter Peresep</th>
                                        <th style="width: 110px;" class="text-center">Jenis Transaksi</th>
                                        <th style="width: 110px;" class="text-center">Metode & Kasir</th>
                                        <th style="width: 130px;" class="text-right">Grand Total</th>
                                        <th style="width: 110px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentSales)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($recentSales as $s): ?>
                                            <tr>
                                                <td class="text-center align-middle"><?= $no++ ?></td>
                                                <td class="text-center align-middle">
                                                    <strong class="text-teal font-monospace"><?= esc($s->sale_no) ?></strong>
                                                    <small class="text-muted d-block text-xs mt-1">
                                                        <i class="far fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($s->created_at ?? $s->sale_date)) ?>
                                                    </small>
                                                </td>
                                                <td class="align-middle">
                                                    <strong><?= esc($s->customer_name ?: 'Pelanggan Umum') ?></strong>
                                                    <?php if (!empty($s->customer_phone)): ?>
                                                        <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($s->customer_phone) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?php if (!empty($s->doctor_name)): ?>
                                                        <span class="text-dark font-weight-bold"><i class="fas fa-user-md text-teal mr-1"></i><?= esc($s->doctor_name) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted text-xs"><i class="fas fa-pills mr-1"></i>Obat Bebas (Non-Resep)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php if (($s->prescription_type ?? '') === 'online'): ?>
                                                        <span class="badge badge-primary py-1 px-2"><i class="fas fa-globe mr-1"></i>ONLINE</span>
                                                    <?php elseif (($s->prescription_type ?? '') === 'resep'): ?>
                                                        <span class="badge badge-info py-1 px-2"><i class="fas fa-file-prescription mr-1"></i>RESEP DOKTER</span>
                                                    <?php elseif (($s->prescription_type ?? '') === 'racikan'): ?>
                                                        <span class="badge badge-warning text-dark py-1 px-2"><i class="fas fa-mortar-pestle mr-1"></i>RACIKAN</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary py-1 px-2"><i class="fas fa-capsules mr-1"></i>OBAT BEBAS</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-light border font-weight-bold text-dark text-xs"><?= strtoupper(esc($s->payment_method)) ?></span>
                                                    <small class="text-muted d-block text-xs mt-1"><i class="fas fa-user-circle mr-1"></i><?= esc($s->cashier_name ?: 'Kasir') ?></small>
                                                </td>
                                                <td class="text-right align-middle">
                                                    <span class="font-weight-bold text-dark">Rp <?= number_format($s->grand_total, 0, ',', '.') ?></span>
                                                    <?php if ((float)($s->tusla_amount ?? 0) > 0 || (float)($s->embalase_amount ?? 0) > 0): ?>
                                                        <small class="text-muted d-block text-xs">+T/E: Rp <?= number_format(($s->tusla_amount ?? 0) + ($s->embalase_amount ?? 0), 0, ',', '.') ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group btn-group-xs" role="group">
                                                        <button type="button" class="btn btn-outline-info btn-xs font-weight-bold shadow-xs btn-view-sale-detail" data-id="<?= $s->id ?>" title="Lihat Rincian Item Obat">
                                                            <i class="fas fa-eye mr-1"></i> Detail
                                                        </button>
                                                        <a href="<?= base_url('apotek/cetak-nota/' . $s->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold shadow-xs" title="Cetak Nota / Struk Penjualan">
                                                            <i class="fas fa-print mr-1"></i> Nota
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Belum ada riwayat penjualan apotek yang tercatat.
                                            </td>
                                        </tr>
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
<!-- MODAL 1: RACIK OBAT (BUILDER OBAT RACIKAN KASIR) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-add-racikan" tabindex="-1" role="dialog" aria-labelledby="modalAddRacikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="modalAddRacikanLabel">
                    <i class="fas fa-mortar-pestle text-warning mr-2"></i> Form Racik Obat (Resep Racikan)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 px-3 mb-3 text-xs">
                    <i class="fas fa-shield-halved mr-1"></i> <strong>Kerahasiaan Formula:</strong> Detail nama obat mentah dalam racikan ini akan memotong stok secara otomatis, tetapi <strong>TIDAK AKAN DITAMPILKAN</strong> kepada pasien pada nota / kwitansi pembayaran.
                </div>

                <div class="row">
                    <div class="col-md-5 form-group">
                        <label class="font-weight-bold text-dark text-sm">Nama Paket Racikan: <span class="text-danger">*</span></label>
                        <input type="text" id="racik-name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Puyer Batuk Pilek Anak 10 Bungkus" value="Puyer Batuk Anak">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="font-weight-bold text-dark text-sm">Bentuk Sediaan:</label>
                        <select id="racik-form" class="form-control form-control-sm font-weight-bold">
                            <option value="Puyer">Puyer / Serbuk Kertas</option>
                            <option value="Kapsul">Kapsul Racikan</option>
                            <option value="Sirup Campur">Sirup / Larutan</option>
                            <option value="Salep Campur">Salep / Krim Campur</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold text-dark text-sm">Aturan Pakai: <span class="text-danger">*</span></label>
                        <input type="text" id="racik-dosage" class="form-control form-control-sm" placeholder="Contoh: 3x1 bungkus sesudah makan" value="3x1 bungkus sesudah makan">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="font-weight-bold text-dark mb-0 text-sm"><i class="fas fa-pills text-teal mr-1"></i> Komposisi Bahan Baku Obat Mentah:</label>
                    <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold" id="btn-add-racik-ing">
                        <i class="fas fa-plus mr-1"></i> Tambah Bahan Baku
                    </button>
                </div>

                <div class="table-responsive border rounded mb-3">
                    <table class="table table-sm table-bordered mb-0" id="table-racik-ingredients">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Bahan Obat</th>
                                <th style="width: 150px;">Batch Obat</th>
                                <th style="width: 90px;" class="text-center">Jumlah (Qty)</th>
                                <th style="width: 110px;" class="text-right">Harga Satuan</th>
                                <th style="width: 110px;" class="text-right">Subtotal</th>
                                <th style="width: 40px;" class="text-center">#</th>
                            </tr>
                        </thead>
                        <tbody id="racik-ing-body">
                            <!-- Dynamic ingredient rows -->
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="font-weight-bold text-teal text-xs mb-1">Tusla Racikan (Rp):</label>
                        <input type="number" id="racik-tusla" class="form-control form-control-sm text-right font-weight-bold" value="3000">
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold text-info text-xs mb-1">Embalase Kemasan (Rp):</label>
                        <input type="number" id="racik-embalase" class="form-control form-control-sm text-right font-weight-bold" value="1000">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-teal font-weight-bold shadow-sm" id="btn-apply-racikan">
                    <i class="fas fa-check mr-1"></i> Masukkan Racikan ke Keranjang Kasir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: DAFTAR RESEP TERTUNDA (PENDING PRESCRIPTIONS) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-pending-list" tabindex="-1" role="dialog" aria-labelledby="modalPendingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="modalPendingLabel">
                    <i class="fas fa-pause-circle mr-2"></i> Daftar Resep Tertunda (Pending Prescriptions)
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="table-pending-list">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 140px;">No. Pending</th>
                                <th>Nama Pelanggan / Pasien</th>
                                <th style="width: 130px;">Waktu Tahan</th>
                                <th style="width: 120px;" class="text-right">Total Tagihan</th>
                                <th style="width: 130px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pending-list-body">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Memuat daftar resep tertunda...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: AMBIL RESEP SEBELUMNYA (HISTORY REPEAT PRESCRIPTION) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-past-prescriptions" tabindex="-1" role="dialog" aria-labelledby="modalPastLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalPastLabel">
                    <i class="fas fa-history mr-2"></i> Ambil Resep Sebelumnya (Repeat / Copy Resep)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" id="input-search-past" class="form-control" placeholder="Cari berdasarkan No. RM, Nama Pasien, atau No. Kunjungan...">
                    <div class="input-group-append">
                        <button class="btn btn-info font-weight-bold" type="button" id="btn-search-past">
                            <i class="fas fa-search mr-1"></i> Cari Riwayat Resep
                        </button>
                    </div>
                </div>

                <div id="past-prescriptions-results" style="max-height: 480px; overflow-y: auto;">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-prescription-bottle-alt fa-2x mb-2 d-block text-secondary"></i>
                        Ketik nama atau No. RM pasien untuk memuat riwayat resep sebelumnya.
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: DETAIL RINCIAN PENJUALAN APOTEK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-detail-sale" tabindex="-1" role="dialog" aria-labelledby="modalDetailSaleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-teal text-white">
                <h5 class="modal-title font-weight-bold" id="modalDetailSaleLabel">
                    <i class="fas fa-receipt mr-2"></i> Rincian Transaksi Penjualan Apotek
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" id="modal-detail-sale-content">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-teal"></i>
                    Memuat rincian transaksi...
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Tutup</button>
                <a href="#" target="_blank" class="btn btn-teal font-weight-bold shadow-sm" id="btn-print-modal-nota">
                    <i class="fas fa-print mr-1"></i> Cetak Ulang Nota
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Logic for Pharmacy Cashier, Tusla, Embalase, Racikan & Pending -->
<script>
    const allMedicines = <?= json_encode($medicines) ?>;
    const batchesMap = <?= json_encode($batchesMap) ?>;

    let rowIdxCounter = 1;
    let racikGroupCounter = 1;

    function buildMedicineRowHtml(idx, defaultMedId = '', defaultQty = 1, defaultDosage = '', isRacikan = 0, racikName = '', racikGroup = '') {
        let medOptions = '<option value="" data-price="0" data-unit="">-- Pilih / Ketik Nama Obat --</option>';
        allMedicines.forEach(function(m) {
            const stock = parseInt(m.total_stock) || 0;
            const stockBadge = stock > 0 ? ` (Stok: ${stock} ${m.unit})` : ' (STOK HABIS)';
            const disabled = stock <= 0 ? 'disabled' : '';
            const selected = (m.id == defaultMedId) ? 'selected' : '';
            medOptions += `<option value="${m.id}" data-price="${m.price}" data-unit="${m.unit}" ${disabled} ${selected}>${m.name}${stockBadge} - Rp ${parseInt(m.price).toLocaleString('id-ID')} / ${m.unit}</option>`;
        });

        let racikBadge = '';
        if (isRacikan && racikName) {
            racikBadge = `<div class="mb-1"><span class="badge badge-dark" style="font-size:10px;"><i class="fas fa-mortar-pestle text-warning mr-1"></i>RACIKAN: ${racikName}</span></div>`;
        }

        return `
            <tr id="otc-row-${idx}" class="otc-item-row" data-index="${idx}">
                <td>
                    <input type="hidden" name="items[${idx}][is_racikan]" class="input-is-racikan" value="${isRacikan}">
                    <input type="hidden" name="items[${idx}][racikan_name]" class="input-racikan-name" value="${racikName}">
                    <input type="hidden" name="items[${idx}][racikan_group]" class="input-racikan-group" value="${racikGroup}">
                    ${racikBadge}
                    <select name="items[${idx}][medicine_id]" class="form-control form-control-sm select-searchable select-med font-weight-bold" data-index="${idx}" data-search-placeholder="🔍 Cari nama obat..." required>
                        ${medOptions}
                    </select>
                    <input type="text" name="items[${idx}][dosage_instruction]" class="form-control form-control-sm mt-1" placeholder="Aturan pakai singkat" value="${defaultDosage}">
                </td>
                <td>
                    <select name="items[${idx}][batch_id]" class="form-control form-control-sm select-batch" data-index="${idx}" id="select-batch-${idx}" required>
                        <option value="">-- Pilih Batch --</option>
                    </select>
                </td>
                <td class="text-right align-middle">
                    <strong id="lbl-unit-price-${idx}" class="text-dark">Rp 0</strong>
                </td>
                <td>
                    <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm text-center input-qty font-weight-bold" data-index="${idx}" id="input-qty-${idx}" value="${defaultQty}" min="1" required>
                    <small class="text-muted d-block text-center mt-1" id="lbl-unit-label-${idx}">pcs</small>
                </td>
                <td>
                    <input type="number" step="100" name="items[${idx}][discount]" class="form-control form-control-sm text-right input-disc" data-index="${idx}" id="input-disc-${idx}" value="0" min="0">
                </td>
                <td>
                    <input type="number" step="100" name="items[${idx}][tusla]" class="form-control form-control-sm text-right input-tusla" data-index="${idx}" id="input-item-tusla-${idx}" value="0" min="0">
                </td>
                <td>
                    <input type="number" step="100" name="items[${idx}][embalase]" class="form-control form-control-sm text-right input-embalase" data-index="${idx}" id="input-item-embalase-${idx}" value="0" min="0">
                </td>
                <td class="text-right align-middle font-weight-bold text-teal" style="font-size: 14px;">
                    <span id="lbl-subtotal-${idx}">Rp 0</span>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-xs btn-remove-row" data-index="${idx}" title="Hapus Baris">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function populateBatches(idx, medId) {
        const batchSelect = $(`#select-batch-${idx}`);
        batchSelect.empty();

        if (batchesMap && batchesMap[medId] && batchesMap[medId].length > 0) {
            batchesMap[medId].forEach(function(b) {
                batchSelect.append(`<option value="${b.id}">${b.batch_no} (Stok: ${b.stock}) - Exp: ${b.expired_date}</option>`);
            });
        } else {
            batchSelect.append('<option value="">-- Batch Otomatis (FEFO) --</option>');
        }
    }

    function calculateOtcTotals() {
        let totalGross = 0;
        let totalDiscount = 0;
        let totalTusla = 0;
        let totalEmbalase = 0;

        $('.otc-item-row').each(function() {
            const row = $(this);
            const idx = row.data('index');
            const medId = row.find('.select-med').val();
            
            let price = 0;
            let unit = 'pcs';

            if (medId && allMedicines) {
                const found = allMedicines.find(m => m.id == medId);
                if (found) {
                    price = parseFloat(found.price) || 0;
                    unit = found.unit || 'pcs';
                }
            }

            const qty = parseInt(row.find('.input-qty').val()) || 0;
            const disc = parseFloat(row.find('.input-disc').val()) || 0;
            const itemTusla = parseFloat(row.find('.input-tusla').val()) || 0;
            const itemEmbalase = parseFloat(row.find('.input-embalase').val()) || 0;

            const gross = price * qty;
            const subtotal = Math.max(0, gross - disc + itemTusla + itemEmbalase);

            row.find(`#lbl-unit-price-${idx}`).text('Rp ' + price.toLocaleString('id-ID'));
            row.find(`#lbl-unit-label-${idx}`).text(unit);
            row.find(`#lbl-subtotal-${idx}`).text('Rp ' + subtotal.toLocaleString('id-ID'));

            totalGross += gross;
            totalDiscount += disc;
            totalTusla += itemTusla;
            totalEmbalase += itemEmbalase;
        });

        // Set the read-only global inputs based on item totals
        $('#input-tusla').val(totalTusla);
        $('#input-embalase').val(totalEmbalase);

        const grandTotal = Math.max(0, (totalGross - totalDiscount) + totalTusla + totalEmbalase);
        
        $('#lbl-total-gross').text('Rp ' + totalGross.toLocaleString('id-ID'));
        $('#lbl-total-discount').text('- Rp ' + totalDiscount.toLocaleString('id-ID'));
        $('#lbl-grand-total').text('Rp ' + grandTotal.toLocaleString('id-ID'));

        // Jika kasir belum mengubah nominal secara manual, sinkronkan otomatis ke grand total (uang pas)
        if (!isManualPaid) {
            $('#input-paid-amount').val(grandTotal > 0 ? grandTotal : '');
        }

        updateChangeAmount();
    }

    let isManualPaid = false;

    function updateChangeAmount() {
        const grandTotal = parseFloat($('#lbl-grand-total').text().replace(/[^0-9]/g, '')) || 0;
        const method = ($('#select-payment-method').val() || 'tunai').toLowerCase();
        const rawPaid = $('#input-paid-amount').val();

        // Mode non-tunai (QRIS, Transfer, Debit): otomatis uang pas dan kunci input
        if (method !== 'tunai') {
            $('#input-paid-amount').val(grandTotal > 0 ? grandTotal : 0);
            $('#input-paid-amount').prop('readonly', true).addClass('bg-light');
            $('#quick-cash-container').hide();
            $('#lbl-payment-mode-hint').html('<i class="fas fa-credit-card text-info mr-1"></i>Non-Tunai (Otomatis Pas)');
            $('#lbl-change-amount').html('<span class="text-muted font-weight-bold">Rp 0 (Non-Tunai)</span>');
            return;
        } else {
            $('#input-paid-amount').prop('readonly', false).removeClass('bg-light');
            $('#quick-cash-container').show();
            $('#lbl-payment-mode-hint').html('<i class="fas fa-money-bill-wave text-success mr-1"></i>Tunai');
        }

        if (rawPaid === '' || isNaN(parseFloat(rawPaid))) {
            $('#lbl-change-amount').html('<span class="text-muted font-weight-bold">Rp 0</span>');
            return;
        }

        const paid = parseFloat(rawPaid) || 0;
        const diff = paid - grandTotal;

        if (diff > 0) {
            $('#lbl-change-amount').html('<span class="text-success font-weight-bold">Rp ' + Math.round(diff).toLocaleString('id-ID') + '</span>');
        } else if (diff === 0) {
            $('#lbl-change-amount').html('<span class="text-primary font-weight-bold">Rp 0 (Uang Pas)</span>');
        } else {
            const shortage = Math.abs(diff);
            $('#lbl-change-amount').html('<span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i>Kurang: Rp ' + Math.round(shortage).toLocaleString('id-ID') + '</span>');
        }
    }

    $(document).ready(function() {
        // Initial setup for row 0
        populateBatches(0, $('#otc-row-0 .select-med').val());
        calculateOtcTotals();



        // Two-way synchronization between Dokter (input-doctor-id) and Jenis Transaksi (input-presc-type)
        function updateDoctorBadge() {
            const selectedOpt = $('#input-doctor-id option:selected');
            const optType = selectedOpt.data('type');
            const prescType = $('#input-presc-type').val();
            const isOnline = (optType === 'online' || prescType === 'online');

            if (isOnline) {
                $('#badge-doc-status').html('<span class="badge badge-info py-1 px-2 shadow-sm"><i class="fas fa-globe mr-1"></i> Mode Konsultasi Online Aktif: Formula Jurnal [KONSULTASI_ONLINE]</span>').show();
                $('#row-konsul-online-fee').slideDown(200);
            } else if (optType === 'resep' || prescType === 'resep') {
                $('#badge-doc-status').html('<span class="badge badge-primary py-1 px-2 shadow-sm"><i class="fas fa-file-prescription mr-1"></i> Mode Resep Dokter: Formula Jurnal [PENJUALAN_OBAT_RESEP]</span>').show();
                $('#row-konsul-online-fee').slideUp(200);
            } else {
                $('#badge-doc-status').hide();
                $('#row-konsul-online-fee').slideUp(200);
            }
        }

        $('#input-doctor-id').on('change', function() {
            const selectedOpt = $(this).find('option:selected');
            const type = selectedOpt.data('type');
            if (type === 'online') {
                $('#input-presc-type').val('online');
            } else if (type === 'resep') {
                if ($('#input-presc-type').val() !== 'racikan') {
                    $('#input-presc-type').val('resep');
                }
            } else if (!$(this).val()) {
                if ($('#input-presc-type').val() === 'online') {
                    $('#input-presc-type').val('bebas');
                }
            }
            updateDoctorBadge();
        });

        $('#input-presc-type').on('change', function() {
            const pType = $(this).val();
            const currentOpt = $('#input-doctor-id option:selected');
            const currentType = currentOpt.data('type');

            if (pType === 'online') {
                if (currentType !== 'online') {
                    const firstOnline = $('#input-doctor-id option[data-type="online"]').first();
                    if (firstOnline.length) {
                        $('#input-doctor-id').val(firstOnline.val());
                    }
                }
            } else if (pType === 'resep' || pType === 'racikan') {
                if (currentType === 'online') {
                    const docId = currentOpt.data('doc-id');
                    const matchingResep = $(`#input-doctor-id option[data-doc-id="${docId}"][data-type="resep"]`);
                    if (matchingResep.length) {
                        $('#input-doctor-id').val(matchingResep.val());
                    } else {
                        const firstResep = $('#input-doctor-id option[data-type="resep"]').first();
                        if (firstResep.length) {
                            $('#input-doctor-id').val(firstResep.val());
                        }
                    }
                }
            } else if (pType === 'bebas') {
                if (currentType === 'online') {
                    $('#input-doctor-id').val('');
                }
            }
            updateDoctorBadge();
        });

        updateDoctorBadge();

        $('#input-tusla, #input-embalase').on('input', function() {
            calculateOtcTotals();
        });

        // Input manual uang diterima oleh kasir
        $('#input-paid-amount').on('input', function() {
            isManualPaid = true;
            updateChangeAmount();
        });

        // Auto-select text saat klik/fokus agar kasir dapat langsung mengetik nominal baru
        $('#input-paid-amount').on('focus', function() {
            $(this).select();
        });

        // Deteksi pergantian metode pembayaran (Tunai vs Non-Tunai)
        $('#select-payment-method').on('change', function() {
            const method = ($(this).val() || 'tunai').toLowerCase();
            if (method !== 'tunai') {
                isManualPaid = false;
            }
            updateChangeAmount();
        });

        // Add regular row
        $('#btn-add-item').click(function() {
            const newIdx = rowIdxCounter++;
            $('#otc-items-body').append(buildMedicineRowHtml(newIdx));
            if (window.initSearchableSelects) {
                window.initSearchableSelects($(`#otc-row-${newIdx}`));
            }
            calculateOtcTotals();
        });

        // Select medicine change
        $(document).on('change', '.select-med', function() {
            const idx = $(this).data('index');
            populateBatches(idx, $(this).val());
            calculateOtcTotals();
        });

        // Input changes in rows
        $(document).on('input', '.input-qty, .input-disc, .input-tusla, .input-embalase', function() {
            calculateOtcTotals();
        });

        // Remove row
        $(document).on('click', '.btn-remove-row', function() {
            const idx = $(this).data('index');
            if ($('.otc-item-row').length > 1) {
                $(`#otc-row-${idx}`).remove();
                calculateOtcTotals();
            } else {
                alert('Transaksi minimal harus memiliki 1 baris item obat.');
            }
        });

        // Quick money buttons
        $('.btn-quick-money-otc').click(function() {
            const grand = parseFloat($('#lbl-grand-total').text().replace(/[^0-9]/g, '')) || 0;
            if ($(this).attr('id') === 'btn-otc-exact') {
                isManualPaid = false;
                $('#input-paid-amount').val(grand > 0 ? grand : '');
            } else {
                isManualPaid = true;
                $('#input-paid-amount').val($(this).data('val'));
            }
            updateChangeAmount();
        });

        // Member Patient Select auto-fill
        $('#select-customer-patient').change(function() {
            const opt = $(this).find(':selected');
            const ptId = opt.val();
            const name = opt.data('name');
            const phone = opt.data('phone');

            if (ptId) {
                $('#input-patient-id').val(ptId);
                $('#input-customer-name').val(name);
                $('#input-customer-phone').val(phone || '');
            } else {
                $('#input-patient-id').val('');
                $('#input-customer-name').val('Pelanggan Umum');
                $('#input-customer-phone').val('');
            }
        });

        // Doctor Select auto-detects Resep Type
        $('#input-doctor-id').change(function() {
            if ($(this).val()) {
                if ($('#input-presc-type').val() === 'bebas') {
                    $('#input-presc-type').val('resep');
                }
            }
        });

        // =========================================================================
        // RACIKAN BUILDER
        // =========================================================================
        let racikIngCounter = 0;
        function addRacikIngredientRow() {
            const rIdx = racikIngCounter++;
            let opts = '<option value="" data-price="0">-- Pilih Bahan Baku --</option>';
            allMedicines.forEach(function(m) {
                if ((parseInt(m.total_stock) || 0) > 0) {
                    opts += `<option value="${m.id}" data-price="${m.price}">${m.name} (Stok: ${m.total_stock} ${m.unit}) - Rp ${parseInt(m.price).toLocaleString('id-ID')}</option>`;
                }
            });

            $('#racik-ing-body').append(`
                <tr id="racik-ing-${rIdx}" class="racik-ing-row" data-idx="${rIdx}">
                    <td>
                        <select class="form-control form-control-sm select-racik-med font-weight-bold" data-idx="${rIdx}" required>
                            ${opts}
                        </select>
                    </td>
                    <td>
                        <select class="form-control form-control-sm select-racik-batch" data-idx="${rIdx}">
                            <option value="">-- Auto FEFO --</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center input-racik-qty font-weight-bold" value="1" min="1" data-idx="${rIdx}">
                    </td>
                    <td class="text-right align-middle lbl-racik-price">Rp 0</td>
                    <td class="text-right align-middle font-weight-bold lbl-racik-sub">Rp 0</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-outline-danger btn-xs btn-remove-racik-ing"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `);
        }

        // Initialize 2 default ingredient rows in racikan modal
        addRacikIngredientRow();
        addRacikIngredientRow();

        $('#btn-add-racik-ing').click(function() {
            addRacikIngredientRow();
        });

        $(document).on('click', '.btn-remove-racik-ing', function() {
            if ($('.racik-ing-row').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Racikan minimal harus memiliki 1 bahan obat.');
            }
        });

        $(document).on('change', '.select-racik-med', function() {
            const rIdx = $(this).data('idx');
            const medId = $(this).val();
            const row = $(`#racik-ing-${rIdx}`);
            const batchSel = row.find('.select-racik-batch');
            batchSel.empty();
            if (batchesMap && batchesMap[medId]) {
                batchesMap[medId].forEach(b => {
                    batchSel.append(`<option value="${b.id}">${b.batch_no} (Stok: ${b.stock})</option>`);
                });
            } else {
                batchSel.append('<option value="">-- Auto FEFO --</option>');
            }

            const med = allMedicines.find(m => m.id == medId);
            const price = med ? parseFloat(med.price) : 0;
            const qty = parseInt(row.find('.input-racik-qty').val()) || 1;
            row.find('.lbl-racik-price').text('Rp ' + price.toLocaleString('id-ID'));
            row.find('.lbl-racik-sub').text('Rp ' + (price * qty).toLocaleString('id-ID'));
        });

        $(document).on('input', '.input-racik-qty', function() {
            const rIdx = $(this).data('idx');
            const row = $(`#racik-ing-${rIdx}`);
            const medId = row.find('.select-racik-med').val();
            const med = allMedicines.find(m => m.id == medId);
            const price = med ? parseFloat(med.price) : 0;
            const qty = parseInt($(this).val()) || 1;
            row.find('.lbl-racik-sub').text('Rp ' + (price * qty).toLocaleString('id-ID'));
        });

        // Apply Racikan to Cart
        $('#btn-apply-racikan').click(function() {
            const racikName = $('#racik-name').val().trim() || 'Puyer Racikan';
            const racikDosage = $('#racik-dosage').val().trim() || '3x1 bungkus sesudah makan';
            const racikTusla = parseFloat($('#racik-tusla').val()) || 0;
            const racikEmbalase = parseFloat($('#racik-embalase').val()) || 0;
            const grpId = 'RCK-' + (racikGroupCounter++);

            let addedCount = 0;
            $('.racik-ing-row').each(function() {
                const medId = $(this).find('.select-racik-med').val();
                const qty = parseInt($(this).find('.input-racik-qty').val()) || 1;
                const batchId = $(this).find('.select-racik-batch').val();

                if (medId) {
                    const newIdx = rowIdxCounter++;
                    const rowHtml = buildMedicineRowHtml(newIdx, medId, qty, racikDosage, 1, racikName, grpId);
                    $('#otc-items-body').append(rowHtml);
                    populateBatches(newIdx, medId);
                    if (batchId) {
                        $(`#select-batch-${newIdx}`).val(batchId);
                    }
                    addedCount++;
                }
            });

            if (addedCount > 0) {
                // Add Tusla and Embalase from racikan to main totals
                const curTusla = parseFloat($('#input-tusla').val()) || 0;
                const curEmbalase = parseFloat($('#input-embalase').val()) || 0;
                $('#input-tusla').val(curTusla + racikTusla);
                $('#input-embalase').val(curEmbalase + racikEmbalase);
                $('#input-presc-type').val('racikan');

                calculateOtcTotals();
                $('#modal-add-racikan').modal('hide');
                alert(`Racikan "${racikName}" (${addedCount} bahan baku) berhasil ditambahkan ke keranjang!`);
            } else {
                alert('Pilih minimal 1 bahan baku obat untuk racikan.');
            }
        });

        // =========================================================================
        // PENDING RESEP (HOLD CART & RESUME)
        // =========================================================================
        $('#btn-pending-save').click(function() {
            const customerName = $('#input-customer-name').val();
            const customerPhone = $('#input-customer-phone').val();
            const patientId = $('#input-patient-id').val();
            const doctorId = $('#input-doctor-id').val();
            const tusla = $('#input-tusla').val();
            const embalase = $('#input-embalase').val();
            const notes = $('#input-notes').val();
            const total = parseFloat($('#lbl-grand-total').text().replace(/[^0-9]/g, '')) || 0;

            const items = [];
            $('.otc-item-row').each(function() {
                const medId = $(this).find('.select-med').val();
                if (medId) {
                    items.push({
                        medicine_id: medId,
                        batch_id: $(this).find('.select-batch').val(),
                        qty: $(this).find('.input-qty').val(),
                        discount: $(this).find('.input-disc').val(),
                        dosage_instruction: $(this).find('input[name$="[dosage_instruction]"]').val(),
                        is_racikan: $(this).find('.input-is-racikan').val(),
                        racikan_name: $(this).find('.input-racikan-name').val(),
                        racikan_group: $(this).find('.input-racikan-group').val()
                    });
                }
            });

            if (items.length === 0) {
                alert('Tidak ada item obat di keranjang untuk ditahan.');
                return;
            }

            if (!confirm(`Tahan transaksi ini untuk "${customerName}"? Form kasir akan dibersihkan agar dapat melayani antrean lain.`)) {
                return;
            }

            $.post('<?= base_url('apotek/pending-save') ?>', {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                customer_name: customerName,
                customer_phone: customerPhone,
                patient_id: patientId,
                doctor_id: doctorId,
                source_type: 'otc',
                notes: notes,
                tusla_amount: tusla,
                embalase_amount: embalase,
                total_amount: total,
                items_json: items
            }, function(res) {
                if (res.status === 'success') {
                    alert(res.message);
                    location.reload();
                } else {
                    alert('Gagal menahan resep: ' + res.message);
                }
            }, 'json').fail(function() {
                alert('Terjadi kesalahan jaringan saat menyimpan resep pending.');
            });
        });

        // Load Pending List
        $('#btn-open-pending-list').click(function() {
            loadPendingList();
        });

        function loadPendingList() {
            $('#pending-list-body').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>');
            $.get('<?= base_url('apotek/pending-list') ?>', function(res) {
                if (res.status === 'success') {
                    $('#badge-pending-count').text(res.data.length);
                    if (res.data.length === 0) {
                        $('#pending-list-body').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success mr-1"></i> Tidak ada antrean resep yang ditahan (Pending kosong).</td></tr>');
                        return;
                    }

                    let rows = '';
                    res.data.forEach(function(item) {
                        rows += `
                            <tr>
                                <td class="font-weight-bold text-teal">${item.pending_no}</td>
                                <td>
                                    <strong>${item.customer_name}</strong>
                                    ${item.notes ? `<small class="text-muted d-block">${item.notes}</small>` : ''}
                                </td>
                                <td><small class="text-secondary">${item.created_at}</small></td>
                                <td class="text-right font-weight-bold text-dark">Rp ${parseInt(item.total_amount).toLocaleString('id-ID')}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-teal btn-xs font-weight-bold btn-resume-pending mr-1" data-id="${item.id}">
                                        <i class="fas fa-play mr-1"></i> Lanjutkan
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-xs btn-delete-pending" data-id="${item.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#pending-list-body').html(rows);
                }
            }, 'json');
        }

        // Resume Pending
        $(document).on('click', '.btn-resume-pending', function() {
            const pendingId = $(this).data('id');
            $.get('<?= base_url('apotek/pending-resume') ?>/' + pendingId, function(res) {
                if (res.status === 'success') {
                    const data = res.data;
                    const items = res.items;

                    $('#input-customer-name').val(data.customer_name);
                    $('#input-customer-phone').val(data.customer_phone || '');
                    $('#input-patient-id').val(data.patient_id || '');
                    $('#input-doctor-id').val(data.doctor_id || '');
                    $('#input-tusla').val(data.tusla_amount || 0);
                    $('#input-embalase').val(data.embalase_amount || 0);
                    $('#input-notes').val(data.notes || '');

                    $('#otc-items-body').empty();
                    if (items && items.length > 0) {
                        items.forEach(function(it) {
                            const newIdx = rowIdxCounter++;
                            const rowHtml = buildMedicineRowHtml(newIdx, it.medicine_id, it.qty, it.dosage_instruction, it.is_racikan, it.racikan_name, it.racikan_group);
                            $('#otc-items-body').append(rowHtml);
                            populateBatches(newIdx, it.medicine_id);
                            if (it.batch_id) {
                                $(`#select-batch-${newIdx}`).val(it.batch_id);
                            }
                        });
                    }

                    calculateOtcTotals();
                    $('#modal-pending-list').modal('hide');
                    alert(`Resep ${data.pending_no} berhasil dimuat kembali ke keranjang kasir!`);
                }
            }, 'json');
        });

        // Delete Pending
        $(document).on('click', '.btn-delete-pending', function() {
            const pendingId = $(this).data('id');
            if (!confirm('Batalkan draf resep pending ini?')) return;

            $.post('<?= base_url('apotek/pending-delete') ?>/' + pendingId, {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(res) {
                loadPendingList();
            }, 'json');
        });

        // =========================================================================
        // AMBIL RESEP SEBELUMNYA (SEARCH & COPY)
        // =========================================================================
        $('#btn-search-past').click(function() {
            searchPastPrescriptions();
        });
        $('#input-search-past').keypress(function(e) {
            if (e.which === 13) searchPastPrescriptions();
        });

        function searchPastPrescriptions() {
            const q = $('#input-search-past').val().trim();
            $('#past-prescriptions-results').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari resep sebelumnya...</div>');

            $.get('<?= base_url('apotek/past-prescriptions') ?>', { q: q }, function(res) {
                if (res.status === 'success') {
                    if (res.data.length === 0) {
                        $('#past-prescriptions-results').html('<div class="text-center py-4 text-muted"><i class="fas fa-info-circle text-info mr-1"></i> Tidak ditemukan resep sebelumnya dengan kata kunci tersebut.</div>');
                        return;
                    }

                    let html = '';
                    res.data.forEach(function(presc) {
                        let medList = '';
                        if (presc.items && presc.items.length > 0) {
                            presc.items.forEach(function(m) {
                                medList += `<li><strong>${m.medicine_name}</strong> - ${m.qty} ${m.unit || 'pcs'} <em>(${m.dosage || '-'})</em></li>`;
                            });
                        }

                        html += `
                            <div class="card card-outline card-info mb-3 shadow-sm">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-dark h6 mb-0">${presc.patient_name}</strong>
                                        <span class="badge badge-secondary ml-1">${presc.no_rm || '-'}</span>
                                        <small class="text-muted ml-2"><i class="fas fa-calendar mr-1"></i>${presc.created_at}</small>
                                    </div>
                                    <div>
                                        <small class="text-teal font-weight-bold mr-2"><i class="fas fa-user-md mr-1"></i>${presc.doctor_name}</small>
                                        <button type="button" class="btn btn-teal btn-xs font-weight-bold btn-copy-past-presc" data-presc='${JSON.stringify(presc)}'>
                                            <i class="fas fa-copy mr-1"></i> Salin Resep Ini
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body py-2 text-sm bg-light">
                                    <ul class="mb-0 pl-3">${medList}</ul>
                                </div>
                            </div>
                        `;
                    });
                    $('#past-prescriptions-results').html(html);
                }
            }, 'json');
        }

        // Copy Past Prescription to Active Cart
        $(document).on('click', '.btn-copy-past-presc', function() {
            const presc = $(this).data('presc');
            if (!presc) return;

            $('#input-customer-name').val(presc.patient_name);
            $('#input-patient-id').val(presc.patient_id || '');
            if (presc.doctor_id) {
                $('#input-doctor-id').val(presc.doctor_id);
                $('#input-presc-type').val('resep');
            }

            if (presc.tusla_amount > 0) $('#input-tusla').val(presc.tusla_amount);
            if (presc.embalase_amount > 0) $('#input-embalase').val(presc.embalase_amount);

            if (presc.items && presc.items.length > 0) {
                // Clear existing empty first row if untouched
                if ($('.otc-item-row').length === 1 && !$('#otc-row-0 .select-med').val()) {
                    $('#otc-items-body').empty();
                }

                presc.items.forEach(function(it) {
                    const newIdx = rowIdxCounter++;
                    const rowHtml = buildMedicineRowHtml(newIdx, it.medicine_id, it.qty, it.dosage, it.is_racikan, it.racikan_name, it.racikan_group);
                    $('#otc-items-body').append(rowHtml);
                    populateBatches(newIdx, it.medicine_id);
                });
            }

            calculateOtcTotals();
            $('#modal-past-prescriptions').modal('hide');
            alert(`Seluruh obat dari resep pasien "${presc.patient_name}" berhasil disalin ke keranjang kasir!`);
        });

        // Form Submit Validation & Anti-Double Submit
        $('#form-otc-sale').on('submit', function(e) {
            let validItemCount = 0;
            $('.otc-item-row').each(function() {
                if ($(this).find('.select-med').val()) {
                    validItemCount++;
                }
            });

            if (validItemCount === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 item obat di keranjang sebelum memproses penjualan.');
                return false;
            }

            const method = ($('#select-payment-method').val() || 'tunai').toLowerCase();
            const grandTotal = parseFloat($('#lbl-grand-total').text().replace(/[^0-9]/g, '')) || 0;
            let paid = parseFloat($('#input-paid-amount').val()) || 0;

            if (method !== 'tunai') {
                paid = grandTotal;
                $('#input-paid-amount').val(grandTotal);
            } else {
                if (paid < grandTotal) {
                    e.preventDefault();
                    const shortage = grandTotal - paid;
                    alert('Perhatian: Uang tunai yang diterima (Rp ' + Math.round(paid).toLocaleString('id-ID') + ') kurang Rp ' + Math.round(shortage).toLocaleString('id-ID') + ' dari total tagihan (Rp ' + Math.round(grandTotal).toLocaleString('id-ID') + '). Silakan periksa kembali nominal.');
                    $('#input-paid-amount').focus().select();
                    return false;
                }
            }

            const $btn = $('#btn-submit-otc');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Penjualan & Cetak Nota...');
            return true;
        });

        // =========================================================================
        // TAB RIWAYAT: ADJUST DATATABLES & MODAL DETAIL PENJUALAN
        // =========================================================================
        // Perbaiki tampilan kolom DataTables saat tab Riwayat dibuka
        $('a[data-toggle="pill"], a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($.fn.DataTable) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            }
        });

        // Buka Modal Detail Penjualan
        $(document).on('click', '.btn-view-sale-detail', function() {
            const saleId = $(this).data('id');
            $('#modal-detail-sale').modal('show');
            $('#modal-detail-sale-content').html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-teal"></i> Memuat rincian transaksi...</div>');
            $('#btn-print-modal-nota').attr('href', '<?= base_url('apotek/cetak-nota') ?>/' + saleId);

            $.get('<?= base_url('apotek/sale-detail') ?>/' + saleId, function(res) {
                if (res.status === 'success') {
                    const s = res.sale;
                    const items = res.items;
                    const j = res.journal;

                    let typeBadge = '';
                    if (s.prescription_type === 'online') {
                        typeBadge = '<span class="badge badge-primary"><i class="fas fa-globe mr-1"></i>KONSUL ONLINE</span>';
                    } else if (s.prescription_type === 'resep') {
                        typeBadge = '<span class="badge badge-info"><i class="fas fa-file-prescription mr-1"></i>RESEP DOKTER</span>';
                    } else if (s.prescription_type === 'racikan') {
                        typeBadge = '<span class="badge badge-warning text-dark"><i class="fas fa-mortar-pestle mr-1"></i>RACIKAN</span>';
                    } else {
                        typeBadge = '<span class="badge badge-secondary"><i class="fas fa-capsules mr-1"></i>OBAT BEBAS</span>';
                    }

                    let journalInfo = '';
                    if (j) {
                        journalInfo = `
                            <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center justify-content-between text-xs shadow-none border">
                                <div>
                                    <strong class="text-success"><i class="fas fa-check-circle mr-1"></i> Terbuku di Jurnal Umum:</strong>
                                    <span class="font-monospace font-weight-bold ml-1">${j.journal_no}</span> (${j.source_module})
                                </div>
                                <a href="<?= base_url('accounting/jurnal') ?>?q=${j.journal_no}" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold">
                                    <i class="fas fa-book mr-1"></i> Buka Jurnal
                                </a>
                            </div>
                        `;
                    }

                    let rows = '';
                    items.forEach(function(it, idx) {
                        const label = (it.is_racikan == 1 && it.racikan_name) ? ('<span class="badge badge-warning text-dark mr-1">Racikan</span> ' + it.racikan_name + ' <small class="text-muted">(' + it.medicine_name + ')</small>') : it.medicine_name;
                        const sub = parseFloat(it.subtotal) || 0;
                        const prc = parseFloat(it.price) || 0;
                        const disc = parseFloat(it.discount) || 0;
                        const tsl = parseFloat(it.tusla) || 0;
                        const emb = parseFloat(it.embalase) || 0;

                        rows += `
                            <tr>
                                <td class="text-center">${idx + 1}</td>
                                <td>
                                    <strong>${label}</strong>
                                    ${it.dosage_instruction ? `<small class="text-teal d-block"><i class="fas fa-info-circle mr-1"></i>${it.dosage_instruction}</small>` : ''}
                                </td>
                                <td class="text-center font-monospace text-xs">${it.batch_no || '-'}</td>
                                <td class="text-right">Rp ${Math.round(prc).toLocaleString('id-ID')}</td>
                                <td class="text-center font-weight-bold">${it.qty} ${it.unit || 'pcs'}</td>
                                <td class="text-right text-xs text-muted">
                                    ${disc > 0 ? `<span class="text-danger">-Rp ${Math.round(disc).toLocaleString('id-ID')}</span>` : '-'}
                                    ${tsl > 0 ? `<span class="text-teal d-block">+Tsl Rp ${Math.round(tsl).toLocaleString('id-ID')}</span>` : ''}
                                    ${emb > 0 ? `<span class="text-info d-block">+Emb Rp ${Math.round(emb).toLocaleString('id-ID')}</span>` : ''}
                                </td>
                                <td class="text-right font-weight-bold text-dark">Rp ${Math.round(sub).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });

                    const grand = parseFloat(s.grand_total) || 0;
                    const paid = parseFloat(s.paid_amount) || grand;
                    const chg = parseFloat(s.change_amount) || 0;

                    let html = `
                        ${journalInfo}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0 text-sm">
                                    <tr>
                                        <td style="width: 120px;" class="text-muted">No. Nota:</td>
                                        <td class="font-weight-bold text-teal font-monospace h6 mb-0">${s.sale_no}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Waktu Transaksi:</td>
                                        <td class="font-weight-bold text-dark">${s.created_at}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pelanggan:</td>
                                        <td class="font-weight-bold">${s.customer_name || 'Pelanggan Umum'} ${s.customer_phone ? `<span class="text-muted font-weight-normal">(${s.customer_phone})</span>` : ''}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0 text-sm">
                                    <tr>
                                        <td style="width: 120px;" class="text-muted">Jenis Transaksi:</td>
                                        <td>${typeBadge}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Dokter:</td>
                                        <td class="font-weight-bold text-secondary">${s.doctor_name || 'Obat Bebas (Non-Resep)'}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kasir / Metode:</td>
                                        <td><span class="badge badge-light border font-weight-bold">${(s.payment_method || 'TUNAI').toUpperCase()}</span> <small class="text-muted ml-1">oleh ${s.cashier_name || 'Kasir'}</small></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="table-responsive border rounded mb-3">
                            <table class="table table-sm table-striped table-bordered mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">No</th>
                                        <th>Nama Item Obat</th>
                                        <th style="width: 120px;" class="text-center">Batch FEFO</th>
                                        <th style="width: 110px;" class="text-right">Harga Satuan</th>
                                        <th style="width: 80px;" class="text-center">Qty</th>
                                        <th style="width: 110px;" class="text-right">Disc/Tsl/Emb</th>
                                        <th style="width: 120px;" class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                ${s.notes ? `<div class="card bg-light p-2 text-xs mb-0"><strong>Catatan Kasir:</strong> ${s.notes}</div>` : ''}
                            </div>
                            <div class="col-md-5">
                                <table class="table table-sm table-borderless mb-0 text-sm">
                                    <tr>
                                        <td class="text-muted">Subtotal Kotor:</td>
                                        <td class="text-right font-weight-bold">Rp ${Math.round(parseFloat(s.total_amount) || 0).toLocaleString('id-ID')}</td>
                                    </tr>
                                    ${parseFloat(s.discount_amount) > 0 ? `
                                    <tr>
                                        <td class="text-muted">Potongan Diskon:</td>
                                        <td class="text-right text-danger font-weight-bold">- Rp ${Math.round(parseFloat(s.discount_amount)).toLocaleString('id-ID')}</td>
                                    </tr>` : ''}
                                    ${parseFloat(s.tusla_amount) > 0 ? `
                                    <tr>
                                        <td class="text-muted">Tusla Racik/Resep:</td>
                                        <td class="text-right text-teal font-weight-bold">+ Rp ${Math.round(parseFloat(s.tusla_amount)).toLocaleString('id-ID')}</td>
                                    </tr>` : ''}
                                    ${parseFloat(s.embalase_amount) > 0 ? `
                                    <tr>
                                        <td class="text-muted">Embalase Kemasan:</td>
                                        <td class="text-right text-info font-weight-bold">+ Rp ${Math.round(parseFloat(s.embalase_amount)).toLocaleString('id-ID')}</td>
                                    </tr>` : ''}
                                    <tr class="border-top">
                                        <td class="font-weight-bold h6 text-dark">GRAND TOTAL:</td>
                                        <td class="text-right font-weight-bold h5 text-teal">Rp ${Math.round(grand).toLocaleString('id-ID')}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Uang Diterima:</td>
                                        <td class="text-right font-weight-bold text-dark">Rp ${Math.round(paid).toLocaleString('id-ID')}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kembalian:</td>
                                        <td class="text-right font-weight-bold text-success">Rp ${Math.round(chg).toLocaleString('id-ID')}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    `;
                    $('#modal-detail-sale-content').html(html);
                } else {
                    $('#modal-detail-sale-content').html('<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> ' + (res.message || 'Gagal memuat rincian transaksi.') + '</div>');
                }
            }, 'json').fail(function() {
                $('#modal-detail-sale-content').html('<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Terjadi kesalahan jaringan saat mengambil data rincian penjualan.</div>');
            });
        });

    });
</script>
<?= $this->endSection() ?>
