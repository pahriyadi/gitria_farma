/**
 * ====================================================================================================
 * SERVICE WORKER: SAWAMAWA MEDICAL CENTER
 * Offline Navigation & Full PWA Page Cache Strategy
 * 
 * Strategi:
 * 1. Navigasi / Halaman Menu (HTML): Network-First, Fallback ke Cache Storage.
 *    -> Saat online: Mengambil halaman terbaru & menyimpannya ke cache.
 *    -> Saat offline: Mengembalikan halaman dari cache sehingga semua menu sidebar tetap bisa dibuka!
 * 2. Static Assets (CSS, JS, Fonts, Images): Cache-First / Stale-While-Revalidate.
 * ====================================================================================================
 */

const CACHE_STATIC_NAME = 'sawamawa-static-v2';
const CACHE_PAGES_NAME = 'sawamawa-pages-v2';

const STATIC_ASSETS = [
    'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
    'https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css',
    'https://cdn.jsdelivr.net/npm/chart.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/id.min.js',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11'
];

// 1. Install Event: Cache Core Assets
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_STATIC_NAME).then((cache) => {
            console.log('[SW] Pre-caching static assets...');
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Beberapa aset gagal di-precache:', err);
            });
        })
    );
});

// 2. Activate Event: Clean Old Caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        Promise.all([
            self.clients.claim(),
            caches.keys().then((keys) => {
                return Promise.all(
                    keys.map((key) => {
                        if (key !== CACHE_STATIC_NAME && key !== CACHE_PAGES_NAME) {
                            console.log('[SW] Menghapus cache lama:', key);
                            return caches.delete(key);
                        }
                    })
                );
            })
        ])
    );
});

// 2.1 Message Event: Handle force update signals from client
self.addEventListener('message', (event) => {
    if (!event.data) return;
    if (event.data.action === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (event.data.action === 'CLEAR_ALL_CACHES') {
        caches.keys().then((keys) => {
            return Promise.all(keys.map((k) => caches.delete(k)));
        });
    }
});

// 3. Fetch Event: Handle Requests
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Abaikan non-GET request (POST / PUT ditangani oleh offline-sync-engine.js)
    if (request.method !== 'GET') {
        return;
    }

    // Abaikan API sync polling agar tidak bentrok dengan background LiveSync
    if (url.pathname.includes('/api/sync/')) {
        return;
    }

    // A. Strategi untuk Navigasi Halaman HTML (Menu Sidebar / URL Navigasi)
    if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Jika online: kembalikan halaman dan simpan salinan ke cache
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_PAGES_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Jika offline (listrik/internet mati): ambil halaman dari cache!
                    console.log('[SW] Offline mode: Menyajikan halaman dari cache untuk', request.url);
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Jika URL spesifik belum pernah dikunjungi, coba ambil halaman dashboard yang tersimpan
                    const dashboardFallback = await caches.match('/sawamawamedicalcenter.id/public/dashboard') ||
                                             await caches.match('/dashboard');
                    if (dashboardFallback) {
                        return dashboardFallback;
                    }

                    // Fallback terakhir: Tampilkan template offline darurat
                    return new Response(`
                        <!DOCTYPE html>
                        <html lang="id">
                        <head>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <title>Mode Offline - Sawamawa Medical Center</title>
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                        </head>
                        <body class="hold-transition bg-light p-4">
                            <div class="container text-center py-5">
                                <div class="card shadow-lg border-0 mx-auto" style="max-width: 600px; border-radius: 12px;">
                                    <div class="card-body p-5">
                                        <div class="mb-4">
                                            <i class="fas fa-plug text-warning" style="font-size: 54px;"></i>
                                        </div>
                                        <h3 class="font-weight-bold text-dark mb-2">Mode Offline (Listrik / Jaringan Terputus)</h3>
                                        <p class="text-secondary mb-4">
                                            Halaman menu ini belum tersimpan di memori lokal. Silakan gunakan tombol navigasi menu operasional yang telah diamankan di bawah ini:
                                        </p>
                                        <div class="d-grid gap-2">
                                            <a href="javascript:history.back()" class="btn btn-secondary btn-block font-weight-bold mb-2">
                                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Sebelumnya
                                            </a>
                                            <a href="./dashboard" class="btn btn-teal btn-block font-weight-bold text-white mb-2" style="background-color:#0d9488;">
                                                <i class="fas fa-chart-pie mr-1"></i> Buka Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </body>
                        </html>
                    `, {
                        headers: { 'Content-Type': 'text/html; charset=utf-8' }
                    });
                })
        );
        return;
    }

    // B. Strategi untuk Static Assets (CSS, JS, Fonts, Images)
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                // Fetch di background untuk update cache (Stale-While-Revalidate)
                fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_STATIC_NAME).then((cache) => {
                            cache.put(request, networkResponse);
                        });
                    }
                }).catch(() => {});
                return cachedResponse;
            }

            return fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_STATIC_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return networkResponse;
            }).catch(() => {
                // Return empty if asset unavailable
            });
        })
    );
});
