<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-sliders text-teal mr-2"></i> Pengaturan Parameter & Identitas Klinik
                </h1>
                <small class="text-muted">Konfigurasi kop klinik resmi, upload logo/favicon, regulasi farmasi/resto, dan WhatsApp Gateway</small>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <form action="<?= base_url('system/settings') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="card card-outline card-teal shadow-sm" style="border: 1px solid #b8b8b8;">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold py-3 px-4" id="klinik-tab" data-toggle="pill" href="#klinik" role="tab">
                                <i class="fas fa-hospital text-teal mr-1"></i> 1. IDENTITAS & KOP KLINIK
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="logo-tab" data-toggle="pill" href="#logo" role="tab">
                                <i class="fas fa-image text-info mr-1"></i> 2. LOGO & FAVICON
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="apotek-tab" data-toggle="pill" href="#apotek" role="tab">
                                <i class="fas fa-pills text-success mr-1"></i> 3. REGULASI FARMASI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="resto-tab" data-toggle="pill" href="#resto" role="tab">
                                <i class="fas fa-utensils text-warning mr-1"></i> 4. BILLING RESTORAN
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="api-tab" data-toggle="pill" href="#api" role="tab">
                                <i class="fab fa-whatsapp text-success mr-1"></i> 5. API WHATSAPP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="online-reg-tab" data-toggle="pill" href="#online-reg" role="tab">
                                <i class="fas fa-calendar-check text-primary mr-1"></i> 6. PENDAFTARAN ONLINE
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="voice-tab" data-toggle="pill" href="#voice" role="tab">
                                <i class="fas fa-volume-high text-teal mr-1"></i> 7. SUARA & NOTIFIKASI PANGGILAN
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-3 px-4" id="satusehat-tab" data-toggle="pill" href="#satusehat" role="tab">
                                <i class="fas fa-heart-pulse text-danger mr-1"></i> 8. SATUSEHAT KEMENKES
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body bg-white p-4">
                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- ========================================================================= -->
                        <!-- TAB 1: IDENTITAS & KOP KLINIK -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade show active" id="klinik" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Identitas Resmi & Kop Surat Klinik</h6>
                                    <small class="text-muted">Data ini otomatis diterapkan pada seluruh halaman, lembar resep, slip gaji, PO, PR, dan laporan cetak.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Nama Resmi Klinik: <span class="text-danger">*</span></label>
                                    <input type="text" name="settings[clinic_name]" class="form-control form-control-sm font-weight-bold text-teal" value="<?= esc(clinic_setting('clinic_name')) ?>" required>
                                    <small class="text-muted text-xs">Digunakan sebagai judul website, kop surat, dan kwitansi.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Slogan / Tagline Klinik:</label>
                                    <input type="text" name="settings[clinic_tagline]" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_tagline')) ?>">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Alamat Lengkap Kantor / Fasilitas: <span class="text-danger">*</span></label>
                                <textarea name="settings[clinic_address]" class="form-control form-control-sm" rows="2" required><?= esc(clinic_setting('clinic_address')) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Nomor Telepon Hotline / WhatsApp: <span class="text-danger">*</span></label>
                                    <input type="text" name="settings[clinic_phone]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_phone')) ?>" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Email Resmi Pelayanan:</label>
                                    <input type="email" name="settings[clinic_email]" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_email')) ?>">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Website Resmi:</label>
                                    <input type="text" name="settings[clinic_website]" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_website')) ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Nomor Izin Operasional Klinik:</label>
                                    <input type="text" name="settings[clinic_license_number]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_license_number')) ?>" placeholder="445/012/DINKES/2024">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Nama Direktur / Penanggung Jawab Medis:</label>
                                    <input type="text" name="settings[clinic_director]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_director')) ?>">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Status Integrasi BPJS Kesehatan:</label>
                                    <select name="settings[clinic_bpjs_active]" class="form-control form-control-sm font-weight-bold">
                                        <option value="true" <?= clinic_setting('clinic_bpjs_active') === 'true' ? 'selected' : '' ?>>Aktif (Terintegrasi BPJS)</option>
                                        <option value="false" <?= clinic_setting('clinic_bpjs_active') === 'false' ? 'selected' : '' ?>>Non-Aktif (Umum / Swasta)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Teks Watermark Dokumen Kertas:</label>
                                    <input type="text" name="settings[clinic_watermark]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('clinic_watermark')) ?>">
                                    <small class="text-muted text-xs">Muncul transparan di tengah lembar dokumen e-resep, PR, PO, dan slip gaji.</small>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Catatan Kaki (Footer Note) Dokumen:</label>
                                    <input type="text" name="settings[clinic_footer_note]" class="form-control form-control-sm" value="<?= esc(clinic_setting('clinic_footer_note')) ?>">
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 2: LOGO & FAVICON -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="logo" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Upload Logo & Favicon Klinik</h6>
                                    <small class="text-muted">Logo otomatis tampil di Sidebar, Halaman Login, Kop Surat, dan Lembar Dokumen Resmi.</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Upload Logo -->
                                <div class="col-md-6 mb-4">
                                    <div class="card p-3 border shadow-none bg-light">
                                        <h6 class="font-weight-bold text-teal mb-2"><i class="fas fa-image mr-1"></i> Logo Utama Klinik</h6>
                                        <div class="text-center p-3 bg-white border rounded mb-3" style="min-height: 140px; display:flex; align-items:center; justify-content:center;">
                                            <?php if (clinic_logo()): ?>
                                                <img src="<?= clinic_logo() ?>" alt="Logo Klinik" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                                            <?php else: ?>
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-hospital-user fa-3x text-teal mb-2"></i>
                                                    <div class="small font-weight-bold"><?= esc(clinic_setting('clinic_name')) ?></div>
                                                    <small class="text-xs text-secondary">(Logo default sistem aktif)</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="text-xs font-weight-bold">Pilih File Logo Baru (PNG / JPG / SVG):</label>
                                            <input type="file" name="clinic_logo_file" class="form-control-file border p-1 bg-white" accept="image/*">
                                            <small class="text-muted text-xs">Rekomendasi resolusi: 400x120 px (Format transparan PNG).</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Favicon -->
                                <div class="col-md-6 mb-4">
                                    <div class="card p-3 border shadow-none bg-light">
                                        <h6 class="font-weight-bold text-info mb-2"><i class="fas fa-globe mr-1"></i> Favicon Browser Tab</h6>
                                        <div class="text-center p-3 bg-white border rounded mb-3" style="min-height: 140px; display:flex; align-items:center; justify-content:center;">
                                            <?php if (clinic_favicon()): ?>
                                                <img src="<?= clinic_favicon() ?>" alt="Favicon" style="max-height: 64px; max-width: 64px; object-fit: contain;">
                                            <?php else: ?>
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-square-plus fa-3x text-info mb-2"></i>
                                                    <div class="small font-weight-bold">Favicon Default</div>
                                                    <small class="text-xs text-secondary">(Muncul di tab browser)</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="text-xs font-weight-bold">Pilih File Favicon (ICO / PNG):</label>
                                            <input type="file" name="clinic_favicon_file" class="form-control-file border p-1 bg-white" accept="image/*">
                                            <small class="text-muted text-xs">Rekomendasi resolusi: 32x32 atau 64x64 px.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 3: REGULASI FARMASI -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="apotek" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Parameter & Regulasi Apotek Farmasi</h6>
                                    <small class="text-muted">Batas peringatan stok kritis, margin keuntungan, dan persentase pajak obat.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Peringatan Minimum Stok Kritis:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="settings[pharmacy_min_stock_alert]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_min_stock_alert', '10')) ?>" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">Unit / Box</span>
                                        </div>
                                    </div>
                                    <small class="text-muted text-xs">Trigger notifikasi merah stok kritis.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Persentase Margin Keuntungan Standar Obat:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="settings[pharmacy_profit_margin_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_profit_margin_percent', '20')) ?>" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Persentase Pajak Penjualan Obat:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="settings[pharmacy_tax_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('pharmacy_tax_percent', '10')) ?>" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 4: BILLING RESTORAN -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="resto" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Parameter POS Resto & Nutrisi Sehat</h6>
                                    <small class="text-muted">Pajak restoran, service charge, dan integrasi pembebanan billing rawat inap.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Pajak Penjualan Restoran (PB1):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="settings[resto_tax_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('resto_tax_percent', '10')) ?>" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Biaya Pelayanan (Service Charge):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="settings[resto_service_charge_percent]" class="form-control font-weight-bold" value="<?= esc(clinic_setting('resto_service_charge_percent', '5')) ?>" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Pembebanan Billing Pasien Rawat Inap:</label>
                                    <select name="settings[resto_auto_billing_inpatient]" class="form-control form-control-sm font-weight-bold">
                                        <option value="true" <?= clinic_setting('resto_auto_billing_inpatient') === 'true' ? 'selected' : '' ?>>Otomatis Bebankan Ke Billing Pasien</option>
                                        <option value="false" <?= clinic_setting('resto_auto_billing_inpatient') === 'false' ? 'selected' : '' ?>>Pisahkan Kasir Resto Mandiri</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 5: API WHATSAPP -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="api" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Integrasi WhatsApp Gateway & Notifikasi Otomatis</h6>
                                    <small class="text-muted">Kirimkan notifikasi antrean, resep obat siap ambil, invoice tagihan, dan reminder jadwal kontrol pasien.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Provider WhatsApp Gateway:</label>
                                    <select name="settings[wa_gateway_provider]" class="form-control form-control-sm font-weight-bold">
                                        <option value="fonnte" <?= clinic_setting('wa_gateway_provider') === 'fonnte' ? 'selected' : '' ?>>Fonnte Gateway</option>
                                        <option value="wablas" <?= clinic_setting('wa_gateway_provider') === 'wablas' ? 'selected' : '' ?>>Wablas Gateway</option>
                                        <option value="custom" <?= clinic_setting('wa_gateway_provider') === 'custom' ? 'selected' : '' ?>>Custom Webhook API</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Nomor Pengirim WhatsApp Resmi:</label>
                                    <input type="text" name="settings[wa_sender_number]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('wa_sender_number')) ?>" placeholder="081122334455">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">API Token WhatsApp Gateway:</label>
                                    <input type="password" name="settings[wa_api_token]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('wa_api_token')) ?>" placeholder="Masukkan API Token Gateway">
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 6: PENGATURAN PEMBATASAN PENDAFTARAN ONLINE -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="online-reg" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Pengaturan Jam & Status Pendaftaran Online</h6>
                                    <small class="text-muted">Kontrol kapan pasien dapat melakukan pendaftaran antrean mandiri dari website publik.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Status Pendaftaran Online:</label>
                                    <select name="settings[online_registration_active]" class="form-control form-control-sm font-weight-bold">
                                        <option value="true" <?= clinic_setting('online_registration_active', 'true') === 'true' ? 'selected' : '' ?>>🟢 Aktif (Buka Pendaftaran)</option>
                                        <option value="false" <?= clinic_setting('online_registration_active', 'true') === 'false' ? 'selected' : '' ?>>🔴 Non-Aktif (Tutup Pendaftaran)</option>
                                    </select>
                                    <small class="text-muted text-xs">Pilih Non-Aktif jika ingin menutup pendaftaran online secara manual sewaktu-waktu.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Jam Buka Pendaftaran Online (WITA):</label>
                                    <input type="time" name="settings[online_registration_open_time]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('online_registration_open_time', '06:00')) ?>">
                                    <small class="text-muted text-xs">Pendaftaran otomatis dibuka mulai jam ini.</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Jam Tutup Pendaftaran Online (WITA):</label>
                                    <input type="time" name="settings[online_registration_close_time]" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('online_registration_close_time', '21:00')) ?>">
                                    <small class="text-muted text-xs">Pendaftaran otomatis ditutup setelah jam ini.</small>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Pesan Pemberitahuan Saat Pendaftaran Tutup:</label>
                                <textarea name="settings[online_registration_closed_message]" class="form-control form-control-sm" rows="3"><?= esc(clinic_setting('online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.')) ?></textarea>
                                <small class="text-muted text-xs">Pesan ini akan otomatis ditampilkan kepada pasien di website saat pendaftaran ditutup.</small>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 7: SUARA & NOTIFIKASI PANGGILAN -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="voice" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Konfigurasi Suara Panggilan Antrean & Nada Notifikasi</h6>
                                    <small class="text-muted">Kustomisasi karakter suara (pria/wanita bahasa Indonesia), melodi chime rumah sakit, kecepatan, nada bicara, serta live test preview.</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Pilihan Karakter Suara -->
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Karakter Suara Panggilan (Voice Gender): <span class="text-danger">*</span></label>
                                    <select name="settings[voice_gender]" id="voice-setting-gender" class="form-control form-control-sm font-weight-bold">
                                        <option value="female" <?= clinic_setting('voice_gender', 'female') === 'female' ? 'selected' : '' ?>>👩 Suara Perempuan (Ramah, Hangat, & Jelas - Rekomendasi)</option>
                                        <option value="male" <?= clinic_setting('voice_gender', 'female') === 'male' ? 'selected' : '' ?>>👨 Suara Laki-Laki (Tegas, Berwibawa, & Jelas)</option>
                                        <option value="auto" <?= clinic_setting('voice_gender', 'female') === 'auto' ? 'selected' : '' ?>>🤖 Otomatis Sistem (Default Bahasa Indonesia Browser)</option>
                                    </select>
                                    <small class="text-muted text-xs">Sistem otomatis mendeteksi speech synthesizer Bahasa Indonesia pada perangkat/browser aktif.</small>
                                </div>

                                <!-- Pilihan Nada Notifikasi / Chime -->
                                <div class="col-md-6 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Melodi Nada Notifikasi / Chime Panggilan: <span class="text-danger">*</span></label>
                                    <select name="settings[voice_chime_type]" id="voice-setting-chime" class="form-control form-control-sm font-weight-bold">
                                        <option value="hospital_2tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'hospital_2tone' ? 'selected' : '' ?>>🔔 Chime Rumah Sakit 2-Tone (Klasik E5 &rarr; C5 - Standar RS)</option>
                                        <option value="modern_3tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'modern_3tone' ? 'selected' : '' ?>>🎵 Chime Modern 3-Tone (Harmonik C5 &rarr; E5 &rarr; G5)</option>
                                        <option value="airport_4tone" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'airport_4tone' ? 'selected' : '' ?>>📢 Ding-Dong Bandara 4-Tone (Mayor C5 &rarr; E5 &rarr; G5 &rarr; C6)</option>
                                        <option value="soft_bell" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'soft_bell' ? 'selected' : '' ?>>🛎️ Soft Bell / Ting Lembut (F5 Minimalis Poliklinik)</option>
                                        <option value="none" <?= clinic_setting('voice_chime_type', 'hospital_2tone') === 'none' ? 'selected' : '' ?>>🔕 Tanpa Chime (Langsung Suara Panggilan)</option>
                                    </select>
                                    <small class="text-muted text-xs">Dibangkitkan secara sintetis beresolusi tinggi tanpa jeda buffering file audio.</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Kecepatan Bicara -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Kecepatan Artikulasi (Speed / Rate):</label>
                                    <select name="settings[voice_rate]" id="voice-setting-rate" class="form-control form-control-sm font-weight-bold">
                                        <option value="0.75" <?= clinic_setting('voice_rate', '0.85') == '0.75' ? 'selected' : '' ?>>0.75x &mdash; Lambat (Sangat Jelas & Santai)</option>
                                        <option value="0.85" <?= clinic_setting('voice_rate', '0.85') == '0.85' ? 'selected' : '' ?>>0.85x &mdash; Standar Pelayanan Medis (Rekomendasi)</option>
                                        <option value="0.95" <?= clinic_setting('voice_rate', '0.85') == '0.95' ? 'selected' : '' ?>>0.95x &mdash; Sedang Normal</option>
                                        <option value="1.0" <?= clinic_setting('voice_rate', '0.85') == '1.0' ? 'selected' : '' ?>>1.00x &mdash; Kecepatan Penuh Natural</option>
                                    </select>
                                </div>

                                <!-- Pitch / Tinggi Rendah Suara -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Tinggi-Rendah Nada Suara (Pitch):</label>
                                    <select name="settings[voice_pitch]" id="voice-setting-pitch" class="form-control form-control-sm font-weight-bold">
                                        <option value="0.85" <?= clinic_setting('voice_pitch', '1.0') == '0.85' ? 'selected' : '' ?>>0.85 &mdash; Bass / Berat & Tegas</option>
                                        <option value="1.0" <?= clinic_setting('voice_pitch', '1.0') == '1.0' ? 'selected' : '' ?>>1.00 &mdash; Nada Natural Seimbang</option>
                                        <option value="1.15" <?= clinic_setting('voice_pitch', '1.0') == '1.15' ? 'selected' : '' ?>>1.15 &mdash; Tinggi / Halus & Ceria</option>
                                    </select>
                                </div>

                                <!-- Volume Suara -->
                                <div class="col-md-4 form-group mb-3">
                                    <label class="text-xs font-weight-bold">Volume Output Suara:</label>
                                    <select name="settings[voice_volume]" id="voice-setting-volume" class="form-control form-control-sm font-weight-bold">
                                        <option value="1.0" <?= clinic_setting('voice_volume', '1.0') == '1.0' ? 'selected' : '' ?>>100% &mdash; Maksimal Jernih</option>
                                        <option value="0.8" <?= clinic_setting('voice_volume', '1.0') == '0.8' ? 'selected' : '' ?>>80% &mdash; Sedang Optimal</option>
                                        <option value="0.6" <?= clinic_setting('voice_volume', '1.0') == '0.6' ? 'selected' : '' ?>>60% &mdash; Lembut / Tenang</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Live Playground Testing Box -->
                            <div class="card p-3 border rounded shadow-none bg-light mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-weight-bold text-dark text-xs"><i class="fas fa-play-circle text-teal mr-1"></i> Uji Coba Suara & Nada Panggilan Langsung (Live Audio Preview)</span>
                                    <span class="badge badge-pill badge-info text-xs font-weight-normal px-2 py-1"><i class="fas fa-headphones mr-1"></i> Tes Langsung di Speaker Anda</span>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="text" id="voice-test-text" class="form-control form-control-sm font-weight-bold text-teal" value="Nomor antrean A, nol nol satu, atas nama Budi Santoso, silakan menuju ke Loket Pelayanan Farmasi. Terima kasih.">
                                    <small class="text-muted text-xs">Ubah teks contoh di atas jika ingin mendengarkan kombinasi kata lain.</small>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" id="btn-test-full-voice" class="btn btn-teal btn-sm font-weight-bold mr-2 shadow-sm">
                                        <i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)
                                    </button>
                                    <button type="button" id="btn-test-chime-only" class="btn btn-outline-info btn-sm font-weight-bold mr-2 shadow-sm">
                                        <i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Nada (Chime Saja)
                                    </button>
                                    <button type="button" id="btn-stop-audio-test" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-sm">
                                        <i class="fas fa-stop mr-1"></i> Hentikan Audio
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- TAB 8: INTEGRASI SATUSEHAT KEMENKES RI (HL7 FHIR R4) -->
                        <!-- ========================================================================= -->
                        <div class="tab-pane fade" id="satusehat" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-heart-pulse text-danger mr-1"></i> Interoperabilitas SATUSEHAT Kemenkes RI
                                    </h6>
                                    <small class="text-muted">Standarisasi data Rekam Medis Elektronik (RME) berbasis HL7 FHIR R4 sesuai amanat Permenkes No. 24 Tahun 2022.</small>
                                </div>
                                <span class="badge badge-pill badge-light border text-danger px-3 py-1 font-weight-bold">
                                    <i class="fas fa-certificate mr-1"></i> HL7 FHIR R4
                                </span>
                            </div>

                            <div class="alert alert-light border border-info shadow-xs p-3 mb-4 rounded">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-circle-info text-info fa-2x mr-3 mt-1"></i>
                                    <div>
                                        <strong class="text-dark d-block mb-1">Mekanisme Bridging Cerdas (Outbound HTTPS & Mock Sandbox Engine)</strong>
                                        <p class="text-muted text-xs mb-1">
                                            Sistem berkomunikasi via <strong>Outbound HTTPS Request</strong> sehingga dapat berjalan di <strong>XAMPP Lokal</strong> maupun <strong>Hosting Production</strong>.
                                        </p>
                                        <p class="text-muted text-xs mb-0">
                                            <span class="badge badge-success mr-1"><i class="fas fa-check"></i> Siap Pakai</span>
                                            Pada mode <strong>Mock Sandbox</strong>, sistem langsung memvalidasi payload HL7 FHIR R4 dengan status respon 201 Created instan.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-7 mb-4">
                                    <div class="card h-100 border shadow-none">
                                        <div class="card-header bg-light py-2 px-3">
                                            <h6 class="font-weight-bold text-dark mb-0 text-sm">
                                                <i class="fas fa-key text-warning mr-1"></i> Kredensial & Parameter Otentikasi OAuth2
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-group mb-3 pb-2 border-bottom">
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden" name="settings[satusehat_active]" value="0">
                                                    <input type="checkbox" class="custom-control-input" id="satusehat_active" name="settings[satusehat_active]" value="1" <?= in_array(clinic_setting('satusehat_active', '1'), ['1', 'true', 'on', 1]) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label font-weight-bold text-dark" for="satusehat_active">
                                                        Aktifkan Bridging SATUSEHAT Kemenkes RI
                                                    </label>
                                                </div>
                                            </div>

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
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Organization ID Faskes (Kemenkes): <span class="text-danger">*</span></label>
                                                <input type="text" name="settings[satusehat_org_id]" id="satusehat_org_id" class="form-control form-control-sm font-weight-bold" value="<?= esc(clinic_setting('satusehat_org_id', '10000004')) ?>" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Client ID OAuth2: <span class="text-danger">*</span></label>
                                                <input type="text" name="settings[satusehat_client_id]" id="satusehat_client_id" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_client_id', 'MOCK-CLIENT-ID-SAWAMAWA')) ?>" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="text-xs font-weight-bold text-dark">Client Secret OAuth2: <span class="text-danger">*</span></label>
                                                <input type="password" name="settings[satusehat_client_secret]" id="satusehat_client_secret" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_client_secret', 'MOCK-CLIENT-SECRET-SAWAMAWA')) ?>" required>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="text-xs font-weight-bold text-dark">OAuth2 Auth URL:</label>
                                                <input type="text" name="settings[satusehat_auth_url]" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_auth_url', 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1')) ?>">
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="text-xs font-weight-bold text-dark">FHIR R4 Base URL:</label>
                                                <input type="text" name="settings[satusehat_fhir_url]" class="form-control form-control-sm" value="<?= esc(clinic_setting('satusehat_fhir_url', 'https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

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

                                            <button type="button" class="btn btn-teal btn-block font-weight-bold shadow-xs py-2 mb-3" id="btn-ping-satusehat">
                                                <i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)
                                            </button>

                                            <div id="satusehat-ping-result" class="d-none"></div>

                                            <hr class="my-3">
                                            <div class="alert alert-light border text-xs text-muted mb-0 p-2">
                                                <i class="fas fa-shield-alt text-teal mr-1"></i> Mendukung Resource: <strong>Patient, Encounter, Condition (ICD-10), Observation (TTV)</strong>.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="card-footer bg-light text-right p-3">
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Seluruh Konfigurasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash && typeof jQuery !== 'undefined') {
            $('#settingsTab a[href="' + window.location.hash + '"]').tab('show');
        }
        if (typeof jQuery !== 'undefined') {
            $('#settingsTab a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                if (history.pushState) {
                    history.pushState(null, null, e.target.hash);
                } else {
                    window.location.hash = e.target.hash;
                }
            });
        }

        // Live Audio Playground Listeners
        const btnTestFull = document.getElementById('btn-test-full-voice');
        const btnTestChime = document.getElementById('btn-test-chime-only');
        const btnStopAudio = document.getElementById('btn-stop-audio-test');

        if (btnTestFull) {
            btnTestFull.addEventListener('click', function() {
                if (!window.VCM) return;
                window.VCM.unlockAudio();
                
                const gender = document.getElementById('voice-setting-gender')?.value || 'female';
                const chimeType = document.getElementById('voice-setting-chime')?.value || 'hospital_2tone';
                const rate = parseFloat(document.getElementById('voice-setting-rate')?.value || '0.85');
                const pitch = parseFloat(document.getElementById('voice-setting-pitch')?.value || '1.0');
                const volume = parseFloat(document.getElementById('voice-setting-volume')?.value || '1.0');
                const testText = document.getElementById('voice-test-text')?.value || '';

                btnTestFull.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memutar Suara...';
                btnTestFull.disabled = true;

                window.VCM.testVoice({
                    gender: gender,
                    chimeType: chimeType,
                    rate: rate,
                    pitch: pitch,
                    volume: volume,
                    text: testText
                }, function() {
                    btnTestFull.innerHTML = '<i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)';
                    btnTestFull.disabled = false;
                });
            });
        }

        if (btnTestChime) {
            btnTestChime.addEventListener('click', function() {
                if (!window.VCM) return;
                window.VCM.unlockAudio();
                
                const chimeType = document.getElementById('voice-setting-chime')?.value || 'hospital_2tone';
                btnTestChime.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memutar Chime...';
                btnTestChime.disabled = true;

                window.VCM.playChime(chimeType, function() {
                    btnTestChime.innerHTML = '<i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Nada (Chime Saja)';
                    btnTestChime.disabled = false;
                });
            });
        }

        if (btnStopAudio) {
            btnStopAudio.addEventListener('click', function() {
                if (window.VCM) window.VCM.stop();
                if (btnTestFull) {
                    btnTestFull.innerHTML = '<i class="fas fa-volume-high mr-1"></i> 🔊 Uji Suara Panggilan Lengkap (Chime + Suara)';
                    btnTestFull.disabled = false;
                }
                if (btnTestChime) {
                    btnTestChime.innerHTML = '<i class="fas fa-bell mr-1"></i> 🔔 Uji Melodi Nada (Chime Saja)';
                    btnTestChime.disabled = false;
                }
            });
        }

        // SATUSEHAT Live Test Ping & Auth Runner (AJAX)
        const btnPingSatuSehat = document.getElementById('btn-ping-satusehat');
        const satuSehatPingResult = document.getElementById('satusehat-ping-result');

        if (btnPingSatuSehat) {
            btnPingSatuSehat.addEventListener('click', function() {
                btnPingSatuSehat.disabled = true;
                btnPingSatuSehat.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menguji Koneksi...';
                satuSehatPingResult.classList.remove('d-none');
                satuSehatPingResult.innerHTML = '<div class="alert alert-info py-2 px-3 mb-0 text-xs"><i class="fas fa-circle-notch fa-spin mr-1"></i> Mengirim permintaan ke server SATUSEHAT...</div>';

                const csrfInput = document.querySelector('input[name="csrf_test_name"]');
                const formData = new FormData();
                if (csrfInput) {
                    formData.append(csrfInput.name, csrfInput.value);
                }

                fetch('<?= base_url('system/test-satusehat') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    btnPingSatuSehat.disabled = false;
                    btnPingSatuSehat.innerHTML = '<i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)';

                    let alertClass = data.status === 'success' ? 'alert-success' : (data.status === 'warning' ? 'alert-warning' : 'alert-danger');
                    let iconClass = data.status === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning';

                    satuSehatPingResult.innerHTML = `
                        <div class="alert ${alertClass} py-2 px-3 mb-0 text-xs shadow-none border">
                            <div class="d-flex align-items-start">
                                <i class="fas ${iconClass} mr-2 fa-lg mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">${data.status === 'success' ? 'Koneksi SATUSEHAT Berhasil!' : 'Pemberitahuan SATUSEHAT'}</strong>
                                    <div style="white-space: pre-line;">${data.message || 'Respon diterima.'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                })
                .catch(err => {
                    btnPingSatuSehat.disabled = false;
                    btnPingSatuSehat.innerHTML = '<i class="fas fa-bolt mr-1"></i> Uji Koneksi SATUSEHAT (Test Auth & Ping)';
                    satuSehatPingResult.innerHTML = `<div class="alert alert-danger py-2 px-3 mb-0 text-xs"><i class="fas fa-times-circle mr-1"></i> Gagal: ${err.message}</div>`;
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
