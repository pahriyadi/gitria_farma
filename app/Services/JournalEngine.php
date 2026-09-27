<?php

namespace App\Services;

class JournalEngine
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');
    }

    /**
     * Helper to resolve account ID by primary code or fallback
     */
    public function getAccountId($code, $fallback = null)
    {
        $q = $this->db->table('accounts')->where('code', $code)->get();
        $row = ($q && is_object($q)) ? $q->getRow() : null;
        if ($row) return (int) $row->id;
        if ($fallback) {
            $qFallback = $this->db->table('accounts')->where('code', $fallback)->get();
            $rowFallback = ($qFallback && is_object($qFallback)) ? $qFallback->getRow() : null;
            if ($rowFallback) return (int) $rowFallback->id;
        }
        return null;
    }

    /**
     * Resolve Debit Cash/Bank Account ID based on payment method
     */
    public function resolveDebitAccountId($paymentMethod, $defaultCode = '1111')
    {
        $pMethod = strtolower(trim($paymentMethod ?? 'tunai'));
        if ($pMethod === 'qris') {
            return $this->getAccountId('1121', '1-103');
        } elseif (in_array($pMethod, ['transfer', 'debit', 'credit', 'edc', 'transfer_bca', 'bank'])) {
            return $this->getAccountId('112', '1-102');
        }
        return $this->getAccountId($defaultCode, '1-101') ?: $this->getAccountId('111', '1-101');
    }

    /**
     * Generate unique journal number (JV-YYYYMMDD-XXXX)
     */
    public function generateJournalNo()
    {
        $today = date('Ymd');
        $nextNum = 1;

        try {
            $q = $this->db->table('journal_entries')
                          ->like('journal_no', 'JV-' . $today . '-', 'after')
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->get();
            $lastEntry = ($q && is_object($q)) ? $q->getRow() : null;
            if ($lastEntry && !empty($lastEntry->journal_no)) {
                if (preg_match('/JV-\d+-(\d+)/', $lastEntry->journal_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
            }
        } catch (\Throwable $e) {
            $nextNum = (int) date('His');
        }

        return 'JV-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update account balance based on debit/credit
     */
    protected function applyBalanceMovement($accountId, $debit, $credit)
    {
        if (!$accountId) return;
        $q = $this->db->table('accounts')->where('id', $accountId)->get();
        $acc = ($q && is_object($q)) ? $q->getRow() : null;
        if (!$acc) return;

        $net = 0;
        if (isset($acc->normal_balance) && $acc->normal_balance === 'debit') {
            $net = $debit - $credit;
        } else {
            $net = $credit - $debit;
        }
        $newBal = (float)($acc->balance ?? 0) + $net;
        $this->db->table('accounts')->where('id', $accountId)->update(['balance' => $newBal]);
    }

    /**
     * Standard 1 Debit vs 1 Credit Journal
     */
    public function postJournal($transactionType, $referenceId, $amount, $description, $paymentMethod = null, $sourceModule = 'Keuangan')
    {
        $amount = (float) $amount;
        if ($amount <= 0) {
            return [
                'status'  => 'success',
                'message' => 'Nilai transaksi 0, tidak perlu penjurnalan.'
            ];
        }

        // 1. Fetch account mapping
        $mappingQ = $this->db->table('transaction_account_mappings')
                             ->where('transaction_type', $transactionType)
                             ->get();
        $mapping = ($mappingQ && is_object($mappingQ)) ? $mappingQ->getRow() : null;

        if (!$mapping) {
            $fallbacks = [
                'CLINIC_PAYMENT' => (object)['debit_account_id' => $this->getAccountId('1111', '1-101'), 'credit_account_id' => $this->getAccountId('411', '4-101')],
                'PHARMACY_SALE'  => (object)['debit_account_id' => $this->getAccountId('1111', '1-101'), 'credit_account_id' => $this->getAccountId('412', '4-102')],
                'RESTO_SALE'     => (object)['debit_account_id' => $this->getAccountId('1111', '1-101'), 'credit_account_id' => $this->getAccountId('4-103', '11')],
                'FEE_EXPENSE'    => (object)['debit_account_id' => $this->getAccountId('242', '6-102'), 'credit_account_id' => $this->getAccountId('241', '2-102')]
            ];
            $mapping = $fallbacks[$transactionType] ?? null;
        }

        if (!$mapping) {
            return [
                'status'  => 'error',
                'message' => 'Mapping akun untuk tipe transaksi ' . $transactionType . ' belum diatur.'
            ];
        }

        $debitAccountId = $mapping->debit_account_id;
        $creditAccountId = $mapping->credit_account_id;

        if (!empty($paymentMethod) && in_array($transactionType, ['CLINIC_PAYMENT', 'PHARMACY_SALE', 'RESTO_SALE'])) {
            $resolvedDebit = $this->resolveDebitAccountId($paymentMethod);
            if ($resolvedDebit) $debitAccountId = $resolvedDebit;
        }

        $journalNo = $this->generateJournalNo();

        // Header
        $this->db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => $sourceModule,
            'reference_id'  => $referenceId,
            'description'   => $description,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $this->db->insertID();

        // Debit
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $debitAccountId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);
        $this->applyBalanceMovement($debitAccountId, $amount, 0.00);

        // Credit
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $creditAccountId,
            'debit'      => 0.00,
            'credit'     => $amount
        ]);
        $this->applyBalanceMovement($creditAccountId, 0.00, $amount);

        return [
            'status'     => 'success',
            'journal_no' => $journalNo,
            'message'    => 'Jurnal transaksi berhasil dibukukan.'
        ];
    }

    /**
     * JURNAL UMUM PERAKUN (DETAIL BAGI HASIL APOTEK PERSIS EXCEL KLIEN)
     * 
     * Penjualan Obat Resep (Total misal 85.000):
     *  - Debit: Kas Tunai Apotek (1111 / 111 / 1121 / 112)
     *  - Kredit:
     *    1. Obat (Untuk Pembelian Obat Lagi) [511] = 44% (default)
     *    2. Utang Pajak [231] = 11%
     *    3. Penunjang [512] = 9%
     *    4. Obat Resep [513] = 7%
     *    5. ADM [514] = 24%
     *    6. Utang Fee Dokter [241] = doctor % (default 5%)
     * 
     * Penjualan Obat Bebas (Total misal 100.000):
     *  - Debit: Kas Tunai Apotek
     *  - Kredit:
     *    1. Obat (Untuk Pembelian Obat Lagi) [511] = 49%
     *    2. Utang Pajak [231] = 11%
     *    3. Penunjang [512] = 9%
     *    4. Obat Resep [513] = 7%
     *    5. ADM [514] = 24%
     * 
     * Konsultasi Online:
     *  - Debit: Kas Tunai Apotek
     *  - Kredit: Pembelian Obat, Pajak, Penunjang, Obat Resep, ADM, Utang Fee Dokter, Pendapatan Jasa Dokter
     */
    public function postPharmacySplitJournal($saleType, $referenceId, $amount, $description, $paymentMethod = 'tunai', $doctorFeePercent = 5.00, $sourceModule = 'Farmasi Apotek', $doctorFeeNominal = null)
    {
        $amount = (float) $amount;
        if ($amount <= 0) {
            return [
                'status'  => 'success',
                'message' => 'Nilai transaksi 0, tidak perlu penjurnalan.'
            ];
        }

        $type = strtolower($saleType);
        $categoryCode = 'PENJUALAN_OBAT_BEBAS';
        if ($type === 'online' || $type === 'konsul_online' || $type === 'konsultasi_online') {
            $categoryCode = 'KONSULTASI_ONLINE';
        } elseif ($type === 'resep' || $type === 'obat_resep') {
            $categoryCode = 'PENJUALAN_OBAT_RESEP';
        }

        if ($sourceModule === 'Farmasi Apotek' || empty($sourceModule)) {
            if ($categoryCode === 'KONSULTASI_ONLINE') {
                $sourceModule = 'Kasir Apotek (Konsultasi Online)';
            } elseif ($categoryCode === 'PENJUALAN_OBAT_RESEP') {
                $sourceModule = 'Kasir Apotek (Obat Resep)';
            } else {
                $sourceModule = 'Kasir Apotek (Obat Bebas)';
            }
        }

        if ($referenceId) {
            $exQ = $this->db->table('journal_entries')
                                 ->where('source_module', $sourceModule)
                                 ->where('reference_id', $referenceId)
                                 ->get();
            $existing = ($exQ && is_object($exQ)) ? $exQ->getRow() : null;
            if ($existing) {
                return [
                    'status'     => 'success',
                    'journal_no' => $existing->journal_no ?? '',
                    'message'    => 'Jurnal penjualan obat sudah pernah dibukukan sebelumnya.'
                ];
            }
        }

        // 1. Resolve Debit Account (Kas Tunai / Kas Digital / Kas Bank)
        $debitAccountId = $this->resolveDebitAccountId($paymentMethod, '1111');
        if (!$debitAccountId) {
            $debitAccountId = $this->getAccountId('111', '1-101');
        }

        // 3. Try to load dynamic rules from database
        $catQ = $this->db->table('journal_categories')
                               ->where('category_code', $categoryCode)
                               ->where('is_active', 1)
                               ->get();
        $dbCategory = ($catQ && is_object($catQ)) ? $catQ->getRow() : null;

        $dbRules = [];
        if ($dbCategory) {
            $rulesQ = $this->db->table('journal_category_rules')
                                ->where('category_id', $dbCategory->id)
                                ->where('is_active', 1)
                                ->orderBy('sort_order', 'ASC')
                                ->get();
            $dbRules = ($rulesQ && is_object($rulesQ)) ? $rulesQ->getResult() : [];
        }

        // 4. Calculate Credits Allocation per type
        $credits = []; // Array of ['account_id' => ..., 'code' => ..., 'name' => ..., 'amount' => ...]

        if (!empty($dbRules)) {
            // Process dynamic database rules
            if ($categoryCode === 'KONSULTASI_ONLINE') {
                // Formula KONSULTASI ONLINE:
                // Jasa Dokter Rp 20.000 (Tetap) + Utang Fee Dokter (Manual) dikurangkan dari Kas.
                // Sisa pengurangan dikalikan persentase 5 pos (Obat, Utang Pajak, Penunjang, Obat Resep, ADM).
                $jasaDokterNom = 20000.0;
                $feeDokterNom = ($doctorFeeNominal !== null && (float)$doctorFeeNominal >= 0) ? (float)$doctorFeeNominal : 0.0;

                foreach ($dbRules as $r) {
                    if ($r->position === 'debit') {
                        if (!empty($r->account_id) && in_array(strtolower($paymentMethod), ['tunai', 'cash', ''])) {
                            $debitAccountId = (int)$r->account_id;
                        }
                        continue;
                    }
                    if ($r->formula_code === 'FIXED_NOMINAL' || stripos($r->item_name, 'JASA DOKTER') !== false) {
                        if ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                            $jasaDokterNom = (float)$r->fixed_amount_value;
                        }
                    } elseif ($r->formula_code === 'DOCTOR_FEE_NOMINAL' || stripos($r->item_name, 'FEE DOKTER') !== false) {
                        if ($doctorFeeNominal === null && $r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                            $feeDokterNom = (float)$r->fixed_amount_value;
                        }
                    }
                }

                $basisSisa = max(0.0, $amount - ($feeDokterNom + $jasaDokterNom));

                foreach ($dbRules as $r) {
                    if ($r->position === 'debit') continue;

                    $accId = (int)$r->account_id;
                    $pct = (float)$r->percentage_value;
                    $nom = 0.0;

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

                    $credits[] = [
                        'account_id' => $accId,
                        'name'       => $r->item_name,
                        'amount'     => $nom,
                        'pct'        => $pct
                    ];
                }
            } else {
                $docPct = ($doctorFeePercent !== null && $doctorFeePercent >= 0) ? (float)$doctorFeePercent : 5.00;

                foreach ($dbRules as $r) {
                    if ($r->position === 'debit') {
                        if (!empty($r->account_id) && in_array(strtolower($paymentMethod), ['tunai', 'cash', ''])) {
                            $debitAccountId = (int)$r->account_id;
                        }
                        continue;
                    }

                    $accId = (int)$r->account_id;
                    $pct = (float)$r->percentage_value;
                    $nom = 0.0;

                    if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                        $pct = $docPct;
                        $nom = round(($amount * ($pct / 100.0)), 2);
                    } elseif ($r->formula_code === 'DYNAMIC_OBAT_REMAINDER' && $categoryCode === 'PENJUALAN_OBAT_RESEP') {
                        $dynamicObatPct = max(10.0, 49.0 - $docPct);
                        $pct = $dynamicObatPct;
                        $nom = round(($amount * ($pct / 100.0)), 2);
                    } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $nom = (float)$r->fixed_amount_value;
                    } else {
                        $nom = round(($amount * ($pct / 100.0)), 2);
                    }

                    $credits[] = [
                        'account_id' => $accId,
                        'name'       => $r->item_name,
                        'amount'     => $nom,
                        'pct'        => $pct
                    ];
                }
            }
        } else {
            // Fallback hardcoded if DB table empty
            if ($type === 'resep' || $type === 'obat_resep') {
                $pajakPct = 11.0;
                $penunjangPct = 9.0;
                $obatResepPct = 7.0;
                $admPct = 24.0;
                $docPct = ($doctorFeePercent !== null && $doctorFeePercent >= 0) ? (float)$doctorFeePercent : 5.00;
                
                $maxDocPct = 100.0 - ($pajakPct + $penunjangPct + $obatResepPct + $admPct);
                if ($docPct > $maxDocPct) {
                    $docPct = $maxDocPct;
                }
                $obatPct = max(0.0, $maxDocPct - $docPct);
                
                $credits = [
                    ['account_id' => $this->getAccountId('511', '5-101'), 'name' => 'Obat (Untuk Pembelian Obat Lagi)', 'amount' => round(($amount * ($obatPct / 100.0)), 2)],
                    ['account_id' => $this->getAccountId('231', '2-101'), 'name' => 'Utang Pajak',                    'amount' => round(($amount * ($pajakPct / 100.0)), 2)],
                    ['account_id' => $this->getAccountId('512', '6-101'), 'name' => 'Penunjang',                      'amount' => round(($amount * ($penunjangPct / 100.0)), 2)],
                    ['account_id' => $this->getAccountId('513', '5-101'), 'name' => 'Obat Resep',                     'amount' => round(($amount * ($obatResepPct / 100.0)), 2)],
                    ['account_id' => $this->getAccountId('514', '6-101'), 'name' => 'ADM',                            'amount' => round(($amount * ($admPct / 100.0)), 2)],
                    ['account_id' => $this->getAccountId('241', '2-102'), 'name' => 'Utang Fee Dokter',               'amount' => round(($amount * ($docPct / 100.0)), 2)],
                ];
            } elseif ($type === 'konsul_online' || $type === 'online') {
                $jasaDokter = 20000.0;
                $feeDokter = ($doctorFeeNominal !== null && (float)$doctorFeeNominal >= 0) ? (float)$doctorFeeNominal : 0.0;
                $basisSisa = max(0.0, $amount - ($feeDokter + $jasaDokter));

                $credits = [
                    ['account_id' => $this->getAccountId('424', '4-101'), 'name' => 'Pendapatan Jasa Dokter', 'amount' => $jasaDokter],
                    ['account_id' => $this->getAccountId('241', '2-102'), 'name' => 'Utang Fee Dokter',     'amount' => $feeDokter],
                    ['account_id' => $this->getAccountId('511', '5-101'), 'name' => 'Obat',                  'amount' => round($basisSisa * 0.49, 2)],
                    ['account_id' => $this->getAccountId('231', '2-101'), 'name' => 'Utang Pajak',          'amount' => round($basisSisa * 0.11, 2)],
                    ['account_id' => $this->getAccountId('512', '6-101'), 'name' => 'Penunjang',            'amount' => round($basisSisa * 0.09, 2)],
                    ['account_id' => $this->getAccountId('513', '5-101'), 'name' => 'Obat Resep',           'amount' => round($basisSisa * 0.07, 2)],
                    ['account_id' => $this->getAccountId('514', '6-101'), 'name' => 'ADM',                  'amount' => round($basisSisa * 0.24, 2)],
                ];
            } else {
                $credits = [
                    ['account_id' => $this->getAccountId('511', '5-101'), 'name' => 'Obat (Untuk Pembelian Obat Lagi)', 'amount' => round(($amount * 0.49), 2)],
                    ['account_id' => $this->getAccountId('231', '2-101'), 'name' => 'Utang Pajak',                    'amount' => round(($amount * 0.11), 2)],
                    ['account_id' => $this->getAccountId('512', '6-101'), 'name' => 'Penunjang',                      'amount' => round(($amount * 0.09), 2)],
                    ['account_id' => $this->getAccountId('513', '5-101'), 'name' => 'Obat Resep',                     'amount' => round(($amount * 0.07), 2)],
                    ['account_id' => $this->getAccountId('514', '6-101'), 'name' => 'ADM',                            'amount' => round(($amount * 0.24), 2)],
                ];
            }
        }

        // 5. Compute nominal amounts & auto-balance penny rounding
        $allocatedItems = [];
        $totalCreditsCalculated = 0;

        foreach ($credits as $c) {
            $nom = (float)$c['amount'];
            $accId = $c['account_id'] ?: $this->getAccountId('511', '5-101');
            $allocatedItems[] = [
                'account_id' => $accId,
                'name'       => $c['name'],
                'amount'     => $nom
            ];
            $totalCreditsCalculated += $nom;
        }

        // Auto-balance difference (round diff) to primary medicine account (allocatedItems[0])
        $diff = round($amount - $totalCreditsCalculated, 2);
        if (abs($diff) > 0.0001 && count($allocatedItems) > 0) {
            $allocatedItems[0]['amount'] += $diff;
            if ($allocatedItems[0]['amount'] < 0) {
                $overflow = abs($allocatedItems[0]['amount']);
                $allocatedItems[0]['amount'] = 0.00;
                for ($k = count($allocatedItems) - 1; $k > 0 && $overflow > 0.0001; $k--) {
                    $canDeduct = min($allocatedItems[$k]['amount'], $overflow);
                    $allocatedItems[$k]['amount'] = round($allocatedItems[$k]['amount'] - $canDeduct, 2);
                    $overflow = round($overflow - $canDeduct, 2);
                }
            }
        }

        // 4. Create Journal Entry Header
        $journalNo = $this->generateJournalNo();
        $this->db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => $sourceModule,
            'reference_id'  => $referenceId,
            'description'   => $description,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $this->db->insertID();

        // 5. Insert Debit Line (Kas Tunai / Bank / QRIS)
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $debitAccountId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);
        $this->applyBalanceMovement($debitAccountId, $amount, 0.00);

        // 6. Insert Credit Lines (Alokasi Per-Akun)
        foreach ($allocatedItems as $item) {
            if ($item['amount'] <= 0 || !$item['account_id']) continue;

            $this->db->table('journal_entry_details')->insert([
                'journal_id' => $journalId,
                'account_id' => $item['account_id'],
                'debit'      => 0.00,
                'credit'     => $item['amount']
            ]);
            $this->applyBalanceMovement($item['account_id'], 0.00, $item['amount']);
        }

        return [
            'status'     => 'success',
            'journal_no' => $journalNo,
            'message'    => 'Jurnal bagi hasil transaksi apotek berhasil dibukukan.'
        ];
    }

    /**
     * JURNAL UMUM PERAKUN (DETAIL BAGI HASIL KLINIK PERSIS EXCEL KLIEN)
     * 
     * Konsultasi Klinik (Total misal 150.000):
     *  - Debit: Kas Tunai Klinik (1111) = 150.000
     *  - Kredit:
     *    1. Utang Fee Dokter [241] = doctor % (default 66.67%)
     *    2. Utang Fee Karyawan [242] = Proporsional (10.000/150.000)
     *    Sisa (Remaining):
     *    3. Fasilitas Klinik [425] = 67.24% dari Sisa
     *    4. BMHP [415] = 19.025% dari Sisa
     *    5. Administrasi [421] = 10.9875% dari Sisa
     *    6. Konseling Farmasi [422] = 2.7475% dari Sisa
     */
    public function postClinicSplitJournal($referenceId, $amount, $description, $paymentMethod = 'tunai', $doctorFeePercent = 66.67, $sourceModule = 'Kasir Utama', $doctorFeeNominal = null)
    {
        $amount = (float)$amount;
        if ($amount <= 0) return ['status' => 'error', 'message' => 'Amount must be greater than zero.'];

        if ($referenceId) {
            $exQ = $this->db->table('journal_entries')
                                 ->where('source_module', $sourceModule)
                                 ->where('reference_id', $referenceId)
                                 ->like('description', 'Pendapatan Layanan Medis')
                                 ->get();
            $existing = ($exQ && is_object($exQ)) ? $exQ->getRow() : null;
            if ($existing) {
                return [
                    'status'     => 'success',
                    'journal_no' => $existing->journal_no ?? '',
                    'message'    => 'Jurnal layanan klinik sudah pernah dibukukan sebelumnya.'
                ];
            }
        }

        // 1. Resolve Debit Account (Kas Kasir Klinik / Bank / QRIS)
        $debitAccountId = $this->resolveDebitAccountId($paymentMethod, '1111');
        if (!$debitAccountId) {
            $debitAccountId = $this->getAccountId('1111', '1-101') ?: $this->getAccountId('111');
        }

        // 2. Fetch Category RAWAT_JALAN_POLI
        $categoryCode = 'RAWAT_JALAN_POLI';
        $catQ = $this->db->table('journal_categories')
                               ->where('category_code', $categoryCode)
                               ->where('is_active', 1)
                               ->get();
        $dbCategory = ($catQ && is_object($catQ)) ? $catQ->getRow() : null;

        $dbRules = [];
        if ($dbCategory) {
            $rulesQ = $this->db->table('journal_category_rules')
                                ->where('category_id', $dbCategory->id)
                                ->where('is_active', 1)
                                ->orderBy('sort_order', 'ASC')
                                ->get();
            $dbRules = ($rulesQ && is_object($rulesQ)) ? $rulesQ->getResult() : [];
        }

        $credits = [];
        if (!empty($dbRules)) {
            $hasFixedDocFee = ($doctorFeeNominal !== null && (float)$doctorFeeNominal > 0);
            $docPct = ($doctorFeePercent !== null && (float)$doctorFeePercent >= 0) ? (float)$doctorFeePercent : 66.67;

            // 1. Hitung Tahap 1: Pengurang Utama (Dokter & Fee Karyawan)
            $docFeeNom = 0.0;
            $empFeeNominal = 0.0;
            $hasRemainderTier = false;

            foreach ($dbRules as $r) {
                if ($r->position === 'debit') {
                    if (!empty($r->account_id) && in_array(strtolower($paymentMethod), ['tunai', 'cash', ''])) {
                        $debitAccountId = (int)$r->account_id;
                    }
                    continue;
                }

                if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                    if ($hasFixedDocFee) {
                        $docFeeNom = round((float)$doctorFeeNominal, 2);
                        $docPct = ($amount > 0) ? round(($docFeeNom / $amount) * 100, 4) : 0;
                    } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                        $docFeeNom = (float)$r->fixed_amount_value;
                        $docPct = ($amount > 0) ? round(($docFeeNom / $amount) * 100, 4) : 0;
                    } elseif (abs($docPct - 66.6667) < 0.1 || abs($docPct - 66.67) < 0.1) {
                        $docFeeNom = round(($amount * 2 / 3), 2);
                    } else {
                        $docFeeNom = round(($amount * ($docPct / 100.0)), 2);
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

            $docFeeNominal = $docFeeNom;

            // Sisa Bersih Dana Klinik (Tier 2: Fasilitas, BMHP, Administrasi, Konseling)
            $clinicRemainder = max(0, $amount - $docFeeNominal - $empFeeNominal);

            // 2. Alokasikan ke seluruh sub-pos kredit
            foreach ($dbRules as $r) {
                if ($r->position === 'debit') continue;

                $accId = (int)$r->account_id;
                $pct = (float)$r->percentage_value;
                $nom = 0.0;

                if ($r->calc_type === 'dynamic_fee' || $r->formula_code === 'DOCTOR_FEE_PCT') {
                    $pct = $docPct;
                    $nom = $docFeeNominal;
                } elseif ($r->formula_code === 'EMPLOYEE_FEE' || stripos($r->item_name, 'FEE KARYAWAN') !== false) {
                    $nom = $empFeeNominal;
                    $pct = $amount > 0 ? round(($nom / $amount) * 100, 2) : 0;
                } elseif ($r->calc_type === 'fixed_amount' && (float)$r->fixed_amount_value > 0) {
                    $nom = (float)$r->fixed_amount_value;
                    $pct = $amount > 0 ? round(($nom / $amount) * 100, 2) : 0;
                } elseif ($hasRemainderTier && ($r->formula_code === 'REMAINDER_TIER' || $r->formula_code === 'DYNAMIC_OBAT_REMAINDER')) {
                    if ($pct > 25.0) {
                        $nom = round(($clinicRemainder * ($pct / 100.0)), 2);
                    } else {
                        $nom = round(($amount * ($pct / 100.0)), 2);
                    }
                } else {
                    $nom = round(($amount * ($pct / 100.0)), 2);
                }

                $credits[] = [
                    'account_id'   => $accId,
                    'name'         => $r->item_name,
                    'amount'       => $nom,
                    'pct'          => $pct,
                    'formula_code' => $r->formula_code
                ];
            }
        } else {
            // Fallback if no rules found (Persis Excel Klien)
            $docNom = round($amount * 2 / 3, 2);
            $empNom = 10000.00;
            $rem = max(0, $amount - $docNom - $empNom);
            $credits = [
                ['account_id' => $this->getAccountId('424', '4-101') ?: $this->getAccountId('241'), 'name' => 'Dokter', 'amount' => $docNom, 'formula_code' => 'DOCTOR_FEE_PCT'],
                ['account_id' => $this->getAccountId('242', '6-102'), 'name' => 'Utang Fee Karyawan', 'amount' => $empNom, 'formula_code' => 'EMPLOYEE_FEE'],
                ['account_id' => $this->getAccountId('425', '4-105'), 'name' => 'Fasilitas Klinik', 'amount' => round($rem * 0.6724, 2), 'formula_code' => 'REMAINDER_TIER'],
                ['account_id' => $this->getAccountId('415', '4-106'), 'name' => 'BMHP', 'amount' => round($rem * 0.19025, 2), 'formula_code' => 'REMAINDER_TIER'],
                ['account_id' => $this->getAccountId('421', '4-107'), 'name' => 'Administrasi', 'amount' => round($rem * 0.109875, 2), 'formula_code' => 'REMAINDER_TIER'],
                ['account_id' => $this->getAccountId('422', '4-108'), 'name' => 'Konseling Farmasi', 'amount' => round($rem * 0.027475, 2), 'formula_code' => 'DYNAMIC_OBAT_REMAINDER']
            ];
        }

        // Auto penny-rounding balance (selisih penyeimbang)
        $allocatedItems = [];
        $totalCreditsCalculated = 0;
        $remainderIndex = -1;

        foreach ($credits as $idx => $c) {
            $nom = (float)$c['amount'];
            $accId = $c['account_id'] ?: $this->getAccountId('425', '4-105');
            $allocatedItems[] = [
                'account_id' => $accId,
                'name'       => $c['name'],
                'amount'     => $nom
            ];
            $totalCreditsCalculated += $nom;
            if ($c['formula_code'] === 'DYNAMIC_OBAT_REMAINDER') {
                $remainderIndex = $idx;
            }
        }

        $diff = round($amount - $totalCreditsCalculated, 2);
        if (abs($diff) > 0.0001 && count($allocatedItems) > 0) {
            $targetIdx = ($remainderIndex >= 0) ? $remainderIndex : (count($allocatedItems) - 1);
            $allocatedItems[$targetIdx]['amount'] += $diff;
            if ($allocatedItems[$targetIdx]['amount'] < 0) {
                $overflow = abs($allocatedItems[$targetIdx]['amount']);
                $allocatedItems[$targetIdx]['amount'] = 0.00;
                for ($k = count($allocatedItems) - 1; $k >= 0 && $overflow > 0.0001; $k--) {
                    if ($k === $targetIdx) continue;
                    $canDeduct = min($allocatedItems[$k]['amount'], $overflow);
                    $allocatedItems[$k]['amount'] = round($allocatedItems[$k]['amount'] - $canDeduct, 2);
                    $overflow = round($overflow - $canDeduct, 2);
                }
            }
        }

        $journalNo = $this->generateJournalNo();

        // Insert Header
        $this->db->table('journal_entries')->insert([
            'journal_no'    => $journalNo,
            'entry_date'    => date('Y-m-d'),
            'source_module' => $sourceModule,
            'reference_id'  => $referenceId,
            'description'   => $description,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $journalId = $this->db->insertID();

        // Insert Debit
        $this->db->table('journal_entry_details')->insert([
            'journal_id' => $journalId,
            'account_id' => $debitAccountId,
            'debit'      => $amount,
            'credit'     => 0.00
        ]);
        $this->applyBalanceMovement($debitAccountId, $amount, 0.00);

        // Insert Credits
        foreach ($allocatedItems as $item) {
            $amt = (float)$item['amount'];
            if ($amt <= 0 || empty($item['account_id'])) continue;

            $this->db->table('journal_entry_details')->insert([
                'journal_id' => $journalId,
                'account_id' => $item['account_id'],
                'debit'      => 0.00,
                'credit'     => $amt
            ]);
            $this->applyBalanceMovement($item['account_id'], 0.00, $amt);
        }

        return [
            'status'     => 'success',
            'journal_no' => $journalNo,
            'message'    => 'Jurnal bagi hasil layanan klinik berhasil dibukukan.'
        ];
    }

    /**
     * Sinkronisasi transaksi terbayar yang belum terjurnal (Resto POS, Apotek OTC, Kasir Klinik)
     */
    public function syncUnpostedTransactions()
    {
        // 1. Sync Paid Resto Orders
        $paidRestoOrdersQ = $this->db->table('restaurant_orders')
                                     ->where('payment_status', 'paid')
                                     ->get();
        $paidRestoOrders = ($paidRestoOrdersQ && is_object($paidRestoOrdersQ)) ? $paidRestoOrdersQ->getResult() : [];

        foreach ($paidRestoOrders as $ro) {
            $exists = $this->db->table('journal_entries')
                               ->where('source_module', 'Resto POS')
                               ->where('reference_id', $ro->id)
                               ->countAllResults();
            if ($exists === 0 && floatval($ro->grand_total) > 0) {
                $detailsQ = $this->db->table('restaurant_order_details rod')
                                     ->select('rod.qty, rod.price, rm.name')
                                     ->join('restaurant_menus rm', 'rm.id = rod.menu_id', 'left')
                                     ->where('rod.order_id', $ro->id)
                                     ->get();
                $details = ($detailsQ && is_object($detailsQ)) ? $detailsQ->getResult() : [];
                $itemStrs = [];
                foreach ($details as $d) {
                    $itemStrs[] = ($d->name ?: 'Item') . ' (' . (int)$d->qty . 'x @Rp ' . number_format($d->price, 0, ',', '.') . ')';
                }
                $itemSummary = !empty($itemStrs) ? implode(', ', $itemStrs) : 'Menu Resto';

                $this->postJournal(
                    'RESTO_SALE',
                    $ro->id,
                    $ro->grand_total,
                    'Pendapatan POS Resto & Nutrisi - ' . $ro->order_no . ' (' . $ro->customer_name . '): ' . $itemSummary,
                    $ro->payment_method,
                    'Resto POS'
                );
            }
        }

        // 2. Sync OTC Pharmacy Sales (Direct Sales) using Split Journal Engine
        $otcSalesQ = $this->db->table('pharmacy_sales')
                              ->where('grand_total >', 0)
                              ->get();
        $otcSales = ($otcSalesQ && is_object($otcSalesQ)) ? $otcSalesQ->getResult() : [];

        foreach ($otcSales as $os) {
            $rawType = $os->prescription_type ?? 'bebas';
            if ($rawType === 'online' || $rawType === 'konsul_online') {
                $saleType     = 'online';
                $targetModule = 'Kasir Apotek (Konsultasi Online)';
            } elseif ($rawType === 'resep' || $rawType === 'racikan') {
                $saleType     = 'resep';
                $targetModule = 'Kasir Apotek (Obat Resep)';
            } else {
                $saleType     = 'bebas';
                $targetModule = 'Kasir Apotek (Obat Bebas)';
            }

            $exists = $this->db->table('journal_entries')
                               ->where('reference_id', $os->id)
                               ->groupStart()
                                   ->where('source_module', $targetModule)
                                   ->orWhere('source_module', 'Apotek (Konsultasi Online)')
                                   ->orWhere('source_module', 'Apotek (Obat Resep)')
                                   ->orWhere('source_module', 'Apotek (Obat Bebas)')
                                   ->orLike('source_module', 'Kasir Apotek')
                               ->groupEnd()
                               ->countAllResults();

            if ($exists === 0 && floatval($os->grand_total) > 0) {
                $detailsQ = $this->db->table('pharmacy_sale_details psd')
                                     ->select('psd.qty, psd.price, psd.tusla, psd.embalase, m.name')
                                     ->join('medicines m', 'm.id = psd.medicine_id', 'left')
                                     ->where('psd.sale_id', $os->id)
                                     ->get();
                $details = ($detailsQ && is_object($detailsQ)) ? $detailsQ->getResult() : [];
                $itemStrs = [];
                $totalTusla = 0;
                $totalEmbalase = 0;
                foreach ($details as $d) {
                    $priceStr = number_format($d->price * $d->qty, 0, ',', '.');
                    $tuslaStr = number_format($d->tusla, 0, ',', '.');
                    $embalaseStr = number_format($d->embalase, 0, ',', '.');
                    
                    $totalTusla += $d->tusla;
                    $totalEmbalase += $d->embalase;

                    $itemStrs[] = ($d->name ?: 'Obat') . ' (' . (int)$d->qty . 'x) (Rp ' . $priceStr . ') | tusla (Rp ' . $tuslaStr . ') | embarse (Rp ' . $embalaseStr . ')';
                }
                $itemSummary = !empty($itemStrs) ? "\n" . implode(", \n", $itemStrs) : 'Obat Bebas';
                if (!empty($itemStrs)) {
                    $itemSummary .= ",\ntotal tusla : Rp " . number_format($totalTusla, 0, ',', '.') . "\ntotal embarse : Rp " . number_format($totalEmbalase, 0, ',', '.');
                }

                $docFeePct = 5.00;
                $docName = '';
                if (!empty($os->doctor_id)) {
                    $docQ = $this->db->table('doctors')->where('id', $os->doctor_id)->get();
                    $docRow = ($docQ && is_object($docQ)) ? $docQ->getRow() : null;
                    if ($docRow) {
                        $docName = $docRow->name;
                        if (isset($docRow->prescription_fee_percent)) {
                            $docFeePct = (float)$docRow->prescription_fee_percent;
                        }
                    }
                }

                $docTag = '';
                if (!empty($docName)) {
                    $cleanDoc = (stripos($docName, 'dr') === 0 || stripos($docName, 'Dr') === 0) ? $docName : ('Dr. ' . $docName);
                    $docTag = ' (' . $cleanDoc . ')';
                }

                $docFeeNom = isset($os->doctor_fee_nominal) ? (float)$os->doctor_fee_nominal : null;

                $this->postPharmacySplitJournal(
                    $saleType,
                    $os->id,
                    $os->grand_total,
                    'Penjualan Farmasi ' . ucfirst($saleType) . ' - ' . $os->sale_no . ' (' . $os->customer_name . '): ' . $itemSummary . $docTag,
                    $os->payment_method,
                    $docFeePct,
                    $targetModule,
                    $docFeeNom
                );
            }
        }
    }
}
