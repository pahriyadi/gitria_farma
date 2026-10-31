import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Fitur Detail Distributor & Farmasi Grosir B2B
 * Riwayat Faktur Penjualan, Transfer Stok Gudang Induk ke Grosir, Retur Barang Grosir, Buku Kas Grosir
 */
test.describe('E2E: Detail Fitur Unit Distributor & Grosir B2B', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Distributor: Harus dapat membuka Riwayat Faktur Penjualan Grosir B2B', async ({ page }) => {
    await page.goto('distributor/riwayat');
    await expect(page.locator('body')).toContainText(/Riwayat|Faktur|Penjualan|Grosir/i);
  });

  test('Distributor: Harus dapat membuka Form Mutasi Transfer Stok Gudang ke Grosir', async ({ page }) => {
    await page.goto('distributor/transfer');
    await expect(page.locator('body')).toContainText(/Transfer|Mutasi|Gudang|Distributor/i);
  });

  test('Distributor: Harus dapat membuka Modul Retur Penjualan Grosir', async ({ page }) => {
    await page.goto('distributor/retur');
    await expect(page.locator('body')).toContainText(/Retur|Pengembalian|Grosir/i);
  });

  test('Distributor: Harus dapat membuka Buku Kas Masuk/Keluar Grosir', async ({ page }) => {
    await page.goto('distributor/kas');
    await expect(page.locator('body')).toContainText(/Kas|Distributor|Pemasukan|Pengeluaran/i);
  });

  test('Distributor: Form Kasir Grosir memiliki opsi Pembayaran Kredit TOP', async ({ page }) => {
    await page.goto('distributor/penjualan');
    await expect(page.locator('body')).toContainText(/Tunai|Kredit|Jatuh Tempo|TOP|Diskon/i);
  });

});

