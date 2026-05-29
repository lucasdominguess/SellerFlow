<?php

namespace App\Http\Controllers\Accout;

use App\Classes\ApiResponse;
use App\Contracts\Services\Accout\StoreServiceInterface;
use App\DTOs\Accout\StoreDTO;
use App\Http\Requests\Accout\FilterStoreIndexRequest;
use App\Http\Requests\Accout\StoreCreateRequest;
use App\Http\Requests\Accout\StoreUpdateRequest;
use App\Models\Accout\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StoreController extends Controller
{
    public function __construct(
        private StoreServiceInterface $service,
    ) {}

    public function index(FilterStoreIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, null, 'Stores recuperados com sucesso');
    }

    public function show(Store $store): JsonResponse
    {
        $storeResponse = $this->service->show($store);

        return ApiResponse::success($storeResponse, 'Store recuperado com sucesso');
    }

    public function store(StoreCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $storeResponse = $this->service->store(StoreDTO::fromRequest($data));

        return ApiResponse::created($storeResponse, 'Store criado com sucesso');
    }

    public function update(Store $store, StoreUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $storeResponse = $this->service->update($store, StoreDTO::fromRequest($data));

        return ApiResponse::success($storeResponse, 'Store atualizado com sucesso');
    }

    public function destroy(Store $store): JsonResponse
    {
        $this->service->delete($store);

        return ApiResponse::success(null, 'Store deletado com sucesso');
    }
}
