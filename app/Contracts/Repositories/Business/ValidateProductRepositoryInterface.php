<?php

namespace App\Contracts\Repositories\Business;

use App\Models\Business\ValidateProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ValidateProductRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(ValidateProduct $validateProduct): ValidateProduct;

    public function store(array $data): ValidateProduct;

    public function update(ValidateProduct $validateProduct, array $data): ValidateProduct;

    public function delete(ValidateProduct $validateProduct);
}
