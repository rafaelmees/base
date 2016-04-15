<?php

namespace Bludata\Tests\Services;

use Bludata\Tests\BaseTest;

abstract class BaseServiceTest extends BaseTest
{
    abstract public function getService();

    abstract public function getRepositoryTest();

	public function getEntityName()
	{
		return $this->getService()->getMainRepository()->getEntityName();
	}
}
