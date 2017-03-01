<?php
namespace Bludata\Tests\Common\Annotations\XML;

use Bludata\Tests\TestCase;
use Doctrine\Common\Annotations\AnnotationReader;

class FieldTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (class_exists('Bludata\Tests\Common\Annotations\XML\FieldStub')) {
            return;
        }

        $fieldStubClass = <<<FieldStubCLASS
namespace Bludata\Tests\Common\Annotations\XML;

class FieldStub
{
    /**
     * @Bludata\Common\Annotations\XML\Field(name="foo")
     */
    public \$property1;
}
FieldStubCLASS;
        eval($fieldStubClass);
    }

    public function testIsInstanciable()
    {
        $fieldStub = new FieldStub;
        $this->assertInstanceOf('Bludata\Tests\Common\Annotations\XML\FieldStub', $fieldStub);
        return $fieldStub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testFieldStubHasProperty1Annotation($fieldStub)
    {
        $this->assertObjectHasAttribute('property1', $fieldStub);
        return $fieldStub;
    }

    /**
     * @depends testFieldStubHasProperty1Annotation
     */
    public function testProperty1HasAnAnnotation($fieldStub)
    {
        $reflectClass = new \ReflectionProperty(get_class($fieldStub), 'property1');
        $reader = new AnnotationReader();
        $annotations = $reader->getPropertyAnnotations($reflectClass);
        $this->assertGreaterThan(0, count($annotations));
        $this->assertContainsOnlyInstancesOf('Bludata\Common\Annotations\XML\Field', $annotations);
        $annotation = array_pop($annotations);
        $this->assertTrue(method_exists($annotation, 'getName'));
        $this->assertEquals('foo', $annotation->getName());
    }
}
