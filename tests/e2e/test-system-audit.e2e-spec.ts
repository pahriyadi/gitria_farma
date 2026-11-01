import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: SISTEM & ADMINISTRASI - AUDIT TRAIL (/system/audit)
 * ============================================================================
 */

test.describe('System: Audit Trail & Security Logs Pipeline', () => {
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

  test('E2E Full Flow: Audit Trail -> Filter -> Table -> Dark Mode -> 0 Striped Tables -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // 1. Navigate to system/audit
    await page.goto('system/audit');
    await page.waitForLoadState('domcontentloaded');

    // 2. Verify Light Mode initial state
    await expect(page.locator('#table-audit')).toBeVisible();
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/system-audit-01-light.png`, fullPage: false });

    // 3. Verify Zero .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // 4. Test Filter Toolbar
    const actionSelect = page.locator('select[name="action"]');
    await expect(actionSelect).toBeVisible();
    await actionSelect.selectOption('LOGIN');

    const btnFilter = page.locator('button[type="submit"]:has-text("Filter Log")');
    await btnFilter.click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/system-audit-02-filtered.png`, fullPage: false });

    // 5. Test Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    const bodyBg = await page.evaluate(() => {
      return window.getComputedStyle(document.body).backgroundColor;
    });
    expect(['rgb(20, 23, 26)', 'rgb(24, 27, 31)', 'rgb(31, 35, 40)']).toContain(bodyBg);

    await page.screenshot({ path: `${artifactDir}/system-audit-03-dark.png`, fullPage: false });

    // 6. Verify 0 Console Errors
    const filteredErrors = consoleErrors.filter(err => 
      !err.includes('favicon') && 
      !err.includes('DevTools')
    );
    expect(filteredErrors).toEqual([]);
  });
});

