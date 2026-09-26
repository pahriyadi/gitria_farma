<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TodayCasesSeeder extends Seeder
{
    public function run()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");

        $now = date('Y-m-d H:i:s');
        $todayDate = date('Y-m-d');

        // Clean any existing entries of these test IDs to avoid conflict
        $this->db->table('patients')->where('id', 3)->delete();
        $this->db->table('patient_visits')->whereIn('id', [1, 2])->delete();
        $this->db->table('queue_numbers')->whereIn('id', [1, 2])->delete();
        $this->db->table('triage_records')->whereIn('visit_id', [1, 2])->delete();
        $this->db->table('medical_records')->whereIn('visit_id', [1, 2])->delete();
        $this->db->table('icd10_diagnoses')->whereIn('visit_id', [1, 2])->delete();
        $this->db->table('icd9_procedures')->whereIn('visit_id', [1, 2])->delete();
        $this->db->table('prescriptions')->whereIn('id', [1, 2])->delete();
        $this->db->table('prescription_details')->whereIn('prescription_id', [1, 2])->delete();
        $this->db->table('billing_transactions')->whereIn('id', [1, 2])->delete();
        $this->db->table('billing_details')->whereIn('billing_id', [1, 2])->delete();
        $this->db->table('cash_transactions')->where('id', 1)->delete();
        $this->db->table('fee_transactions')->whereIn('id', [1, 2])->delete();
        $this->db->table('kitchen_orders')->whereIn('order_detail_id', [1, 2])->delete();
        $this->db->table('restaurant_orders')->where('id', 1)->delete();
        $this->db->table('restaurant_order_details')->where('order_id', 1)->delete();
        $this->db->table('purchase_requests')->where('id', 1)->delete();
        $this->db->table('approval_requests')->where('id', 1)->delete();
        $this->db->table('approval_steps')->where('workflow_id', 1)->delete();
        $this->db->table('approval_workflows')->where('id', 1)->delete();
        $this->db->table('stock_movements')->where('reference_id', 1)->where('transaction_type', 'resep')->delete();
        $this->db->table('journal_entries')->whereIn('id', [1, 2, 3])->delete();
        $this->db->table('journal_entry_details')->whereIn('journal_id', [1, 2, 3])->delete();

        // ==========================================
        // KASUS 1: PASIEN KLINIK JANTUNG (LUNAS)
        // ==========================================

        // 1. Patient
        $this->db->table('patients')->insert([
            'id'                     => 3,
            'no_rm'                  => 'RM-20260821-0001',
            'nik'                    => '3201010101010019',
            'name'                   => 'Budi Santoso',
            'gender'                 => 'L',
            'place_of_birth'         => 'Jakarta',
            'date_of_birth'          => '1985-05-12',
            'phone'                  => '081234567890',
            'address'                => 'Jl. Mawar Merah No. 12, Jakarta',
            'emergency_contact_name' => 'Siti Aminah',
            'emergency_contact_phone'=> '081234567891',
            'bpjs_number'            => null,
            'created_at'             => $now,
            'updated_at'             => $now
        ]);

        // 2. Patient Visit (completed today)
        $this->db->table('patient_visits')->insert([
            'id'             => 1,
            'no_visit'       => 'VST-20260821-0001',
            'patient_id'     => 3,
            'polyclinic_id'  => 1, // Jantung
            'doctor_id'      => 1, // dr. Andi Wijaya
            'payment_method' => 'Tunai',
            'status'         => 'completed',
            'created_at'     => $now,
            'updated_at'     => $now
        ]);

        // 3. Queue
        $this->db->table('queue_numbers')->insert([
            'id'            => 1,
            'visit_id'      => 1,
            'polyclinic_id' => 1,
            'queue_no'      => 1,
            'status'        => 'completed',
            'created_at'    => $now
        ]);

        // 4. Triage / Vital Signs
        $this->db->table('triage_records')->insert([
            'visit_id'       => 1,
            'weight'         => 72.00,
            'height'         => 170.00,
            'blood_pressure' => '120/80',
            'temperature'    => 36.50,
            'pulse'          => 80,
            'respiration'    => 18,
            'complaints'     => 'Nyeri dada ringan setelah naik tangga.',
            'anamnesis'      => 'Kerap lelah berlebih, riwayat kolesterol.',
            'nurse_notes'    => 'Tensi stabil, siap masuk ruangan dokter.',
            'created_at'     => $now
        ]);

        // 5. Medical Record SOAP
        $this->db->table('medical_records')->insert([
            'id'           => 1,
            'visit_id'     => 1,
            'subjective'   => 'Nyeri dada ringan setelah naik tangga.',
            'objective'    => 'Bunyi jantung normal, sinus ritme.',
            'assessment'   => 'Angina pectoris.',
            'plan'         => 'Beri Amlodipine 5mg.',
            'doctor_notes' => 'Kontrol 2 minggu lagi.',
            'created_at'   => $now
        ]);

        // 6. ICD-10 & ICD-9 Codes
        $this->db->table('icd10_diagnoses')->insert([
            'visit_id'    => 1,
            'icd10_code'  => 'I20.9',
            'description' => 'Angina pectoris, unspecified',
            'created_at'  => $now
        ]);

        $this->db->table('icd9_procedures')->insert([
            'visit_id'   => 1,
            'icd9_code'  => '89.52',
            'description'=> 'Electrocardiogram',
            'created_at' => $now
        ]);

        // 7. Prescription & Details
        $this->db->table('prescriptions')->insert([
            'id'         => 1,
            'visit_id'   => 1,
            'doctor_id'  => 1,
            'status'     => 'dispensed',
            'created_at' => $now
        ]);

        $this->db->table('prescription_details')->insert([
            'prescription_id' => 1,
            'medicine_id'     => 1, // Paracetamol
            'qty'             => 30,
            'dosage'          => '3x1 tablet sesudah makan',
            'price'           => 2000.00,
            'created_at'      => $now
        ]);

        // Reduce stock in batch 1 (from 50 to 20)
        $batch = $this->db->table('medicine_batches')->where('id', 1)->get()->getRow();
        if ($batch) {
            $this->db->table('medicine_batches')->where('id', 1)->update(['stock' => 20]);
        }

        // Add Stock Movement Log
        $this->db->table('stock_movements')->insert([
            'medicine_id'      => 1,
            'batch_id'         => 1,
            'transaction_type' => 'resep',
            'reference_id'     => 1,
            'qty_in'           => 0,
            'qty_out'          => 30,
            'balance'          => 20,
            'user_id'          => 8 // Apoteker
        ]);

        // 8. Billing Transaction (Paid)
        $this->db->table('billing_transactions')->insert([
            'id'             => 1,
            'billing_no'     => 'BIL-20260821-0001',
            'visit_id'       => 1,
            'total_services' => 250000.00,
            'total_medicines'=> 60000.00,
            'total_restaurant'=> 0.00,
            'discount'       => 10000.00,
            'grand_total'    => 300000.00,
            'status'         => 'paid',
            'created_at'     => $now
        ]);

        $this->db->table('billing_details')->insertBatch([
            ['billing_id' => 1, 'item_name' => 'Konsultasi Spesialis Jantung', 'item_type' => 'medis', 'qty' => 1, 'price' => 150000.00, 'subtotal' => 150000.00, 'created_at' => $now],
            ['billing_id' => 1, 'item_name' => 'Pemeriksaan EKG Jantung', 'item_type' => 'medis', 'qty' => 1, 'price' => 100000.00, 'subtotal' => 100000.00, 'created_at' => $now],
            ['billing_id' => 1, 'item_name' => 'Paracetamol 500mg Tablet (Box)', 'item_type' => 'obat', 'qty' => 30, 'price' => 2000.00, 'subtotal' => 60000.00, 'created_at' => $now]
        ]);

        // 9. Cash Transaction
        $this->db->table('cash_transactions')->insert([
            'id'               => 1,
            'receipt_no'       => 'RCP-20260821-0001',
            'billing_id'       => 1,
            'cash_register_id' => 1,
            'amount'           => 300000.00,
            'payment_method'   => 'Tunai',
            'created_at'       => $now
        ]);

        // 10. Doctor Commissions (60% of Consultation = 90k, 50% of EKG = 50k, total = 140k)
        $this->db->table('fee_transactions')->insertBatch([
            ['id' => 1, 'visit_id' => 1, 'doctor_id' => 1, 'fee_rule_id' => 1, 'amount' => 90000.00, 'created_at' => $now],
            ['id' => 2, 'visit_id' => 1, 'doctor_id' => 1, 'fee_rule_id' => 2, 'amount' => 50000.00, 'created_at' => $now]
        ]);

        // 11. Post Journal entries & update Accounts
        // A. Jurnal Penerimaan Layanan Medis (Debit Bank QRIS 1-103: 240k, Credit Rev Clinic 4-101: 240k)
        $this->db->table('journal_entries')->insert([
            'id'            => 1,
            'journal_no'    => 'JV-20260821-0001',
            'entry_date'    => $todayDate,
            'source_module' => 'Keuangan',
            'reference_id'  => 1,
            'description'   => 'Penerimaan Billing BIL-20260821-0001 - Jasa Medis'
        ]);
        $this->db->table('journal_entry_details')->insertBatch([
            ['journal_id' => 1, 'account_id' => 3, 'debit' => 240000.00, 'credit' => 0.00], // Bank QRIS
            ['journal_id' => 1, 'account_id' => 9, 'debit' => 0.00, 'credit' => 240000.00]  // Rev Clinic
        ]);
        // Update Account Balances
        $qrisAcc = $this->db->table('accounts')->where('id', 3)->get()->getRow();
        $this->db->table('accounts')->where('id', 3)->update(['balance' => $qrisAcc->balance + 240000.00]);
        $clinicRevAcc = $this->db->table('accounts')->where('id', 9)->get()->getRow();
        $this->db->table('accounts')->where('id', 9)->update(['balance' => $clinicRevAcc->balance + 240000.00]);

        // B. Jurnal Penjualan Farmasi (Debit Kas Kasir 1-101: 60k, Credit Rev Pharmacy 4-102: 60k)
        $this->db->table('journal_entries')->insert([
            'id'            => 2,
            'journal_no'    => 'JV-20260821-0002',
            'entry_date'    => $todayDate,
            'source_module' => 'Keuangan',
            'reference_id'  => 1,
            'description'   => 'Penerimaan Billing BIL-20260821-0001 - Farmasi'
        ]);
        $this->db->table('journal_entry_details')->insertBatch([
            ['journal_id' => 2, 'account_id' => 1, 'debit' => 60000.00, 'credit' => 0.00],  // Kas Kasir
            ['journal_id' => 2, 'account_id' => 10, 'debit' => 0.00, 'credit' => 60000.00]  // Rev Pharmacy
        ]);
        $cashAcc = $this->db->table('accounts')->where('id', 1)->get()->getRow();
        $this->db->table('accounts')->where('id', 1)->update(['balance' => $cashAcc->balance + 60000.00]);
        $pharmacyRevAcc = $this->db->table('accounts')->where('id', 10)->get()->getRow();
        $this->db->table('accounts')->where('id', 10)->update(['balance' => $pharmacyRevAcc->balance + 60000.00]);

        // C. Jurnal Beban Komisi Dokter (Debit Beban Jasa Medis 6-102: 140k, Credit Hutang Komisi 2-102: 140k)
        $this->db->table('journal_entries')->insert([
            'id'            => 3,
            'journal_no'    => 'JV-20260821-0003',
            'entry_date'    => $todayDate,
            'source_module' => 'Keuangan',
            'reference_id'  => 1,
            'description'   => 'Beban Komisi Dokter dr. Andi Wijaya Visit VST-20260821-0001'
        ]);
        $this->db->table('journal_entry_details')->insertBatch([
            ['journal_id' => 3, 'account_id' => 14, 'debit' => 140000.00, 'credit' => 0.00], // Expense
            ['journal_id' => 3, 'account_id' => 7, 'debit' => 0.00, 'credit' => 140000.00]   // Payable
        ]);
        $expAcc = $this->db->table('accounts')->where('id', 14)->get()->getRow();
        $this->db->table('accounts')->where('id', 14)->update(['balance' => $expAcc->balance + 140000.00]);
        $payableAcc = $this->db->table('accounts')->where('id', 7)->get()->getRow();
        $this->db->table('accounts')->where('id', 7)->update(['balance' => $payableAcc->balance + 140000.00]);


        // ==========================================
        // KASUS 2: DIET GIZI RESTORAN RAWAT INAP (DRAFT BILLING)
        // ==========================================

        // 1. Create Patient Visit 2 for Budi Santoso (Nutritionist Sp.GK consultation)
        $this->db->table('patient_visits')->insert([
            'id'             => 2,
            'no_visit'       => 'VST-20260821-0002',
            'patient_id'     => 3,
            'polyclinic_id'  => 2, // Gizi
            'doctor_id'      => 2, // dr. Siti Rahma
            'payment_method' => 'Tunai',
            'status'         => 'examining',
            'created_at'     => $now,
            'updated_at'     => $now
        ]);

        // 2. Queue for Visit 2
        $this->db->table('queue_numbers')->insert([
            'id'            => 2,
            'visit_id'      => 2,
            'polyclinic_id' => 2,
            'queue_no'      => 2,
            'status'        => 'called',
            'created_at'    => $now
        ]);

        // 3. Restaurant Order linked to Patient Visit 2
        $this->db->table('restaurant_orders')->insert([
            'id'           => 1,
            'order_no'     => 'RST-20260821-0001',
            'table_id'     => 2, // Meja 02 (Rawat Inap Bed 02)
            'visit_id'     => 2, // Link to Visit 2
            'status'       => 'ready',
            'created_at'   => $now
        ]);

        $this->db->table('restaurant_order_details')->insertBatch([
            ['id' => 1, 'order_id' => 1, 'menu_id' => 2, 'qty' => 1, 'price' => 35000.00, 'status' => 'ready', 'created_at' => $now], // Salad Buah
            ['id' => 2, 'order_id' => 1, 'menu_id' => 4, 'qty' => 1, 'price' => 15000.00, 'status' => 'ready', 'created_at' => $now]  // Jus Wortel
        ]);

        // Insert Kitchen Orders for the KDS display
        $this->db->table('kitchen_orders')->insertBatch([
            ['order_detail_id' => 1, 'status' => 'ready', 'created_at' => $now],
            ['order_detail_id' => 2, 'status' => 'ready', 'created_at' => $now]
        ]);

        // 4. Draft Billing for Restaurant charges linked to Visit 2
        $this->db->table('billing_transactions')->insert([
            'id'             => 2,
            'billing_no'     => 'BIL-20260821-0002',
            'visit_id'       => 2, // Linked to Visit 2
            'total_services' => 0.00,
            'total_medicines'=> 0.00,
            'total_restaurant'=> 50000.00,
            'discount'       => 0.00,
            'grand_total'    => 50000.00,
            'status'         => 'draft',
            'created_at'     => $now
        ]);

        $this->db->table('billing_details')->insertBatch([
            ['billing_id' => 2, 'item_name' => 'Salad Buah Tinggi Protein & Serat (Resto)', 'item_type' => 'resto', 'qty' => 1, 'price' => 35000.00, 'subtotal' => 35000.00, 'created_at' => $now],
            ['billing_id' => 2, 'item_name' => 'Jus Wortel Murni (Resto)', 'item_type' => 'resto', 'qty' => 1, 'price' => 15000.00, 'subtotal' => 15000.00, 'created_at' => $now]
        ]);


        // ==========================================
        // KASUS 3: LOGISTIK / PENGADAAN DARURAT (PENDING APPROVAL)
        // ==========================================

        // 1. Purchase Request (submitted status)
        $this->db->table('purchase_requests')->insert([
            'id'           => 1,
            'request_no'   => 'PR-20260821-0001',
            'supplier_id'  => 2, // PT Kimia Farma
            'status'       => 'submitted',
            'total_amount' => 1500000.00,
            'created_at'   => $now
        ]);

        // 2. Seed active Workflow templates for PROCUREMENT
        $this->db->table('approval_workflows')->insert([
            'id'               => 1,
            'description'      => 'Alur Verifikasi Pembelian Logistik',
            'transaction_type' => 'PROCUREMENT',
            'created_at'       => $now
        ]);

        $this->db->table('approval_steps')->insert([
            'workflow_id' => 1,
            'step_name'   => 'Persetujuan Akhir Direksi',
            'step_level'  => 1,
            'role_id'     => 3, // Direksi (Direktur)
            'created_at'  => $now
        ]);

        // 3. Approval Request Header (Pending status)
        $this->db->table('approval_requests')->insert([
            'id'            => 1,
            'transaction_type' => 'PROCUREMENT',
            'reference_id'  => 1,
            'step_level'    => 1,
            'approver_id'   => null,
            'status'        => 'pending',
            'notes'         => 'Pengajuan pengadaan Paracetamol darurat oleh Apoteker.',
            'created_at'    => $now,
            'updated_at'    => $now
        ]);

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
