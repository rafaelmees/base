<?php

namespace Bludata\Providers;

use Illuminate\Support\ServiceProvider;

class CustomConnectionSqlanywhereServiceProvider extends ServiceProvider
{
    public function register()
    {
        app('LaravelDoctrine\ORM\Configuration\Connections\ConnectionManager')->extend('sqlanywhere', function () {
            return config('database.connections.sqlanywhere');
        });
    }
}
