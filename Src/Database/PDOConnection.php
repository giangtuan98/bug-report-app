<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exception\DatabaseConnectionException;
use PDO;
use PDOException;

class PDOConnection extends AbstractConnection implements DatabaseConnectionInterface
{
    public function connect()
    {
        $credentials = $this->parseCredential($this->credential);

        try {
            $this->connection = new PDO(...$credentials);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->credential['default_fetch']);
        } catch (PDOException $exception) {
            throw new DatabaseConnectionException([], $exception->getMessage());
        }
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function parseCredential(array $credentials): array
    {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;',
            $credentials['driver'],
            $credentials['host'],
            $credentials['port'],
            $credentials['db_name'],
        );

        return [$dsn, $credentials['db_username'], $credentials['db_user_password']];
    }
}
