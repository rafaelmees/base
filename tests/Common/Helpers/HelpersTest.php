<?php

namespace Bludata\Tests\Common\Helpers;

use Bludata\Tests\TestCase;

class HelpersTest extends TestCase
{
    public function drProvider()
    {
        $dumpArray = <<<EOF
(array) Array
(
    [foo] => bar
)

EOF;

        return [
        ['teste', '(string) teste'],
        [123, '(integer) 123'],
        [1.23, '(double) 1.23'],
        [false, '(boolean) false'],
        [true, '(boolean) true'],
        [['foo' => 'bar'], $dumpArray], ];
    }

    /**
     * @dataProvider drProvider
     */
    public function testDr($value, $expectedResult)
    {
        $this->assertEquals($expectedResult, dr($value));
    }

    public function envProvider()
    {
        return [
            ['foo', 'bar'],
            ['DB_DATABASE', 'mysql'],
            ['123', 321],
        ];
    }

    /**
     * @dataProvider envProvider
     */
    public function testEnv($key, $value)
    {
        putenv($key.'='.$value);
        $this->assertEquals($value, env($key));
    }

    public function testGetClassAnnotationsExists()
    {
        $this->assertTrue(
            function_exists('get_class_annotations'),
            'Função "get_class_annotations" não existe'
        );
    }

    public function testGetClassAnnotationsPassingObject()
    {
        $stub = new \ClassAnnotationStub;
        $annotations = get_class_annotations($stub);
        $this->assertNotEmpty($annotations);
    }

    public function testGetClassAnnotationsPassingString()
    {
        $annotations = get_class_annotations('ClassAnnotationStub');
        $this->assertNotEmpty($annotations);
    }

    public function testGetPropertyAnnotationsExists()
    {
        $this->assertTrue(
            function_exists('get_property_annotations'),
            'Função "get_property_annotations" não existe'
        );
    }

    public function testGetPropertyAnnotationsPassingObject()
    {
        $stub = new \PropertyAnnotationStub;
        $annotations = get_property_annotations($stub, 'property');
        $this->assertNotEmpty($annotations);
    }

    public function testGetPropertyAnnotationsPassingString()
    {
        $annotations = get_property_annotations('PropertyAnnotationStub', 'property');
        $this->assertNotEmpty($annotations);
    }

    public function testGetAllPropertyAnnotationsPassingObject()
    {
        $stub = new \PropertyAnnotationStub;
        $annotations = get_property_annotations($stub);
        $this->assertNotEmpty($annotations);
        $this->assertInternalType('array', $annotations);
        $this->assertContainsOnlyInstancesOf('Bludata\Common\Annotations\XML\Field', $annotations['property']);
    }

    public function testGetAllPropertyAnnotationsPassingString()
    {
        $annotations = get_property_annotations('PropertyAnnotationStub');
        $this->assertNotEmpty($annotations);
        $this->assertInternalType('array', $annotations);
        $this->assertContainsOnlyInstancesOf('Bludata\Common\Annotations\XML\Field', $annotations['property']);
    }
}
