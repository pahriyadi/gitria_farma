<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<!-- html2canvas library for high-resolution JPG image export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<?php
$currentTier = strtolower($patient->membership_tier ?? 'regular');
?>

<style>
    /* Styling Kartu Identitas Pasien CR80 / Ukuran Standar KTP & ATM */
    .id-card-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 24px 0;
    }

    .patient-card {
        width: 550px;
        height: 345px;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        padding: 18px 22px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        transition: all 0.3s ease;
    }

    /* 1. TEMA KARTU REGULER (EMERALD CLEAN) */
    .patient-card.tier-regular {
        background: #ffffff;
        border: 1.5px solid #0d9f4f;
        box-shadow: 0 8px 25px rgba(13, 159, 79, 0.12);
        color: #1e293b;
        background-image: 
            radial-gradient(#e6f4ea 1.2px, transparent 1.2px),
            linear-gradient(135deg, rgba(13,159,79,0.04) 0%, rgba(255,255,255,1) 50%, rgba(13,159,79,0.06) 100%);
        background-size: 16px 16px, 100% 100%;
    }
    .patient-card.tier-regular::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, #0d9f4f 0%, #20c997 50%, #0d9f4f 100%);
    }
    .patient-card.tier-regular .card-watermark {
        color: rgba(13, 159, 79, 0.05);
    }
    .patient-card.tier-regular .card-title-name {
        color: #0f172a;
    }
    .patient-card.tier-regular .card-rm-num {
        color: #0d9f4f;
    }
    .patient-card.tier-regular .tier-badge {
        background: #0d9f4f;
        color: #ffffff;
    }

    /* 2. TEMA KARTU GOLD MEMBER (ROYAL METALLIC GOLD) */
    .patient-card.tier-gold {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 35%, #fde68a 70%, #fef08a 100%);
        border: 2px solid #d97706;
        box-shadow: 0 10px 30px rgba(217, 119, 6, 0.25);
        color: #451a03;
    }
    .patient-card.tier-gold::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, #d97706 0%, #fbbf24 50%, #b45309 100%);
    }
    .patient-card.tier-gold .card-watermark {
        color: rgba(217, 119, 6, 0.07);
    }
    .patient-card.tier-gold .card-title-name {
        color: #78350f;
    }
    .patient-card.tier-gold .card-rm-num {
        color: #b45309;
        text-shadow: 0 1px 2px rgba(245, 158, 11, 0.3);
    }
    .patient-card.tier-gold .tier-badge {
        background: linear-gradient(90deg, #b45309 0%, #d97706 100%);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(180, 83, 9, 0.4);
    }
    .patient-card.tier-gold .border-bottom,
    .patient-card.tier-gold .border-top {
        border-color: rgba(217, 119, 6, 0.25) !important;
    }

    /* 3. TEMA KARTU PLATINUM / VIP EXECUTIVE (OBSIDIAN TITANIUM) */
    .patient-card.tier-vip,
    .patient-card.tier-platinum {
        background: radial-gradient(circle at top right, #1a2c47 0%, #08101a 100%);
        border: 2px solid #ffc107;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.6);
        color: #f1f5f9;
    }
    .patient-card.tier-vip::before,
    .patient-card.tier-platinum::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, #d97706 0%, #fef08a 35%, #d97706 70%, #fbbf24 100%);
    }
    .patient-card.tier-vip .card-watermark,
    .patient-card.tier-platinum .card-watermark {
        color: rgba(255, 193, 7, 0.06);
    }
    .patient-card.tier-vip .card-title-name,
    .patient-card.tier-platinum .card-title-name {
        color: #ffffff;
    }
    .patient-card.tier-vip .card-rm-num,
    .patient-card.tier-platinum .card-rm-num {
        color: #ffc107;
        text-shadow: 0 2px 10px rgba(255, 193, 7, 0.4);
    }
    .patient-card.tier-vip .tier-badge,
    .patient-card.tier-platinum .tier-badge {
        background: #000000;
        color: #ffc107;
        border: 1px solid #ffc107;
        box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
    }
    .patient-card.tier-vip .text-secondary,
    .patient-card.tier-vip .text-muted,
    .patient-card.tier-platinum .text-secondary,
    .patient-card.tier-platinum .text-muted {
        color: #94a3b8 !important;
    }
    .patient-card.tier-vip .border-bottom,
    .patient-card.tier-vip .border-top,
    .patient-card.tier-platinum .border-bottom,
    .patient-card.tier-platinum .border-top {
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    /* Common Card Details */
    .card-watermark {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 170px;
        pointer-events: none;
        z-index: 1;
    }

    .card-chip {
        width: 40px;
        height: 30px;
        background: linear-gradient(135deg, #d4af37 0%, #f9d976 50%, #c59b27 100%);
        border-radius: 5px;
        border: 1px solid #b89728;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .card-chip::after {
        content: '';
        position: absolute;
        top: 10px; left: 0; right: 0; height: 1px;
        background: rgba(0,0,0,0.25);
    }

    .qr-container {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 4px;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    /* Print styling */
    @media print {
        body * {
            visibility: hidden;
        }
        .patient-card, .patient-card * {
            visibility: visible;
        }
        .patient-card {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            box-shadow: none !important;
            border: 1px solid #666 !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="row">
    <!-- Header Title & Action Bar -->
    <div class="col-md-12 mb-3">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 border rounded shadow-none">
            <div>
                <a href="<?= base_url('klinik/pendaftaran') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pendaftaran
                </a>
                <span class="h5 font-weight-bold text-dark mb-0 align-middle">
                    <i class="fas fa-id-card text-teal mr-1"></i> Kartu Identitas Berobat Pasien
                </span>
            </div>
            <div class="d-flex align-items-center">
                <!-- Tombol Download JPG -->
                <button type="button" class="btn btn-teal btn-sm font-weight-bold mr-2" id="btn-download-jpg" onclick="downloadCardAsJpg()">
                    <i class="fas fa-file-image mr-1"></i> Download Kartu (.JPG)
                </button>
                <!-- Tombol Print -->
                <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold mr-2" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak Kartu PVC
                </button>
                <!-- Tombol Kirim WhatsApp -->
                <button type="button" class="btn btn-success btn-sm font-weight-bold" id="btn-share-wa" data-toggle="modal" data-target="#shareWaModal">
                    <i class="fab fa-whatsapp mr-1"></i> Bagikan ke WhatsApp
                </button>
            </div>
        </div>
    </div>

    <!-- Theme Switcher Toolbar -->
    <div class="col-md-12 mb-3">
        <div class="card bg-white border p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="text-xs font-weight-bold text-muted text-uppercase mr-2">GOLONGAN KARTU PASIEN SAAT INI:</span>
                    <?php if ($currentTier === 'gold'): ?>
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-1" style="font-size: 13px; background:#ffc107;"><i class="fas fa-crown mr-1"></i> GOLD MEMBER (PRIORITAS)</span>
                    <?php elseif ($currentTier === 'vip' || $currentTier === 'platinum'): ?>
                        <span class="badge badge-dark font-weight-bold px-3 py-1" style="font-size: 13px; background:#0f172a; border: 1px solid #ffc107; color: #ffc107;"><i class="fas fa-gem mr-1"></i> PLATINUM / VIP EXECUTIVE</span>
                    <?php else: ?>
                        <span class="badge badge-success font-weight-bold px-3 py-1" style="font-size: 13px;"><i class="fas fa-id-card mr-1"></i> REGULER (STANDAR)</span>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Switch Buttons -->
                <div class="btn-group btn-group-sm mt-2 mt-md-0" role="group">
                    <button type="button" class="btn btn-outline-success font-weight-bold <?= ($currentTier === 'regular') ? 'active' : '' ?>" onclick="switchCardTheme('regular')">
                        🟢 Tema Reguler (Emerald)
                    </button>
                    <button type="button" class="btn btn-outline-warning text-dark font-weight-bold <?= ($currentTier === 'gold') ? 'active' : '' ?>" onclick="switchCardTheme('gold')">
                        🟡 Tema Gold Member
                    </button>
                    <button type="button" class="btn btn-outline-dark font-weight-bold <?= ($currentTier === 'vip' || $currentTier === 'platinum') ? 'active' : '' ?>" onclick="switchCardTheme('vip')">
                        ⚫ Tema VIP Executive
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Card Area -->
    <div class="col-md-12">
        <div class="card bg-light border p-4 text-center">
            <div class="id-card-wrapper">
                
                <!-- ========================================================================= -->
                <!-- DIGITAL PATIENT ID CARD CONTAINER (CR80 RATIO / FORMAT KTP) -->
                <!-- ========================================================================= -->
                <div id="patient-id-card" class="patient-card tier-<?= esc($currentTier ?: 'regular') ?> text-left">
                    <i class="fas fa-hospital-user card-watermark"></i>

                    <!-- Card Header: Clinic Identity & Membership Tier Badge -->
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <?php if (clinic_logo()): ?>
                                <img src="<?= clinic_logo() ?>" alt="Logo" style="height: 34px; max-width: 38px; object-fit: contain;" class="mr-2">
                            <?php else: ?>
                                <span class="d-inline-flex align-items-center justify-content-center mr-2 text-teal font-weight-bold" style="background:#e6f4ea; width:34px; height:34px; border-radius:6px; border:1px solid #0d9f4f; font-size:16px;">
                                    <i class="fas fa-hospital"></i>
                                </span>
                            <?php endif; ?>
                            <div>
                                <h6 class="font-weight-bold mb-0 card-title-name" style="font-size: 14px; letter-spacing: 0.3px;"><?= esc(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER')) ?></h6>
                                <small class="text-secondary d-block" style="font-size: 9.5px; line-height: 1.1;"><?= esc(clinic_setting('clinic_address', 'Layanan Kesehatan Paripurna & Resto Gizi')) ?></small>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge tier-badge font-weight-bold text-uppercase px-2 py-1" id="card-tier-label" style="font-size: 9px; letter-spacing: 0.8px;">
                                <?php if ($currentTier === 'gold'): ?>
                                    ★ GOLD MEMBER ★
                                <?php elseif ($currentTier === 'vip' || $currentTier === 'platinum'): ?>
                                    💎 VIP EXECUTIVE
                                <?php else: ?>
                                    KARTU PASIEN
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body: Patient Details & QR -->
                    <div class="row align-items-center mt-2 pt-1 position-relative" style="z-index: 2;">
                        <!-- Left Details (68%) -->
                        <div class="col-8 pr-1">
                            <!-- No RM (Prominent) -->
                            <div class="mb-2">
                                <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 8.5px; letter-spacing: 0.5px;">Nomor Rekam Medis (No RM):</small>
                                <span class="font-weight-bold font-monospace card-rm-num" style="font-size: 19px; letter-spacing: 1px;">
                                    <?= esc($patient->no_rm) ?>
                                </span>
                            </div>

                            <!-- Patient Info Grid -->
                            <table style="width: 100%; font-size: 11px; line-height: 1.4;">
                                <tr>
                                    <td style="width: 72px; font-weight: 600;" class="text-secondary">Nama</td>
                                    <td style="width: 8px;">:</td>
                                    <td><strong class="card-title-name" style="font-size: 12.5px;"><?= esc(strtoupper($patient->name)) ?></strong></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;" class="text-secondary">NIK</td>
                                    <td>:</td>
                                    <td><?= esc($patient->nik ?: '-') ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;" class="text-secondary">Tgl Lahir</td>
                                    <td>:</td>
                                    <td>
                                        <?php 
                                        $dobText = $patient->date_of_birth ? date('d/m/Y', strtotime($patient->date_of_birth)) : '-';
                                        $age = $patient->date_of_birth ? (date('Y') - date('Y', strtotime($patient->date_of_birth))) . ' Thn' : '';
                                        $genderText = $patient->gender === 'L' ? 'Laki-laki' : ($patient->gender === 'P' ? 'Perempuan' : '-');
                                        echo esc($dobText . ($age ? " ($age)" : "") . " / " . $genderText);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600;" class="text-secondary">Alamat</td>
                                    <td>:</td>
                                    <td class="text-truncate" style="max-width: 195px;" title="<?= esc($patient->address) ?>">
                                        <?= esc($patient->address ?: '-') ?>
                                    </td>
                                </tr>
                                <?php if (!empty($patient->bpjs_number)): ?>
                                <tr>
                                    <td style="font-weight: 600;" class="text-secondary">No BPJS</td>
                                    <td>:</td>
                                    <td><span class="badge badge-success px-1" style="font-size: 9.5px;"><?= esc($patient->bpjs_number) ?></span></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <!-- Right Details: Smart Chip & QR Code (32%) -->
                        <div class="col-4 text-center pl-1">
                            <div class="d-flex justify-content-end mb-2 pr-1">
                                <div class="card-chip"></div>
                            </div>
                            
                            <!-- Dynamic QR Code -->
                            <?php 
                            $qrData = urlencode("SAWAMAWA|" . $patient->no_rm . "|" . $patient->name . "|" . $patient->nik . "|" . $currentTier);
                            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=0&data=" . $qrData;
                            ?>
                            <div class="qr-container">
                                <img src="<?= $qrUrl ?>" alt="QR Code Pasien" style="width: 82px; height: 82px; display: block;" crossorigin="anonymous">
                            </div>
                            <small class="text-muted d-block font-weight-bold mt-1" style="font-size: 8.5px; letter-spacing: 0.5px;">SCAN IDENTITAS</small>
                        </div>
                    </div>

                    <!-- Card Footer: Official Warning / Registration Date -->
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top position-relative" style="z-index: 2;">
                        <small class="text-secondary" style="font-size: 8.5px;">
                            <i class="fas fa-circle-info mr-1 text-teal"></i> Harap selalu membawa kartu ini saat berobat / berkunjung.
                        </small>
                        <small class="text-muted font-weight-bold" style="font-size: 8.5px;">
                            Reg: <?= date('d M Y', strtotime($patient->created_at ?? date('Y-m-d'))) ?>
                        </small>
                    </div>
                </div>
                <!-- END DIGITAL PATIENT ID CARD -->

            </div>
            
            <div class="text-center mt-3 text-secondary text-xs">
                <i class="fas fa-lightbulb text-warning mr-1"></i> Kartu berstandar kartu identitas ID-1 / KTP (CR80) beresolusi tinggi. Cocok untuk dicetak pada mesin kartu PVC maupun disimpan di smartphone pasien.
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL BAGIKAN KARTU PASIEN KE WHATSAPP -->
<!-- ========================================================================= -->
<div class="modal fade" id="shareWaModal" tabindex="-1" role="dialog" aria-labelledby="shareWaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title font-weight-bold" id="shareWaModalLabel">
                    <i class="fab fa-whatsapp mr-1"></i> Bagikan Kartu Pasien via WhatsApp
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <?php
                // Sanitize Indonesian phone number
                $rawPhone = preg_replace('/[^0-9]/', '', (string)$patient->phone);
                if (str_starts_with($rawPhone, '0')) {
                    $rawPhone = '62' . substr($rawPhone, 1);
                } elseif (str_starts_with($rawPhone, '8')) {
                    $rawPhone = '62' . $rawPhone;
                }

                $clinicName = clinic_setting('clinic_name', 'SAWAMAWA Medical Center');
                $tierText = ($currentTier === 'gold') ? 'Gold Priority Member' : (($currentTier === 'vip' || $currentTier === 'platinum') ? 'VIP Executive Member' : 'Reguler');
                
                $waMessage = "Halo Bpk/Ibu *" . $patient->name . "*,\n\n"
                           . "Berikut adalah data *Kartu Identitas Pasien Resmi* Anda di *" . $clinicName . "*:\n\n"
                           . "💳 *Nomor Rekam Medis (No RM)*: *" . $patient->no_rm . "*\n"
                           . "⭐ *Golongan Keanggotaan*: *" . $tierText . "*\n"
                           . "👤 *Nama Pasien*: " . $patient->name . "\n"
                           . "🆔 *NIK*: " . ($patient->nik ?: '-') . "\n"
                           . "📅 *Tanggal Lahir*: " . ($patient->date_of_birth ? date('d/m/Y', strtotime($patient->date_of_birth)) : '-') . "\n"
                           . "📍 *Alamat*: " . ($patient->address ?: '-') . "\n\n"
                           . "Harap simpan nomor Rekam Medis ini dan tunjukkan kartu ini kepada petugas pendaftaran saat berkunjung untuk mendapatkan pelayanan antrean prioritas.\n\n"
                           . "Terima kasih telah mempercayakan kesehatan Anda kepada *" . $clinicName . "*.\n"
                           . "Salam Sehat Selalu! 🙏🏥";
                ?>

                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-dark">Nomor WhatsApp Pasien / Wali:</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fab fa-whatsapp text-success"></i></span></div>
                        <input type="text" id="input-wa-phone" class="form-control font-weight-bold" value="<?= esc($rawPhone) ?>" placeholder="Contoh: 6281234567890">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="text-xs font-weight-bold text-dark">Pratinjau Pesan yang Dikirimkan:</label>
                    <textarea id="input-wa-message" class="form-control text-xs" rows="9" readonly style="background:#f8fafc; font-family: monospace;"><?= esc($waMessage) ?></textarea>
                </div>

                <div class="alert alert-info alert-excel text-xs mb-0">
                    <i class="fas fa-info-circle mr-1 text-info"></i> <strong>Tips:</strong> Klik tombol <em>"Buka WhatsApp & Kirim Pesan"</em> di bawah, lalu Anda dapat melampirkan file gambar <strong>.JPG</strong> kartu pasien yang telah di-download ke dalam chat WhatsApp pasien.
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm font-weight-bold px-3" onclick="sendWhatsAppNow()">
                    <i class="fab fa-whatsapp mr-1"></i> Buka WhatsApp & Kirim Pesan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Dynamic Theme Switcher Preview
    function switchCardTheme(tier) {
        const card = $('#patient-id-card');
        const badge = $('#card-tier-label');
        
        card.removeClass('tier-regular tier-gold tier-vip tier-platinum');
        card.addClass('tier-' + tier);

        if (tier === 'gold') {
            badge.text('★ GOLD MEMBER ★');
        } else if (tier === 'vip') {
            badge.text('💎 VIP EXECUTIVE');
        } else {
            badge.text('KARTU PASIEN');
        }
    }

    // 2. Export HTML Patient Card to High-Resolution JPG Image
    function downloadCardAsJpg() {
        const btn = $('#btn-download-jpg');
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses JPG...').prop('disabled', true);

        const cardElem = document.getElementById('patient-id-card');

        html2canvas(cardElem, {
            scale: 3, // High DPI Rendering for ultra-sharp text and borders
            useCORS: true,
            allowTaint: true,
            backgroundColor: null
        }).then(function(canvas) {
            const image = canvas.toDataURL('image/jpeg', 0.95);
            const link = document.createElement('a');
            const cleanName = '<?= preg_replace("/[^a-zA-Z0-9]/", "_", $patient->name) ?>';
            link.download = 'Kartu_Pasien_<?= $patient->no_rm ?>_' + cleanName + '.jpg';
            link.href = image;
            link.click();

            btn.html('<i class="fas fa-file-image mr-1"></i> Download Kartu (.JPG)').prop('disabled', false);
        }).catch(function(err) {
            console.error('Export error:', err);
            alert('Gagal mengekspor gambar. Silakan gunakan tombol Cetak Kartu.');
            btn.html('<i class="fas fa-file-image mr-1"></i> Download Kartu (.JPG)').prop('disabled', false);
        });
    }

    // 3. Open WhatsApp Web / App with formatted text
    function sendWhatsAppNow() {
        let phone = $('#input-wa-phone').val().replace(/[^0-9]/g, '');
        if (!phone) {
            alert('Nomor WhatsApp pasien belum terisi.');
            return;
        }
        if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
        } else if (phone.startsWith('8')) {
            phone = '62' + phone;
        }

        const msg = encodeURIComponent($('#input-wa-message').val());
        const url = 'https://api.whatsapp.com/send?phone=' + phone + '&text=' + msg;
        window.open(url, '_blank');
        $('#shareWaModal').modal('hide');
    }
</script>
<?= $this->endSection() ?>
