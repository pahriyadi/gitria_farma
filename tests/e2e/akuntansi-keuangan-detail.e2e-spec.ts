import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Fitur Detail Akuntansi & Manajemen Keuangan Kasir
 * Saldo Awal Otomatis Seimbang, Aturan Pemecahan Jurnal (Split Rules), Fee Dokter, Rekap Shift Kasir
 */
test.describe('E2E: Detail Fitur Akuntansi & Keuangan', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Akuntansi: Harus dapat membuka Setup Saldo Awal (Auto-Balancing Modal)', async ({ page }) => {
    await page.goto('accounting/saldo-awal');
    await expect(page.locator('body')).toContainText(/Saldo Awal|Debit|Kredit|Modal/i);
  });

  test('Akuntansi: Harus dapat membuka Pengaturan Dynamic Journal Split Rules', async ({ page }) => {
    await page.goto('accounting/aturan-jurnal');
    await expect(page.locator('body')).toContainText(/Aturan Jurnal|Template|Split|Akun/i);
  });

  test('Keuangan: Harus dapat membuka Rekapitulasi Shift Kasir & Pendapatan Harian', async ({ page }) => {
    await page.goto('keuangan/rekap-harian');
    await expect(page.locator('body')).toContainText(/Rekap|Harian|Shift|Kasir/i);
  });

  test('Keuangan: Harus dapat membuka Perhitungan Jasa Medis & Fee Dokter', async ({ page }) => {
    await page.goto('keuangan/fee-dokter');
    await expect(page.locator('body')).toContainText(/Fee Dokter|Jasa Medis|Komisi|Dokter/i);
  });

});

