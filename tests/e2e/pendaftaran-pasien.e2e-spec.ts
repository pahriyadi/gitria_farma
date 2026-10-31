import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Alur Pendaftaran Pasien Baru & Kunjungan Poli
 * Mensimulasikan staf pendaftaran membuat data pasien dan memilih poli tujuan.
 */
test.describe('E2E: Modul Pendaftaran Pasien & Antrean', () => {

  // Login otomatis sebagai Superadmin sebelum setiap tes
  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Harus dapat membuka halaman pendaftaran pasien dengan normal', async ({ page }) => {
    await page.goto('klinik/pendaftaran');
    
    // Cek form pendaftaran dan tabel antrean/pasien
    await expect(page.locator('body')).toContainText(/Pendaftaran|Pasien|Antrean/);
  });

  test('Harus dapat membuka halaman antrean klinik', async ({ page }) => {
    await page.goto('klinik/antrean');
    
    // Cek halaman antrean memuat tabel atau nomor antrean
    await expect(page.locator('body')).toContainText(/Antrean|Poli/);
  });

});
