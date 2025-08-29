<?php

namespace App;
include_once __DIR__ . "/../../controllers/not-found/Controller.php";

use App\Controllers\NotFoundController;

class Router
{
    protected $routes = [];

    private function addRoute($route, $controller, $action, $method): void
    {

        $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
    }

    public function get($route, $controller, $action): void
    {
        $this->addRoute($route, $controller, $action, "GET");
    }

    public function post($route, $controller, $action): void
    {
        $this->addRoute($route, $controller, $action, "POST");
    }

    public function delete($route, $controller, $action): void
    {
        $this->addRoute($route, $controller, $action, "DELETE");
    }

    public function put($route, $controller, $action): void
    {
        $this->addRoute($route, $controller, $action, "PUT");
    }

    public function dispatch(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method =  $_SERVER['REQUEST_METHOD'];

        if (array_key_exists($uri, $this->routes[$method])) {
            $controller = $this->routes[$method][$uri]['controller'];
            $action = $this->routes[$method][$uri]['action'];

            $controller = new $controller();
            $controller->$action();
        } else {
            $notFound = new NotFoundController();
            $notFound->index();        
        }
    }
}