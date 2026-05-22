<?php

namespace App\Contracts\Services\Business;

use App\DTOs\Business\ProductDTO;
use App\DTOs\Business\ProductResponseDTO;
use App\Models\Business\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Product $product): ProductResponseDTO;

    public function store(ProductDTO $dto): ProductResponseDTO;

    public function update(Product $product, ProductDTO $dto): ProductResponseDTO;

    public function delete(Product $product);
}
