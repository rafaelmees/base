<?php

/**
 * Get Enviorment variable.
 */
if (!function_exists('env')) {

    /**
     * @param string $key
     */
    function env($key, $defaultValue = '')
    {
        $env = getenv($key);
        if (!$env) {
            return $defaultValue;
        }

        return $env;
    }
}

/*
 * Register a entierly directory of annotations
 */
if (!function_exists('register_annotation_dir')) {

    /**
     * @param string $dir
     */
    function register_annotation_dir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $handle = opendir($dir);
        while ($path = readdir($handle)) {
            $toRegisterPath = implode(DIRECTORY_SEPARATOR, [$dir, $path]);
            register_annotation_file($toRegisterPath);
        }

        return true;
    }
}

/*
 * Register a single file annotation
 */
if (!function_exists('register_annotation_file')) {

    /**
     * @param string $file
     */
    function register_annotation_file($file)
    {
        if (!is_file($file)) {
            return false;
        }

        return \Doctrine\Common\Annotations\AnnotationRegistry::registerFile($file);
    }
}

/*
 * Default binding a repository interface to a concret class
 */
if (!function_exists('bind_repository_interface')) {
    function bind_repository_interface($repositoryInterface, $repository, $entity)
    {
        app()->bind($repositoryInterface, function($app) use ($repository, $entity) {
            return new $repository(
                $app['em'],
                $app['em']->getClassMetaData($entity)
            );
        });
    }
}
