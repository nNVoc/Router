<?php

namespace nNVoc\Router;

class Request 
{
    public function method() : string 
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }
    
    public function path() : string 
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    public function query(string $key) : ?string 
    {
        return $_GET[$key] ?? null;
    }
}