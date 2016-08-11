<?php

use Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs\EntityStub;

$mockContainer =  new Jaschweder\Mock\Container\ArrayContainer;

$factory = new Jaschweder\Mock\Factory($mockContainer);

$factory->register(EntityStub::class, function () {
    $entity = new EntityStub;
    $entity->setAttr1(faker()->word);
    $entity->setAttr2(faker()->randomNumber);
    return $entity;
});

return $factory;
