<?php

namespace App\Contracts\Repositories\Business;

use App\Models\Business\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Product $product): Product;

    public function store(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product);
}
