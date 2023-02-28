<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define("ROOT_DIR", substr(__DIR__, 0, strrpos(__DIR__, '\\',)));

const DS = DIRECTORY_SEPARATOR;

require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/engine/Function.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__ . '../'));
$dotenv->load();



