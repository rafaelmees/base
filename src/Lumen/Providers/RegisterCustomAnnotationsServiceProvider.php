<?php

namespace Bludata\Lumen\Providers;

use Illuminate\Support\ServiceProvider;

class RegisterCustomAnnotationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        register_annotation_dir(__DIR__.'/../../Doctrine/Common/Annotations');
        register_annotation_dir(base_path().'/vendor/symfony/validator/Constraints');
    }
}
