<?php
require(__DIR__ . '/../vendor/autoload.php');
session_start();

use Symfony\Component\HttpFoundation\Request;

// Constants
define('APP_PATH', dirname(__DIR__) . '/app/');
define('SERVER_URL', 'http://pbn.test/');
define('DEBUG', true); // true en desarollo y false en producción
$time = time();
if (!isset($_SESSION['_token']) || $_SESSION['_tokenTime'] > $time + (30 * 60)) {
    $_SESSION['_tokenTime'] = $time + (30 * 60);
    $_SESSION['_token'] = bin2hex(\random_bytes(64));
}

$request = Request::createFromGlobals();

$response = require_once APP_PATH . 'bootstrap.php'; //cargamos el bootstrap

$response->send(); //enviamos la respuesta de vuelta

