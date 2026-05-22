<?php

namespace App\Services\Business;

use App\Contracts\Repositories\Business\ProductRepositoryInterface;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\DTOs\Business\ProductDTO;
use App\DTOs\Business\ProductResponseDTO;
use App\Models\Business\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return ProductResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Product $product): ProductResponseDTO
    {
        $product = $this->repository->show($product);

        return ProductResponseDTO::fromModel($product);
    }

    public function store(ProductDTO $dto): ProductResponseDTO
    {
        $product = $this->repository->store($dto->toArray());

        return ProductResponseDTO::fromModel($product);
    }

    public function update(Product $product, ProductDTO $dto): ProductResponseDTO
    {
        $product = $this->repository->update($product, $dto->toArray());

        return ProductResponseDTO::fromModel($product);
    }

    public function delete(Product $product)
    {
        return $this->repository->delete($product);
    }
}
