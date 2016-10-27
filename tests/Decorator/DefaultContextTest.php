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

    /**
     * @dataProvider providerAddDefaultContext
     */
    public function testAddDefaultContext($defaultContext, $logContext, $expected)
    {
        $loggerInterface = $this->getMockLogger();

        $loggerInterface->expects($this->once())
            ->method('log')
            ->with(
                $this->anything(),
                $this->anything(),
                $expected
            );

        $decorator = new DefaultContext($loggerInterface, $defaultContext);
        $decorator->info('message', $logContext);
    }

    public function providerAddDefaultContext()
    {
        return [
            // Defaults, no log context
            [
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ],
                [],
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ]
            ],
            // Defaults, add other log context
            [
                [
                    'hello' => 'world'
                ],
                [
                    'foo' => 'bar'
                ],
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ]
            ],
            // Overwrite defaults with log context
            [
                [
                    'hello' => 'world',
                    'foo' => 'bar'
                ],
                [
                    'hello' => 'new world',
                    'test' => 'abc123'
                ],
                [
                    'hello' => 'new world',
                    'foo' => 'bar',
                    'test' => 'abc123'
                ]
            ]
        ];
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
