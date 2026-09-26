<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateKonsultasiOnlineRulesAndPharmacySales extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');

        // 1. Tambah kolom doctor_fee_nominal pada pharmacy_sales jika belum ada
        $cols = $db->getFieldNames('pharmacy_sales');
        if (!in_array('doctor_fee_nominal', $cols)) {
            $db->query("ALTER TABLE pharmacy_sales ADD COLUMN doctor_fee_nominal DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER doctor_id;");
        }

        // 2. Cari kategori KONSULTASI_ONLINE
        $cat = $db->table('journal_categories')->where('category_code', 'KONSULTASI_ONLINE')->get()->getRow();
        if (!$cat) {
            $db->table('journal_categories')->insert([
                'category_code' => 'KONSULTASI_ONLINE',
                'category_name' => 'KONSULTASI ONLINE',
                'module'        => 'apotek',
                'description'   => 'Alokasi bagi hasil otomatis paket konsultasi online: Utang Fee Dokter manual, Jasa Dokter Rp 20.000 tetap, sisa kas dialokasikan ke 5 pos persentase.',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
            $catId = $db->insertID();
        } else {
            $catId = $cat->id;
            $db->table('journal_categories')->where('id', $catId)->update([
                'description' => 'Alokasi bagi hasil otomatis paket konsultasi online: Utang Fee Dokter manual, Jasa Dokter Rp 20.000 tetap, sisa kas dialokasikan ke 5 pos persentase.',
                'updated_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Helper untuk mencari account_id
        $getAcc = function($code, $fallback = null) use ($db) {
            $r = $db->table('accounts')->where('code', $code)->get()->getRow();
            if ($r) return (int)$r->id;
            if ($fallback) {
                $rf = $db->table('accounts')->where('code', $fallback)->get()->getRow();
                if ($rf) return (int)$rf->id;
            }
            return null;
        };

        // 3. Reset dan perbarui rules untuk KONSULTASI_ONLINE
        $db->table('journal_category_rules')->where('category_id', $catId)->delete();

        $now = date('Y-m-d H:i:s');
        $newRules = [
            [
                'item_name'          => 'KAS TUNAI APOTEK',
                'account_id'         => $getAcc('1111', '1-101') ?: $getAcc('111'),
                'position'           => 'debit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 100.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'DEFAULT_DEBIT',
                'sort_order'         => 1
            ],
            [
                'item_name'          => 'PENDAPATAN JASA DOKTER',
                'account_id'         => $getAcc('424', '4-101'),
                'position'           => 'credit',
                'calc_type'          => 'fixed_amount',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 20000.00,
                'formula_code'       => 'FIXED_NOMINAL',
                'sort_order'         => 2
            ],
            [
                'item_name'          => 'UTANG FEE DOKTER',
                'account_id'         => $getAcc('241', '2-102'),
                'position'           => 'credit',
                'calc_type'          => 'dynamic_fee',
                'percentage_value'   => 0.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'DOCTOR_FEE_NOMINAL',
                'sort_order'         => 3
            ],
            [
                'item_name'          => 'OBAT',
                'account_id'         => $getAcc('511', '5-101'),
                'position'           => 'credit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 49.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'DYNAMIC_OBAT_REMAINDER',
                'sort_order'         => 4
            ],
            [
                'item_name'          => 'UTANG PAJAK',
                'account_id'         => $getAcc('231', '2-101'),
                'position'           => 'credit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 11.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 5
            ],
            [
                'item_name'          => 'PENUNJANG',
                'account_id'         => $getAcc('512', '6-101'),
                'position'           => 'credit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 9.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 6
            ],
            [
                'item_name'          => 'OBAT RESEP',
                'account_id'         => $getAcc('513', '5-101'),
                'position'           => 'credit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 7.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 7
            ],
            [
                'item_name'          => 'ADM',
                'account_id'         => $getAcc('514', '6-101'),
                'position'           => 'credit',
                'calc_type'          => 'percentage',
                'percentage_value'   => 24.00,
                'fixed_amount_value' => 0.00,
                'formula_code'       => 'REMAINDER_TIER',
                'sort_order'         => 8
            ],
        ];

        foreach ($newRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }
    }

    public function down()
    {
        $db = \Config\Database::connect('default');
        $cols = $db->getFieldNames('pharmacy_sales');
        if (in_array('doctor_fee_nominal', $cols)) {
            $db->query("ALTER TABLE pharmacy_sales DROP COLUMN doctor_fee_nominal;");
        }
    }
}
