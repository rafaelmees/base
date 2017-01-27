<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

trait DeleteTrait
{
    protected $optionsToArrayDestroy;

    public function destroy($id)
    {
        $entity = $this->mainService
                       ->remove($id)
                       ->flush();

        return response()->json($entity->toArray($this->optionsToArrayDestroy));
    }
}
