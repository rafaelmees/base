<?php

namespace Bludata\Authentication\JWT\Middleware;

use Bludata\Authentication\JWT\Exceptions\RestrictAccessException;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

final class AuthMiddleware
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if ($this->auth->guard($guard)->guest()) {
                throw new RestrictAccessException();
            }

            return $next($request);
        } catch (RestrictAccessException $e) {
            abort(401, $e->getMessage());
        }
    }
}
