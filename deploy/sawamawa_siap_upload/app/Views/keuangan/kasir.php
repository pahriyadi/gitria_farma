<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">

        <!-- Top Information & Flash Success Banner (Paper White & Auto-Dismiss) -->
        <?php if (session()->getFlashdata('last_receipt_id')): ?>
            <div id="receipt-success-banner" class="card mb-3 p-3" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #0d9f4f; border-radius:4px; box-shadow:none; animation: slideInDown 0.3s ease-out;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-start mb-2 mb-md-0">
                        <span class="d-inline-flex align-items-center justify-content-center mr-3 mt-1" style="background:#e6f4ea; color:#076e34; width:38px; height:38px; border-radius:50%; font-size:18px; flex-shrink:0;">
                            <i class="fas fa-check"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="font-weight-bold text-dark mb-0 mr-2" style="font-size:15px;">Pembayaran Berhasil Diproses!</h6>
                                <span class="badge badge-dark px-2 py-1 font-weight-bold" style="font-size:11px; letter-spacing:0.5px;">
                                    <i class="fas fa-receipt mr-1 text-warning"></i><?= esc(session()->getFlashdata('last_receipt_no')) ?>
                                </span>
                            </div>
                            <p class="mb-0 text-secondary text-xs">
                                Kwitansi resmi telah diterbitkan dan tersimpan dalam buku kas & pembukuan keuangan.
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <a href="<?= base_url('keuangan/cetak-kwitansi/' . session()->getFlashdata('last_receipt_id')) ?>" target="_blank" class="btn btn-teal btn-sm font-weight-bold mr-2">
                            <i class="fas fa-print mr-1"></i> Cetak Kwitansi
                        </a>
                        <?php if (session()->getFlashdata('last_visit_id')): ?>
                            <a href="<?= base_url('apotek/cetak-etiket/' . session()->getFlashdata('last_visit_id')) ?>" target="_blank" class="btn btn-default btn-sm font-weight-bold border mr-2">
                                <i class="fas fa-prescription mr-1 text-teal"></i> Cetak E-Tiket
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-xs btn-outline-secondary ml-1" onclick="$('#receipt-success-banner').fadeOut(300, function(){ $(this).remove(); });" title="Tutup Notifikasi" style="border:none; font-size:18px; line-height:1; padding:0 6px;">&times;</button>
                    </div>
                </div>
            </div>
            <script>
                // Auto-dismiss receipt banner after 10 seconds (with hover pause)
                let receiptTimer = setTimeout(function() {
                    $('#receipt-success-banner').fadeOut(500, function() { $(this).remove(); });
                }, 10000);
                $('#receipt-success-banner').hover(
                    function() { clearTimeout(receiptTimer); },
                    function() {
                        receiptTimer = setTimeout(function() {
                            $('#receipt-success-banner').fadeOut(500, function() { $(this).remove(); });
                        }, 5000);
                    }
                );
            </script>
        <?php endif; ?>

        <!-- Top Header Bar with Quick Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="font-weight-bold text-dark mb-0">
                    <i class="fas fa-cash-register text-teal mr-2"></i> Kasir Pembayaran & Kwitansi Pasien
                </h4>
                <small class="text-muted">Proses pelunasan tagihan rawat jalan, resep obat farmasi, resto gizi, dan cetak kwitansi resmi</small>
            </div>
            <div>
                <a href="<?= base_url('keuangan/rekap-harian') ?>" class="btn btn-outline-teal font-weight-bold shadow-sm mr-2">
                    <i class="fas fa-calendar-check mr-1"></i> Rekap Kasir & Tutup Shift
                </a>
                <a href="<?= base_url('keuangan/transaksi') ?>" class="btn btn-outline-dark font-weight-bold shadow-sm">
                    <i class="fas fa-wallet mr-1 text-teal"></i> Kas & Bank Operasional
                </a>
            </div>
        </div>

        <!-- Quick Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-6 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border" style="border: 1px solid #b8b8b8 !important;">
                    <span class="info-box-icon bg-warning elevation-0"><i class="fas fa-clock text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Antrean Kasir Menunggu Bayar</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($billings) ?> <small class="text-muted">Pasien</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border" style="border: 1px solid #b8b8b8 !important;">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-receipt text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Transaksi Pembayaran Lunas</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($completedTransactions ?? []) ?> <small class="text-muted">Kwitansi Diterbitkan</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Tabs Card -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-kasir" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-checkout-tab" data-toggle="pill" href="#tab-checkout" role="tab" aria-controls="tab-checkout" aria-selected="true">
                            <i class="fas fa-cash-register text-teal mr-1"></i> 1. PROSES PEMBAYARAN KASIR (ANTREAN TAGIHAN)
                            <?php if (!empty($billings)): ?>
                                <span class="badge badge-warning ml-2"><?= count($billings) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-history-tab" data-toggle="pill" href="#tab-history" role="tab" aria-controls="tab-history" aria-selected="false">
                            <i class="fas fa-history text-info mr-1"></i> 2. DAFTAR RIWAYAT PEMBAYARAN & CETAK DOKUMEN (LUNAS)
                            <?php if (!empty($completedTransactions)): ?>
                                <span class="badge badge-teal ml-2"><?= count($completedTransactions) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white">
                <div class="tab-content" id="custom-tabs-kasirContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: ACTIVE SETTLEMENT WORKSPACE -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-checkout" role="tabpanel" aria-labelledby="tab-checkout-tab">
                        <div class="row">
                            <!-- Left: List of Pending Billings -->
                            <div class="col-md-5">
                                <div class="card card-outline card-secondary shadow-none border" style="border: 1px solid #b8b8b8 !important;">
                                    <div class="card-header bg-light">
                                        <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-list-ul text-teal mr-1"></i> Pilih Antrean Pasien</h3>
                                    </div>
                                    <div class="card-body p-0" style="max-height: 540px; overflow-y: auto;">
                                        <div class="list-group list-group-flush" id="bill-list">
                                            <?php if (empty($billings)): ?>
                                                <div class="text-center text-muted p-5">
                                                    <i class="fas fa-check-double text-success fa-2x mb-2 d-block"></i>
                                                    Tidak ada antrean tagihan yang belum dibayar.
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($billings as $b): ?>
                                                    <a href="#" class="list-group-item list-group-item-action bill-item" 
                                                       data-id="<?= $b->id ?>" 
                                                       data-name="<?= esc($b->patient_name) ?>" 
                                                       data-rm="<?= esc($b->no_rm) ?>" 
                                                       data-visit="<?= esc($b->no_visit) ?>" 
                                                       data-billno="<?= esc($b->billing_no) ?>" 
                                                       data-serv="<?= $b->total_services ?>" 
                                                       data-med="<?= $b->total_medicines ?>" 
                                                       data-resto="<?= $b->total_restaurant ?>"
                                                       data-discount="<?= $b->discount ?>"
                                                       data-grand="<?= $b->grand_total ?>"
                                                       data-paymethod="<?= esc($b->visit_payment_method ?? '') ?>"
                                                       data-insurance="<?= esc($b->insurance_name ?? '') ?>"
                                                       data-tier="<?= esc($b->membership_tier ?? 'regular') ?>">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1 font-weight-bold text-teal"><?= esc($b->patient_name) ?></h6>
                                                            <small class="badge badge-secondary"><?= esc($b->no_rm) ?></small>
                                                        </div>
                                                        <p class="mb-1 text-sm text-secondary">
                                                            No. Billing: <strong><?= esc($b->billing_no) ?></strong>
                                                            <?php if (!empty($b->insurance_name)): ?>
                                                                <span class="badge badge-info ml-1"><i class="fas fa-handshake mr-1"></i><?= esc($b->insurance_name) ?></span>
                                                            <?php endif; ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-secondary"><?= esc($b->no_visit) ?></small>
                                                            <span class="font-weight-bold text-success">Rp <?= number_format($b->grand_total, 0, ',', '.') ?></span>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Settlement Workspace -->
                            <div class="col-md-7">
                                <div class="card card-teal shadow-none border" id="settlement-card" style="display:none; border: 1px solid #b8b8b8 !important;">
                                    <div class="card-header bg-teal d-flex justify-content-between align-items-center">
                                        <h3 class="card-title font-weight-bold text-white mb-0"><i class="fas fa-receipt mr-1"></i> Form Pembayaran Billing Pasien</h3>
                                        <div class="card-tools d-flex align-items-center">
                                            <button type="button" class="btn btn-sm btn-light font-weight-bold text-teal mr-2 shadow-sm" id="btn-call-cashier-voice" title="Panggil Pasien ke Kasir Pembayaran">
                                                <i class="fas fa-volume-high mr-1"></i> Panggil ke Kasir
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger font-weight-bold mr-2 shadow-sm btn-trigger-cancel-bill" title="Batalkan pesanan / tagihan pasien ini">
                                                <i class="fas fa-ban mr-1"></i> Batalkan Pesanan
                                            </button>
                                            <span class="badge badge-light text-teal font-weight-bold px-2 py-1" id="badge-bill-status">SIAP BAYAR</span>
                                        </div>
                                    </div>
                                    <form action="<?= base_url('keuangan/kasir') ?>" method="post" id="form-settle-kasir">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="billing_id" id="modal-billing-id">
                                        <div class="card-body">
                                            <!-- Patient & Billing Identifiers -->
                                            <div class="row border-bottom pb-2 mb-3 text-sm bg-light p-2 rounded" style="border: 1px solid #e2e8f0;">
                                                <div class="col-md-6">
                                                    <span class="text-muted">Nama Pasien:</span> <strong id="lbl-patient" class="text-teal font-weight-bold h6 mb-0 d-inline-block"></strong><br>
                                                    <span class="text-muted">No. Rekam Medis:</span> <span id="lbl-rm" class="badge badge-secondary"></span>
                                                    <div class="mt-1" id="box-patient-meta">
                                                        <span id="lbl-insurance-badge" class="badge badge-info mr-1 font-weight-bold" style="display:none;"><i class="fas fa-handshake mr-1"></i> <span id="val-insurance-name"></span></span>
                                                        <span id="lbl-tier-badge" class="badge badge-warning text-dark font-weight-bold" style="display:none;"><i class="fas fa-crown mr-1"></i> <span id="val-tier-name"></span></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-md-right">
                                                    <span class="text-muted">No. Kwitansi/Billing:</span> <span id="lbl-billno" class="font-weight-bold text-dark"></span><br>
                                                    <span class="text-muted">No. Kunjungan:</span> <span id="lbl-visit" class="font-weight-bold text-secondary"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Detailed Items Table -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="font-weight-bold text-teal mb-0"><i class="fas fa-list-ul mr-1"></i> Rincian Tagihan Layanan, Obat & Resto</h6>
                                                <small class="text-muted" id="lbl-items-count">0 Item</small>
                                            </div>
                                            
                                            <div class="table-responsive mb-3 border rounded">
                                                <table class="table table-bordered table-striped table-sm mb-0" id="details-table" style="font-size: 13px;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th style="width: 30px;" class="text-center">No</th>
                                                            <th>Deskripsi Item</th>
                                                            <th style="width: 90px;" class="text-center">Kategori</th>
                                                            <th style="width: 50px;" class="text-center">Qty</th>
                                                            <th class="text-right" style="width: 105px;">Harga</th>
                                                            <th class="text-right text-danger" style="width: 95px;">Diskon</th>
                                                            <th class="text-right" style="width: 115px;">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="details-table-body">
                                                        <!-- Dynamic rows -->
                                                    </tbody>
                                                    <tfoot class="bg-light font-weight-bold" id="details-table-foot">
                                                        <tr>
                                                            <td colspan="3" class="text-right text-dark">TOTAL RINCIAN:</td>
                                                            <td class="text-center text-dark" id="foot-total-qty">0</td>
                                                            <td class="text-right text-muted" id="foot-total-gross">Rp 0</td>
                                                            <td class="text-right text-danger" id="foot-total-disc">- Rp 0</td>
                                                            <td class="text-right text-teal font-weight-bold" id="foot-total-sub">Rp 0</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <!-- Category Breakdown Badges -->
                                            <div class="d-flex flex-wrap justify-content-between align-items-center p-2 mb-3 bg-white border rounded" style="border: 1px dashed #cbd5e1 !important; font-size: 12px;">
                                                <div>
                                                    <i class="fas fa-stethoscope text-teal mr-1"></i> Layanan Medis: <strong id="lbl-breakdown-serv" class="text-dark">Rp 0</strong>
                                                </div>
                                                <div>
                                                    <i class="fas fa-pills text-info mr-1"></i> Obat Farmasi: <strong id="lbl-breakdown-med" class="text-dark">Rp 0</strong>
                                                </div>
                                                <div>
                                                    <i class="fas fa-utensils text-warning mr-1"></i> Resto Gizi: <strong id="lbl-breakdown-resto" class="text-dark">Rp 0</strong>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold text-dark mb-1"><i class="fas fa-wallet text-teal mr-1"></i> Metode Pembayaran <span class="text-danger">*</span></label>
                                                    <select name="payment_method" id="select-payment-method" class="form-control font-weight-bold text-teal" required>
                                                        <?php if (!empty($paymentMethods)): ?>
                                                            <?php foreach ($paymentMethods as $pm): ?>
                                                                <option value="<?= esc($pm->code) ?>">
                                                                    <?= esc($pm->name) ?> <?= !empty($pm->account_number) ? '(' . esc($pm->account_number) . ')' : '' ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option value="tunai">Tunai / Cash</option>
                                                            <option value="qris">QRIS Mandiri</option>
                                                            <option value="transfer_bca">Transfer Bank BCA</option>
                                                            <option value="debit">Kartu Debit</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold text-dark mb-1"><i class="fas fa-tags text-danger mr-1"></i> Diskon / Potongan Khusus (Rp)</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text font-weight-bold">Rp</span>
                                                        </div>
                                                        <input type="number" name="discount" id="input-discount" class="form-control font-weight-bold text-danger" value="0" min="0">
                                                    </div>
                                                    <!-- Quick Discount Presets -->
                                                    <div class="d-flex mt-1" style="gap: 4px;">
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-discount-preset" data-pct="0">0%</button>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-discount-preset" data-pct="5">5%</button>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-discount-preset" data-pct="10">10%</button>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-discount-preset" data-pct="20">20%</button>
                                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-discount-preset" data-pct="50">50%</button>
                                                        <button type="button" class="btn btn-xs btn-outline-danger btn-discount-preset" data-pct="100">Gratis</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Summary Box: Subtotal, Diskon, Total Tagihan Bersih -->
                                            <div class="card p-3 mb-3 shadow-none border" style="background:#f1f5f9; border: 1.5px solid #cbd5e1 !important; border-radius: 6px;">
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <span class="text-secondary text-sm">Total Subtotal Kotor:</span><br>
                                                        <span class="text-secondary text-sm">Total Potongan Diskon:</span><br>
                                                        <span class="text-dark font-weight-bold h5 mt-2 d-inline-block">Total Tagihan Bersih:</span>
                                                    </div>
                                                    <div class="col-6 text-right">
                                                        <span class="text-secondary font-weight-bold h6 mb-1 d-inline-block" id="lbl-subtotal">Rp 0</span><br>
                                                        <span class="text-danger font-weight-bold h6 mb-1 d-inline-block" id="lbl-discount">- Rp 0</span><br>
                                                        <span class="text-teal h3 font-weight-bold mb-0 d-inline-block" id="lbl-grandtotal">Rp 0</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cash Tendered & Change Calculator Panel -->
                                            <div class="p-3 mb-3 rounded border" id="cash-calculator-panel" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1 !important;">
                                                <label class="font-weight-bold text-dark mb-1">
                                                    <i class="fas fa-money-bill-wave text-success mr-1"></i> Uang Tunai Diterima dari Pasien (Rp): <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group-lg mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold bg-success text-white">Rp</span>
                                                    </div>
                                                    <input type="number" name="paid_amount" id="input-paid-amount" class="form-control form-control-lg font-weight-bold text-success" placeholder="0" min="0" required>
                                                </div>

                                                <!-- Quick Denomination Buttons -->
                                                <div class="d-flex flex-wrap mb-2" style="gap: 5px;">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-quick-money" id="btn-exact-money">Uang Pas</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-quick-money" data-val="50000">Rp 50.000</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-quick-money" data-val="100000">Rp 100.000</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-quick-money" data-val="200000">Rp 200.000</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-quick-money" data-val="500000">Rp 500.000</button>
                                                </div>

                                                <!-- Live Change Display -->
                                                <div class="d-flex justify-content-between align-items-center p-2 rounded" id="box-change-display" style="background: #e2e8f0;">
                                                    <span class="font-weight-bold text-dark">UANG KEMBALIAN:</span>
                                                    <span class="h4 font-weight-bold mb-0 text-success" id="lbl-change-amount">Rp 0</span>
                                                </div>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label class="text-xs font-weight-bold text-muted">Catatan Transaksi / Referensi Kasir (opsional):</label>
                                                <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: No. Approval Debit / Diskon Khusus Karyawan">
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top d-flex align-items-center" style="gap: 10px;">
                                            <button type="button" class="btn btn-outline-danger font-weight-bold btn-lg btn-trigger-cancel-bill" style="min-width: 175px;">
                                                <i class="fas fa-ban mr-1"></i> Batalkan Pesanan
                                            </button>
                                            <button type="submit" id="btn-submit-kasir" class="btn btn-teal btn-block font-weight-bold btn-lg shadow mb-0" style="margin-top: 0 !important;">
                                                <i class="fas fa-check-circle mr-1"></i> Selesaikan Transaksi & Terbitkan Kwitansi
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Welcome Banner when no billing is selected -->
                                <div class="card bg-light p-5 text-center shadow-none border" id="welcome-pane" style="border: 1px solid #b8b8b8 !important;">
                                    <h1 class="display-4 text-teal"><i class="fas fa-cash-register"></i></h1>
                                    <h4 class="text-dark font-weight-bold mt-3">Kasir Pembayaran Rawat Jalan</h4>
                                    <p class="text-secondary mb-0">Pilih salah satu antrean billing pasien di kolom sebelah kiri untuk memproses kasir dan menerbitkan kwitansi.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: COMPLETED PAYMENT HISTORY & PRINT DOCUMENTS -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel" aria-labelledby="tab-history-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-receipt text-teal mr-1"></i> DAFTAR RIWAYAT PEMBAYARAN & KWITANSI LUNAS</h5>
                                <small class="text-muted">Daftar transaksi kasir yang telah selesai dibayar. Anda dapat mencetak kwitansi resmi dan e-tiket obat kapan saja.</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">NO. KWITANSI</th>
                                        <th style="width: 130px;">TGL & WAKTU</th>
                                        <th>PASIEN & NO. RM</th>
                                        <th>POLIKLINIK / TINDAKAN</th>
                                        <th style="width: 120px;" class="text-right">TOTAL BAYAR</th>
                                        <th style="width: 90px;" class="text-center">METODE</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 220px;" class="text-center">AKSI CETAK DOKUMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($completedTransactions)): ?>
                                        <?php foreach ($completedTransactions as $tx): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold">
                                                    <span class="badge badge-teal px-2 py-1" style="font-size: 12px;">
                                                        <?= esc($tx->receipt_no) ?>
                                                    </span>
                                                    <div class="text-muted text-xs mt-1"><?= esc($tx->billing_no) ?></div>
                                                </td>
                                                <td class="text-sm">
                                                    <strong><?= date('d/m/Y', strtotime($tx->created_at)) ?></strong><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($tx->created_at)) ?> WITA</small>
                                                </td>
                                                <td>
                                                    <strong class="text-dark"><?= esc($tx->patient_name ?? 'Pasien Umum') ?></strong><br>
                                                    <small class="badge badge-secondary"><?= esc($tx->no_rm ?? '-') ?></small>
                                                    <small class="text-muted ml-1"><?= esc($tx->no_visit ?? '') ?></small>
                                                </td>
                                                <td class="text-sm">
                                                    <?= esc($tx->poly_name ?: ($tx->tindakan_name ?: 'Pelayanan Medis')) ?><br>
                                                    <small class="text-muted"><i class="fas fa-user-md"></i> <?= esc($tx->doctor_name ?? '-') ?></small>
                                                </td>
                                                <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                                                    Rp <?= number_format($tx->amount, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center text-uppercase font-weight-bold text-xs">
                                                    <span class="badge badge-light border">
                                                        <?= esc($tx->payment_method) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success px-2 py-1">LUNAS</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Print Receipt -->
                                                    <a href="<?= base_url('keuangan/cetak-kwitansi/' . $tx->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold mr-1 mb-1">
                                                        <i class="fas fa-print"></i> Kwitansi
                                                    </a>
                                                    <!-- Print Medicine Label -->
                                                    <?php if (!empty($tx->visit_id)): ?>
                                                        <a href="<?= base_url('apotek/cetak-etiket/' . $tx->visit_id) ?>" target="_blank" class="btn btn-outline-info btn-xs font-weight-bold mr-1 mb-1">
                                                            <i class="fas fa-prescription"></i> E-Tiket
                                                        </a>
                                                    <?php endif; ?>
                                                    <!-- View Details Modal Trigger -->
                                                    <button class="btn btn-outline-secondary btn-xs btn-view-invoice font-weight-bold mr-1 mb-1"
                                                            data-receipt="<?= esc($tx->receipt_no) ?>"
                                                            data-billno="<?= esc($tx->billing_no) ?>"
                                                            data-patient="<?= esc($tx->patient_name) ?>"
                                                            data-rm="<?= esc($tx->no_rm) ?>"
                                                            data-billid="<?= $tx->billing_id ?>"
                                                            data-amount="<?= number_format($tx->amount, 0, ',', '.') ?>"
                                                            data-toggle="modal" data-target="#modalInvoiceDetail">
                                                        <i class="fas fa-file-invoice"></i> Rincian
                                                    </button>
                                                    <!-- Void Payment Button -->
                                                    <button type="button" class="btn btn-outline-danger btn-xs font-weight-bold btn-void-tx mb-1"
                                                            data-id="<?= $tx->id ?>"
                                                            data-receipt="<?= esc($tx->receipt_no) ?>"
                                                            data-billno="<?= esc($tx->billing_no) ?>"
                                                            data-patient="<?= esc($tx->patient_name ?? 'Pasien') ?>"
                                                            data-amount="Rp <?= number_format($tx->amount, 0, ',', '.') ?>"
                                                            title="Void / Batalkan Kwitansi Pembayaran Ini">
                                                        <i class="fas fa-undo"></i> Void
                                                    </button>
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

<!-- Modal Rincian Tagihan Selesai -->
<div class="modal fade" id="modalInvoiceDetail" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="modalInvoiceDetailLabel">
                    <i class="fas fa-file-invoice mr-1"></i> Rincian Tagihan Pembayaran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row bg-light p-2 rounded mb-3 border">
                    <div class="col-6">
                        <strong>No. Kwitansi:</strong> <span id="dtl-receipt" class="text-teal font-weight-bold"></span><br>
                        <strong>No. Billing:</strong> <span id="dtl-billno"></span>
                    </div>
                    <div class="col-6 text-right">
                        <strong>Pasien:</strong> <span id="dtl-patient" class="font-weight-bold"></span><br>
                        <strong>No. RM:</strong> <span id="dtl-rm"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped" id="dtl-items-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Item Layanan / Obat</th>
                                <th style="width: 90px;" class="text-center">Jenis</th>
                                <th style="width: 50px;" class="text-center">Qty</th>
                                <th style="width: 120px;" class="text-right">Harga</th>
                                <th style="width: 130px;" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="text-right font-weight-bold h5 text-dark mt-3">
                    Total Pembayaran: <span class="text-teal" id="dtl-total-amount"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan Pesanan / Tagihan Antrean Kasir -->
<div class="modal fade" id="modalCancelBilling" tabindex="-1" role="dialog" aria-labelledby="modalCancelBillingLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-danger shadow-lg">
            <form action="<?= base_url('keuangan/batal-tagihan') ?>" method="post" id="form-cancel-billing">
                <?= csrf_field() ?>
                <input type="hidden" name="billing_id" id="cancel-billing-id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="modalCancelBillingLabel">
                        <i class="fas fa-ban mr-2"></i> Konfirmasi Pembatalan Pesanan / Tagihan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 px-3 mb-3 border text-sm">
                        <i class="fas fa-triangle-exclamation mr-1 text-danger"></i> <strong>Perhatian Kasir:</strong> Pembatalan pesanan akan menghapus tagihan dari antrean kasir, membatalkan status antrean kunjungan pasien, dan membatalkan resep obat apotek terkait.
                    </div>

                    <div class="bg-light p-3 rounded mb-3 border text-sm">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Nama Pasien:</span>
                            <strong id="cancel-lbl-patient" class="text-dark"></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">No. Rekam Medis:</span>
                            <span id="cancel-lbl-rm" class="badge badge-secondary"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">No. Billing / Kwitansi:</span>
                            <strong id="cancel-lbl-billno" class="text-teal"></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Tagihan:</span>
                            <strong id="cancel-lbl-total" class="text-danger h6 mb-0"></strong>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark mb-1">Pilih Alasan Pembatalan: <span class="text-danger">*</span></label>
                        <select class="form-control" id="select-cancel-preset" required>
                            <option value="Pasien Batal Berobat / Pulang">Pasien Batal Berobat / Pulang</option>
                            <option value="Salah Input Resep / Jasa Tindakan Medis">Salah Input Resep / Jasa Tindakan Medis</option>
                            <option value="Pasien Tidak Membawa Biaya / Menunda Pembayaran">Pasien Tidak Membawa Biaya / Menunda Pembayaran</option>
                            <option value="Duplikasi Data Pendaftaran / Antrean">Duplikasi Data Pendaftaran / Antrean</option>
                            <option value="lainnya">Lainnya (Tulis alasan sendiri...)</option>
                        </select>
                    </div>

                    <div class="form-group mb-0" id="box-custom-cancel-reason" style="display:none;">
                        <label class="text-xs font-weight-bold text-dark">Keterangan Tambahan Alasan:</label>
                        <textarea id="input-cancel-reason-custom" class="form-control" rows="2" placeholder="Jelaskan alasan pembatalan pesanan..."></textarea>
                    </div>
                    <input type="hidden" name="cancel_reason" id="final-cancel-reason" value="Pasien Batal Berobat / Pulang">
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                    <button type="submit" class="btn btn-danger font-weight-bold shadow-sm" id="btn-confirm-cancel-bill">
                        <i class="fas fa-trash-can mr-1"></i> Ya, Batalkan Pesanan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Void / Batalkan Transaksi Kwitansi Selesai -->
<div class="modal fade" id="modalVoidTransaction" tabindex="-1" role="dialog" aria-labelledby="modalVoidTxLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-danger shadow-lg">
            <form action="<?= base_url('keuangan/void-transaksi') ?>" method="post" id="form-void-tx">
                <?= csrf_field() ?>
                <input type="hidden" name="transaction_id" id="void-tx-id">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="modalVoidTxLabel">
                        <i class="fas fa-undo mr-2"></i> Konfirmasi Void Kwitansi Pembayaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger py-2 px-3 mb-3 border text-sm">
                        <i class="fas fa-triangle-exclamation mr-1"></i> <strong>Peringatan Void:</strong> Tindakan ini akan membatalkan kwitansi lunas, mengembalikan saldo kas (jika tunai), dan menerbitkan jurnal pembalik keuangan secara otomatis.
                    </div>

                    <div class="bg-light p-3 rounded mb-3 border text-sm">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">No. Kwitansi:</span>
                            <strong id="void-lbl-receipt" class="text-teal font-weight-bold"></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pasien:</span>
                            <strong id="void-lbl-patient" class="text-dark"></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Dana Dibatalkan:</span>
                            <strong id="void-lbl-amount" class="text-danger h6 mb-0"></strong>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark mb-1">Alasan Void / Pembatalan: <span class="text-danger">*</span></label>
                        <input type="text" name="void_reason" class="form-control" placeholder="Contoh: Pasien retur obat / salah nominal pembayaran" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger font-weight-bold shadow-sm">
                        <i class="fas fa-undo mr-1"></i> Ya, Void Pembayaran Ini
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.billingDetails = <?= json_encode($billingDetails) ?> || {};
    let grossSubtotal = 0;
    let initialItemDiscount = 0;

    // Helper Rupiah Formatter
    function formatRupiah(num) {
        return 'Rp ' + (parseFloat(num) || 0).toLocaleString('id-ID');
    }

    // Render items table, footers, and category breakdown
    function renderBillDetails(id, items, fallbackData) {
        let html = '';
        let totalQty = 0;
        let totalGross = 0;
        let totalItemDisc = 0;
        let totalSub = 0;

        let servTotal = 0;
        let medTotal = 0;
        let restoTotal = 0;

        if (!items || items.length === 0) {
            // Fallback from data attributes if no items in details table
            const serv = fallbackData ? parseFloat(fallbackData.serv) || 0 : 0;
            const med = fallbackData ? parseFloat(fallbackData.med) || 0 : 0;
            const resto = fallbackData ? parseFloat(fallbackData.resto) || 0 : 0;
            const disc = fallbackData ? parseFloat(fallbackData.discount) || 0 : 0;

            servTotal = serv;
            medTotal = med;
            restoTotal = resto;
            totalItemDisc = disc;
            totalGross = serv + med + resto;
            totalSub = Math.max(0, totalGross - totalItemDisc);

            html = `<tr>
                <td colspan="7" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle text-info mr-1"></i> Tagihan umum terdaftar (Menunggu rincian layanan / tagihan paket)
                </td>
            </tr>`;

            $('#details-table-foot').hide();
        } else {
            items.forEach(function(item, idx) {
                const itemPrice = parseFloat(item.price) || 0;
                const itemQty = parseInt(item.qty) || 1;
                const itemDisc = parseFloat(item.discount) || 0;
                const itemGross = itemPrice * itemQty;
                const itemSub = parseFloat(item.subtotal) || Math.max(0, itemGross - itemDisc);

                totalQty += itemQty;
                totalGross += itemGross;
                totalItemDisc += itemDisc;
                totalSub += itemSub;

                if (item.item_type === 'medis') {
                    servTotal += itemGross;
                } else if (item.item_type === 'obat') {
                    medTotal += itemGross;
                } else if (item.item_type === 'resto') {
                    restoTotal += itemGross;
                }

                const badgeColor = item.item_type === 'obat' ? 'info' : (item.item_type === 'medis' ? 'teal' : 'warning');
                const catLabel = item.item_type === 'obat' ? 'OBAT' : (item.item_type === 'medis' ? 'LAYANAN' : 'RESTO');

                html += `
                    <tr>
                        <td class="text-center text-muted">${idx + 1}</td>
                        <td><strong>${item.item_name}</strong></td>
                        <td class="text-center"><span class="badge badge-${badgeColor}">${catLabel}</span></td>
                        <td class="text-center font-weight-bold">${itemQty}</td>
                        <td class="text-right">${formatRupiah(itemPrice)}</td>
                        <td class="text-right font-weight-bold ${itemDisc > 0 ? 'text-danger' : 'text-muted'}">
                            ${itemDisc > 0 ? '- ' + formatRupiah(itemDisc) : '-'}
                        </td>
                        <td class="text-right font-weight-bold text-dark">${formatRupiah(itemSub)}</td>
                    </tr>
                `;
            });

            // Populate Table Footer
            $('#foot-total-qty').text(totalQty);
            $('#foot-total-gross').text(formatRupiah(totalGross));
            $('#foot-total-disc').text(totalItemDisc > 0 ? '- ' + formatRupiah(totalItemDisc) : '- Rp 0');
            $('#foot-total-sub').text(formatRupiah(totalSub));
            $('#details-table-foot').show();
        }

        $('#details-table-body').html(html);
        $('#lbl-items-count').text((items ? items.length : 0) + ' Item');

        // Breakdown Badges
        $('#lbl-breakdown-serv').text(formatRupiah(servTotal));
        $('#lbl-breakdown-med').text(formatRupiah(medTotal));
        $('#lbl-breakdown-resto').text(formatRupiah(restoTotal));

        // Subtotals
        grossSubtotal = totalGross;
        initialItemDiscount = totalItemDisc;

        $('#input-discount').val(totalItemDisc);
        updateTotals();
    }

    // Global Cashier Billing Selector for Static & Real-Time LiveSync Elements
    window.selectCashierBillItem = function($item) {
        if (!$item || !$item.length) return;

        $('.bill-item').removeClass('active');
        $item.addClass('active');

        const id = $item.data('id');
        const name = $item.data('name');
        const rm = $item.data('rm');
        const visit = $item.data('visit');
        const billno = $item.data('billno');
        
        const serv = parseFloat($item.data('serv')) || 0;
        const med = parseFloat($item.data('med')) || 0;
        const resto = parseFloat($item.data('resto')) || 0;
        const discount = parseFloat($item.data('discount')) || 0;
        const grand = parseFloat($item.data('grand')) || 0;

        $('#welcome-pane').hide();
        $('#settlement-card').show();
        $('#modal-billing-id').val(id);
        
        // Labels
        $('#lbl-patient').text(name || 'Pasien');
        $('#lbl-rm').text(rm || '-');
        $('#lbl-billno').text(billno || '-');
        $('#lbl-visit').text(visit || '-');

        // Penjamin & Membership Tier Auto-Fill
        const payMethod = $item.data('paymethod');
        const insName = $item.data('insurance');
        const tier = $item.data('tier');

        if (insName && insName.trim() !== '') {
            $('#val-insurance-name').text('Penjamin: ' + insName);
            $('#lbl-insurance-badge').show();
        } else {
            $('#lbl-insurance-badge').hide();
        }

        if (tier && tier !== 'regular') {
            $('#val-tier-name').text('Member ' + tier.toUpperCase());
            $('#lbl-tier-badge').show();
        } else {
            $('#lbl-tier-badge').hide();
        }

        // Auto-select payment method if matched
        if (payMethod) {
            const $opt = $('#select-payment-method option[value="' + payMethod + '"]');
            if ($opt.length > 0) {
                $('#select-payment-method').val(payMethod).trigger('change');
            }
        }

        const fallbackData = { serv: serv, med: med, resto: resto, discount: discount, grand: grand };

        // 1. Check local cache first
        const cachedItems = (window.billingDetails && window.billingDetails[id]) ? window.billingDetails[id] : null;
        if (cachedItems && cachedItems.length > 0) {
            renderBillDetails(id, cachedItems, fallbackData);
        } else {
            renderBillDetails(id, [], fallbackData);
        }

        // 2. Fetch fresh items from server via AJAX Fallback
        $.ajax({
            url: '<?= base_url('keuangan/billing-details') ?>/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.status === 'success') {
                    if (typeof window.billingDetails === 'undefined') {
                        window.billingDetails = {};
                    }
                    window.billingDetails[id] = res.items || [];
                    renderBillDetails(id, res.items || [], fallbackData);
                }
            }
        });
    };
    
    $(document).ready(function() {
        // Delegated click for static and live-synced cashier bill items
        $(document).on('click', '.bill-item', function(e) {
            e.preventDefault();
            window.selectCashierBillItem($(this));
        });

        // Discount input event
        $('#input-discount').on('input change', function() {
            updateTotals();
        });

        // Discount preset buttons
        $(document).on('click', '.btn-discount-preset', function() {
            const pct = parseFloat($(this).data('pct')) || 0;
            if (pct === 0) {
                $('#input-discount').val(initialItemDiscount);
            } else {
                const calcDisc = Math.round((grossSubtotal * pct) / 100);
                $('#input-discount').val(calcDisc);
            }
            updateTotals();
        });

        // Paid amount input event
        $('#input-paid-amount').on('input change', function() {
            calculateChange();
        });

        // Payment method change event
        $('#select-payment-method').on('change', function() {
            const method = $(this).val().toLowerCase();
            const currentDiscount = parseFloat($('#input-discount').val()) || 0;
            const grandTotal = Math.max(0, grossSubtotal - currentDiscount);

            if (method === 'tunai' || method === 'cash') {
                $('#cash-calculator-panel').slideDown(150);
                const currentPaid = parseFloat($('#input-paid-amount').val()) || 0;
                if (currentPaid === 0 || isNaN(currentPaid)) {
                    $('#input-paid-amount').val(grandTotal);
                }
                calculateChange();
            } else {
                // Non-cash automatically sets paid amount = grandTotal
                $('#cash-calculator-panel').slideDown(150);
                $('#input-paid-amount').val(grandTotal);
                calculateChange();
            }
        });

        // Quick Denominations Buttons
        $('#btn-exact-money').click(function() {
            const currentDiscount = parseFloat($('#input-discount').val()) || 0;
            const grandTotal = Math.max(0, grossSubtotal - currentDiscount);
            $('#input-paid-amount').val(grandTotal);
            calculateChange();
        });

        $('.btn-quick-money[data-val]').click(function() {
            const val = parseFloat($(this).data('val'));
            $('#input-paid-amount').val(val);
            calculateChange();
        });

        window.updateTotals = function() {
            const currentDiscount = parseFloat($('#input-discount').val()) || 0;
            const grandTotal = Math.max(0, grossSubtotal - currentDiscount);

            $('#lbl-subtotal').text(formatRupiah(grossSubtotal));
            $('#lbl-discount').text('- ' + formatRupiah(currentDiscount));
            $('#lbl-grandtotal').text(formatRupiah(grandTotal));

            const method = $('#select-payment-method').val() ? $('#select-payment-method').val().toLowerCase() : 'tunai';
            if (method !== 'tunai' && method !== 'cash') {
                $('#input-paid-amount').val(grandTotal);
            } else {
                const paidVal = parseFloat($('#input-paid-amount').val());
                if (isNaN(paidVal) || paidVal === 0) {
                    $('#input-paid-amount').val(grandTotal > 0 ? grandTotal : 0);
                }
            }

            calculateChange();
        };

        window.calculateChange = function() {
            const currentDiscount = parseFloat($('#input-discount').val()) || 0;
            const grandTotal = Math.max(0, grossSubtotal - currentDiscount);
            const paid = parseFloat($('#input-paid-amount').val()) || 0;
            const change = paid - grandTotal;

            const changeBox = $('#box-change-display');
            const changeLbl = $('#lbl-change-amount');

            if (change < 0) {
                changeBox.css('background', '#fee2e2');
                changeLbl.removeClass('text-success text-info').addClass('text-danger').text('- ' + formatRupiah(Math.abs(change)) + ' (Kurang Bayar)');
            } else if (change === 0) {
                changeBox.css('background', '#dcfce7');
                changeLbl.removeClass('text-danger text-info').addClass('text-success').text(formatRupiah(0) + ' (Lunas Pas)');
            } else {
                changeBox.css('background', '#dcfce7');
                changeLbl.removeClass('text-danger text-info').addClass('text-success').text(formatRupiah(change) + ' (Uang Kembalian)');
            }
        };

        // Form Submit Validation & Anti-Double Submit
        $('#form-settle-kasir').on('submit', function(e) {
            const method = $('#select-payment-method').val() ? $('#select-payment-method').val().toLowerCase() : 'tunai';
            const currentDiscount = parseFloat($('#input-discount').val()) || 0;
            const grandTotal = Math.max(0, grossSubtotal - currentDiscount);
            const paid = parseFloat($('#input-paid-amount').val()) || 0;

            if ((method === 'tunai' || method === 'cash') && paid < grandTotal) {
                e.preventDefault();
                alert('Perhatian: Uang tunai yang diterima (' + formatRupiah(paid) + ') kurang dari total tagihan bersih (' + formatRupiah(grandTotal) + '). Silakan periksa kembali nominal pembayaran.');
                $('#input-paid-amount').focus();
                return false;
            }

            // Disable submit button with spinner to prevent double submits
            const $btn = $('#btn-submit-kasir');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Pelunasan & Menerbitkan Kwitansi...');
            return true;
        });

        // View invoice detail modal handler (delegated)
        $(document).on('click', '.btn-view-invoice', function() {
            const receipt = $(this).data('receipt');
            const billno = $(this).data('billno');
            const patient = $(this).data('patient');
            const rm = $(this).data('rm');
            const billid = $(this).data('billid');
            const amount = $(this).data('amount');

            $('#dtl-receipt').text(receipt);
            $('#dtl-billno').text(billno);
            $('#dtl-patient').text(patient);
            $('#dtl-rm').text(rm);
            $('#dtl-total-amount').text(formatRupiah(amount));

            const items = (window.billingDetails && window.billingDetails[billid]) ? window.billingDetails[billid] : [];
            let html = '';
            items.forEach(function(item) {
                const badgeColor = item.item_type === 'obat' ? 'info' : (item.item_type === 'medis' ? 'teal' : 'warning');
                html += `
                    <tr>
                        <td><strong>${item.item_name}</strong></td>
                        <td class="text-center"><span class="badge badge-${badgeColor}">${item.item_type.toUpperCase()}</span></td>
                        <td class="text-center font-weight-bold">${item.qty}</td>
                        <td class="text-right">${formatRupiah(item.price)}</td>
                        <td class="text-right font-weight-bold">${formatRupiah(item.subtotal)}</td>
                    </tr>
                `;
            });
            $('#dtl-items-table tbody').html(html);
        });

        // Event Tombol Panggil Suara Kasir
        $('#btn-call-cashier-voice').on('click', function(e) {
            e.preventDefault();
            const pName = $('#lbl-patient').text().trim() || 'Pasien';
            const qNo = $('#lbl-visit').text().trim() || 'A-001';

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'kasir',
                    counter_name: 'Kasir Pembayaran',
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: 1
                }, function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> dipanggil ke Kasir Pembayaran.', '📢 Panggilan Kasir');
                    }
                });
            } else if (typeof callPatientToCashier === 'function') {
                callPatientToCashier(qNo, pName);
            }
        });

        // =========================================================================
        // HANDLER PEMBATALAN PESANAN / TAGIHAN KASIR
        // =========================================================================
        $(document).on('click', '.btn-trigger-cancel-bill', function(e) {
            e.preventDefault();
            const billingId = $('#modal-billing-id').val();
            const patientName = $('#lbl-patient').text().trim() || 'Pasien';
            const rmNo = $('#lbl-rm').text().trim() || '-';
            const billNo = $('#lbl-billno').text().trim() || '-';
            const grandTotal = $('#lbl-grandtotal').text().trim() || 'Rp 0';

            if (!billingId) {
                alert('Pilih salah satu tagihan pasien terlebih dahulu.');
                return;
            }

            $('#cancel-billing-id').val(billingId);
            $('#cancel-lbl-patient').text(patientName);
            $('#cancel-lbl-rm').text(rmNo);
            $('#cancel-lbl-billno').text(billNo);
            $('#cancel-lbl-total').text(grandTotal);

            // Reset selection
            $('#select-cancel-preset').val('Pasien Batal Berobat / Pulang');
            $('#box-custom-cancel-reason').hide();
            $('#input-cancel-reason-custom').val('');
            $('#final-cancel-reason').val('Pasien Batal Berobat / Pulang');

            $('#modalCancelBilling').modal('show');
        });

        $('#select-cancel-preset').on('change', function() {
            const val = $(this).val();
            if (val === 'lainnya') {
                $('#box-custom-cancel-reason').slideDown(150);
                $('#input-cancel-reason-custom').focus();
                $('#final-cancel-reason').val($('#input-cancel-reason-custom').val() || 'Dibatalkan oleh kasir');
            } else {
                $('#box-custom-cancel-reason').slideUp(150);
                $('#final-cancel-reason').val(val);
            }
        });

        $('#input-cancel-reason-custom').on('input change', function() {
            $('#final-cancel-reason').val($(this).val().trim() || 'Dibatalkan oleh kasir');
        });

        $('#form-cancel-billing').on('submit', function(e) {
            const preset = $('#select-cancel-preset').val();
            if (preset === 'lainnya' && !$('#input-cancel-reason-custom').val().trim()) {
                e.preventDefault();
                alert('Silakan tuliskan keterangan alasan pembatalan pesanan.');
                $('#input-cancel-reason-custom').focus();
                return false;
            }

            $('#btn-confirm-cancel-bill').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Membatalkan Pesanan...');
            return true;
        });

        // =========================================================================
        // HANDLER VOID / PEMBATALAN KWITANSI LUNAS (TAB 2)
        // =========================================================================
        $(document).on('click', '.btn-void-tx', function(e) {
            e.preventDefault();
            const txId = $(this).data('id');
            const receipt = $(this).data('receipt');
            const patient = $(this).data('patient');
            const amount = $(this).data('amount');

            $('#void-tx-id').val(txId);
            $('#void-lbl-receipt').text(receipt);
            $('#void-lbl-patient').text(patient);
            $('#void-lbl-amount').text(amount);

            $('#modalVoidTransaction').modal('show');
        });

        $('#form-void-tx').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Void Transaksi...');
            return true;
        });
        <?php 
            $vTrigger = session()->getFlashdata('voice_trigger');
            if (!empty($vTrigger) && is_array($vTrigger)): 
                $qNumber = $vTrigger['queue_number'] ?? 'A-001';
                $pName   = $vTrigger['patient_name'] ?? 'Pasien';
                $vAction = $vTrigger['action'] ?? 'to_pharmacy';
        ?>
            setTimeout(function() {
                const qNo = <?= json_encode($qNumber) ?>;
                const pName = <?= json_encode($pName) ?>;
                const action = <?= json_encode($vAction) ?>;

                function executeVoiceCall() {
                    try {
                        if (window.VCM) {
                            window.VCM.unlockAudio();
                            if (action === 'to_pharmacy') {
                                window.VCM.callToPharmacy(qNo, pName);
                            } else if (action === 'to_completed') {
                                window.VCM.callCompleted(qNo, pName);
                            }
                        } else if (action === 'to_pharmacy' && typeof callPatientToPharmacy === 'function') {
                            callPatientToPharmacy(qNo, pName);
                        } else if (action === 'to_completed' && typeof callPatientCompleted === 'function') {
                            callPatientCompleted(qNo, pName);
                        } else if ('speechSynthesis' in window) {
                            if (window.speechSynthesis.paused) {
                                window.speechSynthesis.resume();
                            }
                            const spokenQ = qNo.replace(/-/g, ' ');
                            let speechText = '';
                            if (action === 'to_pharmacy') {
                                speechText = 'Nomor antrean ' + spokenQ + ', atas nama ' + pName + ', pembayaran kasir telah selesai. Silakan menuju ke Loket Farmasi dan Apotek untuk pengambilan obat. Terima kasih.';
                            } else {
                                speechText = 'Nomor antrean ' + spokenQ + ', atas nama ' + pName + ', pembayaran dan pelayanan telah selesai. Terima kasih banyak atas kunjungan Anda di Sawamawa Medical Center.';
                            }
                            const utt = new SpeechSynthesisUtterance(speechText);
                            utt.lang = 'id-ID';
                            utt.rate = 0.85;
                            window.speechSynthesis.speak(utt);
                        }
                    } catch(e) {
                        console.warn('[Kasir Voice] Execution error:', e);
                    }
                }

                executeVoiceCall();

                // Visual Toastr Notification with Re-Play Button
                if (typeof toastr !== 'undefined') {
                    toastr.info(
                        '📢 <strong>Panggilan Otomatis:</strong> Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> telah dipanggil.<br><button class="btn btn-xs btn-light mt-1 font-weight-bold" id="btn-replay-cashier-voice"><i class="fas fa-volume-high mr-1"></i> Putar Ulang Panggilan</button>',
                        'Panggilan Suara Kasir',
                        { timeOut: 10000, closeButton: true }
                    );

                    $(document).on('click', '#btn-replay-cashier-voice', function(e) {
                        e.preventDefault();
                        executeVoiceCall();
                    });
                }
            }, 300);
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
