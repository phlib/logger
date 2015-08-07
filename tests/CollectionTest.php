<?php
namespace Phlib\Logger\Test;

use Phlib\Logger\Collection;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsPsrLog()
    {
        $logger = new Collection();
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', $logger);
    }


    public function testLog() {
        $logger = new Collection();

        // Add a logger to the collection
        $subLogger1 = $this->getMockLoggerInterface();
        $subLogger1->expects($this->once())->method('log');
        $logger->add($subLogger1);

        // Add another logger to the collection
        // We will remove it before it gets called
        $subLogger2 = $this->getMockLoggerInterface();
        $subLogger2->expects($this->never())->method('log');
        $logger->add($subLogger2);

        // Add another logger to the collection
        $subLogger3 = $this->getMockLoggerInterface();
        $subLogger3->expects($this->once())->method('log');
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
        $loggerInterface = $this->getMock('\Psr\Log\LoggerInterface');
        return $loggerInterface;
    }
}
