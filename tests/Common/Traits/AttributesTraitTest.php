<?php

namespace Bludata\Tests\Common\Traits;

use Bludata\Common\Traits\AttributesTrait;
use Bludata\Tests\TestCase;

class AttributesTraitTest extends TestCase
{
    public function testGetAttributes()
    {
        $mock = $this->mock(AttributesTrait::class);
        $attributes = $mock->getAttributes();
        $this->assertNotEmpty($attributes);
        $this->assertInternalType('array', $attributes);
        $this->assertArrayHasKey('attr1', $attributes);
        $this->assertEquals('Lorem Ipsum', $attributes['attr1']);
    }

    public function testGetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertTrue(method_exists($mock, 'getMethod'));
        $this->assertEquals('getAttr1', $mock->getMethod('attr1'));
    }

    public function testSetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertTrue(method_exists($mock, 'setMethod'));
        $this->assertEquals('setAttr1', $mock->setMethod('attr1'));
    }

    public function testGetWithGetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertEquals('Lorem Ipsum', $mock->get('attr1'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp #The class ".*" don't have a method "get.*"#
     */
    public function testGetWithoutGetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $mock->get('attr3');
    }

    public function testSetWithGetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $word = $this->faker()->word;
        $mock->set('attr1', $word);
        $this->assertEquals($word, $mock->getAttr1());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp #The class ".*" don't have a method "set.*"#
     */
    public function testSetWithoutGetMethod()
    {
        $mock = $this->mock(AttributesTrait::class);
        $mock->set('attr3', 'something');
    }

    public function testToArray()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertTrue(method_exists($mock, 'toArray'));
        $result = $mock->toArray();
        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('attr1', $result);
        $this->assertArrayHasKey('attr2', $result);
    }

    public function testToJson()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertTrue(method_exists($mock, 'toJson'));
        $result = $mock->toJson();
        $this->assertJson($result);
        $objectResult = json_decode($result);
        $this->assertObjectHasAttribute('attr1', $objectResult);
        $this->assertObjectHasAttribute('attr2', $objectResult);
        $arrayResult = json_decode($result, true);
        $this->assertArrayHasKey('attr1', $arrayResult);
        $this->assertArrayHasKey('attr2', $arrayResult);
    }

    public function testToString()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertNotEmpty($mock->toString());
    }

    public function testCastToString()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertEquals((string) $mock, $mock->toString());
    }

    public function testGet()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertEquals($mock->getAttr1(), $mock->attr1);
    }

    public function testSet()
    {
        $mock = $this->mock(AttributesTrait::class);
        $word = $this->faker()->word;
        $mock->attr1 = $word;
        $this->assertEquals($word, $mock->attr1);
        $this->assertEquals($word, $mock->getAttr1());
    }

    public function testIsset()
    {
        $mock = $this->mock(AttributesTrait::class);
        $this->assertTrue(isset($mock->attr1));
    }

    public function testUnset()
    {
        $mock = $this->mock(AttributesTrait::class);
        unset($mock->attr1);
        $this->assertFalse(isset($mock->attr1));
    }
}
