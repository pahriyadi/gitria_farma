<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-none" style="border: 1px solid #b8b8b8 !important; background: #ffffff !important; border-radius: 4px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-book-medical text-teal mr-1"></i> BUKU KARTU STOK OBAT & ALKES (STOCK CARD LEDGER)
                    </h5>
                    <small class="text-muted">Histori mutasi keluar-masuk barang, sisa saldo berjalan (Running Balance), dan nomor batch</small>
                </div>
                <div>
                    <a href="<?= base_url('apotek/stok') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Stok Master
                    </a>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Cetak Kartu Stok
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Filter Section -->
                <form action="<?= base_url('apotek/kartu-stok') ?>" method="get" class="mb-4 p-3 bg-light border rounded">
                    <div class="row align-items-end">
                        <div class="col-md-5 form-group mb-md-0">
                            <label class="text-xs font-weight-bold text-dark">Pilih Obat / Alkes <span class="text-danger">*</span></label>
                            <select name="medicine_id" class="form-control form-control-sm select-searchable font-weight-bold" data-search-placeholder="🔍 Cari nama obat / alkes..." onchange="this.form.submit();" required>
                                <option value="">-- Pilih Obat / Alkes --</option>
                                <?php foreach ($medicines as $m): ?>
                                    <option value="<?= $m->id ?>" <?= ($selectedMedicine && $selectedMedicine->id == $m->id) ? 'selected' : '' ?>>
                                        [<?= esc($m->code) ?>] <?= esc($m->name) ?> (Stok Saat Ini: <?= (int)($m->stock ?? 0) ?> <?= esc($m->unit) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-md-0">
                            <label class="text-xs font-weight-bold text-dark">Periode Mulai</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>">
                        </div>
                        <div class="col-md-3 form-group mb-md-0">
                            <label class="text-xs font-weight-bold text-dark">Periode Sampai</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>">
                        </div>
                        <div class="col-md-1 form-group mb-md-0">
                            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <?php if ($selectedMedicine): ?>
                    <!-- Ringkasan Profil Obat Terpilih -->
                    <div class="card bg-white border mb-4" style="border: 1px solid #b8b8b8 !important;">
                        <div class="card-body p-3">
                            <div class="row text-center">
                                <div class="col-md-3 border-right">
                                    <small class="text-muted d-block text-xs">KODE & NAMA OBAT</small>
                                    <strong class="text-dark" style="font-size: 15px;"><?= esc($selectedMedicine->code) ?> - <?= esc($selectedMedicine->name) ?></strong>
                                </div>
                                <div class="col-md-3 border-right">
                                    <small class="text-muted d-block text-xs">SATUAN & KATEGORI</small>
                                    <strong class="text-dark" style="font-size: 14px;"><?= esc($selectedMedicine->unit) ?> (<?= esc($selectedMedicine->category ?? 'Umum') ?>)</strong>
                                </div>
                                <div class="col-md-3 border-right">
                                    <small class="text-muted d-block text-xs">TOTAL MASUK / KELUAR</small>
                                    <strong class="text-teal" style="font-size: 14px;">+<?= $totalIn ?></strong> / <strong class="text-danger">-<?= $totalOut ?></strong> <?= esc($selectedMedicine->unit) ?>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block text-xs">SISA STOK AKHIR FISIK</small>
                                    <strong class="text-teal h5 font-weight-bold mb-0"><?= (int)($selectedMedicine->stock ?? 0) ?> <?= esc($selectedMedicine->unit) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Kartu Stok -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" style="border: 1px solid #b8b8b8 !important;">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 50px;">NO</th>
                                    <th style="width: 140px;">TANGGAL & WAKTU</th>
                                    <th style="width: 160px;">NO. REFERENSI</th>
                                    <th style="width: 150px;">TIPE TRANSAKSI</th>
                                    <th style="width: 120px;">NO. BATCH / ED</th>
                                    <th style="width: 90px;" class="text-success">MASUK (+)</th>
                                    <th style="width: 90px;" class="text-danger">KELUAR (-)</th>
                                    <th style="width: 110px;" class="text-primary font-weight-bold">SALDO AKHIR</th>
                                    <th>KETERANGAN / KASIR / PASIEN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ledger)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle mr-1"></i> Belum ada rekaman mutasi stok untuk obat ini pada periode yang dipilih.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php 
                                    $no = 1; 
                                    foreach ($ledger as $row): 
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center text-xs"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                            <td class="font-weight-bold text-xs"><code><?= esc($row['ref_no']) ?></code></td>
                                            <td>
                                                <span class="badge badge-<?= $row['type_badge'] ?> px-2 py-1">
                                                    <?= esc($row['type_label']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center text-xs">
                                                <?= esc($row['batch_no'] ?: '-') ?>
                                            </td>
                                            <td class="text-right text-success font-weight-bold">
                                                <?= $row['qty_in'] > 0 ? '+' . number_format($row['qty_in'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right text-danger font-weight-bold">
                                                <?= $row['qty_out'] > 0 ? '-' . number_format($row['qty_out'], 0, ',', '.') : '-' ?>
                                            </td>
                                            <td class="text-right font-weight-bold text-primary" style="background: #f8fafc;">
                                                <?= number_format($row['balance'], 0, ',', '.') ?> <?= esc($selectedMedicine->unit) ?>
                                            </td>
                                            <td class="text-xs"><?= esc($row['notes'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border text-center py-5 text-muted" style="border: 1px dashed #b8b8b8 !important;">
                        <i class="fas fa-search fa-3x mb-3 text-secondary"></i>
                        <h6 class="font-weight-bold text-dark">Silakan Pilih Obat / Alkes Terlebih Dahulu</h6>
                        <p class="mb-0 text-sm">Pilih nama obat dari dropdown di atas untuk melihat buku kartu stok dan riwayat mutasi pergerakan fisiknya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
