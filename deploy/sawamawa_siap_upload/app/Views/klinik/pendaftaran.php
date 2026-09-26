<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Quick Flash Card for Newly Registered Patient -->
    <?php if (session()->getFlashdata('last_registered_patient_id')): ?>
        <div class="col-md-12 mb-3">
            <div class="card p-3" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #0d9f4f; border-radius:4px; box-shadow:none;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <span class="d-inline-flex align-items-center justify-content-center mr-3" style="background:#e6f4ea; color:#076e34; width:38px; height:38px; border-radius:50%; font-size:18px; flex-shrink:0;">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="font-weight-bold text-dark mb-0 mr-2" style="font-size:15px;">Pasien Baru Berhasil Terdaftar!</h6>
                                <span class="badge badge-dark px-2 py-1 font-weight-bold" style="font-size:12px;">
                                    <?= esc(session()->getFlashdata('last_registered_patient_rm')) ?>
                                </span>
                            </div>
                            <span class="text-secondary text-xs">
                                Pasien atas nama <strong><?= esc(session()->getFlashdata('last_registered_patient_name')) ?></strong> siap dicetak kartu identitasnya atau dibagikan ke WhatsApp.
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <a href="<?= base_url('klinik/kartu-pasien/' . session()->getFlashdata('last_registered_patient_id')) ?>" class="btn btn-teal btn-sm font-weight-bold mr-2">
                            <i class="fas fa-id-card mr-1"></i> Buka & Kirim Kartu Pasien (JPG / WA)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- STREAMLINED WORKSPACE RIBBON: METRIK CEPAT & STATUS ONLINE (SLIM & FOCUSED) -->
    <div class="col-12 mb-3">
        <div class="card border-0 shadow-sm p-2 px-3 bg-white" style="border-radius: 8px;">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 10px;">
                
                <!-- Quick Stats Mini Chips -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <div class="d-inline-flex align-items-center px-2.5 py-1 rounded bg-light border text-xs">
                        <i class="fas fa-users text-teal mr-1.5"></i>
                        <span class="text-muted mr-1">Total Pasien:</span>
                        <strong class="text-dark font-weight-bold"><?= number_format($totalPatients ?? 0, 0, ',', '.') ?></strong>
                    </div>
                    <div class="d-inline-flex align-items-center px-2.5 py-1 rounded bg-light border text-xs">
                        <i class="fas fa-hospital-user text-info mr-1.5"></i>
                        <span class="text-muted mr-1">Kunjungan Hari Ini:</span>
                        <strong class="text-dark font-weight-bold"><?= number_format($totalTodayVisits ?? 0, 0, ',', '.') ?></strong>
                    </div>
                    <div class="d-inline-flex align-items-center px-2.5 py-1 rounded bg-light border text-xs">
                        <i class="fas fa-clock-rotate-left text-warning mr-1.5"></i>
                        <span class="text-muted mr-1">Antrean Aktif:</span>
                        <strong class="text-warning font-weight-bold" id="stat-active-queue"><?= count($todayQueues ?? []) ?></strong>
                    </div>
                    <div class="d-inline-flex align-items-center px-2.5 py-1 rounded bg-light border text-xs">
                        <i class="fas fa-circle-check text-success mr-1.5"></i>
                        <span class="text-muted mr-1">Selesai:</span>
                        <strong class="text-success font-weight-bold"><?= number_format($totalCompletedVisits ?? 0, 0, ',', '.') ?></strong>
                    </div>
                </div>

                <!-- Status Pendaftaran Online & Quick Actions -->
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <div class="d-inline-flex align-items-center text-xs">
                        <span class="text-muted mr-1.5 d-none d-sm-inline"><i class="fas fa-globe text-teal mr-1"></i> Form Online:</span>
                        <?php if (is_online_registration_open()): ?>
                            <span class="badge badge-success px-2 py-1 font-weight-bold" title="Jam Buka: <?= esc(clinic_setting('online_registration_open_time', '06:00')) ?> - <?= esc(clinic_setting('online_registration_close_time', '21:00')) ?> WITA">
                                <i class="fas fa-check-circle mr-0.5"></i> BUKA (<?= esc(clinic_setting('online_registration_open_time', '06:00')) ?>-<?= esc(clinic_setting('online_registration_close_time', '21:00')) ?>)
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger px-2 py-1 font-weight-bold">
                                <i class="fas fa-ban mr-0.5"></i> TUTUP
                            </span>
                        <?php endif; ?>
                    </div>

                    <form action="<?= base_url('klinik/toggle-online-registration') ?>" method="post" class="d-inline mb-0">
                        <?= csrf_field() ?>
                        <?php if (clinic_setting('online_registration_active', 'true') === 'true'): ?>
                            <input type="hidden" name="status" value="false">
                            <button type="submit" class="btn btn-outline-danger btn-xs font-weight-bold" title="Tutup Pendaftaran Online Sekarang">
                                <i class="fas fa-power-off"></i>
                            </button>
                        <?php else: ?>
                            <input type="hidden" name="status" value="true">
                            <button type="submit" class="btn btn-success btn-xs font-weight-bold" title="Buka Pendaftaran Online Sekarang">
                                <i class="fas fa-play"></i>
                            </button>
                        <?php endif; ?>
                    </form>

                    <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold" data-toggle="modal" data-target="#modalSopAdmisi" title="Lihat Petunjuk SOP Admisi & Sinkronisasi RME">
                        <i class="fas fa-book-medical mr-1"></i> Panduan SOP
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- KOLOM KIRI (col-lg-9): Data Pasien Terdaftar (MAIN WORKSPACE AREA) -->
    <div class="col-lg-9 col-xl-9 mb-3">
        <div class="card card-outline card-teal shadow-sm h-100" style="border-radius: 8px;">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap bg-white border-bottom py-2.5">
                <div>
                    <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 15px;">
                        <i class="fas fa-users text-teal mr-1"></i> Direktori Data Pasien Terdaftar
                    </h3>
                </div>
                <div class="card-tools ml-auto mt-2 mt-sm-0 d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalExportPasien" title="Ekspor Database atau Cetak Laporan Pasien">
                        <i class="fas fa-file-export mr-1"></i> Ekspor / Laporan
                    </button>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#registerPatientModal" style="background:#0d9488; color:#ffffff;">
                        <i class="fas fa-user-plus mr-1"></i> Registrasi Pasien Baru
                    </button>
                </div>
            </div>
            <div class="card-body p-2 p-md-3">
                <div class="table-responsive">
                    <table id="table-patients" class="table table-bordered table-striped datatable-serverside dt-responsive nowrap w-100" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 90px;">No RM</th>
                                <th style="width: 120px;">NIK</th>
                                <th>Nama Pasien</th>
                                <th style="width: 45px;" class="text-center">Gender</th>
                                <th>WhatsApp</th>
                                <th>Alamat</th>
                                <th style="width: 240px;" class="text-center">Aksi</th>
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

    <!-- KOLOM KANAN SIDEBAR (col-lg-3): Monitoring Antrean Pasien Hari Ini (Compact Widget) -->
    <div class="col-lg-3 col-xl-3 mb-3">
        <div class="card card-outline card-teal shadow-sm h-100" style="border-radius: 8px;">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom py-2 px-3">
                <h3 class="card-title font-weight-bold text-dark mb-0" style="font-size: 13px;">
                    <i class="fas fa-tv text-teal mr-1"></i> Monitoring Antrean
                </h3>
                <div class="card-tools ml-auto d-flex align-items-center">
                    <span class="badge badge-teal font-weight-bold" id="sidebar-queue-count" style="background:#0d9488; color:#ffffff; font-size:10.5px; padding: 3px 6px;"><?= count($todayQueues ?? []) ?> Pasien</span>
                </div>
            </div>
            <!-- Quick search in sidebar queue -->
            <div class="p-1.5 border-bottom bg-light">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 px-2" style="font-size: 11px;"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="pendaftaran-queue-search" class="form-control border-left-0 py-0" style="font-size: 11.5px; height: 28px;" placeholder="Cari nama / No RM...">
                </div>
            </div>
            <div class="card-body p-0" style="max-height: 540px; overflow-y: auto;">
                <div class="list-group list-group-flush" id="live-pendaftaran-antrean-body">
                    <?php if (empty($todayQueues)): ?>
                        <div class="text-center text-muted p-3" id="empty-queue-placeholder">
                            <i class="fas fa-ticket-alt fa-lg mb-1 text-secondary"></i>
                            <p class="mb-0 text-xs">Belum ada antrean hari ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todayQueues as $idx => $q): ?>
                            <?php 
                                $tier = strtolower($q->membership_tier ?? 'regular'); 
                                $targetName = ($q->visit_type === 'tindakan') ? ($q->tindakan_name ?? 'Tindakan') : ($q->polyclinic_name ?? 'Poli');
                                $status = strtolower($q->status ?? 'waiting');
                                $statusBadge = [
                                    'waiting' => ['secondary', 'Menunggu'],
                                    'called' => ['primary', 'Dipanggil'],
                                    'examining' => ['info', 'Diperiksa'],
                                    'triage' => ['warning', 'Triage'],
                                    'completed' => ['success', 'Selesai'],
                                    'no-show' => ['warning', 'Lewat'],
                                    'cancelled' => ['danger', 'Batal']
                                ][$status] ?? ['secondary', strtoupper($status)];
                            ?>
                            <div class="list-group-item p-2 px-2.5 border-bottom sidebar-queue-item" style="font-size: 11.5px;" data-search="<?= strtolower(esc($q->patient_name . ' ' . ($q->no_rm ?? '') . ' ' . $q->queue_no)) ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center text-truncate pr-1">
                                        <span class="badge badge-light border text-muted font-weight-bold mr-1" style="font-size:9.5px; padding: 2px 4px;" title="Urutan Kedatangan">#<?= esc($q->arrival_seq ?? ($idx+1)) ?></span>
                                        <?php if ($tier === 'vip' || $tier === 'platinum'): ?>
                                            <span class="badge badge-dark font-weight-bold mr-1" style="font-size: 10.5px; background:#0f172a; border: 1px solid #ffc107; color:#ffc107; padding: 2px 4px;"><i class="fas fa-gem mr-0.5"></i><?= esc($q->queue_no) ?></span>
                                        <?php elseif ($tier === 'gold'): ?>
                                            <span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size: 10.5px; background:#ffc107; padding: 2px 4px;"><i class="fas fa-crown mr-0.5"></i><?= esc($q->queue_no) ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-teal font-weight-bold mr-1" style="background:#0d9488; color:#ffffff; font-size: 10.5px; padding: 2px 5px;"><?= esc($q->queue_no) ?></span>
                                        <?php endif; ?>
                                        <strong class="text-dark queue-patient-name text-truncate" style="max-width: 105px; font-size: 12px;"><?= esc($q->patient_name) ?></strong>
                                    </div>
                                    <span class="badge badge-<?= $statusBadge[0] ?> font-weight-bold" style="font-size: 8.5px; padding: 2px 4px;"><?= strtoupper($statusBadge[1]) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-xs text-secondary mt-0.5" style="font-size: 10.5px;">
                                    <div class="text-truncate mr-1">
                                        <i class="fas fa-stethoscope text-teal mr-0.5"></i>
                                        <span class="font-weight-bold text-dark"><?= esc($targetName) ?></span> &bull; <span class="text-muted"><?= esc($q->doctor_name ?? 'Dokter') ?></span>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-danger p-0 px-1 border-0 btn-cancel-pendaftaran-queue ml-auto" data-id="<?= $q->visit_id ?>" data-name="<?= esc($q->patient_name) ?>" data-queue="<?= esc($q->queue_no) ?>" title="Batalkan Antrean" style="font-size: 10px;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer bg-light p-1.5 text-center border-top">
                <a href="<?= base_url('klinik/antrean') ?>" class="btn btn-outline-teal btn-xs font-weight-bold btn-block py-1" style="font-size: 11px;">
                    <i class="fas fa-desktop mr-1"></i> Buka Layar Antrean Penuh
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Register Pasien Baru -->
<div class="modal fade" id="registerPatientModal" tabindex="-1" role="dialog" aria-labelledby="registerPatientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="registerPatientModalLabel"><i class="fas fa-user-plus mr-1"></i> Register Pasien Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/pendaftaran') ?>" method="post">
                <input type="hidden" name="action" value="register_patient">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>NIK Pasien <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" required maxlength="16" minlength="16">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama Pasien" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="place_of_birth" class="form-control" placeholder="Kota / Tempat Lahir" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>No. Telp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="Nomor Telepon / WhatsApp" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Nomor Kartu BPJS (Opsional)</label>
                            <input type="text" name="bpjs_number" class="form-control" placeholder="No. BPJS (Jika ada)">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Golongan Kartu Pasien <span class="text-danger">*</span></label>
                            <select name="membership_tier" class="form-control font-weight-bold" required>
                                <option value="regular" selected>🟢 Reguler (Standar)</option>
                                <option value="gold">🟡 Gold Member (Prioritas)</option>
                                <option value="vip">⚫ Platinum / VIP (Eksekutif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Pekerjaan Pasien</label>
                            <input type="text" name="occupation" class="form-control" placeholder="Contoh: PNS, Swasta">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Agama</label>
                            <select name="religion" class="form-control">
                                <option value="Islam" selected>Islam</option>
                                <option value="Kristen Protestan">Kristen Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Golongan Darah</label>
                            <select name="blood_type" class="form-control">
                                <option value="-">- (Belum Tahu)</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Status Pernikahan</label>
                            <select name="marital_status" class="form-control">
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah" selected>Menikah</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Alamat Domisili Pasien" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-danger font-weight-bold"><i class="fas fa-allergies mr-1"></i> Riwayat Alergi (Obat / Makanan)</label>
                            <input type="text" name="allergies" class="form-control border-danger" placeholder="Contoh: Alergi Amoxicillin, Seafood (Kosongkan jika tidak ada)">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Riwayat Penyakit Terdahulu (RPD)</label>
                            <input type="text" name="medical_history" class="form-control" placeholder="Contoh: Hipertensi, Asma, Diabetes">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Kontak Darurat</label>
                            <input type="text" name="emergency_contact_name" class="form-control" placeholder="Nama Keluarga / Kerabat">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>No Telp Kontak Darurat</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" placeholder="Nomor Telepon Kerabat">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Data Pasien</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data Pasien -->
<div class="modal fade" id="editPatientModal" tabindex="-1" role="dialog" aria-labelledby="editPatientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="editPatientModalLabel"><i class="fas fa-user-edit mr-1"></i> Edit Data Pasien</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/pendaftaran') ?>" method="post">
                <input type="hidden" name="action" value="edit_patient">
                <input type="hidden" name="patient_id" id="edit-patient-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>NIK Pasien <span class="text-danger">*</span></label>
                            <input type="text" name="nik" id="edit-nik" class="form-control" required maxlength="16" minlength="16">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="gender" id="edit-gender" class="form-control" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="place_of_birth" id="edit-pob" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="edit-dob" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>No. Telp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="edit-phone" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Nomor Kartu BPJS</label>
                            <input type="text" name="bpjs_number" id="edit-bpjs" class="form-control" placeholder="No. BPJS (Jika ada)">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Golongan Kartu Pasien <span class="text-danger">*</span></label>
                            <select name="membership_tier" id="edit-tier" class="form-control font-weight-bold" required>
                                <option value="regular">🟢 Reguler (Standar)</option>
                                <option value="gold">🟡 Gold Member (Prioritas)</option>
                                <option value="vip">⚫ Platinum / VIP (Eksekutif)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Pekerjaan Pasien</label>
                            <input type="text" name="occupation" id="edit-occupation" class="form-control" placeholder="Contoh: PNS, Swasta">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Agama</label>
                            <select name="religion" id="edit-religion" class="form-control">
                                <option value="Islam">Islam</option>
                                <option value="Kristen Protestan">Kristen Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Golongan Darah</label>
                            <select name="blood_type" id="edit-blood-type" class="form-control">
                                <option value="-">- (Belum Tahu)</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Status Pernikahan</label>
                            <select name="marital_status" id="edit-marital-status" class="form-control">
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah">Menikah</option>
                                <option value="Cerai Hidup">Cerai Hidup</option>
                                <option value="Cerai Mati">Cerai Mati</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" id="edit-address" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-danger font-weight-bold"><i class="fas fa-allergies mr-1"></i> Riwayat Alergi (Obat / Makanan)</label>
                            <input type="text" name="allergies" id="edit-allergies" class="form-control border-danger" placeholder="Riwayat Alergi Pasien">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Riwayat Penyakit Terdahulu (RPD)</label>
                            <input type="text" name="medical_history" id="edit-medical-history" class="form-control" placeholder="Riwayat Penyakit Terdahulu">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Kontak Darurat</label>
                            <input type="text" name="emergency_contact_name" id="edit-emg-name" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>No Telp Kontak Darurat</label>
                            <input type="text" name="emergency_contact_phone" id="edit-emg-phone" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Perbarui Data Pasien</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL IDENTITAS & PROFIL LENGKAP PASIEN                            -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalPatientDetail" tabindex="-1" role="dialog" aria-labelledby="modalPatientDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-gradient-teal text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-teal rounded-circle d-flex align-items-center justify-content-center mr-2.5 font-weight-bold" id="detail-patient-avatar" style="width: 38px; height: 38px; font-size: 16px;">
                        P
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="detail-patient-name">Memuat Data Pasien...</h5>
                        <small class="text-white-50" id="detail-patient-sub">No. RM: - | NIK: -</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-0">
                <!-- Loading State -->
                <div id="detail-loading" class="text-center py-5">
                    <div class="spinner-border text-teal" role="status">
                        <span class="sr-only">Memuat...</span>
                    </div>
                    <p class="text-muted text-xs mt-2 mb-0">Mengambil data profil lengkap pasien...</p>
                </div>

                <!-- Detail Content -->
                <div id="detail-content" style="display: none;">
                    <!-- Hero Status Strip -->
                    <div class="bg-light px-4 py-2.5 border-bottom d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <span class="badge badge-teal px-2.5 py-1 font-monospace" style="font-size: 12px;" id="detail-badge-rm">RM-000000</span>
                            <span class="badge badge-light border text-muted" id="detail-badge-tier">Reguler</span>
                            <span id="detail-badge-gender"></span>
                        </div>
                        <div class="text-xs text-muted">
                            <i class="fas fa-calendar-alt mr-1"></i> Terdaftar sejak: <strong id="detail-created-at" class="text-dark">-</strong>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row">
                            <!-- Kolom Kiri: Biodata Sosial -->
                            <div class="col-md-6 border-right">
                                <h6 class="font-weight-bold text-teal text-xs border-bottom pb-1.5 mb-2 uppercase">
                                    <i class="fas fa-id-card text-teal mr-1"></i> Data Demografi &amp; Kependudukan
                                </h6>
                                <table class="table table-sm table-borderless text-xs mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 125px;">NIK Kependudukan</td>
                                        <td class="font-weight-bold font-monospace" id="detail-nik">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Lengkap</td>
                                        <td class="font-weight-bold" id="detail-name">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tempat, Tgl Lahir</td>
                                        <td id="detail-pob-dob">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Usia Pasien</td>
                                        <td class="font-weight-bold" id="detail-age">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Pekerjaan</td>
                                        <td class="font-weight-bold" id="detail-occupation">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Agama</td>
                                        <td id="detail-religion">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Golongan Darah</td>
                                        <td class="font-weight-bold text-danger" id="detail-blood-type">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status Pernikahan</td>
                                        <td id="detail-marital-status">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Kartu BPJS</td>
                                        <td id="detail-bpjs">: -</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Kolom Kanan: Kontak & Peringatan Medis -->
                            <div class="col-md-6 pl-md-3">
                                <h6 class="font-weight-bold text-teal text-xs border-bottom pb-1.5 mb-2 uppercase">
                                    <i class="fas fa-map-marker-alt text-teal mr-1"></i> Kontak &amp; Domisili
                                </h6>
                                <table class="table table-sm table-borderless text-xs mb-3">
                                    <tr>
                                        <td class="text-muted" style="width: 125px;">No. HP / WhatsApp</td>
                                        <td id="detail-phone">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Alamat Domisili</td>
                                        <td id="detail-address">: -</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kontak Darurat</td>
                                        <td id="detail-emg">: -</td>
                                    </tr>
                                </table>

                                <h6 class="font-weight-bold text-danger text-xs border-bottom pb-1.5 mb-2 uppercase">
                                    <i class="fas fa-shield-virus text-danger mr-1"></i> Peringatan Klinis
                                </h6>
                                <div class="p-2 rounded bg-danger-light border border-danger mb-2 text-xs">
                                    <strong class="text-danger d-block mb-0.5"><i class="fas fa-allergies mr-1"></i> Riwayat Alergi:</strong>
                                    <div id="detail-allergies" class="font-weight-bold text-danger">-</div>
                                </div>
                                <div class="p-2 rounded bg-light border text-xs">
                                    <strong class="text-secondary d-block mb-0.5"><i class="fas fa-file-medical-alt mr-1"></i> Riwayat Penyakit Terdahulu (RPD):</strong>
                                    <div id="detail-medical-history" class="text-dark">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Statistik Cepat Pasien -->
                        <div class="row mt-3 pt-2 border-top">
                            <div class="col-4 text-center border-right">
                                <small class="text-muted text-xs d-block">Total Kunjungan</small>
                                <strong class="text-teal" style="font-size: 15px;" id="detail-count-visits">0</strong>
                                <small class="text-muted d-block" style="font-size: 10px;">Pemeriksaan</small>
                            </div>
                            <div class="col-4 text-center border-right">
                                <small class="text-muted text-xs d-block">Kunjungan Terakhir</small>
                                <strong class="text-dark text-xs d-block mt-1" id="detail-last-visit">-</strong>
                            </div>
                            <div class="col-4 text-center">
                                <small class="text-muted text-xs d-block">Informed Consent</small>
                                <strong class="text-info" style="font-size: 15px;" id="detail-count-consents">0</strong>
                                <small class="text-muted d-block" style="font-size: 10px;">Dokumen Sah</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <a href="#" id="detail-btn-full-page" class="btn btn-outline-secondary btn-sm font-weight-bold">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Halaman Profil Penuh
                    </a>
                    <a href="#" id="detail-btn-print-card" target="_blank" class="btn btn-outline-secondary btn-sm font-weight-bold ml-1">
                        <i class="fas fa-id-card mr-1"></i> Cetak Kartu
                    </a>
                </div>
                <div class="d-flex" style="gap: 6px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" id="detail-btn-quick-visit">
                        <i class="fas fa-calendar-plus mr-1"></i> Daftarkan Kunjungan
                    </button>
                    <button type="button" class="btn btn-info btn-sm font-weight-bold" id="detail-btn-quick-rme">
                        <i class="fas fa-file-medical mr-1"></i> Rekam Medis (RME)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL PANDUAN OPERASIONAL & SOP ADMISI PASIEN (SINKRONISASI RME 7 LEMBAR) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalSopAdmisi" tabindex="-1" role="dialog" aria-labelledby="modalSopAdmisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-gradient-teal text-white py-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mr-2.5 rounded-circle bg-white text-teal font-weight-bold" style="width: 36px; height: 36px; font-size: 16px;">
                        <i class="fas fa-book-medical"></i>
                    </span>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalSopAdmisiLabel">Panduan Operasional Admisi Pasien</h5>
                        <small class="text-white-50">Standard Operating Procedure (SOP) Registrasi &amp; Sinkronisasi RME Gitria Farma</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="alert alert-info border-0 p-3 mb-3 text-xs" style="background:#e0f2fe; color:#0369a1; border-radius: 8px;">
                    <i class="fas fa-lightbulb mr-1.5 font-weight-bold"></i> <strong>Prinsip Layanan 2-Tahap:</strong> Pasien mendaftar dengan cepat tanpa beban form yang panjang di awal, lalu petugas admisi melengkapi biodata sosial &amp; klinis saat pasien tiba di klinik untuk sinkronisasi utuh ke Rekam Medis (RME).
                </div>

                <div class="row">
                    <!-- TAHAP 1 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm p-3 bg-white" style="border-radius: 8px; border-left: 4px solid #0d9488 !important;">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-teal px-2 py-1 mr-2 font-weight-bold" style="background:#0d9488; color:#fff;">Langkah 1</span>
                                <strong class="text-dark" style="font-size: 13.5px;">Registrasi Cepat (Fast-Booking)</strong>
                            </div>
                            <p class="text-secondary text-xs mb-2" style="line-height: 1.5;">
                                Pasien mendaftar mandiri via Web/WhatsApp atau petugas klik <strong>[+ Registrasi Pasien Baru]</strong>:
                            </p>
                            <ul class="text-xs text-muted mb-0 pl-3">
                                <li>Input NIK KTP / KK (kunci unik rekam medis).</li>
                                <li>Nama Lengkap, Jenis Kelamin, &amp; Tanggal Lahir.</li>
                                <li>Nomor WhatsApp &amp; Alamat Domisili.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- TAHAP 2 -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm p-3 bg-white" style="border-radius: 8px; border-left: 4px solid #0284c7 !important;">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-info px-2 py-1 mr-2 font-weight-bold">Langkah 2</span>
                                <strong class="text-dark" style="font-size: 13.5px;">Lengkapi Biodata (Enrichment)</strong>
                            </div>
                            <p class="text-secondary text-xs mb-2" style="line-height: 1.5;">
                                Saat pasien tiba di klinik, klik tombol <span class="badge badge-light border"><i class="fas fa-pen-to-square"></i> Edit</span> pada baris pasien untuk melengkapi data Lembar 1:
                            </p>
                            <ul class="text-xs text-muted mb-0 pl-3">
                                <li>Pekerjaan, Agama, Pendidikan, Gol. Darah.</li>
                                <li>Status Pernikahan &amp; No. BPJS (jika ada).</li>
                                <li><strong>Riwayat Alergi (Highlight)</strong> &amp; RPD.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- TAHAP 3 -->
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm p-3 bg-white" style="border-radius: 8px; border-left: 4px solid #d97706 !important;">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-warning px-2 py-1 mr-2 font-weight-bold text-dark">Langkah 3</span>
                                <strong class="text-dark" style="font-size: 13.5px;">Daftar Kunjungan &amp; Triage</strong>
                            </div>
                            <p class="text-secondary text-xs mb-2" style="line-height: 1.5;">
                                Petugas memasukkan pasien ke antrean poli aktif:
                            </p>
                            <ul class="text-xs text-muted mb-0 pl-3">
                                <li>Klik tombol <span class="badge badge-teal"><i class="fas fa-calendar-plus"></i> Kunjungan</span>.</li>
                                <li>Pilih Poli Tujuan (Umum / Gigi / Estetik) &amp; Dokter DPJP.</li>
                                <li>Perawat melakukan pemeriksaan TTV (Triage Awal).</li>
                            </ul>
                        </div>
                    </div>

                    <!-- TAHAP 4 -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm p-3 bg-white" style="border-radius: 8px; border-left: 4px solid #059669 !important;">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-success px-2 py-1 mr-2 font-weight-bold">Langkah 4</span>
                                <strong class="text-dark" style="font-size: 13.5px;">SOAP Dokter &amp; RME 7 Lembar</strong>
                            </div>
                            <p class="text-secondary text-xs mb-2" style="line-height: 1.5;">
                                Dokter memeriksa pasien di menu Rekam Medis (EMR):
                            </p>
                            <ul class="text-xs text-muted mb-0 pl-3">
                                <li>Dokter mengisi SOAP, ICD-10, &amp; e-Resep Farmasi.</li>
                                <li>Semua data terhubung otomatis ke Lembar 1 s/d 7.</li>
                                <li>Berkas cetak resmi siap dicetak kapan saja.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white py-2 px-3">
                <button type="button" class="btn btn-teal btn-sm font-weight-bold" data-dismiss="modal">
                    <i class="fas fa-check mr-1"></i> Saya Paham Alur Kerja
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EKSPOR DATABASE & CETAK LAPORAN PASIEN                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalExportPasien" tabindex="-1" role="dialog" aria-labelledby="modalExportPasienLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-gradient-teal text-white py-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mr-2.5 rounded-circle bg-white text-teal font-weight-bold" style="width: 36px; height: 36px; font-size: 16px;">
                        <i class="fas fa-file-export"></i>
                    </span>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalExportPasienLabel">Ekspor Database &amp; Laporan Pasien</h5>
                        <small class="text-white-50">Unduh data master direktori pasien ke format Excel / CSV atau Cetak PDF</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <form id="form-export-pasien" method="get">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Dari Tanggal Daftar:</label>
                            <input type="date" name="start_date" id="export-start-date" class="form-control form-control-sm">
                            <small class="text-muted" style="font-size: 10.5px;">Kosongkan untuk semua waktu</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Sampai Tanggal:</label>
                            <input type="date" name="end_date" id="export-end-date" class="form-control form-control-sm">
                            <small class="text-muted" style="font-size: 10.5px;">Kosongkan untuk hari ini</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Filter Jenis Kelamin:</label>
                            <select name="gender" id="export-gender" class="form-control form-control-sm">
                                <option value="">Semua (Laki-laki &amp; Perempuan)</option>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Filter Kategori Member:</label>
                            <select name="tier" id="export-tier" class="form-control form-control-sm">
                                <option value="">Semua Kategori</option>
                                <option value="regular">Reguler</option>
                                <option value="gold">Gold Member</option>
                                <option value="vip">VIP Member</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-white rounded border mt-2">
                        <label class="text-xs font-weight-bold text-dark d-block mb-2"><i class="fas fa-hand-pointer mr-1 text-teal"></i> Pilih Format Dokumen Keluaran:</label>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success btn-block py-2 text-left" onclick="submitExportAction('excel');">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-excel fa-2x text-success mr-2"></i>
                                        <div>
                                            <strong class="d-block text-dark text-xs">Excel / CSV</strong>
                                            <small class="text-muted" style="font-size: 10px;">Spreadsheet Data</small>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info btn-block py-2 text-left" onclick="submitExportAction('pdf');">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger mr-2"></i>
                                        <div>
                                            <strong class="d-block text-dark text-xs">Cetak PDF</strong>
                                            <small class="text-muted" style="font-size: 10px;">Laporan Resmi</small>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer bg-white py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Visit Registration Modal -->
<div class="modal fade" id="visitModal" tabindex="-1" role="dialog" aria-labelledby="visitModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <h5 class="modal-title font-weight-bold text-white" id="visitModalLabel"><i class="fas fa-ticket mr-1"></i> Registrasi Kunjungan Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('klinik/pendaftaran') ?>" method="post">
                <input type="hidden" name="action" value="create_visit">
                <input type="hidden" name="patient_id" id="modal-patient-id">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pasien Terpilih</label>
                        <input type="text" id="modal-patient-name" class="form-control" readonly>
                    </div>

                    <!-- Visit Type Category Select -->
                    <div class="form-group">
                        <label>Kategori Kunjungan <span class="text-danger">*</span></label>
                        <select name="visit_type" id="modal-visit-type" class="form-control font-weight-bold" required>
                            <option value="poli">Pemeriksaan Poliklinik (Spesialis)</option>
                            <option value="tindakan">Tindakan Medis Langsung (Saja)</option>
                        </select>
                    </div>

                    <!-- Polyclinic select (Visible for Poli) -->
                    <div class="form-group" id="group-polyclinic">
                        <label>Poliklinik Tujuan <span class="text-danger">*</span></label>
                        <select name="polyclinic_id" id="modal-polyclinic-id" class="form-control font-weight-bold" required>
                            <option value="">-- Pilih Poliklinik --</option>
                            <?php foreach ($polyclinics as $poly): ?>
                                <?php if ($poly->name !== 'Tindakan Saja'): ?>
                                    <option value="<?= $poly->id ?>"><?= esc($poly->name) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Direct medical procedure select (Visible for Tindakan Saja) -->
                    <div class="form-group" id="group-tindakan" style="display:none;">
                        <label>Pilihan Tindakan Medis <span class="text-danger">*</span></label>
                        <select name="service_id" id="modal-service-id" class="form-control select-searchable font-weight-bold" data-search-placeholder="🔍 Ketik nama tindakan medis...">
                            <option value="">-- Pilih Tindakan Medis --</option>
                            <?php 
                            $grouped = [];
                            foreach ($tindakanServices as $srv) {
                                if ($srv->price === null) continue; // Header category is not directly selectable
                                $groupName = $srv->parent_name ?: 'Standalone / Lainnya';
                                $grouped[$groupName][] = $srv;
                            }
                            foreach ($grouped as $group => $items): 
                            ?>
                                <optgroup label="<?= esc($group) ?>">
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item->id ?>"><?= esc($item->name) ?> (Rp <?= number_format($item->price, 0, ',', '.') ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Doctor select (Filtered automatically) -->
                    <div class="form-group">
                        <label id="label-doctor">Dokter Pemeriksa <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="modal-doctor-id" class="form-control select-searchable font-weight-bold" data-search-placeholder="🔍 Ketik nama dokter..." required>
                            <!-- Options rendered dynamically -->
                        </select>
                    </div>

                    <!-- Ruangan Pelayanan / Poli -->
                    <div class="form-group">
                        <label>Ruangan Pemeriksaan / Poli <small class="text-muted">(Opsional / Otomatis)</small></label>
                        <select name="room_id" id="modal-room-id" class="form-control font-weight-bold text-dark">
                            <option value="">-- Otomatis Sesuai Poli / Tindakan --</option>
                            <?php if (!empty($rooms)): ?>
                                <?php foreach ($rooms as $rm): ?>
                                    <option value="<?= $rm->id ?>"><?= esc($rm->code) ?> - <?= esc($rm->name) ?> (<?= strtoupper($rm->type) ?> - <?= esc($rm->floor) ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Metode Penjamin Bayar <span class="text-danger">*</span></label>
                        <select name="payment_method" id="modal-payment-method" class="form-control font-weight-bold" required>
                            <?php if (!empty($paymentMethods)): ?>
                                <?php foreach ($paymentMethods as $pm): ?>
                                    <option value="<?= esc($pm->code) ?>">
                                        <?= esc($pm->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="umum">Umum (Mandiri)</option>
                                <option value="bpjs">Klaim BPJS</option>
                                <option value="asuransi">Asuransi Swasta</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Dropdown Mitra Asuransi Swasta (Auto-Tampil jika Metode Bayar Asuransi) -->
                    <div class="form-group" id="grp-modal-insurance" style="display:none;">
                        <label class="text-teal font-weight-bold"><i class="fas fa-handshake mr-1"></i> Pilih Mitra Asuransi / Penjamin <span class="text-danger">*</span></label>
                        <select name="insurance_id" id="modal-insurance-id" class="form-control font-weight-bold">
                            <option value="">-- Pilih Asuransi Rekanan --</option>
                            <?php if (!empty($insuranceProviders)): ?>
                                <?php foreach ($insuranceProviders as $ip): ?>
                                    <option value="<?= $ip->id ?>"><?= esc($ip->code) ?> - <?= esc($ip->name) ?> (<?= strtoupper($ip->type) ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-paper-plane"></i> Kirim ke Antrean</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL RIWAYAT REKAM MEDIS & SOAP PASIEN SUPER LENGKAP (RME SUITE) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalMedicalHistory" tabindex="-1" role="dialog" aria-labelledby="modalMedicalHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content" style="border: 1px solid #b8b8b8; border-radius: 4px;">
            <div class="modal-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="p-1 px-2 rounded bg-teal text-white mr-2 font-weight-bold" style="font-size: 13px;">
                        <i class="fas fa-notes-medical"></i> RME
                    </span>
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalMedicalHistoryLabel">
                            Rekam Medis Elektronik &amp; Riwayat Kunjungan Pasien
                        </h5>
                        <small class="text-muted">Pencatatan medis terintegrasi berstandar SATUSEHAT &amp; Akreditasi Klinik</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0)" id="rme-btn-print-summary" target="_blank" class="btn btn-outline-teal btn-sm font-weight-bold mr-2">
                        <i class="fas fa-print mr-1"></i> Cetak Ringkasan RME (PDF)
                    </a>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-3 bg-light" style="max-height: 82vh; overflow-y: auto;">
                <!-- Loading State -->
                <div id="rme-loading" class="text-center py-5">
                    <div class="spinner-border text-teal" role="status"></div>
                    <p class="text-muted mt-2 text-xs font-weight-bold">Memuat rekam medis lengkap pasien...</p>
                </div>

                <!-- Content State -->
                <div id="rme-content" style="display: none;">
                    <!-- Patient Bio Header Card -->
                    <div class="card p-3 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8; border-left: 5px solid #0d9f4f; border-radius: 4px;">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-7 border-right">
                                <div class="d-flex align-items-center mb-1 flex-wrap">
                                    <h4 class="font-weight-bold text-dark mb-0 mr-2" id="rme-patient-name">-</h4>
                                    <span class="badge badge-teal font-monospace font-weight-bold px-2 py-1 mr-2" id="rme-patient-rm" style="font-size: 13px;">-</span>
                                    <span class="badge badge-light border text-muted" id="rme-patient-gender-badge">-</span>
                                </div>
                                <div class="row text-xs text-muted mt-2">
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-id-card text-secondary mr-1"></i> NIK: <strong id="rme-patient-nik" class="text-dark font-monospace">-</strong>
                                    </div>
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-birthday-cake text-secondary mr-1"></i> Lahir: <strong id="rme-patient-pob-dob" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-user-clock text-secondary mr-1"></i> Usia: <strong id="rme-patient-age" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-phone text-secondary mr-1"></i> No. HP: <strong id="rme-patient-phone" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-shield-alt text-secondary mr-1"></i> BPJS / Asuransi: <strong id="rme-patient-bpjs" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-sm-4 mb-1">
                                        <i class="fas fa-users text-secondary mr-1"></i> Kontak Darurat: <strong id="rme-patient-emg" class="text-dark">-</strong>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <i class="fas fa-map-marker-alt text-secondary mr-1"></i> Alamat: <strong id="rme-patient-address" class="text-dark">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 pl-md-3 mt-3 mt-md-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-xs font-weight-bold text-muted">STATISTIK REKAM MEDIS:</span>
                                    <span class="badge badge-teal font-weight-bold px-2 py-1" style="font-size: 12px;">
                                        Total <span id="rme-total-visits">0</span> Kunjungan
                                    </span>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Kunjungan Terakhir:</span>
                                        <strong id="rme-last-visit-date" class="text-dark">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Status IMT / BMI Terakhir:</span>
                                        <strong id="rme-last-bmi-status" class="text-teal">-</strong>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-teal btn-sm btn-block font-weight-bold" id="rme-btn-new-visit">
                                    <i class="fas fa-ticket mr-1"></i> + Buat Kunjungan Baru
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TAB NAVIGASI RME MENGIKUTI DOKUMEN REKAM MEDIS RESMI GITRIA FARMA -->
                    <ul class="nav nav-pills mb-3 bg-white p-2 border rounded shadow-sm flex-wrap" id="rme-tabs" role="tablist" style="border: 1px solid #b8b8b8 !important;">
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link active font-weight-bold text-xs py-2 px-3" id="tab-biodata-link" data-toggle="pill" href="#tab-biodata" role="tab">
                                <i class="fas fa-id-card text-teal mr-1"></i> Lembar 1: Biodata &amp; Riwayat
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-initial-link" data-toggle="pill" href="#tab-initial" role="tab">
                                <i class="fas fa-notes-medical text-primary mr-1"></i> Lembar 2: Asesmen Awal Baru
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-timeline-link" data-toggle="pill" href="#tab-timeline" role="tab">
                                <i class="fas fa-history text-secondary mr-1"></i> Lembar 3: CPPT / SOAP Kunjungan (<span id="rme-count-visits">0</span>)
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-consent-link" data-toggle="pill" href="#tab-consent" role="tab">
                                <i class="fas fa-file-signature text-warning mr-1"></i> Lembar 4&amp;5: Informed Consent (<span id="rme-count-consents">0</span>)
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-photos-link" data-toggle="pill" href="#tab-photos" role="tab">
                                <i class="fas fa-camera text-danger mr-1"></i> Lembar 6&amp;7: Foto Pasien iPad (<span id="rme-count-photos">0</span>)
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-ttv-link" data-toggle="pill" href="#tab-ttv" role="tab">
                                <i class="fas fa-heartbeat text-danger mr-1"></i> Tren TTV &amp; IMT
                            </a>
                        </li>
                        <li class="nav-item mr-1 mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-meds-link" data-toggle="pill" href="#tab-meds" role="tab">
                                <i class="fas fa-pills text-success mr-1"></i> Terapi Obat
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link font-weight-bold text-xs py-2 px-3" id="tab-letters-link" data-toggle="pill" href="#tab-letters" role="tab">
                                <i class="fas fa-file-medical text-info mr-1"></i> Surat Medis &amp; Lab (<span id="rme-count-letters-lab">0</span>)
                            </a>
                        </li>
                    </ul>

                    <!-- TAB CONTENTS -->
                    <div class="tab-content">
                        <!-- LEMBAR 1: BIODATA LENGKAP & RIWAYAT PASIEN -->
                        <div class="tab-pane fade show active" id="tab-biodata" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="font-weight-bold text-dark text-xs mb-0">
                                        <i class="fas fa-user-tag text-teal mr-1"></i> Lembar 1: Formulir Identitas Sosial &amp; Riwayat Medis Dasar Pasien
                                    </h6>
                                    <span class="badge badge-light border text-muted font-weight-bold">Dokumen Rekam Medis Hal. 1</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                                        <!-- Kolom Kiri: Identitas -->
                                        <div class="col-md-6 border-right">
                                            <h6 class="text-xs font-weight-bold text-teal border-bottom pb-1 mb-2">A. IDENTITAS PASIEN</h6>
                                            <table class="table table-sm table-borderless text-xs mb-3">
                                                <tr>
                                                    <td style="width: 140px;" class="text-muted font-weight-bold">Nama Lengkap</td>
                                                    <td>: <strong id="l1-name" class="text-dark">-</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">No. Rekam Medis</td>
                                                    <td>: <span id="l1-rm" class="badge badge-teal font-monospace">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">NIK / KTP</td>
                                                    <td>: <span id="l1-nik" class="font-monospace text-dark font-weight-bold">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Tempat, Tgl Lahir</td>
                                                    <td>: <span id="l1-pob-dob" class="text-dark">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Jenis Kelamin / Usia</td>
                                                    <td>: <span id="l1-gender-age" class="text-dark">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Pekerjaan</td>
                                                    <td>: <strong id="l1-occupation" class="text-dark">-</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Agama</td>
                                                    <td>: <span id="l1-religion" class="text-dark">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Golongan Darah</td>
                                                    <td>: <span id="l1-blood-type" class="badge badge-danger font-weight-bold">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Status Pernikahan</td>
                                                    <td>: <span id="l1-marital-status" class="text-dark">-</span></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <!-- Kolom Kanan: Kontak & Riwayat Medis -->
                                        <div class="col-md-6 pl-md-3">
                                            <h6 class="text-xs font-weight-bold text-teal border-bottom pb-1 mb-2">B. KONTAK &amp; RIWAYAT KESEHATAN</h6>
                                            <table class="table table-sm table-borderless text-xs mb-3">
                                                <tr>
                                                    <td style="width: 140px;" class="text-muted font-weight-bold">No. Telp / WhatsApp</td>
                                                    <td>: <strong id="l1-phone" class="text-dark">-</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Alamat Domisili</td>
                                                    <td>: <span id="l1-address" class="text-dark">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">Kontak Darurat</td>
                                                    <td>: <span id="l1-emg" class="text-dark">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted font-weight-bold">No. BPJS / Asuransi</td>
                                                    <td>: <span id="l1-bpjs" class="text-dark">-</span></td>
                                                </tr>
                                            </table>

                                            <div class="alert alert-danger p-2 text-xs mb-2">
                                                <strong class="d-block mb-1"><i class="fas fa-allergies mr-1"></i> RIWAYAT ALERGI (OBAT / MAKANAN):</strong>
                                                <span id="l1-allergies" class="font-weight-bold">Tidak ada catatan alergi</span>
                                            </div>

                                            <div class="p-2 bg-light rounded border text-xs">
                                                <strong class="d-block mb-1 text-teal"><i class="fas fa-file-waveform mr-1"></i> RIWAYAT PENYAKIT TERDAHULU (RPD):</strong>
                                                <span id="l1-med-history" class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEMBAR 2: ASESMEN AWAL KEPERAWATAN & MEDIS PASIEN BARU -->
                        <div class="tab-pane fade" id="tab-initial" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="font-weight-bold text-dark text-xs mb-0">
                                        <i class="fas fa-stethoscope text-primary mr-1"></i> Lembar 2: Hasil Anamnesa Awal Perawat &amp; SOAP Pertama Dokter
                                    </h6>
                                    <span class="badge badge-primary font-weight-bold">Pemeriksaan Awal (Kunjungan Pertama)</span>
                                </div>
                                <div class="card-body p-3" id="l2-initial-container">
                                    <!-- Diisi oleh JS -->
                                </div>
                            </div>
                        </div>

                        <!-- LEMBAR 3: CATATAN PERKEMBANGAN PASIEN TERINTEGRASI (CPPT / KUNJUNGAN LAMA) -->
                        <div class="tab-pane fade" id="tab-timeline" role="tabpanel">
                            <div class="card bg-white shadow-none mb-3" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="font-weight-bold text-dark text-xs mb-0">
                                        <i class="fas fa-history text-secondary mr-1"></i> Lembar 3: Catatan Perkembangan Pasien Terintegrasi (CPPT / Pasien Kunjungan Ulang)
                                    </h6>
                                    <span class="badge badge-teal font-weight-bold"><span id="l3-total-visits">0</span> Kunjungan Terdaftar</span>
                                </div>
                            </div>
                            <div id="rme-visits-container">
                                <!-- Cards Kunjungan Dimasukkan oleh JavaScript -->
                            </div>
                        </div>

                        <!-- LEMBAR 4 & 5: PERSETUJUAN TINDAKAN MEDIS (INFORMED CONSENT) -->
                        <div class="tab-pane fade" id="tab-consent" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="font-weight-bold text-dark text-xs mb-0">
                                            <i class="fas fa-file-signature text-warning mr-1"></i> Lembar 4 &amp; 5: Surat Persetujuan / Penolakan Tindakan Medis (Informed Consent)
                                        </h6>
                                        <small class="text-muted">Pemberian edukasi, penjelasan risiko medis, dan tanda tangan digital sah pasien/wali &amp; dokter DPJP</small>
                                    </div>
                                    <button type="button" class="btn btn-teal btn-sm font-weight-bold mt-1 mt-md-0" id="btn-open-modal-consent">
                                        <i class="fas fa-plus mr-1"></i> + Buat Informed Consent Baru (TTD Digital)
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0 text-xs">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 35px;" class="text-center">No</th>
                                                    <th>Tanggal Dokumen</th>
                                                    <th>Nama Tindakan / Prosedur</th>
                                                    <th>Diagnosa &amp; Indikasi</th>
                                                    <th class="text-center">Jenis / Status</th>
                                                    <th>Dokter Penanggung Jawab</th>
                                                    <th>Penanggung Jawab / Wali</th>
                                                    <th class="text-center" style="width: 130px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rme-consent-tbody">
                                                <!-- Diisi oleh JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEMBAR 6 & 7: DOKUMENTASI FOTO PASIEN (IPAD / KAMERA KLINIS) -->
                        <div class="tab-pane fade" id="tab-photos" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h6 class="font-weight-bold text-dark text-xs mb-0">
                                            <i class="fas fa-camera text-danger mr-1"></i> Lembar 6 &amp; 7: Dokumentasi Foto Pasien (iPad &amp; Kamera Medis Klinis)
                                        </h6>
                                        <small class="text-muted">Dokumentasi visual perkembangan klinis (Before, Process, After, Hasil Tindakan/Luka) via iPad/Tablet Perawat</small>
                                    </div>
                                    <button type="button" class="btn btn-teal btn-sm font-weight-bold mt-1 mt-md-0" id="btn-open-modal-photo">
                                        <i class="fas fa-camera mr-1"></i> 📷 Ambil Foto Kamera iPad / Upload Berkas
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row" id="rme-photos-grid">
                                        <!-- Diisi oleh JS: Foto Pasien -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TREN TANDA VITAL (TTV TRACKER) -->
                        <div class="tab-pane fade" id="tab-ttv" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom">
                                    <h6 class="font-weight-bold text-dark text-xs mb-0">
                                        <i class="fas fa-chart-line text-danger mr-1"></i> Tabel Komparasi Indikator Tanda Vital Lintas Kunjungan
                                    </h6>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered table-striped mb-0 text-dark" style="font-size: 12px;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Tanggal Kunjungan</th>
                                                <th>No. Visit &amp; Poli</th>
                                                <th class="text-center">Tekanan Darah</th>
                                                <th class="text-center">Nadi</th>
                                                <th class="text-center">Suhu</th>
                                                <th class="text-center">RR (Nafas)</th>
                                                <th class="text-center">Berat Badan</th>
                                                <th class="text-center">Tinggi Badan</th>
                                                <th class="text-center">IMT (BMI)</th>
                                                <th>Keluhan / Anamnesis</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rme-ttv-table-body">
                                            <!-- Diisi oleh JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- RIWAYAT TERAPI OBAT KUMULATIF -->
                        <div class="tab-pane fade" id="tab-meds" role="tabpanel">
                            <div class="card bg-white shadow-none" style="border: 1px solid #b8b8b8;">
                                <div class="card-header bg-white py-2 border-bottom">
                                    <h6 class="font-weight-bold text-dark text-xs mb-0">
                                        <i class="fas fa-capsules text-success mr-1"></i> Rekapitulasi Terapi Obat Pasien dari Waktu ke Waktu
                                    </h6>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered table-striped mb-0 text-dark" style="font-size: 12px;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 40px;" class="text-center">No</th>
                                                <th>Nama Obat / Formula</th>
                                                <th class="text-center">Total Diberikan</th>
                                                <th class="text-center">Frekuensi Resep</th>
                                                <th>Dosis &amp; Signa Terakhir</th>
                                                <th>Tanggal Terakhir Diberikan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rme-meds-table-body">
                                            <!-- Diisi oleh JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- SURAT MEDIS & HASIL LAB -->
                        <div class="tab-pane fade" id="tab-letters" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-white shadow-none h-100" style="border: 1px solid #b8b8b8;">
                                        <div class="card-header bg-white py-2 border-bottom">
                                            <h6 class="font-weight-bold text-dark text-xs mb-0">
                                                <i class="fas fa-file-medical text-info mr-1"></i> Surat Medis Diterbitkan (SKS / SKD / Rujukan)
                                            </h6>
                                        </div>
                                        <div class="card-body p-2" id="rme-letters-list">
                                            <!-- Diisi oleh JS -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-white shadow-none h-100" style="border: 1px solid #b8b8b8;">
                                        <div class="card-header bg-white py-2 border-bottom">
                                            <h6 class="font-weight-bold text-dark text-xs mb-0">
                                                <i class="fas fa-flask text-purple mr-1"></i> Pemeriksaan &amp; Hasil Laboratorium
                                            </h6>
                                        </div>
                                        <div class="card-body p-2" id="rme-lab-list">
                                            <!-- Diisi oleh JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white py-2 d-flex justify-content-between">
                <span class="text-muted text-xs"><i class="fas fa-lock text-teal mr-1"></i> Data rekam medis bersifat rahasia dan terlindungi regulasi medis.</span>
                <div>
                    <a href="<?= base_url('klinik/soap') ?>" class="btn btn-outline-teal btn-sm font-weight-bold mr-1">
                        <i class="fas fa-stethoscope mr-1"></i> Buka Lembar Kerja SOAP
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL BUAT INFORMED CONSENT DENGAN CANVAS TANDA TANGAN DIGITAL (LEMBAR 4&5) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalCreateInformedConsent" tabindex="-1" role="dialog" aria-labelledby="modalCreateInformedConsentLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header bg-teal py-2">
                <h5 class="modal-title font-weight-bold text-white text-sm" id="modalCreateInformedConsentLabel">
                    <i class="fas fa-file-signature mr-1"></i> Lembar Persetujuan / Penolakan Tindakan Medis (Informed Consent)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-save-informed-consent">
                <input type="hidden" name="patient_id" id="consent-patient-id">
                <input type="hidden" name="visit_id" id="consent-visit-id">
                <input type="hidden" name="authorized_person_signature" id="consent-patient-signature-val">
                <input type="hidden" name="doctor_signature" id="consent-doctor-signature-val">
                
                <div class="modal-body p-3 text-xs" style="max-height: 80vh; overflow-y: auto;">
                    <div class="alert alert-info py-2 px-3 mb-2 text-xs">
                        <strong>Pasien Terpilih:</strong> <span id="consent-patient-label" class="font-weight-bold">-</span>
                    </div>

                    <!-- TEMPLATE AUTO-FILL SELECTOR -->
                    <div class="p-2.5 rounded mb-3 border" style="background: #f0fdfa; border-color: #99f6e4 !important;">
                        <label class="text-xs font-weight-bold text-dark mb-1">
                            <i class="fas fa-magic text-teal mr-1"></i> Pilih Template Edukasi Standar (Auto-Fill Instan):
                        </label>
                        <select id="consent-template-dropdown" class="form-control form-control-sm font-weight-bold text-dark border-teal">
                            <option value="">-- Ketik Manual atau Pilih Template Standar --</option>
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 10px;">Memilih template akan otomatis mengisi nama tindakan, indikasi, risiko, komplikasi, dan prognosis.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Dokter Pelaksana Tindakan <span class="text-danger">*</span></label>
                            <select name="doctor_id" id="consent-doctor-id" class="form-control form-control-sm font-weight-bold" required>
                                <option value="">-- Pilih Dokter Pelaksana --</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc->id ?>"><?= esc($doc->name) ?><?= !empty($doc->sip_number) ? ' (' . esc($doc->sip_number) . ')' : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Jenis Pernyataan Tindakan <span class="text-danger">*</span></label>
                            <select name="consent_type" class="form-control form-control-sm font-weight-bold text-teal" required>
                                <option value="agree" selected>✔ PERSETUJUAN TINDAKAN KEDOKTERAN</option>
                                <option value="refuse">❌ PENOLAKAN TINDAKAN KEDOKTERAN</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Nama Tindakan Medis / Prosedur <span class="text-danger">*</span></label>
                            <input type="text" name="procedure_name" id="consent-procedure-name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Ekstraksi Kuku, Cauter Kulit, Injeksi Vitamin" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Diagnosis Medis Terkait <span class="text-danger">*</span></label>
                            <input type="text" name="diagnosis" id="consent-diagnosis" class="form-control form-control-sm" placeholder="Contoh: Vulnus Laceratum, Veruka Vulgaris" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Indikasi Tindakan</label>
                            <textarea name="indication" id="consent-indication" class="form-control form-control-sm" rows="2" placeholder="Indikasi medis dilakukannya tindakan"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Tata Cara / Prosedur Singkat</label>
                            <textarea name="procedure_desc" id="consent-procedure-desc" class="form-control form-control-sm" rows="2" placeholder="Penjelasan tata cara tindakan yang dilakukan"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Risiko, Efek Samping &amp; Komplikasi</label>
                            <textarea name="risks_complications" id="consent-risks-complications" class="form-control form-control-sm" rows="2" placeholder="Pendarahan ringan, nyeri sementara, kemerahan..."></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Prognosis Tindakan</label>
                            <input type="text" name="prognosis" id="consent-prognosis" class="form-control form-control-sm" value="Dubia ad Bonam (Baik)">
                        </div>
                    </div>

                    <hr class="my-2">
                    <h6 class="font-weight-bold text-dark text-xs mb-2"><i class="fas fa-user-shield text-teal mr-1"></i> Penanggung Jawab Pasien / Saksi:</h6>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Nama Yang Menyatakan <span class="text-danger">*</span></label>
                            <input type="text" name="authorized_person_name" id="consent-person-name" class="form-control form-control-sm font-weight-bold" placeholder="Nama Pasien / Wali" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Hubungan dgn Pasien</label>
                            <select name="authorized_person_relation" class="form-control form-control-sm">
                                <option value="Diri Sendiri" selected>Diri Sendiri</option>
                                <option value="Suami / Istri">Suami / Istri</option>
                                <option value="Orang Tua / Ibu / Ayah">Orang Tua / Ibu / Ayah</option>
                                <option value="Anak">Anak</option>
                                <option value="Keluarga / Kerabat">Keluarga / Kerabat</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Nama Saksi Medis (Perawat)</label>
                            <input type="text" name="witness_name" class="form-control form-control-sm" placeholder="Nama Perawat Klinik">
                        </div>
                    </div>

                    <hr class="my-2">
                    <h6 class="font-weight-bold text-dark text-xs mb-2"><i class="fas fa-pen-nib text-teal mr-1"></i> Tanda Tangan Digital Langsung (iPad / Touchscreen / Mouse):</h6>

                    <div class="row">
                        <!-- Canvas Pasien/Wali -->
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">Tanda Tangan Pasien / Wali:</strong>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" id="btn-clear-sig-patient"><i class="fas fa-eraser mr-1"></i> Hapus TTD</button>
                            </div>
                            <div class="border rounded bg-white" style="border: 2px dashed #0d9488 !important;">
                                <canvas id="canvas-sig-patient" style="width: 100%; height: 130px; display: block; touch-action: none; cursor: crosshair;"></canvas>
                            </div>
                            <small class="text-muted">Gunakan jari / Apple Pencil / mouse untuk tanda tangan di area kotak.</small>
                        </div>

                        <!-- Canvas Dokter -->
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">Tanda Tangan Dokter DPJP:</strong>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" id="btn-clear-sig-doctor"><i class="fas fa-eraser mr-1"></i> Hapus TTD</button>
                            </div>
                            <div class="border rounded bg-white" style="border: 2px dashed #0d9488 !important;">
                                <canvas id="canvas-sig-doctor" style="width: 100%; height: 130px; display: block; touch-action: none; cursor: crosshair;"></canvas>
                            </div>
                            <small class="text-muted">Gunakan jari / Apple Pencil / mouse untuk tanda tangan dokter.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold" id="btn-submit-consent">
                        <i class="fas fa-save mr-1"></i> Simpan Dokumen Informed Consent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL UPLOAD / AMBIL FOTO PASIEN IPAD (LEMBAR 6 & 7) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalUploadMedicalPhoto" tabindex="-1" role="dialog" aria-labelledby="modalUploadMedicalPhotoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-teal py-2">
                <h5 class="modal-title font-weight-bold text-white text-sm" id="modalUploadMedicalPhotoLabel">
                    <i class="fas fa-camera mr-1"></i> Ambil / Unggah Foto Dokumentasi Pasien (iPad)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-save-medical-photo" enctype="multipart/form-data">
                <input type="hidden" name="patient_id" id="photo-patient-id">
                <input type="hidden" name="visit_id" id="photo-visit-id">

                <div class="modal-body p-3 text-xs">
                    <div class="alert alert-info py-2 px-3 mb-3 text-xs">
                        <strong>Pasien:</strong> <span id="photo-patient-label" class="font-weight-bold">-</span>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Kategori Foto Dokumentasi <span class="text-danger">*</span></label>
                        <select name="category" class="form-control form-control-sm font-weight-bold" required>
                            <option value="before">🔴 Sebelum Tindakan (Before / Kondisi Awal)</option>
                            <option value="process">🟡 Sedang Tindakan (Process / Intra-Operative)</option>
                            <option value="after">🟢 Sesudah Tindakan (After / Hasil Akhir)</option>
                            <option value="other">⚪ Dokumentasi Lainnya / Berkas Penunjang</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Judul / Area Foto <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Foto Luka Robek Dahi, Foto Gigi Premolar Kanan" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Berkas Foto / Potret Langsung dari iPad <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" name="photo_file" id="input-photo-file" class="custom-file-input form-control-sm" accept="image/*" capture="environment" required>
                            <label class="custom-file-label text-truncate" for="input-photo-file">Pilih berkas foto / Kamera iPad...</label>
                        </div>
                        <small class="text-muted">Di iPad/Tablet/HP, klik tombol di atas untuk langsung membuka kamera belakang.</small>
                    </div>

                    <div id="photo-preview-container" class="text-center my-2 p-2 border rounded bg-light" style="display: none;">
                        <img id="photo-preview-img" src="#" alt="Preview Foto" style="max-height: 180px; max-width: 100%; object-fit: contain; border-radius: 4px;">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Tambahan / Catatan Klinis</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Catatan visual kondisi luka, warna, ukuran, dsb"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Nama Petugas Pengambil Foto</label>
                        <input type="text" name="taken_by" class="form-control form-control-sm" placeholder="Nama Perawat / Dokter" value="<?= esc(session()->get('user_name') ?: 'Perawat Klinik') ?>">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold" id="btn-submit-photo">
                        <i class="fas fa-upload mr-1"></i> Simpan Foto Dokumentasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Load seeded doctor list into JS variable
    const allDoctors = <?= json_encode($doctors) ?>;
    var tablePatients;
    var currentRmePatient = null;
    var sigCanvasPatient = null;
    var sigCanvasDoctor = null;

    $(document).ready(function() {
        // Inisialisasi DataTables Server-Side Processing (Aman dari Reinitialise Warning)
        if ($.fn.DataTable.isDataTable('#table-patients')) {
            $('#table-patients').DataTable().destroy();
        }
        tablePatients = $('#table-patients').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            destroy: true,
            ajax: {
                url: '<?= base_url('klinik/pendaftaran') ?>',
                type: 'GET'
            },
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [6], orderable: false, className: 'text-center', responsivePriority: 1 },
                { targets: [0], className: 'text-center', responsivePriority: 2 },
                { targets: [2], responsivePriority: 3 },
                { targets: [1], responsivePriority: 4 },
                { targets: [4], className: 'text-center', responsivePriority: 5 },
                { targets: [3], className: 'text-center', responsivePriority: 6 },
                { targets: [5], responsivePriority: 7 }
            ],
            language: window.dtIndonesianLanguage,
            pageLength: 10
        });

        // Delegated Event Handler: Tombol Detail Identitas Pasien (Modal Popup)
        $(document).on('click', '.btn-show-patient-detail', function(e) {
            e.preventDefault();
            const patientId = $(this).data('id');
            if (patientId) {
                showPatientDetailModal(patientId);
            }
        });

        // Delegated Event Handler: Tombol Edit Pasien
        $(document).on('click', '.btn-edit-patient', function() {
            $('#edit-patient-id').val($(this).data('id'));
            $('#edit-nik').val($(this).data('nik'));
            $('#edit-name').val($(this).data('name'));
            $('#edit-gender').val($(this).data('gender'));
            $('#edit-pob').val($(this).data('pob'));
            $('#edit-dob').val($(this).data('dob'));
            $('#edit-phone').val($(this).data('phone'));
            $('#edit-address').val($(this).data('address'));
            $('#edit-emg-name').val($(this).data('emg-name'));
            $('#edit-emg-phone').val($(this).data('emg-phone'));
            $('#edit-bpjs').val($(this).data('bpjs'));
            $('#edit-tier').val($(this).data('tier') || 'regular');
            $('#edit-occupation').val($(this).data('occupation') || '');
            $('#edit-religion').val($(this).data('religion') || 'Islam');
            $('#edit-blood-type').val($(this).data('blood-type') || '-');
            $('#edit-marital-status').val($(this).data('marital-status') || 'Menikah');
            $('#edit-allergies').val($(this).data('allergies') || '');
            $('#edit-medical-history').val($(this).data('medical-history') || '');
        });

        // Delegated Event Handler: Tombol Rekam Medis (RME / Riwayat Kunjungan Pasien)
        $(document).on('click', '.btn-show-rme, .badge-rm-code', function(e) {
            e.preventDefault();
            const patientId = $(this).data('id');
            if (patientId) {
                showMedicalHistory(patientId);
            }
        });

        // Delegated Event Handler: Tombol Kunjungan Pasien
        $(document).on('click', '.btn-visit', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const rm = $(this).data('rm');
            const bpjs = $(this).data('bpjs');
            const insurance = $(this).data('insurance');

            $('#modal-patient-id').val(id);
            $('#modal-patient-name').val(name + ' (' + rm + ')');

            if (insurance) {
                $('#modal-payment-method').val('asuransi').trigger('change');
                $('#modal-insurance-id').val(insurance);
            } else if (bpjs && bpjs.toString().trim() !== '') {
                $('#modal-payment-method').val('bpjs').trigger('change');
            } else {
                $('#modal-payment-method').val('umum').trigger('change');
            }

            $('#modal-visit-type').val('poli').trigger('change');
        });

        // Handler when Visit Type is changed
        $('#modal-visit-type').change(function() {
            const type = $(this).val();
            if (type === 'poli') {
                $('#group-polyclinic').show();
                $('#modal-polyclinic-id').prop('required', true);
                $('#group-tindakan').hide();
                $('#modal-service-id').prop('required', false).val('');
                $('#label-doctor').html('Dokter Pemeriksa <span class="text-danger">*</span>');
                filterDoctorsByPolyclinic();
            } else {
                $('#group-polyclinic').hide();
                $('#modal-polyclinic-id').prop('required', false).val('');
                $('#group-tindakan').show();
                $('#modal-service-id').prop('required', true);
                $('#label-doctor').html('Dokter Pelaksana / PJ Tindakan <span class="text-danger">*</span>');
                loadAllDoctors();
            }
        });

        const allDoctorSchedules = <?= json_encode($doctorSchedules ?? []) ?>;
        const daysMap = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const todayName = daysMap[new Date().getDay()];

        $('#modal-payment-method').change(function() {
            const val = $(this).val();
            if (val === 'asuransi') {
                $('#grp-modal-insurance').slideDown(200);
                $('#modal-insurance-id').prop('required', true);
            } else {
                $('#grp-modal-insurance').slideUp(200);
                $('#modal-insurance-id').prop('required', false).val('');
            }
        });

        $('#modal-polyclinic-id').change(function() {
            filterDoctorsByPolyclinic();
        });

        $('#modal-service-id').change(function() {
            filterDoctorsByTindakan();
        });

        $('#modal-doctor-id').change(function() {
            const selectedOpt = $(this).find(':selected');
            const targetRoom = selectedOpt.data('room');
            if (targetRoom) {
                $('#modal-room-id').val(targetRoom);
            }
        });

        function filterDoctorsByPolyclinic() {
            const polyId = $('#modal-polyclinic-id').val();
            const docSelect = $('#modal-doctor-id');
            docSelect.empty();

            if (!polyId) {
                docSelect.append('<option value="">-- Pilih Poliklinik Terlebih Dahulu --</option>');
                return;
            }

            const docs = allDoctors.filter(d => d.polyclinic_id == polyId || (d.category_id == 1 && !d.polyclinic_id));

            if (docs.length === 0) {
                docSelect.append('<option value="">Tidak ada dokter aktif di poli ini</option>');
            } else {
                docSelect.append('<option value="">-- Pilih Dokter --</option>');
                let autoSelectedRoom = null;
                let autoSelectedDocId = null;

                docs.forEach(d => {
                    const schedToday = allDoctorSchedules.find(s => s.doctor_id == d.id && s.day_of_week === todayName);
                    let badgeSched = '';
                    let roomIdAttr = '';
                    if (schedToday) {
                        badgeSched = ` (⭐ Praktek Hari Ini: ${schedToday.start_time.substring(0,5)}-${schedToday.end_time.substring(0,5)} | Kuota: ${schedToday.max_quota})`;
                        if (schedToday.room_id) {
                            roomIdAttr = schedToday.room_id;
                            autoSelectedRoom = schedToday.room_id;
                        }
                        if (!autoSelectedDocId) autoSelectedDocId = d.id;
                    }
                    docSelect.append(`<option value="${d.id}" data-room="${roomIdAttr}">${d.name}${badgeSched}</option>`);
                });

                if (docs.length === 1) {
                    docSelect.val(docs[0].id).trigger('change');
                } else if (autoSelectedDocId) {
                    docSelect.val(autoSelectedDocId).trigger('change');
                }

                if (autoSelectedRoom) {
                    $('#modal-room-id').val(autoSelectedRoom);
                }
            }
        }

        function filterDoctorsByTindakan() {
            const serviceId = $('#modal-service-id').val();
            const docSelect = $('#modal-doctor-id');
            docSelect.empty();

            if (!serviceId) {
                loadAllDoctors();
                return;
            }

            const docs = allDoctors.filter(d => d.tindakan_id == serviceId || d.category_id == 2);

            if (docs.length === 0) {
                loadAllDoctors();
            } else if (docs.length === 1) {
                docSelect.append('<option value="' + docs[0].id + '" selected>' + docs[0].name + '</option>');
            } else {
                docSelect.append('<option value="">-- Pilih Dokter Pelaksana --</option>');
                docs.forEach(d => {
                    docSelect.append('<option value="' + d.id + '">' + d.name + (d.fee_per_pasien > 0 ? ' (Fee: Rp ' + Number(d.fee_per_pasien).toLocaleString('id-ID') + ')' : '') + '</option>');
                });
            }
        }

        function loadAllDoctors() {
            const docSelect = $('#modal-doctor-id');
            docSelect.empty();
            const docs = allDoctors.filter(d => d.category_id == 2 || !d.category_id);

            if (docs.length === 0) {
                docSelect.append('<option value="">Tidak ada dokter/pelaksana aktif</option>');
            } else if (docs.length === 1) {
                docSelect.append('<option value="' + docs[0].id + '" selected>' + docs[0].name + '</option>');
            } else {
                docSelect.append('<option value="">-- Pilih Dokter Pelaksana --</option>');
                docs.forEach(d => {
                    docSelect.append('<option value="' + d.id + '">' + d.name + (d.fee_per_pasien > 0 ? ' (Fee: Rp ' + Number(d.fee_per_pasien).toLocaleString('id-ID') + ')' : '') + '</option>');
                });
            }
        }

        // =========================================================================
        // DIGITAL SIGNATURE CANVAS INITIALIZER (FOR TOUCHSCREEN, IPAD, & MOUSE)
        // =========================================================================
        function setupSignaturePad(canvasId, clearBtnId, hiddenInputId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            const ctx = canvas.getContext('2d');
            let drawing = false;

            function resize() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                ctx.scale(ratio, ratio);
                ctx.strokeStyle = "#000080"; // Navy ink
                ctx.lineWidth = 2.5;
                ctx.lineCap = "round";
                ctx.lineJoin = "round";
            }
            resize();

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function start(e) {
                drawing = true;
                const p = getPos(e);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                if (e.cancelable) e.preventDefault();
            }

            function draw(e) {
                if (!drawing) return;
                const p = getPos(e);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
                if (e.cancelable) e.preventDefault();
            }

            function stop() {
                if (!drawing) return;
                drawing = false;
                if (hiddenInputId) {
                    const hidden = document.getElementById(hiddenInputId);
                    if (hidden) hidden.value = canvas.toDataURL('image/png');
                }
            }

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', draw);
            window.addEventListener('mouseup', stop);

            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            window.addEventListener('touchend', stop);

            if (clearBtnId) {
                $('#' + clearBtnId).off('click').on('click', function(e) {
                    e.preventDefault();
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    if (hiddenInputId) {
                        const hidden = document.getElementById(hiddenInputId);
                        if (hidden) hidden.value = '';
                    }
                });
            }

            return { canvas, ctx, resize };
        }

        // Buka Modal Buat Informed Consent (Lembar 4 & 5)
        $('#btn-open-modal-consent').on('click', function() {
            if (!currentRmePatient) return;
            $('#consent-patient-id').val(currentRmePatient.id);
            $('#consent-patient-label').text(currentRmePatient.name + ' (' + currentRmePatient.no_rm + ')');
            $('#consent-person-name').val(currentRmePatient.name);
            $('#form-save-informed-consent')[0].reset();
            $('#consent-patient-id').val(currentRmePatient.id);
            $('#consent-person-name').val(currentRmePatient.name);
            $('#consent-patient-signature-val').val('');
            $('#consent-doctor-signature-val').val('');

            $('#modalCreateInformedConsent').modal('show');
            
            // Muat Template Informed Consent jika belum dimuat
            if ($('#consent-template-dropdown option').length <= 1) {
                $.getJSON('<?= base_url('system/master-klinik/consent-templates-json') ?>', function(res) {
                    if (res.status === 'success' && res.data) {
                        window.consentTemplatesData = res.data;
                        let optHtml = '<option value="">-- Ketik Manual atau Pilih Template Standar --</option>';
                        res.data.forEach(function(t, idx) {
                            optHtml += '<option value="' + idx + '">' + t.template_title + '</option>';
                        });
                        $('#consent-template-dropdown').html(optHtml);
                    }
                });
            }

            setTimeout(function() {
                sigCanvasPatient = setupSignaturePad('canvas-sig-patient', 'btn-clear-sig-patient', 'consent-patient-signature-val');
                sigCanvasDoctor = setupSignaturePad('canvas-sig-doctor', 'btn-clear-sig-doctor', 'consent-doctor-signature-val');
            }, 300);
        });

        // Event saat template informed consent dipilih
        $('#consent-template-dropdown').on('change', function() {
            const idx = $(this).val();
            if (idx !== '' && window.consentTemplatesData && window.consentTemplatesData[idx]) {
                const t = window.consentTemplatesData[idx];
                $('#consent-procedure-name').val(t.procedure_action || t.template_title);
                $('#consent-diagnosis').val(t.diagnosis_indication || '');
                $('#consent-indication').val(t.diagnosis_indication || '');
                $('#consent-procedure-desc').val(t.procedure_action || '');
                $('#consent-risks-complications').val(t.risks_complications || '');
                $('#consent-prognosis').val(t.prognosis || 'Dubia ad Bonam (Baik)');
            }
        });

        // Submit Form Informed Consent (Lembar 4 & 5)
        $('#form-save-informed-consent').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btn-submit-consent');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan Dokumen...');

            const formData = $(this).serialize();

            $.ajax({
                url: '<?= base_url('klinik/save-informed-consent') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(resp) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Dokumen Informed Consent');
                    if (resp.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(resp.message);
                        $('#modalCreateInformedConsent').modal('hide');
                        if (currentRmePatient) showMedicalHistory(currentRmePatient.id);
                    } else {
                        alert(resp.message || 'Gagal menyimpan persetujuan tindakan.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Dokumen Informed Consent');
                    alert('Terjadi kesalahan koneksi server saat menyimpan Informed Consent.');
                }
            });
        });

        // Hapus Dokumen Informed Consent
        $(document).on('click', '.btn-delete-consent', function() {
            const consentId = $(this).data('id');
            const procName = $(this).data('procedure');
            if (!confirm('Apakah Anda yakin ingin menghapus dokumen Informed Consent "' + procName + '"?')) {
                return;
            }

            $.ajax({
                url: '<?= base_url('klinik/delete-informed-consent') ?>/' + consentId,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(resp.message);
                        if (currentRmePatient) showMedicalHistory(currentRmePatient.id);
                    } else {
                        alert(resp.message || 'Gagal menghapus dokumen.');
                    }
                }
            });
        });

        // Buka Modal Upload Foto Klinis iPad (Lembar 6 & 7)
        $('#btn-open-modal-photo').on('click', function() {
            if (!currentRmePatient) return;
            $('#photo-patient-id').val(currentRmePatient.id);
            $('#photo-patient-label').text(currentRmePatient.name + ' (' + currentRmePatient.no_rm + ')');
            $('#form-save-medical-photo')[0].reset();
            $('#photo-patient-id').val(currentRmePatient.id);
            $('#photo-preview-container').hide();
            $('#modalUploadMedicalPhoto').modal('show');
        });

        // Preview Foto saat dipilih / diambil dari iPad Camera
        $('#input-photo-file').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                $(this).next('.custom-file-label').text(file.name);
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#photo-preview-img').attr('src', evt.target.result);
                    $('#photo-preview-container').show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Submit Upload Foto Klinis iPad (Lembar 6 & 7)
        $('#form-save-medical-photo').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btn-submit-photo');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengunggah Foto...');

            const formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('klinik/save-medical-photo') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Simpan Foto Dokumentasi');
                    if (resp.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(resp.message);
                        $('#modalUploadMedicalPhoto').modal('hide');
                        if (currentRmePatient) showMedicalHistory(currentRmePatient.id);
                    } else {
                        alert(resp.message || 'Gagal mengunggah foto pasien.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-upload mr-1"></i> Simpan Foto Dokumentasi');
                    alert('Terjadi kesalahan koneksi server saat mengunggah foto.');
                }
            });
        });

        // Hapus Foto Klinis
        $(document).on('click', '.btn-delete-photo', function() {
            const photoId = $(this).data('id');
            const title = $(this).data('title');
            if (!confirm('Apakah Anda yakin ingin menghapus foto "' + title + '"?')) {
                return;
            }

            $.ajax({
                url: '<?= base_url('klinik/delete-medical-photo') ?>/' + photoId,
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(resp.message);
                        if (currentRmePatient) showMedicalHistory(currentRmePatient.id);
                    } else {
                        alert(resp.message || 'Gagal menghapus foto.');
                    }
                }
            });
        });
    });

    /**
     * Submit Aksi Ekspor Database Pasien (Excel atau Cetak PDF)
     */
    function submitExportAction(type) {
        const startDate = $('#export-start-date').val();
        const endDate   = $('#export-end-date').val();
        const gender    = $('#export-gender').val();
        const tier      = $('#export-tier').val();

        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (gender) params.append('gender', gender);
        if (tier) params.append('tier', tier);

        let targetUrl = '';
        if (type === 'excel') {
            targetUrl = '<?= base_url('klinik/export-pasien-excel') ?>?' + params.toString();
            window.location.href = targetUrl;
        } else {
            targetUrl = '<?= base_url('klinik/cetak-laporan-pasien') ?>?' + params.toString();
            window.open(targetUrl, '_blank');
        }

        $('#modalExportPasien').modal('hide');
    }

    /**
     * Menampilkan Modal Detail Identitas Lengkap Pasien
     */
    function showPatientDetailModal(patientId) {
        $('#modalPatientDetail').modal('show');
        $('#detail-loading').show();
        $('#detail-content').hide();

        $.getJSON('<?= base_url('klinik/patient-rme-json') ?>/' + patientId, function(res) {
            if (res.status === 'success') {
                const p = res.patient;
                
                // Avatar & Header
                $('#detail-patient-avatar').text(p.name ? p.name.charAt(0).toUpperCase() : 'P');
                $('#detail-patient-name').text(p.name);
                $('#detail-patient-sub').text('No. RM: ' + p.no_rm + ' | NIK: ' + (p.nik || '-'));
                $('#detail-badge-rm').text(p.no_rm);
                
                const tier = (p.membership_tier || 'regular').toLowerCase();
                let tierText = 'Reguler';
                if (tier === 'gold') tierText = '🟡 Gold Member';
                else if (tier === 'vip' || tier === 'platinum') tierText = '⚫ VIP Member';
                $('#detail-badge-tier').text(tierText);

                $('#detail-badge-gender').html(p.gender === 'L' ? '<span class="badge badge-primary"><i class="fas fa-mars mr-1"></i>Laki-laki</span>' : '<span class="badge badge-danger"><i class="fas fa-venus mr-1"></i>Perempuan</span>');
                $('#detail-created-at').text(p.created_at ? new Date(p.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : '-');

                // Demographic Table
                $('#detail-nik').text(': ' + (p.nik || '-'));
                $('#detail-name').text(': ' + p.name);
                $('#detail-pob-dob').text(': ' + (p.place_of_birth || '-') + ', ' + (p.date_of_birth ? new Date(p.date_of_birth).toLocaleDateString('id-ID') : '-'));
                $('#detail-age').text(': ' + res.age);
                $('#detail-occupation').text(': ' + (p.occupation || '-'));
                $('#detail-religion').text(': ' + (p.religion || 'Islam'));
                $('#detail-blood-type').text(': ' + (p.blood_type || '-'));
                $('#detail-marital-status').text(': ' + (p.marital_status || '-'));
                $('#detail-bpjs').text(': ' + (p.bpjs_number || 'Umum / Non-BPJS'));

                // Contact & Medical Table
                let phoneHtml = ': -';
                if (p.phone) {
                    let clean = p.phone.replace(/[^0-9]/g, '');
                    if (clean.startsWith('0')) clean = '62' + clean.substr(1);
                    phoneHtml = ': <a href="https://wa.me/' + clean + '" target="_blank" class="text-success font-weight-bold"><i class="fab fa-whatsapp mr-1"></i>' + p.phone + '</a>';
                }
                $('#detail-phone').html(phoneHtml);
                $('#detail-address').text(': ' + (p.address || '-'));
                $('#detail-emg').text(': ' + (p.emergency_contact_name || '-') + (p.emergency_contact_phone ? ' (' + p.emergency_contact_phone + ')' : ''));

                $('#detail-allergies').text(p.allergies || 'TIDAK ADA CATATAN ALERGI');
                $('#detail-medical-history').text(p.medical_history || 'Tidak ada riwayat penyakit terdahulu.');

                // Stats
                $('#detail-count-visits').text(res.totalVisits || 0);
                $('#detail-last-visit').text(res.lastVisitDate ? new Date(res.lastVisitDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Belum Ada');
                $('#detail-count-consents').text(res.informedConsents ? res.informedConsents.length : 0);

                // Action Links
                $('#detail-btn-full-page').attr('href', '<?= base_url('klinik/pasien') ?>/' + p.id);
                $('#detail-btn-print-card').attr('href', '<?= base_url('klinik/kartu-pasien') ?>/' + p.id);

                $('#detail-btn-quick-visit').off('click').on('click', function() {
                    $('#modalPatientDetail').modal('hide');
                    $('#modal-patient-id').val(p.id);
                    $('#modal-patient-name').val(p.name + ' (' + p.no_rm + ')');
                    
                    if (p.insurance_provider_id) {
                        $('#modal-payment-method').val('asuransi').trigger('change');
                        $('#modal-insurance-id').val(p.insurance_provider_id);
                    } else if (p.bpjs_number && p.bpjs_number.trim() !== '') {
                        $('#modal-payment-method').val('bpjs').trigger('change');
                    } else {
                        $('#modal-payment-method').val('umum').trigger('change');
                    }

                    $('#modal-visit-type').val('poli').trigger('change');
                    $('#visitModal').modal('show');
                });

                $('#detail-btn-quick-rme').off('click').on('click', function() {
                    $('#modalPatientDetail').modal('hide');
                    showMedicalHistory(p.id);
                });

                $('#detail-loading').hide();
                $('#detail-content').show();
            } else {
                alert('Gagal mengambil data detail pasien.');
                $('#modalPatientDetail').modal('hide');
            }
        }).fail(function() {
            alert('Terjadi kesalahan saat memuat detail pasien.');
            $('#modalPatientDetail').modal('hide');
        });
    }

    /**
     * Menampilkan Modal Riwayat Rekam Medis (RME) Pasien Sesuai 7 Lembar Gitria Farma
     */
    function showMedicalHistory(patientId) {
        $('#modalMedicalHistory').modal('show');
        $('#rme-loading').show();
        $('#rme-content').hide();

        $.getJSON('<?= base_url('klinik/patient-rme-json') ?>/' + patientId, function(res) {
            if (res.status === 'success') {
                const p = res.patient;
                currentRmePatient = p;
                const visits = res.visits || [];
                const cumulativeMeds = res.cumulativeMeds || [];
                const letters = res.letters || [];
                const labOrders = res.labOrders || [];
                const informedConsents = res.informedConsents || [];
                const medicalPhotos = res.medicalPhotos || [];
                const initial = res.initialAssessment;
                const latestV = res.latestVitals;

                // Set Print Summary URL
                $('#rme-btn-print-summary').attr('href', '<?= base_url('klinik/cetak-ringkasan-rme') ?>/' + p.id);

                // Header Profile
                $('#rme-patient-name').text(p.name);
                $('#rme-patient-rm').text(p.no_rm);
                $('#rme-patient-nik').text(p.nik || '-');
                $('#rme-patient-gender-badge').html(p.gender === 'L' ? '<span class="text-primary font-weight-bold"><i class="fas fa-mars mr-1"></i>Laki-laki</span>' : '<span class="text-danger font-weight-bold"><i class="fas fa-venus mr-1"></i>Perempuan</span>');
                $('#rme-patient-pob-dob').text((p.place_of_birth || '-') + ', ' + (p.date_of_birth ? new Date(p.date_of_birth).toLocaleDateString('id-ID') : '-'));
                $('#rme-patient-age').text(res.age);
                $('#rme-patient-phone').text(p.phone || '-');
                $('#rme-patient-bpjs').text(p.bpjs_number || '-');
                $('#rme-patient-address').text(p.address || '-');
                $('#rme-patient-emg').text((p.emergency_contact_name || '-') + (p.emergency_contact_phone ? ' (' + p.emergency_contact_phone + ')' : ''));
                $('#rme-total-visits').text(res.totalVisits);
                $('#rme-last-visit-date').text(res.lastVisitDate ? new Date(res.lastVisitDate).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : '-');
                $('#rme-last-bmi-status').text(latestV && latestV.bmi_status ? `${latestV.bmi_status} (${latestV.bmi})` : '-');

                // Counters
                $('#rme-count-visits').text(visits.length);
                $('#l3-total-visits').text(visits.length);
                $('#rme-count-consents').text(informedConsents.length);
                $('#rme-count-photos').text(medicalPhotos.length);
                $('#rme-count-letters-lab').text(letters.length + labOrders.length);

                // Setup quick visit button from inside modal
                $('#rme-btn-new-visit').off('click').on('click', function() {
                    $('#modalMedicalHistory').modal('hide');
                    $('#modal-patient-id').val(p.id);
                    $('#modal-patient-name').val(p.name + ' (' + p.no_rm + ')');
                    
                    if (p.insurance_provider_id) {
                        $('#modal-payment-method').val('asuransi').trigger('change');
                        $('#modal-insurance-id').val(p.insurance_provider_id);
                    } else if (p.bpjs_number && p.bpjs_number.trim() !== '') {
                        $('#modal-payment-method').val('bpjs').trigger('change');
                    } else {
                        $('#modal-payment-method').val('umum').trigger('change');
                    }

                    $('#modal-visit-type').val('poli').trigger('change');
                    $('#visitModal').modal('show');
                });

                // =========================================================================
                // LEMBAR 1: IDENTITAS SOSIAL & BIODATA PASIEN
                // =========================================================================
                $('#l1-name').text(p.name);
                $('#l1-rm').text(p.no_rm);
                $('#l1-nik').text(p.nik || '-');
                $('#l1-pob-dob').text((p.place_of_birth || '-') + ', ' + (p.date_of_birth ? new Date(p.date_of_birth).toLocaleDateString('id-ID') : '-'));
                $('#l1-gender-age').text((p.gender === 'L' ? 'Laki-Laki' : 'Perempuan') + ' / ' + res.age);
                $('#l1-occupation').text(p.occupation || '- (Belum Diisi)');
                $('#l1-religion').text(p.religion || '-');
                $('#l1-blood-type').text(p.blood_type || '-');
                $('#l1-marital-status').text(p.marital_status || '-');
                $('#l1-phone').text(p.phone || '-');
                $('#l1-address').text(p.address || '-');
                $('#l1-emg').text((p.emergency_contact_name || '-') + (p.emergency_contact_phone ? ' (' + p.emergency_contact_phone + ')' : ''));
                $('#l1-bpjs').text(p.bpjs_number || 'Umum (Non-BPJS)');
                $('#l1-allergies').html(p.allergies ? '<span class="text-danger font-weight-bold">' + p.allergies + '</span>' : '<span class="text-muted font-italic">Tidak ada riwayat alergi yang tercatat</span>');
                $('#l1-med-history').text(p.medical_history || 'Tidak ada riwayat penyakit terdahulu');

                // =========================================================================
                // LEMBAR 2: ASESMEN AWAL KEPERAWATAN & MEDIS PASIEN BARU
                // =========================================================================
                const l2Container = $('#l2-initial-container');
                l2Container.empty();

                if (!initial) {
                    l2Container.html(`
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-file-signature fa-2x mb-2 text-secondary"></i>
                            <h6>Belum Ada Rekam Asesmen Awal</h6>
                            <p class="text-xs mb-0">Pasien ini belum pernah melakukan registrasi kunjungan pertama.</p>
                        </div>
                    `);
                } else {
                    const initDate = initial.visit_date ? new Date(initial.visit_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                    l2Container.html(`
                        <div class="alert alert-light border p-2.5 mb-3 d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <strong class="text-teal"><i class="fas fa-calendar-check mr-1"></i> Tanggal Pemeriksaan Pertama:</strong>
                                <span class="text-dark font-weight-bold ml-1">${initDate}</span> (${initial.no_visit || '-'})
                            </div>
                            <div>
                                <span class="badge badge-info mr-1">${initial.polyclinic_name || initial.tindakan_name || 'Poliklinik'}</span>
                                <span class="badge badge-dark">Dokter DPJP: ${initial.doctor_name || 'Dokter Jaga'}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 border-right">
                                <h6 class="text-xs font-weight-bold text-teal border-bottom pb-1 mb-2"><i class="fas fa-user-nurse mr-1"></i> ANAMNESA &amp; TANDA VITAL AWAL:</h6>
                                <div class="p-2 bg-light rounded border text-xs mb-2">
                                    <strong>Keluhan Utama Saat Datang:</strong>
                                    <p class="mb-0 text-dark font-weight-bold mt-0.5">${initial.complaints || '-'}</p>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs mb-2">
                                    <strong>Anamnesis Keperawatan:</strong>
                                    <p class="mb-0 text-muted mt-0.5">${initial.anamnesis || 'Triage dan skrining awal selesai dilakukan.'}</p>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs">
                                    <strong>Tanda-Tanda Vital Awal:</strong>
                                    <div class="row mt-1">
                                        <div class="col-4 mb-1">TD: <strong>${initial.blood_pressure || '-'}</strong> mmHg</div>
                                        <div class="col-4 mb-1">Nadi: <strong>${initial.pulse || '-'}</strong> x/m</div>
                                        <div class="col-4 mb-1">Suhu: <strong>${initial.temperature || '-'}</strong> °C</div>
                                        <div class="col-4 mb-1">RR: <strong>${initial.respiration || '-'}</strong> x/m</div>
                                        <div class="col-4 mb-1">BB: <strong>${initial.weight || '-'}</strong> kg</div>
                                        <div class="col-4 mb-1">TB: <strong>${initial.height || '-'}</strong> cm</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 pl-md-3">
                                <h6 class="text-xs font-weight-bold text-teal border-bottom pb-1 mb-2"><i class="fas fa-stethoscope mr-1"></i> SOAP PERTAMA DOKTER DPJP:</h6>
                                <div class="p-2 bg-light rounded border text-xs mb-1.5">
                                    <strong class="text-teal">Subjective (S):</strong>
                                    <div class="text-dark mt-0.5">${initial.subjective || '-'}</div>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs mb-1.5">
                                    <strong class="text-teal">Objective (O):</strong>
                                    <div class="text-dark mt-0.5">${initial.objective || '-'}</div>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs mb-1.5">
                                    <strong class="text-teal">Assessment (A):</strong>
                                    <div class="text-dark mt-0.5">
                                        ${initial.icd10_code ? `<span class="badge badge-success mb-1">ICD-10: ${initial.icd10_code} - ${initial.icd10_desc || ''}</span><br>` : ''}
                                        ${initial.assessment || '-'}
                                    </div>
                                </div>
                                <div class="p-2 bg-light rounded border text-xs">
                                    <strong class="text-teal">Plan (P):</strong>
                                    <div class="text-dark mt-0.5">${initial.plan || '-'}</div>
                                </div>
                            </div>
                        </div>
                    `);
                }

                // =========================================================================
                // LEMBAR 3: CATATAN PERKEMBANGAN PASIEN TERINTEGRASI (CPPT / KUNJUNGAN LAMA)
                // =========================================================================
                const container = $('#rme-visits-container');
                container.empty();

                if (!visits || visits.length === 0) {
                    container.html(`
                        <div class="card p-4 text-center bg-white shadow-none" style="border: 1px solid #b8b8b8; border-radius: 4px;">
                            <i class="fas fa-file-circle-xmark fa-2x text-muted mb-2"></i>
                            <h6 class="font-weight-bold text-dark text-sm">Belum Ada Riwayat Kunjungan Lama</h6>
                            <p class="text-muted text-xs mb-0">Pasien ini belum memiliki catatan perkembangan kunjungan berkala di klinik.</p>
                        </div>
                    `);
                } else {
                    visits.forEach(function(v, idx) {
                        const visitDateStr = v.visit_date ? new Date(v.visit_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                        let bmiBadge = v.bmi ? `<span class="badge badge-light border text-teal font-weight-bold ml-1">IMT: ${v.bmi} (${v.bmi_status})</span>` : '';
                        let ttvHtml = `
                            <div class="p-2 bg-light rounded border mb-2">
                                <div class="row text-xs">
                                    <div class="col-md-2 col-4 mb-1"><strong>TD:</strong> <span class="text-dark font-weight-bold">${v.blood_pressure || '-'}</span> mmHg</div>
                                    <div class="col-md-2 col-4 mb-1"><strong>Nadi:</strong> <span class="text-dark font-weight-bold">${v.pulse || '-'}</span> x/m</div>
                                    <div class="col-md-2 col-4 mb-1"><strong>Suhu:</strong> <span class="text-dark font-weight-bold">${v.temperature || '-'}</span> °C</div>
                                    <div class="col-md-2 col-4 mb-1"><strong>RR:</strong> <span class="text-dark font-weight-bold">${v.respiration || '-'}</span> x/m</div>
                                    <div class="col-md-2 col-4 mb-1"><strong>BB:</strong> <span class="text-dark font-weight-bold">${v.weight || '-'}</span> kg</div>
                                    <div class="col-md-2 col-4 mb-1"><strong>TB:</strong> <span class="text-dark font-weight-bold">${v.height || '-'}</span> cm</div>
                                </div>
                                ${bmiBadge ? `<div class="mt-1 border-top pt-1 text-xs">${bmiBadge}</div>` : ''}
                            </div>
                        `;

                        let medsHtml = '';
                        if (v.medicines && v.medicines.length > 0) {
                            medsHtml = `
                                <div class="mt-3 pt-2 border-top">
                                    <strong class="text-xs text-teal"><i class="fas fa-pills mr-1"></i> Terapi e-Resep Obat Farmasi:</strong>
                                    <div class="table-responsive mt-1">
                                        <table class="table table-sm table-bordered bg-white mb-0 text-xs">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 30px;" class="text-center">No</th>
                                                    <th>Nama Obat / Formula</th>
                                                    <th class="text-center" style="width: 80px;">Jumlah</th>
                                                    <th>Aturan Pakai / Dosis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;
                            v.medicines.forEach(function(m, mIdx) {
                                medsHtml += `
                                    <tr>
                                        <td class="text-center">${mIdx + 1}</td>
                                        <td><strong>${m.medicine_name || 'Obat'}</strong> <span class="text-muted">(${m.medicine_type || 'Oral'})</span></td>
                                        <td class="text-center">${m.qty} ${m.unit || 'pcs'}</td>
                                        <td><span class="badge badge-light border text-dark font-weight-bold">${m.dosage || 'Sesuai petunjuk'}</span></td>
                                    </tr>
                                `;
                            });
                            medsHtml += `</tbody></table></div></div>`;
                        }

                        let icdBadge = '';
                        if (v.icd10_code) icdBadge = `<span class="badge badge-success mr-1"><i class="fas fa-book-medical mr-1"></i>ICD-10: ${v.icd10_code} - ${v.icd10_desc || ''}</span>`;
                        if (v.icd9_code) icdBadge += `<span class="badge badge-info mr-1"><i class="fas fa-syringe mr-1"></i>ICD-9-CM: ${v.icd9_code} - ${v.icd9_desc || ''}</span>`;

                        let billBadge = '';
                        if (v.billing) {
                            billBadge = v.billing.is_paid == 1 
                                ? `<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Lunas (Rp ${Number(v.billing.grand_total).toLocaleString('id-ID')})</span>`
                                : `<span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i>Belum Lunas</span>`;
                        }

                        const visitCard = `
                            <div class="card mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8; border-radius: 4px;">
                                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center border-bottom flex-wrap">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-teal font-weight-bold mr-2">${visits.length - idx}</span>
                                        <strong class="text-dark" style="font-size: 13px;"><i class="fas fa-calendar-day text-teal mr-1"></i> ${visitDateStr}</strong>
                                        <span class="badge badge-light border ml-2 text-xs font-monospace font-weight-bold">${v.no_visit || '-'}</span>
                                    </div>
                                    <div class="mt-1 mt-md-0">
                                        <span class="badge badge-info mr-1"><i class="fas fa-stethoscope mr-1"></i> ${v.polyclinic_name || v.tindakan_name || 'Poli'}</span>
                                        <span class="badge badge-dark mr-1"><i class="fas fa-user-doctor mr-1"></i> ${v.doctor_name || 'Dokter Jaga'}</span>
                                        ${billBadge}
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="font-weight-bold text-dark text-xs mb-1"><i class="fas fa-heartbeat text-danger mr-1"></i> Asesmen Tanda-Tanda Vital &amp; Skrining Keperawatan:</h6>
                                    ${ttvHtml}

                                    ${v.complaints ? `<div class="p-2 rounded bg-light border text-xs mb-2"><strong>Keluhan / Anamnesis:</strong> ${v.complaints}</div>` : ''}

                                    <div class="border-top pt-2 mt-2">
                                        <h6 class="font-weight-bold text-dark text-xs mb-2"><i class="fas fa-notes-medical text-teal mr-1"></i> Catatan Perkembangan Medis Pasien (SOAP DPJP):</h6>
                                        <div class="row text-xs">
                                            <div class="col-md-6 mb-2">
                                                <strong class="text-teal">Subjective (S):</strong>
                                                <div class="p-2 bg-light rounded border text-muted mt-1" style="min-height: 38px;">${v.subjective || '-'}</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong class="text-teal">Objective (O):</strong>
                                                <div class="p-2 bg-light rounded border text-muted mt-1" style="min-height: 38px;">${v.objective || '-'}</div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong class="text-teal">Assessment / Diagnosa (A):</strong>
                                                <div class="p-2 bg-light rounded border text-dark mt-1" style="min-height: 38px;">
                                                    ${icdBadge ? `<div class="mb-1">${icdBadge}</div>` : ''}
                                                    ${v.assessment || '-'}
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong class="text-teal">Plan &amp; Rencana Tindakan (P):</strong>
                                                <div class="p-2 bg-light rounded border text-dark mt-1" style="min-height: 38px;">${v.plan || '-'}</div>
                                            </div>
                                        </div>
                                        ${v.doctor_notes ? `<div class="p-2 bg-light rounded border text-xs text-muted mt-1"><strong>Instruksi Medis Dokter:</strong> <em>${v.doctor_notes}</em></div>` : ''}
                                    </div>

                                    ${medsHtml}
                                </div>
                            </div>
                        `;
                        container.append(visitCard);
                    });
                }

                // =========================================================================
                // LEMBAR 4 & 5: INFORMED CONSENT (PERSETUJUAN TINDAKAN MEDIS)
                // =========================================================================
                const consentTbody = $('#rme-consent-tbody');
                consentTbody.empty();

                if (!informedConsents || informedConsents.length === 0) {
                    consentTbody.html('<tr><td colspan="8" class="text-center text-muted p-3">Belum ada dokumen Informed Consent yang pernah dibuat untuk pasien ini. Klik tombol di atas untuk membuat persetujuan tindakan baru.</td></tr>');
                } else {
                    informedConsents.forEach(function(c, cIdx) {
                        const cDate = c.consent_date ? new Date(c.consent_date).toLocaleDateString('id-ID') : '-';
                        const badgeType = c.consent_type === 'refuse' 
                            ? '<span class="badge badge-danger">❌ Penolakan Tindakan</span>' 
                            : '<span class="badge badge-success">✔ Persetujuan Tindakan</span>';

                        const sigStatus = (c.authorized_person_signature ? '<span class="badge badge-success mr-1">TTD Pasien ✔</span>' : '<span class="badge badge-secondary mr-1">Tanpa TTD Pasien</span>') +
                                          (c.doctor_signature ? '<span class="badge badge-primary">TTD DPJP ✔</span>' : '<span class="badge badge-secondary">Tanpa TTD DPJP</span>');

                        consentTbody.append(`
                            <tr>
                                <td class="text-center font-weight-bold">${cIdx + 1}</td>
                                <td class="text-nowrap"><strong>${cDate}</strong><br><small class="text-muted">${c.no_visit || ''}</small></td>
                                <td><strong class="text-dark">${c.procedure_name}</strong></td>
                                <td>
                                    <span class="text-dark font-weight-bold">${c.diagnosis || '-'}</span>
                                    ${c.indication ? `<br><small class="text-muted">${c.indication}</small>` : ''}
                                </td>
                                <td class="text-center">${badgeType}<br><div class="mt-1">${sigStatus}</div></td>
                                <td>${c.doctor_name || 'Dokter DPJP'}</td>
                                <td>${c.authorized_person_name} <span class="text-muted">(${c.authorized_person_relation})</span></td>
                                <td class="text-center text-nowrap">
                                    <a href="<?= base_url('klinik/cetak-informed-consent') ?>/${c.id}" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold mr-1" title="Cetak Dokumen Informed Consent PDF">
                                        <i class="fas fa-print mr-1"></i> Cetak
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-xs btn-delete-consent" data-id="${c.id}" data-procedure="${c.procedure_name}" title="Hapus Dokumen">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }

                // =========================================================================
                // LEMBAR 6 & 7: DOKUMENTASI FOTO PASIEN (IPAD / KAMERA KLINIS)
                // =========================================================================
                const photosGrid = $('#rme-photos-grid');
                photosGrid.empty();

                if (!medicalPhotos || medicalPhotos.length === 0) {
                    photosGrid.html(`
                        <div class="col-12 text-center p-4 text-muted">
                            <i class="fas fa-camera-retro fa-2x mb-2 text-secondary"></i>
                            <h6>Belum Ada Dokumentasi Foto Klinis Pasien</h6>
                            <p class="text-xs mb-0">Gunakan iPad / Tablet perawat untuk memotret kondisi klinis luka / tindakan pasien (Before &amp; After).</p>
                        </div>
                    `);
                } else {
                    medicalPhotos.forEach(function(ph) {
                        const phDate = ph.created_at ? new Date(ph.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
                        const catBadges = {
                            'before': '<span class="badge badge-danger">🔴 Before / Awal</span>',
                            'process': '<span class="badge badge-warning text-dark">🟡 Process / Intra-Op</span>',
                            'after': '<span class="badge badge-success">🟢 After / Hasil Akhir</span>',
                            'other': '<span class="badge badge-secondary">⚪ Dokumen Klinis</span>'
                        };
                        const catBadge = catBadges[ph.category] || '<span class="badge badge-info">' + ph.category + '</span>';
                        const imgUrl = ph.photo_path.startsWith('http') ? ph.photo_path : '<?= base_url() ?>/' + ph.photo_path;

                        photosGrid.append(`
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-white h-100 shadow-none border" style="border-radius: 6px; overflow: hidden;">
                                    <div style="height: 160px; background: #222; overflow: hidden; position: relative;" class="d-flex align-items-center justify-content-center">
                                        <img src="${imgUrl}" alt="${ph.title}" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="window.open('${imgUrl}', '_blank')">
                                        <div style="position: absolute; top: 6px; left: 6px;">
                                            ${catBadge}
                                        </div>
                                    </div>
                                    <div class="card-body p-2 text-xs">
                                        <strong class="text-dark d-block text-truncate mb-1" title="${ph.title}">${ph.title}</strong>
                                        <div class="text-muted mb-1" style="font-size: 10.5px;">
                                            <i class="fas fa-calendar-alt mr-1"></i> ${phDate}<br>
                                            <i class="fas fa-user mr-1"></i> ${ph.taken_by || 'Perawat'}
                                        </div>
                                        ${ph.description ? `<p class="text-muted mb-2 text-truncate" style="font-size: 10.5px;" title="${ph.description}">${ph.description}</p>` : ''}
                                        <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-auto">
                                            <a href="${imgUrl}" target="_blank" class="btn btn-outline-teal btn-xs py-0"><i class="fas fa-search-plus mr-1"></i> Zoom</a>
                                            <button type="button" class="btn btn-outline-danger btn-xs py-0 btn-delete-photo" data-id="${ph.id}" data-title="${ph.title}"><i class="fas fa-trash-alt mr-1"></i> Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                // =========================================================================
                // TREN TTV TRACKER
                // =========================================================================
                const ttvTbody = $('#rme-ttv-table-body');
                ttvTbody.empty();

                if (!visits || visits.length === 0) {
                    ttvTbody.html('<tr><td colspan="10" class="text-center text-muted p-3">Belum ada data tanda vital.</td></tr>');
                } else {
                    visits.forEach(function(v) {
                        const vDate = v.visit_date ? new Date(v.visit_date).toLocaleDateString('id-ID') : '-';
                        ttvTbody.append(`
                            <tr>
                                <td class="font-weight-bold text-nowrap">${vDate}</td>
                                <td><span class="badge badge-light border">${v.no_visit}</span><br><small class="text-muted">${v.polyclinic_name || v.tindakan_name || '-'}</small></td>
                                <td class="text-center font-weight-bold font-monospace">${v.blood_pressure || '-'} mmHg</td>
                                <td class="text-center font-monospace">${v.pulse ? v.pulse + ' x/m' : '-'}</td>
                                <td class="text-center font-monospace">${v.temperature ? v.temperature + ' °C' : '-'}</td>
                                <td class="text-center font-monospace">${v.respiration ? v.respiration + ' x/m' : '-'}</td>
                                <td class="text-center font-monospace">${v.weight ? v.weight + ' kg' : '-'}</td>
                                <td class="text-center font-monospace">${v.height ? v.height + ' cm' : '-'}</td>
                                <td class="text-center">${v.bmi ? `<span class="badge badge-teal">${v.bmi}</span><br><small class="text-muted">${v.bmi_status}</small>` : '-'}</td>
                                <td><small class="text-muted">${v.complaints || '-'}</small></td>
                            </tr>
                        `);
                    });
                }

                // =========================================================================
                // RIWAYAT RESEP OBAT KUMULATIF
                // =========================================================================
                const medsTbody = $('#rme-meds-table-body');
                medsTbody.empty();

                if (!cumulativeMeds || cumulativeMeds.length === 0) {
                    medsTbody.html('<tr><td colspan="6" class="text-center text-muted p-3">Belum ada riwayat resep obat farmasi untuk pasien ini.</td></tr>');
                } else {
                    cumulativeMeds.forEach(function(m, idx) {
                        const mDate = m.last_date ? new Date(m.last_date).toLocaleDateString('id-ID') : '-';
                        medsTbody.append(`
                            <tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><strong class="text-dark">${m.name}</strong></td>
                                <td class="text-center"><span class="badge badge-teal">${m.total_qty} ${m.unit}</span></td>
                                <td class="text-center font-weight-bold">${m.frequency} kali</td>
                                <td><span class="badge badge-light border text-dark font-weight-bold">${m.last_dosage || '-'}</span></td>
                                <td class="text-nowrap">${mDate}</td>
                            </tr>
                        `);
                    });
                }

                // =========================================================================
                // SURAT MEDIS & HASIL LAB
                // =========================================================================
                const lettersList = $('#rme-letters-list');
                lettersList.empty();

                if (!letters || letters.length === 0) {
                    lettersList.html('<div class="p-3 text-center text-muted text-xs">Belum ada surat medis yang pernah diterbitkan.</div>');
                } else {
                    letters.forEach(function(letItem) {
                        const letDate = letItem.letter_date ? new Date(letItem.letter_date).toLocaleDateString('id-ID') : '-';
                        let badgeType = letItem.letter_type === 'sakit' ? '<span class="badge badge-danger">Surat Sakit (SKS)</span>' : (letItem.letter_type === 'sehat' ? '<span class="badge badge-success">Surat Sehat (SKD)</span>' : '<span class="badge badge-primary">Surat Rujukan (SRP)</span>');
                        lettersList.append(`
                            <div class="p-2 mb-2 bg-light rounded border text-xs d-flex justify-content-between align-items-center">
                                <div>
                                    ${badgeType} <strong class="text-dark ml-1">${letItem.letter_no}</strong><br>
                                    <span class="text-muted"><i class="fas fa-calendar mr-1"></i>${letDate} | Dokter: ${letItem.doctor_name || '-'}</span>
                                </div>
                                <a href="<?= base_url('klinik/cetak-surat') ?>/${letItem.id}" target="_blank" class="btn btn-outline-teal btn-xs font-weight-bold" title="Cetak Surat">
                                    <i class="fas fa-print mr-1"></i> Cetak
                                </a>
                            </div>
                        `);
                    });
                }

                const labList = $('#rme-lab-list');
                labList.empty();

                if (!labOrders || labOrders.length === 0) {
                    labList.html('<div class="p-3 text-center text-muted text-xs">Belum ada order/hasil pemeriksaan lab untuk pasien ini.</div>');
                } else {
                    labOrders.forEach(function(labItem) {
                        const labDate = labItem.test_date ? new Date(labItem.test_date).toLocaleDateString('id-ID') : (labItem.created_at ? new Date(labItem.created_at).toLocaleDateString('id-ID') : '-');
                        labList.append(`
                            <div class="p-2 mb-2 bg-light rounded border text-xs d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-teal">${labItem.test_type || 'Pemeriksaan Lab'}</span> <strong class="text-dark ml-1">${labItem.lab_no || ''}</strong><br>
                                    <span class="text-muted"><i class="fas fa-calendar mr-1"></i>${labDate} | Hasil: <strong>${labItem.result_value || 'Proses'} ${labItem.unit || ''}</strong> ${labItem.normal_range ? '(Rujukan: ' + labItem.normal_range + ')' : ''}</span>
                                </div>
                                <a href="<?= base_url('klinik/cetak-lab') ?>/${labItem.id}" target="_blank" class="btn btn-outline-purple btn-xs font-weight-bold" title="Cetak Hasil Lab">
                                    <i class="fas fa-print mr-1"></i> Cetak
                                </a>
                            </div>
                        `);
                    });
                }

                $('#rme-loading').hide();
                $('#rme-content').show();
            } else {
                alert(res.message || 'Gagal memuat rekam medis pasien.');
                $('#modalMedicalHistory').modal('hide');
            }
        }).fail(function(xhr, status, err) {
            console.error('RME Load Error:', xhr.responseText || err);
            alert('Terjadi kesalahan saat memuat data rekam medis. Periksa koneksi atau hubungi administrator.');
            $('#modalMedicalHistory').modal('hide');
        });
    }

    // Filter Pencarian Antrean di Sidebar Kanan Pendaftaran
    $(document).ready(function() {
        $('#pendaftaran-queue-search').on('keyup', function() {
            var val = $(this).val().toLowerCase().trim();
            $('.sidebar-queue-item').each(function() {
                var txt = $(this).data('search') || $(this).text().toLowerCase();
                $(this).toggle(txt.indexOf(val) > -1);
            });
        });

        // Event delegasi tombol panggil suara di sidebar pendaftaran
        $(document).on('click', '.sidebar-queue-item .btn-voice-call', function(e) {
            e.preventDefault();
            const qNo = $(this).data('queue');
            const pName = $(this).data('name');
            const target = $(this).data('target') || 'Loket Pendaftaran 1';

            if (qNo) {
                if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                    window.VCM.triggerServerCall({
                        service_type: 'pendaftaran',
                        counter_name: target,
                        queue_number: qNo,
                        patient_name: pName,
                        call_action: 'call',
                        call_priority: 1
                    });
                } else if (typeof callPatientVoice === 'function') {
                    callPatientVoice(qNo, pName, target);
                }
            }
        });

        // Event delegasi batalkan / hapus antrean di sidebar pendaftaran
        $(document).on('click', '.btn-cancel-pendaftaran-queue', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const queueId = $btn.data('id');
            const patientName = $btn.data('name');
            const queueNo = $btn.data('queue');

            if (!confirm('Apakah Anda yakin ingin membatalkan antrean pasien ' + patientName + ' (' + queueNo + ')? Data antrean akan dikeluarkan dari sistem.')) {
                return;
            }

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
            const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
            const postData = {
                action: 'delete_queue',
                queue_id: queueId,
                is_ajax: '1'
            };
            postData[csrfName] = csrfHash;

            $.ajax({
                url: '<?= base_url('klinik/antrean') ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        const $item = $btn.closest('.sidebar-queue-item');
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            const remaining = $('#live-pendaftaran-antrean-body .sidebar-queue-item').length;
                            $('#sidebar-queue-count').text(remaining + ' Pasien');
                            $('#stat-active-queue').text(remaining);
                            if (remaining === 0) {
                                $('#live-pendaftaran-antrean-body').html(
                                    '<div class="text-center text-muted p-3" id="empty-queue-placeholder">' +
                                    '<i class="fas fa-ticket-alt fa-lg mb-1 text-secondary"></i>' +
                                    '<p class="mb-0 text-xs">Belum ada antrean hari ini.</p>' +
                                    '</div>'
                                );
                            }
                        });
                        if (typeof toastr !== 'undefined') {
                            toastr.success(resp.message || 'Antrean berhasil dibatalkan.');
                        }
                    } else {
                        alert(resp.message || 'Gagal membatalkan antrean.');
                        $btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> Batal');
                    }
                },
                error: function(xhr) {
                    const err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan jaringan atau server saat membatalkan antrean.';
                    alert(err);
                    $btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> Batal');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
