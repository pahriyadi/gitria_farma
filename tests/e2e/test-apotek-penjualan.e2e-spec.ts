import { test, expect } from '@playwright/test';

test.describe('Apotek Penjualan: Comprehensive OTC Cashier, Dark/Light Mode, Batch FEFO, & Pending/Checkout Pipeline', () => {
  test.setTimeout(90000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    page.on('dialog', async dialog => {
      console.log('Dialog caught:', dialog.message());
      await dialog.accept();
    });

    // Login as superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: Kasir OTC -> FEFO Batch -> Racikan Modal -> Hold/Resume Pending -> Checkout -> Thermal Receipt -> Riwayat & Detail Modal', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      console.log(`[BROWSER ${msg.type()}]:`, msg.text());
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

    // ------------------------------------------------------------------------
    // STAGE 1: BUKA /apotek/penjualan & VERIFIKASI DESAIN PAPER WHITE (LIGHT MODE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 1: Membuka /apotek/penjualan & Verifikasi Light Mode ---');
    await page.goto('apotek/penjualan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('apotek/penjualan');
    await expect(page.locator('#otc-tab')).toBeVisible();
    await expect(page.locator('#tab-kasir-tab')).toBeVisible();
    await expect(page.locator('#tab-riwayat-tab')).toBeVisible();
    await expect(page.locator('#form-otc-sale')).toBeVisible();
    await expect(page.locator('#table-otc-items')).toBeVisible();
    await expect(page.locator('#lbl-grand-total')).toBeVisible();

    // Pastikan tidak ada class table-striped di halaman
    const stripedTables = await page.locator('table.table-striped').count();
    console.log('Jumlah tabel dengan .table-striped (Harus 0):', stripedTables);
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-penjualan-01-light.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 2: UJI MODE GELAP (PAPER DARK) & MODAL RACIKAN
    // ------------------------------------------------------------------------
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) & Modal Racikan ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(400);

    // Verifikasi warna latar card pada Paper Dark (#1f2328)
    const cardBg = await page.locator('.card').first().evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Card background color in dark mode:', cardBg);
    expect(cardBg).toBe('rgb(31, 35, 40)'); // #1f2328

    // Buka Modal Racikan Builder (id: modal-add-racikan)
    const btnRacik = page.locator('button[data-target="#modal-add-racikan"]');
    await expect(btnRacik).toBeVisible();
    await btnRacik.click();
    await page.waitForSelector('#modal-add-racikan.show', { timeout: 5000 });
    await page.waitForTimeout(400);

    // Verifikasi modal racikan tidak mengandung .table-striped
    const racikStriped = await page.locator('#modal-add-racikan table.table-striped').count();
    expect(racikStriped).toBe(0);

    // Ambil screenshot Modal Racikan Mode Gelap
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-penjualan-02-modal-racik-dark.png',
    });

    // Tutup modal racikan
    await page.locator('#modal-add-racikan .close').click();
    await page.waitForSelector('#modal-add-racikan', { state: 'hidden' });

    // Kembalikan ke Light Mode untuk pengujian alur kasir
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // ------------------------------------------------------------------------
    // STAGE 3: INTERAKSI KERANJANG OBAT, BATCH FEFO, & KALKULASI OTOMATIS
    // ------------------------------------------------------------------------
    console.log('--- STAGE 3: Interaksi Keranjang Kasir & FEFO Batch ---');

    // 1. Pilih Obat Baris 0 (Paracetamol ID: 1) menggunakan jQuery trigger change
    await page.evaluate(() => {
      const $sel = window['$']('#otc-row-0 .select-med');
      $sel.val('1').trigger('change');
    });
    await page.waitForTimeout(400);

    // Verifikasi batch dropdown terisi
    const batchSelect0 = page.locator('#select-batch-0');
    await expect(batchSelect0).toBeVisible();
    const batch0Options = await batchSelect0.locator('option').count();
    console.log('Jumlah opsi batch untuk Obat ID 1:', batch0Options);
    expect(batch0Options).toBeGreaterThan(0);

    // Set qty = 2
    await page.locator('#input-qty-0').fill('2');
    await page.locator('#input-qty-0').dispatchEvent('input');
    await page.locator('#input-qty-0').dispatchEvent('change');
    await page.waitForTimeout(300);

    // 2. Tambah Baris Obat Kedua
    await page.locator('#btn-add-item').click();
    await page.waitForSelector('#otc-row-1', { state: 'visible', timeout: 3000 });

    // Pilih Obat Baris 1 (Cetirizine ID: 3)
    await page.evaluate(() => {
      const $sel = window['$']('#otc-row-1 .select-med');
      $sel.val('3').trigger('change');
    });
    await page.waitForTimeout(400);

    // Set qty = 1
    await page.locator('#input-qty-1').fill('1');
    await page.locator('#input-qty-1').dispatchEvent('input');
    await page.locator('#input-qty-1').dispatchEvent('change');
    await page.waitForTimeout(300);

    // Periksa kalkulasi Total Tagihan (Paracetamol 2 x 1500 = 3000 + Cetirizine 1 x 12000 = 12000 => 15000)
    const grandTotalText = await page.locator('#lbl-grand-total').innerText();
    console.log('Total Tagihan Kasir:', grandTotalText);
    expect(grandTotalText).toContain('15.000');

    // 3. Uji Tombol Uang Pas
    await page.locator('#btn-otc-exact').click();
    await page.waitForTimeout(300);
    const paidVal = await page.locator('#input-paid-amount').inputValue();
    expect(parseInt(paidVal)).toBe(15000);
    const changeText = await page.locator('#lbl-change-amount').innerText();
    expect(changeText).toContain('0');

    // 4. Uji Penguncian Metode Non-Tunai (QRIS)
    await page.locator('#select-payment-method').selectOption('qris');
    await page.locator('#select-payment-method').dispatchEvent('change');
    await page.waitForTimeout(300);

    const isReadonly = await page.locator('#input-paid-amount').getAttribute('readonly');
    expect(isReadonly).not.toBeNull();

    // Kembalikan ke Tunai
    await page.locator('#select-payment-method').selectOption('tunai');
    await page.locator('#select-payment-method').dispatchEvent('change');
    await page.waitForTimeout(300);

    // ------------------------------------------------------------------------
    // STAGE 4: UJI TAHAN & RESUME RESEP PENDING (HOLD CART)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 4: Uji Tahan (Pending) & Resume Resep ---');

    await page.locator('#input-customer-name').fill('Pasien Pending Test E2E');
    await page.locator('#input-notes').fill('Menunggu konfirmasi resep e2e');
    await page.waitForTimeout(200);

    // Klik tombol Tahan Transaksi
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
      page.locator('#btn-pending-save').click(),
    ]);
    await page.waitForTimeout(1000);

    // Pastikan halaman memuat ulang atau buka /apotek/penjualan jika belum
    if (!page.url().includes('apotek/penjualan')) {
      await page.goto('apotek/penjualan');
      await page.waitForLoadState('domcontentloaded');
    }

    // Buka Modal Resep Tertunda
    const btnPendingList = page.locator('#btn-open-pending-list');
    await expect(btnPendingList).toBeVisible();
    await btnPendingList.click();
    await page.waitForSelector('#modal-pending-list.show', { timeout: 5000 });
    await page.waitForTimeout(800);

    // Verifikasi item pending muncul di daftar
    await expect(page.locator('#pending-list-body')).toContainText('Pasien Pending Test E2E');

    // Ambil screenshot Modal Resep Pending
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-penjualan-03-pending-list.png',
    });

    // Lanjutkan resep pending (Klik tombol Lanjutkan pada baris item tersebut)
    const btnResume = page.locator('.btn-resume-pending').first();
    await btnResume.click();
    await page.waitForTimeout(1000);

    // Verifikasi keranjang kasir terisi kembali
    await expect(page.locator('#input-customer-name')).toHaveValue('Pasien Pending Test E2E');
    const restoredRows = await page.locator('#otc-items-body .otc-item-row').count();
    console.log('Jumlah baris obat yang dipulihkan dari pending:', restoredRows);
    expect(restoredRows).toBeGreaterThanOrEqual(1);

    // ------------------------------------------------------------------------
    // STAGE 5: CHECKOUT TRANSAKSI PENJUALAN & CETAK STRUK THERMAL
    // ------------------------------------------------------------------------
    console.log('--- STAGE 5: Checkout Transaksi Kasir & Cetak Struk ---');

    // Update nama pelanggan untuk checkout final
    await page.locator('#input-customer-name').fill('Pasien OTC Checkout E2E');

    // Pastikan uang bayar mencukupi (Rp 50.000)
    await page.locator('#input-paid-amount').fill('50000');
    await page.locator('#input-paid-amount').dispatchEvent('input');
    await page.locator('#input-paid-amount').dispatchEvent('change');
    await page.waitForTimeout(300);

    // Submit transaksi kasir
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(() => {}),
      page.locator('#btn-submit-otc').click(),
    ]);
    await page.waitForTimeout(1500);

    // Verifikasi banner sukses transaksi muncul
    const successBanner = page.locator('#sale-success-banner');
    await expect(successBanner).toBeVisible({ timeout: 8000 });
    await expect(successBanner).toContainText('Transaksi Penjualan Obat Berhasil!');

    // Ambil link cetak nota dari banner
    const printLink = await successBanner.locator('a[href*="cetak-nota"]').getAttribute('href');
    console.log('Link Cetak Nota Penjualan:', printLink);
    expect(printLink).toBeTruthy();

    // Buka halaman Cetak Nota Thermal
    if (printLink) {
      await page.goto(printLink);
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      // Verifikasi komponen struk thermal
      await expect(page.locator('body')).toContainText('SAWAMAWA PHARMACY');
      await expect(page.locator('body')).toContainText('Pasien OTC Checkout E2E');
      await expect(page.locator('.items-table')).toBeVisible();
      await expect(page.locator('.total-section')).toBeVisible();

      // Ambil screenshot Struk Thermal
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-penjualan-04-cetak-nota.png',
        fullPage: true,
      });
    }

    // ------------------------------------------------------------------------
    // STAGE 6: TAB RIWAYAT PENJUALAN & MODAL DETAIL RINCIAN OBAT
    // ------------------------------------------------------------------------
    console.log('--- STAGE 6: Verifikasi Tab Riwayat & Modal Detail ---');

    // Kembali ke kasir apotek
    await page.goto('apotek/penjualan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Buka tab Riwayat Penjualan
    await page.locator('#tab-riwayat-tab').click();
    await page.waitForTimeout(600);

    // Verifikasi tabel riwayat
    const riwayatTable = page.locator('#table-riwayat-penjualan');
    await expect(riwayatTable).toBeVisible();
    await expect(riwayatTable).toContainText('Pasien OTC Checkout E2E');

    // Klik tombol detail pada transaksi teratas
    await page.evaluate(() => {
      window['$']('.btn-view-sale-detail').first().click();
    });
    await page.waitForSelector('#modal-detail-sale.show', { timeout: 6000 });
    await page.waitForTimeout(600);

    // Verifikasi isi modal detail
    await expect(page.locator('#modal-detail-sale')).toContainText('Rincian Transaksi');
    await expect(page.locator('#modal-detail-sale')).toContainText('Pasien OTC Checkout E2E');
    await expect(page.locator('#modal-detail-sale')).toContainText('Jurnal');

    // Pastikan tidak ada class table-striped di dalam modal
    const detailStriped = await page.locator('#modal-detail-sale table.table-striped').count();
    expect(detailStriped).toBe(0);

    // Uji modal detail dalam mode gelap
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
    });
    await page.waitForTimeout(400);

    // Ambil screenshot Modal Detail Transaksi Mode Gelap
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-penjualan-05-modal-detail-dark.png',
    });

    // Tutup modal detail
    await page.locator('#modal-detail-sale .close').click();
    await page.waitForSelector('#modal-detail-sale', { state: 'hidden' });

    // ------------------------------------------------------------------------
    // STAGE 7: VERIFIKASI KONSOL & KEBERSIHAN ERROR
    // ------------------------------------------------------------------------
    console.log('--- STAGE 7: Verifikasi Error Konsol ---');
    console.log('Total console errors caught:', consoleErrors.length);
    if (consoleErrors.length > 0) {
      console.warn('Console errors:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 7 TAHAP PENGUJIAN /apotek/penjualan BERHASIL DENGAN SEMPURNA! ===');
  });
});
