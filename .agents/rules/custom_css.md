# 🎨 Dokumentasi Arsitektur & Implementasi Multi-Theme Engine (`custom.css`)
**Sawamawa Medical Center — SIMKlinik & ERP Terpadu**

---

## 📌 1. Ikhtisar Sistem Tema

Panel Admin Sawamawa Medical Center dilengkapi dengan **Multi-Theme Engine** dinamis yang memungkinkan setiap pengguna (dokter, perawat, kasir, atau admin) memilih antarmuka visual sesuai preferensi kenyamanan kerja masing-masing secara independen.

Pilihan tema disimpan pada penyimpanan lokal browser (`localStorage`) dengan inisialisasi awal pada level `<head>` sehingga **bebas kedip (*zero-flicker*)** saat berpindah menu atau membuka sesi baru.

---

## 🎨 2. Daftar 5 Tema Resmi Sistem

### 1. `theme-macos` (DEFAULT UTAMA — macOS 11 Big Sur Apple Frosted Glass & Traffic Lights)
- **Konsep**: Antarmuka khas Apple macOS Big Sur / Monterey dengan efek *translucent acrylic blur* (`backdrop-filter: blur(25px)`), tombol *Traffic Lights* bulat (🔴 🟡 🟢) di setiap header kartu dan modal, widget kartu elevated, tombol kapsul dengan aksen *Apple System Blue* `#007aff`, scrollbar tipis halus, serta font modern SF Pro / Inter.
- **Tipografi**: `-apple-system, BlinkMacSystemFont, "SF Pro Display", "Inter", sans-serif`.
- **Preloader**: Layar hitam Apple Bootloader dengan logo Apple bercahaya (`fa-apple`) dan *sleek minimalist progress bar*.

### 2. `theme-excel-paper` (Putih di Atas Kertas v2.0 sesuai [`desain_tabel.md`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/desain_tabel.md))
- **Konsep**: Filosofi murni *Putih di Atas Kertas* &mdash; Serba putih bersih (`#ffffff`), border abu-abu seragam `1px solid #b8b8b8`, flat tanpa bayangan (`box-shadow: none !important`), grid tabel tegas seperti lembar spreadsheet Excel (`border: 1.5px solid #a0a0a0`), Anti-Zebra, hover hijau lembut `#f0f7f3`, dan menu sidebar berbingkai seragam dengan aksen aktif hijau brand `#0d9f4f`.
- **Tipografi**: `Source Sans Pro` / `Plus Jakarta Sans`.
- **Preloader**: Lembar kertas putih murni dengan pemutar hijau brand `#0d9f4f`.

### 3. `theme-paper-white` (Soft Precision Medical Slate)
- **Konsep**: Antarmuka modern, bersih, lapang, berjarak napas (*breathable*), dengan garis batas halus presisi (`#e2e8f0` / `#cbd5e1`), aksen toska medis `#0d9488`, dan sangat nyaman di mata untuk penggunaan jangka panjang.
- **Tipografi**: `Plus Jakarta Sans`.
- **Preloader**: Kanvas putih bersih dengan pemutar lingkaran toska medis halus (*pulse ring*).

### 4. `theme-modern-emerald` (Elegan Toska Medis Berkelas)
- **Konsep**: Nuansa hijau zamrud medis mewah (*rich emerald*) dengan sidebar gelap berwibawa `#042f2e` dan kartu bergaya *glassmorphism*.
- **Warna Utama**: Kanvas `#f0fdfa`, sidebar gelap `#042f2e`, border `#ccfbf1`, tombol gradien `#0d9488` ke `#0f766e`.
- **Tipografi**: `Plus Jakarta Sans`.
- **Preloader**: Latar gradien gelap zamrud dengan efek pemutar bercahaya (*emerald glow orb*).

### 5. `theme-windows-xp` (Retro Windows XP Luna Blue 3D Classic)
- **Konsep**: Nostalgia autentik antarmuka Windows XP (Luna Blue Theme) dengan titlebar 3D bergradien, tombol *push-button glossy*, border inset `#7f9db9`, dan kotak dialog klasik.
- **Warna Utama**: Kanvas abu dialog `#ece9d8`, titlebar `#0058ee` ke `#003dd7`.
- **Tipografi**: `Tahoma` (11.5px &ndash; 12px).
- **Preloader**: Layar bootloader Windows XP hitam pekat dengan **3-balok biru bergulir horizontal** (*segmented XP rolling progress bar*).

---

## 🏗️ 3. Struktur Berkas & Implementasi

1. **Stylesheet Utama**: [`public/assets/css/custom.css`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/public/assets/css/custom.css)
2. **Layout & Dropdown Navbar**: [`app/Views/layouts/layout.php`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/app/Views/layouts/layout.php)
3. **Panduan Sistem Putih Kertas v2.0**: [`desain_tabel.md`](file:///c:/xampp/htdocs/sawamawamedicalcenter.id/desain_tabel.md)
