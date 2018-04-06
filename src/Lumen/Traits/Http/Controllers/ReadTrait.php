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
            $this->mainService
                    ->findAll($this->translateFilters($request))
                    ->toArray($this->getToArray($request, $this->optionsToArrayIndex))
        );
    }

    public function count(Request $request)
    {
        $this->defaultFilters($request);

        return response()->json([
            'count' => (int) $this->mainService
                            ->findAll($this->translateFilters($request))
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
            $this->mainService
                    ->find($id)
                    ->toArray($this->getToArray($request, $this->optionsToArrayShow))
        );
    }
}
