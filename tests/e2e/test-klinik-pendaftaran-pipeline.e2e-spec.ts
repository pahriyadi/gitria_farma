import { test, expect } from '@playwright/test';

test.describe('Klinik Pendaftaran & Integrated Clinical Pipeline Verification', () => {
  test.setTimeout(90000);

  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 950 });
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'expanded');
      // Prevent release popup interception during automated test
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    page.on('dialog', async dialog => {
      console.log('Dialog caught:', dialog.message());
      await dialog.accept();
    });

    // Login sebagai superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('Execute full clinical workflow pipeline: Pendaftaran -> Kartu -> Detail -> Kunjungan -> Antrean -> SOAP -> Kasir', async ({ page }) => {
    // ------------------------------------------------------------------------
    // STEP 1: AKSES /klinik/pendaftaran & DAFTARKAN PASIEN BARU (CRUD Create)
    // ------------------------------------------------------------------------
    await page.goto('klinik/pendaftaran');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Buka modal registrasi pasien baru
    await page.evaluate(() => {
      $('#registerPatientModal').modal('show');
    });
    await page.waitForTimeout(500);

    const modalAdd = page.locator('#registerPatientModal');
    await expect(modalAdd).toBeVisible();

    // Data pasien unik
    const uniqueSuffix = Math.floor(1000 + Math.random() * 9000).toString();
    const testNik = '520401010190' + uniqueSuffix;
    const testName = 'Pasien Integrasi E2E ' + uniqueSuffix;

    await page.fill('#registerPatientModal input[name="nik"]', testNik);
    await page.fill('#registerPatientModal input[name="name"]', testName);
    await page.evaluate(() => {
      $('#registerPatientModal select[name="gender"]').val('L').trigger('change');
      $('#registerPatientModal select[name="membership_tier"]').val('gold').trigger('change');
    });
    await page.fill('#registerPatientModal input[name="place_of_birth"]', 'Sumbawa');
    await page.fill('#registerPatientModal input[name="date_of_birth"]', '1990-01-01');
    await page.fill('#registerPatientModal input[name="phone"]', '0812' + uniqueSuffix + '99');
    await page.fill('#registerPatientModal textarea[name="address"]', 'Jl. Garuda No. 88, Sumbawa');
    await page.fill('#registerPatientModal input[name="allergies"]', 'Alergi Penisilin');
    await page.fill('#registerPatientModal input[name="emergency_contact_name"]', 'Istri Pasien');
    await page.fill('#registerPatientModal input[name="emergency_contact_phone"]', '081234567800');

    // Submit form pendaftaran
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#registerPatientModal button[type="submit"]')
    ]);

    await page.waitForTimeout(600);
    expect(page.url()).toContain('klinik/pendaftaran');

    // Screenshot hasil pendaftaran
    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-pasien-baru-sukses.png'
    });

    // ------------------------------------------------------------------------
    // STEP 2: CEK KARTU IDENTITAS PASIEN (Inter-Page Navigation)
    // ------------------------------------------------------------------------
    // Dapatkan ID pasien atau link kartu dari flash card atau tabel
    const flashCard = page.locator('.card:has-text("Pasien Baru Berhasil Terdaftar!")');
    let kartuHref = '';

    if (await flashCard.count() > 0) {
      const btnKartu = flashCard.locator('a[href*="klinik/kartu-pasien"]').first();
      kartuHref = await btnKartu.getAttribute('href') || '';
    }

    if (!kartuHref) {
      // Cari pasien di tabel untuk mendapatkan link kartu
      await page.fill('#table-patients_filter input', testName);
      await page.waitForTimeout(600);
      const rowToggle = page.locator('#table-patients tbody tr').first().locator('.compact-action-toggle');
      if (await rowToggle.isVisible()) {
        await rowToggle.click();
        await page.waitForTimeout(300);
      }
      const kartuLinkInRow = page.locator('#table-patients a[href*="klinik/kartu-pasien"], .compact-action-menu a[href*="klinik/kartu-pasien"]').first();
      kartuHref = await kartuLinkInRow.getAttribute('href') || '';
    }

    console.log('Opening Kartu Pasien at:', kartuHref);
    expect(kartuHref).toContain('klinik/kartu-pasien');

    await page.goto(kartuHref);
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Verifikasi konten kartu identitas pasien (case-insensitive)
    expect(page.url()).toContain('klinik/kartu-pasien');
    const kartuContent = await page.locator('body').innerText();
    expect(kartuContent.toUpperCase()).toContain(testName.toUpperCase());

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-kartu-pasien.png'
    });

    // ------------------------------------------------------------------------
    // STEP 3: CEK DETAIL IDENTITAS PASIEN MODAL (#modalPatientDetail)
    // ------------------------------------------------------------------------
    await page.goto('klinik/pendaftaran');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Cari pasien pada tabel
    await page.fill('#table-patients_filter input', testName);
    await page.waitForTimeout(700);

    // Buka detail identitas pasien via link nama pasien
    const patientNameLink = page.locator('#table-patients a.btn-show-patient-detail', { hasText: testName }).first();
    await patientNameLink.click();
    await page.waitForTimeout(700);

    const detailModal = page.locator('#modalPatientDetail');
    await expect(detailModal).toBeVisible();
    const detailContent = await detailModal.innerText();
    expect(detailContent.toUpperCase()).toContain(testName.toUpperCase());
    expect(detailContent).toContain('Alergi Penisilin');
    expect(detailContent).toContain('Gold');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-detail-pasien.png'
    });

    await page.locator('#modalPatientDetail button[data-dismiss="modal"]').first().click();
    await page.waitForTimeout(400);

    // ------------------------------------------------------------------------
    // STEP 4: DAFTARKAN KUNJUNGAN & ANTREAN POLI (#visitModal)
    // ------------------------------------------------------------------------
    const rowToggle = page.locator('#table-patients tbody tr').first().locator('.compact-action-toggle');
    if (await rowToggle.isVisible()) {
      await rowToggle.click();
      await page.waitForTimeout(300);
    }
    const visitBtn = page.locator('#table-patients .btn-visit, .compact-action-menu .btn-visit').first();
    await visitBtn.click();
    await page.waitForTimeout(600);

    const visitModal = page.locator('#visitModal');
    await expect(visitModal).toBeVisible();
    await expect(visitModal.locator('#card-patient-name')).toContainText(testName);

    // Pilih Poliklinik & Dokter
    await page.evaluate(() => {
      const poly = $('#modal-polyclinic-id option[value!=""]:first').val();
      $('#modal-polyclinic-id').val(poly).trigger('change');
    });
    await page.waitForTimeout(400);

    await page.evaluate(() => {
      const doc = $('#modal-doctor-id option[value!=""]:first').val();
      if (doc) $('#modal-doctor-id').val(doc).trigger('change');
    });
    await page.waitForTimeout(200);

    // Isi Keluhan Utama
    await page.fill('#modal-complaint', 'Nyeri dada sebelah kiri dan sesak napas');

    // Submit registrasi kunjungan
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#btn-submit-visit')
    ]);

    await page.waitForTimeout(800);
    expect(page.url()).toContain('klinik/pendaftaran');

    // Verifikasi pesan sukses kunjungan & modal tiket antrean thermal
    const visitSuccessCard = page.locator('.card:has-text("Kunjungan Berhasil Didaftarkan!")');
    await expect(visitSuccessCard).toBeVisible();

    const ticketModal = page.locator('#modalQueueTicket');
    if (await ticketModal.isVisible()) {
      await page.screenshot({
        path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-tiket-antrean.png'
      });
      await page.click('#modalQueueTicket button[data-dismiss="modal"]');
      await page.waitForTimeout(400);
    }

    // ------------------------------------------------------------------------
    // STEP 5: VERIFIKASI MONITOR ANTREAN KLINIK (/klinik/antrean)
    // ------------------------------------------------------------------------
    await page.goto('klinik/antrean');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    expect(page.url()).toContain('klinik/antrean');
    const antreanContent = await page.locator('body').innerText();
    expect(antreanContent.toUpperCase()).toContain(testName.toUpperCase());

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-pipeline-antrean.png'
    });

    // ------------------------------------------------------------------------
    // STEP 6: VERIFIKASI REKAM MEDIS & SOAP DOKTER (/klinik/soap)
    // ------------------------------------------------------------------------
    await page.goto('klinik/soap');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    expect(page.url()).toContain('klinik/soap');
    const soapContent = await page.locator('body').innerText();
    expect(soapContent.toUpperCase()).toContain(testName.toUpperCase());

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-pipeline-soap.png'
    });

    // ------------------------------------------------------------------------
    // STEP 7: VERIFIKASI KASIR TERPADU (/keuangan/kasir)
    // ------------------------------------------------------------------------
    await page.goto('keuangan/kasir');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    expect(page.url()).toContain('keuangan/kasir');
    const kasirContent = await page.locator('body').innerText();
    expect(kasirContent.toUpperCase()).toContain(testName.toUpperCase());

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/pendaftaran-pipeline-kasir.png'
    });

    console.log('ALL 7 PIPELINE STAGES VERIFIED 100% SUCCESFULLY!');
  });
});
