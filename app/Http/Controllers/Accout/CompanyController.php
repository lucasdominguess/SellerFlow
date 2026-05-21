<?php

namespace App\Http\Controllers\Accout;

use App\Classes\ApiResponse;
use App\Contracts\Services\Accout\CompanyServiceInterface;
use App\DTOs\Accout\CompanyDTO;
use App\Http\Requests\Accout\CompanyCreateRequest;
use App\Http\Requests\Accout\CompanyUpdateRequest;
use App\Http\Requests\Accout\FilterCompanyIndexRequest;
use App\Models\Accout\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyServiceInterface $service,
    ) {}

    public function index(FilterCompanyIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, null, 'Companys recuperados com sucesso');
    }

    public function show(Company $company): JsonResponse
    {
        $companyResponse = $this->service->show($company);

        return ApiResponse::success($companyResponse, 'Company recuperado com sucesso');
    }

    public function store(CompanyCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $companyResponse = $this->service->store(CompanyDTO::fromRequest($data));

        return ApiResponse::created($companyResponse, 'Company criado com sucesso');
    }

    public function update(Company $company, CompanyUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $companyResponse = $this->service->update($company, CompanyDTO::fromRequest($data));

        return ApiResponse::success($companyResponse, 'Company atualizado com sucesso');
    }

    public function delete(Company $company): JsonResponse
    {
        $this->service->delete($company);

        return ApiResponse::success(null, 'Company deletado com sucesso');
    }
}
