<?php

namespace Bludata\Authentication\JWT\Providers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Bludata\Authentication\JWT\Interfaces\JWTInterface', 'Bludata\Authentication\JWT\Libs\JWT');

        $this->app->bind('Bludata\Authentication\JWT\Interfaces\AuthRepositoryInterface', 'Bludata\Authentication\JWT\Repositories\AuthRepository');

        $this->app['auth']->viaRequest(
            'api', function ($request) {

                if ($token = $request->header('authorization')) {
                    $auth = app('Bludata\Authentication\JWT\Interfaces\AuthRepositoryInterface');

                    try {
                        $user = $auth->getUserLoggedByToken($token);
                    } catch (Exception $e) {
                        abort(401, $e->getMessage());
                    }

                    return $user;
                }
            }
        );
    }
}
