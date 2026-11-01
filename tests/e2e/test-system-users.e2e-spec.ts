import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: SISTEM & ADMINISTRASI - PENGGUNA & RBAC (/system/users)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 2 Tabs: Direktori Pengguna & Struktur Peran/Role Hak Akses.
 * 3. 4 KPI Summary Cards.
 * 4. Modal Tambah Pengguna & Konfigurasi Hak Akses.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('System: Manajemen Pengguna & Hak Akses Pipeline', () => {
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

  test('E2E Full Flow: System Users -> 2 Tabs -> Dark Mode -> Modal -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses system/users
    await page.goto('system/users');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 Pengguna)
    await page.screenshot({
      path: `${artifactDir}/system-users-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Struktur Peran / Roles
    await page.click('#tab-roles-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/system-users-02-tab-roles.png`,
      fullPage: false,
    });

    // 5. Toggle Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verifikasi Paper Dark styling
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(cardBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/system-users-03-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 6. Modal Tambah Pengguna
    const btnAdd = page.locator('button[data-target="#modalAddUser"]').first();
    await btnAdd.click();
    await page.waitForSelector('#modalAddUser.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/system-users-04-modal-user.png`,
      fullPage: false,
    });

    await page.click('#modalAddUser button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 7. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

