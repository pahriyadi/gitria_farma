<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Kolom Kiri: Form Setup Saldo Awal Seluruh Akun -->
    <div class="col-md-8">
        <div class="card p-0 mb-3">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-coins text-teal mr-1"></i> Form Pengaturan Saldo Awal Pembukuan Sistem
                    </h5>
                    <small class="text-secondary d-block">
                        Masukkan saldo kas di tangan, rekening bank operasional, persediaan awal, dan modal awal ketika sistem pertama kali mulai digunakan.
                    </small>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-outline-warning btn-sm font-weight-bold mr-2" onclick="clearAllInputs()" title="Setel semua input nominal menjadi 0">
                        <i class="fas fa-eraser mr-1"></i> Bersihkan Input ke 0
                    </button>
                    <a href="<?= base_url('accounting/reset-saldo-awal') ?>" class="btn btn-outline-danger btn-sm font-weight-bold mr-2" onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS / MERESET seluruh saldo awal sistem kembali menjadi Rp 0,00? Jurnal saldo awal lama akan dihapus.');" title="Hapus semua saldo awal di database">
                        <i class="fas fa-trash-can mr-1"></i> Hapus &amp; Reset Semua Saldo ke 0
                    </a>
                    <a href="<?= base_url('accounting/coa') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
                        <i class="fas fa-list mr-1"></i> Master COA
                    </a>
                </div>
            </div>

            <form action="<?= base_url('accounting/save-saldo-awal') ?>" method="post" id="form-saldo-awal">
                <?= csrf_field() ?>
                <div class="card-body p-4">
                    
                    <div class="alert alert-info alert-excel text-xs mb-4">
                        <i class="fas fa-circle-info mr-1"></i> <strong>Panduan Setup Saldo Awal:</strong>
                        Pastikan Anda mengisi nominal saldo kas fisik di kasir, saldo tabungan rekening bank klinik, estimasi nilai persediaan obat/barang yang ada, serta modal awal disetor. Sistem akan secara otomatis membukukan jurnal penyeimbang (Opening Balance Journal) agar neraca akuntansi Anda selalu 100% seimbang (Balance).
                    </div>

                    <!-- KELOMPOK 1: AKTIVA / ASSET (KAS, BANK, PERSEDIAAN & ASET) -->
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                        <h6 class="font-weight-bold text-teal mb-0">
                            <i class="fas fa-wallet mr-1"></i> 1. Saldo Kas, Bank, Stok Obat &amp; Aset (Kekayaan Klinik)
                        </h6>
                        <small class="text-muted">Uang tunai kasir, saldo rekening bank, nilai stok obat, &amp; aset fisik</small>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 120px;">Kode Akun</th>
                                    <th>Nama Rekening Akun</th>
                                    <th style="width: 120px;">Kategori</th>
                                    <th style="width: 220px;" class="text-right">Nominal Saldo Awal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($grouped['asset'])): ?>
                                    <?php foreach ($grouped['asset'] as $ast): ?>
                                        <tr>
                                            <td class="font-monospace font-weight-bold text-teal"><?= esc($ast->code) ?></td>
                                            <td>
                                                <strong><?= esc($ast->name) ?></strong>
                                                <?php if ($ast->id == 1): ?>
                                                    <span class="badge badge-success ml-1" style="font-size: 9px;">Laci Kasir Utama</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-light border text-uppercase" style="font-size: 9.5px;"><?= esc($ast->type) ?></span></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" step="any" min="0" name="balances[<?= $ast->id ?>]" class="form-control text-right font-weight-bold input-asset-calc" value="<?= (float)$ast->balance ?>" onkeyup="recalculateOpeningTotals()" onchange="recalculateOpeningTotals()">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- KELOMPOK 2: KEWAJIBAN / HUTANG (LIABILITIES) -->
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                        <h6 class="font-weight-bold text-danger mb-0">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> 2. Saldo Hutang Usaha &amp; Kewajiban
                        </h6>
                        <small class="text-muted">Sisa tagihan obat dari PBF/supplier atau hutang lainnya yang belum lunas</small>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 120px;">Kode Akun</th>
                                    <th>Nama Rekening Akun</th>
                                    <th style="width: 120px;">Kategori</th>
                                    <th style="width: 220px;" class="text-right">Nominal Saldo Awal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($grouped['liability'])): ?>
                                    <?php foreach ($grouped['liability'] as $lia): ?>
                                        <tr>
                                            <td class="font-monospace font-weight-bold text-danger"><?= esc($lia->code) ?></td>
                                            <td><strong><?= esc($lia->name) ?></strong></td>
                                            <td><span class="badge badge-light border text-uppercase" style="font-size: 9.5px;"><?= esc($lia->type) ?></span></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" step="any" min="0" name="balances[<?= $lia->id ?>]" class="form-control text-right font-weight-bold input-liability-calc" value="<?= (float)$lia->balance ?>" onkeyup="recalculateOpeningTotals()" onchange="recalculateOpeningTotals()">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-muted text-center text-xs">Belum ada akun kewajiban terdaftar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- KELOMPOK 3: EKUITAS & MODAL (EQUITY) -->
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                        <h6 class="font-weight-bold text-primary mb-0">
                            <i class="fas fa-landmark mr-1"></i> 3. Modal Awal Disetor Pemilik (Ekuitas)
                        </h6>
                        <small class="text-muted">Total modal uang/aset yang ditanamkan pemilik untuk mendirikan klinik</small>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 120px;">Kode Akun</th>
                                    <th>Nama Rekening Akun</th>
                                    <th style="width: 120px;">Kategori</th>
                                    <th style="width: 220px;" class="text-right">Nominal Saldo Awal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($grouped['equity'])): ?>
                                    <?php foreach ($grouped['equity'] as $eq): ?>
                                        <tr>
                                            <td class="font-monospace font-weight-bold text-dark"><?= esc($eq->code) ?></td>
                                            <td><strong><?= esc($eq->name) ?></strong></td>
                                            <td><span class="badge badge-light border text-uppercase" style="font-size: 9.5px;"><?= esc($eq->type) ?></span></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" step="any" min="0" name="balances[<?= $eq->id ?>]" class="form-control text-right font-weight-bold input-equity-calc" value="<?= (float)$eq->balance ?>" onkeyup="recalculateOpeningTotals()" onchange="recalculateOpeningTotals()">
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Catatan / Keterangan Pembukuan Saldo Awal:</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Cut-off pembukuan saldo awal faskes per 1 Januari 2026 / Peresmian Operasional." value="Setup Saldo Awal Pembukuan Sistem">
                    </div>

                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center p-3">
                    <div>
                        <span class="text-secondary text-xs">
                            <i class="fas fa-shield-alt text-teal mr-1"></i> Data diverifikasi otomatis dan dicatat ke Audit Trail.
                        </span>
                    </div>
                    <button type="submit" class="btn btn-teal font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Simpan & Terapkan Saldo Awal Sistem
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Status Keseimbangan & Riwayat Saldo Awal -->
    <div class="col-md-4">
        <!-- Card Ringkasan Keseimbangan (Balance Checker) -->
        <div class="card p-3 mb-3">
            <h6 class="font-weight-bold text-dark mb-2 pb-2 border-bottom">
                <i class="fas fa-scale-balanced text-teal mr-1"></i> Keseimbangan Neraca Saldo Awal
            </h6>
            
            <div class="d-flex justify-content-between text-xs py-1 border-bottom">
                <span class="text-muted">Total Aktiva (Debet):</span>
                <strong class="text-teal" id="sum-total-debit">Rp <?= number_format($totalDebit, 2, ',', '.') ?></strong>
            </div>
            <div class="d-flex justify-content-between text-xs py-1 border-bottom">
                <span class="text-muted">Total Pasiva (Kredit):</span>
                <strong class="text-dark" id="sum-total-credit">Rp <?= number_format($totalCredit, 2, ',', '.') ?></strong>
            </div>
            <div class="d-flex justify-content-between text-xs py-2">
                <span class="text-muted">Status Keseimbangan:</span>
                <span id="balance-status-badge">
                    <?php if (abs($totalDebit - $totalCredit) < 0.01): ?>
                        <span class="badge badge-success font-weight-bold"><i class="fas fa-check mr-1"></i>SEIMBANG (BALANCE)</span>
                    <?php else: ?>
                        <span class="badge badge-warning font-weight-bold"><i class="fas fa-magic mr-1"></i>Auto-Balance Aktif</span>
                    <?php endif; ?>
                </span>
            </div>

            <div class="alert alert-success alert-excel text-xs mt-2 mb-0" style="background:#f0fdf4; border-color:#bbf7d0;">
                <i class="fas fa-check-circle mr-1"></i> <strong>Smart Auto-Balance:</strong> Jika Anda belum mengisi modal, sistem secara cerdas menyeimbangkan selisih aset ke akun <strong>Modal Awal Disetor</strong> secara otomatis.
            </div>
        </div>

        <!-- Card Riwayat Jurnal Saldo Awal -->
        <div class="card p-3">
            <h6 class="font-weight-bold text-dark mb-2 pb-2 border-bottom">
                <i class="fas fa-history text-teal mr-1"></i> Riwayat Jurnal Saldo Awal
            </h6>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php if (!empty($openingJournals)): ?>
                    <ul class="list-unstyled mb-0" style="font-size: 11.5px;">
                        <?php foreach ($openingJournals as $oj): ?>
                            <li class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-teal font-monospace"><?= esc($oj->journal_no) ?></strong>
                                    <span class="text-muted"><?= date('d/m/Y', strtotime($oj->entry_date)) ?></span>
                                </div>
                                <div class="text-dark mt-1"><?= esc($oj->description) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-xs text-center my-3">Belum ada riwayat pembukuan saldo awal sebelumnya.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function clearAllInputs() {
        if (confirm('Apakah Anda ingin mengosongkan seluruh kolom input form ini menjadi 0?')) {
            $('.input-asset-calc, .input-liability-calc, .input-equity-calc').val(0);
            recalculateOpeningTotals();
        }
    }

    function recalculateOpeningTotals() {
        let totalAsset = 0;
        let totalLiaEq = 0;

        $('.input-asset-calc').each(function() {
            totalAsset += parseFloat($(this).val()) || 0;
        });

        $('.input-liability-calc, .input-equity-calc').each(function() {
            totalLiaEq += parseFloat($(this).val()) || 0;
        });

        $('#sum-total-debit').text('Rp ' + totalAsset.toLocaleString('id-ID', {minimumFractionDigits: 2}));
        $('#sum-total-credit').text('Rp ' + totalLiaEq.toLocaleString('id-ID', {minimumFractionDigits: 2}));

        if (Math.abs(totalAsset - totalLiaEq) < 0.01) {
            $('#balance-status-badge').html('<span class="badge badge-success font-weight-bold"><i class="fas fa-check mr-1"></i>SEIMBANG (BALANCE)</span>');
        } else {
            $('#balance-status-badge').html('<span class="badge badge-warning font-weight-bold"><i class="fas fa-magic mr-1"></i>Auto-Balance Aktif</span>');
        }
    }

    $(document).ready(function() {
        // Auto-select nominal saat fokus agar mudah diedit / ditimpa
        $('.input-asset-calc, .input-liability-calc, .input-equity-calc').on('focus', function() {
            $(this).select();
        });
    });
</script>
<?= $this->endSection() ?>
