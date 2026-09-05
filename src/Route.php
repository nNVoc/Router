<?php

namespace nNVoc\Router;

class Route
{
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

    public function run(Request $request, array $params): Response
    {
        return ($this->handler)($request, $params);
    }
}