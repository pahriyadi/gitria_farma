import { test, expect } from '@playwright/test';

test.describe('System: Satu Data Referensi & Pengaturan Sistem Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const screenshotsDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

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

  test('E2E Full Flow: Settings -> 8 Tabs -> Dark Mode -> 0 Striped Tables -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // 1. Navigate to system/settings
    await page.goto('system/settings');
    await page.waitForLoadState('domcontentloaded');

    // 2. Verify Light Mode initial state
    await page.screenshot({ path: `${screenshotsDir}/system-settings-01-light.png`, fullPage: false });

    // 3. Test Navigation Tabs
    // Tab 2: Branding & Kop Surat
    const brandingTab = page.locator('#branding-tab');
    await expect(brandingTab).toBeVisible();
    await brandingTab.click();
    await page.waitForTimeout(400);
    await expect(page.locator('#sim-contacts')).toBeVisible();
    await page.screenshot({ path: `${screenshotsDir}/system-settings-02-branding.png`, fullPage: false });

    // Tab 3: WhatsApp Gateway
    const waTab = page.locator('#api-tab');
    await expect(waTab).toBeVisible();
    await waTab.click();
    await page.waitForTimeout(400);
    await expect(page.locator('#btn-ping-wa')).toBeVisible();

    // Tab 6: Display TV & Voice
    const voiceTab = page.locator('#display-voice-tab');
    await expect(voiceTab).toBeVisible();
    await voiceTab.click();
    await page.waitForTimeout(400);
    await expect(page.locator('#btn-test-full-voice')).toBeVisible();
    await page.screenshot({ path: `${screenshotsDir}/system-settings-03-voice.png`, fullPage: false });

    // Tab 7: Diagnostik Sistem (Contains diagnostic tables)
    const diagTab = page.locator('#diagnostics-tab');
    await expect(diagTab).toBeVisible();
    await diagTab.click();
    await page.waitForTimeout(400);
    await page.screenshot({ path: `${screenshotsDir}/system-settings-04-diagnostics.png`, fullPage: false });

    // Tab 8: SATUSEHAT
    const satusehatTab = page.locator('#satusehat-tab');
    await expect(satusehatTab).toBeVisible();
    await satusehatTab.click();
    await page.waitForTimeout(400);
    await expect(page.locator('#btn-ping-satusehat')).toBeVisible();
    await page.screenshot({ path: `${screenshotsDir}/system-settings-05-satusehat.png`, fullPage: false });

    // 4. Verify Zero .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // 5. Test Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    const bodyBg = await page.evaluate(() => {
      return window.getComputedStyle(document.body).backgroundColor;
    });
    expect(['rgb(20, 23, 26)', 'rgb(24, 27, 31)', 'rgb(31, 35, 40)']).toContain(bodyBg);

    await page.screenshot({ path: `${screenshotsDir}/system-settings-06-dark.png`, fullPage: false });

    // 6. Verify Zero Console Errors
    const filteredErrors = consoleErrors.filter(err => 
      !err.includes('favicon') && 
      !err.includes('AudioContext') && 
      !err.includes('speechSynthesis') && 
      !err.includes('DevTools')
    );
    expect(filteredErrors).toEqual([]);
  });
});
