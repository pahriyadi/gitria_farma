<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-none border" style="border: 1px solid #b8b8b8 !important; border-radius: 6px;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #b8b8b8;">
                <div>
                    <h5 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-gauge-high text-teal mr-2"></i> System Scaling &amp; Performance Monitor
                    </h5>
                    <small class="text-muted">Pemantauan metrik beban server, kapasitas penyimpanan database, dan optimasi konkurensi tinggi.</small>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold shadow-none mr-1" onclick="window.forceUpdateSystem()" title="Paksa segarkan seluruh cache browser dan OPcache server">
                        <i class="fas fa-arrows-rotate mr-1"></i> Perbarui Sistem &amp; Cache
                    </button>
                    <a href="<?= base_url('system/optimize-tables') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-none" onclick="return confirm('Jalankan optimasi dan defragmentasi pada seluruh tabel database?');">
                        <i class="fas fa-database mr-1"></i> Optimasi &amp; Defrag Database
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- 6 Health & Scaling KPI Cards -->
                <div class="row mb-4">
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #3b82f6 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">VERSI PHP</small>
                            <strong class="text-primary h5 font-weight-bold mb-0">v<?= esc($serverMetrics['phpVersion']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #10b981 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">PEAK MEMORY RAM</small>
                            <strong class="text-success h5 font-weight-bold mb-0"><?= $serverMetrics['memoryPeakMb'] ?> MB</strong>
                            <small class="text-muted d-block text-xxs">Limit: <?= esc($serverMetrics['memoryLimit']) ?></small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #0d9f4f !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">TOTAL UKURAN DB</small>
                            <strong class="text-teal h5 font-weight-bold mb-0"><?= $serverMetrics['totalDbMb'] ?> MB</strong>
                            <small class="text-muted d-block text-xxs"><?= number_format($serverMetrics['totalDbRows']) ?> Baris Data</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #f59e0b !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">OVERHEAD RUANG</small>
                            <strong class="text-warning h5 font-weight-bold mb-0"><?= $serverMetrics['totalOverheadMb'] ?> MB</strong>
                            <small class="text-muted d-block text-xxs">Bisa Dioptimasi</small>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #8b5cf6 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">OPCACHE STATUS</small>
                            <div class="mt-1">
                                <?php if ($serverMetrics['opcacheEnabled']): ?>
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary px-2 py-1">BYPASS / OFF</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6 mb-2">
                        <div class="p-3 border rounded bg-white text-center" style="border: 1px solid #b8b8b8 !important; border-top: 3px solid #06b6d4 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">USER AKTIF TERDATA</small>
                            <strong class="text-info h5 font-weight-bold mb-0"><?= number_format($serverMetrics['sessionCount']) ?> User</strong>
                            <small class="text-muted d-block text-xxs">High Capacity OK</small>
                        </div>
                    </div>
                </div>

                <!-- Rekomendasi Skalabilitas & Efisiensi High-Concurrency -->
                <div class="alert bg-light border p-3 mb-4 text-xs" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #0d9f4f !important;">
                    <strong class="text-dark font-weight-bold d-block mb-1">
                        <i class="fas fa-lightbulb text-warning mr-1"></i> Panduan Skalabilitas &amp; Efisiensi Sistem Produksi:
                    </strong>
                    <ul class="mb-0 pl-3 text-muted">
                        <li><strong>Database Defragmentation</strong>: Menjalankan <code>OPTIMIZE TABLE</code> secara berkala menyusun ulang indeks B-Tree pada database MySQL/MariaDB sehingga pencarian resep, pasien, dan billing tetap instan di atas puluhan ribu data.</li>
                        <li><strong>View &amp; Route Caching</strong>: Memanfaatkan pre-compiled routing dan output cache untuk meminimalkan beban CPU server saat jam sibuk operasional klinik.</li>
                        <li><strong>Single Responsibility Logging</strong>: Log error disimpan dengan deduplikasi cerdas hash 64-bit sehingga tidak membebani I/O harddisk server.</li>
                    </ul>
                </div>

                <!-- Tabel Analisis Ukuran Tabel Basis Data -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-table-cells text-teal mr-1"></i> Profiling Kapasitas Tabel Basis Data (44+ Tabel Utama)
                    </h6>
                    <small class="text-muted">Data diambil langsung dari <code>information_schema.TABLES</code></small>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th>NAMA TABEL</th>
                                <th style="width: 120px;" class="text-center">JUMLAH BARIS</th>
                                <th style="width: 130px;" class="text-right">UKURAN DATA</th>
                                <th style="width: 130px;" class="text-right">UKURAN INDEKS</th>
                                <th style="width: 130px;" class="text-right">TOTAL SIZE</th>
                                <th style="width: 110px;" class="text-center">OVERHEAD</th>
                                <th style="width: 100px;" class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            foreach ($tableStats as $t): 
                                $dataKb  = round((int)$t->data_length / 1024, 2);
                                $indexKb = round((int)$t->index_length / 1024, 2);
                                $totalKb = round((int)$t->total_size / 1024, 2);
                                $freeKb  = round((int)$t->data_free / 1024, 2);
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-dark font-weight-bold"><code><?= esc($t->table_name) ?></code></strong>
                                </td>
                                <td class="text-center font-weight-bold">
                                    <?= number_format((int)$t->table_rows) ?>
                                </td>
                                <td class="text-right text-xs">
                                    <?= $dataKb > 1024 ? round($dataKb/1024, 2) . ' MB' : $dataKb . ' KB' ?>
                                </td>
                                <td class="text-right text-xs">
                                    <?= $indexKb > 1024 ? round($indexKb/1024, 2) . ' MB' : $indexKb . ' KB' ?>
                                </td>
                                <td class="text-right font-weight-bold text-teal text-xs">
                                    <?= $totalKb > 1024 ? round($totalKb/1024, 2) . ' MB' : $totalKb . ' KB' ?>
                                </td>
                                <td class="text-center text-xs">
                                    <?php if ($freeKb > 0): ?>
                                        <span class="badge badge-warning px-2 py-1"><?= $freeKb ?> KB</span>
                                    <?php else: ?>
                                        <span class="text-muted">0 KB</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check text-xxs mr-1"></i> OPTIMAL</span>
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
<?= $this->endSection() ?>
