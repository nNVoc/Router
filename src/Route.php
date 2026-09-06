<?php

namespace nNVoc\Router;

class Route
{
    private array $middlewares = [];

    public function __construct(
        private string $method,
        private string $path,
        private mixed $handler
    ) {}

    public function match(string $method, string $path): array|false|null
    {
        if ($this->method !== $method) {
            return false;
        }
        
        $pattern = preg_replace(
            '#\{([^}]+)\}#',
            '([^/]+)',
            $this->path
        );

        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $path, $matches)) {
            return null;
        }

        preg_match_all('#\{([^}]+)\}#', $this->path, $names);

        $params = [];

        foreach ($names[1] as $index => $name) {
            $params[$name] = $matches[$index + 1];
        }

        return $params;
    }

    public function middleware(Middleware $middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function run(Request $request, array $params): Response
    {
        $handler = function (Request $request) use ($params): Response {
            return ($this->handler)($request, $params);
        };

        foreach (array_reverse($this->middlewares) as $middleware) {
            $next = $handler;

            $handler = function (Request $request) use ($middleware, $next): Response {
                return $middleware->handle($request, $next);
            };
        }

        return $handler($request);
    }
}