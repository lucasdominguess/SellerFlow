<?php

namespace App\Http\Controllers\Business;

use App\Classes\ApiResponse;
use App\Contracts\Services\Business\ProductServiceInterface;
use App\DTOs\Business\ProductDTO;
use App\Http\Requests\Business\FilterProductIndexRequest;
use App\Http\Requests\Business\ProductCreateRequest;
use App\Http\Requests\Business\ProductUpdateRequest;
use App\Models\Business\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function __construct(
        private ProductServiceInterface $service,
    ) {}

    public function index(FilterProductIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, null, 'Products recuperados com sucesso');
    }

    public function show(Product $product): JsonResponse
    {
        $productResponse = $this->service->show($product);

        return ApiResponse::success($productResponse, 'Product recuperado com sucesso');
    }

    public function store(ProductCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $productResponse = $this->service->store(ProductDTO::fromRequest($data));

        return ApiResponse::created($productResponse, 'Product criado com sucesso');
    }

    public function update(Product $product, ProductUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $productResponse = $this->service->update($product, ProductDTO::fromRequest($data));

        return ApiResponse::success($productResponse, 'Product atualizado com sucesso');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);

        return ApiResponse::success(null, 'Product deletado com sucesso');
    }
}
