<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <!-- Top Metrics Cards -->
        <div class="row mb-3">
            <div class="col-md-6 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-teal elevation-0"><i class="fas fa-file-medical-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Total Diagnosa ICD-10</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= number_format($totalIcd10 ?? 0, 0, ',', '.') ?> <small class="text-muted">Kode Terdaftar</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-12">
                <div class="info-box bg-white shadow-none border">
                    <span class="info-box-icon bg-info elevation-0"><i class="fas fa-procedures"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-uppercase font-weight-bold">Total Prosedur ICD-9-CM</span>
                        <span class="info-box-number text-dark h4 mb-0"><?= number_format($totalIcd9 ?? 0, 0, ',', '.') ?> <small class="text-muted">Kode Terdaftar</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Navigation Tabs -->
        <div class="card card-teal card-outline card-outline-tabs shadow">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-icd10-tab" data-toggle="pill" href="#tab-icd10" role="tab" aria-controls="tab-icd10" aria-selected="true">
                            <i class="fas fa-notes-medical text-teal mr-1"></i> 1. KODE DIAGNOSA PENYAKIT (ICD-10)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-icd9-tab" data-toggle="pill" href="#tab-icd9" role="tab" aria-controls="tab-icd9" aria-selected="false">
                            <i class="fas fa-syringe text-info mr-1"></i> 2. KODE PROSEDUR / TINDAKAN MEDIS (ICD-9-CM)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    
                    <!-- ========================================================================= -->
                    <!-- TAB 1: MASTER ICD-10 (DIAGNOSA PENYAKIT) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-icd10" role="tabpanel" aria-labelledby="tab-icd10-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-list-alt text-teal"></i> DAFTAR MASTER ICD-10</h5>
                                <small class="text-muted">Katalog standar kode dan deskripsi diagnosa medis untuk acuan Rekam Medis (SOAP)</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddIcd10">
                                <i class="fas fa-plus mr-1"></i> + Tambah Kode ICD-10
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table id="table-icd10" class="table table-bordered table-striped table-hover datatable-serverside" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 110px;" class="text-center">KODE ICD-10</th>
                                        <th>DESKRIPSI (INDONESIA)</th>
                                        <th>DESKRIPSI (ENGLISH)</th>
                                        <th style="width: 140px;">KATEGORI KLASIFIKASI</th>
                                        <th style="width: 90px;" class="text-center">STATUS</th>
                                        <th style="width: 130px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris tabel diisi secara instan oleh DataTables Server-Side Processing -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: MASTER ICD-9-CM (PROSEDUR & TINDAKAN MEDIS) -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-icd9" role="tabpanel" aria-labelledby="tab-icd9-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-syringe text-info"></i> DAFTAR MASTER ICD-9-CM</h5>
                                <small class="text-muted">Katalog standar kode prosedur medis dan tindakan klinis untuk acuan Rekam Medis (SOAP)</small>
                            </div>
                            <button class="btn btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddIcd9">
                                <i class="fas fa-plus mr-1"></i> + Tambah Kode ICD-9-CM
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table id="table-icd9" class="table table-bordered table-striped table-hover datatable-serverside" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 110px;" class="text-center">KODE ICD-9</th>
                                        <th>NAMA PROSEDUR / TINDAKAN</th>
                                        <th>DESKRIPSI (ENGLISH)</th>
                                        <th style="width: 140px;">KATEGORI KLASIFIKASI</th>
                                        <th style="width: 90px;" class="text-center">STATUS</th>
                                        <th style="width: 130px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris tabel diisi secara instan oleh DataTables Server-Side Processing -->
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
<!-- MODALS FOR ICD-10 -->
<!-- ========================================================================= -->
<!-- Modal Tambah ICD-10 -->
<div class="modal fade" id="modalAddIcd10" tabindex="-1" role="dialog" aria-labelledby="modalAddIcd10Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="modalAddIcd10Label"><i class="fas fa-plus-circle mr-1"></i> Tambah Master Diagnosa ICD-10</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/master-icd/save-icd10') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kode ICD-10 <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: I10, E11.9, J00" required>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Kategori Klasifikasi Penyakit</label>
                            <input type="text" name="category" class="form-control" placeholder="Contoh: Penyakit Sistem Sirkulasi / Pernapasan">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Diagnosa / Penyakit (Bahasa Indonesia) <span class="text-danger">*</span></label>
                        <input type="text" name="name_id" class="form-control" placeholder="Contoh: Hipertensi Esensial (Primer)" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Medis Internasional (Bahasa Inggris / English)</label>
                        <input type="text" name="name_en" class="form-control" placeholder="Contoh: Essential (primary) hypertension">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">ACTIVE (Aktif Digunakan)</option>
                            <option value="inactive">INACTIVE (Nonaktif)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Kode ICD-10</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit ICD-10 -->
<div class="modal fade" id="modalEditIcd10" tabindex="-1" role="dialog" aria-labelledby="modalEditIcd10Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="modalEditIcd10Label"><i class="fas fa-edit mr-1"></i> Edit Master Diagnosa ICD-10</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/master-icd/save-icd10') ?>" method="post">
                <input type="hidden" name="id" id="edit-icd10-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kode ICD-10 <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-icd10-code" class="form-control" required>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Kategori Klasifikasi Penyakit</label>
                            <input type="text" name="category" id="edit-icd10-category" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Diagnosa / Penyakit (Bahasa Indonesia) <span class="text-danger">*</span></label>
                        <input type="text" name="name_id" id="edit-icd10-name-id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Medis Internasional (Bahasa Inggris / English)</label>
                        <input type="text" name="name_en" id="edit-icd10-name-en" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-icd10-status" class="form-control">
                            <option value="active">ACTIVE (Aktif Digunakan)</option>
                            <option value="inactive">INACTIVE (Nonaktif)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Perbarui Kode ICD-10</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODALS FOR ICD-9-CM -->
<!-- ========================================================================= -->
<!-- Modal Tambah ICD-9-CM -->
<div class="modal fade" id="modalAddIcd9" tabindex="-1" role="dialog" aria-labelledby="modalAddIcd9Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title font-weight-bold text-white" id="modalAddIcd9Label"><i class="fas fa-plus-circle mr-1"></i> Tambah Master Prosedur ICD-9-CM</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/master-icd/save-icd9') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kode ICD-9-CM <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: 89.52, 88.72, 93.57" required>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Kategori Klasifikasi Tindakan</label>
                            <input type="text" name="category" class="form-control" placeholder="Contoh: Pemeriksaan Kardiovaskular / Tindakan Bedah Minor">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Prosedur / Tindakan Medis (Bahasa Indonesia) <span class="text-danger">*</span></label>
                        <input type="text" name="name_id" class="form-control" placeholder="Contoh: Elektrokardiogram (EKG 12-Lead)" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Prosedur Internasional (Bahasa Inggris / English)</label>
                        <input type="text" name="name_en" class="form-control" placeholder="Contoh: Electrocardiogram">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">ACTIVE (Aktif Digunakan)</option>
                            <option value="inactive">INACTIVE (Nonaktif)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Kode ICD-9-CM</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit ICD-9-CM -->
<div class="modal fade" id="modalEditIcd9" tabindex="-1" role="dialog" aria-labelledby="modalEditIcd9Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title font-weight-bold text-white" id="modalEditIcd9Label"><i class="fas fa-edit mr-1"></i> Edit Master Prosedur ICD-9-CM</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/master-icd/save-icd9') ?>" method="post">
                <input type="hidden" name="id" id="edit-icd9-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Kode ICD-9-CM <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-icd9-code" class="form-control" required>
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Kategori Klasifikasi Tindakan</label>
                            <input type="text" name="category" id="edit-icd9-category" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Prosedur / Tindakan Medis (Bahasa Indonesia) <span class="text-danger">*</span></label>
                        <input type="text" name="name_id" id="edit-icd9-name-id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Prosedur Internasional (Bahasa Inggris / English)</label>
                        <input type="text" name="name_en" id="edit-icd9-name-en" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-icd9-status" class="form-control">
                            <option value="active">ACTIVE (Aktif Digunakan)</option>
                            <option value="inactive">INACTIVE (Nonaktif)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i> Perbarui Kode ICD-9-CM</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables Server-Side ICD-10
        $('#table-icd10').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('system/master-icd') ?>',
                type: 'GET'
            },
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [0, 4, 5], className: 'text-center' },
                { targets: [5], orderable: false }
            ],
            language: window.dtIndonesianLanguage,
            pageLength: 10
        });

        // Inisialisasi DataTables Server-Side ICD-9
        let icd9Loaded = false;
        $('a[href="#tab-icd9"]').on('shown.bs.tab', function () {
            if (!icd9Loaded) {
                $('#table-icd9').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '<?= base_url('system/master-icd?tab=icd9') ?>',
                        type: 'GET'
                    },
                    order: [[0, 'asc']],
                    columnDefs: [
                        { targets: [0, 4, 5], className: 'text-center' },
                        { targets: [5], orderable: false }
                    ],
                    language: window.dtIndonesianLanguage,
                    pageLength: 10
                });
                icd9Loaded = true;
            }
        });

        // Delegated Event Listener: Edit ICD-10
        $(document).on('click', '.btn-edit-icd10', function() {
            $('#edit-icd10-id').val($(this).data('id'));
            $('#edit-icd10-code').val($(this).data('code'));
            $('#edit-icd10-name-id').val($(this).data('nameid'));
            $('#edit-icd10-name-en').val($(this).data('nameen'));
            $('#edit-icd10-category').val($(this).data('category'));
            $('#edit-icd10-status').val($(this).data('status'));
            $('#modalEditIcd10').modal('show');
        });

        // Delegated Event Listener: Edit ICD-9
        $(document).on('click', '.btn-edit-icd9', function() {
            $('#edit-icd9-id').val($(this).data('id'));
            $('#edit-icd9-code').val($(this).data('code'));
            $('#edit-icd9-name-id').val($(this).data('nameid'));
            $('#edit-icd9-name-en').val($(this).data('nameen'));
            $('#edit-icd9-category').val($(this).data('category'));
            $('#edit-icd9-status').val($(this).data('status'));
            $('#modalEditIcd9').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
