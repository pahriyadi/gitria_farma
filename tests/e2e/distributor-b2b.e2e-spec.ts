import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Unit Bisnis Grosir & Distributor PBF Farmasi (B2B)
 * Menguji dashboard B2B, kasir faktur grosir, pelanggan grosir, stok, piutang, dan laba rugi distributor.
 */
test.describe('E2E: Unit Distributor & Farmasi Grosir B2B', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka Dashboard Distributor B2B', async ({ page }) => {
    await page.goto('distributor/dashboard');
    await expect(page.locator('body')).toContainText(/Distributor|Grosir|Omset/i);
  });

  test('Harus dapat membuka Kasir Faktur Penjualan Grosir B2B', async ({ page }) => {
    await page.goto('distributor/penjualan');
    await expect(page.locator('body')).toContainText(/Penjualan|Faktur|Grosir/i);
  });

  test('Harus dapat membuka Master Data Pelanggan Grosir & Plafon Kredit', async ({ page }) => {
    await page.goto('distributor/pelanggan');
    await expect(page.locator('body')).toContainText(/Pelanggan|Plafon|Mitra/i);
  });

  test('Harus dapat membuka Manajemen Stok & Batch Grosir', async ({ page }) => {
    await page.goto('distributor/stok');
    await expect(page.locator('body')).toContainText(/Stok|Batch|Grosir/i);
  });

  test('Harus dapat membuka Monitoring Piutang Usaha & Aging Schedule', async ({ page }) => {
    await page.goto('distributor/piutang');
    await expect(page.locator('body')).toContainText(/Piutang|Jatuh Tempo|Aging/i);
  });

  test('Harus dapat membuka Laporan Laba Rugi Mandiri Distributor', async ({ page }) => {
    await page.goto('distributor/laporan');
    await expect(page.locator('body')).toContainText(/Laba Rugi|Laporan|Distributor/i);
  });

});

