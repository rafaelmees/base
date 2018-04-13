<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait ReadTrait
{
    protected $optionsToArrayIndex = [];
    protected $optionsToArrayShow = [];

    public function index(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json(
            $this->mainRepository
                 ->findAll()
                 ->withFilters($this->translateFilters($request))
                 ->toArray($this->getToArray($request, $this->optionsToArrayIndex))
        );
    }

    public function count(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json([
            'count' => (int) $this->mainRepository
                            ->findAll()
                            ->withFilters($this->translateFilters($request))
                            ->getBuilder()
                            ->select('COUNT(t) AS c')
                            ->getQuery()
                            ->getOneOrNullResult()['c'],
        ]);
    }

    public function show(Request $request, $id)
    {
        $this->defaultFilters($request);

        return response()->json(
            $this->mainRepository
                    ->find($id)
                    ->toArray($this->getToArray($request, $this->optionsToArrayShow))
        );
    }
}
