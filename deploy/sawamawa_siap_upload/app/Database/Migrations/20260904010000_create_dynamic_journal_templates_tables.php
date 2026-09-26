<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDynamicJournalTemplatesTables extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect('default');

        // 1. Table journal_categories (Master Template Transaksi / Kategori Jurnal)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'category_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'apotek',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('module');
        $this->forge->addKey('is_active');
        $this->forge->createTable('journal_categories', true);

        // 2. Table journal_category_rules (Sub-Kategori / Aturan Item Bagi Hasil & Alokasi Akun)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'account_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'ENUM',
                'constraint' => ['debit', 'credit'],
                'default'    => 'credit',
            ],
            'calc_type' => [
                'type'       => 'ENUM',
                'constraint' => ['percentage', 'fixed_amount', 'dynamic_fee', 'formula'],
                'default'    => 'percentage',
            ],
            'percentage_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'default'    => 0.00,
            ],
            'fixed_amount_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'formula_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('category_id');
        $this->forge->addKey('account_id');
        $this->forge->addKey('position');
        $this->forge->createTable('journal_category_rules', true);

        // 3. Helper to resolve account id
        $getAcc = function($code, $fallback = null) use ($db) {
            $r = $db->table('accounts')->where('code', $code)->get()->getRow();
            if ($r) return (int)$r->id;
            if ($fallback) {
                $r2 = $db->table('accounts')->where('code', $fallback)->get()->getRow();
                if ($r2) return (int)$r2->id;
            }
            return null;
        };

        // 4. Seed Default Templates (Persis Format & Kasus Excel Klien)
        $now = date('Y-m-d H:i:s');

        // CATEGORY 1: PENJUALAN OBAT RESEP
        $catResep = $db->table('journal_categories')->where('category_code', 'PENJUALAN_OBAT_RESEP')->get()->getRow();
        if (!$catResep) {
            $db->table('journal_categories')->insert([
                'category_code' => 'PENJUALAN_OBAT_RESEP',
                'category_name' => 'PENJUALAN OBAT RESEP',
                'module'        => 'apotek',
                'description'   => 'Alokasi bagi hasil otomatis transaksi penjualan obat resep dokter di farmasi/apotek.',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);
            $catResepId = $db->insertID();
        } else {
            $catResepId = $catResep->id;
        }

        // Rules for PENJUALAN OBAT RESEP
        $db->table('journal_category_rules')->where('category_id', $catResepId)->delete();
        $resepRules = [
            ['item_name' => 'KAS TUNAI APOTEK', 'account_id' => $getAcc('1111', '1-101') ?: $getAcc('111'), 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1],
            ['item_name' => 'OBAT(UNTUK PEMBELIAN OBAT LAGI)', 'account_id' => $getAcc('511', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 44.00, 'formula_code' => 'DYNAMIC_OBAT_REMAINDER', 'sort_order' => 2],
            ['item_name' => 'UTANG PAJAK', 'account_id' => $getAcc('231', '2-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 11.00, 'formula_code' => null, 'sort_order' => 3],
            ['item_name' => 'PENUNJANG', 'account_id' => $getAcc('512', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 9.00, 'formula_code' => null, 'sort_order' => 4],
            ['item_name' => 'OBAT RESEP', 'account_id' => $getAcc('513', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 7.00, 'formula_code' => null, 'sort_order' => 5],
            ['item_name' => 'ADM', 'account_id' => $getAcc('514', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 24.00, 'formula_code' => null, 'sort_order' => 6],
            ['item_name' => 'UTANG FEE DOKTER', 'account_id' => $getAcc('241', '2-102'), 'position' => 'credit', 'calc_type' => 'dynamic_fee', 'percentage_value' => 5.00, 'formula_code' => 'DOCTOR_FEE_PCT', 'sort_order' => 7],
        ];
        foreach ($resepRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catResepId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }

        // CATEGORY 2: PENJUALAN OBAT BEBAS
        $catBebas = $db->table('journal_categories')->where('category_code', 'PENJUALAN_OBAT_BEBAS')->get()->getRow();
        if (!$catBebas) {
            $db->table('journal_categories')->insert([
                'category_code' => 'PENJUALAN_OBAT_BEBAS',
                'category_name' => 'PENJUALAN OBAT BEBAS',
                'module'        => 'apotek',
                'description'   => 'Alokasi bagi hasil otomatis transaksi penjualan obat bebas (OTC/Over The Counter) langsung di apotek.',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);
            $catBebasId = $db->insertID();
        } else {
            $catBebasId = $catBebas->id;
        }

        // Rules for PENJUALAN OBAT BEBAS
        $db->table('journal_category_rules')->where('category_id', $catBebasId)->delete();
        $bebasRules = [
            ['item_name' => 'KAS TUNAI APOTEK', 'account_id' => $getAcc('1111', '1-101') ?: $getAcc('111'), 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1],
            ['item_name' => 'OBAT(UNTUK PEMBELIAN OBAT LAGI)', 'account_id' => $getAcc('511', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 49.00, 'formula_code' => 'DYNAMIC_OBAT_REMAINDER', 'sort_order' => 2],
            ['item_name' => 'UTANG PAJAK', 'account_id' => $getAcc('231', '2-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 11.00, 'formula_code' => null, 'sort_order' => 3],
            ['item_name' => 'PENUNJANG', 'account_id' => $getAcc('512', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 9.00, 'formula_code' => null, 'sort_order' => 4],
            ['item_name' => 'OBAT RESEP', 'account_id' => $getAcc('513', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 7.00, 'formula_code' => null, 'sort_order' => 5],
            ['item_name' => 'ADM', 'account_id' => $getAcc('514', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 24.00, 'formula_code' => null, 'sort_order' => 6],
        ];
        foreach ($bebasRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catBebasId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }

        // CATEGORY 3: KONSULTASI ONLINE
        $catOnline = $db->table('journal_categories')->where('category_code', 'KONSULTASI_ONLINE')->get()->getRow();
        if (!$catOnline) {
            $db->table('journal_categories')->insert([
                'category_code' => 'KONSULTASI_ONLINE',
                'category_name' => 'KONSULTASI ONLINE',
                'module'        => 'apotek',
                'description'   => 'Alokasi bagi hasil otomatis transaksi paket konsultasi online & obat telemedisin.',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);
            $catOnlineId = $db->insertID();
        } else {
            $catOnlineId = $catOnline->id;
        }

        // Rules for KONSULTASI ONLINE (Rp 200.000 Basis: 36.75%, 8.25%, 6.75%, 5.25%, 18.00%, 15.00%, 10.00%)
        $db->table('journal_category_rules')->where('category_id', $catOnlineId)->delete();
        $onlineRules = [
            ['item_name' => 'KAS TUNAI APOTEK', 'account_id' => $getAcc('1111', '1-101') ?: $getAcc('111'), 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1],
            ['item_name' => 'OBAT', 'account_id' => $getAcc('511', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 36.75, 'formula_code' => 'DYNAMIC_OBAT_REMAINDER', 'sort_order' => 2],
            ['item_name' => 'UTANG PAJAK', 'account_id' => $getAcc('231', '2-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 8.25, 'formula_code' => null, 'sort_order' => 3],
            ['item_name' => 'PENUNJANG', 'account_id' => $getAcc('512', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 6.75, 'formula_code' => null, 'sort_order' => 4],
            ['item_name' => 'OBAT RESEP', 'account_id' => $getAcc('513', '5-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 5.25, 'formula_code' => null, 'sort_order' => 5],
            ['item_name' => 'ADM', 'account_id' => $getAcc('514', '6-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 18.00, 'formula_code' => null, 'sort_order' => 6],
            ['item_name' => 'UTANG FEE DOKTER', 'account_id' => $getAcc('241', '2-102'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 15.00, 'formula_code' => null, 'sort_order' => 7],
            ['item_name' => 'PENDAPATAN JASA DOKTER', 'account_id' => $getAcc('424', '4-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 10.00, 'formula_code' => null, 'sort_order' => 8],
        ];
        foreach ($onlineRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catOnlineId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }

        // CATEGORY 4: PENJUALAN RESTO & DAPUR GIZI
        $catResto = $db->table('journal_categories')->where('category_code', 'RESTO_SALE')->get()->getRow();
        if (!$catResto) {
            $db->table('journal_categories')->insert([
                'category_code' => 'RESTO_SALE',
                'category_name' => 'PENJUALAN RESTO & DAPUR GIZI',
                'module'        => 'resto',
                'description'   => 'Alokasi jurnal otomatis transaksi kasir resto sehat dan dapur gizi.',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);
            $catRestoId = $db->insertID();
        } else {
            $catRestoId = $catResto->id;
        }
        $db->table('journal_category_rules')->where('category_id', $catRestoId)->delete();
        $restoRules = [
            ['item_name' => 'KAS KASIR RESTO', 'account_id' => $getAcc('1111', '1-101') ?: $getAcc('111'), 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1],
            ['item_name' => 'PENDAPATAN RESTO & DAPUR GIZI', 'account_id' => $getAcc('411', '4-103') ?: $getAcc('412'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => null, 'sort_order' => 2],
        ];
        foreach ($restoRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catRestoId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }

        // CATEGORY 5: PELAYANAN RAWAT JALAN & TINDAKAN MEDIS
        $catPoli = $db->table('journal_categories')->where('category_code', 'RAWAT_JALAN_POLI')->get()->getRow();
        if (!$catPoli) {
            $db->table('journal_categories')->insert([
                'category_code' => 'RAWAT_JALAN_POLI',
                'category_name' => 'PELAYANAN RAWAT JALAN & TINDAKAN MEDIS',
                'module'        => 'klinik',
                'description'   => 'Alokasi jurnal otomatis pembayaran kasir klinik untuk registrasi dan tindakan poli.',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);
            $catPoliId = $db->insertID();
        } else {
            $catPoliId = $catPoli->id;
        }
        $db->table('journal_category_rules')->where('category_id', $catPoliId)->delete();
        $poliRules = [
            ['item_name' => 'KAS KASIR KLINIK', 'account_id' => $getAcc('1111', '1-101') ?: $getAcc('111'), 'position' => 'debit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => 'DEFAULT_DEBIT', 'sort_order' => 1],
            ['item_name' => 'PENDAPATAN PELAYANAN MEDIS', 'account_id' => $getAcc('411', '4-101'), 'position' => 'credit', 'calc_type' => 'percentage', 'percentage_value' => 100.00, 'formula_code' => null, 'sort_order' => 2],
        ];
        foreach ($poliRules as $r) {
            $db->table('journal_category_rules')->insert(array_merge($r, [
                'category_id' => $catPoliId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now
            ]));
        }
    }

    public function down()
    {
        $this->forge->dropTable('journal_category_rules', true);
        $this->forge->dropTable('journal_categories', true);
    }
}
