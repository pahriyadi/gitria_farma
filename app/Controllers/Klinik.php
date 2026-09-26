<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ClinicService;

class Klinik extends BaseController
{
    protected $clinicService;

    public function __construct()
    {
        $this->clinicService = new ClinicService();
    }

    public function pendaftaran()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            if ($action === 'create_patient' || $action === 'register_patient') {
                $patientData = [
                    'nik'           => $this->request->getPost('nik'),
                    'name'          => $this->request->getPost('name'),
                    'gender'        => $this->request->getPost('gender'),
                    'place_of_birth'=> $this->request->getPost('place_of_birth'),
                    'date_of_birth' => $this->request->getPost('date_of_birth'),
                    'phone'         => $this->request->getPost('phone'),
                    'address'       => $this->request->getPost('address'),
                    'occupation'    => $this->request->getPost('occupation'),
                    'religion'      => $this->request->getPost('religion'),
                    'education'     => $this->request->getPost('education'),
                    'blood_type'    => $this->request->getPost('blood_type'),
                    'marital_status'=> $this->request->getPost('marital_status'),
                    'allergies'     => $this->request->getPost('allergies'),
                    'medical_history' => $this->request->getPost('medical_history'),
                    'emergency_contact_name'  => $this->request->getPost('emergency_contact_name'),
                    'emergency_contact_phone' => $this->request->getPost('emergency_contact_phone'),
                    'bpjs_number'     => $this->request->getPost('bpjs_number'),
                    'membership_tier' => $this->request->getPost('membership_tier') ?: 'regular'
                ];

                $res = $this->clinicService->registerPatient($patientData);
                if ($res['status'] === 'success') {
                    session()->setFlashdata('success', $res['message'] . ' No RM: ' . $res['no_rm']);
                    session()->setFlashdata('last_registered_patient_id', $res['patient_id']);
                    session()->setFlashdata('last_registered_patient_name', $patientData['name']);
                    session()->setFlashdata('last_registered_patient_rm', $res['no_rm']);
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                return redirect()->to(base_url('klinik/pendaftaran'));
            }

            if ($action === 'edit_patient') {
                $patientId = $this->request->getPost('patient_id');
                $patientData = [
                    'nik'                     => $this->request->getPost('nik'),
                    'name'                    => $this->request->getPost('name'),
                    'gender'                  => $this->request->getPost('gender'),
                    'place_of_birth'          => $this->request->getPost('place_of_birth'),
                    'date_of_birth'           => $this->request->getPost('date_of_birth'),
                    'phone'                   => $this->request->getPost('phone'),
                    'address'                 => $this->request->getPost('address'),
                    'occupation'              => $this->request->getPost('occupation'),
                    'religion'                => $this->request->getPost('religion'),
                    'education'               => $this->request->getPost('education'),
                    'blood_type'              => $this->request->getPost('blood_type'),
                    'marital_status'          => $this->request->getPost('marital_status'),
                    'allergies'               => $this->request->getPost('allergies'),
                    'medical_history'         => $this->request->getPost('medical_history'),
                    'emergency_contact_name'  => $this->request->getPost('emergency_contact_name'),
                    'emergency_contact_phone' => $this->request->getPost('emergency_contact_phone'),
                    'bpjs_number'             => $this->request->getPost('bpjs_number'),
                    'membership_tier'         => $this->request->getPost('membership_tier') ?: 'regular'
                ];

                $db->table('patients')->where('id', $patientId)->update($patientData);
                session()->setFlashdata('success', 'Data Pasien berhasil diperbarui.');
                return redirect()->to(base_url('klinik/pendaftaran'));
            }

            if ($action === 'delete_patient') {
                $patientId = $this->request->getPost('patient_id');
                $db->query("SET FOREIGN_KEY_CHECKS = 0;");
                $db->table('patients')->where('id', $patientId)->delete();
                $db->query("SET FOREIGN_KEY_CHECKS = 1;");

                session()->setFlashdata('success', 'Data Pasien berhasil dihapus.');
                return redirect()->to(base_url('klinik/pendaftaran'));
            }

            if ($action === 'create_visit') {
                $patientId = $this->request->getPost('patient_id');
                $visitType = $this->request->getPost('visit_type') ?: 'poli';
                $polyclinicId = $this->request->getPost('polyclinic_id');
                $doctorId = $this->request->getPost('doctor_id');
                $serviceId = $this->request->getPost('service_id');
                $paymentMethod = $this->request->getPost('payment_method') ?: 'umum';
                $roomId = $this->request->getPost('room_id') ?: null;
                $insuranceId = $this->request->getPost('insurance_id') ?: null;
                $complaint = $this->request->getPost('complaint');
                $bpjsNumber = trim((string)$this->request->getPost('bpjs_number'));

                if (!$patientId) {
                    session()->setFlashdata('error', 'Data Pasien belum dipilih. Silakan pilih atau cari pasien terlebih dahulu.');
                    return redirect()->to(base_url('klinik/pendaftaran'));
                }

                $res = $this->clinicService->createVisit($patientId, $polyclinicId, $doctorId, $paymentMethod, $visitType, $serviceId, $roomId, $insuranceId, $complaint);
                if ($res['status'] === 'success') {
                    // Update nomor BPJS pasien jika diisi
                    if ($paymentMethod === 'bpjs' && !empty($bpjsNumber)) {
                        $db->table('patients')->where('id', $patientId)->update(['bpjs_number' => $bpjsNumber]);
                    }

                    session()->setFlashdata('success', $res['message'] . ' No Antrean: ' . $res['queue_no'] . ' (' . $res['no_visit'] . ')');
                    session()->setFlashdata('visit_ticket', $res);
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                return redirect()->to(base_url('klinik/pendaftaran'));
            }
        }

        // AJAX Handler untuk DataTables Server-Side Processing
        if ($this->request->getGet('draw')) {
            return datatable_server_side('patients', [
                0 => 'no_rm',
                1 => 'nik',
                2 => 'name',
                3 => 'gender',
                4 => 'phone',
                5 => 'address',
                6 => 'id'
            ], [
                'search_columns' => ['no_rm', 'nik', 'name', 'phone', 'address'],
                'default_order'  => ['id', 'DESC'],
                'row_formatter'  => function($p, $no) {
                    $genderStr = $p->gender === 'L' 
                        ? '<span class="badge badge-primary px-2 py-0.5"><i class="fas fa-mars mr-1"></i>L</span>' 
                        : '<span class="badge badge-danger px-2 py-0.5"><i class="fas fa-venus mr-1"></i>P</span>';
                    
                    $tier = strtolower($p->membership_tier ?? 'regular');
                    $tierBadge = '<span class="badge badge-light border text-muted ml-1" style="font-size:10px;">Reguler</span>';
                    if ($tier === 'gold') {
                        $tierBadge = '<span class="badge badge-warning ml-1" style="font-size:10px;"><i class="fas fa-crown mr-1"></i>Gold</span>';
                    } elseif ($tier === 'platinum' || $tier === 'vip') {
                        $tierBadge = '<span class="badge badge-dark ml-1" style="font-size:10px;"><i class="fas fa-gem mr-1"></i>VIP</span>';
                    }

                    $rmBadge = '<a href="javascript:void(0)" class="badge-rm-code btn-show-rme text-decoration-none" data-id="' . $p->id . '" title="Klik untuk membuka Riwayat Rekam Medis (RME)"><i class="fas fa-notes-medical mr-1"></i>' . esc($p->no_rm) . '</a>';

                    $phoneFormatted = '<span class="text-muted text-xs">-</span>';
                    if (!empty($p->phone)) {
                        $cleanPhone = preg_replace('/[^0-9]/', '', $p->phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                        $phoneFormatted = '<a href="https://wa.me/' . $cleanPhone . '" target="_blank" class="text-success font-weight-bold text-nowrap" style="font-size:12px;" title="Chat WhatsApp Pasien"><i class="fab fa-whatsapp mr-1"></i>' . esc($p->phone) . '</a>';
                    }

                    $actions = '
                        <div class="d-inline-flex align-items-center" style="gap: 3px;">
                            <button type="button" class="btn btn-outline-primary btn-xs font-weight-bold btn-show-patient-detail" data-id="' . $p->id . '" title="Lihat Detail Identitas & Profil Lengkap Pasien">
                                <i class="fas fa-id-card-clip mr-1"></i> Detail
                            </button>
                            <button class="btn btn-teal btn-xs font-weight-bold btn-visit" 
                                    data-id="' . $p->id . '" 
                                    data-name="' . esc($p->name) . '" 
                                    data-rm="' . esc($p->no_rm) . '" 
                                    data-nik="' . esc($p->nik ?? '') . '" 
                                    data-phone="' . esc($p->phone ?? '') . '" 
                                    data-tier="' . esc($p->membership_tier ?? 'regular') . '" 
                                    data-bpjs="' . esc($p->bpjs_number ?? '') . '"
                                    data-insurance="' . esc($p->insurance_provider_id ?? '') . '"
                                    data-toggle="modal" 
                                    data-target="#visitModal" 
                                    title="Daftarkan Kunjungan / Antrean">
                                <i class="fas fa-calendar-plus mr-1"></i> Kunjungan
                            </button>
                            <button type="button" class="btn btn-info btn-xs font-weight-bold btn-show-rme" data-id="' . $p->id . '" data-name="' . esc($p->name) . '" data-rm="' . esc($p->no_rm) . '" title="Lihat Riwayat Rekam Medis Lengkap (RME / SOAP)">
                                <i class="fas fa-file-medical mr-1"></i> Rekam Medis
                            </button>
                            <a href="' . base_url('klinik/kartu-pasien/' . $p->id) . '" class="btn btn-outline-secondary btn-xs font-weight-bold" title="Kartu Identitas Pasien (.JPG / WhatsApp)">
                                <i class="fas fa-id-card mr-1"></i> Kartu
                            </a>
                            <button class="btn btn-outline-secondary btn-xs btn-edit-patient" 
                                    data-id="' . $p->id . '" 
                                    data-nik="' . esc($p->nik ?? '') . '" 
                                    data-name="' . esc($p->name ?? '') . '" 
                                    data-gender="' . esc($p->gender ?? 'L') . '" 
                                    data-pob="' . esc($p->place_of_birth ?? '') . '" 
                                    data-dob="' . esc($p->date_of_birth ?? '') . '" 
                                    data-phone="' . esc($p->phone ?? '') . '" 
                                    data-address="' . esc($p->address ?? '') . '" 
                                    data-occupation="' . esc($p->occupation ?? '') . '" 
                                    data-religion="' . esc($p->religion ?? 'Islam') . '" 
                                    data-education="' . esc($p->education ?? '') . '" 
                                    data-blood-type="' . esc($p->blood_type ?? '-') . '" 
                                    data-marital-status="' . esc($p->marital_status ?? 'Menikah') . '" 
                                    data-allergies="' . esc($p->allergies ?? '') . '" 
                                    data-medical-history="' . esc($p->medical_history ?? '') . '" 
                                    data-emg-name="' . esc($p->emergency_contact_name ?? '') . '" 
                                    data-emg-phone="' . esc($p->emergency_contact_phone ?? '') . '" 
                                    data-bpjs="' . esc($p->bpjs_number ?? '') . '" 
                                    data-tier="' . esc($p->membership_tier ?? 'regular') . '" 
                                    data-toggle="modal" data-target="#editPatientModal"
                                    title="Edit Data Pasien">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <form action="' . base_url('klinik/pendaftaran') . '" method="post" class="d-inline-block mb-0" onsubmit="return confirm(\'Hapus data pasien ' . esc($p->name) . ' (No RM: ' . esc($p->no_rm) . ')?\');">
                                <input type="hidden" name="action" value="delete_patient">
                                <input type="hidden" name="patient_id" value="' . $p->id . '">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus Pasien"><i class="fas fa-trash-can"></i></button>
                            </form>
                        </div>
                    ';

                    return [
                        0 => $rmBadge,
                        1 => !empty($p->nik) ? '<span class="text-dark font-weight-bold text-xs font-monospace">' . esc($p->nik) . '</span>' : '<span class="text-muted text-xs">-</span>',
                        2 => '<div class="font-weight-bold text-dark" style="font-size:13px;"><a href="javascript:void(0)" class="btn-show-patient-detail text-dark text-decoration-none" data-id="' . $p->id . '" title="Klik untuk melihat Detail Identitas Lengkap Pasien">' . esc($p->name) . '</a> ' . $tierBadge . '</div>',
                        3 => $genderStr,
                        4 => $phoneFormatted,
                        5 => '<span class="text-secondary text-xs text-truncate d-inline-block" style="max-width: 170px;" title="' . esc($p->address) . '">' . esc($p->address ?: '-') . '</span>',
                        6 => $actions
                    ];
                }
            ]);
        }

        $polyclinics = $db->table('polikliniks')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();
        $doctors = $db->table('doctors')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();
        
        $tindakanServices = $db->table('tindakan')
                               ->select('tindakan.id, tindakan.name, tindakan.parent_id, tindakan.price, parent_tind.name as parent_name')
                               ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                               ->where('tindakan.status', 'active')
                               ->where('tindakan.price >', 0)
                               ->orderBy('tindakan.parent_id', 'ASC')
                               ->orderBy('tindakan.id', 'ASC')
                               ->get()
                               ->getResult();

        $paymentMethods = $db->table('payment_methods')
                             ->where('is_active', 1)
                             ->orderBy('category', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();

        $today = date('Y-m-d');
        $totalPatients = $db->table('patients')->countAllResults();
        $totalTodayVisits = $db->table('patient_visits')->where('visit_date', $today)->countAllResults();
        $totalCompletedVisits = $db->table('patient_visits')->where('visit_date', $today)->where('status', 'completed')->countAllResults();

        $todayQueues = $db->table('patient_visits pv')
                          ->select('pv.id as visit_id,
                                    COALESCE(qn.id, pv.id) as id,
                                    COALESCE(qn.queue_no, pv.no_visit) as queue_no,
                                    COALESCE(qn.status, pv.status) as status,
                                    pv.polyclinic_id,
                                    pv.created_at,
                                    COALESCE(polikliniks.name, poly.name) as polyclinic_name, 
                                    tindakan.name as tindakan_name,
                                    parent_tind.name as parent_tindakan_name,
                                    pv.no_visit, 
                                    pv.visit_type, 
                                    p.name as patient_name,
                                    p.no_rm,
                                    COALESCE(p.membership_tier, \'regular\') as membership_tier,
                                    d.name as doctor_name')
                          ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                          ->join('polyclinics poly', 'poly.id = pv.polyclinic_id', 'left')
                          ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                          ->join('tindakan', 'tindakan.id = pv.service_id', 'left')
                          ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                          ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                          ->join('patients p', 'p.id = pv.patient_id', 'left')
                          ->where('pv.visit_date', $today)
                          ->whereNotIn('pv.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed'])
                          ->whereNotIn('COALESCE(qn.status, pv.status)', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed'])
                          ->orderBy('pv.created_at', 'ASC')
                          ->orderBy('pv.id', 'ASC')
                          ->get()
                          ->getResult();

        // Hitung urutan pendaftar hari ini (Arrival Sequence #1, #2, #3...)
        $allVisitsOrder = $db->table('patient_visits')
                             ->select('id')
                             ->where('visit_date', $today)
                             ->orderBy('created_at', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();
        $arrivalOrderMap = [];
        $seq = 1;
        foreach ($allVisitsOrder as $avo) {
            $arrivalOrderMap[$avo->id] = $seq++;
        }

        foreach ($todayQueues as &$tq) {
            $tq->arrival_seq = $arrivalOrderMap[$tq->visit_id] ?? 1;
        }

        $rooms = $db->table('rooms')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();
        $insuranceProviders = $db->table('insurance_providers')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $doctorSchedules = $db->table('doctor_schedules ds')
                              ->select('ds.*, doctors.name as doctor_name, rooms.name as room_name')
                              ->join('doctors', 'doctors.id = ds.doctor_id')
                              ->join('rooms', 'rooms.id = ds.room_id', 'left')
                              ->where('ds.status', 'active')
                              ->get()->getResult();

        $data = [
            'title'                => 'Pendaftaran Pasien & Kunjungan Rawat Jalan',
            'active_menu'          => 'pendaftaran',
            'polyclinics'          => $polyclinics,
            'doctors'              => $doctors,
            'rooms'                => $rooms,
            'insuranceProviders'   => $insuranceProviders,
            'doctorSchedules'      => $doctorSchedules,
            'tindakanServices'     => $tindakanServices,
            'paymentMethods'       => $paymentMethods,
            'todayQueues'          => $todayQueues,
            'totalPatients'        => $totalPatients,
            'totalTodayVisits'     => $totalTodayVisits,
            'totalCompletedVisits' => $totalCompletedVisits
        ];

        return view('klinik/pendaftaran', $data);
    }

    /**
     * AJAX Live Search Pasien untuk Form Cepat / Registrasi Kunjungan
     */
    public function searchPatientsAjax()
    {
        $db = \Config\Database::connect('default');
        $q = trim((string)$this->request->getGet('q'));

        if (strlen($q) < 1) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => []
            ]);
        }

        $patients = $db->table('patients')
                       ->select('id, no_rm, nik, name, gender, phone, address, bpjs_number, membership_tier, insurance_provider_id')
                       ->groupStart()
                           ->like('name', $q)
                           ->orLike('no_rm', $q)
                           ->orLike('nik', $q)
                           ->orLike('phone', $q)
                       ->groupEnd()
                       ->orderBy('id', 'DESC')
                       ->limit(20)
                       ->get()
                       ->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $patients
        ]);
    }

    /**
     * Manajemen Antrean Pasien Rawat Jalan & Tindakan Medis
     */
    public function antrean()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            if ($action === 'delete_queue') {
                $queueId = (int)$this->request->getPost('queue_id');
                return $this->processDeleteQueue($queueId);
            }

            $queueId = (int)$this->request->getPost('queue_id');
            $newStatus = $this->request->getPost('status') ?: 'called';
            $now = date('Y-m-d H:i:s');

            $queue = $db->table('queue_numbers')->where('id', $queueId)->orWhere('visit_id', $queueId)->get()->getRow();
            if ($queue) {
                $db->table('queue_numbers')->where('id', $queue->id)->update([
                    'status'     => $newStatus,
                    'updated_at' => $now
                ]);
                if ($queue->visit_id) {
                    $visitStatus = ($newStatus === 'completed') ? 'completed' : (($newStatus === 'called') ? 'called' : $newStatus);
                    $db->table('patient_visits')->where('id', $queue->visit_id)->update([
                        'status'     => $visitStatus,
                        'updated_at' => $now
                    ]);
                }
            } else {
                $visit = $db->table('patient_visits')->where('id', $queueId)->get()->getRow();
                if ($visit) {
                    $visitStatus = ($newStatus === 'completed') ? 'completed' : (($newStatus === 'called') ? 'called' : $newStatus);
                    $db->table('patient_visits')->where('id', $visit->id)->update([
                        'status'     => $visitStatus,
                        'updated_at' => $now
                    ]);
                    
                    // Insert missing queue number row if needed
                    $existingQn = $db->table('queue_numbers')->where('visit_id', $visit->id)->get()->getRow();
                    if ($existingQn) {
                        $db->table('queue_numbers')->where('id', $existingQn->id)->update([
                            'status'     => $newStatus,
                            'updated_at' => $now
                        ]);
                    } else {
                        $db->table('queue_numbers')->insert([
                            'queue_no'      => $visit->no_visit,
                            'patient_id'    => $visit->patient_id,
                            'visit_id'      => $visit->id,
                            'polyclinic_id' => $visit->polyclinic_id,
                            'status'        => $newStatus,
                            'created_at'    => $now,
                            'updated_at'    => $now
                        ]);
                    }
                }
            }

            if ($this->request->isAJAX() || $this->request->getPost('is_ajax') === '1') {
                return $this->response->setJSON([
                    'status'     => 'success',
                    'message'    => 'Status antrean berhasil diperbarui.',
                    'new_status' => $newStatus,
                    'call_time'  => $now
                ]);
            }

            if ($this->request->getPost('action') === 'reassign_doctor') {
                $visitId = (int)$this->request->getPost('visit_id');
                $newDocId = (int)$this->request->getPost('doctor_id');
                if ($visitId > 0 && $newDocId > 0) {
                    $db->table('patient_visits')->where('id', $visitId)->update([
                        'doctor_id'  => $newDocId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    session()->setFlashdata('success', 'Dokter pemeriksa pasien berhasil dialihkan.');
                }
                return redirect()->to(base_url('klinik/antrean'));
            }

            session()->setFlashdata('success', 'Status antrean berhasil diperbarui.');
            return redirect()->to(base_url('klinik/antrean'));
        }

        $today = date('Y-m-d');
        $tab   = $this->request->getGet('tab') ?: 'active';
        $filterDocId  = $this->request->getGet('doctor_id') ?? 'all';
        $filterPolyId = $this->request->getGet('poly_id') ?? 'all';

        // Hitung total status untuk tab indikator
        $statsBuilder = $db->table('patient_visits pv')
                           ->select('pv.status as pv_status, qn.status as qn_status')
                           ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                           ->where('pv.visit_date', $today);

        if (!empty($filterDocId) && $filterDocId !== 'all') {
            $statsBuilder->where('pv.doctor_id', $filterDocId);
        }
        if (!empty($filterPolyId) && $filterPolyId !== 'all') {
            $statsBuilder->where('pv.polyclinic_id', $filterPolyId);
        }

        $allVisitsToday = $statsBuilder->get()->getResult();

        $countActive    = 0;
        $countCompleted = 0;
        $countCancelled = 0;
        $countTotal     = count($allVisitsToday);

        foreach ($allVisitsToday as $row) {
            $st = strtolower($row->qn_status ?: ($row->pv_status ?: 'waiting'));
            if ($st === 'completed' || $st === 'cashier' || $st === 'prescription') {
                $countCompleted++;
            } elseif ($st === 'cancelled') {
                $countCancelled++;
            } else {
                $countActive++;
            }
        }

        $builder = $db->table('patient_visits pv')
                      ->select('pv.id as visit_id,
                                COALESCE(qn.id, pv.id) as id,
                                COALESCE(qn.queue_no, pv.no_visit) as queue_no,
                                COALESCE(qn.status, pv.status) as status,
                                pv.polyclinic_id,
                                pv.created_at,
                                COALESCE(polikliniks.name, poly.name) as polyclinic_name, 
                                tindakan.name as tindakan_name,
                                parent_tind.name as parent_tindakan_name,
                                d.name as doctor_name,
                                d.sip_number as doctor_sip,
                                d.digital_signature as doctor_signature,
                                d.stamp_image as doctor_stamp,
                                pv.no_visit, 
                                pv.visit_type, 
                                p.name as patient_name,
                                COALESCE(p.membership_tier, \'regular\') as membership_tier,
                                pv.doctor_id,
                                (CASE WHEN tr.id IS NOT NULL THEN 1 ELSE 0 END) as has_triage')
                      ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                      ->join('polyclinics poly', 'poly.id = pv.polyclinic_id', 'left')
                      ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                      ->join('tindakan', 'tindakan.id = pv.service_id', 'left')
                      ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                      ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                      ->join('patients p', 'p.id = pv.patient_id', 'left')
                      ->join('triage_records tr', 'tr.visit_id = pv.id', 'left')
                      ->where('pv.visit_date', $today);

        if ($tab === 'active') {
            $builder->whereNotIn('pv.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done'])
                    ->whereNotIn('COALESCE(qn.status, pv.status)', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done']);
        } elseif ($tab === 'completed') {
            $builder->groupStart()
                    ->whereIn('pv.status', ['completed', 'cashier', 'prescription', 'pharmacy', 'done'])
                    ->orWhere('qn.status', 'completed')
                    ->groupEnd();
        } elseif ($tab === 'cancelled') {
            $builder->groupStart()
                    ->where('pv.status', 'cancelled')
                    ->orWhere('qn.status', 'cancelled')
                    ->groupEnd();
        }

        if (!empty($filterPolyId) && $filterPolyId !== 'all') {
            $builder->where('pv.polyclinic_id', $filterPolyId);
        }
        if (!empty($filterDocId) && $filterDocId !== 'all') {
            $builder->where('pv.doctor_id', $filterDocId);
        }

        $queues = $builder->orderBy("CASE WHEN COALESCE(qn.status, pv.status) = 'called' THEN 1 WHEN COALESCE(qn.status, pv.status) = 'waiting' THEN 2 ELSE 3 END", '', false)
                          ->orderBy("pv.created_at ASC, pv.id ASC", '', false)
                          ->get()
                          ->getResult();

        // Hitung urutan pendaftar hari ini (Arrival Sequence #1, #2, #3...)
        $allVisitsOrder = $db->table('patient_visits')
                             ->select('id')
                             ->where('visit_date', $today)
                             ->orderBy('created_at', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();
        $arrivalOrderMap = [];
        $seq = 1;
        foreach ($allVisitsOrder as $avo) {
            $arrivalOrderMap[$avo->id] = $seq++;
        }

        $now = time();
        foreach ($queues as &$q) {
            $vId = $q->visit_id ?? $q->id;
            $q->arrival_seq = $arrivalOrderMap[$vId] ?? 1;
            $regTime = !empty($q->created_at) ? strtotime($q->created_at) : $now;
            $waitMins = max(0, (int) round(($now - $regTime) / 60));
            $q->wait_minutes = $waitMins;

            if ($waitMins < 15) {
                $q->sla_status = 'normal';
                $q->sla_badge  = 'success';
                $q->sla_label  = '< 15 mnt';
            } elseif ($waitMins <= 30) {
                $q->sla_status = 'warning';
                $q->sla_badge  = 'warning text-dark';
                $q->sla_label  = '15-30 mnt';
            } else {
                $q->sla_status = 'overdue';
                $q->sla_badge  = 'danger';
                $q->sla_label  = '> 30 mnt';
            }
        }

        $doctors = $db->table('doctors')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $polyclinics = $db->table('polikliniks')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();

        $data = [
            'title'              => 'Antrean Pelayanan Klinik & Monitor Time Keeper',
            'active_menu'        => 'antrean',
            'current_tab'        => $tab,
            'count_active'       => $countActive,
            'count_completed'    => $countCompleted,
            'count_cancelled'    => $countCancelled,
            'count_total'        => $countTotal,
            'selected_doctor_id' => $filterDocId,
            'selected_poly_id'   => $filterPolyId,
            'doctors'            => $doctors,
            'polyclinics'        => $polyclinics,
            'queues'             => $queues
        ];

        return view('klinik/antrean', $data);
    }

    public function soap()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');
            $visitId = $this->request->getPost('visit_id');

            if ($action === 'save_triage') {
                $triageData = [
                    'weight'         => $this->request->getPost('weight'),
                    'height'         => $this->request->getPost('height'),
                    'blood_pressure' => $this->request->getPost('blood_pressure'),
                    'temperature'    => $this->request->getPost('temperature'),
                    'pulse'          => $this->request->getPost('pulse'),
                    'respiration'    => $this->request->getPost('respiration'),
                    'complaints'     => $this->request->getPost('complaints'),
                    'anamnesis'      => $this->request->getPost('anamnesis'),
                    'nurse_notes'    => $this->request->getPost('nurse_notes')
                ];

                // Save Triage
                $existing = $db->table('triage_records')->where('visit_id', $visitId)->get()->getRow();
                if ($existing) {
                    $db->table('triage_records')->where('visit_id', $visitId)->update($triageData);
                } else {
                    $triageData['visit_id'] = $visitId;
                    $db->table('triage_records')->insert($triageData);
                }

                $db->table('patient_visits')->where('id', $visitId)->update(['status' => 'examining']);

                if ($this->request->isAJAX() || $this->request->getPost('is_ajax') === '1') {
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'message' => 'Tanda-tanda vital berhasil disimpan otomatis.',
                        'data'    => $triageData
                    ]);
                }

                session()->setFlashdata('success', 'Pemeriksaan awal perawat berhasil disimpan.');
                return redirect()->to(base_url('klinik/soap'));
            }

            if ($action === 'save_soap') {
                $icd10 = $this->request->getPost('icd10_code');
                $icd9  = $this->request->getPost('icd9_code');

                $soapData = [
                    'subjective'   => $this->request->getPost('subjective'),
                    'objective'    => $this->request->getPost('objective'),
                    'assessment'   => $this->request->getPost('assessment'),
                    'plan'         => $this->request->getPost('plan'),
                    'icd10_code'   => $icd10,
                    'icd9_code'    => $icd9,
                    'doctor_notes' => $this->request->getPost('doctor_notes')
                ];

                $res = $this->clinicService->saveSoap($visitId, $soapData);
                if ($res['status'] === 'success') {
                    // Save or update ICD-10 diagnosis
                    $icd10 = $this->request->getPost('icd10_code');
                    $db->table('icd10_diagnoses')->where('visit_id', $visitId)->delete();
                    if (!empty($icd10)) {
                        $db->table('icd10_diagnoses')->insert([
                            'visit_id'   => $visitId,
                            'icd10_code' => $icd10,
                            'description'=> $this->request->getPost('icd10_desc')
                        ]);
                    }

                    // Save or update procedures (ICD-9)
                    $icd9 = $this->request->getPost('icd9_code');
                    $db->table('icd9_procedures')->where('visit_id', $visitId)->delete();
                    if (!empty($icd9)) {
                        $db->table('icd9_procedures')->insert([
                            'visit_id'   => $visitId,
                            'icd9_code' => $icd9,
                            'description'=> $this->request->getPost('icd9_desc')
                        ]);
                    }

                    // Sinkronisasi TTV Otomatis (Jika Dokter Mengisi TTV Langsung)
                    $bp = $this->request->getPost('blood_pressure');
                    $temp = $this->request->getPost('temperature');
                    $pulse = $this->request->getPost('pulse');
                    $resp = $this->request->getPost('respiration');
                    $weight = $this->request->getPost('weight');
                    $height = $this->request->getPost('height');

                    if (!empty($bp) || !empty($temp) || !empty($pulse) || !empty($resp) || !empty($weight) || !empty($height)) {
                        $existingTriage = $db->table('triage_records')->where('visit_id', $visitId)->get()->getRow();
                        $triageUpdate = [];
                        if ($bp !== null && $bp !== '') $triageUpdate['blood_pressure'] = $bp;
                        if ($temp !== null && $temp !== '') $triageUpdate['temperature'] = $temp;
                        if ($pulse !== null && $pulse !== '') $triageUpdate['pulse'] = $pulse;
                        if ($resp !== null && $resp !== '') $triageUpdate['respiration'] = $resp;
                        if ($weight !== null && $weight !== '') $triageUpdate['weight'] = $weight;
                        if ($height !== null && $height !== '') $triageUpdate['height'] = $height;

                        if (!empty($triageUpdate)) {
                            if ($existingTriage) {
                                $db->table('triage_records')->where('visit_id', $visitId)->update($triageUpdate);
                            } else {
                                $triageUpdate['visit_id'] = $visitId;
                                $db->table('triage_records')->insert($triageUpdate);
                            }
                        }
                    }

                    // 1. Pastikan transaksi billing (billing_transactions) SELALU ada untuk visit ini
                    $bill = $db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
                    if (!$bill) {
                        $todayStr = date('Ymd');
                        $billPrefix = 'BIL-' . $todayStr . '-';
                        $lastBilling = $db->table('billing_transactions')
                                          ->where("billing_no LIKE '{$billPrefix}%'")
                                          ->orderBy('id', 'DESC')
                                          ->limit(1)
                                          ->get()
                                          ->getRow();
                        $nextBillNum = 1;
                        if ($lastBilling && preg_match('/BIL-\d+-(\d+)/', $lastBilling->billing_no, $bMatches)) {
                            $nextBillNum = intval($bMatches[1]) + 1;
                        }
                        $billingNo = $billPrefix . str_pad($nextBillNum, 4, '0', STR_PAD_LEFT);
                        while ($db->table('billing_transactions')->where('billing_no', $billingNo)->countAllResults() > 0) {
                            $nextBillNum++;
                            $billingNo = $billPrefix . str_pad($nextBillNum, 4, '0', STR_PAD_LEFT);
                        }

                        $db->table('billing_transactions')->insert([
                            'billing_no'       => $billingNo,
                            'visit_id'         => $visitId,
                            'total_services'   => 0.00,
                            'total_medicines'  => 0.00,
                            'total_restaurant' => 0.00,
                            'discount'         => 0.00,
                            'grand_total'      => 0.00,
                            'status'           => 'open'
                        ]);
                        $bill = $db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
                    }

                    // 2. Pastikan rincian jasa konsultasi dokter / poli masuk jika belum ada detail medis
                    $hasMedisDetail = $db->table('billing_details')
                                         ->where('billing_id', $bill->id)
                                         ->where('item_type', 'medis')
                                         ->countAllResults();
                    if ($hasMedisDetail == 0) {
                        $visitObj = $db->table('patient_visits')->where('id', $visitId)->get()->getRow();
                        $polyObj  = $visitObj ? $db->table('polyclinics')->where('id', $visitObj->polyclinic_id)->get()->getRow() : null;
                        $polyName = $polyObj ? trim($polyObj->name) : 'Poli';
                        if ($polyName === 'Tindakan Saja' || stripos($polyName, 'Tindakan') !== false) {
                            $serviceItemName = 'Konsultasi & Tindakan Medis Dokter';
                        } else {
                            $serviceItemName = 'Konsultasi & Pemeriksaan ' . $polyName;
                        }
                        
                        $consultPrice = 150000;
                        $consultSvc = $db->table('services')
                                         ->select('services.id, services.name, service_prices.price')
                                         ->join('service_prices', 'service_prices.service_id = services.id')
                                         ->where('services.category', 'klinik')
                                         ->where('services.name LIKE', '%Konsultasi%')
                                         ->get()
                                         ->getRow();
                        if ($consultSvc && $consultSvc->price > 0) {
                            $consultPrice = (float) $consultSvc->price;
                        }
                        
                        $db->table('billing_details')->insert([
                            'billing_id' => $bill->id,
                            'item_type'  => 'medis',
                            'item_name'  => $serviceItemName,
                            'qty'        => 1,
                            'price'      => $consultPrice,
                            'discount'   => 0.00,
                            'subtotal'   => $consultPrice
                        ]);
                    }

                    // 3. Save custom services/procedures from tindakan table jika dipilih
                    $serviceId = $this->request->getPost('service_id');
                    if (!empty($serviceId)) {
                        $service = $db->table('tindakan')->where('id', $serviceId)->get()->getRow();
                        if ($service && $service->price > 0) {
                            $existsItem = $db->table('billing_details')
                                             ->where('billing_id', $bill->id)
                                             ->where('item_name', $service->name)
                                             ->get()->getRow();
                            if (!$existsItem) {
                                $db->table('billing_details')->insert([
                                    'billing_id' => $bill->id,
                                    'item_type'  => 'medis',
                                    'item_name'  => $service->name,
                                    'qty'        => 1,
                                    'price'      => $service->price,
                                    'discount'   => 0.00,
                                    'subtotal'   => $service->price
                                ]);
                            }
                        }
                    }

                    // 4. Save e-Resep (Prescriptions) & update billing details
                    $medicines = $this->request->getPost('medicines'); // Array of [medicine_id, qty, dosage]
                    $hasMedicines = false;

                    if (!empty($medicines) && is_array($medicines)) {
                        // Check if prescription already exists for this visit_id
                        $existingPresc = $db->table('prescriptions')->where('visit_id', $visitId)->get()->getRow();
                        if ($existingPresc) {
                            $prescId = $existingPresc->id;
                            $db->table('prescriptions')->where('id', $prescId)->update([
                                'doctor_id' => session('user_id') ?: 1,
                                'status'    => 'waiting'
                            ]);
                            $db->table('prescription_details')->where('prescription_id', $prescId)->delete();
                        } else {
                            $db->table('prescriptions')->insert([
                                'visit_id'  => $visitId,
                                'doctor_id' => session('user_id') ?: 1,
                                'status'    => 'waiting'
                            ]);
                            $prescId = $db->insertID();
                        }

                        $totalMedicinesBill = 0;
                        // Clear existing medicines in billing if any and re-populate
                        $db->table('billing_details')->where('billing_id', $bill->id)->where('item_type', 'obat')->delete();

                        foreach ($medicines as $med) {
                            $medId = $med['medicine_id'] ?? $med['id'] ?? null;
                            if (empty($medId)) continue;
                            
                            $qty = intval($med['qty'] ?? 1);
                            if ($qty <= 0) $qty = 1;
                            $dosage = $med['dosage'] ?? '-';

                            $medicine = $db->table('medicines')->where('id', $medId)->get()->getRow();
                            if ($medicine) {
                                $subMed = $medicine->price * $qty;
                                $isOutside = !empty($med['is_outside']) || ($med['status'] ?? '') === 'bought_outside';
                                $prescStatus = $isOutside ? 'bought_outside' : 'served';

                                $db->table('prescription_details')->insert([
                                    'prescription_id' => $prescId,
                                    'medicine_id'     => $medId,
                                    'qty'             => $qty,
                                    'dosage'          => $dosage,
                                    'price'           => $medicine->price,
                                    'discount'        => 0.00,
                                    'status'          => $prescStatus
                                ]);

                                // Add to billing details if served internally
                                if (!$isOutside) {
                                    $db->table('billing_details')->insert([
                                        'billing_id' => $bill->id,
                                        'item_type'  => 'obat',
                                        'item_name'  => $medicine->name,
                                        'qty'        => $qty,
                                        'price'      => $medicine->price,
                                        'discount'   => 0.00,
                                        'subtotal'   => $subMed
                                    ]);
                                    $totalMedicinesBill += $subMed;
                                }
                                $hasMedicines = true;
                            }
                        }
                    }

                    // 5. Always recalculate Billing Totals accurately from all billing_details
                    $sumDetails = $db->table('billing_details')
                                     ->select('
                                        COALESCE(SUM(CASE WHEN item_type="medis" THEN (price * qty) ELSE 0 END), 0) as gross_serv,
                                        COALESCE(SUM(CASE WHEN item_type="obat" THEN (price * qty) ELSE 0 END), 0) as gross_med,
                                        COALESCE(SUM(CASE WHEN item_type="resto" THEN (price * qty) ELSE 0 END), 0) as gross_resto,
                                        COALESCE(SUM(discount), 0) as total_disc,
                                        COALESCE(SUM(subtotal), 0) as net_grand
                                     ')
                                     ->where('billing_id', $bill->id)
                                     ->get()->getRow();

                    // Status tagihan diset 'open' agar pasien dapat langsung membayar di Kasir Utama
                    $db->table('billing_transactions')
                       ->where('id', $bill->id)
                       ->update([
                           'total_services'   => $sumDetails->gross_serv ?? 0,
                           'total_medicines'  => $sumDetails->gross_med ?? 0,
                           'total_restaurant' => $sumDetails->gross_resto ?? 0,
                           'discount'         => $sumDetails->total_disc ?? 0,
                           'grand_total'      => $sumDetails->net_grand ?? 0,
                           'status'           => 'open'
                        ]);

                    $visitRow = $db->table('patient_visits')->where('id', $visitId)->get()->getRow();
                    $patientRow = $visitRow ? $db->table('patients')->where('id', $visitRow->patient_id)->get()->getRow() : null;
                    $queueRow = $db->table('queue_numbers')->where('visit_id', $visitId)->get()->getRow();
                    $nowTimestamp = date('Y-m-d H:i:s');
                    $pName = $patientRow ? $patientRow->name : 'Pasien';
                    $qNumber = $queueRow ? ($queueRow->queue_no ?: $visitRow->no_visit) : ($visitRow ? $visitRow->no_visit : 'A-001');

                    // Mark queue as completed (selesai di dokter) & set timestamp pemanggilan ke kasir
                    $db->table('queue_numbers')->where('visit_id', $visitId)->update([
                        'status'     => 'completed',
                        'updated_at' => $nowTimestamp
                    ]);

                    // Pasien diarahkan ke Kasir terlebih dahulu untuk pelunasan tagihan
                    $db->table('patient_visits')->where('id', $visitId)->update([
                        'status'     => 'cashier',
                        'updated_at' => $nowTimestamp
                    ]);

                    // Notifikasi ke Kasir Utama (Tagihan Pasien Siap Bayar)
                    try {
                        $notif = new \App\Services\NotificationService();
                        $notif->send([
                            'notification_key' => 'patient_cashier_' . $visitId . '_' . time(),
                            'category'         => 'keuangan',
                            'type'             => 'info',
                            'badge'            => 'Kasir',
                            'icon'             => 'fas fa-cash-register',
                            'title'            => 'Antrean Kasir: ' . $pName,
                            'message'          => "Pemeriksaan pasien {$pName} ({$qNumber}) telah selesai. Tagihan siap diproses di Kasir Utama.",
                            'link'             => base_url('keuangan/kasir'),
                            'target_roles'     => 'kasir,accounting,staf keuangan,super admin,administrator',
                            'sender_name'      => session('username') ?: 'Poli Dokter'
                        ]);
                    } catch (\Throwable $e) {}

                    // Jika ada obat, kirim notifikasi ke Apotek (e-Resep Masuk - Menunggu Pembayaran Kasir)
                    if ($hasMedicines) {
                        try {
                            $notif = new \App\Services\NotificationService();
                            $notif->send([
                                'notification_key' => 'new_prescription_' . $visitId . '_' . time(),
                                'category'         => 'farmasi',
                                'type'             => 'info',
                                'badge'            => 'e-Resep',
                                'icon'             => 'fas fa-prescription',
                                'title'            => 'Resep Masuk: ' . $pName,
                                'message'          => "e-Resep baru untuk pasien {$pName} ({$qNumber}) telah masuk ke Apotek (menunggu pelunasan di Kasir).",
                                'link'             => base_url('apotek/resep'),
                                'target_roles'     => 'apoteker,farmasi,super admin,administrator',
                                'sender_name'      => session('username') ?: 'Poli Dokter'
                            ]);
                        } catch (\Throwable $e) {}
                    }

                    // Daftarkan event pemanggilan suara ke Kasir Utama di antrean server pusat
                    try {
                        $qCallService = new \App\Services\QueueCallService();
                        $qCallService->triggerCall([
                            'service_type'   => 'kasir',
                            'counter_name'   => 'Kasir Pembayaran',
                            'queue_number'   => $qNumber,
                            'patient_name'   => $pName,
                            'visit_id'       => $visitId,
                            'call_action'    => 'call',
                            'call_priority'  => 1,
                        ]);
                    } catch (\Throwable $e) {}

                    // Otomatis sinkronisasi SATUSEHAT Kemenkes RI (HL7 FHIR R4) jika diaktifkan
                    if (in_array(clinic_setting('satusehat_active', '1'), ['1', 'true', 'on', 1])) {
                        try {
                            $satuSehat = new \App\Services\SatuSehatService();
                            $satuSehat->syncAll($visitId);
                        } catch (\Throwable $ssEx) {
                            log_message('error', 'Auto SATUSEHAT Sync Error: ' . $ssEx->getMessage());
                        }
                    }

                    // Set Flashdata Suara Panggilan Otomatis ke Kasir Utama
                    session()->setFlashdata('voice_trigger', [
                        'action'       => 'to_cashier',
                        'queue_number' => $qNumber,
                        'patient_name' => $pName
                    ]);
                    session()->setFlashdata('success', 'Pemeriksaan SOAP selesai. Pasien ' . esc($pName) . ' (' . esc($qNumber) . ') berhasil diarahkan ke Kasir Utama untuk pembayaran.');
                } else {
                    session()->setFlashdata('error', $res['message']);
                }
                $targetUrl = (strpos($this->request->getUri()->getPath(), 'rekam-medis') !== false) ? base_url('klinik/rekam-medis') : base_url('klinik/soap');
                return redirect()->to($targetUrl);
            }
        }

        $today = date('Y-m-d');
        $filterDocId  = $this->request->getGet('doctor_id') ?? 'all';
        $filterPolyId = $this->request->getGet('poly_id') ?? 'all';

        // Fetch active visits waiting or in examination (belum selesai di dokter)
        $visitsBuilder = $db->table('patient_visits')
                            ->select('patient_visits.*, 
                                      patients.name as patient_name, 
                                      patients.no_rm, 
                                      patients.allergies as patient_allergies,
                                      COALESCE(patients.membership_tier, \'regular\') as membership_tier,
                                      COALESCE(queue_numbers.queue_no, patient_visits.no_visit) as queue_no,
                                      COALESCE(queue_numbers.status, patient_visits.status) as queue_status,
                                      COALESCE(polikliniks.name, polyclinics.name) as polyclinic_name,
                                      tindakan.name as tindakan_name,
                                      parent_tind.name as parent_tindakan_name,
                                      doctors.name as doctor_name')
                            ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                            ->join('queue_numbers', 'queue_numbers.visit_id = patient_visits.id', 'left')
                            ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                            ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                            ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                            ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                            ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                            ->where('patient_visits.visit_date', $today)
                            ->whereNotIn('patient_visits.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed'])
                            ->whereNotIn('COALESCE(queue_numbers.status, patient_visits.status)', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done', 'finished', 'closed']);

        if (!empty($filterPolyId) && $filterPolyId !== 'all') {
            $visitsBuilder->where('patient_visits.polyclinic_id', $filterPolyId);
        }
        if (!empty($filterDocId) && $filterDocId !== 'all') {
            $visitsBuilder->where('patient_visits.doctor_id', $filterDocId);
        }

        $visits = $visitsBuilder->orderBy("CASE WHEN COALESCE(queue_numbers.status, patient_visits.status) = 'called' THEN 1 WHEN COALESCE(queue_numbers.status, patient_visits.status) = 'waiting' THEN 2 ELSE 3 END", '', false)
                                ->orderBy('patient_visits.created_at', 'ASC')
                                ->orderBy('patient_visits.id', 'ASC')
                                ->get()
                                ->getResult();

        // Hitung nomor urut pendaftaran global hari ini (Arrival Order Sequence #1, #2, #3...)
        $allVisitsOrder = $db->table('patient_visits')
                             ->select('id')
                             ->where('visit_date', $today)
                             ->orderBy('created_at', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();
        $arrivalOrderMap = [];
        $seq = 1;
        foreach ($allVisitsOrder as $avo) {
            $arrivalOrderMap[$avo->id] = $seq++;
        }

        $now = time();
        foreach ($visits as &$v) {
            $v->arrival_seq = $arrivalOrderMap[$v->id] ?? 1;
            $regTime = !empty($v->created_at) ? strtotime($v->created_at) : $now;
            $v->wait_minutes = max(0, (int) round(($now - $regTime) / 60));
        }

        // Fetch all triage records mapped by visit_id
        $triageRows = $db->table('triage_records')
                         ->get()
                         ->getResult();
        $triages = [];
        foreach ($triageRows as $tr) {
            $triages[$tr->visit_id] = $tr;
        }

        // Fetch longitudinal medical records & prescription history for all active patients
        $patientIds = array_unique(array_map(function($v) { return $v->patient_id; }, $visits));
        $patientHistory = [];

        if (!empty($patientIds)) {
            $pastVisits = $db->table('patient_visits')
                             ->select('patient_visits.*, 
                                       medical_records.subjective, 
                                       medical_records.objective, 
                                       medical_records.assessment, 
                                       medical_records.plan, 
                                       medical_records.icd10_code, 
                                       medical_records.icd9_code,
                                       doctors.name as doctor_name,
                                       COALESCE(polikliniks.name, polyclinics.name) as polyclinic_name')
                             ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                             ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                             ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                             ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                             ->whereIn('patient_visits.patient_id', $patientIds)
                             ->whereIn('patient_visits.status', ['completed', 'cashier', 'pharmacy', 'examining'])
                             ->orderBy('patient_visits.visit_date', 'DESC')
                             ->get()
                             ->getResult();

            foreach ($pastVisits as $pv) {
                // Get past medicines
                $pastMeds = $db->table('prescription_details')
                               ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                               ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id', 'left')
                               ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                               ->where('prescriptions.visit_id', $pv->id)
                               ->get()
                               ->getResult();
                $pv->medicines = $pastMeds;
                $patientHistory[$pv->patient_id][] = $pv;
            }
        }

        // Medicines for prescription modal
        $medicinesList = $db->table('medicines')
                            ->select('medicines.*, COALESCE(SUM(medicine_batches.stock), 0) as total_stock')
                            ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                            ->where('medicines.status', 'active')
                            ->groupBy('medicines.id')
                            ->orderBy('medicines.name', 'ASC')
                            ->get()
                            ->getResult();

        // Medical Services list for add-service modal
        $servicesList = $db->table('tindakan')
                           ->select('tindakan.id, tindakan.name, tindakan.parent_id, tindakan.price, parent_tind.name as parent_name')
                           ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                           ->where('tindakan.status', 'active')
                           ->where('tindakan.price >', 0)
                           ->orderBy('tindakan.parent_id', 'ASC')
                           ->orderBy('tindakan.id', 'ASC')
                           ->get()
                           ->getResult();

        $masterIcd10 = $db->table('master_icd10')->where('status', 'active')->orderBy('code', 'ASC')->get()->getResult();
        $masterIcd9  = $db->table('master_icd9')->where('status', 'active')->orderBy('code', 'ASC')->get()->getResult();

        $doctorsList = $db->table('doctors')->where('status', 'active')->orderBy('name', 'ASC')->get()->getResult();
        $polyclinicsList = $db->table('polikliniks')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();

        $clinicalTemplates = [
            [
                'id'          => 'ispa',
                'name'        => 'ISPA / Batuk Pilek Akut',
                'badge'       => 'primary',
                'subjective'  => 'Pasien mengeluh batuk berdahak/kering, pilek, hidung tersumbat, dan demam ringan sejak 2-3 hari terakhir. Tenggorokan terasa gatal dan nyeri saat menelan.',
                'objective'   => 'Keadaan umum: Tampak sakit ringan. Faring hiperemis (+), tonsil T1/T1 tenang, rhonki (-/-), wheezing (-/-). Suhu subfebris, TTV lain dalam batas normal.',
                'assessment'  => 'Infeksi Saluran Pernapasan Akut (ISPA) / Acute Upper Respiratory Infection',
                'icd10_code'  => 'J06.9',
                'plan'        => 'Edukasi istirahat cukup, perbanyak minum air hangat, hindari makanan berminyak/dingin, dan konsumsi obat secara teratur.',
                'med_keyword' => 'Paracetamol'
            ],
            [
                'id'          => 'gastritis',
                'name'        => 'Gastritis / Dispepsia / Maag Akut',
                'badge'       => 'warning',
                'subjective'  => 'Pasien mengeluh nyeri ulu hati (epigastrium), rasa perih, mual, perut kembung (begah), dan bersendawa. Keluhan memburuk setelah terlambat makan atau konsumsi makanan pedas/asam/kopi.',
                'objective'   => 'Nyeri tekan epigastrium (+), bising usus normal (8-10x/menit), hepar/lien tidak teraba, meteorismus (+).',
                'assessment'  => 'Gastritis Akut / Dyspepsia Syndrome',
                'icd10_code'  => 'K29.7',
                'plan'        => 'Edukasi pola makan teratur (porsi kecil tapi sering), hindari makanan pedas, asam, kopi, dan santan. Kurangi stres.',
                'med_keyword' => 'Antasida'
            ],
            [
                'id'          => 'hipertensi',
                'name'        => 'Hipertensi Primer / Esensial',
                'badge'       => 'danger',
                'subjective'  => 'Pasien mengeluh pusing berputar/nyeri kepala bagian tengkuk (leher belakang tegang), mudah lelah, dan rasa berdebar.',
                'objective'   => 'Tekanan darah meningkat (>= 140/90 mmHg). Cor: S1-S2 murni reguler, murmur (-), gallop (-). Pulmo: Vesikuler (+/+). Ekstremitas: Edema (-/-).',
                'assessment'  => 'Essential (Primary) Hypertension',
                'icd10_code'  => 'I10',
                'plan'        => 'Diet rendah garam (DASH Diet), olahraga aerobik teratur 30 menit/hari, kurangi konsumsi kafein, kontrol tekanan darah berkala.',
                'med_keyword' => 'Amlodipine'
            ],
            [
                'id'          => 'dermatitis',
                'name'        => 'Dermatitis Alergi / Kontak',
                'badge'       => 'info',
                'subjective'  => 'Pasien mengeluh gatal-gatal pada kulit, timbul ruam kemerahan, bintil-bintil berair setelah terpapar alergen/sabun/debu/makanan tertentu.',
                'objective'   => 'Status dermatologikus: Tampak makula eritematosa batas tegas, papul, ekskoriasi (+) akibat garukan.',
                'assessment'  => 'Allergic Contact Dermatitis / Urticaria',
                'icd10_code'  => 'L23.9',
                'plan'        => 'Hindari pemicu alergen, jangan digaruk untuk mencegah infeksi sekunder, gunakan sabun lembut tanpa parfum.',
                'med_keyword' => 'Cetirizine'
            ],
            [
                'id'          => 'dm2',
                'name'        => 'Diabetes Melitus Tipe 2',
                'badge'       => 'success',
                'subjective'  => 'Pasien kontrol gula darah, riwayat 3P (polidipsi/sering haus, polifagi/sering lapar, poliuri/sering kencing malam hari), badan terasa lemas dan kesemutan.',
                'objective'   => 'Gula Darah Sewaktu (GDS) / GDP meningkat di atas normal. Luka/ulkus pedis (-). Sensibilitas perifer menurun minimal.',
                'assessment'  => 'Non-Insulin-Dependent Diabetes Mellitus (T2DM)',
                'icd10_code'  => 'E11.9',
                'plan'        => 'Terapi nutrisi medis (diet 3J: Jadwal, Jumlah, Jenis), olahraga rutin, perawatan kebersihan kaki, minum obat hipoglikemik rutin.',
                'med_keyword' => 'Metformin'
            ],
            [
                'id'          => 'faringitis',
                'name'        => 'Faringitis Akut / Radang Tenggorokan',
                'badge'       => 'secondary',
                'subjective'  => 'Nyeri tenggorokan hebat terutama saat menelan, suara serak, demam, dan malaise sejak 2 hari.',
                'objective'   => 'Faring hiperemis difus (+), eksudat (-), tonsil T2/T2 hiperemis ringan, pembesaran KGB servikal anterior minimal.',
                'assessment'  => 'Acute Pharyngitis',
                'icd10_code'  => 'J02.9',
                'plan'        => 'Kumurlah air garam hangat, minum air putih hangat minimal 2 liter/hari, istirahat bicara sementara waktu.',
                'med_keyword' => 'Amoxicillin'
            ],
            [
                'id'          => 'cephalgia',
                'name'        => 'Cephalgia / Tension Headache',
                'badge'       => 'dark',
                'subjective'  => 'Sakit kepala rasa terikat/tertekan di kedua sisi kepala, menjalar ke leher dan bahu. Riwayat kelelahan, kurang tidur, dan stres kerja.',
                'objective'   => 'Pemeriksaan neurologis dalam batas normal. Spasme otot perikranial dan trapezius (+). Refleks fisiologis (+/+), refleks patologis (-/-).',
                'assessment'  => 'Tension-Type Headache (TTH) / Cephalgia',
                'icd10_code'  => 'G44.2',
                'plan'        => 'Edukasi manajemen stres, perbaiki postur tubuh saat bekerja di depan layar, relaksasi otot leher, kompres hangat.',
                'med_keyword' => 'Paracetamol'
            ]
        ];

        $data = [
            'title'              => 'Rekam Medis & SOAP',
            'active_menu'        => 'soap',
            'visits'             => $visits,
            'triages'            => $triages,
            'patientHistory'     => $patientHistory,
            'medicines'          => $medicinesList,
            'services'           => $servicesList,
            'masterIcd10'        => $masterIcd10,
            'masterIcd9'         => $masterIcd9,
            'selected_doctor_id' => $filterDocId,
            'selected_poly_id'   => $filterPolyId,
            'doctors'            => $doctorsList,
            'polyclinics'        => $polyclinicsList,
            'clinicalTemplates'  => $clinicalTemplates
        ];

        return view('klinik/soap', $data);
    }

    /**
     * Tampilan & Cetak / Download Kartu Pasien (Format KTP / JPG / WhatsApp)
     */
    public function kartuPasien($patientId)
    {
        $db = \Config\Database::connect('default');
        $patient = $db->table('patients')->where('id', $patientId)->get()->getRow();
        if (!$patient) {
            session()->setFlashdata('error', 'Data pasien tidak ditemukan.');
            return redirect()->to(base_url('klinik/pendaftaran'));
        }

        $data = [
            'title'       => 'Kartu Pasien - ' . $patient->name . ' (' . $patient->no_rm . ')',
            'active_menu' => 'pendaftaran',
            'patient'     => $patient
        ];

        return view('klinik/kartu_pasien', $data);
    }

    /**
     * Mengambil Riwayat Rekam Medis (RME) Pasien Lengkap (Format JSON)
     */
    public function getPatientRmeHistoryJson($patientId)
    {
        $db = \Config\Database::connect('default');

        $patient = $db->table('patients')->where('id', $patientId)->get()->getRow();
        if (!$patient) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data pasien tidak ditemukan.']);
        }

        // Hitung Usia
        $ageStr = '-';
        if (!empty($patient->date_of_birth)) {
            $dob = new \DateTime($patient->date_of_birth);
            $now = new \DateTime();
            $diff = $now->diff($dob);
            $ageStr = $diff->y . ' Thn ' . $diff->m . ' Bln';
        }

        // Ambil Seluruh Riwayat Kunjungan Pasien
        $visits = $db->table('patient_visits')
                     ->select('patient_visits.*, 
                               COALESCE(polikliniks.name, polyclinics.name) as polyclinic_name,
                               tindakan.name as tindakan_name,
                               parent_tind.name as parent_tindakan_name,
                               doctors.name as doctor_name,
                               medical_records.subjective,
                               medical_records.objective,
                               medical_records.assessment,
                               medical_records.plan,
                               medical_records.icd10_code,
                               medical_records.icd9_code,
                               medical_records.doctor_notes,
                               triage_records.weight,
                               triage_records.height,
                               triage_records.blood_pressure,
                               triage_records.temperature,
                               triage_records.pulse,
                               triage_records.respiration,
                               triage_records.complaints,
                               triage_records.anamnesis,
                               triage_records.nurse_notes')
                     ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                     ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                     ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                     ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                     ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                     ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                     ->join('triage_records', 'triage_records.visit_id = patient_visits.id', 'left')
                     ->where('patient_visits.patient_id', $patientId)
                     ->orderBy('patient_visits.visit_date', 'DESC')
                     ->orderBy('patient_visits.created_at', 'DESC')
                     ->get()
                     ->getResult();

        // Ambil e-Resep, Billing, ICD-9 & Prosedur per Kunjungan
        $cumulativeMeds = [];
        $latestVitals = null;

        foreach ($visits as $index => $v) {
            // Hitung IMT / BMI
            $v->bmi = null;
            $v->bmi_status = '-';
            if (!empty($v->weight) && !empty($v->height) && $v->height > 0) {
                $heightM = $v->height / 100;
                $bmiVal = round($v->weight / ($heightM * $heightM), 1);
                $v->bmi = $bmiVal;
                if ($bmiVal < 18.5) $v->bmi_status = 'Underweight (Kurus)';
                elseif ($bmiVal <= 24.9) $v->bmi_status = 'Normal Ideal';
                elseif ($bmiVal <= 29.9) $v->bmi_status = 'Overweight (Kelebihan BB)';
                else $v->bmi_status = 'Obesitas';
            }

            if ($index === 0) {
                $latestVitals = [
                    'blood_pressure' => $v->blood_pressure,
                    'pulse'          => $v->pulse,
                    'temperature'    => $v->temperature,
                    'respiration'    => $v->respiration,
                    'weight'         => $v->weight,
                    'height'         => $v->height,
                    'bmi'            => $v->bmi,
                    'bmi_status'     => $v->bmi_status,
                    'date'           => $v->visit_date
                ];
            }

            // e-Resep
            $prescDetails = $db->table('prescription_details')
                               ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit, medicines.type as medicine_type')
                               ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id', 'left')
                               ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                               ->where('prescriptions.visit_id', $v->id)
                               ->get()
                               ->getResult();
            $v->medicines = $prescDetails;

            foreach ($prescDetails as $pd) {
                $mName = $pd->medicine_name ?: 'Obat';
                if (!isset($cumulativeMeds[$mName])) {
                    $cumulativeMeds[$mName] = [
                        'name'       => $mName,
                        'unit'       => $pd->unit ?: 'pcs',
                        'last_date'  => $v->visit_date,
                        'last_dosage'=> $pd->dosage,
                        'total_qty'  => 0,
                        'frequency'  => 0
                    ];
                }
                $cumulativeMeds[$mName]['total_qty'] += $pd->qty;
                $cumulativeMeds[$mName]['frequency']++;
            }

            // Diagnosa ICD-10 deskripsi
            if (!empty($v->icd10_code)) {
                $icd10 = $db->table('master_icd10')->where('code', $v->icd10_code)->get()->getRow();
                $v->icd10_desc = $icd10 ? ($icd10->name_id ?? $icd10->name_en ?? '') : '';
            } else {
                $v->icd10_desc = '';
            }

            // Prosedur ICD-9 deskripsi
            if (!empty($v->icd9_code)) {
                $icd9 = $db->table('master_icd9')->where('code', $v->icd9_code)->get()->getRow();
                $v->icd9_desc = $icd9 ? ($icd9->name_id ?? $icd9->name_en ?? '') : '';
            } else {
                $v->icd9_desc = '';
            }

            // Billing Kasir
            $bill = $db->table('billing_transactions')->where('visit_id', $v->id)->get()->getRow();
            if ($bill) {
                $bill->is_paid = ($bill->status === 'paid') ? 1 : 0;
            }
            $v->billing = $bill;
        }

        // Ambil Semua Surat Medis Pasien
        $letters = $db->table('medical_letters')
                      ->select('medical_letters.*, doctors.name as doctor_name, patient_visits.no_visit')
                      ->join('doctors', 'doctors.id = medical_letters.doctor_id', 'left')
                      ->join('patient_visits', 'patient_visits.id = medical_letters.visit_id', 'left')
                      ->where('medical_letters.patient_id', $patientId)
                      ->orderBy('medical_letters.letter_date', 'DESC')
                      ->get()
                      ->getResult();

        // Ambil Semua Riwayat Lab Pasien
        $labOrders = $db->table('lab_results')
                        ->select('lab_results.*, patient_visits.visit_date, patient_visits.no_visit')
                        ->join('patient_visits', 'patient_visits.id = lab_results.visit_id', 'left')
                        ->where('lab_results.patient_id', $patientId)
                        ->orderBy('lab_results.created_at', 'DESC')
                        ->get()
                        ->getResult();

        // Ambil Lembar 4 & 5: Informed Consent (Persetujuan Tindakan Medis)
        $informedConsents = $db->table('medical_informed_consents')
                               ->select('medical_informed_consents.*, doctors.name as doctor_name, patient_visits.no_visit, patient_visits.visit_date')
                               ->join('doctors', 'doctors.id = medical_informed_consents.doctor_id', 'left')
                               ->join('patient_visits', 'patient_visits.id = medical_informed_consents.visit_id', 'left')
                               ->where('medical_informed_consents.patient_id', $patientId)
                               ->orderBy('medical_informed_consents.consent_date', 'DESC')
                               ->get()
                               ->getResult();

        // Ambil Lembar 6 & 7: Dokumentasi Foto Klinis Pasien (Before/After iPad/Kamera)
        $medicalPhotos = $db->table('medical_photos')
                            ->select('medical_photos.*, patient_visits.no_visit, patient_visits.visit_date')
                            ->join('patient_visits', 'patient_visits.id = medical_photos.visit_id', 'left')
                            ->where('medical_photos.patient_id', $patientId)
                            ->orderBy('medical_photos.created_at', 'DESC')
                            ->get()
                            ->getResult();

        // Bagi visits menjadi Initial Assessment (Kunjungan Pertama / Lembar 2) dan CPPT Records (Lembar 3)
        $chronologicalVisits = array_reverse($visits); // dari yang tertua ke terbaru
        $initialAssessment = !empty($chronologicalVisits) ? $chronologicalVisits[0] : null;

        return $this->response->setJSON([
            'status'            => 'success',
            'patient'           => $patient,
            'age'               => $ageStr,
            'totalVisits'       => count($visits),
            'latestVitals'      => $latestVitals,
            'firstVisitDate'    => !empty($visits) ? end($visits)->visit_date : null,
            'lastVisitDate'     => !empty($visits) ? $visits[0]->visit_date : null,
            'initialAssessment' => $initialAssessment,
            'visits'            => $visits, // CPPT / Riwayat Semua Kunjungan
            'cumulativeMeds'    => array_values($cumulativeMeds),
            'informedConsents'  => $informedConsents,
            'medicalPhotos'     => $medicalPhotos,
            'letters'           => $letters,
            'labOrders'         => $labOrders
        ]);
    }

    /**
     * Halaman Detail Profil & Identitas Lengkap Pasien
     */
    public function detailPasien($patientId)
    {
        $db = \Config\Database::connect('default');
        $patient = $db->table('patients')->where('id', $patientId)->get()->getRow();

        if (!$patient) {
            session()->setFlashdata('error', 'Data Pasien tidak ditemukan.');
            return redirect()->to(base_url('klinik/pendaftaran'));
        }

        // Hitung Usia
        $ageStr = '-';
        if (!empty($patient->date_of_birth)) {
            $dob = new \DateTime($patient->date_of_birth);
            $today = new \DateTime('today');
            $diff = $dob->diff($today);
            $ageStr = $diff->y . ' th ' . $diff->m . ' bln';
        }

        // Kunjungan & Vitals
        $visits = $db->table('patient_visits')
                     ->select('patient_visits.*, doctors.name as doctor_name, polikliniks.name as polyclinic_name, tindakan.name as tindakan_name, medical_records.subjective, medical_records.objective, medical_records.assessment, medical_records.plan, medical_records.icd10_code, medical_records.icd9_code')
                     ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                     ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                     ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                     ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                     ->where('patient_visits.patient_id', $patientId)
                     ->orderBy('patient_visits.visit_date', 'DESC')
                     ->orderBy('patient_visits.id', 'DESC')
                     ->get()
                     ->getResult();

        foreach ($visits as $v) {
            if (!empty($v->icd10_code)) {
                $icd10 = $db->table('master_icd10')->where('code', $v->icd10_code)->get()->getRow();
                $v->icd10_desc = $icd10 ? ($icd10->name_id ?: $icd10->name_en) : '';
            } else {
                $v->icd10_desc = '';
            }
            if (!empty($v->icd9_code)) {
                $icd9 = $db->table('master_icd9')->where('code', $v->icd9_code)->get()->getRow();
                $v->icd9_desc = $icd9 ? ($icd9->name_id ?: $icd9->name_en) : '';
            } else {
                $v->icd9_desc = '';
            }
        }

        $informedConsents = $db->table('medical_informed_consents')
                               ->select('medical_informed_consents.*, doctors.name as doctor_name')
                               ->join('doctors', 'doctors.id = medical_informed_consents.doctor_id', 'left')
                               ->where('patient_id', $patientId)
                               ->orderBy('consent_date', 'DESC')
                               ->get()
                               ->getResult();

        $medicalPhotos = $db->table('medical_photos')
                            ->where('patient_id', $patientId)
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->getResult();

        return view('klinik/detail_pasien', [
            'title'            => 'Detail Identitas Pasien - ' . $patient->name . ' (' . $patient->no_rm . ')',
            'patient'          => $patient,
            'age'              => $ageStr,
            'visits'           => $visits,
            'totalVisits'      => count($visits),
            'informedConsents' => $informedConsents,
            'medicalPhotos'    => $medicalPhotos
        ]);
    }

    /**
     * Simpan Lembar Persetujuan Tindakan Medis (Informed Consent - Lembar 4 & 5)
     */
    public function saveInformedConsent()
    {
        $db = \Config\Database::connect('default');
        $patientId = $this->request->getPost('patient_id');
        $visitId   = $this->request->getPost('visit_id') ?: null;
        $doctorId  = $this->request->getPost('doctor_id') ?: null;

        if (!$patientId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Pasien tidak valid']);
        }

        $data = [
            'patient_id'                  => $patientId,
            'visit_id'                    => $visitId,
            'doctor_id'                   => $doctorId,
            'procedure_name'              => $this->request->getPost('procedure_name'),
            'diagnosis'                   => $this->request->getPost('diagnosis'),
            'indication'                  => $this->request->getPost('indication'),
            'procedure_desc'              => $this->request->getPost('procedure_desc'),
            'risks_complications'         => $this->request->getPost('risks_complications'),
            'prognosis'                   => $this->request->getPost('prognosis') ?: 'Baik (Dubia ad Bonam)',
            'consent_type'                => $this->request->getPost('consent_type') ?: 'agree',
            'authorized_person_name'      => $this->request->getPost('authorized_person_name'),
            'authorized_person_relation'  => $this->request->getPost('authorized_person_relation') ?: 'Diri Sendiri',
            'authorized_person_signature' => $this->request->getPost('authorized_person_signature'),
            'doctor_signature'            => $this->request->getPost('doctor_signature'),
            'witness_name'                => $this->request->getPost('witness_name'),
            'consent_date'                => $this->request->getPost('consent_date') ?: date('Y-m-d H:i:s'),
            'notes'                       => $this->request->getPost('notes')
        ];

        $db->table('medical_informed_consents')->insert($data);
        $insertId = $db->insertID();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Lembar Persetujuan Tindakan Medis (Informed Consent) berhasil disimpan.',
            'id'      => $insertId
        ]);
    }

    /**
     * Hapus Informed Consent
     */
    public function deleteInformedConsent($id)
    {
        $db = \Config\Database::connect('default');
        $db->table('medical_informed_consents')->where('id', $id)->delete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'Dokumen persetujuan tindakan berhasil dihapus']);
    }

    /**
     * Cetak Dokumen Resmi Lembar Persetujuan Tindakan Medis (Informed Consent)
     */
    public function cetakInformedConsent($consentId)
    {
        $db = \Config\Database::connect('default');
        $consent = $db->table('medical_informed_consents')
                      ->select('medical_informed_consents.*, patients.name as patient_name, patients.no_rm, patients.nik, patients.gender, patients.date_of_birth, patients.address, patients.phone, doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp, doctors.sip_number')
                      ->join('patients', 'patients.id = medical_informed_consents.patient_id', 'left')
                      ->join('doctors', 'doctors.id = medical_informed_consents.doctor_id', 'left')
                      ->where('medical_informed_consents.id', $consentId)
                      ->get()
                      ->getRow();

        if (!$consent) {
            session()->setFlashdata('error', 'Dokumen Informed Consent tidak ditemukan.');
            return redirect()->to(base_url('klinik/pendaftaran'));
        }

        // Hitung Usia
        $ageStr = '-';
        if (!empty($consent->date_of_birth)) {
            $dob = new \DateTime($consent->date_of_birth);
            $now = new \DateTime();
            $diff = $now->diff($dob);
            $ageStr = $diff->y . ' Tahun ' . $diff->m . ' Bulan';
        }

        $data = [
            'title'   => 'Informed Consent - ' . $consent->procedure_name . ' (' . $consent->patient_name . ')',
            'consent' => $consent,
            'age'     => $ageStr
        ];

        return view('klinik/cetak_informed_consent', $data);
    }

    /**
     * Upload / Simpan Foto Dokumentasi Pasien (Lembar 6 & 7 - Kamera iPad & Upload File)
     */
    public function saveMedicalPhoto()
    {
        $db = \Config\Database::connect('default');
        $patientId = $this->request->getPost('patient_id');
        $visitId   = $this->request->getPost('visit_id') ?: null;
        $category  = $this->request->getPost('category') ?: 'before';
        $title     = $this->request->getPost('title') ?: 'Dokumentasi Klinis';
        $desc      = $this->request->getPost('description');
        $takenBy   = $this->request->getPost('taken_by') ?: (session()->get('user_name') ?: 'Perawat');

        if (!$patientId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Pasien tidak valid']);
        }

        $photoPath = '';

        // 1. Cek jika dikirim via File Upload (File input / iPad camera capture)
        $file = $this->request->getFile('photo_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/medical_photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $newName = 'PHOTO_' . $patientId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file->getExtension();
            $file->move($uploadDir, $newName);
            $photoPath = 'uploads/medical_photos/' . $newName;
        }

        // 2. Cek jika dikirim via Base64 Image (iPad Snapshot / WebCam Canvas)
        $base64Data = $this->request->getPost('photo_base64');
        if (empty($photoPath) && !empty($base64Data)) {
            $uploadDir = FCPATH . 'uploads/medical_photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $parts = explode(',', $base64Data);
            $rawImg = base64_decode(end($parts));
            $newName = 'SNAP_' . $patientId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
            file_put_contents($uploadDir . $newName, $rawImg);
            $photoPath = 'uploads/medical_photos/' . $newName;
        }

        if (empty($photoPath)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File foto tidak ditemukan atau gagal diunggah.']);
        }

        $data = [
            'patient_id'  => $patientId,
            'visit_id'    => $visitId,
            'category'    => $category,
            'title'       => $title,
            'photo_path'  => $photoPath,
            'description' => $desc,
            'taken_by'    => $takenBy
        ];

        $db->table('medical_photos')->insert($data);
        $insertId = $db->insertID();

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Foto dokumentasi klinis berhasil disimpan.',
            'id'         => $insertId,
            'photo_path' => base_url($photoPath)
        ]);
    }

    /**
     * Hapus Foto Dokumentasi Klinis
     */
    public function deleteMedicalPhoto($id)
    {
        $db = \Config\Database::connect('default');
        $row = $db->table('medical_photos')->where('id', $id)->get()->getRow();
        if ($row && !empty($row->photo_path) && file_exists(FCPATH . $row->photo_path)) {
            @unlink(FCPATH . $row->photo_path);
        }
        $db->table('medical_photos')->where('id', $id)->delete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'Foto dokumentasi klinis berhasil dihapus.']);
    }

    /**
     * Cetak Ringkasan Lengkap Rekam Medis Pasien (Medical Record Summary)
     */
    public function cetakRingkasanRme($patientId)
    {
        $db = \Config\Database::connect('default');
        $patient = $db->table('patients')->where('id', $patientId)->get()->getRow();
        if (!$patient) {
            session()->setFlashdata('error', 'Data pasien tidak ditemukan.');
            return redirect()->to(base_url('klinik/pendaftaran'));
        }

        // Hitung Usia
        $ageStr = '-';
        if (!empty($patient->date_of_birth)) {
            $dob = new \DateTime($patient->date_of_birth);
            $now = new \DateTime();
            $diff = $now->diff($dob);
            $ageStr = $diff->y . ' Tahun ' . $diff->m . ' Bulan';
        }

        $visits = $db->table('patient_visits')
                     ->select('patient_visits.*, 
                               COALESCE(polikliniks.name, polyclinics.name) as polyclinic_name,
                               tindakan.name as tindakan_name,
                               doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp,
                               medical_records.subjective,
                               medical_records.objective,
                               medical_records.assessment,
                               medical_records.plan,
                               medical_records.icd10_code,
                               medical_records.icd9_code,
                               medical_records.doctor_notes,
                               triage_records.weight,
                               triage_records.height,
                               triage_records.blood_pressure,
                               triage_records.temperature,
                               triage_records.pulse,
                               triage_records.respiration,
                               triage_records.complaints,
                               triage_records.anamnesis')
                     ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                     ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                     ->join('tindakan', 'tindakan.id = patient_visits.service_id', 'left')
                     ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                     ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                     ->join('triage_records', 'triage_records.visit_id = patient_visits.id', 'left')
                     ->where('patient_visits.patient_id', $patientId)
                     ->orderBy('patient_visits.visit_date', 'DESC')
                     ->get()
                     ->getResult();

        foreach ($visits as $v) {
            $v->medicines = $db->table('prescription_details')
                               ->select('prescription_details.*, medicines.name as medicine_name, medicines.unit')
                               ->join('prescriptions', 'prescriptions.id = prescription_details.prescription_id', 'left')
                               ->join('medicines', 'medicines.id = prescription_details.medicine_id', 'left')
                               ->where('prescriptions.visit_id', $v->id)
                               ->get()
                               ->getResult();

            if (!empty($v->icd10_code)) {
                $icd = $db->table('master_icd10')->where('code', $v->icd10_code)->get()->getRow();
                $v->icd10_desc = $icd ? ($icd->name_id ?? $icd->name_en ?? '') : '';
            } else {
                $v->icd10_desc = '';
            }
        }

        $informedConsents = $db->table('medical_informed_consents')
                               ->select('medical_informed_consents.*, doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp, doctors.sip_number as doctor_sip, doctors.digital_signature as doctor_ref_signature')
                               ->join('doctors', 'doctors.id = medical_informed_consents.doctor_id', 'left')
                               ->where('medical_informed_consents.patient_id', $patientId)
                               ->orderBy('medical_informed_consents.consent_date', 'DESC')
                               ->get()
                               ->getResult();

        $medicalPhotos = $db->table('medical_photos')
                            ->where('patient_id', $patientId)
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->getResult();

        $data = [
            'title'            => 'Ringkasan Rekam Medis - ' . $patient->name . ' (' . $patient->no_rm . ')',
            'patient'          => $patient,
            'age'              => $ageStr,
            'visits'           => $visits,
            'informedConsents' => $informedConsents,
            'medicalPhotos'    => $medicalPhotos
        ];

        return view('klinik/cetak_ringkasan_rme', $data);
    }

    /**
     * Ekspor Database Pasien ke Excel / CSV
     */
    public function exportPasienExcel()
    {
        $db = \Config\Database::connect('default');

        $builder = $db->table('patients');

        $gender    = $this->request->getGet('gender');
        $tier      = $this->request->getGet('tier');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if (!empty($gender)) {
            $builder->where('gender', $gender);
        }
        if (!empty($tier)) {
            $builder->where('membership_tier', $tier);
        }
        if (!empty($startDate)) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('DATE(created_at) <=', $endDate);
        }

        $patients = $builder->orderBy('id', 'ASC')->get()->getResult();

        $filename = 'Database_Pasien_GitriaFarma_' . date('Ymd_His') . '.csv';

        // Set Headers for CSV Download with UTF-8 BOM
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Output UTF-8 BOM so Excel opens with proper encoding
        fputs($output, "\xEF\xBB\xBF");

        // Header Columns
        fputcsv($output, [
            'No',
            'No. Rekam Medis (No RM)',
            'NIK Kependudukan',
            'Nama Lengkap Pasien',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Usia',
            'Pekerjaan',
            'Agama',
            'Pendidikan',
            'Golongan Darah',
            'Status Pernikahan',
            'No. WhatsApp / HP',
            'No. BPJS',
            'Alamat Domisili',
            'Riwayat Alergi',
            'Riwayat Penyakit Terdahulu (RPD)',
            'Kontak Darurat (Nama)',
            'Kontak Darurat (No Telp)',
            'Kategori Member',
            'Tanggal Pendaftaran'
        ]);

        $no = 1;
        foreach ($patients as $p) {
            $ageStr = '-';
            if (!empty($p->date_of_birth)) {
                $dob = new \DateTime($p->date_of_birth);
                $today = new \DateTime('today');
                $ageStr = $dob->diff($today)->y . ' th';
            }

            fputcsv($output, [
                $no++,
                $p->no_rm,
                "\t" . ($p->nik ?: '-'),
                $p->name,
                $p->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $p->place_of_birth ?: '-',
                $p->date_of_birth ?: '-',
                $ageStr,
                $p->occupation ?: '-',
                $p->religion ?: 'Islam',
                $p->education ?: '-',
                $p->blood_type ?: '-',
                $p->marital_status ?: '-',
                "\t" . ($p->phone ?: '-'),
                "\t" . ($p->bpjs_number ?: '-'),
                $p->address ?: '-',
                $p->allergies ?: 'Tidak Ada',
                $p->medical_history ?: '-',
                $p->emergency_contact_name ?: '-',
                "\t" . ($p->emergency_contact_phone ?: '-'),
                ucfirst($p->membership_tier ?? 'regular'),
                $p->created_at ? date('d/m/Y H:i', strtotime($p->created_at)) : '-'
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Cetak Laporan Database Pasien (PDF / Print View)
     */
    public function cetakLaporanPasien()
    {
        $db = \Config\Database::connect('default');

        $builder = $db->table('patients');

        $gender    = $this->request->getGet('gender');
        $tier      = $this->request->getGet('tier');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        if (!empty($gender)) {
            $builder->where('gender', $gender);
        }
        if (!empty($tier)) {
            $builder->where('membership_tier', $tier);
        }
        if (!empty($startDate)) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('DATE(created_at) <=', $endDate);
        }

        $patients = $builder->orderBy('id', 'ASC')->get()->getResult();

        // Hitung Statistik
        $totalPatients = count($patients);
        $totalMale     = 0;
        $totalFemale   = 0;
        $totalBpjs     = 0;
        $totalUmum     = 0;

        foreach ($patients as $p) {
            if ($p->gender === 'L') $totalMale++;
            else $totalFemale++;

            if (!empty($p->bpjs_number) && $p->bpjs_number !== '-') $totalBpjs++;
            else $totalUmum++;
        }

        $data = [
            'title'         => 'Laporan Database Pasien - ' . clinic_setting('clinic_name', 'Gitria Farma'),
            'patients'      => $patients,
            'totalPatients' => $totalPatients,
            'totalMale'     => $totalMale,
            'totalFemale'   => $totalFemale,
            'totalBpjs'     => $totalBpjs,
            'totalUmum'     => $totalUmum,
            'filterGender'  => $gender,
            'filterTier'    => $tier,
            'startDate'     => $startDate,
            'endDate'       => $endDate
        ];

        return view('klinik/cetak_laporan_pasien', $data);
    }

    // =========================================================================
    // MODUL SURAT KETERANGAN MEDIS & RUJUKAN (SKS, SKD, SRP)
    // =========================================================================
    public function surat()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            if ($action === 'create_letter') {
                $letterType = $this->request->getPost('letter_type'); // 'sakit', 'sehat', 'rujukan'
                $visitId    = (int)$this->request->getPost('visit_id');
                $patientId  = (int)$this->request->getPost('patient_id');
                $doctorId   = (int)$this->request->getPost('doctor_id') ?: 1;
                $letterDate = $this->request->getPost('letter_date') ?: date('Y-m-d');

                // Generate Letter Number: PREFIX-YYYYMM-XXXX
                $prefixMap = ['sakit' => 'SKS', 'sehat' => 'SKD', 'rujukan' => 'SRP'];
                $prefix = ($prefixMap[$letterType] ?? 'SKM') . '-' . date('Ym') . '-';
                
                $lastLetter = $db->table('medical_letters')
                                 ->where("letter_no LIKE '{$prefix}%'")
                                 ->orderBy('id', 'DESC')
                                 ->limit(1)
                                 ->get()
                                 ->getRow();
                $nextSeq = 1;
                if ($lastLetter && preg_match('/-(\d+)$/', $lastLetter->letter_no, $m)) {
                    $nextSeq = intval($m[1]) + 1;
                }
                $letterNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

                $letterData = [
                    'letter_no'            => $letterNo,
                    'letter_type'          => $letterType,
                    'visit_id'             => $visitId,
                    'patient_id'           => $patientId,
                    'doctor_id'            => $doctorId,
                    'letter_date'          => $letterDate,
                    'start_date'           => $this->request->getPost('start_date') ?: null,
                    'end_date'             => $this->request->getPost('end_date') ?: null,
                    'duration_days'        => (int)($this->request->getPost('duration_days') ?: 1),
                    'diagnosis'            => $this->request->getPost('diagnosis'),
                    'purpose'              => $this->request->getPost('purpose'),
                    'health_status'        => $this->request->getPost('health_status') ?: 'sehat',
                    'blood_pressure'       => $this->request->getPost('blood_pressure'),
                    'weight'               => $this->request->getPost('weight') ?: null,
                    'height'               => $this->request->getPost('height') ?: null,
                    'color_blind'          => $this->request->getPost('color_blind') ?: 'normal',
                    'referral_destination' => $this->request->getPost('referral_destination'),
                    'referral_poly'        => $this->request->getPost('referral_poly'),
                    'referral_reason'      => $this->request->getPost('referral_reason'),
                    'notes'                => $this->request->getPost('notes'),
                    'created_at'           => date('Y-m-d H:i:s')
                ];

                $db->table('medical_letters')->insert($letterData);
                session()->setFlashdata('success', "Surat Keterangan Medis berhasil diterbitkan dengan Nomor: {$letterNo}");
                return redirect()->to(base_url('klinik/surat'));
            }

            if ($action === 'delete_letter') {
                $letterId = (int)$this->request->getPost('letter_id');
                $db->table('medical_letters')->where('id', $letterId)->delete();
                session()->setFlashdata('success', 'Surat keterangan medis berhasil dihapus.');
                return redirect()->to(base_url('klinik/surat'));
            }
        }

        $letters = $db->table('medical_letters')
                      ->select('medical_letters.*, 
                                patients.name as patient_name, 
                                patients.no_rm, 
                                patients.date_of_birth,
                                patients.gender,
                                patients.address,
                                doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp,
                                doctors.sip_number')
                      ->join('patients', 'patients.id = medical_letters.patient_id')
                      ->join('doctors', 'doctors.id = medical_letters.doctor_id', 'left')
                      ->orderBy('medical_letters.id', 'DESC')
                      ->get()
                      ->getResult();

        $recentVisits = $db->table('patient_visits')
                           ->select('patient_visits.id, 
                                     patient_visits.no_visit, 
                                     patient_visits.visit_date, 
                                     patient_visits.doctor_id,
                                     patients.id as patient_id, 
                                     patients.name as patient_name, 
                                     patients.no_rm,
                                     doctors.name as doctor_name,
                                     triage_records.blood_pressure,
                                     triage_records.weight,
                                     triage_records.height,
                                     medical_records.assessment,
                                     medical_records.icd10_code')
                           ->join('patients', 'patients.id = patient_visits.patient_id')
                           ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                           ->join('triage_records', 'triage_records.visit_id = patient_visits.id', 'left')
                           ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                           ->orderBy('patient_visits.id', 'DESC')
                           ->limit(50)
                           ->get()
                           ->getResult();

        $doctors = $db->table('doctors')->where('status', 'active')->get()->getResult();

        $data = [
            'title'        => 'Surat Keterangan Medis & Rujukan',
            'active_menu'  => 'surat',
            'letters'      => $letters,
            'recentVisits' => $recentVisits,
            'doctors'      => $doctors
        ];

        return view('klinik/surat', $data);
    }

    public function cetakSurat($letterId)
    {
        $db = \Config\Database::connect('default');

        $letter = $db->table('medical_letters')
                     ->select('medical_letters.*, 
                               patients.name as patient_name, 
                               patients.no_rm, 
                               patients.nik, 
                               patients.date_of_birth,
                               patients.gender,
                               patients.address,
                               patients.phone,
                               doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp,
                               doctors.sip_number,
                               polyclinics.name as polyclinic_name')
                     ->join('patients', 'patients.id = medical_letters.patient_id')
                     ->join('doctors', 'doctors.id = medical_letters.doctor_id', 'left')
                     ->join('patient_visits', 'patient_visits.id = medical_letters.visit_id', 'left')
                     ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                     ->where('medical_letters.id', $letterId)
                     ->get()
                     ->getRow();

        if (!$letter) {
            session()->setFlashdata('error', 'Data Surat tidak ditemukan.');
            return redirect()->to(base_url('klinik/surat'));
        }

        $data = [
            'title'  => 'Cetak Surat - ' . $letter->letter_no,
            'letter' => $letter
        ];

        return view('klinik/cetak_surat', $data);
    }

    // =========================================================================
    // MODUL PEMERIKSAAN LABORATORIUM & HASIL PENUNJANG
    // =========================================================================
    public function lab()
    {
        $db = \Config\Database::connect('default');

        if (strtolower($this->request->getMethod()) === 'post') {
            $action = $this->request->getPost('action');

            if ($action === 'save_lab') {
                $visitId     = (int)$this->request->getPost('visit_id');
                $patientId   = (int)$this->request->getPost('patient_id');
                $doctorId    = (int)$this->request->getPost('doctor_id') ?: 1;
                $officerName = trim($this->request->getPost('officer_name') ?: 'Analis Lab Sawamawa');
                $testDate    = $this->request->getPost('test_date') ?: date('Y-m-d');
                $testType    = $this->request->getPost('test_type');
                $resultVal   = $this->request->getPost('result_value');
                $normalRange = $this->request->getPost('normal_range');
                $unit        = $this->request->getPost('unit');
                $status      = $this->request->getPost('status') ?: 'normal';
                $notes       = $this->request->getPost('notes');

                // Generate Lab No: LAB-YYYYMM-XXXX
                $prefix = 'LAB-' . date('Ym') . '-';
                $lastLab = $db->table('lab_results')
                              ->where("lab_no LIKE '{$prefix}%'")
                              ->orderBy('id', 'DESC')
                              ->limit(1)
                              ->get()
                              ->getRow();
                $nextSeq = 1;
                if ($lastLab && preg_match('/-(\d+)$/', $lastLab->lab_no, $m)) {
                    $nextSeq = intval($m[1]) + 1;
                }
                $labNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

                $db->table('lab_results')->insert([
                    'lab_no'       => $labNo,
                    'visit_id'     => $visitId,
                    'patient_id'   => $patientId,
                    'doctor_id'    => $doctorId,
                    'officer_name' => $officerName,
                    'test_date'    => $testDate,
                    'test_type'    => $testType,
                    'result_value' => $resultVal,
                    'normal_range' => $normalRange,
                    'unit'         => $unit,
                    'status'       => $status,
                    'notes'        => $notes,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);

                session()->setFlashdata('success', "Hasil Pemeriksaan Laboratorium berhasil disimpan dengan No: {$labNo}");
                return redirect()->to(base_url('klinik/lab'));
            }

            if ($action === 'delete_lab') {
                $labId = (int)$this->request->getPost('lab_id');
                $db->table('lab_results')->where('id', $labId)->delete();
                session()->setFlashdata('success', 'Hasil Lab berhasil dihapus.');
                return redirect()->to(base_url('klinik/lab'));
            }
        }

        $labResults = $db->table('lab_results')
                         ->select('lab_results.*, 
                                   patients.name as patient_name, 
                                   patients.no_rm, 
                                   patients.gender, 
                                   patients.date_of_birth,
                                   doctors.name as doctor_name')
                         ->join('patients', 'patients.id = lab_results.patient_id')
                         ->join('doctors', 'doctors.id = lab_results.doctor_id', 'left')
                         ->orderBy('lab_results.id', 'DESC')
                         ->get()
                         ->getResult();

        $recentVisits = $db->table('patient_visits')
                           ->select('patient_visits.id, 
                                     patient_visits.no_visit, 
                                     patient_visits.doctor_id,
                                     patients.id as patient_id, 
                                     patients.name as patient_name, 
                                     patients.no_rm')
                           ->join('patients', 'patients.id = patient_visits.patient_id')
                           ->orderBy('patient_visits.id', 'DESC')
                           ->limit(50)
                           ->get()
                           ->getResult();

        $doctors = $db->table('doctors')->where('status', 'active')->get()->getResult();
        $labTests = $db->table('lab_tests')->where('status', 'active')->orderBy('category', 'ASC')->orderBy('name', 'ASC')->get()->getResult();

        $data = [
            'title'        => 'Pemeriksaan & Hasil Laboratorium',
            'active_menu'  => 'lab',
            'labResults'   => $labResults,
            'recentVisits' => $recentVisits,
            'doctors'      => $doctors,
            'labTests'     => $labTests
        ];

        return view('klinik/lab', $data);
    }

    public function cetakLab($labId)
    {
        $db = \Config\Database::connect('default');

        $lab = $db->table('lab_results')
                  ->select('lab_results.*, 
                            patients.name as patient_name, 
                            patients.no_rm, 
                            patients.nik, 
                            patients.gender, 
                            patients.date_of_birth, 
                            patients.address,
                            doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp,
                            doctors.sip_number')
                  ->join('patients', 'patients.id = lab_results.patient_id')
                  ->join('doctors', 'doctors.id = lab_results.doctor_id', 'left')
                  ->where('lab_results.id', $labId)
                  ->get()
                  ->getRow();

        if (!$lab) {
            session()->setFlashdata('error', 'Hasil lab tidak ditemukan.');
            return redirect()->to(base_url('klinik/lab'));
        }

        $data = [
            'title' => 'Cetak Hasil Laboratorium - ' . $lab->lab_no,
            'lab'   => $lab
        ];

        return view('klinik/cetak_lab', $data);
    }

    // =========================================================================
    // MODUL TV DISPLAY ANTREAN RUANG TUNGGU
    // =========================================================================
    public function display()
    {
        $db = \Config\Database::connect('default');

        $polyclinics = $db->table('polyclinics')
                          ->where('status', 'active')
                          ->get()
                          ->getResult();

        $today = date('Y-m-d');
        $activeQueues = [];

        foreach ($polyclinics as $p) {
            $currentQueue = $db->table('queue_numbers')
                               ->select('queue_numbers.id as id,
                                         queue_numbers.queue_no,
                                         queue_numbers.status,
                                         queue_numbers.created_at,
                                         patients.name as patient_name,
                                         doctors.name as doctor_name')
                               ->join('patient_visits', 'patient_visits.id = queue_numbers.visit_id', 'left')
                               ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                               ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                               ->where('queue_numbers.polyclinic_id', $p->id)
                               ->where('DATE(queue_numbers.created_at)', $today)
                               ->whereNotIn('queue_numbers.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done'])
                               ->whereNotIn('patient_visits.status', ['cancelled', 'completed', 'cashier', 'prescription', 'pharmacy', 'done'])
                               ->orderBy("CASE WHEN queue_numbers.status = 'called' OR queue_numbers.status = 'triage' THEN 1 WHEN queue_numbers.status = 'waiting' THEN 2 ELSE 3 END", '', false)
                               ->orderBy('queue_numbers.id', 'DESC')
                               ->limit(1)
                               ->get()
                               ->getRow();
            $activeQueues[$p->id] = $currentQueue;
        }

        $data = [
            'title'        => 'Layar Display Antrean Poliklinik - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
            'polyclinics'  => $polyclinics,
            'activeQueues' => $activeQueues,
            'tvMediaType'  => clinic_setting('tv_media_type', 'slideshow'),
            'tvVideoUrl'   => clinic_setting('tv_video_url', ''),
            'tvYoutubeId'  => clinic_setting('tv_youtube_id', 'dQw4w9WgXcQ')
        ];

        return view('klinik/display', $data);
    }

    /**
     * Simpan Pengaturan Media Display TV (Slideshow / Video Lokal / YouTube)
     * POST klinik/save-tv-media
     */
    public function saveTvMedia()
    {
        $db = \Config\Database::connect('default');
        $mediaType = $this->request->getPost('media_type') ?: 'slideshow';
        $youtubeId = trim((string)$this->request->getPost('youtube_id'));
        $videoUrl  = trim((string)$this->request->getPost('video_url'));

        // Ekstraksi ID YouTube jika user menginput full URL
        if (!empty($youtubeId)) {
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $youtubeId, $matches)) {
                $youtubeId = $matches[1];
            }
        }

        // Upload Video File jika ada
        $file = $this->request->getFile('video_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                $newName = 'display_ad_' . time() . '.' . $ext;
                $file->move(FCPATH . 'uploads/videos', $newName);
                $videoUrl = base_url('uploads/videos/' . $newName);
                $mediaType = 'local_video';
            }
        }

        $saveSetting = function($key, $val) use ($db) {
            $exists = $db->table('system_settings')->where('setting_key', $key)->countAllResults();
            if ($exists > 0) {
                $db->table('system_settings')->where('setting_key', $key)->update([
                    'setting_value' => $val,
                    'updated_at'    => date('Y-m-d H:i:s')
                ]);
            } else {
                $db->table('system_settings')->insert([
                    'setting_key'   => $key,
                    'setting_value' => $val,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
            }
        };

        $saveSetting('tv_media_type', $mediaType);
        $saveSetting('tv_youtube_id', $youtubeId);
        $saveSetting('tv_video_url', $videoUrl);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => 'Pengaturan media iklan layar TV berhasil disimpan.',
                'media_type' => $mediaType,
                'video_url'  => $videoUrl,
                'youtube_id' => $youtubeId
            ]);
        }

        session()->setFlashdata('success', 'Pengaturan media display TV berhasil diperbarui.');
        return redirect()->to(base_url('klinik/display'));
    }

    // =========================================================================
    // ANJUNGAN PENDAFTARAN MANDIRI (TOUCHSCREEN KIOSK APM)
    // =========================================================================

    /**
     * Tampilan Layar Sentuh Anjungan Pendaftaran Mandiri
     * GET klinik/kiosk
     */
    public function kiosk()
    {
        $db = \Config\Database::connect('default');

        $polyclinics = $db->table('polikliniks')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();
        $doctors = $db->table('doctors')->where('status', 'active')->orderBy('id', 'ASC')->get()->getResult();
        
        $tindakanServices = $db->table('tindakan')
                               ->select('tindakan.id, tindakan.name, tindakan.parent_id, tindakan.price, parent_tind.name as parent_name')
                               ->join('tindakan as parent_tind', 'parent_tind.id = tindakan.parent_id', 'left')
                               ->where('tindakan.status', 'active')
                               ->where('tindakan.price >', 0)
                               ->orderBy('tindakan.parent_id', 'ASC')
                               ->orderBy('tindakan.id', 'ASC')
                               ->get()
                               ->getResult();

        $paymentMethods = $db->table('payment_methods')
                             ->where('is_active', 1)
                             ->orderBy('category', 'ASC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResult();

        $data = [
            'title'            => 'Anjungan Pendaftaran Mandiri (Kiosk) - ' . clinic_setting('clinic_name', 'Sawamawa Medical Center'),
            'polyclinics'      => $polyclinics,
            'doctors'          => $doctors,
            'tindakanServices' => $tindakanServices,
            'paymentMethods'   => $paymentMethods
        ];

        return view('klinik/kiosk', $data);
    }

    /**
     * Cek Data Pasien Lama via NIK / No. RM / Scan QR
     * POST klinik/kiosk-check-patient
     */
    public function kioskCheckPatient()
    {
        $db = \Config\Database::connect('default');
        $rawKeyword = trim((string)$this->request->getPost('keyword'));

        if (empty($rawKeyword)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Silakan masukkan NIK, Nomor Rekam Medis (No RM), atau Nama Pasien.'
            ]);
        }

        // 1. Parsing jika hasil scan QR berformat: SAWAMAWA|RM-000001|Nama|NIK
        $keyword = $rawKeyword;
        if (str_contains($rawKeyword, '|')) {
            $parts = explode('|', $rawKeyword);
            $keyword = trim($parts[1] ?? $parts[0]);
        }

        // Bersihkan whitespace berlebih
        $cleanDigits = preg_replace('/[^0-9]/', '', $keyword);
        $possibleRms = [$keyword];

        // Format nomor RM kemungkinan: jika input "1" -> "RM-000001", jika "000001" -> "RM-000001"
        if (!empty($cleanDigits) && strlen($cleanDigits) <= 6) {
            $possibleRms[] = 'RM-' . str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
            $possibleRms[] = 'RM' . str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
            $possibleRms[] = str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
        }

        // 2. Cek apakah ada kecocokan EXACT terlebih dahulu (No RM atau NIK atau Phone)
        $exactPatient = $db->table('patients')
                           ->groupStart()
                               ->whereIn('no_rm', $possibleRms)
                               ->orWhere('nik', $keyword)
                               ->orWhere('phone', $keyword)
                           ->groupEnd()
                           ->get()
                           ->getRow();

        if ($exactPatient) {
            $dobFormatted = $exactPatient->date_of_birth ? date('d-m-Y', strtotime($exactPatient->date_of_birth)) : '-';
            $age = $exactPatient->date_of_birth ? (date('Y') - date('Y', strtotime($exactPatient->date_of_birth))) : 0;

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data pasien ditemukan.',
                'patient' => [
                    'id'              => $exactPatient->id,
                    'no_rm'           => $exactPatient->no_rm,
                    'nik'             => $exactPatient->nik,
                    'name'            => $exactPatient->name,
                    'gender'          => $exactPatient->gender,
                    'dob'             => $dobFormatted,
                    'age'             => $age,
                    'phone'           => $exactPatient->phone,
                    'address'         => $exactPatient->address,
                    'bpjs_number'     => $exactPatient->bpjs_number,
                    'membership_tier' => strtolower($exactPatient->membership_tier ?? 'regular')
                ]
            ]);
        }

        // 3. Jika tidak ada exact match, lakukan pencarian fleksibel (LIKE name / no_rm / nik / phone)
        $candidates = $db->table('patients')
                         ->groupStart()
                             ->like('name', $keyword)
                             ->orLike('no_rm', $keyword)
                             ->orLike('nik', $keyword)
                             ->orLike('phone', $keyword)
                         ->groupEnd()
                         ->limit(6)
                         ->get()
                         ->getResult();

        if (empty($candidates)) {
            return $this->response->setJSON([
                'status'  => 'not_found',
                'message' => 'Data pasien tidak ditemukan dengan kata kunci "' . esc($rawKeyword) . '". Silakan periksa kembali atau pilih menu Pasien Baru.'
            ]);
        }

        if (count($candidates) === 1) {
            $p = $candidates[0];
            $dobFormatted = $p->date_of_birth ? date('d-m-Y', strtotime($p->date_of_birth)) : '-';
            $age = $p->date_of_birth ? (date('Y') - date('Y', strtotime($p->date_of_birth))) : 0;

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data pasien ditemukan.',
                'patient' => [
                    'id'              => $p->id,
                    'no_rm'           => $p->no_rm,
                    'nik'             => $p->nik,
                    'name'            => $p->name,
                    'gender'          => $p->gender,
                    'dob'             => $dobFormatted,
                    'age'             => $age,
                    'phone'           => $p->phone,
                    'address'         => $p->address,
                    'bpjs_number'     => $p->bpjs_number,
                    'membership_tier' => strtolower($p->membership_tier ?? 'regular')
                ]
            ]);
        }

        // 4. Jika ditemukan beberapa pasien (Multiple Candidates), kembalikan daftar kandidat untuk disentuh langsung
        $candidateList = [];
        foreach ($candidates as $c) {
            $dobFormatted = $c->date_of_birth ? date('d/m/Y', strtotime($c->date_of_birth)) : '-';
            $age = $c->date_of_birth ? (date('Y') - date('Y', strtotime($c->date_of_birth))) : 0;
            $maskedNik = !empty($c->nik) && strlen($c->nik) >= 10 ? substr($c->nik, 0, 6) . '******' . substr($c->nik, -4) : ($c->nik ?: '-');
            $maskedPhone = !empty($c->phone) && strlen($c->phone) >= 8 ? substr($c->phone, 0, 4) . '****' . substr($c->phone, -3) : ($c->phone ?: '-');

            $candidateList[] = [
                'id'              => $c->id,
                'no_rm'           => $c->no_rm,
                'nik'             => $c->nik,
                'masked_nik'      => $maskedNik,
                'name'            => $c->name,
                'gender'          => $c->gender,
                'dob'             => $dobFormatted,
                'age'             => $age,
                'phone'           => $c->phone,
                'masked_phone'    => $maskedPhone,
                'address'         => $c->address,
                'bpjs_number'     => $c->bpjs_number,
                'membership_tier' => strtolower($c->membership_tier ?? 'regular')
            ];
        }

        return $this->response->setJSON([
            'status'     => 'candidates',
            'message'    => 'Ditemukan ' . count($candidateList) . ' data pasien. Sentuh nama Anda di bawah ini:',
            'candidates' => $candidateList
        ]);
    }

    /**
     * Pendaftaran Kunjungan Pasien Lama dari Kiosk
     * POST klinik/kiosk-register-visit
     */
    public function kioskRegisterVisit()
    {
        $db = \Config\Database::connect('default');

        $patientId     = (int)$this->request->getPost('patient_id');
        $visitType     = $this->request->getPost('visit_type') ?: 'poli';
        $polyclinicId  = (int)$this->request->getPost('polyclinic_id');
        $doctorId      = (int)$this->request->getPost('doctor_id');
        $serviceId     = (int)$this->request->getPost('service_id');
        $paymentMethod = $this->request->getPost('payment_method') ?: 'Tunai / Umum';

        $patient = $db->table('patients')->where('id', $patientId)->get()->getRow();
        if (!$patient) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data pasien tidak valid.'
            ]);
        }

        $res = $this->clinicService->createVisit($patientId, $polyclinicId, $doctorId, $paymentMethod, $visitType, $serviceId);
        if ($res['status'] !== 'success') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $res['message']
            ]);
        }

        // Cari data poli / tindakan & dokter
        $targetName = 'Poliklinik Umum';
        if ($visitType === 'tindakan') {
            $tind = $db->table('tindakan')->where('id', $serviceId)->get()->getRow();
            $targetName = 'Ruang Tindakan ' . ($tind ? $tind->name : 'Medis');
        } else {
            $poly = $db->table('polikliniks')->where('id', $polyclinicId)->get()->getRow();
            $targetName = 'Poliklinik ' . ($poly ? $poly->name : 'Umum');
        }

        $doc = $db->table('doctors')->where('id', $doctorId)->get()->getRow();
        $doctorName = $doc ? $doc->name : 'Dokter Jaga';

        // Hitung estimasi antrean di depan
        $waitingCount = $db->table('queue_numbers')
                           ->where('polyclinic_id', $polyclinicId)
                           ->where('DATE(created_at)', date('Y-m-d'))
                           ->where('status', 'waiting')
                           ->countAllResults();

        // Kirim Notifikasi Siaran Terpadu
        try {
            \App\Services\NotificationService::send([
                'category'     => 'queue',
                'type'         => 'success',
                'title'        => 'Pasien Kiosk Mandiri: ' . $res['queue_no'],
                'message'      => 'Pasien ' . $patient->name . ' (' . $patient->no_rm . ') mengambil nomor antrean ' . $res['queue_no'] . ' menuju ' . $targetName,
                'badge'        => 'KIOSK APM',
                'icon'         => 'fas fa-desktop',
                'link'         => 'klinik/antrean',
                'target_roles' => null
            ]);
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => 'Pendaftaran antrean mandiri berhasil.',
            'ticket'    => [
                'queue_no'        => $res['queue_no'],
                'no_visit'        => $res['no_visit'],
                'patient_name'    => $patient->name,
                'no_rm'           => $patient->no_rm,
                'target_name'     => $targetName,
                'doctor_name'     => $doctorName,
                'payment_method'  => $paymentMethod,
                'membership_tier' => strtolower($patient->membership_tier ?? 'regular'),
                'created_at'      => date('d-m-Y H:i:s'),
                'waiting_count'   => max(0, $waitingCount - 1),
                'clinic_name'     => clinic_setting('clinic_name', 'Sawamawa Medical Center'),
                'clinic_tagline'  => clinic_setting('clinic_tagline', 'Pusat Layanan Medis Terpadu'),
                'clinic_address'  => clinic_setting('clinic_address', 'Sumbawa Besar, NTB')
            ]
        ]);
    }

    /**
     * Pendaftaran Pasien Baru + Langsung Ambil Antrean dari Kiosk
     * POST klinik/kiosk-register-new-patient
     */
    public function kioskRegisterNewPatient()
    {
        $db = \Config\Database::connect('default');

        $patientData = [
            'nik'             => trim((string)$this->request->getPost('nik')),
            'name'            => trim((string)$this->request->getPost('name')),
            'gender'          => $this->request->getPost('gender') ?: 'L',
            'place_of_birth'  => trim((string)$this->request->getPost('place_of_birth') ?: 'Sumbawa'),
            'date_of_birth'   => $this->request->getPost('date_of_birth') ?: date('Y-m-d'),
            'phone'           => trim((string)$this->request->getPost('phone')),
            'address'         => trim((string)$this->request->getPost('address') ?: 'Sumbawa'),
            'bpjs_number'     => trim((string)$this->request->getPost('bpjs_number')),
            'membership_tier' => $this->request->getPost('membership_tier') ?: 'regular'
        ];

        // 1. Registrasi Pasien Baru
        $regRes = $this->clinicService->registerPatient($patientData);
        if ($regRes['status'] !== 'success') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $regRes['message']
            ]);
        }

        $patientId = $regRes['patient_id'];
        $noRm = $regRes['no_rm'];

        // 2. Buat Antrean Kunjungan Seketika
        $visitType     = $this->request->getPost('visit_type') ?: 'poli';
        $polyclinicId  = (int)$this->request->getPost('polyclinic_id');
        $doctorId      = (int)$this->request->getPost('doctor_id');
        $serviceId     = (int)$this->request->getPost('service_id');
        $paymentMethod = $this->request->getPost('payment_method') ?: 'Tunai / Umum';

        $res = $this->clinicService->createVisit($patientId, $polyclinicId, $doctorId, $paymentMethod, $visitType, $serviceId);
        if ($res['status'] !== 'success') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $res['message']
            ]);
        }

        // Resolusi Target
        $targetName = 'Poliklinik Umum';
        if ($visitType === 'tindakan') {
            $tind = $db->table('tindakan')->where('id', $serviceId)->get()->getRow();
            $targetName = 'Ruang Tindakan ' . ($tind ? $tind->name : 'Medis');
        } else {
            $poly = $db->table('polikliniks')->where('id', $polyclinicId)->get()->getRow();
            $targetName = 'Poliklinik ' . ($poly ? $poly->name : 'Umum');
        }

        $doc = $db->table('doctors')->where('id', $doctorId)->get()->getRow();
        $doctorName = $doc ? $doc->name : 'Dokter Jaga';

        $waitingCount = $db->table('queue_numbers')
                           ->where('polyclinic_id', $polyclinicId)
                           ->where('DATE(created_at)', date('Y-m-d'))
                           ->where('status', 'waiting')
                           ->countAllResults();

        // Siaran Notifikasi
        try {
            \App\Services\NotificationService::send([
                'category'     => 'patient',
                'type'         => 'info',
                'title'        => 'Pasien Baru Kiosk: ' . $patientData['name'],
                'message'      => 'Pasien Baru ' . $patientData['name'] . ' (No RM: ' . $noRm . ') mendaftar via Kiosk dan mengambil antrean ' . $res['queue_no'],
                'badge'        => 'PASIEN BARU APM',
                'icon'         => 'fas fa-user-plus',
                'link'         => 'klinik/antrean',
                'target_roles' => null
            ]);
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => 'Pendaftaran pasien baru & nomor antrean berhasil dibuat.',
            'ticket'    => [
                'queue_no'        => $res['queue_no'],
                'no_visit'        => $res['no_visit'],
                'patient_name'    => $patientData['name'],
                'no_rm'           => $noRm,
                'target_name'     => $targetName,
                'doctor_name'     => $doctorName,
                'payment_method'  => $paymentMethod,
                'membership_tier' => strtolower($patientData['membership_tier']),
                'created_at'      => date('d-m-Y H:i:s'),
                'waiting_count'   => max(0, $waitingCount - 1),
                'clinic_name'     => clinic_setting('clinic_name', 'Sawamawa Medical Center'),
                'clinic_tagline'  => clinic_setting('clinic_tagline', 'Pusat Layanan Medis Terpadu'),
                'clinic_address'  => clinic_setting('clinic_address', 'Sumbawa Besar, NTB')
            ]
        ]);
    }

    // =========================================================================
    // MODUL LAPORAN & REKAPITULASI KLINIK (TOP 10 MORBIDITAS DINKES)
    // =========================================================================
    public function laporan()
    {
        $db = \Config\Database::connect('default');

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        // 1. Top 10 Morbidity (ICD-10 Diagnoses)
        $topIcd = $db->table('medical_records')
                     ->select('medical_records.icd10_code, 
                               master_icd10.name_id, 
                               master_icd10.name_en, 
                               COUNT(medical_records.id) as total_cases')
                     ->join('master_icd10', 'master_icd10.code = medical_records.icd10_code', 'left')
                     ->join('patient_visits', 'patient_visits.id = medical_records.visit_id', 'left')
                     ->where('patient_visits.visit_date >=', $startDate)
                     ->where('patient_visits.visit_date <=', $endDate)
                     ->where('medical_records.icd10_code IS NOT NULL')
                     ->where('medical_records.icd10_code !=', '')
                     ->groupBy('medical_records.icd10_code')
                     ->orderBy('total_cases', 'DESC')
                     ->limit(10)
                     ->get()
                     ->getResult();

        // 2. Visits by Polyclinic / Service
        $visitsByPoli = $db->table('patient_visits')
                           ->select('COALESCE(polyclinics.name, "Tindakan Medis") as poly_name, COUNT(patient_visits.id) as total_visits')
                           ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                           ->where('patient_visits.visit_date >=', $startDate)
                           ->where('patient_visits.visit_date <=', $endDate)
                           ->groupBy('patient_visits.polyclinic_id')
                           ->orderBy('total_visits', 'DESC')
                           ->get()
                           ->getResult();

        // 3. Visits by Gender & Payment
        $genderStats = $db->table('patient_visits')
                          ->select('patients.gender, COUNT(patient_visits.id) as total')
                          ->join('patients', 'patients.id = patient_visits.patient_id')
                          ->where('patient_visits.visit_date >=', $startDate)
                          ->where('patient_visits.visit_date <=', $endDate)
                          ->groupBy('patients.gender')
                          ->get()
                          ->getResult();

        $paymentStats = $db->table('patient_visits')
                           ->select('payment_method, COUNT(id) as total')
                           ->where('visit_date >=', $startDate)
                           ->where('visit_date <=', $endDate)
                           ->groupBy('payment_method')
                           ->get()
                           ->getResult();

        // 4. Total Statistics
        $totalVisits = $db->table('patient_visits')
                          ->where('visit_date >=', $startDate)
                          ->where('visit_date <=', $endDate)
                          ->countAllResults();

        $totalLetters = $db->table('medical_letters')
                           ->where('letter_date >=', $startDate)
                           ->where('letter_date <=', $endDate)
                           ->countAllResults();

        $totalLabTests = $db->table('lab_results')
                            ->where('test_date >=', $startDate)
                            ->where('test_date <=', $endDate)
                            ->countAllResults();

        // 5. Analisis Waktu Pelayanan Pasien (SLA / Patient Journey Duration Analytics)
        $slaVisits = $db->table('patient_visits pv')
                        ->select('pv.id as visit_id, pv.no_visit, pv.visit_date, pv.created_at as time_arrival, pv.status as visit_status,
                                  p.name as patient_name, p.no_rm, p.gender,
                                  COALESCE(polikliniks.name, polyclinics.name, tindakan.name, "Poli Umum") as service_name,
                                  d.name as doctor_name,
                                  qn.queue_no,
                                  tr.created_at as time_triage,
                                  mr.created_at as time_doctor,
                                  bt.status as billing_status,
                                  bt.updated_at as time_cashier_raw,
                                  ct.created_at as time_cash_payment,
                                  pr.status as prescription_status,
                                  pr.dispensed_at as time_dispensed_raw,
                                  pr.created_at as time_presc_created,
                                  pv.updated_at as time_visit_updated')
                        ->join('patients p', 'p.id = pv.patient_id', 'left')
                        ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                        ->join('polyclinics', 'polyclinics.id = pv.polyclinic_id', 'left')
                        ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                        ->join('tindakan', 'tindakan.id = pv.service_id', 'left')
                        ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                        ->join('triage_records tr', 'tr.visit_id = pv.id', 'left')
                        ->join('medical_records mr', 'mr.visit_id = pv.id', 'left')
                        ->join('billing_transactions bt', 'bt.visit_id = pv.id', 'left')
                        ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                        ->join('prescriptions pr', 'pr.visit_id = pv.id', 'left')
                        ->where('pv.visit_date >=', $startDate)
                        ->where('pv.visit_date <=', $endDate)
                        ->where('pv.status !=', 'cancelled')
                        ->orderBy('pv.id', 'DESC')
                        ->get()
                        ->getResult();

        $processedSla = [];
        $totalWaitTtv = 0; $countTtv = 0;
        $totalDoctorDur = 0; $countDoc = 0;
        $totalCashierDur = 0; $countCashier = 0;
        $totalPharmacyDur = 0; $countPharmacy = 0;
        $totalOverallDur = 0; $countOverall = 0;

        foreach ($slaVisits as $sv) {
            $t0 = !empty($sv->time_arrival) ? strtotime($sv->time_arrival) : null;
            $t1 = !empty($sv->time_triage) ? strtotime($sv->time_triage) : null;
            $t2 = !empty($sv->time_doctor) ? strtotime($sv->time_doctor) : null;
            
            $timeCashier = null;
            if (!empty($sv->time_cash_payment)) {
                $timeCashier = strtotime($sv->time_cash_payment);
            } elseif ($sv->billing_status === 'paid' && !empty($sv->time_cashier_raw)) {
                $timeCashier = strtotime($sv->time_cashier_raw);
            }
            $t3 = $timeCashier;

            $timeDispensed = null;
            if (!empty($sv->time_dispensed_raw)) {
                $timeDispensed = strtotime($sv->time_dispensed_raw);
            } elseif ($sv->prescription_status === 'completed' || $sv->visit_status === 'completed') {
                $timeDispensed = !empty($sv->time_visit_updated) ? strtotime($sv->time_visit_updated) : null;
            }
            $t4 = $timeDispensed;

            // Durasi Pos 1: Tunggu & TTV Perawat (T1 - T0)
            $durTtvMinutes = ($t0 && $t1 && $t1 >= $t0) ? round(($t1 - $t0) / 60) : null;
            if ($durTtvMinutes !== null) {
                $totalWaitTtv += $durTtvMinutes;
                $countTtv++;
            }

            // Durasi Pos 2: Pemeriksaan & Resep Dokter (T2 - T1 atau T2 - T0)
            $durDocMinutes = null;
            if ($t2) {
                $baseStart = ($t1 && $t1 >= $t0) ? $t1 : $t0;
                if ($baseStart && $t2 >= $baseStart) {
                    $durDocMinutes = round(($t2 - $baseStart) / 60);
                    $totalDoctorDur += $durDocMinutes;
                    $countDoc++;
                }
            }

            // Durasi Pos 3: Kasir Utama (T3 - T2)
            $durCashierMinutes = null;
            if ($t3 && $t2 && $t3 >= $t2) {
                $durCashierMinutes = round(($t3 - $t2) / 60);
                $totalCashierDur += $durCashierMinutes;
                $countCashier++;
            }

            // Durasi Pos 4: Apotek / Serah Obat (T4 - T3 atau T4 - T2)
            $durPharmacyMinutes = null;
            if ($t4) {
                $baseKasir = $t3 ?: $t2;
                if ($baseKasir && $t4 >= $baseKasir) {
                    $durPharmacyMinutes = round(($t4 - $baseKasir) / 60);
                    $totalPharmacyDur += $durPharmacyMinutes;
                    $countPharmacy++;
                }
            }

            // Total Durasi Layanan (End-to-End: T_akhir - T0)
            $tEnd = $t4 ?: ($t3 ?: $t2);
            $totalMinutes = ($t0 && $tEnd && $tEnd >= $t0) ? round(($tEnd - $t0) / 60) : null;
            if ($totalMinutes !== null && ($sv->visit_status === 'completed' || $t4 || $t3)) {
                $totalOverallDur += $totalMinutes;
                $countOverall++;
            }

            // Efficiency Category
            $efficiencyBadge = 'badge-secondary';
            $efficiencyLabel = 'Sedang Berlangsung';
            if ($totalMinutes !== null) {
                if ($totalMinutes <= 30) {
                    $efficiencyBadge = 'badge-success';
                    $efficiencyLabel = 'Sangat Cepat (<30 mnt)';
                } elseif ($totalMinutes <= 60) {
                    $efficiencyBadge = 'badge-teal';
                    $efficiencyLabel = 'Standar (30-60 mnt)';
                } elseif ($totalMinutes <= 90) {
                    $efficiencyBadge = 'badge-warning';
                    $efficiencyLabel = 'Cukup Lama (60-90 mnt)';
                } else {
                    $efficiencyBadge = 'badge-danger';
                    $efficiencyLabel = 'Sangat Lama (>90 mnt)';
                }
            }

            $processedSla[] = (object)[
                'visit_id'          => $sv->visit_id,
                'no_visit'          => $sv->no_visit,
                'queue_no'          => $sv->queue_no ?: $sv->no_visit,
                'patient_name'      => $sv->patient_name,
                'no_rm'             => $sv->no_rm,
                'service_name'      => $sv->service_name,
                'doctor_name'       => $sv->doctor_name ?: 'Dokter Jaga',
                'visit_status'      => $sv->visit_status,
                'time_t0'           => $t0 ? date('H:i', $t0) : '-',
                'time_t1'           => $t1 ? date('H:i', $t1) : '-',
                'time_t2'           => $t2 ? date('H:i', $t2) : '-',
                'time_t3'           => $t3 ? date('H:i', $t3) : '-',
                'time_t4'           => $t4 ? date('H:i', $t4) : '-',
                'dur_ttv'           => $durTtvMinutes,
                'dur_doc'           => $durDocMinutes,
                'dur_cashier'       => $durCashierMinutes,
                'dur_pharmacy'      => $durPharmacyMinutes,
                'total_minutes'     => $totalMinutes,
                'efficiency_badge'  => $efficiencyBadge,
                'efficiency_label'  => $efficiencyLabel
            ];
        }

        $avgMetrics = [
            'avg_ttv'      => $countTtv > 0 ? round($totalWaitTtv / $countTtv, 1) : 0,
            'avg_doc'      => $countDoc > 0 ? round($totalDoctorDur / $countDoc, 1) : 0,
            'avg_cashier'  => $countCashier > 0 ? round($totalCashierDur / $countCashier, 1) : 0,
            'avg_pharmacy' => $countPharmacy > 0 ? round($totalPharmacyDur / $countPharmacy, 1) : 0,
            'avg_overall'  => $countOverall > 0 ? round($totalOverallDur / $countOverall, 1) : 0,
            'count_served' => $countOverall
        ];

        $data = [
            'title'         => 'Laporan & Statistik Pelayanan Klinik',
            'active_menu'   => 'laporan_klinik',
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'topIcd'        => $topIcd,
            'visitsByPoli'  => $visitsByPoli,
            'genderStats'   => $genderStats,
            'paymentStats'  => $paymentStats,
            'totalVisits'   => $totalVisits,
            'totalLetters'  => $totalLetters,
            'totalLabTests' => $totalLabTests,
            'slaVisits'     => $processedSla,
            'avgMetrics'    => $avgMetrics
        ];

        return view('klinik/laporan', $data);
    }

    public function cetakLaporan()
    {
        $db = \Config\Database::connect('default');

        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');
        $type      = $this->request->getGet('type') ?: 'morbiditas'; // 'morbiditas', 'kunjungan', 'waktu_pelayanan'

        // Top 10 Morbidity
        $topIcd = $db->table('medical_records')
                     ->select('medical_records.icd10_code, 
                               master_icd10.name_id, 
                               master_icd10.name_en, 
                               COUNT(medical_records.id) as total_cases')
                     ->join('master_icd10', 'master_icd10.code = medical_records.icd10_code', 'left')
                     ->join('patient_visits', 'patient_visits.id = medical_records.visit_id', 'left')
                     ->where('patient_visits.visit_date >=', $startDate)
                     ->where('patient_visits.visit_date <=', $endDate)
                     ->where('medical_records.icd10_code IS NOT NULL')
                     ->groupBy('medical_records.icd10_code')
                     ->orderBy('total_cases', 'DESC')
                     ->limit(10)
                     ->get()
                     ->getResult();

        // Visits Detailed List
        $visits = $db->table('patient_visits')
                     ->select('patient_visits.*, 
                               patients.name as patient_name, 
                               patients.no_rm, 
                               patients.gender, 
                               patients.date_of_birth,
                               COALESCE(polyclinics.name, "Tindakan Medis") as polyclinic_name,
                               doctors.name as doctor_name,
                                doctors.sip_number as doctor_sip,
                                doctors.digital_signature as doctor_signature,
                                doctors.stamp_image as doctor_stamp,
                               medical_records.icd10_code')
                     ->join('patients', 'patients.id = patient_visits.patient_id')
                     ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                     ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                     ->join('medical_records', 'medical_records.visit_id = patient_visits.id', 'left')
                     ->where('patient_visits.visit_date >=', $startDate)
                     ->where('patient_visits.visit_date <=', $endDate)
                     ->orderBy('patient_visits.visit_date', 'ASC')
                     ->get()
                     ->getResult();

        // SLA Visits List for Printing
        $slaVisits = $db->table('patient_visits pv')
                        ->select('pv.id as visit_id, pv.no_visit, pv.visit_date, pv.created_at as time_arrival, pv.status as visit_status,
                                  p.name as patient_name, p.no_rm,
                                  COALESCE(polikliniks.name, polyclinics.name, tindakan.name, "Poli Umum") as service_name,
                                  d.name as doctor_name,
                                  qn.queue_no,
                                  tr.created_at as time_triage,
                                  mr.created_at as time_doctor,
                                  bt.status as billing_status,
                                  bt.updated_at as time_cashier_raw,
                                  ct.created_at as time_cash_payment,
                                  pr.status as prescription_status,
                                  pr.dispensed_at as time_dispensed_raw,
                                  pv.updated_at as time_visit_updated')
                        ->join('patients p', 'p.id = pv.patient_id', 'left')
                        ->join('queue_numbers qn', 'qn.visit_id = pv.id', 'left')
                        ->join('polyclinics', 'polyclinics.id = pv.polyclinic_id', 'left')
                        ->join('polikliniks', 'polikliniks.id = pv.polyclinic_id', 'left')
                        ->join('tindakan', 'tindakan.id = pv.service_id', 'left')
                        ->join('doctors d', 'd.id = pv.doctor_id', 'left')
                        ->join('triage_records tr', 'tr.visit_id = pv.id', 'left')
                        ->join('medical_records mr', 'mr.visit_id = pv.id', 'left')
                        ->join('billing_transactions bt', 'bt.visit_id = pv.id', 'left')
                        ->join('cash_transactions ct', 'ct.billing_id = bt.id', 'left')
                        ->join('prescriptions pr', 'pr.visit_id = pv.id', 'left')
                        ->where('pv.visit_date >=', $startDate)
                        ->where('pv.visit_date <=', $endDate)
                        ->where('pv.status !=', 'cancelled')
                        ->orderBy('pv.id', 'DESC')
                        ->get()
                        ->getResult();

        $processedSla = [];
        $totalWaitTtv = 0; $countTtv = 0;
        $totalDoctorDur = 0; $countDoc = 0;
        $totalCashierDur = 0; $countCashier = 0;
        $totalPharmacyDur = 0; $countPharmacy = 0;
        $totalOverallDur = 0; $countOverall = 0;

        foreach ($slaVisits as $sv) {
            $t0 = !empty($sv->time_arrival) ? strtotime($sv->time_arrival) : null;
            $t1 = !empty($sv->time_triage) ? strtotime($sv->time_triage) : null;
            $t2 = !empty($sv->time_doctor) ? strtotime($sv->time_doctor) : null;
            
            $timeCashier = null;
            if (!empty($sv->time_cash_payment)) {
                $timeCashier = strtotime($sv->time_cash_payment);
            } elseif ($sv->billing_status === 'paid' && !empty($sv->time_cashier_raw)) {
                $timeCashier = strtotime($sv->time_cashier_raw);
            }
            $t3 = $timeCashier;

            $timeDispensed = null;
            if (!empty($sv->time_dispensed_raw)) {
                $timeDispensed = strtotime($sv->time_dispensed_raw);
            } elseif ($sv->prescription_status === 'completed' || $sv->visit_status === 'completed') {
                $timeDispensed = !empty($sv->time_visit_updated) ? strtotime($sv->time_visit_updated) : null;
            }
            $t4 = $timeDispensed;

            $durTtv = ($t0 && $t1 && $t1 >= $t0) ? round(($t1 - $t0) / 60) : null;
            if ($durTtv !== null) { $totalWaitTtv += $durTtv; $countTtv++; }

            $durDoc = null;
            if ($t2) {
                $baseStart = ($t1 && $t1 >= $t0) ? $t1 : $t0;
                if ($baseStart && $t2 >= $baseStart) {
                    $durDoc = round(($t2 - $baseStart) / 60);
                    $totalDoctorDur += $durDoc; $countDoc++;
                }
            }

            $durCashier = null;
            if ($t3 && $t2 && $t3 >= $t2) {
                $durCashier = round(($t3 - $t2) / 60);
                $totalCashierDur += $durCashier; $countCashier++;
            }

            $durPharmacy = null;
            if ($t4) {
                $baseKasir = $t3 ?: $t2;
                if ($baseKasir && $t4 >= $baseKasir) {
                    $durPharmacy = round(($t4 - $baseKasir) / 60);
                    $totalPharmacyDur += $durPharmacy; $countPharmacy++;
                }
            }

            $tEnd = $t4 ?: ($t3 ?: $t2);
            $totalMinutes = ($t0 && $tEnd && $tEnd >= $t0) ? round(($tEnd - $t0) / 60) : null;
            if ($totalMinutes !== null && ($sv->visit_status === 'completed' || $t4 || $t3)) {
                $totalOverallDur += $totalMinutes; $countOverall++;
            }

            $processedSla[] = (object)[
                'no_visit'          => $sv->no_visit,
                'queue_no'          => $sv->queue_no ?: $sv->no_visit,
                'patient_name'      => $sv->patient_name,
                'no_rm'             => $sv->no_rm,
                'service_name'      => $sv->service_name,
                'doctor_name'       => $sv->doctor_name ?: 'Dokter Jaga',
                'time_t0'           => $t0 ? date('H:i', $t0) : '-',
                'time_t1'           => $t1 ? date('H:i', $t1) : '-',
                'time_t2'           => $t2 ? date('H:i', $t2) : '-',
                'time_t3'           => $t3 ? date('H:i', $t3) : '-',
                'time_t4'           => $t4 ? date('H:i', $t4) : '-',
                'dur_ttv'           => $durTtv,
                'dur_doc'           => $durDoc,
                'dur_cashier'       => $durCashier,
                'dur_pharmacy'      => $durPharmacy,
                'total_minutes'     => $totalMinutes
            ];
        }

        $avgMetrics = [
            'avg_ttv'      => $countTtv > 0 ? round($totalWaitTtv / $countTtv, 1) : 0,
            'avg_doc'      => $countDoc > 0 ? round($totalDoctorDur / $countDoc, 1) : 0,
            'avg_cashier'  => $countCashier > 0 ? round($totalCashierDur / $countCashier, 1) : 0,
            'avg_pharmacy' => $countPharmacy > 0 ? round($totalPharmacyDur / $countPharmacy, 1) : 0,
            'avg_overall'  => $countOverall > 0 ? round($totalOverallDur / $countOverall, 1) : 0,
            'count_served' => $countOverall
        ];

        $data = [
            'title'      => 'Laporan Pelayanan Klinik Sawamawa Medical Center',
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'type'       => $type,
            'topIcd'     => $topIcd,
            'visits'     => $visits,
            'slaVisits'  => $processedSla,
            'avgMetrics' => $avgMetrics
        ];

        return view('klinik/cetak_laporan', $data);
    }

    // =========================================================================
    // MODUL ODONTOGRAM INTERAKTIF (POLI GIGI & MULUT)
    // =========================================================================
    public function saveOdontogram()
    {
        $db = \Config\Database::connect('default');
        
        $patientId = $this->request->getPost('patient_id');
        $visitId   = $this->request->getPost('visit_id') ?: null;
        $teethData = $this->request->getPost('teeth'); // array of ['tooth' => 11, 'code' => 'caries', 'name' => 'Karies', 'notes' => '...']

        if (empty($patientId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Pasien tidak valid.']);
        }

        if (is_array($teethData)) {
            foreach ($teethData as $t) {
                $toothNo = $t['tooth'] ?? '';
                $code    = $t['code'] ?? 'normal';
                $name    = $t['name'] ?? 'Normal';
                $notes   = $t['notes'] ?? '';

                if (!empty($toothNo)) {
                    $existing = $db->table('odontograms')
                                   ->where('patient_id', $patientId)
                                   ->where('tooth_number', $toothNo)
                                   ->get()
                                   ->getRow();

                    if ($existing) {
                        $db->table('odontograms')
                           ->where('id', $existing->id)
                           ->update([
                               'visit_id'       => $visitId,
                               'condition_code' => $code,
                               'condition_name' => $name,
                               'notes'          => $notes,
                               'updated_at'     => date('Y-m-d H:i:s')
                           ]);
                    } else {
                        $db->table('odontograms')->insert([
                            'visit_id'       => $visitId,
                            'patient_id'     => $patientId,
                            'tooth_number'   => $toothNo,
                            'condition_code' => $code,
                            'condition_name' => $name,
                            'notes'          => $notes,
                            'created_at'     => date('Y-m-d H:i:s'),
                            'updated_at'     => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Odontogram berhasil diperbarui.']);
    }

    public function getOdontogramJson($patientId)
    {
        $db = \Config\Database::connect('default');
        $records = $db->table('odontograms')
                      ->where('patient_id', $patientId)
                      ->get()
                      ->getResult();

        $map = [];
        foreach ($records as $r) {
            $map[$r->tooth_number] = [
                'code'  => $r->condition_code,
                'name'  => $r->condition_name,
                'notes' => $r->notes
            ];
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $map]);
    }

    /**
     * Endpoint Hapus / Batalkan Antrean Langsung
     * GET klinik/antrean/delete/(:num)
     */
    public function deleteQueueDirect($queueId)
    {
        return $this->processDeleteQueue((int)$queueId);
    }

    /**
     * Helper Eksekusi Penghapusan Antrean & Relasi Terkait (Cascading Delete)
     */
    private function processDeleteQueue(int $queueId)
    {
        $db = \Config\Database::connect('default');
        
        // Cari data antrean beserta metadata pasien & kunjungan
        $queue = $db->table('queue_numbers')
                    ->select('queue_numbers.*, patients.name as patient_name, patient_visits.no_visit, COALESCE(polikliniks.name, polyclinics.name) as poly_name')
                    ->join('patient_visits', 'patient_visits.id = queue_numbers.visit_id', 'left')
                    ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                    ->join('polyclinics', 'polyclinics.id = queue_numbers.polyclinic_id', 'left')
                    ->join('polikliniks', 'polikliniks.id = patient_visits.polyclinic_id', 'left')
                    ->where('queue_numbers.id', $queueId)
                    ->orWhere('queue_numbers.visit_id', $queueId)
                    ->get()
                    ->getRow();

        $patientName = '';
        $noVisit = '';
        $polyName = '';

        if (!$queue) {
            // Cek apakah langsung merujuk ke patient_visits.id
            $visit = $db->table('patient_visits')
                        ->select('patient_visits.*, patients.name as patient_name')
                        ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                        ->where('patient_visits.id', $queueId)
                        ->get()
                        ->getRow();
            if ($visit) {
                $vId = (int)$visit->id;
                $patientName = $visit->patient_name ?? 'Pasien';
                $noVisit = $visit->no_visit ?? '';

                $db->query("SET FOREIGN_KEY_CHECKS = 0;");
                $db->query("DELETE FROM cash_transactions WHERE billing_id IN (SELECT id FROM billing_transactions WHERE visit_id = {$vId})");
                $db->table('fee_transactions')->where('visit_id', $vId)->delete();
                $db->table('billing_details')->where("billing_id IN (SELECT id FROM billing_transactions WHERE visit_id = {$vId})")->delete();
                $db->table('billing_transactions')->where('visit_id', $vId)->delete();
                $db->table('lab_results')->where('visit_id', $vId)->delete();
                $db->table('medical_letters')->where('visit_id', $vId)->delete();
                $db->table('medical_records')->where('visit_id', $vId)->delete();
                $db->table('triage_records')->where('visit_id', $vId)->delete();
                $db->table('prescription_details')->where("prescription_id IN (SELECT id FROM prescriptions WHERE visit_id = {$vId})")->delete();
                $db->table('prescriptions')->where('visit_id', $vId)->delete();
                $db->table('queue_numbers')->where('visit_id', $vId)->delete();
                $db->table('patient_visits')->where('id', $vId)->delete();
                $db->query("SET FOREIGN_KEY_CHECKS = 1;");

                try {
                    $notif = new \App\Services\NotificationService();
                    $notif->send([
                        'notification_key' => 'visit_deleted_' . $vId . '_' . time(),
                        'category'         => 'klinik',
                        'type'             => 'danger',
                        'badge'            => 'Batal Antrean',
                        'icon'             => 'fas fa-user-xmark',
                        'title'            => 'Kunjungan Dibatalkan: ' . $patientName,
                        'message'          => "Kunjungan pasien {$patientName} ({$noVisit}) telah dibatalkan dan dihapus dari antrean klinik.",
                        'link'             => base_url('klinik/antrean'),
                        'target_roles'     => null,
                        'sender_name'      => 'Pelayanan Klinik'
                    ]);
                } catch (\Throwable $e) {}

                if ($this->request->isAJAX() || $this->request->getPost('is_ajax') === '1') {
                    return $this->response->setJSON([
                        'status'  => 'success',
                        'message' => 'Antrean kunjungan pasien (' . $patientName . ') berhasil dibatalkan dan dihapus.'
                    ]);
                }

                session()->setFlashdata('success', 'Antrean kunjungan pasien (' . $patientName . ') berhasil dibatalkan dan dihapus.');
                return redirect()->to(base_url('klinik/pendaftaran'));
            }

            if ($this->request->isAJAX() || $this->request->getPost('is_ajax') === '1') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data antrean tidak ditemukan.'
                ]);
            }

            session()->setFlashdata('error', 'Data antrean tidak ditemukan.');
            return redirect()->to(base_url('klinik/pendaftaran'));
        }

        $realQueueId = (int)$queue->id;
        $visitId     = !empty($queue->visit_id) ? (int)$queue->visit_id : 0;
        $queueNo     = $queue->queue_no ?? "#{$realQueueId}";
        $patientName = $queue->patient_name ?? '';
        $noVisit     = $queue->no_visit ?? '';

        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        if ($visitId > 0) {
            $db->query("DELETE FROM cash_transactions WHERE billing_id IN (SELECT id FROM billing_transactions WHERE visit_id = {$visitId})");
            $db->table('fee_transactions')->where('visit_id', $visitId)->delete();
            $db->table('billing_details')->where("billing_id IN (SELECT id FROM billing_transactions WHERE visit_id = {$visitId})")->delete();
            $db->table('billing_transactions')->where('visit_id', $visitId)->delete();
            $db->table('lab_results')->where('visit_id', $visitId)->delete();
            $db->table('medical_letters')->where('visit_id', $visitId)->delete();
            $db->table('medical_records')->where('visit_id', $visitId)->delete();
            $db->table('triage_records')->where('visit_id', $visitId)->delete();
            $db->table('prescription_details')->where("prescription_id IN (SELECT id FROM prescriptions WHERE visit_id = {$visitId})")->delete();
            $db->table('prescriptions')->where('visit_id', $visitId)->delete();
            $db->table('queue_numbers')->where('visit_id', $visitId)->delete();
            $db->table('patient_visits')->where('id', $visitId)->delete();
        }
        $db->table('queue_numbers')->where('id', $realQueueId)->delete();
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        try {
            $notif = new \App\Services\NotificationService();
            $notif->send([
                'notification_key' => 'queue_deleted_' . $realQueueId . '_' . time(),
                'category'         => 'klinik',
                'type'             => 'danger',
                'badge'            => 'Batal Antrean',
                'icon'             => 'fas fa-user-xmark',
                'title'            => 'Antrean ' . $queueNo . ' Dibatalkan' . ($patientName ? ' (' . $patientName . ')' : ''),
                'message'          => "Antrean pasien " . ($patientName ?: $queueNo) . " (No. " . $queueNo . ") telah dibatalkan dan dikeluarkan dari sistem antrean & SOAP.",
                'link'             => base_url('klinik/pendaftaran'),
                'target_roles'     => null,
                'sender_name'      => 'Pelayanan Klinik'
            ]);
        } catch (\Throwable $e) {}

        if ($this->request->isAJAX() || $this->request->getPost('is_ajax') === '1') {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Antrean kunjungan pasien (' . ($patientName ?: $queueNo) . ') berhasil dibatalkan dan dihapus.'
            ]);
        }

        session()->setFlashdata('success', 'Antrean kunjungan pasien (' . ($patientName ?: $queueNo) . ') berhasil dibatalkan dan dihapus.');
        return redirect()->to(base_url('klinik/pendaftaran'));
    }

    /**
     * Quick toggle status pendaftaran online (Buka / Tutup instan oleh Petugas Pelayanan)
     */
    public function toggleOnlineRegistration()
    {
        $db = \Config\Database::connect('default');
        $newStatus = $this->request->getPost('status') === 'true' ? 'true' : 'false';

        $exists = $db->table('system_settings')->where('setting_key', 'online_registration_active')->countAllResults();
        if ($exists > 0) {
            $db->table('system_settings')->where('setting_key', 'online_registration_active')->update(['setting_value' => $newStatus]);
        } else {
            $db->table('system_settings')->insert([
                'setting_group' => 'online_reg',
                'setting_key'   => 'online_registration_active',
                'setting_value' => $newStatus,
                'description'   => 'Status Pendaftaran Online'
            ]);
        }

        $statusText = $newStatus === 'true' ? 'DIBUKA KEMBALI' : 'DITUTUP SEMENTARA';
        session()->setFlashdata('success', "Status pendaftaran online publik berhasil {$statusText}.");

        return redirect()->back();
    }

    /**
     * Universal macOS Spotlight Global Search (Pasien & Fitur Sistem)
     * POST api/spotlight-search
     */
    public function spotlightSearch()
    {
        $db = \Config\Database::connect('default');
        $rawKeyword = trim((string)($this->request->getPost('keyword') ?: $this->request->getGet('keyword') ?: ($this->request->getJSON(true)['keyword'] ?? '')));

        if (empty($rawKeyword) || strlen($rawKeyword) < 2) {
            return $this->response->setJSON([
                'status'   => 'success',
                'patients' => [],
                'menus'    => []
            ]);
        }

        // 1. Parsing jika hasil scan QR berformat: SAWAMAWA|RM-000001|Nama|NIK
        $keyword = $rawKeyword;
        if (str_contains($rawKeyword, '|')) {
            $parts = explode('|', $rawKeyword);
            $keyword = trim($parts[1] ?? $parts[0]);
        }

        $cleanDigits = preg_replace('/[^0-9]/', '', $keyword);
        $possibleRms = [$keyword];
        if (!empty($cleanDigits) && strlen($cleanDigits) <= 6) {
            $possibleRms[] = 'RM-' . str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
            $possibleRms[] = 'RM' . str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
            $possibleRms[] = str_pad($cleanDigits, 6, '0', STR_PAD_LEFT);
        }

        // Cari Pasien (Exact Match prioritized, then LIKE Match)
        $patients = $db->table('patients')
                       ->groupStart()
                           ->whereIn('no_rm', $possibleRms)
                           ->orLike('name', $keyword)
                           ->orLike('no_rm', $keyword)
                           ->orLike('nik', $keyword)
                           ->orLike('phone', $keyword)
                       ->groupEnd()
                       ->limit(8)
                       ->get()
                       ->getResult();

        $patientResults = [];
        foreach ($patients as $p) {
            $dob = $p->date_of_birth ? date('d/m/Y', strtotime($p->date_of_birth)) : '-';
            $age = $p->date_of_birth ? (date('Y') - date('Y', strtotime($p->date_of_birth))) : 0;
            $patientResults[] = [
                'id'       => $p->id,
                'no_rm'    => $p->no_rm,
                'name'     => $p->name,
                'nik'      => $p->nik ?: '-',
                'gender'   => strtoupper($p->gender ?? 'L'),
                'dob'      => $dob,
                'age'      => $age,
                'phone'    => $p->phone ?: '-',
                'address'  => $p->address ?: '-',
                'soap_url' => base_url('klinik/soap?patient_id=' . $p->id),
                'pendaftaran_url' => base_url('klinik/pendaftaran?search=' . urlencode($p->no_rm)),
                'resep_url' => base_url('apotek/resep?patient_id=' . $p->id),
                'kasir_url' => base_url('keuangan/kasir?patient_id=' . $p->id),
                'kartu_url' => base_url('klinik/kartu-pasien/' . $p->id)
            ];
        }

        // 2. Cari Navigasi Menu / Fitur Sistem
        $allMenus = [
            ['title' => 'Pendaftaran & Antrean Pasien', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/pendaftaran'), 'icon' => 'fa-user-plus', 'tags' => 'daftar antrean registrasi pasien baru rujukan'],
            ['title' => 'RME SOAP & Rekam Medis', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/soap'), 'icon' => 'fa-notes-medical', 'tags' => 'soap rme rekam medis diagnosa icd anamnesa resep dokter'],
            ['title' => 'Pemeriksaan Laboratorium', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/lab'), 'icon' => 'fa-flask', 'tags' => 'lab darah urine tes laboratorium specimen'],
            ['title' => 'Surat Sakit & Rujukan', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/surat'), 'icon' => 'fa-file-medical', 'tags' => 'surat sehat surat sakit rujukan rs'],
            ['title' => 'Pelayanan E-Resep Obat', 'module' => 'Farmasi & Apotek', 'url' => base_url('apotek/resep'), 'icon' => 'fa-file-prescription', 'tags' => 'resep racikan obat farmasi apotek dispensing'],
            ['title' => 'Kasir Apotek (OTC Bebas)', 'module' => 'Farmasi & Apotek', 'url' => base_url('apotek/penjualan'), 'icon' => 'fa-cart-shopping', 'tags' => 'kasir obat bebas otc apotek penjualan langsung nota'],
            ['title' => 'Stok Obat & FEFO', 'module' => 'Farmasi & Apotek', 'url' => base_url('apotek/stok'), 'icon' => 'fa-pills', 'tags' => 'stok obat fefo exp expired batch harga obat'],
            ['title' => 'Gudang & Mutasi Obat', 'module' => 'Farmasi & Apotek', 'url' => base_url('apotek/gudang'), 'icon' => 'fa-warehouse', 'tags' => 'gudang mutasi transfer obat bhp'],
            ['title' => 'Kasir Pembayaran Pasien', 'module' => 'Keuangan & Kasir', 'url' => base_url('keuangan/kasir'), 'icon' => 'fa-cash-register', 'tags' => 'kasir bayar billing invoice kwitansi rawat jalan'],
            ['title' => 'Rekap Kasir & Tutup Shift', 'module' => 'Keuangan & Kasir', 'url' => base_url('keuangan/shift'), 'icon' => 'fa-receipt', 'tags' => 'shift tutup kas rekap uang kasir'],
            ['title' => 'Rekapitulasi Fee Dokter', 'module' => 'Keuangan & Kasir', 'url' => base_url('keuangan/fee-dokter'), 'icon' => 'fa-user-doctor', 'tags' => 'fee dokter jasa medis komisi bagi hasil'],
            ['title' => 'Jurnal Umum Transaksi', 'module' => 'Akuntansi', 'url' => base_url('accounting/jurnal'), 'icon' => 'fa-book', 'tags' => 'jurnal umum debet kredit memorial transaksi akuntansi'],
            ['title' => 'Buku Mutasi Akun (Buku Besar)', 'module' => 'Akuntansi', 'url' => base_url('accounting/buku-besar'), 'icon' => 'fa-book-open', 'tags' => 'buku besar ledger mutasi rekening coa'],
            ['title' => 'Laporan Keuangan', 'module' => 'Akuntansi', 'url' => base_url('accounting/laporan'), 'icon' => 'fa-chart-line', 'tags' => 'laba rugi neraca arus kas financial report'],
            ['title' => 'Bagan Akun (COA)', 'module' => 'Akuntansi', 'url' => base_url('accounting/coa'), 'icon' => 'fa-book-bookmark', 'tags' => 'coa bagan akun kode akun rekening aktiva pasiva modal pendapatan beban'],
            ['title' => 'Template & Aturan Jurnal', 'module' => 'Akuntansi', 'url' => base_url('accounting/aturan-jurnal'), 'icon' => 'fa-sliders', 'tags' => 'aturan jurnal template otomatisasi mapping pos'],
            ['title' => 'Master Referensi Klinik', 'module' => 'Sistem & Pengaturan', 'url' => base_url('system/master-klinik'), 'icon' => 'fa-hospital-user', 'tags' => 'master klinik dokter tarif jadwal ruangan tindakan poli'],
            ['title' => 'Katalog Diagnosa ICD-10', 'module' => 'Sistem & Pengaturan', 'url' => base_url('system/master-icd'), 'icon' => 'fa-book-medical', 'tags' => 'icd10 kode diagnosa penyakit'],
            ['title' => 'Pengaturan Dasar Klinik & Branding', 'module' => 'Sistem & Pengaturan', 'url' => base_url('system/settings'), 'icon' => 'fa-sliders', 'tags' => 'pengaturan sistem logo stempel faskes whatsapp display tv'],
            ['title' => 'Pengguna & Hak Akses (RBAC)', 'module' => 'Sistem & Pengaturan', 'url' => base_url('system/users'), 'icon' => 'fa-users-gear', 'tags' => 'pengguna user role akses rbac password'],
            ['title' => 'Layar Antrean TV Display', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/display'), 'icon' => 'fa-tv', 'tags' => 'tv display antrean layar panggil'],
            ['title' => 'Mesin Kiosk APM Mandiri', 'module' => 'Pelayanan Medis', 'url' => base_url('klinik/kiosk'), 'icon' => 'fa-desktop', 'tags' => 'kiosk apm anjungan mandiri']
        ];

        $matchedMenus = [];
        $lowKey = strtolower($keyword);
        foreach ($allMenus as $m) {
            if (str_contains(strtolower($m['title']), $lowKey) || str_contains(strtolower($m['module']), $lowKey) || str_contains(strtolower($m['tags']), $lowKey)) {
                $matchedMenus[] = $m;
            }
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'patients' => $patientResults,
            'menus'    => array_slice($matchedMenus, 0, 6)
        ]);
    }

    /**
     * Sinkronisasi Manual SATUSEHAT Kemenkes RI (HL7 FHIR R4)
     * Meliputi: Encounter + Condition (ICD-10) + Observation (TTV Vital Signs)
     */
    public function syncSatuSehat($visitId)
    {
        try {
            $satuSehat = new \App\Services\SatuSehatService();
            $result = $satuSehat->syncAll($visitId);

            return $this->response->setJSON($result);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memproses sinkronisasi SATUSEHAT: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ambil Riwayat Log Audit Bridging SATUSEHAT untuk Kunjungan Pasien
     */
    public function getSatuSehatLogs($visitId)
    {
        try {
            $db = \Config\Database::connect('default');
            $visit = $db->table('patient_visits')
                        ->select('patient_visits.*, patients.name as patient_name, patients.nik, patients.no_rm, patients.satusehat_ihs_id')
                        ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                        ->where('patient_visits.id', $visitId)
                        ->get()
                        ->getRow();

            $satuSehat = new \App\Services\SatuSehatService();
            $logs = $satuSehat->getVisitLogs($visitId);

            return $this->response->setJSON([
                'status' => 'success',
                'visit'  => $visit,
                'logs'   => $logs
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal mengambil log audit SATUSEHAT: ' . $e->getMessage(),
                'logs'    => []
            ]);
        }
    }
}


