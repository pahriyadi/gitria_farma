<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateClinicFixedRules extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'clinic:update-fixed-rules';
    protected $description = 'Set RAWAT_JALAN_POLI rules to standard fixed breakdown Rp 150.000';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $cat = $db->table('journal_categories')->where('category_code', 'RAWAT_JALAN_POLI')->get()->getRow();

        if (!$cat) {
            CLI::error("Kategori RAWAT_JALAN_POLI tidak ditemukan!");
            return;
        }

        $now = date('Y-m-d H:i:s');

        // Helper to find account ID
        $getAccId = function($code) use ($db) {
            $row = $db->table('accounts')->where('code', $code)->get()->getRow();
            return $row ? (int)$row->id : null;
        };

        $accKasKlinik   = $getAccId('111.2') ?: $getAccId('111');
        $accDokter      = $getAccId('424') ?: $getAccId('241');
        $accKaryawan    = $getAccId('242') ?: $getAccId('2-103');
        $accFasilitas   = $getAccId('425') ?: $getAccId('4-105');
        $accBmhp        = $getAccId('415') ?: $getAccId('4-104');
        $accAdm         = $getAccId('421') ?: $getAccId('4-103');
        $accKonseling   = $getAccId('422') ?: $getAccId('4-102');

        $rulesData = [
            [
                'category_id'        => $cat->id,
                'item_name'          => 'KAS TUNAI KLINIK',
                'account_id'         => $accKasKlinik,
                'position'           => 'debit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 150000.00,
                'formula_code'       => 'DEFAULT_DEBIT',
                'sort_order'         => 1,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'DOKTER',
                'account_id'         => $accDokter,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 100000.00,
                'formula_code'       => 'DOCTOR_FEE_PCT',
                'sort_order'         => 2,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'UTANG FEE KARYAWAN',
                'account_id'         => $accKaryawan,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 10000.00,
                'formula_code'       => '',
                'sort_order'         => 3,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'FASILITAS KLINIK',
                'account_id'         => $accFasilitas,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 26896.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 4,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'B M H P',
                'account_id'         => $accBmhp,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 7610.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 5,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'ADMINISTRASI',
                'account_id'         => $accAdm,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 4395.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 6,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ],
            [
                'category_id'        => $cat->id,
                'item_name'          => 'KONSELING FARMASI',
                'account_id'         => $accKonseling,
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 1099.00,
                'formula_code'       => 'DYNAMIC_OBAT_REMAINDER',
                'sort_order'         => 7,
                'is_active'          => 1,
                'created_at'         => $now,
                'updated_at'         => $now
            ]
        ];

        // Replace rules for RAWAT_JALAN_POLI
        $db->table('journal_category_rules')->where('category_id', $cat->id)->delete();
        $db->table('journal_category_rules')->insertBatch($rulesData);

        // Update default fee on doctors table to fixed Rp 100.000
        $db->table('doctors')->update([
            'fee_type'       => 'fixed_amount',
            'fee_per_pasien' => 100000.00
        ]);

        CLI::write("Aturan RAWAT_JALAN_POLI berhasil diperbarui ke nilai tetap Rp 150.000!", 'green');
        CLI::write("Tabel dokter berhasil diperbarui ke fee_type = fixed_amount (Rp 100.000)!", 'green');
    }
}
