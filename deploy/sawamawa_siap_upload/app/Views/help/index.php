<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Header Hero & Action Bar -->
<div class="card p-4 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8; border-top: 4px solid #0d9f4f;">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-2">
                <div class="p-2 rounded bg-light text-teal mr-3 border" style="font-size: 26px;">
                    <i class="fas fa-circle-question"></i>
                </div>
                <div>
                    <h4 class="font-weight-bold text-dark mb-0" style="letter-spacing: -0.3px;">
                        Pusat Bantuan & Dokumentasi Alur Sistem
                    </h4>
                    <span class="text-secondary" style="font-size: 13.5px;">
                        Panduan Operasional & Alur Bisnis <strong><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center & Resto Gizi')) ?></strong>
                    </span>
                </div>
            </div>
            <p class="text-muted text-xs mb-0">
                Dokumentasi dinamis berbasis database. Anda dapat membaca alur terpadu antar modul, panduan per peran staf, atau menulis dan menyesuaikan alur sistem sendiri secara mandiri.
            </p>
        </div>
        <div class="col-lg-5 mt-3 mt-lg-0 text-lg-right">
            <button class="btn btn-teal btn-sm font-weight-bold shadow-none mb-2" data-toggle="modal" data-target="#modalAddDoc">
                <i class="fas fa-plus mr-1"></i> + Tulis / Tambah Panduan Baru
            </button>
            <a href="<?= base_url('bantuan/cetak') ?>" target="_blank" class="btn btn-outline-dark btn-sm font-weight-bold shadow-none mb-2 ml-1">
                <i class="fas fa-print mr-1 text-teal"></i> Cetak / PDF Panduan
            </a>
            <div class="input-group mt-1">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                </div>
                <input type="text" id="help-search-input" class="form-control form-control-sm border-left-0 font-weight-bold" placeholder="Cari topik panduan (misal: resep, kasir, jurnal, dokter, stok)...">
            </div>
        </div>
    </div>
</div>

<!-- Navigasi Tab Pusat Bantuan -->
<div class="card p-0 mb-4 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <div class="card-header p-2 bg-white border-bottom">
        <ul class="nav nav-pills" id="help-tabs">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" href="#tab-workflows" data-toggle="pill">
                    <i class="fas fa-diagram-project mr-1"></i> 1. Peta Alur & Workflow Terpadu (<?= count($workflows) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-roles" data-toggle="pill">
                    <i class="fas fa-users-gear mr-1"></i> 2. Panduan Langkah per Peran (<?= count($roleGuides) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-faq" data-toggle="pill">
                    <i class="fas fa-circle-question mr-1"></i> 3. FAQ & Solusi Kendala (<?= count($faqs) ?>)
                </a>
            </li>
            <?php if (!empty($policies)): ?>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" href="#tab-architecture" data-toggle="pill">
                    <i class="fas fa-shield-halved mr-1"></i> 4. Kebijakan & Keamanan (<?= count($policies) ?>)
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content">
            
            <!-- ========================================================================= -->
            <!-- TAB 1: PETA ALUR & WORKFLOW TERINTEGRASI -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade show active" id="tab-workflows">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-sitemap text-teal mr-2"></i> Peta Alur Bisnis Terintegrasi (Data Real-time Database)</h5>
                        <small class="text-muted">Setiap alur dapat Anda edit atau tambahkan diagram langkahnya secara fleksibel.</small>
                    </div>
                </div>

                <?php if (empty($workflows)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-diagram-project fa-3x mb-2 text-secondary"></i>
                        <p>Belum ada alur sistem yang tersimpan di database.</p>
                        <button class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modalAddDoc">
                            <i class="fas fa-plus mr-1"></i> Tulis Alur Sistem Pertama
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($workflows as $wf): ?>
                        <?php 
                        $badgeBg = '#0d9f4f';
                        if ($wf->badge_color === 'warning') $badgeBg = '#f59e0b';
                        elseif ($wf->badge_color === 'danger') $badgeBg = '#dc2626';
                        elseif ($wf->badge_color === 'info') $badgeBg = '#0284c7';
                        elseif ($wf->badge_color === 'purple') $badgeBg = '#9333ea';
                        elseif ($wf->badge_color === 'primary') $badgeBg = '#2563eb';
                        
                        $steps = !empty($wf->flow_steps) ? json_decode($wf->flow_steps, true) : [];
                        ?>
                        <div class="help-item-card card p-3 mb-3 bg-white" style="border: 1px solid #cbd5e1; border-left: 5px solid <?= $badgeBg ?>;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-1">
                                        <i class="<?= esc($wf->icon) ?> mr-1" style="color: <?= $badgeBg ?>;"></i>
                                        <?= esc($wf->title) ?>
                                    </h6>
                                    <span class="badge badge-light border text-xs font-weight-bold text-dark mr-1">
                                        <i class="fas fa-user-tag mr-1 text-muted"></i> <?= esc($wf->target_role) ?>
                                    </span>
                                    <?php if (!$wf->is_published): ?>
                                        <span class="badge badge-warning text-xs font-weight-bold">Draft (Belum Terbit)</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <button class="btn btn-xs btn-outline-secondary font-weight-bold mr-1 btn-edit-doc" 
                                            data-id="<?= $wf->id ?>"
                                            data-category="<?= esc($wf->category) ?>"
                                            data-title="<?= esc($wf->title) ?>"
                                            data-target_role="<?= esc($wf->target_role) ?>"
                                            data-badge_color="<?= esc($wf->badge_color) ?>"
                                            data-icon="<?= esc($wf->icon) ?>"
                                            data-flow_steps="<?= esc($wf->flow_steps) ?>"
                                            data-summary="<?= esc($wf->summary) ?>"
                                            data-content="<?= esc($wf->content) ?>"
                                            data-order_num="<?= $wf->order_num ?>"
                                            data-is_published="<?= $wf->is_published ?>"
                                            title="Edit Alur Ini">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="<?= base_url('bantuan/delete/' . $wf->id) ?>" class="btn btn-xs btn-outline-danger font-weight-bold" onclick="return confirm('Apakah Anda yakin ingin menghapus alur ini?');" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Visual Flow Step Boxes jika ada -->
                            <?php if (!empty($steps) && is_array($steps)): ?>
                                <div class="row align-items-center mt-3 mb-2">
                                    <?php $cnt = count($steps); foreach ($steps as $idx => $st): ?>
                                        <div class="col-md text-center mb-2 mb-md-0">
                                            <div class="p-2 border rounded bg-light font-weight-bold text-xs h-100">
                                                <i class="<?= esc($st['icon'] ?? 'fas fa-circle-check') ?> <?= esc($st['color'] ?? 'text-teal') ?> d-block fa-2x mb-1"></i>
                                                <?= esc($st['title'] ?? 'Langkah') ?>
                                                <?php if (!empty($st['sub'])): ?>
                                                    <small class="d-block text-muted"><?= esc($st['sub']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($idx < $cnt - 1): ?>
                                            <div class="col-auto text-center d-none d-md-block text-muted px-0"><i class="fas fa-arrow-right"></i></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($wf->summary)): ?>
                                <p class="text-xs text-secondary mt-2 mb-1">
                                    <strong>Ringkasan:</strong> <?= esc($wf->summary) ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($wf->content)): ?>
                                <div class="mt-2 p-2 bg-light rounded text-xs text-dark" style="line-height: 1.6;">
                                    <?= nl2br(esc($wf->content)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 2: PANDUAN PENGGUNAAN BERDASARKAN PERAN (ROLES) -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-roles">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-users-gear text-teal mr-2"></i> Panduan Langkah per Peran Staf (Database Driven)</h5>
                        <small class="text-muted">Tata cara dan langkah operasional spesifik untuk setiap profesi dan staf klinik.</small>
                    </div>
                </div>

                <?php if (empty($roleGuides)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-users-gear fa-3x mb-2 text-secondary"></i>
                        <p>Belum ada panduan peran yang tersimpan di database.</p>
                        <button class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modalAddDoc">
                            <i class="fas fa-plus mr-1"></i> Tulis Panduan Peran Baru
                        </button>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionDynamicRoles">
                        <?php foreach ($roleGuides as $idx => $rg): ?>
                            <div class="help-item-card card mb-2 border">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center" id="headingRole<?= $rg->id ?>">
                                    <h6 class="mb-0 flex-grow-1">
                                        <button class="btn btn-link btn-block text-left text-dark font-weight-bold text-decoration-none d-flex justify-content-between align-items-center py-1 <?= $idx > 0 ? 'collapsed' : '' ?>" type="button" data-toggle="collapse" data-target="#collapseRole<?= $rg->id ?>">
                                            <span>
                                                <i class="<?= esc($rg->icon) ?> text-teal mr-2"></i> 
                                                <?= esc($rg->title) ?>
                                                <span class="badge badge-secondary ml-2 text-xs"><?= esc($rg->target_role) ?></span>
                                            </span>
                                            <i class="fas fa-chevron-down text-muted"></i>
                                        </button>
                                    </h6>
                                    <div class="ml-2">
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold btn-edit-doc"
                                                data-id="<?= $rg->id ?>"
                                                data-category="<?= esc($rg->category) ?>"
                                                data-title="<?= esc($rg->title) ?>"
                                                data-target_role="<?= esc($rg->target_role) ?>"
                                                data-badge_color="<?= esc($rg->badge_color) ?>"
                                                data-icon="<?= esc($rg->icon) ?>"
                                                data-flow_steps="<?= esc($rg->flow_steps) ?>"
                                                data-summary="<?= esc($rg->summary) ?>"
                                                data-content="<?= esc($rg->content) ?>"
                                                data-order_num="<?= $rg->order_num ?>"
                                                data-is_published="<?= $rg->is_published ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('bantuan/delete/' . $rg->id) ?>" class="btn btn-xs btn-outline-danger font-weight-bold ml-1" onclick="return confirm('Hapus panduan ini?');" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="collapseRole<?= $rg->id ?>" class="collapse <?= $idx === 0 ? 'show' : '' ?>" data-parent="#accordionDynamicRoles">
                                    <div class="card-body bg-white text-xs">
                                        <?php if (!empty($rg->summary)): ?>
                                            <p class="text-secondary mb-2"><em><?= esc($rg->summary) ?></em></p>
                                        <?php endif; ?>
                                        <div class="text-dark" style="line-height: 1.7;">
                                            <?= nl2br(esc($rg->content)) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 3: FAQ & SOLUSI KENDALA -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade" id="tab-faq">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-circle-question text-teal mr-2"></i> Tanya Jawab & Troubleshooting Harian (FAQ)</h5>
                        <small class="text-muted">Solusi cepat untuk kendala yang sering ditemui saat operasional klinik.</small>
                    </div>
                </div>

                <?php if (empty($faqs)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-circle-question fa-3x mb-2 text-secondary"></i>
                        <p>Belum ada FAQ tersimpan di database.</p>
                        <button class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modalAddDoc">
                            <i class="fas fa-plus mr-1"></i> Tulis FAQ Baru
                        </button>
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionDynamicFaq">
                        <?php foreach ($faqs as $idx => $fq): ?>
                            <div class="help-item-card card mb-2 border">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 flex-grow-1">
                                        <button class="btn btn-link btn-block text-left text-dark font-weight-bold text-decoration-none py-1 <?= $idx > 0 ? 'collapsed' : '' ?>" type="button" data-toggle="collapse" data-target="#collapseFaq<?= $fq->id ?>">
                                            <i class="fas fa-question-circle text-teal mr-2"></i> <?= esc($fq->title) ?>
                                        </button>
                                    </h6>
                                    <div class="ml-2">
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold btn-edit-doc"
                                                data-id="<?= $fq->id ?>"
                                                data-category="<?= esc($fq->category) ?>"
                                                data-title="<?= esc($fq->title) ?>"
                                                data-target_role="<?= esc($fq->target_role) ?>"
                                                data-badge_color="<?= esc($fq->badge_color) ?>"
                                                data-icon="<?= esc($fq->icon) ?>"
                                                data-flow_steps="<?= esc($fq->flow_steps) ?>"
                                                data-summary="<?= esc($fq->summary) ?>"
                                                data-content="<?= esc($fq->content) ?>"
                                                data-order_num="<?= $fq->order_num ?>"
                                                data-is_published="<?= $fq->is_published ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('bantuan/delete/' . $fq->id) ?>" class="btn btn-xs btn-outline-danger font-weight-bold ml-1" onclick="return confirm('Hapus FAQ ini?');" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="collapseFaq<?= $fq->id ?>" class="collapse <?= $idx === 0 ? 'show' : '' ?>" data-parent="#accordionDynamicFaq">
                                    <div class="card-body bg-white text-xs text-muted" style="line-height: 1.7;">
                                        <strong>Jawaban:</strong><br>
                                        <?= nl2br(esc($fq->content)) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 4: KEBIJAKAN & KEAMANAN -->
            <!-- ========================================================================= -->
            <?php if (!empty($policies)): ?>
            <div class="tab-pane fade" id="tab-architecture">
                <div class="mb-3">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-shield-halved text-teal mr-2"></i> Standar Kepatuhan, Keamanan & Kebijakan Akuntansi</h5>
                </div>
                <div class="row">
                    <?php foreach ($policies as $pol): ?>
                        <div class="col-md-6 mb-3">
                            <div class="help-item-card card p-3 h-100 bg-white" style="border: 1px solid #cbd5e1;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold text-teal mb-0"><i class="<?= esc($pol->icon) ?> mr-1"></i> <?= esc($pol->title) ?></h6>
                                    <div>
                                        <button class="btn btn-xs btn-outline-secondary font-weight-bold btn-edit-doc"
                                                data-id="<?= $pol->id ?>"
                                                data-category="<?= esc($pol->category) ?>"
                                                data-title="<?= esc($pol->title) ?>"
                                                data-target_role="<?= esc($pol->target_role) ?>"
                                                data-badge_color="<?= esc($pol->badge_color) ?>"
                                                data-icon="<?= esc($pol->icon) ?>"
                                                data-flow_steps="<?= esc($pol->flow_steps) ?>"
                                                data-summary="<?= esc($pol->summary) ?>"
                                                data-content="<?= esc($pol->content) ?>"
                                                data-order_num="<?= $pol->order_num ?>"
                                                data-is_published="<?= $pol->is_published ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('bantuan/delete/' . $pol->id) ?>" class="btn btn-xs btn-outline-danger font-weight-bold ml-1" onclick="return confirm('Hapus?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-xs text-muted" style="line-height: 1.6;">
                                    <?= nl2br(esc($pol->content)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL FORM CRUD: TAMBAH / EDIT DOKUMENTASI & ALUR SISTEM -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddDoc" tabindex="-1" role="dialog" aria-labelledby="modalDocLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: 1px solid #b8b8b8;">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="modalDocLabel">
                    <i class="fas fa-pen-to-square text-teal mr-1"></i> Tulis / Edit Panduan & Alur Sistem
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('bantuan/save') ?>" method="post" id="form-doc">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="doc_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark text-xs">Kategori Panduan <span class="text-danger">*</span></label>
                            <select name="category" id="doc_category" class="form-control form-control-sm font-weight-bold" required>
                                <option value="workflow">1. Peta Alur & Workflow Terpadu</option>
                                <option value="role_guide">2. Panduan Langkah per Peran (Role)</option>
                                <option value="faq">3. Tanya Jawab (FAQ & Solusi Kendala)</option>
                                <option value="security_policy">4. Kebijakan & Keamanan Sistem</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark text-xs">Target Peran / Profesi Staf</label>
                            <select name="target_role" id="doc_target_role" class="form-control form-control-sm">
                                <option value="Semua Peran">Semua Peran Staf</option>
                                <option value="Pendaftaran / FO">Pendaftaran / Front Office</option>
                                <option value="Perawat">Perawat / Triase</option>
                                <option value="Dokter">Dokter Spesialis & Umum</option>
                                <option value="Apoteker">Farmasi & Apoteker</option>
                                <option value="Kasir">Kasir Utama</option>
                                <option value="Resto & Dapur Gizi">Restoran Sehat & Koki</option>
                                <option value="Akuntansi & Keuangan">Akuntansi & Keuangan</option>
                                <option value="Farmasi & Pengadaan">Pengadaan & Logistik</option>
                                <option value="Manajemen / Direktur">Direktur & Manajemen</option>
                                <option value="IT Admin">Administrator IT</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark text-xs">Judul Panduan / Alur Transaksi <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="doc_title" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Alur Pelayanan Pasien Rawat Jalan" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-dark text-xs">Warna Aksen Badge</label>
                            <select name="badge_color" id="doc_badge_color" class="form-control form-control-sm">
                                <option value="teal">Hijau Toska (Teal)</option>
                                <option value="primary">Biru (Primary)</option>
                                <option value="warning">Oranye / Kuning (Warning)</option>
                                <option value="danger">Merah (Danger)</option>
                                <option value="info">Biru Muda (Info)</option>
                                <option value="purple">Ungu (Purple)</option>
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold text-dark text-xs">Ikon FontAwesome</label>
                            <input type="text" name="icon" id="doc_icon" class="form-control form-control-sm" placeholder="fas fa-hospital-user" value="fas fa-circle-question">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-dark text-xs">Nomor Urut</label>
                            <input type="number" name="order_num" id="doc_order_num" class="form-control form-control-sm" value="1" min="1">
                        </div>
                    </div>

                    <!-- Flow Steps Generator (Khusus Workflow) -->
                    <div class="form-group" id="group_flow_steps">
                        <label class="font-weight-bold text-dark text-xs">
                            Diagram Langkah Alur Visual <small class="text-muted font-weight-normal">(Format 1 baris per langkah: Judul Langkah | Sub Judul)</small>
                        </label>
                        <textarea name="flow_steps" id="doc_flow_steps" class="form-control font-monospace text-xs" rows="3" placeholder="1. Pendaftaran | Online / Loket FO&#10;2. Triase Perawat | TTV & Keluhan&#10;3. Periksa Dokter | SOAP & E-Resep&#10;4. Farmasi & Kasir | Obat & Billing"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark text-xs">Ringkasan / Deskripsi Singkat</label>
                        <input type="text" name="summary" id="doc_summary" class="form-control form-control-sm" placeholder="Ringkasan 1-2 kalimat tentang panduan ini">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark text-xs">Isi Panduan Lengkap / Rincian Langkah <span class="text-danger">*</span></label>
                        <textarea name="content" id="doc_content" class="form-control text-xs" rows="6" placeholder="Tuliskan petunjuk nomor 1, 2, 3 atau penjelasan lengkap di sini..." required></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_published" id="doc_is_published" class="form-check-input" value="1" checked>
                        <label class="form-check-label font-weight-bold text-dark text-xs" for="doc_is_published">
                            Terbitkan Langsung (Dapat dibaca oleh semua staf)
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan ke Database
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Quick Live Search Box
        $('#help-search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.help-item-card').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Edit Document Modal Prefill
        $('.btn-edit-doc').click(function() {
            var btn = $(this);
            $('#doc_id').val(btn.data('id'));
            $('#doc_category').val(btn.data('category'));
            $('#doc_title').val(btn.data('title'));
            $('#doc_target_role').val(btn.data('target_role'));
            $('#doc_badge_color').val(btn.data('badge_color'));
            $('#doc_icon').val(btn.data('icon'));
            $('#doc_summary').val(btn.data('summary'));
            $('#doc_content').val(btn.data('content'));
            $('#doc_order_num').val(btn.data('order_num'));
            
            var steps = btn.data('flow_steps');
            if (typeof steps === 'object') {
                var stepLines = [];
                $.each(steps, function(i, s) {
                    stepLines.push(s.title + (s.sub ? ' | ' + s.sub : ''));
                });
                $('#doc_flow_steps').val(stepLines.join('\n'));
            } else {
                $('#doc_flow_steps').val(steps || '');
            }

            $('#doc_is_published').prop('checked', btn.data('is_published') == 1);
            $('#modalDocLabel').html('<i class="fas fa-edit text-teal mr-1"></i> Edit Panduan: ' + btn.data('title'));
            $('#modalAddDoc').modal('show');
        });

        // Reset form on open Add
        $('#modalAddDoc').on('hidden.bs.modal', function() {
            $('#form-doc')[0].reset();
            $('#doc_id').val('');
            $('#modalDocLabel').html('<i class="fas fa-pen-to-square text-teal mr-1"></i> Tulis / Tambah Panduan & Alur Sistem');
        });
    });
</script>
<?= $this->endSection() ?>
