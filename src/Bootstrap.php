<?php
require_once(__DIR__ . '/../vendor/autoload.php');

define('WORKING_DIRECTORY', $_ENV['PWD']);
define('BASE_DIRECTORY', __DIR__);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', '\Famelo\Soup\Core\WebController:index');
    $r->addRoute('GET', '/recipe/{recipe}', '\Famelo\Soup\Core\WebController:recipe');
    $r->addRoute('POST', '/recipe/{recipe}', '\Famelo\Soup\Core\WebController:saveRecipe');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $arguments = $routeInfo[2];
        $parts = explode(':', $handler);
        $controller = new $parts[0]();
        $action = $parts[1];
        $controller->$action($arguments);
        break;
}