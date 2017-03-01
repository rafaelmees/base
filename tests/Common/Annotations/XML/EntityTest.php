<?php
namespace Bludata\Tests\Common\Annotations\XML;

use Bludata\Tests\TestCase;
use Doctrine\Common\Annotations\AnnotationReader;

class EntityTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (class_exists('Bludata\Tests\Common\Annotations\XML\EntityStub')) {
            return;
        }

        $entityStubClass = <<<EntityStubCLASS
namespace Bludata\Tests\Common\Annotations\XML;

/**
 * @Bludata\Common\Annotations\XML\Entity(name="foo")
 */
class EntityStub
{
}
EntityStubCLASS;
        eval($entityStubClass);
    }

    public function testIsInstanciable()
    {
        $entityStub = new EntityStub;
        $this->assertInstanceOf('Bludata\Tests\Common\Annotations\XML\EntityStub', $entityStub);
        return $entityStub;
    }

    /**
     * @depends testIsInstanciable
     */
    public function testClass1HasAnAnnotation($entityStub)
    {
        $reflectClass = new \ReflectionClass(get_class($entityStub));
        $reader = new AnnotationReader();
        $annotations = $reader->getClassAnnotations($reflectClass);
        $this->assertGreaterThan(0, count($annotations));
        $this->assertContainsOnlyInstancesOf('Bludata\Common\Annotations\XML\Entity', $annotations);
        $annotation = array_pop($annotations);
        $this->assertTrue(method_exists($annotation, 'getName'));
        $this->assertEquals('foo', $annotation->getName());
    }
}
