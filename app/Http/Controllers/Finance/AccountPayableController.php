<?php

namespace App\Http\Controllers\Finance;

use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\DTOs\Finance\AccountPayableDTO;
use App\Classes\ApiResponse;
use App\Http\Requests\Finance\AccountPayableCreateRequest;
use App\Http\Requests\Finance\AccountPayableUpdateRequest;
use App\Http\Requests\Finance\FilterAccountPayableIndexRequest;
use App\Models\Finance\AccountPayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AccountPayableController extends Controller
{
    public function __construct(
        private AccountPayableServiceInterface $service,
    ) {}

    public function index(FilterAccountPayableIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, 'AccountPayables recuperados com sucesso');
    }

    public function show(AccountPayable $accountPayable): JsonResponse
    {
        $accountPayableResponse = $this->service->show($accountPayable);

        return ApiResponse::success($accountPayableResponse, 'AccountPayable recuperado com sucesso');
    }

    public function store(AccountPayableCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $accountPayableResponse = $this->service->store(AccountPayableDTO::fromRequest($data));

        return ApiResponse::created($accountPayableResponse, 'AccountPayable criado com sucesso');
    }

    public function update(AccountPayable $accountPayable, AccountPayableUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $accountPayableResponse = $this->service->update($accountPayable, AccountPayableDTO::fromRequest($data));

        return ApiResponse::success($accountPayableResponse, 'AccountPayable atualizado com sucesso');
    }

    public function destroy(AccountPayable $accountPayable): JsonResponse
    {
        $this->service->delete($accountPayable);

        return ApiResponse::success(null, 'AccountPayable deletado com sucesso');
    }
}
