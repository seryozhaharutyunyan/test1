<?php

namespace Engine\Core\Auth;

use App\Model\User\User;
use App\Request\Auth\LoginRequest;
use App\Request\Auth\RegistrationRequest;
use App\Request\Auth\ResetRequest;
use App\Request\Auth\UpdateRequest;

interface AuthInterface
{
    public function login(LoginRequest $request);

    public function logout();

    public function registration(RegistrationRequest $request);

    public function resetPassword(ResetRequest $request);

    public function update(UpdateRequest $request);
}