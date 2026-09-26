<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MasterKlinik extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect('default');

        // 1. KATEGORI PELAYANAN (POLI, TINDAKAN)
        $categories = $db->table('categories')->orderBy('id', 'ASC')->get()->getResult();

        // 2. SUB KATEGORI PELAYANAN (Poliklinik di bawah Kategori POLI)
        $polikliniks = $db->table('polikliniks')
                          ->select('polikliniks.*, categories.name as category_name')
                          ->join('categories', 'categories.id = polikliniks.category_id', 'left')
                          ->orderBy('polikliniks.id', 'ASC')
                          ->get()
                          ->getResult();

        // 3. SUB KATEGORI & SUB KECIL PELAYANAN (Tindakan di bawah Kategori TINDAKAN)
        $tindakanList = $db->table('tindakan')
                           ->select('tindakan.*, categories.name as category_name, parent_tind.name as parent_name')
                           ->join('categories', 'categories.id = tindakan.category_id', 'left')
                           ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                           ->orderBy('tindakan.parent_id', 'ASC')
                           ->orderBy('tindakan.id', 'ASC')
                           ->get()
                           ->getResult();

        // Induk tindakan untuk parent dropdown
        $parentTindakan = $db->table('tindakan')
                             ->where('parent_id IS NULL')
                             ->orderBy('name', 'ASC')
                             ->get()
                             ->getResult();

        // 4. DATA DOKTER & FEE LAYANAN
        $doctors = $db->table('doctors')
                      ->select('doctors.*, 
                                categories.name as category_name, 
                                polikliniks.name as polyclinic_name, 
                                tindakan.name as tindakan_name,
                                parent_tind.name as parent_tindakan_name,
                                tindakan.price as tindakan_price')
                      ->join('categories', 'categories.id = doctors.category_id', 'left')
                      ->join('polikliniks', 'polikliniks.id = doctors.polyclinic_id', 'left')
                      ->join('tindakan', 'tindakan.id = doctors.tindakan_id', 'left')
                      ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                      ->orderBy('doctors.id', 'ASC')
                      ->get()
                      ->getResult();

        // 5. ATURAN KOMISI (FEE RULES)
        $feeRules = $db->table('fee_rules')
                       ->select('fee_rules.*, tindakan.name as service_name')
                       ->join('tindakan', 'tindakan.id = fee_rules.service_id', 'left')
                       ->orderBy('fee_rules.id', 'DESC')
                       ->get()
                       ->getResult();

        // 6. RUANGAN & BED
        $rooms = $db->table('rooms')->orderBy('id', 'ASC')->get()->getResult();
        $beds = $db->table('beds')
                   ->select('beds.*, rooms.name as room_name, rooms.code as room_code')
                   ->join('rooms', 'rooms.id = beds.room_id', 'left')
                   ->orderBy('beds.id', 'ASC')
                   ->get()->getResult();

        // 7. KATEGORI & SATUAN OBAT
        $medicineCategories = $db->table('medicine_categories')->orderBy('id', 'ASC')->get()->getResult();
        $units = $db->table('units')->orderBy('id', 'ASC')->get()->getResult();

        // 8. TEMPLATE INFORMED CONSENT
        $consentTemplates = $db->table('consent_templates')
                               ->select('consent_templates.*, tindakan.name as tindakan_name')
                               ->join('tindakan', 'tindakan.id = consent_templates.tindakan_id', 'left')
                               ->orderBy('consent_templates.id', 'ASC')
                               ->get()->getResult();

        // 9. DEPARTEMEN & JABATAN
        $departments = $db->table('departments')->orderBy('id', 'ASC')->get()->getResult();
        $jobPositions = $db->table('job_positions')
                           ->select('job_positions.*, departments.name as department_name')
                           ->join('departments', 'departments.id = job_positions.department_id', 'left')
                           ->orderBy('job_positions.id', 'ASC')
                           ->get()->getResult();

        // 10. PEMERIKSAAN LABORATORIUM
        $labTests = $db->table('lab_tests')->orderBy('category', 'ASC')->orderBy('name', 'ASC')->get()->getResult();

        // 11. MITRA PENJAMIN & ASURANSI
        $insuranceProviders = $db->table('insurance_providers')->orderBy('type', 'ASC')->orderBy('name', 'ASC')->get()->getResult();

        // 12. JADWAL PRAKTEK DOKTER
        $doctorSchedules = $db->table('doctor_schedules')
                              ->select('doctor_schedules.*, doctors.name as doctor_name, rooms.name as room_name, rooms.code as room_code')
                              ->join('doctors', 'doctors.id = doctor_schedules.doctor_id', 'left')
                              ->join('rooms', 'rooms.id = doctor_schedules.room_id', 'left')
                              ->orderBy('doctor_schedules.doctor_id', 'ASC')
                              ->orderBy('doctor_schedules.id', 'ASC')
                              ->get()->getResult();

        // 13. DISTRIBUTOR / PEDAGANG BESAR FARMASI (PBF)
        $suppliers = $db->table('suppliers')->orderBy('id', 'ASC')->get()->getResult();

        $data = [
            'title'              => 'Master Data Referensi Klinik Terpadu',
            'active_menu'        => 'system-master-klinik',
            'categories'         => $categories,
            'polikliniks'        => $polikliniks,
            'tindakanList'       => $tindakanList,
            'parentTindakan'     => $parentTindakan,
            'doctors'            => $doctors,
            'feeRules'           => $feeRules,
            'rooms'              => $rooms,
            'beds'               => $beds,
            'medicineCategories' => $medicineCategories,
            'units'              => $units,
            'consentTemplates'   => $consentTemplates,
            'departments'        => $departments,
            'jobPositions'       => $jobPositions,
            'labTests'           => $labTests,
            'insuranceProviders' => $insuranceProviders,
            'doctorSchedules'    => $doctorSchedules,
            'suppliers'          => $suppliers
        ];

        return view('system/master_klinik', $data);
    }

    /**
     * CRUD 1: KATEGORI PELAYANAN
     */
    public function manageCategory()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('categories')->insert([
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description')
            ]);
            session()->setFlashdata('success', 'Kategori Pelayanan berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('categories')->where('id', $id)->update([
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description')
            ]);
            session()->setFlashdata('success', 'Kategori Pelayanan berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $db->table('categories')->where('id', $id)->delete();
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
            session()->setFlashdata('success', 'Kategori Pelayanan berhasil dihapus.');
        }

        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD 2: SUB KATEGORI PELAYANAN (POLIKLINIK)
     */
    public function managePoliklinikBaru()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('polikliniks')->insert([
                'category_id' => $this->request->getPost('category_id') ?: 1, // Default POLI
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            // Sync to legacy polyclinics table
            $db->table('polyclinics')->insert([
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Sub Kategori (Poli) berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $name = strtoupper($this->request->getPost('name'));
            $db->table('polikliniks')->where('id', $id)->update([
                'category_id' => $this->request->getPost('category_id') ?: 1,
                'name'        => $name,
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Sub Kategori (Poli) berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $db->table('polikliniks')->where('id', $id)->delete();
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
            session()->setFlashdata('success', 'Sub Kategori (Poli) berhasil dihapus.');
        }

        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD 3: SUB KATEGORI & SUB KECIL PELAYANAN (TINDAKAN & HARGA)
     */
    public function manageService()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('tindakan')->insert([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'category_id' => $this->request->getPost('category_id') ?: 2, // Default TINDAKAN
                'parent_id'   => $this->request->getPost('parent_id') ?: null,
                'price'       => $this->request->getPost('price') ?: 0.00,
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Tindakan/Pelayanan berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('tindakan')->where('id', $id)->update([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'category_id' => $this->request->getPost('category_id') ?: 2,
                'parent_id'   => $this->request->getPost('parent_id') ?: null,
                'price'       => $this->request->getPost('price') ?: 0.00,
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Tindakan/Pelayanan berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $db->table('tindakan')->where('parent_id', $id)->delete();
            $db->table('tindakan')->where('id', $id)->delete();
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
            session()->setFlashdata('success', 'Tindakan berhasil dihapus.');
        }

        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD 4: DATA DOKTER & FEE PER PASIEN
     */
    public function manageDoctor()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $categoryId = $this->request->getPost('category_id');
            $polyId = ($categoryId == 1) ? $this->request->getPost('polyclinic_id') : null;
            $tindakanId = ($categoryId == 2) ? $this->request->getPost('tindakan_id') : null;
            $feeType = $this->request->getPost('fee_type') === 'fixed_amount' ? 'fixed_amount' : 'percentage';
            $fee = (float)($this->request->getPost('fee_per_pasien') ?: 0.00);
            $prescFeePct = $this->request->getPost('prescription_fee_percent') !== null ? floatval($this->request->getPost('prescription_fee_percent')) : 5.00;

            $db->table('doctors')->insert([
                'nik_employee'             => strtoupper($this->request->getPost('nik_employee')),
                'name'                     => strtoupper($this->request->getPost('name')),
                'category_id'              => $categoryId,
                'polyclinic_id'            => $polyId,
                'tindakan_id'              => $tindakanId,
                'fee_type'                 => $feeType,
                'fee_per_pasien'           => $fee,
                'prescription_fee_percent' => $prescFeePct,
                'sip_number'               => $this->request->getPost('sip_number') ?: 'SIP-DEFAULT',
                'str_number'               => $this->request->getPost('str_number') ?: 'STR-DEFAULT',
                'str_expiry'               => $this->request->getPost('str_expiry') ?: date('Y-12-31', strtotime('+5 years')),
                'status'                   => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Data Dokter & Penugasan berhasil disimpan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $categoryId = $this->request->getPost('category_id');
            $polyId = ($categoryId == 1) ? $this->request->getPost('polyclinic_id') : null;
            $tindakanId = ($categoryId == 2) ? $this->request->getPost('tindakan_id') : null;
            $feeType = $this->request->getPost('fee_type') === 'fixed_amount' ? 'fixed_amount' : 'percentage';
            $fee = (float)($this->request->getPost('fee_per_pasien') ?: 0.00);
            $prescFeePct = $this->request->getPost('prescription_fee_percent') !== null ? floatval($this->request->getPost('prescription_fee_percent')) : 5.00;

            $db->table('doctors')->where('id', $id)->update([
                'nik_employee'             => strtoupper($this->request->getPost('nik_employee')),
                'name'                     => strtoupper($this->request->getPost('name')),
                'category_id'              => $categoryId,
                'polyclinic_id'            => $polyId,
                'tindakan_id'              => $tindakanId,
                'fee_type'                 => $feeType,
                'fee_per_pasien'           => $fee,
                'prescription_fee_percent' => $prescFeePct,
                'sip_number'               => $this->request->getPost('sip_number') ?: 'SIP-DEFAULT',
                'str_number'               => $this->request->getPost('str_number') ?: 'STR-DEFAULT',
                'str_expiry'               => $this->request->getPost('str_expiry') ?: date('Y-12-31', strtotime('+5 years')),
                'status'                   => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Data Dokter & Penugasan berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $db->table('doctors')->where('id', $id)->delete();
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
            session()->setFlashdata('success', 'Data Dokter berhasil dihapus.');
        }

        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * Simpan / Perbarui Tanda Tangan Digital & Stempel Dokter
     */
    public function saveDoctorSignature()
    {
        $db = \Config\Database::connect('default');
        $id = $this->request->getPost('doctor_id');
        $signature = $this->request->getPost('digital_signature');
        $stamp     = $this->request->getPost('stamp_image');

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Dokter tidak valid.']);
        }

        $updateData = [];
        if ($signature !== null) $updateData['digital_signature'] = $signature;
        if ($stamp !== null) $updateData['stamp_image'] = $stamp;

        if (!empty($updateData)) {
            $db->table('doctors')->where('id', $id)->update($updateData);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Tanda Tangan & Stempel Digital Dokter berhasil disimpan ke database referensi.'
        ]);
    }

    /**
     * API JSON: Get List Tindakan Grouped
     */
    public function getTindakanJson()
    {
        $db = \Config\Database::connect('default');
        $parents = $db->table('tindakan')
                      ->select('tindakan.*, categories.name as category_name')
                      ->join('categories', 'categories.id = tindakan.category_id', 'left')
                      ->where('tindakan.parent_id IS NULL')
                      ->where('tindakan.status', 'active')
                      ->get()->getResult();

        foreach ($parents as $parent) {
            $parent->children = $db->table('tindakan')
                                   ->where('parent_id', $parent->id)
                                   ->where('status', 'active')
                                   ->get()->getResult();
        }

        return $this->response->setJSON(['status' => 'ok', 'data' => $parents]);
    }

    /**
     * CRUD: RUANGAN
     */
    public function manageRoom()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('rooms')->insert([
                'code'           => strtoupper($this->request->getPost('code')),
                'name'           => strtoupper($this->request->getPost('name')),
                'type'           => $this->request->getPost('type') ?: 'poli',
                'floor'          => $this->request->getPost('floor') ?: 'Lantai 1',
                'capacity'       => (int)$this->request->getPost('capacity') ?: 1,
                'tariff_per_day' => (float)$this->request->getPost('tariff_per_day') ?: 0.00,
                'status'         => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Master Ruangan berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('rooms')->where('id', $id)->update([
                'code'           => strtoupper($this->request->getPost('code')),
                'name'           => strtoupper($this->request->getPost('name')),
                'type'           => $this->request->getPost('type') ?: 'poli',
                'floor'          => $this->request->getPost('floor') ?: 'Lantai 1',
                'capacity'       => (int)$this->request->getPost('capacity') ?: 1,
                'tariff_per_day' => (float)$this->request->getPost('tariff_per_day') ?: 0.00,
                'status'         => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Master Ruangan berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('rooms')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Master Ruangan berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: TEMPAT TIDUR (BED)
     */
    public function manageBed()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('beds')->insert([
                'room_id'        => (int)$this->request->getPost('room_id'),
                'bed_number'     => strtoupper($this->request->getPost('bed_number')),
                'tariff_per_day' => (float)$this->request->getPost('tariff_per_day') ?: 0.00,
                'status'         => $this->request->getPost('status') ?: 'available'
            ]);
            session()->setFlashdata('success', 'Tempat Tidur berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('beds')->where('id', $id)->update([
                'room_id'        => (int)$this->request->getPost('room_id'),
                'bed_number'     => strtoupper($this->request->getPost('bed_number')),
                'tariff_per_day' => (float)$this->request->getPost('tariff_per_day') ?: 0.00,
                'status'         => $this->request->getPost('status') ?: 'available'
            ]);
            session()->setFlashdata('success', 'Tempat Tidur berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('beds')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Tempat Tidur berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: KATEGORI & GOLONGAN OBAT
     */
    public function manageMedicineCategory()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('medicine_categories')->insert([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'drug_class'  => $this->request->getPost('drug_class') ?: 'obat_keras',
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Kategori Obat berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('medicine_categories')->where('id', $id)->update([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'drug_class'  => $this->request->getPost('drug_class') ?: 'obat_keras',
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Kategori Obat berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('medicine_categories')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Kategori Obat berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: SATUAN UKURAN (UOM)
     */
    public function manageUnit()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('units')->insert([
                'code'     => strtoupper($this->request->getPost('code')),
                'name'     => $this->request->getPost('name'),
                'category' => $this->request->getPost('category') ?: 'farmasi',
                'status'   => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Satuan Ukuran berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('units')->where('id', $id)->update([
                'code'     => strtoupper($this->request->getPost('code')),
                'name'     => $this->request->getPost('name'),
                'category' => $this->request->getPost('category') ?: 'farmasi',
                'status'   => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Satuan Ukuran berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('units')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Satuan Ukuran berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: TEMPLATE INFORMED CONSENT & EDUKASI
     */
    public function manageConsentTemplate()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('consent_templates')->insert([
                'tindakan_id'           => $this->request->getPost('tindakan_id') ?: null,
                'template_title'        => $this->request->getPost('template_title'),
                'diagnosis_indication'  => $this->request->getPost('diagnosis_indication'),
                'procedure_action'      => $this->request->getPost('procedure_action'),
                'goal_benefits'         => $this->request->getPost('goal_benefits'),
                'risks_complications'   => $this->request->getPost('risks_complications'),
                'prognosis'             => $this->request->getPost('prognosis'),
                'alternative_therapies' => $this->request->getPost('alternative_therapies'),
                'status'                => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Template Informed Consent berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('consent_templates')->where('id', $id)->update([
                'tindakan_id'           => $this->request->getPost('tindakan_id') ?: null,
                'template_title'        => $this->request->getPost('template_title'),
                'diagnosis_indication'  => $this->request->getPost('diagnosis_indication'),
                'procedure_action'      => $this->request->getPost('procedure_action'),
                'goal_benefits'         => $this->request->getPost('goal_benefits'),
                'risks_complications'   => $this->request->getPost('risks_complications'),
                'prognosis'             => $this->request->getPost('prognosis'),
                'alternative_therapies' => $this->request->getPost('alternative_therapies'),
                'status'                => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Template Informed Consent berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('consent_templates')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Template Informed Consent berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * API JSON: Get All Active Consent Templates
     */
    public function getConsentTemplatesJson()
    {
        $db = \Config\Database::connect('default');
        $templates = $db->table('consent_templates')
                        ->select('consent_templates.*, tindakan.name as tindakan_name')
                        ->join('tindakan', 'tindakan.id = consent_templates.tindakan_id', 'left')
                        ->where('consent_templates.status', 'active')
                        ->orderBy('consent_templates.template_title', 'ASC')
                        ->get()->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $templates]);
    }

    /**
     * CRUD: DEPARTEMEN SDM
     */
    public function manageDepartment()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('departments')->insert([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Departemen berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('departments')->where('id', $id)->update([
                'code'        => strtoupper($this->request->getPost('code')),
                'name'        => strtoupper($this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Departemen berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('departments')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Departemen berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: JABATAN (JOB POSITION)
     */
    public function manageJobPosition()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('job_positions')->insert([
                'department_id'   => (int)$this->request->getPost('department_id'),
                'code'            => strtoupper($this->request->getPost('code')),
                'title'           => strtoupper($this->request->getPost('title')),
                'base_salary_min' => (float)$this->request->getPost('base_salary_min') ?: 0.00,
                'base_salary_max' => (float)$this->request->getPost('base_salary_max') ?: 0.00,
                'status'          => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Jabatan SDM berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('job_positions')->where('id', $id)->update([
                'department_id'   => (int)$this->request->getPost('department_id'),
                'code'            => strtoupper($this->request->getPost('code')),
                'title'           => strtoupper($this->request->getPost('title')),
                'base_salary_min' => (float)$this->request->getPost('base_salary_min') ?: 0.00,
                'base_salary_max' => (float)$this->request->getPost('base_salary_max') ?: 0.00,
                'status'          => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Jabatan SDM berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('job_positions')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Jabatan SDM berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: PEMERIKSAAN LABORATORIUM
     */
    public function manageLabTest()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('lab_tests')->insert([
                'code'            => strtoupper($this->request->getPost('code')),
                'name'            => $this->request->getPost('name'),
                'category'        => $this->request->getPost('category') ?: 'Hematologi',
                'specimen'        => $this->request->getPost('specimen') ?: 'Darah Vena',
                'reference_range' => $this->request->getPost('reference_range'),
                'unit'            => $this->request->getPost('unit'),
                'price'           => (float)$this->request->getPost('price') ?: 0.00,
                'status'          => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Master Pemeriksaan Laboratorium berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('lab_tests')->where('id', $id)->update([
                'code'            => strtoupper($this->request->getPost('code')),
                'name'            => $this->request->getPost('name'),
                'category'        => $this->request->getPost('category') ?: 'Hematologi',
                'specimen'        => $this->request->getPost('specimen') ?: 'Darah Vena',
                'reference_range' => $this->request->getPost('reference_range'),
                'unit'            => $this->request->getPost('unit'),
                'price'           => (float)$this->request->getPost('price') ?: 0.00,
                'status'          => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Master Pemeriksaan Laboratorium berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('lab_tests')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Master Pemeriksaan Laboratorium berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: MITRA PENJAMIN & ASURANSI KESEHATAN
     */
    public function manageInsuranceProvider()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('insurance_providers')->insert([
                'code'          => strtoupper($this->request->getPost('code')),
                'name'          => $this->request->getPost('name'),
                'type'          => $this->request->getPost('type') ?: 'asuransi_swasta',
                'phone'         => $this->request->getPost('phone'),
                'email'         => $this->request->getPost('email'),
                'claim_address' => $this->request->getPost('claim_address'),
                'pic_name'      => $this->request->getPost('pic_name'),
                'discount_rate' => (float)$this->request->getPost('discount_rate') ?: 0.00,
                'status'        => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Mitra Asuransi / Penjamin berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('insurance_providers')->where('id', $id)->update([
                'code'          => strtoupper($this->request->getPost('code')),
                'name'          => $this->request->getPost('name'),
                'type'          => $this->request->getPost('type') ?: 'asuransi_swasta',
                'phone'         => $this->request->getPost('phone'),
                'email'         => $this->request->getPost('email'),
                'claim_address' => $this->request->getPost('claim_address'),
                'pic_name'      => $this->request->getPost('pic_name'),
                'discount_rate' => (float)$this->request->getPost('discount_rate') ?: 0.00,
                'status'        => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Mitra Asuransi / Penjamin berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('insurance_providers')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Mitra Asuransi / Penjamin berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: JADWAL PRAKTEK DOKTER
     */
    public function manageDoctorSchedule()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('doctor_schedules')->insert([
                'doctor_id'   => (int)$this->request->getPost('doctor_id'),
                'room_id'     => $this->request->getPost('room_id') ?: null,
                'day_of_week' => $this->request->getPost('day_of_week') ?: 'Senin',
                'start_time'  => $this->request->getPost('start_time') ?: '08:00:00',
                'end_time'    => $this->request->getPost('end_time') ?: '16:00:00',
                'max_quota'   => (int)$this->request->getPost('max_quota') ?: 35,
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Jadwal Praktek Dokter berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('doctor_schedules')->where('id', $id)->update([
                'doctor_id'   => (int)$this->request->getPost('doctor_id'),
                'room_id'     => $this->request->getPost('room_id') ?: null,
                'day_of_week' => $this->request->getPost('day_of_week') ?: 'Senin',
                'start_time'  => $this->request->getPost('start_time') ?: '08:00:00',
                'end_time'    => $this->request->getPost('end_time') ?: '16:00:00',
                'max_quota'   => (int)$this->request->getPost('max_quota') ?: 35,
                'status'      => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Jadwal Praktek Dokter berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('doctor_schedules')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Jadwal Praktek Dokter berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }

    /**
     * CRUD: DISTRIBUTOR / PEDAGANG BESAR FARMASI (PBF)
     */
    public function manageSupplier()
    {
        $db = \Config\Database::connect('default');
        $action = $this->request->getPost('action');

        if ($action === 'create') {
            $db->table('suppliers')->insert([
                'code'         => strtoupper($this->request->getPost('code')),
                'name'         => $this->request->getPost('name'),
                'address'      => $this->request->getPost('address') ?: '-',
                'phone'        => $this->request->getPost('phone') ?: '-',
                'pic_name'     => $this->request->getPost('pic_name'),
                'email'        => $this->request->getPost('email'),
                'bank_name'    => $this->request->getPost('bank_name'),
                'bank_account' => $this->request->getPost('bank_account'),
                'status'       => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Data Distributor / PBF berhasil ditambahkan.');
        } elseif ($action === 'update') {
            $id = $this->request->getPost('id');
            $db->table('suppliers')->where('id', $id)->update([
                'code'         => strtoupper($this->request->getPost('code')),
                'name'         => $this->request->getPost('name'),
                'address'      => $this->request->getPost('address') ?: '-',
                'phone'        => $this->request->getPost('phone') ?: '-',
                'pic_name'     => $this->request->getPost('pic_name'),
                'email'        => $this->request->getPost('email'),
                'bank_name'    => $this->request->getPost('bank_name'),
                'bank_account' => $this->request->getPost('bank_account'),
                'status'       => $this->request->getPost('status') ?: 'active'
            ]);
            session()->setFlashdata('success', 'Data Distributor / PBF berhasil diperbarui.');
        } elseif ($action === 'delete') {
            $id = $this->request->getPost('id');
            $db->table('suppliers')->where('id', $id)->delete();
            session()->setFlashdata('success', 'Data Distributor / PBF berhasil dihapus.');
        }
        return redirect()->to(base_url('system/master-klinik'));
    }
}