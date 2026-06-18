<?php

namespace App\Http\Controllers\Finance;

use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\DTOs\Finance\AccountReceivableDTO;
use App\Classes\ApiResponse;
use App\Http\Requests\Finance\AccountReceivableCreateRequest;
use App\Http\Requests\Finance\AccountReceivableUpdateRequest;
use App\Http\Requests\Finance\FilterAccountReceivableIndexRequest;
use App\Models\Finance\AccountReceivable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AccountReceivableController extends Controller
{
    public function __construct(
        private AccountReceivableServiceInterface $service,
    ) {}

    public function index(FilterAccountReceivableIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, message: 'AccountReceivables recuperados com sucesso');
    }

    public function show(AccountReceivable $accountReceivable): JsonResponse
    {
        $accountReceivableResponse = $this->service->show($accountReceivable);

        return ApiResponse::success($accountReceivableResponse, 'AccountReceivable recuperado com sucesso');
    }

    public function store(AccountReceivableCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $accountReceivableResponse = $this->service->store(AccountReceivableDTO::fromRequest($data));

        return ApiResponse::created($accountReceivableResponse, 'AccountReceivable criado com sucesso');
    }

    public function update(AccountReceivable $accountReceivable, AccountReceivableUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $accountReceivableResponse = $this->service->update($accountReceivable, AccountReceivableDTO::fromRequest($data));

        return ApiResponse::success($accountReceivableResponse, 'AccountReceivable atualizado com sucesso');
    }

    public function destroy(AccountReceivable $accountReceivable): JsonResponse
    {
        $this->service->delete($accountReceivable);

        return ApiResponse::success(null, 'AccountReceivable deletado com sucesso');
    }
}
