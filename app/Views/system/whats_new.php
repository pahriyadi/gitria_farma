<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="container-fluid p-0">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h1 class="m-0 wn-page-title font-weight-bold" style="font-size: 1.45rem;">
                    <i class="fas fa-sparkles text-warning mr-2"></i> Apa yang Baru? (What's New)
                </h1>
                <small class="wn-page-sub">Pusat log pembaruan fitur, peningkatan arsitektur, dan rilis versi sistem Sawamawa Medical Center</small>
            </div>
            <div class="col-sm-4 text-right mt-2 mt-sm-0">
                <span class="badge badge-teal px-3 py-2 font-weight-bold shadow-xs" style="font-size: 13px;">
                    <i class="fas fa-code-branch mr-1"></i> Versi Sistem: <?= esc($currentVersion) ?>
                </span>
                <?php if ($isSuper): ?>
                    <button class="btn btn-teal btn-sm font-weight-bold ml-2 shadow-xs" data-toggle="modal" data-target="#modalAddUpdate">
                        <i class="fas fa-plus mr-1"></i> + Tambah Rilis Fitur
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="content p-0">
    <div class="container-fluid p-0">

        <!-- Top Highlight Summary Banner -->
        <div class="card wn-hero-card shadow-none mb-3" style="border-radius: 4px;">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h5 class="font-weight-bold wn-hero-title mb-1">
                            <i class="fas fa-rocket text-teal mr-1"></i> Sawamawa Enterprise ERP &amp; Pelayanan Medis Modern
                        </h5>
                        <p class="wn-hero-desc mb-0" style="font-size: 13px; line-height: 1.5;">
                            Sistem terintegrasi menyeluruh dari pendaftaran pasien berkecepatan tinggi dengan <strong>DataTables Server-Side Processing</strong>, <strong>Rekam Medis Elektronik (RME) SOAP</strong>, <strong>Farmasi Multi-Batch</strong>, <strong>POS Resto Sehat &amp; KDS Dapur</strong>, hingga <strong>Akuntansi Terotomatisasi (Smart Auto-Balancing)</strong>.
                        </p>
                    </div>
                    <div class="col-md-3 text-md-right mt-2 mt-md-0">
                        <a href="<?= base_url('accounting/laporan') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                            <i class="fas fa-file-invoice-dollar mr-1"></i> Laporan Keuangan
                        </a>
                        <a href="<?= base_url('klinik/pendaftaran') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold ml-1">
                            <i class="fas fa-users mr-1"></i> Pasien
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Kumpulkan seluruh kategori yang tersedia di database
        $uniqueCategories = [];
        foreach ($updates as $u) {
            $catTrim = trim($u->category);
            if (!empty($catTrim) && !in_array($catTrim, $uniqueCategories)) {
                $uniqueCategories[] = $catTrim;
            }
        }
        ?>

        <!-- Search & Category Filter Toolbar -->
        <div class="card wn-filter-card shadow-none mb-4" style="border-radius: 4px;">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-lg-5 mb-2 mb-lg-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text wn-search-prepend" style="border-radius: 4px 0 0 4px;">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" id="filter-updates-search" class="form-control wn-search-input" placeholder="Cari rilis versi (v2.11.0), fitur, atau kata kunci..." style="border-radius: 0 4px 4px 0; font-size: 13px;">
                        </div>
                    </div>
                    <div class="col-lg-7 d-flex flex-wrap align-items-center justify-content-lg-end" style="gap: 5px;">
                        <span class="text-xs wn-page-sub font-weight-bold mr-1 d-none d-md-inline-block">Kategori:</span>
                        <button type="button" class="btn btn-xs btn-teal font-weight-bold btn-cat-filter active" data-filter="all">
                            Semua (<span id="count-all"><?= count($updates) ?></span>)
                        </button>
                        <?php foreach ($uniqueCategories as $uc): ?>
                            <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold btn-cat-filter" data-filter="<?= strtolower(esc($uc)) ?>">
                                <?= esc($uc) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- DYNAMIC TIMELINE OF RELEASES -->
        <div class="timeline wn-timeline" id="timeline-updates-container">

            <?php if (empty($updates)): ?>
                <div class="card card-body wn-empty-alert text-center py-5" style="border-radius: 4px;">
                    <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                    <h5 class="font-weight-bold wn-page-title">Belum Ada Catatan Pembaruan</h5>
                    <p class="wn-page-sub mb-0">Catatan pembaruan rilis sistem akan muncul di sini.</p>
                </div>
            <?php else: ?>
                <?php 
                $lastDate = '';
                foreach ($updates as $up): 
                    $currentDateLabel = date('d F Y', strtotime($up->release_date));
                    $isNewDate = ($lastDate !== $up->release_date);
                    $lastDate = $up->release_date;
                    $searchKeywords = strtolower($up->version . ' ' . $up->title . ' ' . $up->category . ' ' . $up->summary . ' ' . $up->details . ' ' . $currentDateLabel);
                ?>

                    <?php if ($isNewDate): ?>
                        <div class="time-label timeline-date-header">
                            <span class="bg-teal px-3 py-1 font-weight-bold" style="border-radius: 4px;">
                                <i class="fas fa-calendar-check mr-1"></i> Rilis: <?= $currentDateLabel ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="update-item-wrapper" data-category="<?= strtolower(esc($up->category)) ?>" data-search="<?= esc($searchKeywords) ?>">
                        <i class="fas fa-bolt bg-<?= esc($up->badge_color) ?> text-white"></i>
                        <div class="timeline-item wn-release-card shadow-none" style="border-radius: 4px;">
                            <span class="time wn-release-version">
                                <i class="fas fa-tag mr-1"></i> <strong><?= esc($up->version) ?></strong>
                                <?php if ($up->is_major): ?>
                                    <span class="badge badge-danger ml-1 font-weight-bold">MAJOR</span>
                                <?php endif; ?>
                                <?php if (!$up->is_published): ?>
                                    <span class="badge badge-secondary ml-1 font-weight-bold">DRAFT</span>
                                <?php endif; ?>
                            </span>

                            <h3 class="timeline-header wn-release-header font-weight-bold d-flex align-items-center justify-content-between" style="padding: 12px 15px;">
                                <div>
                                    <span class="badge badge-<?= esc($up->badge_color) ?> mr-2 px-2 py-1 text-uppercase font-weight-bold" style="font-size: 10.5px;"><?= strtoupper(esc($up->category)) ?></span>
                                    <span class="wn-release-title" style="font-size: 15px;"><?= esc($up->title) ?></span>
                                </div>
                                <?php if ($isSuper): ?>
                                    <div class="timeline-actions ml-2 text-nowrap">
                                        <button class="btn btn-xs btn-outline-info font-weight-bold btn-edit-update mr-1"
                                                data-id="<?= $up->id ?>"
                                                data-version="<?= esc($up->version) ?>"
                                                data-title="<?= esc($up->title) ?>"
                                                data-category="<?= esc($up->category) ?>"
                                                data-color="<?= esc($up->badge_color) ?>"
                                                data-date="<?= esc($up->release_date) ?>"
                                                data-summary="<?= esc($up->summary) ?>"
                                                data-details="<?= esc($up->details) ?>"
                                                data-major="<?= $up->is_major ?>"
                                                data-published="<?= $up->is_published ?>"
                                                title="Edit Catatan Rilis">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="<?= base_url('system/whats-new/toggle/' . $up->id) ?>" class="btn btn-xs btn-outline-<?= $up->is_published ? 'warning' : 'success' ?> mr-1" title="<?= $up->is_published ? 'Jadikan Draft (Sembunyikan)' : 'Publikasikan' ?>">
                                            <i class="fas fa-eye<?= $up->is_published ? '-slash' : '' ?>"></i>
                                        </a>
                                        <form action="<?= base_url('system/whats-new/delete') ?>" method="post" class="d-inline form-delete-update" onsubmit="return confirm('Hapus catatan rilis <?= esc($up->version) ?> - <?= esc($up->title) ?>?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $up->id ?>">
                                            <button type="submit" class="btn btn-xs btn-outline-danger btn-delete-release" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </h3>

                            <div class="timeline-body" style="font-size: 13.5px; padding: 15px;">
                                <?php if (!empty($up->summary)): ?>
                                    <div class="alert wn-summary-box shadow-none mb-3 p-3" style="line-height: 1.6; font-size: 13px; border-radius: 4px;">
                                        <?= nl2br(esc($up->summary)) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($up->details)): ?>
                                    <div class="wn-details-box p-3 rounded">
                                        <?php 
                                        $lines = explode("\n", $up->details);
                                        foreach ($lines as $line):
                                            $line = trim($line);
                                            if (empty($line)) continue;
                                        ?>
                                            <div class="d-flex align-items-start wn-detail-line mb-1.5" style="font-size: 13px; line-height: 1.5;">
                                                <i class="fas fa-check-circle text-teal mr-2 mt-1" style="font-size: 12px; flex-shrink: 0;"></i>
                                                <div>
                                                    <?php if (str_starts_with($line, '•') || str_starts_with($line, '-')): ?>
                                                        <?= esc(ltrim($line, '•- ')) ?>
                                                    <?php else: ?>
                                                        <?= esc($line) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Empty Search Results Placeholder -->
            <div id="filter-empty-alert" class="card card-body wn-empty-alert text-center py-5 d-none" style="border-radius: 4px;">
                <i class="fas fa-search text-muted fa-3x mb-3"></i>
                <h5 class="font-weight-bold wn-page-title">Tidak Ada Pembaruan yang Cocok</h5>
                <p class="wn-page-sub mb-0">Coba gunakan kata kunci pencarian lain atau pilih kategori 'Semua'.</p>
            </div>

            <!-- END TIMELINE -->
            <div>
                <i class="fas fa-flag-checkered bg-teal text-white"></i>
            </div>

        </div>

    </div>
</div>

<?php if ($isSuper): ?>
<!-- MODAL ADD RELEASE -->
<div class="modal fade" id="modalAddUpdate" tabindex="-1" role="dialog" aria-labelledby="modalAddUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content wn-modal-content" style="border-radius: 4px;">
            <div class="modal-header wn-modal-header">
                <h5 class="modal-title font-weight-bold wn-modal-title" id="modalAddUpdateLabel">
                    <i class="fas fa-sparkles text-teal mr-1"></i> Tambah Catatan Rilis / Pembaruan Fitur Baru
                </h5>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/whats-new/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body wn-modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Kode Versi <span class="text-danger">*</span></label>
                            <input type="text" name="version" class="form-control font-weight-bold" placeholder="v2.12.0" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Kategori Rilis <span class="text-danger">*</span></label>
                            <select name="category" class="form-control font-weight-bold">
                                <option value="PERFORMA & DATA">PERFORMA & DATA</option>
                                <option value="FINANSIAL & AKUNTANSI">FINANSIAL & AKUNTANSI</option>
                                <option value="KLINIK & MEDIS">KLINIK & MEDIS</option>
                                <option value="FARMASI & RESTO">FARMASI & RESTO</option>
                                <option value="FITUR BARU & INTERAKTIF">FITUR BARU & INTERAKTIF</option>
                                <option value="PENYEMPURNAAN ANTARMUKA">PENYEMPURNAAN ANTARMUKA</option>
                                <option value="PUBLIC PORTAL">PUBLIC PORTAL</option>
                                <option value="KEAMANAN SISTEM">KEAMANAN SISTEM</option>
                                <option value="CORE ENGINE">CORE ENGINE</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Warna Badge Aksen</label>
                            <select name="badge_color" class="form-control">
                                <option value="teal">Teal (Hijau Toska)</option>
                                <option value="success">Success (Hijau)</option>
                                <option value="info">Info (Biru Muda)</option>
                                <option value="primary">Primary (Biru)</option>
                                <option value="warning">Warning (Kuning Emas)</option>
                                <option value="danger">Danger (Merah)</option>
                                <option value="secondary">Secondary (Abu-abu)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold">Judul Rilis / Fitur Utama <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control font-weight-bold" placeholder="Implementasi Fitur ..." required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Tanggal Rilis <span class="text-danger">*</span></label>
                            <input type="date" name="release_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Ringkasan Pembaruan (Summary)</label>
                        <textarea name="summary" class="form-control" rows="2" placeholder="Uraian singkat perubahan..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Rincian Item Perubahan (Bullet Points)</label>
                        <textarea name="details" class="form-control" rows="4" placeholder="• Poin perubahan 1&#10;• Poin perubahan 2&#10;• Poin perubahan 3"></textarea>
                        <small class="wn-page-sub">Gunakan tanda bullet (•) atau tanda hubung (-) di awal setiap baris.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" name="is_major" value="1" class="custom-control-input" id="checkMajorAdd">
                                <label class="custom-control-label font-weight-bold text-danger" for="checkMajorAdd">Tandai sebagai Rilis Besar (MAJOR RELEASE)</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Status Publikasi</label>
                            <select name="is_published" class="form-control">
                                <option value="1">Langsung Publikasikan (Live)</option>
                                <option value="0">Simpan sebagai Draft (Hanya Admin)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer wn-modal-footer">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Catatan Rilis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT RELEASE -->
<div class="modal fade" id="modalEditUpdate" tabindex="-1" role="dialog" aria-labelledby="modalEditUpdateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content wn-modal-content" style="border-radius: 4px;">
            <div class="modal-header wn-modal-header">
                <h5 class="modal-title font-weight-bold wn-modal-title" id="modalEditUpdateLabel">
                    <i class="fas fa-edit text-info mr-1"></i> Edit Catatan Rilis Pembaruan
                </h5>
                <button type="button" class="close text-secondary" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('system/whats-new/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit-up-id">
                <div class="modal-body wn-modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Kode Versi <span class="text-danger">*</span></label>
                            <input type="text" name="version" id="edit-up-version" class="form-control font-weight-bold" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Kategori Rilis <span class="text-danger">*</span></label>
                            <select name="category" id="edit-up-category" class="form-control font-weight-bold">
                                <option value="PERFORMA & DATA">PERFORMA & DATA</option>
                                <option value="FINANSIAL & AKUNTANSI">FINANSIAL & AKUNTANSI</option>
                                <option value="KLINIK & MEDIS">KLINIK & MEDIS</option>
                                <option value="FARMASI & RESTO">FARMASI & RESTO</option>
                                <option value="FITUR BARU & INTERAKTIF">FITUR BARU & INTERAKTIF</option>
                                <option value="PENYEMPURNAAN ANTARMUKA">PENYEMPURNAAN ANTARMUKA</option>
                                <option value="PUBLIC PORTAL">PUBLIC PORTAL</option>
                                <option value="KEAMANAN SISTEM">KEAMANAN SISTEM</option>
                                <option value="CORE ENGINE">CORE ENGINE</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Warna Badge Aksen</label>
                            <select name="badge_color" id="edit-up-color" class="form-control">
                                <option value="teal">Teal (Hijau Toska)</option>
                                <option value="success">Success (Hijau)</option>
                                <option value="info">Info (Biru Muda)</option>
                                <option value="primary">Primary (Biru)</option>
                                <option value="warning">Warning (Kuning Emas)</option>
                                <option value="danger">Danger (Merah)</option>
                                <option value="secondary">Secondary (Abu-abu)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold">Judul Rilis / Fitur Utama <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit-up-title" class="form-control font-weight-bold" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold">Tanggal Rilis <span class="text-danger">*</span></label>
                            <input type="date" name="release_date" id="edit-up-date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Ringkasan Pembaruan (Summary)</label>
                        <textarea name="summary" id="edit-up-summary" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Rincian Item Perubahan (Bullet Points)</label>
                        <textarea name="details" id="edit-up-details" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" name="is_major" value="1" class="custom-control-input" id="edit-up-major">
                                <label class="custom-control-label font-weight-bold text-danger" for="edit-up-major">Tandai sebagai Rilis Besar (MAJOR RELEASE)</label>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Status Publikasi</label>
                            <select name="is_published" id="edit-up-published" class="form-control">
                                <option value="1">Langsung Publikasikan (Live)</option>
                                <option value="0">Simpan sebagai Draft (Hanya Admin)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer wn-modal-footer">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-save mr-1"></i> Perbarui Catatan Rilis</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        <?php if ($isSuper): ?>
        $('.btn-edit-update').click(function() {
            $('#edit-up-id').val($(this).data('id'));
            $('#edit-up-version').val($(this).data('version'));
            $('#edit-up-title').val($(this).data('title'));
            $('#edit-up-category').val($(this).data('category'));
            $('#edit-up-color').val($(this).data('color'));
            $('#edit-up-date').val($(this).data('date'));
            $('#edit-up-summary').val($(this).data('summary'));
            $('#edit-up-details').val($(this).data('details'));
            $('#edit-up-major').prop('checked', $(this).data('major') == 1);
            $('#edit-up-published').val($(this).data('published'));
            $('#modalEditUpdate').modal('show');
        });
        <?php endif; ?>

        // -------------------------------------------------------------
        // Live Search & Category Filter Engine
        // -------------------------------------------------------------
        var currentCategory = 'all';

        function filterUpdates() {
            var query = $('#filter-updates-search').val().toLowerCase().trim();
            var visibleCount = 0;

            $('.update-item-wrapper').each(function() {
                var itemCat = ($(this).data('category') || '').toString().toLowerCase();
                var itemSearch = ($(this).data('search') || '').toString().toLowerCase();

                var matchCategory = (currentCategory === 'all' || itemCat.indexOf(currentCategory) !== -1);
                var matchQuery = (!query || itemSearch.indexOf(query) !== -1);

                if (matchCategory && matchQuery) {
                    $(this).removeClass('d-none');
                    visibleCount++;
                } else {
                    $(this).addClass('d-none');
                }
            });

            // Adjust date headers visibility based on visible child items
            $('.timeline-date-header').each(function() {
                var $nextItems = $(this).nextUntil('.timeline-date-header', '.update-item-wrapper:not(.d-none)');
                if ($nextItems.length > 0) {
                    $(this).removeClass('d-none');
                } else {
                    $(this).addClass('d-none');
                }
            });

            if (visibleCount === 0) {
                $('#filter-empty-alert').removeClass('d-none');
            } else {
                $('#filter-empty-alert').addClass('d-none');
            }
        }

        $('#filter-updates-search').on('input', function() {
            filterUpdates();
        });

        $('.btn-cat-filter').click(function() {
            $('.btn-cat-filter').removeClass('active btn-teal').addClass('btn-outline-secondary');
            $(this).addClass('active btn-teal').removeClass('btn-outline-secondary');
            currentCategory = $(this).data('filter');
            filterUpdates();
        });
    });
</script>

<?= $this->endSection() ?>
