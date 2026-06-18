<?php

namespace App\Http\Controllers\Purchases;

use App\Contracts\Services\Purchases\PurchaseServiceInterface;
use App\DTOs\Purchases\PurchaseDTO;
use App\Classes\ApiResponse;
use App\Http\Requests\Purchases\PurchaseCreateRequest;
use App\Http\Requests\Purchases\PurchaseUpdateRequest;
use App\Http\Requests\Purchases\FilterPurchaseIndexRequest;
use App\Models\Purchases\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseServiceInterface $service,
    ) {}

    public function index(FilterPurchaseIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Compras recuperados com sucesso');
    }

    public function show(Purchase $purchase): JsonResponse
    {
        $purchaseResponse = $this->service->show($purchase);

        return ApiResponse::success($purchaseResponse, 'Compra recuperado com sucesso');
    }

    public function store(PurchaseCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $purchaseResponse = $this->service->store(PurchaseDTO::fromCreateRequest($data));

        return ApiResponse::created($purchaseResponse, 'Compra criado com sucesso');
    }

    public function update(Purchase $purchase, PurchaseUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $purchaseResponse = $this->service->update($purchase, PurchaseDTO::fromUpdateRequest($data));

        return ApiResponse::success($purchaseResponse, 'Compra atualizado com sucesso');
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $this->service->delete($purchase);

        return ApiResponse::success(null, 'Compra deletado com sucesso');
    }
}
