<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-cash-register text-teal mr-2"></i> Penjualan Obat Bebas & Non-Resep (OTC)</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Farmasi & Apotek</li>
                    <li class="breadcrumb-item active">Penjualan Obat Bebas</li>
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
                                    <i class="fas fa-check-circle mr-1"></i>Stok Dipotong
                                </span>
                            </div>
                            <p class="mb-0 text-secondary text-xs">
                                Penjualan obat langsung telah selesai diproses dan tersimpan dalam riwayat kasir apotek.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <a href="<?= base_url('apotek/cetak-nota/' . session()->getFlashdata('last_sale_id')) ?>" target="_blank" class="btn btn-teal btn-sm font-weight-bold mr-2">
                            <i class="fas fa-print mr-1"></i> Cetak Struk / Nota
                        </a>
                        <button type="button" class="btn btn-xs btn-outline-secondary ml-1" onclick="$('#sale-success-banner').fadeOut(300, function(){ $(this).remove(); });" title="Tutup Notifikasi" style="border:none; font-size:18px; line-height:1; padding:0 6px;">&times;</button>
                    </div>
                </div>
            </div>
            <script>
                // Auto-dismiss sale banner after 10 seconds (with hover pause)
                let saleTimer = setTimeout(function() {
                    $('#sale-success-banner').fadeOut(500, function() { $(this).remove(); });
                }, 10000);
                $('#sale-success-banner').hover(
                    function() { clearTimeout(saleTimer); },
                    function() {
                        saleTimer = setTimeout(function() {
                            $('#sale-success-banner').fadeOut(500, function() { $(this).remove(); });
                        }, 5000);
                    }
                );
            </script>
        <?php endif; ?>

        <div class="card card-teal card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="otc-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-kasir-tab" data-toggle="pill" href="#tab-kasir" role="tab">
                            <i class="fas fa-cart-plus mr-1"></i> Kasir Penjualan Langsung
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-riwayat-tab" data-toggle="pill" href="#tab-riwayat" role="tab">
                            <i class="fas fa-history mr-1"></i> Riwayat Penjualan Obat Bebas
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="otc-tabContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: KASIR PENJUALAN OBAT BEBAS LANGSUNG -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-kasir" role="tabpanel">
                        <form action="<?= base_url('apotek/penjualan') ?>" method="post" id="form-otc-sale">
                            <?= csrf_field() ?>
                            
                            <!-- Header Info Pelanggan & Pembayaran -->
                            <div class="card bg-light border mb-4">
                                <div class="card-body p-3">
                                    <!-- Auto-Fill Member Pasien Terdaftar -->
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <label class="text-xs font-weight-bold text-teal mb-1"><i class="fas fa-id-card mr-1"></i> Pilih dari Data Member Pasien (Otomatis / Opsional):</label>
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
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-user text-teal mr-1"></i> Nama Pembeli / Pelanggan:</label>
                                            <input type="text" name="customer_name" id="input-customer-name" class="form-control form-control-sm" placeholder="Contoh: Budi Santoso (atau kosongkan untuk Umum)" value="Pelanggan Umum">
                                            <small id="badge-patient-tier" class="badge badge-warning text-dark mt-1" style="display:none;"></small>
                                        </div>
                                        <div class="col-md-3 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fab fa-whatsapp text-teal mr-1"></i> No. HP / WhatsApp (Opsional):</label>
                                            <input type="text" name="customer_phone" id="input-customer-phone" class="form-control form-control-sm" placeholder="081234567890">
                                        </div>
                                        <div class="col-md-3 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-wallet text-teal mr-1"></i> Metode Pembayaran:</label>
                                            <select name="payment_method" class="form-control form-control-sm font-weight-bold text-teal" required>
                                                <?php if (!empty($paymentMethods)): ?>
                                                    <?php foreach ($paymentMethods as $pm): ?>
                                                        <option value="<?= esc($pm->code) ?>">
                                                            <?= strtoupper(esc($pm->name)) ?> <?= !empty($pm->account_number) ? '(' . esc($pm->account_number) . ')' : '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="tunai">TUNAI / CASH</option>
                                                    <option value="qris">QRIS DINAMIS / STATIS</option>
                                                    <option value="transfer_bca">TRANSFER BANK BCA</option>
                                                    <option value="debit">KARTU DEBIT</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group mb-2">
                                            <label class="font-weight-bold text-dark mb-1"><i class="fas fa-calendar text-teal mr-1"></i> Tanggal:</label>
                                            <input type="text" class="form-control form-control-sm bg-white font-weight-bold text-center" value="<?= date('d/m/Y') ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Item Obat Bebas -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-pills text-teal mr-1"></i> Rincian Item Obat yang Dibeli</h5>
                                <button type="button" class="btn btn-teal btn-sm font-weight-bold" id="btn-add-item">
                                    <i class="fas fa-plus mr-1"></i> Tambah Item Obat
                                </button>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-striped" id="table-otc-items">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width: 250px;">Nama Obat & Sediaan</th>
                                            <th style="width: 180px;">Pilihan Batch (FEFO)</th>
                                            <th style="width: 130px;" class="text-right">Harga Satuan</th>
                                            <th style="width: 110px;" class="text-center">Jumlah (Qty)</th>
                                            <th style="width: 120px;" class="text-right">Diskon (Rp)</th>
                                            <th style="width: 140px;" class="text-right">Subtotal</th>
                                            <th style="width: 50px;" class="text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="otc-items-body">
                                        <!-- Row 0 rendered directly via PHP to ensure immediate visibility -->
                                        <tr id="otc-row-0" class="otc-item-row" data-index="0">
                                            <td>
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
                                            <td class="text-right align-middle font-weight-bold text-teal" style="font-size: 15px;">
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

                            <!-- Ringkasan Pembayaran & Kalkulasi Kembalian -->
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold text-dark"><i class="fas fa-comment-alt text-teal mr-1"></i> Catatan Transaksi / Keterangan Tambahan:</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan opsional kasir..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light border p-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold text-secondary">Total Tagihan Kotor:</span>
                                            <span class="font-weight-bold text-dark h6 mb-0" id="lbl-total-gross">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="font-weight-bold text-secondary">Total Potongan Diskon:</span>
                                            <span class="font-weight-bold text-danger h6 mb-0" id="lbl-total-discount">- Rp 0</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="h5 font-weight-bold text-dark mb-0">TOTAL PEMBAYARAN:</span>
                                            <span class="h3 font-weight-bold text-teal mb-0" id="lbl-grand-total">Rp 0</span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="font-weight-bold text-dark mb-0">Uang Diterima / Bayar (Rp):</label>
                                            </div>
                                            <input type="number" step="500" name="paid_amount" id="input-paid-amount" class="form-control form-control-lg font-weight-bold text-right text-dark" placeholder="0" required>
                                            <!-- Quick Cash Presets -->
                                            <div class="d-flex flex-wrap mt-1" style="gap: 4px;">
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" id="btn-otc-exact">Uang Pas</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="20000">20k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="50000">50k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="100000">100k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="200000">200k</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xs font-weight-bold btn-quick-money-otc" data-val="500000">500k</button>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <span class="h6 font-weight-bold text-secondary mb-0">Uang Kembalian:</span>
                                            <span class="h5 font-weight-bold text-success mb-0" id="lbl-change-amount">Rp 0</span>
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
                    <!-- TAB 2: RIWAYAT PENJUALAN OBAT BEBAS -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th style="width: 140px;" class="text-center">No. Nota</th>
                                        <th style="width: 100px;" class="text-center">Tanggal</th>
                                        <th>Nama Pelanggan</th>
                                        <th style="width: 100px;" class="text-center">Pembayaran</th>
                                        <th style="width: 130px;" class="text-right">Grand Total</th>
                                        <th style="width: 110px;">Kasir</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($recentSales as $s): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($s->sale_no) ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($s->sale_date)) ?></td>
                                            <td>
                                                <strong><?= esc($s->customer_name) ?></strong>
                                                <?php if (!empty($s->customer_phone)): ?>
                                                    <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($s->customer_phone) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><span class="badge badge-light border font-weight-bold"><?= strtoupper(esc($s->payment_method)) ?></span></td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($s->grand_total, 0, ',', '.') ?></td>
                                            <td><small class="text-secondary font-weight-bold"><?= esc($s->cashier_name ?? 'Petugas Farmasi') ?></small></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('apotek/cetak-nota/' . $s->id) ?>" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm" title="Cetak Nota / Struk Penjualan">
                                                    <i class="fas fa-print mr-1"></i> Nota
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Raw Data for Dynamic JS Execution -->
<script>
    const allMedicines = <?= json_encode($medicines) ?>;
    const batchesMap = <?= json_encode($batchesMap) ?>;

    let rowIdxCounter = 1; // Start from 1 because row 0 is pre-rendered in HTML

    function buildMedicineRowHtml(idx) {
        let medOptions = '<option value="" data-price="0" data-unit="">-- Pilih / Ketik Nama Obat --</option>';
        allMedicines.forEach(function(m) {
            const stock = parseInt(m.total_stock) || 0;
            const stockBadge = stock > 0 ? ` (Stok: ${stock} ${m.unit})` : ' (STOK HABIS)';
            const disabled = stock <= 0 ? 'disabled' : '';
            medOptions += `<option value="${m.id}" data-price="${m.price}" data-unit="${m.unit}" ${disabled}>${m.name}${stockBadge} - Rp ${parseInt(m.price).toLocaleString('id-ID')} / ${m.unit}</option>`;
        });

        return `
            <tr id="otc-row-${idx}" class="otc-item-row" data-index="${idx}">
                <td>
                    <select name="items[${idx}][medicine_id]" class="form-control form-control-sm select-searchable select-med font-weight-bold" data-index="${idx}" data-search-placeholder="🔍 Cari nama obat..." required>
                        ${medOptions}
                    </select>
                    <input type="text" name="items[${idx}][dosage_instruction]" class="form-control form-control-sm mt-1" placeholder="Aturan pakai singkat (contoh: 3x1 tablet setelah makan)">
                </td>
                <td>
                    <select name="items[${idx}][batch_id]" class="form-control form-control-sm select-batch" data-index="${idx}" id="select-batch-${idx}" required>
                        <option value="">-- Pilih Obat Dulu --</option>
                    </select>
                </td>
                <td class="text-right align-middle">
                    <strong id="lbl-unit-price-${idx}" class="text-dark">Rp 0</strong>
                </td>
                <td>
                    <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm text-center input-qty font-weight-bold" data-index="${idx}" id="input-qty-${idx}" value="1" min="1" required>
                    <small class="text-muted d-block text-center mt-1" id="lbl-unit-label-${idx}">pcs</small>
                </td>
                <td>
                    <input type="number" step="100" name="items[${idx}][discount]" class="form-control form-control-sm text-right input-disc" data-index="${idx}" id="input-disc-${idx}" value="0" min="0">
                </td>
                <td class="text-right align-middle font-weight-bold text-teal" style="font-size: 15px;">
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

    function calculateOtcTotals() {
        let totalGross = 0;
        let totalDiscount = 0;

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

            const gross = price * qty;
            const subtotal = Math.max(0, gross - disc);

            row.find(`#lbl-unit-price-${idx}`).text('Rp ' + price.toLocaleString('id-ID'));
            row.find(`#lbl-unit-label-${idx}`).text(unit);
            row.find(`#lbl-subtotal-${idx}`).text('Rp ' + subtotal.toLocaleString('id-ID'));

            totalGross += gross;
            totalDiscount += disc;
        });

        const grandTotal = Math.max(0, totalGross - totalDiscount);
        $('#lbl-total-gross').text('Rp ' + totalGross.toLocaleString('id-ID'));
        $('#lbl-total-discount').text('- Rp ' + totalDiscount.toLocaleString('id-ID'));
        $('#lbl-grand-total').text('Rp ' + grandTotal.toLocaleString('id-ID'));

        // If paid input is empty, auto-fill with exact amount
        let paid = parseFloat($('#input-paid-amount').val());
        if (isNaN(paid) || paid === 0) {
            paid = grandTotal;
            $('#input-paid-amount').val(grandTotal > 0 ? grandTotal : '');
        }

        const change = Math.max(0, (parseFloat($('#input-paid-amount').val()) || 0) - grandTotal);
        $('#lbl-change-amount').text('Rp ' + change.toLocaleString('id-ID'));
    }

    function addOtcMedicineRow() {
        const newIdx = rowIdxCounter++;
        $('#otc-items-body').append(buildMedicineRowHtml(newIdx));
        
        if (window.initSearchableSelects) {
            window.initSearchableSelects($(`#otc-row-${newIdx}`));
        }

        calculateOtcTotals();
    }

    $(document).ready(function() {
        calculateOtcTotals();

        // Button Add Row
        $('#btn-add-item').click(function() {
            addOtcMedicineRow();
        });

        // Remove Row
        $(document).on('click', '.btn-remove-row', function() {
            const idx = $(this).data('index');
            if ($('.otc-item-row').length > 1) {
                $(`#otc-row-${idx}`).remove();
                calculateOtcTotals();
            } else {
                alert('Transaksi minimal harus memiliki 1 baris item obat.');
            }
        });

        // On Medicine Change -> Populate Batches & Set Unit
        $(document).on('change select2:select', '.select-med', function() {
            const row = $(this).closest('.otc-item-row');
            const idx = row.data('index');
            const medId = $(this).val();
            const batchSelect = row.find('.select-batch');

            batchSelect.empty();

            if (!medId) {
                batchSelect.append('<option value="">-- Pilih Obat Dulu --</option>');
                row.find('.input-qty').attr('max', 1);
                batchSelect.trigger('change').trigger('updateOptions');
                calculateOtcTotals();
                return;
            }

            // Find batch list from batchesMap (support string, number, or object keys)
            let bList = [];
            if (typeof batchesMap === 'object' && batchesMap !== null) {
                bList = batchesMap[medId] || batchesMap[parseInt(medId)] || batchesMap[String(medId)] || [];
            }

            if (bList && bList.length > 0) {
                bList.forEach(function(b, bIdx) {
                    const selected = (bIdx === 0) ? 'selected' : '';
                    const expFormatted = b.expired_date ? b.expired_date : '-';
                    batchSelect.append(`<option value="${b.id}" data-stock="${b.stock}" ${selected}>${b.batch_no} (Stok: ${b.stock}, Exp: ${expFormatted})</option>`);
                });
                
                // Set max qty from first selected batch
                const firstStock = parseInt(bList[0].stock) || 1;
                row.find('.input-qty').attr('max', firstStock);
            } else {
                batchSelect.append('<option value="">-- Tidak ada batch aktif / stok habis --</option>');
                row.find('.input-qty').attr('max', 0);
            }

            // Trigger update event to notify macos-select wrapper
            batchSelect.trigger('change').trigger('updateOptions');

            calculateOtcTotals();
        });

        // On Batch Change -> Update max qty
        $(document).on('change', '.select-batch', function() {
            const row = $(this).closest('.otc-item-row');
            const opt = $(this).find(':selected');
            const stock = parseInt(opt.data('stock')) || 999;
            row.find('.input-qty').attr('max', stock);
            calculateOtcTotals();
        });

        // On Qty / Discount / Paid Amount input
        $(document).on('input change keyup', '.input-qty, .input-disc', function() {
            calculateOtcTotals();
        });

        // Auto-fill saat Member Pasien Dipilih
        $('#select-customer-patient').change(function() {
            const opt = $(this).find(':selected');
            const pId = opt.val();
            if (pId) {
                const name = opt.data('name');
                const phone = opt.data('phone');
                const tier = opt.data('tier');

                $('#input-customer-name').val(name);
                $('#input-customer-phone').val(phone || '');
                if (tier && tier !== 'regular') {
                    $('#badge-patient-tier').text('⭐ Member ' + tier.toUpperCase()).show();
                } else {
                    $('#badge-patient-tier').hide();
                }
            } else {
                $('#input-customer-name').val('Pelanggan Umum');
                $('#input-customer-phone').val('');
                $('#badge-patient-tier').hide();
            }
        });

        // Quick Cash Buttons
        $('#btn-otc-exact').click(function() {
            const grandText = $('#lbl-grand-total').text().replace(/[^0-9]/g, '');
            const grand = parseFloat(grandText) || 0;
            $('#input-paid-amount').val(grand).trigger('input');
        });

        $('.btn-quick-money-otc[data-val]').click(function() {
            const val = parseFloat($(this).data('val')) || 0;
            $('#input-paid-amount').val(val).trigger('input');
        });

        $('#input-paid-amount').on('input change keyup', function() {
            const grandText = $('#lbl-grand-total').text().replace(/[^0-9]/g, '');
            const grand = parseFloat(grandText) || 0;
            const paid = parseFloat($(this).val()) || 0;
            const change = Math.max(0, paid - grand);
            $('#lbl-change-amount').text('Rp ' + change.toLocaleString('id-ID'));
        });

        // Form Validation before submit
        $('#form-otc-sale').on('submit', function(e) {
            let hasValidItem = false;
            let stockError = false;

            $('.otc-item-row').each(function() {
                const idx = $(this).data('index');
                const medId = $(`select[name="items[${idx}][medicine_id]"]`).val();
                const qty = parseInt($(`#input-qty-${idx}`).val()) || 0;
                const maxStock = parseInt($(`#select-batch-${idx} option:selected`).data('stock')) || 0;

                if (medId) {
                    hasValidItem = true;
                    if (qty > maxStock) {
                        stockError = true;
                        alert(`Jumlah beli (${qty}) melebihi stok batch yang tersedia (${maxStock}). Silakan kurangi jumlah.`);
                        return false;
                    }
                }
            });

            if (!hasValidItem) {
                alert('Silakan pilih minimal 1 obat yang valid untuk diproses.');
                e.preventDefault();
                return false;
            }

            if (stockError) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<?= $this->endSection() ?>
