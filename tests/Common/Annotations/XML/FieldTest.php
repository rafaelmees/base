<?php
namespace Bludata\Tests\Common\Annotations\XML;

use Bludata\Tests\TestCase;
use Doctrine\Common\Annotations\AnnotationReader;

class FieldTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->registerFieldStub();
    }

    public function registerFieldStub()
    {
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
        return array_values($annotations);
    }

    /**
     * @depends testProperty1HasAnAnnotation
     */
    public function testProperty1CanBeConvertedToString($annotations)
    {
        foreach($annotations as $annotation) {
            $this->assertNotEmpty((string) $annotation);
            $this->assertNotEmpty($annotation->toString());
            $this->assertEquals('<foo>property1</foo>', $annotation->toString('property1'));
        }
    }
}
