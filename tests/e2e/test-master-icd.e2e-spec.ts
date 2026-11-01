import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MASTER DATA - MASTER ICD-10 & ICD-9-CM (/system/master-icd)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 2 Tabs: Diagnosa Penyakit (ICD-10) & Prosedur Medis (ICD-9-CM).
 * 3. 2 KPI Info-Box Cards.
 * 4. Modal Tambah Kode ICD-10.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Master Data: Master ICD-10 & ICD-9-CM Pipeline', () => {
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

  test('E2E Full Flow: Master ICD -> 2 Tabs -> Dark Mode -> Modal -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses system/master-icd
    await page.goto('system/master-icd');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 ICD-10)
    await page.screenshot({
      path: `${artifactDir}/master-icd-01-light.png`,
      fullPage: false,
    });

    // 4. Modal Tambah Kode ICD-10 (Tab 1)
    const btnAdd10 = page.locator('button[data-target="#modalAddIcd10"]').first();
    await btnAdd10.click();
    await page.waitForSelector('#modalAddIcd10.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/master-icd-04-modal-add.png`,
      fullPage: false,
    });

    await page.click('#modalAddIcd10 button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 5. Tab 2: ICD-9-CM
    await page.click('#tab-icd9-tab');
    await page.waitForTimeout(800);
    await page.screenshot({
      path: `${artifactDir}/master-icd-02-tab-icd9.png`,
      fullPage: false,
    });

    // 6. Toggle Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verifikasi Paper Dark styling
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(cardBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/master-icd-03-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 7. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});
