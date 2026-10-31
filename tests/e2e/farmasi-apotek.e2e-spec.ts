import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Modul Farmasi & Apotek Retail
 * Menguji katalog obat, penjualan OTC, resep masuk, dan kartu stok.
 */
test.describe('E2E: Modul Farmasi & Apotek Retail', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka katalog master obat dengan normal', async ({ page }) => {
    await page.goto('apotek/obat');
    await expect(page.locator('body')).toContainText(/Katalog|Obat|Farmasi/i);
  });

  test('Harus dapat membuka halaman penjualan kasir apotek (POS OTC)', async ({ page }) => {
    await page.goto('apotek/penjualan');
    await expect(page.locator('body')).toContainText(/Penjualan|Kasir|Apotek/i);
  });

  test('Harus dapat membuka antrean e-Resep dokter', async ({ page }) => {
    await page.goto('apotek/resep');
    await expect(page.locator('body')).toContainText(/Resep|Dokter|Dispensing/i);
  });

  test('Harus dapat membuka kartu stok mutasi obat', async ({ page }) => {
    await page.goto('apotek/kartu-stok');
    await expect(page.locator('body')).toContainText(/Kartu Stok|Mutasi|Persediaan/i);
  });

  test('Harus dapat membuka multi-gudang dan transfer stok', async ({ page }) => {
    await page.goto('apotek/gudang');
    await expect(page.locator('body')).toContainText(/Gudang|Depo|Stok/i);
  });

});

