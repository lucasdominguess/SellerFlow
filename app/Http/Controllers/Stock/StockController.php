<?php

namespace App\Http\Controllers\Stock;

use App\Classes\ApiResponse;
use App\Contracts\Services\Stock\StockServiceInterface;
use App\DTOs\Stock\StockDTO;
use App\Http\Requests\Stock\CheckStockQuantityRequest;
use App\Http\Requests\Stock\FilterStockIndexRequest;
use App\Http\Requests\Stock\StockCreateRequest;
use App\Http\Requests\Stock\StockInvestmentRequest;
use App\Http\Requests\Stock\StockUpdateRequest;
use App\Models\Stock\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    public function __construct(
        private StockServiceInterface $service,
    ) {}

    public function index(FilterStockIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, 'Stocks recuperados com sucesso');
    }

    public function show(Stock $stock): JsonResponse
    {
        $stockResponse = $this->service->show($stock);

        return ApiResponse::success($stockResponse, 'Stock recuperado com sucesso');
    }

    public function store(StockCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $stockResponse = $this->service->store(StockDTO::fromRequest($data));

        return ApiResponse::created($stockResponse, 'Stock criado com sucesso');
    }

    public function update(Stock $stock, StockUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $stockResponse = $this->service->update($stock, StockDTO::fromRequest($data));

        return ApiResponse::success($stockResponse, 'Stock atualizado com sucesso');
    }

    public function destroy(Stock $stock): JsonResponse
    {
        $this->service->delete($stock);

        return ApiResponse::success(null, 'Stock deletado com sucesso');
    }
    public function checkQuantityProductsInStock(CheckStockQuantityRequest $request): JsonResponse
    {
        $data = $request->validated();

        $paginator = $this->service->checkQuantityProductsInStock(
            companyId: $data['company_id'],
            productId: $data['product_id'] ?? null,
            productName: $data['product_name'] ?? null,
            sku: $data['sku'] ?? null,
            perPage: $data['perPage'],
            page: $data['page'],
        );

        return ApiResponse::paginated($paginator, null, 'Quantidade de produtos em estoque recuperada com sucesso');
    }

    public function investmentInStock(StockInvestmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        Log::info($data);
        
        $result = $this->service->stockInvestment(
            companyId: $data['company_id'],
            productId: $data['product_id'] ?? null,
            productName: $data['product_name'] ?? null,
            sku: $data['sku'] ?? null,
            perPage: $data['perPage'],
            page: $data['page'],
        );

        return ApiResponse::paginated(
            $result['paginator'],
            null,
            'Valor investido no estoque recuperado com sucesso',
            headers: [],
            extra: ['total_investido' => $result['total_investido']],
        );
    }
}
