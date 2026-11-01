import { test, expect } from '@playwright/test';

test('Inspect completed tab and active tab with data', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto('login');
  await page.fill('#username_input', 'superadmin');
  await page.fill('#login-password', 'admin123');
  await page.click('#btn-submit-login');
  await expect(page).toHaveURL(/dashboard/);

  // Helper to remove only whats-new popup, not app modals!
  const suppressWhatsNew = async () => {
    await page.evaluate(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      const p = document.getElementById('modal-whats-new-popup');
      if (p) p.remove();
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      document.body.classList.remove('modal-open');
    });
  };

  // 1. Visit tab=completed in Dark Mode
  await page.goto('klinik/antrean?tab=completed');
  await suppressWhatsNew();
  await page.evaluate(() => {
    localStorage.setItem('theme', 'dark');
    document.body.classList.add('dark-mode');
    document.documentElement.setAttribute('data-theme', 'dark');
  });
  await page.waitForTimeout(1000); // Wait for LiveSync cycle

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-tab-completed-dark.png',
    fullPage: true
  });

  // Check table content in completed tab
  const completedTableData = await page.evaluate(() => {
    const rows = Array.from(document.querySelectorAll('#table-antrean tbody tr')).map(tr => ({
      cells: Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim().replace(/\n+/g, ' ')),
      html: tr.innerHTML
    }));
    return {
      activeTab: document.querySelector('#antrean-status-tabs .nav-link.active')?.innerText.trim(),
      headerTitle: document.querySelector('.card-title')?.innerText.trim(),
      rowsCount: rows.length,
      rows: rows
    };
  });
  console.log('COMPLETED TAB DATA IN DARK MODE:\n', JSON.stringify(completedTableData, null, 2));

  // 2. Visit tab=completed in Light Mode
  await page.evaluate(() => {
    localStorage.setItem('theme', 'light');
    document.body.classList.remove('dark-mode');
    document.documentElement.setAttribute('data-theme', 'light');
  });
  await page.waitForTimeout(500);

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-tab-completed-light.png',
    fullPage: true
  });

  // 3. Inspect reassign modal in Dark Mode
  await page.evaluate(() => {
    localStorage.setItem('theme', 'dark');
    document.body.classList.add('dark-mode');
    document.documentElement.setAttribute('data-theme', 'dark');
    $('#modal-reassign-doctor').modal('show');
  });
  await page.waitForTimeout(400);

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-reassign-modal-dark.png'
  });
});

