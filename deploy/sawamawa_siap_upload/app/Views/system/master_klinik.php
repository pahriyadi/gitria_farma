<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-teal shadow">
            <div class="card-header p-2">
                <ul class="nav nav-pills" id="masterTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="cat-tab" data-toggle="tab" href="#cat" role="tab" aria-controls="cat" aria-selected="true">
                            <i class="fas fa-layer-group mr-1"></i> 1. Kategori Pelayanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="subcat-tab" data-toggle="tab" href="#subcat" role="tab" aria-controls="subcat" aria-selected="false">
                            <i class="fas fa-sitemap mr-1"></i> 2. Sub Kategori & Sub Kecil (Tarif)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="doctor-tab" data-toggle="tab" href="#doctor" role="tab" aria-controls="doctor" aria-selected="false">
                            <i class="fas fa-user-md mr-1"></i> 3. Dokter & TTD
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="rooms-tab" data-toggle="tab" href="#rooms" role="tab" aria-controls="rooms" aria-selected="false">
                            <i class="fas fa-door-open mr-1"></i> 4. Ruangan & Bed
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="pharmacy-tab" data-toggle="tab" href="#pharmacy" role="tab" aria-controls="pharmacy" aria-selected="false">
                            <i class="fas fa-pills mr-1"></i> 5. Kategori & Satuan Obat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="consent-tab" data-toggle="tab" href="#consent" role="tab" aria-controls="consent" aria-selected="false">
                            <i class="fas fa-file-contract mr-1"></i> 6. Template Edukasi / Consent
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="hrd-tab" data-toggle="tab" href="#hrd" role="tab" aria-controls="hrd" aria-selected="false">
                            <i class="fas fa-sitemap mr-1"></i> 7. Departemen & Jabatan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="lab-tab" data-toggle="tab" href="#lab" role="tab" aria-controls="lab" aria-selected="false">
                            <i class="fas fa-vial mr-1"></i> 8. Uji Laboratorium
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="insurance-tab" data-toggle="tab" href="#insurance" role="tab" aria-controls="insurance" aria-selected="false">
                            <i class="fas fa-handshake mr-1"></i> 9. Mitra Asuransi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="schedule-tab" data-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="false">
                            <i class="fas fa-calendar-alt mr-1"></i> 10. Jadwal Dokter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="supplier-tab" data-toggle="tab" href="#supplier" role="tab" aria-controls="supplier" aria-selected="false">
                            <i class="fas fa-truck mr-1"></i> 11. Distributor PBF
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="masterTabContent">
                    
                    <!-- ========================================== -->
                    <!-- TAB 1: KATEGORI PELAYANAN -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade show active" id="cat" role="tabpanel" aria-labelledby="cat-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-layer-group text-teal"></i> TABEL REFERENSI UMUM - KATEGORI PELAYANAN</h5>
                                <small class="text-muted">Master klasifikasi induk utama pelayanan (POLI, TINDAKAN, dll)</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#catAddModal">
                                <i class="fas fa-plus mr-1"></i> Tambah Kategori Pelayanan
                            </button>
                        </div>

                        <table class="table table-striped table-bordered table-hover datatable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 80px;" class="text-center">ID</th>
                                    <th>NAMA KATEGORI PELAYANAN</th>
                                    <th>KETERANGAN / DESKRIPSI</th>
                                    <th style="width: 150px;" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold"><code><?= $c->id ?></code></td>
                                        <td>
                                            <span class="badge badge-teal px-3 py-2 text-md font-weight-bold">
                                                <?= esc($c->name) ?>
                                            </span>
                                        </td>
                                        <td><?= esc($c->description ?? '-') ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs btn-edit-cat" data-id="<?= $c->id ?>" data-name="<?= esc($c->name) ?>" data-desc="<?= esc($c->description) ?>" data-toggle="modal" data-target="#catEditModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="<?= base_url('system/master-klinik/category') ?>" method="post" class="d-inline-block" onsubmit="return confirm('Hapus kategori ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $c->id ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 2: SUB KATEGORI & SUB KECIL PELAYANAN -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="subcat" role="tabpanel" aria-labelledby="subcat-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-sitemap text-teal"></i> SUB KATEGORI & SUB KECIL PELAYANAN</h5>
                                <small class="text-muted">Daftar Sub Kategori (Poli / Induk Tindakan) dan Sub Kecil Layanan beserta Tarif</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-teal font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#polyAddModal">
                                    <i class="fas fa-plus mr-1"></i> + Sub Poli
                                </button>
                                <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#serviceAddModal">
                                    <i class="fas fa-plus mr-1"></i> + Sub Tindakan & Tarif
                                </button>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">ID</th>
                                    <th style="width: 140px;">ID - KATEGORI</th>
                                    <th>SUB KATEGORI PELAYANAN</th>
                                    <th>SUB KECIL KATEGORI PELAYANAN</th>
                                    <th style="width: 160px;" class="text-right">HARGA (TARIF)</th>
                                    <th style="width: 90px;" class="text-center">STATUS</th>
                                    <th style="width: 130px;" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 1. POLIKLINIKS (Kategori POLI) -->
                                <?php foreach ($polikliniks as $p): ?>
                                    <tr class="bg-white">
                                        <td class="text-center font-weight-bold"><code><?= $p->id ?></code></td>
                                        <td>
                                            <span class="badge badge-info font-weight-bold">
                                                <?= $p->category_id ?? 1 ?> - POLI
                                            </span>
                                        </td>
                                        <td><strong><?= esc($p->name) ?></strong></td>
                                        <td class="text-muted text-center">-</td>
                                        <td class="text-right font-weight-bold text-dark">Rp 0,00</td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $p->status === 'active' ? 'success' : 'danger' ?>">
                                                <?= strtoupper($p->status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs btn-edit-poly" data-id="<?= $p->id ?>" data-name="<?= esc($p->name) ?>" data-desc="<?= esc($p->description) ?>" data-cat="<?= $p->category_id ?>" data-status="<?= $p->status ?>" data-toggle="modal" data-target="#polyEditModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="<?= base_url('system/master-klinik/poliklinik-baru') ?>" method="post" class="d-inline-block" onsubmit="return confirm('Hapus poliklinik ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $p->id ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- 2. TINDAKAN & SUB TINDAKAN (Kategori TINDAKAN) -->
                                <?php foreach ($tindakanList as $t): ?>
                                    <tr class="<?= $t->parent_id ? 'bg-light' : '' ?>">
                                        <td class="text-center font-weight-bold"><code><?= $t->id ?></code></td>
                                        <td>
                                            <span class="badge badge-secondary font-weight-bold">
                                                <?= $t->category_id ?? 2 ?> - <?= esc($t->category_name ?? 'TINDAKAN') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($t->parent_name): ?>
                                                <span class="text-teal font-weight-bold"><i class="fas fa-folder-open mr-1"></i> <?= esc($t->parent_name) ?></span>
                                            <?php elseif ($t->price == 0): ?>
                                                <strong><i class="fas fa-folder text-warning mr-1"></i> <?= esc($t->name) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($t->parent_id): ?>
                                                <span class="ml-2"><i class="fas fa-level-up-alt fa-rotate-90 text-muted mr-1"></i> <strong><?= esc($t->name) ?></strong></span>
                                            <?php elseif ($t->price > 0): ?>
                                                <strong><?= esc($t->name) ?></strong>
                                            <?php else: ?>
                                                <span class="badge badge-light border">Induk (Punya Sub)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            Rp <?= number_format($t->price ?? 0, 2, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $t->status === 'active' ? 'success' : 'danger' ?>">
                                                <?= strtoupper($t->status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-xs btn-edit-service" data-id="<?= $t->id ?>" data-code="<?= esc($t->code) ?>" data-name="<?= esc($t->name) ?>" data-desc="<?= esc($t->description) ?>" data-cat="<?= $t->category_id ?>" data-parent="<?= $t->parent_id ?>" data-price="<?= $t->price ?>" data-status="<?= $t->status ?>" data-toggle="modal" data-target="#serviceEditModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="<?= base_url('system/master-klinik/service') ?>" method="post" class="d-inline-block" onsubmit="return confirm('Hapus tindakan ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $t->id ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 3: DATA DOKTER & FEE PER PASIEN -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="doctor" role="tabpanel" aria-labelledby="doctor-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-user-md text-teal"></i> DATA DOKTER & FEE PER PASIEN</h5>
                                <small class="text-muted">Pemetaan Dokter Pelaksana terhadap Kategori, Sub Kategori, dan Hak Komisi Fee Per Pasien</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#doctorAddModal">
                                <i class="fas fa-plus mr-1"></i> Tambah Penugasan Dokter
                            </button>
                        </div>

                        <table class="table table-bordered table-striped table-hover datatable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">ID</th>
                                    <th>NAMA DOKTER</th>
                                    <th>ID - KATEGORI PELAYANAN</th>
                                    <th>ID - SUB KATEGORI PELAYANAN</th>
                                    <th>ID - SUB KECIL PELAYANAN</th>
                                    <th style="width: 130px;" class="text-right">FEE DOKTER</th>
                                    <th style="width: 110px;" class="text-center">TTD / STEMPEL</th>
                                    <th style="width: 80px;" class="text-center">STATUS</th>
                                    <th style="width: 170px;" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctors as $d): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold"><code><?= $d->id ?></code></td>
                                        <td>
                                            <strong><?= esc($d->name) ?></strong>
                                            <div class="text-xs text-muted">NIK: <?= esc($d->nik_employee) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $d->category_id == 1 ? 'info' : 'secondary' ?> font-weight-bold">
                                                <?= $d->category_id ?> - <?= esc($d->category_name ?? ($d->category_id == 1 ? 'POLI' : 'TINDAKAN')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($d->category_id == 1): ?>
                                                <span class="text-teal font-weight-bold"><?= esc($d->polyclinic_name ?? '-') ?></span>
                                            <?php elseif ($d->parent_tindakan_name): ?>
                                                <span class="text-teal font-weight-bold"><?= esc($d->parent_tindakan_name) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($d->category_id == 1): ?>
                                                <span class="text-muted">-</span>
                                            <?php elseif ($d->tindakan_name): ?>
                                                <strong><?= esc($d->tindakan_name) ?></strong>
                                                <?php if ($d->tindakan_price > 0): ?>
                                                    <span class="badge badge-light border ml-1">Tarif: Rp <?= number_format($d->tindakan_price, 0, ',', '.') ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right font-weight-bold text-primary">
                                            <?php if ($d->fee_per_pasien > 0): ?>
                                                Rp <?= number_format($d->fee_per_pasien, 2, ',', '.') ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($d->digital_signature)): ?>
                                                <div class="d-inline-flex align-items-center p-1 rounded bg-light border" title="Tanda Tangan Digital Terdaftar">
                                                    <img src="<?= $d->digital_signature ?>" alt="TTD" style="max-height: 24px; max-width: 60px;">
                                                </div>
                                                <span class="badge badge-success d-block mt-0.5" style="font-size: 8.5px;">TTD Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-light border text-muted" style="font-size: 9px;">Belum Ada TTD</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $d->status === 'active' ? 'success' : 'danger' ?>">
                                                <?= strtoupper($d->status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-teal btn-xs btn-doc-signature font-weight-bold"
                                                data-id="<?= $d->id ?>"
                                                data-name="<?= esc($d->name) ?>"
                                                data-sip="<?= esc($d->sip_number) ?>"
                                                data-sig="<?= esc($d->digital_signature ?? '') ?>"
                                                data-stamp="<?= esc($d->stamp_image ?? '') ?>"
                                                title="Atur Tanda Tangan & Stempel Referensi Dokter">
                                                <i class="fas fa-signature mr-1"></i> TTD
                                            </button>
                                            <button class="btn btn-info btn-xs btn-edit-doctor" 
                                                data-id="<?= $d->id ?>" 
                                                data-nik="<?= esc($d->nik_employee) ?>" 
                                                data-name="<?= esc($d->name) ?>" 
                                                data-cat="<?= $d->category_id ?>"
                                                data-poly="<?= $d->polyclinic_id ?>"
                                                data-tindakan="<?= $d->tindakan_id ?>"
                                                data-fee="<?= $d->fee_per_pasien ?>"
                                                data-sip="<?= esc($d->sip_number) ?>" 
                                                data-str="<?= esc($d->str_number) ?>" 
                                                data-expiry="<?= $d->str_expiry ?>" 
                                                data-status="<?= $d->status ?>" 
                                                data-toggle="modal" 
                                                data-target="#doctorEditModal"
                                                title="Edit Dokter">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="<?= base_url('system/master-klinik/doctor') ?>" method="post" class="d-inline-block mb-0" onsubmit="return confirm('Hapus data dokter ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $d->id ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 4: RUANGAN & TEMPAT TIDUR (BED)        -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-door-open text-teal"></i> MASTER RUANGAN &amp; TEMPAT TIDUR</h5>
                                <small class="text-muted">Manajemen ruang poliklinik, ruang tindakan, ruang observasi/infus, apotek, kasir, dan laboratorium.</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-teal font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#bedAddModal">
                                    <i class="fas fa-bed mr-1"></i> + Tambah Bed
                                </button>
                                <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#roomAddModal">
                                    <i class="fas fa-plus mr-1"></i> + Tambah Ruangan
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-building text-teal mr-1"></i> Daftar Ruangan Klinik</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>KODE</th>
                                                    <th>NAMA RUANGAN</th>
                                                    <th>TIPE / LANTAI</th>
                                                    <th>KAPASITAS</th>
                                                    <th>STATUS</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rooms as $r): ?>
                                                    <tr>
                                                        <td class="font-weight-bold font-mono text-teal"><?= esc($r->code) ?></td>
                                                        <td><strong><?= esc($r->name) ?></strong></td>
                                                        <td>
                                                            <span class="badge badge-info"><?= strtoupper($r->type) ?></span>
                                                            <small class="text-muted d-block"><?= esc($r->floor) ?></small>
                                                        </td>
                                                        <td><?= $r->capacity ?> Bed/Orang</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-<?= $r->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($r->status) ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-room" 
                                                                data-id="<?= $r->id ?>" 
                                                                data-code="<?= esc($r->code) ?>" 
                                                                data-name="<?= esc($r->name) ?>" 
                                                                data-type="<?= esc($r->type) ?>" 
                                                                data-floor="<?= esc($r->floor) ?>" 
                                                                data-capacity="<?= $r->capacity ?>" 
                                                                data-tariff="<?= $r->tariff_per_day ?>" 
                                                                data-status="<?= $r->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#roomEditModal" title="Edit Ruangan">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/room') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus ruangan ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $r->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-bed text-teal mr-1"></i> Tempat Tidur (Beds)</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>BED</th>
                                                    <th>LOKASI RUANG</th>
                                                    <th>TARIF / HARI</th>
                                                    <th>STATUS</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($beds as $b): ?>
                                                    <tr>
                                                        <td class="font-weight-bold text-dark"><?= esc($b->bed_number) ?></td>
                                                        <td><?= esc($b->room_name ?? '-') ?></td>
                                                        <td>Rp <?= number_format($b->tariff_per_day, 0, ',', '.') ?></td>
                                                        <td class="text-center">
                                                            <span class="badge badge-<?= $b->status === 'available' ? 'success' : ($b->status === 'occupied' ? 'warning' : 'secondary') ?>">
                                                                <?= strtoupper($b->status) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-bed" 
                                                                data-id="<?= $b->id ?>" 
                                                                data-room="<?= $b->room_id ?>" 
                                                                data-bed="<?= esc($b->bed_number) ?>" 
                                                                data-tariff="<?= $b->tariff_per_day ?>" 
                                                                data-status="<?= $b->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#bedEditModal" title="Edit Bed">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/bed') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus bed ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $b->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
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

                    <!-- ========================================== -->
                    <!-- TAB 5: KATEGORI & SATUAN OBAT (FARMASI)    -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="pharmacy" role="tabpanel" aria-labelledby="pharmacy-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-pills text-teal"></i> MASTER KATEGORI &amp; SATUAN OBAT</h5>
                                <small class="text-muted">Pengelompokan golongan obat standar BPOM/Kemenkes dan satuan takaran/UOM farmasi.</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-teal font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#unitAddModal">
                                    <i class="fas fa-balance-scale mr-1"></i> + Tambah Satuan UOM
                                </button>
                                <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#medCatAddModal">
                                    <i class="fas fa-plus mr-1"></i> + Kategori Obat
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-7">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-capsules text-teal mr-1"></i> Golongan &amp; Kategori Farmasi</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>KODE</th>
                                                    <th>NAMA KATEGORI</th>
                                                    <th>GOLONGAN REGULASI</th>
                                                    <th>STATUS</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($medicineCategories as $mc): ?>
                                                    <tr>
                                                        <td class="font-mono text-teal font-weight-bold"><?= esc($mc->code) ?></td>
                                                        <td>
                                                            <strong><?= esc($mc->name) ?></strong>
                                                            <small class="text-muted d-block"><?= esc($mc->description ?? '') ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?= strpos($mc->drug_class, 'keras') !== false ? 'danger' : (strpos($mc->drug_class, 'bebas') !== false ? 'success' : 'info') ?>">
                                                                <?= strtoupper(str_replace('_', ' ', $mc->drug_class)) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-<?= $mc->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($mc->status) ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-medcat" 
                                                                data-id="<?= $mc->id ?>" 
                                                                data-code="<?= esc($mc->code) ?>" 
                                                                data-name="<?= esc($mc->name) ?>" 
                                                                data-class="<?= esc($mc->drug_class) ?>" 
                                                                data-desc="<?= esc($mc->description ?? '') ?>" 
                                                                data-status="<?= $mc->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#medCatEditModal" title="Edit Kategori">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/medicine-category') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus kategori obat ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $mc->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-ruler text-teal mr-1"></i> Satuan Ukuran / UOM</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>KODE</th>
                                                    <th>NAMA SATUAN</th>
                                                    <th>KATEGORI</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($units as $u): ?>
                                                    <tr>
                                                        <td class="font-mono font-weight-bold"><?= esc($u->code) ?></td>
                                                        <td><strong><?= esc($u->name) ?></strong></td>
                                                        <td><span class="badge badge-secondary"><?= strtoupper($u->category) ?></span></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-unit" 
                                                                data-id="<?= $u->id ?>" 
                                                                data-code="<?= esc($u->code) ?>" 
                                                                data-name="<?= esc($u->name) ?>" 
                                                                data-category="<?= esc($u->category) ?>" 
                                                                data-status="<?= $u->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#unitEditModal" title="Edit Satuan">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/unit') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus satuan ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $u->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
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

                    <!-- ========================================== -->
                    <!-- TAB 6: TEMPLATE INFORMED CONSENT & EDUKASI -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="consent" role="tabpanel" aria-labelledby="consent-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-file-contract text-teal"></i> TEMPLATE INFORMED CONSENT &amp; EDUKASI TINDAKAN (LEMBAR 4 &amp; 5)</h5>
                                <small class="text-muted">Master draf baku penjelasan indikasi, prosedur, manfaat, risiko, komplikasi, dan prognosis tindakan medis klinis.</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#consentTemplateAddModal">
                                <i class="fas fa-plus mr-1"></i> + Tambah Template Consent
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 200px;">JUDUL TEMPLATE</th>
                                        <th style="width: 180px;">INDIKASI &amp; PROSEDUR</th>
                                        <th>RISIKO, KOMPLIKASI &amp; PROGNOSIS</th>
                                        <th>ALTERNATIF TERAPI</th>
                                        <th style="width: 80px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consentTemplates as $ct): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-teal"><?= esc($ct->template_title) ?></strong>
                                                <?php if (!empty($ct->tindakan_name)): ?>
                                                    <span class="badge badge-light border d-block mt-1">Tindakan: <?= esc($ct->tindakan_name) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="text-xs"><strong>Indikasi:</strong> <?= esc($ct->diagnosis_indication) ?></div>
                                                <div class="text-xs text-muted mt-1"><strong>Tindakan:</strong> <?= esc($ct->procedure_action) ?></div>
                                            </td>
                                            <td>
                                                <div class="text-xs"><strong class="text-danger">Risiko/Komplikasi:</strong> <?= esc($ct->risks_complications) ?></div>
                                                <div class="text-xs text-success mt-1"><strong>Prognosis:</strong> <?= esc($ct->prognosis) ?></div>
                                            </td>
                                            <td class="text-xs"><?= esc($ct->alternative_therapies) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-info btn-xs btn-edit-consent" 
                                                    data-id="<?= $ct->id ?>" 
                                                    data-tindakan="<?= $ct->tindakan_id ?? '' ?>" 
                                                    data-title="<?= esc($ct->template_title) ?>" 
                                                    data-diag="<?= esc($ct->diagnosis_indication ?? '') ?>" 
                                                    data-proc="<?= esc($ct->procedure_action ?? '') ?>" 
                                                    data-goal="<?= esc($ct->goal_benefits ?? '') ?>" 
                                                    data-risks="<?= esc($ct->risks_complications ?? '') ?>" 
                                                    data-prog="<?= esc($ct->prognosis ?? '') ?>" 
                                                    data-alt="<?= esc($ct->alternative_therapies ?? '') ?>" 
                                                    data-status="<?= $ct->status ?>" 
                                                    data-toggle="modal" 
                                                    data-target="#consentTemplateEditModal" title="Edit Template">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="<?= base_url('system/master-klinik/consent-template') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus template ini?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $ct->id ?>">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 7: DEPARTEMEN & JABATAN SDM            -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="hrd" role="tabpanel" aria-labelledby="hrd-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-sitemap text-teal"></i> MASTER DEPARTEMEN &amp; JABATAN PEGAWAI</h5>
                                <small class="text-muted">Struktur divisi dan hierarki posisi kerja karyawan klinik.</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-teal font-weight-bold shadow-sm mr-2" data-toggle="modal" data-target="#jobAddModal">
                                    <i class="fas fa-user-tag mr-1"></i> + Tambah Jabatan
                                </button>
                                <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#deptAddModal">
                                    <i class="fas fa-plus mr-1"></i> + Tambah Departemen
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-building text-teal mr-1"></i> Departemen / Unit Kerja</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>KODE</th>
                                                    <th>NAMA DEPARTEMEN</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($departments as $dep): ?>
                                                    <tr>
                                                        <td class="font-mono font-weight-bold text-teal"><?= esc($dep->code) ?></td>
                                                        <td>
                                                            <strong><?= esc($dep->name) ?></strong>
                                                            <small class="text-muted d-block"><?= esc($dep->description ?? '') ?></small>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-dept" 
                                                                data-id="<?= $dep->id ?>" 
                                                                data-code="<?= esc($dep->code) ?>" 
                                                                data-name="<?= esc($dep->name) ?>" 
                                                                data-desc="<?= esc($dep->description ?? '') ?>" 
                                                                data-status="<?= $dep->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#deptEditModal" title="Edit Departemen">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/department') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus departemen ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $dep->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card card-outline card-secondary shadow-sm mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-user-tag text-teal mr-1"></i> Jabatan / Posisi Kerja</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>KODE</th>
                                                    <th>JABATAN</th>
                                                    <th>DEPARTEMEN</th>
                                                    <th class="text-center">AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($jobPositions as $jp): ?>
                                                    <tr>
                                                        <td class="font-mono font-weight-bold"><?= esc($jp->code) ?></td>
                                                        <td><strong><?= esc($jp->title) ?></strong></td>
                                                        <td><?= esc($jp->department_name ?? '-') ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs btn-edit-job" 
                                                                data-id="<?= $jp->id ?>" 
                                                                data-dept="<?= $jp->department_id ?>" 
                                                                data-code="<?= esc($jp->code) ?>" 
                                                                data-title="<?= esc($jp->title) ?>" 
                                                                data-status="<?= $jp->status ?>" 
                                                                data-toggle="modal" 
                                                                data-target="#jobEditModal" title="Edit Jabatan">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?= base_url('system/master-klinik/job-position') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus jabatan ini?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?= $jp->id ?>">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                            </form>
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

                    <!-- ========================================== -->
                    <!-- TAB 8: UJI LABORATORIUM                    -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="lab" role="tabpanel" aria-labelledby="lab-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-vial text-teal"></i> MASTER PEMERIKSAAN LABORATORIUM &amp; NILAI RUJUKAN</h5>
                                <small class="text-muted">Katalog tes hematologi, kimia darah, urinalisis, serologi beserta nilai normal rujukan klinis.</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#labAddModal">
                                <i class="fas fa-plus mr-1"></i> + Tambah Uji Lab
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 100px;">KODE</th>
                                        <th>NAMA PEMERIKSAAN</th>
                                        <th>KATEGORI &amp; SPESIMEN</th>
                                        <th>NILAI NORMAL (RUJUKAN)</th>
                                        <th style="width: 120px;" class="text-right">TARIF</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 100px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($labTests)): ?>
                                        <?php foreach ($labTests as $lt): ?>
                                            <tr>
                                                <td class="font-mono font-weight-bold text-teal"><?= esc($lt->code) ?></td>
                                                <td><strong><?= esc($lt->name) ?></strong></td>
                                                <td>
                                                    <span class="badge badge-info"><?= esc($lt->category) ?></span>
                                                    <small class="text-muted d-block"><?= esc($lt->specimen) ?></small>
                                                </td>
                                                <td class="text-xs">
                                                    <code><?= esc($lt->reference_range) ?></code> 
                                                    <?php if (!empty($lt->unit)): ?>
                                                        <span class="badge badge-secondary"><?= esc($lt->unit) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold text-success">
                                                    Rp <?= number_format($lt->price, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $lt->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($lt->status) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-xs btn-edit-lab" 
                                                        data-id="<?= $lt->id ?>" 
                                                        data-code="<?= esc($lt->code) ?>" 
                                                        data-name="<?= esc($lt->name) ?>" 
                                                        data-cat="<?= esc($lt->category) ?>" 
                                                        data-spec="<?= esc($lt->specimen) ?>" 
                                                        data-range="<?= esc($lt->reference_range ?? '') ?>" 
                                                        data-unit="<?= esc($lt->unit ?? '') ?>" 
                                                        data-price="<?= $lt->price ?>" 
                                                        data-status="<?= $lt->status ?>" 
                                                        data-toggle="modal" 
                                                        data-target="#labEditModal" title="Edit Uji Lab">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="<?= base_url('system/master-klinik/lab-test') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus pemeriksaan lab ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $lt->id ?>">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 9: MITRA PENJAMIN & ASURANSI          -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="insurance" role="tabpanel" aria-labelledby="insurance-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-handshake text-teal"></i> MASTER MITRA PENJAMIN &amp; ASURANSI KESEHATAN</h5>
                                <small class="text-muted">Daftar penjamin BPJS, asuransi swasta (Third Party Administrator), dan corporate berbadan hukum.</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#insuranceAddModal">
                                <i class="fas fa-plus mr-1"></i> + Tambah Asuransi
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 120px;">KODE</th>
                                        <th>NAMA PENJAMIN / ASURANSI</th>
                                        <th>TIPE PENJAMIN</th>
                                        <th>KONTAK &amp; PIC</th>
                                        <th>ALAMAT KLAIM</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 100px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($insuranceProviders)): ?>
                                        <?php foreach ($insuranceProviders as $ip): ?>
                                            <tr>
                                                <td class="font-mono font-weight-bold text-teal"><?= esc($ip->code) ?></td>
                                                <td><strong><?= esc($ip->name) ?></strong></td>
                                                <td>
                                                    <span class="badge badge-<?= $ip->type === 'bpjs' ? 'success' : ($ip->type === 'corporate' ? 'primary' : 'warning') ?>">
                                                        <?= strtoupper(str_replace('_', ' ', $ip->type)) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-xs"><strong>Telp:</strong> <?= esc($ip->phone ?? '-') ?></div>
                                                    <div class="text-xs text-muted"><strong>PIC:</strong> <?= esc($ip->pic_name ?? '-') ?></div>
                                                </td>
                                                <td class="text-xs"><?= esc($ip->claim_address ?? '-') ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $ip->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($ip->status) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-xs btn-edit-insurance" 
                                                        data-id="<?= $ip->id ?>" 
                                                        data-code="<?= esc($ip->code) ?>" 
                                                        data-name="<?= esc($ip->name) ?>" 
                                                        data-type="<?= esc($ip->type) ?>" 
                                                        data-phone="<?= esc($ip->phone ?? '') ?>" 
                                                        data-email="<?= esc($ip->email ?? '') ?>" 
                                                        data-address="<?= esc($ip->claim_address ?? '') ?>" 
                                                        data-pic="<?= esc($ip->pic_name ?? '') ?>" 
                                                        data-discount="<?= $ip->discount_rate ?>" 
                                                        data-status="<?= $ip->status ?>" 
                                                        data-toggle="modal" 
                                                        data-target="#insuranceEditModal" title="Edit Asuransi">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="<?= base_url('system/master-klinik/insurance-provider') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus mitra asuransi ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $ip->id ?>">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 10: JADWAL PRAKTEK DOKTER              -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-calendar-alt text-teal"></i> MASTER JADWAL PRAKTEK DOKTER &amp; POLI</h5>
                                <small class="text-muted">Pengaturan hari dinas dokter, jam praktek, alokasi ruangan, dan kuota maksimal pasien per sesi.</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#scheduleAddModal">
                                <i class="fas fa-plus mr-1"></i> + Tambah Jadwal Dokter
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>NAMA DOKTER</th>
                                        <th>HARI PRAKTEK</th>
                                        <th>JAM PRAKTEK</th>
                                        <th>RUANGAN POLI</th>
                                        <th>KUOTA MAX</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 100px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($doctorSchedules)): ?>
                                        <?php foreach ($doctorSchedules as $ds): ?>
                                            <tr>
                                                <td><strong class="text-teal"><?= esc($ds->doctor_name) ?></strong></td>
                                                <td><span class="badge badge-primary px-2 py-1"><?= esc($ds->day_of_week) ?></span></td>
                                                <td><?= substr($ds->start_time, 0, 5) ?> - <?= substr($ds->end_time, 0, 5) ?> WITA</td>
                                                <td><?= esc($ds->room_name ?? 'Poli Umum') ?></td>
                                                <td><?= $ds->max_quota ?> Pasien</td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $ds->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($ds->status) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-xs btn-edit-schedule" 
                                                        data-id="<?= $ds->id ?>" 
                                                        data-doctor="<?= $ds->doctor_id ?>" 
                                                        data-room="<?= $ds->room_id ?>" 
                                                        data-day="<?= esc($ds->day_of_week) ?>" 
                                                        data-start="<?= esc($ds->start_time) ?>" 
                                                        data-end="<?= esc($ds->end_time) ?>" 
                                                        data-quota="<?= $ds->max_quota ?>" 
                                                        data-status="<?= $ds->status ?>" 
                                                        data-toggle="modal" 
                                                        data-target="#scheduleEditModal" title="Edit Jadwal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="<?= base_url('system/master-klinik/doctor-schedule') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus jadwal ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $ds->id ?>">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- TAB 11: DISTRIBUTOR / PBF                  -->
                    <!-- ========================================== -->
                    <div class="tab-pane fade" id="supplier" role="tabpanel" aria-labelledby="supplier-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-truck text-teal"></i> MASTER DISTRIBUTOR / PEDAGANG BESAR FARMASI (PBF)</h5>
                                <small class="text-muted">Daftar pemasok resmi obat, alkes, BMHP, dan logistik kebutuhan klinik.</small>
                            </div>
                            <button class="btn btn-teal font-weight-bold shadow-sm" data-toggle="modal" data-target="#supplierAddModal">
                                <i class="fas fa-plus mr-1"></i> + Tambah Distributor PBF
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable w-100" style="font-size: 11.5px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 120px;">KODE</th>
                                        <th>NAMA DISTRIBUTOR / PBF</th>
                                        <th>KONTAK &amp; SALES PIC</th>
                                        <th>ALAMAT KANTOR / GUDANG</th>
                                        <th>REKENING BANK</th>
                                        <th style="width: 80px;" class="text-center">STATUS</th>
                                        <th style="width: 100px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($suppliers)): ?>
                                        <?php foreach ($suppliers as $sup): ?>
                                            <tr>
                                                <td class="font-mono font-weight-bold text-teal"><?= esc($sup->code) ?></td>
                                                <td><strong><?= esc($sup->name) ?></strong></td>
                                                <td>
                                                    <div class="text-xs"><strong>Telp:</strong> <?= esc($sup->phone) ?></div>
                                                    <div class="text-xs text-muted"><strong>PIC:</strong> <?= esc($sup->pic_name ?? '-') ?></div>
                                                </td>
                                                <td class="text-xs"><?= esc($sup->address) ?></td>
                                                <td class="text-xs">
                                                    <strong><?= esc($sup->bank_name ?? '-') ?></strong>
                                                    <div class="font-mono"><?= esc($sup->bank_account ?? '-') ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $sup->status === 'active' ? 'success' : 'danger' ?>"><?= strtoupper($sup->status) ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-info btn-xs btn-edit-supplier" 
                                                        data-id="<?= $sup->id ?>" 
                                                        data-code="<?= esc($sup->code) ?>" 
                                                        data-name="<?= esc($sup->name) ?>" 
                                                        data-phone="<?= esc($sup->phone) ?>" 
                                                        data-pic="<?= esc($sup->pic_name ?? '') ?>" 
                                                        data-email="<?= esc($sup->email ?? '') ?>" 
                                                        data-address="<?= esc($sup->address) ?>" 
                                                        data-bankname="<?= esc($sup->bank_name ?? '') ?>" 
                                                        data-bankacc="<?= esc($sup->bank_account ?? '') ?>" 
                                                        data-status="<?= $sup->status ?>" 
                                                        data-toggle="modal" 
                                                        data-target="#supplierEditModal" title="Edit Distributor">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="<?= base_url('system/master-klinik/supplier') ?>" method="post" class="d-inline mb-0" onsubmit="return confirm('Hapus distributor ini?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $sup->id ?>">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
<!-- MODALS SECTION -->
<!-- ========================================================================= -->

<!-- 1. MODAL KATEGORI PELAYANAN -->
<div class="modal fade" id="catAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/category') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Tambah Kategori Pelayanan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori Pelayanan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="POLI, TINDAKAN, ESTETIKA, dll" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi kategori pelayanan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="catEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/category') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-cat-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Edit Kategori Pelayanan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori Pelayanan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-cat-name" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" id="edit-cat-desc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL SUB POLIKLINIK -->
<div class="modal fade" id="polyAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/poliklinik-baru') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="category_id" value="1">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Tambah Sub Kategori Poli</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kategori Induk</label>
                        <input type="text" class="form-control bg-light" value="POLI" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama Sub Kategori (Poli) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="SPESIALIS JANTUNG, SPESIALIS GIZI, POLI GIGI, dll" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="polyEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/poliklinik-baru') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-poly-id">
            <input type="hidden" name="category_id" value="1">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Edit Sub Kategori Poli</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Sub Kategori (Poli) <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-poly-name" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" id="edit-poly-desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-poly-status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL SUB KATEGORI & SUB KECIL TINDAKAN -->
<div class="modal fade" id="serviceAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/service') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="category_id" value="2">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Tambah Sub / Sub Kecil Tindakan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Layanan <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="TND-XXX" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Pelayanan / Tindakan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="HAIRSTUDIO, RAMBUT RONTOK, FACIAL, dll" required>
                    </div>
                    <div class="form-group">
                        <label>Sub Kategori Induk (Parent) <small class="text-muted">(Pilih jika ini Sub Kecil)</small></label>
                        <select name="parent_id" class="form-control">
                            <option value="">-- Tanpa Induk (Ini adalah Sub Kategori / Layanan Langsung) --</option>
                            <?php foreach ($parentTindakan as $pt): ?>
                                <option value="<?= $pt->id ?>"><?= esc($pt->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga / Tarif Layanan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="50000" required>
                        <small class="text-muted">Isi 0 jika ini adalah Sub Kategori Induk yang memiliki Sub Kecil di bawahnya.</small>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="serviceEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/service') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-srv-id">
            <input type="hidden" name="category_id" value="2">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Edit Tindakan & Tarif</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Layanan <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-srv-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Pelayanan / Tindakan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-srv-name" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Sub Kategori Induk (Parent)</label>
                        <select name="parent_id" id="edit-srv-parent" class="form-control">
                            <option value="">-- Tanpa Induk (Ini adalah Sub Kategori / Layanan Langsung) --</option>
                            <?php foreach ($parentTindakan as $pt): ?>
                                <option value="<?= $pt->id ?>"><?= esc($pt->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga / Tarif Layanan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" id="edit-srv-price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Deskripsi</label>
                        <textarea name="description" id="edit-srv-desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-srv-status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 4. MODAL DOKTER & FEE PER PASIEN -->
<div class="modal fade" id="doctorAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/doctor') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Tambah Penugasan Dokter & Fee</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIK Pegawai <span class="text-danger">*</span></label>
                        <input type="text" name="nik_employee" class="form-control text-uppercase" placeholder="EMP-DOK-XXX" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Dokter / Pelaksana <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="PAHRI, YADI, dll" required>
                    </div>
                    <div class="form-group">
                        <label>ID - Kategori Pelayanan <span class="text-danger">*</span></label>
                        <select name="category_id" id="add-doc-cat" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="1">1 - POLI</option>
                            <option value="2">2 - TINDAKAN</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="grp-poly-select">
                        <label>Sub Kategori (Poliklinik)</label>
                        <select name="polyclinic_id" class="form-control">
                            <option value="">-- Pilih Poliklinik --</option>
                            <?php foreach ($polikliniks as $p): ?>
                                <option value="<?= $p->id ?>"><?= esc($p->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="grp-tindakan-select" style="display: none;">
                        <label>Sub / Sub Kecil Pelayanan (Tindakan)</label>
                        <select name="tindakan_id" class="form-control">
                            <option value="">-- Pilih Tindakan Medis / Estetika --</option>
                            <?php foreach ($tindakanList as $t): ?>
                                <option value="<?= $t->id ?>">
                                    <?= $t->parent_name ? '[' . esc($t->parent_name) . '] ' : '' ?><?= esc($t->name) ?> (Tarif: Rp <?= number_format($t->price, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fee Per Pasien (Rp)</label>
                        <input type="number" step="0.01" name="fee_per_pasien" class="form-control" placeholder="100000" value="0.00">
                        <small class="text-muted">Nominal komisi bagi hasil yang diterima dokter per tindakan/pasien.</small>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="doctorEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/doctor') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-doc-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal">
                    <h5 class="modal-title text-white font-weight-bold">Edit Penugasan Dokter & Fee</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIK Pegawai <span class="text-danger">*</span></label>
                        <input type="text" name="nik_employee" id="edit-doc-nik" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Dokter <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-doc-name" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>ID - Kategori Pelayanan <span class="text-danger">*</span></label>
                        <select name="category_id" id="edit-doc-cat" class="form-control" required>
                            <option value="1">1 - POLI</option>
                            <option value="2">2 - TINDAKAN</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="edit-grp-poly-select">
                        <label>Sub Kategori (Poliklinik)</label>
                        <select name="polyclinic_id" id="edit-doc-poly" class="form-control">
                            <option value="">-- Pilih Poliklinik --</option>
                            <?php foreach ($polikliniks as $p): ?>
                                <option value="<?= $p->id ?>"><?= esc($p->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="edit-grp-tindakan-select">
                        <label>Sub / Sub Kecil Pelayanan (Tindakan)</label>
                        <select name="tindakan_id" id="edit-doc-tindakan" class="form-control">
                            <option value="">-- Pilih Tindakan Medis / Estetika --</option>
                            <?php foreach ($tindakanList as $t): ?>
                                <option value="<?= $t->id ?>">
                                    <?= $t->parent_name ? '[' . esc($t->parent_name) . '] ' : '' ?><?= esc($t->name) ?> (Tarif: Rp <?= number_format($t->price, 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fee Per Pasien (Rp)</label>
                        <input type="number" step="0.01" name="fee_per_pasien" id="edit-doc-fee" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-doc-status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TANDA TANGAN & STEMPEL DIGITAL DOKTER (DATABASE REFERENSI)           -->
<!-- ========================================================================= -->
<div class="modal fade" id="doctorSignatureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-gradient-teal text-white py-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center mr-2.5 rounded-circle bg-white text-teal font-weight-bold" style="width: 36px; height: 36px; font-size: 16px;">
                        <i class="fas fa-signature"></i>
                    </span>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="sig-modal-doc-name">Tanda Tangan &amp; Stempel Dokter</h5>
                        <small class="text-white-50" id="sig-modal-doc-sub">SIP: -</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="sig-target-doc-id">
                
                <div class="alert alert-info border-0 p-2.5 mb-3 text-xs" style="background:#e0f2fe; color:#0369a1; border-radius: 8px;">
                    <i class="fas fa-info-circle mr-1"></i> Tanda tangan ini akan tersimpan permanen di database referensi dan otomatis tersemat di seluruh formulir rekam medis (Lembar 1-7, CPPT, Surat Sakit, dan Resep).
                </div>

                <!-- TTD CANVAS PAD -->
                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="text-xs font-weight-bold text-dark mb-0"><i class="fas fa-pen-nib text-teal mr-1"></i> Tanda Tangan Digital Dokter (Sentuh / Mouse):</label>
                        <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-clear-doc-sig">
                            <i class="fas fa-rotate-left mr-1"></i> Bersihkan Canvas
                        </button>
                    </div>
                    <div class="border rounded bg-white p-1 text-center" style="border-color: #cbd5e1 !important;">
                        <canvas id="canvas-doc-signature" width="450" height="160" style="width: 100%; height: 150px; touch-action: none; background: #ffffff; cursor: crosshair;"></canvas>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 10px;">Goreskan tanda tangan di area kotak di atas (support touchscreen / iPad / stylus).</small>
                </div>

                <!-- ATAU UPLOAD GAMBAR TTD -->
                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-upload text-info mr-1"></i> Atau Upload Gambar TTD (Format PNG Transparan):</label>
                    <input type="file" id="file-upload-doc-sig" class="form-control-file text-xs" accept="image/png,image/jpeg,image/webp">
                </div>

                <!-- PREVIEW TTD AKTIF -->
                <div id="sig-current-preview-box" class="p-2.5 rounded bg-white border text-center mb-2" style="display: none;">
                    <small class="text-muted d-block text-xs mb-1">Tanda Tangan Tersimpan Saat Ini:</small>
                    <img id="sig-current-preview-img" src="" alt="TTD Dokter" style="max-height: 60px; max-width: 180px;">
                </div>

            </div>
            <div class="modal-footer bg-white py-2 px-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-teal btn-sm font-weight-bold" id="btn-save-doc-signature">
                    <i class="fas fa-save mr-1"></i> Simpan Tanda Tangan ke Database
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT EVENT MAPPING FOR MODAL EDITS -->
<!-- ========================================================================= -->
<!-- MODAL TAMBAH RUANGAN -->
<div class="modal fade" id="roomAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/room') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Ruangan Klinik</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="RM-POLI-02" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="Ruang Poli Gigi" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Ruangan</label>
                        <select name="type" class="form-control">
                            <option value="poli">Poli Rawat Jalan</option>
                            <option value="tindakan">Ruang Tindakan Medis</option>
                            <option value="observasi">Ruang Observasi / Infus</option>
                            <option value="rawat_inap">Rawat Inap</option>
                            <option value="apotek">Farmasi &amp; Apotek</option>
                            <option value="laboratorium">Laboratorium</option>
                            <option value="kasir">Admisi &amp; Kasir</option>
                            <option value="gudang">Gudang Logistik</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lokasi Lantai</label>
                        <input type="text" name="floor" class="form-control" value="Lantai 1">
                    </div>
                    <div class="form-group">
                        <label>Kapasitas (Bed / Orang)</label>
                        <input type="number" name="capacity" class="form-control" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Ruangan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH BED -->
<div class="modal fade" id="bedAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/bed') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Tempat Tidur (Bed)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ruangan Lokasi <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach ($rooms as $r): ?>
                                <option value="<?= $r->id ?>"><?= esc($r->code) ?> - <?= esc($r->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nomor / Nama Bed <span class="text-danger">*</span></label>
                        <input type="text" name="bed_number" class="form-control text-uppercase" placeholder="Bed Observasi 04" required>
                    </div>
                    <div class="form-group">
                        <label>Tarif Sewa / Hari (Rp)</label>
                        <input type="number" name="tariff_per_day" class="form-control" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Bed</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH KATEGORI OBAT -->
<div class="modal fade" id="medCatAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/medicine-category') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Kategori &amp; Golongan Obat</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="KAT-GENERIK" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Obat Generik Berlogo" required>
                    </div>
                    <div class="form-group">
                        <label>Golongan Regulasi BPOM</label>
                        <select name="drug_class" class="form-control">
                            <option value="obat_bebas">Obat Bebas (Lingkaran Hijau)</option>
                            <option value="obat_bebas_terbatas">Obat Bebas Terbatas (Lingkaran Biru)</option>
                            <option value="obat_keras">Obat Keras (Lingkaran Merah / K)</option>
                            <option value="psikotropika">Psikotropika</option>
                            <option value="narkotika">Narkotika</option>
                            <option value="prekursor">Prekursor Farmasi</option>
                            <option value="alkes_bmhp">Alat Kesehatan &amp; BMHP</option>
                            <option value="herbal">Herbal / Jamu Fitofarmaka</option>
                            <option value="suplemen">Suplemen &amp; Vitamin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Kategori</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH SATUAN UOM -->
<div class="modal fade" id="unitAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/unit') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Satuan Ukuran (UOM)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="BTL" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Botol / Sirup" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Satuan</label>
                        <select name="category" class="form-control">
                            <option value="farmasi">Farmasi &amp; Obat</option>
                            <option value="logistik">Logistik &amp; ATK</option>
                            <option value="aset">Aset Medis / Non-Medis</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Satuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH TEMPLATE INFORMED CONSENT -->
<div class="modal fade" id="consentTemplateAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/consent-template') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Template Informed Consent Tindakan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Judul Template <span class="text-danger">*</span></label>
                            <input type="text" name="template_title" class="form-control" placeholder="Template Tindakan Injeksi..." required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tautkan ke Tindakan Medis (Opsional)</label>
                            <select name="tindakan_id" class="form-control">
                                <option value="">-- Tidak Terikat Tindakan Tertentu --</option>
                                <?php foreach ($tindakanList as $t): ?>
                                    <option value="<?= $t->id ?>"><?= esc($t->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Indikasi Klinis / Diagnosa</label>
                            <input type="text" name="diagnosis_indication" class="form-control" placeholder="Veruka, Lesi Kulit Jinak...">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tata Cara Prosedur Tindakan</label>
                            <input type="text" name="procedure_action" class="form-control" placeholder="Elektrokauterisasi, Eksisi Bedah Minor...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tujuan &amp; Manfaat Tindakan</label>
                        <textarea name="goal_benefits" class="form-control" rows="2" placeholder="Menghilangkan lesi yang mengganggu..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Risiko, Komplikasi &amp; Efek Samping</label>
                        <textarea name="risks_complications" class="form-control" rows="2" placeholder="Nyeri sementara, pembengkakan, risiko infeksi ringan..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Prognosis Tindakan</label>
                            <input type="text" name="prognosis" class="form-control" placeholder="Dubia ad Bonam...">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Alternatif Terapi &amp; Risiko Penolakan</label>
                            <input type="text" name="alternative_therapies" class="form-control" placeholder="Terapi topikal konservatif...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Template</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH DEPARTEMEN -->
<div class="modal fade" id="deptAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/department') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Departemen SDM</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="DEP-IT" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Teknologi Informasi &amp; RME" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Departemen</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH JABATAN -->
<div class="modal fade" id="jobAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/job-position') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Jabatan SDM</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Departemen <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($departments as $dep): ?>
                                <option value="<?= $dep->id ?>"><?= esc($dep->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="POS-APOTEKER" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Jabatan / Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control text-uppercase" placeholder="Apoteker Penanggung Jawab" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Jabatan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT RUANGAN -->
<div class="modal fade" id="roomEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/room') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-room-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Ruangan Klinik</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-room-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-room-name" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Ruangan</label>
                        <select name="type" id="edit-room-type" class="form-control">
                            <option value="poli">Poli Rawat Jalan</option>
                            <option value="tindakan">Ruang Tindakan Medis</option>
                            <option value="observasi">Ruang Observasi / Infus</option>
                            <option value="rawat_inap">Rawat Inap</option>
                            <option value="apotek">Farmasi &amp; Apotek</option>
                            <option value="laboratorium">Laboratorium</option>
                            <option value="kasir">Admisi &amp; Kasir</option>
                            <option value="gudang">Gudang Logistik</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Lokasi Lantai</label>
                            <input type="text" name="floor" id="edit-room-floor" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kapasitas (Bed/Orang)</label>
                            <input type="number" name="capacity" id="edit-room-capacity" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status Operasional</label>
                        <select name="status" id="edit-room-status" class="form-control">
                            <option value="active">ACTIVE (Beroperasi)</option>
                            <option value="inactive">INACTIVE (Nonaktif / Renovasi)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT BED -->
<div class="modal fade" id="bedEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/bed') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-bed-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Tempat Tidur (Bed)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ruangan Lokasi <span class="text-danger">*</span></label>
                        <select name="room_id" id="edit-bed-room-id" class="form-control" required>
                            <?php foreach ($rooms as $r): ?>
                                <option value="<?= $r->id ?>"><?= esc($r->code) ?> - <?= esc($r->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nomor / Nama Bed <span class="text-danger">*</span></label>
                        <input type="text" name="bed_number" id="edit-bed-number" class="form-control text-uppercase" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tarif / Hari (Rp)</label>
                            <input type="number" name="tariff_per_day" id="edit-bed-tariff" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Bed</label>
                            <select name="status" id="edit-bed-status" class="form-control">
                                <option value="available">AVAILABLE (Kosong / Siap Pakai)</option>
                                <option value="occupied">OCCUPIED (Terisi Pasien)</option>
                                <option value="maintenance">MAINTENANCE (Perawatan / Rusak)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KATEGORI OBAT -->
<div class="modal fade" id="medCatEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/medicine-category') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-medcat-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Kategori Obat</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-medcat-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-medcat-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Golongan Regulasi BPOM</label>
                        <select name="drug_class" id="edit-medcat-class" class="form-control">
                            <option value="obat_bebas">Obat Bebas (Lingkaran Hijau)</option>
                            <option value="obat_bebas_terbatas">Obat Bebas Terbatas (Lingkaran Biru)</option>
                            <option value="obat_keras">Obat Keras (Lingkaran Merah / K)</option>
                            <option value="psikotropika">Psikotropika</option>
                            <option value="narkotika">Narkotika</option>
                            <option value="prekursor">Prekursor Farmasi</option>
                            <option value="alkes_bmhp">Alat Kesehatan &amp; BMHP</option>
                            <option value="herbal">Herbal / Jamu Fitofarmaka</option>
                            <option value="suplemen">Suplemen &amp; Vitamin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="description" id="edit-medcat-desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-medcat-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT SATUAN UOM -->
<div class="modal fade" id="unitEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/unit') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-unit-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Satuan Ukuran (UOM)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-unit-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-unit-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Satuan</label>
                        <select name="category" id="edit-unit-category" class="form-control">
                            <option value="farmasi">Farmasi &amp; Obat</option>
                            <option value="logistik">Logistik &amp; ATK</option>
                            <option value="aset">Aset Medis / Non-Medis</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-unit-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT TEMPLATE INFORMED CONSENT -->
<div class="modal fade" id="consentTemplateEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/consent-template') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-consent-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Template Informed Consent Tindakan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Judul Template <span class="text-danger">*</span></label>
                            <input type="text" name="template_title" id="edit-consent-title" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tautkan ke Tindakan Medis (Opsional)</label>
                            <select name="tindakan_id" id="edit-consent-tindakan" class="form-control">
                                <option value="">-- Tidak Terikat Tindakan Tertentu --</option>
                                <?php foreach ($tindakanList as $t): ?>
                                    <option value="<?= $t->id ?>"><?= esc($t->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Indikasi Klinis / Diagnosa</label>
                            <input type="text" name="diagnosis_indication" id="edit-consent-diag" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tata Cara Prosedur Tindakan</label>
                            <input type="text" name="procedure_action" id="edit-consent-proc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tujuan &amp; Manfaat Tindakan</label>
                        <textarea name="goal_benefits" id="edit-consent-goal" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Risiko, Komplikasi &amp; Efek Samping</label>
                        <textarea name="risks_complications" id="edit-consent-risks" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Prognosis Tindakan</label>
                            <input type="text" name="prognosis" id="edit-consent-prog" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Alternatif Terapi &amp; Risiko Penolakan</label>
                            <input type="text" name="alternative_therapies" id="edit-consent-alt" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status Template</label>
                        <select name="status" id="edit-consent-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DEPARTEMEN -->
<div class="modal fade" id="deptEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/department') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-dept-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Departemen SDM</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-dept-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-dept-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" id="edit-dept-desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-dept-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JABATAN -->
<div class="modal fade" id="jobEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/job-position') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-job-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Jabatan SDM</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Departemen <span class="text-danger">*</span></label>
                        <select name="department_id" id="edit-job-dept" class="form-control" required>
                            <?php foreach ($departments as $dep): ?>
                                <option value="<?= $dep->id ?>"><?= esc($dep->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kode Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="edit-job-code" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Jabatan / Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit-job-title" class="form-control text-uppercase" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-job-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH UJI LAB -->
<div class="modal fade" id="labAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/lab-test') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Uji Laboratorium</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Uji Lab <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="LAB-GLU-01" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Pemeriksaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Gula Darah Puasa (GDP)" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kategori Pemeriksaan</label>
                            <select name="category" class="form-control">
                                <option value="Hematologi">Hematologi &amp; Darah Rutin</option>
                                <option value="Kimia Darah">Kimia Darah (Glukosa, Enzim)</option>
                                <option value="Profil Lipid">Profil Lipid (Kolesterol)</option>
                                <option value="Fungsi Ginjal">Fungsi Ginjal (Asam Urat, Ureum, Kreatinin)</option>
                                <option value="Fungsi Hati">Fungsi Hati (SGOT, SGPT)</option>
                                <option value="Urinalisis">Urinalisis &amp; Feses</option>
                                <option value="Serologi">Serologi &amp; Imunologi (Widal, NS1)</option>
                                <option value="Imunohematologi">Imunohematologi (Gol Darah)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Spesimen / Sampel</label>
                            <input type="text" name="specimen" class="form-control" placeholder="Serum Darah / Darah EDTA / Urine">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label>Rentang Nilai Normal (Rujukan)</label>
                            <input type="text" name="reference_range" class="form-control" placeholder="70 - 100 mg/dL">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Satuan Nilai</label>
                            <input type="text" name="unit" class="form-control" placeholder="mg/dL / U/L / % / Panel">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tarif Pemeriksaan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control font-weight-bold" placeholder="25000" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Uji Lab</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT UJI LAB -->
<div class="modal fade" id="labEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/lab-test') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-lab-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Uji Laboratorium</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Uji Lab <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-lab-code" class="form-control text-uppercase" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Pemeriksaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-lab-name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kategori Pemeriksaan</label>
                            <select name="category" id="edit-lab-cat" class="form-control">
                                <option value="Hematologi">Hematologi &amp; Darah Rutin</option>
                                <option value="Kimia Darah">Kimia Darah (Glukosa, Enzim)</option>
                                <option value="Profil Lipid">Profil Lipid (Kolesterol)</option>
                                <option value="Fungsi Ginjal">Fungsi Ginjal (Asam Urat, Ureum, Kreatinin)</option>
                                <option value="Fungsi Hati">Fungsi Hati (SGOT, SGPT)</option>
                                <option value="Urinalisis">Urinalisis &amp; Feses</option>
                                <option value="Serologi">Serologi &amp; Imunologi (Widal, NS1)</option>
                                <option value="Imunohematologi">Imunohematologi (Gol Darah)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Spesimen / Sampel</label>
                            <input type="text" name="specimen" id="edit-lab-spec" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label>Rentang Nilai Normal (Rujukan)</label>
                            <input type="text" name="reference_range" id="edit-lab-range" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Satuan Nilai</label>
                            <input type="text" name="unit" id="edit-lab-unit" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tarif Pemeriksaan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit-lab-price" class="form-control font-weight-bold" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select name="status" id="edit-lab-status" class="form-control">
                                <option value="active">ACTIVE</option>
                                <option value="inactive">INACTIVE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH ASURANSI -->
<div class="modal fade" id="insuranceAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/insurance-provider') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Mitra Asuransi / Penjamin</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Penjamin <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="INS-BPJS-01" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Asuransi / Penjamin <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="BPJS Kesehatan (JKN-KIS)" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tipe Penjamin</label>
                            <select name="type" class="form-control">
                                <option value="bpjs">BPJS Kesehatan (Pemerintah)</option>
                                <option value="asuransi_swasta">Asuransi Swasta / TPA</option>
                                <option value="corporate">Perusahaan / Corporate PKS</option>
                                <option value="pemerintah">Instansi Pemerintah Non-BPJS</option>
                                <option value="mandiri">Mandiri / Umum</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor Telepon / Call Center</label>
                            <input type="text" name="phone" class="form-control" placeholder="1500085">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Email Klaim</label>
                            <input type="email" name="email" class="form-control" placeholder="klaim@asuransi.com">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama PIC / Kontak</label>
                            <input type="text" name="pic_name" class="form-control" placeholder="Departemen Provider / Relasi RS">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Pengajuan Klaim</label>
                        <textarea name="claim_address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Mitra Asuransi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT ASURANSI -->
<div class="modal fade" id="insuranceEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/insurance-provider') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-ins-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Mitra Asuransi / Penjamin</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode Penjamin <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-ins-code" class="form-control text-uppercase" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Asuransi / Penjamin <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-ins-name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tipe Penjamin</label>
                            <select name="type" id="edit-ins-type" class="form-control">
                                <option value="bpjs">BPJS Kesehatan (Pemerintah)</option>
                                <option value="asuransi_swasta">Asuransi Swasta / TPA</option>
                                <option value="corporate">Perusahaan / Corporate PKS</option>
                                <option value="pemerintah">Instansi Pemerintah Non-BPJS</option>
                                <option value="mandiri">Mandiri / Umum</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor Telepon / Call Center</label>
                            <input type="text" name="phone" id="edit-ins-phone" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Email Klaim</label>
                            <input type="email" name="email" id="edit-ins-email" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama PIC / Kontak</label>
                            <input type="text" name="pic_name" id="edit-ins-pic" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Pengajuan Klaim</label>
                        <textarea name="claim_address" id="edit-ins-address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="edit-ins-status" class="form-control">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH JADWAL DOKTER -->
<div class="modal fade" id="scheduleAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/doctor-schedule') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Jadwal Praktek Dokter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Dokter Pelaksana <span class="text-danger">*</span></label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php foreach ($doctors as $d): ?>
                                <option value="<?= $d->id ?>"><?= esc($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Hari Praktek <span class="text-danger">*</span></label>
                            <select name="day_of_week" class="form-control" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Ruangan Poli</label>
                            <select name="room_id" class="form-control">
                                <option value="">-- Pilih Ruangan --</option>
                                <?php foreach ($rooms as $r): ?>
                                    <option value="<?= $r->id ?>"><?= esc($r->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control" value="08:00">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control" value="16:00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kuota Maksimal Pasien / Sesi</label>
                        <input type="number" name="max_quota" class="form-control" value="35">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JADWAL DOKTER -->
<div class="modal fade" id="scheduleEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/master-klinik/doctor-schedule') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-sched-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Jadwal Praktek Dokter</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Dokter Pelaksana <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="edit-sched-doctor" class="form-control" required>
                            <?php foreach ($doctors as $d): ?>
                                <option value="<?= $d->id ?>"><?= esc($d->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Hari Praktek <span class="text-danger">*</span></label>
                            <select name="day_of_week" id="edit-sched-day" class="form-control" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Ruangan Poli</label>
                            <select name="room_id" id="edit-sched-room" class="form-control">
                                <option value="">-- Pilih Ruangan --</option>
                                <?php foreach ($rooms as $r): ?>
                                    <option value="<?= $r->id ?>"><?= esc($r->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="start_time" id="edit-sched-start" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="end_time" id="edit-sched-end" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kuota Max Pasien</label>
                            <input type="number" name="max_quota" id="edit-sched-quota" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select name="status" id="edit-sched-status" class="form-control">
                                <option value="active">ACTIVE</option>
                                <option value="inactive">INACTIVE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH DISTRIBUTOR PBF -->
<div class="modal fade" id="supplierAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/supplier') ?>" method="post">
            <input type="hidden" name="action" value="create">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold">Tambah Distributor / PBF Farmasi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode PBF <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase" placeholder="PBF-KIMIAFARMA" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Distributor / PBF <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="PT Kimia Farma Trading &amp; Distribution" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="0370-631234" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Sales / PIC</label>
                            <input type="text" name="pic_name" class="form-control" placeholder="Nama Marketing PBF">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Kantor / Gudang</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Jl. Raya Cakranegara Mataram"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Bank</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Bank Mandiri / BCA">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nomor Rekening</label>
                            <input type="text" name="bank_account" class="form-control font-mono" placeholder="123-00-1234567-8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal font-weight-bold">Simpan Distributor</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT DISTRIBUTOR PBF -->
<div class="modal fade" id="supplierEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('system/master-klinik/supplier') ?>" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-sup-id">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Distributor / PBF Farmasi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Kode PBF <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="edit-sup-code" class="form-control text-uppercase" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Distributor / PBF <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-sup-name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="edit-sup-phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Nama Sales / PIC</label>
                            <input type="text" name="pic_name" id="edit-sup-pic" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alamat Kantor / Gudang</label>
                        <textarea name="address" id="edit-sup-address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Nama Bank</label>
                            <input type="text" name="bank_name" id="edit-sup-bankname" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Nomor Rekening</label>
                            <input type="text" name="bank_account" id="edit-sup-bankacc" class="form-control font-mono">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Status</label>
                            <select name="status" id="edit-sup-status" class="form-control">
                                <option value="active">ACTIVE</option>
                                <option value="inactive">INACTIVE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info font-weight-bold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Toggle Category selection in Doctor Add Form
        $('#add-doc-cat').change(function() {
            var val = $(this).val();
            if (val == '1') {
                $('#grp-poly-select').show();
                $('#grp-tindakan-select').hide();
            } else if (val == '2') {
                $('#grp-poly-select').hide();
                $('#grp-tindakan-select').show();
            } else {
                $('#grp-poly-select').show();
                $('#grp-tindakan-select').show();
            }
        });

        $('#edit-doc-cat').change(function() {
            var val = $(this).val();
            if (val == '1') {
                $('#edit-grp-poly-select').show();
                $('#edit-grp-tindakan-select').hide();
            } else if (val == '2') {
                $('#edit-grp-poly-select').hide();
                $('#edit-grp-tindakan-select').show();
            } else {
                $('#edit-grp-poly-select').show();
                $('#edit-grp-tindakan-select').show();
            }
        });

        // Edit Category mapping
        $('.btn-edit-cat').click(function() {
            $('#edit-cat-id').val($(this).data('id'));
            $('#edit-cat-name').val($(this).data('name'));
            $('#edit-cat-desc').val($(this).data('desc'));
        });

        // Edit Polyclinic mapping
        $('.btn-edit-poly').click(function() {
            $('#edit-poly-id').val($(this).data('id'));
            $('#edit-poly-name').val($(this).data('name'));
            $('#edit-poly-desc').val($(this).data('desc'));
            $('#edit-poly-status').val($(this).data('status'));
        });

        // Edit Service (Tindakan) mapping
        $('.btn-edit-service').click(function() {
            $('#edit-srv-id').val($(this).data('id'));
            $('#edit-srv-code').val($(this).data('code'));
            $('#edit-srv-name').val($(this).data('name'));
            $('#edit-srv-desc').val($(this).data('desc'));
            $('#edit-srv-parent').val($(this).data('parent') || '');
            $('#edit-srv-price').val($(this).data('price'));
            $('#edit-srv-status').val($(this).data('status'));
        });

        // Edit Doctor mapping
        $('.btn-edit-doctor').click(function() {
            var catId = $(this).data('cat');
            $('#edit-doc-id').val($(this).data('id'));
            $('#edit-doc-nik').val($(this).data('nik'));
            $('#edit-doc-name').val($(this).data('name'));
            $('#edit-doc-cat').val(catId).trigger('change');
            $('#edit-doc-poly').val($(this).data('poly'));
            $('#edit-doc-tindakan').val($(this).data('tindakan'));
            $('#edit-doc-fee').val($(this).data('fee'));
            $('#edit-doc-status').val($(this).data('status'));
        });

                // Edit Room mapping
        $('.btn-edit-room').click(function() {
            $('#edit-room-id').val($(this).data('id'));
            $('#edit-room-code').val($(this).data('code'));
            $('#edit-room-name').val($(this).data('name'));
            $('#edit-room-type').val($(this).data('type'));
            $('#edit-room-floor').val($(this).data('floor'));
            $('#edit-room-capacity').val($(this).data('capacity'));
            $('#edit-room-status').val($(this).data('status'));
        });

        // Edit Bed mapping
        $('.btn-edit-bed').click(function() {
            $('#edit-bed-id').val($(this).data('id'));
            $('#edit-bed-room-id').val($(this).data('room'));
            $('#edit-bed-number').val($(this).data('bed'));
            $('#edit-bed-tariff').val($(this).data('tariff'));
            $('#edit-bed-status').val($(this).data('status'));
        });

        // Edit Medicine Category mapping
        $('.btn-edit-medcat').click(function() {
            $('#edit-medcat-id').val($(this).data('id'));
            $('#edit-medcat-code').val($(this).data('code'));
            $('#edit-medcat-name').val($(this).data('name'));
            $('#edit-medcat-class').val($(this).data('class'));
            $('#edit-medcat-desc').val($(this).data('desc'));
            $('#edit-medcat-status').val($(this).data('status'));
        });

        // Edit Unit mapping
        $('.btn-edit-unit').click(function() {
            $('#edit-unit-id').val($(this).data('id'));
            $('#edit-unit-code').val($(this).data('code'));
            $('#edit-unit-name').val($(this).data('name'));
            $('#edit-unit-category').val($(this).data('category'));
            $('#edit-unit-status').val($(this).data('status'));
        });

        // Edit Consent Template mapping
        $('.btn-edit-consent').click(function() {
            $('#edit-consent-id').val($(this).data('id'));
            $('#edit-consent-tindakan').val($(this).data('tindakan') || '');
            $('#edit-consent-title').val($(this).data('title'));
            $('#edit-consent-diag').val($(this).data('diag'));
            $('#edit-consent-proc').val($(this).data('proc'));
            $('#edit-consent-goal').val($(this).data('goal'));
            $('#edit-consent-risks').val($(this).data('risks'));
            $('#edit-consent-prog').val($(this).data('prog'));
            $('#edit-consent-alt').val($(this).data('alt'));
            $('#edit-consent-status').val($(this).data('status'));
        });

        // Edit Department mapping
        $('.btn-edit-dept').click(function() {
            $('#edit-dept-id').val($(this).data('id'));
            $('#edit-dept-code').val($(this).data('code'));
            $('#edit-dept-name').val($(this).data('name'));
            $('#edit-dept-desc').val($(this).data('desc'));
            $('#edit-dept-status').val($(this).data('status'));
        });

        // Edit Job Position mapping
        $('.btn-edit-job').click(function() {
            $('#edit-job-id').val($(this).data('id'));
            $('#edit-job-dept').val($(this).data('dept'));
            $('#edit-job-code').val($(this).data('code'));
            $('#edit-job-title').val($(this).data('title'));
            $('#edit-job-status').val($(this).data('status'));
        });

                // Edit Lab Test mapping
        $('.btn-edit-lab').click(function() {
            $('#edit-lab-id').val($(this).data('id'));
            $('#edit-lab-code').val($(this).data('code'));
            $('#edit-lab-name').val($(this).data('name'));
            $('#edit-lab-cat').val($(this).data('cat'));
            $('#edit-lab-spec').val($(this).data('spec'));
            $('#edit-lab-range').val($(this).data('range'));
            $('#edit-lab-unit').val($(this).data('unit'));
            $('#edit-lab-price').val($(this).data('price'));
            $('#edit-lab-status').val($(this).data('status'));
        });

        // Edit Insurance Provider mapping
        $('.btn-edit-insurance').click(function() {
            $('#edit-ins-id').val($(this).data('id'));
            $('#edit-ins-code').val($(this).data('code'));
            $('#edit-ins-name').val($(this).data('name'));
            $('#edit-ins-type').val($(this).data('type'));
            $('#edit-ins-phone').val($(this).data('phone'));
            $('#edit-ins-email').val($(this).data('email'));
            $('#edit-ins-address').val($(this).data('address'));
            $('#edit-ins-pic').val($(this).data('pic'));
            $('#edit-ins-status').val($(this).data('status'));
        });

        // Edit Doctor Schedule mapping
        $('.btn-edit-schedule').click(function() {
            $('#edit-sched-id').val($(this).data('id'));
            $('#edit-sched-doctor').val($(this).data('doctor'));
            $('#edit-sched-room').val($(this).data('room'));
            $('#edit-sched-day').val($(this).data('day'));
            $('#edit-sched-start').val($(this).data('start').substring(0, 5));
            $('#edit-sched-end').val($(this).data('end').substring(0, 5));
            $('#edit-sched-quota').val($(this).data('quota'));
            $('#edit-sched-status').val($(this).data('status'));
        });

        // Edit Supplier mapping
        $('.btn-edit-supplier').click(function() {
            $('#edit-sup-id').val($(this).data('id'));
            $('#edit-sup-code').val($(this).data('code'));
            $('#edit-sup-name').val($(this).data('name'));
            $('#edit-sup-phone').val($(this).data('phone'));
            $('#edit-sup-pic').val($(this).data('pic'));
            $('#edit-sup-email').val($(this).data('email'));
            $('#edit-sup-address').val($(this).data('address'));
            $('#edit-sup-bankname').val($(this).data('bankname'));
            $('#edit-sup-bankacc').val($(this).data('bankacc'));
            $('#edit-sup-status').val($(this).data('status'));
        });
        // =====================================================================
        // DIGITAL SIGNATURE CANVAS FOR DOCTORS
        // =====================================================================
        const canvas = document.getElementById('canvas-doc-signature');
        let ctx = null;
        let isDrawing = false;
        let hasDrawn = false;

        if (canvas) {
            ctx = canvas.getContext('2d');
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = '#0f172a';

            function getTouchPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * (canvas.width / rect.width),
                    y: (clientY - rect.top) * (canvas.height / rect.height)
                };
            }

            function startDraw(e) {
                isDrawing = true;
                hasDrawn = true;
                const pos = getTouchPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
                if (e.type.startsWith('touch')) e.preventDefault();
            }

            function drawMove(e) {
                if (!isDrawing) return;
                const pos = getTouchPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                if (e.type.startsWith('touch')) e.preventDefault();
            }

            function endDraw() {
                isDrawing = false;
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', drawMove);
            window.addEventListener('mouseup', endDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', drawMove, { passive: false });
            window.addEventListener('touchend', endDraw);
        }

        $('#btn-clear-doc-sig').click(function() {
            if (ctx) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
            }
            $('#file-upload-doc-sig').val('');
        });

        // Buka Modal TTD Dokter
        $('.btn-doc-signature').click(function() {
            const docId = $(this).data('id');
            const docName = $(this).data('name');
            const docSip = $(this).data('sip');
            const docSig = $(this).data('sig');

            $('#sig-target-doc-id').val(docId);
            $('#sig-modal-doc-name').text(docName);
            $('#sig-modal-doc-sub').text('SIP: ' + (docSip || '-'));

            if (ctx) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawn = false;
            }
            $('#file-upload-doc-sig').val('');

            if (docSig) {
                $('#sig-current-preview-img').attr('src', docSig);
                $('#sig-current-preview-box').show();
            } else {
                $('#sig-current-preview-box').hide();
            }

            $('#doctorSignatureModal').modal('show');
        });

        // Handler Upload File Gambar TTD
        $('#file-upload-doc-sig').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const img = new Image();
                    img.onload = function() {
                        if (ctx) {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            ctx.drawImage(img, (canvas.width - 200) / 2, (canvas.height - 100) / 2, 200, 100);
                            hasDrawn = true;
                        }
                    };
                    img.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Simpan TTD Dokter ke Database via AJAX
        $('#btn-save-doc-signature').click(function() {
            const docId = $('#sig-target-doc-id').val();
            if (!docId) {
                alert('ID Dokter tidak valid.');
                return;
            }

            let sigDataUrl = '';
            if (hasDrawn && canvas) {
                sigDataUrl = canvas.toDataURL('image/png');
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url('system/master-klinik/save-doctor-signature') ?>',
                type: 'POST',
                data: {
                    doctor_id: docId,
                    digital_signature: sigDataUrl,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(resp) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Tanda Tangan ke Database');
                    if (resp.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(resp.message);
                        else alert(resp.message);
                        $('#doctorSignatureModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 600);
                    } else {
                        alert(resp.message || 'Gagal menyimpan tanda tangan.');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Tanda Tangan ke Database');
                    alert('Terjadi kesalahan jaringan saat menyimpan tanda tangan.');
                }
            });
        });

        // Pastikan tabel menyesuaikan ukuran ketika tab diklik
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
        });
    });
</script>
<?= $this->endSection() ?>
