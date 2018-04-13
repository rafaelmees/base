<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait UpdateTrait
{
    protected $optionsToArrayUpdate;

    public function update(Request $request, $id)
    {
        $this->defaultFilters($request);

        $entity = $this->mainRepository->find($id);

        $entity->setPropertiesEntity(
            $this->filterRequest(
                $request->json()->all(),
                $this->mainRepository
                     ->createEntity()
                     ->getOnlyUpdate()
            )
        );
        
        $this->mainRepository
             ->preSave($entity)
             ->save($entity)
             ->flush();

        return response()->json($entity->toArray($this->optionsToArrayUpdate));
    }
}
