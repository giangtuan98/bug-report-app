<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\DatabaseConnectionException;
use mysqli;
use mysqli_driver;

class MySQLiConnection extends AbstractConnection implements DatabaseConnectionInterface
{
    const REQUIRED_CONNECTION_KEYS = [
        'driver',
        'host',
        'port',
        'db_name',
        'db_username',
        'db_user_password',
        'default_fetch',
    ];

    public function connect(): MySQLiConnection
    {
        $driver = new mysqli_driver;

        $driver->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;

        $credentials = $this->parseCredential($this->credential);
        try {
            $this->connection = new mysqli(...$credentials);
        } catch (\Throwable $th) {
            throw new DatabaseConnectionException(
                [],
                $th->getMessage()
            );
        }
        return $this;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    protected function parseCredential(array $credentials): array
    {
        return [
            $credentials['host'],
            $credentials['db_username'],
            $credentials['db_user_password'],
            $credentials['db_name'],
            $credentials['port'],
        ];
    }
}
