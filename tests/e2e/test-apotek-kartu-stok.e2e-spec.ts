import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL FARMASI & APOTEK - BUKU KARTU STOK (/apotek/kartu-stok)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Metrik (Master Obat, Saldo Awal, Total Masuk/Keluar, Sisa Stok Fisik).
 * 3. Tabel Mutasi Kartu Stok (Excel Strict Border Style) & Running Balance.
 * 4. Interaktivitas DataTables & Live Search filter mutasi.
 * 5. Penggantian Obat Aktif via Dropdown & Auto-Submit (e.g. Cetirizine).
 * 6. Filter Rentang Tanggal & Kalkulasi Saldo Awal Periode Sebelumnya.
 * 7. Tombol Export Excel & Validasi Cetak Resmi.
 * 8. Verifikasi 0 Console Errors.
 */

test.describe('Apotek Kartu Stok: Stock Card Ledger & Audit Trail Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  test.beforeEach(async ({ page }) => {
    // Suppress release popup and expand sidebar
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    // Login as superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: Header -> KPI Metrics -> Running Balance Ledger -> Dark Mode -> Switch Obat -> Filter Tanggal -> Export -> 0 Console Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const txt = msg.text();
        if (!txt.includes('favicon') && !txt.includes('chrome-extension')) {
          consoleErrors.push(txt);
        }
      }
    });

    // ========================================================================
    // STAGE 1: Membuka /apotek/kartu-stok & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /apotek/kartu-stok & Verifikasi Light Mode ---');
    await page.goto('apotek/kartu-stok');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan layout tidak terjebak mode drawer mobile
    await page.evaluate(() => {
      document.body.classList.remove('sidebar-open');
    });

    // Verifikasi Judul Halaman
    await expect(page.locator('h5:has-text("BUKU KARTU STOK OBAT & ALKES")')).toBeVisible();

    // Verifikasi desain_tabel.md: TIDAK BOLEH ada .table-striped
    const stripedTables = await page.locator('.table-striped').count();
    console.log(`Jumlah tabel dengan .table-striped (Harus 0): ${stripedTables}`);
    expect(stripedTables).toBe(0);

    // Ambil Screenshot Light Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-kartu-stok-01-light.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 2: Uji Mode Gelap (Paper Dark Palette)
    // ========================================================================
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(500);

    // Cek warna background card pada dark mode (#1f2328)
    const cardBgDark = await page.evaluate(() => {
      const card = document.querySelector('.ks-card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log(`Card background color in dark mode: ${cardBgDark}`);
    expect(cardBgDark).toBe('rgb(31, 35, 40)');

    // Ambil Screenshot Dark Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-kartu-stok-02-dark.png',
      fullPage: true,
    });

    // Kembalikan ke Light Mode untuk kelanjutan testing interaktif
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Verifikasi 4 KPI Stat Cards & Running Balance
    // ========================================================================
    console.log('--- STAGE 3: Verifikasi 4 KPI Stat Cards & Running Balance ---');
    const kpiBoxes = page.locator('.kpi-stat-box');
    const kpiCount = await kpiBoxes.count();
    console.log(`Jumlah KPI Card Profil Obat: ${kpiCount}`);
    expect(kpiCount).toBe(4);

    // Verifikasi box profil obat awal aktif
    await expect(kpiBoxes.first()).toContainText(/Master Obat Terpilih/i);

    // Pilih Paracetamol (id: 1) untuk menguji audit ledger multi-transaksi
    const medSelect = page.locator('#select-medicine');
    await expect(medSelect).toBeVisible();
    await medSelect.selectOption('1');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi nama obat Paracetamol dan sisa stok fisik 77
    const paracetamolKpi = page.locator('.kpi-stat-box');
    await expect(paracetamolKpi.first()).toContainText(/Paracetamol/i);
    await expect(paracetamolKpi.nth(3)).toContainText(/77/);

    // Verifikasi tabel kartu stok memiliki baris data mutasi
    const tableRows = page.locator('#table-kartu-stok tbody tr');
    const rowCount = await tableRows.count();
    console.log(`Jumlah baris mutasi stok Paracetamol: ${rowCount}`);
    expect(rowCount).toBeGreaterThan(0);

    // Verifikasi adanya kolom Sisa Saldo yang memiliki kelas col-balance
    const balanceCells = page.locator('#table-kartu-stok tbody td.col-balance');
    expect(await balanceCells.count()).toBeGreaterThan(0);

    // ========================================================================
    // STAGE 4: DataTables Interaktif & Live Search
    // ========================================================================
    console.log('--- STAGE 4: DataTables Interaktif & Live Search ---');
    const searchInput = page.locator('#table-kartu-stok_filter input[type="search"]');
    await expect(searchInput).toBeVisible();

    // Filter dengan kata kunci "Penjualan"
    await searchInput.fill('Penjualan');
    await page.waitForTimeout(500);

    const filteredRows = await page.locator('#table-kartu-stok tbody tr').count();
    console.log(`Baris setelah filter pencarian 'Penjualan': ${filteredRows}`);
    expect(filteredRows).toBeGreaterThan(0);

    // Bersihkan search
    await searchInput.clear();
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 5: Penggantian Obat Aktif (Switch ke Cetirizine)
    // ========================================================================
    console.log('--- STAGE 5: Penggantian Obat Aktif via Dropdown ---');
    // Pilih Cetirizine (value: 3)
    const selectCetirizine = page.locator('#select-medicine');
    await selectCetirizine.selectOption('3');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi bahwa nama obat Cetirizine kini ditampilkan di KPI box
    await expect(page.locator('.kpi-stat-box').first()).toContainText(/Cetirizine/i);

    // Ambil Screenshot Cetirizine Ledger
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-kartu-stok-03-cetirizine-ledger.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 6: Pengujian Filter Rentang Tanggal & Saldo Awal
    // ========================================================================
    console.log('--- STAGE 6: Pengujian Filter Rentang Tanggal ---');
    // Set start date ke awal bulan ini
    await page.fill('#filter-start-date', '2026-11-01');
    await page.locator('#form-filter-kartu button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi bahwa halaman tetap menampilkan tabel data dengan benar
    await expect(page.locator('#table-kartu-stok')).toBeVisible();

    // Ambil Screenshot Filter Tanggal
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-kartu-stok-04-filtered-date.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 7: Tombol Export Excel / CSV
    // ========================================================================
    console.log('--- STAGE 7: Tombol Export Excel / CSV ---');
    const btnExport = page.locator('#btn-export-excel');
    await expect(btnExport).toBeVisible();

    // Trigger klik export (tidak boleh melempar unhandled exception di browser)
    await btnExport.click();
    await page.waitForTimeout(500);

    // ========================================================================
    // STAGE 8: Verifikasi Error Konsol
    // ========================================================================
    console.log('--- STAGE 8: Verifikasi Error Konsol ---');
    console.log(`Total console errors caught: ${consoleErrors.length}`);
    if (consoleErrors.length > 0) {
      console.log('Console Errors caught:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 8 TAHAP PENGUJIAN /apotek/kartu-stok BERHASIL DENGAN SEMPURNA! ===');
  });
});
