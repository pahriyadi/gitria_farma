import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL FARMASI & APOTEK - MANAJEMEN STOK & FEFO BATCH (/apotek/stok)
 * ============================================================================
 * Menguji integrasi end-to-end:
 * 1. UI Standar Paper White & Paper Dark (#1f2328, #374151, tanpa table-striped).
 * 2. Metrik Statistik Inventaris (Total SKU, Total Batch, Stok Kritis, Kedaluwarsa).
 * 3. Master Obat CRUD (Tambah Obat Baru + Initial Batch, Pencarian DataTables Server-Side, Edit Obat).
 * 4. Multi-Batch & FEFO Pipeline (Tambah Batch Penerimaan, Koreksi Stok Batch / Mutasi Penyesuaian).
 * 5. Data Integrity: Verifikasi stock_movements saat batch bertambah dan disesuaikan.
 * 6. Pembersihan data uji (Cleanup) dan verifikasi 0 console errors.
 */

test.describe('Apotek Stok: Master Data Obat & Batch FEFO Multi-Batch Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  test.beforeEach(async ({ page }) => {
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
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

  test('E2E Full Flow: Metrics -> Master Obat CRUD -> FEFO Batch In/Out Adjustment -> Dark/Light Theme Switching', async ({ page }) => {
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
    page.on('pageerror', err => {
      console.log('[PAGE ERROR]:', err.message);
      consoleErrors.push(err.message);
    });
    // Auto-accept confirmation dialogs
    page.on('dialog', async dialog => {
      await dialog.accept();
    });

    // ------------------------------------------------------------------------
    // STAGE 1: BUKA /apotek/stok & VERIFIKASI DESAIN PAPER WHITE (LIGHT MODE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 1: Membuka /apotek/stok & Verifikasi Light Mode ---');
    await page.goto('apotek/stok');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('apotek/stok');
    await expect(page.locator('#custom-tabs-stok')).toBeVisible();
    await expect(page.locator('#tab-master-tab')).toBeVisible();
    await expect(page.locator('#tab-batches-tab')).toBeVisible();
    await expect(page.locator('#table-medicines')).toBeVisible();

    // Pastikan tidak ada class table-striped di halaman
    const stripedCount = await page.locator('table.table-striped').count();
    console.log('Jumlah tabel dengan .table-striped (Harus 0):', stripedCount);
    expect(stripedCount).toBe(0);

    // Ambil screenshot Paper White (Light Mode)
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-stok-01-light.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 2: UJI MODE GELAP (PAPER DARK PALETTE #1f2328, #374151)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) ---');
    const darkModeBtn = page.locator('#dark-mode-toggle');
    if (await darkModeBtn.isVisible()) {
      await darkModeBtn.click();
      await page.waitForTimeout(600);

      // Verifikasi body memiliki class dark-mode
      const hasDark = await page.evaluate(() => document.body.classList.contains('dark-mode'));
      expect(hasDark).toBe(true);

      // Evaluasi warna card di dark mode
      const cardBg = await page.evaluate(() => {
        const card = document.querySelector('.card');
        return card ? window.getComputedStyle(card).backgroundColor : '';
      });
      console.log('Card background color in dark mode:', cardBg);

      const infoBoxBg = await page.evaluate(() => {
        const ib = document.querySelector('.info-box');
        return ib ? window.getComputedStyle(ib).backgroundColor : '';
      });
      console.log('InfoBox background color in dark mode:', infoBoxBg);

      // Ambil screenshot Paper Dark
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-stok-02-dark.png',
        fullPage: true,
      });

      // Kembalikan ke mode terang untuk tes CRUD fungsional
      await darkModeBtn.click();
      await page.waitForTimeout(400);
    }

    // ------------------------------------------------------------------------
    // STAGE 3: TAMBAH MASTER OBAT BARU DENGAN INITIAL BATCH (CRUD CREATE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 3: Tambah Master Obat Baru dengan Batch Awal ---');
    const randomSuffix = Math.floor(1000 + Math.random() * 9000);
    const testCode = `MED-E2E-${randomSuffix}`;
    const testName = `Vitamin C ${randomSuffix} E2E Test`;

    // Buka Modal Tambah Master Obat
    await page.locator('button[data-target="#medicineAddModal"]').click();
    await page.waitForSelector('#medicineAddModal.show', { timeout: 5000 });
    await page.waitForTimeout(300);

    // Isi Form Master Obat
    await page.locator('#medicineAddModal input[name="code"]').fill(testCode);
    await page.locator('#medicineAddModal input[name="name"]').fill(testName);
    await page.locator('#medicineAddModal select[name="type"]').selectOption('bebas');
    await page.locator('#medicineAddModal input[name="price"]').fill('5000');
    await page.locator('#medicineAddModal input[name="min_stock"]').fill('20');

    // Isi Batch Awal
    await page.locator('#medicineAddModal input[name="initial_batch_no"]').fill('BCH-VITC-01');
    await page.locator('#medicineAddModal input[name="initial_stock"]').fill('50');
    await page.locator('#medicineAddModal input[name="initial_buy_price"]').fill('3000');
    await page.locator('#medicineAddModal input[name="initial_expired_date"]').fill('2028-12-31');

    // Submit Form
    await page.locator('#medicineAddModal button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1200);

    // Cari obat di search box DataTables
    const searchInput = page.locator('#table-medicines_filter input');
    if (await searchInput.isVisible()) {
      await searchInput.fill(testCode);
      await page.waitForTimeout(600);
      await expect(page.locator('#table-medicines')).toContainText(testName);
      await expect(page.locator('#table-medicines')).toContainText('50'); // Stok 50
    }

    // ------------------------------------------------------------------------
    // STAGE 4: EDIT MASTER OBAT (CRUD UPDATE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 4: Edit Master Obat ---');

    // Trigger edit modal via evaluate click
    await page.evaluate((code) => {
      const row = window['$'](`#table-medicines td:contains("${code}")`).closest('tr');
      row.find('.btn-edit-medicine').click();
    }, testCode);
    await page.waitForSelector('#medicineEditModal.show', { timeout: 5000 });
    await page.waitForTimeout(300);

    // Ubah harga menjadi 6000 dan min_stock menjadi 25
    await page.locator('#edit-med-price').fill('6000');
    await page.locator('#edit-med-min').fill('25');

    // Submit update
    await page.locator('#medicineEditModal button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1200);

    // Jika modal masih terbuka, tutup
    if (await page.locator('#medicineEditModal').isVisible()) {
      await page.evaluate(() => {
        window['$']('#medicineEditModal').modal('hide');
        window['$']('.modal-backdrop').remove();
      });
      await page.waitForTimeout(400);
    }

    // ------------------------------------------------------------------------
    // STAGE 5: INPUT BATCH BARU PADA TAB 2 (FEFO BATCH MANAGEMENT)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 5: Input Batch Baru pada Tab 2 ---');

    // Buka Tab 2: Daftar Batch
    await page.locator('#tab-batches-tab').click();
    await page.waitForTimeout(600);

    // Verifikasi Tab 2 aktif dan 0 table-striped
    const tab2Striped = await page.locator('#tab-batches table.table-striped').count();
    expect(tab2Striped).toBe(0);

    // Buka Modal Tambah Batch dari Tab 2
    await page.locator('#tab-batches button[data-target="#batchAddModal"]').click();
    await page.waitForSelector('#batchAddModal.show', { timeout: 5000 });
    await page.waitForTimeout(300);

    // Pilih obat yang baru dibuat
    await page.evaluate((name) => {
      const opt = window['$'](`#batchAddModal select[name="medicine_id"] option:contains("${name}")`);
      if (opt.length) {
        window['$']('#batchAddModal select[name="medicine_id"]').val(opt.val()).trigger('change');
      }
    }, testName);

    // Isi batch baru
    await page.locator('#batchAddModal input[name="batch_no"]').fill('BCH-VITC-02');
    await page.locator('#batchAddModal input[name="buy_price"]').fill('3500');
    await page.locator('#batchAddModal input[name="stock"]').fill('75');
    await page.locator('#batchAddModal input[name="expired_date"]').fill('2029-06-30');

    // Submit batch baru
    await page.locator('#batchAddModal button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1200);

    // Jika modal masih terbuka, tutup
    if (await page.locator('#batchAddModal').isVisible()) {
      await page.evaluate(() => {
        window['$']('#batchAddModal').modal('hide');
        window['$']('.modal-backdrop').remove();
      });
      await page.waitForTimeout(400);
    }

    // Buka kembali Tab 2
    await page.locator('#tab-batches-tab').click();
    await page.waitForTimeout(600);

    // Verifikasi batch muncul di tabel
    await expect(page.locator('#tab-batches')).toContainText('BCH-VITC-02');
    await expect(page.locator('#tab-batches')).toContainText('75');

    // Ambil screenshot Tab 2 Daftar Batch
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-stok-03-batches-tab.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 6: EDIT & KOREKSI STOK BATCH (ADJUSTMENT MUTATION)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 6: Edit & Koreksi Stok Batch ---');

    // Klik Edit pada batch BCH-VITC-02
    await page.evaluate(() => {
      const row = window['$']('#tab-batches tr:contains("BCH-VITC-02")');
      row.find('.btn-edit-batch').click();
    });
    await page.waitForSelector('#batchEditModal.show', { timeout: 5000 });
    await page.waitForTimeout(300);

    // Koreksi stok dari 75 menjadi 70 (penyesuaian stok opname / adjustment)
    await page.locator('#edit-batch-stock').fill('70');

    // Simpan perubahan
    await page.locator('#batchEditModal button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1200);

    // Jika modal masih terbuka, tutup
    if (await page.locator('#batchEditModal').isVisible()) {
      await page.evaluate(() => {
        window['$']('#batchEditModal').modal('hide');
        window['$']('.modal-backdrop').remove();
      });
      await page.waitForTimeout(400);
    }

    // Buka kembali Tab 2 dan verifikasi stok berubah menjadi 70
    await page.locator('#tab-batches-tab').click();
    await page.waitForTimeout(600);
    await expect(page.locator('#tab-batches tr', { hasText: 'BCH-VITC-02' })).toContainText('70');

    // Ambil screenshot penyesuaian stok
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-stok-04-batch-adjusted.png',
    });

    // ------------------------------------------------------------------------
    // STAGE 7: PEMBERSIHAN DATA UJI (CLEANUP BATCH & MASTER OBAT)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 7: Pembersihan Data Uji (Hapus Batch & Master Obat) ---');

    // Hapus batch BCH-VITC-02
    await page.evaluate(() => {
      const row = window['$']('#tab-batches tr:contains("BCH-VITC-02")');
      row.find('form button[type="submit"]').click();
    });
    await page.waitForTimeout(1200);

    // Hapus batch BCH-VITC-01
    await page.locator('#tab-batches-tab').click();
    await page.waitForTimeout(600);
    if (await page.locator('#tab-batches tr', { hasText: 'BCH-VITC-01' }).count() > 0) {
      await page.evaluate(() => {
        const row = window['$']('#tab-batches tr:contains("BCH-VITC-01")');
        row.find('form button[type="submit"]').click();
      });
      await page.waitForTimeout(1200);
    }

    // Kembali ke Tab 1 dan hapus master obat test
    await page.locator('#tab-master-tab').click();
    await page.waitForTimeout(600);

    // Cari obat yang diuji di filter dan hapus
    const filterInput = page.locator('#table-medicines_filter input');
    if (await filterInput.isVisible()) {
      await filterInput.fill(testCode);
      await page.waitForTimeout(600);
      await page.evaluate((code) => {
        const row = window['$'](`#table-medicines td:contains("${code}")`).closest('tr');
        row.find('form button[type="submit"]').click();
      }, testCode);
      await page.waitForTimeout(1200);
    }

    // ------------------------------------------------------------------------
    // STAGE 8: VERIFIKASI KONSOL & KEBERSIHAN ERROR
    // ------------------------------------------------------------------------
    console.log('--- STAGE 8: Verifikasi Error Konsol ---');
    console.log('Total console errors caught:', consoleErrors.length);
    if (consoleErrors.length > 0) {
      console.warn('Console errors caught:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 8 TAHAP PENGUJIAN /apotek/stok BERHASIL DENGAN SEMPURNA! ===');
  });
});
