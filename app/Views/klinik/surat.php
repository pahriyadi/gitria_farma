<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-file-medical text-teal mr-2"></i> Surat Keterangan Medis & Rujukan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Klinik</li>
                    <li class="breadcrumb-item active">Surat Medis</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Action Card -->
        <div class="card card-outline card-teal mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-list-alt mr-1"></i> Daftar Penerbitan Surat Keterangan Medis
                </h3>
                <div class="card-tools">
                    <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#letterAddModal">
                        <i class="fas fa-plus-circle mr-1"></i> Terbitkan Surat Baru
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 datatable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th>No. Surat</th>
                                <th>Jenis Surat</th>
                                <th>Nama Pasien / No. RM</th>
                                <th>Dokter Penerbit</th>
                                <th>Tanggal Surat</th>
                                <th>Keterangan / Diagnosa</th>
                                <th style="width: 140px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($letters as $l): ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <span class="badge badge-light border font-weight-bold text-dark" style="font-size:12px;">
                                            <?= esc($l->letter_no) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($l->letter_type === 'sakit'): ?>
                                            <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-bed mr-1"></i> Surat Sakit (<?= $l->duration_days ?> Hari)</span>
                                        <?php elseif ($l->letter_type === 'sehat'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-heartbeat mr-1"></i> Surat Keterangan Sehat</span>
                                        <?php else: ?>
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-hospital mr-1"></i> Surat Rujukan Pasien</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-dark"><?= esc($l->patient_name) ?></strong><br>
                                        <small class="text-muted">No. RM: <?= esc($l->no_rm) ?> | <?= $l->gender === 'L' ? 'L' : 'P' ?></small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($l->doctor_name ?? 'Dokter Pemeriksa') ?></div>
                                        <small class="text-muted">SIP: <?= esc($l->sip_number ?: '-') ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($l->letter_date)) ?></td>
                                    <td>
                                        <?php if ($l->letter_type === 'sakit'): ?>
                                            <small class="text-secondary d-block">Masa Istirahat: <?= date('d/m/Y', strtotime($l->start_date)) ?> s/d <?= date('d/m/Y', strtotime($l->end_date)) ?></small>
                                            <small class="text-muted">Diagnosa: <?= esc($l->diagnosis ?: '-') ?></small>
                                        <?php elseif ($l->letter_type === 'sehat'): ?>
                                            <small class="text-secondary d-block">TD: <?= esc($l->blood_pressure ?: '-') ?> | BB: <?= $l->weight ?>kg | TB: <?= $l->height ?>cm</small>
                                            <small class="text-muted">Keperluan: <?= esc($l->purpose ?: '-') ?></small>
                                        <?php else: ?>
                                            <small class="text-secondary d-block">Tujuan: <strong><?= esc($l->referral_destination) ?></strong> (<?= esc($l->referral_poly) ?>)</small>
                                            <small class="text-muted">Alasan: <?= esc($l->referral_reason ?: '-') ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('klinik/cetak-surat/' . $l->id) ?>" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold mr-1" title="Cetak Surat Resmi">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                        <form action="<?= base_url('klinik/surat') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus surat ini?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_letter">
                                            <input type="hidden" name="letter_id" value="<?= $l->id ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

<!-- ========================================================================= -->
<!-- MODAL: TERBITKAN SURAT MEDIS BARU -->
<!-- ========================================================================= -->
<div class="modal fade" id="letterAddModal" tabindex="-1" role="dialog" aria-labelledby="letterAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="letterAddModalLabel">
                    <i class="fas fa-file-medical mr-1"></i> Form Penerbitan Surat Keterangan Medis
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/surat') ?>" method="post">
                <input type="hidden" name="action" value="create_letter">
                <?= csrf_field() ?>
                <div class="modal-body">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Pilih Kunjungan Pasien Terkait <span class="text-danger">*</span></label>
                            <select name="visit_select" id="visit_select" class="form-control font-weight-bold" required>
                                <option value="">-- Cari Pasien / No. Kunjungan --</option>
                                <?php foreach ($recentVisits as $rv): 
                                    $diagStr = $rv->assessment ? ($rv->assessment . ($rv->icd10_code ? ' (' . $rv->icd10_code . ')' : '')) : '';
                                ?>
                                    <option value="<?= $rv->id ?>" 
                                            data-patient="<?= $rv->patient_id ?>" 
                                            data-doctor="<?= $rv->doctor_id ?>"
                                            data-bp="<?= esc($rv->blood_pressure ?? '') ?>"
                                            data-weight="<?= esc($rv->weight ?? '') ?>"
                                            data-height="<?= esc($rv->height ?? '') ?>"
                                            data-diagnosis="<?= esc($diagStr) ?>">
                                        [<?= esc($rv->no_visit) ?>] <?= esc($rv->patient_name) ?> (RM: <?= esc($rv->no_rm) ?>) - <?= date('d/m/Y', strtotime($rv->visit_date)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-teal font-weight-bold d-block mt-1" id="autofill-hint" style="display:none;"></small>
                            <input type="hidden" name="visit_id" id="hidden_visit_id">
                            <input type="hidden" name="patient_id" id="hidden_patient_id">
                        </div>

                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Dokter Pemeriksa <span class="text-danger">*</span></label>
                            <select name="doctor_id" id="doctor_select" class="form-control font-weight-bold" required>
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= $d->id ?>"><?= esc($d->name) ?> (SIP: <?= esc($d->sip_number ?: '-') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="letter_type" id="letter_type" class="form-control font-weight-bold text-teal" required>
                                <option value="sakit">1. Surat Keterangan Sakit (SKS / Istirahat)</option>
                                <option value="sehat">2. Surat Keterangan Sehat (SKD)</option>
                                <option value="rujukan">3. Surat Rujukan Pasien (SRP ke RS/Faskes Lanjutan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tanggal Terbit Surat <span class="text-danger">*</span></label>
                            <input type="date" name="letter_date" class="form-control font-weight-bold" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <!-- PANE 1: SURAT SAKIT -->
                    <div id="pane-letter-sakit" class="card bg-light p-3 border">
                        <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-bed mr-1"></i> Rincian Surat Keterangan Sakit</h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Lama Istirahat (Hari) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" id="input_duration" class="form-control font-weight-bold" value="3" min="1">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Mulai Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="input_start_date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Sampai Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="input_end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+2 days')) ?>">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>Diagnosa Medis / Alasan Istirahat</label>
                            <input type="text" name="diagnosis" class="form-control" placeholder="Contoh: Febris H-2 / Gastritis Akut / ISPA">
                        </div>
                    </div>

                    <!-- PANE 2: SURAT SEHAT -->
                    <div id="pane-letter-sehat" class="card bg-light p-3 border" style="display:none;">
                        <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-heartbeat mr-1"></i> Rincian Hasil Pemeriksaan Fisik</h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Tekanan Darah</label>
                                <input type="text" name="blood_pressure" class="form-control" placeholder="120/80 mmHg">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control" placeholder="60">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="height" class="form-control" placeholder="165">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tes Buta Warna</label>
                                <select name="color_blind" class="form-control">
                                    <option value="normal">Normal (Tidak Buta Warna)</option>
                                    <option value="partial">Buta Warna Parsial</option>
                                    <option value="total">Buta Warna Total</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-0">
                                <label>Kesimpulan Kesehatan</label>
                                <select name="health_status" class="form-control font-weight-bold">
                                    <option value="sehat">SEHAT BADAN (Layak Beraktivitas/Bekerja)</option>
                                    <option value="tidak_sehat">TIDAK SEHAT</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-0">
                                <label>Keperluan Surat</label>
                                <input type="text" name="purpose" class="form-control" placeholder="Contoh: Melamar Pekerjaan / Persyaratan Pendidikan">
                            </div>
                        </div>
                    </div>

                    <!-- PANE 3: SURAT RUJUKAN -->
                    <div id="pane-letter-rujukan" class="card bg-light p-3 border" style="display:none;">
                        <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-hospital mr-1"></i> Rincian Surat Rujukan Pasien</h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Fasilitas Rujukan Tujuan (RS) <span class="text-danger">*</span></label>
                                <input type="text" name="referral_destination" class="form-control font-weight-bold" placeholder="Contoh: RSUD Sumbawa">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Poli / Spesialis Tujuan <span class="text-danger">*</span></label>
                                <input type="text" name="referral_poly" class="form-control" placeholder="Contoh: Poli Penyakit Dalam / Poli Anak">
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label>Alasan Rujukan & Tindak Lanjut yang Diharapkan</label>
                            <textarea name="referral_reason" class="form-control" rows="2" placeholder="Contoh: Mohon evaluasi lanjutan dan penanganan spesialis..."></textarea>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label>Catatan Tambahan / Keterangan Dokter</label>
                        <input type="text" name="notes" class="form-control" placeholder="Opsional...">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Terbitkan & Simpan Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#visit_select').change(function() {
            const opt = $(this).find(':selected');
            const visitId = opt.val();
            $('#hidden_visit_id').val(visitId);
            $('#hidden_patient_id').val(opt.data('patient'));
            
            const docId = opt.data('doctor');
            if (docId) {
                $('#doctor_select').val(docId);
            }

            if (visitId) {
                const bp = opt.data('bp') || '';
                const weight = opt.data('weight') || '';
                const height = opt.data('height') || '';
                const diag = opt.data('diagnosis') || '';

                // Auto-fill Surat Sakit
                if (diag) {
                    $('input[name="diagnosis"]').val(diag);
                }

                // Auto-fill Surat Rujukan
                if (diag && !$('textarea[name="referral_reason"]').val()) {
                    $('textarea[name="referral_reason"]').val('Evaluasi lanjutan dan tatalaksana komprehensif untuk diagnosa: ' + diag);
                }

                // Auto-fill Surat Sehat (TTV Fisik)
                if (bp) $('input[name="blood_pressure"]').val(bp);
                if (weight) $('input[name="weight"]').val(weight);
                if (height) $('input[name="height"]').val(height);

                let hintItems = [];
                if (diag) hintItems.push('Diagnosa: ' + diag);
                if (bp || weight || height) hintItems.push(`TTV: TD ${bp || '-'} mmHg, BB ${weight || '-'} kg, TB ${height || '-'} cm`);

                if (hintItems.length > 0) {
                    $('#autofill-hint').show().html('<i class="fas fa-magic mr-1"></i> Data TTV & Diagnosa kunjungan ter-autofill otomatis.');
                } else {
                    $('#autofill-hint').hide();
                }
            } else {
                $('#autofill-hint').hide();
            }
        });

        $('#letter_type').change(function() {
            const val = $(this).val();
            $('#pane-letter-sakit').hide();
            $('#pane-letter-sehat').hide();
            $('#pane-letter-rujukan').hide();

            if (val === 'sakit') {
                $('#pane-letter-sakit').fadeIn(150);
            } else if (val === 'sehat') {
                $('#pane-letter-sehat').fadeIn(150);
            } else if (val === 'rujukan') {
                $('#pane-letter-rujukan').fadeIn(150);
            }
        });
    });
</script>
<?= $this->endSection() ?>
