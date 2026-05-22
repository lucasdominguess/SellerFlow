<?php

namespace App\Repositories\Business;

use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Models\Business\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private Product $productModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->productModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Product $product): Product
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $product;
    }

    public function store(array $data): Product
    {
        return $this->productModel->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }
}
