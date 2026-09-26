<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\JournalEngine;

class Accounting extends BaseController
{
    protected $journalEngine;

    public function __construct()
    {
        $this->journalEngine = new JournalEngine();
    }

    public function coa()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $code = trim($this->request->getPost('code'));
            $name = trim($this->request->getPost('name'));

            if (empty($code) || empty($name)) {
                session()->setFlashdata('error', 'Kode Akun dan Nama Rekening Akun wajib diisi.');
                return redirect()->to(base_url('accounting/coa'));
            }

            // Verifikasi apakah kode akun sudah ada
            $existing = $db->table('accounts')->where('code', $code)->get()->getRow();
            if ($existing) {
                session()->setFlashdata('error', "Gagal mendaftarkan! Nomor/Kode Akun '{$code}' sudah terdaftar di sistem atas nama akun '{$existing->name}'. Silakan gunakan nomor akun lain atau edit akun tersebut.");
                return redirect()->to(base_url('accounting/coa'));
            }

            $coaData = [
                'code'           => $code,
                'name'           => $name,
                'type'           => $this->request->getPost('type'),
                'normal_balance' => $this->request->getPost('normal_balance'),
                'balance'        => floatval($this->request->getPost('balance')),
                'parent_id'      => null
            ];

            try {
                $db->table('accounts')->insert($coaData);
                session()->setFlashdata('success', "Akun COA baru [{$code}] {$name} berhasil didaftarkan.");
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan akun: ' . $e->getMessage());
            }

            return redirect()->to(base_url('accounting/coa'));
        }

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        $data = [
            'title'       => 'Bagan Akun (Chart of Accounts)',
            'active_menu' => 'accounting-coa',
            'accounts'    => $accounts
        ];

        return view('accounting/coa', $data);
    }

    public function updateCoa($id)
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $code = trim($this->request->getPost('code'));
            $name = trim($this->request->getPost('name'));

            if (empty($code) || empty($name)) {
                session()->setFlashdata('error', 'Kode Akun dan Nama Rekening Akun wajib diisi.');
                return redirect()->to(base_url('accounting/coa'));
            }

            // Verifikasi apakah kode akun sudah digunakan akun lain
            $existing = $db->table('accounts')
                           ->where('code', $code)
                           ->where('id !=', $id)
                           ->get()->getRow();

            if ($existing) {
                session()->setFlashdata('error', "Gagal mengubah! Nomor/Kode Akun '{$code}' sudah digunakan oleh rekening akun lain ('{$existing->name}'). Silakan gunakan nomor akun lain.");
                return redirect()->to(base_url('accounting/coa'));
            }

            $coaData = [
                'code'           => $code,
                'name'           => $name,
                'type'           => $this->request->getPost('type'),
                'normal_balance' => $this->request->getPost('normal_balance')
            ];

            try {
                $db->table('accounts')->where('id', $id)->update($coaData);
                session()->setFlashdata('success', "Data rekening akun [{$code}] {$name} berhasil diperbarui.");
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Terjadi kesalahan saat memperbarui akun: ' . $e->getMessage());
            }
        }
        return redirect()->to(base_url('accounting/coa'));
    }

    /**
     * API AJAX untuk verifikasi ketersediaan nomor/kode akun secara real-time
     */
    public function checkCodeExists()
    {
        $db = \Config\Database::connect('default');
        $code = trim($this->request->getGet('code'));
        $excludeId = (int)$this->request->getGet('exclude_id');

        if (empty($code)) {
            return $this->response->setJSON(['status' => 'empty', 'message' => '']);
        }

        $builder = $db->table('accounts')->where('code', $code);
        if (!empty($excludeId)) {
            $builder->where('id !=', $excludeId);
        }
        $existing = $builder->get()->getRow();

        if ($existing) {
            return $this->response->setJSON([
                'status'    => 'duplicate',
                'is_unique' => false,
                'name'      => $existing->name,
                'message'   => "Nomor akun [{$code}] sudah terdaftar untuk '{$existing->name}'."
            ]);
        }

        return $this->response->setJSON([
            'status'    => 'available',
            'is_unique' => true,
            'message'   => "Nomor akun [{$code}] tersedia."
        ]);
    }

    public function jurnal()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $db->transStart();

            $date = $this->request->getPost('entry_date');
            $desc = $this->request->getPost('description');
            
            $debits = $this->request->getPost('debit_items'); // array of [account_id, amount]
            $credits = $this->request->getPost('credit_items'); // array of [account_id, amount]

            // Validate balance
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($debits as $d) $totalDebit += floatval($d['amount']);
            foreach ($credits as $c) $totalCredit += floatval($c['amount']);

            if (abs($totalDebit - $totalCredit) > 0.01) {
                $db->transRollback();
                session()->setFlashdata('error', 'Gagal membukukan: Jumlah Debet dan Kredit harus seimbang (balance).');
                return redirect()->to(base_url('accounting/jurnal'));
            }

            // Generate Journal Number
            $today = date('Ymd');
            $lastJ = $db->table('journal_entries')
                        ->where('DATE(created_at)', date('Y-m-d'))
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();
            $nextNum = 1;
            if ($lastJ && preg_match('/JV-\d+-(\d+)/', $lastJ->journal_no, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            }
            $journalNo = 'JV-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // Insert Header
            $db->table('journal_entries')->insert([
                'journal_no'    => $journalNo,
                'entry_date'    => $date,
                'source_module' => 'Accounting',
                'reference_id'  => 0,
                'description'   => $desc,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $journalId = $db->insertID();

            // Insert Debits details & Update accounts
            foreach ($debits as $d) {
                if (empty($d['account_id']) || floatval($d['amount']) <= 0) continue;
                $val = floatval($d['amount']);

                $db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $d['account_id'],
                    'debit'      => $val,
                    'credit'     => 0.00
                ]);

                $acc = $db->table('accounts')->where('id', $d['account_id'])->get()->getRow();
                $newBal = ($acc->normal_balance === 'debit') ? ($acc->balance + $val) : ($acc->balance - $val);
                $db->table('accounts')->where('id', $d['account_id'])->update(['balance' => $newBal]);
            }

            // Insert Credits details & Update accounts
            foreach ($credits as $c) {
                if (empty($c['account_id']) || floatval($c['amount']) <= 0) continue;
                $val = floatval($c['amount']);

                $db->table('journal_entry_details')->insert([
                    'journal_id' => $journalId,
                    'account_id' => $c['account_id'],
                    'debit'      => 0.00,
                    'credit'     => $val
                ]);

                $acc = $db->table('accounts')->where('id', $c['account_id'])->get()->getRow();
                $newBal = ($acc->normal_balance === 'credit') ? ($acc->balance + $val) : ($acc->balance - $val);
                $db->table('accounts')->where('id', $c['account_id'])->update(['balance' => $newBal]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Gagal menyimpan jurnal penyesuaian.');
            } else {
                session()->setFlashdata('success', 'Jurnal penyesuaian berhasil dibukukan. No: ' . $journalNo);
            }
            return redirect()->to(base_url('accounting/jurnal'));
        }

        // Auto-sync unposted transactions from Resto POS and OTC Pharmacy (hanya saat load halaman biasa, bukan AJAX DataTables)
        if (!$this->request->getGet('draw')) {
            $this->journalEngine->syncUnpostedTransactions();
        }

        // AJAX Handler untuk DataTables Server-Side Jurnal Umum
        if ($this->request->getGet('draw')) {
            $filterModule = $this->request->getGet('source_module');
            $filterStart  = $this->request->getGet('start_date');
            $filterEnd    = $this->request->getGet('end_date');

            $where = [];
            if (!empty($filterModule)) {
                $where['journal_entries.source_module'] = $filterModule;
            }
            if (!empty($filterStart)) {
                $where['journal_entries.entry_date >='] = $filterStart;
            }
            if (!empty($filterEnd)) {
                $where['journal_entries.entry_date <='] = $filterEnd;
            }

            return datatable_server_side('journal_entries', [
                0 => 'journal_entries.created_at',
                1 => 'journal_entries.journal_no',
                2 => 'journal_entries.description',
                3 => 'journal_entries.id',
                4 => 'journal_entries.id',
                5 => 'journal_entries.id',
                6 => 'journal_entries.id'
            ], [
                'where'           => $where,
                'search_columns'  => ['journal_entries.journal_no', 'journal_entries.source_module', 'journal_entries.description'],
                'default_order'   => ['journal_entries.created_at', 'DESC'],
                'secondary_order' => ['journal_entries.id', 'DESC'],
                'row_formatter'   => function($j, $no) use ($db) {
                    $details = $db->table('journal_entry_details')
                                  ->select('journal_entry_details.*, accounts.code as account_code, accounts.name as account_name, accounts.balance as account_balance')
                                  ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                                  ->where('journal_id', $j->id)
                                  ->get()
                                  ->getResult();

                    // Ambil data referensi transaksi untuk rincian sub-item per akun
                    $billingDetails = null;
                    $doctorName = null;
                    $pharmacyDetails = null;
                    $restoDetails = null;

                    if (in_array($j->source_module, ['Kasir Utama', 'Kasir', 'Keuangan']) && $j->reference_id > 0) {
                        $bill = $db->table('billing_transactions')->where('id', $j->reference_id)->get()->getRow();
                        if ($bill) {
                            $billingDetails = $db->table('billing_details')->where('billing_id', $bill->id)->get()->getResult();
                            if ($bill->visit_id) {
                                $visit = $db->table('patient_visits')
                                            ->select('patient_visits.*, doctors.name as doctor_name')
                                            ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                                            ->where('patient_visits.id', $bill->visit_id)
                                            ->get()
                                            ->getRow();
                                $doctorName = $visit ? $visit->doctor_name : null;
                            }
                        }
                    } elseif (($j->source_module === 'Farmasi Apotek' || stripos($j->source_module, 'Apotek') !== false) && $j->reference_id > 0) {
                        $pharmacyDetails = $db->table('pharmacy_sale_details psd')
                                              ->select('psd.qty, psd.price, m.name')
                                              ->join('medicines m', 'm.id = psd.medicine_id', 'left')
                                              ->where('psd.sale_id', $j->reference_id)
                                              ->get()
                                              ->getResult();
                    } elseif ($j->source_module === 'Resto POS' && $j->reference_id > 0) {
                        $restoDetails = $db->table('restaurant_order_details rod')
                                           ->select('rod.qty, rod.price, rm.name')
                                           ->join('restaurant_menus rm', 'rm.id = rod.menu_id', 'left')
                                           ->where('rod.order_id', $j->reference_id)
                                           ->get()
                                           ->getResult();
                    }

                    $accHtml = '<ul class="list-unstyled mb-0" style="font-size: 12px;">';
                    $debHtml = '<ul class="list-unstyled mb-0 font-monospace text-right" style="font-size: 12px;">';
                    $crdHtml = '<ul class="list-unstyled mb-0 font-monospace text-right" style="font-size: 12px;">';
                    $salHtml = '<ul class="list-unstyled mb-0 font-monospace text-right" style="font-size: 12px;">';

                    foreach ($details as $d) {
                        $subRows = [];

                        // 4-101: Pendapatan Pelayanan Klinik (Dokter & Jasa Medis)
                        if (strpos($d->account_code, '4-101') === 0 || stripos($d->account_name, 'Pelayanan Klinik') !== false) {
                            if (!empty($doctorName)) {
                                $cleanDoc = preg_replace('/^dr\.?\s*/i', '', $doctorName);
                                $subRows[] = [
                                    'label'  => '<span class="text-primary font-weight-bold"><i class="fas fa-user-doctor mr-1"></i>dr. ' . esc($cleanDoc) . '</span>',
                                    'amount' => null
                                ];
                            }
                            if ($billingDetails) {
                                foreach ($billingDetails as $bd) {
                                    if ($bd->item_type === 'medis') {
                                        $subRows[] = [
                                            'label'  => '<span class="text-secondary"><i class="fas fa-stethoscope mr-1"></i>' . esc($bd->item_name) . ' (' . (int)$bd->qty . 'x)</span>',
                                            'amount' => (float)$bd->subtotal
                                        ];
                                    }
                                }
                            }
                        }
                        // 4-102: Pendapatan Apotek Farmasi (Rincian Obat dihapus sesuai request, sudah di uraian)
                        elseif (strpos($d->account_code, '4-102') === 0 || stripos($d->account_name, 'Apotek') !== false || stripos($d->account_name, 'Farmasi') !== false) {
                            // No subRows needed
                        }
                        // 4-103 / 4-104: Pendapatan Resto / Nutrisi
                        elseif (strpos($d->account_code, '4-103') === 0 || strpos($d->account_code, '4-104') === 0 || stripos($d->account_name, 'Resto') !== false) {
                            if ($billingDetails) {
                                foreach ($billingDetails as $bd) {
                                    if ($bd->item_type === 'resto') {
                                        $subRows[] = [
                                            'label'  => '<span class="text-warning font-weight-500"><i class="fas fa-utensils mr-1"></i>' . esc($bd->item_name) . ' (' . (int)$bd->qty . 'x)</span>',
                                            'amount' => (float)$bd->subtotal
                                        ];
                                    }
                                }
                            } elseif (!empty($restoDetails)) {
                                foreach ($restoDetails as $rd) {
                                    $subRows[] = [
                                        'label'  => '<span class="text-warning font-weight-500"><i class="fas fa-utensils mr-1"></i>' . esc($rd->name ?: 'Menu') . ' (' . (int)$rd->qty . 'x)</span>',
                                        'amount' => (float)$rd->price * (int)$rd->qty
                                    ];
                                }
                            }
                        }
                        // 6-102 / 5-xxx: Beban Komisi Dokter
                        elseif (strpos($d->account_code, '6-102') === 0 || stripos($d->account_name, 'Komisi') !== false) {
                            if (!empty($doctorName)) {
                                $cleanDoc = preg_replace('/^dr\.?\s*/i', '', $doctorName);
                                $subRows[] = [
                                    'label'  => '<span class="text-info font-weight-bold"><i class="fas fa-user-doctor mr-1"></i>dr. ' . esc($cleanDoc) . '</span>',
                                    'amount' => null
                                ];
                            }
                        }

                        // Baris Utama Akun COA
                        $pad = $d->credit > 0 ? 'pl-3' : '';
                        $accHtml .= '<li class="' . $pad . '"><strong>' . esc($d->account_code) . '</strong> - ' . esc($d->account_name) . '</li>';
                        $debHtml .= '<li class="text-success font-weight-bold">' . ($d->debit > 0 ? 'Rp ' . number_format($d->debit, 2, ',', '.') : '-') . '</li>';
                        $crdHtml .= '<li class="text-danger font-weight-bold">' . ($d->credit > 0 ? 'Rp ' . number_format($d->credit, 2, ',', '.') : '-') . '</li>';
                        $salHtml .= '<li class="text-teal font-weight-bold">Rp ' . number_format($d->account_balance ?? 0, 2, ',', '.') . '</li>';

                        // Baris Rincian Sub-Item (Sejajar di semua kolom Debet & Kredit)
                        if (!empty($subRows)) {
                            foreach ($subRows as $sr) {
                                $amtFormatted = ($sr['amount'] !== null && $sr['amount'] > 0) ? 'Rp ' . number_format($sr['amount'], 2, ',', '.') : '-';
                                
                                $accHtml .= '<li class="pl-4 text-nowrap" style="font-size:11px; color:#495057; line-height:1.4;">&bull; ' . $sr['label'] . '</li>';
                                
                                if ($d->debit > 0) {
                                    $debHtml .= '<li class="text-secondary font-weight-500" style="font-size:11px; line-height:1.4;">' . $amtFormatted . '</li>';
                                    $crdHtml .= '<li class="text-muted" style="font-size:11px; line-height:1.4;">-</li>';
                                } else {
                                    $debHtml .= '<li class="text-muted" style="font-size:11px; line-height:1.4;">-</li>';
                                    $crdHtml .= '<li class="text-secondary font-weight-500" style="font-size:11px; line-height:1.4;">' . $amtFormatted . '</li>';
                                }
                                $salHtml .= '<li class="text-muted" style="font-size:11px; line-height:1.4;">&nbsp;</li>';
                            }
                        }
                    }
                    $accHtml .= '</ul>';
                    $debHtml .= '</ul>';
                    $crdHtml .= '</ul>';
                    $salHtml .= '</ul>';

                    // Badge Modul Terpadu yang informatif
                    $modulBadge = '<span class="badge badge-light border text-xs">' . esc($j->source_module) . '</span>';
                    if (in_array($j->source_module, ['Kasir Utama', 'Kasir', 'Keuangan'])) {
                        if (stripos($j->description, 'Farmasi') !== false || stripos($j->description, 'Resep') !== false) {
                            $modulBadge = '<span class="badge badge-teal text-xs shadow-none"><i class="fas fa-prescription-bottle-medical mr-1"></i>Kasir &amp; Apotek</span>';
                        } elseif (stripos($j->description, 'Medis') !== false || stripos($j->description, 'Klinik') !== false) {
                            $modulBadge = '<span class="badge badge-info text-xs shadow-none"><i class="fas fa-stethoscope mr-1"></i>Kasir &amp; Poliklinik</span>';
                        } elseif (stripos($j->description, 'Resto') !== false || stripos($j->description, 'Nutrisi') !== false) {
                            $modulBadge = '<span class="badge badge-warning text-xs shadow-none"><i class="fas fa-utensils mr-1"></i>Kasir &amp; Resto</span>';
                        } elseif (stripos($j->description, 'Komisi Dokter') !== false) {
                            $modulBadge = '<span class="badge badge-primary text-xs shadow-none"><i class="fas fa-user-doctor mr-1"></i>Jasa Dokter &amp; Kasir</span>';
                        } else {
                            $modulBadge = '<span class="badge badge-light border text-xs shadow-none"><i class="fas fa-cash-register mr-1"></i>Kasir Utama</span>';
                        }
                    } elseif ($j->source_module === 'Farmasi Apotek' || stripos($j->source_module, 'Apotek') !== false) {
                        if (stripos($j->description, 'Konsultasi Online') !== false || stripos($j->source_module, 'Konsultasi Online') !== false) {
                            $modulBadge = '<span class="badge badge-primary text-xs shadow-none" style="background-color:#6f42c1; color:#fff;"><i class="fas fa-globe mr-1"></i>Apotek (Konsultasi Online)</span>';
                        } elseif (stripos($j->description, 'Resep') !== false || stripos($j->source_module, 'Resep') !== false) {
                            $modulBadge = '<span class="badge badge-success text-xs shadow-none"><i class="fas fa-prescription mr-1"></i>Apotek (Obat Resep)</span>';
                        } else {
                            $modulBadge = '<span class="badge badge-teal text-xs shadow-none"><i class="fas fa-pills mr-1"></i>Apotek (Obat Bebas)</span>';
                        }
                    } elseif ($j->source_module === 'Resto POS') {
                        $modulBadge = '<span class="badge badge-warning text-xs shadow-none"><i class="fas fa-utensils mr-1"></i>Resto &amp; Nutrisi</span>';
                    } elseif ($j->source_module === 'Saldo Awal') {
                        $modulBadge = '<span class="badge badge-secondary text-xs shadow-none"><i class="fas fa-history mr-1"></i>Saldo Awal</span>';
                    } elseif (in_array($j->source_module, ['Manual', 'Penyesuaian', 'Adjustment'])) {
                        $modulBadge = '<span class="badge badge-dark text-xs shadow-none"><i class="fas fa-pen-to-square mr-1"></i>Jurnal Manual</span>';
                    }

                    $entryTime = !empty($j->created_at) ? date('H:i:s', strtotime($j->created_at)) : '00:00:00';
                    $entryDate = date('d/m/Y', strtotime($j->entry_date ?: ($j->created_at ?? 'now')));

                    $dateColHtml = '<div class="text-center">' .
                        '<span class="font-monospace text-xs text-nowrap font-weight-bold text-dark">' . $entryDate . '</span>' .
                        '<small class="text-muted d-block font-monospace text-xs text-nowrap mt-1"><i class="far fa-clock text-teal mr-1"></i>' . $entryTime . '</small>' .
                        '</div>';

                    return [
                        $dateColHtml,
                        '<strong class="text-teal font-monospace">' . esc($j->journal_no) . '</strong><br>' . $modulBadge,
                        '<span class="text-dark font-weight-500" style="line-height: 1.4;">' . nl2br(esc($j->description)) . '</span>',
                        $accHtml,
                        $debHtml,
                        $crdHtml,
                        $salHtml
                    ];
                }
            ]);
        }

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();
        $modules = $db->table('journal_entries')->distinct()->select('source_module')->where('source_module !=', '')->orderBy('source_module', 'ASC')->get()->getResult();

        $data = [
            'title'         => 'Bagan Jurnal Umum & Penyesuaian',
            'active_menu'   => 'accounting-jurnal',
            'accounts'      => $accounts,
            'modules'       => $modules
        ];

        return view('accounting/jurnal', $data);
    }

    /**
     * Ekspor Buku Jurnal Umum Konsolidasian & Penyesuaian ke Excel (SpreadsheetML Native .xls)
     */
    public function exportJurnalExcel()
    {
        $db = \Config\Database::connect('default');

        $filterModule = $this->request->getGet('source_module');
        $filterStart  = $this->request->getGet('start_date');
        $filterEnd    = $this->request->getGet('end_date');

        $builder = $db->table('journal_entries');
        if (!empty($filterModule)) {
            $builder->where('journal_entries.source_module', $filterModule);
        }
        if (!empty($filterStart)) {
            $builder->where('journal_entries.entry_date >=', $filterStart);
        }
        if (!empty($filterEnd)) {
            $builder->where('journal_entries.entry_date <=', $filterEnd);
        }

        $journals = $builder->orderBy('journal_entries.entry_date', 'ASC')
                            ->orderBy('journal_entries.created_at', 'ASC')
                            ->orderBy('journal_entries.id', 'ASC')
                            ->get()
                            ->getResult();

        $journalIds = array_column($journals, 'id');
        $detailsByJournal = [];
        if (!empty($journalIds)) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code as account_code, accounts.name as account_name, accounts.balance as account_balance')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->whereIn('journal_id', $journalIds)
                          ->orderBy('journal_entry_details.debit', 'DESC')
                          ->orderBy('journal_entry_details.id', 'ASC')
                          ->get()
                          ->getResult();

            foreach ($details as $d) {
                $detailsByJournal[$d->journal_id][] = $d;
            }
        }

        $cleanStart = !empty($filterStart) ? str_replace('-', '', $filterStart) : 'Awal';
        $cleanEnd   = !empty($filterEnd) ? str_replace('-', '', $filterEnd) : 'Sekarang';
        $filename   = "Buku_Jurnal_Umum_Sawamawa_{$cleanStart}_sd_{$cleanEnd}.xls";

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Sawamawa Medical Center</Author>
  <Created><?= date('Y-m-d\TH:i:s\Z') ?></Created>
  <Company>Sawamawa Medical Center</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#1e293b"/>
  </Style>
  <Style ss:ID="ClinicTitle">
   <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#0d9488"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="ReportTitle">
   <Font ss:FontName="Calibri" ss:Size="12" ss:Bold="1" ss:Color="#0f172a"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="SubTitle">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Italic="1" ss:Color="#64748b"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="TableHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0f766e"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0f766e"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#ffffff"/>
   <Interior ss:Color="#0f766e" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="TableCell">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Vertical="Top" ss:WrapText="1"/>
  </Style>
  <Style ss:ID="TableCellCenter">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Top"/>
  </Style>
  <Style ss:ID="TableCellCredit">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Vertical="Top" ss:Indent="1" ss:WrapText="1"/>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#475569"/>
  </Style>
  <Style ss:ID="TableDebit">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#15803d"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TableCredit">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#b91c1c"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TableCurrency">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/>
   </Borders>
   <Alignment ss:Horizontal="Right" ss:Vertical="Top"/>
   <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#64748b"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TableTotalLabel">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0f766e"/>
   <Interior ss:Color="#ccfbf1" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="TableTotalDebit">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#15803d"/>
   <Interior ss:Color="#ccfbf1" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TableTotalCredit">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#b91c1c"/>
   <Interior ss:Color="#ccfbf1" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0.00"/>
  </Style>
  <Style ss:ID="TableTotalStatus">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#0f766e"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0f766e"/>
   </Borders>
   <Font ss:FontName="Calibri" ss:Size="9" ss:Bold="1" ss:Color="#166534"/>
   <Interior ss:Color="#dcfce7" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Buku Jurnal Umum">
  <Table ss:DefaultRowHeight="18">
   <Column ss:Width="35"/>
   <Column ss:Width="80"/>
   <Column ss:Width="135"/>
   <Column ss:Width="130"/>
   <Column ss:Width="260"/>
   <Column ss:Width="75"/>
   <Column ss:Width="200"/>
   <Column ss:Width="110"/>
   <Column ss:Width="110"/>
   <Column ss:Width="105"/>

   <!-- TITLE BLOCK -->
   <Row ss:Height="22">
    <Cell ss:MergeAcross="9" ss:StyleID="ClinicTitle"><Data ss:Type="String"><?= htmlspecialchars(clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER'), ENT_QUOTES, 'UTF-8') ?></Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="9" ss:StyleID="ReportTitle"><Data ss:Type="String">BUKU JURNAL UMUM KONSOLIDASIAN &amp; PENYESUAIAN</Data></Cell>
   </Row>
   <Row ss:Height="16">
    <Cell ss:MergeAcross="9" ss:StyleID="SubTitle"><Data ss:Type="String">Periode: <?= !empty($filterStart) ? date('d/m/Y', strtotime($filterStart)) : 'Awal' ?> s/d <?= !empty($filterEnd) ? date('d/m/Y', strtotime($filterEnd)) : 'Sekarang' ?> | Modul: <?= !empty($filterModule) ? htmlspecialchars($filterModule, ENT_QUOTES, 'UTF-8') : 'Semua Modul Sumber' ?> | Dicetak: <?= date('d/m/Y H:i:s') ?> WITA</Data></Cell>
   </Row>
   <Row ss:Height="6"></Row>

   <!-- HEADER TABLE -->
   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Tanggal</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">No. Jurnal</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Modul Sumber</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Keterangan / Uraian Transaksi</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Kode Akun</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Rekening Akun (COA)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Debet (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Kredit (Rp)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Sisa Saldo Akun</Data></Cell>
   </Row>

   <!-- DATA ROWS -->
   <?php 
   $no = 1;
   $grandDebit = 0;
   $grandCredit = 0;
   foreach ($journals as $j): 
       $lines = $detailsByJournal[$j->id] ?? [];
       $lineCount = count($lines);
       if ($lineCount === 0) continue;

       foreach ($lines as $idx => $l): 
           $grandDebit  += (float)$l->debit;
           $grandCredit += (float)$l->credit;
           $isFirst     = ($idx === 0);
           $isCredit    = ((float)$l->credit > 0);
   ?>
   <Row>
    <Cell ss:StyleID="TableCellCenter"><Data ss:Type="String"><?= $isFirst ? $no : '' ?></Data></Cell>
    <Cell ss:StyleID="TableCellCenter"><Data ss:Type="String"><?= $isFirst ? date('d/m/Y', strtotime($j->entry_date)) : '' ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= $isFirst ? htmlspecialchars($j->journal_no, ENT_QUOTES, 'UTF-8') : '' ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= $isFirst ? htmlspecialchars($j->source_module, ENT_QUOTES, 'UTF-8') : '' ?></Data></Cell>
    <Cell ss:StyleID="TableCell"><Data ss:Type="String"><?= $isFirst ? htmlspecialchars($j->description, ENT_QUOTES, 'UTF-8') : '' ?></Data></Cell>
    <Cell ss:StyleID="TableCellCenter"><Data ss:Type="String"><?= htmlspecialchars($l->account_code, ENT_QUOTES, 'UTF-8') ?></Data></Cell>
    <Cell ss:StyleID="<?= $isCredit ? 'TableCellCredit' : 'TableCell' ?>"><Data ss:Type="String"><?= htmlspecialchars(($isCredit ? '   ' : '') . $l->account_name, ENT_QUOTES, 'UTF-8') ?></Data></Cell>
    <Cell ss:StyleID="TableDebit"><?php if ((float)$l->debit > 0): ?><Data ss:Type="Number"><?= (float)$l->debit ?></Data><?php endif; ?></Cell>
    <Cell ss:StyleID="TableCredit"><?php if ((float)$l->credit > 0): ?><Data ss:Type="Number"><?= (float)$l->credit ?></Data><?php endif; ?></Cell>
    <Cell ss:StyleID="TableCurrency"><Data ss:Type="Number"><?= (float)($l->account_balance ?? 0) ?></Data></Cell>
   </Row>
   <?php 
       endforeach;
       $no++;
   endforeach; 
   ?>

   <!-- TOTAL SUMMARY ROW -->
   <Row ss:Height="22">
    <Cell ss:MergeAcross="6" ss:StyleID="TableTotalLabel"><Data ss:Type="String">TOTAL MUTASI JURNAL UMUM:</Data></Cell>
    <Cell ss:StyleID="TableTotalDebit"><Data ss:Type="Number"><?= $grandDebit ?></Data></Cell>
    <Cell ss:StyleID="TableTotalCredit"><Data ss:Type="Number"><?= $grandCredit ?></Data></Cell>
    <Cell ss:StyleID="TableTotalStatus"><Data ss:Type="String"><?= abs($grandDebit - $grandCredit) < 0.01 ? 'BALANCE' : 'SELISIH: Rp ' . number_format(abs($grandDebit - $grandCredit), 2) ?></Data></Cell>
   </Row>

   <!-- SIGNATURE BLOCK -->
   <Row ss:Height="12"></Row>
   <Row ss:Height="16">
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">Disiapkan Oleh,</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="TableCellCenter"><Data ss:Type="String">Diperiksa &amp; Diverifikasi Oleh,</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">Disetujui Oleh,</Data></Cell>
   </Row>
   <Row ss:Height="14">
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">Staf Akuntansi &amp; Pembukuan</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="TableCellCenter"><Data ss:Type="String">Supervisor Keuangan &amp; Akuntansi</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">Direktur Klinik / Pimpinan</Data></Cell>
   </Row>
   <Row ss:Height="40"></Row>
   <Row ss:Height="16">
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">( <?= htmlspecialchars(session()->get('nama_lengkap') ?? 'Staf Akuntansi', ENT_QUOTES, 'UTF-8') ?> )</Data></Cell>
    <Cell ss:MergeAcross="3" ss:StyleID="TableCellCenter"><Data ss:Type="String">( Kepala Keuangan &amp; Akuntansi )</Data></Cell>
    <Cell ss:MergeAcross="2" ss:StyleID="TableCellCenter"><Data ss:Type="String">( Direktur Utama )</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
        <?php
        exit();
    }

    /**
     * Cetak & Ekspor Buku Jurnal Umum Konsolidasian & Penyesuaian ke PDF (A4 Landscape View)
     */
    public function exportJurnalPdf()
    {
        $db = \Config\Database::connect('default');

        $filterModule = $this->request->getGet('source_module');
        $filterStart  = $this->request->getGet('start_date');
        $filterEnd    = $this->request->getGet('end_date');

        $builder = $db->table('journal_entries');
        if (!empty($filterModule)) {
            $builder->where('journal_entries.source_module', $filterModule);
        }
        if (!empty($filterStart)) {
            $builder->where('journal_entries.entry_date >=', $filterStart);
        }
        if (!empty($filterEnd)) {
            $builder->where('journal_entries.entry_date <=', $filterEnd);
        }

        $journals = $builder->orderBy('journal_entries.entry_date', 'ASC')
                            ->orderBy('journal_entries.created_at', 'ASC')
                            ->orderBy('journal_entries.id', 'ASC')
                            ->get()
                            ->getResult();

        $journalIds = array_column($journals, 'id');
        $detailsByJournal = [];
        if (!empty($journalIds)) {
            $details = $db->table('journal_entry_details')
                          ->select('journal_entry_details.*, accounts.code as account_code, accounts.name as account_name, accounts.balance as account_balance')
                          ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                          ->whereIn('journal_id', $journalIds)
                          ->orderBy('journal_entry_details.debit', 'DESC')
                          ->orderBy('journal_entry_details.id', 'ASC')
                          ->get()
                          ->getResult();

            foreach ($details as $d) {
                $detailsByJournal[$d->journal_id][] = $d;
            }
        }

        $data = [
            'title'            => 'Buku Jurnal Umum Konsolidasian & Penyesuaian',
            'filterModule'     => $filterModule,
            'filterStart'      => $filterStart,
            'filterEnd'        => $filterEnd,
            'journals'         => $journals,
            'detailsByJournal' => $detailsByJournal
        ];

        return view('accounting/cetak_jurnal', $data);
    }

    /**
     * Mengambil Detail Jurnal untuk Modal Edit (Format JSON)
     */
    public function getJurnalDetailsJson($id)
    {
        $db = \Config\Database::connect('default');
        $journal = $db->table('journal_entries')->where('id', $id)->get()->getRow();

        if (!$journal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jurnal tidak ditemukan.']);
        }

        $details = $db->table('journal_entry_details')
                      ->select('journal_entry_details.*, accounts.code as account_code, accounts.name as account_name')
                      ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                      ->where('journal_id', $id)
                      ->get()
                      ->getResult();

        return $this->response->setJSON([
            'status'  => 'success',
            'journal' => $journal,
            'details' => $details
        ]);
    }

    /**
     * Memperbarui Jurnal Manual / Penyesuaian
     */
    public function updateJurnalManual()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $journalId   = intval($this->request->getPost('journal_id'));
        $entryDate   = $this->request->getPost('entry_date') ?: date('Y-m-d');
        $desc        = trim($this->request->getPost('description') ?: 'Penyesuaian Jurnal Umum');
        $debits      = $this->request->getPost('debit_items') ?: [];
        $credits     = $this->request->getPost('credit_items') ?: [];

        $journal = $db->table('journal_entries')->where('id', $journalId)->get()->getRow();
        if (!$journal) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan.');
            return redirect()->to(base_url('accounting/jurnal'));
        }

        // 1. Revert saldo lama dari detail jurnal ini
        $oldDetails = $db->table('journal_entry_details')->where('journal_id', $journalId)->get()->getResult();
        foreach ($oldDetails as $od) {
            $acc = $db->table('accounts')->where('id', $od->account_id)->get()->getRow();
            if ($acc) {
                if ($od->debit > 0) {
                    $revertedBal = ($acc->normal_balance === 'debit') ? ($acc->balance - $od->debit) : ($acc->balance + $od->debit);
                } else {
                    $revertedBal = ($acc->normal_balance === 'credit') ? ($acc->balance - $od->credit) : ($acc->balance + $od->credit);
                }
                $db->table('accounts')->where('id', $od->account_id)->update(['balance' => $revertedBal]);
            }
        }

        // Hapus detail lama
        $db->table('journal_entry_details')->where('journal_id', $journalId)->delete();

        // 2. Update data header jurnal
        $db->table('journal_entries')->where('id', $journalId)->update([
            'entry_date'  => $entryDate,
            'description' => $desc
        ]);

        // 3. Masukkan rincian Debet baru
        foreach ($debits as $d) {
            if (empty($d['account_id']) || floatval($d['amount']) <= 0) continue;
            $val = floatval($d['amount']);

            $db->table('journal_entry_details')->insert([
                'journal_id' => $journalId,
                'account_id' => $d['account_id'],
                'debit'      => $val,
                'credit'     => 0.00
            ]);

            $acc = $db->table('accounts')->where('id', $d['account_id'])->get()->getRow();
            $newBal = ($acc->normal_balance === 'debit') ? ($acc->balance + $val) : ($acc->balance - $val);
            $db->table('accounts')->where('id', $d['account_id'])->update(['balance' => $newBal]);
        }

        // 4. Masukkan rincian Kredit baru
        foreach ($credits as $c) {
            if (empty($c['account_id']) || floatval($c['amount']) <= 0) continue;
            $val = floatval($c['amount']);

            $db->table('journal_entry_details')->insert([
                'journal_id' => $journalId,
                'account_id' => $c['account_id'],
                'debit'      => 0.00,
                'credit'     => $val
            ]);

            $acc = $db->table('accounts')->where('id', $c['account_id'])->get()->getRow();
            $newBal = ($acc->normal_balance === 'credit') ? ($acc->balance + $val) : ($acc->balance - $val);
            $db->table('accounts')->where('id', $c['account_id'])->update(['balance' => $newBal]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal memperbarui jurnal penyesuaian.');
        } else {
            session()->setFlashdata('success', 'Jurnal penyesuaian ' . $journal->journal_no . ' berhasil diperbarui dan saldo akun disesuaikan!');
        }

        return redirect()->to(base_url('accounting/jurnal'));
    }

    /**
     * Menghapus Jurnal Manual / Penyesuaian & Mengembalikan Saldo Akun
     */
    public function deleteJurnalManual($id)
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $journal = $db->table('journal_entries')->where('id', $id)->get()->getRow();
        if (!$journal) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan.');
            return redirect()->to(base_url('accounting/jurnal'));
        }

        // Revert saldo akun dari detail jurnal yang akan dihapus
        $oldDetails = $db->table('journal_entry_details')->where('journal_id', $id)->get()->getResult();
        foreach ($oldDetails as $od) {
            $acc = $db->table('accounts')->where('id', $od->account_id)->get()->getRow();
            if ($acc) {
                if ($od->debit > 0) {
                    $revertedBal = ($acc->normal_balance === 'debit') ? ($acc->balance - $od->debit) : ($acc->balance + $od->debit);
                } else {
                    $revertedBal = ($acc->normal_balance === 'credit') ? ($acc->balance - $od->credit) : ($acc->balance + $od->credit);
                }
                $db->table('accounts')->where('id', $od->account_id)->update(['balance' => $revertedBal]);
            }
        }

        // Hapus detail dan header jurnal
        $db->table('journal_entry_details')->where('journal_id', $id)->delete();
        $db->table('journal_entries')->where('id', $id)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menghapus jurnal penyesuaian.');
        } else {
            session()->setFlashdata('success', 'Jurnal ' . $journal->journal_no . ' berhasil dihapus dan saldo akun terkait telah dipulihkan ke posisi semula.');
        }

        return redirect()->to(base_url('accounting/jurnal'));
    }

    public function saldoAwal()
    {
        $db = \Config\Database::connect('default');

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        // Group accounts by category
        $grouped = [
            'asset'     => [],
            'liability' => [],
            'equity'    => [],
            'revenue'   => [],
            'expense'   => []
        ];

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $grouped[$acc->type][] = $acc;
            if ($acc->normal_balance === 'debit') {
                $totalDebit += (float)$acc->balance;
            } else {
                $totalCredit += (float)$acc->balance;
            }
        }

        // Fetch Opening Balance Journal History
        $openingJournals = $db->table('journal_entries')
                              ->where('source_module', 'Saldo Awal')
                              ->orderBy('id', 'DESC')
                              ->limit(10)
                              ->get()
                              ->getResult();

        $data = [
            'title'           => 'Konfigurasi & Setup Saldo Awal Sistem',
            'active_menu'     => 'accounting-saldo-awal',
            'accounts'        => $accounts,
            'grouped'         => $grouped,
            'totalDebit'      => $totalDebit,
            'totalCredit'     => $totalCredit,
            'openingJournals' => $openingJournals
        ];

        return view('accounting/saldo_awal', $data);
    }

    public function saveSaldoAwal()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        $balances = $this->request->getPost('balances'); // array of account_id => amount
        $notes    = trim($this->request->getPost('notes') ?: 'Setup Saldo Awal Pembukuan Sistem');
        $userId   = session()->get('user_id') ?: 1;

        if (!is_array($balances)) {
            $balances = [];
        }

        // 1. Bersihkan seluruh jurnal saldo awal lama dan rinciannya agar tidak terjadi penumpukan/residual saldo!
        $oldOpeningJournals = $db->table('journal_entries')->where('source_module', 'Saldo Awal')->get()->getResult();
        if (!empty($oldOpeningJournals)) {
            $oldJIds = array_column($oldOpeningJournals, 'id');
            $db->table('journal_entry_details')->whereIn('journal_id', $oldJIds)->delete();
            $db->table('journal_entries')->where('source_module', 'Saldo Awal')->delete();
        }

        // 2. Reset seluruh saldo akun COA menjadi 0.00 terlebih dahulu
        $db->table('accounts')->update(['balance' => 0.00]);

        $today = date('Ymd');
        $journalNo = 'JV-SALDOAWAL-' . $today;

        $db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => 'Saldo Awal',
            'reference_id'  => 0,
            'description'   => "Setup Saldo Awal Pembukuan: {$notes}",
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $db->insertID();

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($balances as $accId => $amount) {
            $amount = floatval($amount);
            $accId  = intval($accId);
            if ($accId <= 0) continue;

            $account = $db->table('accounts')->where('id', $accId)->get()->getRow();
            if (!$account) continue;

            // Set saldo baru akun (jika diisi 0 atau kosong, maka saldo menjadi 0.00)
            $db->table('accounts')->where('id', $accId)->update(['balance' => $amount]);

            // Jika akun Kas Kasir Utama (id=1), update saldo cash_registers
            if ($accId === 1) {
                $reg = $db->table('cash_registers')->where('id', 1)->get()->getRow();
                if ($reg) {
                    $db->table('cash_registers')->where('id', 1)->update(['balance' => $amount]);
                } else {
                    $db->table('cash_registers')->insert([
                        'id'      => 1,
                        'name'    => 'Kasir Utama',
                        'balance' => $amount,
                        'status'  => 'open'
                    ]);
                }
            }

            if ($amount > 0) {
                if ($account->normal_balance === 'debit') {
                    $totalDebit += $amount;
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $journalId,
                        'account_id' => $accId,
                        'debit'      => $amount,
                        'credit'     => 0.00
                    ]);
                } else {
                    $totalCredit += $amount;
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $journalId,
                        'account_id' => $accId,
                        'debit'      => 0.00,
                        'credit'     => $amount
                    ]);
                }
            }
        }

        // Auto-balance penyeimbang ke Ekuitas (Modal Awal Disetor 3-101) jika terdapat selisih
        $diff = $totalDebit - $totalCredit;
        if (abs($diff) > 0.01) {
            $equityAcc = $db->table('accounts')->where('code', '3-101')->orWhere('type', 'equity')->orderBy('code', 'ASC')->get()->getRow();
            if ($equityAcc) {
                if ($diff > 0) {
                    // Kredit Ekuitas Modal
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $journalId,
                        'account_id' => $equityAcc->id,
                        'debit'      => 0.00,
                        'credit'     => $diff
                    ]);
                    $db->table('accounts')->where('id', $equityAcc->id)->update(['balance' => (float)$equityAcc->balance + $diff]);
                } else {
                    // Debet Ekuitas
                    $absDiff = abs($diff);
                    $db->table('journal_entry_details')->insert([
                        'journal_id' => $journalId,
                        'account_id' => $equityAcc->id,
                        'debit'      => $absDiff,
                        'credit'     => 0.00
                    ]);
                    $db->table('accounts')->where('id', $equityAcc->id)->update(['balance' => (float)$equityAcc->balance - $absDiff]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menyimpan saldo awal sistem.');
        } else {
            session()->setFlashdata('success', 'Seluruh Saldo Awal akun kas, bank, persediaan, dan modal berhasil diperbarui secara seimbang! No. Jurnal: ' . $journalNo);
        }

        return redirect()->to(base_url('accounting/saldo-awal'));
    }

    /**
     * Reset / Kosongkan Seluruh Saldo Awal Kembali ke Rp 0,00
     */
    public function resetSaldoAwal()
    {
        $db = \Config\Database::connect('default');
        $db->transStart();

        // Hapus seluruh jurnal saldo awal lama
        $oldOpeningJournals = $db->table('journal_entries')->where('source_module', 'Saldo Awal')->get()->getResult();
        if (!empty($oldOpeningJournals)) {
            $oldJIds = array_column($oldOpeningJournals, 'id');
            $db->table('journal_entry_details')->whereIn('journal_id', $oldJIds)->delete();
            $db->table('journal_entries')->where('source_module', 'Saldo Awal')->delete();
        }

        // Reset seluruh akun COA dan cash register
        $db->table('accounts')->update(['balance' => 0.00]);
        if ($db->tableExists('cash_registers')) {
            $db->table('cash_registers')->update(['balance' => 0.00]);
        }

        $db->transComplete();

        session()->setFlashdata('success', 'Seluruh Saldo Awal dan jurnal pembukuan awal telah berhasil di-reset / dikosongkan kembali menjadi Rp 0,00!');
        return redirect()->to(base_url('accounting/saldo-awal'));
    }

    public function laporan()
    {
        $db = \Config\Database::connect('default');

        // Auto-sync unposted transactions from Resto POS and OTC Pharmacy
        $this->journalEngine->syncUnpostedTransactions();

        // Date Filter Parameters
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        // 1. Fetch COA balances
        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        // 2. Classify accounts for financial statements
        $revenueAccounts   = [];
        $expenseAccounts   = [];
        $cogsAccounts      = [];
        $assetAccounts     = [];
        $currentAssets     = [];
        $fixedAssets       = [];
        $liabilityAccounts = [];
        $equityAccounts    = [];

        $totalRevenue = 0;
        $totalExpense = 0;
        $totalCogs    = 0;
        $totalAsset   = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        foreach ($accounts as $acc) {
            $bal = (float)$acc->balance;
            if ($acc->type === 'revenue') {
                $revenueAccounts[] = $acc;
                $totalRevenue += $bal;
            } elseif ($acc->type === 'expense') {
                if (str_starts_with($acc->code, '5-')) {
                    $cogsAccounts[] = $acc;
                    $totalCogs += $bal;
                } else {
                    $expenseAccounts[] = $acc;
                    $totalExpense += $bal;
                }
            } elseif ($acc->type === 'asset') {
                $assetAccounts[] = $acc;
                $totalAsset += $bal;
                if (str_starts_with($acc->code, '1-1') || str_starts_with($acc->code, '1-2') || str_starts_with($acc->code, '1-3')) {
                    $currentAssets[] = $acc;
                } else {
                    $fixedAssets[] = $acc;
                }
            } elseif ($acc->type === 'liability') {
                $liabilityAccounts[] = $acc;
                $totalLiability += $bal;
            } elseif ($acc->type === 'equity') {
                $equityAccounts[] = $acc;
                $totalEquity += $bal;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netIncome   = $grossProfit - $totalExpense;
        
        // Retained Earnings calculation for Balance Sheet
        $retainedEarnings   = $netIncome;
        $totalEquityWithNet = $totalEquity + $retainedEarnings;

        // 3. Arus Kas (Cash Flow Calculations)
        $cashInTotal = $db->table('cash_transactions')
                          ->where('DATE(created_at) >=', $startDate)
                          ->where('DATE(created_at) <=', $endDate)
                          ->selectSum('amount')
                          ->get()
                          ->getRow()->amount ?? 0;

        $otherIncomeTotal = $db->table('journal_entries')
                               ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                               ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                               ->where('journal_entries.source_module', 'Penerimaan Lain')
                               ->where('journal_entries.entry_date >=', $startDate)
                               ->where('journal_entries.entry_date <=', $endDate)
                               ->where('journal_entry_details.debit >', 0)
                               ->selectSum('journal_entry_details.debit')
                               ->get()
                               ->getRow()->debit ?? 0;

        $expensePaidTotal = $db->table('journal_entries')
                               ->join('journal_entry_details', 'journal_entry_details.journal_id = journal_entries.id')
                               ->join('accounts', 'accounts.id = journal_entry_details.account_id')
                               ->where('accounts.type', 'expense')
                               ->where('journal_entries.entry_date >=', $startDate)
                               ->where('journal_entries.entry_date <=', $endDate)
                               ->where('journal_entry_details.debit >', 0)
                               ->selectSum('journal_entry_details.debit')
                               ->get()
                               ->getRow()->debit ?? 0;

        // 4. Breakdown per Unit Bisnis
        // Poli Medis
        $klinikRevenue = $db->table('billing_transactions')
                            ->where('status', 'paid')
                            ->where('DATE(created_at) >=', $startDate)
                            ->where('DATE(created_at) <=', $endDate)
                            ->selectSum('total_services')
                            ->get()
                            ->getRow()->total_services ?? 0;

        // Apotek / Farmasi
        $apotekRevenue = $db->table('billing_transactions')
                            ->where('status', 'paid')
                            ->where('DATE(created_at) >=', $startDate)
                            ->where('DATE(created_at) <=', $endDate)
                            ->selectSum('total_medicines')
                            ->get()
                            ->getRow()->total_medicines ?? 0;

        // Resto Sehat & Gizi
        $restoSalesTotal = $db->table('restaurant_orders')
                              ->whereIn('payment_status', ['paid', 'billed_to_clinic'])
                              ->where('DATE(created_at) >=', $startDate)
                              ->where('DATE(created_at) <=', $endDate)
                              ->selectSum('grand_total')
                              ->get()
                              ->getRow()->grand_total ?? 0;

        $data = [
            'title'                  => 'Laporan Keuangan Terpadu & Akuntansi',
            'active_menu'            => 'accounting-laporan',
            'startDate'              => $startDate,
            'endDate'                => $endDate,
            'revenueAccounts'        => $revenueAccounts,
            'cogsAccounts'           => $cogsAccounts,
            'expenseAccounts'        => $expenseAccounts,
            'totalRevenue'           => (float)$totalRevenue,
            'totalCogs'              => (float)$totalCogs,
            'grossProfit'            => (float)$grossProfit,
            'totalExpense'           => (float)$totalExpense,
            'netIncome'              => (float)$netIncome,
            
            'assetAccounts'          => $assetAccounts,
            'currentAssets'          => $currentAssets,
            'fixedAssets'            => $fixedAssets,
            'liabilityAccounts'      => $liabilityAccounts,
            'equityAccounts'         => $equityAccounts,
            'totalAsset'             => (float)$totalAsset,
            'totalLiability'         => (float)$totalLiability,
            'totalEquity'            => (float)$totalEquityWithNet,
            'retainedEarnings'       => (float)$retainedEarnings,

            // Cash flow
            'cashInTotal'            => (float)$cashInTotal + (float)$otherIncomeTotal,
            'expensePaidTotal'       => (float)$expensePaidTotal,

            // Business Unit Revenue
            'klinikRevenue'          => (float)$klinikRevenue,
            'apotekRevenue'          => (float)$apotekRevenue,
            'restoRevenue'           => (float)$restoSalesTotal,
            'restoSalesTotal'        => (float)$restoSalesTotal
        ];

        return view('accounting/laporan', $data);
    }

    public function cetakLaporan()
    {
        $db = \Config\Database::connect('default');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');
        $type      = $this->request->getGet('type') ?: 'all'; // 'labarugi', 'neraca', 'aruskas', 'all'

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        $revenueAccounts = [];
        $cogsAccounts    = [];
        $expenseAccounts = [];
        $assetAccounts   = [];
        $liabilityAccounts = [];
        $equityAccounts  = [];

        $totalRevenue = 0;
        $totalCogs    = 0;
        $totalExpense = 0;
        $totalAsset   = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        foreach ($accounts as $acc) {
            $bal = (float)$acc->balance;
            if ($acc->type === 'revenue') {
                $revenueAccounts[] = $acc;
                $totalRevenue += $bal;
            } elseif ($acc->type === 'expense') {
                if (str_starts_with($acc->code, '5-')) {
                    $cogsAccounts[] = $acc;
                    $totalCogs += $bal;
                } else {
                    $expenseAccounts[] = $acc;
                    $totalExpense += $bal;
                }
            } elseif ($acc->type === 'asset') {
                $assetAccounts[] = $acc;
                $totalAsset += $bal;
            } elseif ($acc->type === 'liability') {
                $liabilityAccounts[] = $acc;
                $totalLiability += $bal;
            } elseif ($acc->type === 'equity') {
                $equityAccounts[] = $acc;
                $totalEquity += $bal;
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;
        $netIncome   = $grossProfit - $totalExpense;
        $totalEquityWithNet = $totalEquity + $netIncome;

        $data = [
            'title'             => 'Cetak Laporan Keuangan Resmi - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'type'              => $type,
            'revenueAccounts'   => $revenueAccounts,
            'cogsAccounts'      => $cogsAccounts,
            'expenseAccounts'   => $expenseAccounts,
            'assetAccounts'     => $assetAccounts,
            'liabilityAccounts' => $liabilityAccounts,
            'equityAccounts'    => $equityAccounts,
            'totalRevenue'      => $totalRevenue,
            'totalCogs'         => $totalCogs,
            'grossProfit'       => $grossProfit,
            'totalExpense'      => $totalExpense,
            'netIncome'         => $netIncome,
            'totalAsset'        => $totalAsset,
            'totalLiability'    => $totalLiability,
            'totalEquity'       => $totalEquityWithNet
        ];

        return view('accounting/cetak_laporan', $data);
    }

    public function bukuBesar()
    {
        $db = \Config\Database::connect('default');
        $accountId = intval($this->request->getGet('account_id')) ?: 1;
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();
        $selectedAccount = $db->table('accounts')->where('id', $accountId)->get()->getRow();

        // Calculate opening balance before $startDate
        $prevDebit = $db->table('journal_entry_details')
                        ->selectSum('debit')
                        ->join('journal_entries', 'journal_entries.id = journal_entry_details.journal_id')
                        ->where('journal_entry_details.account_id', $accountId)
                        ->where('journal_entries.entry_date <', $startDate)
                        ->get()->getRow()->debit ?? 0;

        $prevCredit = $db->table('journal_entry_details')
                         ->selectSum('credit')
                         ->join('journal_entries', 'journal_entries.id = journal_entry_details.journal_id')
                         ->where('journal_entry_details.account_id', $accountId)
                         ->where('journal_entries.entry_date <', $startDate)
                         ->get()->getRow()->credit ?? 0;

        $isDebitNormal = ($selectedAccount && $selectedAccount->normal_balance === 'debit');
        $openingBalance = $isDebitNormal ? ($prevDebit - $prevCredit) : ($prevCredit - $prevDebit);

        // Fetch journal entries for this account
        $mutations = $db->table('journal_entry_details')
                        ->select('journal_entry_details.*, journal_entries.journal_no, journal_entries.entry_date, journal_entries.created_at, journal_entries.description as journal_desc, journal_entries.source_module')
                        ->join('journal_entries', 'journal_entries.id = journal_entry_details.journal_id')
                        ->where('journal_entry_details.account_id', $accountId)
                        ->where('journal_entries.entry_date >=', $startDate)
                        ->where('journal_entries.entry_date <=', $endDate)
                        ->orderBy('journal_entries.entry_date', 'ASC')
                        ->orderBy('journal_entries.created_at', 'ASC')
                        ->orderBy('journal_entries.id', 'ASC')
                        ->get()
                        ->getResult();

        $runningBalance = $openingBalance;
        foreach ($mutations as $m) {
            if ($isDebitNormal) {
                $runningBalance += ($m->debit - $m->credit);
            } else {
                $runningBalance += ($m->credit - $m->debit);
            }
            $m->running_balance = $runningBalance;
        }

        $data = [
            'title'           => 'Buku Besar & Jurnal Khusus Akun (General Ledger)',
            'active_menu'     => 'accounting-buku-besar',
            'accounts'        => $accounts,
            'selectedAccount' => $selectedAccount,
            'mutations'       => $mutations,
            'openingBalance'  => $openingBalance,
            'closingBalance'  => $runningBalance,
            'isDebitNormal'   => $isDebitNormal,
            'startDate'       => $startDate,
            'endDate'         => $endDate
        ];

        return view('accounting/buku_besar', $data);
    }

    /**
     * MASTER TEMPLATE ATURAN JURNAL & BAGI HASIL (JURNAL UMUM PER-AKUN)
     */
    public function aturanJurnal()
    {
        $db = \Config\Database::connect('default');

        $categories = $db->table('journal_categories')
                         ->orderBy('module', 'ASC')
                         ->orderBy('id', 'ASC')
                         ->get()
                         ->getResult();

        foreach ($categories as $cat) {
            $rules = $db->table('journal_category_rules')
                        ->select('journal_category_rules.*, accounts.code as account_code, accounts.name as account_name')
                        ->join('accounts', 'accounts.id = journal_category_rules.account_id', 'left')
                        ->where('journal_category_rules.category_id', $cat->id)
                        ->orderBy('journal_category_rules.sort_order', 'ASC')
                        ->orderBy('journal_category_rules.id', 'ASC')
                        ->get()
                        ->getResult();

            $debitSum = 0.0;
            $creditSum = 0.0;
            $debitFixedSum = 0.0;
            $creditFixedSum = 0.0;
            $debitCount = 0;
            $creditCount = 0;
            $hasPercentage = false;
            $hasFixed = false;

            foreach ($rules as $r) {
                $pct = (float)$r->percentage_value;
                $fix = (float)$r->fixed_amount_value;
                if ($pct > 0) $hasPercentage = true;
                if ($fix > 0) $hasFixed = true;

                if ($r->position === 'debit') {
                    $debitSum += $pct;
                    $debitFixedSum += $fix;
                    $debitCount++;
                } else {
                    $creditSum += $pct;
                    $creditFixedSum += $fix;
                    $creditCount++;
                }
            }

            $cat->rules = $rules;
            $cat->total_rules = count($rules);
            $cat->debit_count = $debitCount;
            $cat->credit_count = $creditCount;
            $cat->debit_sum = $debitSum;
            $cat->credit_sum = $creditSum;
            $cat->debit_fixed_sum = $debitFixedSum;
            $cat->credit_fixed_sum = $creditFixedSum;
            $cat->is_fixed_amount = ($hasFixed && !$hasPercentage && $debitFixedSum > 0);

            if ($hasPercentage && $debitSum > 0) {
                $cat->is_balanced = (abs($debitSum - $creditSum) < 0.05);
            } elseif ($hasFixed && $debitFixedSum > 0) {
                $cat->is_balanced = (abs($debitFixedSum - $creditFixedSum) < 1.0);
            } else {
                $cat->is_balanced = false;
            }
        }

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        $data = [
            'title'       => 'Master Aturan Jurnal & Bagi Hasil Per-Akun',
            'active_menu' => 'accounting-aturan-jurnal',
            'categories'  => $categories,
            'accounts'    => $accounts
        ];

        return view('accounting/aturan_jurnal', $data);
    }

    /**
     * DETAIL & SUB-RULES MANAGER TEMPLATE JURNAL
     */
    public function detailAturanJurnal($id)
    {
        $db = \Config\Database::connect('default');

        $category = $db->table('journal_categories')->where('id', $id)->get()->getRow();
        if (!$category) {
            session()->setFlashdata('error', 'Kategori aturan jurnal tidak ditemukan.');
            return redirect()->to(base_url('accounting/aturan-jurnal'));
        }

        $rules = $db->table('journal_category_rules')
                    ->select('journal_category_rules.*, accounts.code as account_code, accounts.name as account_name, accounts.type as account_type')
                    ->join('accounts', 'accounts.id = journal_category_rules.account_id', 'left')
                    ->where('journal_category_rules.category_id', $category->id)
                    ->orderBy('journal_category_rules.position', 'DESC') // Debit first
                    ->orderBy('journal_category_rules.sort_order', 'ASC')
                    ->orderBy('journal_category_rules.id', 'ASC')
                    ->get()
                    ->getResult();

        $debitPct = 0.0;
        $creditPct = 0.0;
        $debitFixedSum = 0.0;
        $creditFixedSum = 0.0;
        $hasPct = false;
        $hasFixed = false;

        foreach ($rules as $r) {
            $p = (float)$r->percentage_value;
            $f = (float)$r->fixed_amount_value;
            if ($p > 0) $hasPct = true;
            if ($f > 0) $hasFixed = true;

            if ($r->position === 'debit') {
                $debitPct += $p;
                $debitFixedSum += $f;
            } else {
                $creditPct += $p;
                $creditFixedSum += $f;
            }
        }

        $isFixedAmount = ($hasFixed && !$hasPct && $debitFixedSum > 0);
        if ($hasPct && $debitPct > 0) {
            $isBalanced = (abs($debitPct - $creditPct) < 0.05);
        } elseif ($hasFixed && $debitFixedSum > 0) {
            $isBalanced = (abs($debitFixedSum - $creditFixedSum) < 1.0);
        } else {
            $isBalanced = false;
        }

        $accounts = $db->table('accounts')->orderBy('code', 'ASC')->get()->getResult();

        $data = [
            'title'          => 'Detail Aturan Jurnal: ' . $category->category_name,
            'active_menu'    => 'accounting-aturan-jurnal',
            'category'       => $category,
            'rules'          => $rules,
            'debitPct'       => $debitPct,
            'creditPct'      => $creditPct,
            'debitFixedSum'  => $debitFixedSum,
            'creditFixedSum' => $creditFixedSum,
            'isFixedAmount'  => $isFixedAmount,
            'isBalanced'     => $isBalanced,
            'accounts'       => $accounts
        ];

        return view('accounting/aturan_jurnal_detail', $data);
    }

    /**
     * SIMPAN / UPDATE KATEGORI JURNAL
     */
    public function saveCategory()
    {
        $db = \Config\Database::connect('default');

        $id = $this->request->getPost('id');
        $code = strtoupper(trim($this->request->getPost('category_code')));
        $name = trim($this->request->getPost('category_name'));
        $module = trim($this->request->getPost('module'));
        $description = trim($this->request->getPost('description'));
        $isActive = (int)$this->request->getPost('is_active');

        if (empty($code) || empty($name)) {
            session()->setFlashdata('error', 'Kode dan Nama Kategori wajib diisi.');
            return redirect()->back()->withInput();
        }

        $categoryModel = new \App\Models\JournalCategoryModel();

        $data = [
            'category_code' => $code,
            'category_name' => $name,
            'module'        => $module ?: 'apotek',
            'description'   => $description,
            'is_active'     => $isActive ? 1 : 0
        ];

        if (!empty($id)) {
            $categoryModel->update($id, $data);
            session()->setFlashdata('success', "Kategori Jurnal '{$name}' berhasil diperbarui.");
        } else {
            $insertedId = $categoryModel->insert($data);
            session()->setFlashdata('success', "Kategori Jurnal '{$name}' berhasil ditambahkan. Silakan konfigurasikan sub-aturan pos akun.");
            return redirect()->to(base_url('accounting/aturan-jurnal/detail/' . $insertedId));
        }

        return redirect()->to(base_url('accounting/aturan-jurnal'));
    }

    /**
     * HAPUS KATEGORI JURNAL
     */
    public function deleteCategory($id)
    {
        $db = \Config\Database::connect('default');
        $cat = $db->table('journal_categories')->where('id', $id)->get()->getRow();

        if ($cat) {
            $db->table('journal_category_rules')->where('category_id', $id)->delete();
            $db->table('journal_categories')->where('id', $id)->delete();
            session()->setFlashdata('success', "Kategori '{$cat->category_name}' dan seluruh sub-aturannya berhasil dihapus.");
        } else {
            session()->setFlashdata('error', 'Kategori tidak ditemukan.');
        }

        return redirect()->to(base_url('accounting/aturan-jurnal'));
    }

    /**
     * TOGGLE STATUS AKTIF KATEGORI
     */
    public function toggleCategoryStatus($id)
    {
        $db = \Config\Database::connect('default');
        $cat = $db->table('journal_categories')->where('id', $id)->get()->getRow();

        if ($cat) {
            $newStatus = $cat->is_active ? 0 : 1;
            $db->table('journal_categories')->where('id', $id)->update(['is_active' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
            $label = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            session()->setFlashdata('success', "Status kategori '{$cat->category_name}' berhasil {$label}.");
        }

        return redirect()->back();
    }

    /**
     * SIMPAN / UPDATE SUB-RULE ITEM JURNAL
     */
    public function saveRule()
    {
        $db = \Config\Database::connect('default');

        $id = $this->request->getPost('id');
        $categoryId = (int)$this->request->getPost('category_id');
        $itemName = trim($this->request->getPost('item_name'));
        $accountId = (int)$this->request->getPost('account_id');
        $position = strtolower($this->request->getPost('position')) === 'debit' ? 'debit' : 'credit';
        $calcType = $this->request->getPost('calc_type') ?: 'percentage';
        $percentageValue = (float)$this->request->getPost('percentage_value');
        $fixedAmountValue = (float)$this->request->getPost('fixed_amount_value');
        if ($calcType === 'fixed_amount') {
            $percentageValue = 0.0;
        } elseif ($calcType === 'percentage' || $calcType === 'dynamic_fee') {
            $fixedAmountValue = 0.0;
        }
        $formulaCode = trim($this->request->getPost('formula_code')) ?: null;
        $sortOrder = (int)($this->request->getPost('sort_order') ?: 1);
        $isActive = (int)$this->request->getPost('is_active');

        if (empty($categoryId) || empty($itemName) || empty($accountId)) {
            session()->setFlashdata('error', 'Kategori, Nama Pos Item, dan Akun COA target wajib dipilih.');
            return redirect()->back()->withInput();
        }

        $ruleModel = new \App\Models\JournalCategoryRuleModel();

        $data = [
            'category_id'        => $categoryId,
            'item_name'          => $itemName,
            'account_id'         => $accountId,
            'position'           => $position,
            'calc_type'          => $calcType,
            'percentage_value'   => $percentageValue,
            'fixed_amount_value' => $fixedAmountValue,
            'formula_code'       => $formulaCode,
            'sort_order'         => $sortOrder,
            'is_active'          => $isActive ? 1 : 0
        ];

        if (!empty($id)) {
            $ruleModel->update($id, $data);
            session()->setFlashdata('success', "Sub-aturan '{$itemName}' berhasil diperbarui.");
        } else {
            $ruleModel->insert($data);
            session()->setFlashdata('success', "Sub-aturan '{$itemName}' berhasil ditambahkan ke kategori.");
        }

        return redirect()->to(base_url('accounting/aturan-jurnal/detail/' . $categoryId));
    }

    /**
     * HAPUS SUB-RULE ITEM JURNAL
     */
    public function deleteRule($id)
    {
        $db = \Config\Database::connect('default');
        $rule = $db->table('journal_category_rules')->where('id', $id)->get()->getRow();

        if ($rule) {
            $catId = $rule->category_id;
            $db->table('journal_category_rules')->where('id', $id)->delete();
            session()->setFlashdata('success', "Sub-aturan pos akun '{$rule->item_name}' berhasil dihapus.");
            return redirect()->to(base_url('accounting/aturan-jurnal/detail/' . $catId));
        }

        return redirect()->to(base_url('accounting/aturan-jurnal'));
    }

    /**
     * API SIMULASI KALKULASI BAGI HASIL REAL-TIME (AJAX)
     */
    public function apiSimulateSplit($categoryId)
    {
        $db = \Config\Database::connect('default');

        $category = $db->table('journal_categories')->where('id', $categoryId)->get()->getRow();
        if (!$category) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori tidak ditemukan']);
        }

        $amount = (float)($this->request->getGet('amount') ?: ($_GET['amount'] ?? 100000));
        $doctorFeePct = (float)($this->request->getGet('doctor_fee_pct') ?: ($_GET['doctor_fee_pct'] ?? 5.00));
        $doctorFeeNominalRaw = $this->request->getGet('doctor_fee_nominal') !== null ? $this->request->getGet('doctor_fee_nominal') : ($_GET['doctor_fee_nominal'] ?? null);
        $hasFixedDocFee = ($doctorFeeNominalRaw !== null && $doctorFeeNominalRaw !== '' && is_numeric($doctorFeeNominalRaw) && (float)$doctorFeeNominalRaw > 0);
        $doctorFeeNominal = $hasFixedDocFee ? (float)$doctorFeeNominalRaw : null;

        $rules = $db->table('journal_category_rules')
                    ->select('journal_category_rules.*, accounts.code as account_code, accounts.name as account_name')
                    ->join('accounts', 'accounts.id = journal_category_rules.account_id', 'left')
                    ->where('journal_category_rules.category_id', $categoryId)
                    ->where('journal_category_rules.is_active', 1)
                    ->orderBy('journal_category_rules.sort_order', 'ASC')
                    ->get()
                    ->getResult();

        $debitItems = [];
        $creditItems = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        $isClinic = ($category->category_code === 'RAWAT_JALAN_POLI' || $category->module === 'klinik');
        $isKonsulOnline = ($category->category_code === 'KONSULTASI_ONLINE');

        if ($isClinic) {
            // 1. Hitung Tahap 1: Pengurang Utama (Dokter & Fee Karyawan)
            $docFeeNom = 0.0;
            $empFeeNominal = 0.0;
            $docPctVal = $doctorFeePct;
            $hasRemainderTier = false;

            foreach ($rules as $r) {
                if ($r->position === 'debit') continue;

                if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                    if ($hasFixedDocFee) {
                        $docFeeNom = round((float)$doctorFeeNominal, 2);
                        $docPctVal = ($amount > 0) ? round(($docFeeNom / $amount) * 100, 4) : 0;
                    } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $docFeeNom = (float)$r->fixed_amount_value;
                        $docPctVal = ($amount > 0) ? round(($docFeeNom / $amount) * 100, 4) : 0;
                    } elseif (abs($doctorFeePct - 66.6667) < 0.1 || abs($doctorFeePct - 66.67) < 0.1) {
                        $docFeeNom = round(($amount * 2 / 3), 2);
                    } else {
                        $docFeeNom = round(($amount * ($doctorFeePct / 100.0)), 2);
                    }
                } elseif ($r->formula_code === 'EMPLOYEE_FEE' || stripos($r->item_name, 'FEE KARYAWAN') !== false) {
                    if ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $empFeeNominal = (float)$r->fixed_amount_value;
                    } else {
                        $empFeeNominal = round(($amount * ((float)$r->percentage_value / 100.0)), 2);
                    }
                }

                if ($r->formula_code === 'REMAINDER_TIER' || $r->formula_code === 'DYNAMIC_OBAT_REMAINDER') {
                    $hasRemainderTier = true;
                }
            }

            $clinicRemainder = max(0, $amount - $docFeeNom - $empFeeNominal);

            // 2. Alokasi seluruh pos
            foreach ($rules as $r) {
                $pct = (float)$r->percentage_value;
                $nom = 0.0;

                if ($r->position === 'debit') {
                    $nom = $amount;
                    $pct = 100.0;
                } else {
                    if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                        $pct = $docPctVal;
                        $nom = $docFeeNom;
                    } elseif ($r->formula_code === 'EMPLOYEE_FEE' || stripos($r->item_name, 'FEE KARYAWAN') !== false) {
                        $nom = $empFeeNominal;
                        $pct = $amount > 0 ? round(($nom / $amount) * 100, 2) : 0;
                    } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $nom = (float)$r->fixed_amount_value;
                        $pct = ($amount > 0) ? round(($nom / $amount) * 100, 2) : 0;
                    } elseif ($hasRemainderTier && ($r->formula_code === 'REMAINDER_TIER' || $r->formula_code === 'DYNAMIC_OBAT_REMAINDER')) {
                        if ($pct > 25.0) {
                            $nom = round(($clinicRemainder * ($pct / 100.0)), 2);
                        } else {
                            $nom = round(($amount * ($pct / 100.0)), 2);
                        }
                    } else {
                        $nom = round(($amount * ($pct / 100.0)), 2);
                    }
                }

                $item = [
                    'id'           => $r->id,
                    'item_name'    => $r->item_name,
                    'account_code' => $r->account_code ?: '-',
                    'account_name' => $r->account_name ?: '-',
                    'position'     => $r->position,
                    'calc_type'    => $r->calc_type,
                    'pct'          => $pct,
                    'amount'       => $nom,
                    'amount_fmt'   => 'Rp ' . number_format($nom, 0, ',', '.')
                ];

                if ($r->position === 'debit') {
                    $debitItems[] = $item;
                    $totalDebit += $nom;
                } else {
                    $creditItems[] = $item;
                    $totalCredit += $nom;
                }
            }
        } elseif ($isKonsulOnline) {
            // Formula KONSULTASI ONLINE:
            // Jasa Dokter Rp 20.000 (Tetap) + Utang Fee Dokter (Manual) dikurangkan dari Kas.
            // Sisa pengurangan dikalikan persentase 5 pos (Obat, Utang Pajak, Penunjang, Obat Resep, ADM).
            $jasaDokterNom = 20000.0;
            $feeDokterNom = $hasFixedDocFee ? (float)$doctorFeeNominal : 0.0;

            foreach ($rules as $r) {
                if ($r->position === 'debit') continue;
                if ($r->formula_code === 'FIXED_NOMINAL' || stripos($r->item_name, 'JASA DOKTER') !== false) {
                    if ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $jasaDokterNom = (float)$r->fixed_amount_value;
                    }
                } elseif ($r->formula_code === 'DOCTOR_FEE_NOMINAL' || stripos($r->item_name, 'FEE DOKTER') !== false) {
                    if (!$hasFixedDocFee && $r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $feeDokterNom = (float)$r->fixed_amount_value;
                    }
                }
            }

            $basisSisa = max(0.0, $amount - ($feeDokterNom + $jasaDokterNom));

            foreach ($rules as $r) {
                $pct = (float)$r->percentage_value;
                $nom = 0.0;

                if ($r->position === 'debit') {
                    $nom = $amount;
                    $pct = 100.0;
                } else {
                    if ($r->formula_code === 'FIXED_NOMINAL' || stripos($r->item_name, 'JASA DOKTER') !== false) {
                        $nom = $jasaDokterNom;
                        $pct = ($amount > 0) ? round(($nom / $amount) * 100, 2) : 0;
                    } elseif ($r->formula_code === 'DOCTOR_FEE_NOMINAL' || stripos($r->item_name, 'FEE DOKTER') !== false) {
                        $nom = $feeDokterNom;
                        $pct = ($amount > 0) ? round(($nom / $amount) * 100, 2) : 0;
                    } else {
                        // 5 Pos Berbasis Persentase terhadap Basis Sisa
                        $nom = round(($basisSisa * ($pct / 100.0)), 2);
                    }
                }

                $item = [
                    'id'           => $r->id,
                    'item_name'    => $r->item_name,
                    'account_code' => $r->account_code ?: '-',
                    'account_name' => $r->account_name ?: '-',
                    'position'     => $r->position,
                    'calc_type'    => $r->calc_type,
                    'pct'          => $pct,
                    'amount'       => $nom,
                    'amount_fmt'   => 'Rp ' . number_format($nom, 0, ',', '.')
                ];

                if ($r->position === 'debit') {
                    $debitItems[] = $item;
                    $totalDebit += $nom;
                } else {
                    $creditItems[] = $item;
                    $totalCredit += $nom;
                }
            }
        } else {
            foreach ($rules as $r) {
                $pct = (float)$r->percentage_value;
                $nom = 0.0;

                if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                    $pct = $doctorFeePct;
                    if (abs($pct - 66.6667) < 0.1 || abs($pct - 66.67) < 0.1) {
                        $nom = round(($amount * 2 / 3), 2);
                    } else {
                        $nom = round($amount * ($pct / 100.0), 2);
                    }
                } elseif ($r->formula_code === 'DYNAMIC_OBAT_REMAINDER' && $category->category_code === 'PENJUALAN_OBAT_RESEP') {
                    $pct = max(10.0, 49.0 - $doctorFeePct);
                    $nom = round($amount * ($pct / 100.0), 2);
                } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                    $nom = (float)$r->fixed_amount_value;
                    $pct = ($amount > 0) ? round(($nom / $amount) * 100, 2) : 0.0;
                } elseif ($r->formula_code === 'EMPLOYEE_FEE' && $pct > 0) {
                    $nom = round($amount * ($pct / 100.0), 2);
                } else {
                    $nom = round($amount * ($pct / 100.0), 2);
                }

                $item = [
                    'id'           => $r->id,
                    'item_name'    => $r->item_name,
                    'account_code' => $r->account_code ?: '-',
                    'account_name' => $r->account_name ?: '-',
                    'position'     => $r->position,
                    'calc_type'    => $r->calc_type,
                    'pct'          => $pct,
                    'amount'       => $nom,
                    'amount_fmt'   => 'Rp ' . number_format($nom, 0, ',', '.')
                ];

                if ($r->position === 'debit') {
                    $debitItems[] = $item;
                    $totalDebit += $nom;
                } else {
                    $creditItems[] = $item;
                    $totalCredit += $nom;
                }
            }
        }

        // Auto penny round adjust on credit items (prioritize remainder/facility items or medicine account)
        $diff = round($totalDebit - $totalCredit, 2);
        if (abs($diff) > 0.0001 && count($creditItems) > 0) {
            $adjIdx = count($creditItems) - 1;
            foreach ($creditItems as $ci => $cVal) {
                if (stripos($cVal['item_name'], 'KONSELING') !== false || stripos($cVal['item_name'], 'FASILITAS') !== false || (stripos($cVal['item_name'], 'OBAT') !== false && stripos($cVal['item_name'], 'RESEP') === false)) {
                    $adjIdx = $ci;
                    break;
                }
            }
            $creditItems[$adjIdx]['amount'] += $diff;
            $creditItems[$adjIdx]['amount_fmt'] = 'Rp ' . number_format($creditItems[$adjIdx]['amount'], 0, ',', '.');
            $totalCredit += $diff;
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'category_name' => $category->category_name,
            'nominal_input' => $amount,
            'debits'        => $debitItems,
            'credits'       => $creditItems,
            'total_debit'   => $totalDebit,
            'total_credit'  => $totalCredit,
            'is_balanced'   => (abs($totalDebit - $totalCredit) < 0.01 && $totalDebit > 0)
        ]);
    }
}

