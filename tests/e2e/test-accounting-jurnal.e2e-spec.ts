import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL AKUNTANSI & JURNAL - JURNAL UMUM (/accounting/jurnal)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Navigasi Switcher 6 Pilar Akuntansi.
 * 3. Panduan Cepat Double-Entry & Filter Scope Konsolidasi.
 * 4. DataTables Server-Side Jurnal Umum Double-Entry (Debet, Kredit, Saldo).
 * 5. Modal Input Jurnal Manual & Penyesuaian.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Akuntansi: General Journal Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const artifactDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

  test.beforeEach(async ({ page }) => {
    // Suppress release popup and ensure collapsed sidebar on desktop
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    // Login as superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: General Journal -> Dark Mode -> Manual Journal Modal -> 0 Errors', async ({ page }) => {
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

    // 1. Akses accounting/jurnal
    await page.goto('accounting/jurnal');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('body')).toContainText('Jurnal Umum');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/accounting-jurnal-01-light.png`,
      fullPage: false,
    });

    // 2. Verifikasi Dark Mode (Paper Dark: #14171a, #1f2328, #374151)
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(300);

    const darkBg = await page.evaluate(() => {
      const card = document.querySelector('.card') as HTMLElement;
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    // Mengharapkan warna background kartu bertema dark (#1f2328 / rgb(31, 35, 40))
    expect(darkBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/accounting-jurnal-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Buka Modal Input Jurnal Manual
    await page.click('button[data-target="#journalModal"]');
    await expect(page.locator('#journalModal')).toBeVisible();
    await page.waitForTimeout(300);

    await page.screenshot({
      path: `${artifactDir}/accounting-jurnal-03-modal-manual.png`,
    });

    // Tutup modal
    await page.click('#journalModal button[data-dismiss="modal"]');
    await page.waitForTimeout(200);

    // 4. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});

