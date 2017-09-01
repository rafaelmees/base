<?php

/**
 * Checks the existence of the class in Core or Base.
 *
 * @return name of class
 */
if (!function_exists('bdClassExists')) {
    function bdClassExists($folder, $className, $module = null, $version = 'v1', $abort = true)
    {
        $entityClass = app()->getBaseNamespace().app()->getMainModule($module).'\\'.$version.'\\'.$folder.'\\'.$className;

        if (class_exists($entityClass)) {
            return $entityClass;
        }

        if ($abort) {
            abort(500, 'Undefined \''.$className.'\'');
        }

        return null;
    }
}

/*
 * Checks the existence of the class interface in Core or Base
 * @return name of class interface
 */
if (!function_exists('bdInterfaceExists')) {
    function bdInterfaceExists($folder, $className, $module = null, $version = 'v1', $abort = true)
    {
        $entityClass = app()->getBaseNamespace().app()->getMainModule($module).'\\'.$version.'\\'.$folder.'\\'.$className;

        if (interface_exists($entityClass)) {
            return $entityClass;
        }

        if ($abort) {
            abort(500, 'Undefined \''.$className.'\'');
        }

        return null;
    }
}

/*
 * @return the name of the repository interface
 */
if (!function_exists('bdRepositoryInterfaceClass')) {
    function bdRepositoryInterfaceClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdInterfaceClass($className.'Repository', $module, $version, $abort);
    }
}

/*
 * @return the name of the class
 */
if (!function_exists('bdRepositoryClass')) {
    function bdRepositoryClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdClassExists('Repositories', $className.'Repository', $module, $version, $abort);
    }
}

/*
 * @return an instance of the class repository
 */
if (!function_exists('bdRepository')) {
    function bdRepository($className, $module = null, $version = 'v1', $abort = true)
    {
        return app(bdRepositoryInterfaceClass($className, $module, $version, $abort));
    }
}

/*
 * @return the name of the service interface
 */
if (!function_exists('bdServiceInterfaceClass')) {
    function bdServiceInterfaceClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdInterfaceClass($className.'Service', $module, $version, $abort);
    }
}

/*
 * @return the name of the service
 */
if (!function_exists('bdServiceClass')) {
    function bdServiceClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdClassExists('Services', $className.'Service', $module, $version, $abort);
    }
}

/*
 * @return an instance of the service
 */
if (!function_exists('bdService')) {
    function bdService($className, $module = null, $version = 'v1', $abort = true)
    {
        return app(bdServiceInterfaceClass($className, $module, $version, $abort));
    }
}

/*
 * @return the name of the entity
 */
if (!function_exists('bdEntityClass')) {
    function bdEntityClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdClassExists('Entities', $className, $module, $version, $abort);
    }
}

/*
 * @return an instance of the entity
 */
if (!function_exists('bdEntity')) {
    function bdEntity($className, $module = null, $version = 'v1', $abort = true)
    {
        return app(bdEntityClass($className, $module, $version, $abort));
    }
}

/*
 * @return the name of the contract
 */
if (!function_exists('bdContractClass')) {
    function bdContractClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdInterfaceExists('Contracts', $className.'Interface', $module, $version, $abort);
    }
}

/*
 * @return an instance of the contract
 */
if (!function_exists('bdContract')) {
    function bdContract($className, $module = null, $version = 'v1', $abort = true)
    {
        return app(bdContractClass($className, $module, $version, $abort));
    }
}
/*
 * Verifica a existencia da interface
 */
if (!function_exists('bdInterfaceClass')) {
    function bdInterfaceClass($className, $module = null, $version = 'v1', $abort = true)
    {
        return bdInterfaceExists('Interfaces', $className.'Interface', $module, $version, $abort);
    }
}

/*
 * @return an instance of the EntityManager
 */
if (!function_exists('em')) {
    function em()
    {
        return app('Doctrine\ORM\EntityManagerInterface');
    }
}

/*
 * Dump and die.
 */
if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            var_dump($arg);
        }
        exit();
    }
}

/*
 * Print and die
 */
if (!function_exists('pd')) {
    function pd()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            print_r($arg);
        }
        exit();
    }
}

/*
 * Dump and return
 */
if (!function_exists('dr')) {
    function dr()
    {
        $args = func_get_args();
        $result = '';
        foreach ($args as $arg) {
            $result = '';
            $type = gettype($arg);
            if ($type == 'object') {
                $type = get_class($arg);
            }
            if ($type == 'boolean') {
                $result = $arg ? 'true' : 'false';
            } else {
                $result = print_r($arg, true);
            }
            $result = sprintf('(%s) %s', $type, $result);
        }

        return $result;
    }
}

/*
 * Retrieve all annotations of a giving object
 */
if (!function_exists('get_class_annotations')) {
    function get_class_annotations($element, $annotation = null)
    {
        $class = $element;
        if (is_object($element)) {
            $class = get_class($element);
        }

        $reflectClass = new \ReflectionClass($class);
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();

        return $reader->getClassAnnotations($reflectClass, $annotation);
    }
}

/*
 * Retrieve annotations of a especific property of a giving object
 */
if (!function_exists('get_property_annotations')) {
    function get_property_annotations($element, $property = null, $annotation = null)
    {
        $class = $element;
        if (is_object($element) && !($element instanceof \ReflectionClass)) {
            $class = get_class($element);
        }

        if (is_string($property)) {
            $property = new \ReflectionProperty($class, $property);
        }

        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        if ($property instanceof \ReflectionProperty) {
            return $reader->getPropertyAnnotations($property, $annotation);
        }

        $reflectClass = new \ReflectionClass($class);
        $reflectProperties = $reflectClass->getProperties();
        $annotations = [];
        foreach ($reflectProperties as $property) {
            $annotations[$property->getName()] = get_property_annotations($reflectClass, $property, $annotation);
        }

        return $annotations;
    }
}
