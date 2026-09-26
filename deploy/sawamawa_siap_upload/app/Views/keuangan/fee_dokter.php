<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-none" style="border: 1px solid #b8b8b8 !important; background: #ffffff !important; border-radius: 4px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-user-doctor text-teal mr-1"></i> REKAPITULASI & SETTLEMENT JASA MEDIS / FEE DOKTER
                    </h5>
                    <small class="text-muted">Perhitungan bagi hasil dokter dari tindakan & konsultasi poliklinik serta pencatatan otomatis ke Jurnal Akuntansi</small>
                </div>
                <div>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" data-toggle="modal" data-target="#modalSettlement">
                        <i class="fas fa-money-bill-wave mr-1"></i> Proses Pembayaran Fee Baru
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Summary Fee Per Dokter -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-stethoscope text-teal mr-1"></i> Ringkasan Akumulasi Jasa Medis Dokter Belum Dibayar</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover mb-0" style="border: 1px solid #b8b8b8 !important;">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th style="width: 50px;">NO</th>
                                        <th>NAMA DOKTER</th>
                                        <th>SPESIALISASI / POLIKLINIK</th>
                                        <th>TARIF FEE PER PASIEN</th>
                                        <th>JUMLAH PASIEN SELESAI</th>
                                        <th class="text-right">TOTAL HAK JASA MEDIS</th>
                                        <th style="width: 150px;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($unpaidDoctors)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-circle text-success mr-1"></i> Seluruh jasa medis dokter telah diselesaikan (tidak ada tunggakan fee).
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $no = 1; 
                                        foreach ($unpaidDoctors as $ud): 
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><strong class="text-dark"><?= esc($ud->doctor_name) ?></strong></td>
                                                <td><?= esc($ud->poly_name ?? 'Poli Umum') ?></td>
                                                <td class="text-right">Rp <?= number_format($ud->fee_per_pasien, 0, ',', '.') ?></td>
                                                <td class="text-center font-weight-bold text-teal"><?= $ud->patient_count ?> Pasien</td>
                                                <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                                                    Rp <?= number_format($ud->total_earned, 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-teal btn-xs btn-bayar-dokter font-weight-bold" 
                                                            data-id="<?= $ud->doctor_id ?>" 
                                                            data-name="<?= esc($ud->doctor_name) ?>" 
                                                            data-count="<?= $ud->patient_count ?>" 
                                                            data-amount="<?= $ud->total_earned ?>">
                                                        <i class="fas fa-credit-card mr-1"></i> Bayarkan Fee
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px dashed #b8b8b8; margin: 25px 0;">

                <!-- Riwayat Settlement Yang Sudah Dibayarkan -->
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-history text-teal mr-1"></i> Riwayat Pembayaran (Settlement) Fee Dokter Resmi</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th>NO. SETTLEMENT</th>
                                <th>NAMA DOKTER</th>
                                <th>PERIODE PEMBAYARAN</th>
                                <th>TOTAL PASIEN</th>
                                <th class="text-right">TOTAL NOMINAL DIBAYAR</th>
                                <th>METODE BAYAR</th>
                                <th>TANGGAL & JURNAL REF</th>
                                <th style="width: 80px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            foreach ($settlements as $s): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="font-weight-bold text-xs"><code><?= esc($s->settlement_no) ?></code></td>
                                    <td><strong><?= esc($s->doctor_name) ?></strong></td>
                                    <td class="text-center text-xs"><?= date('d/m/Y', strtotime($s->period_start)) ?> s/d <?= date('d/m/Y', strtotime($s->period_end)) ?></td>
                                    <td class="text-center font-weight-bold"><?= $s->total_actions ?> Pasien</td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($s->total_amount, 0, ',', '.') ?></td>
                                    <td><?= esc($s->payment_method) ?></td>
                                    <td class="text-xs">
                                        <?= date('d/m/Y H:i', strtotime($s->paid_at ?? $s->created_at)) ?><br>
                                        <?php if ($s->journal_id): ?>
                                            <span class="badge badge-light border text-primary">Jurnal #<?= $s->journal_id ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success px-2 py-1"><?= strtoupper($s->status) ?></span>
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

<!-- Modal Form Bayar Fee Dokter -->
<div class="modal fade" id="modalSettlement" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('keuangan/bayar-fee-dokter') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content" style="border: 1px solid #b8b8b8 !important; border-radius: 4px;">
                <div class="modal-header bg-teal text-white py-2">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-dollar mr-1"></i> Form Pembayaran Jasa Medis Dokter</h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Pilih Dokter Spesialis / Umum <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="modal-sel-doctor" class="form-control form-control-sm select-searchable font-weight-bold" data-search-placeholder="🔍 Cari nama dokter..." required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php foreach ($allDoctors as $doc): ?>
                                <option value="<?= $doc->id ?>" data-name="<?= esc($doc->name) ?>"><?= esc($doc->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Periode Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="period_start" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
                        </div>
                        <div class="col-6 form-group">
                            <label class="text-xs font-weight-bold text-dark">Periode Sampai <span class="text-danger">*</span></label>
                            <input type="date" name="period_end" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Total Jumlah Pasien Ditangani</label>
                        <input type="number" name="total_actions" id="modal-total-actions" class="form-control form-control-sm font-weight-bold" placeholder="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Nominal Total Jasa Medis (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_amount" id="modal-total-amount" class="form-control form-control-sm font-weight-bold text-success" placeholder="0" required>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Metode Pembayaran Kas/Bank <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control form-control-sm" required>
                            <option value="Transfer Bank">Transfer Bank (BCA / Mandiri / BRI / NTB Syariah)</option>
                            <option value="Kas Tunai">Kas Tunai Kasir Utama</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-dark">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Contoh: Honorarium tindakan dokter spesialis periode bulan ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Simpan & Buat Jurnal Akuntansi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.btn-bayar-dokter').click(function() {
            const docId = $(this).data('id');
            const count = $(this).data('count');
            const amount = $(this).data('amount');

            $('#modal-sel-doctor').val(docId).trigger('change');
            $('#modal-total-actions').val(count);
            $('#modal-total-amount').val(amount);

            $('#modalSettlement').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>
