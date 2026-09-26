<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-shield-halved text-teal mr-2"></i> Audit Trail & Log Keamanan Sistem
                </h1>
                <small class="text-muted">Pelacakan riwayat aktivitas pengguna, perubahan data, transaksi kritis, dan akses keamanan</small>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <!-- FILTER TOOLBAR -->
        <form method="get" action="<?= base_url('system/audit') ?>" class="mb-3 bg-light p-2 rounded border">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text font-weight-bold bg-white"><i class="fas fa-bolt mr-1"></i> Aksi:</span>
                        </div>
                        <select name="action" class="form-control font-weight-bold">
                            <option value="">-- Semua Aksi --</option>
                            <option value="LOGIN" <?= $filterAction === 'LOGIN' ? 'selected' : '' ?>>LOGIN (Masuk Akun)</option>
                            <option value="LOGOUT" <?= $filterAction === 'LOGOUT' ? 'selected' : '' ?>>LOGOUT (Keluar)</option>
                            <option value="CREATE" <?= $filterAction === 'CREATE' ? 'selected' : '' ?>>CREATE (Tambah Data)</option>
                            <option value="UPDATE" <?= $filterAction === 'UPDATE' ? 'selected' : '' ?>>UPDATE (Ubah / Edit)</option>
                            <option value="DELETE" <?= $filterAction === 'DELETE' ? 'selected' : '' ?>>DELETE (Hapus Data)</option>
                            <option value="PAYMENT" <?= $filterAction === 'PAYMENT' ? 'selected' : '' ?>>PAYMENT (Pembayaran / Kasir)</option>
                            <option value="APPROVE" <?= $filterAction === 'APPROVE' ? 'selected' : '' ?>>APPROVE (Persetujuan PO)</option>
                            <option value="DISPENSE" <?= $filterAction === 'DISPENSE' ? 'selected' : '' ?>>DISPENSE (Tebus Resep)</option>
                            <option value="EXPORT" <?= $filterAction === 'EXPORT' ? 'selected' : '' ?>>EXPORT (Cetak / Unduh)</option>
                            <option value="OPTIMIZE" <?= $filterAction === 'OPTIMIZE' ? 'selected' : '' ?>>OPTIMIZE (Pemeliharaan)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text font-weight-bold bg-white"><i class="fas fa-cubes mr-1"></i> Modul:</span>
                        </div>
                        <select name="module" class="form-control font-weight-bold">
                            <option value="">-- Semua Modul --</option>
                            <?php foreach ($modules as $m): ?>
                                <option value="<?= esc($m->module) ?>" <?= $filterModule === $m->module ? 'selected' : '' ?>><?= esc($m->module) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text font-weight-bold bg-white"><i class="fas fa-calendar mr-1"></i> Tanggal:</span>
                        </div>
                        <input type="date" name="date" class="form-control" value="<?= esc($filterDate) ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold mr-1">
                        <i class="fas fa-filter mr-1"></i> Filter Log
                    </button>
                    <a href="<?= base_url('system/audit') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- AUDIT LOGS TABLE -->
        <div class="card card-teal card-outline shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-list-check text-teal mr-1"></i> Catatan Riwayat Aktivitas Pengguna & Log Keamanan Sistem
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-audit" class="table table-striped table-bordered table-hover datatable-serverside" style="font-size: 13px; width: 100%;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">NO</th>
                                <th style="width: 150px;">Waktu & Tanggal</th>
                                <th style="width: 140px;">Pengguna (User)</th>
                                <th style="width: 130px;">Modul Sistem</th>
                                <th style="width: 110px;" class="text-center">Jenis Aksi</th>
                                <th>Uraian Aktivitas & Data Terkait</th>
                                <th style="width: 120px;">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris tabel diisi secara instan oleh DataTables Server-Side Processing -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table-audit').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('system/audit') ?>' + window.location.search,
                type: 'GET'
            },
            order: [[1, 'desc']],
            columnDefs: [
                { targets: [0], orderable: false, className: 'text-center' },
                { targets: [4], className: 'text-center' }
            ],
            language: window.dtIndonesianLanguage,
            pageLength: 15
        });
    });
</script>
<?= $this->endSection() ?>
