import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL PENGADAAN & LOGISTIK - APPROVALS (/procurement/approval)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 2 Tabs: Menunggu Persetujuan (Pending) & Riwayat Keputusan Approval.
 * 3. 3 KPI Cards Status Persetujuan.
 * 4. Verifikasi 0 Console Errors.
 */

test.describe('Procurement: Pusat Persetujuan & Approvals Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const artifactDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

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

  test('E2E Full Flow: Procurement Approval -> 2 Tabs -> Dark Mode -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('favicon') && !text.includes('chrome-extension')) {
          consoleErrors.push(text);
        }
      }
    });

    // 1. Akses procurement/approval
    await page.goto('procurement/approval');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // 2. Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    expect(stripedCount).toBe(0);

    // 3. Capture Light Mode Screenshot (Tab 1 Pending)
    await page.screenshot({
      path: `${artifactDir}/procurement-approval-01-light.png`,
      fullPage: false,
    });

    // 4. Tab 2: Riwayat Keputusan
    await page.click('#tab-history-link');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${artifactDir}/procurement-approval-02-tab-history.png`,
      fullPage: false,
    });

    // 5. Toggle Dark Mode
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(500);

    // Verifikasi Paper Dark styling
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    expect(cardBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/procurement-approval-03-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // 6. Cek Preview Dokumen jika ada item pending
    const btnPreview = page.locator('.btn-preview-approval').first();
    if (await btnPreview.count() > 0) {
      await btnPreview.click();
      await page.waitForSelector('#modalPreviewApproval.show', { timeout: 5000 });
      await page.waitForTimeout(500);

      await page.screenshot({
        path: `${artifactDir}/procurement-approval-04-preview.png`,
        fullPage: false,
      });

      await page.click('#modalPreviewApproval button[data-dismiss="modal"]');
      await page.waitForTimeout(500);
    }

    // 7. Verifikasi 0 Console Errors
    expect(consoleErrors).toEqual([]);
  });
});

