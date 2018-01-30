<?php

namespace Bludata\Lumen\Traits\Http\Controllers;

use Illuminate\Http\Request;

trait ReadTrait
{
    protected $optionsToArrayIndex;
    protected $optionsToArrayShow;

    public function index(Request $request)
    {
        return response()->json(
            $this->mainService
                    ->findAll($this->translateFilters($request))
                    ->toArray($this->getToArray($request, $this->optionsToArrayIndex))
        );
    }

    public function count(Request $request)
    {
        return response()->json([
            'count' => (int) $this->mainService
                            ->findAll($this->translateFilters($request))
                            ->getBuilder()
                            ->select('COUNT(t) AS c')
                            ->getQuery()
                            ->getOneOrNullResult()['c']
        ]);
    }

    public function show($id)
    {
        return response()->json(
            $this->mainService
                    ->find($id)
                    ->toArray($this->getToArray($request, $this->optionsToArrayShow))
        );
    }
}
