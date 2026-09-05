<?php

namespace nNVoc\Router;

class Router 
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function head(string $path, callable|array $handler): void
    {
        $this->add('HEAD', $path, $handler);
    }

    public function options(string $path, callable|array $handler): void
    {
        $this->add('OPTIONS', $path, $handler);
    }

    private function add(
        string $method,
        string $path,
        callable|array $handler
    ): void
    {
        $this->routes[] = new Route($method, $path, $handler);
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