<?php
namespace App\Core;

use Exception;

class Router {
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $uri, $callback, $middleware = null) { 
        $this->routes['GET'][$uri] = $callback;
        if($middleware) $this->middlewares['GET'][$uri] = $middleware;
    }

    public function post(string $uri, $callback, $middleware = null) { 
        $this->routes['POST'][$uri] = $callback;
        if($middleware) $this->middlewares['POST'][$uri] = $middleware;
    }

    public function put(string $uri, $callback, $middleware = null) { 
        $this->routes['PUT'][$uri] = $callback;
        if($middleware) $this->middlewares['PUT'][$uri] = $middleware;
    }

    public function delete(string $uri, $callback, $middleware = null) { 
        $this->routes['DELETE'][$uri] = $callback;
        if($middleware) $this->middlewares['DELETE'][$uri] = $middleware;
    }

    public function dispatch(string $method, string $uri) {
        header('Content-Type: application/json');
        $found = false;

        foreach($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = "@^" . preg_replace('/{[\w]+}/', '([\w-]+)', $route) . "$@";
            if(preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $found = true;

                try {
                    // تنفيذ Middleware إذا موجود
                    if(isset($this->middlewares[$method][$route])) {
                        $middleware = $this->middlewares[$method][$route];
                        if(is_callable($middleware)) $middleware(); 
                    }

                    // تنفيذ Callback
                    if(is_array($callback)) {
                        [$class, $methodName] = $callback;

                        if(!class_exists($class)) {
                            throw new Exception("Controller $class not found");
                        }

                        $controller = new $class();
                        if(!method_exists($controller, $methodName)) {
                            throw new Exception("Method $methodName not found in $class");
                        }

                        call_user_func_array([$controller, $methodName], $matches);

                    } elseif(is_callable($callback)) {
                        call_user_func_array($callback, $matches);
                    } else {
                        throw new Exception("Invalid callback for route $route");
                    }

                } catch(Exception $e){
                    http_response_code(500);
                    echo json_encode([
                        'success' => false,
                        'message' => 'خطأ داخلي (Debug)',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'method' => $method,
                        'uri' => $uri
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }

                break;
            }
        }

        if(!$found){
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'الصفحة غير موجودة',
                'method' => $method,
                'uri' => $uri
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
    }
}
