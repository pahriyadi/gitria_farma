<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                <h4 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-book-journal-whills text-indigo mr-2"></i> Jurnal Umum Transaksi Distributor
                </h4>
                <small class="text-muted">Daftar Jurnal Pembukuan Double-Entry Otomatis Khusus Unit Bisnis Distributor &amp; Grosir</small>
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
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- INFO BANNER PEMISAHAN & KONSOLIDASI JURNAL -->
        <div class="alert bg-white border shadow-xs d-flex align-items-center justify-content-between p-3 mb-3" style="border-left: 4px solid #4f46e5 !important; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-indigo fa-2x mr-3" style="color: #4f46e5;"></i>
                <div>
                    <strong class="text-dark d-block">Pencatatan Jurnal Mandiri Distributor (B2B)</strong>
                    <span class="text-xs text-muted">Jurnal di halaman ini hanya mencatat transaksi unit grosir &amp; distributor. Untuk melihat pembukuan gabungan (Klinik + Grosir), klik tombol di samping.</span>
                </div>
            </div>
            <a href="<?= base_url('accounting/jurnal?scope=all') ?>" class="btn btn-indigo btn-sm font-weight-bold text-nowrap ml-3 shadow-xs" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
                <i class="fas fa-layer-group mr-1 text-warning"></i> Buka Jurnal Konsolidasi
            </a>
        </div>

        <!-- FILTER CARD -->
        <div class="card shadow-xs border mb-3" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form method="get" action="<?= base_url('distributor/jurnal') ?>" class="row align-items-end">
                    <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                        <label class="text-xs font-weight-bold text-muted mb-1">Mulai Tanggal:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($startDate) ?>">
                    </div>
                    <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                        <label class="text-xs font-weight-bold text-muted mb-1">Sampai Tanggal:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm font-weight-bold" value="<?= esc($endDate) ?>">
                    </div>
                    <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                        <label class="text-xs font-weight-bold text-muted mb-1">Cari Keterangan / No. Jurnal:</label>
                        <input type="text" name="search" class="form-control form-control-sm font-weight-bold" placeholder="Ketik kata kunci..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-lg-3 col-12 text-right">
                        <button type="submit" class="btn btn-indigo btn-sm font-weight-bold px-3" style="background:#4f46e5; border-color:#4f46e5; color:#fff;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('distributor/jurnal') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
                            <i class="fas fa-rotate-left mr-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- DAFTAR JURNAL ENTRIES -->
        <div class="card shadow-xs border" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-list-check text-indigo mr-1"></i> Transaksi Jurnal Distributor (<?= count($journals) ?> Jurnal)
                </h6>
                <span class="badge badge-light border font-weight-bold text-muted font-monospace">
                    <?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 12%;">Tanggal</th>
                                <th style="width: 16%;">No. Jurnal &amp; Modul</th>
                                <th>Keterangan / Rincian Akun Double-Entry</th>
                                <th style="width: 14%;" class="text-right">Debit (Rp)</th>
                                <th style="width: 14%;" class="text-right">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($journals)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-book-open fa-3x mb-2 text-secondary d-block"></i>
                                        Tidak ada catatan jurnal distributor pada filter yang dipilih.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($journals as $idx => $j): 
                                    $lines = $detailsByJournal[$j->id] ?? [];
                                    $totalDeb = 0;
                                    $totalKred = 0;
                                ?>
                                    <tr class="table-light font-weight-bold" style="border-top: 2px solid #cbd5e1;">
                                        <td><?= $idx + 1 ?></td>
                                        <td><?= date('d/m/Y', strtotime($j->entry_date)) ?></td>
                                        <td>
                                            <span class="badge badge-indigo text-white font-monospace" style="background:#4f46e5;"><?= esc($j->journal_no) ?></span>
                                            <small class="d-block text-muted mt-0.5"><?= esc($j->source_module) ?></small>
                                        </td>
                                        <td colspan="3" class="text-dark">
                                            <i class="fas fa-file-lines text-muted mr-1"></i> <?= esc($j->description) ?>
                                        </td>
                                    </tr>

                                    <!-- DETAIL ROWS DEBIT & KREDIT -->
                                    <?php foreach ($lines as $l): 
                                        $totalDeb += (float)$l->debit;
                                        $totalKred += (float)$l->credit;
                                        $isCredit = (float)$l->credit > 0;
                                    ?>
                                        <tr style="background: #ffffff;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="<?= $isCredit ? 'padding-left: 35px;' : '' ?>">
                                                <span class="font-monospace font-weight-bold text-xs badge badge-light border mr-1"><?= esc($l->acc_code) ?></span>
                                                <span class="<?= $isCredit ? 'text-secondary' : 'text-dark font-weight-bold' ?>"><?= esc($l->acc_name) ?></span>
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
                                    <tr style="background: #f8fafc; font-size: 11.5px;">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right text-muted font-weight-bold">
                                            Total Balancing:
                                        </td>
                                        <td class="text-right font-weight-bold text-success font-monospace border-top">
                                            Rp <?= number_format($totalDeb, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-danger font-monospace border-top">
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

    </div>
</div>
<?= $this->endSection() ?>
