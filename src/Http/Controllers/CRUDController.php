<?php

namespace Bludata\Http\Controllers;

use Illuminate\Http\Request;

abstract class CRUDController extends BaseController
{
    public function index(Request $request)
    {
        return response()->json($this->mainService
                                     ->findAll($this->translateFilters($request))
                                     ->toArray());
    }

    public function show($id)
    {
        return response()->json($this->mainService
                                     ->find($id)->toArray());
    }

    public function store(Request $request)
    {
        $entity = $this->mainService
                       ->store($this->filterRequest($request->json()
                                                            ->all(),
                                                    $this->mainService
                                                         ->getMainRepository()
                                                         ->createEntity()
                                                         ->getOnlyStore()))
                       ->flush();

        return response()->json($entity->toArray());
    }

    public function update(Request $request, $id)
    {
        $entity = $this->mainService
                       ->update($id, $this->filterRequest($request->json()
                                                                  ->all(),
                                                          $this->mainService
                                                               ->getMainRepository()
                                                               ->createEntity()
                                                               ->getOnlyUpdate()))
                       ->flush();

        return response()->json($entity->toArray());
    }

    public function destroy($id)
    {
        $entity = $this->mainService
                       ->remove($id)
                       ->flush();

        return response()->json($entity->toArray());
    }
}
