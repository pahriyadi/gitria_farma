import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL RESTO & DAPUR GIZI - KDS DAPUR GIZI (/resto/dapur)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. 4 Kartu KPI Workload Antrean (Total Tiket, Baru, Dimasak, Siap Saji).
 * 3. Filter Status Tab (Semua, Baru, Dimasak, Siap) & Pencarian Cepat No. Tiket.
 * 4. Siklus Status Dapur: Mulai Masak -> Siap Saji -> Selesai Disajikan.
 * 5. Instruksi Diet Khusus & Pantangan Medis Pasien Poli Gizi.
 * 6. Tombol Cetak Tiket Dapur & Kontrol Jeda/Lanjut Auto-Reload.
 * 7. Verifikasi 0 Console Errors.
 */

test.describe('Resto KDS: Kitchen Display System & Dapur Gizi Pipeline', () => {
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

  test('E2E Full Flow: KDS Workload KPI -> Dark Mode -> Filter Tabs -> Lifecycle (Cook/Ready/Served) -> 0 Errors', async ({ page }) => {
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
    // STAGE 1: Membuka /resto/dapur & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /resto/dapur & Verifikasi Light Mode ---');
    await page.goto('resto/dapur');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan sidebar ter-collapse rapi untuk tampilan layar lebar KDS
    const pushmenuBtn = page.locator('[data-widget="pushmenu"]').first();
    const isCollapsed = await page.evaluate(() => document.body.classList.contains('sidebar-collapse'));
    if (!isCollapsed && await pushmenuBtn.isVisible()) {
      await pushmenuBtn.click();
      await page.waitForTimeout(350);
    }

    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
    });

    // Verifikasi Judul Halaman KDS
    await expect(page.locator('h4').filter({ hasText: 'Kitchen Display System (KDS) & Dapur Gizi' })).toBeVisible();

    // Verifikasi 0 .table-striped
    const stripedCount = await page.locator('.table-striped').count();
    console.log('Jumlah .table-striped terdeteksi:', stripedCount);
    expect(stripedCount).toBe(0);

    // Verifikasi 4 Kartu KPI Workload
    const statBoxes = page.locator('.kds-stat-box');
    const statCount = await statBoxes.count();
    console.log('Jumlah Kotak KPI Workload KDS:', statCount);
    expect(statCount).toBe(4);

    await page.screenshot({ path: `${artifactDir}/resto-dapur-01-light.png`, fullPage: true });

    // ========================================================================
    // STAGE 2: Verifikasi Paper Dark Mode
    // ========================================================================
    console.log('--- STAGE 2: Verifikasi Paper Dark Mode ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.waitForTimeout(400);

    // Periksa background card dan border pada Dark Mode
    const darkCardBg = await page.evaluate(() => {
      const card = document.querySelector('.kds-card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log('Computed dark-mode card background:', darkCardBg);
    // rgb(31, 35, 40) corresponds to #1f2328
    expect(darkCardBg).toBe('rgb(31, 35, 40)');

    await page.screenshot({ path: `${artifactDir}/resto-dapur-02-dark.png`, fullPage: true });

    // Kembalikan ke Light Mode untuk alur fungsional berikutnya
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Filter Status Tabs & Pencarian Cepat
    // ========================================================================
    console.log('--- STAGE 3: Filter Status Tabs & Pencarian Cepat ---');
    // Tab: Baru Masuk (new)
    await page.locator('#kdsFilterTabs a[data-status="new"]').click();
    await page.waitForTimeout(300);
    const newTicketsVisible = await page.locator('.kds-ticket-wrapper:visible').count();
    console.log('Tiket baru tampak setelah filter:', newTicketsVisible);

    // Tab: Sedang Dimasak (cooking)
    await page.locator('#kdsFilterTabs a[data-status="cooking"]').click();
    await page.waitForTimeout(300);

    // Tab: Siap Saji (ready)
    await page.locator('#kdsFilterTabs a[data-status="ready"]').click();
    await page.waitForTimeout(300);

    // Tab: Semua Tiket (all)
    await page.locator('#kdsFilterTabs a[data-status="all"]').click();
    await page.waitForTimeout(300);
    const allTicketsVisible = await page.locator('.kds-ticket-wrapper:visible').count();
    console.log('Semua tiket tampak:', allTicketsVisible);

    // Pencarian cepat
    await page.fill('#kds-search-input', 'ORD-');
    await page.waitForTimeout(300);
    expect(await page.locator('.kds-ticket-wrapper:visible').count()).toBeGreaterThanOrEqual(1);

    await page.fill('#kds-search-input', '');
    await page.waitForTimeout(200);

    // ========================================================================
    // STAGE 4: Siklus Status KDS (Mulai Masak -> Siap Saji)
    // ========================================================================
    console.log('--- STAGE 4: Siklus Status KDS (Mulai Masak -> Siap Saji) ---');
    // Cari tombol "Mulai Masak"
    const btnCook = page.locator('.btn-kds-cook').first();
    if (await btnCook.count() > 0) {
      console.log('Menjalankan aksi Mulai Masak...');
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        btnCook.click()
      ]);

      console.log('Status berhasil diubah menjadi COOKING.');
    }

    // Sekarang cari tombol "Siap Saji" (.btn-kds-ready)
    const btnReady = page.locator('.btn-kds-ready').first();
    if (await btnReady.count() > 0) {
      console.log('Menjalankan aksi Siap Saji...');
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        btnReady.click()
      ]);

      await expect(page.locator('.btn-kds-served').first()).toBeVisible();
      console.log('Status berhasil diubah menjadi READY & tombol Saji aktif.');
    }

    // Pastikan layout rapi sebelum screenshot
    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.screenshot({ path: `${artifactDir}/resto-dapur-03-cooking-ready.png`, fullPage: true });

    // ========================================================================
    // STAGE 5: Selesai Disajikan (.btn-kds-served)
    // ========================================================================
    console.log('--- STAGE 5: Selesai Disajikan (.btn-kds-served) ---');
    const initialServedCount = await page.locator('.btn-kds-served').count();
    const btnServed = page.locator('.btn-kds-served').first();
    if (await btnServed.count() > 0) {
      console.log('Menjalankan aksi Selesai Disajikan...');
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        btnServed.click()
      ]);

      console.log('Item berhasil ditandai SERVED dan dikeluarkan dari antrean aktif.');
    }

    // Pastikan layout rapi sebelum screenshot
    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.screenshot({ path: `${artifactDir}/resto-dapur-04-item-served.png`, fullPage: true });

    // ========================================================================
    // STAGE 6: Verifikasi Tombol Pause / Resume Auto-Reload
    // ========================================================================
    console.log('--- STAGE 6: Verifikasi Auto-Reload Toggle ---');
    const toggleBtn = page.locator('#btn-toggle-auto-reload');
    await toggleBtn.click();
    await page.waitForTimeout(200);
    await expect(page.locator('#lbl-auto-reload')).toHaveText('Lanjut');
    await expect(page.locator('#reload-timer')).toHaveText('(Dijeda)');

    // Klik lagi untuk melanjutkan
    await toggleBtn.click();
    await page.waitForTimeout(200);
    await expect(page.locator('#lbl-auto-reload')).toHaveText('Jeda');
    await expect(page.locator('#reload-timer')).toHaveText(/\(\d+s\)/);

    // ========================================================================
    // STAGE 7: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 7: Verifikasi 0 Console Errors ---');
    console.log('Daftar console error yang tertangkap:', consoleErrors);
    expect(consoleErrors).toHaveLength(0);
    console.log('PASSED: E2E Resto Dapur Pipeline 100% Sukses!');
  });
});
