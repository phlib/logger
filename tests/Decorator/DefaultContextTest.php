<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\Decorator;

use Phlib\Logger\Decorator\DefaultContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DefaultContextTest extends TestCase
{
    public function testIsPsrLog(): void
    {
        $decorator = new DefaultContext($this->getMockLogger(), []);
        static::assertInstanceOf(LoggerInterface::class, $decorator);
    }

    #[DataProvider('providerAddDefaultContext')]
    public function testAddDefaultContext(array $defaultContext, array $logContext, array $expected): void
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

    public static function providerAddDefaultContext(): array
    {
        return [
            // Defaults, no log context
            [
                [
                    'hello' => 'world',
                    'foo' => 'bar',
                ],
                [],
                [
                    'hello' => 'world',
                    'foo' => 'bar',
                ],
            ],
            // Defaults, add other log context
            [
                [
                    'hello' => 'world',
                ],
                [
                    'foo' => 'bar',
                ],
                [
                    'hello' => 'world',
                    'foo' => 'bar',
                ],
            ],
            // Overwrite defaults with log context
            [
                [
                    'hello' => 'world',
                    'foo' => 'bar',
                ],
                [
                    'hello' => 'new world',
                    'test' => 'abc123',
                ],
                [
                    'hello' => 'new world',
                    'foo' => 'bar',
                    'test' => 'abc123',
                ],
            ],
        ];
    }

    private function getMockLogger(): LoggerInterface&MockObject
    {
        return $this->createMock(LoggerInterface::class);
    }
}
