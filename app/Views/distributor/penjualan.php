<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Theme Variables for Paper Light and Paper Dark (desain_tabel.md & dokumentasi_dark_mode.md) */
    :root {
        --dist-canvas-bg: #f4f6f9;
        --dist-card-bg: #ffffff;
        --dist-card-header: #ffffff;
        --dist-border: #b8b8b8;
        --dist-border-td: #c8c8c8;
        --dist-border-th: #b0b0b0;
        --dist-text: #212529;
        --dist-text-muted: #6c757d;
        --dist-th-bg: #f8fafc;
        --dist-hover-bg: #f0f4f8;
        --dist-box-bg: #f8fafc;
        --dist-input-bg: #ffffff;
    }

    body.dark-mode {
        --dist-canvas-bg: #14171a;
        --dist-card-bg: #1f2328;
        --dist-card-header: #1f2328;
        --dist-border: #374151;
        --dist-border-td: #374151;
        --dist-border-th: #374151;
        --dist-text: #f0f6fc;
        --dist-text-muted: #9ca3af;
        --dist-th-bg: #181b1f;
        --dist-hover-bg: #262c36;
        --dist-box-bg: #181b1f;
        --dist-input-bg: #181b1f;
    }

    /* Container Card Styling */
    .dist-card {
        background-color: var(--dist-card-bg) !important;
        border: 1px solid var(--dist-border) !important;
        border-radius: 4px !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .dist-card .card-header {
        background-color: var(--dist-card-header) !important;
        border-bottom: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    .dist-card .card-body {
        background-color: var(--dist-card-bg) !important;
        color: var(--dist-text) !important;
    }

    /* Form Controls */
    .dist-input {
        background-color: var(--dist-input-bg) !important;
        border: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
        border-radius: 4px;
    }

    .dist-input:focus {
        border-color: #4f46e5 !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
    }

    body.dark-mode input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    /* Strict Table Styling (desain_tabel.md) */
    .table-dist {
        border-collapse: collapse !important;
        width: 100%;
        margin-bottom: 0;
        color: var(--dist-text) !important;
    }

    .table-dist th,
    .table-dist td {
        border: 1px solid var(--dist-border) !important;
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table-dist thead th {
        background-color: var(--dist-th-bg) !important;
        color: var(--dist-text) !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 2px solid var(--dist-border) !important;
    }

    .table-dist tbody tr {
        background-color: var(--dist-card-bg) !important;
        transition: background-color 0.15s ease;
    }

    .table-dist tbody tr:hover {
        background-color: var(--dist-hover-bg) !important;
    }

    /* Info & Summary Boxes */
    .dist-summary-box {
        background-color: var(--dist-box-bg);
        border: 1px solid var(--dist-border);
        border-radius: 4px;
        padding: 12px;
    }

    /* Modal Styling in Dark Mode */
    body.dark-mode .modal-content {
        background-color: var(--dist-card-bg) !important;
        border: 1px solid var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    body.dark-mode .modal-header {
        border-bottom: 1px solid var(--dist-border) !important;
    }

    body.dark-mode .modal-footer {
        border-top: 1px solid var(--dist-border) !important;
    }

    /* Select2 in Dark Mode */
    body.dark-mode .select2-container--bootstrap4 .select2-selection {
        background-color: var(--dist-input-bg) !important;
        border-color: var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    body.dark-mode .select2-container--bootstrap4 .select2-selection__rendered {
        color: var(--dist-text) !important;
    }

    body.dark-mode .select2-dropdown {
        background-color: var(--dist-card-bg) !important;
        border-color: var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    body.dark-mode .select2-search__field {
        background-color: var(--dist-input-bg) !important;
        border-color: var(--dist-border) !important;
        color: var(--dist-text) !important;
    }

    body.dark-mode .select2-results__option {
        color: var(--dist-text) !important;
    }

    body.dark-mode .select2-results__option--highlighted {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="font-weight-bold mb-1" style="color: var(--dist-text);">
            <i class="fas fa-cash-register text-indigo mr-2"></i> Kasir &amp; Faktur Penjualan Grosir B2B
        </h4>
        <p class="small mb-0" style="color: var(--dist-text-muted);">
            Transaksi penjualan partai besar / grosir ke Apotek mitra, Klinik, Toko Obat, atau B2B
        </p>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <a href="<?= base_url('distributor/riwayat') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-receipt mr-1"></i> Riwayat Penjualan
            </a>
            <a href="<?= base_url('distributor/pelanggan') ?>" class="btn btn-outline-info btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-user-plus mr-1"></i> Tambah Pelanggan
            </a>
            <a href="<?= base_url('distributor/stok') ?>" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-boxes-stacked mr-1"></i> Cek Stok Gudang
            </a>
        </div>
    </div>
</div>

<form id="formPenjualanDistributor">
    <div class="row">
        <!-- LEFT COLUMN: TRANSACTION HEADER & ITEM LIST -->
        <div class="col-lg-8 mb-4">
            <!-- 1. HEADER CARD (CUSTOMER & INVOICE INFO) -->
            <div class="dist-card mb-3">
                <div class="card-header py-2.5">
                    <h6 class="font-weight-bold m-0" style="color: var(--dist-text); font-size: 14px;">
                        <i class="fas fa-circle-info text-indigo mr-1"></i> Data Transaksi &amp; Pelanggan
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">NO. FAKTUR</label>
                            <input type="text" class="form-control form-control-sm font-weight-bold text-indigo dist-input" id="invoice_no" name="invoice_no" value="<?= esc($invoiceNo) ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">TANGGAL FAKTUR</label>
                            <input type="date" class="form-control form-control-sm dist-input" id="sale_date" name="sale_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">METODE PEMBAYARAN</label>
                            <select class="form-control form-control-sm font-weight-bold dist-input" id="payment_type" name="payment_type" data-native="true" onchange="onPaymentTypeChange()">
                                <option value="cash" selected>Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="credit">Kredit / Tempo (TOP)</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">PILIH PELANGGAN / CUSTOMER B2B <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm dist-input" id="customer_id" name="customer_id" data-native="true" onchange="onCustomerSelect()" required>
                                <option value="">-- Pilih Customer --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c->id ?>" 
                                        data-limit="<?= (float)$c->credit_limit ?>" 
                                        data-debt="<?= (float)$c->current_receivable ?>" 
                                        data-terms="<?= (int)$c->payment_terms_days ?>"
                                        data-name="<?= esc($c->name) ?>"
                                        data-phone="<?= esc($c->phone) ?>">
                                        <?= esc($c->code) ?> - <?= esc($c->name) ?> <?= !empty($c->company_name) ? '(' . esc($c->company_name) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 mb-2" id="terms_container" style="display: none;">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">TEMPO (HARI)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control form-control-sm dist-input" id="payment_terms_days" name="payment_terms_days" value="30" min="1" onchange="calculateDueDate()">
                                <div class="input-group-append"><span class="input-group-text dist-input">Hari</span></div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-2" id="due_date_container" style="display: none;">
                            <label class="font-weight-bold text-xs mb-1" style="color: var(--dist-text-muted);">JATUH TEMPO</label>
                            <input type="date" class="form-control form-control-sm dist-input" id="due_date_preview" readonly>
                        </div>
                    </div>

                    <!-- Customer Credit Limit & Debt Information Box -->
                    <div id="customer_credit_box" class="dist-summary-box mt-2 text-xs" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <span style="color: var(--dist-text-muted);">Limit Kredit:</span> 
                                <strong id="cust_limit_val" style="color: var(--dist-text);">Rp 0</strong>
                            </div>
                            <div class="col-md-4">
                                <span style="color: var(--dist-text-muted);">Piutang Berjalan:</span> 
                                <strong id="cust_debt_val" class="text-danger">Rp 0</strong>
                            </div>
                            <div class="col-md-4">
                                <span style="color: var(--dist-text-muted);">Sisa Kuota:</span> 
                                <strong id="cust_quota_val" class="text-success">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. MEDICINE SELECTION & CART TABLE -->
            <div class="dist-card">
                <div class="card-header py-2.5 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold m-0" style="color: var(--dist-text); font-size: 14px;">
                        <i class="fas fa-boxes-stacked text-teal mr-1"></i> Rincian Barang Penjualan
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-indigo font-weight-bold" id="btnOpenItemPicker" onclick="openItemPickerModal()">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="min-height: 250px;">
                        <table class="table-dist mb-0 text-sm" id="cartTable">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Nama Obat / Barang</th>
                                    <th style="width: 15%;">Batch &amp; Exp</th>
                                    <th style="width: 12%;" class="text-center">Qty</th>
                                    <th style="width: 18%;" class="text-right">Harga Satuan</th>
                                    <th style="width: 15%;" class="text-right">Subtotal</th>
                                    <th style="width: 5%;" class="text-center"><i class="fas fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <tr id="emptyCartRow">
                                    <td colspan="6" class="text-center py-5" style="color: var(--dist-text-muted);">
                                        <i class="fas fa-cart-shopping fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                        Belum ada barang di keranjang.<br>
                                        <button type="button" class="btn btn-xs btn-indigo mt-2" onclick="openItemPickerModal()" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                                            <i class="fas fa-plus mr-1"></i> Tambah Barang Sekarang
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: TOTALS, SUMMARY & CHECKOUT -->
        <div class="col-lg-4 mb-4">
            <div class="dist-card">
                <div class="card-header py-2.5">
                    <h6 class="font-weight-bold m-0" style="color: var(--dist-text); font-size: 14px;">
                        <i class="fas fa-calculator text-indigo mr-1"></i> Ringkasan Faktur
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color: var(--dist-text-muted);">Subtotal:</span>
                        <strong id="lbl_subtotal" style="color: var(--dist-text);">Rp 0</strong>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">DISKON FAKTUR (NOMINAL)</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text dist-input">Rp</span></div>
                            <input type="number" class="form-control form-control-sm text-right font-weight-bold dist-input" id="discount_amount" name="discount_amount" value="0" min="0" oninput="calculateGrandTotal()">
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">PPN (PAJAK PERTAMBAHAN NILAI)</label>
                        <select class="form-control form-control-sm font-weight-bold dist-input" id="tax_type" data-native="true" onchange="calculateGrandTotal()">
                            <option value="0" selected>Tanpa PPN (0%)</option>
                            <option value="11">PPN 11% (Standar Faktur Pajak)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mb-2" id="tax_row" style="display: none;">
                        <span style="color: var(--dist-text-muted);">Nilai PPN (11%):</span>
                        <strong id="lbl_tax" style="color: var(--dist-text);">Rp 0</strong>
                    </div>

                    <hr class="my-2" style="border-color: var(--dist-border);">

                    <div class="dist-summary-box mb-3">
                        <span class="text-xs font-weight-bold text-uppercase d-block mb-1" style="color: var(--dist-text-muted);">TOTAL TAGIHAN FAKTUR</span>
                        <h3 class="font-weight-bold mb-0 text-indigo" id="lbl_grand_total" style="color: #4f46e5;">Rp 0</h3>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text-muted);">CATATAN / KETERANGAN FAKTUR</label>
                        <textarea class="form-control form-control-sm dist-input" id="notes" name="notes" rows="2" placeholder="Catatan termin, no. PO customer, alamat kirim, dll..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-indigo btn-block py-2.5 font-weight-bold shadow-sm" id="btnSubmitSale" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-check-circle mr-1"></i> SIMPAN &amp; CETAK FAKTUR
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-block btn-sm mt-2" onclick="resetCart()">
                        <i class="fas fa-rotate-left mr-1"></i> Reset Formulir
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL ITEM PICKER (PILIH OBAT DARI GUDANG DISTRIBUTOR) -->
<div class="modal fade" id="modalItemPicker" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header bg-indigo text-white py-2.5" style="background-color: #4f46e5;">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-boxes-stacked mr-1"></i> Pilih Obat dari Stok Gudang Distributor</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="input-group mb-3">
                    <div class="input-group-prepend"><span class="input-group-text dist-input"><i class="fas fa-search"></i></span></div>
                    <input type="text" class="form-control dist-input" id="searchItemInput" placeholder="Cari nama obat, kode, atau batch..." onkeyup="filterPickerItems()">
                </div>
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table-dist mb-0 text-sm" id="pickerTable">
                        <thead class="sticky-top">
                            <tr>
                                <th>Kode &amp; Nama Obat</th>
                                <th>Batch &amp; Exp</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-right">Harga Jual Grosir</th>
                                <th class="text-center">Pilih</th>
                            </tr>
                        </thead>
                        <tbody id="pickerTableBody">
                            <?php if (empty($stocks)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4" style="color: var(--dist-text-muted);">Stok gudang distributor kosong. Silakan tambahkan stok terlebih dahulu.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stocks as $stk): ?>
                                    <tr class="picker-item-row" data-search="<?= strtolower($stk->med_name . ' ' . $stk->med_code . ' ' . $stk->batch_no) ?>">
                                        <td>
                                            <strong class="d-block" style="color: var(--dist-text);"><?= esc($stk->med_name) ?></strong>
                                            <small style="color: var(--dist-text-muted);"><?= esc($stk->med_code) ?> | Satuan: <?= esc($stk->default_unit ?: 'PCS') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border"><?= esc($stk->batch_no ?: '-') ?></span>
                                            <small class="d-block" style="color: var(--dist-text-muted);">Exp: <?= $stk->expired_date ? date('d/m/Y', strtotime($stk->expired_date)) : '-' ?></small>
                                        </td>
                                        <td class="text-center font-weight-bold text-indigo">
                                            <?= $stk->stock ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            Rp <?= number_format($stk->selling_price, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-xs btn-indigo btn-select-stock" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;" onclick='addToCart(<?= json_encode($stk) ?>)'>
                                                <i class="fas fa-plus mr-1"></i> Pilih
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let cartItems = [];

function onPaymentTypeChange() {
    const pType = document.getElementById('payment_type').value;
    const termsCont = document.getElementById('terms_container');
    const dueCont = document.getElementById('due_date_container');

    if (pType === 'credit') {
        termsCont.style.display = 'block';
        dueCont.style.display = 'block';
        calculateDueDate();
    } else {
        termsCont.style.display = 'none';
        dueCont.style.display = 'none';
    }
}

function onCustomerSelect() {
    const sel = document.getElementById('customer_id');
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('customer_credit_box');

    if (!opt || !opt.value) {
        box.style.display = 'none';
        return;
    }

    const limit = parseFloat(opt.getAttribute('data-limit') || 0);
    const debt = parseFloat(opt.getAttribute('data-debt') || 0);
    const terms = parseInt(opt.getAttribute('data-terms') || 0);
    const quota = Math.max(0, limit - debt);

    document.getElementById('cust_limit_val').innerText = 'Rp ' + formatRupiah(limit);
    document.getElementById('cust_debt_val').innerText = 'Rp ' + formatRupiah(debt);
    document.getElementById('cust_quota_val').innerText = 'Rp ' + formatRupiah(quota);
    box.style.display = 'block';

    if (terms > 0) {
        document.getElementById('payment_terms_days').value = terms;
        calculateDueDate();
    }
}

function calculateDueDate() {
    const saleDateVal = document.getElementById('sale_date').value;
    const termsVal = parseInt(document.getElementById('payment_terms_days').value || 30);
    if (saleDateVal) {
        const d = new Date(saleDateVal);
        d.setDate(d.getDate() + termsVal);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        document.getElementById('due_date_preview').value = `${y}-${m}-${day}`;
    }
}

function openItemPickerModal() {
    $('#modalItemPicker').modal('show');
}

function filterPickerItems() {
    const q = document.getElementById('searchItemInput').value.toLowerCase();
    const rows = document.querySelectorAll('.picker-item-row');
    rows.forEach(r => {
        const txt = r.getAttribute('data-search') || '';
        r.style.display = txt.includes(q) ? '' : 'none';
    });
}

function addToCart(stockItem) {
    const existing = cartItems.find(x => x.distributor_stock_id === stockItem.id);
    if (existing) {
        if (existing.qty + 1 > stockItem.stock) {
            alert(`Stok tidak mencukupi. Maksimal stok tersedia: ${stockItem.stock}`);
            return;
        }
        existing.qty += 1;
    } else {
        cartItems.push({
            distributor_stock_id: stockItem.id,
            medicine_id: stockItem.medicine_id,
            med_name: stockItem.med_name,
            med_code: stockItem.med_code,
            batch_no: stockItem.batch_no || '-',
            expired_date: stockItem.expired_date || '',
            unit: stockItem.default_unit || 'PCS',
            selling_price: parseFloat(stockItem.selling_price || 0),
            buy_price: parseFloat(stockItem.buy_price || 0),
            max_stock: parseInt(stockItem.stock || 0),
            qty: 1,
            discount_amount: 0
        });
    }

    $('#modalItemPicker').modal('hide');
    renderCart();
}

function updateCartQty(idx, newQty) {
    newQty = parseInt(newQty);
    if (isNaN(newQty) || newQty <= 0) newQty = 1;
    if (newQty > cartItems[idx].max_stock) {
        alert(`Jumlah melebihi stok tersedia (${cartItems[idx].max_stock})`);
        newQty = cartItems[idx].max_stock;
    }
    cartItems[idx].qty = newQty;
    renderCart();
}

function updateCartPrice(idx, newPrice) {
    newPrice = parseFloat(newPrice);
    if (isNaN(newPrice) || newPrice < 0) newPrice = 0;
    cartItems[idx].selling_price = newPrice;
    renderCart();
}

function removeFromCart(idx) {
    cartItems.splice(idx, 1);
    renderCart();
}

function resetCart() {
    if (confirm('Yakin ingin mereset keranjang belanja?')) {
        cartItems = [];
        renderCart();
    }
}

function renderCart() {
    const tbody = document.getElementById('cartTableBody');
    if (cartItems.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyCartRow">
                <td colspan="6" class="text-center py-5" style="color: var(--dist-text-muted);">
                    <i class="fas fa-cart-shopping fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                    Belum ada barang di keranjang.<br>
                    <button type="button" class="btn btn-xs btn-indigo mt-2" onclick="openItemPickerModal()" style="background-color: #4f46e5; border-color: #4f46e5; color: #fff;">
                        <i class="fas fa-plus mr-1"></i> Tambah Barang Sekarang
                    </button>
                </td>
            </tr>
        `;
        calculateGrandTotal();
        return;
    }

    let html = '';
    cartItems.forEach((it, idx) => {
        const lineTotal = (it.selling_price * it.qty) - (it.discount_amount || 0);
        html += `
            <tr>
                <td>
                    <strong class="d-block" style="color: var(--dist-text);">${it.med_name}</strong>
                    <small style="color: var(--dist-text-muted);">${it.med_code} | Sat: ${it.unit}</small>
                </td>
                <td>
                    <span class="badge badge-light border">${it.batch_no}</span>
                    <small class="d-block" style="color: var(--dist-text-muted);">${it.expired_date ? it.expired_date : ''}</small>
                </td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center font-weight-bold dist-input cart-qty-input" value="${it.qty}" min="1" max="${it.max_stock}" onchange="updateCartQty(${idx}, this.value)">
                    <small class="text-xs" style="color: var(--dist-text-muted);">Maks: ${it.max_stock}</small>
                </td>
                <td class="text-right">
                    <input type="number" class="form-control form-control-sm text-right font-weight-bold dist-input" value="${it.selling_price}" min="0" onchange="updateCartPrice(${idx}, this.value)">
                </td>
                <td class="text-right font-weight-bold pt-3" style="color: var(--dist-text);">
                    Rp ${formatRupiah(lineTotal)}
                </td>
                <td class="text-center pt-3">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-item" onclick="removeFromCart(${idx})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let subtotal = 0;
    cartItems.forEach(it => {
        subtotal += (it.selling_price * it.qty) - (it.discount_amount || 0);
    });

    const discGlobal = parseFloat(document.getElementById('discount_amount').value || 0);
    const taxType = parseFloat(document.getElementById('tax_type').value || 0);

    const taxableAmount = Math.max(0, subtotal - discGlobal);
    const taxAmount = taxType > 0 ? (taxableAmount * (taxType / 100)) : 0;
    const grandTotal = Math.max(0, taxableAmount + taxAmount);

    document.getElementById('lbl_subtotal').innerText = 'Rp ' + formatRupiah(subtotal);
    document.getElementById('lbl_grand_total').innerText = 'Rp ' + formatRupiah(grandTotal);

    const taxRow = document.getElementById('tax_row');
    if (taxType > 0) {
        taxRow.style.display = 'flex';
        document.getElementById('lbl_tax').innerText = 'Rp ' + formatRupiah(taxAmount);
    } else {
        taxRow.style.display = 'none';
    }
}

function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID').format(Math.round(number));
}

// Handle Form Submission
document.getElementById('formPenjualanDistributor').addEventListener('submit', function(e) {
    e.preventDefault();

    if (cartItems.length === 0) {
        alert('Keranjang penjualan masih kosong. Silakan pilih minimal 1 barang.');
        return;
    }

    const customerId = document.getElementById('customer_id').value;
    if (!customerId) {
        alert('Silakan pilih Pelanggan / Customer B2B.');
        return;
    }

    const paymentType = document.getElementById('payment_type').value;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value || 0);
    const taxType = parseFloat(document.getElementById('tax_type').value || 0);
    
    let subtotal = 0;
    cartItems.forEach(it => { subtotal += (it.selling_price * it.qty); });
    const taxableAmount = Math.max(0, subtotal - discountAmount);
    const taxAmount = taxType > 0 ? (taxableAmount * (taxType / 100)) : 0;

    const payload = {
        customer_id: customerId,
        invoice_no: document.getElementById('invoice_no').value,
        sale_date: document.getElementById('sale_date').value,
        payment_type: paymentType,
        payment_terms_days: document.getElementById('payment_terms_days').value,
        discount_amount: discountAmount,
        tax_percent: taxType,
        tax_amount: taxAmount,
        notes: document.getElementById('notes').value,
        items: cartItems
    };

    const btnSubmit = document.getElementById('btnSubmitSale');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Transaksi...';

    fetch('<?= base_url('distributor/simpan-penjualan') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            alert(res.message);
            // Open print invoice window
            window.open('<?= base_url('distributor/cetak-faktur/') ?>/' + res.sale_id, '_blank');
            window.location.href = '<?= base_url('distributor/riwayat') ?>';
        } else {
            alert('Error: ' + (res.message || 'Terjadi kesalahan'));
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-check-circle mr-1"></i> SIMPAN &amp; CETAK FAKTUR';
        }
    })
    .catch(err => {
        alert('Gagal memproses transaksi: ' + err);
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="fas fa-check-circle mr-1"></i> SIMPAN &amp; CETAK FAKTUR';
    });
});
</script>
<?= $this->endSection() ?>
