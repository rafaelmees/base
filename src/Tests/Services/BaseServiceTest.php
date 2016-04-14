<?php

namespace Bludata\Tests\Services;

use Bludata\Tests\BaseTest;

abstract class BaseServiceTest extends BaseTest
{
    abstract public function getServiceName();

    abstract public function getMockArray();

	abstract public function getFlushedMockArray();

	public function getService()
	{
		return app($this->getServiceName());
	}

	public function getRepository()
	{
		return $this->getService()->getMainRepository();
	}

	public function getEntityName()
	{
		return $this->getRepository()->getEntityName();
	}
}
