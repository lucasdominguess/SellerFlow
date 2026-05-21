<?php

namespace App\Services\Accout;

use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Contracts\Services\Accout\UserServiceInterface;
use App\DTOs\Accout\UserDTO;
use App\DTOs\Accout\UserResponseDTO;
use App\Models\Accout\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {
    }


    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $userPaginator = $this->repository->index($perPage, $page, $filters);

        $userPaginator->getCollection()->transform(function ($item) {
            return UserResponseDTO::fromModel($item)->toArray();
        });
        return $userPaginator;
    }

    public function show(User $user): UserResponseDTO
    {
        $user = $this->repository->show($user);
        return UserResponseDTO::fromModel($user);
    }

    public function store(UserDTO $dto): UserResponseDTO
    {
        $user =$this->repository->store($dto->toArray());
        return UserResponseDTO::fromModel($user);
    }

    public function update(User $user, UserDTO $dto): UserResponseDTO
    {
       $user = $this->repository->update($user,$dto->toArray());
       return UserResponseDTO::fromModel($user);
    }

    public function delete(User $user)
    {
      return $this->repository->delete($user);
    }
}
