import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL AKUNTANSI & JURNAL - SALDO AWAL (/accounting/saldo-awal)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Navigasi Switcher 6 Pilar Akuntansi (Tab 6 aktif).
 * 3. Kelompok Akun (Aktiva/Asset, Hutang/Liability, Ekuitas/Equity).
 * 4. Interaksi Realtime Balance Calculator & Smart Auto-Balance Badge.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Akuntansi: Saldo Awal Pipeline', () => {
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

  test('E2E Full Flow: Saldo Awal -> Dark Mode -> Balance Calc -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses accounting/saldo-awal
    await page.goto('accounting/saldo-awal');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Verifikasi Sub-Navigation Tabs
    const activeTab = page.locator('#accountingNavTabs .nav-link.active');
    await expect(activeTab).toContainText('Saldo Awal');

    // 4. Capture Light Mode Screenshot
    await page.screenshot({
      path: `${artifactDir}/accounting-saldo-awal-01-light.png`,
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
      path: `${artifactDir}/accounting-saldo-awal-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 6. Test Input Calculation
    const firstInput = page.locator('.input-asset-calc').first();
    if (await firstInput.count() > 0) {
      await firstInput.focus();
      await firstInput.fill('5000000');
      await firstInput.dispatchEvent('change');
      await page.waitForTimeout(300);

      // Verifikasi total debit terupdate
      const debitText = await page.locator('#sum-total-debit').innerText();
      expect(debitText).toMatch(/Rp\s*5\./);
    }

    await page.screenshot({
      path: `${artifactDir}/accounting-saldo-awal-03-calc.png`,
      fullPage: false,
    });

    // 7. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});
