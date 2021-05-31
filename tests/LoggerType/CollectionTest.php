<?php

declare(strict_types=1);

namespace Phlib\Logger\Test\LoggerType;

use Phlib\Logger\LoggerType\Collection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class CollectionTest extends TestCase
{
    public function testIsPsrLog()
    {
        $logger = new Collection();
        static::assertInstanceOf(\Psr\Log\LoggerInterface::class, $logger);
    }


    public function testLog()
    {
        $logger = new Collection();

        // Add a logger to the collection
        $subLogger1 = $this->getMockLoggerInterface();
        $subLogger1->expects(static::once())->method('log');
        $logger->add($subLogger1);

        // Add another logger to the collection
        // We will remove it before it gets called
        $subLogger2 = $this->getMockLoggerInterface();
        $subLogger2->expects(static::never())->method('log');
        $logger->add($subLogger2);

        // Add another logger to the collection
        $subLogger3 = $this->getMockLoggerInterface();
        $subLogger3->expects(static::once())->method('log');
        $logger->add($subLogger3);

        // Remove Logger 2 from the collection
        $logger->remove($subLogger2);

        // Perform the log
        $logger->log(LogLevel::ERROR, 'Test Log Message');
    }

    /**
     * @return LoggerInterface
     */
    protected function getMockLoggerInterface()
    {
        $loggerInterface = $this->createMock(\Psr\Log\LoggerInterface::class);
        return $loggerInterface;
    }
}
