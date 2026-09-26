<?php

namespace App\Services;

class ApprovalEngine
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function submitRequest($transactionType, $referenceId, $requestUserId)
    {
        $this->db->transStart();

        // Check if approval workflow exists
        $workflow = $this->db->table('approval_workflows')->where('transaction_type', $transactionType)->get()->getRow();
        if (!$workflow) {
            // No workflow defined, auto-approve immediately
            $this->autoApprove($transactionType, $referenceId);
            $this->db->transComplete();
            return ['status' => 'success', 'approved' => true, 'message' => 'Transaksi disetujui otomatis (tanpa workflow).'];
        }

        // Insert step 1 approval request
        $this->db->table('approval_requests')->insert([
            'transaction_type' => $transactionType,
            'reference_id'     => $referenceId,
            'step_level'       => 1,
            'status'           => 'pending',
            'notes'            => 'Diajukan oleh User ID: ' . $requestUserId
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Gagal mengajukan persetujuan workflow.'];
        }

        return ['status' => 'success', 'approved' => false, 'message' => 'Pengajuan persetujuan berhasil didaftarkan.'];
    }

    public function approveRequest($requestId, $approverUserId, $notes = null)
    {
        $this->db->transStart();

        $req = $this->db->table('approval_requests')->where('id', $requestId)->get()->getRow();
        if (!$req || $req->status !== 'pending') {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Request tidak valid atau sudah diproses.'];
        }

        // Update current step to approved
        $this->db->table('approval_requests')->where('id', $requestId)->update([
            'status'      => 'approved',
            'approver_id' => $approverUserId,
            'notes'       => $notes
        ]);

        // Find next step in workflow
        $workflow = $this->db->table('approval_workflows')->where('transaction_type', $req->transaction_type)->get()->getRow();
        $nextStep = $this->db->table('approval_steps')
                             ->where('workflow_id', $workflow->id)
                             ->where('step_level', $req->step_level + 1)
                             ->get()
                             ->getRow();

        if ($nextStep) {
            // Insert next step request
            $this->db->table('approval_requests')->insert([
                'transaction_type' => $req->transaction_type,
                'reference_id'     => $req->reference_id,
                'step_level'       => $nextStep->step_level,
                'status'           => 'pending',
                'notes'            => 'Menunggu verifikasi level ' . $nextStep->step_level
            ]);
        } else {
            // No more steps: Final Approval!
            $this->finalizeTransaction($req->transaction_type, $req->reference_id);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Gagal menyetujui request.'];
        }

        return ['status' => 'success', 'message' => 'Request berhasil disetujui.'];
    }

    public function rejectRequest($requestId, $approverUserId, $notes = null)
    {
        $this->db->transStart();

        $req = $this->db->table('approval_requests')->where('id', $requestId)->get()->getRow();
        if (!$req || $req->status !== 'pending') {
            $this->db->transRollback();
            return ['status' => 'error', 'message' => 'Request tidak valid atau sudah diproses.'];
        }

        // Reject request
        $this->db->table('approval_requests')->where('id', $requestId)->update([
            'status'      => 'rejected',
            'approver_id' => $approverUserId,
            'notes'       => $notes
        ]);

        // Set source transaction status to rejected
        if ($req->transaction_type === 'PROCUREMENT') {
            $this->db->table('purchase_requests')->where('id', $req->reference_id)->update(['status' => 'rejected']);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Gagal menolak request.'];
        }

        return ['status' => 'success', 'message' => 'Request berhasil ditolak.'];
    }

    protected function finalizeTransaction($type, $referenceId)
    {
        if ($type === 'PROCUREMENT') {
            // Update PR to approved
            $this->db->table('purchase_requests')->where('id', $referenceId)->update(['status' => 'approved']);

            // Auto-create Purchase Order (PO)
            $pr = $this->db->table('purchase_requests')->where('id', $referenceId)->get()->getRow();
            if ($pr) {
                $today = date('Ymd');
                $lastPO = $this->db->table('purchase_orders')
                                   ->where('DATE(created_at)', date('Y-m-d'))
                                   ->orderBy('id', 'DESC')
                                   ->limit(1)
                                   ->get()
                                   ->getRow();
                $nextNum = 1;
                if ($lastPO && preg_match('/PO-\d+-(\d+)/', $lastPO->po_no, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }
                $poNo = 'PO-' . $today . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                $this->db->table('purchase_orders')->insert([
                    'po_no'               => $poNo,
                    'purchase_request_id' => $pr->id,
                    'supplier_id'         => $pr->supplier_id,
                    'order_date'          => date('Y-m-d'),
                    'payment_terms'       => 'Net 30 Hari',
                    'total_amount'        => $pr->total_amount,
                    'notes'               => $pr->description ?? 'Diterbitkan otomatis dari persetujuan ' . $pr->request_no,
                    'status'              => 'ordered'
                ]);
                $poId = $this->db->insertID();

                // Copy PR items to PO items
                $prItems = $this->db->table('purchase_request_items')->where('purchase_request_id', $pr->id)->get()->getResult();
                foreach ($prItems as $pi) {
                    $this->db->table('purchase_order_items')->insert([
                        'purchase_order_id' => $poId,
                        'item_type'         => $pi->item_type,
                        'item_id'           => $pi->item_id,
                        'item_name'         => $pi->item_name,
                        'qty'               => $pi->qty,
                        'unit'              => $pi->unit,
                        'price'             => $pi->estimated_price,
                        'subtotal'          => $pi->subtotal,
                        'notes'             => $pi->notes,
                        'created_at'        => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    protected function autoApprove($type, $referenceId)
    {
        $this->finalizeTransaction($type, $referenceId);
    }
}
