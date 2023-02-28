<?php

namespace Engine\Helper;

use JetBrains\PhpStorm\NoReturn;

class Redirect
{
    public static function go(string $url, bool $permanent = false)
    {
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Location: ' . $url);
        exit();
    }
}