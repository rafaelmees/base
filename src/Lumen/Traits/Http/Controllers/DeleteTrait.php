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
                       ->remove()
                       ->flush();

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

    public function destroyedCount(Request $request)
    {
        $this->defaultFilters($request);
        
        return response()->json([
            'count' => (int) $this->mainRepository
                            ->findAllRemoved($this->translateFilters($request))
                            ->getBuilder()
                            ->select('COUNT(t) AS c')
                            ->getQuery()
                            ->getOneOrNullResult()['c'],
        ]);
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
