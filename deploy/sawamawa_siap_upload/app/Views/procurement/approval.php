<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-clipboard-check text-teal mr-2"></i> Pusat Persetujuan Transaksi & Pengadaan (Approvals)
                </h1>
                <small class="text-muted">Verifikasi berjenjang pengadaan barang (PR), pesanan pembelian (PO), dan persetujuan anggaran operasional</small>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= base_url('procurement/po') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-boxes-packing mr-1"></i> Buka Modul Pengadaan (PO)
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- 3 KPI SUMMARY CARDS -->
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #f59e0b !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-warning text-uppercase">Menunggu Keputusan Anda</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($requests) ?> <small class="text-muted">Pengajuan</small></h4>
                                <small class="text-muted">Memerlukan verifikasi / tanda tangan</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Pengajuan Disetujui</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count(array_filter($historyRequests ?? [], fn($h) => $h->status === 'approved')) ?> <small class="text-muted">Selesai</small></h4>
                                <small class="text-muted">Lanjut ke proses PO & pengiriman</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #ef4444 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-danger text-uppercase">Pengajuan Ditolak</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count(array_filter($historyRequests ?? [], fn($h) => $h->status === 'rejected')) ?> <small class="text-muted">Ditolak</small></h4>
                                <small class="text-muted">Perlu revisi / dibatalkan</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-danger">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="approval-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-pending-link" data-toggle="pill" href="#tab-pending" role="tab">
                            <i class="fas fa-hourglass-half text-warning mr-1"></i> 1. MENUNGGU PERSETUJUAN (PENDING APPROVAL)
                            <span class="badge badge-warning ml-2"><?= count($requests) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-history-link" data-toggle="pill" href="#tab-history" role="tab">
                            <i class="fas fa-history text-teal mr-1"></i> 2. RIWAYAT KEPUTUSAN APPROVAL
                            <span class="badge badge-teal ml-2"><?= count($historyRequests) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="approval-tabsContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: PENDING APPROVAL REQUESTS -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-pending" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Transaksi yang Membutuhkan Keputusan Anda</h6>
                                <small class="text-muted">Periksa rincian item, estimasi harga, dan justifikasi sebelum memberikan persetujuan</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">No. Referensi PR</th>
                                        <th>Departemen / Unit</th>
                                        <th>Supplier Tujuan</th>
                                        <th>Pemohon</th>
                                        <th class="text-right">Total Anggaran</th>
                                        <th class="text-center">Tahap Approval</th>
                                        <th>Waktu Pengajuan</th>
                                        <th style="width: 180px;" class="text-center">Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($requests)): ?>
                                        <?php foreach ($requests as $r): ?>
                                            <?php 
                                            $detailsObj = $requestDetails[$r->id] ?? null; 
                                            $itemsList  = $requestItems[$r->id] ?? [];
                                            ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-teal">
                                                    <?= $detailsObj ? esc($detailsObj->request_no) : 'TRX #' . $r->reference_id ?>
                                                </td>
                                                <td><span class="badge badge-light border font-weight-bold text-dark"><?= $detailsObj ? esc($detailsObj->department ?: 'Apotek Farmasi') : '-' ?></span></td>
                                                <td><strong><?= $detailsObj ? esc($detailsObj->supplier_name) : '-' ?></strong></td>
                                                <td><?= $detailsObj ? esc($detailsObj->requester_name ?: 'Petugas Unit') : '-' ?></td>
                                                <td class="text-right font-weight-bold text-dark">
                                                    Rp <?= $detailsObj ? number_format($detailsObj->total_amount, 2, ',', '.') : '-' ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-warning font-weight-bold px-2 py-1">
                                                        <i class="fas fa-user-check mr-1"></i> Level <?= esc($r->step_level) ?>: <?= esc($r->step_name ?: 'Verifikasi') ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-outline-teal btn-xs font-weight-bold btn-preview-approval mr-1" 
                                                            data-id="<?= $r->id ?>"
                                                            data-refid="<?= $detailsObj ? $detailsObj->id : $r->reference_id ?>"
                                                            data-pr="<?= $detailsObj ? esc($detailsObj->request_no) : '' ?>"
                                                            data-supplier="<?= $detailsObj ? esc($detailsObj->supplier_name) : '' ?>"
                                                            data-dept="<?= $detailsObj ? esc($detailsObj->department) : '' ?>"
                                                            data-total="<?= $detailsObj ? number_format($detailsObj->total_amount, 2, ',', '.') : '0' ?>"
                                                            data-items='<?= json_encode($itemsList) ?>'
                                                            title="Lihat Dokumen Pengajuan">
                                                        <i class="fas fa-file-invoice"></i> Dokumen
                                                    </button>
                                                    <button class="btn btn-success btn-xs font-weight-bold btn-decision" 
                                                            data-id="<?= $r->id ?>" 
                                                            data-no="<?= $detailsObj ? esc($detailsObj->request_no) : 'TRX #' . $r->reference_id ?>"
                                                            data-action="approve" 
                                                            title="Setujui Pengajuan">
                                                        <i class="fas fa-check"></i> Setuju
                                                    </button>
                                                    <button class="btn btn-danger btn-xs font-weight-bold btn-decision ml-1" 
                                                            data-id="<?= $r->id ?>" 
                                                            data-no="<?= $detailsObj ? esc($detailsObj->request_no) : 'TRX #' . $r->reference_id ?>"
                                                            data-action="reject" 
                                                            title="Tolak Pengajuan">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-double text-success fa-2x mb-2 d-block"></i>
                                                Tidak ada pengajuan transaksi yang menunggu persetujuan Anda saat ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: APPROVAL HISTORY LOGS -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-history" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Riwayat Keputusan Persetujuan Transaksi</h6>
                                <small class="text-muted">Jejak audit (*audit trail*) verifikasi transaksi pengadaan dan operasional</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">Tipe & ID Ref</th>
                                        <th>Level & Tahap Persetujuan</th>
                                        <th>Pejabat / Penyetuju</th>
                                        <th class="text-center">Status Keputusan</th>
                                        <th>Catatan / Justifikasi</th>
                                        <th>Waktu Proses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($historyRequests)): ?>
                                        <?php foreach ($historyRequests as $hr): ?>
                                            <tr>
                                                <td class="text-center font-weight-bold text-teal">
                                                    <?= esc($hr->transaction_type) ?> #<?= esc($hr->reference_id) ?>
                                                </td>
                                                <td>Level <?= esc($hr->step_level) ?>: <?= esc($hr->step_name ?: 'Verifikator') ?></td>
                                                <td><strong><?= esc($hr->approver_name ?: 'Administrator') ?></strong></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $hr->status === 'approved' ? 'success' : 'danger' ?> text-uppercase px-2 py-1">
                                                        <i class="fas fa-<?= $hr->status === 'approved' ? 'check-circle' : 'times-circle' ?> mr-1"></i>
                                                        <?= strtoupper(esc($hr->status)) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($hr->notes ?: '-') ?></td>
                                                <td><?= date('d/m/Y H:i:s', strtotime($hr->updated_at ?? $hr->created_at)) ?> WITA</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat persetujuan tercatat.</td>
                                        </tr>
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

<!-- ========================================================================= -->
<!-- MODAL: KEPUTUSAN APPROVAL (SETUJU / TOLAK) -->
<!-- ========================================================================= -->
<div class="modal fade" id="decisionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2" id="modal-decision-header">
                <h6 class="modal-title font-weight-bold text-dark" id="modalDecisionTitle">
                    <i class="fas fa-clipboard-check mr-1 text-teal"></i> Keputusan Persetujuan Transaksi
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('procurement/approval') ?>" method="post">
                <input type="hidden" name="request_id" id="modal-request-id">
                <input type="hidden" name="action" id="modal-action">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="alert alert-light border p-2 mb-3 text-sm">
                        Dokumen: <strong id="lbl-decision-doc" class="text-teal"></strong><br>
                        Tindakan: <strong id="lbl-decision-action" class="text-uppercase"></strong>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold">Catatan / Alasan Keputusan (Wajib): <span class="text-danger">*</span></label>
                        <textarea name="notes" id="modal-notes" class="form-control form-control-sm" rows="3" placeholder="Masukkan catatan persetujuan atau alasan penolakan pengadaan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold" id="btn-submit-decision">
                        Konfirmasi Keputusan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: PREVIEW LEMBAR DOKUMEN PENGADAAN (PR) SEBELUM KEPUTUSAN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalPreviewApproval" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-file-lines mr-1 text-teal"></i> Pratinjau Dokumen Pengajuan <span id="appr-pr-no"></span>
                </h6>
                <div>
                    <a href="#" id="btn-print-appr" target="_blank" class="btn btn-teal btn-xs font-weight-bold mr-2 text-white">
                        <i class="fas fa-print mr-1"></i> Cetak / Download PDF
                    </a>
                    <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Paper Document Sheet Inside Modal -->
                <div class="bg-white p-4 rounded shadow-sm border" style="border-color: #cbd5e1 !important; font-family: 'Inter', sans-serif;">
                    <!-- Letterhead -->
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom" style="border-color: #0d9488 !important; border-bottom-width: 2px !important;">
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 rounded mr-3 text-teal border" style="font-size: 24px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-teal mb-0">SAWAMAWA MEDICAL CENTER</h5>
                                <small class="text-secondary font-weight-bold d-block">Logistik Farmasi, Pelayanan Medis & Pengadaan</small>
                                <small class="text-muted" style="font-size: 11px;">Jl. Kebangsaan No. 12, Sumbawa Besar, NTB</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-warning px-2 py-1 text-uppercase text-dark font-weight-bold" style="letter-spacing: 0.5px;"><i class="fas fa-hourglass-half mr-1"></i> MENUNGGU PERSETUJUAN</span>
                            <div class="font-weight-bold text-teal mt-1" style="font-size: 15px; font-family: monospace;" id="lbl-appr-no"></div>
                        </div>
                    </div>

                    <!-- Metadata Box -->
                    <div class="row bg-light p-3 rounded mb-3 border" style="font-size: 12px;">
                        <div class="col-md-6">
                            <div class="mb-1"><strong>Unit Pemohon:</strong> <span id="appr-pr-dept" class="font-weight-bold"></span></div>
                            <div class="mb-1"><strong>Supplier Tujuan:</strong> <span id="appr-pr-supplier" class="text-teal font-weight-bold"></span></div>
                            <div><strong>Keperluan:</strong> <span id="appr-pr-desc" class="text-muted">Pengadaan kebutuhan unit</span></div>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="mb-1"><strong>Status Pengajuan:</strong> <span class="badge badge-warning text-dark font-weight-bold">PENDING APPROVAL</span></div>
                            <div><strong>Sistem Verifikasi:</strong> <span class="badge badge-light border">Multi-Tier Approvals</span></div>
                        </div>
                    </div>

                    <!-- Table Items -->
                    <h6 class="font-weight-bold text-dark mb-2" style="font-size: 12.5px;"><i class="fas fa-boxes-stacked text-teal mr-1"></i> Rincian Kebutuhan Barang / Obat yang Diminta:</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm mb-0" id="table-appr-items" style="font-size: 12.5px;">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 35px;" class="text-center">NO</th>
                                    <th>NAMA ITEM / OBAT</th>
                                    <th style="width: 70px;" class="text-center">QTY</th>
                                    <th style="width: 70px;">SATUAN</th>
                                    <th style="width: 140px;" class="text-right">EST. HARGA SATUAN</th>
                                    <th style="width: 150px;" class="text-right">EST. SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="5" class="text-right text-dark">ESTIMASI TOTAL ANGGARAN:</td>
                                    <td class="text-right text-teal h6 mb-0 font-weight-bold" id="appr-pr-total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Verification Footer Seal -->
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top text-muted" style="font-size: 11px;">
                        <div>
                            <i class="fas fa-shield-alt text-teal mr-1"></i> Terverifikasi digital melalui SIM-Klinik Sawamawa Medical Center
                        </div>
                        <div>
                            Pusat Persetujuan Transaksi
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <div>
                    <a href="#" id="btn-print-appr-bottom" target="_blank" class="btn btn-teal btn-sm font-weight-bold text-white mr-2">
                        <i class="fas fa-print mr-1"></i> Cetak / Download PDF
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger btn-sm font-weight-bold modal-btn-reject ml-1">
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold modal-btn-approve ml-1">
                        <i class="fas fa-check mr-1"></i> Setujui Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let currentApprReqId = null;
        let currentApprDocNo = null;

        // Decision button handler (Approve / Reject)
        $('.btn-decision').click(function() {
            const id = $(this).data('id');
            const no = $(this).data('no');
            const action = $(this).data('action');

            $('#modal-request-id').val(id);
            $('#modal-action').val(action);
            $('#lbl-decision-doc').text(no);

            if (action === 'approve') {
                $('#modal-decision-header').removeClass('bg-danger').addClass('bg-teal');
                $('#modalDecisionTitle').html('<i class="fas fa-check-circle mr-1"></i> Persetujuan Transaksi (Approve)');
                $('#lbl-decision-action').text('Setujui Pengajuan (Approve)').attr('class', 'text-success font-weight-bold');
                $('#btn-submit-decision').removeClass('btn-danger').addClass('btn-success').html('<i class="fas fa-check mr-1"></i> Setujui Pengajuan');
                $('#modal-notes').val('Disetujui untuk diproses ke Purchase Order.');
            } else {
                $('#modal-decision-header').removeClass('bg-teal').addClass('bg-danger');
                $('#modalDecisionTitle').html('<i class="fas fa-times-circle mr-1"></i> Penolakan Transaksi (Reject)');
                $('#lbl-decision-action').text('Tolak Pengajuan (Reject)').attr('class', 'text-danger font-weight-bold');
                $('#btn-submit-decision').removeClass('btn-success').addClass('btn-danger').html('<i class="fas fa-times mr-1"></i> Tolak Pengajuan');
                $('#modal-notes').val('');
            }

            $('#decisionModal').modal('show');
        });

        // Preview Approval Items handler
        $('.btn-preview-approval').click(function() {
            const reqId = $(this).data('id');
            const prNo = $(this).data('pr');
            const refId = $(this).data('refid') || reqId;
            const supName = $(this).data('supplier');
            const dept = $(this).data('dept');
            const total = $(this).data('total');
            const items = $(this).data('items') || [];

            currentApprReqId = reqId;
            currentApprDocNo = prNo;

            $('#appr-pr-no').text('(' + prNo + ')');
            $('#lbl-appr-no').text(prNo);
            $('#appr-pr-supplier').text(supName);
            $('#appr-pr-dept').text(dept || 'Apotek Farmasi');
            $('#appr-pr-total').text('Rp ' + total);

            const printUrl = '<?= base_url('procurement/cetak-pr/') ?>' + refId;
            $('#btn-print-appr, #btn-print-appr-bottom').attr('href', printUrl);

            let html = '';
            if (items && items.length > 0) {
                let no = 1;
                items.forEach(function(it) {
                    html += `
                        <tr>
                            <td class="text-center font-weight-bold text-muted">${no++}</td>
                            <td><strong>${it.item_name}</strong></td>
                            <td class="text-center font-weight-bold">${it.qty}</td>
                            <td>${it.unit}</td>
                            <td class="text-right">Rp ${parseFloat(it.estimated_price).toLocaleString('id-ID')}</td>
                            <td class="text-right font-weight-bold text-dark">Rp ${parseFloat(it.subtotal).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="6" class="text-center text-muted py-3">Pengadaan bersifat paket anggaran (Rp ${total})</td></tr>`;
            }

            $('#table-appr-items tbody').html(html);
            $('#modalPreviewApproval').modal('show');
        });

        // Quick action from preview modal
        $('.modal-btn-approve').click(function() {
            $('#modalPreviewApproval').modal('hide');
            $('.btn-decision[data-id="' + currentApprReqId + '"][data-action="approve"]').click();
        });

        $('.modal-btn-reject').click(function() {
            $('#modalPreviewApproval').modal('hide');
            $('.btn-decision[data-id="' + currentApprReqId + '"][data-action="reject"]').click();
        });
    });
</script>
<?= $this->endSection() ?>
