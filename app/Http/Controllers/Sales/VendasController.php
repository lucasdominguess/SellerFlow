<?php

namespace App\Http\Controllers\Sales;

use App\Classes\ApiResponse;
use App\Contracts\Services\Sales\VendasServiceInterface;
use App\DTOs\Sales\VendasDTO;
use App\Http\Requests\Sales\FilterVendasIndexRequest;
use App\Http\Requests\Sales\VendasCreateRequest;
use App\Http\Requests\Sales\VendasUpdateRequest;
use App\Models\Sales\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class VendasController extends Controller
{
    public function __construct(
        private VendasServiceInterface $service,
    ) {}

    public function index(FilterVendasIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Vendas recuperadas com sucesso');
    }

    public function show(Sale $venda): JsonResponse
    {
        $vendaResponse = $this->service->show($venda);

        return ApiResponse::success($vendaResponse, message: 'Venda recuperada com sucesso');
    }

    public function store(VendasCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $vendaResponse = $this->service->store(VendasDTO::fromCreateRequest($data));

        return ApiResponse::created($vendaResponse, 'Venda criada com sucesso');
    }

    public function update(Sale $venda, VendasUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $vendaResponse = $this->service->update($venda, VendasDTO::fromUpdateRequest($data));

        return ApiResponse::success($vendaResponse, 'Venda atualizada com sucesso');
    }

    public function destroy(Sale $venda): JsonResponse
    {
        $this->service->delete($venda);

        return ApiResponse::success(null, 'Venda deletada com sucesso');
    }
}
