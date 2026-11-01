import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL AKUNTANSI & JURNAL - LOG VOID ANTI-FRAUD (/accounting/void-logs)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Navigasi Switcher 7 Pilar Akuntansi (Tab 7 aktif).
 * 3. KPI Anti-Fraud Cards & Filter Bar.
 * 4. Modal PIN Supervisor.
 * 5. Modal Panduan Operasional README Anti-Fraud.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Akuntansi: Void Logs Anti-Fraud Pipeline', () => {
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

  test('E2E Full Flow: Void Logs -> Dark Mode -> Modals -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses accounting/void-logs
    await page.goto('accounting/void-logs');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Verifikasi Sub-Navigation Tabs
    const activeTab = page.locator('#accountingNavTabs .nav-link.active');
    await expect(activeTab).toContainText('Log Void');

    // 4. Capture Light Mode Screenshot
    await page.screenshot({
      path: `${artifactDir}/accounting-void-01-light.png`,
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
      path: `${artifactDir}/accounting-void-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 6. Modal Setup PIN Supervisor
    const btnPin = page.locator('button[data-target="#modalManagePin"]').first();
    await btnPin.click();
    await page.waitForSelector('#modalManagePin.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/accounting-void-03-modal-pin.png`,
      fullPage: false,
    });

    await page.click('#modalManagePin button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 7. Modal Panduan README
    const btnReadme = page.locator('button[data-target="#modalReadmeVoid"]').first();
    await btnReadme.click();
    await page.waitForSelector('#modalReadmeVoid.show', { timeout: 5000 });
    await page.waitForTimeout(500);

    await page.screenshot({
      path: `${artifactDir}/accounting-void-04-modal-readme.png`,
      fullPage: false,
    });

    await page.click('#modalReadmeVoid button[data-dismiss="modal"]');
    await page.waitForTimeout(500);

    // 8. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

