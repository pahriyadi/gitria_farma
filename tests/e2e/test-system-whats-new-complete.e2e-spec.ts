import { test, expect } from '@playwright/test';

test.describe('What\'s New & Release Center Complete Verification', () => {
  test.setTimeout(60000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
    });

    // Auto-accept all browser confirm dialogs
    page.on('dialog', async dialog => {
      console.log('Browser Dialog accepted:', dialog.message());
      await dialog.accept();
    });

    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('Verify /system/whats-new - Light & Dark Mode, Search, Filters, Modals, CRUD & Links', async ({ page }) => {
    // ------------------------------------------------------------------------
    // 1. LIGHT MODE VERIFICATION
    // ------------------------------------------------------------------------
    await page.goto('system/whats-new');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(400);

    // Verify Title & Hero Banner
    const pageTitle = page.locator('.wn-page-title').first();
    await expect(pageTitle).toContainText("Apa yang Baru? (What's New)");

    const heroCard = page.locator('.wn-hero-card');
    await expect(heroCard).toBeVisible();

    // Verify Inter-page links in Hero
    const linkLaporan = page.locator('.wn-hero-card a[href*="accounting/laporan"]');
    await expect(linkLaporan).toBeVisible();
    const linkPasien = page.locator('.wn-hero-card a[href*="klinik/pendaftaran"]');
    await expect(linkPasien).toBeVisible();

    // Verify Timeline items exist
    const timelineItems = page.locator('.update-item-wrapper');
    const itemCount = await timelineItems.count();
    console.log('Total visible release items:', itemCount);
    expect(itemCount).toBeGreaterThanOrEqual(3);

    // Verify Filter Toolbar
    const filterCard = page.locator('.wn-filter-card');
    await expect(filterCard).toBeVisible();

    // Test Live Search Filter
    const searchInput = page.locator('#filter-updates-search');
    await searchInput.fill('Visualisasi Peta Alur');
    await page.waitForTimeout(300);
    const visibleAfterSearch = page.locator('.update-item-wrapper:not(.d-none)');
    await expect(visibleAfterSearch).toHaveCount(1);
    await searchInput.fill('');
    await page.waitForTimeout(300);

    // Capture Light Mode Screenshot
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/whats-new-verified-light.png'
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
    await page.waitForTimeout(400);

    // Verify Hero Card Dark Mode Tokens
    const heroBg = await heroCard.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Hero Card Dark Bg:', heroBg);
    expect(heroBg).toBe('rgb(31, 35, 40)'); // #1f2328

    // Verify Filter Card Dark Mode Tokens
    const filterBg = await filterCard.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Filter Card Dark Bg:', filterBg);
    expect(filterBg).toBe('rgb(31, 35, 40)'); // #1f2328

    // Verify Release Card Dark Mode Tokens
    const firstRelease = page.locator('.wn-release-card').first();
    const releaseBg = await firstRelease.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Release Card Dark Bg:', releaseBg);
    expect(releaseBg).toBe('rgb(24, 27, 31)'); // #181b1f

    // Verify Summary Box Dark Mode Tokens
    const summaryBox = page.locator('.wn-summary-box').first();
    const summaryBg = await summaryBox.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Summary Box Dark Bg:', summaryBg);
    expect(summaryBg).not.toBe('rgb(255, 255, 255)');
    expect(summaryBg).not.toBe('rgb(248, 250, 252)');

    // Verify Details Box Dark Mode Tokens
    const detailsBox = page.locator('.wn-details-box').first();
    const detailsBg = await detailsBox.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Details Box Dark Bg:', detailsBg);
    expect(detailsBg).toBe('rgb(38, 43, 50)'); // #262b32

    // Capture Dark Mode Screenshot
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/whats-new-verified-dark.png'
    });

    // ------------------------------------------------------------------------
    // 3. MODAL ADD RELEASE (VERIFY IN DARK MODE)
    // ------------------------------------------------------------------------
    await page.click('button[data-target="#modalAddUpdate"]');
    await page.waitForTimeout(400);

    const modalAdd = page.locator('#modalAddUpdate');
    await expect(modalAdd).toBeVisible();

    const modalAddContent = page.locator('#modalAddUpdate .wn-modal-content');
    const modalAddBg = await modalAddContent.evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Modal Add Dark Bg:', modalAddBg);
    expect(modalAddBg).toBe('rgb(31, 35, 40)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/whats-new-verified-modal-add-dark.png'
    });

    await page.click('#modalAddUpdate button[data-dismiss="modal"]');
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // 4. MODAL EDIT RELEASE (VERIFY IN DARK MODE)
    // ------------------------------------------------------------------------
    await page.locator('.btn-edit-update').first().click();
    await page.waitForTimeout(400);

    const modalEdit = page.locator('#modalEditUpdate');
    await expect(modalEdit).toBeVisible();
    await expect(page.locator('#edit-up-version')).toHaveValue(/.+/);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/whats-new-verified-modal-edit-dark.png'
    });

    await page.click('#modalEditUpdate button[data-dismiss="modal"]');
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // 5. FULL CRUD WORKFLOW: CREATE -> TOGGLE -> DELETE
    // ------------------------------------------------------------------------
    // CREATE:
    await page.click('button[data-target="#modalAddUpdate"]');
    await page.waitForTimeout(400);

    await page.fill('#modalAddUpdate input[name="version"]', 'v9.9.9-e2e');
    await page.fill('#modalAddUpdate input[name="title"]', 'Fitur Pengujian Otomatis E2E Sentinel');
    await page.evaluate(() => {
      $('#modalAddUpdate select[name="category"]').val('KEAMANAN SISTEM');
    });
    await page.fill('#modalAddUpdate textarea[name="summary"]', 'Uji coba penambahan catatan rilis secara otomatis.');
    await page.fill('#modalAddUpdate textarea[name="details"]', '• Poin audit otomatis 1\n• Poin audit otomatis 2');
    await page.click('#modalAddUpdate button[type="submit"]');

    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verify item created
    const createdItem = page.locator('.update-item-wrapper', { hasText: 'v9.9.9-e2e' });
    await expect(createdItem).toBeVisible();

    // TOGGLE STATUS:
    const toggleLink = createdItem.locator('a[href*="toggle"]');
    await toggleLink.click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verify status changed to DRAFT
    const draftBadge = page.locator('.update-item-wrapper', { hasText: 'v9.9.9-e2e' }).locator('.badge-secondary');
    await expect(draftBadge).toContainText('DRAFT');

    // DELETE:
    const deleteBtn = page.locator('.update-item-wrapper', { hasText: 'v9.9.9-e2e' }).locator('.btn-delete-release');
    await deleteBtn.click({ force: true });
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verify item is removed
    const deletedCount = await page.locator('.update-item-wrapper', { hasText: 'v9.9.9-e2e' }).count();
    expect(deletedCount).toBe(0);

    // ------------------------------------------------------------------------
    // 6. GLOBAL WHATS NEW POPUP (#modal-whats-new-popup)
    // ------------------------------------------------------------------------
    await page.evaluate(() => {
      $('#modal-whats-new-popup').modal('show');
    });
    await page.waitForTimeout(500);

    const globalPopup = page.locator('#modal-whats-new-popup');
    await expect(globalPopup).toBeVisible();

    // Verify Dark Mode inside popup
    const popupBodyBg = await page.locator('#modal-whats-new-popup .modal-body').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Global Popup Dark Body Bg:', popupBodyBg);
    expect(popupBodyBg).toBe('rgb(31, 35, 40)');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/whats-new-verified-global-popup-dark.png'
    });

    await page.click('#modal-whats-new-popup button[data-dismiss="modal"]');
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // 7. INTER-PAGE NAVIGATION LINK: LAPORAN KEUANGAN
    // ------------------------------------------------------------------------
    await page.locator('.wn-hero-card a[href*="accounting/laporan"]').click();
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('accounting/laporan');
    await expect(page.locator('.accounting-tabs-scroll-wrapper')).toBeVisible();

    console.log('ALL WHATS NEW VERIFICATIONS COMPLETED SUCCESSFULLY!');
  });
});
