import { test, expect } from '@playwright/test';

test.describe('Klinik Pendaftaran In-Depth Workflow & Communication Test', () => {
  test.setTimeout(90000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
    });

    page.on('dialog', async dialog => {
      console.log('Dialog:', dialog.message());
      await dialog.accept();
    });

    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('Inspect /klinik/pendaftaran functionality, UI, DataTables, Modals & Links', async ({ page }) => {
    // 1. Visit /klinik/pendaftaran in Light Mode
    await page.goto('klinik/pendaftaran');
    await page.waitForLoadState('domcontentloaded');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(800);

    // Verify DataTables loads
    const patientRows = page.locator('#table-patients tbody tr');
    await expect(patientRows.first()).toBeVisible();
    const rowCount = await patientRows.count();
    console.log('Patients Table Row Count:', rowCount);

    // Take screenshot of main page in light mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-test-light.png'
    });

    // 2. Test Patient Detail Modal (#modalPatientDetail)
    const detailBtn = page.locator('.btn-show-patient-detail').first();
    await detailBtn.click();
    await page.waitForTimeout(600);
    const detailModal = page.locator('#modalPatientDetail');
    await expect(detailModal).toBeVisible();
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-detail-light.png'
    });
    await page.locator('#modalPatientDetail button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // 3. Test RME History Modal (#modalMedicalHistory)
    const rmeBtn = page.locator('.btn-show-rme').first();
    await rmeBtn.click();
    await page.waitForTimeout(600);
    const rmeModal = page.locator('#modalMedicalHistory');
    await expect(rmeModal).toBeVisible();
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-rme-light.png'
    });
    await page.locator('#modalMedicalHistory button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // 4. Test Visit Registration Modal (#visitModal)
    const visitBtn = page.locator('.btn-visit').first();
    await visitBtn.click();
    await page.waitForTimeout(600);
    const visitModal = page.locator('#visitModal');
    await expect(visitModal).toBeVisible();
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-visit-light.png'
    });
    await page.locator('#visitModal button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // 5. Test Dark Mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.add('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(600);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-test-dark.png'
    });

    // Dark Mode Modal Detail
    await detailBtn.click();
    await page.waitForTimeout(600);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-detail-dark.png'
    });
    await page.locator('#modalPatientDetail button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // Dark Mode Modal RME
    await rmeBtn.click();
    await page.waitForTimeout(600);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-rme-dark.png'
    });
    await page.locator('#modalMedicalHistory button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // Dark Mode Modal Visit
    await visitBtn.click();
    await page.waitForTimeout(600);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-modal-visit-dark.png'
    });
    await page.locator('#visitModal button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    console.log('ALL DIAGNOSTIC SCREENSHOTS CAPTURED!');
  });
});
