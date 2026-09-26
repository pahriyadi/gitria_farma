<?php

namespace App\Controllers\Api;

use App\Models\MedicineModel;

class MedicineController extends BaseApiController
{
    /**
     * GET /api/v1/medicines?q=keyword&page=1
     */
    public function index()
    {
        $db = \Config\Database::connect('default');
        $keyword = $this->request->getGet('q');
        $limit = (int) ($this->request->getGet('limit') ?: 20);

        $builder = $db->table('medicines')
                      ->select('medicines.id, medicines.code, medicines.name, medicines.type, medicines.unit, medicines.price, COALESCE(SUM(medicine_batches.stock), 0) as total_stock')
                      ->join('medicine_batches', 'medicine_batches.medicine_id = medicines.id', 'left')
                      ->where('medicines.status', 'active')
                      ->groupBy('medicines.id')
                      ->orderBy('medicines.name', 'ASC')
                      ->limit($limit);

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('medicines.name', $keyword)
                    ->orLike('medicines.code', $keyword)
                    ->groupEnd();
        }

        $medicines = $builder->get()->getResult();

        return $this->respondSuccess([
            'total' => count($medicines),
            'items' => $medicines
        ], 'Daftar katalog obat dan stok berhasil dimuat.');
    }
}
