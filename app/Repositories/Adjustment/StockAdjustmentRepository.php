<?php

namespace App\Repositories\Adjustment;

use App\Contracts\Repositories\Adjustment\StockAdjustmentRepositoryInterface;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockAdjustmentRepository implements StockAdjustmentRepositoryInterface
{
    public function __construct(
        private StockAdjustment $stockAdjustmentModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->stockAdjustmentModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(StockAdjustment $stockAdjustment): StockAdjustment
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $stockAdjustment;
    }

    public function store(array $data): StockAdjustment
    {
        return $this->stockAdjustmentModel->create($data);
    }
}
