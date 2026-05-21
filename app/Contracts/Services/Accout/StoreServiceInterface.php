<?php

namespace App\Contracts\Services\Accout;

use App\DTOs\Accout\StoreDTO;
use App\DTOs\Accout\StoreResponseDTO;
use App\Models\Accout\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StoreServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(Store $store): StoreResponseDTO;

    public function store(StoreDTO $dto): StoreResponseDTO;

    public function update(Store $store, StoreDTO $dto): StoreResponseDTO;

    public function delete(Store $store);
}
