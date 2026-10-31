import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Akuntansi Double-Entry & Laporan Keuangan
 * Menguji Chart of Accounts (COA), Jurnal Umum, Buku Besar, dan 5 Laporan Keuangan.
 */
test.describe('E2E: Modul Akuntansi Double-Entry', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka Chart of Accounts (COA)', async ({ page }) => {
    await page.goto('accounting/coa');
    await expect(page.locator('body')).toContainText(/Bagan Akun|Chart of Accounts|COA/i);
  });

  test('Harus dapat membuka Jurnal Umum', async ({ page }) => {
    await page.goto('accounting/jurnal');
    await expect(page.locator('body')).toContainText(/Jurnal|Debit|Kredit/i);
  });

  test('Harus dapat membuka Buku Besar (General Ledger)', async ({ page }) => {
    await page.goto('accounting/buku-besar');
    await expect(page.locator('body')).toContainText(/Buku Besar|Ledger/i);
  });

  test('Harus dapat membuka Laporan Keuangan Komprehensif (Laba Rugi, Neraca, Arus Kas)', async ({ page }) => {
    await page.goto('accounting/laporan');
    await expect(page.locator('body')).toContainText(/Laporan Keuangan|Neraca|Laba Rugi/i);
  });

  test('Harus dapat membuka Pusat Log Pembatalan Transaksi (Void Integrity)', async ({ page }) => {
    await page.goto('accounting/void-logs');
    await expect(page.locator('body')).toContainText(/Void|Pembatalan|Audit/i);
  });

});

