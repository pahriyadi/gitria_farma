/**
 * ====================================================================================================
 * UNIVERSAL OFFLINE RESILIENCE & LOCAL ACTIVITY ENGINE (OPTIMISTIC UI + FIFO SYNC)
 * SAWAMAWA MEDICAL CENTER
 * 
 * Kapabilitas Penuh:
 * 1. Deteksi status jaringan andal (Online / Offline / Sinkronisasi).
 * 2. Caching halaman & navigasi instan (PWA Service Worker + In-Memory View Cache).
 * 3. Eksekusi skrip halaman saat navigasi offline (DataTables, Select2, Modal, Event Handlers).
 * 4. Optimistic UI: Data form (Pasien Baru, Kunjungan, SOAP, Kasir, Apotek) LANGSUNG MASUK KE TABEL
 *    dan daftar antrean di layar secara instan dengan label "Draf Offline".
 * 5. Multi-Store Offline DB: offline_queue, offline_views, offline_patients, offline_queues, offline_billings, offline_prescriptions.
 * 6. Auto-Sync FIFO saat koneksi/listrik kembali normal ke server MySQL.
 * ====================================================================================================
 */

(function (window, $) {
    'use strict';

    const DB_NAME = 'SawamawaOfflineDB';
    const DB_VERSION = 3;
    const STORE_QUEUE = 'offline_queue';
    const STORE_VIEWS = 'offline_views';
    const STORE_PATIENTS = 'offline_patients';
    const STORE_QUEUES = 'offline_queues';
    const STORE_BILLINGS = 'offline_billings';
    const STORE_PRESCRIPTIONS = 'offline_prescriptions';
    const STORE_MASTER = 'offline_master';

    class OfflineSyncEngine {
        constructor() {
            this.db = null;
            this.isOnline = navigator.onLine;
            this.isSyncing = false;
            this.heartbeatInterval = null;
            this.draftCount = 0;
            this.consecutiveFailures = 0;
            this.pingEndpoint = null;
            this._browserOfflineLock = !navigator.onLine; // Lock saat browser offline event aktif
            this.viewsCache = {};
            this.patientsCache = [];
            this.queuesCache = [];
            this.billingsCache = [];
            this.prescriptionsCache = [];

            this.loadMemoryCacheFromStorage();
            this.initDB().then(() => {
                this.initServiceWorker();
                this.initNetworkListeners();
                this.initFormInterceptors();
                this.initNavigationInterceptors();
                this.initDataTablesOfflineAdapter();
                this.initUI();
                this.updateDraftCount();
                this.startHeartbeat();
                this.saveCurrentPageView();
                this.prewarmAppPages();
            });
        }

        /**
         * Muat cache halaman & data sinkron dari LocalStorage ke RAM
         */
        loadMemoryCacheFromStorage() {
            try {
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && key.startsWith('sw_view_')) {
                        const path = key.replace('sw_view_', '');
                        const val = JSON.parse(localStorage.getItem(key));
                        if (val && val.html) {
                            this.viewsCache[path] = val;
                        }
                    }
                }
                const p = localStorage.getItem('sw_patients_cache');
                if (p) this.patientsCache = JSON.parse(p);

                const q = localStorage.getItem('sw_queues_cache');
                if (q) this.queuesCache = JSON.parse(q);
            } catch (e) {}
        }

        /**
         * 1. Inisialisasi Service Worker & Cross-Tab Update Broadcast Listener
         */
        initServiceWorker() {
            if ('serviceWorker' in navigator) {
                const cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
                const swUrl = cleanBase + '/sw.js';
                navigator.serviceWorker.register(swUrl, { updateViaCache: 'none' })
                    .then((reg) => {
                        console.log('[OfflineEngine] Service Worker aktif. Scope:', reg.scope);
                        // Cek pembaruan script sw.js secara proaktif saat inisialisasi
                        reg.update().catch(() => {});
                    })
                    .catch((err) => {
                        console.warn('[OfflineEngine] Gagal mendaftarkan Service Worker:', err);
                    });

                // Deteksi jika ada controller Service Worker baru yang aktif
                navigator.serviceWorker.addEventListener('controllerchange', () => {
                    console.log('[OfflineEngine] Service Worker controller baru telah aktif.');
                });
            }

            // Inisialisasi listener multi-tab sync untuk broadcast sinyal force update
            this.initCrossTabUpdateListener();
        }

        /**
         * Inisialisasi Listener Sinkronisasi Pembaruan Lintas Tab Browser
         */
        initCrossTabUpdateListener() {
            // 1. BroadcastChannel API (Modern Browser)
            if (typeof BroadcastChannel !== 'undefined') {
                try {
                    this.systemChannel = new BroadcastChannel('sawamawa_system_channel');
                    this.systemChannel.onmessage = (event) => {
                        if (event.data && event.data.action === 'FORCE_UPDATE_SIGNAL') {
                            console.log('[OfflineEngine] Menerima sinyal pembaruan sistem dari tab lain.');
                            this.purgeLocalCaches(true).then(() => {
                                const url = new URL(window.location.href);
                                url.searchParams.set('_force_update', event.data.timestamp || Date.now());
                                window.location.href = url.toString();
                            });
                        }
                    };
                } catch (e) {}
            }

            // 2. StorageEvent Fallback (Cross-Tab sync via LocalStorage)
            window.addEventListener('storage', (e) => {
                if (e.key === 'sawamawa_force_update_broadcast' && e.newValue) {
                    console.log('[OfflineEngine] Menerima sinyal storage force update dari tab lain.');
                    this.purgeLocalCaches(true).then(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('_force_update', e.newValue);
                        window.location.href = url.toString();
                    });
                }
            });
        }

        /**
         * 2. Inisialisasi IndexedDB Multi-Store
         */
        initDB() {
            return new Promise((resolve) => {
                if (!window.indexedDB) {
                    resolve();
                    return;
                }

                const request = window.indexedDB.open(DB_NAME, DB_VERSION);

                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains(STORE_QUEUE)) {
                        const store = db.createObjectStore(STORE_QUEUE, { keyPath: 'id', autoIncrement: true });
                        store.createIndex('status', 'status', { unique: false });
                        store.createIndex('created_at', 'created_at', { unique: false });
                    }
                    if (!db.objectStoreNames.contains(STORE_VIEWS)) {
                        db.createObjectStore(STORE_VIEWS, { keyPath: 'path' });
                    }
                    if (!db.objectStoreNames.contains(STORE_PATIENTS)) {
                        const pStore = db.createObjectStore(STORE_PATIENTS, { keyPath: 'id' });
                        pStore.createIndex('no_rm', 'no_rm', { unique: false });
                        pStore.createIndex('name', 'name', { unique: false });
                    }
                    if (!db.objectStoreNames.contains(STORE_QUEUES)) {
                        db.createObjectStore(STORE_QUEUES, { keyPath: 'id' });
                    }
                    if (!db.objectStoreNames.contains(STORE_BILLINGS)) {
                        db.createObjectStore(STORE_BILLINGS, { keyPath: 'id' });
                    }
                    if (!db.objectStoreNames.contains(STORE_PRESCRIPTIONS)) {
                        db.createObjectStore(STORE_PRESCRIPTIONS, { keyPath: 'id' });
                    }
                    if (!db.objectStoreNames.contains(STORE_MASTER)) {
                        db.createObjectStore(STORE_MASTER, { keyPath: 'key' });
                    }
                };

                request.onsuccess = (event) => {
                    this.db = event.target.result;
                    console.log('[OfflineEngine] IndexedDB v' + DB_VERSION + ' siap.');
                    this.loadLocalDataFromIndexedDB();
                    resolve();
                };

                request.onerror = (event) => {
                    console.error('[OfflineEngine] Gagal membuka IndexedDB:', event.target.error);
                    resolve();
                };
            });
        }

        loadLocalDataFromIndexedDB() {
            if (!this.db) return;
            try {
                const tx = this.db.transaction([STORE_PATIENTS, STORE_QUEUES], 'readonly');
                const pStore = tx.objectStore(STORE_PATIENTS);
                const pReq = pStore.getAll();
                pReq.onsuccess = () => {
                    if (pReq.result && pReq.result.length > 0) {
                        this.patientsCache = pReq.result;
                        localStorage.setItem('sw_patients_cache', JSON.stringify(this.patientsCache));
                    }
                };
                const qStore = tx.objectStore(STORE_QUEUES);
                const qReq = qStore.getAll();
                qReq.onsuccess = () => {
                    if (qReq.result && qReq.result.length > 0) {
                        this.queuesCache = qReq.result;
                        localStorage.setItem('sw_queues_cache', JSON.stringify(this.queuesCache));
                    } else {
                        this.queuesCache = [];
                        localStorage.removeItem('sw_queues_cache');
                    }
                };
            } catch (e) {}
        }

        /**
         * 3. Simpan Tampilan Halaman Aktif
         */
        saveCurrentPageView() {
            try {
                const path = this.normalizePath(window.location.href);
                const $wrapper = $('.content-wrapper');
                if ($wrapper.length) {
                    const html = $wrapper.html();
                    const title = document.title;
                    if (html && html.trim().length > 50) {
                        this.saveViewToDB({ path, title, html, url: window.location.href });
                    }
                }
            } catch (e) {}
        }

        saveViewToDB(viewData) {
            this.viewsCache[viewData.path] = viewData;
            try {
                localStorage.setItem('sw_view_' + viewData.path, JSON.stringify(viewData));
            } catch (e) {}
            if (this.db && this.db.objectStoreNames.contains(STORE_VIEWS)) {
                try {
                    const tx = this.db.transaction([STORE_VIEWS], 'readwrite');
                    tx.objectStore(STORE_VIEWS).put(viewData);
                } catch (e) {}
            }
        }

        async getViewFromDB(path) {
            if (this.viewsCache[path]) return this.viewsCache[path];
            try {
                const ls = localStorage.getItem('sw_view_' + path);
                if (ls) {
                    const parsed = JSON.parse(ls);
                    this.viewsCache[path] = parsed;
                    return parsed;
                }
            } catch (e) {}
            return new Promise((resolve) => {
                if (this.db && this.db.objectStoreNames.contains(STORE_VIEWS)) {
                    try {
                        const tx = this.db.transaction([STORE_VIEWS], 'readonly');
                        const req = tx.objectStore(STORE_VIEWS).get(path);
                        req.onsuccess = () => {
                            if (req.result) {
                                this.viewsCache[path] = req.result;
                                resolve(req.result);
                            } else resolve(null);
                        };
                        req.onerror = () => resolve(null);
                        return;
                    } catch (e) {}
                }
                resolve(null);
            });
        }

        normalizePath(urlStr) {
            try {
                const u = new URL(urlStr, window.location.origin);
                let p = u.pathname;
                const basePart = '/sawamawamedicalcenter.id/public';
                if (p.startsWith(basePart)) p = p.replace(basePart, '');
                p = p.replace(/\/+$/, '');
                return (p === '' || p === '/') ? '/dashboard' : p;
            } catch (e) {
                return urlStr;
            }
        }

        /**
         * 4. Prewarm Halaman & Master Data saat Online
         */
        prewarmAppPages() {
            if (!this.isOnline || !window.fetch) return;

            const base = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
            const pagesToWarm = [
                base + '/dashboard',
                base + '/klinik/pendaftaran',
                base + '/klinik/antrean',
                base + '/klinik/soap',
                base + '/keuangan/kasir',
                base + '/apotek/resep',
                base + '/apotek/penjualan',
                base + '/klinik/lab',
                base + '/resto/pos'
            ];

            pagesToWarm.forEach((pageUrl, idx) => {
                setTimeout(async () => {
                    try {
                        const res = await fetch(pageUrl, { credentials: 'same-origin', cache: 'reload' });
                        if (res.ok) {
                            const text = await res.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(text, 'text/html');
                            const contentEl = doc.querySelector('.content-wrapper');
                            const title = doc.querySelector('title') ? doc.querySelector('title').innerText : 'Sawamawa Medical Center';

                            if (contentEl) {
                                const path = this.normalizePath(pageUrl);
                                this.saveViewToDB({
                                    path: path,
                                    title: title,
                                    html: contentEl.innerHTML,
                                    url: pageUrl
                                });
                            }
                        }
                    } catch (e) {}
                }, idx * 250);
            });

            // Unduh data master & cache antrean/pasien awal
            setTimeout(async () => {
                try {
                    const qRes = await fetch(base + '/api/sync/klinik-queue');
                    if (qRes.ok) {
                        const qData = await qRes.json();
                        if (qData && Array.isArray(qData.data)) {
                            this.queuesCache = qData.data;
                            if (qData.data.length > 0) {
                                localStorage.setItem('sw_queues_cache', JSON.stringify(this.queuesCache));
                            } else {
                                localStorage.removeItem('sw_queues_cache');
                            }
                            if (this.db && this.db.objectStoreNames.contains(STORE_QUEUES)) {
                                const tx = this.db.transaction([STORE_QUEUES], 'readwrite');
                                const store = tx.objectStore(STORE_QUEUES);
                                store.clear(); // Bersihkan seluruh record lama agar data zombie tidak muncul
                                if (qData.data.length > 0) {
                                    qData.data.forEach(q => store.put(q));
                                }
                            }
                        }
                    }

                    const pRes = await fetch(base + '/klinik/pendaftaran?draw=1&start=0&length=100');
                    if (pRes.ok) {
                        const pData = await pRes.json();
                        if (pData && pData.data) {
                            // Extract patient data
                            this.patientsCache = pData.data.map(row => {
                                return {
                                    no_rm: $('<div>').html(row[0]).text().trim(),
                                    nik: $('<div>').html(row[1]).text().trim(),
                                    name: $('<div>').html(row[2]).text().trim(),
                                    gender: row[3].indexOf('L') > -1 ? 'L' : 'P',
                                    phone: $('<div>').html(row[4]).text().trim(),
                                    address: $('<div>').html(row[5]).text().trim(),
                                    raw_actions: row[6]
                                };
                            });
                            localStorage.setItem('sw_patients_cache', JSON.stringify(this.patientsCache));
                        }
                    }
                } catch (e) {}
            }, 2500);
        }

        /**
         * 5. Interceptor Navigasi Sidebar Offline
         */
        initNavigationInterceptors() {
            const self = this;

            document.addEventListener('click', function (e) {
                const anchor = e.target.closest('a[href]');
                if (!anchor) return;

                const href = anchor.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                if (anchor.getAttribute('target') === '_blank' || anchor.hasAttribute('data-toggle') || anchor.hasAttribute('data-widget')) return;

                if (!self.isOnline || !navigator.onLine) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    self.handleOfflineNavigation(href, anchor);
                    return false;
                }
            }, true);

            window.addEventListener('popstate', async () => {
                if (!self.isOnline || !navigator.onLine) {
                    const normPath = self.normalizePath(window.location.href);
                    const view = await self.getViewFromDB(normPath);
                    if (view && view.html) {
                        self.renderOfflineView(view);
                    }
                }
            });
        }

        async handleOfflineNavigation(href, anchor) {
            const normPath = this.normalizePath(href);
            const view = await this.getViewFromDB(normPath);

            if (view && view.html) {
                this.renderOfflineView(view);

                // Update active class
                $('.nav-sidebar .nav-link').removeClass('active');
                if (anchor) {
                    $(anchor).addClass('active');
                } else {
                    $(`.nav-sidebar a[href*="${normPath}"]`).addClass('active');
                }

                try {
                    window.history.pushState({ offlinePath: normPath }, view.title, href);
                } catch (e) {}

                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'info',
                        title: `<span style="font-size:13px; font-weight:bold;"><i class="fas fa-plug text-warning mr-1"></i> Mode Offline: ${(view.title || '').split('-')[0].trim()}</span>`
                    });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Menu Operasional Mode Offline',
                        html: `
                            <p class="text-secondary small mb-3">Pilih modul yang telah siap digunakan:</p>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-teal text-left font-weight-bold mb-2" onclick="window.offlineSyncEngine.navigateTo('/klinik/pendaftaran')"><i class="fas fa-user-plus mr-2"></i> Pendaftaran Pasien</button>
                                <button class="btn btn-teal text-left font-weight-bold mb-2" onclick="window.offlineSyncEngine.navigateTo('/klinik/soap')"><i class="fas fa-notes-medical mr-2"></i> Rekam Medis & SOAP Dokter</button>
                                <button class="btn btn-teal text-left font-weight-bold mb-2" onclick="window.offlineSyncEngine.navigateTo('/keuangan/kasir')"><i class="fas fa-cash-register mr-2"></i> Kasir Utama</button>
                                <button class="btn btn-teal text-left font-weight-bold mb-2" onclick="window.offlineSyncEngine.navigateTo('/apotek/resep')"><i class="fas fa-pills mr-2"></i> Apotek & Resep</button>
                                <button class="btn btn-secondary text-left font-weight-bold" onclick="window.offlineSyncEngine.navigateTo('/dashboard')"><i class="fas fa-home mr-2"></i> Dashboard</button>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true
                    });
                }
            }
        }

        navigateTo(path) {
            const cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
            this.handleOfflineNavigation(cleanBase + path, null);
            if (typeof Swal !== 'undefined') Swal.close();
        }

        /**
         * Render Tampilan Offline & Eksekusi Skrip Halaman
         */
        renderOfflineView(view) {
            const $wrapper = $('.content-wrapper');
            $wrapper.html(view.html);
            document.title = view.title || document.title;

            // Eksekusi skrip yang ada di dalam view
            this.executeViewScripts($wrapper);

            // Inisialisasi plugin & DataTables offline
            this.reinitPlugins();
            this.populateOfflineDynamicElements();
        }

        executeViewScripts($container) {
            try {
                $container.find('script').each(function () {
                    const src = $(this).attr('src');
                    const code = $(this).text();
                    if (!src && code && code.trim().length > 0) {
                        try {
                            const newScript = document.createElement('script');
                            newScript.type = 'text/javascript';
                            newScript.text = code;
                            document.body.appendChild(newScript);
                            document.body.removeChild(newScript);
                        } catch (err) {
                            console.warn('[OfflineEngine] Script exec warning:', err);
                        }
                    }
                });
            } catch (e) {}
        }

        reinitPlugins() {
            try {
                if ($.fn.select2) {
                    $('.select2').select2({ theme: 'bootstrap4', width: '100%', language: 'id' });
                }
            } catch (e) {}

            // Re-bind DataTables if server-side table exists
            if ($('#table-patients').length && $.fn.DataTable) {
                if (!$.fn.DataTable.isDataTable('#table-patients')) {
                    this.initOfflinePatientsDataTable();
                }
            }
        }

        /**
         * 6. Offline DataTables Adapter
         */
        initDataTablesOfflineAdapter() {
            const self = this;
            // Jika ajax dipanggil saat offline, sediakan mock data
            $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                if (!self.isOnline && options.url.indexOf('pendaftaran') > -1 && options.type.toUpperCase() === 'GET' && options.data && options.data.indexOf('draw=') > -1) {
                    options.beforeSend = function (xhr) {
                        xhr.abort();
                    };
                }
            });
        }

        initOfflinePatientsDataTable() {
            const self = this;
            if ($.fn.DataTable.isDataTable('#table-patients')) {
                $('#table-patients').DataTable().destroy();
            }

            // Populate table body with cached patients + offline draft patients
            const $tbody = $('#table-patients tbody').empty();
            const allPatients = [...this.patientsCache];

            if (allPatients.length > 0) {
                allPatients.forEach(p => {
                    const isDraft = p.is_offline_draft;
                    const genderBadge = (p.gender === 'L') ? '<span class="badge badge-light border text-primary">L</span> Laki-laki' : '<span class="badge badge-light border text-danger">P</span> Perempuan';
                    const draftBadge = isDraft ? '<br><span class="badge badge-warning text-dark font-weight-bold animate__animated animate__pulse"><i class="fas fa-clock mr-1"></i> Draf Offline</span>' : '';
                    
                    const tr = `
                        <tr class="${isDraft ? 'table-warning' : ''}">
                            <td class="text-center font-weight-bold text-teal">${p.no_rm || '-'} ${draftBadge}</td>
                            <td class="text-center">${p.nik || '-'}</td>
                            <td class="font-weight-bold">${p.name || '-'}</td>
                            <td class="text-center">${genderBadge}</td>
                            <td class="text-center">${p.phone || '-'}</td>
                            <td>${p.address || '-'}</td>
                            <td class="text-center">
                                <button class="btn btn-teal btn-xs font-weight-bold btn-visit mr-1" data-id="${p.id || p.no_rm}" data-name="${p.name}" data-rm="${p.no_rm}" data-toggle="modal" data-target="#visitModal">
                                    <i class="fas fa-ticket mr-1"></i> Kunjungan
                                </button>
                                <button class="btn btn-outline-secondary btn-xs font-weight-bold btn-edit-patient mr-1" data-id="${p.id || ''}" data-name="${p.name}" data-nik="${p.nik}" data-gender="${p.gender}" data-phone="${p.phone}" data-address="${p.address}">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(tr);
                });
            }

            $('#table-patients').DataTable({
                language: Object.assign({}, window.dtIndonesianLanguage || {}, {
                    emptyTable: '<i class="fas fa-users mr-1"></i> Data Pasien siap ditambahkan secara offline. Klik tombol "Register Pasien Baru" di atas.'
                }),
                pageLength: 10,
                order: [[0, 'desc']]
            });
        }

        async populateOfflineDynamicElements() {
            // Ambil draf transaksi lokal yang sedang pending dari IndexedDB STORE_QUEUE
            let drafts = [];
            try {
                drafts = await this.getPendingDrafts();
            } catch (e) {}

            const draftQueues = [];
            if (Array.isArray(drafts)) {
                drafts.forEach(d => {
                    const normData = this.normalizePayload(d);
                    const targetUrl = (d.url || '').toLowerCase();
                    if (targetUrl.includes('pendaftaran') || targetUrl.includes('antrean') || targetUrl.includes('kunjungan')) {
                        draftQueues.push({
                            id: 'draft_' + d.id,
                            patient_name: normData.name || normData.patient_name || d.title || 'Pasien Baru (Draf)',
                            no_rm: normData.no_rm || 'DRAF-OFFLINE',
                            queue_number: normData.queue_number || ('D-' + String(d.id).slice(-3)),
                            polyclinic_name: normData.polyclinic_name || 'Umum',
                            visit_type: normData.visit_type || 'poli',
                            status: 'Draf Offline',
                            is_offline_draft: true
                        });
                    }
                });
            }

            // Gabungkan antrean dari cache (jika valid) + draf antrean offline baru
            const activeQueues = [...(this.queuesCache || []), ...draftQueues];

            // 1. Sidebar Antrean di Modul Pendaftaran
            if ($('#live-pendaftaran-antrean-body').length) {
                const $qBody = $('#live-pendaftaran-antrean-body');
                $qBody.empty();

                if (activeQueues.length > 0) {
                    activeQueues.forEach(q => {
                        const target = (q.visit_type === 'tindakan') ? 'Ruang Tindakan' : 'Poliklinik ' + (q.polyclinic_name || 'Umum');
                        const isDraft = q.is_offline_draft;
                        $qBody.append(`
                            <div class="list-group-item list-group-item-action p-3 sidebar-queue-item ${isDraft ? 'border-left-warning' : ''}" data-search="${(q.patient_name || '')} ${(q.no_rm || '')}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 font-weight-bold text-teal">${q.patient_name || 'Pasien'}</h6>
                                    <span class="badge ${isDraft ? 'badge-warning text-dark font-weight-bold' : 'badge-teal'}">${q.queue_number || 'A-01'}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-xs text-muted mb-2">
                                    <span><i class="fas fa-hospital mr-1"></i> ${target}</span>
                                    <span>${isDraft ? '<span class="badge badge-warning text-dark font-weight-bold"><i class="fas fa-clock mr-1"></i> Draf</span>' : (q.status || 'Menunggu')}</span>
                                </div>
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-xs btn-outline-teal font-weight-bold btn-voice-call" data-queue="${q.queue_number || 'A-01'}" data-name="${q.patient_name || 'Pasien'}" data-target="${target}">
                                        <i class="fas fa-bullhorn mr-1"></i> Panggil
                                    </button>
                                </div>
                            </div>
                        `);
                    });
                    $('#sidebar-queue-count').text(activeQueues.length + ' Pasien');
                } else {
                    $qBody.html(`
                        <div id="empty-queue-placeholder" class="text-center p-4 text-muted">
                            <i class="fas fa-clipboard-check fa-2x mb-2 text-teal" style="opacity: 0.5;"></i>
                            <div class="font-weight-bold small text-dark">Tidak Ada Antrean</div>
                            <small class="text-secondary">Draf antrean baru yang Anda daftarkan secara offline akan otomatis muncul di sini.</small>
                        </div>
                    `);
                    $('#sidebar-queue-count').text('0 Pasien');
                }
            }

            // 2. Queue list di Modul SOAP Dokter
            if ($('#queue-list').length || $('#soap-patient-queue-list').length) {
                const $soapList = $('#queue-list').length ? $('#queue-list') : $('#soap-patient-queue-list');
                $soapList.empty();

                if (activeQueues.length > 0) {
                    activeQueues.forEach(v => {
                        const isDraft = v.is_offline_draft;
                        $soapList.append(`
                            <a href="#" class="list-group-item list-group-item-action visit-item ${isDraft ? 'border-left-warning' : ''}" data-id="${v.id}" data-name="${v.patient_name}" data-rm="${v.no_rm}" data-queue="${v.queue_number || 'A-01'}" data-poly="${v.polyclinic_name || 'Umum'}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="font-weight-bold text-teal mb-0">${v.patient_name}</h6>
                                    <span class="badge ${isDraft ? 'badge-warning text-dark font-weight-bold' : 'badge-teal'}">${v.queue_number || 'A-01'}</span>
                                </div>
                                <small class="text-muted d-block mb-1">No RM: ${v.no_rm || '-'} | Poli: ${v.polyclinic_name || 'Umum'}</small>
                                <span class="badge ${isDraft ? 'badge-warning text-dark font-weight-bold' : 'badge-light border text-secondary'}">${isDraft ? '<i class="fas fa-clock mr-1"></i> Draf Offline' : (v.status || 'Menunggu')}</span>
                            </a>
                        `);
                    });
                    $('#lbl-total-queue').text(activeQueues.length + ' Pasien');
                } else {
                    $soapList.html(`
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-user-clock fa-2x mb-2 text-secondary" style="opacity: 0.5;"></i>
                            <div class="font-weight-bold small text-dark">Antrean Pasien Kosong</div>
                            <small class="text-secondary">Tidak ada antrean pemeriksaan dokter saat ini.</small>
                        </div>
                    `);
                    $('#lbl-total-queue').text('0 Pasien');
                }
            }

            // 3. Tabel Antrean Utama di /klinik/antrean
            if ($('#live-antrean-body').length) {
                const $body = $('#live-antrean-body');
                $body.empty();

                if (activeQueues.length > 0) {
                    activeQueues.forEach(q => {
                        const isDraft = q.is_offline_draft;
                        const target = (q.visit_type === 'tindakan') ? 'Ruang Tindakan' : 'Poliklinik ' + (q.polyclinic_name || 'Umum');
                        $body.append(`
                            <tr class="${isDraft ? 'table-warning' : ''}">
                                <td class="text-center font-weight-bold"><span class="badge badge-teal py-2 px-3 font-weight-bold" style="font-size:16px;">${q.queue_number || 'A-01'}</span></td>
                                <td class="font-weight-bold">${q.patient_name || '-'}<br><small class="text-muted">No RM: ${q.no_rm || '-'}</small></td>
                                <td>${target}</td>
                                <td>Dokter Jaga</td>
                                <td class="text-center"><span class="badge ${isDraft ? 'badge-warning text-dark' : 'badge-secondary'}">${isDraft ? 'Draf Offline' : (q.status || 'waiting')}</span></td>
                                <td class="text-center">${q.created_at ? q.created_at.substring(11, 19) : '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm font-weight-bold btn-voice-call" data-queue="${q.queue_number || 'A-01'}" data-name="${q.patient_name}" data-target="${target}">
                                        <i class="fas fa-volume-up mr-1"></i> Panggil
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    $('#stat-total').text(activeQueues.length);
                    $('#stat-active').text(activeQueues.length);
                    $('#stat-waiting').text(activeQueues.length);
                } else {
                    $body.html(`
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-ticket text-teal mr-2"></i>
                                Tidak ada antrean pasien untuk filter ini.
                            </td>
                        </tr>
                    `);
                    $('#stat-total').text('0');
                    $('#stat-active').text('0');
                    $('#stat-waiting').text('0');
                    $('#stat-called').text('0');
                    $('#stat-done').text('0');
                }
            }
        }

        /**
         * 7. Interceptor Form Universal & Optimistic UI
         */
        initFormInterceptors() {
            const self = this;

            $(document).on('submit', 'form:not(.no-offline-intercept)', function (e) {
                const $form = $(this);
                const actionUrl = $form.attr('action') || window.location.href;
                const method = ($form.attr('method') || 'POST').toUpperCase();

                if (method === 'GET') return;

                if (!self.isOnline || !navigator.onLine) {
                    e.preventDefault();
                    e.stopPropagation();

                    self.handleOfflineFormSubmit($form, actionUrl, method);
                }
            });

            $(document).ajaxError(function (event, jqXHR, settings, thrownError) {
                const url = settings.url || '';

                // Deteksi jaringan terputus realtime dari kegagalan request (status 0 / timeout)
                if (jqXHR && (jqXHR.status === 0 || jqXHR.statusText === 'timeout' || thrownError === 'timeout')) {
                    self.reportNetworkFailure();
                }

                // Abaikan background polling, voice caller, live sync, ping, dan audio triggers untuk pembuatan draf
                if (url.indexOf('api/sync') > -1 || url.indexOf('voice-call') > -1 || url.indexOf('live-sync') > -1 || url.indexOf('ping') > -1 || url.indexOf('queue-calls') > -1) {
                    return;
                }

                if (settings.type === 'POST' && (jqXHR.status === 0 || jqXHR.statusText === 'timeout')) {
                    const desc = 'Transaksi AJAX (' + (settings.url.split('/').pop() || 'Data') + ')';
                    self.saveDraft({
                        url: settings.url,
                        method: 'POST',
                        data: settings.data,
                        contentType: settings.contentType,
                        title: desc,
                        page_title: document.title
                    }).then(() => {
                        self.notifyOfflineSaved(desc);
                    });
                }
            });

            $(document).ajaxSuccess(function (event, xhr, settings) {
                const url = (settings && settings.url) ? settings.url : '';
                // Hanya sinyal dari ping endpoint yang dianggap sebagai verified success
                const isPingUrl = url.indexOf('api/sync/ping') > -1;
                if (isPingUrl) {
                    self.reportNetworkSuccess(true); // fromVerifiedPing
                } else if (url.indexOf('api/sync') > -1) {
                    self.reportNetworkSuccess(false); // Bisa diblokir oleh browserOfflineLock
                }
            });
        }

        /**
         * Ekstrak data form, simpan ke antrean draf, dan lakukan Optimistic UI Update ke Tabel!
         */
        async handleOfflineFormSubmit($form, actionUrl, method) {
            const formDataArray = $form.serializeArray();
            const payload = {};
            $.each(formDataArray, function () {
                if (payload[this.name] !== undefined) {
                    if (!payload[this.name].push) payload[this.name] = [payload[this.name]];
                    payload[this.name].push(this.value || '');
                } else {
                    payload[this.name] = this.value || '';
                }
            });

            const action = payload.action || '';
            let title = 'Formulir Transaksi Pelayanan';

            // =========================================================================
            // OPTIMISTIC UI 1: PENDAFTARAN PASIEN BARU
            // =========================================================================
            if (action === 'register_patient') {
                const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                const randomNo = Math.floor(1000 + Math.random() * 9000);
                const tempRm = 'RM-' + dateStr + '-' + randomNo;

                const newPatient = {
                    id: 'temp_p_' + Date.now(),
                    no_rm: tempRm,
                    nik: payload.nik || '-',
                    name: payload.name || 'Pasien Baru',
                    gender: payload.gender || 'L',
                    place_of_birth: payload.place_of_birth || '',
                    date_of_birth: payload.date_of_birth || '',
                    phone: payload.phone || '-',
                    address: payload.address || '-',
                    membership_tier: payload.membership_tier || 'regular',
                    is_offline_draft: true
                };

                // 1. Simpan ke local cache & IndexedDB
                this.patientsCache.unshift(newPatient);
                localStorage.setItem('sw_patients_cache', JSON.stringify(this.patientsCache));
                if (this.db) {
                    const tx = this.db.transaction([STORE_PATIENTS], 'readwrite');
                    tx.objectStore(STORE_PATIENTS).put(newPatient);
                }

                // 2. Langsung suntikkan baris baru ke Tabel Pasien di layar!
                this.injectPatientRowOptimistic(newPatient);

                title = `Register Pasien: ${newPatient.name} (${tempRm})`;
            }

            // =========================================================================
            // OPTIMISTIC UI 2: PEMBUATAN ANTREAN KUNJUNGAN PASIEN
            // =========================================================================
            else if (action === 'create_visit') {
                const pId = payload.patient_id;
                const pObj = this.patientsCache.find(p => p.id == pId || p.no_rm == pId) || {};
                const pName = pObj.name || $('#modal-patient-name').val() || 'Pasien';
                const pRm = pObj.no_rm || '';
                const polyName = $('select[name="polyclinic_id"] option:selected').text() || 'Poliklinik Umum';
                const queueNo = 'A-' + (this.queuesCache.length + 1).toString().padStart(3, '0');

                const newQueue = {
                    id: 'temp_q_' + Date.now(),
                    visit_id: 'temp_v_' + Date.now(),
                    patient_id: pId,
                    patient_name: pName,
                    no_rm: pRm,
                    polyclinic_id: payload.polyclinic_id,
                    polyclinic_name: polyName,
                    doctor_id: payload.doctor_id,
                    queue_number: queueNo,
                    visit_type: payload.visit_type || 'poli',
                    status: 'waiting',
                    is_offline_draft: true
                };

                this.queuesCache.unshift(newQueue);
                localStorage.setItem('sw_queues_cache', JSON.stringify(this.queuesCache));
                if (this.db) {
                    const tx = this.db.transaction([STORE_QUEUES], 'readwrite');
                    tx.objectStore(STORE_QUEUES).put(newQueue);
                }

                // Suntikkan antrean baru ke sidebar antrean
                this.injectQueueOptimistic(newQueue);

                title = `Antrean Pasien: ${pName} (${queueNo})`;
            }

            // =========================================================================
            // OPTIMISTIC UI 3: PEMERIKSAAN DOKTER SOAP
            // =========================================================================
            else if (actionUrl.indexOf('soap') > -1 || payload.action === 'save_soap') {
                const pName = payload.patient_name || $('.input-patient-name').val() || 'Pasien';
                title = `SOAP Medis: ${pName}`;

                // Beri tanda selesai pada antrean pasien di kolom kiri
                const vId = payload.visit_id || $('.input-visit-id').val();
                $(`.visit-item[data-id="${vId}"]`).find('.badge-light').removeClass('text-secondary').addClass('badge-success text-white').html('<i class="fas fa-check mr-1"></i> SOAP Selesai (Draf)');
            }

            // =========================================================================
            // OPTIMISTIC UI 4: PEMBAYARAN KASIR
            // =========================================================================
            else if (actionUrl.indexOf('kasir') > -1 || actionUrl.indexOf('bayar') > -1) {
                const pName = payload.patient_name || 'Pasien';
                title = `Pelunasan Kasir: ${pName}`;
            }

            // =========================================================================
            // OPTIMISTIC UI 5: APOTEK & RESEP OBAT
            // =========================================================================
            else if (actionUrl.indexOf('resep') > -1 || actionUrl.indexOf('dispense') > -1) {
                const pName = payload.patient_name || $('#lbl-patient').text() || 'Pasien';
                title = `Dispensing Resep: ${pName}`;
            }

            // Simpan draf ke antrean pengiriman FIFO
            await this.saveDraft({
                url: actionUrl,
                method: method,
                payload: payload,
                raw_serialized: $form.serialize(),
                title: title,
                page_title: document.title
            });

            this.notifyOfflineSaved(title);

            // Buka kembali submit button & tutup modal
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', false).html($btn.data('original-text') || $btn.html());

            if ($form.closest('.modal').length) {
                $form.closest('.modal').modal('hide');
                $form[0].reset();
            }
        }

        injectPatientRowOptimistic(p) {
            // Flash notification di atas halaman pendaftaran
            const flashBanner = `
                <div class="col-md-12 mb-3 alert-flash-card animate__animated animate__fadeInDown">
                    <div class="card p-3" style="background:#ffffff; border:1px solid #b8b8b8; border-left:5px solid #d97706; border-radius:4px; box-shadow:none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center mr-3" style="background:#fef3c7; color:#d97706; width:38px; height:38px; border-radius:50%; font-size:18px;">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div>
                                    <h6 class="font-weight-bold text-dark mb-0">Pasien Baru (Draf Offline) Berhasil Ditambahkan ke Tabel!</h6>
                                    <span class="text-secondary text-xs">No RM Sementara: <strong>${p.no_rm}</strong> &bull; Nama: <strong>${p.name}</strong> &bull; Siap dibuatkan antrean kunjungan.</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-teal btn-sm font-weight-bold btn-visit" data-id="${p.id}" data-name="${p.name}" data-rm="${p.no_rm}" data-toggle="modal" data-target="#visitModal">
                                <i class="fas fa-ticket mr-1"></i> Buat Kunjungan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('.content-wrapper .row:first').prepend(flashBanner);

            // Suntikkan ke DataTable
            if ($('#table-patients').length) {
                const isDataTable = $.fn.DataTable && $.fn.DataTable.isDataTable('#table-patients');
                const genderBadge = (p.gender === 'L') ? '<span class="badge badge-light border text-primary">L</span> Laki-laki' : '<span class="badge badge-light border text-danger">P</span> Perempuan';
                const draftBadge = '<br><span class="badge badge-warning text-dark font-weight-bold animate__animated animate__pulse"><i class="fas fa-clock mr-1"></i> Draf Offline</span>';

                const actions = `
                    <button class="btn btn-teal btn-xs font-weight-bold btn-visit mr-1" data-id="${p.id}" data-name="${p.name}" data-rm="${p.no_rm}" data-toggle="modal" data-target="#visitModal">
                        <i class="fas fa-ticket mr-1"></i> Kunjungan
                    </button>
                    <button class="btn btn-outline-secondary btn-xs font-weight-bold btn-edit-patient mr-1" data-id="${p.id}" data-name="${p.name}" data-nik="${p.nik}" data-gender="${p.gender}" data-phone="${p.phone}" data-address="${p.address}">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                `;

                if (isDataTable) {
                    const dt = $('#table-patients').DataTable();
                    const newRow = dt.row.add([
                        `<span class="font-weight-bold text-teal">${p.no_rm}</span> ${draftBadge}`,
                        p.nik,
                        `<strong class="text-dark">${p.name}</strong>`,
                        genderBadge,
                        p.phone,
                        p.address,
                        actions
                    ]).draw(false).node();
                    $(newRow).addClass('table-warning animate__animated animate__flash');
                } else {
                    const trHtml = `
                        <tr class="table-warning animate__animated animate__flash">
                            <td class="text-center font-weight-bold text-teal">${p.no_rm} ${draftBadge}</td>
                            <td class="text-center">${p.nik}</td>
                            <td class="font-weight-bold">${p.name}</td>
                            <td class="text-center">${genderBadge}</td>
                            <td class="text-center">${p.phone}</td>
                            <td>${p.address}</td>
                            <td class="text-center">${actions}</td>
                        </tr>
                    `;
                    $('#table-patients tbody').prepend(trHtml);
                }
            }
        }

        injectQueueOptimistic(q) {
            const target = (q.visit_type === 'tindakan') ? 'Ruang Tindakan' : 'Poliklinik ' + (q.polyclinic_name || 'Umum');
            const cardHtml = `
                <div class="list-group-item list-group-item-action p-3 sidebar-queue-item border-left-warning animate__animated animate__slideInDown" data-search="${q.patient_name} ${q.no_rm}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 font-weight-bold text-teal">${q.patient_name}</h6>
                        <span class="badge badge-teal font-weight-bold">${q.queue_number}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-xs text-muted mb-2">
                        <span><i class="fas fa-hospital mr-1"></i> ${target}</span>
                        <span class="badge badge-warning text-dark font-weight-bold"><i class="fas fa-clock mr-1"></i> Draf Antrean</span>
                    </div>
                    <div class="d-flex justify-content-end gap-1">
                        <button class="btn btn-xs btn-outline-teal font-weight-bold btn-voice-call" data-queue="${q.queue_number}" data-name="${q.patient_name}" data-target="${target}">
                            <i class="fas fa-bullhorn mr-1"></i> Panggil
                        </button>
                    </div>
                </div>
            `;

            if ($('#empty-queue-placeholder').length) {
                $('#empty-queue-placeholder').remove();
            }

            $('#live-pendaftaran-antrean-body').prepend(cardHtml);
            const count = $('#live-pendaftaran-antrean-body .sidebar-queue-item').length;
            $('#sidebar-queue-count').text(count + ' Pasien');
        }

        /**
         * 8. Event Listener Jaringan & Heartbeat Realtime
         */
        reportNetworkSuccess(fromVerifiedPing = false) {
            this.consecutiveFailures = 0;

            // Jika browser offline lock aktif, hanya verified ping yang boleh membuka kunci
            if (this._browserOfflineLock && !fromVerifiedPing) {
                return; // Abaikan sinyal sukses dari AJAX biasa (bisa stale/cached)
            }

            if (fromVerifiedPing) {
                this._browserOfflineLock = false; // Buka kunci setelah ping terbukti berhasil
            }

            if (!this.isOnline) {
                console.log('[OfflineEngine] Jaringan server terdeteksi aktif kembali (Online Realtime).');
                this.setOnlineState(true);
                this.triggerAutoSync();
            }
        }

        reportNetworkFailure() {
            this.consecutiveFailures = 2;
            if (this.isOnline) {
                console.warn('[OfflineEngine] Jaringan server terputus / offline (Offline Realtime).');
                this.setOnlineState(false);
            }
        }

        async purgeLocalCaches() {
            try {
                // 1. Bersihkan localStorage views & caches
                const keysToRemove = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (key.startsWith('sw_view_') || key.startsWith('sw_queues_') || key.startsWith('sw_patients_'))) {
                        keysToRemove.push(key);
                    }
                }
                keysToRemove.forEach(k => localStorage.removeItem(k));
                localStorage.removeItem('sw_queues_cache');
                localStorage.removeItem('sw_patients_cache');

                this.viewsCache = {};
                this.queuesCache = [];
                this.patientsCache = [];

                // 2. Bersihkan IndexedDB STORE_VIEWS, STORE_QUEUES, STORE_PATIENTS
                if (this.db) {
                    const storesToClear = [STORE_VIEWS, STORE_QUEUES, STORE_PATIENTS];
                    storesToClear.forEach(stName => {
                        try {
                            if (this.db.objectStoreNames.contains(stName)) {
                                const tx = this.db.transaction([stName], 'readwrite');
                                tx.objectStore(stName).clear();
                            }
                        } catch (e) {}
                    });
                }

                // 3. Prewarm ulang secara segar jika online
                if (this.isOnline) {
                    this.saveCurrentPageView();
                    this.prewarmAppPages();
                } else {
                    await this.populateOfflineDynamicElements();
                }

                console.log('[OfflineEngine] Cache tampilan dan antrean lokal berhasil dibersihkan.');
                return true;
            } catch (e) {
                console.error('[OfflineEngine] Gagal membersihkan cache lokal:', e);
                return false;
            }
        }

        initNetworkListeners() {
            window.addEventListener('online', () => {
                console.log('[OfflineEngine] Browser event: ONLINE');
                // JANGAN langsung reportNetworkSuccess — verifikasi dulu via ping
                // Ini mencegah false positive ketika Wi-Fi reconnect tapi server belum reachable
                this.verifyConnectivity().then(online => {
                    if (online) {
                        this.reportNetworkSuccess(true); // fromVerifiedPing = true
                    }
                });
            });

            window.addEventListener('offline', () => {
                console.warn('[OfflineEngine] Browser event: OFFLINE');
                this._browserOfflineLock = true; // Kunci agar AJAX stale tidak bisa override
                this.reportNetworkFailure();
            });
        }

        async pingServer() {
            if (!navigator.onLine) return false;

            let cleanBase = (typeof BASE_URL !== 'undefined' && BASE_URL) ? BASE_URL.replace(/\/+$/, '') : '';
            if (!cleanBase) {
                const m = window.location.pathname.match(/^(\/[^\/]+\/)/);
                cleanBase = window.location.origin + (m ? m[1] : '/');
                cleanBase = cleanBase.replace(/\/+$/, '');
            }

            // Bangun kandidat endpoint ping yang valid (dengan dan tanpa index.php)
            const candidates = [];
            if (this.pingEndpoint) {
                candidates.push(this.pingEndpoint);
            }

            // 1. Standar REST URL
            candidates.push(cleanBase + '/api/sync/ping');
            // 2. Fallback URL dengan index.php
            candidates.push(cleanBase + '/index.php/api/sync/ping');

            // 3. Fallback jika root direktori berbeda dari BASE_URL
            const originBase = window.location.origin + window.location.pathname.split('/').slice(0, 3).join('/');
            if (originBase !== cleanBase) {
                candidates.push(originBase + '/api/sync/ping');
                candidates.push(originBase + '/index.php/api/sync/ping');
            }

            const uniqueCandidates = [...new Set(candidates)];

            for (const url of uniqueCandidates) {
                // Setiap kandidat mendapat AbortController sendiri agar timeout tidak terkuras oleh kandidat sebelumnya
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 2000);
                try {
                    const res = await fetch(url, {
                        method: 'GET',
                        cache: 'no-store',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        signal: controller.signal
                    });
                    clearTimeout(timeoutId);
                    if (res.ok) {
                        this.pingEndpoint = url; // Simpan endpoint aktif yang terbukti berhasil
                        return true;
                    }
                } catch (err) {
                    clearTimeout(timeoutId);
                    // Coba kandidat berikutnya
                }
            }

            return false;
        }

        async verifyConnectivity() {
            if (!navigator.onLine) return false;
            return await this.pingServer();
        }

        startHeartbeat() {
            const check = async () => {
                if (this.isSyncing) return;

                // 1. Jika browser sendiri menyatakan offline (kabel LAN / Wi-Fi terputus)
                if (!navigator.onLine) {
                    this._browserOfflineLock = true;
                    this.reportNetworkFailure();
                    return;
                }

                // 2. Ping server untuk verifikasi backend aktif
                const isPingOk = await this.pingServer();
                if (isPingOk) {
                    this.reportNetworkSuccess(true); // fromVerifiedPing = true, bisa buka lock
                } else {
                    // Langsung offline tanpa menunggu 2 kegagalan jika browser sudah offline lock
                    if (this._browserOfflineLock) {
                        this.reportNetworkFailure();
                    } else {
                        this.consecutiveFailures = (this.consecutiveFailures || 0) + 1;
                        if (this.consecutiveFailures >= 2 && this.isOnline) {
                            this.reportNetworkFailure();
                        }
                    }
                }
            };

            // Interval realtime 3 detik untuk deteksi instan tanpa membebani CPU
            this.heartbeatInterval = setInterval(check, 3000);
            check();
        }

        setOnlineState(online) {
            this.isOnline = online;
            const $badge = $('#navbar-network-status');
            const $existingBanner = $('#offline-sticky-banner');
            const $contentWrapper = $('.content-wrapper');

            const countBadge = (this.draftCount > 0)
                ? `<span id="offline-draft-count" class="badge badge-warning text-dark ml-1 font-weight-bold" style="font-size: 10px; border-radius: 10px; padding: 2px 6px;">${this.draftCount}</span>`
                : `<span id="offline-draft-count" class="badge badge-warning text-dark ml-1 d-none font-weight-bold" style="font-size: 10px; border-radius: 10px; padding: 2px 6px;">0</span>`;

            if (online) {
                // Indikator pill navbar hijau (Online)
                if ($badge.length) {
                    $badge.html(`<span class="online-indicator-dot mr-1.5"></span><i class="fas fa-wifi mr-1 text-success" style="font-size: 11px;"></i> <span>Online</span> ${countBadge}`);
                }
                // HAPUS BANNER BERSIH agar tidak pernah menutupi header atau konten operasional
                if ($existingBanner.length) {
                    $existingBanner.remove();
                }
            } else {
                // Indikator pill navbar oranye (Offline)
                if ($badge.length) {
                    $badge.html(`<span class="online-indicator-dot mr-1.5" style="background:#f59e0b;"></span><i class="fas fa-plug text-warning mr-1" style="font-size: 11px;"></i> <span class="text-warning font-weight-bold">Offline</span> ${countBadge}`);
                }

                // TAMPILKAN BANNER DI DALAM .content-wrapper (DI BAWAH HEADER NAVBAR, BUKAN DI ATAS BODY)
                // Memastikan HEADER NAVBAR TETAP 100% BEBAS & BISA DIGUNAKAN
                if (!$existingBanner.length && $contentWrapper.length) {
                    $contentWrapper.prepend(`
                        <div id="offline-sticky-banner" class="alert alert-warning alert-dismissible fade show m-3 shadow-sm border-0 animate__animated animate__fadeInDown" role="alert" style="border-left: 5px solid #d97706 !important; background: #fffbeb; color: #92400e; z-index: 10; font-size: 13px;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-bolt text-danger mr-2 fa-lg"></i>
                                    <span><strong>Mode Offline (Koneksi Terputus)</strong> &mdash; Seluruh transaksi tetap dapat diinput ke tabel lokal dan otomatis disinkronkan saat online.</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-xs btn-dark mr-2 font-weight-bold shadow-sm" onclick="window.offlineSyncEngine.openQueueModal()">
                                        <i class="fas fa-list mr-1"></i> Buka Antrean Draf (<span id="banner-draft-count">${this.draftCount}</span>)
                                    </button>
                                    <button type="button" class="close position-static p-0 ml-2" data-dismiss="alert" aria-label="Tutup" style="font-size: 20px; line-height: 1; opacity: 0.7;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                } else if ($existingBanner.length) {
                    $('#banner-draft-count').text(this.draftCount);
                    $existingBanner.show();
                }
            }
        }

        /**
         * 9. Notifikasi & Audio Draf
         */
        notifyOfflineSaved(title) {
            this.playOfflineChime();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '<span style="color:#0d9488;"><i class="fas fa-check-circle mr-1"></i> Data Masuk ke Tabel & Draf Lokal!</span>',
                    html: `
                        <p class="text-secondary mb-2" style="font-size:14px;">
                            <strong>${title}</strong> telah langsung ditampilkan di tabel kerja dan dicadangkan aman di komputer ini.
                        </p>
                        <div class="alert alert-info text-left py-2 px-3 small font-weight-bold mb-0">
                            <i class="fas fa-info-circle mr-1"></i> Anda dapat terus melanjutkan aktivitas penginputan. Saat listrik/internet menyala kembali, sistem akan otomatis mengirim seluruh draf ke database MySQL.
                        </div>
                    `,
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Lanjutkan Bekerja',
                    confirmButtonColor: '#0d9488'
                });
            } else {
                alert(`[MODE OFFLINE]\nData (${title}) berhasil disimpan ke Draf Lokal & masuk ke tabel.\nSistem akan otomatis mengirimkannya ke database saat listrik/internet pulih.`);
            }

            this.updateDraftCount();
        }

        playOfflineChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(330, ctx.currentTime + 0.3);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) {}
        }

        playSyncSuccessChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;
                [523.25, 659.25, 783.99].forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + i * 0.12);
                    gain.gain.setValueAtTime(0.15, now + i * 0.12);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.12 + 0.25);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + i * 0.12);
                    osc.stop(now + i * 0.12 + 0.25);
                });
            } catch (e) {}
        }

        /**
         * 10. FIFO Auto-Sync Engine
         */
        async triggerAutoSync() {
            if (this.isSyncing) return;
            const drafts = await this.getPendingDrafts();
            if (!drafts.length) return;

            this.isSyncing = true;
            const $badge = $('#navbar-network-status');
            $badge.html('<span class="online-indicator-dot mr-1.5" style="background:#0284c7;"></span><i class="fas fa-sync fa-spin mr-1 text-primary" style="font-size: 11px;"></i> <span class="text-primary font-weight-bold">Menyinkronkan (' + drafts.length + ')...</span>');

            console.log(`[OfflineEngine] Memulai sinkronisasi otomatis ${drafts.length} draf ke server...`);

            let csrfToken = null;
            let csrfName = 'csrf_test_name';
            const fetchCsrf = async () => {
                try {
                    let cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
                    let csrfUrl = cleanBase + '/api/sync/csrf-token';
                    if (this.pingEndpoint && this.pingEndpoint.includes('index.php')) {
                        csrfUrl = cleanBase + '/index.php/api/sync/csrf-token';
                    }
                    const csrfRes = await fetch(csrfUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (csrfRes.ok) {
                        const csrfData = await csrfRes.json();
                        csrfName = csrfData.csrf_name || 'csrf_test_name';
                        csrfToken = csrfData.csrf_hash || null;
                    }
                } catch (e) {}
            };

            await fetchCsrf();

            let successCount = 0;
            let failCount = 0;

            for (const draft of drafts) {
                try {
                    let postData = this.normalizePayload(draft);

                    if (csrfToken) {
                        postData[csrfName] = csrfToken;
                    }

                    const res = await fetch(draft.url, {
                        method: draft.method || 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams(postData).toString()
                    });

                    if (res.ok) {
                        await this.deleteDraft(draft.id);
                        successCount++;
                        await fetchCsrf();
                    } else {
                        failCount++;
                    }
                } catch (err) {
                    failCount++;
                }
            }

            this.isSyncing = false;
            await this.updateDraftCount();
            this.setOnlineState(this.isOnline);
            this.renderDraftsListInModal();

            if (successCount > 0) {
                this.playSyncSuccessChime();
                if (typeof toastr !== 'undefined') {
                    toastr.success(`Berhasil menyinkronkan ${successCount} transaksi draf offline ke database server.`, 'Sinkronisasi Sukses');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sinkronisasi Otomatis Sukses!',
                        text: `Sebanyak ${successCount} transaksi draf offline telah berhasil diproses ke database server.`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }

                // Segarkan LiveSync jika online
                if (window.LiveSyncEngine) {
                    window.LiveSyncEngine.pollNow();
                }
            }
        }

        async syncSingleDraft(id) {
            const drafts = await this.getPendingDrafts();
            const draft = drafts.find(d => d.id == id);
            if (!draft) return false;

            let csrfToken = null;
            let csrfName = 'csrf_test_name';
            try {
                let cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');
                let csrfUrl = cleanBase + '/api/sync/csrf-token';
                if (this.pingEndpoint && this.pingEndpoint.includes('index.php')) {
                    csrfUrl = cleanBase + '/index.php/api/sync/csrf-token';
                }
                const csrfRes = await fetch(csrfUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (csrfRes.ok) {
                    const csrfData = await csrfRes.json();
                    csrfName = csrfData.csrf_name || 'csrf_test_name';
                    csrfToken = csrfData.csrf_hash || null;
                }
            } catch (e) {}

            try {
                let postData = this.normalizePayload(draft);

                if (csrfToken) {
                    postData[csrfName] = csrfToken;
                }

                const res = await fetch(draft.url, {
                    method: draft.method || 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(postData).toString()
                });

                if (res.ok) {
                    await this.deleteDraft(draft.id);
                    await this.updateDraftCount();
                    this.playSyncSuccessChime();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(`Draf "${this.escapeHtml(draft.title)}" berhasil disinkronkan.`, 'Sukses');
                    }
                    if (window.LiveSyncEngine) {
                        window.LiveSyncEngine.pollNow();
                    }
                    return true;
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(`Gagal menyinkronkan draf (${res.status} ${res.statusText}).`, 'Gagal');
                    }
                    return false;
                }
            } catch (err) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Koneksi server gagal.', 'Gagal');
                }
                return false;
            }
        }

        normalizePayload(draft) {
            let data = draft.payload || draft.data || draft.raw_serialized || {};
            if (typeof data === 'string') {
                const trimmed = data.trim();
                if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
                    try {
                        return JSON.parse(trimmed);
                    } catch (e) {}
                }
                return this.parseQueryString(trimmed);
            }
            if (typeof data === 'object' && data !== null) {
                return Object.assign({}, data);
            }
            return {};
        }

        parseQueryString(queryString) {
            const params = {};
            if (!queryString || typeof queryString !== 'string') return params;
            const queries = queryString.split('&');
            for (let i = 0; i < queries.length; i++) {
                if (!queries[i]) continue;
                const pair = queries[i].split('=');
                try {
                    params[decodeURIComponent(pair[0].replace(/\+/g, ' '))] = decodeURIComponent((pair[1] || '').replace(/\+/g, ' '));
                } catch (e) {
                    params[pair[0]] = pair[1] || '';
                }
            }
            return params;
        }

        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        saveDraft(draftData) {
            draftData.status = 'pending';
            draftData.created_at = new Date().toISOString();
            draftData.retry_count = 0;

            return new Promise((resolve) => {
                if (this.db) {
                    try {
                        const tx = this.db.transaction([STORE_QUEUE], 'readwrite');
                        const store = tx.objectStore(STORE_QUEUE);
                        const req = store.add(draftData);
                        req.onsuccess = (e) => {
                            this.updateDraftCount();
                            resolve(e.target.result);
                        };
                        req.onerror = () => {
                            this.saveDraftFallback(draftData);
                            resolve();
                        };
                        return;
                    } catch (e) {}
                }
                this.saveDraftFallback(draftData);
                resolve();
            });
        }

        saveDraftFallback(draftData) {
            try {
                const list = JSON.parse(localStorage.getItem('sw_offline_queue') || '[]');
                draftData.id = Date.now();
                list.push(draftData);
                localStorage.setItem('sw_offline_queue', JSON.stringify(list));
                this.updateDraftCount();
            } catch (e) {}
        }

        getPendingDrafts() {
            return new Promise((resolve) => {
                if (this.db) {
                    try {
                        const tx = this.db.transaction([STORE_QUEUE], 'readonly');
                        const store = tx.objectStore(STORE_QUEUE);
                        const req = store.getAll();
                        req.onsuccess = () => resolve(req.result || []);
                        req.onerror = () => resolve(this.getPendingDraftsFallback());
                        return;
                    } catch (e) {}
                }
                resolve(this.getPendingDraftsFallback());
            });
        }

        getPendingDraftsFallback() {
            try {
                return JSON.parse(localStorage.getItem('sw_offline_queue') || '[]');
            } catch (e) {
                return [];
            }
        }

        deleteDraft(id) {
            return new Promise((resolve) => {
                if (this.db) {
                    try {
                        const tx = this.db.transaction([STORE_QUEUE], 'readwrite');
                        const store = tx.objectStore(STORE_QUEUE);
                        const req = store.delete(id);
                        req.onsuccess = () => {
                            this.updateDraftCount();
                            resolve();
                        };
                        req.onerror = () => resolve();
                        return;
                    } catch (e) {}
                }
                try {
                    let list = JSON.parse(localStorage.getItem('sw_offline_queue') || '[]');
                    list = list.filter(item => item.id !== id);
                    localStorage.setItem('sw_offline_queue', JSON.stringify(list));
                    this.updateDraftCount();
                } catch (e) {}
                resolve();
            });
        }

        async clearAllDrafts() {
            if (this.db) {
                try {
                    const tx = this.db.transaction([STORE_QUEUE], 'readwrite');
                    tx.objectStore(STORE_QUEUE).clear();
                } catch (e) {}
            }
            try {
                localStorage.removeItem('sw_offline_queue');
            } catch (e) {}
            this.updateDraftCount();
        }

        async updateDraftCount() {
            const drafts = await this.getPendingDrafts();
            this.draftCount = drafts.length;

            const $badge = $('#offline-draft-count');
            const $btn = $('#btn-open-offline-queue');
            const $modalCount = $('#offline-modal-count');
            const $bannerCount = $('#banner-draft-count');

            if ($modalCount.length) $modalCount.text(this.draftCount);
            if ($bannerCount.length) $bannerCount.text(this.draftCount);

            if ($badge.length) {
                $badge.text(this.draftCount);
                if (this.draftCount > 0) {
                    $badge.removeClass('d-none badge-secondary').addClass('badge-warning font-weight-bold animate__animated animate__pulse');
                    $btn.removeClass('d-none');
                } else {
                    $badge.addClass('d-none');
                }
            }
        }

        /**
         * 11. Modal Manajemen Draf Offline
         */
        initUI() {
            const self = this;

            $(document).on('click', '#btn-open-offline-queue', function () {
                self.openQueueModal();
            });

            $(document).on('click', '#btn-manual-sync-offline', function () {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sinkronisasi...');
                self.triggerAutoSync().then(() => {
                    setTimeout(() => {
                        $('#btn-manual-sync-offline').prop('disabled', false).html('<i class="fas fa-sync mr-1"></i> Sinkronkan Sekarang');
                        self.renderDraftsListInModal();
                    }, 800);
                });
            });

            $(document).on('click', '#btn-clear-all-offline', function () {
                if (confirm('Apakah Anda yakin ingin menghapus semua draf offline yang belum tersinkron? Tindakan ini tidak dapat dibatalkan.')) {
                    self.clearAllDrafts().then(() => {
                        self.renderDraftsListInModal();
                    });
                }
            });

            $(document).on('click', '.btn-delete-single-draft', async function () {
                const id = $(this).data('id');
                if (confirm('Hapus draf transaksi ini?')) {
                    await self.deleteDraft(id);
                    self.renderDraftsListInModal();
                }
            });

            $(document).on('click', '.btn-sync-single-draft', async function () {
                const id = $(this).data('id');
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                await self.syncSingleDraft(id);
                self.renderDraftsListInModal();
            });

            $(document).on('click', '#btn-purge-local-cache', async function () {
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Membersihkan...');
                await self.purgeLocalCaches(true);
                setTimeout(() => {
                    $btn.prop('disabled', false).html('<i class="fas fa-broom mr-1"></i> Bersihkan Cache');
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Cache tampilan dan antrean lokal berhasil disegarkan.', 'Cache Bersih');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cache Lokal Berhasil Dikosongkan!',
                            text: 'Seluruh riwayat tampilan antrean lokal yang usang telah dibersihkan secara sempurna.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }, 500);
            });

            $(document).on('click', '.btn-force-update, #btn-force-update-system', function (e) {
                e.preventDefault();
                self.forceUpdateSystem(true);
            });
        }

        /**
         * Membersihkan semua cache lokal browser (IndexedDB View, LocalStorage Cache, CacheStorage SW, SessionStorage)
         */
        async purgeLocalCaches(preserveQueue = true) {
            console.log('[OfflineEngine] Memulai pembersihan cache lokal...');

            // 1. Bersihkan memory cache in RAM
            this.viewsCache = {};
            this.patientsCache = [];
            this.queuesCache = [];
            this.billingsCache = [];
            this.prescriptionsCache = [];

            // 2. Bersihkan LocalStorage yang terkait cache
            try {
                const keysToRemove = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (
                        key.startsWith('sw_view_') ||
                        key === 'sw_patients_cache' ||
                        key === 'sw_queues_cache' ||
                        key === 'sw_billings_cache' ||
                        key === 'sw_prescriptions_cache' ||
                        (!preserveQueue && key === 'sw_offline_queue')
                    )) {
                        keysToRemove.push(key);
                    }
                }
                keysToRemove.forEach(k => localStorage.removeItem(k));
            } catch (e) {
                console.warn('[OfflineEngine] Gagal membersihkan localStorage:', e);
            }

            // 3. Bersihkan SessionStorage
            try {
                sessionStorage.clear();
            } catch (e) {}

            // 4. Bersihkan IndexedDB Stores (kecuali queue jika preserveQueue == true)
            if (this.db) {
                const storesToClear = [STORE_VIEWS, STORE_PATIENTS, STORE_QUEUES, STORE_BILLINGS, STORE_PRESCRIPTIONS, STORE_MASTER];
                if (!preserveQueue) {
                    storesToClear.push(STORE_QUEUE);
                }

                for (const storeName of storesToClear) {
                    try {
                        if (this.db.objectStoreNames.contains(storeName)) {
                            const tx = this.db.transaction([storeName], 'readwrite');
                            tx.objectStore(storeName).clear();
                        }
                    } catch (e) {
                        console.warn(`[OfflineEngine] Gagal mengosongkan store ${storeName}:`, e);
                    }
                }
            }

            // 5. Bersihkan CacheStorage (Service Worker Caches)
            if ('caches' in window) {
                try {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    console.log('[OfflineEngine] CacheStorage browser berhasil dikosongkan.');
                } catch (e) {
                    console.warn('[OfflineEngine] Gagal menghapus CacheStorage:', e);
                }
            }

            this.updateDraftCount();
            return true;
        }

        /**
         * Paksa Perbarui Sistem & Bersihkan Cache Menyeluruh (Client + Server)
         * Mengatasi kendala perangkat/komputer yang tertahan menggunakan kode lama / stale cache.
         */
        async forceUpdateSystem(interactive = true) {
            const self = this;
            const cleanBase = (typeof BASE_URL !== 'undefined' ? BASE_URL.replace(/\/+$/, '') : '');

            // Cek apakah ada draf offline tertunda
            const drafts = await self.getPendingDrafts();
            const draftCount = drafts.length;

            const executeUpdate = async () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Memperbarui Sistem &amp; Kode...',
                        html: `
                            <div class="py-2 text-left" style="font-size: 13px; line-height: 1.6;">
                                <div id="update-step-server" class="mb-2 text-primary font-weight-bold">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> 1. Menyegarkan Cache Server &amp; OPcache PHP...
                                </div>
                                <div id="update-step-storage" class="mb-2 text-muted">
                                    <i class="far fa-circle mr-2"></i> 2. Mengosongkan CacheStorage &amp; Service Worker...
                                </div>
                                <div id="update-step-local" class="mb-2 text-muted">
                                    <i class="far fa-circle mr-2"></i> 3. Membersihkan IndexedDB &amp; Local View Cache...
                                </div>
                                <div id="update-step-reload" class="text-muted">
                                    <i class="far fa-circle mr-2"></i> 4. Mengunduh kode &amp; aset versi terbaru...
                                </div>
                            </div>
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: async () => {
                            Swal.showLoading();

                            // Step 1: Bersihkan Cache Server & OPcache
                            try {
                                await fetch(cleanBase + '/system/clear-cache?format=json', {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                            } catch (e) {
                                console.warn('[OfflineEngine] Server clear-cache request error:', e);
                            }

                            $('#update-step-server').removeClass('text-primary font-weight-bold').addClass('text-success').html('<i class="fas fa-check-circle mr-2"></i> 1. Cache Server &amp; OPcache Berhasil Dikosongkan');
                            $('#update-step-storage').removeClass('text-muted').addClass('text-primary font-weight-bold').html('<i class="fas fa-spinner fa-spin mr-2"></i> 2. Mengosongkan CacheStorage &amp; Service Worker...');

                            await new Promise(r => setTimeout(r, 200));

                            // Step 2: Unregister Service Worker & Clear CacheStorage
                            if ('serviceWorker' in navigator) {
                                try {
                                    const regs = await navigator.serviceWorker.getRegistrations();
                                    for (const r of regs) {
                                        await r.unregister();
                                    }
                                } catch (e) {}
                            }

                            if ('caches' in window) {
                                try {
                                    const cacheNames = await caches.keys();
                                    for (const c of cacheNames) {
                                        await caches.delete(c);
                                    }
                                } catch (e) {}
                            }

                            $('#update-step-storage').removeClass('text-primary font-weight-bold').addClass('text-success').html('<i class="fas fa-check-circle mr-2"></i> 2. CacheStorage &amp; Service Worker Segar');
                            $('#update-step-local').removeClass('text-muted').addClass('text-primary font-weight-bold').html('<i class="fas fa-spinner fa-spin mr-2"></i> 3. Membersihkan IndexedDB &amp; Local View Cache...');

                            await new Promise(r => setTimeout(r, 200));

                            // Step 3: Purge Local caches (preserve pending drafts)
                            await self.purgeLocalCaches(true);

                            $('#update-step-local').removeClass('text-primary font-weight-bold').addClass('text-success').html('<i class="fas fa-check-circle mr-2"></i> 3. IndexedDB &amp; Data Lokal Bersih');
                            $('#update-step-reload').removeClass('text-muted').addClass('text-success font-weight-bold').html('<i class="fas fa-arrows-rotate fa-spin mr-2"></i> 4. Memuat Ulang Sistem dengan Kode Terbaru...');

                            await new Promise(r => setTimeout(r, 400));

                            // Step 4: Broadcast sinyal ke seluruh tab lain di perangkat ini & Reload
                            const timestamp = Date.now();

                            try {
                                if (self.systemChannel) {
                                    self.systemChannel.postMessage({ action: 'FORCE_UPDATE_SIGNAL', timestamp: timestamp });
                                }
                            } catch (e) {}

                            try {
                                localStorage.setItem('sawamawa_force_update_broadcast', timestamp.toString());
                            } catch (e) {}

                            const url = new URL(window.location.href);
                            url.searchParams.set('_force_update', timestamp);
                            window.location.href = url.toString();
                        }
                    });
                } else {
                    // Fallback jika SweetAlert2 tidak ada
                    alert('Memulai pembersihan cache dan pembaruan kode sistem...');
                    await self.purgeLocalCaches(true);
                    if ('serviceWorker' in navigator) {
                        const regs = await navigator.serviceWorker.getRegistrations();
                        for (const r of regs) await r.unregister();
                    }
                    if ('caches' in window) {
                        const keys = await caches.keys();
                        for (const k of keys) await caches.delete(k);
                    }
                    window.location.reload(true);
                }
            };

            if (!interactive) {
                return executeUpdate();
            }

            if (typeof Swal !== 'undefined') {
                let warningText = 'Seluruh cache browser di komputer ini (Service Worker, Cache Storage, Tampilan Antrean Lokal, dan OPcache Server) akan disegarkan agar langsung menerima pembaruan fitur dan perbaikan bug terbaru.';
                if (draftCount > 0) {
                    warningText += `<br><br><span class="text-warning font-weight-bold"><i class="fas fa-triangle-exclamation mr-1"></i> Perhatian:</span> Terdapat <strong>${draftCount} draf transaksi offline</strong> yang belum tersinkron. Draf Anda akan tetap aman dipertahankan.`;
                }

                Swal.fire({
                    title: 'Paksa Perbarui Sistem?',
                    html: warningText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-arrows-rotate mr-1"></i> Ya, Perbarui Sekarang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeUpdate();
                    }
                });
            } else {
                if (confirm('Perbarui sistem dan hapus seluruh cache komputer ini untuk mendapatkan kode terbaru?')) {
                    executeUpdate();
                }
            }
        }

        async openQueueModal() {
            await this.renderDraftsListInModal();
            $('#modal-offline-queue').modal('show');
        }

        async renderDraftsListInModal() {
            const drafts = await this.getPendingDrafts();
            const $tbody = $('#offline-queue-tbody');
            const $container = $('#offline-queue-list-container');
            const $count = $('#offline-modal-count');

            if ($count.length) $count.text(drafts.length);

            // 1. Jika modal menggunakan Table (standar layout.php)
            if ($tbody.length) {
                if (drafts.length === 0) {
                    $tbody.html(`
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-cloud-check text-success fa-3x mb-2 d-block"></i>
                                <h6 class="font-weight-bold text-dark mb-1">Semua Data Telah Tersinkronkan</h6>
                                <p class="text-secondary small mb-0">Tidak ada draf transaksi yang tertahan di komputer lokal ini.</p>
                            </td>
                        </tr>
                    `);
                    return;
                }

                let rowsHtml = '';
                drafts.forEach((d, idx) => {
                    const date = new Date(d.created_at || Date.now()).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    const endpoint = (d.url || '').split('/').pop() || 'transaksi';
                    rowsHtml += `
                        <tr>
                            <td class="text-center font-weight-bold">${idx + 1}</td>
                            <td class="text-muted"><i class="fas fa-clock mr-1"></i> ${date}</td>
                            <td>
                                <strong class="text-dark d-block">${this.escapeHtml(d.title || 'Transaksi Formulir')}</strong>
                                <small class="text-secondary font-monospace"><i class="fas fa-link mr-1"></i> ${this.escapeHtml(endpoint)}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="fas fa-hourglass-half mr-1"></i> Draf</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-xs">
                                    <button type="button" class="btn btn-xs btn-teal btn-sync-single-draft mr-1" data-id="${d.id}" title="Sinkronkan Item Ini">
                                        <i class="fas fa-upload"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-delete-single-draft" data-id="${d.id}" title="Hapus Draf">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $tbody.html(rowsHtml);
            }

            // 2. Jika modal menggunakan List-Group container (fallback)
            if ($container.length) {
                if (drafts.length === 0) {
                    $container.html(`
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-cloud-check fa-3x text-teal mb-3"></i>
                            <h6 class="font-weight-bold text-dark mb-1">Semua Data Telah Tersinkronkan</h6>
                            <p class="text-secondary small mb-0">Tidak ada data transaksi yang tertahan di komputer lokal ini.</p>
                        </div>
                    `);
                    return;
                }

                let html = '<div class="list-group list-group-flush">';
                drafts.forEach((d, idx) => {
                    const date = new Date(d.created_at || Date.now()).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    html += `
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-warning text-dark font-weight-bold mr-2">#${idx + 1}</span>
                                    <h6 class="font-weight-bold text-dark mb-0">${d.title || 'Transaksi Formulir'}</h6>
                                </div>
                                <small class="text-muted"><i class="fas fa-clock mr-1"></i> Disimpan pukul: ${date} &bull; Target: <code>${(d.url || '').split('/').pop()}</code></small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-xs btn-teal btn-sync-single-draft mr-1" data-id="${d.id}" title="Sinkronkan"><i class="fas fa-upload"></i></button>
                                <button type="button" class="btn btn-xs btn-outline-danger btn-delete-single-draft" data-id="${d.id}" title="Hapus"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $container.html(html);
            }
        }
    }

    // Inisialisasi Singleton Engine di Window Global
    $(document).ready(function () {
        window.offlineSyncEngine = new OfflineSyncEngine();
    });

    // Helper Global untuk Paksa Perbarui Sistem di luar konteks engine
    window.forceUpdateSystem = function (interactive = true) {
        if (window.offlineSyncEngine && typeof window.offlineSyncEngine.forceUpdateSystem === 'function') {
            return window.offlineSyncEngine.forceUpdateSystem(interactive);
        }
        if (confirm('Paksa perbarui sistem dan bersihkan cache komputer ini sekarang?')) {
            if ('caches' in window) {
                caches.keys().then(keys => Promise.all(keys.map(k => caches.delete(k))));
            }
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(regs => Promise.all(regs.map(r => r.unregister())));
            }
            sessionStorage.clear();
            const url = new URL(window.location.href);
            url.searchParams.set('_force_update', Date.now());
            window.location.href = url.toString();
        }
    };

})(window, jQuery);
