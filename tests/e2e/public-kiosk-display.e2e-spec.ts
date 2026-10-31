import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Fitur Publik, Kiosk Mandiri (APM) & Layar Display TV Poli
 */
test.describe('E2E: Portal Publik, Kiosk Mandiri & Display TV', () => {

  test('Publik: Halaman depan (Landing Page) klinik dapat diakses', async ({ page }) => {
    await page.goto('');
    await expect(page.locator('body')).toContainText(/Sawamawa|Klinik|Fasilitas|Layanan/i);
  });

  test('Publik: Pendaftaran Online Mandiri dapat dibuka', async ({ page }) => {
    await page.goto('daftar-online');
    await expect(page.locator('body')).toContainText(/Pendaftaran Online|Pasien Baru|Pasien Lama|Booking/i);
  });

  test('Kiosk Mandiri (APM): Layar anjungan mandiri pasien dapat dibuka', async ({ page }) => {
    await page.goto('klinik/kiosk');
    await expect(page.locator('body')).toContainText(/Anjungan|Kiosk|Mandiri|Antrean/i);
  });

  test('Display TV: Layar monitor antrean poli dapat dibuka tanpa login (Zero Timeout)', async ({ page }) => {
    await page.goto('klinik/display');
    await expect(page.locator('body')).toContainText(/Nomor Antrean|Poli|Dokter|Sawamawa/i);
  });

});

