<?php

namespace App\Http\Controllers\Business;

use App\Classes\ApiResponse;
use App\Contracts\Services\Business\SupplierServiceInterface;
use App\DTOs\Business\SupplierDTO;
use App\Http\Requests\Business\FilterSupplierIndexRequest;
use App\Http\Requests\Business\SupplierCreateRequest;
use App\Http\Requests\Business\SupplierUpdateRequest;
use App\Models\Business\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierServiceInterface $service,
    ) {}

    public function index(FilterSupplierIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Fornecedores recuperados com sucesso');
    }

    public function show(Supplier $supplier): JsonResponse
    {
        $supplierResponse = $this->service->show($supplier);

        return ApiResponse::success($supplierResponse, 'Fornecedor recuperado com sucesso');
    }

    public function store(SupplierCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $supplierResponse = $this->service->store(SupplierDTO::fromRequest($data));

        return ApiResponse::created($supplierResponse, 'Fornecedor criado com sucesso');
    }

    public function update(Supplier $supplier, SupplierUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $supplierResponse = $this->service->update($supplier, SupplierDTO::fromRequest($data));

        return ApiResponse::success($supplierResponse, 'Fornecedor atualizado com sucesso');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->service->delete($supplier);

        return ApiResponse::success(null, 'Fornecedor deletado com sucesso');
    }
}
