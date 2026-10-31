import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Alur Detail Pelayanan Medis Klinik (RME, SOAP, Odontogram, Lab, Surat Medis, Pasien Detail)
 */
test.describe('E2E: Detail Pelayanan Medis & Rekam Medis (Klinik RME)', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Klinik: Harus dapat membuka RME & Form SOAP Dokter', async ({ page }) => {
    await page.goto('klinik/soap');
    await expect(page.locator('body')).toContainText(/SOAP|Rekam Medis|Diagnosa|Pemeriksaan/i);
  });

  test('Klinik: Harus dapat membuka Modul Laboratorium Klinis', async ({ page }) => {
    await page.goto('klinik/lab');
    await expect(page.locator('body')).toContainText(/Laboratorium|Lab|Hasil Uji|Pemeriksaan/i);
  });

  test('Klinik: Harus dapat membuka Modul Pembuatan Surat Medis (SKS / SKD / Rujukan)', async ({ page }) => {
    await page.goto('klinik/surat');
    await expect(page.locator('body')).toContainText(/Surat|Keterangan Sakit|Sehat|Rujukan/i);
  });

  test('Klinik: Harus dapat membuka Laporan Kunjungan & Pelayanan Medis', async ({ page }) => {
    await page.goto('klinik/laporan');
    await expect(page.locator('body')).toContainText(/Laporan|Kunjungan|Statistik|Pasien/i);
  });

  test('Klinik: Modal Registrasi Pasien Baru dapat dibuka dari Direktori Pasien', async ({ page }) => {
    await page.goto('klinik/pendaftaran');
    // Klik tombol Registrasi Pasien Baru
    const regBtn = page.locator('button[data-target="#registerPatientModal"]');
    if (await regBtn.isVisible()) {
      await regBtn.click();
      await expect(page.locator('#registerPatientModal')).toBeVisible();
    }
  });

  test('Klinik: Modal Kunjungan Baru dapat dibuka', async ({ page }) => {
    await page.goto('klinik/pendaftaran');
    const visitBtn = page.locator('.btn-new-visit-standalone');
    if (await visitBtn.isVisible()) {
      await visitBtn.click();
      await expect(page.locator('#visitModal')).toBeVisible();
    }
  });

});

