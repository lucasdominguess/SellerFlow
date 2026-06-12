<?php

namespace App\Repositories\Finance;

use App\Contracts\Repositories\Finance\AccountPayableRepositoryInterface;
use App\Enums\CategoryFinance;
use App\Enums\OriginType;
use App\Models\Finance\AccountPayable;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountPayableRepository implements AccountPayableRepositoryInterface
{
    public function __construct(
        private AccountPayable $accountPayableModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->accountPayableModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(AccountPayable $accountPayable): AccountPayable
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $accountPayable;
    }

    public function store(array $data): AccountPayable
    {
        return $this->accountPayableModel->create($data);
    }

    public function update(AccountPayable $accountPayable, array $data): AccountPayable
    {
        $accountPayable->update($data);

        return $accountPayable;
    }

    public function delete(AccountPayable $accountPayable)
    {
        return $accountPayable->delete();
    }
}
