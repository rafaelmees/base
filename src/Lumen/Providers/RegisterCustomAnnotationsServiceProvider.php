<?php

namespace Bludata\Lumen\Providers;

use Illuminate\Support\ServiceProvider;

class RegisterCustomAnnotationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * Caso haja outras annotations que necessitem ser registradas, basta adicionar o path das mesmas no array $paths
         */
        $paths = [
            __DIR__.'/../../Doctrine/Common/Annotations', //referente a Bludata\Doctrine\Common\Annotations
            base_path().'/vendor/symfony/validator/Constraints', //referente a Symfony\Component\Validator\Constraints
        ];

        foreach ($paths as $path)
        {
            if ($handle = opendir($path))
            {
                while (false !== ($file = readdir($handle)))
                {
                    $pathFile = $path.'/'.$file;

                    if (pathinfo($pathFile)['extension'] == 'php')
                    {
                        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile($pathFile);
                    }
                }

                closedir($handle);
            }
        }
    }
}
