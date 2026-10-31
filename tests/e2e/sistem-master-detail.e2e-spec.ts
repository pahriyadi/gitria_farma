import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Master Data, Administrasi Sistem & Keamanan ERP
 * Master Poli, Dokter, Tindakan, Master ICD-10 & ICD-9, Master Metode Pembayaran, User Management & Audit Trail
 */
test.describe('E2E: Detail Master Data & Administrasi Sistem', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Sistem: Harus dapat membuka Master Data Klinik Terpadu (Poli, Dokter, Tindakan, Bed)', async ({ page }) => {
    await page.goto('system/master-klinik');
    await expect(page.locator('body')).toContainText(/Master Klinik|Poli|Dokter|Tindakan|Kategori/i);
  });

  test('Sistem: Harus dapat membuka Master ICD-10 & Prosedur ICD-9-CM', async ({ page }) => {
    await page.goto('system/master-icd');
    await expect(page.locator('body')).toContainText(/ICD-10|ICD-9|Diagnosa|Prosedur/i);
  });

  test('Sistem: Harus dapat membuka Master Metode Pembayaran & Multi-Gateway', async ({ page }) => {
    await page.goto('system/master-pembayaran');
    await expect(page.locator('body')).toContainText(/Metode Pembayaran|Tunai|Transfer|QRIS|EDC/i);
  });

  test('Sistem: Harus dapat membuka Manajemen Pengguna (Users & RBAC Roles)', async ({ page }) => {
    await page.goto('system/users');
    await expect(page.locator('body')).toContainText(/Pengguna|User|Role|Akses|Hak/i);
  });

  test('Sistem: Harus dapat membuka Audit Trail & Log Aktivitas Pengguna', async ({ page }) => {
    await page.goto('system/audit');
    await expect(page.locator('body')).toContainText(/Audit Trail|Log Aktivitas|Riwayat/i);
  });

  test('Sistem: Harus dapat membuka WhatsApp Gateway & Notifikasi Sistem', async ({ page }) => {
    await page.goto('system/whatsapp');
    await expect(page.locator('body')).toContainText(/WhatsApp|Pesan|Gateway|Notifikasi/i);
  });

  test('Sistem: Harus dapat membuka Pusat Diagnostik Error Tracker', async ({ page }) => {
    await page.goto('system/error-logs');
    await expect(page.locator('body')).toContainText(/Error|Log|Diagnostik|Sistem/i);
  });

});

