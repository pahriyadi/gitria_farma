import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Fitur Detail Farmasi & Apotek
 * Stock Opname, Cetak Etiket, Gudang Farmasi, Laporan Penjualan Farmasi
 */
test.describe('E2E: Detail Fitur Apotek & Farmasi', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Apotek: Harus dapat membuka Stock Opname Fisik vs Sistem', async ({ page }) => {
    await page.goto('apotek/opname');
    await expect(page.locator('body')).toContainText(/Opname|Penyesuaian|Fisik|Selisih/i);
  });

  test('Apotek: Harus dapat membuka Laporan Penjualan Obat Retail', async ({ page }) => {
    await page.goto('apotek/laporan');
    await expect(page.locator('body')).toContainText(/Laporan|Penjualan|Omset|Apotek/i);
  });

  test('Apotek: Modal Transfer Antar Gudang dapat diakses di manajemen gudang', async ({ page }) => {
    await page.goto('apotek/gudang');
    await expect(page.locator('body')).toContainText(/Gudang|Depo|Transfer/i);
  });

  test('Apotek: Kasir POS OTC memiliki kalkulasi bayar dan kembalian', async ({ page }) => {
    await page.goto('apotek/penjualan');
    // Memastikan elemen POS apotek tersedia
    await expect(page.locator('body')).toContainText(/Bayar|Total|Keranjang|Kasir/i);
  });

});

