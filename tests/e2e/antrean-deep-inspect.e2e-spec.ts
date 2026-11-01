import { test, expect } from '@playwright/test';

test('Deep inspect /klinik/antrean styles and functionality', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto('login');
  await page.fill('#username_input', 'superadmin');
  await page.fill('#login-password', 'admin123');
  await page.click('#btn-submit-login');
  await expect(page).toHaveURL(/dashboard/);

  // Helper to suppress popup
  const dismissPopups = async () => {
    await page.evaluate(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      document.querySelectorAll('.modal, .modal-backdrop').forEach(el => el.remove());
      document.body.classList.remove('modal-open');
    });
  };

  // 1. Inspect Dark Mode
  await page.goto('klinik/antrean?tab=active');
  await page.evaluate(() => {
    localStorage.setItem('theme', 'dark');
    document.body.classList.add('dark-mode');
    document.documentElement.setAttribute('data-theme', 'dark');
  });
  await dismissPopups();
  await page.waitForTimeout(600);

  const darkModeStyles = await page.evaluate(() => {
    const getStyles = (sel) => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const comp = window.getComputedStyle(el);
      return {
        tag: el.tagName,
        className: el.className,
        color: comp.color,
        backgroundColor: comp.backgroundColor,
        borderColor: comp.borderColor,
        borderWidth: comp.borderWidth
      };
    };

    return {
      toolbarCard: getStyles('.card.bg-light'),
      filterLabel: getStyles('.card.bg-light label'),
      selectPoly: getStyles('#filter-poly-select'),
      selectDoc: getStyles('#filter-doctor-select'),
      btnReset: getStyles('a[title="Reset Filter"]'),
      btnTv: getStyles('a:has(i.fa-tv)'),
      btnKiosk: getStyles('a:has(i.fa-desktop)'),
      navTabs: Array.from(document.querySelectorAll('#antrean-status-tabs .nav-link')).map(a => ({
        text: (a as HTMLElement).innerText.trim(),
        className: a.className,
        color: window.getComputedStyle(a).color,
        backgroundColor: window.getComputedStyle(a).backgroundColor,
        borderColor: window.getComputedStyle(a).borderColor
      })),
      cardTitle: getStyles('.card-title'),
      thead: getStyles('#table-antrean thead'),
      thFirst: getStyles('#table-antrean thead th'),
      emptyState: getStyles('#live-antrean-body td')
    };
  });

  console.log('DARK MODE STYLES:\n', JSON.stringify(darkModeStyles, null, 2));

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-dark-diag.png',
    fullPage: true
  });

  // Test Modal in Dark Mode
  await page.evaluate(() => {
    $('#modal-reassign-doctor').modal('show');
  });
  await page.waitForTimeout(400);

  const modalStyles = await page.evaluate(() => {
    const getStyles = (sel) => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const comp = window.getComputedStyle(el);
      return {
        color: comp.color,
        backgroundColor: comp.backgroundColor
      };
    };
    return {
      modalContent: getStyles('#modal-reassign-doctor .modal-content'),
      modalHeader: getStyles('#modal-reassign-doctor .modal-header'),
      modalBodyText: getStyles('#modal-reassign-doctor .modal-body p'),
      modalLabel: getStyles('#modal-reassign-doctor .modal-body label'),
      modalSelect: getStyles('#modal-reassign-doctor select'),
      modalFooter: getStyles('#modal-reassign-doctor .modal-footer')
    };
  });
  console.log('MODAL DARK STYLES:\n', JSON.stringify(modalStyles, null, 2));

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-modal-dark.png'
  });

  await page.evaluate(() => {
    $('#modal-reassign-doctor').modal('hide');
  });
  await page.waitForTimeout(300);

  // 2. Inspect Light Mode
  await page.evaluate(() => {
    localStorage.setItem('theme', 'light');
    document.body.classList.remove('dark-mode');
    document.documentElement.setAttribute('data-theme', 'light');
  });
  await page.waitForTimeout(400);

  const lightModeStyles = await page.evaluate(() => {
    const getStyles = (sel) => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const comp = window.getComputedStyle(el);
      return {
        tag: el.tagName,
        className: el.className,
        color: comp.color,
        backgroundColor: comp.backgroundColor,
        borderColor: comp.borderColor
      };
    };

    return {
      toolbarCard: getStyles('.card.bg-light'),
      navTabs: Array.from(document.querySelectorAll('#antrean-status-tabs .nav-link')).map(a => ({
        text: (a as HTMLElement).innerText.trim(),
        className: a.className,
        color: window.getComputedStyle(a).color,
        backgroundColor: window.getComputedStyle(a).backgroundColor
      })),
      cardTitle: getStyles('.card-title'),
      thead: getStyles('#table-antrean thead'),
      emptyState: getStyles('#live-antrean-body td')
    };
  });

  console.log('LIGHT MODE STYLES:\n', JSON.stringify(lightModeStyles, null, 2));

  await page.screenshot({
    path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/antrean-light-diag.png',
    fullPage: true
  });
});

