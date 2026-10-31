import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Resto Gizi, HRD & Pengadaan (Procurement)
 */
test.describe('E2E: Modul Pendukung (Resto, HRD, Pengadaan & Sistem)', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka POS Kasir Resto Gizi & Menu', async ({ page }) => {
    await page.goto('resto/pos');
    await expect(page.locator('body')).toContainText(/Resto|Menu|Meja|Kasir/i);
  });

  test('Harus dapat membuka Kitchen Display System (KDS Dapur)', async ({ page }) => {
    await page.goto('resto/kitchen');
    await expect(page.locator('body')).toContainText(/Dapur|Kitchen|Pesanan|KDS/i);
  });

  test('Harus dapat membuka Manajemen Pegawai & HRD', async ({ page }) => {
    await page.goto('hrd/pegawai');
    await expect(page.locator('body')).toContainText(/Pegawai|Karyawan|SDM|HRD/i);
  });

  test('Harus dapat membuka modul Pengadaan (Procurement PO & Supplier)', async ({ page }) => {
    await page.goto('procurement/po');
    await expect(page.locator('body')).toContainText(/Purchase Order|Pengadaan|PO|Pemasok/i);
  });

  test('Harus dapat membuka Peta Alur Sistem 3D Interaktif', async ({ page }) => {
    await page.goto('alur-3d');
    await expect(page.locator('body')).toContainText(/Peta Alur Sistem 3D|Skenario/i);
  });

  test('Harus dapat membuka Pusat Bantuan & Panduan Sistem', async ({ page }) => {
    await page.goto('bantuan');
    await expect(page.locator('body')).toContainText(/Pusat Bantuan|Panduan/i);
  });

});

