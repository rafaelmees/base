<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait CreateTrait
{
    protected $optionsToArrayStore;

    public function store(Request $request)
    {
        $this->defaultFilters($request);

        $entity = $this->mainRepository->createEntity();

        $entity->setPropertiesEntity(
            $this->filterRequest(
                $request->json()->all(),
                $this->mainRepository
                     ->createEntity()
                     ->getOnlyStore()
            )
        );

        $this->mainRepository
             ->preSave($entity)
             ->save($entity)
             ->flush();

        return response()->json($entity->toArray($this->optionsToArrayStore));
    }
}
