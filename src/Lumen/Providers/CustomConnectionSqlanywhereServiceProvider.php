<?php

namespace Bludata\Lumen\Providers;

use Illuminate\Support\ServiceProvider;

class CustomConnectionSqlanywhereServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        app('LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager')->extend('sqlanywhere', function() {
            return config('database.connections.sqlanywhere');
        });
    }
}
