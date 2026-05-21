<?php

namespace App\Http\Controllers\Accout;

use App\Classes\ApiResponse;
use App\Contracts\Services\Accout\UserStoreServiceInterface;
use App\DTOs\Accout\UserStoreDTO;
use App\Http\Requests\Accout\FilterUserStoreIndexRequest;
use App\Http\Requests\Accout\UserStoreCreateRequest;
use App\Http\Requests\Accout\UserStoreUpdateRequest;
use App\Models\Accout\UserStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserStoreController extends Controller
{
    public function __construct(
        private UserStoreServiceInterface $service,
    ) {}

    public function index(FilterUserStoreIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $paginator = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($paginator, null, 'UserStores recuperados com sucesso');
    }

    public function show(UserStore $userStore): JsonResponse
    {
        $userStoreResponse = $this->service->show($userStore);

        return ApiResponse::success($userStoreResponse, 'UserStore recuperado com sucesso');
    }

    public function store(UserStoreCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userStoreResponse = $this->service->store(UserStoreDTO::fromRequest($data));

        return ApiResponse::created($userStoreResponse, 'UserStore criado com sucesso');
    }

    public function update(UserStore $userStore, UserStoreUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $userStoreResponse = $this->service->update($userStore, UserStoreDTO::fromRequest($data));

        return ApiResponse::success($userStoreResponse, 'UserStore atualizado com sucesso');
    }

    public function delete(UserStore $userStore): JsonResponse
    {
        $this->service->delete($userStore);

        return ApiResponse::success(null, 'UserStore deletado com sucesso');
    }
}
