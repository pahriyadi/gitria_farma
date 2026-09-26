<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MasterIcd extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // AJAX Handler untuk DataTables Server-Side ICD-9 (Prosedur Medis)
        if ($this->request->getGet('tab') === 'icd9' && $this->request->getGet('draw')) {
            return datatable_server_side('master_icd9', [
                0 => 'code',
                1 => 'name_id',
                2 => 'name_en',
                3 => 'category',
                4 => 'status',
                5 => 'id'
            ], [
                'search_columns' => ['code', 'name_id', 'name_en', 'category'],
                'default_order'  => ['code', 'ASC'],
                'row_formatter'  => function($row, $no) {
                    $statusBadge = $row->status === 'active' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
                    $actions = '
                        <button class="btn btn-outline-info btn-xs btn-edit-icd9 font-weight-bold mr-1"
                                data-id="' . $row->id . '"
                                data-code="' . esc($row->code) . '"
                                data-nameid="' . esc($row->name_id) . '"
                                data-nameen="' . esc($row->name_en) . '"
                                data-category="' . esc($row->category) . '"
                                data-status="' . esc($row->status) . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . base_url('system/master-icd-9-delete') . '" method="post" class="d-inline" onsubmit="return confirm(\'Hapus kode ICD-9 ' . esc($row->code) . '?\');">
                            ' . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <button type="submit" class="btn btn-outline-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    ';
                    return [
                        '<span class="badge badge-teal font-monospace font-weight-bold">' . esc($row->code) . '</span>',
                        '<strong>' . esc($row->name_id) . '</strong>',
                        '<span class="text-muted font-italic">' . esc($row->name_en) . '</span>',
                        '<span class="badge badge-light border text-xs">' . esc($row->category) . '</span>',
                        '<div class="text-center">' . $statusBadge . '</div>',
                        '<div class="text-center text-nowrap">' . $actions . '</div>'
                    ];
                }
            ]);
        }

        // AJAX Handler untuk DataTables Server-Side ICD-10 (Diagnosa Medis)
        if ($this->request->getGet('draw')) {
            return datatable_server_side('master_icd10', [
                0 => 'code',
                1 => 'name_id',
                2 => 'name_en',
                3 => 'category',
                4 => 'status',
                5 => 'id'
            ], [
                'search_columns' => ['code', 'name_id', 'name_en', 'category'],
                'default_order'  => ['code', 'ASC'],
                'row_formatter'  => function($row, $no) {
                    $statusBadge = $row->status === 'active' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>';
                    $actions = '
                        <button class="btn btn-outline-info btn-xs btn-edit-icd10 font-weight-bold mr-1"
                                data-id="' . $row->id . '"
                                data-code="' . esc($row->code) . '"
                                data-nameid="' . esc($row->name_id) . '"
                                data-nameen="' . esc($row->name_en) . '"
                                data-category="' . esc($row->category) . '"
                                data-status="' . esc($row->status) . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="' . base_url('system/master-icd-10-delete') . '" method="post" class="d-inline" onsubmit="return confirm(\'Hapus kode ICD-10 ' . esc($row->code) . '?\');">
                            ' . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <button type="submit" class="btn btn-outline-danger btn-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    ';
                    return [
                        '<span class="badge badge-teal font-monospace font-weight-bold">' . esc($row->code) . '</span>',
                        '<strong>' . esc($row->name_id) . '</strong>',
                        '<span class="text-muted font-italic">' . esc($row->name_en) . '</span>',
                        '<span class="badge badge-light border text-xs">' . esc($row->category) . '</span>',
                        '<div class="text-center">' . $statusBadge . '</div>',
                        '<div class="text-center text-nowrap">' . $actions . '</div>'
                    ];
                }
            ]);
        }

        $totalIcd10 = $db->table('master_icd10')->countAllResults();
        $totalIcd9  = $db->table('master_icd9')->countAllResults();

        $data = [
            'title'       => 'Master Kode ICD (Diagnosa & Prosedur Medis)',
            'active_menu' => 'system-master-icd',
            'totalIcd10'  => $totalIcd10,
            'totalIcd9'   => $totalIcd9
        ];

        return view('system/master_icd', $data);
    }

    public function saveIcd10()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $data = [
            'code'     => strtoupper(trim($this->request->getPost('code'))),
            'name_id'  => trim($this->request->getPost('name_id')),
            'name_en'  => trim($this->request->getPost('name_en')),
            'category' => trim($this->request->getPost('category')),
            'status'   => $this->request->getPost('status') ?: 'active'
        ];

        if (!empty($id)) {
            $db->table('master_icd10')->where('id', $id)->update($data);
            session()->setFlashdata('success', 'Kode Diagnosa ICD-10 (' . $data['code'] . ') berhasil diperbarui.');
        } else {
            // Check unique
            $exists = $db->table('master_icd10')->where('code', $data['code'])->get()->getRow();
            if ($exists) {
                session()->setFlashdata('error', 'Kode ICD-10 ' . $data['code'] . ' sudah terdaftar.');
                return redirect()->to(base_url('system/master-icd'));
            }
            $db->table('master_icd10')->insert($data);
            session()->setFlashdata('success', 'Kode Diagnosa ICD-10 (' . $data['code'] . ') berhasil ditambahkan.');
        }

        return redirect()->to(base_url('system/master-icd'));
    }

    public function deleteIcd10()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $db->table('master_icd10')->where('id', $id)->delete();
        session()->setFlashdata('success', 'Kode Diagnosa ICD-10 berhasil dihapus.');

        return redirect()->to(base_url('system/master-icd'));
    }

    public function saveIcd9()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $data = [
            'code'     => strtoupper(trim($this->request->getPost('code'))),
            'name_id'  => trim($this->request->getPost('name_id')),
            'name_en'  => trim($this->request->getPost('name_en')),
            'category' => trim($this->request->getPost('category')),
            'status'   => $this->request->getPost('status') ?: 'active'
        ];

        if (!empty($id)) {
            $db->table('master_icd9')->where('id', $id)->update($data);
            session()->setFlashdata('success', 'Kode Prosedur ICD-9-CM (' . $data['code'] . ') berhasil diperbarui.');
        } else {
            // Check unique
            $exists = $db->table('master_icd9')->where('code', $data['code'])->get()->getRow();
            if ($exists) {
                session()->setFlashdata('error', 'Kode ICD-9 ' . $data['code'] . ' sudah terdaftar.');
                return redirect()->to(base_url('system/master-icd'));
            }
            $db->table('master_icd9')->insert($data);
            session()->setFlashdata('success', 'Kode Prosedur ICD-9-CM (' . $data['code'] . ') berhasil ditambahkan.');
        }

        return redirect()->to(base_url('system/master-icd'));
    }

    public function deleteIcd9()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $db->table('master_icd9')->where('id', $id)->delete();
        session()->setFlashdata('success', 'Kode Prosedur ICD-9-CM berhasil dihapus.');

        return redirect()->to(base_url('system/master-icd'));
    }

    public function searchJson()
    {
        $db = \Config\Database::connect();
        $type = $this->request->getGet('type') ?: 'icd10';
        $q = trim($this->request->getGet('q') ?? '');

        if ($type === 'icd10') {
            $builder = $db->table('master_icd10')->where('status', 'active');
            if (!empty($q)) {
                $builder->groupStart()
                        ->like('code', $q)
                        ->orLike('name_id', $q)
                        ->orLike('name_en', $q)
                        ->groupEnd();
            }
            $results = $builder->limit(30)->get()->getResult();
        } else {
            $builder = $db->table('master_icd9')->where('status', 'active');
            if (!empty($q)) {
                $builder->groupStart()
                        ->like('code', $q)
                        ->orLike('name_id', $q)
                        ->orLike('name_en', $q)
                        ->groupEnd();
            }
            $results = $builder->limit(30)->get()->getResult();
        }

        return $this->response->setJSON(['status' => 'ok', 'data' => $results]);
    }
}
