import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: SISTEM & ADMINISTRASI - APA YANG BARU / WHAT'S NEW (/system/whats-new)
 * ============================================================================
 */

test.describe('System: What\'s New & System Updates Pipeline', () => {
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

  test('E2E Full Flow: What\'s New -> Hero -> Filter -> Modal -> Dark Mode -> 0 Striped Tables -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // 1. Navigate to system/whats-new
    await page.goto('system/whats-new');
    await page.waitForLoadState('domcontentloaded');

    // 2. Verify Light Mode initial state & hero
    await expect(page.locator('.wn-hero-card')).toBeVisible();
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/system-whats-new-01-light.png`, fullPage: false });

    // 3. Test Filter by typing in search
    const searchInput = page.locator('#filter-updates-search');
    await expect(searchInput).toBeVisible();
    await searchInput.fill('v2');
    await page.waitForTimeout(300);
    await page.screenshot({ path: `${artifactDir}/system-whats-new-02-filtered.png`, fullPage: false });
    await searchInput.fill('');

    // 4. Verify Zero .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // 5. Test Modal Tambah Rilis Fitur
    const btnAddUpdate = page.locator('button[data-target="#modalAddUpdate"]');
    if (await btnAddUpdate.isVisible()) {
      await btnAddUpdate.click();
      await page.waitForTimeout(400);

      const modalAdd = page.locator('#modalAddUpdate');
      await expect(modalAdd).toBeVisible();
      await page.screenshot({ path: `${artifactDir}/system-whats-new-03-modal-add.png`, fullPage: false });

      const btnCloseModal = modalAdd.locator('button[data-dismiss="modal"]').first();
      await btnCloseModal.click();
      await page.waitForTimeout(300);
    }

    // 6. Test Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    const bodyBg = await page.evaluate(() => {
      return window.getComputedStyle(document.body).backgroundColor;
    });
    expect(['rgb(20, 23, 26)', 'rgb(24, 27, 31)', 'rgb(31, 35, 40)']).toContain(bodyBg);

    await page.screenshot({ path: `${artifactDir}/system-whats-new-04-dark.png`, fullPage: false });

    // 7. Verify Zero Console Errors
    const filteredErrors = consoleErrors.filter(err => 
      !err.includes('favicon') && 
      !err.includes('DevTools')
    );
    expect(filteredErrors).toEqual([]);
  });
});

