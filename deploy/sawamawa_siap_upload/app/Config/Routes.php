<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public / Landing Page, Online Registration & Offline Kiosk APM / TV Display
$routes->get('/', 'Home::index');
$routes->match(['GET', 'POST'], 'daftar-online', 'Home::daftarOnline');
$routes->get('daftar-online/sukses/(:num)', 'Home::suksesDaftar/$1');
$routes->post('api/check-patient-public', 'Home::checkPatientPublic');
$routes->get('berita', 'Home::berita');
$routes->get('berita/(:segment)', 'Home::berita/$1');

// Layar Display TV & Kiosk Mandiri (Bebas Session Timeout)
$routes->get('klinik/display', 'Klinik::display');
$routes->post('klinik/save-tv-media', 'Klinik::saveTvMedia');
$routes->get('klinik/kiosk', 'Klinik::kiosk');
$routes->post('klinik/kiosk-check-patient', 'Klinik::kioskCheckPatient');
$routes->post('klinik/kiosk-register-visit', 'Klinik::kioskRegisterVisit');
$routes->post('klinik/kiosk-register-new-patient', 'Klinik::kioskRegisterNewPatient');

$routes->match(['GET', 'POST'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->match(['GET', 'POST'], 'api/backup/cron', 'System::cronBackup');
$routes->get('api/system/version', 'System::systemVersion');

// Secure Routes (Using Auth Filter)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Klinik
    $routes->match(['GET', 'POST'], 'api/spotlight-search', 'Klinik::spotlightSearch');
    $routes->match(['GET', 'POST'], 'klinik/pendaftaran', 'Klinik::pendaftaran');
    $routes->get('klinik/search-patients-ajax', 'Klinik::searchPatientsAjax');
    $routes->post('klinik/toggle-online-registration', 'Klinik::toggleOnlineRegistration');
    $routes->match(['GET', 'POST'], 'klinik/antrean', 'Klinik::antrean');
    $routes->get('klinik/antrean/delete/(:num)', 'Klinik::deleteQueueDirect/$1');
    $routes->match(['GET', 'POST'], 'klinik/soap', 'Klinik::soap');
    $routes->match(['GET', 'POST'], 'klinik/rekam-medis', 'Klinik::soap');
    $routes->post('klinik/satusehat-sync/(:num)', 'Klinik::syncSatuSehat/$1');
    $routes->get('klinik/satusehat-logs/(:num)', 'Klinik::getSatuSehatLogs/$1');
    $routes->match(['GET', 'POST'], 'klinik/surat', 'Klinik::surat');
    $routes->get('klinik/cetak-surat/(:num)', 'Klinik::cetakSurat/$1');
    $routes->match(['GET', 'POST'], 'klinik/lab', 'Klinik::lab');
    $routes->get('klinik/cetak-lab/(:num)', 'Klinik::cetakLab/$1');
    $routes->get('klinik/kartu-pasien/(:num)', 'Klinik::kartuPasien/$1');
    $routes->get('klinik/pasien/(:num)', 'Klinik::detailPasien/$1');
    $routes->get('klinik/detail-pasien/(:num)', 'Klinik::detailPasien/$1');
    $routes->get('klinik/patient-rme-json/(:num)', 'Klinik::getPatientRmeHistoryJson/$1');
    $routes->get('klinik/cetak-ringkasan-rme/(:num)', 'Klinik::cetakRingkasanRme/$1');
    $routes->post('klinik/save-informed-consent', 'Klinik::saveInformedConsent');
    $routes->get('klinik/delete-informed-consent/(:num)', 'Klinik::deleteInformedConsent/$1');
    $routes->get('klinik/cetak-informed-consent/(:num)', 'Klinik::cetakInformedConsent/$1');
    $routes->post('klinik/save-medical-photo', 'Klinik::saveMedicalPhoto');
    $routes->get('klinik/delete-medical-photo/(:num)', 'Klinik::deleteMedicalPhoto/$1');
    $routes->post('klinik/save-odontogram', 'Klinik::saveOdontogram');
    $routes->get('klinik/odontogram-json/(:num)', 'Klinik::getOdontogramJson/$1');
    $routes->get('klinik/laporan', 'Klinik::laporan');
    $routes->get('klinik/cetak-laporan', 'Klinik::cetakLaporan');
    $routes->get('klinik/export-pasien-excel', 'Klinik::exportPasienExcel');
    $routes->get('klinik/cetak-laporan-pasien', 'Klinik::cetakLaporanPasien');

    // Apotek
    $routes->match(['GET', 'POST'], 'apotek/penjualan', 'Apotek::penjualan');
    $routes->match(['GET', 'POST'], 'apotek/penjualan-langsung', 'Apotek::penjualan');
    $routes->get('apotek/cetak-nota/(:num)', 'Apotek::cetakNota/$1');
    $routes->get('apotek/sale-detail/(:num)', 'Apotek::ajaxSaleDetail/$1');
    $routes->match(['GET', 'POST'], 'apotek/resep', 'Apotek::resep');
    $routes->match(['GET', 'POST'], 'apotek/stok', 'Apotek::stok');
    $routes->get('apotek/kartu-stok', 'Apotek::kartuStok');
    $routes->match(['GET', 'POST'], 'apotek/opname', 'Apotek::opname');
    $routes->get('apotek/laporan', 'Apotek::laporan');
    $routes->get('apotek/cetak-etiket/(:num)', 'Apotek::cetakEtiket/$1');
    $routes->get('apotek/cetak-opname/(:num)', 'Apotek::cetakOpname/$1');
    $routes->get('apotek/cetak-laporan', 'Apotek::cetakLaporan');
    $routes->get('apotek/export-penjualan-csv', 'Apotek::exportPenjualanCsv');
    $routes->get('apotek/gudang', 'Apotek::gudang');
    $routes->post('apotek/transfer-stok', 'Apotek::transferStok');
    $routes->get('apotek/cetak-surat-mutasi/(:num)', 'Apotek::cetakSuratMutasi/$1');
    $routes->get('apotek/warehouse-stock-json/(:num)', 'Apotek::getWarehouseStockJson/$1');
    
    // Fitur Pending Resep & Ambil Resep Sebelumnya
    $routes->post('apotek/pending-save', 'Apotek::ajaxPendingSave');
    $routes->get('apotek/pending-list', 'Apotek::ajaxPendingList');
    $routes->get('apotek/pending-resume/(:num)', 'Apotek::ajaxPendingResume/$1');
    $routes->post('apotek/pending-delete/(:num)', 'Apotek::ajaxPendingDelete/$1');
    $routes->get('apotek/past-prescriptions', 'Apotek::ajaxPastPrescriptions');

    // Riwayat e-Resep, Struk & Kwitansi Farmasi
    $routes->get('apotek/resep-detail-json/(:num)', 'Apotek::getPrescriptionDetailJson/$1');
    $routes->get('apotek/cetak-struk-resep/(:num)', 'Apotek::cetakStrukResep/$1');
    $routes->get('apotek/cetak-kwitansi-resep/(:num)', 'Apotek::cetakKwitansiResep/$1');

    // Resto & Healthy Nutrition Store
    $routes->match(['GET', 'POST'], 'resto/pos', 'Resto::pos');
    $routes->match(['GET', 'POST'], 'resto/dapur', 'Resto::dapur');
    $routes->get('resto/display-antrean', 'Resto::displayAntrean');
    $routes->get('resto/antrean-json', 'Resto::getAntreanJson');
    $routes->get('resto/laporan', 'Resto::laporan');
    $routes->get('resto/cetak-laporan', 'Resto::cetakLaporan');
    $routes->post('resto/restock', 'Resto::restock');
    $routes->get('resto/patient-diet-info/(:num)', 'Resto::getPatientDietInfo/$1');
    $routes->get('resto/cetak-nota/(:num)', 'Resto::cetakNota/$1');

    // Keuangan & Kasir
    $routes->match(['GET', 'POST'], 'keuangan/kasir', 'Keuangan::kasir');
    $routes->post('keuangan/batal-tagihan', 'Keuangan::batalTagihan');
    $routes->post('keuangan/void-transaksi', 'Keuangan::voidTransaksi');
    $routes->get('keuangan/billing-details/(:num)', 'Keuangan::getBillingDetails/$1');
    $routes->match(['GET', 'POST'], 'keuangan/transaksi', 'Keuangan::transaksi');
    $routes->get('keuangan/kas', 'Keuangan::transaksi'); // Alias untuk keuangan/transaksi
    $routes->get('keuangan/piutang', function() { return redirect()->to(base_url('accounting/buku-besar?account_id=5')); });
    $routes->get('keuangan/hutang', function() { return redirect()->to(base_url('procurement/po')); });
    $routes->get('klinik/rekam-medis', 'Klinik::soap'); // Alias untuk klinik/soap
    $routes->get('resto/orders', 'Resto::laporan'); // Alias untuk resto/laporan
    $routes->post('keuangan/set-saldo-awal-kas', 'Keuangan::setSaldoAwalKas');
    $routes->get('keuangan/rekap-harian', 'Keuangan::rekapHarian');
    $routes->get('keuangan/cetak-rekap-harian', 'Keuangan::cetakRekapHarian');
    $routes->get('keuangan/aging-schedule', 'Keuangan::agingReport');
    $routes->get('keuangan/fee-dokter', 'Keuangan::feeDokter');
    $routes->post('keuangan/bayar-fee-dokter', 'Keuangan::bayarFeeDokter');
    $routes->post('keuangan/transfer-kas', 'Keuangan::transferKas');
    $routes->post('keuangan/pemasukan-lain', 'Keuangan::pemasukanLain');
    $routes->get('keuangan/cetak-kwitansi/(:num)', 'Keuangan::cetakKwitansi/$1');
    $routes->get('keuangan/ekspor-excel-multisheet', 'Keuangan::eksporExcelMultisheet');
    $routes->get('accounting/ekspor-excel-multisheet', 'Keuangan::eksporExcelMultisheet');

    // Accounting & Laporan Keuangan
    $routes->match(['GET', 'POST'], 'accounting/coa', 'Accounting::coa');
    $routes->get('accounting/coa/check-code', 'Accounting::checkCodeExists');
    $routes->post('accounting/coa/update/(:num)', 'Accounting::updateCoa/$1');
    $routes->match(['GET', 'POST'], 'accounting/saldo-awal', 'Accounting::saldoAwal');
    $routes->post('accounting/save-saldo-awal', 'Accounting::saveSaldoAwal');
    $routes->get('accounting/reset-saldo-awal', 'Accounting::resetSaldoAwal');
    $routes->match(['GET', 'POST'], 'accounting/jurnal', 'Accounting::jurnal');
    $routes->get('accounting/jurnal/export-excel', 'Accounting::exportJurnalExcel');
    $routes->get('accounting/jurnal/export-pdf', 'Accounting::exportJurnalPdf');
    $routes->post('accounting/update-jurnal', 'Accounting::updateJurnalManual');
    $routes->get('accounting/delete-jurnal/(:num)', 'Accounting::deleteJurnalManual/$1');
    $routes->get('accounting/jurnal-json/(:num)', 'Accounting::getJurnalDetailsJson/$1');
    $routes->get('accounting/laporan', 'Accounting::laporan');
    $routes->get('accounting/cetak-laporan', 'Accounting::cetakLaporan');
    $routes->get('accounting/buku-besar', 'Accounting::bukuBesar');

    // Master Dynamic Journal Split Rules & COA Template (Jurnal Umum Per-Akun)
    $routes->get('accounting/aturan-jurnal', 'Accounting::aturanJurnal');
    $routes->get('accounting/aturan-jurnal/detail/(:num)', 'Accounting::detailAturanJurnal/$1');
    $routes->post('accounting/aturan-jurnal/save-category', 'Accounting::saveCategory');
    $routes->post('accounting/aturan-jurnal/delete-category/(:num)', 'Accounting::deleteCategory/$1');
    $routes->post('accounting/aturan-jurnal/toggle-category/(:num)', 'Accounting::toggleCategoryStatus/$1');
    $routes->post('accounting/aturan-jurnal/save-rule', 'Accounting::saveRule');
    $routes->post('accounting/aturan-jurnal/delete-rule/(:num)', 'Accounting::deleteRule/$1');
    $routes->get('accounting/aturan-jurnal/api-simulate/(:num)', 'Accounting::apiSimulateSplit/$1');

    // Procurement & Approval
    $routes->match(['GET', 'POST'], 'procurement/po', 'Procurement::po');
    $routes->match(['GET', 'POST'], 'procurement/approval', 'Procurement::approval');
    $routes->post('procurement/supplier', 'Procurement::supplier');
    $routes->get('procurement/cetak-pr/(:num)', 'Procurement::cetakPr/$1');
    $routes->get('procurement/cetak-po/(:num)', 'Procurement::cetakPo/$1');
    $routes->get('procurement/cetak-grn/(:num)', 'Procurement::cetakGrn/$1');
    $routes->get('procurement/pr-details-json/(:num)', 'Procurement::getPrDetailsJson/$1');
    $routes->get('procurement/po-details-json/(:num)', 'Procurement::getPoDetailsJson/$1');

    // Inventaris
    $routes->match(['GET', 'POST'], 'inventaris/aset', 'Inventaris::aset');
    $routes->post('inventaris/mutasi', 'Inventaris::mutasi');
    $routes->post('inventaris/servis', 'Inventaris::servis');
    $routes->post('inventaris/hitung-depresiasi', 'Inventaris::hitungDepresiasi');
    $routes->get('inventaris/cetak-label/(:num)', 'Inventaris::cetakLabel/$1');
    $routes->get('inventaris/cetak-laporan', 'Inventaris::cetakLaporan');

    // HRD & Payroll
    $routes->match(['GET', 'POST'], 'hrd/pegawai', 'HRD::pegawai');
    $routes->post('hrd/generate-payroll', 'HRD::generatePayroll');
    $routes->post('hrd/bayar-payroll', 'HRD::bayarPayroll');
    $routes->get('hrd/cetak-slip-gaji/(:num)', 'HRD::cetakSlipGaji/$1');
    $routes->get('hrd/cetak-rekap-payroll/(:num)', 'HRD::cetakRekapPayroll/$1');

    // Distributor & Grosir (B2B Unit Mandiri)
    $routes->get('distributor', 'Distributor::index');
    $routes->get('distributor/dashboard', 'Distributor::index');
    $routes->get('distributor/penjualan', 'Distributor::penjualan');
    $routes->post('distributor/simpan-penjualan', 'Distributor::simpanPenjualan');
    $routes->get('distributor/riwayat', 'Distributor::riwayat');
    $routes->get('distributor/detail-penjualan/(:num)', 'Distributor::detailPenjualan/$1');
    $routes->get('distributor/cetak-faktur/(:num)', 'Distributor::cetakFaktur/$1');
    $routes->get('distributor/cetak-surat-jalan/(:num)', 'Distributor::cetakSuratJalan/$1');
    $routes->get('distributor/pelanggan', 'Distributor::pelanggan');
    $routes->post('distributor/simpan-pelanggan', 'Distributor::simpanPelanggan');
    $routes->get('distributor/stok', 'Distributor::stok');
    $routes->post('distributor/simpan-stok', 'Distributor::simpanStok');
    $routes->get('distributor/kartu-stok/(:num)', 'Distributor::kartuStok/$1');
    $routes->get('distributor/transfer', 'Distributor::transfer');
    $routes->post('distributor/simpan-transfer', 'Distributor::simpanTransfer');
    $routes->get('distributor/piutang', 'Distributor::piutang');
    $routes->post('distributor/bayar-piutang', 'Distributor::bayarPiutang');
    $routes->get('distributor/riwayat-pembayaran/(:num)', 'Distributor::riwayatPembayaran/$1');
    $routes->get('distributor/retur', 'Distributor::retur');
    $routes->post('distributor/simpan-retur', 'Distributor::simpanRetur');
    $routes->get('distributor/laporan', 'Distributor::laporan');

    $routes->get('hrd/pegawai-json/(:num)', 'HRD::getPegawaiJson/$1');

    // HRD: Absensi & Presensi Karyawan
    $routes->match(['GET', 'POST'], 'hrd/absensi', 'HRD::absensi');
    $routes->get('hrd/kpi', 'HRD::kpi');
    $routes->post('hrd/check-in', 'HRD::checkIn');
    $routes->post('hrd/check-out', 'HRD::checkOut');
    $routes->post('hrd/ajukan-cuti', 'HRD::ajukanCuti');
    $routes->post('hrd/approval-cuti', 'HRD::approvalCuti');
    $routes->post('hrd/manage-shift', 'HRD::manageShift');
    $routes->get('hrd/cetak-rekap-absensi', 'HRD::cetakRekapAbsensi');

    // User Profile Management
    $routes->match(['GET', 'POST'], 'profile', 'System::profile');
    $routes->post('profile/update-password', 'System::updatePassword');
    $routes->post('profile/upload-avatar', 'System::uploadAvatar');

    // System Administration & Security
    $routes->get('system/whats-new', 'System::whatsNew');
    $routes->post('system/whats-new/save', 'System::saveWhatsNew');
    $routes->post('system/whats-new/delete', 'System::deleteWhatsNew');
    $routes->get('system/whats-new/toggle/(:num)', 'System::toggleWhatsNew/$1');
    $routes->match(['GET', 'POST'], 'system/users', 'System::users');
    $routes->post('system/save-user', 'System::saveUser');
    $routes->get('system/toggle-user/(:num)', 'System::toggleUser/$1');
    $routes->post('system/save-role', 'System::saveRole');
    $routes->get('system/delete-role/(:num)', 'System::deleteRole/$1');
    $routes->get('system/database', 'System::database');
    $routes->get('system/backup-dump', 'System::backupDump');
    $routes->post('system/backup/create', 'System::createBackup');
    $routes->get('system/backup/download/(:segment)', 'System::downloadBackup/$1');
    $routes->post('system/backup/delete/(:segment)', 'System::deleteBackup/$1');
    $routes->post('system/backup/restore', 'System::restoreBackup');
    $routes->post('system/backup/settings', 'System::saveBackupSettings');
    $routes->get('system/whatsapp', 'System::whatsapp');
    $routes->post('system/send-whatsapp', 'System::sendWhatsappSimulation');
    $routes->get('system/error-logs', 'System::errorLogs');
    $routes->get('system/resolve-error/(:num)', 'System::resolveError/$1');
    $routes->get('system/clear-error-logs', 'System::clearErrorLogs');
    $routes->get('system/performance', 'System::performance');
    $routes->match(['GET', 'POST'], 'system/clear-cache', 'System::clearCache');
    $routes->get('system/optimize-tables', 'System::optimizeTables');
    $routes->post('system/optimize-db', 'System::optimizeDb');
    $routes->match(['GET', 'POST'], 'system/reset-transactions', 'System::resetTransactions');
    $routes->get('system/audit', 'System::audit');
    $routes->get('system/notif-api', 'System::notifApi');
    $routes->get('system/csrf-token', 'System::csrfToken');
    $routes->get('system/notifications', 'System::notifications');
    $routes->post('system/notifications/mark-read/(:num)', 'System::markNotificationRead/$1');
    $routes->get('system/notifications/mark-read/(:num)', 'System::markNotificationRead/$1');
    $routes->post('system/notifications/mark-all-read', 'System::markAllNotificationsRead');
    $routes->get('system/notifications/mark-all-read', 'System::markAllNotificationsRead');
    $routes->get('system/notifications/toggle-rule/(:num)', 'System::toggleNotificationRule/$1');
    $routes->get('system/notifications/receipts/(:num)', 'System::notificationReceipts/$1');
    $routes->match(['GET', 'POST'], 'system/settings', 'System::settings');
    $routes->post('system/test-wa-gateway', 'System::testWaGateway');
    $routes->post('system/test-satusehat', 'System::testSatuSehat');
    $routes->match(['GET', 'POST'], 'system/articles', 'System::articles');
    $routes->get('system/master-klinik', 'MasterKlinik::index');
    $routes->post('system/master-klinik/category', 'MasterKlinik::manageCategory');
    $routes->post('system/master-klinik/poly', 'MasterKlinik::managePoliklinikBaru');
    $routes->post('system/master-klinik/poliklinik-baru', 'MasterKlinik::managePoliklinikBaru');
    $routes->post('system/master-klinik/doctor', 'MasterKlinik::manageDoctor');
    $routes->post('system/master-klinik/save-doctor-signature', 'MasterKlinik::saveDoctorSignature');
    $routes->post('system/master-klinik/service', 'MasterKlinik::manageService');
    $routes->get('system/master-klinik/tindakan-json', 'MasterKlinik::getTindakanJson');
    $routes->post('system/master-klinik/room', 'MasterKlinik::manageRoom');
    $routes->post('system/master-klinik/bed', 'MasterKlinik::manageBed');
    $routes->post('system/master-klinik/medicine-category', 'MasterKlinik::manageMedicineCategory');
    $routes->post('system/master-klinik/unit', 'MasterKlinik::manageUnit');
    $routes->post('system/master-klinik/consent-template', 'MasterKlinik::manageConsentTemplate');
    $routes->get('system/master-klinik/consent-templates-json', 'MasterKlinik::getConsentTemplatesJson');
    $routes->post('system/master-klinik/department', 'MasterKlinik::manageDepartment');
    $routes->post('system/master-klinik/job-position', 'MasterKlinik::manageJobPosition');
    $routes->post('system/master-klinik/lab-test', 'MasterKlinik::manageLabTest');
    $routes->post('system/master-klinik/insurance-provider', 'MasterKlinik::manageInsuranceProvider');
    $routes->post('system/master-klinik/doctor-schedule', 'MasterKlinik::manageDoctorSchedule');
    $routes->post('system/master-klinik/supplier', 'MasterKlinik::manageSupplier');

    // Master ICD (Diagnosa & Prosedur Medis)
    $routes->get('system/master-icd', 'MasterIcd::index');
    $routes->post('system/master-icd/save-icd10', 'MasterIcd::saveIcd10');
    $routes->post('system/master-icd/delete-icd10', 'MasterIcd::deleteIcd10');
    $routes->post('system/master-icd/save-icd9', 'MasterIcd::saveIcd9');
    $routes->post('system/master-icd/delete-icd9', 'MasterIcd::deleteIcd9');
    $routes->get('system/master-icd/search-json', 'MasterIcd::searchJson');

    // Master Metode Pembayaran
    $routes->get('system/master-pembayaran', 'MasterPembayaran::index');
    $routes->post('system/master-pembayaran/save', 'MasterPembayaran::save');
    $routes->get('system/master-pembayaran/toggle/(:num)', 'MasterPembayaran::toggle/$1');
    $routes->get('system/master-pembayaran/delete/(:num)', 'MasterPembayaran::delete/$1');
    $routes->get('system/master-pembayaran/json', 'MasterPembayaran::getJson');

    // Pusat Bantuan, Alur Sistem & Dokumentasi Interaktif (Database Driven)
    $routes->get('bantuan', 'Help::index');
    $routes->get('help', 'Help::index');
    $routes->post('bantuan/save', 'Help::save');
    $routes->get('bantuan/delete/(:num)', 'Help::delete/$1');
    $routes->get('bantuan/toggle/(:num)', 'Help::toggle/$1');
    $routes->get('bantuan/cetak', 'Help::cetak');
});

/**
 * --------------------------------------------------------------------
 * RESTful API Routes (Protected by ApiKeyFilter & ResponseTrait)
 * --------------------------------------------------------------------
 */
$routes->group('api/v1', ['filter' => 'api_auth'], static function ($routes) {
    // Antrean Display Real-time
    $routes->get('antrean', '\App\Controllers\Api\AntreanController::index');

    // Patient Interoperability
    $routes->get('patients/search', '\App\Controllers\Api\PatientController::search');
    $routes->post('patients/register', '\App\Controllers\Api\PatientController::register');

    // Medicine & Stock Catalog
    $routes->get('medicines', '\App\Controllers\Api\MedicineController::index');

    // SATUSEHAT Kemenkes Interoperability (HL7 FHIR R4)
    $routes->get('satusehat/encounter/(:num)', '\App\Controllers\Api\SatuSehatController::encounter/$1');
});

/**
 * --------------------------------------------------------------------
 * Real-Time Zero-Reload Live-Sync API Routes (High Speed JSON Engine)
 * --------------------------------------------------------------------
 */
$routes->group('api/sync', static function ($routes) {
    $routes->get('ping', 'LiveSync::ping');
    $routes->get('csrf-token', 'LiveSync::csrfToken');
    $routes->get('klinik-queue', 'LiveSync::klinikQueue');
    $routes->get('pharmacy-prescriptions', 'LiveSync::pharmacyPrescriptions');
    $routes->get('cashier-billings', 'LiveSync::cashierBillings');
    $routes->get('resto-kitchen', 'LiveSync::restoKitchen');
    $routes->get('hrd-presence', 'LiveSync::hrdPresence');
    $routes->get('lab-queue', 'LiveSync::labQueue');

    // Voice Caller & Anti-Collision Central Queue Engine
    $routes->get('voice-queue', 'LiveSync::voiceQueue');
    $routes->match(['GET', 'POST'], 'voice-call-trigger', 'LiveSync::voiceCallTrigger');
    $routes->match(['GET', 'POST'], 'voice-call-ack', 'LiveSync::voiceCallAck');
    $routes->get('voice-call-history', 'LiveSync::voiceCallHistory');
});

