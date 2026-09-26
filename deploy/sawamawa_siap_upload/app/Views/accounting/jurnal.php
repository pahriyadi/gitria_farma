<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- Quick Switcher Tabs -->
<div class="mb-3 d-flex align-items-center justify-content-between">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" href="<?= base_url('accounting/jurnal') ?>">
                <i class="fas fa-file-lines mr-1"></i> 1. Jurnal Umum Konsolidasian
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/buku-besar') ?>">
                <i class="fas fa-book-journal-whills mr-1"></i> 2. Jurnal Mutasi per Akun (Buku Pembantu & Saldo Berjalan)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" href="<?= base_url('accounting/laporan') ?>">
                <i class="fas fa-chart-pie mr-1"></i> 3. Laporan Keuangan
            </a>
        </li>
    </ul>
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-teal btn-sm font-weight-bold mr-2 shadow-none" type="button" data-toggle="collapse" data-target="#guideDoubleEntry" aria-expanded="true" aria-controls="guideDoubleEntry">
            <i class="fas fa-book-open-reader mr-1"></i> <span id="guideBtnText">Panduan Cara Baca Jurnal</span>
        </button>
        <button class="btn btn-teal btn-sm font-weight-bold shadow-none" data-toggle="modal" data-target="#journalModal">
            <i class="fas fa-plus mr-1"></i> Input Jurnal Manual
        </button>
    </div>
</div>

<!-- Interactive Collapsible Guide: Cara Membaca Jurnal Double-Entry -->
<div class="collapse show mb-3" id="guideDoubleEntry">
    <div class="card shadow-none bg-white text-dark" style="border: 1px solid #17a2b8; border-radius: 6px; overflow: hidden;">
        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #e2e8f0;">
            <span class="font-weight-bold text-dark text-sm">
                <i class="fas fa-graduation-cap text-teal mr-1"></i> <strong>Panduan Cepat: Cara Membaca Jurnal Umum Double-Entry (Pembukuan Berpasangan)</strong>
            </span>
            <button type="button" class="close text-secondary" data-toggle="collapse" data-target="#guideDoubleEntry" aria-label="Tutup" style="font-size: 16px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="p-2 rounded bg-light border h-100" style="font-size: 12px;">
                        <strong class="text-teal d-block mb-1"><i class="fas fa-pills mr-1"></i> 1. Penjualan Obat (Apotek / Kasir)</strong>
                        <div class="text-muted" style="line-height: 1.4;">
                            &bull; <strong>Debet (Masuk):</strong> Uang tunai masuk ke laci Kas Kasir Utama (1-101).<br>
                            &bull; <strong>Kredit (Keluar/Sumber):</strong> Sumber dana berasal dari omzet penjualan obat (4-102) dengan rincian nama obat, qty, dan subtotalnya.
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="p-2 rounded bg-light border h-100" style="font-size: 12px;">
                        <strong class="text-info d-block mb-1"><i class="fas fa-user-doctor mr-1"></i> 2. Layanan Dokter &amp; Poliklinik</strong>
                        <div class="text-muted" style="line-height: 1.4;">
                            &bull; <strong>Debet (Masuk):</strong> Pasien membayar biaya konsultasi/tindakan ke Kasir (1-101).<br>
                            &bull; <strong>Kredit (Keluar/Sumber):</strong> Dialokasikan sebagai Pendapatan Poliklinik (4-101) disertai nama dokter pemeriksa &amp; jenis tindakan medis.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 rounded bg-light border h-100" style="font-size: 12px;">
                        <strong class="text-primary d-block mb-1"><i class="fas fa-scale-balanced mr-1"></i> 3. Prinsip Keseimbangan (Balance)</strong>
                        <div class="text-muted" style="line-height: 1.4;">
                            &bull; <strong>Total Debet = Total Kredit:</strong> Setiap transaksi harus selalu seimbang 100%.<br>
                            &bull; <strong>Koreksi Data:</strong> Jurnal otomatis transaksi terkunci demi keamanan stok &amp; audit. Jika ada koreksi, gunakan <em>+ Input Jurnal Manual</em> atau void kasir.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Panel Modul & Periode Tanggal -->
<div class="card p-3 mb-3 bg-white shadow-none" style="border: 1px solid #b8b8b8;">
    <div class="row align-items-end">
        <div class="col-md-4 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-layer-group text-teal mr-1"></i> Filter Berdasarkan Modul Sumber:</label>
            <select id="filter_source_module" class="form-control form-control-sm font-weight-bold">
                <option value="">-- Semua Modul Sumber Transaksi --</option>
                <?php if (!empty($modules)): ?>
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= esc($m->source_module) ?>"><?= esc($m->source_module) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-day text-teal mr-1"></i> Dari Tanggal:</label>
            <input type="date" id="filter_start_date" class="form-control form-control-sm font-weight-bold">
        </div>
        <div class="col-md-3 form-group mb-2 mb-md-0">
            <label class="text-xs font-weight-bold text-dark mb-1"><i class="fas fa-calendar-check text-teal mr-1"></i> Sampai Tanggal:</label>
            <input type="date" id="filter_end_date" class="form-control form-control-sm font-weight-bold">
        </div>
        <div class="col-md-2 form-group mb-2 mb-md-0">
            <button type="button" id="btnFilterJurnal" class="btn btn-teal btn-sm btn-block font-weight-bold">
                <i class="fas fa-filter mr-1"></i> Terapkan
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-teal shadow-none bg-white" style="border: 1px solid #b8b8b8;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-file-lines text-teal mr-1"></i> Buku Jurnal Umum Konsolidasian &amp; Penyesuaian
                </h6>
                <span class="text-muted text-xs">Pencatatan double-entry otomatis</span>
            </div>
            <div class="card-body p-3">
                <table id="table-jurnal" class="table table-bordered table-striped datatable-serverside text-dark" style="width: 100%; font-size: 13px;">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 95px;">Tanggal</th>
                            <th style="width: 160px;">No Jurnal &amp; Modul</th>
                            <th>Keterangan / Uraian Transaksi</th>
                            <th style="width: 270px;">Rekening Akun (COA) &amp; Rincian</th>
                            <th style="width: 140px;" class="text-right text-success"><i class="fas fa-arrow-circle-down mr-1"></i>Debet (Masuk)</th>
                            <th style="width: 140px;" class="text-right text-danger"><i class="fas fa-arrow-circle-up mr-1"></i>Kredit (Keluar)</th>
                            <th style="width: 130px;" class="text-right bg-light text-teal font-weight-bold">Sisa Saldo</th>
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

<!-- Modal Add Journal -->
<div class="modal fade" id="journalModal" tabindex="-1" role="dialog" aria-labelledby="journalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: 1px solid #b8b8b8;">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="journalModalLabel"><i class="fas fa-plus mr-1 text-teal"></i> Input Jurnal Manual / Penyesuaian</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/jurnal') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold">Tanggal Pembukuan <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control form-control-sm font-weight-bold" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-xs font-weight-bold">Keterangan / Uraian Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="description" class="form-control form-control-sm" placeholder="Contoh: Pembayaran beban listrik, koreksi saldo kas, penyusutan aset" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- DEBIT SIDE -->
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-success border-bottom pb-2 mb-2" style="font-size: 13px;"><i class="fas fa-plus-circle mr-1"></i> SISI DEBET (PENAMBAHAN BIAYA / ASET)</h6>
                            <div class="form-group mb-2">
                                <label class="text-xs">Pilih Rekening Akun (Debet)</label>
                                <select name="debit_items[0][account_id]" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Akun Debet --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc->id ?>"><?= esc($acc->code) ?> - <?= esc($acc->name) ?> (Rp <?= number_format($acc->balance, 0, ',', '.') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs">Nominal Debet (Rp)</label>
                                <input type="number" step="any" name="debit_items[0][amount]" class="form-control form-control-sm font-weight-bold" placeholder="100000" required min="1">
                            </div>
                        </div>

                        <!-- CREDIT SIDE -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-danger border-bottom pb-2 mb-2" style="font-size: 13px;"><i class="fas fa-minus-circle mr-1"></i> SISI KREDIT (PENGELUARAN KAS / BANK)</h6>
                            <div class="form-group mb-2">
                                <label class="text-xs">Pilih Rekening Akun (Kredit)</label>
                                <select name="credit_items[0][account_id]" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Akun Kredit --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc->id ?>"><?= esc($acc->code) ?> - <?= esc($acc->name) ?> (Rp <?= number_format($acc->balance, 0, ',', '.') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs">Nominal Kredit (Rp)</label>
                                <input type="number" step="any" name="credit_items[0][amount]" class="form-control form-control-sm font-weight-bold" placeholder="100000" required min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-check-double mr-1"></i> Posting Jurnal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Journal Manual -->
<div class="modal fade" id="editJournalModal" tabindex="-1" role="dialog" aria-labelledby="editJournalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: 1px solid #b8b8b8;">
            <div class="modal-header bg-white border-bottom py-2">
                <h5 class="modal-title font-weight-bold text-dark" id="editJournalModalLabel"><i class="fas fa-pen-to-square mr-1 text-info"></i> Edit / Koreksi Jurnal Penyesuaian</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/update-jurnal') ?>" method="post" id="form-edit-jurnal">
                <?= csrf_field() ?>
                <input type="hidden" name="journal_id" id="edit_journal_id">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-weight-bold">Nomor Jurnal</label>
                            <input type="text" id="edit_journal_no" class="form-control form-control-sm font-monospace font-weight-bold bg-light" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-weight-bold">Tanggal Pembukuan <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" id="edit_entry_date" class="form-control form-control-sm font-weight-bold" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-xs font-weight-bold">Uraian / Keterangan <span class="text-danger">*</span></label>
                            <input type="text" name="description" id="edit_description" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <!-- DEBIT SIDE -->
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-success border-bottom pb-2 mb-2" style="font-size: 13px;"><i class="fas fa-plus-circle mr-1"></i> SISI DEBET (DEBIT)</h6>
                            <div class="form-group mb-2">
                                <label class="text-xs">Pilih Rekening Akun Debet</label>
                                <select name="debit_items[0][account_id]" id="edit_debit_account_id" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Akun Debet --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc->id ?>"><?= esc($acc->code) ?> - <?= esc($acc->name) ?> (Rp <?= number_format($acc->balance, 0, ',', '.') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs">Nominal Debet (Rp)</label>
                                <input type="number" step="any" name="debit_items[0][amount]" id="edit_debit_amount" class="form-control form-control-sm font-weight-bold" required min="1">
                            </div>
                        </div>

                        <!-- CREDIT SIDE -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-danger border-bottom pb-2 mb-2" style="font-size: 13px;"><i class="fas fa-minus-circle mr-1"></i> SISI KREDIT (CREDIT)</h6>
                            <div class="form-group mb-2">
                                <label class="text-xs">Pilih Rekening Akun Kredit</label>
                                <select name="credit_items[0][account_id]" id="edit_credit_account_id" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Akun Kredit --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?= $acc->id ?>"><?= esc($acc->code) ?> - <?= esc($acc->name) ?> (Rp <?= number_format($acc->balance, 0, ',', '.') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label class="text-xs">Nominal Kredit (Rp)</label>
                                <input type="number" step="any" name="credit_items[0][amount]" id="edit_credit_amount" class="form-control form-control-sm font-weight-bold" required min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Perubahan Jurnal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var tableJurnal;
    $(document).ready(function() {
        tableJurnal = $('#table-jurnal').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('accounting/jurnal') ?>',
                type: 'GET',
                data: function(d) {
                    d.source_module = $('#filter_source_module').val();
                    d.start_date    = $('#filter_start_date').val();
                    d.end_date      = $('#filter_end_date').val();
                }
            },
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [0, 1], className: 'text-center' },
                { targets: [3, 4, 5, 6], orderable: false }
            ],
            language: window.dtIndonesianLanguage,
            pageLength: 15
        });

        $('#btnFilterJurnal').click(function() {
            tableJurnal.ajax.reload();
        });

        $('#filter_source_module').change(function() {
            tableJurnal.ajax.reload();
        });
    });

    function editJurnal(id) {
        $.getJSON('<?= base_url('accounting/jurnal-json') ?>/' + id, function(res) {
            if (res.status === 'success') {
                var j = res.journal;
                var details = res.details;

                $('#edit_journal_id').val(j.id);
                $('#edit_journal_no').val(j.journal_no);
                $('#edit_entry_date').val(j.entry_date);
                $('#edit_description').val(j.description);

                var debFound = false;
                var crdFound = false;

                $.each(details, function(i, d) {
                    if (parseFloat(d.debit) > 0 && !debFound) {
                        $('#edit_debit_account_id').val(d.account_id);
                        $('#edit_debit_amount').val(parseFloat(d.debit));
                        debFound = true;
                    } else if (parseFloat(d.credit) > 0 && !crdFound) {
                        $('#edit_credit_account_id').val(d.account_id);
                        $('#edit_credit_amount').val(parseFloat(d.credit));
                        crdFound = true;
                    }
                });

                $('#editJournalModal').modal('show');
            } else {
                alert(res.message || 'Gagal memuat data jurnal.');
            }
        });
    }
</script>
<?= $this->endSection() ?>
