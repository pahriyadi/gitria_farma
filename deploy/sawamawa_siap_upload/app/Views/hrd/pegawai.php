<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-users-gear text-teal mr-2"></i> HRD, Kepegawaian & Payroll Penggajian
                </h1>
                <small class="text-muted">Manajemen data staf/karyawan, integrasi komisi jasa medis dokter, pemrosesan payroll bulanan, dan cetak slip gaji</small>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-teal btn-sm font-weight-bold shadow-sm mr-1" data-toggle="modal" data-target="#modalAddEmployee">
                    <i class="fas fa-user-plus mr-1"></i> Tambah Pegawai Baru
                </button>
                <button class="btn btn-dark btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalGeneratePayroll">
                    <i class="fas fa-calculator mr-1"></i> Proses Payroll Bulanan
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
                                <span class="text-xs font-weight-bold text-teal text-uppercase">Total Pegawai Aktif</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= $totalEmployees ?> <small class="text-muted">Karyawan</small></h4>
                                <small class="text-muted">Medis & Non-Medis</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-teal">
                                <i class="fas fa-id-card-clip fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-info text-uppercase">Beban Gaji Pokok & Tunjangan</span>
                                <h5 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($totalMonthlyBaseSalary, 0, ',', '.') ?></h5>
                                <small class="text-muted">Estimasi pengeluaran rutin/bln</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-info">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-success text-uppercase">Akumulasi Jasa Medis Dokter</span>
                                <h5 class="font-weight-bold text-dark mb-0 mt-1">Rp <?= number_format($totalDoctorFees, 0, ',', '.') ?></h5>
                                <small class="text-muted">Dari tindakan medis terbayar</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-success">
                                <i class="fas fa-user-doctor fa-2x"></i>
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
                                <span class="text-xs font-weight-bold text-purple text-uppercase">Periode Payroll Tercatat</span>
                                <h4 class="font-weight-bold text-dark mb-0 mt-1"><?= count($payrolls) ?> <small class="text-muted">Batch</small></h4>
                                <small class="text-muted">Rekap penggajian bulanan</small>
                            </div>
                            <div class="bg-light p-3 rounded-circle text-purple">
                                <i class="fas fa-file-invoice-dollar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABS CARD -->
        <div class="card card-teal card-outline card-outline-tabs shadow-sm" style="border: 1px solid #b8b8b8;">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="hrd-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 px-4" id="tab-emp-link" data-toggle="pill" href="#tab-emp" role="tab">
                            <i class="fas fa-id-card text-teal mr-1"></i> 1. MASTER PEGAWAI & KARYAWAN
                            <span class="badge badge-teal ml-2"><?= count($employees) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-payroll-link" data-toggle="pill" href="#tab-payroll" role="tab">
                            <i class="fas fa-file-invoice-dollar text-info mr-1"></i> 2. PAYROLL & PENGGAJIAN BULANAN
                            <span class="badge badge-info ml-2"><?= count($payrolls) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 px-4" id="tab-doctor-fees-link" data-toggle="pill" href="#tab-doctor-fees" role="tab">
                            <i class="fas fa-user-doctor text-success mr-1"></i> 3. REKAP KOMISI & JASA MEDIS DOKTER
                            <span class="badge badge-success ml-2"><?= count($doctorAccruals) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body bg-white p-3">
                <div class="tab-content" id="hrd-tabsContent">

                    <!-- ========================================================================= -->
                    <!-- TAB 1: MASTER DATA PEGAWAI & KARYAWAN -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade show active" id="tab-emp" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Direktori Karyawan & Tenaga Kesehatan Klinik</h6>
                                <small class="text-muted">Data identitas pegawai, jabatan/departemen, gaji pokok, tunjangan, dan nomor rekening payroll</small>
                            </div>
                            <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalAddEmployee">
                                <i class="fas fa-plus mr-1"></i> Tambah Pegawai Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 120px;" class="text-center">NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Departemen & Posisi</th>
                                        <th>Tipe Kepegawaian</th>
                                        <th class="text-right">Gaji Pokok</th>
                                        <th class="text-right">Tunjangan</th>
                                        <th>Info Rekening Bank</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employees as $emp): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($emp->nip) ?></td>
                                            <td>
                                                <strong class="text-dark"><?= esc($emp->name) ?></strong>
                                                <?php if (!empty($emp->phone)): ?>
                                                    <small class="d-block text-muted"><i class="fas fa-phone fa-xs mr-1"></i> <?= esc($emp->phone) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border font-weight-bold"><?= esc($emp->department) ?></span>
                                                <small class="d-block text-muted font-weight-bold"><?= esc($emp->position ?: 'Staff') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= ($emp->employment_type ?? 'tetap') === 'tetap' ? 'teal' : 'secondary' ?> text-uppercase">
                                                    <?= esc($emp->employment_type ?? 'tetap') ?>
                                                </span>
                                            </td>
                                            <td class="text-right font-weight-bold text-dark">Rp <?= number_format($emp->salary, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-info">
                                                Rp <?= number_format(($emp->allowance_position ?? 0) + ($emp->allowance_transport ?? 0), 0, ',', '.') ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($emp->bank_name)): ?>
                                                    <small class="badge badge-light border font-weight-bold">
                                                        <?= esc($emp->bank_name) ?>: <?= esc($emp->bank_account) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted text-xs">Tunai / Belum Diisi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success text-uppercase px-2 py-1"><?= strtoupper(esc($emp->status)) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-outline-info btn-xs btn-edit-employee mr-1"
                                                        data-id="<?= $emp->id ?>"
                                                        data-nip="<?= esc($emp->nip) ?>"
                                                        data-nik="<?= esc($emp->nik_ktp ?? '') ?>"
                                                        data-name="<?= esc($emp->name) ?>"
                                                        data-dept="<?= esc($emp->department) ?>"
                                                        data-position="<?= esc($emp->position ?? '') ?>"
                                                        data-salary="<?= esc($emp->salary) ?>"
                                                        data-allowpos="<?= esc($emp->allowance_position ?? 0) ?>"
                                                        data-allowtrans="<?= esc($emp->allowance_transport ?? 0) ?>"
                                                        data-deductbpjs="<?= esc($emp->deduction_bpjs ?? 0) ?>"
                                                        data-phone="<?= esc($emp->phone ?? '') ?>"
                                                        data-email="<?= esc($emp->email ?? '') ?>"
                                                        data-address="<?= esc($emp->address ?? '') ?>"
                                                        data-joindate="<?= esc($emp->join_date ?? '') ?>"
                                                        data-bank="<?= esc($emp->bank_name ?? '') ?>"
                                                        data-acc="<?= esc($emp->bank_account ?? '') ?>"
                                                        data-type="<?= esc($emp->employment_type ?? 'tetap') ?>"
                                                        title="Edit Data Pegawai">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="<?= base_url('hrd/pegawai') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan status pegawai ini?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="action" value="delete_employee">
                                                    <input type="hidden" name="id" value="<?= $emp->id ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Nonaktifkan"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 2: PAYROLL & PENGGAJIAN BULANAN -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-payroll" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Daftar Batch Payroll Penggajian Bulanan</h6>
                                <small class="text-muted">Rekapitulasi penggajian seluruh staf dan dokter terintegrasi dengan slip gaji dan jurnal akuntansi</small>
                            </div>
                            <button class="btn btn-teal btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalGeneratePayroll">
                                <i class="fas fa-calculator mr-1"></i> Proses Payroll Baru
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;" class="text-center">Kode Payroll</th>
                                        <th>Periode Bulan/Tahun</th>
                                        <th>Tanggal Bayar</th>
                                        <th class="text-center">Jumlah Pegawai</th>
                                        <th class="text-right">Total Kotor (Gross)</th>
                                        <th class="text-right">Total Bersih (THP)</th>
                                        <th class="text-center">Status</th>
                                        <th style="width: 200px;" class="text-center">Aksi Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payrolls as $pay): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-teal"><?= esc($pay->payroll_code) ?></td>
                                            <td>
                                                <strong>Bulan <?= date('F', mktime(0, 0, 0, $pay->period_month, 10)) ?> <?= esc($pay->period_year) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($pay->payment_date)) ?></td>
                                            <td class="text-center font-weight-bold"><?= esc($pay->total_employees) ?> Orang</td>
                                            <td class="text-right">Rp <?= number_format($pay->total_gross, 0, ',', '.') ?></td>
                                            <td class="text-right font-weight-bold text-teal">Rp <?= number_format($pay->total_net_salary, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= $pay->status === 'paid' ? 'success' : 'primary' ?> text-uppercase px-2 py-1">
                                                    <?= strtoupper(esc($pay->status)) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('hrd/cetak-rekap-payroll/' . $pay->id) ?>" target="_blank" class="btn btn-outline-dark btn-xs font-weight-bold mr-1" title="Cetak Rekapitulasi Payroll">
                                                    <i class="fas fa-print"></i> Rekap
                                                </a>
                                                <?php if ($pay->status !== 'paid'): ?>
                                                    <form action="<?= base_url('hrd/bayar-payroll') ?>" method="post" class="d-inline" onsubmit="return confirm('Konfirmasi pencairan pembayaran gaji dan pembukuan jurnal beban gaji otomatis?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="payroll_id" value="<?= $pay->id ?>">
                                                        <button type="submit" class="btn btn-success btn-xs font-weight-bold">
                                                            <i class="fas fa-money-check-dollar"></i> Bayar
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge badge-success"><i class="fas fa-check-double"></i> Terbayar</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- TAB 3: REKAP KOMISI & JASA MEDIS DOKTER -->
                    <!-- ========================================================================= -->
                    <div class="tab-pane fade" id="tab-doctor-fees" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Akumulasi Komisi & Jasa Medis Dokter Pemeriksa</h6>
                                <small class="text-muted">Perhitungan otomatis persentase komisi tindakan, konsultasi, dan resep pasien yang telah dibayar kasir</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover datatable" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">NO</th>
                                        <th>Nama Dokter Spesialis / Dokter Umum</th>
                                        <th class="text-right">Total Akumulasi Komisi Medis (Rp)</th>
                                        <th>Keterangan Integrasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($doctorAccruals as $docName => $info): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted"><?= $no++ ?></td>
                                            <td>
                                                <strong class="text-dark"><i class="fas fa-user-md text-teal mr-1"></i> <?= esc($docName) ?></strong>
                                            </td>
                                            <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                                                Rp <?= number_format($info['fees'], 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><i class="fas fa-link text-info mr-1"></i> Otomatis ditarik saat tombol <strong>Proses Payroll Bulanan</strong> dijalankan.</small>
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
<!-- MODAL: TAMBAH / EDIT PEGAWAI -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAddEmployee" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark" id="titleEmployeeModal">
                    <i class="fas fa-user-plus mr-1 text-teal"></i> Daftarkan Pegawai / Tenaga Medis Baru
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/pegawai') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" id="emp-action" value="add_employee">
                <input type="hidden" name="id" id="emp-id" value="">
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">NIP Pegawai:</label>
                            <input type="text" name="nip" id="emp-nip" class="form-control form-control-sm font-weight-bold" placeholder="Auto / EMP-2026-001">
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">NIK KTP:</label>
                            <input type="text" name="nik_ktp" id="emp-nik" class="form-control form-control-sm" placeholder="5204019901880001">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Lengkap & Gelar: <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="emp-name" class="form-control form-control-sm font-weight-bold" placeholder="dr. Ahmad Fauzi, Sp.A / Ns. Nurhayati" required>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Departemen / Divisi: <span class="text-danger">*</span></label>
                            <select name="department" id="emp-dept" class="form-control form-control-sm font-weight-bold" required>
                                <option value="Klinik">Pelayanan Medis & Poliklinik</option>
                                <option value="Apotek">Apotek & Farmasi</option>
                                <option value="Laboratorium">Laboratorium Medis</option>
                                <option value="Restoran">POS Resto & Kuliner Sehat</option>
                                <option value="Keuangan">Keuangan, Kasir & Akuntansi</option>
                                <option value="Sarpras">Umum, Logistik & IT</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Jabatan / Posisi:</label>
                            <input type="text" name="position" id="emp-position" class="form-control form-control-sm" placeholder="Dokter Spesialis / Kepala Apoteker / Kasir">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Status Kepegawaian:</label>
                            <select name="employment_type" id="emp-type" class="form-control form-control-sm">
                                <option value="tetap">Pegawai Tetap</option>
                                <option value="kontrak">Pegawai Kontrak</option>
                                <option value="mitra">Mitra / Dokter Jaga</option>
                                <option value="magang">Magang / Praktikan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tanggal Bergabung:</label>
                            <input type="date" name="join_date" id="emp-joindate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-teal mb-2 mt-2"><i class="fas fa-coins mr-1"></i> Komponen Gaji & Tunjangan:</h6>
                    <div class="row">
                        <div class="col-md-3 form-group mb-2">
                            <label class="text-xs font-weight-bold">Gaji Pokok (Rp): <span class="text-danger">*</span></label>
                            <input type="number" name="salary" id="emp-salary" class="form-control form-control-sm font-weight-bold text-teal" placeholder="4500000" required min="0">
                        </div>
                        <div class="col-md-3 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tunjangan Jabatan (Rp):</label>
                            <input type="number" name="allowance_position" id="emp-allowpos" class="form-control form-control-sm" placeholder="500000" value="0">
                        </div>
                        <div class="col-md-3 form-group mb-2">
                            <label class="text-xs font-weight-bold">Tunjangan Makan/Trans (Rp):</label>
                            <input type="number" name="allowance_transport" id="emp-allowtrans" class="form-control form-control-sm" placeholder="400000" value="0">
                        </div>
                        <div class="col-md-3 form-group mb-2">
                            <label class="text-xs font-weight-bold">Potongan BPJS (Rp):</label>
                            <input type="number" name="deduction_bpjs" id="emp-deductbpjs" class="form-control form-control-sm" placeholder="150000" value="0">
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-secondary mb-2 mt-2"><i class="fas fa-address-book mr-1"></i> Kontak & Rekening Bank:</h6>
                    <div class="row">
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nomor WhatsApp / HP:</label>
                            <input type="text" name="phone" id="emp-phone" class="form-control form-control-sm" placeholder="0812-3456-7890">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nama Bank Payroll:</label>
                            <input type="text" name="bank_name" id="emp-bank" class="form-control form-control-sm" placeholder="Bank BCA / Mandiri">
                        </div>
                        <div class="col-md-4 form-group mb-2">
                            <label class="text-xs font-weight-bold">Nomor Rekening:</label>
                            <input type="text" name="bank_account" id="emp-acc" class="form-control form-control-sm" placeholder="8820-192-334">
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Data Pegawai</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: PROSES PAYROLL BULANAN -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalGeneratePayroll" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom py-2">
                <h6 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-calculator mr-1 text-teal"></i> Proses Payroll Penggajian Bulanan
                </h6>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('hrd/generate-payroll') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Periode Bulan: <span class="text-danger">*</span></label>
                            <select name="period_month" class="form-control form-control-sm font-weight-bold" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-2">
                            <label class="text-xs font-weight-bold">Periode Tahun: <span class="text-danger">*</span></label>
                            <input type="number" name="period_year" class="form-control form-control-sm font-weight-bold" value="<?= date('Y') ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Tanggal Pembayaran Gaji: <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control form-control-sm font-weight-bold" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs font-weight-bold">Keterangan / Catatan Penggajian:</label>
                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Penggajian rutin bulan berjalan">
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 12px;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Proses ini akan <strong>mengalkulasi gaji pokok, tunjangan, dan akumulasi jasa medis dokter</strong> untuk seluruh pegawai aktif, serta membuat slip gaji individual yang siap dicetak.
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm font-weight-bold"><i class="fas fa-play mr-1"></i> Generate Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit Employee Modal prefill
        $('.btn-edit-employee').click(function() {
            $('#titleEmployeeModal').html('<i class="fas fa-edit mr-1"></i> Edit Data Pegawai');
            $('#emp-action').val('edit_employee');
            $('#emp-id').val($(this).data('id'));
            $('#emp-nip').val($(this).data('nip'));
            $('#emp-nik').val($(this).data('nik'));
            $('#emp-name').val($(this).data('name'));
            $('#emp-dept').val($(this).data('dept'));
            $('#emp-position').val($(this).data('position'));
            $('#emp-type').val($(this).data('type'));
            $('#emp-joindate').val($(this).data('joindate'));
            $('#emp-salary').val($(this).data('salary'));
            $('#emp-allowpos').val($(this).data('allowpos'));
            $('#emp-allowtrans').val($(this).data('allowtrans'));
            $('#emp-deductbpjs').val($(this).data('deductbpjs'));
            $('#emp-phone').val($(this).data('phone'));
            $('#emp-email').val($(this).data('email'));
            $('#emp-address').val($(this).data('address'));
            $('#emp-bank').val($(this).data('bank'));
            $('#emp-acc').val($(this).data('acc'));
            $('#modalAddEmployee').modal('show');
        });

        // Reset Add Modal
        $('[data-target="#modalAddEmployee"]').click(function() {
            if (!$('#emp-id').val()) {
                $('#titleEmployeeModal').html('<i class="fas fa-user-plus mr-1"></i> Daftarkan Pegawai / Tenaga Medis Baru');
                $('#emp-action').val('add_employee');
                $('#emp-id').val('');
            }
        });
    });
</script>
<?= $this->endSection() ?>
