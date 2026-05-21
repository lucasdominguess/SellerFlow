<?php

namespace App\Repositories\Accout;

use App\Contracts\Repositories\Accout\StoreRepositoryInterface;
use App\Models\Accout\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreRepository implements StoreRepositoryInterface
{
    public function __construct(
        private Store $storeModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->storeModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Store $store): Store
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $store;
    }

    public function store(array $data): Store
    {
        return $this->storeModel->create($data);
    }

    public function update(Store $store, array $data): Store
    {
        $store->update($data);

        return $store;
    }

    public function delete(Store $store)
    {
        return $store->delete();
    }
}
