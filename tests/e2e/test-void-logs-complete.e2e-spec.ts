import { test, expect } from '@playwright/test';

test.describe('Void Logs & Anti-Fraud Center Complete Verification', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
    });

    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('Verify accounting/void-logs in Light and Dark Mode + Modals', async ({ page }) => {
    // 1. Visit Void Logs in Light Mode
    await page.goto('accounting/void-logs');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(600);

    // Verify Active Tab
    await expect(page.locator('.nav-tabs-modern .nav-link.active')).toContainText('7. Log Void');

    // Verify Alert Banner in Light Mode
    const alertGuide = page.locator('.void-alert-guide');
    await expect(alertGuide).toBeVisible();

    // Verify KPI Cards in Light Mode
    const kpiFirst = page.locator('.kpi-card').first();
    await expect(kpiFirst).toBeVisible();

    // Verify Table in Light Mode (Clean Grid, No zebra)
    const voidTable = page.locator('#table-void-logs');
    await expect(voidTable).toBeVisible();
    await expect(voidTable).not.toHaveClass(/table-striped/);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/void-logs-verified-light.png',
      fullPage: true
    });

    // 2. Switch to Dark Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(600);

    // Verify Alert Banner in Dark Mode (NO stark white!)
    const alertBgDark = await alertGuide.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Void Alert Bg Dark:', alertBgDark);
    expect(alertBgDark).not.toBe('rgb(255, 255, 255)');
    expect(alertBgDark).not.toBe('rgb(248, 249, 250)');

    // Verify KPI Card in Dark Mode
    const kpiBgDark = await kpiFirst.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('KPI Bg Dark:', kpiBgDark);
    expect(kpiBgDark).toBe('rgb(31, 35, 40)');

    // Verify Filter Card in Dark Mode
    const filterCardBgDark = await page.locator('.void-filter-card').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(filterCardBgDark).toBe('rgb(31, 35, 40)');

    // Verify Table Th in Dark Mode
    const thBgDark = await page.locator('#table-void-logs thead th').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Void Table TH Bg Dark:', thBgDark);
    expect(thBgDark).not.toBe('rgb(248, 249, 250)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/void-logs-verified-dark.png',
      fullPage: true
    });

    // 3. Test Modal Readme Void in Dark Mode
    await page.click('button[data-target="#modalReadmeVoid"]');
    await page.waitForSelector('#modalReadmeVoid.show', { state: 'visible' });

    const readmeWarningBg = await page.locator('.void-readme-warning').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Readme Warning Bg Dark:', readmeWarningBg);
    expect(readmeWarningBg).not.toBe('rgb(255, 243, 205)');

    const readmeFooterBg = await page.locator('#modalReadmeVoid .modal-footer').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(readmeFooterBg).not.toBe('rgb(248, 249, 250)');

    // Switch to tab 2 (table matrix) inside modal
    await page.click('#tab-modul-tab');
    await page.waitForTimeout(300);
    const matrixTable = page.locator('#tab-modul table');
    await expect(matrixTable).not.toHaveClass(/table-striped/);

    // Switch to tab 3 (steps) inside modal
    await page.click('#tab-langkah-tab');
    await page.waitForTimeout(300);
    const stepBoxBg = await page.locator('.void-step-box').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(stepBoxBg).toBe('rgb(38, 43, 50)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/void-logs-verified-modal-readme-dark.png'
    });
    await page.click('#modalReadmeVoid .close');
    await page.waitForTimeout(400);

    // 4. Test Modal Manage PIN in Dark Mode
    await page.click('button[data-target="#modalManagePin"]');
    await page.waitForSelector('#modalManagePin.show', { state: 'visible' });

    const pinLabelColor = await page.locator('.void-modal-label').first().evaluate(el => window.getComputedStyle(el).color);
    console.log('Pin Label Color Dark:', pinLabelColor);
    expect(pinLabelColor).not.toBe('rgb(33, 37, 41)');

    const pinFooterBg = await page.locator('#modalManagePin .modal-footer').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(pinFooterBg).not.toBe('rgb(248, 249, 250)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/void-logs-verified-modal-pin-dark.png'
    });
    await page.click('#modalManagePin .close');
    await page.waitForTimeout(400);
  });
});

