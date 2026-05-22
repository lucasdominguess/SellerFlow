<?php

namespace App\Http\Controllers\Auth;

use App\Classes\ApiResponse;
use App\Contracts\Services\Auth\AuthServiceInterface;
use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthServiceInterface $authService)
    {
    }

    public function login(LoginRequest $request)
    {
        $loginDTO = LoginDTO::fromRequest($request->validated());
        $loginResponseDTO = $this->authService->login($loginDTO);

        $cookie = cookie(
            name: 'token',
            value: $loginResponseDTO->token,
            minutes: 60,
            httpOnly: true,
            secure: true,
            sameSite: 'lax'
        );
        return ApiResponse::success($loginResponseDTO->toArray(), 'Login realizado com sucesso')
            ->header('Authorization', 'Bearer ' . $loginResponseDTO->token)
            ->header('Access-Control-Expose-Headers', 'Authorization')
            ->cookie($cookie);
        ;
    }
    public function register(Request $request)
    {
        // Lógica de registro aqui
    }
    public function logout()
    {
        $this->authService->logout();

        $cookie = cookie(
            name: 'token',
            value: null,
            minutes: -1,
            httpOnly: true,
            secure: true,
            sameSite: 'lax'
        );

        return ApiResponse::success(
            message: 'Logout realizado com sucesso'
        )->cookie($cookie);
    }
    public function refreshToken()
    {
        $newToken = $this->authService->refreshToken();

        $cookie = cookie(
            name: 'token',
            value: $newToken,
            minutes: 60,
            httpOnly: true,
            secure: true,
            sameSite: 'lax'
        );

        return ApiResponse::success(
            data: ['token' => $newToken],
            message: 'Token atualizado com sucesso'
        )
            ->header('Authorization', 'Bearer ' . $newToken)
            ->header('Access-Control-Expose-Headers', 'Authorization')
            ->cookie($cookie);
    }
}
