<?php 

namespace Yosev\Login\Management\Helper;

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testLoggerSingleton()
    {
        $logger1 = Logger::getLogger(__CLASS__);
        $logger2 = Logger::getLogger(__CLASS__);

        self::assertSame($logger1, $logger2);
    }
}
