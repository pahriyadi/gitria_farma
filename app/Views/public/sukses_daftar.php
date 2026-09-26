<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Bukti Pendaftaran Online') ?> - <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></title>
    
    <?php if (clinic_favicon()): ?>
        <link rel="icon" type="image/x-icon" href="<?= clinic_favicon() ?>">
        <link rel="shortcut icon" href="<?= clinic_favicon() ?>">
    <?php endif; ?>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- html2canvas for JPG Card Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --brand-primary: #0d9488;
            --brand-dark: #0f766e;
            --brand-light: #14b8a6;
            --dark-slate: #0f172a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --radius-md: 10px;
            --radius-lg: 16px;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: var(--text-main);
            padding: 30px 15px 60px;
            line-height: 1.6;
        }

        .ticket-container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .ticket-header-box {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #ffffff;
            padding: 26px 25px;
            text-align: center;
        }

        .queue-badge-box {
            background-color: #f0fdf4;
            border: 2px dashed #0d9488;
            border-radius: var(--radius-md);
            padding: 20px;
            text-align: center;
            margin-bottom: 24px;
        }

        .queue-number-val {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 54px;
            font-weight: 800;
            color: #0f766e;
            line-height: 1;
            margin: 8px 0;
            letter-spacing: 1px;
        }

        /* Patient Digital Card CR80 Standard */
        .patient-card-public {
            width: 100%;
            max-width: 500px;
            height: 290px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            padding: 16px 20px;
            margin: 0 auto;
            text-align: left;
            background-image: 
                radial-gradient(#f1f5f9 1.5px, transparent 1.5px),
                linear-gradient(135deg, rgba(13,148,136,0.04) 0%, rgba(255,255,255,1) 50%, rgba(13,148,136,0.06) 100%);
            background-size: 16px 16px, 100% 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .patient-card-public::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #0d9488 0%, #059669 50%, #0284c7 100%);
        }

        .card-chip-gold {
            width: 34px;
            height: 24px;
            background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #c59b27 100%);
            border-radius: 4px;
            border: 1px solid #b89728;
            position: relative;
            flex-shrink: 0;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .ticket-container {
                border: 1px solid #000;
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="ticket-container">
    <!-- Header Banner -->
    <div class="ticket-header-box">
        <div class="d-inline-flex align-items-center px-2.5 py-1 rounded-pill mb-2" style="background: rgba(255,255,255,0.2); font-size: 11px; font-weight: 700;">
            <i class="fas fa-check-circle mr-1 text-white"></i> RESERVASI TERVERIFIKASI
        </div>
        <h4 class="font-weight-bold mb-1">
            Pendaftaran Berhasil Dikonfirmasi!
        </h4>
        <p class="mb-0 text-xs" style="opacity: 0.9;">
            <?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?> &bull; Sistem Antrean Terpadu
        </p>
    </div>

    <!-- Body Ticket -->
    <div class="p-4">
        <!-- Kotak Nomor Antrean -->
        <div class="queue-badge-box" id="section-queue-slip">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-success px-3 py-1 font-weight-bold text-uppercase" style="font-size: 11px; background: #059669;">
                    NOMOR ANTREAN ANDA
                </span>
                <span class="badge badge-light border text-muted font-weight-bold text-xs">
                    <?= date('d M Y', strtotime($visit->visit_date)) ?>
                </span>
            </div>
            <div class="queue-number-val"><?= esc($visit->queue_no) ?></div>
            <div class="font-weight-bold text-dark" style="font-size: 15px;">
                <?= $visit->visit_type === 'tindakan' ? esc($visit->service_name) : 'Poliklinik ' . esc($visit->polyclinic_name) ?>
            </div>
            <small class="text-secondary mt-1 d-block" style="font-size: 12.5px;">
                Dokter: <strong><?= esc($visit->doctor_name) ?></strong> &bull; Pasien: <strong><?= esc($visit->patient_name) ?> (<?= esc($visit->no_rm) ?>)</strong>
            </small>

            <!-- Quick Download & Save Button Bar -->
            <div class="mt-3 pt-3 border-top d-flex justify-content-center flex-wrap no-print" style="gap: 8px;">
                <button type="button" class="btn btn-teal btn-sm font-weight-bold shadow-sm" id="btn-dl-ticket-jpg" onclick="downloadTicketJpg()" style="background:#0d9488; color:#ffffff; border-radius:6px;">
                    <i class="fas fa-download mr-1"></i> Download Karcis Antrean (.JPG)
                </button>
                <a href="<?= $waUrl ?>" target="_blank" class="btn btn-success btn-sm font-weight-bold shadow-sm" style="border-radius:6px;">
                    <i class="fab fa-whatsapp mr-1"></i> Simpan ke WhatsApp
                </a>
                <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold" onclick="window.print()" style="border-radius:6px;">
                    <i class="fas fa-print mr-1"></i> Cetak / PDF
                </button>
            </div>
        </div>

        <!-- Tabel Informasi Pasien -->
        <div class="card p-3 mb-4 bg-light border" style="border-radius: var(--radius-md);">
            <h6 class="font-weight-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 13px;">
                <i class="fas fa-id-badge text-teal mr-1" style="color: var(--brand-primary);"></i> Rincian Identitas Pendaftaran:
            </h6>
            <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                <tr>
                    <td style="width: 155px;" class="text-muted font-weight-bold">Nomor Rekam Medis</td>
                    <td style="width: 10px;">:</td>
                    <td><strong class="text-teal font-monospace" style="font-size: 14.5px; color: #0f766e;"><?= esc($visit->no_rm) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-bold">Nama Lengkap</td>
                    <td>:</td>
                    <td><strong class="text-dark"><?= esc(strtoupper($visit->patient_name)) ?></strong></td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-bold">NIK KTP / KK</td>
                    <td>:</td>
                    <td><?= esc($visit->nik ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-bold">Nomor Kunjungan</td>
                    <td>:</td>
                    <td><span class="font-monospace text-secondary"><?= esc($visit->no_visit) ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-bold">Nomor WhatsApp</td>
                    <td>:</td>
                    <td><?= esc($visit->phone) ?></td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-bold">Alamat Pasien</td>
                    <td>:</td>
                    <td><?= esc($visit->address ?: '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- Bagian Kartu Pasien Digital CR80 -->
        <div class="card p-3 mb-4 border text-center" style="border-radius: var(--radius-md);">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13px;">
                    <i class="fas fa-id-card text-teal mr-1" style="color: var(--brand-primary);"></i> Kartu Pasien Digital Resmi
                </h6>
                <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold no-print" id="btn-dl-jpg-public" onclick="downloadPublicCardJpg()" style="color: #0f766e; border-color: #0d9488;">
                    <i class="fas fa-file-image mr-1"></i> Simpan Kartu (.JPG)
                </button>
            </div>

            <!-- CONTAINER KARTU PASIEN DIGITAL -->
            <div id="public-patient-card" class="patient-card-public shadow-none">
                <!-- Header Kartu -->
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <?php if (clinic_logo()): ?>
                            <img src="<?= clinic_logo() ?>" alt="Logo" style="height: 26px; max-width: 32px; object-fit: contain; margin-right: 8px;">
                        <?php else: ?>
                            <span class="text-teal font-weight-bold mr-2" style="color: var(--brand-primary);"><i class="fas fa-hospital"></i></span>
                        <?php endif; ?>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 11.5px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></h6>
                            <small class="text-secondary d-block" style="font-size: 8px; line-height: 1;"><?= esc(clinic_setting('clinic_address', 'Sumbawa Besar, NTB')) ?></small>
                        </div>
                    </div>
                    <span class="badge font-weight-bold text-uppercase px-2 py-1" style="font-size: 8px; background: #ccfbf1; color: #0f766e;">KARTU PASIEN</span>
                </div>

                <!-- Konten Pasien & QR -->
                <div class="row align-items-center mt-2 pt-1">
                    <div class="col-8 pr-1">
                        <div class="mb-1">
                            <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 8px;">No. Rekam Medis (No RM):</small>
                            <span class="font-weight-bold font-monospace" style="font-size: 15px; color: #0f766e !important;">
                                <?= esc($visit->no_rm) ?>
                            </span>
                        </div>
                        <table style="width: 100%; font-size: 9.5px; line-height: 1.35; color: #333333;">
                            <tr>
                                <td style="width: 60px; color: #64748b; font-weight: 600;">Nama</td>
                                <td style="width: 5px;">:</td>
                                <td><strong class="text-dark"><?= esc(strtoupper($visit->patient_name)) ?></strong></td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-weight: 600;">NIK</td>
                                <td>:</td>
                                <td><?= esc($visit->nik ?: '-') ?></td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-weight: 600;">Tgl Lahir</td>
                                <td>:</td>
                                <td><?= esc($visit->date_of_birth ? date('d/m/Y', strtotime($visit->date_of_birth)) : '-') ?></td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-weight: 600;">Alamat</td>
                                <td>:</td>
                                <td class="text-truncate" style="max-width: 160px;"><?= esc($visit->address ?: '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-4 text-center pl-1">
                        <div class="d-flex justify-content-end mb-1 pr-1">
                            <div class="card-chip-gold"></div>
                        </div>
                        <?php 
                        $qrData = urlencode("SAWAMAWA|" . $visit->no_rm . "|" . $visit->patient_name . "|" . $visit->nik);
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&margin=0&data=" . $qrData;
                        ?>
                        <div style="background: #ffffff; border: 1px solid #d1d5db; border-radius: 4px; padding: 2px; display: inline-block;">
                            <img src="<?= $qrUrl ?>" alt="QR Pasien" style="width: 64px; height: 64px; display: block;" crossorigin="anonymous">
                        </div>
                    </div>
                </div>

                <!-- Footer Kartu -->
                <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top" style="font-size: 8px; color: #64748b;">
                    <span><i class="fas fa-circle-info mr-1" style="color: var(--brand-primary);"></i> Tunjukkan saat tiba di klinik.</span>
                    <span>SATUSEHAT Terkoneksi</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Navigasi & Cetak -->
        <div class="text-center no-print">
            <a href="<?= $waUrl ?>" target="_blank" class="btn btn-success font-weight-bold px-4 py-2.5 mr-2 mb-2 shadow-sm" style="border-radius: 8px;">
                <i class="fab fa-whatsapp mr-1.5"></i> Buka / Simpan di WhatsApp
            </a>
            <button type="button" class="btn btn-outline-dark font-weight-bold px-4 py-2.5 mr-2 mb-2" onclick="window.print()" style="border-radius: 8px;">
                <i class="fas fa-print mr-1.5"></i> Cetak Bukti Antrean
            </button>
            <a href="<?= base_url() ?>" class="btn btn-light border font-weight-bold px-3 py-2.5 mb-2" style="border-radius: 8px;">
                <i class="fas fa-home mr-1"></i> Ke Beranda
            </a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function downloadTicketJpg() {
        const btn = $('#btn-dl-ticket-jpg');
        const oldHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...').prop('disabled', true);

        const ticketElem = document.getElementById('section-queue-slip');

        html2canvas(ticketElem, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#f0fdf4'
        }).then(function(canvas) {
            const image = canvas.toDataURL('image/jpeg', 0.95);
            const link = document.createElement('a');
            const queueNo = '<?= esc($visit->queue_no) ?>';
            link.download = 'Karcis_Antrean_' + queueNo + '_<?= esc($visit->no_rm) ?>.jpg';
            link.href = image;
            link.click();

            btn.html(oldHtml).prop('disabled', false);
        }).catch(function(err) {
            console.error('Export ticket error:', err);
            alert('Gagal mendownload karcis. Silakan gunakan tombol Cetak atau Buka WhatsApp.');
            btn.html(oldHtml).prop('disabled', false);
        });
    }

    function downloadPublicCardJpg() {
        const btn = $('#btn-dl-jpg-public');
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...').prop('disabled', true);

        const cardElem = document.getElementById('public-patient-card');

        html2canvas(cardElem, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        }).then(function(canvas) {
            const image = canvas.toDataURL('image/jpeg', 0.95);
            const link = document.createElement('a');
            const cleanName = '<?= preg_replace("/[^a-zA-Z0-9]/", "_", $visit->patient_name) ?>';
            link.download = 'Kartu_Pasien_<?= $visit->no_rm ?>_' + cleanName + '.jpg';
            link.href = image;
            link.click();

            btn.html('<i class="fas fa-file-image mr-1"></i> Simpan Kartu (.JPG)').prop('disabled', false);
        }).catch(function(err) {
            console.error('Export error:', err);
            alert('Gagal mendownload gambar. Silakan gunakan tombol Cetak.');
            btn.html('<i class="fas fa-file-image mr-1"></i> Simpan Kartu (.JPG)').prop('disabled', false);
        });
    }
</script>

</body>
</html>
