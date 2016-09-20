<?php

namespace Bludata\Tests\Lumen\Traits;

use Bludata\Tests\TestCase;

class LogTraitTest extends TestCase
{

    protected $logger;

    public function setUp()
    {
        parent::setUp();

        $loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)->getMock();

        $this->app()->instance(\Psr\Log\LoggerInterface::class, $loggerMock);

        $this->logger = $loggerMock;
    }

    public function messageProvider()
    {
        return [
            ['string message'],
            [123],
            [123.45],
            [true],
            [false],
            [['an', 'array']],
            [(new \stdClass)]
        ];
    }

    public function testIsUsable()
    {
        $stub = new LogTraitStub;
        $this->assertInstanceOf(LogTraitStub::class, $stub);
        return $stub;
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testEmergency($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->emergency($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testAlert($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->alert($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testCritical($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->critical($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testError($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->error($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testWarning($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->warning($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testNotice($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->notice($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testInfo($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->info($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testDebug($message, $stub)
    {
        $this->logger->expects($this->once())->method('log');
        $stub->debug($message);
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testToLogMessage($message, $stub)
    {
        $this->assertInternalType('string', $stub->toLogMessage($message));
    }

    /**
     * @depends testIsUsable
     * @dataProvider messageProvider
     */
    public function testLog($message, $stub)
    {
        $errorLevel = \Psr\Log\LogLevel::ERROR;
        $this->logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo($errorLevel));
        $stub->log($errorLevel, $message);
    }
}
