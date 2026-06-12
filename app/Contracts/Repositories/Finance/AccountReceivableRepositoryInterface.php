<?php

namespace App\Contracts\Repositories\Finance;

use App\Models\Finance\AccountReceivable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountReceivableRepositoryInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(AccountReceivable $accountReceivable): AccountReceivable;

    public function store(array $data): AccountReceivable;

    public function update(AccountReceivable $accountReceivable, array $data): AccountReceivable;

    public function delete(AccountReceivable $accountReceivable);
}
