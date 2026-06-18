<?php

namespace App\Repositories\Business;

use App\Contracts\Repositories\Business\SupplierRepositoryInterface;
use App\Models\Business\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function __construct(
        private Supplier $supplierModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->supplierModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Supplier $supplier): Supplier
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $supplier;
    }

    public function store(array $data): Supplier
    {
        return $this->supplierModel->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier;
    }

    public function delete(Supplier $supplier)
    {
        return $supplier->delete();
    }
}
