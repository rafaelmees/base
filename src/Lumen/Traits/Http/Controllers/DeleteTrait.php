<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait DeleteTrait
{
    protected $optionsToArrayDestroy;

    public function destroy(Request $request, $id)
    {
        $this->defaultFilters($request);

        $entity = $this->mainService
                        ->remove($id)
                        ->flush();

        return response()->json($entity->toArray($this->optionsToArrayDestroy));
    }

    public function destroyed(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json(
            $this->mainService
                    ->findAllDestroyed()
                    ->toArray()
        );
    }

    public function destroyedCount(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json([
            'count' => (int) $this->mainService
                            ->findAllDestroyed($this->translateFilters($request))
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
            $this->mainService
                    ->restoreDestroyed($id)
                    ->flush()
                    ->toArray()
        );
    }
}
