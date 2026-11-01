import { test, expect } from '@playwright/test';

test.describe('Saldo Awal Complete Verification', () => {
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

  test('Verify accounting/saldo-awal in Light and Dark Mode + Real-Time Calculation', async ({ page }) => {
    // 1. Visit Saldo Awal in Light Mode
    await page.goto('accounting/saldo-awal');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(600);

    // Verify Main Card & Tabs in Light Mode
    await expect(page.locator('.saldo-awal-main-card')).toBeVisible();
    await expect(page.locator('.nav-tabs-modern .nav-link.active')).toContainText('6. Saldo Awal');

    // Verify Alert Info in Light Mode
    const alertInfo = page.locator('.alert.alert-info.alert-excel');
    await expect(alertInfo).toBeVisible();

    // Verify Tables (No zebra stripes, clean grid)
    const tables = page.locator('.table-clean-grid');
    const tableCount = await tables.count();
    expect(tableCount).toBeGreaterThanOrEqual(3);

    // Verify Right Column Cards
    await expect(page.locator('.saldo-balance-card')).toBeVisible();
    await expect(page.locator('.saldo-history-card')).toBeVisible();

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/saldo-awal-verified-light.png',
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

    // Verify Alert Info in Dark Mode (NO white background!)
    const alertInfoBgDark = await alertInfo.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Alert Info Bg Dark:', alertInfoBgDark);
    expect(alertInfoBgDark).not.toBe('rgb(255, 255, 255)');
    expect(alertInfoBgDark).not.toBe('rgb(248, 249, 250)');

    // Verify Auto-Balance Alert in Dark Mode (NO white background!)
    const alertSuccess = page.locator('.saldo-autobalance-alert');
    const alertSuccessBgDark = await alertSuccess.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Alert Success Bg Dark:', alertSuccessBgDark);
    expect(alertSuccessBgDark).not.toBe('rgb(255, 255, 255)');
    expect(alertSuccessBgDark).not.toBe('rgb(248, 249, 250)');

    // Verify Table Thead in Dark Mode
    const theadTh = page.locator('.saldo-thead th').first();
    const theadThBgDark = await theadTh.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Thead Th Bg Dark:', theadThBgDark);
    expect(theadThBgDark).not.toBe('rgb(255, 255, 255)');
    expect(theadThBgDark).not.toBe('rgb(248, 249, 250)');

    // Verify Main Card & Balance Card in Dark Mode
    const mainCardBgDark = await page.locator('.saldo-awal-main-card').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(mainCardBgDark).toBe('rgb(31, 35, 40)');

    const balanceCardBgDark = await page.locator('.saldo-balance-card').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(balanceCardBgDark).toBe('rgb(31, 35, 40)');

    // 3. Test Real-time Recalculation
    const firstAssetInput = page.locator('.input-asset-calc').first();
    await firstAssetInput.fill('5000000');
    await firstAssetInput.dispatchEvent('change');
    await page.waitForTimeout(300);

    const debitSumText = await page.locator('#sum-total-debit').innerText();
    console.log('Updated Debit Sum:', debitSumText);
    expect(debitSumText).toContain('5.317.500');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/saldo-awal-verified-dark.png',
      fullPage: true
    });
  });
});
