<?php

namespace App\Contracts\Services\Auth;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\LoginResponseDTO;
use App\DTOs\AUth\RegisterDTO;


interface AuthServiceInterface
{
    public function register(RegisterDTO $registerDTO): LoginResponseDTO;
    public function login(LoginDTO $loginDTO): LoginResponseDTO;

    public function logout(): void;

    public function refreshToken(): LoginResponseDTO;

}
