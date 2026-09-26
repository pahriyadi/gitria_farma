/**
 * ⚡ UNIVERSAL ZERO-RELOAD REAL-TIME LIVE-SYNC ENGINE
 * Sawamawa Medical Center & Resto Gizi ERP
 * Otomatis memperbarui antrean pasien klinik, SOAP dokter, e-resep farmasi, kasir & billing, laboratorium, KDS resto, dan absensi tanpa reload!
 */

(function ($) {
    'use strict';

    function getBaseUrl() {
        if (typeof BASE_URL !== 'undefined' && BASE_URL) {
            return BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
        }
        var m = window.location.pathname.match(/^(\/[^\/]+\/)/);
        var base = window.location.origin + (m ? m[1] : '/');
        return base.endsWith('/') ? base : base + '/';
    }

    function esc(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    window.LiveSyncEngine = {
        interval: 2500, // Polling interval 2.5 detik
        timer: null,
        lastPayloads: {},
        isPollingActive: true,
        baseUrl: getBaseUrl(),

        init: function () {
            var self = this;
            console.log('⚡ [LiveSync Engine] Inisialisasi aktif pada Base URL:', self.baseUrl);

            // Pasang visibility listener (hemat daya saat tab diminimalkan)
            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    self.isPollingActive = false;
                } else {
                    self.isPollingActive = true;
                    self.pollNow();
                }
            });

            self.startLoop();
            self.pollNow();
        },

        startLoop: function () {
            var self = this;
            if (self.timer) clearInterval(self.timer);

            self.timer = setInterval(function () {
                if (self.isPollingActive) {
                    self.pollNow();
                }
            }, self.interval);
        },

        pollNow: function () {
            // Guard: Jangan polling jika sistem sedang offline (hemat resource & cegah error cascade)
            if (window.offlineSyncEngine && !window.offlineSyncEngine.isOnline) return;
            if (!navigator.onLine) return;

            var path = window.location.pathname.toLowerCase();

            // 1. Modul Klinik: Antrean Poli, Pendaftaran & Rekam Medis SOAP
            if (path.includes('klinik/antrean') || path.includes('klinik/soap') || path.includes('klinik/rekam-medis') || path.includes('klinik/pendaftaran') || $('#live-antrean-body').length > 0 || $('#queue-list').length > 0 || $('#soap-patient-queue-list').length > 0 || $('#live-pendaftaran-antrean-body').length > 0) {
                this.syncKlinikQueue();
            }

            // 2. Modul Farmasi & e-Resep Apotek
            if (path.includes('apotek/resep') || $('#presc-list').length > 0) {
                this.syncPharmacyPrescriptions();
            }

            // 3. Modul Kasir & Billing Pembayaran
            if (path.includes('keuangan/kasir') || $('#bill-list').length > 0) {
                this.syncCashierBillings();
            }

            // 4. Modul Laboratorium Klinik
            if (path.includes('klinik/lab') || $('#live-lab-results-body').length > 0) {
                this.syncLabQueue();
            }

            // 5. Modul Resto: Dapur Gizi KDS
            if (path.includes('resto/dapur') || $('#kds-orders-container').length > 0) {
                this.syncRestoKitchen();
            }

            // 6. Modul HRD: Presensi & Absensi Pegawai
            if (path.includes('hrd/absensi') || $('#live-attendance-table').length > 0) {
                this.syncHrdPresence();
            }
        },

        /**
         * 1. SINKRONISASI KLINIK: DAFTAR ANTREAN KUNJUNGAN & SOAP DOKTER
         */
        syncKlinikQueue: function () {
            var self = this;
            var docFilter = $('#filter-doctor-select').val() || window.activeDoctorFilter || '';
            var polyFilter = $('#filter-poly-select').val() || window.activePolyFilter || '';
            var queryParams = [];
            if (docFilter) queryParams.push('doctor_id=' + encodeURIComponent(docFilter));
            if (polyFilter) queryParams.push('poly_id=' + encodeURIComponent(polyFilter));
            var queryString = queryParams.length > 0 ? ('?' + queryParams.join('&')) : '';

            $.ajax({
                url: self.baseUrl + 'api/sync/klinik-queue' + queryString,
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (window.offlineSyncEngine && typeof window.offlineSyncEngine.reportNetworkSuccess === 'function') {
                        window.offlineSyncEngine.reportNetworkSuccess();
                    }

                    if (res && res.status === 'success') {
                        // Jika antrean kosong dari database server, bersihkan cache lokal offlineSyncEngine
                        if (window.offlineSyncEngine && Array.isArray(res.data) && res.data.length === 0) {
                            window.offlineSyncEngine.queuesCache = [];
                            try {
                                localStorage.removeItem('sw_queues_cache');
                            } catch (e) {}
                        }

                        var hash = JSON.stringify(res.stats) + '_' + (res.data ? res.data.map(function (d) {
                            return (d.visit_id || d.id) + ':' + (d.status || '') + ':' + (d.has_triage ? '1' : '0') + ':' +
                                (d.arrival_seq || '') + ':' + (d.doctor_id || '') + ':' + (d.wait_minutes || '') + ':' +
                                (d.blood_pressure || '') + ':' + (d.temperature || '') + ':' + (d.weight || '');
                        }).join('|') : '');
                        
                        if (self.lastPayloads['klinik'] !== hash) {
                            self.lastPayloads['klinik'] = hash;
                            self.renderAntreanTable(res.data, res.stats);
                            self.renderSoapSidebar(res.data);
                            self.renderPendaftaranSidebar(res.data, res.stats);
                        }
                    }
                },
                error: function (jqXHR, textStatus) {
                    if (jqXHR && (jqXHR.status === 0 || textStatus === 'timeout')) {
                        if (window.offlineSyncEngine && typeof window.offlineSyncEngine.reportNetworkFailure === 'function') {
                            window.offlineSyncEngine.reportNetworkFailure();
                        }
                    }
                }
            });
        },

        renderAntreanTable: function (data, stats) {
            var self = this;
            var $body = $('#live-antrean-body');
            if (!$body.length) return;

            if (stats) {
                $('#stat-total').text(stats.total || 0);
                $('#stat-active').text(stats.active || 0);
                $('#stat-waiting').text(stats.waiting || 0);
                $('#stat-called').text((stats.called || 0) + (stats.triage || 0));
                $('#stat-done').text(stats.completed || 0);
            }

            if (!data || data.length === 0) {
                $body.html(
                    '<tr><td colspan="9" class="text-center text-muted py-5">' +
                    '<i class="fas fa-ticket text-teal mr-2"></i>' +
                    'Tidak ada antrean pasien untuk filter ini.</td></tr>'
                );
                return;
            }

            var csrfName = $('meta[name="csrf-name"]').attr('content') || '';
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
            var csrfInput = csrfName ? ('<input type="hidden" name="' + csrfName + '" value="' + csrfToken + '">') : '';

            var rows = '';
            $.each(data, function (i, q) {
                var tier = (q.membership_tier || 'regular').toLowerCase();
                var rowClass = (tier === 'vip' || tier === 'platinum') ? 'table-warning' : (tier === 'gold' ? 'table-light' : '');

                var arrSeqBadge = '<span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size:12px;" title="Urutan Pendaftar #' + (q.arrival_seq || (i+1)) + '"><i class="fas fa-arrow-down-1-9 mr-1"></i>#' + (q.arrival_seq || (i+1)) + '</span>';
                var queueBadge = '<span class="badge badge-teal h4 py-2 px-3 font-weight-bold" style="font-size: 16px;">' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                var tierBadge = '';
                if (tier === 'vip' || tier === 'platinum') {
                    queueBadge = '<span class="badge badge-dark h4 py-2 px-3 font-weight-bold shadow-sm" style="font-size: 16px; background:#0f172a; border: 1.5px solid #ffc107; color:#ffc107;"><i class="fas fa-gem mr-1"></i>' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                    tierBadge = '<span class="badge badge-dark ml-1 font-weight-bold shadow-sm" style="background:#0f172a; border: 1px solid #ffc107; color: #ffc107; font-size:10px;"><i class="fas fa-gem mr-1"></i>VIP</span>';
                } else if (tier === 'gold') {
                    queueBadge = '<span class="badge badge-warning text-dark h4 py-2 px-3 font-weight-bold shadow-sm" style="font-size: 16px; background:#ffc107;"><i class="fas fa-crown mr-1"></i>' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                    tierBadge = '<span class="badge badge-warning text-dark ml-1 font-weight-bold shadow-sm" style="background:#ffc107; font-size:10px;"><i class="fas fa-crown mr-1"></i>Gold</span>';
                }

                var status = (q.status || 'waiting').toLowerCase();
                var badgeCls = {
                    waiting: 'secondary', called: 'primary', triage: 'primary',
                    examining: 'success', prescription: 'info', cashier: 'warning',
                    completed: 'success', cancelled: 'danger', 'no-show': 'warning'
                }[status] || 'secondary';

                var visitType = (q.visit_type || 'poli').toLowerCase();
                var svcLabel = visitType === 'tindakan'
                    ? '<span class="badge badge-secondary mr-1">TINDAKAN</span>' +
                      (q.parent_service_name || q.parent_tindakan_name ? '<span class="text-teal font-weight-bold">' + esc(q.parent_service_name || q.parent_tindakan_name) + ' &raquo; </span>' : '') +
                      '<strong>' + esc(q.service_name || q.tindakan_name || 'Tindakan Medis') + '</strong>'
                    : '<span class="badge badge-info mr-1">POLI</span><strong>' + esc(q.poly_name || q.polyclinic_name || 'Poliklinik') + '</strong>';

                var targetCall = visitType === 'tindakan' ? ('Ruang Tindakan ' + (q.service_name || q.tindakan_name || 'Medis')) : ('Poliklinik ' + (q.poly_name || q.polyclinic_name || 'Umum'));
                var timeStr = q.created_at ? q.created_at.substring(11, 19) : '-';

                var hasTr = (q.has_triage == '1' || q.has_triage == 1);
                var badgeTtv = hasTr
                    ? '<span class="badge badge-success px-2 py-1 ml-1" style="font-size: 10px;"><i class="fas fa-check-circle mr-1"></i> TTV Lengkap</span>'
                    : '<span class="badge badge-warning text-dark px-2 py-1 ml-1" style="font-size: 10px;"><i class="fas fa-heartbeat mr-1"></i> Belum TTV</span>';

                var slaBadge = '<span class="badge badge-' + (q.sla_badge || 'success') + ' font-weight-bold ml-1" style="font-size:10.5px;" title="Lama Menunggu"><i class="fas fa-stopwatch mr-1"></i>' + (q.wait_minutes || 0) + ' mnt</span>';

                var actionButtons = '';
                // 1. Voice Call Button
                actionButtons +=
                    '<button type="button" class="btn btn-warning btn-sm font-weight-bold shadow-sm mr-1 btn-voice-call" ' +
                    'data-queue="' + esc(q.queue_number || q.queue_no || '') + '" ' +
                    'data-name="' + esc(q.patient_name) + '" ' +
                    'data-target="' + esc(targetCall) + '" ' +
                    'data-doctor="' + esc(q.doctor_name || 'Dokter Jaga') + '" ' +
                    'title="Panggil Antrean dengan Suara Audio">' +
                    '<i class="fas fa-volume-up"></i> Panggil Suara</button>';

                // 2. Reassign Doctor / Time Keeper Switch Button
                actionButtons +=
                    '<button type="button" class="btn btn-outline-info btn-sm font-weight-bold shadow-sm mr-1 btn-reassign-doc" ' +
                    'data-visitid="' + esc(q.visit_id || q.id) + '" ' +
                    'data-name="' + esc(q.patient_name) + '" ' +
                    'data-doctorid="' + esc(q.doctor_id || '') + '" ' +
                    'title="Alihkan / Ganti Dokter Pemeriksa"><i class="fas fa-user-doctor"></i></button>';

                // 3. Status Transition Form
                if (status === 'waiting') {
                    actionButtons +=
                        '<form action="' + self.baseUrl + 'klinik/antrean" method="post" class="d-inline-block mr-1">' +
                        csrfInput +
                        '<input type="hidden" name="queue_id" value="' + esc(q.queue_id || q.id) + '">' +
                        '<input type="hidden" name="status" value="called">' +
                        '<button type="submit" class="btn btn-primary btn-sm font-weight-bold shadow-sm btn-status-call" ' +
                        'data-queue="' + esc(q.queue_number || q.queue_no || '') + '" ' +
                        'data-name="' + esc(q.patient_name) + '" ' +
                        'data-target="' + esc(targetCall) + '" ' +
                        'data-doctor="' + esc(q.doctor_name || 'Dokter Jaga') + '">' +
                        '<i class="fas fa-bullhorn mr-1"></i> Mulai</button>' +
                        '</form>';
                } else if (status === 'called' || status === 'triage') {
                    actionButtons +=
                        '<form action="' + self.baseUrl + 'klinik/antrean" method="post" class="d-inline-block mr-1">' +
                        csrfInput +
                        '<input type="hidden" name="queue_id" value="' + esc(q.queue_id || q.id) + '">' +
                        '<input type="hidden" name="status" value="completed">' +
                        '<button type="submit" class="btn btn-success btn-sm font-weight-bold shadow-sm">' +
                        '<i class="fas fa-check mr-1"></i> Selesai Perawat</button>' +
                        '</form>';
                } else {
                    actionButtons += '<span class="text-secondary font-weight-bold mr-2" style="font-size: 12px;"><i class="fas fa-lock"></i> Selesai Diproses</span>';
                }

                // 4. Delete Queue Button
                actionButtons +=
                    '<a href="' + self.baseUrl + 'klinik/antrean/delete/' + esc(q.queue_id || q.id) + '" ' +
                    'class="btn btn-outline-danger btn-sm font-weight-bold shadow-sm ml-1" ' +
                    'onclick="return confirm(\'Batalkan antrean pasien ' + esc(q.patient_name) + ' (No. ' + esc(q.queue_number || q.queue_no || '') + ')?\');" ' +
                    'title="Hapus / Batalkan Antrean">' +
                    '<i class="fas fa-trash"></i></a>';

                rows +=
                    '<tr class="' + rowClass + '">' +
                    '<td class="text-center align-middle">' + arrSeqBadge + queueBadge + '</td>' +
                    '<td class="align-middle"><strong>' + esc(q.no_visit || '-') + '</strong></td>' +
                    '<td class="align-middle"><strong>' + esc(q.patient_name) + '</strong>' + tierBadge + badgeTtv + '</td>' +
                    '<td class="align-middle">' + svcLabel + '</td>' +
                    '<td class="align-middle">' + (q.doctor_name ? ('<i class="fas fa-user-md text-teal mr-1"></i> ' + esc(q.doctor_name)) : '<span class="text-muted">-</span>') + '</td>' +
                    '<td class="text-center align-middle"><span class="badge badge-' + badgeCls + ' py-1 px-2 font-weight-bold">' + status.toUpperCase() + '</span></td>' +
                    '<td class="align-middle text-nowrap">' + timeStr + '<br>' + slaBadge + '</td>' +
                    '<td class="text-center text-nowrap align-middle">' + actionButtons + '</td>' +
                    '</tr>';
            });

            $body.html(rows);
        },

        renderSoapSidebar: function (data) {
            var self = this;
            var $soapList = $('#queue-list').length ? $('#queue-list') : $('#soap-patient-queue-list');
            if (!$soapList.length || !data) return;

            // Filter antrean aktif untuk dokter: status waiting, called, calling, triage, examining
            var activeVisits = data.filter(function (q) {
                var st = (q.status || 'waiting').toLowerCase();
                var vSt = (q.visit_status || '').toLowerCase();
                var excluded = ['cashier', 'prescription', 'pharmacy', 'completed', 'cancelled', 'done', 'finished', 'closed'];
                if (excluded.indexOf(vSt) !== -1 || excluded.indexOf(st) !== -1) {
                    return false;
                }
                return true;
            });

            // Update badge count di header panel jika ada
            var $badge = $('#lbl-total-queue').length ? $('#lbl-total-queue') : $soapList.closest('.card').find('.badge-teal');
            if ($badge.length) {
                $badge.text(activeVisits.length + ' Pasien');
            }

            if (activeVisits.length === 0) {
                $soapList.html(
                    '<div class="text-center text-muted p-5">' +
                    '<i class="fas fa-user-clock fa-2x mb-2 text-secondary"></i>' +
                    '<p class="mb-0 text-sm">Tidak ada antrean konsultasi medis aktif untuk filter ini.</p>' +
                    '</div>'
                );
                return;
            }

            var currentActiveId = $('.visit-item.active').data('id') || $('.input-visit-id').val();

            var html = '';
            $.each(activeVisits, function (i, v) {
                var vId = v.visit_id || v.id;
                var isActive = (currentActiveId && currentActiveId == vId) ? ' active' : '';
                var isTindakan = (v.visit_type === 'tindakan');

                // Cache TTV data into window.triagesData dynamically
                if (typeof window.triagesData !== 'undefined' && v.has_triage) {
                    window.triagesData[vId] = {
                        blood_pressure: v.blood_pressure || '',
                        pulse: v.pulse || '',
                        temperature: v.temperature || '',
                        respiration: v.respiration || '',
                        weight: v.weight || '',
                        height: v.height || '',
                        complaints: v.complaints || '',
                        anamnesis: v.anamnesis || '',
                        nurse_notes: v.nurse_notes || ''
                    };

                    if (currentActiveId && currentActiveId == vId) {
                        if (typeof window.updateLiveSoapPanelsFromTriage === 'function') {
                            window.updateLiveSoapPanelsFromTriage(window.triagesData[vId]);
                        }
                    }
                }

                var vStatus = (v.status || v.visit_status || 'waiting').toLowerCase();
                var statusBadges = {
                    waiting: ['secondary', 'MENUNGGU'],
                    called: ['primary', 'DIPANGGIL'],
                    calling: ['primary', 'DIPANGGIL'],
                    examining: ['info', 'DIPERIKSA'],
                    triage: ['warning', 'TRIAGE']
                };
                var vBadge = statusBadges[vStatus] || ['secondary', vStatus.toUpperCase()];

                var arrSeqBadge = '<span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size:9px; padding: 2px 4px;" title="Urutan Pendaftar #' + (v.arrival_seq || (i+1)) + '">#' + (v.arrival_seq || (i+1)) + '</span>';
                var badgeLayanan = isTindakan
                    ? '<span class="badge badge-info py-0 px-1 font-weight-normal" style="font-size:9px;"><i class="fas fa-syringe mr-1"></i>' + esc(v.service_name || 'Tindakan') + '</span>'
                    : '<span class="badge badge-light border text-teal py-0 px-1 font-weight-bold" style="font-size:9px;"><i class="fas fa-stethoscope mr-1"></i>' + esc(v.poly_name || 'Umum') + '</span>';
                
                var hasTr = (v.has_triage == '1' || v.has_triage == 1);
                var badgeTtv = hasTr
                    ? '<span class="badge badge-success py-0 px-1" style="font-size: 8.5px;"><i class="fas fa-check mr-1"></i>TTV Ok</span>'
                    : '<span class="badge badge-warning text-dark py-0 px-1" style="font-size: 8.5px;"><i class="fas fa-heartbeat mr-1"></i>Belum TTV</span>';

                var doctorName = v.doctor_name || 'Dokter Jaga';

                html +=
                    '<div class="visit-item' + isActive + '" ' +
                    'data-id="' + esc(vId) + '" ' +
                    'data-name="' + esc(v.patient_name) + '" ' +
                    'data-rm="' + esc(v.no_rm) + '" ' +
                    'data-poly="' + esc(v.poly_name || '-') + '" ' +
                    'data-tindakan="' + esc(v.service_name || '-') + '" ' +
                    'data-parent="' + esc(v.parent_service_name || '') + '" ' +
                    'data-doctor="' + esc(doctorName) + '" ' +
                    'data-patientid="' + esc(v.patient_id || '') + '" ' +
                    'data-queue="' + esc(v.queue_number || '') + '" ' +
                    'data-hastriage="' + (hasTr ? '1' : '0') + '">' +
                    '<div class="d-flex w-100 justify-content-between align-items-center mb-1">' +
                    '<div class="d-flex align-items-center text-truncate mr-1">' +
                    arrSeqBadge +
                    '<span class="badge badge-teal mr-1 font-weight-bold text-white" style="font-size:10px; padding: 2px 5px;">' + esc(v.queue_number || '-') + '</span>' +
                    '<strong class="text-dark patient-name-text text-truncate" style="max-width: 135px; font-size: 12px;" title="' + esc(v.patient_name) + '">' + esc(v.patient_name) + '</strong>' +
                    '</div>' +
                    '<span class="badge badge-' + vBadge[0] + ' font-weight-bold" style="font-size: 8.5px; padding: 2px 4px;">' + vBadge[1] + '</span>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center text-xs text-secondary mt-1">' +
                    '<div class="text-truncate mr-1" style="max-width: 140px;">' + badgeLayanan + '<span class="text-muted ml-1" style="font-size: 9.5px;">' + esc(v.no_rm || '') + '</span></div>' +
                    '<div>' + badgeTtv + '</div>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="border-top-color: #f1f5f9 !important;">' +
                    '<small class="text-muted" style="font-size: 10px;"><i class="fas fa-user-doctor text-secondary mr-1"></i>' + esc(doctorName) + '</small>' +
                    '<button type="button" class="btn btn-outline-danger btn-xs py-0 px-2 font-weight-bold btn-sidebar-cancel-queue" data-id="' + esc(vId) + '" data-name="' + esc(v.patient_name) + '" data-queue="' + esc(v.queue_number || '') + '" title="Batalkan antrean konsultasi" style="font-size: 10.5px; height: 22px; line-height: 20px; border-radius: 4px;">' +
                    '<i class="fas fa-times mr-1"></i> Batal</button>' +
                    '</div>' +
                    '</div>';
            });

            $soapList.html(html);
        },

        renderPendaftaranSidebar: function (data, stats) {
            var self = this;
            var $body = $('#live-pendaftaran-antrean-body');
            if (!$body.length || !data) return;

            var activeData = (data || []).filter(function(q) {
                var s = (q.status || '').toLowerCase();
                return s !== 'cancelled' && s !== 'completed' && s !== 'prescription' && s !== 'cashier';
            });

            var $countBadge = $('#sidebar-queue-count');
            if ($countBadge.length) {
                $countBadge.text(activeData.length + ' Pasien');
            }

            if (activeData.length === 0) {
                $body.html(
                    '<div class="text-center text-muted p-4" id="empty-queue-placeholder">' +
                    '<i class="fas fa-ticket-alt fa-2x mb-2 text-secondary"></i>' +
                    '<p class="mb-0 text-sm">Belum ada antrean pasien terdaftar hari ini.</p>' +
                    '</div>'
                );
                return;
            }

            var html = '';
            $.each(activeData, function (i, q) {
                var tier = (q.membership_tier || 'regular').toLowerCase();
                var arrSeqBadge = '<span class="badge badge-warning text-dark font-weight-bold mr-1" style="font-size:11px;"><i class="fas fa-arrow-down-1-9 mr-1"></i>#' + (q.arrival_seq || (i+1)) + '</span>';
                var queueBadge = '<span class="badge badge-teal py-1 px-2 font-weight-bold mr-2" style="font-size: 13px;">' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                if (tier === 'vip' || tier === 'platinum') {
                    queueBadge = '<span class="badge badge-dark py-1 px-2 font-weight-bold shadow-sm mr-2" style="font-size: 13px; background:#0f172a; border: 1px solid #ffc107; color:#ffc107;"><i class="fas fa-gem mr-1"></i>' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                } else if (tier === 'gold') {
                    queueBadge = '<span class="badge badge-warning text-dark py-1 px-2 font-weight-bold shadow-sm mr-2" style="font-size: 13px; background:#ffc107;"><i class="fas fa-crown mr-1"></i>' + esc(q.queue_number || q.queue_no || '-') + '</span>';
                }

                var status = (q.status || 'waiting').toLowerCase();
                var badgeCls = {
                    waiting: 'secondary', called: 'primary', triage: 'primary',
                    examining: 'success', prescription: 'info', cashier: 'warning',
                    completed: 'success', cancelled: 'danger', 'no-show': 'warning'
                }[status] || 'secondary';

                var visitType = (q.visit_type || 'poli').toLowerCase();
                var svcName = (visitType === 'tindakan') ? (q.service_name || q.tindakan_name || 'Tindakan') : (q.polyclinic_name || q.poly_name || 'Poli');
                var targetName = (visitType === 'tindakan') ? ('Ruang Tindakan ' + svcName) : ('Poliklinik ' + svcName);
                var timeStr = q.created_at ? q.created_at.substring(11, 16) : '-';
                var searchKey = (q.patient_name || '') + ' ' + (q.no_rm || '') + ' ' + (q.queue_number || q.queue_no || '');

                html +=
                    '<div class="list-group-item p-3 border-bottom sidebar-queue-item" data-search="' + esc(searchKey.toLowerCase()) + '">' +
                    '<div class="d-flex justify-content-between align-items-center mb-1">' +
                    '<div class="d-flex align-items-center">' +
                    queueBadge +
                    '<strong class="text-dark queue-patient-name text-truncate" style="max-width: 140px; font-size: 13.5px;">' + esc(q.patient_name) + '</strong>' +
                    '</div>' +
                    '<span class="badge badge-' + badgeCls + ' font-weight-bold py-1 px-2" style="font-size: 10px;">' + status.toUpperCase() + '</span>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center text-xs text-secondary mt-1">' +
                    '<div><i class="fas fa-clinic-medical text-teal mr-1"></i><strong>' + esc(svcName) + '</strong></div>' +
                    '<span class="text-muted"><i class="fas fa-clock mr-1"></i>' + timeStr + '</span>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">' +
                    '<small class="text-muted text-truncate" style="max-width: 140px;"><i class="fas fa-user-md mr-1 text-teal"></i>' + esc(q.doctor_name || 'Dokter Jaga') + '</small>' +
                    '<div class="d-flex align-items-center" style="gap: 3px;">' +
                    '<button type="button" class="btn btn-warning btn-xs font-weight-bold shadow-sm btn-voice-call" ' +
                    'data-queue="' + esc(q.queue_number || q.queue_no || '') + '" ' +
                    'data-name="' + esc(q.patient_name) + '" ' +
                    'data-target="' + esc(targetName) + '" ' +
                    'data-doctor="' + esc(q.doctor_name || 'Dokter Jaga') + '" ' +
                    'title="Panggil Suara Pasien"><i class="fas fa-volume-up mr-1"></i> Panggil</button>' +
                    '<button type="button" class="btn btn-xs btn-outline-danger font-weight-bold btn-cancel-pendaftaran-queue" ' +
                    'data-id="' + esc(q.visit_id || q.id) + '" ' +
                    'data-name="' + esc(q.patient_name) + '" ' +
                    'data-queue="' + esc(q.queue_number || q.queue_no || '') + '" ' +
                    'title="Batalkan / Hapus Antrean Pasien"><i class="fas fa-trash-alt"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
            });
            $body.html(html);
        },

        /**
         * 2. SINKRONISASI APOTEK: E-RESEP REAL-TIME & STATUS PEMBAYARAN KASIR
         */
        syncPharmacyPrescriptions: function () {
            var self = this;
            $.ajax({
                url: self.baseUrl + 'api/sync/pharmacy-prescriptions',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        var hash = (res.data ? res.data.map(function(p) {
                            return p.id + ':' + (p.status || '') + ':' + (p.is_paid || 0) + ':' + (p.billing_status || '') + ':' + (p.items ? p.items.length : 0);
                        }).join('|') : '');

                        if (self.lastPayloads['pharmacy'] !== hash) {
                            self.lastPayloads['pharmacy'] = hash;
                            self.renderPharmacyPrescriptions(res);
                        }
                    }
                }
            });
        },

        renderPharmacyPrescriptions: function (res) {
            var self = this;
            var $prescList = $('#presc-list');
            if ($prescList.length && res.data) {
                var currentActiveId = $('.presc-item.active').data('id');
                var prevActiveWasPaid = $('.presc-item.active').attr('data-is-paid') === '1';

                // Cache detail items into window.prescriptionDetails dynamically
                if (typeof window.prescriptionDetails === 'undefined') {
                    window.prescriptionDetails = {};
                }

                $('#lbl-presc-count').text(res.data.length + ' Resep');

                if (res.data.length === 0) {
                    $prescList.html('<div class="text-center text-muted p-4"><i class="fas fa-check-circle text-success mr-1"></i> Tidak ada antrean resep obat saat ini.</div>');
                    return;
                }

                var html = '';
                res.data.forEach(function (p) {
                    var isActive = (p.id == currentActiveId || p.id == $('#modal-prescription-id').val()) ? ' active' : '';
                    var isPaid = (p.is_paid == 1 || p.is_paid === true || p.is_paid === '1' || String(p.billing_status || '').toLowerCase() === 'paid');
                    var bStatus = p.billing_status || (isPaid ? 'paid' : 'open');
                    var pMethod = p.payment_method || '';

                    if (p.items) {
                        window.prescriptionDetails[p.id] = p.items;
                    }

                    var badgePayment = isPaid
                        ? '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Lunas di Kasir</span>'
                        : '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Bayar Kasir</span>';

                    var borderCls = isPaid ? 'border-left-success' : 'border-left-warning';

                    var printEtiketUrl = (self.baseUrl || '/') + 'apotek/cetak-etiket/' + p.id;
                    var btnEtiket = '<a href="' + printEtiketUrl + '" target="_blank" class="btn btn-outline-info btn-xs font-weight-bold mr-1" onclick="event.stopPropagation();" title="Cetak Semua E-Tiket Obat"><i class="fas fa-prescription mr-1"></i> E-Tiket</a>';

                    html +=
                        '<div class="list-group-item list-group-item-action presc-item ' + borderCls + isActive + '" ' +
                        'role="button" tabindex="0" style="cursor: pointer;" ' +
                        'data-id="' + p.id + '" ' +
                        'data-name="' + esc(p.patient_name || 'Pasien') + '" ' +
                        'data-rm="' + esc(p.no_rm || '-') + '" ' +
                        'data-doctor="' + esc(p.doctor_name || 'Dokter Pemeriksa') + '" ' +
                        'data-visit="' + esc(p.queue_number || p.no_visit) + '" ' +
                        'data-is-paid="' + (isPaid ? '1' : '0') + '" ' +
                        'data-billing-status="' + esc(bStatus) + '" ' +
                        'data-payment-method="' + esc(pMethod) + '">' +
                        '<div class="d-flex w-100 justify-content-between align-items-center mb-1">' +
                        '<h6 class="mb-0 font-weight-bold text-teal">' + esc(p.patient_name || 'Pasien') + '</h6>' +
                        '<small class="badge badge-secondary">' + esc(p.no_rm || '-') + '</small>' +
                        '</div>' +
                        '<p class="mb-1 text-sm text-secondary">Dokter: <strong>' + esc(p.doctor_name || 'Dokter Jaga') + '</strong></p>' +
                        '<div class="d-flex justify-content-between align-items-center mt-1">' +
                        '<small class="text-secondary">' + esc(p.queue_number || p.no_visit || '-') + '</small>' +
                        '<div>' +
                        btnEtiket +
                        badgePayment +
                        '</div>' +
                        '</div>' +
                        '</div>';
                });
                $prescList.html(html);

                // Auto-refresh active workspace if currently selected prescription was paid in background
                var activeId = currentActiveId || $('#modal-prescription-id').val();
                if (activeId) {
                    var $activeItem = $('.presc-item[data-id="' + activeId + '"]');
                    if ($activeItem.length) {
                        var attrPaid = $activeItem.attr('data-is-paid');
                        var attrStatus = String($activeItem.attr('data-billing-status') || '').toLowerCase();
                        var nowPaid = (attrPaid === '1' || attrPaid == 1 || attrPaid === 'true' || attrPaid === true || attrStatus === 'paid');

                        if (!prevActiveWasPaid && nowPaid) {
                            if (typeof playHospitalChime === 'function') {
                                playHospitalChime();
                            }
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Pasien telah lunas di Kasir! Tombol penyerahan obat sekarang aktif.', '✅ Pembayaran Lunas');
                            }
                        }
                        if (typeof window.selectPrescriptionItem === 'function') {
                            window.selectPrescriptionItem($activeItem);
                        }
                    }
                }
            }
        },

        /**
         * 3. SINKRONISASI KASIR: BILLING REAL-TIME & NOTIFIKASI SUARA KASIR
         */
        lastCashierUnpaidCount: null,
        syncCashierBillings: function () {
            var self = this;
            $.ajax({
                url: self.baseUrl + 'api/sync/cashier-billings',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        var hash = res.unpaid_count + '_' + (res.unpaid_data ? res.unpaid_data.map(function(b) {
                            return b.id + ':' + (b.grand_total || 0) + ':' + (b.discount || 0) + ':' + (b.items ? b.items.length : 0);
                        }).join('|') : '');

                        // Detect incoming new cashier billings and notify cashier staff
                        if (self.lastCashierUnpaidCount !== null && res.unpaid_count > self.lastCashierUnpaidCount && res.unpaid_data && res.unpaid_data.length > 0) {
                            var newBill = res.unpaid_data[0];
                            
                            // 1. Audio chime
                            if (typeof playHospitalChime === 'function') {
                                playHospitalChime();
                            }

                            // 2. Toastr popup
                            if (typeof toastr !== 'undefined') {
                                toastr.success(
                                    'Tagihan baru siap diproses: <strong>' + esc(newBill.patient_name || 'Pasien') + '</strong> (Rp ' + (newBill.total_formatted || '0') + ')',
                                    '🔔 Tagihan Masuk Kasir',
                                    { timeOut: 8000, closeButton: true, progressBar: true }
                                );
                            }
                        }
                        self.lastCashierUnpaidCount = res.unpaid_count;

                        if (self.lastPayloads['cashier'] !== hash) {
                            self.lastPayloads['cashier'] = hash;
                            self.renderCashierBillings(res);
                        }
                    }
                }
            });
        },

        renderCashierBillings: function (res) {
            var $billList = $('#bill-list');
            if ($billList.length && res.unpaid_data) {
                var currentActiveId = $('.bill-item.active').data('id') || $('#modal-billing-id').val();

                // Cache detail items into window.billingDetails dynamically
                if (typeof window.billingDetails === 'undefined') {
                    window.billingDetails = {};
                }

                // Update counter badge & summary card
                var $unpaidBadge = $('#tab-checkout-tab .badge-warning');
                if ($unpaidBadge.length) {
                    $unpaidBadge.text(res.unpaid_count);
                }
                $('.info-box-number:contains("Pasien")').html(res.unpaid_count + ' <small class="text-muted">Pasien</small>');

                if (res.unpaid_data.length === 0) {
                    $billList.html(
                        '<div class="text-center text-muted p-5">' +
                        '<i class="fas fa-check-double text-success fa-2x mb-2 d-block"></i>' +
                        'Tidak ada antrean tagihan yang belum dibayar.' +
                        '</div>'
                    );
                    $('#settlement-card').hide();
                    return;
                }

                var html = '';
                res.unpaid_data.forEach(function (b) {
                    var isActive = (b.id == currentActiveId) ? ' active' : '';
                    if (b.items) {
                        window.billingDetails[b.id] = b.items;
                    }

                    var insBadge = b.insurance_name ? ('<span class="badge badge-info ml-1"><i class="fas fa-handshake mr-1"></i>' + esc(b.insurance_name) + '</span>') : '';

                    html += `
                        <div class="list-group-item list-group-item-action bill-item${isActive}" 
                           role="button"
                           tabindex="0"
                           style="cursor: pointer;"
                           data-id="${b.id}" 
                           data-name="${esc(b.patient_name || 'Pasien')}" 
                           data-rm="${esc(b.no_rm || '-')}" 
                           data-visit="${esc(b.queue_number || b.no_visit)}" 
                           data-billno="${esc(b.billing_no || 'BILL-' + b.id)}" 
                           data-serv="${b.total_services || 0}" 
                           data-med="${b.total_medicines || 0}" 
                           data-resto="${b.total_restaurant || 0}" 
                           data-discount="${b.discount || 0}" 
                           data-grand="${b.grand_total || 0}"
                           data-paymethod="${esc(b.visit_payment_method || '')}"
                           data-insurance="${esc(b.insurance_name || '')}"
                           data-tier="${esc(b.membership_tier || 'regular')}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 font-weight-bold text-teal">${esc(b.patient_name || 'Pasien')}</h6>
                                <small class="badge badge-secondary">${esc(b.no_rm || '-')}</small>
                            </div>
                            <p class="mb-1 text-sm text-secondary">No. Billing: <strong>${esc(b.billing_no || 'BILL-' + b.id)}</strong>${insBadge}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-secondary"><i class="fas fa-ticket text-teal mr-1"></i>#${esc(b.queue_number || b.no_visit)}</small>
                                <span class="font-weight-bold text-success">Rp ${b.total_formatted || '0'}</span>
                            </div>
                        </div>
                    `;
                });
                $billList.html(html);

                // Auto-select first bill item if none is currently active
                if (!currentActiveId || !$('.bill-item.active').length) {
                    var $firstBill = $('.bill-item').first();
                    if ($firstBill.length && typeof window.selectCashierBillItem === 'function') {
                        window.selectCashierBillItem($firstBill);
                    }
                }
            }
        },

        /**
         * 4. SINKRONISASI LABORATORIUM: DAFTAR HASIL LAB LIVE
         */
        syncLabQueue: function () {
            var self = this;
            $.ajax({
                url: self.baseUrl + 'api/sync/lab-queue',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        var hash = JSON.stringify(res.counts) + '_' + (res.data ? res.data.length : 0);
                        if (self.lastPayloads['lab'] !== hash) {
                            self.lastPayloads['lab'] = hash;
                            self.renderLabQueue(res);
                        }
                    }
                }
            });
        },

        renderLabQueue: function (res) {
            var self = this;
            var $body = $('#live-lab-results-body');
            if (!$body.length || !res.data) return;

            if (res.data.length === 0) {
                $body.html('<tr><td colspan="9" class="text-center text-muted py-4"><i class="fas fa-vials text-teal mr-1"></i> Belum ada hasil lab hari ini.</td></tr>');
                return;
            }

            var csrfName = $('meta[name="csrf-name"]').attr('content') || '';
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
            var csrfInput = csrfName ? ('<input type="hidden" name="' + csrfName + '" value="' + csrfToken + '">') : '';

            var html = '';
            res.data.forEach(function (lr, idx) {
                var badgeStatus = '';
                if (lr.status === 'normal') {
                    badgeStatus = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> NORMAL</span>';
                } else if (lr.status === 'high') {
                    badgeStatus = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-arrow-up"></i> HIGH</span>';
                } else if (lr.status === 'low') {
                    badgeStatus = '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-arrow-down"></i> LOW</span>';
                } else {
                    badgeStatus = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle"></i> ABNORMAL</span>';
                }

                var dateFormatted = lr.test_date ? lr.test_date.split('-').reverse().join('/') : '-';

                html += `
                    <tr>
                        <td class="text-center font-weight-bold">${idx + 1}</td>
                        <td><span class="badge badge-light border font-weight-bold text-dark" style="font-size:12px;">${esc(lr.lab_no)}</span></td>
                        <td><strong class="text-dark">${esc(lr.patient_name)}</strong><br><small class="text-muted">RM: ${esc(lr.no_rm)} | ${lr.gender === 'L' ? 'L' : 'P'}</small></td>
                        <td><strong class="text-dark">${esc(lr.test_type)}</strong></td>
                        <td><span class="font-weight-bold text-dark" style="font-size: 15px;">${esc(lr.result_value)} <small class="text-muted">${esc(lr.unit || '')}</small></span></td>
                        <td><small class="text-muted font-weight-bold">${esc(lr.normal_range || '-')} ${esc(lr.unit || '')}</small></td>
                        <td>${badgeStatus}</td>
                        <td><div class="text-xs text-dark font-weight-bold">${dateFormatted}</div><small class="text-muted">${esc(lr.officer_name || '-')}</small></td>
                        <td class="text-center">
                            <a href="${self.baseUrl}klinik/cetak-lab/${lr.id}" target="_blank" class="btn btn-outline-success btn-xs font-weight-bold mr-1" title="Cetak Lembar Lab">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                            <form action="${self.baseUrl}klinik/lab" method="post" class="d-inline" onsubmit="return confirm('Hapus hasil lab ini?');">
                                ${csrfInput}
                                <input type="hidden" name="action" value="delete_lab">
                                <input type="hidden" name="lab_id" value="${lr.id}">
                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                `;
            });
            $body.html(html);
        },

        /**
         * 5. SINKRONISASI RESTO: DAPUR GIZI KDS
         */
        syncRestoKitchen: function () {
            var self = this;
            $.ajax({
                url: self.baseUrl + 'api/sync/resto-kitchen',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        var hash = res.count + '_' + (res.data ? res.data.length : 0);
                        if (self.lastPayloads['resto'] !== hash) {
                            self.lastPayloads['resto'] = hash;
                            self.renderRestoKitchen(res);
                        }
                    }
                }
            });
        },

        renderRestoKitchen: function (res) {
            var $kds = $('#kds-orders-container');
            if ($kds.length && res.data) {
                if (res.data.length === 0) {
                    $kds.html('<div class="col-12 text-center py-5 text-muted"><i class="fas fa-utensils fa-3x mb-2 d-block text-secondary"></i> Belum ada pesanan aktif di dapur gizi.</div>');
                    return;
                }

                var html = '';
                res.data.forEach(function (ord) {
                    var badgeStatus = '<span class="badge badge-warning">Menunggu</span>';
                    if (ord.status === 'cooking') badgeStatus = '<span class="badge badge-info">Sedang Dimasak</span>';
                    else if (ord.status === 'ready') badgeStatus = '<span class="badge badge-success">Siap Saji</span>';

                    var itemsList = '';
                    if (ord.items) {
                        ord.items.forEach(function (it) {
                            itemsList += `<li class="d-flex justify-content-between py-1 border-bottom"><span><strong>${it.qty}x</strong> ${esc(it.menu_name || 'Menu')}</span></li>`;
                        });
                    }

                    html += `
                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                            <div class="card h-100 border shadow-none" style="border: 1px solid #b8b8b8; border-top: 4px solid #0d9f4f !important;">
                                <div class="card-header bg-light p-2 d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">#${esc(ord.order_number)}</strong>
                                    ${badgeStatus}
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-muted d-block mb-1">Pelanggan: <strong>${esc(ord.customer_name || 'Pelanggan')}</strong></small>
                                    <ul class="list-unstyled mb-0" style="font-size: 13px;">${itemsList}</ul>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $kds.html(html);
            }
        },

        /**
         * 6. SINKRONISASI HRD: PRESENSI LIVE
         */
        syncHrdPresence: function () {
            var self = this;
            $.ajax({
                url: self.baseUrl + 'api/sync/hrd-presence',
                method: 'GET',
                dataType: 'json',
                timeout: 5000,
                success: function (res) {
                    if (res && res.status === 'success') {
                        var hash = res.present_count + '_' + res.total_employees;
                        if (self.lastPayloads['hrd'] !== hash) {
                            self.lastPayloads['hrd'] = hash;
                            $('#hrd-present-count').text(res.present_count);
                            $('#hrd-total-employees').text(res.total_employees);
                        }
                    }
                }
            });
        }
    };

    $(document).ready(function () {
        window.LiveSyncEngine.init();
    });

})(jQuery);
