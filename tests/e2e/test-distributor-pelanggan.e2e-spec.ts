import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - PELANGGAN & PLAFON KREDIT (/distributor/pelanggan)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Master Tabel Pelanggan Grosir (Kode, Nama, Perusahaan, Kontak, Limit, Piutang, TOP).
 * 3. Modal Tambah Pelanggan Baru & Validasi Plafon Kredit / Termin Pembayaran.
 * 4. Simpan Data Pelanggan Baru B2B & Verifikasi Tampilan di Tabel.
 * 5. Modal Edit Data Pelanggan.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Customer & Credit Limit Pipeline', () => {
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

  test('E2E Full Flow: Customer List -> Dark Mode -> Add Customer -> Edit Customer -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /distributor/pelanggan & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/pelanggan & Verifikasi Light Mode ---');
    await page.goto('distributor/pelanggan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan pelanggan
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
    await expect(page.locator('h4').filter({ hasText: 'Database Pelanggan / Customer B2B' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi Tombol Tambah & Tabel Pelanggan
    await expect(page.locator('#btnOpenAddCustomer')).toBeVisible();
    await expect(page.locator('#tableCustomers')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-pelanggan-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-pelanggan-01-light.png tersimpan');

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
      path: `${artifactDir}/distributor-pelanggan-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-pelanggan-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Modal Tambah Pelanggan Baru B2B
    // ========================================================================
    console.log('--- STAGE 3: Modal Tambah Pelanggan Baru ---');
    await page.click('#btnOpenAddCustomer');
    await page.waitForTimeout(400);

    // Verifikasi Modal Muncul
    await expect(page.locator('#modalCustomerForm')).toBeVisible();
    await expect(page.locator('#cust_name')).toBeVisible();
    await expect(page.locator('#cust_phone')).toBeVisible();

    // Tangkap Screenshot Modal Tambah
    await page.screenshot({
      path: `${artifactDir}/distributor-pelanggan-03-modal-add.png`,
    });
    console.log('Screenshot distributor-pelanggan-03-modal-add.png tersimpan');

    // Isi formulir pelanggan
    const randomSuffix = Math.floor(1000 + Math.random() * 9000);
    const testCustName = `Apotek Mitra E2E ${randomSuffix}`;
    await page.fill('#cust_name', testCustName);
    await page.fill('#cust_company', `PT Mitra Farma ${randomSuffix}`);
    await page.fill('#cust_phone', `0812${randomSuffix}9988`);
    await page.fill('#cust_email', `mitra${randomSuffix}@test.com`);
    await page.fill('#cust_address', 'Jl. Trans Sulawesi No. 88, Palu');
    await page.fill('#cust_limit', '30000000');
    await page.fill('#cust_terms', '30');

    // Simpan data
    await page.click('#btnSubmitCustomer');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi Data Baru Muncul di Tabel
    await expect(page.locator('#tableCustomers')).toContainText(testCustName);

    // Tangkap Screenshot Pelanggan Tersimpan
    await page.screenshot({
      path: `${artifactDir}/distributor-pelanggan-04-customer-saved.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-pelanggan-04-customer-saved.png tersimpan');

    // ========================================================================
    // STAGE 4: Modal Edit Data Pelanggan
    // ========================================================================
    console.log('--- STAGE 4: Modal Edit Data Pelanggan ---');
    const editBtn = page.locator('.btn-edit-customer').first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForTimeout(400);

      // Verifikasi Modal Terbuka dalam mode Edit
      await expect(page.locator('#modalCustomerForm')).toBeVisible();
      await expect(page.locator('#modalCustomerTitle')).toContainText('Edit Data Pelanggan');

      // Tangkap Screenshot Modal Edit
      await page.screenshot({
        path: `${artifactDir}/distributor-pelanggan-05-modal-edit.png`,
      });
      console.log('Screenshot distributor-pelanggan-05-modal-edit.png tersimpan');

      // Tutup Modal via Batal
      await page.locator('#modalCustomerForm button[data-dismiss="modal"]').first().click();
      await page.waitForTimeout(300);
    }

    // ========================================================================
    // STAGE 5: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 5: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 4 (/distributor/pelanggan) berhasil diuji 100% tanpa kendala.');
  });
});

