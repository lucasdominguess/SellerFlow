<?php

namespace App\Contracts\Services\Accout;

use App\DTOs\Accout\UserStoreDTO;
use App\DTOs\Accout\UserStoreResponseDTO;
use App\Models\Accout\UserStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserStoreServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(UserStore $userStore): UserStoreResponseDTO;

    public function store(UserStoreDTO $dto): UserStoreResponseDTO;

    public function update(UserStore $userStore, UserStoreDTO $dto): UserStoreResponseDTO;

    public function delete(UserStore $userStore);
}
