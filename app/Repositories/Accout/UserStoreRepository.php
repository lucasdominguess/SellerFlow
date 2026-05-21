<?php

namespace App\Repositories\Accout;

use App\Contracts\Repositories\Accout\UserStoreRepositoryInterface;
use App\Models\Accout\UserStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserStoreRepository implements UserStoreRepositoryInterface
{
    public function __construct(
        private UserStore $userStoreModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->userStoreModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(UserStore $userStore): UserStore
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $userStore;
    }

    public function store(array $data): UserStore
    {
        return $this->userStoreModel->create($data);
    }

    public function update(UserStore $userStore, array $data): UserStore
    {
        $userStore->update($data);

        return $userStore;
    }

    public function delete(UserStore $userStore)
    {
        return $userStore->delete();
    }
}
