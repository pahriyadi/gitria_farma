import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Inventaris Aset, HRD Absensi, Multi-level Approval Pengadaan, Backup Database
 */
test.describe('E2E: Inventaris Aset, HRD Absensi, Approval Pengadaan & Backup', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Inventaris: Harus dapat membuka modul Manajemen Aset & Penyusutan', async ({ page }) => {
    await page.goto('inventaris/aset');
    await expect(page.locator('body')).toContainText(/Inventaris|Aset|Depresiasi|Penyusutan/i);
  });

  test('HRD: Harus dapat membuka modul Absensi & Presensi Pegawai', async ({ page }) => {
    await page.goto('hrd/absensi');
    await expect(page.locator('body')).toContainText(/Absensi|Presensi|Shift|Check-in|Check-out/i);
  });

  test('Pengadaan: Harus dapat membuka halaman Multi-Level Approval Workflow', async ({ page }) => {
    await page.goto('procurement/approval');
    await expect(page.locator('body')).toContainText(/Approval|Persetujuan|Verifikasi|Status/i);
  });

  test('Sistem: Harus dapat membuka Manajemen Backup & Database Dump', async ({ page }) => {
    await page.goto('system/database');
    await expect(page.locator('body')).toContainText(/Database|Backup|Restore|Dump/i);
  });

  test('Sistem: Harus dapat membuka Pengaturan Konfigurasi Global Sistem', async ({ page }) => {
    await page.goto('system/settings');
    await expect(page.locator('body')).toContainText(/Pengaturan|Setting|Konfigurasi|Klinik/i);
  });

});

