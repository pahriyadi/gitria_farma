<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Help extends BaseController
{
    /**
     * Pusat Bantuan, Alur Sistem & Dokumentasi Interaktif (Berbasis Database)
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $workflows = $db->table('system_documentations')
                        ->where('category', 'workflow')
                        ->orderBy('order_num', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->get()
                        ->getResult();

        $roleGuides = $db->table('system_documentations')
                         ->where('category', 'role_guide')
                         ->orderBy('order_num', 'ASC')
                         ->orderBy('id', 'ASC')
                         ->get()
                         ->getResult();

        $faqs = $db->table('system_documentations')
                   ->where('category', 'faq')
                   ->orderBy('order_num', 'ASC')
                   ->orderBy('id', 'ASC')
                   ->get()
                   ->getResult();

        $policies = $db->table('system_documentations')
                       ->where('category', 'security_policy')
                       ->orderBy('order_num', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->get()
                       ->getResult();

        $latestUpdate = $db->table('system_updates')->orderBy('id', 'DESC')->limit(1)->get()->getRow();

        $data = [
            'title'        => 'Pusat Bantuan & Panduan Alur Sistem',
            'active_menu'  => 'bantuan',
            'workflows'    => $workflows,
            'roleGuides'   => $roleGuides,
            'faqs'         => $faqs,
            'policies'     => $policies,
            'latestUpdate' => $latestUpdate
        ];

        return view('help/index', $data);
    }

    /**
     * Simpan atau Edit Dokumentasi / Alur Sistem (CRUD)
     */
    public function save()
    {
        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $category   = trim($this->request->getPost('category')) ?: 'workflow';
        $title      = trim($this->request->getPost('title'));
        $targetRole = trim($this->request->getPost('target_role')) ?: 'Semua Peran';
        $badgeColor = trim($this->request->getPost('badge_color')) ?: 'teal';
        $icon       = trim($this->request->getPost('icon')) ?: 'fas fa-circle-question';
        $summary    = trim($this->request->getPost('summary'));
        $content    = trim($this->request->getPost('content'));
        $orderNum   = intval($this->request->getPost('order_num')) ?: 0;
        $isPublished= $this->request->getPost('is_published') !== null ? 1 : 0;

        // Process flow steps if provided as JSON or newline list
        $rawSteps = trim($this->request->getPost('flow_steps'));
        $flowStepsJson = null;
        if (!empty($rawSteps)) {
            // Check if already valid JSON
            $decoded = json_decode($rawSteps, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $flowStepsJson = json_encode($decoded);
            } else {
                // Parse line by line
                $lines = explode("\n", str_replace("\r", "", $rawSteps));
                $parsedSteps = [];
                $icons = ['fas fa-mobile-screen-button', 'fas fa-heart-pulse', 'fas fa-user-doctor', 'fas fa-receipt', 'fas fa-boxes-stacked', 'fas fa-check-double'];
                $colors = ['text-teal', 'text-danger', 'text-primary', 'text-warning', 'text-success', 'text-info'];
                $i = 0;
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (!empty($trimmed)) {
                        $parts = explode('|', $trimmed);
                        $stepTitle = trim($parts[0]);
                        $stepSub   = isset($parts[1]) ? trim($parts[1]) : '';
                        $stepIcon  = isset($parts[2]) ? trim($parts[2]) : ($icons[$i % count($icons)]);
                        $stepColor = isset($parts[3]) ? trim($parts[3]) : ($colors[$i % count($colors)]);
                        $parsedSteps[] = [
                            'title' => $stepTitle,
                            'sub'   => $stepSub,
                            'icon'  => $stepIcon,
                            'color' => $stepColor
                        ];
                        $i++;
                    }
                }
                $flowStepsJson = json_encode($parsedSteps);
            }
        }

        $data = [
            'category'     => $category,
            'title'        => $title,
            'target_role'  => $targetRole,
            'badge_color'  => $badgeColor,
            'icon'         => $icon,
            'flow_steps'   => $flowStepsJson,
            'summary'      => $summary,
            'content'      => $content,
            'order_num'    => $orderNum,
            'is_published' => $isPublished,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $db->table('system_documentations')->where('id', $id)->update($data);
            session()->setFlashdata('success', 'Dokumentasi / Alur sistem berhasil diperbarui!');
        } else {
            $data['created_by'] = session('user_id') ?: 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('system_documentations')->insert($data);
            session()->setFlashdata('success', 'Panduan / Alur baru berhasil ditambahkan ke database!');
        }

        return redirect()->to(base_url('bantuan'));
    }

    /**
     * Hapus Dokumentasi
     */
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('system_documentations')->where('id', $id)->delete();
        session()->setFlashdata('success', 'Panduan / Alur sistem berhasil dihapus!');
        return redirect()->to(base_url('bantuan'));
    }

    /**
     * Toggle Status Publikasi
     */
    public function toggle($id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('system_documentations')->where('id', $id)->get()->getRow();
        if ($row) {
            $newStatus = $row->is_published ? 0 : 1;
            $db->table('system_documentations')->where('id', $id)->update([
                'is_published' => $newStatus,
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
            session()->setFlashdata('success', 'Status publikasi panduan berhasil diubah!');
        }
        return redirect()->to(base_url('bantuan'));
    }

    /**
     * Versi Cetak / Unduh Panduan Operasional dari Database
     */
    public function cetak()
    {
        $db = \Config\Database::connect();
        $workflows = $db->table('system_documentations')->where('category', 'workflow')->where('is_published', 1)->orderBy('order_num', 'ASC')->get()->getResult();
        $roleGuides = $db->table('system_documentations')->where('category', 'role_guide')->where('is_published', 1)->orderBy('order_num', 'ASC')->get()->getResult();
        $faqs = $db->table('system_documentations')->where('category', 'faq')->where('is_published', 1)->orderBy('order_num', 'ASC')->get()->getResult();

        $data = [
            'title'      => 'Panduan Operasional & Dokumentasi Alur Sistem ERP Sawamawa',
            'workflows'  => $workflows,
            'roleGuides' => $roleGuides,
            'faqs'       => $faqs
        ];

        return view('help/cetak', $data);
    }
}
