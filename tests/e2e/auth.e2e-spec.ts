import { test, expect } from '@playwright/test';

/**
 * E2E Spec: Autentikasi Pengguna & Keamanan Sesi
 * Menguji alur login berhasil, login gagal, dan logout sistem.
 */
test.describe('E2E: Modul Autentikasi (Login & Logout)', () => {

  test('Harus menampilkan halaman login dengan elemen lengkap', async ({ page }) => {
    await page.goto('login');

    // Cek judul halaman & form login
    await expect(page).toHaveTitle(/Portal Autentikasi/);
    await expect(page.locator('#username_input')).toBeVisible();
    await expect(page.locator('#login-password')).toBeVisible();
    await expect(page.locator('#btn-submit-login')).toBeVisible();
  });

  test('Harus menolak login dengan password yang salah', async ({ page }) => {
    await page.goto('login');

    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'password_salah_123');
    await page.click('#btn-submit-login');

    // Harus tetap di halaman login atau muncul alert error
    await expect(page.locator('.alert-danger')).toBeVisible();
    await expect(page).toHaveURL(/login/);
  });

  test('Harus berhasil login dengan kredensial Superadmin dan masuk ke Dashboard', async ({ page }) => {
    await page.goto('login');

    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');

    // Menunggu navigasi ke dashboard
    await expect(page).toHaveURL(/dashboard/);
    
    // Memastikan elemen penting di dashboard muncul (misal judul atau navbar user)
    await expect(page.locator('body')).toContainText(/Dashboard|Sawamawa/);
  });

  test('Harus dapat logout dan kembali ke halaman login', async ({ page }) => {
    // Login terlebih dahulu
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);

    // Buka URL logout langsung
    await page.goto('logout');
    await expect(page).toHaveURL(/login/);
  });

});

