<?php

namespace App\Repositories\Business;

use App\Contracts\Repositories\Business\ValidateProductRepositoryInterface;
use App\Models\Business\ValidateProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ValidateProductRepository implements ValidateProductRepositoryInterface
{
    public function __construct(
        private ValidateProduct $validateProductModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->validateProductModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(ValidateProduct $validateProduct): ValidateProduct
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $validateProduct;
    }

    public function store(array $data): ValidateProduct
    {

        return $this->validateProductModel->create($data);
    }

    public function update(ValidateProduct $validateProduct, array $data): ValidateProduct
    {
        $validateProduct->update($data);

        return $validateProduct;
    }

    public function delete(ValidateProduct $validateProduct)
    {
        return $validateProduct->delete();
    }
}
