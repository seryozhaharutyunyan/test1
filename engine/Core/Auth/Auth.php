<?php

namespace Engine\Core\Auth;

use App\Model\User\User;
use Engine\Helper\Cookie;
use Engine\Helper\Session;

class Auth
{
    protected static ?int $id = null;

    /**
     * @return bool
     */
    public static function authorized(): bool
    {
        if (Session::get('auth_authorized')) {
            return Session::get('auth_authorized');
        } elseif (Cookie::get('auth_authorized')) {
            return Cookie::get('auth_authorized');
        }
        return false;
    }

    /**
     * @return string|null
     */
    public static function getUser(): string|null
    {
        if (Session::get('auth_user')) {
            return Session::get('auth_user');
        }
        return Cookie::get('auth_user');
    }

    /**
     * @return string|null
     */
    public static function getToken(): string|null
    {
        if (Session::get('auth_token')) {
            return Session::get('auth_token');
        }
        return Cookie::get('auth_token');
    }


    /**
     * @param int $id
     * @param string $method
     * @return void
     */
    public static function authorize(int $id, string $token, string $method = 'cookie'): void
    {
        if ($method === 'session') {
            Session::set('auth_authorized', true);
            Session::set('auth_user', $id);
            Session::set('auth_token', $token);
        } else {
            Cookie::set('auth_authorized', true);
            Cookie::set('auth_user', $id);
            Cookie::set('auth_token', $token);
        }
    }

    /**
     * @return void
     */
    public static function unAuthorize(string $method = 'cookie'): void
    {
        if ($method === 'session') {
            Session::set('auth_authorized', false);
            Session::delete('auth_user');
            Session::delete('auth_token');
        } else {
            Cookie::set('auth_authorized', false);
            Cookie::delete('auth_user');
            Cookie::delete('auth_token');
        }
    }

    /**
     * @return string
     */
    public static function salt(): string
    {
        return (string)rand(1000000000, 9999999999);
    }

    /**
     * @param string $password
     * @param string $salt
     *
     * @return bool|string
     */
    public static function encryptPassword(string $password, string $salt = ""): bool|string
    {
        return hash('sha256', $password . $salt);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function createToken(): string
    {
        return bin2hex(random_bytes(128));
    }

    /**
     * @param User $user
     * @return string
     * @throws \Exception
     */
    public static function addToken(User $user): string
    {
        $token = Auth::createToken();
        if ($token) {
            $user->setToken($token);
            $user->save();
            Auth::authorize($user->getId(), $token, \Config::item('saveMethod'));
        }

        return $token;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public static function deleteToken(User $user): void
    {
        $user->setToken(null);
        $user->save();
        Auth::unAuthorize(\Config::item('saveMethod'));
    }

    /**
     * @return bool|int
     * @throws \Exception
     */
    public static function authProtection(): bool|int
    {
        if (Auth::authorized()) {
            if (isset($_SERVER['HTTP_AUTHORIZATION']) && !preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                return 409;
            }
            $token = $matches[1] ?? false;
            $user = new User(Auth::getUser());
            if ($user->getToken() !== $token) {
                Auth::deleteToken($user);
                return false;
            } else {
                Auth::addToken($user);
                return true;
            }
        }
        return false;
    }
}