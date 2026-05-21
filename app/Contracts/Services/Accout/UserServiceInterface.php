<?php

namespace App\Contracts\Services\Accout;

use App\DTOs\Accout\UserDTO;
use App\DTOs\Accout\UserResponseDTO;
use App\Models\Accout\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator;

    public function show(User $user): UserResponseDTO;

    public function store(UserDTO $dto): UserResponseDTO;

    public function update(User $user, UserDTO $dto): UserResponseDTO;

    public function delete(User $user);

}
