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
        $db = \Config\Database::connect();

        if (strtolower($this->request->getMethod()) === 'post') {
            $coaData = [
                'code'           => $this->request->getPost('code'),
                'name'           => $this->request->getPost('name'),
                'type'           => $this->request->getPost('type'),
                'normal_balance' => $this->request->getPost('normal_balance'),
                'balance'        => floatval($this->request->getPost('balance')),
                'parent_id'      => null
            ];

            $db->table('accounts')->insert($coaData);
            session()->setFlashdata('success', 'Akun COA baru berhasil didaftarkan.');
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

    public function jurnal()
    {
        $db = \Config\Database::connect();

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
                'description'   => $desc
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

        // Auto-sync unposted transactions from Resto POS and OTC Pharmacy
        $this->journalEngine->syncUnpostedTransactions();

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
                0 => 'journal_entries.entry_date',
                1 => 'journal_entries.journal_no',
                2 => 'journal_entries.description',
                3 => 'journal_entries.id',
                4 => 'journal_entries.id',
                5 => 'journal_entries.id',
                6 => 'journal_entries.id'
            ], [
                'where'          => $where,
                'search_columns' => ['journal_entries.journal_no', 'journal_entries.source_module', 'journal_entries.description'],
                'default_order'  => ['journal_entries.entry_date', 'DESC'],
                'row_formatter'  => function($j, $no) use ($db) {
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
                    } elseif ($j->source_module === 'Farmasi Apotek' && $j->reference_id > 0) {
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
                        // 4-102: Pendapatan Apotek Farmasi (Rincian Obat)
                        elseif (strpos($d->account_code, '4-102') === 0 || stripos($d->account_name, 'Apotek') !== false || stripos($d->account_name, 'Farmasi') !== false) {
                            if ($billingDetails) {
                                foreach ($billingDetails as $bd) {
                                    if ($bd->item_type === 'obat') {
                                        $subRows[] = [
                                            'label'  => '<span class="text-teal font-weight-500"><i class="fas fa-pills mr-1"></i>' . esc($bd->item_name) . ' (' . (int)$bd->qty . 'x)</span>',
                                            'amount' => (float)$bd->subtotal
                                        ];
                                    }
                                }
                            } elseif (!empty($pharmacyDetails)) {
                                foreach ($pharmacyDetails as $pd) {
                                    $subRows[] = [
                                        'label'  => '<span class="text-teal font-weight-500"><i class="fas fa-pills mr-1"></i>' . esc($pd->name ?: 'Obat') . ' (' . (int)$pd->qty . 'x)</span>',
                                        'amount' => (float)$pd->price * (int)$pd->qty
                                    ];
                                }
                            }
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
                    } elseif ($j->source_module === 'Farmasi Apotek') {
                        $modulBadge = '<span class="badge badge-teal text-xs shadow-none"><i class="fas fa-pills mr-1"></i>Apotek (Obat Bebas)</span>';
                    } elseif ($j->source_module === 'Resto POS') {
                        $modulBadge = '<span class="badge badge-warning text-xs shadow-none"><i class="fas fa-utensils mr-1"></i>Resto &amp; Nutrisi</span>';
                    } elseif ($j->source_module === 'Saldo Awal') {
                        $modulBadge = '<span class="badge badge-secondary text-xs shadow-none"><i class="fas fa-history mr-1"></i>Saldo Awal</span>';
                    } elseif (in_array($j->source_module, ['Manual', 'Penyesuaian', 'Adjustment'])) {
                        $modulBadge = '<span class="badge badge-dark text-xs shadow-none"><i class="fas fa-pen-to-square mr-1"></i>Jurnal Manual</span>';
                    }

                    return [
                        '<span class="font-monospace text-xs text-nowrap">' . date('d/m/Y', strtotime($j->entry_date)) . '</span>',
                        '<strong class="text-teal font-monospace">' . esc($j->journal_no) . '</strong><br>' . $modulBadge,
                        '<span class="text-dark font-weight-500">' . esc($j->description) . '</span>',
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
     * Mengambil Detail Jurnal untuk Modal Edit (Format JSON)
     */
    public function getJurnalDetailsJson($id)
    {
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();

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
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();

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
        $db = \Config\Database::connect();
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
        $db = \Config\Database::connect();
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
                        ->select('journal_entry_details.*, journal_entries.journal_no, journal_entries.entry_date, journal_entries.description as journal_desc, journal_entries.source_module')
                        ->join('journal_entries', 'journal_entries.id = journal_entry_details.journal_id')
                        ->where('journal_entry_details.account_id', $accountId)
                        ->where('journal_entries.entry_date >=', $startDate)
                        ->where('journal_entries.entry_date <=', $endDate)
                        ->orderBy('journal_entries.entry_date', 'ASC')
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
}
