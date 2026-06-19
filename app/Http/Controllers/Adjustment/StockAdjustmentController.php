<?php

namespace App\Http\Controllers\Adjustment;

use App\Classes\ApiResponse;
use App\Contracts\Services\Adjustment\StockAdjustmentServiceInterface;
use App\DTOs\Adjustment\StockAdjustmentDTO;
use App\Http\Requests\Adjustment\FilterStockAdjustmentIndexRequest;
use App\Http\Requests\Adjustment\StockAdjustmentCreateRequest;
use App\Models\Stock\StockAdjustment;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private StockAdjustmentServiceInterface $service,
    ) {}

    public function index(FilterStockAdjustmentIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'StockAdjustments recuperados com sucesso');
    }

    public function show(StockAdjustment $stockAdjustment): JsonResponse
    {
        $stockAdjustmentResponse = $this->service->show($stockAdjustment);

        return ApiResponse::success($stockAdjustmentResponse, 'StockAdjustment recuperado com sucesso');
    }

    public function store(StockAdjustmentCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $stockAdjustmentResponse = $this->service->store(StockAdjustmentDTO::fromRequest($data));

        return ApiResponse::created($stockAdjustmentResponse, 'StockAdjustment criado com sucesso');
    }
}
