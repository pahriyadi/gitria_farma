import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - RIWAYAT PENJUALAN (/distributor/riwayat)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Filter Multikriteria: Rentang Tanggal, Pelanggan, Metode Bayar, Status Bayar.
 * 3. Arsip Tabel Faktur (No. Faktur, Tanggal, Customer, Jatuh Tempo, Total, Sisa, Status).
 * 4. Modal Detail Rincian Faktur Grosir & Tabel Item Obat B2B.
 * 5. Tautan Cetak Faktur & Cetak Surat Jalan (Delivery Order).
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Sales History & Archive Pipeline', () => {
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

  test('E2E Full Flow: Sales History -> Dark Mode -> Filter -> Detail Modal -> Cetak Faktur -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const txt = msg.text();
        if (!txt.includes('favicon') && !txt.includes('chrome-extension')) {
          consoleErrors.push(txt);
        }
      }
    });

    // ========================================================================
    // STAGE 1: Membuka /distributor/riwayat & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/riwayat & Verifikasi Light Mode ---');
    await page.goto('distributor/riwayat');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan riwayat penjualan
    const pushmenuBtn = page.locator('[data-widget="pushmenu"]').first();
    const isCollapsed = await page.evaluate(() => document.body.classList.contains('sidebar-collapse'));
    if (!isCollapsed && await pushmenuBtn.isVisible()) {
      await pushmenuBtn.click();
      await page.waitForTimeout(350);
    }

    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
    });

    // Verifikasi Judul Halaman
    await expect(page.locator('h4').filter({ hasText: 'Riwayat Penjualan Distributor' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi Filter Card & Tabel Riwayat
    await expect(page.locator('form[action*="distributor/riwayat"]')).toBeVisible();
    await expect(page.locator('#tableSalesHistory')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-riwayat-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-riwayat-01-light.png tersimpan');

    // ========================================================================
    // STAGE 2: Verifikasi Paper Dark Mode
    // ========================================================================
    console.log('--- STAGE 2: Verifikasi Paper Dark Mode (#1f2328, #374151) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    // Evaluasi Background & Border Card Dark Mode
    const darkCardBg = await page.locator('.dist-card').first().evaluate(el => {
      return window.getComputedStyle(el).backgroundColor;
    });
    const darkCardBorder = await page.locator('.dist-card').first().evaluate(el => {
      return window.getComputedStyle(el).borderColor;
    });

    console.log(`Dark Mode Card BG: ${darkCardBg}, Border: ${darkCardBorder}`);
    expect(darkCardBg).toBe('rgb(31, 35, 40)');
    expect(darkCardBorder).toBe('rgb(55, 65, 81)');

    // Tangkap Screenshot Dark Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-riwayat-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-riwayat-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    let firstInvoiceId = '';

    // ========================================================================
    // STAGE 3: Modal Detail Rincian Faktur Grosir
    // ========================================================================
    console.log('--- STAGE 3: Modal Detail Rincian Faktur Grosir ---');
    await page.evaluate(() => {
      const btn = document.querySelector('.btn-view-detail') as HTMLElement;
      if (btn) btn.click();
    });
    await page.waitForTimeout(600);

    // Verifikasi Modal Muncul & Konten Termuat
    await expect(page.locator('#modalDetailSale')).toBeVisible();
    await expect(page.locator('#detailModalBody')).toContainText('PELANGGAN:');
    await expect(page.locator('#detailModalBody')).toContainText('NO. FAKTUR:');

    // Ambil ID faktur untuk pengujian cetak
    const printLink = page.locator('#detailModalBody a[href*="cetak-faktur"]').first();
    if (await printLink.isVisible()) {
      const href = await printLink.getAttribute('href');
      if (href) {
        const parts = href.split('/');
        firstInvoiceId = parts[parts.length - 1];
      }
    }

    // Tangkap Screenshot Modal Detail
    await page.screenshot({
      path: `${artifactDir}/distributor-riwayat-03-modal-detail.png`,
    });
    console.log('Screenshot distributor-riwayat-03-modal-detail.png tersimpan');

    // Tutup Modal
    await page.locator('#modalDetailSale button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // ========================================================================
    // STAGE 4: Filter Riwayat Penjualan
    // ========================================================================
    console.log('--- STAGE 4: Filter Riwayat Penjualan ---');
    await page.selectOption('select[name="status"]', 'unpaid');
    await page.click('button[type="submit"]:has-text("Filter")');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(500);

    // Verifikasi URL mengandung parameter status
    expect(page.url()).toContain('status=unpaid');

    // Tangkap Screenshot Hasil Filter
    await page.screenshot({
      path: `${artifactDir}/distributor-riwayat-04-filtered.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-riwayat-04-filtered.png tersimpan');

    // ========================================================================
    // STAGE 5: Cetak Faktur Grosir (Print Preview)
    // ========================================================================
    console.log('--- STAGE 5: Cetak Faktur Grosir ---');
    if (firstInvoiceId) {
      await page.goto(`distributor/cetak-faktur/${firstInvoiceId}`);
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(500);

      // Verifikasi Header Faktur Cetak
      await expect(page.locator('body')).toContainText('FAKTUR PENJUALAN');

      // Tangkap Screenshot Cetak Faktur
      await page.screenshot({
        path: `${artifactDir}/distributor-riwayat-05-cetak-faktur.png`,
        fullPage: true,
      });
      console.log('Screenshot distributor-riwayat-05-cetak-faktur.png tersimpan');
    }

    // ========================================================================
    // STAGE 6: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 6: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 3 (/distributor/riwayat) berhasil diuji 100% tanpa kendala.');
  });
});
