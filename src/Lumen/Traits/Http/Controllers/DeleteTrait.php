<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait DeleteTrait
{
    protected $optionsToArrayDestroy;

    public function destroy(Request $request, $id)
    {
        $this->defaultFilters($request);

        $entity = $this->mainRepository
                       ->find($id)
                       ->remove();

        return response()->json($entity->toArray($this->optionsToArrayDestroy));
    }

    public function destroyed(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json(
            $this->mainRepository
                    ->findAllRemoved()
                    ->toArray()
        );
    }

    public function restoreDestroyed(Request $request, $id)
    {
        $this->defaultFilters($request);

        return response()->json(
            $this->mainRepository
                 ->findRemoved($id)
                 ->restoreRemoved()
                 ->flush()
                 ->toArray()
        );
    }
}
