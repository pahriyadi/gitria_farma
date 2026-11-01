import { test, expect } from '@playwright/test';

test.describe('System Notifications & Rules Engine Complete Verification', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
    });

    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('Verify /system/notifications - Light & Dark Mode, CRUD, Rules, Receipts & Links', async ({ page }) => {
    // ------------------------------------------------------------------------
    // 1. LIGHT MODE VERIFICATION
    // ------------------------------------------------------------------------
    await page.goto('system/notifications');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(600);

    // Verify Hero Card
    const heroCard = page.locator('.notif-hero-card');
    await expect(heroCard).toBeVisible();
    await expect(page.locator('.notif-hero-title')).toContainText('Riwayat Notifikasi & Manajemen Aturan');

    // Verify Broken Link Fix: Must point to accounting/jurnal, NOT akuntansi/jurnal
    const journalAuditBtn = page.locator('.notif-hero-card a[href*="accounting/jurnal"]');
    await expect(journalAuditBtn).toBeVisible();
    const wrongJournalBtn = page.locator('.notif-hero-card a[href*="akuntansi/jurnal"]');
    await expect(wrongJournalBtn).toHaveCount(0);

    // Verify 4 KPI Cards
    const kpiCards = page.locator('.notif-kpi-card');
    await expect(kpiCards).toHaveCount(4);

    // Verify Notification List items exist
    const notifItems = page.locator('.notif-item-card');
    const notifCount = await notifItems.count();
    console.log('Total visible notification items:', notifCount);
    expect(notifCount).toBeGreaterThan(0);

    // Capture Light Mode Screenshot
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/system-notifications-light.png',
      fullPage: true
    });

    // ------------------------------------------------------------------------
    // 2. DARK MODE VERIFICATION ("Paper Dark")
    // ------------------------------------------------------------------------
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.add('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(600);

    // Verify Hero Card background is NOT white
    const heroBg = await heroCard.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Hero Card Dark Bg:', heroBg);
    expect(heroBg).toBe('rgb(31, 35, 40)'); // #1f2328

    // Verify KPI Card background is NOT white
    const kpiFirstBg = await kpiCards.first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('KPI Card Dark Bg:', kpiFirstBg);
    expect(kpiFirstBg).toBe('rgb(31, 35, 40)'); // #1f2328

    // Verify Notification Item background is dark (#181b1f or #1e2632 for unread)
    const firstItemBg = await notifItems.first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('First Notification Item Dark Bg:', firstItemBg);
    expect(firstItemBg).not.toBe('rgb(255, 255, 255)');
    expect(firstItemBg).not.toBe('rgb(248, 250, 252)');

    // Capture Dark Mode Screenshot (Tab 1)
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/system-notifications-dark.png',
      fullPage: true
    });

    // ------------------------------------------------------------------------
    // 3. READ RECEIPTS MODAL (AUDIT JEJAK PEMBACA)
    // ------------------------------------------------------------------------
    const viewReceiptBtn = page.locator('.btn-view-receipts').first();
    await viewReceiptBtn.click();

    const receiptModal = page.locator('#modalReadReceipts');
    await expect(receiptModal).toBeVisible();
    await page.waitForTimeout(800);

    // Verify modal styling in dark mode
    const modalContent = page.locator('.notif-modal-content');
    const modalContentBg = await modalContent.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Modal Content Dark Bg:', modalContentBg);
    expect(modalContentBg).toBe('rgb(31, 35, 40)');

    // Capture Modal Screenshot
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/system-notifications-modal-receipts-dark.png'
    });

    // Close modal
    await page.locator('#modalReadReceipts button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // 4. CATEGORY & UNREAD FILTER NAVIGATION
    // ------------------------------------------------------------------------
    // Test filter: Belum Dibaca
    await page.click('a[href*="unread=1"]');
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('unread=1');

    // Test filter: Accounting
    await page.click('a[href*="category=accounting"]');
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('category=accounting');

    // Test filter: Farmasi
    await page.click('a[href*="category=pharmacy"]');
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('category=pharmacy');

    // Return to Semua
    await page.goto('system/notifications');
    await page.waitForLoadState('domcontentloaded');

    // ------------------------------------------------------------------------
    // 5. TAB 2: RULES ENGINE & TOGGLE ACTION
    // ------------------------------------------------------------------------
    // Switch to Tab 2
    await page.click('#notif-tabs a[href="#tab-rules"]');
    await page.waitForTimeout(500);

    // Verify rules table
    const rulesTable = page.locator('.table-clean-grid');
    await expect(rulesTable).toBeVisible();
    await expect(rulesTable.locator('tbody tr')).toHaveCount(10); // 10 seeded default rules

    // Re-apply dark mode to inspect Tab 2 in dark mode
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.add('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(400);

    // Capture Tab 2 Dark Mode Screenshot
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/system-notifications-rules-dark.png',
      fullPage: true
    });

    // Test Toggle Rule Action (e.g. for last rule or specific rule)
    page.on('dialog', async dialog => {
      console.log('Dialog opened:', dialog.message());
      await dialog.accept();
    });

    const toggleBtn = page.locator('#tab-rules a[href*="toggle-rule"]').last();
    const toggleHref = await toggleBtn.getAttribute('href');
    console.log('Toggling rule at:', toggleHref);
    await toggleBtn.click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verify flash message or successful redirect to #tab-rules
    expect(page.url()).toContain('tab=rules');

    // Toggle back to keep database clean
    const toggleBackBtn = page.locator('#tab-rules a[href*="toggle-rule"]').last();
    await toggleBackBtn.click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // ------------------------------------------------------------------------
    // 6. INTER-PAGE LINK: AUDIT KESEIMBANGAN JURNAL
    // ------------------------------------------------------------------------
    const auditBtn = page.locator('.notif-hero-card a[href*="accounting/jurnal"]');
    await auditBtn.click();
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('accounting/jurnal');
    // Verify accounting journal page loads successfully (HTTP 200)
    await expect(page.locator('.nav-tabs-modern .nav-link.active')).toContainText('1. Jurnal Umum');

    console.log('ALL VERIFICATIONS COMPLETED SUCCESSFULLY!');
  });
});
