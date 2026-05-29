<?php

namespace App\Contracts\Repositories\Accout;

use App\Models\Accout\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function findByEmail(string $email): ?User;

    public function show(User $user);

    public function store(array $dataUser, ?array $dataCompany = null): User;

    public function update(User $user, array $data);

    public function delete(User $user);

    public function getDataUser(User $user): User;

}
