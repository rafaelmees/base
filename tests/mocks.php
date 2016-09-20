<?php

use Bludata\Common\Traits\AttributesTrait;
use Bludata\Tests\Doctrine\ODM\MongoDB\Entities\Stubs\EntityStub;
use Bludata\Tests\Lumen\Traits\LogTraitStub;

$mockContainer =  new Jaschweder\Mock\Container\ArrayContainer;

$factory = new Jaschweder\Mock\Factory($mockContainer);

$factory->register(EntityStub::class, function () {
    $entity = new EntityStub;
    $entity->setAttr1(faker()->word);
    $entity->setAttr2(faker()->randomNumber);
    return $entity;
});

$factory->register(AttributesTrait::class, function () {
    return new Bludata\Tests\Common\Traits\Stubs\AttributesTraitStub;
});

$factory->register(LogTraitStub::class, function () {
    return new LogTraitStub;
});

return $factory;
