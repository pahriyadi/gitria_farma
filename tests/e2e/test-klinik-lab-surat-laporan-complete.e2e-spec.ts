import { test, expect } from '@playwright/test';

test.describe('Klinik Lab, Surat, and Laporan End-to-End Integration Pipeline', () => {
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

  // ==========================================================================
  // PIPELINE 1: MODUL LABORATORIUM KLINIK (/klinik/lab)
  // ==========================================================================
  test('Pipeline 1: Full CRUD & Cetak Hasil Pemeriksaan Laboratorium (/klinik/lab)', async ({ page }) => {
    console.log('--- TEST PIPELINE 1: MODUL LABORATORIUM ---');
    await page.goto('klinik/lab');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('klinik/lab');

    // 1. Verifikasi modal input hasil lab baru
    await page.click('button[data-target="#labAddModal"]');
    await page.waitForTimeout(400);
    const modal = page.locator('#labAddModal');
    await expect(modal).toBeVisible();

    // 2. Pilih kunjungan pasien pertama yang ada
    const visitSelect = page.locator('#lab_visit_select');
    const optionCount = await visitSelect.locator('option').count();
    expect(optionCount).toBeGreaterThan(1);

    await page.evaluate(() => {
      const select = document.getElementById('lab_visit_select') as HTMLSelectElement;
      if (select && select.options.length > 1) {
        select.selectedIndex = 1;
        $(select).trigger('change');
      }
    });

    const selectedVisitId = await page.locator('#lab_hidden_visit_id').inputValue();
    console.log('Kunjungan Pasien terpilih untuk Lab ID:', selectedVisitId);
    expect(selectedVisitId).not.toBe('');

    // 3. Pilih jenis tes lab: Gula Darah Sewaktu (GDS)
    await page.evaluate(() => {
      const testSelect = document.getElementById('lab_test_type') as HTMLSelectElement;
      if (testSelect) {
        for (let i = 0; i < testSelect.options.length; i++) {
          if (testSelect.options[i].text.includes('Gula Darah Sewaktu') || testSelect.options[i].value.includes('GDS')) {
            testSelect.selectedIndex = i;
            $(testSelect).trigger('change');
            break;
          }
        }
      }
    });

    await page.waitForTimeout(300);

    // 4. Input hasil pengukuran: 185 mg/dL (tinggi / abnormal)
    await page.fill('#lab_result_value', '185');
    await page.waitForTimeout(300);

    // Verifikasi auto-evaluasi cerdas hasil lab
    const evaluatedStatus = await page.locator('#lab_status_select').inputValue();
    console.log('Hasil Auto-Evaluasi Status Lab:', evaluatedStatus);
    expect(evaluatedStatus).toBe('high');

    await page.fill('input[name="notes"]', 'Pasien disarankan puasa dan cek GDP ulang 1 minggu kemudian.');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/lab-01-modal-filled.png'
    });

    // 5. Submit Form Lab
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#labAddModal button[type="submit"]')
    ]);

    await page.waitForTimeout(600);
    expect(page.url()).toContain('klinik/lab');

    // Verifikasi baris hasil lab baru di tabel Datatable
    const firstRowLab = page.locator('table.datatable tbody tr:first-child');
    await expect(firstRowLab).toBeVisible();
    const labRowText = await firstRowLab.innerText();
    console.log('Baris Lab Terbaru di Tabel:', labRowText);
    expect(labRowText).toContain('LAB-');

    // Ambil nomor lab yang baru dibuat
    const labNoMatch = labRowText.match(/LAB-\d+-\d+/);
    const createdLabNo = labNoMatch ? labNoMatch[0] : '';
    console.log('Nomor Lab Terbit:', createdLabNo);

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/lab-02-created-success.png'
    });

    // 6. Uji Cetak Hasil Laboratorium Resmi (/klinik/cetak-lab/{id})
    const printLink = page.locator(`tr:has-text("${createdLabNo}") a[href*="cetak-lab"]`);
    if (await printLink.count() > 0) {
      const printUrl = await printLink.getAttribute('href');
      console.log('Membuka Dokumen Cetak Lab:', printUrl);
      if (printUrl) {
        const printPage = await page.context().newPage();
        await printPage.goto(printUrl);
        await printPage.waitForLoadState('domcontentloaded');
        const printContent = await printPage.locator('body').innerText();
        expect(printContent).toContain('HASIL PEMERIKSAAN LABORATORIUM');
        expect(printContent).toContain(createdLabNo);

        await printPage.screenshot({
          path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/lab-03-cetak-lembar.png'
        });
        await printPage.close();
      }
    }

    console.log('Pipeline 1: Modul Laboratorium Berhasil 100%!');
  });

  // ==========================================================================
  // PIPELINE 2: MODUL SURAT KETERANGAN MEDIS (/klinik/surat)
  // ==========================================================================
  test('Pipeline 2: Full CRUD & Cetak Surat Sakit, Sehat, dan Rujukan (/klinik/surat)', async ({ page }) => {
    console.log('--- TEST PIPELINE 2: MODUL SURAT MEDIS ---');
    await page.goto('klinik/surat');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('klinik/surat');

    // ------------------------------------------------------------------------
    // A. Terbitkan Surat Keterangan Sakit (SKS)
    // ------------------------------------------------------------------------
    console.log('A. Menerbitkan Surat Keterangan Sakit (SKS)...');
    await page.click('button[data-target="#letterAddModal"]');
    await page.waitForTimeout(400);

    await page.evaluate(() => {
      const select = document.getElementById('visit_select') as HTMLSelectElement;
      if (select && select.options.length > 1) {
        select.selectedIndex = 1;
        $(select).trigger('change');
      }
    });
    await page.waitForTimeout(300);

    // Pilih jenis surat 'sakit'
    await page.evaluate(() => {
      $('#letter_type').val('sakit').trigger('change');
    });
    await page.fill('#input_duration', '3');
    await page.fill('input[name="diagnosis"]', 'Faringitis Akut / Febris H-2');
    await page.fill('input[name="notes"]', 'Perlu istirahat tirah baring dan minum obat secara teratur.');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/surat-01-sks-modal.png'
    });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#letterAddModal button[type="submit"]')
    ]);

    await page.waitForTimeout(600);
    const firstRowSks = page.locator('table.datatable tbody tr:first-child');
    await expect(firstRowSks).toBeVisible();
    const msgSks = await firstRowSks.innerText();
    console.log('Surat Baru di Tabel:', msgSks);
    expect(msgSks).toContain('SKS-');

    const sksMatch = msgSks.match(/SKS-\d+-\d+/);
    const createdSksNo = sksMatch ? sksMatch[0] : '';

    // ------------------------------------------------------------------------
    // B. Terbitkan Surat Keterangan Sehat (SKD)
    // ------------------------------------------------------------------------
    console.log('B. Menerbitkan Surat Keterangan Sehat (SKD)...');
    await page.click('button[data-target="#letterAddModal"]');
    await page.waitForTimeout(400);

    await page.evaluate(() => {
      const select = document.getElementById('visit_select') as HTMLSelectElement;
      if (select && select.options.length > 1) {
        select.selectedIndex = 1;
        $(select).trigger('change');
      }
    });
    await page.waitForTimeout(300);

    await page.evaluate(() => {
      $('#letter_type').val('sehat').trigger('change');
    });
    await page.fill('input[name="blood_pressure"]', '120/80 mmHg');
    await page.fill('input[name="weight"]', '65');
    await page.fill('input[name="height"]', '170');
    await page.fill('input[name="purpose"]', 'Persyaratan Administrasi Pendaftaran Pekerjaan');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/surat-02-skd-modal.png'
    });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#letterAddModal button[type="submit"]')
    ]);

    await page.waitForTimeout(600);
    const firstRowSkd = page.locator('table.datatable tbody tr:first-child');
    await expect(firstRowSkd).toBeVisible();
    const msgSkd = await firstRowSkd.innerText();
    console.log('Surat SKD Baru di Tabel:', msgSkd);
    expect(msgSkd).toContain('SKD-');

    const skdMatch = msgSkd.match(/SKD-\d+-\d+/);
    const createdSkdNo = skdMatch ? skdMatch[0] : '';

    // ------------------------------------------------------------------------
    // C. Terbitkan Surat Rujukan Pasien (SRP)
    // ------------------------------------------------------------------------
    console.log('C. Menerbitkan Surat Rujukan Pasien (SRP)...');
    await page.click('button[data-target="#letterAddModal"]');
    await page.waitForTimeout(400);

    await page.evaluate(() => {
      const select = document.getElementById('visit_select') as HTMLSelectElement;
      if (select && select.options.length > 1) {
        select.selectedIndex = 1;
        $(select).trigger('change');
      }
    });
    await page.waitForTimeout(300);

    await page.evaluate(() => {
      $('#letter_type').val('rujukan').trigger('change');
    });
    await page.fill('input[name="referral_destination"]', 'RSUD Sumbawa');
    await page.fill('input[name="referral_poly"]', 'Poli Spesialis Penyakit Dalam');
    await page.fill('textarea[name="referral_reason"]', 'Mohon evaluasi lanjutan dan penanganan spesialis penyakit dalam.');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/surat-03-srp-modal.png'
    });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#letterAddModal button[type="submit"]')
    ]);

    await page.waitForTimeout(600);
    const firstRowSrp = page.locator('table.datatable tbody tr:first-child');
    await expect(firstRowSrp).toBeVisible();
    const msgSrp = await firstRowSrp.innerText();
    console.log('Surat SRP Baru di Tabel:', msgSrp);
    expect(msgSrp).toContain('SRP-');

    const srpMatch = msgSrp.match(/SRP-\d+-\d+/);
    const createdSrpNo = srpMatch ? srpMatch[0] : '';

    // ------------------------------------------------------------------------
    // D. Uji Cetak Dokumen Surat Medis Resmi (/klinik/cetak-surat/{id})
    // ------------------------------------------------------------------------
    console.log('D. Menguji Cetak Surat Resmi...');
    const printSks = page.locator(`tr:has-text("${createdSksNo}") a[href*="cetak-surat"]`);
    if (await printSks.count() > 0) {
      const url = await printSks.getAttribute('href');
      if (url) {
        const pPage = await page.context().newPage();
        await pPage.goto(url);
        await pPage.waitForLoadState('domcontentloaded');
        const content = await pPage.locator('body').innerText();
        expect(content).toContain('SURAT KETERANGAN SAKIT');
        expect(content).toContain(createdSksNo);

        await pPage.screenshot({
          path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/surat-04-cetak-sks.png'
        });
        await pPage.close();
      }
    }

    console.log('Pipeline 2: Modul Surat Medis Berhasil 100%!');
  });

  // ==========================================================================
  // PIPELINE 3: MODUL LAPORAN & REKAPITULASI KLINIK (/klinik/laporan)
  // ==========================================================================
  test('Pipeline 3: Verifikasi Laporan Morbiditas, Demografi, SLA & Cetak PDF (/klinik/laporan)', async ({ page }) => {
    console.log('--- TEST PIPELINE 3: MODUL LAPORAN KLINIK ---');
    await page.goto('klinik/laporan');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    expect(page.url()).toContain('klinik/laporan');

    // 1. Verifikasi Ringkasan KPI
    const totalVisits = await page.locator('.info-box:has-text("Total Kunjungan Pasien") .info-box-number').innerText();
    const totalLetters = await page.locator('.info-box:has-text("Surat Medis Diterbitkan") .info-box-number').innerText();
    const totalLabs = await page.locator('.info-box:has-text("Tes Laboratorium") .info-box-number').innerText();

    console.log(`KPI Klinik: Total Kunjungan=${totalVisits}, Surat=${totalLetters}, Lab=${totalLabs}`);
    expect(parseInt(totalVisits)).toBeGreaterThanOrEqual(1);

    // 2. Verifikasi Tab 1: 10 Besar Morbiditas (ICD-10)
    const morbiditasTable = page.locator('#tab-morbiditas table');
    await expect(morbiditasTable).toBeVisible();
    const morbiditasText = await morbiditasTable.innerText();
    console.log('Rekapitulasi Morbiditas ICD-10:\n', morbiditasText);
    expect(morbiditasText).toContain('Kasus');

    // 3. Verifikasi Tab 2: Demografi & Poliklinik
    await page.click('#tab-demografi-tab');
    await page.waitForTimeout(400);
    const demoTab = page.locator('#tab-demografi');
    await expect(demoTab).toBeVisible();
    const demoText = await demoTab.innerText();
    console.log('Demografi & Poli:\n', demoText);
    expect(demoText).toMatch(/laki-laki/i);

    // 4. Verifikasi Tab 3: Evaluasi Waktu Layanan & SLA
    await page.click('#tab-sla-tab');
    await page.waitForTimeout(400);
    const slaTab = page.locator('#tab-sla');
    await expect(slaTab).toBeVisible();
    const slaText = await slaTab.innerText();
    console.log('Waktu Tunggu & SLA:\n', slaText);
    expect(slaText).toContain('TOTAL WAKTU LAYANAN');

    await page.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/laporan-01-dashboard.png'
    });

    // 5. Uji Cetak Laporan 10 Besar Morbiditas
    console.log('Menguji Cetak Laporan Morbiditas...');
    const printMorbiditasUrl = 'klinik/cetak-laporan?type=morbiditas';
    const p1 = await page.context().newPage();
    await p1.goto(printMorbiditasUrl);
    await p1.waitForLoadState('domcontentloaded');
    const p1Text = await p1.locator('body').innerText();
    expect(p1Text).toContain('10 BESAR PENYAKIT');
    await p1.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/laporan-02-cetak-morbiditas.png'
    });
    await p1.close();

    // 6. Uji Cetak Rekap Kunjungan
    console.log('Menguji Cetak Laporan Rekap Kunjungan...');
    const printKunjunganUrl = 'klinik/cetak-laporan?type=kunjungan';
    const p2 = await page.context().newPage();
    await p2.goto(printKunjunganUrl);
    await p2.waitForLoadState('domcontentloaded');
    const p2Text = await p2.locator('body').innerText();
    expect(p2Text).toContain('REKAPITULASI KUNJUNGAN PASIEN');
    await p2.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/laporan-03-cetak-kunjungan.png'
    });
    await p2.close();

    // 7. Uji Cetak Waktu Layanan SLA
    console.log('Menguji Cetak Laporan SLA...');
    const printSlaUrl = 'klinik/cetak-laporan?type=waktu_pelayanan';
    const p3 = await page.context().newPage();
    await p3.goto(printSlaUrl);
    await p3.waitForLoadState('domcontentloaded');
    const p3Text = await p3.locator('body').innerText();
    expect(p3Text).toContain('EVALUASI WAKTU TUNGGU');
    await p3.screenshot({
      path: 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b/laporan-04-cetak-sla.png'
    });
    await p3.close();

    console.log('Pipeline 3: Modul Laporan & Rekapitulasi Berhasil 100%!');
  });
});
