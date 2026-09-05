<?php

namespace nNVoc\Router;

interface Middleware
{
    public function handle(Request $request, callable $next): Response;
}