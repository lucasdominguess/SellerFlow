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
        $query = $this->productModel->with($this->withRelations());

        if (!empty($filters)) {
            // Adicione filtros específicos aqui:
            // if (!empty($filters['name'])) {
            //     $query->where('name', 'like', '%' . $filters['name'] . '%');
            // }
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Product $product): Product
    {
        return $product->load($this->withRelations());
    }

    public function store(array $data): Product
    {
        $product = $this->productModel->create($data);

        return $product->load($this->withRelations());
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->load($this->withRelations());
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }

    private function withRelations(): array
    {
        return ['status', 'supplier', 'images'];
    }
}
