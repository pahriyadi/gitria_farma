<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
/* CSS Variabel Distributor Paper White / Paper Dark */
:root {
    --dist-bg: #ffffff;
    --dist-text: #1f2937;
    --dist-muted: #6b7280;
    --dist-border: #b8b8b8;
    --dist-card-bg: #ffffff;
    --dist-head-bg: #f8fafc;
    --dist-hover-bg: #f1f5f9;
    --dist-input-bg: #ffffff;
}

body.dark-mode {
    --dist-bg: #14171a;
    --dist-text: #f0f6fc;
    --dist-muted: #9ca3af;
    --dist-border: #374151;
    --dist-card-bg: #1f2328;
    --dist-head-bg: #181b1f;
    --dist-hover-bg: #272c33;
    --dist-input-bg: #181b1f;
}

.dist-card {
    background-color: var(--dist-card-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    border-radius: 8px;
}

.table-dist {
    width: 100%;
    border-collapse: collapse !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
    background-color: var(--dist-card-bg) !important;
}

.table-dist th,
.table-dist td {
    border: 1px solid var(--dist-border) !important;
    padding: 8px 10px;
    vertical-align: middle;
}

.table-dist thead th {
    background-color: var(--dist-head-bg) !important;
    color: var(--dist-text) !important;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-dist tbody tr:hover {
    background-color: var(--dist-hover-bg) !important;
}

.dist-input {
    background-color: var(--dist-input-bg) !important;
    border: 1px solid var(--dist-border) !important;
    color: var(--dist-text) !important;
}

.dist-input:focus {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25) !important;
}

body.dark-mode .modal-content {
    background-color: var(--dist-card-bg) !important;
    color: var(--dist-text) !important;
    border: 1px solid var(--dist-border) !important;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: var(--dist-border) !important;
    background-color: var(--dist-head-bg) !important;
}
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--dist-text);">
            <i class="fas fa-wallet text-indigo mr-2"></i> Buku Kas &amp; Arus Kas Distributor
        </h4>
        <small style="color: var(--dist-muted);">Pencatatan Keuangan, Mutasi Kas Tunai &amp; Bank Khusus Unit Distributor Mandiri</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 8px;">
            <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" id="btnKasMasuk" onclick="openModalKas('in')">
                <i class="fas fa-arrow-down-left mr-1"></i> + Kas Masuk
            </button>
            <button type="button" class="btn btn-danger btn-sm font-weight-bold shadow-sm" id="btnKasKeluar" onclick="openModalKas('out')">
                <i class="fas fa-arrow-up-right mr-1"></i> - Kas Keluar / Biaya
            </button>
            <a href="<?= base_url('distributor/jurnal') ?>" class="btn btn-outline-indigo btn-sm font-weight-bold shadow-sm" style="color:#4f46e5; border-color:#c7d2fe;">
                <i class="fas fa-book-journal-whills mr-1"></i> Jurnal B2B
            </a>
            <a href="<?= base_url('accounting/jurnal?scope=all') ?>" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm" title="Lihat Pembukuan Jurnal Umum Terpusat">
                <i class="fas fa-layer-group mr-1"></i> Jurnal Umum Konsolidasi
            </a>
        </div>
    </div>
</div>

<!-- RINGKASAN SALDO KAS -->
<div class="row mb-3">
    <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
        <div class="card shadow-xs border-0" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: #fff;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-uppercase" style="opacity: 0.85;">Total Saldo Kas Distributor</span>
                        <h4 class="font-weight-bold mt-1 mb-0">Rp <?= number_format($totalSaldo, 0, ',', '.') ?></h4>
                        <small class="text-xs" style="opacity: 0.8;"><i class="fas fa-shield-halved mr-1"></i> Kas Tunai + Bank Operasional</small>
                    </div>
                    <div style="font-size: 32px; opacity: 0.25;">
                        <i class="fas fa-vault"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
        <div class="card dist-card shadow-xs" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-muted text-uppercase">Kas Tunai Distributor (1131)</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-success">Rp <?= number_format($saldoKasTunai, 0, ',', '.') ?></h4>
                        <small class="text-muted"><i class="fas fa-money-bill-wave mr-1"></i> Kas Fisik Brankas B2B</small>
                    </div>
                    <div style="font-size: 28px; color: #10b981; opacity: 0.25;">
                        <i class="fas fa-money-bill-1-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-12">
        <div class="card dist-card shadow-xs" style="border-left: 4px solid #0284c7 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs font-weight-bold text-muted text-uppercase">Bank Penerimaan B2B (1132)</span>
                        <h4 class="font-weight-bold mt-1 mb-0 text-info">Rp <?= number_format($saldoKasBank, 0, ',', '.') ?></h4>
                        <small class="text-muted"><i class="fas fa-building-columns mr-1"></i> Rekening Penerimaan Grosir</small>
                    </div>
                    <div style="font-size: 28px; color: #0284c7; opacity: 0.25;">
                        <i class="fas fa-building-columns"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER & DATA MUTASI -->
<div class="card dist-card shadow-sm border-0 mb-4">
    <div class="card-header border-bottom py-3" style="background-color: var(--dist-head-bg); border-color: var(--dist-border) !important;">
        <form method="get" action="<?= base_url('distributor/kas') ?>" class="row align-items-end">
            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Mulai Tanggal:</label>
                <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold dist-input" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold dist-input" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Akun Kas:</label>
                <select name="account_type" class="form-control form-control-sm font-weight-bold dist-input" data-native="true">
                    <option value="all" <?= ($accountType ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Akun Kas (Tunai &amp; Bank)</option>
                    <option value="cash" <?= ($accountType ?? '') === 'cash' ? 'selected' : '' ?>>Kas Tunai Distributor (1131)</option>
                    <option value="bank" <?= ($accountType ?? '') === 'bank' ? 'selected' : '' ?>>Bank Distributor (1132)</option>
                </select>
            </div>
            <div class="col-lg-3 col-12 text-right">
                <button type="submit" class="btn btn-indigo btn-sm font-weight-bold px-3 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
                    <i class="fas fa-filter mr-1"></i> Terapkan Filter
                </button>
                <a href="<?= base_url('distributor/kas') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-rotate-left mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- STATS PERIODE IN / OUT -->
        <div class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-bottom text-xs" style="background-color: var(--dist-head-bg); border-color: var(--dist-border) !important;">
            <div>
                <span class="mr-3" style="color: var(--dist-muted);">Periode: <strong><?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?></strong></span>
                <span style="color: var(--dist-muted);">Total Mutasi: <strong><?= count($mutasiList) ?> Baris</strong></span>
            </div>
            <div>
                <span class="text-success font-weight-bold mr-3"><i class="fas fa-arrow-down mr-1"></i> Total Masuk: Rp <?= number_format($totalMasuk, 0, ',', '.') ?></span>
                <span class="text-danger font-weight-bold"><i class="fas fa-arrow-up mr-1"></i> Total Keluar: Rp <?= number_format($totalKeluar, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="table-mutasi-kas">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 15%;">No. Jurnal</th>
                        <th style="width: 18%;">Akun Kas</th>
                        <th>Keterangan / Transaksi</th>
                        <th style="width: 13%;" class="text-right text-success">Masuk (Debit)</th>
                        <th style="width: 13%;" class="text-right text-danger">Keluar (Kredit)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mutasiList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color: var(--dist-muted);">
                                <i class="fas fa-inbox fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                Belum ada mutasi transaksi kas distributor pada periode tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($mutasiList as $idx => $m): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="font-weight-bold"><?= date('d/m/Y', strtotime($m->entry_date)) ?></td>
                                <td>
                                    <span class="badge badge-light border font-weight-bold text-dark font-monospace"><?= esc($m->journal_no) ?></span>
                                    <small class="d-block text-muted text-xs"><?= esc($m->source_module) ?></small>
                                </td>
                                <td>
                                    <strong class="d-block" style="color: var(--dist-text);"><?= esc($m->acc_name) ?></strong>
                                    <small class="text-muted font-monospace"><?= esc($m->acc_code) ?></small>
                                </td>
                                <td>
                                    <span><?= esc($m->description) ?></span>
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    <?= (float)$m->debit > 0 ? 'Rp ' . number_format($m->debit, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    <?= (float)$m->credit > 0 ? 'Rp ' . number_format($m->credit, 0, ',', '.') : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CATAT KAS MASUK / KAS KELUAR DISTRIBUTOR -->
<div class="modal fade" id="modalKasDistributor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white" id="modalKasHeader" style="background: #4f46e5;">
                <h5 class="modal-title font-weight-bold" id="modalKasTitle">
                    <i class="fas fa-money-bill-transfer mr-2"></i> Catat Kas Masuk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKasDistributor">
                <input type="hidden" name="type" id="kas_type" value="in">
                <div class="modal-body p-4">
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase" style="color: var(--dist-text);">Tanggal Transaksi: <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control form-control-sm font-weight-bold dist-input" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase" style="color: var(--dist-text);">Akun Kas Distributor (Target): <span class="text-danger">*</span></label>
                        <select name="account_target" class="form-control form-control-sm font-weight-bold dist-input" data-native="true" required>
                            <option value="1131">1131 - Kas Tunai Distributor (Fisik / Brankas)</option>
                            <option value="1132">1132 - Bank Penerimaan Distributor (B2B)</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase" style="color: var(--dist-text);" id="labelOpposingAcc">Pilih Akun Sumber / Kategori: <span class="text-danger">*</span></label>
                        <select name="opposing_account_id" id="opposing_account_id" class="form-control form-control-sm font-weight-bold dist-input" data-native="true" style="width: 100%;" required>
                            <!-- Populated via JS based on type (in / out) -->
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-xs text-uppercase" style="color: var(--dist-text);">Nominal Transaksi (Rp): <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold dist-input">Rp</span>
                            </div>
                            <input type="number" name="amount" id="kas_amount" class="form-control font-weight-bold dist-input text-right" placeholder="0" min="1" step="any" required>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-xs text-uppercase" style="color: var(--dist-text);">Keterangan / Catatan: <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control dist-input" rows="2" placeholder="Contoh: Biaya bensin & tol pengiriman DO #INV-001..." required></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-weight-bold btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-indigo font-weight-bold btn-sm px-4 shadow-sm" id="btnSubmitKas" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
                        <i class="fas fa-save mr-1"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const expenseAccounts = <?= json_encode($expenseAccounts ?? []) ?>;
const revenueAccounts = <?= json_encode($revenueAccounts ?? []) ?>;

function openModalKas(type) {
    $('#kas_type').val(type);
    const selectOpp = $('#opposing_account_id');
    selectOpp.empty();

    if (type === 'in') {
        $('#modalKasHeader').css('background', '#10b981');
        $('#modalKasTitle').html('<i class="fas fa-arrow-down-left mr-2"></i> Catat Kas Masuk Distributor');
        $('#labelOpposingAcc').text('Pilih Akun Sumber Penerimaan / Modal:');
        $('#btnSubmitKas').css({'background': '#10b981', 'border-color': '#10b981'});
        
        selectOpp.append('<option value="">-- Pilih Akun Penerimaan / Modal --</option>');
        revenueAccounts.forEach(acc => {
            selectOpp.append(`<option value="${acc.id}">${acc.code} - ${acc.name} (${acc.type})</option>`);
        });
    } else {
        $('#modalKasHeader').css('background', '#dc2626');
        $('#modalKasTitle').html('<i class="fas fa-arrow-up-right mr-2"></i> Catat Kas Keluar / Biaya Distributor');
        $('#labelOpposingAcc').text('Pilih Akun Beban / Keperluan Pengeluaran:');
        $('#btnSubmitKas').css({'background': '#dc2626', 'border-color': '#dc2626'});

        selectOpp.append('<option value="">-- Pilih Akun Beban / Biaya --</option>');
        expenseAccounts.forEach(acc => {
            selectOpp.append(`<option value="${acc.id}">${acc.code} - ${acc.name} (${acc.type})</option>`);
        });
    }

    $('#formKasDistributor')[0].reset();
    $('#kas_type').val(type);
    $('#modalKasDistributor').modal('show');
}

$(document).ready(function() {
    $('#formKasDistributor').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#btnSubmitKas');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

        $.ajax({
            url: '<?= base_url('distributor/simpan-kas') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Transaksi');
                if (res.status === 'success') {
                    $('#modalKasDistributor').modal('hide');
                    alert(res.message || 'Transaksi kas berhasil disimpan');
                    window.location.reload();
                } else {
                    alert(res.message || 'Gagal menyimpan transaksi');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Transaksi');
                alert('Terjadi kesalahan sistem saat menyimpan kas.');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
