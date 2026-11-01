import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL FARMASI & APOTEK - LAPORAN & ANALITIK (/apotek/laporan)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Metrik Eksekutif (Obat Diserahkan, Omset Resep, Risiko Expired, Mutasi).
 * 3. Tab 1: Pemakaian & Penjualan Resep Pasien & Total Penjualan.
 * 4. Tab 2: Penjualan Kasir Apotek OTC & Resep Langsung + 6 KPI Omset (Tusla, Embalase, Fee Dokter).
 * 5. Tab 3: Log Mutasi & Buku Kartu Stok Obat Farmasi.
 * 6. Tab 4: Monitoring Kadaluarsa & Analisis FEFO (Days Left, Rekomendasi).
 * 7. Tab 5: Analisis Ranking 10 Besar Obat Fast-Moving (Top Prescribed).
 * 8. Form Filter Periode Tanggal & Jenis Transaksi.
 * 9. Halaman Cetak Laporan Resmi Ber-Kop Surat.
 * 10. Verifikasi 0 Console Errors.
 */

test.describe('Apotek Laporan: Pharmacy Analytics & Reporting Pipeline', () => {
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

  test('E2E Full Flow: KPI Metrik -> 5 Tab Laporan -> Dark Mode -> Filter Periode -> Cetak Laporan -> 0 Console Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /apotek/laporan & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /apotek/laporan & Verifikasi Light Mode ---');
    await page.goto('apotek/laporan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan layout tidak terjebak mode drawer mobile
    await page.evaluate(() => {
      document.body.classList.remove('sidebar-open');
    });

    // Verifikasi 4 Kartu KPI Eksekutif di Header
    const infoBoxes = page.locator('.lp-info-box');
    const infoCount = await infoBoxes.count();
    console.log(`Jumlah KPI Card Eksekutif: ${infoCount}`);
    expect(infoCount).toBe(4);

    // Verifikasi 5 Tab Navigasi Laporan
    await expect(page.locator('#tab-dispensed-tab')).toBeVisible();
    await expect(page.locator('#tab-sales-tab')).toBeVisible();
    await expect(page.locator('#tab-movements-tab')).toBeVisible();
    await expect(page.locator('#tab-expired-tab')).toBeVisible();
    await expect(page.locator('#tab-top-tab')).toBeVisible();

    // Verifikasi desain_tabel.md: TIDAK BOLEH ada .table-striped
    const stripedTables = await page.locator('.table-striped').count();
    console.log(`Jumlah tabel dengan .table-striped (Harus 0): ${stripedTables}`);
    expect(stripedTables).toBe(0);

    // Ambil Screenshot Light Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-01-light.png',
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
      const card = document.querySelector('.lp-card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log(`Card background color in dark mode: ${cardBgDark}`);
    expect(cardBgDark).toBe('rgb(31, 35, 40)');

    // Ambil Screenshot Dark Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-02-dark.png',
      fullPage: true,
    });

    // Kembalikan ke Light Mode untuk pengujian interaktif
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Verifikasi Tab 1 Pemakaian & Penjualan Resep
    // ========================================================================
    console.log('--- STAGE 3: Verifikasi Tab 1 Pemakaian & Penjualan Resep ---');
    const tableDispensed = page.locator('#table-dispensed');
    await expect(tableDispensed).toBeVisible();

    // ========================================================================
    // STAGE 4: Verifikasi Tab 2 Penjualan Kasir Apotek OTC & 6 KPI Omset
    // ========================================================================
    console.log('--- STAGE 4: Verifikasi Tab 2 Penjualan Kasir Apotek OTC ---');
    await page.locator('#tab-sales-tab').click();
    await page.waitForTimeout(600);

    // Verifikasi 6 KPI Omset Box di Tab Penjualan
    const salesKpiBoxes = page.locator('#tab-sales .lp-box');
    const salesKpiCount = await salesKpiBoxes.count();
    console.log(`Jumlah KPI Box Omset Penjualan: ${salesKpiCount}`);
    expect(salesKpiCount).toBe(6);

    // Verifikasi Tabel Penjualan
    const tableSales = page.locator('#table-sales');
    await expect(tableSales).toBeVisible();

    // Ambil Screenshot Tab Penjualan
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-03-sales-tab.png',
      fullPage: true,
    });

    // Verifikasi tombol Ekspor CSV tersedia
    const btnExportCsv = page.locator('#btn-export-sales-csv');
    await expect(btnExportCsv).toBeVisible();

    // ========================================================================
    // STAGE 5: Verifikasi Tab 3 Kartu Stok & Log Mutasi
    // ========================================================================
    console.log('--- STAGE 5: Verifikasi Tab 3 Kartu Stok & Log Mutasi ---');
    await page.locator('#tab-movements-tab').click();
    await page.waitForTimeout(600);

    const tableMovements = page.locator('#table-movements');
    await expect(tableMovements).toBeVisible();

    // ========================================================================
    // STAGE 6: Verifikasi Tab 4 Monitoring Kadaluarsa & FEFO
    // ========================================================================
    console.log('--- STAGE 6: Verifikasi Tab 4 Monitoring Kadaluarsa & FEFO ---');
    await page.locator('#tab-expired-tab').click();
    await page.waitForTimeout(600);

    const tableExpired = page.locator('#table-expired');
    await expect(tableExpired).toBeVisible();

    // Verifikasi baris batch expired memiliki status risiko
    const expiredRows = page.locator('#table-expired tbody tr');
    expect(await expiredRows.count()).toBeGreaterThan(0);

    // Ambil Screenshot Tab Expired & FEFO
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-04-expired-fefo.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 7: Verifikasi Tab 5 Top 10 Fast-Moving Medicines
    // ========================================================================
    console.log('--- STAGE 7: Verifikasi Tab 5 Top 10 Fast-Moving ---');
    await page.locator('#tab-top-tab').click();
    await page.waitForTimeout(600);

    const tableTop = page.locator('#table-top-medicines');
    await expect(tableTop).toBeVisible();

    // Ambil Screenshot Tab Top Fast-Moving
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-05-top-medicines.png',
      fullPage: true,
    });

    // ========================================================================
    // STAGE 8: Pengujian Form Filter Periode Tanggal
    // ========================================================================
    console.log('--- STAGE 8: Pengujian Filter Periode Tanggal ---');
    await page.fill('#filter-start-date', '2026-01-01');
    await page.locator('#form-filter-laporan button[type="submit"]').click();
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi bahwa halaman tetap menampilkan tabel data dengan benar
    await expect(page.locator('#tab-dispensed-tab')).toBeVisible();

    // ========================================================================
    // STAGE 9: Halaman Cetak Laporan Resmi
    // ========================================================================
    console.log('--- STAGE 9: Halaman Cetak Laporan Resmi ---');
    const printLink = page.locator('.btn-print-report').first();
    const printHref = await printLink.getAttribute('href');
    console.log(`URL Cetak Laporan: ${printHref}`);
    expect(printHref).toBeTruthy();

    if (printHref) {
      await page.goto(printHref);
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);

      // Verifikasi komponen cetak laporan
      await expect(page.locator('.clinic-title')).toContainText(/SAWAMAWA MEDICAL CENTER/i);
      await expect(page.locator('.report-badge')).toBeVisible();
      await expect(page.locator('.table-items')).toBeVisible();

      // Ambil Screenshot Cetak Laporan
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-laporan-06-cetak-laporan.png',
        fullPage: true,
      });

      // Kembali ke halaman laporan
      await page.goto('apotek/laporan');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(600);
    }

    // ========================================================================
    // STAGE 10: Verifikasi Error Konsol
    // ========================================================================
    console.log('--- STAGE 10: Verifikasi Error Konsol ---');
    console.log(`Total console errors caught: ${consoleErrors.length}`);
    if (consoleErrors.length > 0) {
      console.log('Console Errors caught:', consoleErrors);
    }
    expect(consoleErrors.length).toBe(0);

    console.log('=== SEMUA 10 TAHAP PENGUJIAN /apotek/laporan BERHASIL DENGAN SEMPURNA! ===');
  });
});

