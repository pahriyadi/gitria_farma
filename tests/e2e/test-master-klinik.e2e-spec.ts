import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MASTER DATA - REFERENSI KLINIK (/system/master-klinik)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Navigasi Modern Scrollable Tabs (Kategori, Tarif, Dokter, Jadwal, Ruangan).
 * 3. 0 .table-striped di seluruh 14 tabel referensi.
 * 4. Modal Tambah Kategori Pelayanan.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Master Data: Referensi Klinik Pipeline', () => {
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

  test('E2E Full Flow: Master Klinik -> Multi-Tabs -> Dark Mode -> Modal -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses system/master-klinik
    await page.goto('system/master-klinik');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 Kategori)
    await page.screenshot({
      path: `${artifactDir}/master-klinik-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Sub Kategori & Tarif
    await page.click('#subcat-tab');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/master-klinik-02-subcat.png`,
      fullPage: false,
    });

    // 5. Tab 3: Dokter & Fee Medis
    await page.click('#doctor-tab');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/master-klinik-03-doctor.png`,
      fullPage: false,
    });

    // 6. Tab 4: Jadwal Praktik Dokter
    await page.click('#schedule-tab');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/master-klinik-04-schedule.png`,
      fullPage: false,
    });

    // 7. Tab 5: Ruangan & Bed
    await page.click('#rooms-tab');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/master-klinik-05-rooms.png`,
      fullPage: false,
    });

    // 8. Toggle Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verifikasi Paper Dark styling
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(cardBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/master-klinik-06-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 9. Kembali ke Tab 1 dan buka modal tambah kategori
    await page.click('#cat-tab');
    await page.waitForTimeout(300);

    const btnAddCat = page.locator('button[data-target="#catAddModal"]').first();
    await btnAddCat.click();
    await page.waitForSelector('#catAddModal.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/master-klinik-07-modal-cat.png`,
      fullPage: false,
    });

    await page.click('#catAddModal button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 10. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

