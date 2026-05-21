<?php

namespace App\Contracts\Repositories\Accout;

use App\Models\Accout\UserStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserStoreRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(UserStore $userStore): UserStore;

    public function store(array $data): UserStore;

    public function update(UserStore $userStore, array $data): UserStore;

    public function delete(UserStore $userStore);
}
