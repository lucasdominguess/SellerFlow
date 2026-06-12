<?php

namespace App\Services\Purchases;

use App\Contracts\Repositories\Purchases\CompraRepositoryInterface;
use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\Contracts\Services\Purchases\CompraServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Purchases\CompraResponseDTO;
use App\Enums\TransactionStatus;
use App\Models\Purchases\Compra;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompraService implements CompraServiceInterface
{
    public function __construct(
        private CompraRepositoryInterface $repository,
        private StockServiceInterface $stockService,
        private AccountPayableServiceInterface $accountPayableService
    ) {
    }

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return CompraResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Compra $compra): CompraResponseDTO
    {
        $compra = $this->repository->show($compra);

        return CompraResponseDTO::fromModel($compra);
    }

    public function store(CompraDTO $dto): CompraResponseDTO
    {
        $compra = DB::transaction(function () use ($dto) {
            $data = $dto->toArray();

            $data['valor_total'] = $this->calcularValorTotal($dto->itens);

            $compra = $this->repository->store($data);

            $this->repository->storeItens($compra, $dto->itens);

            $this->stockService->proccessItensPurchase($compra, $dto->itens);
            $this->accountPayableService->proccessPurchase($compra);
            return $compra;
        });


        return CompraResponseDTO::fromModel($compra);
    }

    public function update(Compra $compra, CompraDTO $dto): CompraResponseDTO
    {
        // Sem mudança de status: update simples, sem transação nem propagação.
        if (! $this->isStatusChanging($compra, $dto)) {
            $compra = $this->repository->update($compra, $dto->toArray());

            return CompraResponseDTO::fromModel($compra);
        }

        // Mudança de status sincroniza estoque e conta a pagar — escrita múltipla, exige transação.
        $compra = DB::transaction(function () use ($compra, $dto) {
            $compra = $this->repository->update($compra, $dto->toArray());

            // cancelar a compra estorna a entrada de estoque
            if ($compra->status === TransactionStatus::CANCELED) {
                $this->stockService->reverseItensPurchase($compra);
            }

            $this->accountPayableService->syncStatusFromPurchase($compra);

            return $compra;
        });

        return CompraResponseDTO::fromModel($compra);
    }

    private function isStatusChanging(Compra $compra, CompraDTO $dto): bool
    {
        return $dto->status !== null && $compra->status?->value !== $dto->status;
    }

    public function delete(Compra $compra)
    {
        return $this->repository->delete($compra);
    }

    // valor_total da compra é a soma de (quantidade * valor_unitario) de cada item
    private function calcularValorTotal(array $itens): float
    {
        return round(array_reduce($itens, function (float $carry, $item) {
            return $carry + ($item->quantidade * $item->valor_unitario);
        }, 0.0), 2);
    }
}
