<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        
        <!-- Bar Pengawasan & Filter Dokter / Poli (Time Keeper Toolbar) -->
        <div class="card bg-light border shadow-sm mb-3">
            <div class="card-body p-3">
                <form action="<?= base_url('klinik/antrean') ?>" method="get" id="form-filter-antrean">
                    <input type="hidden" name="tab" value="<?= esc($current_tab ?? 'active') ?>">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold text-secondary mb-1"><i class="fas fa-stethoscope text-teal mr-1"></i> Filter Poliklinik:</label>
                            <select name="poly_id" id="filter-poly-select" class="form-control form-control-sm" onchange="$('#form-filter-antrean').submit();">
                                <option value="all">-- Semua Poliklinik --</option>
                                <?php if (!empty($polyclinics)): foreach ($polyclinics as $poly): ?>
                                    <option value="<?= $poly->id ?>" <?= ($selected_poly_id == $poly->id) ? 'selected' : '' ?>>
                                        <?= esc($poly->name) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold text-secondary mb-1"><i class="fas fa-user-doctor text-teal mr-1"></i> Filter Dokter Pemeriksa:</label>
                            <select name="doctor_id" id="filter-doctor-select" class="form-control form-control-sm" onchange="$('#form-filter-antrean').submit();">
                                <option value="all">-- Semua Dokter (Mode Time Keeper) --</option>
                                <?php if (!empty($doctors)): foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc->id ?>" <?= ($selected_doctor_id == $doc->id) ? 'selected' : '' ?>>
                                        <?= esc($doc->name) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="text-xs font-weight-bold text-secondary mb-1"><i class="fas fa-stopwatch text-teal mr-1"></i> Status SLA Waktu Tunggu:</label>
                            <div class="d-flex align-items-center" style="gap: 4px;">
                                <span class="badge badge-success py-1 px-2 font-weight-bold" style="font-size: 11px;"><i class="fas fa-check-circle mr-1"></i> &lt;15m</span>
                                <span class="badge badge-warning text-dark py-1 px-2 font-weight-bold" style="font-size: 11px;"><i class="fas fa-exclamation-triangle mr-1"></i> 15-30m</span>
                                <span class="badge badge-danger py-1 px-2 font-weight-bold" style="font-size: 11px;"><i class="fas fa-clock mr-1"></i> &gt;30m Overdue</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-right mt-2 mt-md-0">
                            <a href="<?= base_url('klinik/antrean?tab=' . esc($current_tab ?? 'active') . '&doctor_id=all&poly_id=all') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1" title="Reset Filter">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                            <a href="<?= base_url('klinik/display') ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm mr-1">
                                <i class="fas fa-tv mr-1"></i> TV
                            </a>
                            <a href="<?= base_url('klinik/kiosk') ?>" target="_blank" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                                <i class="fas fa-desktop mr-1"></i> Kiosk
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Navigasi Status Antrean -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <ul class="nav nav-pills" id="antrean-status-tabs">
                <li class="nav-item mr-2 mb-2">
                    <a class="nav-link font-weight-bold <?= ($current_tab === 'active') ? 'active bg-teal shadow-sm' : 'bg-white text-dark shadow-xs' ?>" href="<?= base_url('klinik/antrean?tab=active' . ($selected_doctor_id ? '&doctor_id='.$selected_doctor_id : '') . ($selected_poly_id ? '&poly_id='.$selected_poly_id : '')) ?>">
                        <i class="fas fa-hourglass-half mr-1"></i> Antrean Aktif / Belum Selesai 
                        <span class="badge badge-light ml-1 font-weight-bold" id="stat-active"><?= esc($count_active ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item mr-2 mb-2">
                    <a class="nav-link font-weight-bold <?= ($current_tab === 'completed') ? 'active bg-success shadow-sm' : 'bg-white text-dark shadow-xs' ?>" href="<?= base_url('klinik/antrean?tab=completed' . ($selected_doctor_id ? '&doctor_id='.$selected_doctor_id : '') . ($selected_poly_id ? '&poly_id='.$selected_poly_id : '')) ?>">
                        <i class="fas fa-check-circle mr-1"></i> Selesai Dilayani Hari Ini 
                        <span class="badge badge-light ml-1 font-weight-bold" id="stat-done"><?= esc($count_completed ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item mr-2 mb-2">
                    <a class="nav-link font-weight-bold <?= ($current_tab === 'cancelled') ? 'active bg-danger shadow-sm' : 'bg-white text-dark shadow-xs' ?>" href="<?= base_url('klinik/antrean?tab=cancelled' . ($selected_doctor_id ? '&doctor_id='.$selected_doctor_id : '') . ($selected_poly_id ? '&poly_id='.$selected_poly_id : '')) ?>">
                        <i class="fas fa-times-circle mr-1"></i> Dibatalkan 
                        <span class="badge badge-light ml-1 font-weight-bold"><?= esc($count_cancelled ?? 0) ?></span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link font-weight-bold <?= ($current_tab === 'all') ? 'active bg-dark shadow-sm' : 'bg-white text-dark shadow-xs' ?>" href="<?= base_url('klinik/antrean?tab=all' . ($selected_doctor_id ? '&doctor_id='.$selected_doctor_id : '') . ($selected_poly_id ? '&poly_id='.$selected_poly_id : '')) ?>">
                        <i class="fas fa-list mr-1"></i> Semua Kunjungan 
                        <span class="badge badge-light ml-1 font-weight-bold" id="stat-total"><?= esc($count_total ?? 0) ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card card-outline <?= ($current_tab === 'completed') ? 'card-success' : (($current_tab === 'cancelled') ? 'card-danger' : 'card-teal') ?> shadow">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap py-2">
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 16px;">
                    <?php if ($current_tab === 'completed'): ?>
                        <i class="fas fa-check-circle text-success mr-1"></i> Riwayat Pasien yang Sudah Selesai Dilayani Hari Ini
                    <?php elseif ($current_tab === 'cancelled'): ?>
                        <i class="fas fa-times-circle text-danger mr-1"></i> Daftar Antrean yang Dibatalkan
                    <?php else: ?>
                        <i class="fas fa-hourglass-half text-teal mr-1"></i> Antrean Pasien Aktif (Menunggu / Sedang Dilayani)
                    <?php endif; ?>
                </h3>
                <span class="text-muted ml-auto text-xs">
                    <i class="fas fa-arrow-down-1-9 text-warning mr-1"></i> <strong>Urutan #1, #2, #3</strong> menunjukkan urutan pendaftar awal (Giliran masuk).
                </span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover mb-0" id="table-antrean">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 140px;" class="text-center">Urutan &amp; No. Antrean</th>
                            <th>No. Kunjungan</th>
                            <th>Nama Pasien</th>
                            <th>Layanan Tujuan</th>
                            <th>Dokter Pelaksana</th>
                            <th class="text-center">Status Pelayanan</th>
                            <th>Jam Daftar &amp; SLA</th>
                            <th class="text-center" style="width: 260px;">Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody id="live-antrean-body">
                        <?php if (empty($queues)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-ticket text-teal mr-2"></i> 
                                    <?= ($current_tab === 'completed') ? 'Belum ada pasien yang selesai dilayani hari ini.' : 'Tidak ada antrean pasien untuk filter ini.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                        <?php foreach ($queues as $q): ?>
                            <?php $tier = strtolower($q->membership_tier ?? 'regular'); ?>
                            <tr class="<?= ($tier === 'vip' || $tier === 'platinum') ? 'table-warning' : (($tier === 'gold') ? 'table-light' : '') ?>">
                                <td class="text-center align-middle">
                                    <span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size:12px;" title="Urutan Pendaftar #<?= esc($q->arrival_seq ?? 1) ?>"><i class="fas fa-arrow-down-1-9 mr-1"></i>#<?= esc($q->arrival_seq ?? 1) ?></span>
                                    <?php if ($tier === 'vip' || $tier === 'platinum'): ?>
                                        <span class="badge badge-dark h4 py-2 px-3 font-weight-bold shadow-sm" style="font-size: 15px; background:#0f172a; border: 1.5px solid #ffc107; color:#ffc107;"><i class="fas fa-gem mr-1"></i><?= esc($q->queue_no) ?></span>
                                    <?php elseif ($tier === 'gold'): ?>
                                        <span class="badge badge-warning text-dark h4 py-2 px-3 font-weight-bold shadow-sm" style="font-size: 15px; background:#ffc107;"><i class="fas fa-crown mr-1"></i><?= esc($q->queue_no) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-teal h4 py-2 px-3 font-weight-bold" style="font-size: 15px;"><?= esc($q->queue_no) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><strong><?= esc($q->no_visit) ?></strong></td>
                                <td class="align-middle">
                                    <strong><?= esc($q->patient_name) ?></strong>
                                    <?php if ($tier === 'gold'): ?>
                                        <span class="badge badge-warning text-dark ml-1 font-weight-bold shadow-sm" style="background:#ffc107; font-size:10px;"><i class="fas fa-crown mr-1"></i>Gold</span>
                                    <?php elseif ($tier === 'vip' || $tier === 'platinum'): ?>
                                        <span class="badge badge-dark ml-1 font-weight-bold shadow-sm" style="background:#0f172a; border: 1px solid #ffc107; color: #ffc107; font-size:10px;"><i class="fas fa-gem mr-1"></i>VIP</span>
                                    <?php endif; ?>
                                    <?php if (!empty($q->has_triage)): ?>
                                        <span class="badge badge-success px-2 py-1 ml-1" style="font-size: 10px;"><i class="fas fa-check-circle mr-1"></i> TTV Lengkap</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark px-2 py-1 ml-1" style="font-size: 10px;"><i class="fas fa-heartbeat mr-1"></i> Belum TTV</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($q->visit_type === 'tindakan'): ?>
                                        <span class="badge badge-secondary mr-1">TINDAKAN</span>
                                        <?php if ($q->parent_tindakan_name): ?>
                                            <span class="text-teal font-weight-bold"><?= esc($q->parent_tindakan_name) ?> &raquo; </span>
                                        <?php endif; ?>
                                        <strong><?= esc($q->tindakan_name ?? 'Tindakan Medis') ?></strong>
                                    <?php else: ?>
                                        <span class="badge badge-info mr-1">POLI</span>
                                        <strong><?= esc($q->polyclinic_name) ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($q->doctor_name): ?>
                                        <i class="fas fa-user-md text-teal mr-1"></i> <?= esc($q->doctor_name) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php 
                                    $st = strtolower($q->status);
                                    $badgeColor = [
                                        'waiting'      => 'secondary',
                                        'called'       => 'primary',
                                        'triage'       => 'primary',
                                        'examining'    => 'success',
                                        'cashier'      => 'warning text-dark',
                                        'prescription' => 'info',
                                        'completed'    => 'success',
                                        'no-show'      => 'warning text-dark',
                                        'cancelled'    => 'danger'
                                    ][$st] ?? 'secondary';

                                    $statusLabel = [
                                        'waiting'      => 'MENUNGGU',
                                        'called'       => 'DIPANGGIL',
                                        'triage'       => 'SKRINING TTV',
                                        'examining'    => 'PERIKSA DOKTER',
                                        'cashier'      => 'KASIR BAYAR',
                                        'prescription' => 'FARMASI OBAT',
                                        'completed'    => 'SELESAI',
                                        'cancelled'    => 'BATAL'
                                    ][$st] ?? strtoupper($st);
                                    ?>
                                    <span class="badge badge-<?= $badgeColor ?> py-1 px-2 font-weight-bold" style="font-size: 11px;"><?= $statusLabel ?></span>
                                </td>
                                <td class="align-middle text-nowrap">
                                    <?= date('H:i:s', strtotime($q->created_at)) ?><br>
                                    <span class="badge badge-<?= esc($q->sla_badge ?? 'success') ?> font-weight-bold" style="font-size: 10.5px;" title="Lama Menunggu"><i class="fas fa-stopwatch mr-1"></i><?= esc($q->wait_minutes ?? 0) ?> mnt</span>
                                </td>
                                <td class="text-center align-middle text-nowrap">
                                    <?php 
                                        $targetName = ($q->visit_type === 'tindakan') ? 'Ruang Tindakan ' . ($q->tindakan_name ?? 'Medis') : 'Poliklinik ' . $q->polyclinic_name;
                                    ?>

                                    <?php if ($st !== 'completed' && $st !== 'cancelled'): ?>
                                        <!-- Tombol Panggil Suara Murni -->
                                        <button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm mr-1 btn-voice-call"
                                                data-queue="<?= esc($q->queue_no) ?>"
                                                data-name="<?= esc($q->patient_name) ?>"
                                                data-target="<?= esc($targetName) ?>"
                                                data-doctor="<?= esc($q->doctor_name ?? 'Dokter Jaga') ?>"
                                                title="Panggil Antrean dengan Suara Audio">
                                            <i class="fas fa-volume-up"></i> Panggil
                                        </button>

                                        <!-- Tombol Alihkan Dokter (Time Keeper Switch) -->
                                        <button type="button" class="btn btn-outline-info btn-sm font-weight-bold shadow-sm mr-1 btn-reassign-doc"
                                                data-visitid="<?= esc($q->visit_id ?? $q->id) ?>"
                                                data-name="<?= esc($q->patient_name) ?>"
                                                data-doctorid="<?= esc($q->doctor_id ?? '') ?>"
                                                title="Alihkan Dokter Pemeriksa Pasien">
                                            <i class="fas fa-user-doctor"></i>
                                        </button>

                                        <!-- Form Status -->
                                        <form action="<?= base_url('klinik/antrean') ?>" method="post" class="d-inline-block">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="queue_id" value="<?= $q->id ?>">
                                            
                                            <?php if ($st === 'waiting'): ?>
                                                <input type="hidden" name="status" value="called">
                                                <button type="submit" class="btn btn-primary btn-sm font-weight-bold shadow-sm btn-status-call"
                                                        data-queue="<?= esc($q->queue_no) ?>"
                                                        data-name="<?= esc($q->patient_name) ?>"
                                                        data-target="<?= esc($targetName) ?>"
                                                        data-doctor="<?= esc($q->doctor_name ?? 'Dokter Jaga') ?>">
                                                    <i class="fas fa-play mr-1"></i> Skrining
                                                </button>
                                            <?php elseif ($st === 'called' || $st === 'triage'): ?>
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-success btn-sm font-weight-bold shadow-sm"><i class="fas fa-check mr-1"></i> Selesai</button>
                                            <?php endif; ?>
                                        </form>

                                        <a href="<?= base_url('klinik/antrean/delete/' . $q->id) ?>" class="btn btn-outline-danger btn-sm font-weight-bold shadow-sm ml-1" onclick="return confirm('Batalkan antrean pasien <?= esc($q->patient_name) ?> (No. <?= esc($q->queue_no) ?>)?');" title="Hapus / Batalkan Antrean">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-success py-1 px-2 font-weight-bold"><i class="fas fa-check mr-1"></i> Pelayanan Tuntas</span>
                                    <?php endif; ?>
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

<!-- Modal Alihkan Dokter Pemeriksa (Time Keeper Feature) -->
<div class="modal fade" id="modal-reassign-doctor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white py-2">
                <h5 class="modal-title font-weight-bold" style="font-size: 16px;"><i class="fas fa-user-doctor mr-2"></i> Alihkan Dokter Pemeriksa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('klinik/antrean') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reassign_doctor">
                <input type="hidden" name="visit_id" id="reassign-visit-id" value="">
                <div class="modal-body p-3">
                    <p class="text-sm text-secondary mb-2">
                        Alihkan antrean pasien <strong id="reassign-patient-name" class="text-dark"></strong> ke dokter jaga lainnya untuk mempercepat pelayanan:
                    </p>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark mb-1">Pilih Dokter Pengganti:</label>
                        <select name="doctor_id" id="reassign-doctor-select" class="form-control" required>
                            <?php if (!empty($doctors)): foreach ($doctors as $d): ?>
                                <option value="<?= $d->id ?>"><?= esc($d->name) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm font-weight-bold"><i class="fas fa-check mr-1"></i> Simpan Pengalihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // 1. Delegasi Event Tombol Panggil Suara Murni (Audio Only)
        $(document).on('click', '.btn-voice-call', function(e) {
            e.preventDefault();
            const qNo = $(this).data('queue');
            const pName = $(this).data('name');
            const target = $(this).data('target');

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'poliklinik',
                    counter_name: target,
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: 1
                });
            } else if (window.VCM && typeof window.VCM.callPatient === 'function') {
                window.VCM.callPatient(qNo, pName, target);
            }
        });

        // 2. Tombol Mulai Skrining (Ubah Status + Panggil Suara Otomatis)
        $(document).on('click', '.btn-status-call', function(e) {
            const qNo = $(this).data('queue');
            const pName = $(this).data('name');
            const target = $(this).data('target');

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'triage',
                    counter_name: target,
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: 1
                });
            }
        });

        // 3. Tombol Modal Alihkan Dokter (Reassign)
        $(document).on('click', '.btn-reassign-doc', function(e) {
            e.preventDefault();
            const visitId = $(this).data('visitid');
            const patientName = $(this).data('name');
            const docId = $(this).data('doctorid');

            $('#reassign-visit-id').val(visitId);
            $('#reassign-patient-name').text(patientName);
            if (docId) {
                $('#reassign-doctor-select').val(docId);
            }
            $('#modal-reassign-doctor').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
