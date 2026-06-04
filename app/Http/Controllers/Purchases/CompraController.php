<?php

namespace App\Http\Controllers\Purchases;

use App\Contracts\Services\Purchases\CompraServiceInterface;
use App\DTOs\Purchases\CompraDTO;
use App\Classes\ApiResponse;
use App\Http\Requests\Purchases\CompraCreateRequest;
use App\Http\Requests\Purchases\CompraUpdateRequest;
use App\Http\Requests\Purchases\FilterCompraIndexRequest;
use App\Models\Purchases\Compra;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CompraController extends Controller
{
    public function __construct(
        private CompraServiceInterface $service,
    ) {}

    public function index(FilterCompraIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Compras recuperados com sucesso');
    }

    public function show(Compra $compra): JsonResponse
    {
        $compraResponse = $this->service->show($compra);

        return ApiResponse::success($compraResponse, 'Compra recuperado com sucesso');
    }

    public function store(CompraCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $compraResponse = $this->service->store(CompraDTO::fromCreateRequest($data));

        return ApiResponse::created($compraResponse, 'Compra criado com sucesso');
    }

    public function update(Compra $compra, CompraUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $compraResponse = $this->service->update($compra, CompraDTO::fromUpdateRequest($data));

        return ApiResponse::success($compraResponse, 'Compra atualizado com sucesso');
    }

    public function destroy(Compra $compra): JsonResponse
    {
        $this->service->delete($compra);

        return ApiResponse::success(null, 'Compra deletado com sucesso');
    }
}
