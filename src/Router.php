<?php

namespace nNVoc\Router;

class Router 
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): Route
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): Route
    {
        return $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): Route
    {
        return $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): Route
    {
        return $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): Route
    {
        return $this->add('DELETE', $path, $handler);
    }

    public function head(string $path, callable|array $handler): Route
    {
        return $this->add('HEAD', $path, $handler);
    }

    public function options(string $path, callable|array $handler): Route
    {
        return $this->add('OPTIONS', $path, $handler);
    }

    private function add(
        string $method,
        string $path,
        callable|array $handler
    ): Route
    {
        $route = new Route($method, $path, $handler);
    
        $this->routes[] = $route;

        return $route;
    }

    public function dispatch(Request $request): Response
    {
        $methodNotAllowed = false;
        
        foreach ($this->routes as $route) {
            $result = $route->match(
                $request->method(),
                $request->path()
            );

            if ($result === null) {
                continue;
            }

            if ($result === false) {
                $methodNotAllowed = true;
                continue;
            }
                
            return $route->run($request, $result);
        }

        if ($methodNotAllowed) {
            return new Response('405 Method Not Allowed', 405);
        }

        return new Response('404 Not Found', 404);
    }
}