<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-none" style="border: 1px solid #b8b8b8 !important; background: #ffffff !important; border-radius: 4px;">
            <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fab fa-whatsapp text-success mr-1"></i> OTOMASI WHATSAPP GATEWAY & NOTIFIKASI PASIEN REAL-TIME
                    </h5>
                    <small class="text-muted">Pusat kontrol integrasi WhatsApp Gateway: e-Ticket Antrean Online, Notifikasi Obat Siap Ambil, Pengingat Kontrol H-1, & Slip Gaji Karyawan</small>
                </div>
                <div>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-none" data-toggle="modal" data-target="#modalTestWa">
                        <i class="fab fa-whatsapp mr-1"></i> Uji Coba Kirim Pesan WA
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Status Koneksi Gateway -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #10b981 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">STATUS GATEWAY</small>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge badge-success px-2 py-1 mr-2"><i class="fas fa-circle text-xs mr-1"></i> CONNECTED</span>
                                <small class="text-muted font-weight-bold">Fonnte / Wablas API</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #3b82f6 !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">TOTAL PESAN TERKIRIM</small>
                            <strong class="text-primary h5 font-weight-bold mb-0"><?= $totalSent ?> Pesan</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #f59e0b !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">PENGINGAT KONTROL H-1</small>
                            <strong class="text-warning h5 font-weight-bold mb-0"><?= $totalReminder ?> Terjadwal</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="p-3 border rounded bg-white" style="border: 1px solid #b8b8b8 !important; border-left: 4px solid #0d9f4f !important;">
                            <small class="text-muted d-block text-xs font-weight-bold">TEMPLAT PESAN OTOMATIS</small>
                            <strong class="text-teal h5 font-weight-bold mb-0">4 Templat Aktif</strong>
                        </div>
                    </div>
                </div>

                <!-- 4 Templat Pesan Otomatis -->
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-layer-group text-teal mr-1"></i> Templat Pesan Otomatis Terkonfigurasi</h6>
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 bg-light border" style="border: 1px solid #b8b8b8 !important;">
                            <div class="card-body p-3 text-xs">
                                <strong class="text-dark d-block mb-1 font-weight-bold"><i class="fas fa-ticket text-teal mr-1"></i> 1. e-Ticket Antrean Online</strong>
                                <p class="text-muted mb-2">Dikirim otomatis saat pasien selesai mendaftar secara mandiri melalui website.</p>
                                <div class="p-2 bg-white border rounded font-italic">
                                    "Halo [Nama Pasien], pendaftaran Anda di Sawamawa Medical Center berhasil! No Antrean: [A-01], Poliklinik: [Poli Umum], Estimasi: [09:00 WITA]."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 bg-light border" style="border: 1px solid #b8b8b8 !important;">
                            <div class="card-body p-3 text-xs">
                                <strong class="text-dark d-block mb-1 font-weight-bold"><i class="fas fa-pills text-teal mr-1"></i> 2. Notifikasi Obat Siap Ambil</strong>
                                <p class="text-muted mb-2">Dikirim saat apoteker selesai meracik resep dokter di instalasi farmasi.</p>
                                <div class="p-2 bg-white border rounded font-italic">
                                    "Yth. [Nama Pasien], resep obat Anda No. [RX-001] telah selesai diracik dan siap diambil di Loket Farmasi. Terima kasih."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 bg-light border" style="border: 1px solid #b8b8b8 !important;">
                            <div class="card-body p-3 text-xs">
                                <strong class="text-dark d-block mb-1 font-weight-bold"><i class="fas fa-calendar-check text-teal mr-1"></i> 3. Pengingat Kontrol Ulang</strong>
                                <p class="text-muted mb-2">Dikirim otomatis H-1 sebelum tanggal jadwal kontrol ulang ke dokter spesialis.</p>
                                <div class="p-2 bg-white border rounded font-italic">
                                    "Pengingat: Besok adalah jadwal kontrol ulang Anda dengan dr. [Nama Dokter] di Poliklinik [Nama Poli] pukul [Jam]."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card h-100 bg-light border" style="border: 1px solid #b8b8b8 !important;">
                            <div class="card-body p-3 text-xs">
                                <strong class="text-dark d-block mb-1 font-weight-bold"><i class="fas fa-file-invoice-dollar text-teal mr-1"></i> 4. Notifikasi Slip Gaji</strong>
                                <p class="text-muted mb-2">Dikirim ke staf/karyawan saat payroll bulanan resmi dibayarkan oleh bagian Keuangan.</p>
                                <div class="p-2 bg-white border rounded font-italic">
                                    "Yth. [Nama Pegawai], gaji periode [Bulan] telah ditransfer ke rekening Anda. Unduh slip gaji di portal karyawan."
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr style="border-top: 1px dashed #b8b8b8; margin: 25px 0;">

                <!-- Riwayat Log Pesan WhatsApp Terkirim -->
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-history text-teal mr-1"></i> Riwayat Log Pesan WhatsApp Terkirim (Audit Log)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mb-0 datatable" style="border: 1px solid #b8b8b8 !important;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th style="width: 50px;">NO</th>
                                <th>PENERIMA (NAMA & NO. WA)</th>
                                <th>KATEGORI PESAN</th>
                                <th>ISI KONTEN PESAN WHATSAPP</th>
                                <th style="width: 140px;">WAKTU TERKIRIM</th>
                                <th style="width: 90px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($logs as $l): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <strong class="text-dark"><?= esc($l->recipient_name ?? 'Pelanggan/Pasien') ?></strong>
                                        <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($l->recipient_phone) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border font-weight-bold"><?= strtoupper($l->message_type) ?></span>
                                    </td>
                                    <td class="text-xs">
                                        <?= nl2br(esc($l->message_content)) ?>
                                    </td>
                                    <td class="text-center text-xs">
                                        <?= date('d/m/Y H:i', strtotime($l->sent_at ?? $l->created_at)) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> <?= strtoupper($l->status) ?></span>
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

<!-- Modal Form Test WA -->
<div class="modal fade" id="modalTestWa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('system/send-whatsapp') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content" style="border: 1px solid #b8b8b8 !important; border-radius: 4px;">
                <div class="modal-header bg-teal text-white py-2">
                    <h6 class="modal-title font-weight-bold"><i class="fab fa-whatsapp mr-1"></i> Simulator Pengiriman WhatsApp Gateway</h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control form-control-sm" placeholder="Nama Pasien / Dokter / Staf" required>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_phone" class="form-control form-control-sm" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-dark">Tipe Pesan</label>
                        <select name="message_type" class="form-control form-control-sm">
                            <option value="antrean_online">e-Ticket Antrean Online</option>
                            <option value="obat_siap">Pemberitahuan Obat Siap Ambil</option>
                            <option value="pengingat_kontrol">Pengingat Jadwal Kontrol H-1</option>
                            <option value="info_umum">Pengumuman / Informasi Umum Klinik</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-dark">Konten Pesan WhatsApp <span class="text-danger">*</span></label>
                        <textarea name="message_content" class="form-control form-control-sm" rows="4" placeholder="Tulis isi pesan WhatsApp..." required>Halo, ini adalah pesan resmi dari Sawamawa Medical Center. Terima kasih telah mempercayakan layanan kesehatan Anda kepada kami.</textarea>
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fab fa-whatsapp mr-1"></i> Kirim Pesan Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
