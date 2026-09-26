<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-id-card-clip text-teal mr-2"></i> Detail Identitas Pasien
                </h1>
                <p class="text-muted text-xs mb-0">Informasi profil demografi, kontak sosial, status klinis, dan riwayat terintegrasi.</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('klinik/pendaftaran') ?>">Pendaftaran Pasien</a></li>
                    <li class="breadcrumb-item active"><?= esc($patient->name) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- HERO PROFILE CARD -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px; overflow: hidden;">
            <div class="card-body p-4 bg-gradient-teal text-white">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="d-flex align-items-center justify-content-center bg-white text-teal rounded-circle font-weight-bold shadow-sm" style="width: 75px; height: 75px; font-size: 28px; border: 3px solid rgba(255,255,255,0.4);">
                            <?= strtoupper(substr(esc($patient->name), 0, 1)) ?>
                        </div>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 8px;">
                            <h3 class="font-weight-bold mb-0 text-white"><?= esc($patient->name) ?></h3>
                            <span class="badge badge-light px-2.5 py-1 text-teal font-monospace font-weight-bold" style="font-size: 13px;">
                                <i class="fas fa-notes-medical mr-1"></i> <?= esc($patient->no_rm) ?>
                            </span>
                            <?php 
                                $tier = strtolower($patient->membership_tier ?? 'regular');
                                if ($tier === 'gold'): ?>
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold"><i class="fas fa-crown mr-1"></i> GOLD MEMBER</span>
                                <?php elseif ($tier === 'vip' || $tier === 'platinum'): ?>
                                    <span class="badge badge-dark px-2 py-1 font-weight-bold"><i class="fas fa-gem mr-1"></i> VIP MEMBER</span>
                                <?php else: ?>
                                    <span class="badge badge-light border px-2 py-1 text-muted font-weight-bold">REGULER</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-white-50 d-flex align-items-center flex-wrap" style="gap: 15px;">
                            <span><i class="fas fa-id-card mr-1"></i> NIK: <strong><?= esc($patient->nik ?: '-') ?></strong></span>
                            <span><i class="fas <?= $patient->gender === 'L' ? 'fa-mars' : 'fa-venus' ?> mr-1"></i> <?= $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                            <span><i class="fas fa-birthday-cake mr-1"></i> <?= $patient->date_of_birth ? date('d F Y', strtotime($patient->date_of_birth)) : '-' ?> (<?= $age ?>)</span>
                            <span><i class="fas fa-phone mr-1"></i> <?= esc($patient->phone ?: '-') ?></span>
                        </div>
                    </div>
                    <div class="col-md-auto mt-3 mt-md-0 d-flex flex-wrap" style="gap: 6px;">
                        <a href="<?= base_url('klinik/cetak-ringkasan-rme/' . $patient->id) ?>" target="_blank" class="btn btn-light btn-sm font-weight-bold text-teal shadow-sm">
                            <i class="fas fa-print mr-1"></i> Cetak Berkas RME (Lembar 1-7)
                        </a>
                        <a href="<?= base_url('klinik/kartu-pasien/' . $patient->id) ?>" target="_blank" class="btn btn-outline-light btn-sm font-weight-bold">
                            <i class="fas fa-id-card mr-1"></i> Cetak Kartu Pasien
                        </a>
                        <a href="<?= base_url('klinik/pendaftaran') ?>" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Direktori
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- KOLOM KIRI: DATA LENGKAP SOSIAL & DEMOGRAFI -->
            <div class="col-lg-5 col-md-6">
                
                <!-- KARTU IDENTITAS LENGKAP -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-user-check text-teal mr-1"></i> Profil Biodata Sosial Pasien
                        </h6>
                        <span class="badge badge-light border text-muted">Lembar 1</span>
                    </div>
                    <div class="card-body p-3">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="text-muted" style="width: 140px;">Nomor Rekam Medis</td>
                                    <td class="font-weight-bold font-monospace text-teal">: <?= esc($patient->no_rm) ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Nomor Induk (NIK)</td>
                                    <td class="font-weight-bold font-monospace">: <?= esc($patient->nik ?: '-') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Nama Lengkap</td>
                                    <td class="font-weight-bold">: <?= esc($patient->name) ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Jenis Kelamin</td>
                                    <td>: <?= $patient->gender === 'L' ? '<span class="badge badge-primary px-2"><i class="fas fa-mars mr-1"></i>Laki-laki</span>' : '<span class="badge badge-danger px-2"><i class="fas fa-venus mr-1"></i>Perempuan</span>' ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Tempat, Tgl Lahir</td>
                                    <td>: <?= esc($patient->place_of_birth ?: '-') ?>, <?= $patient->date_of_birth ? date('d F Y', strtotime($patient->date_of_birth)) : '-' ?> (<?= $age ?>)</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Pekerjaan Pasien</td>
                                    <td class="font-weight-bold">: <?= esc($patient->occupation ?: 'Belum diisi') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Agama</td>
                                    <td>: <?= esc($patient->religion ?: 'Islam') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Golongan Darah</td>
                                    <td class="font-weight-bold">: <?= esc($patient->blood_type ?: '-') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Status Pernikahan</td>
                                    <td>: <?= esc($patient->marital_status ?: 'Menikah') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">Pendidikan Terakhir</td>
                                    <td>: <?= esc($patient->education ?: '-') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">No. Kartu BPJS</td>
                                    <td>: <?= esc($patient->bpjs_number ?: 'Umum / Non-BPJS') ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="text-muted">No. Telepon / WA</td>
                                    <td>: 
                                        <?php if (!empty($patient->phone)): 
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $patient->phone);
                                            if (str_starts_with($cleanPhone, '0')) $cleanPhone = '62' . substr($cleanPhone, 1);
                                        ?>
                                            <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" class="text-success font-weight-bold">
                                                <i class="fab fa-whatsapp mr-1"></i> <?= esc($patient->phone) ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat Domisili</td>
                                    <td>: <?= esc($patient->address ?: '-') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PERINGATAN KLINIS ALERGI & RPD -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-shield-virus text-danger mr-1"></i> Peringatan Klinis &amp; Riwayat Penyakit
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="alert alert-danger border-0 p-3 mb-3">
                            <strong class="d-block text-xs uppercase mb-1"><i class="fas fa-allergies mr-1"></i> RIWAYAT ALERGI PASIEN:</strong>
                            <div class="font-weight-bold" style="font-size: 13px;">
                                <?= esc($patient->allergies ?: 'TIDAK ADA CATATAN ALERGI') ?>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border text-xs mb-2">
                            <strong class="d-block text-muted mb-1"><i class="fas fa-file-medical-alt mr-1"></i> Riwayat Penyakit Terdahulu (RPD):</strong>
                            <div class="text-dark font-weight-bold" style="font-size: 12.5px;">
                                <?= esc($patient->medical_history ?: 'Tidak ada riwayat penyakit berat/kronis terdahulu') ?>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border text-xs">
                            <strong class="d-block text-muted mb-1"><i class="fas fa-phone-alt mr-1"></i> Kontak Darurat Keluarga:</strong>
                            <div class="text-dark font-weight-bold">
                                <?= esc($patient->emergency_contact_name ?: '-') ?> 
                                <?= $patient->emergency_contact_phone ? ' (' . esc($patient->emergency_contact_phone) . ')' : '' ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: STATISTIK & RIWAYAT REKAM MEDIS -->
            <div class="col-lg-7 col-md-6">
                
                <!-- SUMMARY METRICS -->
                <div class="row mb-3">
                    <div class="col-sm-4 mb-2">
                        <div class="card border-0 bg-light p-3 rounded shadow-sm text-center">
                            <small class="text-muted font-weight-bold d-block text-xs uppercase">Total Kunjungan</small>
                            <h3 class="font-weight-bold text-teal mb-0"><?= $totalVisits ?></h3>
                            <small class="text-muted" style="font-size: 10.5px;">Kali Pemeriksaan</small>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <div class="card border-0 bg-light p-3 rounded shadow-sm text-center">
                            <small class="text-muted font-weight-bold d-block text-xs uppercase">Informed Consent</small>
                            <h3 class="font-weight-bold text-dark mb-0"><?= count($informedConsents) ?></h3>
                            <small class="text-muted" style="font-size: 10.5px;">Dokumen Tindakan</small>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-2">
                        <div class="card border-0 bg-light p-3 rounded shadow-sm text-center">
                            <small class="text-muted font-weight-bold d-block text-xs uppercase">Foto Dokumentasi</small>
                            <h3 class="font-weight-bold text-info mb-0"><?= count($medicalPhotos) ?></h3>
                            <small class="text-muted" style="font-size: 10.5px;">Kamera iPad / Klinis</small>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT KUNJUNGAN PASIEN (CPPT) -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-history text-teal mr-1"></i> Riwayat Kunjungan Medis (CPPT)
                        </h6>
                        <span class="badge badge-teal px-2"><?= count($visits) ?> Riwayat</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($visits)): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2 text-secondary"></i>
                                <p class="mb-0 text-xs">Belum ada riwayat kunjungan pemeriksaan untuk pasien ini.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 text-xs">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>No. Visit</th>
                                            <th>Layanan / Poli</th>
                                            <th>Dokter DPJP</th>
                                            <th>Diagnosis &amp; Terapi</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($visits as $v): ?>
                                            <tr>
                                                <td class="font-weight-bold"><?= $v->visit_date ? date('d/m/Y', strtotime($v->visit_date)) : '-' ?></td>
                                                <td><span class="font-monospace text-muted"><?= esc($v->no_visit) ?></span></td>
                                                <td><?= esc($v->polyclinic_name ?: $v->tindakan_name ?: 'Poli Umum') ?></td>
                                                <td><?= esc($v->doctor_name ?: 'Dokter Jaga') ?></td>
                                                <td>
                                                    <?php if (!empty($v->icd10_code)): ?>
                                                        <span class="badge badge-success mb-1">ICD: <?= esc($v->icd10_code) ?></span><br>
                                                    <?php endif; ?>
                                                    <span class="text-secondary"><?= esc($v->assessment ?: $v->subjective ?: '-') ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $v->status === 'completed' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst(esc($v->status)) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- DOKUMEN TINDAKAN & FOTO PASIEN -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 8px;">
                            <div class="card-header bg-white border-bottom py-2.5 d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-xs text-dark"><i class="fas fa-file-signature text-teal mr-1"></i> Informed Consent</span>
                                <span class="badge badge-light border"><?= count($informedConsents) ?></span>
                            </div>
                            <div class="card-body p-2 text-xs">
                                <?php if (empty($informedConsents)): ?>
                                    <div class="text-center py-3 text-muted font-italic">Belum ada dokumen persetujuan.</div>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach (array_slice($informedConsents, 0, 3) as $c): ?>
                                            <li class="list-group-item px-2 py-1.5 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="d-block text-dark"><?= esc($c->procedure_name) ?></strong>
                                                    <small class="text-muted"><?= date('d/m/Y', strtotime($c->consent_date)) ?> &bull; <?= esc($c->doctor_name ?: 'Dokter') ?></small>
                                                </div>
                                                <a href="<?= base_url('klinik/cetak-informed-consent/' . $c->id) ?>" target="_blank" class="btn btn-outline-teal btn-xs">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 8px;">
                            <div class="card-header bg-white border-bottom py-2.5 d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-xs text-dark"><i class="fas fa-camera text-teal mr-1"></i> Foto Dokumentasi iPad</span>
                                <span class="badge badge-light border"><?= count($medicalPhotos) ?></span>
                            </div>
                            <div class="card-body p-2 text-xs">
                                <?php if (empty($medicalPhotos)): ?>
                                    <div class="text-center py-3 text-muted font-italic">Belum ada foto dokumentasi.</div>
                                <?php else: ?>
                                    <div class="d-flex flex-wrap" style="gap: 6px;">
                                        <?php foreach (array_slice($medicalPhotos, 0, 4) as $ph): ?>
                                            <div style="width: 48%; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; text-align: center;">
                                                <img src="<?= base_url($ph->photo_path) ?>" alt="<?= esc($ph->title) ?>" style="width: 100%; height: 55px; object-fit: cover; border-radius: 2px;">
                                                <div style="font-size: 9px; font-weight: bold; margin-top: 2px;" class="text-truncate"><?= esc($ph->title) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
