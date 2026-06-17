<?php

namespace App\Http\Controllers\Business;

use App\Classes\ApiResponse;
use App\Contracts\Services\Business\FornecedorServiceInterface;
use App\DTOs\Business\FornecedorDTO;
use App\Http\Requests\Business\FilterFornecedorIndexRequest;
use App\Http\Requests\Business\FornecedorCreateRequest;
use App\Http\Requests\Business\FornecedorUpdateRequest;
use App\Models\Business\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class FornecedorController extends Controller
{
    public function __construct(
        private FornecedorServiceInterface $service,
    ) {}

    public function index(FilterFornecedorIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'Fornecedors recuperados com sucesso');
    }

    public function show(Supplier $fornecedor): JsonResponse
    {
        $fornecedorResponse = $this->service->show($fornecedor);

        return ApiResponse::success($fornecedorResponse, 'Fornecedor recuperado com sucesso');
    }

    public function store(FornecedorCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $fornecedorResponse = $this->service->store(FornecedorDTO::fromRequest($data));

        return ApiResponse::created($fornecedorResponse, 'Fornecedor criado com sucesso');
    }

    public function update(Supplier $fornecedor, FornecedorUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $fornecedorResponse = $this->service->update($fornecedor, FornecedorDTO::fromRequest($data));

        return ApiResponse::success($fornecedorResponse, 'Fornecedor atualizado com sucesso');
    }

    public function destroy(Supplier $fornecedor): JsonResponse
    {
        $this->service->delete($fornecedor);

        return ApiResponse::success(null, 'Fornecedor deletado com sucesso');
    }
}
