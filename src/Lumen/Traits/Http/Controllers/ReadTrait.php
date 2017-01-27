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
                 ->toArray($this->optionsToArrayIndex)
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->mainService
                 ->find($id)
                 ->toArray($this->optionsToArrayShow)
        );
    }
}
