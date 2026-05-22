<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Credenciais inválidas.', 401);
    }
}
