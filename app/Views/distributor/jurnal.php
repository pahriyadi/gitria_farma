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
    --dist-subhead-bg: #f1f5f9;
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
    --dist-subhead-bg: #21262d;
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

.journal-header-row td {
    background-color: var(--dist-subhead-bg) !important;
    color: var(--dist-text) !important;
    font-weight: 700;
}

.journal-line-row td {
    background-color: var(--dist-card-bg) !important;
    color: var(--dist-text) !important;
}

.journal-balance-row td {
    background-color: var(--dist-head-bg) !important;
    font-size: 11.5px;
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
</style>

<!-- HEADER TOOLBAR -->
<div class="row align-items-center mb-3">
    <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
        <h4 class="m-0 font-weight-bold" style="color: var(--dist-text);">
            <i class="fas fa-book-journal-whills text-indigo mr-2"></i> Jurnal Umum Transaksi Distributor
        </h4>
        <small style="color: var(--dist-muted);">Daftar Jurnal Pembukuan Double-Entry Otomatis Khusus Unit Bisnis Distributor &amp; Grosir</small>
    </div>
    <div class="col-lg-6 col-md-12 text-lg-right">
        <div class="d-inline-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 8px;">
            <a href="<?= base_url('accounting/jurnal?scope=all') ?>" class="btn btn-dark btn-sm font-weight-bold shadow-sm" title="Lihat Gabungan Seluruh Transaksi Klinik + Distributor">
                <i class="fas fa-layer-group mr-1 text-warning"></i> 📊 Gabungkan ke Jurnal Umum
            </a>
            <a href="<?= base_url('distributor/kas') ?>" class="btn btn-outline-success btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-wallet mr-1"></i> Buku Kas
            </a>
            <a href="<?= base_url('distributor/laporan') ?>" class="btn btn-outline-indigo btn-sm font-weight-bold shadow-sm" style="color:#4f46e5; border-color:#c7d2fe;">
                <i class="fas fa-chart-pie mr-1"></i> Laba Rugi Grosir
            </a>
            <a href="<?= base_url('accounting/coa') ?>" class="btn btn-light border btn-sm font-weight-bold shadow-sm" title="Kelola Master COA Terpusat">
                <i class="fas fa-sitemap mr-1 text-teal"></i> Master COA
            </a>
        </div>
    </div>
</div>

<!-- INFO BANNER PEMISAHAN & KONSOLIDASI JURNAL -->
<div class="card dist-card mb-3" style="border-left: 4px solid #4f46e5 !important;">
    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="fas fa-info-circle fa-2x mr-3" style="color: #4f46e5;"></i>
            <div>
                <strong class="d-block" style="color: var(--dist-text);">Pencatatan Jurnal Mandiri Distributor (B2B)</strong>
                <span class="text-xs" style="color: var(--dist-muted);">Jurnal di halaman ini hanya mencatat transaksi unit grosir &amp; distributor. Untuk melihat pembukuan gabungan (Klinik + Grosir), klik tombol konsolidasi.</span>
            </div>
        </div>
        <a href="<?= base_url('accounting/jurnal?scope=all') ?>" class="btn btn-indigo btn-sm font-weight-bold text-nowrap shadow-sm" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
            <i class="fas fa-layer-group mr-1 text-warning"></i> Buka Jurnal Konsolidasi
        </a>
    </div>
</div>

<!-- FILTER CARD -->
<div class="card dist-card mb-3">
    <div class="card-body p-3">
        <form method="get" action="<?= base_url('distributor/jurnal') ?>" class="row align-items-end">
            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Mulai Tanggal:</label>
                <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold dist-input" value="<?= esc($startDate) ?>">
            </div>
            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold dist-input" value="<?= esc($endDate) ?>">
            </div>
            <div class="col-lg-4 col-md-4 col-12 mb-2 mb-md-0">
                <label class="text-xs font-weight-bold mb-1" style="color: var(--dist-text);">Cari Keterangan / No. Jurnal:</label>
                <input type="text" name="search" class="form-control form-control-sm font-weight-bold dist-input" placeholder="Ketik kata kunci..." value="<?= esc($search ?? '') ?>">
            </div>
            <div class="col-lg-2 col-12 text-right">
                <button type="submit" class="btn btn-indigo btn-sm font-weight-bold px-3 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="<?= base_url('distributor/jurnal') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-rotate-left mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- DAFTAR JURNAL ENTRIES -->
<div class="card dist-card shadow-sm border-0 mb-4">
    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center" style="background-color: var(--dist-head-bg); border-color: var(--dist-border) !important;">
        <h6 class="font-weight-bold mb-0" style="color: var(--dist-text);">
            <i class="fas fa-list-check text-indigo mr-1"></i> Transaksi Jurnal Distributor (<?= count($journals) ?> Jurnal)
        </h6>
        <span class="badge badge-light border font-weight-bold text-muted font-monospace">
            <?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-dist mb-0 text-sm" id="tableJournals">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 18%;">No. Jurnal &amp; Modul</th>
                        <th>Keterangan / Rincian Akun Double-Entry</th>
                        <th style="width: 15%;" class="text-right">Debit (Rp)</th>
                        <th style="width: 15%;" class="text-right">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($journals)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color: var(--dist-muted);">
                                <i class="fas fa-book-open fa-3x mb-2 text-secondary opacity-50 d-block"></i>
                                Tidak ada catatan jurnal distributor pada filter yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($journals as $idx => $j): 
                            $lines = $detailsByJournal[$j->id] ?? [];
                            $totalDeb = 0;
                            $totalKred = 0;
                        ?>
                            <tr class="journal-header-row">
                                <td><?= $idx + 1 ?></td>
                                <td><?= date('d/m/Y', strtotime($j->entry_date)) ?></td>
                                <td>
                                    <span class="badge badge-indigo text-white font-monospace" style="background:#4f46e5;"><?= esc($j->journal_no) ?></span>
                                    <small class="d-block text-muted mt-0.5"><?= esc($j->source_module) ?></small>
                                </td>
                                <td colspan="3">
                                    <i class="fas fa-file-lines text-muted mr-1"></i> <?= esc($j->description) ?>
                                </td>
                            </tr>

                            <!-- DETAIL ROWS DEBIT & KREDIT -->
                            <?php foreach ($lines as $l): 
                                $totalDeb += (float)$l->debit;
                                $totalKred += (float)$l->credit;
                                $isCredit = (float)$l->credit > 0;
                            ?>
                                <tr class="journal-line-row">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="<?= $isCredit ? 'padding-left: 35px;' : '' ?>">
                                        <span class="font-monospace font-weight-bold text-xs badge badge-light border mr-1"><?= esc($l->acc_code) ?></span>
                                        <span class="<?= $isCredit ? 'text-secondary' : 'font-weight-bold' ?>" style="color: var(--dist-text);"><?= esc($l->acc_name) ?></span>
                                    </td>
                                    <td class="text-right font-weight-bold text-success font-monospace">
                                        <?= (float)$l->debit > 0 ? number_format($l->debit, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-right font-weight-bold text-danger font-monospace">
                                        <?= (float)$l->credit > 0 ? number_format($l->credit, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <!-- BALANCE CHECK ROW -->
                            <tr class="journal-balance-row">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right text-muted font-weight-bold">
                                    Total Balancing:
                                </td>
                                <td class="text-right font-weight-bold text-success font-monospace">
                                    Rp <?= number_format($totalDeb, 0, ',', '.') ?>
                                </td>
                                <td class="text-right font-weight-bold text-danger font-monospace">
                                    Rp <?= number_format($totalKred, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
