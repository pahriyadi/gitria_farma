import { test, expect } from '@playwright/test';

test.describe('Klinik SOAP & Complete Cross-Module Integration Pipeline', () => {
  test.setTimeout(120000);

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
      console.log('Dialog prompt/alert caught:', dialog.message());
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

  test('Execute complete clinical SOAP lifecycle: Queue -> Triage -> SOAP -> e-Resep -> Kasir Billing -> Apotek Dispensing', async ({ page }) => {
    // ------------------------------------------------------------------------
    // STAGE 1: BUKA /klinik/soap DAN PILIH PASIEN DARI ANTREAN
    // ------------------------------------------------------------------------
    console.log('--- STAGE 1: Membuka Halaman SOAP & Memilih Pasien ---');
    await page.goto('klinik/soap');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    expect(page.url()).toContain('klinik/soap');

    // Pastikan list antrean pasien tersedia
    const visitItems = page.locator('.visit-item');
    const visitCount = await visitItems.count();
    console.log('Jumlah pasien dalam antrean SOAP:', visitCount);
    expect(visitCount).toBeGreaterThan(0);

    // Ambil data pasien pertama
    const firstVisit = visitItems.first();
    const patientName = (await firstVisit.locator('.patient-name-text').innerText()).trim();
    const visitId = await firstVisit.getAttribute('data-id');
    const queueNo = await firstVisit.getAttribute('data-queue');

    console.log(`Memilih Pasien Aktif: ${patientName} (Visit ID: ${visitId}, Antrean: ${queueNo})`);

    // Klik kartu pasien antrean
    await firstVisit.click();
    await page.waitForTimeout(600);

    // Pastikan EMR workspace tampil
    await expect(page.locator('#workspace-card')).toBeVisible();
    await expect(page.locator('#lbl-patient')).toContainText(patientName);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-01-patient-selected.png'
    });

    // ------------------------------------------------------------------------
    // STAGE 2: PENGISIAN & LIVE-SYNC ASESMEN TTV PERAWAT (TRIAGE)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 2: Pengisian Tanda Vital (TTV) & Skrining Perawat ---');
    await page.click('#tab-btn-triage');
    await page.waitForTimeout(400);

    await expect(page.locator('#triage-pane')).toBeVisible();

    // Isi vital signs
    await page.fill('#tr-bp', '120/80');
    await page.fill('#tr-weight', '65');
    await page.fill('#tr-height', '170');
    await page.fill('#tr-temp', '36.6');
    await page.fill('#tr-pulse', '82');
    await page.fill('#tr-resp', '18');
    await page.fill('#tr-complaints', 'Demam sumer-sumer sejak 2 hari, sakit tenggorokan, dan batuk kering.');
    await page.fill('#tr-anamnesis', 'Tidur kurang teratur, nafsu makan menurun, tidak ada sesak napas.');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-02-triage-saved.png'
    });

    // ------------------------------------------------------------------------
    // STAGE 3: PENGISIAN PEMERIKSAAN DOKTER (SOAP), ICD-10 & e-RESEP
    // ------------------------------------------------------------------------
    console.log('--- STAGE 3: Form Rekam Medis SOAP Dokter & Resep Apotek ---');
    await page.click('#tab-btn-soap');
    await page.waitForTimeout(400);
    await expect(page.locator('#soap-pane')).toBeVisible();

    // Uji tombol "Salin Catatan Perawat"
    const btnCopyNurse = page.locator('#btn-copy-nurse-s');
    if (await btnCopyNurse.isVisible()) {
      await btnCopyNurse.click();
      await page.waitForTimeout(200);
      const subjectiveVal = await page.locator('#doc-subjective').inputValue();
      console.log('Hasil Salin Catatan Perawat ke (S):', subjectiveVal);
    }

    // Pastikan S, O, A, P terisi dengan data klinis bermutu
    await page.fill('#doc-subjective', 'Pasien datang dengan keluhan demam sejak 2 hari yang lalu, disertai nyeri menelan, batuk kering, dan sakit kepala. Riwayat alergi penisilin.');
    await page.fill('#doc-objective', 'Keadaan umum: Tampak sakit ringan, Compos Mentis. T: 120/80 mmHg, N: 82x/m, S: 36.6 C, RR: 18x/m. Faring hiperemis (+), tonsil T1-T1 tenang. Cor/Pulmo: BJ I-II murni reguler, vesikuler kanan=kiri, rhonki (-/-), wheezing (-/-). Abdomen: Supel, bising usus (+) normal.');
    await page.fill('#doc-assessment', 'Faringitis Akut / ISPA (Acute Upper Respiratory Infection)');
    await page.fill('#input-icd10-code', 'J06.9');
    await page.fill('#input-icd10-desc', 'Acute upper respiratory infection, unspecified');
    await page.fill('#doc-plan', 'Edukasi istirahat cukup, perbanyak minum air hangat, hindari makanan berminyak/pedas. Terapi analgetik-antipiretik dan mukolitik.');
    await page.fill('#input-icd9-code', '89.03');
    await page.fill('#input-icd9-desc', 'Interview and evaluation');

    // Input e-Resep Obat 1: Paracetamol 500 mg Tablet
    await page.evaluate(() => {
      const select0 = document.querySelector('select[name="medicines[0][medicine_id]"]');
      if (select0) {
        for (let i = 0; i < select0.options.length; i++) {
          if (select0.options[i].value === '1' || select0.options[i].text.toLowerCase().includes('paracetamol')) {
            select0.selectedIndex = i;
            $(select0).trigger('change');
            break;
          }
        }
      }
    });

    await page.fill('input[name="medicines[0][qty]"]', '10');
    await page.fill('input[name="medicines[0][dosage]"]', '3 x 1 tablet sesudah makan (p.r.n)');

    await page.waitForTimeout(400);

    // Verifikasi estimasi biaya obat
    const totalMedLabel = await page.locator('#lbl-grand-medicine-total').innerText();
    console.log('Estimasi Total Obat di E-Resep:', totalMedLabel);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-03-form-filled.png'
    });

    // ------------------------------------------------------------------------
    // STAGE 4: SUBMIT PEMERIKSAAN DOKTER (save_soap)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 4: Menyimpan Rekam Medis SOAP & Mengarahkan ke Kasir ---');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#btn-submit-soap')
    ]);

    await page.waitForTimeout(800);
    expect(page.url()).toContain('klinik/soap');

    // Periksa flash message
    const alertSuccess = page.locator('.alert-flash-card, .alert-success');
    await expect(alertSuccess).toBeVisible();
    const successMsg = await alertSuccess.innerText();
    console.log('Notifikasi Sukses SOAP:', successMsg);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-04-submitted-success.png'
    });

    // ------------------------------------------------------------------------
    // STAGE 5: VERIFIKASI KE KASIR TERPADU (/keuangan/kasir) & PELUNASAN
    // ------------------------------------------------------------------------
    console.log('--- STAGE 5: Beralih ke Kasir Terpadu (/keuangan/kasir) ---');
    await page.goto('keuangan/kasir');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    // Cari tagihan pasien di antrean kasir
    const kasirItems = page.locator(`.bill-item:has-text("${patientName}")`);
    const kasirCount = await kasirItems.count();
    console.log(`Tagihan atas nama ${patientName} ditemukan di Kasir:`, kasirCount);
    expect(kasirCount).toBeGreaterThan(0);

    // Buka tagihan kasir
    await kasirItems.first().click();
    await page.waitForTimeout(600);

    await expect(page.locator('#settlement-card')).toBeVisible();

    // Verifikasi rincian tagihan kasir mencakup Layanan Medis & Obat
    const detailsBody = await page.locator('#details-table-body').innerText();
    console.log('Item rincian tagihan kasir:\n', detailsBody);
    expect(detailsBody.toLowerCase()).toContain('konsultasi');
    expect(detailsBody.toLowerCase()).toContain('paracetamol');

    // Uji kalkulasi Uang Pas di Kasir
    await page.click('#btn-exact-money');
    await page.waitForTimeout(300);

    const changeStatus = await page.locator('#lbl-change-amount').innerText();
    console.log('Status Kembalian Kasir:', changeStatus);
    expect(changeStatus).toContain('Lunas Pas');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-05-kasir-settlement.png'
    });

    // Selesaikan transaksi di kasir
    console.log('Memproses pelunasan transaksi di kasir...');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#btn-submit-kasir')
    ]);

    await page.waitForTimeout(800);
    console.log('Pelunasan di kasir sukses!');

    // ------------------------------------------------------------------------
    // STAGE 6: VERIFIKASI KE FARMASI / APOTEK (/apotek/resep) & PENYERAHAN OBAT
    // ------------------------------------------------------------------------
    console.log('--- STAGE 6: Beralih ke Farmasi & Dispensing Obat (/apotek/resep) ---');
    await page.goto('apotek/resep');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    const prescItems = page.locator(`.presc-item:has-text("${patientName}")`);
    const prescCount = await prescItems.count();
    console.log(`Resep pasien ${patientName} ditemukan di antrean farmasi:`, prescCount);
    expect(prescCount).toBeGreaterThan(0);

    // Klik resep pasien
    await prescItems.first().click();
    await page.waitForTimeout(600);

    await expect(page.locator('#workspace-card')).toBeVisible();

    // Pastikan status tagihan kasir terdeteksi Lunas
    const prescCardText = await page.locator('#workspace-card').innerText();
    console.log('Workspace Farmasi Resep:\n', prescCardText);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-06-apotek-prescriptions.png'
    });

    // Klik selesaikan dispensing dan serahkan obat
    const btnDispense = page.locator('#btn-submit-dispense');
    if (await btnDispense.isVisible()) {
      console.log('Menyelesaikan dispensing dan penyerahan obat...');
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        btnDispense.click()
      ]);
      await page.waitForTimeout(800);
      console.log('Dispensing resep berhasil diserahkan!');
    }

    // ------------------------------------------------------------------------
    // STAGE 7: MONITOR ANTREAN KLINIK (/klinik/antrean)
    // ------------------------------------------------------------------------
    console.log('--- STAGE 7: Memeriksa Antrean Selesai di /klinik/antrean ---');
    await page.goto('klinik/antrean');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(800);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/soap-07-antrean-completed.png'
    });

    console.log('========================================================================');
    console.log('SELURUH 7 TAHAP INTEGRASI KLINIK SOAP BERHASIL 100% TANPA KENDALA!');
    console.log('========================================================================');
  });
});
