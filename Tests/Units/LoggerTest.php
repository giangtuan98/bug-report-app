<?php

namespace Tests\Units;

use App\Contracts\LoggerInterface;
use App\Exception\InvalidLogLevelArgument;
use App\Helpers\App;
use App\Loggers\Logger;
use App\Loggers\LogLevel;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * @var Logger
     */
    private $logger;

    public function setUp()
    {
        $this->logger = new Logger();

        parent::setUp();
    }

    public function testItImplementsTheLoggerInterface()
    {
        self::assertInstanceOf(LoggerInterface::class, new Logger());
    }

    // Test logger co tao duoc file khong
    // Test logger co ghi dung noi dung vao file da tao hay khong
    public function testItCanCreateDifferentTypesOfLogLevel()
    {
        $this->logger->info('Testing Info logs');
        $this->logger->error('Testing error logs');
        $this->logger->log(LogLevel::ALERT, 'Testing alert logs');

        $app = new App;

        $fileName = sprintf("%s/%s-%s.log", $app->getLogPath(), 'test', date("j.n.Y"));
        self::assertFileExists($fileName);

        $contentOfLogFile = file_get_contents($fileName);
        self::assertStringContainsString('Testing Info logs', $contentOfLogFile);
        self::assertStringContainsString('Testing error logs', $contentOfLogFile);
        self::assertStringContainsString('Testing alert logs', $contentOfLogFile);

        unlink($fileName);
        self::assertFileNotExists($fileName);
    }

    public function testItThrowsInvalidLogLevelArgumentExceptionWhenGivenAWrongLogLevel()
    {
        self::expectException(InvalidLogLevelArgument::class);

        $this->logger->log('invalid', 'Testing invalid log level');
    }
}
