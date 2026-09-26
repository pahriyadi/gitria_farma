<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Kolom Kiri: Kartu Identitas Profil & Foto -->
    <div class="col-md-4">
        <!-- Card Profil Pengguna -->
        <div class="card p-3 text-center mb-3">
            <div class="position-relative d-inline-block mx-auto mb-3" style="width: 120px; height: 120px;">
                <!-- Avatar Image Preview -->
                <div id="avatar-preview-container" class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; border: 3px solid #0d9f4f; background: #f0fdfa;">
                    <?php if (!empty($user->avatar) && file_exists(FCPATH . $user->avatar)): ?>
                        <img id="avatar-img-preview" src="<?= base_url($user->avatar) ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img id="avatar-img-preview" src="" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <span id="avatar-fallback-icon" class="text-teal" style="font-size: 54px;">
                            <i class="fas fa-user-doctor"></i>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Tombol Ganti Foto Overlay -->
                <button type="button" class="btn btn-sm btn-teal rounded-circle position-absolute" style="bottom: 0; right: 0; width: 36px; height: 36px; padding: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.15);" onclick="$('#quick-avatar-input').click();" title="Ganti Foto Profil">
                    <i class="fas fa-camera"></i>
                </button>
            </div>

            <!-- Form Upload Avatar Tersembunyi (Auto-Submit) -->
            <form id="form-quick-avatar" action="<?= base_url('profile/upload-avatar') ?>" method="post" enctype="multipart/form-data" class="d-none">
                <?= csrf_field() ?>
                <input type="file" id="quick-avatar-input" name="avatar_quick" accept="image/jpeg,image/png,image/webp" onchange="previewAndSubmitAvatar(this);">
            </form>

            <h5 class="font-weight-bold text-dark mb-1">
                <?= esc($user->fullname ?: $user->username) ?>
            </h5>
            <div class="mb-2">
                <span class="badge badge-teal px-2 py-1 font-weight-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="fas fa-user-shield mr-1"></i> <?= esc($user->role_name) ?>
                </span>
                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 11px;">
                    <i class="fas fa-check-circle mr-1"></i> <?= esc(ucfirst($user->status)) ?>
                </span>
            </div>

            <p class="text-secondary text-xs mb-3 px-2">
                <?= esc($user->bio ?: 'Pengguna terdaftar sistem SAWAMAWA Medical Center.') ?>
            </p>

            <div class="border-top pt-3 text-left">
                <div class="d-flex justify-content-between text-xs py-1 border-bottom">
                    <span class="text-muted"><i class="fas fa-user mr-1"></i> Username:</span>
                    <strong class="text-dark font-monospace"><?= esc($user->username) ?></strong>
                </div>
                <div class="d-flex justify-content-between text-xs py-1 border-bottom">
                    <span class="text-muted"><i class="fas fa-envelope mr-1"></i> Email:</span>
                    <strong class="text-dark"><?= esc($user->email) ?></strong>
                </div>
                <div class="d-flex justify-content-between text-xs py-1 border-bottom">
                    <span class="text-muted"><i class="fas fa-phone mr-1"></i> No. Telp / WA:</span>
                    <strong class="text-dark"><?= esc($user->phone ?: '-') ?></strong>
                </div>
                <div class="d-flex justify-content-between text-xs py-1">
                    <span class="text-muted"><i class="fas fa-calendar-check mr-1"></i> Terdaftar Sejak:</span>
                    <strong class="text-dark"><?= date('d M Y', strtotime($user->created_at ?? date('Y-m-d'))) ?></strong>
                </div>
            </div>
        </div>

        <!-- Card Aktivitas Terakhir Pengguna -->
        <div class="card p-3">
            <h6 class="font-weight-bold text-dark mb-2 pb-2 border-bottom">
                <i class="fas fa-clock-rotate-left text-teal mr-1"></i> Riwayat Aktivitas Terakhir
            </h6>
            <div style="max-height: 260px; overflow-y: auto;">
                <?php if (!empty($recent_logs)): ?>
                    <ul class="list-unstyled mb-0" style="font-size: 11.5px;">
                        <?php foreach ($recent_logs as $log): ?>
                            <li class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="badge badge-secondary" style="font-size: 9.5px;"><?= esc($log->action) ?></span>
                                    <span class="text-muted text-xs"><?= date('d/m H:i', strtotime($log->created_at)) ?></span>
                                </div>
                                <div class="text-dark font-weight-bold mt-1"><?= esc($log->module) ?></div>
                                <div class="text-secondary text-truncate" title="<?= esc($log->new_value) ?>"><?= esc($log->new_value) ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-xs text-center my-3">Belum ada catatan aktivitas audit.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Formulir Pengaturan Profil, Password & Hak Akses -->
    <div class="col-md-8">
        <div class="card p-0">
            <!-- Nav Tabs -->
            <div class="card-header p-2 bg-white border-bottom">
                <ul class="nav nav-pills" id="profile-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-info-tab" data-toggle="pill" href="#tab-info" role="tab">
                            <i class="fas fa-user-edit mr-1"></i> Informasi Profil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-keamanan-tab" data-toggle="pill" href="#tab-keamanan" role="tab">
                            <i class="fas fa-key mr-1"></i> Keamanan & Password
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-akses-tab" data-toggle="pill" href="#tab-akses" role="tab">
                            <i class="fas fa-shield-alt mr-1"></i> Hak Akses & Wewenang
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="profile-tab-content">
                    
                    <!-- TAB 1: INFORMASI PROFIL -->
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                        <form action="<?= base_url('profile') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <h6 class="font-weight-bold text-dark mb-3 pb-2 border-bottom">
                                <i class="fas fa-id-card-clip text-teal mr-1"></i> Biodata & Kontak Pengguna
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nama Lengkap & Gelar:</label>
                                    <input type="text" name="fullname" class="form-control" value="<?= esc($user->fullname ?: '') ?>" placeholder="Contoh: dr. Andi Wijaya, Sp.PD / Staff Medis">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Username Akun:</label>
                                    <input type="text" class="form-control" value="<?= esc($user->username) ?>" readonly style="background:#f8fafc; font-family: monospace;">
                                    <small class="text-muted" style="font-size: 10px;">Username dikunci demi keamanan audit sistem.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Alamat Email:</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Nomor Telepon / WhatsApp:</label>
                                    <input type="text" name="phone" class="form-control" value="<?= esc($user->phone ?: '') ?>" placeholder="Contoh: 081234567890">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Catatan / Bio / Spesialisasi:</label>
                                    <textarea name="bio" class="form-control" rows="3" placeholder="Tuliskan catatan singkat mengenai jabatan, unit poli, atau deskripsi tugas Anda..."><?= esc($user->bio ?: '') ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Unggah Foto Profil Baru (Opsional):</label>
                                    <input type="file" name="avatar" class="form-control-file border p-1 rounded" accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted" style="font-size: 10px;">Mendukung format JPG, PNG, WEBP (Maksimal 3MB).</small>
                                </div>
                            </div>

                            <div class="text-right border-top pt-3">
                                <button type="submit" class="btn btn-teal font-weight-bold px-4">
                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: KEAMANAN & PASSWORD -->
                    <div class="tab-pane fade" id="tab-keamanan" role="tabpanel">
                        <form action="<?= base_url('profile/update-password') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <h6 class="font-weight-bold text-dark mb-3 pb-2 border-bottom">
                                <i class="fas fa-lock text-teal mr-1"></i> Perbarui Kata Sandi / Password Akun
                            </h6>

                            <div class="alert alert-info alert-excel text-xs mb-3">
                                <i class="fas fa-shield-alt mr-1"></i> <strong>Keamanan Akun:</strong> Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol dengan panjang minimal 6 karakter.
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold text-dark">Password Saat Ini (Password Lama):</label>
                                <div class="input-group">
                                    <input type="password" id="input-old-pwd" name="old_password" class="form-control" required placeholder="Masukkan kata sandi lama Anda">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('input-old-pwd', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Password Baru:</label>
                                    <div class="input-group">
                                        <input type="password" id="input-new-pwd" name="new_password" class="form-control" minlength="6" required placeholder="Minimal 6 karakter">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('input-new-pwd', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs font-weight-bold text-dark">Konfirmasi Password Baru:</label>
                                    <div class="input-group">
                                        <input type="password" id="input-confirm-pwd" name="confirm_password" class="form-control" minlength="6" required placeholder="Ulangi password baru">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('input-confirm-pwd', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right border-top pt-3">
                                <button type="submit" class="btn btn-danger font-weight-bold px-4">
                                    <i class="fas fa-key mr-1"></i> Perbarui Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: HAK AKSES & WEWENANG -->
                    <div class="tab-pane fade" id="tab-akses" role="tabpanel">
                        <h6 class="font-weight-bold text-dark mb-2 pb-2 border-bottom">
                            <i class="fas fa-shield-halved text-teal mr-1"></i> Hak Akses & Wewenang Sistem Anda
                        </h6>
                        <p class="text-secondary text-xs mb-3">
                            Berikut adalah daftar modul dan izin akses yang diberikan kepada peran <strong><?= esc($user->role_name) ?></strong> pada akun Anda:
                        </p>

                        <?php if (session('role_name') === 'Super Admin'): ?>
                            <div class="alert alert-success alert-excel text-xs mb-3">
                                <i class="fas fa-crown mr-1"></i> <strong>Akses Penuh Super Administrator:</strong> Akun Anda memiliki izin tanpa batas untuk mengelola seluruh modul medis, keuangan, farmasi, operasional resto, HRD, dan konfigurasi sistem.
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <?php if (!empty($permissions)): ?>
                                <?php foreach ($permissions as $perm): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="p-2 border rounded bg-light d-flex align-items-center">
                                            <span class="badge badge-teal mr-2" style="font-size: 10px; font-family: monospace;">
                                                <i class="fas fa-check mr-1"></i><?= esc($perm->name) ?>
                                            </span>
                                            <span class="text-secondary text-xs"><?= esc($perm->description ?: $perm->name) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-3 text-muted text-xs">
                                    Tidak ada permission khusus yang terdaftar.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview image and auto-submit avatar
    function previewAndSubmitAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatar-img-preview').attr('src', e.target.result).show();
                $('#avatar-fallback-icon').hide();
            }
            reader.readAsDataURL(input.files[0]);

            // Submit form automatically
            setTimeout(function() {
                $('#form-quick-avatar').submit();
            }, 300);
        }
    }

    // Toggle password visibility helper
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash', 'text-teal');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash', 'text-teal');
            icon.classList.add('fa-eye');
        }
    }

    // Activate specific tab on hash change (e.g. #tab-keamanan)
    $(document).ready(function() {
        if (window.location.hash) {
            const hash = window.location.hash;
            $(`a[href="${hash}"]`).tab('show');
        }
    });
</script>
<?= $this->endSection() ?>
