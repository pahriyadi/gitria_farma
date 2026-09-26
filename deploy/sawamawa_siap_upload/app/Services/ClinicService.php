<?php

namespace App\Services;

use App\Services\FinanceService;

class ClinicService
{
    protected $db;
    protected $tindakanPolyId;

    public function __construct()
    {
        $this->db = \Config\Database::connect();

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
        $builder = $this->db->table('patients');

        // Check if NIK already exists
        $existing = $builder->where('nik', $data['nik'])->get()->getRow();
        if ($existing) {
            return [
                'status' => 'error',
                'message' => 'Pasien dengan NIK tersebut sudah terdaftar.'
            ];
        }

        // Generate No RM (RM-XXXXXX)
        $lastRM = $builder->orderBy('id', 'DESC')->limit(1)->get()->getRow();
        $nextNum = 1;
        if ($lastRM && preg_match('/RM-(\d+)/', $lastRM->no_rm, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $noRm = 'RM-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

        $data['no_rm'] = $noRm;
        $builder->insert($data);
        $patientId = $this->db->insertID();

        // Trigger Event Hook Pasien Terdaftar
        \CodeIgniter\Events\Events::trigger('patient.registered', $patientId, $data);

        return [
            'status' => 'success',
            'patient_id' => $patientId,
            'no_rm' => $noRm,
            'message' => 'Pendaftaran pasien baru berhasil.'
        ];
    }

    public function createVisit($patientId, $polyclinicId, $doctorId, $paymentMethod, $visitType = 'poli', $serviceId = null, $roomId = null, $insuranceId = null)
    {
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

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return [
                'status' => 'error',
                'message' => 'Gagal membuat kunjungan pasien baru.'
            ];
        }

        // Push Notifikasi Operasional Real-Time ke Semua Browser & Perangkat
        try {
            $patient = $this->db->table('patients')->where('id', $patientId)->get()->getRow();
            $patientName = $patient ? $patient->name : 'Pasien';
            $patientRm   = $patient ? $patient->no_rm : '-';

            $notif = new \App\Services\NotificationService();
            $notif->send([
                'notification_key' => 'visit_created_' . $visitId,
                'category'         => 'klinik',
                'type'             => 'info',
                'badge'            => 'Pasien Baru',
                'icon'             => 'fas fa-user-plus',
                'title'            => 'Antrean Pasien Baru #' . $queueNo,
                'message'          => "Pasien [{$patientName}] (No. RM: {$patientRm}) terdaftar pada antrean #{$queueNo}.",
                'link'             => base_url('klinik/antrean'),
                'target_roles'     => null,
                'sender_name'      => 'Pendaftaran Rawat Jalan'
            ]);
        } catch (\Throwable $e) {}

        return [
            'status' => 'success',
            'visit_id' => $visitId,
            'no_visit' => $noVisit,
            'queue_no' => $queueNo,
            'message' => 'Kunjungan pasien berhasil didaftarkan.'
        ];
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

        // Add Polyclinic Consultation price to Billing details automatically (Only if it's a Poli visit)
        $visit = $this->db->table('patient_visits')->where('id', $visitId)->get()->getRow();
        
        if ($visit && $visit->visit_type === 'poli') {
            // Find consultation service
            $service = $this->db->table('services')
                                ->select('services.id, services.name, service_prices.price')
                                ->join('service_prices', 'service_prices.service_id = services.id')
                                ->where('services.category', 'klinik')
                                ->where('services.name LIKE', '%Konsultasi%')
                                ->get()
                                ->getRow();

            if ($service && $bill) {
                // Check if already billed
                $checkItem = $this->db->table('billing_details')
                                      ->where('billing_id', $bill->id)
                                      ->where('item_name', $service->name)
                                      ->get()
                                      ->getRow();
                if (!$checkItem) {
                    $this->db->table('billing_details')->insert([
                        'billing_id' => $bill->id,
                        'item_type'  => 'medis',
                        'item_name'  => $service->name,
                        'qty'        => 1,
                        'price'      => $service->price,
                        'discount'   => 0.00,
                        'subtotal'   => $service->price
                    ]);

                    // Update billing transaction total
                    $newTotalServices = $bill->total_services + $service->price;
                    $newGrandTotal = $bill->grand_total + $service->price;
                    $this->db->table('billing_transactions')
                             ->where('id', $bill->id)
                             ->update([
                                 'total_services' => $newTotalServices,
                                 'grand_total'    => $newGrandTotal,
                                 'status'         => 'open'
                             ]);
                }
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
