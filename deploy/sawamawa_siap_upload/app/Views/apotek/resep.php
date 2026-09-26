<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Active e-Prescriptions Queue -->
    <div class="col-md-5">
        <div class="card card-outline card-teal shadow sticky-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark mb-0"><i class="fas fa-file-prescription text-teal mr-1"></i> Resep Menunggu Penyiapan</h3>
                <span class="badge badge-teal ml-auto font-weight-normal" id="lbl-presc-count"><?= count($prescriptions) ?> Resep</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="presc-list">
                    <?php if (empty($prescriptions)): ?>
                        <div class="text-center text-muted p-4">Tidak ada antrean resep obat saat ini.</div>
                    <?php else: ?>
                        <?php foreach ($prescriptions as $p): 
                            $isPaid = !empty($p->is_paid);
                            $bStatus = $p->billing_status ?? 'open';
                            $pMethod = $p->payment_method ?? '';
                        ?>
                            <a href="#" class="list-group-item list-group-item-action presc-item <?= $isPaid ? 'border-left-success' : 'border-left-warning' ?>" 
                               data-id="<?= $p->id ?>" 
                               data-name="<?= esc($p->patient_name) ?>" 
                               data-rm="<?= esc($p->no_rm) ?>" 
                               data-doctor="<?= esc($p->doctor_name) ?>" 
                               data-visit="<?= esc($p->no_visit) ?>"
                               data-is-paid="<?= $isPaid ? '1' : '0' ?>"
                               data-billing-status="<?= esc($bStatus) ?>"
                               data-payment-method="<?= esc($pMethod) ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 font-weight-bold text-teal"><?= esc($p->patient_name) ?></h6>
                                    <small class="badge badge-secondary"><?= esc($p->no_rm) ?></small>
                                </div>
                                <p class="mb-1 text-sm text-secondary">Dokter: <strong><?= esc($p->doctor_name) ?></strong></p>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-secondary"><?= esc($p->no_visit) ?></small>
                                    <div>
                                        <a href="<?= base_url('apotek/cetak-etiket/' . $p->id) ?>" target="_blank" class="btn btn-outline-info btn-xs font-weight-bold mr-1" onclick="event.stopPropagation();" title="Cetak Semua E-Tiket Obat">
                                            <i class="fas fa-prescription mr-1"></i> E-Tiket
                                        </a>
                                        <?php if ($isPaid): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Lunas</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Bayar</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispense e-Prescription Workspace -->
    <div class="col-md-7">
        <div class="card card-teal shadow" id="workspace-card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold" id="workspace-title"><i class="fas fa-prescription-bottle-medical mr-1"></i> Penyiapan & Dispensing Resep</h3>
                <div class="card-tools ml-auto d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-light text-teal font-weight-bold shadow-sm mr-2" id="btn-call-pharmacy-voice" title="Panggil Pasien ke Loket Obat">
                        <i class="fas fa-volume-high mr-1"></i> Panggil ke Apotek
                    </button>
                    <a href="#" id="btn-print-etiket-direct" target="_blank" class="btn btn-sm btn-light text-teal font-weight-bold shadow-sm" style="display:none;">
                        <i class="fas fa-print mr-1"></i> Cetak E-Tiket Obat
                    </a>
                </div>
            </div>
            <form action="<?= base_url('apotek/resep') ?>" method="post" id="form-dispense-resep">
                <?= csrf_field() ?>
                <input type="hidden" name="prescription_id" id="modal-prescription-id">
                <div class="card-body">
                    <!-- Status Banner Tagihan Kasir (Live-Sync Alert) -->
                    <div id="box-payment-status" class="mb-3"></div>

                    <div class="row border-bottom pb-2 mb-3 text-sm bg-light p-2 rounded">
                        <div class="col-md-6">
                            <strong>Pasien:</strong> <span id="lbl-patient" class="text-teal font-weight-bold"></span><br>
                            <strong>No RM:</strong> <span id="lbl-rm"></span>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <strong>Dokter Pemeriksa:</strong> <span id="lbl-doctor" class="font-weight-bold"></span><br>
                            <strong>No. Kunjungan:</strong> <span id="lbl-visit"></span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold text-teal mb-0"><i class="fas fa-pills"></i> Rincian Obat & Opsi Penyerahan</h6>
                        <small class="text-muted"><i class="fas fa-info-circle"></i> Stok kosong otomatis disarankan Beli di Luar</small>
                    </div>
                    
                    <!-- Container for dynamic prescription medicines -->
                    <div id="medicines-container"></div>
                </div>
                <div class="card-footer">
                    <button type="submit" id="btn-submit-dispense" class="btn btn-teal btn-block font-weight-bold btn-lg shadow-sm">
                        <i class="fas fa-check-double mr-1"></i> Selesai Dispensing & Serahkan Obat
                    </button>
                    <div id="lbl-btn-help-text" class="text-center text-xs text-muted mt-2"></div>
                </div>
            </form>
        </div>

        <!-- Welcome Banner when no prescription is selected -->
        <div class="card bg-light p-5 text-center shadow-sm" id="welcome-pane">
            <h1 class="display-4 text-teal"><i class="fas fa-file-prescription"></i></h1>
            <h3 class="text-dark font-weight-bold mt-3">Apotek Dispensing Pane</h3>
            <p class="text-secondary">Pilih salah satu antrean e-resep di kolom sebelah kiri untuk mulai menyiapkan obat atau mengalihkan ke resep luar.</p>
        </div>
    </div>
</div>

<!-- Raw details data JSON mapping for script usage -->
<script>
    window.prescriptionDetails = <?= json_encode($details) ?> || {};
    
    // Global Prescription Selector for Static & Real-Time LiveSync Elements
    window.selectPrescriptionItem = function($item) {
        if (!$item || !$item.length) return;

        $('.presc-item').removeClass('active');
        $item.addClass('active');

        const id = $item.data('id') || $item.attr('data-id');
        const name = $item.data('name') || $item.attr('data-name');
        const rm = $item.data('rm') || $item.attr('data-rm');
        const doctor = $item.data('doctor') || $item.attr('data-doctor');
        const visit = $item.data('visit') || $item.attr('data-visit');

        // Ultra-resilient payment detection checking attributes, data properties, and billing status
        const attrPaid = $item.attr('data-is-paid');
        const attrStatus = String($item.attr('data-billing-status') || '').toLowerCase();
        const dataPaid = $item.data('is-paid');
        const dataStatus = String($item.data('billing-status') || '').toLowerCase();
        const paymentMethod = $item.attr('data-payment-method') || $item.data('payment-method') || '';

        const isPaid = (
            attrPaid === '1' || 
            attrPaid === 1 || 
            attrPaid === 'true' || 
            attrPaid === true || 
            attrStatus === 'paid' || 
            dataPaid === 1 || 
            dataPaid === '1' || 
            dataPaid === true || 
            dataPaid === 'true' || 
            dataStatus === 'paid'
        );

        $('#welcome-pane').hide();
        $('#workspace-card').show();
        $('#modal-prescription-id').val(id);
        
        // Labels
        $('#lbl-patient').text(name || 'Pasien');
        $('#lbl-rm').text(rm || '-');
        $('#lbl-doctor').text(doctor || 'Dokter Pemeriksa');
        $('#lbl-visit').text(visit || '-');
        $('#btn-print-etiket-direct').attr('href', '<?= base_url('apotek/cetak-etiket') ?>/' + id).show();

        // Payment status alert box & button locking
        if (isPaid) {
            $('#box-payment-status').html(
                '<div class="alert alert-success d-flex align-items-center mb-0 py-2 shadow-sm border-0">' +
                '<i class="fas fa-check-circle fa-2x mr-3 text-success"></i>' +
                '<div>' +
                '<div class="font-weight-bold text-success"><i class="fas fa-receipt mr-1"></i> PEMBAYARAN SUDAH LUNAS DI KASIR UTAMA</div>' +
                '<div class="text-sm text-dark">Pasien telah menyelesaikan pembayaran tagihan' + (paymentMethod ? ' (Metode: ' + paymentMethod.toUpperCase() + ')' : '') + '. Obat siap disiapkan, dipotong stok batch, dan diserahkan.</div>' +
                '</div>' +
                '</div>'
            );
            $('#btn-submit-dispense')
                .prop('disabled', false)
                .removeAttr('disabled')
                .removeClass('btn-secondary cursor-not-allowed')
                .addClass('btn-teal')
                .html('<i class="fas fa-check-double mr-1"></i> Selesai Dispensing & Serahkan Obat');
            $('#lbl-btn-help-text').html('<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Pasien lunas. Klik tombol di atas untuk menyelesaikan penyerahan obat & memotong stok FEFO.</span>');
        } else {
            $('#box-payment-status').html(
                '<div class="alert alert-warning d-flex align-items-center mb-0 py-2 shadow-sm border-0 bg-warning text-dark">' +
                '<i class="fas fa-lock fa-2x mr-3 text-dark"></i>' +
                '<div>' +
                '<div class="font-weight-bold text-dark"><i class="fas fa-exclamation-triangle mr-1"></i> MENUNGGU PELUNASAN KASIR UTAMA (OBAT TERKUNCI)</div>' +
                '<div class="text-sm text-dark">Pasien <strong>belum membayar tagihan</strong> di Kasir Utama. Apoteker dapat menyiapkan racikan terlebih dahulu, namun tombol penyerahan obat terkunci sampai kasir melunasi tagihan.</div>' +
                '</div>' +
                '</div>'
            );
            $('#btn-submit-dispense')
                .prop('disabled', true)
                .attr('disabled', 'disabled')
                .removeClass('btn-teal')
                .addClass('btn-secondary cursor-not-allowed')
                .html('<i class="fas fa-lock mr-1"></i> Menunggu Pembayaran di Kasir (Terkunci)');
            $('#lbl-btn-help-text').html('<span class="text-danger"><i class="fas fa-info-circle mr-1"></i> Tombol penyerahan obat akan terbuka otomatis secara real-time begitu pasien melunasi tagihan di Kasir Utama.</span>');
        }

        // Helper to render medicine rows
        function renderMedicineRows(items) {
            if (!items || items.length === 0) {
                $('#medicines-container').html('<div class="alert alert-light border text-center text-muted py-3">Tidak ada rincian item obat pada resep ini.</div>');
                return;
            }

            let html = '';
            items.forEach(function(item) {
                const hasStock = item.batches && item.batches.length > 0;
                
                // Batch options dengan standar FEFO (First Expired, First Out)
                let batchOptions = '';
                if (hasStock) {
                    item.batches.forEach(function(b, idx) {
                        const isSelected = idx === 0 ? 'selected' : '';
                        const fefoBadge = idx === 0 ? ' ⭐ [Prioritas FEFO - Terdekat ED]' : '';
                        batchOptions += `<option value="${b.id}" ${isSelected}>Batch: ${b.batch_no} (Stok: ${b.stock} | Exp: ${b.expired_date})${fefoBadge}</option>`;
                    });
                }

                html += `
                    <div class="card ${hasStock ? 'card-light' : 'border-warning'} mb-3 shadow-sm">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <h6 class="font-weight-bold text-dark mb-1">${item.medicine_name || 'Obat'}</h6>
                                    <div class="text-sm text-secondary">Aturan Pakai: <strong>${item.dosage || item.dosage_rule || '-'}</strong></div>
                                    <div class="text-sm text-secondary">Jumlah Resep: <span class="badge badge-teal">${item.qty} ${item.unit || 'item'}</span></div>
                                    <input type="hidden" name="dispense[${item.medicine_id}][qty]" value="${item.qty}">
                                    
                                    ${!hasStock ? '<div class="mt-2"><span class="badge badge-warning text-dark"><i class="fas fa-exclamation-circle"></i> Stok internal kosong</span></div>' : ''}
                                </div>
                                <div class="col-md-7">
                                    <label class="text-xs text-secondary mb-1 font-weight-bold">Status Penyerahan Item Ini:</label>
                                    <div class="form-group mb-2">
                                        <select name="dispense[${item.medicine_id}][action]" class="form-control form-control-sm select-action" data-medid="${item.medicine_id}">
                                            ${hasStock ? '<option value="internal" selected>&#9989; Diserahkan dari Apotek Internal</option>' : ''}
                                            <option value="beli_luar" ${!hasStock ? 'selected' : ''}>&#128221; Beli di Luar (Rekomendasi / Resep Luar)</option>
                                            <option value="cancel">&#10060; Dibatalkan (Pasien Tidak Mengambil)</option>
                                        </select>
                                    </div>

                                    <div id="batch-box-${item.medicine_id}" style="${hasStock ? 'display:block;' : 'display:none;'}">
                                        <label class="text-xs text-muted mb-1">Pilih Batch FEFO (Potong Stok):</label>
                                        <select name="dispense[${item.medicine_id}][batch_id]" class="form-control form-control-sm mb-2">
                                            ${batchOptions}
                                        </select>
                                        <div class="form-group mb-0">
                                            <label class="text-xs text-dark font-weight-bold mb-1"><i class="fas fa-tag text-teal mr-1"></i> Potongan Diskon Obat (Rp):</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold">Rp</span>
                                                </div>
                                                <input type="number" step="100" min="0" name="dispense[${item.medicine_id}][discount]" class="form-control form-control-sm" placeholder="0" value="0">
                                            </div>
                                            <small class="text-muted text-xs">Potongan diskon per-item obat.</small>
                                        </div>
                                    </div>

                                    <div id="luar-box-${item.medicine_id}" class="alert alert-warning py-1 px-2 mb-0 text-xs" style="${!hasStock ? 'display:block;' : 'display:none;'}">
                                        <i class="fas fa-info-circle"></i> Pasien disarankan beli di luar.
                                    </div>

                                    <div id="cancel-box-${item.medicine_id}" class="alert alert-secondary py-1 px-2 mb-0 text-xs" style="display:none;">
                                        <i class="fas fa-ban"></i> Item dibatalkan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            $('#medicines-container').html(html);
        }

        const items = (window.prescriptionDetails && window.prescriptionDetails[id]) ? window.prescriptionDetails[id] : [];
        renderMedicineRows(items);
    };

    $(document).ready(function() {
        // Delegated click for static and live-synced prescription items
        $(document).on('click', '.presc-item', function(e) {
            e.preventDefault();
            window.selectPrescriptionItem($(this));
        });

        // Action changer handler (delegated)
        $(document).on('change', '.select-action', function() {
            const medId = $(this).data('medid');
            const action = $(this).val();

            if (action === 'internal') {
                $(`#batch-box-${medId}`).slideDown(150);
                $(`#luar-box-${medId}`).slideUp(150);
                $(`#cancel-box-${medId}`).slideUp(150);
            } else if (action === 'beli_luar') {
                $(`#batch-box-${medId}`).slideUp(150);
                $(`#luar-box-${medId}`).slideDown(150);
                $(`#cancel-box-${medId}`).slideUp(150);
            } else if (action === 'cancel') {
                $(`#batch-box-${medId}`).slideUp(150);
                $(`#luar-box-${medId}`).slideUp(150);
                $(`#cancel-box-${medId}`).slideDown(150);
            }
        });

        // Event Tombol Panggil Suara Apotek
        $('#btn-call-pharmacy-voice').on('click', function(e) {
            e.preventDefault();
            const pName = $('#lbl-patient').text().trim() || 'Pasien';
            const qNo = $('#lbl-visit').text().trim() || 'A-001';

            if (window.VCM && typeof window.VCM.triggerServerCall === 'function') {
                window.VCM.triggerServerCall({
                    service_type: 'farmasi',
                    counter_name: 'Loket Farmasi dan Apotek',
                    queue_number: qNo,
                    patient_name: pName,
                    call_action: 'call',
                    call_priority: 1
                }, function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> dipanggil ke Loket Apotek.', '📢 Panggilan Farmasi');
                    }
                });
            } else if (typeof callPatientToPharmacy === 'function') {
                callPatientToPharmacy(qNo, pName);
            }
        });

        // Trigger Panggilan Suara Penyerahan Obat Selesai & Ucapan Terima Kasih
        <?php 
            $vTrigger = session()->getFlashdata('voice_trigger');
            if (!empty($vTrigger) && is_array($vTrigger)): 
                $qNumber = $vTrigger['queue_number'] ?? 'A-001';
                $pName   = $vTrigger['patient_name'] ?? 'Pasien';
                if (($vTrigger['action'] ?? '') === 'to_completed'):
        ?>
            setTimeout(function() {
                const qNo = <?= json_encode($qNumber) ?>;
                const pName = <?= json_encode($pName) ?>;

                function executeVoiceCall() {
                    try {
                        if (window.VCM) {
                            window.VCM.unlockAudio();
                            window.VCM.callCompleted(qNo, pName);
                        } else if (typeof callPatientCompleted === 'function') {
                            callPatientCompleted(qNo, pName);
                        } else if ('speechSynthesis' in window) {
                            if (window.speechSynthesis.paused) {
                                window.speechSynthesis.resume();
                            }
                            const spokenQ = qNo.replace(/-/g, ' ');
                            const speechText = 'Nomor antrean ' + spokenQ + ', atas nama ' + pName + ', penyerahan obat telah selesai. Terima kasih banyak atas kunjungan Anda di Sawamawa Medical Center, semoga lekas sembuh.';
                            const utt = new SpeechSynthesisUtterance(speechText);
                            utt.lang = 'id-ID';
                            utt.rate = 0.85;
                            window.speechSynthesis.speak(utt);
                        }
                    } catch(e) {
                        console.warn('[Apotek Voice] Error triggering completed call:', e);
                    }
                }

                executeVoiceCall();

                // Visual Toastr Notification with Re-Play Button
                if (typeof toastr !== 'undefined') {
                    toastr.success(
                        '📢 <strong>Panggilan Selesai:</strong> Nomor antrean <strong>' + qNo + ' (' + pName + ')</strong> telah dipanggil dengan ucapan terima kasih.<br><button class="btn btn-xs btn-light mt-1 font-weight-bold" id="btn-replay-finish-voice"><i class="fas fa-volume-high mr-1"></i> Putar Ulang Panggilan</button>',
                        'Penyerahan Obat Selesai',
                        { timeOut: 10000, closeButton: true }
                    );

                    $(document).on('click', '#btn-replay-finish-voice', function(e) {
                        e.preventDefault();
                        executeVoiceCall();
                    });
                }
            }, 300);
        <?php endif; endif; ?>
    });
</script>
<?= $this->endSection() ?>

