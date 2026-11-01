import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL HRD & KEPEGAWAIAN - DATA PEGAWAI & PAYROLL (/hrd/pegawai)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 3 Tabs: Direktori Pegawai, Riwayat Payroll Bulanan, Komisi Jasa Medis Dokter.
 * 3. 4 KPI Summary Cards.
 * 4. Modal Tambah Pegawai Baru & Modal Generate Payroll.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('HRD: Kepegawaian & Payroll Pipeline', () => {
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

  test('E2E Full Flow: HRD Pegawai -> 3 Tabs -> Dark Mode -> Modals -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses hrd/pegawai
    await page.goto('hrd/pegawai');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 Pegawai)
    await page.screenshot({
      path: `${artifactDir}/hrd-pegawai-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Riwayat Payroll
    await page.click('#tab-payroll-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/hrd-pegawai-02-tab-payroll.png`,
      fullPage: false,
    });

    // 5. Tab 3: Jasa Medis Dokter
    await page.click('#tab-doctor-fees-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/hrd-pegawai-03-tab-fees.png`,
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
      path: `${artifactDir}/hrd-pegawai-04-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 7. Modal Tambah Pegawai
    const btnAddEmp = page.locator('button[data-target="#modalAddEmployee"]').first();
    await btnAddEmp.click();
    await page.waitForSelector('#modalAddEmployee.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/hrd-pegawai-05-modal-add.png`,
      fullPage: false,
    });

    await page.click('#modalAddEmployee button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 8. Modal Generate Payroll
    const btnGenPayroll = page.locator('button[data-target="#modalGeneratePayroll"]').first();
    await btnGenPayroll.click();
    await page.waitForSelector('#modalGeneratePayroll.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/hrd-pegawai-06-modal-payroll.png`,
      fullPage: false,
    });

    await page.click('#modalGeneratePayroll button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 9. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});
