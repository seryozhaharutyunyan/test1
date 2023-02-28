<?php

namespace App\Controller;

use App\Model\User\User;
use App\Request\Auth\LoginRequest;
use App\Request\Auth\RegistrationRequest;
use App\Request\Auth\ResetRequest;
use App\Request\Auth\UpdateRequest;
use Engine\Controller;
use Engine\Core\Auth\Auth;
use Engine\Core\Auth\AuthInterface;

class AuthController extends Controller implements AuthInterface
{

    public function login(LoginRequest $request)
    {
        $data=$request->validate();
        $user=(new User())->attempt($data['email'], $data['password']);
        if(!empty($user)){
            $token=Auth::addToken(new User($user->id));
            $this->response->setHeader('Authorization', "Bearer $token");
            $this->response->setData([
                'token'=>$token,
                'user'=>$user->id
                ])->send(200, 'Authorization');
        }

        $this->response->send(415, 'Invalid authorization data');
    }

    public function logout()
    {
        $user=new User(Auth::getUser());
        Auth::deleteToken($user);
        $this->response->send(200, 'Logout');
    }

    public function registration(RegistrationRequest $request)
    {
        $data=$request->validate();

        $user=new User();
        $user->setEmail($data['email']);
        $user->setPassword(Auth::encryptPassword($data['password']));
        $user->save();
        $this->response->setData($data)->send();
    }

    public function resetPassword(ResetRequest $request)
    {
        // TODO: Implement resetPassword() method.
    }

    public function update(UpdateRequest $request)
    {
        // TODO: Implement update() method.
    }
}