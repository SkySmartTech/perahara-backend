<?php

namespace App\Http;

use Fruitcake\Cors\HandleCors;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Global middleware
    protected $middleware = [
        // Keep only essential middleware for API
        
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    // Middleware groups
    protected $middlewareGroups = [
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // Route middleware
    protected $routeMiddleware = [
        'auth'  => Authenticate::class,
        'admin' => EnsureAdmin::class,
    ];
}
