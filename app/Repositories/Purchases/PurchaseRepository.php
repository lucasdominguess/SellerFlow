<?php

namespace App\Repositories\Purchases;

use App\Contracts\Repositories\Purchases\PurchaseRepositoryInterface;
use App\DTOs\Purchases\PurchaseItemDTO;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function __construct(
        private Purchase $purchaseModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->purchaseModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['fornecedor_id'])) {
        //     $query->where('fornecedor_id', $filters['fornecedor_id']);
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Purchase $purchase): Purchase
    {
        return $purchase->load('itens');
    }

    public function store(array $data): Purchase
    {
        return $this->purchaseModel->create($data);
    }

    /**
     * @param PurchaseItemDTO[] $itens
     * Cria os itens da compra em lote, calcula valor_total (quantidade * valor_unitario)
     * e retorna a compra com a relação itens carregada.
     */
    public function storeItens(Purchase $purchase, array $itens): Purchase
    {
        $purchase->itens()->createMany(
            array_map(fn(PurchaseItemDTO $item) => [
                ...$item->toArray(),
                'valor_total' => round($item->quantidade * $item->valor_unitario, 2),
            ], $itens)
        );

        return $purchase->load('itens');
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        $purchase->update($data);

        return $purchase;
    }

    public function delete(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            $purchase->itens()->delete();
            $purchase->delete();
        });
    }
}
