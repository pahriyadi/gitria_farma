<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">

        <!-- Top Metrics Cards -->
        <div class="row mb-3">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Total Batch Obat Terdaftar</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($batches) ?> <small class="text-muted">Batch Fisik</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-info elevation-0"><i class="fas fa-clipboard-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Total Riwayat Opname</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= count($opnameHistory) ?> <small class="text-muted">Kali Dilakukan</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-warning elevation-0"><i class="fas fa-calendar-check text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Opname Terakhir</span>
                        <span class="info-box-number text-dark h5 mb-0">
                            <?= !empty($opnameHistory) ? date('d/m/Y', strtotime($opnameHistory[0]->opname_date)) : 'Belum Ada' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Navigation Tabs -->
        <div class="card card-teal card-outline card-outline-tabs shadow">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-opname" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-input-tab" data-toggle="pill" href="#tab-input" role="tab" aria-controls="tab-input" aria-selected="true">
                            <i class="fas fa-calculator text-teal mr-1"></i> 1. FORMULIR INPUT STOCK OPNAME OBAT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-history-tab" data-toggle="pill" href="#tab-history" role="tab" aria-controls="tab-history" aria-selected="false">
                            <i class="fas fa-history text-info mr-1"></i> 2. RIWAYAT & BERITA ACARA OPNAME
                            <?php if (!empty($opnameHistory)): ?>
                                <span class="badge badge-teal ml-2"><?= count($opnameHistory) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-opnameContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: FORMULIR INPUT STOCK OPNAME -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-input" role="tabpanel" aria-labelledby="tab-input-tab">
                        <form action="<?= base_url('apotek/opname') ?>" method="post" id="form-stock-opname">
                            <?= csrf_field() ?>

                            <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 border rounded">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-edit text-teal mr-1"></i> LEMBAR HITUNG STOK FISIK OBAT</h5>
                                    <small class="text-muted">Masukkan jumlah fisik riil di rak/gudang obat. Sistem akan menghitung selisih dan melakukan audit penyesuaian otomatis.</small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2" id="btn-prefill-system">
                                        <i class="fas fa-magic mr-1"></i> Isi Semua Sesuai Stok Sistem
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" id="btn-clear-physical">
                                        <i class="fas fa-eraser mr-1"></i> Kosongkan Input Fisik
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover datatable" id="opname-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">NO</th>
                                            <th style="width: 100px;">KODE</th>
                                            <th>NAMA OBAT & KATEGORI</th>
                                            <th style="width: 130px;">NO. BATCH & EXP</th>
                                            <th style="width: 100px;" class="text-center bg-light">STOK SISTEM</th>
                                            <th style="width: 130px;" class="text-center bg-teal-light">STOK FISIK (RIIL) <span class="text-danger">*</span></th>
                                            <th style="width: 110px;" class="text-center">SELISIH</th>
                                            <th>ALASAN / KETERANGAN SELISIH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($batches as $b): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                                <td><span class="badge badge-secondary"><?= esc($b->medicine_code) ?></span></td>
                                                <td>
                                                    <strong class="text-dark"><?= esc($b->medicine_name) ?></strong><br>
                                                    <small class="text-muted"><i class="fas fa-tag"></i> Golongan: <span class="badge badge-light border"><?= esc(strtoupper($b->type ?? 'OBAT')) ?></span> | Satuan: <strong><?= esc($b->unit ?? 'Pcs') ?></strong></small>
                                                    <input type="hidden" name="items[<?= $b->id ?>][medicine_id]" value="<?= $b->medicine_id ?>">
                                                    <input type="hidden" name="items[<?= $b->id ?>][system_stock]" value="<?= $b->stock ?>" id="sys-<?= $b->id ?>">
                                                </td>
                                                <td>
                                                    <strong><?= esc($b->batch_no) ?></strong><br>
                                                    <small class="text-muted">Exp: <?= date('d/m/Y', strtotime($b->expired_date)) ?></small>
                                                </td>
                                                <td class="text-center font-weight-bold text-dark" style="font-size: 15px;">
                                                    <?= esc($b->stock) ?> <small class="text-muted"><?= esc($b->unit ?? 'Pcs') ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" 
                                                           name="items[<?= $b->id ?>][physical_stock]" 
                                                           id="phys-<?= $b->id ?>" 
                                                           data-batchid="<?= $b->id ?>"
                                                           class="form-control form-control-sm text-center font-weight-bold input-physical" 
                                                           placeholder="Isi Fisik" 
                                                           min="0">
                                                </td>
                                                <td class="text-center font-weight-bold" id="diff-cell-<?= $b->id ?>">
                                                    <span class="badge badge-light border text-muted">-</span>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           name="items[<?= $b->id ?>][reason]" 
                                                           id="reason-<?= $b->id ?>"
                                                           class="form-control form-control-sm" 
                                                           placeholder="Keterangan selisih (opsional)">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Submission -->
                            <div class="card bg-light p-3 mt-3 border">
                                <div class="row">
                                    <div class="col-md-7 form-group mb-0">
                                        <label>Catatan / Keterangan Berita Acara Stock Opname:</label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Stock Opname Rutin Bulanan Farmasi Periode Agustus 2026. Pemeriksaan fisik lemari obat utama."></textarea>
                                    </div>
                                    <div class="col-md-5 text-md-right d-flex flex-column justify-content-end">
                                        <button type="submit" class="btn btn-teal btn-lg font-weight-bold shadow" onclick="return confirm('Apakah Anda yakin ingin menyimpan dan menerapkan penyesuaian stok fisik obat ini? Data stok sistem akan otomatis diselaraskan dengan hasil hitungan.');">
                                            <i class="fas fa-save mr-1"></i> Simpan & Terapkan Penyesuaian Stok Fisik
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: RIWAYAT & BERITA ACARA OPNAME -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel" aria-labelledby="tab-history-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-history text-info mr-1"></i> RIWAYAT DOKUMEN STOCK OPNAME OBAT</h5>
                                <small class="text-muted">Arsip berita acara stock opname dan log selisih penyesuaian fisik obat farmasi.</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">NO. OPNAME</th>
                                        <th style="width: 110px;" class="text-center">TANGGAL</th>
                                        <th>PETUGAS OPNAME</th>
                                        <th style="width: 110px;" class="text-center">ITEM DIHITUNG</th>
                                        <th style="width: 120px;" class="text-center">TOTAL SELISIH</th>
                                        <th>CATATAN / KETERANGAN</th>
                                        <th style="width: 90px;" class="text-center">STATUS</th>
                                        <th style="width: 200px;" class="text-center">AKSI DOKUMEN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($opnameHistory)): ?>
                                        <?php foreach ($opnameHistory as $op): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold">
                                                    <span class="badge badge-teal px-2 py-1" style="font-size: 12px;">
                                                        <?= esc($op->opname_no) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <strong><?= date('d/m/Y', strtotime($op->opname_date)) ?></strong><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($op->created_at)) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($op->staff_name ?? 'Apoteker') ?></strong><br>
                                                    <small class="text-muted">Instalasi Farmasi</small>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?= esc($op->total_items) ?> Item
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($op->total_discrepancy > 0): ?>
                                                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1">
                                                            <?= esc($op->total_discrepancy) ?> Unit Selisih
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success px-2 py-1">Cocok (0)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-sm">
                                                    <?= esc($op->notes ?: 'Stock Opname Rutin Farmasi') ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">TERADJUST</span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Print Berita Acara -->
                                                    <a href="<?= base_url('apotek/cetak-opname/' . $op->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold mr-1">
                                                        <i class="fas fa-print"></i> Berita Acara
                                                    </a>
                                                    <!-- View Details Modal Trigger -->
                                                    <button class="btn btn-outline-secondary btn-xs btn-view-opname-detail font-weight-bold"
                                                            data-id="<?= $op->id ?>"
                                                            data-no="<?= esc($op->opname_no) ?>"
                                                            data-date="<?= date('d/m/Y', strtotime($op->opname_date)) ?>"
                                                            data-staff="<?= esc($op->staff_name ?? 'Apoteker') ?>"
                                                            data-toggle="modal" data-target="#modalOpnameDetail">
                                                        <i class="fas fa-eye"></i> Rincian
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

<!-- Modal Rincian Detail Opname -->
<div class="modal fade" id="modalOpnameDetail" tabindex="-1" role="dialog" aria-labelledby="modalOpnameDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="modalOpnameDetailLabel">
                    <i class="fas fa-clipboard-list mr-1"></i> Rincian Selisih Stock Opname
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row bg-light p-2 rounded mb-3 border">
                    <div class="col-6">
                        <strong>No. Opname:</strong> <span id="mdl-opname-no" class="text-teal font-weight-bold"></span><br>
                        <strong>Tanggal:</strong> <span id="mdl-opname-date"></span>
                    </div>
                    <div class="col-6 text-right">
                        <strong>Petugas / Apoteker:</strong> <span id="mdl-opname-staff" class="font-weight-bold"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped" id="mdl-opname-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Obat</th>
                                <th style="width: 110px;">Batch & Exp</th>
                                <th style="width: 90px;" class="text-center">Sistem</th>
                                <th style="width: 90px;" class="text-center">Fisik</th>
                                <th style="width: 90px;" class="text-center">Selisih</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const opnameDetailsMap = <?= json_encode($opnameDetails) ?>;

    $(document).ready(function() {
        // Calculate discrepancy live on physical stock input
        $('.input-physical').on('input change', function() {
            const batchId = $(this).data('batchid');
            const sysVal = parseInt($('#sys-' + batchId).val()) || 0;
            const physVal = $(this).val();

            const cell = $('#diff-cell-' + batchId);

            if (physVal === '') {
                cell.html('<span class="badge badge-light border text-muted">-</span>');
                return;
            }

            const pNum = parseInt(physVal) || 0;
            const diff = pNum - sysVal;

            if (diff === 0) {
                cell.html('<span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Cocok (0)</span>');
            } else if (diff > 0) {
                cell.html(`<span class="badge badge-info px-2 py-1"><i class="fas fa-plus"></i> +${diff} (Lebih)</span>`);
            } else {
                cell.html(`<span class="badge badge-danger px-2 py-1"><i class="fas fa-minus"></i> ${diff} (Kurang)</span>`);
            }
        });

        // Pre-fill physical stock identical to system stock
        $('#btn-prefill-system').click(function() {
            $('.input-physical').each(function() {
                const batchId = $(this).data('batchid');
                const sysVal = $('#sys-' + batchId).val();
                $(this).val(sysVal).trigger('input');
            });
        });

        // Clear physical inputs
        $('#btn-clear-physical').click(function() {
            if (confirm('Kosongkan semua isian stok fisik?')) {
                $('.input-physical').val('').trigger('input');
            }
        });

        // Opname Detail Modal Trigger
        $('.btn-view-opname-detail').click(function() {
            const id = $(this).data('id');
            const no = $(this).data('no');
            const date = $(this).data('date');
            const staff = $(this).data('staff');

            $('#mdl-opname-no').text(no);
            $('#mdl-opname-date').text(date);
            $('#mdl-opname-staff').text(staff);

            const details = opnameDetailsMap[id] || [];
            let html = '';

            if (details.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted p-3">Tidak ada rincian item.</td></tr>';
            } else {
                details.forEach(function(d) {
                    const diff = parseInt(d.difference) || 0;
                    let diffBadge = '';
                    if (diff === 0) {
                        diffBadge = '<span class="badge badge-success">0 (Cocok)</span>';
                    } else if (diff > 0) {
                        diffBadge = `<span class="badge badge-info">+${diff}</span>`;
                    } else {
                        diffBadge = `<span class="badge badge-danger">${diff}</span>`;
                    }

                    html += `
                        <tr>
                            <td><strong>${d.medicine_name}</strong></td>
                            <td class="text-sm">${d.batch_no}<br><small class="text-muted">${d.expired_date}</small></td>
                            <td class="text-center font-weight-bold">${d.system_stock} ${d.unit}</td>
                            <td class="text-center font-weight-bold text-teal">${d.physical_stock} ${d.unit}</td>
                            <td class="text-center font-weight-bold">${diffBadge}</td>
                            <td class="text-sm">${d.reason || '-'}</td>
                        </tr>
                    `;
                });
            }

            $('#mdl-opname-table tbody').html(html);
        });
    });
</script>
<?= $this->endSection() ?>
