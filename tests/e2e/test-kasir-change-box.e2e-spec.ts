import { test, expect } from '@playwright/test';

test.describe('Kasir Live Change Calculator Visual & Theme Verification', () => {
  test.setTimeout(60000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    // Login
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Verify Cash Calculator in Dark Mode and Light Mode', async ({ page }) => {
    await page.goto('keuangan/kasir');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 1. Check if dark mode is active, or activate it
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      localStorage.setItem('sawamawa_theme', 'dark');
    });
    await page.waitForTimeout(300);

    // 2. Click the first bill-item if available
    const billItems = page.locator('.bill-item');
    const count = await billItems.count();
    console.log('Bill items count in Kasir:', count);

    if (count > 0) {
      await billItems.first().click();
      await page.waitForTimeout(500);

      // Verify settlement card is visible
      await expect(page.locator('#settlement-card')).toBeVisible();

      // Click "Uang Pas"
      await page.click('#btn-exact-money');
      await page.waitForTimeout(300);

      // Capture screenshot in Dark Mode
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/kasir-dark-mode-uang-pas.png',
        fullPage: false
      });

      // Get styles of the change box
      const changeBoxStyles = await page.evaluate(() => {
        const box = document.getElementById('box-change-display');
        const title = document.getElementById('lbl-change-title');
        const amount = document.getElementById('lbl-change-amount');
        if (!box) return null;
        return {
          boxClasses: box.className,
          boxBg: window.getComputedStyle(box).backgroundColor,
          boxBorder: window.getComputedStyle(box).borderColor,
          titleText: title ? title.innerText : '',
          titleColor: title ? window.getComputedStyle(title).color : '',
          amountText: amount ? amount.innerText : '',
          amountColor: amount ? window.getComputedStyle(amount).color : ''
        };
      });

      console.log('Dark Mode Change Box Styles:', JSON.stringify(changeBoxStyles, null, 2));

      // Test Light Mode
      await page.evaluate(() => {
        document.body.classList.remove('dark-mode');
        document.documentElement.classList.remove('dark-mode');
        localStorage.setItem('sawamawa_theme', 'light');
      });
      await page.waitForTimeout(300);

      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/kasir-light-mode-uang-pas.png',
        fullPage: false
      });

      const lightBoxStyles = await page.evaluate(() => {
        const box = document.getElementById('box-change-display');
        const title = document.getElementById('lbl-change-title');
        const amount = document.getElementById('lbl-change-amount');
        if (!box) return null;
        return {
          boxClasses: box.className,
          boxBg: window.getComputedStyle(box).backgroundColor,
          boxBorder: window.getComputedStyle(box).borderColor,
          titleText: title ? title.innerText : '',
          titleColor: title ? window.getComputedStyle(title).color : '',
          amountText: amount ? amount.innerText : '',
          amountColor: amount ? window.getComputedStyle(amount).color : ''
        };
      });

      console.log('Light Mode Change Box Styles:', JSON.stringify(lightBoxStyles, null, 2));
    }
  });
});

