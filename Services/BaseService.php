<?php

namespace Bludata\Services;

abstract class BaseService
{
	/**
     * @var Bludata\Repositories\BaseRepository
     */
	protected $mainRepository;

	public function getMainRepository()
	{
		return $this->mainRepository;
	}
}
