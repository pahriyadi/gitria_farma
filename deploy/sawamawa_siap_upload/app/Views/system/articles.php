<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Stat Counter Cards -->
    <div class="col-lg-3 col-6 mb-3">
        <div class="small-box bg-white shadow-sm border p-3" style="border-radius: 8px; border-left: 4px solid #0d9488 !important;">
            <div class="inner">
                <h3 class="font-weight-bold text-dark mb-1"><?= count($articles) ?></h3>
                <p class="text-muted text-xs text-uppercase font-weight-bold mb-0">Total Artikel Diterbitkan</p>
            </div>
            <div class="icon text-teal" style="font-size: 48px; opacity: 0.15; position: absolute; right: 15px; top: 15px;">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3">
        <div class="small-box bg-white shadow-sm border p-3" style="border-radius: 8px; border-left: 4px solid #0284c7 !important;">
            <div class="inner">
                <?php 
                    $totalViews = 0;
                    foreach ($articles as $a) { $totalViews += (int)$a->views; }
                ?>
                <h3 class="font-weight-bold text-dark mb-1"><?= number_format($totalViews, 0, ',', '.') ?></h3>
                <p class="text-muted text-xs text-uppercase font-weight-bold mb-0">Total Pembaca (Views)</p>
            </div>
            <div class="icon text-info" style="font-size: 48px; opacity: 0.15; position: absolute; right: 15px; top: 15px;">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3">
        <div class="small-box bg-white shadow-sm border p-3" style="border-radius: 8px; border-left: 4px solid #16a34a !important;">
            <div class="inner">
                <?php 
                    $publishedCount = 0;
                    foreach ($articles as $a) { if ($a->status === 'published') $publishedCount++; }
                ?>
                <h3 class="font-weight-bold text-dark mb-1"><?= $publishedCount ?></h3>
                <p class="text-muted text-xs text-uppercase font-weight-bold mb-0">Status Published</p>
            </div>
            <div class="icon text-success" style="font-size: 48px; opacity: 0.15; position: absolute; right: 15px; top: 15px;">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 mb-3">
        <div class="small-box bg-white shadow-sm border p-3" style="border-radius: 8px; border-left: 4px solid #f59e0b !important;">
            <div class="inner">
                <h3 class="font-weight-bold text-dark mb-1">Live</h3>
                <p class="text-muted text-xs text-uppercase font-weight-bold mb-0">Terintegrasi Landing Page</p>
            </div>
            <div class="icon text-warning" style="font-size: 48px; opacity: 0.15; position: absolute; right: 15px; top: 15px;">
                <i class="fas fa-globe"></i>
            </div>
        </div>
    </div>

    <!-- Main Card Articles Table -->
    <div class="col-12">
        <div class="card card-outline card-teal shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-newspaper text-teal mr-1"></i> Daftar Artikel &amp; Edukasi Kesehatan
                    </h3>
                    <span class="text-muted text-xs d-block">Kelola konten berita, edukasi medis, dan tips kesehatan yang tampil di Landing Page publik.</span>
                </div>
                <div class="card-tools ml-auto mt-2 mt-sm-0">
                    <a href="<?= base_url('#artikel') ?>" target="_blank" class="btn btn-outline-info btn-sm font-weight-bold mr-2">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat di Landing Page
                    </a>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalCreateArticle">
                        <i class="fas fa-plus-circle mr-1"></i> Tulis Artikel Baru
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable-serverside dt-responsive w-100" id="table-articles">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 70px;" class="text-center">Sampul</th>
                                <th>Judul Artikel &amp; Ringkasan</th>
                                <th style="width: 140px;">Kategori</th>
                                <th style="width: 130px;">Penulis</th>
                                <th style="width: 80px;" class="text-center">Views</th>
                                <th style="width: 90px;" class="text-center">Status</th>
                                <th style="width: 100px;" class="text-center">Tanggal</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($articles as $art): ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                    <td class="text-center align-middle p-1">
                                        <?php if (!empty($art->image_url) && file_exists(FCPATH . $art->image_url)): ?>
                                            <img src="<?= base_url($art->image_url) ?>" alt="Thumb" style="width: 54px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;">
                                        <?php else: ?>
                                            <div class="d-inline-flex align-items-center justify-content-center bg-light text-teal border" style="width: 54px; height: 40px; border-radius: 4px; font-size: 18px;">
                                                <i class="fas fa-notes-medical"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark" style="font-size: 13.5px;"><?= esc($art->title) ?></div>
                                        <small class="text-muted text-xs d-block"><?= esc(mb_substr($art->summary, 0, 110)) ?>...</small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-info px-2 py-1 font-weight-bold text-xs">
                                            <?= esc($art->category) ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-xs font-weight-bold text-dark">
                                        <i class="fas fa-user-doctor text-teal mr-1"></i> <?= esc($art->author) ?>
                                    </td>
                                    <td class="align-middle text-center font-weight-bold text-xs text-muted">
                                        <i class="fas fa-eye mr-1"></i> <?= number_format((int)$art->views, 0, ',', '.') ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <form action="<?= base_url('system/articles') ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="article_id" value="<?= $art->id ?>">
                                            <?php if ($art->status === 'published'): ?>
                                                <button type="submit" class="btn btn-xs btn-success font-weight-bold" title="Klik untuk jadikan Draft">
                                                    <i class="fas fa-check-circle mr-1"></i> Publish
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-xs btn-secondary font-weight-bold" title="Klik untuk Terbitkan">
                                                    <i class="fas fa-file-lines mr-1"></i> Draft
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td class="align-middle text-center text-xs text-muted">
                                        <?= date('d M Y', strtotime($art->created_at)) ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-teal btn-xs btn-edit-article" 
                                                data-id="<?= $art->id ?>"
                                                data-title="<?= esc($art->title) ?>"
                                                data-category="<?= esc($art->category) ?>"
                                                data-author="<?= esc($art->author) ?>"
                                                data-summary="<?= esc($art->summary) ?>"
                                                data-content="<?= esc($art->content) ?>"
                                                data-status="<?= esc($art->status) ?>"
                                                title="Edit Artikel">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="<?= base_url('system/articles') ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="article_id" value="<?= $art->id ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus Artikel">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

<!-- Modal Create Article -->
<div class="modal fade" id="modalCreateArticle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('system/articles') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <div class="modal-header bg-teal text-white p-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-pen-nib mr-1"></i> Tulis &amp; Terbitkan Artikel Medis Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Judul Artikel / Berita: <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: 5 Langkah Pencegahan Demam Berdarah di Musim Hujan" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Kategori Artikel:</label>
                            <select name="category" class="form-control form-control-sm font-weight-bold">
                                <option value="Edukasi Kesehatan">Edukasi Kesehatan</option>
                                <option value="Tips Medis">Tips Medis</option>
                                <option value="Berita Klinik">Berita Klinik</option>
                                <option value="Layanan & Fasilitas">Layanan &amp; Fasilitas</option>
                                <option value="Promo & Kegiatan">Promo &amp; Kegiatan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Penulis / Dokter:</label>
                            <input type="text" name="author" class="form-control form-control-sm" value="Tim Medis Sawamawa">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Status Publikasi:</label>
                            <select name="status" class="form-control form-control-sm font-weight-bold">
                                <option value="published">🟢 Published (Langsung Tayang)</option>
                                <option value="draft">🟡 Draft (Disimpan Dulu)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Foto Sampul Artikel (Opsional):</label>
                        <input type="file" name="article_image" class="form-control-file" accept="image/*">
                        <small class="text-muted text-xs">Format JPG/PNG/WebP, maksimal 2MB.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Ringkasan Singkat (Lead Paragraph):</label>
                        <textarea name="summary" class="form-control form-control-sm" rows="2" placeholder="Tuliskan 1-2 kalimat ringkasan yang menarik pembaca di kartu depan..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-dark">Konten Lengkap Artikel: <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control form-control-sm" rows="8" placeholder="Tuliskan isi artikel selengkapnya..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Simpan &amp; Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Article -->
<div class="modal fade" id="modalEditArticle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('system/articles') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="article_id" id="edit_article_id">
                <div class="modal-header bg-dark text-white p-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-edit mr-1"></i> Edit Artikel / Berita Medis
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Judul Artikel / Berita: <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control form-control-sm font-weight-bold" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Kategori Artikel:</label>
                            <select name="category" id="edit_category" class="form-control form-control-sm font-weight-bold">
                                <option value="Edukasi Kesehatan">Edukasi Kesehatan</option>
                                <option value="Tips Medis">Tips Medis</option>
                                <option value="Berita Klinik">Berita Klinik</option>
                                <option value="Layanan & Fasilitas">Layanan &amp; Fasilitas</option>
                                <option value="Promo & Kegiatan">Promo &amp; Kegiatan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Penulis / Dokter:</label>
                            <input type="text" name="author" id="edit_author" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-weight-bold text-dark">Status Publikasi:</label>
                            <select name="status" id="edit_status" class="form-control form-control-sm font-weight-bold">
                                <option value="published">🟢 Published</option>
                                <option value="draft">🟡 Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Ganti Foto Sampul (Opsional):</label>
                        <input type="file" name="article_image" class="form-control-file" accept="image/*">
                        <small class="text-muted text-xs">Kosongkan jika tidak ingin mengubah foto yang ada.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-weight-bold text-dark">Ringkasan Singkat:</label>
                        <textarea name="summary" id="edit_summary" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-dark">Konten Lengkap Artikel: <span class="text-danger">*</span></label>
                        <textarea name="content" id="edit_content" class="form-control form-control-sm" rows="8" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark btn-sm font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Perbarui Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined') {
            $('.btn-edit-article').click(function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const cat = $(this).data('category');
                const author = $(this).data('author');
                const summary = $(this).data('summary');
                const content = $(this).data('content');
                const status = $(this).data('status');

                $('#edit_article_id').val(id);
                $('#edit_title').val(title);
                $('#edit_category').val(cat);
                $('#edit_author').val(author);
                $('#edit_summary').val(summary);
                $('#edit_content').val(content);
                $('#edit_status').val(status);

                $('#modalEditArticle').modal('show');
            });
        }
    });
</script>
<?= $this->endSection() ?>
