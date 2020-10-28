<?php

namespace App\Contracts;

interface LoggerInterface
{
    public function emergency(string $messge, array $context = []);
    public function alert(string $messge, array $context = []);
    public function critical(string $messge, array $context = []);
    public function error(string $messge, array $context = []);
    public function warning(string $messge, array $context = []);
    public function notice(string $messge, array $context = []);
    public function info(string $messge, array $context = []);
    public function debug(string $messge, array $context = []);
    public function log(string $level, string $messge, array $context = []);
}
