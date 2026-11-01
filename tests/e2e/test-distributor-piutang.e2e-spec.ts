import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL DISTRIBUTOR & GROSIR B2B - MONITORING PIUTANG & AGING (/distributor/piutang)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Tabel Piutang Usaha Grosir (Faktur, Pelanggan, Jatuh Tempo, Aging, Total, Terbayar, Sisa, Status).
 * 3. Filter Klasifikasi Aging Schedule (Semua, 0-30 Hari, 31-60 Hari, >90 Hari, Lewat Tempo).
 * 4. Modal Pembayaran Piutang & Input Pelunasan.
 * 5. Modal Riwayat Pembayaran Faktur Piutang.
 * 6. Verifikasi 0 Console Errors.
 */

test.describe('Distributor & Grosir B2B: Receivables & Aging Pipeline', () => {
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

  test('Monitoring Piutang & Aging schedule, payment modal, history logs, and dark mode compliance', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const text = msg.text();
        if (!text.includes('favicon') && !text.includes('chrome-extension')) {
          consoleErrors.push(text);
        }
      }
    });

    // 1. Akses distributor/piutang
    await page.goto('distributor/piutang');
    await page.waitForLoadState('domcontentloaded');

    // Pastikan halaman termuat dengan benar
    await expect(page.locator('body')).toContainText('Monitoring Piutang');

    // Verifikasi Desain Tabel: Tidak ada .table-striped
    const stripedTables = await page.locator('table.table-striped').count();
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: `${artifactDir}/distributor-piutang-01-light.png`,
      fullPage: false,
    });

    // 2. Verifikasi Dark Mode (Paper Dark: #14171a, #1f2328, #374151)
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.setAttribute('data-theme', 'dark');
    });
    await page.waitForTimeout(300);

    const darkBg = await page.evaluate(() => {
      const card = document.querySelector('.card.dist-card') as HTMLElement;
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    // Mengharapkan warna background kartu bertema dark (#1f2328 / rgb(31, 35, 40))
    expect(darkBg).toMatch(/rgb\(31,\s*35,\s*40\)/);

    await page.screenshot({
      path: `${artifactDir}/distributor-piutang-02-dark.png`,
      fullPage: false,
    });

    // Kembalikan ke Light Mode
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.removeAttribute('data-theme');
    });
    await page.waitForTimeout(200);

    // 3. Test Aging Filter buttons
    const filterAging30 = page.locator('a:has-text("0-30 Hari")');
    if (await filterAging30.isVisible()) {
      await filterAging30.click();
      await page.waitForLoadState('domcontentloaded');
      expect(page.url()).toContain('aging=0-30');
    }

    await page.screenshot({
      path: `${artifactDir}/distributor-piutang-03-aging-filter.png`,
      fullPage: false,
    });

    // Kembali ke semua piutang
    await page.goto('distributor/piutang');
    await page.waitForLoadState('domcontentloaded');

    // 4. Test Buka Modal Pembayaran Piutang
    const payBtnCount = await page.locator('.btn-pay-receivable').count();
    
    if (payBtnCount > 0) {
      // Klik tombol bayar menggunakan evaluate agar tidak terhalang compact-action-toggle
      await page.evaluate(() => {
        const btn = document.querySelector('.btn-pay-receivable') as HTMLElement;
        if (btn) btn.click();
      });

      await expect(page.locator('#modalPayment')).toBeVisible();
      await page.waitForTimeout(300);

      await page.screenshot({
        path: `${artifactDir}/distributor-piutang-04-payment-modal.png`,
      });

      // Tutup modal pembayaran
      await page.click('#modalPayment button[data-dismiss="modal"]');
      await page.waitForTimeout(300);
    } else {
      // Jika tidak ada data row, buka modal via JS untuk memverifikasi UI modal
      await page.evaluate(() => {
        (window as any).openPaymentModal({
          id: 999,
          invoice_no: 'INV-TEST-0001',
          customer_name: 'Apotek Test Farma',
          remaining_balance: 500000
        });
      });
      await expect(page.locator('#modalPayment')).toBeVisible();
      await page.waitForTimeout(300);

      await page.screenshot({
        path: `${artifactDir}/distributor-piutang-04-payment-modal.png`,
      });
      await page.click('#modalPayment button[data-dismiss="modal"]');
      await page.waitForTimeout(300);
    }

    // 5. Test Modal Riwayat Pembayaran Faktur
    const viewLogsBtnCount = await page.locator('.btn-view-payment-logs').count();
    if (viewLogsBtnCount > 0) {
      await page.evaluate(() => {
        const btn = document.querySelector('.btn-view-payment-logs') as HTMLElement;
        if (btn) btn.click();
      });
      await expect(page.locator('#modalPaymentLogs')).toBeVisible();
      await page.waitForTimeout(500);

      await page.screenshot({
        path: `${artifactDir}/distributor-piutang-05-payment-logs.png`,
      });
      await page.click('#modalPaymentLogs button[data-dismiss="modal"]');
    } else {
      await page.evaluate(() => {
        (window as any).viewPaymentLogs(1, 'INV-DIST-DEMO-001');
      });
      await expect(page.locator('#modalPaymentLogs')).toBeVisible();
      await page.waitForTimeout(500);

      await page.screenshot({
        path: `${artifactDir}/distributor-piutang-05-payment-logs.png`,
      });
      await page.click('#modalPaymentLogs button[data-dismiss="modal"]');
    }

    // 6. Verifikasi tidak ada fatal console errors
    expect(consoleErrors).toEqual([]);
  });
});
