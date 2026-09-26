<?php

namespace App\Controllers\Api;

use App\Models\PatientModel;

class PatientController extends BaseApiController
{
    /**
     * GET /api/v1/patients/search?q=NIK_OR_RM
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        if (empty($keyword)) {
            return $this->respondFailed('Parameter pencarian (q) wajib disertakan.', 400);
        }

        $patientModel = new PatientModel();
        $patient = $patientModel->groupStart()
                                ->where('nik', $keyword)
                                ->orWhere('no_rm', $keyword)
                                ->orWhere('phone', $keyword)
                                ->groupEnd()
                                ->first();

        if (!$patient) {
            return $this->respondFailed('Data pasien tidak ditemukan.', 404);
        }

        return $this->respondSuccess([
            'id'            => (int) $patient->id,
            'no_rm'         => $patient->no_rm,
            'nik'           => $patient->nik,
            'name'          => $patient->name,
            'gender'        => $patient->gender,
            'date_of_birth' => $patient->date_of_birth,
            'phone'         => $patient->phone,
            'address'       => $patient->address,
            'bpjs_number'   => $patient->bpjs_number,
        ], 'Pasien terverifikasi.');
    }

    /**
     * POST /api/v1/patients/register
     */
    public function register()
    {
        $json = $this->request->getJSON(true) ?: $this->request->getPost();
        if (empty($json)) {
            return $this->respondFailed('Data JSON pendaftaran tidak boleh kosong.', 400);
        }

        $patientModel = new PatientModel();
        
        // Generate No RM otomatis jika tidak disertakan
        if (empty($json['no_rm'])) {
            $json['no_rm'] = $patientModel->generateNextNoRm();
        }

        if (!$patientModel->validate($json)) {
            return $this->respondFailed('Validasi data pasien gagal.', 422, $patientModel->errors());
        }

        try {
            $insertedId = $patientModel->insert($json);
            \Config\Events::trigger('patient.registered', $insertedId, $json);

            return $this->respondSuccess([
                'patient_id' => $insertedId,
                'no_rm'      => $json['no_rm'],
                'name'       => $json['name'],
            ], 'Registrasi pasien berhasil.', 201);
        } catch (\Throwable $e) {
            return $this->respondFailed('Gagal menyimpan data pasien: ' . $e->getMessage(), 500);
        }
    }
}
