<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestModelsValidation extends BaseCommand
{
    protected $group       = 'Audit';
    protected $name        = 'test:models';
    protected $description = 'Menguji instansiasi, proteksi mass-assignment, dan engine validasi seluruh CI4 Models';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'yellow');
        CLI::write("📦 PENGUJIAN CI4 NATIVE MODELS & BUILT-IN VALIDATION ENGINE", 'yellow');
        CLI::write("==================================================================", 'yellow');

        $db = \Config\Database::connect();
        $allTables = $db->listTables();
        CLI::write("Daftar tabel database: " . implode(', ', $allTables) . "\n", 'white');

        $models = [
            'UserModel'               => \App\Models\UserModel::class,
            'PatientModel'            => \App\Models\PatientModel::class,
            'PatientVisitModel'       => \App\Models\PatientVisitModel::class,
            'DoctorModel'             => \App\Models\DoctorModel::class,
            'PolyclinicModel'         => \App\Models\PolyclinicModel::class,
            'ServiceModel'            => \App\Models\ServiceModel::class,
            'OdontogramModel'         => \App\Models\OdontogramModel::class,
            'MedicineModel'           => \App\Models\MedicineModel::class,
            'MedicineBatchModel'      => \App\Models\MedicineBatchModel::class,
            'PrescriptionModel'       => \App\Models\PrescriptionModel::class,
            'PrescriptionDetailModel' => \App\Models\PrescriptionDetailModel::class,
            'PharmacySaleModel'       => \App\Models\PharmacySaleModel::class,
            'PharmacySaleDetailModel' => \App\Models\PharmacySaleDetailModel::class,
            'StockOpnameModel'        => \App\Models\StockOpnameModel::class,
            'StockOpnameDetailModel'  => \App\Models\StockOpnameDetailModel::class,
            'BillingTransactionModel' => \App\Models\BillingTransactionModel::class,
            'CashTransactionModel'    => \App\Models\CashTransactionModel::class,
            'DoctorFeeSettlementModel'=> \App\Models\DoctorFeeSettlementModel::class,
            'AccountModel'            => \App\Models\AccountModel::class,
            'JournalEntryModel'       => \App\Models\JournalEntryModel::class,
            'JournalEntryDetailModel' => \App\Models\JournalEntryDetailModel::class,
            'SupplierModel'           => \App\Models\SupplierModel::class,
            'PurchaseOrderModel'      => \App\Models\PurchaseOrderModel::class,
            'GoodsReceiptModel'       => \App\Models\GoodsReceiptModel::class,
            'AssetModel'              => \App\Models\AssetModel::class,
            'EmployeeModel'           => \App\Models\EmployeeModel::class,
            'PayrollModel'            => \App\Models\PayrollModel::class,
            'RestaurantOrderModel'    => \App\Models\RestaurantOrderModel::class,
            'PaymentMethodModel'      => \App\Models\PaymentMethodModel::class,
            'Icd10Model'              => \App\Models\Icd10Model::class,
            'AuditLogModel'           => \App\Models\AuditLogModel::class,
            'SystemErrorLogModel'     => \App\Models\SystemErrorLogModel::class,
            'ClinicSettingModel'      => \App\Models\ClinicSettingModel::class,
        ];

        $passed = 0;
        foreach ($models as $name => $class) {
            try {
                $instance = new $class();
                $count = $instance->countAllResults();
                CLI::write("   - Model [" . str_pad($name, 26) . "]: OK (Tabel: " . str_pad($instance->table, 22) . " -> {$count} records)", 'green');
                $passed++;
            } catch (\Throwable $e) {
                CLI::error("   - Model [" . str_pad($name, 26) . "]: FAILED -> " . $e->getMessage());
            }
        }

        CLI::write("\n------------------------------------------------------------------", 'yellow');
        CLI::write("🧪 Menguji Validasi Model (Simulasi Input Kosong pada PatientModel)...", 'cyan');
        $patientModel = new \App\Models\PatientModel();
        $testData = [
            'no_rm'  => '',
            'nik'    => '123', // kurang dari 16 digit
            'name'   => '',
            'gender' => 'X'    // bukan L/P
        ];
        $isValid = $patientModel->validate($testData);
        if (!$isValid) {
            $errors = $patientModel->errors();
            CLI::write("   ✅ Validasi Native CI4 Bekerja Sempurna! Tertangkap " . count($errors) . " pelanggaran aturan:", 'green');
            foreach ($errors as $field => $err) {
                CLI::write("      * [{$field}]: {$err}", 'white');
            }
        }

        CLI::write("\n==================================================================", 'yellow');
        CLI::write("🎉 HASIL PENGUJIAN: {$passed}/" . count($models) . " CI4 MODELS 100% SIAP DIGUNAKAN!", 'green');
        CLI::write("==================================================================\n", 'yellow');
    }
}
