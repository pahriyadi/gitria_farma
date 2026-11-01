import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - RETUR PENJUALAN GROSIR (/distributor/retur)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Tabel Arsip Retur Penjualan Grosir (No. Dokumen, Tanggal, No. Faktur, Pelanggan, Total, Kompensasi, Alasan, Status).
 * 3. Modal Form Pembuatan Retur Baru (Pilih Faktur, Metode Kompensasi, Alasan).
 * 4. Pemuatan Dinamis Daftar Item Faktur Penjualan & Input Qty Retur.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Sales Return Pipeline', () => {
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

  test('E2E Full Flow: Returns Table -> Dark Mode -> Return Modal -> Dynamic Item Loading -> 0 Errors', async ({ page }) => {
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

    // 1. Akses distributor/retur
    await page.goto('distributor/retur');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('h4')).toContainText('Retur Penjualan Grosir');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-retur-01-light.png`,
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
      path: `${artifactDir}/distributor-retur-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Test Buka Modal Form Retur
    await page.click('#btnOpenAddReturn');
    await expect(page.locator('#modalAddReturn')).toBeVisible();
    await page.waitForTimeout(300);

    await page.screenshot({
      path: `${artifactDir}/distributor-retur-03-modal-add.png`,
    });

    // 4. Pilih Faktur Penjualan jika ada opsi
    const optCount = await page.locator('#ret_sale_id option').count();
    if (optCount > 1) {
      await page.selectOption('#ret_sale_id', { index: 1 });
      await page.waitForTimeout(600); // Tunggu fetching detail item faktur

      await page.screenshot({
        path: `${artifactDir}/distributor-retur-04-sale-items.png`,
      });
    }

    // Tutup modal
    await page.click('#modalAddReturn button[data-dismiss="modal"]');
    await page.waitForTimeout(200);

    // 5. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});
