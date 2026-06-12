<?php

namespace App\Contracts\Repositories\Finance;

use App\Models\Finance\AccountPayable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountPayableRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(AccountPayable $accountPayable): AccountPayable;

    public function store(array $data): AccountPayable;

    public function update(AccountPayable $accountPayable, array $data): AccountPayable;

    public function delete(AccountPayable $accountPayable);
}
