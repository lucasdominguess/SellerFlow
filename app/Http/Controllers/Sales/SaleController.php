<?php

namespace App\Http\Controllers\Sales;

use App\Classes\ApiResponse;
use App\Contracts\Services\Sales\SaleServiceInterface;
use App\DTOs\Sales\SaleDTO;
use App\Http\Requests\Sales\FilterSaleIndexRequest;
use App\Http\Requests\Sales\SaleCreateRequest;
use App\Http\Requests\Sales\SaleUpdateRequest;
use App\Models\Sales\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SaleController extends Controller
{
    public function __construct(
        private SaleServiceInterface $service,
    ) {}

    public function index(FilterSaleIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Vendas recuperadas com sucesso');
    }

    public function show(Sale $sale): JsonResponse
    {
        $saleResponse = $this->service->show($sale);

        return ApiResponse::success($saleResponse, message: 'Venda recuperada com sucesso');
    }

    public function store(SaleCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $saleResponse = $this->service->store(SaleDTO::fromCreateRequest($data));

        return ApiResponse::created($saleResponse, 'Venda criada com sucesso');
    }

    public function update(Sale $sale, SaleUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $saleResponse = $this->service->update($sale, SaleDTO::fromUpdateRequest($data));

        return ApiResponse::success($saleResponse, 'Venda atualizada com sucesso');
    }

    public function destroy(Sale $sale): JsonResponse
    {
        $this->service->delete($sale);

        return ApiResponse::success(null, 'Venda deletada com sucesso');
    }
}
