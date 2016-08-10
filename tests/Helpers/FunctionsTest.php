<?php

namespace Bludata\Tests\Helpers;

use Bludata\Tests\TestCase;

class FunctionsTest extends TestCase
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
}
