<?php

namespace App\Controllers\Api;

class SatuSehatController extends BaseApiController
{
    /**
     * GET /api/v1/satusehat/encounter/(:num)
     * Format HL7 FHIR Standard JSON untuk Integrasi SATUSEHAT Kemenkes RI
     */
    public function encounter($visitId = null)
    {
        $db = \Config\Database::connect('default');

        $visit = $db->table('patient_visits')
                    ->select('patient_visits.*, patients.name as patient_name, patients.nik, doctors.name as doctor_name, doctors.sip_number, polyclinics.name as poly_name')
                    ->join('patients', 'patients.id = patient_visits.patient_id', 'left')
                    ->join('doctors', 'doctors.id = patient_visits.doctor_id', 'left')
                    ->join('polyclinics', 'polyclinics.id = patient_visits.polyclinic_id', 'left')
                    ->where('patient_visits.id', $visitId)
                    ->get()
                    ->getRow();

        if (!$visit) {
            return $this->respondFailed('Kunjungan medis tidak ditemukan.', 404);
        }

        // FHIR R4 Encounter Resource Format
        $fhirEncounter = [
            'resourceType' => 'Encounter',
            'id'           => 'ENC-' . $visit->id,
            'identifier'   => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . (env('SATUSEHAT_ORG_ID') ?: 'SAWAMAWA-ORG-001'),
                    'value'  => $visit->no_visit
                ]
            ],
            'status'       => ($visit->status === 'completed') ? 'finished' : 'in-progress',
            'class'        => [
                'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code'    => 'AMB',
                'display' => 'ambulatory'
            ],
            'subject'      => [
                'reference' => 'Patient/' . $visit->nik,
                'display'   => $visit->patient_name
            ],
            'participant'  => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system'  => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code'    => 'ATND',
                                    'display' => 'attender'
                                ]
                            ]
                        ]
                    ],
                    'individual' => [
                        'display' => $visit->doctor_name
                    ]
                ]
            ],
            'period'       => [
                'start' => date('c', strtotime($visit->created_at)),
                'end'   => ($visit->status === 'completed') ? date('c', strtotime($visit->updated_at)) : null
            ],
            'location'     => [
                [
                    'location' => [
                        'display' => 'Poliklinik ' . $visit->poly_name
                    ]
                ]
            ]
        ];

        return $this->respondSuccess($fhirEncounter, 'Resource FHIR Encounter berhasil digenerate.');
    }
}
