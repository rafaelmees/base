<?php

namespace Bludata\Lumen\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_ENV') !== 'testing') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, token, _');
            header('Content-Type: application/json; charset=utf-8');
        }

        if ($request->getRealMethod() == 'OPTIONS') {
            return new \Illuminate\Http\Response('OK', 200);
        }

        return $next($request);
    }
}
