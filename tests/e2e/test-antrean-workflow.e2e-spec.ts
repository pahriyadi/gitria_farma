import { test, expect } from '@playwright/test';

test.describe('E2E: Antrean Active Workflow & Actions', () => {
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

  test('Verifikasi antrean aktif, Alihkan Dokter, Skrining, dan Tampilan Dark/Light', async ({ page }) => {
    // 1. Buka antrean tab=active
    await page.goto('klinik/antrean?tab=active');
    await page.waitForLoadState('domcontentloaded');

    // Cek pasien Ahmad Santoso (VIP)
    const row = page.locator('tr:has-text("Bpk. Ahmad Santoso")');
    await expect(row).toBeVisible();
    await expect(row).toContainText('VIP');
    await expect(row).toContainText('A-002');

    // 2. Test tombol Panggil Suara
    const voiceBtn = row.locator('.btn-voice-call');
    await expect(voiceBtn).toBeVisible();

    // 3. Test modal Alihkan Dokter
    const reassignBtn = row.locator('.btn-reassign-doc');
    await expect(reassignBtn).toBeVisible();
    await reassignBtn.click();
    await page.waitForTimeout(400);

    const modal = page.locator('#modal-reassign-doctor');
    await expect(modal).toBeVisible();
    await expect(page.locator('#reassign-patient-name')).toContainText('Ahmad Santoso');

    // Pilih dokter lain dan submit
    await page.selectOption('#reassign-doctor-select', { index: 1 });
    await Promise.all([
      page.waitForNavigation(),
      page.click('#modal-reassign-doctor button[type="submit"]')
    ]);

    // Pastikan antrean tetap ada setelah pengalihan
    await expect(page.locator('tr:has-text("Bpk. Ahmad Santoso")')).toBeVisible();

    // 4. Test tombol Skrining (jika status masih waiting)
    const skriningBtn = page.locator('tr:has-text("Bpk. Ahmad Santoso") .btn-status-call');
    if (await skriningBtn.isVisible()) {
      await Promise.all([
        page.waitForNavigation(),
        skriningBtn.click()
      ]);

      const updatedRow = page.locator('tr:has-text("Bpk. Ahmad Santoso")');
      await expect(updatedRow).toBeVisible();
      const updatedStatus = await updatedRow.locator('td').nth(5).innerText();
      console.log('Status updated to:', updatedStatus);
      expect(updatedStatus).toMatch(/DIPANGGIL|SKRINING|CALLED/i);
    }

    // 5. Screenshot Light Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      document.body.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(500);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-active-workflow-light.png',
      fullPage: true
    });

    // 6. Screenshot Dark Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(500);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-active-workflow-dark.png',
      fullPage: true
    });
  });
});
