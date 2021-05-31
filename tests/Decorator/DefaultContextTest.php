<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\Decorator;

use Phlib\Logger\Decorator\DefaultContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DefaultContextTest extends TestCase
{
    public function testIsPsrLog()
    {
        $decorator = new DefaultContext($this->getMockLogger(), []);
        static::assertInstanceOf(\Psr\Log\LoggerInterface::class, $decorator);
    }

    /**
     * @dataProvider providerAddDefaultContext
     */
    public function testAddDefaultContext($defaultContext, $logContext, $expected)
    {
        $loggerInterface = $this->getMockLogger();

        $loggerInterface->expects(static::once())
            ->method('log')
            ->with(
                static::anything(),
                static::anything(),
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
     * @return LoggerInterface|MockObject
     */
    protected function getMockLogger()
    {
        $loggerInterface = $this->createMock(\Psr\Log\LoggerInterface::class);
        return $loggerInterface;
    }
}
