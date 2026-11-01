import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - LAPORAN LABA RUGI & OPERASIONAL (/distributor/laporan)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Tab 1: Penjualan & Rekap Grosir (Statistik Ringkasan, Tabel per Pelanggan & per Barang).
 * 3. Tab 2: Piutang & Aging Schedule (Daftar Faktur, Umur Tagihan, Sisa Piutang).
 * 4. Tab 3: Persediaan & Nilai Stok (Katalog Stok Gudang B2B, HPP & Valuasi Persediaan).
 * 5. Tab 4: Laporan Laba Rugi (P&L) Mandiri Grosir (Pendapatan Bersih, HPP, Gross Margin).
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Financial & Operations Reports Pipeline', () => {
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

  test('E2E Full Flow: 4 Report Tabs -> Dark Mode -> Zero Striped Tables -> P&L Verification -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('favicon') && !text.includes('chrome-extension')) {
          consoleErrors.push(text);
        }
      }
    });

    // 1. Akses distributor/laporan (Tab 1: Penjualan)
    await page.goto('distributor/laporan');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('body')).toContainText('Laporan Keuangan & Operasional Distributor');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    let stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode (Tab 1)
    await page.screenshot({
      path: `${artifactDir}/distributor-laporan-01-light.png`,
      fullPage: false,
    });

    // 2. Verifikasi Dark Mode (Paper Dark: #14171a, #1f2328, #374151)
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(300);

    const darkBg = await page.evaluate(() => {
      const card = document.querySelector('.card.dist-card') as HTMLElement;
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    // Mengharapkan warna background kartu bertema dark (#1f2328 / rgb(31, 35, 40))
    expect(darkBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/distributor-laporan-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Tab 2: Piutang & Aging Schedule
    await page.goto('distributor/laporan?tab=piutang');
    await page.waitForLoadState('domcontentloaded');
    stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    await page.screenshot({
      path: `${artifactDir}/distributor-laporan-03-piutang.png`,
      fullPage: false,
    });

    // 4. Tab 3: Persediaan & Nilai Stok
    await page.goto('distributor/laporan?tab=stok');
    await page.waitForLoadState('domcontentloaded');
    stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    await page.screenshot({
      path: `${artifactDir}/distributor-laporan-04-stok.png`,
      fullPage: false,
    });

    // 5. Tab 4: Laba Rugi (P&L) Distributor
    await page.goto('distributor/laporan?tab=labarugi');
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('body')).toContainText('LAPORAN LABA RUGI (PROFIT & LOSS)');
    await expect(page.locator('body')).toContainText('LABA KOTOR DISTRIBUTOR');

    await page.screenshot({
      path: `${artifactDir}/distributor-laporan-05-labarugi.png`,
      fullPage: false,
    });

    // 6. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});

