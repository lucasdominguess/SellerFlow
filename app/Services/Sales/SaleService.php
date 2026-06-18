<?php

namespace App\Services\Sales;

use App\Contracts\Repositories\Sales\SaleRepositoryInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\Contracts\Services\Sales\SaleServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Sales\SaleDTO;
use App\DTOs\Sales\SaleResponseDTO;
use App\Enums\TransactionStatus;
use App\Models\Sales\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SaleService implements SaleServiceInterface
{

    public function __construct(
        private SaleRepositoryInterface $repository,
        private StockServiceInterface $stockService,
        private AccountReceivableServiceInterface $accountReceivableService
    ) {
    }

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return SaleResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Sale $sale): SaleResponseDTO
    {
        $sale = $this->repository->show($sale);

        return SaleResponseDTO::fromModel($sale);
    }

    public function store(SaleDTO $dto): SaleResponseDTO
    {
        $sale = DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            // valor_liquido é derivado, não vem do cliente: bruto - taxa marketplace - frete
            $data['valor_liquido'] = $this->calcularValorLiquido(
                $data['valor_bruto'] ?? 0,
                $data['taxa_marketplace'] ?? 0,
                $data['valor_frete'] ?? 0,
            );

            $sale = $this->repository->store($data);


          $this->repository->storeItens($sale, $dto->venda_itens);
          $this->stockService->proccessItensSale($sale, $dto->venda_itens);
          $this->accountReceivableService->proccessSale($sale);

            return $sale;
        });

        return SaleResponseDTO::fromModel($sale);
    }

    public function update(Sale $sale, SaleDTO $dto): SaleResponseDTO
    {
        $data = $dto->toArray();
        // recalcula só quando algum componente do valor mudou, usando o valor atual como fallback
        if (isset($data['valor_bruto']) || isset($data['taxa_marketplace']) || isset($data['valor_frete'])) {
            $data['valor_liquido'] = $this->calcularValorLiquido(
                $data['valor_bruto'] ?? (float) $sale->valor_bruto,
                $data['taxa_marketplace'] ?? (float) $sale->taxa_marketplace,
                $data['valor_frete'] ?? (float) $sale->valor_frete,
            );
        }

        // Sem mudança de status: update simples, sem transação nem propagação.
        if (! $this->isStatusChanging($sale, $dto)) {
            $sale = $this->repository->update($sale, $data);

            return SaleResponseDTO::fromModel($sale);
        }

        // Mudança de status sincroniza estoque e conta a receber — escrita múltipla, exige transação.
        $sale = DB::transaction(function () use ($sale, $data) {
            $sale = $this->repository->update($sale, $data);

            // cancelar a venda devolve a saída de estoque
            if ($sale->status === TransactionStatus::CANCELED) {
                $this->stockService->reverseItensSale($sale);
            }

            $this->accountReceivableService->syncStatusFromSale($sale);

            return $sale;
        });

        return SaleResponseDTO::fromModel($sale);
    }

    private function isStatusChanging(Sale $sale, SaleDTO $dto): bool
    {
        return $dto->status !== null && $sale->status?->value !== $dto->status;
    }

    private function calcularValorLiquido(float $bruto, float $taxa, float $frete): float
    {
        return round($bruto - $taxa - $frete, 2);
    }

    public function delete(Sale $sale)
    {
        return $this->repository->delete($sale);

    }
}
