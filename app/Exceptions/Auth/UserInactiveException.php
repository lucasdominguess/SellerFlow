<?php

namespace App\Exceptions\Auth;

use Exception;

class UserInactiveException extends Exception
{
    public function __construct()
    {
        parent::__construct('Usuário inativo. Entre em contato com o suporte.', 403);
    }
}
