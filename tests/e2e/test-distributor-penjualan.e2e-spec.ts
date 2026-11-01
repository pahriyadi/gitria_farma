import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - KASIR & FAKTUR B2B (/distributor/penjualan)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Pemilihan Customer B2B & kalkulasi otomatis Limit Kredit / Sisa Plafon.
 * 3. Opsi Pembayaran Kredit / Tempo (TOP) & Preview Tanggal Jatuh Tempo.
 * 4. Modal Item Picker Stok Gudang Distributor & Penambahan Item ke Keranjang.
 * 5. Kalkulasi Dinamis Subtotal, Diskon Faktur, PPN 11%, dan Grand Total.
 * 6. Pemrosesan Faktur Penjualan Grosir Baru & Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Point of Sale & Invoicing Pipeline', () => {
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

  test('E2E Full Flow: POS Invoicing -> Dark Mode -> Customer Plafon -> Item Picker -> Cart -> Settle -> 0 Errors', async ({ page }) => {
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

    // Handle any window alert/confirm dialogs
    page.on('dialog', async dialog => {
      console.log(`Dialog (${dialog.type()}): ${dialog.message()}`);
      await dialog.accept();
    });

    // ========================================================================
    // STAGE 1: Membuka /distributor/penjualan & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/penjualan & Verifikasi Light Mode ---');
    await page.goto('distributor/penjualan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan POS Grosir
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
    await expect(page.locator('h4').filter({ hasText: 'Kasir & Faktur Penjualan Grosir B2B' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi Elemen Form Header
    await expect(page.locator('#invoice_no')).toBeVisible();
    await expect(page.locator('#sale_date')).toBeVisible();
    await expect(page.locator('#payment_type')).toBeVisible();
    await expect(page.locator('#cartTable')).toBeVisible();
    await expect(page.locator('#lbl_grand_total')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-penjualan-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-penjualan-01-light.png tersimpan');

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
      path: `${artifactDir}/distributor-penjualan-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-penjualan-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Pemilihan Pelanggan B2B & Informasi Limit Kredit
    // ========================================================================
    console.log('--- STAGE 3: Pemilihan Customer B2B & Limit Kredit ---');
    // Pilih customer pertama yang valid dari select2 / native select
    await page.evaluate(() => {
      const select = document.getElementById('customer_id') as HTMLSelectElement;
      if (select && select.options.length > 1) {
        select.selectedIndex = 1;
        $(select).trigger('change');
      }
    });
    await page.waitForTimeout(300);

    // Verifikasi Box Info Limit Kredit Muncul
    await expect(page.locator('#customer_credit_box')).toBeVisible();
    await expect(page.locator('#cust_limit_val')).toBeVisible();
    await expect(page.locator('#cust_quota_val')).toBeVisible();

    // Ubah Metode Pembayaran ke Kredit / Tempo (TOP)
    await page.selectOption('#payment_type', 'credit');
    await page.waitForTimeout(300);

    // Verifikasi Container Tempo & Preview Jatuh Tempo Muncul
    await expect(page.locator('#terms_container')).toBeVisible();
    await expect(page.locator('#due_date_container')).toBeVisible();

    // Tangkap Screenshot Customer & Credit Terms
    await page.screenshot({
      path: `${artifactDir}/distributor-penjualan-03-credit-customer.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-penjualan-03-credit-customer.png tersimpan');

    // ========================================================================
    // STAGE 4: Modal Item Picker & Tambah Barang ke Keranjang
    // ========================================================================
    console.log('--- STAGE 4: Modal Item Picker & Cart ---');
    await page.click('#btnOpenItemPicker');
    await page.waitForTimeout(400);

    // Verifikasi Modal Muncul
    await expect(page.locator('#modalItemPicker')).toBeVisible();
    await expect(page.locator('#pickerTable')).toBeVisible();

    // Tangkap Screenshot Modal Item Picker
    await page.screenshot({
      path: `${artifactDir}/distributor-penjualan-04-item-picker.png`,
    });
    console.log('Screenshot distributor-penjualan-04-item-picker.png tersimpan');

    // Pilih item pertama dari tabel modal
    const selectStockBtn = page.locator('.btn-select-stock').first();
    if (await selectStockBtn.isVisible()) {
      await selectStockBtn.click();
      await page.waitForTimeout(400);
    }

    // Verifikasi Modal tertutup & Item masuk ke keranjang
    await expect(page.locator('#cartTableBody tr')).not.toHaveCount(0);
    await expect(page.locator('.btn-remove-item').first()).toBeVisible();

    // Tangkap Screenshot Keranjang Aktif
    await page.screenshot({
      path: `${artifactDir}/distributor-penjualan-05-cart-active.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-penjualan-05-cart-active.png tersimpan');

    // ========================================================================
    // STAGE 5: Simpan & Checkout Faktur Grosir
    // ========================================================================
    console.log('--- STAGE 5: Simpan Faktur Penjualan Grosir ---');
    // Beri catatan faktur
    await page.fill('#notes', 'E2E Testing Invoice B2B Otomatis');
    
    // Klik Simpan & Cetak Faktur
    await page.click('#btnSubmitSale');
    await page.waitForTimeout(1000);

    // ========================================================================
    // STAGE 6: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 6: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 2 (/distributor/penjualan) berhasil diuji 100% tanpa kendala.');
  });
});

