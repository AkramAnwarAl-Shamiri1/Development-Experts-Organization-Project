<?php
namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $path = $this->normalize($path);
        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$class, $action] = $handler;
        $controller = new $class();
        $controller->$action();
    }

    private function normalize(string $path): string
    {
        if ($path === '') return '/';
        $path = '/' . ltrim($path, '/');
        return rtrim($path, '/') ?: '/';
    }
}
