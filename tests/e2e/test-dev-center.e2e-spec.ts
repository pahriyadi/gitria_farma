import { test, expect } from '@playwright/test';

test.describe('System: Ruang Khusus Pengembang (Developer Command Center)', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  test('E2E Full Flow: Dev Center -> 6 Tabs -> Live Telemetry -> Dark Mode -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', (msg) => {
      if (msg.type() === 'error') errors.push(`Console Error: ${msg.text()}`);
    });
    page.on('pageerror', (err) => {
      errors.push(`Page Error: ${err.message}`);
    });

    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.12.0');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    // 1. Login sebagai Super Admin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await page.waitForURL('**/dashboard**', { timeout: 15000 });

    // 2. Buka Ruang Pengembang via SPA atau URL langsung
    await page.goto('system/dev-center', { waitUntil: 'domcontentloaded' });

    await expect(page.locator('h4')).toContainText('Ruang Khusus Pengembang', { timeout: 10000 });
    await expect(page.locator('#kpi-unresolved-err')).toBeVisible();
    await expect(page.locator('#kpi-failed-login')).toBeVisible();

    // 3. Test 6 Tab
    const tabSelectors = [
      'a[href="#tab-anomalies"]',
      'a[href="#tab-staff"]',
      'a[href="#tab-errors"]',
      'a[href="#tab-database"]',
      'a[href="#tab-integrations"]',
      'a[href="#tab-server"]'
    ];
    for (const sel of tabSelectors) {
      await page.click(sel);
      await page.waitForTimeout(250);
    }

    // 4. Test Live Telemetry Refresh button
    await page.click('#btn-dev-refresh-telemetry');
    await page.waitForTimeout(400);

    // 5. Verifikasi Zero .table-striped
    const stripedCount = await page.locator('table.table-striped').count();
    expect(stripedCount).toBe(0);

    // 6. Test Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(200);

    expect(errors).toEqual([]);
  });
});
