import { test, expect } from '@playwright/test';

test('Capture tabs in /klinik/antrean', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto('login');
  await page.fill('#username_input', 'superadmin');
  await page.fill('#login-password', 'admin123');
  await page.click('#btn-submit-login');
  await expect(page).toHaveURL(/dashboard/);

  await page.goto('klinik/antrean');
  await page.waitForTimeout(800);

  // Prevent popup
  await page.evaluate(() => {
    localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
    document.querySelectorAll('.modal, .modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
  });

  // Click "Selesai Dilayani Hari Ini"
  const completedTab = page.locator('text=Selesai Dilayani Hari Ini, [data-tab="selesai"], a:has-text("Selesai Dilayani")').first();
  if (await completedTab.isVisible()) {
    await completedTab.click();
    await page.waitForTimeout(500);
  }

  // Dark Mode
  await page.evaluate(() => {
    localStorage.setItem('theme', 'dark');
    document.body.classList.add('dark-mode');
    document.documentElement.setAttribute('data-theme', 'dark');
  });
  await page.waitForTimeout(500);

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-dark-tab-selesai.png'
  });

  // Light Mode
  await page.evaluate(() => {
    localStorage.setItem('theme', 'light');
    document.body.classList.remove('dark-mode');
    document.documentElement.setAttribute('data-theme', 'light');
  });
  await page.waitForTimeout(500);

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-light-tab-selesai.png'
  });
});
