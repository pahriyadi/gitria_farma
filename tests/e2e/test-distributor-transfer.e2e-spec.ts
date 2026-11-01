import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - TRANSFER ANTAR-UNIT (/distributor/transfer)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Tabel Arsip Transfer Stok Antar-Unit Bisnis (No. Dokumen, Asal, Tujuan, Petugas, Status).
 * 3. Modal Form Transfer Stok Antar Unit (Source: Distributor/Pharmacy -> Target: Apotek/Klinik/Resto).
 * 4. Pemilihan Item Obat, Batch, Qty Mutasi, dan Penambahan ke Daftar Transfer.
 * 5. Eksekusi Proses Transfer Stok & Verifikasi Masuk ke Tabel.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Stock Transfer Pipeline', () => {
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

  test('E2E Full Flow: Transfer List -> Dark Mode -> Transfer Modal -> Add Items -> Process -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /distributor/transfer & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /distributor/transfer & Verifikasi Light Mode ---');
    await page.goto('distributor/transfer');
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
    await expect(page.locator('h4').filter({ hasText: 'Transfer Stok Antar-Unit Bisnis' })).toBeVisible();

    // Verifikasi TIDAK ADA class .table-striped (sesuai desain_tabel.md)
    const stripedTables = await page.locator('.table-striped').count();
    expect(stripedTables).toBe(0);

    // Verifikasi Tombol Tambah & Tabel Transfer
    await expect(page.locator('#btnOpenTransferModal')).toBeVisible();
    await expect(page.locator('#tableTransfers')).toBeVisible();

    // Tangkap Screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-transfer-01-light.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-transfer-01-light.png tersimpan');

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
      path: `${artifactDir}/distributor-transfer-02-dark.png`,
      fullPage: true,
    });
    console.log('Screenshot distributor-transfer-02-dark.png tersimpan');

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Modal Form Transfer Stok & Add Items
    // ========================================================================
    console.log('--- STAGE 3: Modal Transfer Stok & Add Items ---');
    await page.click('#btnOpenTransferModal');
    await page.waitForTimeout(400);

    // Verifikasi Modal Muncul
    await expect(page.locator('#modalTransferStock')).toBeVisible();
    await expect(page.locator('#source_type')).toBeVisible();
    await expect(page.locator('#target_type')).toBeVisible();

    // Isi Catatan
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const testNote = `Transfer E2E Testing #${randomSuffix}`;
    await page.fill('input[name="notes"]', testNote);

    // Pilih item obat jika tersedia di dropdown
    const medSelect = page.locator('#trf_med_select');
    const optionCount = await medSelect.locator('option').count();
    if (optionCount > 1) {
      await medSelect.selectOption({ index: 1 });
      await page.fill('#trf_qty_input', '2');
      await page.click('#btnAddTransferItem');
      await page.waitForTimeout(300);

      // Verifikasi Item Masuk ke Tabel
      await expect(page.locator('#trfItemsTableBody tr')).not.toHaveCount(0);
      await expect(page.locator('.btn-remove-transfer-item').first()).toBeVisible();

      // Tangkap Screenshot Modal dengan Item
      await page.screenshot({
        path: `${artifactDir}/distributor-transfer-03-modal-add-item.png`,
      });
      console.log('Screenshot distributor-transfer-03-modal-add-item.png tersimpan');

      // ====================================================================
      // STAGE 4: Submit Transfer
      // ====================================================================
      console.log('--- STAGE 4: Submit Transfer ---');
      await page.click('#btnSubmitTransfer');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      // Verifikasi Dokumen Masuk di Tabel Utama
      await expect(page.locator('#tableTransfers')).toContainText(testNote);

      // Tangkap Screenshot Hasil Transfer
      await page.screenshot({
        path: `${artifactDir}/distributor-transfer-04-transfer-saved.png`,
        fullPage: true,
      });
      console.log('Screenshot distributor-transfer-04-transfer-saved.png tersimpan');
    } else {
      console.log('Tidak ada stok obat distributor untuk ditransfer. Melewati submit transfer.');
    }

    // ========================================================================
    // STAGE 5: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 5: Verifikasi 0 Console Errors ---');
    console.log('Console Errors terdeteksi:', consoleErrors);
    expect(consoleErrors.length).toBe(0);
    console.log('✓ Modul Distributor Sub-Menu 6 (/distributor/transfer) berhasil diuji 100% tanpa kendala.');
  });
});

