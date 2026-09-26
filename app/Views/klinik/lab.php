<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-vials text-teal mr-2"></i> Pemeriksaan & Hasil Laboratorium Klinik</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item">Klinik</li>
                    <li class="breadcrumb-item active">Laboratorium</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Main Card -->
        <div class="card card-outline card-teal mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-microscope mr-1"></i> Riwayat Hasil Pemeriksaan Laboratorium Pasien
                </h3>
                <div class="card-tools">
                    <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#labAddModal">
                        <i class="fas fa-plus-circle mr-1"></i> Input Hasil Tes Lab Baru
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 datatable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th>No. Lab</th>
                                <th>Nama Pasien / No. RM</th>
                                <th>Jenis Pemeriksaan</th>
                                <th>Hasil Pengukuran</th>
                                <th>Nilai Rujukan / Normal</th>
                                <th>Status Hasil</th>
                                <th>Tanggal & Petugas</th>
                                <th style="width: 130px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="live-lab-results-body">
                            <?php $no = 1; ?>
                            <?php foreach ($labResults as $lr): ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <span class="badge badge-light border font-weight-bold text-dark" style="font-size:12px;">
                                            <?= esc($lr->lab_no) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-dark"><?= esc($lr->patient_name) ?></strong><br>
                                        <small class="text-muted">RM: <?= esc($lr->no_rm) ?> | <?= $lr->gender === 'L' ? 'L' : 'P' ?></small>
                                    </td>
                                    <td>
                                        <strong class="text-dark"><?= esc($lr->test_type) ?></strong>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark" style="font-size: 15px;">
                                            <?= esc($lr->result_value) ?> <small class="text-muted"><?= esc($lr->unit) ?></small>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted font-weight-bold"><?= esc($lr->normal_range ?: '-') ?> <?= esc($lr->unit) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($lr->status === 'normal'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> NORMAL</span>
                                        <?php elseif ($lr->status === 'high'): ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up"></i> HIGH (TINGGI)</span>
                                        <?php elseif ($lr->status === 'low'): ?>
                                            <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-arrow-down"></i> LOW (RENDAH)</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> ABNORMAL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-xs text-dark font-weight-bold"><?= date('d/m/Y', strtotime($lr->test_date)) ?></div>
                                        <small class="text-muted"><?= esc($lr->officer_name) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('klinik/cetak-lab/' . $lr->id) ?>" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold mr-1" title="Cetak Lembar Lab">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                        <form action="<?= base_url('klinik/lab') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus hasil lab ini?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_lab">
                                            <input type="hidden" name="lab_id" value="<?= $lr->id ?>">
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
<!-- MODAL: INPUT HASIL LAB BARU -->
<!-- ========================================================================= -->
<div class="modal fade" id="labAddModal" tabindex="-1" role="dialog" aria-labelledby="labAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="labAddModalLabel">
                    <i class="fas fa-flask mr-1"></i> Form Input Hasil Pemeriksaan Laboratorium
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/lab') ?>" method="post">
                <input type="hidden" name="action" value="save_lab">
                <?= csrf_field() ?>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Pilih Kunjungan Pasien <span class="text-danger">*</span></label>
                            <select name="visit_select" id="lab_visit_select" class="form-control font-weight-bold" required>
                                <option value="">-- Cari Pasien / No. Kunjungan --</option>
                                <?php foreach ($recentVisits as $rv): ?>
                                    <option value="<?= $rv->id ?>" data-patient="<?= $rv->patient_id ?>" data-doctor="<?= $rv->doctor_id ?>">
                                        [<?= esc($rv->no_visit) ?>] <?= esc($rv->patient_name) ?> (RM: <?= esc($rv->no_rm) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="visit_id" id="lab_hidden_visit_id">
                            <input type="hidden" name="patient_id" id="lab_hidden_patient_id">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Dokter Perujuk / Penanggung Jawab</label>
                            <select name="doctor_id" id="lab_doctor_select" class="form-control font-weight-bold">
                                <?php foreach ($doctors as $d): ?>
                                    <option value="<?= $d->id ?>"><?= esc($d->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Jenis Tes / Parameter Lab <span class="text-danger">*</span></label>
                            <select name="test_type" id="lab_test_type" class="form-control font-weight-bold text-teal" required>
                                <option value="">-- Pilih Pemeriksaan dari Master Lab --</option>
                                <?php if (!empty($labTests)): ?>
                                    <?php 
                                    $currentCat = '';
                                    foreach ($labTests as $lt): 
                                        if ($currentCat !== $lt->category):
                                            if ($currentCat !== '') echo '</optgroup>';
                                            $currentCat = $lt->category;
                                            echo '<optgroup label="📋 ' . esc($currentCat) . '">';
                                        endif;
                                    ?>
                                        <option value="<?= esc($lt->name) ?>" 
                                                data-unit="<?= esc($lt->unit ?? '') ?>" 
                                                data-range="<?= esc($lt->reference_range ?? '') ?>" 
                                                data-spec="<?= esc($lt->specimen ?? 'Darah') ?>" 
                                                data-price="<?= $lt->price ?>">
                                            <?= esc($lt->code) ?> - <?= esc($lt->name) ?> (Rp <?= number_format($lt->price, 0, ',', '.') ?>)
                                        </option>
                                    <?php 
                                    endforeach; 
                                    if ($currentCat !== '') echo '</optgroup>';
                                    ?>
                                <?php else: ?>
                                    <option value="Gula Darah Sewaktu (GDS)" data-unit="mg/dL" data-range="70 - 140">Gula Darah Sewaktu (GDS)</option>
                                    <option value="Gula Darah Puasa (GDP)" data-unit="mg/dL" data-range="70 - 100">Gula Darah Puasa (GDP)</option>
                                    <option value="Asam Urat (Uric Acid)" data-unit="mg/dL" data-range="3.4 - 7.0">Asam Urat (Uric Acid)</option>
                                    <option value="Kolesterol Total" data-unit="mg/dL" data-range="< 200">Kolesterol Total</option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted d-block mt-1" id="lab_spec_info"></small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                            <input type="date" name="test_date" class="form-control font-weight-bold" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Hasil Pengukuran <span class="text-danger">*</span></label>
                            <input type="text" name="result_value" id="lab_result_value" class="form-control font-weight-bold" placeholder="Contoh: 110 atau Positif" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Nilai Normal / Rujukan</label>
                            <input type="text" name="normal_range" id="input_normal_range" class="form-control" placeholder="70 - 140">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Satuan</label>
                            <input type="text" name="unit" id="input_unit" class="form-control" placeholder="mg/dL">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Status Evaluasi Hasil <span class="text-danger">*</span></label>
                            <select name="status" id="lab_status_select" class="form-control font-weight-bold">
                                <option value="normal" class="text-success">NORMAL (Dalam Rentang Sehat)</option>
                                <option value="high" class="text-danger">HIGH (Tinggi di Atas Normal)</option>
                                <option value="low" class="text-warning">LOW (Rendah di Bawah Normal)</option>
                                <option value="abnormal" class="text-danger">ABNORMAL / POSITIF REAKTIF</option>
                            </select>
                            <small class="text-muted" id="lab_eval_hint">Evaluasi otomatis saat hasil diketik.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Nama Petugas / Analis Laboratorium</label>
                            <input type="text" name="officer_name" class="form-control" value="Analis Lab Sawamawa">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Catatan Hasil / Kesimpulan Tambahan</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Dianjurkan kontrol diet rendah purin...">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Hasil Laboratorium</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#lab_visit_select').change(function() {
            const opt = $(this).find(':selected');
            $('#lab_hidden_visit_id').val(opt.val());
            $('#lab_hidden_patient_id').val(opt.data('patient'));
            const docId = opt.data('doctor');
            if (docId) {
                $('#lab_doctor_select').val(docId);
            }
        });

        // Auto-Fill Nilai Rujukan, Satuan, & Info Spesimen saat Tes Dipilih
        $('#lab_test_type').change(function() {
            const opt = $(this).find(':selected');
            const unit = opt.data('unit') || '';
            const range = opt.data('range') || '';
            const spec = opt.data('spec') || '';
            const price = opt.data('price') || 0;

            $('#input_unit').val(unit);
            $('#input_normal_range').val(range);
            
            if (spec) {
                $('#lab_spec_info').html('<span class="badge badge-info mr-1"><i class="fas fa-vial"></i> Spesimen: ' + spec + '</span> <span class="badge badge-success">Tarif: Rp ' + Number(price).toLocaleString('id-ID') + '</span>');
            } else {
                $('#lab_spec_info').html('');
            }

            evaluateLabResult();
        });

        // Auto-Evaluasi Cerdas Hasil Uji Lab (Normal / High / Low)
        $('#lab_result_value, #input_normal_range').on('input', function() {
            evaluateLabResult();
        });

        function evaluateLabResult() {
            const valStr = $('#lab_result_value').val().trim().toLowerCase();
            const rangeStr = $('#input_normal_range').val().trim();
            const $status = $('#lab_status_select');
            const $hint = $('#lab_eval_hint');

            if (!valStr) {
                $hint.text('Evaluasi otomatis saat hasil diketik.');
                return;
            }

            // Cek kualitatif
            if (valStr.includes('positif') || valStr.includes('reaktif') || valStr.includes('abnormal')) {
                $status.val('abnormal');
                $hint.html('<span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Terdeteksi Positif / Abnormal</span>');
                return;
            } else if (valStr.includes('negatif') || valStr.includes('non reaktif')) {
                $status.val('normal');
                $hint.html('<span class="text-success font-weight-bold"><i class="fas fa-check"></i> Terdeteksi Negatif / Normal</span>');
                return;
            }

            // Cek numerik dengan rentang misal: "70 - 100" atau "< 200" atau "> 45"
            const numVal = parseFloat(valStr.replace(',', '.'));
            if (!isNaN(numVal) && rangeStr) {
                // Pola Min - Max
                const rangeMatch = rangeStr.match(/([\d\.]+)\s*-\s*([\d\.]+)/);
                if (rangeMatch) {
                    const min = parseFloat(rangeMatch[1]);
                    const max = parseFloat(rangeMatch[2]);
                    if (numVal < min) {
                        $status.val('low');
                        $hint.html('<span class="text-warning font-weight-bold"><i class="fas fa-arrow-down"></i> Nilai di bawah rentang normal (' + min + ' - ' + max + ')</span>');
                    } else if (numVal > max) {
                        $status.val('high');
                        $hint.html('<span class="text-danger font-weight-bold"><i class="fas fa-arrow-up"></i> Nilai di atas rentang normal (' + min + ' - ' + max + ')</span>');
                    } else {
                        $status.val('normal');
                        $hint.html('<span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Nilai dalam rentang normal (' + min + ' - ' + max + ')</span>');
                    }
                    return;
                }

                // Pola < Max (misal: < 200)
                const maxMatch = rangeStr.match(/<\s*([\d\.]+)/);
                if (maxMatch) {
                    const max = parseFloat(maxMatch[1]);
                    if (numVal >= max) {
                        $status.val('high');
                        $hint.html('<span class="text-danger font-weight-bold"><i class="fas fa-arrow-up"></i> Nilai melebihi batas ' + max + '</span>');
                    } else {
                        $status.val('normal');
                        $hint.html('<span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Nilai optimal (< ' + max + ')</span>');
                    }
                    return;
                }

                // Pola > Min (misal: > 45)
                const minMatch = rangeStr.match(/>\s*([\d\.]+)/);
                if (minMatch) {
                    const min = parseFloat(minMatch[1]);
                    if (numVal <= min) {
                        $status.val('low');
                        $hint.html('<span class="text-warning font-weight-bold"><i class="fas fa-arrow-down"></i> Nilai di bawah target (> ' + min + ')</span>');
                    } else {
                        $status.val('normal');
                        $hint.html('<span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Nilai optimal (> ' + min + ')</span>');
                    }
                    return;
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>
