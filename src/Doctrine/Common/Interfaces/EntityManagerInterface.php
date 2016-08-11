<?php

namespace Bludata\Doctrine\Common\Interfaces;

interface EntityManagerInterface
{
    public function getRepository($class);
}
