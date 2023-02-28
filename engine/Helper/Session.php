<?php

namespace Engine\Helper;

class Session
{

    /**
     * @return array
     */
    public static function getData(): array
    {
        return $_SESSION;
    }

    /**
     * @param array $data
     */
    public static function setData(array $data): void
    {
        $_SESSION = \array_merge($_SESSION, $data);
    }

    /**
     * @return void
     */
    public static function start(): void
    {
        \session_start();
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     */
    public static function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public static function get($key): mixed
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
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
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}