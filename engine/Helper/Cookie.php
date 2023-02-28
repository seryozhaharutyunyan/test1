<?php

namespace Engine\Helper;

class Cookie
{
    /**
     * @param        $key
     * @param        $value
     * @param   int  $time
     *
     * @return void
     */
    public static function set($key, $value, int $time = 31536000): void
    {
        \setcookie($key, $value, time() + $time, '/');
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public static function get($key): mixed
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }

        return false;
    }

    /**
     * @param $key
     *
     * @return void
     */
    public static function delete($key): void
    {
        if (isset($_COOKIE[$key])) {
            self::set($key, "", -3600);
            unset($_COOKIE[$key]);
        }
    }

}