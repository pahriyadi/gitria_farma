import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - STOK & KARTU STOK (/distributor/stok)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Master Tabel Stok Gudang B2B (Batch, Expired Date, HPP, Harga Grosir, Saldo Stok, Status).
 * 3. Modal Tambah / Inbound Stok Gudang Distributor & Pencatatan Mutasi Masuk.
 * 4. Modal Buku Kartu Stok (Stock Card Ledger) dengan riwayat mutasi, referensi, saldo akhir.
 * 5. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Stock & Stock Card Pipeline', () => {
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

  test('E2E Full Flow: Stock Table -> Dark Mode -> Add Stock -> Kartu Stok Modal -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /distributor/stok & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/stok & Verifikasi Light Mode ---');
    await page.goto('distributor/stok');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi
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
    await expect(page.locator('h4').filter({ hasText: 'Stok Gudang Distributor & Kartu Stok' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi Tombol Tambah & Tabel Stok
    await expect(page.locator('#btnOpenAddStock')).toBeVisible();
    await expect(page.locator('#tableStocks')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-stok-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-stok-01-light.png tersimpan');

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
      path: `${artifactDir}/distributor-stok-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-stok-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Modal Tambah / Inbound Stok Gudang
    // ========================================================================
    console.log('--- STAGE 3: Modal Tambah / Inbound Stok ---');
    await page.click('#btnOpenAddStock');
    await page.waitForTimeout(400);

    // Verifikasi Modal Muncul
    await expect(page.locator('#modalStockForm')).toBeVisible();
    await expect(page.locator('#stock_med_id')).toBeVisible();
    await expect(page.locator('#stock_qty')).toBeVisible();

    // Tangkap Screenshot Modal Tambah Stok
    await page.screenshot({
      path: `${artifactDir}/distributor-stok-03-modal-add.png`,
    });
    console.log('Screenshot distributor-stok-03-modal-add.png tersimpan');

    // Isi formulir stok
    await page.selectOption('#stock_med_id', { index: 1 });
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    await page.fill('#stock_batch', `DIST-BATCH-${randomSuffix}`);
    await page.fill('#stock_buy_price', '5000');
    await page.fill('#stock_sell_price', '7500');
    await page.fill('#stock_qty', '50');
    await page.fill('#stock_notes', 'Penerimaan Stok Inbound Testing E2E');

    // Simpan data
    await page.click('#btnSubmitStock');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi Data Tersimpan
    await expect(page.locator('#tableStocks')).toContainText(`DIST-BATCH-${randomSuffix}`);

    // Tangkap Screenshot Stok Tersimpan
    await page.screenshot({
      path: `${artifactDir}/distributor-stok-04-stock-saved.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-stok-04-stock-saved.png tersimpan');

    // ========================================================================
    // STAGE 4: Modal Buku Kartu Stok Mutasi
    // ========================================================================
    console.log('--- STAGE 4: Modal Buku Kartu Stok Mutasi ---');
    await page.evaluate(() => {
      const btn = document.querySelector('.btn-kartu-stok') as HTMLElement;
      if (btn) btn.click();
    });
    await page.waitForTimeout(600);

    // Verifikasi Modal Kartu Stok Muncul
    await expect(page.locator('#modalKartuStok')).toBeVisible();
    await expect(page.locator('#kartuStokModalBody')).toContainText('SISA SALDO STOK:');

    // Tangkap Screenshot Kartu Stok
    await page.screenshot({
      path: `${artifactDir}/distributor-stok-05-kartu-stok.png`,
    });
    console.log('Screenshot distributor-stok-05-kartu-stok.png tersimpan');

    // Tutup Modal
    await page.locator('#modalKartuStok button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 5: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 5: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 5 (/distributor/stok) berhasil diuji 100% tanpa kendala.');
  });
});
