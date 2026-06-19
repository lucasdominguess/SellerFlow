<?php

namespace App\Repositories\Sales;

use App\Classes\AuthContext;
use App\Contracts\Repositories\Sales\SaleRepositoryInterface;
use App\DTOs\Sales\SaleItemDTO;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SaleRepository implements SaleRepositoryInterface
{
    public function __construct(
        private Sale $saleModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        // O isolamento por empresa é aplicado pelo CompanyScope (global scope do model).
        $query = $this->saleModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Sale $sale): Sale
    {
        return $sale->load('itens');
    }

    public function store(array $data): Sale
    {
        return $this->saleModel->create($data);
    }

    /**
     * @param SaleItemDTO[] $itens
     * Cria os itens da venda em lote, calcula valor_total (quantidade * valor_unitario)
     * e retorna a venda com a relação itens carregada.
     */
    public function storeItens(Sale $sale, array $itens): Sale
    {
        $sale->itens()->createMany(
            array_map(fn(SaleItemDTO $item) => [
                ...$item->toArray(),
                'valor_total' => round($item->quantidade * $item->valor_unitario, 2),
            ], $itens)
        );

        return $sale->load('itens');
    }

    public function update(Sale $sale, array $data): Sale
    {
        $sale->update($data);

        return $sale;
    }

    public function delete(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->itens()->delete();
            $sale->delete();
        });
    }
}
