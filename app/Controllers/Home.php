<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function __construct()
    {
        helper(['setting', 'form', 'url']);
    }

    public function index()
    {
        $db = \Config\Database::connect('default');

        // 1. Fetch Active Doctors with Polyclinics
        $doctors = $db->table('doctors')
                      ->select('doctors.*, polyclinics.name as polyclinic_name')
                      ->join('polyclinics', 'polyclinics.id = doctors.polyclinic_id', 'left')
                      ->where('doctors.status', 'active')
                      ->orderBy('doctors.name', 'ASC')
                      ->get()
                      ->getResult();

        // 2. Fetch Polyclinics
        $polyclinics = $db->table('polyclinics')
                          ->where('status', 'active')
                          ->orderBy('name', 'ASC')
                          ->get()
                          ->getResult();

        // 3. Fetch Featured Services & Medical Procedures
        $services = $db->table('tindakan')
                       ->select('tindakan.*, categories.name as category_name')
                       ->join('categories', 'categories.id = tindakan.category_id', 'left')
                       ->where('tindakan.status', 'active')
                       ->orderBy('categories.name', 'ASC')
                       ->orderBy('tindakan.price', 'ASC')
                       ->limit(12)
                       ->get()
                       ->getResult();

        // WhatsApp Admin Contact
        $adminWa = clinic_setting('clinic_phone', '6281234567890');

        // Fetch Published Articles
        $articles = [];
        try {
            if ($db->tableExists('articles')) {
                $articles = $db->table('articles')
                               ->where('status', 'published')
                               ->orderBy('id', 'DESC')
                               ->limit(6)
                               ->get()
                               ->getResult();
            }
        } catch (\Throwable $e) {}

        $data = [
            'title'         => 'Sawamawa Medical Center - Klinik Pratama & Pelayanan Kesehatan Terpadu Sumbawa',
            'doctors'       => $doctors,
            'polyclinics'   => $polyclinics,
            'services'      => $services,
            'articles'      => $articles,
            'adminWa'       => $adminWa,
            'isOnlineOpen'  => is_online_registration_open(),
            'openTime'      => clinic_setting('online_registration_open_time', '06:00'),
            'closeTime'     => clinic_setting('online_registration_close_time', '21:00'),
            'closedMessage' => clinic_setting('online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA.')
        ];

        return view('public/landing', $data);
    }

    public function daftarOnline()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            if (!is_online_registration_open()) {
                $closedMsg = clinic_setting('online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Silakan mendaftar via WhatsApp atau datang langsung ke klinik.');
                session()->setFlashdata('error', $closedMsg);
                return redirect()->back()->withInput();
            }

            $patientType = $this->request->getPost('patient_type') ?: 'baru'; // 'baru' atau 'lama'
            $visitType   = $this->request->getPost('visit_type') ?: 'poli'; // 'poli' or 'tindakan'
            $polyId      = $this->request->getPost('polyclinic_id') ? (int)$this->request->getPost('polyclinic_id') : 1;
            $doctorId    = $this->request->getPost('doctor_id') ? (int)$this->request->getPost('doctor_id') : null;
            $serviceId   = $this->request->getPost('service_id') ? (int)$this->request->getPost('service_id') : null;
            $visitDate   = $this->request->getPost('visit_date') ?: date('Y-m-d');
            $complaints  = trim(strip_tags((string)($this->request->getPost('complaints') ?? '')));

            $db->transStart();

            if ($patientType === 'lama') {
                // Pasien Lama: Verifikasi 2-Faktor Aman (No RM / NIK + Tanggal Lahir)
                $identifier = trim(strip_tags((string)$this->request->getPost('old_identifier')));
                $dob        = $this->request->getPost('old_dob');
                $phone      = trim(strip_tags((string)$this->request->getPost('old_phone')));

                if (empty($identifier) || empty($dob)) {
                    session()->setFlashdata('error', 'Mohon masukkan Nomor Rekam Medis (No. RM) atau NIK beserta Tanggal Lahir Anda.');
                    return redirect()->back()->withInput();
                }

                $patient = $db->table('patients')
                              ->groupStart()
                                  ->where('no_rm', $identifier)
                                  ->orWhere('nik', $identifier)
                              ->groupEnd()
                              ->where('date_of_birth', $dob)
                              ->get()
                              ->getRow();

                if (!$patient) {
                    session()->setFlashdata('error', 'Verifikasi data pasien tidak cocok. Pastikan Nomor Rekam Medis / NIK dan Tanggal Lahir sesuai dengan data terdaftar di klinik.');
                    return redirect()->back()->withInput();
                }

                $patientId = $patient->id;
                // Update nomor WhatsApp jika ada pembaruan
                if (!empty($phone)) {
                    $db->table('patients')->where('id', $patientId)->update(['phone' => $phone]);
                }
            } else {
                // Pasien Baru: Pendaftaran Identitas Pasien
                $nik        = trim(strip_tags((string)$this->request->getPost('nik')));
                $name       = trim(strip_tags((string)$this->request->getPost('name')));
                $gender     = $this->request->getPost('gender') ?: 'L';
                $pob        = trim(strip_tags((string)($this->request->getPost('place_of_birth') ?? 'Sumbawa')));
                $dob        = $this->request->getPost('date_of_birth');
                $phone      = trim(strip_tags((string)$this->request->getPost('phone')));
                $address    = trim(strip_tags((string)$this->request->getPost('address')));
                $bpjs       = trim(strip_tags((string)$this->request->getPost('bpjs_number')));

                if (empty($nik) || empty($name) || empty($phone) || empty($dob)) {
                    session()->setFlashdata('error', 'Mohon lengkapi NIK (16 digit), Nama Lengkap, Tanggal Lahir, dan Nomor WhatsApp Anda.');
                    return redirect()->back()->withInput();
                }

                // Cek apakah NIK sudah pernah terdaftar
                $existing = $db->table('patients')->where('nik', $nik)->get()->getRow();
                if ($existing) {
                    $patientId = $existing->id;
                    $db->table('patients')->where('id', $patientId)->update([
                        'phone'   => $phone,
                        'address' => $address ?: $existing->address
                    ]);
                } else {
                    // Terbitkan No RM baru: RM-XXXXXX
                    $lastRM = $db->table('patients')->orderBy('id', 'DESC')->limit(1)->get()->getRow();
                    $nextNum = 1;
                    if ($lastRM && preg_match('/RM-(\d+)/', $lastRM->no_rm, $matches)) {
                        $nextNum = intval($matches[1]) + 1;
                    }
                    $noRm = 'RM-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

                    $patientData = [
                        'no_rm'          => $noRm,
                        'nik'            => $nik,
                        'name'           => $name,
                        'gender'         => $gender,
                        'place_of_birth' => $pob,
                        'date_of_birth'  => $dob,
                        'phone'          => $phone,
                        'address'        => $address,
                        'bpjs_number'    => $bpjs,
                        'created_at'     => date('Y-m-d H:i:s')
                    ];
                    $db->table('patients')->insert($patientData);
                    $patientId = $db->insertID();
                }
            }

            // 2. Generate Nomor Kunjungan: VS-YYYYMMDD-XXXX
            $today = date('Ymd', strtotime($visitDate));
            $visitPrefix = 'VS-' . $today . '-';
            $lastVisit = $db->table('patient_visits')
                            ->where("no_visit LIKE '{$visitPrefix}%'")
                            ->orderBy('id', 'DESC')
                            ->limit(1)
                            ->get()
                            ->getRow();
            $nextVisitSeq = 1;
            if ($lastVisit && preg_match('/VS-\d+-(\d+)/', $lastVisit->no_visit, $m)) {
                $nextVisitSeq = intval($m[1]) + 1;
            }
            $noVisit = $visitPrefix . str_pad($nextVisitSeq, 4, '0', STR_PAD_LEFT);
            while ($db->table('patient_visits')->where('no_visit', $noVisit)->countAllResults() > 0) {
                $nextVisitSeq++;
                $noVisit = $visitPrefix . str_pad($nextVisitSeq, 4, '0', STR_PAD_LEFT);
            }

            // 3. Insert Patient Visit
            $visitData = [
                'no_visit'       => $noVisit,
                'patient_id'     => $patientId,
                'polyclinic_id'  => $polyId,
                'doctor_id'      => $doctorId,
                'service_id'     => $serviceId,
                'visit_type'     => $visitType,
                'payment_method' => 'umum',
                'status'         => 'waiting',
                'visit_date'     => $visitDate
            ];
            $db->table('patient_visits')->insert($visitData);
            $visitId = $db->insertID();

            // 4. Generate Queue Number: 'A' for Poliklinik, 'T' for Tindakan
            $queueCode = ($visitType === 'tindakan') ? 'T' : 'A';
            $lastQueue = $db->table('queue_numbers')
                            ->where("queue_no LIKE '{$queueCode}-%'")
                            ->where('DATE(created_at)', $visitDate)
                            ->orderBy('id', 'DESC')
                            ->limit(1)
                            ->get()
                            ->getRow();
            $nextQueueNum = 1;
            if ($lastQueue && preg_match('/[A-Z]-(\d+)/', $lastQueue->queue_no, $matches)) {
                $nextQueueNum = intval($matches[1]) + 1;
            }
            $queueNo = $queueCode . '-' . str_pad($nextQueueNum, 3, '0', STR_PAD_LEFT);

            $db->table('queue_numbers')->insert([
                'polyclinic_id' => $polyId,
                'queue_no'      => $queueNo,
                'visit_id'      => $visitId,
                'status'        => 'waiting'
            ]);

            // 5. Simpan keluhan awal pasien jika ada
            if (!empty($complaints)) {
                $db->table('triage_records')->insert([
                    'visit_id'    => $visitId,
                    'complaints'  => $complaints,
                    'anamnesis'   => 'Pendaftaran Online Mandiri',
                    'nurse_notes' => 'Pasien mendaftar via formulir online website'
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Gagal memproses pendaftaran online. Silakan coba kembali.');
                return redirect()->back()->withInput();
            }

            return redirect()->to(base_url('daftar-online/sukses/' . $visitId));
        }

        // GET Form Data
        $doctors = $db->table('doctors')
                      ->select('doctors.*, polyclinics.name as polyclinic_name')
                      ->join('polyclinics', 'polyclinics.id = doctors.polyclinic_id', 'left')
                      ->where('doctors.status', 'active')
                      ->orderBy('doctors.name', 'ASC')
                      ->get()
                      ->getResult();

        $polyclinics = $db->table('polyclinics')
                          ->where('status', 'active')
                          ->orderBy('name', 'ASC')
                          ->get()
                          ->getResult();

        $services = $db->table('tindakan')
                       ->select('tindakan.*, categories.name as category_name')
                       ->join('categories', 'categories.id = tindakan.category_id', 'left')
                       ->where('tindakan.status', 'active')
                       ->orderBy('categories.name', 'ASC')
                       ->orderBy('tindakan.name', 'ASC')
                       ->get()
                       ->getResult();

        $data = [
            'title'         => 'Pendaftaran Pasien Online (Pasien Baru & Lama) - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
            'doctors'       => $doctors,
            'polyclinics'   => $polyclinics,
            'services'      => $services,
            'adminWa'       => clinic_setting('clinic_phone', '6281234567890'),
            'isOnlineOpen'  => is_online_registration_open(),
            'openTime'      => clinic_setting('online_registration_open_time', '06:00'),
            'closeTime'     => clinic_setting('online_registration_close_time', '21:00'),
            'closedMessage' => clinic_setting('online_registration_closed_message', 'Pendaftaran online saat ini sedang ditutup di luar jam operasional. Jam pendaftaran online dibuka setiap hari pukul 06.00 - 21.00 WITA. Untuk penanganan medis darurat 24 Jam atau pendaftaran via WhatsApp, silakan hubungi kontak resmi klinik kami.')
        ];

        return view('public/daftar_online', $data);
    }

    /**
     * AJAX Endpoint: Verifikasi & Cek Pasien Lama (Aman & Bermasking Privasi)
     */
    public function checkPatientPublic()
    {
        $db = \Config\Database::connect('default');
        $identifier = trim(strip_tags((string)$this->request->getPost('identifier')));
        $dob        = trim(strip_tags((string)$this->request->getPost('dob')));

        if (empty($identifier) || empty($dob)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nomor RM/NIK dan Tanggal Lahir wajib diisi untuk verifikasi.'
            ]);
        }

        $patient = $db->table('patients')
                      ->groupStart()
                          ->where('no_rm', $identifier)
                          ->orWhere('nik', $identifier)
                      ->groupEnd()
                      ->where('date_of_birth', $dob)
                      ->get()
                      ->getRow();

        if (!$patient) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data pasien tidak ditemukan atau Tanggal Lahir tidak cocok dengan rekam medis faskes.'
            ]);
        }

        // Masking Nama dan No Telepon demi perlindungan privasi medis
        $words = explode(' ', $patient->name);
        $maskedNameArr = [];
        foreach ($words as $w) {
            if (strlen($w) <= 2) {
                $maskedNameArr[] = $w;
            } else {
                $maskedNameArr[] = substr($w, 0, 1) . str_repeat('*', strlen($w) - 2) . substr($w, -1);
            }
        }
        $maskedName = implode(' ', $maskedNameArr);

        $phone = (string)$patient->phone;
        $maskedPhone = (strlen($phone) > 6) ? substr($phone, 0, 4) . '****' . substr($phone, -3) : $phone;

        return $this->response->setJSON([
            'status'       => 'success',
            'patient_id'   => $patient->id,
            'no_rm'        => $patient->no_rm,
            'masked_name'  => $maskedName,
            'name'         => $patient->name,
            'masked_phone' => $maskedPhone,
            'phone'        => $patient->phone,
            'address'      => $patient->address
        ]);
    }

    public function suksesDaftar($visitId)
    {
        $db = \Config\Database::connect('default');

        $visit = $db->table('patient_visits')
                    ->select('patient_visits.*, 
                              patients.id as patient_id,
                              patients.name as patient_name, 
                              patients.no_rm, 
                              patients.nik, 
                              patients.phone, 
                              patients.address,
                              patients.date_of_birth,
                              patients.gender,
                              patients.bpjs_number,
                              polyclinics.name as polyclinic_name,
                              COALESCE(doctors.name, "Dokter Pemeriksa") as doctor_name,
                              tindakan.name as service_name,
                              tindakan.price as service_price,
                              queue_numbers.queue_no')
                    ->join('patients', 'patients.id = patient_visits.patient_id')
                    ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                    ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                    ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                    ->join('queue_numbers', 'queue_numbers.visit_id = patient_visits.id', 'left')
                    ->where('patient_visits.id', $visitId)
                    ->get()
                    ->getRow();

        if (!$visit) {
            return redirect()->to(base_url('/'));
        }

        // WhatsApp Confirmation Message
        $clinicName = clinic_setting('clinic_name', 'SAWAMAWA MEDICAL CENTER');
        $waText = "Halo Bpk/Ibu *" . $visit->patient_name . "*,\n\n"
                . "Pendaftaran kunjungan berobat Anda di *" . $clinicName . "* telah BERHASIL diverifikasi!\n\n"
                . "📋 *DATA PENDAFTARAN & ANTREAN ANDA:*\n"
                . "• *No. Antrean* : *" . $visit->queue_no . "*\n"
                . "• *No. Rekam Medis (No RM)* : *" . $visit->no_rm . "*\n"
                . "• *No. Kunjungan* : " . $visit->no_visit . "\n"
                . "• *Nama Pasien* : " . $visit->patient_name . "\n"
                . "• *Tgl Kunjungan* : " . date('d F Y', strtotime($visit->visit_date)) . "\n"
                . "• *Layanan/Poli* : " . ($visit->visit_type === 'tindakan' ? $visit->service_name : $visit->polyclinic_name) . "\n"
                . "• *Dokter Penanggung Jawab* : " . $visit->doctor_name . "\n\n"
                . "📌 *PETUNJUK KEDATANGAN:*\n"
                . "1. Harap hadir 15 menit sebelum jam pelayanan dimulai.\n"
                . "2. Tunjukkan bukti pesan WhatsApp ini atau Kartu Pasien Digital ke Meja Resepsionis / Perawat.\n\n"
                . "Lokasi: " . clinic_setting('clinic_address', 'Sumbawa Besar, NTB') . "\n"
                . "Terima kasih telah mempercayakan kesehatan Anda kepada *" . $clinicName . "*.\n"
                . "Salam Sehat Selalu! 🙏🏥";

        // Phone sanitization for WhatsApp
        $rawPhone = preg_replace('/[^0-9]/', '', (string)$visit->phone);
        if (str_starts_with($rawPhone, '0')) {
            $rawPhone = '62' . substr($rawPhone, 1);
        } elseif (str_starts_with($rawPhone, '8')) {
            $rawPhone = '62' . $rawPhone;
        }

        $waUrl = "https://api.whatsapp.com/send?phone=" . $rawPhone . "&text=" . urlencode($waText);
        $adminWaUrl = "https://api.whatsapp.com/send?phone=" . clinic_setting('clinic_phone', '6281234567890') . "&text=" . urlencode("Halo Admin Sawamawa, saya ingin konfirmasi pendaftaran online atas nama {$visit->patient_name} (No. Antrean: {$visit->queue_no}, No. RM: {$visit->no_rm}).");

        $data = [
            'title'      => 'Bukti Pendaftaran Online - ' . $visit->patient_name,
            'visit'      => $visit,
            'waText'     => $waText,
            'waUrl'      => $waUrl,
            'adminWaUrl' => $adminWaUrl
        ];

        return view('public/sukses_daftar', $data);
    }

    /**
     * =========================================================================
     * HALAMAN PUBLIK ARTIKEL & EDUKASI KESEHATAN
     * =========================================================================
     */
    public function berita($slug = null)
    {
        $db = \Config\Database::connect('default');

        if ($slug !== null) {
            $article = $db->table('articles')
                          ->where('slug', $slug)
                          ->where('status', 'published')
                          ->get()
                          ->getRow();

            if (!$article) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
                }
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
            }

            // Increment views count
            $db->table('articles')->where('id', $article->id)->increment('views', 1);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'article' => [
                        'id'         => $article->id,
                        'title'      => $article->title,
                        'category'   => $article->category,
                        'author'     => $article->author,
                        'summary'    => $article->summary,
                        'content'    => $article->content,
                        'image_url'  => $article->image_url ? base_url($article->image_url) : null,
                        'views'      => (int)$article->views + 1,
                        'created_at' => date('d F Y', strtotime($article->created_at))
                    ]
                ]);
            }

            $recentArticles = $db->table('articles')
                                 ->where('status', 'published')
                                 ->where('id !=', $article->id)
                                 ->orderBy('id', 'DESC')
                                 ->limit(4)
                                 ->get()
                                 ->getResult();

            $data = [
                'title'          => esc($article->title) . ' - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
                'article'        => $article,
                'recentArticles' => $recentArticles
            ];

            return view('public/berita_detail', $data);
        }

        // List all articles
        $category = $this->request->getGet('kategori');
        $search   = trim((string)$this->request->getGet('q'));

        $builder = $db->table('articles')->where('status', 'published');
        if (!empty($category)) {
            $builder->where('category', $category);
        }
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('title', $search)
                    ->orLike('summary', $search)
                    ->orLike('content', $search)
                    ->groupEnd();
        }

        $articles = $builder->orderBy('id', 'DESC')->get()->getResult();

        $data = [
            'title'       => 'Pusat Edukasi Medis & Berita Kesehatan - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
            'articles'    => $articles,
            'currentCat'  => $category,
            'searchQuery' => $search
        ];

        return view('public/berita_index', $data);
    }
}
