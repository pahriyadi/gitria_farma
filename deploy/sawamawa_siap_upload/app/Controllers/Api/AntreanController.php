<?php

namespace App\Controllers\Api;

use App\Models\PatientVisitModel;
use App\Models\PolyclinicModel;

class AntreanController extends BaseApiController
{
    /**
     * GET /api/v1/antrean
     * Data realtime seluruh antrean poliklinik untuk Display TV Ruang Tunggu
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $polyclinics = $db->table('polyclinics')
                          ->where('status', 'active')
                          ->get()
                          ->getResult();

        $antreanData = [];
        foreach ($polyclinics as $poly) {
            // Antrean Sedang Dilayani
            $currentCalling = $db->table('patient_visits')
                                 ->select('patient_visits.no_visit, patients.name as patient_name, doctors.name as doctor_name')
                                 ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                                 ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                                 ->where('patient_visits.polyclinic_id', $poly->id)
                                 ->where('patient_visits.visit_date', $today)
                                 ->where('patient_visits.status', 'examining')
                                 ->orderBy('patient_visits.updated_at', 'DESC')
                                 ->get()
                                 ->getRow();

            // Total Menunggu & Total Selesai
            $totalWaiting = $db->table('patient_visits')
                               ->where('polyclinic_id', $poly->id)
                               ->where('visit_date', $today)
                               ->whereIn('status', ['waiting', 'triage'])
                               ->countAllResults();

            $totalCompleted = $db->table('patient_visits')
                                 ->where('polyclinic_id', $poly->id)
                                 ->where('visit_date', $today)
                                 ->where('status', 'completed')
                                 ->countAllResults();

            $antreanData[] = [
                'polyclinic_id'   => (int) $poly->id,
                'polyclinic_name' => $poly->name,
                'current_calling' => $currentCalling ? [
                    'no_antrean'   => $currentCalling->no_visit,
                    'patient_name' => $currentCalling->patient_name,
                    'doctor_name'  => $currentCalling->doctor_name,
                ] : null,
                'total_waiting'   => $totalWaiting,
                'total_completed' => $totalCompleted,
            ];
        }

        return $this->respondSuccess([
            'date'         => $today,
            'polyclinics'  => $antreanData,
            'server_clock' => date('H:i:s')
        ], 'Data antrean poliklinik berhasil dimuat.');
    }
}
