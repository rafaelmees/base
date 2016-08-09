<?php

namespace Bludata\Lumen\Providers;
<<<<<<< HEAD:src/Lumen/Providers/RegisterSymfonyConstraintsServiceProvider.php

use Illuminate\Support\ServiceProvider;
=======
>>>>>>> 23d05296dece732c2042b36ddf80c2de5961911d:src/Lumen/Providers/RegisterSymfonyConstraintsServiceProvider.php

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
