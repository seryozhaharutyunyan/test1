<?php

namespace Engine\Helper;

class Common
{
    /**
     * @return bool
     */
    public static function isGet(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isPost(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getMethod(): string
    {
        return (string)$_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public static function getPathUrl(): string
    {
        $pathUrl = $_SERVER['REQUEST_URI'];

        if ($position = \strpos($pathUrl, '?')) {
            $pathUrl = \substr($pathUrl, 0, $position);
        }

        return $pathUrl;
    }
}