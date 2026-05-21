<?php

namespace App\Contracts\Repositories\Accout;

use App\Models\Accout\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StoreRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Store $store): Store;

    public function store(array $data): Store;

    public function update(Store $store, array $data): Store;

    public function delete(Store $store);
}
