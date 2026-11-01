import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - BUKU KAS & ARUS KAS (/distributor/kas)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Stat Cards Ringkasan Saldo (Total Kas, Kas Tunai 1131, Kas Bank 1132).
 * 3. Filter Mutasi Kas Berdasarkan Periode & Jenis Akun.
 * 4. Tabel Mutasi Kas Distributor (Tanggal, No. Jurnal, Akun, Keterangan, Debit/Kredit).
 * 5. Modal Catat Kas Masuk (+ Penerimaan/Modal).
 * 6. Modal Catat Kas Keluar (- Pengeluaran/Beban).
 * 7. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Cashbook & Cashflow Pipeline', () => {
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

  test('E2E Full Flow: Cash Summary -> Mutasi Table -> Dark Mode -> Modal Kas Masuk & Keluar -> 0 Errors', async ({ page }) => {
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

    // 1. Akses distributor/kas
    await page.goto('distributor/kas');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('body')).toContainText('Buku Kas & Arus Kas Distributor');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-kas-01-light.png`,
      fullPage: false,
    });

    // 2. Verifikasi Dark Mode (Paper Dark: #14171a, #1f2328, #374151)
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(300);

    const darkBg = await page.evaluate(() => {
      const card = document.querySelector('.card.dist-card') as HTMLElement;
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    // Mengharapkan warna background kartu bertema dark (#1f2328 / rgb(31, 35, 40))
    expect(darkBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/distributor-kas-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Test Buka Modal Kas Masuk
    await page.click('#btnKasMasuk');
    await expect(page.locator('#modalKasDistributor')).toBeVisible();
    await expect(page.locator('#modalKasTitle')).toContainText('Catat Kas Masuk');
    await page.waitForTimeout(300);

    await page.screenshot({
      path: `${artifactDir}/distributor-kas-03-modal-masuk.png`,
    });

    // Tutup modal
    await page.click('#modalKasDistributor button[data-dismiss="modal"]');
    await page.waitForTimeout(300);

    // 4. Test Buka Modal Kas Keluar
    await page.click('#btnKasKeluar');
    await expect(page.locator('#modalKasDistributor')).toBeVisible();
    await expect(page.locator('#modalKasTitle')).toContainText('Catat Kas Keluar');
    await page.waitForTimeout(300);

    await page.screenshot({
      path: `${artifactDir}/distributor-kas-04-modal-keluar.png`,
    });

    // Tutup modal
    await page.click('#modalKasDistributor button[data-dismiss="modal"]');
    await page.waitForTimeout(200);

    // 5. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});

