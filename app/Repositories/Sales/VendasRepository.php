<?php

namespace App\Repositories\Sales;

use App\Classes\AuthContext;
use App\Contracts\Repositories\Sales\VendasRepositoryInterface;
use App\DTOs\Sales\VendaItemDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendasRepository implements VendasRepositoryInterface
{
    public function __construct(
        private Sale $vendaModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        // tenant scoping: lista apenas vendas da empresa do usuário logado (barra IDOR)
        $query = $this->vendaModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Sale $venda): Sale
    {
        return $venda->load('itens');
    }

    public function store(array $data): Sale
    {
        return $this->vendaModel->create($data);
    }

    /**
     * @param VendaItemDTO[] $itens
     * Cria os itens da venda em lote, calcula valor_total (quantidade * valor_unitario)
     * e retorna a venda com a relação itens carregada.
     */
    public function storeItens(Sale $venda, array $itens): Sale
    {
        $venda->itens()->createMany(
            array_map(fn(VendaItemDTO $item) => [
                ...$item->toArray(),
                'valor_total' => round($item->quantidade * $item->valor_unitario, 2),
            ], $itens)
        );

        return $venda->load('itens');
    }

    public function update(Sale $venda, array $data): Sale
    {
        $venda->update($data);

        return $venda;
    }

    public function delete(Sale $venda)
    {
        DB::transaction(function () use ($venda) {
            $venda->itens()->delete();
            $venda->delete();
        });
    }
}
