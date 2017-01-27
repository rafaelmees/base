<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait UpdateTrait
{
    protected $optionsToArrayUpdate;

    public function update(Request $request, $id)
    {
        $entity = $this->mainService
                       ->update(
                            $id,
                            $this->filterRequest(
                                $request->json()->all(),
                                $this->mainService
                                     ->getMainRepository()
                                     ->createEntity()
                                     ->getOnlyUpdate()
                            )
                       )
                       ->flush();

        return response()->json($entity->toArray($this->optionsToArrayUpdate));
    }
}
