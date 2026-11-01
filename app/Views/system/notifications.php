<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Header Hero & Action Bar -->
<div class="card notif-hero-card p-4 mb-3 shadow-none">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-2">
                <div class="notif-hero-icon p-2 rounded mr-3" style="font-size: 26px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h4 class="font-weight-bold notif-hero-title mb-0" style="letter-spacing: -0.3px;">
                        Riwayat Notifikasi & Manajemen Aturan (Notification Rules)
                    </h4>
                    <span class="notif-hero-subtitle" style="font-size: 13.5px;">
                        Pusat Monitoring Peringatan Real-time, Accounting Balance Alerts, Audit Log Baca & Konfigurasi Rules
                    </span>
                </div>
            </div>
            <p class="notif-hero-desc text-xs mb-0">
                Sistem tidak membiarkan anomali terjadi secara diam-diam. Seluruh peringatan ketidakseimbangan jurnal, stok kritis, piutang menumpuk, dan insiden keamanan terekam permanen dengan jejak audit pembaca (*Read Receipts*).
            </p>
        </div>
        <div class="col-lg-5 mt-3 mt-lg-0 text-lg-right">
            <a href="<?= base_url('system/notifications/mark-all-read') ?>" class="btn btn-teal btn-sm font-weight-bold shadow-none mb-2">
                <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
            </a>
            <a href="<?= base_url('accounting/jurnal') ?>" class="btn btn-outline-danger btn-sm font-weight-bold shadow-none mb-2 ml-1">
                <i class="fas fa-scale-unbalanced mr-1"></i> Audit Keseimbangan Jurnal
            </a>
        </div>
    </div>
</div>

<!-- Metrics Overview Cards -->
<div class="row mb-3">
    <div class="col-md-3 col-6 mb-2">
        <div class="card notif-kpi-card p-3 shadow-none text-center" style="border-left: 4px solid #dc2626 !important;">
            <div class="text-xs font-weight-bold notif-kpi-label text-uppercase mb-1">Notifikasi Belum Dibaca</div>
            <div class="h4 font-weight-bold notif-kpi-val-danger mb-0"><?= $unreadCount ?></div>
            <small class="notif-kpi-sub" style="font-size: 11px;">Perlu ditindaklanjuti</small>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card notif-kpi-card p-3 shadow-none text-center" style="border-left: 4px solid #0d9f4f !important;">
            <div class="text-xs font-weight-bold notif-kpi-label text-uppercase mb-1">Total Riwayat Tersimpan</div>
            <div class="h4 font-weight-bold notif-kpi-val-dark mb-0"><?= $totalCount ?></div>
            <small class="notif-kpi-sub" style="font-size: 11px;">Arsip permanen database</small>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card notif-kpi-card p-3 shadow-none text-center" style="border-left: 4px solid #0284c7 !important;">
            <div class="text-xs font-weight-bold notif-kpi-label text-uppercase mb-1">Aturan Notifikasi Aktif</div>
            <div class="h4 font-weight-bold notif-kpi-val-primary mb-0"><?= count(array_filter($rules, fn($r) => $r->is_active == 1)) ?> / <?= count($rules) ?></div>
            <small class="notif-kpi-sub" style="font-size: 11px;">Rules Sentinel Engine</small>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="card notif-kpi-card p-3 shadow-none text-center" style="border-left: 4px solid #f59e0b !important;">
            <div class="text-xs font-weight-bold notif-kpi-label text-uppercase mb-1">Accounting & Security</div>
            <div class="h4 font-weight-bold notif-kpi-val-warning mb-0">AKTIF 24/7</div>
            <small class="notif-kpi-sub" style="font-size: 11px;">Deteksi anomali real-time</small>
        </div>
    </div>
</div>

<!-- Navigasi Tab Notifikasi & Rules -->
<div class="card notif-main-card p-0 mb-4 shadow-none">
    <div class="card-header notif-main-header p-2 border-bottom d-flex justify-content-between align-items-center">
        <ul class="nav nav-pills" id="notif-tabs">
            <li class="nav-item">
                <a class="nav-link notif-nav-pill <?= empty($_GET['tab']) || $_GET['tab'] === 'history' ? 'active' : '' ?> font-weight-bold" href="#tab-history" data-toggle="pill">
                    <i class="fas fa-list-check mr-1"></i> 1. Riwayat Notifikasi Masuk (<?= count($notifications) ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link notif-nav-pill <?= !empty($_GET['tab']) && $_GET['tab'] === 'rules' ? 'active' : '' ?> font-weight-bold" href="#tab-rules" data-toggle="pill">
                    <i class="fas fa-sliders mr-1"></i> 2. Manajemen Aturan Notifikasi / Rules Engine (<?= count($rules) ?>)
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content">
            
            <!-- ========================================================================= -->
            <!-- TAB 1: RIWAYAT NOTIFIKASI MASUK -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade <?= empty($_GET['tab']) || $_GET['tab'] === 'history' ? 'show active' : '' ?>" id="tab-history">
                
                <!-- Filter Kategori & Status -->
                <div class="d-flex flex-wrap align-items-center justify-content-between notif-filter-bar mb-3 pb-2">
                    <div class="btn-group btn-group-sm mb-2" role="group">
                        <a href="<?= base_url('system/notifications') ?>" class="btn <?= empty($category) && !$onlyUnread ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            Semua
                        </a>
                        <a href="<?= base_url('system/notifications?unread=1') ?>" class="btn <?= $onlyUnread ? 'btn-danger' : 'btn-outline-secondary' ?> font-weight-bold">
                            Belum Dibaca (<?= $unreadCount ?>)
                        </a>
                        <a href="<?= base_url('system/notifications?category=accounting') ?>" class="btn <?= $category === 'accounting' ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            <i class="fas fa-scale-balanced mr-1"></i> Accounting
                        </a>
                        <a href="<?= base_url('system/notifications?category=security') ?>" class="btn <?= $category === 'security' ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            <i class="fas fa-shield-halved mr-1"></i> Keamanan & APM
                        </a>
                        <a href="<?= base_url('system/notifications?category=pharmacy') ?>" class="btn <?= $category === 'pharmacy' ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            <i class="fas fa-pills mr-1"></i> Farmasi
                        </a>
                        <a href="<?= base_url('system/notifications?category=clinical') ?>" class="btn <?= $category === 'clinical' ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            <i class="fas fa-user-doctor mr-1"></i> Pelayanan
                        </a>
                        <a href="<?= base_url('system/notifications?category=cashier') ?>" class="btn <?= $category === 'cashier' ? 'btn-teal' : 'btn-outline-secondary' ?> font-weight-bold">
                            <i class="fas fa-cash-register mr-1"></i> Kasir
                        </a>
                    </div>
                    <div class="notif-filter-count text-xs mb-2">
                        Menampilkan <?= count($notifications) ?> rekaman notifikasi terkini
                    </div>
                </div>

                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5 notif-empty-state">
                        <i class="fas fa-bell-slash fa-3x mb-2 text-secondary"></i>
                        <p class="font-weight-bold notif-item-title mb-1">Tidak ada rekaman notifikasi pada kriteria ini.</p>
                        <small class="notif-hero-desc">Seluruh sistem beroperasi dalam batas normal dan terpantau aman.</small>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($notifications as $n): ?>
                            <?php
                            $accentColor = '#0d9f4f';
                            $badgeClass = 'badge-success';
                            if ($n->type === 'danger') {
                                $accentColor = '#dc2626';
                                $badgeClass = 'badge-danger';
                            } elseif ($n->type === 'warning') {
                                $accentColor = '#d97706';
                                $badgeClass = 'badge-warning';
                            } elseif ($n->type === 'info') {
                                $accentColor = '#0284c7';
                                $badgeClass = 'badge-info';
                            } elseif ($n->type === 'teal') {
                                $accentColor = '#0d9f4f';
                                $badgeClass = 'badge-success';
                            }
                            $isRead = (int) $n->is_read === 1;
                            ?>
                            <div class="list-group-item notif-item-card p-3 mb-2 <?= !$isRead ? 'notif-unread-item' : '' ?>" style="border-left: 5px solid <?= $accentColor ?> !important;">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="<?= esc($n->icon ?: 'fas fa-bell') ?> mr-2" style="color: <?= $accentColor ?>; font-size: 16px;"></i>
                                            <span class="badge <?= $badgeClass ?> font-weight-bold mr-2" style="font-size: 10.5px; text-transform: uppercase;">
                                                <?= esc($n->badge ?: ucfirst($n->category)) ?>
                                            </span>
                                            <span class="notif-item-title font-weight-bold" style="font-size: 14px;">
                                                <?= esc($n->title) ?>
                                            </span>
                                            <?php if (!$isRead): ?>
                                                <span class="badge badge-danger ml-2 font-weight-bold" style="font-size: 9.5px;">BARU / UNREAD</span>
                                            <?php else: ?>
                                                <span class="badge notif-read-badge ml-2" style="font-size: 9.5px;"><i class="fas fa-check text-success mr-1"></i> Dibaca: <?= date('d/m/y H:i', strtotime($n->read_at)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="notif-item-msg text-xs mb-2" style="line-height: 1.45; font-size: 12.5px;">
                                            <?= esc($n->message) ?>
                                        </p>
                                        <div class="notif-item-meta" style="font-size: 11px;">
                                            <i class="fas fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($n->created_at)) ?> WIB &bull;
                                            <i class="fas fa-paper-plane mr-1 ml-1"></i> Pengirim: <?= esc($n->sender_name) ?> &bull;
                                            <i class="fas fa-users-gear mr-1 ml-1"></i> Target: <?= esc($n->target_roles ?: 'Semua Pengguna') ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-2 mt-md-0">
                                        <?php if (!empty($n->link)): ?>
                                            <a href="<?= esc($n->link) ?>" class="btn btn-teal btn-xs font-weight-bold shadow-none mr-1">
                                                <i class="fas fa-arrow-up-right-from-square mr-1"></i> Buka Modul Terkait
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$isRead): ?>
                                            <a href="<?= base_url('system/notifications/mark-read/' . $n->id) ?>" class="btn btn-outline-secondary btn-xs font-weight-bold shadow-none mr-1 btn-mark-read" data-id="<?= $n->id ?>">
                                                <i class="fas fa-check mr-1"></i> Tandai Dibaca
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-info btn-xs font-weight-bold btn-view-receipts shadow-none" data-id="<?= $n->id ?>" data-title="<?= esc($n->title) ?>" title="Audit Jejak Pembaca">
                                            <i class="fas fa-eye mr-1"></i> Jejak Pembaca
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 2: MANAJEMEN ATURAN NOTIFIKASI (RULES ENGINE) -->
            <!-- ========================================================================= -->
            <div class="tab-pane fade <?= !empty($_GET['tab']) && $_GET['tab'] === 'rules' ? 'show active' : '' ?>" id="tab-rules">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold notif-rules-header-title mb-0"><i class="fas fa-sliders text-teal mr-2"></i> Konfigurasi Aturan Notifikasi Sentinel Engine</h5>
                        <small class="notif-rules-header-sub">Aktifkan atau nonaktifkan pemicu otomatis peringatan sistem per kategori operasional.</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-clean-grid mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Kode & Nama Aturan</th>
                                <th>Kategori</th>
                                <th>Tingkat Keparahan</th>
                                <th>Target Peran Pengguna</th>
                                <th>Deskripsi Pemicu</th>
                                <th style="width: 120px;" class="text-center">Status</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px;">
                            <?php $no = 1; foreach ($rules as $r): ?>
                                <?php
                                $sevBadge = 'badge-success';
                                if ($r->severity === 'danger') $sevBadge = 'badge-danger';
                                elseif ($r->severity === 'warning') $sevBadge = 'badge-warning';
                                elseif ($r->severity === 'info') $sevBadge = 'badge-info';
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="<?= esc($r->icon ?: 'fas fa-bell') ?> text-teal mr-2"></i>
                                            <div>
                                                <strong class="notif-rule-title d-block"><?= esc($r->rule_name) ?></strong>
                                                <code class="notif-rule-code" style="font-size: 10px;"><?= esc($r->rule_code) ?></code>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge notif-cat-badge text-uppercase font-weight-bold">
                                            <?= esc($r->category) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $sevBadge ?> font-weight-bold text-uppercase">
                                            <?= esc($r->severity) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="notif-role-tag font-weight-bold"><?= esc($r->target_roles) ?></small>
                                    </td>
                                    <td>
                                        <span class="notif-rule-desc"><?= esc($r->description) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($r->is_active): ?>
                                            <span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-check-circle mr-1"></i> AKTIF</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary font-weight-bold px-2 py-1"><i class="fas fa-ban mr-1"></i> NONAKTIF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('system/notifications/toggle-rule/' . $r->id) ?>" class="btn btn-xs <?= $r->is_active ? 'btn-outline-danger' : 'btn-teal' ?> font-weight-bold shadow-none" onclick="return confirm('Apakah Anda yakin ingin mengubah status aturan notifikasi ini?')">
                                            <?= $r->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
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
</div>

<!-- Modal Read Receipts (Audit Jejak Pembaca) -->
<div class="modal fade notif-modal" id="modalReadReceipts" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content notif-modal-content" style="border-radius: 4px;">
            <div class="modal-header notif-modal-header p-3 border-bottom">
                <h5 class="modal-title notif-modal-title font-weight-bold" style="font-size: 15px;">
                    <i class="fas fa-eye text-teal mr-2"></i> Jejak Audit Pembaca Notifikasi
                </h5>
                <button type="button" class="close notif-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body notif-modal-body p-3">
                <div class="p-2 mb-3 notif-receipt-banner rounded">
                    <small class="notif-receipt-banner-label d-block">Judul Notifikasi:</small>
                    <strong id="receipt-modal-title" class="notif-receipt-banner-title"></strong>
                </div>
                <div id="receipt-list-container">
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data jejak pembaca...
                    </div>
                </div>
            </div>
            <div class="modal-footer notif-modal-footer p-2 border-top">
                <button type="button" class="btn btn-secondary btn-sm font-weight-bold shadow-none" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sync Tabs with URL Hash
    if (window.location.hash) {
        $('#notif-tabs a[href="' + window.location.hash + '"]').tab('show');
    }
    $('#notif-tabs a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        if (history.replaceState) {
            history.replaceState(null, null, e.target.hash);
        } else {
            window.location.hash = e.target.hash;
        }
    });

    // 2. Modal View Read Receipts (AJAX)
    $('.btn-view-receipts').on('click', function() {
        const notifId = $(this).data('id');
        const notifTitle = $(this).data('title');

        $('#receipt-modal-title').text(notifTitle);
        $('#receipt-list-container').html('<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat jejak pembaca...</div>');
        $('#modalReadReceipts').modal('show');

        $.ajax({
            url: '<?= base_url("system/notifications/receipts/") ?>/' + notifId,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.status === 'success') {
                    if (!res.receipts || res.receipts.length === 0) {
                        $('#receipt-list-container').html('<div class="text-center py-4 text-muted"><i class="fas fa-envelope-open mr-1"></i> Belum ada pengguna yang membaca notifikasi ini.</div>');
                    } else {
                        let html = '<div class="list-group list-group-flush">';
                        res.receipts.forEach(function(r) {
                            html += `
                                <div class="list-group-item notif-receipt-row px-0 py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="notif-receipt-user d-block" style="font-size: 13px;">${r.user_name} (${r.username})</strong>
                                            <span class="badge notif-receipt-role border" style="font-size: 10px;">${r.role_name || 'Staf'}</span>
                                        </div>
                                        <div class="text-right notif-receipt-time" style="font-size: 11px;">
                                            <i class="fas fa-check-double text-teal mr-1"></i> ${r.read_at}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#receipt-list-container').html(html);
                    }
                } else {
                    $('#receipt-list-container').html('<div class="text-danger text-center py-2">Gagal memuat jejak pembaca.</div>');
                }
            },
            error: function() {
                $('#receipt-list-container').html('<div class="text-danger text-center py-2">Terjadi kesalahan jaringan.</div>');
            }
        });
    });
});
</script>

<style>
/* Desain Tabel Bersih & Border Konsisten Sesuai desain_tabel.md */
.table-clean-grid {
    border-collapse: collapse !important;
    width: 100% !important;
}
.table-clean-grid thead th {
    background-color: #f8fafc !important;
    color: #1e293b !important;
    border: 1px solid #b8b8b8 !important;
    font-weight: 600;
    vertical-align: middle;
}
.table-clean-grid tbody td {
    border: 1px solid #b8b8b8 !important;
    background-color: #ffffff;
    vertical-align: middle;
}
.table-clean-grid tbody tr:hover td {
    background-color: #f1f5f9 !important;
}

.notif-hero-card,
.notif-kpi-card,
.notif-main-card {
    border: 1px solid #b8b8b8 !important;
    background-color: #ffffff;
}

/* Paper Dark Mode Palette Support */
body.dark-mode .card,
body.dark-mode .notif-hero-card,
body.dark-mode .notif-kpi-card,
body.dark-mode .notif-main-card {
    background-color: #1f2328 !important;
    border-color: #374151 !important;
    color: #f0f6fc !important;
}
body.dark-mode .card-header,
body.dark-mode .notif-main-header {
    background-color: #1f2328 !important;
    border-color: #374151 !important;
    color: #f0f6fc !important;
}
body.dark-mode .card-body {
    background-color: #1f2328 !important;
    color: #f0f6fc !important;
}
body.dark-mode .modal-content,
body.dark-mode .notif-modal-content {
    background-color: #1f2328 !important;
    border-color: #374151 !important;
    color: #f0f6fc !important;
}
body.dark-mode .modal-header,
body.dark-mode .modal-body,
body.dark-mode .modal-footer {
    background-color: #1f2328 !important;
    border-color: #374151 !important;
    color: #f0f6fc !important;
}
body.dark-mode .table-clean-grid thead th {
    background-color: #181b1f !important;
    color: #f0f6fc !important;
    border: 1px solid #374151 !important;
}
body.dark-mode .table-clean-grid tbody td {
    background-color: #1f2328 !important;
    color: #f0f6fc !important;
    border: 1px solid #374151 !important;
}
body.dark-mode .table-clean-grid tbody tr:hover td {
    background-color: #262c36 !important;
}
body.dark-mode .notif-hero-title,
body.dark-mode .notif-kpi-val-dark {
    color: #f0f6fc !important;
}
body.dark-mode .notif-hero-subtitle,
body.dark-mode .notif-hero-desc,
body.dark-mode .notif-kpi-label,
body.dark-mode .notif-kpi-sub {
    color: #9ca3af !important;
}
body.dark-mode .notif-receipt-banner {
    background-color: #181b1f !important;
    border: 1px solid #374151 !important;
}
body.dark-mode .list-group-item {
    background-color: #1f2328 !important;
    border-color: #374151 !important;
    color: #f0f6fc !important;
}
</style>
<?= $this->endSection() ?>
