<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<style>
    /* ========================================================================= */
    /* FULL-WIDTH SPACIOUS VERTICAL CLINICAL EMR (CLEAN & STREAMLINED SIDEBAR)   */
    /* ========================================================================= */
    
    /* Sticky Sidebar Container (Terkunci & Tidak ikut ter-scroll) */
    .emr-sidebar-sticky {
        position: -webkit-sticky;
        position: sticky;
        top: 75px;
        max-height: calc(100vh - 85px);
        display: flex;
        flex-direction: column;
    }
    .emr-sidebar-card {
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 85px);
        overflow: hidden;
    }
    .emr-sidebar-card .card-header-streamlined {
        flex-shrink: 0;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 12px;
    }
    .sidebar-queue-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 8px 10px;
        background: #f8fafc;
        max-height: calc(100vh - 210px);
        min-height: 300px;
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 #f1f5f9;
    }
    .sidebar-queue-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .sidebar-queue-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .sidebar-queue-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    .sidebar-queue-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* Queue Patient Item Card Styling (Kompak & Bersih) */
    .visit-item {
        border: 1px solid #e2e8f0 !important;
        border-radius: 7px !important;
        background: #ffffff !important;
        transition: all 0.15s ease-in-out;
        padding: 8px 10px !important;
        margin-bottom: 7px !important;
        cursor: pointer;
        display: block;
        text-decoration: none !important;
        box-sizing: border-box !important;
        position: relative !important;
        overflow: visible !important;
    }
    .visit-item:hover {
        background-color: #f8fafc !important;
        border-color: #94a3b8 !important;
    }
    .visit-item.active {
        background-color: #f0fdfa !important;
        border-color: #0d9488 !important;
        border-left: 4px solid #0d9488 !important;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
    }
    .visit-item .dropdown-menu {
        position: absolute !important;
        right: 0 !important;
        top: 100% !important;
        z-index: 1060 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12) !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
    }

    /* Micro Action Buttons in Queue Item */
    .btn-micro-action {
        height: 21px !important;
        line-height: 19px !important;
        padding: 0 5px !important;
        font-size: 10px !important;
        border-radius: 3px !important;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        transition: all 0.15s ease;
    }

    .emr-workspace-card {
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
    }

    /* Top EMR Patient Banner */
    .emr-patient-hero {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }

    /* Vitals Bar */
    .emr-vitals-bar {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 14px;
        margin-top: 10px;
    }
    .vital-chip {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        padding: 5px 10px;
        text-align: center;
        min-width: 95px;
    }

    /* Tab Navigasi RME */
    .emr-nav-tabs {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 16px 0 16px;
    }
    .emr-nav-tabs .nav-link {
        border: none !important;
        color: #64748b !important;
        font-weight: 600 !important;
        background: transparent !important;
        padding: 10px 18px !important;
        font-size: 0.9rem !important;
        border-bottom: 2.5px solid transparent !important;
        transition: all 0.15s ease;
    }
    .emr-nav-tabs .nav-link:hover {
        color: #0d9488 !important;
    }
    .emr-nav-tabs .nav-link.active {
        color: #0d9488 !important;
        border-bottom: 2.5px solid #0d9488 !important;
        background: transparent !important;
        font-weight: 700 !important;
    }

    /* Full-Width Spacious Section Cards */
    .emr-section-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .emr-section-header {
        padding: 10px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
    }
    .emr-section-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        background: #ffffff !important;
        margin-bottom: 20px !important;
        overflow: visible !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03) !important;
    }
    .emr-section-header-s { border-left: 4px solid #10b981; }
    .emr-section-header-o { border-left: 4px solid #0284c7; }
    .emr-section-header-a { border-left: 4px solid #7c3aed; }
    .emr-section-header-p { border-left: 4px solid #ea580c; }
    .emr-section-header-rx { border-left: 4px solid #0d9488; }

    /* e-Resep Prescription Section Spacious & 100% Responsive */
    .prescription-table-wrapper {
        width: 100%;
        overflow: visible !important;
        position: relative;
        border-radius: 8px;
    }
    .prescription-table-wrapper.macos-select-parent-elevated,
    .prescription-table-wrapper:has(.macos-select-wrapper.is-open) {
        overflow: visible !important;
        z-index: 1085 !important;
    }
    #prescription-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        min-width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    #prescription-table th,
    #prescription-table td {
        overflow: visible !important;
        position: relative;
    }
    #prescription-table td.macos-select-parent-elevated,
    #prescription-table tr.macos-select-parent-elevated,
    #prescription-table td:has(.macos-select-wrapper.is-open),
    #prescription-table tr:has(.macos-select-wrapper.is-open) {
        overflow: visible !important;
        z-index: 1085 !important;
    }
    #prescription-table thead th {
        background: #f8fafc;
        font-size: 11.5px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 12px;
        border-bottom: 2px solid #e2e8f0;
    }
    #prescription-table tbody tr {
        background: #ffffff;
        transition: background 0.15s ease;
    }
    #prescription-table tbody tr:hover {
        background: #fbfdff;
    }
    #prescription-table td {
        padding: 10px 10px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        position: relative;
    }
    #prescription-table .macos-select-wrapper {
        min-width: 260px;
        width: 100%;
    }
    #prescription-table .macos-select-menu {
        min-width: 100% !important;
        max-width: 480px !important;
        max-height: 320px !important;
        z-index: 1090 !important;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.2) !important;
        border: 1px solid rgba(0, 0, 0, 0.14) !important;
    }
    #prescription-table .macos-select-options-list {
        max-height: 260px !important;
    }

    .triage-compact-badge {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 7px 12px;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .form-control-spacious {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 13.5px;
        line-height: 1.5;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .form-control-spacious:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 0.2rem rgba(13, 148, 136, 0.15);
    }

    .sticky-submit-bar {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
</style>

<div class="row">
    <!-- ========================================================================= -->
    <!-- KOLOM KIRI: DAFTAR ANTREAN KONSULTASI PASIEN (STICKY & STREAMLINED)       -->
    <!-- ========================================================================= -->
    <div class="col-lg-3 col-md-4 mb-3">
        <div class="emr-sidebar-sticky">
            <div class="card emr-sidebar-card">
                
                <!-- Header Toolbar Ringkas & Tidak Menumpuk -->
                <div class="card-header-streamlined">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users-line text-teal mr-1"></i>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 14px;">Antrean Pasien</h6>
                        </div>
                        <span class="badge badge-teal font-weight-bold px-2 py-1" id="lbl-total-queue" style="font-size: 11px;"><?= count($visits) ?> Pasien</span>
                    </div>
                    
                    <!-- Search Input Ringkas -->
                    <div class="input-group input-group-sm mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0" style="padding: 4px 8px;"><i class="fas fa-search text-muted" style="font-size: 11px;"></i></span>
                        </div>
                        <input type="text" id="search-queue-input" class="form-control form-control-sm border-left-0" placeholder="Cari nama / RM / antrean...">
                    </div>

                    <!-- Filter Dokter Dropdown Bersih -->
                    <div class="d-flex align-items-center">
                        <small class="text-muted mr-1" style="font-size: 11px; white-space: nowrap;"><i class="fas fa-user-doctor text-secondary mr-1"></i>Dokter:</small>
                        <select id="filter-doctor-select" class="form-control form-control-sm border" style="font-size: 11.5px; height: 28px; padding: 2px 6px;" onchange="window.location.href = '<?= (strpos(current_url(), 'rekam-medis') !== false) ? base_url('klinik/rekam-medis') : base_url('klinik/soap') ?>?doctor_id=' + this.value;">
                            <option value="all" <?= (empty($selected_doctor_id) || $selected_doctor_id === 'all') ? 'selected' : '' ?>>Semua Dokter</option>
                            <?php if (!empty($doctors)): foreach ($doctors as $doc): ?>
                                <option value="<?= $doc->id ?>" <?= ($selected_doctor_id == $doc->id) ? 'selected' : '' ?>>
                                    Dr. <?= esc($doc->name) ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Scroll List Antrean Pasien -->
                <div class="sidebar-queue-scroll" id="queue-list-container">
                    <div id="queue-list">
                        <?php if (empty($visits)): ?>
                            <div class="text-center text-muted p-4">
                                <i class="fas fa-user-clock fa-2x mb-2 text-secondary"></i>
                                <p class="mb-0 text-sm">Tidak ada antrean konsultasi aktif untuk filter ini.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($visits as $idx => $v): 
                                $hasTriage = isset($triages[$v->id]);
                                $tData = $hasTriage ? $triages[$v->id] : null;
                            ?>
                                <?php 
                                    $vStatus = strtolower($v->status ?? 'waiting');
                                    $vBadge = [
                                        'waiting'   => ['secondary', 'Menunggu'],
                                        'called'    => ['primary', 'Dipanggil'],
                                        'examining' => ['info', 'Diperiksa'],
                                        'triage'    => ['warning', 'Triage']
                                    ][$vStatus] ?? ['secondary', strtoupper($vStatus)];
                                ?>
                                <div class="visit-item" 
                                   data-id="<?= $v->id ?>" 
                                   data-name="<?= esc($v->patient_name) ?>" 
                                   data-rm="<?= esc($v->no_rm) ?>" 
                                   data-allergies="<?= esc($v->patient_allergies ?? '') ?>"
                                   data-poly="<?= esc($v->polyclinic_name ?? '-') ?>" 
                                   data-tindakan="<?= esc($v->tindakan_name ?? '-') ?>" 
                                   data-parent="<?= esc($v->parent_tindakan_name ?? '') ?>" 
                                   data-doctor="<?= esc($v->doctor_name ?? 'Dokter Jaga') ?>" 
                                   data-patientid="<?= $v->patient_id ?>"
                                   data-queue="<?= esc($v->queue_no ?? '') ?>"
                                   data-hastriage="<?= $hasTriage ? '1' : '0' ?>"
                                   data-satusehat="<?= !empty($v->satusehat_encounter_id) ? '1' : '0' ?>"
                                   data-encounterid="<?= esc($v->satusehat_encounter_id ?? '') ?>">
                                     
                                     <!-- Baris 1: Urutan, No Antrean, Nama Pasien & Status -->
                                     <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                         <div class="d-flex align-items-center text-truncate mr-1">
                                             <span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size:9px; padding: 2px 4px;" title="Urutan Pendaftar #<?= esc($v->arrival_seq ?? ($idx+1)) ?>">#<?= esc($v->arrival_seq ?? ($idx+1)) ?></span>
                                             <span class="badge badge-teal mr-1 font-weight-bold text-white" style="font-size:10px; padding: 2px 5px;"><?= esc($v->queue_no ?? '-') ?></span>
                                             <strong class="text-dark patient-name-text text-truncate" style="max-width: 135px; font-size: 12px;" title="<?= esc($v->patient_name) ?>"><?= esc($v->patient_name) ?></strong>
                                             <?php if (!empty($v->satusehat_encounter_id)): ?>
                                                 <i class="fas fa-heart-circle-check text-success ml-1 icon-ss-synced" title="Tersinkron SATUSEHAT" style="font-size:11px;"></i>
                                             <?php endif; ?>
                                         </div>
                                         <span class="badge badge-<?= $vBadge[0] ?> font-weight-bold" style="font-size: 8.5px; padding: 2px 4px;"><?= strtoupper($vBadge[1]) ?></span>
                                     </div>

                                     <!-- Baris 2: Layanan Medis & No RM & Status TTV -->
                                     <div class="d-flex justify-content-between align-items-center text-xs text-secondary mt-1">
                                         <div class="text-truncate mr-1" style="max-width: 140px;">
                                             <?php if (!empty($v->tindakan_name)): ?>
                                                 <span class="badge badge-info py-0 px-1 font-weight-normal" style="font-size:9px;"><i class="fas fa-syringe mr-1"></i><?= esc($v->tindakan_name) ?></span>
                                             <?php else: ?>
                                                 <span class="badge badge-light border text-teal py-0 px-1 font-weight-bold" style="font-size:9px;"><i class="fas fa-stethoscope mr-1"></i><?= esc($v->polyclinic_name ?? 'Umum') ?></span>
                                             <?php endif; ?>
                                             <span class="text-muted ml-1" style="font-size: 9.5px;"><?= esc($v->no_rm) ?></span>
                                         </div>
                                         <div>
                                             <?php if ($hasTriage): ?>
                                                 <span class="badge badge-success py-0 px-1" style="font-size: 8.5px;"><i class="fas fa-check mr-1"></i>TTV Ok</span>
                                             <?php else: ?>
                                                 <span class="badge badge-warning text-dark py-0 px-1" style="font-size: 8.5px;"><i class="fas fa-heartbeat mr-1"></i>Belum TTV</span>
                                             <?php endif; ?>
                                         </div>
                                     </div>

                                     <!-- Baris 3: Jam Kedatangan & Tombol Panggil Aksi -->
                                     <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="border-top-color: #f1f5f9 !important;">
                                         <small class="text-muted" style="font-size: 10px;">
                                             <i class="fas fa-user-doctor text-secondary mr-1"></i><?= esc($v->doctor_name ?? 'Dokter Jaga') ?>
                                         </small>
                                         
                                         <div class="d-flex align-items-center" style="gap: 3px;">
                                             <button type="button" class="btn btn-teal btn-xs py-0 px-2 font-weight-bold btn-sidebar-call-voice" title="Panggil Pasien ke Ruang Periksa Dokter" style="font-size: 10px; height: 22px; line-height: 20px; border-radius: 4px;">
                                                 <i class="fas fa-volume-high mr-1"></i> Panggil
                                             </button>
                                             <button type="button" class="btn btn-outline-warning text-dark btn-xs py-0 px-2 font-weight-bold btn-sidebar-start-ttv" title="Panggil ke Ruang TTV Perawat" style="font-size: 10px; height: 22px; line-height: 20px; border-radius: 4px;">
                                                 <i class="fas fa-heartbeat mr-1"></i> TTV
                                             </button>
                                             <button type="button" class="btn btn-outline-danger btn-xs py-0 px-1 font-weight-bold btn-sidebar-cancel-queue" data-id="<?= $v->id ?>" data-name="<?= esc($v->patient_name) ?>" data-queue="<?= esc($v->queue_no) ?>" title="Batalkan antrean konsultasi" style="font-size: 10px; height: 22px; line-height: 20px; border-radius: 4px;">
                                                 <i class="fas fa-times"></i>
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- KOLOM KANAN: WORKSPACE EMR (SPACIOUS FULL-WIDTH VERTICAL DOCUMENT)        -->
    <!-- ========================================================================= -->
    <div class="col-lg-9 col-md-8">
        
        <!-- Panel Selamat Datang saat Belum Ada Pasien Dipilih -->
        <div class="card emr-workspace-card p-5 text-center" id="welcome-pane">
            <div class="my-5">
                <i class="fas fa-stethoscope text-teal" style="font-size: 48px;"></i>
                <h4 class="text-dark font-weight-bold mt-3">Rekam Medis Elektronik (RME) &amp; SOAP</h4>
                <p class="text-secondary mx-auto mb-4" style="max-width: 500px; font-size: 14px;">
                    Pilih salah satu antrean pasien di sebelah kiri atau klik tombol <strong>Panggil</strong> untuk membuka lembar kerja pemeriksaan dokter.
                </p>
                <div class="d-inline-flex align-items-center p-2 rounded bg-light border text-xs text-muted">
                    <i class="fas fa-shield-alt text-teal mr-2"></i> Rekam Medis terintegrasi ICD-10, ICD-9, e-Resep Apotek, dan Kasir.
                </div>
            </div>
        </div>

        <!-- Kartu Kerja Utama Pasien Terpilih -->
        <div class="card emr-workspace-card" id="workspace-card" style="display:none;">
            
            <!-- Header Ringkasan Identitas Pasien Terpilih (EMR Hero Card) -->
            <div class="emr-patient-hero">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge badge-dark mr-2 font-weight-bold px-2 py-1" id="lbl-queue" style="font-size: 13px;">-</span>
                            <h5 class="font-weight-bold text-dark mb-0 mr-2" id="lbl-patient">-</h5>
                            <span class="badge badge-light border text-muted font-weight-bold" id="lbl-rm">-</span>
                        </div>
                        <div class="text-xs text-secondary d-flex align-items-center flex-wrap">
                            <span id="lbl-poly" class="mr-3"></span>
                            <span><i class="fas fa-user-doctor text-teal mr-1"></i> Dokter PJ: <strong id="lbl-doctor" class="text-dark">-</strong></span>
                        </div>
                        <div class="mt-1" id="box-patient-allergies" style="display:none;">
                            <span class="badge badge-danger px-2 py-1 font-weight-bold shadow-sm" style="font-size:11px;">
                                <i class="fas fa-allergies mr-1"></i> RIWAYAT ALERGI: <span id="lbl-patient-allergies"></span>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                        <!-- SATUSEHAT Hub Controls -->
                        <div id="box-satusehat-header" class="d-inline-flex align-items-center mr-1" style="gap: 4px;">
                            <span id="badge-satusehat-synced" class="badge badge-success px-2 py-1 font-weight-bold shadow-xs" style="display:none; font-size:11px;">
                                <i class="fas fa-check-circle mr-1"></i> SATUSEHAT OK
                            </span>
                            <button type="button" class="btn btn-sm btn-info text-white font-weight-bold shadow-xs" id="btn-header-sync-satusehat" title="Kirim data rekam medis ke SATUSEHAT Kemenkes RI">
                                <i class="fas fa-cloud-arrow-up mr-1"></i> Kirim SATUSEHAT
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold shadow-xs" id="btn-header-view-satusehat" title="Lihat Riwayat & Payload FHIR R4" style="display:none;">
                                <i class="fas fa-file-code mr-1"></i> Log FHIR
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-teal font-weight-bold shadow-sm" id="btn-header-call-doctor" title="Panggil pasien ke ruang periksa dokter">
                            <i class="fas fa-volume-high mr-1"></i> Panggil Pasien
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark font-weight-bold" id="btn-header-call-ttv" title="Panggil ke ruang TTV perawat">
                            <i class="fas fa-heartbeat mr-1"></i> Panggil TTV
                        </button>
                    </div>
                </div>

                <!-- Strip TTV Ringkas Pasien Terkini -->
                <div class="emr-vitals-bar">
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                        <span class="text-xs font-weight-bold text-dark">
                            <i class="fas fa-heartbeat text-danger mr-1"></i> Tanda-Tanda Vital (TTV Pasien Terkini)
                        </span>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-xs btn-outline-secondary mr-1" id="btn-copy-nurse-o" title="Salin TTV ke Catatan Fisik">
                                <i class="fas fa-copy mr-1 text-info"></i> Salin TTV
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-edit-triage-from-soap" title="Buka Form TTV Perawat">
                                <i class="fas fa-edit mr-1"></i> Edit TTV
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap text-xs pt-1" style="gap: 5px;">
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">Tekanan Darah</small>
                            <strong class="text-dark" id="live-o-bp">-</strong> <small class="text-muted">mmHg</small>
                        </div>
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">Suhu Tubuh</small>
                            <strong class="text-dark" id="live-o-temp">-</strong> <small class="text-muted">&deg;C</small>
                        </div>
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">Denyut Nadi</small>
                            <strong class="text-dark" id="live-o-pulse">-</strong> <small class="text-muted">bpm</small>
                        </div>
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">Pernapasan</small>
                            <strong class="text-dark" id="live-o-resp">-</strong> <small class="text-muted">x/m</small>
                        </div>
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">BB / TB</small>
                            <strong class="text-dark" id="live-o-bbtb">-</strong>
                        </div>
                        <div class="vital-chip flex-fill">
                            <small class="text-muted d-block" style="font-size:10px;">IMT / Status</small>
                            <strong class="text-dark" id="live-o-imt">-</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigasi RME -->
            <div class="emr-nav-tabs">
                <ul class="nav nav-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-btn-soap" href="javascript:void(0)">
                            <i class="fas fa-stethoscope mr-1 text-teal"></i> Lembar Pemeriksaan Dokter (SOAP &amp; Resep)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-btn-triage" href="javascript:void(0)">
                            <i class="fas fa-heartbeat mr-1 text-danger"></i> Skrining TTV Perawat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-btn-history" href="javascript:void(0)">
                            <i class="fas fa-history mr-1 text-info"></i> Riwayat Rekam Medis (EHR)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-btn-odontogram" href="javascript:void(0)">
                            <i class="fas fa-tooth mr-1 text-warning"></i> Odontogram Gigi
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-3 p-md-4">
                
                <!-- ============================================================= -->
                <!-- 1. TAB: PEMERIKSAAN DOKTER (FULL-WIDTH VERTICAL DOCUMENT)     -->
                <!-- ============================================================= -->
                <div id="soap-pane">
                    <form action="<?= current_url() ?>" method="post" id="form-soap-doctor">
                        <input type="hidden" name="action" value="save_soap">
                        <input type="hidden" name="visit_id" class="input-visit-id">
                        <?= csrf_field() ?>

                        <!-- Quick Clinical Presets Toolbar (Template Cepat SOAP Dokter) -->
                        <?php if (!empty($clinicalTemplates)): ?>
                            <div class="card mb-3 shadow-none border bg-white" style="border: 1.5px solid #0d9488 !important; border-radius: 8px;">
                                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                                    <div>
                                        <strong class="text-teal" style="font-size: 13px;"><i class="fas fa-bolt text-warning mr-1"></i> Template Klinis Cepat (Quick Clinical Presets):</strong>
                                        <small class="text-muted d-block" style="font-size: 11px;">Pilih salah satu template untuk auto-fill S, O, A, Kode ICD-10, dan P seketika.</small>
                                    </div>
                                    <span class="badge badge-teal px-2 py-1"><i class="fas fa-magic mr-1"></i> 1-Klik Auto-Fill</span>
                                </div>
                                <div class="card-body p-2 d-flex flex-wrap" style="gap: 5px;">
                                    <?php foreach ($clinicalTemplates as $tpl): ?>
                                        <button type="button" class="btn btn-outline-<?= esc($tpl['badge']) ?> btn-xs font-weight-bold btn-clinical-tpl shadow-none py-1 px-2"
                                                data-id="<?= esc($tpl['id']) ?>"
                                                data-name="<?= esc($tpl['name']) ?>"
                                                data-s="<?= esc($tpl['subjective']) ?>"
                                                data-o="<?= esc($tpl['objective']) ?>"
                                                data-a="<?= esc($tpl['assessment']) ?>"
                                                data-icd="<?= esc($tpl['icd10_code']) ?>"
                                                data-p="<?= esc($tpl['plan']) ?>"
                                                data-med="<?= esc($tpl['med_keyword']) ?>">
                                            <i class="fas fa-notes-medical mr-1"></i> <?= esc($tpl['name']) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- [ S ] 1. ANAMNESIS & KELUHAN PASIEN (SUBJECTIVE) - FULL WIDTH -->
                        <div class="emr-section-card">
                            <div class="emr-section-header emr-section-header-s">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <span class="badge badge-success px-2 py-1 mr-1">S</span> 
                                        <strong>1. Anamnesis &amp; Keluhan Pasien (Subjective)</strong>
                                    </h6>
                                    <small class="text-muted">Keluhan utama, riwayat penyakit sekarang (RPS), riwayat penyakit dahulu (RPD), riwayat alergi obat/makanan.</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-copy-nurse-s" title="Salin Keluhan Perawat">
                                    <i class="fas fa-copy mr-1 text-teal"></i> Salin Catatan Perawat
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="triage-compact-badge d-flex justify-content-between align-items-center" id="nurse-s-live-box">
                                    <div class="text-truncate mr-2">
                                        <strong class="text-dark"><i class="fas fa-user-nurse text-teal mr-1"></i> Skrining Perawat (Triage):</strong> <span id="live-s-complaints" class="text-secondary">-</span>
                                    </div>
                                    <span class="badge badge-light border text-muted" style="font-size:9.5px;">Live Triage</span>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="text-xs font-weight-bold text-dark">Catatan Anamnesis Dokter (S) <span class="text-danger">*</span></label>
                                    <textarea name="subjective" id="doc-subjective" class="form-control form-control-spacious" rows="3" placeholder="Tuliskan hasil wawancara keluhan utama, RPS, RPD, alergi obat/makanan dengan leluasa di sini..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- [ O ] 2. PEMERIKSAAN FISIK DOKTER (OBJECTIVE) - FULL WIDTH -->
                        <div class="emr-section-card">
                            <div class="emr-section-header emr-section-header-o">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <span class="badge badge-info px-2 py-1 mr-1">O</span> 
                                        <strong>2. Hasil Pemeriksaan Fisik Langsung (Objective)</strong>
                                    </h6>
                                    <small class="text-muted">Pemeriksaan fisik langsung oleh dokter (Kepala, Leher, Thoraks, Cor/Pulmo, Abdomen, Ekstremitas, Status Lokalis).</small>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-group mb-0">
                                    <label class="text-xs font-weight-bold text-dark">Hasil Pemeriksaan Fisik (O) <span class="text-danger">*</span></label>
                                    <textarea name="objective" id="doc-objective" class="form-control form-control-spacious" rows="3" placeholder="Tuliskan hasil pemeriksaan fisik umum dan status lokalis pasien..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- [ A ] 3. DIAGNOSA MEDIS & ICD-10 (ASSESSMENT) - FULL WIDTH -->
                        <div class="emr-section-card">
                            <div class="emr-section-header emr-section-header-a">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <span class="badge badge-primary px-2 py-1 mr-1" style="background:#7c3aed;">A</span> 
                                        <strong>3. Diagnosa Medis Kerja &amp; Kode ICD-10 (Assessment)</strong>
                                    </h6>
                                    <small class="text-muted">Diagnosa utama medis kerja, diagnosa diferensial, serta klasifikasi standar ICD-10 Kemenkes.</small>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Diagnosa Medis Kerja (A) <span class="text-danger">*</span></label>
                                    <textarea name="assessment" id="doc-assessment" class="form-control form-control-spacious" rows="2" placeholder="Tuliskan diagnosa kerja medis utama dan diagnosa banding..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 form-group mb-0">
                                        <label class="text-xs font-weight-bold text-dark">Kode ICD-10</label>
                                        <input type="text" name="icd10_code" id="input-icd10-code" list="icd10-datalist" class="form-control font-weight-bold" placeholder="Ketik Kode (misal: I10)">
                                        <datalist id="icd10-datalist">
                                            <?php if (!empty($masterIcd10)): ?>
                                                <?php foreach ($masterIcd10 as $icd): ?>
                                                    <option value="<?= esc($icd->code) ?>" data-desc="<?= esc($icd->name_id) ?>">
                                                        <?= esc($icd->code) ?> - <?= esc($icd->name_id) ?> (<?= esc($icd->category ?? 'Umum') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </datalist>
                                    </div>
                                    <div class="col-md-8 form-group mb-0">
                                        <label class="text-xs font-weight-bold text-dark">Deskripsi Penyakit ICD-10</label>
                                        <input type="text" name="icd10_desc" id="input-icd10-desc" class="form-control" placeholder="Otomatis terisi dari kode ICD-10...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- [ P ] 4. RENCANA PENATALAKSANAAN & EDUKASI (PLAN) - FULL WIDTH -->
                        <div class="emr-section-card">
                            <div class="emr-section-header emr-section-header-p">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <span class="badge badge-warning text-dark px-2 py-1 mr-1" style="background:#ea580c; color:#fff !important;">P</span> 
                                        <strong>4. Rencana Penatalaksanaan, Tindakan &amp; Edukasi (Plan)</strong>
                                    </h6>
                                    <small class="text-muted">Anjuran istirahat, diet/nutrisi, edukasi pasien, rencana kontrol kembali, prosedur ICD-9, atau tindakan klinik.</small>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Rencana Penatalaksanaan &amp; Edukasi (P) <span class="text-danger">*</span></label>
                                    <textarea name="plan" id="doc-plan" class="form-control form-control-spacious" rows="2" placeholder="Tuliskan rencana terapi, edukasi pasien, pantangan makanan, dan anjuran kontrol..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 form-group mb-3 mb-md-0">
                                        <label class="text-xs font-weight-bold text-dark">Prosedur Medis (ICD-9-CM)</label>
                                        <input type="text" name="icd9_code" id="input-icd9-code" list="icd9-datalist" class="form-control font-weight-bold" placeholder="Ketik Kode (misal: 89.52)">
                                        <datalist id="icd9-datalist">
                                            <?php if (!empty($masterIcd9)): ?>
                                                <?php foreach ($masterIcd9 as $icd): ?>
                                                    <option value="<?= esc($icd->code) ?>" data-desc="<?= esc($icd->name_id) ?>">
                                                        <?= esc($icd->code) ?> - <?= esc($icd->name_id) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </datalist>
                                    </div>
                                    <div class="col-md-4 form-group mb-3 mb-md-0">
                                        <label class="text-xs font-weight-bold text-dark">Deskripsi Prosedur ICD-9</label>
                                        <input type="text" name="icd9_desc" id="input-icd9-desc" class="form-control" placeholder="Otomatis terisi...">
                                    </div>
                                    <div class="col-md-4 form-group mb-0">
                                        <label class="text-xs font-weight-bold text-dark">Tindakan Medis Tambahan:</label>
                                        <select name="service_id" id="doc-service-id" class="form-control select-searchable font-weight-bold" data-search-placeholder="🔍 Cari tindakan tambahan...">
                                            <option value="">-- Tidak ada tindakan tambahan --</option>
                                            <?php 
                                            $groupedServices = [];
                                            foreach ($services as $srv) {
                                                if ($srv->price === null) continue;
                                                $grp = $srv->parent_name ?: 'Tindakan Umum / Standalone';
                                                $groupedServices[$grp][] = $srv;
                                            }
                                            foreach ($groupedServices as $grpName => $items):
                                            ?>
                                                <optgroup label="<?= esc($grpName) ?>">
                                                    <?php foreach ($items as $srv): ?>
                                                        <option value="<?= $srv->id ?>">
                                                            <?= esc($srv->name) ?> (Rp <?= number_format($srv->price, 0, ',', '.') ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. E-RESEP OBAT ELEKTRONIK - FULL WIDTH TABLE -->
                        <div class="emr-section-card">
                            <div class="emr-section-header emr-section-header-rx">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-prescription-bottle-medical text-teal mr-1"></i> 
                                        <strong>5. Resep Obat Elektronik (e-Resep Apotek)</strong>
                                    </h6>
                                    <small class="text-muted">Pilih obat dengan validasi stok apotek internal, penulisan aturan pakai, dan estimasi biaya.</small>
                                </div>
                                <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold" id="add-medicine-row">
                                    <i class="fas fa-plus mr-1"></i> Tambah Baris Obat
                                </button>
                            </div>

                            <div class="card-body p-3" style="overflow: visible !important;">
                                <!-- Drug-Allergy Safety Alert Banner -->
                                <div id="alert-drug-allergy" class="alert alert-danger font-weight-bold mb-3 shadow-sm py-2 px-3 border-danger" style="display:none; font-size:13px; border-left: 5px solid #dc3545 !important;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-virus fa-lg text-danger mr-2"></i>
                                        <div id="msg-drug-allergy">PERINGATAN: Potensi alergi obat terdeteksi!</div>
                                    </div>
                                </div>

                                <div class="prescription-table-wrapper mb-3">
                                    <table class="table mb-0 bg-white" id="prescription-table">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 380px;">Nama Obat &amp; Satuan (Stok Apotek)</th>
                                                <th style="width: 100px;" class="text-center">Jumlah (Qty)</th>
                                                <th style="width: 130px;" class="text-right">Harga Satuan</th>
                                                <th style="width: 130px;" class="text-right">Subtotal</th>
                                                <th style="min-width: 250px;">Dosis &amp; Aturan Pakai (Signa)</th>
                                                <th style="width: 50px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="med-presc-row" data-index="0" id="med-presc-row-0">
                                                <td>
                                                    <select name="medicines[0][medicine_id]" class="form-control form-control-sm select-searchable select-med font-weight-bold" data-index="0" data-search-placeholder="🔍 Ketik nama obat...">
                                                        <option value="" data-price="0" data-unit="" data-stock="0">-- Pilih Obat dari Apotek --</option>
                                                        <?php foreach ($medicines as $m): 
                                                            $stk = (int)($m->total_stock ?? 0);
                                                            $isOut = $stk <= 0;
                                                        ?>
                                                            <option value="<?= $m->id ?>" 
                                                                    data-price="<?= $m->price ?>" 
                                                                    data-unit="<?= esc($m->unit) ?>" 
                                                                    data-stock="<?= $stk ?>"
                                                                    <?= $isOut ? 'data-out="1"' : '' ?>>
                                                                <?= $isOut ? '🔴 [KOSONG: 0 ' . esc($m->unit) . '] ' : '🟢 ' ?><?= esc($m->name) ?> (Stok: <?= $stk ?> <?= esc($m->unit) ?>) - Rp <?= number_format($m->price, 0, ',', '.') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="stock-warning-container mt-1" id="stock-warning-0" style="display:none;"></div>
                                                </td>
                                                <td>
                                                    <input type="number" name="medicines[0][qty]" class="form-control form-control-sm text-center font-weight-bold input-qty" data-index="0" placeholder="Qty" min="1" value="1">
                                                </td>
                                                <td class="text-right text-muted text-xs pt-2 font-weight-bold" id="lbl-unitprice-0">
                                                    Rp 0
                                                </td>
                                                <td class="text-right font-weight-bold text-teal text-xs pt-2" id="lbl-subtotal-0">
                                                    Rp 0
                                                </td>
                                                <td>
                                                    <input type="text" name="medicines[0][dosage]" class="form-control form-control-sm input-dosage" list="dosage-suggestions" placeholder="Contoh: 3 x 1 tablet sesudah makan">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-xs remove-row" title="Hapus"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Datalist Saran Signa / Aturan Pakai Dokter -->
                                <datalist id="dosage-suggestions">
                                    <option value="3 x 1 tablet sesudah makan">
                                    <option value="3 x 1 tablet sebelum makan">
                                    <option value="2 x 1 tablet sesudah makan">
                                    <option value="2 x 1 tablet sebelum makan">
                                    <option value="1 x 1 tablet malam hari">
                                    <option value="1 x 1 tablet pagi hari">
                                    <option value="3 x 1 sendok teh (5 ml) sesudah makan">
                                    <option value="3 x 1 sendok makan (15 ml) sesudah makan">
                                    <option value="1 x 1 tetes pada mata yang sakit">
                                    <option value="2 x 1 oles tipis pada area kulit yang sakit">
                                    <option value="Jika perlu (p.r.n) / saat demam / nyeri hebat">
                                </datalist>

                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 px-3 bg-light border rounded">
                                    <span class="text-muted text-xs mb-1 mb-md-0">
                                        <i class="fas fa-info-circle mr-1"></i> Resep yang disimpan otomatis diteruskan ke antrean farmasi &amp; billing kasir.
                                    </span>
                                    <div class="text-right">
                                        <span class="text-secondary text-xs font-weight-bold">Estimasi Total Biaya Obat:</span>
                                        <span class="h5 font-weight-bold text-teal ml-2 mb-0" id="lbl-grand-medicine-total">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Submit Action Bar -->
                        <div class="sticky-submit-bar">
                            <div>
                                <span class="text-xs text-muted">
                                    <i class="fas fa-check-double text-teal mr-1"></i> Pastikan seluruh data klinis terisi dengan benar.
                                </span>
                            </div>
                            <button type="submit" class="btn btn-teal btn-md font-weight-bold px-4 shadow-sm" id="btn-submit-soap">
                                <i class="fas fa-check-circle mr-1"></i> Simpan SOAP &amp; Arahkan ke Kasir / Farmasi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================================= -->
                <!-- 2. TAB: ASESMEN TTV PERAWAT                                   -->
                <!-- ============================================================= -->
                <div id="triage-pane" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-heartbeat text-teal mr-1"></i> Pengukuran Tanda-Tanda Vital &amp; Skrining Perawat
                            </h6>
                            <small class="text-muted">Skrining awal pasien sebelum masuk ke ruang dokter.</small>
                        </div>
                        <div id="triage-autosave-status">
                            <span class="badge badge-light border text-muted"><i class="fas fa-bolt text-warning mr-1"></i> Auto-sync aktif</span>
                        </div>
                    </div>

                    <!-- Nurse Call Banner -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3 mb-3 rounded bg-light border">
                        <div class="mb-2 mb-md-0">
                            <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-volume-high text-warning mr-1"></i> Panggil Pasien ke Ruang TTV</h6>
                            <small class="text-secondary">Panggil pasien <strong id="triage-call-patient-name" class="text-dark">-</strong> (<span id="triage-call-queue-no" class="badge badge-secondary">-</span>) untuk skrining awal.</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm" id="btn-triage-call-voice">
                                <i class="fas fa-volume-high mr-1"></i> Siarkan Panggilan TTV
                            </button>
                        </div>
                    </div>

                    <form id="form-triage" action="<?= base_url('klinik/soap') ?>" method="post">
                        <input type="hidden" name="action" value="save_triage">
                        <input type="hidden" name="visit_id" class="input-visit-id">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Tekanan Darah (mmHg)</label>
                                <input type="text" name="blood_pressure" id="tr-bp" class="form-control form-control-sm" placeholder="120/80">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Berat Badan (kg)</label>
                                <input type="number" step="0.1" name="weight" id="tr-weight" class="form-control form-control-sm" placeholder="60.0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" name="height" id="tr-height" class="form-control form-control-sm" placeholder="165">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Suhu Tubuh (&deg;C)</label>
                                <input type="number" step="0.1" name="temperature" id="tr-temp" class="form-control form-control-sm" placeholder="36.5">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Denyut Nadi (bpm)</label>
                                <input type="number" name="pulse" id="tr-pulse" class="form-control form-control-sm" placeholder="80">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="text-xs font-weight-bold text-dark">Laju Pernapasan (x/mnt)</label>
                                <input type="number" name="respiration" id="tr-resp" class="form-control form-control-sm" placeholder="18">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Keluhan Utama Pasien</label>
                            <textarea name="complaints" id="tr-complaints" class="form-control form-control-sm" rows="2" placeholder="Keluhan utama saat skrining awal..."></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-xs font-weight-bold text-dark">Anamnesa Awal &amp; Riwayat Alergi</label>
                            <textarea name="anamnesis" id="tr-anamnesis" class="form-control form-control-sm" rows="2" placeholder="Riwayat alergi, riwayat penyakit terdahulu..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-return-to-soap">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Lembar Dokter
                            </button>
                            <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-3">
                                <i class="fas fa-save mr-1"></i> Simpan &amp; Teruskan ke Dokter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================================= -->
                <!-- 3. TAB: RIWAYAT REKAM MEDIS PASIEN (EHR TIMELINE)             -->
                <!-- ============================================================= -->
                <div id="history-pane" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-history text-teal mr-1"></i> Riwayat Rekam Medis Pasien Terdahulu
                            </h6>
                            <small class="text-muted font-weight-normal text-xs">Arsip kunjungan, diagnosa ICD, dan terapi obat masa lalu.</small>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-xs btn-return-to-soap">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke SOAP
                        </button>
                    </div>

                    <div id="patient-history-container">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Memuat riwayat rekam medis...
                        </div>
                    </div>
                </div>

                <!-- ============================================================= -->
                <!-- 4. TAB: ODONTOGRAM POLI GIGI                                  -->
                <!-- ============================================================= -->
                <div id="odontogram-pane" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-tooth text-warning mr-1"></i> Diagram Odontogram Visual (FDI 32 Gigi Standar Kemenkes)
                            </h6>
                            <small class="text-muted font-weight-normal text-xs">Klik nomor gigi untuk mencatat karies, tambalan, atau tindakan gigi.</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-teal btn-xs font-weight-bold mr-1" id="btn-save-all-odontogram">
                                <i class="fas fa-save mr-1"></i> Simpan Odontogram
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-xs btn-return-to-soap">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke SOAP
                            </button>
                        </div>
                    </div>

                    <!-- Indikator Legenda Warna Odontogram -->
                    <div class="p-2 mb-3 bg-light border rounded text-xs d-flex flex-wrap align-items-center justify-content-between">
                        <strong class="text-secondary mr-2"><i class="fas fa-palette mr-1"></i> Legenda Kondisi Gigi:</strong>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#e2e8f0;"></span> Normal</span>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#dc2626;"></span> Karies (C)</span>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#2563eb;"></span> Tambalan (F)</span>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#475569;"></span> Hilang (M)</span>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#eab308;"></span> Mahkota (CR)</span>
                        <span class="badge badge-light border mr-1"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#9333ea;"></span> Sisa Akar (RR)</span>
                        <span class="badge badge-light border"><span class="d-inline-block rounded-circle mr-1" style="width:10px; height:10px; background:#ea580c;"></span> Cabut (EXT)</span>
                    </div>

                    <!-- Interactive Teeth Chart Grid -->
                    <div class="odontogram-chart-wrapper text-center p-3 border rounded mb-3 bg-white" style="border: 1px solid #e2e8f0 !important;">
                        <div class="font-weight-bold text-xs text-secondary mb-2 uppercase tracking-wide">
                            &uarr; RAHANG ATAS (MAXILLA) &uarr;
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-4 flex-wrap">
                            <!-- Kuadran 1 (Kanan Atas: 18 - 11) -->
                            <div class="d-flex justify-content-end mr-2 kuadran-box">
                                <?php for ($t = 18; $t >= 11; $t--): ?>
                                    <div class="tooth-item mx-1 text-center" data-tooth="<?= $t ?>" style="cursor: pointer;">
                                        <div class="tooth-box border rounded p-1" id="tooth-box-<?= $t ?>" style="width: 38px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; transition: all 0.2s;">
                                            <i class="fas fa-tooth tooth-icon" id="tooth-icon-<?= $t ?>" style="font-size: 18px; color: #94a3b8;"></i>
                                            <span class="d-block text-xs font-weight-bold text-dark mt-1" style="font-size: 10px; line-height: 1;"><?= $t ?></span>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="border-right mx-2" style="height: 48px; border-width: 2px !important; border-color: #cbd5e1 !important;"></div>
                            <!-- Kuadran 2 (Kiri Atas: 21 - 28) -->
                            <div class="d-flex justify-content-start ml-2 kuadran-box">
                                <?php for ($t = 21; $t <= 28; $t++): ?>
                                    <div class="tooth-item mx-1 text-center" data-tooth="<?= $t ?>" style="cursor: pointer;">
                                        <div class="tooth-box border rounded p-1" id="tooth-box-<?= $t ?>" style="width: 38px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; transition: all 0.2s;">
                                            <i class="fas fa-tooth tooth-icon" id="tooth-icon-<?= $t ?>" style="font-size: 18px; color: #94a3b8;"></i>
                                            <span class="d-block text-xs font-weight-bold text-dark mt-1" style="font-size: 10px; line-height: 1;"><?= $t ?></span>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="font-weight-bold text-xs text-secondary mb-2 uppercase tracking-wide">
                            &darr; RAHANG BAWAH (MANDIBULA) &darr;
                        </div>
                        <div class="d-flex justify-content-center align-items-center flex-wrap">
                            <!-- Kuadran 4 (Kanan Bawah: 48 - 41) -->
                            <div class="d-flex justify-content-end mr-2 kuadran-box">
                                <?php for ($t = 48; $t >= 41; $t--): ?>
                                    <div class="tooth-item mx-1 text-center" data-tooth="<?= $t ?>" style="cursor: pointer;">
                                        <div class="tooth-box border rounded p-1" id="tooth-box-<?= $t ?>" style="width: 38px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; transition: all 0.2s;">
                                            <span class="d-block text-xs font-weight-bold text-dark mb-1" style="font-size: 10px; line-height: 1;"><?= $t ?></span>
                                            <i class="fas fa-tooth tooth-icon" id="tooth-icon-<?= $t ?>" style="font-size: 18px; color: #94a3b8;"></i>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="border-right mx-2" style="height: 48px; border-width: 2px !important; border-color: #cbd5e1 !important;"></div>
                            <!-- Kuadran 3 (Kiri Bawah: 31 - 38) -->
                            <div class="d-flex justify-content-start ml-2 kuadran-box">
                                <?php for ($t = 31; $t <= 38; $t++): ?>
                                    <div class="tooth-item mx-1 text-center" data-tooth="<?= $t ?>" style="cursor: pointer;">
                                        <div class="tooth-box border rounded p-1" id="tooth-box-<?= $t ?>" style="width: 38px; height: 46px; background: #f8fafc; border: 1px solid #cbd5e1; transition: all 0.2s;">
                                            <span class="d-block text-xs font-weight-bold text-dark mb-1" style="font-size: 10px; line-height: 1;"><?= $t ?></span>
                                            <i class="fas fa-tooth tooth-icon" id="tooth-icon-<?= $t ?>" style="font-size: 18px; color: #94a3b8;"></i>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Form Popup Diagnosa Gigi Terpilih -->
                    <div id="tooth-detail-card" class="card border rounded bg-light p-3 mb-3" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-tooth text-teal mr-1"></i> Edit Kondisi Gigi No. <span id="lbl-selected-tooth" class="badge badge-teal font-weight-bold">-</span>
                            </h6>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-close-tooth-detail">&times; Tutup</button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold">Kondisi Gigi:</label>
                                <select id="sel-tooth-condition" class="form-control form-control-sm">
                                    <option value="normal" data-name="Normal / Sehat">⚪ Normal / Sehat</option>
                                    <option value="caries" data-name="Karies / Berlubang (C)">🔴 Karies / Berlubang (C)</option>
                                    <option value="filling" data-name="Tumpatan / Tambalan (F)">🔵 Tumpatan / Tambalan (F)</option>
                                    <option value="missing" data-name="Hilang / Ompong (M)">⚫ Hilang / Ompong (M)</option>
                                    <option value="crown" data-name="Mahkota Tiruan (CR)">🟡 Mahkota Tiruan (CR)</option>
                                    <option value="radix" data-name="Sisa Akar / Radix (RR)">🟣 Sisa Akar / Radix (RR)</option>
                                    <option value="extract" data-name="Perlu Dicabut (EXT)">🟠 Perlu Dicabut (EXT)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="text-xs font-weight-bold">Catatan Klinis Gigi:</label>
                                <input type="text" id="txt-tooth-notes" class="form-control form-control-sm" placeholder="Catatan karies oklusal, tambalan komposit, dll...">
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-teal btn-xs font-weight-bold px-3" id="btn-apply-tooth">
                                <i class="fas fa-check mr-1"></i> Terapkan ke Diagram
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL AUDIT LOG SATUSEHAT KEMENKES (HL7 FHIR R4)                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="modal-satusehat-logs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-teal text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fas fa-heart-pulse mr-2"></i> Log Audit SATUSEHAT Kemenkes (HL7 FHIR R4)
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="card border mb-3 shadow-none">
                    <div class="card-body p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <strong id="modal-ss-patient-name" class="text-dark d-block" style="font-size:14px;">Pasien</strong>
                                <small class="text-muted" id="modal-ss-patient-info">-</small>
                            </div>
                            <div class="mt-1 mt-md-0">
                                <span class="badge badge-success px-2 py-1 font-weight-bold" id="modal-ss-encounter-badge">Encounter ID: -</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modal-ss-logs-container">
                    <!-- Logs list generated dynamically -->
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-shield-alt text-teal mr-1"></i> Kepatuhan Interoperabilitas Permenkes No. 24 Tahun 2022
                </small>
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC & LIVE SYNC DATA BINDING                                 -->
<!-- ========================================================================= -->
<script>
    const triagesData = <?= json_encode($triages ?? []) ?>;
    window.triagesData = triagesData;
    const patientHistoryData = <?= json_encode($patientHistory ?? []) ?>;
    window.patientHistoryData = patientHistoryData;

    let medicinesOptionsHtml = `
        <option value="" data-price="0" data-unit="" data-stock="0">-- Pilih Obat (Opsional) --</option>
        <?php if (!empty($medicines)): foreach ($medicines as $m): 
            $stk = (int)($m->total_stock ?? 0);
            $isOut = $stk <= 0;
        ?>
            <option value="<?= $m->id ?>" 
                    data-price="<?= $m->price ?>" 
                    data-unit="<?= esc($m->unit) ?>" 
                    data-stock="<?= $stk ?>"
                    <?= $isOut ? 'data-out="1"' : '' ?>>
                <?= $isOut ? '🔴 [KOSONG: 0 ' . esc($m->unit) . '] ' : '🟢 ' ?><?= esc($m->name) ?> (Stok: <?= $stk ?> <?= esc($m->unit) ?>) - Rp <?= number_format($m->price, 0, ',', '.') ?>
            </option>
        <?php endforeach; endif; ?>
    `;

    function calculateMedicineRow(idx) {
        const row = $(`#med-presc-row-${idx}`);
        if (!row.length) return;

        const sel = $(`select[name="medicines[${idx}][medicine_id]"] option:selected`);
        const price = parseFloat(sel.data('price')) || 0;
        const unit = sel.data('unit') || '';
        const stock = parseInt(sel.data('stock')) || 0;
        const isOut = sel.data('out') == '1' || stock <= 0;
        const qty = parseInt($(`input[name="medicines[${idx}][qty]"]`).val()) || 0;
        const medId = sel.val();

        if (medId) {
            $(`#lbl-unitprice-${idx}`).text('Rp ' + price.toLocaleString('id-ID'));
            $(`#lbl-subtotal-${idx}`).text('Rp ' + (price * qty).toLocaleString('id-ID'));

            if (isOut) {
                $(`#stock-warning-${idx}`).show().html(`
                    <small class="text-danger font-weight-bold d-block" style="font-size:11px;">
                        <i class="fas fa-exclamation-circle mr-1"></i> Stok 0 di Apotek Internal. Tetap dapat diresepkan sebagai Resep Luar.
                    </small>
                `);
            } else if (qty > stock) {
                $(`#stock-warning-${idx}`).show().html(`
                    <small class="text-warning font-weight-bold d-block" style="font-size:11px;">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Jumlah (${qty}) melebihi stok internal (${stock}). Sisa akan dialihkan ke Resep Luar.
                    </small>
                `);
            } else {
                $(`#stock-warning-${idx}`).hide().empty();
            }
        } else {
            $(`#lbl-unitprice-${idx}`).text('Rp 0');
            $(`#lbl-subtotal-${idx}`).text('Rp 0');
            $(`#stock-warning-${idx}`).hide().empty();
        }

        let grand = 0;
        $('.med-presc-row').each(function() {
            const rowIdx = $(this).data('index');
            const rowSel = $(`select[name="medicines[${rowIdx}][medicine_id]"] option:selected`);
            const rowStock = parseInt(rowSel.data('stock')) || 0;
            const rowIsOut = rowSel.data('out') == '1' || rowStock <= 0;
            const rowMedId = rowSel.val();

            if (rowMedId && !rowIsOut) {
                const rowPrice = parseFloat(rowSel.data('price')) || 0;
                const rowQty = parseInt($(`input[name="medicines[${rowIdx}][qty]"]`).val()) || 0;
                grand += (rowPrice * rowQty);
            }
        });

        $('#lbl-grand-medicine-total').text('Rp ' + grand.toLocaleString('id-ID'));
    }

    $(document).ready(function() {
        let medicineRowCount = 1;
        const medicinesOptionsHtml = $('#med-presc-row-0 select.select-med').html() || '';

        // Nav Tab Switchers
        $('#tab-btn-soap').click(function() {
            $('.emr-nav-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('#soap-pane').slideDown(150);
            $('#triage-pane, #history-pane, #odontogram-pane').slideUp(150);
        });

        $('#tab-btn-triage').click(function() {
            $('.emr-nav-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('#triage-pane').slideDown(150);
            $('#soap-pane, #history-pane, #odontogram-pane').slideUp(150);
        });

        $('#tab-btn-history').click(function() {
            $('.emr-nav-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('#history-pane').slideDown(150);
            $('#soap-pane, #triage-pane, #odontogram-pane').slideUp(150);
        });

        $('#tab-btn-odontogram').click(function() {
            $('.emr-nav-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            $('#odontogram-pane').slideDown(150);
            $('#soap-pane, #triage-pane, #history-pane').slideUp(150);
        });

        $('#btn-edit-triage-from-soap').click(function() {
            $('#tab-btn-triage').trigger('click');
        });

        $('.btn-return-to-soap').click(function() {
            $('#tab-btn-soap').trigger('click');
        });

        // Search queue list filter
        $('#search-queue-input').on('keyup input', function() {
            const val = $(this).val().toLowerCase().trim();
            $('.visit-item').each(function() {
                const name = $(this).data('name').toString().toLowerCase();
                const rm = $(this).data('rm').toString().toLowerCase();
                const queue = $(this).data('queue').toString().toLowerCase();
                if (name.includes(val) || rm.includes(val) || queue.includes(val)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Recalculate row on medicine change or qty change & Drug Allergy Alert
        $(document).on('change', '.select-med', function() {
            const idx = $(this).data('index');
            const opt = $(this).find(':selected');
            const stock = parseInt(opt.data('stock')) || 0;
            const isOut = opt.data('out') == '1' || stock <= 0;

            calculateMedicineRow(idx);
            checkDrugAllergySafety();

            if (opt.val() && isOut) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning(`Stok obat di apotek internal kosong (0). Obat ini dialihkan sebagai Resep Luar.`, 'Perhatian Stok Obat');
                }
            }
        });

        // Verifikasi Riwayat Alergi Pasien vs Obat yang Dipilih di e-Resep
        function checkDrugAllergySafety() {
            const allergies = (window.currentSoapPatientAllergies || '').trim().toLowerCase();
            const $alertBox = $('#alert-drug-allergy');
            const $alertMsg = $('#msg-drug-allergy');

            if (!allergies || allergies === '-' || allergies === 'tidak ada' || allergies === 'tidak ada catatan alergi') {
                $alertBox.hide();
                return;
            }

            let triggeredAllergies = [];

            $('.select-med').each(function() {
                const opt = $(this).find(':selected');
                const medName = (opt.text() || '').toLowerCase();
                const medVal = opt.val();

                if (medVal && medName) {
                    // Split keywords alergi
                    const allergyKeywords = allergies.split(/[,;\/\s]+/).filter(k => k.length >= 3);
                    
                    for (let kw of allergyKeywords) {
                        if (medName.includes(kw) || kw.includes(medName.split(' ')[0])) {
                            triggeredAllergies.push({
                                med: opt.text().split('(')[0].trim(),
                                allergy: kw
                            });
                            break;
                        }
                    }
                }
            });

            if (triggeredAllergies.length > 0) {
                const listStr = triggeredAllergies.map(t => `<strong>"${t.med}"</strong> (Alergi: <span class="badge badge-warning text-dark">${t.allergy}</span>)`).join(', ');
                $alertMsg.html(`<strong>⚠️ PERINGATAN KESELAMATAN OBAT (DRUG-ALLERGY ALERT):</strong> Pasien memiliki catatan alergi: <span class="badge badge-light border text-danger font-weight-bold">${window.currentSoapPatientAllergies}</span>. Obat terpilih ${listStr} berpotensi menimbulkan reaksi kontraindikasi alergi!`);
                $alertBox.slideDown(200);

                if (typeof toastr !== 'undefined') {
                    toastr.error(`Pasien alergi terhadap riwayat terkait (${window.currentSoapPatientAllergies})! Evaluasi kembali resep obat.`, '⚠️ Drug-Allergy Warning', { timeOut: 8000 });
                }
            } else {
                $alertBox.slideUp(200);
            }
        }

        $(document).on('input change', '.input-qty', function() {
            const idx = $(this).data('index');
            calculateMedicineRow(idx);
        });

        // Add new row for medicine prescription
        $('#add-medicine-row').click(function() {
            const currentIdx = medicineRowCount;
            let row = `
                <tr class="med-presc-row" data-index="${currentIdx}" id="med-presc-row-${currentIdx}">
                    <td>
                        <select name="medicines[${currentIdx}][medicine_id]" class="form-control form-control-sm select-searchable select-med font-weight-bold" data-index="${currentIdx}" data-search-placeholder="🔍 Ketik nama obat...">
                            ${medicinesOptionsHtml}
                        </select>
                        <div class="stock-warning-container mt-1" id="stock-warning-${currentIdx}" style="display:none;"></div>
                    </td>
                    <td>
                        <input type="number" name="medicines[${currentIdx}][qty]" class="form-control form-control-sm text-center font-weight-bold input-qty" data-index="${currentIdx}" placeholder="Qty" min="1" value="1">
                    </td>
                    <td class="text-right text-muted text-xs pt-2 font-weight-bold" id="lbl-unitprice-${currentIdx}">
                        Rp 0
                    </td>
                    <td class="text-right font-weight-bold text-teal text-xs pt-2" id="lbl-subtotal-${currentIdx}">
                        Rp 0
                    </td>
                    <td>
                        <input type="text" name="medicines[${currentIdx}][dosage]" class="form-control form-control-sm input-dosage" list="dosage-suggestions" placeholder="Contoh: 3 x 1 tablet sesudah makan">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-xs remove-row" title="Hapus Baris"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#prescription-table tbody').append(row);
            if (window.initSearchableSelects) {
                window.initSearchableSelects($(`#med-presc-row-${currentIdx}`));
            }
            medicineRowCount++;
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            if ($('#prescription-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                const firstRow = $(this).closest('tr');
                firstRow.find('.select-med').val('').trigger('change');
                firstRow.find('input').val('');
                firstRow.find('.input-qty').val(1);
            }
            calculateMedicineRow(0);
        });

        // Form SOAP Doctor Submit Validation
        $('#form-soap-doctor').on('submit', function(e) {
            const subj = $('#doc-subjective').val().trim();
            const assess = $('#doc-assessment').val().trim();
            const plan = $('#doc-plan').val().trim();

            if (!subj) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') toastr.warning('Mohon isi Keluhan / Anamnesis Pasien (Subjective).', 'Validasi Rekam Medis');
                $('#doc-subjective').focus();
                return false;
            }
            if (!assess) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') toastr.warning('Mohon isi Diagnosa Medis Pasien (Assessment).', 'Validasi Rekam Medis');
                $('#doc-assessment').focus();
                return false;
            }
            if (!plan) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') toastr.warning('Mohon isi Rencana Terapi & Edukasi (Plan).', 'Validasi Rekam Medis');
                $('#doc-plan').focus();
                return false;
            }

            $('#btn-submit-soap').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan SOAP...');
        });

        // Quick Copy Buttons (Perawat S & O -> Dokter S & O)
        $('#btn-copy-nurse-s').click(function() {
            const comp = $('#tr-complaints').val() || $('#live-s-complaints').text();
            const anam = $('#tr-anamnesis').val() || $('#live-s-anamnesis').text();
            let text = '';
            if (comp && comp !== '-') text += 'Keluhan: ' + comp;
            if (anam && anam !== '-') text += (text ? '\n' : '') + 'Riwayat / Catatan: ' + anam;
            
            if (text) {
                $('#doc-subjective').val(text);
                if (typeof toastr !== 'undefined') toastr.success('Data skrining perawat berhasil disalin ke Subjective dokter.');
            } else {
                alert('Belum ada data keluhan atau skrining perawat.');
            }
        });

        $('#btn-copy-nurse-o').click(function() {
            const bp = $('#tr-bp').val() || $('#live-o-bp').text();
            const temp = $('#tr-temp').val() || $('#live-o-temp').text();
            const pulse = $('#tr-pulse').val() || $('#live-o-pulse').text();
            const resp = $('#tr-resp').val() || $('#live-o-resp').text();
            const w = $('#tr-weight').val();
            const h = $('#tr-height').val();

            let text = `TTV: TD ${bp || '-'} mmHg, Suhu ${temp || '-'} °C, Nadi ${pulse || '-'} bpm, Nafas ${resp || '-'} x/mnt`;
            if (w || h) text += `, BB ${w || '-'} kg / TB ${h || '-'} cm`;
            
            const existing = $('#doc-objective').val();
            if (existing && !existing.includes('TTV:')) {
                $('#doc-objective').val(text + '\n' + existing);
            } else {
                $('#doc-objective').val(text);
            }
            if (typeof toastr !== 'undefined') toastr.success('TTV perawat berhasil disalin ke Objective dokter.');
        });

        // 1-Klik Quick Clinical Presets Auto-Fill Handler
        $('.btn-clinical-tpl').click(function() {
            const name = $(this).data('name');
            const s = $(this).data('s');
            const o = $(this).data('o');
            const a = $(this).data('a');
            const icd = $(this).data('icd');
            const p = $(this).data('p');
            const med = $(this).data('med');

            $('#doc-subjective').val(s);
            $('#doc-objective').val(o);
            $('#doc-assessment').val(a);
            $('#doc-plan').val(p);

            // Auto-fill ICD-10 if field exists
            const $icdInput = $('input[name="icd10_code"], #input-icd10-code, select[name="icd10_code"]');
            if ($icdInput.length) {
                $icdInput.val(icd).trigger('change');
            }

            if (typeof toastr !== 'undefined') {
                toastr.success(`Template ${name} (${icd}) berhasil diterapkan ke form SOAP.`, '⚡ Auto-Fill Berhasil');
            }
        });

        let currentQueueNo = '', currentPatientName = '', currentTargetName = '', currentDoctorName = '';

        // Global Patient Selector for Static & Real-Time LiveSync Elements
        window.selectSoapPatient = function($item) {
            if (!$item || !$item.length) return;

            $('.visit-item').removeClass('active');
            $item.addClass('active');

            const id = $item.data('id');
            const patientId = $item.data('patientid');
            const name = $item.data('name');
            const rm = $item.data('rm');
            const poly = $item.data('poly');
            const tindakan = $item.data('tindakan');
            const parent = $item.data('parent');
            const doctor = $item.data('doctor');
            const queueNo = $item.data('queue');

            currentQueueNo = queueNo;
            currentPatientName = name;
            currentTargetName = (tindakan && tindakan !== '-') ? 'Ruang Tindakan ' + tindakan : 'Poliklinik ' + poly;
            currentDoctorName = doctor;

            $('#welcome-pane').hide();
            $('#workspace-card').fadeIn(200);
            if ($(window).width() < 992) {
                $('html, body').animate({
                    scrollTop: $('#workspace-card').offset().top - 70
                }, 300);
            }
            $('.input-visit-id').val(id);
            
            // Labels
            $('#lbl-queue').text(queueNo || '-');
            $('#lbl-patient').text(name || 'Pasien');
            $('#lbl-rm').text('No RM: ' + (rm || '-'));
            $('#lbl-doctor').text(doctor || 'Dokter Jaga');
            
            // Riwayat Alergi Pasien
            const patientAllergies = $item.data('allergies') || '';
            window.currentSoapPatientAllergies = patientAllergies;

            if (patientAllergies && patientAllergies !== '-' && patientAllergies.toLowerCase() !== 'tidak ada catatan alergi') {
                $('#lbl-patient-allergies').text(patientAllergies);
                $('#box-patient-allergies').show();
            } else {
                $('#box-patient-allergies').hide();
            }

            // Reset Allergy Alert on Switch
            $('#alert-drug-allergy').hide();
            
            // Nurse TTV Caller widget labels
            $('#triage-call-patient-name').text(name || 'Pasien');
            $('#triage-call-queue-no').text(queueNo || '-');
            
            if (tindakan && tindakan !== '-') {
                $('#lbl-poly').html('<span class="badge badge-info mr-1">TINDAKAN</span> ' + (parent ? parent + ' &raquo; ' : '') + tindakan);
            } else {
                $('#lbl-poly').html('<span class="badge badge-teal mr-1">POLI</span> ' + (poly || 'Umum'));
            }

            // Status & Tombol SATUSEHAT
            const ssSynced = $item.data('satusehat') == '1' || Boolean($item.data('encounterid'));
            const ssEncounterId = $item.data('encounterid') || '';
            if (ssSynced) {
                $('#badge-satusehat-synced').show().html(`<i class="fas fa-check-circle mr-1"></i> Terkirim ke SATUSEHAT`);
                $('#btn-header-sync-satusehat').html('<i class="fas fa-rotate mr-1"></i> Sinkron Ulang').removeClass('btn-info text-white').addClass('btn-outline-info');
                $('#btn-header-view-satusehat').show();
            } else {
                $('#badge-satusehat-synced').hide();
                $('#btn-header-sync-satusehat').html('<i class="fas fa-cloud-arrow-up mr-1"></i> Kirim SATUSEHAT').removeClass('btn-outline-info').addClass('btn-info text-white');
                $('#btn-header-view-satusehat').hide();
            }

            // Function to render EHR Cards
            function renderEhrCards(historyList) {
                if (!historyList || historyList.length === 0) {
                    $('#patient-history-container').html(`
                        <div class="alert alert-light border text-center py-4 text-muted">
                            <i class="fas fa-info-circle mr-1"></i> Ini adalah kunjungan pertama pasien (belum ada riwayat rekam medis terdahulu).
                        </div>
                    `);
                    return;
                }

                let histHtml = '';
                historyList.forEach(function(h) {
                    let medsHtml = '';
                    if (h.medicines && h.medicines.length > 0) {
                        medsHtml = '<div class="mt-2 pt-2 border-top"><strong class="text-xs text-secondary d-block"><i class="fas fa-pills mr-1"></i> Resep yang Diberikan:</strong><ul class="mb-0 pl-3 text-xs">';
                        h.medicines.forEach(function(m) {
                            medsHtml += `<li><strong>${m.medicine_name}</strong> (${m.qty} ${m.unit}) - <em>${m.dosage || '-'}</em></li>`;
                        });
                        medsHtml += '</ul></div>';
                    }

                    histHtml += `
                        <div class="card bg-white border mb-3" style="border: 1px solid #e2e8f0 !important;">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
                                <span class="font-weight-bold text-dark text-sm"><i class="fas fa-calendar-alt text-teal mr-1"></i> Kunjungan: ${h.visit_date} (${h.polyclinic_name || 'Layanan Medis'})</span>
                                <span class="badge badge-secondary font-weight-normal">Dokter: ${h.doctor_name || '-'}</span>
                            </div>
                            <div class="card-body p-3 text-sm">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <strong class="text-xs text-muted d-block">Subjective (S):</strong>
                                        <div class="text-dark">${h.subjective || '-'}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong class="text-xs text-muted d-block">Objective (O):</strong>
                                        <div class="text-dark">${h.objective || '-'}</div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <strong class="text-xs text-muted d-block">Assessment / Diagnosa (A):</strong>
                                        <div class="text-dark font-weight-bold">${h.assessment || '-'} <span class="badge badge-teal">${h.icd10_code || ''}</span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong class="text-xs text-muted d-block">Plan / Terapi (P):</strong>
                                        <div class="text-dark">${h.plan || '-'}</div>
                                    </div>
                                </div>
                                ${medsHtml}
                            </div>
                        </div>
                    `;
                });
                $('#patient-history-container').html(histHtml);
            }

            // Check if patient history is already in memory or fetch via AJAX
            if (window.patientHistoryData && window.patientHistoryData[patientId]) {
                renderEhrCards(window.patientHistoryData[patientId]);
            } else if (patientId) {
                $.getJSON('<?= base_url("klinik/patient-rme-json") ?>/' + patientId, function(res) {
                    if (res && res.status === 'success' && res.data) {
                        window.patientHistoryData[patientId] = res.data.visits || [];
                        renderEhrCards(res.data.visits);
                    } else {
                        renderEhrCards([]);
                    }
                }).fail(function() {
                    renderEhrCards([]);
                });
            } else {
                renderEhrCards([]);
            }

            // Populate Triage & Reference Data
            const tr = (window.triagesData && window.triagesData[id]) ? window.triagesData[id] : null;
            if (tr) {
                $('#tr-bp').val(tr.blood_pressure || '');
                $('#tr-weight').val(tr.weight || '');
                $('#tr-height').val(tr.height || '');
                $('#tr-temp').val(tr.temperature || '');
                $('#tr-pulse').val(tr.pulse || '');
                $('#tr-resp').val(tr.respiration || '');
                $('#tr-complaints').val(tr.complaints || '');
                $('#tr-anamnesis').val(tr.anamnesis || '');

                updateLiveSoapPanelsFromTriage(tr);
            } else {
                $('#tr-bp, #tr-weight, #tr-height, #tr-temp, #tr-pulse, #tr-resp, #tr-complaints, #tr-anamnesis').val('');
                resetLiveSoapPanels();
            }

            // Activate Doctor's SOAP tab by default
            $('#tab-btn-soap').trigger('click');

            if (window.initSearchableSelects) {
                window.initSearchableSelects($('#prescription-table'));
            }
        };

        // Delegated Click Patient Queue Item
        $(document).on('click', '.visit-item', function(e) {
            e.preventDefault();
            window.selectSoapPatient($(this));
        });

        // Keyboard navigation (Enter / Space)
        $(document).on('keydown', '.visit-item', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.selectSoapPatient($(this));
            }
        });

        // Trigger Sync SATUSEHAT Kemenkes RI (AJAX)
        $(document).on('click', '#btn-header-sync-satusehat', function(e) {
            e.preventDefault();
            const visitId = $('.input-visit-id').val();
            if (!visitId) {
                alert('Pilih pasien antrean terlebih dahulu.');
                return;
            }

            const $btn = $(this);
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim ke SATUSEHAT...');

            fetch('<?= base_url('klinik/satusehat-sync') ?>/' + visitId, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(originalHtml);
                if (data.status === 'success') {
                    // Update active visit item data & badge
                    const activeItem = $('.visit-item.active');
                    activeItem.attr('data-satusehat', '1');
                    activeItem.data('satusehat', 1);
                    if (data.encounter && data.encounter.id) {
                        activeItem.attr('data-encounterid', data.encounter.id);
                        activeItem.data('encounterid', data.encounter.id);
                    }
                    if (!activeItem.find('.icon-ss-synced').length) {
                        activeItem.find('.patient-name-text').after(' <i class="fas fa-heart-circle-check text-success ml-1 icon-ss-synced" title="Tersinkron SATUSEHAT" style="font-size:11px;"></i>');
                    }

                    $('#badge-satusehat-synced').show().html(`<i class="fas fa-check-circle mr-1"></i> Terkirim ke SATUSEHAT`);
                    $('#btn-header-sync-satusehat').html('<i class="fas fa-rotate mr-1"></i> Sinkron Ulang').removeClass('btn-info text-white').addClass('btn-outline-info');
                    $('#btn-header-view-satusehat').show();

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Terkirim ke SATUSEHAT!',
                            html: `
                                <div class="text-left text-sm">
                                    <p class="mb-1"><strong>Status:</strong> ${data.message}</p>
                                    <p class="mb-1"><strong>Mode:</strong> <span class="badge badge-teal">${(data.mode || 'sandbox').toUpperCase()}</span></p>
                                    <p class="mb-1"><strong>Encounter ID:</strong> <code>${data.encounter?.id || '-'}</code></p>
                                    <p class="mb-0"><strong>Kepatuhan:</strong> HL7 FHIR R4 (Permenkes 24/2022)</p>
                                </div>
                            `,
                            confirmButtonColor: '#0d9488'
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(data.message, 'SATUSEHAT Kemenkes Berhasil');
                    } else {
                        alert(data.message);
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian SATUSEHAT',
                            text: data.message || 'Gagal memproses pengiriman.',
                            confirmButtonColor: '#eab308'
                        });
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(originalHtml);
                alert('Gagal menghubungi endpoint SATUSEHAT: ' + err.message);
            });
        });

        // Tampilkan Modal Log Audit HL7 FHIR R4
        $(document).on('click', '#btn-header-view-satusehat', function(e) {
            e.preventDefault();
            const visitId = $('.input-visit-id').val();
            if (!visitId) return;

            $('#modal-satusehat-logs').modal('show');
            $('#modal-ss-logs-container').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-teal"></i><br>Memuat riwayat log HL7 FHIR R4...</div>');

            fetch('<?= base_url('klinik/satusehat-logs') ?>/' + visitId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const visit = data.visit || {};
                    $('#modal-ss-patient-name').text(visit.patient_name || 'Pasien');
                    $('#modal-ss-patient-info').text(`NIK: ${visit.nik || '-'} | No RM: ${visit.no_rm || '-'} | IHS: ${visit.satusehat_ihs_id || 'Belum Terdaftar'}`);
                    $('#modal-ss-encounter-badge').text(`Encounter ID: ${visit.satusehat_encounter_id || '-'}`);

                    const logs = data.logs || [];
                    if (logs.length === 0) {
                        $('#modal-ss-logs-container').html('<div class="alert alert-light border text-center py-3 text-muted">Belum ada riwayat payload FHIR untuk kunjungan ini.</div>');
                        return;
                    }

                    let html = '';
                    logs.forEach((log) => {
                        let reqObj = null;
                        let resObj = null;
                        try { reqObj = JSON.parse(log.request_payload); } catch(e) { reqObj = log.request_payload; }
                        try { resObj = JSON.parse(log.response_payload); } catch(e) { resObj = log.response_payload; }

                        const isOk = log.status === 'success' || (log.http_status >= 200 && log.http_status < 300);
                        const badgeColor = isOk ? 'badge-success' : 'badge-danger';

                        html += `
                            <div class="card border mb-3 shadow-none">
                                <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge ${badgeColor} mr-2 font-weight-bold">HTTP ${log.http_status}</span>
                                        <strong class="text-dark font-weight-bold text-uppercase">${log.resource_type || 'Resource'}</strong>
                                        <small class="text-muted ml-2">${log.created_at || ''}</small>
                                    </div>
                                    <small class="text-secondary font-weight-bold">ID: <code>${log.resource_id || '-'}</code></small>
                                </div>
                                <div class="card-body p-2 bg-light">
                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <small class="text-xs font-weight-bold text-dark d-block mb-1">HL7 FHIR R4 Request Payload:</small>
                                            <pre class="bg-white p-2 border rounded text-xs mb-0" style="max-height: 200px; overflow-y:auto;">${JSON.stringify(reqObj, null, 2)}</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-xs font-weight-bold text-dark d-block mb-1">SATUSEHAT Response Gateway:</small>
                                            <pre class="bg-white p-2 border rounded text-xs mb-0" style="max-height: 200px; overflow-y:auto;">${JSON.stringify(resObj, null, 2)}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#modal-ss-logs-container').html(html);
                }
            })
            .catch(err => {
                $('#modal-ss-logs-container').html(`<div class="alert alert-danger py-2 text-xs"><i class="fas fa-times mr-1"></i> Gagal memuat log: ${err.message}</div>`);
            });
        });

        // Function to update S & O Live Panels in Doctor's SOAP tab in Real-Time
        window.updateLiveSoapPanelsFromTriage = function(tr) {
            if (!tr) return;

            $('#live-s-complaints').text(tr.complaints || '-');
            $('#live-s-anamnesis').text(tr.anamnesis || '-');

            $('#live-o-bp').text(tr.blood_pressure || '-');
            $('#live-o-temp').text(tr.temperature || '-');
            $('#live-o-pulse').text(tr.pulse || '-');
            $('#live-o-resp').text(tr.respiration || '-');

            let bbtbStr = '-';
            let imtStr = '-';
            const w = parseFloat(tr.weight);
            const h = parseFloat(tr.height);

            if (!isNaN(w) && !isNaN(h) && h > 0) {
                bbtbStr = `${tr.weight} kg / ${tr.height} cm`;
                const hM = h / 100;
                const bmi = (w / (hM * hM)).toFixed(1);
                let cat = 'Normal';
                let cls = 'badge-success';
                if (bmi < 18.5) { cat = 'Kurus'; cls = 'badge-warning'; }
                else if (bmi >= 25 && bmi < 30) { cat = 'Overweight'; cls = 'badge-warning'; }
                else if (bmi >= 30) { cat = 'Obesitas'; cls = 'badge-danger'; }
                imtStr = `<span class="badge ${cls} text-xs">${bmi} (${cat})</span>`;
            } else if (!isNaN(w) && w > 0) {
                bbtbStr = `${tr.weight} kg`;
            }

            $('#live-o-bbtb').html(bbtbStr);
            $('#live-o-imt').html(imtStr);

            // Pre-fill Doctor S & O if empty
            if (!$('#doc-subjective').val() && tr.complaints) {
                $('#doc-subjective').val('Keluhan: ' + tr.complaints + (tr.anamnesis ? ' | Catatan: ' + tr.anamnesis : ''));
            }
            if (!$('#doc-objective').val() && (tr.blood_pressure || tr.temperature)) {
                $('#doc-objective').val(`TD: ${tr.blood_pressure || '-'} mmHg, Suhu: ${tr.temperature || '-'} °C, Nadi: ${tr.pulse || '-'} bpm, BB: ${tr.weight || '-'} kg`);
            }
        };

        function resetLiveSoapPanels() {
            $('#live-s-complaints, #live-s-anamnesis').text('-');
            $('#live-o-bp, #live-o-temp, #live-o-pulse, #live-o-resp, #live-o-bbtb, #live-o-imt').text('-');
        }

        // Form Triage AJAX Submit
        $('#form-triage').on('submit', function(e) {
            e.preventDefault();
            performTriageAutosave();
            $('#tab-btn-soap').trigger('click');
        });

        // Auto-save logic for nurse triage
        let triageAutosaveTimer = null;
        function performTriageAutosave() {
            const visitId = $('.input-visit-id').val();
            if (!visitId) return;

            const tData = {
                blood_pressure: $('#tr-bp').val(),
                weight: $('#tr-weight').val(),
                height: $('#tr-height').val(),
                temperature: $('#tr-temp').val(),
                pulse: $('#tr-pulse').val(),
                respiration: $('#tr-resp').val(),
                complaints: $('#tr-complaints').val(),
                anamnesis: $('#tr-anamnesis').val(),
                nurse_notes: ''
            };

            window.updateLiveSoapPanelsFromTriage(tData);

            const postData = {
                action: 'save_triage',
                is_ajax: '1',
                visit_id: visitId,
                ...tData
            };

            $('#triage-autosave-status').html('<span class="text-teal font-weight-bold text-xs"><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...</span>');

            $.ajax({
                url: '<?= base_url('klinik/soap') ?>',
                method: 'POST',
                data: postData,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        $('#triage-autosave-status').html('<span class="badge badge-success px-2 py-1" style="font-size: 10px;"><i class="fas fa-check-circle mr-1"></i> Tersinkron ke SOAP</span>');
                        triagesData[visitId] = resp.data;
                        window.updateLiveSoapPanelsFromTriage(resp.data);

                        const activeItem = $(`.visit-item[data-id="${visitId}"]`);
                        if (activeItem.length > 0) {
                            activeItem.find('.badge-warning').removeClass('badge-warning text-dark').addClass('badge-success').html('<i class="fas fa-check-circle mr-1"></i> TTV Lengkap');
                            activeItem.attr('data-hastriage', '1');
                        }
                    }
                },
                error: function() {
                    $('#triage-autosave-status').html('<span class="text-danger text-xs"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal menyimpan</span>');
                }
            });
        }

        $('#tr-bp, #tr-weight, #tr-height, #tr-temp, #tr-pulse, #tr-resp, #tr-complaints, #tr-anamnesis').on('input change keyup', function() {
            const immediateData = {
                blood_pressure: $('#tr-bp').val(),
                weight: $('#tr-weight').val(),
                height: $('#tr-height').val(),
                temperature: $('#tr-temp').val(),
                pulse: $('#tr-pulse').val(),
                respiration: $('#tr-resp').val(),
                complaints: $('#tr-complaints').val(),
                anamnesis: $('#tr-anamnesis').val()
            };
            window.updateLiveSoapPanelsFromTriage(immediateData);

            clearTimeout(triageAutosaveTimer);
            $('#triage-autosave-status').html('<span class="text-secondary text-xs"><i class="fas fa-pencil-alt mr-1"></i> Mengetik...</span>');
            triageAutosaveTimer = setTimeout(performTriageAutosave, 450);
        });

        // Auto-fill ICD-10 description from Datalist
        $('#input-icd10-code').on('input change', function() {
            const val = $(this).val().trim().toUpperCase();
            const option = $(`#icd10-datalist option[value="${val}"]`);
            if (option.length && option.data('desc')) {
                $('#input-icd10-desc').val(option.data('desc'));
            }
        });

        // Auto-fill ICD-9 description from Datalist
        $('#input-icd9-code').on('input change', function() {
            const val = $(this).val().trim();
            const option = $(`#icd9-datalist option[value="${val}"]`);
            if (option.length && option.data('desc')) {
                $('#input-icd9-desc').val(option.data('desc'));
            }
        });

        // Odontogram JS
        let selectedToothNo = null;
        let currentPatientOdontogram = {};

        function resetToothChartUI() {
            $('.tooth-box').css({'background': '#f8fafc', 'border-color': '#cbd5e1'});
            $('.tooth-icon').css('color', '#94a3b8');
        }

        function paintTooth(toothNo, code) {
            const box = $(`#tooth-box-${toothNo}`);
            const icon = $(`#tooth-icon-${toothNo}`);
            if (!box.length) return;

            let bg = '#f8fafc', border = '#cbd5e1', iconColor = '#94a3b8';
            if (code === 'caries') { bg = '#fee2e2'; border = '#dc2626'; iconColor = '#dc2626'; }
            else if (code === 'filling') { bg = '#dbeafe'; border = '#2563eb'; iconColor = '#2563eb'; }
            else if (code === 'missing') { bg = '#f1f5f9'; border = '#475569'; iconColor = '#475569'; }
            else if (code === 'crown') { bg = '#fef9c3'; border = '#eab308'; iconColor = '#ca8a04'; }
            else if (code === 'radix') { bg = '#f3e8ff'; border = '#9333ea'; iconColor = '#9333ea'; }
            else if (code === 'extract') { bg = '#ffedd5'; border = '#ea580c'; iconColor = '#ea580c'; }

            box.css({'background': bg, 'border-color': border});
            icon.css('color', iconColor);
        }

        $(document).on('click', '.tooth-item', function() {
            selectedToothNo = $(this).data('tooth');
            $('#lbl-selected-tooth').text(selectedToothNo);
            
            const existing = currentPatientOdontogram[selectedToothNo] || { code: 'normal', name: 'Normal / Sehat', notes: '' };
            $('#sel-tooth-condition').val(existing.code || 'normal');
            $('#txt-tooth-notes').val(existing.notes || '');

            $('.tooth-box').removeClass('shadow-sm').css('outline', 'none');
            $(`#tooth-box-${selectedToothNo}`).css('outline', '2px solid #0d9f4f');
            $('#tooth-detail-card').slideDown(150);
        });

        $('#btn-close-tooth-detail').click(function() {
            $('#tooth-detail-card').slideUp(150);
            $('.tooth-box').css('outline', 'none');
        });

        $('#btn-apply-tooth').click(function() {
            if (!selectedToothNo) return;
            const code = $('#sel-tooth-condition').val();
            const name = $('#sel-tooth-condition option:selected').data('name');
            const notes = $('#txt-tooth-notes').val();

            currentPatientOdontogram[selectedToothNo] = {
                tooth: selectedToothNo,
                code: code,
                name: name,
                notes: notes
            };

            paintTooth(selectedToothNo, code);
            $('#tooth-detail-card').slideUp(150);
            $('.tooth-box').css('outline', 'none');
        });

        $('#btn-save-all-odontogram').click(function() {
            const activeItem = $('.visit-item.active');
            if (!activeItem.length) {
                alert('Pilih pasien terlebih dahulu.');
                return;
            }
            const patientId = activeItem.data('patientid');
            const visitId = activeItem.data('id');

            const teethArray = Object.values(currentPatientOdontogram);
            if (teethArray.length === 0) {
                alert('Belum ada data diagnosa gigi yang dipilih.');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url('klinik/save-odontogram') ?>',
                method: 'POST',
                data: {
                    patient_id: patientId,
                    visit_id: visitId,
                    teeth: teethArray,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(resp) {
                    btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Odontogram');
                    if (resp && resp.status === 'success') {
                        alert('Odontogram pasien berhasil disimpan.');
                    } else {
                        alert(resp.message || 'Gagal menyimpan odontogram.');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Odontogram');
                    alert('Terjadi kesalahan saat menyimpan odontogram.');
                }
            });
        });

        // 1. Core Function to Call Patient to Doctor's Room
        // 1. Core Function to Call Patient to Doctor's Room
        window.callPatientToDoctor = function(visitId, queueNo, patientName, targetName) {
            // Auto-select first queue item if none is currently selected
            if (!visitId || !queueNo) {
                const $targetItem = $('.visit-item.active').length ? $('.visit-item.active') : $('.visit-item:visible').first();
                if ($targetItem.length) {
                    window.selectSoapPatient($targetItem);
                    visitId = $targetItem.data('id');
                    queueNo = $targetItem.data('queue');
                    patientName = $targetItem.data('name');
                    const doctor = $targetItem.data('doctor');
                    const poly = $targetItem.data('poly');
                    const tindakan = $targetItem.data('tindakan');
                    targetName = (tindakan && tindakan !== '-') ? 'Ruang Tindakan ' + tindakan : 'Poliklinik ' + poly;
                    if (doctor && doctor !== '-') targetName += ' (' + doctor + ')';
                }
            }

            if (!visitId || !queueNo) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Tidak ada antrean pasien yang menunggu saat ini.', 'ℹ Antrean Kosong');
                }
                return;
            }

            const destination = (targetName && targetName !== '-') ? targetName : (currentTargetName || 'Ruang Pemeriksaan Dokter');

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'poliklinik',
                    counter_name: destination,
                    queue_number: queueNo,
                    patient_name: patientName,
                    visit_id: visitId,
                    call_action: 'call',
                    call_priority: 1
                }, function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nomor antrean <strong>' + queueNo + ' (' + patientName + ')</strong> dipanggil ke ' + destination + '.', '📢 Panggilan Pasien Dokter');
                    }
                });
            }

            $('#tab-btn-soap').trigger('click');
        };

        // 2. Core Function to Start TTV and Call Display
        window.startTtvAndCallDisplay = function(visitId, queueNo, patientName, targetName) {
            // Auto-select first queue item if none is currently selected
            if (!visitId || !queueNo) {
                const $targetItem = $('.visit-item.active').length ? $('.visit-item.active') : $('.visit-item:visible').first();
                if ($targetItem.length) {
                    window.selectSoapPatient($targetItem);
                    visitId = $targetItem.data('id');
                    queueNo = $targetItem.data('queue');
                    patientName = $targetItem.data('name');
                }
            }

            if (!visitId || !queueNo) {
                if (typeof toastr !== 'undefined') {
                    toastr.info('Tidak ada antrean pasien yang menunggu saat ini.', 'ℹ Antrean Kosong');
                }
                return;
            }

            const destination = 'Ruang Pemeriksaan Tanda Vital Perawat';

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'triage',
                    counter_name: destination,
                    queue_number: queueNo,
                    patient_name: patientName,
                    visit_id: visitId,
                    call_action: 'call',
                    call_priority: 1
                }, function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nomor antrean <strong>' + queueNo + ' (' + patientName + ')</strong> dipanggil ke Ruang TTV.', '📢 Panggilan Pasien Perawat');
                    }
                });
            }

            $('#tab-btn-triage').trigger('click');
        };

        // Click Panggil Dokter from sidebar queue item
        $(document).on('click', '.btn-sidebar-call-voice', function(e) {
            e.stopPropagation();
            const $item = $(this).closest('.visit-item');
            window.selectSoapPatient($item);
            
            const visitId = $item.data('id');
            const queueNo = $item.data('queue');
            const patientName = $item.data('name');
            const targetName = (currentDoctorName && currentDoctorName !== '-') ? (currentTargetName + ' (' + currentDoctorName + ')') : currentTargetName;

            window.callPatientToDoctor(visitId, queueNo, patientName, targetName);
        });

        // Click Mulai TTV from sidebar queue item
        $(document).on('click', '.btn-sidebar-start-ttv', function(e) {
            e.stopPropagation();
            const $item = $(this).closest('.visit-item');
            window.selectSoapPatient($item);
            
            const visitId = $item.data('id');
            const queueNo = $item.data('queue');
            const patientName = $item.data('name');
            const targetName = 'Ruang Pemeriksaan Tanda Vital Perawat';

            window.startTtvAndCallDisplay(visitId, queueNo, patientName, targetName);
        });

        // Header Action Buttons
        $('#btn-header-call-doctor').click(function() {
            let visitId = $('.input-visit-id').val();
            let queueNo = currentQueueNo;
            let patientName = currentPatientName;
            let targetName = (currentDoctorName && currentDoctorName !== '-') ? (currentTargetName + ' (' + currentDoctorName + ')') : currentTargetName;

            if (!visitId || !queueNo) {
                const $targetItem = $('.visit-item.active').length ? $('.visit-item.active') : $('.visit-item:visible').first();
                if ($targetItem.length) {
                    window.selectSoapPatient($targetItem);
                    visitId = $targetItem.data('id');
                    queueNo = $targetItem.data('queue');
                    patientName = $targetItem.data('name');
                    const doctor = $targetItem.data('doctor');
                    const poly = $targetItem.data('poly');
                    const tindakan = $targetItem.data('tindakan');
                    targetName = (tindakan && tindakan !== '-') ? 'Ruang Tindakan ' + tindakan : 'Poliklinik ' + poly;
                    if (doctor && doctor !== '-') targetName += ' (' + doctor + ')';
                }
            }

            window.callPatientToDoctor(visitId, queueNo, patientName, targetName);
        });

        $('#btn-header-call-ttv, #btn-triage-call-voice').click(function() {
            let visitId = $('.input-visit-id').val();
            let queueNo = currentQueueNo;
            let patientName = currentPatientName;

            if (!visitId || !queueNo) {
                const $targetItem = $('.visit-item.active').length ? $('.visit-item.active') : $('.visit-item:visible').first();
                if ($targetItem.length) {
                    window.selectSoapPatient($targetItem);
                    visitId = $targetItem.data('id');
                    queueNo = $targetItem.data('queue');
                    patientName = $targetItem.data('name');
                }
            }

            window.startTtvAndCallDisplay(visitId, queueNo, patientName, 'Ruang Pemeriksaan Tanda Vital Perawat');
        });

        // Click Batalkan Antrean from SOAP sidebar queue item
        $(document).on('click', '.btn-sidebar-cancel-queue', function(e) {
            e.stopPropagation();
            const $btn = $(this);
            const visitId = $btn.data('id');
            const patientName = $btn.data('name');
            const queueNo = $btn.data('queue');

            if (!confirm('Apakah Anda yakin ingin membatalkan antrean pasien ' + patientName + ' (' + queueNo + ')? Data antrean akan dikeluarkan dari daftar periksa.')) {
                return;
            }

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            const csrfName = $('meta[name="csrf-name"]').attr('content') || '<?= csrf_token() ?>';
            const csrfHash = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
            const postData = {
                action: 'delete_queue',
                queue_id: visitId,
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
                        const $item = $btn.closest('.visit-item');
                        const isCurrentActive = ($item.hasClass('active') || $('.input-visit-id').val() == visitId);
                        
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            const remaining = $('#queue-list .visit-item').length;
                            $('#lbl-total-queue').text(remaining + ' Pasien');
                            if (remaining === 0) {
                                $('#queue-list').html(
                                    '<div class="text-center text-muted p-4">' +
                                    '<i class="fas fa-user-clock fa-2x mb-2 text-secondary"></i>' +
                                    '<p class="mb-0 text-sm">Tidak ada antrean konsultasi medis aktif saat ini.</p>' +
                                    '</div>'
                                );
                            }
                        });

                        if (isCurrentActive) {
                            $('#workspace-card').hide();
                            $('#welcome-pane').show();
                            currentQueueNo = '';
                            currentPatientName = '';
                            currentTargetName = '';
                            $('.input-visit-id').val('');
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success(resp.message || 'Antrean berhasil dibatalkan.');
                        }
                    } else {
                        alert(resp.message || 'Gagal membatalkan antrean.');
                        $btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                    }
                },
                error: function(xhr) {
                    const err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan jaringan atau server saat membatalkan antrean.';
                    alert(err);
                    $btn.prop('disabled', false).html('<i class="fas fa-times"></i>');
                }
            });
        });

        // Toggle Queue Action Dropdown Menu
        $(document).on('click', '.btn-queue-action-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $menu = $(this).siblings('.dropdown-menu');
            $('.sidebar-queue-scroll .dropdown-menu').not($menu).removeClass('show');
            $menu.toggleClass('show');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.sidebar-queue-scroll .dropdown-menu').removeClass('show');
            }
        });

        $(document).on('click', '.sidebar-queue-scroll .dropdown-item', function(e) {
            $(this).closest('.dropdown-menu').removeClass('show');
        });

        // Auto-select pasien aktif pertama saat halaman dibuka jika ada antrean
        setTimeout(function() {
            const $activeOrFirst = $('#queue-list .visit-item[data-id]').first();
            if ($activeOrFirst.length > 0 && !$('.visit-item.active').length) {
                window.selectSoapPatient($activeOrFirst);
            }
        }, 100);

        // Trigger Panggilan Suara Otomatis ke Kasir Pasca Dokter Selesai Simpan SOAP
        <?php 
            $vTrigger = session()->getFlashdata('voice_trigger');
            if (!empty($vTrigger) && is_array($vTrigger)): 
                $qNumber = $vTrigger['queue_number'] ?? 'A-001';
                $pName   = $vTrigger['patient_name'] ?? 'Pasien';
                if (($vTrigger['action'] ?? '') === 'to_cashier'):
        ?>
            setTimeout(function() {
                const qNo = <?= json_encode($qNumber) ?>;
                const pName = <?= json_encode($pName) ?>;

                function executeVoiceCall() {
                    try {
                        if ('speechSynthesis' in window) {
                            if (window.speechSynthesis.paused) {
                                window.speechSynthesis.resume();
                            }
                        }

                        if (window.VCM) {
                            window.VCM.unlockAudio();
                            window.VCM.callToCashier(qNo, pName);
                        } else if (typeof callPatientToCashier === 'function') {
                            callPatientToCashier(qNo, pName);
                        } else if ('speechSynthesis' in window) {
                            const spokenQ = qNo.replace(/-/g, ' ');
                            const speechText = 'Nomor antrean ' + spokenQ + ', atas nama ' + pName + ', pemeriksaan dokter telah selesai. Silakan menuju ke Kasir Pembayaran untuk administrasi. Terima kasih.';
                            const utt = new SpeechSynthesisUtterance(speechText);
                            utt.lang = 'id-ID';
                            utt.rate = 0.85;
                            window.speechSynthesis.speak(utt);
                        }
                    } catch(e) {
                        console.warn('[SOAP Voice] Error triggering cashier call:', e);
                    }
                }

                executeVoiceCall();

                // Visual Toastr Notification with Re-Play Button
                if (typeof toastr !== 'undefined') {
                    toastr.success(
                        '📢 <strong>Pemeriksaan Selesai:</strong> Pasien <strong>' + qNo + ' (' + pName + ')</strong> telah diarahkan ke Kasir Pembayaran.<br><button class="btn btn-xs btn-light mt-1 font-weight-bold" id="btn-replay-soap-finish-voice"><i class="fas fa-volume-high mr-1"></i> Putar Ulang Panggilan</button>',
                        'Pemeriksaan Dokter Selesai',
                        { timeOut: 10000, closeButton: true }
                    );

                    $(document).on('click', '#btn-replay-soap-finish-voice', function(e) {
                        e.preventDefault();
                        executeVoiceCall();
                    });
                }
            }, 300);
        <?php endif; endif; ?>
    });
</script>
<?= $this->endSection() ?>
