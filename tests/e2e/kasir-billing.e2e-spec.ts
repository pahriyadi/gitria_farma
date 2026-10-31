import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Modul Keuangan & Kasir Terpadu
 * Memastikan halaman kasir & kas harian dapat diakses tanpa hambatan.
 */
test.describe('E2E: Modul Kasir & Keuangan', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka halaman kasir billing dengan normal', async ({ page }) => {
    await page.goto('keuangan/kasir');
    
    // Cek elemen kasir / tagihan
    await expect(page.locator('body')).toContainText(/Kasir|Tagihan|Billing/);
  });

  test('Harus dapat membuka halaman kas harian', async ({ page }) => {
    await page.goto('keuangan/kas');
    
    // Cek saldo / transaksi kas
    await expect(page.locator('body')).toContainText(/Kas|Saldo|Transaksi/);
  });

});

