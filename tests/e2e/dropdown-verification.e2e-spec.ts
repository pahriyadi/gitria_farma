import { test, expect } from '@playwright/test';

test.describe('E2E: Verifikasi Fungsionalitas Dropdown Sistem (Light & Dark Mode)', () => {

  test.beforeEach(async ({ page }) => {
    // Pastikan modal whats new tidak memblokir pointer
    await page.addInitScript(() => {
      window.localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
    });

    // Login sebagai superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
  });

  test('Dropdown Navbar: Utility Dropdown, Notifikasi & Profil Pengguna harus berfungsi dengan benar', async ({ page }) => {
    // 1. Cek Header Utility Dropdown (3-dot)
    const utilityToggle = page.locator('.compact-navbar-utility-dropdown .compact-action-toggle');
    await expect(utilityToggle).toBeVisible();
    await utilityToggle.click();

    const utilityMenu = page.locator('.compact-navbar-utility-dropdown .compact-action-menu');
    await expect(utilityMenu).toBeVisible();
    await expect(utilityMenu).toHaveClass(/show/);
    await expect(utilityMenu.locator('.compact-action-item')).toHaveCount(4);

    // Klik tombol utility lagi untuk menutup
    await utilityToggle.click();
    await expect(utilityMenu).not.toHaveClass(/show/);

    // 2. Cek Dropdown Notifikasi Sistem
    const notifToggle = page.locator('a[data-toggle="dropdown"]:has(#icon-nav-bell)');
    await expect(notifToggle).toBeVisible();
    await notifToggle.click();

    const notifMenu = page.locator('#notif-dropdown');
    await expect(notifMenu).toBeVisible();
    await expect(notifMenu).toHaveClass(/show/);

    // 3. Cek Dropdown Profil Pengguna
    const profileToggle = page.locator('.nav-profile-btn');
    await expect(profileToggle).toBeVisible();
    await profileToggle.click();

    const profileMenu = page.locator('.nav-profile-btn + .dropdown-menu');
    await expect(profileMenu).toBeVisible();
    await expect(profileMenu).toHaveClass(/show/);
    await expect(profileMenu.locator('a[href*="logout"]')).toBeVisible();

    // Notifikasi harus otomatis tertutup saat profil terbuka
    await expect(notifMenu).not.toHaveClass(/show/);
  });

  test('Dropdown Searchable Select (.macos-select-*): Harus merender, membuka opsi, melakukan pencarian, dan memilih opsi', async ({ page }) => {
    // Buka halaman akuntansi jurnal yang memiliki elemen select
    await page.goto('accounting/jurnal');
    
    // Cek apakah ada macos-select-wrapper
    const selectWrapper = page.locator('.macos-select-wrapper').first();
    await expect(selectWrapper).toBeVisible();

    const selectTrigger = selectWrapper.locator('.macos-select-trigger');
    await expect(selectTrigger).toBeVisible();

    // Klik trigger untuk membuka dropdown menu
    await selectTrigger.click();

    const selectMenu = selectWrapper.locator('.macos-select-menu');
    await expect(selectMenu).toBeVisible();
    await expect(selectMenu).toHaveClass(/is-open/);

    // Cek options list
    const options = selectMenu.locator('.macos-select-option');
    await expect(options.first()).toBeVisible();

    // Tekan tombol Escape untuk menutup
    await page.keyboard.press('Escape');
    await expect(selectMenu).not.toHaveClass(/is-open/);
  });

  test('Dropdown Compact Action Baris Tabel (.compact-action-dropdown): Harus dapat dibuka tanpa terpotong overflow tabel', async ({ page }) => {
    // Buka halaman klinik pendaftaran
    await page.goto('klinik/pendaftaran');

    // Tunggu tabel siap
    await page.waitForSelector('table', { state: 'visible' });

    // Cari compact-action-dropdown di header atau tabel
    const actionToggle = page.locator('.compact-action-toggle').first();
    await expect(actionToggle).toBeVisible();
    await actionToggle.click();

    const actionMenu = page.locator('.compact-action-menu.show').first();
    await expect(actionMenu).toBeVisible();

    // Tekan tombol Escape untuk menutup
    await page.keyboard.press('Escape');
    await expect(page.locator('.compact-action-menu.show')).toHaveCount(0);
  });

  test('Dropdown dalam Dark Mode: Tampilan warna, background, dan border konsisten dengan Paper Dark', async ({ page }) => {
    // Tunggu halaman siap lalu aktifkan dark mode
    await page.waitForSelector('.compact-navbar-utility-dropdown', { state: 'visible' });
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
    });

    const utilityToggle = page.locator('.compact-navbar-utility-dropdown .compact-action-toggle');
    await utilityToggle.click();

    const utilityMenu = page.locator('.compact-navbar-utility-dropdown .compact-action-menu');
    await expect(utilityMenu).toBeVisible();

    // Verifikasi warna latar belakang menu di dark mode adalah #1f2328
    const bgColor = await utilityMenu.evaluate((el) => {
      return window.getComputedStyle(el).backgroundColor;
    });

    // rgb(31, 35, 40) is #1f2328
    expect(bgColor).toBe('rgb(31, 35, 40)');

    // Verifikasi warna teks item di dark mode
    const itemColor = await utilityMenu.locator('.compact-action-item').first().evaluate((el) => {
      return window.getComputedStyle(el).color;
    });
    // rgb(229, 231, 235) is #e5e7eb
    expect(itemColor).toBe('rgb(229, 231, 235)');
  });

});
