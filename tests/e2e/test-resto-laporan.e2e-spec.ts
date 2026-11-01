import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL RESTO & DAPUR GIZI - LAPORAN & ANALITIK PENJUALAN (/resto/laporan)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Ringkasan Omset (Total Omset, Pesanan Selesai, Rata-rata Basket, Porsi/Item).
 * 3. 2 ChartJS Visualisasi (Line Chart Tren Omset Harian & Doughnut Chart Kategori).
 * 4. Leaderboard Menu Terlaris (Top Sellers) ber-badge Crown/Ranking.
 * 5. Tabel Detail Seluruh Transaksi ber-DataTables dengan pencarian interaktif.
 * 6. Filter Rentang Tanggal (Form Submission) & Halaman Cetak Rekap Laporan.
 * 7. Verifikasi 0 Console Errors.
 */

test.describe('Resto Analytics & Sales Report Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const artifactDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

  test.beforeEach(async ({ page }) => {
    // Suppress release popup and ensure collapsed sidebar on desktop
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
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

  test('E2E Full Flow: Analytics KPI -> Dark Mode -> Charts -> Leaderboard & DataTables -> Date Filter -> Cetak -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /resto/laporan & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /resto/laporan & Verifikasi Light Mode ---');
    await page.goto('resto/laporan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan analitik
    const pushmenuBtn = page.locator('[data-widget="pushmenu"]').first();
    const isCollapsed = await page.evaluate(() => document.body.classList.contains('sidebar-collapse'));
    if (!isCollapsed && await pushmenuBtn.isVisible()) {
      await pushmenuBtn.click();
      await page.waitForTimeout(350);
    }

    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
    });

    // Verifikasi Judul Halaman
    await expect(page.locator('h4').filter({ hasText: 'Laporan & Analitik Penjualan Resto' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi 4 Kartu KPI Summary
    await expect(page.locator('.rl-stat-box').filter({ hasText: 'Total Omset Penjualan' })).toBeVisible();
    await expect(page.locator('.rl-stat-box').filter({ hasText: 'Total Pesanan Selesai' })).toBeVisible();
    await expect(page.locator('.rl-stat-box').filter({ hasText: 'Rata-rata Transaksi' })).toBeVisible();
    await expect(page.locator('.rl-stat-box').filter({ hasText: 'Porsi & Item Terjual' })).toBeVisible();

    // Verifikasi Tombol Cepat di Header
    await expect(page.locator('a').filter({ hasText: 'Buka Display TV' })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: 'Buka Kasir POS' })).toBeVisible();

    // Verifikasi Chart Canvas
    await expect(page.locator('#salesTrendChart')).toBeVisible();
    await expect(page.locator('#categoryChart')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/resto-laporan-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot resto-laporan-01-light.png tersimpan');

    // ========================================================================
    // STAGE 2: Verifikasi Paper Dark Mode
    // ========================================================================
    console.log('--- STAGE 2: Verifikasi Paper Dark Mode (#1f2328, #374151) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    // Evaluasi Background & Border Card Dark Mode
    const darkCardBg = await page.locator('.rl-card').first().evaluate(el => {
      return window.getComputedStyle(el).backgroundColor;
    });
    const darkCardBorder = await page.locator('.rl-card').first().evaluate(el => {
      return window.getComputedStyle(el).borderColor;
    });

    console.log(`Dark Mode Card BG: ${darkCardBg}, Border: ${darkCardBorder}`);
    expect(darkCardBg).toBe('rgb(31, 35, 40)');
    expect(darkCardBorder).toBe('rgb(55, 65, 81)');

    // Tangkap Screenshot Dark Mode
    await page.screenshot({
      path: `${artifactDir}/resto-laporan-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot resto-laporan-02-dark.png tersimpan');

    // Kembalikan ke Light Mode untuk pengujian interaksi
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Top Sellers Leaderboard & DataTables Rincian Transaksi
    // ========================================================================
    console.log('--- STAGE 3: Leaderboard & DataTables Rincian Transaksi ---');
    // Verifikasi Keberadaan Leaderboard
    await expect(page.locator('h6').filter({ hasText: 'Produk & Menu Paling Laris' })).toBeVisible();

    // Verifikasi Tabel Detail Transaksi & Fitur Pencarian DataTables
    const searchInput = page.locator('#tableOrdersReport_filter input');
    if (await searchInput.isVisible()) {
      await searchInput.fill('ORD-');
      await page.waitForTimeout(300);
      const rowCount = await page.locator('#tableOrdersReport tbody tr').count();
      console.log(`DataTables filtered rows: ${rowCount}`);
      expect(rowCount).toBeGreaterThan(0);
      // Bersihkan filter pencarian
      await searchInput.clear();
      await page.waitForTimeout(300);
    }

    // Tangkap Screenshot Leaderboard & Tabel
    await page.screenshot({
      path: `${artifactDir}/resto-laporan-03-tables.png`,
      fullPage: true,
    });
    console.log('Screenshot resto-laporan-03-tables.png tersimpan');

    // ========================================================================
    // STAGE 4: Test Date Range Filter
    // ========================================================================
    console.log('--- STAGE 4: Form Filter Tanggal Penjualan ---');
    const todayStr = new Date().toISOString().split('T')[0];
    await page.fill('input[name="start_date"]', todayStr);
    await page.fill('input[name="end_date"]', todayStr);
    await page.click('button[type="submit"]:has-text("Terapkan Filter")');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verifikasi URL memiliki parameter tanggal
    expect(page.url()).toContain(`start_date=${todayStr}`);
    expect(page.url()).toContain(`end_date=${todayStr}`);

    // Tangkap Screenshot Hasil Filter
    await page.screenshot({
      path: `${artifactDir}/resto-laporan-04-filtered.png`,
      fullPage: true,
    });
    console.log('Screenshot resto-laporan-04-filtered.png tersimpan');

    // ========================================================================
    // STAGE 5: Cetak Rekap Laporan Penjualan (Print View)
    // ========================================================================
    console.log('--- STAGE 5: Cetak Rekap Laporan Penjualan ---');
    await page.goto(`resto/cetak-laporan?start_date=${todayStr}&end_date=${todayStr}`);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verifikasi Teks Header Cetak
    await expect(page.locator('h2').filter({ hasText: 'SAWAMAWA MEDICAL CENTER' })).toBeVisible();
    await expect(page.locator('p').filter({ hasText: 'REKAP LAPORAN PENJUALAN RESTO & NUTRISI SEHAT' })).toBeVisible();

    // Verifikasi Ringkasan Card Cetak
    await expect(page.locator('.summary-card').first()).toBeVisible();

    // Tangkap Screenshot Halaman Cetak
    await page.screenshot({
      path: `${artifactDir}/resto-laporan-05-cetak.png`,
      fullPage: true,
    });
    console.log('Screenshot resto-laporan-05-cetak.png tersimpan');

    // ========================================================================
    // STAGE 6: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 6: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Resto Sub-Menu 3 (/resto/laporan) berhasil diuji 100% tanpa kendala.');
  });
});

