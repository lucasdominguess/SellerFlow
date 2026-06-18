<?php

namespace App\Services\Purchases;

use App\Contracts\Repositories\Purchases\PurchaseRepositoryInterface;
use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\Contracts\Services\Purchases\PurchaseServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Purchases\PurchaseDTO;
use App\DTOs\Purchases\PurchaseResponseDTO;
use App\Enums\TransactionStatus;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseService implements PurchaseServiceInterface
{
    public function __construct(
        private PurchaseRepositoryInterface $repository,
        private StockServiceInterface $stockService,
        private AccountPayableServiceInterface $accountPayableService
    ) {
    }

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return PurchaseResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Purchase $purchase): PurchaseResponseDTO
    {
        $purchase = $this->repository->show($purchase);

        return PurchaseResponseDTO::fromModel($purchase);
    }

    public function store(PurchaseDTO $dto): PurchaseResponseDTO
    {
        $purchase = DB::transaction(function () use ($dto) {
            $data = $dto->toArray();

            $data['valor_total'] = $this->calcularValorTotal($dto->itens);

            $purchase = $this->repository->store($data);

            $this->repository->storeItens($purchase, $dto->itens);

            $this->stockService->proccessItensPurchase($purchase, $dto->itens);
            $this->accountPayableService->proccessPurchase($purchase);
            return $purchase;
        });


        return PurchaseResponseDTO::fromModel($purchase);
    }

    public function update(Purchase $purchase, PurchaseDTO $dto): PurchaseResponseDTO
    {
        // Sem mudança de status: update simples, sem transação nem propagação.
        if (! $this->isStatusChanging($purchase, $dto)) {
            $purchase = $this->repository->update($purchase, $dto->toArray());

            return PurchaseResponseDTO::fromModel($purchase);
        }

        // Mudança de status sincroniza estoque e conta a pagar — escrita múltipla, exige transação.
        $purchase = DB::transaction(function () use ($purchase, $dto) {
            $purchase = $this->repository->update($purchase, $dto->toArray());

            // cancelar a compra estorna a entrada de estoque
            if ($purchase->status === TransactionStatus::CANCELED) {
                $this->stockService->reverseItensPurchase($purchase);
            }

            $this->accountPayableService->syncStatusFromPurchase($purchase);

            return $purchase;
        });

        return PurchaseResponseDTO::fromModel($purchase);
    }

    private function isStatusChanging(Purchase $purchase, PurchaseDTO $dto): bool
    {
        return $dto->status !== null && $purchase->status?->value !== $dto->status;
    }

    public function delete(Purchase $purchase)
    {
        return $this->repository->delete($purchase);
    }

    // valor_total da compra é a soma de (quantidade * valor_unitario) de cada item
    private function calcularValorTotal(array $itens): float
    {
        return round(array_reduce($itens, function (float $carry, $item) {
            return $carry + ($item->quantidade * $item->valor_unitario);
        }, 0.0), 2);
    }
}
