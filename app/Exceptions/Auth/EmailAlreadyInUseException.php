<?php

namespace App\Exceptions\Auth;

use Exception;

class EmailAlreadyInUseException extends Exception
{
    public function __construct()
    {
        parent::__construct('Este e-mail já está em uso.', 422);
    }
}
