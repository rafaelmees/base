<?php

namespace Bludata\Lumen\Providers;

use Illuminate\Support\ServiceProvider;

class BludataServiceProvider extends ServiceProvider
{
    public function register()
    {
    	$this->app->register(\LaravelDoctrine\Migrations\MigrationsServiceProvider::class);
		$this->app->register(\LaravelDoctrine\ORM\DoctrineServiceProvider::class);
		$this->app->register(\LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider::class);

    	$this->app->register(\Bludata\Lumen\Authentication\JWT\Providers\JWTServiceProvider::class);
		$this->app->register(CustomConnectionSqlanywhereServiceProvider::class);
		$this->app->register(RegisterCustomAnnotationsServiceProvider::class);

		/*
		|--------------------------------------------------------------------------
		| Aliases
		|--------------------------------------------------------------------------
		|
		| Here we will register all of the application's aliases.
		|
		*/
		class_alias(\LaravelDoctrine\ORM\Facades\EntityManager::class, 'EntityManager');
		class_alias(\LaravelDoctrine\ORM\Facades\Registry::class, 'Registry');
		class_alias(\LaravelDoctrine\ORM\Facades\Doctrine::class, 'Doctrine');
    }
}
