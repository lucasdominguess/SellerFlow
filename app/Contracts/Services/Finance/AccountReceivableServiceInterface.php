<?php

namespace App\Contracts\Services\Finance;

use App\DTOs\Finance\AccountReceivableDTO;
use App\DTOs\Finance\AccountReceivableResponseDTO;
use App\Models\Finance\AccountReceivable;
use App\Models\Sales\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountReceivableServiceInterface
{
    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(AccountReceivable $accountReceivable): AccountReceivableResponseDTO;

    public function store(AccountReceivableDTO $dto): AccountReceivableResponseDTO;

    public function update(AccountReceivable $accountReceivable, AccountReceivableDTO $dto): AccountReceivableResponseDTO;

    public function delete(AccountReceivable $accountReceivable);

    public function proccessSale(Venda $venda) : AccountReceivable;

    public function syncStatusFromSale(Venda $venda): void;
}
