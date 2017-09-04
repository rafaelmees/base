<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait CreateTrait
{
    protected $optionsToArrayStore;

    public function store(Request $request)
    {
        $entity = $this->mainService
                        ->store(
                                $this->filterRequest(
                                    $request->json()->all(),
                                $this->mainService
                                        ->getMainRepository()
                                        ->createEntity()
                                        ->getOnlyStore()
                            )
                        )
                        ->flush();

        return response()->json($entity->toArray($this->optionsToArrayStore));
    }
}
