<?php

namespace App\Repositories\Purchases;

use App\Contracts\Repositories\Purchases\CompraRepositoryInterface;
use App\DTOs\Purchases\CompraItemDTO;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompraRepository implements CompraRepositoryInterface
{
    public function __construct(
        private Compra $compraModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->compraModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['fornecedor_id'])) {
        //     $query->where('fornecedor_id', $filters['fornecedor_id']);
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Compra $compra): Compra
    {
        return $compra->load('itens');
    }

    public function store(array $data): Compra
    {
        return $this->compraModel->create($data);
    }

    /**
     * @param CompraItemDTO[] $itens
     * Cria os itens da compra em lote, calcula valor_total (quantidade * valor_unitario)
     * e retorna a compra com a relação itens carregada.
     */
    public function storeItens(Compra $compra, array $itens): Compra
    {
        $compra->itens()->createMany(
            array_map(fn(CompraItemDTO $item) => [
                ...$item->toArray(),
                'valor_total' => round($item->quantidade * $item->valor_unitario, 2),
            ], $itens)
        );

        return $compra->load('itens');
    }

    public function update(Compra $compra, array $data): Compra
    {
        $compra->update($data);

        return $compra;
    }

    public function delete(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            $compra->itens()->delete();
            $compra->delete();
        });
    }
}
