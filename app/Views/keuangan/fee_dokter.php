<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Rekap Fee Dokter Paper White / Paper Dark */
:root {
    --fee-bg: #ffffff;
    --fee-text: #1f2937;
    --fee-muted: #6b7280;
    --fee-border: #b8b8b8;
    --fee-card-bg: #ffffff;
    --fee-head-bg: #f8fafc;
    --fee-hover-bg: #f1f5f9;
    --fee-input-bg: #ffffff;
}

body.dark-mode {
    --fee-bg: #14171a;
    --fee-text: #f0f6fc;
    --fee-muted: #9ca3af;
    --fee-border: #374151;
    --fee-card-bg: #1f2328;
    --fee-head-bg: #181b1f;
    --fee-hover-bg: #272c33;
    --fee-input-bg: #181b1f;
}

.fee-card {
    background-color: var(--fee-card-bg) !important;
    border: 1px solid var(--fee-border) !important;
    color: var(--fee-text) !important;
    border-radius: 8px;
}

.table-fee {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--fee-border) !important;
    color: var(--fee-text) !important;
    background-color: var(--fee-card-bg) !important;
}

.table-fee th,
.table-fee td {
    border: 1px solid var(--fee-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-fee thead th {
    background-color: var(--fee-head-bg) !important;
    color: var(--fee-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-fee tbody tr:hover {
    background-color: var(--fee-hover-bg) !important;
}

.fee-input {
    background-color: var(--fee-input-bg) !important;
    border: 1px solid var(--fee-border) !important;
    color: var(--fee-text) !important;
}

.fee-input:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25) !important;
}

body.dark-mode .modal-content {
    background-color: var(--fee-card-bg) !important;
    color: var(--fee-text) !important;
    border: 1px solid var(--fee-border) !important;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: var(--fee-border) !important;
    background-color: var(--fee-head-bg) !important;
}
</style>

<!-- HEADER ROW -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--fee-text);">
            <i class="fas fa-user-md text-teal mr-2"></i> Rekapitulasi &amp; Fee Dokter
        </h4>
        <small style="color: var(--fee-muted);">Perhitungan bagi hasil dokter dari tindakan poliklinik &amp; fee resep apotek (Akun 241) serta pencatatan otomatis ke Jurnal Akuntansi</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 6px;">
            <a href="<?= base_url('system/master-klinik#doctor') ?>" class="btn btn-outline-teal btn-sm font-weight-bold shadow-sm" title="Kelola Tarif Jasa Medis &amp; Persentase Fee Dokter di Master Data">
                <i class="fas fa-cog mr-1"></i> Pengaturan Tarif Fee
            </a>
            <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" id="btnOpenSettlement" data-toggle="modal" data-target="#modalSettlement">
                <i class="fas fa-money-bill-wave mr-1"></i> Proses Pembayaran Fee
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card fee-card shadow-sm mb-4">
            <div class="card-body p-4">
                
                <!-- Summary Fee Per Dokter -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0" style="color: var(--fee-text);"><i class="fas fa-stethoscope text-teal mr-1"></i> Ringkasan Akumulasi Hak Jasa Medis &amp; Resep Dokter</h6>
                        <span class="badge badge-light border text-secondary font-weight-bold">Status: Akumulasi Real-Time</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table-fee mb-0 text-sm" id="tableSummaryFee">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">NO</th>
                                    <th>NAMA DOKTER</th>
                                    <th>POLIKLINIK</th>
                                    <th class="text-right">FEE POLI (KONSULTASI)</th>
                                    <th class="text-right">FEE RESEP APOTEK (5%)</th>
                                    <th class="text-right">TOTAL HAK DOKTER</th>
                                    <th class="text-right">SUDAH DIBAYAR</th>
                                    <th class="text-right font-weight-bold">SISA BELUM DIBAYAR</th>
                                    <th style="width: 140px;" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($unpaidDoctors)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4" style="color: var(--fee-muted);">
                                            <i class="fas fa-check-circle text-success mr-1"></i> Seluruh jasa medis &amp; fee resep dokter telah diselesaikan (tidak ada tunggakan fee).
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $no = 1; 
                                    foreach ($unpaidDoctors as $ud): 
                                    ?>
                                        <tr>
                                            <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                            <td>
                                                <strong style="color: var(--fee-text); font-size: 14px;"><?= esc($ud->doctor_name) ?></strong><br>
                                                <small style="color: var(--fee-muted);">Tarif Poli: Rp <?= number_format($ud->fee_per_pasien, 0, ',', '.') ?> / pasien</small>
                                            </td>
                                            <td><span class="badge badge-light border"><?= esc($ud->poly_name ?? 'Poli Umum') ?></span></td>
                                            <td class="text-right">
                                                <strong style="color: var(--fee-text);">Rp <?= number_format($ud->medical_earned, 0, ',', '.') ?></strong><br>
                                                <small style="color: var(--fee-muted);">(<?= $ud->patient_count ?> Pasien)</small>
                                            </td>
                                            <td class="text-right">
                                                <strong class="text-info">Rp <?= number_format($ud->pharmacy_fee, 0, ',', '.') ?></strong><br>
                                                <small style="color: var(--fee-muted);">(<?= $ud->rx_count ?> Lembar Resep)</small>
                                            </td>
                                            <td class="text-right font-weight-bold" style="font-size: 14px; color: var(--fee-text);">
                                                Rp <?= number_format($ud->gross_earned, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right" style="color: var(--fee-muted);">
                                                Rp <?= number_format($ud->total_paid, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-right font-weight-bold <?= $ud->unpaid_balance > 0 ? 'text-danger' : 'text-success' ?>" style="font-size: 14px;">
                                                Rp <?= number_format($ud->unpaid_balance, 0, ',', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($ud->unpaid_balance > 0): ?>
                                                    <button type="button" class="btn btn-teal btn-xs btn-bayar-dokter font-weight-bold shadow-sm" 
                                                            data-id="<?= $ud->doctor_id ?>" 
                                                            data-name="<?= esc($ud->doctor_name) ?>" 
                                                            data-count="<?= $ud->patient_count + $ud->rx_count ?>" 
                                                            data-medical="<?= $ud->medical_earned ?>" 
                                                            data-pharmacy="<?= $ud->pharmacy_fee ?>" 
                                                            data-amount="<?= $ud->unpaid_balance ?>">
                                                        <i class="fas fa-credit-card mr-1"></i> Bayarkan Fee
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Lunas</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr style="border-color: var(--fee-border); margin: 25px 0;">

                <!-- Riwayat Settlement Yang Sudah Dibayarkan -->
                <h6 class="font-weight-bold mb-3" style="color: var(--fee-text);"><i class="fas fa-history text-teal mr-1"></i> Riwayat Pembayaran (Settlement) Fee Dokter Resmi</h6>
                <div class="table-responsive">
                    <table class="table-fee mb-0 datatable text-sm" id="tableSettlements">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">NO</th>
                                <th>NO. SETTLEMENT</th>
                                <th>NAMA DOKTER</th>
                                <th>PERIODE PEMBAYARAN</th>
                                <th class="text-center">TOTAL TINDAKAN/RESEP</th>
                                <th class="text-right">TOTAL NOMINAL DIBAYAR</th>
                                <th>METODE BAYAR</th>
                                <th>TANGGAL &amp; JURNAL REF</th>
                                <th style="width: 90px;" class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            foreach ($settlements as $s): 
                            ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td class="font-weight-bold text-xs"><code><?= esc($s->settlement_no) ?></code></td>
                                    <td><strong><?= esc($s->doctor_name) ?></strong></td>
                                    <td class="text-center text-xs"><?= date('d/m/Y', strtotime($s->period_start)) ?> s/d <?= date('d/m/Y', strtotime($s->period_end)) ?></td>
                                    <td class="text-center font-weight-bold text-teal"><?= $s->total_actions ?> Item</td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></td>
                                    <td><span class="badge badge-light border"><?= esc($s->payment_method) ?></span></td>
                                    <td class="text-xs">
                                        <?= date('d/m/Y H:i', strtotime($s->paid_at ?? $s->created_at)) ?><br>
                                        <?php if ($s->journal_id): ?>
                                            <span class="badge badge-light border text-primary">Jurnal #<?= $s->journal_id ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success px-2 py-1"><?= strtoupper($s->status) ?></span>
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

<!-- Modal Form Bayar Fee Dokter -->
<div class="modal fade" id="modalSettlement" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= base_url('keuangan/bayar-fee-dokter') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-teal text-white py-2">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-dollar mr-1"></i> Form Pembayaran Jasa Medis &amp; Fee Resep Dokter</h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Pilih Dokter Spesialis / Umum <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="modal-sel-doctor" class="form-control form-control-sm fee-input font-weight-bold" data-native="true" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php foreach ($allDoctors as $doc): ?>
                                <option value="<?= $doc->id ?>" data-name="<?= esc($doc->name) ?>"><?= esc($doc->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group mb-2">
                            <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Periode Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="period_start" class="form-control form-control-sm fee-input" value="<?= date('Y-m-01') ?>" required>
                        </div>
                        <div class="col-6 form-group mb-2">
                            <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Periode Sampai <span class="text-danger">*</span></label>
                            <input type="date" name="period_end" class="form-control form-control-sm fee-input" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group mb-2">
                            <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Porsi Jasa Medis Poli (Rp)</label>
                            <input type="number" step="0.01" name="medical_portion" id="modal-medical-portion" class="form-control form-control-sm font-weight-bold fee-input" placeholder="0" value="0">
                        </div>
                        <div class="col-6 form-group mb-2">
                            <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Porsi Fee Resep Apotek 241 (Rp)</label>
                            <input type="number" step="0.01" name="pharmacy_portion" id="modal-pharmacy-portion" class="form-control form-control-sm font-weight-bold text-info fee-input" placeholder="0" value="0">
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Total Tindakan &amp; Resep</label>
                        <input type="number" name="total_actions" id="modal-total-actions" class="form-control form-control-sm font-weight-bold fee-input" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Nominal Total Yang Dibayarkan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_amount" id="modal-total-amount" class="form-control form-control-sm font-weight-bold text-success fee-input" placeholder="0" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Metode Pembayaran Kas/Bank <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control form-control-sm fee-input" data-native="true" required>
                            <option value="Transfer Bank">Transfer Bank (BCA / Mandiri / BRI / NTB Syariah)</option>
                            <option value="Kas Tunai">Kas Tunai Kasir Utama</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold" style="color: var(--fee-text);">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control form-control-sm fee-input" rows="2" placeholder="Contoh: Honorarium tindakan dokter spesialis &amp; jasa resep periode bulan ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-check-circle mr-1"></i> Simpan &amp; Buat Jurnal Akuntansi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-bayar-dokter').click(function() {
        const docId = $(this).data('id');
        const count = $(this).data('count');
        const amount = $(this).data('amount');
        const medical = $(this).data('medical') || 0;
        const pharmacy = $(this).data('pharmacy') || 0;

        $('#modal-sel-doctor').val(docId).trigger('change');
        $('#modal-total-actions').val(count);
        $('#modal-medical-portion').val(medical);
        $('#modal-pharmacy-portion').val(pharmacy);
        $('#modal-total-amount').val(amount);

        $('#modalSettlement').modal('show');
    });

    // Recalculate total if portions change
    $('#modal-medical-portion, #modal-pharmacy-portion').on('input', function() {
        const med = parseFloat($('#modal-medical-portion').val()) || 0;
        const rx  = parseFloat($('#modal-pharmacy-portion').val()) || 0;
        if (med > 0 || rx > 0) {
            $('#modal-total-amount').val((med + rx).toFixed(2));
        }
    });
});
</script>
<?= $this->endSection() ?>
