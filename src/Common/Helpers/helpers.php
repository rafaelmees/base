<?php

/**
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

/**
 * Retrieve all annotations of a giving object
 */
if (!function_exists('get_class_annotations')) {
    function get_class_annotations($element, $annotation=null)
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

/**
 * Retrieve annotations of a especific property of a giving object
 */
if (!function_exists('get_property_annotations')) {
    function get_property_annotations($element, $property=null, $annotation=null)
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
        foreach($reflectProperties as $property) {
            $annotations[$property->getName()] = get_property_annotations($reflectClass, $property, $annotation);
        }

        return $annotations;
    }
}
