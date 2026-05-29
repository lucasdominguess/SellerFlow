<?php

namespace App\Http\Controllers\Accout;

use App\Classes\ApiResponse;
use App\Contracts\Services\Accout\UserServiceInterface;
use App\DTOs\Accout\UserDTO;
use App\Http\Requests\Accout\FilterUserIndexRequest;
use App\Http\Requests\Accout\UserCreateRequest;
use App\Http\Requests\Accout\UserUpdateRequest;
use App\Models\Accout\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $service,
    ) {}


    public function index(FilterUserIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $users = $this->service->index($data['perPage'], $data['page'], $data['filters']);

        return ApiResponse::paginated($users, null, 'Usuarios recuperados com sucesso');
    }

    public function show(User $user): JsonResponse
    {
        $user =$this->service->show($user);
        return ApiResponse::success($user,'Usuario recuperado com sucesso');
    }

    public function store(UserCreateRequest $request): JsonResponse
    {
        $dataValidated = $request->validated();
        $user = $this->service->store(UserDTO::fromRequest($dataValidated));
        return ApiResponse::created($user,'Usuario criado com sucesso');
    }

    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $dataValidated = $request->validated();
        $userResponse = $this->service->update($user, UserDTO::fromRequest($dataValidated));

        return ApiResponse::success($userResponse, 'Usuario atualizado com sucesso');
    }

    public function destroy(User $user): JsonResponse
    {
        $this->service->delete($user);
        return ApiResponse::success(null,'Usuario deletado com sucesso');
    }
}
