<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | <?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></title>
    <meta name="description" content="Peta alur kerja sistem ERP dalam bentuk kantor 3D: lihat permintaan dan pengembalian data bergerak antar modul, lalu atur alur sendiri.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/alur-3d.css?v=' . (file_exists(FCPATH . 'assets/css/alur-3d.css') ? filemtime(FCPATH . 'assets/css/alur-3d.css') : time())) ?>">
    <script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
        }
    }
    </script>
</head>
<body>
<a class="skip-link" href="#narration">Lewati ke narasi alur</a>

<header class="topbar">
    <a class="btn btn-ghost" href="<?= base_url('bantuan') ?>">
        <i class="fas fa-arrow-left" aria-hidden="true"></i><span class="hide-sm">Pusat Bantuan</span>
    </a>
    <div class="topbar__title">
        <h1>Peta Alur Sistem 3D</h1>
        <p><?= esc(clinic_setting('clinic_name', 'Sawamawa Medical Center')) ?></p>
    </div>
    <div class="topbar__actions">
        <button type="button" class="btn btn-ghost" id="btnResetCam" title="Kembalikan sudut kamera (C)">
            <i class="fas fa-expand" aria-hidden="true"></i><span class="hide-sm">Reset kamera</span>
        </button>
        <button type="button" class="btn btn-ghost" id="btnHelp" title="Cara pakai & pintasan keyboard (?)" aria-label="Cara pakai">
            <i class="fas fa-keyboard" aria-hidden="true"></i>
        </button>
        <button type="button" class="btn btn-primary" id="btnEditor" aria-expanded="false" aria-controls="editorDrawer">
            <i class="fas fa-sliders" aria-hidden="true"></i><span>Atur Alur</span>
        </button>
    </div>
</header>

<main class="layout">
    <div id="stage" class="stage">
        <div id="loader" class="stage-state" role="status">
            <div class="spinner" aria-hidden="true"></div>
            <p>Menyiapkan kantor 3D…</p>
        </div>
        <div id="errorState" class="stage-state stage-state--error" role="alert" hidden>
            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
            <p id="errorText"></p>
            <div class="row-gap">
                <button type="button" class="btn btn-primary" onclick="location.reload()">Muat ulang halaman</button>
                <a class="btn btn-ghost" href="<?= base_url('bantuan') ?>">Kembali ke Pusat Bantuan</a>
            </div>
        </div>
        <div id="emptyOffice" class="stage-state" hidden>
            <p>Semua ruangan sedang disembunyikan. Buka <strong>Atur Alur → Ruangan</strong> dan centang "Tampil" minimal pada satu ruangan.</p>
        </div>
        <button type="button" class="btn btn-ghost btn-expand-narration" id="btnExpandNarration" title="Buka panel keterangan alur (K)" aria-label="Buka keterangan" hidden>
            <i class="fas fa-list-ol" aria-hidden="true"></i><span>Keterangan Alur</span>
        </button>
    </div>

    <aside id="narration" class="narration" aria-label="Narasi alur">
        <div class="narration__head">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="scenarioSelect" class="eyebrow-label mb-0">Skenario</label>
                <button type="button" class="icon-btn icon-btn--sm" id="btnCollapseNarration" title="Ciutkan panel keterangan (K)" aria-label="Ciutkan panel">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
            </div>
            <select id="scenarioSelect"></select>
            <p id="scenarioDesc" class="muted"></p>
        </div>
        <section id="nowCard" class="now-card" aria-live="polite"></section>
        <div class="legend" aria-label="Keterangan simbol">
            <span><i class="lg lg--request" aria-hidden="true"></i>Permintaan data</span>
            <span><i class="lg lg--response" aria-hidden="true"></i>Pengembalian data</span>
            <span><i class="lg lg--process" aria-hidden="true"></i>Proses internal</span>
        </div>
        <ol id="stepList" class="step-list"></ol>
    </aside>
</main>

<nav class="player" aria-label="Kontrol pemutaran alur">
    <button type="button" class="btn btn-ghost btn-narration" id="btnNarration" aria-expanded="false" aria-controls="narration">
        <i class="fas fa-list-ol" aria-hidden="true"></i><span>Langkah</span>
    </button>
    <div class="player__main">
        <button type="button" class="icon-btn icon-btn--lg" id="btnRestart" aria-label="Putar dari awal (R)" title="Putar dari awal (R)"><i class="fas fa-rotate-left"></i></button>
        <button type="button" class="icon-btn icon-btn--lg" id="btnPrev" aria-label="Langkah sebelumnya" title="Langkah sebelumnya (←)"><i class="fas fa-backward-step"></i></button>
        <button type="button" class="btn btn-primary btn-play" id="btnPlay" aria-pressed="false" title="Putar / jeda (Spasi)"><i class="fas fa-play" aria-hidden="true"></i><span>Putar</span></button>
        <button type="button" class="icon-btn icon-btn--lg" id="btnNext" aria-label="Langkah berikutnya" title="Langkah berikutnya (→)"><i class="fas fa-forward-step"></i></button>
    </div>
    <span id="stepCounter" class="step-counter" aria-live="polite"></span>
    <div class="player__opts">
        <label class="speed"><span class="hide-sm">Kecepatan</span>
            <select id="speedSelect" aria-label="Kecepatan animasi">
                <option value="0.5">0.5×</option>
                <option value="1">1×</option>
                <option value="1.5">1.5×</option>
                <option value="2">2×</option>
                <option value="3">3×</option>
            </select>
        </label>
        <button type="button" class="toggle-btn" id="btnLoop" aria-pressed="false" title="Ulangi otomatis setelah langkah terakhir"><i class="fas fa-repeat" aria-hidden="true"></i><span class="hide-sm">Ulangi</span></button>
        <button type="button" class="toggle-btn" id="btnFollow" aria-pressed="false" title="Kamera mengikuti paket data (F)"><i class="fas fa-crosshairs" aria-hidden="true"></i><span class="hide-sm">Ikuti</span></button>
    </div>
</nav>

<!-- Panel editor -->
<aside id="editorDrawer" class="drawer" aria-labelledby="drawerTitle" hidden>
    <div class="drawer__head">
        <h2 id="drawerTitle">Atur Alur</h2>
        <button type="button" class="icon-btn" id="btnCloseEditor" aria-label="Tutup panel (Esc)"><i class="fas fa-xmark"></i></button>
    </div>
    <p class="drawer__note"><i class="fas fa-circle-info" aria-hidden="true"></i> Perubahan tersimpan otomatis di browser ini. Pakai <strong>Ekspor JSON</strong> untuk membagikan ke komputer lain.</p>
    <div class="tabs" role="tablist" aria-label="Bagian pengaturan">
        <button type="button" role="tab" id="tab-steps" aria-controls="panel-steps" aria-selected="true">Langkah</button>
        <button type="button" role="tab" id="tab-modules" aria-controls="panel-modules" aria-selected="false" tabindex="-1">Ruangan</button>
        <button type="button" role="tab" id="tab-display" aria-controls="panel-display" aria-selected="false" tabindex="-1">Tampilan</button>
        <button type="button" role="tab" id="tab-data" aria-controls="panel-data" aria-selected="false" tabindex="-1">Data</button>
    </div>

    <section id="panel-steps" role="tabpanel" aria-labelledby="tab-steps" class="drawer__body">
        <label class="field"><span>Nama skenario</span><input type="text" id="scName" maxlength="60"></label>
        <label class="field"><span>Deskripsi</span><textarea id="scDesc" rows="2" maxlength="200"></textarea></label>
        <div class="row-gap">
            <button type="button" class="btn btn-ghost btn-sm" id="btnNewScenario"><i class="fas fa-plus" aria-hidden="true"></i>Skenario baru</button>
            <button type="button" class="btn btn-ghost btn-sm" id="btnDupScenario"><i class="far fa-copy" aria-hidden="true"></i>Duplikat</button>
            <button type="button" class="btn btn-ghost btn-sm btn-danger-text" id="btnDeleteScenario"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button>
        </div>
        <h3 class="section-title">Urutan langkah</h3>
        <ol id="stepsEditor" class="edit-list"></ol>
        <button type="button" class="btn btn-primary btn-block" id="btnAddStep"><i class="fas fa-plus" aria-hidden="true"></i>Tambah langkah</button>
    </section>

    <section id="panel-modules" role="tabpanel" aria-labelledby="tab-modules" class="drawer__body" hidden>
        <label class="field">
            <span>Ruangan per baris: <strong id="columnsValue">5</strong></span>
            <input type="range" id="columnsRange" min="2" max="8" step="1">
        </label>
        <p class="muted">Urutan daftar = posisi ruangan di kantor (kiri ke kanan, atas ke bawah). Ruangan bawaan bisa disembunyikan, ruangan buatan Anda bisa dihapus.</p>
        <ol id="modulesEditor" class="edit-list"></ol>
        <form id="addModuleForm" class="add-module" novalidate>
            <h3 class="section-title">Tambah ruangan / modul baru</h3>
            <label class="field"><span>Nama ruangan *</span><input type="text" name="name" maxlength="40" placeholder="Contoh: Rawat Inap" required></label>
            <label class="field"><span>Keterangan</span><input type="text" name="sub" maxlength="40" placeholder="Contoh: Bed management"></label>
            <div class="step-edit__route">
                <label><span>Isi ruangan</span><select name="kind"></select></label>
                <label><span>Petugas</span><select name="staff"></select></label>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i>Tambah ruangan</button>
        </form>
    </section>

    <section id="panel-display" role="tabpanel" aria-labelledby="tab-display" class="drawer__body" hidden>
        <label class="check check--row"><input type="checkbox" id="set-labels"><span>Tampilkan papan nama ruangan</span></label>
        <label class="check check--row"><input type="checkbox" id="set-routes"><span>Tampilkan semua jalur data skenario (garis abu-abu)</span></label>
        <label class="check check--row"><input type="checkbox" id="set-follow"><span>Kamera mengikuti paket data saat diputar</span></label>
        <label class="check check--row"><input type="checkbox" id="set-loop"><span>Ulangi skenario otomatis</span></label>
        <label class="check check--row"><input type="checkbox" id="set-shadows"><span>Bayangan (matikan jika laptop terasa berat)</span></label>
    </section>

    <section id="panel-data" role="tabpanel" aria-labelledby="tab-data" class="drawer__body" hidden>
        <h3 class="section-title">Ekspor & impor</h3>
        <p class="muted">Simpan seluruh ruangan dan skenario ke file JSON, lalu impor di browser atau komputer lain.</p>
        <div class="row-gap">
            <button type="button" class="btn btn-primary" id="btnExport"><i class="fas fa-file-arrow-down" aria-hidden="true"></i>Ekspor JSON</button>
            <label class="btn btn-ghost file-btn"><i class="fas fa-file-arrow-up" aria-hidden="true"></i>Impor JSON<input type="file" id="importFile" accept="application/json,.json"></label>
        </div>
        <h3 class="section-title">Kembalikan ke bawaan</h3>
        <p class="muted">Menghapus semua perubahan dan memuat ulang 17 ruangan dan 5 skenario bawaan sistem.</p>
        <button type="button" class="btn btn-ghost btn-danger-text" id="btnReset"><i class="fas fa-rotate" aria-hidden="true"></i>Reset ke bawaan</button>
    </section>
</aside>

<!-- Dialog info modul -->
<dialog id="moduleDialog" class="dialog" aria-labelledby="moduleDialogTitle">
    <div class="dialog__head">
        <div>
            <h2 id="moduleDialogTitle"></h2>
            <p id="moduleDialogSub" class="muted"></p>
        </div>
        <button type="button" class="icon-btn" data-action="close-dialog" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
    </div>
    <div id="moduleDialogBody" class="dialog__body"></div>
    <div class="dialog__foot">
        <button type="button" class="btn btn-primary" data-action="add-step-from"><i class="fas fa-plus" aria-hidden="true"></i>Tambah langkah dari ruangan ini</button>
    </div>
</dialog>

<!-- Dialog bantuan -->
<dialog id="helpDialog" class="dialog" aria-labelledby="helpTitle">
    <div class="dialog__head">
        <h2 id="helpTitle">Cara membaca peta ini</h2>
        <button type="button" class="icon-btn" data-action="close-dialog" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="dialog__body">
        <p>Setiap ruangan adalah satu modul sistem. Saat diputar, paket terbang dari ruangan pengirim ke penerima, dan lantai ruangan yang sedang bekerja menyala.</p>
        <ul class="plain-list">
            <li><i class="lg lg--request" aria-hidden="true"></i><strong>Kotak toska</strong>: permintaan data (simpan, cek, kirim).</li>
            <li><i class="lg lg--response" aria-hidden="true"></i><strong>Belah ketupat kuning</strong>: pengembalian data (hasil, laporan, notifikasi).</li>
            <li><i class="lg lg--process" aria-hidden="true"></i><strong>Ruangan menyala tanpa paket</strong>: proses internal di modul itu.</li>
        </ul>
        <h3 class="section-title">Kamera</h3>
        <p>Seret untuk memutar, klik kanan + seret untuk menggeser, scroll / cubit untuk zoom. Klik lantai atau papan nama ruangan untuk melihat perannya.</p>
        <h3 class="section-title">Pintasan keyboard</h3>
        <dl class="kbd-list">
            <dt><kbd>Spasi</kbd></dt><dd>Putar / jeda</dd>
            <dt><kbd>←</kbd> <kbd>→</kbd></dt><dd>Langkah sebelumnya / berikutnya</dd>
            <dt><kbd>R</kbd></dt><dd>Putar dari awal</dd>
            <dt><kbd>F</kbd></dt><dd>Kamera ikuti paket</dd>
            <dt><kbd>C</kbd></dt><dd>Reset kamera</dd>
            <dt><kbd>E</kbd></dt><dd>Buka / tutup Atur Alur</dd>
            <dt><kbd>Esc</kbd></dt><dd>Tutup panel atau dialog</dd>
        </dl>
    </div>
</dialog>

<div id="toast" class="toast" role="status" aria-live="polite" hidden></div>

<script>
    // Tampilkan pesan jika mesin 3D (CDN) gagal dimuat dalam 15 detik.
    window.__alur3dFail = function (msg) {
        document.getElementById('loader').hidden = true;
        document.getElementById('errorText').textContent = msg;
        document.getElementById('errorState').hidden = false;
    };
    setTimeout(function () {
        if (!window.__alur3dReady) {
            window.__alur3dFail('Mesin 3D gagal dimuat. Periksa koneksi internet (library Three.js diambil dari CDN), lalu muat ulang halaman.');
        }
    }, 15000);
</script>
<script type="module" src="<?= base_url('assets/js/alur-3d/app.js?v=' . (file_exists(FCPATH . 'assets/js/alur-3d/app.js') ? filemtime(FCPATH . 'assets/js/alur-3d/app.js') : time())) ?>"></script>
</body>
</html>
