<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function (): void {
    if (ENVIRONMENT !== 'testing') {
        $value = ini_get('zlib.output_compression');

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) || (int) $value > 0) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }
});

/*
 * --------------------------------------------------------------------
 * Enterprise Domain Events & Hooks (Sawamawa Medical Center)
 * --------------------------------------------------------------------
 */

// 1. Hook Event: Pasien Baru Terdaftar
Events::on('patient.registered', static function ($patientId, $patientData = []): void {
    \App\Services\AuditService::log(
        'CREATE',
        'Pendaftaran Pasien',
        'Registrasi Pasien Baru: ' . ($patientData['name'] ?? "ID #{$patientId}") . ' (No RM: ' . ($patientData['no_rm'] ?? '-') . ')',
        'patients',
        (int) $patientId
    );
});

// 2. Hook Event: Pelunasan Billing Kasir (Auto-Jurnal & Fee Dokter)
Events::on('billing.paid', static function ($billingId, $amount, $paymentMethod = 'tunai', $notes = ''): void {
    try {
        $je = new \App\Services\JournalEngine();
        $je->postJournal('CLINIC_PAYMENT', $billingId, (float) $amount, "Pelunasan Billing Kasir #{$billingId} - {$notes}", $paymentMethod, 'Kasir');
        
        \App\Services\AuditService::log(
            'PAYMENT',
            'Kasir Utama',
            "Pelunasan Billing Tagihan Pasien #{$billingId} sebesar Rp " . number_format((float) $amount, 0, ',', '.') . " ({$paymentMethod})",
            'billing_transactions',
            (int) $billingId
        );
    } catch (\Throwable $e) {
        \App\Services\ErrorTrackerService::logException($e, 'ERROR');
    }
});

// 3. Hook Event: Penyerahan & Tebus Resep Obat Farmasi
Events::on('pharmacy.dispensed', static function ($prescriptionId, $doctorName = '', $patientName = ''): void {
    \App\Services\AuditService::log(
        'DISPENSE',
        'Apotek (e-Resep)',
        "Penyerahan dan peracikan e-Resep #{$prescriptionId} Pasien [{$patientName}] oleh Dokter [{$doctorName}]",
        'prescriptions',
        (int) $prescriptionId
    );
});

// 4. Hook Event: Peringatan Stok Obat Menipis (< Min Stock)
Events::on('pharmacy.stock_low', static function ($medicineId, $medicineName, $currentStock, $minStock): void {
    \App\Services\AuditService::log(
        'ALERT',
        'Logistik & Farmasi',
        "Peringatan Stok Kritis: Obat [{$medicineName}] tersisa {$currentStock} (Batas minimum: {$minStock})",
        'medicines',
        (int) $medicineId
    );
});

// 5. Hook Event: Deteksi Galat APM Otomatis
Events::on('system.error_logged', static function (\Throwable $exception, $severity = 'ERROR'): void {
    \App\Services\ErrorTrackerService::logException($exception, $severity);
});

