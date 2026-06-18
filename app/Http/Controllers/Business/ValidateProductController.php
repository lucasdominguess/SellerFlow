<?php

namespace App\Http\Controllers\Business;

use App\Classes\ApiResponse;
use App\Contracts\Services\Business\ValidateProductServiceInterface;
use App\DTOs\Business\ValidateProductDTO;
use App\Http\Requests\Business\ValidateProduct\FilterValidateProductIndexRequest;
use App\Http\Requests\Business\ValidateProduct\ValidatedProductRequest;
use App\Http\Requests\Business\ValidateProduct\ValidateProductCreateRequest;
use App\Http\Requests\Business\ValidateProduct\ValidateProductUpdateRequest;
use App\Models\Business\ValidateProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ValidateProductController extends Controller
{
    public function __construct(
        private ValidateProductServiceInterface $service,
    ) {}

    public function index(FilterValidateProductIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'ValidateProducts recuperados com sucesso');
    }

    public function show(ValidateProduct $validateProduct): JsonResponse
    {
        $validateProductResponse = $this->service->show($validateProduct);

        return ApiResponse::success($validateProductResponse, 'ValidateProduct recuperado com sucesso');
    }

    public function store(ValidateProductCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $validateProductResponse = $this->service->store(ValidateProductDTO::fromRequest($data));

        return ApiResponse::created($validateProductResponse, 'ValidateProduct criado com sucesso');
    }

    public function update(ValidateProduct $validateProduct, ValidateProductUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $validateProductResponse = $this->service->update($validateProduct, ValidateProductDTO::fromRequest($data));

        return ApiResponse::success($validateProductResponse, 'ValidateProduct atualizado com sucesso');
    }

    public function destroy(ValidateProduct $validateProduct): JsonResponse
    {
        $this->service->delete($validateProduct);

        return ApiResponse::success(null, 'ValidateProduct deletado com sucesso');
    }
    public function validate(ValidatedProductRequest $request): JsonResponse
    {
        $validateProductResponse = $this->service->validate(ValidateProductDTO::fromRequest($request->validated()));

        return ApiResponse::success($validateProductResponse, 'ValidateProduct validado com sucesso');
    }

}
