<?php

namespace App\Repositories\Business;

use App\Contracts\Repositories\Business\FornecedorRepositoryInterface;
use App\Models\Business\Fornecedor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FornecedorRepository implements FornecedorRepositoryInterface
{
    public function __construct(
        private Fornecedor $fornecedorModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->fornecedorModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Fornecedor $fornecedor): Fornecedor
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $fornecedor;
    }

    public function store(array $data): Fornecedor
    {
        return $this->fornecedorModel->create($data);
    }

    public function update(Fornecedor $fornecedor, array $data): Fornecedor
    {
        $fornecedor->update($data);

        return $fornecedor;
    }

    public function delete(Fornecedor $fornecedor)
    {
        return $fornecedor->delete();
    }
}
