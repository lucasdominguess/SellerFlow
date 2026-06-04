<?php

namespace App\Services\Stock;

use App\Classes\AuthContext;
use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Purchases\CompraDTO;
use App\DTOs\Sales\VendasDTO;
use App\DTOs\Stock\StockDTO;
use App\DTOs\Stock\StockResponseDTO;
use App\Enums\OriginType;
use App\Models\Sales\Venda;
use App\Models\Stock\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockService implements StockServiceInterface
{
    public function __construct(
        private StockRepositoryInterface $repository,
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


    public function proccessItensPurchase(CompraDTO $compraDTO,array $itens)
    {
        array_map(function ($item) use ($compraDTO) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $compraDTO->user_id,
                tipo: 'entrada',
                quantidade: $item->quantidade,
                origem_tipo: OriginType::COMPRA->value,
                origem_id: 1,
                observacao: $compraDTO->observacao,
                company_id:   $compraDTO->company_id
            );
            $this->repository->store($dto->toArray());
        }, $itens);
    }
    public function proccessItensSale(VendasDTO $vendaDTO,array $itens)
    {
        array_map(function ($item) use ($vendaDTO) {
            $dto = new StockDTO(
                product_id: $item->product_id,
                user_id: $vendaDTO->user_id,
                tipo: 'saida',
                quantidade: $item->quantidade,
                origem_tipo: OriginType::VENDA->value,
                origem_id: 1,
                observacao: $vendaDTO->observacao,
                company_id:   $vendaDTO->company_id
            );
            $this->repository->store($dto->toArray());
        }, $itens);
    }
}
