<?php

namespace App\Services\Stock;

use App\Contracts\Repositories\Stock\StockBalanceRepositoryInterface;
use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Stock\StockBalanceDTO;
use App\DTOs\Stock\StockDTO;
use App\DTOs\Stock\StockInvestmentDTO;
use App\DTOs\Stock\StockResponseDTO;
use App\Enums\OriginType;
use App\Enums\TipoStock;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use App\Models\Stock\Stock;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockService implements StockServiceInterface
{
    public function __construct(
        private StockRepositoryInterface $repository,
        private StockBalanceRepositoryInterface $balanceRepository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return StockResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(Stock $stock): StockResponseDTO
    {
        $stock = $this->repository->show($stock);

        return StockResponseDTO::fromModel($stock);
    }

    public function store(StockDTO $dto): StockResponseDTO
    {
        $stock = $this->repository->store($dto->toArray());

        return StockResponseDTO::fromModel($stock);
    }

    public function update(Stock $stock, StockDTO $dto): StockResponseDTO
    {
        $stock = $this->repository->update($stock, $dto->toArray());

        return StockResponseDTO::fromModel($stock);
    }

    public function delete(Stock $stock)
    {
        return $this->repository->delete($stock);
    }


    public function proccessItensPurchase(Purchase $compra,array $itens)
    {
        array_map(function ($item) use ($compra) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $compra->user_id,
                tipo:TipoStock::ENTRADA->value,
                quantidade: $item->quantidade,
                origem_tipo: OriginType::COMPRA->value,
                origem_id:  $compra->id,
                observacao: $compra->observacao,
                company_id:   $compra->company_id
            );
            $this->repository->store($dto->toArray());
        }, $itens);
    }

    public function proccessItensSale(Sale $venda,array $itens)
    {
        array_map(function ($item) use ($venda) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $venda->user_id,
                tipo: TipoStock::SAIDA->value,
                quantidade: $item->quantidade,
                origem_tipo: OriginType::VENDA->value,
                origem_id:  $venda->id,
                observacao: $venda->observacao,
                company_id:   $venda->company_id
            );
            $this->repository->store($dto->toArray());
        }, $itens);
    }

    // Estorno do cancelamento da compra: cada ENTRADA original é anulada por uma SAIDA
    // de mesma quantidade. O StockObserver recalcula stock_balances automaticamente.
    public function reverseItensPurchase(Purchase $compra)
    {
        $compra->loadMissing('itens');

        $compra->itens->each(function ($item) use ($compra) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $compra->user_id,
                tipo: TipoStock::SAIDA->value,
                quantidade: $item->quantidade,
                origem_tipo: OriginType::COMPRA->value,
                origem_id: $compra->id,
                observacao: 'Estorno: cancelamento da compra #' . $compra->id,
                company_id: $compra->company_id
            );
            $this->repository->store($dto->toArray());
        });
    }

    // Estorno do cancelamento da venda: cada SAIDA original é anulada por uma ENTRADA
    // de mesma quantidade, devolvendo o saldo ao estoque.
    public function reverseItensSale(Sale $venda)
    {
        $venda->loadMissing('itens');

        $venda->itens->each(function ($item) use ($venda) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $venda->user_id,
                tipo: TipoStock::ENTRADA->value,
                quantidade: $item->quantidade,
                origem_tipo: OriginType::VENDA->value,
                origem_id: $venda->id,
                observacao: 'Estorno: cancelamento da venda #' . $venda->id,
                company_id: $venda->company_id
            );
            $this->repository->store($dto->toArray());
        });
    }
    public function proccessItensAdjustment(StockAdjustment $stockAdjustment)
    {
        $dto = new StockDTO(
            product_id: $stockAdjustment->product_id,
            user_id: $stockAdjustment->user_id,
            tipo: TipoStock::AJUSTE->value,
            // stock_movements.quantidade é sempre positivo; o sinal de
            // stock_adjustments.quantidade indica entrada (+) ou saída (-)
            quantidade: abs($stockAdjustment->quantidade),
            origem_tipo: OriginType::AJUSTE_MANUAL->value,
            origem_id: $stockAdjustment->id,
            observacao: $stockAdjustment->observacao,
            company_id: $stockAdjustment->company_id
        );
        $this->repository->store($dto->toArray());
    }
    public function checkQuantityProductsInStock(int $companyId, ?int $productId = null, ?string $productName = null, ?string $sku = null, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $paginator = $this->balanceRepository->paginate($companyId, $productId, $productName, $sku, $perPage, $page);

        $paginator->getCollection()->transform(
            fn ($row) => StockBalanceDTO::fromQueryResult($row)->toArray()
        );

        return $paginator;
    }

    public function stockInvestment(int $companyId, ?int $productId = null, ?string $productName = null, ?string $sku = null, int $perPage = 15, int $page = 1): array
    {
        $paginator = $this->balanceRepository->paginateInvestment($companyId, $productId, $productName, $sku, $perPage, $page);

        $paginator->getCollection()->transform(
            fn ($row) => StockInvestmentDTO::fromQueryResult($row)->toArray()
        );

        return [
            'total_investido' => $this->balanceRepository->totalInvested($companyId, $productId, $productName, $sku),
            'paginator'       => $paginator,
        ];
    }
}
