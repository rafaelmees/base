<?php

namespace Bludata\Lumen\Http\Controllers;

use EntityManager;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

abstract class BaseController extends Controller
{
    /**
     * @var Bludata\Lumen\Services\BaseService
     */
    protected $mainService;

    public function getMainService()
    {
        return $this->mainService;
    }

    protected function translateFilters($filters)
    {
        if ($filters instanceof Request) {
            if ($filters->has('filters')) {
                $filters = json_decode(base64_decode($filters->input('filters')), true);
            } else {
                $filters = [];
            }
        }

        return $filters;
    }

    public function filterRequest($allInputs, $only)
    {
        return array_intersect_key($allInputs, array_flip($only));
    }

    public function getToArray(Request $request, array $default = [])
    {
        if ($request->has('toArray')) {
            if ($toArray = json_decode(base64_decode($request->get('toArray')), true)) {
                $default = $toArray;
            }
        }

        return $default;
    }

    public function defaultFilters(Request $request)
    {
        if ($request->has('defaultFilters') && $filters = json_decode(base64_decode($request->get('defaultFilters')), true)) {
            foreach ($filters as $filter => $enable) {
                if ($enable) {
                    if (!EntityManager::getFilters()->isEnabled($filter)) {
                        EntityManager::getFilters()->enable($filter);
                    }
                } else {
                    if (EntityManager::getFilters()->isEnabled($filter)) {
                        EntityManager::getFilters()->disable($filter);
                    }
                }
            }
        }
    }
}
