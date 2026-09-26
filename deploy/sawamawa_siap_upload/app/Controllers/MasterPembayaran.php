<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MasterPembayaran extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $methods = $db->table('payment_methods')
                      ->orderBy('category', 'ASC')
                      ->orderBy('id', 'ASC')
                      ->get()
                      ->getResult();

        $data = [
            'title'       => 'Master Metode Pembayaran',
            'active_menu' => 'master-pembayaran',
            'methods'     => $methods
        ];

        return view('master/pembayaran', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();

        $id = $this->request->getPost('id');
        $code = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $this->request->getPost('code') ?? '')));
        $name = trim($this->request->getPost('name') ?? '');
        $category = $this->request->getPost('category') ?? 'cash';
        $accNo = trim($this->request->getPost('account_number') ?? '');
        $accName = trim($this->request->getPost('account_name') ?? '');
        $notes = trim($this->request->getPost('notes') ?? '');
        $isActive = $this->request->getPost('is_active') !== null ? 1 : 0;

        if (empty($code) || empty($name)) {
            return redirect()->back()->with('error', 'Kode dan Nama Metode Pembayaran wajib diisi.');
        }

        $saveData = [
            'code'           => $code,
            'name'           => $name,
            'category'       => $category,
            'account_number' => $accNo,
            'account_name'   => $accName,
            'notes'          => $notes,
            'is_active'      => $isActive,
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        if (!empty($id)) {
            $db->table('payment_methods')->where('id', $id)->update($saveData);
            session()->setFlashdata('success', "Metode pembayaran '{$name}' berhasil diperbarui.");
        } else {
            // Check unique code
            $exist = $db->table('payment_methods')->where('code', $code)->get()->getRow();
            if ($exist) {
                return redirect()->back()->with('error', "Kode metode pembayaran '{$code}' sudah ada.");
            }

            $saveData['created_at'] = date('Y-m-d H:i:s');
            $db->table('payment_methods')->insert($saveData);
            session()->setFlashdata('success', "Metode pembayaran baru '{$name}' berhasil ditambahkan.");
        }

        return redirect()->to(base_url('system/master-pembayaran'));
    }

    public function toggle($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('payment_methods')->where('id', $id)->get()->getRow();
        if ($row) {
            $newStatus = $row->is_active == 1 ? 0 : 1;
            $db->table('payment_methods')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            session()->setFlashdata('success', "Metode pembayaran '{$row->name}' berhasil {$statusText}.");
        }
        return redirect()->to(base_url('system/master-pembayaran'));
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('payment_methods')->where('id', $id)->get()->getRow();
        if ($row) {
            $db->table('payment_methods')->where('id', $id)->delete();
            session()->setFlashdata('success', "Metode pembayaran '{$row->name}' berhasil dihapus.");
        }
        return redirect()->to(base_url('system/master-pembayaran'));
    }

    public function getJson()
    {
        $db = \Config\Database::connect();
        $methods = $db->table('payment_methods')
                      ->where('is_active', 1)
                      ->orderBy('category', 'ASC')
                      ->orderBy('id', 'ASC')
                      ->get()
                      ->getResult();

        return $this->response->setJSON($methods);
    }
}
