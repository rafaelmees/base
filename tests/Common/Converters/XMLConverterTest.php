<?php

namespace Bludata\Tests\Common\Converters;

use Bludata\Common\Converters\XMLConverter;
use Bludata\Tests\TestCase;

class XMLConverterTest extends TestCase
{
    protected $converter;

    public function setUp()
    {
        $this->converter = new XMLConverter;
    }

    public function testConvertToStringOneObjectWithClassAnnotation()
    {
        $stub = new \ClassAnnotationStub;
        $this->assertEquals('<foo></foo>', $this->converter->toString($stub));
    }

    public function testConvertToStringOneStringWithClassAnnotation()
    {
        $this->assertEquals('<foo></foo>', $this->converter->toString('ClassAnnotationStub'));
    }

    public function testConvertToStringOneObjectWithPropertyAnnotation()
    {
        $stub = new \PropertyAnnotationStub;
        $this->assertEquals('<foo><bar></bar></foo>', $this->converter->toString($stub));
    }

    public function testConvertToStringOneStringWithPropertyAnnotation()
    {
        $this->assertEquals('<foo><bar></bar></foo>', $this->converter->toString('PropertyAnnotationStub'));
    }

    public function testConvertToStringOneObjectWithPropertyAnnotationAndValue()
    {
        $stub = new \PropertyAnnotationStub;
        $stub->property = 'Lorem Ipsum';
        $this->assertEquals('<foo><bar>Lorem Ipsum</bar></foo>', $this->converter->toString($stub));
    }

    public function testConvertToStringOneObjectWithCollectionAnnotationAndValue()
    {
        $property1 = new \PropertyAnnotationStub;
        $property1->property = 'Property1';

        $property2 = new \PropertyAnnotationStub;
        $property2->property = 'Property2';

        $stub = new \CollectionAnnotationStub;
        $stub->property = 'Lorem Ipsum2';
        $stub->collection = [$property1, $property2];

        $this->assertEquals('<foo><bar>Lorem Ipsum2</bar><collection><foo><bar>Property1</bar></foo><foo><bar>Property2</bar></foo></collection></foo>', $this->converter->toString($stub));
    }
}
