import { test, expect } from '@playwright/test';

test.describe('Aturan Jurnal & Dynamic COA Split Complete Verification', () => {
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

  test('Verify accounting/aturan-jurnal in Light and Dark Mode', async ({ page }) => {
    // 1. Visit Main Aturan Jurnal in Light Mode
    await page.goto('accounting/aturan-jurnal');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(500);

    // Verify Alert Banner in Light Mode
    const alertLight = page.locator('.aturan-alert-guide');
    await expect(alertLight).toBeVisible();
    const alertBgLight = await alertLight.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Alert Bg Light:', alertBgLight);

    // Verify KPI Cards in Light Mode
    const kpiLight = page.locator('.kpi-card').first();
    await expect(kpiLight).toBeVisible();

    // Verify Table in Light Mode (Clean Grid, No zebra)
    const tableLight = page.locator('#table-aturan-jurnal');
    await expect(tableLight).toBeVisible();
    await expect(tableLight).not.toHaveClass(/table-striped/);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-verified-light.png',
      fullPage: true
    });

    // 2. Switch to Dark Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verify Alert Banner in Dark Mode (No stark white!)
    const alertBgDark = await alertLight.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Alert Bg Dark:', alertBgDark);
    expect(alertBgDark).not.toBe('rgb(248, 249, 250)');
    expect(alertBgDark).not.toBe('rgb(255, 255, 255)');

    // Verify KPI Card in Dark Mode
    const kpiBgDark = await kpiLight.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('KPI Bg Dark:', kpiBgDark);
    expect(kpiBgDark).not.toBe('rgb(255, 255, 255)');

    // Verify Table in Dark Mode
    const thBgDark = await page.locator('#table-aturan-jurnal thead th').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Table TH Bg Dark:', thBgDark);
    expect(thBgDark).not.toBe('rgb(248, 249, 250)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-verified-dark.png',
      fullPage: true
    });

    // 3. Test Modal Add Category in Dark Mode
    await page.click('button[data-target="#modalAddCategory"]');
    await page.waitForSelector('#modalAddCategory.show', { state: 'visible' });
    const modalCatFooter = await page.locator('#modalAddCategory .modal-footer').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Modal Cat Footer Bg Dark:', modalCatFooter);
    expect(modalCatFooter).not.toBe('rgb(248, 249, 250)');
    expect(modalCatFooter).not.toBe('rgb(255, 255, 255)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-verified-modal-cat-dark.png'
    });
    await page.click('#modalAddCategory .close');
    await page.waitForTimeout(400);

    // 4. Test Modal Default Template in Dark Mode
    await page.click('button[data-target="#modalDefaultTemplate"]');
    await page.waitForSelector('#modalDefaultTemplate.show', { state: 'visible' });
    const tplBoxBg = await page.locator('.aturan-template-box').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Template Box Bg Dark:', tplBoxBg);
    expect(tplBoxBg).not.toBe('rgb(248, 249, 250)');
    expect(tplBoxBg).not.toBe('rgb(255, 255, 255)');

    const modalTplFooter = await page.locator('#modalDefaultTemplate .modal-footer').evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(modalTplFooter).not.toBe('rgb(248, 249, 250)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-verified-modal-default-dark.png'
    });
    await page.click('#modalDefaultTemplate .close');
    await page.waitForTimeout(400);
  });

  test('Verify accounting/aturan-jurnal/detail in Light and Dark Mode + Live Simulator', async ({ page }) => {
    // 1. Visit Detail in Light Mode
    await page.goto('accounting/aturan-jurnal/detail/1');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(600);

    // Verify Header Card & Summary Box
    await expect(page.locator('.aturan-detail-header-card')).toBeVisible();
    await expect(page.locator('.aturan-summary-box')).toBeVisible();

    // Verify Sub-Rules Table (Clean grid, not striped)
    const detailTable = page.locator('.table-clean-grid');
    await expect(detailTable).toBeVisible();
    await expect(detailTable).not.toHaveClass(/table-striped/);

    // Wait for simulation to populate
    await page.waitForSelector('#sim_results_box table', { state: 'visible', timeout: 5000 });

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-detail-verified-light.png',
      fullPage: true
    });

    // 2. Switch Detail to Dark Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(600);

    // Verify Header Summary Box in Dark Mode
    const sumBoxBg = await page.locator('.aturan-summary-box').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Summary Box Bg Dark:', sumBoxBg);
    expect(sumBoxBg).not.toBe('rgb(248, 249, 250)');
    expect(sumBoxBg).not.toBe('rgb(255, 255, 255)');

    // Verify Simulation Result Table in Dark Mode (NO bg-white leak!)
    const simTable = page.locator('#sim_results_box table').first();
    const simTableBg = await simTable.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Sim Table Bg Dark:', simTableBg);
    expect(simTableBg).not.toBe('rgb(255, 255, 255)');

    const simTotalTr = page.locator('#sim_results_box tr.sim-tr-total').first();
    const simTotalTrBg = await simTotalTr.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Sim Total Tr Bg Dark:', simTotalTrBg);
    expect(simTotalTrBg).not.toBe('rgb(248, 249, 250)');
    expect(simTotalTrBg).not.toBe('rgb(255, 255, 255)');

    // Test Quick Preset click in simulator
    const presetBtn = page.locator('.btn-outline-teal.btn-xs').first();
    if (await presetBtn.isVisible()) {
      await presetBtn.click();
      await page.waitForTimeout(400);
      await expect(page.locator('#sim_balance_badge')).toBeVisible();
    }

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-detail-verified-dark.png',
      fullPage: true
    });

    // 3. Test Modal Add Rule in Dark Mode
    await page.click('button[data-target="#modalAddRule"]');
    await page.waitForSelector('#modalAddRule.show', { state: 'visible' });
    const modalRuleFooter = await page.locator('#modalAddRule .modal-footer').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Modal Rule Footer Bg Dark:', modalRuleFooter);
    expect(modalRuleFooter).not.toBe('rgb(248, 249, 250)');
    expect(modalRuleFooter).not.toBe('rgb(255, 255, 255)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/aturan-jurnal-detail-modal-rule-dark.png'
    });
    await page.click('#modalAddRule .close');
    await page.waitForTimeout(400);
  });
});

