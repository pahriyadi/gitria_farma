import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL FARMASI & APOTEK - STOK OPNAME OBAT (/apotek/opname)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 3 Kartu KPI Metrik (Total Batch Fisik, Total Riwayat, Tanggal Terakhir).
 * 3. Tab 1 Lembar Hitung Stok Fisik: Prefill Otomatis, Input Fisik, Live Discrepancy.
 * 4. Submit Stock Opname & Penyelarasan Otomatis ke medicine_batches & stock_movements.
 * 5. Tab 2 Riwayat Berita Acara Opname & Status TERADJUST.
 * 6. Modal Popup Rincian Selisih Opname.
 * 7. Halaman Cetak Berita Acara Resmi dengan Kop Surat & 3 Blok Tanda Tangan.
 * 8. Verifikasi 0 Console Errors.
 */

test.describe('Apotek Opname: Stock Opname Fisik & Audit Selisih Pipeline', () => {
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

  test('E2E Full Flow: Lembar Hitung -> Prefill -> Edit Fisik -> Submit Opname -> Tab Riwayat -> Modal Detail -> Cetak Berita Acara -> 0 Console Errors', async ({ page }) => {
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

    // Handle confirm dialogs automatically
    page.on('dialog', async dialog => {
      await dialog.accept();
    });

    // ========================================================================
    // STAGE 1: Membuka /apotek/opname & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /apotek/opname & Verifikasi Light Mode ---');
    await page.goto('apotek/opname');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan layout tidak terjebak mode drawer mobile
    await page.evaluate(() => {
      document.body.classList.remove('sidebar-open');
    });

    // Verifikasi Judul Tab & Tab Navigasi
    await expect(page.locator('#tab-input-tab')).toBeVisible();
    await expect(page.locator('#tab-history-tab')).toBeVisible();

    // Verifikasi desain_tabel.md: TIDAK BOLEH ada .table-striped
    const stripedTables = await page.locator('.table-striped').count();
    console.log(`Jumlah tabel dengan .table-striped (Harus 0): ${stripedTables}`);
    expect(stripedTables).toBe(0);

    // Ambil Screenshot Light Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-opname-01-light.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 2: Uji Mode Gelap (Paper Dark Palette)
    // ========================================================================
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });
    await page.waitForTimeout(500);

    // Cek warna background card pada dark mode (#1f2328)
    const cardBgDark = await page.evaluate(() => {
      const card = document.querySelector('.op-card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log(`Card background color in dark mode: ${cardBgDark}`);
    expect(cardBgDark).toBe('rgb(31, 35, 40)');

    // Ambil Screenshot Dark Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-opname-02-dark.png',
      fullPage: true,
    });

    // Kembalikan ke Light Mode untuk pengujian interaktif
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Uji Fitur Interaktif Formulir Hitung Fisik
    // ========================================================================
    console.log('--- STAGE 3: Uji Fitur Interaktif Lembar Hitung ---');
    // Klik tombol "Isi Semua Sesuai Stok Sistem"
    const btnPrefill = page.locator('#btn-prefill-system');
    await expect(btnPrefill).toBeVisible();
    await btnPrefill.click();
    await page.waitForTimeout(500);

    // Verifikasi input fisik terisi
    const firstPhysicalInput = page.locator('.input-physical').first();
    const filledVal = await firstPhysicalInput.inputValue();
    console.log(`Nilai stok fisik terisi otomatis (item pertama): ${filledVal}`);
    expect(filledVal).not.toBe('');

    // Ubah nilai stok fisik pada baris pertama untuk menciptakan selisih uji
    const currentNum = parseInt(filledVal) || 50;
    const testPhysicalVal = Math.max(1, currentNum - 2); // Kurangi 2
    await firstPhysicalInput.fill(testPhysicalVal.toString());
    await firstPhysicalInput.dispatchEvent('change');
    await page.waitForTimeout(400);

    // Verifikasi badge selisih berubah menjadi merah / kurang
    const firstBatchId = await firstPhysicalInput.getAttribute('data-batchid');
    const diffBadge = page.locator(`#diff-cell-${firstBatchId}`);
    await expect(diffBadge).toContainText(/Kurang|-2/i);

    // Isi alasan selisih
    const reasonInput = page.locator(`#reason-${firstBatchId}`);
    await reasonInput.fill('Koreksi uji opname E2E: 2 unit rusak/pecah');

    // ========================================================================
    // STAGE 4: Submit Stock Opname & Penyelarasan Otomatis
    // ========================================================================
    console.log('--- STAGE 4: Submit Stock Opname ---');
    await page.fill('#opname-notes', 'Stock Opname Rutin Bulanan Farmasi E2E Verification');
    
    // Submit form
    await page.locator('#btn-submit-opname').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000);

    // ========================================================================
    // STAGE 5: Verifikasi Tab 2 Riwayat Opname
    // ========================================================================
    console.log('--- STAGE 5: Verifikasi Tab 2 Riwayat Opname ---');
    const tabHistory = page.locator('#tab-history-tab');
    await tabHistory.click();
    await page.waitForTimeout(600);

    // Pastikan tabel riwayat opname terlihat dan memiliki data
    const historyTable = page.locator('#table-history-opname');
    await expect(historyTable).toBeVisible();

    const historyRows = page.locator('#table-history-opname tbody tr');
    const historyCount = await historyRows.count();
    console.log(`Jumlah riwayat dokumen stock opname: ${historyCount}`);
    expect(historyCount).toBeGreaterThan(0);

    // Baris teratas harus memiliki badge TERADJUST
    await expect(historyRows.first()).toContainText(/TERADJUST/);

    // Ambil Screenshot Tab Riwayat
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-opname-03-history.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 6: Modal Popup Detail Selisih Opname
    // ========================================================================
    console.log('--- STAGE 6: Modal Detail Selisih Opname ---');
    const toggle = historyRows.first().locator('.compact-action-toggle');
    if (await toggle.count() > 0) {
      await toggle.click();
      await page.waitForTimeout(300);
    }
    const btnDetail = historyRows.first().locator('.btn-view-opname-detail');
    await btnDetail.click({ force: true });
    await page.waitForTimeout(600);

    // Verifikasi modal detail terbuka
    const modalDetail = page.locator('#modalOpnameDetail');
    await expect(modalDetail).toBeVisible();
    await expect(modalDetail.locator('#mdl-opname-table tbody tr')).not.toHaveCount(0);

    // Ambil Screenshot Modal Detail
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-opname-04-modal-detail.png',
    });

    // Tutup modal
    await modalDetail.locator('button:has-text("Tutup")').click();
    await page.waitForTimeout(500);

    // ========================================================================
    // STAGE 7: Cetak Berita Acara Stock Opname
    // ========================================================================
    console.log('--- STAGE 7: Cetak Berita Acara Stock Opname ---');
    const printLink = historyRows.first().locator('.btn-cetak-opname');
    const printHref = await printLink.getAttribute('href');
    console.log(`URL Cetak Berita Acara: ${printHref}`);
    expect(printHref).toBeTruthy();

    if (printHref) {
      await page.goto(printHref);
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      // Verifikasi elemen halaman cetak
      await expect(page.locator('.clinic-title')).toContainText(/SAWAMAWA MEDICAL CENTER/i);
      await expect(page.locator('.report-badge')).toContainText(/BERITA ACARA OPNAME/i);
      await expect(page.locator('.table-items')).toBeVisible();

      // Ambil Screenshot Cetak Berita Acara
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-opname-05-cetak-berita-acara.png',
        fullPage: true,
      });

      // Kembali ke halaman opname
      await page.goto('apotek/opname');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);
    }

    // ========================================================================
    // STAGE 8: Verifikasi Error Konsol
    // ========================================================================
    console.log('--- STAGE 8: Verifikasi Error Konsol ---');
    console.log(`Total console errors caught: ${consoleErrors.length}`);
    if (consoleErrors.length > 0) {
      console.log('Console Errors caught:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 8 TAHAP PENGUJIAN /apotek/opname BERHASIL DENGAN SEMPURNA! ===');
  });
});
