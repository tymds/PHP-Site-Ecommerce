<?php
declare(strict_types=1);

require_once __DIR__ . '/../private/bootstrap.php';

$scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$basePath = preg_replace('#/index\.php$#', '', $scriptName);

if ($basePath === null) {
    $basePath = '';
}

$basePath = preg_replace('#/public$#', '', $basePath) ?? $basePath;
$basePath = rtrim($basePath, '/');
$basePath = $basePath === '' ? '/' : $basePath;

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', $basePath);
}

$requestUri = (string)($_SERVER['REQUEST_URI'] ?? '/');
$requestPath = (string)(parse_url($requestUri, PHP_URL_PATH) ?: '/');
$requestPath = rawurldecode($requestPath);

if ($basePath !== '/' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath));
}

$requestPath = '/' . ltrim($requestPath, '/');
$route = rtrim($requestPath, '/');
$route = $route === '' ? '/' : $route;

if ($route === '/public') {
    $route = '/';
} elseif (str_starts_with($route, '/public/')) {
    $route = substr($route, strlen('/public'));
    $route = $route === '' ? '/' : $route;
}

if ($route === '/home.php') {
    $route = '/home';
} elseif ($route === '/sell.php') {
    $route = '/sell';
} elseif ($route === '/index.php') {
    $route = '/';
}

$routes = [
    '/' => __DIR__ . '/../app/controllers/home.php',
    '/home' => __DIR__ . '/../app/controllers/home.php',
    '/home.php' => __DIR__ . '/../app/controllers/home.php',
    '/account' => __DIR__ . '/../app/controllers/account.php',
    '/login' => __DIR__ . '/../app/controllers/login.php',
    '/register' => __DIR__ . '/../app/controllers/register.php',
    '/logout' => __DIR__ . '/../app/controllers/logout.php',
    '/index.php' => __DIR__ . '/../app/controllers/home.php',
    '/sell' => __DIR__ . '/../app/controllers/sell.php',
    '/sell.php' => __DIR__ . '/../app/controllers/sell.php',
    '/sell-action' => __DIR__ . '/../app/controllers/sell_action.php',
    '/sell_action.php' => __DIR__ . '/../app/controllers/sell_action.php',
];

if (isset($routes[$route])) {
    require $routes[$route];
    exit;
}

http_response_code(404);
require __DIR__ . '/../app/views/404.php';
