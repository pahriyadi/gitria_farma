import { defineConfig, devices } from '@playwright/test';

/**
 * Konfigurasi Playwright E2E Testing untuk Sawamawa Medical Center & Gitria Farma ERP
 */
export default defineConfig({
  testDir: './tests/e2e',
  testMatch: /.*\.e2e-spec\.ts/,
  timeout: 30 * 1000,
  expect: {
    timeout: 5000,
  },
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: 0,
  workers: 1,
  reporter: [
    ['list'],
    ['html', { outputFolder: 'tests/e2e-report', open: 'never' }]
  ],
  use: {
    baseURL: process.env.TEST_BASE_URL || 'http://localhost/sawamawamedicalcenter.id/',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    headless: true, // Ubah false jika ingin melihat browser bergerak secara visual
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
