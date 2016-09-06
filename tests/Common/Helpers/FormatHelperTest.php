<?php

namespace Bludata\Tests\Common\Helpers;

use DateTime;
use Bludata\Common\Helpers\FormatHelper;
use Bludata\Tests\TestCase;

class FormatHelperTest extends TestCase
{
    public function onlyNumbersProvider()
    {
        return [
            ['abc123', '123'],
            ['1a2b3c', '123'],
            ['388.788.163-02', '38878816302'],
            ['079.415.444-15', '07941544415'],
            ['71.231.263/0001-50', '71231263000150'],
            ['11.890.166/0001-47', '11890166000147'],
        ];
    }

    /**
     * @dataProvider onlyNumbersProvider
     */
    public function testOnlyNumbers($number, $result)
    {
        $this->assertEquals(FormatHelper::onlyNumbers($number), $result);
    }

    public function parseDateProvider()
    {
        return [
            ['2016-01-01','Y-m-d','d/m/Y', '01/01/2016'],
            ['01/01/2016','d/m/Y','Y-m-d', '2016-01-01'],
            ['2016-01-01','Y-m-d','obj', DateTime::createFromFormat('Y-m-d', '2016-01-01')],
            ['01/01/2016','d/m/Y','obj', DateTime::createFromFormat('d/m/Y', '01/01/2016')]
        ];
    }

    /**
     * @dataProvider parseDateProvider
     */
    public function testParseDate($date, $from, $to, $result)
    {
        $this->assertEquals(FormatHelper::parseDate($date, $from, $to), $result);
    }
}
