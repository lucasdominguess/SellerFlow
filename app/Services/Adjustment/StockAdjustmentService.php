<?php

namespace App\Services\Adjustment;

use App\Contracts\Repositories\Adjustment\StockAdjustmentRepositoryInterface;
use App\Contracts\Services\Adjustment\StockAdjustmentServiceInterface;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Adjustment\StockAdjustmentDTO;
use App\DTOs\Adjustment\StockAdjustmentItemDTO;
use App\DTOs\Adjustment\StockAdjustmentResponseDTO;
use App\Models\Stock\StockAdjustment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService implements StockAdjustmentServiceInterface
{
    public function __construct(
        private StockAdjustmentRepositoryInterface $repository,
        private StockServiceInterface $stockService
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return StockAdjustmentResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(StockAdjustment $stockAdjustment): StockAdjustmentResponseDTO
    {
        $stockAdjustment = $this->repository->show($stockAdjustment);

        return StockAdjustmentResponseDTO::fromModel($stockAdjustment);
    }

    public function store(StockAdjustmentDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            return array_map(function (StockAdjustmentItemDTO $item) use ($dto) {
                $stockAdjustment = $this->repository->store([
                    ...$item->toArray(),
                    'company_id' => $dto->company_id,
                    'user_id' => $dto->user_id,
                ]);

                $this->stockService->proccessItensAdjustment($stockAdjustment);

                return StockAdjustmentResponseDTO::fromModel($stockAdjustment)->toArray();
            }, $dto->itens);
        });
    }
}
