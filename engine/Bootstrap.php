<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Function.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__.'../'));
$dotenv->load();

class_alias('Engine\Core\Config\Config', 'Config');
class_alias('Engine\Helper\Session', 'Session');

try {
    header("Access-Control-Allow-Headers: " . implode(', ', Config::item('headers', 'cors')));
    header("Access-Control-Allow-Origin: " . Config::item('host', 'cors'));
    header("Access-Control-Allow-Credentials: true");
    if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        header("Access-Control-Allow-Methods: PATCH, PUT, GET, POST, DELETE, OPTIONS");
        header("HTTP/1.1 200 OK");
        die();
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

Session::start();

use Engine\Cms;
use Engine\DI\DI;

try {
    $di = new DI();

    $services = require __DIR__ . "/Config/Service.php";

    foreach ($services as $service) {
        $provider = new $service($di);
        $provider->init();
    }

    $cms = new Cms($di);
    $cms->run();

} catch (\ErrorException $e) {
    echo $e->getMessage();
}