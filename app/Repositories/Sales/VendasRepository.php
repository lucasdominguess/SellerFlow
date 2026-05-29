<?php

namespace App\Repositories\Sales;

use App\Classes\AuthContext;
use App\Contracts\Repositories\Sales\VendasRepositoryInterface;
use App\DTOs\Sales\VendaItemDTO;
use App\Models\Sales\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendasRepository implements VendasRepositoryInterface
{
    public function __construct(
        private Venda $vendaModel,
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

    public function show(Venda $venda): Venda
    {
        return $venda->load('itens');
    }

    public function store(array $data): Venda
    {
        return $this->vendaModel->create($data);
    }

    /**
     * @param VendaItemDTO[] $itens
     * Cria os itens da venda em lote, calcula valor_total (quantidade * valor_unitario)
     * e retorna a venda com a relação itens carregada.
     */
    public function storeItens(Venda $venda, array $itens): Venda
    {
        $venda->itens()->createMany(
            array_map(fn(VendaItemDTO $item) => [
                ...$item->toArray(),
                'valor_total' => round($item->quantidade * $item->valor_unitario, 2),
            ], $itens)
        );

        return $venda->load('itens');
    }

    public function update(Venda $venda, array $data): Venda
    {
        $venda->update($data);

        return $venda;
    }

    public function delete(Venda $venda)
    {
        DB::transaction(function () use ($venda) {
            $venda->itens()->delete();
            $venda->delete();
        });
    }
}
