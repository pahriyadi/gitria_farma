<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-none" style="border: 1px solid #b8b8b8 !important; background: #ffffff !important; border-radius: 4px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-chart-line text-teal mr-1"></i> MATRIKS KPI & PRODUKTIVITAS KINERJA PEGAWAI / DOKTER
                    </h5>
                    <small class="text-muted">Metrik performa bulanan: total pasien ditangani dokter, tindakan perawat, resep apoteker, dan tingkat presensi kehadiran</small>
                </div>
                <div>
                    <button type="button" class="btn btn-teal btn-sm font-weight-bold" onclick="window.print();">
                        <i class="fas fa-print mr-1"></i> Cetak Evaluasi KPI
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Filter Periode Bulan -->
                <form action="<?= base_url('hrd/kpi') ?>" method="get" class="mb-4 p-3 bg-light border rounded">
                    <div class="row align-items-end">
                        <div class="col-md-4 form-group mb-md-0">
                            <label class="text-xs font-weight-bold text-dark">Pilih Bulan & Tahun Evaluasi</label>
                            <input type="month" name="month" class="form-control form-control-sm font-weight-bold" value="<?= esc($selectedMonth) ?>" onchange="this.form.submit();">
                        </div>
                        <div class="col-md-6 form-group mb-md-0">
                            <span class="text-xs text-muted d-block">Periode Aktif:</span>
                            <strong class="text-teal" style="font-size: 14px;"><?= date('F Y', strtotime($selectedMonth . '-01')) ?></strong>
                        </div>
                        <div class="col-md-2 form-group mb-md-0 text-right">
                            <button type="submit" class="btn btn-teal btn-sm btn-block font-weight-bold">
                                <i class="fas fa-sync-alt mr-1"></i> Muat Data
                            </button>
                        </div>
                    </div>
                </form>

                <!-- 1. KPI DOKTER & TENAGA MEDIS -->
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-user-doctor text-teal mr-1"></i> 1. Kinerja Pelayanan Dokter Spesialis & Dokter Umum</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped table-hover mb-0" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th>NAMA DOKTER</th>
                                <th>POLIKLINIK / SPESIALISASI</th>
                                <th>TOTAL PASIEN DILAYANI</th>
                                <th>TOTAL E-RESEP DITERBITKAN</th>
                                <th>ESTIMASI FEE DOKTER</th>
                                <th style="width: 130px;">STATUS KPI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($doctorKpi as $dk): 
                                $ptCount = (int)$dk->total_patients;
                                $scoreBadge = 'success'; $scoreLabel = 'Sangat Baik (A)';
                                if ($ptCount == 0) { $scoreBadge = 'secondary'; $scoreLabel = 'Standby'; }
                                elseif ($ptCount < 10) { $scoreBadge = 'warning'; $scoreLabel = 'Cukup (C)'; }
                                elseif ($ptCount < 30) { $scoreBadge = 'primary'; $scoreLabel = 'Baik (B)'; }
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong class="text-dark"><?= esc($dk->name) ?></strong></td>
                                    <td><?= esc($dk->poly_name ?? 'Poli Umum') ?></td>
                                    <td class="text-center font-weight-bold text-teal" style="font-size: 14px;"><?= $ptCount ?> Pasien</td>
                                    <td class="text-center font-weight-bold"><?= (int)$dk->total_prescriptions ?> Resep</td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($dk->total_fee, 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $scoreBadge ?> px-2 py-1"><?= $scoreLabel ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 2. KPI STAF, PERAWAT & APOTEKER (PRESENSI & PRODUKTIVITAS) -->
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-user-nurse text-teal mr-1"></i> 2. Rekapitulasi Presensi & Disiplin Kerja Karyawan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th>NIK & NAMA PEGAWAI</th>
                                <th>POSISI / JABATAN</th>
                                <th>HADIR TEPAT WAKTU</th>
                                <th>TERLAMBAT</th>
                                <th>CUTI / IJIN</th>
                                <th>TOTAL JAM KERJA</th>
                                <th style="width: 130px;">TINGKAT KEHADIRAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($employeeKpi as $ek): 
                                $hadir = (int)$ek->hadir_count;
                                $rate = $hadir > 0 ? min(100, round(($hadir / 22) * 100)) : 0;
                                $rateBadge = ($rate >= 90) ? 'success' : (($rate >= 75) ? 'primary' : 'danger');
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <strong class="text-dark"><?= esc($ek->name) ?></strong>
                                        <small class="text-muted d-block">NIK: <?= esc($ek->nik) ?></small>
                                    </td>
                                    <td><?= esc($ek->position ?? 'Staf Operasional') ?></td>
                                    <td class="text-center font-weight-bold text-success"><?= $hadir ?> Hari</td>
                                    <td class="text-center text-warning font-weight-bold"><?= (int)$ek->late_count ?> Hari</td>
                                    <td class="text-center text-info"><?= (int)$ek->leave_count ?> Hari</td>
                                    <td class="text-center font-weight-bold"><?= number_format($ek->total_work_hours, 1) ?> Jam</td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $rateBadge ?> px-2 py-1"><?= $rate ?>%</span>
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
<?= $this->endSection() ?>
