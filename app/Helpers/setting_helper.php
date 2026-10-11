<?php

if (!function_exists('clinic_setting')) {
    /**
     * Ambil nilai konfigurasi identitas klinik / sistem secara dinamis & cached
     */
    function clinic_setting($key, $default = '')
    {
        static $cachedSettings = null;

        if ($key === '__clear_cache__') {
            $cachedSettings = null;
            return null;
        }

        if ($cachedSettings === null) {
            $cachedSettings = [];
            try {
                $db = \Config\Database::connect('default');
                if ($db->tableExists('system_settings')) {
                    $rows = $db->table('system_settings')->get()->getResult();
                    foreach ($rows as $r) {
                        $cachedSettings[$r->setting_key] = $r->setting_value;
                    }
                }
            } catch (\Exception $e) {
                // Fallback default
            }
        }

        // Global default fallbacks if not yet in database (Sawamawa Medical Center Single Source of Truth)
        $defaults = [
            // Identitas & Profil Resmi
            'clinic_name'           => 'Sawamawa Medical Center',
            'clinic_tagline'        => 'Pusat Layanan Medis Terpadu & Terpercaya',
            'clinic_address'        => 'Jl. Kebangsaan No. 12, Sumbawa Besar, NTB',
            'clinic_phone'          => '(0371) 23456 / 081122334455',
            'clinic_email'          => 'info@sawamawamedicalcenter.id',
            'clinic_website'        => 'www.sawamawamedicalcenter.id',
            'clinic_license_number' => '445/012/DINKES/2024',
            'clinic_license'        => '445/012/DINKES/2024',
            'clinic_faskes_code'    => '52040101',
            'clinic_director'       => 'dr. Andi Wijaya, Sp.PD',
            'clinic_head_doctor'    => 'dr. Andi Wijaya, Sp.PD',
            'clinic_head_sip'       => '503/449/SIP-D/2022',
            'clinic_bpjs_active'    => 'true',
            
            // Branding & Dokumen Legal
            'clinic_logo'           => '',
            'clinic_favicon'        => '',
            'clinic_stamp'          => '',
            'clinic_watermark'      => 'SAWAMAWA',
            'clinic_footer_note'    => 'Dokumen resmi ini diterbitkan secara digital oleh SIM-Klinik Sawamawa Medical Center.',
            
            // Parameter Bisnis Farmasi & Resto
            'pharmacy_min_stock_alert'       => '10',
            'pharmacy_profit_margin_percent' => '20',
            'pharmacy_tax_percent'           => '10',
            'resto_tax_percent'              => '10',
            'resto_service_charge_percent'   => '5',
            'resto_auto_billing_inpatient'   => 'true',

            // Integrasi WhatsApp Gateway
            'wa_gateway_provider'   => 'fonnte',
            'wa_api_token'          => '',
            'wa_sender_number'      => '081122334455',

            // Pendaftaran Mandiri Online
            'online_registration_active'         => 'true',
            'online_registration_open_time'      => '06:00',
            'online_registration_close_time'     => '21:00',
            'online_registration_closed_message' => 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.',

            // Display TV Antrean & Pemanggil Suara
            'tv_media_type'         => 'slideshow',
            'tv_video_url'          => '',
            'tv_youtube_id'         => 'dQw4w9WgXcQ',
            'voice_gender'          => 'female',
            'voice_chime_type'      => 'hospital_2tone',
            'voice_rate'            => '0.85',
            'voice_pitch'           => '1.0',
            'voice_volume'          => '1.0'
        ];

        return $cachedSettings[$key] ?? ($defaults[$key] ?? $default);
    }
}

if (!function_exists('clear_setting_cache')) {
    /**
     * Kosongkan cache pengaturan dinamis (berguna saat admin baru saja menyimpan perubahan)
     */
    function clear_setting_cache()
    {
        // Re-trigger static reload next time clinic_setting() is invoked
        clinic_setting('__clear_cache__');
    }
}

if (!function_exists('is_online_registration_open')) {
    /**
     * Memeriksa apakah pendaftaran online saat ini dibuka berdasarkan setting & jam operasional
     */
    function is_online_registration_open()
    {
        $active = clinic_setting('online_registration_active', 'true');
        if ($active === 'false' || $active === '0' || $active === false) {
            return false;
        }

        $openTime  = clinic_setting('online_registration_open_time', '06:00');
        $closeTime = clinic_setting('online_registration_close_time', '21:00');

        if (!empty($openTime) && !empty($closeTime)) {
            $currentTime = date('H:i');
            if ($currentTime < $openTime || $currentTime > $closeTime) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('clinic_logo')) {
    /**
     * Dapatkan URL logo resmi klinik
     */
    function clinic_logo()
    {
        $path = clinic_setting('clinic_logo', '');
        if (!empty($path)) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
                return $path;
            }
            if (file_exists(FCPATH . $path)) {
                return base_url($path);
            }
        }
        return null;
    }
}

if (!function_exists('clinic_stamp')) {
    /**
     * Dapatkan URL stempel resmi klinik (untuk e-resep, surat sakit, rujukan, lab)
     */
    function clinic_stamp()
    {
        $path = clinic_setting('clinic_stamp', '');
        if (!empty($path)) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
                return $path;
            }
            if (file_exists(FCPATH . $path)) {
                return base_url($path);
            }
        }
        return null;
    }
}

if (!function_exists('clinic_latest_version')) {
    /**
     * Dapatkan versi sistem terkini dari database system_updates
     */
    function clinic_latest_version()
    {
        $info = clinic_latest_update_info();
        return $info ? ($info->version ?? 'v2.10.0') : 'v2.10.0';
    }
}

if (!function_exists('clinic_latest_update_info')) {
    /**
     * Dapatkan objek rilis pembaruan terkini dari database system_updates
     */
    function clinic_latest_update_info()
    {
        static $cachedInfo = null;
        if ($cachedInfo === null) {
            try {
                $db = \Config\Database::connect('default');
                if ($db->tableExists('system_updates')) {
                    $row = $db->table('system_updates')
                              ->where('is_published', 1)
                              ->orderBy('release_date', 'DESC')
                              ->orderBy('id', 'DESC')
                              ->get(1)
                              ->getRow();
                    if ($row) {
                        $cachedInfo = $row;
                    }
                }
            } catch (\Exception $e) {
                // fallback
            }
        }
        return $cachedInfo;
    }
}

if (!function_exists('clinic_favicon')) {
    /**
     * Dapatkan URL favicon resmi klinik
     */
    function clinic_favicon()
    {
        $path = clinic_setting('clinic_favicon', '');
        if (!empty($path)) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
                return $path;
            }
            if (file_exists(FCPATH . $path)) {
                return base_url($path);
            }
        }
        return null;
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Dapatkan URL foto profil pengguna saat ini atau berdasarkan user_id
     */
    function user_avatar($userId = null)
    {
        if ($userId === null) {
            $userId = session('user_id');
        }
        if (!$userId) {
            return null;
        }

        static $userAvatars = [];
        if (!isset($userAvatars[$userId])) {
            try {
                $db = \Config\Database::connect('default');
                $fields = $db->getFieldNames('users');
                if (in_array('avatar', $fields)) {
                    $user = $db->table('users')->select('avatar')->where('id', $userId)->get()->getRow();
                    $userAvatars[$userId] = $user->avatar ?? null;
                } else {
                    $userAvatars[$userId] = null;
                }
            } catch (\Exception $e) {
                $userAvatars[$userId] = null;
            }
        }

        $avatarPath = $userAvatars[$userId];
        if (!empty($avatarPath) && file_exists(FCPATH . $avatarPath)) {
            return base_url($avatarPath);
        }
        return null;
    }
}

if (!function_exists('current_user_data')) {
    /**
     * Dapatkan objek lengkap data pengguna yang sedang login
     */
    function current_user_data()
    {
        $userId = session('user_id');
        if (!$userId) {
            return null;
        }
        try {
            $db = \Config\Database::connect('default');
            return $db->table('users')
                      ->select('users.*, roles.name as role_name')
                      ->join('roles', 'roles.id = users.role_id')
                      ->where('users.id', $userId)
                      ->get()
                      ->getRow();
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('datatable_server_side')) {
    /**
     * Helper Standar Terpadu DataTables Server-Side Processing (Sesuai Panduan Arsitektur)
     *
     * @param string $tableName Nama tabel utama (contoh: 'patients p' atau 'medicines')
     * @param array $columns Map indeks kolom: [0 => 'p.id', 1 => 'p.no_rm', 2 => 'p.name', ...]
     * @param array $options Konfigurasi tambahan:
     *      - 'select'         => 'p.*, b.name as badge_name'
     *      - 'joins'          => [['table' => 'roles r', 'cond' => 'r.id = p.role_id', 'type' => 'left']]
     *      - 'where'          => ['p.deleted_at' => null] atau string SQL
     *      - 'search_columns' => ['p.no_rm', 'p.name', 'p.nik', 'p.phone']
     *      - 'default_order'  => ['p.id', 'DESC']
     *      - 'row_formatter'  => function($row, $no) { return [$no, ...]; }
     * @return \CodeIgniter\HTTP\ResponseInterface|void
     */
    function datatable_server_side(string $tableName, array $columns, array $options = [])
    {
        $request = \Config\Services::request();
        $response = \Config\Services::response();
        $db = \Config\Database::connect('default');

        $draw        = intval($request->getGet('draw') ?? 1);
        $start       = intval($request->getGet('start') ?? 0);
        $length      = intval($request->getGet('length') ?? 10);
        $searchParam = $request->getGet('search');
        $searchValue = trim(is_array($searchParam) ? ($searchParam['value'] ?? '') : '');

        // 1. Sorting Order
        $orderParam = $request->getGet('order');
        $orderColumnIndex = intval(is_array($orderParam) ? ($orderParam[0]['column'] ?? 0) : 0);
        $orderDir = strtolower(is_array($orderParam) ? ($orderParam[0]['dir'] ?? 'desc') : 'desc');
        if (!in_array($orderDir, ['asc', 'desc'])) {
            $orderDir = 'desc';
        }

        $orderColumn = $columns[$orderColumnIndex] ?? ($options['default_order'][0] ?? $columns[0] ?? 'id');
        if (isset($options['default_order']) && !isset($columns[$orderColumnIndex])) {
            $orderColumn = $options['default_order'][0];
            $orderDir = $options['default_order'][1] ?? $orderDir;
        }

        // 2. Base Builder for Counting Total Records
        $baseBuilder = $db->table($tableName);
        if (!empty($options['joins'])) {
            foreach ($options['joins'] as $join) {
                $baseBuilder->join($join['table'], $join['cond'], $join['type'] ?? 'left');
            }
        }
        if (!empty($options['where'])) {
            if (is_array($options['where'])) {
                $baseBuilder->where($options['where']);
            } else {
                $baseBuilder->where($options['where'], null, false);
            }
        }
        $totalRecords = $baseBuilder->countAllResults(false);

        // 3. Search Filter
        $searchColumns = $options['search_columns'] ?? [];
        if (!empty($searchValue) && !empty($searchColumns)) {
            $baseBuilder->groupStart();
            foreach ($searchColumns as $idx => $sCol) {
                if ($idx === 0) {
                    $baseBuilder->like($sCol, $searchValue);
                } else {
                    $baseBuilder->orLike($sCol, $searchValue);
                }
            }
            $baseBuilder->groupEnd();
        }

        $totalRecordwithFilter = $baseBuilder->countAllResults(false);

        // 4. Fetch Paginated Records
        if (!empty($options['select'])) {
            $baseBuilder->select($options['select']);
        }
        $baseBuilder->orderBy($orderColumn, $orderDir);
        if (!empty($options['secondary_order'])) {
            $secCol = $options['secondary_order'][0];
            $secDir = $options['secondary_order'][1] ?? $orderDir;
            if ($secCol !== $orderColumn) {
                $baseBuilder->orderBy($secCol, $secDir);
            }
        }
        if ($length > 0) {
            $baseBuilder->limit($length, $start);
        }

        $rows = $baseBuilder->get()->getResultArray();
        $data = [];
        $no = $start + 1;

        $formatter = $options['row_formatter'] ?? null;

        foreach ($rows as $row) {
            if (is_callable($formatter)) {
                $data[] = $formatter((object)$row, $no++);
            } else {
                $item = [$no++];
                foreach ($columns as $c) {
                    $cKey = str_contains($c, '.') ? explode('.', $c)[1] : $c;
                    $item[] = $row[$cKey] ?? '';
                }
                $data[] = $item;
            }
        }

        return $response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data'            => $data
        ]);
    }
}

if (!function_exists('clinic_active_queue_count')) {
    /**
     * Hitung total antrean aktif hari ini (waiting & in_progress)
     */
    function clinic_active_queue_count()
    {
        try {
            $db = \Config\Database::connect('default');
            return $db->table('patient_visits')
                      ->where('visit_date', date('Y-m-d'))
                      ->whereIn('status', ['waiting', 'in_progress'])
                      ->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
