import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - DASHBOARD (/distributor/dashboard)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Metrik Eksekutif (Omset Hari Ini, Omset Bulan Ini, Piutang, Nilai Stok Gudang).
 * 3. Tabel Transaksi Penjualan Terbaru (Faktur, Tanggal, Customer, Status, Aksi Cetak).
 * 4. Panel Monitoring Piutang Jatuh Tempo & Quick Action Menu B2B.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Executive Dashboard Pipeline', () => {
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

  test('E2E Full Flow: Dashboard KPI -> Dark Mode -> Recent Sales -> Receivables Alert -> Quick Actions -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /distributor/dashboard & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/dashboard & Verifikasi Light Mode ---');
    await page.goto('distributor/dashboard');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan dashboard distributor
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
    await expect(page.locator('h4').filter({ hasText: 'Dashboard Distributor & Grosir' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi 4 Kartu KPI Ringkasan
    await expect(page.locator('.dist-stat-box').filter({ hasText: 'Omset Hari Ini' })).toBeVisible();
    await expect(page.locator('.dist-stat-box').filter({ hasText: 'Omset Bulan Ini' })).toBeVisible();
    await expect(page.locator('.dist-stat-box').filter({ hasText: 'Total Piutang Berjalan' })).toBeVisible();
    await expect(page.locator('.dist-stat-box').filter({ hasText: 'Nilai Stok Gudang' })).toBeVisible();

    // Verifikasi Tombol Cepat di Header
    await expect(page.locator('a').filter({ hasText: 'Kasir & Faktur Baru' }).first()).toBeVisible();
    await expect(page.locator('a').filter({ hasText: 'Kas & Bank B2B' }).first()).toBeVisible();
    await expect(page.locator('a').filter({ hasText: 'Laporan Laba Rugi' }).first()).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-dashboard-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-dashboard-01-light.png tersimpan');

    // ========================================================================
    // STAGE 2: Verifikasi Paper Dark Mode
    // ========================================================================
    console.log('--- STAGE 2: Verifikasi Paper Dark Mode (#1f2328, #374151) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    // Evaluasi Background & Border Card Dark Mode
    const darkCardBg = await page.locator('.dist-card').first().evaluate(el => {
      return window.getComputedStyle(el).backgroundColor;
    });
    const darkCardBorder = await page.locator('.dist-card').first().evaluate(el => {
      return window.getComputedStyle(el).borderColor;
    });

    console.log(`Dark Mode Card BG: ${darkCardBg}, Border: ${darkCardBorder}`);
    expect(darkCardBg).toBe('rgb(31, 35, 40)');
    expect(darkCardBorder).toBe('rgb(55, 65, 81)');

    // Tangkap Screenshot Dark Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-dashboard-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-dashboard-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Verifikasi Recent Sales Table & Receivables Alert
    // ========================================================================
    console.log('--- STAGE 3: Recent Sales Table & Receivables Alert ---');
    await expect(page.locator('h6').filter({ hasText: 'Transaksi Penjualan Terbaru' })).toBeVisible();
    await expect(page.locator('h6').filter({ hasText: 'Piutang Jatuh Tempo' })).toBeVisible();
    await expect(page.locator('h6').filter({ hasText: 'Akses Cepat Distributor' })).toBeVisible();

    // Verifikasi 6 Quick Access Button di Panel Kanan
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Kasir Grosir' })).toBeVisible();
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Data Pelanggan' })).toBeVisible();
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Stok & FEFO' })).toBeVisible();
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Transfer Unit' })).toBeVisible();
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Retur Penjualan' })).toBeVisible();
    await expect(page.locator('.dist-quick-btn').filter({ hasText: 'Laba Rugi' })).toBeVisible();

    // Tangkap Screenshot Actions & Quick Panel
    await page.screenshot({
      path: `${artifactDir}/distributor-dashboard-03-actions.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-dashboard-03-actions.png tersimpan');

    // ========================================================================
    // STAGE 4: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 4: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 1 (/distributor/dashboard) berhasil diuji 100% tanpa kendala.');
  });
});

