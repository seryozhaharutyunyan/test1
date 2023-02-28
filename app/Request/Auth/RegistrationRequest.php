<?php

namespace App\Request\Auth;

use Engine\Core\Request\Request;

class RegistrationRequest extends Request
{

    protected function validated(): array
    {
        return [
            'email'=>'required|string|unique:users',
            'password'=>'required|string|confirmation',
            'confirmation_password'=>'required|string',
        ];
    }
}