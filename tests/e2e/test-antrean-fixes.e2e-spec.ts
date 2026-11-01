import { test, expect } from '@playwright/test';

test.describe('E2E Verification: /klinik/antrean Time Keeper & Monitor', () => {
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

  test('1. Verify Dark Mode styling on /klinik/antrean (Toolbar, Buttons, Tabs, Table)', async ({ page }) => {
    await page.goto('klinik/antrean?tab=active');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(600);

    // Verify TV button contrast
    const btnTvColor = await page.evaluate(() => {
      const el = document.querySelector('a:has(i.fa-tv)');
      const comp = window.getComputedStyle(el);
      return { color: comp.color, borderColor: comp.borderColor };
    });
    console.log('Dark Mode TV button colors:', btnTvColor);
    // Should NOT be dark black rgb(6, 7, 8)
    expect(btnTvColor.color).not.toBe('rgb(6, 7, 8)');

    // Verify Active Tab
    const activeTab = await page.locator('#antrean-status-tabs .nav-link.active');
    await expect(activeTab).toBeVisible();
    await expect(activeTab).toContainText('Antrean Aktif');

    // Verify Inactive Tab
    const inactiveTab = await page.locator('#antrean-status-tabs a:has-text("Selesai Dilayani")');
    await expect(inactiveTab).toBeVisible();

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-verified-dark-active.png',
      fullPage: true
    });
  });

  test('2. Verify Light Mode styling on /klinik/antrean (Paper White)', async ({ page }) => {
    await page.goto('klinik/antrean?tab=active');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(600);

    const toolbarBg = await page.evaluate(() => {
      const el = document.querySelector('.antrean-toolbar-card');
      return window.getComputedStyle(el).backgroundColor;
    });
    console.log('Light Mode Toolbar bg:', toolbarBg);
    expect(toolbarBg).toBe('rgb(255, 255, 255)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-verified-light-active.png',
      fullPage: true
    });
  });

  test('3. Verify Completed Tab shows finished patient and survives LiveSync polling', async ({ page }) => {
    await page.goto('klinik/antrean?tab=completed');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });

    // Wait for at least 2 LiveSync polling cycles (3-4 seconds)
    await page.waitForTimeout(3500);

    // Patient Pahriyadi, S.Ap MUST be visible in the table!
    const tableBodyText = await page.locator('#live-antrean-body').innerText();
    console.log('Completed tab body text after LiveSync:', tableBodyText);
    expect(tableBodyText).toContain('Pahriyadi, S.Ap');
    expect(tableBodyText).toContain('Pelayanan Tuntas');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-verified-dark-completed.png',
      fullPage: true
    });

    // Switch to Light mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(1000);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-verified-light-completed.png',
      fullPage: true
    });
  });

  test('4. Verify Reassign Doctor Modal in Dark Mode', async ({ page }) => {
    await page.goto('klinik/antrean?tab=active');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
      $('#modal-reassign-doctor').modal('show');
    });
    await page.waitForTimeout(500);

    const modalStyles = await page.evaluate(() => {
      const content = document.querySelector('.antrean-modal-content');
      const footer = document.querySelector('.antrean-modal-footer');
      const label = document.querySelector('.antrean-modal-label');
      return {
        contentBg: window.getComputedStyle(content).backgroundColor,
        footerBg: window.getComputedStyle(footer).backgroundColor,
        labelColor: window.getComputedStyle(label).color
      };
    });
    console.log('Modal Dark Mode styles:', modalStyles);
    expect(modalStyles.contentBg).toBe('rgb(31, 35, 40)');
    expect(modalStyles.footerBg).toBe('rgb(24, 27, 31)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-verified-modal-dark.png'
    });
  });
});
