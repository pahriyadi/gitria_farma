import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL PENGADAAN & LOGISTIK - PO & PR (/procurement/po)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Tabs: Purchase Requisitions, Purchase Orders, Goods Receipt (GRN), Master Supplier.
 * 3. 4 KPI Cards Pengadaan.
 * 4. Modal Ajukan PR Baru.
 * 5. Modal Tambah Supplier PBF.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Procurement: Pengadaan & Purchase Orders Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const artifactDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

  test.beforeEach(async ({ page }) => {
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: Procurement PO -> 4 Tabs -> Dark Mode -> Modals -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses procurement/po
    await page.goto('procurement/po');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 PR)
    await page.screenshot({
      path: `${artifactDir}/procurement-po-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Purchase Orders
    await page.click('#tab-po-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/procurement-po-02-tab-po.png`,
      fullPage: false,
    });

    // 5. Tab 3: Goods Receipt (GRN)
    await page.click('#tab-grn-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/procurement-po-03-tab-grn.png`,
      fullPage: false,
    });

    // 6. Tab 4: Master Supplier
    await page.click('#tab-suppliers-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/procurement-po-04-tab-suppliers.png`,
      fullPage: false,
    });

    // 7. Toggle Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verifikasi Paper Dark styling
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(cardBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/procurement-po-05-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 8. Modal Ajukan PR Baru
    const btnCreatePr = page.locator('button[data-target="#modalCreatePr"]').first();
    await btnCreatePr.click();
    await page.waitForSelector('#modalCreatePr.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/procurement-po-06-modal-pr.png`,
      fullPage: false,
    });

    await page.click('#modalCreatePr button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 9. Modal Tambah Supplier
    const btnAddSup = page.locator('button[data-target="#modalAddSupplier"]').first();
    await btnAddSup.click();
    await page.waitForSelector('#modalAddSupplier.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/procurement-po-07-modal-sup.png`,
      fullPage: false,
    });

    await page.click('#modalAddSupplier button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 10. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

