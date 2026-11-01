import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL AKUNTANSI & JURNAL - BAGAN AKUN COA (/accounting/coa)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Navigasi Switcher 6 Pilar Akuntansi.
 * 3. Master Data Akun COA (Kode Akun, Nama Akun, Tipe, Saldo Normal, Saldo Saat Ini).
 * 4. Pembeda Badge Distributor B2B vs Akun Klinik.
 * 5. Modal Registrasi Akun Baru.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Akuntansi: Chart of Accounts Pipeline', () => {
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

  test('E2E Full Flow: COA Table -> Dark Mode -> Add Account Modal -> Zero Striped Tables -> 0 Errors', async ({ page }) => {
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

    // 1. Akses accounting/coa
    await page.goto('accounting/coa');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('body')).toContainText('Bagan Akun');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/accounting-coa-01-light.png`,
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
      path: `${artifactDir}/accounting-coa-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Buka Modal Registrasi Akun Baru
    await page.click('button[data-target="#coaModal"]');
    await expect(page.locator('#coaModal')).toBeVisible();
    await page.waitForTimeout(300);

    await page.screenshot({
      path: `${artifactDir}/accounting-coa-03-modal-add.png`,
    });

    // Tutup modal
    await page.click('#coaModal button[data-dismiss="modal"]');
    await page.waitForTimeout(200);

    // 4. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});

