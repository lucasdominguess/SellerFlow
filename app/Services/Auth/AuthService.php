<?php

namespace App\Services\Auth;

use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Contracts\Services\Auth\AuthServiceInterface;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\LoginResponseDTO;
use App\DTOs\Auth\RegisterResponseDTO;
use App\DTOs\AUth\RegisterDTO;
use App\Enums\Status;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\UserInactiveException;

class AuthService implements AuthServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function login(LoginDTO $loginDTO): LoginResponseDTO
    {
        $token = auth('api')->attempt([
            'email' => $loginDTO->email,
            'password' => $loginDTO->password,

        ]);

        if (!$token) {
            throw new InvalidCredentialsException();
        }

        $user = auth('api')->user();


        if ($user->status_id !== Status::ACTIVE->value) {
            throw new UserInactiveException();
        }


        return LoginResponseDTO::fromToken($token, $user);
    }

    public function register(RegisterDTO $registerDTO): RegisterResponseDTO
    {
        $user = $this->userRepository->store($registerDTO->user->toArray(), $registerDTO->company->toArray());

        return RegisterResponseDTO::fromModel($user);
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refreshToken(): LoginResponseDTO
    {
        $newToken = auth('api')->refresh();

        if (!$newToken) {
            throw new InvalidCredentialsException();
        }

        $user = auth('api')->setToken($newToken)->authenticate();

        if ($user->status_id !== Status::ACTIVE->value) {
            throw new UserInactiveException();
        }

        return LoginResponseDTO::fromToken($newToken, $user);
    }
}
