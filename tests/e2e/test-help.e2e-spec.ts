import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: PUSAT BANTUAN & DOKUMENTASI SISTEM (/help & /alur-3d)
 * ============================================================================
 */

test.describe('Help & System Documentation Pipeline', () => {
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

  test('E2E Full Flow: Help Center -> Tabs -> Modal -> Dark Mode -> 0 Striped Tables -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // 1. Navigate to help
    await page.goto('help');
    await page.waitForLoadState('domcontentloaded');

    // 2. Verify Light Mode initial state & Hero Card
    await page.waitForTimeout(500);
    await page.screenshot({ path: `${artifactDir}/help-center-01-light.png`, fullPage: false });

    // 3. Test Tabs
    // Tab 2: Panduan Langkah per Peran
    const tabRoles = page.locator('a[href="#tab-roles"]');
    await expect(tabRoles).toBeVisible();
    await tabRoles.click();
    await page.waitForTimeout(400);
    await page.screenshot({ path: `${artifactDir}/help-center-02-roles.png`, fullPage: false });

    // Tab 3: FAQ & Solusi Kendala
    const tabFaq = page.locator('a[href="#tab-faq"]');
    await expect(tabFaq).toBeVisible();
    await tabFaq.click();
    await page.waitForTimeout(400);
    await page.screenshot({ path: `${artifactDir}/help-center-03-faq.png`, fullPage: false });

    // Switch back to Tab 1
    const tabWorkflows = page.locator('a[href="#tab-workflows"]');
    await tabWorkflows.click();
    await page.waitForTimeout(300);

    // 4. Verify Zero .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // 5. Test Modal Tulis Panduan Baru
    const btnAddDoc = page.locator('button[data-target="#modalAddDoc"]').first();
    await expect(btnAddDoc).toBeVisible();
    await btnAddDoc.click();
    await page.waitForTimeout(400);

    const modalDoc = page.locator('#modalAddDoc');
    await expect(modalDoc).toBeVisible();
    await page.screenshot({ path: `${artifactDir}/help-center-04-modal-add.png`, fullPage: false });

    const btnCloseModal = modalDoc.locator('button[data-dismiss="modal"]').first();
    await btnCloseModal.click();
    await page.waitForTimeout(300);

    // 6. Test Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    const bodyBg = await page.evaluate(() => {
      return window.getComputedStyle(document.body).backgroundColor;
    });
    expect(['rgb(20, 23, 26)', 'rgb(24, 27, 31)', 'rgb(31, 35, 40)']).toContain(bodyBg);

    await page.screenshot({ path: `${artifactDir}/help-center-05-dark.png`, fullPage: false });

    // 7. Verify Zero Console Errors
    const filteredErrors = consoleErrors.filter(err => 
      !err.includes('favicon') && 
      !err.includes('DevTools')
    );
    expect(filteredErrors).toEqual([]);
  });

  test('E2E: Peta Alur 3D (/alur-3d) Loads Cleanly', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    await page.goto('alur-3d');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    await page.screenshot({ path: `${artifactDir}/help-alur-3d-01.png`, fullPage: false });

    const stage = page.locator('#stage');
    await expect(stage).toBeVisible();

    const filteredErrors = consoleErrors.filter(err => 
      !err.includes('favicon') && 
      !err.includes('DevTools') &&
      !err.includes('WebGL') &&
      !err.includes('three')
    );
    expect(filteredErrors).toEqual([]);
  });
});
