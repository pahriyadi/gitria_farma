import { test, expect } from '@playwright/test';

test.describe('Apotek Resep: Comprehensive Pipeline, Dark/Light Mode, & Cross-Module Verification', () => {
  test.setTimeout(90000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    page.on('dialog', async dialog => {
      console.log('Dialog caught:', dialog.message());
      await dialog.accept();
    });

    // Login as superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: Queue -> FEFO Dispensing -> History -> Detail Modal -> 3 Print Views & Theme Switching', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // ------------------------------------------------------------------------
    // STAGE 1: BUKA /apotek/resep & VERIFIKASI DESAIN PAPER WHITE (LIGHT MODE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 1: Membuka /apotek/resep & Verifikasi Light Mode ---');
    await page.goto('apotek/resep');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('apotek/resep');
    await expect(page.locator('#resepTabs')).toBeVisible();
    await expect(page.locator('#tab-antrean-link')).toBeVisible();
    await expect(page.locator('#tab-riwayat-link')).toBeVisible();

    // Pastikan tidak ada class table-striped di halaman
    const stripedTables = await page.locator('table.table-striped').count();
    console.log('Jumlah tabel dengan .table-striped (Harus 0):', stripedTables);
    expect(stripedTables).toBe(0);

    // Ambil screenshot Light Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-01-resep-light.png',
      fullPage: true,
    });

    // ------------------------------------------------------------------------
    // STAGE 2: UJI MODE GELAP (PAPER DARK)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 2: Uji Mode Gelap (Paper Dark) ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
      localStorage.setItem('theme', 'dark');
    });
    await page.waitForTimeout(400);

    // Verifikasi palet warna Paper Dark di container tabs
    const tabsBg = await page.locator('#resepTabs').evaluate(el => window.getComputedStyle(el).backgroundColor);
    console.log('Background #resepTabs saat Dark Mode:', tabsBg);
    // #1f2328 corresponds to rgb(31, 35, 40)
    expect(tabsBg).toBe('rgb(31, 35, 40)');

    // Ambil screenshot Dark Mode
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-02-resep-dark.png',
      fullPage: true,
    });

    // Kembalikan ke Light Mode untuk pengujian alur kerja
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
      localStorage.setItem('theme', 'light');
    });
    await page.waitForTimeout(300);

    // ------------------------------------------------------------------------
    // STAGE 3: INTERAKSI ANTREAN RESEP MENUNGGU & FEFO WORKSPACE
    // ------------------------------------------------------------------------
    console.log('--- STAGE 3: Interaksi Antrean Resep & FEFO Workspace ---');
    const prescItems = page.locator('.presc-item');
    const prescCount = await prescItems.count();
    console.log('Jumlah antrean resep menunggu:', prescCount);

    if (prescCount > 0) {
      const firstItem = prescItems.first();
      const patientName = await firstItem.locator('h6').innerText();
      const isPaid = (await firstItem.getAttribute('data-is-paid')) === '1';
      console.log(`Memilih resep pasien: ${patientName} (Lunas: ${isPaid})`);

      // Klik item antrean resep
      await firstItem.click();
      await page.waitForTimeout(600);

      // Verifikasi workspace muncul dan placeholder hilang
      await expect(page.locator('#workspace-card')).toBeVisible();
      await expect(page.locator('#workspace-placeholder')).toBeHidden();

      // Verifikasi label pasien terisi
      const lblPatient = await page.locator('#lbl-patient').innerText();
      expect(lblPatient.trim()).toBe(patientName.trim());

      // Verifikasi banner status tagihan kasir
      const payBanner = page.locator('#box-payment-status');
      await expect(payBanner).toBeVisible();
      if (isPaid) {
        await expect(payBanner).toContainText(/TAGIHAN KASIR SUDAH LUNAS/i);
      } else {
        await expect(payBanner).toContainText(/TAGIHAN KASIR BELUM LUNAS/i);
      }

      // Verifikasi dynamic medicine card
      const medCards = page.locator('.med-dispense-card');
      const medCount = await medCards.count();
      console.log('Jumlah item obat yang siap disiapkan:', medCount);

      if (medCount > 0) {
        const firstMed = medCards.first();
        // Cek dropdown aksi
        const actionSelect = firstMed.locator('select.select-action');
        await expect(actionSelect).toBeVisible();

        // Uji ganti status penyerahan ke 'beli_luar'
        await actionSelect.selectOption('beli_luar');
        await page.waitForTimeout(300);
        await expect(firstMed.locator('[id^="luar-box-"]')).toBeVisible();

        // Kembalikan ke 'internal'
        await actionSelect.selectOption('internal');
        await page.waitForTimeout(300);
        await expect(firstMed.locator('[id^="batch-box-"]')).toBeVisible();
      }

      // Uji tombol panggilan suara apotek
      const callVoiceBtn = page.locator('#btn-call-pharmacy-voice');
      const compactToggle = page.locator('#workspace-card .compact-action-toggle');
      if (await compactToggle.isVisible()) {
        await compactToggle.click();
        await page.waitForTimeout(200);
      }
      await expect(callVoiceBtn).toBeVisible();
      await callVoiceBtn.click();
      await page.waitForTimeout(400);

      // Screenshot Workspace Aktif
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-03-workspace-active.png',
      });

      // Jika resep berstatus lunas, lakukan submit dispensing
      if (isPaid) {
        console.log('Menjalankan proses Selesai Dispensing & Serahkan Obat...');
        await page.click('#btn-submit-dispense');
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(1000);

        // Verifikasi flash message sukses atau peringatan
        const alertFlash = page.locator('.alert-success, .alert-flash-card');
        if (await alertFlash.count() > 0) {
          const alertText = await alertFlash.first().innerText();
          console.log('Hasil submit dispensing:', alertText);
        }
      }
    }

    // ------------------------------------------------------------------------
    // STAGE 4: TAB RIWAYAT e-RESEP SELESAI & MODAL DETAIL RESEP
    // ------------------------------------------------------------------------
    console.log('--- STAGE 4: Tab Riwayat Resep Selesai & Modal Detail ---');
    await page.click('#tab-riwayat-link');
    await page.waitForTimeout(600);

    await expect(page.locator('#tab-riwayat')).toHaveClass(/active/);
    const riwayatTable = page.locator('#table-riwayat-resep');
    await expect(riwayatTable).toBeVisible();

    const detailButtons = page.locator('.btn-view-resep-detail');
    const completedCount = await detailButtons.count();
    console.log('Jumlah resep selesai dalam tabel riwayat:', completedCount);
    expect(completedCount).toBeGreaterThan(0);

    // Buka Modal Rincian Resep pertama
    console.log('Membuka modal detail resep...');
    const firstRowToggle = page.locator('#table-riwayat-resep tbody tr').first().locator('.compact-action-toggle');
    if (await firstRowToggle.isVisible()) {
      await firstRowToggle.click();
      await page.waitForTimeout(200);
    }
    await detailButtons.first().click({ force: true });
    await page.waitForTimeout(800);

    const modal = page.locator('#modalDetailResep');
    await expect(modal).toBeVisible();
    await expect(page.locator('#modal-detail-content')).toBeVisible();

    // Verifikasi isi modal
    const mdlPatient = await page.locator('#mdl-patient-name').innerText();
    const mdlDoctor = await page.locator('#mdl-doctor-name').innerText();
    const mdlPrescNo = await page.locator('#mdl-presc-no').innerText();
    console.log(`Modal detail: Pasien=${mdlPatient}, Dokter=${mdlDoctor}, No=${mdlPrescNo}`);
    expect(mdlPatient.length).toBeGreaterThan(0);

    // Verifikasi tabel obat dalam modal tidak memiliki table-striped
    const modalTableStriped = await page.locator('#modalDetailResep table.table-striped').count();
    expect(modalTableStriped).toBe(0);

    // Ambil screenshot modal detail resep
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-04-modal-detail.png',
    });

    // Uji Dark Mode pada Modal
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.documentElement.classList.add('dark-mode');
    });
    await page.waitForTimeout(300);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-05-modal-detail-dark.png',
    });

    // Tutup modal
    await page.click('#modalDetailResep .close, #modalDetailResep button[data-dismiss="modal"]');
    await page.waitForTimeout(400);

    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.documentElement.classList.remove('dark-mode');
    });

    // ------------------------------------------------------------------------
    // STAGE 5: UJI 3 DOKUMEN CETAK RESEP
    // ------------------------------------------------------------------------
    console.log('--- STAGE 5: Uji 3 Dokumen Cetak Resep ---');
    // Ambil ID resep selesai dari baris pertama
    const firstDetailBtn = detailButtons.first();
    const prescId = await firstDetailBtn.getAttribute('data-id');
    console.log(`Menguji cetak dokumen untuk Resep ID: ${prescId}`);

    // 1. Dokumen 1: Cetak E-Tiket Obat (/apotek/cetak-etiket/{id})
    console.log('Navigasi ke /apotek/cetak-etiket/' + prescId);
    await page.goto(`apotek/cetak-etiket/${prescId}`);
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('body')).toContainText(/ETIKET|ATURAN PAKAI|FARMASI|OBAT/i);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-06-cetak-etiket.png',
    });

    // 2. Dokumen 2: Cetak Struk POS Thermal 80mm (/apotek/cetak-struk-resep/{id})
    console.log('Navigasi ke /apotek/cetak-struk-resep/' + prescId);
    await page.goto(`apotek/cetak-struk-resep/${prescId}`);
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('body')).toContainText(/BUKTI PENYERAHAN|STRUK|RESEP|APOTEK/i);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-07-cetak-struk-resep.png',
    });

    // 3. Dokumen 3: Cetak Kwitansi Resmi Pembayaran (/apotek/cetak-kwitansi-resep/{id})
    console.log('Navigasi ke /apotek/cetak-kwitansi-resep/' + prescId);
    await page.goto(`apotek/cetak-kwitansi-resep/${prescId}`);
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('body')).toContainText(/KWITANSI|PEMBAYARAN|SAWAMAWA/i);
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/apotek-08-cetak-kwitansi-resep.png',
    });

    // ------------------------------------------------------------------------
    // STAGE 6: VERIFIKASI ZERO CONSOLE ERRORS
    // ------------------------------------------------------------------------
    console.log('Console Errors caught:', consoleErrors);
    // Abaikan error favicon jika ada
    const realErrors = consoleErrors.filter(e => !e.includes('favicon.ico'));
    expect(realErrors.length).toBe(0);

    console.log('=== SEMUA UJI COBA APOTEK RESEP BERHASIL DENGAN SEMPURNA! ===');
  });
});
