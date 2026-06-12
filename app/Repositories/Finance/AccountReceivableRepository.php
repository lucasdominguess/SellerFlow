<?php

namespace App\Repositories\Finance;

use App\Contracts\Repositories\Finance\AccountReceivableRepositoryInterface;
use App\Models\Finance\AccountReceivable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountReceivableRepository implements AccountReceivableRepositoryInterface
{
    public function __construct(
        private AccountReceivable $accountReceivableModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->accountReceivableModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(AccountReceivable $accountReceivable): AccountReceivable
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $accountReceivable;
    }

    public function store(array $data): AccountReceivable
    {
        return $this->accountReceivableModel->create($data);
    }

    public function update(AccountReceivable $accountReceivable, array $data): AccountReceivable
    {
        $accountReceivable->update($data);

        return $accountReceivable;
    }

    public function delete(AccountReceivable $accountReceivable)
    {
        return $accountReceivable->delete();
    }
}
