<?php

namespace Bludata\Lumen\Application;

use Closure;
use Laravel\Lumen\Application;

abstract class BaseApplication extends Application
{
    protected $currentUser;

    protected $regexIdParamRoutes = ':[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    abstract public function getRepositoryInterface($entity);

    abstract public function getBaseNamespace();

    abstract public function getMainModule($module = null);

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
        $asPrefix = str_replace('/', '.', $prefix);
        $exceptAll = false;

        if (isset($except[0])) {
            $exceptAll = $except[0] == '*';
        }

        if (!$exceptAll) {
            if (!in_array('index', $except)) {
                $this->get($prefix, [
                    'as'               => $asPrefix.'.index',
                    'uses'             => $controller.'@index',
                    'description'      => 'Buscar todos',
                    'descriptionGroup' => $descriptionGroup,
                ]);
                $this->get($prefix.'/count', [
                    'as'               => $asPrefix.'.count',
                    'uses'             => $controller.'@count',
                    'description'      => 'Retorna a quantidade total de registros',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('show', $except)) {
                $this->get($prefix.'/{id'.$this->regexIdParamRoutes.'}', [
                    'as'               => $asPrefix.'.show',
                    'uses'             => $controller.'@show',
                    'description'      => 'Buscar um',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('store', $except)) {
                $this->post($prefix, [
                    'as'               => $asPrefix.'.store',
                    'uses'             => $controller.'@store',
                    'description'      => 'Cadastrar',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('update', $except)) {
                $this->put($prefix.'/{id'.$this->regexIdParamRoutes.'}', [
                    'as'               => $asPrefix.'.update',
                    'uses'             => $controller.'@update',
                    'description'      => 'Editar',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('destroy', $except)) {
                $this->delete($prefix.'/{id'.$this->regexIdParamRoutes.'}', [
                    'as'               => $asPrefix.'.destroy',
                    'uses'             => $controller.'@destroy',
                    'description'      => 'Excluir',
                    'descriptionGroup' => $descriptionGroup,
                ]);
            }

            if (!in_array('destroyed', $except)) {
                $this->get($prefix.'/destroyed', [
                    'as'               => $asPrefix.'.destroyed',
                    'uses'             => $controller.'@destroyed',
                    'description'      => 'Buscar excluídos',
                    'descriptionGroup' => $descriptionGroup,
                ]);
                $this->get($prefix.'/count', [
                    'as'               => $asPrefix.'.count',
                    'uses'             => $controller.'@destroyedCount',
                    'description'      => 'Retorna a quantidade total de registros removidos',
                    'descriptionGroup' => $descriptionGroup,
                ]);

            }

            if (!in_array('restoreDestroyed', $except)) {
                $this->post($prefix.'/destroyed/{id'.$this->regexIdParamRoutes.'}', [
                    'as'               => $asPrefix.'.restoreDestroyed',
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
