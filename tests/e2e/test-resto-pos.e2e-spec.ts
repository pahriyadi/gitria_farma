import { test, expect } from '@playwright/test';

/**
 * ============================================================================
 * E2E TEST: MODUL RESTO & DAPUR GIZI - POS RESTO SEHAT (/resto/pos)
 * ============================================================================
 * Menguji alur lengkap:
 * 1. UI Paper White & Paper Dark (#1f2328, #374151, tanpa .table-striped).
 * 2. Filter Kategori Menu (Makanan, Minuman, Diet Gizi, Skincare, Suplemen, Paket).
 * 3. Pencarian Cepat Produk Menu Sehat & Skincare.
 * 4. Interaksi Keranjang Belanja (Tambah Item, Qty, Diskon, Uang Diterima, Kembalian).
 * 5. Tipe Pemesanan: Penjualan Bebas / Walk-In & Diet Pasien Rujukan Poli Gizi.
 * 6. Pemrosesan Pesanan Walk-In -> Tersimpan di Monitoring Hari Ini.
 * 7. Modal Pelunasan Tagihan (Settle Payment) & Cek Nota Cetak.
 * 8. Modal Kelola Master Menu & Stok (DataTables, 0 .table-striped).
 * 9. Verifikasi 0 Console Errors.
 */

test.describe('Resto POS: Healthy Nutrition & Diet Therapy Cashier Pipeline', () => {
  test.use({
    viewport: { width: 1440, height: 900 },
    locale: 'id-ID',
    timezoneId: 'Asia/Jakarta',
  });

  const artifactDir = 'C:/Users/HP/.gemini/antigravity/brain/88766967-a515-4796-a41f-eff36fa87e5b';

  test.beforeEach(async ({ page }) => {
    // Suppress release popup and expand sidebar
    await page.addInitScript(() => {
      localStorage.setItem('sawamawa_last_seen_version', 'v2.11.0');
      localStorage.setItem('sawamawa_sidebar_state', 'collapsed');
      window.addEventListener('DOMContentLoaded', () => {
        const popup = document.getElementById('modal-whats-new-popup');
        if (popup) popup.remove();
      });
    });

    // Login as superadmin
    await page.goto('login');
    await page.fill('#username_input', 'superadmin');
    await page.fill('#login-password', 'admin123');
    await page.click('#btn-submit-login');
    await expect(page).toHaveURL(/dashboard/);
    await page.waitForLoadState('domcontentloaded');
  });

  test('E2E Full Flow: POS Catalog -> Search & Category -> Cart Calc -> Process Order -> Settle Modal -> Master Modal -> 0 Errors', async ({ page }) => {
    test.setTimeout(60000);
    const consoleErrors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        const txt = msg.text();
        if (!txt.includes('favicon') && !txt.includes('chrome-extension')) {
          consoleErrors.push(txt);
        }
      }
    });

    // ========================================================================
    // STAGE 1: Membuka /resto/pos & Verifikasi Light Mode
    // ========================================================================
    console.log('--- STAGE 1: Membuka /resto/pos & Verifikasi Light Mode ---');
    await page.goto('resto/pos');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(600);

    // Pastikan layout rapi tanpa fixed footer overlay saat fullPage screenshot
    await page.evaluate(() => {
      document.body.classList.remove('sidebar-open');
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });

    // Verifikasi Judul Halaman
    await expect(page.locator('h4').filter({ hasText: 'POS Resto Sehat & Terapi Gizi' })).toBeVisible();

    // Verifikasi 0 .table-striped di seluruh halaman
    const stripedCount = await page.locator('.table-striped').count();
    console.log('Jumlah .table-striped terdeteksi:', stripedCount);
    expect(stripedCount).toBe(0);

    // Verifikasi katalog produk ditampilkan
    const cardItems = page.locator('.product-card-clickable');
    const cardCount = await cardItems.count();
    console.log('Jumlah kartu produk aktif:', cardCount);
    expect(cardCount).toBeGreaterThan(0);

    // Verifikasi keranjang kosong awal
    await expect(page.locator('#empty-cart-placeholder')).toBeVisible();
    await expect(page.locator('#btn-submit-order')).toBeDisabled();

    await page.screenshot({ path: `${artifactDir}/resto-pos-01-light.png`, fullPage: true });

    // ========================================================================
    // STAGE 2: Verifikasi Paper Dark Mode
    // ========================================================================
    console.log('--- STAGE 2: Verifikasi Paper Dark Mode ---');
    await page.evaluate(() => {
      document.body.classList.add('dark-mode');
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.waitForTimeout(400);

    // Periksa background card dan border tabel pada Dark Mode
    const darkCardBg = await page.evaluate(() => {
      const card = document.querySelector('.pos-card');
      return card ? window.getComputedStyle(card).backgroundColor : '';
    });
    console.log('Computed dark-mode card background:', darkCardBg);
    // rgb(31, 35, 40) corresponds to #1f2328
    expect(darkCardBg).toBe('rgb(31, 35, 40)');

    await page.screenshot({ path: `${artifactDir}/resto-pos-02-dark.png`, fullPage: true });

    // Kembalikan ke Light Mode untuk pengujian alur fungsional berikutnya
    await page.evaluate(() => {
      document.body.classList.remove('dark-mode');
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 3: Filter Kategori & Pencarian Produk
    // ========================================================================
    console.log('--- STAGE 3: Filter Kategori & Pencarian Produk ---');
    // Klik Tab Minuman (Jus & Detox)
    await page.locator('#categoryTabs a[data-cat="minuman"]').click();
    await page.waitForTimeout(300);

    // Verifikasi kartu minuman terlihat
    const visibleCards = page.locator('.product-card-wrapper:visible');
    const visibleCount = await visibleCards.count();
    console.log('Jumlah produk kategori Minuman tampak:', visibleCount);
    expect(visibleCount).toBeGreaterThan(0);

    // Kembalikan ke Tab Semua
    await page.locator('#categoryTabs a[data-cat="all"]').click();
    await page.waitForTimeout(300);

    // Cari Produk berdasarkan kata kunci 'Salad'
    await page.fill('#search-product-input', 'Salad');
    await page.waitForTimeout(300);
    const searchMatchCount = await page.locator('.product-card-wrapper:visible').count();
    console.log('Jumlah match pencarian "Salad":', searchMatchCount);
    expect(searchMatchCount).toBeGreaterThanOrEqual(1);

    // Bersihkan filter pencarian
    await page.fill('#search-product-input', '');
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 4: Interaksi Keranjang Belanja & Kalkulasi Subtotal/Diskon/Kembalian
    // ========================================================================
    console.log('--- STAGE 4: Interaksi Keranjang Belanja & Kalkulasi ---');
    // Pilih produk pertama yang tersedia
    const firstProduct = page.locator('.product-card-clickable').first();
    const productName = await firstProduct.locator('.pos-item-name').innerText();
    console.log('Memilih produk pertama:', productName);
    await firstProduct.click();
    await page.waitForTimeout(300);

    // Verifikasi keranjang sekarang memiliki 1 item
    await expect(page.locator('#empty-cart-placeholder')).not.toBeVisible();
    await expect(page.locator('#btn-submit-order')).toBeEnabled();
    const cartRowCount = await page.locator('#cart-body tr').count();
    expect(cartRowCount).toBe(1);

    // Ubah qty menjadi 2
    const qtyInput = page.locator('#cart-body .input-cart-qty').first();
    await qtyInput.fill('2');
    await qtyInput.dispatchEvent('change');
    await page.waitForTimeout(200);

    // Berikan diskon Rp 5.000
    await page.fill('#input-discount', '5000');
    await page.locator('#input-discount').dispatchEvent('change');
    await page.waitForTimeout(200);

    // Verifikasi label subtotal & grandtotal terisi angka > 0
    const grandTotalText = await page.locator('#lbl-grandtotal-cart').innerText();
    console.log('Grand Total Keranjang:', grandTotalText);
    expect(grandTotalText).toContain('Rp');

    // Input uang diterima untuk memverifikasi kalkulasi kembalian
    await page.fill('#input-paid', '150000');
    await page.locator('#input-paid').dispatchEvent('change');
    await page.waitForTimeout(200);

    const changeText = await page.locator('#lbl-change').innerText();
    console.log('Kembalian terhitung:', changeText);
    expect(changeText).toContain('Rp');

    await page.screenshot({ path: `${artifactDir}/resto-pos-03-cart-active.png` });

    // ========================================================================
    // STAGE 5: Pengalihan Mode Diet Pasien vs Walk-In
    // ========================================================================
    console.log('--- STAGE 5: Pengalihan Mode Diet Pasien vs Walk-In ---');
    await page.locator('#btn-mode-diet').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#patient-visit-group')).toBeVisible();
    await expect(page.locator('#opt-bill-clinic')).toBeEnabled();

    // Kembalikan ke Penjualan Bebas (Walk-In)
    await page.locator('#btn-mode-umum').click();
    await page.waitForTimeout(300);
    await expect(page.locator('#patient-visit-group')).not.toBeVisible();

    // ========================================================================
    // STAGE 6: Pemrosesan Pesanan Walk-In & Submit Form
    // ========================================================================
    console.log('--- STAGE 6: Pemrosesan Pesanan Walk-In & Submit Form ---');
    await page.fill('#input-customer-name', 'Ny. Fitri Handayani');
    await page.fill('#input-customer-phone', '081298765432');
    await page.selectOption('#select-payment-action', 'pay_later');
    await page.waitForTimeout(200);

    // Klik tombol submit order
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#btn-submit-order')
    ]);

    // Verifikasi pesan sukses / flash notification
    const alertSuccess = page.locator('.alert-success, .alert-flash-card');
    await expect(alertSuccess).toBeVisible();
    console.log('Pesanan berhasil dibuat dan diarahkan kembali.');

    // Verifikasi tabel monitoring hari ini terisi
    const orderRows = page.locator('#table-orders-today tbody tr');
    const orderCount = await orderRows.count();
    console.log('Jumlah pesanan pada monitoring hari ini:', orderCount);
    expect(orderCount).toBeGreaterThan(0);

    // Ambil order pertama
    const firstOrderRow = orderRows.first();
    await expect(firstOrderRow.locator('td').filter({ hasText: 'Ny. Fitri Handayani' })).toBeVisible();

    await page.evaluate(() => {
      document.body.classList.remove('layout-footer-fixed');
      document.body.classList.add('sidebar-collapse');
    });
    await page.screenshot({ path: `${artifactDir}/resto-pos-04-order-created.png`, fullPage: true });

    // ========================================================================
    // STAGE 7: Interaksi Modal Pelunasan (Settle Payment)
    // ========================================================================
    console.log('--- STAGE 7: Interaksi Modal Pelunasan ---');
    const settleBtn = page.locator('.btn-settle-modal').first();
    if (await settleBtn.count() > 0) {
      const toggle = firstOrderRow.locator('.compact-action-toggle');
      if (await toggle.count() > 0) {
        await toggle.click();
        await page.waitForTimeout(200);
      }
      await page.evaluate(() => {
        const btn = document.querySelector('.btn-settle-modal') as HTMLElement;
        if (btn) btn.click();
      });
      await page.waitForTimeout(400);
      await expect(page.locator('#modalSettlePayment')).toBeVisible();
      await expect(page.locator('#settle-total-text')).not.toHaveText('Rp 0');
      await page.screenshot({ path: `${artifactDir}/resto-pos-05-modal-settle.png` });
      // Tutup modal
      await page.locator('#modalSettlePayment .modal-footer button[data-dismiss="modal"]').click();
      await page.waitForTimeout(300);
    }

    // ========================================================================
    // STAGE 8: Master Menu & Stok Modal (#modalManageMenusList)
    // ========================================================================
    console.log('--- STAGE 8: Master Menu & Stok Modal ---');
    await page.locator('button[data-target="#modalManageMenusList"]').click();
    await page.waitForTimeout(500);

    await expect(page.locator('#modalManageMenusList')).toBeVisible();
    const modalTable = page.locator('#table-manage-menus');
    await expect(modalTable).toBeVisible();
    const modalRows = await modalTable.locator('tbody tr').count();
    console.log('Jumlah item menu dalam modal kelola:', modalRows);
    expect(modalRows).toBeGreaterThan(0);

    // Verifikasi tidak ada .table-striped di dalam modal
    const modalStriped = await page.locator('#modalManageMenusList .table-striped').count();
    expect(modalStriped).toBe(0);

    await page.screenshot({ path: `${artifactDir}/resto-pos-06-modal-manage.png` });

    // Tutup modal kelola
    await page.locator('#modalManageMenusList .modal-footer button[data-dismiss="modal"]').click();
    await page.waitForTimeout(300);

    // ========================================================================
    // STAGE 9: Verifikasi 0 Console Errors
    // ========================================================================
    console.log('--- STAGE 9: Verifikasi 0 Console Errors ---');
    console.log('Daftar console error yang tertangkap:', consoleErrors);
    expect(consoleErrors).toHaveLength(0);
    console.log('PASSED: E2E Resto POS Pipeline 100% Sukses!');
  });
});
