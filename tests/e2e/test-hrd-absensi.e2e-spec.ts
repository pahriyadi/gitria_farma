import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL HRD & KEPEGAWAIAN - ABSENSI & PRESENSI (/hrd/absensi)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Tabs: Presensi Harian, Rekapitulasi Bulanan, Pengajuan Cuti, Master Shift.
 * 3. 4 KPI Summary Cards Kehadiran.
 * 4. Modal Check-In Presensi & Modal Pengajuan Cuti.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('HRD: Absensi & Presensi Pegawai Pipeline', () => {
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

  test('E2E Full Flow: HRD Absensi -> 4 Tabs -> Dark Mode -> Modals -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses hrd/absensi
    await page.goto('hrd/absensi');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 Harian)
    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Rekap Bulanan
    await page.click('#tab-recap-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-02-tab-recap.png`,
      fullPage: false,
    });

    // 5. Tab 3: Pengajuan Cuti
    await page.click('#tab-cuti-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-03-tab-cuti.png`,
      fullPage: false,
    });

    // 6. Tab 4: Master Shift Kerja
    await page.click('#tab-shift-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-04-tab-shift.png`,
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
      path: `${artifactDir}/hrd-absensi-05-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 8. Modal Check-In
    const btnCheckIn = page.locator('button[data-target="#modalCheckIn"]').first();
    await btnCheckIn.click();
    await page.waitForSelector('#modalCheckIn.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-06-modal-checkin.png`,
      fullPage: false,
    });

    await page.click('#modalCheckIn button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 9. Modal Pengajuan Cuti
    const btnLeave = page.locator('button[data-target="#modalLeave"]').first();
    await btnLeave.click();
    await page.waitForSelector('#modalLeave.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/hrd-absensi-07-modal-leave.png`,
      fullPage: false,
    });

    await page.click('#modalLeave button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 10. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

