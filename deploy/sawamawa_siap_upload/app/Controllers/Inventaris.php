<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Inventaris extends BaseController
{
    /**
     * Dashboard Master Inventaris Aset, Mutasi, Pemeliharaan & Depresiasi
     */
    public function aset()
    {
        $db = \Config\Database::connect();

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            // 1. TAMBAH ASET BARU
            if ($action === 'add_asset' || empty($action)) {
                $code = trim($this->request->getPost('code') ?: '');
                if (empty($code)) {
                    $cat = strtoupper(substr($this->request->getPost('category') ?: 'AST', 0, 3));
                    $count = $db->table('inventory_assets')->countAllResults();
                    $code = 'AST-' . $cat . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                }

                $price        = max(0, floatval($this->request->getPost('price')));
                $usefulLife   = max(1, intval($this->request->getPost('useful_life_years') ?: 4));
                $salvageValue = max(0, floatval($this->request->getPost('salvage_value') ?: 0));

                $assetData = [
                    'code'              => $code,
                    'name'              => trim($this->request->getPost('name')),
                    'brand'             => trim($this->request->getPost('brand') ?: ''),
                    'category'          => $this->request->getPost('category') ?: 'Elektronik',
                    'location'          => trim($this->request->getPost('location') ?: 'Gudang Utama'),
                    'purchase_date'     => $this->request->getPost('purchase_date') ?: date('Y-m-d'),
                    'price'             => $price,
                    'useful_life_years' => $usefulLife,
                    'salvage_value'     => $salvageValue,
                    'current_value'     => $price,
                    'supplier_id'       => intval($this->request->getPost('supplier_id')) ?: null,
                    'condition_status'  => $this->request->getPost('condition_status') ?: 'good',
                    'pj_employee'       => trim($this->request->getPost('pj_employee') ?: 'Staff Umum'),
                    'serial_number'     => trim($this->request->getPost('serial_number') ?: ''),
                    'status'            => 'active',
                    'created_at'        => date('Y-m-d H:i:s')
                ];

                $db->table('inventory_assets')->insert($assetData);
                session()->setFlashdata('success', 'Aset baru berhasil dicatatkan dengan kode: ' . $code);
                return redirect()->to(base_url('inventaris/aset'));
            }

            // 2. EDIT ASET
            if ($action === 'edit_asset') {
                $id = intval($this->request->getPost('id'));
                $price        = max(0, floatval($this->request->getPost('price')));
                $usefulLife   = max(1, intval($this->request->getPost('useful_life_years') ?: 4));
                $salvageValue = max(0, floatval($this->request->getPost('salvage_value') ?: 0));

                $db->table('inventory_assets')->where('id', $id)->update([
                    'code'              => trim($this->request->getPost('code')),
                    'name'              => trim($this->request->getPost('name')),
                    'brand'             => trim($this->request->getPost('brand') ?: ''),
                    'category'          => $this->request->getPost('category'),
                    'location'          => trim($this->request->getPost('location')),
                    'purchase_date'     => $this->request->getPost('purchase_date'),
                    'price'             => $price,
                    'useful_life_years' => $usefulLife,
                    'salvage_value'     => $salvageValue,
                    'supplier_id'       => intval($this->request->getPost('supplier_id')) ?: null,
                    'condition_status'  => $this->request->getPost('condition_status'),
                    'pj_employee'       => trim($this->request->getPost('pj_employee')),
                    'serial_number'     => trim($this->request->getPost('serial_number') ?: ''),
                    'status'            => $this->request->getPost('status') ?: 'active'
                ]);

                session()->setFlashdata('success', 'Data aset berhasil diperbarui.');
                return redirect()->to(base_url('inventaris/aset'));
            }

            // 3. HAPUS / NONAKTIFKAN ASET
            if ($action === 'delete_asset') {
                $id = intval($this->request->getPost('id'));
                $db->table('inventory_assets')->where('id', $id)->update(['status' => 'inactive', 'condition_status' => 'damaged']);
                session()->setFlashdata('success', 'Aset dinonaktifkan dari inventaris aktif.');
                return redirect()->to(base_url('inventaris/aset'));
            }
        }

        // Fetch Assets
        $assets = $db->table('inventory_assets')
                     ->select('inventory_assets.*, suppliers.name as supplier_name,
                               (SELECT COUNT(*) FROM asset_mutations WHERE asset_mutations.asset_id = inventory_assets.id) as mutation_count,
                               (SELECT COUNT(*) FROM asset_maintenances WHERE asset_maintenances.asset_id = inventory_assets.id) as maintenance_count')
                     ->join('suppliers', 'suppliers.id = inventory_assets.supplier_id', 'left')
                     ->where('inventory_assets.status', 'active')
                     ->orderBy('inventory_assets.created_at', 'DESC')
                     ->get()
                     ->getResult();

        // Fetch Mutations
        $mutations = $db->table('asset_mutations')
                        ->select('asset_mutations.*, inventory_assets.code as asset_code, inventory_assets.name as asset_name')
                        ->join('inventory_assets', 'inventory_assets.id = asset_mutations.asset_id')
                        ->orderBy('asset_mutations.created_at', 'DESC')
                        ->get()
                        ->getResult();

        // Fetch Maintenances
        $maintenances = $db->table('asset_maintenances')
                           ->select('asset_maintenances.*, inventory_assets.code as asset_code, inventory_assets.name as asset_name, inventory_assets.location')
                           ->join('inventory_assets', 'inventory_assets.id = asset_maintenances.asset_id')
                           ->orderBy('asset_maintenances.service_date', 'DESC')
                           ->get()
                           ->getResult();

        // Fetch Depreciations
        $depreciations = $db->table('asset_depreciations')
                            ->select('asset_depreciations.*, inventory_assets.code as asset_code, inventory_assets.name as asset_name, inventory_assets.price as original_price')
                            ->join('inventory_assets', 'inventory_assets.id = asset_depreciations.asset_id')
                            ->orderBy('asset_depreciations.created_at', 'DESC')
                            ->get()
                            ->getResult();

        $suppliers = $db->table('suppliers')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();

        // KPI Calculations
        $totalAcquisition = 0;
        $totalBookValue   = 0;
        $goodCondition    = 0;
        $maintenanceCount = 0;

        foreach ($assets as $a) {
            $totalAcquisition += $a->price;
            $totalBookValue   += ($a->current_value !== null ? $a->current_value : $a->price);
            if ($a->condition_status === 'good') $goodCondition++;
            if ($a->condition_status === 'maintenance' || $a->condition_status === 'damaged') $maintenanceCount++;
        }

        $data = [
            'title'            => 'Inventaris & Manajemen Aset Klinik',
            'active_menu'      => 'inventaris-aset',
            'assets'           => $assets,
            'mutations'        => $mutations,
            'maintenances'     => $maintenances,
            'depreciations'    => $depreciations,
            'suppliers'        => $suppliers,
            'totalAcquisition' => $totalAcquisition,
            'totalBookValue'   => $totalBookValue,
            'goodCondition'    => $goodCondition,
            'maintenanceCount' => $maintenanceCount
        ];

        return view('inventaris/aset', $data);
    }

    /**
     * Mutasi / Relokasi Lokasi dan PJ Aset
     */
    public function mutasi()
    {
        $db = \Config\Database::connect();

        $assetId      = intval($this->request->getPost('asset_id'));
        $newLocation  = trim($this->request->getPost('new_location'));
        $newPj        = trim($this->request->getPost('new_pj'));
        $mutationDate = $this->request->getPost('mutation_date') ?: date('Y-m-d');
        $notes        = trim($this->request->getPost('notes') ?: '');

        $asset = $db->table('inventory_assets')->where('id', $assetId)->get()->getRow();
        if (!$asset) {
            session()->setFlashdata('error', 'Aset tidak ditemukan.');
            return redirect()->to(base_url('inventaris/aset#tab-mutasi'));
        }

        $db->transStart();

        // Insert mutation history
        $db->table('asset_mutations')->insert([
            'asset_id'      => $assetId,
            'old_location'  => $asset->location,
            'new_location'  => $newLocation,
            'old_pj'        => $asset->pj_employee,
            'new_pj'        => $newPj,
            'mutation_date' => $mutationDate,
            'notes'         => $notes,
            'approved_by'   => session('user_id') ?: 1,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        // Update current asset location and PJ
        $db->table('inventory_assets')->where('id', $assetId)->update([
            'location'    => $newLocation,
            'pj_employee' => $newPj
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses mutasi aset.');
        } else {
            session()->setFlashdata('success', 'Aset ' . $asset->name . ' (' . $asset->code . ') berhasil dimutasi ke ' . $newLocation . ' (PJ: ' . $newPj . ').');
        }

        return redirect()->to(base_url('inventaris/aset#tab-mutasi'));
    }

    /**
     * Catat Riwayat Servis / Pemeliharaan Aset
     */
    public function servis()
    {
        $db = \Config\Database::connect();

        $assetId         = intval($this->request->getPost('asset_id'));
        $serviceDate     = $this->request->getPost('service_date') ?: date('Y-m-d');
        $cost            = max(0, floatval($this->request->getPost('cost')));
        $technician      = trim($this->request->getPost('technician_vendor') ?: 'Teknisi Internal');
        $description     = trim($this->request->getPost('description') ?: 'Servis & pemeliharaan berkala');
        $nextDate        = $this->request->getPost('next_maintenance_date') ?: date('Y-m-d', strtotime('+6 months'));
        $conditionStatus = $this->request->getPost('condition_status') ?: 'good';

        $asset = $db->table('inventory_assets')->where('id', $assetId)->get()->getRow();
        if (!$asset) {
            session()->setFlashdata('error', 'Aset tidak ditemukan.');
            return redirect()->to(base_url('inventaris/aset#tab-servis'));
        }

        $db->transStart();

        $db->table('asset_maintenances')->insert([
            'asset_id'          => $assetId,
            'service_date'      => $serviceDate,
            'cost'              => $cost,
            'technician_vendor' => $technician,
            'description'       => $description,
            'status'            => 'completed',
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        $db->table('inventory_assets')->where('id', $assetId)->update([
            'last_maintenance_date' => $serviceDate,
            'next_maintenance_date' => $nextDate,
            'condition_status'      => $conditionStatus
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal mencatat pemeliharaan aset.');
        } else {
            session()->setFlashdata('success', 'Riwayat servis aset ' . $asset->name . ' berhasil dicatat.');
        }

        return redirect()->to(base_url('inventaris/aset#tab-servis'));
    }

    /**
     * Hitung & Bukukan Depresiasi Aset Garis Lurus (Straight-Line)
     */
    public function hitungDepresiasi()
    {
        $db = \Config\Database::connect();

        $assetId = intval($this->request->getPost('asset_id'));
        $asset = $db->table('inventory_assets')->where('id', $assetId)->get()->getRow();

        if (!$asset) {
            session()->setFlashdata('error', 'Aset tidak ditemukan.');
            return redirect()->to(base_url('inventaris/aset#tab-depresiasi'));
        }

        $price        = $asset->price;
        $salvageValue = $asset->salvage_value ?: 0;
        $usefulYears  = max(1, $asset->useful_life_years ?: 4);

        // Annual straight-line depreciation = (Price - Salvage Value) / Useful Years
        $annualDepr   = ($price - $salvageValue) / $usefulYears;
        $monthlyDepr  = $annualDepr / 12;

        $deprPeriod = $this->request->getPost('depreciation_period') ?: 'tahunan';
        $deprAmount = ($deprPeriod === 'bulanan') ? $monthlyDepr : $annualDepr;

        $currentVal = $asset->current_value !== null ? $asset->current_value : $price;
        $newVal     = max($salvageValue, $currentVal - $deprAmount);

        $db->transStart();

        $db->table('asset_depreciations')->insert([
            'asset_id'          => $assetId,
            'depreciation_date' => date('Y-m-d'),
            'amount'            => $deprAmount,
            'book_value_after'  => $newVal,
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        $db->table('inventory_assets')->where('id', $assetId)->update([
            'current_value' => $newVal
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memproses depresiasi aset.');
        } else {
            session()->setFlashdata('success', 'Penyusutan aset ' . $asset->name . ' sebesar Rp ' . number_format($deprAmount, 0, ',', '.') . ' berhasil dibukukan. Nilai buku saat ini: Rp ' . number_format($newVal, 0, ',', '.'));
        }

        return redirect()->to(base_url('inventaris/aset#tab-depresiasi'));
    }

    /**
     * Cetak Label Stiker / QR Code Aset Siap Tempel
     */
    public function cetakLabel($id)
    {
        $db = \Config\Database::connect();

        $asset = $db->table('inventory_assets')
                    ->select('inventory_assets.*, suppliers.name as supplier_name')
                    ->join('suppliers', 'suppliers.id = inventory_assets.supplier_id', 'left')
                    ->where('inventory_assets.id', $id)
                    ->get()
                    ->getRow();

        if (!$asset) {
            session()->setFlashdata('error', 'Aset tidak ditemukan.');
            return redirect()->to(base_url('inventaris/aset'));
        }

        $data = [
            'title' => 'Label Inventaris Aset ' . $asset->code,
            'asset' => $asset
        ];

        return view('inventaris/cetak_label', $data);
    }

    /**
     * Cetak Laporan Rekapitulasi Inventaris Aset Lengkap
     */
    public function cetakLaporan()
    {
        $db = \Config\Database::connect();

        $assets = $db->table('inventory_assets')
                     ->select('inventory_assets.*, suppliers.name as supplier_name')
                     ->join('suppliers', 'suppliers.id = inventory_assets.supplier_id', 'left')
                     ->where('inventory_assets.status', 'active')
                     ->orderBy('inventory_assets.category', 'ASC')
                     ->get()
                     ->getResult();

        $totalAcquisition = 0;
        $totalBookValue   = 0;
        foreach ($assets as $a) {
            $totalAcquisition += $a->price;
            $totalBookValue   += ($a->current_value !== null ? $a->current_value : $a->price);
        }

        $data = [
            'title'            => 'Laporan Rekapitulasi Inventaris Aset',
            'assets'           => $assets,
            'totalAcquisition' => $totalAcquisition,
            'totalBookValue'   => $totalBookValue
        ];

        return view('inventaris/cetak_laporan', $data);
    }
}
