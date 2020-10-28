<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

// Sử dụng config helper để đọc nội dung từ file config/app.php
class App
{
    private $config = [];

    public function __construct()
    {
        $this->config = Config::get('app');
    }

    public function isDebugMode()
    {
        if (!isset($this->config['debug'])) {
            return false;
        }

        return $this->config['debug'];
    }

    public function getEnvironment(): string
    {
        if (!isset($this->config['env'])) {
            return 'production';
        }

        return $this->isTestMode() ? 'test' : $this->config['env'];
    }

    public function getLogPath(): string
    {
        if (!isset($this->config['log_path'])) {
            throw new \Exception('log path is not defined');
        }

        return $this->config['log_path'];
    }

    public function isRunningFromConsole(): bool
    {
        return php_sapi_name() == 'cli' || php_sapi_name() == 'phpbg';
    }

    public function getServerTime(): DateTimeInterface
    {
        return new DateTime('now', new DateTimeZone('Asia/Ho_chi_minh'));
    }

    public function isTestMode()
    {
        if ($this->isRunningFromConsole() && defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING == true) {
            return true;
        }

        return false;
    }
}
