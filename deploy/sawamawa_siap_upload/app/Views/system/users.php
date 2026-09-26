<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-users-gear text-teal mr-2"></i> Manajemen Pengguna & Hak Akses (RBAC)
                </h1>
                <small class="text-muted">Kelola akun staf medis, kasir, apoteker, koki resto, perawat, dan hak otorisasi peran sistem</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddUser">
                    <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna Baru
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- 4 KPI SUMMARY CARDS -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0d9488 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Total Pengguna</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalUsers ?> <small class="text-muted">Akun</small></h4>
                                <small class="text-muted">Terdaftar di sistem</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-success text-uppercase">Akun Aktif</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $activeUsers ?> <small class="text-muted">Akun</small></h4>
                                <small class="text-muted">Dapat login & beroperasi</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #8b5cf6 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-purple text-uppercase">Administrator</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $adminCount ?> <small class="text-muted">Akun</small></h4>
                                <small class="text-muted">Hak akses super user</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-purple">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card shadow-sm h-100 bg-white" style="border: 1px solid #b8b8b8; border-left: 4px solid #0284c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-xs font-weight-bold text-info text-uppercase">Tenaga Medis</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $medicalStaff ?> <small class="text-muted">Akun</small></h4>
                                <small class="text-muted">Dokter, Perawat & Apoteker</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-user-doctor fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION FOR USERS & ROLES -->
        <ul class="nav nav-pills mb-3" id="rbacTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold px-3 py-2" id="tab-users-link" data-toggle="pill" href="#tab-users" role="tab">
                    <i class="fas fa-users mr-1"></i> Direktori Pengguna (<?= count($users) ?> Akun)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold px-3 py-2" id="tab-roles-link" data-toggle="pill" href="#tab-roles" role="tab">
                    <i class="fas fa-user-shield mr-1"></i> Master Peran &amp; Hak Akses (<?= count($roles) ?> Role)
                </a>
            </li>
        </ul>

        <div class="tab-content" id="rbacTabsContent">
            <!-- TAB 1: USERS DIRECTORY -->
            <div class="tab-pane fade show active" id="tab-users" role="tabpanel">
                <div class="card card-teal card-outline shadow-sm" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-id-badge text-teal mr-1"></i> Direktori Akun &amp; Hak Akses Pengguna
                        </h5>
                        <div class="ml-auto">
                            <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddUser" id="btn-open-add-user">
                                <i class="fas fa-plus mr-1"></i> Tambah Pengguna Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th>Username Akun</th>
                                        <th>Email Resmi</th>
                                        <th>Peran Hak Akses (Role)</th>
                                        <th class="text-center" style="width: 150px;">Password Akun</th>
                                        <th class="text-center">Status Akun</th>
                                        <th>Tanggal Dibuat</th>
                                        <th style="width: 140px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($users as $u): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-dark"><i class="fas fa-user-circle text-teal mr-1"></i> <?= esc($u->username) ?></strong>
                                            </td>
                                            <td><?= esc($u->email) ?></td>
                                            <td>
                                                <span class="badge badge-teal px-2 py-1 font-weight-bold text-uppercase"><?= esc($u->role_name) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light px-2 py-1 rounded" style="border: 1px solid #d1d5db; min-width: 120px;">
                                                    <span class="pwd-mask font-weight-bold text-muted mr-2" id="pwd-text-<?= $u->id ?>" data-real="<?= esc($u->plain_password ?: 'admin123') ?>" style="letter-spacing: 2px;">••••••••</span>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-peek-pwd p-0 px-1 border-0" data-target="#pwd-text-<?= $u->id ?>" title="Lihat Password">
                                                        <i class="fas fa-eye text-teal"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $u->status === 'active' ? 'success' : 'secondary' ?> px-2 py-1 text-uppercase">
                                                    <?= strtoupper(esc($u->status)) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($u->created_at)) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-outline-info btn-xs btn-edit-user mr-1"
                                                        data-id="<?= $u->id ?>"
                                                        data-username="<?= esc($u->username) ?>"
                                                        data-email="<?= esc($u->email) ?>"
                                                        data-role="<?= esc($u->role_id) ?>"
                                                        data-status="<?= esc($u->status) ?>"
                                                        data-password="<?= esc($u->plain_password ?: '') ?>"
                                                        data-permissions='<?= json_encode($u->custom_permissions ?? []) ?>'
                                                        title="Edit Akun & Otorisasi Menu">
                                                    <i class="fas fa-user-gear"></i> Edit &amp; Akses
                                                </button>
                                                <a href="<?= base_url('system/toggle-user/' . $u->id) ?>" 
                                                    class="btn btn-outline-<?= $u->status === 'active' ? 'danger' : 'success' ?> btn-xs"
                                                    onclick="return confirm('Apakah Anda yakin ingin mengubah status pengguna ini?');"
                                                    title="<?= $u->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: ROLES & DEFAULT PERMISSIONS MASTER -->
            <div class="tab-pane fade" id="tab-roles" role="tabpanel">
                <div class="card card-primary card-outline shadow-sm" style="border: 1px solid #b8b8b8;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fas fa-user-shield text-primary mr-1"></i> Master Peran &amp; Hak Akses Bawaan (Role Master)
                            </h5>
                            <small class="d-block text-muted">Definisikan peran jabatan baru dan atur paket menu default yang otomatis tercentang.</small>
                        </div>
                        <div class="ml-auto">
                            <button class="btn btn-primary btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddRole" id="btn-open-add-role">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah Role Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">NO</th>
                                        <th style="width: 180px;">Nama Peran (Role)</th>
                                        <th>Deskripsi Tanggung Jawab</th>
                                        <th style="width: 130px;" class="text-center">Jumlah Staf</th>
                                        <th>Hak Akses Default (Menu Bawaan)</th>
                                        <th style="width: 130px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $noR = 1; foreach ($roles as $r): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $noR++ ?></td>
                                            <td>
                                                <strong class="text-dark"><i class="fas fa-shield-halved text-teal mr-1"></i> <?= esc($r->name) ?></strong>
                                            </td>
                                            <td><span class="text-muted"><?= esc($r->description ?: '-') ?></span></td>
                                            <td class="text-center">
                                                <span class="badge badge-info px-2 py-1 font-weight-bold"><?= $r->user_count ?> Pengguna</span>
                                            </td>
                                            <td>
                                                <?php if (!empty($r->permissions)): ?>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                        <?php foreach ($r->permissions as $pName): ?>
                                                            <span class="badge badge-light border text-teal px-2 py-1 font-weight-normal"><?= esc($pName) ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted font-italic text-xs">Belum ada izin default</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-outline-primary btn-xs btn-edit-role mr-1"
                                                        data-id="<?= $r->id ?>"
                                                        data-name="<?= esc($r->name) ?>"
                                                        data-description="<?= esc($r->description ?? '') ?>"
                                                        data-permissions='<?= json_encode($r->permissions ?? []) ?>'
                                                        title="Edit Nama Role & Izin Default">
                                                    <i class="fas fa-pen-to-square"></i> Edit
                                                </button>
                                                <?php if (!in_array($r->id, [1, 2])): ?>
                                                    <a href="<?= base_url('system/delete-role/' . $r->id) ?>" 
                                                       class="btn btn-outline-danger btn-xs"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus role \'<?= esc($r->name) ?>\'?');"
                                                       title="Hapus Role">
                                                        <i class="fas fa-trash-can"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-light btn-xs text-muted" disabled title="Role Sistem Dilindungi">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
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

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TAMBAH / EDIT USER & ATUR HAK AKSES MENU -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleUserModal">
                    <i class="fas fa-user-plus mr-1 text-teal"></i> Daftarkan Pengguna &amp; Atur Hak Akses
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('system/save-user') ?>" method="post" id="form-user-management">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="usr-id" value="">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Username Akun: <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="usr-username" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: dr.andi / kasir_resto" required>
                        </div>

                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Email Resmi: <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="usr-email" class="form-control form-control-sm font-weight-bold" placeholder="user@sawamawa.id" required>
                        </div>

                        <div class="col-md-5 form-group mb-2">
                            <label class="text-xs font-weight-bold">Peran Utama (Role): <span class="text-danger">*</span></label>
                            <select name="role_id" id="usr-role" class="form-control form-control-sm font-weight-bold text-teal" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r->id ?>"><?= esc($r->name) ?> (<?= esc($r->description ?: 'Otorisasi Default') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle text-teal mr-1"></i> Memilih peran otomatis mencentang menu default.</small>
                        </div>

                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Password Akun:</label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="usr-password" class="form-control font-weight-bold" placeholder="Kosongkan jika tidak ubah">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-toggle-modal-pwd" title="Lihat Password">
                                        <i class="fas fa-eye text-teal"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Default: <code>Sawamawa@2026</code></small>
                        </div>

                        <div class="col-md-3 form-group mb-2">
                            <label class="text-xs font-weight-bold">Status Akun:</label>
                            <select name="status" id="usr-status" class="form-control form-control-sm font-weight-bold">
                                <option value="active">Aktif (Dapat Login)</option>
                                <option value="inactive">Nonaktif (Diblokir)</option>
                            </select>
                        </div>
                    </div>

                    <!-- KOTAK PILIHAN CENTANG HAK AKSES MENU PER-USER -->
                    <div class="card mt-2 mb-0 shadow-none" style="border: 1px solid #b8b8b8;">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-dark text-xs text-uppercase">
                                <i class="fas fa-shield-halved text-teal mr-1"></i> Otorisasi Centang Menu &amp; Modul Pengguna
                            </span>
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-teal font-weight-bold mr-1" id="btn-select-all-perms">
                                    <i class="fas fa-check-double mr-1"></i> Centang Semua
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold" id="btn-deselect-all-perms">
                                    <i class="fas fa-times mr-1"></i> Hapus Semua
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3" style="max-height: 360px; overflow-y: auto;">
                            <!-- Group 1: Pelayanan Klinik -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-hospital-user mr-1"></i> PELAYANAN KLINIK &amp; MEDIS
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.register" class="custom-control-input chk-perm" id="perm-clinic-reg">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-clinic-reg">
                                                <strong>Pendaftaran Pasien &amp; Antrean Poli</strong>
                                                <small class="d-block text-muted">Pendaftaran pasien, nomor antrean & layar display TV</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.soap" class="custom-control-input chk-perm" id="perm-clinic-soap">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-clinic-soap">
                                                <strong>Rekam Medis (SOAP) &amp; e-Resep Dokter</strong>
                                                <small class="d-block text-muted">Pemeriksaan dokter/nakes, rujukan, surat medis & lab</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.billing" class="custom-control-input chk-perm" id="perm-clinic-bill">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-clinic-bill">
                                                <strong>Kasir Utama &amp; Billing Pasien</strong>
                                                <small class="d-block text-muted">Transaksi pembayaran klinik & cetak kwitansi resmi</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Farmasi & Apotek -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-pills mr-1"></i> FARMASI &amp; APOTEK
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="pharmacy.dispense" class="custom-control-input chk-perm" id="perm-pharm-disp">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-pharm-disp">
                                                <strong>Penjualan Obat Bebas &amp; Tebus e-Resep</strong>
                                                <small class="d-block text-muted">Kasir OTC obat bebas, penyiapan resep & etiket</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="pharmacy.stock" class="custom-control-input chk-perm" id="perm-pharm-stk">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-pharm-stk">
                                                <strong>Stok Obat &amp; Stock Opname</strong>
                                                <small class="d-block text-muted">Kartu stok, monitoring expired date FEFO & laporan</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Resto Sehat & Produk Nutrisi -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-utensils mr-1"></i> RESTO SEHAT &amp; PRODUK NUTRISI
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="resto.order" class="custom-control-input chk-perm" id="perm-resto-ord">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-resto-ord">
                                                <strong>POS Kasir Resto &amp; Skincare</strong>
                                                <small class="d-block text-muted">Order makanan sehat, open bill meja & laporan omzet</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="resto.kitchen" class="custom-control-input chk-perm" id="perm-resto-kds">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-resto-kds">
                                                <strong>Dapur Gizi (KDS)</strong>
                                                <small class="d-block text-muted">Layar antrean pesanan makanan dapur koki</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Keuangan & Akuntansi -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-wallet mr-1"></i> KEUANGAN &amp; AKUNTANSI
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="finance.manage" class="custom-control-input chk-perm" id="perm-fin-man">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-fin-man">
                                                <strong>Pengelolaan Kas &amp; Bank Operasional</strong>
                                                <small class="d-block text-muted">Buku kas masuk/keluar, fee dokter & aging piutang</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="accounting.ledger" class="custom-control-input chk-perm" id="perm-acc-led">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-acc-led">
                                                <strong>Jurnal Umum &amp; Laporan Keuangan SAK</strong>
                                                <small class="d-block text-muted">Jurnal otomatis, buku besar, laba rugi & neraca</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 5: Pengadaan & Logistik PO -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-truck-ramp-box mr-1"></i> PENGADAAN &amp; LOGISTIK (PO)
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.apply" class="custom-control-input chk-perm" id="perm-proc-app">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-proc-app">
                                                <strong>Pengajuan PO Obat</strong>
                                                <small class="d-block text-muted">Input surat pesanan PO</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.verify" class="custom-control-input chk-perm" id="perm-proc-ver">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-proc-ver">
                                                <strong>Verifikasi Anggaran</strong>
                                                <small class="d-block text-muted">Verifikasi tagihan PO</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.approve" class="custom-control-input chk-perm" id="perm-proc-apr">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-proc-apr">
                                                <strong>Persetujuan Direksi</strong>
                                                <small class="d-block text-muted">Approval pencairan PO</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 6: Aset & Kepegawaian HRD -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-id-card-clip mr-1"></i> ASET &amp; KEPEGAWAIAN (HRD)
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="inventory.manage" class="custom-control-input chk-perm" id="perm-inv-man">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-inv-man">
                                                <strong>Inventaris &amp; Aset Tetap</strong>
                                                <small class="d-block text-muted">Kelola aset, penyusutan & label barcode</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="hrd.payroll" class="custom-control-input chk-perm" id="perm-hrd-pay">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-hrd-pay">
                                                <strong>Data Pegawai, Presensi &amp; Payroll</strong>
                                                <small class="d-block text-muted">Absensi, slip gaji, cuti & matriks KPI</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 7: Administrasi & Keamanan Sistem -->
                            <div>
                                <div class="font-weight-bold text-teal text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-sliders mr-1"></i> MASTER DATA &amp; SISTEM
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="system.settings" class="custom-control-input chk-perm" id="perm-sys-set">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-sys-set">
                                                <strong>Pengaturan Klinik &amp; WA</strong>
                                                <small class="d-block text-muted">Konfigurasi profil & gateway</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="users.manage" class="custom-control-input chk-perm" id="perm-sys-usr">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-sys-usr">
                                                <strong>Kelola User &amp; Role</strong>
                                                <small class="d-block text-muted">Akun login & otorisasi RBAC</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="audit.view" class="custom-control-input chk-perm" id="perm-sys-aud">
                                            <label class="custom-control-label text-xs cursor-pointer" for="perm-sys-aud">
                                                <strong>Log Audit &amp; Keamanan</strong>
                                                <small class="d-block text-muted">Monitoring aktivitas user</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Pengguna &amp; Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: TAMBAH / EDIT ROLE JABATAN & DEFAULT PERMISSIONS -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleRoleModal">
                    <i class="fas fa-plus-circle mr-1 text-primary"></i> Tambah Peran Jabatan Baru (Role Master)
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('system/save-role') ?>" method="post" id="form-role-management">
                <?= csrf_field() ?>
                <input type="hidden" name="role_id" id="role-id" value="">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Peran / Jabatan: <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="role-name" class="form-control form-control-sm font-weight-bold" placeholder="Contoh: Fisioterapis / Bidan / Kurir" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Deskripsi Tanggung Jawab:</label>
                            <input type="text" name="description" id="role-description" class="form-control form-control-sm" placeholder="Contoh: Menangani pelayanan fisioterapi dan rehab medik">
                        </div>
                    </div>

                    <!-- KOTAK PILIHAN CENTANG HAK AKSES DEFAULT UNTUK ROLE INI -->
                    <div class="card mt-2 mb-0 shadow-none" style="border: 1px solid #b8b8b8;">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-dark text-xs text-uppercase">
                                <i class="fas fa-shield-halved text-primary mr-1"></i> Paket Menu Bawaan (Default Permissions) untuk Role Ini
                            </span>
                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold mr-1" id="btn-role-select-all">
                                    <i class="fas fa-check-double mr-1"></i> Centang Semua
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold" id="btn-role-deselect-all">
                                    <i class="fas fa-times mr-1"></i> Hapus Semua
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3" style="max-height: 360px; overflow-y: auto;">
                            <!-- Group 1: Pelayanan Klinik -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-hospital-user mr-1"></i> PELAYANAN KLINIK &amp; MEDIS
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.register" class="custom-control-input chk-role-perm" id="roleperm-clinic-reg">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-clinic-reg">
                                                <strong>Pendaftaran Pasien &amp; Antrean Poli</strong>
                                                <small class="d-block text-muted">Pendaftaran pasien & nomor antrean</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.soap" class="custom-control-input chk-role-perm" id="roleperm-clinic-soap">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-clinic-soap">
                                                <strong>Rekam Medis (SOAP) &amp; e-Resep Dokter</strong>
                                                <small class="d-block text-muted">Pemeriksaan dokter/nakes, surat medis & lab</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="clinic.billing" class="custom-control-input chk-role-perm" id="roleperm-clinic-bill">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-clinic-bill">
                                                <strong>Kasir Utama &amp; Billing Pasien</strong>
                                                <small class="d-block text-muted">Transaksi pembayaran kasir klinik</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Farmasi & Apotek -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-pills mr-1"></i> FARMASI &amp; APOTEK
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="pharmacy.dispense" class="custom-control-input chk-role-perm" id="roleperm-pharm-disp">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-pharm-disp">
                                                <strong>Penjualan Obat Bebas &amp; Tebus e-Resep</strong>
                                                <small class="d-block text-muted">Kasir OTC obat & penyiapan e-resep</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="pharmacy.stock" class="custom-control-input chk-role-perm" id="roleperm-pharm-stk">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-pharm-stk">
                                                <strong>Stok Obat &amp; Stock Opname</strong>
                                                <small class="d-block text-muted">Kartu stok, monitoring ED & laporan</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Resto Sehat & Produk Nutrisi -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-utensils mr-1"></i> RESTO SEHAT &amp; PRODUK NUTRISI
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="resto.order" class="custom-control-input chk-role-perm" id="roleperm-resto-ord">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-resto-ord">
                                                <strong>POS Kasir Resto &amp; Skincare</strong>
                                                <small class="d-block text-muted">Order makanan sehat & open bill meja</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="resto.kitchen" class="custom-control-input chk-role-perm" id="roleperm-resto-kds">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-resto-kds">
                                                <strong>Dapur Gizi (KDS)</strong>
                                                <small class="d-block text-muted">Layar antrean dapur koki</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Keuangan & Akuntansi -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-wallet mr-1"></i> KEUANGAN &amp; AKUNTANSI
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="finance.manage" class="custom-control-input chk-role-perm" id="roleperm-fin-man">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-fin-man">
                                                <strong>Pengelolaan Kas &amp; Bank Operasional</strong>
                                                <small class="d-block text-muted">Kas masuk/keluar & aging piutang</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="accounting.ledger" class="custom-control-input chk-role-perm" id="roleperm-acc-led">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-acc-led">
                                                <strong>Jurnal Umum &amp; Laporan Keuangan SAK</strong>
                                                <small class="d-block text-muted">Jurnal otomatis, buku besar & neraca</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 5: Pengadaan & Logistik PO -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-truck-ramp-box mr-1"></i> PENGADAAN &amp; LOGISTIK (PO)
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.apply" class="custom-control-input chk-role-perm" id="roleperm-proc-app">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-proc-app">
                                                <strong>Pengajuan PO Obat</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.verify" class="custom-control-input chk-role-perm" id="roleperm-proc-ver">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-proc-ver">
                                                <strong>Verifikasi Anggaran</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="procurement.approve" class="custom-control-input chk-role-perm" id="roleperm-proc-apr">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-proc-apr">
                                                <strong>Persetujuan Direksi</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 6: Aset & Kepegawaian HRD -->
                            <div class="mb-3">
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-id-card-clip mr-1"></i> ASET &amp; KEPEGAWAIAN (HRD)
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="inventory.manage" class="custom-control-input chk-role-perm" id="roleperm-inv-man">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-inv-man">
                                                <strong>Inventaris &amp; Aset Tetap</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="hrd.payroll" class="custom-control-input chk-role-perm" id="roleperm-hrd-pay">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-hrd-pay">
                                                <strong>Data Pegawai, Presensi &amp; Payroll</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 7: Administrasi & Keamanan Sistem -->
                            <div>
                                <div class="font-weight-bold text-primary text-xs mb-2 border-bottom pb-1">
                                    <i class="fas fa-sliders mr-1"></i> MASTER DATA &amp; SISTEM
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="system.settings" class="custom-control-input chk-role-perm" id="roleperm-sys-set">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-sys-set">
                                                <strong>Pengaturan Klinik</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="users.manage" class="custom-control-input chk-role-perm" id="roleperm-sys-usr">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-sys-usr">
                                                <strong>Kelola User &amp; Role</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="permissions[]" value="audit.view" class="custom-control-input chk-role-perm" id="roleperm-sys-aud">
                                            <label class="custom-control-label text-xs cursor-pointer" for="roleperm-sys-aud">
                                                <strong>Log Audit &amp; Keamanan</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Role Jabatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .badge-teal { background-color: #0d9488; color: #fff; }
    .btn-teal { background-color: #0d9488; color: #fff; border-color: #0d9488; }
    .btn-teal:hover { background-color: #0f766e; color: #fff; border-color: #0f766e; }
    .btn-outline-teal { color: #0d9488; border-color: #0d9488; }
    .btn-outline-teal:hover { background-color: #0d9488; color: #fff; }
    .text-purple { color: #8b5cf6 !important; }
    .nav-pills .nav-link.active {
        background-color: #0d9488 !important;
        color: #fff !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rolePermissionsMap = <?= json_encode($rolePermissionsMap ?? []) ?>;

        // Auto-switch to tab from URL hash (e.g. #tab-roles)
        if (window.location.hash) {
            const hash = window.location.hash;
            if (hash === '#tab-roles') {
                $('#tab-roles-link').tab('show');
            } else {
                $('#tab-users-link').tab('show');
            }
        }
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            window.location.hash = $(e.target).attr('href');
            // Adjust DataTables layout on tab switch
            if ($.fn.dataTable) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            }
        });

        // Function to check default checkboxes when role changes
        function applyPermissionsForRole(roleId) {
            $('.chk-perm').prop('checked', false);
            const defaultPerms = rolePermissionsMap[roleId] || [];
            defaultPerms.forEach(function(perm) {
                $(`.chk-perm[value="${perm}"]`).prop('checked', true);
            });
        }

        // Helper to safely parse permissions array from data attribute
        function parsePermissions(raw) {
            if (!raw) return [];
            if (Array.isArray(raw)) return raw;
            if (typeof raw === 'string') {
                try {
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }
            return [];
        }

        // =========================================================================
        // 1. LIHAT / SEMBUNYIKAN PASSWORD (DELEGATED EVENT UNTUK DATATABLES)
        // =========================================================================
        $(document).on('click', '.btn-peek-pwd', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const targetSelector = $btn.attr('data-target') || $btn.data('target');
            const $target = $(targetSelector);
            
            if (!$target.length) return;

            const realPwd = String($target.attr('data-real') !== undefined ? $target.attr('data-real') : ($target.data('real') || ''));
            const $icon = $btn.find('i');
            const currentText = $target.text().trim();

            if (currentText.indexOf('•') !== -1 || currentText === '••••••••') {
                // Tampilkan plain password asli
                $target.text(realPwd || '(Kosong)')
                       .removeClass('text-muted')
                       .addClass('text-teal font-weight-bold')
                       .css('letter-spacing', 'normal');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                $btn.attr('title', 'Sembunyikan Password');
            } else {
                // Sembunyikan kembali menjadi bulatan mask
                $target.text('••••••••')
                       .removeClass('text-teal font-weight-bold')
                       .addClass('text-muted font-weight-bold')
                       .css('letter-spacing', '2px');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                $btn.attr('title', 'Lihat Password');
            }
        });

        // Toggle modal password input visibility
        $(document).on('click', '#btn-toggle-modal-pwd', function(e) {
            e.preventDefault();
            const input = $('#usr-password');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
                $(this).attr('title', 'Sembunyikan Password');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
                $(this).attr('title', 'Lihat Password');
            }
        });

        // When changing role in dropdown, automatically apply default role permissions
        $('#usr-role').on('change', function() {
            applyPermissionsForRole($(this).val());
        });

        // Select All / Deselect All Permission Checkboxes for User
        $('#btn-select-all-perms').on('click', function() {
            $('.chk-perm').prop('checked', true);
        });
        $('#btn-deselect-all-perms').on('click', function() {
            $('.chk-perm').prop('checked', false);
        });

        // Select All / Deselect All Permission Checkboxes for Role
        $('#btn-role-select-all').on('click', function() {
            $('.chk-role-perm').prop('checked', true);
        });
        $('#btn-role-deselect-all').on('click', function() {
            $('.chk-role-perm').prop('checked', false);
        });

        // =========================================================================
        // 2. EDIT USER & OTORISASI AKSES (DELEGATED EVENT)
        // =========================================================================
        $(document).on('click', '.btn-edit-user', function(e) {
            e.preventDefault();
            const $btn = $(this);

            const userId = $btn.attr('data-id') || $btn.data('id');
            const username = $btn.attr('data-username') || $btn.data('username') || '';
            const email = $btn.attr('data-email') || $btn.data('email') || '';
            const roleId = $btn.attr('data-role') || $btn.data('role') || '';
            const status = $btn.attr('data-status') || $btn.data('status') || 'active';
            const password = $btn.attr('data-password') || $btn.data('password') || '';
            const rawPerms = $btn.attr('data-permissions') || $btn.data('permissions');
            const userPerms = parsePermissions(rawPerms);

            $('#titleUserModal').html('<i class="fas fa-user-pen mr-1 text-teal"></i> Edit Akun &amp; Otorisasi Menu Pengguna');
            $('#usr-id').val(userId);
            $('#usr-username').val(username);
            $('#usr-email').val(email);
            $('#usr-role').val(roleId);
            $('#usr-status').val(status);
            $('#usr-password').val(password);
            $('#usr-password').attr('type', 'password');
            $('#btn-toggle-modal-pwd i').removeClass('fa-eye-slash').addClass('fa-eye');

            // Load user-specific custom permissions
            $('.chk-perm').prop('checked', false);
            if (userPerms.length > 0) {
                userPerms.forEach(function(perm) {
                    $(`.chk-perm[value="${perm}"]`).prop('checked', true);
                });
            } else {
                applyPermissionsForRole(roleId);
            }

            $('#modalAddUser').modal('show');
        });

        // Reset Add User Modal
        $(document).on('click', '#btn-open-add-user, [data-target="#modalAddUser"]', function() {
            $('#titleUserModal').html('<i class="fas fa-user-plus mr-1 text-teal"></i> Daftarkan Pengguna &amp; Atur Hak Akses');
            $('#usr-id').val('');
            $('#usr-username').val('');
            $('#usr-email').val('');
            $('#usr-password').val('');
            $('#usr-password').attr('type', 'password');
            $('#btn-toggle-modal-pwd i').removeClass('fa-eye-slash').addClass('fa-eye');
            const firstRole = $('#usr-role option:first').val();
            $('#usr-role').val(firstRole);
            applyPermissionsForRole(firstRole);
        });

        // =========================================================================
        // 3. EDIT ROLE & PERMISSIONS DEFAULT (DELEGATED EVENT)
        // =========================================================================
        $(document).on('click', '.btn-edit-role', function(e) {
            e.preventDefault();
            const $btn = $(this);

            const roleId = $btn.attr('data-id') || $btn.data('id');
            const roleName = $btn.attr('data-name') || $btn.data('name') || '';
            const roleDesc = $btn.attr('data-description') || $btn.data('description') || '';
            const rawPerms = $btn.attr('data-permissions') || $btn.data('permissions');
            const rolePerms = parsePermissions(rawPerms);

            $('#titleRoleModal').html('<i class="fas fa-pen-to-square mr-1 text-primary"></i> Edit Peran &amp; Hak Akses Default');
            $('#role-id').val(roleId);
            $('#role-name').val(roleName);
            $('#role-description').val(roleDesc);

            $('.chk-role-perm').prop('checked', false);
            rolePerms.forEach(function(perm) {
                $(`.chk-role-perm[value="${perm}"]`).prop('checked', true);
            });

            $('#modalAddRole').modal('show');
        });

        // Reset Add Role Modal
        $(document).on('click', '#btn-open-add-role', function() {
            $('#titleRoleModal').html('<i class="fas fa-plus-circle mr-1 text-primary"></i> Tambah Peran Jabatan Baru (Role Master)');
            $('#role-id').val('');
            $('#role-name').val('');
            $('#role-description').val('');
            $('.chk-role-perm').prop('checked', false);
        });
    });
</script>
<?= $this->endSection() ?>
