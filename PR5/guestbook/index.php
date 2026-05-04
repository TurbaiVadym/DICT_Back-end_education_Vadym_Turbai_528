<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions

// 1. namespace
namespace PR5\guestbook;

session_start();

// 2. use

// 3. require_once

require_once 'vendor/autoload.php';
require_once 'Controllers/HomeController.php';
require_once 'Controllers/GuestbookController.php';
require_once 'Controllers/RegisterController.php';
require_once 'Controllers/LoginController.php';
require_once 'Controllers/AdminController.php';
require_once 'Controllers/LogoutController.php';

// TODO 2: ROUTING
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/Back-end/PR5/guestbook';

// Отримуємо чистий роут (наприклад: /login або /guestbook)
$route = str_replace($basePath, '', $requestUri);
// Прибираємо index.php з рядка запиту
$route = str_replace('/index.php', '', $route);

$route = '/' . trim($route, '/'); // Робимо формат завжди "/щось"

if (empty($route) || $route === '/') {
    $route = '/';
}

switch ($route) {
    case '/guestbook':
        $controllerClassName = 'GuestbookController';
        break;
    case '/':
        $controllerClassName = 'HomeController';
        break;
    case '/register':
        $controllerClassName = 'RegisterController';
        break;
    case '/login':
        $controllerClassName = 'LoginController';
        break;
    case '/logout':
        $controllerClassName = 'LogoutController';
        break;
    case '/admin':
        $controllerClassName = 'AdminController';
        break;
    default:
        echo 'Path not found.';
        die;
}

$fullClassName = '\PR5\guestbook\Controllers\\'.$controllerClassName;

$controller = new $fullClassName();
$controller->execute();


