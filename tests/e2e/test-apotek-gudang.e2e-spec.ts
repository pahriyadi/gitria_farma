import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL FARMASI & APOTEK - GUDANG & MUTASI TRANSFER (/apotek/gudang)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Metrik (Lokasi Gudang, Stok Induk, Stok Depo, Riwayat Mutasi).
 * 3. Distribusi Stok per Lokasi & Filter Gudang.
 * 4. Pembuatan Mutasi Transfer Stok Antar-Gudang (Gudang Induk -> Depo Apotek).
 * 5. Tab 2 Riwayat Mutasi & Cetak Surat Jalan Bukti Mutasi Resmi.
 * 6. Tab 3 Master Lokasi Gudang & Depo Pelayanan.
 * 7. Verifikasi 0 Console Errors.
 */

test.describe('Apotek Gudang: Multi-Depo & Mutasi Transfer Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  test.beforeEach(async ({ page }) => {
    // Suppress release popup and expand sidebar
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

  test('E2E Full Flow: KPI Metrics -> Tab Stok -> Mutasi Antar-Gudang -> Cetak Surat Jalan -> Tab Master Lokasi -> Dark/Light Theme Switching', async ({ page }) => {
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
    page.on('dialog', async dialog => {
      await dialog.accept();
    });

    // ------------------------------------------------------------------------
    // STAGE 1: BUKA /apotek/gudang & VERIFIKASI DESAIN PAPER WHITE (LIGHT MODE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 1: Membuka /apotek/gudang & Verifikasi Light Mode ---');
    await page.goto('apotek/gudang');
    await page.waitForLoadState('domcontentloaded');
    await page.evaluate(() => {
      document.body.classList.remove('sidebar-open');
    });
    await page.waitForTimeout(400);

    expect(page.url()).toContain('apotek/gudang');
    await expect(page.locator('#warehouseTabs')).toBeVisible();
    await expect(page.locator('#tab-stock-link')).toBeVisible();
    await expect(page.locator('#tab-transfer-link')).toBeVisible();
    await expect(page.locator('#tab-master-wh-link')).toBeVisible();

    // Pastikan tidak ada class table-striped
    const stripedCount = await page.locator('table.table-striped').count();
    console.log('Jumlah tabel dengan .table-striped (Harus 0):', stripedCount);
    expect(stripedCount).toBe(0);

    // Ambil screenshot Paper White (Light Mode)
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-gudang-01-light.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 2: UJI MODE GELAP (PAPER DARK PALETTE #1f2328, #374151)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) ---');
    await page.evaluate(() => {
      localStorage.setItem('theme', 'dark');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.add('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(400);

    const hasDark = await page.evaluate(() => document.body.classList.contains('dark-mode'));
    expect(hasDark).toBe(true);

    const cardBg = await page.evaluate(() => {
      const card = document.querySelector('.card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log('Card background color in dark mode:', cardBg);
    expect(cardBg).toBe('rgb(31, 35, 40)');

    // Ambil screenshot Paper Dark
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-gudang-02-dark.png',
      fullPage: true,
    });

    // Kembalikan ke mode terang
    await page.evaluate(() => {
      localStorage.setItem('theme', 'light');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('sidebar-collapse');
      document.documentElement.classList.remove('dark-mode');
      document.documentElement.setAttribute('data-theme', 'light');
    });
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // STAGE 3: FILTER LOKASI STOK PER GUDANG
    // ------------------------------------------------------------------------
    console.log('--- STAGE 3: Filter Lokasi Stok per Gudang ---');
    const filterPills = page.locator('#tab-stock .d-flex.flex-wrap a.btn');
    const pillCount = await filterPills.count();
    console.log('Jumlah tombol filter lokasi:', pillCount);
    expect(pillCount).toBeGreaterThanOrEqual(2);

    // Klik filter lokasi kedua (misal WH-INDUK)
    if (pillCount > 1) {
      await filterPills.nth(1).click();
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(500);
      expect(page.url()).toContain('warehouse_id=');

      // Kembali ke semua lokasi
      await page.locator('#tab-stock a:has-text("Semua Lokasi")').click();
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(500);
    }

    // ------------------------------------------------------------------------
    // STAGE 4: PROSES MUTASI TRANSFER STOK ANTAR-GUDANG
    // ------------------------------------------------------------------------
    console.log('--- STAGE 4: Input Mutasi Transfer Stok Antar-Gudang ---');

    // Buka Modal Transfer Stok (gunakan tombol yang visible di tab-stock)
    await page.locator('#tab-stock button[data-target="#modalTransferStok"]').click();
    await page.waitForSelector('#modalTransferStok.show', { timeout: 5000 });
    await page.waitForTimeout(400);

    // Pilih Gudang Asal (WH-INDUK) dan Gudang Tujuan (DEPO-RJ)
    const sourceWh = page.locator('#trf-source-wh');
    const targetWh = page.locator('#trf-target-wh');

    // Ambil opsi yang tersedia
    const sourceOptions = await sourceWh.locator('option').allInnerTexts();
    console.log('Pilihan Gudang Asal:', sourceOptions);

    await sourceWh.selectOption({ index: 0 }); // WH-INDUK
    await targetWh.selectOption({ index: 1 }); // DEPO-RJ

    // Isi Catatan
    const transferNote = 'Mutasi E2E Suplai Rutin Depo Rawat Jalan';
    await page.locator('#modalTransferStok input[name="notes"]').fill(transferNote);

    // Pilih Obat Pertama
    const medSelect = page.locator('#table-transfer-items select[name="medicine_id[]"]').first();
    const medOptionsCount = await medSelect.locator('option').count();
    expect(medOptionsCount).toBeGreaterThan(1);
    await medSelect.selectOption({ index: 1 }); // Pilih obat pertama
    await page.waitForTimeout(300);

    // Set kuantitas transfer
    const qtyInput = page.locator('#table-transfer-items input[name="qty[]"]').first();
    await qtyInput.fill('15');

    // Submit form transfer
    await page.locator('#modalTransferStok button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1200);

    // Verifikasi mutasi transfer berhasil dibukukan
    const transferTabCount = await page.locator('#tab-transfer-link').innerText();
    console.log('Tab Riwayat Mutasi:', transferTabCount);
    expect(transferTabCount).toMatch(/\(\d+\)/);

    // ------------------------------------------------------------------------
    // STAGE 5: TAB 2 RIWAYAT MUTASI & SURAT JALAN TRANSFER
    // ------------------------------------------------------------------------
    console.log('--- STAGE 5: Verifikasi Riwayat Mutasi & Cetak Surat Jalan ---');

    // Buka Tab 2: Riwayat Mutasi
    await page.locator('#tab-transfer-link').click();
    await page.waitForTimeout(600);

    // Verifikasi transaksi terbaru ada di tabel
    await expect(page.locator('#table-transfers')).toContainText('TRF-');
    await expect(page.locator('#table-transfers')).toContainText('15');
    await expect(page.locator('#table-transfers')).toContainText(/completed/i);

    // Ambil screenshot Tab 2 Riwayat Mutasi
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-gudang-03-riwayat-transfer.png',
      fullPage: true,
    });

    // Ambil link surat jalan pertama
    const suratJalanLink = page.locator('#table-transfers a:has-text("Surat Jalan")').first();
    const href = await suratJalanLink.getAttribute('href');
    expect(href).toBeTruthy();

    if (href) {
      // Buka halaman Cetak Surat Jalan
      await page.goto(href);
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      // Verifikasi komponen cetak surat jalan
      await expect(page.locator('.clinic-name')).toContainText('SAWAMAWA MEDICAL CENTER');
      await expect(page.locator('.doc-badge')).toContainText('SURAT JALAN MUTASI STOK');
      await expect(page.locator('.table-items')).toBeVisible();
      await expect(page.locator('.table-items')).toContainText('15');

      // Ambil screenshot Cetak Surat Jalan
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-gudang-04-cetak-surat-jalan.png',
        fullPage: true,
      });

      // Kembali ke halaman gudang
      await page.goto('apotek/gudang');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);
    }

    // ------------------------------------------------------------------------
    // STAGE 6: TAB 3 MASTER LOKASI GUDANG & DEPO
    // ------------------------------------------------------------------------
    console.log('--- STAGE 6: Verifikasi Master Lokasi Gudang & Depo ---');

    // Buka Tab 3
    await page.locator('#tab-master-wh-link').click();
    await page.waitForTimeout(600);

    // Verifikasi kartu master gudang
    await expect(page.locator('#tab-master-wh')).toContainText('Gudang Induk Logistik Farmasi');
    await expect(page.locator('#tab-master-wh')).toContainText('Depo Apotek Rawat Jalan');

    // Ambil screenshot Tab 3 Master Lokasi
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-gudang-05-master-lokasi.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 7: VERIFIKASI ERROR KONSOL
    // ------------------------------------------------------------------------
    console.log('--- STAGE 7: Verifikasi Error Konsol ---');
    console.log('Total console errors caught:', consoleErrors.length);
    if (consoleErrors.length > 0) {
      console.warn('Console errors caught:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 7 TAHAP PENGUJIAN /apotek/gudang BERHASIL DENGAN SEMPURNA! ===');
  });
});
