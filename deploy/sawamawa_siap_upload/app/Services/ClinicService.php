<?php

namespace App\Services;

use App\Services\FinanceService;

class ClinicService
{
    protected $db;
    protected $tindakanPolyId;

    public function __construct()
    {
        $this->db = \Config\Database::connect('default');

        // 1. Ensure dynamic database column adaptations exist
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("ALTER TABLE patient_visits MODIFY COLUMN doctor_id INT NULL;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");

        $fields = $this->db->getFieldNames('patient_visits');
        if (!in_array('visit_type', $fields)) {
            $this->db->query("ALTER TABLE patient_visits ADD COLUMN visit_type ENUM('poli', 'tindakan') DEFAULT 'poli' AFTER status;");
        }
        if (!in_array('service_id', $fields)) {
            $this->db->query("ALTER TABLE patient_visits ADD COLUMN service_id INT NULL AFTER visit_type;");
        }

        // Alter services table for parent-child hierarchy
        $fieldsServices = $this->db->getFieldNames('services');
        if (!in_array('parent_id', $fieldsServices)) {
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
            $this->db->query("ALTER TABLE services ADD COLUMN parent_id INT NULL AFTER category;");
            $this->db->query("ALTER TABLE services ADD CONSTRAINT fk_services_parent FOREIGN KEY (parent_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE;");
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
            
            // Seed parent categories Hairstudio and Perawatan Estetik for demonstration
            $this->db->table('services')->insertBatch([
                ['id' => 6, 'code' => 'CAT-001', 'name' => 'Hairstudio', 'category' => 'tindakan', 'parent_id' => null, 'status' => 'active'],
                ['id' => 7, 'code' => 'CAT-002', 'name' => 'Perawatan Estetik', 'category' => 'tindakan', 'parent_id' => null, 'status' => 'active']
            ]);
            // Link existing actions (Rambut Rontok and Facial Treatment) to their new parents
            $this->db->table('services')->where('id', 4)->update(['parent_id' => 6]); // Rambut Rontok -> Hairstudio
            $this->db->table('services')->where('id', 5)->update(['parent_id' => 7]); // Facial -> Perawatan Estetik
        }

        // 2. Automatically check/seed a virtual "Tindakan Saja" polyclinic to preserve foreign keys
        $polyTindakan = $this->db->table('polyclinics')->where('name', 'Tindakan Saja')->get()->getRow();
        if (!$polyTindakan) {
            $this->db->table('polyclinics')->insert([
                'name'        => 'Tindakan Saja',
                'description' => 'Poli khusus untuk pelayanan tindakan medis langsung',
                'status'      => 'active'
            ]);
            $this->tindakanPolyId = $this->db->insertID();
        } else {
            $this->tindakanPolyId = $polyTindakan->id;
        }
    }

    public function registerPatient(array $data)
    {
        try {
            $builder = $this->db->table('patients');

            // Sanitize & fallback NIK
            $nik = trim($data['nik'] ?? '');
            if (empty($nik)) {
                $nik = '3201' . date('ymd') . rand(1000, 9999);
            }
            $data['nik'] = $nik;

            // Check if NIK already exists
            $existing = $builder->where('nik', $data['nik'])->get()->getRow();
            if ($existing) {
                return [
                    'status' => 'error',
                    'message' => 'Pasien dengan NIK (' . esc($data['nik']) . ') sudah terdaftar atas nama: ' . esc($existing->name) . ' (No RM: ' . esc($existing->no_rm) . ').'
                ];
            }

            // Fallback for mandatory fields
            $data['name']           = trim($data['name'] ?? 'Pasien Baru');
            $data['gender']         = in_array($data['gender'] ?? 'L', ['L', 'P']) ? $data['gender'] : 'L';
            $data['place_of_birth'] = !empty($data['place_of_birth']) ? trim($data['place_of_birth']) : 'Sumbawa';
            $data['date_of_birth']  = !empty($data['date_of_birth']) ? $data['date_of_birth'] : date('Y-m-d', strtotime('-25 years'));
            $data['phone']          = !empty($data['phone']) ? trim($data['phone']) : '-';
            $data['address']        = !empty($data['address']) ? trim($data['address']) : '-';

            // Generate No RM (RM-XXXXXX)
            $lastRM = $builder->orderBy('id', 'DESC')->limit(1)->get()->getRow();
            $nextNum = 1;
            if ($lastRM && preg_match('/RM-(\d+)/', $lastRM->no_rm ?? '', $matches)) {
                $nextNum = intval($matches[1]) + 1;
            }
            $noRm = 'RM-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            $data['no_rm'] = $noRm;

            $builder->insert($data);
            $patientId = $this->db->insertID();

            // Trigger Event Hook Pasien Terdaftar
            try {
                \CodeIgniter\Events\Events::trigger('patient.registered', $patientId, $data);
            } catch (\Throwable $e) {
                log_message('error', 'Event patient.registered error: ' . $e->getMessage());
            }

            return [
                'status' => 'success',
                'patient_id' => $patientId,
                'no_rm' => $noRm,
                'message' => 'Pendaftaran pasien baru berhasil.'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'Gagal mendaftarkan pasien: ' . $e->getMessage()
            ];
        }
    }

    public function createVisit($patientId, $polyclinicId, $doctorId, $paymentMethod = 'umum', $visitType = 'poli', $serviceId = null, $roomId = null, $insuranceId = null, $complaint = null)
    {
        // Pastikan fallback aman untuk payment_method
        $paymentMethod = (!empty($paymentMethod) && is_string($paymentMethod)) ? strtolower(trim($paymentMethod)) : 'umum';
        if ($paymentMethod === 'tunai' || $paymentMethod === 'cash') {
            $paymentMethod = 'umum';
        }

        try {
            $this->db->transStart();

            $today = date('Ymd');
            
            // 1. Generate No Visit (VS-YYYYMMDD-XXXX) with loop check
            $visitPrefix = 'VS-' . $today . '-';
            $lastVisit = $this->db->table('patient_visits')
                                  ->where("no_visit LIKE '{$visitPrefix}%'")
                                  ->orderBy('id', 'DESC')
                                  ->limit(1)
                                  ->get()
                                  ->getRow();
            $nextNum = 1;
            if ($lastVisit && preg_match('/VS-\d+-(\d+)/', $lastVisit->no_visit, $matches)) {
                $nextNum = intval($matches[1]) + 1;
            }
            $noVisit = $visitPrefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            while ($this->db->table('patient_visits')->where('no_visit', $noVisit)->countAllResults() > 0) {
                $nextNum++;
                $noVisit = $visitPrefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            }

            // 2. Resolve Polyclinic target ID & Room ID
            $polyIdToInsert = $visitType === 'poli' ? $polyclinicId : $this->tindakanPolyId;

            // Auto-resolve room jika belum ditentukan
            if (!$roomId) {
                $roomQuery = $visitType === 'poli' ? 'poli' : 'tindakan';
                $matchedRoom = $this->db->table('rooms')->where('type', $roomQuery)->where('status', 'active')->get()->getRow();
                if ($matchedRoom) {
                    $roomId = $matchedRoom->id;
                }
            }

            // 3. Insert Patient Visit
            $visitData = [
                'no_visit'        => $noVisit,
                'patient_id'      => $patientId,
                'polyclinic_id'   => $polyIdToInsert,
                'doctor_id'       => $doctorId ?: null,
                'room_id'         => $roomId ?: null,
                'payment_method'  => $paymentMethod,
                'insurance_id'    => $insuranceId ?: null,
                'status'          => 'waiting',
                'visit_date'      => date('Y-m-d'),
                'visit_type'      => $visitType,
                'service_id'      => $visitType === 'tindakan' ? $serviceId : null
            ];
            $this->db->table('patient_visits')->insert($visitData);
            $visitId = $this->db->insertID();

            if (!$visitId) {
                throw new \Exception('Gagal memperoleh ID Kunjungan Pasien.');
            }

            // 4. Generate Queue Number Berdasarkan Golongan Pasien (VIP, Gold Priority, atau Reguler)
            $patient = $this->db->table('patients')->where('id', $patientId)->get()->getRow();
            $tier = strtolower($patient->membership_tier ?? 'regular');

            if ($tier === 'vip' || $tier === 'platinum') {
                $queuePrefix = ($visitType === 'poli') ? 'VIP' : 'TV';
            } elseif ($tier === 'gold') {
                $queuePrefix = ($visitType === 'poli') ? 'G' : 'TG';
            } else {
                $queuePrefix = ($visitType === 'poli') ? 'A' : 'T';
            }

            $lastQueue = $this->db->table('queue_numbers')
                                  ->where("queue_no LIKE '{$queuePrefix}-%'")
                                  ->where('DATE(created_at)', date('Y-m-d'))
                                  ->orderBy('id', 'DESC')
                                  ->limit(1)
                                  ->get()
                                  ->getRow();
            $nextQueueNum = 1;
            if ($lastQueue && preg_match('/[A-Za-z]+-(\d+)/', $lastQueue->queue_no, $matches)) {
                $nextQueueNum = intval($matches[1]) + 1;
            }
            $queueNo = $queuePrefix . '-' . str_pad($nextQueueNum, 3, '0', STR_PAD_LEFT);
            while ($this->db->table('queue_numbers')->where('queue_no', $queueNo)->where('DATE(created_at)', date('Y-m-d'))->countAllResults() > 0) {
                $nextQueueNum++;
                $queueNo = $queuePrefix . '-' . str_pad($nextQueueNum, 3, '0', STR_PAD_LEFT);
            }

            $queueData = [
                'polyclinic_id' => $polyIdToInsert,
                'queue_no'      => $queueNo,
                'visit_id'      => $visitId,
                'status'        => 'waiting'
            ];
            $this->db->table('queue_numbers')->insert($queueData);

            // 5. Pre-create Billing transaction Draft with unique billing_no check
            $billPrefix = 'BIL-' . $today . '-';
            $lastBilling = $this->db->table('billing_transactions')
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
            while ($this->db->table('billing_transactions')->where('billing_no', $billingNo)->countAllResults() > 0) {
                $nextBillNum++;
                $billingNo = $billPrefix . str_pad($nextBillNum, 4, '0', STR_PAD_LEFT);
            }

            $this->db->table('billing_transactions')->insert([
                'billing_no'      => $billingNo,
                'visit_id'        => $visitId,
                'total_services'  => 0.00,
                'total_medicines' => 0.00,
                'total_restaurant'=> 0.00,
                'discount'        => 0.00,
                'grand_total'     => 0.00,
                'status'          => 'draft'
            ]);
            $billingId = $this->db->insertID();

            // 6. If it's a "Tindakan Saja", automatically append the action price to billing details
            if ($visitType === 'tindakan' && $serviceId) {
                $service = $this->db->table('tindakan')
                                    ->where('id', $serviceId)
                                    ->get()
                                    ->getRow();
                
                if ($service && $service->price > 0) {
                    $this->db->table('billing_details')->insert([
                        'billing_id' => $billingId,
                        'item_type'  => 'medis',
                        'item_name'  => $service->name,
                        'qty'        => 1,
                        'price'      => $service->price,
                        'subtotal'   => $service->price
                    ]);
                    
                    $this->db->table('billing_transactions')
                             ->where('id', $billingId)
                             ->update([
                                 'total_services' => $service->price,
                                 'grand_total'    => $service->price
                             ]);
                }
            }

            // 7. Simpan Keluhan Utama / Catatan Awal Kunjungan ke triage_records jika ada
            if (!empty($complaint)) {
                $this->db->table('triage_records')->insert([
                    'visit_id'    => $visitId,
                    'complaints'  => trim($complaint),
                    'nurse_notes' => 'Keluhan awal dicatat pada saat registrasi kunjungan admisi.'
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $err = $this->db->error();
                return [
                    'status' => 'error',
                    'message' => 'Gagal membuat kunjungan pasien: ' . ($err['message'] ?? 'Kesalahan pada database.')
                ];
            }

            // Resolve context names for ticket printing & notification
            $doctorRow = $doctorId ? $this->db->table('doctors')->where('id', $doctorId)->get()->getRow() : null;
            $doctorName = $doctorRow ? $doctorRow->name : 'Dokter Pemeriksa / Jaga';

            $polyName = 'Poliklinik';
            if ($visitType === 'tindakan') {
                $srv = $serviceId ? $this->db->table('tindakan')->where('id', $serviceId)->get()->getRow() : null;
                $polyName = $srv ? $srv->name : 'Tindakan Medis Langsung';
            } else {
                $polyRow = $this->db->table('polikliniks')->where('id', $polyIdToInsert)->get()->getRow();
                if (!$polyRow) {
                    $polyRow = $this->db->table('polyclinics')->where('id', $polyIdToInsert)->get()->getRow();
                }
                if ($polyRow) {
                    $polyName = $polyRow->name;
                }
            }

            $roomName = '-';
            if ($roomId) {
                $rRow = $this->db->table('rooms')->where('id', $roomId)->get()->getRow();
                if ($rRow) {
                    $roomName = $rRow->name;
                }
            }

            // Push Notifikasi Operasional Real-Time ke Semua Browser & Perangkat
            try {
                $patientName = $patient ? $patient->name : 'Pasien';
                $patientRm   = $patient ? $patient->no_rm : '-';

                $notif = new \App\Services\NotificationService();
                $notif->send([
                    'type'       => 'queue',
                    'title'      => 'Antrean Pasien Baru',
                    'message'    => "Pasien {$patientName} ({$patientRm}) telah didaftarkan ke antrean {$queueNo} ({$polyName}).",
                    'icon'       => 'fas fa-ticket-alt',
                    'url'        => base_url('klinik/pendaftaran'),
                    'target_role'=> 'dokter,perawat,kasir,admin'
                ]);
            } catch (\Exception $ne) {
                // Ignore background notification error
            }

            return [
                'status'         => 'success',
                'visit_id'       => $visitId,
                'no_visit'       => $noVisit,
                'queue_no'       => $queueNo,
                'patient_id'     => $patientId,
                'patient_name'   => $patient ? $patient->name : 'Pasien',
                'patient_rm'     => $patient ? $patient->no_rm : '-',
                'patient_phone'  => $patient ? ($patient->phone ?? '') : '',
                'patient_nik'    => $patient ? ($patient->nik ?? '-') : '-',
                'doctor_name'    => $doctorName,
                'poly_name'      => $polyName,
                'room_name'      => $roomName,
                'payment_method' => $paymentMethod,
                'complaint'      => !empty($complaint) ? trim($complaint) : '-',
                'created_at'     => date('Y-m-d H:i:s'),
                'message'        => 'Kunjungan pasien berhasil didaftarkan.'
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return [
                'status' => 'error',
                'message' => 'Gagal membuat kunjungan: ' . $e->getMessage()
            ];
        }
    }

    public function saveSoap($visitId, array $soapData)
    {
        $this->db->transStart();

        // 1. Save or Update Medical Record
        $existing = $this->db->table('medical_records')->where('visit_id', $visitId)->get()->getRow();
        if ($existing) {
            $this->db->table('medical_records')->where('visit_id', $visitId)->update($soapData);
        } else {
            $soapData['visit_id'] = $visitId;
            $this->db->table('medical_records')->insert($soapData);
        }

        // 2. Pastikan billing_transactions SELALU ada
        $bill = $this->db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
        if (!$bill) {
            $todayStr = date('Ymd');
            $billPrefix = 'BIL-' . $todayStr . '-';
            $lastBilling = $this->db->table('billing_transactions')
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
            while ($this->db->table('billing_transactions')->where('billing_no', $billingNo)->countAllResults() > 0) {
                $nextBillNum++;
                $billingNo = $billPrefix . str_pad($nextBillNum, 4, '0', STR_PAD_LEFT);
            }

            $this->db->table('billing_transactions')->insert([
                'billing_no'       => $billingNo,
                'visit_id'         => $visitId,
                'total_services'   => 0.00,
                'total_medicines'  => 0.00,
                'total_restaurant' => 0.00,
                'discount'         => 0.00,
                'grand_total'      => 0.00,
                'status'           => 'open'
            ]);
            $bill = $this->db->table('billing_transactions')->where('visit_id', $visitId)->get()->getRow();
        }

        // Add Polyclinic Consultation price to Billing details automatically
        $visit = $this->db->table('patient_visits')->where('id', $visitId)->get()->getRow();
        
        if ($visit && $bill) {
            $consultPrice = 150000.00;
            $hasMedis = $this->db->table('billing_details')
                                 ->where('billing_id', $bill->id)
                                 ->where('item_type', 'medis')
                                 ->countAllResults();
            if ($hasMedis == 0) {
                $polyObj  = !empty($visit->polyclinic_id) ? $this->db->table('polyclinics')->where('id', $visit->polyclinic_id)->get()->getRow() : null;
                $polyName = $polyObj ? trim($polyObj->name) : 'Poli / Tindakan';
                if ($polyName === 'Tindakan Saja' || stripos($polyName, 'Tindakan') !== false) {
                    $serviceItemName = 'Konsultasi & Tindakan Medis Dokter';
                } else {
                    $serviceItemName = 'Konsultasi & Pemeriksaan ' . $polyName;
                }

                $this->db->table('billing_details')->insert([
                    'billing_id' => $bill->id,
                    'item_type'  => 'medis',
                    'item_name'  => $serviceItemName,
                    'qty'        => 1,
                    'price'      => $consultPrice,
                    'discount'   => 0.00,
                    'subtotal'   => $consultPrice
                ]);

                // Update billing transaction total
                $newTotalServices = $bill->total_services + $consultPrice;
                $newGrandTotal = $bill->grand_total + $consultPrice;
                $this->db->table('billing_transactions')
                         ->where('id', $bill->id)
                         ->update([
                             'total_services' => $newTotalServices,
                             'grand_total'    => $newGrandTotal,
                             'status'         => 'open'
                         ]);
            }
        }

        // 3. Update Patient Visit Status to 'examining'
        $this->db->table('patient_visits')->where('id', $visitId)->update(['status' => 'examining']);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return [
                'status' => 'error',
                'message' => 'Gagal menyimpan data rekam medis SOAP.'
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Rekam medis SOAP berhasil disimpan.'
        ];
    }
}
