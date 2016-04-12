<?php

namespace Bludata\Http\Controllers;

use Illuminate\Http\Request;

abstract class CRUDController extends BaseController
{
	/**
     * @return '*' | array
     */
	protected function getOnlyStore()
	{
		return '*';
	}

	/**
     * @return '*' | array
     */
	protected function getOnlyUpdate()
	{
		return '*';
	}

	public function index(Request $request)
	{
		return response()->json($this->mainService->findAll($this->translateFilters($request))->toArray());
	}

	public function store(Request $request)
	{
		$input = $request->all();
		if (is_array($this->getOnlyStore()))
		{
			$input = array_intersect_key($request->all(), array_flip($this->getOnlyUpdate()));
		}

		$entity = $this->mainService->store($input);

		$this->mainService->getMainRepository()->flush();

		return response()->json($entity->toArray());
	}

	public function update(Request $request, $id)
	{
		$input = $request->all();
		if (is_array($this->getOnlyUpdate()))
		{
			$input = array_intersect_key($request->all(), array_flip($this->getOnlyUpdate()));
		}

		$entity = $this->mainService->update($id, $input);

		$this->mainService->getMainRepository()->flush();
		
		return response()->json($entity->toArray());
	}

	public function destroy($id)
	{
		$entity = $this->mainService->remove($id);

		$this->mainService->getMainRepository()->flush();

		return response()->json($entity->toArray());
	}
}
