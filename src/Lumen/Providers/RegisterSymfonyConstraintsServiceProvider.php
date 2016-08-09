<?php

namespace Bludata\LumenProviders;

use Illuminate\Support\Service\Provider;

class RegisterSymfonyConstraintsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $path = base_path().'/vendor/symfony/validator/Constraints';

        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $pathFile = $path.'/'.$file;

                if (pathinfo($pathFile)['extension'] == 'php') {
                    \Doctrine\Common\Annotations\AnnotationRegistry::registerFile($pathFile);
                }
            }

            closedir($handle);
        }
    }
}
