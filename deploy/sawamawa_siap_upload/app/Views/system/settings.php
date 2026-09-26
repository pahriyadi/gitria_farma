<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-7">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-sliders text-teal mr-2"></i> Satu Data Referensi & Pengaturan Sistem
                </h1>
                <small class="text-muted">Pusat kendali parameter tunggal, kop surat resmi, stempel digital, regulasi farmasi/resto, dan integrasi API.</small>
            </div>
            <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
                <span class="badge badge-pill badge-light border text-teal px-3 py-2 font-weight-bold shadow-xs mr-1">
                    <i class="fas fa-shield-halved mr-1"></i> Sawamawa Medical Center
                </span>
                <span class="badge badge-pill badge-info px-2 py-2 font-weight-normal shadow-xs">
                    <i class="fas fa-clock mr-1"></i> <span id="clock-display"><?= date('H:i') ?> WITA</span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- Flash Message Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x mr-3 text-success"></i>
                    <div>
                        <strong class="font-weight-bold">Berhasil Disimpan!</strong>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                    <div>
                        <strong class="font-weight-bold">Gagal Menyimpan!</strong>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Form Master Settings -->
        <form id="form-system-settings" action="<?= base_url('system/settings') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Hidden inputs for file removal flags -->
            <input type="hidden" name="remove_logo" id="remove_logo" value="0">
            <input type="hidden" name="remove_favicon" id="remove_favicon" value="0">
            <input type="hidden" name="remove_stamp" id="remove_stamp" value="0">

            <div class="card card-outline card-teal shadow-sm border-0 mb-4">
                
                <!-- Sticky Top Action Bar inside Card Header -->
                <div class="card-header bg-white p-2 p-md-3 border-bottom d-flex flex-wrap justify-content-between align-items-center sticky-top shadow-xs" style="z-index: 1020; top: 0;">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <span class="badge badge-teal px-2 py-1 mr-2"><i class="fas fa-database mr-1"></i> Master Referensi</span>
                        <span id="dirty-badge" class="badge badge-warning text-dark d-none px-2 py-1 mr-2"><i class="fas fa-pen mr-1"></i> Ada perubahan belum disimpan</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="reset" class="btn btn-outline-secondary btn-sm font-weight-bold px-3 mr-2" id="btn-reset-form">
                            <i class="fas fa-undo mr-1"></i> Reset Form
                        </button>
                        <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4 shadow-sm" id="btn-save-top">
                            <i class="fas fa-save mr-1"></i> Simpan Seluruh Konfigurasi
                        </button>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="card-header p-0 border-bottom-0 bg-light">
                    <ul class="nav nav-tabs nav-justified" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold py-3 text-truncate" id="klinik-tab" data-toggle="pill" href="#klinik" role="tab">
                                <i class="fas fa-hospital text-teal mr-1"></i> 1. Identitas Klinik
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="branding-tab" data-toggle="pill" href="#branding" role="tab">
                                <i class="fas fa-file-invoice text-info mr-1"></i> 2. Branding & Kop Surat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="api-tab" data-toggle="pill" href="#api" role="tab">
                                <i class="fab fa-whatsapp text-success mr-1"></i> 3. WhatsApp Gateway
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="unit-bisnis-tab" data-toggle="pill" href="#unit-bisnis" role="tab">
                                <i class="fas fa-boxes-stacked text-warning mr-1"></i> 4. Farmasi & Resto
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="online-reg-tab" data-toggle="pill" href="#online-reg" role="tab">
                                <i class="fas fa-calendar-check text-primary mr-1"></i> 5. Pendaftaran Online
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="display-voice-tab" data-toggle="pill" href="#display-voice" role="tab">
                                <i class="fas fa-bullhorn text-teal mr-1"></i> 6. Display TV & Voice
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="diagnostics-tab" data-toggle="pill" href="#diagnostics" role="tab">
                                <i class="fas fa-server text-secondary mr-1"></i> 7. Diagnostik Sistem
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 text-truncate" id="satusehat-tab" data-toggle="pill" href="#satusehat" role="tab">
                                <i class="fas fa-heart-pulse text-danger mr-1"></i> 8. SATUSEHAT Kemenkes
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body bg-white p-3 p-md-4">
                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- ========================================================================= -->
                        <!-- TAB 1: IDENTITAS RESMI KLINIK -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade show active" id="klinik" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-hospital text-teal mr-2"></i>Profil & Identitas Resmi Fasilitas</h5>
                                    <small class="text-muted">Data ini menjadi referensi tunggal (*single source of truth*) pada kop surat, formulir rujukan, e-resep, kwitansi kasir, dan laporan akuntansi.</small>
                                </div>
                                <span class="badge badge-pill badge-light border text-teal px-3 py-1 font-weight-bold">
                                    <i class="fas fa-check-double mr-1"></i> Master Referensi
                                </span>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nama Resmi Fasilitas Pelayanan: <span class="text-danger">*</span></label>
                                    <input type="text" name="settings[clinic_name]" id="input-clinic-name" class="form-control form-control-sm font-weight-bold text-teal" value="<?= esc(clinic_setting('clinic_name')) ?>" required placeholder="Sawamawa Medical Center">
                                    <small class="text-muted text-xs">Otomatis muncul di header cetak, kartu pasien, dan judul portal web.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Slogan / Tagline Resmi:</label>
                                    <input type="text" name="settings[clinic_tagline]" id="input-clinic-tagline" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_tagline')) ?>" placeholder="Pusat Layanan Medis Terpadu & Terpercaya">
                                    <small class="text-muted text-xs">Ditampilkan di bawah nama klinik pada kop surat dan halaman publik.</small>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold text-dark">Alamat Lengkap Kantor / Gedung Fasilitas: <span class="text-danger">*</span></label>
                                <textarea name="settings[clinic_address]" id="input-clinic-address" class="form-control form-control-sm" rows="2" required placeholder="Jl. Kebangsaan No. 12, Sumbawa Besar, NTB"><?= esc(clinic_setting('clinic_address')) ?></textarea>
                                <small class="text-muted text-xs">Alamat fisik lengkap yang tercantum pada kop surat dinas dan footer website.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nomor Telepon Hotline / WhatsApp: <span class="text-danger">*</span></label>
                                    <input type="text" name="settings[clinic_phone]" id="input-clinic-phone" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_phone')) ?>" required placeholder="(0371) 23456 / 081122334455">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Email Resmi Layanan Pasien:</label>
                                    <input type="email" name="settings[clinic_email]" id="input-clinic-email" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_email')) ?>" placeholder="info@sawamawamedicalcenter.id">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Website Resmi:</label>
                                    <input type="text" name="settings[clinic_website]" id="input-clinic-website" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_website')) ?>" placeholder="www.sawamawamedicalcenter.id">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nomor Izin Operasional Klinik (Dinkes/Kemenkes):</label>
                                    <input type="text" name="settings[clinic_license_number]" id="input-clinic-license" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_license_number')) ?>" placeholder="445/012/DINKES/2024">
                                    <small class="text-muted text-xs">Nomor registrasi perizinan resmi yang tertera pada lembar resep.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Kode Fasilitas Kesehatan (Kode Faskes / SatuSehat):</label>
                                    <input type="text" name="settings[clinic_faskes_code]" id="input-clinic-faskes" class="form-control form-control-sm font-weight-bold text-teal" value="<?= esc(clinic_setting('clinic_faskes_code', '52040101')) ?>" placeholder="52040101">
                                    <small class="text-muted text-xs">Digunakan untuk bridging BPJS P-Care dan integrasi SatuSehat Kemenkes.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Status Integrasi BPJS Kesehatan:</label>
                                    <select name="settings[clinic_bpjs_active]" class="form-control form-control-sm font-weight-bold">
                                        <option value="true" <?= clinic_setting('clinic_bpjs_active') === 'true' ? 'selected' : '' ?>>🟢 Aktif (Terintegrasi BPJS Kesehatan)</option>
                                        <option value="false" <?= clinic_setting('clinic_bpjs_active') === 'false' ? 'selected' : '' ?>>⚪ Non-Aktif (Khusus Pasien Umum / Swasta)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nama Direktur / Penanggung Jawab Medis: <span class="text-danger">*</span></label>
                                    <input type="text" name="settings[clinic_director]" id="input-clinic-director" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_director')) ?>" required placeholder="dr. Andi Wijaya, Sp.PD">
                                    <small class="text-muted text-xs">Nama penandatangan dokumen legal, kontrak kerja, dan laporan tahunan.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nomor SIP Dokter Penanggung Jawab:</label>
                                    <input type="text" name="settings[clinic_head_sip]" id="input-clinic-sip" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_head_sip', '503/449/SIP-D/2022')) ?>" placeholder="503/449/SIP-D/2022">
                                    <small class="text-muted text-xs">Nomor Surat Izin Praktik (SIP) dokter penanggung jawab medis faskes.</small>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 2: BRANDING, DOKUMEN & KOP SURAT SIMULATOR -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="branding" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-file-invoice text-info mr-2"></i>Branding, Legalitas & Kop Surat Resmi</h5>
                                    <small class="text-muted">Kelola logo klinik, favicon browser, stempel digital, serta amati visualisasi kop surat secara *real-time*.</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Upload Logo -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border p-3 bg-light shadow-none">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-weight-bold text-teal mb-0"><i class="fas fa-image mr-1"></i> Logo Utama</h6>
                                            <?php if (clinic_logo()): ?>
                                                <button type="button" class="btn btn-outline-danger btn-xs" id="btn-remove-logo" title="Hapus Logo">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="text-center p-3 bg-white border rounded mb-3 position-relative" style="min-height: 120px; display:flex; align-items:center; justify-content:center;">
                                            <img id="preview-logo-img" src="<?= clinic_logo() ?: '' ?>" alt="Logo Utama" style="max-height: 80px; max-width: 100%; object-fit: contain; <?= clinic_logo() ? '' : 'display:none;' ?>">
                                            <div id="placeholder-logo" class="text-center text-muted" style="<?= clinic_logo() ? 'display:none;' : '' ?>">
                                                <i class="fas fa-hospital-user fa-3x text-teal mb-1"></i>
                                                <div class="text-xs font-weight-bold"><?= esc(clinic_setting('clinic_name')) ?></div>
                                                <small class="text-muted" style="font-size: 10px;">(Logo Default Sistem)</small>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="text-xs font-weight-bold">Unggah Logo Baru (PNG/JPG/SVG):</label>
                                            <input type="file" name="clinic_logo_file" id="clinic_logo_file" class="form-control-file border p-1 bg-white" accept="image/*">
                                            <small class="text-muted text-xs d-block mt-1">Transparan PNG 400x120 px direkomendasikan.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Favicon -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border p-3 bg-light shadow-none">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-weight-bold text-info mb-0"><i class="fas fa-globe mr-1"></i> Favicon Tab Browser</h6>
                                            <?php if (clinic_favicon()): ?>
                                                <button type="button" class="btn btn-outline-danger btn-xs" id="btn-remove-favicon" title="Hapus Favicon">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <div class="text-center p-3 bg-white border rounded mb-3 position-relative" style="min-height: 120px; display:flex; align-items:center; justify-content:center;">
                                            <img id="preview-favicon-img" src="<?= clinic_favicon() ?: '' ?>" alt="Favicon" style="max-height: 48px; max-width: 48px; object-fit: contain; <?= clinic_favicon() ? '' : 'display:none;' ?>">
                                            <div id="placeholder-favicon" class="text-center text-muted" style="<?= clinic_favicon() ? 'display:none;' : '' ?>">
                                                <i class="fas fa-square-plus fa-3x text-info mb-1"></i>
                                                <div class="text-xs font-weight-bold">Favicon Default</div>
                                                <small class="text-muted" style="font-size: 10px;">(Tampil pada tab browser)</small>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="text-xs font-weight-bold">Unggah Favicon Baru (ICO/PNG):</label>
                                            <input type="file" name="clinic_favicon_file" id="clinic_favicon_file" class="form-control-file border p-1 bg-white" accept="image/*">
                                            <small class="text-muted text-xs d-block mt-1">Resolusi persegi 32x32 atau 64x64 px.</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Stempel Digital -->
                                <div class="col-lg-4 col-md-12 mb-3">
                                    <div class="card h-100 border p-3 bg-light shadow-none">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-weight-bold text-success mb-0"><i class="fas fa-stamp mr-1"></i> Stempel Digital Resmi</h6>
                                            <?php if (clinic_stamp()): ?>
                                                <button type="button" class="btn btn-outline-danger btn-xs" id="btn-remove-stamp" title="Hapus Stempel">
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <div class="text-center p-3 bg-white border rounded mb-3 position-relative" style="min-height: 120px; display:flex; align-items:center; justify-content:center;">
                                            <img id="preview-stamp-img" src="<?= clinic_stamp() ?: '' ?>" alt="Stempel Klinik" style="max-height: 80px; max-width: 100%; object-fit: contain; <?= clinic_stamp() ? '' : 'display:none;' ?>">
                                            <div id="placeholder-stamp" class="text-center text-muted" style="<?= clinic_stamp() ? 'display:none;' : '' ?>">
                                                <i class="fas fa-certificate fa-3x text-success mb-1"></i>
                                                <div class="text-xs font-weight-bold">Belum Ada Stempel</div>
                                                <small class="text-muted" style="font-size: 10px;">(Muncul di atas TTD surat & e-resep)</small>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="text-xs font-weight-bold">Unggah Stempel Baru (PNG Transparan):</label>
                                            <input type="file" name="clinic_stamp_file" id="clinic_stamp_file" class="form-control-file border p-1 bg-white" accept="image/*">
                                            <small class="text-muted text-xs d-block mt-1">Gunakan format PNG berlatar transparan.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Teks Watermark Dokumen Kertas:</label>
                                    <input type="text" name="settings[clinic_watermark]" id="input-clinic-watermark" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_watermark')) ?>" placeholder="SAWAMAWA">
                                    <small class="text-muted text-xs">Muncul samar transparan di latar belakang dokumen e-resep, surat sakit, dan PO/PR.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Catatan Kaki (Footer Note) Dokumen Resmi:</label>
                                    <input type="text" name="settings[clinic_footer_note]" id="input-clinic-footer" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_footer_note')) ?>" placeholder="Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.">
                                    <small class="text-muted text-xs">Tercetak di bagian paling bawah pada seluruh lembaran formal klinik.</small>
                                </div>
                            </div>

                            <!-- ========================================================================= -->
                            <!-- LIVE KOP SURAT SIMULATOR WIDGET -->
                            <!-- ========================================================================= -->
                            <div class="card border rounded shadow-xs mt-3">
                                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                                    <div class="font-weight-bold text-dark text-xs">
                                        <i class="fas fa-eye text-teal mr-1"></i> SIMULATOR DOKUMEN CETAK & KOP SURAT RESMI (LIVE PREVIEW)
                                    </div>
                                    <span class="badge badge-pill badge-light border text-muted text-xs font-weight-normal">
                                        <i class="fas fa-wand-magic-sparkles text-teal mr-1"></i> Berubah Mengikuti Input Anda
                                    </span>
                                </div>
                                <div class="card-body p-4 bg-light">
                                    <!-- Visualisasi Kertas Dokumen A4 -->
                                    <div class="p-4 bg-white border rounded shadow-sm mx-auto position-relative" style="max-width: 780px; min-height: 480px; font-family: 'Times New Roman', Times, serif; color: #111;">
                                        
                                        <!-- Watermark Simulation -->
                                        <div id="sim-watermark" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 58px; font-weight: bold; color: rgba(0,0,0,0.04); pointer-events: none; text-transform: uppercase; white-space: nowrap;">
                                            <?= esc(clinic_setting('clinic_watermark', 'SAWAMAWA')) ?>
                                        </div>

                                        <!-- Header Kop Surat -->
                                        <div class="d-flex align-items-center pb-2" style="border-bottom: 3px double #111;">
                                            <div class="mr-3" style="width: 80px; text-align: center;">
                                                <img id="sim-logo" src="<?= clinic_logo() ?: '' ?>" alt="Logo" style="max-height: 65px; max-width: 80px; object-fit: contain; <?= clinic_logo() ? '' : 'display:none;' ?>">
                                                <div id="sim-logo-fallback" class="p-2 border rounded text-center text-teal" style="<?= clinic_logo() ? 'display:none;' : '' ?>">
                                                    <i class="fas fa-hospital fa-2x"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 text-center pr-4">
                                                <h4 id="sim-name" class="font-weight-bold mb-0 text-uppercase" style="letter-spacing: 1px; font-size: 19px; line-height: 1.2;">
                                                    <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?>
                                                </h4>
                                                <div id="sim-tagline" class="font-italic text-secondary" style="font-size: 12px; line-height: 1.2;">
                                                    <?= esc(clinic_setting('clinic_tagline', 'Pusat Layanan Medis Terpadu & Terpercaya')) ?>
                                                </div>
                                                <div id="sim-license" class="font-weight-bold" style="font-size: 11px; margin-top: 2px;">
                                                    Izin Dinkes: <?= esc(clinic_setting('clinic_license_number', '445/012/DINKES/2024')) ?> &bull; Kode Faskes: <?= esc(clinic_setting('clinic_faskes_code', '52040101')) ?>
                                                </div>
                                                <div id="sim-address" style="font-size: 10.5px; line-height: 1.2; margin-top: 2px;">
                                                    <?= esc(clinic_setting('clinic_address', 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB')) ?>
                                                </div>
                                                <div id="sim-contacts" style="font-size: 10.5px;">
                                                    Telp: <?= esc(clinic_setting('clinic_phone', '(0371) 23456')) ?> | Email: <?= esc(clinic_setting('clinic_email', 'info@sawamawamedicalcenter.id')) ?> | Web: <?= esc(clinic_setting('clinic_website', 'www.sawamawamedicalcenter.id')) ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Isi Dokumen Simulasi -->
                                        <div class="pt-3" style="font-size: 12px; line-height: 1.5;">
                                            <div class="text-center font-weight-bold mb-3" style="text-decoration: underline; font-size: 14px;">
                                                SURAT KETERANGAN DOKTER / LEMBAR REKAM MEDIS
                                            </div>
                                            <p class="mb-2">Yang bertanda tangan di bawah ini dokter penanggung jawab medis pada fasilitas kesehatan <strong><span id="sim-doc-clinic"><?= esc(clinic_setting('clinic_name')) ?></span></strong>, menerangkan bahwa pasien telah diperiksa secara seksama sesuai standar pelayanan medis.</p>

                                            <!-- Signature & Stamp Preview Box -->
                                            <div class="row mt-4 pt-2">
                                                <div class="col-7"></div>
                                                <div class="col-5 text-center">
                                                    <div>Sumbawa Besar, <?= date('d F Y') ?></div>
                                                    <div class="font-weight-bold">Dokter Penanggung Jawab,</div>
                                                    
                                                    <!-- Signature Space with Stamp overlay -->
                                                    <div class="position-relative my-2" style="height: 65px; display: flex; align-items: center; justify-content: center;">
                                                        <div class="font-italic text-muted" style="font-size: 11px; z-index: 1;">(Tanda Tangan Digital)</div>
                                                        <!-- Stamp Simulation -->
                                                        <img id="sim-stamp" src="<?= clinic_stamp() ?: '' ?>" alt="Stempel" style="max-height: 60px; max-width: 100px; position: absolute; left: 10px; opacity: 0.8; z-index: 2; <?= clinic_stamp() ? '' : 'display:none;' ?>">
                                                    </div>

                                                    <div class="font-weight-bold"><u id="sim-director"><?= esc(clinic_setting('clinic_director', 'dr. Andi Wijaya, Sp.PD')) ?></u></div>
                                                    <div style="font-size: 10px;">SIP: <span id="sim-sip"><?= esc(clinic_setting('clinic_head_sip', '503/449/SIP-D/2022')) ?></span></div>
                                                </div>
                                            </div>

                                            <!-- Footer Note Simulation -->
                                            <div class="mt-4 pt-3 border-top text-center text-muted" style="font-size: 9.5px;" id="sim-footer">
                                                <?= esc(clinic_setting('clinic_footer_note', 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.')) ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 3: INTEGRASI WHATSAPP GATEWAY -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="api" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fab fa-whatsapp text-success mr-2"></i>Integrasi WhatsApp Gateway & Notifikasi Otomatis</h5>
                                    <small class="text-muted">Kirim otomatis notifikasi antrean, resep obat siap ambil, billing invoice kasir, dan pengingat kontrol ke nomor WhatsApp pasien.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Provider WhatsApp Gateway: <span class="text-danger">*</span></label>
                                    <select name="settings[wa_gateway_provider]" id="wa_gateway_provider" class="form-control form-control-sm font-weight-bold">
                                        <option value="fonnte" <?= clinic_setting('wa_gateway_provider') === 'fonnte' ? 'selected' : '' ?>>🟢 Fonnte Gateway (Rekomendasi Indonesia)</option>
                                        <option value="wablas" <?= clinic_setting('wa_gateway_provider') === 'wablas' ? 'selected' : '' ?>>🔵 Wablas Official Gateway</option>
                                        <option value="custom" <?= clinic_setting('wa_gateway_provider') === 'custom' ? 'selected' : '' ?>>⚪ Custom Webhook API Internal</option>
                                    </select>
                                    <small class="text-muted text-xs">Pilih gateway WhatsApp yang digunakan untuk pengiriman pesan otomatis.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nomor Pengirim Resmi (Sender Number):</label>
                                    <input type="text" name="settings[wa_sender_number]" id="wa_sender_number" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('wa_sender_number')) ?>" placeholder="081122334455">
                                    <small class="text-muted text-xs">Nomor WhatsApp klinik yang terhubung pada perangkat gateway.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">API Token WhatsApp Gateway: <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" name="settings[wa_api_token]" id="wa_api_token" class="form-control font-weight-bold" value="<?= esc(clinic_setting('wa_api_token')) ?>" placeholder="Masukkan token rahasia API">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="btn-toggle-token" title="Lihat/Sembunyikan Token">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted text-xs">Token rahasia yang didapatkan dari dashboard akun gateway Anda.</small>
                                </div>
                            </div>

                            <!-- Card Diagnostic & Live Ping Test -->
                            <div class="card border rounded bg-light p-3 mt-2 shadow-none">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="font-weight-bold text-dark text-xs"><i class="fas fa-plug-circle-bolt text-teal mr-1"></i> Uji Coba Koneksi Token WhatsApp Gateway (Live Ping Tester)</span>
                                        <small class="d-block text-muted text-xs">Periksa validitas API Token dan status perangkat WhatsApp yang terhubung tanpa harus mengirim pesan broadcast.</small>
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm font-weight-bold mt-2 mt-sm-0 shadow-xs" id="btn-ping-wa">
                                        <i class="fab fa-whatsapp mr-1"></i> Uji Koneksi Gateway (Test Ping)
                                    </button>
                                </div>

                                <div id="wa-ping-result" class="d-none mt-2">
                                    <!-- Result will be inserted here via JS -->
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 4: PARAMETER FARMASI & RESTO POS -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="unit-bisnis" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-boxes-stacked text-warning mr-2"></i>Parameter Operasional Farmasi & Restoran</h5>
                                    <small class="text-muted">Kelola batas stok kritis apotek, margin keuntungan obat standar, pajak penjualan, dan integrasi kasir resto rawat inap.</small>
                                </div>
                            </div>

                            <div class="card border rounded p-3 mb-4 shadow-none bg-light">
                                <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-pills mr-1"></i> Regulasi Farmasi & Apotek</h6>
                                <div class="row">
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Batas Notifikasi Stok Kritis:</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="settings[pharmacy_min_stock_alert]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_min_stock_alert', '10')) ?>" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">Unit / Box</span>
                                            </div>
                                        </div>
                                        <small class="text-muted text-xs">Pemicu warna merah dan alert stok menipis pada katalog obat.</small>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Margin Keuntungan Standar Obat:</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="settings[pharmacy_profit_margin_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_profit_margin_percent', '20')) ?>" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted text-xs">Persentase markup default dari harga beli HPP obat.</small>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Pajak Penjualan Obat (PPN):</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="settings[pharmacy_tax_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_tax_percent', '10')) ?>" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted text-xs">Diterapkan pada invoice kwitansi penjualan obat kasir.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="card border rounded p-3 shadow-none bg-light">
                                <h6 class="font-weight-bold text-warning mb-3"><i class="fas fa-utensils mr-1"></i> Parameter POS Restoran & Nutrisi Gizi</h6>
                                <div class="row">
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Pajak Restoran (PB1 / Pajak Daerah):</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="settings[resto_tax_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('resto_tax_percent', '10')) ?>" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted text-xs">Pajak resto yang otomatis dihitung pada nota makanan/minuman.</small>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Biaya Pelayanan (Service Charge):</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="settings[resto_service_charge_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('resto_service_charge_percent', '5')) ?>" min="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text font-weight-bold">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted text-xs">Biaya layanan pesanan makan di tempat (dine-in).</small>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Pembebanan Billing Pasien Rawat Inap:</label>
                                        <select name="settings[resto_auto_billing_inpatient]" class="form-control form-control-sm font-weight-bold">
                                            <option value="true" <?= clinic_setting('resto_auto_billing_inpatient') === 'true' ? 'selected' : '' ?>>🟢 Otomatis Gabung Ke Billing Kasir Inap</option>
                                            <option value="false" <?= clinic_setting('resto_auto_billing_inpatient') === 'false' ? 'selected' : '' ?>>⚪ Pisahkan Kasir Resto Mandiri</option>
                                        </select>
                                        <small class="text-muted text-xs">Integrasi pesanan makanan kamar langsung ke settlement pasien.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 5: PENDAFTARAN ONLINE & JAM OPERASIONAL -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="online-reg" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-calendar-check text-primary mr-2"></i>Pengaturan Jam Operasional & Status Pendaftaran Online</h5>
                                    <small class="text-muted">Kontrol akses pasien untuk mendaftar antrean mandiri secara daring melalui portal publik klinik.</small>
                                </div>
                                <div>
                                    <?php if (is_online_registration_open()): ?>
                                        <span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-door-open mr-1"></i> Pendaftaran Sedang DIBUKA</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 py-1 font-weight-bold"><i class="fas fa-door-closed mr-1"></i> Pendaftaran Sedang DITUTUP</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Status Pendaftaran Online Mandiri: <span class="text-danger">*</span></label>
                                    <select name="settings[online_registration_active]" class="form-control form-control-sm font-weight-bold">
                                        <option value="true" <?= clinic_setting('online_registration_active', 'true') === 'true' ? 'selected' : '' ?>>🟢 Aktif (Buka Akses Pendaftaran)</option>
                                        <option value="false" <?= clinic_setting('online_registration_active', 'true') === 'false' ? 'selected' : '' ?>>🔴 Non-Aktif (Tutup Akses Mandiri)</option>
                                    </select>
                                    <small class="text-muted text-xs">Gunakan tombol Non-Aktif jika ingin menutup kuota secara manual sewaktu-waktu.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Jam Buka Pendaftaran Online (WITA):</label>
                                    <input type="time" name="settings[online_registration_open_time]" class="form-control form-control-sm font-weight-bold text-teal" value="<?= esc(clinic_setting('online_registration_open_time', '06:00')) ?>">
                                    <small class="text-muted text-xs">Pasien hanya dapat mengambil nomor antrean mulai dari jam ini.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Jam Tutup Pendaftaran Online (WITA):</label>
                                    <input type="time" name="settings[online_registration_close_time]" class="form-control form-control-sm font-weight-bold text-teal" value="<?= esc(clinic_setting('online_registration_close_time', '21:00')) ?>">
                                    <small class="text-muted text-xs">Form pendaftaran otomatis terkunci setelah jam ini.</small>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold text-dark">Pesan Pemberitahuan Saat Pendaftaran Ditutup:</label>
                                <textarea name="settings[online_registration_closed_message]" class="form-control form-control-sm" rows="3" placeholder="Pendaftaran online saat ini sedang ditutup di luar jam operasional..."><?= esc(clinic_setting('online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.')) ?></textarea>
                                <small class="text-muted text-xs">Pesan ini otomatis muncul di layar pasien apabila mereka mengakses form pendaftaran di luar jadwal.</small>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 6: DISPLAY TV ANTREAN & VOICE CALLER -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="display-voice" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-bullhorn text-teal mr-2"></i>Media Layar TV Antrean & Pemanggil Suara Otomatis</h5>
                                    <small class="text-muted">Kustomisasi display media edukasi di ruang tunggu serta intonasi suara pemanggil antrean poliklinik & farmasi.</small>
                                </div>
                            </div>

                            <!-- Display TV Antrean Section -->
                            <div class="card border rounded p-3 mb-4 shadow-none bg-light">
                                <h6 class="font-weight-bold text-teal mb-3"><i class="fas fa-tv mr-1"></i> Media Iklan / Video Layar Display Antrean TV</h6>
                                <div class="row">
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">Tipe Media Tampilan TV:</label>
                                        <select name="settings[tv_media_type]" id="tv_media_type" class="form-control form-control-sm font-weight-bold">
                                            <option value="slideshow" <?= clinic_setting('tv_media_type', 'slideshow') === 'slideshow' ? 'selected' : '' ?>>🖼️ Slideshow Edukasi Kesehatan & Poster</option>
                                            <option value="youtube" <?= clinic_setting('tv_media_type', 'slideshow') === 'youtube' ? 'selected' : '' ?>>📺 Video Streaming YouTube</option>
                                            <option value="local_video" <?= clinic_setting('tv_media_type', 'slideshow') === 'local_video' ? 'selected' : '' ?>>📁 Video Lokal Offline (.MP4)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">ID / Link Video YouTube:</label>
                                        <input type="text" name="settings[tv_youtube_id]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('tv_youtube_id', 'dQw4w9WgXcQ')) ?>" placeholder="dQw4w9WgXcQ atau URL lengkap">
                                        <small class="text-muted text-xs">ID 11 karakter video edukasi di YouTube.</small>
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="text-xs font-weight-bold text-dark">URL Video Lokal:</label>
                                        <input type="text" name="settings[tv_video_url]" class="form-control form-control-sm" value="<?= esc(clinic_setting('tv_video_url', '')) ?>" placeholder="https://.../video.mp4">
                                        <small class="text-muted text-xs">Diisi jika memilih media video lokal tersimpan.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Voice Synthesizer Section -->
                            <div class="card border rounded p-3 shadow-none bg-light">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-volume-high text-teal mr-1"></i> Konfigurasi Artikulasi Suara Pemanggil Antrean</h6>
                                
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="text-xs font-weight-bold text-dark">Karakter Suara Panggilan (Voice Gender): <span class="text-danger">*</span></label>
                                        <select name="settings[voice_gender]" id="voice-setting-gender" class="form-control form-control-sm font-weight-bold">
                                            <option value="female" <?= clinic_setting('voice_gender', 'female') === 'female' ? 'selected' : '' ?>>👩 Suara Perempuan (Ramah, Hangat, & Jelas - Standar Rekomendasi)</option>
                                            <option value="male" <?= clinic_setting('voice_gender', 'female') === 'male' ? 'selected' : '' ?>>👨 Suara Laki-Laki (Tegas, Berwibawa, & Jelas)</option>
                                            <option value="auto" <?= clinic_setting('voice_gender', 'female') === 'auto' ? 'selected' : '' ?>>🤖 Otomatis Sistem (Default Bahasa Indonesia Browser)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-3">
                                        <label class="text-xs font-weight-bold text-dark">Melodi Notifikasi Chime Panggilan: <span class="text-danger">*</span></label>
                                        <select name="settings[voice_chime_type]" id="voice-setting-chime" class="form-control form-control-sm font-weight-bold">
                                            <option value="hospital_2tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'hospital_2tone' ? 'selected' : '' ?>>🔔 Chime Rumah Sakit 2-Tone (Klasik E5 &rarr; C5 - Standar RS)</option>
                                            <option value="modern_3tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'modern_3tone' ? 'selected' : '' ?>>🎵 Chime Modern 3-Tone (Harmonik C5 &rarr; E5 &rarr; G5)</option>
                                            <option value="airport_4tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'airport_4tone' ? 'selected' : '' ?>>📢 Ding-Dong Bandara 4-Tone (Mayor C5 &rarr; E5 &rarr; G5 &rarr; C6)</option>
                                            <option value="soft_bell" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'soft_bell' ? 'selected' : '' ?>>🛎️ Soft Bell / Ting Lembut (F5 Minimalis Poliklinik)</option>
                                            <option value="none" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'none' ? 'selected' : '' ?>>🔕 Tanpa Chime (Langsung Suara Panggilan)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="text-xs font-weight-bold text-dark">Kecepatan Artikulasi (Speed / Rate):</label>
                                        <select name="settings[voice_rate]" id="voice-setting-rate" class="form-control form-control-sm font-weight-bold">
                                            <option value="0.75" <?= clinic_setting('voice_rate', '0.85') == '0.75' ? 'selected' : '' ?>>0.75x &mdash; Lambat (Sangat Santai & Jelas)</option>
                                            <option value="0.85" <?= clinic_setting('voice_rate', '0.85') == '0.85' ? 'selected' : '' ?>>0.85x &mdash; Standar Pelayanan Medis (Rekomendasi)</option>
                                            <option value="0.95" <?= clinic_setting('voice_rate', '0.85') == '0.95' ? 'selected' : '' ?>>0.95x &mdash; Sedang Normal</option>
                                            <option value="1.0" <?= clinic_setting('voice_rate', '0.85') == '1.0' ? 'selected' : '' ?>>1.00x &mdash; Kecepatan Penuh Natural</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="text-xs font-weight-bold text-dark">Tinggi-Rendah Nada Suara (Pitch):</label>
                                        <select name="settings[voice_pitch]" id="voice-setting-pitch" class="form-control form-control-sm font-weight-bold">
                                            <option value="0.85" <?= clinic_setting('voice_pitch', '1.0') == '0.85' ? 'selected' : '' ?>>0.85 &mdash; Bass / Berat & Berwibawa</option>
                                            <option value="1.0" <?= clinic_setting('voice_pitch', '1.0') == '1.0' ? 'selected' : '' ?>>1.00 &mdash; Nada Natural Seimbang</option>
                                            <option value="1.15" <?= clinic_setting('voice_pitch', '1.0') == '1.15' ? 'selected' : '' ?>>1.15 &mdash; Tinggi / Lembut & Ramah</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="text-xs font-weight-bold text-dark">Volume Suara Output:</label>
                                        <select name="settings[voice_volume]" id="voice-setting-volume" class="form-control form-control-sm font-weight-bold">
                                            <option value="1.0" <?= clinic_setting('voice_volume', '1.0') == '1.0' ? 'selected' : '' ?>>100% &mdash; Maksimal Jernih</option>
                                            <option value="0.8" <?= clinic_setting('voice_volume', '1.0') == '0.8' ? 'selected' : '' ?>>80% &mdash; Sedang Optimal</option>
                                            <option value="0.6" <?= clinic_setting('voice_volume', '1.0') == '0.6' ? 'selected' : '' ?>>60% &mdash; Lembut / Tenang</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Live Playground Testing Box -->
                                <div class="card p-3 border rounded shadow-none bg-white mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold text-dark text-xs"><i class="fas fa-play-circle text-teal mr-1"></i> Uji Coba Suara & Nada Panggilan Langsung (Live Audio Preview)</span>
                                        <span class="badge badge-pill badge-info text-xs font-weight-normal px-2 py-1"><i class="fas fa-headphones mr-1"></i> Tes di Speaker Browser Anda</span>
                                    </div>
                                    <div class="form-group mb-2">
                                        <input type="text" id="voice-test-text" class="form-control form-control-sm font-weight-bold text-teal" value="Nomor antrean A, nol nol satu, atas nama Budi Santoso, silakan menuju ke Loket Pelayanan Farmasi. Terima kasih.">
                                        <small class="text-muted text-xs">Ubah teks contoh di atas jika ingin mendengarkan variasi kombinasi kalimat lain.</small>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" id="btn-test-full-voice" class="btn btn-teal btn-sm font-weight-bold mr-2 mb-2 shadow-xs">
                                            <i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)
                                        </button>
                                        <button type="button" id="btn-test-chime-only" class="btn btn-outline-info btn-sm font-weight-bold mr-2 mb-2 shadow-xs">
                                            <i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Chime Saja
                                        </button>
                                        <button type="button" id="btn-stop-audio-test" class="btn btn-outline-secondary btn-sm font-weight-bold mb-2 shadow-xs">
                                            <i class="fas fa-stop mr-1"></i> Hentikan Audio
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 7: DIAGNOSTIK & INFORMASI TEKNIS SISTEM -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="diagnostics" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-server text-secondary mr-2"></i>Diagnostik & Spesifikasi Teknis Server</h5>
                                    <small class="text-muted">Informasi arsitektur sistem, status direktori unggahan, dan kesehatan database referensi terpusat.</small>
                                </div>
                                <a href="<?= base_url('system/database') ?>" class="btn btn-outline-teal btn-xs font-weight-bold">
                                    <i class="fas fa-database mr-1"></i> Manajemen Database SIM
                                </a>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border p-3 bg-light shadow-none">
                                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-microchip text-teal mr-1"></i> Lingkungan Perangkat Lunak (Software Stack)</h6>
                                        <table class="table table-sm table-borderless text-xs mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 40%;">Aplikasi:</td>
                                                <td class="font-weight-bold text-teal">Sawamawa Medical Center SIM-ERP</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Framework:</td>
                                                <td class="font-weight-bold">CodeIgniter <?= esc($diagnostics['ci_version'] ?? '4.x') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Versi PHP:</td>
                                                <td class="font-weight-bold">PHP <?= esc($diagnostics['php_version'] ?? PHP_VERSION) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Versi Database:</td>
                                                <td class="font-weight-bold">MySQL / MariaDB <?= esc($diagnostics['db_version'] ?? '8.0+') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Environment:</td>
                                                <td><span class="badge badge-success px-2 py-1"><?= esc($diagnostics['environment'] ?? 'production') ?></span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border p-3 bg-light shadow-none">
                                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-folder-tree text-info mr-1"></i> Status Storage & Direktori</h6>
                                        <table class="table table-sm table-borderless text-xs mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 40%;">Direktori Unggahan:</td>
                                                <td class="font-weight-bold text-dark">public/uploads/settings/</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Izin Tulis (Writable):</td>
                                                <td>
                                                    <?php if (!empty($diagnostics['upload_dir_writable'])): ?>
                                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Dapat Ditulis (Writable OK)</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i> Tidak Dapat Ditulis (Read Only)</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Total Baris Referensi:</td>
                                                <td class="font-weight-bold text-teal"><?= esc($diagnostics['total_settings'] ?? 0) ?> Parameter Aktif</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Waktu Server:</td>
                                                <td class="font-weight-bold"><?= esc($diagnostics['server_time'] ?? date('d M Y H:i:s')) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 8: INTEGRASI SATUSEHAT KEMENKES RI (HL7 FHIR R4) -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="satusehat" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h5 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-heart-pulse text-danger mr-2"></i>Interoperabilitas SATUSEHAT Kemenkes RI
                                    </h5>
                                    <small class="text-muted">Standarisasi data Rekam Medis Elektronik (RME) berbasis HL7 FHIR R4 sesuai amanat Permenkes No. 24 Tahun 2022.</small>
                                </div>
                                <span class="badge badge-pill badge-light border text-danger px-3 py-1 font-weight-bold">
                                    <i class="fas fa-certificate mr-1"></i> HL7 FHIR R4 Compliant
                                </span>
                            </div>

                            <!-- Alert Box Banner -->
                            <div class="alert alert-light border border-info shadow-xs p-3 mb-4 rounded">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-circle-info text-info fa-2x mr-3 mt-1"></i>
                                    <div>
                                        <strong class="text-dark d-block mb-1">Mekanisme Bridging Cerdas (Outbound HTTPS & Mock Sandbox Engine)</strong>
                                        <p class="text-muted text-xs mb-1">
                                            Sistem ERP Sawamawa Medical Center berkomunikasi dengan SATUSEHAT melalui <strong>Outbound HTTPS Request</strong> (koneksi keluar dari server).
                                            Baik saat Anda menjalankan sistem di <strong>XAMPP Lokal</strong> maupun di <strong>Hosting Production</strong>, integrasi dapat berjalan lancar.
                                        </p>
                                        <p class="text-muted text-xs mb-0">
                                            <span class="badge badge-success mr-1"><i class="fas fa-check"></i> Siap Pakai</span>
                                            Pada mode <strong>Mock Sandbox</strong>, sistem langsung memvalidasi payload HL7 FHIR R4 dan mencatat transaksi audit tanpa perlu menunggu persetujuan akun DTO Kemenkes.
                                            Begitu kredensial resmi dari Kemenkes diperoleh, cukup masukkan <strong>Client ID & Secret</strong> lalu alihkan mode ke <em>Staging</em> atau <em>Production</em>.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Kolom Kiri: Konfigurasi Akun & Endpoint -->
                                <div class="col-lg-7 mb-4">
                                    <div class="card h-100 border shadow-none">
                                        <div class="card-header bg-light py-2 px-3">
                                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                                <i class="fas fa-key text-warning mr-1"></i> Kredensial & Parameter Otentikasi OAuth2
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            
                                            <!-- Status Aktif -->
                                            <div class="form-group mb-3 pb-2 border-bottom">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="settings[satusehat_active]" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="satusehat_active" name="settings[satusehat_active]" value="1" <?= in_array(clinic_setting('satusehat_active', '1'), ['1', 'true', 'on', 1]) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label font-weight-bold text-dark" for="satusehat_active">
                                                        Aktifkan Bridging SATUSEHAT Kemenkes RI
                                                    </label>
                                                </div>
                                                <small class="text-muted text-xs d-block mt-1">Jika dinonaktifkan, proses rekam medis tetap berjalan lokal tanpa mengirim log bridging ke Kemenkes.</small>
                                            </div>

                                            <!-- Mode Lingkungan -->
                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Mode Lingkungan (Environment): <span class="text-danger">*</span></label>
                                                <?php $currentMode = clinic_setting('satusehat_mode', 'sandbox'); ?>
                                                <select name="settings[satusehat_mode]" id="satusehat_mode" class="form-control form-control-sm font-weight-bold">
                                                    <option value="sandbox" <?= $currentMode === 'sandbox' ? 'selected' : '' ?>>
                                                        🛠️ Mock Sandbox Cerdas (Simulasi Lokal XAMPP - Respon 201 Created Instan)
                                                    </option>
                                                    <option value="staging" <?= $currentMode === 'staging' ? 'selected' : '' ?>>
                                                        🧪 Staging Kemenkes RI (Server Uji Coba DTO Resmi)
                                                    </option>
                                                    <option value="production" <?= $currentMode === 'production' ? 'selected' : '' ?>>
                                                        🚀 Production Kemenkes RI (Server Live Satu Data Nasional)
                                                    </option>
                                                </select>
                                                <small class="text-muted text-xs">Pilih <em>Mock Sandbox</em> untuk pengetesan bebas galat di XAMPP lokal.</small>
                                            </div>

                                            <!-- Organization ID (Faskes ID) -->
                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Organization ID Faskes (Kemenkes): <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light"><i class="fas fa-building text-secondary"></i></span>
                                                    </div>
                                                    <input type="text" name="settings[satusehat_org_id]" id="satusehat_org_id" class="form-control" value="<?= esc(clinic_setting('satusehat_org_id', '10000004')) ?>" required placeholder="Contoh: 10000004">
                                                </div>
                                                <small class="text-muted text-xs">ID Organisasi Klinik Sawamawa yang terdaftar pada portal SATUSEHAT Platform Kemenkes.</small>
                                            </div>

                                            <!-- Client ID -->
                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Client ID OAuth2: <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light"><i class="fas fa-id-badge text-secondary"></i></span>
                                                    </div>
                                                    <input type="text" name="settings[satusehat_client_id]" id="satusehat_client_id" class="form-control" value="<?= esc(clinic_setting('satusehat_client_id', 'MOCK-CLIENT-ID-SAWAMAWA')) ?>" required placeholder="Client ID dari portal SatuSehat">
                                                </div>
                                            </div>

                                            <!-- Client Secret -->
                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Client Secret OAuth2: <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light"><i class="fas fa-lock text-secondary"></i></span>
                                                    </div>
                                                    <input type="password" name="settings[satusehat_client_secret]" id="satusehat_client_secret" class="form-control" value="<?= esc(clinic_setting('satusehat_client_secret', 'MOCK-CLIENT-SECRET-SAWAMAWA')) ?>" required placeholder="Client Secret dari portal SatuSehat">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary btn-toggle-pw" type="button" data-target="satusehat_client_secret">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Advanced URL Endpoints Accordion -->
                                            <div class="accordion" id="accordionSatuSehatEndpoints">
                                                <div class="border rounded">
                                                    <div class="p-2 bg-light d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#collapseEndpoints" style="cursor: pointer;">
                                                        <span class="text-xs font-weight-bold text-teal"><i class="fas fa-sliders mr-1"></i> Konfigurasi Lanjutan Endpoint URL</span>
                                                        <i class="fas fa-chevron-down text-muted text-xs"></i>
                                                    </div>
                                                    <div id="collapseEndpoints" class="collapse p-3" data-parent="#accordionSatuSehatEndpoints">
                                                        <div class="form-group mb-2">
                                                            <label class="text-xs font-weight-bold text-dark">OAuth2 Auth URL:</label>
                                                            <input type="text" name="settings[satusehat_auth_url]" id="satusehat_auth_url" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_auth_url', 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1')) ?>">
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <label class="text-xs font-weight-bold text-dark">FHIR R4 Base URL:</label>
                                                            <input type="text" name="settings[satusehat_fhir_url]" id="satusehat_fhir_url" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_fhir_url', 'https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1')) ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Uji Coba Koneksi Real-time & Panduan FHIR -->
                                <div class="col-lg-5 mb-4">
                                    <div class="card h-100 border shadow-none bg-light">
                                        <div class="card-header bg-white py-2 px-3 border-bottom">
                                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                                <i class="fas fa-network-wired text-teal mr-1"></i> Uji Konektivitas Real-Time
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <p class="text-xs text-muted mb-3">
                                                Uji jalur komunikasi dan otentikasi token Bearer dengan gateway SATUSEHAT Kemenkes secara langsung tanpa reload halaman.
                                            </p>

                                            <!-- Tombol Uji Koneksi -->
                                            <button type="button" class="btn btn-teal btn-block font-weight-bold shadow-xs py-2 mb-3" id="btn-ping-satusehat">
                                                <i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)
                                            </button>

                                            <!-- Hasil Diagnostik Uji Koneksi -->
                                            <div id="satusehat-ping-result" class="d-none"></div>

                                            <hr class="my-3">

                                            <!-- Ringkasan Cakupan Sumber Daya HL7 FHIR R4 -->
                                            <h6 class="text-xs font-weight-bold text-dark text-uppercase mb-2">
                                                <i class="fas fa-cubes text-info mr-1"></i> Sumber Daya FHIR yang Didukung:
                                            </h6>
                                            <div class="list-group list-group-flush text-xs bg-transparent">
                                                <div class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-user-check text-success mr-1"></i> <strong>Patient</strong> (IHS Pasien via NIK)</span>
                                                    <span class="badge badge-light border">Otomatis</span>
                                                </div>
                                                <div class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-door-open text-primary mr-1"></i> <strong>Encounter</strong> (Kunjungan Rawat Jalan)</span>
                                                    <span class="badge badge-success">Siap</span>
                                                </div>
                                                <div class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-stethoscope text-danger mr-1"></i> <strong>Condition</strong> (Diagnosa Medis ICD-10)</span>
                                                    <span class="badge badge-success">Siap</span>
                                                </div>
                                                <div class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-heart-pulse text-warning mr-1"></i> <strong>Observation</strong> (Vital Signs / TTV)</span>
                                                    <span class="badge badge-success">Siap</span>
                                                </div>
                                            </div>

                                            <div class="alert alert-light border text-xs text-muted mt-3 mb-0 p-2">
                                                <i class="fas fa-shield-alt text-teal mr-1"></i> Seluruh payload rekam medis dienkripsi dan dicatat ke log audit database untuk kepatuhan Permenkes No. 24/2022.
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <!-- Bottom Action Footer -->
                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle text-teal mr-1"></i> Seluruh perubahan disimpan secara permanen pada tabel <code>system_settings</code>.
                    </small>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4 shadow-sm" id="btn-save-bottom">
                        <i class="fas fa-save mr-1"></i> Simpan Seluruh Konfigurasi
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC (Live Kop Surat Binding, Previews, Audio, WhatsApp Ping) -->
<!-- ========================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // 1. Tab Persistence via Hash & LocalStorage
    const hash = window.location.hash;
    if (hash && typeof jQuery !== 'undefined') {
        $('#settingsTab a[href="' + hash + '"]').tab('show');
    }
    if (typeof jQuery !== 'undefined') {
        $('#settingsTab a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            const targetHash = e.target.hash;
            if (history.pushState) {
                history.pushState(null, null, targetHash);
            } else {
                window.location.hash = targetHash;
            }
            try { localStorage.setItem('sawamawa_active_settings_tab', targetHash); } catch(err){}
        });
        const savedTab = localStorage.getItem('sawamawa_active_settings_tab');
        if (!hash && savedTab && $('#settingsTab a[href="' + savedTab + '"]').length) {
            $('#settingsTab a[href="' + savedTab + '"]').tab('show');
        }
    }

    // 2. Real-time Live Kop Surat Simulator Binding
    const bindMap = [
        { input: 'input-clinic-name', sim: ['sim-name', 'sim-doc-clinic'], def: 'Sawamawa Medical Center' },
        { input: 'input-clinic-tagline', sim: ['sim-tagline'], def: 'Pusat Layanan Medis Terpadu & Terpercaya' },
        { input: 'input-clinic-address', sim: ['sim-address'], def: 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB' },
        { input: 'input-clinic-director', sim: ['sim-director'], def: 'dr. Andi Wijaya, Sp.PD' },
        { input: 'input-clinic-sip', sim: ['sim-sip'], def: '503/449/SIP-D/2022' },
        { input: 'input-clinic-footer', sim: ['sim-footer'], def: 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.' },
        { input: 'input-clinic-watermark', sim: ['sim-watermark'], def: 'SAWAMAWA' }
    ];

    bindMap.forEach(item => {
        const el = document.getElementById(item.input);
        if (el) {
            el.addEventListener('input', function() {
                item.sim.forEach(simId => {
                    const simEl = document.getElementById(simId);
                    if (simEl) {
                        simEl.textContent = el.value.trim() || item.def;
                    }
                });
                triggerDirty();
            });
        }
    });

    // Update contacts line in kop preview
    function updateSimContacts() {
        const phone = document.getElementById('input-clinic-phone')?.value.trim() || '(0371) 23456';
        const email = document.getElementById('input-clinic-email')?.value.trim() || 'info@sawamawamedicalcenter.id';
        const web   = document.getElementById('input-clinic-website')?.value.trim() || 'www.sawamawamedicalcenter.id';
        const simContacts = document.getElementById('sim-contacts');
        if (simContacts) {
            simContacts.textContent = `Telp: ${phone} | Email: ${email} | Web: ${web}`;
        }
    }
    ['input-clinic-phone', 'input-clinic-email', 'input-clinic-website'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            updateSimContacts();
            triggerDirty();
        });
    });

    // Update license line in kop preview
    function updateSimLicense() {
        const lic = document.getElementById('input-clinic-license')?.value.trim() || '445/012/DINKES/2024';
        const faskes = document.getElementById('input-clinic-faskes')?.value.trim() || '52040101';
        const simLicense = document.getElementById('sim-license');
        if (simLicense) {
            simLicense.innerHTML = `Izin Dinkes: ${lic} &bull; Kode Faskes: ${faskes}`;
        }
    }
    ['input-clinic-license', 'input-clinic-faskes'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            updateSimLicense();
            triggerDirty();
        });
    });

    // 3. Instant Image Preview via FileReader
    function setupFilePreview(inputId, previewImgId, placeholderId, simImgId, simFallbackId) {
        const input = document.getElementById(inputId);
        const previewImg = document.getElementById(previewImgId);
        const placeholder = document.getElementById(placeholderId);
        const simImg = document.getElementById(simImgId);
        const simFallback = document.getElementById(simFallbackId);

        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        const dataUrl = evt.target.result;
                        if (previewImg) {
                            previewImg.src = dataUrl;
                            previewImg.style.display = 'inline-block';
                        }
                        if (placeholder) placeholder.style.display = 'none';
                        if (simImg) {
                            simImg.src = dataUrl;
                            simImg.style.display = 'inline-block';
                        }
                        if (simFallback) simFallback.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                    triggerDirty();
                }
            });
        }
    }

    setupFilePreview('clinic_logo_file', 'preview-logo-img', 'placeholder-logo', 'sim-logo', 'sim-logo-fallback');
    setupFilePreview('clinic_favicon_file', 'preview-favicon-img', 'placeholder-favicon', null, null);
    setupFilePreview('clinic_stamp_file', 'preview-stamp-img', 'placeholder-stamp', 'sim-stamp', null);

    // Removal Buttons for Logo, Favicon, Stamp
    document.getElementById('btn-remove-logo')?.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus Logo Utama dan kembali ke default?')) {
            document.getElementById('remove_logo').value = '1';
            document.getElementById('preview-logo-img').style.display = 'none';
            document.getElementById('placeholder-logo').style.display = 'block';
            document.getElementById('sim-logo').style.display = 'none';
            document.getElementById('sim-logo-fallback').style.display = 'block';
            this.style.display = 'none';
            triggerDirty();
        }
    });

    document.getElementById('btn-remove-favicon')?.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus Favicon?')) {
            document.getElementById('remove_favicon').value = '1';
            document.getElementById('preview-favicon-img').style.display = 'none';
            document.getElementById('placeholder-favicon').style.display = 'block';
            this.style.display = 'none';
            triggerDirty();
        }
    });

    document.getElementById('btn-remove-stamp')?.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus Stempel Digital resmi?')) {
            document.getElementById('remove_stamp').value = '1';
            document.getElementById('preview-stamp-img').style.display = 'none';
            document.getElementById('placeholder-stamp').style.display = 'block';
            document.getElementById('sim-stamp').style.display = 'none';
            this.style.display = 'none';
            triggerDirty();
        }
    });

    // 4. Form Dirty Indicator & Anti Double-Submit
    let formIsDirty = false;
    function triggerDirty() {
        formIsDirty = true;
        document.getElementById('dirty-badge')?.classList.remove('d-none');
    }

    const form = document.getElementById('form-system-settings');
    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('change', triggerDirty);
        });

        form.addEventListener('submit', function(e) {
            formIsDirty = false;
            const btnTop = document.getElementById('btn-save-top');
            const btnBtm = document.getElementById('btn-save-bottom');
            const spinnerHtml = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan Konfigurasi...';
            if (btnTop) {
                btnTop.disabled = true;
                btnTop.innerHTML = spinnerHtml;
            }
            if (btnBtm) {
                btnBtm.disabled = true;
                btnBtm.innerHTML = spinnerHtml;
            }
        });
    }

    window.addEventListener('beforeunload', function(e) {
        if (formIsDirty) {
            e.preventDefault();
            e.returnValue = 'Terdapat perubahan pengaturan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        }
    });

    // Toggle View Password on API Token
    document.getElementById('btn-toggle-token')?.addEventListener('click', function() {
        const inp = document.getElementById('wa_api_token');
        const icon = this.querySelector('i');
        if (inp) {
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    });

    // 5. WhatsApp Gateway Live Ping Test (AJAX)
    const btnPingWa = document.getElementById('btn-ping-wa');
    const waPingResult = document.getElementById('wa-ping-result');

    if (btnPingWa) {
        btnPingWa.addEventListener('click', function() {
            const provider = document.getElementById('wa_gateway_provider')?.value || 'fonnte';
            const token = document.getElementById('wa_api_token')?.value.trim() || '';
            const sender = document.getElementById('wa_sender_number')?.value.trim() || '';

            if (!token) {
                alert('Silakan masukkan API Token WhatsApp Gateway terlebih dahulu pada input di atas.');
                document.getElementById('wa_api_token')?.focus();
                return;
            }

            btnPingWa.disabled = true;
            btnPingWa.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menguji Gateway...';
            waPingResult.classList.remove('d-none');
            waPingResult.innerHTML = '<div class="alert alert-info py-2 px-3 mb-0 text-xs"><i class="fas fa-circle-notch fa-spin mr-1"></i> Mengirim permintaan ping ke server gateway...</div>';

            const csrfInput = form?.querySelector('input[name="csrf_test_name"]');
            const formData = new FormData();
            formData.append('provider', provider);
            formData.append('token', token);
            formData.append('sender', sender);
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            fetch('<?= base_url('system/test-wa-gateway') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btnPingWa.disabled = false;
                btnPingWa.innerHTML = '<i class="fab fa-whatsapp mr-1"></i> Uji Koneksi Gateway (Test Ping)';

                let alertClass = 'alert-success';
                let iconClass = 'fa-check-circle text-success';
                if (data.status === 'warning') {
                    alertClass = 'alert-warning';
                    iconClass = 'fa-exclamation-triangle text-warning';
                } else if (data.status === 'error') {
                    alertClass = 'alert-danger';
                    iconClass = 'fa-times-circle text-danger';
                }

                waPingResult.innerHTML = `
                    <div class="alert ${alertClass} py-2 px-3 mb-0 text-xs shadow-none">
                        <div class="d-flex align-items-center">
                            <i class="fas ${iconClass} mr-2 fa-lg"></i>
                            <div>
                                <strong>Hasil Pengujian WhatsApp:</strong>
                                <div style="white-space: pre-line;">${data.message || 'Respon diterima.'}</div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(err => {
                btnPingWa.disabled = false;
                btnPingWa.innerHTML = '<i class="fab fa-whatsapp mr-1"></i> Uji Koneksi Gateway (Test Ping)';
                waPingResult.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 mb-0 text-xs">
                        <i class="fas fa-times-circle mr-1"></i> Gagal menghubungi endpoint uji coba: ${err.message}
                    </div>
                `;
            });
        });
    }

    // 5.b SATUSEHAT Kemenkes Live Test Ping & Auth Runner (AJAX)
    const btnPingSatuSehat = document.getElementById('btn-ping-satusehat');
    const satuSehatPingResult = document.getElementById('satusehat-ping-result');

    if (btnPingSatuSehat) {
        btnPingSatuSehat.addEventListener('click', function() {
            btnPingSatuSehat.disabled = true;
            btnPingSatuSehat.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menguji Koneksi SATUSEHAT...';
            satuSehatPingResult.classList.remove('d-none');
            satuSehatPingResult.innerHTML = '<div class="alert alert-info py-2 px-3 mb-0 text-xs"><i class="fas fa-circle-notch fa-spin mr-1"></i> Mengirim permintaan otentikasi token ke server SATUSEHAT...</div>';

            const csrfInput = form?.querySelector('input[name="csrf_test_name"]');
            const formData = new FormData();
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            fetch('<?= base_url('system/test-satusehat') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btnPingSatuSehat.disabled = false;
                btnPingSatuSehat.innerHTML = '<i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)';

                let alertClass = 'alert-success';
                let iconClass = 'fa-check-circle text-success';
                if (data.status === 'warning') {
                    alertClass = 'alert-warning';
                    iconClass = 'fa-exclamation-triangle text-warning';
                } else if (data.status === 'error') {
                    alertClass = 'alert-danger';
                    iconClass = 'fa-times-circle text-danger';
                }

                const details = data.data || {};
                let detailBadges = '';
                if (details.mode) {
                    detailBadges += `<span class="badge badge-dark mr-1">Mode: ${details.mode.toUpperCase()}</span>`;
                }
                if (details.http_status) {
                    detailBadges += `<span class="badge badge-info mr-1">HTTP ${details.http_status}</span>`;
                }
                if (details.token_type) {
                    detailBadges += `<span class="badge badge-success mr-1">Token: ${details.token_type}</span>`;
                }

                satuSehatPingResult.innerHTML = `
                    <div class="alert ${alertClass} py-3 px-3 mb-0 text-xs shadow-none border">
                        <div class="d-flex align-items-start">
                            <i class="fas ${iconClass} mr-2 fa-2x mt-1"></i>
                            <div>
                                <strong class="d-block mb-1 font-weight-bold" style="font-size:13px;">${data.status === 'success' ? 'Koneksi SATUSEHAT Berhasil!' : 'Pemberitahuan SATUSEHAT'}</strong>
                                <div class="mb-2" style="white-space: pre-line;">${data.message || 'Respon diterima.'}</div>
                                <div class="mt-1">${detailBadges}</div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .catch(err => {
                btnPingSatuSehat.disabled = false;
                btnPingSatuSehat.innerHTML = '<i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)';
                satuSehatPingResult.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 mb-0 text-xs">
                        <i class="fas fa-times-circle mr-1"></i> Gagal menghubungi endpoint uji coba: ${err.message}
                    </div>
                `;
            });
        });
    }

    // 6. Built-in Web Audio Chime & Speech Synthesizer Engine
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    let audioContext = null;

    function getAudioContext() {
        if (!audioContext) {
            audioContext = new AudioCtx();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
        return audioContext;
    }

    function playTone(freq, type, startTime, duration, gainNode, ctx) {
        const osc = ctx.createOscillator();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, startTime);
        osc.connect(gainNode);
        osc.start(startTime);
        osc.stop(startTime + duration);
    }

    function synthesizeChime(chimeType, onEnd) {
        if (chimeType === 'none') {
            if (typeof onEnd === 'function') onEnd();
            return;
        }

        try {
            const ctx = getAudioContext();
            const now = ctx.currentTime;
            const gain = ctx.createGain();
            gain.connect(ctx.destination);

            let totalDuration = 1.0;

            if (chimeType === 'hospital_2tone') {
                // E5 (659.25Hz) -> C5 (523.25Hz)
                gain.gain.setValueAtTime(0.001, now);
                gain.gain.linearRampToValueAtTime(0.4, now + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.05, now + 0.45);
                gain.gain.linearRampToValueAtTime(0.45, now + 0.5);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 1.2);
                playTone(659.25, 'sine', now, 0.45, gain, ctx);
                playTone(523.25, 'sine', now + 0.45, 0.75, gain, ctx);
                totalDuration = 1.2;
            } else if (chimeType === 'modern_3tone') {
                // C5 -> E5 -> G5
                gain.gain.setValueAtTime(0.001, now);
                gain.gain.linearRampToValueAtTime(0.35, now + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 1.2);
                playTone(523.25, 'triangle', now, 0.3, gain, ctx);
                playTone(659.25, 'triangle', now + 0.3, 0.3, gain, ctx);
                playTone(783.99, 'sine', now + 0.6, 0.6, gain, ctx);
                totalDuration = 1.2;
            } else if (chimeType === 'airport_4tone') {
                // C5 -> E5 -> G5 -> C6
                gain.gain.setValueAtTime(0.001, now);
                gain.gain.linearRampToValueAtTime(0.35, now + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 1.5);
                playTone(523.25, 'sine', now, 0.25, gain, ctx);
                playTone(659.25, 'sine', now + 0.25, 0.25, gain, ctx);
                playTone(783.99, 'sine', now + 0.5, 0.25, gain, ctx);
                playTone(1046.50, 'sine', now + 0.75, 0.75, gain, ctx);
                totalDuration = 1.5;
            } else if (chimeType === 'soft_bell') {
                gain.gain.setValueAtTime(0.001, now);
                gain.gain.linearRampToValueAtTime(0.3, now + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.9);
                playTone(698.46, 'sine', now, 0.9, gain, ctx);
                totalDuration = 0.9;
            }

            setTimeout(function() {
                if (typeof onEnd === 'function') onEnd();
            }, totalDuration * 1000);
        } catch(e) {
            console.warn('Audio synthesis fallback', e);
            if (typeof onEnd === 'function') onEnd();
        }
    }

    function speakText(text, gender, rate, pitch, volume, onEnd) {
        if (!('speechSynthesis' in window)) {
            alert('Browser Anda tidak mendukung Web Speech API Synthesizer.');
            if (typeof onEnd === 'function') onEnd();
            return;
        }

        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'id-ID';
        utter.rate = rate;
        utter.pitch = pitch;
        utter.volume = volume;

        const voices = window.speechSynthesis.getVoices();
        if (voices && voices.length > 0) {
            let selectedVoice = null;
            const idVoices = voices.filter(v => v.lang.startsWith('id') || v.lang.startsWith('ID'));
            if (idVoices.length > 0) {
                if (gender === 'female') {
                    selectedVoice = idVoices.find(v => v.name.toLowerCase().includes('gadis') || v.name.toLowerCase().includes('female') || v.name.toLowerCase().includes('putri')) || idVoices[0];
                } else if (gender === 'male') {
                    selectedVoice = idVoices.find(v => v.name.toLowerCase().includes('male') || v.name.toLowerCase().includes('budi') || v.name.toLowerCase().includes('pria')) || idVoices[0];
                } else {
                    selectedVoice = idVoices[0];
                }
            }
            if (selectedVoice) {
                utter.voice = selectedVoice;
            }
        }

        utter.onend = function() {
            if (typeof onEnd === 'function') onEnd();
        };
        utter.onerror = function() {
            if (typeof onEnd === 'function') onEnd();
        };

        window.speechSynthesis.speak(utter);
    }

    // Audio Playground Buttons
    const btnTestFull = document.getElementById('btn-test-full-voice');
    const btnTestChime = document.getElementById('btn-test-chime-only');
    const btnStopAudio = document.getElementById('btn-stop-audio-test');

    if (btnTestFull) {
        btnTestFull.addEventListener('click', function() {
            const gender = document.getElementById('voice-setting-gender')?.value || 'female';
            const chimeType = document.getElementById('voice-setting-chime')?.value || 'hospital_2tone';
            const rate = parseFloat(document.getElementById('voice-setting-rate')?.value || '0.85');
            const pitch = parseFloat(document.getElementById('voice-setting-pitch')?.value || '1.0');
            const volume = parseFloat(document.getElementById('voice-setting-volume')?.value || '1.0');
            const text = document.getElementById('voice-test-text')?.value.trim() || 'Nomor antrean A nol nol satu, silakan menuju loket.';

            btnTestFull.disabled = true;
            btnTestFull.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memutar Audio...';

            synthesizeChime(chimeType, function() {
                speakText(text, gender, rate, pitch, volume, function() {
                    btnTestFull.disabled = false;
                    btnTestFull.innerHTML = '<i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)';
                });
            });
        });
    }

    if (btnTestChime) {
        btnTestChime.addEventListener('click', function() {
            const chimeType = document.getElementById('voice-setting-chime')?.value || 'hospital_2tone';
            btnTestChime.disabled = true;
            btnTestChime.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memutar Chime...';

            synthesizeChime(chimeType, function() {
                btnTestChime.disabled = false;
                btnTestChime.innerHTML = '<i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Chime Saja';
            });
        });
    }

    if (btnStopAudio) {
        btnStopAudio.addEventListener('click', function() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
            if (audioContext && audioContext.state !== 'closed') {
                audioContext.suspend();
            }
            if (btnTestFull) {
                btnTestFull.disabled = false;
                btnTestFull.innerHTML = '<i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)';
            }
            if (btnTestChime) {
                btnTestChime.disabled = false;
                btnTestChime.innerHTML = '<i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Chime Saja';
            }
        });
    }

});
</script>

<style>
.nav-tabs .nav-link {
    color: #495057;
    border-top: 3px solid transparent;
    transition: all 0.2s ease-in-out;
}
.nav-tabs .nav-link:hover {
    border-top: 3px solid #b2dfdb;
    background-color: #f8f9fa;
}
.nav-tabs .nav-link.active {
    color: #00796b !important;
    border-top: 3px solid #20c997 !important;
    background-color: #ffffff !important;
}
.badge-teal {
    background-color: #20c997;
    color: #fff;
}
.btn-teal {
    background-color: #20c997;
    border-color: #20c997;
    color: #fff;
}
.btn-teal:hover {
    background-color: #17a57a;
    border-color: #17a57a;
    color: #fff;
}
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
</style>
<?= $this->endSection() ?>
