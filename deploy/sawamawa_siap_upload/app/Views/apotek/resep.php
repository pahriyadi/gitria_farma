<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>

<!-- Nav Tabs: Antrean Resep Aktif & Riwayat e-Resep Selesai -->
<ul class="nav nav-pills mb-3 bg-white p-2 rounded shadow-sm border" id="resepTabs" role="tablist">
    <li class="nav-item mr-2">
        <a class="nav-link active font-weight-bold" id="tab-antrean-link" data-toggle="pill" href="#tab-antrean" role="tab" aria-controls="tab-antrean" aria-selected="true">
            <i class="fas fa-prescription-bottle-medical mr-1 text-teal"></i> Resep Menunggu Penyiapan
            <span class="badge badge-teal ml-1 font-weight-normal" id="lbl-presc-count"><?= count($prescriptions) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link font-weight-bold" id="tab-riwayat-link" data-toggle="pill" href="#tab-riwayat" role="tab" aria-controls="tab-riwayat" aria-selected="false">
            <i class="fas fa-history mr-1 text-info"></i> Daftar Riwayat e-Resep Selesai
            <span class="badge badge-info ml-1 font-weight-normal" id="lbl-completed-count"><?= count($completedPrescriptions ?? []) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content" id="resepTabContent">
    <!-- ===================================================================== -->
    <!-- TAB 1: WORKSPACE PENYIAPAN & DISPENSING RESEP (ANTREAN MENUNGGU)      -->
    <!-- ===================================================================== -->
    <div class="tab-pane fade show active" id="tab-antrean" role="tabpanel" aria-labelledby="tab-antrean-link">
        <div class="row">
            <!-- Active e-Prescriptions Queue -->
            <div class="col-md-5">
                <div class="card card-outline card-teal shadow sticky-card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <h6 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-file-prescription text-teal mr-1"></i> Antrean Resep Menunggu
                        </h6>
                        <span class="badge badge-teal ml-auto font-weight-normal"><?= count($prescriptions) ?> Resep</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="presc-list" style="max-height: 580px; overflow-y: auto;">
                            <?php if (empty($prescriptions)): ?>
                                <div class="text-center text-muted p-4">
                                    <i class="fas fa-check-circle fa-2x text-teal mb-2 d-block"></i>
                                    Tidak ada antrean resep obat yang menunggu saat ini.
                                </div>
                            <?php else: ?>
                                <?php foreach ($prescriptions as $p): 
                                    $isPaid = !empty($p->is_paid);
                                    $bStatus = $p->billing_status ?? 'open';
                                    $pMethod = $p->payment_method ?? '';
                                ?>
                                    <div class="list-group-item list-group-item-action presc-item <?= $isPaid ? 'border-left-success' : 'border-left-warning' ?>" 
                                       role="button"
                                       tabindex="0"
                                       style="cursor: pointer;"
                                       data-id="<?= $p->id ?>" 
                                       data-name="<?= esc($p->patient_name) ?>" 
                                       data-rm="<?= esc($p->no_rm) ?>" 
                                       data-doctor="<?= esc($p->doctor_name) ?>" 
                                       data-visit="<?= esc($p->no_visit) ?>"
                                       data-is-paid="<?= $isPaid ? '1' : '0' ?>"
                                       data-billing-status="<?= esc($bStatus) ?>"
                                       data-payment-method="<?= esc($pMethod) ?>">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-teal"><?= esc($p->patient_name) ?></h6>
                                            <small class="badge badge-secondary"><?= esc($p->no_rm) ?></small>
                                        </div>
                                        <p class="mb-1 text-sm text-secondary">Dokter: <strong><?= esc($p->doctor_name) ?></strong></p>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-secondary"><?= esc($p->no_visit) ?></small>
                                            <div>
                                                <a href="<?= base_url('apotek/cetak-etiket/' . $p->id) ?>" target="_blank" class="btn btn-outline-info btn-xs font-weight-bold mr-1" onclick="event.stopPropagation();" title="Cetak Semua E-Tiket Obat">
                                                    <i class="fas fa-prescription mr-1"></i> E-Tiket
                                                </a>
                                                <?php if ($isPaid): ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Lunas</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Bayar</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dispense e-Prescription Workspace -->
            <div class="col-md-7">
                <div class="card card-teal shadow" id="workspace-card" style="display:none;">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <h6 class="card-title font-weight-bold mb-0" id="workspace-title">
                            <i class="fas fa-prescription-bottle-medical mr-1"></i> Penyiapan & Dispensing Resep
                        </h6>
                        <div class="card-tools ml-auto d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-light text-teal font-weight-bold shadow-sm mr-2" id="btn-call-pharmacy-voice" title="Panggil Pasien ke Loket Obat">
                                <i class="fas fa-volume-high mr-1"></i> Panggil ke Apotek
                            </button>
                            <a href="#" id="btn-print-etiket-direct" target="_blank" class="btn btn-sm btn-light text-teal font-weight-bold shadow-sm" style="display:none;">
                                <i class="fas fa-print mr-1"></i> Cetak E-Tiket
                            </a>
                        </div>
                    </div>
                    <form action="<?= base_url('apotek/resep') ?>" method="post" id="form-dispense-resep">
                        <?= csrf_field() ?>
                        <input type="hidden" name="prescription_id" id="modal-prescription-id">
                        <div class="card-body">
                            <!-- Status Banner Tagihan Kasir (Live-Sync Alert) -->
                            <div id="box-payment-status" class="mb-3"></div>

                            <div class="row border-bottom pb-2 mb-3 text-sm bg-light p-2 rounded">
                                <div class="col-md-6">
                                    <strong>Pasien:</strong> <span id="lbl-patient" class="text-teal font-weight-bold"></span><br>
                                    <strong>No RM:</strong> <span id="lbl-rm"></span>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <strong>Dokter Pemeriksa:</strong> <span id="lbl-doctor" class="font-weight-bold"></span><br>
                                    <strong>No. Kunjungan:</strong> <span id="lbl-visit"></span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="font-weight-bold text-teal mb-0"><i class="fas fa-pills mr-1"></i> Rincian Obat & Opsi Penyerahan</h6>
                                <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Stok kosong otomatis disarankan Beli di Luar</small>
                            </div>
                            
                            <!-- Container for dynamic prescription medicines -->
                            <div id="medicines-container"></div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <button type="submit" id="btn-submit-dispense" class="btn btn-teal btn-block font-weight-bold btn-lg shadow-sm">
                                <i class="fas fa-check-double mr-1"></i> Selesai Dispensing & Serahkan Obat
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Empty state placeholder -->
                <div class="card shadow-none border bg-light text-center py-5" id="workspace-placeholder">
                    <div class="py-4">
                        <i class="fas fa-hand-holding-medical fa-3x text-muted mb-3"></i>
                        <h6 class="font-weight-bold text-secondary">Pilih Resep untuk Mulai Menyiapkan</h6>
                        <p class="text-muted text-sm mb-0">Klik salah satu pasien pada daftar antrean resep di sebelah kiri.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- TAB 2: DAFTAR RIWAYAT e-RESEP SELESAI (LENGKAP DENGAN AKSI CETAK)      -->
    <!-- ===================================================================== -->
    <div class="tab-pane fade" id="tab-riwayat" role="tabpanel" aria-labelledby="tab-riwayat-link">
        <div class="card card-outline card-teal shadow-none bg-white border">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-history text-teal mr-1"></i> Daftar Riwayat e-Resep Pasien yang Telah Selesai
                </h6>
                <span class="badge badge-light border text-xs font-weight-bold">
                    Total: <?= count($completedPrescriptions ?? []) ?> Resep Selesai
                </span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable-full text-dark" id="table-riwayat-resep" style="width: 100%; font-size: 13px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 135px;">Waktu Selesai</th>
                                <th style="width: 140px;">No. Resep &amp; Billing</th>
                                <th>Pasien &amp; No. RM</th>
                                <th style="width: 160px;">Dokter Peresep</th>
                                <th>Rincian Item Obat</th>
                                <th style="width: 130px;" class="text-right">Total Biaya</th>
                                <th style="width: 110px;" class="text-center">Status Bayar</th>
                                <th style="width: 280px;" class="text-center">Aksi Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($completedPrescriptions)): ?>
                                <?php $no = 1; foreach ($completedPrescriptions as $cp): 
                                    $isPaid = ($cp->billing_status === 'paid' || !empty($cp->is_paid));
                                    $totVal = $cp->calculated_total ?? $cp->billing_total_medicines ?? 0;
                                    $timeStr = !empty($cp->dispensed_at) ? date('d/m/Y H:i', strtotime($cp->dispensed_at)) : date('d/m/Y H:i', strtotime($cp->created_at));
                                ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $no++ ?></td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold text-dark d-block"><?= $timeStr ?></span>
                                            <small class="text-muted"><i class="fas fa-clock mr-1"></i>WITA</small>
                                        </td>
                                        <td class="align-middle">
                                            <strong class="text-teal d-block">RSP-<?= str_pad($cp->id, 4, '0', STR_PAD_LEFT) ?></strong>
                                            <small class="text-muted d-block"><?= esc($cp->billing_no ?? '-') ?></small>
                                            <small class="badge badge-light border"><?= esc($cp->no_visit ?? '-') ?></small>
                                        </td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block"><?= esc($cp->patient_name ?? 'Pasien Umum') ?></strong>
                                            <span class="badge badge-secondary"><?= esc($cp->no_rm ?? '-') ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold text-secondary"><?= esc($cp->doctor_name ?? 'Dokter Pemeriksa') ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-teal mb-1"><?= $cp->item_count ?? 0 ?> Jenis Obat</span>
                                            <div class="text-xs text-muted" style="max-width: 250px; line-height: 1.3;">
                                                <?= esc($cp->items_summary ?? '-') ?>
                                            </div>
                                        </td>
                                        <td class="text-right align-middle font-weight-bold text-teal">
                                            Rp <?= number_format($totVal, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($isPaid): ?>
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Lunas</span>
                                                <small class="d-block text-muted text-xs mt-1"><?= strtoupper(esc($cp->payment_method ?? 'TUNAI')) ?></small>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Button 1: Rincian Modal -->
                                                <button type="button" class="btn btn-xs btn-outline-info font-weight-bold btn-view-resep-detail" data-id="<?= $cp->id ?>" title="Lihat Rincian Resep">
                                                    <i class="fas fa-eye mr-1"></i> Rincian
                                                </button>
                                                <!-- Button 2: E-Tiket -->
                                                <a href="<?= base_url('apotek/cetak-etiket/' . $cp->id) ?>" target="_blank" class="btn btn-xs btn-outline-primary font-weight-bold" title="Cetak Semua E-Tiket Obat">
                                                    <i class="fas fa-prescription mr-1"></i> E-Tiket
                                                </a>
                                                <!-- Button 3: Kwitansi Resmi -->
                                                <a href="<?= base_url('apotek/cetak-kwitansi-resep/' . $cp->id) ?>" target="_blank" class="btn btn-xs btn-outline-success font-weight-bold" title="Cetak Kwitansi Pembayaran Resep">
                                                    <i class="fas fa-receipt mr-1"></i> Kwitansi
                                                </a>
                                                <!-- Button 4: Cetak Struk Thermal POS -->
                                                <a href="<?= base_url('apotek/cetak-struk-resep/' . $cp->id) ?>" target="_blank" class="btn btn-xs btn-outline-dark font-weight-bold" title="Cetak Struk Thermal POS (80mm)">
                                                    <i class="fas fa-print mr-1"></i> Cetak Struk
                                                </a>
                                            </div>
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

<!-- ===================================================================== -->
<!-- MODAL RINCIAN LENGKAP e-RESEP PASIEN                                  -->
<!-- ===================================================================== -->
<div class="modal fade" id="modalDetailResep" tabindex="-1" role="dialog" aria-labelledby="modalDetailResepLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border">
            <div class="modal-header bg-light border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="modalDetailResepLabel">
                    <i class="fas fa-file-prescription text-teal mr-1"></i> Rincian Lengkap e-Resep Pasien
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div id="modal-detail-loading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-teal mb-2"></i>
                    <div class="text-muted text-sm">Memuat data rincian resep...</div>
                </div>

                <div id="modal-detail-content" style="display: none;">
                    <!-- Patient & Presc Info -->
                    <div class="row bg-light p-2 rounded mb-3 border text-sm">
                        <div class="col-md-6">
                            <span class="text-muted">Nama Pasien:</span> <strong id="mdl-patient-name" class="text-teal"></strong><br>
                            <span class="text-muted">No. Rekam Medis:</span> <span id="mdl-no-rm" class="badge badge-secondary font-weight-bold"></span><br>
                            <span class="text-muted">No. Rawat / Kunjungan:</span> <span id="mdl-no-visit" class="font-weight-bold text-dark"></span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span class="text-muted">Dokter Peresep:</span> <strong id="mdl-doctor-name" class="text-dark"></strong><br>
                            <span class="text-muted">No. Resep:</span> <strong id="mdl-presc-no" class="text-teal"></strong><br>
                            <span class="text-muted">Waktu Selesai:</span> <span id="mdl-dispensed-time" class="font-weight-bold text-secondary"></span><br>
                            <span class="text-muted">Status Bayar:</span> <span id="mdl-payment-badge"></span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-3 border rounded">
                        <table class="table table-bordered table-striped table-sm mb-0" style="font-size: 12.5px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 30px;" class="text-center">No</th>
                                    <th>Nama Obat &amp; Aturan Pakai</th>
                                    <th style="width: 60px;" class="text-center">Qty</th>
                                    <th style="width: 120px;" class="text-right">Harga Satuan</th>
                                    <th style="width: 90px;" class="text-right text-danger">Diskon</th>
                                    <th style="width: 130px;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="mdl-medicines-body">
                                <!-- Dynamic rows -->
                            </tbody>
                            <tfoot class="bg-light font-weight-bold" id="mdl-medicines-foot">
                                <!-- Dynamic totals -->
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between">
                <div>
                    <!-- Shortcut Cetak Buttons -->
                    <a href="#" id="btn-mdl-etiket" target="_blank" class="btn btn-sm btn-outline-primary font-weight-bold mr-1">
                        <i class="fas fa-prescription mr-1"></i> Cetak E-Tiket
                    </a>
                    <a href="#" id="btn-mdl-kwitansi" target="_blank" class="btn btn-sm btn-outline-success font-weight-bold mr-1">
                        <i class="fas fa-receipt mr-1"></i> Cetak Kwitansi
                    </a>
                    <a href="#" id="btn-mdl-struk" target="_blank" class="btn btn-sm btn-outline-dark font-weight-bold">
                        <i class="fas fa-print mr-1"></i> Cetak Struk
                    </a>
                </div>
                <button type="button" class="btn btn-sm btn-secondary font-weight-bold" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.prescriptionDetails = <?= json_encode($details ?? []) ?>;

    function renderMedicineRows(items) {
        if (!items || items.length === 0) {
            $('#medicines-container').html('<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ada rincian obat pada resep ini.</div>');
            return;
        }

        let html = '';
        items.forEach(function(item) {
            const hasStock = item.batches && item.batches.length > 0;
            let batchOptions = '';
            if (hasStock) {
                item.batches.forEach(function(b) {
                    batchOptions += `<option value="${b.id}">Batch: ${b.batch_no} (Sisa: ${b.stock}, Exp: ${b.expired_date})</option>`;
                });
            } else {
                batchOptions = '<option value="">-- Stok Habis di Gudang --</option>';
            }

            html += `
                <div class="card card-outline card-secondary mb-3 shadow-none border">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-capsules text-teal mr-1"></i> ${item.medicine_name}
                                </h6>
                                <span class="badge badge-info">Dosis: ${item.dosage || '-'}</span>
                                <span class="badge badge-light border">Jumlah Butuh: <strong>${item.qty}</strong></span>
                            </div>
                            <div style="min-width: 170px;">
                                <label class="text-xs text-muted mb-0 font-weight-bold">Status Penyerahan:</label>
                                <select name="dispense[${item.medicine_id}][action]" class="form-control form-control-sm select-action" data-medid="${item.medicine_id}">
                                    <option value="internal" ${hasStock ? 'selected' : ''}>Serahkan di Apotek</option>
                                    <option value="beli_luar" ${!hasStock ? 'selected' : ''}>Resep Luar (Beli di Luar)</option>
                                    <option value="cancel">Batalkan Item Ini</option>
                                </select>
                            </div>
                        </div>

                        <div id="action-details-${item.medicine_id}">
                            <div id="batch-box-${item.medicine_id}" style="${hasStock ? 'display:block;' : 'display:none;'}">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="text-xs text-dark font-weight-bold mb-1">Pilih Batch (FEFO / Expired Terdekat):</label>
                                        <select name="dispense[${item.medicine_id}][batch_id]" class="form-control form-control-sm" ${hasStock ? 'required' : ''}>
                                            ${batchOptions}
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="text-xs text-dark font-weight-bold mb-1">Jumlah Diambil:</label>
                                        <input type="number" min="1" max="${item.qty}" name="dispense[${item.medicine_id}][qty]" class="form-control form-control-sm" value="${item.qty}" ${hasStock ? 'required' : ''}>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="text-xs text-dark font-weight-bold mb-1">Diskon Item (Rp):</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="number" step="100" min="0" name="dispense[${item.medicine_id}][discount]" class="form-control form-control-sm" placeholder="0" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="text-xs text-dark font-weight-bold mb-1">Tuslah (Rp):</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="number" step="100" min="0" name="dispense[${item.medicine_id}][tusla]" class="form-control form-control-sm" placeholder="0" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="text-xs text-dark font-weight-bold mb-1">Embalase (Rp):</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="number" step="100" min="0" name="dispense[${item.medicine_id}][embalase]" class="form-control form-control-sm" placeholder="0" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="luar-box-${item.medicine_id}" class="alert alert-warning py-1 px-2 mb-0 text-xs" style="${!hasStock ? 'display:block;' : 'display:none;'}">
                                <i class="fas fa-external-link-alt mr-1"></i> Item ini ditandai untuk ditebus di apotek luar (tidak memotong stok &amp; tidak masuk tagihan).
                            </div>

                            <div id="cancel-box-${item.medicine_id}" class="alert alert-danger py-1 px-2 mb-0 text-xs" style="display:none;">
                                <i class="fas fa-ban mr-1"></i> Item obat dibatalkan oleh apoteker.
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#medicines-container').html(html);
    }

    window.selectPrescriptionItem = function($item) {
        if (!$item || !$item.length) return;

        $('.presc-item').removeClass('active border-left-teal').addClass('border-left-secondary');
        $item.addClass('active border-left-teal');

        const id = $item.data('id') || $item.attr('data-id');
        const name = $item.data('name') || $item.attr('data-name') || 'Pasien';
        const rm = $item.data('rm') || $item.attr('data-rm') || '-';
        const doctor = $item.data('doctor') || $item.attr('data-doctor') || 'Dokter Pemeriksa';
        const visit = $item.data('visit') || $item.attr('data-visit') || '-';
        const isPaid = ($item.data('is-paid') == '1' || $item.attr('data-is-paid') == '1');
        const bStatus = $item.data('billing-status') || $item.attr('data-billing-status') || 'open';
        const pMethod = $item.data('payment-method') || $item.attr('data-payment-method') || 'Tunai';

        $('#workspace-placeholder').hide();
        $('#workspace-card').fadeIn(200);

        if ($(window).width() < 992) {
            $('html, body').animate({
                scrollTop: $('#workspace-card').offset().top - 70
            }, 300);
        }

        $('#modal-prescription-id').val(id);
        $('#lbl-patient').text(name);
        $('#lbl-rm').text(rm);
        $('#lbl-doctor').text(doctor);
        $('#lbl-visit').text(visit);

        // Update link cetak etiket direct
        $('#btn-print-etiket-direct')
            .attr('href', '<?= base_url("apotek/cetak-etiket/") ?>/' + id)
            .show();

        // Banner Status Tagihan Kasir
        let payBanner = '';
        if (isPaid) {
            payBanner = `
                <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-0" style="border-radius: 6px;">
                    <i class="fas fa-check-circle fa-lg mr-2"></i>
                    <div>
                        <strong>TAGIHAN KASIR SUDAH LUNAS</strong> (Metode: ${pMethod.toUpperCase()})<br>
                        <small>Obat dapat langsung diracik, diberi etiket, dan diserahkan kepada pasien.</small>
                    </div>
                </div>
            `;
        } else {
            payBanner = `
                <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-0" style="border-radius: 6px;">
                    <i class="fas fa-exclamation-triangle fa-lg mr-2"></i>
                    <div>
                        <strong>TAGIHAN KASIR BELUM LUNAS</strong> (Status: ${bStatus.toUpperCase()})<br>
                        <small>Pasien belum menyelesaikan pembayaran di Kasir Utama. Pastikan pembayaran dikonfirmasi sebelum penyerahan obat.</small>
                    </div>
                </div>
            `;
        }
        $('#box-payment-status').html(payBanner);

        // Render obat dari cache atau ambil secara real-time via AJAX fallback
        if (window.prescriptionDetails && window.prescriptionDetails[id] && window.prescriptionDetails[id].length > 0) {
            renderMedicineRows(window.prescriptionDetails[id]);
        } else {
            $('#medicines-container').html(`
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-teal mb-2"></i>
                    <div class="text-muted text-sm">Memuat rincian obat resep...</div>
                </div>
            `);
            $.ajax({
                url: '<?= base_url("apotek/resep-detail-json") ?>/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && res.status === 'success' && res.details && res.details.length > 0) {
                        if (!window.prescriptionDetails) window.prescriptionDetails = {};
                        window.prescriptionDetails[id] = res.details;
                        renderMedicineRows(res.details);
                    } else {
                        $('#medicines-container').html('<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-circle mr-1"></i> Tidak ada rincian obat pada resep ini.</div>');
                    }
                },
                error: function() {
                    $('#medicines-container').html('<div class="alert alert-danger py-2 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat rincian obat dari server.</div>');
                }
            });
        }
    };

    $(document).ready(function() {
        // Initialize DataTable on Riwayat Table
        if ($.fn.DataTable && $('#table-riwayat-resep').length) {
            $('#table-riwayat-resep').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "order": [[1, "desc"]],
                "pageLength": 15,
                "responsive": true
            });
        }

        // Support URL Hash to jump to tab (e.g. /apotek/resep#riwayat)
        if (window.location.hash === '#riwayat') {
            $('#tab-riwayat-link').tab('show');
        }

        // Delegated click for static and live-synced prescription items in active queue
        $(document).on('click', '.presc-item', function(e) {
            e.preventDefault();
            window.selectPrescriptionItem($(this));
        });

        // Keyboard navigation (Enter / Space)
        $(document).on('keydown', '.presc-item', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.selectPrescriptionItem($(this));
            }
        });

        // Action changer handler (delegated)
        $(document).on('change', '.select-action', function() {
            const medId = $(this).data('medid');
            const action = $(this).val();

            if (action === 'internal') {
                $(`#batch-box-${medId}`).slideDown(150);
                $(`#batch-box-${medId} select, #batch-box-${medId} input[type="number"]`).prop('required', true);
                $(`#luar-box-${medId}`).slideUp(150);
                $(`#cancel-box-${medId}`).slideUp(150);
            } else if (action === 'beli_luar') {
                $(`#batch-box-${medId}`).slideUp(150);
                $(`#batch-box-${medId} select, #batch-box-${medId} input[type="number"]`).prop('required', false);
                $(`#luar-box-${medId}`).slideDown(150);
                $(`#cancel-box-${medId}`).slideUp(150);
            } else if (action === 'cancel') {
                $(`#batch-box-${medId}`).slideUp(150);
                $(`#batch-box-${medId} select, #batch-box-${medId} input[type="number"]`).prop('required', false);
                $(`#luar-box-${medId}`).slideUp(150);
                $(`#cancel-box-${medId}`).slideDown(150);
            }
        });

        // Anti-Double Submit on Prescription Dispensing Form
        $('#form-dispense-resep').on('submit', function() {
            const $btn = $('#btn-submit-dispense');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Dispensing & Stok...');
        });

        // Event Tombol Panggil Suara Apotek
        $('#btn-call-pharmacy-voice').on('click', function(e) {
            e.preventDefault();
            const pName = $('#lbl-patient').text().trim() || 'Pasien';
            const qNo = $('#lbl-visit').text().trim() || 'A-001';

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'farmasi',
                    counter_name: 'Loket Farmasi dan Apotek',
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: 1
                }, function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> dipanggil ke Loket Apotek.', '📢 Panggilan Farmasi');
                    }
                });
            } else if (typeof callPatientToPharmacy === 'function') {
                callPatientToPharmacy(qNo, pName);
            }
        });

        // Click Handler: Modal Rincian e-Resep
        $(document).on('click', '.btn-view-resep-detail', function(e) {
            e.preventDefault();
            const prescId = $(this).data('id');
            $('#modal-detail-loading').show();
            $('#modal-detail-content').hide();
            $('#modalDetailResep').modal('show');

            // Set direct buttons
            $('#btn-mdl-etiket').attr('href', '<?= base_url("apotek/cetak-etiket") ?>/' + prescId);
            $('#btn-mdl-kwitansi').attr('href', '<?= base_url("apotek/cetak-kwitansi-resep") ?>/' + prescId);
            $('#btn-mdl-struk').attr('href', '<?= base_url("apotek/cetak-struk-resep") ?>/' + prescId);

            $.ajax({
                url: '<?= base_url("apotek/resep-detail-json") ?>/' + prescId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        const p = res.prescription;
                        const d = res.details;
                        const s = res.summary;

                        $('#mdl-patient-name').text(p.patient_name || 'Pasien Umum');
                        $('#mdl-no-rm').text(p.no_rm || '-');
                        $('#mdl-no-visit').text(p.no_visit || '-');
                        $('#mdl-doctor-name').text(p.doctor_name || '-');
                        $('#mdl-presc-no').text('RSP-' + String(p.id).padStart(4, '0'));

                        const timeVal = p.dispensed_at || p.created_at || '';
                        $('#mdl-dispensed-time').text(timeVal ? timeVal + ' WITA' : '-');

                        const isPaid = (p.billing_status === 'paid' || p.is_paid == 1);
                        if (isPaid) {
                            $('#mdl-payment-badge').html('<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> LUNAS (' + (p.payment_method || 'TUNAI').toUpperCase() + ')</span>');
                        } else {
                            $('#mdl-payment-badge').html('<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> BELUM LUNAS</span>');
                        }

                        // Build rows
                        let rowsHtml = '';
                        let no = 1;
                        d.forEach(function(item) {
                            const tusla = parseFloat(item.tusla) || 0;
                            const embalase = parseFloat(item.embalase) || 0;
                            const disc = parseFloat(item.discount) || 0;
                            const price = parseFloat(item.price) || 0;
                            const qty = parseInt(item.qty) || 1;
                            const subtotal = Math.max(0, (qty * price) + tusla + embalase - disc);
                            const effectiveUnitPrice = qty > 0 ? (subtotal / qty) : subtotal;

                            rowsHtml += `
                                <tr>
                                    <td class="text-center">${no++}</td>
                                    <td>
                                        <strong class="text-dark">${item.medicine_name}</strong>
                                        ${item.dosage && item.dosage !== '-' ? `<div class="text-xs text-muted font-italic"><i class="fas fa-info-circle text-teal mr-1"></i>${item.dosage}</div>` : ''}
                                    </td>
                                    <td class="text-center font-weight-bold">${qty}</td>
                                    <td class="text-right">Rp ${Math.round(effectiveUnitPrice).toLocaleString('id-ID')}</td>
                                    <td class="text-right text-danger">${disc > 0 ? '-Rp ' + disc.toLocaleString('id-ID') : '-'}</td>
                                    <td class="text-right font-weight-bold text-teal">Rp ${subtotal.toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });
                        $('#mdl-medicines-body').html(rowsHtml);

                        // Build footer summary
                        let footHtml = `
                            <tr>
                                <td colspan="5" class="text-right text-muted">Subtotal Tagihan Obat:</td>
                                <td class="text-right font-weight-bold">Rp ${(s.grand_total + s.total_discount).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                        if (s.total_discount > 0) {
                            footHtml += `
                                <tr>
                                    <td colspan="5" class="text-right text-danger">Potongan Diskon Farmasi:</td>
                                    <td class="text-right text-danger font-weight-bold">- Rp ${s.total_discount.toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        }
                        footHtml += `
                            <tr class="bg-light" style="border-top: 2px solid #0d9488;">
                                <td colspan="5" class="text-right font-weight-bold text-dark" style="font-size: 13px;">GRAND TOTAL RESEP:</td>
                                <td class="text-right font-weight-bold text-teal" style="font-size: 14px;">Rp ${s.grand_total.toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                        $('#mdl-medicines-foot').html(footHtml);

                        $('#modal-detail-loading').hide();
                        $('#modal-detail-content').fadeIn(150);
                    } else {
                        $('#modal-detail-loading').html('<div class="alert alert-danger mb-0">' + (res.message || 'Gagal memuat rincian resep.') + '</div>');
                    }
                },
                error: function() {
                    $('#modal-detail-loading').html('<div class="alert alert-danger mb-0">Terjadi kesalahan koneksi server saat memuat rincian resep.</div>');
                }
            });
        });

        // Trigger Panggilan Suara Penyerahan Obat Selesai & Ucapan Terima Kasih
        <?php 
            $vTrigger = session()->getFlashdata('voice_trigger');
            if (!empty($vTrigger) && is_array($vTrigger)): 
                $qNumber = $vTrigger['queue_number'] ?? 'A-001';
                $pName   = $vTrigger['patient_name'] ?? 'Pasien';
                if (($vTrigger['action'] ?? '') === 'to_completed'):
        ?>
            setTimeout(function() {
                const qNo = <?= json_encode($qNumber) ?>;
                const pName = <?= json_encode($pName) ?>;

                function executeVoiceCall() {
                    try {
                        if (window.VCM) {
                            window.VCM.unlockAudio();
                            window.VCM.callCompleted(qNo, pName);
                        } else if (typeof callPatientCompleted === 'function') {
                            callPatientCompleted(qNo, pName);
                        } else if ('speechSynthesis' in window) {
                            if (window.speechSynthesis.paused) {
                                window.speechSynthesis.resume();
                            }
                            const spokenQ = qNo.replace(/-/g, ' ');
                            const speechText = 'Nomor antrean ' + spokenQ + ', atas nama ' + pName + ', penyerahan obat telah selesai. Terima kasih banyak atas kunjungan Anda di Sawamawa Medical Center, semoga lekas sembuh.';
                            const utt = new SpeechSynthesisUtterance(speechText);
                            utt.lang = 'id-ID';
                            utt.rate = 0.85;
                            window.speechSynthesis.speak(utt);
                        }
                    } catch(e) {
                        console.warn('[Apotek Voice] Error triggering completed call:', e);
                    }
                }

                executeVoiceCall();

                // Visual Toastr Notification with Re-Play Button
                if (typeof toastr !== 'undefined') {
                    toastr.success(
                        '📢 <strong>Panggilan Selesai:</strong> Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> telah dipanggil dengan ucapan terima kasih.<br><button class="btn btn-xs btn-light mt-1 font-weight-bold" id="btn-replay-finish-voice"><i class="fas fa-volume-high mr-1"></i> Putar Ulang Panggilan</button>',
                        'Penyerahan Obat Selesai',
                        { timeOut: 10000, closeButton: true }
                    );

                    $(document).on('click', '#btn-replay-finish-voice', function(e) {
                        e.preventDefault();
                        executeVoiceCall();
                    });
                }
            }, 300);
        <?php endif; endif; ?>
    });
</script>
<?= $this->endSection() ?>
