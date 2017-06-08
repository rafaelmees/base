<?php

namespace Bludata\Lumen\Application;

use Closure;
use Laravel\Lumen\Application;

abstract class BaseApplication extends Application
{
    protected $currentUser;

    abstract public function getRepositoryInterface($entity);

    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    public function registerPublicRoutes($descriptionGroup, $prefix, $controller, array $except = [], Closure $routes = null)
    {
        $this->resource($descriptionGroup, $prefix, $controller, array_merge($except, ['store', 'update', 'destroy', 'restoreDestroyed']), $routes);

        return $this;
    }

    public function registerPrivateRoutes($descriptionGroup, $prefix, $controller, array $except = [], Closure $routes = null)
    {
        $this->resource($descriptionGroup, $prefix, $controller, array_merge($except, ['index', 'show', 'destroyed']), $routes);

        return $this;
    }

    /**
     * @return Bludata\Lumen\Application\BaseApplication
     */
    public function resource($descriptionGroup, $prefix, $controller, array $except = [], Closure $routes = null)
    {
        $exceptAll = false;

        if (isset($except[0])) {
            $exceptAll = $except[0] == '*';
        }

        if (!$exceptAll) {
            if (!in_array('index', $except)) {
                $this->get($prefix, [
                    'as'               => $prefix.'.index',
                    'uses'             => $controller.'@index',
                    'description'      => 'Buscar todos',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('show', $except)) {
                $this->get($prefix.'/{id:[0-9]+}', [
                    'as'               => $prefix.'.show',
                    'uses'             => $controller.'@show',
                    'description'      => 'Buscar um',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('store', $except)) {
                $this->post($prefix, [
                    'as'               => $prefix.'.store',
                    'uses'             => $controller.'@store',
                    'description'      => 'Cadastrar',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('update', $except)) {
                $this->put($prefix.'/{id:[0-9]+}', [
                    'as'               => $prefix.'.update',
                    'uses'             => $controller.'@update',
                    'description'      => 'Editar',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('destroy', $except)) {
                $this->delete($prefix.'/{id:[0-9]+}', [
                    'as'               => $prefix.'.destroy',
                    'uses'             => $controller.'@destroy',
                    'description'      => 'Excluir',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('destroyed', $except)) {
                $this->get($prefix.'/destroyed', [
                    'as'               => $prefix.'.destroyed',
                    'uses'             => $controller.'@destroyed',
                    'description'      => 'Buscar excluídos',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('restoreDestroyed', $except)) {
                $this->post($prefix.'/destroyed/{id:[0-9]+}', [
                    'as'               => $prefix.'.restoreDestroyed',
                    'uses'             => $controller.'@restoreDestroyed',
                    'description'      => 'Restaurar excluído',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }
        }

        if ($routes instanceof Closure) {
            $routes($descriptionGroup, $prefix, $controller);
        }

        return $this;
    }
}
