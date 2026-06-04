<?php

namespace App\Repositories\Stock;

use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Models\Stock\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockRepository implements StockRepositoryInterface
{
    public function __construct(
        private Stock $stockModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->stockModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Stock $stock): Stock
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $stock;
    }

    public function store(array $data): Stock
    {
        return $this->stockModel->create($data);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update($data);

        return $stock;
    }

    public function delete(Stock $stock)
    {
        return $stock->delete();
    }
}
