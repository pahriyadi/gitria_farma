<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- ========================================================================= -->
<!-- HEADER & BACK NAVIGATION                                                  -->
<!-- ========================================================================= -->
<div class="mb-3 d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
    <div>
        <a href="<?= base_url('accounting/aturan-jurnal') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold shadow-xs">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Template Jurnal
        </a>
    </div>
    <div class="d-flex align-items-center">
        <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddRule">
            <i class="fas fa-plus mr-1"></i> + Tambah Sub-Pos Akun Baru
        </button>
    </div>
</div>

<!-- ========================================================================= -->
<!-- CATEGORY OVERVIEW HEADER CARD                                             -->
<!-- ========================================================================= -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 6px;">
                    <?php
                    $modBadge = 'badge-secondary';
                    $modIcon = 'fa-tag';
                    if ($category->module === 'apotek') { $modBadge = 'badge-teal'; $modIcon = 'fa-prescription-bottle-medical'; }
                    elseif ($category->module === 'keuangan') { $modBadge = 'badge-success'; $modIcon = 'fa-cash-register'; }
                    elseif ($category->module === 'resto') { $modBadge = 'badge-warning text-dark'; $modIcon = 'fa-utensils'; }
                    elseif ($category->module === 'klinik') { $modBadge = 'badge-primary'; $modIcon = 'fa-hospital-user'; }
                    ?>
                    <span class="badge <?= $modBadge ?> text-uppercase font-weight-bold px-2 py-1">
                        <i class="fas <?= $modIcon ?> mr-1"></i> <?= esc($category->module) ?>
                    </span>
                    <span class="badge badge-light border font-monospace text-muted">
                        <code><?= esc($category->category_code) ?></code>
                    </span>
                    <?php if ($category->is_active): ?>
                        <span class="badge badge-success px-2 py-1 font-weight-bold">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1 font-weight-bold">Non-Aktif</span>
                    <?php endif; ?>
                </div>
                <h3 class="font-weight-bold text-dark mb-1" style="font-size: 1.35rem;"><?= esc($category->category_name) ?></h3>
                <p class="text-muted text-sm mb-0">
                    <?= esc($category->description ?: 'Aturan formula pembagian jurnal otomatis multi-akun per transaksi.') ?>
                </p>
            </div>
            <div class="col-lg-4 text-lg-right">
                <div class="p-3 bg-light rounded border text-center text-lg-right d-inline-block w-100 w-lg-auto" style="min-width: 240px;">
                    <?php if (!empty($isFixedAmount)): ?>
                        <div class="d-flex justify-content-between text-sm mb-1">
                            <span class="text-muted font-weight-bold">Total Debet:</span>
                            <span class="font-weight-bold text-primary">Rp <?= number_format($debitFixedSum, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-sm mb-2">
                            <span class="text-muted font-weight-bold">Total Kredit:</span>
                            <span class="font-weight-bold text-info">Rp <?= number_format($creditFixedSum, 0, ',', '.') ?></span>
                        </div>
                    <?php else: ?>
                        <div class="d-flex justify-content-between text-sm mb-1">
                            <span class="text-muted font-weight-bold">Total Debet:</span>
                            <span class="font-weight-bold text-primary"><?= number_format($debitPct, 2) ?>%</span>
                        </div>
                        <div class="d-flex justify-content-between text-sm mb-2">
                            <span class="text-muted font-weight-bold">Total Kredit:</span>
                            <span class="font-weight-bold text-info"><?= number_format($creditPct, 2) ?>%</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($isBalanced): ?>
                        <div class="badge badge-success px-3 py-2 w-100 font-weight-bold text-sm shadow-xs">
                            <i class="fas fa-check-circle mr-1"></i> 100% DOUBLE-ENTRY BALANCE
                        </div>
                    <?php else: ?>
                        <div class="badge badge-warning text-dark px-3 py-2 w-100 font-weight-bold text-sm shadow-xs">
                            <i class="fas fa-triangle-exclamation mr-1"></i> BELUM BALANCE
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ===================================================================== -->
    <!-- LEFT COLUMN: SUB-RULES TABLE MANAGER                                  -->
    <!-- ===================================================================== -->
    <div class="col-lg-8">
        <div class="card card-outline card-teal shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <h5 class="card-title font-weight-bold text-dark mb-0">
                            <i class="fas fa-list-ol text-teal mr-1"></i> Sub-Pos Akun &amp; Formula Alokasi Jurnal
                        </h5>
                        <small class="text-muted d-block mt-0.5">Daftar rekening COA target penerimaan &amp; pembagian nilai transaksi</small>
                    </div>
                    <div>
                        <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddRule">
                            <i class="fas fa-plus mr-1"></i> + Tambah Sub-Pos
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered datatable w-100 mb-0" style="font-size: 12px;">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 45px;" class="text-center">URUT</th>
                                <th style="width: 80px;" class="text-center">POSISI</th>
                                <th style="min-width: 160px;">NAMA SUB-POS ITEM</th>
                                <th style="min-width: 180px;">REKENING COA TARGET</th>
                                <th style="width: 130px;" class="text-center">ALOKASI / NILAI</th>
                                <th style="width: 75px;" class="text-center">STATUS</th>
                                <th style="width: 90px;" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rules)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 text-secondary"></i><br>
                                        Belum ada sub-pos akun. Klik tombol "+ Tambah Sub-Pos" untuk menambahkan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rules as $r): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold text-muted"><?= $r->sort_order ?></td>
                                        <td class="text-center">
                                            <?php if ($r->position === 'debit'): ?>
                                                <span class="badge badge-primary px-2 py-1 font-weight-bold">DEBET</span>
                                            <?php else: ?>
                                                <span class="badge badge-info px-2 py-1 font-weight-bold">KREDIT</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="text-dark"><?= esc($r->item_name) ?></strong>
                                            <?php if (!empty($r->formula_code)): ?>
                                                <small class="d-block text-muted font-monospace"><code>[<?= esc($r->formula_code) ?>]</code></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($r->account_code): ?>
                                                <span class="badge badge-light border font-weight-bold text-teal"><?= esc($r->account_code) ?></span>
                                                <span class="font-weight-bold text-secondary"><?= esc($r->account_name) ?></span>
                                            <?php else: ?>
                                                <span class="text-danger font-italic">Akun COA Belum Terhubung</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $pctVal = (float)$r->percentage_value;
                                            $formattedPct = (floor($pctVal) == $pctVal) ? (int)$pctVal . '%' : number_format($pctVal, 2, '.', '') . '%';
                                            ?>
                                            <?php if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT'): ?>
                                                <span class="badge badge-warning text-dark font-weight-bold py-1 px-2">
                                                    Fee Dokter (<?= $formattedPct ?>)
                                                </span>
                                            <?php elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0): ?>
                                                <span class="badge badge-secondary font-weight-bold py-1 px-2">
                                                    Rp <?= number_format($r->fixed_amount_value, 0, ',', '.') ?>
                                                </span>
                                            <?php elseif ($pctVal > 0): ?>
                                                <span class="badge badge-teal font-weight-bold py-1 px-2" style="font-size: 12px;">
                                                    <?= $formattedPct ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-light border text-muted">0%</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($r->is_active): ?>
                                                <span class="badge badge-success px-2 py-1 font-weight-bold">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1 font-weight-bold">Non-Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-secondary btn-xs shadow-xs" onclick="editRule(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Sub-Pos">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-xs shadow-xs" onclick="deleteRule(<?= $r->id ?>, '<?= esc(addslashes($r->item_name)) ?>')" title="Hapus Sub-Pos">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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

    <!-- ===================================================================== -->
    <!-- RIGHT COLUMN: INTERACTIVE REAL-TIME SPLIT SIMULATOR                  -->
    <!-- ===================================================================== -->
    <div class="col-lg-4">
        <div class="card card-outline card-teal shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-calculator text-teal mr-1"></i> Simulator Kalkulasi Bagi Hasil
                </h6>
            </div>
            <div class="card-body p-3">
                <?php
                $isClinicCategory = ($category->category_code === 'RAWAT_JALAN_POLI' || $category->module === 'klinik');
                $isOnlineCategory = ($category->category_code === 'KONSULTASI_ONLINE');
                $defaultSimAmt = $isClinicCategory ? 150000 : ($isOnlineCategory ? 200000 : 85000);
                $defaultDocFee = $isClinicCategory ? 66.67 : 5;
                $defaultDocFeeNom = $isOnlineCategory ? 80000 : 100000;
                ?>
                <?php if ($isOnlineCategory): ?>
                    <div class="alert alert-info py-2 px-2 mb-2 text-xs font-weight-bold shadow-xs border-0" style="border-left: 3px solid #17a2b8 !important; line-height: 1.4;">
                        <i class="fas fa-info-circle text-info mr-1"></i> Formula Konsultasi Online:<br>
                        <span class="font-weight-normal text-dark">Jasa Dokter <strong>Rp 20.000 (Tetap)</strong> + Utang Fee Dokter (Diisi Sendiri) sebagai pengurang Kas. Sisa kas dikalikan 5 pos (Obat, Pajak, Penunjang, Resep, ADM).</span>
                    </div>
                <?php endif; ?>
                <div class="form-group mb-2">
                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Nominal Transaksi Simulasi (Rp)</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light font-weight-bold">Rp</span>
                        </div>
                        <input type="number" id="sim_amount" class="form-control font-weight-bold text-dark" value="<?= $defaultSimAmt ?>" min="0" step="any">
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Mode Parameter Fee Dokter</label>
                    <div class="d-flex mb-1" style="gap: 12px;">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="sim_type_pct" name="sim_fee_type" value="pct" class="custom-control-input" <?= $isOnlineCategory ? '' : 'checked' ?> onchange="toggleSimFeeType()">
                            <label class="custom-control-label text-xs font-weight-bold" for="sim_type_pct">Persentase (%)</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="sim_type_nom" name="sim_fee_type" value="nominal" class="custom-control-input" <?= $isOnlineCategory ? 'checked' : '' ?> onchange="toggleSimFeeType()">
                            <label class="custom-control-label text-xs font-weight-bold text-teal" for="sim_type_nom"><?= $isOnlineCategory ? 'Utang Fee Dokter (Rp)' : 'Nominal Tetap (Rp)' ?></label>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-2" id="grp_sim_pct" style="<?= $isOnlineCategory ? 'display:none;' : '' ?>">
                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1">Fee Jasa Medis (%)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" id="sim_doc_fee" class="form-control font-weight-bold text-dark" value="<?= $defaultDocFee ?>" min="0" max="100" step="any">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light font-weight-bold">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-2" id="grp_sim_nom" style="<?= $isOnlineCategory ? '' : 'display:none;' ?>">
                    <label class="text-xs font-weight-bold text-muted text-uppercase mb-1"><?= $isOnlineCategory ? 'Utang Fee Dokter Manual (Rp)' : 'Fee Jasa Medis Tetap (Rp)' ?></label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light font-weight-bold">Rp</span>
                        </div>
                        <input type="number" id="sim_doc_fee_nom" class="form-control font-weight-bold text-teal" value="<?= $defaultDocFeeNom ?>" min="0" step="any">
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="mb-3 d-flex flex-wrap" style="gap: 4px;">
                    <?php if ($isClinicCategory): ?>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(150000, 100000, 'nominal')">Rp 150rb (Dr. Rp 100rb Baku)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(150000, 66.67, 'pct')">Rp 150rb (66.67%)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(200000, 100000, 'nominal')">Rp 200rb (Dr. Rp 100rb)</button>
                    <?php elseif ($isOnlineCategory): ?>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(200000, 80000, 'nominal')">Rp 200rb (Fee Dr Rp 80rb)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(150000, 50000, 'nominal')">Rp 150rb (Fee Dr Rp 50rb)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(100000, 30000, 'nominal')">Rp 100rb (Fee Dr Rp 30rb)</button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(85000, 5, 'pct')">Rp 85.000 (5%)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(100000, 5, 'pct')">Rp 100.000 (5%)</button>
                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold mb-1" onclick="setSimPreset(250000, 5, 'pct')">Rp 250.000 (5%)</button>
                    <?php endif; ?>
                </div>

                <!-- Live Simulation Output Box -->
                <div class="border rounded bg-light p-2 mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                        <span class="text-xs font-weight-bold text-muted text-uppercase">Hasil Simulasi Double-Entry:</span>
                        <span id="sim_balance_badge" class="badge badge-success text-xs font-weight-bold">Balance 100%</span>
                    </div>
                    <div id="sim_results_box" style="max-height: 280px; overflow-y: auto; font-size: 11px;">
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Menghitung simulasi jurnal...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH / EDIT SUB-POS AKUN                                          -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddRule" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-teal text-white py-3">
                <h5 class="modal-title font-weight-bold" id="ruleModalTitle">
                    <i class="fas fa-plus mr-1"></i> Tambah Sub-Pos Akun
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('accounting/aturan-jurnal/save-rule') ?>" method="post" id="formRule" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="rule_id" value="">
                <input type="hidden" name="category_id" value="<?= $category->id ?>">
                
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Posisi Jurnal <span class="text-danger">*</span></label>
                        <div class="d-flex" style="gap: 16px;">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pos_debit" name="position" value="debit" class="custom-control-input">
                                <label class="custom-control-label font-weight-bold text-primary" for="pos_debit">DEBET (Penerimaan Kas/Bank)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pos_credit" name="position" value="credit" class="custom-control-input" checked>
                                <label class="custom-control-label font-weight-bold text-info" for="pos_credit">KREDIT (Alokasi Bagi Hasil / Pendapatan / Utang)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Nama Sub-Pos Item <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" id="rule_item_name" class="form-control font-weight-bold" placeholder="Contoh: OBAT (PEMBELIAN LAGI), UTANG PAJAK, DOKTER, ADM" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark text-sm">Pilih Akun Rekening COA <span class="text-danger">*</span></label>
                        <select name="account_id" id="rule_account_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Pilih Akun COA --</option>
                            <?php foreach ($accounts as $acc): ?>
                                <option value="<?= $acc->id ?>">[<?= esc($acc->code) ?>] <?= esc($acc->name) ?> (<?= ucfirst($acc->type) ?> - <?= ucfirst($acc->normal_balance) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark text-sm">Tipe Kalkulasi <span class="text-danger">*</span></label>
                            <select name="calc_type" id="rule_calc_type" class="form-control font-weight-bold" onchange="toggleCalcType()">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed_amount">Nominal Tetap (Rp)</option>
                                <option value="dynamic_fee">Fee Dokter Dinamis (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold text-dark text-sm" id="lbl_calc_val">Persentase Nilai (%) <span class="text-danger">*</span></label>
                            <div class="input-group" id="grp_pct_input">
                                <input type="number" step="any" min="0" max="100" name="percentage_value" id="rule_percentage_value" class="form-control font-weight-bold" placeholder="Misal: 66.67" value="0.00">
                                <div class="input-group-append" id="grp_append_symbol">
                                    <span class="input-group-text font-weight-bold">%</span>
                                </div>
                            </div>
                            <div class="input-group" id="grp_fixed_input" style="display:none;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" step="any" min="0" name="fixed_amount_value" id="rule_fixed_amount_value" class="form-control font-weight-bold" placeholder="Nominal Rp (misal: 10000)" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="font-weight-bold text-dark text-sm">Urutan Tampil (Sort Order)</label>
                            <input type="number" name="sort_order" id="rule_sort_order" class="form-control font-weight-bold" value="1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold text-dark text-sm">Formula Khusus (Opsional)</label>
                            <select name="formula_code" id="rule_formula_code" class="form-control font-weight-bold">
                                <option value="">Tanpa Formula Khusus</option>
                                <option value="DEFAULT_DEBIT">DEFAULT_DEBIT (Penerimaan Utama Kas/Bank)</option>
                                <option value="DOCTOR_FEE_PCT">DOCTOR_FEE_PCT (Fee Jasa Medis Dokter)</option>
                                <option value="EMPLOYEE_FEE">EMPLOYEE_FEE (Fee Jasa Karyawan)</option>
                                <option value="REMAINDER_TIER">REMAINDER_TIER (Alokasi Sisa Fasilitas / BMHP / Adm)</option>
                                <option value="DYNAMIC_OBAT_REMAINDER">DYNAMIC_OBAT_REMAINDER (Penyeimbang Selisih Obat/Resep)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="rule_is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold text-dark" for="rule_is_active">Aktifkan Sub-Pos Ini</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" id="btn-save-rule" class="btn btn-teal btn-sm font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Sub-Pos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus Sub-Rule -->
<form id="formDeleteRule" action="" method="post" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
function toggleCalcType() {
    const type = $('#rule_calc_type').val();
    if (type === 'fixed_amount') {
        $('#lbl_calc_val').text('Nominal Tetap (Rp)');
        $('#grp_pct_input').hide();
        $('#grp_fixed_input').show();
        if (!$('#rule_fixed_amount_value').val()) {
            $('#rule_fixed_amount_value').val('0');
        }
    } else {
        $('#lbl_calc_val').text(type === 'dynamic_fee' ? 'Fee Dokter Default (%)' : 'Persentase Nilai (%)');
        $('#grp_pct_input').show();
        $('#grp_fixed_input').hide();
    }
}

function editRule(r) {
    $('#ruleModalTitle').html('<i class="fas fa-edit mr-1"></i> Edit Sub-Pos Akun');
    $('#rule_id').val(r.id);
    if (r.position === 'debit') {
        $('#pos_debit').prop('checked', true);
    } else {
        $('#pos_credit').prop('checked', true);
    }
    $('#rule_item_name').val(r.item_name);
    $('#rule_account_id').val(r.account_id).trigger('change');
    $('#rule_calc_type').val(r.calc_type);
    $('#rule_percentage_value').val(r.percentage_value);
    $('#rule_fixed_amount_value').val(r.fixed_amount_value);
    $('#rule_sort_order').val(r.sort_order);
    $('#rule_formula_code').val(r.formula_code || '');
    $('#rule_is_active').prop('checked', parseInt(r.is_active) === 1);
    toggleCalcType();
    $('#modalAddRule').modal('show');
}

$('#modalAddRule').on('hidden.bs.modal', function () {
    $('#ruleModalTitle').html('<i class="fas fa-plus mr-1"></i> Tambah Sub-Pos Akun');
    $('#formRule')[0].reset();
    $('#rule_id').val('');
    $('#pos_credit').prop('checked', true);
    $('#rule_account_id').val('').trigger('change');
    $('#rule_is_active').prop('checked', true);
    toggleCalcType();
});

// Explicit validation & anti-double-submit
$('#formRule').on('submit', function(e) {
    const itemName = $.trim($('#rule_item_name').val());
    const accId = $('#rule_account_id').val();

    if (!itemName) {
        e.preventDefault();
        alert('Silakan masukkan nama sub-pos akun.');
        $('#rule_item_name').focus();
        return false;
    }

    if (!accId) {
        e.preventDefault();
        alert('Silakan pilih Rekening Akun COA terlebih dahulu.');
        $('#rule_account_id').focus();
        return false;
    }

    const calcType = $('#rule_calc_type').val();
    if (calcType === 'fixed_amount') {
        const fixVal = parseFloat($('#rule_fixed_amount_value').val());
        if (isNaN(fixVal) || fixVal < 0) {
            e.preventDefault();
            alert('Silakan masukkan nominal Rupiah yang valid.');
            $('#rule_fixed_amount_value').focus();
            return false;
        }
        $('#rule_percentage_value').val('0');
    } else {
        const pctVal = parseFloat($('#rule_percentage_value').val());
        if (isNaN(pctVal) || pctVal < 0) {
            e.preventDefault();
            alert('Silakan masukkan persentase (%) yang valid.');
            $('#rule_percentage_value').focus();
            return false;
        }
        $('#rule_fixed_amount_value').val('0');
    }

    $('#btn-save-rule').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
    return true;
});

function deleteRule(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Sub-Pos Akun?',
            text: 'Anda yakin ingin menghapus sub-pos "' + name + '" dari formula ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formDeleteRule');
                form.action = '<?= base_url('accounting/aturan-jurnal/delete-rule') ?>/' + id;
                form.submit();
            }
        });
    } else {
        if (confirm('Anda yakin ingin menghapus sub-pos "' + name + '" dari formula ini?')) {
            const form = document.getElementById('formDeleteRule');
            form.action = '<?= base_url('accounting/aturan-jurnal/delete-rule') ?>/' + id;
            form.submit();
        }
    }
}

function toggleSimFeeType() {
    const isNom = $('#sim_type_nom').is(':checked');
    if (isNom) {
        $('#grp_sim_pct').hide();
        $('#grp_sim_nom').show();
    } else {
        $('#grp_sim_pct').show();
        $('#grp_sim_nom').hide();
    }
    runSimulation();
}

function setSimPreset(amt, fee, type) {
    $('#sim_amount').val(amt);
    if (type === 'nominal') {
        $('#sim_type_nom').prop('checked', true);
        $('#sim_doc_fee_nom').val(fee);
    } else {
        $('#sim_type_pct').prop('checked', true);
        if (fee !== undefined) {
            $('#sim_doc_fee').val(fee);
        }
    }
    toggleSimFeeType();
}

let simDebounceTimer = null;
function runSimulation() {
    clearTimeout(simDebounceTimer);
    simDebounceTimer = setTimeout(function() {
        const amount = $('#sim_amount').val() || 150000;
        const isNom = $('#sim_type_nom').is(':checked');
        const reqData = { amount: amount };

        if (isNom) {
            reqData.doctor_fee_nominal = $('#sim_doc_fee_nom').val() !== '' ? $('#sim_doc_fee_nom').val() : 0;
        } else {
            reqData.doctor_fee_pct = $('#sim_doc_fee').val() || 66.67;
        }

        $.ajax({
            url: '<?= base_url('accounting/aturan-jurnal/api-simulate/' . $category->id) ?>',
            type: 'GET',
            data: reqData,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    let html = '';
                    
                    // Debits
                    html += '<div class="font-weight-bold text-primary mb-1"><i class="fas fa-arrow-down mr-1"></i> SISI DEBET (Kas Masuk):</div>';
                    html += '<table class="table table-sm table-bordered mb-2 bg-white" style="font-size: 11px;">';
                    res.debits.forEach(d => {
                        html += `<tr>
                            <td><strong>[${d.account_code}]</strong> ${d.item_name}</td>
                            <td class="text-right font-weight-bold text-primary">${d.amount_fmt}</td>
                        </tr>`;
                    });
                    html += `<tr class="bg-light font-weight-bold">
                        <td>Total Debet</td>
                        <td class="text-right text-primary">Rp ${new Intl.NumberFormat('id-ID').format(res.total_debit)}</td>
                    </tr></table>`;

                    // Credits
                    html += '<div class="font-weight-bold text-info mb-1"><i class="fas fa-arrow-up mr-1"></i> SISI KREDIT (Alokasi Bagi Hasil):</div>';
                    html += '<table class="table table-sm table-bordered mb-0 bg-white" style="font-size: 11px;">';
                    res.credits.forEach(c => {
                        let pctNum = parseFloat(c.pct);
                        let pctDisplay = (!isNaN(pctNum)) ? (pctNum % 1 === 0 ? pctNum + '%' : pctNum.toFixed(2) + '%') : (c.pct + '%');
                        html += `<tr>
                            <td><strong>[${c.account_code}]</strong> ${c.item_name} <span class="text-muted text-xs">(${pctDisplay})</span></td>
                            <td class="text-right font-weight-bold text-info">${c.amount_fmt}</td>
                        </tr>`;
                    });
                    html += `<tr class="bg-light font-weight-bold">
                        <td>Total Kredit</td>
                        <td class="text-right text-info">Rp ${new Intl.NumberFormat('id-ID').format(res.total_credit)}</td>
                    </tr></table>`;

                    $('#sim_results_box').html(html);

                    if (res.is_balanced) {
                        $('#sim_balance_badge').removeClass('badge-warning').addClass('badge-success').html('<i class="fas fa-check"></i> 100% Balance');
                    } else {
                        $('#sim_balance_badge').removeClass('badge-success').addClass('badge-warning').html('<i class="fas fa-exclamation-triangle"></i> Selisih Rp ' + Math.abs(res.total_debit - res.total_credit));
                    }
                }
            },
            error: function() {
                $('#sim_results_box').html('<div class="text-danger text-center py-2">Gagal memuat simulasi.</div>');
            }
        });
    }, 150);
}

$(document).ready(function() {
    runSimulation();
    $('#sim_amount, #sim_doc_fee, #sim_doc_fee_nom').on('input change', function() {
        runSimulation();
    });
});
</script>

<style>
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.table td, .table th {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>
