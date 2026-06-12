<?php

namespace App\Contracts\Services\Finance;

use App\DTOs\Finance\AccountPayableDTO;
use App\DTOs\Finance\AccountPayableResponseDTO;
use App\Models\Finance\AccountPayable;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountPayableServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(AccountPayable $accountPayable): AccountPayableResponseDTO;

    public function store(AccountPayableDTO $dto): AccountPayableResponseDTO;

    public function update(AccountPayable $accountPayable, AccountPayableDTO $dto): AccountPayableResponseDTO;

    public function delete(AccountPayable $accountPayable);

    public function proccessPurchase(Compra $compra) : AccountPayable;

    public function syncStatusFromPurchase(Compra $compra): void;
}
