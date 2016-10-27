<?php

namespace Phlib\Logger\Test\Decorator;

use Phlib\Logger\Decorator\DefaultContext;
use Psr\Log\LoggerInterface;

class DefaultContextTest extends \PHPUnit_Framework_TestCase
{
    public function testIsPsrLog()
    {
        $decorator = new DefaultContext($this->getMockLogger(), []);
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $decorator);
    }

    public function testAddDecorations()
    {
        $loggerInterface = $this->getMockLogger();

        $loggerInterface->expects($this->once())
            ->method('log')
            ->with(
                $this->anything(),
                $this->anything(),
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ]
            );

        $decorator = new DefaultContext($loggerInterface, [
            'hello' => 'world',
            'foo' => 'bar'
        ]);
        $decorator->info('message', []);
    }

    public function testAddContext()
    {
        $loggerInterface = $this->getMockLogger();

        $loggerInterface->expects($this->once())
            ->method('log')
            ->with(
                $this->anything(),
                $this->anything(),
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ]
            );

        $decorator = new DefaultContext($loggerInterface, [
            'hello' => 'world'
        ]);
        $decorator->info('message', [
            'foo' => 'bar'
        ]);
    }

    public function testOverrideDecorations()
    {
        $loggerInterface = $this->getMockLogger();

        $loggerInterface->expects($this->once())
            ->method('log')
            ->with(
                $this->anything(),
                $this->anything(),
                [
                    'hello' => 'new world',
                    'foo' => 'bob',
                    'test' => 'abc123'
                ]
            );

        $decorator = new DefaultContext($loggerInterface, [
            'hello' => 'world',
            'foo' => 'bar'
        ]);
        $decorator->info('message', [
            'hello' => 'new world',
            'foo' => 'bob',
            'test' => 'abc123'
        ]);
    }

    /**
     * @return LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockLogger()
    {
        $loggerInterface = $this->getMock('\Psr\Log\LoggerInterface');
        return $loggerInterface;
    }
}
